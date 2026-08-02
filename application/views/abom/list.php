<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
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
    <link href="<?php echo assets_url; ?>abom/abom.css" rel="stylesheet">
    <link href="<?php echo assets_url; ?>abom/abom-print.css" rel="stylesheet" media="all">
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
          <input type="text" name="search" placeholder="BOM no / DF ref"
                 value="<?php echo abom_e($filters['search']); ?>">
          <button type="submit" class="btn-sm btn-print">Filter</button>
        </form>
        <div class="abom-actions">
          <a class="btn-sm btn-generate" href="<?php echo page_url; ?>abom/generate">&#43; New BOM</a>
          <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/guide">&#10068; Guide</a>
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
                  <th style="width:70px;">LINES</th>
                  <th style="width:70px;">TOTAL QTY</th>
                  <th style="width:80px;">ISSUES</th>
                  <th style="width:130px;">STATUS</th>
                  <th style="width:110px;">CREATED</th>
                  <th style="width:120px;">&nbsp;</th>
                </tr>
              </thead>
              <tbody>
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
                    <td style="text-align:center;">
                      <a class="btn-sm btn-print" href="<?php echo page_url; ?>abom/view/<?php echo (int) $bom->id; ?>">Open</a>
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
</body>
</html>
