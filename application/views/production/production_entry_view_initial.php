<!DOCTYPE html>
<html>
<head>
    <title>Daily Production Entry Setup</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

    <style>
        body { background-color: #e6e9f0; font-family: Arial, sans-serif; }
        .container { 
            width: 95%; 
            max-width: 1200px;
            margin-top: 20px; 
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 { border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 25px; color: #007bff; }
        .setup-box { padding: 30px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; text-align: center; }
        .setup-box .form-control { width: 100%; }
        .setup-box .form-group { margin-bottom: 20px; }
        #load_table_btn { margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Daily Production Entry Setup</h2>

    <div class="setup-box">
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="setup_date">Production Date</label>
                    <input type="date" id="setup_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="setup_shift">Shift</label>
                    <select id="setup_shift" class="form-control" required>
                        <option value="" disabled selected>Select Shift</option>
                        <?php 
                        foreach ($shifts as $s) {
                            echo "<option value='{$s}'>Shift {$s}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="setup_operator">Operator Name</label>
                    <select id="setup_operator" class="form-control" required>
                        <option value="" disabled selected>Select Operator</option>
                        <?php 
                        foreach ($operators as $item) {
                            if (!empty($item->operator_name)) {
                                echo "<option value='{$item->operator_name}'>{$item->operator_name}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <button id="load_table_btn" class="btn btn-lg btn-primary btn-block">Load Entry Sheet</button>
            </div>
        </div>
    </div>
    
    <div id="entry_table_container" style="margin-top: 30px;">
        </div>
</div>

<script>
    const MACHINE_OPTIONS = <?php echo json_encode(array_column($machines, 'machine_no')); ?>;
    const SETUP_OPTIONS = <?php echo json_encode(array_column($setup_options, 'setup')); ?>;
    const TOOL_OPTIONS = <?php echo json_encode(array_column($tool_options, 'tool_used')); ?>;
    const OPERATION_OPTIONS = <?php echo json_encode(array_column($operation_options, 'operation')); ?>;
    const ALL_OPTIONS = {
        machine_no: MACHINE_OPTIONS,
        setup: SETUP_OPTIONS,
        tool_used: TOOL_OPTIONS,
        operation: OPERATION_OPTIONS
    };
    
    // --- Event Handler to Load Table ---
    $('#load_table_btn').click(function() {
        const date = $('#setup_date').val();
        const shift = $('#setup_shift').val();
        const operator = $('#setup_operator').val();
        const $container = $('#entry_table_container');

        if (!date || !shift || !operator) {
            alert("Please select the Date, Shift, and Operator.");
            return;
        }

        $container.html('<p class="text-center text-info">Fetching existing records...</p>');

        // 1. Fetch existing records for pre-filling
        $.ajax({
            url: "<?php echo site_url('entry/fetch_existing_logs_ajax')?>",
            type: 'POST',
            data: {
                production_date: date,
                shift: shift,
                operator_name: operator
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // 2. Load the partial HTML containing the table structure
                    $.ajax({
                        url: "<?php echo site_url('entry/load_table_partial')?>", // NEW Endpoint below
                        type: 'GET',
                        success: function(html) {
                            $container.html(html);
                            // 3. Initialize the dynamic table with the fetched data
                            initializeEntryTable(response.records, date, shift, operator, ALL_OPTIONS);
                        },
                        error: function() {
                            $container.html('<p class="text-center text-danger">Error loading table structure.</p>');
                        }
                    });
                } else {
                    $container.html('<p class="text-center text-danger">' + response.message + '</p>');
                }
            },
            error: function() {
                $container.html('<p class="text-center text-danger">Network error while fetching data.</p>');
            }
        });
    });

    // We need a helper endpoint in the controller to load the partial view
    // Since CodeIgniter doesn't have a direct route for views, you need to add this to Entry.php:
    /*
        public function load_table_partial() {
            // Pass necessary constants/data needed by the JS to re-render dropdowns/datalists
            $data['shifts'] = ['A', 'B', 'C'];
            $data['machines'] = $this->production_model->get_distinct('machine_no');
            $data['operators'] = $this->production_model->get_distinct('operator_name');
            $data['setup_options'] = $this->production_model->get_distinct('setup');
            $data['tool_options'] = $this->production_model->get_distinct('tool_used');
            $data['operation_options'] = $this->production_model->get_distinct('operation');

            $this->load->view('production_entry_table_partial', $data);
        }
    */
</script>

</body>
</html>