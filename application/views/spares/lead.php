<?php
defined('BASEPATH') or exit('No direct script access allowed');
// Fetch company info once
$company_info = $this->db->select('company_name, logo, colorcode')->from('company_information')->get()->row();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A modern and attractive web form.">
    <meta name="author" content="<?php echo htmlspecialchars($company_info->company_name ?? 'Company'); ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> &mdash; Add New Opportunity</title>

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
        .select2-container .select2-selection--single { height: 40px !important; }
        .select2-container--default .select2-selection--single { border: 1px solid #ced4da !important; border-radius: 5px !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
        .required-star { color: #e74c3c; margin-left: 3px; }
        label { font-weight: 500; }
        .error { color: #e74c3c; font-size: 0.875em; margin-top: 5px; }
        input.error, select.error, textarea.error { border-color: #e74c3c !important; }
        .select2-container--default .select2-selection--single.error { border: 1px solid #e74c3c !important; }
        .product-row { display: flex; align-items: flex-end; margin-bottom: 15px; gap: 15px; }
        .product-row .form-group { flex: 1; margin-bottom: 0; }
    </style>
</head>

<body>
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row"><div class="col-12"><div class="page-title-box text-center"><h4 class="page-title">Create New Opportunity</h4></div></div>
        <?php echo $this->session->flashdata('message'); ?>
    </div>

            <form name="frm" id="opportunity_form" action="<?php echo page_url.'Spares/add_opportunity'; ?>" method="post" autocomplete="off">
                <div class="card-box">
                    <h5 class="card-header-title"><span>Opportunity Details</span></h5>
                    <div class="row">
                        <div class="col-md-4 form-group"><label>Opportunity Date<span class="required-star">*</span></label><input type="date" name="op_date" class="form-control" required value="<?php echo set_value('op_date', date('Y-m-d')); ?>"></div>
                        <div class="col-md-4 form-group"><label>Source<span class="required-star">*</span></label><select name="lsource" id="lsource" class="form-control" required onchange="checkifexhibition();"><option value="">Select Source</option><?php $sources = $this->db->select('source_id, lead_source')->from('lead_source')->where('status', 1)->get()->result(); foreach ($sources as $source) { echo '<option value="' . $source->source_id . '" ' . set_select('lsource', $source->source_id) . '>' . htmlspecialchars(ucwords(strtolower($source->lead_source))) . '</option>'; } ?></select></div>
                        <div class="col-md-4 form-group" style="display:none;" id="exhibitiondiv"><label>Select Exhibition</label><select class="form-control" name="exhibitionname" id="exhibitionname"><?php $exhibitions = $this->db->select('id, exhibition')->from('exhibition_info')->order_by('exhibition', 'asc')->get()->result(); foreach ($exhibitions as $exhibition) { echo '<option value="' . $exhibition->id . '">' . htmlspecialchars(ucwords(strtolower($exhibition->exhibition))) . '</option>'; } ?></select></div>
                        <div class="col-md-4 form-group"><label>Opportunity Type<span class="required-star">*</span></label><select name="op_type" id="op_type" class="form-control" required onchange="get_opp_no();"><option value="1" <?php echo set_select('op_type', '1', TRUE); ?>>Domestic</option><option value="2" <?php echo set_select('op_type', '2'); ?>>Export</option></select></div>
                        <div class="col-md-4 form-group"><label>Opportunity Number<span class="required-star">*</span></label><input type="text" name="op_no" id="op_no" class="form-control" readonly><input type="hidden" name="op_increment_no" id="op_increment_no"></div>
                        <div class="col-md-4 form-group"><label>Marketing Person<span class="required-star">*</span></label><select name="marketing" required id="marketing" class="form-control"><option value="">Select Person</option><?php 
                        $department = array('31','22');
                        $this->db->select('user_id,first_name,last_name')->from('system_users')->where_in('department_id',$department,false)->where('business_location', 2)->where('user_status', 1); if ($this->session->userdata['logged_in']['role'] != 12 && $this->session->userdata['logged_in']['role'] != 64) { $this->db->where('user_id', $this->session->userdata['logged_in']['user_id']); } $users = $this->db->order_by('first_name')->get()->result(); foreach ($users as $user) { echo '<option value="' . $user->user_id . '" ' . set_select('marketing', $user->user_id) . '>' . htmlspecialchars(ucwords(strtolower($user->first_name . ' ' . $user->last_name))) . '</option>'; } ?></select></div>
                        <div class="col-md-12 form-group"><label>Remarks</label><textarea class="form-control" name="remarks" id="remarks" rows="3"><?php echo set_value('remarks'); ?></textarea></div>
                    </div>
                </div>

                <div class="card-box">
                    <h5 class="card-header-title"><span>Customer Details</span><a href='javascript:;' data-toggle="modal" data-target="#add-customer-modal" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Add New</a></h5>
                    <div class="row">
                         <div class="col-md-4 form-group"><label>Customer<span class="required-star">*</span></label>
                             <select class="form-control select-customer" name="customer" id="customer" onchange="getDetails();" required></select>
                         </div>
                         <div class="col-md-4 form-group"><label>Brand</label><select class="form-control select-brand" name="brand" id="brand"></select></div>
                         <div class="col-md-4 form-group"><label>Country<span class="required-star">*</span></label><select class="form-control" name="country" id="country" onchange="getcountrytax();" required style="width: 100%;"><option value="">Select Country</option><?php $countries = $this->db->select('country_name, country_id')->from('countries')->where('country_status',1)->order_by('country_name', 'asc')->get()->result(); foreach($countries as $country) { echo '<option value="'.$country->country_id.'">'.htmlspecialchars($country->country_name).'</option>'; } ?></select></div>
                         <div class="col-md-8 form-group"><label>Customer Address<span class="required-star">*</span></label><textarea name="address" id="address" class="form-control" rows="3" required><?php echo set_value('address'); ?></textarea></div>
                         <div class="col-md-4"><div class="form-group"><label>Contact Number<span class="required-star">*</span></label><input type="text" name="customercontactno" id="customercontactno" class="form-control" required value="<?php echo set_value('customercontactno'); ?>"></div><div class="form-group"><label>Email ID<span class="required-star">*</span></label><input type="email" name="customeremailid" id="customeremailid" class="form-control" required value="<?php echo set_value('customeremailid'); ?>"></div></div>
                         <div class="col-md-4 form-group" id="showhidetaxtype" style="display:none;"><label id="taxt"></label><input type="text" name="gst" id="gst" class="form-control" value="<?php echo set_value('gst'); ?>"></div>
                    </div>
                </div>

                <div class="card-box">
                    <h5 class="card-header-title"><span>Product Details</span>
                        <span>Item Not existing in this list? <a href="<?php echo page_url;?>spares_master" target="_blank" class="btn btn-primary btn-xs">Click here to add</a></span>
                        <button type="button" id="add_product_row" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> Add Item</button></h5>
                    <div id="product_rows_container">
                        <div class="product-row">
                            <div class="form-group"><label>Product<span class="required-star">*</span></label><select class="form-control select-product" name="product[]" required></select></div>
                            <div class="form-group" style="flex: 0.4;"><label>Qty<span class="required-star">*</span></label><input type="number" name="qty[]" min="1" value="1" class="form-control" required></div>
                            <button type="button" class="btn btn-danger btn-sm btn-remove-product"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                     <div class="row mt-3">
                         <div class="col-md-6 form-group"><label>Client Type<span class="required-star">*</span></label><select class="form-control" name="customertype" id="customertype" required><option value="">Select Type</option><option value="1">New Customer</option><option value="2">Repeat Customer</option></select></div>
                         <div class="col-md-6 form-group"><label>Probability (%)<span class="required-star">*</span></label><select name="probability" id="probability" class="form-control" required><option value="">Select</option><?php for ($i = 10; $i <= 100; $i += 10) { echo "<option value='{$i}'>{$i}%</option>"; } ?></select></div>
                    </div>
                </div>

                <div class="row"><div class="col-12 text-center mb-4"><button type="submit" class="btn btn-success btn-lg"><i class="fa fa-check"></i> Submit Opportunity</button></div></div>
            </form>
        </div>
    </div>
    
    <div id="add-customer-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="add_customer_form">
                    <div class="modal-header"><h4 class="modal-title">Add New Company</h4><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>
                    <div class="modal-body">
                        <p class="text-muted">All fields marked with <span class="required-star">*</span> are mandatory.</p>
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Company Name<span class="required-star">*</span></label><input type="text" class="form-control" id="new_companyname" name="new_companyname" required></div>
                            <div class="col-md-6 form-group"><label>Company Brand<span class="required-star">*</span></label><select class="form-control" id="new_company_brand" name="new_company_brand" style="width:100%" required><option value=""></option><?php $brands = $this->db->select('id,name')->from('spare_company_brand')->get()->result(); foreach($brands as $brand) { echo '<option value="'.$brand->id.'">'.htmlspecialchars($brand->name).'</option>'; } ?></select></div>
                            <div class="col-md-6 form-group">
                                <label>Country<span class="required-star">*</span></label>
                                <select class="form-control" id="new_country" name="new_country" style="width:100%" required>
                                    <option value="">Select Country</option>
                                    <?php $countries = $this->db->select('country_name, country_id')->from('countries')->where('country_status',1)->order_by('country_name', 'asc')->get()->result(); foreach($countries as $country) { echo '<option value="'.$country->country_id.'">'.htmlspecialchars($country->country_name).'</option>'; } ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group"><label>Email (use comma for multiple)<span class="required-star">*</span></label><input type="text" class="form-control" id="new_email" name="new_email" required></div>
                            <div class="col-md-4 form-group"><label>Contact Person<span class="required-star">*</span></label><input type="text" class="form-control" id="contactpersonname" name="contactpersonname" required></div>
                            <div class="col-md-4 form-group"><label>Contact No<span class="required-star">*</span></label><input type="text" class="form-control" id="personcontactno" name="personcontactno" required></div>
                            <div class="col-md-4 form-group"><label>Alternate Contact No</label><input type="text" class="form-control" id="acontactno" name="acontactno"></div>
                            <div class="col-md-12 form-group"><label>Address<span class="required-star">*</span></label><textarea id="new_address" name="new_address" class="form-control" rows="3" required></textarea></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button><button type="button" id="save_customer_button" class="btn btn-info waves-effect waves-light">Save Customer</button></div>
                </form>
            </div>
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
        // --- COMMON VALIDATION HANDLERS ---
        const validationHandlers = {
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('error');
                if (element.hasClass('select2-hidden-accessible')) { error.insertAfter(element.next('.select2-container')); }
                else { error.insertAfter(element); }
            },
            highlight: function(element, errorClass, validClass) { $(element).addClass('error').removeClass(validClass); $(element).closest('.form-group').find('.select2-selection').addClass('error'); },
            unhighlight: function(element, errorClass, validClass) { $(element).removeClass('error').addClass(validClass); $(element).closest('.form-group').find('.select2-selection').removeClass('error'); }
        };

        // --- MAIN FORM VALIDATION ---
        var validator = $('#opportunity_form').validate({
            ...validationHandlers,
            rules: {
                op_date: "required", lsource: "required", op_type: "required", marketing: "required",
                customer: "required", country: "required", address: "required", customertype: "required", probability: "required",
                customercontactno: { required: true }, customeremailid: { required: true, email: true },
                'product[]': "required", 'qty[]': { required: true, number: true, min: 1 }
            },
            messages: { customeremailid: { email: "Please enter a valid email address." }, 'qty[]': { min: "Quantity must be at least 1." } }
        });
        
        $('#opportunity_form').on('submit', function(e) {
            if ($(this).valid()) {
                $.blockUI({ 
                    message: '<h3><i class="fa fa-spinner fa-spin"></i> Please wait...</h3>',
                    css: { border: 'none', padding: '15px', backgroundColor: '#000', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', opacity: .5, color: '#fff' } 
                });
                return true; 
            }
        });

        // --- MODAL FORM VALIDATION ---
        $('#add_customer_form').validate({
            ...validationHandlers,
            rules: {
                new_companyname: "required", new_company_brand: "required", 
                new_country: "required", new_address: "required",
                new_email: { required: true },
                contactpersonname: "required", personcontactno: "required"
            }
        });

        // --- INITIALIZATIONS & EVENT BINDINGS ---
        get_opp_no();
        initializeSelect2($('.select-product'));
        $('.select-customer').select2({ placeholder: 'Type to search...', allowClear: true, ajax: { url: "<?php echo page_url;?>Spares/get_customer_by_company", dataType: 'json', delay: 250, data: (params) => ({searchTerm:params.term}), processResults: (data) => ({results:data}) }});
        $('.select-brand').select2({ placeholder: 'Select a brand', allowClear: true });
        $('#country').select2({ placeholder: 'Select a Country' });

        // MODAL SELECT2 INITIALIZERS
        $('#new_company_brand').select2({ placeholder: 'Select or type a Brand', tags: true, dropdownParent: $('#add-customer-modal') });
        $('#new_country').select2({ placeholder: 'Select a Country', dropdownParent: $('#add-customer-modal') }); 
        
        // --- DYNAMIC PRODUCT ROWS ---
        $('#add_product_row').on('click', function() {
            let newRow = $('#product_rows_container .product-row:first').clone();
            newRow.find('input, select').val(''); newRow.find('input[type="number"]').val('1');
            newRow.find('.select2-container').remove();
            let newSelect = newRow.find('select.select-product');
            newSelect.removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden').removeAttr('tabindex');
            $('#product_rows_container').append(newRow);
            initializeSelect2(newSelect);
        });

        $('#product_rows_container').on('click', '.btn-remove-product', function() {
            if ($('#product_rows_container .product-row').length > 1) { $(this).closest('.product-row').remove(); } 
            else { alert('You must have at least one product.'); }
        });

        // --- MODAL SAVE BUTTON ---
        $('#save_customer_button').on('click', function() {
            if ($('#add_customer_form').valid()) {
                addCustomerAjax();
            }
        });
    });

    // --- HELPER & AJAX FUNCTIONS ---
    function initializeSelect2(element) {
        element.select2({
            placeholder: 'Type to search for a product...',
            ajax: {
                url: "<?php echo page_url.'Spares/get_products_ajax'; ?>",
                dataType: 'json',
                delay: 250,
                data: (params) => ({ searchTerm: params.term }),
                processResults: (data) => ({ results: data })
            }
        });
    }

    function checkifexhibition() { $('#exhibitiondiv').toggle($('#lsource').val() == 8); }
    
    function get_opp_no() {
        var op_type = $("#op_type").val();
        if (op_type) {
            const prefix = 'SPM/SPARES/';
            const typeString = (op_type == 1) ? 'DOM' : 'EXP';
            const now = new Date();
            const month = now.getMonth(); 
            let year = now.getFullYear();
            let financialYear;
            if (month >= 3) { 
                financialYear = String(year).slice(-2) + '-' + String(year + 1).slice(-2);
            } else {
                financialYear = String(year - 1).slice(-2) + '-' + String(year).slice(-2);
            }
            $.post("<?php echo page_url;?>Spares/generateOppNo", { op_type: op_type }, function(data) {
                const raw_number = data.trim();
                if (!isNaN(raw_number) && raw_number) {
                    const padded_number = String(raw_number).padStart(4, '0');
                    const full_op_no = `${prefix}${typeString}/${padded_number}/${financialYear}`;
                    $("#op_no").val(full_op_no);
                    $("#op_increment_no").val(raw_number);
                } else {
                    $("#op_no").val('Error: Invalid number from server');
                    $("#op_increment_no").val('');
                }
            }).fail(function() {
                $("#op_no").val('Error: Could not connect to server');
            });
        }
    }

    function getDetails() {
        var custid = $('#customer').val();
        if (!custid) {
            $('#address, #gst, #customercontactno, #customeremailid').val('');
            $('#brand, #country').val(null).trigger('change');
            return;
        }
        $.post("<?php echo page_url;?>Spares/getCustomerDetails_frommaster", { custid: custid }, function(data) {
            if (data) {
                var arr = data.split('|');
                $("#address").val(arr[0]);
                $("#gst").val(arr[1]);
                $("#customercontactno").val(arr[2]);
                $("#customeremailid").val(arr[3]);
                var countryId = arr[4];
                if (countryId) { $('#country').val(countryId).trigger('change'); } 
                else { $('#country').val(null).trigger('change'); }
                var brandName = arr[5];
                var brandId = arr[6]; 
                if (brandName && brandId) {
                    var newOption = new Option(brandName, brandId, true, true);
                    $('#brand').empty().append(newOption).trigger('change');
                } else {
                    $('#brand').val(null).trigger('change');
                }
            }
        }, 'text');
    }

    function getcountrytax() {
        var countryid = $("#country").val();
        if (countryid) { $.post("<?php echo page_url;?>Spares/getcountrytaxinfo", { countryid }, function(data) { if (data) { $("#showhidetaxtype").show(); $("#taxt").html(data); } else { $("#showhidetaxtype").hide(); } }); }
        else { $("#showhidetaxtype").hide(); }
    }
    
    function addCustomerAjax() {
        var btn = $("#save_customer_button");
        btn.text('Saving...').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: "<?php echo page_url;?>Spares/add_new_ajax_customer",
            data: {
                company: $("#new_companyname").val(), 
                brand: $("#new_company_brand").val(),
                country: $("#new_country").val(),
                email: $("#new_email").val(),
                address: $("#new_address").val(), 
                contactpersonname: $("#contactpersonname").val(),
                personcontactno: $("#personcontactno").val(), 
                acontactno: $("#acontactno").val()
            },
            success: function(data) {
                if (data.split('~')[0] == 1) {
                    $("#add-customer-modal").modal('hide');
                    alert('Customer successfully added. You can now search for them in the customer list.');
                    $('#add_customer_form')[0].reset();
                    $('#new_company_brand').val(null).trigger('change');
                    $('#new_country').val(null).trigger('change');
                } else {
                    var errorMessage = data.split('~')[1] || 'An error occurred or this customer already exists.';
                    alert(errorMessage);
                }
            },
            error: function() { alert('Could not connect to the server. Please try again.'); },
            complete: function() { btn.text('Save Customer').prop('disabled', false); }
        });
    }
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Check for success message
            <?php if ($this->session->flashdata('success')) { ?>
                $.gritter.add({
                    title: 'Success!',
                    text: '<?php echo $this->session->flashdata('success'); ?>',
                    sticky: false,
                    time: 5000,
                    class_name: 'gritter-success'
                });
            <?php } ?>

            // Check for error message
            <?php if ($this->session->flashdata('error')) { ?>
                $.gritter.add({
                    title: 'Error!',
                    text: '<?php echo str_replace(["\r", "\n"], " ", $this->session->flashdata('error')); ?>',
                    sticky: true,
                    class_name: 'gritter-danger'
                });
            <?php } ?>
        });
    </script>
</body>
</html>