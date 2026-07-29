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
    var $rows = $wrap.find('#bomBody tr');

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

    $wrap.find('#bomBody tr.data-row').each(function () {
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
  });

  window.ABOM = ABOM;

}(window.jQuery, window, document));
