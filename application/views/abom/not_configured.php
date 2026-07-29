<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM — configuration</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>abom/abom.css" rel="stylesheet">
</head>
<body>

<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<?php $this->load->view('common/info-section.php'); ?>

<div class="abom-wrap">
  <div class="abom-guard">
    <h3>&#9888;&#65039; Automation BOM permissions are not configured</h3>
    <p>The module is installed, but its ACL wiring has not been completed. This is a
      <strong>configuration</strong> problem, not a permissions one — nobody is being denied
      access; the module does not yet know which submodule ids to check.</p>
    <?php foreach ($problems as $problem): ?>
      <p style="margin-top:10px;">&bull; <?php echo abom_e($problem); ?></p>
    <?php endforeach; ?>
    <p style="margin-top:12px;">Run <code>Database/abom_003_permissions.sql</code>, note the
      submodule ids it creates, then set them in
      <code>application/config/abom.php</code> under
      <code>$config['abom_submodule_ids']</code>.</p>
    <p style="margin-top:10px;font-size:11px;">Generating and viewing BOMs is unaffected.
      Only the approval controls are withheld.</p>
  </div>
</div>

<?php $this->load->view('common/footer'); ?>
</body>
</html>
