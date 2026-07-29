<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PO Received - <?php echo $opportunity->op_no; ?></title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f9; }
        .card-box { border-radius: 12px; padding: 35px; box-shadow: 0 5px 25px rgba(0,0,0,0.07); background: #fff; margin-top: 40px; border-top: 4px solid #28a745; }
        .header-title { color: #28a745; font-weight: 700; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .opp-info-bar { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #eef0f2; }
        
        /* jQuery Validation Highlighting */
        label.error { color: #ef5350; font-size: 12px; font-weight: 500; margin-top: 5px; }
        input.error, textarea.error { border: 1px solid #ef5350 !important; background-color: #fff8f8 !important; }
        input.valid { border: 1px solid #28a745 !important; }
        
        .required-star { color: #ef5350; }
        .btn-success { background-color: #28a745 !important; border: 1px solid #28a745 !important; padding: 12px 30px; font-weight: 600; }
        .btn-success:hover { background-color: #218838 !important; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="card-box">
                        <h4 class="header-title"><i class="fa fa-cloud-upload"></i> Purchase Order Recording</h4>
                        <div class="opp-info-bar">
                            <div class="row">
                                <div class="col-sm-6">
                                    <small class="text-muted">Opportunity Number</small><br>
                                    <strong><?php echo $opportunity->op_no; ?></strong>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <small class="text-muted">Customer</small><br>
                                    <strong><?php echo $opportunity->company_name; ?></strong>
                                </div>
                            </div>
                        </div>

                        <?php if($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>

                        <form id="poForm" action="<?php echo page_url; ?>ServiceLeads/save_po_details" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="opportunity_id" value="<?php echo $opportunity->opportunity_id; ?>">

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>PO Number <span class="required-star">*</span></label>
                                    <input type="text" name="po_number" id="po_number" class="form-control" placeholder="e.g. PO/2026/001">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>PO Date <span class="required-star">*</span></label>
                                    <input type="date" name="po_date" id="po_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Final Agreed Amount <span class="required-star">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-inr"></i></span>
                                        <input type="number" step="0.01" name="po_amount" id="po_amount" class="form-control" value="<?php echo $suggested_amount; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>PO Copy (PDF/JPG) <span class="required-star">*</span></label>
                                    <input type="file" name="po_file" id="po_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Max size 5MB</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Internal Remarks / Notes</label>
                                <textarea name="po_remarks" id="po_remarks" class="form-control" rows="4" placeholder="Mention any deviations or specific billing instructions..."></textarea>
                            </div>

                            <hr>
                            <div class="text-center m-t-20">
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    <i class="fa fa-check"></i> Save Details & Move to WON
                                </button>
                                <a href="<?php echo page_url; ?>ServiceLeads/opportunity_detail/<?php echo $opportunity->opportunity_id; ?>" class="btn btn-link text-muted">
                                    Cancel & Go Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // --- JQUERY VALIDATION WITH HIGHLIGHTING ---
        $("#poForm").validate({
            rules: {
                po_number: { required: true, minlength: 2 },
                po_date: { required: true },
                po_amount: { required: true, min: 1 },
                po_file: { required: true }
            },
            messages: {
                po_number: "Please enter the Purchase Order number",
                po_date: "Please select the date mentioned on PO",
                po_amount: "Agreed amount must be greater than zero",
                po_file: "Please upload a scanned copy of the PO"
            },
            highlight: function(element) {
                $(element).addClass('error').removeClass('valid');
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).removeClass('error').addClass('valid');
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorPlacement: function(error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Recording this PO will move the lead to 'ORDER WON' stage.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Save & Finalize'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Uploading file and updating status',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        form.submit();
                    }
                });
            }
        });
    });
    </script>
</body>
</html>