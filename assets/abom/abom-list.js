/* =====================================================================
   Automation BOM Generator — saved-BOM register behaviour

   Loaded only by application/views/abom/list.php. jQuery 2.1.4 /
   Bootstrap 3.3.7 — the versions the project already loads. No new
   library.

   Two actions, both POST, both confirmed before they fire:

     Duplicate — creates a NEW draft BOM copied from this one. The
                 source is left untouched and still current.
     Delete    — SOFT delete. The row, its lines, its approval trail and
                 its revision snapshots all stay; deleted_at is set and
                 the BOM leaves the register.

   Both are gated again on the server. Nothing here is a permission
   check — hiding a button is a courtesy, not a control.
   ===================================================================== */
(function ($, window, document) {
  'use strict';

  if (!$) { return; }

  function busy($btn, label) {
    $btn.data('label', $btn.html()).prop('disabled', true).text(label);
  }

  function idle($btn) {
    $btn.prop('disabled', false).html($btn.data('label'));
  }

  // One place for both actions: they differ only in URL, confirmation
  // wording and what they do with the reply.
  function post($btn, url, done) {
    $.ajax({
      url: url,
      type: 'POST',
      dataType: 'json'
    }).done(function (res) {
      if (res && res.status) {
        done(res);
        return;
      }
      window.alert(res && res.message ? res.message : 'The action could not be completed.');
      idle($btn);
    }).fail(function (xhr) {
      var res = null;
      try { res = JSON.parse(xhr.responseText); } catch (e) { res = null; }

      // The server's refusals are written to be read — a status rule, a
      // permission, a BOM someone else already removed. Show the message
      // it sent rather than inventing a generic one.
      window.alert(res && res.message
        ? res.message
        : 'The action could not be completed (HTTP ' + xhr.status + ').');
      idle($btn);
    });
  }

  $(function () {
    var $table = $('.abom-wrap');
    if (!$table.length) { return; }

    $table.on('click', '.abom-duplicate', function () {
      var $btn  = $(this);
      var bomNo = $btn.attr('data-bomno') || 'this BOM';

      if (!window.confirm(
            'Create a new draft BOM copied from ' + bomNo + '?\n\n' +
            'Quantities, remarks and any hand-added rows are copied. ' +
            'Approvals are not — the copy starts as an unsigned draft.\n\n' +
            bomNo + ' itself is not changed.')) {
        return;
      }

      busy($btn, 'Copying…');

      post($btn, (window.ABOM_DUPLICATE_URL || 'abom/duplicate/') + $btn.attr('data-bom'),
        function (res) {
          // Straight to the copy. Landing back on the register leaves
          // the operator hunting for a number they have not been told.
          window.location.href = res.redirect;
        });
    });

    $table.on('click', '.abom-delete', function () {
      var $btn  = $(this);
      var bomNo = $btn.attr('data-bomno') || 'this BOM';
      var lines = parseInt($btn.attr('data-lines'), 10) || 0;

      // Names the document and its size. "Are you sure?" on its own is
      // a question nobody reads.
      if (!window.confirm(
            'Delete ' + bomNo + ' (' + lines + ' line item' + (lines === 1 ? '' : 's') + ')?\n\n' +
            'It is removed from the register. The record is retained and an ' +
            'administrator can restore it, but you will not be able to from here.')) {
        return;
      }

      busy($btn, 'Deleting…');

      post($btn, (window.ABOM_DELETE_URL || 'abom/delete/') + $btn.attr('data-bom'),
        function (res) {
          // Drop the row rather than reloading: the operator keeps their
          // filters, their scroll position and their place in the list.
          var $row = $btn.closest('tr');

          $row.fadeOut(180, function () {
            $row.remove();

            if (!$table.find('#abomBomRows tr').length) {
              window.location.href = res.redirect;   // empty — show the empty state
            }
          });
        });
    });
  });

}(window.jQuery, window, document));
