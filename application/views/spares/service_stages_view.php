<!DOCTYPE html>
<html>
<head>
    <title>Service Lead Stages</title>
    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .card-box { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .stage-badge { padding: 5px 12px; border-radius: 20px; font-weight: 500; background: #eef2f7; color: #333; border: 1px solid #d1d9e6; }
    </style>
</head>
<body>
    <header id="topnav"><?php $this->load->view('common/nav-menu'); ?></header>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box"><h4 class="page-title">Service Pipeline Stages</h4></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-20">Add/Edit Stage</h4>
                        <form action="<?php echo page_url; ?>ServiceStages/save_stage" method="post">
                            <input type="hidden" name="stage_id" id="stage_id">
                            <div class="form-group">
                                <label>Stage Name</label>
                                <input type="text" name="stage_name" id="stage_name" class="form-control" required placeholder="e.g. PI Received">
                            </div>
                            <div class="form-group">
                                <label>Sequence (Sort Order)</label>
                                <input type="number" name="sort_order" id="sort_order" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block waves-effect waves-light">Save Pipeline Stage</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-20">Pipeline Sequence</h4>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Step #</th>
                                    <th>Stage Label</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($stages as $s): ?>
                                <tr>
                                    <td><span class="stage-badge"><?php echo $s->sort_order; ?></span></td>
                                    <td><strong><?php echo $s->stage_name; ?></strong></td>
                                    <td>
                                        <button class="btn btn-xs btn-warning" onclick='edit_stage(<?php echo json_encode($s); ?>)'><i class="fa fa-pencil"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
    <script>
        function edit_stage(data) {
            $('#stage_id').val(data.stage_id);
            $('#stage_name').val(data.stage_name);
            $('#sort_order').val(data.sort_order);
            window.scrollTo(0,0);
        }
    </script>
</body>
</html>