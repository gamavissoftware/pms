<?php 
class Ticket_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_ticket_history($ticket_id) {
    	$this->db->select('a.comment, a.added_on, b.title, b.first_name, b.last_name')->from('communication_chain a')->join('system_users b','a.added_by=b.user_id','left')->where('a.record_id', $ticket_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_tickets($start, $length, $search = "")
    {
        $this->db->select('
            a.id,
            a.ticket_id,
            CASE 
                WHEN a.department_id = 0 THEN "All" 
                ELSE d.department 
            END AS department_name,
            CASE 
                WHEN a.user_id = 0 THEN "All" 
                ELSE (SELECT GROUP_CONCAT(CONCAT(u.first_name, " ", u.last_name) SEPARATOR ", ") 
                      FROM system_users u 
                      WHERE FIND_IN_SET(u.user_id, a.user_id)) 
            END AS user_names,
            a.ticket,
            a.status,
            a.added_on
        ');
        $this->db->from('maintenance_support a');
        $this->db->join('departments d', 'a.department_id = d.department_id', 'left');

        // Apply search filter
        if (!empty($search)) {
            $this->db->group_start()
                     ->like('a.ticket_id', $search)
                     ->or_like('d.department', $search)
                     ->or_like('a.ticket', $search)
                     ->or_like('a.status', $search)
                     ->group_end();
        }

        // Pagination and limit
        $this->db->limit($length, $start);
        return $this->db->get()->result_array();
    }

    public function count_tickets($search = "")
    {
        $this->db->from('maintenance_support a');
        $this->db->join('departments d', 'a.department_id = d.department_id', 'left');

        if (!empty($search)) {
            $this->db->group_start()
                     ->like('a.ticket_id', $search)
                     ->or_like('d.department', $search)
                     ->or_like('a.ticket', $search)
                     ->or_like('a.status', $search)
                     ->group_end();
        }

        return $this->db->count_all_results();
    }
public function getMaintenanceSupportList() {
        // Join query to fetch data from maintenance_support, departments, and system_users
        $this->db->select('ms.*, d.department, su.first_name, su.last_name, su.email, su.contact_number');
        $this->db->from('maintenance_support ms');
        $this->db->join('departments d', 'ms.department_id = d.department_id', 'left');
        $this->db->join('system_users su', 'ms.user_id = su.user_id', 'left');
        $query = $this->db->get();

        return $query->result_array(); // Return data as an associative array
    }

public function getUnresolvedTickets($userId) {
    $this->db->select('a.id, a.help_ticket_no, a.remarks, a.added_on, b.df_no, c.task_name, d.title, d.first_name, d.last_name');
    $this->db->from('communication_ticket_system a');
    $this->db->join('df_release b', 'a.df_id = b.id', 'left');
    $this->db->join('task_management c', 'a.task_id = c.task_id', 'left');
    $this->db->join('system_users d', 'a.added_by = d.user_id', 'left');
    $this->db->where('a.user_id', $userId);
    $this->db->where('a.ticket_closed_by', 0);
    $this->db->where('a.updated_remarks', ''); // Exclude rows where updated_remarks is not empty
    $query = $this->db->get();

    return $query->result_array() ?? [];
}


public function getOverdueTasks($userId) {
    $this->db->select('tds.id, tm.task_name, tds.assigned_user, tds.end_date, d.df_no');
    $this->db->from('task_department_wise_scheduling tds');
    $this->db->join('task_management tm', 'tds.taskid = tm.task_id', 'left');
    $this->db->join('df_release d','tds.df_id=d.id','left');
    $this->db->where('tds.end_date <', date('Y-m-d')); // Overdue tasks with past end_date
    $this->db->where('tds.assigned_user', $userId); // Only tasks assigned to the current user
    $this->db->where('tds.task_status',0);
    $this->db->where('d.on_hold',0);
    $query = $this->db->get();

    return $query->result_array() ?? [];
}

 public function get_tickets_without_update($days = 2) {
        $threshold_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $this->db->select('t.*, u.email, m.task_name, d.df_no');
        $this->db->from('communication_ticket_system t');
        $this->db->join('task_management m','a.task_id=m.task_id','left');
        //$this->db->join('df_release d','a.df_id=d.id','left')
        //$this->db->join('system_users u', 't.user_id = u.user_id'); // Assuming a users table with HOD and higher person email
        $this->db->where('t.updated_on <', $threshold_date);
        $this->db->or_where('t.updated_on IS NULL');
        $this->db->where('t.updated_remarks', '');
        $this->db->where('t.added_on <', $threshold_date);
        return $this->db->get()->result_array();
    }

}
?>