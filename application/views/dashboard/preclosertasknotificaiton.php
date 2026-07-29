<?php 
$USERIDSS = base64_encode($this->session->userdata['logged_in']['user_id']); 
$loggedinuserid = $this->session->userdata['logged_in']['user_id'];
echo $loggedinuserid." mangleshtest";
// Fetch team ID of the logged-in user
$teamQuery = $this->db->select('team_id')
    ->from('prestogroup_teams')
    ->where('team_leader', $loggedinuserid)
    ->get();

if ($teamQuery->num_rows() > 0) {
    $teamid = $teamQuery->row()->team_id;

    // Fetch all team members
    $teamMembersQuery = $this->db->select('employee_id')
        ->from('presto_team_members')
        ->where('team_id', $teamid)
        ->get();

    if ($teamMembersQuery->num_rows() > 0) {
        $teamMemberIDs = array_column($teamMembersQuery->result_array(), 'employee_id');

        // Fetch tasks for team members
        $taskQuery = $this->db->select('a.id, a.start_date, a.end_date, a.task_completed_on, b.df_no, c.task_name, d.title, d.first_name, d.last_name, e.department')
            ->from('task_department_wise_scheduling a')
            ->join('df_release b', 'a.df_id = b.id', 'left')
            ->join('task_management c', 'a.taskid = c.task_id', 'left')
            ->join('system_users d', 'a.task_completed_by = d.user_id', 'left')
            ->join('departments e', 'd.department_id = e.department_id', 'left')
            ->where('a.task_status', 2) // Ensure only tasks with status 2 are fetched
            ->group_start()
                ->where_in('a.task_completed_by', $teamMemberIDs) // Include all team members
                ->or_where('a.task_completed_by', $loggedinuserid) // Include logged-in user
            ->group_end()
            ->order_by('a.start_date', 'ASC') // Optional: Ensure consistent ordering
            ->get();

        if ($taskQuery->num_rows() > 0) {
            $tasks = $taskQuery->result_array();
?>
<style>
    /* Full-screen overlay */
    #blocking-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: none; 
    }

    /* Popup content container */
    #blocking-overlay-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 12px;
        width: 90%;
        height: 80%;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        overflow: auto;
    }

    /* Close button */
    #close-popup {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        color: black;
    }

    /* Table styling */
    #popup-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    #popup-table thead th {
        background-color: #4872b8;
        color: #fff;
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
    }

    #popup-table tbody tr {
        text-align: center;
        background-color: #f9f9f9;
        border-bottom: 1px solid #ddd;
    }

    #popup-table tbody tr:nth-child(even) {
        background-color: #f1f1f1;
    }

    #popup-table tbody tr:hover {
        background-color: #f3f3f3;
    }

    .btn-approve, .btn-reject {
        padding: 5px 10px;
        font-size: 14px;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-approve {
        background-color: #28a745;
    }

    .btn-reject {
        background-color: #dc3545;
    }

    .btn-approve:disabled, .btn-reject:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .loader {
        border: 5px solid #f3f3f3;
        border-radius: 50%;
        border-top: 5px solid #3498db;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        display: none; /* Initially hidden */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .search-box {
        margin-bottom: 15px;
    }

    #search-input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .confirmation-message {
        margin-top: 15px;
        padding: 10px;
        font-size: 16px;
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        display: none; /* Hidden by default */
    }
</style>

<div id="blocking-overlay">
    <div id="blocking-overlay-content">
        <span id="close-popup">&times;</span>
        
        <h2>Pending Tasks for Approval</h2>

        <div class="search-box">
            <input type="text" id="search-input" placeholder="Search tasks by DF No, Task Name, or Department...">
        </div>

        <table id="popup-table">
            <thead>
                <tr>
                    <th style="color:#000;">#</th>
                    <th style="color:#000;">DF No.</th>
                    <th style="color:#000;">Task Name</th>
                    <th style="color:#000;">Department</th>
                    <th style="color:#000;">Start Date</th>
                    <th style="color:#000;">End Date</th>
                    <th style="color:#000;">Completed On</th>
                    <th style="color:#000;">Completed By</th>
                    <th style="color:#000;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach ($tasks as $task) { 
                    $taskCompletedOn = strtotime($task['task_completed_on']);
                    $currentDate = time();
                    $daysSinceCompletion = floor(($currentDate - $taskCompletedOn) / (60 * 60 * 24)); // Difference in days
                ?>
                    <tr id="task-row-<?= $task['id']; ?>">
                        <td><?= $i++; ?></td>
                        <td><?= $task['df_no']; ?></td>
                        <td><?= ucwords(strtolower($task['task_name'])); ?></td>
                        <td><?= ucwords(strtolower($task['department'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($task['start_date'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($task['end_date'])); ?></td>
                        <td><?= date('d-M-Y', $taskCompletedOn); ?></td>
                        <td><?php echo ucwords(strtolower($task['title']));?> <?php echo ucwords(strtolower($task['first_name']));?> <?php echo ucwords(strtolower($task['last_name']));?></td>
                        <td>
                            <?php if ($daysSinceCompletion > 2) { ?>
                                <p style="color: red; font-weight: bold;">
                                    This task approval was due for more than 2 days. Please check with Rishi Sir to update.
                                </p>
                            <?php } else { ?>
                                <button class="btn-approve" data-id="<?= $task['id']; ?>">Approve</button>
                                <button class="btn-reject" data-id="<?= $task['id']; ?>">Reject</button>
                                <div class="loader" id="loader-<?= $task['id']; ?>"></div>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="confirmation-message" id="confirmation-message"></div>

        <button id="close-popup-btn">Close</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#blocking-overlay').fadeIn();

    $('#close-popup, #close-popup-btn').on('click', function() {
        $('#blocking-overlay').fadeOut();
    });

    $('.btn-approve, .btn-reject').on('click', function() {
        var taskId = $(this).data('id');
        var row = $('#task-row-' + taskId);
        var action = $(this).hasClass('btn-approve') ? 'approved' : 'rejected';
        var messageText = `Task has been successfully ${action}.`;

        row.find('.btn-approve, .btn-reject').prop('disabled', true);
        $('#loader-' + taskId).show();

        $.ajax({
            url: $(this).hasClass('btn-approve') ? '<?= page_url."Task/directmarkaspendingtoapprove"; ?>' : '<?= page_url."Task/directmarkaspendingtoreject"; ?>',
            type: 'POST',
            data: { task_id: taskId },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    row.fadeOut();
                    showConfirmationMessage(messageText);
                } else {
                    alert(res.message);
                }
            },
            complete: function() {
                $('#loader-' + taskId).hide();
            }
        });
    });

    function showConfirmationMessage(message) {
        $('#confirmation-message').text(message).fadeIn();
        setTimeout(function() {
            $('#confirmation-message').fadeOut();
        }, 5000);
    }

    $('#search-input').on('keyup', function() {
        var searchValue = $(this).val().toLowerCase();
        $('#popup-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
        });
    });
});
</script>
<?php 
        }
    }
}
?>
