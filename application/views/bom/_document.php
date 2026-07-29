<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _document.php — the BOM document body. SHARED by view.php and
 * generate.php so the two screens cannot drift apart.
 *
 * Assembles, in the order the approved design document uses them:
 *   app-header banner, config chips + actions, doc-title strip, stats
 *   row, review banner, section pills, the table, the sign-off block.
 *
 * @var object $bom
 * @var array  $lines
 * @var array  $sections
 * @var array  $stats
 * @var bool   $editable
 */

$editable = isset($editable) ? (bool) $editable : false;
?>

<header class="app-header">
  <div>
    <h1>&#9881;&#65039; Automation BOM Generator</h1>
    <div class="sub">
      <?php echo abom_e($bom->machine_model); ?> &middot; Mitsubishi Automation
      <?php if (!empty($bom->df_ref)): ?>
        &middot; Reference <?php echo abom_e($bom->df_ref); ?>
      <?php endif; ?>
    </div>
  </div>
  <?php if ($editable): ?>
    <span class="badge">GENERATOR</span>
  <?php else: ?>
    <span class="badge review">FOR CUSTOMER REVIEW &amp; APPROVAL</span>
  <?php endif; ?>
  <?php if (!empty($overridden)): ?>
    <span class="badge overridden">PLC FAMILY OVERRIDDEN</span>
  <?php endif; ?>
</header>

<div class="layout">

  <?php $this->load->view('bom/_config_panel'); ?>

  <main class="main">

    <div class="bom-topbar" id="configBar">
      <span id="chipRow" style="display:contents;">
        <span class="config-chip"><?php echo abom_e($bom->bom_no); ?><?php echo $bom->revision !== '' ? ' &middot; REV.' . abom_e($bom->revision) : ''; ?></span>
        <span class="config-chip lite"><?php echo abom_e($bom->machine_model); ?></span>
        <span class="config-chip lite"><?php echo (int) $bom->axes; ?> Axis</span>
        <span class="config-chip lite"><?php echo (int) $bom->tracks; ?> Track</span>
        <span class="config-chip lite"><?php echo (int) $bom->speed_ppm; ?> PPM</span>
        <?php if (!empty($bom->machine_side) && $bom->machine_side !== 'N/A'): ?>
          <span class="config-chip lite"><?php echo abom_e($bom->machine_side); ?></span>
        <?php endif; ?>
        <span class="config-chip cont"><?php echo abom_e($bom->motion_type); ?></span>
        <span class="config-chip <?php echo abom_chip_class($family_code); ?>">
          <?php echo abom_e($family_code === 'FX5' ? 'FX5 Series' : 'iQ-R Series'); ?>
        </span>
        <span class="config-chip lite"><?php echo abom_e($panel_location); ?></span>
        <?php if (!empty($bom->status)): ?>
          <span class="config-chip lite"><?php echo abom_e(abom_status_label($bom->status)); ?></span>
        <?php endif; ?>
      </span>
      <div class="bom-actions">
        <?php if ($editable): ?>
          <button type="button" class="btn-sm btn-generate" id="btnGenerate">&#8635; Recalculate</button>
        <?php endif; ?>
        <button type="button" class="btn-sm btn-print" onclick="window.print()">&#128424;&#65039; Print</button>
        <?php if (!empty($bom->id)): ?>
          <a class="btn-sm btn-export" href="<?php echo page_url; ?>abom/export/csv/<?php echo (int) $bom->id; ?>">&#128229; Export CSV</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="bom-area">
      <div id="bomContent">

        <?php $this->load->view('bom/_doc_header'); ?>

        <div class="filter-pills" id="sectionPills">
          <button type="button" class="pill active" data-filter="ALL">All (<?php echo count($lines); ?>)</button>
          <?php foreach ($sections as $section): ?>
            <button type="button" class="pill" data-filter="<?php echo abom_e($section['name']); ?>">
              <?php echo abom_e($section['name']); ?> (<?php echo (int) $section['count']; ?>)
            </button>
          <?php endforeach; ?>
        </div>

        <div id="bomTableHost">
          <?php $this->load->view('bom/_table'); ?>
        </div>

        <?php $this->load->view('bom/_approval_block'); ?>

      </div>
    </div>
  </main>
</div>
