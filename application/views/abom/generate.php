<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM Generator</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>abom/abom.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>abom/abom-print.css" rel="stylesheet" media="all">
</head>
<body>

<header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
<?php $this->load->view('common/info-section.php'); ?>

<div class="abom-wrap">
  <?php $this->load->view('abom/_document'); ?>
</div>

<?php $this->load->view('common/footer'); ?>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script src="<?php echo assets_url; ?>abom/abom.js"></script>
<script src="<?php echo assets_url; ?>abom/abom-generate.js"></script>
<script>
window.ABOM_GENERATE_URL = "<?php echo page_url; ?>abom/generate_ajax";
window.ABOM_SAVE_URL     = "<?php echo page_url; ?>abom/save";
</script>
</body>
</html>
