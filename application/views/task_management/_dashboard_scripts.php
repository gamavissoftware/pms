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
            attachTableFilter('#assignedTaskFilter', '#assignedTasksTable');
            attachTableFilter('#createdTaskFilter', '#createdTasksTable');
        });
    })(jQuery);
</script>
