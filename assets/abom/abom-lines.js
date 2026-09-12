/* =====================================================================
   Automation BOM Generator — editing the lines of a SAVED BOM

   Loaded only by application/views/abom/view.php, on top of abom.js.
   jQuery 2.1.4 / Bootstrap 3.3.7 — the versions the project already
   loads. No new library.

   HOW THIS DIFFERS FROM abom-generate.js
   --------------------------------------
   The generator holds row edits as form state and, on save, the server
   REGENERATES from the engine and treats the posted rows as a selection
   over that. Rows are keyed by master item.

   A saved BOM has no engine behind it. Its lines are a frozen snapshot,
   the master item behind a line may since have changed, and re-deriving
   them would silently rewrite a document somebody has already reviewed.
   So here:

     * rows are identified by their PERSISTED LINE ID, not by item key —
       every hand-added row has item_id NULL, so key "i0" is not unique
     * nothing is regenerated; the server writes only the fields an
       engineer may touch
     * a row that is not posted is a row that was removed

   Everything is scoped to this one BOM. No path from this screen writes
   to abom_item.
   ===================================================================== */
(function ($, window, document) {
  'use strict';

  if (!$) { return; }

  var $wrap  = null;
  var $state = null;
  var seq    = 0;          // uid for rows added but not yet written
  var dirty  = false;

  function esc(v) {
    return $('<div>').text(v == null ? '' : v).html();
  }

  function markDirty() {
    dirty = true;
    if ($state) {
      $state.text('Unsaved changes.').addClass('is-dirty');
    }
  }

  // Read by abom-config.js: applying a configuration regenerates the
  // line set, so it has to know whether there are unsaved row edits
  // about to be thrown away and say so BEFORE it fires.
  window.ABOM_LINES_DIRTY = function () { return dirty; };

  // Warn before losing work. Only while there is work to lose.
  $(window).on('beforeunload', function () {
    if (dirty) {
      return 'This BOM has unsaved row changes.';
    }
  });

  function cell(field, value, placeholder, maxlen) {
    return '<input type="text" class="manual-input manual-' + field + '"' +
           ' value="' + esc(value) + '" data-field="' + field + '"' +
           ' maxlength="' + maxlen + '" placeholder="' + esc(placeholder) + '">';
  }

  // A hand-added row inherits BOTH the section name and its sort
  // position from the row it is inserted after. Carrying only the name
  // let the server fall back to its "unknown section" order of 99, which
  // sorts after every real section — the row was saved correctly but
  // reappeared at the very bottom of the sheet under a duplicate heading.
  function newRowHtml(uid, section, sectionOrder) {
    return '<tr class="data-row is-manual-row" data-key="m' + uid + '"' +
             ' data-line-id="0" data-manual="1" data-section="' + esc(section) + '"' +
             ' data-section-order="' + (parseInt(sectionOrder, 10) || 0) + '">' +
      '<td style="text-align:center;font-weight:700;" class="sno-cell"></td>' +
      '<td style="text-align:center;">' + cell('erp_code', '', 'ERP', 32) + '</td>' +
      '<td class="desc-cell">' + cell('description', '', 'Description', 255) + '</td>' +
      '<td class="part-cell">' + cell('part_no', '', 'Part no.', 96) + '</td>' +
      '<td style="text-align:center;">' + cell('manufacturer', '', 'Manufacturer', 64) + '</td>' +
      '<td style="text-align:center;">' +
        '<input type="number" class="qty-input" min="0" value="1"></td>' +
      '<td style="text-align:center;">' +
        '<input type="text" class="manual-input manual-uom" data-field="uom" maxlength="16"' +
        ' value="NO(S)" style="width:48px;text-align:center;"></td>' +
      '<td class="remarks-cell">' +
        '<input type="text" class="remark-input" maxlength="255" placeholder="Add a remark…"></td>' +
      '<td style="text-align:center;">' +
        '<span class="formula-tag tag-manual">MANUAL ROW</span></td>' +
      '<td class="col-actions">' +
        '<button type="button" class="row-act row-add" title="Insert a blank row below this one">+</button>' +
        '<button type="button" class="row-act row-del" title="Remove this row from this BOM">&times;</button>' +
      '</td>' +
    '</tr>';
  }

  // S.NO. is positional. It must read 1..n with no gaps after any insert
  // or remove, or the printed sheet and the review notes stop agreeing.
  function renumber() {
    var n = 0;
    $wrap.find('#abomBody tr.data-row').each(function () {
      n++;
      $(this).attr('data-line', n).find('.sno-cell').text(n);
    });
  }

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

  function collectRows() {
    var rows = [];

    $wrap.find('#abomBody tr.data-row').each(function () {
      var $row = $(this);

      var row = {
        id:          parseInt($row.attr('data-line-id'), 10) || 0,
        manual:      $row.attr('data-manual') === '1' ? 1 : 0,
        qty:         $row.find('.qty-input').val(),
        user_remark: $row.find('.remark-input').val() || '',
        section:     $row.attr('data-section') || '',
        // Without this the server cannot know WHERE the section sits and
        // falls back to 99 — see newRowHtml above.
        section_order: parseInt($row.attr('data-section-order'), 10) || 0
      };

      if (row.qty === undefined) {
        // Quantity is static when the workflow has locked it. Read the
        // rendered value so saving a remark cannot zero the quantity.
        row.qty = $.trim($row.find('.qty-static').text()) || 0;
      }

      if (row.manual) {
        $row.find('.manual-input').each(function () {
          row[$(this).attr('data-field')] = this.value;
        });
      }

      rows.push(row);
    });

    return rows;
  }

  $(function () {
    $wrap = $('.abom-wrap');

    var $bar = $('#abomLinesBar');
    if (!$wrap.length || !$bar.length) { return; }      // read-only document

    $state = $('#abomLinesState');

    $wrap.on('input change', '#abomBody .remark-input, #abomBody .manual-input', function () {
      $(this).toggleClass('is-filled', $.trim(this.value) !== '');
      markDirty();
    });

    $wrap.on('input change', '#abomBody .qty-input', markDirty);

    $wrap.on('click', '#abomBody .row-add', function () {
      var $row = $(this).closest('tr');

      seq++;
      $row.after(newRowHtml(seq,
          $row.attr('data-section') || '',
          $row.attr('data-section-order') || 0));
      renumber();
      dropEmptySections();
      markDirty();

      $wrap.find('tr[data-key="m' + seq + '"] .manual-description').focus();
    });

    // Removal is immediate on screen but only committed on Save, so a
    // mis-click costs a reload rather than a document.
    $wrap.on('click', '#abomBody .row-del', function () {
      var $row = $(this).closest('tr');
      var what = $.trim($row.find('.desc-cell').text())
              || $.trim($row.find('.manual-description').val())
              || 'this row';

      if (!window.confirm('Remove ' + what + ' from this BOM?\n\n'
            + 'It is removed when you press Save changes. The master item list is '
            + 'not affected.')) {
        return;
      }

      $row.remove();
      renumber();
      dropEmptySections();
      markDirty();

      if (window.ABOM && ABOM.recalcStats) { ABOM.recalcStats(); }
    });

    $bar.on('click', '#abomSaveLines', function () {
      var $btn = $(this);
      var rows = collectRows();

      if (!rows.length) {
        window.alert('Every line has been removed. A BOM with no items cannot be saved.');
        return;
      }

      $btn.prop('disabled', true).text('Saving…');
      $state.text('Saving…').removeClass('is-dirty');

      $.ajax({
        url: window.ABOM_SAVE_LINES_URL || 'abom/save_lines',
        type: 'POST',
        dataType: 'json',
        data: { bom_id: $bar.attr('data-bom'), rows: JSON.stringify(rows) }
      }).done(function (res) {
        if (res && res.status) {
          // Reload rather than patch. The header totals, the section
          // counts, the review banner and the workflow blockers all move
          // when the line set does; re-rendering them here in three
          // places is how they drift apart.
          dirty = false;
          window.location.reload();
          return;
        }
        window.alert(res && res.message ? res.message : 'The changes could not be saved.');
        $btn.prop('disabled', false).html('&#128190; Save changes');
        $state.text('Unsaved changes.').addClass('is-dirty');
      }).fail(function (xhr) {
        var res = null;
        try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }

        window.alert(res && res.message
          ? res.message
          : 'The changes could not be saved (HTTP ' + xhr.status + '). Nothing was written.');

        $btn.prop('disabled', false).html('&#128190; Save changes');
        $state.text('Unsaved changes.').addClass('is-dirty');
      });
    });
  });

}(window.jQuery, window, document));
