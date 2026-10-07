<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_sheet_link_model extends CI_Model
{
    private $table = 'department_sheet_links';

    public function __construct()
    {
        parent::__construct();
        $this->db->db_debug = false;
    }

    public function ensure_schema()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `department_id` INT NOT NULL,
            `title` VARCHAR(150) NOT NULL,
            `sheet_url` TEXT NOT NULL,
            `description` VARCHAR(500) NULL,
            `created_by` INT NOT NULL,
            `updated_by` INT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            KEY `idx_department_sheet_links_department` (`department_id`, `is_active`),
            KEY `idx_department_sheet_links_created_by` (`created_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        return $this->db->query($sql) !== false && $this->db->table_exists($this->table);
    }

    public function user($user_id)
    {
        $query = $this->db->query(
            'SELECT u.user_id, u.first_name, u.last_name, u.business_location, u.department_id,
                    COALESCE(r.isadmin, 0) AS is_admin, d.department
             FROM system_users u
             LEFT JOIN user_role r ON r.user_role_id = u.user_role_id AND r.status = 1
             LEFT JOIN departments d ON d.department_id = u.department_id
             WHERE u.user_id = ? AND u.user_status = 1',
            array((int) $user_id)
        );

        return $query ? $query->row_array() : array();
    }

    public function departments($business_location)
    {
        $query = $this->db->select('department_id, department, departmenthead')
            ->from('departments')
            ->where('business_loc_id', (int) $business_location)
            ->where('status', 1)
            ->order_by('department', 'ASC')
            ->get();

        return $query ? $query->result_array() : array();
    }

    public function department($department_id, $business_location)
    {
        $query = $this->db->select('department_id, department, departmenthead')
            ->from('departments')
            ->where('department_id', (int) $department_id)
            ->where('business_loc_id', (int) $business_location)
            ->where('status', 1)
            ->limit(1)
            ->get();

        return $query ? $query->row_array() : array();
    }

    public function links($business_location, $department_id = 0, $search = '')
    {
        $this->db->select("l.*, d.department, d.departmenthead,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS created_by_name", false)
            ->from($this->table . ' l')
            ->join('departments d', 'd.department_id = l.department_id', 'inner')
            ->join('system_users u', 'u.user_id = l.created_by', 'left')
            ->where('d.business_loc_id', (int) $business_location)
            ->where('d.status', 1)
            ->where('l.is_active', 1);

        if ((int) $department_id > 0) {
            $this->db->where('l.department_id', (int) $department_id);
        }
        if ($search !== '') {
            $this->db->group_start()
                ->like('l.title', $search)
                ->or_like('l.description', $search)
                ->or_like('d.department', $search)
                ->group_end();
        }

        $query = $this->db->order_by('d.department', 'ASC')
            ->order_by('l.updated_at IS NULL', 'ASC', false)
            ->order_by('l.updated_at', 'DESC')
            ->order_by('l.created_at', 'DESC')
            ->get();

        return $query ? $query->result_array() : array();
    }

    public function find($id, $business_location)
    {
        $query = $this->db->select('l.*, d.department, d.departmenthead')
            ->from($this->table . ' l')
            ->join('departments d', 'd.department_id = l.department_id', 'inner')
            ->where('l.id', (int) $id)
            ->where('l.is_active', 1)
            ->where('d.business_loc_id', (int) $business_location)
            ->where('d.status', 1)
            ->limit(1)
            ->get();

        return $query ? $query->row_array() : array();
    }

    public function duplicate_exists($department_id, $sheet_url, $exclude_id = 0)
    {
        $this->db->from($this->table)
            ->where('department_id', (int) $department_id)
            ->where('sheet_url', $sheet_url)
            ->where('is_active', 1);
        if ((int) $exclude_id > 0) {
            $this->db->where('id !=', (int) $exclude_id);
        }
        return $this->db->count_all_results() > 0;
    }

    public function save($id, $values, $user_id)
    {
        $now = date('Y-m-d H:i:s');
        $data = array(
            'department_id' => (int) $values['department_id'],
            'title' => $values['title'],
            'sheet_url' => $values['sheet_url'],
            'description' => $values['description'],
            'updated_by' => (int) $user_id,
            'updated_at' => $now,
        );

        if ((int) $id > 0) {
            return $this->db->where('id', (int) $id)->where('is_active', 1)->update($this->table, $data);
        }

        $data['created_by'] = (int) $user_id;
        $data['created_at'] = $now;
        $data['is_active'] = 1;
        return $this->db->insert($this->table, $data);
    }

    public function archive($id, $user_id)
    {
        return $this->db->where('id', (int) $id)->where('is_active', 1)->update($this->table, array(
            'is_active' => 0,
            'updated_by' => (int) $user_id,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    public static function valid_google_sheet_url($url)
    {
        $url = trim((string) $url);
        if ($url === '' || strlen($url) > 2048 || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        $parts = @parse_url($url);
        if (!$parts || strtolower(isset($parts['scheme']) ? $parts['scheme'] : '') !== 'https') {
            return false;
        }
        if (strtolower(isset($parts['host']) ? $parts['host'] : '') !== 'docs.google.com') {
            return false;
        }
        if (isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) {
            return false;
        }
        return isset($parts['path']) && preg_match('#^/spreadsheets(?:/|$)#i', $parts['path']) === 1;
    }
}
