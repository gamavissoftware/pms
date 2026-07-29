<?php
defined('BASEPATH') OR exit('No direct script access allowed');


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Task extends CI_Controller {
	private $receivedPoOwnOrdersOnly = false;

	public function __construct()
	{
		parent::__construct();
		ini_set('memory_limit', '-1');
		$this->load->model('User_model','user');
		$this->load->model('Task_model','task');
		$this->load->model('Ticket_model','Ticket_model');
		

		$config = Array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'taskmanagement@shubhampack.com',
        'smtp_pass' => 'ficihlqnfcdrrqkb',
        'mailtype'  => 'html', 
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
        //'smtp_crypto'   => 'tls'
        );
        //echo "<pre>"; print_r($config); exit;
         $this->email->initialize($config);
		$this->email->set_mailtype("html");
		if (!$this->session->userdata('logged_in'))
        { 
            $this->session->set_flashdata('message','Session Logged Out. Login to continue', 'refresh');
            redirect(page_url);
        }

	}

	private function ensureDfMeetingRecipientFields()
	{
		if ($this->db->table_exists('dfwise_mom') && !$this->db->field_exists('to_record', 'dfwise_mom')) {
			$this->db->query("ALTER TABLE `dfwise_mom` ADD `to_record` TEXT NULL AFTER `cc_record`");
		}
	}

	private function ensureDfMeetingPointTrackingFields()
	{
		if (!$this->db->table_exists('dfmom_points')) {
			return;
		}

		if (!$this->db->field_exists('responsible_person', 'dfmom_points')) {
			$this->db->query("ALTER TABLE `dfmom_points` ADD `responsible_person` INT(11) NOT NULL DEFAULT 0 AFTER `mom_point`");
		}

		if (!$this->db->field_exists('due_date', 'dfmom_points')) {
			$this->db->query("ALTER TABLE `dfmom_points` ADD `due_date` DATE DEFAULT NULL AFTER `responsible_person`");
		}

		if (!$this->db->field_exists('workstatus', 'dfmom_points')) {
			$this->db->query("ALTER TABLE `dfmom_points` ADD `workstatus` TINYINT(1) NOT NULL DEFAULT 0 AFTER `due_date`");
		}

		if (!$this->db->field_exists('update_on', 'dfmom_points')) {
			$this->db->query("ALTER TABLE `dfmom_points` ADD `update_on` DATETIME DEFAULT NULL AFTER `workstatus`");
		}

		if (!$this->db->field_exists('update_by', 'dfmom_points')) {
			$this->db->query("ALTER TABLE `dfmom_points` ADD `update_by` INT(11) NOT NULL DEFAULT 0 AFTER `update_on`");
		}

		if (!$this->db->field_exists('work_remarks', 'dfmom_points')) {
			$this->db->query("ALTER TABLE `dfmom_points` ADD `work_remarks` TEXT DEFAULT NULL AFTER `update_by`");
		}
	}

	private function canViewAllDfWeeklyMeetingPoints($scope = array())
	{
		if (empty($scope)) {
			$scope = $this->getTaskDashboardScope();
		}

		$user_id = isset($scope['user_id']) ? (int) $scope['user_id'] : 0;
		$admin_user_type = isset($scope['admin_user_type']) ? (int) $scope['admin_user_type'] : (isset($_SESSION['logged_in']['adminuser']) ? (int) $_SESSION['logged_in']['adminuser'] : 0);

		return $admin_user_type === 1 || !empty($scope['is_super_admin']) || in_array($user_id, array(61, 161, 189, 209), true);
	}

	private function getDfWeeklyMeetingPointStatusLabel($status)
	{
		$status = (int) $status;

		if ($status === 1) {
			return 'Done';
		}

		if ($status === 2) {
			return 'In Progress';
		}

		return 'Open';
	}

	private function canManageDfMeetingFridayReset($user_id = 0, $role_id = 0)
	{
		$user_id = (int) ($user_id ?: $this->session->userdata['logged_in']['user_id']);
		$role_id = (int) ($role_id ?: $this->session->userdata['logged_in']['role']);

		if (in_array($user_id, array(61, 161), true)) {
			return true;
		}

		return $this->isDashboardSuperAdmin($user_id, $role_id);
	}

	private function getDfMeetingFridayDate($reference_date = null, $include_current_friday = true)
	{
		$date = new DateTime(!empty($reference_date) ? $reference_date : date('Y-m-d'));
		$day_of_week = (int) $date->format('N'); // Monday = 1, Friday = 5
		$days_until_friday = 5 - $day_of_week;

		if ($days_until_friday < 0 || ($days_until_friday === 0 && !$include_current_friday)) {
			$days_until_friday += 7;
		}

		if ($days_until_friday !== 0) {
			$date->modify('+' . $days_until_friday . ' days');
		}

		return $date->format('Y-m-d');
	}

	private function getDfMeetingFridayWindow($reference_date = null, $include_current_friday = true)
	{
		$friday_date = $this->getDfMeetingFridayDate($reference_date, $include_current_friday);

		return array(
			'start_date' => $friday_date,
			'end_date' => $friday_date
		);
	}

	private function normalizeEmailList($raw_input)
	{
		$emails = array();

		foreach ((array) $raw_input as $value) {
			$value = trim((string) $value);
			if ($value === '') {
				continue;
			}

			$parts = preg_split('/[\s,;]+/', $value);
			foreach ((array) $parts as $email) {
				$email = strtolower(trim((string) $email));
				if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
					continue;
				}

				if (!in_array($email, $emails, true)) {
					$emails[] = $email;
				}
			}
		}

		return $emails;
	}

	private function getUserEmailsByIds($user_ids)
	{
		$emails = array();
		$normalized_ids = array();

		foreach ((array) $user_ids as $user_id) {
			$user_id = (int) $user_id;
			if ($user_id > 0 && !in_array($user_id, $normalized_ids, true)) {
				$normalized_ids[] = $user_id;
			}
		}

		if (empty($normalized_ids)) {
			return $emails;
		}

		$query = $this->db->select('email')
			->from('system_users')
			->where_in('user_id', $normalized_ids)
			->where('user_status', 1)
			->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$email = strtolower(trim((string) $row->email));
				if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $emails, true)) {
					$emails[] = $email;
				}
			}
		}

		return $emails;
	}

	private function buildDfTaskScheduleWindow($task_id, $start_date, $tat, $include_current_friday = true)
	{
		$task_id = (int) $task_id;
		$tat = (int) $tat;
		$start_date = !empty($start_date) ? $start_date : date('Y-m-d');

		if ($task_id === 4) {
			return $this->getDfMeetingFridayWindow($start_date, $include_current_friday);
		}

		$end_date = date(
			'Y-m-d',
			strtotime(
				'+' . max(0, ($tat - 1)) . ' days',
				strtotime($start_date)
			)
		);

		return $this->task->SKIP_holidays($start_date, $end_date);
	}

	private function getTaskSchedulingAuditColumns($user_id = 0)
	{
		$audit_data = array();
		$user_id = (int) $user_id;

		if ($this->db->field_exists('updated_by', 'task_department_wise_scheduling')) {
			$audit_data['updated_by'] = $user_id;
		}

		if ($this->db->field_exists('updated_on', 'task_department_wise_scheduling')) {
			$audit_data['updated_on'] = date('Y-m-d H:i:s');
		}

		return $audit_data;
	}

	private function isDashboardSuperAdmin($user_id = 0, $role_id = 0)
	{
		$user_id = (int) ($user_id ?: $this->session->userdata['logged_in']['user_id']);
		$role_id = (int) ($role_id ?: $this->session->userdata['logged_in']['role']);

		if (in_array($user_id, array(189, 209), true)) {
			return true;
		}

		$this->load->model('Dashboard_model', 'dashboardmodel');
		$super_admin_roles = $this->dashboardmodel->getsuperadminuserole();

		return in_array($role_id, $super_admin_roles, true);
	}

	private function getTaskDashboardScope($user_id = 0, $role_id = 0)
	{
		$user_id = (int) ($user_id ?: $this->session->userdata['logged_in']['user_id']);
		$role_id = (int) ($role_id ?: $this->session->userdata['logged_in']['role']);
		$admin_user_type = isset($_SESSION['logged_in']['adminuser']) ? (int) $_SESSION['logged_in']['adminuser'] : 0;

		$mapped_department_ids = array();
		$view_department_ids = array();
		$assign_department_ids = array();

		if ($admin_user_type === 0) {
			if ($this->isDashboardSuperAdmin($user_id, $role_id)) {
				$admin_user_type = 1;
			} else {
				$mapped_department_ids = $this->task->getAssignedDepartment($user_id);
				$admin_user_type = !empty($mapped_department_ids) ? 2 : 3;
			}
		}

		if ($admin_user_type === 2 && empty($mapped_department_ids)) {
			$mapped_department_ids = $this->task->getAssignedDepartment($user_id);
		}

		if ($admin_user_type === 2 && !empty($mapped_department_ids)) {
			$view_department_ids = $mapped_department_ids;
			$assign_department_ids = $mapped_department_ids;
		}

		$is_super_admin = ($admin_user_type === 1);
		$can_view_team_tasks = ($admin_user_type === 1 || $admin_user_type === 2);
		$can_assign_tasks = ($admin_user_type === 1 || $admin_user_type === 2);
		$personal_visible_user_ids = $this->getTaskPersonalVisibleAssigneeIds($user_id);

		return array(
			'user_id' => $user_id,
			'role_id' => $role_id,
			'is_super_admin' => $is_super_admin,
			'admin_user_type' => $admin_user_type,
			'mapped_department_ids' => array_values(array_unique(array_map('intval', $mapped_department_ids))),
			'view_department_ids' => array_values(array_unique(array_map('intval', $view_department_ids))),
			'assign_department_ids' => array_values(array_unique(array_map('intval', $assign_department_ids))),
			'personal_visible_user_ids' => $personal_visible_user_ids,
			'can_view_team_tasks' => $can_view_team_tasks,
			'can_assign_tasks' => $can_assign_tasks,
			'personal_task_only' => !$is_super_admin && !$can_view_team_tasks
		);
	}

	private function normalizeTaskDashboardUserIds($user_ids)
	{
		$normalized_ids = array();

		foreach ((array) $user_ids as $user_id) {
			$user_id = (int) $user_id;
			if ($user_id <= 0) {
				continue;
			}

			if (!in_array($user_id, $normalized_ids, true)) {
				$normalized_ids[] = $user_id;
			}
		}

		return $normalized_ids;
	}

	private function applyTaskAssignedUserFilter($column, $user_ids)
	{
		$user_ids = $this->normalizeTaskDashboardUserIds($user_ids);
		if (empty($user_ids)) {
			return false;
		}

		if (count($user_ids) === 1) {
			$this->db->where($column, $user_ids[0]);
		} else {
			$this->db->where_in($column, $user_ids);
		}

		return true;
	}

	private function applyTaskAssignedUserOrFilter($column, $user_ids)
	{
		$user_ids = $this->normalizeTaskDashboardUserIds($user_ids);
		if (empty($user_ids)) {
			return false;
		}

		if (count($user_ids) === 1) {
			$this->db->or_where($column, $user_ids[0]);
		} else {
			$this->db->or_where_in($column, $user_ids);
		}

		return true;
	}

	private function getTaskProjectCoordinatorAssigneeIds($user_id = 0)
	{
		$user_id = (int) ($user_id ?: $this->session->userdata['logged_in']['user_id']);
		if ($user_id <= 0) {
			return array();
		}

		if (!$this->db->table_exists('system_users') || !$this->db->field_exists('project_coordinator_user_id', 'system_users')) {
			return array();
		}

		$assignee_ids = array();
		$query = $this->db->select('user_id')
			->from('system_users')
			->where('project_coordinator_user_id', $user_id)
			->where('user_status', 1)
			->order_by('first_name', 'ASC')
			->order_by('last_name', 'ASC')
			->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$row_user_id = isset($row->user_id) ? (int) $row->user_id : 0;
				if ($row_user_id > 0 && $row_user_id !== $user_id) {
					$assignee_ids[] = $row_user_id;
				}
			}
		}

		return $this->normalizeTaskDashboardUserIds($assignee_ids);
	}

	private function getTaskPersonalVisibleAssigneeIds($user_id = 0)
	{
		$user_id = (int) ($user_id ?: $this->session->userdata['logged_in']['user_id']);
		if ($user_id <= 0) {
			return array();
		}

		$visible_user_ids = array($user_id);
		$coordinator_assignee_ids = $this->getTaskProjectCoordinatorAssigneeIds($user_id);
		if (!empty($coordinator_assignee_ids)) {
			$visible_user_ids = array_merge($visible_user_ids, $coordinator_assignee_ids);
		}

		return $this->normalizeTaskDashboardUserIds($visible_user_ids);
	}

	private function canCurrentUserUpdateOwnOrCoordinatorTask($assigned_user_id, $scope = array())
	{
		$assigned_user_id = (int) $assigned_user_id;
		if ($assigned_user_id <= 0) {
			return false;
		}

		if (empty($scope)) {
			$scope = $this->getTaskDashboardScope();
		}

		$current_user_id = isset($scope['user_id']) ? (int) $scope['user_id'] : 0;
		if ($current_user_id <= 0) {
			return false;
		}

		if ($current_user_id === $assigned_user_id) {
			return true;
		}

		$personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array();

		return in_array($assigned_user_id, $personal_visible_user_ids, true);
	}

	private function ensurePunchPointClosureTableExists()
	{
		if (!$this->db->table_exists('task_punch_point_closure')) {
			$this->db->query("
				CREATE TABLE IF NOT EXISTS `task_punch_point_closure` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`task_record_id` int(11) NOT NULL DEFAULT 0,
					`source_task_record_id` int(11) NOT NULL DEFAULT 0,
					`task_id` int(11) NOT NULL DEFAULT 11,
					`df_id` int(11) NOT NULL DEFAULT 0,
					`po_id` int(11) NOT NULL DEFAULT 0,
					`assigned_user` int(11) NOT NULL DEFAULT 0,
					`point_title` varchar(255) NOT NULL,
					`point_description` text DEFAULT NULL,
					`department_id` int(11) NOT NULL DEFAULT 0,
					`delegate_to` int(11) NOT NULL DEFAULT 0,
					`due_date` date NOT NULL,
					`delegation_task_id` int(11) NOT NULL DEFAULT 0,
					`status` tinyint(1) NOT NULL DEFAULT 1,
					`added_on` datetime NOT NULL,
					`added_by` int(11) NOT NULL DEFAULT 0,
					`updated_on` datetime DEFAULT NULL,
					`updated_by` int(11) NOT NULL DEFAULT 0,
					PRIMARY KEY (`id`),
					KEY `task_record_id` (`task_record_id`),
					KEY `source_task_record_id` (`source_task_record_id`),
					KEY `df_id` (`df_id`),
					KEY `delegate_to` (`delegate_to`),
					KEY `delegation_task_id` (`delegation_task_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
			");
		}

		if (!$this->db->field_exists('source_task_record_id', 'task_punch_point_closure')) {
			$this->db->query("ALTER TABLE `task_punch_point_closure` ADD `source_task_record_id` int(11) NOT NULL DEFAULT 0 AFTER `task_record_id`");
			$this->db->query("ALTER TABLE `task_punch_point_closure` ADD KEY `source_task_record_id` (`source_task_record_id`)");
		}
	}

	private function generateDelegationCaseNo()
	{
		$row = $this->db->select_max('id', 'max_id')
			->from('delegation_task')
			->get()
			->row();

		$next_id = !empty($row->max_id) ? ((int) $row->max_id + 1) : 1;

		return 'SP-' . $next_id;
	}

	private function isPunchPointWorkflowTaskId($task_id)
	{
		return in_array((int) $task_id, array(50, 51, 11), true);
	}

	private function getPunchPointWorkflowLabel($task_id)
	{
		$task_id = (int) $task_id;

		if ($task_id === 50) {
			return 'Punch Point List';
		}

		if ($task_id === 51) {
			return 'Punch Point Closure';
		}

		return 'Punch Point Closure Report';
	}

	private function getPunchPointClosureTaskRecord($task_record_id, $scope = array(), $task_ids = array(11))
	{
		$task_record_id = (int) $task_record_id;
		if ($task_record_id <= 0) {
			return null;
		}

		if (empty($scope)) {
			$scope = $this->getTaskDashboardScope();
		}

		$this->db->select('
			tdws.id,
			tdws.taskid,
			tdws.df_id,
			tdws.po_id,
			tdws.department_id,
			tdws.assigned_user,
			tdws.start_date,
			tdws.end_date,
			tdws.task_status,
			tdws.task_completed_on,
			tdws.remarks,
			tm.task_name,
			tm.df_meeting_close,
			tm.isitfinalstep,
			df.df_no,
			df.df_description,
			df.added_on AS df_added_on,
			po.company_name,
			po.pono,
			po.df_number,
			u.first_name,
			u.last_name
		');
		$this->db->from('task_department_wise_scheduling tdws');
		$this->db->join('task_management tm', 'tdws.taskid = tm.task_id', 'left');
		$this->db->join('df_release df', 'tdws.df_id = df.id', 'left');
		$this->db->join('poreceived po', 'tdws.po_id = po.id', 'left');
		$this->db->join('system_users u', 'tdws.assigned_user = u.user_id', 'left');
		$this->db->where('tdws.id', $task_record_id);
		$this->db->where_in('tdws.taskid', $task_ids);

		$row = $this->db->get()->row();
		if (empty($row)) {
			return null;
		}

		$current_user_id = isset($scope['user_id']) ? (int) $scope['user_id'] : 0;
		$can_access = ($current_user_id === 61 || $current_user_id === 161)
			|| $this->canCurrentUserUpdateOwnOrCoordinatorTask((int) $row->assigned_user, $scope);

		if (!$can_access) {
			return null;
		}

		return $row;
	}

	private function getPunchPointSourceTaskRecordId($task_row)
	{
		if (empty($task_row)) {
			return 0;
		}

		$current_task_id = (int) $task_row->taskid;
		if ($current_task_id === 50) {
			return (int) $task_row->id;
		}

		$this->db->select('id');
		$this->db->from('task_department_wise_scheduling');
		$this->db->where('taskid', 50);

		if ((int) $task_row->df_id > 0) {
			$this->db->where('df_id', (int) $task_row->df_id);
		}

		if ((int) $task_row->po_id > 0) {
			$this->db->where('po_id', (int) $task_row->po_id);
		}

		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);

		$row = $this->db->get()->row();

		return !empty($row->id) ? (int) $row->id : 0;
	}

	private function getRelatedPunchPointTaskRecord($task_row, $task_id)
	{
		if (empty($task_row) || (int) $task_id <= 0) {
			return null;
		}

		$this->db->select('id, taskid, task_status, assigned_user, start_date, end_date, task_completed_on');
		$this->db->from('task_department_wise_scheduling');
		$this->db->where('taskid', (int) $task_id);

		if ((int) $task_row->df_id > 0) {
			$this->db->where('df_id', (int) $task_row->df_id);
		}

		if ((int) $task_row->po_id > 0) {
			$this->db->where('po_id', (int) $task_row->po_id);
		}

		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);

		return $this->db->get()->row();
	}

	private function getPunchPointClosureItems($task_row)
	{
		$this->ensurePunchPointClosureTableExists();

		if (empty($task_row) || empty($task_row->id)) {
			return array();
		}

		$source_task_record_id = $this->getPunchPointSourceTaskRecordId($task_row);
		$fallback_task_record_ids = array_unique(array_filter(array(
			(int) $source_task_record_id,
			(int) $task_row->id
		)));

		$query = $this->db->select('
			ppc.*,
			d.department,
			u.first_name,
			u.last_name,
			dt.case_no,
			dt.task AS delegated_task,
			dt.email_url,
			dt.task_status AS delegated_task_status,
			dt.updated_time AS delegated_updated_time
		')
			->from('task_punch_point_closure ppc')
			->join('departments d', 'ppc.department_id = d.department_id', 'left')
			->join('system_users u', 'ppc.delegate_to = u.user_id', 'left')
			->join('delegation_task dt', 'ppc.delegation_task_id = dt.id', 'left')
			->where('ppc.status', 1);

		if ((int) $task_row->df_id > 0) {
			$query->where('ppc.df_id', (int) $task_row->df_id);
		}

		if ((int) $task_row->po_id > 0) {
			$query->where('ppc.po_id', (int) $task_row->po_id);
		}

		if ($source_task_record_id > 0) {
			$query->group_start()
				->where('ppc.source_task_record_id', $source_task_record_id)
				->or_group_start()
					->where('ppc.source_task_record_id', 0);

			if (!empty($fallback_task_record_ids)) {
				$query->group_start()
					->where_in('ppc.task_record_id', $fallback_task_record_ids)
					->or_where_in('ppc.task_id', array(50, 11))
				->group_end();
			} else {
				$query->where_in('ppc.task_id', array(50, 11));
			}

			$query->group_end()
			->group_end();
		} else if (!empty($fallback_task_record_ids)) {
			$query->where_in('ppc.task_record_id', $fallback_task_record_ids);
		} else {
			$query->where('ppc.task_record_id', (int) $task_row->id);
		}

		$query = $query
			->order_by('ppc.id', 'DESC')
			->get();

		$items = array();
		if ($query->num_rows() <= 0) {
			return $items;
		}

		foreach ($query->result() as $row) {
			$response_query = $this->db->select('remarks, status, created_at')
				->from('delegation_task_response')
				->where('task_id', (int) $row->delegation_task_id)
				->order_by('id', 'DESC')
				->limit(1)
				->get();

			$response_row = $response_query->row();

			if (empty($response_row)) {
				$response_query = $this->db->select('user_response AS remarks, task_status AS status, updated_on AS created_at')
					->from('user_response_on_delegated_task')
					->where('task_id', (int) $row->delegation_task_id)
					->order_by('id', 'DESC')
					->limit(1)
					->get();

				$response_row = $response_query->row();
				}

				$row->delegate_name = strtoupper(trim($row->first_name . ' ' . $row->last_name));
				$row->is_done = ((int) $row->delegated_task_status === 1);
				$row->response_status_label = $row->is_done ? 'Done' : 'Pending';
				$row->response_status_class = $row->is_done ? 'label-success' : 'label-warning';
				$row->response_remarks = '';
				$row->response_updated_on = !empty($row->delegated_updated_time) && $row->delegated_updated_time !== '0000-00-00 00:00:00'
					? (string) $row->delegated_updated_time
					: '';

				if (!empty($response_row)) {
					if ((int) $response_row->status === 1) {
						$row->is_done = true;
						$row->response_status_label = 'Done';
						$row->response_status_class = 'label-success';
					}

				$row->response_remarks = (string) $response_row->remarks;
				$row->response_updated_on = (string) $response_row->created_at;
			}

			$items[] = $row;
		}

		return $items;
	}

	private function countCompletedPunchPointItems($closure_items)
	{
		$completed_count = 0;

		if (empty($closure_items)) {
			return 0;
		}

		foreach ($closure_items as $closure_item) {
			if (!empty($closure_item->is_done)) {
				$completed_count++;
			}
		}

		return $completed_count;
	}

	private function getPunchPointWorkflowState($task_row, $closure_items = null)
	{
		if (empty($task_row)) {
			return array(
				'total_items' => 0,
				'completed_items' => 0,
				'all_items_done' => false,
				'can_add_points' => false,
				'can_complete_task' => false,
				'block_message' => 'Task details are not available.',
				'related_task_51' => null,
				'related_task_51_done' => false
			);
		}

		if ($closure_items === null) {
			$closure_items = $this->getPunchPointClosureItems($task_row);
		}

		$total_items = count($closure_items);
		$completed_items = $this->countCompletedPunchPointItems($closure_items);
		$all_items_done = ($total_items > 0 && $completed_items === $total_items);
		$related_task_51 = $this->getRelatedPunchPointTaskRecord($task_row, 51);
		$related_task_51_done = (!empty($related_task_51) && (int) $related_task_51->task_status === 1);
		$current_task_id = (int) $task_row->taskid;
		$is_task_open = ((int) $task_row->task_status === 0);
		$can_add_points = ($current_task_id === 50 && $is_task_open);
		$can_complete_task = false;
		$block_message = '';

		if (!$is_task_open) {
			$block_message = 'This task has already been submitted.';
		} else if ($current_task_id === 50) {
			if ($total_items <= 0) {
				$block_message = 'Add at least one punch point to complete Punch Point List.';
			} else {
				$can_complete_task = true;
			}
		} else if ($current_task_id === 51) {
			if ($total_items <= 0) {
				$block_message = 'Punch point list is not available yet. Complete Punch Point List first.';
			} else if (!$all_items_done) {
				$block_message = 'All delegated punch points must be marked done before Punch Point Closure can be closed.';
			} else {
				$can_complete_task = true;
			}
		} else if ($current_task_id === 11) {
			if ($total_items <= 0) {
				$block_message = 'Punch point list is not available yet. Complete Punch Point List first.';
			} else if (!$related_task_51_done) {
				$block_message = 'Punch Point Closure must be marked done before this report can be closed.';
			} else {
				$can_complete_task = true;
			}
		}

		return array(
			'total_items' => $total_items,
			'completed_items' => $completed_items,
			'all_items_done' => $all_items_done,
			'can_add_points' => $can_add_points,
			'can_complete_task' => $can_complete_task,
			'block_message' => $block_message,
			'related_task_51' => $related_task_51,
			'related_task_51_done' => $related_task_51_done
		);
	}

	private function buildPunchPointClosureButton($task_record_id, $task_id = 11)
	{
		$task_record_id = (int) $task_record_id;
		if ($task_record_id <= 0) {
			return '';
		}

		$task_id = (int) $task_id;
		$label = 'Closure Points';
		$button_class = 'btn-success';

		if ($task_id === 50) {
			$label = 'Add Points';
			$button_class = 'btn-primary';
		} else if ($task_id === 51) {
			$label = 'View Points';
			$button_class = 'btn-info';
		} else if ($task_id === 11) {
			$label = 'Closure Report';
			$button_class = 'btn-success';
		}

		return '<a href="' . page_url . 'Task/punch_point_closure/' . $task_record_id . '" class="btn ' . $button_class . ' btn-xs" style="margin-right:4px; white-space:nowrap;">' . $label . '</a>';
	}

	private function buildPunchPointDelegationText($task_row, $point_title, $point_description = '')
	{
		$df_label = !empty($task_row->df_no) ? strtoupper($task_row->df_no) : '';
		if ($df_label === '' && !empty($task_row->df_number)) {
			$df_label = 'DF-' . $task_row->df_number;
		}

		$task_lines = array();
		if ($df_label !== '') {
			$task_lines[] = 'DF NO: ' . $df_label;
		}

		if (!empty($task_row->company_name)) {
			$task_lines[] = 'CUSTOMER: ' . strtoupper($task_row->company_name);
		}

		$task_lines[] = 'PUNCH POINT: ' . strtoupper($point_title);

		if ($point_description !== '') {
			$task_lines[] = 'DETAIL: ' . strtoupper($point_description);
		}

		$task_lines[] = 'SOURCE TASK: PUNCH POINT CLOSURE REPORT';

		return implode("\n", $task_lines);
	}

	private function getPunchPointClosureMailUser($user_id)
	{
		$user_id = (int) $user_id;
		if ($user_id <= 0) {
			return null;
		}

		return $this->db->select('user_id, first_name, last_name, email')
			->from('system_users')
			->where('user_id', $user_id)
			->limit(1)
			->get()
			->row();
	}

	private function sendPunchPointClosureDelegationEmail($task_row, $delegate_to, $delegated_by_user_id, $point_title, $due_date, $email_url, $delegation_case_no)
	{
		$delegate_user = $this->getPunchPointClosureMailUser($delegate_to);
		$delegated_by_user = $this->getPunchPointClosureMailUser($delegated_by_user_id);

		if (empty($delegate_user) || empty($delegate_user->email)) {
			return false;
		}

		$delegate_name = trim($delegate_user->first_name . ' ' . $delegate_user->last_name);
		if ($delegate_name === '') {
			$delegate_name = 'Team Member';
		}

		$delegated_by_name = 'PMS USER';
		if (!empty($delegated_by_user)) {
			$delegated_by_name = trim($delegated_by_user->first_name . ' ' . $delegated_by_user->last_name);
			if ($delegated_by_name === '') {
				$delegated_by_name = 'PMS USER';
			}
		}

		$df_label = 'NA';
		if (!empty($task_row->df_no)) {
			$df_label = strtoupper($task_row->df_no);
		} elseif (!empty($task_row->df_number)) {
			$df_label = 'DF-' . strtoupper($task_row->df_number);
		}

		$customer_name = !empty($task_row->company_name) ? strtoupper($task_row->company_name) : 'NA';
		$task_name = !empty($task_row->task_name) ? strtoupper($task_row->task_name) : 'PUNCH POINT CLOSURE REPORT';
		$due_date_label = ($due_date !== '' && $due_date !== '0000-00-00') ? date('d-M-Y', strtotime($due_date)) : 'NA';
		$point_label = strtoupper(trim((string) $point_title));
		$case_label = strtoupper(trim((string) $delegation_case_no));
		$email_url_safe = htmlspecialchars($email_url, ENT_QUOTES, 'UTF-8');

		$message = '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; background-color: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px;">'
			. '<tr><td style="background-color: #4872b8; padding: 10px; text-align: center;">'
			. '<img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="160" alt="Shubham Packs" style="display: block; margin: 0 auto;" />'
			. '</td></tr>'
			. '<tr><td style="padding: 15px; background-color: #ffffff;">'
			. '<h2 style="color: #333333; font-size: 20px; margin: 0; text-align: center;">WORK DELEGATION NOTIFICATION</h2>'
			. '<hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">'
			. '<p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;"><strong>Dear ' . htmlspecialchars(ucwords(strtolower($delegate_name)), ENT_QUOTES, 'UTF-8') . ',</strong><br>A new closure point has been delegated to you. Below are the task details:</p>'
			. '<p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">'
			. '<strong>Case No.:</strong> ' . htmlspecialchars($case_label, ENT_QUOTES, 'UTF-8') . '<br>'
			. '<strong>DF No.:</strong> ' . htmlspecialchars($df_label, ENT_QUOTES, 'UTF-8') . '<br>'
			. '<strong>Customer:</strong> ' . htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8') . '<br>'
			. '<strong>Parent Task:</strong> ' . htmlspecialchars($task_name, ENT_QUOTES, 'UTF-8') . '<br>'
			. '<strong>Closure Point:</strong> ' . htmlspecialchars($point_label, ENT_QUOTES, 'UTF-8') . '<br>'
			. '<strong>Task Completion Date:</strong> ' . htmlspecialchars($due_date_label, ENT_QUOTES, 'UTF-8') . '<br>'
			. '<strong>Delegated By:</strong> ' . htmlspecialchars(ucwords(strtolower($delegated_by_name)), ENT_QUOTES, 'UTF-8')
			. '</p>'
			. '<p style="color: #555555; font-size: 14px; margin: 15px 0; line-height: 1.6;">Kindly log in to your PMS and update the progress on the task at the earliest.</p>'
			. '<p style="text-align: center; margin: 22px 0;">'
			. '<a href="' . $email_url_safe . '" style="display: inline-block; padding: 12px 20px; background-color: #34a853; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold;">Open Punch Point Closure</a>'
			. '</p>'
			. '<hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">'
			. '<p style="color: #555555; font-size: 13px; text-align: center; margin: 0; line-height: 1.6;">If you have any questions, contact us at <a href="mailto:taskmanagement@shubhampack.com" style="color: #4872b8; text-decoration: none;">taskmanagement@shubhampack.com</a>.</p>'
			. '</td></tr>'
			. '<tr><td style="background-color: #4872b8; color: #ffffff; padding: 10px; text-align: center; font-size: 12px;">&copy; ' . date('Y') . ' Shubham Packs. All rights reserved.</td></tr>'
			. '</table>';

		$this->email->clear(true);
		$this->email->set_mailtype('html');
		$this->email->to($delegate_user->email);
		$this->email->bcc('mangleshup@gmail.com');
		$this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
		$this->email->subject('Work Delegation Notification | ' . $case_label);
		$this->email->message($message);

		$result = $this->email->send();
		if (!$result) {
			log_message('error', 'Punch point delegation email failed for user_id ' . $delegate_to . ' case ' . $case_label . '.');
		}

		$this->email->clear(true);

		return $result;
	}

	private function completePunchPointClosureParentTask($task_row, $user_id, $auto_note = '')
	{
		if (empty($task_row) || empty($task_row->id)) {
			return 0;
		}

		$record_id = (int) $task_row->id;
		$mastertaskid = (int) $task_row->taskid;
		$dfid = (int) $task_row->df_id;
		$now = date('Y-m-d H:i:s');
		$today = date('Y-m-d');
		if ($auto_note === '') {
			$auto_note = 'Auto-closed from ' . $this->getPunchPointWorkflowLabel($mastertaskid);
		}

		if (!empty($task_row->start_date) && $task_row->start_date > $today) {
			$precloserdata = array(
				'df_id' => $dfid,
				'task_id' => $mastertaskid,
				'record_id' => $record_id,
				'added_on' => $now,
				'added_by' => $user_id,
				'remarks' => $auto_note,
				'current_status' => 0
			);
			$this->db->insert('precloser_task_request', $precloserdata);

			$dataupdate11 = array(
				'task_status' => 2,
				'task_completed_on' => $now,
				'taskupdatedontime' => $now,
				'task_completed_by' => $user_id
			);
			$this->db->where('id', $record_id);
			$this->db->where('taskid', $mastertaskid);
			$this->db->update('task_department_wise_scheduling', $dataupdate11);

			$payload = json_encode(array(
				'df_id' => $dfid,
				'task_id' => $mastertaskid,
				'user_id' => $user_id,
				'remarks' => $auto_note
			));
			$this->db->insert('notification_queue', array(
				'notification_type' => 'pre_closer',
				'payload' => $payload,
				'status' => 'pending',
				'created_at' => $now
			));

			return 2;
		}

		$data = array(
			'task_status' => 1,
			'task_completed_on' => $now,
			'taskupdatedontime' => $now,
			'task_completed_by' => $user_id
		);
		$this->db->where('id', $record_id);
		$this->db->where('taskid', $mastertaskid);
		$this->db->update('task_department_wise_scheduling', $data);

		$payload = json_encode(array(
			'record_id' => $record_id,
			'mastertaskid' => $mastertaskid,
			'user_id' => $user_id,
			'dfid' => $dfid
		));
		$this->db->insert('notification_queue', array(
			'notification_type' => 'task_completion',
			'payload' => $payload,
			'status' => 'pending',
			'created_at' => $now
		));

		if ((int) $task_row->df_meeting_close === 1) {
			$data3 = array(
				'task_status' => 1,
				'task_completed_on' => $now,
				'taskupdatedontime' => $now
			);
			$this->db->where('taskid', 4);
			$this->db->where('df_id', $dfid);
			$this->db->update('task_department_wise_scheduling', $data3);
		}

		if ((int) $task_row->isitfinalstep === 1 && $dfid > 0) {
			$dfdataarray = array(
				'df_status' => 1,
				'completed_on' => $now,
				'completed_by' => $user_id
			);
			$this->db->where('id', $dfid);
			$this->db->update('df_release', $dfdataarray);
		}

		return 1;
	}

	public function punch_point_closure()
	{
		$scope = $this->getTaskDashboardScope();
		$task_record_id = (int) $this->uri->segment(3);
		$task_row = $this->getPunchPointClosureTaskRecord($task_record_id, $scope, array(50, 51, 11));

		if (empty($task_row)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry!, Punch point closure task was not found or you do not have access.</div>');
			redirect(page_url . 'Dashboard');
		}

		$this->ensurePunchPointClosureTableExists();

		$department_options = $this->db->select('department_id, department')
			->from('departments')
			->where('status', 1)
			->where('business_loc_id', 2)
			->order_by('department', 'ASC')
			->get()
			->result();

		$closure_items = $this->getPunchPointClosureItems($task_row);
		$workflow_state = $this->getPunchPointWorkflowState($task_row, $closure_items);
		$page_mode = 'report';
		if ((int) $task_row->taskid === 50) {
			$page_mode = 'list';
		} else if ((int) $task_row->taskid === 51) {
			$page_mode = 'closure';
		}

		$data = array(
			'task_row' => $task_row,
			'closure_items' => $closure_items,
			'department_options' => $department_options,
			'workflow_state' => $workflow_state,
			'page_mode' => $page_mode,
			'page_title' => $this->getPunchPointWorkflowLabel((int) $task_row->taskid)
		);

		$this->load->view('taskview/punch_point_closure', $data);
	}

	public function save_punch_point_closure()
	{
		$scope = $this->getTaskDashboardScope();
		$task_record_id = (int) $this->input->post('task_record_id');
		$task_row = $this->getPunchPointClosureTaskRecord($task_record_id, $scope, array(50));

		if (empty($task_row)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry!, Punch point closure task was not found or you do not have access.</div>');
			redirect(page_url . 'Dashboard');
		}

		if ((int) $task_row->task_status !== 0) {
			$this->session->set_flashdata('message', '<div class="alert alert-info alert-dismissable">This punch point list has already been submitted.</div>');
			redirect(page_url . 'Task/punch_point_closure/' . $task_record_id);
		}

		$this->ensurePunchPointClosureTableExists();

		$point_titles = $this->input->post('point_title');
		$point_descriptions = $this->input->post('point_description');
		$department_ids = $this->input->post('department_id');
		$delegate_to_ids = $this->input->post('delegate_to');
		$due_dates = $this->input->post('due_date');

		if (!is_array($point_titles) || empty($point_titles)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Please add at least one closure point.</div>');
			redirect(page_url . 'Task/punch_point_closure/' . $task_record_id);
		}

		$user_id = (int) $this->session->userdata['logged_in']['user_id'];
		$added_on = date('Y-m-d H:i:s');
		$success_count = 0;
		$source_task_record_id = (int) $task_row->id;

		foreach ($point_titles as $index => $point_title) {
			$point_title = trim((string) $point_title);
			$point_description = isset($point_descriptions[$index]) ? trim((string) $point_descriptions[$index]) : '';
			$department_id = isset($department_ids[$index]) ? (int) $department_ids[$index] : 0;
			$delegate_to = isset($delegate_to_ids[$index]) ? (int) $delegate_to_ids[$index] : 0;
			$due_date_raw = isset($due_dates[$index]) ? trim((string) $due_dates[$index]) : '';

			if ($point_title === '' || $department_id <= 0 || $delegate_to <= 0 || $due_date_raw === '') {
				continue;
			}

			$due_date = date('Y-m-d', strtotime($due_date_raw));
			if ($due_date === '1970-01-01' || $due_date === '0000-00-00') {
				continue;
			}

			$delegation_case_no = $this->generateDelegationCaseNo();
			$delegation_task_text = $this->buildPunchPointDelegationText($task_row, $point_title, $point_description);
			$email_url = page_url . 'Task/punch_point_closure/' . $task_record_id;

			$delegation_data = array(
				'yourname' => $user_id,
				'department_id' => $department_id,
				'delegate_to' => $delegate_to,
				'urgency' => 0,
				'task' => $delegation_task_text,
				'image' => '',
				'email_url' => $email_url,
				'delegated_date' => $due_date,
				'second_date' => '0000-00-00',
				'third_date' => '0000-00-00',
				'case_no' => $delegation_case_no,
				'added_on' => $added_on,
				'targetdate' => $due_date,
				'done_ontime_or_late' => 0,
				'task_completed_time' => '0000-00-00 00:00:00',
				'added_by' => $user_id,
				'task_status' => 0,
				'updated_time' => '0000-00-00 00:00:00',
				'task_module_date' => 1
			);

			$this->db->insert('delegation_task', $delegation_data);
			$delegation_task_id = (int) $this->db->insert_id();

			if ($delegation_task_id <= 0) {
				continue;
			}

				$closure_data = array(
					'task_record_id' => $task_record_id,
					'source_task_record_id' => $source_task_record_id,
					'task_id' => 50,
					'df_id' => (int) $task_row->df_id,
					'po_id' => (int) $task_row->po_id,
					'assigned_user' => (int) $task_row->assigned_user,
				'point_title' => strtoupper($point_title),
				'point_description' => $point_description !== '' ? strtoupper($point_description) : null,
				'department_id' => $department_id,
				'delegate_to' => $delegate_to,
				'due_date' => $due_date,
				'delegation_task_id' => $delegation_task_id,
				'status' => 1,
				'added_on' => $added_on,
				'added_by' => $user_id,
				'updated_on' => null,
				'updated_by' => 0
			);

			$this->db->insert('task_punch_point_closure', $closure_data);
			$this->sendPunchPointClosureDelegationEmail(
				$task_row,
				$delegate_to,
				$user_id,
				$point_title,
				$due_date,
				$email_url,
				$delegation_case_no
			);
			$success_count++;
		}

		if ($success_count > 0) {
			$completion_status = $this->completePunchPointClosureParentTask($task_row, $user_id, 'Auto-closed from Punch Point List');

			if ($completion_status === 2) {
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Punch point list saved successfully and Punch Point List has been sent for approval.</div>');
			} else {
				$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Punch point list saved successfully and Punch Point List has been marked done.</div>');
			}
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Please fill all required closure point details before submitting.</div>');
		}

		redirect(page_url . 'Task/punch_point_closure/' . $task_record_id);
	}

	public function complete_punch_point_workflow_task()
	{
		$scope = $this->getTaskDashboardScope();
		$task_record_id = (int) $this->input->post('task_record_id');
		$task_row = $this->getPunchPointClosureTaskRecord($task_record_id, $scope, array(50, 51, 11));

		if (empty($task_row)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry!, Punch point workflow task was not found or you do not have access.</div>');
			redirect(page_url . 'Dashboard');
		}

		if ((int) $task_row->task_status !== 0) {
			$this->session->set_flashdata('message', '<div class="alert alert-info alert-dismissable">This task has already been submitted.</div>');
			redirect(page_url . 'Task/punch_point_closure/' . $task_record_id);
		}

		$closure_items = $this->getPunchPointClosureItems($task_row);
		$workflow_state = $this->getPunchPointWorkflowState($task_row, $closure_items);

		if (empty($workflow_state['can_complete_task'])) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">' . htmlspecialchars($workflow_state['block_message'], ENT_QUOTES, 'UTF-8') . '</div>');
			redirect(page_url . 'Task/punch_point_closure/' . $task_record_id);
		}

		$user_id = (int) $this->session->userdata['logged_in']['user_id'];
		$completion_status = $this->completePunchPointClosureParentTask(
			$task_row,
			$user_id,
			'Auto-closed from ' . $this->getPunchPointWorkflowLabel((int) $task_row->taskid)
		);

		$success_label = $this->getPunchPointWorkflowLabel((int) $task_row->taskid);
		if ($completion_status === 2) {
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">' . htmlspecialchars($success_label, ENT_QUOTES, 'UTF-8') . ' has been submitted and sent for approval.</div>');
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">' . htmlspecialchars($success_label, ENT_QUOTES, 'UTF-8') . ' has been marked done successfully.</div>');
		}

		redirect(page_url . 'Task/punch_point_closure/' . $task_record_id);
	}

	public function punch_point_department_users()
	{
		$scope = $this->getTaskDashboardScope();
		$department_id = (int) $this->input->post('department');
		$option = '<option value="">--SELECT USER--</option>';

		if ($department_id <= 0) {
			echo $option;
			exit;
		}

		$user_map = array();
		$assignable_users = $this->getAssignableUsersForDepartment($department_id, $scope);
		if (!empty($assignable_users)) {
			foreach ($assignable_users as $assignable_user) {
				$user_id = isset($assignable_user['user_id']) ? (int) $assignable_user['user_id'] : 0;
				if ($user_id <= 0 || isset($user_map[$user_id])) {
					continue;
				}

				$label = isset($assignable_user['label']) ? trim((string) $assignable_user['label']) : '';
				if ($label === '') {
					$label = 'USER #' . $user_id;
				}

				$user_map[$user_id] = $label;
			}
		}

		$fallback_query = $this->db->select('user_id, first_name, last_name, user_status, hide_profile')
			->from('system_users')
			->where('department_id', $department_id)
			->group_start()
				->where('user_status', 1)
				->or_where('user_status IS NULL', null, false)
			->group_end()
			->order_by('first_name', 'ASC')
			->order_by('last_name', 'ASC')
			->get();

		if ($fallback_query->num_rows() > 0) {
			foreach ($fallback_query->result() as $row) {
				$user_id = isset($row->user_id) ? (int) $row->user_id : 0;
				if ($user_id <= 0 || isset($user_map[$user_id])) {
					continue;
				}

				$full_name = trim($row->first_name . ' ' . $row->last_name);
				if ($full_name === '') {
					$full_name = 'USER #' . $user_id;
				}

				$user_map[$user_id] = strtoupper($full_name);
			}
		}

		foreach ($user_map as $user_id => $label) {
			$option .= '<option value="' . (int) $user_id . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
		}

		echo $option;
		exit;
	}

	private function canCurrentUserViewDepartmentTasks($department_id, $scope = array())
	{
		$department_id = (int) $department_id;
		if ($department_id <= 0) {
			return false;
		}

		if (empty($scope)) {
			$scope = $this->getTaskDashboardScope();
		}

		if (!empty($scope['is_super_admin'])) {
			return true;
		}

		return in_array($department_id, $scope['view_department_ids'], true);
	}

	private function canCurrentUserAssignDepartmentTasks($department_id, $scope = array())
	{
		$department_id = (int) $department_id;
		if ($department_id <= 0) {
			return false;
		}

		if (empty($scope)) {
			$scope = $this->getTaskDashboardScope();
		}

		if (!empty($scope['is_super_admin'])) {
			return true;
		}

		return in_array($department_id, $scope['assign_department_ids'], true);
	}

	private function getDepartmentAssignmentLeaderNames($department_id)
	{
		$department_id = (int) $department_id;
		if ($department_id <= 0) {
			return '';
		}

		$names = array();
		$query = $this->db->select('b.first_name, b.last_name')
			->from('prestogroup_teams a')
			->join('system_users b', 'a.team_leader=b.user_id', 'left')
			->where('a.department_id', $department_id)
			->where('a.status', 1)
			->order_by('b.first_name', 'asc')
			->order_by('b.last_name', 'asc')
			->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$name = trim($row->first_name . ' ' . $row->last_name);
				if ($name === '') {
					continue;
				}

				$formatted_name = strtoupper($name);
				if (!in_array($formatted_name, $names, true)) {
					$names[] = $formatted_name;
				}
			}
		}

		return implode(', ', $names);
	}

	private function getAssignableUsersForDepartment($department_id, $scope = array())
	{
		$department_id = (int) $department_id;
		if ($department_id <= 0) {
			return array();
		}

		if (empty($scope)) {
			$scope = $this->getTaskDashboardScope();
		}

		$user_list = array();
		$is_super_admin = !empty($scope['is_super_admin']);
		$current_user_id = (int) ($scope['user_id'] ?? 0);

		$this->db->select('
			u.user_id,
			u.department_id,
			u.first_name,
			u.last_name,
			IFNULL(u.user_status, 1) AS user_status,
			COUNT(DISTINCT CASE
				WHEN t.df_id > 0
				AND t.task_status IN (0, 2)
				AND IFNULL(t.on_hold, 0) = 0
				AND IFNULL(df.df_status, 0) = 0
				AND IFNULL(df.on_hold, 0) = 0
				THEN t.df_id
				ELSE NULL
			END) AS active_df_count
		', false);

		if ($is_super_admin) {
			$this->db->from('system_users u');
			$this->db->where('u.department_id', $department_id);
		} else {
			$this->db->from('prestogroup_teams team');
			$this->db->join('presto_team_members member', 'member.team_id = team.team_id', 'inner');
			$this->db->join('system_users u', 'u.user_id = member.employee_id', 'left');
			$this->db->where('team.team_leader', $current_user_id);
			$this->db->where('team.department_id', $department_id);
			$this->db->where('team.status', 1);
		}

		$this->db->join('task_department_wise_scheduling t', 'u.user_id = t.assigned_user', 'left');
		$this->db->join('df_release df', 'df.id = t.df_id', 'left');
		$this->db->group_by('u.user_id, u.department_id, u.first_name, u.last_name, u.user_status');
		$this->db->order_by('CASE WHEN IFNULL(u.user_status, 1) = 1 THEN 0 ELSE 1 END', 'ASC', false);

		if ($department_id === 11) {
			$this->db->order_by('active_df_count', 'asc');
		}

		$this->db->order_by('u.first_name', 'asc');
		$this->db->order_by('u.last_name', 'asc');

		$query = $this->db->get();
		if ($query->num_rows() <= 0) {
			return $user_list;
		}

			foreach ($query->result() as $row) {
				$user_id = (int) $row->user_id;
				if ($user_id <= 0) {
					continue;
				}

			$name = trim($row->first_name . ' ' . $row->last_name);
			if ($name === '') {
				$name = 'USER #' . $user_id;
			}

			$label_meta = array();
			if ($department_id === 11) {
				$label_meta[] = (int) $row->active_df_count . ' Active DFs';
			}
			if ((int) $row->user_status === 0) {
				$label_meta[] = 'Inactive';
			}

			$label = strtoupper($name);
			if (!empty($label_meta)) {
				$label .= ' (' . implode(', ', $label_meta) . ')';
			}

				$user_list[$user_id] = array(
					'user_id' => $user_id,
					'department_id' => (int) $row->department_id,
					'user_status' => (int) $row->user_status,
					'label' => $label
				);
			}

			if (!$is_super_admin && $current_user_id > 0 && !isset($user_list[$current_user_id])) {
				$leader_team_exists = $this->db->select('team_id')
					->from('prestogroup_teams')
					->where('team_leader', $current_user_id)
					->where('department_id', $department_id)
					->where('status', 1)
					->limit(1)
					->get()
					->num_rows() > 0;

				if ($leader_team_exists) {
					$leader_row = $this->db->select('
						u.user_id,
						u.first_name,
						u.last_name,
						IFNULL(u.user_status, 1) AS user_status,
						COUNT(DISTINCT CASE
							WHEN t.df_id > 0
							AND t.task_status IN (0, 2)
							AND IFNULL(t.on_hold, 0) = 0
							AND IFNULL(df.df_status, 0) = 0
							AND IFNULL(df.on_hold, 0) = 0
							THEN t.df_id
							ELSE NULL
						END) AS active_df_count
					', false)
						->from('system_users u')
						->join('task_department_wise_scheduling t', 'u.user_id = t.assigned_user', 'left')
						->join('df_release df', 'df.id = t.df_id', 'left')
						->where('u.user_id', $current_user_id)
						->group_by('u.user_id, u.first_name, u.last_name, u.user_status')
						->limit(1)
						->get()
						->row();

					if (!empty($leader_row) && (int) $leader_row->user_id > 0) {
						$leader_name = trim($leader_row->first_name . ' ' . $leader_row->last_name);
						if ($leader_name === '') {
							$leader_name = 'USER #' . (int) $leader_row->user_id;
						}

						$label_meta = array();
						if ($department_id === 11) {
							$label_meta[] = (int) $leader_row->active_df_count . ' Active DFs';
						}
						if ((int) $leader_row->user_status === 0) {
							$label_meta[] = 'Inactive';
						}

						$label = strtoupper($leader_name);
						if (!empty($label_meta)) {
							$label .= ' (' . implode(', ', $label_meta) . ')';
						}

						$user_list[(int) $leader_row->user_id] = array(
							'user_id' => (int) $leader_row->user_id,
							'department_id' => $department_id,
							'user_status' => (int) $leader_row->user_status,
							'label' => $label
						);
					}
				}
			}

			return array_values($user_list);
		}

	public  function index()
	{

		$this->load->view('master/task');
	}

	public function add_task()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$this->form_validation->set_rules('responsibleperson', 'Responsible Person', 'required|trim');
		$this->form_validation->set_rules('taskname', 'Task Name', 'required|trim');
		$this->form_validation->set_rules('task_type', 'Task Type', 'required|trim');
		$this->form_validation->set_rules('tat', 'TAT', 'required|trim');
		$this->form_validation->set_rules('startfrom', 'Start From', 'required|trim');
		$this->form_validation->set_rules('taskfrequency', 'Task Frequency', 'required|trim');
		$this->form_validation->set_rules('sortorder', 'Sort Order', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/task');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "task_management";
		$query = $this->db->select('task_name, department_id')->from('task_management')->where('department_id',$this->input->post('department'))->where('task_name',$this->input->post('taskname'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Task');
			
		}else{
			if(isset($_REQUEST['isitfinalstep'])){
			$finalstep = 1;
		} else{
			$finalstep = 0;
		}
			$data = array('department_id'=>$this->input->post('department'),
			'task_name'=>$this->input->post('taskname'),
			'task_type'=>$this->input->post('task_type'),
			'responsible_person_id'=>$this->input->post('responsibleperson'),
			'tat'=>$this->input->post('tat'),
			'tat_start_from'=>$this->input->post('startfrom'),
			'task_frequency'=>$this->input->post('taskfrequency'),
			'sortorder'=>$this->input->post('sortorder'),
			'status'=>1,
			'added_on'=>$date,
			'isitfinalstep'=>$finalstep,
			'added_by'=>$user_id);
			
		$result  = $this->db->insert('task_management',$data);
		$id = $this->db->insert_id();	
		if($result)
		{

			if(isset($_REQUEST['definemessage'])){	
					$tags1=count($_REQUEST['definemessage']);
					if($tags1>0)
					{
					$definemessage=$_REQUEST['definemessage'];
					//echo "<pre>"; print_r($_REQUEST['messagefordepartment']); exit;
					$row = $_REQUEST['row'];
					for($x=0;$x<$tags1;$x++){
					if($definemessage[$x]!='')
						{
							$rowid = $row[$x];
							$departmentid = $_REQUEST['messagefordepartment'.$rowid];
							//echo "<pre>"; print_r($departmentid); exit;
							for($y=0; $y<count($departmentid); $y++){
							$data=array('department_id'=>$departmentid[$y],
							'taskid'=>$id,
							'task_message'=>$definemessage[$x],
							'added_on'=>date('Y-m-d H:i:s'),
							'added_by'=>$user_id);
							$this->db->insert('task_related_messages',$data);
							}
							
							
						}
					
					}
					}
					}

			$this->task->get_sorted_tasks();
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Task');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Task');
		}
		}
		
	}
		
	}

	function taskmanagement(){
		$this->load->view('master/tasklist');

	}

	public function tasklistdata()
	{
		$i=1;
		$taskdata= array();
		$this->db->select('a.*, b.department, c.task_name as taskname, f.title, f.first_name, f.last_name')->from('task_management a');
		$this->db->join('departments b','a.department_id=b.department_id','left');
		$this->db->join('task_management c','a.tat_start_from=c.task_id','left');
		$this->db->join('system_users f','a.responsible_person_id=f.user_id','left');
		if($this->uri->segment(3)<>'')
		{
			$this->db->where('b.department_id',$this->uri->segment(3));
		}

		$this->db->order_by('a.sortorder','asc');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){

			$task_type = $row->task_type;
			if($task_type==1){
				$type = "Main Task";
			}else{
				$type = "Sub Task";
			}


			$status = $row->status;
			if($status=='1')
			{
				$sta =  "<a href='".page_url."Task/updatetaskstatus/".$row->task_id."/".$row->status."'><span class='btn btn-success btn-xs'>Active</span></a>";
			}else
			{
				$sta =  "<a href='".page_url."Task/updatetaskstatus/".$row->task_id."/".$row->status."'><span class='btn btn-danger btn-xs'>Not Active</span></a>";
			}	
			$edit = "<a href='".page_url."Task/edit_task/".$row->task_id."'><i class='fa fa-pencil'></i></a>";	

			$message = "<table border='1' style='width:300px; padding:5px 5px 5px 5px;'><tr style='background-color:#fbeeee; text-align:center;'><th style='padding:2px 2px 2px 2px; text-align:center;'>Department</th><th style='padding:2px 2px 2px 2px;  text-align:center;'>Message</th></tr>";
			$q = $this->db->select('a.task_message, b.department, a.department_id')->from('task_related_messages a')->join('departments b','a.department_id=b.department_id','left')->where('a.taskid',$row->task_id)->get();
			foreach($q->result() as $row1){
				if($row1->department_id==0){
					$departmentmsg = "Applicable for All the Departments";
				}else{
					$departmentmsg = $row1->department;
				}
				$message.='<tr>
					<td>'.$departmentmsg.'</td>
					<td>'.$row1->task_message.'</td>
				</tr>';
			}
			$message.='</table>';

			$finalstep = $row->isitfinalstep;
			if($finalstep==1){
				$fstep = "Final Step";
			}else{
				$fstep = "";
			}

			$selectoption = '<input type="number" id="taskid'.$row->task_id.'" class="form-control" value="'.$row->sortorder.'" onkeyup="updateorders('.$row->task_id.');"><span id="success'.$row->task_id.'"></span>';

			if($row->visibleformd==1){
				$visible = "<strong>Yes</strong>";
			}else{
				$visible  = "";
			}

			$tatfrom_design=$this->task->getdepartmentoftaskBYID($row->tat_start_from);
			
			$taskdata[] = array('sr_no'=>$i."<br><br><br>".$edit,
			'department'=>$row->department,
			'task_name'=>$row->task_name,
            'type'=>$type,
            'fstep'=>$fstep,
            'tat'=>$row->tat,
            'responsibleperson'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
            'taskname'=>$row->taskname."<BR/>".$tatfrom_design,
            'task_frequency'=>$row->task_frequency,
            'sortorder'=>$row->sortorder,
            'changeorder'=>$selectoption,
            'status'=>$sta,
            'message'=>$message,
            'visibleformd'=>$visible,
			'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($taskdata),
			"iTotalDisplayRecords" => count($taskdata),
			"aaData"=>$taskdata);
			
		echo json_encode($results);
	}

	public function updatetaskstatus()
	{
		/*************Dynamic information****************/
		$identifier =  $this->uri->segment(3);
		$sval =  $this->uri->segment(4);
		$field_name = "task_id";
		$table = "task_management";
		if($sval=='1')
			{
				$status = 0;
				}else
				{
					$status = 1;
					}
			$data = array('status'=>$status);
			
			$res = $this->db->where('task_id',$identifier);
			$this->db->update('task_management',$data);
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Status successfully updated.</div>');
			redirect(page_url.'Task/taskmanagement');
		}

	public function edit_task(){
		$this->load->view('master/edit_task');
	}

	public function deletetaskmessage(){
		$id = $this->uri->segment(3);
		$uri = $this->uri->segment(4);

		$this->db->where('id',$id);
		$this->db->where('taskid',$uri);
		$this->db->delete('task_related_messages');
		$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You! Record successfully deleted.</div>');
		redirect(page_url.'Task/edit_task/'.$uri);


	}

	public function update_task()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('department', 'Department', 'required|trim');
		$this->form_validation->set_rules('taskname', 'Task Name', 'required|trim');
		$this->form_validation->set_rules('task_type', 'Task Type', 'required|trim');
		$this->form_validation->set_rules('tat', 'TAT', 'required|trim');
		$this->form_validation->set_rules('startfrom', 'Start From', 'required|trim');
		$this->form_validation->set_rules('taskfrequency', 'Task Frequency', 'required|trim');
		$this->form_validation->set_rules('sortorder', 'Sort Order', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/task');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s');
		if(isset($_REQUEST['isitfinalstep'])){
			$finalstep = 1;
		} else{
			$finalstep = 0;
		}
		$table = "task_management";
			$data = array('department_id'=>$this->input->post('department'),
			'task_name'=>$this->input->post('taskname'),
			'task_type'=>$this->input->post('task_type'),
			'responsible_person_id'=>$this->input->post('responsibleperson'),
			'tat'=>$this->input->post('tat'),
			'tat_start_from'=>$this->input->post('startfrom'),
			'task_frequency'=>$this->input->post('taskfrequency'),
			'sortorder'=>$this->input->post('sortorder'),
			'status'=>$this->input->post('status'),
			'added_on'=>$date,
			'isitfinalstep'=>$finalstep,
			'visibleformd'=>$this->input->post('visibleformd'),
			'machinereadyonfloor'=>$this->input->post('machinereadyonfloor'),
			'updatedOn'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id);
			$this->db->where('task_id',$this->uri->segment(3));
			$result  = $this->db->update('task_management',$data);
			
		
		$id = $this->uri->segment(3);	
		if($result)
		{

			if(isset($_REQUEST['definemessage'])){	
					$tags1=count($_REQUEST['definemessage']);
					if($tags1>0)
					{
					$messagefordepartment=$_REQUEST['messagefordepartment'];
					$applicableforall = $_REQUEST['applicableforall'];
					$definemessage=$_REQUEST['definemessage'];
					$recordid = $_REQUEST['recordid'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($definemessage[$x]!='')
						{


							$data=array('department_id'=>$messagefordepartment[$x],
							'taskid'=>$id,
							'task_message'=>$definemessage[$x],
							'added_on'=>date('Y-m-d H:i:s'),
							'added_by'=>$user_id);
							if($recordid[$x]){
								$this->db->where('id',$recordid[$x]);
								$this->db->update('task_related_messages',$data);
							}else{
								$this->db->insert('task_related_messages',$data);
							}
							
							
						}
					$i++;	
					}
					}
					}

			$this->task->get_sorted_tasks();
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
			redirect(page_url.'Task/taskmanagement');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Task');
		}
		
		
	}
		
	}

	public function paymentterms(){
		$this->load->view('master/paymentterms');
	}
	public function paymenttermsmanagement(){
		$this->load->view('master/paymenttermslist');
	}

	public function addpaymentterms()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/paymentterms');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "payment_terms";
		$query = $this->db->select('payment_terms')->from('payment_terms')->where('payment_terms',$this->input->post('paymentterms'))->get();
		$res = $query->result();
		if($res){
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry! This record already exist.</div>');
			redirect(page_url.'Task/paymentterms');
			
		}else{
			
			$data = array('payment_terms'=>$this->input->post('paymentterms'),
			'status'=>0,
			'added_on'=>$date,
			'added_by'=>$user_id);
			
		$result  = $this->db->insert('payment_terms',$data);
		$lastid = $this->db->insert_id();
		$this->sendnotificationforpaymentterms($user_id,$lastid);

		if($result)
		{
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Task/paymentterms');
			
		}else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry,technical error accure.</div><br/>');
			redirect(page_url.'Task/paymentterms');
		}
		}
		
	}
		
	}



	function sendnotificationforpaymentterms($userid,$id){

		$approvallink = 'https://pms.shubhampack.in/index.php/User/paymenttermapprove/'.$id;
		$rejectionlink = 'https://pms.shubhampack.in/index.php/User/paymenttermrejection/'.$id;
		$q = $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$userid)->get();
		foreach($q->result() as $row);
		$personname = ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name));

		$q = $this->db->select('payment_terms')->from('payment_terms')->where('id',$id)->get();
		if($q->num_rows()>0){
		 foreach($q->result() as $row);
		 $payment_terms = "*".$row->payment_terms."*";
		}else{
			$payment_terms = '';
		}
		$message="Dear Sir,

New payment terms for Payment have been created by $personname
$payment_terms 

Please review and either approve ✅ $approvallink 
or 
reject ❌ $rejectionlink 

as needed.

Thank you!

Regards,
Shubham Pack 📦 ";


					$usercontact = '9818505161';
					//$usercontact = '9718991797';
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					//'receiverMobileNo' => '91'.$contactnumber,
					'receiverMobileNo' => '91'.$usercontact,
					'username' => whatsappuser2,
					'password' => whatsapppass2,
					'message'=>strip_tags($message));
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					$result = curl_exec($ch);
						//echo $result; exit;
					if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
					}
					curl_close($ch);
	}

	public function addpaymenttermsmilestone()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('existingpaymentterms', 'Payment Term', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/paymentterms');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		

			if(isset($_REQUEST['paymentpercentage'])){	
					$tags1=count($_REQUEST['paymentpercentage']);
					if($tags1>0)
					{
					$paymentpercentage=$_REQUEST['paymentpercentage'];
					$milestone=$_REQUEST['milestone'];
					$recordid = $_REQUEST['recordid'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($paymentpercentage[$x]!='')
						{

							$data=array('payment_term_id'=>$this->input->post('existingpaymentterms'),
							'payment_percentage'=>$paymentpercentage[$x],
							'milestone'=>$milestone[$x],
							'added_on'=>date('Y-m-d H:i:s'),
							'added_by'=>$user_id);
							if($recordid[$x]){
								$this->db->where('id',$recordid[$x]);
								$this->db->where('payment_term_id',$this->input->post('existingpaymentterms'));
								$this->db->update('payment_terms_milestone',$data);
							}else{
								$this->db->insert('payment_terms_milestone',$data);
							}
							
							
						}
					$i++;	
					}
					}
					}


			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Task/paymenttermsmanagement');
			
		
		}
		
	}
		
	public function paymentlist()
	{
		$i=1;
		$taskdata= array();
		$this->db->select('a.id, a.payment_terms, a.sortorder')->from('payment_terms a');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){

				
			$edit = "<a href='".page_url."Task/edit_paymentterms/".$row->id."'><i class='fa fa-pencil'></i></a>";	

			$message = "<table border='1' style='width:300px;'><tr style='background-color:#fbeeee; text-align:center;'><th style='padding:2px 2px 2px 2px; text-align:center;'>Percentage</th><th style='padding:2px 2px 2px 2px;  text-align:center;'>Milestone</th></tr>";
			$q = $this->db->select('a.payment_percentage, a.milestone, b.task_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->where('a.payment_term_id',$row->id)->get();
			foreach($q->result() as $row1){
				$message.='<tr>
					<td>'.$row1->payment_percentage.'</td>
					<td>'.$row1->task_name.'</td>
				</tr>';
			}
			$message.='</table>';
			$message.='<br><a href="'.page_url.'Task/paymentterms/'.$row->id.'"><span class="btn btn-primary btn-xs">Set Milestone</span></a>';

			$taskdata[] = array('sr_no'=>$i,
			'paymentterm'=>$row->payment_terms,
			'milestone'=>$message,
			'sortorder'=>$row->sortorder,
           	'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($taskdata),
			"iTotalDisplayRecords" => count($taskdata),
			"aaData"=>$taskdata);
			
		echo json_encode($results);
	}

	public function deletemilstone(){
		$id = $this->uri->segment(3);
		$uri = $this->uri->segment(4);

		$this->db->where('id',$id);
		$this->db->where('payment_term_id',$uri);
		$this->db->delete('payment_terms_milestone');
		$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You! Record successfully deleted.</div>');
		redirect(page_url.'Task/paymentterms/'.$uri);


	}

	public function poreceived(){
		$this->load->view('master/poreceived');
	}

		public function directpaymenttermmaster()
		{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('paymentterms', 'Payment Term', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/poreceived');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		
		$data = array('payment_terms'=>$this->input->post('paymentterms'),
			'status'=>1,
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->insert('payment_terms',$data);
		$id = $this->db->insert_id();
			if(isset($_REQUEST['paymentpercentage'])){	
					$tags1=count($_REQUEST['paymentpercentage']);
					if($tags1>0)
					{
					$paymentpercentage=$_REQUEST['paymentpercentage'];
					$milestone=$_REQUEST['milestone'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($paymentpercentage[$x]!='')
						{

							$data=array('payment_term_id'=>$id,
							'payment_percentage'=>$paymentpercentage[$x],
							'milestone'=>$milestone[$x],
							'added_on'=>date('Y-m-d H:i:s'),
							'added_by'=>$user_id);
							$this->db->insert('payment_terms_milestone',$data);
							
							
						}
					$i++;	
					}
					}
					}


			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Task/poreceived');
			
		
		}
		
	}


	public function addporeceivedinfo()
		{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('existingpaymentterms', 'Payment Term', 'required|trim');
		$this->form_validation->set_rules('companyname', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('pono', 'Po Number', 'required|trim');
		$this->form_validation->set_rules('podate', 'PO Date', 'required|trim');
		$this->form_validation->set_rules('order_value', 'Order Value', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/poreceived');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$photo=$_FILES['attachpo']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$poattachment=time().'.'.$cat_image;
				move_uploaded_file($_FILES['attachpo']["tmp_name"],UPLOADPATH.'Taskdocument/' . $poattachment);
			}else
			{
				$poattachment="";
				}

			$data = array('company_name'=>$this->input->post('companyname'),
			'pono'=>$this->input->post('pono'),
			'podate'=>date('Y-m-d',strtotime($this->input->post('podate'))),
			'po_attachment'=>$poattachment,
			'payment_term'=>$this->input->post('existingpaymentterms'),
			'added_on'=>$date,
			'order_value'=>$this->input->post('order_value'),
			'customer_currency'=>$this->input->post('currency'),
			'amount_in_customer_currency'=>$this->input->post('ordervalueincustomercurrency'),
			'added_by'=>$user_id);
			$this->db->insert('poreceived',$data);
			$polastid = $this->db->insert_id();

			$nexttaskid = 0;
			$departmentid = 0;
			$enddate = date('Y-m-d');

			/*Notification of PO Release*/
			$this->task->notificationofprocessdone(1);
			/*Notification of PO Release*/



			/*PO Release entry in Task scheduling table*/
			$data1 = array('df_id'=>0,
				'taskid'=>1,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d'),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$polastid,
				'task_status'=>1,
				'remarks'=>'',
				'task_completed_on'=>date('Y-m-d H:i:s'),
				'task_completed_by'=>$user_id,
				'assigned_user'=>$user_id, 
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'),
				'userid'=>$user_id);
			   $this->db->insert('task_department_wise_scheduling',$data1);
				$q = $this->db->select('task_id, department_id, tat')->from('task_management')->where('task_id',3)->order_by('sortorder','ASC')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $row1);

				$nexttaskid = $row1->task_id;
				$departmentid = $row1->department_id;

						$startdate = date('Y-m-d');
						$enddate = date('Y-m-d', strtotime('+'.$row1->tat.' days', strtotime($startdate)));

						$skipped_dates = $this->task->SKIP_holidays($startdate, $enddate);

						$startdate = $skipped_dates['start_date'];
						$enddate = $skipped_dates['end_date'];

				//echo $startdate."<br/>".$enddate; exit;
				$data1 = array('df_id'=>0,
				'taskid'=>$nexttaskid,
				'department_id'=>$departmentid,
				'start_date'=>$startdate,
				'end_date'=>$enddate,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'assigned_user'=>$user_id, 
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'),
				'po_id'=>$polastid);

			$this->db->insert('task_department_wise_scheduling',$data1);
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Task/poreceived');
				
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry, PI has not being uploaded.</div>');
			redirect(page_url.'Task/poreceived');
			}
			
			
		
		}
		
	}
	public function receivedpolist(){
		$this->load->view('master/receivedpolist', array(
			'report_title' => 'Received PO Report',
			'received_po_data_action' => 'receivedpodata',
			'own_orders_only' => false
		));
	}

	public function myreceivedpolist(){
		$this->load->view('master/receivedpolist', array(
			'report_title' => 'My Received PO Report',
			'received_po_data_action' => 'myreceivedpodata',
			'own_orders_only' => true
		));
	}

	public function myreceivedpodata(){
		// The ownership scope is set on the server. It cannot be changed with a
		// query-string or URI parameter.
		$this->receivedPoOwnOrdersOnly = true;
		return $this->receivedpodata();
	}

	public function receivedpodata()
{
    $i = 1;
    $taskdata = array();

    $startdate = $this->uri->segment(3);
    $enddate   = $this->uri->segment(4);

    $this->db->select('
        a.*,
        b.payment_terms,
        c.title,
        c.first_name,
        c.last_name,
        d.year,
        e.name as brand_name
    ');
    $this->db->from('poreceived a');
    $this->db->join('payment_terms b', 'a.payment_term = b.id', 'left');
    $this->db->join('system_users c', 'a.added_by = c.user_id', 'left');
    $this->db->join('financialyear d', 'd.id = a.financialyear', 'left');
    $this->db->join('company_brand e', 'e.id = a.brand_tag', 'left');

    if ($this->receivedPoOwnOrdersOnly) {
        $loggedInUserId = isset($_SESSION['logged_in']['user_id'])
            ? (int) $_SESSION['logged_in']['user_id']
            : 0;
        $this->db->where('a.added_by', $loggedInUserId);
    }

    if (!empty($startdate) && !empty($enddate)) {
        $start_date = base64_decode($startdate);
        $end_date   = base64_decode($enddate);

        if (!empty($start_date) && !empty($end_date)) {
            $st = date('Y-m-d', strtotime($start_date)) . " 00:00:00";
            $et = date('Y-m-d', strtotime($end_date)) . " 23:59:59";

            $this->db->where('a.added_on >=', $st);
            $this->db->where('a.added_on <=', $et);
        }
    }

    $this->db->where('a.basic_machine', 0);
    $this->db->order_by('a.id', 'desc');

    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {

            $poId = (int)$row->id;

            $companyName = !empty($row->company_name) ? ucwords(strtolower($row->company_name)) : '-';
            $financialYear = !empty($row->year) ? $row->year : '-';
            $poNo = !empty($row->pono) ? $row->pono : '-';
            $dfNo = !empty($row->df_number) ? strtoupper($row->df_number) : '-';

            $poDate = '-';
            if (!empty($row->podate) && $row->podate != '0000-00-00') {
                $poDate = date('d-m-Y', strtotime($row->podate));
            }

            $title = !empty($row->title) ? $row->title : '';
            $firstName = !empty($row->first_name) ? $row->first_name : '';
            $lastName = !empty($row->last_name) ? $row->last_name : '';

            $marketingPerson = trim($title . ' ' . $firstName . ' ' . $lastName);
            $marketingPerson = !empty($marketingPerson) ? ucwords(strtolower($marketingPerson)) : '-';

            $formattedNumber = '0';
            if (!empty($row->order_value)) {
                if (method_exists($this, 'formatIndianNumber')) {
                    $formattedNumber = $this->formatIndianNumber($row->order_value);
                } else {
                    $formattedNumber = number_format((float)$row->order_value, 2);
                }
            }

            $orderValue = "<span style='font-weight:700; white-space:nowrap;'><i class='fa fa-inr'></i> " . $formattedNumber . "</span>";

            $edit = "<a href='" . page_url . "Task/editpo/" . $poId . "' class='btn btn-primary btn-xs' style='border-radius:20px; font-weight:700;'>
                        <i class='fa fa-pencil'></i> Edit
                    </a>";

            $poAttachment = "<span class='label label-default'>No File</span>";
            if (!empty($row->po_attachment)) {
                $poAttachment = "<a href='" . sfdocument . "Taskdocument/" . $row->po_attachment . "' download class='btn btn-info btn-xs' style='border-radius:20px; font-weight:700;'>
                                    <i class='fa fa-download'></i> PO
                                </a>";
            }

            $leadId = !empty($row->lead_id) ? $row->lead_id : 0;

            $poIntroEmail = "<a href='" . page_url . "Master/User_management/posendintroemail/15/" . $poId . "/" . $leadId . "' class='btn btn-primary btn-xs' style='border-radius:20px; font-weight:700;'>
                                Send Intro <i class='fa fa-envelope'></i>
                            </a>";

            if (!empty($row->brand_tag) && (int)$row->brand_tag != 0 && !empty($row->brand_name)) {
                $brandname = "<span class='label label-success' style='font-size:11px; padding:6px 10px; border-radius:20px; display:inline-block;'>" . ucwords(strtolower($row->brand_name)) . "</span>";
            } else {
                $brandname = "<button type='button' class='btn btn-success btn-xs' onclick='assignbrand(" . $poId . ", \"\")' style='border-radius:20px; font-weight:700;'>
                                <i class='fa fa-plus'></i> Add Brand
                              </button>";
            }

            $message = "<div style='min-width:420px;'>";
            $message .= "<h5 style='font-weight:800; text-align:center; margin:5px 0 10px;'>Payment Milestone</h5>";
            $message .= "<table border='1' style='width:100%; text-align:center; border-collapse:collapse; font-size:12px;'>";
            $message .= "<tr style='background-color:#fbeeee; text-align:center;'>
                            <th style='padding:5px; text-align:center; font-weight:bold;'>Percentage (%)</th>
                            <th style='padding:5px; text-align:center; font-weight:bold;'>Milestone</th>
                         </tr>";

            $milestone_q = $this->db
                ->select('a.payment_percentage, a.milestone, b.task_name')
                ->from('payment_terms_milestone a')
                ->join('task_management b', 'a.milestone = b.task_id', 'left')
                ->where('a.payment_term_id', $row->payment_term)
                ->order_by('a.id', 'asc')
                ->get();

            if ($milestone_q->num_rows() > 0) {
                foreach ($milestone_q->result() as $row1) {
                    $percentage = !empty($row1->payment_percentage) ? $row1->payment_percentage : '0';
                    $taskName = !empty($row1->task_name) ? $row1->task_name : '-';

                    $message .= "<tr>
                                    <td style='padding:5px;'>" . $percentage . "%</td>
                                    <td style='padding:5px;'>" . $taskName . "</td>
                                 </tr>";
                }
            } else {
                $message .= "<tr>
                                <td colspan='2' style='padding:8px; color:#999;'>No milestone found</td>
                             </tr>";
            }

            $message .= "</table>";
            $message .= "</div>";

            $paymentTerms = !empty($row->payment_terms) ? $row->payment_terms : '-';
            $milestoneHtml = "<strong>" . $paymentTerms . "</strong><br>" . $message;

            $canceledorder = "<a href='javascript:void(0);' onclick='confirmCancel(" . $poId . ")' class='btn btn-danger btn-xs' style='border-radius:20px; font-weight:700;'>
                                <i class='fa fa-ban'></i> Cancel
                              </a>";

            $spareQuotation = '';
            if ($this->receivedPoOwnOrdersOnly) {
                $spareQuotation = "<a href='" . page_url . "Order_spare_quotation/index/" . $poId . "' class='btn btn-warning btn-xs' style='border-radius:20px; font-weight:700;'>
                                      <i class='fa fa-cogs'></i> Spare Quote
                                   </a>";
            }

            if (isset($row->status) && (int)$row->status == 2) {
                $canceledorder = "<span class='label label-danger' style='font-size:11px; padding:6px 10px; border-radius:20px;'>Cancelled</span>";
            }

            $taskdata[] = array(
                'sr_no'           => $i,
                'intro_email'     => $poIntroEmail,
                'df_no'           => $dfNo,
                'financialyear'   => $financialYear,
                'company_name'    => $companyName,
                'pono'            => $poNo,
                'podate'          => $poDate,
                'ordervalue'      => $orderValue,
                'marketingperson' => $marketingPerson,
                'po_attachment'   => $poAttachment,
                'brandtag'        => $brandname,
                'milestone'       => $milestoneHtml,
                'spare_quotation' => $spareQuotation,
                'edit'            => $edit,
                'canceledorder'   => $canceledorder
            );

            $i++;
        }
    }

    $results = array(
        "draw" => intval($this->input->get('draw')),
        "recordsTotal" => count($taskdata),
        "recordsFiltered" => count($taskdata),
        "sEcho" => 1,
        "iTotalRecords" => count($taskdata),
        "iTotalDisplayRecords" => count($taskdata),
        "aaData" => $taskdata,
        "data" => $taskdata
    );

    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}

	public function basicmachinelistdata(){
		$this->load->view('master/basicmachine-order-data.php');
	}	

	public function basicmachinedatalistinfo()
	{
		$i=1;
		$taskdata= array();
		$startdate = $this->uri->segment(3);
		$enddate = $this->uri->segment(4);
		$this->db->select('a.*, b.payment_terms, c.title, c.first_name, c.last_name, d.year')->from('poreceived a')->join('payment_terms b','a.payment_term=b.id','left')->join('system_users c','a.added_by=c.user_id','left')->join('financialyear d','d.id=a.financialyear','left')->order_by('a.id','desc');
		$this->db->where('basic_machine',1);
		if($startdate<>''){
			$start_date = base64_decode($startdate);
			$end_date = base64_decode($enddate);
			$st = $start_date." 00:00:00";
			$et = $end_date = " 23:59:59";
			$this->db->where('a.added_on BETWEEN "'.$st. '" and "'.$et.'"');
		}
		//$this->db->where('a.basic_machine',0);
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){

				
			$edit = "<a href='".page_url."Task/editpo/".$row->id."'><i class='fa fa-pencil'></i></a>";	

			$message = "<center><h5>Payment Milestone</h5><hr><table border='1' style='width:500px; text-align:center'><tr style='background-color:#fbeeee; text-align:center;'><th style='padding:2px 2px 2px 2px; text-align:center; font-weight:bold;'><b>Percentage(%)</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold;'><b>Milestone</b></th></tr>";
			$q = $this->db->select('a.payment_percentage, a.milestone, b.task_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->where('a.payment_term_id',$row->payment_term)->get();
			foreach($q->result() as $row1){
				$message.='<tr>
					<td>'.$row1->payment_percentage.'</td>
					<td>'.$row1->task_name.'</td>
				</tr>';
			}
			$message.='</table></center>';
			//$message.='<br><a href="'.page_url.'Task/paymentterms/'.$row->id.'"><span class="btn btn-primary btn-xs">Set Milestone</span></a>';
			if($row->brand_tag<>0){
				$q = $this->db->select('name')->from('company_brand')->where('id',$row->brand_tag)->get();
				if($q->num_rows()>0){
					foreach($q->result() as $brand);
					$brandname = ucwords(strtolower($brand->name));
				}else{
					$brandname ='';
				}

			}else{
				$brandname= '<span class="btn btn-success btn-xs" onclick="assignbrand('.$row->id.');">Add Brand</span>';
			}
			$po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
			 $formattedNumber = $this->formatIndianNumber($row->order_value);

			 $canceledorder = '<a href="javascript:void(0);" onclick="confirmCancel('.$row->id.')" class="btn btn-danger btn-xs">Click to Cancel</a>';

			 $po_intro_email='<a href="'.page_url.'Master/User_management/posendintroemail/15/'.$row->id.'/'.$row->lead_id.'"><span class="btn btn-primary btn-xs">Send Intro Email <i class="fa fa-envelope"></i></span></a>';

			$taskdata[] = array('sr_no'=>$i,
			'company_name'=>$row->company_name,
			'intro_email'=>$po_intro_email,
			'financialyear'=>$row->year,
			'pono'=>$row->pono,
			'df_no'=>$row->df_number,
			'podate'=>date('d-m-Y',strtotime($row->podate)),
			'po_attachment'=>$po_attachment,
			'milestone'=>$row->payment_terms."<br>".$message,
			'ordervalue'=>"<i class='fa fa-inr'></i> ".$formattedNumber,
			'marketingperson'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
			'canceledorder'=>$canceledorder,
           	'edit'=>$edit,'brandtag'=>$brandname);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($taskdata),
			"iTotalDisplayRecords" => count($taskdata),
			"aaData"=>$taskdata);
			
		echo json_encode($results);
	}



public function basicmachinedatalist()
	{
		$i=1;
		$taskdata= array();
		$startdate = $this->uri->segment(3);
		$enddate = $this->uri->segment(4);
		$this->db->select('a.*, c.title, c.first_name, c.last_name, d.year, p.model_no as mno, p.type, p.motion')->from('poreceived a')->join('system_users c','a.added_by=c.user_id','left')->join('financialyear d','d.id=a.financialyear','left')->join('basic_machine_model_for_pms p','a.machine_model=p.id','left')->order_by('a.id','desc');
		if($startdate<>''){
			$start_date = base64_decode($startdate);
			$end_date = base64_decode($enddate);
			$st = $start_date." 00:00:00";
			$et = $end_date = " 23:59:59";
			$this->db->where('a.added_on BETWEEN "'.$st. '" and "'.$et.'"');
		}
		$this->db->where('a.basic_machine',1);
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){

				
			$edit = "<a href='".page_url."Task/edit_basicmachine_record/".$row->id."'><i class='fa fa-pencil'></i></a>";	

		
			 $formattedNumber = $this->formatIndianNumber($row->order_value);
			$taskdata[] = array('sr_no'=>$i,
			'financialyear'=>$row->year,
			'speed'=>$row->speed,
			'productname'=>$row->productname,
			'reference_df'=>$row->reference_df,
			'addedondate'=>date('d-m-Y',strtotime($row->machine_punch_date)),
			'machinename'=>$row->machinetype,
			'added_by'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
			'modelno'=>$row->mno." - ".$row->type." - ".$row->motion,
			'action'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($taskdata),
			"iTotalDisplayRecords" => count($taskdata),
			"aaData"=>$taskdata);
			
		echo json_encode($results);
	}


	function formatIndianNumber($number) {
        $number_parts = explode(".", $number);
        $integer_part = $number_parts[0];
        $decimal_part = isset($number_parts[1]) ? '.' . $number_parts[1] : '';

        // Handle negative numbers
        $negative = '';
        if ($integer_part[0] == '-') {
        $negative = '-';
        $integer_part = substr($integer_part, 1);
        }

        // Split the integer part into 3 digits for the last group and 2 digits thereafter
        $lastThree = substr($integer_part, -3);
        $restUnits = substr($integer_part, 0, -3);

        if ($restUnits != '') {
        $lastThree = ',' . $lastThree;
        }

        $result = $negative . preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits) . $lastThree . $decimal_part;
        return $result;
        }

	public function edit_paymentterms(){
		$this->load->view('master/edit_payment_terms');
	}

	public function update_payment_terms()
		{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('paymentterms', 'Payment Term', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_payment_terms');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		
		$data = array('payment_terms'=>$this->input->post('paymentterms'),
			'status'=>1,
			'sortorder'=>$this->input->post('sortnumber'),
			'added_on'=>$date,
			'added_by'=>$user_id);
		$this->db->where('id',$this->uri->segment(3));
		$this->db->update('payment_terms',$data);
		$id = $this->uri->segment(3);
			if(isset($_REQUEST['paymentpercentage'])){	
					$tags1=count($_REQUEST['paymentpercentage']);
					if($tags1>0)
					{
					$paymentpercentage=$_REQUEST['paymentpercentage'];
					$milestone=$_REQUEST['milestone'];
					$recordid = $_REQUEST['recordid'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($paymentpercentage[$x]!='')
						{

							$data=array('payment_term_id'=>$id,
							'payment_percentage'=>$paymentpercentage[$x],
							'milestone'=>$milestone[$x],
							'added_on'=>date('Y-m-d H:i:s'),
							'added_by'=>$user_id);
							if($recordid[$x]){
								$this->db->where('id',$recordid[$x]);
								$this->db->update('payment_terms_milestone',$data);

							}else{
								$this->db->insert('payment_terms_milestone',$data);
							}
							
							
							
						}
					$i++;	
					}
					}
					}


			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Task/paymenttermsmanagement');
			
		
		}
		
	}

public function dfrelease()
{
	if (isset($_REQUEST['dfno'])) {
		$user_id = $this->session->userdata['logged_in']['user_id'];
		date_default_timezone_set("Asia/Kolkata");
		$date = date('Y-m-d H:i:s');
		$photo = $_FILES['uploaddf']['name'];
		if ($photo <> '') {
			$image1 = explode('.', $photo);
			$cat_image = end($image1);
			$dfattachment = time() . '.' . $cat_image;
			move_uploaded_file($_FILES['uploaddf']["tmp_name"], UPLOADPATH . 'Taskdocument/dfattachment/' . $dfattachment);
		} else {
			$dfattachment = "";
		}

		$data = array(
			'df_upload' => $dfattachment,
			'df_no' => $this->input->post('dfno'),
			'df_description' => $this->input->post('df_description'),
			'df_status' => 0,
			'added_on' => $date,
			'added_by' => $user_id
		);
		$dfn = $this->input->post('dfno');
		// **Get PO ID once to use throughout the function**
		$poid = $this->input->post('poid');

		$this->db->insert('df_release', $data);
		$dfid = $this->db->insert_id();

		$this->task->triggernotificationondfrelease($dfn, $dfid);

		/*Update PO and Task Scheduling data*/
		$data1 = array(
			'df_id' => $dfid,
			'task_status' => 1,
			'task_completed_on' => date('Y-m-d H:i:s'),
			'task_completed_by' => $user_id,
			'userid' => $user_id
		);
		$this->db->where('id', $this->input->post('dfrecordid'));
		$this->db->update('task_department_wise_scheduling', $data1);

		$data11 = array('df_id' => $dfid);
		$this->db->where('po_id', $poid);
		$this->db->update('task_department_wise_scheduling', $data11);

		$data2 = array('df_id' => $dfid);
		$this->db->where('id', $poid);
		$this->db->update('poreceived', $data2);


		/*Task Scheduling*/
		$q = $this->db->select('taskid')->from('task_department_wise_scheduling')->where('df_id', $dfid)->get();
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $rowsss) {
				$taskids[] = $rowsss->taskid;
			}
		}

		$q = $this->db->select('task_id, department_id, tat, tat_start_from')->from('task_management')->where_not_in('task_id', $taskids, false)->where('status', 1)->order_by('system_created_sort_order', 'asc')->get();
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				$taskid = $row->task_id;
				$department_id = $row->department_id;
				$tat = $row->tat;
				$startfrom = $row->tat_start_from;

				/*Check start from date*/
				$q1 = $this->db->select('end_date')->from('task_department_wise_scheduling')->where('df_id', $dfid)->where('taskid', $startfrom)->get();
				if ($q1->num_rows() > 0) {
					foreach ($q1->result() as $row1);
					if($startfrom!=2)
					{
					$startdate = $row1->end_date;
					$startdate = date('Y-m-d', strtotime('+1 days', strtotime($startdate)));
					}else
					{
						$startdate=$startdate = date('Y-m-d', strtotime('+1 day'));
					}

					$holidaysdays = 0;
					$scheduled_dates = $this->buildDfTaskScheduleWindow($row->task_id, $startdate, $tat, true);
					$startdate = $scheduled_dates['start_date'];
					$enddate = $scheduled_dates['end_date'];
					
					/*Check Holiday*/
					$data = array(
						'df_id' => $dfid,
						'taskid' => $row->task_id,
						'department_id' => $row->department_id,
						'start_date' => $startdate,
						'end_date' => $enddate,
						'added_on' => date('Y-m-d H:i:s'),
						'added_by' => $user_id,
						'po_id' => $poid,
						'task_status' => 0,
						'holidayscount' => $holidaysdays,
						'userid' => $user_id
					);

					// ## START VALIDATION ##
					// Check for an existing record with the same df_no, po_id, and task_id
					$this->db->select('tdws.id');
					$this->db->from('task_department_wise_scheduling as tdws');
					$this->db->join('df_release as dfr', 'tdws.df_id = dfr.id');
					$this->db->where('dfr.df_no', $dfn);
					$this->db->where('tdws.po_id', $poid);
					$this->db->where('tdws.taskid', $row->task_id);
					$check_query = $this->db->get();

					// Only insert if the check_query finds no existing record
					if ($check_query->num_rows() == 0) {
						$this->db->insert('task_department_wise_scheduling', $data);
						$this->checkpaymentstageandmark($row->task_id, $dfid, $poid);
					}
					// ## END VALIDATION ##
				}
			}
		}

		/*114 and PO NO NEED TO CHECK */

		// $Q = $this->db->select('id')->from('task_department_wise_scheduling')->where('po_id',$poid)->where('taskid',114)->where('df_id',0)->get();
		// if($Q->num_rows()>0){

		// 	foreach($Q->result() as $existingdfrecord){
		// 			$this->db->where('id',$existingdfrecord->id);
		// 			$this->db->delete('task_department_wise_scheduling');
		// 	}
			
		// }



		$departid = 9;
		$this->task->marketingtaskautoassign($dfid, $user_id, $departid);

		$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url . 'Task/dfrelease');
	} else {
		$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry!, DF Information is missing. Please try again.</div>');
		redirect(page_url . 'Dashboard');
	}

	// This part of your original code was unreachable, as the code always redirects before this point.
	// I've removed it for clarity, but you can add it back if your logic requires it.
	// $this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry!, DF Information is missing. Please try again.</div>');
	// redirect(page_url.'Dashboard');
}

	public function dfreleaseoldfile28july2025()
		{
		
		if(isset($_REQUEST['dfno'])){
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$photo=$_FILES['uploaddf']['name'];
		if($photo<>'')
		{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$dfattachment=time().'.'.$cat_image;
			move_uploaded_file($_FILES['uploaddf']["tmp_name"],UPLOADPATH.'Taskdocument/dfattachment/' . $dfattachment);
		}else
		{
			$dfattachment="";
			}

		$data = array('df_upload'=>$dfattachment,
			'df_no'=>$this->input->post('dfno'),
			'df_description'=>$this->input->post('df_description'),
			'df_status'=>0,
			'added_on'=>$date,
			'added_by'=>$user_id);
		$dfn = $this->input->post('dfno');
		
		$this->db->insert('df_release',$data);
		$dfid = $this->db->insert_id();

		$this->task->triggernotificationondfrelease($dfn,$dfid);

		/*Update PO and Task Scheduling data*/
		$data1 = array('df_id'=>$dfid,
			'task_status'=>1,
			'task_completed_on'=>date('Y-m-d H:i:s'),
			'task_completed_by'=>$user_id,
			'userid'=>$user_id);
		$this->db->where('id',$this->input->post('dfrecordid'));
		$this->db->update('task_department_wise_scheduling',$data1);

		$data11 = array('df_id'=>$dfid);
		$this->db->where('po_id',$this->input->post('poid'));
		$this->db->update('task_department_wise_scheduling',$data11);

		$data2 = array('df_id'=>$dfid);
		$this->db->where('id',$this->input->post('poid'));
		$this->db->update('poreceived',$data2);

		
		/*Task Scheduling*/

		$q = $this->db->select('taskid')->from('task_department_wise_scheduling')->where('df_id',$dfid)->get();
		if($q->num_rows()>0){
			foreach($q->result() as $rowsss){
				$taskids[] = $rowsss->taskid;
			}
		}
		$q = $this->db->select('task_id, department_id, tat, tat_start_from')->from('task_management')->where_not_in('task_id',$taskids,false)->where('status',1)->order_by('system_created_sort_order','asc')->get();
		if($q->num_rows()>0){
			foreach($q->result() as $row){
					$taskid = $row->task_id;
					$department_id = $row->department_id;
					$tat = $row->tat;
					$startfrom = $row->tat_start_from;

					/*Check start from date*/
					$q1 = $this->db->select('end_date')->from('task_department_wise_scheduling')->where('df_id',$dfid)->where('taskid',$startfrom)->get();
					if($q1->num_rows()>0){
						foreach($q1->result() as $row1);
						$startdate = $row1->end_date;
						$startdate = date('Y-m-d', strtotime('+1 days', strtotime($startdate)));
						
						$holidaysdays = 0;
						$enddate = date('Y-m-d', strtotime('+'.$tat.' days', strtotime($startdate)));
						
						$skipped_dates = $this->task->SKIP_holidays($startdate, $enddate);

						$startdate = $skipped_dates['start_date'];
						$enddate = $skipped_dates['end_date'];
						$poiiiddd = $this->input->post('poid');
					
					/*Check Holiday*/
					$data = array('df_id'=>$dfid,
						'taskid'=>$row->task_id,
						'department_id'=>$row->department_id,
						'start_date'=>$startdate,
						'end_date'=>$enddate,
						'added_on'=>date('Y-m-d H:i:s'),
						'added_by'=>$user_id,
						'po_id'=>$this->input->post('poid'),
						'task_status'=>0,
						'holidayscount'=>$holidaysdays,
						'userid'=>$user_id);

					$qqqq = $this->db->select('id')->from('task_department_wise_scheduling')->where('taskid',$row->task_id)->where('df_id',$dfid)->get();
					if($qqqq->num_rows()>0){
						
					}else{
					$this->db->insert('task_department_wise_scheduling',$data);
					$this->checkpaymentstageandmark($row->task_id,$dfid, $poiiiddd);
					}

					
					}

					


			}
		}
		$departid = 9;
		$this->task->marketingtaskautoassign($dfid,$user_id,$departid);

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Task/dfrelease');
		
	}else{
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Dashboard');
	}	

	$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry!, DF Information is missing. Please try again.</div>');
			redirect(page_url.'Dashboard');
		
	}


public function dfreleasebynewfeature()
{
    // 1. Basic Validation
    if (!isset($_REQUEST['dfno'])) {
        $this->session->set_flashdata('message', '<div class="alert alert-danger">Sorry!, DF Information is missing.</div>');
        redirect(page_url . 'Dashboard');
    }

    // 2. Setup Variables
    $user_id          = $this->session->userdata['logged_in']['user_id'];
    date_default_timezone_set("Asia/Kolkata");
    $current_date     = date('Y-m-d H:i:s');
    
    $df_release_type  = $this->input->post('df_release'); // 1=Normal, 3=Customized
    $poid             = $this->input->post('poid');
    $dfn              = $this->input->post('dfno');
    $df_record_id     = $this->input->post('dfrecordid'); // The Task ID triggering this

    // 3. Handle File Upload
    $photo = $_FILES['uploaddf']['name'];
    if ($photo <> '') {
        $ext = pathinfo($photo, PATHINFO_EXTENSION);
        $dfattachment = time() . '.' . $ext;
        move_uploaded_file($_FILES['uploaddf']["tmp_name"], UPLOADPATH . 'Taskdocument/dfattachment/' . $dfattachment);
    } else {
        $dfattachment = "";
    }

    // 4. Insert into df_release table
    $data = array(
        'df_upload'      => $dfattachment,
        'df_no'          => $dfn,
        'df_description' => $this->input->post('df_description'),
        'df_type'        => $df_release_type,
        'df_status'      => 0,
        'added_on'       => $current_date,
        'added_by'       => $user_id
    );

    $this->db->insert('df_release', $data);
    $dfid = $this->db->insert_id(); 

    // 5. Trigger Notification
    $this->task->triggernotificationondfrelease($dfn, $dfid);

    // 6. Mark the CURRENT TASK (Segment 3) as COMPLETED & Link to DF
    if (!empty($df_record_id)) {
        $current_task_update = array(
            'df_id'             => $dfid,
            'task_status'       => 1,              
            'task_completed_on' => $current_date,  
            'task_completed_by' => $user_id,       
            'userid'            => $user_id
        );
        
        $this->db->where('id', $df_record_id);
        $this->db->update('task_department_wise_scheduling', $current_task_update);
    }

    // 7. Update PO Received table
    $this->db->where('id', $poid)->update('poreceived', array('df_id' => $dfid));
    $this->db->where('po_id', $poid)->update('task_department_wise_scheduling', array('df_id' => $dfid));


    // =================================================================================
    // LOGIC SPLIT: CUSTOMIZED (3) vs NORMAL (1, 2, 4)
    // =================================================================================

    if ($df_release_type == '3') {
        // --- CUSTOMIZED LOGIC ---

        // A. Prepare Department Map
        $dept_map = array();
        $posted_dept_ids = $this->input->post('departmentid');
        $posted_starts   = $this->input->post('start_date');
        $posted_ends     = $this->input->post('end_date');

        if(!empty($posted_dept_ids)){
            for($i = 0; $i < count($posted_dept_ids); $i++){
                if(!empty($posted_starts[$i]) && !empty($posted_ends[$i])){
                    $dept_map[$posted_dept_ids[$i]] = array(
                        'start' => $posted_starts[$i],
                        'end'   => $posted_ends[$i]
                    );
                }
            }
        }

        // B. [OPTIMIZATION] Get IDs of tasks ALREADY linked to this DF (e.g., Task from Step 6)
        $existing_task_ids = array();
        $q_check = $this->db->select('taskid')->from('task_department_wise_scheduling')->where('df_id', $dfid)->get();
        if ($q_check->num_rows() > 0) {
            foreach ($q_check->result() as $r) {
                $existing_task_ids[] = $r->taskid;
            }
        }

        // C. Get Tasks to Schedule (Excluding duplicates and Task ID 2)
        $this->db->select('task_id, department_id');
        $this->db->from('task_management');
        $this->db->where('status', 1); 
        $this->db->where('task_id !=', 2); // Never schedule DF Release itself
        
        // EXCLUDE already existing tasks for this DF
        if (!empty($existing_task_ids)) {
            $this->db->where_not_in('task_id', $existing_task_ids);
        }

        $all_tasks = $this->db->get()->result();

        // D. Loop and Insert
        foreach ($all_tasks as $task_row) {
            // Check if this task's department has user-submitted dates
            if (array_key_exists($task_row->department_id, $dept_map)) {
                $schedule_start_date = $dept_map[$task_row->department_id]['start'];
                $schedule_end_date = $dept_map[$task_row->department_id]['end'];

                if ((int) $task_row->task_id === 4) {
                    $friday_window = $this->buildDfTaskScheduleWindow($task_row->task_id, $schedule_start_date, 0, true);
                    $schedule_start_date = $friday_window['start_date'];
                    $schedule_end_date = $friday_window['end_date'];
                }
                
                $schedule_data = array(
                    'df_id'         => $dfid,
                    'taskid'        => $task_row->task_id,
                    'department_id' => $task_row->department_id,
                    'start_date'    => $schedule_start_date,
                    'end_date'      => $schedule_end_date,
                    'added_on'      => $current_date,
                    'added_by'      => $user_id,
                    'po_id'         => $poid,
                    'task_status'   => 0, // Pending
                    'holidayscount' => 0,
                    'userid'        => $user_id
                );

                // Double check (Safety)
                $check = $this->db->get_where('task_department_wise_scheduling', array(
                    'df_id' => $dfid,
                    'po_id' => $poid,
                    'taskid' => $task_row->task_id
                ));

                if ($check->num_rows() == 0) {
                    $this->db->insert('task_department_wise_scheduling', $schedule_data);
                    $this->checkpaymentstageandmark($task_row->task_id, $dfid, $poid);
                }
            }
        }

    } else {
        // --- NORMAL LOGIC ---
        
        // Get existing task IDs
        $taskids = array();
        $q_exist = $this->db->select('taskid')->from('task_department_wise_scheduling')->where('df_id', $dfid)->get();
        if ($q_exist->num_rows() > 0) {
            foreach ($q_exist->result() as $r) {
                $taskids[] = $r->taskid;
            }
        }

        // Fetch Remaining Tasks
        $this->db->select('task_id, department_id, tat, tat_start_from');
        $this->db->from('task_management');
        if (!empty($taskids)) {
            $this->db->where_not_in('task_id', $taskids);
        }
        $this->db->where('status', 1);
        $this->db->where('task_id !=', 2); // Exclude Task 2
        $this->db->order_by('system_created_sort_order', 'asc');
        $query_tasks = $this->db->get();

        if ($query_tasks->num_rows() > 0) {
            foreach ($query_tasks->result() as $row) {
                $startfrom_task_id = $row->tat_start_from;
                $tat = $row->tat;

                $q1 = $this->db->select('end_date')
                               ->from('task_department_wise_scheduling')
                               ->where('df_id', $dfid)
                               ->where('taskid', $startfrom_task_id)
                               ->get();

                if ($q1->num_rows() > 0) {
                    $row1 = $q1->row();
                    $prev_end_date = $row1->end_date;
                    
                    $startdate = date('Y-m-d', strtotime('+1 days', strtotime($prev_end_date)));
                    $scheduled_dates = $this->buildDfTaskScheduleWindow($row->task_id, $startdate, $tat, true);

                    $final_start_date = $scheduled_dates['start_date'];
                    $final_end_date = $scheduled_dates['end_date'];

                    $schedule_data = array(
                        'df_id'         => $dfid,
                        'taskid'        => $row->task_id,
                        'department_id' => $row->department_id,
                        'start_date'    => $final_start_date,
                        'end_date'      => $final_end_date,
                        'added_on'      => $current_date,
                        'added_by'      => $user_id,
                        'po_id'         => $poid,
                        'task_status'   => 0,
                        'holidayscount' => 0,
                        'userid'        => $user_id
                    );

                    $this->db->insert('task_department_wise_scheduling', $schedule_data);
                    $this->checkpaymentstageandmark($row->task_id, $dfid, $poid);
                }
            }
        }
    }

    // 9. Marketing Auto Assign
    $departid = 9;
    $this->task->marketingtaskautoassign($dfid, $user_id, $departid);

    $this->session->set_flashdata('message', '<div class="alert alert-success">Record successfully added.</div>');
    redirect(page_url . 'Dashboard'); 
}

	function checkpaymentstageandmark($taskid, $dfid, $poid){
		$q = $this->db->select('payment_term')->from('poreceived')->where('id',$poid)->get();
		if($q->num_rows()>0){
		foreach($q->result() as $row);

		$q1 = $this->db->select('milestone')->from('payment_terms_milestone')->where('payment_term_id',$row->payment_term)->get();
		if($q1->num_rows()>0){
			foreach($q1->result() as $row1){

				$data = array('paymentstage'=>1);
				$this->db->where('taskid',$row1->milestone);
				$this->db->where('df_id',$dfid);
				$this->db->where('po_id',$poid);
				$this->db->update('task_department_wise_scheduling',$data);


			}
		}
		}
	}

	public function dflist()
	{
		$i=1;
		$dfdata= array();
		$this->db->select('a.*, b.first_name, b.last_name')->from('df_release a')->join('system_users b','a.added_by=b.user_id');

		if($_SESSION['logged_in']['adminuser']<>1)
		{
			$this->db->where('a.added_by',$_SESSION['logged_in']['user_id']);
		}
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span class="btn btn-primary btn-xs">Click to download DF</span></a>';

			$dfdata[] = array('sr_no'=>$i,
			'df_no'=>$row->df_no,
			'dfupload'=>$dfupload,
			'addedon'=>date('d-m-Y',strtotime($row->added_on))."<br>".date('H:i A',strtotime($row->added_on)),
			'addedby'=>$row->first_name." ".$row->last_name);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($dfdata),
			"iTotalDisplayRecords" => count($dfdata),
			"aaData"=>$dfdata);
			
		echo json_encode($results);
	}

public function dfreleasedashboard(){
	$this->load->view('master/dfreleasedashboard');
}

public function save_marked_df()
{
	$current_user_id = (int) $this->session->userdata['logged_in']['user_id'];
	$df_id = (int) $this->input->post('df_id');
	$mark_value = (int) $this->input->post('mark_value');
	$default_redirect = page_url . 'Task/dfreleasedashboard/';
	$redirect_url = $default_redirect;

	$posted_redirect = trim((string) $this->input->post('return_url'));
	if ($posted_redirect !== '' && strpos($posted_redirect, page_url) === 0) {
		$redirect_url = $posted_redirect;
	}

	if ($df_id <= 0) {
		$this->session->set_flashdata('message', '<strong>Error:</strong> Invalid DF selected for marking.');
		redirect($redirect_url);
		return;
	}

	if (!in_array($mark_value, array(0, 1), true)) {
		$this->session->set_flashdata('message', '<strong>Error:</strong> Invalid DF marking request.');
		redirect($redirect_url);
		return;
	}

	if (!$this->db->field_exists('priority_marked', 'df_release')) {
		$this->session->set_flashdata('message', '<strong>Error:</strong> DF marking field is missing in the database.');
		redirect($redirect_url);
		return;
	}

	$owner_row = $this->db->select('id')
		->from('poreceived')
		->where('df_id', $df_id)
		->where('added_by', $current_user_id)
		->order_by('id', 'DESC')
		->limit(1)
		->get()
		->row();

	if (empty($owner_row)) {
		$this->session->set_flashdata('message', '<strong>Access denied:</strong> You can mark only your own DF.');
		redirect($redirect_url);
		return;
	}

	$this->db->where('id', $df_id)->update('df_release', array('priority_marked' => $mark_value));

	if ($this->db->error()['code'] !== 0) {
		$this->session->set_flashdata('message', '<strong>Error:</strong> DF marking could not be updated due to a database issue.');
		redirect($redirect_url);
		return;
	}

	if ($mark_value === 1) {
		$this->session->set_flashdata('message', '<strong>Success:</strong> DF has been marked successfully.');
	} else {
		$this->session->set_flashdata('message', '<strong>Success:</strong> DF marking has been removed.');
	}

	redirect($redirect_url);
}

public function save_penality_df()
{
	$current_user_id = (int) $this->session->userdata['logged_in']['user_id'];
	$df_id = (int) $this->input->post('df_id');
	$penalityamount = (float) $this->input->post('penalityamount');
	$default_redirect = page_url . 'Task/dfreleasedashboard/';
	$redirect_url = $default_redirect;

	$posted_redirect = trim((string) $this->input->post('return_url'));
	if ($posted_redirect !== '' && strpos($posted_redirect, page_url) === 0) {
		$redirect_url = $posted_redirect;
	}

	if ($df_id <= 0) {
		$this->session->set_flashdata('message', '<strong>Error:</strong> Invalid DF selected for penalty marking.');
		redirect($redirect_url);
		return;
	}

	if ($penalityamount < 0) {
		$this->session->set_flashdata('message', '<strong>Error:</strong> Penalty amount cannot be negative.');
		redirect($redirect_url);
		return;
	}

	$po_rows = $this->db->select('id, added_by')
		->from('poreceived')
		->where('df_id', $df_id)
		->where('added_by', $current_user_id)
		->order_by('id', 'DESC')
		->get();

	if ($po_rows->num_rows() <= 0) {
		$this->session->set_flashdata('message', '<strong>Access denied:</strong> You can mark penalty only for your own DF.');
		redirect($redirect_url);
		return;
	}

	$primary_po_row = $po_rows->row();

	$this->db->trans_start();
	$this->db->where('df_id', $df_id)
		->where('added_by', $current_user_id)
		->update('poreceived', ['penalityamount' => 0]);

	$this->db->where('id', (int) $primary_po_row->id)
		->update('poreceived', ['penalityamount' => $penalityamount]);
	$this->db->trans_complete();

	if (!$this->db->trans_status()) {
		$this->session->set_flashdata('message', '<strong>Error:</strong> Penalty DF could not be updated due to a database issue.');
		redirect($redirect_url);
		return;
	}

	if ($penalityamount > 0) {
		$this->session->set_flashdata('message', '<strong>Success:</strong> Penalty amount has been updated and linked to the penalty DF report.');
	} else {
		$this->session->set_flashdata('message', '<strong>Success:</strong> Penalty marking has been removed from this DF.');
	}

	redirect($redirect_url);
}

public function onholdf(){
	$this->load->view('master/onholddf');
}

public function dfmeeting(){
	$this->ensureDfMeetingRecipientFields();
	$this->ensureDfMeetingPointTrackingFields();
	$data = array(
		'can_manage_friday_reset' => $this->canManageDfMeetingFridayReset(),
		'friday_reset_target_date' => $this->getDfMeetingFridayDate(date('Y-m-d'), true)
	);
	$this->load->view('master/dfmeeting', $data);
}

public function dfmeeting_friday_reset()
{
	if (!$this->canManageDfMeetingFridayReset()) {
		$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">This admin backup tool is not available for your account.</div>');
		redirect(page_url . 'Dashboard');
	}

	$target_friday = $this->getDfMeetingFridayDate(date('Y-m-d'), true);
	$query = $this->db->select('
			t.id,
			t.df_id,
			t.start_date,
			t.end_date,
			t.assigned_user,
			d.df_no,
			d.df_description,
			p.company_name,
			u.first_name,
			u.last_name
		')
		->from('task_department_wise_scheduling t')
		->join('df_release d', 't.df_id = d.id', 'left')
		->join('poreceived p', 't.po_id = p.id', 'left')
		->join('system_users u', 't.assigned_user = u.user_id', 'left')
		->where('t.taskid', 4)
		->where('t.task_status', 0)
		->where('t.on_hold', 0)
		->where('d.df_status', 0)
		->order_by('d.df_no', 'ASC')
		->get();

	$meeting_rows = array();
	if ($query->num_rows() > 0) {
		foreach ($query->result() as $row) {
			$row->target_friday = $target_friday;
			$meeting_rows[] = $row;
		}
	}

	$data = array(
		'target_friday' => $target_friday,
		'meeting_rows' => $meeting_rows
	);

	$this->load->view('master/dfmeeting_friday_reset', $data);
}

public function apply_dfmeeting_friday_reset()
{
	if (!$this->canManageDfMeetingFridayReset()) {
		$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">This admin backup tool is not available for your account.</div>');
		redirect(page_url . 'Dashboard');
	}

	$task_record_ids = array();
	foreach ((array) $this->input->post('task_record_ids') as $task_record_id) {
		$task_record_id = (int) $task_record_id;
		if ($task_record_id > 0 && !in_array($task_record_id, $task_record_ids, true)) {
			$task_record_ids[] = $task_record_id;
		}
	}

	if (empty($task_record_ids)) {
		$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Please select at least one DF meeting task to reset.</div>');
		redirect(page_url . 'Task/dfmeeting_friday_reset');
	}

	$target_friday = $this->getDfMeetingFridayDate(date('Y-m-d'), true);
	$updated_count = 0;
	$user_id = (int) $this->session->userdata['logged_in']['user_id'];
	$audit_update = $this->getTaskSchedulingAuditColumns($user_id);

	$this->db->trans_begin();

	foreach ($task_record_ids as $task_record_id) {
		$row = $this->db->select('t.id')
			->from('task_department_wise_scheduling t')
			->join('df_release d', 't.df_id = d.id', 'left')
			->where('t.id', $task_record_id)
			->where('t.taskid', 4)
			->where('t.task_status', 0)
			->where('t.on_hold', 0)
			->where('d.df_status', 0)
			->limit(1)
			->get()
			->row();

		if (empty($row)) {
			continue;
		}

		$update_data = array(
			'start_date' => $target_friday,
			'end_date' => $target_friday,
		);

		if (!empty($audit_update)) {
			$update_data = array_merge($update_data, $audit_update);
		}

		$this->db->where('id', (int) $row->id)->update('task_department_wise_scheduling', $update_data);

		if ($this->db->affected_rows() >= 0) {
			$updated_count++;
		}
	}

	if ($this->db->trans_status() === FALSE) {
		$this->db->trans_rollback();
		$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">DF meeting Friday reset could not be completed.</div>');
		redirect(page_url . 'Task/dfmeeting_friday_reset');
	}

	$this->db->trans_commit();
	$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">' . $updated_count . ' DF meeting schedule(s) have been reset to Friday ' . date('d-M-Y', strtotime($target_friday)) . '.</div>');
	redirect(page_url . 'Task/dfmeeting_friday_reset');
}

public function ongoingtasklist()
{
    $scope = $this->getTaskDashboardScope();
    $user_id = (int) $scope['user_id'];
    $admin_user_type = (int) $scope['admin_user_type'];
    $personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($user_id);
    
    $i = 1;
    
    // URI Segments
    $filter_req = $this->uri->segment(3);
    $deptid = $this->uri->segment(4);
    $usrid = $this->uri->segment(5);
    $df_ids = $this->uri->segment(6);
    $activeusertype = $this->uri->segment(7);

    // --- 1. Prepare Department Filter ---
    $department_ids_filter = !empty($scope['can_view_team_tasks']) ? $scope['view_department_ids'] : array();

    // --- 2. Build the Optimized Query ---
    $this->db->select('
        a.id, a.taskid, a.df_id, a.po_id, a.department_id, a.assigned_user, 
        a.start_date, a.end_date, a.taskupdatedontime, a.remarks, a.on_hold, a.task_status,
        
        b.df_no, b.df_upload, b.added_on as df_added_on, b.df_description, b.df_status, 
        
        c.task_name, c.sortorder, c.is_it_mom, c.task_frequency,
        
        d.department,
        
        f.first_name, f.last_name,
        
        p.lead_id, p.pono, p.df_number, p.basic_machine as po_basic_machine, p.po_attachment, p.company_name,
        
        k.first_name as marketingpersonfname, k.last_name as marketingpersonlname,
        
        qcd.mach_model_no,
        annex.product_to_be_packed,
        
        l.patient_type_id,

        (SELECT COUNT(id) FROM communication_ticket_system WHERE task_record_id = a.id AND df_id = a.df_id) as ticket_count
    ');

    $this->db->from('task_department_wise_scheduling a');
    $this->db->join('df_release b', 'a.df_id=b.id', 'left');
    $this->db->join('task_management c', 'a.taskid=c.task_id', 'left');
    $this->db->join('departments d', 'a.department_id=d.department_id', 'left');
    $this->db->join('system_users f', 'a.assigned_user=f.user_id', 'left');
    $this->db->join('poreceived p', 'a.po_id=p.id', 'left');
    $this->db->join('system_users k', 'p.added_by=k.user_id', 'left');
    
    // Joins to replace the loops (Optimized)
    $this->db->join('quotation_customer_data qcd', 'p.lead_id=qcd.lead_id', 'left');
    $this->db->join('quotation_annexture_1 annex', 'qcd.id=annex.record_id', 'left');
    $this->db->join('leads l', 'p.lead_id=l.id', 'left');

    // Filters
    $this->db->where('a.task_status', 0);
    $this->db->where('a.on_hold', 0);
    $this->db->where('a.assigned_user !=', 0);
    $this->db->where('a.end_date >=', date('Y-m-d'));

    if (!empty($department_ids_filter)) {
        $this->db->group_start();
        $this->db->where_in('a.department_id', $department_ids_filter);
        $this->applyTaskAssignedUserOrFilter('a.assigned_user', $personal_visible_user_ids);
        $this->db->group_end();
    }

    $this->db->where('a.department_id !=', '22');

    if (!empty($scope['personal_task_only'])) {
        $this->applyTaskAssignedUserFilter('a.assigned_user', $personal_visible_user_ids);
    }
    if ($df_ids != '' && $df_ids != 'ALL') {
        $this->db->where('a.df_id', $df_ids);
    }
    if ($deptid != '' && $deptid != 'ALL') {
        $this->db->where('a.department_id', $deptid);
    }
    if ($usrid != '' && $usrid != 'ALL') {
        $this->db->where('a.assigned_user', $usrid);
    }

    // DF Status Check (OR condition)
    $this->db->group_start();
    $this->db->where('a.df_id', 0);
    $this->db->or_where('b.df_status', 0);
    $this->db->group_end();

    // Group by to handle potential duplicates from joins
    $this->db->group_by('a.id');
    $this->db->order_by('b.df_no', 'asc');
    
    $query = $this->db->get();
    $res = $query->result();

    $taskdata = array();
    $today_str = date('Y-m-d');

    foreach ($res as $row) {

        // --- 3. Filter Logic (PHP side) ---
        $show = 1;
        if ($filter_req == 1) {
            if (strtotime($today_str) != strtotime(date('Y-m-d', strtotime($row->end_date)))) {
                $show = 0;
            }
        } else if ($filter_req == 2) {
            $show = $this->task->compareDateThisWeek(date('Y-m-d', strtotime($row->end_date)));
        }

        if ($show == 1) {
            
            $pendinddays = $this->task->getDays($today_str, $row->end_date, 2);

            // Date Formatting
            $dfreleasedate = "NA";
            if ($row->po_id > 0 && $row->df_added_on && $row->df_added_on != '0000-00-00 00:00:00') {
                $dfreleasedate = date('d-m-Y', strtotime($row->df_added_on));
                if ($dfreleasedate == '01-01-1970') $dfreleasedate = 'NA';
            }

            // --- 4. Button Logic ---
            $updateprogress = '';
            $is_direct_assignee = ((int) $user_id === (int) $row->assigned_user);
            $can_update_as_coordinator = (!$is_direct_assignee
                && $user_id != 61
                && $user_id != 161
                && $this->canCurrentUserUpdateOwnOrCoordinatorTask((int) $row->assigned_user, $scope));
            
            if ($is_direct_assignee || $can_update_as_coordinator) {
                
                if ($row->df_id == 0 && $row->taskid == 2) {
                    // Release DF
                     $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease(' . $row->id . ',' . $row->po_id . ',' . $row->df_number . ');">Release DF</span>';
                    //$updateprogress = '<a href="' . page_url . 'Dashboard/releasedf/' . $row->id . '/' . $row->df_number . '"><span class="btn btn-warning btn-xs">Release DF</span></a>';
                    // if ($user_id == 161) {
                    //     $updateprogress = '<a href="' . page_url . 'Dashboard/releasedf/' . $row->id . '/' . $row->df_number . '"><span class="btn btn-warning btn-xs">Release DF</span></a>';
                    // } else {
                    //     $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease(' . $row->id . ',' . $row->po_id . ',' . $row->df_number . ');">Release DF</span>';
                    // }

                } else {
                    
                    if ($row->is_it_mom == 1) {
                        $updateprogress = '<a href="' . page_url . 'Task/dfmeeting/' . $row->id . '/' . $row->df_id . '"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
                    
                    } else if ($row->df_id == 0 && $row->taskid == 114) {
                        // Fill DF (Design Form)
                        if (in_array($row->mach_model_no, [300, 600])) {
                            $updateprogress = '<a href="' . page_url . 'Dashboard/df_form_600/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                        } else {
                            if ($row->product_to_be_packed == 2) {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/powder_df_form_design/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                            } else {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/df_project_form/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                            }
                        }

                    } else if ($row->df_id == 0 && $row->taskid == 86) {
                        // DF Review Meeting
                        if ($row->po_basic_machine == 1) {
                        	$updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
                            //$updateprogress = '<a href="' . page_url . 'Dashboard/edit_basic_machine_df_project_form/' . $row->po_id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                        } else {
                            if (in_array($row->mach_model_no, [300, 600])) {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/design_form_600_edit/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                            } else {
                                if ($row->product_to_be_packed == 2) {
                                    $updateprogress = '<a href="' . page_url . 'Dashboard/edit_powder_df_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                                } else {
                                    $updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                                }
                            }
                        }

                    } else if ($row->taskid == 3) {
                        // Create PI
                        if ($row->patient_type_id == 1) {
                            $updateprogress = '<a href="' . page_url . 'Form/performa_invoice/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">Create PI</a>';
                        } else {
                            $updateprogress = '<a href="' . page_url . 'Form/export_performa_invoice/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">Create PI</a>';
                        }

                    } else {
                        // Standard Update Progress
                        $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks(' . $row->id . ',' . $row->sortorder . ',' . $row->df_id . ',' . $row->taskid . ',' . $row->start_date . ');">Update Progress</span>';
                    }
                }
                
                // Safety check for Task 114 if still empty
                if ($row->df_id == 0 && $row->taskid == 114 && empty($updateprogress)) {
                     if ($row->po_basic_machine == 1) {
                     	$updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                         //$updateprogress = '<a href="' . page_url . 'Dashboard/basic_machine_df_form/' . $row->id . '/' . $row->po_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)- Basic Machine</a>';
                     }
                }

                if ($this->isPunchPointWorkflowTaskId((int) $row->taskid)) {
                    $updateprogress = $this->buildPunchPointClosureButton((int) $row->id, (int) $row->taskid);
                }

            } else {
                // Admin Logic
                if ($user_id == 61 || $user_id == 161) {
                    if ($row->df_id == 0 && $row->taskid == 2) {
                        $updateprogress = '<a href="' . page_url . 'Dashboard/releasedf/' . $row->id . '/' . $row->df_number . '"><span class="btn btn-warning btn-xs">Release DF</span></a>';
                    } else {
                        if ($row->is_it_mom == 1) {
                            $updateprogress = '<a href="' . page_url . 'Task/dfmeeting/' . $row->id . '/' . $row->df_id . '"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
                        } else if ($row->df_id == 0 && $row->taskid == 114) {
                             if ($row->po_basic_machine == 1) {
                             	$updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                                //$updateprogress = '<a href="' . page_url . 'Dashboard/basic_machine_df_form/' . $row->id . '/' . $row->po_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF) - Basic Machine</a>';
                            } else {
                                if (in_array($row->mach_model_no, [300, 600])) {
                                    $updateprogress = '<a href="' . page_url . 'Dashboard/df_form_600/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                                } else {
                                    if ($row->product_to_be_packed == 2) {
                                        $updateprogress = '<a href="' . page_url . 'Dashboard/powder_df_form_design/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                                    } else {
                                        $updateprogress = '<a href="' . page_url . 'Dashboard/df_project_form/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                                    }
                                }
                            }
                        } else if ($row->df_id == 0 && $row->taskid == 86) {
                             if ($row->po_basic_machine == 1) {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                            } else {
                                if (in_array($row->mach_model_no, [300, 600])) {
                                    $updateprogress = '<a href="' . page_url . 'Dashboard/design_form_600_edit/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                                } else {
                                    if ($row->product_to_be_packed == 2) {
                                        $updateprogress = '<a href="' . page_url . 'Dashboard/edit_powder_df_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                                    } else {
                                        $updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                                    }
                                }
                            }
                        } else {
                            // PI Check
                            if ($row->taskid == 3) {
                                $pilink = '';
                                if ($row->patient_type_id == 1) {
                                    $pilink = page_url . 'Form/performa_invoice/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id;
                                } else if ($row->patient_type_id == 2) { 
                                    $pilink = page_url . 'Form/export_performa_invoice/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id;
                                }
                                $updateprogress = '<a href="' . $pilink . '" class="btn btn-sm btn-danger">CREATE PI</a>';
                            } else {
                                $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks(' . $row->id . ',' . $row->sortorder . ',' . $row->df_id . ',' . $row->taskid . ',' . $row->start_date . ');">Update Progress</span>';
                            }
                        }
                    }

                    if ($this->isPunchPointWorkflowTaskId((int) $row->taskid)) {
                        $updateprogress = $this->buildPunchPointClosureButton((int) $row->id, (int) $row->taskid);
                    }
                } else {
                    // Show Reassign
                    if ($this->canCurrentUserAssignDepartmentTasks((int) $row->department_id, $scope)) {
                        $updateprogress = '<span class="btn btn-danger btn-xs" onclick="reassigntasktoanotheruser(' . $row->id . ',' . $row->department_id . ',' . $row->assigned_user . ');">Reassign</span>';
                    }
                }
            }

            // --- 5. Display Logic ---
            $ticketlink = '';
            if ($row->taskid == 2) {
                $pono = "PO NO. " . $row->pono;
                $po_attachment = '<a href="' . sfdocument . 'Taskdocument/' . $row->po_attachment . '" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
            } else {
                $pono = "";
                $po_attachment = "";
            }

            if ($row->df_id == 0) {
                $dfno = "<span style='color:red;'>DF Not Uploaded</span>";
                $dfupload = "--";
            } else {
                $dfno = strtoupper($row->df_no);
                $dfupload = '<a href="' . sfdocument . 'Taskdocument/dfattachment/' . $row->df_upload . '" download><span><u>DOWNLOAD DF</u></span></a>';
            }

            $userdepartment_id = $this->session->userdata['logged_in']['department_id'];
            $pofordownload = "";
            if ($userdepartment_id == 9 || $userdepartment_id == 20 || $userdepartment_id == 10) {
                $companyname = "DF No.: <strong>" . $row->df_number . "</strong><br><br>";
                $pofordownload = $companyname . '<a href="' . sfdocument . 'Taskdocument/' . $row->po_attachment . '" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
            }

            if (!empty($row->taskupdatedontime) && $row->taskupdatedontime != '0000-00-00 00:00:00' && strtotime($row->taskupdatedontime) !== false) {
                $lastremarksupdateddate = date('d-m-Y', strtotime($row->taskupdatedontime));
                $lastremarksupdatedtime = date('h:i A', strtotime($row->taskupdatedontime));
                $updatedt = $lastremarksupdateddate . "<br>" . $lastremarksupdatedtime;

                if ($row->ticket_count > 0) {
                    $ticketlink = '<a href="' . page_url . 'Task/viewdfwiseticket/' . $row->df_id . '/' . $row->id . '"><span class="btn btn-xs btn-primary">View Tickets</span></a>';
                }
            } else {
                $updatedt = "";
            }

            $taskdata[] = array(
                'sr_no' => $i,
                'df_no' => strtoupper($dfno . "<br>" . $row->df_description) . "<br><br>" . $pono . "<br>" . $po_attachment . " " . $pofordownload . "<br> MARKETING PERSON - " . strtoupper($row->marketingpersonfname . " " . $row->marketingpersonlname),
                'department' => strtoupper($row->department),
                'task_name' => strtoupper($row->task_name),
                'dfupload' => $dfupload,
                'companyname' => $row->company_name,
                'startdate' => date('d-m-Y', strtotime($row->start_date)),
                'pendingdays' => "<strong style='color:green;font-weight:bold;'>" . $pendinddays . " DAYS</strong>",
                'remarks' => strtoupper($row->remarks) . "<br><br>" . $updatedt . "<br>" . $ticketlink,
                'df_release_date' => $dfreleasedate,
                'end_date' => date('d-m-Y', strtotime($row->end_date)),
                'membername' => strtoupper($row->first_name . " " . $row->last_name),
                'updateprogress' => $updateprogress
            );
            $i++;
        }
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($taskdata),
        "iTotalDisplayRecords" => count($taskdata),
        "aaData" => $taskdata
    );

    echo json_encode($results);
}

public function ongoingtasklistoldone()
	{
		$updateprogress = '';
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$i=1;
		$filter_req=$this->uri->segment(3);
		/** user check **/
		$department_id=array();
		$self_user=0;
		if($_SESSION['logged_in']['adminuser']==2)
		{
		$department_id=$this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
		}
		$user_id =$this->session->userdata['logged_in']['user_id']; 
		$deptid = $this->uri->segment(4);
		$usrid = $this->uri->segment(5);
		$df_ids = $this->uri->segment(6);
		$activeusertype= $this->uri->segment(7);
		if($activeusertype==2){
		 	$q = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		 	if($q->num_rows()>0){
		 		foreach($q->result() as $rows){
		 			$leaderdepartment[] = $rows->department_id;
		 		}
		 	}
		 }

		/** end **/
		$taskdata= array();
		$this->db->select('a.assigned_user,a.department_id, a.end_date, a.start_date, a.id,a.taskid, c.sortorder, c.is_it_mom, b.df_no, c.task_name, a.taskupdatedontime, d.department, b.df_upload, b.added_on, a.remarks, a.po_id, p.lead_id, a.df_id, a.id, p.df_number, f.first_name, f.last_name, c.task_frequency, b.df_description, p.pono, p.df_number, p.basic_machine, p.po_attachment,p.lead_id, k.first_name as marketingpersonfname, k.last_name as marketingpersonlname, p.company_name')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->join('task_management c','a.taskid=c.task_id','left')->join('departments d','a.department_id=d.department_id','left')->join('system_users f','a.assigned_user=f.user_id','left')->join('poreceived p','a.po_id=p.id','left')->join('system_users k','p.added_by=k.user_id','left')->where('a.task_status',0)->where('a.on_hold',0)->where('a.assigned_user!=',0)->where('a.end_date>=',date('Y-m-d'));
			if(count($department_id)>0)
			{
				$this->db->where_in('a.department_id',$department_id,'false');
			}

			$this->db->where('a.department_id!=','22');
			if($_SESSION['logged_in']['adminuser']==3)
			{
				$this->db->where('a.assigned_user',$_SESSION['logged_in']['user_id']);
			}
			if($df_ids<>'' && $df_ids<>'ALL'){
				$this->db->where('a.df_id',$df_ids);
			}
			if($deptid<>'' && $deptid<>'ALL'){
				$this->db->where('a.department_id',$deptid);
			}
			if($usrid<>'' && $usrid<>'ALL'){
				$this->db->where('a.assigned_user',$usrid);
			}
			$this->db->group_start()
			->where('a.df_id', 0) // No DF uploaded yet → show task
			->or_where('b.df_status', 0) // DF exists and is active
			->group_end();
			$query = $this->db->order_by('b.df_no','asc')->get();
			$res = $query->result();
			foreach($res as $row){

				$pendinddays=$this->task->getDays(date('Y-m-d'),$row->end_date,2);
				if($row->po_id>0){
					$dfno = $row->df_no;
					if($row->added_on<>'0000-00-00 00:00:00' || $row->added_on<>'')
					{
						$dfreleasedate = date('d-m-Y',strtotime($row->added_on));
					}else{
						$dfreleasedate = "";
					}
					
					
					
				}else{
					$dfno = "NA";
					$dfreleasedate = "NA";
				}
				if($dfreleasedate=='01-01-1970'){
					$dfreleasedate='NA';
				}else{
					$dfreleasedate = $dfreleasedate;
				}
			if($user_id==$row->assigned_user){
			if($row->df_id==0 && $row->taskid==2){
				$updateprogress = '<a href="' . page_url . 'Dashboard/releasedf/' . $row->id . '/' . $row->df_number . '"><span class="btn btn-warning btn-xs">Release DF</span></a>'; 
				// if($user_id==161){
				// 	$updateprogress = '<a href="' . page_url . 'Dashboard/releasedf/' . $row->id . '/' . $row->df_number . '"><span class="btn btn-warning btn-xs">Release DF</span></a>'; 
				// }else{
				// 	$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.','.$row->df_number.');">Release DF</span>'; 
				// }
				
			}else{
				if($row->is_it_mom==1){
					$updateprogress = '<a href="'.page_url.'Task/dfmeeting/'.$row->id.'/'.$row->df_id.'"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
				}else if($row->df_id==0 && $row->taskid==114){

					/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
							if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600 || $productpacktype->mach_model_no==600){
									 $updateprogress = '<a href="'.page_url.'Dashboard/df_form_600/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
							}else{
								if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/powder_df_form_design/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/df_project_form/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}
						}
						
					}
					

				}else if($row->df_id==0 && $row->taskid==86){

					if($row->basic_machine==1){
						$updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
						//$updateprogress = '<a href="'.page_url.'Dashboard/edit_basic_machine_df_project_form/'.$row->po_id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>'; 
					}else{
						/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600 || $productpacktype->mach_model_no==600){

							$updateprogress = '<a href="'.page_url.'Dashboard/design_form_600_edit/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>'; 
						}else{
							if($productpacktype->product_to_be_packed==2){

							
							  $updateprogress = '<a href="'.page_url.'Dashboard/edit_powder_df_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';   
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						}
						}
						
					}
					}

					

					

				}else if($row->df_id==0 && $row->taskid==3){

					/**PI Creation*/

					$q = $this->db->select('patient_type_id')->from('leads')->where('id',$row->lead_id)->get();
					if($q->num_rows()>0){
						foreach($q->result() as $picondition);
						if($picondition->patient_type_id==1){
							$updateprogress = '<a href="'.page_url.'Form/performa_invoice/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">Create PI</a>'; 

							//https://pms.shubhampack.in/index.php/Form/performa_invoice/95/682/
						}else{
							$updateprogress = '<a href="'.page_url.'Form/export_performa_invoice/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">Create PI</a>'; 
							//https://pms.shubhampack.in/index.php/Form/export_performa_invoice/51/607/
						}
					}

					/**PI Creation*/

					

					

				}else{

					$tdate = date('Y-m-d');
					// if(date('Y-m-d',strtotime($row->start_date))<=$tdate){
						$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row->id.','.$row->sortorder.','.$row->df_id.','.$row->taskid.','.$row->start_date.');">Update Progress</span>
					';
					// }else{
					// 	$updateprogress = "";
					// }

					

					
				}			
				
			}
			if($row->df_id==0 && $row->taskid==114){
				//updateprogress = '<span class="btn btn-primary btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.');">Fill Delivery Form (DF)</span>'; 
				/*Fetch Product type*/
				//echo $row->lead_id; exit;


				/*If record is related to Basic Machine*/
				if($row->basic_machine==1){
					$updateprogress = '<a href="'.page_url.'Dashboard/basic_machine_df_form/'.$row->id.'/'.$row->po_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)- Basic Machine</a>';
				}else{

						$q99 = $this->db
					->select('b.product_to_be_packed, a.mach_model_no')
					->from('quotation_customer_data a')
					->join('quotation_annexture_1 b', 'a.id = b.record_id', 'left')
					->where('a.lead_id', $row->lead_id)
					->get();
					//echo "<pre>"; print_r($q99->result()); exit;
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600 || $productpacktype->mach_model_no==600){
							$updateprogress = '<a href="'.page_url.'Dashboard/df_form_600/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';

						}else{
							if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/powder_df_form_design/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/df_project_form/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}
						}
						
						
					}
				}
				

					
				 
			}
			}else{
				if($_SESSION['logged_in']['user_id']==61 || $_SESSION['logged_in']['user_id']==161)
				{
					//echo "text"; exit;
					if($row->df_id==0 && $row->taskid==2){
					$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.','.$row->df_number.');">Release DF</span>';  

				

					}else{

					if($row->is_it_mom==1){
					$updateprogress = '<a href="'.page_url.'Task/dfmeeting/'.$row->id.'/'.$row->df_id.'"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
					}else if($row->df_id==0 && $row->taskid==114){

						if($row->basic_machine==1){
							$updateprogress = '<a href="'.page_url.'Dashboard/basic_machine_df_form/'.$row->id.'/'.$row->po_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF) - Basic Machine</a>';
						}else{
							/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600 || $productpacktype->mach_model_no==600){
									 $updateprogress = '<a href="'.page_url.'Dashboard/df_form_600/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
							}else{
								if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/powder_df_form_design/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/df_project_form/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}
					}
						
						
					}
						}

					
					

				}else if($row->df_id==0 && $row->taskid==86){

					if($row->basic_machine==1){
						 $updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						//$updateprogress = '<a href="'.page_url.'Dashboard/edit_basic_machine_df_project_form/'.$row->po_id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
					}else{
						/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600 || $productpacktype->mach_model_no==600){

							$updateprogress = '<a href="'.page_url.'Dashboard/design_form_600_edit/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>'; 
						}else{
							if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/edit_powder_df_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						}
						}
						
					}
					}

					 

				}else{


					/** IF PERFORMA INVOICE **/

					if($row->taskid==3)
					{	

					$CheckDomesticExport=$this->CheckDomesticExport($row->lead_id);
					if($CheckDomesticExport==1)
					{
						$pilink=page_url.'Form/performa_invoice/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id;
					}else if($CheckDomesticExport==2)
					{
						$pilink=page_url.'Form/export_performa_invoice/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id;
					}else
					{
						$pilink='';
					}

					$updateprogress = '<a href="'.$pilink.'" class="btn btn-sm btn-danger">CREATE PI</a>';
					}else
					{
						$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row->id.','.$row->sortorder.','.$row->df_id.','.$row->taskid.','.$row->start_date.');">Update Progress</span>';
					}
					
				}
					// else{

					// $tdate = date('Y-m-d');
					// if(date('Y-m-d',strtotime($row->start_date))<=$tdate){
					// $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row->id.','.$row->sortorder.','.$row->df_id.','.$row->taskid.','.$row->start_date.');">Update Progress</span>
					// ';
					//  }else{
					//  $updateprogress = "";
					//  }
					// }			
					}

				}else
				{
				$updateprogress='';
				if($_SESSION['logged_in']['adminuser']==2){
					$updateprogress.= '<span class="btn btn-danger btn-xs" onclick="reassigntasktoanotheruser('.$row->id.','.$row->department_id.','.$row->assigned_user.');">Reassign</span>';
				    }
				}
			}


			$show=0;
			/*** FILTER CONDITIONS ***/
			if($filter_req==1)
			{

				if(strtotime(date('Y-m-d'))==strtotime(date('Y-m-d',strtotime($row->end_date))))
				{
				$show=1;
				}
			}else if($filter_req==2)
			{
				$show=$this->task->compareDateThisWeek(date('Y-m-d',strtotime($row->end_date)));

			}else
			{
				$show=1;
			}
			/** END **/
			$ticketlink = '';
			if($row->taskid==2){
			$pono = "PO NO. ".$row->pono;
			$po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
			}else{
			$pono= "";
			$po_attachment = "";
			}
			//$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span><u>DOWNLOAD DF</u></span></a>';

			if ($row->df_id == 0) {
			$dfno = "<span style='color:red;'>DF Not Uploaded</span>";
			$dfreleasedate = "NA";
			$dfupload = "--";
			} else {
			$dfno = strtoupper($row->df_no);
			$dfreleasedate = ($row->added_on && strtotime($row->added_on)) ? date('d-m-Y', strtotime($row->added_on)) : "NA";
			$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span><u>DOWNLOAD DF</u></span></a>';
			}

			$userdepartment_id =$this->session->userdata['logged_in']['department_id'];	

			if($userdepartment_id==9 || $userdepartment_id==20 || $userdepartment_id==10){
				$companyname = "DF No.: <strong>".$row->df_number."</strong><br><br>";
				$pofordownload = $companyname.'<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
			}else{
				$pofordownload  = "";
			}
			if (!empty($row->taskupdatedontime) && $row->taskupdatedontime != '0000-00-00 00:00:00' && strtotime($row->taskupdatedontime) !== false) {
			$lastremarksupdateddate = date('d-m-Y', strtotime($row->taskupdatedontime));
			$lastremarksupdatedtime = date('h:i A', strtotime($row->taskupdatedontime));
			$updatedt = $lastremarksupdateddate . "<br>" . $lastremarksupdatedtime;

			$ticketcountdata  = $this->task->checkhelpticket($row->id, $row->df_id);
			if ($ticketcountdata) {
			$ticketlink = '<a href="' . page_url . 'Task/viewdfwiseticket/' . $row->df_id . '/' . $row->id . '"><span class="btn btn-xs btn-primary">View Tickets</span></a>';
			} else {
			$ticketlink = '';
			}
			} else {
			$updatedt = "";
			}

		
			if($show==1)
			{
			$taskdata[] = array('sr_no'=>$i,
			'df_no'=>strtoupper($dfno."<br>".$row->df_description)."<br><br>".$pono."<br>".$po_attachment." ".$pofordownload."<br> MARKETING PERSON - ".strtoupper($row->marketingpersonfname." ".$row->marketingpersonlname),
			'department'=>strtoupper($row->department),
			'task_name'=>strtoupper($row->task_name),
			'dfupload'=>$dfupload,
			'companyname'=>$row->company_name,
			'startdate'=>date('d-m-Y',strtotime($row->start_date)),
			'pendingdays'=>"<strong style='color:green;font-weight:bold;'>".$pendinddays." DAYS</strong>",
			'remarks'=>strtoupper($row->remarks)."<br><br>".$updatedt."<br>".$ticketlink,
			'df_release_date'=>$dfreleasedate,
			'end_date'=>date('d-m-Y',strtotime($row->end_date)),
			'membername'=>strtoupper($row->first_name." ".$row->last_name),
			'updateprogress'=>$updateprogress);
			$i++;
		}
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($taskdata),
			"iTotalDisplayRecords" => count($taskdata),
			"aaData"=>$taskdata);
			
		echo json_encode($results);
	}

public function outdatedtask()
{
    $previous_db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    $updateprogress = '';
    $scope = $this->getTaskDashboardScope();
    $user_id = (int) $scope['user_id'];
    $admin_user_type = (int) $scope['admin_user_type'];
    $personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($user_id);

    // Segments
    $filter_req = $this->uri->segment(3);
    $deptid = $this->uri->segment(4);
    $usrid = $this->uri->segment(5);
    $df_ids = $this->uri->segment(6);
    $fiterdays = $this->uri->segment(7);

    $department_id = !empty($scope['can_view_team_tasks']) ? $scope['view_department_ids'] : array();

    $i = 1;
    $taskdata = array();

    // --- 1. Build Optimized Query (Comments Removed) ---
    $this->db->select('
        a.id, a.taskid, a.df_id, a.po_id, a.department_id, a.assigned_user,
        a.start_date, a.end_date, a.taskupdatedontime, a.remarks, a.on_hold, a.task_status,
        
        b.df_no, b.df_upload, b.added_on, b.df_description, b.df_status,
        
        c.task_name, c.is_it_mom, c.sortorder,
        
        d.department,
        
        f.first_name, f.last_name,
        
        p.lead_id, p.pono, p.df_number, p.basic_machine, p.po_attachment, p.company_name,
        
        k.first_name as marketingpersonfname, k.last_name as marketingpersonlname,

        MAX(qcd.mach_model_no) as mach_model_no,
        MAX(annex.product_to_be_packed) as product_to_be_packed,
        MAX(l.patient_type_id) as patient_type_id,
        
        (SELECT COUNT(id) FROM communication_ticket_system WHERE task_record_id = a.id AND df_id = a.df_id) as ticket_count
    ');

    $this->db->from('task_department_wise_scheduling a');
    $this->db->join('df_release b', 'a.df_id=b.id', 'left');
    $this->db->join('task_management c', 'a.taskid=c.task_id', 'left');
    $this->db->join('departments d', 'a.department_id=d.department_id', 'left');
    $this->db->join('poreceived p', 'a.po_id=p.id', 'left');
    $this->db->join('system_users f', 'a.assigned_user=f.user_id', 'left');
    $this->db->join('system_users k', 'p.added_by=k.user_id', 'left');

    // Joins for Logic (Replacing loop queries)
    $this->db->join('quotation_customer_data qcd', 'p.lead_id=qcd.lead_id', 'left');
    $this->db->join('quotation_annexture_1 annex', 'qcd.id=annex.record_id', 'left');
    $this->db->join('leads l', 'p.lead_id=l.id', 'left');

    // Base Conditions for "Outdated"
    $this->db->where('a.task_status', 0);
    $this->db->where('a.on_hold', 0);
    $this->db->where('a.end_date <', date('Y-m-d'));

    if (count($department_id) > 0) {
        $this->db->group_start();
        $this->db->where_in('a.department_id', $department_id);
        $this->applyTaskAssignedUserOrFilter('a.assigned_user', $personal_visible_user_ids);
        $this->db->group_end();
    }

    $this->db->where('a.department_id!=', '22');

    if (!empty($scope['personal_task_only'])) {
        $this->applyTaskAssignedUserFilter('a.assigned_user', $personal_visible_user_ids);
    }

    if ($df_ids <> '' && $df_ids <> 'ALL') {
        $this->db->where('a.df_id', $df_ids);
    }
    if ($deptid <> '' && $deptid <> 'ALL') {
        $this->db->where('a.department_id', $deptid);
    }
    if ($usrid <> '' && $usrid <> 'ALL') {
        $this->db->where('a.assigned_user', $usrid);
    }

    // DF Status Group
    $this->db->group_start();
    $this->db->where('a.df_id', 0); // No DF uploaded yet
    $this->db->or_where('b.df_status', 0); // DF exists and is active
    $this->db->group_end();

    // Group by to avoid duplicates from joins
    $this->db->group_by(array(
        'a.id', 'a.taskid', 'a.df_id', 'a.po_id', 'a.department_id', 'a.assigned_user',
        'a.start_date', 'a.end_date', 'a.taskupdatedontime', 'a.remarks', 'a.on_hold', 'a.task_status',
        'b.df_no', 'b.df_upload', 'b.added_on', 'b.df_description', 'b.df_status',
        'c.task_name', 'c.is_it_mom', 'c.sortorder',
        'd.department',
        'f.first_name', 'f.last_name',
        'p.lead_id', 'p.pono', 'p.df_number', 'p.basic_machine', 'p.po_attachment', 'p.company_name',
        'k.first_name', 'k.last_name'
    ));

    $query = $this->db->get();
    if ($query === false) {
        log_message('error', 'outdatedtask query failed: ' . json_encode($this->db->error()));
        $res = array();
    } else {
        $res = $query->result();
    }

    // --- 2. Process Results (No DB queries inside loop) ---
    foreach ($res as $row) {

        $pendinddays = $this->task->getDays(date('Y-m-d'), $row->end_date, 1);

        if ($row->po_id > 0) {
            $dfno = $row->df_no;
            $dfreleasedate = date('d-m-Y', strtotime($row->added_on));
        } else {
            $dfno = "NA";
            $dfreleasedate = "NA";
        }

        // Username Logic
        if ($row->first_name <> '') {
            $username = $row->first_name . " " . $row->last_name;
        } else {
            $username = "<strong style='color:red;'>Task Not Assigned</strong>";
        }

        $updateprogress = '';
        $is_direct_assignee = ((int) $user_id === (int) $row->assigned_user);
        $can_update_as_coordinator = (!$is_direct_assignee
            && $user_id != 61
            && $user_id != 161
            && $this->canCurrentUserUpdateOwnOrCoordinatorTask((int) $row->assigned_user, $scope));

        // --- Button Logic ---
        if ($is_direct_assignee || $can_update_as_coordinator) {
            
            if ($row->df_id == 0 && $row->taskid == 2) {
                // Release DF
                $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease(' . $row->id . ',' . $row->po_id . ',' . $row->df_number . ');">Release DF</span>';
            
            } else {
                if ($row->is_it_mom == 1) {
                    $updateprogress = '<a href="' . page_url . 'Task/dfmeeting/' . $row->id . '/' . $row->df_id . '"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
                
                } else if ($row->df_id == 0 && $row->taskid == 114) {
                    // Fill DF
                    if ($row->basic_machine == 1) {
                        // $updateprogress = '<a href="' . page_url . 'Dashboard/basic_machine_df_form/' . $row->id . '/' . $row->po_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF) - Basic Machine</a>';
                         $updateprogress = '<a href="' . page_url . 'Dashboard/df_project_form/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                    } else {
                        // Optimized: Using pre-fetched mach_model_no
                        if (in_array($row->mach_model_no, [300, 600])) {
                            $updateprogress = '<a href="' . page_url . 'Dashboard/df_form_600/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                        } else {
                            if ($row->product_to_be_packed == 2) {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/powder_df_form_design/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                            } else {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/df_project_form/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                            }
                        }
                    }

                } else if ($row->df_id == 0 && $row->taskid == 86) {
                    // DF Review
                    if ($row->basic_machine == 1) {
                    	$updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                       // $updateprogress = '<a href="' . page_url . 'Dashboard/edit_basic_machine_df_project_form/' . $row->po_id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                    } else {
                        // Optimized
                        if (in_array($row->mach_model_no, [300, 600])) {
                            $updateprogress = '<a href="' . page_url . 'Dashboard/design_form_600_edit/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                        } else {
                            if ($row->product_to_be_packed == 2) {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/edit_powder_df_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                            } else {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                            }
                        }
                    }

                } else if ($row->df_id == 0 && $row->taskid == 3) {
                    // Create PI - Optimized: Using pre-fetched patient_type_id
                    if ($row->patient_type_id == 1) {
                        $updateprogress = '<a href="' . page_url . 'Form/performa_invoice/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">Create PI</a>';
                    } else {
                        $updateprogress = '<a href="' . page_url . 'Form/export_performa_invoice/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">Create PI</a>';
                    }

                } else {
                    // Standard Update
                    $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks(' . $row->id . ',' . $row->sortorder . ',' . $row->df_id . ',' . $row->taskid . ',' . $row->start_date . ');">Update Progress</span>';
                }
            }
            
            // Fallback check for Task 114
            if ($row->df_id == 0 && $row->taskid == 114 && empty($updateprogress)) {
                if ($row->basic_machine == 1) {
                    //$updateprogress = '<a href="' . page_url . 'Dashboard/basic_machine_df_form/' . $row->id . '/' . $row->po_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)- Basic Machine</a>';
                     $updateprogress = '<a href="' . page_url . 'Dashboard/df_project_form/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                } else {
                    // Re-apply logic just in case
                    if (in_array($row->mach_model_no, [300, 600])) {
                         $updateprogress = '<a href="' . page_url . 'Dashboard/df_form_600/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                    } else {
                        if ($row->product_to_be_packed == 2) {
                            $updateprogress = '<a href="' . page_url . 'Dashboard/powder_df_form_design/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                        } else {
                            $updateprogress = '<a href="' . page_url . 'Dashboard/df_project_form/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                        }
                    }
                }
            }

            if ($this->isPunchPointWorkflowTaskId((int) $row->taskid)) {
                $updateprogress = $this->buildPunchPointClosureButton((int) $row->id, (int) $row->taskid);
            }

        } else {
            // Admin Logic (User 61 or 161)
            if ($user_id == 61 || $user_id == 161) {

                if ($row->df_id == 0 && $row->taskid == 2) {
                     $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease(' . $row->id . ',' . $row->po_id . ',' . $row->df_number . ');">Release DF</span>';
                } else {
                    if ($row->is_it_mom == 1) {
                        $updateprogress = '<a href="' . page_url . 'Task/dfmeeting/' . $row->id . '/' . $row->df_id . '"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
                    } else if ($row->df_id == 0 && $row->taskid == 114) {
                        if ($row->basic_machine == 1) {
                            // $updateprogress = '<a href="' . page_url . 'Dashboard/basic_machine_df_form/' . $row->id . '/' . $row->po_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF) - Basic Machine</a>';
                             $updateprogress = '<a href="' . page_url . 'Dashboard/df_project_form/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                        } else {
                             if (in_array($row->mach_model_no, [300, 600])) {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/df_form_600/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                            } else {
                                if ($row->product_to_be_packed == 2) {
                                    $updateprogress = '<a href="' . page_url . 'Dashboard/powder_df_form_design/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                                } else {
                                    $updateprogress = '<a href="' . page_url . 'Dashboard/df_project_form/' . $row->id . '/' . $row->po_id . '/' . $row->lead_id . '" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
                                }
                            }
                        }
                    } else if ($row->df_id == 0 && $row->taskid == 86) {
                        if ($row->basic_machine == 1) {
                        	 $updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                           // $updateprogress = '<a href="' . page_url . 'Dashboard/edit_basic_machine_df_project_form/' . $row->po_id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                        } else {
                             if (in_array($row->mach_model_no, [300, 600])) {
                                $updateprogress = '<a href="' . page_url . 'Dashboard/design_form_600_edit/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                            } else {
                                if ($row->product_to_be_packed == 2) {
                                    $updateprogress = '<a href="' . page_url . 'Dashboard/edit_powder_df_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                                } else {
                                    $updateprogress = '<a href="' . page_url . 'Dashboard/edit_df_project_form/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
                                }
                            }
                        }
                    } else if ($row->df_id == 0 && $row->taskid == 3) {
                         // Create PI - Admin
                         if ($row->patient_type_id == 1) {
                            $updateprogress = '<a href="' . page_url . 'Form/performa_invoice/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">Create PI</a>';
                        } else {
                            $updateprogress = '<a href="' . page_url . 'Form/export_performa_invoice/' . $row->po_id . '/' . $row->lead_id . '/' . $row->id . '" class="btn btn-sm btn-danger">Create PI</a>';
                        }
                    } else {
                        $updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks(' . $row->id . ',' . $row->sortorder . ',' . $row->df_id . ',' . $row->taskid . ',' . $row->start_date . ');">Update Progress</span>';
                    }
                }

                if ($this->isPunchPointWorkflowTaskId((int) $row->taskid)) {
                    $updateprogress = $this->buildPunchPointClosureButton((int) $row->id, (int) $row->taskid);
                }

            } else {
                // Not assigned and Not Admin -> Reassign
                $updateprogress = '';
                if ($this->canCurrentUserAssignDepartmentTasks((int) $row->department_id, $scope)) {
                    $updateprogress .= '<span class="btn btn-danger btn-xs" onclick="reassigntasktoanotheruser(' . $row->id . ',' . $row->department_id . ',' . $row->assigned_user . ');">Reassign</span>';
                }
            }
        }

        $show = 0;
        /*** FILTER CONDITIONS ***/
        if ($filter_req <> 'ALL' && $filter_req <> '') {
            if ($fiterdays == $pendinddays && $fiterdays <> 0) {
                $show = 1;
            }
        } else {
            $show = 1;
        }

        if ($row->df_id == 0) {
            $dfno = "<span style='color:red;'>DF Not Uploaded</span>";
            $dfreleasedate = "NA";
            $dfupload = "--";
        } else {
            $dfno = strtoupper($row->df_no);
            $dfreleasedate = ($row->added_on && strtotime($row->added_on)) ? date('d-m-Y', strtotime($row->added_on)) : "NA";
            $dfupload = '<a href="' . sfdocument . 'Taskdocument/dfattachment/' . $row->df_upload . '" download><span><u>DOWNLOAD DF</u></span></a>';
        }

        //$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span><u>DOWNLOAD DF</u></span></a>';
        $userdepartment_id = isset($this->session->userdata['logged_in']['department_id']) ? (int)$this->session->userdata['logged_in']['department_id'] : 0;
        if ($userdepartment_id == 9 || $userdepartment_id == 20 || $userdepartment_id == 10) {
            if ($row->company_name <> '') {
                if ($row->df_number <> 0) {
                    $companyname = "DF No.: <strong>" . $row->df_number . "</strong><br><br>";
                } else {
                    $companyname = '';
                }

            } else {
                $companyname = "<strong>Basic Machine</strong>";
            }

            $pofordownload = $companyname . '<a href="' . sfdocument . 'Taskdocument/' . $row->po_attachment . '" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
        } else {
            $pofordownload  = "";
        }

        if (!empty($row->taskupdatedontime) && $row->taskupdatedontime !== '0000-00-00 00:00:00' && $row->taskupdatedontime !== '1970-01-01 05:30:00' && $row->remarks != '') {
            // Validate date
            $timestamp = strtotime($row->taskupdatedontime);
            if ($timestamp !== false) {
                $lastremarksupdateddate = date('d-m-Y', $timestamp);
                $lastremarksupdatedtime = date('h:i A', $timestamp);
                $updatedt = $lastremarksupdateddate . "<br>" . $lastremarksupdatedtime;

                // Optimized: Use ticket_count from query
                if ($row->ticket_count > 0) {
                    $ticketlink = '<a href="' . page_url . 'Task/viewdfwiseticket/' . $row->df_id . '/' . $row->id . '"><span class="btn btn-xs btn-primary">View Tickets</span></a>';
                } else {
                    $ticketlink = '';
                }
            } else {
                // Handle invalid date
                $updatedt = "";
                $ticketlink = "";
            }
        } else {
            $updatedt = "";
            $ticketlink = "";
        }


        if ($show == 1) {
            $taskdata[] = array(
                'sr_no' => $i,
                'df_no' => strtoupper($dfno . "<br>" . $row->df_description),
                'dfupload' => $dfupload . "<br><br>" . $pofordownload . "<br> Marketing Person - " . ucwords(strtolower($row->marketingpersonfname . " " . $row->marketingpersonlname)),
                'department' => strtoupper($row->department),
                'membername' => strtoupper($username),
                'task_name' => strtoupper($row->task_name),
                'pendingdays' => "<strong style='color:red;font-weight:bold;'>" . $pendinddays . " Days</strong>",
                'remarks' => strtoupper($row->remarks) . "<br><br>" . $updatedt . "<br>" . $ticketlink,
                'updateprogress' => $updateprogress,
                'df_release_date' => $dfreleasedate,
                'end_date' => date('d-m-Y', strtotime($row->end_date))
            );
            $i++;
        }
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($taskdata),
        "iTotalDisplayRecords" => count($taskdata),
        "aaData" => $taskdata
    );

    $this->db->db_debug = $previous_db_debug;

    $json_flags = 0;
    if (defined('JSON_UNESCAPED_UNICODE')) {
        $json_flags |= JSON_UNESCAPED_UNICODE;
    }
    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $json_flags |= JSON_INVALID_UTF8_SUBSTITUTE;
    }
    if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
        $json_flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
    }

    $json_output = json_encode($results, $json_flags);
    if ($json_output === false) {
        log_message('error', 'outdatedtask json_encode failed: ' . json_last_error_msg());
        $json_output = '{"sEcho":1,"iTotalRecords":0,"iTotalDisplayRecords":0,"aaData":[]}';
    }

    while (ob_get_level() > 0) {
        @ob_end_clean();
    }

    $this->output
        ->set_content_type('application/json')
        ->set_output($json_output);
    return;
}

public function outdatedtaskbeforeoptimization()
	{

		$updateprogress = '';
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$filter_req = $this->uri->segment(3);
		$department_id=array();
		$self_user=0;
		if($_SESSION['logged_in']['adminuser']==2)
		{
		$department_id=$this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
		}

		$deptid = $this->uri->segment(4);
		$usrid = $this->uri->segment(5);
		$df_ids = $this->uri->segment(6);
		$fiterdays = $this->uri->segment(7);

		$i=1;
		$ticketlink = '';
		$taskdata= array();
		$this->db->select('a.start_date,a.taskupdatedontime, a.department_id, a.assigned_user,a.taskid,a.df_id, c.is_it_mom,c.sortorder, p.df_number,  f.first_name,f.last_name,a.end_date,a.id, b.df_upload, b.df_no, c.task_name, d.department, b.added_on, p.lead_id, a.remarks, p.lead_id, a.po_id, b.df_description, p.pono, p.company_name, p.po_attachment, p.df_number, p.basic_machine, k.first_name as marketingpersonfname, k.last_name as marketingpersonlname')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->join('task_management c','a.taskid=c.task_id','left')->join('departments d','a.department_id=d.department_id','left')->join('poreceived p','a.po_id=p.id','left')->join('system_users f','a.assigned_user=f.user_id','left')->join('system_users k','p.added_by=k.user_id','left')->where('a.task_status',0)->where('a.on_hold',0)->where('a.end_date<',date('Y-m-d'));

		if(count($department_id)>0)
			{
				$this->db->where_in('a.department_id',$department_id,'false');
			}

			$this->db->where('a.department_id!=','22');
			if($_SESSION['logged_in']['adminuser']==3)
			{
				$this->db->where('a.assigned_user',$_SESSION['logged_in']['user_id']);
			}

		if($df_ids<>'' && $df_ids<>'ALL'){
				$this->db->where('a.df_id',$df_ids);
			}
			if($deptid<>'' && $deptid<>'ALL'){
				$this->db->where('a.department_id',$deptid);
			}
			if($usrid<>'' && $usrid<>'ALL'){
				$this->db->where('a.assigned_user',$usrid);
			}
			$this->db->group_start()
			->where('a.df_id', 0) // No DF uploaded yet → show task
			->or_where('b.df_status', 0) // DF exists and is active
			->group_end();

				$query = $this->db->get();
				$res = $query->result();
				foreach($res as $row){

				$pendinddays=$this->task->getDays(date('Y-m-d'),$row->end_date,1);
				// $now = time(); 
				// $remainingdays = strtotime($row->end_date);
				// $datediff = $now - $remainingdays;

				// $pendinddays =  round($datediff / (60 * 60 * 24));
				if($row->po_id>0){
					$dfno = $row->df_no;
					$dfreleasedate = date('d-m-Y',strtotime($row->added_on));
					
					
				}else{
					$dfno = "NA";
					$dfreleasedate = "NA";
				}
				if($row->first_name<>'')
				{
					$username=$row->first_name." ".$row->last_name;
				}else
				{
					$username="<strong style='color:red;'>Task Not Assigned</strong>";
				}
			if($user_id==$row->assigned_user){
			if($row->df_id==0 && $row->taskid==2){

				//$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.'',');">Release DF</span>'; 
				$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.','.$row->df_number.');">Release DF</span>'; 
			}else{
				if($row->is_it_mom==1){
					$updateprogress = '<a href="'.page_url.'Task/dfmeeting/'.$row->id.'/'.$row->df_id.'"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
				}else if($row->df_id==0 && $row->taskid==114){


					if($row->basic_machine==1){
						$updateprogress = '<a href="'.page_url.'Dashboard/basic_machine_df_form/'.$row->id.'/'.$row->po_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF) - Basic Machine</a>';
					}else{

					/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600){
									 $updateprogress = '<a href="'.page_url.'Dashboard/df_form_600/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
							}else{
								if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/powder_df_form_design/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/df_project_form/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}
					}
						
						
					}
					}

					

				}else if($row->df_id==0 && $row->taskid==86){

					if($row->basic_machine==1){
						$updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
							//$updateprogress = '<a href="'.page_url.'Dashboard/edit_basic_machine_df_project_form/'.$row->po_id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';
					}else{
						/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600){

							$updateprogress = '<a href="'.page_url.'Dashboard/design_form_600_edit/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>'; 
						}else{
							if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/edit_powder_df_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						}
						}
						
					}
					}


					 

				}else if($row->df_id==0 && $row->taskid==3){

					/**PI Creation*/

					$q = $this->db->select('patient_type_id')->from('leads')->where('id',$row->lead_id)->get();
					if($q->num_rows()>0){
						foreach($q->result() as $picondition);
						if($picondition->patient_type_id==1){
							$updateprogress = '<a href="'.page_url.'Form/performa_invoice/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">Create PI</a>'; 

							
						}else{
							$updateprogress = '<a href="'.page_url.'Form/export_performa_invoice/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">Create PI</a>'; 
							
						}
					}else{
						$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row->id.','.$row->sortorder.','.$row->df_id.','.$row->taskid.','.$row->start_date.');">Update Progress</span>';
					}

					/**PI Creation*/

					

					

				}else{


					$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row->id.','.$row->sortorder.','.$row->df_id.','.$row->taskid.','.$row->start_date.');">Update Progress</span>';
					
				}			
				
			}
			if($row->df_id==0 && $row->taskid==114){
				//updateprogress = '<span class="btn btn-primary btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.');">Fill Delivery Form (DF)</span>'; 

				if($row->basic_machine==1){
					$updateprogress = '<a href="'.page_url.'Dashboard/basic_machine_df_form/'.$row->id.'/'.$row->po_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)- Basic Machine</a>';
				}else{
					/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600){
									 $updateprogress = '<a href="'.page_url.'Dashboard/df_form_600/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
							}else{
								if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/powder_df_form_design/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/df_project_form/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}
					}

						
					}
				}
				
			}
		}else{
			if($_SESSION['logged_in']['user_id']==61 || $_SESSION['logged_in']['user_id']==161)
				{
					
				if($row->df_id==0 && $row->taskid==2){

				//$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.'',');">Release DF</span>'; 
				$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.','.$row->df_number.');">Release DF</span>'; 
					}else{
				if($row->is_it_mom==1){
					$updateprogress = '<a href="'.page_url.'Task/dfmeeting/'.$row->id.'/'.$row->df_id.'"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
				}else if($row->df_id==0 && $row->taskid==114){

					if($row->basic_machine==1){
						$updateprogress = '<a href="'.page_url.'Dashboard/basic_machine_df_form/'.$row->id.'/'.$row->po_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF) - Basic Machine</a>';
					}else{
						/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600){
									 $updateprogress = '<a href="'.page_url.'Dashboard/df_form_600/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
							}else{
								if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/powder_df_form_design/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/df_project_form/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}
					}
						
						
					}
					}
					
					

				}else if($row->df_id==0 && $row->taskid==86){

					if($row->basic_machine==1){
						$updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						//$updateprogress = '<a href="'.page_url.'Dashboard/edit_basic_machine_df_project_form/'.$row->po_id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>'; 
					}else{
						 /*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600){

							$updateprogress = '<a href="'.page_url.'Dashboard/design_form_600_edit/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>'; 
						}else{
							if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/edit_powder_df_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/edit_df_project_form/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id.'" class="btn btn-sm btn-danger">DF Review Meeting (Update DF)</a>';  
						}
						}
						
					}
					}

					

				}else{


					/** IF PERFORMA INVOICE **/

					if($row->taskid==3)
					{	

					$CheckDomesticExport=$this->CheckDomesticExport($row->lead_id);
					if($CheckDomesticExport==1)
					{
						$pilink=page_url.'Form/performa_invoice/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id;
					}else if($CheckDomesticExport==2)
					{
						$pilink=page_url.'Form/export_performa_invoice/'.$row->po_id.'/'.$row->lead_id.'/'.$row->id;
					}else
					{
						$pilink='';
					}

					$updateprogress = '<a href="'.$pilink.'" class="btn btn-sm btn-danger">CREATE PI</a>';
					}else
					{
						$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row->id.','.$row->sortorder.','.$row->df_id.','.$row->taskid.','.$row->start_date.');">Update Progress</span>';
					}
					
				}			
				
			}
			if($row->df_id==0 && $row->taskid==114){
				//updateprogress = '<span class="btn btn-primary btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.');">Fill Delivery Form (DF)</span>'; 
				if($row->basic_machine==1){
					$updateprogress = '<a href="'.page_url.'Dashboard/basic_machine_df_form/'.$row->id.'/'.$row->po_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF) - Basic Machine</a>';
				}else{
					/*Fetch Product type*/
					$q99 = $this->db->select('b.product_to_be_packed, a.mach_model_no')->from('quotation_customer_data a')->join('quotation_annexture_1 b','a.id=b.record_id','left')->where('a.lead_id',$row->lead_id)->get();
					if($q99->num_rows()>0){
						foreach ($q99->result() as $productpacktype);
						if($productpacktype->mach_model_no==300 || $productpacktype->mach_model_no==600 || $productpacktype->mach_model_no==600){
									 $updateprogress = '<a href="'.page_url.'Dashboard/df_form_600/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>';
							}else{
								if($productpacktype->product_to_be_packed==2){

							 $updateprogress = '<a href="'.page_url.'Dashboard/powder_df_form_design/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}else{
							 $updateprogress = '<a href="'.page_url.'Dashboard/df_project_form/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-danger">Fill Design Form (DF)</a>'; 
						}
					}

						
					}
				}
				
			}

				}else
				{
				$updateprogress='';
				if($_SESSION['logged_in']['adminuser']==2){
					$updateprogress.= '<span class="btn btn-danger btn-xs" onclick="reassigntasktoanotheruser('.$row->id.','.$row->department_id.','.$row->assigned_user.');">Reassign</span>';
				    }
				}
		}

				$show=0;
				/*** FILTER CONDITIONS ***/
			if($filter_req<>'ALL' && $filter_req<>''){
				if($fiterdays==$pendinddays && $fiterdays<>0){
					$show = 1;
				}

			}else{
				$show = 1;
			}

				if ($row->df_id == 0) {
				$dfno = "<span style='color:red;'>DF Not Uploaded</span>";
				$dfreleasedate = "NA";
				$dfupload = "--";
				} else {
				$dfno = strtoupper($row->df_no);
				$dfreleasedate = ($row->added_on && strtotime($row->added_on)) ? date('d-m-Y', strtotime($row->added_on)) : "NA";
				$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span><u>DOWNLOAD DF</u></span></a>';
				}

			//$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span><u>DOWNLOAD DF</u></span></a>';
			$userdepartment_id =$this->session->userdata['logged_in']['department_id'];	
			if($userdepartment_id==9 || $userdepartment_id==20 || $userdepartment_id==10){
				if($row->company_name<>''){
					if($row->df_number<>0){
						$companyname = "DF No.: <strong>".$row->df_number."</strong><br><br>";
					}else{
						$companyname = '';
					}
				
				}else{
					$companyname = "<strong>Basic Machine</strong>";
				}
				
				$pofordownload = $companyname.'<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
			}else{
				$pofordownload  = "";
			}

			if (!empty($row->taskupdatedontime) && $row->taskupdatedontime !== '0000-00-00 00:00:00' && $row->taskupdatedontime !== '1970-01-01 05:30:00' && $row->remarks!='' ) {
			// Validate date
			$timestamp = strtotime($row->taskupdatedontime);
			if ($timestamp !== false) {
			$lastremarksupdateddate = date('d-m-Y', $timestamp);
			$lastremarksupdatedtime = date('h:i A', $timestamp);
			$updatedt = $lastremarksupdateddate . "<br>" . $lastremarksupdatedtime;

			$ticketcountdata = $this->task->checkhelpticket($row->id, $row->df_id);
			if ($ticketcountdata) {
			$ticketlink = '<a href="' . page_url . 'Task/viewdfwiseticket/' . $row->df_id . '/' . $row->id . '"><span class="btn btn-xs btn-primary">View Tickets</span></a>';
			} else {
			$ticketlink = '';
			}
			} else {
			// Handle invalid date
			$updatedt = "";
			$ticketlink = "";
			}
			} else {
			$updatedt = "";
			$ticketlink = "";
			}


			if($show==1){
			$taskdata[] = array('sr_no'=>$i,
			'df_no'=>strtoupper($dfno."<br>".$row->df_description),
			'dfupload'=>$dfupload."<br><br>".$pofordownload."<br> Marketing Person - ".ucwords(strtolower($row->marketingpersonfname." ".$row->marketingpersonlname)),
			'department'=>strtoupper($row->department),
			'membername'=>strtoupper($username),
			'task_name'=>strtoupper($row->task_name),
			'pendingdays'=>"<strong style='color:red;font-weight:bold;'>".$pendinddays." Days</strong>",
			'remarks'=>strtoupper($row->remarks)."<br><br>".$updatedt."<br>".$ticketlink,
			'updateprogress'=>$updateprogress,
			'df_release_date'=>$dfreleasedate,
			'end_date'=>date('d-m-Y',strtotime($row->end_date)));
			$i++;
		}
		}
		
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($taskdata),
			"iTotalDisplayRecords" => count($taskdata),
			"aaData"=>$taskdata);
			
		echo json_encode($results);
	}

	public function pendingtoassigndf()
		{
			$i=1;
			$taskdata= array();
			$departmentid = array();
			$scope = $this->getTaskDashboardScope();
			$user_id = (int) $scope['user_id'];
	$admin_user_type = (int) $scope['admin_user_type'];
	$previous_db_debug = $this->db->db_debug;
	$this->db->db_debug = false;

	if (empty($scope['can_assign_tasks'])) {
		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => 0,
			"iTotalDisplayRecords" => 0,
			"aaData" => array()
		);
		$this->db->db_debug = $previous_db_debug;
		$this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($results));
		return;
	}

	$departmentid = array();

	if ($admin_user_type === 2) {

	    // Special condition: user 215 should also see PPC department
	    if ($user_id == 215) {
	        $departmentid[] = 12; // PPC Department
    }

    // Limit assignment scope to departments where this leader can assign tasks.
	    $departmentid = array_merge($departmentid, $scope['assign_department_ids']);

	    // Optional: remove duplicate department IDs
	    $departmentid = array_unique($departmentid);
	}

			$this->db->select('b.id AS df_id, b.df_no, b.df_upload, b.added_on, b.df_description')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id');
			if($admin_user_type === 2 && empty($departmentid)){
				$this->db->where('1 = 0', null, false);
			}
			if(count($departmentid)>0){
				$this->db->where_in('a.department_id',$departmentid,false);
			}
				$this->db->where('a.department_id!=','22');
			$this->db->where('a.df_id!=',0);
			$this->db->group_start();
			$this->db->where('a.assigned_user', '');
			$this->db->or_where('a.assigned_user', 0);
			$this->db->or_where('a.assigned_user IS NULL', null, false);
			$this->db->group_end();
			$this->db->group_by(array('b.id', 'b.df_no', 'b.df_upload', 'b.added_on', 'b.df_description'))->order_by('b.df_no','asc');
			$this->db->where('b.df_status',0);
			$query = $this->db->get();
			if ($query === false) {
			    log_message('error', 'pendingtoassigndf main query failed: ' . json_encode($this->db->error()));
			    $res = array();
			} else {
			    $res = $query->result();
			}
			foreach($res as $row){

				$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span><u>Click to download DF</u></span></a>';
				$html = "";
			
					 $this->db->select('a.df_id, a.department_id, b.department')->from('task_department_wise_scheduling a')->join('departments b','a.department_id=b.department_id')->where('a.df_id',$row->df_id);
					 $this->db->group_start();
					 $this->db->where('a.assigned_user', '');
					 $this->db->or_where('a.assigned_user', 0);
					 $this->db->or_where('a.assigned_user IS NULL', null, false);
					 $this->db->group_end();

						if($admin_user_type === 2 && empty($departmentid)){
							$this->db->where('1 = 0', null, false);
						}

						if(count($departmentid)>0){
						$this->db->where_in('a.department_id',$departmentid,false);
						}

						$this->db->where('a.department_id!=','22');
						$q =$this->db->group_by(array('a.department_id', 'b.department'))->get();
					if($q !== false && $q->num_rows()>0){
						$a = 1;
						$html.= "<table border='1' style='width:450px; text-align:center'><tr style='background-color:#3b77a6; color:#fff; text-align:center;'><th style='text-align:center; color:#fff;'>Sr No</th><th style='text-align:center; color:#fff;'>Department Name</th><th style='text-align:center; color:#fff;'>Deaprtment Head</th><th style='text-align:center; color:#fff;'>Assign</th></tr>";


					foreach($q->result() as $rows){

							$departmenthead = $this->getDepartmentAssignmentLeaderNames((int) $rows->department_id);
							if ($departmenthead === '') {
								$departmenthead = "<span style='color:red; font-size:14px; font-weight:bold;'>Head Not Defined</span>";
							}

						$updateprogress = '<span style="cursor:pointer;" onclick="assigntasktousers('.(int)$row->df_id.','.(int)$rows->department_id.');"><u>Assign</u></span>';

						$html.="<tr>
							<td>".$a."</td>
							<td>".$rows->department."</td>
							<td>".$departmenthead."</td>
							<td>".$updateprogress."</td>
						</tr>";
					
					$a++;}
					$html.='</table>';
				}

				


			$taskdata[] = array('sr_no'=>$i,
			'df_no'=>strtoupper($row->df_no."<br>".$row->df_description),
			'df_release_date'=>date('d-m-Y',strtotime($row->added_on)),
			'assigment'=>$html,
			'downloaddf'=>$dfupload);
			$i++;
		}
				$results = array(
				"sEcho" => 1,
				"iTotalRecords" => count($taskdata),
				"iTotalDisplayRecords" => count($taskdata),
				"aaData"=>$taskdata);

			$this->db->db_debug = $previous_db_debug;

			$json_flags = 0;
			if (defined('JSON_UNESCAPED_UNICODE')) {
			    $json_flags |= JSON_UNESCAPED_UNICODE;
			}
			if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
			    $json_flags |= JSON_INVALID_UTF8_SUBSTITUTE;
			}
			if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
			    $json_flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
			}

			$json_output = json_encode($results, $json_flags);
			if ($json_output === false) {
			    log_message('error', 'pendingtoassigndf json_encode failed: ' . json_last_error_msg());
			    $json_output = '{"sEcho":1,"iTotalRecords":0,"iTotalDisplayRecords":0,"aaData":[]}';
			}

			while (ob_get_level() > 0) {
			    @ob_end_clean();
			}

			$this->output
			    ->set_content_type('application/json')
			    ->set_output($json_output);
			return;
		}

public function featch_dynamic_tasks()
{
    $html = '';
    $i = 1;
    $dfid = (int)$this->input->post('dfid');
    $department = (int)$this->input->post('department');
    $has_assignable_tasks = false;
    $scope = $this->getTaskDashboardScope();

    if (!$this->canCurrentUserAssignDepartmentTasks($department, $scope)) {
        $html .= '<div class="col-md-12"><div class="alert alert-danger" style="margin-bottom:0;">You do not have permission to assign tasks for this department.</div></div>';
        echo $html;
        exit;
    }
    $user_list = $this->getAssignableUsersForDepartment($department, $scope);

    // --- START: Build Task List HTML ---
    $this->db->select('a.id, a.df_id, b.task_name, c.df_no')
        ->from('task_department_wise_scheduling a')
        ->join('task_management b', 'a.taskid=b.task_id')
        ->join('df_release c', 'a.df_id=c.id')
        ->where('a.task_status', 0)
        ->where('a.df_id', $dfid)
        ->where('a.department_id', $department)
        ->group_start()
        ->where('a.assigned_user', '')
        ->or_where('a.assigned_user', 0)
        ->or_where('a.assigned_user IS NULL', null, false)
        ->group_end()
        ->order_by('b.sortorder', 'asc');

    $q = $this->db->get();
    $has_assignable_tasks = ($q->num_rows() > 0);

    if (!$has_assignable_tasks) {
        $html .= '<div class="col-md-12"><div class="alert alert-warning" style="margin-bottom:0;">No unassigned open tasks are available for this DF and department now. Please refresh the Unassigned DF tab.</div></div>';
        echo $html;
        exit;
    }

    if (empty($user_list)) {
        $empty_user_message = !empty($scope['is_super_admin'])
            ? 'No users are available for task assignment in this department right now.'
            : 'No team members are available for task assignment in this department right now.';
        $html .= '<div class="col-md-12"><div class="alert alert-warning" style="margin-bottom:0;">' . $empty_user_message . '</div></div>';
        echo $html;
        exit;
    }

    foreach ($q->result() as $row) {
        $html .= '
        <div class="col-md-9">
        <div class="form-group">';
        if ($i == 1) {
            $html .= '
        <label for="field-1" class="control-label">Task Name <span id="error_dfno" style="color:red;">*</span></label>';
        }
        $html .= '
        <input type="hidden" value="' . $row->id . '" name="recordid[]">
        <input type="hidden" value="' . $row->df_id . '" name="dfid[]">
        <input type="text" class="form-control" name="dfno" style="padding: 0px 10px;height: 27px; font-size: 12px;" id="dfno" value="' . strtoupper($row->task_name) . '" readonly>
        </div>
        </div>';
        $html .= '<div class="col-md-3">
        <div class="form-group">';
        if ($i == 1) {
            $cl = "firstClass";
            $html .= '<label for="field-1" class="control-label">Assign to <span id="error_dfno" style="color:red;">*</span></label>';
        } else {
            $cl = "";
        }
        $html .= '
        <select class="form-control ' . $cl . ' allassignUser" name="assignuser[]" style="padding: 0px 10px;height: 27px; font-size: 12px;" id="assignuser">
        <option value="">Select User</option>';

        foreach ($user_list as $user) {
            $html .= '<option value="' . (int) $user['user_id'] . '">' . htmlspecialchars($user['label']) . '</option>';
        }

        $html .= '</select>';

        if ($i == 1) {
            // This checkbox logic is kept and will work perfectly
            $html .= '<span><input type="checkbox" name="appall" id="appall" value="1" onchange="applysameUser();">&nbsp;<strong>Apply Same user to all</strong></span>';
        }

        $html .= '</div></div>';
        $i++;
    }

    // --- START: Add Notification/Message Box ---
    $html .= '<div class="col-md-12">
        <div class="form-group">
        <label>Notification Message<span id="errormes" style="color:red;">*</span></label>
        <input type="text" class="form-control" name="message[]" id="message" value="">
        <textarea class="form-control" name="predefinemsg" id="predefinemsg">Hey everyone, a new task has been assigned to me in our task management software. Please log in to check the details. Let me know if you need anything.</textarea>
        
        </div>
    </div>';
    
    echo $html;
    exit;
}

	public function assigntasktoteammember()
{
    $scope = $this->getTaskDashboardScope();
    $user_id = (int) $scope['user_id'];

    if (!isset($_REQUEST['assignuser'])) {
        $this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">No unassigned task was available to assign. Please refresh the dashboard and try again.</div>');
        redirect(page_url . 'Dashboard');
        return;
    }

    $assignuser_array = isset($_REQUEST['assignuser']) ? (array) $_REQUEST['assignuser'] : array();
    $recordid_array = isset($_REQUEST['recordid']) ? (array) $_REQUEST['recordid'] : array();
    $prmsg = isset($_REQUEST['predefinemsg']) ? trim($_REQUEST['predefinemsg']) : '';
    $tags1 = count($assignuser_array);

    if ($tags1 <= 0) {
        $this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">No task was assigned. Please choose at least one team member.</div>');
        redirect(page_url . 'Dashboard');
        return;
    }

    $record_ids_to_query = array_values(array_unique(array_map('intval', $recordid_array)));
    $task_records = array();
    if (!empty($record_ids_to_query)) {
        $task_query = $this->db->select('id, df_id, department_id')
            ->from('task_department_wise_scheduling')
            ->where_in('id', $record_ids_to_query)
            ->get();

        foreach ($task_query->result() as $task_row) {
            $task_records[(int) $task_row->id] = array(
                'df_id' => (int) $task_row->df_id,
                'department_id' => (int) $task_row->department_id
            );
        }
    }

    $q_assigner = $this->db->select('first_name, last_name')->from('system_users')->where('user_id', $user_id)->get()->row();
    $assignedbyuser = $q_assigner ? ucfirst($q_assigner->first_name . " " . $q_assigner->last_name) : "PMS Software";

    $update_batch = array();
    $insert_batch = array();
    $notification_digest = array();
    $department_user_cache = array();

    for ($x = 0; $x < $tags1; $x++) {
        $assigned_user_id = isset($assignuser_array[$x]) ? (int) $assignuser_array[$x] : 0;
        $current_record_id = isset($recordid_array[$x]) ? (int) $recordid_array[$x] : 0;

        if ($assigned_user_id <= 0) {
            continue;
        }

        if ($current_record_id <= 0 || !isset($task_records[$current_record_id])) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Task assignment data was invalid. Please refresh the dashboard and try again.</div>');
            redirect(page_url . 'Dashboard');
            return;
        }

        $task_record = $task_records[$current_record_id];
        $task_department_id = (int) $task_record['department_id'];
        $current_df_id = (int) $task_record['df_id'];

        if (!$this->canCurrentUserAssignDepartmentTasks($task_department_id, $scope)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">You do not have permission to assign tasks for one or more selected departments.</div>');
            redirect(page_url . 'Dashboard');
            return;
        }

        if (!isset($department_user_cache[$task_department_id])) {
            $department_user_cache[$task_department_id] = array();
            $assignable_users = $this->getAssignableUsersForDepartment($task_department_id, $scope);
            foreach ($assignable_users as $assignable_user) {
                $department_user_cache[$task_department_id][(int) $assignable_user['user_id']] = $assignable_user;
            }
        }

        if (!isset($department_user_cache[$task_department_id][$assigned_user_id])) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Selected assignee is not available for the chosen department.</div>');
            redirect(page_url . 'Dashboard');
            return;
        }

        $update_batch[] = array(
            'id' => $current_record_id,
            'df_id' => $current_df_id,
            'assigned_user' => $assigned_user_id,
            'assigned_by' => $user_id,
            'assigned_on' => date('Y-m-d H:i:s')
        );

        $insert_batch[] = array(
            'df_id' => $current_df_id,
            'message' => $prmsg,
            'added_by' => $user_id,
            'added_on' => date('Y-m-d H:i:s'),
            'department_id' => $task_department_id,
            'user_id' => $assigned_user_id
        );

        $notification_digest[$assigned_user_id]['df_ids'][] = $current_df_id;
    }

    if (empty($update_batch)) {
        $this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">No task was assigned. Please choose at least one team member.</div>');
        redirect(page_url . 'Dashboard');
        return;
    }

    $this->db->trans_start();
    $this->db->update_batch('task_department_wise_scheduling', $update_batch, 'id');
    if (!empty($insert_batch)) {
        $this->db->insert_batch('task_intimation_alert', $insert_batch);
    }
    $this->db->trans_complete();

    if ($this->db->trans_status() === false) {
        $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Task assignment could not be completed. Please try again.</div>');
        redirect(page_url . 'Dashboard');
        return;
    }

    if (!empty($notification_digest)) {
        $all_df_ids = array();
        foreach ($notification_digest as $notification_user_id => $data) {
            $all_df_ids = array_merge($all_df_ids, $data['df_ids']);
        }

        $df_cache = array();
        if (!empty($all_df_ids)) {
            $df_query = $this->db->select('id, df_no')->from('df_release')->where_in('id', array_unique($all_df_ids))->get();
            foreach ($df_query->result() as $df_row) {
                $df_cache[(int) $df_row->id] = $df_row->df_no;
            }
        }

        foreach ($notification_digest as $notification_user_id => $data) {
            $user_df_no_list = array();
            foreach (array_unique($data['df_ids']) as $df_id) {
                $user_df_no_list[] = $df_cache[$df_id] ?? 'DF#' . $df_id;
            }

            $this->task->send_digest_notification((int) $notification_user_id, $user_df_no_list, $prmsg, $assignedbyuser);
        }
    }

    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You!, Task has been Assigned.</div>');
    redirect(page_url . 'Dashboard');
}

	public function checknotification(){
		$departmentid = $this->input->post('departmentid');
		$userid = $this->input->post('userid');
		$message = array();
		$q = $this->db->select('a.message, b.department, c.df_no, d.first_name, d.last_name')->from('task_intimation_alert a')->join('departments b','a.department_id=b.department_id')->join('df_release c','a.df_id=c.id','left')->join('system_users d','a.user_id=d.user_id','left')
		//->where('department_id',$departmentid)->where('user_id',$userid)
		->where('a.status',0)->order_by('a.added_on','asc')->limit(2)->get();
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$name = ucwords($row->first_name." ".$row->last_name);
				$find = array('{df_number}','{department_name}', '{department_hod}');
							$replace = array(ucwords(strtolower($row->df_no)), ucwords(strtolower($row->department)), ucwords(strtolower($name)));
					$message_body = str_replace($find, $replace, $row->message);

			$message[] =  array('notification'=>$row->message);
			}

		}

		echo json_encode($message);
	}


	public function notificationuserwiseOLdd(){
		$departmentid = $this->input->post('departmentid');
		$userid = $this->input->post('userid');
		$i=0;
		$message = "";
		$q = $this->db->select('a.message, b.department, c.df_no, d.first_name, d.last_name, a.added_on')->from('task_intimation_alert a')->join('departments b','a.department_id=b.department_id')->join('df_release c','a.df_id=c.id','left')->join('system_users d','a.user_id=d.user_id','left')
		//->where('department_id',$departmentid)->where('user_id',$userid)
		->where('a.status',0)->order_by('a.added_on','asc')->get();
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$name = ucwords($row->first_name." ".$row->last_name);
				$find = array('{df_number}','{department_name}', '{department_hod}');
							$replace = array(ucwords(strtolower($row->df_no)), ucwords(strtolower($row->department)), ucwords(strtolower($name)));
					$message_body = str_replace($find, $replace, $row->message);
					
					$addedondatetime = date('d-m-Y',strtotime($row->added_on))."<br>".date('h:i A',strtotime($row->added_on));

					if($i%2<>0)
                            {
                                $back="#d0fffe";
                            }else
                            {
                                 $back="#fffddb";
                            }

							$message.= ' <div class="top_box" style="background-color:'.$back.'; margin-bottom:20px">
							<p>'.$message_body.'</p>
							<div class="time_btn">
							<div class="row" style="align-items: right;">
							<p>'.$addedondatetime.'</p>
							</div>

							</div>
							</div>';
              $i++;
			}

		}
		echo $message;		
	}

	public function unassignednotificationcount(){
		$scope = $this->getTaskDashboardScope();
		$department_id = !empty($scope['can_assign_tasks']) ? $scope['assign_department_ids'] : array();
		if (empty($scope['can_assign_tasks'])) {
			echo 0; exit;
		}


		$count = 0;
		$this->db->select('a.df_id')
			->from('task_department_wise_scheduling a')
			->join('df_release b', 'a.df_id=b.id')
			->where('a.task_status',0)
			->where('a.df_id!=',0)
			->where('a.department_id!=','22')
			->where('b.df_status',0)
			->group_start()
			->where('a.assigned_user', '')
			->or_where('a.assigned_user', 0)
			->or_where('a.assigned_user IS NULL', null, false)
			->group_end()
			->group_by('a.df_id');
		if(count($department_id)>0)
			{
				$this->db->where_in('a.department_id',$department_id,'false');
			}
		$q = $this->db->get();
		if($q->num_rows()>0){
			$count = count($q->result());
		}
		echo $count; exit;
	}

	public function ongoingtaskcountnotification(){
		$previous_db_debug = $this->db->db_debug;
		$this->db->db_debug = false;
		$count = 0;
		$department_id = array();
		$scope = $this->getTaskDashboardScope();
		$user_id = (int) $scope['user_id'];
		$admin_user_type = (int) $scope['admin_user_type'];
		$department_id = !empty($scope['can_view_team_tasks']) ? $scope['view_department_ids'] : array();
		$personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($user_id);

		$this->db->select('a.id')
			->from('task_department_wise_scheduling a')
			->join('df_release b', 'a.df_id=b.id', 'left')
			->where('a.task_status', 0)
			->where('a.on_hold', 0)
			->where('a.assigned_user !=', 0)
			->where('a.end_date >=', date('Y-m-d'))
			->where('a.department_id !=', 22)
			->group_start()
			->where('a.df_id', 0)
			->or_where('b.df_status', 0)
			->group_end();

		if (count($department_id) > 0) {
			$this->db->group_start();
			$this->db->where_in('a.department_id', $department_id);
			$this->applyTaskAssignedUserOrFilter('a.assigned_user', $personal_visible_user_ids);
			$this->db->group_end();
		}
		if (!empty($scope['personal_task_only'])) {
			$this->applyTaskAssignedUserFilter('a.assigned_user', $personal_visible_user_ids);
		}

		$query = $this->db->get();
		if ($query === false) {
			log_message('error', 'ongoingtaskcountnotification query failed: ' . json_encode($this->db->error()));
		} else {
			$count = (int)$query->num_rows();
		}

		$this->db->db_debug = $previous_db_debug;
		while (ob_get_level() > 0) {
			@ob_end_clean();
		}
		$this->output->set_content_type('text/plain')->set_output((string)$count);
		return;
	}

public function overduetaskcountnotification(){
		$previous_db_debug = $this->db->db_debug;
		$this->db->db_debug = false;
		$count = 0;
		$department_id = array();
		$scope = $this->getTaskDashboardScope();
		$user_id = (int) $scope['user_id'];
		$admin_user_type = (int) $scope['admin_user_type'];
		$department_id = !empty($scope['can_view_team_tasks']) ? $scope['view_department_ids'] : array();
		$personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($user_id);

		$this->db->select('a.id')
			->from('task_department_wise_scheduling a')
			->join('df_release b', 'a.df_id=b.id', 'left')
			->where('a.on_hold', 0)
			->where('a.task_status', 0)
			->where('a.end_date <', date('Y-m-d'))
			->where('a.department_id !=', 22)
			->group_start()
			->where('a.df_id', 0)
			->or_where('b.df_status', 0)
			->group_end();

		if (count($department_id) > 0) {
			$this->db->group_start();
			$this->db->where_in('a.department_id', $department_id);
			$this->applyTaskAssignedUserOrFilter('a.assigned_user', $personal_visible_user_ids);
			$this->db->group_end();
		}
		if (!empty($scope['personal_task_only'])) {
			$this->applyTaskAssignedUserFilter('a.assigned_user', $personal_visible_user_ids);
		}

		$query = $this->db->get();
		if ($query === false) {
			log_message('error', 'overduetaskcountnotification query failed: ' . json_encode($this->db->error()));
		} else {
			$count = (int)$query->num_rows();
		}

		$this->db->db_debug = $previous_db_debug;
		while (ob_get_level() > 0) {
			@ob_end_clean();
		}
		$this->output->set_content_type('text/plain')->set_output((string)$count);
		return;
	}

	public function completeddfnotificationcount(){
		$count = 0;
		$scope = $this->getTaskDashboardScope();
		$department_id = !empty($scope['can_view_team_tasks']) ? $scope['view_department_ids'] : array();
		$personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($scope['user_id']);

		$q = $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.task_status',1)->where('a.on_hold',0);
		if(count($department_id)>0)
			{
				$this->db->group_start();
				$this->db->where_in('a.department_id',$department_id,'false');
				$this->applyTaskAssignedUserOrFilter('a.assigned_user', $personal_visible_user_ids);
				$this->db->group_end();
			}
			if(!empty($scope['personal_task_only']))
			{
				$this->applyTaskAssignedUserFilter('a.assigned_user', $personal_visible_user_ids);
			}
			$this->db->limit(100);
		$q=$this->db->get();
		if($q->num_rows()>0){
			$count = count($q->result());
		}
		echo $count; exit;
	}

	public function completeddfnotificationcountpendingforapproval(){
		$count = 0;
		$scope = $this->getTaskDashboardScope();
		$department_id = !empty($scope['can_view_team_tasks']) ? $scope['view_department_ids'] : array();
		$personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($scope['user_id']);

		$q = $this->db->select('a.id')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->where('a.task_status',2)->where('a.on_hold',0);
		if(count($department_id)>0)
			{
				$this->db->group_start();
				$this->db->where_in('a.department_id',$department_id,'false');
				$this->applyTaskAssignedUserOrFilter('a.assigned_user', $personal_visible_user_ids);
				$this->db->group_end();
			}
			if(!empty($scope['personal_task_only']))
			{
				$this->applyTaskAssignedUserFilter('a.assigned_user', $personal_visible_user_ids);
			}

		$q=$this->db->get();
		if($q->num_rows()>0){
			$count = count($q->result());
		}
		echo $count; exit;
	}


public function completeddf()
{
    $previous_db_debug = $this->db->db_debug;
    $this->db->db_debug = false;
    // --- 1. Prepare Filters ---
    $scope = $this->getTaskDashboardScope();
    $user_id = (int) $scope['user_id'];
    $admin_user_type = (int) $scope['admin_user_type'];
    $department_id = !empty($scope['can_view_team_tasks']) ? $scope['view_department_ids'] : array();
    $personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($user_id);

    $departmentids = $this->uri->segment(3);
    $userids = $this->uri->segment(4);
    $dfnos = $this->uri->segment(5);

    // --- 2. Build Optimized Query (Comments Removed) ---
    $this->db->select('
        a.id, a.taskid, a.df_id, a.po_id, a.department_id, a.assigned_user, 
        a.task_completed_on, a.end_date, a.remarks, a.task_status, a.task_completed_by,
        
        b.task_name, 
        
        c.df_no, c.df_upload, c.added_on, c.df_description, c.on_hold,
        
        d.first_name AS completed_first_name, d.last_name AS completed_last_name, 
        
        e.department, 
        
        f.first_name, f.last_name,
        
        p.lead_id, p.id as po_id,
        
        l.patient_type_id
    ');

    $this->db->from('task_department_wise_scheduling a');
    $this->db->join('task_management b', 'a.taskid = b.task_id', 'left');
    $this->db->join('df_release c', 'a.df_id = c.id', 'left');
    $this->db->join('system_users d', 'a.task_completed_by = d.user_id', 'left');
    $this->db->join('departments e', 'a.department_id = e.department_id', 'left');
    $this->db->join('system_users f', 'a.assigned_user = f.user_id', 'left');
    $this->db->join('poreceived p', 'a.po_id = p.id', 'left');
    
    // Optimized Join
    $this->db->join('leads l', 'p.lead_id = l.id', 'left');

    // Base Conditions
    $this->db->where('c.on_hold', 0);
    $this->db->where('a.task_status', 1); // Completed tasks

    // Apply Filters
    if (count($department_id) > 0) {
        $this->db->group_start();
        $this->db->where_in('a.department_id', $department_id);
        $this->applyTaskAssignedUserOrFilter('a.assigned_user', $personal_visible_user_ids);
        $this->db->group_end();
    }

    if (!empty($scope['personal_task_only'])) {
        $this->applyTaskAssignedUserFilter('a.assigned_user', $personal_visible_user_ids);
    }

    if ($dfnos != '' && $dfnos != 'ALL') {
        $this->db->where('a.df_id', $dfnos);
    }
    if ($departmentids != '' && $departmentids != 'ALL') {
        $this->db->where('a.department_id', $departmentids);
    }
    if ($userids != '' && $userids != 'ALL') {
        $this->db->where('a.assigned_user', $userids);
    }

    $this->db->order_by('a.task_completed_on', 'desc');
    $this->db->limit(100);

    $query = $this->db->get();
    if ($query === false) {
        log_message('error', 'completeddf query failed: ' . json_encode($this->db->error()));
        $res = array();
    } else {
        $res = $query->result();
    }

    // --- 3. Process Data ---
    $dfdata = array();
    $i = 1;

    foreach ($res as $row) {
        
        // Download Link
        $dfupload = '<a href="' . sfdocument . 'Taskdocument/dfattachment/' . $row->df_upload . '" download><span class=""><u>DOWNLOAD</u></span></a>';

        // Delay Calculation
        $task_completed_date = date('Y-m-d', strtotime($row->task_completed_on));
        
        if (strtotime($row->end_date) < strtotime($row->task_completed_on)) {
            // Delayed
            $pendinddays = $this->task->getDays($row->end_date, $task_completed_date, 2);
            $status = "Delayed";
            $task_delay_display = "<strong style='color:red;font-weight:bold;'>{$pendinddays} Days</strong>";
        } else {
            // On Time / Early
            $pendinddays = $this->task->getDays($row->end_date, $task_completed_date, 1);
            $status = ($pendinddays > 0) ? "Early" : "On Time";
            $task_delay_display = "<strong style='color:black;font-weight:bold;'>{$pendinddays} Days</strong>";
        }

        // Admin Date Change Input
        $end_dateChange = '';
        if ($user_id == 61 || $user_id == 161) {
            $end_dateChange = "<div class='col-md-12'><input type='date' name='doneadate" . $row->id . "' id='donedate" . $row->id . "' class='form-control' onchange='changeDoneDate(" . $row->id . ");'></div>";
        }

        // PI Link Logic
        $previewpi = '';
        $editpi = '';
        
        if ($row->taskid == 3) {
            if ($row->patient_type_id == 1) {
                $previewpi = '<a href="' . page_url . 'Formats/performa_invoice/' . $row->po_id . '/' . $row->lead_id . '"><span class="btn btn-primary btn-xs">Click to View PI</span></a>';
                $editpi = '<a href="' . page_url . 'Form/edit_performa_invoice/' . $row->po_id . '/' . $row->lead_id . '"><span class="btn btn-primary btn-xs">Click to Edit PI</span></a>';
            } else {
                $previewpi = '<a href="' . page_url . 'Formats/export_performa_invoice/' . $row->po_id . '/' . $row->lead_id . '"><span class="btn btn-primary btn-xs">Click to View PI</span></a>';
                $editpi = '<a href="' . page_url . 'Form/edit_export_performa_invoice/' . $row->po_id . '/' . $row->lead_id . '"><span class="btn btn-primary btn-xs">Click to Edit PI</span></a>';
            }
        }

        $manage_link = '';
        if ($user_id == 61 || $user_id == 161) {
            $manage_link = '<br><br><a href="' . page_url . 'Task_completion_control/view/' . $row->id . '" class="btn btn-warning btn-xs">Manage</a>';
        }

        // Final Array Construction
        $dfdata[] = array(
            'sr_no' => $i,
            'df_no' => strtoupper($row->df_no . "<br>" . $row->df_description),
            'dfupload' => $dfupload . "<br><br>" . $previewpi . "<br><br>" . $editpi . $manage_link,
            'department' => strtoupper($row->department),
            'membername' => strtoupper($row->first_name . " " . $row->last_name),
            'df_release_date' => date('d-m-Y', strtotime($row->added_on)),
            'taskname' => strtoupper($row->task_name),
            'tasktat' => date('d-m-Y', strtotime($row->end_date)),
            'taskdelay' => $task_delay_display,
            'status' => $status,
            'remarks' => strtoupper($row->remarks),
            'addedon' => date('d-m-Y', strtotime($row->task_completed_on)) . "<br/><br/>" . $end_dateChange,
            'addedby' => strtoupper($row->completed_first_name . " " . $row->completed_last_name)
        );
        
        $i++;
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($dfdata),
        "iTotalDisplayRecords" => count($dfdata),
        "aaData" => $dfdata
    );

    $this->db->db_debug = $previous_db_debug;

    $json_flags = 0;
    if (defined('JSON_UNESCAPED_UNICODE')) {
        $json_flags |= JSON_UNESCAPED_UNICODE;
    }
    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $json_flags |= JSON_INVALID_UTF8_SUBSTITUTE;
    }
    if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
        $json_flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
    }

    $json_output = json_encode($results, $json_flags);
    if ($json_output === false) {
        log_message('error', 'completeddf json_encode failed: ' . json_last_error_msg());
        $json_output = '{"sEcho":1,"iTotalRecords":0,"iTotalDisplayRecords":0,"aaData":[]}';
    }

    while (ob_get_level() > 0) {
        @ob_end_clean();
    }

    $this->output
        ->set_content_type('application/json')
        ->set_output($json_output);
    return;
}
	public function completeddfbeforquery()
{
    $department_id = array();
    $self_user = 0;

    if ($_SESSION['logged_in']['adminuser'] == 2) {
        $department_id = $this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
    }

    $i = 1;
    $dfdata = array();
    $this->db->select('a.id,a.taskid, f.first_name, f.last_name, a.task_completed_on, b.task_name, c.df_no, c.df_upload, c.added_on, d.first_name AS completed_first_name, d.last_name AS completed_last_name, e.department, a.end_date, c.df_description, a.remarks, p.lead_id, p.id as po_id')
        ->from('task_department_wise_scheduling a')
        ->join('task_management b', 'a.taskid = b.task_id', 'left')
        ->join('df_release c', 'a.df_id = c.id', 'left')
        ->join('system_users d', 'a.task_completed_by = d.user_id', 'left')
        ->join('departments e', 'a.department_id = e.department_id', 'left')
        ->join('system_users f', 'a.assigned_user = f.user_id', 'left')
        ->join('poreceived p','a.po_id=p.id','left')
        ->where('c.on_hold',0)
        ->where('a.task_status', 1);

    if (count($department_id) > 0) {
        $this->db->group_start();
        $this->db->where_in('a.department_id', $department_id, 'false');
        $this->db->or_where('a.assigned_user', $current_user_id);
        $this->db->group_end();
    }
    if ($_SESSION['logged_in']['adminuser'] == 3) {
        $this->db->where('a.assigned_user', $_SESSION['logged_in']['user_id']);
    }

    $departmentids = $this->uri->segment(3);
    $userids = $this->uri->segment(4);
    $dfnos = $this->uri->segment(5);

    if ($dfnos != '' && $dfnos != 'ALL') {
        $this->db->where('a.df_id', $dfnos);
    }
    if ($departmentids != '' && $departmentids != 'ALL') {
        $this->db->where('a.department_id', $departmentids);
    }
    if ($userids != '' && $userids != 'ALL') {
        $this->db->where('a.assigned_user', $userids);
    }

    $this->db->order_by('a.task_completed_on', 'desc')->limit(100);
    $query = $this->db->get();
    $res = $query->result();

    foreach ($res as $row) {
        // Generate download link for DF attachment
        $dfupload = '<a href="' . sfdocument . 'Taskdocument/dfattachment/' . $row->df_upload . '" download><span class=""><u>DOWNLOAD</u></span></a>';

        // Calculate delay or early completion
        if (strtotime($row->end_date) < strtotime($row->task_completed_on)) {
            $pendinddays = $this->task->getDays($row->end_date, date('Y-m-d', strtotime($row->task_completed_on)), 2);
            $status = "Delayed";
            // Set delay count in red color if delayed
            $task_delay_display = "<strong style='color:red;font-weight:bold;'>{$pendinddays} Days</strong>";
        } else {
            $pendinddays = $this->task->getDays($row->end_date, date('Y-m-d', strtotime($row->task_completed_on)), 1);
            $status = ($pendinddays > 0) ? "Early" : "On Time";
            // Set delay count in black color if on time or early
            $task_delay_display = "<strong style='color:black;font-weight:bold;'>{$pendinddays} Days</strong>";
        }

        // Allow changing end date if user ID is 61 or 161
        if ($_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161) {
            $end_dateChange = "<div class='col-md-12'><input type='date' name='doneadate" . $row->id . "' id='donedate" . $row->id . "' class='form-control' onchange='changeDoneDate(" . $row->id . ");'></div>";
        } else {
            $end_dateChange = '';
        }

        if($row->taskid==3){
        	$q= $this->db->select('patient_type_id')->from('leads')->where('id',$row->lead_id)->get();
        	if($q->num_rows()>0){
        		foreach($q->result() as $pitype);
        		if($pitype->patient_type_id==1){
        			$previewpi ='<a href="'.page_url.'Formats/performa_invoice/'.$row->po_id.'/'.$row->lead_id.'"><span class="btn btn-primary btn-xs">Click to View PI</span></a>';
        			$editpi ='<a href="'.page_url.'Form/edit_performa_invoice/'.$row->po_id.'/'.$row->lead_id.'"><span class="btn btn-primary btn-xs">Click to Edit PI</span></a>';

        		}else{
        			$previewpi ='<a href="'.page_url.'Formats/export_performa_invoice/'.$row->po_id.'/'.$row->lead_id.'"><span class="btn btn-primary btn-xs">Click to View PI</span></a>';
        			$editpi ='<a href="'.page_url.'Form/edit_export_performa_invoice/'.$row->po_id.'/'.$row->lead_id.'"><span class="btn btn-primary btn-xs">Click to Edit PI</span></a>';

        			
        		}
        	}
        	
        
        }else{
        	$previewpi = '';
        	$editpi ='';
        }


        $manage_link = '';
        if ($_SESSION['logged_in']['user_id'] == 61 || $_SESSION['logged_in']['user_id'] == 161) {
            $manage_link = '<br><br><a href="' . page_url . 'Task_completion_control/view/' . $row->id . '" class="btn btn-warning btn-xs">Manage</a>';
        }

        $completedon = date('Y-m-d H:i A', strtotime($row->task_completed_on));
        $dfdata[] = array(
            'sr_no' => $i,
            'df_no' => strtoupper($row->df_no . "<br>" . $row->df_description),
            'dfupload' => $dfupload."<br><br>".$previewpi."<br><br>".$editpi.$manage_link,
            'department' => strtoupper($row->department),
            'membername' => strtoupper($row->first_name . " " . $row->last_name),
            'df_release_date' => date('d-m-Y', strtotime($row->added_on)),
            'taskname' => strtoupper($row->task_name),
            'tasktat' => date('d-m-Y', strtotime($row->end_date)),
            'taskdelay' => $task_delay_display,
            'status' => $status,
            'remarks' => strtoupper($row->remarks),
            'addedon' => date('d-m-Y', strtotime($row->task_completed_on)) . "<br/><br/>" . $end_dateChange,
            'addedby' => strtoupper($row->completed_first_name . " " . $row->completed_last_name)
        );
        $i++;
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($dfdata),
        "iTotalDisplayRecords" => count($dfdata),
        "aaData" => $dfdata
    );

    echo json_encode($results);
}

public function updatetaskremarks()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $id = $this->input->post('taskkiid');
    $mastertaskid = $this->input->post('mastertaskid'); // This is taskid
    $status = $this->input->post('taskstatus');
    $taskremarks = $this->input->post('taskremarks');
    $dfid = $this->input->post('progressdfno');
    $scope = $this->getTaskDashboardScope();

    $this->db->select('tdws.start_date, tdws.remarks, tdws.taskupdatedontime, tdws.assigned_user, m.df_meeting_close, m.isitfinalstep');
    $this->db->from('task_department_wise_scheduling tdws');
    $this->db->join('task_management m', 'tdws.taskid = m.task_id', 'left');
    $this->db->where('tdws.id', $id);
    $task_info = $this->db->get()->row();

    if (!$task_info) {
        echo "Invalid Response: Task not found.";
        exit;
    }

    $can_update_task = ((int) $user_id === 61 || (int) $user_id === 161)
        || $this->canCurrentUserUpdateOwnOrCoordinatorTask((int) $task_info->assigned_user, $scope);

    if (!$can_update_task) {
        $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry!, You are not allowed to update this task.</div>');
        redirect(page_url . 'Dashboard');
    }

    if ((int) $status === 1 && $this->isPunchPointWorkflowTaskId((int) $mastertaskid)) {
        $workflow_task_row = $this->getPunchPointClosureTaskRecord((int) $id, $scope, array(50, 51, 11));

        if (empty($workflow_task_row)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry!, Punch point workflow task was not found or you do not have access.</div>');
            redirect(page_url . 'Dashboard');
        }

        $closure_items = $this->getPunchPointClosureItems($workflow_task_row);
        $workflow_state = $this->getPunchPointWorkflowState($workflow_task_row, $closure_items);

        if (empty($workflow_state['can_complete_task'])) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">' . htmlspecialchars($workflow_state['block_message'], ENT_QUOTES, 'UTF-8') . '</div>');
            redirect(page_url . 'Task/punch_point_closure/' . (int) $id);
        }

        $completion_note = trim((string) $taskremarks);
        if ($completion_note === '') {
            $completion_note = 'Auto-closed from ' . $this->getPunchPointWorkflowLabel((int) $mastertaskid);
        }

        $completion_status = $this->completePunchPointClosureParentTask($workflow_task_row, (int) $user_id, $completion_note);
        $success_label = $this->getPunchPointWorkflowLabel((int) $mastertaskid);

        if ($completion_status === 2) {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">' . htmlspecialchars($success_label, ENT_QUOTES, 'UTF-8') . ' has been submitted and sent for approval.</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">' . htmlspecialchars($success_label, ENT_QUOTES, 'UTF-8') . ' has been marked done successfully.</div>');
        }

        redirect(page_url . 'Task/punch_point_closure/' . (int) $id);
    }

    // --- Special handling (No changes) ---
    if ($mastertaskid == 3) {
        $result = $this->updatecreationofperformainvoice($id, $mastertaskid, $status, $taskremarks);
        if ($result) {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
            redirect(page_url . 'Dashboard');
        }
    } else if ($mastertaskid == 87) {
        $result = $this->updatedfreviewmeetingapprove($id, $mastertaskid, $status, $taskremarks);
        if ($result) {
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
            redirect(page_url . 'Dashboard');
        }
    } else {

        /*If task status is pending*/
        if ($status == 0 && $taskremarks <> '') {
            $data = [
                'task_status' => $status,
                'taskupdatedontime' => date('Y-m-d H:i:s'),
                'remarks' => $taskremarks
            ];

            // Use the $task_info we already fetched
            if (!empty($task_info->remarks)) {
                $datass = [
                    'recordid' => $id,
                    'taskupdatedontime' => date('Y-m-d H:i:s'), // Current time of logging
                    'remarks' => $task_info->remarks,          // The OLD remarks
                    'added_on' => date('Y-m-d H:i:s'),
                    'added_by' => $user_id
                ];
                $this->db->insert('task_pending_status', $datass);
            }

            $this->db->where('id', $id);
            $this->db->update('task_department_wise_scheduling', $data);
            
            // This part is external, no changes
            $ticketselection = $this->input->post('ticketcondition');
            if ($ticketselection == 1) {
                $selecteddepartmentid = $this->input->post('selectdepartment');
                $selecteduserinfo = $this->input->post('departmentuser');
                $this->task->createnewticket($selecteddepartmentid, $selecteduserinfo, $dfid, $mastertaskid, $id, $taskremarks);
            }

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
            redirect(page_url . 'Dashboard');

        /*Work completed*/
        } else if ($status <> '' && $status == 1) {
            
            // Use the $task_info we fetched earlier
            $start_date = $task_info->start_date;
            $tdadate = date('Y-m-d');

            if ($start_date > $tdadate) {
                $precloserdata = [
                    'df_id' => $dfid,
                    'task_id' => $mastertaskid,
                    'record_id' => $id,
                    'added_on' => date('Y-m-d H:i:s'),
                    'added_by' => $user_id,
                    'remarks' => $taskremarks,
                    'current_status' => 0
                ];
                $this->db->insert('precloser_task_request', $precloserdata);

                $dataupdate11 = [
                    'task_status' => 2, // Pending Approval
                    'task_completed_on' => date('Y-m-d H:i:s'),
                    'taskupdatedontime' => date('Y-m-d H:i:s'),
                    'task_completed_by' => $user_id
                ];
                $this->db->where('id', $id);
                $this->db->update('task_department_wise_scheduling', $dataupdate11);

                // --- START QUEUE FIX ---
                // OLD SLOW CALL: $this->preclosertasknotification(...)
                
                // NEW FAST QUEUE INSERT:
                $payload = json_encode([
                    'df_id' => $dfid,
                    'task_id' => $mastertaskid,
                    'user_id' => $user_id,
                    'remarks' => $taskremarks
                ]);
                $this->db->insert('notification_queue', [
                    'notification_type' => 'pre_closer',
                    'payload' => $payload,
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                // --- END QUEUE FIX ---

                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable" style="color:black; font-size:25px;">Thank You!, Record successfully updated and pending for Approval</div>');
                redirect(page_url . 'Dashboard');
            }

            // If not pre-closer, continue with normal completion
            $data = [
                'task_status' => $status,
                'task_completed_on' => date('Y-m-d H:i:s'),
                'taskupdatedontime' => date('Y-m-d H:i:s'),
                'task_completed_by' => $user_id
            ];
            $this->db->where('id', $id);
            $this->db->update('task_department_wise_scheduling', $data);

            // --- START QUEUE FIX ---
            // OLD SLOW CALLS:
            // $this->task->getmessageoftaskcompletionandtrigger(...);
            // $this->task->checkiftaskispaymentstage(...);
            // $this->task->getdepartmentuserwhomdfassigned(...);
            
            // NEW FAST QUEUE INSERT (to handle all completion logic):
            $payload = json_encode([
                'record_id' => $id,
                'mastertaskid' => $mastertaskid,
                'user_id' => $user_id,
                'dfid' => $dfid
            ]);
            $this->db->insert('notification_queue', [
                'notification_type' => 'task_completion',
                'payload' => $payload,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            // --- END QUEUE FIX ---

            // Use the $task_info we fetched earlier
            if ($task_info->df_meeting_close == 1) {
                $data3 = [
                    'task_status' => 1,
                    'task_completed_on' => date('Y-m-d H:i:s'),
                    'taskupdatedontime' => date('Y-m-d H:i:s')
                ];
                $this->db->where('taskid', 4); // Closes "DF Review Meeting" task
                $this->db->where('df_id', $dfid);
                $this->db->update('task_department_wise_scheduling', $data3);
            }

            // Use the $task_info we fetched earlier
            if ($task_info->isitfinalstep == 1) {
                $dfdataarray = [
                    'df_status' => 1,
                    'completed_on' => date('Y-m-d H:i:s'),
                    'completed_by' => $user_id
                ];
                $this->db->where('id', $dfid);
                $this->db->update('df_release', $dfdataarray);
            }

            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
            redirect(page_url . 'Dashboard');

        /*If task send on previous step*/
        } else if ($status <> '' && $status == 2) {

            // Use the $task_info we already fetched
            if (!empty($task_info->remarks)) {
                $datass = [
                    'recordid' => $id,
                    'taskupdatedontime' => date('Y-m-d H:i:s'),
                    'remarks' => $task_info->remarks, // The OLD remarks
                    'added_on' => date('Y-m-d H:i:s'),
                    'added_by' => $user_id
                ];
                $this->db->insert('task_pending_status', $datass);
            }

            $previous_task_id = $this->input->post('previousstep');
            
            // Get data for copying
            $q = $this->db->select('df_id, taskid, department_id, start_date, end_date, po_id, userid, assigned_user')
                         ->from('task_department_wise_scheduling')
                         ->where('id', $id) // Use the current ID
                         ->get();
            
            if ($q->num_rows() > 0) {
                $row3 = $q->row();
                
                $data = [
                    'df_id' => $row3->df_id,
                    'taskid' => $row3->taskid,
                    'department_id' => $row3->department_id,
                    'start_date' => $row3->start_date,
                    'end_date' => $row3->end_date,
                    'added_on' => date('Y-m-d H:i:s'),
                    'added_by' => $user_id,
                    'po_id' => $row3->po_id,
                    'task_status' => 0,
                    'remarks' => $taskremarks, // The NEW remarks
                    'taskupdatedontime' => date('Y-m-d H:i:s'),
                    'userid' => $row3->userid,
                    'assigned_user' => $row3->assigned_user,
                    'assigned_by' => $user_id, // Assigned by the current user
                    'assigned_on' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('task_department_wise_scheduling', $data);
                $lastinsertid = $this->db->insert_id();

                // Now update the *previous* task
                $data1 = ['task_status' => 0, 'remarks' => $taskremarks];
                $this->db->where('id', $previous_task_id);
                $this->db->update('task_department_wise_scheduling', $data1);

                // --- START QUEUE FIX ---
                // OLD SLOW CALL: $this->task->addnotification(...)
                
                // NEW FAST QUEUE INSERT:
                $payload = json_encode([
                    'df_id' => $row3->df_id,
                    'task_remark' => $taskremarks,
                    'user_id' => $user_id,
                    'department_id' => $row3->department_id,
                    'assigned_user' => $row3->assigned_user,
                    'last_insert_id' => $lastinsertid
                ]);
                $this->db->insert('notification_queue', [
                    'notification_type' => 'previous_step',
                    'payload' => $payload,
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                // --- END QUEUE FIX ---
                
                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
                redirect(page_url . 'Dashboard');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry!, Could not find task data to copy.</div>');
                redirect(page_url . 'Dashboard');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry!, There is some technical issue.</div>');
            redirect(page_url . 'Dashboard');
        }
    }
}

		public function updatetaskremarksoldfunction(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$id = $this->input->post('taskkiid');
		$mastertaskid = $this->input->post('mastertaskid');
		$status = $this->input->post('taskstatus');
		$taskremarks = $this->input->post('taskremarks');
		$dfid = $this->input->post('progressdfno');
		if($mastertaskid==3){
				$result = $this->updatecreationofperformainvoice($id, $mastertaskid, $status, $taskremarks);
				if($result){
					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
					redirect(page_url.'Dashboard');
				}
		}else if($mastertaskid==87){
				$result = $this->updatedfreviewmeetingapprove($id, $mastertaskid, $status, $taskremarks);
				if($result){
					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
					redirect(page_url.'Dashboard');
				}
		}else{
		
		$rt=$this->db->select('start_date')->from('task_department_wise_scheduling')->where('id',$id)->get();
		if($rt->num_rows()>0)
		{
			foreach($rt->result() as $rtt);
			$start_date = $rtt->start_date;
		}else
		{
			echo "Invalid Response"; exit;
		}

		// if($status==1)
		// {
		// if($_SESSION['logged_in']['user_id']==61 || $_SESSION['logged_in']['user_id']==161)
		// {

		// 	$doneDate=date('Y-m-d',strtotime($this->input->post('doneDate')));

		// 	$completedondateifadministrator = $doneDate=date('Y-m-d',strtotime($this->input->post('doneDate')));

		// 	if(strtotime($doneDate)<strtotime($start_date))
		// 	{
		// 		echo "Completed Date Cannot be less than Start Date"; exit;
		// 	}
		// }else
		// {
		// 	$doneDate=date('Y-m-d');
		// 	$completedondateifadministrator = date('Y-m-d');
		// }
		// }
		
		//echo date('Y-m-d H:i:s',strtotime($doneDate)); exit;
		/*If task status is pending*/
		if($status==0 && $taskremarks<>''){
			$data = array('task_status'=>$status,
				'taskupdatedontime'=>date('Y-m-d H:i:s'),
				'remarks'=>$taskremarks);

			$q = $this->db->select('id, remarks, taskupdatedontime')->from('task_department_wise_scheduling')->where('id',$id)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $rowssss);
				if($rowssss->remarks<>''){
					//'taskupdatedontime'=>$rowssss->taskupdatedontime,
				$datass = array('recordid'=>$rowssss->id,
					'taskupdatedontime'=>date('Y-m-d H:i:s'),
					'remarks'=>$rowssss->remarks,
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);
				$this->db->insert('task_pending_status',$datass);
			}
			}

			$this->db->where('id',$id);
			$this->db->update('task_department_wise_scheduling',$data);
			$ticketselection = $this->input->post('ticketcondition');
			$selecteddepartmentid = $this->input->post('selectdepartment');
			$selecteduserinfo = $this->input->post('departmentuser');
			if($ticketselection==1){
				$this->task->createnewticket($selecteddepartmentid, $selecteduserinfo, $dfid, $mastertaskid, $id, $taskremarks);
			}



			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
			redirect(page_url.'Dashboard');
			/*Work completed*/
		}else if($status<>'' && $status==1){

				$rt=$this->db->select('start_date')->from('task_department_wise_scheduling')->where('id',$id)->get();
				if($rt->num_rows()>0)
				{
				foreach($rt->result() as $rtt);
				$start_date = $rtt->start_date;
				}else
				{
				echo "Invalid Response"; exit;
				}
				$tdadate = date('Y-m-d');
				if($start_date>$tdadate){
					$precloserdata = array('df_id'=>$dfid,
					'task_id'=>$mastertaskid,
					'record_id'=>$id,
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id,
					'remarks'=>$taskremarks,
					'current_status'=>0);
					
					$this->db->insert('precloser_task_request',$precloserdata);

					$dataupdate11 = array('task_status'=>2,
					'task_completed_on'=>date('Y-m-d H:i:s'),
					'taskupdatedontime'=>date('Y-m-d H:i:s'),
					'task_completed_by'=>$user_id);
					$this->db->where('id',$id);
					$this->db->where('taskid',$mastertaskid);
					$this->db->update('task_department_wise_scheduling',$dataupdate11);

					//$this->preclosertasknotification();
					$this->preclosertasknotification($dfid, $mastertaskid, $user_id, $taskremarks);
					$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable" style="color:black; font-size:25px;">Thank You!, Record successfully updated and pending for Approval</div>');
			        redirect(page_url.'Dashboard');

				}

			$data = array('task_status'=>$status,
				'task_completed_on'=>date('Y-m-d H:i:s'),
				'taskupdatedontime'=>date('Y-m-d H:i:s'),
				'task_completed_by'=>$user_id);
			$this->db->where('id',$id);
			$this->db->where('taskid',$mastertaskid);
			$this->db->update('task_department_wise_scheduling',$data);

			//$this->task->getmessageoftaskcompletionandtrigger($mastertaskid, $user_id,$dfid);
			$this->task->checkiftaskispaymentstage($id);
			/*Check Task Mapped with Department and Message*/
			$taskid = $this->task->gettaskidfrommaster($id);
			$df_id = $this->task->getdfidfrommaster($id);
			$departmentoftask = $this->task->getdepartmentoftask($taskid,$df_id);
			$this->task->getdepartmentuserwhomdfassigned($taskid,$departmentoftask, $df_id);

			$q = $this->db->select('task_id')->from('task_management')->where('task_id',$mastertaskid)->where('df_meeting_close',1)->get();
			if($q->num_rows()>0){
				$q3 = $this->db->select('df_id')->from('task_department_wise_scheduling')->where('id',$id)->get();
				foreach($q3->result() as $f);
				$finddfid = $f->df_id;
				$data3 = array('task_status'=>1,
					'task_completed_on'=>date('Y-m-d H:i:s'),
					'taskupdatedontime'=>date('Y-m-d H:i:s'));
				$this->db->where('taskid',4);
				$this->db->where('df_id',$finddfid);
				$this->db->update('task_department_wise_scheduling',$data3);

			 }

			 $q = $this->db->select('task_id')->from('task_management')->where('task_id',$mastertaskid)->where('isitfinalstep',1)->get();
			if($q->num_rows()>0){
				
				$dfdataarray = array('df_status'=>1,
					'completed_on'=>date('Y-m-d H:i:s'),
					'completed_by'=>$user_id);
				$this->db->where('id',$dfid);
				$this->db->update('df_release',$dfdataarray);
			 }

			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
			redirect(page_url.'Dashboard');
			/*If task send on previous step*/
		    }else if($status<>'' && $status==2){


			$q = $this->db->select('id, remarks, taskupdatedontime')->from('task_department_wise_scheduling')->where('id',$id)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $rowssss);
				if($rowssss->remarks<>''){
				$datass = array('recordid'=>$rowssss->id,
					'taskupdatedontime'=>date('Y-m-d H:i:s'),
					'remarks'=>$rowssss->remarks,
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);
				$this->db->insert('task_pending_status',$datass);
			}
			}
			
			$taskid = $this->input->post('previousstep');
			$departmentid = $this->task->getdepartment($taskid);
			$dfid = $this->input->post('progressdfno');
			$mastertaskid = $this->input->post('mastertaskid');

			$q = $this->db->select('df_id, taskid, department_id, start_date, end_date, added_on, added_by, po_id, task_status, remarks, userid, assigned_user, assigned_by, assigned_on')->from('task_department_wise_scheduling')->where('df_id',$dfid)->where('taskid',$mastertaskid)->get();
			    foreach($q->result() as $row3);
				$data = array('df_id'=>$row3->df_id,
				'taskid'=>$row3->taskid,
				'department_id'=>$row3->department_id,
				'start_date'=>$row3->start_date,
				'end_date'=>$row3->end_date,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$row3->po_id,
				'task_status'=>0,
				'remarks'=>$taskremarks,
				'taskupdatedontime'=>date('Y-m-d H:i:s'),
				'userid'=>$row3->userid,
				'assigned_user'=>$row3->assigned_user,
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'));
				$this->db->insert('task_department_wise_scheduling',$data);
				$lastinsertid = $this->db->insert_id();
				$data1 = array('task_status'=>0,
					'remarks'=>$taskremarks);
			$this->db->where('id',$taskid);
			$this->db->update('task_department_wise_scheduling',$data1);
			$this->task->addnotification($row3->df_id, $taskremarks, $user_id,$row3->department_id,$row3->assigned_user,$lastinsertid);
			
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You!, Record successfully updated.</div>');
			redirect(page_url.'Dashboard');
		}else{

			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry!, There is some technical issue.</div>');
			redirect(page_url.'Dashboard');
		}
	}
		
	}






	function updatecreationofperformainvoice($id, $mastertaskid, $status, $taskremarks){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$recordid = $id; 
		$taskid = $mastertaskid;
		$taskstatus = $status;
		$taskrelatedremarks = $taskremarks;


		if($taskstatus==1){
			$data = array('task_status'=>1,
				'remarks'=>$taskrelatedremarks,
				'task_completed_on'=>date('Y-m-d H:i:s'),
				'task_completed_by'=>$user_id);
			$this->db->where('id',$recordid);
			$this->db->where('taskid',$taskid);
			$this->db->update('task_department_wise_scheduling',$data);

			$q = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('id',$recordid)->get();
			foreach($q->result() as $row);
			$poid = $row->po_id;
			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$enddate = $this->iftomorrowisholiday($tomorrow);

			$res = true;
			if($this->shouldQueuePreReleaseTask($poid, array(114, 86, 87, 2))){
				$data1 = array('df_id'=>0,
					'taskid'=>114,
					'department_id'=>9,
					'start_date'=>date('Y-m-d'),
					'end_date'=>date('Y-m-d',strtotime($enddate)),
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id,
					'po_id'=>$poid,
					'task_status'=>0,
					'userid'=>$user_id,
					'assigned_user'=>$user_id,
					'assigned_by'=>$user_id,
					'assigned_on'=>date('Y-m-d H:i:s'));

				$res = $this->db->insert('task_department_wise_scheduling',$data1);
			}

			return $res;
			}else{

			$data = array('remarks'=>$taskrelatedremarks);
			$this->db->where('id',$recordid);
			$this->db->where('taskid',$taskid);
			$res = $this->db->update('task_department_wise_scheduling',$data);
			return $res; 
		}

	}


	function updatedfreviewmeetingapprove($id, $mastertaskid, $status, $taskremarks){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$recordid = $id; 
		$taskid = $mastertaskid;
		$taskstatus = $status;
		$taskrelatedremarks = $taskremarks;


		if($taskstatus==1){
			$data = array('task_status'=>1,
				'remarks'=>$taskrelatedremarks,
				'task_completed_on'=>date('Y-m-d H:i:s'),
				'task_completed_by'=>$user_id);
			$this->db->where('id',$recordid);
			$this->db->where('taskid',$taskid);
			$this->db->update('task_department_wise_scheduling',$data);

			$q = $this->db->select('po_id')->from('task_department_wise_scheduling')->where('id',$recordid)->get();
			foreach($q->result() as $row);
			$poid = $row->po_id;
			$tomorrow = date('Y-m-d', strtotime('+1 day'));
			$enddate = $this->iftomorrowisholiday($tomorrow);

			$res = true;
			if($this->shouldQueuePreReleaseTask($poid, array(2))){
				$data1 = array('df_id'=>0,
					'taskid'=>2,
					'department_id'=>9,
					'start_date'=>date('Y-m-d'),
					'end_date'=>date('Y-m-d',strtotime($enddate)),
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id,
					'po_id'=>$poid,
					'task_status'=>0,
					'userid'=>$user_id,
					'assigned_user'=>$user_id,
					'assigned_by'=>$user_id,
					'assigned_on'=>date('Y-m-d H:i:s'));

				$res = $this->db->insert('task_department_wise_scheduling',$data1);
			}

			return $res;
			}else{

			$data = array('remarks'=>$taskrelatedremarks);
			$this->db->where('id',$recordid);
			$this->db->where('taskid',$taskid);
			$res = $this->db->update('task_department_wise_scheduling',$data);
			return $res; 
		}

		}

	private function shouldQueuePreReleaseTask($poid, $blockedTaskIds = array())
	{
		if(!empty($blockedTaskIds)){
			$existing_task = $this->db->select('id')
				->from('task_department_wise_scheduling')
				->where('po_id', $poid)
				->where('df_id', 0)
				->where('task_status', 0)
				->where_in('taskid', $blockedTaskIds)
				->limit(1)
				->get();

			if($existing_task->num_rows() > 0){
				return false;
			}
		}

		$active_df = $this->db->select('p.id')
			->from('poreceived p')
			->join('df_release dr', 'dr.id = p.df_id', 'left')
			->where('p.id', $poid)
			->where('p.df_id >', 0)
			->group_start()
				->where('dr.df_status', 0)
				->or_where('dr.df_status', 'running')
				->or_where('dr.df_status IS NULL', null, false)
			->group_end()
			->limit(1)
			->get();

		return $active_df->num_rows() === 0;
	}

		function iftomorrowisholiday($date){
    while (true) {
        $q = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date', $date)->get();

        if ($q->num_rows() > 0) {
            // If it's a holiday, move to the next day
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        } else {
            // Found a working day
            break;
        }
    }
    return $date;
}

	public function preclosertasknotification($df_id, $task_id, $user_id, $remarks)
{
    // Load email library if not already loaded
    $this->load->library('email');

    // Fetch the task details using the task_id
    $taskDetails = $this->db->select('task_name, task_id')
                            ->from('task_management')
                            ->where('task_id', $task_id)
                            ->get()
                            ->row_array();

     $DfDetails = $this->db->select('df_no')
                            ->from('df_release')
                            ->where('id', $df_id)
                            ->get()
                            ->row_array();

    // Fetch the user details using the user_id
    $userDetails = $this->db->select('title, first_name, last_name')
                            ->from('system_users')
                            ->where('user_id', $user_id)
                            ->get()
                            ->row_array();

    // Check if task and user details are found
    if (!$taskDetails || !$userDetails) {
        log_message('error', 'Task or User details not found for Task ID: ' . $task_id . ' or User ID: ' . $user_id);
        return false;
    }

    // Set sender, recipient, and email subject
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging - Pre Closer Task Check');
    $this->email->to('groupceo@shubhampack.com'); // Send email to the user
    //$this->email->cc('admin@example.com'); // Optional CC
    //$this->email->bcc('audit@example.com'); // Optional BCC

    // Email subject
    $subject = 'Pre-Completion Task Notification - ' . $taskDetails['task_name'];
    $markupdatedby = $userDetails['title']." ".$userDetails['first_name']." ".$userDetails['last_name'];

    // Email message with company logo and attractive design
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task Marked as Completed</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
        <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="background-color: #4872b8; padding: 20px; text-align: center;">
                <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Company Logo" style="max-width: 150px;">
            </div>
            
            <div style="padding: 20px;">
                <h2 style="color: #4872b8; text-align: center;">Task Marked as Completed</h2>

                <p style="font-size: 16px; line-height: 1.6;">Dear Sir,</p>

                <p style="font-size: 16px; line-height: 1.6;">
                    Please be informed that the following task has been marked as <strong>completed ahead of the planned date</strong>. Kindly review the task details below and take the appropriate action.
                </p>

                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">DF No.</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . $DfDetails['df_no'] . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Task Name</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . $taskDetails['task_name'] . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Remarks updated by User </td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . ucwords(strtolower($remarks)) . '</td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Marked as Completed By</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . ucwords(strtolower($markupdatedby)) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Date of Completion</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . date('d M Y') . '</td>
                    </tr>
                </table>

                <p style="font-size: 16px; line-height: 1.6;">
                    We request you to review the completion status and take one of the following actions:
                </p>

                <ul style="font-size: 16px; line-height: 1.6;">
                    <li>Approve the completion if the task has been fully accomplished.</li>
                    <li>Rollback the status if the completion is found to be incorrect.</li>
                </ul>

                <p style="font-size: 16px; line-height: 1.6;">
                    To view the task details and take the necessary action, please log in to your PMS panel.
                </p>

                

                <p style="font-size: 16px; line-height: 1.6; margin-top: 20px;">
                    If you have any questions, please reach out to '.ucwords(strtolower($markupdatedby)).'
                </p>

                <p style="font-size: 16px; line-height: 1.6; margin-top: 20px;">
                    Best Regards, <br>
                    <strong>Shubham Flexible Packaging</strong> <br>
                   
                </p>
            </div>

            <div style="background-color: #4872b8; padding: 10px; text-align: center; color: #fff;">
                &copy; ' . date('Y') . ' Shubham Flexible Packaging. All Rights Reserved.
            </div>
        </div>
    </body>
    </html>
';


    // Set email subject and message
    $this->email->subject($subject);
    $this->email->message($message);

    // Send the email
    if ($this->email->send()) {
        log_message('info', 'Pre-completion task email successfully sent to ' . $userDetails['email']);
        return true;
    } else {
        log_message('error', 'Failed to send pre-completion task email to ' . $userDetails['email']);
        return false;
    }
}


public function savedfmeeting()
{

    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('agenceofthemeeting', 'Agenda of Meeting', 'required|trim');
    $this->form_validation->set_rules('date', 'Meeting Date', 'required|trim');
    $this->form_validation->set_rules('time', 'Meeting Time', 'required|trim');

    $user_id = $this->session->userdata['logged_in']['user_id'];

    if ($this->form_validation->run() == FALSE)
    {
        $this->dfmeeting();
        return;
    }
    else
    {

        date_default_timezone_set("Asia/Kolkata");
        $date = date('Y-m-d H:i:s');
        $this->ensureDfMeetingRecipientFields();
        $this->ensureDfMeetingPointTrackingFields();

        $participants = array();
        foreach ((array) $this->input->post('participant') as $participant_id) {
            $participant_id = (int) $participant_id;
            if ($participant_id > 0 && !in_array($participant_id, $participants, true)) {
                $participants[] = $participant_id;
            }
        }

        if (empty($participants)) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger alert-dismissable">Please select at least one participant for the DF meeting.</div>'
            );
            redirect(page_url.'Task/dfmeeting/'.$this->uri->segment(3)."/".$this->uri->segment(4));
            return;
        }

        $mom_points = (array) $this->input->post('dfmompoints');
        $responsible_people = (array) $this->input->post('responsible_person');
        $due_dates = (array) $this->input->post('due_date');
        $point_rows = array();
        $responsible_user_ids = array();
        $point_signatures = array();
        $row_count = max(count($mom_points), count($responsible_people), count($due_dates));
        $today_date = date('Y-m-d');

        for ($x = 0; $x < $row_count; $x++) {
            $mom_point = trim((string) (isset($mom_points[$x]) ? $mom_points[$x] : ''));
            $responsible_person = (int) (isset($responsible_people[$x]) ? $responsible_people[$x] : 0);
            $due_date_raw = trim((string) (isset($due_dates[$x]) ? $due_dates[$x] : ''));

            if ($mom_point === '' && $responsible_person <= 0 && $due_date_raw === '') {
                continue;
            }

            if ($mom_point === '' || $responsible_person <= 0 || $due_date_raw === '' || strtotime($due_date_raw) === false) {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger alert-dismissable">Each weekly meeting point needs a task, responsible person and due date.</div>'
                );
                redirect(page_url.'Task/dfmeeting/'.$this->uri->segment(3)."/".$this->uri->segment(4));
                return;
            }

            $due_date = date('Y-m-d', strtotime($due_date_raw));
            if ($due_date < $today_date) {
                $this->session->set_flashdata(
                    'message',
                    '<div class="alert alert-danger alert-dismissable">Due date cannot be earlier than today for weekly meeting points.</div>'
                );
                redirect(page_url.'Task/dfmeeting/'.$this->uri->segment(3)."/".$this->uri->segment(4));
                return;
            }

            $signature = strtolower($mom_point) . '|' . $responsible_person . '|' . $due_date;
            if (in_array($signature, $point_signatures, true)) {
                continue;
            }

            $point_signatures[] = $signature;
            $point_rows[] = array(
                'mom_point' => $mom_point,
                'responsible_person' => $responsible_person,
                'due_date' => $due_date
            );

            if (!in_array($responsible_person, $responsible_user_ids, true)) {
                $responsible_user_ids[] = $responsible_person;
            }
        }

        if (empty($point_rows)) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger alert-dismissable">Please add at least one weekly meeting point with responsible person and due date.</div>'
            );
            redirect(page_url.'Task/dfmeeting/'.$this->uri->segment(3)."/".$this->uri->segment(4));
            return;
        }

        $bcc_only_email = 'mangleshup@gmail.com';
        $additional_to_emails = $this->normalizeEmailList($this->input->post('emailto'));
        $ccemailids = $this->normalizeEmailList($this->input->post('emailcc'));
        $participant_emails = $this->getUserEmailsByIds($participants);
        $responsible_user_emails = $this->getUserEmailsByIds($responsible_user_ids);

        $stripBccOnlyEmail = function ($emails) use ($bcc_only_email) {
            $filtered_emails = array();

            foreach ((array) $emails as $email_address) {
                $email_address = trim((string) $email_address);
                if ($email_address === '' || strcasecmp($email_address, $bcc_only_email) === 0) {
                    continue;
                }

                if (!in_array(strtolower($email_address), array_map('strtolower', $filtered_emails), true)) {
                    $filtered_emails[] = $email_address;
                }
            }

            return $filtered_emails;
        };

        $additional_to_emails = $stripBccOnlyEmail($additional_to_emails);
        $ccemailids = $stripBccOnlyEmail($ccemailids);
        $participant_emails = $stripBccOnlyEmail($participant_emails);
        $responsible_user_emails = $stripBccOnlyEmail($responsible_user_emails);
        $to_addresses = array_values(array_unique(array_merge($participant_emails, $responsible_user_emails, $additional_to_emails)));

        if (empty($to_addresses)) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-danger alert-dismissable">No valid email address was found for the selected participants. Please add a valid To email.</div>'
            );
            redirect(page_url.'Task/dfmeeting/'.$this->uri->segment(3)."/".$this->uri->segment(4));
            return;
        }

        $this->db->trans_begin();

        try
        {

            $data = array(
                'dfid'       => $this->uri->segment(4),
                'recordid'   => $this->uri->segment(3),
                'particular' => $this->input->post('agenceofthemeeting'),
                'date'       => date('Y-m-d', strtotime($this->input->post('date'))),
                'time'       => date('H:i:s', strtotime($this->input->post('time'))),
                'cc_record'  => implode(',', $ccemailids),
                'to_record'  => implode(',', $additional_to_emails),
                'added_on'   => $date,
                'added_by'   => $user_id
            );

            $q = $this->db->select('id')
                          ->from('dfwise_mom')
                          ->where('dfid', $this->uri->segment(4))
                          ->get();

            if ($q->num_rows() > 0)
            {
                foreach ($q->result() as $row);
                $id = $row->id;

                if (!$this->db->where('id', $id)->update('dfwise_mom', $data))
                {
                    throw new Exception($this->db->error()['message']);
                }
            }
            else
            {
                if (!$this->db->insert('dfwise_mom', $data))
                {
                    throw new Exception($this->db->error()['message']);
                }

                $id = $this->db->insert_id();
            }

            $this->db->where('dfid', $this->uri->segment(4))->delete('dfwise_mom_participant');
            foreach ($participants as $participant_id)
            {
                $participant_data = array(
                    'dfid'     => $this->uri->segment(4),
                    'user_id'  => $participant_id,
                    'added_on' => date('Y-m-d H:i:s'),
                    'added_by' => $user_id
                );

                if (!$this->db->insert('dfwise_mom_participant', $participant_data))
                {
                    throw new Exception($this->db->error()['message']);
                }
            }

            $data1 = array(
                'df_id'    => $this->uri->segment(4),
                'added_on' => date('Y-m-d H:i:s'),
                'added_by' => $user_id
            );

            if (!$this->db->insert('dfmom_history_date', $data1))
            {
                throw new Exception($this->db->error()['message']);
            }

            $responsible_user_map = array();
            if (!empty($responsible_user_ids)) {
                $responsible_users_query = $this->db->select('user_id, title, first_name, last_name')
                    ->from('system_users')
                    ->where_in('user_id', $responsible_user_ids)
                    ->where('user_status', 1)
                    ->get();

                if ($responsible_users_query->num_rows() > 0) {
                    foreach ($responsible_users_query->result() as $responsible_user_row) {
                        $responsible_user_map[(int) $responsible_user_row->user_id] = trim($responsible_user_row->title . ' ' . $responsible_user_row->first_name . ' ' . $responsible_user_row->last_name);
                    }
                }
            }

            $pcular = $this->input->post('agenceofthemeeting');

            $message = "Dear Members, <br> Please find the MOM of todays DF Meeting. ".$pcular."<br/><br/>";

            $message .= '<table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr>
            <td align="left" colspan="2" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd." />
            </td>
            </tr>
            <tr>
                <th colspan="4" style="background-color: #2c86b8; color: white; padding: 15px; text-align: left; width:100%; border: 1px solid #fff;">'.$pcular.'</th>
            </tr>

            <tr>
                <th style="background-color: #2c86b8; color: white; padding: 15px; text-align: left; width:10%; border: 1px solid #fff;">Sr No</th>
                <th style="background-color: #2c86b8; color: white; padding: 15px; text-align: left; width:50%; border: 1px solid #fff;">MOM Points</th>
                <th style="background-color: #2c86b8; color: white; padding: 15px; text-align: left; width:25%; border: 1px solid #fff;">Responsible Person</th>
                <th style="background-color: #2c86b8; color: white; padding: 15px; text-align: left; width:15%; border: 1px solid #fff;">Due Date</th>
            </tr>
            </thead><tbody>';

            $i = 1;
            foreach ($point_rows as $point_row) {
                $responsible_name = isset($responsible_user_map[(int) $point_row['responsible_person']]) ? $responsible_user_map[(int) $point_row['responsible_person']] : 'Not Available';
                $point_data = array(
                    'df_id' => $this->uri->segment(4),
                    'record_id' => $this->uri->segment(3),
                    'mom_point' => $point_row['mom_point'],
                    'responsible_person' => (int) $point_row['responsible_person'],
                    'due_date' => $point_row['due_date'],
                    'workstatus' => 0,
                    'added_on' => $date,
                    'added_by' => $user_id
                );

                if (!$this->db->insert('dfmom_points', $point_data)) {
                    throw new Exception($this->db->error()['message']);
                }

                $message .= "<tr>
                    <td style='border:1px solid #ddd;padding:8px;'>" . $i . "</td>
                    <td style='border:1px solid #ddd;padding:8px;'>" . htmlspecialchars($point_row['mom_point'], ENT_QUOTES, 'UTF-8') . "</td>
                    <td style='border:1px solid #ddd;padding:8px;'>" . htmlspecialchars($responsible_name, ENT_QUOTES, 'UTF-8') . "</td>
                    <td style='border:1px solid #ddd;padding:8px;'>" . date('d-m-Y', strtotime($point_row['due_date'])) . "</td>
                </tr>";
                $i++;
            }

$message .= "</tbody></table>";

//echo $message; exit;

$SUB = "DF Meeting MOM Points ".$pcular;
					$qq = $this->db->select('end_date, taskid')
    ->from('task_department_wise_scheduling')
    ->where('id', $this->uri->segment(3))
    ->where('df_id', $this->uri->segment(4))
    ->get();

if($qq->num_rows() > 0)
{
    foreach($qq->result() as $o);

    if ((int) $o->taskid === 4) {
        $meeting_window = $this->getDfMeetingFridayWindow($o->end_date, false);
        $data1 = array(
            'start_date' => $meeting_window['start_date'],
            'end_date'   => $meeting_window['end_date']
        );
    } else {
        $q4 = $this->db->select('tat')
            ->from('task_management')
            ->where('task_id', $o->taskid)
            ->get();

        foreach($q4->result() as $ross);

        $date = new DateTime($o->end_date);
        $date->modify('+'.$ross->tat.' days');
        $enddate1 = $date->format('Y-m-d');

        $q5 = $this->db->select('holiday_date')
            ->from('prestogroup_holidays')
            ->where('holiday_date', $enddate1)
            ->get();

        if($q5->num_rows() > 0)
        {
            $date1 = new DateTime($enddate1);
            $date1->modify('+1 days');
            $enddate2 = $date1->format('Y-m-d');
        }
        else
        {
            $enddate2 = $enddate1;
        }

        $data1 = array(
            'start_date' => $o->end_date,
            'end_date'   => $enddate2
        );
    }

    $this->db->where('id', $this->uri->segment(3));
    $this->db->where('df_id', $this->uri->segment(4));

    if(!$this->db->update('task_department_wise_scheduling', $data1))
    {
        throw new Exception($this->db->error()['message']);
    }
}

/*
|--------------------------------------------------------------------------
| Commit Transaction
|--------------------------------------------------------------------------
*/

if($this->db->trans_status() === FALSE)
{
    throw new Exception('Database Transaction Failed.');
}

$this->db->trans_commit();

/*
|--------------------------------------------------------------------------
| Send Email AFTER Commit
|--------------------------------------------------------------------------
*/

$this->email->clear(true);
$this->email->set_mailtype("html");
$this->email->to($to_addresses);
if(!empty($ccemailids))
{
    $this->email->cc($ccemailids);
}

$this->email->bcc($bcc_only_email);

$this->email->from('taskmanagement@shubhampack.com');
$this->email->subject($SUB);
$this->email->message($message);
$result11 = $this->email->send();
//echo $this->email->print_debugger(); exit;


    		$this->session->set_flashdata(
    'message',
    '<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>'
);

redirect(page_url.'Task/dfmeeting/'.$this->uri->segment(3)."/".$this->uri->segment(4));

}
catch (Exception $e)
{
    $this->db->trans_rollback();

    log_message('error', 'savedfmeeting() Error : '.$e->getMessage());

    $this->session->set_flashdata(
        'message',
        '<div class="alert alert-danger alert-dismissable">Something went wrong while saving the meeting. Please try again.</div>'
    );

    redirect(page_url.'Task/dfmeeting/'.$this->uri->segment(3)."/".$this->uri->segment(4));
}

}

}
	

	// public function savedfmeeting()
	// 	{
		
	// 	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	// 	$this->form_validation->set_rules('agenceofthemeeting', 'Agenda of Meeting', 'required|trim');
	// 	$this->form_validation->set_rules('date', 'Meeting Date', 'required|trim');
	// 	$this->form_validation->set_rules('time', 'Meeting Time', 'required|trim');
	// 	$user_id =$this->session->userdata['logged_in']['user_id'];		
	// 	if ($this->form_validation->run() == FALSE)
	// 	{
	// 		$this->load->view('master/dfmeeting');
	// 	}
	// 	else
	// 	{
	// 	date_default_timezone_set("Asia/Kolkata");
	// 	$date =  date('Y-m-d H:i:s'); 
		
	// 	$data = array('dfid'=>$this->uri->segment(4),
	// 		'recordid'=>$this->uri->segment(3),
	// 		'particular'=>$this->input->post('agenceofthemeeting'),
	// 		'date'=>date('Y-m-d',strtotime($this->input->post('date'))),
	// 		'time'=>date('H:i:s',strtotime($this->input->post('time'))),
	// 		'cc_record'=>$this->input->post('emailcc'),
	// 		'added_on'=>$date,
	// 		'added_by'=>$user_id);

	// 	$q = $this->db->select('id')->from('dfwise_mom')->where('dfid',$this->uri->segment(4))->get();
	// 	if($q->num_rows()>0){
	// 		foreach($q->result() as $row);
	// 		$id = $row->id;
	// 	}else{
	// 		$this->db->insert('dfwise_mom',$data);
	// 		$id = $this->db->insert_id();
	// 	}
	// 		$participants = $_REQUEST['participant'];
	// 		if(isset($_REQUEST['participant'])){	
	// 				$tags1=count($_REQUEST['participant']);
	// 				if($tags1>0)
	// 				{
	// 				$participant=$_REQUEST['participant'];
	// 				$i=1;
	// 				for($x=0;$x<$tags1;$x++){
	// 				if($participant[$x]!='')
	// 					{

	// 						$data=array('dfid'=>$this->uri->segment(4),
	// 						'user_id'=>$participant[$x],
	// 						'added_on'=>date('Y-m-d H:i:s'),
	// 						'added_by'=>$user_id);
	// 						$this->db->insert('dfwise_mom_participant',$data);
							
							
	// 					}
	// 				$i++;	
	// 				}
	// 				}
	// 				}
	// 				$data1 = array('df_id'=>$this->uri->segment(4),
	// 				'added_on'=>date('Y-m-d H:i:s'),
	// 				'added_by'=>$user_id);
	// 				$this->db->insert('dfmom_history_date',$data1);
	// 				$pcular = $this->input->post('agenceofthemeeting');

					
   
	// 				$message = "Dear Members, <br> Please find the MOM of todays DF Meeting. ".$pcular."<br/><br/>";
	// 				$message.='<table style="width: 100%; border-collapse: collapse;">
	// 				<thead>
	// 				<tr>
	// 				<td align="left" colspan="2" style="background: #fff; padding: 15px 0px;" bgcolor="#120001"><img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" width="200px;" alt="Shubham Flexible Packaging Machines Pvt. Ltd." />
	// 				</td>
	// 				</tr>
	// 				<tr>
	// 					<th colspan="2" style="background-color: #2c86b8; color: white; padding: 15px; text-align: left; width:100%; border: 1px solid #fff;">'.$pcular.'</th>
						
	// 					</tr>

	// 					<tr>
	// 					<th style="background-color: #2c86b8; color: white; padding: 15px; text-align: left; width:20%; border: 1px solid #fff;">Sr No</th>
	// 					<th style="background-color: #2c86b8; color: white; padding: 15px; text-align: left; width:80%; border: 1px solid #fff;">MOM Points</th>
	// 					</tr>
	// 				</thead><tbody>';

	// 			if(isset($_REQUEST['dfmompoints'])){	
	// 				$tags1=count($_REQUEST['dfmompoints']);
	// 				if($tags1>0)
	// 				{
	// 				$dfmompoints=$_REQUEST['dfmompoints'];
	// 				$i=1;
	// 				for($x=0;$x<$tags1;$x++){
	// 				if($dfmompoints[$x]!='')
	// 					{
	// 						$rest=$this->db->select('id')->from('dfmom_points')->where('df_id',$this->uri->segment(3))->where('mom_point',$dfmompoints[$x])->where('added_on',date('Y-m-d H:i:s'))->get();
	// 						if($rest->num_rows()==0)
	// 						{

	// 						$data=array('df_id'=>$this->uri->segment(4),
	// 						'record_id'=>$this->uri->segment(3),
	// 						'mom_point'=>$dfmompoints[$x],
	// 						'added_on'=>date('Y-m-d H:i:s'),
	// 						'added_by'=>$user_id);
	// 						$this->db->insert('dfmom_points',$data);
							
	// 						$message.="<tr><td style='border: 1px solid #ddd; padding: 8px;'>".$i."</td>
	// 						<td style='border: 1px solid #ddd; padding: 8px;'>".$dfmompoints[$x]."</td></tr>";
	// 					}
							
	// 					}
	// 				$i++;	
	// 				}
	// 				}
	// 				}
	// 				$message.="</tbody></table>";
	// 				//echo $message; exit;
	// 				$ccemailids = $this->input->post('emailcc');
	// 				$SUB = "DF Meeting MOM Points  ".$pcular;
	// 				$emails = array();
					
	// 				$participantperson=array();
	// 				$q = $this->db->select('email')->from('system_users')->where_in('user_id',$participants,false)->get();
	// 				foreach($q->result() as $participantinfo){
	// 				$participantperson[]= $participantinfo->email;
	// 				}
	// 				if(count($participantperson)>0){
	// 				$ccemail = trim(implode(',',$participantperson),',');
	// 				}else{
	// 				  $ccemail="";  
	// 				}
	// 				//echo $ccemail; exit;
	// 				$this->email->set_mailtype("html");
	// 			    $this->email->to('mangleshup@gmail.com');
	// 				//$this->email->cc($ccemail);
	// 				if($ccemailids){
	// 					$this->email->cc($ccemailids);
	// 				}
	// 				//$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
	// 				$this->email->from('taskmanagement@shubhampack.com');
    // 				$this->email->subject($SUB);
    // 			    $this->email->message($message);
    // 				$result11=$this->email->send();
    // 				//echo $this->email->print_debugger(); exit;

    // 				$qq = $this->db->select('end_date, taskid')->from('task_department_wise_scheduling')->where('id',$this->uri->segment(3))->where('df_id',$this->uri->segment(4))->get();
    // 				if($qq->num_rows()>0){
    // 					foreach($qq->result() as $o);

    // 					$q4 = $this->db->select('tat')->from('task_management')->where('task_id',$o->taskid)->get();
    // 					foreach($q4->result() as $ross);
	// 					$date = new DateTime($o->end_date); 
	// 					$date->modify('+'.$ross->tat.' days'); 
	// 					$enddate1 =  $date->format('Y-m-d');
	// 					$q5 = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$enddate1)->get();
	// 					if($q5->num_rows()>0){
	// 						$date1 = new DateTime($enddate1); 
	// 					$date1->modify('+1 days'); 
	// 					$enddate2 =  $date1->format('Y-m-d');
	// 					}else{
	// 						$enddate2  = $enddate1;
	// 					}
    // 					$data1 = array('start_date'=>$o->end_date,
    // 					'end_date'=>$enddate2);
    // 					$this->db->where('id',$this->uri->segment(3));
    // 					$this->db->where('df_id',$this->uri->segment(4));
    // 					$this->db->update('task_department_wise_scheduling',$data1);
    // 				}
    // 			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
	// 		redirect(page_url.'Task/dfmeeting/'.$this->uri->segment(3)."/".$this->uri->segment(4));
			
		
	// 	}
		
	// }

	public function notificationuserwise(){
		$departmentid = $this->input->post('departmentid');
		$userid = $this->input->post('userid');
		$i=0;
		$message = "";
		$q = $this->db->select('a.id,a.message, b.department, c.df_no, d.first_name, d.last_name, a.added_on')->from('task_intimation_alert a')->join('departments b','a.department_id=b.department_id')->join('df_release c','a.df_id=c.id','left')->join('system_users d','a.user_id=d.user_id','left')
		//->where('department_id',$departmentid)->where('user_id',$userid)
		->where('a.status',0)->order_by('a.added_on','asc')->limit(2)->get();
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$name = ucwords($row->first_name." ".$row->last_name);
				$find = array('{df_number}','{department_name}', '{department_hod}');
							$replace = array(ucwords(strtolower($row->df_no)), ucwords(strtolower($row->department)), ucwords(strtolower($name)));
					$message_body = str_replace($find, $replace, $row->message);
					
					$addedondatetime = date('d-m-Y',strtotime($row->added_on))." ".date('h:i A',strtotime($row->added_on));

				

                            $message.='<li id="item_notification_'.$i.'">
                        <div class="media">
                              <div class="media-body">
                              <p>'.$message_body.'</p>
                              <p style="font-size:12px;font-style:italics;">'.$addedondatetime.'</p>
                           </div>
                        </div>
                     </li>';


							
							
              $i++;
			}
			 $message.='<a href="'.page_url.'Task/tasknotification"><div style="text-align:center;">Click hereto View All Notification</div></a>';

		}
		echo $message;		
	}

	function markasreadNotifications(){
		$id=$this->input->post('id');
		$dt=array('status'=>1,'accepted_on'=>date('Y-m-d'),'accepted_by'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$id);
		$this->db->update('task_intimation_alert',$dt);
		echo true;
		
	}

	public function notificationuserwiseCount(){
		$departmentid = $this->input->post('departmentid');
		$userid = $this->input->post('userid');
		$q = $this->db->select('a.id')->from('task_intimation_alert a')->join('departments b','a.department_id=b.department_id')->join('df_release c','a.df_id=c.id','left')->join('system_users d','a.user_id=d.user_id','left')->where('a.user_id',$userid)
		->where('a.status',0)->order_by('a.added_on','asc')->get();
		echo $q->num_rows();		
	}

	public function viewdfmeetingmom(){
		$this->ensureDfMeetingPointTrackingFields();
		$data = array(
			'can_manage_friday_reset' => $this->canManageDfMeetingFridayReset(),
			'friday_reset_target_date' => $this->getDfMeetingFridayDate(date('Y-m-d'), true)
		);
		$this->load->view('master/view_dfmeeting', $data);
	}

	public function dfweeklymeetingpoints()
	{
		$this->ensureDfMeetingPointTrackingFields();

		$scope = $this->getTaskDashboardScope();
		$current_user_id = (int) $scope['user_id'];
		$show_all_points = $this->canViewAllDfWeeklyMeetingPoints($scope);

		$this->db->select("
			p.id,
			p.df_id,
			p.record_id,
			p.responsible_person,
			p.mom_point,
			p.due_date,
			p.workstatus,
			CASE
				WHEN p.update_on IS NULL OR p.update_on = '0000-00-00 00:00:00' OR p.update_on = '0000-00-00'
					THEN NULL
				ELSE p.update_on
			END AS update_on,
			p.update_by,
			p.work_remarks,
			p.added_on,
			d.df_no,
			d.df_description,
			responsible.title AS responsible_title,
			responsible.first_name AS responsible_first_name,
			responsible.last_name AS responsible_last_name,
			creator.title AS creator_title,
			creator.first_name AS creator_first_name,
			creator.last_name AS creator_last_name,
			updater.title AS updater_title,
			updater.first_name AS updater_first_name,
			updater.last_name AS updater_last_name
		", false);
		$this->db->from('dfmom_points p');
		$this->db->join('df_release d', 'p.df_id = d.id', 'left');
		$this->db->join('system_users responsible', 'p.responsible_person = responsible.user_id', 'left');
		$this->db->join('system_users creator', 'p.added_by = creator.user_id', 'left');
		$this->db->join('system_users updater', 'p.update_by = updater.user_id', 'left');
		$this->db->where('p.responsible_person >', 0);

		if (!$show_all_points) {
			$this->db->where('p.responsible_person', $current_user_id);
		}

		$this->db->order_by('p.added_on', 'DESC');
		$this->db->order_by('p.id', 'DESC');
		$points = $this->db->get()->result();

		$summary = array(
			'total' => 0,
			'open' => 0,
			'in_progress' => 0,
			'done' => 0,
			'overdue' => 0,
			'due_today' => 0,
			'unique_df' => 0
		);
		$unique_df_ids = array();
		$today = date('Y-m-d');

		foreach ($points as $point_row) {
			$summary['total']++;

			if (!empty($point_row->df_id) && !in_array((int) $point_row->df_id, $unique_df_ids, true)) {
				$unique_df_ids[] = (int) $point_row->df_id;
			}

			$status = (int) $point_row->workstatus;
			if ($status === 1) {
				$summary['done']++;
			} else if ($status === 2) {
				$summary['in_progress']++;
			} else {
				$summary['open']++;
			}

			if (!empty($point_row->due_date)) {
				if ($point_row->due_date === $today) {
					$summary['due_today']++;
				}

				if ($status !== 1 && $point_row->due_date < $today) {
					$summary['overdue']++;
				}
			}
		}

		$summary['unique_df'] = count($unique_df_ids);

		$data = array(
			'points' => $points,
			'summary' => $summary,
			'show_all_points' => $show_all_points,
			'current_user_id' => $current_user_id
		);

		$this->load->view('master/dfweeklymeetingpoints', $data);
	}

	public function updatedfweeklymeetingpointstatus()
	{
		$this->ensureDfMeetingPointTrackingFields();

		$scope = $this->getTaskDashboardScope();
		$current_user_id = (int) $scope['user_id'];
		$show_all_points = $this->canViewAllDfWeeklyMeetingPoints($scope);
		$point_id = (int) $this->uri->segment(3);
		$status = (int) $this->input->post('workstatus');
		$remarks = trim((string) $this->input->post('work_remarks'));

		if ($point_id <= 0 || !in_array($status, array(0, 1, 2), true)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Invalid weekly meeting point update request.</div>');
			redirect(page_url . 'Task/dfweeklymeetingpoints');
		}

		$point_row = $this->db->select('id, responsible_person, mom_point')
			->from('dfmom_points')
			->where('id', $point_id)
			->where('responsible_person >', 0)
			->limit(1)
			->get()
			->row();

		if (empty($point_row)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Weekly meeting point was not found.</div>');
			redirect(page_url . 'Task/dfweeklymeetingpoints');
		}

		if (!$show_all_points && (int) $point_row->responsible_person !== $current_user_id) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">You are not allowed to update this weekly meeting point.</div>');
			redirect(page_url . 'Task/dfweeklymeetingpoints');
		}

		$update_data = array(
			'workstatus' => $status,
			'work_remarks' => $remarks !== '' ? $remarks : null,
			'update_on' => date('Y-m-d H:i:s'),
			'update_by' => $current_user_id
		);

		$this->db->where('id', $point_id)->update('dfmom_points', $update_data);

		$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Weekly meeting point status updated to ' . $this->getDfWeeklyMeetingPointStatusLabel($status) . '.</div>');
		redirect(page_url . 'Task/dfweeklymeetingpoints');
	}

	public function getallprevioussteps(){
		$sortorder = $this->input->post('sortorder');
		$option = '<option value="">Select Option</option>';
		$this->db->select('a.task_id, a.task_name, b.department')->from('task_management a')->join('departments b','a.department_id=b.department_id','left')->where('a.status',1);
		$this->db->where('a.sortorder >=', 0);
		$q = $this->db->where('a.sortorder <=', $sortorder)->get();
		if($q->num_rows()>0){
			foreach($q->result() as $row){
				$taskname = $row->task_name." (".ucfirst($row->department).")";
		$option.= '<option value="'.$row->task_id.'">'.$taskname.'</option>';
			}
		}

		echo $option; exit;
	}

	function dfgantchart()
	{
		$this->load->view('charts/DF_Gant_chart');
	}


	function get_gantData()
	{
		echo '';
	}

	function task_wise_gantchart()
	{
		$this->load->view('charts/DF_Task_Wise_Gant_chart');
	}

	function getusersofdepartment(){
		$scope = $this->getTaskDashboardScope();
		$current_user_id = (int) $scope['user_id'];
		$departmentid = (int) $this->input->post('department');
		$personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($current_user_id);

		if ($departmentid <= 0 && (!empty($scope['is_super_admin']) || !empty($scope['can_view_team_tasks']))) {
			echo '<option value="ALL">ALL</option>'; exit;
		}

		if (!empty($scope['personal_task_only'])) {
			$option = (count($personal_visible_user_ids) > 1) ? '<option value="ALL">ALL</option>' : '';
			$this->db->select('user_id, first_name, last_name')
				->from('system_users')
				->where('user_status', 1);

			if ($departmentid > 0) {
				$this->db->where('department_id', $departmentid);
			}

			$this->applyTaskAssignedUserFilter('user_id', $personal_visible_user_ids);
			$q = $this->db->order_by('first_name', 'ASC')
				->order_by('last_name', 'ASC')
				->get();

			if ($q->num_rows() > 0) {
				foreach ($q->result() as $row) {
					$option .= '<option value="' . $row->user_id . '">' . strtoupper($row->first_name . ' ' . $row->last_name) . '</option>';
				}
			} else {
				$option = '<option value="' . $current_user_id . '">ME</option>';
			}
		} else if ($departmentid > 0 && $this->canCurrentUserViewDepartmentTasks($departmentid, $scope)) {
			$option = '<option value="ALL">ALL</option>';
			$q = $this->db->select('user_id, first_name, last_name')
				->from('system_users')
				->where('department_id', $departmentid)
				->where('user_status', 1)
				->order_by('first_name', 'ASC')
				->get();

			if ($q->num_rows() > 0) {
				foreach ($q->result() as $row) {
					$option .= '<option value="' . $row->user_id . '">' . strtoupper($row->first_name . ' ' . $row->last_name) . '</option>';
				}
			}
		} else {
			$option = '';
			$q = $this->db->select('user_id, first_name, last_name')
				->from('system_users')
				->where('user_id', $current_user_id)
				->limit(1)
				->get();

			if ($q->num_rows() > 0) {
				$row = $q->row();
				$option = '<option value="' . $row->user_id . '">' . strtoupper($row->first_name . ' ' . $row->last_name) . '</option>';
			} else {
				$option = '<option value="' . $current_user_id . '">ME</option>';
			}
		}

		echo $option; exit;
	}

	public function tasknotification(){
		$this->load->view('master/task_notification');
	}

	public function allnotificationsdata(){
		 $msg = "";
		 $leaderdepartment[] = 0;
		 $user_id =$this->session->userdata['logged_in']['user_id'];
		 $dfno = $this->uri->segment(3);
		 $startdate = $this->uri->segment(4);
		 $enddate = $this->uri->segment(5);
		 $user = $this->uri->segment(6);
		 $activeusertype = $this->uri->segment(7);

		 if($activeusertype==2){
		 	$q = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		 	if($q->num_rows()>0){
		 		foreach($q->result() as $rows){
		 			$leaderdepartment[] = $rows->department_id;
		 		}
		 	}
		 }

		$this->db->select('a.id,a.message,a.status, b.department, c.df_no, d.first_name, d.last_name, a.added_on')->from('task_intimation_alert a')->join('departments b','a.department_id=b.department_id')->join('df_release c','a.df_id=c.id','left')->join('system_users d','a.user_id=d.user_id','left')->where('a.status',0);
		if($dfno<>'ALL' && $dfno<>''){
			$this->db->where('a.df_id',$dfno);
		}

		if($startdate<>'' && $enddate<>''){
			$this->db->where('a.added_on BETWEEN "'. date('Y-m-d', strtotime($startdate)). ' 00:00:00" and "'. date('Y-m-d', strtotime($enddate)).' 23:59:59"');
		}
		if($activeusertype==2 && $leaderdepartment<>0){
			$this->db->where_in('a.department_id',$leaderdepartment,false);
		}	
		if($user<>'ALL' && $user<>''){
			$this->db->where('a.user_id',$user);
		}
		$q = $this->db->order_by('a.added_on','asc')->get();
                                if($q->num_rows()>0){
                                    foreach($q->result() as $row){
                                    $name = ucwords($row->first_name." ".$row->last_name);
                                    $find = array('{df_number}','{department_name}', '{department_hod}');
                                    $replace = array(ucwords(strtolower($row->df_no)), ucwords(strtolower($row->department)), ucwords(strtolower($name)));
                                    $message_body = str_replace($find, $replace, $row->message);

                                    $addedondatetime = date('d-m-Y',strtotime($row->added_on))." ".date('h:i A',strtotime($row->added_on));
                                    if($row->status==0){
                                    	$checkbox = '<input type="checkbox" onchange="getvalue('.$row->id.');">';
                                    }else{
                                    	$checkbox = '';
                                    }
                                   $msg.= ' <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 notificationlist">
                                        <p>'.$message_body.'</p>
                               <p>'.$addedondatetime.' '.$checkbox.'</p>

                                    </div>
                                </div>
                               
                               <hr>
                               
                            </div>';

}
}else{
	$msg = "No Record Found.";
}
echo $msg; exit;

	}


function graphStructure()
{
	$this->load->view('master/tree_graph');
}

function ExpenseMaster()
{
	$this->load->view('master/expenses');

}

function addexpense()
{
	$head=$this->input->post('head');
	$cost=$this->input->post('cost');
	for($i=0;$i<count($head);$i++)
	{
		if($head[$i]!='' && $cost[$i]!='')
		{
	  $dtr=array('head'=>$head[$i],'cost'=>$cost[$i],'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
	  $this->db->insert('expense_master',$dtr);
		}
	}

	$edit_id=$this->input->post('edit_id');
	for($j=0;$j<count($edit_id);$j++)
	{
		$edit_head=$this->input->post('edit_head'.$edit_id[$i]);
		$edit_cost=$this->input->post('edit_cost'.$edit_id[$i]);
		$data=array('head'=>$edit_head,'cost'=>$edit_cost);
		$this->db->where('id',$edit_id[$i]);
		$this->db->update('expense_master',$data);
	}


	redirect(page_url.'Task/ExpenseMaster');
}


function deleteExpense()
{
	$id = $this->uri->segment(3);
	$this->db->where('id',$id);
	$this->db->delete('expense_master');
	$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank You! Record successfully deleted.</div>');
	redirect(page_url.'Task/ExpenseMaster');
}

function googlecurrencyconvertertool(){
	$basecurrency = "INR";
	$customercurrency = $this->input->post('currency');
	$orderamount = $this->input->post('order_value');
	$apiKey = 'ae29ccca0a2344afa2be8e5022302ff1';

// Base currency (the currency you want to convert from)
$baseCurrency = $customercurrency;

// Target currency (the currency you want to convert to)
$targetCurrency = 'INR';

// API endpoint URL
$apiUrl = "https://open.er-api.com/v6/latest/{$baseCurrency}?apikey={$apiKey}";

// Initialize cURL
$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
]);

// Execute cURL request
$response = curl_exec($curl);

// Check for errors
if (curl_error($curl)) {
    echo 'Error: ' . curl_error($curl);
    exit;
}

// Close cURL session
curl_close($curl);

// Decode JSON response
$data = json_decode($response, true);
//echo "<pre>"; print_r($data); exit;
// Check if API request was successful
if ($data['result'] =='success') {
    // Get the exchange rate for the target currency
    $exchangeRate = $data['rates'][$targetCurrency];

    $usdAmount = $orderamount; // Change this to your desired USD amount
    
    // Calculate the equivalent amount in INR
    $inrAmount = $usdAmount * $exchangeRate;
    echo round($inrAmount); 
    
    // Output the exchange rate
   // echo "1 {$baseCurrency} = {$exchangeRate} {$targetCurrency}";
} else {
    // Output error message
    echo "Error: {$data['description']}";
}
}

function getgooglecurrencyconvertertool(){
	$basecurrency = "INR";
	$customercurrency = $this->input->post('currency');
	$orderamount = $this->input->post('order_value');
	$apiKey = 'ae29ccca0a2344afa2be8e5022302ff1';

// Base currency (the currency you want to convert from)
$baseCurrency = $customercurrency;

// Target currency (the currency you want to convert to)
$targetCurrency = 'INR';

// API endpoint URL
$apiUrl = "https://open.er-api.com/v6/latest/{$baseCurrency}?apikey={$apiKey}";

// Initialize cURL
$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
]);

// Execute cURL request
$response = curl_exec($curl);

// Check for errors
if (curl_error($curl)) {
    echo 'Error: ' . curl_error($curl);
    exit;
}

// Close cURL session
curl_close($curl);

// Decode JSON response
$data = json_decode($response, true);
//echo "<pre>"; print_r($data); exit;
// Check if API request was successful
if ($data['result'] =='success') {
    // Get the exchange rate for the target currency
    $exchangeRate = $data['rates'][$targetCurrency];

    $usdAmount = $orderamount; // Change this to your desired USD amount
    
    // Calculate the equivalent amount in INR
    $inrAmount = $usdAmount * $exchangeRate;
    echo round($exchangeRate); 
    
    // Output the exchange rate
   // echo "1 {$baseCurrency} = {$exchangeRate} {$targetCurrency}";
} else {
    // Output error message
    echo "Error: {$data['description']}";
}
}

function dfgantchartNew()
{
$this->output->cache(5);
$this->load->view('charts/DF_Gant_chartNew');
}

function dfmeetingreminders(){
	$q = $this->db->select('a.end_date,a.id, b.task_name, a.df_id, c.df_no, c.df_description, d.task_message, d.department_id')
	->from('task_department_wise_scheduling a')
	->join('task_management b','a.taskid=b.task_id')
	->join('df_release c','a.df_id=c.id')
	->join('task_related_messages d','a.taskid=d.taskid')
	->where('a.taskid',4)->where('a.end_date',date('Y-m-d'))->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){
			$enddate = date('d-m-Y',strtotime($row->end_date));
			$dfdetail = $row->df_no." ".$row->df_description;
			$this->task->triggernotificationondfmeetingalert($row->df_no, $dfdetail, $enddate, $row->df_id);

		}
	}
}


public function yourtodaysduetaskreminderpdf()
{

$userid = $this->uri->segment(3);
$q = $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$userid)->get();
foreach($q->result() as $us);
$html='<style>li span{ font-weight:bold;}</style>';
$this->load->library('Pdf');
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SHUBHAM PACK');
$pdf->SetTitle("YOUR TODAYS SCHEDULED TASK");
$pdf->SetSubject('YOUR TODAYS SCHEDULED TASK');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);
$pdf->SetPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(2, 2, 2);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('pdfahelvetica', '', 12, '', true);
$pdf->AddPage();

$html='';
$html.='<table width="100%" style="padding:3px;">
<tr>
<td></td>
<td style="text-align:center;">
<img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" style="width:150px">
</td>
<td></td>
</tr>
</table>';

$startdate = date('Y-m-d');
$enddate = date('Y-m-d',strtotime($startdate."+7 DAYS"));
$dateformat = date('d-m-Y',strtotime($enddate));
$html.='<table  width="100%" style="padding:3px;" border="1" ruled="all">
<tr>
<th colspan="8" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">YOUR TODAYS SCHEDULED TASK</th>
</tr>
<tr>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:4%"><b>#</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>DF DETAIL</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>DF RELEASE DATE</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:22%"><b>TASK NAME</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:8%"><b>START DATE</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:5%"><b>DUE DATE OF COMPLETION</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>LAST REMARKS</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:8%"><b>STATUS</b></th>
</tr><tbody>';
$m=1;
$this->db->select('a.assigned_user, a.end_date,a.id,a.taskid, c.sortorder, c.is_it_mom, b.df_no, c.task_name, d.department, b.df_upload, b.added_on, a.remarks, a.po_id, a.df_id, a.id, f.first_name, f.last_name, c.task_frequency, b.df_description')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->join('task_management c','a.taskid=c.task_id')->join('departments d','a.department_id=d.department_id')->join('system_users f','a.assigned_user=f.user_id','left')->where('a.task_status',0)->where('a.end_date',date('Y-m-d'));
			$this->db->where('a.assigned_user',$userid);
			$query = $this->db->order_by('b.df_no','asc')->get();
			$res = $query->result();
			
	if($q->num_rows()>0){
		foreach($q->result() as $row){
			$html.='<tr>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$m.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.strtoupper($row->company_name).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$dfdetail.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.strtoupper($row->pono).' '.date('d-m-Y',strtotime($row->podate)).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.strtoupper($row->task_name).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.date('d-m-Y',strtotime($row->end_date)).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$row1->payment_percentage.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.$partpayment.'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.strtoupper($marketingperson).'</td>
      <td style="border:1px solid #000; font-size:10px; text-align:center;">'.strtoupper($taskstatus).'</td>
      
  </tr>';
$m++;
}
}

$html.='</tbody></table><br><br><br>';


echo $html; exit;


$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('overdue_payment_list.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = SITE_ROOT.'image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL=$filelocation."/Store_purchase_report_".date('Y-m-d').".pdf"; //Linux

 //$pdf->Output($fileNL, 'F');
 $pdf->Output($fileNL, 'I');
 
    
}

public function fetchreassignuserlist(){
	$scope = $this->getTaskDashboardScope();
	$option='<option value="">Select Member</option>';
	$departmentid = (int) $this->input->post('department');
	$selecteduser = $this->input->post('assigneduser');

	if (!$this->canCurrentUserAssignDepartmentTasks($departmentid, $scope)) {
		echo $option; exit;
	}

	$user_list = $this->getAssignableUsersForDepartment($departmentid, $scope);
	if (!empty($user_list)) {
		foreach ($user_list as $row) {
			$selected = ((int) $row['user_id'] === (int) $selecteduser) ? 'selected' : '';
			$option .= '<option value="' . (int) $row['user_id'] . '" ' . $selected . '>' . htmlspecialchars($row['label']) . '</option>';
		}
	}
	echo $option; exit;

}

public function currentlyassignedto(){
	$id = $this->input->post('id');
	$q = $this->db->select('b.first_name, b.last_name')->from('task_department_wise_scheduling a')->join('system_users b','a.assigned_user=b.user_id')->where('a.id',$id)->get();
	foreach($q->result() as $row);
	echo "CURRENTLY ASSIGNED TO ".strtoupper($row->first_name." ".$row->last_name); exit;
}

public function reassignselectedtask(){
	$scope = $this->getTaskDashboardScope();
	$user_id = (int) $scope['user_id'];		
	$id = (int) $this->input->post('selectedtasktoreassign');
	$reassign_user_id = (int) $this->input->post('showuserstoreassign');
	$task_row = $this->db->select('id, df_id, department_id')->from('task_department_wise_scheduling')->where('id', $id)->limit(1)->get()->row();

	if (!$task_row || !$this->canCurrentUserAssignDepartmentTasks((int) $task_row->department_id, $scope)) {
		$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">You do not have permission to reassign this task.</div>');
		redirect(page_url."Dashboard");
		return;
	}

	$assignable_users = $this->getAssignableUsersForDepartment((int) $task_row->department_id, $scope);
	$assignable_user_map = array();
	foreach ($assignable_users as $assignable_user) {
		$assignable_user_map[(int) $assignable_user['user_id']] = $assignable_user;
	}

	if (!isset($assignable_user_map[$reassign_user_id])) {
		$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Selected user is not available for this department.</div>');
		redirect(page_url."Dashboard");
		return;
	}

	$data = array('assigned_user'=>$reassign_user_id,
	'remarks'=>$this->input->post('reassignremarks'));
	$this->db->where('id',$id);
	$this->db->update('task_department_wise_scheduling',$data);
	$prmsg = $this->input->post('predefinedmessage');
	$this->task->sendnotificationtorespectiveteammemberforassignment($task_row->df_id, $prmsg, $reassign_user_id, $user_id);
	$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
	redirect(page_url."Dashboard");
}

function dfgantchartSharmaji()
{
	$this->output->cache(5);
$this->load->view('charts/DF_Gant_chartSharmaji');
}

function getdepartmentwiseusers(){
	$option = '<option value="">Select User</option>';
	$departmentid = $this->input->post('departmentid');
	$q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',$departmentid)->where('user_status',1)->where('hide_profile',0)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){
			$username = ucfirst(strtolower($row->first_name))." ".ucfirst(strtolower($row->last_name));
			$option .="<option value='".$row->user_id."'>".$username."</option>";
		}
	}
	echo $option; exit;
}

public function viewdfwiseticket(){
	$this->load->view('master/helpticket');
}


public function viewraisedtickets()
	{

		$i=1;
		$helpticketdata = array();
		$this->db->select('a.added_by, a.help_ticket_no, a.remarks, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no')->from('communication_ticket_system a')->join('departments b','a.department_id=b.department_id','left')->join('task_management c','a.task_id=c.task_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users e','a.user_id=e.user_id','left')->join('df_release f','a.df_id=f.id','left')->where('a.ticket_status',0);
		if($this->uri->segment(3)<>''){
			$this->db->where('a.df_id',$this->uri->segment(3));
		}
		if($this->uri->segment(4)<>''){
			$this->db->where('a.task_record_id',$this->uri->segment(4));
		}
		
		$query = $this->db->get();
		$res = $query->result();

$i=1;
		foreach($res as $row){
			$user_id =$this->session->userdata['logged_in']['user_id'];
			if($user_id==$row->added_by){
			$closestatus = '<a href="'.page_url.'Task/clicktoclose/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'" class="btn btn-xs btn-success">Click to Close</a>';
		}else{
			$closestatus = '';
		}

		$addyourcomment = '<span class="btn btn-primary btn-xs" onclick="showcommentbox('.$this->uri->segment(4).');">Add Your Comment</span>';
			$addcomment = '<a href"'.page_url.'Task/addcommentonticket/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'" class="btn btn-xs btn-primary">Add Comment</a>';

			$helpticketdata[] = array('sr_no'=>$i,
				'dfno'=>$row->df_no,
				'ticket_no'=>$row->help_ticket_no,
				'taskname'=>$row->task_name,
				'remarks'=>$row->remarks,
				'department'=>$row->department,
				'assignedto'=>ucwords(strtolower($row->fname." ".$row->lname)),
				'addedon'=>date('d-m-Y',strtotime($row->added_on))."<br> ".date('h:i A',strtotime($row->added_on)),
				'addedby'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
				'closestatus'=>$closestatus,
				'comment'=>$row->updated_remarks,
				'addcomment'=>$addyourcomment);

			$i++;

		}

			$results = array(

			"sEcho" => 1,

			"iTotalRecords" => count($helpticketdata),

			"iTotalDisplayRecords" => count($helpticketdata),

			"aaData"=>$helpticketdata);

			

		echo json_encode($results);

	}

	function clicktoclose(){
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$markasclosedby = $this->uri->segment(4);
		$recordid = $this->uri->segment(3);
		//$recordid = $this->uri->segment(5);

		$userseg = base64_encode($user_id);
		//echo $userseg; exit;

		$data = array('ticket_status'=>1,
			'ticket_closed_by'=>$user_id,
			'ticket_closed_on'=>date('Y-m-d H:i:s'));
		

		//echo "<pre>"; print_r($data); exit;

		//$this->db->where('df_id',$dfid);
		$this->db->where('id',$recordid);
		$this->db->update('communication_ticket_system',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully updated.</div>');
		redirect(page_url."Task/viewallrunninghelptickets/".base64_encode($markasclosedby));



	}

public function viewdfwisetickethistory(){
	$this->load->view('master/helptickethistory.php');
}


public function viewraisedticketshistorylist()
	{

		$i=1;
		$helpticketdata = array();
		$this->db->select('a.help_ticket_no, a.remarks, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no, g.first_name as closedbyfname, g.last_name as closedbylname, g.title as closedbytitle')->from('communication_ticket_system a')->join('departments b','a.department_id=b.department_id','left')->join('task_management c','a.task_id=c.task_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users e','a.updated_by=e.user_id','left')->join('df_release f','a.df_id=f.id','left')->join('system_users g','a.ticket_closed_by=g.user_id','left')->where('a.ticket_status',1);
		if($this->uri->segment(3)<>''){
			$userid = base64_decode($this->uri->segment(3));
			$this->db->where('a.added_by',$userid);
		}
		if($this->uri->segment(4)<>''){
			$this->db->where('a.task_record_id',$this->uri->segment(4));
		}
		
		$query = $this->db->get();
		$res = $query->result();

		$i=1;
		foreach($res as $row){

			$closedby = ucwords(strtolower($row->closedbytitle." ".$row->first_name." ".$row->last_name));
			$closestatus = "<b>Closed.</b> "."<br>".$closedby."<br>".date('d-m-Y H:i: A');

			$addcomment = '<a href"'.page_url.'Task/addcommentonticket/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'" class="btn btn-xs btn-primary">Add Comment</a>';
			$helpticketdata[] = array('sr_no'=>$i,
				'dfno'=>$row->df_no,
				'ticket_no'=>$row->help_ticket_no,
				'taskname'=>ucwords(strtolower($row->task_name)),
				'remarks'=>ucwords(strtolower($row->remarks)),
				'department'=>ucwords(strtolower($row->department)),
				'assignedto'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
				'addedon'=>date('d-m-Y',strtotime($row->added_on))."<br> ".date('h:i A',strtotime($row->added_on)),
				'addedby'=>ucwords(strtolower($row->fname." ".$row->lname)),
				'closestatus'=>$closestatus,
				'comment'=>$row->updated_remarks,
				'addcomment'=>$addcomment);

			$i++;

		}

			$results = array(

			"sEcho" => 1,

			"iTotalRecords" => count($helpticketdata),

			"iTotalDisplayRecords" => count($helpticketdata),

			"aaData"=>$helpticketdata);

			

		echo json_encode($results);

	}

	public function viewallrunninghelptickets(){
	$this->load->view('master/allrunninghelptickets.php');
}


public function viewallrunninghelpticketslist()
{
    $i = 1;
    $helpticketdata = array();

    $loggedInUserId = (int)$this->session->userdata['logged_in']['user_id'];
    $adminUserType  = (int)$this->session->userdata['logged_in']['adminuser'];

    $uid  = $this->uri->segment(3);
    $dfid = $this->uri->segment(4);

    if ($uid == '') {
        $uid = 'ALL';
    }

    if ($dfid == '') {
        $dfid = 'ALL';
    }

    $department_id = $this->task->getAssignedDepartment($loggedInUserId);
    $alluserofdepartment = array();

    if ($adminUserType == 2) {
        if (is_array($department_id) && !empty($department_id)) {
            $user_q = $this->db
                ->select('user_id')
                ->from('system_users')
                ->where_in('department_id', $department_id)
                ->get();

            if ($user_q->num_rows() > 0) {
                foreach ($user_q->result() as $userRow) {
                    $alluserofdepartment[] = (int)$userRow->user_id;
                }
            }
        }
    }

    $this->db->select('
        a.id,
        a.help_ticket_no,
        a.user_id,
        a.remarks,
        a.added_by,
        a.updated_remarks,
        a.added_on,
        a.updated_on,
        a.department_id,
        a.task_id,
        a.df_id,
        a.task_record_id,
        b.department,
        c.task_name,
        d.title,
        d.first_name,
        d.last_name,
        e.title as usertitle,
        e.first_name as fname,
        e.last_name as lname,
        f.df_no,
        f.on_hold
    ');
    $this->db->from('communication_ticket_system a');
    $this->db->join('departments b', 'a.department_id = b.department_id', 'left');
    $this->db->join('task_management c', 'a.task_id = c.task_id', 'left');
    $this->db->join('system_users d', 'a.added_by = d.user_id', 'left');
    $this->db->join('system_users e', 'a.user_id = e.user_id', 'left');
    $this->db->join('df_release f', 'a.df_id = f.id', 'left');

    $this->db->where('a.ticket_status', 0);
    $this->db->where('a.df_id >', 0);
    $this->db->where('f.df_status', 0);
    $this->db->where('(f.on_hold = 0 OR f.on_hold IS NULL)', null, false);

    /*
     * Access logic:
     * adminuser = 2: department/team view
     * adminuser = 3: normal user
     */
    if ($uid != '' && $uid != 'ALL') {
        if ($adminUserType == 2) {
            if (!empty($alluserofdepartment)) {
                $this->db->where_in('a.added_by', $alluserofdepartment);
            } else {
                $this->db->where('a.added_by', 0);
            }
        } elseif ($adminUserType == 3) {
            $this->db->where('a.added_by', $loggedInUserId);
        }
    } else {
        if ($adminUserType == 3) {
            $this->db->where('a.added_by', $loggedInUserId);
        } elseif ($adminUserType == 2 && !empty($alluserofdepartment)) {
            $this->db->where_in('a.added_by', $alluserofdepartment);
        }
    }

    if (!empty($dfid) && $dfid != 'ALL') {
        $this->db->where('a.df_id', $dfid);
    }

    if ($this->uri->segment(6) != '') {
        $this->db->where('a.task_record_id', $this->uri->segment(4));
    }

    $this->db->order_by('a.added_on', 'DESC');

    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {

            $ticketId = (int)$row->id;

            $dfNo = !empty($row->df_no) ? strtoupper($row->df_no) : '-';
            $ticketNo = !empty($row->help_ticket_no) ? $row->help_ticket_no : '-';
            $taskName = !empty($row->task_name) ? $row->task_name : '-';
            $remarks = !empty($row->remarks) ? nl2br(htmlspecialchars($row->remarks)) : '-';
            $department = !empty($row->department) ? ucwords(strtolower($row->department)) : '-';

            $assignedTo = trim($row->usertitle . ' ' . $row->fname . ' ' . $row->lname);
            $assignedTo = !empty($assignedTo) ? ucwords(strtolower($assignedTo)) : '-';

            $addedBy = trim($row->title . ' ' . $row->first_name . ' ' . $row->last_name);
            $addedBy = !empty($addedBy) ? ucwords(strtolower($addedBy)) : '-';

            $addedOn = '-';
            $addedOnSort = '';

            if (!empty($row->added_on) && $row->added_on != '0000-00-00 00:00:00') {
                $addedOn = date('d-m-Y', strtotime($row->added_on)) . '<br>' . date('h:i A', strtotime($row->added_on));
                $addedOnSort = date('Y-m-d H:i:s', strtotime($row->added_on));
            }

            /*
             * Close ticket permission:
             * 1. Ticket raiser can close
             * 2. Team leader can close
             * 3. adminuser 2 can close
             */
            $canClose = false;

            if ($loggedInUserId == (int)$row->added_by) {
                $canClose = true;
            }

            if ($adminUserType == 2) {
                $canClose = true;
            }

            $leader_q = $this->db
                ->select('team_leader')
                ->from('prestogroup_teams')
                ->where('team_leader', $loggedInUserId)
                ->limit(1)
                ->get();

            if ($leader_q->num_rows() > 0) {
                $canClose = true;
            }

            if ($canClose) {
                $closestatus = '<a href="' . page_url . 'Task/clicktoclose/' . $ticketId . '/' . $uid . '/' . $dfid . '" class="btn btn-xs btn-success btn-action" onclick="return confirm(\'Are you sure you want to close this ticket?\');">
                                    <i class="fa fa-check"></i> Close
                                </a>';
            } else {
                $closestatus = '<span class="status-pill pill-warning">Pending to Close</span>';
            }

            /*
             * Add comment permission:
             * Assigned person can add comment.
             */
            if ((int)$row->user_id == $loggedInUserId) {
                $addcomment = '<button type="button" class="btn btn-primary btn-xs btn-action" onclick="showcommentbox(' . $ticketId . ');">
                                    <i class="fa fa-comment"></i> Add Comment
                               </button>';
            } else {
                $addcomment = '<span class="status-pill pill-info">Assigned User Only</span>';
            }

            /*
             * Latest comment
             */
            $commenthistory = '<span class="text-muted">No comment yet</span>';

            $comment_q = $this->db
                ->select('a.comment, a.added_on, b.title, b.first_name, b.last_name')
                ->from('communication_chain a')
                ->join('system_users b', 'a.added_by = b.user_id', 'left')
                ->where('a.record_id', $ticketId)
                ->order_by('a.id', 'DESC')
                ->limit(1)
                ->get();

            if ($comment_q->num_rows() > 0) {
                $comrow = $comment_q->row();

                $commentBy = trim($comrow->title . ' ' . $comrow->first_name . ' ' . $comrow->last_name);
                $commentBy = !empty($commentBy) ? ucwords(strtolower($commentBy)) : '-';

                $commentDate = '-';
                if (!empty($comrow->added_on) && $comrow->added_on != '0000-00-00 00:00:00') {
                    $commentDate = date('d-m-Y', strtotime($comrow->added_on)) . ' ' . date('h:i A', strtotime($comrow->added_on));
                }

                $commentText = !empty($comrow->comment) ? nl2br(htmlspecialchars($comrow->comment)) : '-';

                $commenthistory = '
                    <div class="latest-comment-box">
                        <div class="latest-comment-text">' . $commentText . '</div>
                        <div class="latest-comment-meta">
                            By: ' . $commentBy . '<br>
                            On: ' . $commentDate . '
                        </div>
                        <button type="button" class="btn btn-link btn-xs" onclick="showcommunicationhistory(' . $ticketId . ');">
                            View Full History
                        </button>
                    </div>
                ';
            }

            /*
             * Delay calculation
             */
            $delayCount = 0;

            if (!empty($row->added_on) && $row->added_on != '0000-00-00 00:00:00') {
                $addedondate = date('Y-m-d', strtotime($row->added_on));
                $todaysdate = date('Y-m-d');

                $date1 = new DateTime($addedondate);
                $date2 = new DateTime($todaysdate);
                $interval = $date1->diff($date2);
                $delayCount = (int)$interval->days;
            }

            $delayLabel = '<span class="status-pill pill-success">Today</span>';
            $delayStatus = 'Today';

            if ($delayCount > 3) {
                $delayLabel = '<span class="status-pill pill-danger">' . $delayCount . ' Days</span>';
                $delayStatus = 'Critical';
            } elseif ($delayCount > 1) {
                $delayLabel = '<span class="status-pill pill-warning">' . $delayCount . ' Days</span>';
                $delayStatus = 'Delayed';
            } elseif ($delayCount == 1) {
                $delayLabel = '<span class="status-pill pill-info">1 Day</span>';
                $delayStatus = 'Fresh';
            }

            $helpticketdata[] = array(
                'sr_no' => $i,
                'dfno' => $dfNo,
                'ticket_no' => '<span class="ticket-no-badge">' . $ticketNo . '</span>',
                'taskname' => $taskName,
                'remarks' => '<div class="remarks-box">' . $remarks . '</div>',
                'department' => $department,
                'assignedto' => $assignedTo,
                'addedon' => $addedOn,
                'addedon_sort' => $addedOnSort,
                'addedby' => $addedBy,
                'closestatus' => $closestatus,
                'delay' => $delayLabel,
                'delay_days' => $delayCount,
                'delay_status' => $delayStatus,
                'comment' => $commenthistory,
                'addcomment' => $addcomment
            );

            $i++;
        }
    }

    $results = array(
        "draw" => intval($this->input->get('draw')),
        "recordsTotal" => count($helpticketdata),
        "recordsFiltered" => count($helpticketdata),

        "sEcho" => 1,
        "iTotalRecords" => count($helpticketdata),
        "iTotalDisplayRecords" => count($helpticketdata),
        "aaData" => $helpticketdata,
        "data" => $helpticketdata
    );

    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}

	public function viewdfwisetickethistory_ofyour_team(){
	$this->load->view('master/helptickethistory.php');
}


public function helpticketsforyourteamlist_history_by_your_team()
	{

		$user_id =$this->session->userdata['logged_in']['user_id'];
		$department_id=$this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
		$i=1;
		$helpticketdata = array();
		$this->db->select('a.id, a.user_id, a.help_ticket_no, a.remarks,a.added_by, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no')->from('communication_ticket_system a')->join('departments b','a.department_id=b.department_id','left')->join('task_management c','a.task_id=c.task_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users e','a.user_id=e.user_id','left')->join('df_release f','a.df_id=f.id','left')->where('a.ticket_status',1);
		if($_SESSION['logged_in']['adminuser']==2)
		{
			$this->db->where_in('a.department_id',$department_id,false);
		}
		

		if($this->uri->segment(5)<>''){
			$this->db->where('a.df_id',$this->uri->segment(3));
		}
		if($this->uri->segment(6)<>''){
			$this->db->where('a.task_record_id',$this->uri->segment(4));
		}
		
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row){
			$closestatus  = "";
			$user_id =$this->session->userdata['logged_in']['user_id'];
			if($user_id==$row->added_by){
			$closestatus = '<a href="'.page_url.'Task/clicktoclose/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'" class="btn btn-xs btn-success">Click to Close</a>';
		}else{
			$closestatus = "Pending to Close";
		}
		$addcomment = '';
		$commenthistory = '';
		if($row->user_id==$_SESSION['logged_in']['user_id']){
			$addcomment = '<span class="btn btn-primary btn-xs" onclick="showcommentbox('.$row->id.');">Add Comment</span>';
			$q = $this->db->select('a.comment, a.added_on, b.title, b.first_name, b.last_name')->from('communication_chain a')->join('system_users b','a.added_by=b.user_id','left')->where('record_id',$row->id)->order_by('a.id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $comrow);
				$commenthistory.=$comrow->comment."<br>Added By: ".$comrow->title." ".$comrow->first_name." ".$comrow->last_name."<br>Added On: ".date('d-m-Y',strtotime($comrow->added_on))."<br>".date('h:i A',strtotime($comrow->added_on));
				$commenthistory.="<br><br><span onclick='showcommunicationhistorydata(".$row->id.");'><u>Click here to View History</u></span>";

			}
			}else{
			$q = $this->db->select('a.comment, a.added_on, b.title, b.first_name, b.last_name')->from('communication_chain a')->join('system_users b','a.added_by=b.user_id','left')->where('record_id',$row->id)->order_by('a.id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $comrow);
				$commenthistory.=$comrow->comment."<br>Added By: ".$comrow->title." ".$comrow->first_name." ".$comrow->last_name."<br>Added On: ".date('d-m-Y',strtotime($comrow->added_on))."<br>".date('h:i A',strtotime($comrow->added_on));
				$commenthistory.="<br><br><span onclick='showcommunicationhistorydata(".$row->id.");'><u>Click here to View History</u></span>";

			}

}
			$addedondate = date('Y-m-d',strtotime($row->added_on));
			$todaysdate = date('Y-m-d');
			$date1 = new DateTime($addedondate);
			$date2 = new DateTime($todaysdate);
			$interval = $date1->diff($date2);
			$delayCount = $interval->days;
			if($delayCount>1){
				$delaydays = "<span style='color:red; font-weight:bold;'>".$delayCount." Days</span>";
			}else{
				$delaydays = "";
			}

			$helpticketdata[] = array('sr_no'=>$i,
				'dfno'=>$row->df_no,
				'ticket_no'=>$row->help_ticket_no,
				'taskname'=>$row->task_name,
				'remarks'=>$row->remarks,
				'department'=>ucwords(strtolower($row->department)),
				'assignedto'=>ucwords(strtolower($row->fname." ".$row->lname)),
				'addedon'=>date('d-m-Y',strtotime($row->added_on))."<br> ".date('h:i A',strtotime($row->added_on)),
				'addedby'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
				'closestatus'=>$closestatus,
				'comment'=>$commenthistory,
				'delay'=>$delaydays,
				'addcomment'=>$addcomment);

			$i++;

		}

			$results = array(

			"sEcho" => 1,

			"iTotalRecords" => count($helpticketdata),

			"iTotalDisplayRecords" => count($helpticketdata),

			"aaData"=>$helpticketdata);

			

		echo json_encode($results);

	}

	public function helpticketsforyourteam(){
	$this->load->view('master/helpticketforyourteam.php');
}

public function helpticketsforyourteamlist()
	{

	$user_id =$this->session->userdata['logged_in']['user_id'];
		$department_id=$this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
		$i=1;
		$helpticketdata = array();
		$this->db->select('a.id, a.user_id, a.help_ticket_no, a.remarks,a.added_by, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no')->from('communication_ticket_system a')->join('departments b','a.department_id=b.department_id','left')->join('task_management c','a.task_id=c.task_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users e','a.user_id=e.user_id','left')->join('df_release f','a.df_id=f.id','left')->where('a.ticket_status',0)->where('a.df_id >', 0)->where('f.df_status', 0)->where('(f.on_hold = 0 OR f.on_hold IS NULL)', null, false);
		if($this->uri->segment(3)<>''){
		if($_SESSION['logged_in']['adminuser']==2)
		{
			$this->db->where_in('a.department_id',$department_id,false);
		}
		}

		if($this->uri->segment(5)<>''){
			$this->db->where('a.df_id',$this->uri->segment(3));
		}
		if($this->uri->segment(6)<>''){
			$this->db->where('a.task_record_id',$this->uri->segment(4));
		}
		
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row){
			$closestatus  = "";
			$user_id =$this->session->userdata['logged_in']['user_id'];
			if($user_id==$row->added_by){
			$closestatus = '<a href="'.page_url.'Task/clicktoclose/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'" class="btn btn-xs btn-success">Click to Close</a>';
		}else{
			$closestatus = "Pending to Close";
		}
		$addcomment = '';
		$commenthistory = '';
		if($row->user_id==$_SESSION['logged_in']['user_id']){
			$addcomment = '<span class="btn btn-primary btn-xs" onclick="showcommentbox('.$row->id.');">Add Comment</span>';
			$q = $this->db->select('a.comment, a.added_on, b.title, b.first_name, b.last_name')->from('communication_chain a')->join('system_users b','a.added_by=b.user_id','left')->where('record_id',$row->id)->order_by('a.id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $comrow);
				$commenthistory.=$comrow->comment."<br>Added By: ".$comrow->title." ".$comrow->first_name." ".$comrow->last_name."<br>Added On: ".date('d-m-Y',strtotime($comrow->added_on))."<br>".date('h:i A',strtotime($comrow->added_on));
				$commenthistory.="<br><br><span onclick='showcommunicationhistorydata(".$row->id.");'><u>Click here to View History</u></span>";

			}
			}else{
			$q = $this->db->select('a.comment, a.added_on, b.title, b.first_name, b.last_name')->from('communication_chain a')->join('system_users b','a.added_by=b.user_id','left')->where('record_id',$row->id)->order_by('a.id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $comrow);
				$commenthistory.=$comrow->comment."<br>Added By: ".$comrow->title." ".$comrow->first_name." ".$comrow->last_name."<br>Added On: ".date('d-m-Y',strtotime($comrow->added_on))."<br>".date('h:i A',strtotime($comrow->added_on));
				$commenthistory.="<br><br><span onclick='showcommunicationhistorydata(".$row->id.");'><u>Click here to View History</u></span>";

			}

}
			$addedondate = date('Y-m-d',strtotime($row->added_on));
			$todaysdate = date('Y-m-d');
			$date1 = new DateTime($addedondate);
			$date2 = new DateTime($todaysdate);
			$interval = $date1->diff($date2);
			$delayCount = $interval->days;
			if($delayCount>1){
				$delaydays = "<span style='color:red; font-weight:bold;'>".$delayCount." Days</span>";
			}else{
				$delaydays = "";
			}

			$helpticketdata[] = array('sr_no'=>$i,
				'dfno'=>$row->df_no,
				'ticket_no'=>$row->help_ticket_no,
				'taskname'=>$row->task_name,
				'remarks'=>$row->remarks,
				'department'=>ucwords(strtolower($row->department)),
				'assignedto'=>ucwords(strtolower($row->fname." ".$row->lname)),
				'addedon'=>date('d-m-Y',strtotime($row->added_on))."<br> ".date('h:i A',strtotime($row->added_on)),
				'addedby'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
				'closestatus'=>$closestatus,
				'comment'=>$commenthistory,
				'delay'=>$delaydays,
				'addcomment'=>$addcomment);

			$i++;

		}

			$results = array(

			"sEcho" => 1,

			"iTotalRecords" => count($helpticketdata),

			"iTotalDisplayRecords" => count($helpticketdata),

			"aaData"=>$helpticketdata);

			

		echo json_encode($results);

	}
	public function helpticketsforyou(){
	$this->load->view('master/helpticketsforyou.php');
}
	public function helpticketsforyoulist()
{
    $i = 1;
    $helpticketdata = array();

    $loggedInUserId = (int)$this->session->userdata['logged_in']['user_id'];
    $adminUserType  = (int)$this->session->userdata['logged_in']['adminuser'];

    $uriUserId = $this->uri->segment(3);
    $dfFilter  = $this->uri->segment(4);
    $taskRecordFilter = $this->uri->segment(5);

    $department_id = $this->task->getAssignedDepartment($loggedInUserId);

    $this->db->select('
        a.id,
        a.user_id,
        a.help_ticket_no,
        a.remarks,
        a.added_by,
        a.updated_remarks,
        a.added_on,
        a.updated_on,
        a.department_id,
        a.task_id,
        a.df_id,
        a.task_record_id,
        b.department,
        c.task_name,
        d.title,
        d.first_name,
        d.last_name,
        e.title as usertitle,
        e.first_name as fname,
        e.last_name as lname,
        f.df_no
    ');
    $this->db->from('communication_ticket_system a');
    $this->db->join('departments b', 'a.department_id = b.department_id', 'left');
    $this->db->join('task_management c', 'a.task_id = c.task_id', 'left');
    $this->db->join('system_users d', 'a.added_by = d.user_id', 'left');
    $this->db->join('system_users e', 'a.user_id = e.user_id', 'left');
    $this->db->join('df_release f', 'a.df_id = f.id', 'left');

    $this->db->where('a.ticket_status', 0);
    $this->db->where('a.df_id >', 0);
    $this->db->where('f.df_status', 0);
    $this->db->where('(f.on_hold = 0 OR f.on_hold IS NULL)', null, false);

    /*
     * Permission logic:
     * adminuser = 2: department head / assigned department access
     * adminuser = 3: normal user, only assigned tickets
     */
    if ($uriUserId != '') {
        if ($adminUserType == 2) {
            if (!empty($department_id)) {
                $this->db->where_in('a.department_id', $department_id, false);
            }
        } elseif ($adminUserType == 3) {
            $this->db->where('a.user_id', $loggedInUserId);
        }
    } else {
        if ($adminUserType == 3) {
            $this->db->where('a.user_id', $loggedInUserId);
        }
    }

    /*
     * DF filter.
     * Existing view redirects: Task/filterticket/userid/dfid
     * If this function receives df id in segment 4, filter by it.
     */
    if (!empty($dfFilter) && $dfFilter != 'ALL') {
        $this->db->where('a.df_id', $dfFilter);
    }

    if (!empty($taskRecordFilter)) {
        $this->db->where('a.task_record_id', $taskRecordFilter);
    }

    $this->db->order_by('a.added_on', 'DESC');

    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        foreach ($query->result() as $row) {

            if ($uriUserId != '') {
                $decodedUserId = base64_decode($uriUserId);
                $loggedinuserid = !empty($decodedUserId) ? $decodedUserId : $loggedInUserId;
            } else {
                $loggedinuserid = $loggedInUserId;
            }

            $ticketId = (int)$row->id;

            $dfNo = !empty($row->df_no) ? strtoupper($row->df_no) : '-';
            $ticketNo = !empty($row->help_ticket_no) ? $row->help_ticket_no : '-';
            $taskName = !empty($row->task_name) ? $row->task_name : '-';
            $department = !empty($row->department) ? ucwords(strtolower($row->department)) : '-';

            $assignedTo = trim($row->usertitle . ' ' . $row->fname . ' ' . $row->lname);
            $assignedTo = !empty($assignedTo) ? ucwords(strtolower($assignedTo)) : '-';

            $addedBy = trim($row->title . ' ' . $row->first_name . ' ' . $row->last_name);
            $addedBy = !empty($addedBy) ? ucwords(strtolower($addedBy)) : '-';

            $remarks = !empty($row->remarks) ? nl2br(htmlspecialchars($row->remarks)) : '-';

            $addedOnDate = '';
            $addedOnSort = '';
            if (!empty($row->added_on) && $row->added_on != '0000-00-00 00:00:00') {
                $addedOnDate = date('d-m-Y', strtotime($row->added_on)) . "<br>" . date('h:i A', strtotime($row->added_on));
                $addedOnSort = date('Y-m-d H:i:s', strtotime($row->added_on));
            } else {
                $addedOnDate = '-';
                $addedOnSort = '';
            }

            /*
             * Delay calculation
             */
            $delayCount = 0;
            if (!empty($row->added_on) && $row->added_on != '0000-00-00 00:00:00') {
                $addedondate = date('Y-m-d', strtotime($row->added_on));
                $todaysdate = date('Y-m-d');

                $date1 = new DateTime($addedondate);
                $date2 = new DateTime($todaysdate);
                $interval = $date1->diff($date2);
                $delayCount = (int)$interval->days;
            }

            $delayLabel = '<span class="status-pill pill-success">Today</span>';
            $delayStatus = 'Today';

            if ($delayCount > 3) {
                $delayLabel = '<span class="status-pill pill-danger">' . $delayCount . ' Days</span>';
                $delayStatus = 'Critical';
            } elseif ($delayCount > 1) {
                $delayLabel = '<span class="status-pill pill-warning">' . $delayCount . ' Days</span>';
                $delayStatus = 'Delayed';
            } elseif ($delayCount == 1) {
                $delayLabel = '<span class="status-pill pill-info">1 Day</span>';
                $delayStatus = 'Fresh';
            }

            /*
             * Close ticket permission
             */
            $closestatus = '';
            if ($loggedInUserId == (int)$row->added_by || $adminUserType == 2) {
                $closestatus = '<a href="' . page_url . 'Task/clicktoclose/' . $loggedinuserid . '/' . $ticketId . '" class="btn btn-xs btn-success btn-action" onclick="return confirm(\'Are you sure you want to close this ticket?\');">
                                    <i class="fa fa-check"></i> Close
                                </a>';
            } else {
                $closestatus = '<span class="status-pill pill-warning">Pending to Close</span>';
            }

            /*
             * Add comment permission:
             * Assigned user can add comment.
             */
            $addcomment = '';
            if ((int)$row->user_id == $loggedInUserId) {
                $addcomment = '<button type="button" class="btn btn-primary btn-xs btn-action" onclick="showcommentbox(' . $ticketId . ');">
                                    <i class="fa fa-comment"></i> Add Comment
                               </button>';
            } else {
                $addcomment = '<span class="status-pill pill-info">Assigned User Only</span>';
            }

            /*
             * Latest comment history
             */
            $commenthistory = '<span class="text-muted">No comment yet</span>';

            $comment_q = $this->db
                ->select('a.comment, a.added_on, b.title, b.first_name, b.last_name')
                ->from('communication_chain a')
                ->join('system_users b', 'a.added_by = b.user_id', 'left')
                ->where('a.record_id', $ticketId)
                ->order_by('a.id', 'DESC')
                ->limit(1)
                ->get();

            if ($comment_q->num_rows() > 0) {
                $comrow = $comment_q->row();

                $commentBy = trim($comrow->title . ' ' . $comrow->first_name . ' ' . $comrow->last_name);
                $commentBy = !empty($commentBy) ? ucwords(strtolower($commentBy)) : '-';

                $commentDate = '';
                if (!empty($comrow->added_on) && $comrow->added_on != '0000-00-00 00:00:00') {
                    $commentDate = date('d-m-Y', strtotime($comrow->added_on)) . ' ' . date('h:i A', strtotime($comrow->added_on));
                }

                $commentText = !empty($comrow->comment) ? nl2br(htmlspecialchars($comrow->comment)) : '-';

                $commenthistory = '
                    <div class="latest-comment-box">
                        <div class="latest-comment-text">' . $commentText . '</div>
                        <div class="latest-comment-meta">
                            By: ' . $commentBy . '<br>
                            On: ' . $commentDate . '
                        </div>
                        <button type="button" class="btn btn-link btn-xs" onclick="showcommunicationhistorydata(' . $ticketId . ');">
                            View Full History
                        </button>
                    </div>
                ';
            }

            $helpticketdata[] = array(
                'sr_no' => $i,
                'dfno' => $dfNo,
                'ticket_no' => '<span class="ticket-no-badge">' . $ticketNo . '</span>',
                'taskname' => $taskName,
                'remarks' => '<div class="remarks-box">' . $remarks . '</div>',
                'department' => $department,
                'assignedto' => $assignedTo,
                'addedon' => $addedOnDate,
                'addedon_sort' => $addedOnSort,
                'addedby' => $addedBy,
                'closestatus' => $closestatus,
                'comment' => $commenthistory,
                'delay' => $delayLabel,
                'delay_days' => $delayCount,
                'delay_status' => $delayStatus,
                'addcomment' => $addcomment
            );

            $i++;
        }
    }

    $results = array(
        "draw" => intval($this->input->get('draw')),
        "recordsTotal" => count($helpticketdata),
        "recordsFiltered" => count($helpticketdata),

        "sEcho" => 1,
        "iTotalRecords" => count($helpticketdata),
        "iTotalDisplayRecords" => count($helpticketdata),
        "aaData" => $helpticketdata,
        "data" => $helpticketdata
    );

    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}

	function notificationmarkasread(){
		$recordid = $this->uri->segment(3);
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$updatedon = date('Y-m-d H:i:s');

		$data = array('status'=>1,'accepted_on'=>$updatedon,'accepted_by'=>$user_id);
		$this->db->where('id',$recordid);
		$res = $this->db->update('task_intimation_alert',$data);
		if($res>0){
			echo "Updated"; exit;
		}

	}

public function addyourcommentonticket()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $usr = base64_encode($user_id);
    $comment = $this->input->post('yourcomment');

    if ($comment <> '') {
        // Insert comment into 'communication_chain'
        $data = array(
            'record_id' => $this->input->post('recordid'),
            'comment' => $this->input->post('yourcomment'),
            'added_on' => date('Y-m-d H:i:s'),
            'added_by' => $user_id
        );
        $this->db->insert('communication_chain', $data);

        // Update 'communication_ticket_system'
        $data1 = array(
            'updated_remarks' => $this->input->post('yourcomment'),
            'updated_on' => date('Y-m-d H:i:s'),
            'updated_by' => $user_id
        );
        $this->db->where('id', $this->input->post('recordid'));
        $this->db->update('communication_ticket_system', $data1);

        // Fetch DF details and user email for notification
      $this->db->select('a.help_ticket_no, b.email, b.first_name, b.last_name, c.task_name, d.df_no, a.added_by')
            ->from('communication_ticket_system a')
            ->join('system_users b', 'a.added_by = b.user_id', 'left')
            ->join('task_management c','a.task_id=c.task_id','left')
            ->join('df_release d','a.df_id=d.id','left')
            ->where('a.id', $this->input->post('recordid'));
        $query = $this->db->get();
        

        if ($query->num_rows() > 0) {
            $ticket_data = $query->row();

            /* Get Team Leader Email ID */
            $q = $this->db->select('team_id')
                ->from('presto_team_members')
                ->where('employee_id', $ticket_data->added_by)
                ->get();

            $teamleaderemailid = '';
            if ($q->num_rows() > 0) {
                $row22 = $q->row();
                $q1 = $this->db->select('b.email')
                    ->from('prestogroup_teams a')
                    ->join('system_users b', 'a.team_leader = b.user_id', 'left')
                    ->where('a.team_id', $row22->team_id)
                    ->get();
                if ($q1->num_rows() > 0) {
                    $row23 = $q1->row();
                    $teamleaderemailid = $row23->email;
                }
            }
            /* End Team Leader Email ID */

            $notification_recipients = array(
                $ticket_data->added_by,
                $this->task->getTeamLeaderIdByUser($ticket_data->added_by),
                $this->task->getTeamLeaderIdByUser($user_id)
            );

            $notification_message = $this->task->buildTicketNotificationMessage(
                'New update received on your help ticket',
                $ticket_data->help_ticket_no,
                $ticket_data->df_no,
                $ticket_data->task_name,
                $comment,
                $this->session->userdata['logged_in']['name']
            );

            $this->task->createTicketNotifications(
                $notification_recipients,
                $this->input->post('recordid'),
                $ticket_data->df_no,
                $notification_message
            );

            // Email Notification
            $to_email = $ticket_data->email;
            $user_name = $ticket_data->first_name . ' ' . $ticket_data->last_name;
            $ticket_no = $ticket_data->help_ticket_no;
            $dfno = $ticket_data->df_no;

            $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging');
            $this->email->to($to_email);
            //$this->email->to('mangleshup@gmail.com');
            if ($teamleaderemailid <> '') {
                $this->email->cc($teamleaderemailid);
            }
            $this->email->cc('groupceo@shubhampack.com');
            $this->email->bcc('mangleshup@gmail.com');
            $this->email->subject('New Comment Added to Help Ticket #' . $ticket_no . " DF No. " . $dfno);

            $email_message = "<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .email-container { border: 1px solid #ccc; border-radius: 8px; padding: 20px; max-width: 600px; margin: auto; }
        .header { background-color: #4872b8; color: #fff; padding: 10px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { margin: 20px 0; }
        .ticket-info { background-color: #f9f9f9; padding: 10px; border-left: 4px solid #4872b8; margin-bottom: 20px; }
        .comment-box { background-color: #f4f4f4; border: 1px solid #ddd; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-style: italic; }
        .footer { font-size: 12px; color: #777; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <h2>New Comment Notification</h2>
        </div>
        <div class='content'>
            <p>Dear <strong>$user_name</strong>,</p>
            <p>A new comment has been added to your Help Ticket:</p>
            <div class='ticket-info'>
                <p><strong>Ticket Number:</strong> #{$ticket_no}</p>
                <p><strong>DF Number:</strong> {$dfno}</p>
                <p><strong>Task Name:</strong> {$ticket_data->task_name}</p>
            </div>
            <div class='comment-box'>
                \"$comment\"
            </div>
            <p><strong>Added by:</strong> " . $this->session->userdata['logged_in']['name'] . "</p>
            <p><strong>Date:</strong> " . date('d-m-Y H:i:s') . "</p>
        </div>
        <div class='footer'>
            <p>Thank you,<br>The Shubham Flexible Packaging Team</p>
        </div>
    </div>
</body>
</html>";

            $this->email->message($email_message);

            if ($this->email->send()) {
                $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Thank you! Record successfully added and email notification sent.</div>');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">Record added, but email notification failed.</div>');
            }
        }

        redirect(page_url . 'Task/helpticketsforyou/' . $usr);
    } else {
        $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Sorry! Please add your comment.</div>');
        redirect(page_url . 'Task/helpticketsforyou/' . $usr);
    }
}




function getallcommunicationoftask(){
	$taskid = $this->input->post('taskid');
	$commenthistory= "";
	$i=1;
	$q = $this->db->select('a.comment, a.added_on, b.title, b.first_name, b.last_name, a.record_id')->from('communication_chain a')->join('system_users b','a.added_by=b.user_id','left')->where('record_id',$taskid)->order_by('a.id','desc')->get();
	if($q->num_rows()>0){
		foreach($q->result() as $comrow){
			
			$qq = $this->db->select('help_ticket_no')->from('communication_ticket_system')->where('id',$comrow->record_id)->get();
			foreach($qq->result() as $ticketinfo);
			
			$name = $comrow->title." ".$comrow->first_name." ".$comrow->last_name;
			$commenthistory.=' <div class="timeline-item left">
        <div class="timeline-content">
            <h2>Ticket '.$ticketinfo->help_ticket_no.'</h2>
            <p>Comment Added On: '.date('d-m-Y',strtotime($comrow->added_on)).'</p>
            <p>Comments: '.$comrow->comment.'</p>
            <p>Comment Added By: '.$name.'</p>
        </div>
    </div>';
		$i++;}
	}


	echo "<p>".$commenthistory."</p>"; exit;

}

function timeline(){
	$this->load->view('master/timeline');
}
function checkusrticket(){
$department_id=$this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
$userid = $this->getteamwisetickets($department_id);
$this->db->select('id')->from('communication_ticket_system')->where('ticket_status',0);
if($userid){
	$this->db->where_in('added_by',$userid,false);
}
$q = $this->db->get();
echo "<pre>"; print_r($q->result()); exit;

}

function getteamwisetickets($departmentid){
	$q = $this->db->select('user_id')->from('system_users')->where_in('department_id',$departmentid,false)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){
			$userid[]= $row->user_id;
		}
		return $userid;
	}

}
	public function orderwonpoupload()
		{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('existingpaymentterms', 'Payment Term', 'required|trim');
		$this->form_validation->set_rules('companyname', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('pono', 'Po Number', 'required|trim');
		$this->form_validation->set_rules('podate', 'PO Date', 'required|trim');
		//$this->form_validation->set_rules('order_value', 'Order Value', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('opportunity/order_won_stage');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$companyname = ucwords(strtolower($this->input->post('companyname')));
		$photo=$_FILES['attachpo']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$poattachment=time().'.'.$cat_image;
				move_uploaded_file($_FILES['attachpo']["tmp_name"],UPLOADPATH.'Taskdocument/' . $poattachment);
			}else
			{
				$poattachment="";
				}

			if(isset($_REQUEST['orderwonmachinename'])){	
					$tags1=count($_REQUEST['orderwonmachinename']);
					$machineinfomation = $_REQUEST['orderwonmachinename'];
					$ordervalue = $this->input->post('order_value');
					if($tags1>0)
					{
				$i=1;
				for($x=0;$x<$tags1;$x++){
				if($machineinfomation[$x]!='')
				{

				$orderamountarr[] = $ordervalue[$x];	
				$data = array('company_name'=>$this->input->post('companyname'),
				'pono'=>$this->input->post('pono'),
				'podate'=>date('Y-m-d',strtotime($this->input->post('podate'))),
				'po_attachment'=>$poattachment,
				'payment_term'=>$this->input->post('existingpaymentterms'),
				'added_on'=>$date,
				'order_value'=>$ordervalue[$x],
				'customer_currency'=>$this->input->post('currency'),
				'amount_in_customer_currency'=>$this->input->post('ordervalueincustomercurrency'),
				'added_by'=>$user_id);
				$this->db->insert('poreceived',$data);
				$polastid = $this->db->insert_id();

				$nexttaskid = 0;
				$departmentid = 0;
				$enddate = date('Y-m-d');

				/*Notification of PO Release*/
				$this->task->notificationofprocessdone(1);
				/*Notification of PO Release*/



				/*PO Release entry in Task scheduling table*/
				$data1 = array('df_id'=>0,
				'taskid'=>1,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d'),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$polastid,
				'task_status'=>1,
				'remarks'=>'',
				'task_completed_on'=>date('Y-m-d H:i:s'),
				'task_completed_by'=>$user_id,
				'assigned_user'=>$user_id, 
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'),
				'userid'=>$user_id);
				$this->db->insert('task_department_wise_scheduling',$data1);
				$q = $this->db->select('task_id, department_id, tat')->from('task_management')->where('task_id',2)->order_by('sortorder','ASC')->limit(1)->get();
				if($q->num_rows()>0){
				foreach($q->result() as $row1);

				$nexttaskid = $row1->task_id;
				$departmentid = $row1->department_id;

				$startdate = date('Y-m-d');
				$enddate = date('Y-m-d', strtotime('+'.$row1->tat.' days', strtotime($startdate)));

				$skipped_dates = $this->task->SKIP_holidays($startdate, $enddate);

				$startdate = $skipped_dates['start_date'];
				$enddate = $skipped_dates['end_date'];

				//echo $startdate."<br/>".$enddate; exit;
				$data1 = array('df_id'=>0,
				'taskid'=>$nexttaskid,
				'department_id'=>$departmentid,
				'start_date'=>$startdate,
				'end_date'=>$enddate,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'assigned_user'=>$user_id, 
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'),
				'po_id'=>$polastid);

				$this->db->insert('task_department_wise_scheduling',$data1);
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
				redirect(page_url.'Task/poreceived');

				}else{
				$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry, PI has not being uploaded.</div>');
				redirect(page_url.'Task/poreceived');
				}	

				}
				$i++;	
				}
				}
				}
		$ordergrandtotal = array_sum($orderamountarr);
		$formatted_amount = indian_number_format($ordergrandtotal);
			$d=array(
					'lead_id'=>$this->uri->segment(3),
					'lead_status'=>35,
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);
			$this->db->insert('progress_remarks',$d);	
			$q = $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
			foreach($q->result() as $rowss);
$orderwonbyuser = ucwords(strtolower($rowss->title." ".$rowss->first_name." ".$rowss->last_name));


			$message = '🏆 Great News! 🏆

We are thrilled to announce that Shubham Pack has successfully won a new order from '.$companyname.'!

Order Amount: '.$formatted_amount.'

Order Won By: '.$orderwonbyuser.'

Regards,
Shubham Pack 📦 ';

$usercontact = "9818505161";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://app.messageautosender.com/api/v1/message/create');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
$post = array(
//'receiverMobileNo' => '91'.$contactnumber,
'receiverMobileNo' => '91'.$usercontact,
'username' => whatsappuser2,
'password' => whatsapppass2,
'message'=>strip_tags($message));

curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$result = curl_exec($ch);
//echo $result; exit;
if (curl_errno($ch)) {
echo 'Error:' . curl_error($ch);
}
curl_close($ch);

		
		}
		
	}


function indian_number_format($num) {
    $num_parts = explode('.', $num); // Split the number into integer and decimal parts
    $integer_part = $num_parts[0];
    $decimal_part = isset($num_parts[1]) ? '.' . $num_parts[1] : '';
    
    $last_three_digits = substr($integer_part, -3);
    $remaining_digits = substr($integer_part, 0, -3);

    if($remaining_digits != '') {
        $remaining_digits = ',' . $remaining_digits;
        $remaining_digits = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remaining_digits);
    }

    return $remaining_digits . $last_three_digits . $decimal_part;
}

public function checkdfwithpaymentterms(){
	$this->load->view('master/checkdfwithpaymentterms');
}


function dfgantchartDepartmentWise()
{
	//$this->output->cache(5);
$this->load->view('charts/DF_Gant_chartDepartmentWise');
}

function getDataforDepartmentTask()
{
	$df_id=$this->input->post('dfid');
	$department=$this->input->post('departmentid');
	$departName=$this->task->getdepartmentByID($department);
	$df_info=$this->task->getdfinfo($df_id);
	
	$html='';

		$html.='<ul id="tabs" class="nav nav-tabs">
                <li class="nav-item"><a href="" data-target="#home1" data-toggle="tab" class="nav-link small text-uppercase active">Task Details</a></li>
              
            </ul>
            <br>
            <div id="tabsContent" class="tab-content">
                <div id="home1" class="tab-pane active show fade">
                    	
				<table class="table table-bordered" style="width:100%">
				<thead>
				<tr>
				<th>Sr. No.</th>
				<th>Task Name</th>
				<th>Assigned To</th>
				<th>Current Status</th>
				<th>PLN<br/>Completion Date</th>
				<th>ACT<br/>Completion Date</th>
				<th>Delay</th>
				<th>Tickets<br/>Raised</th>
				</tr>
				</thead>
				<tbody>';

				$tasks=$this->task->getDFTaskScheduled($df_id,$department);
				if(count($tasks)>0)
				{
				$i=1;
				foreach($tasks as $row)
				{
					$df_tickets=$this->task->ticketsbydfandtask($df_id,$row['task_id']);
					$username=$this->task->getusername($row['assignedto']);
				$actualDone=$this->task->checkActualDoneStatus($row['task_id'],$df_id);
				if($actualDone<>'')
				{
				if(strtotime($actualDone)>strtotime($row['end_date']))
				{
				$delayDays=$this->task->getDateDiffInDays($actualDone,$row['end_date'])." Days";
				$dcolor="#ffb2ae";
				}else
				{
				$delayDays='0 Days';
				$dcolor="#BFF6C3";
				}
				$status="Done";
				$actualDone=date('d-M-Y',strtotime($actualDone));
				$color="";
				}else
				{
				$delayDays='';
				$status="Pending";
				$actualDone='';
				$color="#ffb2ae";

				if(strtotime(date('Y-m-d'))>strtotime($row['end_date']))
				{
				$delayDays=$this->task->getDateDiffInDays($actualDone,$row['end_date'])." Days";
				$dcolor="#ffb2ae";
				}else
				{
				$delayDays='0 Days';
				$dcolor="#BFF6C3";
				}


				}

				$tid=$row['task_id'];

				$html.='<tr>
				<td>'.$i.'</td>
				<td>'.ucwords(strtolower($row['task_name'])).'</td>
				<td>'.$username.'</td>
				<td>'.$status.'</td>
				<td>'.date('d-M-Y',strtotime($row['end_date'])).'</td>
				<td>'.$actualDone.'</td>
				<td style="background-color:'.$dcolor.'">'.$delayDays.'</td>
				<td><u><a href="javascript:;" onclick="showhelptickets('.$df_id.','.$department.','.$tid.');"><i>'.$df_tickets.'</i></a></u></td>
				</tr>';
				$i++;
				}
				}
				$html.='</tbody>
				</table>
               </div>';

		echo $html.'~'.ucwords(strtolower($departName)).'~'.$df_info;
}

	function gethelptickets()
	{

		$df_id=$this->input->post('dfid');
		$department=$this->input->post('departmentid');
		$taskid=$this->input->post('taskid');
		$departName=$this->task->getdepartmentByID($department);
		$df_info=$this->task->getdfinfo($df_id);

		$html='<table class="table table-bordered" style="width:100%">
				<thead>
				<tr>
				<th>Sr. No.</th>
				<th>Ticket No.</th>
				<th>Ticket</th>
				<th>Raised For</th>
				<th>Raised By</th>
				<th>Raised On</th>
				<th>Response Given</th>
				<th>Response Given On</th>
				<th>Status</th>
				<th>Closed On</th>
				<th>Time Taken</th>
				</tr>
				</thead>
				<tbody>';

				$ticket=$this->task->ticketsbydfandtaskDetail($df_id,$taskid);
				if(count($ticket)>0)
				{
					for($t=0;$t<count($ticket['help_ticket_no']);$t++)
					{

						$r=$t+1;
						if($ticket['ticket_status'][$t]==1)
						{
							$p="Resolved";
						}else
						{
							$p="Pending";
						}
				$html.='<tr>
				<td>'.$r.'</td>
				<td>'.$ticket['help_ticket_no'][$t].'</td>
				<td>'.$ticket['remarks'][$t].'</td>
				<td>'.$ticket['assignedTo'][$t].'</td>
				<td>'.$ticket['assignBy'][$t].'</td>
				<td>'.$ticket['added_on'][$t].'</td>
				
				<td>'.$ticket['updated_remarks'][$t].'</td>
				<td>'.$ticket['updated_on'][$t].'</td>
				<td>'.$p.'</td>
				<td>'.$ticket['ticket_closed_on'][$t].'</td>
				<td>'.$ticket['timeTaken'][$t].'</td>
			
				</tr>';
					}

				}

				$html.='</tbody>
				</table>
               </div>';

		echo $html;




	}

public function history($ticket_id) {
        $data = $this->Ticket_model->get_ticket_history($ticket_id);
        echo json_encode($data);
    }

function filterticket(){
	$userid = $this->uri->segment(3);
	$dfid = $this->uri->segment(4);

	redirect(page_url."Task/viewhelpticketdfwise/".$userid."/".$dfid);
}
public function viewhelpticketdfwise(){
	$this->load->view('master/viewhelpticketdfwise.php');
}

	public function viewhelpticketdfwiselist()
	{

		$user_id= $this->uri->segment(3);
		$department_id=$this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
		$i=1;
		$helpticketdata = array();
		$this->db->select('a.id, a.user_id, a.help_ticket_no, a.remarks,a.added_by, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no')->from('communication_ticket_system a')->join('departments b','a.department_id=b.department_id','left')->join('task_management c','a.task_id=c.task_id','left')->join('system_users d','a.added_by=d.user_id','left')->join('system_users e','a.updated_by=e.user_id','left')->join('df_release f','a.df_id=f.id','left')->where('a.ticket_status',0);
			$this->db->where('a.user_id',$user_id);
			if($this->uri->segment(4)<>'ALL'){
			$this->db->where('a.df_id',$this->uri->segment(4));
			}
		
		$query = $this->db->get();
		$res = $query->result();
		$i=1;
		foreach($res as $row){
			$closestatus  = "";
			$user_id =$this->session->userdata['logged_in']['user_id'];
			if($user_id==$row->added_by){
			$closestatus = '<a href="'.page_url.'Task/clicktoclose/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'" class="btn btn-xs btn-success">Click to Close</a>';
		}else{
			$closestatus = "Pending to Close";
		}
		$addcomment = '';
		$commenthistory = '';
		

		$addcomment = '<span class="btn btn-primary btn-xs" onclick="showcommentbox('.$row->id.');">Add Comment</span>';
			$q = $this->db->select('a.comment, a.added_on, b.title, b.first_name, b.last_name')->from('communication_chain a')->join('system_users b','a.added_by=b.user_id','left')->where('record_id',$row->id)->order_by('a.id','desc')->limit(1)->get();
			if($q->num_rows()>0){
				foreach($q->result() as $comrow);
				$commenthistory.=$comrow->comment."<br>Added By: ".ucwords(strtolower($comrow->title." ".$comrow->first_name." ".$comrow->last_name))."<br>Added On: ".date('d-m-Y',strtotime($comrow->added_on))."<br>".date('h:i A',strtotime($comrow->added_on));
				

				$commenthistory.='<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#ticketModal" onclick="loadTicketHistory('.$row->id.')">
  View Help Ticket Timeline
</button>';

			}

			$addedondate = date('Y-m-d',strtotime($row->added_on));
			$todaysdate = date('Y-m-d');
			$date1 = new DateTime($addedondate);
			$date2 = new DateTime($todaysdate);
			$interval = $date1->diff($date2);
			$delayCount = $interval->days;
			if($delayCount>1){
				$delaydays = "<span style='color:red; font-weight:bold;'>".$delayCount." Days</span>";
			}else{
				$delaydays = "";
			}

			$helpticketdata[] = array('sr_no'=>$i,
				'dfno'=>$row->df_no,
				'ticket_no'=>$row->help_ticket_no,
				'taskname'=>$row->task_name,
				'remarks'=>$row->remarks,
				'department'=>ucwords(strtolower($row->department)),
				'assignedto'=>ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)),
				'addedon'=>date('d-m-Y',strtotime($row->added_on))."<br> ".date('h:i A',strtotime($row->added_on)),
				'addedby'=>ucwords(strtolower($row->fname." ".$row->lname)),
				'closestatus'=>$closestatus,
				'comment'=>$commenthistory,
				'delay'=>$delaydays,
				'addcomment'=>$addcomment);

			$i++;

		}

			$results = array(

			"sEcho" => 1,

			"iTotalRecords" => count($helpticketdata),

			"iTotalDisplayRecords" => count($helpticketdata),

			"aaData"=>$helpticketdata);

			

		echo json_encode($results);

	}

	function GetTaskName()
	{
		$taskid=$this->input->post('taskid');

		$tname=substr($this->task->GetTaskName($taskid),0,1);
		echo $tname;
	}

	function brandmapping(){
		$recordid = $this->input->post('poid');
		$tagbrand = $this->input->post('tagbrand');

		if(!is_numeric($this->input->post('tagbrand'))){


		$resty=$this->db->select('id')->from('company_brand')->where('LOWER(name)',strtolower($this->input->post('tagbrand')))->get();
		if($resty->num_rows()==0)
		{
		$dr=array('name'=>$this->input->post('tagbrand'));
		$this->db->insert('company_brand',$dr);
		$brand_id=$this->db->insert_id();
		}else
		{
		foreach($resty->result() as $roww);
		$brand_id=$roww->id;
		}
		}else{
		$brand_id = $this->input->post('tagbrand');
		}



		$data = array('brand_tag'=>$brand_id);
		$this->db->where('id',$recordid);
		$this->db->update('poreceived',$data);
		$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Thank You! Brand successfully updated.</div>');
			redirect(page_url.'Task/receivedpolist/');

	}

	public function edit_df(){
		$this->load->view('master/edit_df');
	}

	function sharmajitaskmanagement(){
		$this->load->view('master/sharmajitask');

	}

	public function sharmajitaskmanagementlist()
	{
		$i=1;
		$taskdata= array();
		$this->db->select('a.*, b.department, c.task_name')->from('mdgantchartmaster a');
		$this->db->join('departments b','a.department_id=b.department_id','left');
		$this->db->join('task_management c','a.main_task_id=c.task_id','left');
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){
		$edit = "<a href='".page_url."Task/edit_sharmajitaskmanagement/".$row->id."'><i class='fa fa-pencil'></i></a>";	
			$m=1;	
			$message = "<table border='1' style='width:300px;'><tr style='background-color:#fbeeee; text-align:center;'><th style='padding:2px 2px 2px 2px; text-align:center;'>Sr No</th><th style='padding:2px 2px 2px 2px;  text-align:center;'>Task Name</th></tr>";
			$q = $this->db->select('b.task_name')->from('sharmajitaskmapping a')->join('task_management b','a.task_id=b.task_id','left')->where('a.report_id',$row->id)->get();
			foreach($q->result() as $row1){
				$message.='<tr>
					<td>'.$m.'</td>
					<td>'.$row1->task_name.'</td>
				</tr>';
				$m++;
			}
			$message.='</table><br><br>';

			$message.='<a href="'.page_url.'Task/taskmapping/'.$row->id.'" class="btn btn-xs btn-success">Map Task</a>';

			$taskdata[] = array('sr_no'=>$i,
			'department'=>$row->department,
			'task_name'=>$row->taskname,
			'taskmapping'=>$message,
			'maintask'=>$row->task_name,
            'edit'=>$edit);
			$i++;
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($taskdata),
			"iTotalDisplayRecords" => count($taskdata),
			"aaData"=>$taskdata);
			
		echo json_encode($results);
	}

	public function edit_sharmajitaskmanagement(){
		$this->load->view('master/edit_sharmajitask');
	}

	public function updatemdtask()
	{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('departmentid', 'Department', 'required|trim');
		$this->form_validation->set_rules('taskname', 'Task Name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_sharmajitask');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s');
		
		$table = "mdgantchartmaster";
			$data = array('department_id'=>$this->input->post('departmentid'),
			'taskname'=>$this->input->post('taskname'),
			'status'=>1);
			$this->db->where('id',$this->uri->segment(3));
			$result  = $this->db->update('mdgantchartmaster',$data);
		
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank You!,record successfully updated.</div><br/>');
			redirect(page_url.'Task/sharmajitaskmanagement');
	}
		
	}

	function taskmapping(){
		$this->load->view('master/sharmajitaskmapping');
	}

	public function addtaskmapping(){
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$table = "sharmajitaskmapping";
		$user_id =$this->session->userdata['logged_in']['user_id'];
		if(isset($_REQUEST['my_multi_select1'])){	
					$tags1=count($_REQUEST['my_multi_select1']);
					if($tags1>0)
					{
					$taskid=$_REQUEST['my_multi_select1'];
					for($x=0;$x<$tags1;$x++){
					if($taskid[$x]!='')
						{
							$data=array('report_id'=>$this->uri->segment(3),
							'task_id'=>$taskid[$x],
							'added_on'=>$date,
							'added_by'=>$user_id);
							$query = $this->db->select('id')->from('sharmajitaskmapping')->where('report_id',$this->uri->segment(3))->where('task_id',$taskid[$x])->get();
							$res = $query->result();
							if($res){

							}else{
							$this->db->insert($table,$data);
							}
						}
					}
					}
					}
		$data1 = array('main_task_id'=>$this->input->post('masterid'));
		$this->db->where('id',$this->uri->segment(3));
		$this->db->update('mdgantchartmaster',$data1);
		
			$this->session->set_flashdata('message','<div class="alert alert-success">Thank you, record successfully added.</div>');
			redirect(page_url.'Task/sharmajitaskmanagement');
		
	}

	public function finalgantchart(){

		$this->load->view('charts/finalgantchart');
	}



public function getreportoftasks(){
    $dfid = $this->input->post('dfid');
    $taskid = $this->input->post('taskid');
    $departmentid = $this->input->post('departmentid');

    $html = '<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">    
                <thead>
                    <tr>
                        <th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Sr No</th>
                        <th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Task</th>
                        <th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Responsible Person</th>
                        <th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Start Date</th>
                        <th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">End Date</th>
                         
                        <th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Actual Completion Date</th>
                        <th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Remarks</th>
                        <th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Delay</th>
                    </tr>
                </thead>
                <tbody>';
    $data = array();
    $m11 = 1;

    $q = $this->db->select('b.task_name, b.task_id')
        ->from('sharmajitaskmapping a')
        ->join('task_management b', 'a.task_id=b.task_id', 'left')
        ->where('a.report_id', $taskid)
        ->get();

    foreach ($q->result() as $recod) {
        $q9 = $this->db->select('a. start_date, a.end_date, a.task_completed_on, a.remarks, b.title, b.first_name,b.last_name')
            ->from('task_department_wise_scheduling a')
            ->join('system_users b','a.assigned_user=b.user_id','left')
            ->where('a.df_id', $dfid)
            ->where('a.taskid', $recod->task_id)
            ->get();
        
        foreach ($q9->result() as $recoractualinfo);
        $enddatess = date('Y-m-d', strtotime($recoractualinfo->end_date));
        $currentDate = date('Y-m-d');

        if ($recoractualinfo->task_completed_on !== '0000-00-00 00:00:00') {
            $actenddatess = date('Y-m-d', strtotime($recoractualinfo->task_completed_on));
            $printcompletiondate = date('d-M-Y', strtotime($recoractualinfo->task_completed_on));
            $ddayss = $this->task->calculateDelayInDays($enddatess, $actenddatess);
            if ($ddayss > 0) {
                $delaydays = "<span style='color:red; font-weight:bold;'>".$ddayss." Days</span>";
            } else {
                $delaydays = "<span style='color:green; font-weight:bold;'>0 Days</span>";
            }
        } else {
            $printcompletiondate = '<span style="color:red; font-weight:bold">Pending to Start</span>';
            if ($currentDate > $enddatess) {
                $ddayss = $this->task->calculateDelayInDays($enddatess, $currentDate);
                $delaydays = "<span style='color:red; font-weight:bold;'>".$ddayss." Days Overdue</span>";
            } else {
                $delaydays = "<span style='color:green; font-weight:bold;'>No Delay</span>";
            }
        }

        $responsibleperson = ucwords(strtolower($recoractualinfo->title." ".$recoractualinfo->first_name." ".$recoractualinfo->last_name));
        
        $data[] = array(
            'task_name' => ucwords(strtolower($recod->task_name)),
            'start_date' => date('Y-m-d', strtotime($recoractualinfo->start_date)),
            'end_date' => date('Y-m-d', strtotime($recoractualinfo->end_date)),
            'completionDate' => $printcompletiondate,
            'responsibleperson'=>$responsibleperson,
            'remarks' => ucwords(strtolower($recoractualinfo->remarks)),
            'delaydays' => $delaydays
        );
        $m11++;
    }

    if (count($data) > 0) {
        usort($data, function($a, $b) {
            $date1 = strtotime($a['start_date']);
            $date2 = strtotime($b['start_date']);
            return $date1 - $date2;
        });

        $t = 1;
        foreach ($data as $task) {
            $html .= '<tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size:12px;">'.$t.'</td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size:12px;">'.$task['task_name'].'</td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size:12px;">'.$task['responsibleperson'].'</td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size:12px;">'.date('d-M-Y', strtotime($task['start_date'])).'</td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size:12px;">'.date('d-M-Y', strtotime($task['end_date'])).'</td>

                        <td style="padding: 10px; border: 1px solid #ddd; font-size:12px;">'.$task['completionDate'].'</td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size:12px;">'.$task['remarks'].'</td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size:12px;">'.$task['delaydays'].'</td>
                    </tr>';
            $t++;
        }
    }

    $html .= '</tbody>
            </table>';

    echo $html;
    exit;
}



	function updateTaskDone()
	{
		$restu=$this->db->select('id,start_date,task_completed_on')->from('task_department_wise_scheduling')->where('task_status',1)->get();
		if($restu->num_rows()>0)
		{
			foreach($restu->result() as $row)
			{
				$start_date=$row->start_date;
				$task_completed_on=date('Y-m-d',strtotime($row->task_completed_on));

				if(strtotime($task_completed_on)<strtotime($start_date))
				{
					
					$data=array('task_completed_on'=>$start_date." 11:03:00");
					$this->db->where('id',$row->id);
					$this->db->update('task_department_wise_scheduling',$data);
				}
			}
		}
	}

	function editpo(){
		$this->load->view('master/edit_po');
	}

	public function updatepoinfo()
		{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('companyname', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('pono', 'Po Number', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_po');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 

		$photo=$_FILES['attachpo']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$poattachment=time().'.'.$cat_image;
				move_uploaded_file($_FILES['attachpo']["tmp_name"],UPLOADPATH.'Taskdocument/' . $poattachment);
			}else
			{
				$poattachment=$this->input->post('oldpo');
				}


		
			$data = array('company_name'=>$this->input->post('companyname'),
			'pono'=>$this->input->post('pono'),
			'financialyear'=>$this->input->post('financialyear'),
			'amount_in_customer_currency'=>$this->input->post('ordervalueincustomercurrency'),
			'order_value'=>$this->input->post('order_value'),
			'po_attachment'=>$poattachment,
			'po_updated_by'=>$user_id,
			'po_updated_on'=>date('Y-m-d H:i:s'));
			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('poreceived',$data);
			
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Thank You!, Record successfully updated.</div>');
			redirect(page_url.'Task/poreceived');
		}
		
	}

	function manualsorting()
	{
		$this->task->get_sorted_tasks();
	}

	function changeTaskCompleteDate()
	{
		$user_id = isset($_SESSION['logged_in']['user_id']) ? (int) $_SESSION['logged_in']['user_id'] : 0;
		$response = array(
			'success' => false,
			'message' => 'You are not allowed to update completed task dates.'
		);

		if (!in_array($user_id, array(61, 161), true)) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
		}

		$id = (int) $this->uri->segment(3);
		$closedDate = trim((string) $this->uri->segment(4));

		if ($id <= 0 || $closedDate === '') {
			$response['message'] = 'Task record or completion date is missing.';
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode($response));
		}

		$this->load->model('Task_completion_control_model', 'task_completion_control');
		$response = $this->task_completion_control->update_completion_date_quickly($id, $user_id, $closedDate);

		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode($response));
	}

public function markNotificationAsRead()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $notification_id = $this->input->post('notification_id');

    $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    $this->output->set_header('Pragma: no-cache');
    $this->output->set_header('Expires: 0');

    if (!empty($notification_id)) {
        $this->db->where('id', $notification_id);
        $this->db->where('user_id', $user_id);
        $this->db->update('notifications', ['is_read' => 1]);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'message' => 'Notification marked as read.']));
    } else {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid notification ID.']));
    }
}


public function fetchNotifications()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];
    $encoded_user_id = base64_encode((string) $user_id);

    $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    $this->output->set_header('Pragma: no-cache');
    $this->output->set_header('Expires: 0');

    $this->db->select('
        n.id,
        n.ticket_id,
        n.message,
        n.created_at,
        n.df_no,
        c.df_id,
        c.task_record_id,
        c.help_ticket_no,
        t.task_name
    ');
    $this->db->from('notifications n');
    $this->db->join('communication_ticket_system c', 'c.id = n.ticket_id', 'left');
    $this->db->join('task_management t', 't.task_id = c.task_id', 'left');
    $this->db->where('n.user_id', $user_id);
    $this->db->where('n.is_read', 0);
    $this->db->order_by('n.created_at', 'DESC');
    $query = $this->db->get();

    $notifications = array();

    foreach ($query->result() as $row) {
        $message = (string) $row->message;
        $task_name = trim((string) $row->task_name);
        $df_no = trim((string) $row->df_no);

        if ($df_no !== '' && stripos($message, 'DF:') === false) {
            $message .= '<br>DF: ' . htmlspecialchars($df_no, ENT_QUOTES, 'UTF-8');
        }

        if ($task_name !== '' && stripos($message, 'Task:') === false) {
            $message .= '<br>Task: ' . htmlspecialchars($task_name, ENT_QUOTES, 'UTF-8');
        }

        $redirect_url = page_url . 'Task/helpticketsforyou/' . $encoded_user_id;

        if ((int) $row->df_id > 0) {
            $redirect_url .= '/' . (int) $row->df_id;

            if ((int) $row->task_record_id > 0) {
                $redirect_url .= '/' . (int) $row->task_record_id;
            }
        }

        $notifications[] = array(
            'id' => (int) $row->id,
            'ticket_id' => (int) $row->ticket_id,
            'message' => $message,
            'created_at' => $row->created_at,
            'df_no' => $df_no,
            'task_name' => $task_name,
            'help_ticket_no' => (string) $row->help_ticket_no,
            'redirect_url' => $redirect_url
        );
    }

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($notifications));
}



public function get_delay_data_by_df() {
    //$df_name = $this->input->post('df_name'); // Capture DF selection from AJAX request
    $df_name = 1; // Capture DF selection from AJAX request
    $query = $this->db->query("
        SELECT department, MAX(DATEDIFF(actual_completion_date, expected_completion_date)) AS max_delay
        FROM df_completion_table
        WHERE df_name = ?
        GROUP BY department
        ORDER BY max_delay DESC
    ", array($df_name));
    echo json_encode($query->result());
}


public function dfdelayreportbydepartment(){
	$this->load->view('master/delay_report_graph');
}


public function checkUnresolvedTickets() {
    $userId = $this->session->userdata('user_id');
    $this->load->model('Ticket_model');
    $unresolvedTickets = $this->Ticket_model->getUnresolvedTickets($userId);

    if (!empty($unresolvedTickets)) {
        echo json_encode(['status' => true, 'message' => 'You have unresolved help tickets that are overdue.']);
    } else {
        echo json_encode(['status' => false]);
    }
}

public function fetchUnresolvedTickets() {
    $user_id = $this->session->userdata['logged_in']['user_id'];    

    $this->load->model('Ticket_model');
    $unresolvedTickets = $this->Ticket_model->getUnresolvedTickets($user_id);
    $overdueTasks = $this->Ticket_model->getOverdueTasks($user_id);

    if (!empty($unresolvedTickets) || !empty($overdueTasks)) {
        echo json_encode([
            'status' => true,
            'tickets' => $unresolvedTickets,
            'overdue_tasks' => $overdueTasks
        ]);
    } else {
        echo json_encode(['status' => false, 'message' => 'No unresolved tickets or overdue tasks.']);
    }
}



public function closePopup() {
    // Get JSON payload
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Get the user ID from session
    $userId = $this->session->userdata['logged_in']['user_id']; 

    // Fetch user email from system_users table
    $userQuery = $this->db->select('email')->from('system_users')->where('user_id', $userId)->get();
    $userEmail = $userQuery->row('email') ?? null;

    // Fetch team leader email from prestogroup_teams table
    $leaderEmail = null;
    $teamQuery = $this->db->select('team_id')->from('presto_team_members')->where('employee_id', $userId)->get();
    if ($teamQuery->num_rows() > 0) {
        $teamId = $teamQuery->row('team_id');
        $leaderQuery = $this->db->select('b.email')
            ->from('prestogroup_teams a')
            ->join('system_users b', 'a.team_leader = b.user_id', 'left')
            ->where('a.team_id', $teamId)
            ->get();
        $leaderEmail = $leaderQuery->row('email') ?? null;
    }

    // Insert log into "popup_logs" table
    $logData = [
        'user_id' => $userId,
        'closed_at' => date('Y-m-d H:i:s'),
        'closedate' => date('Y-m-d')
    ];
    $this->db->insert('popup_logs', $logData);

    // Generate HTML for Tickets Table
    $emailContent = '<h3>The following tickets were acknowledged:</h3>';
    $emailContent .= '<table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
    $emailContent .= '<thead>
        <tr>
            <th>#</th>
            <th>Ticket No</th>
            <th>DF No</th>
            <th>Task Name</th>
            <th>Remarks</th>
            <th>Added On</th>
            <th>Assigned To</th>
        </tr>
    </thead>';
    $emailContent .= '<tbody>';
    
    foreach ($input['tickets'] as $index => $ticket) {
        $emailContent .= '<tr>
            <td>' . ($index + 1) . '</td>
            <td>' . htmlspecialchars(strtoupper($ticket['help_ticket_no'])) . '</td>
            <td>' . htmlspecialchars(strtoupper($ticket['df_no'])) . '</td>
            <td>' . htmlspecialchars(strtoupper($ticket['task_name'])) . '</td>
            <td>' . htmlspecialchars(strtoupper($ticket['remarks'])) . '</td>
            <td>' . htmlspecialchars($ticket['added_on']) . '</td>
            <td>' . htmlspecialchars(strtoupper($ticket['title'] . ' ' . $ticket['first_name'] . ' ' . $ticket['last_name'])) . '</td>
        </tr>';
    }
    $emailContent .= '</tbody></table>';

    // **New Section: Generate Task Table**
    $emailContent .= '<h3>The following tasks were acknowledged:</h3>';
    $emailContent .= '<table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
    $emailContent .= '<thead>
        <tr>
            <th>#</th>
            <th>Task ID</th>
            <th>DF No</th>
            <th>Task Name</th>
            <th>Due Date</th>
            <th>Status</th>
           
        </tr>
    </thead>';
    $emailContent .= '<tbody>';

    // Fetch Task Data (Modify the query based on your logic)
    $taskQuery = $this->db->select('a.end_date, b.df_no, c.task_name')
        ->from('task_department_wise_scheduling a')
        ->join('df_release b','a.df_id=b.id','left')
        ->join('task_management c','a.taskid=c.task_id','left')
        ->where('a.task_status', 0)
        ->where('a.end_date<',date('Y-m-d'))
        ->where('a.assigned_user', $userId) // Get tasks assigned to the user
        ->get();



    if ($taskQuery->num_rows() > 0) {
        $tasks = $taskQuery->result_array();
        foreach ($tasks as $index => $task) {
            $statusText = ($task['task_status'] == 1) ? 'Approved' : 'Pending';
            $emailContent .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . htmlspecialchars(strtoupper($task['id'])) . '</td>
                <td>' . htmlspecialchars(strtoupper($task['df_no'])) . '</td>
                <td>' . htmlspecialchars(strtoupper($task['task_name'])) . '</td>
                <td>' . htmlspecialchars(date('d-m-Y', strtotime($task['end_date']))) . '</td>
                <td>' . htmlspecialchars($statusText) . '</td>
            </tr>';
        }
    } else {
        $emailContent .= '<tr><td colspan="6" style="text-align: center;">No tasks found</td></tr>';
    }

    $emailContent .= '</tbody></table>';

    // Send Email Notification
    
    $this->email->from('taskmanagement@shubhampack.com', 'Help Ticket Notification Acceptance by User - PMS Shubham Pack');
    $this->email->to($userEmail ?: 'groupceo@shubhampack.com');
    $this->email->bcc('mangleshup@gmail.com');

    // Add team leader email as CC
    if (!empty($leaderEmail)) {
        $this->email->cc($leaderEmail, 'groupceo@shubhampack.com');
    }

    $this->email->subject('Help Tickets and Task Pending More Than 2 Days to Answer.');
    $this->email->message($emailContent);

    if ($this->email->send()) {
        echo json_encode(['status' => true, 'message' => 'Popup closed and email sent.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to send email.']);
    }
}




public function getPopupStatus()
{
    $user_id = $this->input->post('user_id');
    $today = date('Y-m-d');

    $this->db->where('user_id', $user_id);
    $this->db->where('closedate', $today);
    $query = $this->db->get('popup_logs');

    if ($query->num_rows() > 0) {
        echo json_encode(['status' => true, 'show_popup' => false]);
    } else {
        echo json_encode(['status' => true, 'show_popup' => true]);
    }
}

public function closePopupForUser()
{
    $user_id = $this->input->post('user_id');
    $today = date('Y-m-d');

    $data = [
        'user_id' => $user_id,
        'closedate' => $today
    ];

    $this->db->insert('popup_close_logs', $data);

    echo json_encode(['status' => true, 'message' => 'Popup closure recorded for the user.']);
}


function markasholddf(){
	$user_id =$this->session->userdata['logged_in']['user_id'];	
    $id = $this->uri->segment(3);
    $data = array('on_hold' => 1,'mark_as_hold_by'=>$user_id,'hold_mark_on'=>date('Y-m-d H:i:s'));
    $this->db->where('id', $id);
    $this->db->update('df_release', $data);


    $data32 = array('on_hold'=>1);
   	$this->db->where('df_id',$id);
   	$this->db->update('task_department_wise_scheduling',$data32);



    // Email notification setup
    $this->load->library('email');

    $q = $this->db->select('a.df_no, b.title, b.first_name, b.last_name, b.email, b.contact_number')
        ->from('df_release a')
        ->join('system_users b', 'a.added_by=b.user_id', 'left')
        ->where('a.id', $id)
        ->get();

    $ros = $q->row(); // Fetch a single row
    $username = ucwords(strtolower($ros->title . " " . $ros->first_name . " " . $ros->last_name));
    $dfno = $ros->df_no;

    // Get all user emails for CC
    $users = $this->db->select('email')->from('system_users')->get()->result();
    $cc_emails = array_column($users, 'email');

    // Define email parameters
    $to_email = 'groupceo@shubhampack.com'; // Primary recipient
    $subject = "Important Notification: DF {$dfno} Marked as On Hold";
    $message = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f9f9f9;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: auto;
                padding: 20px;
                background: #ffffff;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header img {
                max-width: 150px;
            }
            .content {
                font-size: 14px;
                margin-bottom: 20px;
            }
            .footer {
                font-size: 12px;
                color: #555;
                margin-top: 20px;
                text-align: center;
            }
            .footer a {
                color: #4872b8;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='https://pms.shubhampack.in/assets/images/shubhampack.png' alt='Shubham Pack Logo'>
            </div>
            <div class='content'>
                <p>Dear Team,</p>
                <p>This is to inform you that the DF No <strong>{$dfno}</strong> has been marked as <strong>On Hold</strong> effective immediately.</p>
                <p><strong>Action Required:</strong> Please stop all purchasing activities related to this DF until further notice.</p>
                <p>If you have any questions or require clarification, please reach out to the management team.</p>
            </div>
            <div class='footer'>
                Regards,<br>
                {$username}<br>
                <strong>Shubham Pack</strong><br>
                Email: <a href='mailto:{$ros->email}'>{$ros->email}</a><br>
                Phone: {$ros->contact_number}
            </div>
        </div>
    </body>
    </html>";

    // Set email headers
    $this->email->clear();
    //$this->email->to('mangleshup@gmail.com');
    //$this->email->cc('manglesh@gamavis.com');
    $this->email->to($to_email); // Primary recipient
    $this->email->cc($cc_emails); // All other users in CC
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Pack Management');
    $this->email->subject($subject);
    $this->email->message($message);

    // Send email
    if (!$this->email->send()) {
        log_message('error', 'Email sending failed: ' . $this->email->print_debugger());
    }

    // Set flash message

    // Set Growl notification in session for users
    $notification_message = "DF No {$dfno} has been marked as On Hold. Please review it and stop purchasing activities immediately.";
    $this->session->set_userdata('growl_notification', $notification_message);


    $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Thank You! DF successfully marked as on hold and notifications have been sent to the respective departments.</div>');

    // Redirect to dashboard
    redirect(page_url . 'Task/dfreleasedashboard/');
}


public function markasunhold() {
    $id = $this->uri->segment(3);

    // Update the DF status to unhold
    $data = array('on_hold' => 0);
    $this->db->where('id', $id);
    $this->db->update('df_release', $data);

   	$data32 = array('on_hold'=>0);
   	$this->db->where('df_id',$id);
   	$this->db->update('task_department_wise_scheduling',$data32);

    $user_id = $this->session->userdata['logged_in']['user_id'];
    $audit_update = $this->getTaskSchedulingAuditColumns($user_id);
    date_default_timezone_set("Asia/Kolkata");
    $today = date('Y-m-d');

    // Fetch the last completed task for the DF based on system_created_sort_order
    $last_completed_task = $this->db->select('t.taskid, t.end_date')
        ->from('task_department_wise_scheduling t')
        ->join('task_management tm', 't.taskid = tm.task_id', 'left')
        ->where('t.df_id', $id)
        ->where('t.task_status', 1) // Completed tasks
        ->order_by('tm.system_created_sort_order', 'desc')
        ->limit(1)
        ->get()
        ->row();

    // Update the last completed task to mark it as done today
    if ($last_completed_task) {
        $last_completed_task_update = array(
            'task_completed_on' => $today,
            'task_status' => 1
        );

        if (!empty($audit_update)) {
            $last_completed_task_update = array_merge($last_completed_task_update, $audit_update);
        }

        $this->db->where('taskid', $last_completed_task->taskid)
            ->where('df_id', $id)
            ->update('task_department_wise_scheduling', $last_completed_task_update);

        $start_date = date('Y-m-d', strtotime('+1 day')); // Schedule from tomorrow
    } else {
        // If no tasks have been completed, start scheduling from today
        $start_date = $today;
    }

    // Fetch all pending tasks in order
    $pending_tasks = $this->db->select('tm.task_id, tm.tat, tm.system_created_sort_order, tm.tat_start_from')
        ->from('task_management tm')
        ->join('task_department_wise_scheduling t', 'tm.task_id = t.taskid AND t.df_id = ' . $this->db->escape($id))
        ->where('t.task_status', 0) // Pending tasks
        ->order_by('tm.system_created_sort_order', 'asc')
        ->get()
        ->result();

    foreach ($pending_tasks as $task) {
        // Check if the task has a dependent task
        $dependent_end_date = null;

        if ($task->tat_start_from) {
            $q1 = $this->db->select('end_date')
                ->from('task_department_wise_scheduling')
                ->where('df_id', $id)
                ->where('taskid', $task->tat_start_from)
                ->get();

            if ($q1->num_rows() > 0) {
                $dependent_end_date = $q1->row()->end_date;
                $start_date = date('Y-m-d', strtotime('+1 day', strtotime($dependent_end_date)));
            }
        }

        $reference_start_date = !empty($dependent_end_date)
            ? date('Y-m-d', strtotime('+1 day', strtotime($dependent_end_date)))
            : $start_date;
        $scheduled_dates = $this->buildDfTaskScheduleWindow($task->task_id, $reference_start_date, $task->tat, true);

        $start_date = $scheduled_dates['start_date'];
        $end_date = $scheduled_dates['end_date'];

        // Update task scheduling
        $pending_task_update = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'task_status' => 0, // Ensure it's marked as pending
            'mark_hold' => 1
        );

        if (!empty($audit_update)) {
            $pending_task_update = array_merge($pending_task_update, $audit_update);
        }

        $this->db->where('taskid', $task->task_id)
            ->where('df_id', $id)
            ->update('task_department_wise_scheduling', $pending_task_update);

        // Prepare start_date for the next task
        $start_date = date('Y-m-d', strtotime('+1 day', strtotime($end_date)));
    }

    // Notify users via email
    $q = $this->db->select('a.df_no, b.title, b.first_name, b.last_name, b.email, b.contact_number')
        ->from('df_release a')
        ->join('system_users b', 'a.added_by = b.user_id', 'left')
        ->where('a.id', $id)
        ->get();

    $ros = $q->row();
    $username = ucwords(strtolower($ros->title . " " . $ros->first_name . " " . $ros->last_name));
    $dfno = $ros->df_no;

    $to_email = 'groupceo@shubhampack.com';
    $subject = "Notification: DF {$dfno} Marked as Unhold";
    $message = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
            }
            .container {
                max-width: 600px;
                margin: auto;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 5px;
                background: #f9f9f9;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header img {
                max-width: 150px;
            }
            .content {
                font-size: 14px;
                margin-bottom: 20px;
            }
            .footer {
                font-size: 12px;
                color: #555;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='https://pms.shubhampack.in/assets/images/shubhampack.png' alt='Shubham Pack Logo'>
            </div>
            <div class='content'>
                <p>Dear Team,</p>
                <p>The DF No <strong>{$dfno}</strong> has been marked as <strong>Unhold</strong> effective immediately.</p>
                <p><strong>Action Required:</strong> The pending tasks have been rescheduled considering holidays. Please log in to your <a href='https://pms.shubhampack.in'>PMS</a> account to check the updated schedule and ensure timely completion.</p>
            </div>
            <div class='footer'>
                <p>Regards,<br>{$username}<br>Shubham Pack</p>
            </div>
        </div>
    </body>
    </html>";

    $this->email->to($to_email);
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Pack Management');
    $this->email->subject($subject);
    $this->email->message($message);
    $this->email->send();

    // Set flash message
    $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">DF marked as unhold. Tasks have been rescheduled, and notifications sent.</div>');
    redirect(page_url . 'Task/dfreleasedashboard/');
}


		public function completeddfpendingforapproval()
	{
	    $previous_db_debug = $this->db->db_debug;
	    $this->db->db_debug = false;
	    $scope = $this->getTaskDashboardScope();
	    $department_id = !empty($scope['can_view_team_tasks']) ? $scope['view_department_ids'] : array();
	    $current_user_id = (int) $scope['user_id'];
	    $admin_user_type = (int) $scope['admin_user_type'];
	    $personal_visible_user_ids = isset($scope['personal_visible_user_ids']) ? $scope['personal_visible_user_ids'] : array($current_user_id);


    $i = 1;
    $dfdata = array();
    $this->db->select('a.id, f.first_name, f.last_name, a.task_completed_on, b.task_name, c.df_no, c.df_upload, c.added_on, d.first_name AS completed_first_name, d.last_name AS completed_last_name, e.department, a.end_date, c.df_description, a.remarks')
        ->from('task_department_wise_scheduling a')
        ->join('task_management b', 'a.taskid = b.task_id', 'left')
        ->join('df_release c', 'a.df_id = c.id', 'left')
        ->join('system_users d', 'a.task_completed_by = d.user_id', 'left')
        ->join('departments e', 'a.department_id = e.department_id', 'left')
        ->join('system_users f', 'a.assigned_user = f.user_id', 'left')
        ->where('a.task_status', 2);

	    if (count($department_id) > 0) {
	    	$this->db->group_start();
	        $this->db->where_in('a.department_id', $department_id, 'false');
	        $this->applyTaskAssignedUserOrFilter('a.assigned_user', $personal_visible_user_ids);
	        $this->db->group_end();
	    }
	    if (!empty($scope['personal_task_only'])) {
	        $this->applyTaskAssignedUserFilter('a.assigned_user', $personal_visible_user_ids);
	    }

    $departmentids = $this->uri->segment(3);
    $userids = $this->uri->segment(4);
    $dfnos = $this->uri->segment(5);

    if ($dfnos != '' && $dfnos != 'ALL') {
        $this->db->where('a.df_id', $dfnos);
    }
    if ($departmentids != '' && $departmentids != 'ALL') {
        $this->db->where('a.department_id', $departmentids);
    }
    if ($userids != '' && $userids != 'ALL') {
        $this->db->where('a.assigned_user', $userids);
    }

    $this->db->order_by('a.task_completed_on', 'desc');
	    $query = $this->db->get();
	    if ($query === false) {
	        log_message('error', 'completeddfpendingforapproval query failed: ' . json_encode($this->db->error()));
	        $res = array();
	    } else {
	        $res = $query->result();
	    }
	    //echo "<pre>"; print_r($res); exit;

    foreach ($res as $row) {
        // Generate download link for DF attachment
        $dfupload = '<a href="' . sfdocument . 'Taskdocument/dfattachment/' . $row->df_upload . '" download><span class=""><u>DOWNLOAD</u></span></a>';

        // Calculate delay or early completion
        if (strtotime($row->end_date) < strtotime($row->task_completed_on)) {
            $pendinddays = $this->task->getDays($row->end_date, date('Y-m-d', strtotime($row->task_completed_on)), 2);
            $status = "Delayed";
            // Set delay count in red color if delayed
            $task_delay_display = "<strong style='color:red;font-weight:bold;'>{$pendinddays} Days</strong>";
        } else {
            $pendinddays = $this->task->getDays($row->end_date, date('Y-m-d', strtotime($row->task_completed_on)), 1);
            $status = ($pendinddays > 0) ? "Early" : "On Time";
            // Set delay count in black color if on time or early
            $task_delay_display = "<strong style='color:black;font-weight:bold;'>{$pendinddays} Days</strong>";
        }

        // Allow changing end date if user ID is 61 or 161
	        if ($current_user_id == 61 || $current_user_id == 161) {
	            $end_dateChange = "<div class='col-md-12'><input type='date' name='doneadate" . $row->id . "' id='donedate" . $row->id . "' class='form-control' onchange='changeDoneDate(" . $row->id . ");'></div>";
	        } else {
	            $end_dateChange = '';
	        }

	        if($current_user_id == 162 || $current_user_id == 139){
	        	$approve = '<a href="'.page_url.'Task/markaspendingtoapprove/'.$row->id.'" class="btn btn-success btn-xs">Mark as Approve</a><br>';
	        	$reject = '<a href="'.page_url.'Task/markaspendingtoreject/'.$row->id.'" class="btn btn-warning btn-xs">Mark as Still Pending</a>';
	        }else{
	        	$approve = '';
	        	$reject = '';
	        }

	        if (!empty($scope['can_view_team_tasks']) || $current_user_id == 139) {
				$approve = '<a href="'.page_url.'Task/markaspendingtoapprove/'.$row->id.'" class="btn btn-success btn-xs">Mark as Approve</a><br>';
	        	$reject = '<a href="'.page_url.'Task/markaspendingtoreject/'.$row->id.'" class="btn btn-warning btn-xs">Mark as Still Pending</a>';
	        }else{
	        	$approve = '';
	        	$reject = '';
        }


        $completedon = date('Y-m-d H:i A', strtotime($row->task_completed_on));
        $dfdata[] = array(
            'sr_no' => $i,
            'df_no' => strtoupper($row->df_no . "<br>" . $row->df_description),
            'dfupload' => $dfupload,
            'department' => strtoupper($row->department),
            'membername' => strtoupper($row->first_name . " " . $row->last_name),
            'df_release_date' => date('d-m-Y', strtotime($row->added_on)),
            'taskname' => strtoupper($row->task_name),
            'tasktat' => date('d-m-Y', strtotime($row->end_date)),
            'taskdelay' => $task_delay_display,
            'status' => $status,
            'approveorreject'=>$approve."<br>".$reject,
            'remarks' => strtoupper($row->remarks),
            'addedon' => date('d-m-Y', strtotime($row->task_completed_on)) . "<br/><br/>" . $end_dateChange,
            'addedby' => strtoupper($row->completed_first_name . " " . $row->completed_last_name)
        );
        $i++;
    }

	    $results = array(
	        "sEcho" => 1,
	        "iTotalRecords" => count($dfdata),
	        "iTotalDisplayRecords" => count($dfdata),
	        "aaData" => $dfdata
	    );

	    $this->db->db_debug = $previous_db_debug;

	    $json_flags = 0;
	    if (defined('JSON_UNESCAPED_UNICODE')) {
	        $json_flags |= JSON_UNESCAPED_UNICODE;
	    }
	    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
	        $json_flags |= JSON_INVALID_UTF8_SUBSTITUTE;
	    }
	    if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
	        $json_flags |= JSON_PARTIAL_OUTPUT_ON_ERROR;
	    }

	    $json_output = json_encode($results, $json_flags);
	    if ($json_output === false) {
	        log_message('error', 'completeddfpendingforapproval json_encode failed: ' . json_last_error_msg());
	        $json_output = '{"sEcho":1,"iTotalRecords":0,"iTotalDisplayRecords":0,"aaData":[]}';
	    }

	    while (ob_get_level() > 0) {
	        @ob_end_clean();
	    }

	    $this->output
	        ->set_content_type('application/json')
	        ->set_output($json_output);
	    return;
	}


public function markaspendingtoapprove()
{
	$user_id =$this->session->userdata['logged_in']['user_id'];	
    // Get record ID from URL segment
    $recordid = $this->uri->segment(3);

    // Fetch user details and task details for the record
    $q = $this->db->select('a.id, c.task_name, d.df_no, b.user_id, b.first_name, b.title, b.last_name, b.email')
                  ->from('task_department_wise_scheduling a')
                  ->join('task_management c','a.taskid=c.task_id','left')
                  ->join('df_release d','a.df_id=d.id','left')
                  ->join('system_users b', 'a.assigned_user = b.user_id', 'left')
                  ->where('a.id', $recordid)
                  ->get();

    if ($q->num_rows() > 0) {
        $row = $q->row(); // Get the first row of results

        // Update task status to 1 (Approved)
        $data = array('task_status' => 1,
        	'checked_by_sir'=>$user_id,
        	'checked_on'=>date('Y-m-d H:i:s'));
        $this->db->where('id', $recordid);
        $res = $this->db->update('task_department_wise_scheduling', $data);

        if ($res) {
        	$username = $row->title." ".$row->first_name." ".$row->last_name;
            // Send email notification to user
            $this->sendTaskApprovalEmail($row->email, $username, $row->task_name, $row->df_no);
            log_message('info', 'Task marked as approved and email sent to ' . $row->email);
        } else {
            log_message('error', 'Failed to update task status for task ID: ' . $recordid);
        }
    } else {
        log_message('error', 'Task or user not found for task ID: ' . $recordid);
    }

    $this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable" style="color:black; font-size:18px;">Thank you, Task marked as approved.</div>');
	redirect(page_url."Dashboard");
}

/**
 * Function to send email notification to the user when the task is approved
 * 
 * @param string $email User's email
 * @param string $name User's name
 * @param string $task_name Name of the task that was approved
 * @param string $df_no DF Number of the task
 */
private function sendTaskApprovalEmail($email, $name, $task_name, $df_no)
{

	$user_id = $this->session->userdata['logged_in']['user_id'];
	$q= $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
	foreach($q->result() as $doneby);
	$markedby = ucwords(strtolower($doneby->title." ".$doneby->first_name." ".$doneby->last_name));
    // Set email sender, recipient, and subject
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging - Task Approved Notification');
    $this->email->to($email);
    $this->email->cc('mangleshup@gmail.com');
    $this->email->subject('Task Approval Notification - ' . $task_name);

    // Email message
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task Approval Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
        <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="background-color: #4872b8; padding: 20px; text-align: center;">
                <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Company Logo" style="max-width: 150px;">
            </div>
            
            <div style="padding: 20px;">
                <h2 style="color: #4872b8; text-align: center;">Task Approved!</h2>

                <p style="font-size: 16px; line-height: 1.6;">Dear ' . ucwords(strtolower($name)) . ',</p>

                <p style="font-size: 16px; line-height: 1.6;">
                    Congratulations! Your task titled <strong>' . ucwords(strtolower($task_name)) . '</strong> with DF No. <strong>' . $df_no . '</strong> has been approved by <strong>'.$markedby.'</strong>.
                </p>

                <p style="font-size: 16px; line-height: 1.6;">
                    We appreciate your hard work and dedication to complete this task on time. Please continue to maintain this level of excellence in the future.
                </p>

                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">DF No.</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . $df_no . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Task Name</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . ucwords(strtolower($task_name)) . '</td>
                    </tr>
                    
                </table>

                <p style="font-size: 16px; line-height: 1.6;">
                    If you have any questions or need further details, please do not hesitate to contact us.
                </p>

               
            </div>

            <div style="background-color: #4872b8; padding: 10px; text-align: center; color: #fff;">
                &copy; ' . date('Y') . ' Shubham Flexible Packaging. All Rights Reserved.
            </div>
        </div>
    </body>
    </html>
    ';

    // Set email message
    $this->email->message($message);

    // Send the email
    if ($this->email->send()) {
        log_message('info', 'Approval email successfully sent to ' . $email);
    } else {
        log_message('error', 'Failed to send approval email to ' . $email);
    }
}


public function markaspendingtoreject()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];	
    // Get record ID from URL segment
    $recordid = $this->uri->segment(3);

    // Fetch user details and task details for the record
    $q = $this->db->select('a.id, c.task_name, d.df_no, b.user_id, b.first_name, b.title, b.last_name, b.email')
                  ->from('task_department_wise_scheduling a')
                  ->join('task_management c','a.taskid=c.task_id','left')
                  ->join('df_release d','a.df_id=d.id','left')
                  ->join('system_users b', 'a.assigned_user = b.user_id', 'left')
                  ->where('a.id', $recordid)
                  ->get();

    if ($q->num_rows() > 0) {
        $row = $q->row(); // Get the first row of results

        // Update task status to 0 (Rejected)
        $data = array('task_status' => 0, 'checked_by_sir' => $user_id, 'checked_on' => date('Y-m-d H:i:s'));
        $this->db->where('id', $recordid);
        $res = $this->db->update('task_department_wise_scheduling', $data);

        if ($res) {
            $username = $row->title . " " . $row->first_name . " " . $row->last_name;
            // Send email notification to user
            $this->sendTaskRejectionEmail($row->email, $username, $row->task_name, $row->df_no);
            log_message('info', 'Task marked as rejected and email sent to ' . $row->email);
        } else {
            log_message('error', 'Failed to update task status for task ID: ' . $recordid);
        }
    } else {
        log_message('error', 'Task or user not found for task ID: ' . $recordid);
    }

    $this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable" style="color:black; font-size:18px;">Thank you, Task marked as rejected.</div>');
    redirect(page_url . "Dashboard");
}

/**
 * Function to send email notification to the user when the task is rejected
 * 
 * @param string $email User's email
 * @param string $name User's full name (Title, First Name, Last Name)
 * @param string $task_name Name of the task that was rejected
 * @param string $df_no DF Number of the task
 */
private function sendTaskRejectionEmail($email, $name, $task_name, $df_no)
{
    // Load email library if not already loaded
    $this->load->library('email');
	$user_id = $this->session->userdata['logged_in']['user_id'];
	$q= $this->db->select('title, first_name, last_name')->from('system_users')->where('user_id',$user_id)->get();
	foreach($q->result() as $doneby);
	$markedby = ucwords(strtolower($doneby->title." ".$doneby->first_name." ".$doneby->last_name));
    // Set email sender, recipient, and subject
    $this->email->from('taskmanagement@shubhampack.com', 'Shubham Flexible Packaging - Task Rejected');
    $this->email->to($email);
    $this->email->cc('mangleshup@gmail.com');
    $this->email->subject('Task Rejection Notification - ' . $task_name);

    // Email message
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task Rejection Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333;">
        <div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="background-color: #4872b8; padding: 20px; text-align: center;">
                <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Company Logo" style="max-width: 150px;">
            </div>
            
            <div style="padding: 20px;">
                <h2 style="color: #e74c3c; text-align: center;">Task Rejected</h2>

                <p style="font-size: 16px; line-height: 1.6;">Dear ' . ucwords($name) . ',</p>

                <p style="font-size: 16px; line-height: 1.6;">
                    We would like to inform you that your task titled <strong>' . $task_name . '</strong> with DF No. <strong>' . $df_no . '</strong> has been <strong>rejected</strong> by <strong>'.$markedby.'</strong>.
                </p>

                <p style="font-size: 16px; line-height: 1.6;">
                    Please review the task details and make the necessary adjustments to meet the required standards. We encourage you to review the feedback and resubmit the task for approval.
                </p>

                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">DF No.</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . $df_no . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #4872b8; color: #fff; font-weight: bold;">Task Name</td>
                        <td style="padding: 10px; background-color: #f9f9f9;">' . $task_name . '</td>
                    </tr>
                   
                </table>

                <p style="font-size: 16px; line-height: 1.6;">
                    If you have any questions or need further guidance, please reach out to us.
                </p>

               
            </div>

            <div style="background-color: #4872b8; padding: 10px; text-align: center; color: #fff;">
                &copy; ' . date('Y') . ' Shubham Flexible Packaging. All Rights Reserved.
            </div>
        </div>
    </body>
    </html>
    ';

    // Set email message
    $this->email->message($message);

    // Send the email
    if ($this->email->send()) {
        log_message('info', 'Rejection email successfully sent to ' . $email);
    } else {
        log_message('error', 'Failed to send rejection email to ' . $email);
    }
}


public function directmarkaspendingtoapprove()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];	
    $recordid = $this->input->post('task_id'); // Get task ID from POST data

    $q = $this->db->select('a.id, c.task_name, d.df_no, b.user_id, b.first_name, b.title, b.last_name, b.email, a.task_completed_on')
                  ->from('task_department_wise_scheduling a')
                  ->join('task_management c', 'a.taskid=c.task_id', 'left')
                  ->join('df_release d', 'a.df_id=d.id', 'left')
                  ->join('system_users b', 'a.assigned_user = b.user_id', 'left')
                  ->where('a.id', $recordid)
                  ->get();

    if ($q->num_rows() > 0) {
        $row = $q->row();

        // Update task status to 1 (Approved)
        $data = [
            'task_status' => 1,
            'checked_by_sir' => $user_id,
            'task_completed_on'=>$row->task_completed_on,
            'checked_on' => date('Y-m-d H:i:s')
        ];
        //echo "<pre>"; print_r($data); exit;

        $this->db->where('id', $recordid);
        $res = $this->db->update('task_department_wise_scheduling', $data);

        if ($res) {
            $username = $row->title . " " . $row->first_name . " " . $row->last_name;
            $this->sendTaskApprovalEmail($row->email, $username, $row->task_name, $row->df_no);

            echo json_encode(['success' => true, 'message' => 'Task marked as approved.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update task status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Task not found.']);
    }
}

public function directmarkaspendingtoreject()
{
    $user_id = $this->session->userdata['logged_in']['user_id'];	
    $recordid = $this->input->post('task_id'); // Get task ID from POST data

    $q = $this->db->select('a.id, c.task_name, d.df_no, b.user_id, b.first_name, b.title, b.last_name, b.email')
                  ->from('task_department_wise_scheduling a')
                  ->join('task_management c', 'a.taskid=c.task_id', 'left')
                  ->join('df_release d', 'a.df_id=d.id', 'left')
                  ->join('system_users b', 'a.assigned_user = b.user_id', 'left')
                  ->where('a.id', $recordid)
                  ->get();

    if ($q->num_rows() > 0) {
        $row = $q->row();

        // Update task status to 0 (Rejected)
        $data = [
            'task_status' => 0,
            'checked_by_sir' => $user_id,
            'checked_on' => date('Y-m-d H:i:s'),
            'task_completed_by'=>0
        ];

       // echo "<pre>"; print_r($data); exit;
        $this->db->where('id', $recordid);
        $res = $this->db->update('task_department_wise_scheduling', $data);

        if ($res) {
            $username = $row->title . " " . $row->first_name . " " . $row->last_name;
            $this->sendTaskRejectionEmail($row->email, $username, $row->task_name, $row->df_no);

            echo json_encode(['success' => true, 'message' => 'Task marked as rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update task status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Task not found.']);
    }
}


public function running_df_delay_report()
{

    
    // SQL query to calculate delays for running DFs and sort by delay percentage
     

    $query = "
        SELECT 
            d.department, df.df_no,
            COUNT(t.id) AS total_tasks,
            MAX(
                CASE 
                    WHEN t.end_date < CURDATE() AND t.task_status = 0 THEN DATEDIFF(CURDATE(), t.end_date)
                    ELSE 0
                END
            ) AS max_delay_days
        FROM task_department_wise_scheduling t
        JOIN df_release df ON t.df_id = df.id
        JOIN departments d ON t.department_id = d.department_id
        WHERE df.df_status = 'running'
          AND t.task_status = 0
          AND t.end_date < CURDATE()
           AND t.on_hold = '0'
        GROUP BY t.department_id
        ORDER BY max_delay_days DESC;
    ";

    $data['report'] = $this->db->query($query)->result_array();
    echo "<pre>"; print_r($data['report']); exit;
    
    // Pass data to the view
    $this->load->view('running_df_delay_report', $data);
}


public function pending_tasks($department_id)
{
    $department_id = (int) $department_id;

    $department_row = $this->db
        ->select('department')
        ->from('departments')
        ->where('department_id', $department_id)
        ->get()
        ->row_array();

    $data = array(
        'department_id' => $department_id,
        'department' => !empty($department_row['department']) ? $this->format_pending_task_title_case($department_row['department']) : 'Unknown Department',
        'tasks' => array(),
        'summary' => array(
            'total_tasks' => 0,
            'unique_df_count' => 0,
            'max_delay_days' => 0,
            'avg_delay_days' => 0,
            'unassigned_tasks' => 0,
            'tasks_without_updates' => 0,
            'tasks_with_open_tickets' => 0,
            'critical_tasks' => 0,
            'stale_tasks' => 0
        ),
        'owner_summary' => array(),
        'df_summary' => array()
    );

    $po_sub_query = "
        (
            SELECT 
                p1.df_id,
                p1.company_name,
                p1.added_by
            FROM poreceived p1
            INNER JOIN (
                SELECT MAX(id) AS latest_po_id
                FROM poreceived
                WHERE df_id IS NOT NULL
                AND df_id > 0
                GROUP BY df_id
            ) latest_po ON latest_po.latest_po_id = p1.id
        ) po
    ";

    $query = "
        SELECT
            t.id,
            t.df_id,
            t.taskid,
            IFNULL(tt.task_name, '') as task_name,
            IFNULL(df.df_no, '') as df_no,
            IFNULL(df.df_description, '') as df_description,
            df.added_on as df_release_date,
            t.start_date,
            t.end_date,
            DATEDIFF(CURDATE(), t.end_date) AS delay_days,
            IFNULL(t.remarks, '') as task_remarks,
            t.taskupdatedontime,
            t.assigned_user,
            assignee.title as assigned_title,
            assignee.first_name as assigned_first_name,
            assignee.last_name as assigned_last_name,
            IFNULL(po.company_name, '') as company_name,
            marketing.title as marketing_title,
            marketing.first_name as marketing_first_name,
            marketing.last_name as marketing_last_name,
            (
                SELECT COUNT(cts.id)
                FROM communication_ticket_system cts
                WHERE cts.task_record_id = t.id
                AND cts.ticket_status = 0
            ) as open_ticket_count,
            (
                SELECT cts.help_ticket_no
                FROM communication_ticket_system cts
                WHERE cts.task_record_id = t.id
                ORDER BY cts.added_on DESC, cts.id DESC
                LIMIT 1
            ) as latest_ticket_no,
            (
                SELECT COALESCE(NULLIF(cts.updated_remarks, ''), NULLIF(cts.remarks, ''), '')
                FROM communication_ticket_system cts
                WHERE cts.task_record_id = t.id
                ORDER BY cts.added_on DESC, cts.id DESC
                LIMIT 1
            ) as latest_ticket_remark,
            (
                SELECT cts.added_on
                FROM communication_ticket_system cts
                WHERE cts.task_record_id = t.id
                ORDER BY cts.added_on DESC, cts.id DESC
                LIMIT 1
            ) as latest_ticket_on,
            (
                SELECT tps.remarks
                FROM task_pending_status tps
                WHERE tps.recordid = t.id
                ORDER BY tps.added_on DESC, tps.id DESC
                LIMIT 1
            ) as latest_pending_remark,
            (
                SELECT tps.added_on
                FROM task_pending_status tps
                WHERE tps.recordid = t.id
                ORDER BY tps.added_on DESC, tps.id DESC
                LIMIT 1
            ) as latest_pending_on
        FROM task_department_wise_scheduling t
        JOIN df_release df ON t.df_id = df.id
        JOIN task_management tt ON tt.task_id = t.taskid
        LEFT JOIN system_users assignee ON t.assigned_user = assignee.user_id
        LEFT JOIN {$po_sub_query} ON po.df_id = t.df_id
        LEFT JOIN system_users marketing ON marketing.user_id = po.added_by
        WHERE t.department_id = ?
          AND (df.df_status = 0 OR df.df_status = 'running' OR df.df_status IS NULL)
          AND IFNULL(df.on_hold, 0) = 0
          AND IFNULL(t.on_hold, 0) = 0
          AND t.task_status = 0
          AND t.end_date < CURDATE()
        ORDER BY delay_days DESC, t.end_date ASC, t.id ASC
    ";

    $task_rows = $this->db->query($query, array($department_id))->result_array();

    $df_summary = array();
    $owner_summary = array();
    $unique_df_ids = array();
    $delay_total = 0;

    foreach ($task_rows as $index => $task_row) {
        $task_row['task_name'] = $this->format_pending_task_title_case($task_row['task_name']);
        $task_row['assigned_to'] = $this->format_pending_task_person_name(
            $task_row['assigned_title'],
            $task_row['assigned_first_name'],
            $task_row['assigned_last_name']
        );
        if ($task_row['assigned_to'] === '') {
            $task_row['assigned_to'] = 'Unassigned';
            $data['summary']['unassigned_tasks']++;
        }

        $task_row['marketing_person'] = $this->format_pending_task_person_name(
            $task_row['marketing_title'],
            $task_row['marketing_first_name'],
            $task_row['marketing_last_name']
        );

        $latest_update_remark = trim((string) $task_row['latest_pending_remark']);
        $latest_update_on = trim((string) $task_row['latest_pending_on']);

        if ($latest_update_remark === '') {
            $latest_update_remark = trim((string) $task_row['task_remarks']);
            $latest_update_on = trim((string) $task_row['taskupdatedontime']);
        }

        if ($latest_update_remark === '') {
            $latest_update_remark = trim((string) $task_row['latest_ticket_remark']);
            $latest_update_on = trim((string) $task_row['latest_ticket_on']);
        }

        $task_row['latest_update_remark'] = $latest_update_remark;
        $task_row['latest_update_on'] = $latest_update_on;

        if ($latest_update_remark === '') {
            $data['summary']['tasks_without_updates']++;
        }

        $update_age_days = null;
        if (!empty($latest_update_on) && $latest_update_on !== '0000-00-00 00:00:00') {
            $update_age_days = (int) floor((strtotime(date('Y-m-d')) - strtotime(date('Y-m-d', strtotime($latest_update_on)))) / 86400);
        }
        $task_row['update_age_days'] = $update_age_days;

        $delay_days = (int) $task_row['delay_days'];
        $delay_total += $delay_days;
        $data['summary']['max_delay_days'] = max($data['summary']['max_delay_days'], $delay_days);

        if ($delay_days >= 15) {
            $task_row['urgency_key'] = 'critical';
            $task_row['urgency_label'] = 'Critical';
            $data['summary']['critical_tasks']++;
        } elseif ($delay_days >= 8) {
            $task_row['urgency_key'] = 'high';
            $task_row['urgency_label'] = 'High';
        } else {
            $task_row['urgency_key'] = 'watch';
            $task_row['urgency_label'] = 'Watch';
        }

        if ($update_age_days === null || $update_age_days > 7) {
            $data['summary']['stale_tasks']++;
        }

        if ((int) $task_row['open_ticket_count'] > 0) {
            $data['summary']['tasks_with_open_tickets']++;
        }

        $task_row['df_no'] = strtoupper(trim((string) $task_row['df_no']));
        $task_row['company_name'] = $this->format_pending_task_title_case($task_row['company_name']);
        $task_row['df_description'] = trim((string) $task_row['df_description']);
        $task_row['gantt_url'] = page_url . 'Task/finalgantchartWithDetails/' . (int) $task_row['df_id'];
        $task_row['df_detail_url'] = page_url . 'Dashboard/df_full_detail?df_id=' . (int) $task_row['df_id'];

        $unique_df_ids[$task_row['df_id']] = true;

        $df_key = (int) $task_row['df_id'];
        if (!isset($df_summary[$df_key])) {
            $df_summary[$df_key] = array(
                'df_id' => $df_key,
                'df_no' => $task_row['df_no'],
                'company_name' => $task_row['company_name'],
                'task_count' => 0,
                'max_delay_days' => 0,
                'open_ticket_count' => 0
            );
        }
        $df_summary[$df_key]['task_count']++;
        $df_summary[$df_key]['max_delay_days'] = max($df_summary[$df_key]['max_delay_days'], $delay_days);
        $df_summary[$df_key]['open_ticket_count'] += (int) $task_row['open_ticket_count'];

        $owner_key = $task_row['assigned_to'];
        if (!isset($owner_summary[$owner_key])) {
            $owner_summary[$owner_key] = array(
                'owner' => $owner_key,
                'task_count' => 0,
                'max_delay_days' => 0,
                'open_ticket_count' => 0
            );
        }
        $owner_summary[$owner_key]['task_count']++;
        $owner_summary[$owner_key]['max_delay_days'] = max($owner_summary[$owner_key]['max_delay_days'], $delay_days);
        $owner_summary[$owner_key]['open_ticket_count'] += (int) $task_row['open_ticket_count'];

        $task_rows[$index] = $task_row;
    }

    $data['tasks'] = $task_rows;
    $data['summary']['total_tasks'] = count($task_rows);
    $data['summary']['unique_df_count'] = count($unique_df_ids);
    $data['summary']['avg_delay_days'] = ($data['summary']['total_tasks'] > 0)
        ? round($delay_total / $data['summary']['total_tasks'])
        : 0;

    $data['df_summary'] = array_values($df_summary);
    usort($data['df_summary'], function ($left, $right) {
        if ((int) $left['max_delay_days'] === (int) $right['max_delay_days']) {
            if ((int) $left['task_count'] === (int) $right['task_count']) {
                return 0;
            }
            return ((int) $left['task_count'] > (int) $right['task_count']) ? -1 : 1;
        }
        return ((int) $left['max_delay_days'] > (int) $right['max_delay_days']) ? -1 : 1;
    });

    $data['owner_summary'] = array_values($owner_summary);
    usort($data['owner_summary'], function ($left, $right) {
        if ((int) $left['task_count'] === (int) $right['task_count']) {
            if ((int) $left['max_delay_days'] === (int) $right['max_delay_days']) {
                return 0;
            }
            return ((int) $left['max_delay_days'] > (int) $right['max_delay_days']) ? -1 : 1;
        }
        return ((int) $left['task_count'] > (int) $right['task_count']) ? -1 : 1;
    });

    $this->load->view('master/department_wise_delayed_task', $data);
}

private function format_pending_task_person_name($title, $first_name, $last_name)
{
    $parts = array();
    foreach (array($title, $first_name, $last_name) as $value) {
        $value = trim((string) $value);
        if ($value !== '') {
            $parts[] = $this->format_pending_task_title_case($value);
        }
    }

    return trim(implode(' ', $parts));
}

private function format_pending_task_title_case($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    $value = preg_replace('/\s+/', ' ', $value);
    return ucwords(strtolower($value));
}


public function updateremarktasktime(){
	$q = $this->db->select()->from('')->where()->get();
}




function getTicktsDepartmentWise()
{
	$dfid=$this->input->post('dfid');
	$departmentid=$this->input->post('departmentid');
		$html='<table class="table table-bordered" style="width:100%;">
		<thead>
		<tr>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Sr No.</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Ticket No.</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">DF Number</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Task Name</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Ticket Particular</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Created By/On</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Accountable</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Status</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Remarks</th>
		<th style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; text-align: left; font-size:12px;">Pending Since</th>
		</tr>
		</thead>
		<tbody>';
		$this->db->select('a.ticket_status,a.id, a.user_id, a.help_ticket_no, a.remarks,a.added_by, a.updated_remarks, a.added_on, a.updated_on, b.department, c.task_name, d.title, d.first_name, d.last_name, e.title as usertitle, e.first_name as fname, e.last_name as lname, f.df_no')->from('communication_ticket_system a')->join('departments b','a.department_id=b.department_id')->join('task_management c','a.task_id=c.task_id')->join('system_users d','a.added_by=d.user_id','left')->join('system_users e','a.user_id=e.user_id')->join('df_release f','a.df_id=f.id','left');
		if($dfid<>'ALL')
		{
		$this->db->where('a.df_id',$dfid);
		}

		if($departmentid<>'ALL')
		{
			$this->db->where('a.department_id',$departmentid);
		}

		$rest=$this->db->get();

		if($rest->num_rows()>0)
		{
		$i=1;
		foreach($rest->result() as $row)
		{
			$days=$this->calculateDayDiff(date('Y-m-d',strtotime($row->added_on)),date('Y-m-d'));

			if($row->updated_remarks=='')
			{
				$sta="<strong style='color:red;'>Open</strong>";
			}else 
			{
				if($row->ticket_status==0)
				{
					$sta="<strong style='color:red;'>Open</strong>";
				}else
				{
					$sta="<strong style='color:green;'>Closed</strong>";
				}
			}
		$html.='<tr>
		<td style="text-align:center; font-size:12px;">'.$i.'</td>
		<td style="text-align:center; font-size:12px;">'.$row->help_ticket_no.'</td>
		<td style="text-align:center; font-size:12px;">'.$row->df_no.'</td>
		<td style="text-align:center; font-size:12px;">'.$row->task_name.'</td>
		<td style="text-align:center; color:red; font-weight:bold; font-size:12px;">'.ucwords(strtolower($row->remarks)).'</td>
		<td style="text-align:center; font-size:12px;">'.ucwords(strtolower($row->title." ".$row->first_name." ".$row->last_name)).'<br/><br/>'.date('d-m-Y',strtotime($row->added_on))."<br> ".date('h:i A',strtotime($row->added_on)).'</td>
		<td style="text-align:center; font-size:12px;">'.ucwords(strtolower($row->title." ".$row->fname." ".$row->lname)).'<br/>('.ucwords(strtolower($row->department)).')</td>
		<td style="text-align:center; font-size:12px;">'.$sta.'</td>
		<td style="text-align:center; font-size:12px;">'.$row->updated_remarks.'<br/></td>
		<td style="text-align:center;color:red;font-weight:bold; font-size:12px;">'.$days.' days</td>
		</tr>';
		$i++;
		}
		}

		$html.='</tbody></table>';

		echo $html;

}




function calculateDayDiff($d1,$d2)
{
// Define the two dates
$date1 = new DateTime($d1);
$date2 = new DateTime($d2);
// Calculate the difference between the two dates
$interval = $date1->diff($date2);
// Output the difference in days
return $interval->days;
}


public function task_completed_pending_for_approval(){
	$this->load->view('dashboard/task_completed_before_time_and_pending_for_approval');
}

public function basicmachine(){
	$this->load->view('taskview/basic-machine.php');
}

	public function addbasicmachine()
		{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('machine_type', 'Machine Type', 'required|trim');
		//$this->form_validation->set_rules('modelno', 'Modelno', 'required|trim');
		$this->form_validation->set_rules('order_punch_date', 'Order Punch Date', 'required|trim');
		$this->form_validation->set_rules('speed', 'speed', 'required|trim');
		$this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('taskview/basic-machine.php');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		
			$dfnumber= $this->getdfno();	
			$data = array('machinetype'=>$this->input->post('machine_type'),
			'machine_model'=>$this->input->post('modelno'),
			'machine_punch_date'=>date('Y-m-d',strtotime($this->input->post('order_punch_date'))),
			'podate'=>date('Y-m-d',strtotime($this->input->post('order_punch_date'))),
			'basic_machine'=>1,
			'speed'=>$this->input->post('speed'),
			'productname'=>$this->input->post('productname'),
			'reference_df'=>$this->input->post('referencedf'),
			'added_on'=>$date,
			'df_number'=>$dfnumber,
			'added_by'=>$user_id);
			$this->db->insert('poreceived',$data);
			$polastid = $this->db->insert_id();

			$nexttaskid = 0;
			$departmentid = 0;
			$enddate = date('Y-m-d');

			/*Notification of PO Release*/
			//$this->task->notificationofprocessdone(1);
			/*Notification of PO Release*/

			
			/*PO Release entry in Task scheduling table*/
			$data1 = array('df_id'=>0,
				'taskid'=>1,
				'department_id'=>9,
				'start_date'=>date('Y-m-d'),
				'end_date'=>date('Y-m-d'),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'po_id'=>$polastid,
				'task_status'=>1,
				'remarks'=>'',
				'task_completed_on'=>date('Y-m-d H:i:s'),
				'task_completed_by'=>$user_id,
				'assigned_user'=>$user_id, 
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'),
				'userid'=>$user_id);
			  

				$nexttaskid = 114;
				$departmentid = 9;

				$startdate = date('Y-m-d');
				$enddate = date('Y-m-d');

					

				//echo $startdate."<br/>".$enddate; exit;
				$data1 = array('df_id'=>0,
				'taskid'=>$nexttaskid,
				'department_id'=>$departmentid,
				'start_date'=>$startdate,
				'end_date'=>$enddate,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'assigned_user'=>$user_id, 
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'),
				'po_id'=>$polastid);

			$this->db->insert('task_department_wise_scheduling',$data1);
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Task/basicmachine');
				
			
			
		
		}
		
	}


function getdfno(){
	$dfno = 1730;
	$q = $this->db->select('MAX(df_number) as dfnumber')->from('poreceived')->where('df_number!=',0)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);
		if($row->dfnumber<>''){
			$dfno= $row->dfnumber+1;
		}
		
	}
	return $dfno;

}

public function basicmachinedata(){
	$this->load->view('master/basic-machine-list.php');
}


public function legacy_data(){
		$this->load->view('master/dfreceived');
	}

	public function addlegacydata()
		{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('existingpaymentterms', 'Payment Term', 'required|trim');
		$this->form_validation->set_rules('companyname', 'Company Name', 'required|trim');
		$this->form_validation->set_rules('pono', 'Po Number', 'required|trim');
		$this->form_validation->set_rules('podate', 'PO Date', 'required|trim');
		$this->form_validation->set_rules('order_value', 'Order Value', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/dfreceived');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$photo=$_FILES['po_attachment']['name'];
			if($photo<>'')
			{
				$image1=explode('.',$photo);
				$cat_image=end($image1);
				$poattachment=time().'.'.$cat_image;
				move_uploaded_file($_FILES['po_attachment']["tmp_name"],UPLOADPATH.'Taskdocument/' . $poattachment);
			}else
			{
				$poattachment="";
				}

				$photo1=$_FILES['df_attachment']['name'];
			if($photo1<>'')
			{
				$image1=explode('.',$photo1);
				$cat_image=end($image1);
				$dfattachment=time().'.'.$cat_image;
				move_uploaded_file($_FILES['df_attachment']["tmp_name"],UPLOADPATH.'Taskdocument/' . $dfattachment);
			}else
			{
				$dfattachment="";
				}

			$data = array('company_name'=>$this->input->post('companyname'),
			'pono'=>$this->input->post('pono'),
			'podate'=>date('Y-m-d',strtotime($this->input->post('podate'))),
			'df_attachment'=>$dfattachment,
			'po_attachment'=>$poattachment,
			'financialyear'=>$this->input->post('financialyear'),
			'remark'=>$this->input->post('remark'),
			'added_on'=>$date,
			'order_value'=>$this->input->post('order_value'),
			'customer_currency'=>$this->input->post('currency'),
			'amount_in_customer_currency'=>$this->input->post('ordervalueincustomercurrency'),
			'added_by'=>$user_id);
			$this->db->insert('dfreceived',$data);
		
		}
		
	}

	public function save_machine(){

		  $machine_id = $this->input->post('machine_id');
		  $dfid = $this->input->post('dfid');

		  $data = array('machine_id'=>$machine_id);

		  $this->db->where('id', $dfid);

		  $this->db->update('df_release', $data);


	}



	public function finalgantchartwithDetails(){
		$this->load->view('charts/finalgantchartwithDetails');
	}

public function get_task_details() {
    $df_id = (int) $this->input->post('df_id');
    $department_id = (int) $this->input->post('department_id');
    $task_status = trim((string) $this->input->post('task_status'));
    $detail_scope = trim((string) $this->input->post('detail_scope'));
    $response_type = trim((string) $this->input->post('response_type'));

    if ($df_id <= 0 || $department_id <= 0) {
        if ($response_type === 'json') {
            echo json_encode(['success' => false, 'rows' => [], 'message' => 'Task details are not available for this selection.']);
            return;
        }

        echo '<div class="alert alert-warning" style="margin-bottom:0;">Task details are not available for this selection.</div>';
        return;
    }

    $this->db->select('
        t.task_name,
        a.id as task_record_id,
        a.start_date,
        a.end_date,
        a.task_status,
        DATE(a.task_completed_on) as task_completed_on,
        IFNULL(a.remarks, "") as task_remarks,
        (
            SELECT tps.remarks
            FROM task_pending_status tps
            WHERE tps.recordid = a.id
            ORDER BY tps.added_on DESC, tps.id DESC
            LIMIT 1
        ) as latest_pending_remark,
        (
            SELECT COUNT(cts.id)
            FROM communication_ticket_system cts
            WHERE cts.task_record_id = a.id
        ) as ticket_count,
        (
            SELECT COUNT(cts.id)
            FROM communication_ticket_system cts
            WHERE cts.task_record_id = a.id
            AND cts.ticket_status = 0
        ) as open_ticket_count,
        (
            SELECT cts.help_ticket_no
            FROM communication_ticket_system cts
            WHERE cts.task_record_id = a.id
            ORDER BY cts.added_on DESC, cts.id DESC
            LIMIT 1
        ) as latest_ticket_no,
        (
            SELECT COALESCE(NULLIF(cts.updated_remarks, ""), NULLIF(cts.remarks, ""), "")
            FROM communication_ticket_system cts
            WHERE cts.task_record_id = a.id
            ORDER BY cts.added_on DESC, cts.id DESC
            LIMIT 1
        ) as latest_ticket_remark,
        (
            SELECT DATE_FORMAT(cts.added_on, "%d-%m-%Y %h:%i %p")
            FROM communication_ticket_system cts
            WHERE cts.task_record_id = a.id
            ORDER BY cts.added_on DESC, cts.id DESC
            LIMIT 1
        ) as latest_ticket_on,
        (
            SELECT TRIM(CONCAT(IFNULL(su.title, ""), " ", IFNULL(su.first_name, ""), " ", IFNULL(su.last_name, "")))
            FROM communication_ticket_system cts
            LEFT JOIN system_users su ON su.user_id = cts.added_by
            WHERE cts.task_record_id = a.id
            ORDER BY cts.added_on DESC, cts.id DESC
            LIMIT 1
        ) as latest_ticket_by,
        (
            SELECT cts.ticket_status
            FROM communication_ticket_system cts
            WHERE cts.task_record_id = a.id
            ORDER BY cts.added_on DESC, cts.id DESC
            LIMIT 1
        ) as latest_ticket_status,
        u.title,
        u.first_name,
        u.last_name
    ');
    $this->db->from('task_department_wise_scheduling a');
    $this->db->join('task_management t', 'a.taskid = t.task_id', 'left');
    $this->db->join('system_users u', 'a.assigned_user = u.user_id', 'left');
    $this->db->where('a.df_id', $df_id);
    $this->db->where('a.department_id', $department_id);

    if ($detail_scope !== 'all') {
        $status_filter = ($task_status === 'delayed') ? [0, 2] : [1];
        $this->db->where_in('a.task_status', $status_filter);
    }

    $this->db->order_by('a.end_date', 'ASC');
    $this->db->order_by('t.task_name', 'ASC');
    $query = $this->db->get();

    if ($query->num_rows() <= 0) {
        if ($response_type === 'json') {
            echo json_encode(['success' => true, 'rows' => []]);
            return;
        }

        echo '<div class="alert alert-info" style="margin-bottom:0;">No task details found for this department.</div>';
        return;
    }

    $task_rows = [];
    $output = '<div class="table-responsive">';
    $output .= '<table class="table table-bordered table-striped task-detail-table" style="margin-bottom:0;">';
    $output .= '<thead><tr>';
    $output .= '<th>Task</th>';
    $output .= '<th>Responsible Person</th>';
    $output .= '<th>Start Date</th>';
    $output .= '<th>End Date</th>';
    $output .= '<th>Actual Completion Date</th>';
    $output .= '<th>Delay In Day</th>';
    $output .= '<th>Task Remark</th>';
    $output .= '<th>Ticket Info</th>';
    $output .= '</tr></thead><tbody>';

    $today = date('Y-m-d');

    foreach ($query->result() as $task) {
        $due_date = !empty($task->end_date) && $task->end_date !== '0000-00-00' ? $task->end_date : '';
        $start_date = !empty($task->start_date) && $task->start_date !== '0000-00-00' ? $task->start_date : '';
        $completed_on = !empty($task->task_completed_on) && $task->task_completed_on !== '0000-00-00' ? $task->task_completed_on : '';
        $current_task_status = (int) $task->task_status;

        $delay_days = 0;

        if ($due_date !== '') {
            if ($current_task_status === 1 && $completed_on !== '') {
                $delay_days = (int) floor((strtotime($completed_on) - strtotime($due_date)) / 86400);
            } else {
                $delay_days = (int) floor((strtotime($today) - strtotime($due_date)) / 86400);
            }
        }

        if ($delay_days < 0) {
            $delay_days = 0;
        }

        $assigned_to = trim($task->title . ' ' . $task->first_name . ' ' . $task->last_name);
        $assigned_to = $assigned_to !== '' ? ucwords(strtolower($assigned_to)) : 'Not Assigned';

        $formatted_start_date = $start_date !== '' ? date('d-m-Y', strtotime($start_date)) : '-';
        $formatted_due_date = $due_date !== '' ? date('d-m-Y', strtotime($due_date)) : '-';
        $formatted_completed_date = $completed_on !== '' ? date('d-m-Y', strtotime($completed_on)) : 'Not Completed';
        $delay_label = $delay_days > 0 ? $delay_days . ' day' . ($delay_days > 1 ? 's' : '') : 'On Time';
        $delay_class = $delay_days > 0 ? 'style="color:#b91c1c; font-weight:700;"' : 'style="color:#15803d; font-weight:700;"';
        $task_remark = trim((string) $task->latest_pending_remark);
        if ($task_remark === '') {
            $task_remark = trim((string) $task->task_remarks);
        }
        if ($task_remark === '') {
            $task_remark = '-';
        }

        $ticket_count = (int) $task->ticket_count;
        $open_ticket_count = (int) $task->open_ticket_count;
        $latest_ticket_no = trim((string) $task->latest_ticket_no);
        $latest_ticket_by = trim((string) $task->latest_ticket_by);
        $latest_ticket_on = trim((string) $task->latest_ticket_on);
        $latest_ticket_remark = trim((string) $task->latest_ticket_remark);
        $latest_ticket_status = '';

        if ($ticket_count > 0) {
            $latest_ticket_status = ((string) $task->latest_ticket_status === '1') ? 'Closed' : 'Open';
        }

        $ticket_lines = [];
        if ($ticket_count > 0) {
            $ticket_lines[] = 'Tickets Raised: ' . $ticket_count;

            if ($open_ticket_count > 0) {
                $ticket_lines[] = 'Open Tickets: ' . $open_ticket_count;
            }

            if ($latest_ticket_no !== '') {
                $ticket_lines[] = 'Latest Ticket: ' . $latest_ticket_no;
            }

            if ($latest_ticket_by !== '') {
                $ticket_lines[] = 'Raised By: ' . $latest_ticket_by;
            }

            if ($latest_ticket_on !== '') {
                $ticket_lines[] = 'Raised On: ' . $latest_ticket_on;
            }

            if ($latest_ticket_status !== '') {
                $ticket_lines[] = 'Latest Status: ' . $latest_ticket_status;
            }

            if ($latest_ticket_remark !== '') {
                $ticket_lines[] = 'Ticket Remark: ' . $latest_ticket_remark;
            }
        }

        $ticket_info_plain = !empty($ticket_lines) ? implode(' | ', $ticket_lines) : 'No Ticket Raised';
        $ticket_info_html = '<span style="color:#64748b;">No Ticket Raised</span>';

        if (!empty($ticket_lines)) {
            $ticket_info_html_lines = [];
            foreach ($ticket_lines as $ticket_line) {
                $ticket_info_html_lines[] = htmlspecialchars($ticket_line, ENT_QUOTES, 'UTF-8');
            }
            $ticket_info_html = '<div style="min-width:260px; line-height:1.5;">' . implode('<br>', $ticket_info_html_lines) . '</div>';
        }

        $task_rows[] = [
            'task_name' => (string) $task->task_name,
            'assigned_to' => $assigned_to,
            'start_date' => $formatted_start_date,
            'end_date' => $formatted_due_date,
            'actual_completion_date' => $formatted_completed_date,
            'delay_in_day' => $delay_label,
            'delay_days_numeric' => $delay_days,
            'task_remark' => $task_remark,
            'ticket_info' => $ticket_info_plain,
            'ticket_count' => $ticket_count,
            'open_ticket_count' => $open_ticket_count,
            'latest_ticket_no' => $latest_ticket_no,
            'latest_ticket_by' => $latest_ticket_by,
            'latest_ticket_on' => $latest_ticket_on,
            'latest_ticket_status' => $latest_ticket_status,
            'latest_ticket_remark' => $latest_ticket_remark,
        ];

        $output .= '<tr>';
        $output .= '<td>' . htmlspecialchars((string) $task->task_name, ENT_QUOTES, 'UTF-8') . '</td>';
        $output .= '<td>' . htmlspecialchars($assigned_to, ENT_QUOTES, 'UTF-8') . '</td>';
        $output .= '<td>' . htmlspecialchars($formatted_start_date, ENT_QUOTES, 'UTF-8') . '</td>';
        $output .= '<td>' . htmlspecialchars($formatted_due_date, ENT_QUOTES, 'UTF-8') . '</td>';
        $output .= '<td>' . htmlspecialchars($formatted_completed_date, ENT_QUOTES, 'UTF-8') . '</td>';
        $output .= '<td ' . $delay_class . '>' . htmlspecialchars($delay_label, ENT_QUOTES, 'UTF-8') . '</td>';
        $output .= '<td style="min-width:220px; line-height:1.5;">' . nl2br(htmlspecialchars($task_remark, ENT_QUOTES, 'UTF-8')) . '</td>';
        $output .= '<td>' . $ticket_info_html . '</td>';
        $output .= '</tr>';
    }

    if ($response_type === 'json') {
        echo json_encode(['success' => true, 'rows' => $task_rows]);
        return;
    }

    $output .= '</tbody></table></div>';
    echo $output;
}


function canceledorder(){
    $orderid = $this->uri->segment(3);
    
    // Update the order status to canceled
    $data = array('orderhold' => 1);
    $this->db->where('id', $orderid);
    $this->db->update('poreceived', $data);

    $qq = $this->db->select('a.company_name, b.df_no, b.df_description, c.title, c.first_name, c.last_name')->from('poreceived a')->join('df_release b','a.df_id=b.id','left')->join('system_users c','a.added_by=c.user_id','left')->where('a.id',$orderid)->get();

    foreach($qq->result() as $rowss);

    $dfdetail = $rowss->company_name." ".$rowss->df_no." ".$rowss->df_description;
    $leadowner = $rowss->title." ".$rowss->first_name." ".$rowss->last_name;
    // Fetch team leader emails based on the provided query
    $q = $this->db->select('b.email')
                  ->from('prestogroup_teams a')
                  ->join('system_users b', 'a.team_leader=b.user_id', 'left')
                  ->where('a.business_loc_id', 2)
                  ->get();
    $team_leaders = $q->result_array();

    // Extract emails
    $cc_emails = array_column($team_leaders, 'email');

    // Define the main recipient
    $to_email = "rishibhatnagar@shubhampack.com";


    // Email Subject
    $subject = "⚠ Order Cancellation Alert - Order #$dfdetail";

    // Email Message with Improved HTML Styling
    $message = '
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 80%;
                    margin: 20px auto;
                    background: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                }
                .logo {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header {
                    background-color: #d9534f;
                    color: #ffffff;
                    text-align: center;
                    padding: 10px;
                    font-size: 20px;
                    font-weight: bold;
                    border-radius: 8px 8px 0 0;
                }
                .content {
                    padding: 20px;
                    font-size: 16px;
                    color: #333;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #d9534f;
                    color: white;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 14px;
                    color: #777;
                }
                .highlight {
                    font-weight: bold;
                    color: #d9534f;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">
                    <img src="https://pms.shubhampack.in/assets/images/shubhampack.png" alt="Shubhampack Logo" width="200">
                </div>
                <div class="header">Order Cancellation Notification</div>
                <div class="content">
                    <p>Dear Team,</p>
                    <p>The following order has been <span class="highlight">marked as Canceled</span>:</p>
                    <table>
                        <tr>
                            <th>Company Name</th>
                            <td>#'.$rowss->company_name.'</td>
                        </tr>
                        <tr>
                            <th>Order Date</th>
                            <td>'.($rowss->podate ?? 'N/A').'</td>
                        </tr>
                       
                        <tr>
                            <th>Marketing Person</th>
                            <td>'.($leadowner ?? 'N/A').'</td>
                        </tr>
                    </table>
                    <p>Please take necessary actions.</p>
                </div>
                <div class="footer">
                    <p>Regards,</p>
                    <p><strong>Shubhampack Team</strong></p>
                </div>
            </div>
        </body>
        </html>
    ';

    // Load Email Library
    $this->load->library('email');
    $this->email->from('taskmanagement@shubhampack.com', 'Shubhampack PMS Notifications');
    $this->email->to('mangleshup@gmail.com'); // Main recipient
    $this->email->to($to_email); // Main recipient
    // if (!empty($cc_emails)) {
    //     $this->email->cc($cc_emails); // Add team leaders as CC
    // }
    $this->email->subject($subject);
    $this->email->message($message);
    $this->email->set_mailtype("html");

    // Send email and set session flash message
    if ($this->email->send()) {
        $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Order canceled, and team leaders have been notified via email.</div>');
    } else {
        $this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissable">Order canceled, but email notification failed.</div>');
    }

    // Redirect to received PO list
    redirect(page_url.'Task/receivedpolist');
}



public function edit_basicmachine_record(){
	$this->load->view('master/edit_basicmachine_record');
}


public function updatebasicmachine()
		{
		
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('machine_type', 'Machine Type', 'required|trim');
		$this->form_validation->set_rules('modelno', 'Modelno', 'required|trim');
		$this->form_validation->set_rules('order_punch_date', 'Order Punch Date', 'required|trim');
		$this->form_validation->set_rules('speed', 'speed', 'required|trim');
		$this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('master/edit_basicmachine_record');
		}
		else
		{
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		

			$data = array('machinetype'=>$this->input->post('machine_type'),
			'machine_model'=>$this->input->post('modelno'),
			'machine_punch_date'=>date('Y-m-d',strtotime($this->input->post('order_punch_date'))),
			'podate'=>date('Y-m-d',strtotime($this->input->post('order_punch_date'))),
			'basic_machine'=>1,
			'speed'=>$this->input->post('speed'),
			'productname'=>$this->input->post('productname'),
			'reference_df'=>$this->input->post('referencedf'));
			$this->db->where('id',$this->uri->segment(3));
			$this->db->update('poreceived',$data);
			
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Thank You!, Record successfully updated.</div>');
			redirect(page_url.'Task/basicmachine');

			
			
			
		
		}
		
	}



public function ongoingtasklistbackupfunction()
	{
		$user_id =$this->session->userdata['logged_in']['user_id'];	
		$i=1;
		$filter_req=$this->uri->segment(3);
		/** user check **/
		$department_id=array();
		$self_user=0;
		if($_SESSION['logged_in']['adminuser']==2)
		{
		$department_id=$this->task->getAssignedDepartment($_SESSION['logged_in']['user_id']);
		}
		$user_id =$this->session->userdata['logged_in']['user_id']; 
		$deptid = $this->uri->segment(4);
		$usrid = $this->uri->segment(5);
		$df_ids = $this->uri->segment(6);
		$activeusertype= $this->uri->segment(7);
		if($activeusertype==2){
		 	$q = $this->db->select('department_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();
		 	if($q->num_rows()>0){
		 		foreach($q->result() as $rows){
		 			$leaderdepartment[] = $rows->department_id;
		 		}
		 	}
		 }

		/** end **/
		$taskdata= array();
		$this->db->select('a.assigned_user,a.department_id, a.end_date, a.start_date, a.id,a.taskid, c.sortorder, c.is_it_mom, b.df_no, c.task_name, a.taskupdatedontime, d.department, b.df_upload, b.added_on, a.remarks, a.po_id, a.df_id, a.id, f.first_name, f.last_name, c.task_frequency, b.df_description, p.pono, p.po_attachment,p.lead_id, k.first_name as marketingpersonfname, k.last_name as marketingpersonlname')->from('task_department_wise_scheduling a')->join('df_release b','a.df_id=b.id','left')->join('task_management c','a.taskid=c.task_id','left')->join('departments d','a.department_id=d.department_id','left')->join('system_users f','a.assigned_user=f.user_id','left')->join('poreceived p','a.po_id=p.id','left')->join('system_users k','p.added_by=k.user_id','left')->where('a.task_status',0)->where('a.on_hold',0)->where('a.assigned_user!=',0)->where('a.end_date>=',date('Y-m-d'));
			if(count($department_id)>0)
			{
				$this->db->where_in('a.department_id',$department_id,'false');
			}

			$this->db->where('a.department_id!=','22');
			if($_SESSION['logged_in']['adminuser']==3)
			{
				$this->db->where('a.assigned_user',$_SESSION['logged_in']['user_id']);
			}
			if($df_ids<>'' && $df_ids<>'ALL'){
				$this->db->where('a.df_id',$df_ids);
			}
			if($deptid<>'' && $deptid<>'ALL'){
				$this->db->where('a.department_id',$deptid);
			}
			if($usrid<>'' && $usrid<>'ALL'){
				$this->db->where('a.assigned_user',$usrid);
			}
			$this->db->group_start()
			->where('a.df_id', 0) // No DF uploaded yet → show task
			->or_where('b.df_status', 0) // DF exists and is active
			->group_end();
			$query = $this->db->order_by('b.df_no','asc')->get();
			$res = $query->result();
			foreach($res as $row){

				$pendinddays=$this->task->getDays(date('Y-m-d'),$row->end_date,2);
				if($row->po_id>0){
					$dfno = $row->df_no;
					if($row->added_on<>'0000-00-00 00:00:00' || $row->added_on<>'')
					{
						$dfreleasedate = date('d-m-Y',strtotime($row->added_on));
					}else{
						$dfreleasedate = "";
					}
					
					
					
				}else{
					$dfno = "NA";
					$dfreleasedate = "NA";
				}
				if($dfreleasedate=='01-01-1970'){
					$dfreleasedate='NA';
				}else{
					$dfreleasedate = $dfreleasedate;
				}
			if($user_id==$row->assigned_user){
			if($row->df_id==0){
				$updateprogress = '<span class="btn btn-primary btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.');">Release DF</span>'; 
			}else{
				if($row->is_it_mom==1){
					$updateprogress = '<a href="'.page_url.'Task/dfmeeting/'.$row->id.'/'.$row->df_id.'"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
				}else{

					$tdate = date('Y-m-d');
					// if(date('Y-m-d',strtotime($row->start_date))<=$tdate){
						$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row->id.','.$row->sortorder.','.$row->df_id.','.$row->taskid.','.$row->start_date.');">Update Progress</span>
					';
					// }else{
					// 	$updateprogress = "";
					// }

					

					
				}			
				
			}
			}else{
				if($_SESSION['logged_in']['user_id']==61 || $_SESSION['logged_in']['user_id']==161)
				{
					if($row->df_id==0){
					$updateprogress = '<span class="btn btn-primary btn-xs" onclick="updatedfrelease('.$row->id.','.$row->po_id.');">Release DF</span>'; 

				

					}else{
					if($row->is_it_mom==1){
					$updateprogress = '<a href="'.page_url.'Task/dfmeeting/'.$row->id.'/'.$row->df_id.'"><span class="btn btn-primary btn-xs">DF Meeting MOM</span></a>';
					}else{

					$tdate = date('Y-m-d');
					// if(date('Y-m-d',strtotime($row->start_date))<=$tdate){
					$updateprogress = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row->id.','.$row->sortorder.','.$row->df_id.','.$row->taskid.','.$row->start_date.');">Update Progress</span>
					';
					// }else{
					// $updateprogress = "";
					// }
					}			
					}

				}else
				{
				$updateprogress='';
				if($_SESSION['logged_in']['adminuser']==2){
					$updateprogress.= '<span class="btn btn-danger btn-xs" onclick="reassigntasktoanotheruser('.$row->id.','.$row->department_id.','.$row->assigned_user.');">Reassign</span>';
				    }
				}
			}


			$show=0;
			/*** FILTER CONDITIONS ***/
			if($filter_req==1)
			{

				if(strtotime(date('Y-m-d'))==strtotime(date('Y-m-d',strtotime($row->end_date))))
				{
				$show=1;
				}
			}else if($filter_req==2)
			{
				$show=$this->task->compareDateThisWeek(date('Y-m-d',strtotime($row->end_date)));

			}else
			{
				$show=1;
			}
			/** END **/
			$ticketlink = '';
			if($row->taskid==2){
			$pono = "PO NO. ".$row->pono;
			$po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
			}else{
			$pono= "";
			$po_attachment = "";
			}
			//$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span><u>DOWNLOAD DF</u></span></a>';

			if ($row->df_id == 0) {
			$dfno = "<span style='color:red;'>DF Not Uploaded</span>";
			$dfreleasedate = "NA";
			$dfupload = "--";
			} else {
			$dfno = strtoupper($row->df_no);
			$dfreleasedate = ($row->added_on && strtotime($row->added_on)) ? date('d-m-Y', strtotime($row->added_on)) : "NA";
			$dfupload = '<a href="'.sfdocument.'Taskdocument/dfattachment/'.$row->df_upload.'" download><span><u>DOWNLOAD DF</u></span></a>';
			}

			$userdepartment_id =$this->session->userdata['logged_in']['department_id'];	

			if($userdepartment_id==20){
				$pofordownload = '<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
			}else{
				$pofordownload  = "";
			}
			if (!empty($row->taskupdatedontime) && $row->taskupdatedontime != '0000-00-00 00:00:00' && strtotime($row->taskupdatedontime) !== false) {
			$lastremarksupdateddate = date('d-m-Y', strtotime($row->taskupdatedontime));
			$lastremarksupdatedtime = date('h:i A', strtotime($row->taskupdatedontime));
			$updatedt = $lastremarksupdateddate . "<br>" . $lastremarksupdatedtime;

			$ticketcountdata  = $this->task->checkhelpticket($row->id, $row->df_id);
			if ($ticketcountdata) {
			$ticketlink = '<a href="' . page_url . 'Task/viewdfwiseticket/' . $row->df_id . '/' . $row->id . '"><span class="btn btn-xs btn-primary">View Tickets</span></a>';
			} else {
			$ticketlink = '';
			}
			} else {
			$updatedt = "";
			}

			
				$df_form ='';
						if($_SESSION['logged_in']['user_id']==161 || $_SESSION['logged_in']['user_id']==147){

							if($row->df_id==0){
					$df_form = '<a href="'.page_url.'Dashboard/df_project_form/'.$row->id.'/'.$row->po_id.'/'.$row->lead_id.'" class="btn btn-sm btn-primary">DF Form</a>'; 

					}

			}

		

			if($show==1)
			{
			$taskdata[] = array('sr_no'=>$i,
			'df_no'=>strtoupper($dfno."<br>".$row->df_description)."<br><br>".$pono."<br>".$po_attachment." ".$pofordownload."<br> MARKETING PERSON - ".strtoupper($row->marketingpersonfname." ".$row->marketingpersonlname),
			'department'=>strtoupper($row->department),
			'task_name'=>strtoupper($row->task_name),
			'dfupload'=>$dfupload,
			'startdate'=>date('d-m-Y',strtotime($row->start_date)),
			'pendingdays'=>"<strong style='color:green;font-weight:bold;'>".$pendinddays." DAYS</strong>",
			'remarks'=>strtoupper($row->remarks)."<br><br>".$updatedt."<br>".$ticketlink,
			'df_release_date'=>$dfreleasedate,
			'end_date'=>date('d-m-Y',strtotime($row->end_date)),
			'membername'=>strtoupper($row->first_name." ".$row->last_name),
			'updateprogress'=>$updateprogress.'<br>'.$df_form);
			$i++;
		}
		}
			$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($taskdata),
			"iTotalDisplayRecords" => count($taskdata),
			"aaData"=>$taskdata);
			
		echo json_encode($results);
	}

// function fetchcreateddffile(){
// 	$id = $this->input->post('poid');
	
	
// }


function CheckDomesticExport($id)
{
	$lead=0;
	$l=$this->db->select('patient_type_id')->from('leads')->where('id',$id)->get();
	if($l->num_rows()>0)
	{
		foreach($l->result() as $ll);
		$lead=$ll->patient_type_id;
	}

	return $lead;
}

/**
 * AJAX function to get open ticket details for a specific DF.
 */
public function ajax_get_df_tickets($df_id = 0)
{
    // Check if ID is valid or if it's not an AJAX request
    if (empty($df_id) || !$this->input->is_ajax_request()) {
        // Return a JSON error instead of a 404 HTML page to handle it gracefully
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid DF ID']);
        exit;
    }

    $this->load->model('Task_model');

    $this->db->select(
        'cts.help_ticket_no, cts.remarks, ' .
        'DATE_FORMAT(cts.added_on, "%d-%m-%Y %h:%i %p") as added_date, ' .
        'CONCAT(u.first_name, " ", u.last_name) as added_by_name, ' .
        'tm.task_name'
    );
    $this->db->from('communication_ticket_system cts');
    $this->db->join('system_users u', 'cts.added_by = u.user_id', 'left');
    $this->db->join('task_department_wise_scheduling tdws', 'cts.task_record_id = tdws.id', 'left');
    
    // *** FIXED TYPO BELOW: Changed $this-db to $this->db ***
    $this->db->join('task_management tm', 'tdws.taskid = tm.task_id', 'left');
    
    $this->db->where('cts.df_id', $df_id);
    $this->db->where('cts.ticket_status', 0); // Only show OPEN tickets
    $this->db->order_by('cts.added_on', 'DESC');
    
    $query = $this->db->get();
    $tickets = $query->result_array();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'tickets' => $tickets]);
    exit;
}

public function new_dfreleasedashboard(){
	$this->load->view('master/new_dfreleasedashboard_demo'); 
}


public function new_dfreleasedashboard_demo(){
	$this->load->view('master/new_dfreleasedashboard_demo');
}

public function save_brand_action()
{
    $quotation_brand_id = $this->input->post('quotation_brand_id');

    $check = $this->db
        ->where('quotation_brand_id', $quotation_brand_id)
        ->get('quotation_brand_action');

    if($check->num_rows() == 0){

        $insert = array(
            'quotation_brand_id' => $quotation_brand_id,
            'checked_by'         => $_SESSION['logged_in']['user_id'],
            'checked_on'         => date('Y-m-d H:i:s')
        );

        $this->db->insert('quotation_brand_action', $insert);
    }

    echo 1;
}

public function delete_brand_action()
{
    $quotation_brand_id = $this->input->post('quotation_brand_id');

    $this->db->where('quotation_brand_id', $quotation_brand_id);
    $this->db->delete('quotation_brand_action');

    echo 1;
}


public function export_purchase_forecasting_excel()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $this->load->library('excel');

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // =========================================================
    // HEADER
    // =========================================================

    $sheet->mergeCells('A1:J1');
    $sheet->setCellValue('A1', 'PURCHASE FORECASTING FOR RUNNING DF');

    $sheet->getStyle('A1:J1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 16,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4']
        ]
    ]);

    // =========================================================
    // TABLE HEADER
    // =========================================================

    $headers = [
        'S NO',
        'DF NO',
        'PO DATE',
        'MARKETING PERSON',
        'DF RELEASE DATE',
        'PURCHASE START DATE',
        'PURCHASE END DATE',
        'DESCRIPTION',
        'BRAND / MAKE',
        'PURCHASE STATUS'
    ];

    $col = 'A';

    foreach ($headers as $head) {

        $sheet->setCellValue($col . '3', $head);

        $sheet->getStyle($col . '3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5B9BD5']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ]);

        $col++;
    }

    // =========================================================
    // MAIN QUERY
    // =========================================================

    $this->db->select("
        df.id,
        df.df_no,
        df.added_on,

        po.podate,
        po.lead_id,

        CONCAT(
            u.title,
            ' ',
            u.first_name,
            ' ',
            u.last_name
        ) as dfowner,

        MIN(tasks.start_date) as df_start_date,
        MAX(tasks.end_date) as projected_completion_date
    ");

    $this->db->from('df_release df');

    $this->db->join(
        'poreceived po',
        'po.df_id = df.id',
        'left'
    );

    $this->db->join(
        'system_users u',
        'po.added_by = u.user_id',
        'left'
    );

    $this->db->join(
        'task_department_wise_scheduling tasks',
        'tasks.df_id = df.id
        AND tasks.department_id = 13
        AND tasks.on_hold = 0',
        'left'
    );

    $this->db->where('df.df_status', 0);
    $this->db->where('df.on_hold', 0);

    $this->db->group_by('df.id');

    $query = $this->db->get();

    $row = 4;
    $sr  = 1;

    foreach ($query->result() as $rows) {

        // =====================================================
        // GET QUOTATION
        // =====================================================

        $quotation = $this->db
            ->select('id')
            ->from('quotation_customer_data')
            ->where('lead_id', $rows->lead_id)
            ->get()
            ->row();

        if (!$quotation) {
            continue;
        }

        // =====================================================
        // GET BOUGHT OUT DATA
        // =====================================================

        $botout_data = $this->db
            ->select("
                qbd.id,
                qphm.name as description,
                qphmo.name as brand_name
            ")
            ->from('quotation_brand_data qbd')

            ->join(
                'quote_parts_heading_master qphm',
                'qphm.id = qbd.head_id',
                'left'
            )

            ->join(
                'quote_parts_heading_master_options qphmo',
                'qphmo.id = qbd.value_id',
                'left'
            )

            ->where('qbd.record_id', $quotation->id)

            ->get();

        foreach ($botout_data->result() as $botout) {

            // =================================================
            // PURCHASE DATA
            // =================================================

            $checkedData = $this->db
                ->select("
                    qba.checked_on,

                    CONCAT(
                        su.first_name,
                        ' ',
                        su.last_name
                    ) as checked_by_name
                ")

                ->from('quotation_brand_action qba')

                ->join(
                    'system_users su',
                    'su.user_id = qba.checked_by',
                    'left'
                )

                ->where(
                    'qba.quotation_brand_id',
                    $botout->id
                )

                ->get();

            $purchase_status = '';

            if ($checkedData->num_rows() > 0) {

                $checkedRow = $checkedData->row();

                $purchase_status =
                    'PURCHASED ON ' .
                    date(
                        'd-m-Y h:i A',
                        strtotime($checkedRow->checked_on)
                    ) .
                    ' BY ' .
                    strtoupper($checkedRow->checked_by_name);
            }

            // =================================================
            // INSERT DATA
            // =================================================

            $sheet->setCellValue('A'.$row, $sr);
            $sheet->setCellValue('B'.$row, strtoupper($rows->df_no));
            $sheet->setCellValue('C'.$row, date('d-m-Y', strtotime($rows->podate)));
            $sheet->setCellValue('D'.$row, strtoupper($rows->dfowner));
            $sheet->setCellValue('E'.$row, date('d-m-Y', strtotime($rows->added_on)));

            $sheet->setCellValue(
                'F'.$row,
                !empty($rows->df_start_date)
                ? date('d-m-Y', strtotime($rows->df_start_date))
                : ''
            );

            $sheet->setCellValue(
                'G'.$row,
                !empty($rows->projected_completion_date)
                ? date('d-m-Y', strtotime($rows->projected_completion_date))
                : ''
            );

            $sheet->setCellValue(
                'H'.$row,
                strtoupper($botout->description)
            );

            $sheet->setCellValue(
                'I'.$row,
                strtoupper($botout->brand_name)
            );

            $sheet->setCellValue(
                'J'.$row,
                $purchase_status
            );

            // =================================================
            // STYLE
            // =================================================

            $sheet->getStyle('A'.$row.':J'.$row)
                ->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

            $row++;
            $sr++;
        }
    }

    // =========================================================
    // AUTO WIDTH
    // =========================================================

    foreach(range('A','J') as $columnID) {

        $sheet->getColumnDimension($columnID)
              ->setAutoSize(true);
    }

    // =========================================================
    // DOWNLOAD
    // =========================================================

    $filename = 'purchase_forecasting_report.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    header(
        'Content-Disposition: attachment;filename="'.$filename.'"'
    );

    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    $writer->save('php://output');

    exit;
}

}
