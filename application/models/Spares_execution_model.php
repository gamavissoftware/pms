<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Spares_execution_model extends CI_Model
{
    const SPARES_EXECUTION_BUSINESS_LOC_ID = 2;

    private function apply_scoped_department_filter($department_alias, $entity_department_column = null)
    {
        if (empty($entity_department_column)) {
            $this->db->where($department_alias . '.business_loc_id', self::SPARES_EXECUTION_BUSINESS_LOC_ID);
            return;
        }

        $this->db->group_start()
            ->where($entity_department_column . ' IS NULL', null, false)
            ->or_where($department_alias . '.business_loc_id', self::SPARES_EXECUTION_BUSINESS_LOC_ID)
        ->group_end();
    }

    private function get_task_last_update_subquery()
    {
        return "
            SELECT execution_task_id, MAX(added_on) as last_update_at
            FROM spares_execution_task_updates
            GROUP BY execution_task_id
        ";
    }

    public function get_execution_notification_types()
    {
        return array(
            'spares_execution_schedule_pending',
            'spares_execution_overdue',
            'spares_execution_stale_task',
            'spares_execution_extension_pending',
        );
    }

    public function get_workflow_types()
    {
        return array(
            'IN_STOCK' => 'In Stock',
            'STANDARD' => 'Standard Procurement',
            'CUSTOM' => 'Custom Production',
        );
    }

    public function get_departments()
    {
        return $this->db
            ->where('business_loc_id', self::SPARES_EXECUTION_BUSINESS_LOC_ID)
            ->order_by('department', 'ASC')
            ->get('departments')
            ->result();
    }

    public function get_department_by_id($department_id)
    {
        return $this->db
            ->where('department_id', (int) $department_id)
            ->where('business_loc_id', self::SPARES_EXECUTION_BUSINESS_LOC_ID)
            ->get('departments')
            ->row();
    }

    public function get_users($department_id = null)
    {
        $this->db
            ->select('u.user_id, u.title, u.first_name, u.last_name, u.department_id')
            ->from('system_users u')
            ->join('departments d', 'd.department_id = u.department_id', 'inner')
            ->where('u.user_status', 1)
            ->where('d.business_loc_id', self::SPARES_EXECUTION_BUSINESS_LOC_ID)
            ->order_by('u.first_name', 'ASC');

        if (!empty($department_id)) {
            $this->db->where('u.department_id', $department_id);
        }

        return $this->db->get()->result();
    }

    public function is_valid_scoped_department($department_id)
    {
        if (empty($department_id)) {
            return false;
        }

        return (bool) $this->db
            ->select('department_id')
            ->from('departments')
            ->where('department_id', (int) $department_id)
            ->where('business_loc_id', self::SPARES_EXECUTION_BUSINESS_LOC_ID)
            ->get()
            ->row();
    }

    public function is_valid_user_for_department($user_id, $department_id = null)
    {
        if (empty($user_id)) {
            return false;
        }

        $this->db
            ->select('u.user_id')
            ->from('system_users u')
            ->join('departments d', 'd.department_id = u.department_id', 'inner')
            ->where('u.user_id', (int) $user_id)
            ->where('u.user_status', 1)
            ->where('d.business_loc_id', self::SPARES_EXECUTION_BUSINESS_LOC_ID);

        if (!empty($department_id)) {
            $this->db->where('u.department_id', (int) $department_id);
        }

        return (bool) $this->db->get()->row();
    }

    public function get_execution_task_context($execution_task_id)
    {
        $this->db
            ->select("
                t.execution_task_id,
                t.department_id,
                t.execution_order_id,
                t.task_status,
                t.depends_on_task_id,
                COALESCE(tm.can_start_parallel, 0) as can_start_parallel,
                eo.order_id,
                dep.task_name as dependency_task_name,
                dep.task_status as dependency_task_status,
                CASE
                    WHEN t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed' THEN 1
                    ELSE 0
                END as dependency_blocked
            ", false)
            ->from('spares_execution_tasks t')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->join('spares_execution_tasks dep', 'dep.execution_task_id = t.depends_on_task_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->where('t.execution_task_id', (int) $execution_task_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        return $this->db->get()->row();
    }

    public function get_extension_request_context($execution_task_id)
    {
        $this->db
            ->select("
                t.execution_task_id,
                t.execution_order_id,
                t.task_name,
                t.task_status,
                t.planned_end_date,
                eo.order_id,
                COALESCE(tm.extension_allowed, 1) as extension_allowed,
                (
                    SELECT COUNT(*)
                    FROM spares_execution_extension_requests r
                    WHERE r.execution_task_id = t.execution_task_id
                      AND r.request_status = 'Pending'
                ) as pending_request_count
            ", false)
            ->from('spares_execution_tasks t')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->where('t.execution_task_id', (int) $execution_task_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        return $this->db->get()->row();
    }

    public function get_extension_review_context($extension_request_id)
    {
        $this->db
            ->select("
                r.extension_request_id,
                r.request_status,
                r.current_due_date,
                r.requested_due_date,
                t.execution_task_id,
                t.execution_order_id,
                t.task_name,
                t.task_status,
                t.planned_end_date,
                eo.order_id
            ", false)
            ->from('spares_execution_extension_requests r')
            ->join('spares_execution_tasks t', 't.execution_task_id = r.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->where('r.extension_request_id', (int) $extension_request_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        return $this->db->get()->row();
    }

    public function get_task_master($workflow_type = null)
    {
        $this->db
            ->select('m.*, d.department, u.title, u.first_name, u.last_name')
            ->from('spares_execution_task_master m')
            ->join('departments d', 'd.department_id = m.department_id', 'left')
            ->join('system_users u', 'u.user_id = m.default_owner_id', 'left')
            ->order_by('m.workflow_type', 'ASC')
            ->order_by('m.sequence_no', 'ASC');
        $this->apply_scoped_department_filter('d', 'm.department_id');

        if (!empty($workflow_type)) {
            $this->db->where('m.workflow_type', $workflow_type);
        }

        return $this->db->get()->result();
    }

    public function get_task_master_by_id($task_master_id)
    {
        $this->db
            ->select('m.*')
            ->from('spares_execution_task_master m')
            ->join('departments d', 'd.department_id = m.department_id', 'left')
            ->where('m.task_master_id', (int) $task_master_id);
        $this->apply_scoped_department_filter('d', 'm.department_id');

        return $this->db
            ->get()
            ->row();
    }

    public function save_task_master($data, $task_master_id = null)
    {
        if ($task_master_id) {
            $this->db->where('task_master_id', (int) $task_master_id);
            return $this->db->update('spares_execution_task_master', $data);
        }

        return $this->db->insert('spares_execution_task_master', $data);
    }

    public function set_task_master_status($task_master_id, $status, $user_id)
    {
        return $this->db
            ->where('task_master_id', (int) $task_master_id)
            ->update('spares_execution_task_master', array(
                'is_active' => (int) $status,
                'updated_by' => (int) $user_id,
                'updated_at' => date('Y-m-d H:i:s'),
            ));
    }

    public function get_order_snapshot($order_id)
    {
        return $this->db
            ->select("
                so.order_id,
                so.opportunity_id,
                so.po_id,
                so.customer_id,
                so.marketing_person_id,
                so.order_value,
                so.order_date,
                so.status as spare_order_status,
                o.op_no,
                o.op_type,
                c.company_name,
                po.po_no,
                po.po_date,
                m.title as marketing_title,
                m.first_name as marketing_first_name,
                m.last_name as marketing_last_name
            ")
            ->from('spares_orders so')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('purchase_orders po', 'po.po_id = so.po_id', 'left')
            ->join('system_users m', 'm.user_id = so.marketing_person_id', 'left')
            ->where('so.order_id', (int) $order_id)
            ->get()
            ->row();
    }

    public function get_execution_order_by_order($order_id)
    {
        return $this->db
            ->where('order_id', (int) $order_id)
            ->get('spares_execution_orders')
            ->row();
    }

    public function get_execution_order($execution_order_id)
    {
        return $this->db
            ->where('execution_order_id', (int) $execution_order_id)
            ->get('spares_execution_orders')
            ->row();
    }

    public function get_execution_dashboard_metrics()
    {
        $today = date('Y-m-d');
        $week_end = date('Y-m-d', strtotime('+7 day'));

        $orders = $this->db
            ->select("
                COUNT(*) as total_orders,
                SUM(CASE WHEN execution_status IN ('Scheduled', 'In Progress', 'On Hold') THEN 1 ELSE 0 END) as active_orders,
                SUM(CASE WHEN execution_status = 'Scheduled' THEN 1 ELSE 0 END) as scheduled_orders,
                SUM(CASE WHEN execution_status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_orders,
                SUM(CASE WHEN execution_status = 'On Hold' THEN 1 ELSE 0 END) as on_hold_orders,
                SUM(CASE WHEN execution_status = 'Completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN execution_status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                SUM(CASE WHEN priority = 'Critical' AND execution_status NOT IN ('Completed', 'Cancelled') THEN 1 ELSE 0 END) as critical_orders,
                SUM(CASE WHEN commit_date BETWEEN " . $this->db->escape($today) . " AND " . $this->db->escape($week_end) . " AND execution_status NOT IN ('Completed', 'Cancelled') THEN 1 ELSE 0 END) as due_this_week
            ", false)
            ->from('spares_execution_orders')
            ->get()
            ->row();

        $task_metrics = $this->db
            ->select("
                SUM(CASE WHEN task_status NOT IN ('Completed', 'Cancelled') AND planned_end_date < " . $this->db->escape($today) . " THEN 1 ELSE 0 END) as overdue_tasks
            ", false)
            ->from('spares_execution_tasks t')
            ->join('departments d', 'd.department_id = t.department_id', 'left');
        $this->apply_scoped_department_filter('d', 't.department_id');
        $task_metrics = $this->db->get()->row();

        $extension_metrics = $this->db
            ->select("
                SUM(CASE WHEN request_status = 'Pending' THEN 1 ELSE 0 END) as pending_extensions
            ", false)
            ->from('spares_execution_extension_requests r')
            ->join('spares_execution_tasks t', 't.execution_task_id = r.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left');
        $this->apply_scoped_department_filter('d', 't.department_id');
        $extension_metrics = $this->db->get()->row();

        $unscheduled_metrics = $this->db
            ->select('COUNT(*) as unscheduled_orders')
            ->from('spares_orders so')
            ->join('spares_execution_orders eo', 'eo.order_id = so.order_id', 'left')
            ->where('eo.execution_order_id IS NULL', null, false)
            ->where_not_in('so.status', array('Completed', 'Cancelled'))
            ->get()
            ->row();

        return (object) array(
            'total_orders' => !empty($orders->total_orders) ? (int) $orders->total_orders : 0,
            'active_orders' => !empty($orders->active_orders) ? (int) $orders->active_orders : 0,
            'scheduled_orders' => !empty($orders->scheduled_orders) ? (int) $orders->scheduled_orders : 0,
            'in_progress_orders' => !empty($orders->in_progress_orders) ? (int) $orders->in_progress_orders : 0,
            'on_hold_orders' => !empty($orders->on_hold_orders) ? (int) $orders->on_hold_orders : 0,
            'completed_orders' => !empty($orders->completed_orders) ? (int) $orders->completed_orders : 0,
            'cancelled_orders' => !empty($orders->cancelled_orders) ? (int) $orders->cancelled_orders : 0,
            'critical_orders' => !empty($orders->critical_orders) ? (int) $orders->critical_orders : 0,
            'due_this_week' => !empty($orders->due_this_week) ? (int) $orders->due_this_week : 0,
            'overdue_tasks' => !empty($task_metrics->overdue_tasks) ? (int) $task_metrics->overdue_tasks : 0,
            'pending_extensions' => !empty($extension_metrics->pending_extensions) ? (int) $extension_metrics->pending_extensions : 0,
            'unscheduled_orders' => !empty($unscheduled_metrics->unscheduled_orders) ? (int) $unscheduled_metrics->unscheduled_orders : 0,
        );
    }

    public function get_execution_orders($filters = array())
    {
        $last_update_subquery = $this->get_task_last_update_subquery();
        $task_summary_subquery = "
            SELECT
                t.execution_order_id,
                COUNT(*) as total_tasks,
                SUM(CASE WHEN t.task_status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN t.task_status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_tasks,
                SUM(CASE WHEN t.task_status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                SUM(CASE WHEN t.task_status = 'Open' THEN 1 ELSE 0 END) as open_tasks,
                SUM(CASE WHEN t.task_status = 'Pending' THEN 1 ELSE 0 END) as pending_tasks,
                SUM(CASE WHEN t.task_status = 'Blocked' OR (t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed') THEN 1 ELSE 0 END) as blocked_tasks,
                SUM(CASE WHEN t.task_status = 'On Hold' THEN 1 ELSE 0 END) as on_hold_tasks,
                SUM(CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND t.planned_end_date < CURDATE() THEN 1 ELSE 0 END) as overdue_tasks
            FROM spares_execution_tasks t
            LEFT JOIN spares_execution_task_master tm ON tm.task_master_id = t.task_master_id
            LEFT JOIN spares_execution_tasks dep ON dep.execution_task_id = t.depends_on_task_id
            LEFT JOIN departments d ON d.department_id = t.department_id
            WHERE (t.department_id IS NULL OR d.business_loc_id = " . (int) self::SPARES_EXECUTION_BUSINESS_LOC_ID . ")
            GROUP BY t.execution_order_id
        ";

        $next_task_priority_case = "
            CASE
                WHEN t2.task_status = 'In Progress' THEN 1
                WHEN t2.task_status = 'Open' THEN 2
                WHEN (t2.task_status = 'Blocked' OR (t2.depends_on_task_id IS NOT NULL AND COALESCE(tm2.can_start_parallel, 0) <> 1 AND COALESCE(dep2.task_status, '') <> 'Completed')) THEN 3
                WHEN t2.task_status = 'Pending' THEN 4
                WHEN t2.task_status = 'On Hold' THEN 5
                ELSE 9
            END
        ";

        $next_task_name_subquery = "
            SELECT t2.task_name
            FROM spares_execution_tasks t2
            LEFT JOIN departments d2 ON d2.department_id = t2.department_id
            LEFT JOIN spares_execution_task_master tm2 ON tm2.task_master_id = t2.task_master_id
            LEFT JOIN spares_execution_tasks dep2 ON dep2.execution_task_id = t2.depends_on_task_id
            WHERE t2.execution_order_id = eo.execution_order_id
              AND (t2.department_id IS NULL OR d2.business_loc_id = " . (int) self::SPARES_EXECUTION_BUSINESS_LOC_ID . ")
              AND t2.task_status NOT IN ('Completed', 'Cancelled')
            ORDER BY " . $next_task_priority_case . ",
                CASE WHEN t2.task_status NOT IN ('Completed', 'Cancelled') AND t2.planned_end_date < CURDATE() THEN 0 ELSE 1 END,
                t2.sequence_no ASC,
                t2.planned_end_date ASC
            LIMIT 1
        ";

        $next_task_status_subquery = "
            SELECT t2.task_status
            FROM spares_execution_tasks t2
            LEFT JOIN departments d2 ON d2.department_id = t2.department_id
            LEFT JOIN spares_execution_task_master tm2 ON tm2.task_master_id = t2.task_master_id
            LEFT JOIN spares_execution_tasks dep2 ON dep2.execution_task_id = t2.depends_on_task_id
            WHERE t2.execution_order_id = eo.execution_order_id
              AND (t2.department_id IS NULL OR d2.business_loc_id = " . (int) self::SPARES_EXECUTION_BUSINESS_LOC_ID . ")
              AND t2.task_status NOT IN ('Completed', 'Cancelled')
            ORDER BY " . $next_task_priority_case . ",
                CASE WHEN t2.task_status NOT IN ('Completed', 'Cancelled') AND t2.planned_end_date < CURDATE() THEN 0 ELSE 1 END,
                t2.sequence_no ASC,
                t2.planned_end_date ASC
            LIMIT 1
        ";

        $next_task_owner_subquery = "
            SELECT CONCAT_WS(' ', u2.title, u2.first_name, u2.last_name)
            FROM spares_execution_tasks t2
            LEFT JOIN departments d2 ON d2.department_id = t2.department_id
            LEFT JOIN spares_execution_task_master tm2 ON tm2.task_master_id = t2.task_master_id
            LEFT JOIN spares_execution_tasks dep2 ON dep2.execution_task_id = t2.depends_on_task_id
            LEFT JOIN system_users u2 ON u2.user_id = t2.assigned_to
            WHERE t2.execution_order_id = eo.execution_order_id
              AND (t2.department_id IS NULL OR d2.business_loc_id = " . (int) self::SPARES_EXECUTION_BUSINESS_LOC_ID . ")
              AND t2.task_status NOT IN ('Completed', 'Cancelled')
            ORDER BY " . $next_task_priority_case . ",
                CASE WHEN t2.task_status NOT IN ('Completed', 'Cancelled') AND t2.planned_end_date < CURDATE() THEN 0 ELSE 1 END,
                t2.sequence_no ASC,
                t2.planned_end_date ASC
            LIMIT 1
        ";

        $order_last_activity_subquery = "
            SELECT MAX(COALESCE(last_up.last_update_at, t3.updated_at, t3.created_at))
            FROM spares_execution_tasks t3
            LEFT JOIN (" . $last_update_subquery . ") last_up ON last_up.execution_task_id = t3.execution_task_id
            LEFT JOIN departments d3 ON d3.department_id = t3.department_id
            WHERE t3.execution_order_id = eo.execution_order_id
              AND (t3.department_id IS NULL OR d3.business_loc_id = " . (int) self::SPARES_EXECUTION_BUSINESS_LOC_ID . ")
        ";

        $extension_summary_subquery = "
            SELECT
                t.execution_order_id,
                SUM(CASE WHEN r.request_status = 'Pending' THEN 1 ELSE 0 END) as pending_extensions
            FROM spares_execution_extension_requests r
            INNER JOIN spares_execution_tasks t ON t.execution_task_id = r.execution_task_id
            LEFT JOIN departments d ON d.department_id = t.department_id
            WHERE (t.department_id IS NULL OR d.business_loc_id = " . (int) self::SPARES_EXECUTION_BUSINESS_LOC_ID . ")
            GROUP BY t.execution_order_id
        ";

        $this->db
            ->select("
                eo.*,
                so.order_value,
                so.order_date,
                so.status as spare_order_status,
                o.op_no,
                o.op_type,
                c.company_name,
                po.po_no,
                CONCAT_WS(' ', m.title, m.first_name, m.last_name) as marketing_person_name,
                COALESCE(ts.total_tasks, 0) as total_tasks,
                COALESCE(ts.completed_tasks, 0) as completed_tasks,
                COALESCE(ts.cancelled_tasks, 0) as cancelled_tasks,
                COALESCE(ts.in_progress_tasks, 0) as in_progress_tasks,
                COALESCE(ts.open_tasks, 0) as open_tasks,
                COALESCE(ts.pending_tasks, 0) as pending_tasks,
                COALESCE(ts.blocked_tasks, 0) as blocked_tasks,
                COALESCE(ts.on_hold_tasks, 0) as on_hold_tasks,
                COALESCE(ts.overdue_tasks, 0) as overdue_tasks,
                COALESCE(ex.pending_extensions, 0) as pending_extensions,
                (" . $next_task_name_subquery . ") as next_task_name,
                (" . $next_task_status_subquery . ") as next_task_status,
                (" . $next_task_owner_subquery . ") as next_task_owner_name,
                (" . $order_last_activity_subquery . ") as order_last_activity_at,
                CASE
                    WHEN COALESCE(ts.total_tasks, 0) > 0 THEN ROUND((COALESCE(ts.completed_tasks, 0) * 100) / ts.total_tasks, 0)
                    ELSE 0
                END as progress_percent
            ", false)
            ->from('spares_execution_orders eo')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('purchase_orders po', 'po.po_id = so.po_id', 'left')
            ->join('system_users m', 'm.user_id = eo.marketing_owner_id', 'left')
            ->join('(' . $task_summary_subquery . ') ts', 'ts.execution_order_id = eo.execution_order_id', 'left', false)
            ->join('(' . $extension_summary_subquery . ') ex', 'ex.execution_order_id = eo.execution_order_id', 'left', false);

        if (!empty($filters['execution_status'])) {
            if (is_array($filters['execution_status'])) {
                $this->db->where_in('eo.execution_status', $filters['execution_status']);
            } else {
                $this->db->where('eo.execution_status', $filters['execution_status']);
            }
        }

        if (!empty($filters['workflow_type'])) {
            $this->db->where('eo.workflow_type', $filters['workflow_type']);
        }

        if (!empty($filters['priority'])) {
            $this->db->where('eo.priority', $filters['priority']);
        }

        if (!empty($filters['marketing_owner_id'])) {
            $this->db->where('eo.marketing_owner_id', (int) $filters['marketing_owner_id']);
        }

        if (!empty($filters['manager_user_id'])) {
            $user_id = (int) $filters['manager_user_id'];
            $this->db->group_start()
                ->where('eo.marketing_owner_id', $user_id)
                ->or_where('eo.created_by', $user_id)
            ->group_end();
        }

        if (!empty($filters['order_id'])) {
            $this->db->where('eo.order_id', (int) $filters['order_id']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('c.company_name', $search)
                ->or_like('o.op_no', $search)
                ->or_like('po.po_no', $search)
                ->or_like('eo.order_id', $search)
            ->group_end();
        }

        if (!empty($filters['active_only'])) {
            $this->db->where_not_in('eo.execution_status', array('Completed', 'Cancelled'));
        }

        if (!empty($filters['overdue_only'])) {
            $this->db->where('COALESCE(ts.overdue_tasks, 0) > 0', null, false);
        }

        if (!empty($filters['pending_extensions_only'])) {
            $this->db->where('COALESCE(ex.pending_extensions, 0) > 0', null, false);
        }

        $this->db
            ->order_by('overdue_tasks', 'DESC', false)
            ->order_by('eo.commit_date', 'ASC')
            ->order_by('eo.execution_order_id', 'DESC');

        if (!empty($filters['limit'])) {
            $this->db->limit((int) $filters['limit']);
        }

        return $this->db->get()->result();
    }

    public function get_template_tasks($workflow_type)
    {
        $this->db
            ->from('spares_execution_task_master m')
            ->join('departments d', 'd.department_id = m.department_id', 'left')
            ->where('m.workflow_type', $workflow_type)
            ->where('m.is_active', 1)
            ->order_by('m.sequence_no', 'ASC');
        $this->apply_scoped_department_filter('d', 'm.department_id');

        return $this->db
            ->get()
            ->result();
    }

    public function build_schedule_preview($workflow_type, $commit_date)
    {
        $tasks = $this->get_template_tasks($workflow_type);
        $preview = array();

        if (empty($tasks) || empty($commit_date)) {
            return $preview;
        }

        $cursor = new DateTime($commit_date);
        $reversed = array_reverse($tasks);

        foreach ($reversed as $task) {
            $sla_days = max(1, (int) $task->sla_days);
            $planned_end = clone $cursor;
            $planned_start = clone $cursor;
            $planned_start->modify('-' . ($sla_days - 1) . ' day');

            $preview[$task->task_code] = array(
                'task_master_id' => (int) $task->task_master_id,
                'workflow_type' => $task->workflow_type,
                'task_code' => $task->task_code,
                'task_name' => $task->task_name,
                'department_id' => $task->department_id,
                'default_owner_id' => $task->default_owner_id,
                'sequence_no' => (int) $task->sequence_no,
                'sla_days' => $sla_days,
                'depends_on_code' => $task->depends_on_code,
                'can_start_parallel' => (int) $task->can_start_parallel,
                'planned_start_date' => $planned_start->format('Y-m-d'),
                'planned_end_date' => $planned_end->format('Y-m-d'),
            );

            $cursor = clone $planned_start;
            $cursor->modify('-1 day');
        }

        $ordered_preview = array();
        foreach ($tasks as $task) {
            if (isset($preview[$task->task_code])) {
                $ordered_preview[] = $preview[$task->task_code];
            }
        }

        return $ordered_preview;
    }

    public function create_execution_schedule($execution_data, $task_rows)
    {
        $this->db->trans_start();

        $this->db->insert('spares_execution_orders', $execution_data);
        $execution_order_id = $this->db->insert_id();

        $task_id_map = array();
        $dependency_map = array();

        foreach ($task_rows as $task_row) {
            $depends_on_code = isset($task_row['depends_on_code']) ? $task_row['depends_on_code'] : null;
            $can_start_parallel = !empty($task_row['can_start_parallel']) ? 1 : 0;
            unset($task_row['depends_on_code']);
            unset($task_row['can_start_parallel']);

            $task_row['execution_order_id'] = $execution_order_id;
            $task_row['task_status'] = (empty($depends_on_code) || $can_start_parallel === 1) ? 'Open' : 'Pending';

            $this->db->insert('spares_execution_tasks', $task_row);
            $execution_task_id = $this->db->insert_id();

            $task_id_map[$task_row['task_code']] = $execution_task_id;
            $dependency_map[$execution_task_id] = $depends_on_code;

            $this->db->insert('spares_execution_task_updates', array(
                'execution_task_id' => $execution_task_id,
                'update_type' => 'Schedule',
                'remarks' => 'Task scheduled for execution.',
                'added_by' => $task_row['created_by'],
                'added_on' => $task_row['created_at'],
            ));
        }

        foreach ($dependency_map as $execution_task_id => $depends_on_code) {
            if (!empty($depends_on_code) && isset($task_id_map[$depends_on_code])) {
                $this->db
                    ->where('execution_task_id', $execution_task_id)
                    ->update('spares_execution_tasks', array(
                        'depends_on_task_id' => $task_id_map[$depends_on_code],
                    ));
            }
        }

        $this->refresh_execution_order_status($execution_order_id);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return false;
        }

        return $execution_order_id;
    }

    public function update_execution_schedule($execution_order_id, $execution_data, $task_rows, $user_id)
    {
        $execution_order = $this->get_execution_order($execution_order_id);
        if (!$execution_order) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $execution_data['updated_by'] = (int) $user_id;
        $execution_data['updated_at'] = $now;

        $this->db
            ->where('execution_order_id', (int) $execution_order_id)
            ->update('spares_execution_orders', $execution_data);

        foreach ($task_rows as $task_row) {
            if (empty($task_row['execution_task_id'])) {
                continue;
            }

            $existing_task = $this->db
                ->where('execution_task_id', (int) $task_row['execution_task_id'])
                ->where('execution_order_id', (int) $execution_order_id)
                ->get('spares_execution_tasks')
                ->row();

            if (!$existing_task) {
                continue;
            }

            $update_data = array(
                'department_id' => $task_row['department_id'],
                'assigned_to' => $task_row['assigned_to'],
                'planned_start_date' => $task_row['planned_start_date'],
                'planned_end_date' => $task_row['planned_end_date'],
                'updated_by' => (int) $user_id,
                'updated_at' => $now,
            );

            $changes = array();

            if ((int) $existing_task->department_id !== (int) $task_row['department_id']) {
                $changes[] = 'department updated';
            }

            if ((int) $existing_task->assigned_to !== (int) $task_row['assigned_to']) {
                $changes[] = 'owner updated';
            }

            if ((string) $existing_task->planned_start_date !== (string) $task_row['planned_start_date']) {
                $changes[] = 'start date ' . ($existing_task->planned_start_date ?: '-') . ' to ' . ($task_row['planned_start_date'] ?: '-');
            }

            if ((string) $existing_task->planned_end_date !== (string) $task_row['planned_end_date']) {
                $changes[] = 'end date ' . ($existing_task->planned_end_date ?: '-') . ' to ' . ($task_row['planned_end_date'] ?: '-');
            }

            $update_data['is_overdue'] = ($existing_task->task_status !== 'Completed' && $existing_task->task_status !== 'Cancelled' && !empty($task_row['planned_end_date']) && $task_row['planned_end_date'] < date('Y-m-d')) ? 1 : 0;

            $this->db
                ->where('execution_task_id', (int) $task_row['execution_task_id'])
                ->where('execution_order_id', (int) $execution_order_id)
                ->update('spares_execution_tasks', $update_data);

            if (!empty($changes)) {
                $this->db->insert('spares_execution_task_updates', array(
                    'execution_task_id' => (int) $task_row['execution_task_id'],
                    'update_type' => 'Schedule',
                    'remarks' => 'Schedule updated: ' . implode(', ', $changes) . '.',
                    'added_by' => (int) $user_id,
                    'added_on' => $now,
                ));
            }
        }

        $this->refresh_execution_order_status($execution_order_id);
        $this->db->trans_complete();

        return $this->db->trans_status() !== false;
    }

    public function get_execution_tasks($execution_order_id)
    {
        $last_update_subquery = $this->get_task_last_update_subquery();

        $this->db
            ->select("
                t.*,
                CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND t.planned_end_date < CURDATE() THEN 1 ELSE 0 END as live_is_overdue,
                COALESCE(tm.extension_allowed, 1) as extension_allowed,
                COALESCE(tm.can_start_parallel, 0) as can_start_parallel,
                (
                    SELECT COUNT(*)
                    FROM spares_execution_extension_requests r
                    WHERE r.execution_task_id = t.execution_task_id
                      AND r.request_status = 'Pending'
                ) as pending_extension_count,
                (
                    SELECT MAX(r.requested_due_date)
                    FROM spares_execution_extension_requests r
                    WHERE r.execution_task_id = t.execution_task_id
                      AND r.request_status = 'Pending'
                ) as pending_extension_requested_due_date,
                d.department,
                u.title,
                u.first_name,
                u.last_name,
                COALESCE(last_up.last_update_at, t.updated_at, t.created_at) as last_activity_at,
                dep.task_code as dependency_task_code,
                dep.task_name as dependency_task_name,
                dep.task_status as dependency_task_status,
                CASE
                    WHEN t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed' THEN 1
                    ELSE 0
                END as dependency_blocked
            ")
            ->from('spares_execution_tasks t')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('system_users u', 'u.user_id = t.assigned_to', 'left')
            ->join('(' . $last_update_subquery . ') last_up', 'last_up.execution_task_id = t.execution_task_id', 'left', false)
            ->join('spares_execution_tasks dep', 'dep.execution_task_id = t.depends_on_task_id', 'left')
            ->where('t.execution_order_id', (int) $execution_order_id)
            ->order_by('t.sequence_no', 'ASC');
        $this->apply_scoped_department_filter('d', 't.department_id');

        return $this->db->get()->result();
    }

    public function get_task_counters($execution_order_id)
    {
        $this->db
            ->select("
                COUNT(*) as total_tasks,
                SUM(CASE WHEN t.task_status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN t.task_status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_tasks,
                SUM(CASE WHEN t.task_status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                SUM(CASE WHEN t.task_status = 'Open' THEN 1 ELSE 0 END) as open_tasks,
                SUM(CASE WHEN t.task_status = 'Pending' THEN 1 ELSE 0 END) as pending_tasks,
                SUM(CASE WHEN t.task_status = 'Blocked' OR (t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed') THEN 1 ELSE 0 END) as blocked_tasks,
                SUM(CASE WHEN t.task_status = 'On Hold' THEN 1 ELSE 0 END) as on_hold_tasks,
                SUM(CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND t.planned_end_date < CURDATE() THEN 1 ELSE 0 END) as overdue_tasks
            ")
            ->from('spares_execution_tasks t')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->join('spares_execution_tasks dep', 'dep.execution_task_id = t.depends_on_task_id', 'left')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->where('t.execution_order_id', (int) $execution_order_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        $row = $this->db->get()->row();

        return $row ? $row : (object) array(
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'cancelled_tasks' => 0,
            'in_progress_tasks' => 0,
            'open_tasks' => 0,
            'pending_tasks' => 0,
            'blocked_tasks' => 0,
            'on_hold_tasks' => 0,
            'overdue_tasks' => 0,
        );
    }

    public function get_extension_requests($execution_order_id, $status = null)
    {
        $this->db
            ->select("
                r.*,
                t.task_name,
                t.planned_end_date,
                req.title as requested_title,
                req.first_name as requested_first_name,
                req.last_name as requested_last_name,
                rev.title as reviewed_title,
                rev.first_name as reviewed_first_name,
                rev.last_name as reviewed_last_name
            ")
            ->from('spares_execution_extension_requests r')
            ->join('spares_execution_tasks t', 't.execution_task_id = r.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('system_users req', 'req.user_id = r.requested_by', 'left')
            ->join('system_users rev', 'rev.user_id = r.reviewed_by', 'left')
            ->where('t.execution_order_id', (int) $execution_order_id)
            ->order_by('r.requested_on', 'DESC');
        $this->apply_scoped_department_filter('d', 't.department_id');

        if (!empty($status)) {
            $this->db->where('r.request_status', $status);
        }

        return $this->db->get()->result();
    }

    public function get_task_updates($execution_order_id)
    {
        $this->db
            ->select("
                up.*,
                t.task_name,
                u.title,
                u.first_name,
                u.last_name
            ")
            ->from('spares_execution_task_updates up')
            ->join('spares_execution_tasks t', 't.execution_task_id = up.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('system_users u', 'u.user_id = up.added_by', 'left')
            ->where('t.execution_order_id', (int) $execution_order_id)
            ->order_by('up.added_on', 'DESC')
            ->limit(100);
        $this->apply_scoped_department_filter('d', 't.department_id');

        return $this->db->get()->result();
    }

    public function get_task_update_attachment($update_id)
    {
        $this->db
            ->select("
                up.update_id,
                up.attachment_name,
                up.attachment_original_name,
                t.execution_task_id,
                t.execution_order_id,
                eo.order_id
            ")
            ->from('spares_execution_task_updates up')
            ->join('spares_execution_tasks t', 't.execution_task_id = up.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->where('up.update_id', (int) $update_id)
            ->where('up.attachment_name IS NOT NULL', null, false)
            ->where("TRIM(COALESCE(up.attachment_name, '')) <>", '');
        $this->apply_scoped_department_filter('d', 't.department_id');

        return $this->db->get()->row();
    }

    public function get_recent_task_activity($limit = 20, $filters = array())
    {
        $this->db
            ->select("
                up.*,
                t.execution_task_id,
                t.task_name,
                t.task_status,
                eo.order_id,
                eo.workflow_type,
                eo.priority,
                c.company_name,
                o.op_no,
                CONCAT_WS(' ', u.title, u.first_name, u.last_name) as updated_by_name
            ")
            ->from('spares_execution_task_updates up')
            ->join('spares_execution_tasks t', 't.execution_task_id = up.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('system_users u', 'u.user_id = up.added_by', 'left');
        $this->apply_scoped_department_filter('d', 't.department_id');

        if (!empty($filters['manager_user_id'])) {
            $user_id = (int) $filters['manager_user_id'];
            $this->db->group_start()
                ->where('eo.marketing_owner_id', $user_id)
                ->or_where('eo.created_by', $user_id)
            ->group_end();
        }

        if (!empty($filters['workflow_type'])) {
            $this->db->where('eo.workflow_type', $filters['workflow_type']);
        }

        if (!empty($filters['priority'])) {
            $this->db->where('eo.priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('c.company_name', $search)
                ->or_like('o.op_no', $search)
                ->or_like('t.task_name', $search)
                ->or_like('eo.order_id', $search)
            ->group_end();
        }

        return $this->db
            ->order_by('up.added_on', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function get_user_task_queue($user_id, $filters = array())
    {
        $last_update_subquery = $this->get_task_last_update_subquery();

        $this->db
            ->select("
                t.*,
                CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND t.planned_end_date < CURDATE() THEN 1 ELSE 0 END as live_is_overdue,
                COALESCE(tm.extension_allowed, 1) as extension_allowed,
                (
                    SELECT COUNT(*)
                    FROM spares_execution_extension_requests r
                    WHERE r.execution_task_id = t.execution_task_id
                      AND r.request_status = 'Pending'
                ) as pending_extension_count,
                (
                    SELECT MAX(r.requested_due_date)
                    FROM spares_execution_extension_requests r
                    WHERE r.execution_task_id = t.execution_task_id
                      AND r.request_status = 'Pending'
                ) as pending_extension_requested_due_date,
                eo.order_id,
                eo.workflow_type,
                eo.commit_date,
                eo.priority,
                eo.execution_status,
                c.company_name,
                o.op_no,
                d.department,
                COALESCE(last_up.last_update_at, t.updated_at, t.created_at) as last_activity_at,
                dep.task_name as dependency_task_name,
                dep.task_status as dependency_task_status,
                COALESCE(tm.can_start_parallel, 0) as can_start_parallel,
                CASE
                    WHEN t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed' THEN 1
                    ELSE 0
                END as dependency_blocked
            ")
            ->from('spares_execution_tasks t')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('(' . $last_update_subquery . ') last_up', 'last_up.execution_task_id = t.execution_task_id', 'left', false)
            ->join('spares_execution_tasks dep', 'dep.execution_task_id = t.depends_on_task_id', 'left')
            ->where('t.assigned_to', (int) $user_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        if (!empty($filters['workflow_type'])) {
            $this->db->where('eo.workflow_type', $filters['workflow_type']);
        }

        if (!empty($filters['task_status'])) {
            $this->db->where('t.task_status', $filters['task_status']);
        }

        if (!empty($filters['show'])) {
            if ($filters['show'] === 'active') {
                $this->db->where_not_in('t.task_status', array('Completed', 'Cancelled'));
            } elseif ($filters['show'] === 'overdue') {
                $this->db->where_not_in('t.task_status', array('Completed', 'Cancelled'));
                $this->db->where('t.planned_end_date <', date('Y-m-d'));
            } elseif ($filters['show'] === 'stale') {
                $this->db->where_not_in('t.task_status', array('Completed', 'Cancelled'));
                $this->db->where("DATE(COALESCE(last_up.last_update_at, t.updated_at, t.created_at)) <=", date('Y-m-d', strtotime('-3 day')));
            } elseif ($filters['show'] === 'completed') {
                $this->db->where('t.task_status', 'Completed');
            } elseif ($filters['show'] === 'blocked') {
                $this->db->group_start()
                    ->where('t.task_status', 'Blocked')
                    ->or_where("(t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed')", null, false)
                ->group_end();
            } elseif ($filters['show'] === 'on_hold') {
                $this->db->where('t.task_status', 'On Hold');
            }
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('c.company_name', $search)
                ->or_like('o.op_no', $search)
                ->or_like('t.task_name', $search)
                ->or_like('eo.order_id', $search)
            ->group_end();
        }

        if (!empty($filters['show']) && $filters['show'] === 'stale') {
            $this->db->order_by('last_activity_at', 'ASC');
        } else {
            $this->db->order_by('live_is_overdue', 'DESC', false);
            $this->db->order_by('t.planned_end_date', 'ASC');
        }

        return $this->db
            ->order_by('eo.commit_date', 'ASC')
            ->limit(!empty($filters['limit']) ? (int) $filters['limit'] : 200)
            ->get()
            ->result();
    }

    public function get_user_task_queue_counts($user_id)
    {
        $last_update_subquery = $this->get_task_last_update_subquery();

        $this->db
            ->select("
                COUNT(*) as total_tasks,
                SUM(CASE WHEN t.task_status = 'Open' THEN 1 ELSE 0 END) as open_tasks,
                SUM(CASE WHEN t.task_status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                SUM(CASE WHEN t.task_status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN t.task_status = 'Blocked' OR (t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed') THEN 1 ELSE 0 END) as blocked_tasks,
                SUM(CASE WHEN t.task_status = 'On Hold' THEN 1 ELSE 0 END) as on_hold_tasks,
                SUM(CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND t.planned_end_date < CURDATE() THEN 1 ELSE 0 END) as overdue_tasks,
                SUM(CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND DATE(COALESCE(last_up.last_update_at, t.updated_at, t.created_at)) <= " . $this->db->escape(date('Y-m-d', strtotime('-3 day'))) . " THEN 1 ELSE 0 END) as stale_tasks
            ")
            ->from('spares_execution_tasks t')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->join('spares_execution_tasks dep', 'dep.execution_task_id = t.depends_on_task_id', 'left')
            ->join('(' . $last_update_subquery . ') last_up', 'last_up.execution_task_id = t.execution_task_id', 'left', false)
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->where('t.assigned_to', (int) $user_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        $row = $this->db->get()->row();

        return $row ? $row : (object) array(
            'total_tasks' => 0,
            'open_tasks' => 0,
            'in_progress_tasks' => 0,
            'completed_tasks' => 0,
            'blocked_tasks' => 0,
            'on_hold_tasks' => 0,
            'overdue_tasks' => 0,
            'stale_tasks' => 0,
        );
    }

    public function get_department_task_queue($department_id, $filters = array())
    {
        $last_update_subquery = $this->get_task_last_update_subquery();

        $this->db
            ->select("
                t.*,
                CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND t.planned_end_date < CURDATE() THEN 1 ELSE 0 END as live_is_overdue,
                COALESCE(tm.extension_allowed, 1) as extension_allowed,
                (
                    SELECT COUNT(*)
                    FROM spares_execution_extension_requests r
                    WHERE r.execution_task_id = t.execution_task_id
                      AND r.request_status = 'Pending'
                ) as pending_extension_count,
                (
                    SELECT MAX(r.requested_due_date)
                    FROM spares_execution_extension_requests r
                    WHERE r.execution_task_id = t.execution_task_id
                      AND r.request_status = 'Pending'
                ) as pending_extension_requested_due_date,
                eo.order_id,
                eo.workflow_type,
                eo.commit_date,
                eo.priority,
                eo.execution_status,
                c.company_name,
                o.op_no,
                d.department,
                COALESCE(last_up.last_update_at, t.updated_at, t.created_at) as last_activity_at,
                dep.task_name as dependency_task_name,
                dep.task_status as dependency_task_status,
                COALESCE(tm.can_start_parallel, 0) as can_start_parallel,
                CASE
                    WHEN t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed' THEN 1
                    ELSE 0
                END as dependency_blocked,
                CONCAT_WS(' ', u.title, u.first_name, u.last_name) as owner_name
            ")
            ->from('spares_execution_tasks t')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('(' . $last_update_subquery . ') last_up', 'last_up.execution_task_id = t.execution_task_id', 'left', false)
            ->join('spares_execution_tasks dep', 'dep.execution_task_id = t.depends_on_task_id', 'left')
            ->join('system_users u', 'u.user_id = t.assigned_to', 'left')
            ->where('t.department_id', (int) $department_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        if (!empty($filters['workflow_type'])) {
            $this->db->where('eo.workflow_type', $filters['workflow_type']);
        }

        if (!empty($filters['task_status'])) {
            $this->db->where('t.task_status', $filters['task_status']);
        }

        if (!empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $this->db->where('(t.assigned_to IS NULL OR t.assigned_to = 0)', null, false);
            } else {
                $this->db->where('t.assigned_to', (int) $filters['assigned_to']);
            }
        }

        if (!empty($filters['show'])) {
            if ($filters['show'] === 'active') {
                $this->db->where_not_in('t.task_status', array('Completed', 'Cancelled'));
            } elseif ($filters['show'] === 'overdue') {
                $this->db->where_not_in('t.task_status', array('Completed', 'Cancelled'));
                $this->db->where('t.planned_end_date <', date('Y-m-d'));
            } elseif ($filters['show'] === 'stale') {
                $this->db->where_not_in('t.task_status', array('Completed', 'Cancelled'));
                $this->db->where("DATE(COALESCE(last_up.last_update_at, t.updated_at, t.created_at)) <=", date('Y-m-d', strtotime('-3 day')));
            } elseif ($filters['show'] === 'completed') {
                $this->db->where('t.task_status', 'Completed');
            } elseif ($filters['show'] === 'blocked') {
                $this->db->group_start()
                    ->where('t.task_status', 'Blocked')
                    ->or_where("(t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed')", null, false)
                ->group_end();
            } elseif ($filters['show'] === 'on_hold') {
                $this->db->where('t.task_status', 'On Hold');
            } elseif ($filters['show'] === 'unassigned') {
                $this->db->where('(t.assigned_to IS NULL OR t.assigned_to = 0)', null, false);
            }
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('c.company_name', $search)
                ->or_like('o.op_no', $search)
                ->or_like('t.task_name', $search)
                ->or_like('eo.order_id', $search)
            ->group_end();
        }

        if (!empty($filters['show']) && $filters['show'] === 'stale') {
            $this->db->order_by('last_activity_at', 'ASC');
        } else {
            $this->db->order_by('live_is_overdue', 'DESC', false);
            $this->db->order_by('t.planned_end_date', 'ASC');
        }

        return $this->db
            ->order_by('eo.commit_date', 'ASC')
            ->limit(!empty($filters['limit']) ? (int) $filters['limit'] : 250)
            ->get()
            ->result();
    }

    public function get_department_task_queue_counts($department_id)
    {
        $last_update_subquery = $this->get_task_last_update_subquery();

        $row = $this->db
            ->select("
                COUNT(*) as total_tasks,
                SUM(CASE WHEN t.task_status = 'Open' THEN 1 ELSE 0 END) as open_tasks,
                SUM(CASE WHEN t.task_status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                SUM(CASE WHEN t.task_status = 'Completed' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN t.task_status = 'Blocked' OR (t.depends_on_task_id IS NOT NULL AND COALESCE(tm.can_start_parallel, 0) <> 1 AND COALESCE(dep.task_status, '') <> 'Completed') THEN 1 ELSE 0 END) as blocked_tasks,
                SUM(CASE WHEN t.task_status = 'On Hold' THEN 1 ELSE 0 END) as on_hold_tasks,
                SUM(CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND t.planned_end_date < CURDATE() THEN 1 ELSE 0 END) as overdue_tasks,
                SUM(CASE WHEN t.task_status NOT IN ('Completed', 'Cancelled') AND DATE(COALESCE(last_up.last_update_at, t.updated_at, t.created_at)) <= " . $this->db->escape(date('Y-m-d', strtotime('-3 day'))) . " THEN 1 ELSE 0 END) as stale_tasks,
                SUM(CASE WHEN t.assigned_to IS NULL OR t.assigned_to = 0 THEN 1 ELSE 0 END) as unassigned_tasks
            ")
            ->from('spares_execution_tasks t')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->join('spares_execution_tasks dep', 'dep.execution_task_id = t.depends_on_task_id', 'left')
            ->join('(' . $last_update_subquery . ') last_up', 'last_up.execution_task_id = t.execution_task_id', 'left', false)
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->where('t.department_id', (int) $department_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        $row = $this->db
            ->get()
            ->row();

        return $row ? $row : (object) array(
            'total_tasks' => 0,
            'open_tasks' => 0,
            'in_progress_tasks' => 0,
            'completed_tasks' => 0,
            'blocked_tasks' => 0,
            'on_hold_tasks' => 0,
            'overdue_tasks' => 0,
            'stale_tasks' => 0,
            'unassigned_tasks' => 0,
        );
    }

    public function get_overdue_tasks($limit = 20, $filters = array())
    {
        $this->db
            ->select("
                t.execution_task_id,
                t.execution_order_id,
                t.task_name,
                t.task_status,
                t.planned_end_date,
                DATEDIFF(CURDATE(), t.planned_end_date) as delay_days,
                eo.order_id,
                eo.workflow_type,
                eo.commit_date,
                eo.priority,
                c.company_name,
                o.op_no,
                d.department,
                CONCAT_WS(' ', u.title, u.first_name, u.last_name) as owner_name
            ", false)
            ->from('spares_execution_tasks t')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('system_users u', 'u.user_id = t.assigned_to', 'left')
            ->where_not_in('t.task_status', array('Completed', 'Cancelled'))
            ->where('t.planned_end_date <', date('Y-m-d'));
        $this->apply_scoped_department_filter('d', 't.department_id');

        if (!empty($filters['manager_user_id'])) {
            $user_id = (int) $filters['manager_user_id'];
            $this->db->group_start()
                ->where('eo.marketing_owner_id', $user_id)
                ->or_where('eo.created_by', $user_id)
            ->group_end();
        }

        return $this->db
            ->order_by('t.planned_end_date', 'ASC')
            ->order_by('eo.commit_date', 'ASC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function get_stale_tasks($limit = 20, $filters = array(), $stale_after_days = 3)
    {
        $stale_after_days = max(1, (int) $stale_after_days);
        $last_update_subquery = "
            SELECT execution_task_id, MAX(added_on) as last_update_at
            FROM spares_execution_task_updates
            GROUP BY execution_task_id
        ";

        $this->db
            ->select("
                t.execution_task_id,
                t.execution_order_id,
                t.task_name,
                t.task_status,
                t.planned_end_date,
                t.assigned_to,
                eo.order_id,
                eo.workflow_type,
                eo.commit_date,
                eo.priority,
                eo.marketing_owner_id,
                eo.created_by,
                c.company_name,
                o.op_no,
                d.department,
                CONCAT_WS(' ', u.title, u.first_name, u.last_name) as owner_name,
                COALESCE(up.last_update_at, t.updated_at, t.created_at) as last_activity_at,
                DATEDIFF(CURDATE(), DATE(COALESCE(up.last_update_at, t.updated_at, t.created_at))) as stale_days
            ", false)
            ->from('spares_execution_tasks t')
            ->join('(' . $last_update_subquery . ') up', 'up.execution_task_id = t.execution_task_id', 'left', false)
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('system_users u', 'u.user_id = t.assigned_to', 'left')
            ->where_not_in('t.task_status', array('Completed', 'Cancelled'))
            ->where("DATE(COALESCE(up.last_update_at, t.updated_at, t.created_at)) <=", date('Y-m-d', strtotime('-' . $stale_after_days . ' day')));
        $this->apply_scoped_department_filter('d', 't.department_id');

        if (!empty($filters['manager_user_id'])) {
            $user_id = (int) $filters['manager_user_id'];
            $this->db->group_start()
                ->where('eo.marketing_owner_id', $user_id)
                ->or_where('eo.created_by', $user_id)
            ->group_end();
        }

        if (!empty($filters['workflow_type'])) {
            $this->db->where('eo.workflow_type', $filters['workflow_type']);
        }

        if (!empty($filters['priority'])) {
            $this->db->where('eo.priority', $filters['priority']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('c.company_name', $search)
                ->or_like('o.op_no', $search)
                ->or_like('t.task_name', $search)
                ->or_like('eo.order_id', $search)
            ->group_end();
        }

        return $this->db
            ->order_by('stale_days', 'DESC', false)
            ->order_by('last_activity_at', 'ASC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function get_pending_extension_queue($limit = 20, $filters = array())
    {
        $this->db
            ->select("
                r.*,
                t.task_name,
                t.execution_order_id,
                eo.order_id,
                eo.workflow_type,
                eo.commit_date,
                eo.marketing_owner_id,
                eo.created_by,
                c.company_name,
                o.op_no,
                CONCAT_WS(' ', req.title, req.first_name, req.last_name) as requested_by_name
            ")
            ->from('spares_execution_extension_requests r')
            ->join('spares_execution_tasks t', 't.execution_task_id = r.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('system_users req', 'req.user_id = r.requested_by', 'left')
            ->where('r.request_status', 'Pending');
        $this->apply_scoped_department_filter('d', 't.department_id');

        if (!empty($filters['manager_user_id'])) {
            $user_id = (int) $filters['manager_user_id'];
            $this->db->group_start()
                ->where('eo.marketing_owner_id', $user_id)
                ->or_where('eo.created_by', $user_id)
            ->group_end();
        }

        return $this->db
            ->order_by('r.requested_on', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function get_unscheduled_orders($limit = 25, $filters = array())
    {
        $this->db
            ->select("
                so.order_id,
                so.order_value,
                so.order_date,
                so.status as spare_order_status,
                so.current_stage_id,
                so.marketing_person_id,
                o.op_no,
                o.op_type,
                c.company_name,
                po.po_no,
                pos.stage_name as current_progress_stage,
                CONCAT_WS(' ', u.title, u.first_name, u.last_name) as marketing_person_name
            ")
            ->from('spares_orders so')
            ->join('spares_execution_orders eo', 'eo.order_id = so.order_id', 'left')
            ->join('opportunities o', 'o.opportunity_id = so.opportunity_id', 'left')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->join('purchase_orders po', 'po.po_id = so.po_id', 'left')
            ->join('spares_order_stages pos', 'pos.stage_id = so.current_stage_id', 'left')
            ->join('system_users u', 'u.user_id = so.marketing_person_id', 'left')
            ->where('eo.execution_order_id IS NULL', null, false)
            ->where_not_in('so.status', array('Completed', 'Cancelled'));

        if (!empty($filters['marketing_owner_id'])) {
            $this->db->where('so.marketing_person_id', (int) $filters['marketing_owner_id']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $this->db->group_start()
                ->like('c.company_name', $search)
                ->or_like('o.op_no', $search)
                ->or_like('po.po_no', $search)
                ->or_like('so.order_id', $search)
            ->group_end();
        }

        return $this->db
            ->order_by('so.order_date', 'DESC')
            ->order_by('so.order_id', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function get_recent_alert_log($limit = 20)
    {
        return $this->db
            ->select("
                l.*,
                u.title,
                u.first_name,
                u.last_name
            ")
            ->from('spares_execution_alert_log l')
            ->join('system_users u', 'u.user_id = l.recipient_user_id', 'left')
            ->order_by('l.created_at', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function log_alert_dispatch_run($data)
    {
        return $this->db->insert('spares_execution_alert_runs', $data);
    }

    public function get_recent_alert_runs($limit = 20)
    {
        return $this->db
            ->select("
                r.*,
                u.title,
                u.first_name,
                u.last_name
            ")
            ->from('spares_execution_alert_runs r')
            ->join('system_users u', 'u.user_id = r.triggered_by', 'left')
            ->order_by('r.created_at', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function get_user_execution_alerts($user_id, $unread_only = false, $limit = 100)
    {
        $this->db
            ->select('n.id, n.user_id, n.title, n.message, n.type, n.reference_id, n.is_read, n.created_at, l.alert_type as alert_log_type, l.reference_id as alert_log_reference_id')
            ->from('app_notifications n')
            ->join('spares_execution_alert_log l', 'l.notification_id = n.id', 'left')
            ->where('n.user_id', (int) $user_id)
            ->where_in('n.type', $this->get_execution_notification_types());

        if ($unread_only) {
            $this->db->where('n.is_read', 0);
        }

        return $this->db
            ->order_by('n.created_at', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result();
    }

    public function count_user_execution_alerts($user_id, $unread_only = true)
    {
        $this->db
            ->from('app_notifications')
            ->where('user_id', (int) $user_id)
            ->where_in('type', $this->get_execution_notification_types());

        if ($unread_only) {
            $this->db->where('is_read', 0);
        }

        return (int) $this->db->count_all_results();
    }

    public function mark_user_execution_alert_read($notification_id, $user_id)
    {
        return $this->db
            ->where('id', (int) $notification_id)
            ->where('user_id', (int) $user_id)
            ->where_in('type', $this->get_execution_notification_types())
            ->update('app_notifications', array(
                'is_read' => 1,
            ));
    }

    public function mark_all_user_execution_alerts_read($user_id)
    {
        $this->db
            ->where('user_id', (int) $user_id)
            ->where_in('type', $this->get_execution_notification_types())
            ->where('is_read', 0)
            ->update('app_notifications', array(
                'is_read' => 1,
            ));

        return (int) $this->db->affected_rows();
    }

    public function can_user_review_extension_request($extension_request_id, $user_id)
    {
        $this->db
            ->select('eo.marketing_owner_id, eo.created_by')
            ->from('spares_execution_extension_requests r')
            ->join('spares_execution_tasks t', 't.execution_task_id = r.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->where('r.extension_request_id', (int) $extension_request_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        $row = $this->db->get()->row();

        if (!$row) {
            return false;
        }

        return ((int) $row->marketing_owner_id === (int) $user_id) || ((int) $row->created_by === (int) $user_id);
    }

    public function dispatch_execution_alerts($triggered_by)
    {
        $summary = array(
            'sent' => 0,
            'skipped' => 0,
            'unscheduled_sent' => 0,
            'overdue_sent' => 0,
            'stale_sent' => 0,
            'pending_extension_sent' => 0,
        );

        $unscheduled_orders = $this->get_unscheduled_orders(500);
        foreach ($unscheduled_orders as $order) {
            $recipient_user_id = !empty($order->marketing_person_id) ? (int) $order->marketing_person_id : 0;
            if ($recipient_user_id <= 0) {
                continue;
            }

            $message = 'SO-' . (int) $order->order_id . ' for ' . $order->company_name . ' is still waiting for execution task scheduling.';
            $sent = $this->send_execution_alert_once(
                'UNSCHEDULED_ORDER',
                (int) $order->order_id,
                $recipient_user_id,
                'Execution Schedule Pending',
                $message,
                'spares_execution_schedule_pending',
                (int) $order->order_id,
                (int) $triggered_by
            );

            if ($sent) {
                $summary['sent']++;
                $summary['unscheduled_sent']++;
            } else {
                $summary['skipped']++;
            }
        }

        $overdue_tasks = $this->get_overdue_task_alert_candidates();
        foreach ($overdue_tasks as $task) {
            $recipients = array_unique(array_filter(array(
                !empty($task->assigned_to) ? (int) $task->assigned_to : 0,
                !empty($task->marketing_owner_id) ? (int) $task->marketing_owner_id : 0,
                !empty($task->created_by) ? (int) $task->created_by : 0,
            )));

            foreach ($recipients as $recipient_user_id) {
                $message = $task->task_name . ' for SO-' . (int) $task->order_id . ' (' . $task->company_name . ') is overdue since ' . date('d M Y', strtotime($task->planned_end_date)) . '.';
                $sent = $this->send_execution_alert_once(
                    'OVERDUE_TASK',
                    (int) $task->execution_task_id,
                    (int) $recipient_user_id,
                    'Overdue Execution Task',
                    $message,
                    'spares_execution_overdue',
                    (int) $task->order_id,
                    (int) $triggered_by
                );

                if ($sent) {
                    $summary['sent']++;
                    $summary['overdue_sent']++;
                } else {
                    $summary['skipped']++;
                }
            }
        }

        $stale_tasks = $this->get_stale_task_alert_candidates();
        foreach ($stale_tasks as $task) {
            $recipients = array_unique(array_filter(array(
                !empty($task->assigned_to) ? (int) $task->assigned_to : 0,
                !empty($task->marketing_owner_id) ? (int) $task->marketing_owner_id : 0,
                !empty($task->created_by) ? (int) $task->created_by : 0,
            )));

            foreach ($recipients as $recipient_user_id) {
                $message = $task->task_name . ' for SO-' . (int) $task->order_id . ' (' . $task->company_name . ') has no update for ' . max(0, (int) $task->stale_days) . ' day(s).';
                $sent = $this->send_execution_alert_once(
                    'STALE_TASK',
                    (int) $task->execution_task_id,
                    (int) $recipient_user_id,
                    'Stale Execution Task',
                    $message,
                    'spares_execution_stale_task',
                    (int) $task->order_id,
                    (int) $triggered_by
                );

                if ($sent) {
                    $summary['sent']++;
                    $summary['stale_sent']++;
                } else {
                    $summary['skipped']++;
                }
            }
        }

        $pending_extensions = $this->get_pending_extension_alert_candidates();
        foreach ($pending_extensions as $request) {
            $recipients = array_unique(array_filter(array(
                !empty($request->marketing_owner_id) ? (int) $request->marketing_owner_id : 0,
                !empty($request->created_by) ? (int) $request->created_by : 0,
            )));

            foreach ($recipients as $recipient_user_id) {
                $message = 'Extension approval is pending for ' . $request->task_name . ' on SO-' . (int) $request->order_id . ' (' . $request->company_name . ').';
                $sent = $this->send_execution_alert_once(
                    'PENDING_EXTENSION',
                    (int) $request->extension_request_id,
                    (int) $recipient_user_id,
                    'Extension Approval Pending',
                    $message,
                    'spares_execution_extension_pending',
                    (int) $request->order_id,
                    (int) $triggered_by
                );

                if ($sent) {
                    $summary['sent']++;
                    $summary['pending_extension_sent']++;
                } else {
                    $summary['skipped']++;
                }
            }
        }

        return (object) $summary;
    }

    public function can_user_manage_execution_order($execution_order_id, $user_id)
    {
        $row = $this->db
            ->select('marketing_owner_id, created_by')
            ->from('spares_execution_orders')
            ->where('execution_order_id', (int) $execution_order_id)
            ->get()
            ->row();

        if (!$row) {
            return false;
        }

        return ((int) $row->marketing_owner_id === (int) $user_id) || ((int) $row->created_by === (int) $user_id);
    }

    public function bulk_update_execution_order_tasks($execution_order_id, $action_key, $remarks, $user_id)
    {
        $allowed_actions = array('hold_remaining', 'resume_held', 'cancel_remaining');
        if (!in_array($action_key, $allowed_actions, true)) {
            return false;
        }

        $this->db
            ->select('t.*, COALESCE(tm.can_start_parallel, 0) as can_start_parallel')
            ->from('spares_execution_tasks t')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->where('t.execution_order_id', (int) $execution_order_id)
            ->order_by('t.sequence_no', 'ASC');
        $this->apply_scoped_department_filter('d', 't.department_id');
        $tasks = $this->db->get()->result();

        if (empty($tasks)) {
            return false;
        }

        $dependency_status_map = array();
        foreach ($tasks as $task) {
            $dependency_status_map[(int) $task->execution_task_id] = $task->task_status;
        }

        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $affected = 0;

        $this->db->trans_start();

        foreach ($tasks as $task) {
            $previous_status = $task->task_status;
            $new_status = $previous_status;

            if ($action_key === 'hold_remaining') {
                if (in_array($previous_status, array('Completed', 'Cancelled', 'On Hold'), true)) {
                    continue;
                }

                $new_status = 'On Hold';
            } elseif ($action_key === 'resume_held') {
                if ($previous_status !== 'On Hold') {
                    continue;
                }

                $dependency_blocked = false;
                if (!empty($task->depends_on_task_id) && (int) $task->can_start_parallel !== 1) {
                    $dependency_status = isset($dependency_status_map[(int) $task->depends_on_task_id]) ? $dependency_status_map[(int) $task->depends_on_task_id] : '';
                    $dependency_blocked = ($dependency_status !== 'Completed');
                }

                if ($dependency_blocked) {
                    $new_status = 'Pending';
                } elseif ((int) $task->completion_percent > 0 || !empty($task->actual_start_date)) {
                    $new_status = 'In Progress';
                } else {
                    $new_status = 'Open';
                }
            } elseif ($action_key === 'cancel_remaining') {
                if (in_array($previous_status, array('Completed', 'Cancelled'), true)) {
                    continue;
                }

                $new_status = 'Cancelled';
            }

            if ($new_status === $previous_status) {
                continue;
            }

            $update_data = array(
                'task_status' => $new_status,
                'last_remark' => $remarks,
                'updated_by' => (int) $user_id,
                'updated_at' => $now,
                'is_overdue' => (!in_array($new_status, array('Completed', 'Cancelled'), true) && !empty($task->planned_end_date) && $task->planned_end_date < $today) ? 1 : 0,
            );

            if ($new_status === 'Cancelled') {
                $update_data['actual_end_date'] = $now;
            } elseif ($new_status === 'In Progress' && empty($task->actual_start_date)) {
                $update_data['actual_start_date'] = $now;
            } elseif (in_array($new_status, array('Open', 'Pending', 'On Hold'), true)) {
                $update_data['actual_end_date'] = null;
            }

            $this->db
                ->where('execution_task_id', (int) $task->execution_task_id)
                ->update('spares_execution_tasks', $update_data);

            $this->db->insert('spares_execution_task_updates', array(
                'execution_task_id' => (int) $task->execution_task_id,
                'update_type' => 'Bulk Status',
                'previous_status' => $previous_status,
                'new_status' => $new_status,
                'remarks' => $remarks,
                'added_by' => (int) $user_id,
                'added_on' => $now,
            ));

            $dependency_status_map[(int) $task->execution_task_id] = $new_status;
            $affected++;
        }

        if ($affected > 0) {
            $this->refresh_execution_order_status($execution_order_id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return false;
        }

        return $affected;
    }

    private function send_execution_alert_once($alert_type, $reference_id, $recipient_user_id, $title, $message, $notification_type, $app_reference_id, $triggered_by)
    {
        if ((int) $recipient_user_id <= 0) {
            return false;
        }

        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $existing = $this->db
            ->where('alert_type', $alert_type)
            ->where('reference_id', (int) $reference_id)
            ->where('recipient_user_id', (int) $recipient_user_id)
            ->where('alert_date', $today)
            ->get('spares_execution_alert_log')
            ->row();

        if ($existing) {
            return false;
        }

        $this->db->trans_start();

        $this->db->insert('app_notifications', array(
            'user_id' => (int) $recipient_user_id,
            'title' => $title,
            'message' => $message,
            'type' => $notification_type,
            'reference_id' => (int) $app_reference_id,
            'is_read' => 0,
            'created_at' => $now,
        ));

        $notification_id = $this->db->insert_id();

        $this->db->insert('spares_execution_alert_log', array(
            'alert_type' => $alert_type,
            'reference_id' => (int) $reference_id,
            'recipient_user_id' => (int) $recipient_user_id,
            'notification_type' => $notification_type,
            'notification_id' => $notification_id,
            'alert_message' => $message,
            'alert_date' => $today,
            'created_by' => (int) $triggered_by,
            'created_at' => $now,
        ));

        $this->db->trans_complete();

        return $this->db->trans_status() !== false;
    }

    private function get_overdue_task_alert_candidates()
    {
        $this->db
            ->select("
                t.execution_task_id,
                t.task_name,
                t.planned_end_date,
                t.assigned_to,
                eo.order_id,
                eo.marketing_owner_id,
                eo.created_by,
                c.company_name
            ")
            ->from('spares_execution_tasks t')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->where_not_in('t.task_status', array('Completed', 'Cancelled'))
            ->where('t.planned_end_date <', date('Y-m-d'))
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->order_by('t.planned_end_date', 'ASC');
        $this->apply_scoped_department_filter('d', 't.department_id');

        return $this->db->get()->result();
    }

    private function get_stale_task_alert_candidates()
    {
        return $this->get_stale_tasks(500, array(), 3);
    }

    private function get_pending_extension_alert_candidates()
    {
        $this->db
            ->select("
                r.extension_request_id,
                t.task_name,
                eo.order_id,
                eo.marketing_owner_id,
                eo.created_by,
                c.company_name
            ")
            ->from('spares_execution_extension_requests r')
            ->join('spares_execution_tasks t', 't.execution_task_id = r.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_orders eo', 'eo.execution_order_id = t.execution_order_id', 'inner')
            ->join('spares_orders so', 'so.order_id = eo.order_id', 'inner')
            ->join('spares_customers c', 'c.customer_id = so.customer_id', 'left')
            ->where('r.request_status', 'Pending')
            ->order_by('r.requested_on', 'DESC');
        $this->apply_scoped_department_filter('d', 't.department_id');

        return $this->db->get()->result();
    }

    public function update_execution_task($execution_task_id, $payload, $remark_text, $user_id, $attachment_data = null)
    {
        $this->db
            ->select('t.*, COALESCE(tm.can_start_parallel, 0) as can_start_parallel')
            ->from('spares_execution_tasks t')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->join('spares_execution_task_master tm', 'tm.task_master_id = t.task_master_id', 'left')
            ->where('t.execution_task_id', (int) $execution_task_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        $task = $this->db->get()->row();

        if (!$task) {
            return false;
        }

        $previous_status = $task->task_status;
        $new_status = isset($payload['task_status']) ? $payload['task_status'] : $previous_status;
        $now = date('Y-m-d H:i:s');
        $reopened_from_closed = in_array($previous_status, array('Completed', 'Cancelled'), true) && !in_array($new_status, array('Completed', 'Cancelled'), true);

        if (!empty($task->depends_on_task_id) && (int) $task->can_start_parallel !== 1 && in_array($new_status, array('Open', 'In Progress', 'Completed'), true)) {
            $dependency_task = $this->db
                ->select('task_status')
                ->from('spares_execution_tasks')
                ->where('execution_task_id', (int) $task->depends_on_task_id)
                ->get()
                ->row();

            if (!$dependency_task || $dependency_task->task_status !== 'Completed') {
                return false;
            }
        }

        if ($new_status === 'In Progress' && empty($task->actual_start_date)) {
            $payload['actual_start_date'] = $now;
        }

        if ($new_status === 'Cancelled') {
            $payload['actual_end_date'] = $now;
        }

        if ($new_status === 'Completed') {
            $payload['actual_start_date'] = !empty($task->actual_start_date) ? $task->actual_start_date : $now;
            $payload['actual_end_date'] = $now;
            $payload['completion_percent'] = 100;
        }

        if ($reopened_from_closed) {
            $payload['actual_end_date'] = null;

            if ($new_status === 'Pending') {
                $payload['completion_percent'] = 0;
            } elseif ($new_status === 'Open') {
                $payload['completion_percent'] = 0;
            } elseif ($new_status === 'In Progress' && (!array_key_exists('completion_percent', $payload) || (int) $payload['completion_percent'] >= 100)) {
                $payload['completion_percent'] = 1;
            } elseif (in_array($new_status, array('Blocked', 'On Hold'), true) && (!array_key_exists('completion_percent', $payload) || (int) $payload['completion_percent'] >= 100)) {
                $payload['completion_percent'] = 0;
            }
        }

        $payload['last_remark'] = $remark_text;
        $payload['updated_by'] = (int) $user_id;
        $payload['updated_at'] = $now;
        $payload['is_overdue'] = ($new_status !== 'Completed' && !empty($task->planned_end_date) && $task->planned_end_date < date('Y-m-d')) ? 1 : 0;

        $this->db->where('execution_task_id', (int) $execution_task_id);
        $updated = $this->db->update('spares_execution_tasks', $payload);

        if ($updated) {
            $has_attachment = !empty($attachment_data['attachment_name']);
            $owner_changed = array_key_exists('assigned_to', $payload) && (int) $payload['assigned_to'] !== (int) $task->assigned_to;
            $progress_changed = array_key_exists('completion_percent', $payload) && (int) $payload['completion_percent'] !== (int) $task->completion_percent;
            $update_type = 'Status';

            if ($owner_changed) {
                $update_type = 'Assignment';
            } elseif ($has_attachment && $new_status === $previous_status && !$progress_changed && !$owner_changed && trim($remark_text) === '') {
                $update_type = 'Attachment';
            } elseif ($new_status === $previous_status && !$progress_changed && trim($remark_text) !== '') {
                $update_type = 'Remark';
            }

            $this->db->insert('spares_execution_task_updates', array(
                'execution_task_id' => (int) $execution_task_id,
                'update_type' => $update_type,
                'previous_status' => $previous_status,
                'new_status' => $new_status,
                'remarks' => $remark_text,
                'attachment_name' => $has_attachment ? $attachment_data['attachment_name'] : null,
                'attachment_original_name' => $has_attachment ? $attachment_data['attachment_original_name'] : null,
                'added_by' => (int) $user_id,
                'added_on' => $now,
            ));

            if ($new_status === 'Completed' && $previous_status !== 'Completed') {
                $this->db
                    ->where('depends_on_task_id', (int) $execution_task_id)
                    ->where('task_status', 'Pending')
                    ->update('spares_execution_tasks', array(
                        'task_status' => 'Open',
                        'updated_by' => (int) $user_id,
                        'updated_at' => $now,
                    ));
            }

            $this->refresh_execution_order_status($task->execution_order_id);
        }

        return $updated;
    }

    public function create_extension_request($execution_task_id, $data)
    {
        $this->db
            ->select('t.*')
            ->from('spares_execution_tasks t')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->where('t.execution_task_id', (int) $execution_task_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        $task = $this->db->get()->row();

        if (!$task) {
            return false;
        }

        $insert = array(
            'execution_task_id' => (int) $execution_task_id,
            'current_due_date' => $task->planned_end_date,
            'requested_due_date' => $data['requested_due_date'],
            'reason' => $data['reason'],
            'request_status' => 'Pending',
            'requested_by' => (int) $data['requested_by'],
            'requested_on' => date('Y-m-d H:i:s'),
        );

        $created = $this->db->insert('spares_execution_extension_requests', $insert);

        if ($created) {
            $this->db->insert('spares_execution_task_updates', array(
                'execution_task_id' => (int) $execution_task_id,
                'update_type' => 'Extension Request',
                'remarks' => $data['reason'],
                'next_action_date' => $data['requested_due_date'],
                'added_by' => (int) $data['requested_by'],
                'added_on' => date('Y-m-d H:i:s'),
            ));
        }

        return $created;
    }

    public function review_extension_request($extension_request_id, $decision, $review_remarks, $reviewed_by)
    {
        $this->db
            ->select('r.*, t.execution_order_id, t.execution_task_id, t.task_status, t.planned_end_date as latest_planned_end_date')
            ->from('spares_execution_extension_requests r')
            ->join('spares_execution_tasks t', 't.execution_task_id = r.execution_task_id', 'inner')
            ->join('departments d', 'd.department_id = t.department_id', 'left')
            ->where('r.extension_request_id', (int) $extension_request_id);
        $this->apply_scoped_department_filter('d', 't.department_id');

        $request = $this->db->get()->row();

        if (!$request || $request->request_status !== 'Pending') {
            return false;
        }

        if (in_array($request->task_status, array('Completed', 'Cancelled'), true)) {
            return false;
        }

        if ($decision === 'Approved' && !empty($request->latest_planned_end_date) && $request->requested_due_date <= $request->latest_planned_end_date) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $this->db
            ->where('extension_request_id', (int) $extension_request_id)
            ->update('spares_execution_extension_requests', array(
                'request_status' => $decision,
                'reviewed_by' => (int) $reviewed_by,
                'reviewed_on' => $now,
                'review_remarks' => $review_remarks,
            ));

        if ($decision === 'Approved') {
            $this->db
                ->where('execution_task_id', (int) $request->execution_task_id)
                ->update('spares_execution_tasks', array(
                    'planned_end_date' => $request->requested_due_date,
                    'is_overdue' => ($request->requested_due_date < date('Y-m-d')) ? 1 : 0,
                    'updated_by' => (int) $reviewed_by,
                    'updated_at' => $now,
                ));
        }

        $this->db->insert('spares_execution_task_updates', array(
            'execution_task_id' => (int) $request->execution_task_id,
            'update_type' => 'Extension Decision',
            'remarks' => $review_remarks,
            'next_action_date' => $request->requested_due_date,
            'added_by' => (int) $reviewed_by,
            'added_on' => $now,
            'new_status' => $decision,
        ));

        $this->refresh_execution_order_status($request->execution_order_id);
        $this->db->trans_complete();

        return $this->db->trans_status() !== false;
    }

    public function refresh_execution_order_status($execution_order_id)
    {
        $today = date('Y-m-d');

        $this->db
            ->where('execution_order_id', (int) $execution_order_id)
            ->update('spares_execution_tasks', array('is_overdue' => 0));

        $this->db
            ->where('execution_order_id', (int) $execution_order_id)
            ->where('task_status !=', 'Completed')
            ->where('planned_end_date <', $today)
            ->update('spares_execution_tasks', array('is_overdue' => 1));

        $counters = $this->get_task_counters($execution_order_id);
        $status = 'Scheduled';
        $total_tasks = (int) $counters->total_tasks;
        $completed_tasks = (int) $counters->completed_tasks;
        $cancelled_tasks = (int) $counters->cancelled_tasks;
        $in_progress_tasks = (int) $counters->in_progress_tasks;
        $open_tasks = (int) $counters->open_tasks;
        $pending_tasks = (int) $counters->pending_tasks;
        $blocked_tasks = (int) $counters->blocked_tasks;
        $on_hold_tasks = (int) $counters->on_hold_tasks;
        $closed_tasks = $completed_tasks + $cancelled_tasks;
        $active_tasks = max(0, $total_tasks - $closed_tasks);

        if ($total_tasks > 0 && $cancelled_tasks === $total_tasks) {
            $status = 'Cancelled';
        } elseif ($total_tasks > 0 && $closed_tasks === $total_tasks) {
            $status = 'Completed';
        } elseif ($active_tasks > 0 && ($on_hold_tasks + $blocked_tasks) === $active_tasks && $in_progress_tasks === 0 && $open_tasks === 0 && $pending_tasks === 0) {
            $status = 'On Hold';
        } elseif ($in_progress_tasks > 0 || $completed_tasks > 0 || $open_tasks > 0 || $blocked_tasks > 0 || $on_hold_tasks > 0) {
            $status = 'In Progress';
        }

        $this->db
            ->where('execution_order_id', (int) $execution_order_id)
            ->update('spares_execution_orders', array(
                'execution_status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ));
    }
}
