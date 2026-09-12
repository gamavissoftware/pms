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
    <link href="<?php echo abom_asset('abom/abom.css'); ?>" rel="stylesheet">
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
    <p style="margin-top:12px;">If the module has <strong>never</strong> been set up on this
      site, run <code>Database/abom_003_permissions.sql</code>, note the submodule ids it
      creates, then set them in <code>application/config/abom.php</code> under
      <code>$config['abom_submodule_ids']</code>.</p>
    <p style="margin-top:10px;">If it <strong>was</strong> working until a deployment, the ids
      were almost certainly overwritten — <code>application/config/abom.php</code> ships them
      as <code>null</code> and is easy to copy over by mistake. Do <em>not</em> re-run the
      permissions script; it will abort, because the submodule rows already exist. Read them
      back instead:</p>
    <p style="margin-top:6px;"><code>SELECT id, submodule FROM submodule WHERE submodule LIKE
      'AUTOMATION BOM %' ORDER BY id;</code></p>
    <?php
    /**
     * This paragraph used to read "Generating and viewing BOMs is
     * unaffected. Only the approval controls are withheld." That was
     * wrong, and misleading in the worst way: require_permissions_configured()
     * is called from require_perm() and require_any_perm(), which gate
     * EVERY screen in the module including generate(). Someone reading
     * the old line would have gone looking for a second, non-existent
     * fault. Corrected 2026-08-11, when it did exactly that.
     */
    ?>
    <p style="margin-top:10px;font-size:11px;">Every screen in this module is unavailable until
      this is set — generating, viewing and approving alike. No BOM data has been lost; this is
      a configuration gate, not a data problem.</p>
  </div>
</div>

<?php $this->load->view('common/footer'); ?>
</body>
</html>
