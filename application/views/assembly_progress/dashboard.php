<!DOCTYPE html>
<html>
<head>
    <title>Supervisor Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <h2>Supervisor Production Dashboard</h2>
    
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
    <?php endif; ?>
    <?php if($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
    <?php endif; ?>

    <div class="panel panel-primary">
        <div class="panel-heading">Assigned / Running Jobs</div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Job (DF No)</th>
                        <th>Line</th>
                        <th>Machine</th>
                        <th>Release Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($active_jobs)): ?>
                        <?php foreach($active_jobs as $job): ?>
                        <tr>
                            <td><strong><?php echo $job->df_number; ?></strong></td>
                            <td><?php echo $job->line_name; ?></td>
                            <td><?php echo $job->machine_name; ?></td>
                            <td><?php echo $job->release_date; ?></td>
                            <td>
                                <button type="button" 
                                        class="btn btn-success btn-sm btn-report" 
                                        data-id="<?php echo $job->assignment_id; ?>"
                                        data-df="<?php echo $job->df_number; ?>">
                                    <i class="glyphicon glyphicon-camera"></i> Daily Report
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No active jobs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="reportModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Update Progress: <span id="modal_df_number"></span></h4>
      </div>
      
      <form action="<?php echo base_url('production/save_daily_log'); ?>" method="post" enctype="multipart/form-data">
          <div class="modal-body">
            <input type="hidden" name="assignment_id" id="modal_assignment_id">
            
            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="log_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Progress Status:</label>
                <select name="progress_status" class="form-control">
                    <option value="On Track">On Track</option>
                    <option value="Running Slow">Running Slow</option>
                    <option value="Material Shortage">Material Shortage</option>
                    <option value="Technical Issue">Technical Issue</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="form-group">
                <label>Remarks / Notes:</label>
                <textarea name="remarks" class="form-control" rows="3" placeholder="Enter details about today's production..."></textarea>
            </div>

            <div class="form-group">
                <label>Upload Photo (Mandatory):</label>
                <input type="file" name="site_photo" class="form-control" accept="image/*" required>
                <small class="text-muted">Max size: 4MB (JPG, PNG)</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save Report</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    // jQuery to pass data to Modal
    $(document).ready(function(){
        $('.btn-report').click(function(){
            var id = $(this).data('id');
            var df = $(this).data('df');
            
            $('#modal_assignment_id').val(id);
            $('#modal_df_number').text(df);
            $('#reportModal').modal('show');
        });
    });
</script>

</body>
</html>