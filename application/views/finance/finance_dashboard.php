<!DOCTYPE html>
<html>
<head>
    <title>Finance Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">

    <h3 class="mb-4">Finance Dashboard</h3>

    <div class="row text-center">

        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total PO Value</h5>
                    <h4>₹ <?= number_format($summary->total_po_value,2) ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Total Delivered</h5>
                    <h4>₹ <?= number_format($summary->total_delivered,2) ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Total Pending</h5>
                    <h4>₹ <?= number_format($summary->total_pending,2) ?></h4>
                </div>
            </div>
        </div>

    </div>

    <hr>

    <?php $this->load->view('finance_list'); ?>

</div>

</body>
</html>