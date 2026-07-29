<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart with Modal Popup</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <canvas id="orderChart" width="400" height="200"></canvas>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>SR NO.</th>
                                <th>COMPANY NAME</th>
                                <th>PO NO.</th>
                                <th>PO DATE</th>
                                <th>ORDER WON BY</th>
                                <th>ORDER VALUE</th>
                                <!-- Add more columns as needed -->
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            <!-- Content will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        function toSentenceCase(str) {
            return str.toLowerCase().replace(/(^\w|\s\w)/g, m => m.toUpperCase());
        }

        $(document).ready(function () {
            var ctx = document.getElementById('orderChart').getContext('2d');
            var orderData = <?php echo json_encode($order_data_by_brand); ?>;
            console.log("Order Data:", orderData); // Log the order data to check its structure

            var orderChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: orderData.map(order => toSentenceCase(order.name)),
                    datasets: [
                        {
                            label: 'Total Order Value',
                            data: orderData.map(order => order.total_order_value),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            yAxisID: 'y-axis-1'
                        },
                        {
                            label: 'Order Count',
                            data: orderData.map(order => order.order_count),
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1,
                            type: 'line',
                            yAxisID: 'y-axis-2'
                        }
                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                            id: 'y-axis-1',
                            position: 'left',
                            ticks: {
                                beginAtZero: true
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Total Order Value'
                            }
                        }, {
                            id: 'y-axis-2',
                            position: 'right',
                            ticks: {
                                beginAtZero: true
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Order Count'
                            }
                        }]
                    },
                    onClick: function (evt) {
                        var activeElement = orderChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, false);
                        console.log("Active Element:", activeElement); // Log the active element

                        if (activeElement.length > 0) {
                            var elementIndex = activeElement[0].index;
                            var brandName = orderChart.data.labels[elementIndex];
                            var brandTag = orderData[elementIndex].id; // Assuming tag is part of the data
                            console.log(`Clicked on: ${brandName} with tag: ${brandTag}`); // Log the brand name and tag

                            // AJAX request to fetch order details
                            $.ajax({
                                url: 'https://pms.shubhampack.in/index.php/OrderController/get_order_details/' + brandTag,
                                type: 'GET',
                                dataType: 'json',
                                success: function (data) {
                                    console.log("Fetched Data:", data); // Log the fetched data
                                    let tableBody = '';
                                    data.forEach((order, index) => {
                                        tableBody += `
                                            <tr>
                                                <td>${index + 1}</td>
                                                <td>${order.company_name}</td>
                                                <td>${order.pono}</td>
                                                <td>${order.podate}</td>
                                                <td>${order.first_name} ${order.last_name}</td>
                                                <td>${order.order_value}</td>
                                                <!-- Add more columns as needed -->
                                            </tr>
                                        `;
                                    });
                                    document.getElementById('modalTableBody').innerHTML = tableBody;
                                    // Show modal
                                    $('#orderModal').modal('show');
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error fetching order details:', error);
                                }
                            });
                        } else {
                            console.log('No active element found');
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
