<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _legend.php — the Review Legend, from the approved design document.
 *
 * The design's three original entries are kept verbatim and in order.
 * The row-tint entries below them describe the four modifier classes
 * Bom_engine::row_class() produces, using the same --var colours.
 */
?>
<div class="legend">
  <b>Review Legend:</b><br>
  <span class="lg-chip" style="background:var(--orange);"></span><span style="color:var(--red);font-weight:700;">NEW / PENDING</span> — ERP code to be assigned<br>
  <span class="lg-chip" style="background:var(--lyellow);"></span>Qty edited during review (marked &#9998;)<br>
  <span class="lg-chip" style="background:#FAFCFF;border:1px solid var(--border);"></span>Qty as per released DF<br>
  <span class="lg-chip" style="background:#F7D9D9;"></span>ERP code conflict — verify before ordering<br>
  <span class="lg-chip" style="background:var(--lgreen);"></span>Optional item — included by machine feature<br>
  <span class="lg-chip" style="background:var(--lblue);"></span>Manual quantity — engineer must confirm
</div>
