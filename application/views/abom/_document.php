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
 * @var bool   $config_editable
 * @var string $notice           pre-escaped HTML, or ''
 */

$editable        = isset($editable) ? (bool) $editable : false;
$config_editable = isset($config_editable) ? (bool) $config_editable : false;

/**
 * An ARCHIVED version — a photograph of a document that no longer
 * exists in this form. Every control that would act on a live BOM is
 * withheld: there is no row behind it to act on.
 *
 * Export is withheld too, and that is the important one. A PDF printed
 * from here would be indistinguishable from a current BOM once it left
 * the screen, and somebody would order from it.
 */
$archived = isset($archived) ? (bool) $archived : false;
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
  <?php if ($archived): ?>
    <span class="badge overridden">ARCHIVED &middot; NOT FOR ISSUE</span>
  <?php elseif ($editable): ?>
    <span class="badge">GENERATOR</span>
  <?php elseif ($config_editable): ?>
    <?php
    // A draft whose configuration is still open is NOT the document to
    // put in front of a customer, and saying so is the whole point of
    // the badge. Calling it "for review" while the axis count is a
    // typeable box would be the header contradicting the screen.
    ?>
    <span class="badge">DRAFT &middot; EDITABLE</span>
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
      /**
       * NO chip row here.
       *
       * It carried one chip per configuration value, and on a wide
       * machine with several feature gates enabled it wrapped onto a
       * second line and crowded the action buttons — a bar of twelve
       * badges reads as decoration, not as information.
       *
       * Nothing was lost by removing it. Every value it showed is
       * stated somewhere it belongs:
       *
       *   BOM no, revision, DF ref, model,
       *   motion, axes, tracks, speed, side   -> the document title strip
       *   PLC family, build variant, panel    -> the configuration sidebar
       *   enabled features                    -> the configuration sidebar
       *   workflow status                     -> the sign-off block
       *
       * The features were the one thing the sidebar did not show on a
       * READ-ONLY document, because the checkboxes are rendered only
       * when the panel is editable. A read-only list was added there so
       * that removing this row could not quietly drop them from a
       * released BOM.
       */
      ?>
      <div class="abom-actions">
        <?php if ($editable): ?>
          <button type="button" class="btn-sm btn-generate" id="btnGenerate">&#8635; Recalculate</button>
          <button type="button" class="btn-sm btn-export" id="btnSave">Save BOM</button>
        <?php elseif ($config_editable): ?>
          <?php
          /**
           * Deliberately NOT labelled "Save BOM". The generator's button
           * of that name creates a new document; this one rewrites the
           * one on screen. Two buttons that read the same and do
           * different things is how someone ends up with a fork of their
           * own BOM.
           *
           * It also does not say "Recalculate", because it is not a
           * preview — pressing it changes stored lines.
           */
          ?>
          <button type="button" class="btn-sm btn-generate" id="abomSaveConfig"
                  data-bom="<?php echo (int) $bom->id; ?>">
            &#128295; Apply configuration
          </button>
        <?php endif; ?>
        <?php if ($archived): ?>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/history/<?php echo (int) $bom->id; ?>">&#8617; Version history</a>
        <?php else: ?>
          <?php if (!empty($bom->id)): ?>
            <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/history/<?php echo (int) $bom->id; ?>"
               title="Every revision of this BOM and what changed between them">&#128337; History</a>
          <?php endif; ?>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/list">Saved BOMs</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/guide">&#10068; Guide</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master">&#128295; Master items</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master_bom">&#128193; Reference BOMs</a>
        <?php endif; ?>
        <?php if (!$archived): ?>
          <button type="button" class="btn-sm btn-print" onclick="window.print()">&#128424;&#65039; Print</button>
        <?php endif; ?>
        <?php
        /**
         * NO export button here.
         *
         * There was a CSV-only one, and it was worse than nothing: it
         * sat next to a full CSV / Excel / PDF row in the sign-off block
         * below, so the topbar quietly implied CSV was the only format
         * on offer. One complete set of export buttons, in the place
         * where the document is finished and sent.
         */
        ?>
      </div>
    </div>

    <div class="abom-area">
      <div id="abomContent">

        <?php if (!empty($notice)): ?>
          <?php
          /**
           * Where this screen's starting configuration came from —
           * shown only when it came from somewhere the operator cannot
           * see, i.e. a build chosen on the Reference BOMs register.
           *
           * NOT escaped: the controller composes it and escapes every
           * value it interpolates. It carries <b> because the build
           * code and any caveat are the parts that must be read.
           */
          ?>
          <div class="abom-prefill-notice"><?php echo $notice; ?></div>
        <?php endif; ?>

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
          <?php $this->load->view('abom/_table', array(
              'qty_editable'  => !empty($qty_editable),
              'rows_editable' => !empty($rows_editable),
          )); ?>
        </div>

        <?php if (!empty($rows_editable)): ?>
          <?php
          /**
           * Removing a row is reversible, and it has to SAY so. A
           * mis-click that silently drops a purchasable line from a
           * document heading for procurement is not an acceptable
           * failure, so the count of removed rows stays on screen with a
           * restore next to it until the BOM is saved.
           *
           * It also states outright that master data is untouched,
           * because "delete" on a row of a parts list is exactly the
           * word that makes people worry they have broken the catalogue.
           */
          ?>
          <div class="row-restore-bar" id="abomRestoreBar" hidden></div>

          <?php if (!empty($bom->id)): ?>
            <?php
            /**
             * A SAVED BOM edits in place and needs an explicit Save —
             * the generator's Save BOM button creates a new document,
             * which is not what is wanted here.
             *
             * The bar is sticky-visible rather than hidden until dirty,
             * because a row added and then not saved is exactly the
             * work someone will assume was kept.
             */
            ?>
            <div class="lines-save-bar" id="abomLinesBar" data-bom="<?php echo (int) $bom->id; ?>">
              <span class="lsb-state" id="abomLinesState">
                Add, remove or edit rows above, then save.
              </span>
              <button type="button" class="btn-sm btn-generate" id="abomSaveLines">
                &#128190; Save changes
              </button>
            </div>
          <?php endif; ?>

          <p class="abom-rowedit-note">
            <b>Editing this BOM only.</b> Remarks, added rows and removed rows apply to this
            document alone. The master item list and the reference builds are not changed
            by anything on this screen.
          </p>
        <?php endif; ?>

        <?php $this->load->view('abom/_workflow'); ?>

        <?php $this->load->view('abom/_approval_block'); ?>

      </div>
    </div>
  </main>
</div>
