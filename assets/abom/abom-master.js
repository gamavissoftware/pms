/* =====================================================================
   Automation BOM Generator — master-item screens

   Loaded by application/views/abom/master_list.php and master_form.php.
   jQuery 2.1.4 / Bootstrap 3.3.7 — the versions the project already
   loads. No new library.

   Two jobs:

     LIST — retire / restore an item. Never a delete: abom_bom_line.item_id
            points at these rows for provenance, so a deleted row would
            leave every BOM ever generated from it pointing at nothing.

     FORM — keep the Section dropdown honest against the chosen Build, and
            show only the help text for the formula actually selected.
            Both are conveniences; the server validates either way.
   ===================================================================== */
(function ($, window, document) {
  'use strict';

  if (!$) { return; }

  /* -------------------------------------------------------------------
     LIST — retire and restore
     ------------------------------------------------------------------- */
  function initList($wrap) {
    $wrap.on('click', '.abom-item-toggle', function () {
      var $btn   = $(this);
      var active = parseInt($btn.attr('data-active'), 10) === 1;   // target state
      var desc   = $btn.attr('data-desc') || 'this item';

      // Says what it does AND what it does not do. "Retire" on a parts
      // catalogue is exactly the word that makes people fear they have
      // altered documents already signed.
      var ask = active
        ? 'Restore ' + desc + '?\n\nIt will appear on BOMs generated from now on.'
        : 'Retire ' + desc + '?\n\n' +
          'It stops appearing on BOMs generated from now on.\n' +
          'Every BOM that already contains it is unchanged — each holds its own ' +
          'frozen copy of the line.';

      if (!window.confirm(ask)) { return; }

      $btn.prop('disabled', true).text(active ? 'Restoring…' : 'Retiring…');

      $.ajax({
        url: window.ABOM_ITEM_TOGGLE_URL || 'abom/master_toggle',
        type: 'POST',
        dataType: 'json',
        data: { item_id: $btn.attr('data-item'), active: active ? 1 : 0 }
      }).done(function (res) {
        if (res && res.status) {
          // Reload: the row's styling, its badges and the button's own
          // label and colour all follow the active flag, and rebuilding
          // them here in four places is how they drift.
          window.location.reload();
          return;
        }
        window.alert(res && res.message ? res.message : 'The item could not be changed.');
        $btn.prop('disabled', false).text(active ? 'Restore' : 'Retire');
      }).fail(function (xhr) {
        var res = null;
        try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }

        window.alert(res && res.message
          ? res.message
          : 'The item could not be changed (HTTP ' + xhr.status + '). Nothing was written.');

        $btn.prop('disabled', false).text(active ? 'Restore' : 'Retire');
      });
    });
  }

  /* -------------------------------------------------------------------
     FORM
     ------------------------------------------------------------------- */
  function initForm() {
    var $variant = $('#fVariant');
    var $section = $('#fSection');
    var $formula = $('#fFormula');

    if (!$variant.length) { return; }

    // A section belongs to a PLC family and so does a build. Offering
    // the other family's sections invites a combination the server then
    // rejects — better not to offer it. The <option>s are kept in the
    // DOM and only hidden, so an existing item whose section is from the
    // other family (which is a data error worth seeing) still shows what
    // it is currently set to.
    function filterSections() {
      var family = $variant.find('option:selected').attr('data-family') || '';
      var chosen = $section.val();
      var lost   = false;

      $section.find('option').each(function () {
        var $o  = $(this);
        var fam = $o.attr('data-family');

        if (!fam) { return; }                        // the "Choose…" row

        var fits = (family === '' || fam === family);
        $o.prop('disabled', !fits).toggle(fits);

        if (!fits && $o.val() === chosen) { lost = true; }
      });

      // Clear a selection the new build cannot use, rather than leaving
      // a hidden option selected and letting the form post it.
      if (lost) { $section.val(''); }
    }

    $variant.on('change', filterSections);
    filterSections();

    // Only the selected formula's explanation. Five paragraphs of help
    // text, four of which are about something else, is not help.
    function showFormulaHelp() {
      var code = $formula.val();
      $('.abom-formula-help .afh').each(function () {
        $(this).toggle($(this).attr('data-formula') === code);
      });
    }

    $formula.on('change', showFormulaHelp);
    showFormulaHelp();

    // A feature gate on a non-optional item does nothing — the engine
    // only consults it when the item is optional. Ticking one implies
    // the other; the server enforces it, this just avoids the bounce.
    $('#fFeature').on('change', function () {
      if ($(this).val() !== '') { $('#fOptional').prop('checked', true); }
    });
  }

  /* -------------------------------------------------------------------
     CONFIGURATION LIST — delete a variant, rule, section or feature.

     Refused server-side while anything still references the row. That
     refusal is the useful part: the consequences of an orphan here are
     silent — items detached from a build stop appearing on every BOM,
     and a rule pointing at a deleted variant makes generation fall
     through and quote a different machine.
     ------------------------------------------------------------------- */
  function initConfigList($wrap) {
    $wrap.on('click', '.abom-config-delete', function () {
      var $btn  = $(this);
      var label = $btn.attr('data-label') || 'this row';

      if (!window.confirm('Delete "' + label + '"?\n\n'
            + 'This is permanent. If anything still refers to it the delete will be '
            + 'refused and nothing will change.')) {
        return;
      }

      $btn.prop('disabled', true).text('Deleting…');

      $.ajax({
        url: window.ABOM_CONFIG_DELETE_URL || 'abom/config_delete',
        type: 'POST',
        dataType: 'json',
        data: { entity: $btn.attr('data-entity'), row_id: $btn.attr('data-row') }
      }).done(function (res) {
        if (res && res.status) { window.location.reload(); return; }
        window.alert(res && res.message ? res.message : 'It could not be deleted.');
        $btn.prop('disabled', false).text('Delete');
      }).fail(function (xhr) {
        var res = null;
        try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }

        window.alert(res && res.message
          ? res.message
          : 'It could not be deleted (HTTP ' + xhr.status + '). Nothing was changed.');

        $btn.prop('disabled', false).text('Delete');
      });
    });
  }

  $(function () {
    var $wrap = $('.abom-wrap');
    if (!$wrap.length) { return; }

    initList($wrap);
    initConfigList($wrap);
    initForm();
  });

}(window.jQuery, window, document));
