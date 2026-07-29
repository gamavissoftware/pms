<script>
    (function ($) {
        $(function () {
            $('#progress_percent').on('input change', function () {
                $('#progressPercentValue').text($(this).val() + '%');
            });
        });
    })(jQuery);
</script>
