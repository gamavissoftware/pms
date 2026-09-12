/* =====================================================================
   Automation BOM Generator — the master sheet of one build

   Loaded only by application/views/abom/master_bom.php. jQuery 2.1.4 /
   Bootstrap 3.3.7 — the versions the project already loads.

   Collects the whole sheet into one JSON field and posts it as a normal
   form submit, so a refused save comes back as a re-rendered page with
   per-row messages and everything the engineer typed still in place.

   THIS EDITS MASTER DATA. Removing a row here RETIRES the item — it
   stops appearing on BOMs generated from now on and stays on every BOM
   that already contains it. The server does that; nothing here deletes.
   ===================================================================== */
(function ($, window, document) {
  'use strict';

  if (!$) { return; }

  var $body   = null;
  var $state  = null;
  var retired = [];
  var seq     = 0;
  var dirty   = false;

  function esc(v) { return $('<div>').text(v == null ? '' : v).html(); }

  function markDirty() {
    dirty = true;
    if ($state) { $state.text('Unsaved changes.').addClass('is-dirty'); }
  }

  $(window).on('beforeunload', function () {
    if (dirty) { return 'This build sheet has unsaved changes.'; }
  });

  // S.NO. is positional and must read 1..n with no gaps, or the sheet
  // stops matching the order the BOM will actually print in.
  function renumber() {
    // Numbers the VISIBLE rows. Under a source filter the sheet is a
    // view of one uploaded BOM, and 1..n is what that BOM's own numbering
    // looked like — numbering through hidden rows would show gaps that
    // mean nothing.
    var n = 0;
    $body.find('tr.data-row:visible').each(function () {
      n++;
      $(this).find('.sno-cell').text(n);
    });
  }

  function dropEmptySections() {
    $body.find('tr.sec-row').each(function () {
      var $sec = $(this);
      $sec.toggle($sec.nextUntil('tr.sec-row', 'tr.data-row').filter(':visible').length > 0);
    });
  }

  function updateRetireBar() {
    var $bar = $('#abomRetireBar');
    if (!$bar.length) { return; }

    if (!retired.length) { $bar.attr('hidden', 'hidden'); return; }

    $bar.removeAttr('hidden').html(
      retired.length + ' item' + (retired.length === 1 ? '' : 's') +
      ' will be RETIRED when you save. They stop appearing on new BOMs and remain on ' +
      'every BOM that already contains them. ' +
      '<button type="button" id="abomUndoRetire">Undo</button>'
    );
  }

  function formulaOptions() {
    // Cloned from a rendered row rather than hard-coded, so a formula
    // added in Build configuration appears here without a code change.
    var $any = $body.find('select[data-f="formula_code"]').first();
    return $any.length ? $any.html() : '<option value="FIXED">FIXED</option>';
  }

  function severityOptions() {
    var $any = $body.find('select[data-f="issue_severity"]').first();
    return $any.length ? $any.html() : '<option value="none">Clean</option>';
  }

  function newRowHtml(uid, sectionId) {
    return '<tr class="data-row is-new-row" data-id="0" data-uid="' + uid +
             '" data-section="' + sectionId + '">' +
      '<td style="text-align:center;font-weight:700;" class="sno-cell"></td>' +
      '<td><input type="text" class="mi" data-f="erp_code" maxlength="32" placeholder="none"></td>' +
      '<td><input type="text" class="mi" data-f="description" maxlength="255" placeholder="Description"></td>' +
      '<td><input type="text" class="mi" data-f="part_no" maxlength="96" placeholder="Part no."></td>' +
      '<td><input type="text" class="mi" data-f="manufacturer" maxlength="64" value="MITSUBISHI"></td>' +
      '<td><input type="number" class="mi" data-f="base_qty" min="0" max="65535" value="1"></td>' +
      '<td><select class="mi" data-f="formula_code">' + formulaOptions() + '</select></td>' +
      '<td><input type="text" class="mi" data-f="uom" maxlength="16" value="NOS"></td>' +
      '<td><input type="text" class="mi" data-f="usage_remark" maxlength="255"></td>' +
      '<td><input type="text" class="mi mi-src" data-f="source_df" maxlength="32" ' +
          'placeholder="which sheets"></td>' +
      '<td><select class="mi" data-f="issue_severity">' + severityOptions() + '</select></td>' +
      '<td class="col-actions">' +
        '<button type="button" class="row-act row-del" title="Remove this new row">&times;</button>' +
      '</td>' +
    '</tr>';
  }

  function collect() {
    var rows = [];

    $body.find('tr.data-row').each(function () {
      var $row = $(this);
      var row  = {
        id:         parseInt($row.attr('data-id'), 10) || 0,
        section_id: parseInt($row.attr('data-section'), 10) || 0,
        is_active:  1
      };

      $row.find('.mi').each(function () {
        row[$(this).attr('data-f')] = this.value;
      });

      rows.push(row);
    });

    return rows;
  }

  $(function () {
    $body = $('#abomBuildBody');
    if (!$body.length) { return; }

    $state = $('#abomBuildState');

    $body.on('input change', '.mi', markDirty);

    $('#abomAddItem').on('click', function () {
      var sectionId = $('#abomNewSection').val();

      seq++;
      var $html = $(newRowHtml(seq, sectionId));

      // Land it at the end of its own section, so the sheet stays in the
      // order the BOM will print in rather than growing a tail.
      var $last = $body.find('tr.data-row[data-section="' + sectionId + '"]').last();

      if ($last.length) {
        $last.after($html);
      } else {
        $body.append($html);
      }

      renumber();
      dropEmptySections();
      markDirty();
      $html.find('[data-f="description"]').focus();
    });

    $body.on('click', '.row-del', function () {
      var $row = $(this).closest('tr');
      var id   = parseInt($row.attr('data-id'), 10) || 0;
      var what = $row.find('[data-f="description"]').val() || 'this item';

      if (id === 0) {                       // never saved — just drop it
        $row.remove();
        renumber();
        dropEmptySections();
        markDirty();
        return;
      }

      if (!window.confirm('Retire "' + what + '" from this build?\n\n' +
            'It stops appearing on BOMs generated from now on.\n' +
            'Every BOM that already contains it is unchanged.\n\n' +
            'Nothing happens until you press Save sheet.')) {
        return;
      }

      retired.push(id);
      $row.remove();
      renumber();
      dropEmptySections();
      updateRetireBar();
      markDirty();
    });

    $(document).on('click', '#abomUndoRetire', function () {
      // Reload rather than re-insert: the rows carried selects, error
      // markers and section placement, and rebuilding them here is how
      // the sheet quietly stops matching what will be saved.
      dirty = false;
      window.location.reload();
    });

    /* -----------------------------------------------------------------
       SOURCE-BOM FILTER.

       Hides rows on screen only. collect() deliberately ignores it and
       walks EVERY data-row, because a filter that narrowed what gets
       posted would retire every item the operator had filtered out of
       view — silently, and on the seed every BOM is built from.
       ----------------------------------------------------------------- */
    $('#abomSrcPills').on('click', '.pill', function () {
      var $pill = $(this);
      var want  = $pill.attr('data-src');

      $pill.addClass('active').siblings().removeClass('active');

      $body.find('tr.data-row').each(function () {
        var $row = $(this);

        if (want === 'ALL') { $row.show(); return; }

        // Substring on a comma-joined list, bounded by the separators so
        // "DF-180" cannot match "DF-1808".
        var list = ',' + ($row.attr('data-src') || '').replace(/\s+/g, '') + ',';
        $row.toggle(list.indexOf(',' + want + ',') !== -1);
      });

      dropEmptySections();
      renumber();
    });

    $('#abomBuildForm').on('submit', function () {
      var rows = collect();

      if (!rows.length) {
        window.alert('Every item has been removed. A build with no items cannot be saved.');
        return false;
      }

      $('#abomBuildRows').val(JSON.stringify(rows));
      dirty = false;                        // the submit is the save
      $('#abomSaveBuild').prop('disabled', true).text('Saving…');
      return true;
    });
  });

}(window.jQuery, window, document));
