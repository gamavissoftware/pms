<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _approval_block.php — the sign-off boxes.
 *
 * $config['abom_signoff_mode']:
 *   'customer' (default) — 3 boxes, exactly as the approved design
 *                          document. This is what goes to the customer.
 *   'internal'           — the same box markup with a fourth added,
 *                          splitting Approved By into Engineering and
 *                          Procurement.
 *
 * The internal four-stage workflow (draft -> submitted -> checked ->
 * eng_approved -> approved) still drives abom_bom_approval and the
 * on-screen status indicator. That is internal state; it is not forced
 * onto the printed page.
 *
 * A stage completed in the system prints the recorded name and
 * timestamp, with the blank rule kept for the wet signature.
 *
 * @var object $bom
 * @var string $signoff_mode
 * @var array  $signoff_names  optional [prepared_by => 'Name', ...]
 */

$signoff_mode  = isset($signoff_mode) ? $signoff_mode : 'customer';
$signoff_names = isset($signoff_names) ? $signoff_names : array();

$boxes = array(
    array(
        'role'     => 'Prepared By — Engineering',
        'user_key' => 'prepared_by',
        'at'       => isset($bom->prepared_at) ? $bom->prepared_at : null,
    ),
    array(
        'role'     => 'Checked By',
        'user_key' => 'checked_by',
        'at'       => isset($bom->checked_at) ? $bom->checked_at : null,
    ),
);

if ($signoff_mode === 'internal') {
    $boxes[] = array(
        'role'     => 'Approved By — Engineering',
        'user_key' => 'eng_approved_by',
        'at'       => isset($bom->eng_approved_at) ? $bom->eng_approved_at : null,
    );
    $boxes[] = array(
        'role'     => 'Approved By — Procurement',
        'user_key' => 'proc_approved_by',
        'at'       => isset($bom->proc_approved_at) ? $bom->proc_approved_at : null,
    );
} else {
    $boxes[] = array(
        'role'     => 'Approved By — Customer',
        'user_key' => 'proc_approved_by',
        'at'       => isset($bom->proc_approved_at) ? $bom->proc_approved_at : null,
    );
}
?>

<div class="approval-block" id="approvalBlock">
  <div class="ap-note">
    <strong style="color:var(--navy);">Review instructions:</strong>
    This BOM is reproduced verbatim from the released DF document. Please verify every item,
    model number, quantity and remark. Mark any quantity changes directly in the QTY column
    (changes are tagged &#9998;), note pending ERP codes highlighted in red, and return this
    document signed for approval.
  </div>
  <div class="ap-grid<?php echo count($boxes) === 4 ? ' ap-4' : ''; ?>">
    <?php foreach ($boxes as $box): ?>
      <?php
        $name = isset($signoff_names[$box['user_key']]) ? $signoff_names[$box['user_key']] : '';
        $done = ($name !== '' && !empty($box['at']));
      ?>
      <div class="ap-box">
        <div class="ap-role"><?php echo abom_e($box['role']); ?></div>
        <?php if ($done): ?>
          <div class="ap-recorded">
            <?php echo abom_e($name); ?><br>
            <span style="font-weight:400;color:#888;">
              <?php echo abom_e(date('d-m-Y H:i', strtotime($box['at']))); ?>
            </span>
          </div>
          <div class="ap-line ap-signed"></div>
        <?php else: ?>
          <div class="ap-line"></div>
        <?php endif; ?>
        <div class="ap-lbl"><span>Name / Sign</span><span>Date</span></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
