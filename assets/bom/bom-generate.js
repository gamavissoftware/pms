/* =====================================================================
   Automation BOM Generator — generate screen behaviour

   Loaded only by application/views/abom/generate.php, on top of bom.js.
   jQuery 2.1.4 / Bootstrap 3.3.7 — the versions the project already
   loads. No new library.

   Every input change fires a debounced POST to /abom/generate_ajax,
   which returns the re-rendered table partial. The row-class precedence
   therefore lives in exactly one place — Bom_engine and the helper —
   and is never duplicated here.
   ===================================================================== */
(function ($, window, document) {
  'use strict';

  if (!$) { return; }

  var DEBOUNCE_MS = 250;          // the engineer is typing
  var $wrap = null;
  var timer = null;
  var lastFamilyId = null;
  var inFlight = null;

  function url() {
    return window.ABOM_GENERATE_URL || 'abom/generate_ajax';
  }

  // -------------------------------------------------------------------
  function collect() {
    var data = {
      axes:          $('#cfgAxes').val(),
      tracks:        $('#cfgTracks').val(),
      speed_ppm:     $('#cfgSpeed').val(),
      motion_type:   $('#cfgMotion').val(),
      machine_model: $('#cfgModel').val(),
      machine_side:  $('#cfgSide').val(),
      df_ref:        $('#cfgDfRef').val(),
      j4_units:      $('#cfgJ4').val(),
      battery_qty:   $('#cfgBattery').val(),
      plc_family_id: $('#cfgFamilyOverride').val() || '',
      features:      {}
    };

    // An unchecked box posts nothing, so send an explicit 0 — otherwise
    // the server cannot tell "gate off" from "field absent".
    $wrap.find('[data-feature]').each(function () {
      data.features[$(this).attr('data-feature')] = this.checked ? 1 : 0;
    });

    return data;
  }

  function clearErrors() {
    $wrap.find('.is-invalid').removeClass('is-invalid');
    $wrap.find('.field-error').remove();
  }

  function showErrors(errors) {
    $.each(errors, function (field, message) {
      var $input = $wrap.find('[name="' + field + '"]');
      $input.addClass('is-invalid');
      if (!$input.next('.field-error').length) {
        $input.after($('<span class="field-error"></span>').text(message));
      }
    });
  }

  // -------------------------------------------------------------------
  function applyFamilyDefaults(family, defaults) {
    // Only reset on an ACTUAL family transition. If the engineer has
    // typed a value and the family has not changed, leave it alone.
    var familyId = family.code;

    if (lastFamilyId !== null && lastFamilyId !== familyId) {
      $('#cfgJ4').val(defaults.j4_units);
      $('#cfgBattery').val(defaults.battery_qty);
    }
    lastFamilyId = familyId;

    $('#cfgPanel').val(defaults.panel_location || '');
  }

  function applyFamily(family) {
    var $badge = $('#plcFamilyBadge');
    $badge.attr('class', 'plc-badge ' + family.badge_class)
          .text(family.code === 'FX5' ? 'FX5 Series' : 'iQ-R Series');

    $('#plcExplain').text(family.explanation || '');

    // An override that is never surfaced is how wrong BOMs get approved.
    var $row = $wrap.find('.override-row');
    if (family.overridden) {
      if (!$row.length) {
        $('#plcFamilyBadge').after(
          '<div class="override-row"><span class="override-flag">Manual override</span></div>'
        );
      }
    } else {
      $row.remove();
    }
  }

  function applyChips(chips) {
    var html = '';
    for (var i = 0; i < chips.length; i++) {
      html += '<span class="config-chip ' + chips[i]['class'] + '">' +
              $('<div>').text(chips[i].label).html() + '</span>';
    }
    $('#chipRow').html(html);
  }

  function applyStats(stats, sections, lineCount) {
    var $cards = $wrap.find('#statsRow .stat-card .val');
    $cards.eq(0).text(stats.lines);
    $cards.eq(1).text(stats.total_qty);
    $cards.eq(2).text(sections.length);
    $cards.eq(3).text(stats.no_erp);
    $cards.eq(4).text(0);

    var html = '<button type="button" class="pill active" data-filter="ALL">All (' + lineCount + ')</button>';
    for (var i = 0; i < sections.length; i++) {
      html += '<button type="button" class="pill" data-filter="' +
              $('<div>').text(sections[i].name).html() + '">' +
              $('<div>').text(sections[i].name).html() +
              ' (' + sections[i].count + ')</button>';
    }
    $('#sectionPills').html(html);
  }

  // -------------------------------------------------------------------
  function run() {
    if (inFlight) { inFlight.abort(); }

    var $host = $('#bomTableHost');
    $host.addClass('abom-busy');

    inFlight = $.ajax({
      url: url(),
      type: 'POST',
      dataType: 'json',
      data: collect()
    }).done(function (res) {
      clearErrors();

      if (!res || !res.status) {
        return;
      }

      $host.html(res.html);
      applyFamily(res.family);
      applyFamilyDefaults(res.family, res.defaults);
      applyChips(res.chips);
      applyStats(res.stats, res.sections, res.stats.lines);
      $('#cfgDF').val(res.bom_no);

    }).fail(function (xhr) {
      if (xhr.statusText === 'abort') { return; }

      clearErrors();

      var res = null;
      try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }

      if (res && res.errors) {
        showErrors(res.errors);
      }
    }).always(function () {
      $host.removeClass('abom-busy');
      inFlight = null;
    });
  }

  function schedule() {
    window.clearTimeout(timer);
    timer = window.setTimeout(run, DEBOUNCE_MS);
  }

  // -------------------------------------------------------------------
  function applyPreset($card) {
    var cfg;
    try { cfg = JSON.parse($card.attr('data-cfg')); } catch (e) { return; }

    $wrap.find('.preset-card').removeClass('active');
    $card.addClass('active');

    $('#cfgAxes').val(cfg.axes);
    $('#cfgTracks').val(cfg.tracks);
    $('#cfgSpeed').val(cfg.speed_ppm);
    $('#cfgMotion').val(cfg.motion_type);
    $('#cfgModel').val(cfg.machine_model);
    $('#cfgSide').val(cfg.machine_side);
    $('#cfgJ4').val(cfg.j4_units);
    $('#cfgBattery').val(cfg.battery_qty);
    $('#cfgDfRef').val($card.attr('data-dfref') || '');
    $('#cfgFamilyOverride').val('');

    $wrap.find('[data-feature]').each(function () {
      var code = $(this).attr('data-feature');
      this.checked = !!(cfg.features && cfg.features[code]);
    });

    // A preset is a starting configuration, not a data load — force a
    // fresh detection rather than carrying the previous family over.
    lastFamilyId = null;
    run();
  }

  // -------------------------------------------------------------------
  $(function () {
    $wrap = $('.abom-wrap');
    if (!$wrap.length || !$('#cfgAxes').length) { return; }

    lastFamilyId = $('#plcFamilyBadge').hasClass('plc-fx5') ? 'FX5' : 'iQ-R';

    $wrap.on('input change',
      '#cfgAxes, #cfgTracks, #cfgSpeed, #cfgJ4, #cfgBattery, #cfgDfRef',
      schedule);

    $wrap.on('change',
      '#cfgMotion, #cfgModel, #cfgSide, #cfgFamilyOverride, [data-feature]',
      run);

    $wrap.on('click', '.preset-card', function () {
      applyPreset($(this));
    });

    $wrap.on('click', '#btnGenerate', run);
  });

}(window.jQuery, window, document));
