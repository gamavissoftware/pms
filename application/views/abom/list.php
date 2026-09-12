<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
// A caller that forgets the rule gets the safe answer: Delete is offered
// on drafts only.
$deletable_status = isset($deletable_status) && is_array($deletable_status) && !empty($deletable_status)
    ? $deletable_status
    : array('draft', 'rejected');

$manufacturers = isset($manufacturers) && is_array($manufacturers) ? $manufacturers : array();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Saved Automation BOMs</title>
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

<?php
/**
 * The application chrome (common/nav-menu, common/info-section) is
 * DELIBERATELY NOT LOADED on this module's screens.
 *
 * Two reasons:
 *   1. nav-menu renders a fixed left sidebar, and the theme offsets the
 *      content for it via `<div class="wrapper">`. This module's document
 *      layout is not a .wrapper page — it is a full-bleed sheet — so the
 *      sidebar overlapped the BOM table.
 *   2. A BOM is a wide document: 9 columns plus editable quantities. The
 *      ~250px the sidebar costs is the difference between the table
 *      fitting and scrolling horizontally.
 *
 * common/nav-menu.php itself is UNCHANGED and still carries this module's
 * menu entry, so the module is reached from any other page as normal.
 * Navigation back out is the home button in .app-header.
 *
 * info-section.php is not loaded either: its entire body is wrapped in an
 * HTML comment, so it renders nothing while still running a query
 * against system_reports.
 */
?>

<div class="abom-wrap">

  <header class="app-header">
    <a class="abom-home" href="<?php echo page_url; ?>Dashboard" title="Back to Dashboard">
      <span class="abom-home-icon" aria-hidden="true">&#8962;</span>
      <span class="abom-home-text">Dashboard</span>
    </a>
    <div>
      <h1>&#9881;&#65039; Automation BOM Generator</h1>
      <div class="sub">Saved bills of material</div>
    </div>
    <span class="badge"><?php echo count($boms); ?> SAVED</span>
  </header>

  <div class="layout">
    <main class="main">

      <div class="abom-topbar">
        <form method="get" action="<?php echo page_url; ?>abom/list" class="abom-filter-form">
          <select name="status">
            <option value="">All statuses</option>
            <?php foreach ($statuses as $status): ?>
              <option value="<?php echo abom_e($status); ?>"<?php echo $filters['status'] === $status ? ' selected' : ''; ?>>
                <?php echo abom_e(abom_status_label($status)); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select name="model">
            <option value="">All models</option>
            <?php foreach ($models as $model): ?>
              <option value="<?php echo abom_e($model); ?>"<?php echo $filters['machine_model'] === $model ? ' selected' : ''; ?>>
                <?php echo abom_e($model); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php
          /**
           * MANUFACTURER filter. Hidden entirely while only one brand is
           * on record — a dropdown with a single option is furniture,
           * not a control. It appears on its own as soon as a second
           * brand reaches a saved BOM.
           *
           * The options are read from the saved BOMs, so this needs no
           * maintenance as more manufacturers arrive.
           */
          ?>
          <?php if (count($manufacturers) > 1): ?>
            <select name="mfr">
              <option value="">All manufacturers</option>
              <?php foreach ($manufacturers as $mfr): ?>
                <option value="<?php echo abom_e($mfr); ?>"<?php echo $filters['manufacturer'] === $mfr ? ' selected' : ''; ?>>
                  <?php echo abom_e($mfr); ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php endif; ?>
          <input type="text" name="search" placeholder="BOM no / DF ref"
                 value="<?php echo abom_e($filters['search']); ?>">
          <button type="submit" class="btn-sm btn-print">Filter</button>
        </form>
        <div class="abom-actions">
          <a class="btn-sm btn-generate" href="<?php echo page_url; ?>abom/generate">&#43; New BOM</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/guide">&#10068; Guide</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master">&#128295; Master items</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master_bom">&#128193; Reference BOMs</a>
        </div>
      </div>

      <div class="abom-area">
        <?php if (empty($boms)): ?>
          <div class="empty-state">
            <div class="icon">&#128203;</div>
            <h3>No saved BOMs yet</h3>
            <p>Generate a configuration and save it to see it listed here.</p>
          </div>
        <?php else: ?>
          <div class="abom-table-wrap">
            <table>
              <thead>
                <tr>
                  <th style="width:120px;">BOM NO.</th>
                  <th style="width:60px;">REV</th>
                  <th style="width:100px;">DF REF</th>
                  <th class="left" style="width:110px;">MODEL</th>
                  <th style="width:190px;">CONFIGURATION</th>
                  <th style="width:100px;">PLC FAMILY</th>
                  <th style="width:110px;">BUILD</th>
                  <th class="left" style="width:130px;">MANUFACTURER</th>
                  <th style="width:70px;">LINES</th>
                  <th style="width:70px;">TOTAL QTY</th>
                  <th style="width:80px;">ISSUES</th>
                  <th style="width:130px;">STATUS</th>
                  <th style="width:110px;">CREATED</th>
                  <th style="width:120px;">&nbsp;</th>
                </tr>
              </thead>
              <tbody id="abomBomRows">
                <?php foreach ($boms as $bom): ?>
                  <tr class="data-row<?php echo (int) $bom->open_issues > 0 ? ' is-noerp' : ''; ?>">
                    <td style="text-align:center;"><span class="erp-cell"><?php echo abom_e($bom->bom_no); ?></span></td>
                    <td style="text-align:center;"><?php echo abom_e($bom->revision); ?></td>
                    <td style="text-align:center;"><?php echo $bom->df_ref !== null && $bom->df_ref !== '' ? abom_e($bom->df_ref) : '&mdash;'; ?></td>
                    <td><?php echo abom_e($bom->machine_model); ?></td>
                    <td style="text-align:center;">
                      <?php echo (int) $bom->axes; ?>A ·
                      <?php echo (int) $bom->tracks; ?>T ·
                      <?php echo (int) $bom->speed_ppm; ?> PPM ·
                      <?php echo abom_e($bom->motion_type); ?>
                    </td>
                    <td style="text-align:center;">
                      <span class="formula-tag <?php echo $bom->family_code === 'FX5' ? 'tag-manual' : 'tag-review'; ?>">
                        <?php echo abom_e($bom->family_code); ?>
                      </span>
                      <?php if (!empty($bom->plc_family_locked)): ?>
                        <br><span class="formula-tag tag-pending">OVERRIDDEN</span>
                      <?php endif; ?>
                    </td>
                    <?php
                    // The family alone does not identify the machine: two
                    // FX5 builds carry different servo ranges. Anyone
                    // comparing two rows here needs the build to tell
                    // them apart.
                    ?>
                    <td style="text-align:center;">
                      <?php if (!empty($bom->variant_code)): ?>
                        <span class="formula-tag <?php echo $bom->family_code === 'FX5' ? 'tag-manual' : 'tag-review'; ?>">
                          <?php echo abom_e($bom->variant_code); ?>
                        </span>
                        <?php if (!empty($bom->variant_locked)): ?>
                          <br><span class="formula-tag tag-pending">OVERRIDDEN</span>
                        <?php endif; ?>
                      <?php else: ?>
                        &mdash;
                      <?php endif; ?>
                    </td>
                    <?php
                    /**
                     * MANUFACTURER is derived from the LINES, because
                     * that is where it lives — there is no brand column
                     * on the header and there should not be one. Nearly
                     * every BOM is one brand of automation plus a
                     * bought-in part or two, so the brands are ordered by
                     * how many lines each supplies and the machine's
                     * actual brand comes first. Same rule the export
                     * filename uses, so the list and the file agree.
                     *
                     * The rest are a count rather than a wrapped list:
                     * on a register you scan down one column, and three
                     * brand names per row would bury the one that
                     * matters. The full list is on hover.
                     */
                    $mfrs = isset($bom->manufacturers) ? $bom->manufacturers : array();
                    ?>
                    <td>
                      <?php if (empty($mfrs)): ?>
                        &mdash;
                      <?php else: ?>
                        <?php echo abom_e($mfrs[0]); ?>
                        <?php if (count($mfrs) > 1): ?>
                          <span class="mfr-more" title="<?php echo abom_e(implode(', ', $mfrs)); ?>">+<?php echo count($mfrs) - 1; ?></span>
                        <?php endif; ?>
                      <?php endif; ?>
                    </td>
                    <td style="text-align:center;font-weight:700;"><?php echo (int) $bom->total_lines; ?></td>
                    <td style="text-align:center;"><?php echo (int) $bom->total_qty; ?></td>
                    <td style="text-align:center;">
                      <?php if ((int) $bom->open_issues > 0): ?>
                        <span class="formula-tag tag-pending"><?php echo (int) $bom->open_issues; ?> OPEN</span>
                      <?php else: ?>
                        <span class="tag-ok">&mdash;</span>
                      <?php endif; ?>
                    </td>
                    <td style="text-align:center;"><?php echo abom_e(abom_status_label($bom->status)); ?></td>
                    <td style="text-align:center;font-size:11px;">
                      <?php echo $bom->created_at ? abom_e(date('d-m-Y', strtotime($bom->created_at))) : '&mdash;'; ?>
                    </td>
                    <?php
                    /**
                     * Duplicate is offered on every BOM, including an
                     * approved one — that is the commonest case, because
                     * the next machine is usually nearly the last one.
                     * The copy is always a fresh draft.
                     *
                     * Delete is offered only where the status rule in
                     * $config['abom_deletable_status'] allows it. The
                     * button is HIDDEN rather than shown-and-refused, so
                     * nobody forms the habit of clicking it on documents
                     * that carry signatures. The server enforces the same
                     * rule regardless — see Abom::delete_bom().
                     */
                    $may_delete = in_array($bom->status, $deletable_status, true);
                    ?>
                    <td style="text-align:center;white-space:nowrap;">
                      <a class="btn-sm btn-print" href="<?php echo page_url; ?>abom/view/<?php echo (int) $bom->id; ?>">Open</a>
                      <button type="button" class="btn-sm btn-copy abom-duplicate"
                              data-bom="<?php echo (int) $bom->id; ?>"
                              data-bomno="<?php echo abom_e($bom->bom_no); ?>"
                              title="Create a new draft BOM copied from this one">&#128203; Duplicate</button>
                      <?php if ($may_delete): ?>
                        <button type="button" class="btn-sm abom-delete"
                                data-bom="<?php echo (int) $bom->id; ?>"
                                data-bomno="<?php echo abom_e($bom->bom_no); ?>"
                                data-lines="<?php echo (int) $bom->total_lines; ?>"
                                title="Remove this BOM from the register">&#128465; Delete</button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>

<?php $this->load->view('common/footer'); ?>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<?php
// AFTER jQuery. This page had no module script until now, and the
// existing block sits below the footer include, so the new one has to
// join the end of it rather than the end of the markup.
?>
<script>
  // The METHOD names, not the pretty routes. CodeIgniter's default
  // routing reaches these with no routes.php entry, so this feature
  // works on an install whose routes.php has not been updated —
  // that file is shared with the rest of the application and is not
  // safe to overwrite wholesale. The explicit /abom/delete/ and
  // /abom/duplicate/ aliases exist too, for fresh installs.
  window.ABOM_DELETE_URL    = '<?php echo page_url; ?>abom/delete_bom/';
  window.ABOM_DUPLICATE_URL = '<?php echo page_url; ?>abom/duplicate/';
</script>
<script src="<?php echo abom_asset('abom/abom-list.js'); ?>"></script>
</body>
</html>
