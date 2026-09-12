<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * master_bom_list.php — the BUILD register.
 *
 * @var array $variants  filtered; id => variant row
 * @var int   $total     how many exist before filtering
 * @var array $filters   search, family, model, panel, flag, active
 * @var array $families  id => family row
 * @var array $summary   id => item and flag counts
 * @var array $models    distinct machine models present
 * @var array $panels    distinct panel locations present
 */
$filters = isset($filters) ? $filters : array();
foreach (array('search', 'family', 'model', 'panel', 'flag', 'active') as $k) {
    if (!isset($filters[$k])) { $filters[$k] = ''; }
}
$total  = isset($total) ? (int) $total : count($variants);
$models = isset($models) ? $models : array();
$panels = isset($panels) ? $panels : array();
?>
<?php
/**
 * master_bom_list.php — the REFERENCE BOM REGISTER.
 *
 * The uploaded BOMs, as BOMs. /abom/master lists every master item flat,
 * which is right for "find every line using ERP 4060431" and wrong for
 * "work through DF-1808". This is the same data grouped the way it
 * arrived: one row per build, opening onto its own sheet.
 *
 * Reached only through Abom::master_bom(), which requires 'master_edit'.
 *
 * @var array  $variants  id => row
 * @var array  $families  id => row
 * @var array  $summary   variant_id => counts
 * @var string $message
 */
$summary = isset($summary) ? $summary : array();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM — Reference BOMs</title>
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet">
    <link href="<?php echo abom_asset('abom/abom.css'); ?>" rel="stylesheet">
</head>
<body>
<div class="abom-wrap">
  <header class="app-header">
    <a class="abom-home" href="<?php echo page_url; ?>Dashboard" title="Back to Dashboard">
      <span class="abom-home-icon" aria-hidden="true">&#8962;</span>
      <span class="abom-home-text">Dashboard</span>
    </a>
    <div>
      <h1>&#128193; Reference BOMs</h1>
      <div class="sub">The uploaded BOMs, one sheet each &mdash; view, edit and update</div>
    </div>
    <span class="badge review">MASTER DATA</span>
  </header>

  <div class="layout"><main class="main">

    <div class="abom-topbar">
      <?php
      /**
       * FILTERS, server-side on GET — the same shape as /abom/list and
       * /abom/master, deliberately. A third idiom on a third register
       * would be one more thing to learn for no gain, and a GET form
       * means a narrowed view is a URL somebody can bookmark or send to
       * a colleague.
       *
       * Every option except family is derived from the builds that
       * exist, so a machine model or panel location introduced by a
       * future build becomes filterable the day it lands.
       */
      ?>
      <form method="get" action="<?php echo page_url; ?>abom/master_bom" class="abom-filter-form">
        <input type="text" name="search" value="<?php echo abom_e($filters['search']); ?>"
               placeholder="Search build, machine, DF no., tracks&hellip;" style="min-width:230px;">

        <select name="family">
          <option value="">All families</option>
          <?php foreach ($families as $f): ?>
            <option value="<?php echo (int) $f->id; ?>"<?php echo abom_sel($filters['family'], $f->id); ?>>
              <?php echo abom_e($f->code); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <?php if (count($models) > 1): ?>
          <select name="model">
            <option value="">All models</option>
            <?php foreach ($models as $m): ?>
              <option value="<?php echo abom_e($m); ?>"<?php echo abom_sel($filters['model'], $m); ?>>
                <?php echo abom_e($m); ?>
              </option>
            <?php endforeach; ?>
          </select>
        <?php endif; ?>

        <?php if (count($panels) > 1): ?>
          <select name="panel">
            <option value="">All panel locations</option>
            <?php foreach ($panels as $pl): ?>
              <option value="<?php echo abom_e($pl); ?>"<?php echo abom_sel($filters['panel'], $pl); ?>>
                <?php echo abom_e($pl); ?>
              </option>
            <?php endforeach; ?>
          </select>
        <?php endif; ?>

        <select name="flag">
          <option value="">Any flags</option>
          <option value="conflict"<?php echo abom_sel($filters['flag'], 'conflict'); ?>>Has ERP conflicts</option>
          <option value="no_erp"<?php echo abom_sel($filters['flag'], 'no_erp'); ?>>Has items with no ERP code</option>
          <option value="review"<?php echo abom_sel($filters['flag'], 'review'); ?>>Has items to review</option>
          <option value="clean"<?php echo abom_sel($filters['flag'], 'clean'); ?>>Nothing to resolve</option>
        </select>

        <select name="active">
          <option value=""<?php echo abom_sel($filters['active'], ''); ?>>Active and retired</option>
          <option value="1"<?php echo abom_sel($filters['active'], '1'); ?>>Active only</option>
          <option value="0"<?php echo abom_sel($filters['active'], '0'); ?>>Retired only</option>
        </select>

        <button type="submit" class="btn-sm btn-print">Filter</button>
      </form>

      <?php
      // The count has to say whether it is the whole register or a
      // slice of it. "11 builds" under an active filter reads as the
      // total and would send somebody looking for the missing rows.
      $filtered = count($variants) !== (int) $total;
      ?>
      <span class="config-chip">
        <?php if ($filtered): ?>
          <?php echo count($variants); ?> of <?php echo (int) $total; ?> builds
        <?php else: ?>
          <?php echo (int) $total; ?> builds
        <?php endif; ?>
      </span>
      <?php if ($filtered): ?>
        <a class="mb-clear" href="<?php echo page_url; ?>abom/master_bom">Clear filters</a>
      <?php endif; ?>

      <div class="abom-actions">
        <a class="btn-sm btn-generate" href="<?php echo page_url; ?>abom/import"
           title="Turn a released DF spreadsheet into a build">&#128228; Import a BOM</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master">All items (flat)</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/config">&#9881; Build configuration</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/generate">Generator</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/guide">&#10068; Guide</a>
      </div>
    </div>

    <div class="abom-area">
      <?php if (!empty($message)): ?>
        <div class="abom-master-ok">&#10003; <?php echo abom_e($message); ?></div>
      <?php endif; ?>

      <div class="abom-master-note">
        <b>Each row is one build.</b> Open it to see its items in printed order and edit them
        in place. Changes affect every BOM generated <b>from now on</b> and none already saved
        &mdash; each saved BOM holds its own frozen copy of every line.
        <br><br>
        <b>Some builds combine several uploaded BOMs.</b> Where they do, those sheets are the
        same machine specification at different sizes &mdash; same CPU, same amplifiers, same
        motors &mdash; differing only in axes, tracks, speed and quantity. Because quantities
        here are <b>calculated rather than stored</b>, one catalogue produces all of them, and
        a new machine of that kind generates correctly with no new data at all. Inside the
        sheet you can filter by any individual BOM to see exactly what it contained.
      </div>

      <?php if (empty($variants)): ?>
        <div class="abom-empty">
          No build matches that filter.
          <a href="<?php echo page_url; ?>abom/master_bom">Show all <?php echo (int) $total; ?></a>.
        </div>
      <?php else: ?>
      <div class="abom-table-wrap">
        <table id="abomTable">
          <thead>
            <tr>
              <th class="left" style="width:120px;">BUILD</th>
              <th class="left">MACHINE</th>
              <th class="left" style="width:190px;">SOURCE BOMs</th>
              <th style="width:70px;">FAMILY</th>
              <th style="width:64px;">ITEMS</th>
              <th style="width:210px;">FLAGS TO RESOLVE</th>
              <th style="width:200px;">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($variants as $v): ?>
              <?php
              $c   = isset($summary[(int) $v->id]) ? $summary[(int) $v->id]
                     : array('items' => 0, 'active' => 0, 'conflict' => 0, 'no_erp' => 0, 'review' => 0);
              $fam = isset($families[(int) $v->plc_family_id]) ? $families[(int) $v->plc_family_id]->code : '';

              // How many BOMs were generated from this build. Anything
              // above zero and the build is not deletable at any price --
              // see Abom::master_bom_delete() for why.
              $used = isset($bom_usage[(int) $v->id]) ? (int) $bom_usage[(int) $v->id] : 0;
              ?>
              <tr class="data-row<?php echo empty($v->is_active) ? ' is-retired' : ''; ?>">
                <td class="left">
                  <b><?php echo abom_e($v->code); ?></b>
                  <?php if (empty($v->is_active)): ?>
                    <br><span class="formula-tag tag-review">INACTIVE</span>
                  <?php endif; ?>
                </td>
                <td class="left">
                  <?php echo abom_e($v->name); ?>
                  <div style="font-size:10.5px;color:#8A94A6;line-height:1.5;">
                    <?php echo abom_e($v->machine_model); ?>
                    <?php if (!empty($v->default_panel_location)): ?>
                      &middot; <?php echo abom_e($v->default_panel_location); ?>
                    <?php endif; ?>
                  </div>
                </td>
                <?php
                /**
                 * A build assembled from several sheets is not an
                 * anomaly — those sheets are the same machine at
                 * different sizes, sharing one catalogue so their
                 * quantities can be calculated. Listing them one per
                 * line, with a count, says that plainly; a single
                 * comma-run reads like a mistake.
                 */
                $dfs = array_filter(array_map('trim',
                    preg_split('/\s*,\s*/', (string) $v->source_df)));
                ?>
                <td class="left" style="font-size:11px;">
                  <?php if (count($dfs) > 1): ?>
                    <b><?php echo count($dfs); ?> BOMs combined</b>
                    <div class="mb-dfs">
                      <?php foreach ($dfs as $df): ?>
                        <span class="mb-df"><?php echo abom_e($df); ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <?php echo abom_e($v->source_df); ?>
                  <?php endif; ?>
                </td>
                <td style="text-align:center;">
                  <span class="formula-tag <?php echo $fam === 'FX5' ? 'tag-manual' : 'tag-review'; ?>">
                    <?php echo abom_e($fam); ?></span>
                </td>
                <td style="text-align:center;font-weight:700;">
                  <?php echo (int) $c['items']; ?>
                  <?php if ($c['active'] !== $c['items']): ?>
                    <div style="font-size:10px;font-weight:400;color:#8A94A6;">
                      <?php echo (int) $c['active']; ?> active</div>
                  <?php endif; ?>
                </td>
                <td style="text-align:center;">
                  <?php if ($c['conflict']): ?>
                    <span class="formula-tag tag-conflict"><?php echo (int) $c['conflict']; ?> CONFLICT</span>
                  <?php endif; ?>
                  <?php if ($c['no_erp']): ?>
                    <span class="formula-tag tag-pending"><?php echo (int) $c['no_erp']; ?> NO ERP</span>
                  <?php endif; ?>
                  <?php if ($c['review']): ?>
                    <span class="formula-tag tag-review"><?php echo (int) $c['review']; ?> REVIEW</span>
                  <?php endif; ?>
                  <?php if (!$c['conflict'] && !$c['no_erp'] && !$c['review']): ?>
                    <span class="tag-ok">&mdash;</span>
                  <?php endif; ?>
                </td>
                <?php
                /**
                 * GENERATE BOM, straight from the build.
                 *
                 * The generator's reference picker asks "which DF was
                 * this like?"; this asks "which build do I want?" —
                 * which is the question somebody standing at this table
                 * has already answered. It matters more as the list
                 * grows: searching a picker of forty reference
                 * configurations to reach a build you are already
                 * looking at is work the screen should not create.
                 *
                 * Only the build id travels. The machine is derived
                 * server-side from the build's recorded reference, so a
                 * link cannot be edited into a configuration that
                 * reaches a different build.
                 *
                 * Not offered on a RETIRED build. Engineering took it
                 * out of service; a button that starts a new document
                 * from it would be inviting exactly what retiring it
                 * was meant to stop. The sheet stays readable.
                 */
                ?>
                <td style="text-align:center;white-space:nowrap;">
                  <a class="btn-sm btn-copy"
                     href="<?php echo page_url; ?>abom/master_bom/<?php echo (int) $v->id; ?>">Open sheet</a>
                  <?php if (!empty($v->is_active)): ?>
                    <a class="btn-sm btn-generate"
                       href="<?php echo page_url; ?>abom/generate?build=<?php echo (int) $v->id; ?>"
                       title="Start a new BOM already configured for this build">
                      &#43; Generate BOM</a>
                  <?php endif; ?>
                  <?php
                  /*
                   * DELETE — offered ONLY on a build nothing has been
                   * generated from. This is for the duplicate left behind
                   * when the same DF is imported twice, and for nothing
                   * else.
                   *
                   * Where a BOM HAS been generated the button is not
                   * rendered disabled, it is replaced by the count and the
                   * reason. A greyed-out button invites clicking and
                   * explains nothing; the count tells the operator what to
                   * go and look at, and points at the thing they actually
                   * want, which is to retire the build rather than erase
                   * it.
                   *
                   * The server re-checks regardless -- this is a
                   * convenience, not the control.
                   */
                  ?>
                  <?php if ($used === 0): ?>
                    <button type="button" class="btn-sm abom-del-build"
                            data-variant="<?php echo (int) $v->id; ?>"
                            data-code="<?php echo abom_e($v->code); ?>"
                            data-items="<?php echo (int) $c['items']; ?>"
                            title="Delete this build — nothing has been generated from it">
                      &times; Delete</button>
                  <?php else: ?>
                    <span class="abom-del-locked"
                          title="<?php echo (int) $used; ?> generated BOM<?php echo $used === 1 ? '' : 's'; ?> reference this build, so it cannot be deleted. Deactivate it instead.">
                      <?php echo (int) $used; ?> BOM<?php echo $used === 1 ? '' : 's'; ?> — cannot delete
                    </span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

      <p class="abom-rowedit-note">
        A build is what a generated BOM is cut from. Which machine reaches which build is
        decided by the <a href="<?php echo page_url; ?>abom/config_list/variant_rule">selection
        rules</a>; what each one contains is the sheet behind <b>Open sheet</b>.
      </p>
    </div>

  </main></div>
</div>
<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script>
/*
 * Deleting a build from the register.
 *
 * Two-step confirmation, because this is one of the few genuinely
 * irreversible actions in the module: the build, its items and its
 * selection rule go, and there is no undo. The prompt names the build and
 * says how many items go with it, so the operator is confirming something
 * specific rather than agreeing to a dialog.
 *
 * The server re-checks that nothing has been generated from the build,
 * inside the transaction. This button only avoids offering an action that
 * is going to be refused.
 */
(function ($) {
  'use strict';

  $(document).on('click', '.abom-del-build', function () {
    var $btn  = $(this),
        id    = parseInt($btn.data('variant'), 10),
        code  = String($btn.data('code') || 'this build'),
        items = parseInt($btn.data('items'), 10) || 0;

    if (!id) { return; }

    var msg = 'Delete ' + code + '?\n\n'
            + 'This removes the build, its ' + items + ' item'
            + (items === 1 ? '' : 's') + ' and its selection rule.\n'
            + 'It cannot be undone.\n\n'
            + 'Nothing has been generated from this build, so no existing BOM is affected.';

    if (!window.confirm(msg)) { return; }

    $btn.prop('disabled', true).text('Deleting…');

    $.ajax({
      url: '<?php echo page_url; ?>abom/master_bom_delete',
      type: 'POST',
      dataType: 'json',
      data: { variant_id: id },
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).done(function (res) {
      if (res && res.status) {
        // Drop the row rather than reloading: the operator is usually
        // clearing several duplicates in a row and a reload loses the
        // filters they set to find them.
        $btn.closest('tr').fadeOut(200, function () { $(this).remove(); });
      } else {
        window.alert((res && res.message) || 'The build could not be deleted.');
        $btn.prop('disabled', false).html('&times; Delete');
      }
    }).fail(function (xhr) {
      var m = 'The build could not be deleted.';
      try { m = JSON.parse(xhr.responseText).message || m; } catch (e) {}
      window.alert(m);
      $btn.prop('disabled', false).html('&times; Delete');
    });
  });
}(jQuery));
</script>
</body>
</html>
