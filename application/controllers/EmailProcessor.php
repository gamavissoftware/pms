<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmailProcessor extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    
   
        $this->load->model('User_model','user');
        $this->load->model('Master_model','master');
        $config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'taskmanagement@shubhampack.com',
        'smtp_pass' => 'ficihlqnfcdrrqkb',
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
         $this->email->initialize($config);
        $this->email->set_mailtype("html");
    }


    public function process_queue()
    {
            $query = $this->db->select('*')->from('queue_emails')->where('status',0)->limit(30)->get();
        $emails =$query->result();
        foreach ($emails as $email) {
            $this->send_email($email->to_email, $email->subject, $email->message, $email->attachment);
            $this->db->where('id', $email->id)
                     ->update('queue_emails', ['status' => 1, 'sent_at' => date('Y-m-d H:i:s')]);
        }
    }

private function send_email($to, $subject, $message, $attachment = null)
{
    $attachment_path = UPLOADPATH . 'maintenance/' . $attachment; // Adjust the path as needed

    $this->load->library('email');
    $this->email->set_mailtype("html");
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Pack DF Related Help Ticket');

    // Handle single or multiple recipients
    if (is_array($to)) {
        $this->email->to(implode(',', $to));
    } else {
        $this->email->to($to);
    }
    //$this->email->to('mangleshup@gmail.com');

    // Add CC (if required)
    $this->email->cc('groupceo@shubhampack.com');

    // Set email subject and message
    $this->email->subject($subject);
    $this->email->message($message);

    // Attach file if provided
    if (!empty($attachment) && file_exists($attachment_path)) {
        $this->email->attach($attachment_path);
    }

    // Send the email and return the result
    return $this->email->send();
}

public function notify_hods() {
        $this->load->model('Ticket_model');
        $this->load->library('email'); // Email library

        // Fetch tickets pending for the last 2 days
        $pending_tickets = $this->Ticket_model->get_tickets_without_update(2);

        foreach ($pending_tickets as $ticket) {
            $hod_email = 'mangleshup@gmail.com';
            //$hod_email = $ticket['email'];
            //$higher_email = "groupceo@shubhampack.com";
            $higher_email = "mangleshup@gmail.com";
            $help_ticket_no = $ticket['help_ticket_no'];

            // Send email to HOD if within 2 days
            if (strtotime($ticket['added_on']) >= strtotime('-4 days')) {
                $this->_send_email(
                    $hod_email,
                    "Pending Ticket Reminder - $help_ticket_no",
                    "Ticket #$help_ticket_no is pending for more than 2 days. Please review."
                );
            }

            // Send email to higher authority if beyond 4 days
            if (strtotime($ticket['added_on']) < strtotime('-4 days')) {
                $this->_send_email(
                    $higher_email,
                    "Escalation: Pending Ticket - $help_ticket_no",
                    "Ticket #$help_ticket_no is pending for more than 4 days. Immediate attention is required."
                );
            }
        }
    }



    private function _send_email($to, $subject, $message) {
    $email_body = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='text-align: center; padding: 10px;'>
                <img src='https://pms.shubhampack.in/assets/images/shubhampack.png' alt='Shubham Pack' style='max-width: 200px; margin-bottom: 20px;'>
            </div>
            <div style='background: #f4f4f4; padding: 20px; border-radius: 8px;'>
                <h2 style='color: #4872b8;'>$subject</h2>
                <p>$message</p>
            </div>
            <div style='text-align: center; margin-top: 20px;'>
                <p style='font-size: 12px; color: #888;'>This is an automated email. Please do not reply.</p>
                <p style='font-size: 12px; color: #888;'>For further assistance, Please coordinate over call.</p>
            </div>
        </div>
    ";

    $this->email->from('taskmanagement@shubhampack.com', 'PMS Help Ticket Due More Than 2 Days');
    $this->email->to($to);
    $this->email->subject($subject);
    $this->email->message($email_body);

    if (!$this->email->send()) {
        log_message('error', "Email to $to failed: " . $this->email->print_debugger());
    }
}


}
