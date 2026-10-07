<?php
$approval_user_id = (int)$this->session->userdata['logged_in']['user_id'];
if ($approval_user_id === 139) {
    $approval_ci = &get_instance();
    $approval_ci->load->model('Df_change_control_model', 'change_approval_model');
    $approval_requests = $approval_ci->change_approval_model->get_approval_queue($approval_user_id);
?>
<div class="panel panel-default" id="change-approval-queue" style="margin:20px 0;">
    <div class="panel-heading"><h4>Change Requests Awaiting Your Approval <span class="badge"><?php echo count($approval_requests); ?></span></h4></div>
    <div class="panel-body">
        <p>Review each request before releasing it to the department heads. Only Shubham Sir can approve or reject.</p>
        <?php if (empty($approval_requests)) { ?>
            <p>No change requests are awaiting approval.</p>
        <?php } else { ?>
        <div class="table-responsive" style="max-height:420px;overflow:auto;">
            <table class="table table-bordered">
                <thead><tr><th>Reference / DF</th><th>Request</th><th>Raised By</th><th>Raised On</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($approval_requests as $approval_request) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($approval_request['change_no'], ENT_QUOTES, 'UTF-8'); ?><br><?php echo htmlspecialchars($approval_request['df_id'] ? (string)$approval_request['df_no'] : 'Others', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($approval_request['title'], ENT_QUOTES, 'UTF-8'); ?><br><span class="label label-warning"><?php echo htmlspecialchars($approval_request['priority'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php echo htmlspecialchars(trim($approval_request['first_name'] . ' ' . $approval_request['last_name']), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($approval_request['created_on'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><a class="btn btn-primary btn-sm" href="<?php echo page_url; ?>Df_change_control/view/<?php echo (int)$approval_request['id']; ?>">Review / Approve / Reject</a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
