<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Report_model', 'report');
        $this->load->helper('date');
    }

    public function index() {
        $data['dfs'] = $this->report->get_all_dfs();
        $this->load->view('gantt/df_list_view', $data);
    }

    public function gantt_chart($df_id = NULL) {
        if (empty($df_id) || !is_numeric($df_id)) {
            show_404();
        }

        $df_data = $this->report->get_df_details($df_id);

        $dates = [];
        $days_count = [];
        $report_data = [];
        $error_message = '';

        if (!$df_data) {
            $error_message = "DF data not found for ID: " . $df_id;
        } else {
            $date_range = $this->report->get_project_date_range($df_id);

            if (count($date_range) > 1 && $date_range[0] && $date_range[1]) {
                $stdate = $date_range[0];
                $etdate = $date_range[1];
                
                $dates = $this->report->get_mondays($stdate, $etdate);
                $days_count = $this->report->get_days_count_for_each_month_from_array($dates);

                $all_tasks_data = $this->report->get_all_tasks_for_report($df_id);

                foreach ($all_tasks_data as $main_task) {
                    $main_task_id = $main_task['id'];
                    $department_id = $main_task['department_id'];

                    $planned_dates = $this->report->get_aggregated_dates($main_task_id, $df_id, $department_id);
                    $actual_dates = $this->report->get_aggregated_actual_dates($main_task_id, $df_id);

                    // Consolidate the actual completion date for the main task
                    $main_task_completion_date = $this->report->get_main_task_completion_date($main_task['subtasks']);

                    if ($planned_dates && !empty($planned_dates['min_start_date'])) {
                        $completion_percent = $this->report->get_completion_percentage($main_task['subtasks']);
                        $delay_days = $this->report->get_delay_days($planned_dates['max_end_date'], $main_task_completion_date);

                        $report_data[] = [
                            'main_task_data' => $main_task,
                            'department_name' => $main_task['department_name'],
                            'min_start_date' => $planned_dates['min_start_date'],
                            'max_end_date' => $planned_dates['max_end_date'],
                            'main_task_completion_date' => $main_task_completion_date, // Added for the view
                            'completion_percent' => number_format($completion_percent, 2),
                            'delay_days' => $delay_days,
                            'planned_weeks' => $this->report->get_week_numbers_between_dates($planned_dates['min_start_date'], $planned_dates['max_end_date']),
                            'actual_weeks' => $this->report->get_week_numbers_between_dates($actual_dates['min_start'] ?? null, $main_task_completion_date),
                            'sub_tasks' => $main_task['subtasks']
                        ];
                    }
                }
            } else {
                $error_message = "Date range not found for this DF. Please ensure tasks exist in `task_department_wise_scheduling` table.";
            }
        }

        $data = [
            'df_details' => $df_data,
            'dates' => $dates,
            'days_count' => $days_count,
            'report_data' => $report_data,
            'df_id' => $df_id,
            'error' => $error_message,
        ];

        $this->load->view('gantt/gantt_chart_view', $data);
    }
}