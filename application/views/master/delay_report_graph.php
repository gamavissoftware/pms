<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delay Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2>Department-Wise Delay Report</h2>
   <select id="df-selector">
    <option value="1">DF1</option>
    <option value="9">DF2</option>
    <!-- Populate dynamically -->
</select>
<canvas id="delayChart" width="400" height="200"></canvas>



    <script>
        $(document).on('change', '#df-selector', function () {
    let selectedDF = $(this).val();
    $.ajax({
        url: '<?php echo page_url;?>Task/get_delay_data_by_df',
        type: 'POST',
        data: { df_name: selectedDF },
        success: function (response) {
            let data = JSON.parse(response);
            let departments = data.map(item => item.department);
            let delays = data.map(item => item.max_delay);

            // Update the chart
            updateChart(departments, delays);
        }
    });
});

function updateChart(departments, delays) {
    const ctx = document.getElementById('delayChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: departments,
            datasets: [{
                label: 'Max Delay (days)',
                data: delays,
            }]
        }
    });
}

    </script>
</body>
</html>
