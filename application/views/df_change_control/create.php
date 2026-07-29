<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> DF Change Control</title>

    <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <style>
        .change-hero {
            background: linear-gradient(135deg, #17365d 0%, #0f172a 100%);
            border-radius: 18px;
            padding: 28px;
            color: #fff;
            margin-bottom: 20px;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.18);
        }

        .change-hero h4 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 700;
        }

        .change-hero p {
            margin: 0;
            font-size: 15px;
            line-height: 1.7;
            max-width: 900px;
        }

        .change-card {
            border: 1px solid #dbe7f3;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            background: #fff;
        }

        .change-card .card-head {
            background: #f7fbff;
            border-bottom: 1px solid #e1edf7;
            padding: 18px 24px;
        }

        .change-card .card-head h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #17365d;
        }

        .change-card .card-head p {
            margin: 6px 0 0;
            color: #5f7187;
        }

        .change-card .card-body {
            padding: 24px;
        }

        .section-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #17365d;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .flow-box {
            background: #f7fbff;
            border: 1px solid #deebf7;
            border-radius: 14px;
            padding: 16px;
            min-height: 126px;
            margin-bottom: 16px;
        }

        .flow-box h6 {
            margin: 0 0 8px;
            font-size: 16px;
            color: #17365d;
            font-weight: 700;
        }

        .flow-box p {
            margin: 0;
            color: #596d84;
            line-height: 1.7;
        }

        .form-group label {
            color: #17365d;
            font-weight: 700;
        }

        .hint-box {
            background: #fff7ed;
            border: 1px solid #fdba74;
            border-radius: 14px;
            padding: 14px 16px;
            color: #9a3412;
            margin-bottom: 20px;
        }

        .hint-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .select2-container {
            width: 100% !important;
        }

        @media (max-width: 767px) {
            .change-hero {
                padding: 20px;
            }

            .change-hero h4 {
                font-size: 22px;
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
                        <h4 class="page-title text-center">DF Change Control</h4>
                    </div>
                    <?php echo $this->session->flashdata('message'); ?>
                    <?php $this->load->view('df_change_control/_module_nav', array('module_nav' => $module_nav)); ?>
                </div>
            </div>

            <div class="change-hero">
                <h4>Raise Rework, Revision, Add-On, ECN or IOM</h4>
                <p>Select the DF, describe the change requirement, and choose the departments that need to act. Each selected department head will receive the request, define the turnaround days, and assign execution to the right team member. This creates a clear audit trail for management and links the rework back to the DF and Gantt view.</p>
            </div>

            <?php if (!$module_ready) { ?>
                <div class="alert alert-danger">
                    <strong>Migration pending:</strong> Please run <code><?php echo $migration_file; ?></code> before using this module.
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="change-card">
                        <div class="card-head">
                            <h5>Execution Flow</h5>
                            <p>The requester triggers the change, department head plans the work, the team member executes, and management gets full visibility.</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="flow-box">
                                        <h6>1. Requester Raises ECN / IOM</h6>
                                        <p>Pick the DF, define the type of change, upload the context, and select all impacted departments.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="flow-box">
                                        <h6>2. Department Head Plans Action</h6>
                                        <p>The notified HOD defines the completion days, target date, remarks, and assigns the action to the right team member.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="flow-box">
                                        <h6>3. Execution and Visibility</h6>
                                        <p>The assignee updates progress and completion. The change remains linked to the DF and is highlighted in the Gantt chart.</p>
                                    </div>
                                </div>
                            </div>

                            <?php
                                $selected_departments = $this->input->post('department_ids');
                                $selected_departments = is_array($selected_departments) ? $selected_departments : array();
                                $selected_df_value = set_value('df_id');
                                if ($selected_df_value === '' && !empty($selected_df_id)) {
                                    $selected_df_value = $selected_df_id;
                                }
                                $request_type_options = array('ECN', 'IOM');
                                $category_options = array('REWORK', 'ADDON', 'REVISION', 'CLIENT_CHANGE', 'DEPARTMENT_CHANGE', 'CORRECTION');
                                $priority_options = array('CRITICAL', 'HIGH', 'MEDIUM', 'NORMAL');
                                $source_options = array('CLIENT', 'OTHER_DEPARTMENT', 'MANAGEMENT', 'SITE_FEEDBACK', 'INTERNAL_TEAM');
                            ?>

                            <?php if ($module_ready) { ?>
                                <form method="post" action="<?php echo page_url; ?>Df_change_control/save" enctype="multipart/form-data">
                                    <div class="hint-box">
                                        <strong>Smart routing enabled</strong>
                                        The selected department heads will get email and system notifications automatically, and each action will be tracked department-wise on the DF.
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="section-label">Request Details</div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>DF No. <span style="color:red;">*</span></label>
                                                <select class="form-control select2" name="df_id" required>
                                                    <option value="">Select DF</option>
                                                    <?php foreach ($df_options as $row) { ?>
                                                        <option value="<?php echo $row['id']; ?>" <?php echo ((string)$selected_df_value === (string)$row['id']) ? 'selected' : ''; ?>>
                                                            <?php echo $row['df_no'] . ' - ' . $row['df_description']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <?php echo form_error('df_id'); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Type <span style="color:red;">*</span></label>
                                                <select class="form-control" name="request_type" required>
                                                    <option value="">Choose</option>
                                                    <?php foreach ($request_type_options as $option) { ?>
                                                        <option value="<?php echo $option; ?>" <?php echo set_select('request_type', $option); ?>><?php echo $option; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php echo form_error('request_type'); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Change Category <span style="color:red;">*</span></label>
                                                <select class="form-control" name="change_category" required>
                                                    <option value="">Choose</option>
                                                    <?php foreach ($category_options as $option) { ?>
                                                        <option value="<?php echo $option; ?>" <?php echo set_select('change_category', $option); ?>><?php echo str_replace('_', ' ', $option); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php echo form_error('change_category'); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Priority <span style="color:red;">*</span></label>
                                                <select class="form-control" name="priority" required>
                                                    <option value="">Choose</option>
                                                    <?php foreach ($priority_options as $option) { ?>
                                                        <option value="<?php echo $option; ?>" <?php echo set_select('priority', $option); ?>><?php echo $option; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php echo form_error('priority'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Source Of Change <span style="color:red;">*</span></label>
                                                <select class="form-control" name="source_of_change" required>
                                                    <option value="">Choose</option>
                                                    <?php foreach ($source_options as $option) { ?>
                                                        <option value="<?php echo $option; ?>" <?php echo set_select('source_of_change', $option); ?>><?php echo str_replace('_', ' ', $option); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php echo form_error('source_of_change'); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Client / Internal Ref. No.</label>
                                                <input type="text" class="form-control" name="reference_no" value="<?php echo set_value('reference_no'); ?>" placeholder="Optional reference number">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Revision No.</label>
                                                <input type="text" class="form-control" name="revision_no" value="<?php echo set_value('revision_no'); ?>" placeholder="Optional revision">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="section-label">Change Description</div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Change Title <span style="color:red;">*</span></label>
                                                <input type="text" class="form-control" name="title" value="<?php echo set_value('title'); ?>" required placeholder="Example: Rework in electrical routing after client add-on">
                                                <?php echo form_error('title'); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Supporting Attachment</label>
                                                <input type="file" class="form-control" name="attachment">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Change Summary <span style="color:red;">*</span></label>
                                                <textarea class="form-control" name="change_summary" rows="5" required placeholder="Describe the exact rework, add-on, revision, or change requirement."><?php echo set_value('change_summary'); ?></textarea>
                                                <?php echo form_error('change_summary'); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Impact / Expected Outcome</label>
                                                <textarea class="form-control" name="impact_note" rows="4" placeholder="Mention design impact, schedule impact, customer expectation, or any critical execution point."><?php echo set_value('impact_note'); ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="section-label">Department Notification</div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Notify Department(s) <span style="color:red;">*</span></label>
                                                <select class="form-control select2" multiple name="department_ids[]" required>
                                                    <?php foreach ($department_options as $department) { ?>
                                                        <option value="<?php echo $department['department_id']; ?>" <?php echo in_array($department['department_id'], $selected_departments) ? 'selected' : ''; ?>>
                                                            <?php echo strtoupper($department['department']); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <small class="text-muted">Every selected department head will receive this change request and define the execution plan.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top:10px;">
                                        <div class="col-md-12 text-right">
                                            <a href="<?php echo page_url; ?>Df_change_control" class="btn btn-default">Open Dashboard</a>
                                            <button type="submit" class="btn btn-primary">Create DF Change Control</button>
                                        </div>
                                    </div>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php $this->load->view('common/footer'); ?>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>
    <script src="<?php echo assets_url; ?>plugins/select2/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: 'Select value'
            });
        });
    </script>
</body>
</html>
