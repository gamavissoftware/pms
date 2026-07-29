<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Master_profile_guard
{
    protected $CI;
    protected $master_write_access = null;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function is_master_read_only()
    {
        return !$this->role_can_manage_masters();
    }

    public function allow_only_methods(array $allowed_methods, $message = '')
    {
        if ($this->role_can_manage_masters()) {
            return;
        }

        $method = (string) $this->CI->router->fetch_method();
        if (in_array($method, $allowed_methods, true)) {
            return;
        }

        $this->deny($message);
    }

    public function block_methods(array $blocked_methods, $message = '')
    {
        if ($this->role_can_manage_masters()) {
            return;
        }

        $method = (string) $this->CI->router->fetch_method();
        if (!in_array($method, $blocked_methods, true)) {
            return;
        }

        $this->deny($message);
    }

    public function role_can_manage_masters()
    {
        if ($this->master_write_access !== null) {
            return $this->master_write_access;
        }

        $session = $this->CI->session->userdata('logged_in');
        $role_id = !empty($session['role']) ? (int) $session['role'] : 0;

        if ($role_id <= 0) {
            $this->master_write_access = false;
            return $this->master_write_access;
        }

        if (
            !$this->CI->db->table_exists('user_role') ||
            !$this->CI->db->field_exists('master_write_access', 'user_role')
        ) {
            $this->master_write_access = true;
            return $this->master_write_access;
        }

        $role = $this->CI->db->select('master_write_access')
            ->from('user_role')
            ->where('user_role_id', $role_id)
            ->limit(1)
            ->get()
            ->row();

        if (empty($role) || !isset($role->master_write_access)) {
            $this->master_write_access = true;
            return $this->master_write_access;
        }

        $this->master_write_access = ((string) $role->master_write_access === '1');
        return $this->master_write_access;
    }

    public function deny($message = '')
    {
        $message = $message !== ''
            ? $message
            : 'This EA profile can access the software but cannot change master records.';

        if ($this->CI->input->is_ajax_request()) {
            $this->CI->output->set_status_header(403);
            $this->CI->output->set_content_type('application/json');
            echo json_encode(array(
                'status' => 0,
                'message' => $message
            ));
            exit;
        }

        $this->CI->session->set_flashdata(
            'message',
            '<div class="alert alert-danger alert-dismissable">' . $message . '</div>'
        );
        redirect(page_url . 'Dashboard');
    }
}
