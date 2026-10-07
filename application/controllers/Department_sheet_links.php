<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_sheet_links extends CI_Controller
{
    private $viewer = array();
    private $schema_ready = false;

    public function __construct()
    {
        parent::__construct();
        $session = $this->session->userdata('logged_in');
        if (!is_array($session) || empty($session['user_id'])) {
            redirect(page_url);
            exit;
        }

        $this->load->model('Department_sheet_link_model', 'sheet_links');
        $this->viewer = $this->sheet_links->user((int) $session['user_id']);
        if (!$this->viewer || empty($this->viewer['business_location'])) {
            show_error('An active employee account with a business location is required.', 403);
            exit;
        }

        $this->schema_ready = $this->sheet_links->ensure_schema();
        if (!$this->session->userdata('department_sheet_links_csrf')) {
            $this->session->set_userdata('department_sheet_links_csrf', bin2hex(random_bytes(32)));
        }
    }

    public function index()
    {
        $department_id = max(0, (int) $this->value('department_id', 'get'));
        $search = substr($this->value('q', 'get'), 0, 100);
        $departments = $this->sheet_links->departments($this->viewer['business_location']);

        if ($department_id > 0 && !$this->sheet_links->department($department_id, $this->viewer['business_location'])) {
            $department_id = 0;
        }

        $rows = array();
        if ($this->schema_ready) {
            $rows = $this->sheet_links->links($this->viewer['business_location'], $department_id, $search);
            foreach ($rows as &$row) {
                $row['can_manage'] = $this->can_manage($row);
            }
            unset($row);
        }

        $this->load->view('department_sheet_links/index', array(
            'viewer' => $this->viewer,
            'departments' => $departments,
            'rows' => $rows,
            'department_id' => $department_id,
            'search' => $search,
            'schema_ready' => $this->schema_ready,
            'csrf' => $this->session->userdata('department_sheet_links_csrf'),
        ));
    }

    public function save()
    {
        $this->post_guard();
        $this->ready_guard();

        $id = max(0, (int) $this->value('id'));
        $existing = $id > 0 ? $this->sheet_links->find($id, $this->viewer['business_location']) : array();
        if ($id > 0 && !$existing) {
            show_error('The shared sheet link was not found.', 404);
            return;
        }
        if ($existing && !$this->can_manage($existing)) {
            show_error('You do not have permission to edit this department link.', 403);
            return;
        }

        $department_id = (int) $this->viewer['department_id'];
        if ((int) $this->viewer['is_admin'] === 1) {
            $department_id = max(0, (int) $this->value('department_id'));
        }

        $department = $this->sheet_links->department($department_id, $this->viewer['business_location']);
        if (!$department) {
            $this->failure('Select a valid active department.');
            return;
        }
        if ((int) $this->viewer['is_admin'] !== 1 && (int) $department_id !== (int) $this->viewer['department_id']) {
            show_error('You can only share links for your own department.', 403);
            return;
        }

        $title = trim(strip_tags($this->value('title')));
        $description = trim(strip_tags($this->value('description')));
        $sheet_url = trim($this->value('sheet_url'));
        if ($title === '' || $this->length($title) > 150) {
            $this->failure('Enter a link title of 150 characters or fewer.');
            return;
        }
        if ($this->length($description) > 500) {
            $this->failure('Keep the description within 500 characters.');
            return;
        }
        if (!Department_sheet_link_model::valid_google_sheet_url($sheet_url)) {
            $this->failure('Enter a valid HTTPS Google Sheets link from docs.google.com/spreadsheets.');
            return;
        }
        if ($this->sheet_links->duplicate_exists($department_id, $sheet_url, $id)) {
            $this->failure('This Google Sheet is already shared by the selected department.');
            return;
        }

        $saved = $this->sheet_links->save($id, array(
            'department_id' => $department_id,
            'title' => $title,
            'sheet_url' => $sheet_url,
            'description' => $description,
        ), $this->viewer['user_id']);

        if (!$saved) {
            log_message('error', 'Department sheet link save failed for user ' . (int) $this->viewer['user_id']);
            $this->failure('The link could not be saved. Please try again.');
            return;
        }

        $this->session->set_flashdata('department_sheet_links_message', $id > 0 ? 'Google Sheet link updated.' : 'Google Sheet link shared.');
        redirect(page_url . 'department-sheets');
    }

    public function delete($id = 0)
    {
        $this->post_guard();
        $this->ready_guard();

        $row = $this->sheet_links->find((int) $id, $this->viewer['business_location']);
        if (!$row) {
            show_error('The shared sheet link was not found.', 404);
            return;
        }
        if (!$this->can_manage($row)) {
            show_error('You do not have permission to remove this department link.', 403);
            return;
        }

        if (!$this->sheet_links->archive($row['id'], $this->viewer['user_id'])) {
            $this->failure('The link could not be removed. Please try again.');
            return;
        }
        $this->session->set_flashdata('department_sheet_links_message', 'Google Sheet link removed.');
        redirect(page_url . 'department-sheets');
    }

    private function can_manage($row)
    {
        if ((int) $this->viewer['is_admin'] === 1) {
            return true;
        }
        if ((int) $row['department_id'] !== (int) $this->viewer['department_id']) {
            return false;
        }
        if ((int) $row['created_by'] === (int) $this->viewer['user_id']) {
            return true;
        }
        if (array_key_exists('departmenthead', $row)) {
            return (int) $row['departmenthead'] === (int) $this->viewer['user_id'];
        }
        $department = $this->sheet_links->department($row['department_id'], $this->viewer['business_location']);
        return $department && (int) $department['departmenthead'] === (int) $this->viewer['user_id'];
    }

    private function ready_guard()
    {
        if (!$this->schema_ready) {
            show_error('Department Sheets is not set up. Run Database/department_sheet_links_001.sql and try again.', 503);
            exit;
        }
    }

    private function post_guard()
    {
        $token = $this->input->post('department_sheet_links_csrf');
        $stored = $this->session->userdata('department_sheet_links_csrf');
        if (strtoupper($this->input->method()) !== 'POST' || !is_string($token) || !is_string($stored) || !hash_equals($stored, $token)) {
            show_error('Invalid or expired form. Reload the page and try again.', 403);
            exit;
        }
    }

    private function failure($message)
    {
        $this->session->set_flashdata('department_sheet_links_error', $message);
        redirect(page_url . 'department-sheets');
    }

    private function value($key, $method = 'post')
    {
        $value = $this->input->$method($key);
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function length($value)
    {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    }
}
