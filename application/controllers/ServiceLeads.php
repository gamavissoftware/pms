<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ServiceLeads extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        // Load necessary models as per your Spares controller [cite: 1]
        $this->load->model('User_model','user');
        $this->load->model('Master_model','master');
        $this->load->model('Service_deployment_type_model', 'deployment_type_model');
        $this->load->model('Service_pi_model', 'service_pi_model');
        $this->load->model('Service_visit_execution_model', 'service_visit_execution_model');
        $this->load->model('Service_payment_request_model', 'service_payment_request_model');
        
        // if (!$this->session->userdata('logged_in')) { 
        //     redirect(page_url); 
        // }
        date_default_timezone_set("Asia/Kolkata"); 
    }

    private function ensure_service_visit_dependencies()
    {
        $this->service_visit_execution_model->ensure_tables();
    }

    private function ensure_service_payment_request_dependencies()
    {
        $this->service_payment_request_model->ensure_tables();
    }

    private function get_logged_in_service_user_id()
    {
        $logged_in = isset($this->session->userdata['logged_in'])
            ? $this->session->userdata['logged_in']
            : $this->session->userdata('logged_in');

        if (is_array($logged_in) && !empty($logged_in['user_id'])) {
            return (int) $logged_in['user_id'];
        }

        if (is_object($logged_in) && !empty($logged_in->user_id)) {
            return (int) $logged_in->user_id;
        }

        return 0;
    }

    private function get_service_visit_overview_module_id()
    {
        return 17;
    }

    private function get_service_visit_overview_submodule_name()
    {
        return 'SERVICE ENGINEER VISIT ASSIGNMENT OVERVIEW';
    }

    private function ensure_service_submodule($module_id, $submodule_name)
    {
        $existing = $this->db->select('id')
            ->from('submodule')
            ->where('moduleid', (int) $module_id)
            ->where('submodule', (string) $submodule_name)
            ->limit(1)
            ->get()
            ->row();

        if (!empty($existing)) {
            return (int) $existing->id;
        }

        $insert_data = [
            'moduleid' => $module_id,
            'submodule' => $submodule_name,
            'status' => 1,
            'addedOn' => date('Y-m-d H:i:s'),
        ];

        if ($this->db->field_exists('dynachem', 'submodule')) {
            $insert_data['dynachem'] = 0;
        }

        if ($this->db->field_exists('shubhampack', 'submodule')) {
            $insert_data['shubhampack'] = 2;
        }

        $this->db->insert('submodule', $insert_data);

        return (int) $this->db->insert_id();
    }

    private function ensure_service_visit_overview_submodule()
    {
        return $this->ensure_service_submodule(
            $this->get_service_visit_overview_module_id(),
            $this->get_service_visit_overview_submodule_name()
        );
    }

    private function get_service_payment_request_submodule_name()
    {
        return 'SERVICE PAYMENT REQUESTS';
    }

    private function ensure_service_payment_request_submodule()
    {
        return $this->ensure_service_submodule(
            $this->get_service_visit_overview_module_id(),
            $this->get_service_payment_request_submodule_name()
        );
    }

    private function get_service_payment_approval_submodule_name()
    {
        return 'SERVICE PAYMENT APPROVALS';
    }

    private function ensure_service_payment_approval_submodule()
    {
        return $this->ensure_service_submodule(
            $this->get_service_visit_overview_module_id(),
            $this->get_service_payment_approval_submodule_name()
        );
    }

    private function current_user_has_submodule_access($module_id, $submodule_id)
    {
        $user_id = $this->get_logged_in_service_user_id();
        if ($user_id <= 0 || (int) $submodule_id <= 0) {
            return false;
        }

        return $this->db->select('id')
            ->from('module_capablity')
            ->where('role_id', $user_id)
            ->where('moduleid', (int) $module_id)
            ->where('submoduleid', (int) $submodule_id)
            ->where('submodule_access', '1')
            ->limit(1)
            ->get()
            ->num_rows() > 0;
    }

    private function current_user_has_module_access($module_id)
    {
        $user_id = $this->get_logged_in_service_user_id();
        if ($user_id <= 0 || (int) $module_id <= 0) {
            return false;
        }

        return $this->db->select('id')
            ->from('module_access')
            ->where('role_id', $user_id)
            ->where('moduleid', (int) $module_id)
            ->where('access', '1')
            ->limit(1)
            ->get()
            ->num_rows() > 0;
    }

    private function current_user_is_admin()
    {
        $admin_flag = isset($this->session->userdata['logged_in']['adminuser'])
            ? (int) $this->session->userdata['logged_in']['adminuser']
            : 0;

        return $admin_flag === 1;
    }

    private function get_logged_in_service_user_context()
    {
        $logged_in = isset($this->session->userdata['logged_in'])
            ? $this->session->userdata['logged_in']
            : $this->session->userdata('logged_in');

        if (is_array($logged_in)) {
            return $logged_in;
        }

        if (is_object($logged_in)) {
            return (array) $logged_in;
        }

        return [];
    }

    private function get_service_hod_user()
    {
        return $this->db->select("
                t.team_leader as user_id,
                t.team_name,
                u.email,
                u.contact_number,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as full_name
            ")
            ->from('prestogroup_teams t')
            ->join('system_users u', 'u.user_id = t.team_leader', 'left')
            ->where('t.business_loc_id', 2)
            ->where('t.department_id', 22)
            ->where('t.status', 1)
            ->order_by('t.team_id', 'ASC')
            ->limit(1)
            ->get()
            ->row();
    }

    private function current_user_is_service_hod()
    {
        if ($this->current_user_is_admin()) {
            return true;
        }

        $hod = $this->get_service_hod_user();
        if (empty($hod) || empty($hod->user_id)) {
            return false;
        }

        return (int) $hod->user_id === $this->get_logged_in_service_user_id();
    }

    private function current_user_can_raise_service_payment_requests()
    {
        if ($this->current_user_is_admin()) {
            return true;
        }

        $submodule_id = $this->ensure_service_payment_request_submodule();
        return $this->current_user_has_submodule_access($this->get_service_visit_overview_module_id(), $submodule_id);
    }

    private function current_user_can_approve_service_payment_requests()
    {
        if ($this->current_user_is_admin()) {
            return true;
        }

        $submodule_id = $this->ensure_service_payment_approval_submodule();
        return $this->current_user_has_submodule_access($this->get_service_visit_overview_module_id(), $submodule_id);
    }

    private function require_service_payment_request_access($approval_only = false)
    {
        $allowed = $approval_only
            ? $this->current_user_can_approve_service_payment_requests()
            : $this->current_user_can_raise_service_payment_requests();

        if ($allowed) {
            return;
        }

        show_error(
            $approval_only
                ? 'You do not have permission to approve service payment requests.'
                : 'You do not have permission to access service payment requests.',
            403,
            'Access Denied'
        );
    }

    private function get_service_payment_permission_snapshot()
    {
        return [
            'can_create' => $this->current_user_can_raise_service_payment_requests(),
            'can_approve' => $this->current_user_can_approve_service_payment_requests(),
        ];
    }

    private function ensure_service_payment_request_read_access()
    {
        $permissions = $this->get_service_payment_permission_snapshot();

        if (!empty($permissions['can_create']) || !empty($permissions['can_approve'])) {
            return;
        }

        show_error(
            'You do not have permission to access service payment requests.',
            403,
            'Access Denied'
        );
    }

    private function can_current_user_view_service_payment_request($request, $payment_permissions = null)
    {
        if (empty($request)) {
            return false;
        }

        if (!is_array($payment_permissions)) {
            $payment_permissions = $this->get_service_payment_permission_snapshot();
        }

        if (!empty($payment_permissions['can_approve'])) {
            return true;
        }

        $current_user_id = $this->get_logged_in_service_user_id();
        if ($current_user_id <= 0) {
            return false;
        }

        return in_array($current_user_id, [
            (int) $request->created_by,
            (int) $request->requested_for_user_id,
            (int) $request->engineer_id,
        ], true);
    }

    private function get_service_payment_request_type_options()
    {
        return [
            'FOC',
            'Included with Machine',
            'Order Won',
        ];
    }

    private function get_default_service_payment_request_type()
    {
        $request_types = $this->get_service_payment_request_type_options();

        return !empty($request_types) ? $request_types[0] : 'FOC';
    }

    private function sanitize_service_payment_request_type($request_type, $fallback = null)
    {
        $request_type = trim((string) $request_type);
        $allowed_request_types = $this->get_service_payment_request_type_options();

        if (in_array($request_type, $allowed_request_types, true)) {
            return $request_type;
        }

        return $fallback === null ? $this->get_default_service_payment_request_type() : $fallback;
    }

    private function is_service_payment_order_won_type($request_type)
    {
        return trim((string) $request_type) === 'Order Won';
    }

    private function is_service_payment_foc_type($request_type)
    {
        return strtoupper(trim((string) $request_type)) === 'FOC';
    }

    private function get_service_payment_status_options()
    {
        return [
            'pending' => 'Pending HOD Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'all' => 'All Requests',
        ];
    }

    private function build_service_payment_request_kpi($requests)
    {
        $kpi = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'requested_value' => 0,
            'approved_value' => 0,
        ];

        foreach ($requests as $request) {
            $status = strtolower(trim((string) $request->status));
            $amount = (float) ($request->amount ?? 0);

            $kpi['total']++;
            $kpi['requested_value'] += $amount;

            if ($status === 'approved') {
                $kpi['approved']++;
                $kpi['approved_value'] += $amount;
            } elseif ($status === 'rejected') {
                $kpi['rejected']++;
            } else {
                $kpi['pending']++;
            }
        }

        return $kpi;
    }

    private function get_service_payment_won_orders()
    {
        $sql = "
            SELECT
                so.opportunity_id,
                so.op_no,
                so.customer_id,
                COALESCE(
                    NULLIF(sc.contact_person, ''),
                    NULLIF(cd.customer_name, ''),
                    COALESCE(sc.company_name, cd.company_name)
                ) as customer_name,
                COALESCE(sc.company_name, cd.company_name) as company_name,
                spo.id as po_id,
                spo.po_number,
                spo.po_date,
                spo.po_amount
            FROM service_opportunities so
            INNER JOIN service_purchase_orders spo
                ON spo.id = (
                    SELECT MAX(sp2.id)
                    FROM service_purchase_orders sp2
                    WHERE sp2.opportunity_id = so.opportunity_id
                )
            LEFT JOIN spares_customers sc
                ON sc.customer_id = so.customer_id
            LEFT JOIN customer_detail cd
                ON cd.id = so.customer_id
            ORDER BY so.opportunity_id DESC
        ";

        return $this->db->query($sql)->result();
    }

    private function get_service_payment_visit_options($opportunity_id = 0)
    {
        $this->db->select("
            v.visit_id,
            v.opportunity_id,
            v.engineer_id,
            v.start_date,
            v.end_date,
            v.visit_type,
            v.visit_status,
            so.op_no,
            COALESCE(sc.company_name, cd.company_name) as company_name,
            TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as engineer_name
        ");
        $this->db->from('service_engineer_visits v');
        $this->db->join('service_opportunities so', 'so.opportunity_id = v.opportunity_id', 'inner');
        $this->db->join(
            'service_purchase_orders spo',
            'spo.id = (SELECT MAX(sp2.id) FROM service_purchase_orders sp2 WHERE sp2.opportunity_id = so.opportunity_id)',
            'inner'
        );
        $this->db->join('system_users u', 'u.user_id = v.engineer_id', 'left');
        $this->db->join('spares_customers sc', 'sc.customer_id = so.customer_id', 'left');
        $this->db->join('customer_detail cd', 'cd.id = so.customer_id', 'left');

        if ($opportunity_id > 0) {
            $this->db->where('v.opportunity_id', $opportunity_id);
        }

        $this->db->order_by('v.start_date', 'DESC');
        $this->db->order_by('v.visit_id', 'DESC');

        return $this->db->get()->result();
    }

    private function get_service_payment_request_context_snapshot($opportunity_id = 0, $visit_id = 0)
    {
        $orders = $this->get_service_payment_won_orders();
        $order = null;

        foreach ($orders as $row) {
            if ((int) $row->opportunity_id === (int) $opportunity_id) {
                $order = $row;
                break;
            }
        }

        $visit = null;
        if ($visit_id > 0) {
            $visit_rows = $this->get_service_payment_visit_options($opportunity_id);
            foreach ($visit_rows as $visit_row) {
                if ((int) $visit_row->visit_id === (int) $visit_id) {
                    $visit = $visit_row;
                    if ($order === null) {
                        $opportunity_id = (int) $visit_row->opportunity_id;
                        $order = $this->get_service_payment_request_context_snapshot($opportunity_id, 0)['order'];
                    }
                    break;
                }
            }
        }

        return [
            'order' => $order,
            'visit' => $visit,
        ];
    }

    private function upload_service_payment_request_attachment($field_name)
    {
        if (empty($_FILES[$field_name]) || trim((string) $_FILES[$field_name]['name']) === '') {
            return [
                'success' => true,
                'file_name' => '',
            ];
        }

        $config = [
            'upload_path' => './uploads/service_payment_requests/',
            'allowed_types' => 'pdf|jpg|jpeg|png|doc|docx|xls|xlsx',
            'encrypt_name' => true,
            'max_size' => 10240,
        ];

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($field_name)) {
            return [
                'success' => false,
                'error' => strip_tags($this->upload->display_errors()),
            ];
        }

        $file_data = $this->upload->data();

        return [
            'success' => true,
            'file_name' => $file_data['file_name'],
        ];
    }

    private function create_service_payment_notification($user_id, $title, $message, $request_id)
    {
        if ((int) $user_id <= 0) {
            return;
        }

        $this->db->insert('app_notifications', [
            'user_id' => (int) $user_id,
            'title' => $title,
            'message' => $message,
            'type' => 'service_payment_request',
            'reference_id' => (int) $request_id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function current_user_has_service_visit_overview_access()
    {
        $module_id = $this->get_service_visit_overview_module_id();
        $overview_submodule_id = $this->ensure_service_visit_overview_submodule();

        if ($this->current_user_is_admin()) {
            return true;
        }

        return $this->current_user_has_submodule_access($module_id, $overview_submodule_id);
    }

    private function require_service_visit_overview_access()
    {
        if ($this->current_user_has_service_visit_overview_access()) {
            return;
        }

        show_error('You do not have permission to view the engineer visit assignment overview.', 403, 'Access Denied');
    }

    private function get_service_scheduler_engineers()
    {
        return $this->db->select('user_id, first_name, last_name, department_id')
            ->from('system_users')
            ->where('user_status', 1)
            ->where_in('department_id', [22, 14])
            ->order_by('first_name', 'ASC')
            ->order_by('last_name', 'ASC')
            ->get()
            ->result();
    }

    private function get_service_payment_engineer_options()
    {
        $unique_engineers = [];

        foreach ($this->get_service_scheduler_engineers() as $engineer) {
            $full_name = trim(preg_replace('/\s+/', ' ', (string) $engineer->first_name . ' ' . (string) $engineer->last_name));
            $name_key = strtolower($full_name);

            if ($name_key === '' || isset($unique_engineers[$name_key])) {
                continue;
            }

            $unique_engineers[$name_key] = $engineer;
        }

        return array_values($unique_engineers);
    }

    private function get_service_visit_status_map()
    {
        return [
            'scheduled' => 'Scheduled',
            'on-site' => 'On-Site',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    private function build_service_visit_kpi($visits)
    {
        $kpi = [
            'total' => 0,
            'scheduled' => 0,
            'on_site' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'pending_docs' => 0,
        ];

        foreach ($visits as $visit) {
            $status = strtolower(trim((string) $visit->visit_status));
            $kpi['total']++;

            switch ($status) {
                case 'completed':
                    $kpi['completed']++;
                    if ((int) $visit->document_count === 0) {
                        $kpi['pending_docs']++;
                    }
                    break;
                case 'on-site':
                    $kpi['on_site']++;
                    break;
                case 'cancelled':
                    $kpi['cancelled']++;
                    break;
                default:
                    $kpi['scheduled']++;
                    break;
            }
        }

        return $kpi;
    }

    private function has_service_visit_uploads($field_name)
    {
        if (empty($_FILES[$field_name]) || !isset($_FILES[$field_name]['name'])) {
            return false;
        }

        if (is_array($_FILES[$field_name]['name'])) {
            foreach ($_FILES[$field_name]['name'] as $file_name) {
                if (trim((string) $file_name) !== '') {
                    return true;
                }
            }

            return false;
        }

        return trim((string) $_FILES[$field_name]['name']) !== '';
    }

    private function cleanup_service_visit_uploads($files)
    {
        foreach ($files as $file) {
            if (empty($file['file_path'])) {
                continue;
            }

            $full_path = FCPATH . ltrim($file['file_path'], '/');
            if (file_exists($full_path)) {
                @unlink($full_path);
            }
        }
    }

    private function upload_service_visit_documents($field_name = 'signed_documents')
    {
        if (!$this->has_service_visit_uploads($field_name)) {
            return [
                'success' => true,
                'files' => [],
            ];
        }

        $upload_path = FCPATH . 'uploads/service_visit_docs/';
        if (!is_dir($upload_path) && !mkdir($upload_path, 0777, true) && !is_dir($upload_path)) {
            return [
                'success' => false,
                'error' => 'Unable to prepare the signed document upload folder.',
            ];
        }

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'pdf|jpg|jpeg|png|doc|docx',
            'encrypt_name' => true,
        ];

        $this->load->library('upload');

        $uploaded_files = [];
        $file_names = is_array($_FILES[$field_name]['name']) ? $_FILES[$field_name]['name'] : [$_FILES[$field_name]['name']];
        $file_types = is_array($_FILES[$field_name]['type']) ? $_FILES[$field_name]['type'] : [$_FILES[$field_name]['type']];
        $file_tmp_names = is_array($_FILES[$field_name]['tmp_name']) ? $_FILES[$field_name]['tmp_name'] : [$_FILES[$field_name]['tmp_name']];
        $file_errors = is_array($_FILES[$field_name]['error']) ? $_FILES[$field_name]['error'] : [$_FILES[$field_name]['error']];
        $file_sizes = is_array($_FILES[$field_name]['size']) ? $_FILES[$field_name]['size'] : [$_FILES[$field_name]['size']];

        foreach ($file_names as $index => $original_name) {
            $original_name = trim((string) $original_name);
            if ($original_name === '') {
                continue;
            }

            $_FILES['__service_visit_document'] = [
                'name' => $original_name,
                'type' => $file_types[$index] ?? '',
                'tmp_name' => $file_tmp_names[$index] ?? '',
                'error' => $file_errors[$index] ?? 0,
                'size' => $file_sizes[$index] ?? 0,
            ];

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('__service_visit_document')) {
                $this->cleanup_service_visit_uploads($uploaded_files);
                unset($_FILES['__service_visit_document']);

                return [
                    'success' => false,
                    'error' => trim(strip_tags($this->upload->display_errors('', ''))),
                ];
            }

            $file_data = $this->upload->data();
            $uploaded_files[] = [
                'file_name' => $file_data['file_name'],
                'original_name' => $original_name,
                'file_path' => 'uploads/service_visit_docs/' . $file_data['file_name'],
                'document_type' => 'SIGNED_COMPLETION',
            ];
        }

        unset($_FILES['__service_visit_document']);

        return [
            'success' => true,
            'files' => $uploaded_files,
        ];
    }

    private function ensure_service_pi_dependencies()
    {
        $this->service_pi_model->ensure_tables();

        $stage = $this->db->query(
            "SELECT stage_id
             FROM service_lead_stages
             WHERE LOWER(TRIM(stage_name)) = ?
             LIMIT 1",
            ['create pi']
        )->row();

        if ($stage) {
            return (int) $stage->stage_id;
        }

        $max_sort_row = $this->db->select_max('sort_order', 'max_sort')->get('service_lead_stages')->row();
        $next_sort = (int) ($max_sort_row->max_sort ?? 0) + 1;

        $this->db->insert('service_lead_stages', [
            'stage_name' => 'Create PI',
            'sort_order' => $next_sort,
        ]);

        return (int) $this->db->insert_id();
    }

    private function get_service_company_profile()
    {
        $profile = [
            'name' => 'SHUBHAM FLEXIBLE PACKAGING MACHINES PVT. LTD.',
            'unit' => 'UNIT - II',
            'iso_label' => '(An ISO 9001 : 2015 Company)',
            'tagline' => 'Pack With Confidence!',
            'gst_no' => '06AAFCS9908R1ZH',
            'cin_no' => 'U29299DL2002PTC114679',
            'ecc_no' => 'AAFCS 9908 RXM001',
            'service_tax_regn_no' => 'AAFCS 9908 RST001',
            'gst_regn_no' => '06AAFCS9908R1ZH',
            'iec_code' => '0503025216',
            'tariff_no' => '998719',
            'logo' => 'https://pms.shubhampack.in/assets/images/shubhampack.png',
            'colorcode' => '#003366',
            'bank_name' => 'Axis Bank Ltd.',
            'account_no' => '920030068344715',
            'bank_address' => 'SCO-40 Sec-7 Market Ballabhgarh Faridabad',
            'ifsc_code' => 'UTIB0000039',
            'account_type' => 'Current Account',
            'account_holder' => 'Shubham Flexible Packaging Machines Pvt. Ltd.',
            'swift_code' => 'AXISINBB039',
            'corporate_office' => 'B-8A, Sector 59 Part II, Ballabgarh, Faridabad - 121004, Haryana (India)',
            'registered_office' => '2, Central Road, Bhogal, New Delhi - 110014',
            'phone' => '+91 129 4272352-399',
            'fax' => '+91 129 4272351',
            'emails' => 'ccare@shubhampack.com, marketing@shubhampack.com',
            'website' => 'www.shubhampack.com',
        ];

        return $profile;
    }

    private function get_service_customer_snapshot($opportunity)
    {
        $customer = null;
        $origin = $this->normalize_service_customer_origin($opportunity->customer_table_origin ?? '');

        if ($origin === 'spare') {
            $customer = $this->db->select('company_name, address, contact_person as contact_name, contact_person_no as phone, email, tax_number as gst_no, country_id')
                ->get_where('spares_customers', ['customer_id' => $opportunity->customer_id])
                ->row();
        } else {
            $customer = $this->db->select('company_name, address, customer_name as contact_name, contact_no as phone, email, gst as gst_no, country as country_id')
                ->get_where('customer_detail', ['id' => $opportunity->customer_id])
                ->row();

            if (!$customer) {
                $customer = $this->db->select('company_name, address, contact_person as contact_name, contact_person_no as phone, email, tax_number as gst_no, country_id')
                    ->get_where('spares_customers', ['customer_id' => $opportunity->customer_id])
                    ->row();
            }
        }

        if (!$customer) {
            $customer = (object) [
                'company_name' => $opportunity->company_name ?? '',
                'address' => $opportunity->company_address ?? ($opportunity->customer_address ?? ''),
                'contact_name' => $opportunity->customer_contact_name ?? '',
                'phone' => $opportunity->customer_contact_number ?? ($opportunity->customer_contact_no ?? ''),
                'email' => $opportunity->customer_email_address ?? ($opportunity->customer_email ?? ''),
                'gst_no' => $opportunity->customer_gst_number ?? '',
                'country_id' => $opportunity->customer_country_id ?? 101,
            ];
        }

        $customer->company_name = $customer->company_name ?: ($opportunity->company_name ?? '');
        $customer->address = $customer->address ?: ($opportunity->company_address ?? ($opportunity->customer_address ?? ''));
        $customer->contact_name = $customer->contact_name ?: ($opportunity->customer_contact_name ?? '');
        $customer->phone = $customer->phone ?: ($opportunity->customer_contact_number ?? ($opportunity->customer_contact_no ?? ''));
        $customer->email = $customer->email ?: ($opportunity->customer_email_address ?? ($opportunity->customer_email ?? ''));
        $customer->gst_no = $customer->gst_no ?: ($opportunity->customer_gst_number ?? '');
        $customer->country_id = $customer->country_id ?: ($opportunity->customer_country_id ?? 101);

        return $customer;
    }

    private function get_service_quotation_rows($opportunity_id, $opportunity_no = '')
    {
        $opportunity_id = (int) $opportunity_id;
        $opportunity_no = strtoupper(trim((string) $opportunity_no));

        $this->db->from('service_quotations');
        $this->db->group_start();
        $this->db->where('opportunity_id', $opportunity_id);

        if ($opportunity_no !== '') {
            $this->db->or_where(
                'UPPER(TRIM(quotation_no)) = ' . $this->db->escape($opportunity_no),
                null,
                false
            );
        }

        $this->db->group_end();
        $this->db->order_by('id', 'DESC');

        return $this->db->get()->result();
    }

    private function get_latest_service_quote_row($opportunity_id, $opportunity_no = '')
    {
        $opportunity_id = (int) $opportunity_id;
        $opportunity_no = strtoupper(trim((string) $opportunity_no));

        $this->db->from('service_quotations');
        $this->db->group_start();
        $this->db->where('opportunity_id', $opportunity_id);

        if ($opportunity_no !== '') {
            $this->db->or_where(
                'UPPER(TRIM(quotation_no)) = ' . $this->db->escape($opportunity_no),
                null,
                false
            );
        }

        $this->db->group_end();
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    private function get_latest_service_quote_with_items($opportunity_id, $opportunity_no = '')
    {
        $quote = $this->get_latest_service_quote_row($opportunity_id, $opportunity_no);

        $items = [];

        if ($quote) {
            $items = $this->db->select('sqi.*, scm.charge_name')
                ->from('service_quotation_items sqi')
                ->join('service_charges_master scm', 'scm.id = sqi.charge_id', 'left')
                ->where('sqi.service_quote_id', $quote->id)
                ->order_by('sqi.id', 'ASC')
                ->get()
                ->result();
        }

        return [
            'quote' => $quote,
            'items' => $items,
        ];
    }

    private function get_latest_service_po($opportunity_id)
    {
        return $this->db->order_by('id', 'DESC')
            ->get_where('service_purchase_orders', ['opportunity_id' => (int) $opportunity_id], 1)
            ->row();
    }

    private function build_service_pi_title($buyer_order_no, $buyer_order_date = null)
    {
        $title = 'Proforma Invoice';
        $buyer_order_no = trim((string) $buyer_order_no);

        if ($buyer_order_no !== '') {
            $title .= ' Against ' . $buyer_order_no;
        }

        if (!empty($buyer_order_date) && $buyer_order_date !== '0000-00-00') {
            $title .= ', Dated-' . date('d.m.Y', strtotime($buyer_order_date));
        }

        return $title;
    }

    private function build_service_pi_items_from_quote($quote_items)
    {
        $items = [];

        foreach ($quote_items as $index => $item) {
            $extra_description = '';
            if (!empty($item->description)) {
                $extra_description = trim(strip_tags(str_replace(
                    ['<br />', '<br/>', '<br>'],
                    "\n",
                    $item->description
                )));
            }

            $description = trim((string) ($item->charge_name ?? 'Service Charge'));
            if ($extra_description !== '') {
                $description .= "\n" . $extra_description;
            }

            $items[] = (object) [
                'sac_code' => '998719',
                'description' => $description,
                'unit_rate' => (float) ($item->rate ?? 0),
                'no_of_days' => (float) ($item->no_of_days ?? 0),
                'no_of_engineers' => (float) ($item->no_of_engineers ?? 0),
                'uom' => $item->uom ?: 'Day',
                'is_customer_scope' => (int) ($item->is_customer_scope ?? 0),
                'scope_note' => !empty($item->is_customer_scope) ? 'At Customer Scope' : '',
                'row_total' => (float) ($item->row_total ?? 0),
            ];
        }

        return $items;
    }

    private function parse_service_pi_items_from_post($post)
    {
        $items = [];
        $basic_amount = 0;
        $descriptions = $post['description'] ?? [];

        foreach ($descriptions as $index => $description) {
            $description = trim((string) $description);
            if ($description === '') {
                continue;
            }

            $scope_type = (int) ($post['scope_type'][$index] ?? 0);
            $unit_rate = (float) ($post['unit_rate'][$index] ?? 0);
            $no_of_days = (float) ($post['no_of_days'][$index] ?? 0);
            $no_of_engineers = (float) ($post['no_of_engineers'][$index] ?? 0);
            $uom = trim((string) ($post['uom'][$index] ?? 'Day'));
            $row_total = $scope_type === 1 ? 0 : round($unit_rate * $no_of_days * $no_of_engineers, 2);
            $scope_note = trim((string) ($post['scope_note'][$index] ?? ''));

            if ($scope_type === 1 && $scope_note === '') {
                $scope_note = 'At Customer Scope';
            }

            $items[] = [
                'sort_order' => count($items) + 1,
                'sac_code' => trim((string) ($post['sac_code'][$index] ?? '998719')),
                'description' => $description,
                'unit_rate' => $unit_rate,
                'no_of_days' => $no_of_days,
                'no_of_engineers' => $no_of_engineers,
                'uom' => $uom !== '' ? $uom : 'Day',
                'is_customer_scope' => $scope_type,
                'scope_note' => $scope_note,
                'row_total' => $row_total,
            ];

            if ($scope_type !== 1) {
                $basic_amount += $row_total;
            }
        }

        $gst_percent = (float) ($post['gst_percent'] ?? 0);
        $gst_amount = round(($basic_amount * $gst_percent) / 100, 2);
        $grand_total = round($basic_amount + $gst_amount, 2);

        return [
            'items' => $items,
            'basic_amount' => round($basic_amount, 2),
            'gst_percent' => $gst_percent,
            'gst_amount' => $gst_amount,
            'grand_total' => $grand_total,
        ];
    }

    private function insert_service_progress_history($opportunity_id, $stage_id, $remarks)
    {
        $user_id = $this->session->userdata['logged_in']['user_id'] ?? null;

        $this->db->insert('service_progress_history', [
            'opportunity_id' => (int) $opportunity_id,
            'stage_id' => (int) $stage_id,
            'remarks' => $remarks,
            'added_by' => $user_id,
            'added_on' => date('Y-m-d H:i:s'),
        ]);
    }

    private function get_service_pi_default_notes()
    {
        return "1. All disputes are subject to Faridabad Jurisdiction.\n2. Bills not paid on presentation, interest will be charged @ 18% p.a.";
    }

    private function get_service_stage_default_probability($stage_id)
    {
        $stage = $this->db->select('default_probability')
            ->from('service_lead_stages')
            ->where('stage_id', (int) $stage_id)
            ->limit(1)
            ->get()
            ->row();

        return isset($stage->default_probability) ? (int) $stage->default_probability : 0;
    }

    public function create() {
        $this->load->view('spares/service_lead_form');
    }

    private function normalize_service_customer_origin($origin)
    {
        $origin = strtolower(trim((string) $origin));

        if ($origin === 'spare' || $origin === 'spares') {
            return 'spare';
        }

        return 'marketing';
    }

    private function rebuild_service_opportunity_no_for_type($current_op_no, $op_type)
    {
        $current_op_no = trim((string) $current_op_no);
        $type_prefix = ((int) $op_type === 1) ? 'DOM' : 'EXP';

        if ($current_op_no === '') {
            return $this->generateOppNo((int) $op_type, 0);
        }

        $parts = explode('/', $current_op_no);
        if (count($parts) >= 4) {
            $parts[1] = $type_prefix;
            return implode('/', $parts);
        }

        $updated_op_no = preg_replace('/\b(DOM|EXP)\b/i', $type_prefix, $current_op_no, 1);
        if (!empty($updated_op_no)) {
            return $updated_op_no;
        }

        return $this->generateOppNo((int) $op_type, 0);
    }

    /**
     * AJAX: Generate Opportunity Number specifically for Service
     * Format: SPM/SERVICE/DOM/0001/25-26 [cite: 1]
     */
// public function generateOppNo() {
//     $op_type = $this->input->post('op_type'); // 1 = Domestic, 2 = International

//     // 1. Calculate Financial Year (April 1st Start)
//     if (date('m') >= 4) { 
//         $financial_year = date('y') . '-' . (date('y') + 1);
//     } else { 
//         $financial_year = (date('y') - 1) . '-' . date('y');
//     }

//     // 2. Fetch the last ID to determine the next increment
//     $this->db->select_max('opportunity_id', 'last_id');
//     $query = $this->db->get('service_opportunities');
//     $result = $query->row();

//     $next_id = 1; 
//     if ($result && !empty($result->last_id)) {
//         $next_id = $result->last_id + 1;
//     }

//     $padded_number = str_pad($next_id, 4, '0', STR_PAD_LEFT);

//     // 3. Dynamic Prefix Logic
//     // If Type is 1 (Domestic), use DOM. If 2 (International), use EXP.
//     $type_prefix = ($op_type == '1') ? "DOM" : "EXP";

//     // 4. Format the Number: SPM / [PREFIX] / [PaddedID] / [FY]
//     $formatted_opp_no = "SPM/" . $type_prefix . "/" . $padded_number . "/" . $financial_year;

//     // Return the full formatted string
//     echo $formatted_opp_no;
// }

    public function generateOppNo($op_type=null,$flag=0) {
    if($op_type==null)
    {
    $op_type = $this->input->post('op_type'); // 1 = Domestic, 2 = International
    $flag = $this->input->post('flag'); // 1 = Domestic, 2 = International
    }else
    {
        $op_type=$op_type;
        $flag=$flag;
    }

    // 1. Financial Year
    if (date('m') >= 4) { 
        $financial_year = date('y') . '-' . (date('y') + 1);
    } else { 
        $financial_year = (date('y') - 1) . '-' . date('y');
    }

    // 2. Prefix (only for display, NOT for sequence logic)
    $type_prefix = ($op_type == '1') ? "DOM" : "EXP";

    // 3. Get LAST record of this financial year (ignore DOM/EXP)
    $this->db->select('op_no');
    $this->db->from('service_opportunities');
    $this->db->like('op_no', "/$financial_year", 'before'); // only FY filter
    $this->db->order_by('opportunity_id', 'DESC');
    $this->db->limit(1);

    $query = $this->db->get();

    $last_number = 0;

    if ($query->num_rows() > 0) {
        $row = $query->row();
        $last_opp_no = $row->op_no;

        // Example: SPM/EXP/0005/26-27 OR SPM/DOM/0004/26-27
        $parts = explode('/', $last_opp_no);

        if (isset($parts[2])) {
            $last_number = (int)$parts[2];
        }
    }

    // 4. Increment globally
    $next_number = $last_number + 1;

    // 5. Pad
    $padded_number = str_pad($next_number, 4, '0', STR_PAD_LEFT);

    // 6. Final format
    $formatted_opp_no = "SPM/" . $type_prefix . "/" . $padded_number . "/" . $financial_year;
    if($flag==1)
    {
    echo $formatted_opp_no;
    }else
    {
        return $formatted_opp_no;
    }   
}


    /**
     * MAIN SAVE FUNCTION: missing previously
     * Handles the database insertion for the new service lead
     */
    public function save_opportunity() {
        $user_id = $this->session->userdata['logged_in']['user_id'];
        
        // 1. Validation Logic
        $this->load->library('form_validation');
        $this->form_validation->set_rules('op_date', 'Opportunity Date', 'required');
        $this->form_validation->set_rules('lsource', 'Source', 'required');
        $this->form_validation->set_rules('op_type', 'Type', 'required');
        $this->form_validation->set_rules('customer', 'Customer', 'required');
        $this->form_validation->set_rules('remarks', 'Requirement', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(page_url.'ServiceLeads/create');
            return;
        }

        $op_no=$this->generateOppNo($this->input->post('op_type'),0);
        $customer_origin = $this->normalize_service_customer_origin($this->input->post('customer_origin'));
        //echo $op_no; exit;
        // 2. Prepare Data Array
        $data = array(
            'op_no'               => $op_no,
            'op_date'             => $this->input->post('op_date'),
            'op_type'             => $this->input->post('op_type'),
            'customer_id'         => $this->input->post('customer'),
            'customer_table_origin' => $customer_origin,
            'marketing_person_id' => $this->input->post('marketing'),
            'source_id'           => $this->input->post('lsource'),
            'customer_address'    => $this->input->post('address'),
            'customer_contact_no' => $this->input->post('customercontactno'),
            'customer_email'      => $this->input->post('customeremailid'),
            'remarks'             => $this->input->post('remarks'),
            'current_stage_id'    => 1, // Default to "New Opportunity"
            'probability'         => $this->get_service_stage_default_probability(1),
            'status'              => 'Open',
            'gst'                 =>$this->input->post('gst'),
            'created_by'          => $user_id,
            'created_at'          => date('Y-m-d H:i:s')
        );

        // 3. Database Transaction
        $this->db->trans_start();
        $this->db->insert('service_opportunities', $data);

        /** UPDATE CUSTOMER DETAILS IN MASTER **/
        $customer_id = (int) $this->input->post('customer');
        $customer_table = $customer_origin === 'spare' ? 'spares_customers' : 'customer_detail';
        $customer_key = $customer_origin === 'spare' ? 'customer_id' : 'id';
        $country_field = $customer_origin === 'spare' ? 'country_id' : 'country';
        $phone_field = $customer_origin === 'spare' ? 'contact_person_no' : 'contact_no';
        $gst_field = $customer_origin === 'spare' ? 'tax_number' : 'gst';

        $customer_data = $this->db
            ->where($customer_key, $customer_id)
            ->get($customer_table)
            ->row_array();

        $update_customer = array();

        if (!empty($customer_data)) {
            if ((empty($customer_data[$country_field]) || (string) $customer_data[$country_field] === '0') && !empty($this->input->post('country'))) {
                $update_customer[$country_field] = $this->input->post('country');
            }

            if (empty($customer_data[$phone_field]) && !empty($this->input->post('customercontactno'))) {
                $update_customer[$phone_field] = $this->input->post('customercontactno');
            }

            if (empty($customer_data[$gst_field]) && !empty($this->input->post('gst'))) {
                $update_customer[$gst_field] = $this->input->post('gst');
            }

            if (empty($customer_data['email']) && !empty($this->input->post('customeremailid'))) {
                $update_customer['email'] = $this->input->post('customeremailid');
            }

            if (empty($customer_data['address']) && !empty($this->input->post('address'))) {
                $update_customer['address'] = $this->input->post('address');
            }

            if (!empty($update_customer)) {
                $this->db->where($customer_key, $customer_id);
                $this->db->update($customer_table, $update_customer);
            }
        }

        /** END **/

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Database Error: Could not save the service lead.');
        } else {
            $this->session->set_flashdata('success', 'Service Opportunity '.$data['op_no'].' created successfully!');
        }

        redirect(page_url.'ServiceLeads/create');
    }

    /**
     * AJAX: Add New Customer from the Modal Popup [cite: 1]
     */
    public function add_new_ajax_customer() {

     
        $brand_input = $this->input->post('brand');
        $brand_id_to_save = is_numeric($brand_input) ? (int)$brand_input : null;

        // If brand is text, it's a new brand; save it first
        if (!$brand_id_to_save && !empty($brand_input)) {
            $this->db->insert('spare_company_brand', ['name' => trim($brand_input)]);
            $brand_id_to_save = $this->db->insert_id();
        }

        $data = array(
            'company_name'         => trim($this->input->post('new_companyname')),
            'brand_id'             => $brand_id_to_save,
            'country_id'           => $this->input->post('new_country'),
            'email'                => trim($this->input->post('new_email')),
            'address'              => trim($this->input->post('new_address')),
            'contact_person'       => trim($this->input->post('contactpersonname')),
            'contact_person_no'    => trim($this->input->post('personcontactno')),
            'tax_number'=>trim($this->input->post('gstno')),
            'status'               => 1,
            'created_at'           => date('Y-m-d H:i:s')
        );

        if ($this->db->insert('spares_customers', $data)) {
            echo "1~" . $this->db->insert_id();
        } else {
            echo "0~Failed to save customer.";
        }
    }

    /**
     * AJAX: Auto-fetch details after selecting a customer
     */
public function getCustomerDetails_frommaster() {
    $id = $this->input->post('custid');
    $origin = $this->normalize_service_customer_origin($this->input->post('origin'));
    $res = null;

    if ($origin === 'spare') {
        $res = $this->db->select('address, contact_person_no as phone, email, country_id, tax_number')
            ->get_where('spares_customers', ['customer_id' => $id])
            ->row();
    } elseif ($origin === 'marketing') {
        $res = $this->db->select('address, contact_no as phone, email, country as country_id, gst as tax_number')
            ->get_where('customer_detail', ['id' => $id])
            ->row();
    }

    if (!$res) {
        $res = $this->db->select('address, contact_person_no as phone, email, country_id, tax_number')
            ->get_where('spares_customers', ['customer_id' => $id])
            ->row();
    }

    if (!$res) {
        $res = $this->db->select('address, contact_no as phone, email, country as country_id, gst as tax_number')
            ->get_where('customer_detail', ['id' => $id])
            ->row();
    }

    if ($res) {
        echo $res->address . '|' . $res->phone . '|' . $res->email . '|' . $res->country_id.'|'.$res->tax_number;
    }
}


public function dashboard() {
    $this->load->model('Dashboard_model');
    
    // 1. Data for Bar Chart: Engineer-wise Earning (Won Orders)
    $data['engineer_earnings'] = $this->db->select("u.first_name as label, SUM(sq.grand_total) as value")
        ->from('service_engineer_visits v')
        ->join('system_users u', 'u.user_id = v.engineer_id')
        ->join('service_quotations sq', 'sq.opportunity_id = v.opportunity_id')
        ->join('service_opportunities so', 'so.opportunity_id = v.opportunity_id')
        ->where('so.current_stage_id', 7) // Stage 7 is Order Won [cite: 1]
        ->group_by('v.engineer_id')
        ->get()->result();

    // 2. Data for Pie Chart: Business Split (Domestic vs International)
    // op_type 1 = Domestic, others = International [cite: 1]
    $data['business_split'] = $this->db->select("
            SUM(CASE WHEN op_type = 1 THEN 1 ELSE 0 END) as domestic_count,
            SUM(CASE WHEN op_type != 1 THEN 1 ELSE 0 END) as international_count
        ")
        ->from('service_opportunities')
        ->get()->row();

    $data['upcoming_visits'] = $this->db->select('v.*, u.first_name, cm.company_name')
        ->from('service_engineer_visits v')
        ->join('system_users u', 'u.user_id = v.engineer_id')
        ->join('service_opportunities so', 'so.opportunity_id = v.opportunity_id')
        ->join('spares_customers cm', 'cm.customer_id = so.customer_id')
        ->where('v.visit_status', 'Scheduled')
        ->limit(5)->get()->result();

    $data['payment_permissions'] = $this->get_service_payment_permission_snapshot();

    $this->load->view('dashboard/service_dashboard_view', $data);
}

public function opportunity_list() {
    // 1. Get Stage ID from URI Segment 3 or Get Request
    $stage_from_uri = $this->uri->segment(3);
    $stage_filter = ($stage_from_uri) ? $stage_from_uri : $this->input->get('stage');

    // 2. Fetch Marketing Persons for the Filter Dropdown (Dept 22)
    $data['marketing_persons'] = $this->db->select('user_id, first_name, last_name')
        ->from('system_users')
        ->where('department_id', 22)
        ->where('user_status', 1)
        ->get()->result();

    // 3. Smart KPIs - Synchronized with Lead Stages 
    $data['kpi'] = [
        'total'         => $this->db->count_all_results('service_opportunities'),
        'new'           => $this->db->where('current_stage_id', 1)->count_all_results('service_opportunities'),
        'quote_pending' => $this->db->where('current_stage_id', 3)->count_all_results('service_opportunities'),
        'quote_shared'  => $this->db->where('current_stage_id', 5)->count_all_results('service_opportunities'),
        'won'           => $this->db->where('current_stage_id', 7)->count_all_results('service_opportunities')
    ];

    // 4. Set Active Stage Title for UI Header
    $data['active_stage_name'] = "All Service Leads";
    if($stage_filter) {
        $stage_row = $this->db->get_where('service_lead_stages', ['stage_id' => $stage_filter])->row();
        $data['active_stage_name'] = $stage_row ? $stage_row->stage_name : "All Service Leads";
    }

    // 5. Main Query: Selecting Probability and Dual Table Customer Names
    $this->db->select("
        so.opportunity_id, 
        so.op_no, 
        so.op_date, 
        so.op_type, 
        so.probability, 
        IFNULL(cm_spares.company_name, cm_marketing.company_name) as company_name, 
        ls.lead_source,
        CONCAT(u.first_name, ' ', u.last_name) as marketing_person_name,
        sls.stage_name as current_stage_name,
        (SELECT quotation_no FROM service_quotations WHERE opportunity_id = so.opportunity_id ORDER BY id DESC LIMIT 1) as latest_quote_no,
        (SELECT id FROM service_quotations WHERE opportunity_id = so.opportunity_id ORDER BY id DESC LIMIT 1) as latest_quote_id
    ");
    $this->db->from('service_opportunities so');
    
    // Join Logic for Dual Customer Support
    $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
    $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
    
    // Other Standard Joins
    $this->db->join('system_users u', 'u.user_id = so.marketing_person_id', 'left');
    $this->db->join('lead_source ls', 'ls.source_id = so.source_id', 'left');
    $this->db->join('service_lead_stages sls', 'sls.stage_id = so.current_stage_id', 'left');

    // 6. Apply Active Filters
    if ($stage_filter) { 
        $this->db->where('so.current_stage_id', $stage_filter); 
    }
    if ($this->input->get('marketing_person')) { 
        $this->db->where('so.marketing_person_id', $this->input->get('marketing_person')); 
    }
    if ($this->input->get('op_type')) { 
        $this->db->where('so.op_type', $this->input->get('op_type')); 
    }
    if ($this->input->get('from_date')) { 
        $this->db->where('so.op_date >=', $this->input->get('from_date')); 
    }
    if ($this->input->get('to_date')) { 
        $this->db->where('so.op_date <=', $this->input->get('to_date')); 
    }

    // 7. Order by Newest First and Execute
    $data['opportunities'] = $this->db->order_by('so.opportunity_id', 'DESC')->get()->result();
    $data['current_active_filter'] = $stage_filter;

    // 8. Load List View
    $this->load->view('spares/service_opportunity_list_view', $data);
}

// Updated Progress function for Auto-Probability
/**
 * AJAX: Update Opportunity Stage and Log History
 */
public function update_opportunity_progress($id) {
    header('Content-Type: application/json');
    $post = $this->input->post();
    $user_id = $this->session->userdata['logged_in']['user_id'];
    
    // 1. Fetch default probability for the new stage from master
    $stage = $this->db->get_where('service_lead_stages', ['stage_id' => $post['new_stage_id']])->row();
    $new_prob = $stage->default_probability ?? 0;

    $this->db->trans_start();

    // 2. Update Main Opportunity Record
    $update_data = [
        'current_stage_id' => $post['new_stage_id'],
        'probability'      => $new_prob,
        'remarks'          => $post['remarks'] // Update main remarks to latest
    ];
    $this->db->where('opportunity_id', $id)->update('service_opportunities', $update_data);

    // 3. Insert into History Timeline
    $history_data = [
        'opportunity_id'   => $id,
        'stage_id'         => $post['new_stage_id'],
        'remarks'          => $post['remarks'],
        'next_follow_date' => !empty($post['followup_date']) ? date('Y-m-d', strtotime($post['followup_date'])) : NULL,
        'added_by'         => $user_id,
        'added_on'         => date('Y-m-d H:i:s')
    ];
    $this->db->insert('service_progress_history', $history_data);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update progress.']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Timeline updated successfully!']);
    }
}

public function opportunity_detail($id) {
    $this->load->model('Service_model');
    $data['create_pi_stage_id'] = $this->ensure_service_pi_dependencies();
    
    // 1. Fetch details FIRST
    $opportunity = $this->Service_model->get_opportunity_details($id);
    $data['opportunity'] = $opportunity;
    
    if (!$data['opportunity']) { show_404(); }

    // 2. Now perform checks using the defined variable
    $data['is_rejected'] = (strpos(strtolower($opportunity->remarks), 'rejected') !== false);
    $data['is_approved'] = (strpos(strtolower($opportunity->remarks), 'approved') !== false);

    // 3. Load other data
    $data['history'] = $this->Service_model->get_opportunity_history($id);
    $data['quotations'] = $this->get_service_quotation_rows($id, $opportunity->op_no ?? '');
    $data['has_quotation'] = !empty($data['quotations']);
    $data['missing_quote_warning'] = (
        (int) $data['opportunity']->current_stage_id >= 3
        && empty($data['quotations'])
    );
    $data['service_pi'] = $this->service_pi_model->get_by_opportunity($id);
    $data['payment_permissions'] = $this->get_service_payment_permission_snapshot();
    
    // 4. Custom logic for Next Stages based on Stage 8 status
    if ($data['opportunity']->current_stage_id == 8) {
        if ($data['is_rejected']) {
            // If rejected, only allow Revised Quotation (Stage 4)
            $data['next_stages'] = [['lead_id' => 4, 'lead_name' => 'Revised Quotation']];
        } else {
            // If approved, move to Quotation Shared & Followup (Stage 5)
            $data['next_stages'] = [['lead_id' => 5, 'lead_name' => 'Quotation Shared & Followup']];
        }
    } else {
        $data['next_stages'] = $this->Service_model->get_next_stages($data['opportunity']->current_stage_id);
    }

    if (!empty($data['has_quotation'])) {
        $has_revise_option = false;

        foreach ($data['next_stages'] as $stage) {
            if ((int) ($stage['lead_id'] ?? 0) === 4) {
                $has_revise_option = true;
                break;
            }
        }

        if (!$has_revise_option) {
            $data['next_stages'][] = [
                'lead_id' => 4,
                'lead_name' => 'Revised Quotation',
                'app_access' => 0,
                'followup_date_required' => false,
            ];
        }
    }

    $this->load->view('spares/service_opportunity_detail_view', $data);
}

public function create_pi($opportunity_id)
{
    $this->ensure_service_pi_dependencies();
    $this->load->model('Service_model');

    $data['opportunity'] = $this->Service_model->get_opportunity_details($opportunity_id);
    if (!$data['opportunity']) {
        show_404();
    }

    $quote_bundle = $this->get_latest_service_quote_with_items(
        $opportunity_id,
        $data['opportunity']->op_no ?? ''
    );
    $latest_quote = $quote_bundle['quote'];
    $latest_po = $this->get_latest_service_po($opportunity_id);
    $existing_pi = $this->service_pi_model->get_by_opportunity($opportunity_id);
    $customer = $this->get_service_customer_snapshot($data['opportunity']);
    $company_profile = $this->get_service_company_profile();

    if ($existing_pi) {
        $pi = $existing_pi;
        $pi_items = $this->service_pi_model->get_items($existing_pi->id);
    } else {
        $default_currency = strtoupper(trim((string) ($latest_quote->currency ?? 'INR')));
        $default_gst_percent = isset($latest_quote->gst_percent) ? (float) $latest_quote->gst_percent : ($default_currency === 'INR' ? 18 : 0);
        // Buyer PO Number is entered manually by the PI creator. The latest
        // service PO remains linked through po_id but must not prefill this field.
        $default_buyer_order_no = '';
        $default_buyer_order_date = $latest_po->po_date ?? null;

        $pi = (object) [
            'id' => 0,
            'quote_id' => $latest_quote->id ?? null,
            'po_id' => $latest_po->id ?? null,
            'pi_no' => $this->service_pi_model->get_next_pi_no(),
            'pi_date' => date('Y-m-d'),
            'pi_title' => $this->build_service_pi_title($default_buyer_order_no, $default_buyer_order_date),
            'currency' => $default_currency !== '' ? $default_currency : 'INR',
            'buyer_name' => $customer->company_name ?? '',
            'buyer_contact' => $customer->contact_name ?? '',
            'buyer_address' => $customer->address ?? '',
            'buyer_gstin' => $customer->gst_no ?? '',
            'consignee_name' => '',
            'consignee_contact' => '',
            'consignee_address' => '',
            'consignee_ecc_no' => '',
            'consignee_phone' => '',
            'buyer_order_no' => $default_buyer_order_no,
            'buyer_order_date' => $default_buyer_order_date,
            'payment_terms' => $latest_quote->payment_terms ?? '100% Against PI',
            'bank_name' => $company_profile['bank_name'],
            'account_no' => $company_profile['account_no'],
            'bank_address' => $company_profile['bank_address'],
            'ifsc_code' => $company_profile['ifsc_code'],
            'account_type' => $company_profile['account_type'],
            'account_holder' => $company_profile['account_holder'],
            'swift_code' => $company_profile['swift_code'],
            'country_of_origin' => 'India',
            'gst_percent' => $default_gst_percent,
            'basic_amount' => 0,
            'gst_amount' => 0,
            'grand_total' => 0,
            'amount_in_words' => '',
            'declaration_text' => 'We declare that this invoice shows the actual price of the Service described and that all particulars are true and correct.',
            'notes' => $this->get_service_pi_default_notes(),
        ];

        $pi_items = $this->build_service_pi_items_from_quote($quote_bundle['items']);

        $basic_amount = 0;
        foreach ($pi_items as $item) {
            if ((int) $item->is_customer_scope !== 1) {
                $basic_amount += (float) $item->row_total;
            }
        }

        $pi->basic_amount = round($basic_amount, 2);
        $pi->gst_amount = round(($pi->basic_amount * (float) $pi->gst_percent) / 100, 2);
        $pi->grand_total = round($pi->basic_amount + $pi->gst_amount, 2);
    }

    $this->load->helper('number');
    if (empty($pi->amount_in_words)) {
        $pi->amount_in_words = function_exists('get_amount_in_words')
            ? get_amount_in_words((float) $pi->grand_total, $pi->currency)
            : number_format((float) $pi->grand_total, 2);
    }

    $data['customer'] = $customer;
    $data['company_profile'] = $company_profile;
    $data['pi'] = $pi;
    $data['pi_items'] = $pi_items;
    $data['latest_quote'] = $latest_quote;
    $data['latest_po'] = $latest_po;

    $this->load->view('spares/service_pi_form_view', $data);
}

public function save_pi_details()
{
    $this->ensure_service_pi_dependencies();
    $this->load->model('Service_model');
    $this->load->helper('number');

    $post = $this->input->post();
    $opportunity_id = (int) ($post['opportunity_id'] ?? 0);
    $pi_id = (int) ($post['pi_id'] ?? 0);
    $action_type = trim((string) ($post['action_type'] ?? 'save'));
    $user_id = $this->session->userdata['logged_in']['user_id'];

    $opportunity = $this->Service_model->get_opportunity_details($opportunity_id);
    if (!$opportunity) {
        show_404();
    }

    $pi_stage_id = $this->ensure_service_pi_dependencies();
    $quote_bundle = $this->get_latest_service_quote_with_items(
        $opportunity_id,
        $opportunity->op_no ?? ''
    );
    $latest_quote = $quote_bundle['quote'];
    $latest_po = $this->get_latest_service_po($opportunity_id);
    $totals = $this->parse_service_pi_items_from_post($post);

    if (empty($totals['items'])) {
        $this->session->set_flashdata('error', 'Please add at least one PI line item.');
        redirect(page_url . 'ServiceLeads/create_pi/' . $opportunity_id);
    }

    $currency = strtoupper(trim((string) ($post['currency'] ?? ($latest_quote->currency ?? 'INR'))));
    $buyer_order_no = trim((string) ($post['buyer_order_no'] ?? ($latest_po->po_number ?? '')));
    $buyer_order_date = !empty($post['buyer_order_date']) ? date('Y-m-d', strtotime($post['buyer_order_date'])) : (!empty($latest_po->po_date) ? $latest_po->po_date : null);

    $pi_number = trim((string) ($post['pi_no'] ?? ''));
    if ($pi_number === '') {
        $pi_number = (string) $this->service_pi_model->get_next_pi_no();
    }

    $declaration_text = trim((string) ($post['declaration_text'] ?? ''));
    if ($declaration_text === '') {
        $declaration_text = 'We declare that this invoice shows the actual price of the Service described and that all particulars are true and correct.';
    }

    $notes_text = trim((string) ($post['notes'] ?? ''));
    if ($notes_text === '') {
        $notes_text = $this->get_service_pi_default_notes();
    }

    $pi_data = [
        'opportunity_id' => $opportunity_id,
        'quote_id' => (int) ($post['quote_id'] ?? ($latest_quote->id ?? 0)) ?: null,
        'po_id' => (int) ($post['po_id'] ?? ($latest_po->id ?? 0)) ?: null,
        'customer_id' => (int) ($opportunity->customer_id ?? 0) ?: null,
        'pi_no' => $pi_number,
        'pi_date' => !empty($post['pi_date']) ? date('Y-m-d', strtotime($post['pi_date'])) : date('Y-m-d'),
        'pi_title' => $this->build_service_pi_title($buyer_order_no, $buyer_order_date),
        'currency' => $currency !== '' ? $currency : 'INR',
        'buyer_name' => trim((string) ($post['buyer_name'] ?? '')),
        'buyer_contact' => trim((string) ($post['buyer_contact'] ?? '')),
        'buyer_address' => trim((string) ($post['buyer_address'] ?? '')),
        'buyer_gstin' => trim((string) ($post['buyer_gstin'] ?? '')),
        'consignee_name' => trim((string) ($post['consignee_name'] ?? '')),
        'consignee_contact' => trim((string) ($post['consignee_contact'] ?? '')),
        'consignee_address' => trim((string) ($post['consignee_address'] ?? '')),
        'consignee_ecc_no' => trim((string) ($post['consignee_ecc_no'] ?? '')),
        'consignee_phone' => trim((string) ($post['consignee_phone'] ?? '')),
        'buyer_order_no' => $buyer_order_no,
        'buyer_order_date' => $buyer_order_date,
        'payment_terms' => trim((string) ($post['payment_terms'] ?? '100% Against PI')),
        'bank_name' => trim((string) ($post['bank_name'] ?? '')),
        'account_no' => trim((string) ($post['account_no'] ?? '')),
        'bank_address' => trim((string) ($post['bank_address'] ?? '')),
        'ifsc_code' => trim((string) ($post['ifsc_code'] ?? '')),
        'account_type' => trim((string) ($post['account_type'] ?? '')),
        'account_holder' => trim((string) ($post['account_holder'] ?? '')),
        'swift_code' => trim((string) ($post['swift_code'] ?? '')),
        'country_of_origin' => trim((string) ($post['country_of_origin'] ?? 'India')),
        'gst_percent' => $totals['gst_percent'],
        'basic_amount' => $totals['basic_amount'],
        'gst_amount' => $totals['gst_amount'],
        'grand_total' => $totals['grand_total'],
        'amount_in_words' => function_exists('get_amount_in_words')
            ? get_amount_in_words($totals['grand_total'], $currency)
            : number_format($totals['grand_total'], 2),
        'declaration_text' => $declaration_text,
        'notes' => $notes_text,
        'updated_by' => $user_id,
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    $this->db->trans_start();

    if ($pi_id > 0 && $this->service_pi_model->get_by_id($pi_id)) {
        $this->db->where('id', $pi_id)->update($this->service_pi_model->get_table_name(), $pi_data);
        $history_message = 'PI updated: ' . $pi_data['pi_no'];
    } else {
        $pi_data['created_by'] = $user_id;
        $pi_data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->service_pi_model->get_table_name(), $pi_data);
        $pi_id = (int) $this->db->insert_id();
        $history_message = 'PI created: ' . $pi_data['pi_no'];
    }

    $this->db->where('service_pi_id', $pi_id)->delete($this->service_pi_model->get_item_table_name());

    $item_rows = [];
    foreach ($totals['items'] as $item) {
        $item_rows[] = [
            'service_pi_id' => $pi_id,
            'sort_order' => $item['sort_order'],
            'sac_code' => $item['sac_code'],
            'description' => $item['description'],
            'unit_rate' => $item['unit_rate'],
            'no_of_days' => $item['no_of_days'],
            'no_of_engineers' => $item['no_of_engineers'],
            'uom' => $item['uom'],
            'is_customer_scope' => $item['is_customer_scope'],
            'scope_note' => $item['scope_note'],
            'row_total' => $item['row_total'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    if (!empty($item_rows)) {
        $this->db->insert_batch($this->service_pi_model->get_item_table_name(), $item_rows);
    }

    $this->db->where('opportunity_id', $opportunity_id)->update('service_opportunities', [
        'current_stage_id' => $pi_stage_id,
        'remarks' => $history_message,
    ]);

    $this->insert_service_progress_history($opportunity_id, $pi_stage_id, $history_message);

    $this->db->trans_complete();

    if ($this->db->trans_status() === false) {
        $this->session->set_flashdata('error', 'Failed to save PI details. Please try again.');
        redirect(page_url . 'ServiceLeads/create_pi/' . $opportunity_id);
    }

    $this->session->set_flashdata('success', 'PI details saved successfully.');

    if ($action_type === 'pdf') {
        redirect(page_url . 'ServiceLeads/view_pi_pdf/' . $pi_id);
    }

    redirect(page_url . 'ServiceLeads/opportunity_detail/' . $opportunity_id);
}

public function view_pi_pdf($pi_id = null)
{
    $this->ensure_service_pi_dependencies();
    $this->load->model('Service_model');
    $this->load->helper('number');

    $pi_id = $pi_id !== null ? (int) $pi_id : (int) $this->uri->segment(3);
    $pi = $this->service_pi_model->get_by_id($pi_id);

    if (!$pi) {
        show_404();
    }

    $opportunity = $this->Service_model->get_opportunity_details($pi->opportunity_id);
    if (!$opportunity) {
        show_404();
    }

    $pdf_data = [
        'pi' => $pi,
        'items' => $this->service_pi_model->get_items($pi_id),
        'opportunity' => $opportunity,
        'customer' => $this->get_service_customer_snapshot($opportunity),
        'company_profile' => $this->get_service_company_profile(),
        'currency_symbol' => strtoupper(trim((string) $pi->currency)) === 'USD' ? '$' : '₹',
        'amount_in_words' => !empty($pi->amount_in_words)
            ? $pi->amount_in_words
            : (function_exists('get_amount_in_words') ? get_amount_in_words((float) $pi->grand_total, $pi->currency) : number_format((float) $pi->grand_total, 2)),
    ];

    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new \Dompdf\Dompdf($options);

    $html = $this->load->view('spares/service_pi_pdf_template', $pdf_data, true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_length()) {
        ob_end_clean();
    }

    $folder_path = FCPATH . 'uploads/service_pis/';
    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    file_put_contents($folder_path . $pi_id . '.pdf', $dompdf->output());

    $safe_file_name = preg_replace('/[\/\\\\:*?"<>|]+/', '-', 'PI-' . trim((string) $pi->pi_no) . ' - ' . trim((string) $opportunity->op_no));
    $safe_file_name = rtrim($safe_file_name, '-');

    $dompdf->stream($safe_file_name . '.pdf', ['Attachment' => 0]);
}


/**
 * Load Create Quotation Form.
 * The quotation number mirrors the lead opportunity number.
 */
public function create_quotation($opportunity_id) {
    $this->load->model('Service_model');
    
    // 1. Fetch Opportunity Details
    $data['opportunity'] = $this->Service_model->get_opportunity_details($opportunity_id);
    
    if (!$data['opportunity']) { 
        show_404(); 
    }

    // 2. Fetch the customer from either table to check country (101 = India)
    // Check Spares Table First
    $customer = $this->db->get_where('spares_customers', [
        'customer_id' => $data['opportunity']->customer_id
    ])->row();

    // If not in Spares, check Marketing Table (customer_detail)
    if (!$customer) {
        $customer = $this->db->select('country as country_id, gst')->get_where('customer_detail', [
            'id' => $data['opportunity']->customer_id
        ])->row();
    }
    
    // Determine Type Prefix: DOM for India, EXP for others
    $is_export = ($customer && $customer->country_id != 101);
    $type_prefix = $is_export ? "EXP" : "DOM";
    $data['is_export'] = $is_export;

    // 3. Keep quotation number aligned with the lead opportunity number.
    $data['new_quotation_no'] = !empty($data['opportunity']->op_no)
        ? (string) $data['opportunity']->op_no
        : $this->generateOppNo((int) ($data['opportunity']->op_type ?? 1), 0);

    // 4. Fetch the service master charges for the dropdown
    $data['service_master'] = $this->db->get_where('service_charges_master', [
        'status' => 1
    ])->result();

    // 5. Load the view
    $this->load->view('spares/service_create_quotation_view', $data);
}

public function get_clone_quotation_by_opportunity()
{
    $this->output->set_content_type('application/json');

    $source_opportunity_no = trim((string) $this->input->post('opportunity_no'));
    $current_opportunity_id = (int) $this->input->post('current_opportunity_id');

    if ($source_opportunity_no === '') {
        $this->output->set_output(json_encode([
            'status' => false,
            'message' => 'Please enter an opportunity number.',
        ]));
        return;
    }

    $source_opportunity = $this->db
        ->select('opportunity_id, op_no, customer_id')
        ->from('service_opportunities')
        ->where("UPPER(TRIM(op_no)) = " . $this->db->escape(strtoupper($source_opportunity_no)), null, false)
        ->get()
        ->row();

    if (!$source_opportunity) {
        $this->output->set_output(json_encode([
            'status' => false,
            'message' => 'Opportunity not found.',
        ]));
        return;
    }

    $source_quote = $this->get_latest_service_quote_row(
        (int) $source_opportunity->opportunity_id,
        (string) $source_opportunity->op_no
    );

    if (!$source_quote) {
        $this->output->set_output(json_encode([
            'status' => false,
            'message' => 'No quotation found for this opportunity.',
        ]));
        return;
    }

    $source_items = $this->db
        ->order_by('id', 'ASC')
        ->get_where('service_quotation_items', ['service_quote_id' => (int) $source_quote->id])
        ->result();

    $item_discount_total = 0;
    $formatted_items = [];

    foreach ($source_items as $item) {
        $discount_amount = (float) ($item->discount_amount ?? 0);
        $item_discount_total += $discount_amount;

        $formatted_items[] = [
            'charge_id' => (string) $item->charge_id,
            'description' => (string) $item->description,
            'rate' => (float) $item->rate,
            'no_of_days' => (float) $item->no_of_days,
            'no_of_engineers' => (float) $item->no_of_engineers,
            'uom' => (string) $item->uom,
            'scope' => (string) $item->is_customer_scope,
            'discount' => $discount_amount,
        ];
    }

    $current_opportunity = null;
    if ($current_opportunity_id > 0) {
        $current_opportunity = $this->db
            ->select('opportunity_id, customer_id')
            ->from('service_opportunities')
            ->where('opportunity_id', $current_opportunity_id)
            ->get()
            ->row();
    }

    $same_customer = $current_opportunity
        && (int) $current_opportunity->customer_id === (int) $source_opportunity->customer_id;

    $overall_discount = (float) $source_quote->total_discount - $item_discount_total;
    if ($overall_discount < 0) {
        $overall_discount = 0;
    }

    $response = [
        'status' => true,
        'message' => 'Quotation data loaded.',
        'same_customer' => $same_customer,
        'source' => [
            'quote_id' => (int) $source_quote->id,
            'quotation_no' => (string) $source_quote->quotation_no,
            'opportunity_id' => (int) $source_opportunity->opportunity_id,
            'opportunity_no' => (string) $source_opportunity->op_no,
            'quotation_date' => (string) $source_quote->quotation_date,
        ],
        'quote' => [
            'currency' => (string) $source_quote->currency,
            'kindattention' => (string) $source_quote->kindattention,
            'contactno' => (string) $source_quote->contactno,
            'email' => (string) $source_quote->email,
            'subject' => (string) $source_quote->subject,
            'gst_percent' => (float) $source_quote->gst_percent,
            'wht_percent' => (float) ($source_quote->wht_percent ?? 0),
            'ex_works_amount' => (float) ($source_quote->ex_works_amount ?? 0),
            'freight_amount' => (float) ($source_quote->freight_amount ?? 0),
            'overall_discount' => $overall_discount,
            'terms_conditions' => (string) $source_quote->terms_conditions,
        ],
        'items' => $formatted_items,
    ];

    $this->output->set_output(json_encode($response));
}

public function generate_quotation_pdf()
{
    // 1. CRITICAL: Clear any existing output buffers to prevent PDF corruption
    if (ob_get_length()) {
        ob_end_clean();
    }

    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';

    $post = $this->input->post();
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $opp_id = $post['opportunity_id'];
    $cust_id = $post['customer_id'];

    /** CHECK ORIGIN TABLE & FETCH NAMES FOR NOTIFICATION **/
    $this->db->select('
        so.opportunity_id,
        so.customer_table_origin,
        so.created_by,
        CONCAT(su.first_name, " ", su.last_name) as created_by_name,
        CASE 
            WHEN so.customer_table_origin IN ("spare", "spares")
            THEN sc.company_name
            ELSE cd.company_name
        END as company_name
    ', false);

    $this->db->from('service_opportunities so');
    $this->db->join('system_users su', 'su.user_id = so.created_by', 'left');
    $this->db->join('spares_customers sc', 'sc.customer_id = so.customer_id AND so.customer_table_origin IN ("spare", "spares")', 'left');
    $this->db->join('customer_detail cd', 'cd.id = so.customer_id AND (so.customer_table_origin IS NULL OR so.customer_table_origin NOT IN ("spare", "spares"))', 'left');
    $this->db->where('so.opportunity_id', $opp_id);

    $resultCust = $this->db->get()->row();

    $companyname = '';
    $leadownername = '';

    if ($resultCust) {
        $companyname = ucwords(strtolower($resultCust->company_name));
        $leadownername = $resultCust->created_by_name;
    }

    // 2. Financial Calculations
    $gross_basic = 0;
    $item_discount_total = 0;

    if (!empty($post['rate'])) {
        foreach ($post['rate'] as $key => $val) {

            // Only calculate for items in SPM Scope (scope == 0)
            if (isset($post['scope'][$key]) && $post['scope'][$key] == '0') {

                $rate = (float)$val;
                $days = isset($post['no_of_days'][$key]) ? (float)$post['no_of_days'][$key] : 0;
                $engineers = isset($post['no_of_engineers'][$key]) ? (int)$post['no_of_engineers'][$key] : 0;
                $line_disc = isset($post['discount'][$key]) ? (float)$post['discount'][$key] : 0;

                $line_gross = ($rate * $days * $engineers);

                $gross_basic += $line_gross;
                $item_discount_total += $line_disc;
            }
        }
    }

    $overall_discount = isset($post['overall_discount']) ? (float)$post['overall_discount'] : 0;
    $total_discount = $item_discount_total + $overall_discount;

    $net_taxable = $gross_basic - $total_discount;
    if ($net_taxable < 0) {
        $net_taxable = 0;
    }

    $gst_percent = isset($post['gst_percent']) ? (float)$post['gst_percent'] : 0;
    $gst_amount = ($net_taxable * $gst_percent) / 100;

    $wht_percent = isset($post['wht_percent']) ? (float)$post['wht_percent'] : 0;
    $wht_amount = ($net_taxable * $wht_percent) / 100;

    $ex_works = isset($post['ex_works_amount']) ? (float)$post['ex_works_amount'] : 0;
    $freight = isset($post['freight_amount']) ? (float)$post['freight_amount'] : 0;

    $grand_total = $net_taxable + $gst_amount + $wht_amount + $ex_works + $freight;

    // 3. Fetch Probability for Stage 3
    $stage_data = $this->db->get_where('service_lead_stages', ['stage_id' => 3])->row();
    $new_probability = $stage_data->default_probability ?? 0;

    // 4. Prepare Master Data Record
    $quote_data = [
        'quotation_no'       => $post['quotation_no'],
        'quotation_date'     => $post['quotation_date'],
        'opportunity_id'     => $opp_id,
        'kindattention'      => $post['kindattention'] ?? '',
        'contactno'          => $post['customercontactno'] ?? '',
        'email'              => $post['customeremail'] ?? '',
        'customer_id'        => $cust_id,
        'subject'            => $post['subject'],
        'currency'           => $post['currency'],
        'total_basic_amount' => $gross_basic,
        'total_discount'     => $total_discount,
        'gst_percent'        => $gst_percent,
        'wht_percent'        => $wht_percent,
        'ex_works_amount'    => $ex_works,
        'freight_amount'     => $freight,
        'grand_total'        => $grand_total,
        'payment_terms'      => $post['payment_terms'] ?? 'Within 35 days',
        'validity'           => $post['validity'] ?? '2 Months',
        'terms_conditions'   => $post['terms_conditions'],
        'created_by'         => $user_id,
        'created_at'         => date('Y-m-d H:i:s')
    ];

    $this->db->trans_start();

    // SYNC TAX/GST NO
    if (!empty($post['gst_no'])) {
        $exists_in_spares = $this->db->get_where('spares_customers', ['customer_id' => $cust_id])->num_rows();

        if ($exists_in_spares > 0) {
            $this->db
                ->where('customer_id', $cust_id)
                ->update('spares_customers', ['tax_number' => $post['gst_no']]);
        } else {
            $this->db
                ->where('id', $cust_id)
                ->update('customer_detail', ['gst' => $post['gst_no']]);
        }
    }

    // A. Insert Master Quotation
    $this->db->insert('service_quotations', $quote_data);
    $quote_id = $this->db->insert_id();

    // B. Insert Line Items
    if (!empty($post['charge_id'])) {

        $items_to_save = [];

        foreach ($post['charge_id'] as $key => $cid) {

            if (empty($cid)) {
                continue;
            }

            $rate = isset($post['rate'][$key]) ? (float)$post['rate'][$key] : 0;
            $days = isset($post['no_of_days'][$key]) ? (float)$post['no_of_days'][$key] : 0;
            $engineers = isset($post['no_of_engineers'][$key]) ? (int)$post['no_of_engineers'][$key] : 0;
            $discount = isset($post['discount'][$key]) ? (float)$post['discount'][$key] : 0;
            $scope = isset($post['scope'][$key]) ? $post['scope'][$key] : 0;

            $line_raw = ($rate * $days * $engineers);
            $row_total = ($scope == '0') ? ($line_raw - $discount) : 0;

            if ($row_total < 0) {
                $row_total = 0;
            }

            $items_to_save[] = [
                'service_quote_id' => $quote_id,
                'charge_id'        => $cid,
                'description'      => $post['description'][$key] ?? '',
                'rate'             => $rate,
                'no_of_days'       => $days,
                'no_of_engineers'  => $engineers,
                'uom'              => $post['uom'][$key] ?? 'Day',
                'is_customer_scope'=> $scope,
                'discount_amount'  => $discount,
                'row_total'        => $row_total
            ];
        }

        if (!empty($items_to_save)) {
            $this->db->insert_batch('service_quotation_items', $items_to_save);
        }
    }

    // C. Update Lead Stage to 3
    $this->db
        ->where('opportunity_id', $opp_id)
        ->update('service_opportunities', [
            'current_stage_id' => 3,
            'probability'      => $new_probability
        ]);

    // D. History Log
    $this->db->insert('service_progress_history', [
        'opportunity_id' => $opp_id,
        'stage_id'       => 3,
        'remarks'        => "Quotation Updated/Revised: " . $post['quotation_no'] . ". Sent for approval.",
        'added_by'       => $user_id,
        'added_on'       => date('Y-m-d H:i:s')
    ]);

    // E. INTERNAL APP NOTIFICATION
    $notifyData = [
        'user_id'      => 139,
        'title'        => 'Service Quote Approval Required',
        'message'      => "Service Quotation for $companyname by $leadownername needs approval.",
        'type'         => 'service_quote_approval',
        'reference_id' => $opp_id
    ];

    $this->db->insert('app_notifications', $notifyData);

    // F. EXTERNAL PUSH NOTIFICATION (CURL)
    $push_url = page_url . 'mobile/Api/send_push_notification';

    $pushData = [
        "title"   => "📄 Service Quote Approval Required",
        "message" => "Quotation for $companyname by $leadownername needs approval.",
        "quote_id" => $opp_id
    ];

    $ch = curl_init($push_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pushData));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $push_response = curl_exec($ch);

    if (curl_errno($ch)) {
        log_message('error', 'Push Error: ' . curl_error($ch));
    }

    curl_close($ch);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        $this->session->set_flashdata(
            'error',
            'Something went wrong while saving quotation. Please try again.'
        );

        redirect(page_url.'ServiceLeads/opportunity_list/3');
        exit;
    }

    // 5. PDF Data & Template Loading
    $pdf_data = $quote_data;
    $pdf_data['gst_amount'] = $gst_amount;
    $pdf_data['wht_amount'] = $wht_amount;

    $pdf_data['items'] = $this->db
        ->select('sqi.*, scm.charge_name, so.op_no')
        ->from('service_quotation_items sqi')
        ->join('service_quotations squo', 'squo.id = sqi.service_quote_id')
        ->join('service_opportunities so', 'so.opportunity_id = squo.opportunity_id')
        ->join('service_charges_master scm', 'scm.id = sqi.charge_id')
        ->where('sqi.service_quote_id', $quote_id)
        ->get()
        ->result();

    // Customer details for PDF
    $customer = $this->db
        ->select('company_name, address, contact_person_no as phone, email, country_id, contact_person as customername')
        ->get_where('spares_customers', ['customer_id' => $cust_id])
        ->row();

    if (!$customer) {
        $customer = $this->db
            ->select('company_name, address, contact_no as phone, email, country as country_id, customer_name as customername')
            ->get_where('customer_detail', ['id' => $cust_id])
            ->row();
    }

    $pdf_data['customer'] = $customer;

    $this->load->helper('number');

    $pdf_data['amount_in_words'] = function_exists('get_amount_in_words')
        ? get_amount_in_words($grand_total, $post['currency'])
        : number_format($grand_total, 2);

    $pdf_data['company_info'] = [
        'name'      => 'Shubham Flexible Packaging Machine Pvt. Ltd.',
        'address'   => 'B-8A, Sector 59 Part II, Ballabgarh, Faridabad - 121004, Haryana, India',
        'logo'      => 'https://pms.shubhampack.in/assets/images/shubhampack.png',
        'colorcode' => '#003366',
        'email'     => 'service1@shubhampack.com',
        'website'   => 'www.shubhampack.com'
    ];

    // 6. Generate and Save PDF
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', TRUE);

    $dompdf = new \Dompdf\Dompdf($options);

    $html = $this->load->view('spares/service_quotation_pdf_template', $pdf_data, true);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_length()) {
        ob_end_clean();
    }

    // Save PDF to server
    $filename1 = $quote_id . ".pdf";
    $folder_path = FCPATH . 'uploads/service_quotations/';

    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    file_put_contents($folder_path . $filename1, $dompdf->output());

    // IMPORTANT:
    // Do not open/stream PDF in browser.
    // PDF has been generated and saved successfully.

    $this->session->set_flashdata(
        'success',
        'Quotation has been generated successfully and sent for approval.'
    );

    redirect(page_url.'ServiceLeads/opportunity_list/3');
    exit;
}

public function view_quotation_pdf($quote_id = null, $flag = 0) {
    // 1. Ensure we have the correct ID from the URL segment if not passed
    if ($quote_id == null) {
        $quote_id = $this->uri->segment(3);
    }

    // 2. CRITICAL FIX: Fetch by 'id' (Primary Key) to get the EXACT version
    // Fetching by opportunity_id returns the wrong version if multiple exist
    $quote = $this->db->get_where('service_quotations', ['id' => $quote_id])->row();
    
    if (!$quote) {
        show_404();
        return;
    }


    $pdf_data = (array)$quote;

    // 3. Fetch items specifically linked to THIS quote version ID
    $this->db->select('sqi.*, scm.charge_name, so.op_no');
    $this->db->from('service_quotation_items sqi');
    $this->db->join('service_opportunities so', 'so.opportunity_id = ' . $quote->opportunity_id, 'left');
    $this->db->join('service_charges_master scm', 'scm.id = sqi.charge_id', 'left');
    $this->db->where('sqi.service_quote_id', $quote_id); 
    
    $pdf_data['items'] = $this->db->get()->result();

    // 4. Dual Table Customer Lookup
    $customer = $this->db->select('company_name, address, contact_person_no as phone, email, contact_person as customername')
                         ->get_where('spares_customers', ['customer_id' => $quote->customer_id])->row();
    
    if (!$customer) {
        $customer = $this->db->select('company_name, address, contact_no as phone, email, customer_name as customername')
                             ->get_where('customer_detail', ['id' => $quote->customer_id])->row();
    }
    $pdf_data['customer'] = $customer;

    // 5. Financial Calculations for the View[cite: 1]
    $net_taxable = $quote->total_basic_amount - $quote->total_discount;
    
    $pdf_data['gst_amount'] = ($net_taxable * $quote->gst_percent) / 100;
    $pdf_data['wht_amount'] = ($net_taxable * ($quote->wht_percent ?? 0)) / 100;
    $pdf_data['ex_works_amount'] = $quote->ex_works_amount ?? 0;
    $pdf_data['freight_amount'] = $quote->freight_amount ?? 0;
    
    $this->load->helper('number');
    $pdf_data['amount_in_words'] = function_exists('get_amount_in_words') 
        ? get_amount_in_words($quote->grand_total, $quote->currency) 
        : number_format($quote->grand_total, 2);

    $pdf_data['company_info'] = [
        'name' => 'Shubham Flexible Packaging Machine Pvt. Ltd.',
        'address' => 'B-8A, Sector 59 Part II, Ballabgarh, Faridabad - 121004, Haryana, India',
        'logo' => 'https://pms.shubhampack.in/assets/images/shubhampack.png',
        'colorcode' => '#003366',
        'email' => 'service1@shubhampack.com',
        'website' => 'www.shubhampack.com'
    ];

    // 6. PDF Generation Engine
    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', TRUE);
    $options->set('defaultFont', 'Helvetica');
    $dompdf = new \Dompdf\Dompdf($options);
    
    $html = $this->load->view('spares/service_quotation_pdf_template', $pdf_data, true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    if (ob_get_length()) ob_end_clean();
    
    // 7. Save and Stream
    $filename = $quote_id . ".pdf";
    $folder_path = FCPATH . 'uploads/service_quotations/';

    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    file_put_contents($folder_path . $filename, $dompdf->output());

/** GET FILE NAME **/
    $version='';
  $q = $this->db->select('opportunity_id')->from('service_quotations')->where('id',$this->uri->segment(3))->get();
    foreach($q->result() as $oppninfo);
    $q1 = $this->db->select('op_no')->from('service_opportunities')->where('opportunity_id',$quote->opportunity_id)->get();
    foreach($q1->result() as $opportunitynoinfo);
    $opno=$opportunitynoinfo->op_no;

    $countingnum = '';
    $quotation_count = $this->db
    ->where('opportunity_id', $oppninfo->opportunity_id)
    ->count_all_results('service_quotations');

    if ($quotation_count > 0) {
    $versioncount = $quotation_count;
    if($versioncount>1){
    $countingnum = $versioncount-1;
    }else{
    $countingnum = '';
    }
    if($countingnum!==''){
    $version="V" . $countingnum;
    }

    }


    $download_filename = 'Quotation - ' . trim((string) $opno);

    if (trim((string) $version) !== '') {
        $download_filename .= ' ' . trim((string) $version);
    }

    // Real "/" characters are not valid in downloaded filenames, so we
    // swap them with a visually equivalent safe slash for browser downloads.
    $download_filename = str_replace('/', '／', $download_filename);
    $download_filename = preg_replace('/[\\\\:*?"<>|]+/', '-', $download_filename);
    $download_filename .= '.pdf';


    if ($flag != 1) {
        $dompdf->stream($download_filename, ["Attachment" => 0]);
    }
}

public function edit_opportunity($id) {
    $this->load->model('Service_model');
    $data['opportunity'] = $this->Service_model->get_opportunity_details($id);
    
    if (!$data['opportunity']) { 
        $this->session->set_flashdata('error', 'Opportunity not found.');
        redirect(page_url . 'ServiceLeads/opportunity_list');
    }

    // Fetch master data for dropdowns
    $data['sources'] = $this->db->get_where('lead_source', ['status' => 1])->result();
    $data['marketing_persons'] = $this->db->select('user_id, first_name, last_name')
        ->from('system_users')
        ->where('department_id', 22)
        ->where('user_status', 1)
        ->order_by('first_name')
        ->get()->result();
    $data['countries'] = $this->db->get_where('countries', ['country_status' => 1])->result();

    $this->load->view('spares/service_edit_opportunity_view', $data);
}

public function clone_opportunity($id) {
    $current_user_id = $this->session->userdata['logged_in']['user_id'] ?? 0;
    if ($current_user_id <= 0) {
        $this->session->set_flashdata('error', 'Your session has expired. Please login again.');
        redirect(page_url . 'ServiceLeads/opportunity_list');
    }

    $source = $this->db->get_where('service_opportunities', ['opportunity_id' => (int) $id])->row();
    if (!$source) {
        $this->session->set_flashdata('error', 'Opportunity not found for cloning.');
        redirect(page_url . 'ServiceLeads/opportunity_list');
    }

    $new_opportunity_no = $this->generateOppNo($source->op_type, 0);
    $clone_data = array(
        'op_no' => $new_opportunity_no,
        'op_date' => date('Y-m-d'),
        'op_type' => $source->op_type,
        'customer_id' => $source->customer_id,
        'marketing_person_id' => $source->marketing_person_id,
        'source_id' => $source->source_id,
        'customer_address' => $source->customer_address,
        'customer_contact_no' => $source->customer_contact_no,
        'customer_email' => $source->customer_email,
        'customer_table_origin' => isset($source->customer_table_origin) ? $source->customer_table_origin : null,
        'remarks' => $source->remarks,
        'current_stage_id' => 1,
        'probability' => $this->get_service_stage_default_probability(1),
        'status' => 'Open',
        'gst' => isset($source->gst) ? $source->gst : '',
        'created_by' => $current_user_id,
        'created_at' => date('Y-m-d H:i:s')
    );

    $this->db->trans_start();
    $this->db->insert('service_opportunities', $clone_data);
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        $this->session->set_flashdata('error', 'Unable to clone the opportunity right now. Please try again.');
        redirect(page_url . 'ServiceLeads/opportunity_list');
    }

    $this->session->set_flashdata('success', 'Opportunity cloned successfully as ' . $new_opportunity_no . '.');
    redirect(page_url . 'ServiceLeads/opportunity_list/1');
}

public function update_opportunity_action($id) {
    $post = $this->input->post();
    $customer_origin = $this->normalize_service_customer_origin($post['customer_origin'] ?? '');
    $op_type = (int) ($post['op_type'] ?? 1);
    if (!in_array($op_type, [1, 2], true)) {
        $op_type = 1;
    }
    $existing_opportunity = $this->db
        ->select('op_no')
        ->from('service_opportunities')
        ->where('opportunity_id', (int) $id)
        ->limit(1)
        ->get()
        ->row();

    if (!$existing_opportunity) {
        $this->session->set_flashdata('error', 'Opportunity not found.');
        redirect(page_url . 'ServiceLeads/opportunity_list');
        return;
    }
    
    $update_data = [
        'op_no'               => $this->rebuild_service_opportunity_no_for_type($existing_opportunity->op_no ?? '', $op_type),
        'op_date'             => $post['op_date'],
        'op_type'             => $op_type,
        'customer_id'         => $post['customer'],
        'customer_table_origin' => $customer_origin,
        'source_id'           => $post['lsource'],
        'marketing_person_id' => $post['marketing'],
        'customer_address'    => $post['address'],
        'customer_contact_no' => $post['customercontactno'],
        'customer_email'      => $post['customeremailid'],
        'remarks'             => $post['remarks'],
        'gst'                 => $post['gst']
    ];

    $this->db->trans_start();
    $this->db->where('opportunity_id', $id);
    $updated = $this->db->update('service_opportunities', $update_data);

    $customer_id = (int) ($post['customer'] ?? 0);
    if ($customer_id > 0) {
        $customer_table = $customer_origin === 'spare' ? 'spares_customers' : 'customer_detail';
        $customer_key = $customer_origin === 'spare' ? 'customer_id' : 'id';
        $country_field = $customer_origin === 'spare' ? 'country_id' : 'country';
        $phone_field = $customer_origin === 'spare' ? 'contact_person_no' : 'contact_no';
        $gst_field = $customer_origin === 'spare' ? 'tax_number' : 'gst';

        $customer_data = $this->db
            ->where($customer_key, $customer_id)
            ->get($customer_table)
            ->row_array();

        if (!empty($customer_data)) {
            $update_customer = [];

            if ((empty($customer_data[$country_field]) || (string) $customer_data[$country_field] === '0') && !empty($post['country'])) {
                $update_customer[$country_field] = $post['country'];
            }

            if (empty($customer_data[$phone_field]) && !empty($post['customercontactno'])) {
                $update_customer[$phone_field] = $post['customercontactno'];
            }

            if (empty($customer_data[$gst_field]) && !empty($post['gst'])) {
                $update_customer[$gst_field] = $post['gst'];
            }

            if (empty($customer_data['email']) && !empty($post['customeremailid'])) {
                $update_customer['email'] = $post['customeremailid'];
            }

            if (empty($customer_data['address']) && !empty($post['address'])) {
                $update_customer['address'] = $post['address'];
            }

            if (!empty($update_customer)) {
                $this->db->where($customer_key, $customer_id)->update($customer_table, $update_customer);
            }
        }
    }

    $this->db->trans_complete();

    if ($updated && $this->db->trans_status() !== FALSE) {
        $this->session->set_flashdata('success', 'Service Opportunity updated successfully.');
    } else {
        $this->session->set_flashdata('error', 'Failed to update opportunity.');
    }

    redirect(page_url . 'ServiceLeads/opportunity_list');
}

public function get_customer_by_company() {
    $search = $this->input->get('searchTerm');

    // 1. Marketing Query (customer_detail table)
    $this->db->select("CONCAT(company_name, ' | ', address) as text, id as id, 'marketing' as origin", FALSE);
    $this->db->from('customer_detail');
    // Broaden search to name OR address
    $this->db->group_start();
        $this->db->like('company_name', $search);
        $this->db->or_like('address', $search);
    $this->db->group_end();
    $query1 = $this->db->get_compiled_select();

    // 2. Spares Query (spares_customers table)
    $this->db->select("CONCAT(company_name, ' | ', address) as text, customer_id as id, 'spares' as origin", FALSE);
    $this->db->from('spares_customers');
    $this->db->group_start();
        $this->db->like('company_name', $search);
        $this->db->or_like('address', $search);
    $this->db->group_end();
    $query2 = $this->db->get_compiled_select();

    // UNION with a higher limit to ensure specific companies aren't cut off
    $final_query = $this->db->query($query1 . " UNION " . $query2 . " LIMIT 50");
    echo json_encode($final_query->result());
}

/**
 * REVISED QUOTATION: Load existing quote data into a new draft
 * Stage ID 4: Revised Quotation
 */
/**
 * REVISED QUOTATION: Load existing quote data into a new draft
 */
/**
 * REVISED QUOTATION: Load existing quote data into a new draft
 * Path: ServiceLeads/revise_quotation
 */
public function revise_quotation($opportunity_id) {
    $this->load->model('Service_model');
    $data['opportunity'] = $this->Service_model->get_opportunity_details($opportunity_id);
    
    if (!$data['opportunity']) { 
        show_404(); 
    }

    // 1. Fetch the LATEST quotation for this opportunity
    $this->db->order_by('id', 'DESC');
    $data['prev_quote'] = $this->get_latest_service_quote_row(
        $opportunity_id,
        $data['opportunity']->op_no ?? ''
    );

    if (!$data['prev_quote']) {
        $this->session->set_flashdata('error', 'No previous quotation found to revise.');
        redirect(page_url . 'ServiceLeads/opportunity_list');
    }

    // 2. Fetch previous items specifically for that quote ID
    $data['prev_items'] = $this->db->get_where('service_quotation_items', ['service_quote_id' => $data['prev_quote']->id])->result();

    // 3. Dual Table Customer Lookup for country logic
    $customer = $this->db->get_where('spares_customers', ['customer_id' => $data['opportunity']->customer_id])->row();
    if (!$customer) {
        $customer = $this->db->select('country as country_id')->get_where('customer_detail', ['id' => $data['opportunity']->customer_id])->row();
    }

    // 4. Metadata Setup
    $data['is_export'] = ($customer && $customer->country_id != 101);
    $data['new_quotation_no'] = $data['prev_quote']->quotation_no; 
    $data['service_master'] = $this->db->get_where('service_charges_master', ['status' => 1])->result();
    $data['is_revision'] = true;

    $this->load->view('spares/service_revised_quotation_view', $data);
}

public function po_received($opportunity_id) {
    $this->load->model('Service_model');
    $data['opportunity'] = $this->Service_model->get_opportunity_details($opportunity_id);
    
    if (!$data['opportunity']) { show_404(); }

    // Fetch latest quotation amount to pre-fill PO amount
    $latest_quote = $this->get_latest_service_quote_row(
        $opportunity_id,
        $data['opportunity']->op_no ?? ''
    );
    $data['suggested_amount'] = $latest_quote ? $latest_quote->grand_total : 0;
    
    $this->load->view('spares/service_po_received_view', $data);
}

public function save_po_details() {
    $post = $this->input->post();
    $opp_id = $post['opportunity_id'];

    // 1. Handle File Upload
    $config['upload_path']   = './uploads/service_pos/';
    $config['allowed_types'] = 'pdf|jpg|jpeg|png';
    $config['encrypt_name']  = TRUE;

    if (!is_dir($config['upload_path'])) { mkdir($config['upload_path'], 0777, TRUE); }
    $this->load->library('upload', $config);

    if ($this->upload->do_upload('po_file')) {
        $file_data = $this->upload->data();
        
        $po_data = [
            'opportunity_id' => $opp_id,
            'po_number'      => $post['po_number'],
            'po_date'        => $post['po_date'],
            'po_amount'      => $post['po_amount'],
            'po_attachment'  => $file_data['file_name'],
            'remarks'        => $post['po_remarks'],
            'created_by'     => $this->session->userdata['logged_in']['user_id'],
            'created_at'     => date('Y-m-d H:i:s')
        ];

        $this->db->trans_start();
        // Insert PO
        $this->db->insert('service_purchase_orders', $po_data);
        
        // Move Stage to 7 (Order Won / Completed)
        $this->db->where('opportunity_id', $opp_id)->update('service_opportunities', ['current_stage_id' => 7]);
        $this->db->trans_complete();

        $this->session->set_flashdata('success', 'Purchase Order recorded and Lead Won!');
        redirect(page_url.'ServiceLeads/opportunity_list/7');
    } else {
        $this->session->set_flashdata('error', $this->upload->display_errors());
        redirect(page_url.'ServiceLeads/po_received/'.$opp_id);
    }
}

/**
 * Handle Quotation Approval or Rejection
 * From Stage 3 -> Stage 8 (Approved/Rejected)
 */
public function update_quotation_status() {
    $post = $this->input->post();
    $opp_id = (int) ($post['opportunity_id'] ?? 0);
    $status = trim((string) ($post['approval_status'] ?? '')); // 'Approved' or 'Rejected'

    $this->load->model('Service_model');
    $opportunity = $this->Service_model->get_opportunity_details($opp_id);

    if (!$opportunity) {
        $this->output->set_status_header(404);
        echo json_encode(['status' => 'error', 'message' => 'Opportunity not found.']);
        return;
    }

    $latest_quote = $this->get_latest_service_quote_row($opp_id, $opportunity->op_no ?? '');
    if (!$latest_quote) {
        $this->output->set_status_header(422);
        echo json_encode([
            'status' => 'error',
            'message' => 'No saved quotation record was found for this opportunity. Please create the quotation first.',
        ]);
        return;
    }
    
    // Logic: If approved, we might want to suggest moving to Stage 5 next.
    // If rejected, suggest moving to Stage 4 (Revised).
    // For now, we move them to Stage 8 (Approved/Rejected Quotation) as per your table.
    
    $update_data = [
        'current_stage_id' => 8, 
        'approval_status'=>$status,
        'remarks' => "Quotation " . $status . ". Reason/Notes: " . $post['approval_remarks']
    ];

    $this->db->trans_start();
    $this->db->where('opportunity_id', $opp_id)->update('service_opportunities', $update_data);
    
    // Log in History
    $this->db->insert('service_progress_history', [
        'opportunity_id' => $opp_id,
        'stage_id' => 8,
        'remarks' => "STATUS: " . $status . " | " . $post['approval_remarks'],
        'added_by' => $this->session->userdata['logged_in']['user_id'],
        'added_on' => date('Y-m-d H:i:s')
    ]);
    $this->db->trans_complete();

    echo json_encode(['status' => 'success', 'message' => 'Quotation ' . $status . ' successfully!']);
}

public function service_payment_request_form()
{
    $this->ensure_service_payment_request_dependencies();
    $this->require_service_payment_request_access();

    $selected_opportunity_id = (int) $this->input->get('opportunity_id');
    $selected_visit_id = (int) $this->input->get('visit_id');
    $selected_parent_request_id = (int) $this->input->get('parent_request_id');
    $selected_request_type = $this->sanitize_service_payment_request_type($this->input->get('request_type'), '');
    $selected_engineer_id = 0;

    if ($selected_parent_request_id > 0) {
        $parent_request = $this->service_payment_request_model->get_request_by_id($selected_parent_request_id);
        if (!empty($parent_request)) {
            if ($selected_opportunity_id <= 0) {
                $selected_opportunity_id = (int) $parent_request->opportunity_id;
            }
            if ($selected_visit_id <= 0) {
                $selected_visit_id = (int) $parent_request->visit_id;
            }
            if ($selected_request_type === '') {
                $selected_request_type = $this->sanitize_service_payment_request_type($parent_request->request_type);
            }
            $selected_engineer_id = (int) $parent_request->engineer_id;
        }
    }

    if ($selected_request_type === '') {
        $selected_request_type = $this->get_default_service_payment_request_type();
    }

    $request_basis = strtoupper(trim((string) $this->input->get('basis')));
    if (!in_array($request_basis, ['ORDER', 'VISIT'], true)) {
        $request_basis = $selected_visit_id > 0 ? 'VISIT' : 'ORDER';
    }

    if ($this->is_service_payment_foc_type($selected_request_type)) {
        $request_basis = 'ORDER';
        $selected_visit_id = 0;
    } elseif (!$this->is_service_payment_order_won_type($selected_request_type)) {
        $request_basis = 'VISIT';
    }

    $context = [
        'order' => null,
        'visit' => null,
    ];
    $related_requests = [];
    $context_totals = [
        'approved_total' => 0,
        'pending_total' => 0,
        'request_count' => 0,
    ];

    if ($selected_opportunity_id > 0 || $selected_visit_id > 0) {
        $context = $this->get_service_payment_request_context_snapshot($selected_opportunity_id, $selected_visit_id);
        if (!empty($context['order'])) {
            $selected_opportunity_id = (int) $context['order']->opportunity_id;
            $related_requests = $this->service_payment_request_model->get_related_requests(
                $selected_opportunity_id,
                !empty($context['visit']) ? (int) $context['visit']->visit_id : null
            );
            $context_totals = $this->service_payment_request_model->get_context_totals(
                $selected_opportunity_id,
                !empty($context['visit']) ? (int) $context['visit']->visit_id : null
            );
        }
    }

    $data = [
        'won_orders' => $this->get_service_payment_won_orders(),
        'visit_options' => $this->get_service_payment_visit_options(),
        'engineer_options' => $this->get_service_payment_engineer_options(),
        'request_types' => $this->get_service_payment_request_type_options(),
        'request_code' => $this->service_payment_request_model->get_next_request_code(),
        'selected_opportunity_id' => $selected_opportunity_id,
        'selected_visit_id' => $selected_visit_id,
        'selected_parent_request_id' => $selected_parent_request_id,
        'selected_request_type' => $selected_request_type,
        'selected_engineer_id' => $selected_engineer_id,
        'request_basis' => $request_basis,
        'context' => $context,
        'related_requests' => $related_requests,
        'context_totals' => $context_totals,
        'service_hod' => $this->get_service_hod_user(),
        'payment_permissions' => $this->get_service_payment_permission_snapshot(),
    ];

    $this->load->view('spares/service_payment_request_form_view', $data);
}

public function get_service_payment_request_context()
{
    $this->ensure_service_payment_request_dependencies();
    $this->require_service_payment_request_access();
    $this->output->set_content_type('application/json');

    $opportunity_id = (int) ($this->input->get('opportunity_id') ?: $this->input->post('opportunity_id'));
    $visit_id = (int) ($this->input->get('visit_id') ?: $this->input->post('visit_id'));
    $exclude_request_id = (int) ($this->input->get('exclude_request_id') ?: $this->input->post('exclude_request_id'));

    $context = $this->get_service_payment_request_context_snapshot($opportunity_id, $visit_id);
    if (empty($context['order'])) {
        echo json_encode([
            'status' => false,
            'message' => 'No won order found for the selected record.',
        ]);
        return;
    }

    $order = $context['order'];
    $visit = $context['visit'];
    $related_requests = $this->service_payment_request_model->get_related_requests(
        (int) $order->opportunity_id,
        !empty($visit) ? (int) $visit->visit_id : null,
        $exclude_request_id
    );
    $context_totals = $this->service_payment_request_model->get_context_totals(
        (int) $order->opportunity_id,
        !empty($visit) ? (int) $visit->visit_id : null
    );

    $response = [
        'status' => true,
        'order' => [
            'opportunity_id' => (int) $order->opportunity_id,
            'op_no' => (string) $order->op_no,
            'customer_id' => (int) $order->customer_id,
            'customer_name' => (string) $order->customer_name,
            'po_number' => (string) $order->po_number,
            'po_date' => (string) $order->po_date,
            'po_amount' => (float) $order->po_amount,
        ],
        'visit' => !empty($visit) ? [
            'visit_id' => (int) $visit->visit_id,
            'engineer_id' => (int) $visit->engineer_id,
            'engineer_name' => (string) $visit->engineer_name,
            'start_date' => (string) $visit->start_date,
            'end_date' => (string) $visit->end_date,
            'visit_type' => (string) $visit->visit_type,
            'visit_status' => (string) $visit->visit_status,
        ] : null,
        'totals' => $context_totals,
        'related_requests' => [],
    ];

    foreach ($related_requests as $request) {
        $response['related_requests'][] = [
            'request_id' => (int) $request->request_id,
            'request_code' => (string) $request->request_code,
            'request_type' => (string) $request->request_type,
            'request_basis' => (string) $request->request_basis,
            'amount' => (float) $request->amount,
            'status' => (string) $request->status,
            'request_date' => (string) $request->request_date,
            'created_by_name' => (string) $request->created_by_name,
            'parent_request_code' => (string) ($request->parent_request_code ?? ''),
        ];
    }

    echo json_encode($response);
}

public function save_service_payment_request()
{
    $this->ensure_service_payment_request_dependencies();
    $this->require_service_payment_request_access();

    $post = $this->input->post();
    $current_user_id = $this->get_logged_in_service_user_id();
    $opportunity_id = isset($post['opportunity_id']) ? (int) $post['opportunity_id'] : 0;
    $visit_id = isset($post['visit_id']) ? (int) $post['visit_id'] : 0;
    $parent_request_id = isset($post['parent_request_id']) ? (int) $post['parent_request_id'] : 0;
    $request_basis = strtoupper(trim((string) ($post['request_basis'] ?? 'ORDER')));
    $request_type = trim((string) ($post['request_type'] ?? $this->get_default_service_payment_request_type()));
    $selected_engineer_id = isset($post['requested_for_user_id']) ? (int) $post['requested_for_user_id'] : 0;
    $request_title = trim((string) ($post['request_title'] ?? ''));
    $amount = (float) str_replace(',', '', (string) ($post['amount'] ?? '0'));
    $purpose = trim((string) ($post['purpose'] ?? ''));
    $extension_reason = trim((string) ($post['extension_reason'] ?? ''));
    $service_from_date = trim((string) ($post['service_from_date'] ?? ''));
    $service_to_date = trim((string) ($post['service_to_date'] ?? ''));
    $allowed_request_types = $this->get_service_payment_request_type_options();

    if (!in_array($request_basis, ['ORDER', 'VISIT'], true)) {
        $request_basis = $visit_id > 0 ? 'VISIT' : 'ORDER';
    }

    $is_foc_request = $this->is_service_payment_foc_type($request_type);
    if ($is_foc_request) {
        $request_basis = 'ORDER';
        $visit_id = 0;
    } elseif (!$this->is_service_payment_order_won_type($request_type)) {
        $request_basis = 'VISIT';
    }

    if ($request_basis === 'VISIT' && $visit_id <= 0) {
        $this->session->set_flashdata('error', 'Please select a valid engineer visit for this request.');
        redirect(page_url . 'ServiceLeads/service_payment_request_form');
        return;
    }

    if (!in_array($request_type, $allowed_request_types, true)) {
        $this->session->set_flashdata('error', 'Please select a valid service payment request type.');
        redirect(page_url . 'ServiceLeads/service_payment_request_form');
        return;
    }

    if ($is_foc_request) {
        $valid_engineer_ids = array_map(function ($engineer) {
            return (int) $engineer->user_id;
        }, $this->get_service_payment_engineer_options());

        if ($selected_engineer_id <= 0 || !in_array($selected_engineer_id, $valid_engineer_ids, true)) {
            $this->session->set_flashdata('error', 'Please select a valid engineer for the FOC request.');
            redirect(page_url . 'ServiceLeads/service_payment_request_form?request_type=FOC&opportunity_id=' . $opportunity_id);
            return;
        }
    }

    $context = $this->get_service_payment_request_context_snapshot($opportunity_id, $visit_id);
    if (empty($context['order'])) {
        $this->session->set_flashdata('error', 'Please select a valid won order before creating the request.');
        redirect(page_url . 'ServiceLeads/service_payment_request_form');
        return;
    }

    $order = $context['order'];
    $visit = $context['visit'];
    if ($request_basis === 'VISIT' && empty($visit)) {
        $this->session->set_flashdata('error', 'Visit-based requests require a valid engineer visit selection.');
        redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id);
        return;
    }

    if ($amount <= 0 || $purpose === '') {
        $this->session->set_flashdata('error', 'Amount and purpose are required for every payment request.');
        redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id . '&visit_id=' . (int) $visit_id);
        return;
    }

    $parent_request = null;
    if ($parent_request_id > 0) {
        $parent_request = $this->service_payment_request_model->get_request_by_id($parent_request_id);

        if (empty($parent_request)) {
            $this->session->set_flashdata('error', 'The selected parent request was not found.');
            redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id . '&visit_id=' . (int) $visit_id);
            return;
        }

        if ((int) $parent_request->opportunity_id !== (int) $order->opportunity_id) {
            $this->session->set_flashdata('error', 'Additional funding can only be linked with requests from the same won order.');
            redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id . '&visit_id=' . (int) $visit_id);
            return;
        }

        if (
            $request_basis === 'VISIT'
            && (
                (int) $visit_id <= 0
                || (int) $parent_request->visit_id <= 0
                || (int) $parent_request->visit_id !== (int) $visit_id
            )
        ) {
            $this->session->set_flashdata('error', 'Visit-based linked requests must stay linked to the same engineer visit.');
            redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id . '&visit_id=' . (int) $visit_id);
            return;
        }
    }

    if ($service_from_date !== '' && strtotime($service_from_date) === false) {
        $service_from_date = '';
    }
    if ($service_to_date !== '' && strtotime($service_to_date) === false) {
        $service_to_date = '';
    }

    if (!empty($visit)) {
        if ($service_from_date === '') {
            $service_from_date = $visit->start_date;
        }
        if ($service_to_date === '') {
            $service_to_date = $visit->end_date;
        }
    }

    if ($service_from_date !== '' && $service_to_date !== '' && strtotime($service_to_date) < strtotime($service_from_date)) {
        $this->session->set_flashdata('error', 'Service to-date cannot be before from-date.');
        redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id . '&visit_id=' . (int) $visit_id);
        return;
    }

    $upload_result = $this->upload_service_payment_request_attachment('attachment');
    if (!$upload_result['success']) {
        $this->session->set_flashdata('error', $upload_result['error']);
        redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id . '&visit_id=' . (int) $visit_id);
        return;
    }

    $requested_for_user_id = $selected_engineer_id;
    if ($requested_for_user_id <= 0 && !empty($visit)) {
        $requested_for_user_id = (int) $visit->engineer_id;
    }
    if ($requested_for_user_id <= 0) {
        $requested_for_user_id = $current_user_id;
    }

    $service_hod = $this->get_service_hod_user();
    $hod_id = !empty($service_hod->user_id) ? (int) $service_hod->user_id : null;
    if (empty($hod_id)) {
        $this->session->set_flashdata('error', 'Service HOD is not mapped yet. Please map the service department head before raising payment requests.');
        redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id . '&visit_id=' . (int) $visit_id);
        return;
    }

    $request_code = $this->service_payment_request_model->get_next_request_code();

    $request_data = [
        'request_code' => $request_code,
        'opportunity_id' => (int) $order->opportunity_id,
        'visit_id' => !empty($visit) ? (int) $visit->visit_id : null,
        'parent_request_id' => $parent_request_id > 0 ? $parent_request_id : null,
        'request_basis' => $request_basis,
        'request_type' => $request_type,
        'request_title' => $request_title !== '' ? $request_title : null,
        'op_no' => (string) $order->op_no,
        'customer_id' => (int) $order->customer_id,
        'customer_name' => (string) $order->customer_name,
        'po_number' => (string) $order->po_number,
        'po_date' => !empty($order->po_date) ? $order->po_date : null,
        'po_amount' => (float) $order->po_amount,
        'engineer_id' => $is_foc_request ? $selected_engineer_id : (!empty($visit) ? (int) $visit->engineer_id : null),
        'requested_for_user_id' => $requested_for_user_id,
        'service_from_date' => $service_from_date !== '' ? $service_from_date : null,
        'service_to_date' => $service_to_date !== '' ? $service_to_date : null,
        'request_date' => date('Y-m-d'),
        'amount' => $amount,
        'purpose' => $purpose,
        'extension_reason' => $extension_reason !== '' ? $extension_reason : null,
        'attachment' => $upload_result['file_name'] !== '' ? $upload_result['file_name'] : null,
        'status' => 'Pending HOD Approval',
        'hod_id' => $hod_id,
        'created_by' => $current_user_id,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    $this->db->trans_start();
    $request_id = $this->service_payment_request_model->create_request($request_data);
    $this->db->trans_complete();

    if (!$this->db->trans_status() || $request_id <= 0) {
        $this->session->set_flashdata('error', 'The payment request could not be saved. Please try again.');
        redirect(page_url . 'ServiceLeads/service_payment_request_form?opportunity_id=' . (int) $order->opportunity_id . '&visit_id=' . (int) $visit_id);
        return;
    }

    if (!empty($hod_id)) {
        $message = 'New service payment request ' . $request_code . ' for ' . $order->company_name . ' needs your approval.';
        $this->create_service_payment_notification($hod_id, 'Service Payment Approval Required', $message, $request_id);
    }

    $this->session->set_flashdata('success', 'Service payment request ' . $request_code . ' created successfully and sent for HOD approval.');
    redirect(page_url . 'ServiceLeads/service_payment_requests');
}

public function service_payment_requests()
{
    $this->ensure_service_payment_request_dependencies();
    $this->require_service_payment_request_access();

    $status_slug = strtolower(trim((string) $this->input->get('status')));
    $status_map = $this->get_service_payment_status_options();
    if ($status_slug === '' || !isset($status_map[$status_slug])) {
        $status_slug = 'all';
    }

    $all_requests = $this->service_payment_request_model->get_request_rows();
    $current_user_id = $this->get_logged_in_service_user_id();
    $payment_permissions = $this->get_service_payment_permission_snapshot();
    $can_approve = !empty($payment_permissions['can_approve']);

    if (!$can_approve) {
        $all_requests = array_values(array_filter($all_requests, function ($row) use ($current_user_id) {
            return in_array($current_user_id, [
                (int) $row->created_by,
                (int) $row->requested_for_user_id,
                (int) $row->engineer_id,
            ], true);
        }));
    }

    $requests = $all_requests;
    if ($status_slug !== 'all') {
        $expected_status = $status_map[$status_slug];
        $requests = array_values(array_filter($requests, function ($row) use ($expected_status) {
            return trim((string) $row->status) === $expected_status;
        }));
    }

    $data = [
        'page_title' => $can_approve ? 'Service Payment Requests' : 'My Service Payment Requests',
        'requests' => $requests,
        'kpi' => $this->build_service_payment_request_kpi($all_requests),
        'status_map' => $status_map,
        'active_status_slug' => $status_slug,
        'payment_permissions' => $payment_permissions,
        'is_approval_view' => false,
    ];

    $this->load->view('spares/service_payment_request_list_view', $data);
}

public function service_payment_approvals()
{
    $this->ensure_service_payment_request_dependencies();
    $this->require_service_payment_request_access(true);

    $status_slug = strtolower(trim((string) $this->input->get('status')));
    $status_map = $this->get_service_payment_status_options();
    if ($status_slug === '' || !isset($status_map[$status_slug])) {
        $status_slug = 'pending';
    }

    $all_requests = $this->service_payment_request_model->get_request_rows();
    $requests = $all_requests;

    if ($status_slug !== 'all') {
        $expected_status = $status_map[$status_slug];
        $requests = array_values(array_filter($requests, function ($row) use ($expected_status) {
            return trim((string) $row->status) === $expected_status;
        }));
    }

    $data = [
        'page_title' => 'Service Payment Approval Queue',
        'requests' => $requests,
        'kpi' => $this->build_service_payment_request_kpi($all_requests),
        'status_map' => $status_map,
        'active_status_slug' => $status_slug,
        'payment_permissions' => $this->get_service_payment_permission_snapshot(),
        'is_approval_view' => true,
    ];

    $this->load->view('spares/service_payment_request_list_view', $data);
}

public function print_service_payment_request_pdf($request_id = null)
{
    $this->ensure_service_payment_request_dependencies();
    $this->ensure_service_payment_request_read_access();

    $request_id = $request_id !== null ? (int) $request_id : (int) $this->uri->segment(3);
    $request = $this->service_payment_request_model->get_request_by_id($request_id);

    if (empty($request)) {
        show_404();
    }

    $payment_permissions = $this->get_service_payment_permission_snapshot();
    if (!$this->can_current_user_view_service_payment_request($request, $payment_permissions)) {
        show_error(
            'You do not have permission to print this service payment request.',
            403,
            'Access Denied'
        );
    }

    $pdf_data = [
        'request' => $request,
        'logs' => $this->service_payment_request_model->get_request_logs($request_id),
        'company_profile' => $this->get_service_company_profile(),
    ];

    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new \Dompdf\Dompdf($options);
    $html = $this->load->view('spares/service_payment_request_pdf_template', $pdf_data, true);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_length()) {
        ob_end_clean();
    }

    $safe_file_name = preg_replace('/[\/\\\\:*?"<>|]+/', '-', 'Service-Payment-Request-' . trim((string) $request->request_code));
    $safe_file_name = trim((string) $safe_file_name, '-');

    $dompdf->stream(($safe_file_name !== '' ? $safe_file_name : 'service-payment-request') . '.pdf', ['Attachment' => 0]);
}

public function update_service_payment_request_status()
{
    $this->ensure_service_payment_request_dependencies();
    $this->require_service_payment_request_access(true);
    $this->output->set_content_type('application/json');

    $request_id = (int) $this->input->post('request_id');
    $action = strtolower(trim((string) $this->input->post('action')));
    $remarks = trim((string) $this->input->post('remarks'));

    $request = $this->service_payment_request_model->get_request_by_id($request_id);
    if (empty($request)) {
        echo json_encode([
            'status' => false,
            'message' => 'Payment request not found.',
        ]);
        return;
    }

    if (trim((string) $request->status) !== 'Pending HOD Approval') {
        echo json_encode([
            'status' => false,
            'message' => 'This request has already been processed.',
        ]);
        return;
    }

    if (!in_array($action, ['approved', 'rejected'], true)) {
        echo json_encode([
            'status' => false,
            'message' => 'Invalid approval action.',
        ]);
        return;
    }

    if ($action === 'rejected' && $remarks === '') {
        echo json_encode([
            'status' => false,
            'message' => 'Rejection remarks are required.',
        ]);
        return;
    }

    $status_label = $action === 'approved' ? 'Approved' : 'Rejected';
    $actor_id = $this->get_logged_in_service_user_id();

    $this->db->trans_start();
    $this->service_payment_request_model->update_request($request_id, [
        'status' => $status_label,
        'hod_id' => $actor_id,
        'hod_remarks' => $remarks !== '' ? $remarks : null,
        'hod_action_on' => date('Y-m-d H:i:s'),
        'updated_by' => $actor_id,
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $this->service_payment_request_model->log_action(
        $request_id,
        $action === 'approved' ? 'HOD_APPROVED' : 'HOD_REJECTED',
        $remarks,
        $actor_id,
        [
            'amount' => (float) $request->amount,
            'request_code' => (string) $request->request_code,
        ],
        $status_label
    );
    $this->db->trans_complete();

    if (!$this->db->trans_status()) {
        echo json_encode([
            'status' => false,
            'message' => 'The request could not be updated due to a database issue.',
        ]);
        return;
    }

    $message = 'Service payment request ' . $request->request_code . ' has been ' . strtolower($status_label) . '.';
    if ($remarks !== '') {
        $message .= ' Remarks: ' . $remarks;
    }

    $notify_user_ids = array_unique(array_filter([
        (int) $request->created_by,
        (int) $request->requested_for_user_id,
        (int) $request->engineer_id,
    ]));

    foreach ($notify_user_ids as $notify_user_id) {
        $this->create_service_payment_notification(
            $notify_user_id,
            'Service Payment Request ' . $status_label,
            $message,
            $request_id
        );
    }

    echo json_encode([
        'status' => true,
        'message' => 'Request ' . strtolower($status_label) . ' successfully.',
    ]);
}

public function order_report() {
    $from_date = $this->sanitize_report_date($this->input->get('from_date'));
    $to_date = $this->sanitize_report_date($this->input->get('to_date'));
    $marketing_person = trim((string) $this->input->get('marketing_person'));

    $data['filters'] = [
        'from_date' => $from_date,
        'to_date' => $to_date,
        'marketing_person' => $marketing_person,
    ];

    $data['marketing_persons'] = $this->db->select('user_id, first_name, last_name')
        ->from('system_users')
        ->where('department_id', 22)
        ->where('user_status', 1)
        ->order_by('first_name', 'ASC')
        ->order_by('last_name', 'ASC')
        ->get()->result();

    // Pull won-order rows directly so filters stay aligned with dashboard data and current schema.
    $this->db->select("
        so.opportunity_id,
        so.op_no,
        so.op_date,
        so.op_type,
        so.marketing_person_id,
        IFNULL(cm_spares.company_name, cm_marketing.company_name) as customer_name,
        TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as marketing_person,
        spo.po_number,
        spo.po_date,
        spo.po_amount,
        spo.po_attachment,
        (
            SELECT sq.currency
            FROM service_quotations sq
            WHERE sq.opportunity_id = so.opportunity_id
            ORDER BY sq.id DESC
            LIMIT 1
        ) as currency,
        (
            SELECT sq.total_basic_amount
            FROM service_quotations sq
            WHERE sq.opportunity_id = so.opportunity_id
            ORDER BY sq.id DESC
            LIMIT 1
        ) as total_basic_amount,
        (
            SELECT sq.grand_total
            FROM service_quotations sq
            WHERE sq.opportunity_id = so.opportunity_id
            ORDER BY sq.id DESC
            LIMIT 1
        ) as quote_amount
    ", false);
    $this->db->from('service_opportunities so');
    $this->db->join('service_purchase_orders spo', 'spo.opportunity_id = so.opportunity_id');
    $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
    $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
    $this->db->join('system_users u', 'u.user_id = so.marketing_person_id', 'left');
    $this->db->where('so.current_stage_id', 7);

    if ($from_date !== '') {
        $this->db->where('spo.po_date >=', $from_date);
    }

    if ($to_date !== '') {
        $this->db->where('spo.po_date <=', $to_date);
    }

    if ($marketing_person !== '' && ctype_digit($marketing_person)) {
        $this->db->where('so.marketing_person_id', (int) $marketing_person);
    }
    
    $data['orders'] = $this->db
        ->order_by('spo.po_date', 'DESC')
        ->order_by('spo.id', 'DESC')
        ->get()
        ->result();

    // 2. Revenue KPIs by currency
    $data['kpi'] = [
        'total_orders' => count($data['orders']),
        'total_value_inr' => 0,
        'total_value_usd' => 0,
        'avg_order_value' => 0,
        'currency_totals' => [],
        'currency_counts' => [],
        'currency_averages' => [],
    ];

    $opportunity_map = [];
    $opportunity_ids = [];

    foreach ($data['orders'] as $row) {
        $currency = $this->normalize_report_currency($row->currency);
        $amount = (float) $row->po_amount;

        if (!isset($data['kpi']['currency_totals'][$currency])) {
            $data['kpi']['currency_totals'][$currency] = 0;
            $data['kpi']['currency_counts'][$currency] = 0;
        }

        $data['kpi']['currency_totals'][$currency] += $amount;
        $data['kpi']['currency_counts'][$currency]++;
        if (!isset($opportunity_map[(int) $row->opportunity_id])) {
            $opportunity_map[(int) $row->opportunity_id] = [];
        }

        $opportunity_map[(int) $row->opportunity_id][] = $row;
        $opportunity_ids[] = (int) $row->opportunity_id;
    }

    foreach ($data['kpi']['currency_totals'] as $currency => $total) {
        $count = $data['kpi']['currency_counts'][$currency] ?: 1;
        $data['kpi']['currency_averages'][$currency] = $total / $count;
    }

    $data['kpi']['total_value_inr'] = $data['kpi']['currency_totals']['INR'] ?? 0;
    $data['kpi']['total_value_usd'] = $data['kpi']['currency_totals']['USD'] ?? 0;
    $data['kpi']['avg_order_value'] = $data['kpi']['currency_averages']['INR'] ?? 0;

    // 3. Engineer revenue in order currency
    $data['engineer_revenue'] = [];
    if (!empty($opportunity_ids)) {
        $opportunity_ids = array_values(array_unique($opportunity_ids));
        $engineer_rows = $this->db->select("
                v.engineer_id,
                v.opportunity_id,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as engineer_name
            ")
            ->from('service_engineer_visits v')
            ->join('system_users u', 'u.user_id = v.engineer_id', 'left')
            ->where_in('v.opportunity_id', $opportunity_ids)
            ->group_by('v.engineer_id, v.opportunity_id')
            ->order_by('engineer_name', 'ASC')
            ->get()->result();

        $engineer_count_by_opportunity = [];
        foreach ($engineer_rows as $engineer_row) {
            $opportunity_id = (int) $engineer_row->opportunity_id;
            if (!isset($engineer_count_by_opportunity[$opportunity_id])) {
                $engineer_count_by_opportunity[$opportunity_id] = 0;
            }

            $engineer_count_by_opportunity[$opportunity_id]++;
        }

        $engineer_summary = [];
        foreach ($engineer_rows as $engineer_row) {
            $opportunity_id = (int) $engineer_row->opportunity_id;
            if (empty($opportunity_map[$opportunity_id])) {
                continue;
            }

            $engineer_id = (int) $engineer_row->engineer_id;
            $engineer_name = trim($engineer_row->engineer_name);
            if ($engineer_name === '') {
                $engineer_name = 'Unknown Engineer';
            }

            if (!isset($engineer_summary[$engineer_id])) {
                $engineer_summary[$engineer_id] = [
                    'engineer_id' => $engineer_id,
                    'engineer_name' => $engineer_name,
                    'order_count' => 0,
                    'revenue_by_currency' => [],
                    'seen_opportunities' => [],
                ];
            }

            if (!isset($engineer_summary[$engineer_id]['seen_opportunities'][$opportunity_id])) {
                $engineer_summary[$engineer_id]['seen_opportunities'][$opportunity_id] = true;
                $engineer_summary[$engineer_id]['order_count']++;
            }

            foreach ($opportunity_map[$opportunity_id] as $order) {
                $currency = $this->normalize_report_currency($order->currency);
                $amount = (float) $order->po_amount;
                $engineer_count = (int) ($engineer_count_by_opportunity[$opportunity_id] ?? 1);
                $distributed_amount = $engineer_count > 0 ? ($amount / $engineer_count) : $amount;

                if (!isset($engineer_summary[$engineer_id]['revenue_by_currency'][$currency])) {
                    $engineer_summary[$engineer_id]['revenue_by_currency'][$currency] = 0;
                }

                $engineer_summary[$engineer_id]['revenue_by_currency'][$currency] += $distributed_amount;
            }
        }

        $data['engineer_revenue'] = array_values($engineer_summary);
        foreach ($data['engineer_revenue'] as &$engineer_row) {
            unset($engineer_row['seen_opportunities']);

            if (empty($engineer_row['revenue_by_currency'])) {
                $engineer_row['revenue_by_currency'] = ['INR' => 0];
            } else {
                ksort($engineer_row['revenue_by_currency']);
            }
        }
        unset($engineer_row);

        usort($data['engineer_revenue'], function ($left, $right) {
            if ($left['order_count'] === $right['order_count']) {
                return strcmp($left['engineer_name'], $right['engineer_name']);
            }

            return $right['order_count'] <=> $left['order_count'];
        });
    }

    $this->load->view('spares/service_order_report_view', $data);
}

private function normalize_report_currency($currency) {
    $currency = strtoupper(trim((string) $currency));
    return $currency !== '' ? $currency : 'INR';
}

private function sanitize_report_date($date_value) {
    $date_value = trim((string) $date_value);
    if ($date_value === '') {
        return '';
    }

    $parsed_date = DateTime::createFromFormat('Y-m-d', $date_value);
    if ($parsed_date && $parsed_date->format('Y-m-d') === $date_value) {
        return $date_value;
    }

    return '';
}

public function engineer_scheduler() {
    $this->ensure_service_visit_dependencies();

    // 1. Fetch assignable team members from Service (department 22)
    // and Automation (department 14).
    $data['engineers'] = $this->get_service_scheduler_engineers();

    // 2. Fetch Won Orders for the "Plan New Visit" dropdown (Stage 7)
    $this->db->select("so.opportunity_id, so.op_no, IFNULL(cm_spares.company_name, cm_marketing.company_name) as company_name");
    $this->db->from('service_opportunities so');
    $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
    $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
    $this->db->where('so.current_stage_id', 7);
    $this->db->order_by('so.opportunity_id', 'DESC');
    $data['pending_orders'] = $this->db->get()->result();

    // 3. Fetch and decorate scheduler visits for history, insights, and KPIs.
    $data['all_visits'] = $this->get_scheduler_visit_rows();
    $data['scheduler_kpi'] = $this->build_scheduler_kpi($data['all_visits'], count($data['pending_orders']));
    $data['engineer_workload'] = $this->build_engineer_workload($data['all_visits']);
    $data['upcoming_deployments'] = $this->get_upcoming_scheduler_visits($data['all_visits']);
    $data['history_statuses'] = $this->get_scheduler_statuses($data['all_visits']);

    // 4. Load deployment types from master with a fallback to the current defaults
    $data['deployment_types'] = $this->deployment_type_model->get_active_for_scheduler();

    // 5. Load the view with populated data
    $this->load->view('spares/service_engineer_scheduler_view', $data);
}

/**
 * AJAX: Fetch visits for the Calendar
 */
public function get_scheduled_events() {
    $this->ensure_service_visit_dependencies();
    echo json_encode($this->build_scheduler_calendar_events($this->get_scheduler_visit_rows()));
}

public function get_assignment_overview_events()
{
    $this->ensure_service_visit_dependencies();
    $this->require_service_visit_overview_access();

    $status_slug = strtolower(trim((string) $this->input->get('status')));
    $status_map = $this->get_service_visit_status_map();
    $filters = [];

    if ($status_slug !== '' && isset($status_map[$status_slug])) {
        $filters['visit_status'] = $status_map[$status_slug];
    }

    $engineer_id = (int) $this->input->get('engineer_id');
    if ($engineer_id > 0) {
        $filters['engineer_id'] = $engineer_id;
    }

    echo json_encode($this->build_scheduler_calendar_events($this->get_scheduler_visit_rows($filters)));
}

public function save_visit_plan() {
    $post = $this->input->post();
    $engineer_ids = isset($post['engineer_ids']) ? (array) $post['engineer_ids'] : [];

    if (empty($engineer_ids) && !empty($post['engineer_id'])) {
        $engineer_ids = [$post['engineer_id']];
    }

    $engineer_ids = array_values(array_unique(array_filter(array_map('intval', $engineer_ids))));
    $opportunity_id = isset($post['opportunity_id']) ? (int) $post['opportunity_id'] : 0;
    $start_date = isset($post['start_date']) ? $post['start_date'] : '';
    $end_date = isset($post['end_date']) ? $post['end_date'] : '';
    $visit_type = isset($post['visit_type']) ? trim($post['visit_type']) : '';
    $remarks = isset($post['remarks']) ? trim($post['remarks']) : '';

    if ($opportunity_id <= 0 || empty($engineer_ids) || empty($start_date) || empty($end_date) || strtotime($end_date) < strtotime($start_date)) {
        $this->session->set_flashdata('schedule_feedback', [
            'type' => 'error',
            'title' => 'Unable to schedule deployment',
            'message' => 'Please choose a valid order, at least one engineer, and a correct date range.',
        ]);
        redirect(page_url . 'ServiceLeads/engineer_scheduler');
        return;
    }

    $engineer_name_map = [];
    $engineer_rows = $this->db->select("user_id, CONCAT(TRIM(COALESCE(first_name, '')), ' ', TRIM(COALESCE(last_name, ''))) as full_name")
        ->from('system_users')
        ->where_in('user_id', $engineer_ids)
        ->get()->result();

    foreach ($engineer_rows as $engineer_row) {
        $engineer_name_map[(int) $engineer_row->user_id] = trim($engineer_row->full_name);
    }

    $has_visit_status = $this->db->field_exists('visit_status', 'service_engineer_visits');
    $has_created_at = $this->db->field_exists('created_at', 'service_engineer_visits');
    $has_updated_at = $this->db->field_exists('updated_at', 'service_engineer_visits');
    $timestamp = date('Y-m-d H:i:s');
    $current_user_id = $this->get_logged_in_service_user_id();

    $insert_rows = [];
    $saved_names = [];
    $conflicts = [];

    foreach ($engineer_ids as $engineer_id) {
        $engineer_name = !empty($engineer_name_map[$engineer_id]) ? $engineer_name_map[$engineer_id] : 'Engineer #' . $engineer_id;
        $conflict = $this->find_scheduler_conflict($engineer_id, $start_date, $end_date);

        if (!empty($conflict)) {
            $conflicts[] = $engineer_name . ' is already booked for ' . $conflict->op_no . ' (' . $conflict->customer_name . ')';
            continue;
        }

        $row = [
            'opportunity_id' => $opportunity_id,
            'engineer_id' => $engineer_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'visit_type' => $visit_type,
            'remarks' => $remarks,
        ];

        if ($has_visit_status) {
            $row['visit_status'] = 'Scheduled';
        }

        if ($has_created_at) {
            $row['created_at'] = $timestamp;
        }

        if ($has_updated_at) {
            $row['updated_at'] = $timestamp;
        }

        $insert_rows[] = $row;
        $saved_names[] = $engineer_name;
    }

    if (empty($insert_rows)) {
        $this->session->set_flashdata('schedule_feedback', [
            'type' => 'warning',
            'title' => 'No engineers scheduled',
            'message' => 'All selected engineers already have overlapping deployments. Please choose another date range.',
            'details' => $conflicts,
        ]);
        redirect(page_url . 'ServiceLeads/engineer_scheduler');
        return;
    }

    $this->db->trans_begin();
    $inserted = true;

    foreach ($insert_rows as $insert_row) {
        $this->db->insert('service_engineer_visits', $insert_row);
        $visit_id = (int) $this->db->insert_id();

        if ($visit_id <= 0 || !$this->service_visit_execution_model->create_initial_schedule_version($visit_id, $current_user_id, 'Visit scheduled from scheduler', $timestamp)) {
            $inserted = false;
            break;
        }
    }

    if (!$inserted || !$this->db->trans_status()) {
        $this->db->trans_rollback();
        $this->session->set_flashdata('schedule_feedback', [
            'type' => 'error',
            'title' => 'Deployment save failed',
            'message' => 'The deployment plan could not be saved due to a database issue.',
        ]);
        redirect(page_url . 'ServiceLeads/engineer_scheduler');
        return;
    }

    $this->db->trans_commit();

    if (!empty($conflicts)) {
        $this->session->set_flashdata('schedule_feedback', [
            'type' => 'warning',
            'title' => 'Deployment partially scheduled',
            'message' => count($saved_names) . ' engineer(s) scheduled successfully. Some engineers were skipped because of date conflicts.',
            'details' => $conflicts,
        ]);
    } else {
        $this->session->set_flashdata('schedule_feedback', [
            'type' => 'success',
            'title' => 'Deployment scheduled',
            'message' => count($saved_names) . ' engineer(s) scheduled successfully for this order.',
        ]);
    }

    redirect(page_url . 'ServiceLeads/engineer_scheduler');
}

public function engineer_visit_list($status_slug = 'all')
{
    $this->ensure_service_visit_dependencies();

    $status_slug = strtolower(trim((string) $status_slug));
    if ($status_slug === '') {
        $status_slug = 'all';
    }

    $status_map = $this->get_service_visit_status_map();
    $filters = [];

    if ($status_slug !== 'all' && isset($status_map[$status_slug])) {
        $filters['visit_status'] = $status_map[$status_slug];
    }

    $data['visits'] = $this->service_visit_execution_model->get_visit_rows($filters);
    $data['visit_kpi'] = $this->build_service_visit_kpi($this->service_visit_execution_model->get_visit_rows());
    $data['status_map'] = $status_map;
    $data['active_status_slug'] = $status_slug;
    $data['active_status_label'] = $status_slug !== 'all' && isset($status_map[$status_slug])
        ? $status_map[$status_slug]
        : 'All Visits';
    $data['payment_permissions'] = $this->get_service_payment_permission_snapshot();

    $this->load->view('spares/service_engineer_visit_list_view', $data);
}

public function engineer_assignment_overview($status_slug = 'all')
{
    $this->ensure_service_visit_dependencies();
    $this->require_service_visit_overview_access();

    $status_map = $this->get_service_visit_status_map();
    $requested_status = strtolower(trim((string) $this->input->get('status')));
    if ($requested_status === '' || !isset($status_map[$requested_status])) {
        $requested_status = '';
    }

    $requested_engineer_id = (int) $this->input->get('engineer_id');
    $events_url = page_url . 'ServiceLeads/get_assignment_overview_events';
    $query_parts = [];
    if ($requested_status !== '') {
        $query_parts['status'] = $requested_status;
    }
    if ($requested_engineer_id > 0) {
        $query_parts['engineer_id'] = $requested_engineer_id;
    }
    if (!empty($query_parts)) {
        $events_url .= '?' . http_build_query($query_parts);
    }

    $data = [
        'engineers' => $this->get_service_scheduler_engineers(),
        'status_map' => $status_map,
        'active_status_slug' => $requested_status,
        'active_engineer_id' => $requested_engineer_id,
        'events_url' => $events_url,
    ];

    $this->load->view('spares/service_engineer_assignment_overview_view', $data);
}

public function engineer_visit_detail($visit_id = null)
{
    $this->ensure_service_visit_dependencies();

    $visit_id = $visit_id !== null ? (int) $visit_id : (int) $this->uri->segment(3);
    $visit = $this->service_visit_execution_model->get_visit_by_id($visit_id);

    if (!$visit) {
        show_404();
    }

    $data['visit'] = $visit;
    $data['updates'] = $this->service_visit_execution_model->get_visit_updates($visit_id);
    $data['documents'] = $this->service_visit_execution_model->get_visit_documents($visit_id);
    $data['version_history'] = $this->service_visit_execution_model->get_visit_versions($visit_id, $visit);
    $data['payment_permissions'] = $this->get_service_payment_permission_snapshot();

    $this->load->view('spares/service_engineer_visit_detail_view', $data);
}

public function save_visit_daily_update($visit_id = null)
{
    $this->ensure_service_visit_dependencies();

    $visit_id = $visit_id !== null ? (int) $visit_id : (int) $this->uri->segment(3);
    $visit = $this->service_visit_execution_model->get_visit_by_id($visit_id);

    if (!$visit) {
        show_404();
    }

    if (strtolower(trim((string) $visit->visit_status)) === 'completed') {
        $this->session->set_flashdata('error', 'This visit is already completed. Please open a new visit if more work is required.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $work_date = trim((string) $this->input->post('work_date'));
    $mom_points = trim((string) $this->input->post('mom_points'));
    $next_plan = trim((string) $this->input->post('next_plan'));

    if ($work_date === '' || strtotime($work_date) === false || $mom_points === '') {
        $this->session->set_flashdata('error', 'Please fill a valid work date and MOM update before saving.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $saved = $this->service_visit_execution_model->save_daily_update($visit_id, [
        'work_date' => $work_date,
        'mom_points' => $mom_points,
        'next_plan' => $next_plan,
        'user_id' => $this->get_logged_in_service_user_id(),
    ]);

    if ($saved) {
        $this->session->set_flashdata('success', 'Daily MOM update saved successfully.');
    } else {
        $this->session->set_flashdata('error', 'The MOM update could not be saved. Please try again.');
    }

    redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
}

public function extend_visit_schedule($visit_id = null)
{
    $this->ensure_service_visit_dependencies();

    $visit_id = $visit_id !== null ? (int) $visit_id : (int) $this->uri->segment(3);
    $visit = $this->service_visit_execution_model->get_visit_by_id($visit_id);

    if (!$visit) {
        show_404();
    }

    $current_status = strtolower(trim((string) $visit->visit_status));
    if (in_array($current_status, ['completed', 'cancelled'], true)) {
        $this->session->set_flashdata('error', 'Completed or cancelled visits cannot be extended.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $new_end_date = trim((string) $this->input->post('new_end_date'));
    $extension_reason = trim((string) $this->input->post('extension_reason'));

    if ($new_end_date === '' || strtotime($new_end_date) === false) {
        $this->session->set_flashdata('error', 'Please choose a valid revised end date for the extension.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    if ($extension_reason === '') {
        $this->session->set_flashdata('error', 'Please mention the reason for extending this visit.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    if ($new_end_date <= (string) $visit->end_date) {
        $this->session->set_flashdata('error', 'The revised end date must be after the current end date to extend the visit.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $conflict = $this->find_scheduler_conflict((int) $visit->engineer_id, (string) $visit->start_date, $new_end_date, $visit_id);
    if (!empty($conflict)) {
        $this->session->set_flashdata('error', 'This extension overlaps with another booking: ' . $conflict->op_no . ' (' . $conflict->customer_name . ').');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $extended = $this->service_visit_execution_model->extend_visit_schedule(
        $visit_id,
        $new_end_date,
        $extension_reason,
        $this->get_logged_in_service_user_id()
    );

    if (!$extended) {
        $this->session->set_flashdata('error', 'The engineer visit could not be extended due to a database issue.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $this->session->set_flashdata('success', 'Engineer visit extended successfully. A new schedule version has been captured.');
    redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
}

public function mark_visit_completed($visit_id = null)
{
    $this->ensure_service_visit_dependencies();

    $visit_id = $visit_id !== null ? (int) $visit_id : (int) $this->uri->segment(3);
    $visit = $this->service_visit_execution_model->get_visit_by_id($visit_id);

    if (!$visit) {
        show_404();
    }

    if (strtolower(trim((string) $visit->visit_status)) === 'completed') {
        $this->session->set_flashdata('success', 'This visit is already marked as completed.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    if ($this->service_visit_execution_model->get_visit_update_count($visit_id) <= 0) {
        $this->session->set_flashdata('error', 'Please add at least one daily MOM update before completing this visit.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $upload_result = $this->upload_service_visit_documents('signed_documents');
    if (!$upload_result['success']) {
        $this->session->set_flashdata('error', $upload_result['error']);
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $total_document_count = $this->service_visit_execution_model->get_visit_document_count($visit_id) + count($upload_result['files']);
    if ($total_document_count <= 0) {
        $this->session->set_flashdata('error', 'Please upload at least one signed completion document before marking this visit as completed.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $user_id = $this->get_logged_in_service_user_id();
    $completion_notes = trim((string) $this->input->post('completion_notes'));

    $this->db->trans_start();
    foreach ($upload_result['files'] as $file) {
        $this->service_visit_execution_model->save_completion_document($visit_id, $file, $user_id);
    }
    $this->service_visit_execution_model->mark_visit_completed($visit_id, $completion_notes);
    $this->db->trans_complete();

    if (!$this->db->trans_status()) {
        $this->cleanup_service_visit_uploads($upload_result['files']);
        $this->session->set_flashdata('error', 'The visit could not be completed due to a database issue.');
        redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
        return;
    }

    $this->session->set_flashdata('success', 'Visit marked as completed and signed document(s) uploaded successfully.');
    redirect(page_url . 'ServiceLeads/engineer_visit_detail/' . $visit_id);
}

public function view_visit_mom_pdf($visit_id = null)
{
    $this->ensure_service_visit_dependencies();

    $visit_id = $visit_id !== null ? (int) $visit_id : (int) $this->uri->segment(3);
    $visit = $this->service_visit_execution_model->get_visit_by_id($visit_id);

    if (!$visit) {
        show_404();
    }

    $pdf_data = [
        'visit' => $visit,
        'updates' => $this->service_visit_execution_model->get_visit_updates($visit_id),
        'documents' => $this->service_visit_execution_model->get_visit_documents($visit_id),
        'company_profile' => $this->get_service_company_profile(),
    ];

    require_once FCPATH . 'application/third_party/dompdf/autoload.inc.php';

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');

    $dompdf = new \Dompdf\Dompdf($options);
    $html = $this->load->view('spares/service_engineer_visit_pdf_template', $pdf_data, true);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_length()) {
        ob_end_clean();
    }

    $folder_path = FCPATH . 'uploads/service_visit_pdfs/';
    if (!is_dir($folder_path)) {
        mkdir($folder_path, 0777, true);
    }

    file_put_contents($folder_path . $visit_id . '.pdf', $dompdf->output());

    $safe_file_name = preg_replace('/[\/\\\\:*?"<>|]+/', '-', 'Visit-MOM-' . trim((string) $visit->op_no) . '-' . $visit_id);
    $safe_file_name = rtrim($safe_file_name, '-');

    $dompdf->stream($safe_file_name . '.pdf', ['Attachment' => 0]);
}

private function get_scheduler_visit_rows($filters = []) {
    $visit_status_select = $this->db->field_exists('visit_status', 'service_engineer_visits')
        ? "COALESCE(NULLIF(v.visit_status, ''), 'Scheduled')"
        : "'Scheduled'";

    $this->db->select("
        v.visit_id,
        v.opportunity_id,
        v.engineer_id,
        v.start_date,
        v.end_date,
        v.visit_type,
        v.remarks,
        v.completed_on,
        v.completion_notes,
        {$visit_status_select} as visit_status,
        so.op_no,
        so.op_date,
        TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) as engineer_full_name,
        IFNULL(cm_spares.company_name, cm_marketing.company_name) as customer_name,
        COALESCE(NULLIF(cm_spares.contact_person, ''), NULLIF(cm_marketing.customer_name, ''), '') as customer_contact_name,
        COALESCE(NULLIF(cm_spares.contact_person_no, ''), NULLIF(cm_marketing.contact_no, ''), '') as customer_contact_no,
        COALESCE(NULLIF(cm_spares.email, ''), NULLIF(cm_marketing.email, ''), '') as customer_email,
        COALESCE(NULLIF(cm_spares.address, ''), NULLIF(cm_marketing.address, ''), '') as customer_address,
        (
            SELECT MAX(du.work_date)
            FROM service_visit_daily_updates du
            WHERE du.visit_id = v.visit_id
        ) as latest_work_date,
        (
            SELECT COUNT(1)
            FROM service_visit_daily_updates du
            WHERE du.visit_id = v.visit_id
        ) as mom_count,
        (
            SELECT COUNT(1)
            FROM service_visit_completion_documents dd
            WHERE dd.visit_id = v.visit_id
        ) as document_count
    ");
    $this->db->from('service_engineer_visits v');
    $this->db->join('system_users u', 'u.user_id = v.engineer_id', 'inner');
    $this->db->join('service_opportunities so', 'so.opportunity_id = v.opportunity_id', 'inner');
    $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
    $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');

    if (!empty($filters['visit_status'])) {
        $this->db->where($visit_status_select . ' = ' . $this->db->escape($filters['visit_status']), null, false);
    }

    if (!empty($filters['engineer_id'])) {
        $this->db->where('v.engineer_id', (int) $filters['engineer_id']);
    }

    $this->db->order_by('v.start_date', 'DESC');
    $this->db->order_by('v.visit_id', 'DESC');
    $visits = $this->db->get()->result();

    $today = date('Y-m-d');
    foreach ($visits as $visit) {
        $visit->engineer_full_name = trim($visit->engineer_full_name);
        if ($visit->engineer_full_name === '') {
            $visit->engineer_full_name = 'Unknown Engineer';
        }

        if (empty($visit->customer_name)) {
            $visit->customer_name = 'Customer not available';
        }

        $duration_days = 1;
        if (!empty($visit->start_date) && !empty($visit->end_date)) {
            $duration_days = (int) floor((strtotime($visit->end_date) - strtotime($visit->start_date)) / 86400) + 1;
        }

        $visit->duration_days = max(1, $duration_days);
        $visit->duration_label = ($visit->duration_days === 1) ? '1 day' : $visit->duration_days . ' days';
        $visit->status_slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $visit->visit_status), '-'));
        $visit->type_slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $visit->visit_type), '-'));
        $visit->is_active_window = $this->is_scheduler_visit_active($visit, $today);
        $visit->is_today = (!empty($visit->start_date) && !empty($visit->end_date) && $today >= $visit->start_date && $today <= $visit->end_date);
    }

    return $visits;
}

private function build_scheduler_calendar_events($visits)
{
    $events = [];

    foreach ($visits as $row) {
        $is_long_deployment = ((int) $row->duration_days > 1);
        $events[] = [
            'id' => $row->visit_id,
            'title' => $row->engineer_full_name . " • " . $row->op_no,
            'start' => $row->start_date,
            'end' => date('Y-m-d', strtotime($row->end_date . ' +1 day')),
            'allDay' => true,
            'display' => $is_long_deployment ? 'list-item' : 'auto',
            'backgroundColor' => $this->get_scheduler_event_color($row->visit_status),
            'borderColor' => $this->get_scheduler_event_color($row->visit_status),
            'textColor' => '#17324d',
            'classNames' => [
                'scheduler-event',
                'status-' . $row->status_slug,
                $is_long_deployment ? 'event-multi-day' : 'event-single-day',
            ],
            'extendedProps' => [
                'engineer_name' => $row->engineer_full_name,
                'customer_name' => $row->customer_name,
                'op_no' => $row->op_no,
                'visit_type' => $row->visit_type,
                'visit_status' => $row->visit_status,
                'status_slug' => $row->status_slug,
                'start_date' => $row->start_date,
                'end_date' => $row->end_date,
                'duration_label' => $row->duration_label,
                'visit_id' => (int) $row->visit_id,
                'op_date' => $row->op_date,
                'customer_contact_name' => $row->customer_contact_name,
                'customer_contact_no' => $row->customer_contact_no,
                'customer_email' => $row->customer_email,
                'customer_address' => $row->customer_address,
                'completed_on' => $row->completed_on,
                'completion_notes' => $row->completion_notes,
                'latest_work_date' => $row->latest_work_date,
                'mom_count' => (int) $row->mom_count,
                'document_count' => (int) $row->document_count,
                'remarks' => $row->remarks,
                'detail_url' => page_url . 'ServiceLeads/engineer_visit_detail/' . (int) $row->visit_id,
                'pdf_url' => page_url . 'ServiceLeads/view_visit_mom_pdf/' . (int) $row->visit_id,
                'is_long_deployment' => $is_long_deployment,
            ],
        ];
    }

    return $events;
}

private function build_scheduler_kpi($visits, $pending_orders_count = 0) {
    $today = date('Y-m-d');
    $busy_engineers = [];
    $multi_engineer_buckets = [];
    $active_deployments = 0;
    $long_deployments = 0;

    foreach ($visits as $visit) {
        if ((int) $visit->duration_days > 1) {
            $long_deployments++;
        }

        if ($this->is_scheduler_visit_active($visit, $today)) {
            $active_deployments++;
        }

        if ($visit->is_today && $this->is_scheduler_visit_active($visit, $today)) {
            $busy_engineers[(int) $visit->engineer_id] = true;
        }

        $bucket_key = implode('|', [
            $visit->opportunity_id,
            $visit->start_date,
            $visit->end_date,
            strtolower(trim($visit->visit_type)),
        ]);
        if (!isset($multi_engineer_buckets[$bucket_key])) {
            $multi_engineer_buckets[$bucket_key] = [];
        }
        $multi_engineer_buckets[$bucket_key][(int) $visit->engineer_id] = true;
    }

    $multi_engineer_orders = 0;
    foreach ($multi_engineer_buckets as $bucket) {
        if (count($bucket) > 1) {
            $multi_engineer_orders++;
        }
    }

    return [
        'active_deployments' => $active_deployments,
        'engineers_busy_today' => count($busy_engineers),
        'multi_engineer_orders' => $multi_engineer_orders,
        'long_deployments' => $long_deployments,
        'won_orders_ready' => (int) $pending_orders_count,
    ];
}

private function build_engineer_workload($visits) {
    $today = date('Y-m-d');
    $workload = [];

    foreach ($visits as $visit) {
        if (!$this->is_scheduler_visit_active($visit, $today)) {
            continue;
        }

        $engineer_id = (int) $visit->engineer_id;
        if (!isset($workload[$engineer_id])) {
            $workload[$engineer_id] = [
                'engineer_id' => $engineer_id,
                'engineer_name' => $visit->engineer_full_name,
                'active_assignments' => 0,
                'booked_days' => 0,
                'today_assignments' => 0,
            ];
        }

        $workload[$engineer_id]['active_assignments']++;
        $workload[$engineer_id]['booked_days'] += (int) $visit->duration_days;

        if ($visit->is_today) {
            $workload[$engineer_id]['today_assignments']++;
        }
    }

    if (empty($workload)) {
        return [];
    }

    usort($workload, function ($left, $right) {
        if ($left['active_assignments'] === $right['active_assignments']) {
            return $right['booked_days'] <=> $left['booked_days'];
        }

        return $right['active_assignments'] <=> $left['active_assignments'];
    });

    $workload = array_slice($workload, 0, 6);
    $max_booked_days = 1;
    foreach ($workload as $row) {
        if ($row['booked_days'] > $max_booked_days) {
            $max_booked_days = $row['booked_days'];
        }
    }

    foreach ($workload as &$row) {
        $row['load_percent'] = max(12, (int) round(($row['booked_days'] / $max_booked_days) * 100));
    }
    unset($row);

    return $workload;
}

private function get_upcoming_scheduler_visits($visits, $limit = 6) {
    $today = date('Y-m-d');
    $upcoming = [];

    foreach ($visits as $visit) {
        if (!$this->is_scheduler_visit_active($visit, $today)) {
            continue;
        }

        if (!empty($visit->end_date) && $visit->end_date >= $today) {
            $upcoming[] = $visit;
        }
    }

    usort($upcoming, function ($left, $right) {
        return strcmp($left->start_date, $right->start_date);
    });

    return array_slice($upcoming, 0, $limit);
}

private function get_scheduler_statuses($visits) {
    $statuses = [];

    foreach ($visits as $visit) {
        $status = trim((string) $visit->visit_status);
        if ($status !== '') {
            $statuses[$status] = true;
        }
    }

    $statuses = array_keys($statuses);
    sort($statuses, SORT_NATURAL | SORT_FLAG_CASE);

    return $statuses;
}

private function find_scheduler_conflict($engineer_id, $start_date, $end_date, $exclude_visit_id = 0) {
    $this->db->select("
        v.start_date,
        v.end_date,
        so.op_no,
        IFNULL(cm_spares.company_name, cm_marketing.company_name) as customer_name
    ");
    $this->db->from('service_engineer_visits v');
    $this->db->join('service_opportunities so', 'so.opportunity_id = v.opportunity_id', 'inner');
    $this->db->join('spares_customers cm_spares', 'cm_spares.customer_id = so.customer_id', 'left');
    $this->db->join('customer_detail cm_marketing', 'cm_marketing.id = so.customer_id', 'left');
    $this->db->where('v.engineer_id', (int) $engineer_id);
    $this->db->where('v.start_date <=', $end_date);
    $this->db->where('v.end_date >=', $start_date);
    if ((int) $exclude_visit_id > 0) {
        $this->db->where('v.visit_id !=', (int) $exclude_visit_id);
    }

    if ($this->db->field_exists('visit_status', 'service_engineer_visits')) {
        $this->db->group_start();
        $this->db->where('v.visit_status !=', 'Completed');
        $this->db->where('v.visit_status !=', 'Cancelled');
        $this->db->or_where('v.visit_status IS NULL', null, false);
        $this->db->or_where('v.visit_status', '');
        $this->db->group_end();
    }

    return $this->db->order_by('v.start_date', 'ASC')->get()->row();
}

private function is_scheduler_visit_active($visit, $today = null) {
    $today = $today ?: date('Y-m-d');
    $status = strtolower(trim((string) $visit->visit_status));

    if (in_array($status, ['completed', 'cancelled'], true)) {
        return false;
    }

    return !empty($visit->end_date) && $visit->end_date >= $today;
}

private function get_scheduler_event_color($status) {
    $status = strtolower(trim((string) $status));

    switch ($status) {
        case 'completed':
            return '#34a853';
        case 'on-site':
            return '#f59e0b';
        case 'cancelled':
            return '#ef4444';
        default:
            return '#2563eb';
    }
}



}
