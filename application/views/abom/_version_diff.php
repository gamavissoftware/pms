<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _version_diff.php — the changelog between two versions.
 *
 * Grouped by KIND of change, not listed as one flat stream. "Three
 * quantities moved" and "one part was removed" are read differently by
 * whoever has to approve the result, and burying a removal among
 * quantity changes is exactly how it gets missed.
 *
 * Order is by consequence, worst first: parts leaving the sheet, then
 * parts arriving, then the machine specification, then quantities, then
 * the descriptive fields, then remarks. Somebody who reads only the top
 * of this block has still read the part that could stop a build.
 *
 * @var array $d  from Abom_revision_model::diff()
 */
?>
<div class="ver-diff">

  <div class="vd-summary">
    <?php
    $chips = array(
        array('removed', $d['counts']['removed'], 'removed',        'vd-out'),
        array('added',   $d['counts']['added'],   'added',          'vd-in'),
        array('header',  $d['counts']['header'],  'config change',  'vd-cfg'),
        array('qty',     $d['counts']['qty'],     'quantity change','vd-qty'),
        array('detail',  $d['counts']['detail'],  'detail change',  'vd-det'),
        array('remark',  $d['counts']['remark'],  'remark change',  'vd-rem'),
    );
    foreach ($chips as $c):
        if ($c[1] <= 0) { continue; }
    ?>
      <span class="vd-chip <?php echo $c[3]; ?>">
        <?php echo (int) $c[1]; ?> <?php echo abom_e($c[2]); ?><?php echo $c[1] === 1 ? '' : 's'; ?>
      </span>
    <?php endforeach; ?>
  </div>

  <?php if (!empty($d['removed'])): ?>
    <?php
    // First, and in red. A part that has left the sheet is the change
    // most likely to stop a machine being built, and the one least
    // likely to be noticed by reading the new document on its own.
    ?>
    <div class="vd-group vd-g-out">
      <div class="vd-title">&#8722; Removed from the BOM</div>
      <ul>
        <?php foreach ($d['removed'] as $line): ?>
          <li>
            <?php echo abom_e(abom_line_label($line)); ?>
            <span class="vd-qtyval">was qty <?php echo (int) $line['qty']; ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($d['added'])): ?>
    <div class="vd-group vd-g-in">
      <div class="vd-title">&#43; Added to the BOM</div>
      <ul>
        <?php foreach ($d['added'] as $line): ?>
          <li>
            <?php echo abom_e(abom_line_label($line)); ?>
            <span class="vd-qtyval">qty <?php echo (int) $line['qty']; ?></span>
            <?php if (!empty($line['is_manual_add'])): ?>
              <span class="formula-tag tag-manual">ADDED BY HAND</span>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($d['header'])): ?>
    <div class="vd-group vd-g-cfg">
      <div class="vd-title">&#9881; Machine configuration</div>
      <ul>
        <?php foreach ($d['header'] as $h): ?>
          <li>
            <b><?php echo abom_e($h['label']); ?></b>
            <span class="vd-was"><?php echo abom_e($h['was']); ?></span>
            &rarr;
            <span class="vd-now"><?php echo abom_e($h['now']); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($d['qty'])): ?>
    <div class="vd-group vd-g-qty">
      <div class="vd-title">&#8801; Quantities</div>
      <ul>
        <?php foreach ($d['qty'] as $q): ?>
          <li>
            <?php echo abom_e(abom_line_label($q['line'])); ?>
            <span class="vd-was"><?php echo (int) $q['was']; ?></span>
            &rarr;
            <span class="vd-now"><?php echo (int) $q['now']; ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($d['detail'])): ?>
    <?php
    // A frozen line duplicates description, part number and ERP code on
    // purpose. If one moved between revisions the MASTER ITEM was
    // corrected and this revision picked the correction up. That is a
    // change to what will be ordered, and it must not be left for a
    // reviewer to spot by eye.
    ?>
    <div class="vd-group vd-g-det">
      <div class="vd-title">&#9998; Part details corrected from master data</div>
      <ul>
        <?php foreach ($d['detail'] as $x): ?>
          <li>
            <?php echo abom_e(abom_line_label($x['line'])); ?> —
            <b><?php echo abom_e($x['label']); ?></b>
            <span class="vd-was"><?php echo abom_e($x['was'] !== '' ? $x['was'] : '—'); ?></span>
            &rarr;
            <span class="vd-now"><?php echo abom_e($x['now'] !== '' ? $x['now'] : '—'); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (!empty($d['remark'])): ?>
    <div class="vd-group vd-g-rem">
      <div class="vd-title">&#128172; Remarks</div>
      <ul>
        <?php foreach ($d['remark'] as $r): ?>
          <li>
            <?php echo abom_e(abom_line_label($r['line'])); ?>
            <span class="vd-was"><?php echo abom_e($r['was'] !== '' ? $r['was'] : '(blank)'); ?></span>
            &rarr;
            <span class="vd-now"><?php echo abom_e($r['now'] !== '' ? $r['now'] : '(blank)'); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

</div>
