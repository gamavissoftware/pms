<?php 
$CIA =& get_instance();
$CIA->load->model('Dashboard_model');
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
    <title><?php echo sitetitle; ?> Sales Order Filter by Brand</title>
    <!-- Table Responsive css -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            
            background-color: #f9f9f9;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header select {
            padding: 5px;
            font-size: 14px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }

        .card h3 {
            font-size: 18px;
            margin: 0 0 10px;
        }

        .card p {
            font-size: 16px;
            margin: 0;
            font-weight: bold;
        }

        .card i {
            font-size: 24px;
            color: #049dd4;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .charts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .chart-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chart-container canvas {
            width: 100% !important;
            height: auto !important;
        }

        .notification {
            background-color: #049dd4;
            color: white;
            padding: 15px;
            border-radius: 5px;
            position: fixed;
            top: 20px;
            right: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .header select {
                margin-top: 10px;
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 15px;
            }

            .card h3 {
                font-size: 16px;
            }

            .card p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu');?>
    </header>
    <!-- End Navigation Bar-->

    <div class="wrapper">
        <div class="container-fluid">
            
        <div class="header">
            <h1>Hello Olivia</h1>
            <select>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
            </select>
        </div>

        <div class="cards">
                <div class="card">
                <h3>Total Sales</h3>
                <hr>
                <p><span id="total-sales">₹0.00</span></p>
                <i class="fa fa-inr"></i>
                </div>

            <div class="card">
                <h3>Inquiry Success Rate</h3><hr>
                <p><span id="success-rate">0%</span></p>
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="card">
                <h3>New Clients</h3><hr>
                <p><span id="new-clients">0</span></p>
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="card">
                <h3>Total Order Won</h3><hr>
                <p><span id="order-won">0</span></p>
                <i class="fas fa-trophy"></i>
            </div>
              <div class="card">
                <h3>Quotation Follow-up</h3><hr>
                <p><span id="quotation-follow-up">0</span></p>
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="charts">
        <div class="chart-container">
        <canvas id="orderBreakdownChart"></canvas>
        </div>


            <script type="text/javascript">
                $(document).ready(function() {
    function fetchOrderBreakdown() {
        $.ajax({
    url: "<?php echo page_url.'OrderController/get_order_breakdown'; ?>",
    type: "GET",
    dataType: "json",
    success: function(response) {
        console.log(response); // Check the response in the browser console
        if (response.domestic !== undefined && response.international !== undefined) {
            updateOrderBreakdownChart(response.domestic, response.international);
        } else {
            console.error("Invalid data received for order breakdown");
        }
    },
    error: function(xhr, status, error) {
        console.error("Error fetching order breakdown:", error);
    }
});

    }

    // Function to update the chart
    function updateOrderBreakdownChart(domestic, international) {
        const ctx = document.getElementById('orderBreakdownChart').getContext('2d');
        if (window.orderBreakdownChart) {
            // If chart exists, update its data
            window.orderBreakdownChart.data.datasets[0].data = [domestic, international];
            window.orderBreakdownChart.update();
        } else {
            // If chart doesn't exist, create it
            window.orderBreakdownChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Domestic', 'International'],
                    datasets: [{
                        data: [domestic, international],
                        backgroundColor: ['#049dd4', '#C0C0C0']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    }
                }
            });
        }
    }

    // Fetch order breakdown data on page load
    fetchOrderBreakdown();

    // Optional: Refresh the chart dynamically at intervals
    setInterval(fetchOrderBreakdown, 60000); // Refresh every 60 seconds
});

            </script>
            <div class="chart-container">
                <canvas id="inquiriesPerMonthChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="incomePerQuarterChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="incomePerMonthChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="inquirySourceBreakdownChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="incomeSourcePerMonthChart"></canvas>
            </div>
        </div>
    

    <div class="notification" id="notification">Welcome back, Olivia! Here's your dashboard summary.</div>

    <script>
        // Show notification
        const notification = document.getElementById('notification');
        setTimeout(() => {
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }, 1000);

        // Inquiry Breakdown Chart
        new Chart(document.getElementById('inquiryBreakdownChart'), {
            type: 'doughnut',
            data: {
                labels: ['Booked', 'Flopped'],
                datasets: [{
                    data: [36.2, 63.8],
                    backgroundColor: ['#049dd4', '#C0C0C0']
                }]
            },
            options: {
                responsive: true
            }
        });

        // Inquiries Per Month Chart
        new Chart(document.getElementById('inquiriesPerMonthChart'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Inquiries',
                    data: [5, 10, 15, 20, 25, 30, 15, 20, 25, 30, 35, 40],
                    backgroundColor: '#049dd4'
                }]
            },
            options: {
                responsive: true
            }
        });

        // Income Per Quarter Chart
        new Chart(document.getElementById('incomePerQuarterChart'), {
            type: 'pie',
            data: {
                labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                datasets: [{
                    data: [13.1, 28.6, 28, 30.3],
                    backgroundColor: ['#049dd4', '#C0C0C0', '#9370DB', '#B0C4DE']
                }]
            },
            options: {
                responsive: true
            }
        });

        // Income Per Month Chart
        new Chart(document.getElementById('incomePerMonthChart'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Income',
                    data: [10000, 15000, 20000, 25000, 30000, 35000, 40000, 45000, 50000, 55000, 60000, 65000],
                    borderColor: '#049dd4',
                    fill: false
                }]
            },
            options: {
                responsive: true
            }
        });

        // Inquiry Source Breakdown Chart
        new Chart(document.getElementById('inquirySourceBreakdownChart'), {
            type: 'bar',
            data: {
                labels: ['Web', 'Email', 'Instagram', 'TikTok', 'Pinterest'],
                datasets: [{
                    label: 'Sources',
                    data: [70, 50, 30, 20, 10],
                    backgroundColor: ['#049dd4', '#C0C0C0', '#9370DB', '#B0C4DE', '#D8BFD8']
                }]
            },
            options: {
                responsive: true
            }
        });

        // Income Source Per Month Chart
        new Chart(document.getElementById('incomeSourcePerMonthChart'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Income Source',
                    data: [20000, 25000, 30000, 35000, 40000, 45000, 50000, 55000, 60000, 65000, 70000, 75000],
                    backgroundColor: ['#049dd4']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>

    <script type="text/javascript">
     $(document).ready(function() {
    function fetchTotalSales() {
        $.ajax({
            url: "<?php echo page_url.'OrderController/get_total_sales'; ?>", // URL to the controller method
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.total_sales) {
                    $('#total-sales').text(response.total_sales); // Display formatted sales from backend
                } else {
                    $('#total-sales').text('₹0.00'); // Default value if no data
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching total sales:", error);
            }
        });
    }

    // Fetch total sales on page load
    fetchTotalSales();

    // Optional: Refresh the total sales dynamically at intervals (e.g., every minute)
    setInterval(fetchTotalSales, 60000); // Refresh every 60 seconds
});

     $(document).ready(function() {
    function fetchNewClients() {
        $.ajax({
            url: "<?php echo page_url.'OrderController/get_new_clients'; ?>", // URL to the controller method
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.new_clients !== undefined) {
                    $('#new-clients').text(response.new_clients); // Update the count of new clients
                } else {
                    $('#new-clients').text('0'); // Default value if no data
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching new clients:", error);
            }
        });
    }

    // Fetch new clients on page load
    fetchNewClients();

    // Optional: Refresh new clients count dynamically at intervals
    setInterval(fetchNewClients, 60000); // Refresh every 60 seconds
});


     $(document).ready(function() {
    function fetchInquirySuccessRate() {
        $.ajax({
            url: "<?php echo page_url.'OrderController/get_inquiry_success_rate'; ?>", // URL to the controller method
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success_rate !== undefined) {
                    $('#success-rate').text(`${response.success_rate}%`); // Update the success rate
                } else {
                    $('#success-rate').text('0%'); // Default value if no data
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching inquiry success rate:", error);
            }
        });
    }

    // Fetch inquiry success rate on page load
    fetchInquirySuccessRate();

    // Optional: Refresh inquiry success rate dynamically at intervals
    setInterval(fetchInquirySuccessRate, 60000); // Refresh every 60 seconds
});


$(document).ready(function() {
    function fetchNewClients() {
        $.ajax({
            url: "<?php echo page_url.'OrderController/get_new_clients'; ?>", // URL to the controller method
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.new_clients !== undefined) {
                    $('#new-clients').text(response.new_clients); // Update the count of new clients
                } else {
                    $('#new-clients').text('0'); // Default value if no data
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching new clients:", error);
            }
        });
    }

    // Fetch new clients on page load
    fetchNewClients();

    // Optional: Refresh new clients count dynamically at intervals
    setInterval(fetchNewClients, 60000); // Refresh every 60 seconds
});


$(document).ready(function() {
    function fetchOrderWon() {
        $.ajax({
            url: "<?php echo page_url.'OrderController/get_order_won'; ?>", // URL to the controller method
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.order_won !== undefined) {
                    $('#order-won').text(response.order_won); // Update the count of orders won
                } else {
                    $('#order-won').text('0'); // Default value if no data
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching order won:", error);
            }
        });
    }

    // Fetch order won data on page load
    fetchOrderWon();

    // Optional: Refresh order won data dynamically at intervals
    setInterval(fetchOrderWon, 60000); // Refresh every 60 seconds
});


$(document).ready(function() {
    function fetchQuotationFollowUp() {
        $.ajax({
            url: "<?php echo page_url.'OrderController/get_quotation_follow_up'; ?>", // URL to the controller method
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.quotation_follow_up !== undefined) {
                    $('#quotation-follow-up').text(response.quotation_follow_up); // Update the count
                } else {
                    $('#quotation-follow-up').text('0'); // Default value if no data
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching quotation follow-up:", error);
            }
        });
    }

    // Fetch quotation follow-up data on page load
    fetchQuotationFollowUp();

    // Optional: Refresh data dynamically at intervals
    setInterval(fetchQuotationFollowUp, 60000); // Refresh every 60 seconds
});





    </script>





            <!-- Footer -->
            <?php $this->load->view('common/footer');?>
            <!-- End Footer -->
        </div> <!-- end container -->
    </div>
    <!-- end wrapper -->

    <!-- jQuery  -->
    <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
    <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url;?>js/detect.js"></script>
    <script src="<?php echo assets_url;?>js/fastclick.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url;?>js/waves.js"></script>
    <script src="<?php echo assets_url;?>js/wow.min.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>
   
    <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>
    <!-- App js -->
    <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
    <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
   
</body>
</html>
