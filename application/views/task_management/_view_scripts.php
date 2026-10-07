<script>
    (function ($) {
        $(function () {
            var $range = $('#progress_percent');
            var $label = $('#progressPercentValue');
            var $status = $('#progress_status');

            function paint(value) {
                $label.text(value + '%');
                $('.tm-preset').each(function () {
                    $(this).toggleClass('is-on', parseInt($(this).data('tm-preset'), 10) === parseInt(value, 10));
                });
            }

            $range.on('input change', function () {
                var value = $(this).val();
                paint(value);

                /* Keep the two controls telling the same story. The server
                   already forces COMPLETED at 100% and IN_PROGRESS above 0, so
                   a form showing "Open" next to 60% was about to be silently
                   overruled - now the user sees what will actually be saved. */
                if ($status.length) {
                    if (parseInt(value, 10) >= 100) {
                        $status.val('COMPLETED');
                    } else if (parseInt(value, 10) > 0 && $status.val() === 'OPEN') {
                        $status.val('IN_PROGRESS');
                    }
                }
            });

            $('.tm-preset').on('click', function () {
                $range.val($(this).data('tm-preset')).trigger('change');
            });

            $status.on('change', function () {
                if ($(this).val() === 'COMPLETED') {
                    $range.val(100).trigger('input');
                    $(this).val('COMPLETED');
                }
            });

            paint($range.val());

            $('.js-reopen-task-form').on('submit', function () {
                return window.confirm('Reopen this task and assign it back to the same user?');
            });
        });
    })(jQuery);
</script>
