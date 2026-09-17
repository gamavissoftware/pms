<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _config_panel.php — the "Machine Configuration" sidebar. SHARED.
 *
 * Same markup and the same grid in every mode. Unlocking drops the `ro`
 * class and the readonly attribute, and reveals the controls a released
 * document does not need: MR-J4 units, battery quantity, the feature
 * checkboxes and the two overrides.
 *
 * Without those the J4_STO, BATTERY, AXES_MINUS_1 formulas and the
 * feature gates have nothing to drive them.
 *
 * TWO separate flags decide this, and they are not the same question:
 *
 *   $editable         this is the GENERATOR. Fields are form state; the
 *                     preset picker and Save BOM (which creates a NEW
 *                     document) belong to it.
 *   $config_editable  this is a SAVED BOM still open to change — a
 *                     draft, or a clone, or a rejected sheet. The same
 *                     fields unlock, but Save writes back to THIS
 *                     document and re-derives its lines.
 *
 * They are kept apart because a clone that cannot be re-specified is
 * only a duplicate, and a saved BOM that shows the generator's Save
 * button would quietly fork into a second document instead.
 *
 * @var object $bom
 * @var bool   $editable
 * @var bool   $config_editable
 * @var string $family_code
 * @var string $family_explanation
 * @var bool   $overridden
 * @var array  $families
 * @var array  $features        master feature rows
 * @var array  $active_features ['feat_perf' => 1, ...]
 * @var array  $errors          field => message
 */

$editable           = isset($editable) ? (bool) $editable : false;
$config_editable    = isset($config_editable) ? (bool) $config_editable : false;

// Everything below asks "may these FIELDS be typed in", which is true in
// both modes. Only the preset picker and the action buttons care which
// of the two it is.
$cfg_edit           = $editable || $config_editable;
$overridden         = isset($overridden) ? (bool) $overridden : false;
$errors             = isset($errors) ? $errors : array();
$families           = isset($families) ? $families : array();
$features           = isset($features) ? $features : array();
$active_features    = isset($active_features) ? $active_features : array();
$family_explanation = isset($family_explanation) ? $family_explanation : '';
$presets            = isset($presets) ? $presets : array();

$variant             = isset($variant) ? $variant : null;
$variants            = isset($variants) ? $variants : array();
$variant_explanation = isset($variant_explanation) ? $variant_explanation : '';
$variant_overridden  = isset($variant_overridden) ? (bool) $variant_overridden : false;
$variant_missing     = isset($variant_missing) ? (bool) $variant_missing : false;
$variant_message     = isset($variant_message) ? $variant_message : '';

$ro   = $cfg_edit ? '' : ' class="ro" readonly';
$roc  = $cfg_edit ? '' : ' ro';
$sel  = $cfg_edit ? '' : ' disabled';

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
      /**
       * CONFIGURATION presets, not data loaders. Each fills the panel
       * with a known machine configuration; the engine then generates
       * from those inputs like any other configuration.
       *
       * A SEARCHABLE COMBOBOX, not a stack of cards. Nine reference BOMs
       * already pushed the machine configuration itself below the fold,
       * and the set only grows — every future DF adds another card. The
       * combobox is a fixed two rows however many there are, and typing
       * "1826", "9 track" or "IQR-FLM" narrows it, which a card stack
       * cannot do at all.
       *
       * It is built from a plain <input> plus a filtered list rather
       * than select2 or chosen: the project loads jQuery 2.1.4 and
       * Bootstrap 3.3.7 and nothing else, and this module has shipped
       * without adding a library. A searchable select is not worth
       * breaking that for.
       *
       * The <select> underneath is the real control and stays in the
       * DOM: with JavaScript off it is an ordinary working dropdown, and
       * it is what keyboard and screen-reader users operate.
       */
      ?>
      <div class="form-group abom-preset-picker" id="abomPresetPicker">
        <label for="abomPresetSearch">Load Reference Configuration</label>

        <div class="preset-combo">
          <input type="text" id="abomPresetSearch" class="preset-search"
                 autocomplete="off" role="combobox" aria-expanded="false"
                 aria-controls="abomPresetList" aria-autocomplete="list"
                 placeholder="Search <?php echo count($presets); ?> reference BOMs — DF no., axes, tracks, build…">
          <button type="button" class="preset-clear" id="abomPresetClear"
                  title="Clear search" aria-label="Clear search">&times;</button>

          <ul class="preset-list" id="abomPresetList" role="listbox" hidden>
            <?php foreach ($presets as $key => $preset): ?>
              <?php
              // Everything a search should match, flattened into one
              // attribute so the filter never has to guess which field
              // the operator typed at.
              $haystack = $preset['title'] . ' ' . $preset['df_ref'] . ' '
                        . $preset['summary'] . ' ' . $preset['panel'];
              foreach ($preset['tags'] as $tag) {
                  $haystack .= ' ' . $tag[1];
              }
              ?>
              <li class="preset-option" role="option" tabindex="-1"
                  data-preset="<?php echo abom_e($key); ?>"
                  data-search="<?php echo abom_e(strtolower($haystack)); ?>"
                  data-cfg="<?php echo abom_e(json_encode($preset['cfg'])); ?>"
                  data-dfref="<?php echo abom_e($preset['df_ref']); ?>">
                <span class="po-df"><?php echo abom_e($preset['title']); ?></span>
                <span class="po-line"><?php echo abom_e($preset['summary']); ?></span>
                <span class="po-line po-muted"><?php echo abom_e($preset['panel']); ?></span>
                <span class="po-tags">
                  <?php foreach ($preset['tags'] as $tag): ?>
                    <span class="pc-tag pc-<?php echo abom_e($tag[0]); ?>"><?php echo abom_e($tag[1]); ?></span>
                  <?php endforeach; ?>
                </span>
              </li>
            <?php endforeach; ?>
            <li class="preset-empty" hidden>No reference BOM matches that.</li>
          </ul>
        </div>

        <select id="abomPresetSelect" class="preset-fallback">
          <option value="">Choose a reference configuration…</option>
          <?php foreach ($presets as $key => $preset): ?>
            <option value="<?php echo abom_e($key); ?>">
              <?php echo abom_e($preset['title'] . ' — ' . $preset['summary']); ?>
            </option>
          <?php endforeach; ?>
        </select>
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
        <?php
        /*
         * "New DF No" while the configuration can still be changed — on
         * the generator this field is where the NEW drawing number goes,
         * and "DF Reference" read as though it wanted the number of the
         * reference sheet the build came from.
         *
         * On a saved, locked document it is simply that BOM's DF number,
         * so the word "New" would be wrong there.
         */
        ?>
        <label for="cfgDfRef"><?php echo $cfg_edit ? 'New DF No' : 'DF No'; ?></label>
        <input id="cfgDfRef" name="df_ref"
               class="<?php echo $cfg_edit ? '' : 'ro'; ?>"
               value="<?php echo abom_e($bom->df_ref); ?>"
               placeholder="<?php echo $cfg_edit ? 'e.g. DF-1826' : ''; ?>"
               <?php echo $cfg_edit ? '' : 'readonly'; ?>>
      </div>

      <div class="form-group">
        <label for="cfgModel">Machine Model</label>
        <?php if ($cfg_edit): ?>
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
          <input id="cfgAxes" name="axes" type="<?php echo $cfg_edit ? 'number' : 'text'; ?>"
                 class="<?php echo $roc . abom_inv($errors, 'axes'); ?>"
                 value="<?php echo (int) $bom->axes; ?>"
                 <?php if ($cfg_edit): ?>min="<?php echo (int) $axes_min; ?>" max="<?php echo (int) $axes_max; ?>"<?php else: ?>readonly<?php endif; ?>>
          <?php echo abom_err($errors, 'axes'); ?>
        </div>
        <div class="form-group">
          <label for="cfgTracks">Tracks</label>
          <input id="cfgTracks" name="tracks" type="<?php echo $cfg_edit ? 'number' : 'text'; ?>"
                 class="<?php echo $roc . abom_inv($errors, 'tracks'); ?>"
                 value="<?php echo (int) $bom->tracks; ?>"
                 <?php if ($cfg_edit): ?>min="<?php echo (int) $tracks_min; ?>" max="<?php echo (int) $tracks_max; ?>"<?php else: ?>readonly<?php endif; ?>>
          <?php echo abom_err($errors, 'tracks'); ?>
        </div>
      </div>

      <div class="cfg-2col">
        <div class="form-group">
          <label for="cfgSpeed">Speed (PPM)</label>
          <input id="cfgSpeed" name="speed_ppm" type="<?php echo $cfg_edit ? 'number' : 'text'; ?>"
                 class="<?php echo $roc . abom_inv($errors, 'speed_ppm'); ?>"
                 value="<?php echo (int) $bom->speed_ppm; ?>"
                 <?php if ($cfg_edit): ?>min="<?php echo (int) $speed_min; ?>" max="<?php echo (int) $speed_max; ?>"<?php else: ?>readonly<?php endif; ?>>
          <?php echo abom_err($errors, 'speed_ppm'); ?>
        </div>
        <div class="form-group">
          <label for="cfgSide">Side</label>
          <?php if ($cfg_edit): ?>
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
        <?php if ($cfg_edit): ?>
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
        <label>PLC Family <?php echo $cfg_edit ? '(auto-detected)' : '(as per DF)'; ?></label>
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

      <?php
      /**
       * BUILD VARIANT.
       *
       * The family says which CPU; the variant says which machine. Two
       * FX5 builds in the reference set carry entirely different servo
       * ranges (MR-JE/HG-SN below 8 axes, MR-J4/HG-JR at 8), so the
       * variant is the single most consequential thing on this panel —
       * it decides what is on the purchasable document. It is shown
       * whether or not the panel is editable, because a released BOM has
       * to state which build it was cut from.
       */
      ?>
      <div class="form-group">
        <label>Build Variant <?php echo $cfg_edit ? '(auto-detected)' : '(as per DF)'; ?></label>
        <div id="abomVariantBadge" class="plc-badge <?php echo abom_plc_badge_class($family_code); ?>">
          <?php echo abom_e($variant ? $variant->code : '—'); ?>
        </div>
        <?php if ($variant): ?>
          <div class="plc-explain" id="abomVariantName"><?php echo abom_e($variant->name); ?></div>
        <?php else: ?>
          <div class="plc-explain" id="abomVariantName"></div>
        <?php endif; ?>
        <?php if ($variant_overridden): ?>
          <div class="override-row"><span class="override-flag">Manual override</span></div>
        <?php endif; ?>
        <?php if ($variant_explanation !== ''): ?>
          <div class="plc-explain" id="abomVariantExplain"><?php echo abom_e($variant_explanation); ?></div>
        <?php else: ?>
          <div class="plc-explain" id="abomVariantExplain"></div>
        <?php endif; ?>
      </div>

      <?php
      // A configuration no variant rule matches generates NOTHING. That
      // is deliberate — see Abom_engine::generate() — so it has to say
      // so loudly rather than let an empty table read as "no parts
      // required".
      ?>
      <div class="form-group abom-variant-missing<?php echo $variant_missing ? '' : ' is-hidden'; ?>"
           id="abomVariantMissing">
        <div class="override-row">
          <span class="override-flag">No build matched</span>
        </div>
        <div class="plc-explain" id="abomVariantMissingText"><?php echo abom_e($variant_message); ?></div>
      </div>

      <?php if ($cfg_edit): ?>
        <div class="form-group">
          <label for="cfgVariantOverride">Override Build Variant</label>
          <select id="cfgVariantOverride" name="variant_id">
            <option value="">Use auto-detected</option>
            <?php foreach ($variants as $v): ?>
              <option value="<?php echo (int) $v->id; ?>"
                <?php echo ($variant && (int) $variant->id === (int) $v->id && $variant_overridden) ? ' selected' : ''; ?>>
                <?php echo abom_e($v->code . ' — ' . $v->name); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

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
        <?php
        /**
         * The ENABLED feature gates, read-only.
         *
         * These decide whether whole groups of parts are on the
         * document — the braking resistor, the I-mark sensor, the
         * perforation axis — so a released BOM has to state which were
         * switched on. The checkboxes above are not rendered when the
         * panel is locked, and this list is what a reviewer reads
         * instead.
         *
         * Silent when none are enabled, rather than printing "None":
         * an empty heading on a document is a question, not an answer.
         */
        $on = array();
        foreach ($features as $code => $feature) {
            if (!empty($active_features[$code])) {
                $on[] = $feature->label;
            }
        }
        ?>
        <?php if (!empty($on)): ?>
          <div class="form-group">
            <label>Machine Features</label>
            <div class="feature-readout">
              <?php foreach ($on as $label): ?>
                <span class="fr-item"><?php echo abom_e($label); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <div style="font-size:10px;color:#888;line-height:1.5;">
          Parameters are locked to the released DF document. Quantities in the table remain
          editable for review markups.
        </div>
      <?php endif; ?>

      <?php if ($config_editable): ?>
        <?php
        /**
         * Say what the button will DO before it is pressed.
         *
         * Changing axes or tracks does not just relabel the header — it
         * re-derives every computed quantity and can select a different
         * build entirely, which means the line list is rewritten. That
         * is the intended behaviour and it is also the kind of thing
         * nobody should discover afterwards, so it is stated here, next
         * to the fields that cause it.
         */
        ?>
        <div class="cfg-apply-note">
          <b>Applying re-derives the lines.</b>
          Axes, tracks, speed, motion, model and the overrides decide which build is used
          and what every computed quantity comes to, so the item list is regenerated.
          <span class="cfg-apply-keep">Your hand-added rows, typed remarks and quantity
          overrides are carried across.</span>
          The master item list and the reference builds are never changed from here.
        </div>
      <?php endif; ?>

      <hr class="divider">
    </div>

    <?php $this->load->view('abom/_legend'); ?>
  </div>
</aside>
