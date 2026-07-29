<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Df_Report_model extends CI_Model {

    public function get_delay_data($filters = []) {
        $this->db->select("
            tdws.id,
            tdws.assigned_user,
            tdws.start_date,
            tdws.end_date,
            tdws.task_completed_on,
            tdws.task_status,
            tdws.remarks,
            tm.task_name,
            dr.df_no,
            dr.df_description,
            d.department,
            d.department_id,
            tdws.df_id, 
            po.order_value,
            CONCAT(assignee.first_name, ' ', assignee.last_name) as assigned_user_name,
            
            -- NEW: Get approver/team leader name
            CONCAT(approver.first_name, ' ', approver.last_name) as approver_name,
            CASE 
                WHEN tdws.task_status = 2 THEN 'approver' 
                ELSE 'assignee' 
            END as bottleneck_type,

            CASE 
                WHEN tdws.task_status = 1 AND CAST(tdws.task_completed_on AS DATE) > tdws.end_date 
                THEN DATEDIFF(CAST(tdws.task_completed_on AS DATE), tdws.end_date)
                WHEN tdws.task_status IN (0, 2) AND CURDATE() > tdws.end_date
                THEN DATEDIFF(CURDATE(), tdws.end_date)
                ELSE 0 
            END as delay_days
        ", FALSE);

        $this->db->from('task_department_wise_scheduling AS tdws');
        $this->db->join('df_release AS dr', 'dr.id = tdws.df_id', 'left');
        $this->db->join('departments AS d', 'd.department_id = tdws.department_id', 'left');
        $this->db->join('system_users AS assignee', 'assignee.user_id = tdws.assigned_user', 'left');
        $this->db->join('task_management AS tm', 'tm.task_id = tdws.taskid', 'left'); 
        $this->db->join('poreceived AS po', 'po.df_id = tdws.df_id', 'left');
        
        /* --- NEW JOINS for Bottleneck --- */
        $this->db->join('prestogroup_teams AS team', 'team.department_id = d.department_id AND team.status = 1', 'left');
        $this->db->join('system_users AS approver', 'approver.user_id = team.team_leader', 'left');
        /* --- END NEW JOINS --- */

        $this->db->where('tdws.df_id >', 0);

        // --- Conditional WHERE Clauses ---
        if (!empty($filters['show_all_pending'])) {
            $this->db->where('tdws.task_status', 0);
            $this->db->where('tdws.end_date <', date('Y-m-d'));
        } else {
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $this->db->where('tdws.end_date >=', $filters['start_date']);
                $this->db->where('tdws.end_date <=', $filters['end_date']);
            }
            $this->db->group_start();
                $this->db->where('(tdws.task_status = 1 AND CAST(tdws.task_completed_on AS DATE) > tdws.end_date)');
                $this->db->or_where('(tdws.task_status IN (0, 2) AND CURDATE() > tdws.end_date)');
            $this->db->group_end();
        }

        // --- Other Filters ---
        if (!empty($filters['department_id'])) {
            $this->db->where('tdws.department_id', $filters['department_id']);
        }
        if (!empty($filters['df_id'])) {
            $this->db->where('tdws.df_id', $filters['df_id']);
        }
        
        $this->db->group_by('tdws.id'); 
        $this->db->order_by('delay_days', 'DESC'); 

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_dfs() {
    $this->db->distinct();
    $this->db->select('dr.id as df_id, dr.df_no');
    $this->db->from('task_department_wise_scheduling AS tdws');
    $this->db->join('df_release AS dr', 'dr.id = tdws.df_id', 'inner');
    $this->db->where('tdws.df_id >', 0);
    $this->db->order_by('dr.df_no', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function get_all_departments() {
    $this->db->select('department_id, department');
    $this->db->from('departments');
    $this->db->where('status', 1);
    $this->db->where('business_loc_id',2);
    $this->db->order_by('department', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function get_completed_df_overview_rows($filters = array()) {
    $selected_fy = isset($filters['financial_year']) ? trim((string) $filters['financial_year']) : '';

    $po_subquery = "
      (
        SELECT
          p.df_id,
          MAX(IFNULL(p.pono, '')) as po_no,
          MAX(IFNULL(p.company_name, '')) as customer_name,
          MAX(IFNULL(p.podate, '')) as po_date,
          MAX(IFNULL(p.order_value, 0)) as order_value,
          MAX(TRIM(CONCAT(IFNULL(su.title, ''), ' ', IFNULL(su.first_name, ''), ' ', IFNULL(su.last_name, '')))) as marketing_person
        FROM poreceived p
        LEFT JOIN system_users su ON su.user_id = p.added_by
        GROUP BY p.df_id
      ) po_info
    ";

    $this->db->select("
      df.id as df_id,
      df.df_no,
      IFNULL(df.df_description, '') as df_description,
      DATE(df.added_on) as df_release_date,
      IFNULL(po_info.po_no, '') as po_no,
      IFNULL(po_info.customer_name, '') as customer_name,
      IFNULL(po_info.po_date, '') as po_date,
      IFNULL(po_info.order_value, 0) as order_value,
      IFNULL(po_info.marketing_person, '') as marketing_person,
      MIN(tdws.start_date) as project_start_date,
      MAX(CASE WHEN tdws.taskid = 103 THEN tdws.end_date END) as dispatch_planned_closure,
      MAX(tdws.end_date) as fallback_planned_closure,
      DATE(MAX(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
          THEN tdws.task_completed_on
        END
      )) as actual_completion_date,
      COUNT(DISTINCT tdws.id) as total_tasks,
      SUM(CASE WHEN tdws.task_status = 1 THEN 1 ELSE 0 END) as completed_tasks,
      SUM(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
            AND DATE(tdws.task_completed_on) > tdws.end_date
          THEN 1
          ELSE 0
        END
      ) as delayed_task_count,
      SUM(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
            AND DATE(tdws.task_completed_on) > tdws.end_date
          THEN DATEDIFF(DATE(tdws.task_completed_on), tdws.end_date)
          ELSE 0
        END
      ) as task_delay_days_total,
      MAX(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
            AND DATE(tdws.task_completed_on) > tdws.end_date
          THEN DATEDIFF(DATE(tdws.task_completed_on), tdws.end_date)
          ELSE 0
        END
      ) as max_task_delay_days
    ", false);
    $this->db->from('df_release df');
    $this->db->join('task_department_wise_scheduling tdws', 'tdws.df_id = df.id AND tdws.taskid > 0 AND IFNULL(tdws.on_hold, 0) = 0', 'left');
    $this->db->join($po_subquery, 'po_info.df_id = df.id', 'left', false);
    $this->db->where('df.df_status', 1);
    $this->db->group_by('df.id');
    $this->db->having('actual_completion_date IS NOT NULL', null, false);

    if ($selected_fy !== '') {
      $years = explode('-', $selected_fy);
      if (count($years) === 2) {
        $fy_start = trim($years[0]) . '-04-01';
        $fy_end = trim($years[1]) . '-03-31';
        $this->db->having('actual_completion_date >=', $fy_start);
        $this->db->having('actual_completion_date <=', $fy_end);
      }
    }

    $this->db->order_by('actual_completion_date', 'DESC');
    $this->db->order_by('df.df_no', 'ASC');

    return $this->db->get()->result_array();
  }

  public function get_completed_df_delay_department_rows($df_ids = array()) {
    $df_ids = array_values(array_filter(array_map('intval', (array) $df_ids)));
    if (empty($df_ids)) {
      return array();
    }

    $this->db->select("
      tdws.df_id,
      tdws.department_id,
      IFNULL(d.department, 'Unmapped') as department,
      SUM(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
            AND DATE(tdws.task_completed_on) > tdws.end_date
          THEN DATEDIFF(DATE(tdws.task_completed_on), tdws.end_date)
          ELSE 0
        END
      ) as total_delay_days,
      SUM(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
            AND DATE(tdws.task_completed_on) > tdws.end_date
          THEN 1
          ELSE 0
        END
      ) as delayed_task_count
    ", false);
    $this->db->from('task_department_wise_scheduling tdws');
    $this->db->join('departments d', 'd.department_id = tdws.department_id', 'left');
    $this->db->where_in('tdws.df_id', $df_ids);
    $this->db->where('tdws.taskid >', 0);
    $this->db->where('IFNULL(tdws.on_hold, 0) = 0', null, false);
    $this->db->group_by(array('tdws.df_id', 'tdws.department_id'));
    $this->db->having('total_delay_days >', 0);
    $this->db->order_by('total_delay_days', 'DESC');

    return $this->db->get()->result_array();
  }

  public function get_completed_df_department_progress_rows($df_ids = array()) {
    $df_ids = array_values(array_filter(array_map('intval', (array) $df_ids)));
    if (empty($df_ids)) {
      return array();
    }

    $this->db->select("
      tdws.df_id,
      tdws.department_id,
      IFNULL(d.department, 'Unmapped') as department,
      MIN(tdws.start_date) as department_start_date,
      MAX(tdws.end_date) as department_planned_date,
      DATE(MAX(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
          THEN tdws.task_completed_on
        END
      )) as department_actual_date,
      COUNT(DISTINCT tdws.id) as total_tasks,
      SUM(CASE WHEN tdws.task_status = 1 THEN 1 ELSE 0 END) as completed_tasks,
      SUM(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
            AND DATE(tdws.task_completed_on) > tdws.end_date
          THEN 1
          ELSE 0
        END
      ) as delayed_task_count,
      SUM(
        CASE
          WHEN tdws.task_status = 1
            AND tdws.task_completed_on IS NOT NULL
            AND tdws.task_completed_on != '0000-00-00 00:00:00'
            AND DATE(tdws.task_completed_on) > tdws.end_date
          THEN DATEDIFF(DATE(tdws.task_completed_on), tdws.end_date)
          ELSE 0
        END
      ) as total_task_delay_days
    ", false);
    $this->db->from('task_department_wise_scheduling tdws');
    $this->db->join('departments d', 'd.department_id = tdws.department_id', 'left');
    $this->db->where_in('tdws.df_id', $df_ids);
    $this->db->where('tdws.taskid >', 0);
    $this->db->where('IFNULL(tdws.on_hold, 0) = 0', null, false);
    $this->db->group_by(array('tdws.df_id', 'tdws.department_id'));
    $this->db->having('department_actual_date IS NOT NULL', null, false);
    $this->db->order_by('tdws.df_id', 'ASC');
    $this->db->order_by('total_task_delay_days', 'DESC');
    $this->db->order_by('department', 'ASC');

    return $this->db->get()->result_array();
  }

  public function get_open_tickets_for_tasks($filters = []) {
        
        // --- 1. Build the Subquery to get the task IDs ---
        $this->db->select('tdws.id');
        $this->db->from('task_department_wise_scheduling AS tdws');
        $this->db->join('departments AS d', 'd.department_id = tdws.department_id', 'left');
        $this->db->join('prestogroup_teams AS team', 'team.department_id = d.department_id AND team.status = 1', 'left');
        
        if (!empty($filters['show_all_pending'])) {
            $this->db->where('tdws.task_status', 0);
            $this->db->where('tdws.end_date <', date('Y-m-d'));
        } else {
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $this->db->where('tdws.end_date >=', $filters['start_date']);
                $this->db->where('tdws.end_date <=', $filters['end_date']);
            }
            $this->db->group_start();
                $this->db->where('(tdws.task_status = 1 AND CAST(tdws.task_completed_on AS DATE) > tdws.end_date)');
                $this->db->or_where('(tdws.task_status IN (0, 2) AND CURDATE() > tdws.end_date)');
            $this->db->group_end();
        }
        if (!empty($filters['department_id'])) {
            $this->db->where('tdws.department_id', $filters['department_id']);
        }
        if (!empty($filters['df_id'])) {
            $this->db->where('tdws.df_id', $filters['df_id']);
        }
        $this->db->group_by('tdws.id');
        
        $sub_query = $this->db->get_compiled_select();

        // --- 2. Build the Main Ticket Query ---
        $this->db->select("cts.task_record_id, cts.remarks, cts.help_ticket_no, cts.delay_reason, CONCAT(creator.first_name, ' ', creator.last_name) as created_by_name, CONCAT(pending.first_name, ' ', pending.last_name) as pending_with_name", FALSE);
        $this->db->from('communication_ticket_system AS cts');
        $this->db->join('system_users AS creator', 'creator.user_id = cts.user_id', 'left');
        $this->db->join('system_users AS pending', 'pending.user_id = cts.added_by', 'left');
        $this->db->where('cts.ticket_status', 0);
        $this->db->where("cts.task_record_id IN ($sub_query)", NULL, FALSE);
        
        $query = $this->db->get();
        $tickets = $query->result_array();
        
        $result = [];
        foreach ($tickets as $ticket) {
            $result[$ticket['task_record_id']] = $ticket;
        }
        return $result;
    }


  public function get_trend_stats($start_date, $end_date) {
        $this->db->select("
            COUNT(tdws.id) as total_tasks_closed_late,
            AVG(DATEDIFF(CAST(tdws.task_completed_on AS DATE), tdws.end_date)) as avg_delay,
            SUM( (po.order_value * (0.08 / 365)) * DATEDIFF(CAST(tdws.task_completed_on AS DATE), tdws.end_date) ) as total_loss
        ", FALSE);
        $this->db->from('task_department_wise_scheduling AS tdws');
        $this->db->join('poreceived AS po', 'po.df_id = tdws.df_id', 'left');
        $this->db->where('tdws.task_status', 1);
        $this->db->where('CAST(tdws.task_completed_on AS DATE) > tdws.end_date');
        $this->db->where('CAST(tdws.task_completed_on AS DATE) >=', $start_date);
        $this->db->where('CAST(tdws.task_completed_on AS DATE) <=', $end_date);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_at_risk_tasks() {
        $this->db->select("
            dr.df_no, 
            tm.task_name, 
            tdws.end_date, 
            d.department, 
            CONCAT(assignee.first_name, ' ', assignee.last_name) as assigned_user_name
        ", FALSE);
        $this->db->from('task_department_wise_scheduling AS tdws');
        $this->db->join('df_release AS dr', 'dr.id = tdws.df_id', 'left');
        $this->db->join('task_management AS tm', 'tm.task_id = tdws.taskid', 'left');
        $this->db->join('departments AS d', 'd.department_id = tdws.department_id', 'left');
        $this->db->join('system_users AS assignee', 'assignee.user_id = tdws.assigned_user', 'left');
        
        $this->db->where('tdws.task_status IN (0, 2)');
        $this->db->where('tdws.end_date >=', date('Y-m-d')); 
        $this->db->where('tdws.end_date <=', date('Y-m-d', strtotime('+7 days'))); 
        
        $this->db->order_by('tdws.end_date', 'ASC');
        $this->db->limit(5); 
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_root_cause_stats($filters = []) {
        
        // --- 1. Build the Subquery to get the delayed task IDs ---
        $this->db->select('tdws.id');
        $this->db->from('task_department_wise_scheduling AS tdws');
        $this->db->join('departments AS d', 'd.department_id = tdws.department_id', 'left');
        $this->db->join('prestogroup_teams AS team', 'team.department_id = d.department_id AND team.status = 1', 'left');
        
        if (!empty($filters['show_all_pending'])) {
            $this->db->where('tdws.task_status', 0);
            $this->db->where('tdws.end_date <', date('Y-m-d'));
        } else {
            if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
                $this->db->where('tdws.end_date >=', $filters['start_date']);
                $this->db->where('tdws.end_date <=', $filters['end_date']);
            }
            $this->db->group_start();
                $this->db->where('(tdws.task_status = 1 AND CAST(tdws.task_completed_on AS DATE) > tdws.end_date)');
                $this->db->or_where('(tdws.task_status IN (0, 2) AND CURDATE() > tdws.end_date)');
            $this->db->group_end();
        }
        if (!empty($filters['department_id'])) {
            $this->db->where('tdws.department_id', $filters['department_id']);
        }
        if (!empty($filters['df_id'])) {
            $this->db->where('tdws.df_id', $filters['df_id']);
        }
        $this->db->group_by('tdws.id');
        $sub_query = $this->db->get_compiled_select();

        // --- 2. Build Main Query ---
        $this->db->select("
            CASE 
                WHEN cts.delay_reason IS NULL OR cts.delay_reason = '' THEN 'Not Specified' 
                ELSE cts.delay_reason 
            END as reason, 
            COUNT(cts.id) as reason_count
        ", FALSE);
        $this->db->from('communication_ticket_system AS cts');
        $this->db->where("cts.task_record_id IN ($sub_query)", NULL, FALSE);
        $this->db->group_by('reason');
        $this->db->order_by('reason_count', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }



/**
     * UPDATED: Fetches user performance statistics.
     * This fixes the bug where on-time tasks were counted as 'tasks_delayed_closed'.
     *
     * @param array $filters (Optional) Can contain 'start_date', 'end_date', 'department_id'
     * @return array A list of users with their performance metrics.
     */
  public function get_user_performance_data($filters = []) {

    // Date filter only for completed tasks
    $completed_date_where = "";
    if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
        $completed_date_where = " AND CAST(tdws.task_completed_on AS DATE) BETWEEN " .
            $this->db->escape($filters['start_date']) . " AND " .
            $this->db->escape($filters['end_date']);
    }

    // Optional filter for department
    $dept_where = "";
    if (!empty($filters['department_id'])) {
        $dept_where = " AND d.department_id = " . $this->db->escape($filters['department_id']);
    }

    $query = $this->db->query("
        SELECT
            u.user_id,
            CONCAT(u.first_name, ' ', u.last_name) AS user_name,
            us.user_role AS designation,
            u.profile_image AS profile_pic,
            d.department_id,
            d.department,

            COUNT(
                DISTINCT CASE 
                    WHEN tdws.df_id > 0
                    AND (
                        (
                            tdws.task_status = 1
                            $completed_date_where
                        )
                        OR (
                            tdws.task_status IN (0, 2)
                            AND IFNULL(tdws.on_hold, 0) != 1
                        )
                    )
                    THEN tdws.df_id 
                    ELSE NULL 
                END
            ) AS total_df_assigned,

            COUNT(
                DISTINCT CASE 
                    WHEN tdws.df_id > 0
                    AND tdws.task_status IN (0, 2)
                    AND IFNULL(tdws.on_hold, 0) != 1
                    AND IFNULL(df.df_status, 0) = 0
                    THEN tdws.df_id 
                    ELSE NULL 
                END
            ) AS active_df_count,

            SUM(
                CASE 
                    WHEN tdws.task_status = 1 
                    $completed_date_where
                    THEN 1 
                    ELSE 0 
                END
            ) AS total_completed,

            SUM(
                CASE 
                    WHEN tdws.task_status = 1 
                    AND CAST(tdws.task_completed_on AS DATE) <= tdws.end_date
                    $completed_date_where
                    THEN 1 
                    ELSE 0 
                END
            ) AS tasks_on_time,

            SUM(
                CASE 
                    WHEN tdws.task_status = 1 
                    AND CAST(tdws.task_completed_on AS DATE) > tdws.end_date
                    $completed_date_where
                    THEN 1 
                    ELSE 0 
                END
            ) AS tasks_delayed_closed,

            SUM(
                CASE 
                    WHEN tdws.task_status IN (0, 2) 
                    AND CURDATE() > tdws.end_date 
                    THEN 1 
                    ELSE 0 
                END
            ) AS tasks_pending_delayed,

            SUM(
                CASE 
                    WHEN tdws.task_status IN (0, 2) 
                    AND CURDATE() <= tdws.end_date 
                    THEN 1 
                    ELSE 0 
                END
            ) AS tasks_pending_on_time,

            SUM(
                CASE 
                    WHEN tdws.task_status = 1 
                    AND CAST(tdws.task_completed_on AS DATE) > tdws.end_date
                    $completed_date_where
                    THEN DATEDIFF(CAST(tdws.task_completed_on AS DATE), tdws.end_date)
                    ELSE 0
                END
            ) AS total_delay_days

        FROM
            system_users AS u

        LEFT JOIN
            task_department_wise_scheduling AS tdws 
            ON u.user_id = tdws.assigned_user

        LEFT JOIN
            df_release AS df 
            ON df.id = tdws.df_id

        LEFT JOIN
            departments AS d 
            ON tdws.department_id = d.department_id

        LEFT JOIN
            user_role AS us 
            ON u.user_role_id = us.user_role_id

        WHERE
            u.user_status = 1
            AND d.department IS NOT NULL

            -- Exclude DF on hold
            AND IFNULL(df.on_hold, 0) != 1

            $dept_where

        GROUP BY
            u.user_id, d.department_id

        HAVING
            total_completed > 0 
            OR tasks_pending_delayed > 0 
            OR tasks_pending_on_time > 0

        ORDER BY
            d.department ASC, user_name ASC
    ");

    return $query->result_array();
}


public function get_user_df_details($user_id, $filters = []) {

 // --- Configuration (from your code) ---
 $df_master_table = 'df_release';
 $df_task_table = 'task_department_wise_scheduling';
 $df_table_pk = 'id';
 $df_name_col = 'df_no';
 $task_df_fk = 'df_id';
 $marketing_user_fk = 'added_by';
 $task_status_col = 'task_status';
 $task_user_col = 'assigned_user';
  
  // *** NEW: Add the completion date column name ***
  $task_completed_col = 'task_completed_on'; 
 // --- End of config ---

 $this->db->select("
  df.{$df_table_pk} AS df_id,
  df.{$df_name_col} AS df_name,
  CONCAT(mkt.first_name, ' ', mkt.last_name) as marketing_person,
   
    /* Get the earliest start date and latest end date for all tasks
       associated with this DF and user */
  MIN(tdws.start_date) AS start_date,
  MAX(tdws.end_date) AS end_date,
   
    /* Count due tasks by summing cases within the group */
  SUM(CASE WHEN tdws.{$task_status_col} IN (0, 2) AND IFNULL(tdws.on_hold, 0) != 1 THEN 1 ELSE 0 END) AS due_tasks
 ");

 $this->db->from("{$df_master_table} df");

 // JOIN 1: Join DFs to Tasks (This is the critical new part)
 $this->db->join(
  "{$df_task_table} tdws",
  "tdws.{$task_df_fk} = df.{$df_table_pk}",
  'inner' // We only want DFs that have tasks
 );

 // JOIN 2: Join for Marketing Person name
 $this->db->join(
  'system_users mkt',
  "mkt.user_id = df.{$marketing_user_fk}",
  'left'
 );

 // WHERE 1: Filter by the user ID (this replaces the subquery)
 $this->db->where("tdws.{$task_user_col}", $user_id);
 $this->db->where('IFNULL(df.on_hold, 0) != 1', NULL, FALSE);

  // *** CHANGES START HERE ***
 // WHERE 2: Apply date filters to match the dashboard's logic
 if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
  $start = $filters['start_date']; // 'Y-m-d'
  $end = $filters['end_date'];   // 'Y-m-d'
 
  // Match the dashboard scope: completed tasks in range OR currently active tasks.
  $this->db->where(
   "(
      (
        tdws.{$task_status_col} = 1
        AND CAST(tdws.{$task_completed_col} AS DATE) >= " . $this->db->escape($start) . "
        AND CAST(tdws.{$task_completed_col} AS DATE) <= " . $this->db->escape($end) . "
      )
      OR (
        tdws.{$task_status_col} IN (0, 2)
        AND IFNULL(tdws.on_hold, 0) != 1
      )
    )",
   NULL,
   FALSE
  );
 }
  // *** CHANGES END HERE ***

 // GROUP BY: Get one row per DF
 $this->db->group_by("df.{$df_table_pk}");
 // Add other non-aggregated fields to group_by for strict SQL mode
 $this->db->group_by("df.{$df_name_col}");
 $this->db->group_by("marketing_person");

 // ORDER BY
 $this->db->order_by('MIN(tdws.start_date)', 'DESC');

 $query = $this->db->get();

 return $query->result_array();
}



/**
     * UPDATED: Fetches a detailed list of tasks for a specific user.
     * - Now includes tdws.id to link to tickets.
     * - Fixes the delay bug by CASTING dates.
     */
    public function get_user_task_list_for_dashboard($user_id, $report_type, $filters = []) {
        
        $this->db->select("
            tdws.id,  /* <-- ADDED THIS ID FOR TICKET LINKING */
            dr.df_no,
            tm.task_name,
            tdws.start_date,
            tdws.end_date,
            tdws.task_completed_on,
            tdws.task_status,
            CASE 
                WHEN tdws.task_status = 1 AND CAST(tdws.task_completed_on AS DATE) > tdws.end_date 
                THEN DATEDIFF(CAST(tdws.task_completed_on AS DATE), tdws.end_date)
                WHEN tdws.task_status IN (0, 2) AND CURDATE() > tdws.end_date
                THEN DATEDIFF(CURDATE(), tdws.end_date)
                ELSE 0 
            END as delay_days
        ", FALSE);

        $this->db->from('task_department_wise_scheduling AS tdws');
        $this->db->join('df_release AS dr', 'dr.id = tdws.df_id', 'left');
        $this->db->join('task_management AS tm', 'tm.task_id = tdws.taskid', 'left');
        $this->db->where('tdws.assigned_user', $user_id);

        $start_date = $filters['start_date'] ?? null;
        $end_date = $filters['end_date'] ?? null;

        // Apply logic based on the report type
        switch ($report_type) {
            case 'done':
                $this->db->where('tdws.task_status', 1);
                if ($start_date && $end_date) {
                    $this->db->where("CAST(tdws.task_completed_on AS DATE) BETWEEN '$start_date' AND '$end_date'");
                }
                break;
            
            case 'pending':
                $this->db->where('tdws.task_status IN (0, 2)');
                break;

            case 'delayed':
                $this->db->where('tdws.task_status', 1);
                $this->db->where('CAST(tdws.task_completed_on AS DATE) > tdws.end_date');
                if ($start_date && $end_date) {
                    $this->db->where("CAST(tdws.task_completed_on AS DATE) BETWEEN '$start_date' AND '$end_date'");
                }
                break;

            case 'assigned':
            default:
                // "Assigned" = Done (filtered) + Pending (not filtered)
                $this->db->group_start();
                    // 1. Done (within date filter)
                    $this->db->group_start();
                        $this->db->where('tdws.task_status', 1);
                        if ($start_date && $end_date) {
                            $this->db->where("CAST(tdws.task_completed_on AS DATE) BETWEEN '$start_date' AND '$end_date'");
                        }
                    $this->db->group_end();
                    // 2. OR All Pending
                    $this->db->or_group_start();
                        $this->db->where('tdws.task_status IN (0, 2)');
                    $this->db->group_end();
                $this->db->group_end();
                break;
        }

        $this->db->order_by('tdws.end_date', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }


/**
     * NEW: Fetches the high-level DF summary for the selected financial year.
     *
     * @param array $filters (Must contain 'start_date' and 'end_date' for the FY)
     * @return array Summary stats
     */
    public function get_df_summary_for_financial_year($filters = []) {
        $stats = ['total_released' => 0, 'total_dispatched' => 0];
        if (empty($filters['start_date']) || empty($filters['end_date'])) {
            return $stats;
        }
        $start_date = $this->db->escape($filters['start_date']);
        $end_date = $this->db->escape($filters['end_date']);
        $query = $this->db->query("
            SELECT 
                COUNT(dr.id) as total_released,
                SUM(CASE 
                    WHEN dispatch.task_status = 1 AND dispatch.task_completed_on <= {$end_date} 
                    THEN 1 
                    ELSE 0 
                END) as total_dispatched
            FROM 
                df_release as dr
            LEFT JOIN 
                task_department_wise_scheduling as dispatch 
                ON dispatch.df_id = dr.id AND dispatch.taskid = 103
            WHERE 
                dr.added_on >= {$start_date} 
                AND dr.added_on <= {$end_date}
        ");
        $result = $query->row_array();
        if ($result) {
            $stats['total_released'] = (int)$result['total_released'];
            $stats['total_dispatched'] = (int)$result['total_dispatched'];
        }
        return $stats;
    }

    public function get_approver_bottlenecks() {
        $this->db->select("
            CONCAT(approver.first_name, ' ', approver.last_name) as approver_name,
            approver.profile_image as profile_pic,
            team.team_name,
            COUNT(tdws.id) as pending_tasks,
            SUM(DATEDIFF(CURDATE(), tdws.end_date)) as total_delay_days
        ", FALSE);
        $this->db->from('task_department_wise_scheduling AS tdws');
        $this->db->join('departments AS d', 'd.department_id = tdws.department_id', 'inner');
        $this->db->join('prestogroup_teams AS team', 'team.department_id = d.department_id AND team.status = 1', 'inner');
        $this->db->join('system_users AS approver', 'approver.user_id = team.team_leader', 'inner');
        
        $this->db->where('tdws.task_status', 2); 
        $this->db->where('CURDATE() < tdws.end_date'); 
        
        $this->db->group_by('approver.user_id');
        $this->db->order_by('pending_tasks', 'DESC');
        $this->db->limit(5);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_trend_detail_tasks($start_date, $end_date) {
        
        $this->db->select("
            dr.df_no,
            tm.task_name,
            d.department,
            CONCAT(assignee.first_name, ' ', assignee.last_name) as assigned_user_name,
            tdws.end_date,
            tdws.task_completed_on,
            DATEDIFF(CAST(tdws.task_completed_on AS DATE), tdws.end_date) as delay_days,
            ( (po.order_value * (0.08 / 365)) * DATEDIFF(CAST(tdws.task_completed_on AS DATE), tdws.end_date) ) as estimated_loss
        ", FALSE);
        
        $this->db->from('task_department_wise_scheduling AS tdws');
        $this->db->join('df_release AS dr', 'dr.id = tdws.df_id', 'left');
        $this->db->join('task_management AS tm', 'tm.task_id = tdws.taskid', 'left');
        $this->db->join('departments AS d', 'd.department_id = tdws.department_id', 'left');
        $this->db->join('system_users AS assignee', 'assignee.user_id = tdws.assigned_user', 'left');
        $this->db->join('poreceived AS po', 'po.df_id = tdws.df_id', 'left');
        
        $this->db->where('tdws.task_status', 1); 
        $this->db->where('CAST(tdws.task_completed_on AS DATE) > tdws.end_date'); 
        $this->db->where('CAST(tdws.task_completed_on AS DATE) >=', $start_date);
        $this->db->where('CAST(tdws.task_completed_on AS DATE) <=', $end_date);
        
        $this->db->order_by('estimated_loss', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }


    public function get_due_task_details($df_id, $user_id) {
    
    // --- !! IMPORTANT: CHECK THESE ASSUMPTIONS !! ---
    $task_table = 'task_department_wise_scheduling'; // (tdws)
    
    // ASSUMPTION 1: This is the primary key of 'task_department_wise_scheduling'.
    // 'communication_ticket_system.task_record_id' links to this.
    $task_table_pk = 'id'; 
    
    // ASSUMPTION 2: This is the column in 'task_department_wise_scheduling' 
    // that holds the task's name or description.
    $task_name_col = 'task_name'; 
    
    $ticket_table = 'communication_ticket_system'; // (cts)
    $ticket_task_fk = 'task_record_id'; // This links to tdws.id
    $ticket_display_col = 'help_ticket_no'; // This is the ticket number
    // --- End of Assumptions ---

    $this->db->select("
        taskmanage.{$task_name_col} AS task_name,
        tdws.start_date,
        tdws.end_date,
        GROUP_CONCAT(DISTINCT cts.{$ticket_display_col} SEPARATOR ', ') AS ticket_number
    ");
    
    $this->db->from("{$task_table} tdws");
    $this->db->join('task_management taskmanage','tdws.taskid=taskmanage.task_id','left');
    // Left Join to get ticket info (if it exists)
    $this->db->join(
        "{$ticket_table} cts", 
        "cts.{$ticket_task_fk} = tdws.{$task_table_pk}", // The correct join
        'left'
    );
    
    // Filter for the specific DF and User
    $this->db->where("tdws.df_id", $df_id);
    $this->db->where("tdws.assigned_user", $user_id);
    
    // Filter for only "Due" tasks
    $this->db->where_in("tdws.task_status", [0, 2]); 
    
    // Group by the task record to get one row per task
    $this->db->group_by("tdws.{$task_table_pk}");
    
    // Add other non-aggregated columns to group_by for strict SQL mode
    $this->db->group_by("taskmanage.{$task_name_col}"); 
    $this->db->group_by("tdws.start_date");
    $this->db->group_by("tdws.end_date");

    $this->db->order_by("tdws.end_date", "ASC");
    
    return $this->db->get()->result_array();
}

public function get_all_tasks_export($filters) {
    // 1. Select fields
    $this->db->select('
        tm.task_name,
        tdws.id as ticket_number,  
        CONCAT(u.first_name, " ", u.last_name) as assigned_to,
        d.department as department_name,
        tdws.start_date,
        tdws.end_date as due_date,
        tdws.task_status,  
        tdws.task_completed_on as completed_date,
        tdws.remarks   
    ');

    // 2. Main Table
    $this->db->from('task_department_wise_scheduling tdws');

    // 3. Joins
    $this->db->join('task_management tm', 'tm.task_id = tdws.taskid', 'left');
    $this->db->join('system_users u', 'u.user_id = tdws.assigned_user', 'left');
    $this->db->join('departments d', 'd.department_id = tdws.department_id', 'left');

    // 4. Filters
    if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
        $this->db->where('tdws.start_date >=', $filters['start_date']);
        $this->db->where('tdws.start_date <=', $filters['end_date']);
    }

    if (!empty($filters['department_id'])) {
        $this->db->where('tdws.department_id', $filters['department_id']);
    }

    // $this->db->where('tdws.is_deleted', 0); 

    $this->db->order_by('d.department', 'ASC'); 
    $this->db->order_by('tdws.end_date', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}


public function get_all_tasks_exportforemail($filters) {
    // 1. Select fields
    $this->db->select('
        tm.task_name,
        df.df_no,
        d.department as department_name,
        tdws.start_date,
        tdws.end_date as due_date,
        tdws.task_status,
        tdws.task_completed_on as completed_date,
        tdws.remarks,
        TRIM(CONCAT(IFNULL(u.first_name, ""), " ", IFNULL(u.last_name, ""))) as assigned_to
    ');

    // 2. Main Table
    $this->db->from('task_department_wise_scheduling tdws');

    // 3. Joins
    $this->db->join('task_management tm', 'tm.task_id = tdws.taskid', 'left');
    $this->db->join('system_users u', 'u.user_id = tdws.assigned_user', 'left');
    $this->db->join('departments d', 'd.department_id = tdws.department_id', 'left');
    $this->db->join('df_release df','tdws.df_id=df.id','left');

    // 4. Filters
    if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
        $this->db->where('tdws.start_date >=', $filters['start_date']);
        $this->db->where('tdws.start_date <=', $filters['end_date']);
    }

    if (!empty($filters['department_id'])) {
        $this->db->where('tdws.department_id', $filters['department_id']);
    }
    $this->db->where('tdws.id >', 0);
    // 5. Ordering (CRITICAL for the report grouping)
    $this->db->order_by('d.department', 'ASC'); 
    $this->db->order_by('tdws.end_date', 'ASC');
    
    $query = $this->db->get();
    return $query->result_array();
}
  
}
