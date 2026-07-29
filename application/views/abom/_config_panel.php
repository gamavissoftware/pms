<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _config_panel.php — the "Machine Configuration" sidebar. SHARED.
 *
 * Same markup and the same grid in both modes. $editable drops the `ro`
 * class and the readonly attribute, and reveals the controls that the
 * generator needs and a released document does not: MR-J4 units,
 * battery quantity, the four feature checkboxes and the PLC override.
 *
 * Without those the J4_STO, BATTERY, AXES_MINUS_1 formulas and the
 * feature gates have nothing to drive them.
 *
 * @var object $bom
 * @var bool   $editable
 * @var string $family_code
 * @var string $family_explanation
 * @var bool   $overridden
 * @var array  $families
 * @var array  $features        master feature rows
 * @var array  $active_features ['feat_perf' => 1, ...]
 * @var array  $errors          field => message
 */

$editable           = isset($editable) ? (bool) $editable : false;
$overridden         = isset($overridden) ? (bool) $overridden : false;
$errors             = isset($errors) ? $errors : array();
$families           = isset($families) ? $families : array();
$features           = isset($features) ? $features : array();
$active_features    = isset($active_features) ? $active_features : array();
$family_explanation = isset($family_explanation) ? $family_explanation : '';
$presets            = isset($presets) ? $presets : array();

$ro   = $editable ? '' : ' class="ro" readonly';
$roc  = $editable ? '' : ' ro';
$sel  = $editable ? '' : ' disabled';

function abom_err($errors, $field)
{
    return isset($errors[$field])
        ? '<span class="field-error">' . abom_e($errors[$field]) . '</span>'
        : '';
}

function abom_inv($errors, $field)
{
    return isset($errors[$field]) ? ' is-invalid' : '';
}
?>
<aside class="sidebar">
  <div class="sidebar-title">&#128295; Machine Configuration</div>
  <div class="sidebar-body">

    <?php if ($editable && !empty($presets)): ?>
      <?php
      // CONFIGURATION presets, not data loaders. Each fills the panel
      // with a known machine configuration; the engine then generates
      // from those inputs like any other configuration. That gives a
      // one-click starting point and doubles as a live regression check
      // against the 29 / 42 counts.
      ?>
      <div class="form-group">
        <label>Load Reference Configuration</label>
        <?php foreach ($presets as $key => $preset): ?>
          <button type="button" class="preset-card" data-preset="<?php echo abom_e($key); ?>"
                  data-cfg="<?php echo abom_e(json_encode($preset['cfg'])); ?>"
                  data-dfref="<?php echo abom_e($preset['df_ref']); ?>">
            <span class="pc-df"><?php echo abom_e($preset['title']); ?></span>
            <span class="pc-line"><?php echo abom_e($preset['summary']); ?><br><?php echo abom_e($preset['panel']); ?></span>
            <span class="pc-tags">
              <?php foreach ($preset['tags'] as $tag): ?>
                <span class="pc-tag pc-<?php echo abom_e($tag[0]); ?>"><?php echo abom_e($tag[1]); ?></span>
              <?php endforeach; ?>
            </span>
          </button>
        <?php endforeach; ?>
      </div>
      <hr class="divider">
    <?php endif; ?>

    <div id="cfgFields">
      <div class="cfg-2col">
        <div class="form-group">
          <label for="cfgDF">BOM Number</label>
          <input id="cfgDF" name="bom_no" class="ro" value="<?php echo abom_e($bom->bom_no); ?>" readonly>
        </div>
        <div class="form-group">
          <label for="cfgRev">Revision</label>
          <input id="cfgRev" name="revision" class="ro" value="<?php echo abom_e($bom->revision !== '' ? $bom->revision : '—'); ?>" readonly>
        </div>
      </div>

      <div class="form-group">
        <label for="cfgDfRef">DF Reference</label>
        <input id="cfgDfRef" name="df_ref"
               class="<?php echo $editable ? '' : 'ro'; ?>"
               value="<?php echo abom_e($bom->df_ref); ?>"
               placeholder="<?php echo $editable ? 'e.g. DF-1826' : ''; ?>"
               <?php echo $editable ? '' : 'readonly'; ?>>
      </div>

      <div class="form-group">
        <label for="cfgModel">Machine Model</label>
        <?php if ($editable): ?>
          <select id="cfgModel" name="machine_model">
            <?php foreach ($models as $model): ?>
              <option value="<?php echo abom_e($model); ?>"<?php echo $bom->machine_model === $model ? ' selected' : ''; ?>>
                <?php echo abom_e($model); ?>
              </option>
            <?php endforeach; ?>
          </select>
        <?php else: ?>
          <input id="cfgModel" class="ro" value="<?php echo abom_e($bom->machine_model); ?>" readonly>
        <?php endif; ?>
      </div>

      <div class="cfg-2col">
        <div class="form-group">
          <label for="cfgAxes">Axes</label>
          <input id="cfgAxes" name="axes" type="<?php echo $editable ? 'number' : 'text'; ?>"
                 class="<?php echo $roc . abom_inv($errors, 'axes'); ?>"
                 value="<?php echo (int) $bom->axes; ?>"
                 <?php if ($editable): ?>min="<?php echo (int) $axes_min; ?>" max="<?php echo (int) $axes_max; ?>"<?php else: ?>readonly<?php endif; ?>>
          <?php echo abom_err($errors, 'axes'); ?>
        </div>
        <div class="form-group">
          <label for="cfgTracks">Tracks</label>
          <input id="cfgTracks" name="tracks" type="<?php echo $editable ? 'number' : 'text'; ?>"
                 class="<?php echo $roc . abom_inv($errors, 'tracks'); ?>"
                 value="<?php echo (int) $bom->tracks; ?>"
                 <?php if ($editable): ?>min="<?php echo (int) $tracks_min; ?>" max="<?php echo (int) $tracks_max; ?>"<?php else: ?>readonly<?php endif; ?>>
          <?php echo abom_err($errors, 'tracks'); ?>
        </div>
      </div>

      <div class="cfg-2col">
        <div class="form-group">
          <label for="cfgSpeed">Speed (PPM)</label>
          <input id="cfgSpeed" name="speed_ppm" type="<?php echo $editable ? 'number' : 'text'; ?>"
                 class="<?php echo $roc . abom_inv($errors, 'speed_ppm'); ?>"
                 value="<?php echo (int) $bom->speed_ppm; ?>"
                 <?php if ($editable): ?>min="<?php echo (int) $speed_min; ?>" max="<?php echo (int) $speed_max; ?>"<?php else: ?>readonly<?php endif; ?>>
          <?php echo abom_err($errors, 'speed_ppm'); ?>
        </div>
        <div class="form-group">
          <label for="cfgSide">Side</label>
          <?php if ($editable): ?>
            <select id="cfgSide" name="machine_side">
              <?php foreach ($sides as $side): ?>
                <option value="<?php echo abom_e($side); ?>"<?php echo $bom->machine_side === $side ? ' selected' : ''; ?>>
                  <?php echo abom_e($side); ?>
                </option>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <input id="cfgSide" class="ro" value="<?php echo abom_e(($bom->machine_side !== '' && $bom->machine_side !== 'N/A') ? $bom->machine_side : '—'); ?>" readonly>
          <?php endif; ?>
        </div>
      </div>

      <div class="form-group">
        <label for="cfgMotion">Motion Type</label>
        <?php if ($editable): ?>
          <select id="cfgMotion" name="motion_type">
            <?php foreach ($motion_types as $motion): ?>
              <option value="<?php echo abom_e($motion); ?>"<?php echo $bom->motion_type === $motion ? ' selected' : ''; ?>>
                <?php echo abom_e($motion); ?>
              </option>
            <?php endforeach; ?>
          </select>
        <?php else: ?>
          <input id="cfgMotion" class="ro" value="<?php echo abom_e($bom->motion_type); ?>" readonly>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="cfgPanel">Panel Location</label>
        <input id="cfgPanel" class="ro" value="<?php echo abom_e($panel_location); ?>" readonly>
      </div>

      <div class="form-group">
        <label>PLC Family <?php echo $editable ? '(auto-detected)' : '(as per DF)'; ?></label>
        <div id="plcFamilyBadge" class="plc-badge <?php echo abom_plc_badge_class($family_code); ?>">
          <?php echo abom_e($family_code === 'FX5' ? 'FX5 Series' : 'iQ-R Series'); ?>
        </div>
        <?php if ($overridden): ?>
          <div class="override-row"><span class="override-flag">Manual override</span></div>
        <?php endif; ?>
        <?php if ($family_explanation !== ''): ?>
          <div class="plc-explain" id="plcExplain"><?php echo abom_e($family_explanation); ?></div>
        <?php endif; ?>
      </div>

      <?php if ($editable): ?>
        <div class="form-group">
          <label for="cfgFamilyOverride">Override PLC Family</label>
          <select id="cfgFamilyOverride" name="plc_family_id">
            <option value="">Use auto-detected</option>
            <?php foreach ($families as $family): ?>
              <option value="<?php echo (int) $family->id; ?>"
                <?php echo ((int) $bom->plc_family_id === (int) $family->id && $overridden) ? ' selected' : ''; ?>>
                <?php echo abom_e($family->name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <hr class="divider">

        <div class="cfg-2col">
          <div class="form-group">
            <label for="cfgJ4">MR-J4 Units</label>
            <input id="cfgJ4" name="j4_units" type="number" min="0" max="99"
                   class="<?php echo trim(abom_inv($errors, 'j4_units')); ?>"
                   value="<?php echo (int) $bom->j4_units; ?>">
            <?php echo abom_err($errors, 'j4_units'); ?>
          </div>
          <div class="form-group">
            <label for="cfgBattery">Battery Qty</label>
            <input id="cfgBattery" name="battery_qty" type="number" min="0" max="99"
                   class="<?php echo trim(abom_inv($errors, 'battery_qty')); ?>"
                   value="<?php echo (int) $bom->battery_qty; ?>">
            <?php echo abom_err($errors, 'battery_qty'); ?>
          </div>
        </div>
        <div style="font-size:10px;color:#888;line-height:1.5;margin-top:-6px;">
          Battery quantity is independent of MR-J4 units. DF-1826 shows 11 units but 12
          battery sets — do not derive one from the other.
        </div>

        <hr class="divider">

        <div class="form-group">
          <label>Machine Features</label>
          <div class="feature-list">
            <?php foreach ($features as $code => $feature): ?>
              <label class="feature-item">
                <input type="checkbox" name="features[<?php echo abom_e($code); ?>]" value="1"
                       data-feature="<?php echo abom_e($code); ?>"
                       <?php echo !empty($active_features[$code]) ? 'checked' : ''; ?>>
                <span>
                  <?php echo abom_e($feature->label); ?>
                  <?php if (!empty($feature->help_text)): ?>
                    <span class="fi-help"><?php echo abom_e($feature->help_text); ?></span>
                  <?php endif; ?>
                </span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php else: ?>
        <div style="font-size:10px;color:#888;line-height:1.5;">
          Parameters are locked to the released DF document. Quantities in the table remain
          editable for review markups.
        </div>
      <?php endif; ?>

      <hr class="divider">
    </div>

    <?php $this->load->view('abom/_legend'); ?>
  </div>
</aside>
