<?php 

$user_id =$this->session->userdata['logged_in']['user_id'];

/* =========================================
   PERMISSION LOGIC
========================================= */

$show_service = 0;
$show_spare   = 0;

/* SPARE ACCESS */
$submoduleid = array('52');

$qry = $this->db
->select('role_id')
->from('module_capablity')
->where('role_id', $user_id)
->where('moduleid', '17')
->where_in('submoduleid', $submoduleid)
->where('submodule_access', '1')
->get();

if ($qry->num_rows() > 0) {
    $show_spare = 1;
}

/* SERVICE ACCESS */
$submoduleid = array('62');

$qry = $this->db
->select('role_id')
->from('module_capablity')
->where('role_id', $user_id)
->where('moduleid', '17')
->where_in('submoduleid', $submoduleid)
->where('submodule_access', '1')
->get();

if ($qry->num_rows() > 0) {
    $show_service = 1;
}

/* =========================================
   DYNAMIC COLUMN ALIGNMENT
========================================= */

$col_class = 'col-md-6';

if(
    ($show_service == 1 && $show_spare == 0) || 
    ($show_service == 0 && $show_spare == 1)
){
    $col_class = 'col-md-4 col-md-offset-4';
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Service & Spare Dashboard</title>

<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

<link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/core.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/components.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>

body{
    background:#f4f7fb;
}

/* =========================================
   EQUAL HEIGHT ROW
========================================= */

.row.equal-height{
    display:flex;
    flex-wrap:wrap;
}

.row.equal-height > [class*='col-']{
    display:flex;
    margin-bottom:25px;
}

/* =========================================
   HEADER
========================================= */

.main-heading{
    font-size:28px;
    font-weight:700;
    margin-bottom:5px;
    color:#222;
}

.sub-heading{
    color:#777;
    margin-bottom:30px;
}

/* =========================================
   CARD
========================================= */

.dashboard-card{
    background:#fff;
    border-radius:16px;
    padding:22px;
    box-shadow:0 3px 15px rgba(0,0,0,0.08);
    width:100%;
    display:flex;
    flex-direction:column;
}

.dashboard-card.service{
    border-top:4px solid #007bff;
}

.dashboard-card.spare{
    border-top:4px solid #28a745;
}

/* =========================================
   ICON
========================================= */

.card-icon{
    width:65px;
    height:65px;
    border-radius:50%;
    background:#f5f5f5;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:15px;
}

.card-icon i{
    font-size:28px;
}

.service .card-icon i{
    color:#007bff;
}

.spare .card-icon i{
    color:#28a745;
}

/* =========================================
   TEXT
========================================= */

.dashboard-title{
    font-size:24px;
    font-weight:700;
    margin-bottom:6px;
}

.dashboard-desc{
    color:#777;
    margin-bottom:20px;
    font-size:14px;
}

/* =========================================
   BUTTONS
========================================= */

.quick-btn{
    width:100%;
    text-align:left;
    border-radius:10px;
    padding:11px 15px;
    margin-bottom:10px;
    font-size:14px;
    font-weight:600;
    border:none;
    transition:0.3s;
}

.quick-btn i{
    margin-right:10px;
}

.primary-btn{
    background:#1e88e5;
    color:#fff;
}

.primary-btn:hover{
    background:#1565c0;
    color:#fff;
}

.action-btn{
    background:#f5f7fa;
    color:#333;
}

.action-btn:hover{
    background:#e9eef5;
}

/* =========================================
   MOBILE
========================================= */

@media(max-width:767px){

    .row.equal-height{
        display:block;
    }

    .row.equal-height > [class*='col-']{
        display:block;
    }

    .main-heading{
        font-size:22px;
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

    <div class="row" style="margin-top:25px;">

        <div class="col-md-12 text-center">

            <div class="main-heading">
                Service & Spare Dashboard
            </div>

            <div class="sub-heading">
                Quick access panel for all operations
            </div>

        </div>

    </div>

    <div class="row equal-height">

        <!-- =========================================
             SERVICE CARD
        ========================================== -->
        <?php if($show_service == 1){ ?>

        <div class="<?php echo $col_class; ?>">

            <div class="dashboard-card service">

                <div class="card-icon">
                    <i class="fa fa-cogs"></i>
                </div>

                <div class="dashboard-title">
                    Service Dashboard
                </div>

                <div class="dashboard-desc">
                    Manage service opportunities and followups.
                </div>

                <a href="<?php echo page_url;?>ServiceLeads/dashboard">
                    <button class="quick-btn primary-btn">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </button>
                </a>

                <a href="<?php echo page_url;?>ServiceMaster">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-gears"></i> Service Masters
                    </button>
                </a>

                <a href="<?php echo page_url;?>ServiceLeads/create">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-plus-circle"></i> New Opportunities
                    </button>
                </a>

                <a href="<?php echo page_url;?>ServiceLeads/opportunity_list?filter=today">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-calendar-check-o"></i> Today's Followup
                    </button>
                </a>

                <a href="<?php echo page_url;?>ServiceLeads/opportunity_list?filter=missed">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-warning"></i> Missed Followup
                    </button>
                </a>

                <a href="<?php echo page_url;?>ServiceLeads/opportunity_list?filter=upcoming">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-clock-o"></i> Upcoming Followup
                    </button>
                </a>

            </div>

        </div>

        <?php } ?>


        <!-- =========================================
             SPARE CARD
        ========================================== -->
        <?php if($show_spare == 1){ ?>

        <div class="<?php echo $col_class; ?>">

            <div class="dashboard-card spare">

                <div class="card-icon">
                    <i class="fa fa-wrench"></i>
                </div>

                <div class="dashboard-title">
                    Spare Dashboard
                </div>

                <div class="dashboard-desc">
                    Manage spare opportunities and followups.
                </div>

                <a href="<?php echo page_url;?>Dashboard/sparesdashboard">
                    <button class="quick-btn primary-btn">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </button>
                </a>

                <a href="<?php echo page_url;?>Spares/leadform">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-plus-circle"></i> New Opportunities
                    </button>
                </a>

                <a href="<?php echo page_url;?>Spares/opportunity_list">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-list"></i> All Opportunities
                    </button>
                </a>

                <a href="<?php echo page_url;?>Spares/followup_list/1">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-calendar-check-o"></i> Today's Followup
                    </button>
                </a>

                <a href="<?php echo page_url;?>Spares/followup_list/2">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-warning"></i> Missed Followup
                    </button>
                </a>

                <a href="<?php echo page_url;?>Spares/followup_list/3">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-clock-o"></i> Upcoming Followup
                    </button>
                </a>

                <a href="<?php echo page_url;?>Spares/running_orders_list">
                    <button class="quick-btn action-btn">
                        <i class="fa fa-refresh"></i> Running Orders
                    </button>
                </a>

            </div>

        </div>

        <?php } ?>

    </div>

</div>
</div>

<?php $this->load->view('common/footer'); ?>

<script src="<?php echo assets_url;?>js/jquery.min.js"></script>
<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

</body>
</html>
