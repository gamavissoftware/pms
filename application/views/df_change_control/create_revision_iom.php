<?php 
$revision_count = $this->db
->where('df_id',$df['id'])
->where('change_category','REVISION')
->count_all_results('df_revision_control');
$revision_no =
    $df['df_no'].'_V'.($revision_count+1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> DF Revision IOM</title>

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

        #revisionLoader{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(255,255,255,.95);
    z-index:99999;
    display:none;
}

.loader-box{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    text-align:center;
}

.loader-spinner{
    width:70px;
    height:70px;
    border:6px solid #e5e7eb;
    border-top:6px solid #17365d;
    border-radius:50%;
    animation:spin 1s linear infinite;
    margin:auto;
}

.loader-title{
    margin-top:20px;
    font-size:24px;
    font-weight:700;
    color:#17365d;
}

.loader-text{
    margin-top:10px;
    font-size:15px;
    color:#666;
}

@keyframes spin{
    100%{
        transform:rotate(360deg);
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
                        <h4 class="page-title text-center">DF Revision IOM</h4>
                    </div>
                    <?php echo $this->session->flashdata('message'); ?>
                   
                </div>
            </div>

            <div class="change-hero">
                <h4 style="color:white !important;">Raise DF Revision IOM</h4>
             
            </div>

          
            <div class="row">
                <div class="col-md-12">
                    <div class="change-card">
                       
                        <div class="card-body">
                        

                            <?php 
                                $selected_departments = $this->input->post('department_ids');
                                $selected_departments = is_array($selected_departments) ? $selected_departments : array();
                                $selected_df_value = set_value('df_id');
                                if ($selected_df_value === '' && !empty($selected_df_id)) {
                                    $selected_df_value = $selected_df_id;
                                }
                                $request_type_options = array('IOM');
                                $category_options = array('REVISION');
                                $priority_options = array('CRITICAL', 'HIGH', 'MEDIUM', 'NORMAL');
                                $source_options = array('CLIENT', 'OTHER_DEPARTMENT', 'MANAGEMENT', 'SITE_FEEDBACK', 'INTERNAL_TEAM');
                            ?>

                           
                                <form id="revisionForm"
      method="post"
      action="<?php echo page_url; ?>DF_revision/save_df_revision"
      enctype="multipart/form-data">
                                    <div class="hint-box">
                                        <strong>Smart routing enabled</strong>
                                        All Department heads will get email and system notifications automatically, and each action will be tracked department-wise on the DF.
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="section-label">Request Details</div>
                                        </div>

                                        <div class="col-md-4">

                                        <div class="form-group">

                                        <label>DF No.</label>

                                        <input type="text"
                                        class="form-control"
                                        readonly
                                        value="<?php echo $df['df_no']; ?>">

                                        <input type="hidden"
                                        name="df_id"
                                        value="<?php echo $df['id']; ?>">

                                        </div>

                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Type <span style="color:red;">*</span></label>
                                                <select class="form-control" name="request_type" required>
                                                    <!-- <option value="">Choose</option> -->
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
                                                    <!-- <option value="">Choose</option> -->
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
                                                <input type="text" class="form-control" name="revision_no" value="<?php echo $revision_no;?>" placeholder="Revision No." readonly>
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
        <div class="section-label">
            Performa Impact
        </div>
    </div>

    <div class="col-md-6">

        <div class="form-group">

            <label>
                Changes Required In Performa ?
            </label>

            <div style="
                padding:15px;
                background:#f8fbff;
                border:1px solid #dbe7f3;
                border-radius:12px;">

                <label class="radio-inline">
                    <input type="radio"
                           name="performa_change_required"
                           value="0"
                           checked>

                    No
                </label>

                <label class="radio-inline"
                       style="margin-left:20px;">

                    <input type="radio"
                           name="performa_change_required"
                           value="1">

                    Yes
                </label>

            </div>

            <small class="text-muted">
                Select Yes if quotation / PI / commercial values need revision due to this DF revision.
            </small>

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
                                                <!-- <select class="form-control select2" multiple name="department_ids[]" required> -->
                                                <select class="form-control" name="department_ids[]" required>
                                                    <option value="ALL">
                                                    ALL DEPARTMENTS
                                                    </option>
                                                   <!--  <?php foreach ($department_options as $department) { ?>
                                                        <option value="<?php echo $department['department_id']; ?>" <?php echo in_array($department['department_id'], $selected_departments) ? 'selected' : ''; ?>>
                                                            <?php echo strtoupper($department['department']); ?>
                                                        </option>
                                                    <?php } ?> -->
                                                </select>
                                                <small class="text-muted">Every  department head will receive this change request and define the execution plan.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top:10px;">
                                        <div class="col-md-12 text-right">
                                            <!-- <a href="<?php echo page_url; ?>Df_change_control" class="btn btn-default">Open Dashboard</a> -->
                                            <button type="submit"
        id="btnSubmitRevision"
        class="btn btn-primary">Create DF Revsion Request</button>
                                        </div>
                                    </div>
                                </form>
                            
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

    <div id="revisionLoader">

    <div class="loader-box">

        <div class="loader-spinner"></div>

        <div class="loader-title">
            Saving Revision...
        </div>

        <div class="loader-text" id="loaderMessage">
            Validating Request
        </div>

    </div>

</div>

<script>
    $('#revisionForm').on('submit',function(e){

    var title = $.trim($('[name="title"]').val());
    var summary = $.trim($('[name="change_summary"]').val());
    var priority = $('[name="priority"]').val();
    var source = $('[name="source_of_change"]').val();

    if(title=='')
    {
        return true;
    }

    if(summary=='')
    {
        return true;
    }

    if(priority=='')
    {
        return true;
    }

    if(source=='')
    {
        return true;
    }

    $('#btnSubmitRevision')
        .prop('disabled',true)
        .html(
            '<i class="fa fa-spinner fa-spin"></i> Processing'
        );

    $('#revisionLoader').show();

    var messages = [
        'Validating Revision Request',
        'Saving Revision Data',
        'Creating Revision Record',
        'Notifying Department Heads',
        'Sending Email Notifications',
        'Finalizing Request'
    ];

    var counter = 0;

    var loaderInterval = setInterval(function(){

        if(counter < messages.length)
        {
            $('#loaderMessage').html(
                messages[counter]
            );

            counter++;
        }

    },1200);

});
</script>
</body>
</html>
