<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DF List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Design File (DF) Listing</h1>
        <hr>
        <?php if (!empty($dfs)) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DF No</th>
                        <th>Added On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dfs as $df) { ?>
                        <tr>
                            <td><?php echo $df['id']; ?></td>
                            <td><?php echo $df['df_no']; ?></td>
                            <td><?php echo date('d-M-Y', strtotime($df['added_on'])); ?></td>
                            <td>
                                <a href="<?php echo page_url.'Report_Controller/gantt_chart/' . $df['id']; ?>" class="btn btn-primary btn-sm">View Gantt Chart</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert alert-warning">No Design Files found.</div>
        <?php } ?>
    </div>
</body>
</html>