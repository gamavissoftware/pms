/**
 * abom-dfref.js — live "is this DF reference already used?" check.
 *
 * Shared by the generator and by the configuration panel of a saved,
 * still-editable BOM, because the mistake is the same on both screens:
 * a DF reference is an engineering drawing number, and two live BOMs
 * carrying the same one at the same revision are two documents both
 * claiming to be the parts list for one drawing.
 *
 * WHEN IT ASKS
 * On blur, and on a 500 ms pause in typing. Not on every keystroke —
 * "DF-1" and "DF-18" are not questions anybody meant to ask, and each
 * one would be a round trip.
 *
 * WHAT IT DECIDES
 * Nothing. Abom::save() and Abom::save_config() re-run the same check
 * on the server before writing. This is here so the answer arrives
 * while the field still has focus, instead of after a machine has been
 * configured and forty lines generated. With JavaScript off, or with a
 * stale answer on screen, the save still refuses correctly.
 *
 * jQuery 2.1.4, ES5. No new libraries.
 */
(function ($, window, document) {
  'use strict';

  if (!$) { return; }

  var WAIT   = 500;
  var timer  = null;
  var last   = null;      // last value actually asked about
  var $field = null;
  var $note  = null;

  function esc(v) {
    return $('<div>').text(v == null ? '' : v).html();
  }

  function revision() {
    var v = $.trim(String($('#cfgRev').val() || ''));
    return (v === '' || v === '—') ? '' : v;   // em dash = "none yet"
  }

  // The BOM being edited, so it is not reported as clashing with
  // itself. Absent on the generator, which is creating a new one.
  function bomId() {
    var $btn = $('#abomSaveConfig');
    return $btn.length ? $btn.attr('data-bom') : '';
  }

  function clear() {
    $field.removeClass('is-invalid is-taken');
    $note.attr('class', 'dfref-note').empty().hide();
  }

  function show(state, html) {
    $note.attr('class', 'dfref-note dfref-' + state).html(html).show();
  }

  function ask() {
    var value = $.trim(String($field.val() || ''));

    if (value === '') { last = ''; clear(); return; }

    var key = value + '|' + revision();
    if (key === last) { return; }
    last = key;

    show('checking', 'Checking…');

    $.ajax({
      url: window.ABOM_CHECK_DFREF_URL || 'abom/check_df_ref',
      type: 'POST',
      dataType: 'json',
      data: { df_ref: value, revision: revision(), bom_id: bomId() }
    }).done(function (res) {
      // The field may have moved on while this was in flight. An
      // answer about a value the operator has already replaced is
      // worse than no answer.
      if ($.trim(String($field.val() || '')) !== value) { return; }

      if (!res || !res.status) { clear(); return; }

      if (!res.taken) {
        $field.removeClass('is-invalid is-taken');
        show('free', '✓ ' + esc(value) + ' is not used by any other BOM.');
        return;
      }

      var c = res.conflict || {};
      $field.addClass('is-invalid is-taken');
      show('taken',
        '<strong>Already used.</strong> ' +
        esc(value) + ' belongs to <a href="' + esc(c.url) + '" target="_blank">' +
        esc(c.bom_no) + (c.revision ? ' rev ' + esc(c.revision) : '') + '</a>' +
        (c.status ? ' (' + esc(c.status) + ')' : '') + '. ' +
        'Use a different reference, or raise a revision on that BOM.');
    }).fail(function () {
      // A failed check must not look like a pass. Say nothing rather
      // than imply the reference is free.
      last = null;
      clear();
    });
  }

  // Read by the save handlers so a known clash is refused before the
  // request is built. Not a substitute for the server check.
  window.ABOM_DFREF_TAKEN = function () {
    return $field !== null && $field.hasClass('is-taken');
  };

  $(function () {
    $field = $('#cfgDfRef');

    // Absent, or rendered read-only on a released document: nothing to
    // check and nothing the operator could change if there were.
    if (!$field.length || $field.prop('readonly')) { $field = null; return; }

    $note = $('<div class="dfref-note">').hide();
    $field.after($note);

    $field.on('input', function () {
      $field.removeClass('is-invalid is-taken');
      $note.hide();
      window.clearTimeout(timer);
      timer = window.setTimeout(ask, WAIT);
    });

    $field.on('blur', function () {
      window.clearTimeout(timer);
      ask();
    });

    // A reference already in the box when a clone opens is worth
    // checking unprompted — that is exactly the case where somebody
    // inherits a number they did not choose.
    if ($.trim(String($field.val() || '')) !== '') { ask(); }
  });

}(window.jQuery, window, document));
