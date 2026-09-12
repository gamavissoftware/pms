<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _table.php — the BOM table. SHARED, not forked.
 *
 * Used unchanged by view.php and generate.php. Everything — the nine
 * columns, the sec-row grouping, the badges — is identical, so the two
 * screens cannot drift apart.
 *
 * Nine columns, exactly as the approved design document:
 *   S.NO. | ERP CODE | DESCRIPTION | MODEL NO. / PART NO. | MANUFACTURER
 *   | QTY. | UOM | REMARKS | STATUS
 *
 * $qty_editable is decided by the CALLER from workflow state via
 * abom_qty_editable() — never by a default here. The design keeps its
 * quantity inputs live for review markups, and that is where the ✎
 * marker comes from, but an engineering- or procurement-approved BOM is
 * the document someone is about to print and send: it renders a static
 * quantity with no edit affordance. It is separate from $editable, which
 * governs the configuration panel only.
 *
 * Defaults to FALSE. A caller that forgets to pass it gets the safe,
 * read-only rendering rather than an editable released document.
 *
 * ROW EDITING ($rows_editable) is a SEPARATE permission from quantity
 * editing, and defaults to FALSE for the same reason: a caller that
 * forgets gets the read-only rendering. It adds a tenth ACTIONS column
 * carrying insert/remove, and makes REMARKS typeable.
 *
 * Everything it does is scoped to THIS BOM. Nothing on this screen can
 * write to abom_item — the generator holds row edits as form state until
 * Save, and a saved BOM writes them to abom_bom_line, which is already a
 * per-BOM frozen snapshot. The nine reference builds cannot be altered
 * from here.
 *
 * @var array $lines
 * @var bool  $qty_editable   required; defaults to FALSE if omitted
 * @var bool  $rows_editable  optional; defaults to FALSE
 */

$qty_editable  = isset($qty_editable) ? (bool) $qty_editable : false;
$rows_editable = isset($rows_editable) ? (bool) $rows_editable : false;

// The ACTIONS column exists only while rows are editable, so the printed
// and released documents keep the design's nine columns exactly.
$col_count = $rows_editable ? 10 : 9;

// Line counts per section, for the "(n items)" caption on each sec-row.
$section_counts = array();
foreach ($lines as $line) {
    $name = $line->section_name;
    $section_counts[$name] = isset($section_counts[$name]) ? $section_counts[$name] + 1 : 1;
}

$current_section = null;
?>
<div class="abom-table-wrap">
  <table id="abomTable">
    <thead>
      <tr>
        <th style="width:44px;">S.NO.</th>
        <th style="width:100px;">ERP CODE</th>
        <th class="left" style="width:290px;">DESCRIPTION</th>
        <th class="left" style="width:165px;">MODEL NO. / PART NO.</th>
        <th style="width:100px;">MANUFACTURER</th>
        <th style="width:60px;">QTY.</th>
        <th style="width:55px;">UOM</th>
        <th class="left" style="width:220px;">REMARKS</th>
        <th style="width:96px;">STATUS</th>
        <?php if ($rows_editable): ?>
          <th class="col-actions" style="width:62px;">&nbsp;</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody id="abomBody">
    <?php if (empty($lines)): ?>
      <tr class="data-row">
        <td colspan="<?php echo (int) $col_count; ?>" style="text-align:center;padding:30px;color:#555;">
          No line items for this configuration.
        </td>
      </tr>
    <?php else: ?>
      <?php foreach ($lines as $line): ?>

        <?php if ($line->section_name !== $current_section): ?>
          <?php $current_section = $line->section_name; ?>
          <tr class="sec-row" data-section="<?php echo abom_e($current_section); ?>"
              data-section-order="<?php echo (int) $line->section_order; ?>">
            <td colspan="<?php echo (int) $col_count; ?>"><?php echo abom_e($current_section); ?>
              <span>(<?php echo (int) $section_counts[$current_section]; ?> items)</span>
            </td>
          </tr>
        <?php endif; ?>

        <tr class="data-row <?php echo abom_row_class($line); ?><?php echo !empty($line->is_manual_add) ? ' is-manual-row' : ''; ?>"
            data-section="<?php echo abom_e($line->section_name); ?>"
            <?php
            /*
             * The section's SORT POSITION travels with the row, not just
             * its name. A hand-added row that carries the name but not
             * the order was stored with the fallback (99), which sorts
             * after every real section — so it appeared at the bottom of
             * the sheet under a second copy of its own section heading,
             * however carefully it had been placed.
             */
            ?>
            data-section-order="<?php echo (int) $line->section_order; ?>"
            data-line="<?php echo (int) $line->line_no; ?>"
            <?php
            /**
             * STABLE ROW KEY. The table is re-rendered from scratch on
             * every configuration change, so line_no is not an identity —
             * it shifts the moment the item count does. Remarks, removals
             * and the anchor of a hand-added row are all keyed on this
             * instead, which is what lets an engineer set the tracks and
             * still have their typed remarks on the right lines.
             *
             *   i<item_id>  a generated row, from master item <item_id>
             *   m<uid>      a hand-added row, uid minted by the browser
             */
            ?>
            data-key="<?php echo !empty($line->row_key)
                ? abom_e($line->row_key)
                : ('i' . (int) $line->item_id); ?>"
            <?php
            /**
             * PERSISTED LINE ID. On a saved BOM this — not data-key — is
             * the identity: data-key is derived from item_id, and every
             * hand-added row has item_id NULL, so two manual rows would
             * share the key "i0". The saved-BOM editor matches on this.
             * 0 means a row that has never been written.
             */
            ?>
            data-line-id="<?php echo isset($line->id) ? (int) $line->id : 0; ?>"
            data-item="<?php echo (int) $line->item_id; ?>"
            data-formula="<?php echo abom_e($line->formula_code); ?>"
            data-severity="<?php echo abom_e($line->issue_severity); ?>"
            data-optional="<?php echo (int) $line->is_optional; ?>"
            data-manual="<?php echo !empty($line->is_manual_add) ? 1 : 0; ?>">
          <td style="text-align:center;font-weight:700;" class="sno-cell"><?php echo (int) $line->line_no; ?></td>
          <?php if ($rows_editable && !empty($line->is_manual_add)): ?>
            <?php
            /**
             * A hand-added row has no master item behind it, so the four
             * descriptive columns are free text. They are inputs rather
             * than static cells ONLY on a manual row — a generated row's
             * description and part number stay read-only, because those
             * come from the master data and editing them here would make
             * the sheet disagree with the catalogue it claims to quote.
             */
            ?>
            <td style="text-align:center;"><?php echo abom_manual_cell('erp_code', $line->erp_code, $line->line_no, 'ERP', 32); ?></td>
            <td class="desc-cell"><?php echo abom_manual_cell('description', $line->description, $line->line_no, 'Description', 255); ?></td>
            <td class="part-cell"><?php echo abom_manual_cell('part_no', $line->part_no, $line->line_no, 'Part no.', 96); ?></td>
            <td style="text-align:center;"><?php echo abom_manual_cell('manufacturer', $line->manufacturer, $line->line_no, 'Manufacturer', 64); ?></td>
          <?php else: ?>
            <td style="text-align:center;"><?php echo abom_erp_cell($line); ?></td>
            <td class="desc-cell"><?php echo abom_e($line->description); ?></td>
            <td class="part-cell"><?php echo $line->part_no !== '' ? abom_e($line->part_no) : '&mdash;'; ?></td>
            <td style="text-align:center;"><?php echo $line->manufacturer !== '' ? abom_e($line->manufacturer) : '&mdash;'; ?></td>
          <?php endif; ?>
          <td style="text-align:center;"><?php echo abom_qty_cell($line, $qty_editable); ?></td>
          <td style="text-align:center;"><?php echo abom_e(abom_uom($line->uom)); ?></td>
          <td class="remarks-cell"><?php echo abom_remarks_cell($line, $rows_editable); ?></td>
          <td style="text-align:center;"><?php echo abom_status_badges($line); ?></td>
          <?php if ($rows_editable): ?>
            <td class="col-actions"><?php echo abom_row_actions_cell($line); ?></td>
          <?php endif; ?>
        </tr>

      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>
