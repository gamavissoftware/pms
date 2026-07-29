<?php 
$USERIDSS = base64_encode($this->session->userdata['logged_in']['user_id']); 
$loggedinuserid = $this->session->userdata['logged_in']['user_id'];

// Check if the logged-in user is Rishi Sir
if ($loggedinuserid == 162 || $loggedinuserid ==209) { 
    // Fetch all tasks with `task_status = 2` and pending for more than 2 days
    $taskQuery = $this->db->select('a.id, a.start_date, a.end_date, a.task_completed_on, b.df_no, c.task_name, d.title, d.first_name, d.last_name, e.department')
        ->from('task_department_wise_scheduling a')
        ->join('df_release b', 'a.df_id = b.id', 'left')
        ->join('task_management c', 'a.taskid = c.task_id', 'left')
        ->join('system_users d', 'a.task_completed_by = d.user_id', 'left')
        ->join('departments e', 'd.department_id = e.department_id', 'left')
        ->where('a.task_status', 2) // Only completed tasks
        ->where('DATEDIFF(NOW(), a.task_completed_on) >', 2) // Tasks pending for more than 2 days
        ->order_by('a.start_date', 'ASC') // Optional: Order by start date
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
        
        <h2>Task Pending More Than 2 Days for Approval</h2>

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
                    <th style="color:#000;">Ask HOD for Reason of Delay</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach ($tasks as $task) { 
                ?>
                    <tr id="task-row-<?= $task['id']; ?>">
                        <td><?= $i++; ?></td>
                        <td><?= $task['df_no']; ?></td>
                        <td><?= ucwords(strtolower($task['task_name'])); ?></td>
                        <td><?= ucwords(strtolower($task['department'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($task['start_date'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($task['end_date'])); ?></td>
                        <td><?= date('d-M-Y', strtotime($task['task_completed_on'])); ?></td>
                        <td>
                            <?= ucwords(strtolower($task['title'] . ' ' . $task['first_name'] . ' ' . $task['last_name'])); ?>
                        </td>
                        <td>
                            <button class="btn-approve btn-xs" data-id="<?= $task['id']; ?>">Approve</button>
                            <button class="btn-reject btn-xs" data-id="<?= $task['id']; ?>">Reject</button>
                            <div class="loader" id="loader-<?= $task['id']; ?>"></div>
                        </td>
                        <td>
                            <a class="btn btn-warning btn-xs" href="<?= page_url.'Form/askReasonPage/' . $task['id']; ?>">
                            Click to Ask
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php 

        // Fetch pending help tickets for the logged-in user's department
$helpTicketQuery = $this->db->select('a.id, a.remarks, a.help_ticket_no, b.df_no, c.task_name,d.title, d.first_name, d.last_name, a.added_on, a.updated_remarks, e.title as tname, e.first_name as fname, e.last_name as lname, a.updated_on')
    ->from('communication_ticket_system a')
    ->join('df_release b','a.df_id=b.id','left')
    ->join('task_management c','a.task_id=c.task_id','left')
    ->join('system_users d', 'a.added_by = d.user_id', 'left')
    ->join('system_users e','a.updated_by=e.user_id','left')
    ->where('a.ticket_status',0)
    ->order_by('a.added_on', 'ASC')
    ->get();

$helpTickets = $helpTicketQuery->num_rows() > 0 ? $helpTicketQuery->result_array() : [];
?>

<h4 class="text-center">Pending Help Tickets Raised for your Department</h4><hr>
<style>
    #help-ticket-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    #help-ticket-table thead th {
        background-color: #4872b8;
        color: #fff;
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
    }
    #help-ticket-table tbody tr {
        text-align: center;
        background-color: #f9f9f9;
        border-bottom: 1px solid #ddd;
    }
    #help-ticket-table tbody tr:nth-child(even) {
        background-color: #f1f1f1;
    }
    #help-ticket-table tbody tr:hover {
        background-color: #f3f3f3;
    }
    input[type="text"] {
        padding: 5px;
        width: 90%;
    }
    .btn-done {
        background-color: #28a745;
        color: #fff;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

<table id="help-ticket-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Ticket No.</th>
            <th>Issue</th>
            <th>Created By</th>
            <th>Created On</th>
            <th>Remark</th>
            <th>Update on Ticket</th>
            <th>Action</th>
            <th>Ask HOD</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach ($helpTickets as $ticket) { ?>
            <tr id="ticket-row-<?= $ticket['id']; ?>">
                <td><?= $i++; ?></td>
                <td><?Php if(isset($ticket['help_ticket_no'])){echo $ticket['help_ticket_no'];} ?></td>
                <td><?= ucwords(strtolower($ticket['remarks'])); ?></td>
                
                <td><?= ucwords(strtolower($ticket['title'] . ' ' .$ticket['first_name'] . ' ' . $ticket['last_name'])); ?></td>
                <td><?= date('d-M-Y', strtotime($ticket['added_on'])); ?></td>
                <td>
    <?= ucwords(strtolower($ticket['updated_remarks'])); ?><br><br>
    <?= $ticket['tname'] . ' ' .$ticket['fname'] . ' ' . $ticket['lname']; ?><br>
    <?php
        if (!empty($ticket['updated_on']) && $ticket['updated_on'] !== '0000-00-00 00:00:00') {
            echo date('d-M-Y', strtotime($ticket['updated_on'])) . '<br> ';
            echo date('h:i A', strtotime($ticket['updated_on']));
        }
    ?>
</td>

                <td>
                    <input type="text" id="remark-<?= $ticket['id']; ?>" placeholder="Enter remark" />
                </td>
                <td>
                    <button class="btn-done btn btn-xs" data-id="<?= $ticket['id']; ?>">Mark as Done</button>

                </td>
                <td><button class="btn-ask-hod btn-xs btn-danger" data-id="<?= $ticket['id']; ?>">Ask HOD</button></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('.btn-done').on('click', function() {
        var button = $(this);
        var ticketId = button.data('id');
        var remark = $('#remark-' + ticketId).val().trim();

        if (remark === '') {
            alert('Please enter a remark before marking as done.');
            return;
        }

        // Store original text and disable the button
        var originalText = button.text();
        button.prop('disabled', true).text('Please wait...');

        $.ajax({
            url: '<?= page_url."Maintenance_support/markticketasclosed"; ?>',
            type: 'POST',
            data: { ticket_id: ticketId, remark: remark },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    $('#ticket-row-' + ticketId).fadeOut();
                    alert('Ticket marked as done successfully.');
                } else {
                    alert(res.message);
                    button.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                alert('Something went wrong. Please try again.');
                button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>

<script>
$(document).ready(function() {
    $('.btn-ask-hod').on('click', function() {
        var button = $(this);
        var ticketId = button.data('id');

        if (!confirm("Are you sure you want to notify the HOD for this ticket?")) {
            return;
        }

        var originalText = button.text();
        button.prop('disabled', true).text('Please wait...');

        $.ajax({
            url: '<?= page_url."Maintenance_support/notifyhod"; ?>',
            type: 'POST',
            data: { ticket_id: ticketId },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    alert('Your request has been successfully raised to HOD.');
                } else {
                    alert(res.message || 'Unable to notify HOD.');
                }
                button.prop('disabled', false).text(originalText);
            },
            error: function() {
                alert('Something went wrong while contacting the server.');
                button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>





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
?>
