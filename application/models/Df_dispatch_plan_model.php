<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Df_dispatch_plan_model extends CI_Model
{
    private $plan_table = 'df_dispatch_plans';
    private $dependency_table = 'df_dispatch_dependencies';

    public function tables_ready()
    {
        return $this->db->table_exists($this->plan_table)
            && $this->db->table_exists($this->dependency_table);
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
            creator.first_name AS created_by_name,
            updater.first_name AS updated_by_name,
            (SELECT COUNT(*) FROM df_dispatch_dependencies dep_all WHERE dep_all.plan_id = p.id) AS dependency_count,
            (SELECT COUNT(*) FROM df_dispatch_dependencies dep_open WHERE dep_open.plan_id = p.id AND dep_open.status != "Closed") AS open_dependency_count
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

    public function sync_from_pms_schedule($financial_year, $user_id)
    {
        $range = $this->financial_year_range($financial_year);
        $this->db->where('priority', 999)->update($this->plan_table, array('priority' => 0));

        $rows = $this->db
            ->select('d.id AS df_id, d.df_no, d.df_description, s.end_date')
            ->from('task_department_wise_scheduling s')
            ->join('df_release d', 'd.id = s.df_id')
            ->where('s.taskid', 103)
            ->where('s.end_date >=', $range['start'])
            ->where('s.end_date <=', $range['end'])
            ->where('s.end_date !=', '0000-00-00')
            ->order_by('s.end_date', 'ASC')
            ->get()
            ->result();

        $inserted = 0;
        foreach ($rows as $row) {
            $exists = $this->db
                ->select('id')
                ->from($this->plan_table)
                ->where('df_no', $row->df_no)
                ->where('planned_dispatch_date', $row->end_date)
                ->limit(1)
                ->get()
                ->num_rows() > 0;

            if ($exists) {
                continue;
            }

            $this->db->insert($this->plan_table, array(
                'df_id' => $row->df_id,
                'df_no' => $row->df_no,
                'priority' => 0,
                'model' => $row->df_description,
                'planned_dispatch_date' => $row->end_date,
                'completion_percent' => 0,
                'status' => 'Planned',
                'created_by' => $user_id,
                'created_on' => date('Y-m-d H:i:s'),
                'updated_by' => $user_id,
                'updated_on' => date('Y-m-d H:i:s')
            ));

            if ($this->db->affected_rows() > 0) {
                $inserted++;
            }
        }

        return array('found' => count($rows), 'inserted' => $inserted);
    }

    public function save_plan($data, $plan_id = 0)
    {
        if ($plan_id > 0) {
            return $this->db->where('id', $plan_id)->update($this->plan_table, $data);
        }

        $this->db->insert($this->plan_table, $data);
        return $this->db->insert_id();
    }

    public function get_plan($plan_id)
    {
        return $this->db->get_where($this->plan_table, array('id' => $plan_id))->row();
    }

    public function get_dependencies($plan_id)
    {
        return $this->db
            ->select('dep.*, u.first_name AS owner_name, u.last_name AS owner_last_name')
            ->from($this->dependency_table . ' dep')
            ->join('system_users u', 'u.user_id = dep.owner_user_id', 'left')
            ->where('dep.plan_id', $plan_id)
            ->order_by('dep.status = "Closed"', 'ASC', false)
            ->order_by('dep.due_date', 'ASC')
            ->get()
            ->result();
    }

    public function add_dependency($data)
    {
        $this->db->insert($this->dependency_table, $data);
        return $this->db->insert_id();
    }

    public function update_dependency($dependency_id, $data)
    {
        return $this->db
            ->where('id', $dependency_id)
            ->update($this->dependency_table, $data);
    }
}
