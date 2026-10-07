<?php
// --- Step 1: Logic to fetch GST No from Master Tables ---
$existing_gst = '';
$cust_id = $opportunity->customer_id;
$opno = $opportunity->op_no;

$existing_gst = '';
$contact_person = '';
$contact_person_no = '';
$contact_person_email ='';

// Check Spares Customers first
$check_spares = $this->db->get_where('spares_customers', ['customer_id' => $cust_id])->row();
if ($check_spares && !empty($check_spares->tax_number)) {
    $existing_gst = $check_spares->tax_number;
    $contact_person = $check_spares->contact_person;
    $contact_person_no = $check_spares->contact_person_no;
    $contact_person_email = $check_spares->email;
} else {
    // Fallback to Marketing Detail
    $check_marketing = $this->db->get_where('customer_detail', ['id' => $cust_id])->row();
    if ($check_marketing && !empty($check_marketing->gst)) {
        $existing_gst = $check_marketing->gst;
        $contact_person = $check_marketing->customer_name;
        $contact_person_no = $check_marketing->contact_no;
        $contact_person_email = $check_marketing->email;
    }
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Service Quotation</title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.tiny.cloud/1/wwtw9h0axk15f04u8c7pdl69svgrde4jt4uqcc58qtwvc2f0/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .card-box {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e1e6ef;
            background: #fff;
        }

        .customer-bar {
            background: #003366;
            border-radius: 8px;
            padding: 15px;
            color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .customer-bar label {
            color: #aec6cf;
            font-weight: 400;
            margin-bottom: 0;
        }

        .customer-bar h4 {
            margin: 0;
            color: #ffffff;
        }

        .clone-strip {
            display: flex;
            align-items: end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .clone-strip .form-group {
            margin-bottom: 0;
        }

        .clone-status {
            margin-top: 10px;
            font-size: 12px;
            color: #5f6f81;
        }

        .clone-source-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef4ff;
            color: #1f4e8c;
            font-weight: 600;
            margin-right: 8px;
        }

        .required-field {
            border-left: 3px solid #ff5d48;
        }

        .discount-field {
            background-color: #fff4f2 !important;
            border: 1px solid #ffccc7 !important;
            font-weight: bold;
            color: #d4380d;
        }

        .adjustment-field {
            background-color: #f0f7ff !important;
            border: 1px solid #cce5ff !important;
            font-weight: bold;
            color: #004085;
        }

        .error-border {
            border: 2px solid #ff5d48 !important;
            box-shadow: 0 0 5px rgba(255, 93, 72, 0.5);
        }

        .unit-price {
            font-weight: 600;
            color: #2b3d51;
            background-color: #fdfdfd;
        }

        .terms-wrapper {
            margin-top: 15px;
            text-transform: none !important;
        }

        .terms-wrapper ul,
        .terms-wrapper ol {
            padding-left: 20px;
            margin: 0;
        }

        .terms-wrapper li {
            margin-bottom: 2px;
            line-height: 1.3;
        }

        /* ==========================================================
           SERVICE LINE ITEM TABLE FIX - NO HORIZONTAL SCROLL VERSION
           ========================================================== */

        .service-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .service-card-header .header-title {
            margin: 0;
            font-weight: 700;
        }

        .service-line-note {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }

        .service-line-wrapper {
            width: 100%;
            overflow-x: visible !important;
            overflow-y: visible !important;
            padding-bottom: 0;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #ffffff;
        }

        #service-table {
            width: 100% !important;
            min-width: 100% !important;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        #service-table thead th {
            position: sticky;
            top: 75px;
            z-index: 99;
            background-color: #eef2f7;
            color: #333;
            font-size: 11px;
            font-weight: 700;
            padding: 9px 5px;
            white-space: nowrap;
            vertical-align: middle;
            border-bottom: 2px solid #dce3ec;
            border-right: 1px solid #e2e8f0;
        }

        #service-table thead th:last-child {
            border-right: 0;
        }

        #service-table tbody td {
            padding: 6px 5px;
            vertical-align: top;
            background: #fff;
            border-top: 1px solid #eef1f5;
            border-right: 1px solid #f0f2f5;
        }

        #service-table tbody td:last-child {
            border-right: 0;
        }

        #service-table .service-col {
            width: 28%;
        }

        #service-table .rate-col {
            width: 10%;
        }

        #service-table .small-col {
            width: 7%;
        }

        #service-table .uom-col {
            width: 9%;
        }

        #service-table .scope-col {
            width: 11%;
        }

        #service-table .discount-col {
            width: 10%;
        }

        #service-table .total-col {
            width: 11%;
        }

        #service-table .action-col {
            width: 7%;
            text-align: center;
        }

        #service-table input,
        #service-table select,
        #service-table textarea {
            width: 100%;
            min-height: 36px;
            font-size: 12px;
            border-radius: 4px;
            padding: 6px 7px;
        }

        #service-table textarea {
            resize: vertical;
            min-height: 58px;
            margin-top: 6px;
            font-size: 12px;
        }

        #service-table .select2-container {
            width: 100% !important;
        }

        #service-table .select2-container .select2-selection--single {
            height: 36px !important;
            border: 1px solid #E3E3E3 !important;
            display: flex !important;
            align-items: center !important;
            padding-top: 0 !important;
        }

        #service-table .select2-selection__rendered {
            line-height: 34px !important;
            padding-left: 8px !important;
            padding-right: 20px !important;
            font-size: 12px;
        }

        #service-table .select2-selection__arrow {
            height: 34px !important;
        }

        #service-table .row-total {
            background-color: #f3f4f6 !important;
            font-weight: 700;
            color: #333;
        }

        #service-table .remove-row {
            padding: 5px 7px;
            margin-top: 2px;
        }

        .summary-card {
            background: #fdfdfd;
            position: sticky;
            top: 80px;
        }

        @media screen and (max-width: 1200px) {
            #service-table thead th {
                font-size: 10px;
                padding: 8px 4px;
            }

            #service-table input,
            #service-table select,
            #service-table textarea {
                font-size: 11px;
                padding: 5px;
            }

            #service-table .service-col {
                width: 27%;
            }

            #service-table .rate-col {
                width: 10%;
            }

            #service-table .small-col {
                width: 7%;
            }

            #service-table .uom-col {
                width: 9%;
            }

            #service-table .scope-col {
                width: 11%;
            }

            #service-table .discount-col {
                width: 10%;
            }

            #service-table .total-col {
                width: 11%;
            }

            #service-table .action-col {
                width: 8%;
            }
        }

        @media screen and (max-width: 767px) {
            .service-card-header {
                display: block;
            }

            .service-card-header .text-right {
                text-align: left !important;
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>

<header id="topnav">
    <?php $this->load->view('common/nav-menu'); ?>
</header>

<div class="wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">
                        Create Service Quotation
                        <span class="pull-right" style="font-size: 14px; margin-top: 5px;">
                            <i class="fa fa-calendar"></i> <?php echo date('d M, Y'); ?>
                        </span>
                    </h4>
                </div>
            </div>
        </div>

        <div class="customer-bar">
            <div class="row">
                <div class="col-md-5">
                    <label>Customer Name</label>
                    <h4><b><?php echo $opportunity->company_name; ?></b></h4>
                </div>

                <div class="col-md-4">
                    <label>Contact / Email</label>
                    <p class="m-0">
                        <?php echo $opportunity->customer_contact_no; ?> |
                        <?php echo $opportunity->customer_email; ?>
                    </p>
                </div>

                <div class="col-md-3 text-right">
                    <label>Lead Opportunity ID</label>
                    <p class="m-0"><b><?php echo $opportunity->op_no; ?></b></p>
                </div>
            </div>
        </div>

        <form id="quotationForm" action="<?php echo page_url; ?>ServiceLeads/generate_quotation_pdf" method="post" target="_blank">

            <input type="hidden" name="opportunity_id" value="<?php echo $opportunity->opportunity_id; ?>">
            <input type="hidden" name="customer_id" value="<?php echo $opportunity->customer_id; ?>">

            <div class="card-box">
                <div class="clone-strip">
                    <div class="form-group" style="min-width: 260px; flex: 1 1 260px;">
                        <label>Clone From Opportunity No.</label>
                        <input type="text"
                               id="clone_opportunity_no"
                               class="form-control"
                               placeholder="Enter source opportunity no.">
                    </div>

                    <div class="form-group">
                        <button type="button" id="cloneQuotationBtn" class="btn btn-info" style="min-width: 150px;">
                            <i class="fa fa-clone"></i> Clone Quotation
                        </button>
                    </div>
                </div>

                <div id="cloneStatus" class="clone-status"></div>
            </div>

            <div class="card-box">
                <div class="row">

                    <div class="col-md-3 form-group">
                        <label>Quotation No</label>
                        <input type="text"
                               name="quotation_no"
                               class="form-control"
                               value="<?php echo htmlspecialchars($new_quotation_no, ENT_QUOTES, 'UTF-8'); ?>"
                               readonly
                               style="background:#eee; font-weight:bold;">
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Quotation Date</label>
                        <input type="date"
                               name="quotation_date"
                               class="form-control required-field"
                               value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Currency Mode</label>
                        <select name="currency" id="currency_selector" class="form-control" style="font-weight: 600; color: #003366;">
                            <?php 
                        
                            if($opportunity->op_type==1)
                            {
                            ?>
                              <option value="INR" <?php echo !$is_export ? 'selected' : ''; ?>>Domestic (INR ₹)</option>
                            
                          
                        <?php }else{ ?>
                            <option value="USD" <?php echo $is_export ? 'selected' : ''; ?>>Export (USD $)</option>
                            <option value="EUR">Export (EUR €)</option>
                        <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>GSTN (Optional)</label>
                        <input type="text"
                               name="gst_no"
                               class="form-control"
                               value="<?php echo $existing_gst; ?>"
                               placeholder="Update GST if missing">
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Kind Attention:</label>
                        <input type="text"
                               name="kindattention"
                               id="kindattention"
                               class="form-control"
                               value="<?php echo $contact_person;?>"
                               placeholder="Kind Attention"
                               required>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Customer Contact No:</label>
                        <input type="text"
                               name="customercontactno"
                               id="customercontactno"
                               class="form-control"
                               value="<?php echo $contact_person_no;?>"
                               placeholder="Customer Contact No."
                               required>
                    </div>

                     <div class="col-md-3 form-group">
                        <label>Customer Email:</label>
                        <input type="text"
                               name="customeremail"
                               id="customeremail"
                               class="form-control"
                               value="<?php echo $contact_person_email;?>"
                               placeholder="Customer Email."
                               >
                    </div>

                    <div class="col-md-12 form-group">
                        <label>Subject & Detailed Scope (Rich Text)</label>
                        <textarea id="subject_editor" name="subject"><?php echo $opportunity->remarks; ?></textarea>
                    </div>

                </div>
            </div>

            <!-- SERVICE LINE ITEMS -->
            <div class="card-box">
                <div class="service-card-header">
                    <div>
                        <h4 class="header-title">Service Line Items</h4>
                        <div class="service-line-note">
                            Add multiple service lines. Complete row will remain visible on screen without horizontal scroll.
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="button" id="add-row" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i> Add Another Service Line
                        </button>
                    </div>
                </div>

                <div class="service-line-wrapper">
                    <table class="table" id="service-table">
                        <thead>
                            <tr>
                                <th class="service-col">Service Description</th>
                                <th class="rate-col">Rate (<span class="curr-symbol">₹</span>)</th>
                                <th class="small-col">Days</th>
                                <th class="small-col">Eng.</th>
                                <th class="uom-col">UOM</th>
                                <th class="scope-col">Scope</th>
                                <th class="discount-col">Discount</th>
                                <th class="total-col">Net Total</th>
                                <th class="action-col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="item-row">
                                <td class="service-col">
                                    <select class="form-control service-select select2" name="charge_id[]" required>
                                        <option value="">Select Service...</option>
                                        <?php foreach($service_master as $s): ?>
                                            <option value="<?php echo $s->id; ?>"
                                                    data-rate-inr="<?php echo $s->default_rate_inr; ?>"
                                                    data-rate-usd="<?php echo $s->default_rate_usd; ?>"
                                                    data-rate-eur="<?php echo isset($s->default_rate_eur) ? $s->default_rate_eur : '0.00'; ?>"
                                                    data-type="<?php echo $s->charge_type; ?>">
                                                <?php echo $s->charge_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <textarea name="description[]"
                                              class="form-control"
                                              rows="2"
                                              placeholder="Add specific notes..."></textarea>
                                </td>

                                <td class="rate-col">
                                    <input type="number"
                                           step="0.01"
                                           class="form-control unit-price required-field"
                                           name="rate[]"
                                           value="0.00">
                                </td>

                                <td class="small-col">
                                    <input type="number"
                                           class="form-control qty-days"
                                           name="no_of_days[]"
                                           value="1"
                                           min="1">
                                </td>

                                <td class="small-col">
                                    <input type="number"
                                           class="form-control qty-eng"
                                           name="no_of_engineers[]"
                                           value="1"
                                           min="1">
                                </td>

                                <td class="uom-col">
                                    <select name="uom[]" class="form-control">
                                        <option value="Day">Day</option>
                                        <option value="Visit">Visit</option>
                                        <option value="Lumpsum">Lumpsum</option>
                                        <option value="KM">KM</option>
                                        <option value="Time">Time</option>
                                    </select>
                                </td>

                                <td class="scope-col">
                                    <select name="scope[]" class="form-control scope-select">
                                        <option value="0">SPM Scope</option>
                                        <option value="1">Cust. Scope</option>
                                    </select>
                                </td>

                                <td class="discount-col">
                                    <input type="number"
                                           step="0.01"
                                           class="form-control discount-field"
                                           name="discount[]"
                                           value="0.00">
                                </td>

                                <td class="total-col">
                                    <input type="text"
                                           class="form-control row-total"
                                           readonly
                                           value="0.00">
                                </td>

                                <td class="action-col">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove Line">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>

            <div class="row">

                <div class="col-md-7">
                    <div class="card-box">
                        <h4 class="header-title m-b-20">Terms, Taxes & Adjustments</h4>

                        <div class="row">

                            <div class="col-md-4 form-group" id="gst_container">
                                <label>GST (%)</label>
                                <input type="number"
                                       name="gst_percent"
                                       id="gst_percent"
                                       class="form-control"
                                       value="18">
                            </div>

                            <div class="col-md-4 form-group" id="wht_container" style="display:none">
                                <label>WHT (%)</label>
                                <input type="number"
                                       name="wht_percent"
                                       id="wht_percent"
                                       class="form-control"
                                       value="0">
                            </div>

                            <div class="col-md-4 form-group" style="display:none">
                                <label>Ex-Works Amount (<span class="curr-symbol">₹</span>)</label>
                                <input type="number"
                                       step="0.01"
                                       name="ex_works_amount"
                                       id="ex_works_amount"
                                       class="form-control adjustment-field"
                                       value="0.00">
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Freight Amount (<span class="curr-symbol">₹</span>)</label>
                                <input type="number"
                                       step="0.01"
                                       name="freight_amount"
                                       id="freight_amount"
                                       class="form-control adjustment-field"
                                       value="0.00">
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Overall Special Discount (<span class="curr-symbol">₹</span>)</label>
                                <input type="number"
                                       step="0.01"
                                       name="overall_discount"
                                       id="overall_discount"
                                       class="form-control discount-field"
                                       value="0.00">
                            </div>

                            <div class="col-md-12 form-group">
                                <label>Quotation Terms & Conditions</label>
                                <textarea name="terms_conditions" id="terms_conditions" class="form-control" rows="10"><ul><li>Billing would be done according to actual number of days.</li><li>If no. of working days increase you are required to send another P.O. for the extended service man-days as per new shared quotation.</li><li>Valid for 2 months from the date of quotation.</li><li>Working hours from 9.00am to 6.00pm.</li><li>Medical aid at site in your scope, if required.</li><li>If our engineer has to be quarantined then expense of quarantine will be borne by you.</li><li>All holidays that comes in between the work will be included & chargeable as per quoted rates.</li><li>Engineers travel time also included and chargeable</li><li>As per Shubham Pack Policy of lodging and boarding, our engineers shall be liable for individual stay in hotel.</li><li>As per Shubham Pack policy we shall not share the supporting documents of TO & FRO and lodging/boarding and local conveyance of engineer if arranged by Shubhham pack.</li></ul></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card-box text-right summary-card">
                        <h4 class="header-title m-b-20">Price Summary (<span class="curr-symbol">₹</span>)</h4>

                        <h5>Gross Basic: <span id="display-gross">0.00</span></h5>
                        <h5>Item Level Discounts: <span id="display-item-discount" class="text-danger">0.00</span></h5>
                        <h5>Additional Discount: <span id="display-overall-discount" class="text-danger">0.00</span></h5>

                        <hr>

                        <h5 style="color:#333;">Net Taxable: <span id="display-taxable">0.00</span></h5>
                        <h5 id="gst_summary_line">GST Amount: <span id="display-gst">0.00</span></h5>
                        <h5 id="wht_summary_line" style="display:none">WHT Amount: <span id="display-wht">0.00</span></h5>

                        <hr>

                        <h5 style="display:none">Ex-Works: <span id="display-ex-works">0.00</span></h5>
                        <h5>Freight: <span id="display-freight">0.00</span></h5>

                        <hr>

                        <h3 class="text-primary" style="font-weight: 800; font-size: 28px;">
                            Grand Total: <span id="display-total">0.00</span>
                        </h3>
                    </div>
                </div>

            </div>

            <div class="text-center m-b-40">
                <button type="button"
                        id="submitBtn"
                        class="btn btn-primary btn-lg waves-effect waves-light"
                        style="padding: 12px 40px; font-weight: 600;">
                    <i class="fa fa-file-pdf-o"></i> Save & Generate Quotation PDF
                </button>
            </div>

        </form>

    </div>
</div>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var baseRowHtml = $('#service-table tbody').html().trim();

    tinymce.init({
        selector: '#subject_editor, #terms_conditions',
        menubar: false,
        plugins: 'lists advlist',
        toolbar: 'bold italic underline | bullist numlist | outdent indent | removeformat',
        height: 250
    });

    function initPlugins() {
        $('.service-select').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    placeholder: "Search or Select Service Charge...",
                    width: '100%'
                });
            }
        });
    }

    function applyCurrencyMode(mode, resetTaxes) {
        const currencySymbols = { INR: '₹', USD: '$', EUR: '€' };
        $('.curr-symbol').text(currencySymbols[mode] || mode);

        if (mode !== 'INR') {
            $('#gst_container, #gst_summary_line').hide();
            $('#wht_container, #wht_summary_line').show();
            if (resetTaxes) {
                $('#gst_percent').val(0);
            }
        } else {
            $('#gst_container, #gst_summary_line').show();
            $('#wht_container, #wht_summary_line').hide();
            if (resetTaxes) {
                $('#wht_percent').val(0);
                $('#gst_percent').val(18);
            }
        }

        $('#service-table tbody tr').each(function() {
            let row = $(this);
            let opt = row.find('.service-select option:selected');

            if (opt.val() !== "") {
                let rate = getDefaultRate(opt, mode);
                row.find('.unit-price').val(rate);
            }
        });

        calculateTotals();
    }

    function getDefaultRate(option, mode) {
        if (mode === 'EUR') {
            return option.data('rate-eur');
        }

        return mode === 'USD' ? option.data('rate-usd') : option.data('rate-inr');
    }

    function syncRowChargeType(row, forceDefaultValues) {
        let opt = row.find('.service-select option:selected');

        if (opt.val() !== "" && opt.data('type') === 'Fixed') {
            if (forceDefaultValues) {
                row.find('.qty-days, .qty-eng').val(1);
            }

            row.find('.qty-days, .qty-eng')
                .attr('readonly', true)
                .css('background', '#eee');
        } else {
            row.find('.qty-days, .qty-eng')
                .attr('readonly', false)
                .css('background', '#fff');
        }
    }

    function calculateTotals() {
        let grossAmt = 0;
        let itemDiscountTotal = 0;

        $('#service-table tbody tr').each(function() {
            let row = $(this);
            let isCustScope = row.find('.scope-select').val() === '1';

            if (isCustScope) {
                row.find('.row-total').val('CUSTOMER SCOPE');
            } else {
                let rate = parseFloat(row.find('.unit-price').val()) || 0;
                let days = parseFloat(row.find('.qty-days').val()) || 0;
                let eng = parseFloat(row.find('.qty-eng').val()) || 0;
                let disc = parseFloat(row.find('.discount-field').val()) || 0;

                let lineGross = rate * days * eng;
                let lineNet = lineGross - disc;

                if (lineNet < 0) {
                    lineNet = 0;
                }

                row.find('.row-total').val(lineNet.toFixed(2));

                grossAmt += lineGross;
                itemDiscountTotal += disc;
            }
        });

        let overallDiscount = parseFloat($('#overall_discount').val()) || 0;
        let taxable = grossAmt - itemDiscountTotal - overallDiscount;

        if (taxable < 0) {
            taxable = 0;
        }

        let gstP = parseFloat($('#gst_percent').val()) || 0;
        let gstA = (taxable * gstP) / 100;
        let whtP = parseFloat($('#wht_percent').val()) || 0;
        let whtA = (taxable * whtP) / 100;
        let exWorks = parseFloat($('#ex_works_amount').val()) || 0;
        let freight = parseFloat($('#freight_amount').val()) || 0;
        let grandTotal = taxable + gstA + whtA + exWorks + freight;

        $('#display-gross').text(grossAmt.toFixed(2));
        $('#display-item-discount').text(itemDiscountTotal.toFixed(2));
        $('#display-overall-discount').text(overallDiscount.toFixed(2));
        $('#display-taxable').text(taxable.toFixed(2));
        $('#display-gst').text(gstA.toFixed(2));
        $('#display-wht').text(whtA.toFixed(2));
        $('#display-ex-works').text(exWorks.toFixed(2));
        $('#display-freight').text(freight.toFixed(2));
        $('#display-total').text(grandTotal.toFixed(2));
    }

    function createServiceRow(itemData) {
        let row = $(baseRowHtml);
        let rowData = itemData || {};

        row.find('.select2-container').remove();
        row.find('select.service-select')
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id')
            .removeAttr('aria-hidden')
            .removeAttr('tabindex')
            .val(rowData.charge_id || '');

        row.find('select.service-select option').removeAttr('data-select2-id');
        row.find('textarea[name="description[]"]').val(rowData.description || '');
        row.find('.unit-price').val(rowData.rate != null ? rowData.rate : '0.00');
        row.find('.qty-days').val(rowData.no_of_days != null ? rowData.no_of_days : 1);
        row.find('.qty-eng').val(rowData.no_of_engineers != null ? rowData.no_of_engineers : 1);
        row.find('select[name="uom[]"]').val(rowData.uom || 'Day');
        row.find('select[name="scope[]"]').val(rowData.scope != null ? rowData.scope : '0');
        row.find('.discount-field').val(rowData.discount != null ? rowData.discount : '0.00');
        row.find('.row-total').val('0.00');

        syncRowChargeType(row, false);

        return row;
    }

    function rebuildServiceRows(items) {
        let tbody = $('#service-table tbody');
        tbody.empty();

        if (items && items.length) {
            $.each(items, function(_, item) {
                tbody.append(createServiceRow(item));
            });
        } else {
            tbody.append(createServiceRow());
        }

        initPlugins();
        calculateTotals();
    }

    function setEditorContent(editorId, content) {
        let editor = tinymce.get(editorId);

        if (editor) {
            editor.setContent(content || '');
        } else {
            $('#' + editorId).val(content || '');
        }
    }

    function setCloneStatus(payload) {
        let message = '<span class="clone-source-tag">Source: ' + payload.source.opportunity_no + '</span>' +
            'Latest quote ' + payload.source.quotation_no + ' loaded.';

        if (!payload.same_customer) {
            message += ' Current contact and GST were kept for this opportunity.';
        }

        $('#cloneStatus').html(message);
    }

    function loadClonedQuotation() {
        let opportunityNo = $.trim($('#clone_opportunity_no').val());

        if (!opportunityNo) {
            Swal.fire({
                icon: 'warning',
                title: 'Opportunity No. Required',
                text: 'Enter the source opportunity number first.'
            });
            return;
        }

        $('#cloneQuotationBtn')
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Loading');

        $.ajax({
            url: '<?php echo page_url; ?>ServiceLeads/get_clone_quotation_by_opportunity',
            type: 'POST',
            dataType: 'json',
            data: {
                opportunity_no: opportunityNo,
                current_opportunity_id: $('input[name="opportunity_id"]').val()
            }
        }).done(function(response) {
            if (!response || !response.status) {
                Swal.fire({
                    icon: 'error',
                    title: 'Clone Failed',
                    text: response && response.message ? response.message : 'Unable to load quotation data.'
                });
                return;
            }

            if ($('#currency_selector option[value="' + response.quote.currency + '"]').length) {
                $('#currency_selector').val(response.quote.currency);
            }

            applyCurrencyMode($('#currency_selector').val(), false);

            if (response.same_customer) {
                $('#kindattention').val(response.quote.kindattention || '');
                $('#customercontactno').val(response.quote.contactno || '');
                $('#customeremail').val(response.quote.email || '');
            }

            $('#gst_percent').val(response.quote.gst_percent != null ? response.quote.gst_percent : 0);
            $('#wht_percent').val(response.quote.wht_percent != null ? response.quote.wht_percent : 0);
            $('#ex_works_amount').val(response.quote.ex_works_amount != null ? response.quote.ex_works_amount : '0.00');
            $('#freight_amount').val(response.quote.freight_amount != null ? response.quote.freight_amount : '0.00');
            $('#overall_discount').val(response.quote.overall_discount != null ? response.quote.overall_discount : '0.00');

            setEditorContent('subject_editor', response.quote.subject || '');
            setEditorContent('terms_conditions', response.quote.terms_conditions || '');
            rebuildServiceRows(response.items || []);
            setCloneStatus(response);
            calculateTotals();

            Swal.fire({
                icon: 'success',
                title: 'Quotation Cloned',
                text: 'You can review the data and make changes now.',
                timer: 1800,
                showConfirmButton: false
            });
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Clone Failed',
                text: 'Something went wrong while fetching the quotation.'
            });
        }).always(function() {
            $('#cloneQuotationBtn')
                .prop('disabled', false)
                .html('<i class="fa fa-clone"></i> Clone Quotation');
        });
    }

    initPlugins();

    $('#currency_selector').on('change', function() {
        applyCurrencyMode($(this).val(), true);
    });

    $(document).on('change', '.service-select', function() {
        let opt = $(this).find('option:selected');
        let row = $(this).closest('tr');
        let mode = $('#currency_selector').val();

        if (opt.val() !== "") {
            let defaultRate = getDefaultRate(opt, mode);
            row.find('.unit-price').val(defaultRate);
            syncRowChargeType(row, true);
        } else {
            row.find('.unit-price').val('0.00');
            row.find('.qty-days, .qty-eng').val(1);
            syncRowChargeType(row, false);
        }

        calculateTotals();
    });

    $(document).on('input', '.unit-price, .qty-days, .qty-eng, .discount-field, .adjustment-field, #gst_percent, #wht_percent, #overall_discount', function() {
        calculateTotals();
    });

    $(document).on('change', '.scope-select', function() {
        calculateTotals();
    });

    $('#add-row').click(function() {
        $('#service-table tbody').append(createServiceRow());
        initPlugins();
        calculateTotals();
    });

    $(document).on('click', '.remove-row', function() {
        if ($('#service-table tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Required',
                text: 'At least one service line item is required.'
            });
        }
    });

    $('#submitBtn').click(function() {
        let isValid = true;
        let errorMessage = "";

        $('#service-table tbody tr').each(function() {
            let row = $(this);
            let service = row.find('.service-select').val();
            let rate = parseFloat(row.find('.unit-price').val()) || 0;
            let days = parseFloat(row.find('.qty-days').val()) || 0;
            let eng = parseFloat(row.find('.qty-eng').val()) || 0;
            let discount = parseFloat(row.find('.discount-field').val()) || 0;
            let isSpmScope = row.find('.scope-select').val() === '0';

            row.find('.unit-price, .qty-days, .qty-eng, .discount-field').removeClass('error-border');

            if (!service) {
                isValid = false;
                errorMessage = "Please select a service for all line items.";
                row.find('.service-select').next('.select2-container').find('.select2-selection').addClass('error-border');
                return false;
            } else {
                row.find('.service-select').next('.select2-container').find('.select2-selection').removeClass('error-border');
            }

            if (isSpmScope && rate <= 0) {
                isValid = false;
                row.find('.unit-price').addClass('error-border');
                errorMessage = "Rate must be greater than 0 for all active SPM Scope items.";
                return false;
            }

            if (isSpmScope && days <= 0) {
                isValid = false;
                row.find('.qty-days').addClass('error-border');
                errorMessage = "Days must be greater than 0.";
                return false;
            }

            if (isSpmScope && eng <= 0) {
                isValid = false;
                row.find('.qty-eng').addClass('error-border');
                errorMessage = "Engineer count must be greater than 0.";
                return false;
            }

            if (discount > (rate * days * eng)) {
                isValid = false;
                row.find('.discount-field').addClass('error-border');
                errorMessage = "Item level discount cannot be greater than line item gross amount.";
                return false;
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                text: errorMessage
            });
            return false;
        }

        $('#submitBtn').prop('disabled', true);

        Swal.fire({
            title: 'Generating PDF...',
            text: 'Wait... we are sending the draft for approval.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $('#quotationForm').submit();

        setTimeout(function() {
            window.location.href = "<?php echo page_url; ?>ServiceLeads/opportunity_list/3";
        }, 2000);
    });

    $('#cloneQuotationBtn').on('click', loadClonedQuotation);

    $('#clone_opportunity_no').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            loadClonedQuotation();
        }
    });

    applyCurrencyMode($('#currency_selector').val(), true);

});
</script>

</body>
</html>
