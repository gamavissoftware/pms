<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * master_form.php — add or edit one master item.
 *
 * Reached only through Abom::master_form(), which requires 'master_edit'.
 *
 * Every field that drives the ENGINE is grouped together and explained
 * inline, because the consequences are not guessable from the label:
 * "Formula" decides whether a quantity moves with the axis count, and
 * "Build" decides which machines the item appears on at all. Getting
 * either wrong is silent — the BOM generates, it is just wrong.
 *
 * plc_family_id is NOT on this form. It is derived from the build, in
 * Abom_item_model::payload(). The two disagreeing is what put an iQ-R
 * build under an FX5 heading on ABOM-5.
 *
 * @var object|null $item
 * @var int         $item_id   0 = new
 * @var array       $errors    field => message
 * @var array       $variants  id => row
 * @var array       $sections  flat list, every family
 * @var array       $formulas  code => row
 * @var array       $features  code => row
 * @var array       $families  id => row
 * @var int         $uses      saved BOM lines generated from this item
 * @var string      $message   flashdata
 */

$item    = isset($item) ? $item : null;
$errors  = isset($errors) ? $errors : array();
$item_id = isset($item_id) ? (int) $item_id : 0;
$uses    = isset($uses) ? (int) $uses : 0;

$val = function ($field, $default = '') use ($item) {
    if ($item === null || !isset($item->$field) || $item->$field === null) {
        return $default;
    }
    return $item->$field;
};

$err = function ($field) use ($errors) {
    return isset($errors[$field])
        ? '<span class="field-error">' . abom_e($errors[$field]) . '</span>' : '';
};

$inv = function ($field) use ($errors) {
    return isset($errors[$field]) ? ' is-invalid' : '';
};
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> Automation BOM — <?php echo $item_id ? 'Edit' : 'New'; ?> Master Item</title>
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
    <a class="abom-home" href="<?php echo page_url; ?>abom/master" title="Back to master items">
      <span class="abom-home-icon" aria-hidden="true">&#8592;</span>
      <span class="abom-home-text">Master items</span>
    </a>
    <div>
      <h1>&#9881;&#65039; <?php echo $item_id ? 'Edit master item ' . (int) $item_id : 'New master item'; ?></h1>
      <div class="sub">Affects every BOM generated from now on &mdash; not any already saved</div>
    </div>
    <span class="badge review">MASTER DATA</span>
  </header>

  <div class="layout">
    <main class="main">
      <div class="abom-area">

        <?php if (!empty($message)): ?>
          <div class="abom-master-ok">&#10003; <?php echo abom_e($message); ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="abom-master-err">
            <b>Nothing was saved.</b> <?php echo count($errors); ?>
            field<?php echo count($errors) === 1 ? '' : 's'; ?> below need correcting.
            Everything you typed has been kept.
          </div>
        <?php endif; ?>

        <?php if ($item_id > 0 && $uses > 0): ?>
          <div class="abom-master-note">
            <b><?php echo $uses; ?></b> saved BOM line<?php echo $uses === 1 ? '' : 's'; ?>
            <?php echo $uses === 1 ? 'was' : 'were'; ?> generated from this item.
            <b>They will not change.</b> Each holds its own frozen copy &mdash; editing here
            only affects BOMs generated after you save.
          </div>
        <?php endif; ?>

        <form method="post" action="<?php echo page_url; ?>abom/master_save" class="abom-master-form">
          <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">

          <h3 class="abom-fs-title">What the part is</h3>
          <div class="abom-fs">
            <div class="form-group">
              <label for="fDesc">Description <span class="req">*</span></label>
              <input id="fDesc" name="description" maxlength="255"
                     class="<?php echo trim($inv('description')); ?>"
                     value="<?php echo abom_e($val('description')); ?>">
              <?php echo $err('description'); ?>
            </div>

            <div class="abom-fs-row">
              <div class="form-group">
                <label for="fErp">ERP code</label>
                <input id="fErp" name="erp_code" maxlength="32"
                       value="<?php echo abom_e($val('erp_code')); ?>"
                       placeholder="blank = not yet created">
                <span class="fi-help">Leave blank if procurement has not created one.
                  The line is then flagged ERP PENDING on every BOM.</span>
              </div>
              <div class="form-group">
                <label for="fPart">Part / model no.</label>
                <input id="fPart" name="part_no" maxlength="96"
                       value="<?php echo abom_e($val('part_no')); ?>">
              </div>
            </div>

            <div class="abom-fs-row">
              <div class="form-group">
                <label for="fMfr">Manufacturer</label>
                <input id="fMfr" name="manufacturer" maxlength="64"
                       value="<?php echo abom_e($val('manufacturer', 'MITSUBISHI')); ?>">
              </div>
              <div class="form-group">
                <label for="fUom">UOM <span class="req">*</span></label>
                <input id="fUom" name="uom" maxlength="16"
                       class="<?php echo trim($inv('uom')); ?>"
                       value="<?php echo abom_e($val('uom', 'NOS')); ?>">
                <?php echo $err('uom'); ?>
                <span class="fi-help">Stored as NOS; printed as NO(S).</span>
              </div>
            </div>
          </div>

          <h3 class="abom-fs-title">Which machines it appears on</h3>
          <div class="abom-fs">
            <div class="abom-fs-row">
              <div class="form-group">
                <label for="fVariant">Build <span class="req">*</span></label>
                <select id="fVariant" name="variant_id" class="<?php echo trim($inv('variant_id')); ?>">
                  <option value="">Choose a build…</option>
                  <?php foreach ($variants as $v): ?>
                    <option value="<?php echo (int) $v->id; ?>"
                            data-family="<?php echo (int) $v->plc_family_id; ?>"
                            <?php echo ((int) $val('variant_id') === (int) $v->id) ? ' selected' : ''; ?>>
                      <?php echo abom_e($v->code . ' — ' . $v->name); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php echo $err('variant_id'); ?>
                <span class="fi-help">The item appears ONLY on this build. The PLC family
                  follows from it automatically.</span>
              </div>

              <div class="form-group">
                <label for="fSection">Section <span class="req">*</span></label>
                <select id="fSection" name="section_id" class="<?php echo trim($inv('section_id')); ?>">
                  <option value="">Choose a section…</option>
                  <?php foreach ($sections as $sec): ?>
                    <option value="<?php echo (int) $sec->id; ?>"
                            data-family="<?php echo (int) $sec->plc_family_id; ?>"
                            <?php echo ((int) $val('section_id') === (int) $sec->id) ? ' selected' : ''; ?>>
                      <?php echo abom_e($sec->code . ' — ' . $sec->name); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php echo $err('section_id'); ?>
                <span class="fi-help">Filtered to the build's PLC family. Decides the
                  grouping heading and the printed order.</span>
              </div>
            </div>

            <div class="abom-fs-row">
              <label class="feature-item">
                <input type="checkbox" name="is_optional" value="1"
                       id="fOptional"<?php echo !empty($val('is_optional')) ? ' checked' : ''; ?>>
                <span>Optional &mdash; only included when a feature is switched on
                  <span class="fi-help">An unticked optional item is left off the BOM entirely,
                    not shown with quantity zero.</span></span>
              </label>

              <div class="form-group">
                <label for="fFeature">Feature gate</label>
                <select id="fFeature" name="feature_code" class="<?php echo trim($inv('feature_code')); ?>">
                  <option value="">None</option>
                  <?php foreach ($features as $code => $f): ?>
                    <option value="<?php echo abom_e($code); ?>"
                            <?php echo ((string) $val('feature_code') === (string) $code) ? ' selected' : ''; ?>>
                      <?php echo abom_e($f->label); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php echo $err('feature_code'); ?>
              </div>
            </div>
            <?php echo $err('is_optional'); ?>
          </div>

          <h3 class="abom-fs-title">How its quantity is worked out</h3>
          <div class="abom-fs">
            <div class="abom-fs-row">
              <div class="form-group">
                <label for="fFormula">Formula <span class="req">*</span></label>
                <select id="fFormula" name="formula_code" class="<?php echo trim($inv('formula_code')); ?>">
                  <?php foreach ($formulas as $code => $f): ?>
                    <option value="<?php echo abom_e($code); ?>"
                            <?php echo ((string) $val('formula_code', 'FIXED') === (string) $code) ? ' selected' : ''; ?>>
                      <?php echo abom_e($code . ' — ' . $f->name); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php echo $err('formula_code'); ?>
              </div>
              <div class="form-group">
                <label for="fBase">Base quantity</label>
                <input id="fBase" name="base_qty" type="number" min="0" max="65535"
                       class="<?php echo trim($inv('base_qty')); ?>"
                       value="<?php echo (int) $val('base_qty', 1); ?>">
                <?php echo $err('base_qty'); ?>
              </div>
            </div>

            <div class="abom-formula-help">
              <?php foreach ($formulas as $code => $f): ?>
                <div class="afh" data-formula="<?php echo abom_e($code); ?>">
                  <b><?php echo abom_e($code); ?></b> &mdash; <?php echo abom_e($f->description); ?>
                  <?php if ($code === 'FIXED' || $code === 'MANUAL'): ?>
                    <i>Base quantity is used.</i>
                  <?php else: ?>
                    <i>Base quantity is ignored &mdash; it is kept only as the reference-BOM value.</i>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <h3 class="abom-fs-title">Notes and flags</h3>
          <div class="abom-fs">
            <div class="form-group">
              <label for="fUsage">Usage remark</label>
              <input id="fUsage" name="usage_remark" maxlength="255"
                     value="<?php echo abom_e($val('usage_remark')); ?>"
                     placeholder="e.g. HORZ. + VERT.">
              <span class="fi-help">Reference material. Shown to the engineer as the greyed
                placeholder in the REMARKS box; it does not print.</span>
            </div>

            <div class="form-group">
              <label for="fIssue">Data issue</label>
              <input id="fIssue" name="data_issue" maxlength="255"
                     value="<?php echo abom_e($val('data_issue')); ?>">
              <span class="fi-help">Engineering note. Appears in the "Points to verify"
                banner and in the PDF.</span>
            </div>

            <div class="abom-fs-row">
              <div class="form-group">
                <label for="fSeverity">Flag</label>
                <select id="fSeverity" name="issue_severity" class="<?php echo trim($inv('issue_severity')); ?>">
                  <?php foreach (array('none' => 'Clean', 'review' => 'Review',
                                       'no_erp' => 'No ERP code', 'conflict' => 'ERP conflict') as $k => $label): ?>
                    <option value="<?php echo abom_e($k); ?>"
                            <?php echo ((string) $val('issue_severity', 'none') === (string) $k) ? ' selected' : ''; ?>>
                      <?php echo abom_e($label); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php echo $err('issue_severity'); ?>
              </div>
              <div class="form-group">
                <label for="fPanel">Panel location</label>
                <input id="fPanel" name="panel_location" maxlength="64"
                       value="<?php echo abom_e($val('panel_location')); ?>">
              </div>
            </div>

            <div class="abom-fs-row">
              <div class="form-group">
                <label for="fSource">Source DF</label>
                <input id="fSource" name="source_df" maxlength="32"
                       value="<?php echo abom_e($val('source_df')); ?>"
                       placeholder="e.g. DF-1808">
                <span class="fi-help">Provenance &mdash; which reference BOM this came from.</span>
              </div>
              <label class="feature-item">
                <input type="checkbox" name="is_active" value="1"
                       <?php echo ($item_id === 0 || !empty($val('is_active'))) ? ' checked' : ''; ?>>
                <span>Active
                  <span class="fi-help">Unticked, the item is never generated onto a new BOM.
                    BOMs already containing it are unaffected.</span></span>
              </label>
            </div>
          </div>

          <div class="abom-form-actions">
            <button type="submit" class="btn-sm btn-generate">
              &#128190; <?php echo $item_id ? 'Save changes' : 'Create item'; ?>
            </button>
            <a class="btn-sm btn-copy" href="<?php echo page_url; ?>abom/master">Cancel</a>
          </div>
        </form>

      </div>
    </main>
  </div>
</div>

<?php $this->load->view('common/footer'); ?>

<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
<script src="<?php echo abom_asset('abom/abom-master.js'); ?>"></script>
</body>
</html>
