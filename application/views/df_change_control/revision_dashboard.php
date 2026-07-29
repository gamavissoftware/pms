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
                DF Revision Control Dashoard
            </h2>

            <p>
                Search an existing DF, review all linked tasks,
                analyze dependencies and raise a controlled revision.
            </p>

        </div>

      
    </div>

</div>

            

         
<div class="task-card">

    <div class="task-card-header">

        <div class="row">

            <div class="col-md-8">

                <h4>
                    DF Revision Register
                </h4>

            </div>

            <div class="col-md-4 text-right">

    <a href="<?php echo page_url; ?>DF_revision/create_revision"
       class="btn btn-success"
       style="
       border-radius:30px;
       padding:10px 18px;
       margin-right:10px;
       font-weight:600;">

        <i class="fa fa-plus-circle"></i>
        Create DF Revision

    </a>

    <span class="badge"
          style="
          font-size:16px;
          padding:10px 15px;
          background:#17365d;">

        Total :
        <?php echo $total_revision; ?>

    </span>

</div>

        </div>

    </div>

    <div class="table-responsive">

        <table class="table table-bordered table-hover"
       id="revisionTable">

    <thead>
        <tr>
            <th>#</th>
            <th>Reference No</th>
            <th>Revision No</th>
            <th>DF</th>
            <th>Title</th>
            <th>Priority</th>
            <th>Archived Tasks</th>
            <th>Raised By</th>
            <th>Created On</th>
            <th>Status</th>
            <!-- <th>Action</th> -->
        </tr>
    </thead>

</table>

    </div>

</div>

</form>




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

    <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">

<script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>

  <script>
   $(document).ready(function(){

    $('#revisionTable').DataTable({

        processing:true,

        pageLength:25,

        order:[[0,'desc']],

        ajax:{
            url:'<?php echo page_url;?>DF_revision/ajax_revision_list',
            type:'POST',
            dataSrc:'data'
        },

        columnDefs:[
            {
                targets:[3,9],
                orderable:false
            }
        ]

    });

});
  </script>
</body>
</html>
