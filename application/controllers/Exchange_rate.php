<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Currency exchange rate - the desktop half of the app's Exchange Rate screen.
 *
 * Added 2026-09-17. Deliberately SELF-CONTAINED: a new controller and one new
 * view, and not one line changed in any existing PMS file. The shared layout
 * and nav-menu.php are edited by other people and have been overwritten
 * before, so a page that needs them is a page that breaks without warning.
 *
 * Reads and writes app_exchange_rate, the same table mobile/Api.php uses, so
 * a rate typed here appears in the app and the other way round. The rule for
 * who may write is repeated from _fx_can_update() rather than shared, because
 * that one lives in the mobile controller - if you change one, change both.
 */
class Exchange_rate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $session = $this->session->userdata('logged_in');

        if ($session == FALSE) {
            redirect(page_url);
        }

        $user_id = $this->session->userdata['logged_in']['user_id'];

        if (empty($user_id)) {
            redirect(site_url(), 'refresh');
        }
    }

    private function _table() { return 'app_exchange_rate'; }

    private function _me()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];

        /* Read from the table rather than trusted from wherever the session
           was built: the role decides admin, and the session does not
           reliably carry it. The department comes along because the page
           still shows it, not because the write rule turns on it. */
        $u = $this->db->select('user_id, user_role_id, department_id')
                      ->from('system_users')
                      ->where('user_id', $user_id)
                      ->get()
                      ->row_array();

        $role = $u ? (int) $u['user_role_id'] : 0;
        $dept = $u ? (int) $u['department_id'] : 0;

        $is_admin = ($role === 12 || in_array($user_id, array(139, 161, 61), true));

        return array(
            'id'         => $user_id,
            'role'       => $role,
            'department' => $dept,
            'is_admin'   => $is_admin,
            /* Hukam (63) and Vijay (92) by name, plus admins - NOT the
               Accounts department. Narrowed 2026-09-18; mirrors
               _fx_can_update() in mobile/Api.php, change one and change
               both. */
            'can_update' => (in_array($user_id, array(63, 92), true)
                             || $is_admin),
        );
    }

    private function _ensure()
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS ' . $this->_table() . ' (
                id            INT(11) NOT NULL AUTO_INCREMENT,
                rate_date     DATE NOT NULL,
                usd           DECIMAL(12,4) NOT NULL DEFAULT 0,
                eur           DECIMAL(12,4) NOT NULL DEFAULT 0,
                aed           DECIMAL(12,4) NOT NULL DEFAULT 0,
                remark        VARCHAR(190) NOT NULL DEFAULT "",
                updated_by    INT(11) NOT NULL DEFAULT 0,
                updated_at    DATETIME NULL,
                changed_count INT(11) NOT NULL DEFAULT 0,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_rate_date (rate_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function index()
    {
        date_default_timezone_set('Asia/Kolkata');
        $this->_ensure();

        $today = date('Y-m-d');

        $data['me']     = $this->_me();
        $data['today']  = $today;
        $data['rate']   = $this->db->where('rate_date', $today)
                                   ->get($this->_table())
                                   ->row_array();

        $data['history'] = $this->db->select('r.*, TRIM(CONCAT(u.first_name," ",COALESCE(u.last_name,""))) AS updated_name', FALSE)
                                    ->from($this->_table() . ' r')
                                    ->join('system_users u', 'u.user_id = r.updated_by', 'left')
                                    ->order_by('r.rate_date', 'DESC')
                                    ->limit(30)
                                    ->get()
                                    ->result_array();

        $data['flash'] = $this->session->flashdata('fx_msg');
        $data['flash_ok'] = $this->session->flashdata('fx_ok');

        $this->load->view('exchange_rate/index', $data);
    }

    public function save()
    {
        date_default_timezone_set('Asia/Kolkata');

        $me = $this->_me();

        if (empty($me['can_update'])) {
            $this->session->set_flashdata('fx_msg', 'You are not allowed to update the exchange rate.');
            redirect('Exchange_rate');
            return;
        }

        $usd = (float) $this->input->post('usd');
        $eur = (float) $this->input->post('eur');
        $aed = (float) $this->input->post('aed');

        /* Zero is an empty box submitted by accident, not a rate, and it
           would be published to the whole company as though it were real. */
        foreach (array('USD' => $usd, 'EUR' => $eur, 'AED' => $aed) as $code => $v) {
            if ($v <= 0) {
                $this->session->set_flashdata('fx_msg', 'Enter a rate for ' . $code . ' - it cannot be left at zero.');
                redirect('Exchange_rate');
                return;
            }

            if ($v > 10000) {
                $this->session->set_flashdata('fx_msg', $code . ' looks wrong (' . $v . '). Check the figure.');
                redirect('Exchange_rate');
                return;
            }
        }

        $this->_ensure();

        $date = date('Y-m-d');
        $now  = date('Y-m-d H:i:s');

        $existing = $this->db->where('rate_date', $date)->get($this->_table())->row_array();

        $row = array(
            'rate_date'  => $date,
            'usd'        => $usd,
            'eur'        => $eur,
            'aed'        => $aed,
            'remark'     => substr((string) $this->input->post('remark'), 0, 190),
            'updated_by' => $me['id'],
            'updated_at' => $now,
        );

        if ($existing) {
            $row['changed_count'] = (int) $existing['changed_count'] + 1;
            $this->db->where('id', (int) $existing['id'])->update($this->_table(), $row);
            $this->session->set_flashdata('fx_msg', 'Exchange rate updated.');
        } else {
            $row['changed_count'] = 0;
            $this->db->insert($this->_table(), $row);
            $this->session->set_flashdata('fx_msg', 'Exchange rate saved.');
        }

        $this->session->set_flashdata('fx_ok', 1);
        redirect('Exchange_rate');
    }
}
