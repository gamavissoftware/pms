/* =====================================================================
   Automation BOM Generator — generate screen behaviour

   Loaded only by application/views/abom/generate.php, on top of bom.js.
   jQuery 2.1.4 / Bootstrap 3.3.7 — the versions the project already
   loads. No new library.

   Every input change fires a debounced POST to /abom/generate_ajax,
   which returns the re-rendered table partial. The row-class precedence
   therefore lives in exactly one place — Abom_engine and the helper —
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

  /* -------------------------------------------------------------------
     ROW EDITS — remarks, hand-added rows, removed rows.

     Held here rather than in the DOM because the DOM does not survive:
     every configuration change replaces the whole table with freshly
     rendered HTML from the server. Without this, typing a remark and
     then correcting the track count would silently discard the remark.

     Keyed on data-key ("i<item_id>" / "m<uid>"), never on line number —
     line numbers shift the moment the item count does.

     NONE OF THIS TOUCHES MASTER DATA. It is form state until Save, and
     Save writes it to abom_bom_line, which is a per-BOM snapshot. No
     path from this screen writes to abom_item.
     ------------------------------------------------------------------- */
  var edits = {
    remarks: {},   // key -> typed remark
    removed: {},   // key -> true
    manual:  [],   // [{key, afterKey, erp_code, description, part_no,
                   //   manufacturer, qty, uom, user_remark}]
    seq: 0         // uid counter for manual rows
  };

  /* The configuration the CURRENTLY VISIBLE table was generated from.
     Save compares against it: if the panel has moved on since the last
     successful render — a changed axis count, a variant override, a
     failed or still-in-flight request — the rows on screen describe a
     different machine to the one being saved, and the server would
     reject them. Regenerate first, then save. */
  var renderedCfg = null;

  function cfgFingerprint(data) {
    var d = data || collect();
    return JSON.stringify([
      d.axes, d.tracks, d.speed_ppm, d.motion_type, d.machine_model,
      d.machine_side, d.plc_family_id, d.variant_id, d.features
    ]);
  }

  function manualByKey(key) {
    for (var i = 0; i < edits.manual.length; i++) {
      if (edits.manual[i].key === key) { return edits.manual[i]; }
    }
    return null;
  }

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
      variant_id:    $('#cfgVariantOverride').val() || '',
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
  function applyFamilyDefaults(family, variant, defaults) {
    // Only reset on an ACTUAL build transition. If the engineer has
    // typed a value and the build has not changed, leave it alone.
    //
    // Keyed on the VARIANT, not the family: DF-1770 and DF-1826 are both
    // iQ-R but run 9/9 and 11/12: keying on the family would leave
    // DF-1770's numbers standing when the build changes under it.
    var buildId = family.code + '/' + (variant && variant.code ? variant.code : '');

    if (lastFamilyId !== null && lastFamilyId !== buildId) {
      $('#cfgJ4').val(defaults.j4_units);
      $('#cfgBattery').val(defaults.battery_qty);
    }
    lastFamilyId = buildId;

    $('#cfgPanel').val(defaults.panel_location || '');
  }

  // An override that is never surfaced is how wrong BOMs get approved.
  // Scoped to the badge it belongs to — the panel now has two of these
  // (family and variant) and a global remove() would clear the wrong one.
  function applyOverrideFlag($badge, overridden) {
    var $row = $badge.next('.override-row');

    if (overridden) {
      if (!$row.length) {
        $badge.after(
          '<div class="override-row"><span class="override-flag">Manual override</span></div>'
        );
      }
    } else {
      $row.remove();
    }
  }

  function applyFamily(family) {
    var $badge = $('#plcFamilyBadge');
    $badge.attr('class', 'plc-badge ' + family.badge_class)
          .text(family.code === 'FX5' ? 'FX5 Series' : 'iQ-R Series');

    $('#plcExplain').text(family.explanation || '');
    applyOverrideFlag($badge, family.overridden);
  }

  function applyVariant(variant) {
    if (!variant) {
      return;
    }

    var $badge = $('#abomVariantBadge');
    $badge.attr('class', 'plc-badge ' + variant.badge_class)
          .text(variant.code || '—');

    $('#abomVariantName').text(variant.name || '');
    $('#abomVariantExplain').text(variant.explanation || '');
    applyOverrideFlag($badge, variant.overridden);

    // No variant matched means the engine returned no lines at all. Say
    // so where the engineer is looking, not only by an empty table.
    $('#abomVariantMissing').toggleClass('is-hidden', !variant.missing);
    $('#abomVariantMissingText').text(variant.message || '');
  }

  /* -------------------------------------------------------------------
     Re-apply the engineer's row edits to a freshly rendered table.

     Runs after every AJAX render. Order matters: remove first (so the
     rows are gone before anything is counted), then remarks, then
     re-insert the hand-added rows against their anchors, then renumber.
     ------------------------------------------------------------------- */
  function applyEdits() {
    var $body = $wrap.find('#abomBody');

    // 1. removals
    $body.find('tr.data-row').each(function () {
      var key = $(this).attr('data-key');
      if (key && edits.removed[key]) { $(this).remove(); }
    });

    // 2. remarks
    $body.find('tr.data-row').each(function () {
      var $row = $(this);
      var key  = $row.attr('data-key');
      if (key && typeof edits.remarks[key] === 'string') {
        $row.find('.remark-input').val(edits.remarks[key]);
      }
    });

    // 3. hand-added rows, in the order they were created so that a row
    //    inserted after another manual row lands in the right place
    for (var i = 0; i < edits.manual.length; i++) {
      insertManualRow(edits.manual[i]);
    }

    markFilled();
    renumber();
    dropEmptySections();
    updateRestoreBar();
  }

  function colCount() {
    return $wrap.find('#abomTable thead th').length || 10;
  }

  function esc(v) {
    return $('<div>').text(v == null ? '' : v).html();
  }

  function manualCell(field, value, placeholder, maxlen) {
    return '<input type="text" class="manual-input manual-' + field + '"' +
           ' value="' + esc(value) + '" data-field="' + field + '"' +
           ' maxlength="' + maxlen + '" placeholder="' + esc(placeholder) + '">';
  }

  function manualRowHtml(row) {
    return '<tr class="data-row is-manual-row" data-key="' + esc(row.key) + '"' +
             ' data-item="0" data-manual="1" data-section="' + esc(row.section) + '">' +
      '<td style="text-align:center;font-weight:700;" class="sno-cell"></td>' +
      '<td style="text-align:center;">' + manualCell('erp_code', row.erp_code, 'ERP', 32) + '</td>' +
      '<td class="desc-cell">' + manualCell('description', row.description, 'Description', 255) + '</td>' +
      '<td class="part-cell">' + manualCell('part_no', row.part_no, 'Part no.', 96) + '</td>' +
      '<td style="text-align:center;">' + manualCell('manufacturer', row.manufacturer, 'Manufacturer', 64) + '</td>' +
      '<td style="text-align:center;">' +
        '<input type="number" class="qty-input" min="0" value="' + (parseInt(row.qty, 10) || 0) + '">' +
      '</td>' +
      '<td style="text-align:center;">' +
        '<input type="text" class="manual-input manual-uom" data-field="uom" maxlength="16"' +
        ' value="' + esc(row.uom || 'NO(S)') + '" style="width:48px;text-align:center;">' +
      '</td>' +
      '<td class="remarks-cell">' +
        '<input type="text" class="remark-input" maxlength="255"' +
        ' value="' + esc(row.user_remark) + '" placeholder="Add a remark…">' +
      '</td>' +
      '<td style="text-align:center;"><span class="formula-tag tag-manual">MANUAL ROW</span></td>' +
      '<td class="col-actions">' +
        '<button type="button" class="row-act row-add" title="Insert a blank row below this one">+</button>' +
        '<button type="button" class="row-act row-del" title="Remove this row from this BOM">&times;</button>' +
      '</td>' +
    '</tr>';
  }

  // Put a manual row back where it belongs. If its anchor is gone —
  // because the configuration changed and that part is no longer on the
  // sheet — it goes to the end of the table rather than vanishing. A
  // hand-typed row must never be silently dropped.
  function insertManualRow(row) {
    var $body = $wrap.find('#abomBody');

    if ($body.find('tr[data-key="' + row.key + '"]').length) { return; }

    var $html   = $(manualRowHtml(row));
    var $anchor = row.afterKey ? $body.find('tr[data-key="' + row.afterKey + '"]') : $();

    if ($anchor.length) {
      $anchor.after($html);
    } else {
      $body.append($html);
    }
  }

  // S.NO. is positional and must read 1..n with no gaps after any
  // insert or remove, or the "Points to verify" references and the
  // printed sheet stop agreeing with each other.
  function renumber() {
    var n = 0;
    $wrap.find('#abomBody tr.data-row').each(function () {
      n++;
      $(this).attr('data-line', n).find('.sno-cell').text(n);
      $(this).find('.qty-input, .remark-input').attr('data-line', n);
    });
  }

  // A section header with nothing under it is noise. Removing the last
  // row of a section hides its heading too.
  function dropEmptySections() {
    $wrap.find('#abomBody tr.sec-row').each(function () {
      var $sec = $(this);
      var live = $sec.nextUntil('tr.sec-row', 'tr.data-row').length;

      $sec.toggle(live > 0);
      if (live > 0) {
        $sec.find('span').text('(' + live + ' item' + (live === 1 ? '' : 's') + ')');
      }
    });
  }

  function markFilled() {
    $wrap.find('#abomBody .remark-input').each(function () {
      $(this).toggleClass('is-filled', $.trim(this.value) !== '');
    });
  }

  function updateRestoreBar() {
    var n = 0, k;
    for (k in edits.removed) { if (edits.removed[k]) { n++; } }

    var $bar = $wrap.find('#abomRestoreBar');
    if (!$bar.length) { return; }

    if (n === 0) {
      $bar.attr('hidden', 'hidden');
      return;
    }

    $bar.removeAttr('hidden').html(
      n + ' row' + (n === 1 ? '' : 's') + ' removed from this BOM. ' +
      'The master data is untouched. ' +
      '<button type="button" id="abomRestoreAll">Restore them</button>'
    );
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

    var $host = $('#abomTableHost');
    $host.addClass('abom-busy');

    var sent = collect();

    inFlight = $.ajax({
      url: url(),
      type: 'POST',
      dataType: 'json',
      data: sent
    }).done(function (res) {
      clearErrors();

      if (!res || !res.status) {
        return;
      }

      $host.html(res.html);
      renderedCfg = cfgFingerprint(sent);
      applyEdits();
      applyFamily(res.family);
      applyVariant(res.variant);
      applyFamilyDefaults(res.family, res.variant, res.defaults);
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
    $('#cfgVariantOverride').val('');

    $wrap.find('[data-feature]').each(function () {
      var code = $(this).attr('data-feature');
      this.checked = !!(cfg.features && cfg.features[code]);
    });

    // A preset is a starting configuration, not a data load — force a
    // fresh detection rather than carrying the previous build over.
    lastFamilyId = null;
    run();
  }

  /* -------------------------------------------------------------------
     The ordered row list posted on Save.
     ------------------------------------------------------------------- */
  function collectRows() {
    var rows = [];

    $wrap.find('#abomBody tr.data-row').each(function () {
      var $row = $(this);
      var key  = $row.attr('data-key') || '';
      var qty  = $row.find('.qty-input').val();

      var row = {
        key:         key,
        item_id:     parseInt($row.attr('data-item'), 10) || 0,
        manual:      $row.attr('data-manual') === '1' ? 1 : 0,
        qty:         qty === undefined ? '' : qty,
        user_remark: $row.find('.remark-input').val() || ''
      };

      if (row.manual) {
        row.section = $row.attr('data-section') || '';
        $row.find('.manual-input').each(function () {
          row[$(this).attr('data-field')] = this.value;
        });
      }

      rows.push(row);
    });

    return rows;
  }

  /* -------------------------------------------------------------------
     Searchable reference-configuration picker.

     Progressive enhancement: the markup ships a working <select>, and
     this upgrades it. If this function never runs, the operator still
     has a usable dropdown rather than a dead text box.
     ------------------------------------------------------------------- */
  function initPresetCombo() {
    var $picker = $wrap.find('#abomPresetPicker');
    if (!$picker.length) { return; }

    $picker.addClass('js-combo');

    var $combo  = $picker.find('.preset-combo');
    var $search = $picker.find('#abomPresetSearch');
    var $list   = $picker.find('#abomPresetList');
    var $empty  = $list.find('.preset-empty');

    function open()  { $list.removeAttr('hidden'); $search.attr('aria-expanded', 'true'); }
    function close() { $list.attr('hidden', 'hidden'); $search.attr('aria-expanded', 'false'); }

    function filter() {
      var q = $.trim($search.val()).toLowerCase();
      var shown = 0;

      $combo.toggleClass('has-text', q !== '');

      $list.find('.preset-option').each(function () {
        var $o  = $(this);
        // Every searchable field is flattened into data-search server
        // side, so "1826", "9 track", "iqr-flm" and "continuous" all
        // match without this having to know the shape of a preset.
        var hit = q === '' || ($o.attr('data-search') || '').indexOf(q) !== -1;
        $o.toggleClass('is-active', false);
        if (hit) { $o.removeAttr('hidden'); shown++; } else { $o.attr('hidden', 'hidden'); }
      });

      if (shown === 0) { $empty.removeAttr('hidden'); } else { $empty.attr('hidden', 'hidden'); }
    }

    function visible() { return $list.find('.preset-option:not([hidden])'); }

    function choose($opt) {
      if (!$opt || !$opt.length) { return; }
      applyPreset($opt);
      $search.val($opt.find('.po-df').text());
      $combo.addClass('has-text');
      close();
    }

    $search.on('focus click', function () { filter(); open(); });
    $search.on('input', function () { filter(); open(); });

    $search.on('keydown', function (e) {
      var $vis = visible();
      var idx  = $vis.index($vis.filter('.is-active'));

      if (e.which === 40 || e.which === 38) {            // down / up
        e.preventDefault();
        open();
        idx = e.which === 40 ? Math.min(idx + 1, $vis.length - 1) : Math.max(idx - 1, 0);
        $vis.removeClass('is-active').eq(idx).addClass('is-active');
        var el = $vis.get(idx);
        if (el && el.scrollIntoView) { el.scrollIntoView({ block: 'nearest' }); }
      } else if (e.which === 13) {                       // enter
        e.preventDefault();
        choose($vis.filter('.is-active').first().length ? $vis.filter('.is-active').first() : $vis.first());
      } else if (e.which === 27) {                       // escape
        close();
      }
    });

    $list.on('click', '.preset-option', function () { choose($(this)); });

    $picker.on('click', '#abomPresetClear', function () {
      $search.val('').trigger('input').focus();
    });

    // The no-JS <select> still works if anything above fails to bind.
    $picker.on('change', '#abomPresetSelect', function () {
      choose($list.find('.preset-option[data-preset="' + $(this).val() + '"]'));
    });

    $(document).on('click.abomcombo', function (e) {
      if (!$.contains($picker.get(0), e.target)) { close(); }
    });
  }

  /* -------------------------------------------------------------------
     Insert / remove rows, and the typed REMARKS column.
     ------------------------------------------------------------------- */
  function initRowEditing() {
    // Remarks. Recorded against the row key so they survive the table
    // being re-rendered when the configuration changes.
    $wrap.on('input change', '#abomBody .remark-input', function () {
      var $row = $(this).closest('tr');
      var key  = $row.attr('data-key');
      if (!key) { return; }

      edits.remarks[key] = this.value;

      var manual = manualByKey(key);
      if (manual) { manual.user_remark = this.value; }

      $(this).toggleClass('is-filled', $.trim(this.value) !== '');
    });

    // Hand-added rows keep their typed values in the same store.
    $wrap.on('input change', '#abomBody .manual-input', function () {
      var $row   = $(this).closest('tr');
      var manual = manualByKey($row.attr('data-key'));
      if (manual) { manual[$(this).attr('data-field')] = this.value; }
    });

    $wrap.on('input change', '#abomBody tr[data-manual="1"] .qty-input', function () {
      var manual = manualByKey($(this).closest('tr').attr('data-key'));
      if (manual) { manual.qty = this.value; }
    });

    // Insert a blank row directly below the one clicked.
    $wrap.on('click', '#abomBody .row-add', function () {
      var $row = $(this).closest('tr');

      edits.seq++;
      var row = {
        key:          'm' + edits.seq,
        afterKey:     $row.attr('data-key') || '',
        section:      $row.attr('data-section') || '',
        erp_code:     '',
        description:  '',
        part_no:      '',
        manufacturer: '',
        qty:          1,
        uom:          'NO(S)',
        user_remark:  ''
      };

      edits.manual.push(row);
      insertManualRow(row);
      renumber();
      dropEmptySections();

      $wrap.find('tr[data-key="' + row.key + '"] .manual-description').focus();
      if (window.ABOM && ABOM.recalcStats) { ABOM.recalcStats(); }
    });

    // Remove a row from THIS BOM. Reversible, and it says so — the bar
    // below the table offers a restore, because a mis-click that
    // silently drops a purchasable line is not acceptable.
    $wrap.on('click', '#abomBody .row-del', function () {
      var $row = $(this).closest('tr');
      var key  = $row.attr('data-key');
      if (!key) { return; }

      if ($row.attr('data-manual') === '1') {
        for (var i = 0; i < edits.manual.length; i++) {
          if (edits.manual[i].key === key) { edits.manual.splice(i, 1); break; }
        }
      } else {
        edits.removed[key] = true;
      }

      delete edits.remarks[key];
      $row.remove();
      renumber();
      dropEmptySections();
      updateRestoreBar();
      if (window.ABOM && ABOM.recalcStats) { ABOM.recalcStats(); }
    });

    $wrap.on('click', '#abomRestoreAll', function () {
      edits.removed = {};
      updateRestoreBar();
      run();
    });
  }

  // -------------------------------------------------------------------
  $(function () {
    $wrap = $('.abom-wrap');
    if (!$wrap.length || !$('#cfgAxes').length) { return; }

    lastFamilyId = ($('#plcFamilyBadge').hasClass('plc-fx5') ? 'FX5' : 'iQ-R')
                 + '/' + ($.trim($('#abomVariantBadge').text()) || '');

    $wrap.on('input change',
      '#cfgAxes, #cfgTracks, #cfgSpeed, #cfgJ4, #cfgBattery, #cfgDfRef',
      schedule);

    $wrap.on('change',
      '#cfgMotion, #cfgModel, #cfgSide, #cfgFamilyOverride, #cfgVariantOverride, [data-feature]',
      run);

    $wrap.on('click', '.preset-card', function () {
      applyPreset($(this));
    });

    initPresetCombo();
    initRowEditing();

    $wrap.on('click', '#btnGenerate', run);

    // Save posts the CONFIGURATION plus any quantity overrides. The
    // server regenerates from the engine and treats the posted numbers
    // as overrides only — it never trusts them as computed values.
    $wrap.on('click', '#btnSave', function () {
      var $btn = $(this);

      // STALE-SHEET GUARD. If the panel has changed since the table was
      // last rendered — or a render is still in flight, or the last one
      // failed — the rows on screen belong to a different machine. The
      // server refuses that (409), so regenerate first and save on the
      // way back rather than bouncing an error off the operator.
      if (inFlight || renderedCfg === null || renderedCfg !== cfgFingerprint()) {
        $btn.prop('disabled', true).text('Recalculating…');
        var again = $btn;
        run();
        var wait = window.setInterval(function () {
          if (inFlight) { return; }
          window.clearInterval(wait);
          again.prop('disabled', false).text('Save BOM');
          if (renderedCfg === cfgFingerprint()) {
            again.trigger('click');
          } else {
            window.alert('The configuration could not be recalculated, so nothing was saved. '
                       + 'Correct the highlighted fields and try again.');
          }
        }, 120);
        return;
      }

      // A DF reference already known to be taken is refused here, so
      // the operator is not made to wait for a round trip to be told
      // something the screen is already showing. The server checks it
      // again regardless — this is not the gate.
      if (typeof window.ABOM_DFREF_TAKEN === 'function' && window.ABOM_DFREF_TAKEN()) {
        window.alert('That DF reference is already used by another BOM.\n\n' +
                     'Two BOMs cannot share one drawing number at the same revision. ' +
                     'Change the DF Reference, or raise a revision on the existing BOM.');
        $('#cfgDfRef').focus();
        return;
      }

      var data = collect();

      data.revision = $('#cfgRev').val() === '—' ? '' : $('#cfgRev').val();

      // Post the table AS IT STANDS, in order. The server still
      // regenerates from the engine and remains the authority on every
      // descriptive field and on computed_qty — this list only says
      // which rows are on the sheet, in what order, with what quantity
      // override and what remark. A generated row's description or part
      // number is never taken from the browser.
      data.rows = JSON.stringify(collectRows());

      $btn.prop('disabled', true).text('Saving…');

      $.ajax({
        url: window.ABOM_SAVE_URL,
        type: 'POST',
        dataType: 'json',
        data: data
      }).done(function (res) {
        if (res && res.status && res.redirect) {
          window.location.href = res.redirect;
          return;
        }
        $btn.prop('disabled', false).text('Save BOM');
      }).fail(function (xhr) {
        clearErrors();
        var res = null;
        try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }
        if (res && res.errors) { showErrors(res.errors); }
        window.alert(res && res.message ? res.message : 'The BOM could not be saved.');
        $btn.prop('disabled', false).text('Save BOM');
      });
    });
  });

}(window.jQuery, window, document));
