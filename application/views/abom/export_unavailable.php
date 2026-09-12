<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM — export unavailable</title>
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
    <h3>&#9888;&#65039; <?php echo abom_e($format); ?> export is unavailable on this server</h3>
    <p><?php echo abom_e($message); ?></p>
    <p style="margin-top:12px;">The other formats are unaffected:</p>
    <p style="margin-top:6px;">
      <a class="btn-sm btn-export" href="<?php echo page_url; ?>abom/export/csv/<?php echo (int) $bom->id; ?>">Export CSV</a>
      &nbsp;
      <a class="btn-sm btn-export" href="<?php echo page_url; ?>abom/export/pdf/<?php echo (int) $bom->id; ?>">Export PDF</a>
      &nbsp;
      <a class="btn-sm btn-print" href="<?php echo page_url; ?>abom/view/<?php echo (int) $bom->id; ?>">Back to the BOM</a>
    </p>
    </div>
</div>

<?php $this->load->view('common/footer'); ?>
</body>
</html>
