<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Df_reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Df_Report_model');
        // It's good practice to load the security helper for CSRF protection in AJAX
        $this->load->helper('security');
    }

    public function weekly_delay_report() {
    $data = [];
    $filters = [];

    // --- UPDATED: Financial Year Filter Logic ---
    $start_date_str = $this->input->post('start_date');
    $end_date_str = $this->input->post('end_date');
    $selected_fy = $this->input->post('financial_year');
    $filters['department_id'] = $this->input->post('department_id');
    $filters['df_id'] = $this->input->post('df_id');

    // --- NEW: Default to Current FY Logic ---
    // Check if any filters are set.
    $is_filtered = $start_date_str || $end_date_str || $selected_fy || $filters['department_id'] || $filters['df_id'];

    if (!$is_filtered) {
        // NO filters set, so default to CURRENT financial year
        $current_month = (int)date('m');
        $current_year = (int)date('Y');
        
        if ($current_month >= 4) { // April or later
            $fy_start_year = $current_year;
            $fy_end_year = $current_year + 1;
        } else { // Jan, Feb, March
            $fy_start_year = $current_year - 1;
            $fy_end_year = $current_year;
        }
        $selected_fy = $fy_start_year . '-' . $fy_end_year;
    }
    // --- END: Default to Current FY Logic ---


    if ($selected_fy) {
        // If FY is chosen (or defaulted), it overrides the date pickers
        $years = explode('-', $selected_fy);
        $fy_start = $years[0] . '-04-01'; // April 1st
        $fy_end = $years[1] . '-03-31';   // March 31st

        $filters['start_date'] = $fy_start;
        $filters['end_date'] = $fy_end;
        $data['page_title'] = "Delayed Tasks for FY " . htmlspecialchars($selected_fy);
        
        // Overwrite date strings to show the FY range in the filter boxes
        $start_date_str = date('d-m-Y', strtotime($fy_start));
        $end_date_str = date('d-m-Y', strtotime($fy_end));

    } else if ($start_date_str && $end_date_str) {
        // Standard date range
        $start_date_obj = DateTime::createFromFormat('d-m-Y', $start_date_str);
        $end_date_obj = DateTime::createFromFormat('d-m-Y', $end_date_str);
        $filters['start_date'] = $start_date_obj ? $start_date_obj->format('Y-m-d') : null;
        $filters['end_date'] = $end_date_obj ? $end_date_obj->format('Y-m-d') : null;
        $data['page_title'] = "Delayed Tasks from " . htmlspecialchars($start_date_str) . " to " . htmlspecialchars($end_date_str);
    
    } else if (!empty($filters['department_id']) || !empty($filters['df_id'])) {
        // No dates, but specific filters
        $data['page_title'] = "All Historical Delays (Pending & Closed) for Selected Filters";
    } else {
        // This will now only be hit if a user *clears* all filters and hits submit
        // It's still good to keep as a fallback.
        $filters['show_all_pending'] = true;
        $data['page_title'] = "All Pending Delayed Tasks (as of " . date('d-m-Y') . ")";
    }
    // --- End Filter Logic ---

    // --- NEW: Get FY Summary Stats ---
    $fy_summary_stats = $this->Df_Report_model->get_df_summary_for_financial_year($filters);
    $fy_summary_stats['carry_forward'] = $fy_summary_stats['total_released'] - $fy_summary_stats['total_dispatched'];
    $data['fy_summary'] = $fy_summary_stats;
    // --- END NEW BLOCK ---

    // 1. Get the main report data (flat array)
    $data['report_data'] = $this->Df_Report_model->get_delay_data($filters);
    
    // 2. Get associated ticket information
    $data['tickets'] = $this->Df_Report_model->get_open_tickets_for_tasks($filters);

    // --- Calculate Statistics, Loss, AND Prepare Graph Data ---
    define('OPPORTUNITY_COST_RATE', 0.08);
    $department_stats = [];
    $dept_name_map = []; 
    $grouped_data = []; 

    if (!empty($data['report_data'])) {
        foreach ($data['report_data'] as $index => $row) {
            $dept_id = $row['department_id'];
            
            if (!isset($dept_name_map[$dept_id])) {
                $dept_name_map[$dept_id] = $row['department'];
            }
            
            if (!isset($department_stats[$dept_id])) {
                $department_stats[$dept_id] = [
                    'total_delay' => 0, 'task_count' => 0, 
                    'average_delay' => 0, 'total_loss' => 0  
                ];
            }
            
            $delay = (int)$row['delay_days'];
            $order_value = (float)$row['order_value'];
            $daily_rate = (OPPORTUNITY_COST_RATE / 365);
            $estimated_loss = ($order_value * $daily_rate) * $delay;
            
            $data['report_data'][$index]['estimated_loss'] = $estimated_loss;
            $row['estimated_loss'] = $estimated_loss;
            
            if ($delay > 0) {
                $department_stats[$dept_id]['total_delay'] += $delay;
                $department_stats[$dept_id]['task_count']++;
                $department_stats[$dept_id]['total_loss'] += $estimated_loss; 
            }

            $grouped_data[$dept_id]['tasks'][] = $row;
            $grouped_data[$dept_id]['department_name'] = $row['department'];
        }
        
        foreach ($department_stats as $dept_id => $stats) {
            if ($stats['task_count'] > 0) {
                $department_stats[$dept_id]['average_delay'] = round($stats['total_delay'] / $stats['task_count'], 1);
            }
        }
    }
    $data['department_stats'] = $department_stats;

    // --- Sort tasks within each group by delay_days (descending) ---
    foreach ($grouped_data as $dept_id => $group) {
        usort($group['tasks'], function($a, $b) {
            return $b['delay_days'] <=> $a['delay_days'];
        });
        $grouped_data[$dept_id]['tasks'] = $group['tasks'];
    }
    $data['grouped_report_data'] = $grouped_data;
    
    // --- Prepare Final Graph Data Array ---
    $graph_labels = [];
    $graph_dept_ids = []; 
    $graph_avg_delays = [];
    $graph_total_delays = [];
    $graph_total_loss = []; 
    $grand_total_loss = 0; 

    uasort($department_stats, function($a, $b) {
        return $b['total_loss'] <=> $a['total_loss'];
    });

    foreach ($department_stats as $dept_id => $stats) {
        if ($stats['task_count'] > 0) {
            $graph_labels[] = $dept_name_map[$dept_id] ?? 'Unknown Dept';
            $graph_dept_ids[] = $dept_id;
            $graph_avg_delays[] = $stats['average_delay'];
            $graph_total_delays[] = $stats['task_count'];
            $graph_total_loss[] = round($stats['total_loss'], 0); 
            $grand_total_loss += $stats['total_loss']; 
        }
    }
    
    $data['graph_data'] = [
        'labels' => $graph_labels,
        'dept_ids' => $graph_dept_ids,
        'avg_delay_data' => $graph_avg_delays,
        'total_delay_data' => $graph_total_delays,
        'total_loss_data' => $graph_total_loss 
    ];
    $data['grand_total_loss'] = $grand_total_loss; 
    // --- End Graph Prep ---

    // 3. Get data for filters
    $data['departments'] = $this->Df_Report_model->get_all_departments();
    $data['all_dfs'] = $this->Df_Report_model->get_all_dfs(); 

    // 4. Pass selected filter values back to the view
    $data['selected_start_date'] = $start_date_str;
    $data['selected_end_date'] = $end_date_str;
    $data['selected_department_id'] = $filters['department_id'];
    $data['selected_df_id'] = $filters['df_id'];
    $data['selected_fy'] = $selected_fy; 
    
    $this->load->view('reports/weekly_delay_report_view', $data);
}

    public function ajax_get_dfs_for_week() {
        if (!$this->input->is_ajax_request()) {
           exit('No direct script access allowed');
        }

        $selected_date_str = $this->input->post('selected_date');
        if (!$selected_date_str) {
            echo json_encode([]);
            return;
        }

        $date_obj = DateTime::createFromFormat('d-m-Y', $selected_date_str);
        if (!$date_obj) {
            echo json_encode([]);
            return;
        }
        $selected_timestamp = $date_obj->getTimestamp();

        $day_of_week = date('N', $selected_timestamp);
        $week_start = date('Y-m-d', strtotime('-'.($day_of_week - 1).' days', $selected_timestamp));
        $week_end = date('Y-m-d', strtotime('+'.(7 - $day_of_week).' days', $selected_timestamp));

        $dfs = $this->Df_Report_model->get_df_numbers_for_week($week_start, $week_end);

        header('Content-Type: application/json');
        echo json_encode($dfs);
    }


   public function user_appraisal_report() {
        $data = [];
        $filters = [];

        // --- 1. Get Filters (Unchanged) ---
        $start_date_str = $this->input->post('start_date');
        $end_date_str = $this->input->post('end_date');
        $filters['department_id'] = $this->input->post('department_id');

        if ($start_date_str && $end_date_str) {
            $start_date_obj = DateTime::createFromFormat('d-m-Y', $start_date_str);
            $end_date_obj = DateTime::createFromFormat('d-m-Y', $end_date_str);
            $filters['start_date'] = $start_date_obj ? $start_date_obj->format('Y-m-d') : null;
            $filters['end_date'] = $end_date_obj ? $end_date_obj->format('Y-m-d') : null;
            $data['page_title'] = "User Performance Report (" . htmlspecialchars($start_date_str) . " to " . htmlspecialchars($end_date_str) . ")";
        } else {
            $data['page_title'] = "User Performance Report (All-Time)";
        }
        
        // --- 2. Get Raw Data (Unchanged) ---
        $raw_data = $this->Df_Report_model->get_user_performance_data($filters);
        
        // --- 3. UPDATED: Process Data ---
        $processed_data = [];
        if (!empty($raw_data)) {
            
            $temp_dept_data = [];
            foreach ($raw_data as $row) {
                $dept_id = $row['department_id'];
                
                // --- NEW: Calculate Total Assigned ---
                // (Note: This logic depends on your date filter. If you filter by date, 
                // "Total Assigned" means "Total Completed" + "Total Pending")
                $total_assigned = $row['total_completed'] + $row['tasks_pending_delayed'] + $row['tasks_pending_on_time'];
                $total_done = $row['total_completed'];
                $work_done_delayed = $row['tasks_delayed_closed'];
                
                // --- NEW: Metric 1: % of Work NOT Done (i.e., Pending) ---
                if ($total_assigned > 0) {
                    $row['percent_not_done'] = round((($total_assigned - $total_done) / $total_assigned) * 100, 1);
                } else {
                    $row['percent_not_done'] = 0;
                }
                
                // --- NEW: Metric 2: % of (Completed) Work that was Delayed ---
                if ($total_done > 0) {
                    $row['percent_work_delayed'] = round(($work_done_delayed / $total_done) * 100, 1);
                } else {
                    $row['percent_work_delayed'] = 0;
                }
                
                // (Existing Calculations)
                if ($row['total_completed'] > 0) {
                    $row['on_time_percent'] = round(($row['tasks_on_time'] / $row['total_completed']) * 100, 1);
                } else {
                    $row['on_time_percent'] = 0; 
                }
                if ($row['tasks_delayed_closed'] > 0) {
                    $row['avg_delay'] = round($row['total_delay_days'] / $row['tasks_delayed_closed'], 1);
                } else {
                    $row['avg_delay'] = 0;
                }
                
                $temp_dept_data[$dept_id]['users'][] = $row;
                $temp_dept_data[$dept_id]['department_name'] = $row['department'];
            }

            // Find star performer and rank (Unchanged)
            foreach ($temp_dept_data as $dept_id => $dept) {
                usort($dept['users'], function($a, $b) {
                    if ($a['on_time_percent'] == $b['on_time_percent']) {
                        return $b['total_completed'] <=> $a['total_completed']; 
                    }
                    return $b['on_time_percent'] <=> $a['on_time_percent']; 
                });
                $processed_data[$dept_id]['star_performer'] = array_shift($dept['users']);
                $processed_data[$dept_id]['other_performers'] = $dept['users'];
                $processed_data[$dept_id]['department_name'] = $dept['department_name'];
            }
        }
        
        $data['performance_data'] = $processed_data;
        
        // --- 4. Get Data for Filters (Unchanged) ---
        $data['departments'] = $this->Df_Report_model->get_all_departments();
        $data['selected_start_date'] = $start_date_str;
        $data['selected_end_date'] = $end_date_str;
        $data['selected_department_id'] = $filters['department_id'];

        $this->load->view('reports/user_performance_report_view', $data);
    }

    public function closed_df_report() {
        $filters = $this->build_closed_df_report_filters();
        $data = $this->prepare_closed_df_report_payload($filters);
        $this->load->view('reports/closed_df_report_view', $data);
    }

    public function export_closed_df_report_excel() {
        $filters = $this->build_closed_df_report_filters();
        $data = $this->prepare_closed_df_report_payload($filters);

        $this->load->library('excel');
        $object = new PHPExcel();
        $object->getProperties()
            ->setCreator('PMS')
            ->setTitle('Completed DF Analytics Report')
            ->setSubject('Completed DF Analytics Report');

        $header_style = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 13
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '163A70')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $table_header_style = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '2F6FED')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $body_style = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $index_sheet = $object->setActiveSheetIndex(0);
        $index_sheet->setTitle('DF Index');
        $index_sheet->mergeCells('A1:M1');
        $index_sheet->setCellValue('A1', 'COMPLETED DF ANALYTICS REPORT');
        $index_sheet->getStyle('A1:M1')->applyFromArray($header_style);
        $index_sheet->getRowDimension(1)->setRowHeight(24);
        $index_sheet->setCellValue('A3', 'Filter');
        $index_sheet->setCellValue('B3', $data['selected_fy'] !== '' ? ('FY ' . $data['selected_fy']) : 'All Years');
        $index_sheet->setCellValue('D3', 'Total Completed DF');
        $index_sheet->setCellValue('E3', (int) $data['summary_cards']['total_completed_df']);
        $index_sheet->setCellValue('G3', 'Delayed DF');
        $index_sheet->setCellValue('H3', (int) $data['summary_cards']['delayed_df_count']);
        $index_sheet->setCellValue('J3', 'Total Delay Loss');
        $index_sheet->setCellValue('K3', (float) $data['summary_cards']['total_estimated_loss']);
        $index_sheet->getStyle('A3:K3')->applyFromArray($body_style);

        $index_headers = array(
            'DF No',
            'Marketing',
            'Release Date',
            'Planned Completion',
            'Actual Completion',
            'Planned Days',
            'Actual Days',
            'Difference Days',
            'Delay Days',
            'Delay Dept',
            'Dept Delay Days',
            'Loss of Delay',
            'Status'
        );
        $index_columns = range('A', 'M');
        foreach ($index_headers as $index => $header) {
            $index_sheet->setCellValue($index_columns[$index] . '6', $header);
        }
        $index_sheet->getStyle('A6:M6')->applyFromArray($table_header_style);

        $index_row_number = 7;
        foreach ($data['report_rows'] as $row) {
            $index_sheet->setCellValueExplicit('A' . $index_row_number, $row['df_no']);
            $index_sheet->setCellValue('B' . $index_row_number, $row['marketing_person']);
            $index_sheet->setCellValue('C' . $index_row_number, $row['df_release_date']);
            $index_sheet->setCellValue('D' . $index_row_number, $row['planned_closure_date']);
            $index_sheet->setCellValue('E' . $index_row_number, $row['actual_completion_date']);
            $index_sheet->setCellValue('F' . $index_row_number, (float) $row['planned_days']);
            $index_sheet->setCellValue('G' . $index_row_number, (float) $row['actual_days']);
            $index_sheet->setCellValue('H' . $index_row_number, (float) $row['difference_days']);
            $index_sheet->setCellValue('I' . $index_row_number, (float) $row['df_delay_days']);
            $index_sheet->setCellValue('J' . $index_row_number, $row['delay_department']);
            $index_sheet->setCellValue('K' . $index_row_number, (float) $row['delay_department_days']);
            $index_sheet->setCellValue('L' . $index_row_number, (float) $row['estimated_loss']);
            $index_sheet->setCellValue('M' . $index_row_number, $row['status_label']);
            $index_row_number++;
        }
        if ($index_row_number > 7) {
            $index_sheet->getStyle('A7:M' . ($index_row_number - 1))->applyFromArray($body_style);
        }
        foreach (array('A' => 18, 'B' => 22, 'C' => 15, 'D' => 18, 'E' => 18, 'F' => 12, 'G' => 12, 'H' => 14, 'I' => 12, 'J' => 18, 'K' => 16, 'L' => 16, 'M' => 14) as $column => $width) {
            $index_sheet->getColumnDimension($column)->setWidth($width);
        }
        $index_sheet->freezePane('A7');

        if (!empty($data['overall_department_bar_chart']['labels'])) {
            $overall_chart_row = 4;
            $index_sheet->setCellValue('X3', 'Department');
            $index_sheet->setCellValue('Y3', 'Delay Days');
            foreach ($data['overall_department_bar_chart']['labels'] as $chart_index => $label) {
                $index_sheet->setCellValue('X' . $overall_chart_row, $label);
                $index_sheet->setCellValue('Y' . $overall_chart_row, (float) $data['overall_department_bar_chart']['values'][$chart_index]);
                $overall_chart_row++;
            }

            $overall_chart_last_row = $overall_chart_row - 1;
            $this->add_excel_pie_chart(
                $index_sheet,
                'Department Delay Share',
                "'DF Index'!\$Y\$3",
                "'DF Index'!\$X\$4:\$X\$" . $overall_chart_last_row,
                "'DF Index'!\$Y\$4:\$Y\$" . $overall_chart_last_row,
                'O3',
                'U18'
            );

            $this->add_excel_bar_chart(
                $index_sheet,
                'Top Delayed Departments',
                "'DF Index'!\$X\$4:\$X\$" . $overall_chart_last_row,
                array(
                    array(
                        'label' => "'DF Index'!\$Y\$3",
                        'values' => "'DF Index'!\$Y\$4:\$Y\$" . $overall_chart_last_row
                    )
                ),
                'O20',
                'W35',
                false
            );

            $index_sheet->getColumnDimension('X')->setVisible(false);
            $index_sheet->getColumnDimension('Y')->setVisible(false);
        }

        $used_sheet_titles = array('df index' => true);
        foreach ($data['df_detail_tabs'] as $df_tab) {
            $sheet = $object->createSheet();
            $sheet_title = $this->build_closed_df_excel_sheet_title($df_tab['df_no'], $used_sheet_titles);
            $sheet->setTitle($sheet_title);

            $sheet->mergeCells('A1:H1');
            $sheet->setCellValue('A1', $df_tab['df_no'] . ' - DF DETAIL');
            $sheet->getStyle('A1:H1')->applyFromArray($header_style);
            $sheet->getRowDimension(1)->setRowHeight(24);

            $sheet->setCellValue('A3', 'DF Release Date');
            $sheet->setCellValue('B3', $df_tab['df_release_date']);
            $sheet->setCellValue('C3', 'Planned Completion');
            $sheet->setCellValue('D3', $df_tab['planned_closure_date']);
            $sheet->setCellValue('E3', 'Actual Completion');
            $sheet->setCellValue('F3', $df_tab['actual_completion_date']);
            $sheet->setCellValue('G3', 'Difference Days');
            $sheet->setCellValue('H3', (float) $df_tab['difference_days']);
            $sheet->getStyle('A3:H3')->applyFromArray($body_style);

            $sheet->setCellValue('A4', 'Planned Days');
            $sheet->setCellValue('B4', (float) $df_tab['planned_days']);
            $sheet->setCellValue('C4', 'Actual Days');
            $sheet->setCellValue('D4', (float) $df_tab['actual_days']);
            $sheet->setCellValue('E4', 'Delay Days');
            $sheet->setCellValue('F4', (float) $df_tab['delay_days']);
            $sheet->setCellValue('G4', 'Status');
            $sheet->setCellValue('H4', $df_tab['status_label']);
            $sheet->getStyle('A4:H4')->applyFromArray($body_style);

            $detail_headers = array(
                'Department',
                'Planned Completion',
                'Actual Completion',
                'Planned Days',
                'Actual Days',
                'Difference Days',
                'Delay Days',
                'Delayed Tasks'
            );
            $detail_columns = range('A', 'H');
            foreach ($detail_headers as $index => $header) {
                $sheet->setCellValue($detail_columns[$index] . '7', $header);
            }
            $sheet->getStyle('A7:H7')->applyFromArray($table_header_style);

            $detail_row_number = 8;
            foreach ($df_tab['department_rows'] as $department_row) {
                $sheet->setCellValue('A' . $detail_row_number, $department_row['department']);
                $sheet->setCellValue('B' . $detail_row_number, $department_row['planned_completion_date']);
                $sheet->setCellValue('C' . $detail_row_number, $department_row['actual_completion_date']);
                $sheet->setCellValue('D' . $detail_row_number, (float) $department_row['planned_days']);
                $sheet->setCellValue('E' . $detail_row_number, (float) $department_row['actual_days']);
                $sheet->setCellValue('F' . $detail_row_number, (float) $department_row['difference_days']);
                $sheet->setCellValue('G' . $detail_row_number, (float) $department_row['delay_days']);
                $sheet->setCellValue('H' . $detail_row_number, (int) $department_row['delayed_task_count']);
                $detail_row_number++;
            }

            if ($detail_row_number > 8) {
                $sheet->getStyle('A8:H' . ($detail_row_number - 1))->applyFromArray($body_style);
            } else {
                $sheet->mergeCells('A8:H8');
                $sheet->setCellValue('A8', 'No department progress data available for this DF.');
                $sheet->getStyle('A8:H8')->applyFromArray($body_style);
            }

            if (!empty($df_tab['department_rows'])) {
                $detail_last_row = 7 + count($df_tab['department_rows']);
                $pie_value_column = $df_tab['pie_chart']['mode'] === 'delay' ? 'G' : 'E';
                $pie_title = $df_tab['pie_chart']['mode'] === 'delay'
                    ? 'Department Delay Share'
                    : 'Department Actual Days Share';

                $this->add_excel_pie_chart(
                    $sheet,
                    $pie_title,
                    "'" . $sheet_title . "'!\$" . $pie_value_column . "\$7",
                    "'" . $sheet_title . "'!\$A\$8:\$A\$" . $detail_last_row,
                    "'" . $sheet_title . "'!\$" . $pie_value_column . "\$8:\$" . $pie_value_column . "\$" . $detail_last_row,
                    'J3',
                    'P18'
                );

                $this->add_excel_bar_chart(
                    $sheet,
                    'Department Planned vs Actual Days',
                    "'" . $sheet_title . "'!\$A\$8:\$A\$" . $detail_last_row,
                    array(
                        array(
                            'label' => "'" . $sheet_title . "'!\$D\$7",
                            'values' => "'" . $sheet_title . "'!\$D\$8:\$D\$" . $detail_last_row
                        ),
                        array(
                            'label' => "'" . $sheet_title . "'!\$E\$7",
                            'values' => "'" . $sheet_title . "'!\$E\$8:\$E\$" . $detail_last_row
                        )
                    ),
                    'J20',
                    'R38',
                    false
                );
            }

            foreach (array('A' => 22, 'B' => 18, 'C' => 18, 'D' => 14, 'E' => 14, 'F' => 16, 'G' => 12, 'H' => 14) as $column => $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
            }
            $sheet->freezePane('A8');
        }

        $object->setActiveSheetIndex(0);
        $file_name = 'completed_df_analytics_' . date('Ymd_His') . '.xlsx';
        $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $writer->setIncludeCharts(true);

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function export_closed_df_report_overall_excel() {
        $filters = $this->build_closed_df_report_filters();
        $data = $this->prepare_closed_df_report_payload($filters);

        $this->load->library('excel');
        $object = new PHPExcel();
        $object->getProperties()
            ->setCreator('PMS')
            ->setTitle('Completed DF Overall Report')
            ->setSubject('Completed DF Overall Report');

        $header_style = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 13
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '163A70')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $table_header_style = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF')
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '2F6FED')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $body_style = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $metric_label_style = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size' => 11
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '2F6FED')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $metric_value_style = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '132035'),
                'size' => 15
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F5F9FD')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $summary_sheet = $object->setActiveSheetIndex(0);
        $summary_sheet->setTitle('Overall Summary');
        $summary_sheet->setShowGridlines(false);
        $summary_sheet->mergeCells('A1:N1');
        $summary_sheet->setCellValue('A1', 'COMPLETED DF OVERALL REPORT');
        $summary_sheet->getStyle('A1:N1')->applyFromArray($header_style);
        $summary_sheet->getRowDimension(1)->setRowHeight(28);

        $summary_cards = array(
            array('label_range' => 'A3:C3', 'value_range' => 'A4:C5', 'label' => 'Filter', 'value' => $data['selected_fy'] !== '' ? ('FY ' . $data['selected_fy']) : 'All Years'),
            array('label_range' => 'D3:F3', 'value_range' => 'D4:F5', 'label' => 'Total Completed DF', 'value' => (int) $data['summary_cards']['total_completed_df']),
            array('label_range' => 'G3:I3', 'value_range' => 'G4:I5', 'label' => 'Delayed DF', 'value' => (int) $data['summary_cards']['delayed_df_count']),
            array('label_range' => 'J3:N3', 'value_range' => 'J4:N5', 'label' => 'Total Delay Loss', 'value' => round((float) $data['summary_cards']['total_estimated_loss'], 0)),
            array('label_range' => 'A6:C6', 'value_range' => 'A7:C8', 'label' => 'Planned Days', 'value' => (int) $data['summary_cards']['total_planned_days']),
            array('label_range' => 'D6:F6', 'value_range' => 'D7:F8', 'label' => 'Actual Days', 'value' => (int) $data['summary_cards']['total_actual_days']),
            array('label_range' => 'G6:I6', 'value_range' => 'G7:I8', 'label' => 'Total Delay Days', 'value' => (int) $data['summary_cards']['total_delay_days']),
            array('label_range' => 'J6:N6', 'value_range' => 'J7:N8', 'label' => 'Avg DF Delay', 'value' => (float) $data['summary_cards']['avg_df_delay_days'] . ' d')
        );

        foreach ($summary_cards as $summary_card) {
            $summary_sheet->mergeCells($summary_card['label_range']);
            $summary_sheet->mergeCells($summary_card['value_range']);
            $label_cell = explode(':', $summary_card['label_range']);
            $value_cell = explode(':', $summary_card['value_range']);
            $summary_sheet->setCellValue($label_cell[0], $summary_card['label']);
            $summary_sheet->setCellValue($value_cell[0], $summary_card['value']);
            $summary_sheet->getStyle($summary_card['label_range'])->applyFromArray($metric_label_style);
            $summary_sheet->getStyle($summary_card['value_range'])->applyFromArray($metric_value_style);
        }

        if (!empty($data['overall_department_bar_chart']['labels'])) {
            $chart_data_row = 4;
            $summary_sheet->setCellValue('X3', 'Department');
            $summary_sheet->setCellValue('Y3', 'Delay Days');
            foreach ($data['overall_department_bar_chart']['labels'] as $chart_index => $label) {
                $summary_sheet->setCellValue('X' . $chart_data_row, $label);
                $summary_sheet->setCellValue('Y' . $chart_data_row, (float) $data['overall_department_bar_chart']['values'][$chart_index]);
                $chart_data_row++;
            }

            $chart_last_row = $chart_data_row - 1;
            $this->add_excel_pie_chart(
                $summary_sheet,
                'Department Delay Share',
                "'Overall Summary'!\$Y\$3",
                "'Overall Summary'!\$X\$4:\$X\$" . $chart_last_row,
                "'Overall Summary'!\$Y\$4:\$Y\$" . $chart_last_row,
                'A11',
                'G26'
            );

            $this->add_excel_bar_chart(
                $summary_sheet,
                'Top Delayed Departments',
                "'Overall Summary'!\$X\$4:\$X\$" . $chart_last_row,
                array(
                    array(
                        'label' => "'Overall Summary'!\$Y\$3",
                        'values' => "'Overall Summary'!\$Y\$4:\$Y\$" . $chart_last_row
                    )
                ),
                'H11',
                'N26',
                false
            );

            $summary_sheet->getColumnDimension('X')->setVisible(false);
            $summary_sheet->getColumnDimension('Y')->setVisible(false);
        } else {
            $summary_sheet->mergeCells('A11:N14');
            $summary_sheet->setCellValue('A11', 'No delayed department data found for the selected filter.');
            $summary_sheet->getStyle('A11:N14')->applyFromArray($body_style);
        }

        $summary_sheet->mergeCells('A29:N29');
        $summary_sheet->setCellValue('A29', 'Detailed overall records are available in the "Overall Data" sheet.');
        $summary_sheet->getStyle('A29:N29')->applyFromArray($body_style);

        foreach (array('A' => 12, 'B' => 12, 'C' => 12, 'D' => 12, 'E' => 12, 'F' => 12, 'G' => 12, 'H' => 12, 'I' => 12, 'J' => 12, 'K' => 12, 'L' => 12, 'M' => 12, 'N' => 12) as $column => $width) {
            $summary_sheet->getColumnDimension($column)->setWidth($width);
        }

        $data_sheet = $object->createSheet();
        $data_sheet->setTitle('Overall Data');
        $data_sheet->setShowGridlines(false);
        $data_sheet->mergeCells('A1:M1');
        $data_sheet->setCellValue('A1', 'COMPLETED DF OVERALL DATA');
        $data_sheet->getStyle('A1:M1')->applyFromArray($header_style);
        $data_sheet->getRowDimension(1)->setRowHeight(24);

        $data_sheet->setCellValue('A3', 'Filter');
        $data_sheet->setCellValue('B3', $data['selected_fy'] !== '' ? ('FY ' . $data['selected_fy']) : 'All Years');
        $data_sheet->setCellValue('D3', 'Total Completed DF');
        $data_sheet->setCellValue('E3', (int) $data['summary_cards']['total_completed_df']);
        $data_sheet->setCellValue('G3', 'Delayed DF');
        $data_sheet->setCellValue('H3', (int) $data['summary_cards']['delayed_df_count']);
        $data_sheet->setCellValue('J3', 'Total Delay Loss');
        $data_sheet->setCellValue('K3', (float) $data['summary_cards']['total_estimated_loss']);
        $data_sheet->getStyle('A3:K3')->applyFromArray($body_style);

        $overall_headers = array(
            'DF No',
            'Marketing',
            'Release Date',
            'Planned Completion',
            'Actual Completion',
            'Planned Days',
            'Actual Days',
            'Difference Days',
            'Delay Days',
            'Delay Dept',
            'Dept Delay Days',
            'Loss of Delay',
            'Status'
        );
        $overall_columns = range('A', 'M');
        foreach ($overall_headers as $index => $header) {
            $data_sheet->setCellValue($overall_columns[$index] . '5', $header);
        }
        $data_sheet->getStyle('A5:M5')->applyFromArray($table_header_style);
        $data_sheet->setAutoFilter('A5:M5');

        $table_row_number = 6;
        foreach ($data['report_rows'] as $row) {
            $data_sheet->setCellValueExplicit('A' . $table_row_number, $row['df_no']);
            $data_sheet->setCellValue('B' . $table_row_number, $row['marketing_person']);
            $data_sheet->setCellValue('C' . $table_row_number, $row['df_release_date']);
            $data_sheet->setCellValue('D' . $table_row_number, $row['planned_closure_date']);
            $data_sheet->setCellValue('E' . $table_row_number, $row['actual_completion_date']);
            $data_sheet->setCellValue('F' . $table_row_number, (float) $row['planned_days']);
            $data_sheet->setCellValue('G' . $table_row_number, (float) $row['actual_days']);
            $data_sheet->setCellValue('H' . $table_row_number, (float) $row['difference_days']);
            $data_sheet->setCellValue('I' . $table_row_number, (float) $row['df_delay_days']);
            $data_sheet->setCellValue('J' . $table_row_number, $row['delay_department']);
            $data_sheet->setCellValue('K' . $table_row_number, (float) $row['delay_department_days']);
            $data_sheet->setCellValue('L' . $table_row_number, (float) $row['estimated_loss']);
            $data_sheet->setCellValue('M' . $table_row_number, $row['status_label']);
            $table_row_number++;
        }

        if ($table_row_number > 6) {
            $data_sheet->getStyle('A6:M' . ($table_row_number - 1))->applyFromArray($body_style);
        } else {
            $data_sheet->mergeCells('A6:M6');
            $data_sheet->setCellValue('A6', 'No completed DF data found for the selected filter.');
            $data_sheet->getStyle('A6:M6')->applyFromArray($body_style);
        }

        foreach (array('A' => 18, 'B' => 22, 'C' => 15, 'D' => 18, 'E' => 18, 'F' => 12, 'G' => 12, 'H' => 14, 'I' => 12, 'J' => 18, 'K' => 16, 'L' => 16, 'M' => 14) as $column => $width) {
            $data_sheet->getColumnDimension($column)->setWidth($width);
        }
        $data_sheet->freezePane('A6');

        $file_name = 'completed_df_overall_' . date('Ymd_His') . '.xlsx';
        $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        $writer->setIncludeCharts(true);
        $object->setActiveSheetIndex(0);

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    private function build_closed_df_report_filters() {
        return array(
            'financial_year' => trim((string) $this->input->get_post('financial_year', true))
        );
    }

    private function prepare_closed_df_report_payload($filters) {
        $selected_fy = isset($filters['financial_year']) ? trim((string) $filters['financial_year']) : '';
        $daily_loss_rate = 0.08 / 365;
        $base_rows = $this->Df_Report_model->get_completed_df_overview_rows($filters);
        $df_ids = array();
        foreach ($base_rows as $row) {
            $df_ids[] = (int) $row['df_id'];
        }

        $delay_department_rows = $this->Df_Report_model->get_completed_df_delay_department_rows($df_ids);
        $department_progress_rows = $this->Df_Report_model->get_completed_df_department_progress_rows($df_ids);
        $top_department_by_df = array();
        $department_totals = array();
        $department_df_tracker = array();

        foreach ($delay_department_rows as $department_row) {
            $df_id = (int) $department_row['df_id'];
            $department_name = trim((string) $department_row['department']) !== '' ? trim((string) $department_row['department']) : 'Unmapped';
            $delay_days = (float) $department_row['total_delay_days'];
            $delayed_task_count = (int) $department_row['delayed_task_count'];

            if (!isset($top_department_by_df[$df_id]) || $delay_days > (float) $top_department_by_df[$df_id]['total_delay_days']) {
                $top_department_by_df[$df_id] = array(
                    'department' => $department_name,
                    'total_delay_days' => $delay_days,
                    'delayed_task_count' => $delayed_task_count
                );
            }

            if (!isset($department_totals[$department_name])) {
                $department_totals[$department_name] = array(
                    'department' => $department_name,
                    'delayed_df_count' => 0,
                    'delayed_task_count' => 0,
                    'total_delay_days' => 0,
                    'avg_delay_per_df' => 0
                );
                $department_df_tracker[$department_name] = array();
            }

            $department_totals[$department_name]['delayed_task_count'] += $delayed_task_count;
            $department_totals[$department_name]['total_delay_days'] += $delay_days;

            if (!isset($department_df_tracker[$department_name][$df_id])) {
                $department_df_tracker[$department_name][$df_id] = true;
                $department_totals[$department_name]['delayed_df_count']++;
            }
        }

        foreach ($department_totals as $department_name => $department_row) {
            $department_totals[$department_name]['avg_delay_per_df'] = $department_row['delayed_df_count'] > 0
                ? round($department_row['total_delay_days'] / $department_row['delayed_df_count'], 1)
                : 0;
        }

        $report_rows = array();
        $total_completed_df = 0;
        $delayed_df_count = 0;
        $on_time_df_count = 0;
        $delay_days_total = 0;
        $planned_days_total = 0;
        $actual_days_total = 0;
        $estimated_loss_total = 0;

        foreach ($base_rows as $row) {
            $df_id = (int) $row['df_id'];
            $planned_closure = $this->normalize_issue_date($row['dispatch_planned_closure']);
            if ($planned_closure === null) {
                $planned_closure = $this->normalize_issue_date($row['fallback_planned_closure']);
            }

            $project_start = $this->normalize_issue_date($row['project_start_date']);
            $actual_completion = $this->normalize_issue_date($row['actual_completion_date']);
            $planned_days = $this->calculate_closed_df_day_span($project_start, $planned_closure);
            $actual_days = $this->calculate_closed_df_day_span($project_start, $actual_completion);
            $df_delay_days = 0;
            $order_value = isset($row['order_value']) ? (float) $row['order_value'] : 0;

            if ($planned_closure !== null && $actual_completion !== null && strtotime($actual_completion) > strtotime($planned_closure)) {
                $df_delay_days = $this->calculate_closed_df_day_span($planned_closure, $actual_completion);
            }

            $estimated_loss = ($order_value > 0 && $df_delay_days > 0)
                ? round(($order_value * $daily_loss_rate) * $df_delay_days, 0)
                : 0;

            $delay_department = $df_delay_days > 0 && isset($top_department_by_df[$df_id])
                ? $top_department_by_df[$df_id]['department']
                : 'On Time';

            $report_row = array(
                'df_id' => $df_id,
                'df_no' => strtoupper((string) $row['df_no']),
                'df_description' => trim((string) $row['df_description']),
                'customer_name' => trim((string) $row['customer_name']),
                'po_no' => trim((string) $row['po_no']),
                'marketing_person' => trim((string) $row['marketing_person']),
                'df_release_date' => $this->normalize_issue_date($row['df_release_date']),
                'planned_closure_date' => $planned_closure,
                'actual_completion_date' => $actual_completion,
                'planned_days' => $planned_days,
                'actual_days' => $actual_days,
                'difference_days' => $actual_days - $planned_days,
                'df_delay_days' => $df_delay_days,
                'order_value' => $order_value,
                'estimated_loss' => $estimated_loss,
                'delay_department' => $delay_department,
                'delay_department_days' => isset($top_department_by_df[$df_id]) ? (float) $top_department_by_df[$df_id]['total_delay_days'] : 0,
                'status_label' => $df_delay_days > 0 ? 'Delayed' : 'On Time',
                'delayed_task_count' => (int) $row['delayed_task_count'],
                'total_tasks' => (int) $row['total_tasks']
            );

            $report_rows[] = $report_row;
            $total_completed_df++;
            $planned_days_total += (int) $planned_days;
            $actual_days_total += (int) $actual_days;

            if ($df_delay_days > 0) {
                $delayed_df_count++;
                $delay_days_total += $df_delay_days;
                $estimated_loss_total += $estimated_loss;
            } else {
                $on_time_df_count++;
            }
        }

        usort($report_rows, function ($left, $right) {
            if ((int) $left['df_delay_days'] === (int) $right['df_delay_days']) {
                return strcmp($right['actual_completion_date'], $left['actual_completion_date']);
            }
            return ((int) $left['df_delay_days'] > (int) $right['df_delay_days']) ? -1 : 1;
        });

        uasort($department_totals, function ($left, $right) {
            if ((float) $left['total_delay_days'] === (float) $right['total_delay_days']) {
                if ((int) $left['delayed_df_count'] === (int) $right['delayed_df_count']) {
                    return strcmp($left['department'], $right['department']);
                }
                return ((int) $left['delayed_df_count'] > (int) $right['delayed_df_count']) ? -1 : 1;
            }
            return ((float) $left['total_delay_days'] > (float) $right['total_delay_days']) ? -1 : 1;
        });

        $department_table_rows = array_values($department_totals);
        $chart_top_delay_rows = array_values(array_filter($report_rows, function ($row) {
            return (int) $row['df_delay_days'] > 0;
        }));
        if (empty($chart_top_delay_rows)) {
            $chart_top_delay_rows = $report_rows;
        }
        $chart_top_delay_rows = array_slice($chart_top_delay_rows, 0, 10);
        $df_detail_tabs = $this->build_closed_df_df_detail_tabs($report_rows, $department_progress_rows);
        $overall_department_chart_rows = array_slice($department_table_rows, 0, 10);
        $overall_department_pie_chart = array(
            'labels' => array_map(function ($row) {
                return $row['department'];
            }, array_slice($overall_department_chart_rows, 0, 8)),
            'values' => array_map(function ($row) {
                return (float) $row['total_delay_days'];
            }, array_slice($overall_department_chart_rows, 0, 8))
        );
        $overall_department_bar_chart = array(
            'labels' => array_map(function ($row) {
                return $row['department'];
            }, $overall_department_chart_rows),
            'values' => array_map(function ($row) {
                return (float) $row['total_delay_days'];
            }, $overall_department_chart_rows)
        );

        return array(
            'page_title' => 'Completed DF Analytics Report',
            'selected_fy' => $selected_fy,
            'financial_year_options' => $this->build_closed_df_financial_year_options(),
            'summary_cards' => array(
                'total_completed_df' => $total_completed_df,
                'delayed_df_count' => $delayed_df_count,
                'on_time_df_count' => $on_time_df_count,
                'avg_df_delay_days' => $delayed_df_count > 0 ? round($delay_days_total / $delayed_df_count, 1) : 0,
                'total_delay_days' => $delay_days_total,
                'total_estimated_loss' => $estimated_loss_total,
                'total_planned_days' => $planned_days_total,
                'total_actual_days' => $actual_days_total,
                'avg_planned_days' => $total_completed_df > 0 ? round($planned_days_total / $total_completed_df, 1) : 0,
                'avg_actual_days' => $total_completed_df > 0 ? round($actual_days_total / $total_completed_df, 1) : 0
            ),
            'report_rows' => $report_rows,
            'df_detail_tabs' => $df_detail_tabs,
            'department_table_rows' => $department_table_rows,
            'overall_department_chart_rows' => $overall_department_chart_rows,
            'overall_department_pie_chart' => $overall_department_pie_chart,
            'overall_department_bar_chart' => $overall_department_bar_chart,
            'chart_planned_vs_actual' => array(
                'labels' => array('Planned Days', 'Actual Days'),
                'values' => array($planned_days_total, $actual_days_total)
            ),
            'chart_top_delay_rows' => $chart_top_delay_rows,
            'chart_department_rows' => array_slice($department_table_rows, 0, 12)
        );
    }

    private function build_closed_df_df_detail_tabs($report_rows, $department_progress_rows) {
        $department_rows_by_df = array();

        foreach ($department_progress_rows as $department_row) {
            $df_id = (int) $department_row['df_id'];
            $department_start = $this->normalize_issue_date($department_row['department_start_date']);
            $department_planned = $this->normalize_issue_date($department_row['department_planned_date']);
            $department_actual = $this->normalize_issue_date($department_row['department_actual_date']);
            $planned_days = $this->calculate_closed_df_day_span($department_start, $department_planned);
            $actual_days = $this->calculate_closed_df_day_span($department_start, $department_actual);
            $difference_days = $actual_days - $planned_days;
            $delay_days = 0;

            if ($department_planned !== null && $department_actual !== null && strtotime($department_actual) > strtotime($department_planned)) {
                $delay_days = $this->calculate_closed_df_day_span($department_planned, $department_actual);
            }

            $department_rows_by_df[$df_id][] = array(
                'department' => trim((string) $department_row['department']) !== '' ? trim((string) $department_row['department']) : 'Unmapped',
                'planned_completion_date' => $department_planned,
                'actual_completion_date' => $department_actual,
                'planned_days' => $planned_days,
                'actual_days' => $actual_days,
                'difference_days' => $difference_days,
                'delay_days' => $delay_days,
                'delayed_task_count' => (int) $department_row['delayed_task_count'],
                'total_task_delay_days' => (float) $department_row['total_task_delay_days']
            );
        }

        $df_detail_tabs = array();

        foreach ($report_rows as $report_row) {
            $df_id = (int) $report_row['df_id'];
            $department_rows = isset($department_rows_by_df[$df_id]) ? $department_rows_by_df[$df_id] : array();

            usort($department_rows, function ($left, $right) {
                if ((int) $left['delay_days'] === (int) $right['delay_days']) {
                    if ((int) $left['difference_days'] === (int) $right['difference_days']) {
                        if ((float) $left['total_task_delay_days'] === (float) $right['total_task_delay_days']) {
                            return strcmp($left['department'], $right['department']);
                        }
                        return ((float) $left['total_task_delay_days'] > (float) $right['total_task_delay_days']) ? -1 : 1;
                    }
                    return ((int) $left['difference_days'] > (int) $right['difference_days']) ? -1 : 1;
                }
                return ((int) $left['delay_days'] > (int) $right['delay_days']) ? -1 : 1;
            });

            $top_department_rows = array_slice($department_rows, 0, 5);
            $pie_labels = array();
            $pie_values = array();
            $bar_labels = array();
            $bar_planned_values = array();
            $bar_actual_values = array();

            foreach ($top_department_rows as $department_row) {
                $pie_labels[] = $department_row['department'];
                $pie_values[] = (float) $department_row['delay_days'];
                $bar_labels[] = $department_row['department'];
                $bar_planned_values[] = (float) $department_row['planned_days'];
                $bar_actual_values[] = (float) $department_row['actual_days'];
            }

            $pie_mode = 'delay';
            if (array_sum($pie_values) <= 0) {
                $pie_mode = 'actual';
                $pie_values = array();
                foreach ($top_department_rows as $department_row) {
                    $pie_values[] = (float) $department_row['actual_days'];
                }
            }

            $df_detail_tabs[] = array(
                'tab_id' => 'df_tab_' . $df_id,
                'df_id' => $df_id,
                'df_no' => $report_row['df_no'],
                'df_release_date' => $report_row['df_release_date'],
                'planned_closure_date' => $report_row['planned_closure_date'],
                'actual_completion_date' => $report_row['actual_completion_date'],
                'planned_days' => (int) $report_row['planned_days'],
                'actual_days' => (int) $report_row['actual_days'],
                'difference_days' => (int) $report_row['difference_days'],
                'delay_days' => (int) $report_row['df_delay_days'],
                'status_label' => $report_row['status_label'],
                'department_rows' => $top_department_rows,
                'pie_chart' => array(
                    'labels' => $pie_labels,
                    'values' => $pie_values,
                    'mode' => $pie_mode
                ),
                'bar_chart' => array(
                    'labels' => $bar_labels,
                    'planned_values' => $bar_planned_values,
                    'actual_values' => $bar_actual_values
                )
            );
        }

        return $df_detail_tabs;
    }

    private function build_closed_df_financial_year_options() {
        $options = array();
        $current_year = (int) date('Y');
        for ($year = $current_year + 1; $year >= 2023; $year--) {
            $options[] = ($year - 1) . '-' . $year;
        }
        return $options;
    }

    private function build_closed_df_excel_sheet_title($seed, &$used_titles) {
        $base_title = preg_replace('/[\[\]\*\/\\\\\?\:]/', ' ', (string) $seed);
        $base_title = trim(preg_replace('/\s+/', ' ', $base_title));

        if ($base_title === '') {
            $base_title = 'DF';
        }

        $base_title = substr($base_title, 0, 31);
        $candidate = $base_title;
        $suffix_index = 1;

        while (isset($used_titles[strtolower($candidate)])) {
            $suffix = '-' . $suffix_index;
            $candidate = substr($base_title, 0, 31 - strlen($suffix)) . $suffix;
            $suffix_index++;
        }

        $used_titles[strtolower($candidate)] = true;
        return $candidate;
    }

    private function calculate_closed_df_day_span($start_date, $end_date) {
        $start_date = $this->normalize_issue_date($start_date);
        $end_date = $this->normalize_issue_date($end_date);

        if ($start_date === null || $end_date === null) {
            return 0;
        }

        $start_time = strtotime($start_date);
        $end_time = strtotime($end_date);

        if ($start_time === false || $end_time === false) {
            return 0;
        }

        return (int) abs(floor(($end_time - $start_time) / 86400));
    }

    private function add_excel_pie_chart($sheet, $title_text, $series_label_range, $category_range, $value_range, $top_left, $bottom_right) {
        $point_count = $this->count_excel_range_points($category_range);
        $label = array(
            new PHPExcel_Chart_DataSeriesValues('String', $series_label_range, null, 1)
        );
        $categories = array(
            new PHPExcel_Chart_DataSeriesValues('String', $category_range, null, $point_count)
        );
        $values = array(
            new PHPExcel_Chart_DataSeriesValues('Number', $value_range, null, $point_count)
        );

        $series = new PHPExcel_Chart_DataSeries(
            PHPExcel_Chart_DataSeries::TYPE_PIECHART,
            null,
            range(0, count($values) - 1),
            $label,
            $categories,
            $values
        );

        $plot_area = new PHPExcel_Chart_PlotArea(null, array($series));
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
        $title = new PHPExcel_Chart_Title($title_text);
        $chart = new PHPExcel_Chart('pie_' . md5($title_text . $top_left), $title, $legend, $plot_area, true, 0, null, null);
        $chart->setPlotVisibleOnly(false);
        $chart->setTopLeftPosition($top_left);
        $chart->setBottomRightPosition($bottom_right);
        $sheet->addChart($chart);
    }

    private function add_excel_bar_chart($sheet, $title_text, $category_range, $series_definitions, $top_left, $bottom_right, $horizontal = false) {
        $point_count = $this->count_excel_range_points($category_range);
        $data_series_labels = array();
        $data_series_values = array();
        foreach ($series_definitions as $series_index => $series_definition) {
            $data_series_labels[] = new PHPExcel_Chart_DataSeriesValues('String', $series_definition['label'], null, 1);
            $data_series_values[] = new PHPExcel_Chart_DataSeriesValues('Number', $series_definition['values'], null, $point_count);
        }

        $x_axis_tick_values = array(
            new PHPExcel_Chart_DataSeriesValues('String', $category_range, null, $point_count)
        );

        $series = new PHPExcel_Chart_DataSeries(
            PHPExcel_Chart_DataSeries::TYPE_BARCHART,
            PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,
            range(0, count($data_series_values) - 1),
            $data_series_labels,
            $x_axis_tick_values,
            $data_series_values
        );
        $series->setPlotDirection(
            $horizontal
                ? PHPExcel_Chart_DataSeries::DIRECTION_BAR
                : PHPExcel_Chart_DataSeries::DIRECTION_COL
        );

        $plot_area = new PHPExcel_Chart_PlotArea(null, array($series));
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
        $title = new PHPExcel_Chart_Title($title_text);
        $chart = new PHPExcel_Chart('bar_' . md5($title_text . $top_left), $title, $legend, $plot_area, true, 0, null, null);
        $chart->setPlotVisibleOnly(false);
        $chart->setTopLeftPosition($top_left);
        $chart->setBottomRightPosition($bottom_right);
        $sheet->addChart($chart);
    }

    private function count_excel_range_points($range) {
        if (!preg_match('/\$?[A-Z]+\$?(\d+):\$?[A-Z]+\$?(\d+)/', (string) $range, $matches)) {
            return 1;
        }

        $start_row = (int) $matches[1];
        $end_row = (int) $matches[2];
        if ($end_row < $start_row) {
            return 1;
        }

        return max(1, ($end_row - $start_row) + 1);
    }

     public function report_logic_summary() {
        
        $this->load->view('reports/report_logic_summary'); 
    }

    public function management_dashboard() {
        
        $this->load->model('Df_Report_model');
        $data = [];

        // 1. Get Trend Data
        $today = date('Y-m-d');
        $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
        $sixty_days_ago = date('Y-m-d', strtotime('-60 days'));
        $data['trends']['current'] = $this->Df_Report_model->get_trend_stats($thirty_days_ago, $today);
        $data['trends']['previous'] = $this->Df_Report_model->get_trend_stats($sixty_days_ago, $thirty_days_ago);

        // 2. Get At-Risk Tasks
        $data['at_risk_tasks'] = $this->Df_Report_model->get_at_risk_tasks();
        
        // 3. Get Root Cause Stats
        $data['root_cause_stats'] = $this->Df_Report_model->get_root_cause_stats();
        
        // 4. Get Approver Bottlenecks
        $data['approver_bottlenecks'] = $this->Df_Report_model->get_approver_bottlenecks();
        //echo "<pre>"; print_r($data['approver_bottlenecks']); exit;
        // 5. Load the view
        $this->load->view('reports/management_dashboard_view', $data);
    }

    public function trend_detail_report($period_days = 30) {
        
        $this->load->model('Df_Report_model');
        $this->load->model('Task_model'); // For formatIndianCurrency
        $data = [];

        // 1. Calculate Date Range
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-$period_days days"));
        
        $data['page_title'] = "Delayed Task Details (Last $period_days Days)";
        $data['start_date_str'] = date('d-m-Y', strtotime($start_date));
        $data['end_date_str'] = date('d-m-Y', strtotime($end_date));

        // 2. Get Task List
        $data['tasks'] = $this->Df_Report_model->get_trend_detail_tasks($start_date, $end_date);
        
        // 3. Load the new view
        $this->load->view('reports/trend_detail_report_view', $data);
    }

    public function df_issue_review() {
        $filter_data = $this->build_issue_review_filters();
        $filters = $filter_data['filters'];

        $report_data = $this->Df_Report_model->get_delay_data($filters);
        $tickets = $this->Df_Report_model->get_open_tickets_for_tasks($filters);
        $root_cause_stats = $this->Df_Report_model->get_root_cause_stats($filters);

        if (!empty($report_data)) {
            foreach ($report_data as $index => $row) {
                $delay = isset($row['delay_days']) ? (int) $row['delay_days'] : 0;
                $order_value = isset($row['order_value']) ? (float) $row['order_value'] : 0;
                $report_data[$index]['estimated_loss'] = ($order_value * (0.08 / 365)) * $delay;
            }
        }

        $prepared_data = $this->prepare_issue_review_payload($report_data, $tickets, $root_cause_stats);

        $data = array_merge($filter_data, $prepared_data);
        $data['page_title'] = 'DF Issue Review Report';
        $data['departments'] = $this->Df_Report_model->get_all_departments();
        $data['all_dfs'] = $this->Df_Report_model->get_all_dfs();

        $this->load->view('reports/df_issue_review_view', $data);
    }

public function ajax_get_df_details($user_id = 0) {
    // Basic security check
    if ($user_id == 0 || !$this->input->is_ajax_request()) {
        show_404();
        return;
    }
    
    $this->load->model('Df_Report_model');
    $filters = [];
    
    // Get and format dates from the GET query string
    $start_date_str = $this->input->get('start_date');
    $end_date_str = $this->input->get('end_date');
    
    if ($start_date_str && $end_date_str) {
        $start_date_obj = DateTime::createFromFormat('d-m-Y', $start_date_str);
        $end_date_obj = DateTime::createFromFormat('d-m-Y', $end_date_str);
        $filters['start_date'] = $start_date_obj ? $start_date_obj->format('Y-m-d') : null;
        $filters['end_date'] = $end_date_obj ? $end_date_obj->format('Y-m-d') : null;
    }
    
    // Call the new model function
    $df_list = $this->Df_Report_model->get_user_df_details($user_id, $filters);
    
    // Format dates for display
    foreach ($df_list as &$df) {
        // Handle potential NULL or invalid dates
        $start_obj = $df['start_date'] ? DateTime::createFromFormat('Y-m-d', $df['start_date']) : false;
        $end_obj = $df['end_date'] ? DateTime::createFromFormat('Y-m-d', $df['end_date']) : false;
        
        $df['start_date'] = $start_obj ? $start_obj->format('d-m-Y') : 'N/A';
        $df['end_date'] = $end_obj ? $end_obj->format('d-m-Y') : 'N/A';
        
        $df['df_name'] = htmlspecialchars($df['df_name']);
        $df['marketing_person'] = htmlspecialchars($df['marketing_person'] ?? 'N/A');
    }

    // Send back a JSON response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'dfs' => $df_list]);
    exit;
}

private function get_request_filter_value($key) {
    $value = $this->input->post($key);
    if ($value === null || $value === '') {
        $value = $this->input->get($key);
    }
    return $value;
}

private function build_issue_review_filters() {
    $filters = array(
        'department_id' => $this->get_request_filter_value('department_id'),
        'df_id' => $this->get_request_filter_value('df_id')
    );

    $start_date_str = $this->get_request_filter_value('start_date');
    $end_date_str = $this->get_request_filter_value('end_date');
    $selected_fy = $this->get_request_filter_value('financial_year');

    $is_filtered = $start_date_str || $end_date_str || $selected_fy || $filters['department_id'] || $filters['df_id'];

    if (!$is_filtered) {
        $current_month = (int) date('m');
        $current_year = (int) date('Y');

        if ($current_month >= 4) {
            $fy_start_year = $current_year;
            $fy_end_year = $current_year + 1;
        } else {
            $fy_start_year = $current_year - 1;
            $fy_end_year = $current_year;
        }

        $selected_fy = $fy_start_year . '-' . $fy_end_year;
    }

    $period_label = 'Current delayed tasks';

    if ($selected_fy) {
        $years = explode('-', $selected_fy);
        $fy_start = $years[0] . '-04-01';
        $fy_end = $years[1] . '-03-31';

        $filters['start_date'] = $fy_start;
        $filters['end_date'] = $fy_end;
        $start_date_str = date('d-m-Y', strtotime($fy_start));
        $end_date_str = date('d-m-Y', strtotime($fy_end));
        $period_label = 'FY ' . $selected_fy;
    } elseif ($start_date_str && $end_date_str) {
        $start_date_obj = DateTime::createFromFormat('d-m-Y', $start_date_str);
        $end_date_obj = DateTime::createFromFormat('d-m-Y', $end_date_str);
        $filters['start_date'] = $start_date_obj ? $start_date_obj->format('Y-m-d') : null;
        $filters['end_date'] = $end_date_obj ? $end_date_obj->format('Y-m-d') : null;
        $period_label = $start_date_str . ' to ' . $end_date_str;
    } elseif (!empty($filters['department_id']) || !empty($filters['df_id'])) {
        $period_label = 'All historical delayed tasks';
    } else {
        $filters['show_all_pending'] = true;
        $period_label = 'Pending delayed tasks as of ' . date('d-m-Y');
    }

    return array(
        'filters' => $filters,
        'report_period_label' => $period_label,
        'selected_start_date' => $start_date_str,
        'selected_end_date' => $end_date_str,
        'selected_department_id' => $filters['department_id'],
        'selected_df_id' => $filters['df_id'],
        'selected_fy' => $selected_fy
    );
}

private function prepare_issue_review_payload($report_data, $tickets, $root_cause_stats = array()) {
    $df_summaries = array();
    $department_rollup = array();
    $bottleneck_counts = array('approver' => 0, 'assignee' => 0);
    $total_estimated_loss = 0;
    $ticketed_task_count = 0;
    $open_issue_count = 0;

    foreach ($report_data as $row) {
        $df_id = isset($row['df_id']) ? (int) $row['df_id'] : 0;
        if ($df_id <= 0) {
            continue;
        }

        if (!isset($df_summaries[$df_id])) {
            $df_summaries[$df_id] = array(
                'df_id' => $df_id,
                'df_no' => !empty($row['df_no']) ? $row['df_no'] : 'DF-' . $df_id,
                'df_description' => !empty($row['df_description']) ? $row['df_description'] : 'No DF description available.',
                'total_issues' => 0,
                'pending_delay_count' => 0,
                'pending_approval_count' => 0,
                'closed_delay_count' => 0,
                'approver_bottleneck_count' => 0,
                'assignee_bottleneck_count' => 0,
                'max_delay_days' => 0,
                'total_delay_days' => 0,
                'estimated_loss_total' => 0,
                'department_counts' => array(),
                'departments' => array(),
                'users' => array(),
                'owner_counts' => array(),
                'task_names' => array(),
                'ticket_count' => 0,
                'ticket_refs' => array(),
                'ticket_remarks' => array(),
                'reason_counts' => array(),
                'top_department' => '',
                'top_reason' => '',
                'primary_issue_label' => '',
                'latest_signal' => '',
                'severity_rank' => 1,
                'severity_label' => 'Low',
                'avg_delay_days' => 0,
                'issue_highlights' => array(),
                'improvement_points' => array(),
                'department_list' => array(),
                'department_list_text' => '',
                'ticket_refs_text' => '',
                'primary_owner' => 'Unassigned',
                'planned_start_date' => null,
                'planned_end_date' => null,
                'actual_end_date' => null,
                'status_label' => 'Watch'
            );
        }

        $delay = isset($row['delay_days']) ? (int) $row['delay_days'] : 0;
        $loss = isset($row['estimated_loss']) ? (float) $row['estimated_loss'] : 0;
        $department_name = !empty($row['department']) ? $row['department'] : 'Unassigned';
        $bottleneck_type = (!empty($row['bottleneck_type']) && $row['bottleneck_type'] === 'approver') ? 'approver' : 'assignee';
        $start_date = $this->normalize_issue_date($row['start_date'] ?? null);
        $end_date = $this->normalize_issue_date($row['end_date'] ?? null);
        $completed_date = $this->normalize_issue_date($row['task_completed_on'] ?? null);

        $df_summaries[$df_id]['total_issues']++;
        $df_summaries[$df_id]['total_delay_days'] += $delay;
        $df_summaries[$df_id]['max_delay_days'] = max($df_summaries[$df_id]['max_delay_days'], $delay);
        $df_summaries[$df_id]['estimated_loss_total'] += $loss;
        $df_summaries[$df_id][$bottleneck_type . '_bottleneck_count']++;
        $bottleneck_counts[$bottleneck_type]++;
        $total_estimated_loss += $loss;

        if ((int) $row['task_status'] === 2) {
            $df_summaries[$df_id]['pending_approval_count']++;
            $open_issue_count++;
        } elseif ((int) $row['task_status'] === 1) {
            $df_summaries[$df_id]['closed_delay_count']++;
        } else {
            $df_summaries[$df_id]['pending_delay_count']++;
            $open_issue_count++;
        }

        if ($start_date !== null) {
            if ($df_summaries[$df_id]['planned_start_date'] === null || strtotime($start_date) < strtotime($df_summaries[$df_id]['planned_start_date'])) {
                $df_summaries[$df_id]['planned_start_date'] = $start_date;
            }
        }

        if ($end_date !== null) {
            if ($df_summaries[$df_id]['planned_end_date'] === null || strtotime($end_date) > strtotime($df_summaries[$df_id]['planned_end_date'])) {
                $df_summaries[$df_id]['planned_end_date'] = $end_date;
            }
        }

        $actual_end_candidate = $completed_date;
        if ($actual_end_candidate === null && (int) $row['task_status'] !== 1) {
            $actual_end_candidate = date('Y-m-d');
        }
        if ($actual_end_candidate === null) {
            $actual_end_candidate = $end_date;
        }
        if ($actual_end_candidate !== null) {
            if ($df_summaries[$df_id]['actual_end_date'] === null || strtotime($actual_end_candidate) > strtotime($df_summaries[$df_id]['actual_end_date'])) {
                $df_summaries[$df_id]['actual_end_date'] = $actual_end_candidate;
            }
        }

        if (!isset($df_summaries[$df_id]['department_counts'][$department_name])) {
            $df_summaries[$df_id]['department_counts'][$department_name] = 0;
        }
        $df_summaries[$df_id]['department_counts'][$department_name]++;
        $df_summaries[$df_id]['departments'][$department_name] = $department_name;

        if (!isset($department_rollup[$department_name])) {
            $department_rollup[$department_name] = array('issues' => 0, 'delay_days' => 0);
        }
        $department_rollup[$department_name]['issues']++;
        $department_rollup[$department_name]['delay_days'] += $delay;

        if (!empty($row['assigned_user_name'])) {
            $df_summaries[$df_id]['users'][$row['assigned_user_name']] = $row['assigned_user_name'];
            if (!isset($df_summaries[$df_id]['owner_counts'][$row['assigned_user_name']])) {
                $df_summaries[$df_id]['owner_counts'][$row['assigned_user_name']] = 0;
            }
            $df_summaries[$df_id]['owner_counts'][$row['assigned_user_name']]++;
        }

        if (!empty($row['task_name'])) {
            $df_summaries[$df_id]['task_names'][$row['task_name']] = $row['task_name'];
        }

        $signal_text = '';
        if (isset($tickets[$row['id']])) {
            $ticket = $tickets[$row['id']];
            $df_summaries[$df_id]['ticket_count']++;
            $ticketed_task_count++;

            if (!empty($ticket['help_ticket_no'])) {
                $df_summaries[$df_id]['ticket_refs']['#' . $ticket['help_ticket_no']] = '#' . $ticket['help_ticket_no'];
            }

            if (!empty($ticket['remarks'])) {
                $signal_text = trim($ticket['remarks']);
                $df_summaries[$df_id]['ticket_remarks'][] = $signal_text;
            }

            $delay_reason = trim((string) ($ticket['delay_reason'] ?? ''));
            if ($delay_reason === '') {
                $delay_reason = 'Not Specified';
            }

            if (!isset($df_summaries[$df_id]['reason_counts'][$delay_reason])) {
                $df_summaries[$df_id]['reason_counts'][$delay_reason] = 0;
            }
            $df_summaries[$df_id]['reason_counts'][$delay_reason]++;
        }

        if ($signal_text === '' && !empty($row['remarks'])) {
            $signal_text = trim($row['remarks']);
        }

        if ($signal_text !== '' && $df_summaries[$df_id]['latest_signal'] === '') {
            $df_summaries[$df_id]['latest_signal'] = $this->build_issue_excerpt($signal_text, 135);
        }
    }

    $df_summaries = array_values($df_summaries);

    foreach ($df_summaries as $index => $summary) {
        arsort($summary['department_counts']);
        $department_names = array_keys($summary['department_counts']);
        $summary['department_list'] = $department_names;
        $summary['top_department'] = !empty($department_names) ? $department_names[0] : 'Unassigned';

        arsort($summary['reason_counts']);
        $reason_names = array_keys($summary['reason_counts']);
        $summary['top_reason'] = !empty($reason_names) ? $reason_names[0] : '';

        $summary['severity_rank'] = $this->get_df_issue_severity_rank($summary);
        $summary['severity_label'] = $this->get_df_issue_severity_label($summary['severity_rank']);
        $summary['primary_issue_label'] = $this->get_df_primary_issue_label($summary);
        $summary['avg_delay_days'] = $summary['total_issues'] > 0 ? round($summary['total_delay_days'] / $summary['total_issues'], 1) : 0;
        $summary['status_label'] = $this->get_df_status_label($summary);

        if (!empty($summary['owner_counts'])) {
            arsort($summary['owner_counts']);
            $owner_names = array_keys($summary['owner_counts']);
            $summary['primary_owner'] = !empty($owner_names[0]) ? $owner_names[0] : 'Unassigned';
        }

        $summary['department_list_text'] = !empty($summary['department_list']) ? implode(', ', array_slice($summary['department_list'], 0, 3)) : 'No department mapped';
        if (count($summary['department_list']) > 3) {
            $summary['department_list_text'] .= ' +' . (count($summary['department_list']) - 3) . ' more';
        }

        $summary['ticket_refs_text'] = !empty($summary['ticket_refs']) ? implode(', ', array_slice(array_values($summary['ticket_refs']), 0, 3)) : 'No open ticket';
        $summary['issue_highlights'] = $this->get_df_issue_highlights($summary);
        $summary['improvement_points'] = $this->get_df_improvement_points($summary);

        $df_summaries[$index] = $summary;
    }

    usort($df_summaries, function($left, $right) {
        if ($left['severity_rank'] !== $right['severity_rank']) {
            return $right['severity_rank'] - $left['severity_rank'];
        }
        if ($left['total_issues'] !== $right['total_issues']) {
            return $right['total_issues'] - $left['total_issues'];
        }
        if ($left['max_delay_days'] !== $right['max_delay_days']) {
            return $right['max_delay_days'] - $left['max_delay_days'];
        }
        if ($left['estimated_loss_total'] === $right['estimated_loss_total']) {
            return strcmp($left['df_no'], $right['df_no']);
        }
        return ($right['estimated_loss_total'] > $left['estimated_loss_total']) ? 1 : -1;
    });

    $critical_high_count = 0;
    foreach ($df_summaries as $summary) {
        if ($summary['severity_rank'] >= 3) {
            $critical_high_count++;
        }
    }

    $top_department = null;
    if (!empty($department_rollup)) {
        uasort($department_rollup, function($left, $right) {
            if ($left['issues'] !== $right['issues']) {
                return $right['issues'] - $left['issues'];
            }
            return $right['delay_days'] - $left['delay_days'];
        });

        $top_department_name = key($department_rollup);
        $top_department_stats = current($department_rollup);
        $top_department = array(
            'name' => $top_department_name,
            'issues' => $top_department_stats['issues'],
            'delay_days' => $top_department_stats['delay_days']
        );
    }

    $primary_bottleneck = 'Balanced follow-up';
    if ($bottleneck_counts['approver'] > $bottleneck_counts['assignee']) {
        $primary_bottleneck = 'Approval waiting';
    } elseif ($bottleneck_counts['assignee'] > 0) {
        $primary_bottleneck = 'Execution backlog';
    }

    $top_df = !empty($df_summaries) ? $df_summaries[0] : null;
    $common_reason = !empty($root_cause_stats[0]['reason']) ? $root_cause_stats[0]['reason'] : '';
    if ($common_reason === '' && !empty($top_df['top_reason'])) {
        $common_reason = $top_df['top_reason'];
    }
    if ($common_reason === '') {
        $common_reason = 'Reason data still needs standardisation';
    }

    $department_chart = array(
        'labels' => array(),
        'issues' => array()
    );

    if (!empty($department_rollup)) {
        $chart_counter = 0;
        foreach ($department_rollup as $department_name => $stats) {
            $department_chart['labels'][] = $department_name;
            $department_chart['issues'][] = (int) $stats['issues'];
            $chart_counter++;
            if ($chart_counter >= 6) {
                break;
            }
        }
    }

    $gantt_rows = array();
    foreach ($df_summaries as $summary) {
        if (!empty($summary['planned_start_date']) && !empty($summary['planned_end_date'])) {
            $gantt_rows[] = $summary;
        }
        if (count($gantt_rows) >= 6) {
            break;
        }
    }

    $gantt_timeline = array(
        'start' => null,
        'end' => null,
        'total_days' => 0,
        'today' => date('Y-m-d')
    );

    foreach ($gantt_rows as $row) {
        if ($gantt_timeline['start'] === null || strtotime($row['planned_start_date']) < strtotime($gantt_timeline['start'])) {
            $gantt_timeline['start'] = $row['planned_start_date'];
        }

        $row_end_date = !empty($row['actual_end_date']) ? $row['actual_end_date'] : $row['planned_end_date'];
        if ($gantt_timeline['end'] === null || strtotime($row_end_date) > strtotime($gantt_timeline['end'])) {
            $gantt_timeline['end'] = $row_end_date;
        }
    }

    if ($gantt_timeline['start'] !== null) {
        $timeline_start = new DateTime($gantt_timeline['start']);
        $timeline_end = new DateTime($gantt_timeline['end']);
        $timeline_start->modify('-1 day');
        $timeline_end->modify('+1 day');
        $gantt_timeline['start'] = $timeline_start->format('Y-m-d');
        $gantt_timeline['end'] = $timeline_end->format('Y-m-d');
        $gantt_timeline['total_days'] = (int) $timeline_start->diff($timeline_end)->days + 1;
    }

    return array(
        'report_data' => $report_data,
        'tickets' => $tickets,
        'root_cause_stats' => $root_cause_stats,
        'priority_dfs' => array_slice($df_summaries, 0, 8),
        'secondary_dfs' => array_slice($df_summaries, 8),
        'all_df_summaries' => $df_summaries,
        'summary_cards' => array(
            'dfs_with_issues' => count($df_summaries),
            'delayed_tasks' => count($report_data),
            'open_issue_count' => $open_issue_count,
            'ticketed_tasks' => $ticketed_task_count,
            'estimated_exposure' => round($total_estimated_loss, 0)
        ),
        'management_highlights' => array(
            'top_df' => $top_df,
            'top_department' => $top_department,
            'primary_bottleneck' => $primary_bottleneck,
            'common_reason' => $common_reason,
            'critical_high_count' => $critical_high_count
        ),
        'overall_improvement_points' => $this->build_overall_improvement_points($df_summaries, $bottleneck_counts, $root_cause_stats),
        'chart_data' => array(
            'department' => $department_chart
        ),
        'gantt_rows' => $gantt_rows,
        'gantt_timeline' => $gantt_timeline
    );
}

private function get_df_issue_severity_rank($summary) {
    if ($summary['max_delay_days'] >= 21 || $summary['total_issues'] >= 6 || $summary['estimated_loss_total'] >= 100000) {
        return 4;
    }

    if ($summary['max_delay_days'] >= 12 || $summary['total_issues'] >= 4 || $summary['pending_delay_count'] >= 3) {
        return 3;
    }

    if ($summary['max_delay_days'] >= 5 || $summary['total_issues'] >= 2 || $summary['ticket_count'] >= 1) {
        return 2;
    }

    return 1;
}

private function get_df_issue_severity_label($severity_rank) {
    if ($severity_rank >= 4) {
        return 'Critical';
    }

    if ($severity_rank === 3) {
        return 'High';
    }

    if ($severity_rank === 2) {
        return 'Watch';
    }

    return 'Low';
}

private function get_df_primary_issue_label($summary) {
    if ($summary['pending_approval_count'] > 0 && $summary['pending_approval_count'] >= $summary['pending_delay_count']) {
        return 'Approval wait';
    }

    if ($summary['pending_delay_count'] > 0) {
        return 'Execution backlog';
    }

    if ($summary['closed_delay_count'] > 0) {
        return 'Late closure';
    }

    return 'Mixed issue';
}

private function get_df_status_label($summary) {
    if ($summary['pending_approval_count'] > 0 && $summary['pending_approval_count'] >= $summary['pending_delay_count']) {
        return 'Pending approval';
    }

    if ($summary['pending_delay_count'] > 0) {
        return 'Open overdue';
    }

    if ($summary['closed_delay_count'] > 0) {
        return 'Delayed closed';
    }

    return 'Watch';
}

private function get_df_issue_highlights($summary) {
    $highlights = array();

    if ($summary['pending_delay_count'] > 0) {
        $highlights[] = $summary['pending_delay_count'] . ' overdue task(s) are still open.';
    }

    if ($summary['pending_approval_count'] > 0) {
        $highlights[] = $summary['pending_approval_count'] . ' task(s) are waiting at approval stage.';
    }

    if (!empty($summary['top_reason']) && $summary['top_reason'] !== 'Not Specified') {
        $highlights[] = 'Most reported blocker: ' . $summary['top_reason'] . '.';
    } elseif ($summary['ticket_count'] > 0) {
        $highlights[] = $summary['ticket_count'] . ' ticket-linked issue(s) need follow-up.';
    }

    if (count($summary['department_list']) > 1) {
        $highlights[] = 'Impact is spread across ' . $summary['department_list_text'] . '.';
    }

    if ($summary['max_delay_days'] > 0) {
        $highlights[] = 'Longest delay has reached ' . $summary['max_delay_days'] . ' day(s).';
    }

    if (empty($highlights)) {
        $highlights[] = 'Limited signal available, but this DF still appears in the delay tracker.';
    }

    return array_slice(array_values(array_unique($highlights)), 0, 3);
}

private function get_df_improvement_points($summary) {
    $points = array();

    if ($summary['pending_approval_count'] > 0 && $summary['pending_approval_count'] >= $summary['pending_delay_count']) {
        $points[] = 'Set a same-day approval SLA and escalate aged approvals in the daily review.';
    }

    if ($summary['pending_delay_count'] > 0) {
        $points[] = 'Break overdue open work into 48-hour recovery checkpoints with named owners.';
    }

    if (count($summary['department_list']) >= 3) {
        $points[] = 'Nominate one DF owner for cross-functional follow-up across all impacted departments.';
    }

    if ($summary['ticket_count'] > 0) {
        $points[] = 'Convert open tickets into an owner-wise closure tracker with target dates.';
    }

    if ($summary['top_reason'] === 'Not Specified' && $summary['ticket_count'] > 0) {
        $points[] = 'Make delay reason mandatory before a ticket can stay open.';
    } elseif (!empty($summary['top_reason']) && $summary['top_reason'] !== 'Not Specified') {
        $points[] = 'Address the recurring blocker "' . $summary['top_reason'] . '" with a department-level countermeasure.';
    }

    if ($summary['max_delay_days'] >= 15 || $summary['estimated_loss_total'] >= 50000) {
        $points[] = 'Review this DF in the management meeting until the oldest delay is closed.';
    }

    if (empty($points)) {
        $points[] = 'Keep this DF on weekly review and close the oldest delayed task first.';
    }

    return array_slice(array_values(array_unique($points)), 0, 3);
}

private function build_overall_improvement_points($df_summaries, $bottleneck_counts, $root_cause_stats = array()) {
    $points = array();
    $multi_department_df_count = 0;
    $ticketed_df_count = 0;

    foreach ($df_summaries as $summary) {
        if (count($summary['department_list']) >= 3) {
            $multi_department_df_count++;
        }
        if ($summary['ticket_count'] > 0) {
            $ticketed_df_count++;
        }
    }

    if ($bottleneck_counts['approver'] > $bottleneck_counts['assignee']) {
        $points[] = 'Tighten approval turnaround with a visible SLA and escalation ageing list.';
    }

    if (!empty($root_cause_stats[0]['reason']) && $root_cause_stats[0]['reason'] === 'Not Specified') {
        $points[] = 'Standardise delay reason capture so management can act on trends instead of assumptions.';
    }

    if ($multi_department_df_count > 0) {
        $points[] = 'Run a weekly cross-functional recovery review for DFs touching three or more departments.';
    }

    if ($ticketed_df_count > 0) {
        $points[] = 'Track ticket-linked DFs in a separate closure sheet with owner and committed closure date.';
    }

    if (empty($points)) {
        $points[] = 'Continue reviewing the highest-delay DFs first and close the oldest backlog every week.';
    }

    return array_slice(array_values(array_unique($points)), 0, 3);
}

private function build_issue_excerpt($text, $limit = 135) {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));
    if ($text === '') {
        return '';
    }

    if (strlen($text) <= $limit) {
        return $text;
    }

    return rtrim(substr($text, 0, $limit - 3)) . '...';
}

private function normalize_issue_date($value) {
    $value = trim((string) $value);
    if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return null;
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return null;
    }

    return date('Y-m-d', $timestamp);
}

}
