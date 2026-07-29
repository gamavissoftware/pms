<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _workflow.php — the on-screen approval panel.
 *
 * Screen only: the print stylesheet hides it. The printed sign-off block
 * is _approval_block.php and is governed by abom_signoff_mode, which is
 * a separate concern from this internal four-stage workflow.
 *
 * Every control here is a convenience. The authority is
 * Abom_approval_model — a button that should not be shown is also a
 * transition the server refuses.
 *
 * @var array  $workflow
 * @var object $bom
 */

if (empty($workflow['enabled'])) {
    return;
}
?>
<div class="abom-workflow" id="abomWorkflow" data-bom="<?php echo (int) $bom->id; ?>">

  <div class="wf-head">
    <span class="wf-stage">Workflow</span>
    <span class="wf-status wf-<?php echo abom_e($bom->status); ?>">
      <?php echo abom_e($workflow['status_label']); ?>
    </span>
  </div>

  <?php if (empty($workflow['configured'])): ?>
    <div class="wf-problem">
      <strong>&#9888;&#65039; Approval controls are unavailable — this is a CONFIGURATION problem, not a permissions one.</strong>
      <?php foreach ($workflow['problems'] as $problem): ?>
        <div>&bull; <?php echo abom_e($problem); ?></div>
      <?php endforeach; ?>
    </div>

  <?php else: ?>

    <?php if (!empty($workflow['blockers'])): ?>
      <div class="wf-blockers">
        <strong>Blocking the next stage:</strong>
        <?php foreach ($workflow['blockers'] as $blocker): ?>
          <div>&bull; <?php echo abom_e($blocker); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="wf-actions">
      <?php if (!empty($workflow['next'])): ?>
        <?php if ($workflow['may'] && empty($workflow['blockers'])): ?>
          <button type="button" class="btn-sm btn-export" data-wf="advance">
            <?php echo abom_e($workflow['next']['label']); ?>
          </button>
        <?php else: ?>
          <button type="button" class="btn-sm btn-export" disabled
                  title="<?php echo abom_e(!$workflow['may']
                      ? 'You do not have permission for this stage.'
                      : 'Resolve the blocking items first.'); ?>">
            <?php echo abom_e($workflow['next']['label']); ?>
          </button>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (!empty($workflow['can_reject'])): ?>
        <button type="button" class="btn-sm btn-copy" data-wf="reject">Reject</button>
      <?php endif; ?>

      <?php if (!empty($workflow['can_reopen'])): ?>
        <button type="button" class="btn-sm btn-print" data-wf="reopen">Reopen for editing</button>
      <?php endif; ?>

      <?php if (!empty($workflow['can_revise'])): ?>
        <button type="button" class="btn-sm btn-generate" data-wf="revision">Create revision</button>
      <?php endif; ?>
    </div>

    <div class="wf-exports">
      <a class="btn-sm btn-export" href="<?php echo page_url; ?>abom/export/csv/<?php echo (int) $bom->id; ?>">&#128229; CSV</a>
      <a class="btn-sm btn-export" href="<?php echo page_url; ?>abom/export/xlsx/<?php echo (int) $bom->id; ?>">&#128229; Excel</a>
      <a class="btn-sm btn-export" href="<?php echo page_url; ?>abom/export/pdf/<?php echo (int) $bom->id; ?>">&#128229; PDF</a>
    </div>

  <?php endif; ?>

  <?php if (!empty($workflow['trail'])): ?>
    <div class="wf-trail">
      <strong>Approval trail</strong>
      <?php foreach ($workflow['trail'] as $entry): ?>
        <div class="wf-entry">
          <span class="wf-when"><?php echo abom_e(date('d-m-Y H:i', strtotime($entry->created_at))); ?></span>
          <span class="wf-what"><?php echo abom_e(strtoupper($entry->action)); ?></span>
          <span class="wf-who"><?php echo abom_e($entry->user_name !== null ? $entry->user_name : 'user ' . $entry->user_id); ?></span>
          <?php if (!empty($entry->comment)): ?>
            <span class="wf-comment">&ldquo;<?php echo abom_e($entry->comment); ?>&rdquo;</span>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
