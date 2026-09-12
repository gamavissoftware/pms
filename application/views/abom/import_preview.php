<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * import_preview.php — step 2. The screen that makes the feature safe.
 *
 * Nothing has been written when this renders. Every row the sheet
 * contains is shown with the quantity rule worked out for it, the
 * reasoning behind that rule, and every problem found — and the
 * operator can change any rule or drop any row before confirming.
 *
 * The rows needing a decision are pulled to the TOP by default, because
 * a forty-row table where three rows matter is a table where those
 * three get scrolled past.
 *
 * @var array $meta
 * @var array $parsed
 * @var array $analysis
 * @var string $token
 * @var array $formulas
 */
$stats  = $analysis['stats'];
$blocked = !empty($analysis['errors']) || $stats['ok'] === 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Check import — <?php echo abom_e($meta['code']); ?></title>
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
    <a class="abom-home" href="<?php echo page_url; ?>abom/import" title="Start again">
      <span class="abom-home-icon" aria-hidden="true">&#8617;</span>
      <span class="abom-home-text">Back</span>
    </a>
    <div>
      <h1>&#128269; Check before importing</h1>
      <div class="sub">
        <?php echo abom_e($filename); ?> &middot; <?php echo abom_e($meta['code']); ?>
        &middot; <?php echo abom_e($meta['machine_model']); ?>
        <?php echo (int) $meta['axes']; ?>A / <?php echo (int) $meta['tracks']; ?>T /
        <?php echo (int) $meta['speed_ppm']; ?> PPM
      </div>
    </div>
    <span class="badge">NOTHING WRITTEN YET</span>
  </header>

  <div class="layout"><main class="main">

    <div class="abom-area">

      <?php if (!empty($commit_error)): ?>
        <div class="imp-error"><b>&#9888;&#65039; <?php echo abom_e($commit_error); ?></b></div>
      <?php endif; ?>

      <div class="imp-stats">
        <div class="stat-card"><div class="val"><?php echo (int) $stats['rows']; ?></div>
          <div class="lbl">Rows read</div></div>
        <div class="stat-card"><div class="val" style="color:var(--success)"><?php echo (int) $stats['ok']; ?></div>
          <div class="lbl">Will import</div></div>
        <div class="stat-card"><div class="val" style="color:var(--red)"><?php echo (int) $stats['errors']; ?></div>
          <div class="lbl">Blocked</div></div>
        <div class="stat-card"><div class="val" style="color:var(--warn)"><?php echo (int) $stats['ambiguous']; ?></div>
          <div class="lbl">Need a decision</div></div>
        <div class="stat-card"><div class="val" style="color:var(--warn)"><?php echo (int) $stats['no_erp']; ?></div>
          <div class="lbl">No ERP code</div></div>
      </div>

      <?php if ($blocked): ?>
        <div class="imp-error">
          <b>&#9888;&#65039; This sheet cannot be imported as it stands.</b>
          <?php foreach ($analysis['errors'] as $e): ?>
            <div>&bull; <?php echo abom_e($e); ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($stats['errors'] > 0 && !$blocked): ?>
        <div class="imp-warnbox">
          <b><?php echo (int) $stats['errors']; ?> row(s) cannot be imported</b> and are
          marked below. The rest will import without them. Fix the sheet and upload it
          again if those rows are needed &mdash; you cannot add them later without
          re-importing the whole build.
        </div>
      <?php endif; ?>

      <?php if (!empty($analysis['warnings'])): ?>
        <div class="imp-warnbox">
          <b>Worth checking across the whole sheet</b>
          <?php foreach ($analysis['warnings'] as $w): ?>
            <div>&bull; <?php echo abom_e($w); ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="abom-master-note">
        <b>Read the QUANTITY RULE column.</b> It is the one thing a spreadsheet cannot tell
        us. A cell saying 7 might be a fixed seven or the track rule that comes to seven on
        this machine &mdash; and the difference only shows up on the next machine of a
        different size. Rows the module could not decide are marked
        <span class="formula-tag tag-review">DECIDE</span> and sorted to the top.
      </div>

      <form method="post" action="<?php echo page_url; ?>abom/import_commit">
        <input type="hidden" name="token" value="<?php echo abom_e($token); ?>">
        <?php foreach ($meta as $k => $v): ?>
          <input type="hidden" name="<?php echo abom_e($k); ?>" value="<?php echo abom_e($v); ?>">
        <?php endforeach; ?>

        <div class="abom-table-wrap">
          <table class="imp-table">
            <thead>
              <tr>
                <th style="width:44px;">ROW</th>
                <th style="width:34px;" title="Leave this row out">&#10005;</th>
                <th class="left" style="width:88px;">ERP</th>
                <th class="left">DESCRIPTION</th>
                <th class="left" style="width:130px;">PART NO.</th>
                <th style="width:48px;">QTY</th>
                <th class="left" style="width:150px;">SECTION</th>
                <th class="left" style="width:300px;">QUANTITY RULE</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Rows needing attention first: errors, then undecided
              // rules, then everything else in sheet order.
              $ordered = $analysis['items'];
              usort($ordered, function ($a, $b) {
                  $rank = function ($x) {
                      if (!empty($x['errors']))  { return 0; }
                      if (empty($x['confident'])) { return 1; }
                      if (!empty($x['warnings'])) { return 2; }
                      return 3;
                  };
                  $ra = $rank($a); $rb = $rank($b);
                  return ($ra === $rb) ? ($a['excel_row'] - $b['excel_row']) : ($ra - $rb);
              });

              foreach ($ordered as $it):
                  $row = (int) $it['excel_row'];
                  $cls = !empty($it['errors']) ? ' is-conflict'
                       : (empty($it['confident']) ? ' is-manual'
                       : (!empty($it['warnings']) ? ' is-noerp' : ''));
              ?>
                <tr class="data-row<?php echo $cls; ?>">
                  <td style="text-align:center;font-size:10px;color:#8A94A6;"><?php echo $row; ?></td>
                  <td style="text-align:center;">
                    <input type="checkbox" name="skip[<?php echo $row; ?>]" value="1"
                           title="Leave this row out of the import"
                           <?php echo !empty($it['errors']) ? 'checked disabled' : ''; ?>>
                  </td>
                  <td class="left">
                    <?php if ($it['erp_code'] === null): ?>
                      <span class="formula-tag tag-pending">NONE</span>
                    <?php else: ?>
                      <span class="erp-cell"><?php echo abom_e($it['erp_code']); ?></span>
                    <?php endif; ?>
                  </td>
                  <td class="left">
                    <?php echo abom_e($it['description']); ?>
                    <?php foreach ($it['errors'] as $e): ?>
                      <div class="imp-rowerr">&#9888;&#65039; <?php echo abom_e($e); ?></div>
                    <?php endforeach; ?>
                    <?php foreach ($it['warnings'] as $w): ?>
                      <div class="imp-rowwarn"><?php echo abom_e($w); ?></div>
                    <?php endforeach; ?>
                  </td>
                  <td class="left" style="font-family:monospace;font-size:10.5px;">
                    <?php echo abom_e($it['part_no']); ?></td>
                  <td style="text-align:center;font-weight:700;"><?php echo abom_e($it['qty_raw']); ?></td>
                  <td class="left" style="font-size:10.5px;"><?php echo abom_e($it['section_name']); ?></td>
                  <td class="left">
                    <?php if (empty($it['confident'])): ?>
                      <span class="formula-tag tag-review">DECIDE</span>
                    <?php endif; ?>
                    <select name="formula[<?php echo $row; ?>]" class="imp-formula">
                      <?php foreach ($formulas as $f): ?>
                        <option value="<?php echo abom_e($f->code); ?>"
                          <?php echo $it['formula'] === $f->code ? ' selected' : ''; ?>>
                          <?php echo abom_e($f->code); ?>
                          <?php if (in_array($f->code, $it['candidates'], true)): ?>&nbsp;&#9679;<?php endif; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <div class="imp-reason"><?php echo abom_e($it['reason']); ?></div>
                    <label class="imp-opt">
                      <input type="checkbox" name="optional[<?php echo $row; ?>]" value="1">
                      optional item
                    </label>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="imp-confirm">
          <?php if ($blocked): ?>
            <b>Nothing can be imported from this sheet.</b>
            <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/import">Start again</a>
          <?php else: ?>
            <div class="imp-confirm-text">
              This will create build <b><?php echo abom_e($meta['code']); ?></b> with
              <b><?php echo (int) $stats['ok']; ?> items</b> and one selection rule.
              No existing build, item or saved BOM is touched.
              <?php if ($stats['ambiguous'] > 0): ?>
                <br><b>&#9888;&#65039; <?php echo (int) $stats['ambiguous']; ?> row(s) still
                marked DECIDE.</b> They will import with the rule shown. Check them first
                &mdash; a wrong rule is correct for this machine and wrong for every other
                size.
              <?php endif; ?>
            </div>
            <button type="submit" class="btn-sm btn-generate"
                    onclick="return confirm('Create build <?php echo abom_e($meta['code']); ?> with <?php echo (int) $stats['ok']; ?> items?\n\nNothing existing is changed. You can edit or retire the build afterwards.');">
              &#10003; Import <?php echo (int) $stats['ok']; ?> items
            </button>
            <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/import">Cancel</a>
          <?php endif; ?>
        </div>
      </form>

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
