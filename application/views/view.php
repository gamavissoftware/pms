<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM <?php echo abom_e($bom->bom_no); ?></title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom.css'); ?>" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom-print.css'); ?>" rel="stylesheet" media="all">
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
<script>
window.ABOM_LINE_QTY_URL = "<?php echo page_url; ?>abom/save_line_qty";
window.ABOM_BOM_ID       = <?php echo (int) $bom->id; ?>;
window.ABOM_WF_ADVANCE_URL  = "<?php echo page_url; ?>abom/approve";
window.ABOM_WF_REJECT_URL   = "<?php echo page_url; ?>abom/reject";
window.ABOM_WF_REOPEN_URL   = "<?php echo page_url; ?>abom/reopen";
window.ABOM_WF_REVISION_URL = "<?php echo page_url; ?>abom/create_revision";
</script>
<script>
  window.ABOM_SAVE_LINES_URL  = "<?php echo page_url; ?>abom/save_lines";
  window.ABOM_SAVE_CONFIG_URL = "<?php echo page_url; ?>abom/save_config";
</script>
<script src="<?php echo abom_asset('abom/abom.js'); ?>"></script>
<script src="<?php echo abom_asset('abom/abom-lines.js'); ?>"></script>
<?php
// Loaded after abom-lines.js, which it asks about unsaved row edits.
// Only on a BOM whose configuration the server actually unlocked.
?>
<?php if (!empty($config_editable)): ?>
  <script>
    window.ABOM_CHECK_DFREF_URL = "<?php echo page_url; ?>abom/check_df_ref";
  </script>
  <?php // BEFORE abom-config.js, which asks it whether the reference clashes. ?>
  <script src="<?php echo abom_asset('abom/abom-dfref.js'); ?>"></script>
  <script src="<?php echo abom_asset('abom/abom-config.js'); ?>"></script>
<?php endif; ?>
</body>
</html>
