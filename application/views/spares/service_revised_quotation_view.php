<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Fetch company info for dynamic branding
$company_info = $this->db->select('company_name, logo, colorcode')->from('company_information')->get()->row();

// --- Step 1: Logic to fetch GST No from Master Tables ---
$existing_gst = '';
$cust_id = $opportunity->customer_id;

// Check Spares Table
$check_spares = $this->db->get_where('spares_customers', ['customer_id' => $cust_id])->row();
if ($check_spares && !empty($check_spares->tax_number)) {
    $existing_gst = $check_spares->tax_number;
} else {
    // Fallback to Marketing Table
    $check_marketing = $this->db->get_where('customer_detail', ['id' => $cust_id])->row();
    if ($check_marketing && !empty($check_marketing->gst)) {
        $existing_gst = $check_marketing->gst;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Revise Service Quotation</title>

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
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e1e6ef;
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

        .required-field {
            border-left: 3px solid #f1b53d;
        }

        .curr-symbol {
            font-weight: bold;
            color: #2c3e50;
        }

        .adjustment-field {
            background-color: #f0f7ff !important;
            border: 1px solid #cce5ff !important;
            font-weight: bold;
            color: #004085;
        }

        .discount-field {
            background-color: #fff4f2 !important;
            border: 1px solid #ffccc7 !important;
            font-weight: bold;
            color: #d4380d;
        }

        .converter-box {
            background: #fff4e5;
            border: 1px solid #ffd1a3;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
        }

        .page-title {
            color: <?php echo $company_info->colorcode ?? '#333'; ?>;
            font-weight: 600;
        }

        .error-border {
            border: 2px solid #ff5d48 !important;
            box-shadow: 0 0 5px rgba(255, 93, 72, 0.45);
        }

        .unit-price {
            font-weight: 600;
            color: #2b3d51;
            background-color: #fdfdfd;
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
            text-transform: none;
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

            .converter-box {
                float: none !important;
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
    <div class="container-fluid" id="main-container">

        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box <?php echo $is_export ? 'export-header' : ''; ?>">
                    <h4 class="page-title">
                        Revise Service Quotation (Stage 4)
                        <span class="badge badge-warning" style="margin-left: 10px;">REVISION MODE</span>
                        <span class="pull-right" style="font-size: 14px; margin-top: 5px; color: #666;">
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
                        <label>Revision Date</label>
                        <input type="date"
                               name="quotation_date"
                               class="form-control required-field"
                               value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Currency Mode</label>
                        <select name="currency" id="currency_selector" class="form-control" style="font-weight: 600; color: #003366;">
                            <option value="INR" <?php echo ($prev_quote->currency == 'INR') ? 'selected' : ''; ?>>Domestic (INR ₹)</option>
                            <option value="USD" <?php echo ($prev_quote->currency == 'USD') ? 'selected' : ''; ?>>Export (USD $)</option>
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
                               value="<?php echo $prev_quote->kindattention; ?>"
                               placeholder="Kind Attention"
                               required>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Customer Contact No:</label>
                        <input type="text"
                               name="customercontactno"
                               id="customercontactno"
                               class="form-control"
                               value="<?php echo $prev_quote->contactno; ?>"
                               placeholder="Customer Contact No."
                               required>
                    </div>


  <div class="col-md-3 form-group">
                        <label>Customer Email:</label>
                        <input type="text"
                               name="customeremail"
                               id="customeremail"
                               class="form-control"
                               value="<?php echo $prev_quote->email; ?>"
                               placeholder="Customer Email"
                               >
                    </div>

                    <div class="col-md-12 form-group">
                        <label>Subject (Rich Text)</label>
                        <textarea id="subject_editor" name="subject"><?php echo $prev_quote->subject; ?></textarea>
                    </div>

                </div>
            </div>

            <!-- SERVICE LINE ITEMS -->
            <div class="card-box">
                <div class="service-card-header">
                    <div>
                        <h4 class="header-title">Edit Service Line Items</h4>
                        <div class="service-line-note">
                            Add or revise service lines. Complete row will remain visible on screen without horizontal scroll.
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="converter-box form-inline">
                            <label class="m-r-10">Conversion (1 USD = 94.00)</label>
                            <input type="number" id="calc_inr" class="form-control input-sm" placeholder="Type INR" style="width:100px">
                            <i class="fa fa-arrow-right m-x-10"></i>
                            <input type="text" id="calc_usd" class="form-control input-sm" readonly style="width:80px; background:#f0f0f0;">
                        </div>
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
                            <?php if (!empty($prev_items)): ?>
                                <?php foreach($prev_items as $item): ?>
                                    <tr class="item-row">
                                        <td class="service-col">
                                            <select class="form-control service-select select2 required-field" name="charge_id[]">
                                                <option value="">Select Service...</option>
                                                <?php foreach($service_master as $s): ?>
                                                    <option value="<?php echo $s->id; ?>"
                                                            <?php echo ($s->id == $item->charge_id) ? 'selected' : ''; ?>
                                                            data-rate-inr="<?php echo $s->default_rate_inr; ?>"
                                                            data-rate-usd="<?php echo $s->default_rate_usd; ?>"
                                                            data-type="<?php echo $s->charge_type; ?>">
                                                        <?php echo $s->charge_name; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <textarea name="description[]"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Line notes..."><?php echo $item->description; ?></textarea>
                                        </td>

                                        <td class="rate-col">
                                            <input type="number"
                                                   step="0.01"
                                                   class="form-control unit-price required-field"
                                                   name="rate[]"
                                                   value="<?php echo $item->rate; ?>">
                                        </td>

                                        <td class="small-col">
                                            <input type="number"
                                                   class="form-control qty-days"
                                                   name="no_of_days[]"
                                                   value="<?php echo $item->no_of_days; ?>"
                                                   min="1">
                                        </td>

                                        <td class="small-col">
                                            <input type="number"
                                                   class="form-control qty-eng"
                                                   name="no_of_engineers[]"
                                                   value="<?php echo $item->no_of_engineers; ?>"
                                                   min="1">
                                        </td>

                                        <td class="uom-col">
                                            <select name="uom[]" class="form-control uom-select">
                                                <option value="Day" <?php echo ($item->uom == 'Day') ? 'selected' : ''; ?>>Day</option>
                                                <option value="Visit" <?php echo ($item->uom == 'Visit') ? 'selected' : ''; ?>>Visit</option>
                                                <option value="Lumpsum" <?php echo ($item->uom == 'Lumpsum') ? 'selected' : ''; ?>>Lumpsum</option>
                                                <option value="KM" <?php echo ($item->uom == 'KM') ? 'selected' : ''; ?>>KM</option>
                                                <option value="Time" <?php echo ($item->uom == 'Time') ? 'selected' : ''; ?>>Time</option>
                                            </select>
                                        </td>

                                        <td class="scope-col">
                                            <select name="scope[]" class="form-control scope-select">
                                                <option value="0" <?php echo ($item->is_customer_scope == 0) ? 'selected' : ''; ?>>SPM Scope</option>
                                                <option value="1" <?php echo ($item->is_customer_scope == 1) ? 'selected' : ''; ?>>Cust. Scope</option>
                                            </select>
                                        </td>

                                        <td class="discount-col">
                                            <input type="number"
                                                   step="0.01"
                                                   class="form-control discount-field"
                                                   name="discount[]"
                                                   value="<?php echo $item->discount_amount ?? 0; ?>">
                                        </td>

                                        <td class="total-col">
                                            <input type="text"
                                                   class="form-control row-total"
                                                   readonly
                                                   value="<?php echo number_format($item->row_total, 2, '.', ''); ?>">
                                        </td>

                                        <td class="action-col">
                                            <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove Line">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="item-row">
                                    <td class="service-col">
                                        <select class="form-control service-select select2 required-field" name="charge_id[]">
                                            <option value="">Select Service...</option>
                                            <?php foreach($service_master as $s): ?>
                                                <option value="<?php echo $s->id; ?>"
                                                        data-rate-inr="<?php echo $s->default_rate_inr; ?>"
                                                        data-rate-usd="<?php echo $s->default_rate_usd; ?>"
                                                        data-type="<?php echo $s->charge_type; ?>">
                                                    <?php echo $s->charge_name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <textarea name="description[]"
                                                  class="form-control"
                                                  rows="2"
                                                  placeholder="Line notes..."></textarea>
                                    </td>

                                    <td class="rate-col">
                                        <input type="number" step="0.01" class="form-control unit-price required-field" name="rate[]" value="0.00">
                                    </td>

                                    <td class="small-col">
                                        <input type="number" class="form-control qty-days" name="no_of_days[]" value="1" min="1">
                                    </td>

                                    <td class="small-col">
                                        <input type="number" class="form-control qty-eng" name="no_of_engineers[]" value="1" min="1">
                                    </td>

                                    <td class="uom-col">
                                        <select name="uom[]" class="form-control uom-select">
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
                                        <input type="number" step="0.01" class="form-control discount-field" name="discount[]" value="0.00">
                                    </td>

                                    <td class="total-col">
                                        <input type="text" class="form-control row-total" readonly value="0.00">
                                    </td>

                                    <td class="action-col">
                                        <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove Line">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <button type="button" id="add-row" class="btn btn-success btn-sm m-t-10">
                    <i class="fa fa-plus"></i> Add Service Line
                </button>
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
                                       value="<?php echo $prev_quote->gst_percent; ?>">
                            </div>

                            <div class="col-md-4 form-group" id="wht_container" style="display:none">
                                <label>WHT (%)</label>
                                <input type="number"
                                       name="wht_percent"
                                       id="wht_percent"
                                       class="form-control"
                                       value="<?php echo $prev_quote->wht_percent ?? 0; ?>">
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Ex-Works Amount (<span class="curr-symbol">₹</span>)</label>
                                <input type="number"
                                       step="0.01"
                                       name="ex_works_amount"
                                       id="ex_works_amount"
                                       class="form-control adjustment-field"
                                       value="<?php echo $prev_quote->ex_works_amount ?? 0; ?>">
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Freight Amount (<span class="curr-symbol">₹</span>)</label>
                                <input type="number"
                                       step="0.01"
                                       name="freight_amount"
                                       id="freight_amount"
                                       class="form-control adjustment-field"
                                       value="<?php echo $prev_quote->freight_amount ?? 0; ?>">
                            </div>

                            <div class="col-md-4 form-group">
                                <label>Overall Special Discount (<span class="curr-symbol">₹</span>)</label>
                                <input type="number"
                                       step="0.01"
                                       name="overall_discount"
                                       id="overall_discount"
                                       class="form-control discount-field"
                                       value="<?php
                                            $item_disc = 0;
                                            if (!empty($prev_items)) {
                                                $item_disc = array_sum(array_column($prev_items, 'discount_amount'));
                                            }
                                            echo number_format($prev_quote->total_discount - $item_disc, 2, '.', '');
                                       ?>">
                            </div>

                            <div class="col-md-12 form-group">
                                <label>Revised Quotation Terms & Conditions</label>
                                <textarea id="terms_editor" name="terms_conditions"><?php echo $prev_quote->terms_conditions; ?></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card-box text-right summary-card">
                        <h4 class="header-title m-b-20">Revised Summary (<span class="curr-symbol">₹</span>)</h4>

                        <h5>Gross Basic: <span id="display-gross">0.00</span></h5>
                        <h5>Item Level Discounts: <span id="display-item-discount" class="text-danger">0.00</span></h5>
                        <h5>Additional Discount: <span id="display-overall-discount" class="text-danger">0.00</span></h5>

                        <hr>

                        <h5 style="color:#333;">Net Taxable: <span id="display-taxable">0.00</span></h5>
                        <h5 id="gst_summary_line">GST Amount: <span id="display-gst">0.00</span></h5>
                        <h5 id="wht_summary_line" style="display:none">WHT Amount: <span id="display-wht">0.00</span></h5>

                        <hr>

                        <h5>Ex-Works: <span id="display-ex-works">0.00</span></h5>
                        <h5>Freight: <span id="display-freight">0.00</span></h5>

                        <hr>

                        <h3 class="text-primary" style="font-weight: 700; font-size: 28px;">
                            Grand Total: <span id="display-total">0.00</span>
                        </h3>
                    </div>
                </div>

            </div>

            <div class="text-center m-b-40">
                <button type="button"
                        id="submitBtn"
                        class="btn btn-warning btn-lg waves-effect waves-light"
                        style="padding: 12px 40px; font-weight: 600;">
                    <i class="fa fa-refresh"></i> Update & Generate Revised PDF
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

    // Init Editors
    tinymce.init({
        selector: '#subject_editor, #terms_editor',
        menubar: false,
        plugins: 'lists advlist',
        toolbar: 'bold italic underline | fontsize | bullist numlist | outdent indent | removeformat',
        font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt',
        height: 250,
        content_style: 'body { font-family:Poppins,sans-serif; font-size:14px }'
    });

    function initPlugins() {
        $('.service-select').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    placeholder: 'Search or Select Service Charge...',
                    width: '100%'
                });
            }
        });
    }

    initPlugins();

    // Currency Toggle
    $('#currency_selector').on('change', function() {
        let mode = $(this).val();

        $('.curr-symbol').text(mode === 'USD' ? '$' : '₹');

        if (mode === 'USD') {
            $('#gst_container, #gst_summary_line').hide();
            $('#wht_container, #wht_summary_line').show();
            $('#gst_percent').val(0);
        } else {
            $('#gst_container, #gst_summary_line').show();
            $('#wht_container, #wht_summary_line').hide();
            $('#wht_percent').val(0);
            $('#gst_percent').val(18);
        }

        $('#service-table tbody tr').each(function() {
            let row = $(this);
            let opt = row.find('.service-select option:selected');

            if (opt.val() !== '') {
                let rate = mode === 'USD' ? opt.data('rate-usd') : opt.data('rate-inr');
                row.find('.unit-price').val(rate);
            }
        });

        calculateTotals();
    });

    // Service selection logic
    $(document).on('change', '.service-select', function() {
        let opt = $(this).find('option:selected');
        let row = $(this).closest('tr');
        let mode = $('#currency_selector').val();

        row.find('.service-select').next('.select2-container').find('.select2-selection').removeClass('error-border');

        if (opt.val() !== '') {
            let defaultRate = mode === 'USD' ? opt.data('rate-usd') : opt.data('rate-inr');

            row.find('.unit-price').val(defaultRate);

            if (opt.data('type') === 'Fixed') {
                row.find('.qty-days, .qty-eng')
                    .val(1)
                    .attr('readonly', true)
                    .css('background', '#eee');
            } else {
                row.find('.qty-days, .qty-eng')
                    .attr('readonly', false)
                    .css('background', '#fff');
            }
        } else {
            row.find('.unit-price').val('0.00');
            row.find('.qty-days, .qty-eng')
                .val(1)
                .attr('readonly', false)
                .css('background', '#fff');
        }

        calculateTotals();
    });

    $(document).on('input', '.unit-price, .qty-days, .qty-eng, .discount-field, .adjustment-field, #gst_percent, #wht_percent, #overall_discount', function() {
        calculateTotals();
    });

    $(document).on('change', '.scope-select', function() {
        calculateTotals();
    });

    // Calculation Engine
    function calculateTotals() {
        let grossAmt = 0;
        let itemDiscountTotal = 0;

        $('#service-table tbody tr').each(function() {
            let row = $(this);
            let isCustScope = row.find('.scope-select').val() === '1';

            if (isCustScope) {
                row.find('.row-total').val('CUST SCOPE');
            } else {
                let r = parseFloat(row.find('.unit-price').val()) || 0;
                let d = parseFloat(row.find('.qty-days').val()) || 0;
                let e = parseFloat(row.find('.qty-eng').val()) || 0;
                let disc = parseFloat(row.find('.discount-field').val()) || 0;

                let lineGross = r * d * e;
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

        let final = taxable + gstA + whtA + exWorks + freight;

        $('#display-gross').text(grossAmt.toFixed(2));
        $('#display-item-discount').text(itemDiscountTotal.toFixed(2));
        $('#display-overall-discount').text(overallDiscount.toFixed(2));
        $('#display-taxable').text(taxable.toFixed(2));
        $('#display-gst').text(gstA.toFixed(2));
        $('#display-wht').text(whtA.toFixed(2));
        $('#display-ex-works').text(exWorks.toFixed(2));
        $('#display-freight').text(freight.toFixed(2));
        $('#display-total').text(final.toFixed(2));
    }

    // Add Row
    $('#add-row').click(function() {

        $('.service-select').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        let row = $('#service-table tbody tr:first').clone();

        row.find('.select2-container').remove();

        row.find('select.service-select')
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id')
            .removeAttr('aria-hidden')
            .removeAttr('tabindex')
            .val('');

        row.find('select.service-select option').removeAttr('data-select2-id');

        row.find('textarea[name="description[]"]').val('');

        row.find('.unit-price')
            .val('0.00')
            .removeClass('error-border')
            .attr('readonly', false)
            .css('background', '#fff');

        row.find('.qty-days, .qty-eng')
            .val(1)
            .removeClass('error-border')
            .attr('readonly', false)
            .css('background', '#fff');

        row.find('select[name="uom[]"]').val('Day');
        row.find('select[name="scope[]"]').val('0');

        row.find('.discount-field').val('0.00').removeClass('error-border');
        row.find('.row-total').val('0.00');

        $('#service-table tbody').append(row);

        initPlugins();
        calculateTotals();
    });

    // Remove Row
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

    // Submit
    $('#submitBtn').click(function() {
        let isValid = true;
        let errorMessage = '';

        $('#service-table tbody tr').each(function() {
            let row = $(this);

            let service = row.find('.service-select').val();
            let rate = parseFloat(row.find('.unit-price').val()) || 0;
            let days = parseFloat(row.find('.qty-days').val()) || 0;
            let eng = parseFloat(row.find('.qty-eng').val()) || 0;
            let discount = parseFloat(row.find('.discount-field').val()) || 0;
            let isSpmScope = row.find('.scope-select').val() === '0';

            row.find('.unit-price, .qty-days, .qty-eng, .discount-field').removeClass('error-border');
            row.find('.service-select').next('.select2-container').find('.select2-selection').removeClass('error-border');

            if (!service) {
                isValid = false;
                errorMessage = 'Please select a service for all line items.';
                row.find('.service-select').next('.select2-container').find('.select2-selection').addClass('error-border');
                return false;
            }

            if (isSpmScope && rate <= 0) {
                isValid = false;
                errorMessage = 'Rate must be greater than 0 for all active SPM Scope items.';
                row.find('.unit-price').addClass('error-border');
                return false;
            }

            if (isSpmScope && days <= 0) {
                isValid = false;
                errorMessage = 'Days must be greater than 0.';
                row.find('.qty-days').addClass('error-border');
                return false;
            }

            if (isSpmScope && eng <= 0) {
                isValid = false;
                errorMessage = 'Engineer count must be greater than 0.';
                row.find('.qty-eng').addClass('error-border');
                return false;
            }

            let gross = rate * days * eng;

            if (discount > gross) {
                isValid = false;
                errorMessage = 'Item level discount cannot be greater than line item gross amount.';
                row.find('.discount-field').addClass('error-border');
                return false;
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
            return false;
        }

        Swal.fire({
            title: 'Processing Revision...',
            text: 'Moving draft back to internal approval stage.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $('#quotationForm').submit();

        setTimeout(function() {
            window.location.href = "<?php echo page_url; ?>ServiceLeads/opportunity_list/3";
        }, 2000);
    });

    $('#calc_inr').on('input', function() {
        let inr = parseFloat($(this).val()) || 0;
        $('#calc_usd').val((inr / 94).toFixed(2));
    });

    $('#currency_selector').trigger('change');
    calculateTotals();

});
</script>

</body>
</html>
