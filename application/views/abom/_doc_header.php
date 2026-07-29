<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _doc_header.php — the verbatim DF title strip, the stats row and the
 * review-notes banner. SHARED by view.php and generate.php.
 *
 * @var object $bom    header (persisted row, or a stdClass built from
 *                     the live configuration on the generate screen)
 * @var array  $stats  from Abom_engine::stats()
 * @var array  $notes  optional review notes
 * @var string $family_code
 * @var bool   $overridden
 */

$notes      = isset($notes) ? $notes : array();
$overridden = isset($overridden) ? (bool) $overridden : false;

$title_bits = array();
$title_bits[] = $bom->bom_no . (!empty($bom->revision) ? ' (REV.' . $bom->revision . ')' : '');
if (!empty($bom->df_ref))       { $title_bits[] = 'REF ' . $bom->df_ref; }
$title_bits[] = $bom->machine_model;
$title_bits[] = strtoupper($bom->motion_type);
$title_bits[] = $bom->axes . ' AXIS';
$title_bits[] = $bom->tracks . ' TRACKS';
$title_bits[] = 'SPEED ' . $bom->speed_ppm;
if (!empty($bom->machine_side) && $bom->machine_side !== 'N/A') { $title_bits[] = $bom->machine_side; }
$title_bits[] = 'MITSUBISHI';

$doc_date = !empty($bom->created_at) ? date('d-m-Y', strtotime($bom->created_at)) : date('d-m-Y');
?>

<div class="doc-title" id="docTitle">
  &#128196; <b><?php echo abom_e(implode(', ', $title_bits)); ?></b>
  <span class="dt-date">BOM Date: <?php echo abom_e($doc_date); ?></span>
</div>

<?php
// Five cards, exactly as the approved design document: Line Items,
// Total Qty, Sections, ERP Pending, Qty Edited. The conflict and manual
// counts are NOT given cards here — the design does not have them, and
// both are already unmissable: a conflict shows as a red row, a CONFLICT
// badge and a line in the review banner above.
$qty_edited = 0;
foreach ($lines as $l) {
    if (!empty($l->is_overridden)) {
        $qty_edited++;
    }
}
?>
<div class="stats-row" id="statsRow">
  <div class="stat-card">
    <div class="val"><?php echo (int) $stats['lines']; ?></div>
    <div class="lbl">Line Items</div>
  </div>
  <div class="stat-card">
    <div class="val"><?php echo (int) $stats['total_qty']; ?></div>
    <div class="lbl">Total Qty</div>
  </div>
  <div class="stat-card">
    <div class="val"><?php echo count($sections); ?></div>
    <div class="lbl">Sections</div>
  </div>
  <div class="stat-card">
    <div class="val" style="color:var(--red)"><?php echo (int) $stats['no_erp']; ?></div>
    <div class="lbl">ERP Pending</div>
  </div>
  <div class="stat-card">
    <div class="val" style="color:var(--warn)" id="statQtyEdited"><?php echo (int) $qty_edited; ?></div>
    <div class="lbl">Qty Edited</div>
  </div>
</div>

<?php if ($overridden): ?>
  <div class="action-banner" style="background:#FDE7E7;border-color:#E0A0A0;color:#7A1A1A;">
    <strong>&#9888;&#65039; PLC family manually overridden</strong>
    <div>The engineer selected <b><?php echo abom_e($family_code); ?></b> rather than the
      auto-detected family. Verify the selection before approval.</div>
  </div>
<?php endif; ?>

<?php if (!empty($notes)): ?>
  <div class="action-banner" id="actionBanner">
    <strong>&#9888;&#65039; Points to verify during review</strong>
    <?php foreach ($notes as $note): ?>
      <div>&bull; <?php echo abom_e($note); ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
