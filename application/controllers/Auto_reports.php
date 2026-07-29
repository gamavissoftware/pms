<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auto_reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Df_Report_model');
        $this->load->library('email'); 
    }

    public function send_weekly_team_report() {
        // --- 1. Define Time Range (Last Monday to Today) ---
        $end_date = date('Y-m-d'); // Today
        $start_date = date('Y-m-d', strtotime('last Monday', strtotime($end_date)));

        // --- 2. Setup Filters ---
        $filters = [
            'start_date' => $start_date,
            'end_date'   => $end_date,
            'department_id' => null // Null means ALL departments
        ];

        // --- 3. Fetch Data ---
        $export_data = $this->Df_Report_model->get_all_tasks_exportforemail($filters);

        // --- 4. Generate CSV Content (In Memory) ---
        $csv_content = $this->_generate_csv_string($export_data);
        $filename = 'Weekly_Task_Report_' . $start_date . '_to_' . $end_date . '.csv';

        // --- 5. EMAIL CONFIGURATION ---
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'taskmanagement@shubhampack.com',
            'smtp_pass' => 'ficihlqnfcdrrqkb', // App Password
            'mailtype'  => 'html', 
            'charset'   => 'utf-8',
            'newline'   => "\r\n"
        );
        
        $this->email->initialize($config);
        $this->email->set_mailtype("html");

        // --- 6. Compose Email ---
        // IMPORTANT: Change the email below to who should receive the report
        $to_email = 'ea.ed@shubhampack.com'; 
        //$to_email = 'mangleshup@gmail.com'; 

        $subject = 'Weekly Department Performance Report (' . $start_date . ' to ' . $end_date . ')';
        
        $message  = "Hello, <br><br>";
        $message .= "Please find attached the weekly task performance report for all departments.<br>";
        $message .= "<strong>Period:</strong> " . $start_date . " to " . $end_date . "<br><br>";
        $message .= "Regards,<br>Task Management System";

        $this->email->from('taskmanagement@shubhampack.com', 'Task Management System');
        $this->email->to($to_email);
        $this->email->cc('deepesh@shubhampack.com');
        $this->email->bcc('mangleshup@gmail.com');
        $this->email->subject($subject);
        $this->email->message($message);
        
        // Attach the CSV string
        $this->email->attach($csv_content, 'attachment', $filename, 'text/csv');

        // --- 7. Send ---
        if ($this->email->send()) {
            echo "Email sent successfully to $to_email at " . date('Y-m-d H:i:s');
        } else {
            echo "Failed to send email. <br>";
            echo $this->email->print_debugger();
        }
    }

    // --- Helper function to generate CSV string ---
    private function _generate_csv_string($export_data) {
        $fp = fopen('php://memory', 'w+');

        // --- 1. Summary Calculation ---
        $total_tasks = count($export_data);
        $total_completed = 0;
        $total_pending = 0;

        if (!empty($export_data)) {
            foreach ($export_data as $row) {
                if (isset($row['task_status']) && $row['task_status'] == 1) {
                    $total_completed++;
                } else {
                    $total_pending++;
                }
            }
        }

        // --- 2. Write Summary Section ---
        fputcsv($fp, ['--- WEEKLY REPORT SUMMARY ---']);
        fputcsv($fp, ['Total Tasks', 'Completed', 'Pending']);
        fputcsv($fp, [$total_tasks, $total_completed, $total_pending]);
        fputcsv($fp, []); 
        
        // --- 3. Write Data with Department Grouping ---
        if (!empty($export_data)) {
            $today_ts = strtotime(date('Y-m-d'));
            $current_dept = ""; 

            foreach ($export_data as $row) {
                
                // --- A. DATE FIX ---
                $raw_completed = $row['completed_date'] ?? '';
                $display_completed_date = '';
                $status_val = $row['task_status'] ?? 0;

                if ($status_val == 1 && !empty($raw_completed)) {
                    $ts = strtotime($raw_completed);
                    if ($ts && date('Y', $ts) > 1970) {
                        $display_completed_date = date('Y-m-d', $ts);
                    }
                }

                // --- B. NAME FORMATTING ---
                $assigned_name = $row['assigned_to'] ?? '';
                $assigned_name = ucwords(strtolower($assigned_name));

                // --- C. DEPARTMENT GROUPING ---
                $row_dept = strtoupper($row['department_name'] ?? 'UNKNOWN DEPARTMENT');

                if ($row_dept !== $current_dept) {
                    if ($current_dept !== "") {
                        fputcsv($fp, []); 
                        fputcsv($fp, []); 
                    }

                    fputcsv($fp, ["*** DEPARTMENT: " . $row_dept . " ***"]);
                    
                    // Header Row: Added "DF No" at the start
                    fputcsv($fp, [
                        'DF No', 'Task Name', 'Assigned To', 
                        'Start Date', 'Due Date', 'Completed Date', 
                        'Status', 'Days Delayed', 'Remarks'
                    ]);

                    $current_dept = $row_dept; 
                }

                // --- D. Calculate Delays ---
                $due_ts = !empty($row['due_date']) ? strtotime($row['due_date']) : 0;
                $completed_ts = !empty($display_completed_date) ? strtotime($display_completed_date) : 0;
                $days_delayed = 0;
                $status_text = ($status_val == 1) ? 'Completed' : 'Pending';

                if ($due_ts > 0) {
                    if ($status_val == 1) {
                        if ($completed_ts > $due_ts) {
                            $diff = $completed_ts - $due_ts;
                            $days_delayed = floor($diff / (60 * 60 * 24));
                        }
                    } else {
                        if ($today_ts > $due_ts) {
                            $diff = $today_ts - $due_ts;
                            $days_delayed = floor($diff / (60 * 60 * 24));
                        }
                    }
                }

                // --- E. Write Data Row ---
                fputcsv($fp, [
                    $row['df_no'] ?? '', // Added DF No here
                    $row['task_name'] ?? '',
                    $assigned_name,
                    $row['start_date'] ?? '',
                    $row['due_date'] ?? '',
                    $display_completed_date,
                    $status_text,
                    $days_delayed,
                    $row['remarks'] ?? ''
                ]);
            }
        }

        rewind($fp);
        $csv_string = stream_get_contents($fp);
        fclose($fp);

        return $csv_string;
    }
}