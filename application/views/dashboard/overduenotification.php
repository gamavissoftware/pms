<?php 
$USERIDSS = base64_encode($this->session->userdata['logged_in']['user_id']); 
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
        display: none; /* Initially hidden */
    }

    /* Popup content container */
    #blocking-overlay-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        height: 80%;
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
        display: none; /* Initially hidden */
    }

    /* Countdown timer */
    #countdown {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 18px;
        font-weight: bold;
        color: black;
        padding: 5px 10px;
        background: #f8d7da;
        border-radius: 5px;
    }

    /* Table styling */
    #popup-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    #popup-table thead th {
        background-color: #f8f9fa;
        text-align: center;
        padding: 10px;
    }

    #popup-table tbody tr {
        text-align: center;
    }

    #popup-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Pagination controls */
    #pagination-controls button {
        margin: 0 5px;
    }

    /* Button styling */
    .table-button {
        text-align: center;
        margin-top: 10px;
    }
</style>

<?php 
$q = $this->db->select('id')->from('popup_logs')->where('user_id', base64_decode($USERIDSS))->where('closedate', date('Y-m-d'))->get();
if ($q->num_rows() > 0) {
    // Popup already shown for the day
} else {

$q1= $this->db->select('id')->from('task_department_wise_scheduling')->where('assigned_user',base64_decode($USERIDSS))->where('task_status',0)->where('on_hold',1)->get();

$q2 = $this->db->select('id')->from('communication_ticket_system')->where('user_id',base64_decode($USERIDSS))->where('ticket_status',0)->get();

if($q->num_rows()>0 || $q2->num_rows()>0){
?>

<script>
$(document).ready(function () {
    const rowsPerPage = 5;
    let currentPageTickets = 1;
    let currentPageTasks = 1;

    // Initialize countdown timer
    let countdown = 60;
    const countdownInterval = setInterval(() => {
        $('#countdown').text(`Close available in ${countdown} seconds`);
        countdown--;

        if (countdown < 0) {
            clearInterval(countdownInterval);
            $('#countdown').hide(); // Hide countdown
            $('#close-popup').fadeIn(); // Show close button
        }
    }, 1000);

    // Hide the close button initially
    $('#close-popup').hide();

    // Fetch unresolved tickets and overdue tasks
    $.ajax({
        url: '<?php echo page_url."Task/fetchUnresolvedTickets"; ?>',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.status && (response.tickets.length > 0 || response.overdue_tasks.length > 0)) {
                const tickets = response.tickets;
                const tasks = response.overdue_tasks;

                // Populate tickets table
                tickets.forEach((ticket, index) => {
                    $('#tickets-table-body').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${ticket.help_ticket_no}</td>
                            <td>${ticket.df_no}</td>
                            <td>${ticket.task_name}</td>
                            <td>${ticket.remarks}</td>
                            <td>${ticket.title} ${ticket.first_name} ${ticket.last_name}</td>
                            <td>${ticket.added_on}</td>
                        </tr>
                    `);
                });

                // Populate tasks table
                tasks.forEach((task, index) => {
                    $('#tasks-table-body').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${task.df_no}</td>
                            <td>${task.task_name}</td>
                            <td>${task.end_date}</td>
                        </tr>
                    `);
                });

                // Show Popup
                $('#blocking-overlay').fadeIn();
            }
        },
        error: function () {
            console.error('Failed to fetch unresolved tickets and overdue tasks.');
        }
    });

    // Define the triggerClosePopup function
    function triggerClosePopup(action = 'closed_by_user') {
        const tickets = $('#tickets-table-body tr').length ? $('#tickets-table-body tr').map((_, row) => {
            const cells = $(row).find('td');
            return {
                help_ticket_no: cells.eq(1).text(),
                df_no: cells.eq(2).text(),
                task_name: cells.eq(3).text(),
                remarks: cells.eq(4).text(),
                added_on: cells.eq(6).text(),
                title: cells.eq(5).text()
            };
        }).get() : [];

        $.ajax({
            url: '<?php echo page_url."Task/closePopup"; ?>',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action, tickets }),
            success: function (response) {
                if (response.status) {
                    console.log('Popup closed and email sent.');
                } else {
                    console.error('Failed to send email.');
                }
            },
            error: function () {
                console.error('Failed to send popup close data to the server.');
            }
        });

        $('#blocking-overlay').fadeOut(); // Hide the popup
    }

    // Close popup on cross button
    $('#close-popup').on('click', function () {
        triggerClosePopup();
    });

    // Resolve tickets button
    $('#resolve-btn').on('click', function () {
        var userid = '<?php echo $USERIDSS;?>';
        alert(userid); return false;
        triggerClosePopup('resolve_tickets');
       window.location.href = '<?php echo page_url . "Task/helpticketsforyou/" .+userid; ?>';


    });

    // Update overdue tasks button
    $('#update-task-btn').on('click', function () {
        triggerClosePopup('update_overdue_tasks');
        window.location.href = '<?php echo page_url."Dashboard"; ?>';
    });
});
</script>

<div id="blocking-overlay">
    <div id="blocking-overlay-content">
        <div id="countdown">Close available in 60 seconds</div>
        <span id="close-popup">&times;</span>
        
        <!-- Tickets Section -->
        <h4>Unresolved Help Tickets</h4>
        <input type="text" id="ticket-search-input" placeholder="Search tickets..." style="margin-bottom: 10px; width: 100%; padding: 10px;">
        <table id="tickets-popup-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ticket No</th>
                    <th>DF No</th>
                    <th>Task Name</th>
                    <th>Remarks</th>
                    <th>Created By</th>
                    <th>Created On</th>
                </tr>
            </thead>
            <tbody id="tickets-table-body"></tbody>
        </table>
        <div class="table-button">
            <button id="resolve-btn" class="btn btn-primary">Resolve Tickets</button>
        </div>

        <!-- Tasks Section -->
        <h4>Overdue Tasks</h4>
        <input type="text" id="task-search-input" placeholder="Search tasks..." style="margin-bottom: 10px; width: 100%; padding: 10px;">
        <table id="tasks-popup-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DF No</th>
                    <th>Task Name</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody id="tasks-table-body"></tbody>
        </table>
        <div class="table-button">
            <button id="update-task-btn" class="btn btn-warning">Update Overdue Task</button>
        </div>
    </div>
</div>

<?php }?>

<?php }?>
