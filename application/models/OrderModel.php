<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderModel extends CI_Model {

    private function get_report_date_expression($alias = 'poreceived')
    {
        return "COALESCE(NULLIF(DATE(" . $alias . ".podate), '0000-00-00'), NULLIF(DATE(" . $alias . ".added_on), '0000-00-00'))";
    }

    public function get_orders() {
        // Join company_brand and poreceived to fetch order data
        $this->db->select('company_brand.name, company_brand.id, SUM(poreceived.order_value) as total_order_value, COUNT(poreceived.id) as order_count');
        $this->db->from('company_brand');
        $this->db->join('poreceived', 'poreceived.brand_tag = company_brand.id');
        $this->db->group_by('company_brand.name');
        $this->db->order_by('total_order_value','desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_brand_performance_rows($filters = array(), $limit = null)
    {
        $date_expression = $this->get_report_date_expression('p');

        $this->db->select(
            'COALESCE(p.brand_tag, 0) as id, ' .
            'COALESCE(NULLIF(company_brand.name, ""), "Unmapped Brand") as name, ' .
            'COUNT(p.id) as order_count, ' .
            'SUM(COALESCE(p.order_value, 0)) as total_order_value, ' .
            'AVG(COALESCE(p.order_value, 0)) as avg_order_value, ' .
            'MAX(' . $date_expression . ') as latest_order_date',
            false
        );
        $this->db->from('poreceived p');
        $this->db->join('company_brand', 'company_brand.id = p.brand_tag', 'left');
        $this->db->where($date_expression . ' >= ' . $this->db->escape($filters['effective_start_date']), null, false);
        $this->db->where($date_expression . ' <= ' . $this->db->escape($filters['effective_end_date']), null, false);
        $this->db->group_by('p.brand_tag');
        $this->db->order_by('total_order_value', 'DESC');
        $this->db->order_by('name', 'ASC');

        if ($limit !== null) {
            $this->db->limit((int) $limit);
        }

        return $this->db->get()->result_array();
    }

    public function get_brand_order_details($brand_tag, $filters = array())
    {
        $date_expression = $this->get_report_date_expression('poreceived');

        $this->db->select(
            'poreceived.company_name, poreceived.pono, ' .
            $date_expression . ' as podate, ' .
            'poreceived.order_value, c.title, c.first_name, c.last_name',
            false
        );
        $this->db->from('poreceived');
        $this->db->join('system_users c', 'poreceived.added_by=c.user_id', 'left');
        if ((int) $brand_tag === 0) {
            $this->db->where('(poreceived.brand_tag IS NULL OR poreceived.brand_tag = 0)', null, false);
        } else {
            $this->db->where('poreceived.brand_tag', $brand_tag);
        }

        if (!empty($filters['effective_start_date']) && !empty($filters['effective_end_date'])) {
            $this->db->where($date_expression . ' >= ' . $this->db->escape($filters['effective_start_date']), null, false);
            $this->db->where($date_expression . ' <= ' . $this->db->escape($filters['effective_end_date']), null, false);
        }

        $this->db->order_by($date_expression, 'DESC', false);
        return $this->db->get()->result_array();
    }

    public function get_order_details_by_brand($brand_tag) {
        // Fetch detailed order data by brand tag
        $this->db->select('poreceived.*, company_brand.name as company_name, c.title, c.first_name, c.last_name'); // Select necessary fields
        $this->db->from('poreceived');
        $this->db->join('company_brand', 'company_brand.id = poreceived.brand_tag');
        $this->db->join('system_users c','poreceived.added_by=c.user_id');
        $this->db->where('poreceived.brand_tag', $brand_tag);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_orders_by_date_range($year, $month) {

        $start_date =  $year."-".$month."-01";
        $end_date = $year."-".$month."-31";

        // Fetch order data based on date range
        $this->db->select('company_brand.name, poreceived.podate, company_brand.id, SUM(poreceived.order_value) as total_order_value, COUNT(poreceived.id) as order_count');
        $this->db->from('company_brand');
        $this->db->join('poreceived', 'poreceived.brand_tag = company_brand.id');
        $this->db->where('poreceived.podate >=', $start_date);
        $this->db->where('poreceived.podate <=', $end_date);
        $this->db->group_by('company_brand.name');
         $this->db->order_by('total_order_value','desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_order_details_by_brand_and_date($brand_tag, $start_date, $end_date) {
        // Fetch detailed order data by brand tag and date range
        $this->db->select('poreceived.*, company_brand.name as company_name, c.title, c.first_name, c.last_name'); // Select necessary fields
        $this->db->from('poreceived');
        $this->db->join('company_brand', 'company_brand.id = poreceived.brand_tag');
         $this->db->join('system_users c','poreceived.added_by=c.user_id');
        $this->db->where('poreceived.brand_tag', $brand_tag);
        $this->db->where('poreceived.podate >=', $start_date);
        $this->db->where('poreceived.podate <=', $end_date);
        $query = $this->db->get();
        return $query->result_array();
    }

     public function get_orders_by_date_range_between($start_date, $end_date) {

       

        // Fetch order data based on date range
        $this->db->select('company_brand.name, poreceived.podate, company_brand.id, SUM(poreceived.order_value) as total_order_value, COUNT(poreceived.id) as order_count');
        $this->db->from('company_brand');
        $this->db->join('poreceived', 'poreceived.brand_tag = company_brand.id');
        $this->db->where('poreceived.podate >=', $start_date);
        $this->db->where('poreceived.podate <=', $end_date);
        $this->db->group_by('company_brand.name');
         $this->db->order_by('total_order_value','desc');
        $query = $this->db->get();
        return $query->result_array();
    }
}
