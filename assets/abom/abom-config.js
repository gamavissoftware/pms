/**
 * abom-config.js — "Apply configuration" on a SAVED, still-editable BOM.
 *
 * Loaded only by view.php, and only ever wired up when the server chose
 * to render the button (abom_config_editable(): draft or rejected). The
 * server re-checks the same rule, so nothing here is a permission gate;
 * it is the interaction only.
 *
 * WHY THIS EXISTS
 * A duplicate that cannot be re-specified is not a clone, it is a
 * photocopy. Copying an 11-axis BOM to quote a 15-axis machine has to
 * mean changing the axis count, and changing the axis count has to mean
 * the lines are re-derived — which is exactly why this is a deliberate
 * button and not an autosave.
 *
 * The three things it is careful about:
 *   1. It says what will happen, with the specific fields that changed,
 *      before it does it.
 *   2. It refuses to silently discard unsaved ROW edits, because those
 *      live in a different save button on the same screen.
 *   3. It posts every feature checkbox, ticked or not. An unchecked box
 *      posts nothing of its own accord, and "no features posted" would
 *      otherwise be indistinguishable from "use the defaults".
 *
 * jQuery 2.1.4, ES5. No new libraries.
 */
(function ($, window, document) {
  'use strict';

  if (!$) { return; }

  // id -> the name the server reads it as. Only fields the panel
  // actually renders in editable mode appear here; a missing one is
  // skipped rather than posted empty, so an absent control can never
  // blank a stored value.
  var FIELDS = {
    cfgDfRef:          'df_ref',
    cfgModel:          'machine_model',
    cfgAxes:           'axes',
    cfgTracks:         'tracks',
    cfgSpeed:          'speed_ppm',
    cfgSide:           'machine_side',
    cfgMotion:         'motion_type',
    cfgVariantOverride:'variant_id',
    cfgFamilyOverride: 'plc_family_id',
    cfgJ4:             'j4_units',
    cfgBattery:        'battery_qty'
  };

  // What the document said when the page was rendered, so the
  // confirmation can name what actually changed instead of asking about
  // a regeneration in the abstract.
  var initial = {};

  var LABELS = {
    df_ref:        'DF reference',
    machine_model: 'Model',
    axes:          'Axes',
    tracks:        'Tracks',
    speed_ppm:     'Speed',
    machine_side:  'Side',
    motion_type:   'Motion',
    variant_id:    'Build variant',
    plc_family_id: 'PLC family',
    j4_units:      'MR-J4 units',
    battery_qty:   'Battery qty'
  };

  function collect() {
    var data = {};

    for (var id in FIELDS) {
      if (!FIELDS.hasOwnProperty(id)) { continue; }
      var $el = $('#' + id);
      if (!$el.length) { continue; }
      data[FIELDS[id]] = $.trim(String($el.val() === null ? '' : $el.val()));
    }

    // Every box, ticked or not — see the header comment.
    $('.feature-list input[type="checkbox"][data-feature]').each(function () {
      var code = $(this).attr('data-feature');
      data['features[' + code + ']'] = this.checked ? 1 : 0;
    });

    return data;
  }

  function snapshot() {
    var now  = collect();
    var copy = {};
    for (var k in now) { if (now.hasOwnProperty(k)) { copy[k] = now[k]; } }
    return copy;
  }

  function describeChanges(now) {
    var out = [];

    for (var k in now) {
      if (!now.hasOwnProperty(k)) { continue; }
      if (String(initial[k]) === String(now[k])) { continue; }

      if (k.indexOf('features[') === 0) {
        var code  = k.slice(9, -1);
        var $box  = $('.feature-list input[data-feature="' + code + '"]');
        var label = $.trim($box.closest('.feature-item').find('span').first()
                       .contents().filter(function () { return this.nodeType === 3; })
                       .text()) || code;
        out.push('  ' + label + ': ' + (now[k] ? 'ON' : 'OFF'));
        continue;
      }

      var name = LABELS[k] || k;
      var was  = String(initial[k]) === '' ? '(none)' : initial[k];
      var is   = String(now[k]) === '' ? '(none)' : now[k];

      // The two overrides post ids, which mean nothing in a dialog.
      // Show the chosen option's text instead.
      if (k === 'variant_id' || k === 'plc_family_id') {
        var $sel = $(k === 'variant_id' ? '#cfgVariantOverride' : '#cfgFamilyOverride');
        is  = $.trim($sel.find('option:selected').text()) || is;
        was = $.trim($sel.find('option[value="' + initial[k] + '"]').text()) || was;
      }

      out.push('  ' + name + ': ' + was + '  →  ' + is);
    }

    return out;
  }

  function fieldErrors(errors) {
    $('#cfgFields .is-invalid').removeClass('is-invalid');
    $('#cfgFields .field-error.js-added').remove();

    var byField = {
      axes: '#cfgAxes', tracks: '#cfgTracks', speed_ppm: '#cfgSpeed',
      j4_units: '#cfgJ4', battery_qty: '#cfgBattery',
      motion_type: '#cfgMotion', machine_model: '#cfgModel', machine_side: '#cfgSide'
    };

    var first = null;

    for (var field in errors) {
      if (!errors.hasOwnProperty(field) || !byField[field]) { continue; }
      var $el = $(byField[field]);
      if (!$el.length) { continue; }
      $el.addClass('is-invalid');
      $el.after($('<span class="field-error js-added">').text(errors[field]));
      if (!first) { first = $el; }
    }

    if (first) { first.focus(); }
  }

  $(function () {
    var $btn = $('#abomSaveConfig');
    if (!$btn.length) { return; }

    initial = snapshot();

    // Same courtesy the row editor extends: configuration typed and not
    // applied is work, and losing it to a stray click on "Saved BOMs"
    // is the kind of small betrayal that stops people trusting a screen.
    $(window).on('beforeunload', function () {
      if (describeChanges(collect()).length) {
        return 'The machine configuration has been changed but not applied.';
      }
    });

    $btn.on('click', function () {
      var now     = collect();
      var changes = describeChanges(now);

      if (!changes.length) {
        window.alert('Nothing has been changed in the configuration panel.');
        return;
      }

      // Same pre-flight as the generator: a reference the screen has
      // already been told is taken does not need a round trip to be
      // refused. The server re-checks it either way.
      if (typeof window.ABOM_DFREF_TAKEN === 'function' && window.ABOM_DFREF_TAKEN()) {
        window.alert('That DF reference is already used by another BOM.\n\n' +
                     'Two BOMs cannot share one drawing number at the same revision. ' +
                     'Change the DF Reference, or raise a revision on the existing BOM.');
        $('#cfgDfRef').focus();
        return;
      }

      // Row edits live in a DIFFERENT save button on this same screen,
      // and regenerating replaces the rows they are attached to. Losing
      // them without a word would be indefensible.
      if (typeof window.ABOM_LINES_DIRTY === 'function' && window.ABOM_LINES_DIRTY()) {
        window.alert(
          'There are unsaved row changes on this BOM.\n\n' +
          'Applying a new configuration regenerates the item list, which would ' +
          'discard them. Save the rows first (the "Save changes" button under the ' +
          'table), then apply the configuration.'
        );
        return;
      }

      var ok = window.confirm(
        'Apply this configuration to the BOM?\n\n' +
        changes.join('\n') + '\n\n' +
        'The item list will be regenerated from these values. Your hand-added rows, ' +
        'typed remarks and quantity overrides are carried across.\n\n' +
        'The master item list and the reference builds are not affected.'
      );

      if (!ok) { return; }

      var payload = collect();
      payload.bom_id = $btn.attr('data-bom');

      $btn.prop('disabled', true).text('Applying…');

      $.ajax({
        url: window.ABOM_SAVE_CONFIG_URL || 'abom/save_config',
        type: 'POST',
        dataType: 'json',
        data: payload
      }).done(function (res) {
        if (res && res.status) {
          // The message names what changed and what was carried over,
          // and it is worth reading — so it is shown before the reload
          // rather than lost to it.
          window.alert(res.message || 'Configuration applied.');
          window.location.reload();
          return;
        }
        window.alert(res && res.message ? res.message : 'The configuration could not be applied.');
        $btn.prop('disabled', false).html('&#128295; Apply configuration');
      }).fail(function (xhr) {
        var res = null;
        try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }

        if (res && res.errors) { fieldErrors(res.errors); }

        window.alert(res && res.message
          ? res.message
          : 'The configuration could not be applied (HTTP ' + xhr.status +
            '). Nothing was changed.');

        $btn.prop('disabled', false).html('&#128295; Apply configuration');
      });
    });
  });

}(window.jQuery, window, document));
