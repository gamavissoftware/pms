<?php
defined('BASEPATH') or exit('No direct script access allowed');
$company_info = $this->db->select('company_name, colorcode')->from('company_information')->get()->row();

// Set default values for new part
$form_code = $part->code ?? '';
$form_description = $part->description ?? '';
$form_price = $part->price ?? '0.00';
$form_available_qty = isset($part->available_qty) ? $part->available_qty : '0.000';
$form_revision = $part->revision ?? '0';
$form_status = $part->status ?? '1'; // Default to 'Active'
$inventory_columns_available = isset($inventory_columns_available) ? $inventory_columns_available : false;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> &mdash; <?php echo $page_title; ?></title>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .page-title { color: <?php echo $company_info->colorcode ?? '#333'; ?>; }
        .card-box { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: none; }
        .form-control { border-radius: 5px; border: 1px solid #ddd; }
        .btn-success { background-color: #2ecc71; border-color: #2ecc71; }
        .btn-secondary { background-color: #6c757d; border-color: #6c757d; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
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
                        <div class="btn-group pull-right"> 
                            <a href="<?php echo page_url.'spares_master'; ?>" class="btn btn-secondary waves-effect waves-light"> 
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a> 
                        </div> 
                        <h4 class="page-title"><?php echo $page_title; ?></h4> 
                    </div> 
                </div> 
            </div>

            <div class="row">
                <div class="col-12">
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <form action="<?php echo $form_action; ?>" method="POST" class="form-horizontal">
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="code">Part Code <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="code" name="code" value="<?php echo html_escape($form_code); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="description">Description <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo html_escape($form_description); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="price">Price <span class="text-danger">*</span></label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₹</span>
                                        </div>
                                        <input type="text" class="form-control" id="price" name="price" value="<?php echo html_escape($form_price); ?>" required>
                                    </div>
                                </div>
                                
                                <label class="col-sm-2 col-form-label" for="revision">Revision</label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" id="revision" name="revision" value="<?php echo html_escape($form_revision); ?>">
                                </div>
                            </div>

                            <?php if ($inventory_columns_available): ?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label" for="available_qty">Available Qty</label>
                                    <div class="col-sm-4">
                                        <input type="number" step="0.001" min="0" class="form-control" id="available_qty" name="available_qty" value="<?php echo html_escape($form_available_qty); ?>">
                                        <small class="text-muted">MRP shortage calculation will use this quantity.</small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    Available quantity field is not active yet. Please run <strong>Database/spares_master_inventory_001.sql</strong>.
                                </div>
                            <?php endif; ?>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="status">Status <span class="text-danger">*</span></label>
                                <div class="col-sm-4">
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="1" <?php if ($form_status == '1') echo 'selected'; ?>>Active</option>
                                        <option value="0" <?php if ($form_status == '0') echo 'selected'; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-success waves-effect waves-light">
                                        <i class="fa fa-check"></i> Save Spare Part
                                    </button>
                                    <a href="<?php echo page_url.'spares_master'; ?>" class="btn btn-secondary waves-effect waves-light">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <?php $this->load->view('common/footer'); ?>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>
</html>
