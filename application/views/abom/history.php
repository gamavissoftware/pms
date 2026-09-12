<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * history.php — the VERSION REPORT for one document.
 *
 * Every revision, newest first, each with what changed since the one
 * before it and who changed it.
 *
 * Two sources feed each entry and they answer different questions:
 *   the SNAPSHOT diff  — what did this revision end up containing
 *   the AUDIT rows     — who changed what while it was being prepared
 * Both are shown because either alone leaves an obvious question open.
 *
 * @var object $bom         the revision the user arrived from
 * @var array  $versions    newest first
 * @var array  $user_names  user_id => display name
 */

$user_names = isset($user_names) ? $user_names : array();

// abom_who(), abom_when(), abom_line_label(), abom_audit_action_label()
// and abom_audit_detail() live in abom_helper.php — _version_diff.php
// needs them too, and two unguarded declarations of one name is a fatal
// error waiting to happen.
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Version history <?php echo abom_e($bom->bom_no); ?></title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom.css'); ?>" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom-print.css'); ?>" rel="stylesheet" media="all">
</head>
<body>

<div class="abom-wrap">

  <header class="app-header">
    <a class="abom-home" href="<?php echo page_url; ?>Dashboard" title="Back to Dashboard">
      <span class="abom-home-icon" aria-hidden="true">&#8962;</span>
      <span class="abom-home-text">Dashboard</span>
    </a>
    <div>
      <h1>&#128337; Version History</h1>
      <div class="sub">
        <?php echo abom_e($bom->bom_no); ?>
        <?php if (!empty($bom->df_ref)): ?>&middot; <?php echo abom_e($bom->df_ref); ?><?php endif; ?>
        &middot; <?php echo abom_e($bom->machine_model); ?>
      </div>
    </div>
    <span class="badge review"><?php echo count($versions); ?>
      REVISION<?php echo count($versions) === 1 ? '' : 'S'; ?></span>
  </header>

  <div class="layout"><main class="main">

    <div class="abom-topbar">
      <div class="abom-actions">
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/view/<?php echo (int) $bom->id; ?>">
          &#8617; Back to the BOM</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/list">Saved BOMs</a>
        <button type="button" class="btn-sm btn-print" onclick="window.print()">&#128424;&#65039; Print</button>
      </div>
    </div>

    <div class="abom-area">

      <div class="abom-master-note">
        <b>Newest revision first.</b> Each entry lists what changed since the revision
        before it, and who changed it while it was being prepared.
        <br><br>
        <b>Archived versions are records, not documents.</b> A revision is photographed
        when it is approved and again when it is superseded. Those photographs can be
        read here at any time, but they cannot be edited, exported or issued &mdash; the
        current revision is the one to work from.
      </div>

      <?php foreach ($versions as $v): ?>
        <?php
        $b    = $v['bom'];
        $d    = $v['diff'];
        $rev  = (string) $b->revision !== '' ? $b->revision : '00';
        ?>
        <div class="ver-card<?php echo $b->status === 'superseded' ? ' is-superseded' : ''; ?>">

          <div class="ver-head">
            <span class="ver-no">REV.<?php echo abom_e($rev); ?></span>
            <span class="ver-status wf-<?php echo abom_e($b->status); ?>">
              <?php echo abom_e(abom_status_label($b->status)); ?>
            </span>
            <span class="ver-meta">
              <?php echo (int) $v['lines']; ?> line<?php echo (int) $v['lines'] === 1 ? '' : 's'; ?>
              &middot; <?php echo (int) $b->total_qty; ?> total qty
              <?php if (!empty($b->variant_code)): ?>
                &middot; <?php echo abom_e($b->variant_code); ?>
              <?php endif; ?>
            </span>
            <span class="ver-actions">
              <?php if ($v['source'] === 'archived' && $v['ref'] > 0): ?>
                <a class="btn-sm btn-copy"
                   href="<?php echo page_url; ?>abom/version/<?php echo (int) $v['ref']; ?>">
                  &#128444;&#65039; View this version</a>
              <?php elseif ($v['source'] === 'live'): ?>
                <a class="btn-sm btn-print"
                   href="<?php echo page_url; ?>abom/view/<?php echo (int) $b->id; ?>">Open</a>
              <?php endif; ?>
            </span>
          </div>

          <div class="ver-people">
            Prepared by <b><?php echo abom_e(abom_who($user_names, $b->prepared_by)); ?></b>
            on <?php echo abom_e(abom_when($b->prepared_at)); ?>
            <?php if (!empty($b->checked_by)): ?>
              &middot; checked by <b><?php echo abom_e(abom_who($user_names, $b->checked_by)); ?></b>
              on <?php echo abom_e(abom_when($b->checked_at)); ?>
            <?php endif; ?>
            <?php if (!empty($b->eng_approved_by)): ?>
              &middot; engineering <b><?php echo abom_e(abom_who($user_names, $b->eng_approved_by)); ?></b>
              on <?php echo abom_e(abom_when($b->eng_approved_at)); ?>
            <?php endif; ?>
            <?php if (!empty($b->proc_approved_by)): ?>
              &middot; procurement <b><?php echo abom_e(abom_who($user_names, $b->proc_approved_by)); ?></b>
              on <?php echo abom_e(abom_when($b->proc_approved_at)); ?>
            <?php endif; ?>
          </div>

          <?php if ($v['source'] === 'broken'): ?>
            <div class="ver-broken">
              <b>&#9888;&#65039; This version's stored snapshot could not be read.</b>
              The revision itself is intact and listed above; only the archived copy of
              its contents is unreadable, so no comparison can be shown for it. Report
              this &mdash; nothing you can do on screen will fix it.
            </div>

          <?php elseif ($d !== null && $d['first']): ?>
            <div class="ver-first">
              <b>First revision.</b> <?php echo (int) $d['counts']['added']; ?> lines,
              nothing to compare against.
            </div>

          <?php elseif ($d !== null): ?>
            <?php
            /**
             * The changelog. Deliberately grouped by KIND of change
             * rather than listed as one flat stream: "three quantities
             * moved" and "one part was removed" are read differently by
             * whoever has to approve the result, and burying the removal
             * among the quantity changes is how it gets missed.
             */
            ?>
            <?php if (array_sum($d['counts']) === 0): ?>
              <div class="ver-first">
                <b>No change to the parts list or the configuration.</b>
                This revision was raised but its content matches the one before it.
              </div>
            <?php else: ?>
              <?php $this->load->view('abom/_version_diff', array('d' => $d)); ?>
            <?php endif; ?>
          <?php endif; ?>

          <?php if (!empty($v['snapshots'])): ?>
            <div class="ver-snaps">
              <?php foreach ($v['snapshots'] as $s): ?>
                <span class="ver-snap">
                  &#128444;&#65039; <?php echo abom_e(abom_when($s->created_at)); ?>
                  <?php if (!empty($s->change_note)): ?>
                    — <?php echo abom_e($s->change_note); ?>
                  <?php endif; ?>
                  <?php if (!empty($s->created_by)): ?>
                    (<?php echo abom_e(abom_who($user_names, $s->created_by)); ?>)
                  <?php endif; ?>
                </span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($v['audit'])): ?>
            <details class="ver-audit">
              <summary><?php echo count($v['audit']); ?>
                change<?php echo count($v['audit']) === 1 ? '' : 's'; ?>
                recorded while this revision was being prepared</summary>
              <table class="ver-audit-table">
                <thead>
                  <tr><th class="left">WHEN</th><th class="left">WHO</th>
                      <th class="left">WHAT</th><th class="left">DETAIL</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($v['audit'] as $a): ?>
                    <tr>
                      <td class="left"><?php echo abom_e(abom_when($a->created_at)); ?></td>
                      <td class="left"><?php echo abom_e(abom_who($user_names, $a->user_id)); ?></td>
                      <td class="left"><?php echo abom_e(abom_audit_action_label($a->action)); ?></td>
                      <td class="left"><?php echo abom_e(abom_audit_detail($a)); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </details>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>

    </div>

  </main></div>
</div>

<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>
</html>
