<?php
class Report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_dfs() {
        $this->db->select('id, df_no, added_on');
        $this->db->from('df_release');
        $this->db->order_by('added_on', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_df_details($df_id) {
        $this->db->select('d.id, d.df_no, p.company_name, d.added_on, su.first_name as marketing_person');
        $this->db->from('df_release d');
        $this->db->join('poreceived p', 'p.df_id=d.id', 'left');
        $this->db->join('system_users su', 'd.added_by = su.user_id', 'left');
        $this->db->where('d.id', $df_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_project_date_range($df_id) {
        $this->db->select('MIN(start_date) as min_start_date, MAX(IFNULL(task_completed_on, end_date)) as max_end_date');
        $this->db->from('task_department_wise_scheduling');
        $this->db->where('df_id', $df_id);
        $this->db->where('taskid >', 0); // Exclude master task
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return [$row->min_start_date, $row->max_end_date];
        }
        return [];
    }

    public function get_all_tasks_for_report($df_id) {
        $report_data = [];
        $main_tasks_query = $this->db->select('m.id, m.department_id, m.taskname, d.department as department_name, d.color')
            ->from('mdgantchartmaster m')
            ->join('departments d', 'm.department_id = d.department_id', 'left')
            ->order_by('m.sortorder', 'asc')
            ->get();
        
        foreach ($main_tasks_query->result_array() as $main_task) {
            $subtasks_query = $this->db->select('s.taskid, t.task_name, s.start_date, s.end_date, s.task_completed_on, s.task_status')
                ->from('sharmajitaskmapping sm')
                ->join('task_department_wise_scheduling s', 'sm.task_id = s.taskid', 'left')
                ->join('task_management t', 'sm.task_id = t.task_id', 'left')
                ->where('sm.report_id', $main_task['id'])
                ->where('s.df_id', $df_id)
                ->get();
            
            $subtasks = [];
            foreach ($subtasks_query->result_array() as $subtask) {
                if ($subtask['taskid']) {
                    $subtasks[] = $subtask;
                }
            }
            $main_task['subtasks'] = $subtasks;
            $report_data[] = $main_task;
        }
        return $report_data;
    }

    public function get_aggregated_dates($main_task_id, $df_id, $department_id) {
        $this->db->select('MIN(s.start_date) as min_start_date, MAX(s.end_date) as max_end_date');
        $this->db->from('sharmajitaskmapping sm');
        $this->db->join('task_department_wise_scheduling s', 'sm.task_id = s.taskid', 'left');
        $this->db->where('sm.report_id', $main_task_id);
        $this->db->where('s.df_id', $df_id);
        $this->db->where('s.department_id', $department_id);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function get_aggregated_actual_dates($main_task_id, $df_id) {
        $this->db->select('MIN(s.assigned_on) as min_start, MAX(s.task_completed_on) as max_end');
        $this->db->from('sharmajitaskmapping sm');
        $this->db->join('task_department_wise_scheduling s', 'sm.task_id = s.taskid', 'left');
        $this->db->where('sm.report_id', $main_task_id);
        $this->db->where('s.df_id', $df_id);
        $this->db->where('s.task_status', 1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_delay_days($planned_end, $actual_end) {
        if ($planned_end && $actual_end && strtotime($actual_end) > strtotime($planned_end)) {
            $planned_date = new DateTime($planned_end);
            $actual_date = new DateTime($actual_end);
            return $actual_date->diff($planned_date)->days . " Days";
        }
        return "";
    }

    public function get_completion_percentage($sub_tasks) {
        $total_tasks = count($sub_tasks);
        if ($total_tasks == 0) {
            return 0;
        }
        $completed_tasks = 0;
        foreach ($sub_tasks as $subtask) {
            if (isset($subtask['task_status']) && $subtask['task_status'] == 1) {
                $completed_tasks++;
            }
        }
        return ($completed_tasks / $total_tasks) * 100;
    }
    
    public function get_mondays($start_date, $end_date) {
        if (empty($start_date) || empty($end_date)) return [];
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $mondays = [];
        $current = clone $start;
        if ($current->format('N') != 1) {
            $current->modify('next monday');
        }
        while ($current <= $end) {
            $mondays[] = $current->format('Y-m-d');
            $current->modify('+1 week');
        }
        return $mondays;
    }

    public function get_days_count_for_each_month_from_array($dates) {
        $counts = [];
        foreach ($dates as $date) {
            $month = date('F Y', strtotime($date));
            if (!isset($counts[$month])) {
                $counts[$month] = 0;
            }
            $counts[$month]++;
        }
        return $counts;
    }

    public function get_week_number_from_date($date) {
        if (empty($date) || $date == '0000-00-00 00:00:00') return '';
        $d = new DateTime($date);
        return $d->format('W');
    }

    public function get_week_numbers_between_dates($start, $end) {
        if (empty($start) || empty($end)) {
            return [];
        }
        $start_date = new DateTime($start);
        $end_date = new DateTime($end);
        $interval = new DateInterval('P1W');
        $period = new DatePeriod($start_date, $interval, $end_date);
        $weeks = [];
        foreach ($period as $date) {
            $weeks[] = $date->format('oW');
        }
        $weeks[] = $end_date->format('oW');
        return array_unique($weeks);
    }

    public function get_main_task_completion_date($sub_tasks) {
        $latest_completion_date = null;
        $all_completed = true;

        foreach ($sub_tasks as $subtask) {
            if ($subtask['task_status'] != 1) {
                $all_completed = false;
                break;
            }
            if ($subtask['task_completed_on']) {
                $latest_completion_date = max($latest_completion_date, strtotime($subtask['task_completed_on']));
            }
        }

        return $all_completed && $latest_completion_date ? date('Y-m-d', $latest_completion_date) : null;
    }

    public function get_daily_df_progress_report($report_date, $scope = 'active') {
        $report_date = $this->normalize_report_date($report_date);
        $scope = $this->normalize_daily_df_scope($scope);

        $base_rows = $this->get_daily_df_base_rows($report_date, $scope);
        if (empty($base_rows)) {
            return [
                'report_date' => $report_date,
                'scope' => $scope,
                'summary' => $this->get_empty_daily_df_summary(),
                'user_summary' => $this->get_empty_daily_df_user_summary(),
                'department_activity' => [],
                'movement_breakdown' => [
                    'completed' => 0,
                    'updates' => 0,
                    'assignments' => 0,
                    'tickets' => 0
                ],
                'user_activity' => [],
                'top_active_users' => [],
                'idle_users' => [],
                'rows' => [],
                'top_attention' => [],
                'top_active_df' => null,
                'top_department' => null
            ];
        }

        $df_ids = array_map('intval', array_column($base_rows, 'id'));
        $task_rows = $this->get_daily_df_task_rows($df_ids);
        // Filter through the joined task table to avoid thousands of task IDs
        // overflowing CodeIgniter's WHERE-clause regular expression.
        $history_rows = $this->get_daily_df_history_rows($df_ids, $report_date);
        $ticket_rows = $this->get_daily_df_ticket_rows($df_ids);

        $current_date = date('Y-m-d');
        $active_df_ids = [];
        $rows = [];
        $department_activity = [];
        $movement_breakdown = [
            'completed' => 0,
            'updates' => 0,
            'assignments' => 0,
            'tickets' => 0
        ];

        foreach ($base_rows as $df) {
            $marketing_owner = $this->format_person_name(
                $df['marketing_title'],
                $df['marketing_first_name'],
                $df['marketing_last_name']
            );
            $created_by = $this->format_person_name(
                $df['creator_title'],
                $df['creator_first_name'],
                $df['creator_last_name']
            );

            $rows[(int) $df['id']] = [
                'id' => (int) $df['id'],
                'df_no' => (string) $df['df_no'],
                'df_description' => (string) $df['df_description'],
                'company_name' => (string) $df['company_name'],
                'po_no' => (string) $df['po_no'],
                'machine_name' => (string) $df['machine_name'],
                'marketing_owner' => $marketing_owner !== '' ? $marketing_owner : ($created_by !== '' ? $created_by : 'Not Available'),
                'created_by' => $created_by !== '' ? $created_by : 'Not Available',
                'release_date' => (string) $df['added_on'],
                'completed_on' => (string) $df['completed_on'],
                'df_status' => (int) $df['df_status'],
                'on_hold' => (int) $df['on_hold'],
                'planned_close_date' => '',
                'dispatch_due_date' => '',
                'latest_activity_on' => '',
                'latest_activity_summary' => 'No activity captured yet',
                'latest_activity_actor' => 'System',
                'next_due_date' => '',
                'next_due_task' => '',
                'next_due_owner' => '',
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'open_tasks' => 0,
                'completion_pct' => 0,
                'today_completed_count' => 0,
                'today_touched_count' => 0,
                'today_assignment_count' => 0,
                'today_remark_count' => 0,
                'today_new_ticket_count' => 0,
                'closed_late_today_count' => 0,
                'open_delayed_count' => 0,
                'max_delay_days' => 0,
                'open_ticket_count' => 0,
                'has_movement_today' => false,
                'no_update_today' => false,
                'status_key' => 'stalled',
                'status_label' => 'No Update',
                'today_events' => [],
                'today_department_counts' => [],
                'delayed_tasks' => [],
                'ticket_numbers' => [],
                'risk_score' => 0,
                '_today_touch_ids' => [],
                '_today_completed_ids' => [],
                '_today_assigned_ids' => [],
                '_today_remark_ids' => [],
                '_today_ticket_ids' => [],
            ];

            if ((int) $df['df_status'] === 0) {
                $active_df_ids[] = (int) $df['id'];
            }
        }

        $user_activity = [];
        foreach ($task_rows as $task) {
            $df_id = (int) $task['df_id'];
            if (!isset($rows[$df_id])) {
                continue;
            }

            $task_id = (int) $task['id'];
            $assigned_name = $this->format_person_name(
                $task['assigned_title'],
                $task['assigned_first_name'],
                $task['assigned_last_name']
            );
            $assigned_name = $assigned_name !== '' ? $assigned_name : 'Unassigned';

            $completed_by_name = $this->format_person_name(
                $task['completed_title'],
                $task['completed_first_name'],
                $task['completed_last_name']
            );
            $completed_by_name = $completed_by_name !== '' ? $completed_by_name : $assigned_name;
            $assigned_user_id = (int) $task['assigned_user'];
            $is_active_df_task = in_array($df_id, $active_df_ids, true);

            $rows[$df_id]['total_tasks']++;

            $end_date = $this->normalize_report_date($task['end_date']);
            if ($end_date !== '') {
                if ($rows[$df_id]['planned_close_date'] === '' || $end_date > $rows[$df_id]['planned_close_date']) {
                    $rows[$df_id]['planned_close_date'] = $end_date;
                }
                if ((int) $task['taskid'] === 103) {
                    $rows[$df_id]['dispatch_due_date'] = $end_date;
                }
            }

            $is_completed_currently = ((int) $task['task_status'] === 1 && $this->is_valid_datetime($task['task_completed_on']));
            if ($is_completed_currently) {
                $rows[$df_id]['completed_tasks']++;
            } else {
                $rows[$df_id]['open_tasks']++;

                if ((int) $task['on_hold'] === 0 && $end_date !== '') {
                    if ($rows[$df_id]['next_due_date'] === '' || $end_date < $rows[$df_id]['next_due_date']) {
                        $rows[$df_id]['next_due_date'] = $end_date;
                        $rows[$df_id]['next_due_task'] = (string) $task['task_name'];
                        $rows[$df_id]['next_due_owner'] = $assigned_name;
                    }
                }
            }

            if (!$is_completed_currently && (int) $task['on_hold'] === 0 && $end_date !== '' && $end_date < $current_date) {
                $delay_days = (int) floor((strtotime($current_date) - strtotime($end_date)) / 86400);
                $rows[$df_id]['open_delayed_count']++;
                $rows[$df_id]['max_delay_days'] = max($rows[$df_id]['max_delay_days'], $delay_days);
                if (count($rows[$df_id]['delayed_tasks']) < 4) {
                    $rows[$df_id]['delayed_tasks'][] = [
                        'task_name' => (string) $task['task_name'],
                        'department' => (string) $task['department'],
                        'owner' => $assigned_name,
                        'delay_days' => $delay_days,
                        'due_date' => $end_date
                    ];
                }
            }

            if ($this->is_valid_datetime($task['assigned_on'])) {
                $this->set_daily_df_latest_activity(
                    $rows[$df_id],
                    $task['assigned_on'],
                    'Task assigned: ' . $task['task_name'],
                    $assigned_name
                );
            }

            if ($this->is_valid_datetime($task['taskupdatedontime'])) {
                $this->set_daily_df_latest_activity(
                    $rows[$df_id],
                    $task['taskupdatedontime'],
                    'Task updated: ' . $task['task_name'],
                    $assigned_name
                );
            }

            if ($this->is_valid_datetime($task['task_completed_on'])) {
                $this->set_daily_df_latest_activity(
                    $rows[$df_id],
                    $task['task_completed_on'],
                    'Task completed: ' . $task['task_name'],
                    $completed_by_name
                );
            }

            if ($is_active_df_task && $assigned_user_id > 0 && (int) $task['assigned_user_status'] === 1) {
                if (!isset($user_activity[$assigned_user_id])) {
                    $user_activity[$assigned_user_id] = [
                        'user_id' => $assigned_user_id,
                        'user_name' => $assigned_name,
                        'departments' => [],
                        'assigned_df_ids' => [],
                        'total_assigned_tasks' => 0,
                        'open_assigned_tasks' => 0,
                        'delayed_open_tasks' => 0,
                        'max_delay_days' => 0,
                        'today_assignment_count' => 0,
                        'today_completion_count' => 0,
                        'today_remark_count' => 0,
                        'today_working_task_count' => 0,
                        'has_worked_today' => false,
                        'latest_activity_on' => '',
                        'latest_activity_summary' => 'No visible progress update on selected date.',
                        'status_key' => 'idle',
                        'status_label' => 'No Visible Update',
                        '_today_work_task_ids' => [],
                        '_today_completion_ids' => [],
                        '_today_remark_ids' => [],
                        '_today_assignment_ids' => [],
                    ];
                }

                $user_activity[$assigned_user_id]['departments'][(string) $task['department']] = true;
                $user_activity[$assigned_user_id]['assigned_df_ids'][$df_id] = true;
                $user_activity[$assigned_user_id]['total_assigned_tasks']++;

                if (!$is_completed_currently) {
                    $user_activity[$assigned_user_id]['open_assigned_tasks']++;
                }

                if (!$is_completed_currently && (int) $task['on_hold'] === 0 && $end_date !== '' && $end_date < $current_date) {
                    $delay_days = (int) floor((strtotime($current_date) - strtotime($end_date)) / 86400);
                    $user_activity[$assigned_user_id]['delayed_open_tasks']++;
                    $user_activity[$assigned_user_id]['max_delay_days'] = max($user_activity[$assigned_user_id]['max_delay_days'], $delay_days);
                }

                if ($this->is_same_report_date($task['assigned_on'], $report_date)) {
                    $user_activity[$assigned_user_id]['_today_assignment_ids'][$task_id] = true;
                    $this->set_daily_df_user_latest_activity(
                        $user_activity[$assigned_user_id],
                        $task['assigned_on'],
                        'Received task: ' . $task['task_name']
                    );
                }

                if ($this->is_same_report_date($task['task_completed_on'], $report_date)) {
                    $user_activity[$assigned_user_id]['_today_completion_ids'][$task_id] = true;
                    $user_activity[$assigned_user_id]['_today_work_task_ids'][$task_id] = true;
                    $user_activity[$assigned_user_id]['has_worked_today'] = true;
                    $this->set_daily_df_user_latest_activity(
                        $user_activity[$assigned_user_id],
                        $task['task_completed_on'],
                        'Completed task: ' . $task['task_name']
                    );
                }

                if ($this->is_same_report_date($task['taskupdatedontime'], $report_date) && trim((string) $task['remarks']) !== '') {
                    $user_activity[$assigned_user_id]['_today_remark_ids'][$task_id] = true;
                    $user_activity[$assigned_user_id]['_today_work_task_ids'][$task_id] = true;
                    $user_activity[$assigned_user_id]['has_worked_today'] = true;
                    $this->set_daily_df_user_latest_activity(
                        $user_activity[$assigned_user_id],
                        $task['taskupdatedontime'],
                        'Updated progress on: ' . $task['task_name']
                    );
                }
            }

            if ($this->is_same_report_date($task['assigned_on'], $report_date)) {
                $rows[$df_id]['_today_assigned_ids'][$task_id] = true;
                $rows[$df_id]['_today_touch_ids'][$task_id] = true;
                $rows[$df_id]['has_movement_today'] = true;
                $movement_breakdown['assignments']++;
                $this->increment_daily_df_department_activity(
                    $rows[$df_id],
                    $department_activity,
                    (string) $task['department']
                );
                $this->append_daily_df_event($rows[$df_id]['today_events'], [
                    'time' => (string) $task['assigned_on'],
                    'type' => 'Assigned',
                    'task_name' => (string) $task['task_name'],
                    'department' => (string) $task['department'],
                    'owner' => $assigned_name,
                    'message' => 'Task assigned for execution.'
                ]);
            }

            if ($this->is_same_report_date($task['task_completed_on'], $report_date)) {
                $rows[$df_id]['_today_completed_ids'][$task_id] = true;
                $rows[$df_id]['_today_touch_ids'][$task_id] = true;
                $rows[$df_id]['has_movement_today'] = true;
                $movement_breakdown['completed']++;
                $this->increment_daily_df_department_activity(
                    $rows[$df_id],
                    $department_activity,
                    (string) $task['department']
                );
                if ($end_date !== '' && $task['task_completed_on'] > ($end_date . ' 23:59:59')) {
                    $rows[$df_id]['closed_late_today_count']++;
                }
                $this->append_daily_df_event($rows[$df_id]['today_events'], [
                    'time' => (string) $task['task_completed_on'],
                    'type' => 'Completed',
                    'task_name' => (string) $task['task_name'],
                    'department' => (string) $task['department'],
                    'owner' => $completed_by_name,
                    'message' => 'Task closed successfully.'
                ]);
            }

            if ($this->is_same_report_date($task['taskupdatedontime'], $report_date) && trim((string) $task['remarks']) !== '') {
                $rows[$df_id]['_today_remark_ids'][$task_id] = true;
                $rows[$df_id]['_today_touch_ids'][$task_id] = true;
                $rows[$df_id]['has_movement_today'] = true;
                $movement_breakdown['updates']++;
                $this->increment_daily_df_department_activity(
                    $rows[$df_id],
                    $department_activity,
                    (string) $task['department']
                );
                $this->append_daily_df_event($rows[$df_id]['today_events'], [
                    'time' => (string) $task['taskupdatedontime'],
                    'type' => 'Progress Note',
                    'task_name' => (string) $task['task_name'],
                    'department' => (string) $task['department'],
                    'owner' => $assigned_name,
                    'message' => trim((string) $task['remarks'])
                ]);
            }
        }

        foreach ($history_rows as $history) {
            $df_id = (int) $history['df_id'];
            $task_id = (int) $history['task_record_id'];
            if (!isset($rows[$df_id])) {
                continue;
            }

            $history_owner = $this->format_person_name(
                $history['added_title'],
                $history['added_first_name'],
                $history['added_last_name']
            );
            $history_owner = $history_owner !== '' ? $history_owner : 'Team Update';

            $rows[$df_id]['_today_remark_ids'][$task_id] = true;
            $rows[$df_id]['_today_touch_ids'][$task_id] = true;
            $rows[$df_id]['has_movement_today'] = true;
            $this->increment_daily_df_department_activity(
                $rows[$df_id],
                $department_activity,
                (string) $history['department']
            );
            $this->append_daily_df_event($rows[$df_id]['today_events'], [
                'time' => (string) $history['added_on'],
                'type' => 'Remark Trail',
                'task_name' => (string) $history['task_name'],
                'department' => (string) $history['department'],
                'owner' => $history_owner,
                'message' => trim((string) $history['remarks'])
            ]);
            $this->set_daily_df_latest_activity(
                $rows[$df_id],
                $history['added_on'],
                'Remark trail updated: ' . $history['task_name'],
                $history_owner
            );

            $history_user_id = (int) $history['added_by'];
            if ($history_user_id > 0 && isset($user_activity[$history_user_id])) {
                $user_activity[$history_user_id]['_today_remark_ids'][$task_id . '_' . strtotime($history['added_on'])] = true;
                $user_activity[$history_user_id]['_today_work_task_ids'][$task_id] = true;
                $user_activity[$history_user_id]['has_worked_today'] = true;
                $this->set_daily_df_user_latest_activity(
                    $user_activity[$history_user_id],
                    $history['added_on'],
                    'Added remark trail on: ' . $history['task_name']
                );
            }
        }

        foreach ($ticket_rows as $ticket) {
            $df_id = (int) $ticket['df_id'];
            if (!isset($rows[$df_id])) {
                continue;
            }

            $rows[$df_id]['open_ticket_count']++;
            if (!in_array($ticket['help_ticket_no'], $rows[$df_id]['ticket_numbers'], true) && count($rows[$df_id]['ticket_numbers']) < 4) {
                $rows[$df_id]['ticket_numbers'][] = (string) $ticket['help_ticket_no'];
            }

            if ($this->is_same_report_date($ticket['added_on'], $report_date)) {
                $ticket_id = (int) $ticket['id'];
                $rows[$df_id]['_today_ticket_ids'][$ticket_id] = true;
                $rows[$df_id]['has_movement_today'] = true;
                $movement_breakdown['tickets']++;
                $this->increment_daily_df_department_activity(
                    $rows[$df_id],
                    $department_activity,
                    (string) $ticket['department']
                );
                $this->append_daily_df_event($rows[$df_id]['today_events'], [
                    'time' => (string) $ticket['added_on'],
                    'type' => 'Open Ticket',
                    'task_name' => (string) $ticket['task_name'],
                    'department' => (string) $ticket['department'],
                    'owner' => 'Help Ticket',
                    'message' => 'Open ticket ' . $ticket['help_ticket_no'] . ' raised.'
                ]);
                $this->set_daily_df_latest_activity(
                    $rows[$df_id],
                    $ticket['added_on'],
                    'Open help ticket raised for ' . $ticket['task_name'],
                    'Help Ticket'
                );
            }
        }

        $final_rows = [];
        foreach ($rows as $row) {
            $row['today_completed_count'] = count($row['_today_completed_ids']);
            $row['today_touched_count'] = count($row['_today_touch_ids']);
            $row['today_assignment_count'] = count($row['_today_assigned_ids']);
            $row['today_remark_count'] = count($row['_today_remark_ids']);
            $row['today_new_ticket_count'] = count($row['_today_ticket_ids']);
            $row['completion_pct'] = ($row['total_tasks'] > 0) ? (int) round(($row['completed_tasks'] * 100) / $row['total_tasks']) : 0;
            $row['no_update_today'] = (!$row['has_movement_today'] && $row['df_status'] == 0 && $row['on_hold'] == 0);

            if ($row['df_status'] == 1 && $this->is_same_report_date($row['completed_on'], $report_date)) {
                $row['status_key'] = 'closed_today';
                $row['status_label'] = 'Closed On Selected Date';
            } elseif ($row['on_hold'] == 1) {
                $row['status_key'] = 'on_hold';
                $row['status_label'] = 'DF On Hold';
            } elseif ($row['open_delayed_count'] > 0 && !$row['has_movement_today']) {
                $row['status_key'] = 'critical';
                $row['status_label'] = 'Delayed With No Update';
            } elseif ($row['open_delayed_count'] > 0) {
                $row['status_key'] = 'delayed';
                $row['status_label'] = 'Delayed But Moving';
            } elseif ($row['has_movement_today']) {
                $row['status_key'] = 'updated';
                $row['status_label'] = 'Movement Captured';
            } else {
                $row['status_key'] = 'stalled';
                $row['status_label'] = 'No Update';
            }

            usort($row['today_events'], function ($left, $right) {
                return strtotime($right['time']) - strtotime($left['time']);
            });

            usort($row['delayed_tasks'], function ($left, $right) {
                return $right['delay_days'] - $left['delay_days'];
            });

            $row['risk_score'] = ($row['open_delayed_count'] * 10)
                + ($row['open_ticket_count'] * 4)
                + ($row['no_update_today'] ? 6 : 0)
                + (($row['completion_pct'] < 50 && $row['df_status'] == 0) ? 2 : 0);

            unset(
                $row['_today_touch_ids'],
                $row['_today_completed_ids'],
                $row['_today_assigned_ids'],
                $row['_today_remark_ids'],
                $row['_today_ticket_ids']
            );

            $final_rows[] = $row;
        }

        usort($final_rows, function ($left, $right) {
            if ($left['risk_score'] !== $right['risk_score']) {
                return $right['risk_score'] - $left['risk_score'];
            }
            if ($left['today_touched_count'] !== $right['today_touched_count']) {
                return $right['today_touched_count'] - $left['today_touched_count'];
            }
            return strcmp($left['df_no'], $right['df_no']);
        });

        $summary = $this->get_empty_daily_df_summary();
        $user_summary = $this->get_empty_daily_df_user_summary();
        foreach ($final_rows as $row) {
            if ($row['df_status'] == 0) {
                $summary['active_df_count']++;
            }
            if ($row['status_key'] === 'closed_today') {
                $summary['closed_today_df_count']++;
            }
            if ($row['on_hold'] == 1) {
                $summary['on_hold_df_count']++;
            }
            if ($row['has_movement_today']) {
                $summary['movement_df_count']++;
            }
            if ($row['no_update_today']) {
                $summary['no_update_df_count']++;
            }
            if ($row['risk_score'] >= 20) {
                $summary['high_risk_df_count']++;
            }

            $summary['completed_task_count'] += $row['today_completed_count'];
            $summary['touched_task_count'] += $row['today_touched_count'];
            $summary['open_delayed_task_count'] += $row['open_delayed_count'];
            $summary['open_ticket_count'] += $row['open_ticket_count'];
            $summary['new_ticket_count'] += $row['today_new_ticket_count'];
        }

        $final_user_activity = [];
        foreach ($user_activity as $user_row) {
            $user_row['assigned_df_count'] = count($user_row['assigned_df_ids']);
            $user_row['today_assignment_count'] = count($user_row['_today_assignment_ids']);
            $user_row['today_completion_count'] = count($user_row['_today_completion_ids']);
            $user_row['today_remark_count'] = count($user_row['_today_remark_ids']);
            $user_row['today_working_task_count'] = count($user_row['_today_work_task_ids']);
            $user_row['department_label'] = implode(', ', array_keys($user_row['departments']));

            if ($user_row['has_worked_today']) {
                $user_row['status_key'] = 'active';
                $user_row['status_label'] = 'Working Today';
            } else {
                $user_row['status_key'] = 'idle';
                $user_row['status_label'] = 'No Visible Update';
            }

            $user_summary['users_with_active_df_tasks']++;
            $user_summary['total_open_assigned_tasks'] += $user_row['open_assigned_tasks'];
            $user_summary['total_overdue_open_tasks'] += $user_row['delayed_open_tasks'];

            if ($user_row['has_worked_today']) {
                $user_summary['users_working_today']++;
            } else {
                $user_summary['users_no_update_today']++;
            }

            unset(
                $user_row['departments'],
                $user_row['assigned_df_ids'],
                $user_row['_today_work_task_ids'],
                $user_row['_today_completion_ids'],
                $user_row['_today_remark_ids'],
                $user_row['_today_assignment_ids']
            );

            $final_user_activity[] = $user_row;
        }

        usort($final_user_activity, function ($left, $right) {
            if ((int) $left['has_worked_today'] !== (int) $right['has_worked_today']) {
                return (int) $right['has_worked_today'] - (int) $left['has_worked_today'];
            }
            if ($left['today_working_task_count'] !== $right['today_working_task_count']) {
                return $right['today_working_task_count'] - $left['today_working_task_count'];
            }
            if ($left['delayed_open_tasks'] !== $right['delayed_open_tasks']) {
                return $right['delayed_open_tasks'] - $left['delayed_open_tasks'];
            }
            return strcmp($left['user_name'], $right['user_name']);
        });

        $top_active_users = array_values(array_slice(array_filter($final_user_activity, function ($user_row) {
            return !empty($user_row['has_worked_today']);
        }), 0, 8));

        $idle_users = array_values(array_slice(array_filter($final_user_activity, function ($user_row) {
            return empty($user_row['has_worked_today']);
        }), 0, 8));

        arsort($department_activity);
        $department_chart = [];
        foreach ($department_activity as $department_name => $count) {
            $department_chart[] = [
                'department' => $department_name,
                'count' => $count
            ];
        }

        $top_attention = array_slice($final_rows, 0, 6);
        $top_active_df = null;
        foreach ($final_rows as $row) {
            if ($top_active_df === null || $row['today_touched_count'] > $top_active_df['today_touched_count']) {
                $top_active_df = $row;
            }
        }

        $top_department = null;
        if (!empty($department_chart)) {
            $top_department = $department_chart[0];
        }

        return [
            'report_date' => $report_date,
            'scope' => $scope,
            'summary' => $summary,
            'user_summary' => $user_summary,
            'department_activity' => $department_chart,
            'movement_breakdown' => $movement_breakdown,
            'user_activity' => $final_user_activity,
            'top_active_users' => $top_active_users,
            'idle_users' => $idle_users,
            'rows' => $final_rows,
            'top_attention' => $top_attention,
            'top_active_df' => $top_active_df,
            'top_department' => $top_department
        ];
    }

    public function get_daily_planned_task_report($report_date, $department_ids = [], $user_id = 0, $df_id = 0) {
        $report_date = $this->normalize_report_date($report_date);
        if (!is_array($department_ids)) {
            $department_ids = explode(',', (string) $department_ids);
        }
        $department_ids = array_values(array_unique(array_filter(array_map('intval', $department_ids))));
        $user_id = max(0, (int) $user_id);
        $df_id = max(0, (int) $df_id);

        $this->db->select('
            td.id,
            td.df_id,
            td.start_date,
            td.end_date,
            td.task_status,
            td.task_completed_on,
            IFNULL(td.on_hold, 0) as task_on_hold,
            dr.df_no,
            dr.df_description,
            IFNULL(dr.on_hold, 0) as df_on_hold,
            tm.task_name,
            d.department_id,
            d.department,
            u.user_id,
            u.title,
            u.first_name,
            u.last_name,
            IFNULL(po.company_name, "") as company_name,
            IFNULL(po.basic_machine, "") as machine_name
        ');
        $this->db->from('task_department_wise_scheduling td');
        $this->db->join('df_release dr', 'dr.id = td.df_id', 'inner');
        $this->db->join('task_management tm', 'tm.task_id = td.taskid', 'left');
        $this->db->join('departments d', 'd.department_id = td.department_id', 'left');
        $this->db->join('system_users u', 'u.user_id = td.assigned_user', 'left');
        $this->db->join(
            '(SELECT p1.* FROM poreceived p1 INNER JOIN (SELECT MAX(id) latest_po_id FROM poreceived GROUP BY df_id) p2 ON p2.latest_po_id = p1.id) po',
            'po.df_id = dr.id',
            'left',
            false
        );
        $this->db->where('dr.df_status', 0);
        $this->db->where('DATE(td.start_date) <=', $report_date);
        $this->db->where('DATE(td.end_date) >=', $report_date);
        $this->db->where('td.taskid >', 0);

        if (!empty($department_ids)) {
            $this->db->where_in('td.department_id', $department_ids);
        }
        if ($user_id > 0) {
            $this->db->where('td.assigned_user', $user_id);
        }
        if ($df_id > 0) {
            $this->db->where('td.df_id', $df_id);
        }

        $this->db->order_by('td.end_date', 'ASC');
        $this->db->order_by('dr.df_no', 'ASC');
        $this->db->order_by('tm.sortorder', 'ASC');
        $rows = $this->db->get()->result_array();

        $summary = [
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'pending_tasks' => 0,
            'overdue_tasks' => 0,
            'on_hold_tasks' => 0,
            'df_count' => 0,
            'user_count' => 0,
            'completion_pct' => 0
        ];
        $df_ids = [];
        $user_ids = [];
        $today = date('Y-m-d');

        foreach ($rows as &$row) {
            $row['user_name'] = $this->format_person_name(
                isset($row['title']) ? $row['title'] : '',
                isset($row['first_name']) ? $row['first_name'] : '',
                isset($row['last_name']) ? $row['last_name'] : ''
            );
            if ($row['user_name'] === '') {
                $row['user_name'] = 'Unassigned';
            } else {
                $row['user_name'] = $this->format_report_sentence_case($row['user_name']);
            }
            $row['department'] = $this->format_report_sentence_case($row['department']);

            $is_completed = (int) $row['task_status'] === 1;
            $is_on_hold = (int) $row['task_on_hold'] === 1 || (int) $row['df_on_hold'] === 1;
            $end_date = $this->normalize_report_date($row['end_date']);

            if ($is_completed) {
                $row['status_key'] = 'completed';
                $row['status_label'] = 'Completed';
                $summary['completed_tasks']++;
            } elseif ($is_on_hold) {
                $row['status_key'] = 'on_hold';
                $row['status_label'] = 'On Hold';
                $summary['on_hold_tasks']++;
                $summary['pending_tasks']++;
            } elseif ($end_date !== '' && $end_date < $today) {
                $row['status_key'] = 'overdue';
                $row['status_label'] = 'Overdue';
                $summary['overdue_tasks']++;
                $summary['pending_tasks']++;
            } else {
                $row['status_key'] = 'planned';
                $row['status_label'] = 'Planned';
                $summary['pending_tasks']++;
            }

            $df_ids[(int) $row['df_id']] = true;
            if ((int) $row['user_id'] > 0) {
                $user_ids[(int) $row['user_id']] = true;
            }
        }
        unset($row);

        $summary['total_tasks'] = count($rows);
        $summary['df_count'] = count($df_ids);
        $summary['user_count'] = count($user_ids);
        $summary['completion_pct'] = $summary['total_tasks'] > 0
            ? round(($summary['completed_tasks'] / $summary['total_tasks']) * 100)
            : 0;

        return [
            'report_date' => $report_date,
            'summary' => $summary,
            'rows' => $rows
        ];
    }

    public function get_daily_planned_task_filters() {
        $departments = $this->db
            ->select('MIN(department_id) as department_id, GROUP_CONCAT(department_id ORDER BY department_id) as department_ids, department', false)
            ->from('departments')
            ->where('department IS NOT NULL', null, false)
            ->where('TRIM(department) !=', '')
            ->group_by('UPPER(TRIM(department))', false)
            ->order_by('department', 'ASC')
            ->get()
            ->result_array();

        foreach ($departments as &$department) {
            $department['department_name'] = $this->format_report_sentence_case($department['department']);
        }
        unset($department);

        $users = $this->db
            ->select('user_id, department_id, title, first_name, last_name')
            ->from('system_users')
            ->where('user_status', 1)
            ->order_by('first_name', 'ASC')
            ->order_by('last_name', 'ASC')
            ->get()
            ->result_array();

        foreach ($users as &$user) {
            $user['user_name'] = $this->format_person_name(
                $user['title'],
                $user['first_name'],
                $user['last_name']
            );
            $user['user_name'] = $this->format_report_sentence_case($user['user_name']);
        }
        unset($user);

        $running_dfs = $this->db
            ->select('id, df_no, df_description')
            ->from('df_release')
            ->where('df_status', 0)
            ->order_by('df_no', 'ASC')
            ->get()
            ->result_array();

        return [
            'departments' => $departments,
            'users' => $users,
            'running_dfs' => $running_dfs
        ];
    }

    private function format_report_sentence_case($value) {
        $value = preg_replace('/\s+/', ' ', trim((string) $value));
        return $value === '' ? '' : ucwords(strtolower($value));
    }

    private function get_daily_df_base_rows($report_date, $scope) {
        $where_sql = 'dr.df_status = 0 OR DATE(dr.completed_on) = ?';
        if ($scope === 'running') {
            $where_sql = 'dr.df_status = 0';
        } elseif ($scope === 'closed_today') {
            $where_sql = 'dr.df_status = 1 AND DATE(dr.completed_on) = ?';
        }

        $params = [];
        if (strpos($where_sql, '?') !== false) {
            $params[] = $report_date;
        }

        $sql = "
            SELECT
                dr.id,
                dr.df_no,
                dr.df_description,
                dr.added_on,
                dr.completed_on,
                dr.df_status,
                IFNULL(dr.on_hold, 0) as on_hold,
                IFNULL(po.company_name, '') as company_name,
                IFNULL(po.pono, '') as po_no,
                IFNULL(po.basic_machine, '') as machine_name,
                creator.title as creator_title,
                creator.first_name as creator_first_name,
                creator.last_name as creator_last_name,
                marketing.title as marketing_title,
                marketing.first_name as marketing_first_name,
                marketing.last_name as marketing_last_name
            FROM df_release dr
            LEFT JOIN (
                SELECT p1.*
                FROM poreceived p1
                INNER JOIN (
                    SELECT MAX(id) as latest_po_id
                    FROM poreceived
                    GROUP BY df_id
                ) latest_po ON latest_po.latest_po_id = p1.id
            ) po ON po.df_id = dr.id
            LEFT JOIN system_users creator ON creator.user_id = dr.added_by
            LEFT JOIN system_users marketing ON marketing.user_id = po.added_by
            WHERE {$where_sql}
            ORDER BY dr.df_status ASC, dr.on_hold ASC, dr.id DESC
        ";

        return $this->db->query($sql, $params)->result_array();
    }

    private function get_daily_df_task_rows($df_ids) {
        if (empty($df_ids)) {
            return [];
        }

        $this->db->select('
            td.id,
            td.df_id,
            td.taskid,
            td.department_id,
            td.assigned_user,
            td.start_date,
            td.end_date,
            td.task_status,
            td.remarks,
            td.taskupdatedontime,
            td.task_completed_on,
            td.task_completed_by,
            td.assigned_on,
            IFNULL(td.on_hold, 0) as on_hold,
            tm.task_name,
            tm.sortorder,
            d.department,
            assignee.title as assigned_title,
            assignee.first_name as assigned_first_name,
            assignee.last_name as assigned_last_name,
            assignee.user_status as assigned_user_status,
            completer.title as completed_title,
            completer.first_name as completed_first_name,
            completer.last_name as completed_last_name
        ');
        $this->db->from('task_department_wise_scheduling td');
        $this->db->join('task_management tm', 'tm.task_id = td.taskid', 'left');
        $this->db->join('departments d', 'd.department_id = td.department_id', 'left');
        $this->db->join('system_users assignee', 'assignee.user_id = td.assigned_user', 'left');
        $this->db->join('system_users completer', 'completer.user_id = td.task_completed_by', 'left');
        $this->db->where_in('td.df_id', $df_ids);
        $this->db->order_by('td.df_id', 'ASC');
        $this->db->order_by('tm.sortorder', 'ASC');
        $this->db->order_by('td.end_date', 'ASC');
        $this->db->order_by('td.id', 'ASC');
        return $this->db->get()->result_array();
    }

    private function get_daily_df_history_rows($df_ids, $report_date) {
        if (empty($df_ids)) {
            return [];
        }

        $this->db->select('
            h.recordid as task_record_id,
            td.df_id,
            h.remarks,
            h.added_on,
            h.added_by,
            tm.task_name,
            d.department,
            added_by.title as added_title,
            added_by.first_name as added_first_name,
            added_by.last_name as added_last_name
        ');
        $this->db->from('task_pending_status h');
        $this->db->join('task_department_wise_scheduling td', 'td.id = h.recordid', 'inner');
        $this->db->join('task_management tm', 'tm.task_id = td.taskid', 'left');
        $this->db->join('departments d', 'd.department_id = td.department_id', 'left');
        $this->db->join('system_users added_by', 'added_by.user_id = h.added_by', 'left');
        $this->db->where_in('td.df_id', $df_ids);
        $this->db->where('DATE(h.added_on)', $report_date);
        $this->db->order_by('h.added_on', 'DESC');
        return $this->db->get()->result_array();
    }

    private function get_daily_df_ticket_rows($df_ids) {
        if (empty($df_ids)) {
            return [];
        }

        $this->db->select('
            cts.id,
            cts.task_record_id,
            td.df_id,
            cts.help_ticket_no,
            cts.added_on,
            tm.task_name,
            d.department
        ');
        $this->db->from('communication_ticket_system cts');
        $this->db->join('task_department_wise_scheduling td', 'td.id = cts.task_record_id', 'inner');
        $this->db->join('task_management tm', 'tm.task_id = td.taskid', 'left');
        $this->db->join('departments d', 'd.department_id = td.department_id', 'left');
        $this->db->where_in('td.df_id', $df_ids);
        $this->db->where('cts.ticket_status', 0);
        $this->db->order_by('cts.added_on', 'DESC');
        return $this->db->get()->result_array();
    }

    private function get_empty_daily_df_summary() {
        return [
            'active_df_count' => 0,
            'closed_today_df_count' => 0,
            'movement_df_count' => 0,
            'no_update_df_count' => 0,
            'open_delayed_task_count' => 0,
            'open_ticket_count' => 0,
            'completed_task_count' => 0,
            'touched_task_count' => 0,
            'new_ticket_count' => 0,
            'on_hold_df_count' => 0,
            'high_risk_df_count' => 0
        ];
    }

    private function get_empty_daily_df_user_summary() {
        return [
            'users_with_active_df_tasks' => 0,
            'users_working_today' => 0,
            'users_no_update_today' => 0,
            'total_open_assigned_tasks' => 0,
            'total_overdue_open_tasks' => 0
        ];
    }

    private function normalize_daily_df_scope($scope) {
        $allowed = ['active', 'running', 'closed_today'];
        return in_array($scope, $allowed, true) ? $scope : 'active';
    }

    private function normalize_report_date($date_value) {
        if ($this->is_valid_datetime($date_value)) {
            return date('Y-m-d', strtotime($date_value));
        }
        return '';
    }

    private function is_valid_datetime($value) {
        if (empty($value)) {
            return false;
        }
        $value = trim((string) $value);
        if ($value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return false;
        }
        return strtotime($value) !== false;
    }

    private function is_same_report_date($datetime_value, $report_date) {
        if (!$this->is_valid_datetime($datetime_value)) {
            return false;
        }
        return date('Y-m-d', strtotime($datetime_value)) === $report_date;
    }

    private function format_person_name($title, $first_name, $last_name) {
        $parts = [];
        foreach ([$title, $first_name, $last_name] as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $parts[] = ucwords(strtolower($value));
            }
        }
        return trim(implode(' ', $parts));
    }

    private function append_daily_df_event(&$events, $event) {
        if (count($events) >= 8) {
            return;
        }
        $events[] = $event;
    }

    private function increment_daily_df_department_activity(&$row, &$global_activity, $department_name) {
        $department_name = trim((string) $department_name);
        if ($department_name === '') {
            $department_name = 'Unknown';
        }

        if (!isset($row['today_department_counts'][$department_name])) {
            $row['today_department_counts'][$department_name] = 0;
        }
        $row['today_department_counts'][$department_name]++;

        if (!isset($global_activity[$department_name])) {
            $global_activity[$department_name] = 0;
        }
        $global_activity[$department_name]++;
    }

    private function set_daily_df_latest_activity(&$row, $activity_time, $summary, $actor_name) {
        if (!$this->is_valid_datetime($activity_time)) {
            return;
        }

        if ($row['latest_activity_on'] === '' || strtotime($activity_time) >= strtotime($row['latest_activity_on'])) {
            $row['latest_activity_on'] = $activity_time;
            $row['latest_activity_summary'] = $summary;
            $row['latest_activity_actor'] = trim((string) $actor_name) !== '' ? $actor_name : 'System';
        }
    }

    private function set_daily_df_user_latest_activity(&$row, $activity_time, $summary) {
        if (!$this->is_valid_datetime($activity_time)) {
            return;
        }

        if ($row['latest_activity_on'] === '' || strtotime($activity_time) >= strtotime($row['latest_activity_on'])) {
            $row['latest_activity_on'] = $activity_time;
            $row['latest_activity_summary'] = $summary;
        }
    }
}
