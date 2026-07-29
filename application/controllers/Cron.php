<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This controller handles background tasks (cron jobs).
 * It CANNOT be run from a web browser.
 */
class Cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // This is CRITICAL. It ensures this controller can only be run
        // from the command line (a cron job) and not by a web browser.
        if (!$this->input->is_cli()) {
            echo "Access Denied. This script can only be run from the CLI.";
            exit;
        }

        // Load all the models and libraries your notifications need
        // Assuming your model is 'task_model' or just 'task'
        $this->load->model('task_model', 'task'); 
        $this->load->library('email');
    }

    /**
     * This is the main function your cron job will call.
     * It processes a batch of pending notifications.
     */
    public function process_notification_queue() {
        
        echo "Starting notification queue processing...\n";

        // Lock and fetch a batch of 10 pending jobs
        $this->db->trans_start();
        $query = $this->db
            ->where('status', 'pending')
            ->order_by('created_at', 'ASC')
            ->limit(10) // Process 10 at a time
            ->get('notification_queue');
        
        $jobs = $query->result();

        if (empty($jobs)) {
            echo "No pending jobs found.\n";
            $this->db->trans_complete();
            return;
        }

        // Mark the jobs as 'processing' so other cron runs don't grab them
        $job_ids = array_map(function($job) { return $job->id; }, $jobs);
        $this->db->where_in('id', $job_ids)
                 ->update('notification_queue', ['status' => 'processing']);
        $this->db->trans_complete();

        echo "Processing " . count($jobs) . " job(s)...\n";

        // Now, process each job
        foreach ($jobs as $job) {
            $payload = json_decode($job->payload, true);
            $success = false;
            $error_message = '';

            try {
                // Call the correct function based on the notification type
                switch ($job->notification_type) {
                    
                    case 'pre_closer':
                        echo "Processing pre_closer job ID: $job->id\n";
                        // This is the controller function below
                        $success = $this->preclosertasknotification(
                            $payload['df_id'],
                            $payload['task_id'],
                            $payload['user_id'],
                            $payload['remarks']
                        );
                        break;

                    case 'task_completion':
                        echo "Processing task_completion job ID: $job->id\n";
                        // These are your model functions
                        $this->task->getmessageoftaskcompletionandtrigger(
                            $payload['mastertaskid'],
                            $payload['user_id'],
                            $payload['dfid']
                        );
                        $this->task->checkiftaskispaymentstage($payload['record_id']);
                        
                        // Re-create the logic to call the final notification
                        $taskid = $this->task->gettaskidfrommaster($payload['record_id']);
                        $df_id = $this->task->getdfidfrommaster($payload['record_id']);
                        $dept = $this->task->getdepartmentoftask($taskid, $df_id);
                        $this->task->getdepartmentuserwhomdfassigned($taskid, $dept, $df_id);
                        
                        $success = true;
                        break;
                        
                    case 'previous_step':
                        echo "Processing previous_step job ID: $job->id\n";
                        // This is your model function
                        $this->task->addnotification(
                            $payload['df_id'],
                            $payload['task_remark'],
                            $payload['user_id'],
                            $payload['department_id'],
                            $payload['assigned_user'],
                            $payload['last_insert_id']
                        );
                        $success = true;
                        break;
                }

            } catch (Exception $e) {
                $success = false;
                $error_message = $e->getMessage();
            }

            // Update the job status
            if ($success) {
                // If successful, delete the job from the queue
                $this->db->where('id', $job->id)->delete('notification_queue');
                echo "Successfully processed job ID: $job->id\n";
            } else {
                // If failed, log the error and increment attempts
                $this->db->where('id', $job->id)->update('notification_queue', [
                    'status' => ($job->attempts >= 3) ? 'failed' : 'pending',
                    'attempts' => $job->attempts + 1,
                    'error_message' => $error_message,
                    'processed_at' => date('Y-m-d H:i:s')
                ]);
                echo "Failed to process job ID: $job->id. Error: $error_message\n";
            }
        }
        
        echo "Queue processing finished.\n";
    }

    /**
     * This is your original preclosertasknotification function,
     * now part of the Cron controller.
     */
    public function preclosertasknotification($df_id, $task_id, $user_id, $remarks)
    {
        // Load email library if not already loaded (already loaded in __construct)
        // $this->load->library('email');

        // Fetch the task details using the task_id
        $taskDetails = $this->db->select('task_name, task_id')
                             ->from('task_management')
                             ->where('task_id', $task_id)
                             ->get()
                             ->row_array();

        $DfDetails = $this->db->select('df_no')
                           ->from('df_release')
                           ->where('id', $df_id)
                           ->get()
                           ->row_array();

        // Fetch the user details using the user_id
        $userDetails = $this->db->select('title, first_name, last_name, email') // Added email
                            ->from('system_users')
                            ->where('user_id', $user_id)
                            ->get()
                            ->row_array();

        // Check if task and user details are found
        if (!$taskDetails || !$userDetails || !$DfDetails) {
            log_message('error', 'Task, User, or DF details not found for Task ID: ' . $task_id . ' or User ID: ' . $user_id);
            return false;
        }

        // Set sender, recipient, and email subject
        $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging - Pre Closer Task Check');
        $this->email->to('groupceo@shubhampack.com'); // Send email to the user
        //$this->email->cc('admin@example.com'); // Optional CC
        //$this->email->bcc('audit@example.com'); // Optional BCC

        // Email subject
        $subject = 'Pre-Completion Task Notification - ' . $taskDetails['task_name'];
        $markupdatedby = $userDetails['title']." ".$userDetails['first_name']." ".$userDetails['last_name'];

        // Email message with company logo and attractive design
        $message = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Task Marked as Completed</title>
        </head>
        <body style="font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
            <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <div style="background-color: #4872b8; padding: 20px; text-align: center;">
                    <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Company Logo" style="max-width: 150px;">
                </div>
                
                <div style="padding: 20px;">
                    <h2 style="color: #4872b8; text-align: center;">Task Marked as Completed</h2>

                    <p style="font-size: 16px; line-height: 1.6;">Dear Sir,</p>

                    <p style="font-size: 16px; line-height: 1.6;">
                        Please be informed that the following task has been marked as <strong>completed ahead of the planned date</strong>. Kindly review the task details below and take the appropriate action.
                    </p>

                    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                        <tr>
                            <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">DF No.</td>
                            <td style="padding: 10px; background-color: #f9f9f9;">' . $DfDetails['df_no'] . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Task Name</td>
                            <td style="padding: 10px; background-color: #f9f9f9;">' . $taskDetails['task_name'] . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Remarks updated by User </td>
                            <td style="padding: 10px; background-color: #f9f9f9;">' . ucwords(strtolower($remarks)) . '</td>
                        </tr>
                        
                        <tr>
                            <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Marked as Completed By</td>
                            <td style="padding: 10px; background-color: #f9f9f9;">' . ucwords(strtolower($markupdatedby)) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Date of Completion</td>
                            <td style="padding: 10px; background-color: #f9f9f9;">' . date('d M Y') . '</td>
                        </tr>
                    </table>

                    <p style="font-size: 16px; line-height: 1.6;">
                        We request you to review the completion status and take one of the following actions:
                    </p>

                    <ul style="font-size: 16px; line-height: 1.6;">
                        <li>Approve the completion if the task has been fully accomplished.</li>
                        <li>Rollback the status if the completion is found to be incorrect.</li>
                    </ul>

                    <p style="font-size: 16px; line-height: 1.6;">
                        To view the task details and take the necessary action, please log in to your PMS panel.
                    </p>

                    <p style="font-size: 16px; line-height: 1.6; margin-top: 20px;">
                        If you have any questions, please reach out to '.ucwords(strtolower($markupdatedby)).'
                    </p>

                    <p style="font-size: 16px; line-height: 1.6; margin-top: 20px;">
                        Best Regards, <br>
                        <strong>Shubham Flexible Packaging</strong> <br>
                        
                    </p>
                </div>

                <div style="background-color: #4872b8; padding: 10px; text-align: center; color: #fff;">
                    &copy; ' . date('Y') . ' Shubham Flexible Packaging. All Rights Reserved.
                </div>
            </div>
        </body>
        </html>
    ';


        // Set email subject and message
        $this->email->subject($subject);
        $this->email->message($message);

        // Send the email
        if ($this->email->send()) {
            log_message('info', 'Pre-completion task email successfully sent to groupceo@shubhampack.com');
            return true;
        } else {
            // Log the CodeIgniter email debugger error
            $error_log = $this->email->print_debugger(['headers']);
            log_message('error', 'Failed to send pre-completion task email. Debug: ' . $error_log);
            return false;
        }
    }
}