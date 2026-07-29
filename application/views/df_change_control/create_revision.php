<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?> DF Revision</title>

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

        .task-filter-wrap{
    display:flex;
    gap:12px;
}

.task-filter-box{
    min-width:130px;
    cursor:pointer;
    background:#f8fbff;
    border:2px solid #e2ebf5;
    border-radius:14px;
    padding:12px;
    text-align:center;
    transition:.3s;
}

.task-filter-box:hover{
    transform:translateY(-3px);
}

.task-filter-box.active{
    background:#17365d;
    border-color:#17365d;
}

.task-filter-box.active .task-filter-count,
.task-filter-box.active .task-filter-label{
    color:#fff;
}

.task-filter-count{
    display:block;
    font-size:24px;
    font-weight:700;
    color:#17365d;
}

.task-filter-label{
    display:block;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:1px;
    color:#666;
}

.task-row-completed{
    display:none;
}

.status-completed{
    background:#d4edda;
    color:#155724;
    padding:4px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:600;
}

.status-pending{
    background:#fff3cd;
    color:#856404;
    padding:4px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:600;
}
        .select2-container{
    width:100%!important;
}

/* Hero */

.revision-hero{
    background:linear-gradient(135deg,#17365d,#0f172a);
    color:#fff;
    border-radius:20px;
    padding:30px;
    margin-bottom:25px;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
}

.revision-hero h2{
    margin:0 0 10px;
    font-weight:700;
    color:#fff;
}

.revision-hero p{
    margin:0;
    opacity:.85;
}

.hero-stat{
    background:rgba(255,255,255,.12);
    border-radius:16px;
    padding:20px;
    text-align:center;
}

.hero-stat span{
    display:block;
    font-size:34px;
    font-weight:700;
}

.hero-stat small{
    color:#d7e4ff;
}

/* Search */

.search-card{
    background:#fff;
    border-radius:20px;
    padding:30px;
    margin-bottom:25px;
    border:1px solid #e8eef5;
    box-shadow:0 15px 40px rgba(23,54,93,.08);
}

.btn-search{
    height:60px;
    border:none;
    border-radius:14px;
    background:#17365d;
    color:#fff;
    font-weight:600;
}

.btn-search:hover{
    background:#0f2d50;
    color:#fff;
}

/* Select2 */

.search-card .select2-selection{
    height:60px!important;
    border:2px solid #dce6f2!important;
    border-radius:14px!important;
    background:#f8fbff!important;
}

.search-card .select2-selection__rendered{
    line-height:56px!important;
    color:#17365d!important;
    font-weight:600;
}

.search-card .select2-selection__arrow{
    height:58px!important;
}

.select2-dropdown{
    border-radius:14px!important;
    overflow:hidden;
    border:1px solid #dce6f2!important;
    box-shadow:0 15px 30px rgba(0,0,0,.10);
}

.select2-results__option--highlighted{
    background:#17365d!important;
}

/* DF Header */

.df-header-card{
    background:linear-gradient(135deg,#17365d,#0f172a);
    border-radius:18px;
    padding:22px 25px;
    margin-bottom:20px;
    color:#fff;
    box-shadow:0 12px 30px rgba(23,54,93,.25);
}

.df-title-row{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:10px;
}

.df-title-row h3{
    margin:0;
    color:#fff!important;
    font-weight:700;
}

.df-tag{
    background:rgba(255,255,255,.15);
    color:#fff;
    padding:6px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}

.df-description{
    color:rgba(255,255,255,.9);
    margin-bottom:12px;
    line-height:22px;
}

.df-meta-row{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    font-size:13px;
}

.df-meta-row,
.df-meta-row span,
.df-meta-row strong,
.df-meta-row i{
    color:#fff!important;
}

.btn-revision-action{
    background:#ff9800;
    color:#fff;
    border:none;
    border-radius:30px;
    padding:10px 20px;
    font-weight:600;
}

.btn-revision-action:hover{
    background:#f57c00;
    color:#fff;
}

/* Workflow Card */

.task-overview-card{
    background:#fff;
    border-radius:20px;
    padding:25px;
    margin-bottom:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.task-overview-card h3{
    margin:0;
    color:#17365d;
    font-weight:700;
}

.task-overview-card p{
    margin:8px 0 0;
    color:#777;
}

.btn-workflow{
    background:#17365d;
    color:#fff!important;
    border:none;
    border-radius:50px;
    padding:12px 25px;
}

.workflow-count{
    background:#fff;
    color:#17365d;
    padding:4px 10px;
    border-radius:20px;
    margin-left:8px;
    font-weight:700;
}

/* Task Table */

.task-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    border:1px solid #e6edf5;
    box-shadow:0 8px 24px rgba(0,0,0,.05);
}

.task-card-header{
    padding:18px 25px;
    background:#f8fbff;
    border-bottom:1px solid #e6edf5;
}

.task-card-header h4{
    margin:0;
    color:#17365d;
    font-weight:700;
}

.table > thead > tr{
    background:#17365d;
}

.table > thead > tr > th{
    color:#fff!important;
    border:none!important;
    padding:12px!important;
}

.table tbody tr:hover{
    background:#f8fbff;
}

/* Mobile */

@media(max-width:767px){

    .df-title-row{
        flex-direction:column;
        align-items:flex-start;
    }

    .df-meta-row{
        gap:10px;
        display:block;
    }

    .btn-revision-action{
        margin-top:15px;
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
           <div class="revision-hero">

    <div class="row">

        <div class="col-md-8">

            <h2 style="color:white !important;">
                <i class="fa fa-code-fork"></i>
                DF Revision Control
            </h2>

            <p>
                Search an existing DF, review all linked tasks,
                analyze dependencies and raise a controlled revision.
            </p>

        </div>

        <div class="col-md-4 text-right">

            <div class="hero-stat">

                <span id="hero_df_count">
                    <?php echo count($df_list); ?>
                </span>

                <small>Available DF's</small>

            </div>

        </div>

    </div>

</div>

            

         
<div class="search-card">

    <div class="row">

        <div class="col-md-12 text-center">

            <h3 style="margin-top:0;">
                Find Design File
            </h3>

            <p class="text-muted">
                Search any released DF and review all linked activities.
            </p>

        </div>

    </div>

    <div class="row">

        <div class="col-md-9">


    <select class="form-control select2" id="df_id">

        <option value="">
            Search DF Number, Description, Client...
        </option>

        <?php foreach($df_list as $df){ ?>

        <option value="<?php echo $df['id']; ?>">

            DF-<?php echo $df['df_sr_no']; ?>
            | <?php echo substr($df['df_description'],0,50); ?>

        </option>

        <?php } ?>

    </select>

</div>

        <div class="col-md-3">

            <button class="btn btn-search btn-block"
                    id="btnSearchDf">

                <i class="fa fa-search"></i>

                Search DF

            </button>

        </div>

    </div>

</div>
</form>

<hr>

<div id="dfDetailsContainer" style="display:none;">

    <div class="df-header-card">

        <div class="row">

            <div class="col-md-9">

                <div class="df-title-row">

                    <span class="df-tag" id="show_df_sr_no">
                        DF-0000
                    </span>

                    <h3 id="show_df_no"></h3>

                </div>

                <div class="df-description">
                    <span id="show_df_description"></span>
                </div>

                <div class="df-meta-row">

                    <span>
                        <i class="fa fa-user"></i>
                        <strong>Created By:</strong>
                        <span id="show_added_by"></span>
                    </span>

                    <span>
                        <i class="fa fa-calendar"></i>
                        <strong>Created On:</strong>
                        <span id="show_added_on"></span>
                    </span>

                    <span>
                        <i class="fa fa-tasks"></i>
                        <strong>Total Tasks:</strong>
                        <span id="show_total_tasks">0</span>
                    </span>

                </div>

            </div>

            <div class="col-md-3 text-right">

                <button type="button"
                        id="btnReviseDf"
                        class="btn btn-revision-action">

                    <i class="fa fa-code-fork"></i>
                    Create Revision

                </button>

            </div>

        </div>

    </div>

<div class="text-center" style="margin-top:20px;">

   <div class="task-overview-card">

    <div class="row">

        <div class="col-md-8">

            <h3>
                <i class="fa fa-sitemap"></i>
                Project Workflow
            </h3>

            <p>
                Review all scheduled tasks, ownership and completion status.
            </p>

        </div>

        <div class="col-md-4 text-right">

            <button class="btn btn-workflow"
                    data-toggle="collapse"
                    data-target="#taskAccordion" style="color:white !important;">

                <i class="fa fa-chevron-down"></i>

                Show Workflow

                <span class="workflow-count"
                      id="taskCountBtn">
                    0
                </span>

            </button>

        </div>

    </div>

</div>

</div>

<br>

<div id="taskAccordion" class="collapse">

   <div class="task-card">

    <div class="task-card-header">

        <div class="row">

            <div class="col-md-6">
                <h4>
                    DF Task Schedule
                </h4>
            </div>

            <div class="col-md-6">

                <div class="task-filter-wrap pull-right">

                    <div class="task-filter-box active"
                         data-type="completed">

                        <span class="task-filter-count"
                              id="completedCount">
                            0
                        </span>

                        <span class="task-filter-label">
                            Completed
                        </span>

                    </div>

                    <div class="task-filter-box"
                         data-type="pending">

                        <span class="task-filter-count"
                              id="pendingCount">
                            0
                        </span>

                        <span class="task-filter-label">
                            Pending
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="table-responsive">

        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Department</th>
                    <th>Assigned To</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody id="taskBody">
            </tbody>

        </table>

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
      $(document).ready(function(){

    $('#df_id').select2({
        placeholder: 'Type DF No, Description or Client Name',
        allowClear: true
    });

});

      $(document).on('click','#btnSearchDf',function(){

    var df_id = $('#df_id').val();

    if(df_id=='')
    {
        alert('Please Select DF');
        return false;
    }

    $('#btnSearchDf').html(
        '<i class="fa fa-spinner fa-spin"></i> Loading'
    );

    $.ajax({

        url:'<?php echo page_url;?>DF_revision/ajax_get_df_details',

        type:'POST',

        data:{
            df_id:df_id
        },

        dataType:'json',

       success:function(res){

        $('#btnReviseDf').attr(
    'data-df-id',
    res.df.id
);

    $('#btnSearchDf').html(
        '<i class="fa fa-search"></i> Search DF'
    );

    if(res.status==1)
    {
        $('#dfDetailsContainer').fadeIn();

        $('#show_df_no').html(res.df.df_no);

        $('#show_df_sr_no').html(
            'DF-'+res.df.df_sr_no
        );

        $('#show_added_by').html(
            res.df.added_by_name
        );

        $('#show_added_on').html(
            res.df.added_on
        );

        $('#show_df_description').html(
            res.df.df_description
        );

        $('#show_total_tasks').html(
            res.task_count
        );

        $('#taskCountBtn').html(
            res.task_count
        );

        $('#completedCount').html(
            res.completed_count
        );

        $('#pendingCount').html(
            res.pending_count
        );

        $('#taskBody').html(
            res.task_html
        );

        $('.task-filter-box').removeClass('active');

        $('.task-filter-box[data-type="completed"]')
        .addClass('active');

        $('.task-row').hide();

        $('.task-row-completed').show();

        $('#taskAccordion').removeClass('in');
    }

}

    });

});

      $(document).on('click','.task-filter-box',function(){

    var type = $(this).attr('data-type');

    $('.task-filter-box').removeClass('active');

    $(this).addClass('active');

    if(type=='completed')
    {
        $('.task-row').hide();
        $('.task-row-completed').show();
    }
    else
    {
        $('.task-row').hide();
        $('.task-row-pending').show();
    }


});

          $(document).on('click','#btnReviseDf',function(){

    var df_id=$(this).attr('data-df-id');
    

    window.location.href=
    '<?php echo page_url;?>DF_revision/create_revision_iom/'
    +df_id;

});
  </script>
</body>
</html>
