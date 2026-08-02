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
  <a class="abom-home" href="<?php echo page_url; ?>Dashboard" title="Back to Dashboard">
    <span class="abom-home-icon" aria-hidden="true">&#8962;</span>
    <span class="abom-home-text">Dashboard</span>
  </a>
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

  <?php $this->load->view('abom/_config_panel'); ?>

  <main class="main">

    <div class="abom-topbar" id="configBar">
      <?php
      // One chip per configuration value, per ENABLED feature gate, and
      // for the workflow status — built in the controller so the AJAX
      // response can replace the whole row.
      ?>
      <span id="chipRow" style="display:contents;">
        <?php foreach ($chips as $chip): ?>
          <span class="config-chip <?php echo abom_e($chip['class']); ?>"><?php echo abom_e($chip['label']); ?></span>
        <?php endforeach; ?>
      </span>
      <div class="abom-actions">
        <?php if ($editable): ?>
          <button type="button" class="btn-sm btn-generate" id="btnGenerate">&#8635; Recalculate</button>
          <button type="button" class="btn-sm btn-export" id="btnSave">Save BOM</button>
        <?php endif; ?>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/list">Saved BOMs</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/guide">&#10068; Guide</a>
        <button type="button" class="btn-sm btn-print" onclick="window.print()">&#128424;&#65039; Print</button>
        <?php if (!empty($bom->id)): ?>
          <a class="btn-sm btn-export" href="<?php echo page_url; ?>abom/export/csv/<?php echo (int) $bom->id; ?>">&#128229; Export CSV</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="abom-area">
      <div id="abomContent">

        <?php $this->load->view('abom/_doc_header'); ?>

        <div class="filter-pills" id="sectionPills">
          <button type="button" class="pill active" data-filter="ALL">All (<?php echo count($lines); ?>)</button>
          <?php foreach ($sections as $section): ?>
            <button type="button" class="pill" data-filter="<?php echo abom_e($section['name']); ?>">
              <?php echo abom_e($section['name']); ?> (<?php echo (int) $section['count']; ?>)
            </button>
          <?php endforeach; ?>
        </div>

        <?php if (!empty($qty_locked_reason)): ?>
          <div class="qty-lock-note" id="qtyLockNote">&#128274; <?php echo abom_e($qty_locked_reason); ?></div>
        <?php endif; ?>

        <div id="abomTableHost">
          <?php $this->load->view('abom/_table', array('qty_editable' => !empty($qty_editable))); ?>
        </div>

        <?php $this->load->view('abom/_workflow'); ?>

        <?php $this->load->view('abom/_approval_block'); ?>

      </div>
    </div>
  </main>
</div>
