<script>
    (function ($) {
        function attachTableFilter(inputSelector, tableSelector) {
            $(inputSelector).on('keyup', function () {
                var query = $.trim($(this).val()).toLowerCase();

                $(tableSelector).find('tbody tr.tm-data-row').each(function () {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(query) !== -1);
                });
            });
        }

        $(function () {
            attachTableFilter('#weeklyMeetingPointFilter', '#weeklyMeetingPointsTable');

            $('.js-point-update').on('click', function () {
                var trigger = $(this);
                var pointId = trigger.data('point-id');
                var pointText = trigger.data('point-text') || '';
                var status = trigger.data('status');
                var remarks = trigger.data('remarks') || '';

                $('#weeklyMeetingPointUpdateForm').attr('action', '<?php echo page_url; ?>Task/updatedfweeklymeetingpointstatus/' + pointId);
                $('#weeklyMeetingPointText').text(pointText);
                $('#weeklyMeetingPointStatus').val(String(status));
                $('#weeklyMeetingPointRemarks').val(remarks);
                $('#weeklyMeetingPointModal').modal('show');
            });
        });
    })(jQuery);
</script>
