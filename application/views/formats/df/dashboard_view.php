<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<title>DF Engineering Dashboard | <?php echo sitetitle; ?></title>

<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

<link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/core.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/components.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet">
<link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet">

<script src="<?php echo assets_url;?>js/jquery.min.js"></script>

<?php
$q = $this->db->select('company_name,logo,colorcode')
->from('company_information')
->get();

foreach($q->result() as $LOGO);
?>

<style>
.df-card{
    border:1px solid #ddd;
    border-radius:6px;
    margin-bottom:20px;
    background:#fff;
}
.df-head{
    background:<?php echo $LOGO->colorcode;?>;
    color:#fff;
    padding:8px;
    font-weight:bold;
}
.df-body{
    padding:12px;
}
.df-table th{
    background:#f2f2f2;
}
</style>

</head>

<body>

<header id="topnav">
<?php $this->load->view('common/nav-menu'); ?>
</header>

<?php $this->load->view('common/info-section.php'); ?>

<div class="wrapper">
<div class="container">

<?php $d = $dashboard; ?>

<!-- ================= DF MASTER ================= -->

<div class="df-card">
<div class="df-head">DF MASTER DETAILS</div>
<div class="df-body">

<table class="table table-bordered df-table">

<tr>
<th width="30%">DF No</th>
<td><?= $d['df']->design_form_name ?? '' ?></td>
</tr>

<tr>
<th>IOM No</th>
<td><?= $d['df']->iom_no ?? '' ?></td>
</tr>

<tr>
<th>Invoice No</th>
<td><?= $d['df']->invoice_no ?? '' ?></td>
</tr>

<tr>
<th>Dispatch Date</th>
<td><?= $d['df']->dispatch_date ?? '' ?></td>
</tr>

<tr>
<th>Trial Date</th>
<td><?= $d['df']->trial_date ?? '' ?></td>
</tr>

</table>

</div>
</div>

<!-- ================= MACHINE SPEC ================= -->

<div class="df-card">
<div class="df-head">MACHINE SPECIFICATION SNAPSHOT</div>
<div class="df-body">

<table class="table table-bordered df-table">

<tr>
<th>No Of Tracks</th>
<td><?= $d['spec']->tracks ?? '' ?></td>
</tr>

<tr>
<th>Product Packed</th>
<td><?= $d['spec']->product_packed ?? '' ?></td>
</tr>

<tr>
<th>Type Of Filling Unit</th>
<td><?= $d['spec']->filling_unit ?? '' ?></td>
</tr>

<tr>
<th>Pouch Size</th>
<td><?= $d['spec']->pouch_size ?? '' ?></td>
</tr>

<tr>
<th>Quantity</th>
<td><?= $d['spec']->quantity_packed ?? '' ?></td>
</tr>

<tr>
<th>Profile Of Sealing</th>
<td><?= $d['spec']->profile_of_sealing ?? '' ?></td>
</tr>

<tr>
<th>Working Speed</th>
<td><?= $d['spec']->working_speed ?? '' ?></td>
</tr>

</table>

</div>
</div>

<!-- ================= MULTI TRACK ================= -->

<div class="df-card">
<div class="df-head">MULTI TRACK MACHINE</div>
<div class="df-body">

<table class="table table-bordered df-table">

<tr>
<th>Horizontal Sealer Width</th>
<td><?= $d['multi']->horizontal_sealer_width ?? '' ?></td>
</tr>

<tr>
<th>Vertical Sealer Width</th>
<td><?= $d['multi']->vertical_sealer_width ?? '' ?></td>
</tr>

<tr>
<th>Supply Voltage</th>
<td><?= $d['multi']->supply_voltage ?? '' ?></td>
</tr>

<tr>
<th>PLC Maker</th>
<td><?= $d['multi']->plc_maker ?? '' ?></td>
</tr>

<tr>
<th>HMI Size</th>
<td><?= $d['multi']->hmi_size ?? '' ?></td>
</tr>

</table>

</div>
</div>

<!-- ================= SPECIAL NOTES ================= -->

<div class="df-card">
<div class="df-head">SPECIAL NOTES</div>
<div class="df-body">

<?= $d['notes']; ?>

</div>
</div>

</div>
</div>

<?php $this->load->view('common/footer'); ?>

<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>

</body>
</html>
