<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// PHP Data Encoding for JavaScript consumption
$machine_options = array_column($machines, 'machine_no');
$operator_options = array_column($operators, 'operator_name');
$shift_options = $shifts;
$setup_options = array_column($setup_options, 'setup');
$tool_options = array_column($tool_options, 'tool_used');
$operation_options = array_column($operation_options, 'operation');

$default_date = date('Y-m-d');
$default_shift = $shift_options[0] ?? 'A';
$default_operator = $operator_options[0] ?? 'N/A';

$initial_logs = json_encode($initial_logs ?? []);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Real-Time Production Entry</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

    <style>
        body { background-color: #e6e9f0; font-family: Arial, sans-serif; }
        .container { 
            width: 98%; 
            max-width: 1700px; 
            margin-top: 20px; 
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 { border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 15px; color: #007bff; }
        
        /* Table Styling for Excel-like appearance */
        #production_entry_table {
            border-collapse: collapse;
            table-layout: fixed; 
            width: 100%;
        }
        #production_entry_table thead th {
            background-color: #007bff;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
            padding: 8px 5px; 
            font-size: 11px;
            white-space: normal;
            border: 1px solid #0056b3; 
        }
        #production_entry_table td, #production_entry_table th {
            padding: 0; 
            border: 1px solid #ddd;
            height: 35px;
        }
        
        /* Input within cell (Excel-like) */
        .input-cell {
            border: none;
            width: 100%;
            height: 100%;
            padding: 6px 10px;
            box-sizing: border-box;
            background-color: transparent;
            transition: background-color 0.1s;
            font-size: 12px;
            -webkit-appearance: none;
        }
        .input-cell:focus {
            background-color: #e6f7ff; 
            outline: 1px solid #007bff;
            border-radius: 0;
        }
        .read-only {
            background-color: #f0f0f0 !important;
            color: #666;
            cursor: not-allowed;
            text-align: center;
        }
        /* Real-Time Feedback */
        .saved { background-color: #d4edda !important; transition: background-color 0.5s; }
        .error-cell { background-color: #f8d7da !important; }
        .saving { background-color: #fff3cd !important; }

        /* Status Message */
        #status-message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            background-color: #ffffff;
            border: 1px solid #ddd;
            min-height: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Real-Time Production Data Entry</h2>
    <p class="text-info">The table below contains all 24 production fields. Data saves instantly when you leave a cell. **Tab** moves to the next field. Changes to **Date, Shift, or Operator** will re-load saved logs for that session.</p>
    
    <div id="status-message" class="alert alert-info">Sheet loading...</div>

    <!-- Data Entry Table -->
    <div class="table-responsive" style="max-height: 75vh; overflow: auto;">
        <table id="production_entry_table" class="table table-hover">
            <thead>
                <tr>
                    <th class="col-index">#</th>
                    <th class="col-date">Date</th>
                    <th class="col-shift">Shift</th>
                    <th class="col-op">Operator Name</th>
                    <th class="col-mach">Machine No.</th>
                    <th class="col-df">Available Time</th>
                    <th class="col-drawing">Drawing No</th>
                    <th class="col-part">Part Name</th>
                    <th class="col-df">DF No</th>
                    <th class="col-time">Cycle Time (Min)</th>
                    <th class="col-qty">Planned Qty</th>
                    <th class="col-qty">Actual Qty</th>
                    <th class="col-qty">Qty Rejected</th>
                    <th class="col-downtime">BD (Min)</th>
                    <th class="col-downtime">Setting (Min)</th>
                    <th class="col-downtime">RM Short (Min)</th>
                    <th class="col-downtime">No Operator (Min)</th>
                    <th class="col-downtime">Other (Min)</th>
                    <th class="col-time">Total Time (Min)</th>
                    <th class="col-setup">Setup</th>
                    <th class="col-tool">Tools Used</th>
                    <th class="col-op">Operation</th>
                    <th class="col-remarks">Remarks</th>
                    <th class="col-rejection">Rejection Reason</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows injected by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<script>
    // --- PHP Data encoded into JavaScript ---
    const MACHINE_OPTIONS = <?php echo json_encode($machine_options); ?>;
    const OPERATOR_OPTIONS = <?php echo json_encode($operator_options); ?>;
    const SHIFT_OPTIONS = <?php echo json_encode($shift_options); ?>;
    const SETUP_OPTIONS = <?php echo json_encode($setup_options); ?>;
    const TOOL_OPTIONS = <?php echo json_encode($tool_options); ?>;
    const OPERATION_OPTIONS = <?php echo json_encode($operation_options); ?>;
    
    const INITIAL_LOGS_DATA = <?php echo $initial_logs; ?>;
    
    const DEFAULT_DATE = '<?php echo $default_date; ?>';
    const DEFAULT_SHIFT = '<?php echo $default_shift; ?>';
    const DEFAULT_OPERATOR = '<?php echo $default_operator; ?>'; // Should now be a string!
    
    let currentSession = {
        date: DEFAULT_DATE,
        shift: DEFAULT_SHIFT,
        operator: DEFAULT_OPERATOR
    };
    
    const ALL_LISTS = {
        machine_no: MACHINE_OPTIONS,
        setup: SETUP_OPTIONS,
        tool_used: TOOL_OPTIONS,
        operation: OPERATION_OPTIONS,
        operator_name: OPERATOR_OPTIONS 
    };
    
    const API_SAVE_URL = "<?php echo page_url.'Production/auto_save_ajax';?>";
    const API_FETCH_URL = "<?php echo page_url.'Production/fetch_existing_logs_ajax';?>";

    const DEFAULT_ROWS_TO_DISPLAY = 20;
    
    const DOWNTIME_FIELDS = ['b_d', 'setting_s', 'rm_short', 'no_operator', 'other'];
    const TIME_AFFECTING_FIELDS = ['planned_qty', 'cycle_time_minutes'].concat(DOWNTIME_FIELDS);

    let globalRowCounter = 0; 
    let currentSession = {
        date: DEFAULT_DATE,
        shift: DEFAULT_SHIFT,
        operator: DEFAULT_OPERATOR
    };
    
    // --- Field Metadata (defines all 24 fields in exact order, using the friendly JS name) ---
    const FIELD_METADATA = [
        { name: 'production_date', type: 'date', className: 'col-date' },
        { name: 'shift', type: 'select', options: SHIFT_OPTIONS, className: 'col-shift' },
        { name: 'operator_name', type: 'select', options: OPERATOR_OPTIONS, className: 'col-op' },
        { name: 'machine_no', type: 'text', list: true, className: 'col-mach', required: true },
        { name: 'available_time', type: 'number', className: 'col-df' },
        { name: 'drawing_no', type: 'text', className: 'col-drawing' },
        { name: 'part_name', type: 'text', className: 'col-part' },
        { name: 'df_no', type: 'text', className: 'col-df' },
        { name: 'cycle_time_minutes', type: 'number', className: 'col-time' },
        { name: 'planned_qty', type: 'number', className: 'col-qty' },
        { name: 'actual_qty', type: 'number', className: 'col-qty' },
        { name: 'quantity_rejected', type: 'number', className: 'col-qty' },
        { name: 'b_d', type: 'number', className: 'col-downtime' },
        { name: 'setting_s', type: 'number', className: 'col-downtime' },
        { name: 'rm_short', type: 'number', className: 'col-downtime' },
        { name: 'no_operator', type: 'number', className: 'col-downtime' }, 
        { name: 'other', type: 'number', className: 'col-downtime' },
        { name: 'total_time_minutes', type: 'number', readOnly: true, className: 'col-time' },
        { name: 'setup', type: 'text', list: true, className: 'col-setup' },
        { name: 'tool_used', type: 'text', list: true, className: 'col-tool' },
        { name: 'operation', type: 'text', list: true, className: 'col-op' },
        { name: 'remarks', type: 'text', className: 'col-remarks' },
        { name: 'rejection_reason', type: 'text', className: 'col-rejection' }
    ];

    /**
     * Creates an input or select cell.
     * CRITICAL FIX: Ensure the jQuery object is built correctly for append.
     */
    function createCell(field, value = '', readOnly = false) {
        let control;
        const td = $('<td>').addClass(field.className);

        if (field.type === 'select') {
            control = $('<select>')
                .attr({ 'class': 'input-cell', 'data-name': field.name });
            
            control.append('<option value="" selected></option>');
            field.options.forEach(option => {
                control.append(`<option value="${option}">${option}</option>`);
            });
            control.val(value);
        } else {
            const inputType = field.type;
            control = $('<input>')
                .attr({
                    'type': inputType,
                    'class': 'input-cell',
                    'data-name': field.name,
                    'value': value
                });
            
            if (inputType === 'number') {
                control.attr('min', 0).css('text-align', 'right');
                if (field.name === 'cycle_time_minutes') {
                    control.attr('step', '0.01'); 
                } else {
                    control.attr('step', '1');
                }
            }
            
            if (field.list) {
                control.attr('list', field.name + '_list');
            }
        }
        
        if (readOnly || field.readOnly) {
             control.attr('readonly', true).addClass('read-only');
        }

        // Return the constructed TD element with the control inside
        return td.append(control);
    }

    /**
     * Calculates and updates the Total Time (Min) column.
     */
    function updateRowTotalTime($row) {
        let downtimeTotal = 0;
        
        DOWNTIME_FIELDS.forEach(field => {
            const value = parseFloat($row.find(`input[data-name="${field}"]`).val()) || 0;
            downtimeTotal += value;
        });

        const plannedQty = parseFloat($row.find('input[data-name="planned_qty"]').val()) || 0;
        const cycleTime = parseFloat($row.find('input[data-name="cycle_time_minutes"]').val()) || 0;
        
        const overallTotalTime = Math.round((plannedQty * cycleTime) + downtimeTotal);
        
        $row.find('input[data-name="total_time_minutes"]').val(overallTotalTime);
    }

    /**
     * Adds a new row to the table body, or pre-fills an existing one.
     */
    function appendRow(data = {}, baseDate, baseShift, baseOperator) {
        globalRowCounter++;
        
        const $newRow = $('<tr>')
            .attr('data-row-index', globalRowCounter)
            .attr('data-log-id', data.log_id || 0);
        
        $newRow.append($('<td>').text(globalRowCounter).addClass('read-only col-index')); 
        
        FIELD_METADATA.forEach(field => {
            let cellValue = data[field.name] !== undefined && data[field.name] !== null ? data[field.name] : '';
            
            // Set base context for new rows
            if (data.log_id === undefined || data.log_id === 0) {
                if (field.name === 'production_date') cellValue = baseDate;
                if (field.name === 'shift') cellValue = baseShift;
                if (field.name === 'operator_name') cellValue = baseOperator;
            }

            // Create cell and append (calling the fixed createCell)
            $newRow.append(createCell(field, cellValue));
        });
        
        $('#production_entry_table tbody').append($newRow);

        if (data.log_id > 0) {
             updateRowTotalTime($newRow);
        }
        
        return $newRow;
    }

    /**
     * Fetches existing data or uses initial data, and initializes the table.
     */
    function fetchAndInitializeTable(date, shift, operator, isInitialLoad = false) {
        const $tbody = $('#production_entry_table tbody').empty();
        globalRowCounter = 0;
        
        currentSession = { date, shift, operator };

        $('#status-message').text('Loading logs for ' + operator + ' (' + shift + ') on ' + date + '...').removeClass('alert-success alert-danger').addClass('alert-info');

        const fetchPromise = isInitialLoad ? 
                             Promise.resolve({ status: 'success', records: INITIAL_LOGS_DATA }) : 
                             $.ajax({ 
                                 url: API_FETCH_URL, 
                                 type: 'POST', 
                                 data: { production_date: date, shift: shift, operator_name: operator }, 
                                 dataType: 'json' 
                             });

        fetchPromise.then(response => {
            if (response.status === 'success') {
                const records = response.records || [];
                
                records.forEach(record => {
                    appendRow(record, date, shift, operator);
                });

                const rowsToAdd = Math.max(DEFAULT_ROWS_TO_DISPLAY - records.length, DEFAULT_ROWS_TO_DISPLAY);
                for (let i = 0; i < rowsToAdd; i++) {
                    appendRow({}, date, shift, operator);
                }
                
                $('#status-message').text(`${records.length} existing log(s) loaded. Ready for input.`).removeClass('alert-danger alert-info').addClass('alert-success');
                setupTableHandlers();

            } else {
                $('#status-message').text('Error fetching data: ' + response.message).removeClass('alert-info alert-success').addClass('alert-danger');
            }
        }).catch(() => {
            $('#status-message').text('Network error. Could not load data.').removeClass('alert-info alert-success').addClass('alert-danger');
        });
    }


    /**
     * Sets up event handlers for blur (save) and tab (navigation)
     */
    function setupTableHandlers() {
        const $table = $('#production_entry_table');

        // 1. Blur handler for auto-save and calculation
        $table.off('blur', 'input:not(.read-only), select').on('blur', 'input:not(.read-only), select', function(e) {
            const $input = $(this);
            autoSaveCell($input);

            if (TIME_AFFECTING_FIELDS.includes($input.data('name'))) {
                updateRowTotalTime($input.closest('tr'));
                autoSaveCell($input.closest('tr').find('input[data-name="total_time_minutes"]'));
            }
        }).off('focus', 'input:not(.read-only), select').on('focus', 'input:not(.read-only), select', function() {
            const $input = $(this);
            $input.data('old-value', $input.val());
            $input.closest('tr').removeClass('saved error-cell');
        });
        
        // 2. Keydown handler for Tab navigation and dynamic row creation
        $table.off('keydown', 'input:not(.read-only), select').on('keydown', 'input:not(.read-only), select', function(e) {
            if (e.key === 'Tab' || e.keyCode === 9) {
                e.preventDefault();
                
                const $current = $(this);
                const $cells = $current.closest('tr').find('input:not(.read-only), select');
                let currentIndex = $cells.index($current);
                
                if (!e.shiftKey) { // Forward Tab
                    let nextIndex = currentIndex + 1;
                    if (nextIndex < $cells.length) {
                        $cells.eq(nextIndex).focus();
                    } else {
                        const $nextRow = $current.closest('tr').next();
                        if ($nextRow.length) {
                             $nextRow.find('input:not(.read-only), select').first().focus();
                        } else {
                            const $newRow = appendRow({}, currentSession.date, currentSession.shift, currentSession.operator);
                            $newRow.find('input:not(.read-only), select').first().focus();
                        }
                    }
                } else { // Shift + Tab (Backward)
                    let nextIndex = currentIndex - 1;
                    if (nextIndex >= 0) {
                        $cells.eq(nextIndex).focus();
                    } else {
                        const $prevRow = $current.closest('tr').prev();
                        if ($prevRow.length) {
                            $prevRow.find('input:not(.read-only), select').last().focus();
                        }
                    }
                }
            }
        });
        
        // 3. Session change handler (date, shift, operator) - Triggers data reload
        $table.off('change', 'input[data-name="production_date"], select[data-name="shift"], select[data-name="operator_name"]').on('change', 'input[data-name="production_date"], select[data-name="shift"], select[data-name="operator_name"]', function() {
            const $row = $(this).closest('tr');
            const newDate = $row.find('input[data-name="production_date"]').val();
            const newShift = $row.find('select[data-name="shift"]').val();
            const newOperator = $row.find('select[data-name="operator_name"]').val();

            if (newDate && newShift && newOperator) {
                // IMPORTANT: Update the session context for ALL rows to maintain consistency
                $table.find('tr').each(function() {
                    $(this).find('input[data-name="production_date"]').val(newDate);
                    $(this).find('select[data-name="shift"]').val(newShift);
                    $(this).find('select[data-name="operator_name"]').val(newOperator);
                    // Force blur/save on the changed cell immediately
                    autoSaveCell($(this).find(`[data-name="${$(this).data('name')}"]`)); 
                });
                
                fetchAndInitializeTable(newDate, newShift, newOperator, false);
            }
        });
    }

    /**
     * Auto-saves the cell data to the server via AJAX.
     */
    function autoSaveCell($cellInput) {
        const $row = $cellInput.closest('tr');
        const rowId = parseInt($row.attr('data-log-id') || 0); 
        const fieldName = $cellInput.data('name');
        const fieldValue = $cellInput.val();
        const oldValue = $cellInput.data('old-value');
        
        if (fieldValue === oldValue) {
             $row.removeClass('saving error-cell');
             return;
        }

        // --- Core Data Check for INSERT/UPDATE ---
        let baseData = {};
        let machineNo = '';

        // Gather all core fields for the base_data object
        FIELD_METADATA.forEach(field => {
            let value;
            if (field.type === 'select') {
                value = $row.find(`select[data-name="${field.name}"]`).val();
            } else {
                value = $row.find(`input[data-name="${field.name}"]`).val();
            }
            baseData[field.name] = value;

            if (field.name === 'machine_no') {
                machineNo = value;
            }
        });
        
        if (rowId === 0 && machineNo.trim() === '') {
             $row.addClass('error-cell');
             $('#status-message').text('ERROR: Machine No. is required to create a new log.').removeClass('text-success text-info').addClass('text-danger');
             return;
        }
        $row.removeClass('error-cell');
        
        $row.addClass('saving');
        $('#status-message').text('Saving...');

        $.ajax({
            url: API_SAVE_URL,
            type: 'POST',
            data: {
                log_id: rowId,
                name: fieldName,
                value: fieldValue,
                base_data: baseData
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $row.removeClass('saving error-cell').addClass('saved');
                    $('#status-message').text('Data saved successfully.').removeClass('text-danger').addClass('text-success');

                    if (response.log_id && rowId === 0) {
                        $row.attr('data-log-id', response.log_id);
                    }
                    
                    setTimeout(() => $row.removeClass('saved'), 1500);
                    $cellInput.data('old-value', fieldValue); 

                } else {
                    $row.removeClass('saving saved').addClass('error-cell');
                    $('#status-message').text(`ERROR: ${response.message}`).removeClass('text-success').addClass('text-danger');
                    $cellInput.val(oldValue);
                }
            },
            error: function() {
                $row.removeClass('saving saved').addClass('error-cell');
                $('#status-message').text('AJAX Error: Could not connect to server or server error.').removeClass('text-success').addClass('text-danger');
                 $cellInput.val(oldValue);
            }
        });
    }

    // --- INITIAL DOCUMENT LOAD ---
    $(document).ready(function() {
        
        // Setup Datalists
        function setupDatalists() {
            const $body = $('body');
            for (const name in ALL_LISTS) {
                let $datalist = $(`#${name}_list`);
                if ($datalist.length === 0) {
                    $datalist = $('<datalist>').attr('id', name + '_list');
                    $body.append($datalist);
                }
                ALL_LISTS[name].forEach(option => {
                    if(option && option.trim() !== '') {
                        $datalist.append(`<option value="${option}">`);
                    }
                });
            }
        }
        setupDatalists();

        // Load the initial set of data/blank rows for the default session context
        // Fetch and initialize the table using the initial data provided by PHP
        fetchAndInitializeTable(DEFAULT_DATE, DEFAULT_SHIFT, DEFAULT_OPERATOR, true);
    });
</script>

</body>
</html>
