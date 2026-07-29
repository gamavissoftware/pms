/* =====================================================================
   Automation BOM Generator — module script

   jQuery 2.1.4 / Bootstrap 3.3.7, the versions the project already
   loads. No new library, no version bump.

   Everything is scoped to .abom-wrap and namespaced under window.ABOM,
   so nothing here can touch an existing screen.

   This file carries the VIEW-side behaviour: section filtering and the
   quantity-edited marker. The generate screen's debounced recalculation
   is added with generate.php.
   ===================================================================== */
(function ($, window, document) {
  'use strict';

  if (!$) { return; }

  var ABOM = window.ABOM || {};

  var $wrap = null;

  // -------------------------------------------------------------------
  // Section filter pills. Presentation only — filtering MUST NOT change
  // what gets saved or exported (spec 7.5).
  // -------------------------------------------------------------------
  ABOM.filterSection = function (section) {
    var $rows = $wrap.find('#abomBody tr');

    $rows.each(function () {
      var $row = $(this);
      if (section === 'ALL') {
        $row.show();
        return;
      }
      $row.toggle($row.attr('data-section') === section);
    });
  };

  // -------------------------------------------------------------------
  // Quantity edited marker. --lyellow on the QTY cell only, never the
  // row, matching the design legend: "Qty edited during review
  // (marked pencil)".
  // -------------------------------------------------------------------
  ABOM.onQtyChange = function (input) {
    var $input   = $(input);
    var computed = parseInt($input.attr('data-computed'), 10);
    var value    = parseInt($input.val(), 10);

    if (isNaN(value) || value < 0) {
      value = 0;
      $input.val(0);
    }

    var edited = (value !== computed);
    $input.toggleClass('qty-edited', edited);

    var $status = $input.closest('tr').find('td').last();
    var $badge  = $status.find('.tag-edited');

    if (edited && $badge.length === 0) {
      if ($status.find('.tag-ok').length) { $status.empty(); }
      $status.append(
        ($status.children().length ? '<br>' : '') +
        '<span class="formula-tag tag-edited">QTY EDITED ✎</span>'
      );
    } else if (!edited && $badge.length) {
      $badge.prev('br').remove();
      $badge.remove();
      if ($.trim($status.html()) === '') {
        $status.html('<span class="tag-ok">&mdash;</span>');
      }
    }

    ABOM.recalcStats();
  };

  // -------------------------------------------------------------------
  // Total Qty stat follows the visible quantities.
  // -------------------------------------------------------------------
  ABOM.recalcStats = function () {
    var total = 0;

    $wrap.find('#abomBody tr.data-row').each(function () {
      var $row   = $(this);
      var $input = $row.find('.qty-input');

      if ($input.length) {
        total += parseInt($input.val(), 10) || 0;
      } else {
        total += parseInt($row.find('.qty-static').text(), 10) || 0;
      }
    });

    $wrap.find('#statsRow .stat-card').eq(1).find('.val').text(total);
  };

  // -------------------------------------------------------------------
  $(function () {
    $wrap = $('.abom-wrap');
    if (!$wrap.length) { return; }

    $wrap.on('click', '.filter-pills .pill', function () {
      var $pill = $(this);
      $wrap.find('.filter-pills .pill').removeClass('active');
      $pill.addClass('active');
      ABOM.filterSection($pill.attr('data-filter'));
    });

    $wrap.on('change input', '.qty-input', function () {
      ABOM.onQtyChange(this);
    });

    // On a SAVED BOM the edit must reach the server. Without this the
    // markup would vanish on refresh and the print would come from
    // unedited server state.
    if (window.ABOM_LINE_QTY_URL && window.ABOM_BOM_ID) {
      $wrap.on('change', '.qty-input[data-line-id]', function () {
        var $input = $(this);

        $.ajax({
          url: window.ABOM_LINE_QTY_URL,
          type: 'POST',
          dataType: 'json',
          data: {
            bom_id:  window.ABOM_BOM_ID,
            line_id: $input.attr('data-line-id'),
            qty:     $input.val(),
            reason:  $input.attr('data-reason') || ''
          }
        }).done(function (res) {
          if (!res || !res.status) { return; }
          $input.toggleClass('qty-edited', !!res.is_overridden);
          $input.closest('tr').find('td').last().html(res.status_html);
          $input.attr('data-saved', res.qty);
          ABOM.recalcStats();
        }).fail(function (xhr) {
          var res = null;
          try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }
          window.alert(res && res.message ? res.message : 'The quantity could not be saved.');
          // Roll the field back so the screen never shows an unsaved number.
          var saved = $input.attr('data-saved');
          if (saved !== undefined) { $input.val(saved); }
          ABOM.onQtyChange($input[0]);
        });
      });
    }
  });

  window.ABOM = ABOM;

}(window.jQuery, window, document));
