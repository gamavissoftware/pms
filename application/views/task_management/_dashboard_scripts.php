<script>
    (function ($) {
        /**
         * Client-side search over an already-rendered table.
         *
         * Also shows the "nothing matches" row: before, filtering everything
         * out left an empty white box with no explanation, which reads as a
         * broken page rather than as a search with no results.
         */
        function filterTable(tableSelector, query, predicate) {
            var $table = $(tableSelector);
            var visible = 0;

            $table.find('tbody tr.tm-data-row').each(function () {
                var $row = $(this);
                var show = true;

                if (query) {
                    show = $row.text().toLowerCase().indexOf(query) !== -1;
                }
                if (show && typeof predicate === 'function') {
                    show = predicate($row);
                }

                $row.toggleClass('tm-hide-row', !show);
                if (show) visible++;
            });

            // the "no tasks at all" row must not compete with the "no match"
            // one - only ever one of them is on screen
            var hasRows = $table.find('tbody tr.tm-data-row').length > 0;
            $table.find('tbody tr.tm-no-match').prop('hidden', !(hasRows && visible === 0));
        }

        function attachTableFilter(inputSelector, tableSelector) {
            $(inputSelector).on('keyup search', function () {
                var query = $.trim($(this).val()).toLowerCase();
                $(tableSelector).data('tmQuick', null);
                $('#tmQuickChip').hide();
                filterTable(tableSelector, query);
            });
        }

        /* The stat cards were decoration - five numbers that did nothing when
           pressed. Overdue and Awaiting Due Date now filter the table below to
           exactly the rows they counted. */
        function applyQuickFilter(kind) {
            var tableSelector = '#assignedTasksTable';
            var predicate = null;

            if (kind === 'Overdue') {
                predicate = function ($row) { return $row.hasClass('is-late'); };
            } else if (kind === 'Awaiting Due Date') {
                predicate = function ($row) {
                    return $row.text().toLowerCase().indexOf('awaiting due date') !== -1;
                };
            }

            $('#assignedTaskFilter').val('');
            $(tableSelector).data('tmQuick', kind);
            filterTable(tableSelector, '', predicate);

            $('#tmQuickChipLabel').text(kind);
            $('#tmQuickChip').show();
        }

        function clearQuickFilter() {
            $('#assignedTasksTable').data('tmQuick', null);
            $('#tmQuickChip').hide();
            filterTable('#assignedTasksTable', '');
        }

        $(function () {
            attachTableFilter('#assignedTaskFilter', '#assignedTasksTable');
            attachTableFilter('#createdTaskFilter', '#createdTasksTable');

            // a chip appears next to the search box while a card filter is on,
            // so the table is never quietly showing a subset
            if ($('#assigned-table').length && !$('#tmQuickChip').length) {
                $('#assigned-table').find('.tm-section-head').append(
                    '<span id="tmQuickChip" class="tm-filter-chip" style="display:none;cursor:pointer;">' +
                    'Showing: <span id="tmQuickChipLabel"></span> <i class="fa fa-times"></i></span>'
                );
            }

            $('[data-tm-quick-filter]').on('click', function () {
                applyQuickFilter($(this).data('tm-quick-filter'));
            });

            $(document).on('click', '#tmQuickChip', clearQuickFilter);

            // filters start folded unless some are already applied
            $('#tmFilterToggle').on('click', function () {
                var $body = $('#tmFilterBody');
                var hidden = $body.prop('hidden');
                $body.prop('hidden', !hidden);
                $(this).attr('aria-expanded', hidden ? 'true' : 'false');
            });

            /* A GET form cannot carry a #fragment - the browser replaces
               everything after "?" with the query string - so filtering used
               to dump the administrator back at the top of the page. Put them
               back at the table they were working in. */
            if (window.location.search.length > 1 && $('#all-tasks-table').length) {
                var target = document.getElementById('all-tasks-table');
                if (target && target.scrollIntoView) {
                    target.scrollIntoView();
                }
            }

            /* Clearing the whole inbox in one press. The server answers with
               the new unread total, which the navigation badge reads. */
            $('#tmMarkAllRead').on('click', function () {
                var $button = $(this).prop('disabled', true).text('Clearing…');

                $.post('<?php echo page_url; ?>Task_management/mark_notification_read', {})
                    .done(function (response) {
                        $('#tm-inbox').slideUp(160);
                        var count = (response && typeof response.unread_count !== 'undefined')
                            ? response.unread_count : 0;
                        if (window.TaskNav && window.TaskNav.setUnread) {
                            window.TaskNav.setUnread(count);
                        }
                    })
                    .fail(function () {
                        $button.prop('disabled', false).text('Mark all as read');
                    });
            });
        });
    })(jQuery);
</script>
