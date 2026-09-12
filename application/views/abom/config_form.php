<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * config_form.php — add or edit one configuration row.
 *
 * Generic: every field is rendered from the descriptor, so all six
 * tables share one form and one set of validation messages.
 *
 * Reached only through Abom::config_form(), which requires 'master_edit'.
 *
 * @var string      $entity
 * @var array       $d       descriptor
 * @var object|null $row
 * @var mixed       $row_id  null = new
 * @var array       $errors  field => message
 * @var array       $refs    ref name => [key => label]
 */

$is_new = ($row_id === null);

$val = function ($field, $spec) use ($row) {
    if ($row === null || !isset($row->$field) || $row->$field === null) {
        return isset($spec['default']) ? $spec['default'] : '';
    }
    return $row->$field;
};
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> — <?php echo $is_new ? 'New' : 'Edit'; ?> <?php echo abom_e($d['label']); ?></title>
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
    <a class="abom-home" href="<?php echo page_url; ?>abom/config_list/<?php echo abom_e($entity); ?>">
      <span class="abom-home-icon" aria-hidden="true">&#8592;</span>
      <span class="abom-home-text"><?php echo abom_e($d['plural']); ?></span>
    </a>
    <div>
      <h1>&#9881;&#65039; <?php echo $is_new ? 'New' : 'Edit'; ?> <?php echo abom_e(strtolower($d['label'])); ?></h1>
      <div class="sub"><?php echo abom_e($d['blurb']); ?></div>
    </div>
    <span class="badge review">MASTER DATA</span>
  </header>

  <div class="layout"><main class="main"><div class="abom-area">

    <?php if (!empty($errors)): ?>
      <div class="abom-master-err">
        <b>Nothing was saved.</b> <?php echo count($errors); ?>
        field<?php echo count($errors) === 1 ? '' : 's'; ?> below need correcting.
        Everything you entered has been kept.
      </div>
    <?php endif; ?>

    <form method="post" action="<?php echo page_url; ?>abom/config_save" class="abom-master-form">
      <input type="hidden" name="entity" value="<?php echo abom_e($entity); ?>">
      <input type="hidden" name="row_id" value="<?php echo abom_e($is_new ? '' : $row_id); ?>">

      <div class="abom-fs">
        <?php foreach ($d['fields'] as $field => $spec): ?>
          <?php
          // A key column is settable on create only — changing it would
          // orphan every row that references it.
          $locked = (!empty($spec['key']) && !$is_new);
          $err    = isset($errors[$field]) ? $errors[$field] : '';
          $v      = $val($field, $spec);
          ?>
          <div class="form-group">
            <label for="f_<?php echo abom_e($field); ?>">
              <?php echo abom_e($spec['label']); ?>
              <?php if (!empty($spec['required'])): ?><span class="req">*</span><?php endif; ?>
            </label>

            <?php if ($spec['type'] === 'bool'): ?>
              <label class="feature-item">
                <input type="checkbox" id="f_<?php echo abom_e($field); ?>"
                       name="<?php echo abom_e($field); ?>" value="1"
                       <?php echo (!empty($v) || ($is_new && $field === 'is_active')) ? 'checked' : ''; ?>>
                <span><?php echo abom_e(isset($spec['help']) ? $spec['help'] : 'Yes'); ?></span>
              </label>

            <?php elseif ($spec['type'] === 'enum'): ?>
              <select id="f_<?php echo abom_e($field); ?>" name="<?php echo abom_e($field); ?>"
                      class="<?php echo $err ? 'is-invalid' : ''; ?>">
                <?php foreach ($spec['options'] as $ov => $ol): ?>
                  <option value="<?php echo abom_e($ov); ?>"
                    <?php echo ((string) $v === (string) $ov) ? ' selected' : ''; ?>><?php echo abom_e($ol); ?></option>
                <?php endforeach; ?>
              </select>

            <?php elseif ($spec['type'] === 'ref'): ?>
              <select id="f_<?php echo abom_e($field); ?>" name="<?php echo abom_e($field); ?>"
                      class="<?php echo $err ? 'is-invalid' : ''; ?>">
                <?php if (!empty($spec['nullable'])): ?>
                  <option value="">Any</option>
                <?php else: ?>
                  <option value="">Choose&hellip;</option>
                <?php endif; ?>
                <?php foreach ((isset($refs[$spec['ref']]) ? $refs[$spec['ref']] : array()) as $ov => $ol): ?>
                  <option value="<?php echo abom_e($ov); ?>"
                    <?php echo ((string) $v === (string) $ov) ? ' selected' : ''; ?>><?php echo abom_e($ol); ?></option>
                <?php endforeach; ?>
              </select>

            <?php elseif ($spec['type'] === 'textarea'): ?>
              <textarea id="f_<?php echo abom_e($field); ?>" name="<?php echo abom_e($field); ?>"
                        rows="2" maxlength="<?php echo (int) (isset($spec['max']) ? $spec['max'] : 255); ?>"
                        class="<?php echo $err ? 'is-invalid' : ''; ?>"><?php echo abom_e($v); ?></textarea>

            <?php elseif (in_array($spec['type'], array('int', 'nullint'), true)): ?>
              <input type="number" id="f_<?php echo abom_e($field); ?>" name="<?php echo abom_e($field); ?>"
                     value="<?php echo ($v === '' || $v === null) ? '' : (int) $v; ?>"
                     <?php if (isset($spec['min'])): ?>min="<?php echo (int) $spec['min']; ?>"<?php endif; ?>
                     <?php if (isset($spec['max'])): ?>max="<?php echo (int) $spec['max']; ?>"<?php endif; ?>
                     placeholder="<?php echo $spec['type'] === 'nullint' ? 'blank = unbounded' : ''; ?>"
                     class="<?php echo $err ? 'is-invalid' : ''; ?>">

            <?php else: ?>
              <input type="text" id="f_<?php echo abom_e($field); ?>" name="<?php echo abom_e($field); ?>"
                     value="<?php echo abom_e($v); ?>"
                     maxlength="<?php echo (int) (isset($spec['max']) ? $spec['max'] : 255); ?>"
                     class="<?php echo $err ? 'is-invalid' : ''; ?>"
                     <?php echo $locked ? 'readonly' : ''; ?>>
            <?php endif; ?>

            <?php if ($err): ?>
              <span class="field-error"><?php echo abom_e($err); ?></span>
            <?php endif; ?>
            <?php if (!empty($spec['help']) && $spec['type'] !== 'bool'): ?>
              <span class="fi-help"><?php echo abom_e($spec['help']); ?></span>
            <?php endif; ?>
            <?php if ($locked): ?>
              <span class="fi-help">Fixed after creation — rows elsewhere reference this value.</span>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="abom-form-actions">
        <button type="submit" class="btn-sm btn-generate">
          &#128190; <?php echo $is_new ? 'Create' : 'Save changes'; ?>
        </button>
        <a class="btn-sm btn-copy"
           href="<?php echo page_url; ?>abom/config_list/<?php echo abom_e($entity); ?>">Cancel</a>
      </div>
    </form>

  </div></main></div>
</div>
<?php $this->load->view('common/footer'); ?>
<script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
<script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.core.js"></script>
<script src="<?php echo assets_url; ?>js/jquery.app.js"></script>
</body>
</html>
