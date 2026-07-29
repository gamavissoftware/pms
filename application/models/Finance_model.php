<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_model extends CI_Model {

    public function get_po_finance_data()
    {
        $this->db->select("
            p.id AS po_id,
            p.pono,
            p.company_name,
            p.podate,
            p.order_value,
            IFNULL(SUM(t.amount_received),0) AS delivered_amount,
            (p.order_value - IFNULL(SUM(t.amount_received),0)) AS pending_amount,
            CASE 
                WHEN COUNT(t.id) > 0 THEN 'Executed'
                ELSE 'Not Executed'
            END AS execution_status
        ");

        $this->db->from('poreceived p');

        $this->db->join(
            'task_department_wise_scheduling t',
            't.po_id = p.id AND t.task_status = 1',
            'left'
        );

        $this->db->group_by('p.id');
        $this->db->order_by('p.podate', 'DESC');

        return $this->db->get()->result();
    }


    public function get_finance_summary()
    {
        $subquery = $this->db
            ->select("
                p.id,
                p.order_value,
                IFNULL(SUM(t.amount_received),0) AS delivered_amount
            ")
            ->from('poreceived p')
            ->join(
                'task_department_wise_scheduling t',
                't.po_id = p.id AND t.task_status = 1',
                'left'
            )
            ->group_by('p.id')
            ->get_compiled_select();

        $query = $this->db->query("
            SELECT 
                SUM(order_value) AS total_po_value,
                SUM(delivered_amount) AS total_delivered,
                SUM(order_value - delivered_amount) AS total_pending
            FROM ($subquery) AS finance_data
        ");

        return $query->row();
    }

}