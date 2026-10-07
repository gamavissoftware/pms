<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Overtime extends CI_Controller
{
    private $viewer;
    private $permissions;
    private $statuses = array('PENDING_LEADER', 'PENDING_ADMIN', 'APPROVED', 'REJECTED', 'CANCELLED');

    public function __construct()
    {
        parent::__construct();
        $session = $this->session->userdata('logged_in');
        if (!is_array($session) || empty($session['user_id'])) { redirect(page_url); exit; }
        $this->load->model('Overtime_model', 'overtime');
        $this->load->helper('overtime');
        $this->viewer = $this->overtime->user((int) $session['user_id']);
        if (!$this->viewer || empty($this->viewer['business_location'])) { show_error('An active employee account with a business location is required.', 403); exit; }
        $this->permissions = ot_user_permissions($this->db, $this->viewer['user_id']);
        $this->permissions['create'] = $this->permissions['create'] && $this->overtime->can_request_for_team($this->viewer);
        $method = strtolower((string)$this->router->fetch_method());
        $required = array('index'=>'requests','create'=>'create','save'=>'create','approvals'=>'approvals','reports'=>'reports','export'=>'reports','reassign'=>'leaders_edit');
        if (isset($required[$method])) $this->permission_guard($required[$method]);
        if ($method === 'settings' && !$this->permissions['policy'] && !$this->permissions['leaders'] && !$this->permissions['costs']) $this->permission_guard('policy');
        if (in_array($method,array('view','decide','read_notification'),true) && !$this->permissions['requests'] && !$this->permissions['approvals'] && !$this->permissions['reports'] && !$this->permissions['leaders']) $this->permission_guard('requests');
        // This module has its own CSRF protection because the application's global protection is disabled.
        if (!$this->session->userdata('overtime_csrf')) $this->session->set_userdata('overtime_csrf', bin2hex(random_bytes(32)));
    }

    private function permission_guard($key)
    {
        if (empty($this->permissions[$key])) { show_error('Overtime access is not enabled for this action. Ask your administrator to grant the matching module/submodule permission.', 403); exit; }
    }

    private function ready()
    {
        if ($this->overtime->module_ready()) return true;
        $this->render('setup', array('title' => 'Overtime setup'));
        return false;
    }

    private function render($view, $data = array())
    {
        $data['viewer'] = $this->viewer;
        $data['permissions'] = $this->permissions;
        $data['csrf'] = $this->session->userdata('overtime_csrf');
        $data['statuses'] = $this->statuses;
        $this->load->view('overtime/_header', $data);
        $this->load->view('overtime/' . $view, $data);
        $this->load->view('overtime/_footer', $data);
    }

    private function post_guard()
    {
        $token = $this->input->post('overtime_csrf');
        $stored = $this->session->userdata('overtime_csrf');
        if (strtoupper($this->input->method()) !== 'POST' || !is_string($token) || !is_string($stored) || !hash_equals($stored, $token)) {
            show_error('Invalid or expired form. Reload the page and try again.', 403); exit;
        }
    }

    private function admin_guard()
    {
        if ((int) $this->viewer['is_admin'] !== 1) { show_error('Administrator access required.', 403); exit; }
    }

    private function value($key, $method = 'post')
    {
        $value = $this->input->$method($key);
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function input_values($keys)
    {
        $values = array();
        foreach ($keys as $key) $values[$key] = $this->value($key);
        return $values;
    }

    private function filters($reports = false)
    {
        $from = $this->value('from', 'get'); $to = $this->value('to', 'get');
        if ($reports && $from === '' && $to === '') { $from = date('Y-m-01'); $to = date('Y-m-t'); }
        foreach (array($from, $to) as $date) {
            if ($date === '') continue;
            $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
            if (!$parsed || $parsed->format('Y-m-d') !== $date) throw new InvalidArgumentException('Enter valid report dates.');
        }
        if ($from !== '' && $to !== '' && $from > $to) throw new InvalidArgumentException('From date must be on or before To date.');
        $status = $this->value('status', 'get');
        if ($status !== '' && !in_array($status, $this->statuses, true)) throw new InvalidArgumentException('Select a valid status.');
        return array('from' => $from, 'to' => $to, 'status' => $status,
            'df_id'=>max(0, (int)$this->value('df_id','get')), 'person'=>substr($this->value('person','get'),0,200),
            'employee_id' => max(0, (int) $this->value('employee_id', 'get')), 'department_id' => max(0, (int) $this->value('department_id', 'get')));
    }

    public function index()
    {
        if (!$this->ready()) return;
        $this->list_page(false);
    }

    public function approvals()
    {
        if (!$this->ready()) return;
        $this->list_page(true);
    }

    private function list_page($inbox)
    {
        try { $filters = $this->filters(); }
        catch (InvalidArgumentException $e) { show_error($e->getMessage(), 400); return; }
        if ($inbox) $filters['inbox'] = true;
        elseif ($this->value('scope', 'get') !== 'visible') $filters['mine'] = true;
        $page = max(1, min(100000, (int) $this->value('page', 'get')));
        $summary = $this->overtime->summary($this->viewer, $filters);
        $this->render('index', array('title' => $inbox ? 'Approval inbox' : 'Overtime requests', 'inbox' => $inbox, 'filters' => $filters,
            'summary' => $summary, 'rows' => $this->overtime->listing($this->viewer, $filters, 50, ($page - 1) * 50),
            'page' => $page, 'options' => $this->overtime->filter_options($this->viewer), 'notifications' => $this->overtime->notifications($this->viewer)));
    }

    public function create()
    {
        if (!$this->ready()) return;
        $this->create_page(array(), '', bin2hex(random_bytes(32)));
    }

    private function create_page($values, $error, $key)
    {
        $this->render('create', array('title' => 'Request overtime', 'values' => $values, 'error' => $error, 'submission_key' => $key,
            'users' => $this->overtime->users($this->viewer['business_location']), 'dfs'=>$this->overtime->df_options(),
            'policy' => $this->overtime->policy($this->viewer['business_location'])));
    }

    public function save()
    {
        $this->post_guard();
        if (!$this->ready()) return;
        $values = $this->input_values(array('df_id', 'start_at', 'hours', 'manual_people', 'reason', 'work_reference'));
        $values['user_ids'] = $this->input->post('user_ids');
        $key = $this->value('submission_key');
        try {
            $id = $this->overtime->submit((int) $this->viewer['user_id'], $values, $key);
            $this->announce('REQUESTED', $id);
            $this->session->set_flashdata('overtime_message', 'Request sent to Shubham Sharma for approval, by PMS notification, chat and email. Work will be assigned after approval.');
            redirect(page_url . 'Overtime/view/' . $id);
        } catch (InvalidArgumentException $e) {
            $this->create_page($values, $e->getMessage(), preg_match('/^[a-f0-9]{64}$/D', $key) ? $key : bin2hex(random_bytes(32)));
        } catch (Throwable $e) {
            log_message('error', 'Overtime submission failed: ' . $e->getMessage());
            $this->create_page($values, 'Unable to save right now. Please try again; your submission reference prevents duplicate requests.', $key);
        }
    }

    public function view($id = 0)
    {
        if (!$this->ready()) return;
        $request = $this->overtime->get_request((int) $id);
        if (!$request || !Overtime_model::can_view($request, $this->viewer)) { show_404(); return; }
        $this->render('view', array('title' => $request['request_code'], 'request' => $request,
            'history' => $this->overtime->history((int) $id), 'can_decide' => $this->permissions['decide'] && Overtime_model::can_decide($request, $this->viewer),
            'reassign_users' => array(),
            'can_cancel' => $this->permissions['create'] && (int) $request['employee_id'] === (int) $this->viewer['user_id'] && in_array($request['status'], array('PENDING_LEADER','PENDING_ADMIN','APPROVED'), true)
                && ($request['status'] !== 'APPROVED' || $request['start_at'] > date('Y-m-d H:i:s'))));
    }

    public function decide($id = 0)
    {
        $this->post_guard();
        if (!$this->ready()) return;
        $request = $this->overtime->get_request((int) $id);
        if (!$request || !Overtime_model::can_view($request, $this->viewer)) { show_404(); return; }
        try {
            $this->permission_guard($this->value('decision') === 'CANCEL' ? 'create' : 'decide');
            $status = $this->overtime->decide((int) $id, (int) $this->viewer['user_id'], $this->value('decision'), $this->value('note'));
            if (in_array($status, array('APPROVED', 'REJECTED'), true)) $this->announce($status, $id, $this->value('note'));
            $this->session->set_flashdata('overtime_message', 'Request updated: ' . str_replace('_', ' ', $status) . '.');
        } catch (InvalidArgumentException $e) {
            $this->session->set_flashdata('overtime_error', $e->getMessage());
        } catch (Throwable $e) {
            log_message('error', 'Overtime decision failed: ' . $e->getMessage());
            $this->session->set_flashdata('overtime_error', 'Unable to save the decision. Reload the request and try again.');
        }
        redirect(page_url . 'Overtime/view/' . (int) $id);
    }

    /**
     * Tell the outside world what just happened: an email and a chat message to the one
     * person who now has to act, or who has been waiting to hear.
     *
     * Both channels are a courtesy on top of the in-module notification, never a
     * precondition. This runs only after the transaction has committed, each channel is
     * attempted independently, and every failure is swallowed and logged - a dead SMTP
     * host or an uninstalled chat module must not lose an approved request.
     */
    private function announce($kind, $id, $note = '')
    {
        $context = null;
        try {
            $context = $this->overtime->email_context((int) $id);
        } catch (Throwable $e) {
            log_message('error', 'Overtime notification context failed: ' . $e->getMessage());
        }
        if (!$context) return;
        foreach (array('Overtime_mailer' => 'overtime_mailer', 'Overtime_chat' => 'overtime_chat') as $class => $alias) {
            try {
                $this->load->library($class, null, $alias);
                if ($kind === 'REQUESTED') $this->$alias->request_raised($context);
                else $this->$alias->decision($context, $kind, $note);
            } catch (Throwable $e) {
                log_message('error', 'Overtime ' . $alias . ' dispatch failed: ' . $e->getMessage());
            }
        }
    }

    public function reports()
    {
        if (!$this->ready()) return;
        try { $filters = $this->filters(true); }
        catch (InvalidArgumentException $e) { show_error($e->getMessage(), 400); return; }
        $group = $this->value('group', 'get');
        if (!in_array($group, array('employee', 'department', 'df', 'day'), true)) $group = 'employee';
        $page = max(1, min(100000, (int) $this->value('page', 'get')));
        $this->render('reports', array('title' => 'Overtime reports', 'filters' => $filters, 'group' => $group, 'page' => $page,
            'summary' => $this->overtime->summary($this->viewer, $filters), 'groups' => $this->overtime->grouped_report($this->viewer, $filters, $group),
            'rows' => $this->overtime->listing($this->viewer, $filters, 50, ($page - 1) * 50), 'options' => $this->overtime->filter_options($this->viewer)));
    }

    public function reassign($id = 0)
    {
        show_error('Overtime approvals are assigned to Shubham Sharma (user 139). Reassignment is no longer used.', 410);
    }

    public static function csv_cell($value)
    {
        $value = (string) $value;
        // Guard spreadsheet formula injection, including formulas after whitespace/control characters.
        return preg_match('/^[\x00-\x20]*[=+@-]/', $value) ? "'" . $value : $value;
    }

    public function export()
    {
        if (!$this->ready()) return;
        try { $filters = $this->filters(true); }
        catch (InvalidArgumentException $e) { show_error($e->getMessage(), 400); return; }
        $rows = $this->overtime->listing($this->viewer, $filters, 10001);
        if (count($rows) > 10000) { show_error('Export is limited to 10,000 rows. Narrow the date range or filters.', 400); return; }
        $this->output->set_content_type('text/csv', 'utf-8');
        $this->output->set_header('Content-Disposition: attachment; filename="overtime-' . date('Y-m-d') . '.csv"');
        $this->output->set_header('Cache-Control: no-store');
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, array('Request','DF No.','Person user ID','Person','Person type','Department','Overtime date','Start (local time)','End (local time)','Break minutes','Requested minutes','Requested hours','Approved hours','Hourly cost rate','Overtime cost','Approved cost','Status','Requested by ID','Requested by','Approver ID','Decision time','Assigned at','Reason','Work reference','Submitted at'), ',', '"', '');
        foreach ($rows as $r) {
            $row = array($r['request_code'], $r['df_no'], $r['worker_id'], $r['person_name'], $r['worker_id'] ? 'PMS user' : 'Manual worker', $r['department'], substr($r['start_at'], 0, 10), $r['start_at'], $r['end_at'], $r['break_minutes'], $r['requested_minutes'], number_format($r['requested_minutes']/60, 2, '.', ''),
                $r['status'] === 'APPROVED' ? number_format($r['requested_minutes']/60, 2, '.', '') : '0.00',
                number_format((float) $r['hourly_rate'], 2, '.', ''), number_format((float) $r['cost_amount'], 2, '.', ''),
                $r['status'] === 'APPROVED' ? number_format((float) $r['cost_amount'], 2, '.', '') : '0.00',
                $r['status'], $r['employee_id'], $r['first_name'].' '.$r['last_name'], $r['admin_decided_by'], $r['admin_decided_at'], $r['assigned_at'], $r['reason'], $r['work_reference'], $r['created_at']);
            fputcsv($stream, array_map(array(__CLASS__, 'csv_cell'), $row), ',', '"', '');
        }
        rewind($stream); $this->output->set_output(stream_get_contents($stream)); fclose($stream);
    }

    public function settings()
    {
        $this->admin_guard();
        if (!$this->ready()) return;
        $section = $this->value('section','get');
        if ($section === 'policy') $this->permission_guard('policy');
        if ($section === 'leaders') $this->permission_guard('leaders');
        if ($section === 'cost') $this->permission_guard('costs');
        $this->render('settings', array_merge(array('section'=>$section, 'title' => 'Overtime settings', 'policy' => $this->overtime->policy($this->viewer['business_location']),
            'users' => $this->overtime->users($this->viewer['business_location'])), $this->overtime->settings_data($this->viewer['business_location'])));
    }

    public function save_settings()
    {
        $this->post_guard(); $this->admin_guard();
        if (!$this->ready()) return;
        try {
            $action = $this->value('action');
            $guards = array('policy'=>'policy_edit', 'leader'=>'leaders_edit', 'cost'=>'costs_edit', 'cost_delete'=>'costs_edit', 'cost_recalculate'=>'costs_edit');
            if (!isset($guards[$action])) throw new InvalidArgumentException('Invalid settings action.');
            $this->permission_guard($guards[$action]);
            $message = 'Settings saved. Changes apply to new requests; existing approval routes are preserved.';
            if ($action === 'policy') $this->overtime->save_policy($this->viewer['user_id'], $this->input_values(array('max_request_minutes','max_daily_minutes','past_days','future_days')));
            elseif ($action === 'leader') $this->overtime->save_override($this->viewer['user_id'], (int) $this->value('employee_id'), (int) $this->value('leader_id'), $this->value('reason'));
            elseif ($action === 'cost') {
                // One select carries both halves of the scope, so the form cannot post a
                // department rate with an employee id.
                $target = explode(':', $this->value('scope_target'), 2);
                $this->overtime->save_cost_rate($this->viewer['user_id'], array_merge($this->input_values(array('hourly_rate','note')),
                    array('scope' => $target[0], 'scope_id' => isset($target[1]) ? $target[1] : 0)));
                $message = 'Cost rate saved. It applies to overtime raised or approved from now on; use Recalculate to restate earlier rows.';
            } elseif ($action === 'cost_delete') {
                $this->overtime->delete_cost_rate($this->viewer['user_id'], (int) $this->value('rate_id'));
                $message = 'Cost rate removed. Overtime that used it keeps the cost already stored against it.';
            } else {
                $changed = $this->overtime->recalculate_costs($this->viewer['user_id']);
                $message = 'Recalculated ' . $changed . ' person row' . ($changed === 1 ? '' : 's') . ' at the current cost rates.';
            }
            $this->session->set_flashdata('overtime_message', $message);
        } catch (InvalidArgumentException $e) { $this->session->set_flashdata('overtime_error', $e->getMessage()); }
        catch (Throwable $e) { log_message('error', 'Overtime settings failed: ' . $e->getMessage()); $this->session->set_flashdata('overtime_error', 'Unable to save settings. Please try again.'); }
        $section = strpos($this->value('action'), 'cost') === 0 ? 'cost' : ($this->value('action') === 'policy' ? 'policy' : 'leaders');
        redirect(page_url . 'Overtime/settings?section=' . $section);
    }

    public function read_notification($id = 0)
    {
        $this->post_guard();
        if (!$this->ready()) return;
        $this->overtime->mark_read($this->viewer['user_id'], (int) $id);
        redirect(page_url . 'Overtime' . ($this->permissions['requests'] ? '' : '/approvals'));
    }
}
