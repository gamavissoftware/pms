<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Df_dispatch_plan_model extends CI_Model
{
    private $plan_table = 'df_dispatch_plans';
    private $dependency_table = 'df_dispatch_dependencies';
    private $audit_table = 'df_dispatch_plan_audit_logs';

    /** Audit action used for dates the sync moved, so they stay distinguishable from a person's edit. */
    const SYNC_RESCHEDULE_ACTION = 'Rescheduled by PMS Sync';

    public function tables_ready()
    {
        if (!$this->db->table_exists($this->plan_table)
            || !$this->db->table_exists($this->dependency_table)
            || !$this->db->table_exists($this->audit_table)
            || !$this->db->table_exists('task_management_items')) {
            return false;
        }

        foreach (array(
            'machine_type', 'df_date', 'bom_date', 'loading_status', 'dispatch_schedule',
            'completion_date', 'actual_planned_completion_date', 'section_6_shortage', 'section_6_eol', 'purchase_shortage',
            'purchase_eol', 'bop_shortage', 'bop_eol', 'automation_status',
            'electrical_status', 'gear_box_status'
        ) as $field) {
            if (!$this->db->field_exists($field, $this->plan_table)) {
                return false;
            }
        }
        foreach (array('task_management_item_id', 'item_qty', 'pendency_from_date') as $field) {
            if (!$this->db->field_exists($field, $this->dependency_table)) {
                return false;
            }
        }

        return true;
    }

    public function financial_year_range($financial_year)
    {
        if (!preg_match('/^(\d{4})-(\d{2})$/', $financial_year, $matches)) {
            $start_year = (int) date('n') >= 4 ? (int) date('Y') : (int) date('Y') - 1;
        } else {
            $start_year = (int) $matches[1];
        }

        return array(
            'start' => $start_year . '-04-01',
            'end' => ($start_year + 1) . '-03-31',
            'label' => $start_year . '-' . substr((string) ($start_year + 1), -2)
        );
    }

    public function get_plans($financial_year, $month = '', $status = '', $search = '')
    {
        $range = $this->financial_year_range($financial_year);

        $this->db->select('
            p.*,
            d.df_no AS master_df_no,
            d.df_description AS master_model,
            d.added_on AS master_df_date,
            d.df_status AS master_df_status,
            DATE(d.added_on) AS source_df_date,
            (
                SELECT DATE(MAX(bom_task.task_completed_on))
                FROM task_department_wise_scheduling bom_task
                WHERE bom_task.df_id = p.df_id
                  AND bom_task.taskid = 24
                  AND bom_task.task_status = 1
                  AND bom_task.task_completed_on IS NOT NULL
                  AND bom_task.task_completed_on != "0000-00-00 00:00:00"
            ) AS source_bom_date,
            (
                SELECT DATE(MAX(completed_task.task_completed_on))
                FROM task_department_wise_scheduling completed_task
                WHERE completed_task.df_id = p.df_id
                  AND completed_task.task_status = 1
                  AND completed_task.task_completed_on IS NOT NULL
                  AND completed_task.task_completed_on != "0000-00-00 00:00:00"
            ) AS source_completion_date,
            (
                SELECT TRIM(CONCAT(COALESCE(designer.first_name, ""), " ", COALESCE(designer.last_name, "")))
                FROM task_department_wise_scheduling design_task
                LEFT JOIN system_users designer ON designer.user_id = design_task.assigned_user
                WHERE design_task.df_id = p.df_id AND design_task.taskid = 24
                ORDER BY design_task.task_status = 1 DESC, design_task.id DESC
                LIMIT 1
            ) AS source_design_owner,
            (
                SELECT TRIM(CONCAT(COALESCE(marketer.first_name, ""), " ", COALESCE(marketer.last_name, "")))
                FROM poreceived po
                LEFT JOIN system_users marketer ON marketer.user_id = po.added_by
                WHERE po.df_id = p.df_id
                ORDER BY po.id DESC
                LIMIT 1
            ) AS source_marketing_owner,
            creator.first_name AS created_by_name,
            updater.first_name AS updated_by_name,
            (SELECT COUNT(*) FROM df_dispatch_dependencies dep_all WHERE dep_all.plan_id = p.id) AS dependency_count,
            (SELECT COUNT(*) FROM df_dispatch_dependencies dep_open LEFT JOIN task_management_items tm_open ON tm_open.id=dep_open.task_management_item_id WHERE dep_open.plan_id=p.id AND ((tm_open.id IS NULL AND dep_open.status != "Closed") OR (tm_open.id IS NOT NULL AND CAST(tm_open.status AS BINARY) != CAST("COMPLETED" AS BINARY)))) AS open_dependency_count,
            (SELECT GROUP_CONCAT(CONCAT_WS("~~", dep.title, COALESCE(dep.item_qty,""), COALESCE(dep.pendency_from_date,""), COALESCE(owner.first_name,""), COALESCE(tm.id,""), COALESCE(tm.task_code,""), COALESCE(tm.status,dep.status)) ORDER BY dep.id SEPARATOR "||") FROM df_dispatch_dependencies dep LEFT JOIN task_management_items tm ON tm.id=dep.task_management_item_id LEFT JOIN system_users owner ON owner.user_id=dep.owner_user_id WHERE dep.plan_id=p.id AND dep.department="Section 6") AS section_6_items,
            (SELECT GROUP_CONCAT(CONCAT_WS("~~", dep.title, COALESCE(dep.item_qty,""), COALESCE(dep.pendency_from_date,""), COALESCE(owner.first_name,""), COALESCE(tm.id,""), COALESCE(tm.task_code,""), COALESCE(tm.status,dep.status)) ORDER BY dep.id SEPARATOR "||") FROM df_dispatch_dependencies dep LEFT JOIN task_management_items tm ON tm.id=dep.task_management_item_id LEFT JOIN system_users owner ON owner.user_id=dep.owner_user_id WHERE dep.plan_id=p.id AND dep.department="Section 6 EOL") AS section_6_eol_items,
            (SELECT GROUP_CONCAT(CONCAT_WS("~~", dep.title, COALESCE(dep.item_qty,""), COALESCE(dep.pendency_from_date,""), COALESCE(owner.first_name,""), COALESCE(tm.id,""), COALESCE(tm.task_code,""), COALESCE(tm.status,dep.status)) ORDER BY dep.id SEPARATOR "||") FROM df_dispatch_dependencies dep LEFT JOIN task_management_items tm ON tm.id=dep.task_management_item_id LEFT JOIN system_users owner ON owner.user_id=dep.owner_user_id WHERE dep.plan_id=p.id AND dep.department="Purchase") AS purchase_items,
            (SELECT GROUP_CONCAT(CONCAT_WS("~~", dep.title, COALESCE(dep.item_qty,""), COALESCE(dep.pendency_from_date,""), COALESCE(owner.first_name,""), COALESCE(tm.id,""), COALESCE(tm.task_code,""), COALESCE(tm.status,dep.status)) ORDER BY dep.id SEPARATOR "||") FROM df_dispatch_dependencies dep LEFT JOIN task_management_items tm ON tm.id=dep.task_management_item_id LEFT JOIN system_users owner ON owner.user_id=dep.owner_user_id WHERE dep.plan_id=p.id AND dep.department="Purchase EOL") AS purchase_eol_items,
            (SELECT GROUP_CONCAT(CONCAT_WS("~~", dep.title, COALESCE(dep.item_qty,""), COALESCE(dep.pendency_from_date,""), COALESCE(owner.first_name,""), COALESCE(tm.id,""), COALESCE(tm.task_code,""), COALESCE(tm.status,dep.status)) ORDER BY dep.id SEPARATOR "||") FROM df_dispatch_dependencies dep LEFT JOIN task_management_items tm ON tm.id=dep.task_management_item_id LEFT JOIN system_users owner ON owner.user_id=dep.owner_user_id WHERE dep.plan_id=p.id AND dep.department="BOP") AS bop_items,
            (SELECT GROUP_CONCAT(CONCAT_WS("~~", dep.title, COALESCE(dep.item_qty,""), COALESCE(dep.pendency_from_date,""), COALESCE(owner.first_name,""), COALESCE(tm.id,""), COALESCE(tm.task_code,""), COALESCE(tm.status,dep.status)) ORDER BY dep.id SEPARATOR "||") FROM df_dispatch_dependencies dep LEFT JOIN task_management_items tm ON tm.id=dep.task_management_item_id LEFT JOIN system_users owner ON owner.user_id=dep.owner_user_id WHERE dep.plan_id=p.id AND dep.department="BOP EOL") AS bop_eol_items
        ', false);
        $this->db->from($this->plan_table . ' p');
        $this->db->join('df_release d', 'd.id = p.df_id', 'left');
        $this->db->join('system_users creator', 'creator.user_id = p.created_by', 'left');
        $this->db->join('system_users updater', 'updater.user_id = p.updated_by', 'left');
        $this->db->where('p.planned_dispatch_date >=', $range['start']);
        $this->db->where('p.planned_dispatch_date <=', $range['end']);

        if ($month !== '' && preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month_start = $month . '-01';
            $month_end = date('Y-m-t', strtotime($month_start));
            $this->db->where('p.planned_dispatch_date >=', $month_start);
            $this->db->where('p.planned_dispatch_date <=', $month_end);
        }

        if ($status !== '') {
            $this->db->where('p.status', $status);
        }

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('p.df_no', $search);
            $this->db->or_like('p.model', $search);
            $this->db->or_like('p.machine_type', $search);
            $this->db->or_like('p.product', $search);
            $this->db->or_like('p.marketing_owner', $search);
            $this->db->group_end();
        }

        $this->db->order_by('p.planned_dispatch_date', 'ASC');
        $this->db->order_by('p.priority', 'ASC');

        return $this->db->get()->result();
    }

    public function get_summary($financial_year)
    {
        $range = $this->financial_year_range($financial_year);

        $this->db->select('
            COUNT(*) AS total,
            SUM(CASE WHEN status = "Dispatched" THEN 1 ELSE 0 END) AS dispatched,
            SUM(CASE WHEN status = "At Risk" THEN 1 ELSE 0 END) AS at_risk,
            SUM(CASE WHEN status != "Dispatched" AND planned_dispatch_date < CURDATE() THEN 1 ELSE 0 END) AS overdue
        ', false);
        $this->db->from($this->plan_table);
        $this->db->where('planned_dispatch_date >=', $range['start']);
        $this->db->where('planned_dispatch_date <=', $range['end']);

        $summary = $this->db->get()->row();

        $this->db->select('DATE_FORMAT(planned_dispatch_date, "%Y-%m") AS month_key, COUNT(*) AS total', false);
        $this->db->from($this->plan_table);
        $this->db->where('planned_dispatch_date >=', $range['start']);
        $this->db->where('planned_dispatch_date <=', $range['end']);
        $this->db->group_by('DATE_FORMAT(planned_dispatch_date, "%Y-%m")');
        $month_rows = $this->db->get()->result();

        $summary->months = array();
        foreach ($month_rows as $row) {
            $summary->months[$row->month_key] = (int) $row->total;
        }

        return $summary;
    }

    public function get_available_dfs()
    {
        return $this->db
            ->select('id, df_no, df_description, added_on')
            ->from('df_release')
            ->where('df_status', 0)
            ->order_by('df_sr_no', 'DESC')
            ->limit(500)
            ->get()
            ->result();
    }

    public function get_users()
    {
        return $this->db
            ->select('user_id, first_name, last_name')
            ->from('system_users')
            ->where('user_status', 1)
            ->order_by('first_name', 'ASC')
            ->get()
            ->result();
    }

    public function get_shortages_by_df_numbers($df_numbers)
    {
        if (!$this->db->table_exists($this->plan_table)
            || !$this->db->table_exists($this->dependency_table)
            || !$this->db->field_exists('task_management_item_id', $this->dependency_table)
            || !$this->db->field_exists('item_qty', $this->dependency_table)
            || !$this->db->field_exists('pendency_from_date', $this->dependency_table)) {
            return array();
        }

        $candidates = array();
        foreach ((array) $df_numbers as $df_number) {
            $raw = trim((string) $df_number);
            if ($raw === '') {
                continue;
            }
            $bare = preg_replace('/^DF[\s\-_]*/i', '', $raw);
            $candidates[] = $raw;
            $candidates[] = $bare;
            $candidates[] = 'DF-' . $bare;
            $candidates[] = 'DF ' . $bare;
        }
        $candidates = array_values(array_unique(array_filter($candidates)));
        if (empty($candidates)) {
            return array();
        }

        $rows = $this->db
            ->select('p.id AS plan_id, p.df_no, p.section_6_shortage, p.section_6_eol, p.purchase_shortage, p.purchase_eol, p.bop_shortage, p.bop_eol, dep.department, dep.title, dep.item_qty, dep.pendency_from_date, dep.status AS dispatch_status, dep.task_management_item_id, tm.task_code, tm.status AS task_status, owner.first_name AS owner_name, owner.last_name AS owner_last_name')
            ->from($this->plan_table . ' p')
            ->join($this->dependency_table . ' dep', 'dep.plan_id = p.id', 'left')
            ->join('task_management_items tm', 'tm.id = dep.task_management_item_id', 'left')
            ->join('system_users owner', 'owner.user_id = dep.owner_user_id', 'left')
            ->where_in('p.df_no', $candidates)
            ->order_by('p.id', 'DESC')
            ->order_by('dep.id', 'ASC')
            ->get()
            ->result();

        $result = array();
        foreach ($rows as $row) {
            $key = $this->normalize_df_number($row->df_no);
            if (!isset($result[$key])) {
                $result[$key] = array(
                    'plan_id' => (int) $row->plan_id,
                    'numbers' => array(
                        'Section 6' => $row->section_6_shortage,
                        'Section 6 EOL' => $row->section_6_eol,
                        'Purchase' => $row->purchase_shortage,
                        'Purchase EOL' => $row->purchase_eol,
                        'BOP' => $row->bop_shortage,
                        'BOP EOL' => $row->bop_eol
                    ),
                    'items' => array()
                );
            }
            if ((int) $result[$key]['plan_id'] !== (int) $row->plan_id) {
                continue;
            }
            if (!empty($row->department) && !empty($row->title)) {
                $status = !empty($row->task_status) ? strtoupper((string) $row->task_status) : strtoupper((string) $row->dispatch_status);
                if ($status === 'COMPLETED' || $status === 'CLOSED') {
                    $status = 'Closed';
                } elseif ($status === 'IN_PROGRESS' || $status === 'IN PROGRESS') {
                    $status = 'In Progress';
                } else {
                    $status = ucwords(strtolower(str_replace('_', ' ', $status)));
                }
                $result[$key]['items'][] = array(
                    'category' => $row->department,
                    'title' => $row->title,
                    'item_qty' => $row->item_qty,
                    'pendency_from_date' => $row->pendency_from_date,
                    'owner_name' => trim($row->owner_name . ' ' . $row->owner_last_name),
                    'task_id' => (int) $row->task_management_item_id,
                    'task_code' => $row->task_code,
                    'status' => $status
                );
            }
        }
        return $result;
    }

    public function normalize_df_number($df_number)
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', preg_replace('/^DF[\s\-_]*/i', '', trim((string) $df_number))));
    }

    public function format_user_name($first_name, $last_name = '')
    {
        return ucwords(strtolower(trim((string) $first_name . ' ' . (string) $last_name)));
    }

    public function active_user_exists($user_id)
    {
        return $this->db
            ->from('system_users')
            ->where('user_id', (int) $user_id)
            ->where('user_status', 1)
            ->limit(1)
            ->count_all_results() > 0;
    }

    public function sync_from_pms_schedule($financial_year, $user_id, $reset_priority_sentinel = true)
    {
        $range = $this->financial_year_range($financial_year);

        if ($reset_priority_sentinel) {
            $this->db->where('priority', 999)->update($this->plan_table, array('priority' => 0));
        }

        // The dispatch task (103) carries the DF's live dispatch date. It is re-dated
        // every time an upstream task slips, so the newest date on that task - not the
        // one the DF was released with - is the date the meeting has to plan against.
        $rows = $this->db
            ->select('d.id AS df_id, d.df_no, d.df_description, MAX(s.end_date) AS end_date', false)
            ->from('task_department_wise_scheduling s')
            ->join('df_release d', 'd.id = s.df_id')
            ->where('s.taskid', 103)
            ->where('s.end_date >=', $range['start'])
            ->where('s.end_date <=', $range['end'])
            ->group_by('d.id, d.df_no, d.df_description')
            ->order_by('end_date', 'ASC')
            ->get()
            ->result();

        $plans = $this->db
            ->select('id, df_id, df_no, planned_dispatch_date')
            ->from($this->plan_table)
            ->where('planned_dispatch_date >=', $range['start'])
            ->where('planned_dispatch_date <=', $range['end'])
            ->order_by('id', 'ASC')
            ->get()
            ->result();

        $plan_by_df_id = array();
        $plan_by_df_no = array();
        $plan_ids = array();
        foreach ($plans as $plan) {
            $plan_ids[] = (int) $plan->id;
            if ((int) $plan->df_id > 0 && !isset($plan_by_df_id[(int) $plan->df_id])) {
                $plan_by_df_id[(int) $plan->df_id] = $plan;
            }
            $key = $this->normalize_df_number($plan->df_no);
            if ($key !== '' && !isset($plan_by_df_no[$key])) {
                $plan_by_df_no[$key] = $plan;
            }
        }

        $hand_set_dates = $this->plans_with_hand_set_dispatch_date($plan_ids);

        $inserted = 0;
        $rescheduled = 0;
        $now = date('Y-m-d H:i:s');

        foreach ($rows as $row) {
            $df_id = (int) $row->df_id;
            $df_key = $this->normalize_df_number($row->df_no);
            $plan = null;
            if (isset($plan_by_df_id[$df_id])) {
                $plan = $plan_by_df_id[$df_id];
            } elseif ($df_key !== '' && isset($plan_by_df_no[$df_key])) {
                $plan = $plan_by_df_no[$df_key];
            }

            if (!$plan) {
                // The board now syncs on every page load, so re-check against the table
                // itself before inserting - two people opening the page at the same
                // moment must not each add their own copy of the DF.
                if ($this->plan_exists_for_df($df_id, $row->df_no, $range)) {
                    continue;
                }

                $this->db->insert($this->plan_table, array(
                    'df_id' => $df_id,
                    'df_no' => $row->df_no,
                    'priority' => 0,
                    'model' => $row->df_description,
                    'planned_dispatch_date' => $row->end_date,
                    'dispatch_schedule' => date('d/m/Y', strtotime($row->end_date)),
                    'completion_percent' => 0,
                    'status' => 'Planned',
                    'created_by' => $user_id,
                    'created_on' => $now,
                    'updated_by' => $user_id,
                    'updated_on' => $now
                ));

                if ($this->db->affected_rows() > 0) {
                    $synced_plan_id = $this->db->insert_id();
                    $this->db->insert($this->audit_table, array(
                        'plan_id' => $synced_plan_id,
                        'action' => 'Created by PMS Sync',
                        'field_name' => null,
                        'old_value' => null,
                        'new_value' => $row->df_no,
                        'changed_by' => (int) $user_id,
                        'changed_on' => $now
                    ));

                    // Keep the in-memory index in step so a DF that somehow appears
                    // twice in the schedule cannot be inserted twice in one pass.
                    $fresh = (object) array(
                        'id' => $synced_plan_id,
                        'df_id' => $df_id,
                        'df_no' => $row->df_no,
                        'planned_dispatch_date' => $row->end_date
                    );
                    if ($df_id > 0) {
                        $plan_by_df_id[$df_id] = $fresh;
                    }
                    if ($df_key !== '') {
                        $plan_by_df_no[$df_key] = $fresh;
                    }
                    $inserted++;
                }
                continue;
            }

            $update = array();

            // Rows added by hand carry no DF link, which is what made them invisible
            // to the next sync and left the DF duplicated on the board.
            if ($df_id > 0 && (int) $plan->df_id !== $df_id) {
                $update['df_id'] = $df_id;
            }

            // Follow the revised dispatch date so a delayed DF moves to the month it
            // is actually going to ship in - unless someone has set that date by hand,
            // in which case the meeting's decision wins.
            if ((string) $plan->planned_dispatch_date !== (string) $row->end_date
                && !isset($hand_set_dates[(int) $plan->id])) {
                $update['planned_dispatch_date'] = $row->end_date;
                $update['dispatch_schedule'] = date('d/m/Y', strtotime($row->end_date));
            }

            if (empty($update)) {
                continue;
            }

            $before = $this->get_plan((int) $plan->id);
            if (!$before) {
                continue;
            }

            $this->db->where('id', (int) $plan->id)->update($this->plan_table, array_merge($update, array(
                'updated_by' => $user_id,
                'updated_on' => $now
            )));
            $this->insert_change_logs((int) $plan->id, $before, $update, $user_id, self::SYNC_RESCHEDULE_ACTION);

            if (isset($update['planned_dispatch_date'])) {
                $plan->planned_dispatch_date = $update['planned_dispatch_date'];
                $rescheduled++;
            }
            if (isset($update['df_id'])) {
                $plan->df_id = $update['df_id'];
                $plan_by_df_id[$df_id] = $plan;
            }
        }

        return array('found' => count($rows), 'inserted' => $inserted, 'rescheduled' => $rescheduled);
    }

    private function plan_exists_for_df($df_id, $df_no, $range)
    {
        $this->db
            ->from($this->plan_table)
            ->where('planned_dispatch_date >=', $range['start'])
            ->where('planned_dispatch_date <=', $range['end']);

        if ((int) $df_id > 0) {
            $this->db->group_start()->where('df_id', (int) $df_id)->or_where('df_no', (string) $df_no)->group_end();
        } else {
            $this->db->where('df_no', (string) $df_no);
        }

        return $this->db->count_all_results() > 0;
    }

    /**
     * Plans whose dispatch date a person has changed. Those are left alone by the
     * sync; everything else keeps tracking the PMS dispatch task.
     */
    private function plans_with_hand_set_dispatch_date($plan_ids)
    {
        $hand_set = array();
        if (empty($plan_ids)) {
            return $hand_set;
        }

        $rows = $this->db
            ->distinct()
            ->select('plan_id')
            ->from($this->audit_table)
            ->where_in('plan_id', $plan_ids)
            ->where('field_name', 'planned_dispatch_date')
            ->where('action !=', self::SYNC_RESCHEDULE_ACTION)
            ->get()
            ->result();

        foreach ($rows as $row) {
            $hand_set[(int) $row->plan_id] = true;
        }

        return $hand_set;
    }

    /**
     * DFs released in this financial year that the board cannot show, with the
     * reason. Without this a DF whose dispatch task was never scheduled just goes
     * missing and there is nothing on screen to explain why.
     */
    public function get_dfs_missing_from_board($financial_year)
    {
        $range = $this->financial_year_range($financial_year);

        $sql = '
            SELECT d.id AS df_id, d.df_no, d.df_description, DATE(d.added_on) AS released_on,
                (
                    SELECT MAX(s.end_date)
                    FROM task_department_wise_scheduling s
                    WHERE s.df_id = d.id AND s.taskid = 103 AND s.end_date >= "1000-01-01"
                ) AS dispatch_task_date
            FROM df_release d
            WHERE d.df_status = 0
              AND d.added_on >= ?
              AND d.added_on <= ?
              AND NOT EXISTS (
                  SELECT 1 FROM ' . $this->plan_table . ' p
                  WHERE (p.df_id = d.id OR p.df_no = d.df_no)
                    AND p.planned_dispatch_date >= ?
                    AND p.planned_dispatch_date <= ?
              )
            ORDER BY d.added_on DESC
        ';

        return $this->db->query($sql, array(
            $range['start'] . ' 00:00:00',
            $range['end'] . ' 23:59:59',
            $range['start'],
            $range['end']
        ))->result();
    }

    public function save_plan($data, $plan_id = 0)
    {
        if ($plan_id > 0) {
            $before = $this->get_plan($plan_id);
            if (!$before) {
                return false;
            }

            $this->db->trans_start();
            $this->db->where('id', $plan_id)->update($this->plan_table, $data);
            $this->insert_change_logs($plan_id, $before, $data, isset($data['updated_by']) ? $data['updated_by'] : 0, 'Updated');
            $this->db->trans_complete();
            return $this->db->trans_status();
        }

        $this->db->trans_start();
        $this->db->insert($this->plan_table, $data);
        $new_id = $this->db->insert_id();
        if ($new_id) {
            $this->db->insert($this->audit_table, array(
                'plan_id' => $new_id,
                'action' => 'Created',
                'field_name' => null,
                'old_value' => null,
                'new_value' => isset($data['df_no']) ? $data['df_no'] : null,
                'changed_by' => isset($data['created_by']) ? (int) $data['created_by'] : 0,
                'changed_on' => date('Y-m-d H:i:s')
            ));
        }
        $this->db->trans_complete();
        return $this->db->trans_status() ? $new_id : false;
    }

    public function get_plan($plan_id)
    {
        return $this->db->get_where($this->plan_table, array('id' => $plan_id))->row();
    }

    public function update_plan_field($plan_id, $field, $value, $user_id)
    {
        $plan = $this->get_plan($plan_id);
        if (!$plan || !property_exists($plan, $field)) {
            return false;
        }
        $old_value = (string) $plan->{$field};
        if ($old_value === (string) $value) {
            return true;
        }

        $this->db->trans_start();
        $this->db
            ->where('id', (int) $plan_id)
            ->update($this->plan_table, array(
                $field => $value,
                'updated_by' => (int) $user_id,
                'updated_on' => date('Y-m-d H:i:s')
            ));
        $this->db->insert($this->audit_table, array(
            'plan_id' => (int) $plan_id,
            'action' => 'Inline Update',
            'field_name' => $field,
            'old_value' => $old_value,
            'new_value' => (string) $value,
            'changed_by' => (int) $user_id,
            'changed_on' => date('Y-m-d H:i:s')
        ));
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_audit_logs($plan_id, $limit = 200)
    {
        return $this->db
            ->select('log.*, u.first_name, u.last_name')
            ->from($this->audit_table . ' log')
            ->join('system_users u', 'u.user_id = log.changed_by', 'left')
            ->where('log.plan_id', (int) $plan_id)
            ->order_by('log.changed_on', 'DESC')
            ->order_by('log.id', 'DESC')
            ->limit(max(1, min(500, (int) $limit)))
            ->get()
            ->result();
    }

    private function insert_change_logs($plan_id, $before, $data, $user_id, $action)
    {
        $rows = array();
        $ignored = array('updated_by', 'updated_on', 'created_by', 'created_on');
        foreach ($data as $field => $new_value) {
            if (in_array($field, $ignored, true) || !property_exists($before, $field)) {
                continue;
            }
            $old_value = $before->{$field};
            if ((string) $old_value === (string) $new_value) {
                continue;
            }
            $rows[] = array(
                'plan_id' => (int) $plan_id,
                'action' => $action,
                'field_name' => $field,
                'old_value' => $old_value,
                'new_value' => $new_value,
                'changed_by' => (int) $user_id,
                'changed_on' => date('Y-m-d H:i:s')
            );
        }
        if (!empty($rows)) {
            $this->db->insert_batch($this->audit_table, $rows);
        }
    }

    public function get_dependencies($plan_id)
    {
        return $this->db
            ->select('dep.*, u.first_name AS owner_name, u.last_name AS owner_last_name, tm.task_code, tm.status AS task_management_status, CASE WHEN CAST(tm.status AS BINARY) = CAST("COMPLETED" AS BINARY) THEN "Closed" WHEN CAST(tm.status AS BINARY) = CAST("IN_PROGRESS" AS BINARY) THEN "In Progress" WHEN tm.status IS NOT NULL THEN "Open" ELSE dep.status END AS effective_status', false)
            ->from($this->dependency_table . ' dep')
            ->join('system_users u', 'u.user_id = dep.owner_user_id', 'left')
            ->join('task_management_items tm', 'tm.id = dep.task_management_item_id', 'left')
            ->where('dep.plan_id', $plan_id)
            ->order_by('effective_status = "Closed"', 'ASC', false)
            ->order_by('dep.due_date', 'ASC')
            ->get()
            ->result();
    }

    public function add_dependency($data)
    {
        $this->db->trans_start();
        $this->db->insert($this->dependency_table, $data);
        $dependency_id = $this->db->insert_id();
        if ($dependency_id) {
            $this->db->insert($this->audit_table, array(
                'plan_id' => (int) $data['plan_id'],
                'action' => !empty($data['task_management_item_id']) ? 'Task Delegated' : 'Shortage Item Submitted',
                'field_name' => 'dependencies',
                'old_value' => null,
                'new_value' => $data['department'] . ': ' . $data['title'],
                'changed_by' => (int) $data['created_by'],
                'changed_on' => $data['created_on']
            ));
        }
        $this->db->trans_complete();
        return $this->db->trans_status() ? $dependency_id : false;
    }

    public function add_dependencies($rows)
    {
        $this->db->trans_start();
        foreach ($rows as $row) {
            $this->db->insert($this->dependency_table, $row);
            $this->db->insert($this->audit_table, array(
                'plan_id' => (int) $row['plan_id'],
                'action' => !empty($row['task_management_item_id']) ? 'Task Delegated' : 'Shortage Item Submitted',
                'field_name' => 'dependencies',
                'old_value' => null,
                'new_value' => $row['department'] . ': ' . $row['title'] . (!empty($row['item_qty']) ? ' (Qty: ' . $row['item_qty'] . ')' : ''),
                'changed_by' => (int) $row['created_by'],
                'changed_on' => $row['created_on']
            ));
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function update_dependency($dependency_id, $data)
    {
        $dependency = $this->db->get_where($this->dependency_table, array('id' => (int) $dependency_id))->row();
        if (!$dependency) {
            return false;
        }

        $this->db->trans_start();
        $this->db
            ->where('id', $dependency_id)
            ->update($this->dependency_table, $data);
        if (!empty($dependency->task_management_item_id) && isset($data['status'])) {
            $task_status = $data['status'] === 'Closed' ? 'COMPLETED' : ($data['status'] === 'In Progress' ? 'IN_PROGRESS' : 'OPEN');
            $task_update = array(
                'status' => $task_status,
                'progress_percent' => $data['status'] === 'Closed' ? 100 : 0,
                'updated_on' => isset($data['updated_on']) ? $data['updated_on'] : date('Y-m-d H:i:s')
            );
            if ($task_status === 'COMPLETED') {
                $task_update['completed_on'] = $task_update['updated_on'];
                $task_update['completed_by_user_id'] = isset($data['updated_by']) ? (int) $data['updated_by'] : 0;
            } else {
                $task_update['completed_on'] = null;
                $task_update['completed_by_user_id'] = null;
            }
            $this->db->where('id', (int) $dependency->task_management_item_id)->update('task_management_items', $task_update);
        }
        if (isset($data['status']) && (string) $dependency->status !== (string) $data['status']) {
            $this->db->insert($this->audit_table, array(
                'plan_id' => (int) $dependency->plan_id,
                'action' => 'Task Status Updated',
                'field_name' => 'dependency_status',
                'old_value' => $dependency->title . ': ' . $dependency->status,
                'new_value' => $dependency->title . ': ' . $data['status'],
                'changed_by' => isset($data['updated_by']) ? (int) $data['updated_by'] : 0,
                'changed_on' => isset($data['updated_on']) ? $data['updated_on'] : date('Y-m-d H:i:s')
            ));
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
