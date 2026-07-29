<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Allocate New Job</title>
    
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
        .card-box { border-radius: 8px; border: none; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        label { font-weight: 500; color: #495057; }
        .required-star { color: red; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container-fluid">
            
            <div class="row mt-4 mb-3">
                <div class="col-12 text-center">
                    <h4 class="page-title">Create New Job Allocation</h4>
                    <p class="text-muted">Assign a Design File (DF) to a Machine</p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <form id="allocation_form" autocomplete="off">
                        
                        <div class="card-box mb-4">
                            <h5 class="header-title mb-3 text-primary">1. Select Machine Location</h5>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Production Line <span class="required-star">*</span></label>
                                    <select name="line_id" id="line_id" class="form-control select2" required onchange="get_machines(this.value)">
                                        <option value="">Select Line</option>
                                        <?php foreach($lines as $line): ?>
                                            <option value="<?php echo $line->line_id; ?>"><?php echo $line->line_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Machine <span class="required-star">*</span></label>
                                    <select name="machine_id" id="machine_id" class="form-control select2" required disabled>
                                        <option value="">Select Line First</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card-box mb-4">
                            <h5 class="header-title mb-3 text-primary">2. Job Details</h5>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Select Design File (DF) <span class="required-star">*</span></label>
                                    <select name="df_no" id="df_no" class="form-control select2" required>
                                        <option value="">Search by DF No or Description...</option>
                                        <?php if(!empty($dfs)): ?>
                                            <?php foreach($dfs as $row): ?>
                                                <option value="<?php echo $row->df_no; ?>" data-date="<?php echo date('Y-m-d', strtotime($row->added_on)); ?>">
                                                    <?php echo $row->df_no . ' - ' . $row->df_description; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No Open DFs Available</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 form-group">
                                    <label>Release Date (From DF) <span class="required-star">*</span></label>
                                    <input type="date" name="release_date" id="release_date" class="form-control" readonly required style="background-color: #e9ecef;">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label>Assign Supervisor <span class="required-star">*</span></label>
                                    <select name="supervisor_id" class="form-control select2" required>
                                        <option value="">Select Supervisor</option>
                                        <?php foreach($supervisors as $sup): ?>
                                            <option value="<?php echo $sup->supervisor_id; ?>">
                                                <?php echo $sup->supervisor_name . ' (' . ($sup->department ?? 'General') . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label>Remarks / Instructions</label>
                                    <textarea name="remarks" class="form-control" rows="2" placeholder="Any specific instructions..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-center mb-5">
                                <a href="<?php echo page_url.'Masters/manage_jobs'; ?>" class="btn btn-secondary btn-lg mr-2">Cancel</a>
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fa fa-check-circle"></i> Save Allocation
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

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
        $('.select2').select2({ placeholder: 'Select Option', allowClear: true });

        // Auto-fill Release Date
        $('#df_no').on('change', function() {
            var selectedDate = $(this).find(':selected').data('date');
            if(selectedDate) {
                $('#release_date').val(selectedDate);
            } else {
                $('#release_date').val('');
            }
        });

        $('#allocation_form').validate({
            errorClass: 'text-danger', // Make errors visible red
            submitHandler: function(form) {
                
                // 1. Show Loading
                try {
                    $.blockUI({ message: '<h3><i class="fa fa-spinner fa-spin"></i> Saving...</h3>' });
                } catch(e) { console.log('BlockUI not loaded'); }

                $.ajax({
                    url: "<?php echo page_url.'Masters/save_allocation'; ?>",
                    type: "POST",
                    data: $(form).serialize(),
                    dataType: "json",
                    success: function(resp) {
                        // 2. Hide Loading
                        try { $.unblockUI(); } catch(e) {}

                        console.log("Server Response:", resp); // Check Console (F12) if this fails

                        if(resp.status == 1) {
                            // 3. Show Success
                            try {
                                $.gritter.add({ 
                                    title:'Success', 
                                    text: 'Job allocated successfully! Redirecting...', 
                                    class_name:'gritter-success',
                                    time: 2000
                                });
                            } catch(e) {
                                alert("Success! Job Allocated."); // Fallback if Gritter fails
                            }

                            // 4. Redirect
                            setTimeout(function(){ 
                                window.location.href = resp.redirect_url; 
                            }, 1000);
                        } else {
                            // Show Error
                            try {
                                $.gritter.add({ title:'Error', text:resp.message, class_name:'gritter-danger' });
                            } catch(e) {
                                alert("Error: " + resp.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        try { $.unblockUI(); } catch(e) {}
                        console.error(xhr.responseText); // This will show PHP errors in Console
                        alert('System Error: Please check the browser console (F12) for details.');
                    }
                });
            }
        });
    });

    function get_machines(line_id) {
        if(!line_id) {
            $('#machine_id').html('<option value="">Select Line First</option>').prop('disabled', true);
            return;
        }
        $('#machine_id').html('<option>Loading...</option>').prop('disabled', true);
        $.ajax({
            url: "<?php echo page_url.'Masters/get_machines_by_line'; ?>",
            type: "POST",
            data: {line_id: line_id},
            dataType: "json",
            success: function(data) {
                var options = '<option value="">Select Machine</option>';
                if(data.length > 0) {
                    $.each(data, function(index, val) {
                        options += '<option value="'+val.machine_id+'">'+val.machine_name+'</option>';
                    });
                    $('#machine_id').html(options).prop('disabled', false);
                } else {
                    $('#machine_id').html('<option value="">No Machines in this Line</option>').prop('disabled', true);
                }
            }
        });
    }
    </script>
</body>
</html>