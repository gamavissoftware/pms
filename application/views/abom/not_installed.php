<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom.css'); ?>" rel="stylesheet">
</head>
<body>

<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<?php $this->load->view('common/info-section.php'); ?>

<div class="abom-wrap">
  <div class="abom-guard">
    <h3>&#9888;&#65039; Automation BOM tables are not installed yet</h3>
    <p>The module is deployed but its database tables have not been created. Nothing else in
      the application is affected — this module simply has nowhere to read from.</p>
    <p style="margin-top:10px;">First missing table: <code><?php echo abom_e($missing); ?></code></p>
    <p style="margin-top:10px;">Run these two files against the database, in order:</p>
    <p style="margin-top:6px;">
      <code>Database/abom_001.sql</code> &nbsp;then&nbsp; <code>Database/abom_002_seed.sql</code>
    </p>
    <p style="margin-top:10px;font-size:11px;">Both scripts only ever create or populate
      <code>abom_*</code> tables. They do not alter, drop or truncate anything else.</p>
  </div>
</div>

<?php $this->load->view('common/footer'); ?>
</body>
</html>
