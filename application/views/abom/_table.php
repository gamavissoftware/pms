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
 * @var array $lines
 * @var bool  $qty_editable  required; defaults to FALSE if omitted
 */

$qty_editable = isset($qty_editable) ? (bool) $qty_editable : false;

// Line counts per section, for the "(n items)" caption on each sec-row.
$section_counts = array();
foreach ($lines as $line) {
    $name = $line->section_name;
    $section_counts[$name] = isset($section_counts[$name]) ? $section_counts[$name] + 1 : 1;
}

$current_section = null;
?>
<div class="bom-table-wrap">
  <table id="bomTable">
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
      </tr>
    </thead>
    <tbody id="bomBody">
    <?php if (empty($lines)): ?>
      <tr class="data-row">
        <td colspan="9" style="text-align:center;padding:30px;color:#555;">
          No line items for this configuration.
        </td>
      </tr>
    <?php else: ?>
      <?php foreach ($lines as $line): ?>

        <?php if ($line->section_name !== $current_section): ?>
          <?php $current_section = $line->section_name; ?>
          <tr class="sec-row" data-section="<?php echo abom_e($current_section); ?>">
            <td colspan="9"><?php echo abom_e($current_section); ?>
              <span>(<?php echo (int) $section_counts[$current_section]; ?> items)</span>
            </td>
          </tr>
        <?php endif; ?>

        <tr class="data-row <?php echo abom_row_class($line); ?>"
            data-section="<?php echo abom_e($line->section_name); ?>"
            data-line="<?php echo (int) $line->line_no; ?>"
            data-formula="<?php echo abom_e($line->formula_code); ?>"
            data-severity="<?php echo abom_e($line->issue_severity); ?>"
            data-optional="<?php echo (int) $line->is_optional; ?>">
          <td style="text-align:center;font-weight:700;"><?php echo (int) $line->line_no; ?></td>
          <td style="text-align:center;"><?php echo abom_erp_cell($line); ?></td>
          <td class="desc-cell"><?php echo abom_e($line->description); ?></td>
          <td class="part-cell"><?php echo $line->part_no !== '' ? abom_e($line->part_no) : '&mdash;'; ?></td>
          <td style="text-align:center;"><?php echo $line->manufacturer !== '' ? abom_e($line->manufacturer) : '&mdash;'; ?></td>
          <td style="text-align:center;"><?php echo abom_qty_cell($line, $qty_editable); ?></td>
          <td style="text-align:center;"><?php echo abom_e(abom_uom($line->uom)); ?></td>
          <td class="remarks-cell"><?php echo abom_remarks_cell($line); ?></td>
          <td style="text-align:center;"><?php echo abom_status_badges($line); ?></td>
        </tr>

      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>
