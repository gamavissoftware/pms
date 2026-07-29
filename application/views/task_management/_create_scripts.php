<script>
    (function ($) {
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

            $('#taskManagementCreateForm [data-required="1"], #taskManagementCreateForm [required]').each(function () {
                var $field = $(this);
                var value = $.trim(($field.val() || '').toString());
                var hasError = false;

                if ($field.is('select')) {
                    hasError = value === '';
                } else if ($field.attr('type') === 'date') {
                    hasError = value === '';
                } else {
                    hasError = value === '';
                }

                toggleFieldError($field, hasError);

                if (hasError) {
                    var label = $field.closest('.tm-field').find('label').first().text().replace('*', '').trim();
                    missingLabels.push(label);
                }
            });

            if (missingLabels.length > 0) {
                showValidationGrowl('Please fill: ' + missingLabels.join(', ') + '.');
                return false;
            }

            return true;
        }

        function updateAssigneeMeta() {
            var selected = $('#assigned_to_user_id option:selected');
            var departmentName = selected.data('department-name') || '-';
            $('#selectedDepartmentName').val(departmentName);
        }

        $(function () {
            if ($.fn.select2) {
                $('#assigned_to_user_id').select2({
                    placeholder: 'Select assignee',
                    allowClear: true
                });
            }

            $('#taskManagementCreateForm').on('submit', function (event) {
                if (!validateCreateForm()) {
                    event.preventDefault();
                    return false;
                }
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
