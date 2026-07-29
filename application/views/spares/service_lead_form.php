<?php
defined('BASEPATH') or exit('No direct script access allowed');
// Fetch company info for dynamic branding
$company_info = $this->db->select('company_name, logo, colorcode')->from('company_information')->get()->row();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; Add Service Opportunity</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>plugins/jquery.gritter/css/jquery.gritter.css" rel="stylesheet" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: none; margin-bottom: 25px; padding: 20px; }
        .page-title { font-size: 24px; font-weight: 600; color: <?php echo $company_info->colorcode ?? '#333'; ?>; }
        .card-header-title { display: flex; justify-content: space-between; align-items: center; font-size: 18px; font-weight: 600; color: #495057; border-bottom: 1px solid #e9ecef; padding-bottom: 10px; margin-bottom: 20px; }
        .required-star { color: #e74c3c; margin-left: 3px; }
        label { font-weight: 500; color: #333; }
        .error { color: #e74c3c; font-size: 0.875em; margin-top: 5px; font-weight: normal; }
        input.error, select.error, textarea.error { border: 1px solid #e74c3c !important; background-color: #fff8f8; }
        .select2-container--default .select2-selection--single.error { border: 1px solid #e74c3c !important; }
        .btn-success { background-color: <?php echo $company_info->colorcode ?? '#28a745'; ?>; border-color: <?php echo $company_info->colorcode ?? '#28a745'; ?>; transition: all 0.3s ease; }
        .btn-success:hover { opacity: 0.9; }
    </style>
</head>

<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box text-center">
                        <h4 class="page-title">Create Service Opportunity</h4>
                    </div>
                </div>
            </div>

            <form id="service_opportunity_form" action="<?php echo page_url.'ServiceLeads/save_opportunity'; ?>" method="post" autocomplete="off">
                <input type="hidden" name="customer_origin" id="customer_origin" value="">
                <div class="card-box">
                    <h5 class="card-header-title"><span>Service Lead Details</span></h5>

                    <div class="row">
                    <div class="col-md-12">
                    <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> <?php echo $this->session->flashdata('success'); ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php endif; ?>

                    <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?php echo $this->session->flashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php endif; ?>
                    </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Opportunity Date<span class="required-star">*</span></label>
                            <input type="date" name="op_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Lead Source<span class="required-star">*</span></label>
                            <select name="lsource" class="form-control" required>
                                <option value="">Select Source</option>
                                <?php 
                                $sources = $this->db->get_where('lead_source', ['status'=>1])->result(); 
                                foreach($sources as $s) echo "<option value='{$s->source_id}'>".ucwords(strtolower($s->lead_source))."</option>"; 
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Type<span class="required-star">*</span></label>
                            <select name="op_type" id="op_type" class="form-control" required onchange="get_service_opp_no();">
                                <option value="1">Domestic Service</option>
                                <option value="2">International Service</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Opportunity Number</label>
                            <input type="text" name="op_no" id="op_no" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Marketing Person<span class="required-star">*</span></label>
                            <select name="marketing" required class="form-control">
                                <option value="">Select Person</option>
                                <?php 
                                $users = $this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status', 1)->where('department_id',22)->order_by('first_name','ASC')->get()->result(); 
                                foreach($users as $u) echo "<option value='{$u->user_id}'>".ucwords(strtolower($u->first_name.' '.$u->last_name))."</option>"; 
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Subject / Requirement<span class="required-star">*</span></label>
                            <textarea class="form-control" name="remarks" rows="2" required placeholder="Describe the service requirement..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <h5 class="card-header-title">
                        <span>Customer Information</span>
                        <a href="javascript:;" data-toggle="modal" data-target="#add-customer-modal" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Add New Customer</a>
                    </h5>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Search Customer<span class="required-star">*</span></label>
                            <select class="form-control select-customer" name="customer" id="customer" required></select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Country<span class="required-star">*</span></label>
                            <select class="form-control" name="country" id="country" required style="width:100%;">
                                <option value="">Select Country</option>
                                <?php 
                                $countries = $this->db->get_where('countries', ['country_status'=>1])->result(); 
                                foreach($countries as $c) echo "<option value='{$c->country_id}'>{$c->country_name}</option>"; 
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Contact Number<span class="required-star">*</span></label>
                            <input type="text" name="customercontactno" id="customercontactno" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Email ID<span class="required-star">*</span></label>
                            <input type="email" name="customeremailid" id="customeremailid" class="form-control" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>GST No.<span class="required-star">*</span></label>
                            <input type="text" name="gst" id="gst" class="form-control">
                        </div>
                        <div class="col-md-5 form-group">
                            <label>Service Address<span class="required-star">*</span></label>
                            <textarea name="address" id="address" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center mb-4">
                        <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-check"></i> Submit Opportunity</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="add-customer-modal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="add_customer_form">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Company</h4>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Company Name<span class="required-star">*</span></label><input type="text" class="form-control" id="new_companyname" name="new_companyname" required></div>
                            <div class="col-md-6 form-group"><label>Country<span class="required-star">*</span></label><select class="form-control" id="new_country" name="new_country" style="width:100%" required><option value="">Select...</option><?php foreach($countries as $c) echo "<option value='{$c->country_id}'>{$c->country_name}</option>"; ?></select></div>
                            <div class="col-md-6 form-group"><label>Email<span class="required-star">*</span></label><input type="email" class="form-control" name="new_email" required></div>
                            <div class="col-md-6 form-group"><label>Contact Name<span class="required-star">*</span></label><input type="text" class="form-control" name="contactpersonname" required></div>
                            <div class="col-md-6 form-group"><label>Contact No<span class="required-star">*</span></label><input type="text" class="form-control" name="personcontactno" required></div>
                             <div class="col-md-4 form-group"><label>GST No<span class="required-star"></span></label><input type="text" class="form-control" name="gstno" ></div>
                            <div class="col-md-8 form-group"><label>Address<span class="required-star">*</span></label><textarea name="new_address" class="form-control" rows="2" required></textarea></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="save_customer_button" class="btn btn-info">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js"></script>

    <script>
    $(document).ready(function() {
        const validationConfig = {
            errorElement: 'div',
            errorPlacement: (error, el) => { 
                error.addClass('error'); 
                error.insertAfter(el.hasClass('select2-hidden-accessible') ? el.next('.select2-container') : el); 
            },
            highlight: (el) => { 
                $(el).addClass('error'); 
                $(el).closest('.form-group').find('.select2-selection').addClass('error'); 
            },
            unhighlight: (el) => { 
                $(el).removeClass('error'); 
                $(el).closest('.form-group').find('.select2-selection').removeClass('error'); 
            }
        };

        $('#service_opportunity_form').validate(validationConfig);
        $('#add_customer_form').validate(validationConfig);

        $('.select-customer').select2({
            placeholder: 'Search Company Name or Address...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: "<?php echo page_url;?>ServiceLeads/get_customer_by_company",
                dataType: 'json',
                delay: 250,
                data: (params) => ({ searchTerm: params.term }),
                processResults: (data) => ({ results: data })
            },
            templateResult: function(data) {
                if (!data.id) return data.text;
                var parts = data.text.split(' | ');
                return $('<div><b>'+parts[0]+'</b><br><small style="color:#777;">'+(parts[1] || '')+'</small></div>');
            },
            templateSelection: function(data) {
                if (!data.id) return data.text;
                return data.text.split(' | ')[0];
            }
        }).on('select2:select', function (e) {
            $('#customer_origin').val(e.params.data.origin || '');
            fetchDetails(e.params.data.id, e.params.data.origin || '');
        });

        $('#country, #new_country').select2({ placeholder: 'Select Country' });
        get_service_opp_no();

        $('#save_customer_button').on('click', function() {
            if ($('#add_customer_form').valid()) {
                let btn = $(this); btn.text('Saving...').prop('disabled', true);
                $.post("<?php echo page_url;?>ServiceLeads/add_new_ajax_customer", $('#add_customer_form').serialize(), function(res) {
                    let data = res.split('~');
                    if (data[0] == 1) {
                        let newOpt = new Option($('#new_companyname').val(), data[1], true, true);
                        $('#customer').append(newOpt).trigger('change');
                        $('#customer_origin').val('spare');
                        $('#add-customer-modal').modal('hide');
                        fetchDetails(data[1], 'spare');
                    } else { alert(data[1]); }
                }).always(() => btn.text('Save Customer').prop('disabled', false));
            }
        });
    });

    function fetchDetails(id, origin) {
        if(!id) return;
        $.post("<?php echo page_url;?>ServiceLeads/getCustomerDetails_frommaster", { custid: id, origin: origin || $('#customer_origin').val() }, function(data) {
            if(data) {
                var arr = data.split('|');
                $("#address").val(arr[0]);
                $("#customercontactno").val(arr[1]);
                $("#customeremailid").val(arr[2]);
                $("#gst").val(arr[4]);
                if(arr[3]) {
                    $('#country').val(arr[3]).trigger('change');
                }
            }
        });
    }

   function get_service_opp_no() {
    var op_type = $("#op_type").val();
    $.post("<?php echo page_url;?>ServiceLeads/generateOppNo", { op_type: op_type,flag:1 }, function(data) {
        // 'data' now contains the full string like "SPM/DOM/0005/26-27" 
        // or "SPM/EXP/0005/26-27" from the controller
        $("#op_no").val(data);
    });
}
    </script>
</body>
</html>
