<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * config_hub.php — the six configuration tables, with counts.
 *
 * Reached only through Abom::config(), which requires 'master_edit'.
 *
 * @var array  $entities  descriptor per entity
 * @var array  $counts    entity => ['total'=>int,'active'=>int]
 * @var string $message   flashdata
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM — Build Configuration</title>
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
<div class="abom-wrap">
  <header class="app-header">
    <a class="abom-home" href="<?php echo page_url; ?>abom/master" title="Master items">
      <span class="abom-home-icon" aria-hidden="true">&#8592;</span>
      <span class="abom-home-text">Master items</span>
    </a>
    <div>
      <h1>&#9881;&#65039; Build Configuration</h1>
      <div class="sub">Which build a machine gets, and how quantities are worked out</div>
    </div>
    <span class="badge review">MASTER DATA</span>
  </header>

  <div class="layout"><main class="main"><div class="abom-area">

    <?php if (!empty($message)): ?>
      <div class="abom-master-ok">&#10003; <?php echo abom_e($message); ?></div>
    <?php endif; ?>

    <div class="abom-master-note">
      <b>This decides which build a machine gets</b> — before the master items decide what
      that build contains. A wrong selection rule does not produce a visibly broken BOM;
      it produces a plausible one for the wrong machine. Changes affect every BOM generated
      from now on and none already saved.
    </div>

    <div class="abom-hub">
      <?php foreach ($entities as $key => $d): ?>
        <?php $c = isset($counts[$key]) ? $counts[$key] : array('total' => 0, 'active' => 0); ?>
        <a class="hub-card" href="<?php echo page_url; ?>abom/config_list/<?php echo abom_e($key); ?>">
          <span class="hc-title"><?php echo abom_e($d['plural']); ?></span>
          <span class="hc-count">
            <?php echo (int) $c['total']; ?>
            <?php if (!empty($d['active']) && $c['active'] !== $c['total']): ?>
              <em>(<?php echo (int) $c['active']; ?> active)</em>
            <?php endif; ?>
          </span>
          <span class="hc-blurb"><?php echo abom_e($d['blurb']); ?></span>
          <?php if (isset($d['addable']) && $d['addable'] === false): ?>
            <span class="formula-tag tag-review">EDIT ONLY</span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <p class="abom-rowedit-note">
      <b>To add a new machine build:</b> create the <b>Build variant</b>, add at least one
      <b>Build selection rule</b> that reaches it, then add its items on the
      <a href="<?php echo page_url; ?>abom/master">master items</a> screen. Sections are
      usually reused from the family the build belongs to.
    </p>

  </div></main></div>
</div>
<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>
</html>
