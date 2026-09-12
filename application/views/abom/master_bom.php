<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * master_bom.php — one uploaded BOM's items, in printed order, editable
 * in place.
 *
 * Reached only through Abom::master_bom(), which requires 'master_edit'.
 *
 * THIS IS MASTER DATA. Every field here feeds every BOM generated from
 * this build from now on. It feeds NO BOM already saved — abom_bom_line
 * holds its own frozen copy of every line — and the banner says so,
 * because "editing the BOM master" is exactly the phrase that makes
 * people fear they have altered documents already signed.
 *
 * $posted is the rows as they were typed, handed back when a save is
 * refused so corrections are made to those and not to what is stored.
 *
 * @var object      $variant
 * @var object|null $family
 * @var array       $items     every item of this build, active and retired
 * @var array|null  $posted
 * @var array       $errors    item ref => [field => msg], plus '_form'
 * @var array       $sections  this family's sections
 * @var array       $formulas
 * @var array       $features
 * @var string      $message
 */

$errors = isset($errors) ? $errors : array();
$posted = isset($posted) && is_array($posted) ? $posted : null;

// When a save was refused, render from what was typed. Keyed by item id
// so an existing row finds its posted values; new rows are appended.
$by_id = array();
$new_rows = array();
if ($posted !== null) {
    foreach ($posted as $n => $row) {
        if (!is_array($row)) { continue; }
        $id = isset($row['id']) ? (int) $row['id'] : 0;
        if ($id > 0) { $by_id[$id] = $row; } else { $new_rows[] = $row; }
    }
}

/** Field value: posted if we have it, else stored. */
$val = function ($item, $field) use ($by_id) {
    $id = (int) $item->id;
    if (isset($by_id[$id]) && array_key_exists($field, $by_id[$id])) {
        return $by_id[$id][$field];
    }
    return isset($item->$field) ? $item->$field : '';
};

$err = function ($item, $field) use ($errors) {
    $ref = (int) $item->id;
    return isset($errors[$ref][$field]) ? $errors[$ref][$field] : '';
};

$current_section = null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> — <?php echo abom_e($variant->code); ?> master sheet</title>
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
    <a class="abom-home" href="<?php echo page_url; ?>abom/master_bom" title="Reference BOMs">
      <span class="abom-home-icon" aria-hidden="true">&#8592;</span>
      <span class="abom-home-text">Reference BOMs</span>
    </a>
    <div>
      <h1>&#128193; <?php echo abom_e($variant->code); ?> &mdash; master sheet</h1>
      <div class="sub">
        <?php echo abom_e($variant->name); ?>
        <?php if (!empty($variant->source_df)): ?>
          &middot; from <?php echo abom_e($variant->source_df); ?>
        <?php endif; ?>
      </div>
    </div>
    <span class="badge review">MASTER DATA</span>
  </header>

  <div class="layout"><main class="main">

    <div class="abom-topbar">
      <span class="config-chip lite"><?php echo abom_e($variant->machine_model); ?></span>
      <span class="config-chip <?php echo abom_chip_class($family ? $family->code : ''); ?>">
        <?php echo abom_e($family ? $family->name : ''); ?></span>
      <span class="config-chip lite"><?php echo abom_e($variant->default_panel_location); ?></span>
      <span class="config-chip lite"><?php echo count($items); ?> items</span>
      <div class="abom-actions">
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master_form/0">&#43; Full item form</a>
        <a class="btn-sm btn-copy"
           href="<?php echo page_url; ?>abom/config_form/variant/<?php echo (int) $variant->id; ?>">Edit build</a>
        <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master_bom">All reference BOMs</a>
      </div>
    </div>

    <div class="abom-area">
      <?php if (!empty($message)): ?>
        <div class="abom-master-ok">&#10003; <?php echo abom_e($message); ?></div>
      <?php endif; ?>

      <?php if (!empty($errors['_form'])): ?>
        <div class="abom-master-err">
          <b>Nothing was saved.</b> <?php echo abom_e($errors['_form']); ?>
          The rows below are as you typed them.
        </div>
      <?php endif; ?>

      <div class="abom-master-note">
        <b>This is the master sheet for <?php echo abom_e($variant->code); ?>.</b>
        Every BOM generated for this build <b>from now on</b> comes from these rows.
        No BOM already saved changes &mdash; each holds its own frozen copy.
        Removing a row <b>retires</b> it: it stops appearing on new BOMs and stays on old ones.
      </div>

      <?php
      /**
       * SOURCE-BOM FILTER.
       *
       * Built from the DFs actually present on this build's items, so a
       * build assembled from one sheet shows no filter at all and one
       * assembled from five shows exactly those five. Nothing is
       * hard-coded and nothing is hidden from the SAVE — filtering only
       * hides rows on screen; every row is still collected and posted,
       * because a filter that silently narrowed what gets saved would
       * retire everything the operator could not see.
       */
      $source_dfs = array();
      foreach ($items as $it) {
          foreach (preg_split('/\s*,\s*/', (string) $it->source_df) as $df) {
              $df = trim($df);
              if ($df !== '') { $source_dfs[$df] = isset($source_dfs[$df]) ? $source_dfs[$df] + 1 : 1; }
          }
      }
      ksort($source_dfs);
      ?>
      <?php if (count($source_dfs) > 1): ?>
        <div class="filter-pills" id="abomSrcPills">
          <button type="button" class="pill active" data-src="ALL">
            All <?php echo count($items); ?> items</button>
          <?php foreach ($source_dfs as $df => $n): ?>
            <button type="button" class="pill" data-src="<?php echo abom_e($df); ?>">
              <?php echo abom_e($df); ?> (<?php echo (int) $n; ?>)</button>
          <?php endforeach; ?>
        </div>
        <p class="abom-rowedit-note" style="margin:0 0 10px;">
          This build is assembled from <b><?php echo count($source_dfs); ?> uploaded BOMs</b>.
          They are the same machine at different sizes, so they share one catalogue and their
          quantities are calculated rather than stored. Filter to see what any one of them
          contained. <b>Filtering never changes what is saved</b> &mdash; every row is posted
          whether or not it is on screen.
        </p>
      <?php endif; ?>

      <form method="post" action="<?php echo page_url; ?>abom/master_bom_save" id="abomBuildForm">
        <input type="hidden" name="variant_id" value="<?php echo (int) $variant->id; ?>">
        <input type="hidden" name="rows" id="abomBuildRows" value="">

        <div class="abom-table-wrap">
          <table id="abomTable" class="abom-build-sheet">
            <thead>
              <tr>
                <th style="width:40px;">#</th>
                <th style="width:96px;">ERP CODE</th>
                <th class="left" style="width:240px;">DESCRIPTION</th>
                <th class="left" style="width:150px;">PART NO.</th>
                <th style="width:104px;">MANUFACTURER</th>
                <th style="width:58px;">BASE</th>
                <th style="width:120px;">FORMULA</th>
                <th style="width:56px;">UOM</th>
                <th class="left" style="width:150px;">USAGE REMARK</th>
                <th class="left" style="width:150px;">SOURCE BOM</th>
                <th style="width:92px;">FLAG</th>
                <th class="col-actions" style="width:44px;">&nbsp;</th>
              </tr>
            </thead>
            <tbody id="abomBuildBody">
              <?php $n = 0; foreach ($items as $item): ?>

                <?php if ($item->section_name !== $current_section): ?>
                  <?php $current_section = $item->section_name; ?>
                  <tr class="sec-row"><td colspan="12"><?php echo abom_e($current_section); ?></td></tr>
                <?php endif; ?>

                <?php $n++; $row_err = isset($errors[(int) $item->id]) ? $errors[(int) $item->id] : array(); ?>
                <tr class="data-row<?php echo empty($val($item, 'is_active')) ? ' is-retired' : ''; ?><?php echo !empty($row_err) ? ' has-row-error' : ''; ?>"
                    data-id="<?php echo (int) $item->id; ?>"
                    data-section="<?php echo (int) $item->section_id; ?>"
                    data-src="<?php echo abom_e($val($item, 'source_df')); ?>">
                  <td style="text-align:center;font-weight:700;" class="sno-cell"><?php echo $n; ?></td>
                  <td><input type="text" class="mi" data-f="erp_code" maxlength="32"
                             value="<?php echo abom_e($val($item, 'erp_code')); ?>"
                             placeholder="none"></td>
                  <td><input type="text" class="mi<?php echo isset($row_err['description']) ? ' is-invalid' : ''; ?>"
                             data-f="description" maxlength="255"
                             value="<?php echo abom_e($val($item, 'description')); ?>"></td>
                  <td><input type="text" class="mi" data-f="part_no" maxlength="96"
                             value="<?php echo abom_e($val($item, 'part_no')); ?>"></td>
                  <td><input type="text" class="mi" data-f="manufacturer" maxlength="64"
                             value="<?php echo abom_e($val($item, 'manufacturer')); ?>"></td>
                  <td><input type="number" class="mi<?php echo isset($row_err['base_qty']) ? ' is-invalid' : ''; ?>"
                             data-f="base_qty" min="0" max="65535"
                             value="<?php echo (int) $val($item, 'base_qty'); ?>"></td>
                  <td>
                    <select class="mi" data-f="formula_code">
                      <?php foreach ($formulas as $code => $f): ?>
                        <option value="<?php echo abom_e($code); ?>"
                          <?php echo ((string) $val($item, 'formula_code') === (string) $code) ? ' selected' : ''; ?>>
                          <?php echo abom_e($code); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td><input type="text" class="mi" data-f="uom" maxlength="16"
                             value="<?php echo abom_e($val($item, 'uom')); ?>"></td>
                  <td><input type="text" class="mi" data-f="usage_remark" maxlength="255"
                             value="<?php echo abom_e($val($item, 'usage_remark')); ?>"
                             placeholder="e.g. HORZ. + VERT."></td>
                  <?php
                  /**
                   * WHICH UPLOADED BOMs THIS ITEM CAME FROM.
                   *
                   * A build assembled from five sheets shares one
                   * catalogue — that is what makes quantities
                   * calculable — but "which of the five actually has
                   * this part" is a real question and had no answer
                   * before. Carried in data-src as well so the filter
                   * above can work on it without parsing the input.
                   */
                  ?>
                  <td><input type="text" class="mi mi-src" data-f="source_df" maxlength="32"
                             value="<?php echo abom_e($val($item, 'source_df')); ?>"
                             placeholder="which sheets"></td>
                  <td>
                    <select class="mi" data-f="issue_severity">
                      <?php foreach (array('none' => 'Clean', 'review' => 'Review',
                                           'no_erp' => 'No ERP', 'conflict' => 'Conflict') as $k => $label): ?>
                        <option value="<?php echo abom_e($k); ?>"
                          <?php echo ((string) $val($item, 'issue_severity') === (string) $k) ? ' selected' : ''; ?>>
                          <?php echo abom_e($label); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td class="col-actions">
                    <a class="row-act" title="Full item form — section, build, feature gate"
                       href="<?php echo page_url; ?>abom/master_form/<?php echo (int) $item->id; ?>">&#9998;</a>
                    <button type="button" class="row-act row-del"
                            title="Retire this item from the build">&times;</button>
                  </td>
                </tr>

                <?php if (!empty($row_err)): ?>
                  <tr class="row-error-row">
                    <td colspan="12">
                      <?php foreach ($row_err as $msg): ?>
                        <span class="field-error">&bull; <?php echo abom_e($msg); ?></span>
                      <?php endforeach; ?>
                    </td>
                  </tr>
                <?php endif; ?>

              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="lines-save-bar">
          <span class="lsb-state" id="abomBuildState">
            Edit any cell, then save. Removing a row retires the item.
          </span>
          <select id="abomNewSection" title="Section for a new item">
            <?php foreach ($sections as $sec): ?>
              <option value="<?php echo (int) $sec->id; ?>">
                <?php echo abom_e($sec->code . ' — ' . $sec->name); ?></option>
            <?php endforeach; ?>
          </select>
          <button type="button" class="btn-sm btn-copy" id="abomAddItem">&#43; Add item</button>
          <button type="submit" class="btn-sm btn-generate" id="abomSaveBuild">&#128190; Save sheet</button>
        </div>

        <div class="row-restore-bar" id="abomRetireBar" hidden></div>
      </form>

      <p class="abom-rowedit-note">
        The pencil on each row opens the <b>full item form</b>, where the section, the build,
        the feature gate and the data-issue note can also be changed. This sheet covers the
        fields that get corrected most often.
      </p>
    </div>

  </main></div>
</div>
<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script src="<?php echo abom_asset('abom/abom-masterbom.js'); ?>"></script>
</body>
</html>
