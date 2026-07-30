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

<?php
/**
 * The application chrome (common/nav-menu, common/info-section) is
 * DELIBERATELY NOT LOADED on this module's screens.
 *
 * Two reasons:
 *   1. nav-menu renders a fixed left sidebar, and the theme offsets the
 *      content for it via `<div class="wrapper">`. This module's document
 *      layout is not a .wrapper page — it is a full-bleed sheet — so the
 *      sidebar overlapped the BOM table.
 *   2. A BOM is a wide document: 9 columns plus editable quantities. The
 *      ~250px the sidebar costs is the difference between the table
 *      fitting and scrolling horizontally.
 *
 * common/nav-menu.php itself is UNCHANGED and still carries this module's
 * menu entry, so the module is reached from any other page as normal.
 * Navigation back out is the home button in .app-header.
 *
 * info-section.php is not loaded either: its entire body is wrapped in an
 * HTML comment, so it renders nothing while still running a query
 * against system_reports.
 */
?>

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
