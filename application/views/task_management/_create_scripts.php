<script>
    (function ($) {
        var isSubmitting = false;

        function setSubmitting(submitting) {
            isSubmitting = submitting;
            $('#taskManagementCreateForm').attr('aria-busy', submitting ? 'true' : 'false');
            $('#createTaskButton').prop('disabled', submitting);
            $('#createTaskButtonLabel').text(submitting ? 'Creating task…' : 'Create Task');
            $('#taskCreateLoading').prop('hidden', !submitting);
        }

        function showValidationGrowl(message) {
            if (typeof window.showTaskManagementGrowl === 'function') {
                window.showTaskManagementGrowl({
                    title: 'Validation Required',
                    message: message,
                    dismissLabel: 'Okay',
                    autoDismissMs: 4500
                });
            }
        }

        function toggleFieldError($field, hasError) {
            $field.toggleClass('tm-field-error', hasError);

            if ($field.hasClass('select2-hidden-accessible')) {
                $field.next('.select2').find('.select2-selection').toggleClass('tm-field-error', hasError);
            }
        }

        function markServerErrors() {
            $('#taskManagementCreateForm .tm-form-error').each(function () {
                var $fieldWrap = $(this).closest('.tm-field');
                var $field = $fieldWrap.find('input, select, textarea').first();
                if ($field.length) {
                    toggleFieldError($field, true);
                }
            });

            if ($('#taskManagementCreateForm .tm-form-error').length > 0) {
                showValidationGrowl('Please fix the highlighted fields and try again.');
            }
        }

        function validateCreateForm() {
            var missingLabels = [];
            var invalidMessages = [];

            $('#taskManagementCreateForm [data-required="1"], #taskManagementCreateForm [required]').each(function () {
                var $field = $(this);
                var value = $.trim(($field.val() || '').toString());
                var label = $field.closest('.tm-field').find('label').first().text().replace('*', '').trim();
                var hasError = value === '';

                if (hasError) {
                    missingLabels.push(label);
                } else if ($field.attr('type') === 'date') {
                    /* The form carries novalidate, so the browser will not
                       enforce min= on its own. The server rejects a past date
                       either way (callback_not_past_date); this is only so the
                       user finds out before the round trip. Both compare
                       yyyy-mm-dd strings against the same server-rendered day. */
                    var min = $field.attr('min');
                    if (min && value < min) {
                        hasError = true;
                        invalidMessages.push(label + ' cannot be in the past.');
                    }
                }

                toggleFieldError($field, hasError);
            });

            if (invalidMessages.length > 0) {
                showValidationGrowl(invalidMessages.join(' '));
                return false;
            }

            if (missingLabels.length > 0) {
                showValidationGrowl('Please fill: ' + missingLabels.join(', ') + '.');
                return false;
            }

            return true;
        }

        function updateAssigneeMeta() {
            var departments = [];
            $('#assigned_to_user_id option:selected').each(function () {
                var departmentName = $(this).data('department-name') || '';
                if (departmentName && departments.indexOf(departmentName) === -1) {
                    departments.push(departmentName);
                }
            });
            $('#selectedDepartmentName').val(departments.length ? departments.join(', ') : '-');
        }

        $(function () {
            if ($.fn.select2) {
                $('#assigned_to_user_id').select2({
                    placeholder: 'Select one or more assignees',
                    allowClear: true
                });
            }

            $('#taskManagementCreateForm').on('submit', function (event) {
                if (isSubmitting) {
                    event.preventDefault();
                    return false;
                }
                if (!validateCreateForm()) {
                    event.preventDefault();
                    return false;
                }
                setSubmitting(true);
            });

            $(window).on('pageshow', function () {
                setSubmitting(false);
            });

            $('#taskManagementCreateForm').find('input, select, textarea').on('change keyup', function () {
                toggleFieldError($(this), false);
            });

            $('#assigned_to_user_id').on('change', function () {
                updateAssigneeMeta();
                toggleFieldError($(this), false);
            });

            updateAssigneeMeta();
            markServerErrors();
        });
    })(jQuery);
</script>
