<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Accounts extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model','user');
		$this->load->model('Task_model','task');
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
		$this->email->initialize($config);
		$this->email->set_mailtype("html");
		//$this->email->set_newline("\r\n");


		if (!$this->session->userdata('logged_in'))
        { 
            $this->session->set_flashdata('message','Session Logged Out. Login to continue', 'refresh');
            redirect(page_url);
        }

	}

function allrunningdf(){
	$filters = $this->get_allrunningdf_filters();
	$report = $this->build_allrunningdf_report($filters);

	$data = array(
		'filters' => $filters,
		'report' => $report,
		'df_options' => $this->get_allrunningdf_df_options()
	);

	$this->load->view('accounts/allrunningdf', $data);
}

private function get_allrunningdf_filters()
{
	$default_end_date = date('Y-m-d');
	$default_start_date = date('Y-m-d', strtotime($default_end_date . ' -3 months'));

	$start_raw = trim((string) $this->uri->segment(3));
	$end_raw = trim((string) $this->uri->segment(4));
	$df_raw = trim((string) $this->uri->segment(5));

	$use_all_dates = (strtoupper($start_raw) === 'ALL' && strtoupper($end_raw) === 'ALL');
	$start_date = $use_all_dates ? 'ALL' : $this->normalize_allrunningdf_date($start_raw, $default_start_date);
	$end_date = $use_all_dates ? 'ALL' : $this->normalize_allrunningdf_date($end_raw, $default_end_date);

	if (!$use_all_dates && strtotime($start_date) > strtotime($end_date)) {
		$temp_date = $start_date;
		$start_date = $end_date;
		$end_date = $temp_date;
	}

	return array(
		'start_date' => $start_date,
		'end_date' => $end_date,
		'df_id' => ($df_raw === '' ? 'ALL' : $df_raw),
		'has_date_filter' => !$use_all_dates
	);
}

private function normalize_allrunningdf_date($value, $fallback)
{
	$value = trim((string) $value);
	if ($value === '' || strtoupper($value) === 'ALL') {
		return $fallback;
	}

	$timestamp = strtotime($value);
	if ($timestamp === false) {
		return $fallback;
	}

	return date('Y-m-d', $timestamp);
}

private function get_allrunningdf_df_options()
{
	$this->db->select('id, df_no, df_description');
	$this->db->from('df_release');
	$this->db->where('(df_status = 0 OR df_status = "running" OR df_status IS NULL)', null, false);
	$this->db->where('IFNULL(on_hold, 0) = 0', null, false);
	$this->db->order_by('df_no', 'ASC');
	return $this->db->get()->result_array();
}

private function build_allrunningdf_report($filters)
{
	$today = date('Y-m-d');
	$next_seven_days = date('Y-m-d', strtotime($today . ' +7 days'));

	$this->db->select('
		p.id AS po_id,
		p.df_id,
		p.payment_term,
		p.pono,
		p.podate,
		p.order_value,
		p.company_name,
		p.customer_currency,
		p.po_attachment,
		p.amount_in_customer_currency,
		pt.payment_terms,
		df.id AS dfprimaryid,
		df.df_no,
		df.df_description,
		df.added_on AS df_release_on,
		IFNULL(df.on_hold, 0) AS df_on_hold,
		marketing.title AS marketing_title,
		marketing.first_name AS marketing_first_name,
		marketing.last_name AS marketing_last_name
	');
	$this->db->from('poreceived p');
	$this->db->join('df_release df', 'p.df_id = df.id', 'inner');
	$this->db->join('payment_terms pt', 'p.payment_term = pt.id', 'left');
	$this->db->join('system_users marketing', 'marketing.user_id = p.added_by', 'left');
	$this->db->where('(df.df_status = 0 OR df.df_status = "running" OR df.df_status IS NULL)', null, false);
	$this->db->where('IFNULL(df.on_hold, 0) = 0', null, false);

	if (!empty($filters['has_date_filter'])) {
		$this->db->where('DATE(df.added_on) >=', $filters['start_date']);
		$this->db->where('DATE(df.added_on) <=', $filters['end_date']);
	}

	if ((string) $filters['df_id'] !== 'ALL') {
		$this->db->where('df.id', (int) $filters['df_id']);
	}

	$this->db->order_by('df.added_on', 'DESC');
	$this->db->order_by('p.podate', 'DESC');
	$this->db->order_by('p.id', 'DESC');

	$po_rows = $this->db->get()->result_array();

	$report = array(
		'rows' => array(),
			'summary' => array(
				'po_count' => 0,
				'df_count' => 0,
				'total_order_value' => 0,
			'total_order_value_display' => $this->format_allrunningdf_money(0),
			'total_received_amount' => 0,
			'total_received_amount_display' => $this->format_allrunningdf_money(0),
			'total_pending_amount' => 0,
			'total_pending_amount_display' => $this->format_allrunningdf_money(0),
			'total_overdue_amount' => 0,
			'total_overdue_amount_display' => $this->format_allrunningdf_money(0),
			'total_upcoming_amount' => 0,
			'total_upcoming_amount_display' => $this->format_allrunningdf_money(0),
			'collection_percentage' => 0,
				'critical_row_count' => 0,
				'gap_row_count' => 0,
				'overdue_row_count' => 0,
				'partial_row_count' => 0,
				'milestone_count' => 0,
				'overdue_milestone_count' => 0,
				'dispatch_tracker_row_count' => 0,
				'dispatch_tracker_missing_count' => 0,
				'dispatch_tracker_due_count' => 0,
				'dispatch_tracker_hold_count' => 0,
				'dispatch_sync_gap_count' => 0,
				'dispatch_invoice_amount_total' => 0,
				'dispatch_invoice_amount_total_display' => $this->format_allrunningdf_money(0),
				'dispatch_tracker_received_total' => 0,
				'dispatch_tracker_received_total_display' => $this->format_allrunningdf_money(0),
				'dispatch_tracker_balance_total' => 0,
				'dispatch_tracker_balance_total_display' => $this->format_allrunningdf_money(0)
			),
		'insights' => array(),
		'top_risk_rows' => array(),
		'gap_rows' => array(),
		'marketing_summary' => array(),
		'filter_summary' => array(
				'date_range_label' => !empty($filters['has_date_filter'])
					? (date('d M Y', strtotime($filters['start_date'])) . ' to ' . date('d M Y', strtotime($filters['end_date'])))
					: 'All Running DF Timeline',
				'df_label' => 'All Running DFs',
				'generated_on' => date('d M Y h:i A'),
				'date_scope_note' => !empty($filters['has_date_filter'])
					? 'Filter is based on DF release date. Milestone finance intelligence is now paired with the DF-wise dispatch ledger.'
					: 'Showing all running DFs irrespective of release date. Milestone finance intelligence is now paired with the DF-wise dispatch ledger.'
			)
		);

	if (empty($po_rows)) {
		return $report;
	}

	$selected_df_label = 'All Running DFs';
	foreach ($this->get_allrunningdf_df_options() as $df_option) {
		if ((string) $df_option['id'] === (string) $filters['df_id']) {
			$selected_df_label = trim($df_option['df_no'] . ' ' . $df_option['df_description']);
			break;
		}
	}
	$report['filter_summary']['df_label'] = $selected_df_label;

	$po_row_count_by_df = array();
	$df_ids = array();
	$payment_term_ids = array();
	foreach ($po_rows as $po_row) {
		$po_df_key = (int) $po_row['df_id'];
		if (!isset($po_row_count_by_df[$po_df_key])) {
			$po_row_count_by_df[$po_df_key] = 0;
		}
		$po_row_count_by_df[$po_df_key]++;
		$df_ids[(int) $po_row['df_id']] = (int) $po_row['df_id'];
		if (!empty($po_row['payment_term'])) {
			$payment_term_ids[(int) $po_row['payment_term']] = (int) $po_row['payment_term'];
		}
	}

	$dispatch_tracking_map = array();
	if (!empty($df_ids)) {
		$this->db->select('
			tracker.*,
			updater.title AS tracker_updater_title,
			updater.first_name AS tracker_updater_first_name,
			updater.last_name AS tracker_updater_last_name
		');
		$this->db->from('mcs_dispatch_report_tracking tracker');
		$this->db->join('system_users updater', 'updater.user_id = tracker.updated_by', 'left');
		$this->db->where_in('tracker.df_id', array_values($df_ids));
		$this->db->order_by('tracker.id', 'DESC');
		$dispatch_tracking_rows = $this->db->get()->result_array();

		foreach ($dispatch_tracking_rows as $dispatch_tracking_row) {
			$dispatch_df_key = (int) $dispatch_tracking_row['df_id'];
			if (!isset($dispatch_tracking_map[$dispatch_df_key])) {
				$dispatch_tracking_map[$dispatch_df_key] = $dispatch_tracking_row;
			}
		}
	}

	$milestones_by_term = array();
	$milestone_task_ids = array();
	if (!empty($payment_term_ids)) {
		$this->db->select('ptm.id, ptm.payment_term_id, ptm.payment_percentage, ptm.milestone AS milestone_task_id, tm.task_name');
		$this->db->from('payment_terms_milestone ptm');
		$this->db->join('task_management tm', 'tm.task_id = ptm.milestone', 'left');
		$this->db->where_in('ptm.payment_term_id', array_values($payment_term_ids));
		$this->db->order_by('ptm.payment_term_id', 'ASC');
		$this->db->order_by('ptm.id', 'ASC');
		$milestone_rows = $this->db->get()->result_array();

		foreach ($milestone_rows as $milestone_row) {
			$term_id = (int) $milestone_row['payment_term_id'];
			if (!isset($milestones_by_term[$term_id])) {
				$milestones_by_term[$term_id] = array();
			}
			$milestones_by_term[$term_id][] = $milestone_row;
			$milestone_task_ids[(int) $milestone_row['milestone_task_id']] = (int) $milestone_row['milestone_task_id'];
		}
	}

	$task_candidates_by_df = array();
	if (!empty($df_ids) && !empty($milestone_task_ids)) {
		$this->db->select('
			td.id,
			td.po_id,
			td.df_id,
			td.taskid,
			td.task_status,
			td.end_date,
			td.amount_received,
			td.amount_received_date,
			IFNULL(td.paymentstage, 0) AS paymentstage,
			td.amount_received_by,
			receiver.title AS receiver_title,
			receiver.first_name AS receiver_first_name,
			receiver.last_name AS receiver_last_name
		');
		$this->db->from('task_department_wise_scheduling td');
		$this->db->join('system_users receiver', 'receiver.user_id = td.amount_received_by', 'left');
		$this->db->where_in('td.df_id', array_values($df_ids));
		$this->db->where_in('td.taskid', array_values($milestone_task_ids));
		$this->db->where('IFNULL(td.on_hold, 0) = 0', null, false);
		$this->db->order_by('td.id', 'DESC');
		$task_rows = $this->db->get()->result_array();

		foreach ($task_rows as $task_row) {
			$df_key = (int) $task_row['df_id'];
			$task_key = (int) $task_row['taskid'];
			if (!isset($task_candidates_by_df[$df_key])) {
				$task_candidates_by_df[$df_key] = array();
			}
			if (!isset($task_candidates_by_df[$df_key][$task_key])) {
				$task_candidates_by_df[$df_key][$task_key] = array();
			}
			$task_candidates_by_df[$df_key][$task_key][] = $task_row;
		}
	}

	$scheduled_task_ids = array();
	foreach ($task_candidates_by_df as $task_group) {
		foreach ($task_group as $task_rows) {
			foreach ($task_rows as $task_row) {
				$scheduled_task_ids[(int) $task_row['id']] = (int) $task_row['id'];
			}
		}
	}

	$followup_map = $this->get_allrunningdf_followup_map(array_values($scheduled_task_ids));

	$marketing_summary = array();
	$unique_df_tracker = array();
	$dispatch_summary_df_seen = array();
	$rows = array();

	foreach ($po_rows as $po_row) {
		$order_value = (float) $po_row['order_value'];
		$payment_term_id = (int) $po_row['payment_term'];
		$milestone_definitions = isset($milestones_by_term[$payment_term_id]) ? $milestones_by_term[$payment_term_id] : array();

		$row_data = array(
			'po_id' => (int) $po_row['po_id'],
			'df_id' => (int) $po_row['df_id'],
			'company_name' => $this->format_allrunningdf_title_case($po_row['company_name']),
			'customer_currency' => trim((string) $po_row['customer_currency']),
			'df_no' => strtoupper(trim((string) $po_row['df_no'])),
			'df_description' => trim((string) $po_row['df_description']),
			'df_release_date' => $this->format_allrunningdf_date($po_row['df_release_on']),
			'po_no' => trim((string) $po_row['pono']),
			'po_date' => $this->format_allrunningdf_date($po_row['podate']),
			'order_value' => $order_value,
			'order_value_display' => $this->format_allrunningdf_money($order_value),
			'payment_term_name' => trim((string) $po_row['payment_terms']),
			'marketing_person' => $this->format_allrunningdf_person_name($po_row['marketing_title'], $po_row['marketing_first_name'], $po_row['marketing_last_name']),
			'po_download_url' => !empty($po_row['po_attachment']) ? (sfdocument . 'Taskdocument/' . $po_row['po_attachment']) : '',
			'gantt_url' => page_url . 'Task/finalgantchartWithDetails/' . (int) $po_row['df_id'],
			'df_detail_url' => page_url . 'Dashboard/df_full_detail?df_id=' . (int) $po_row['df_id'],
			'milestones' => array(),
			'issues' => array(),
			'configured_percentage_total' => 0,
			'expected_collection_amount' => 0,
			'received_amount' => 0,
			'pending_amount' => 0,
			'overdue_amount' => 0,
			'upcoming_amount' => 0,
			'collection_pending_amount' => 0,
			'partial_amount' => 0,
			'milestone_count' => 0,
			'overdue_milestone_count' => 0,
			'missing_mapping_count' => 0,
				'followup_due_count' => 0,
				'health_key' => 'stable',
				'health_label' => 'Stable',
				'health_priority' => 1,
				'latest_followup_text' => '',
				'latest_followup_on' => '',
				'latest_followup_on_display' => 'No follow-up logged',
				'dispatch_tracker_available' => false,
				'dispatch_nos_of_machines' => 'N/A',
				'dispatch_invoice_no' => '',
				'dispatch_invoice_date' => 'N/A',
				'dispatch_invoice_amount' => 0,
				'dispatch_invoice_amount_display' => $this->format_allrunningdf_money(0),
				'dispatch_taxable_sale' => 0,
				'dispatch_taxable_sale_display' => $this->format_allrunningdf_money(0),
				'dispatch_payment_received' => 0,
				'dispatch_payment_received_display' => $this->format_allrunningdf_money(0),
				'dispatch_balance_amount' => 0,
				'dispatch_balance_amount_display' => $this->format_allrunningdf_money(0),
				'dispatch_payment_due_status_raw' => '',
				'dispatch_payment_due_status' => 'Not Updated',
				'dispatch_due_date' => 'Not Planned',
				'dispatch_due_state_key' => 'not_updated',
				'dispatch_due_state_label' => 'Not Updated',
				'dispatch_remarks' => '',
				'dispatch_commissioning_status' => '',
				'dispatch_inc_date' => 'N/A',
				'dispatch_last_updated_by' => 'Not Updated',
				'dispatch_last_updated_on' => 'Not Updated',
				'dispatch_sync_gap' => false,
				'dispatch_sync_gap_note' => '',
				'dispatch_tracker_note' => 'DF-wise dispatch billing ledger is not updated yet for this row.'
			);
			$row_data['df_po_row_count'] = isset($po_row_count_by_df[(int) $po_row['df_id']]) ? (int) $po_row_count_by_df[(int) $po_row['df_id']] : 1;

			if ($row_data['marketing_person'] === '') {
				$row_data['marketing_person'] = 'Not Mapped';
			}

			$dispatch_tracking_row = isset($dispatch_tracking_map[(int) $po_row['df_id']]) ? $dispatch_tracking_map[(int) $po_row['df_id']] : array();
			if (!empty($dispatch_tracking_row)) {
				$dispatch_due_state = $this->get_allrunningdf_dispatch_due_state(isset($dispatch_tracking_row['payment_due_status']) ? $dispatch_tracking_row['payment_due_status'] : '');
				$dispatch_invoice_amount = isset($dispatch_tracking_row['invoice_amount']) ? (float) $dispatch_tracking_row['invoice_amount'] : 0;
				$dispatch_payment_received = isset($dispatch_tracking_row['payment_received']) ? (float) $dispatch_tracking_row['payment_received'] : 0;
				$dispatch_balance_amount = 0;

				if (isset($dispatch_tracking_row['balance_amount']) && $dispatch_tracking_row['balance_amount'] !== '' && $dispatch_tracking_row['balance_amount'] !== null) {
					$dispatch_balance_amount = (float) $dispatch_tracking_row['balance_amount'];
				} else {
					$dispatch_balance_amount = round(max(0, $dispatch_invoice_amount - $dispatch_payment_received), 2);
				}

				$dispatch_last_updated_by = $this->format_allrunningdf_person_name(
					isset($dispatch_tracking_row['tracker_updater_title']) ? $dispatch_tracking_row['tracker_updater_title'] : '',
					isset($dispatch_tracking_row['tracker_updater_first_name']) ? $dispatch_tracking_row['tracker_updater_first_name'] : '',
					isset($dispatch_tracking_row['tracker_updater_last_name']) ? $dispatch_tracking_row['tracker_updater_last_name'] : ''
				);

				$row_data['dispatch_tracker_available'] = true;
				$row_data['dispatch_nos_of_machines'] = !empty($dispatch_tracking_row['nos_of_machines']) ? $dispatch_tracking_row['nos_of_machines'] : '1';
				$row_data['dispatch_invoice_no'] = trim((string) $dispatch_tracking_row['invoice_no']);
				$row_data['dispatch_invoice_date'] = $this->format_allrunningdf_date(isset($dispatch_tracking_row['invoice_date']) ? $dispatch_tracking_row['invoice_date'] : '');
				$row_data['dispatch_invoice_amount'] = round($dispatch_invoice_amount, 2);
				$row_data['dispatch_invoice_amount_display'] = $this->format_allrunningdf_money($dispatch_invoice_amount);
				$row_data['dispatch_taxable_sale'] = isset($dispatch_tracking_row['taxable_sale']) ? (float) $dispatch_tracking_row['taxable_sale'] : 0;
				$row_data['dispatch_taxable_sale_display'] = $this->format_allrunningdf_money($row_data['dispatch_taxable_sale']);
				$row_data['dispatch_payment_received'] = round($dispatch_payment_received, 2);
				$row_data['dispatch_payment_received_display'] = $this->format_allrunningdf_money($dispatch_payment_received);
				$row_data['dispatch_balance_amount'] = round($dispatch_balance_amount, 2);
				$row_data['dispatch_balance_amount_display'] = $this->format_allrunningdf_money($dispatch_balance_amount);
				$row_data['dispatch_payment_due_status_raw'] = trim((string) $dispatch_tracking_row['payment_due_status']);
				$row_data['dispatch_payment_due_status'] = $dispatch_due_state['label'];
				$row_data['dispatch_due_date'] = $this->format_allrunningdf_date(isset($dispatch_tracking_row['due_date']) ? $dispatch_tracking_row['due_date'] : '');
				$row_data['dispatch_due_state_key'] = $dispatch_due_state['key'];
				$row_data['dispatch_due_state_label'] = $dispatch_due_state['label'];
				$row_data['dispatch_remarks'] = trim((string) $dispatch_tracking_row['remarks']);
				$row_data['dispatch_commissioning_status'] = trim((string) $dispatch_tracking_row['commissioning_status']);
				$row_data['dispatch_inc_date'] = $this->format_allrunningdf_date(isset($dispatch_tracking_row['inc_date']) ? $dispatch_tracking_row['inc_date'] : '');
				$row_data['dispatch_last_updated_by'] = $dispatch_last_updated_by !== '' ? $dispatch_last_updated_by : 'Not Updated';
				$row_data['dispatch_last_updated_on'] = $this->format_allrunningdf_datetime(isset($dispatch_tracking_row['updated_on']) ? $dispatch_tracking_row['updated_on'] : '');
				$row_data['dispatch_tracker_note'] = 'Invoice '
					. ($row_data['dispatch_invoice_no'] !== '' ? $row_data['dispatch_invoice_no'] : 'not yet updated')
					. ' | Machines ' . $row_data['dispatch_nos_of_machines']
					. ' | Due status ' . $row_data['dispatch_due_state_label'] . '.';
				if ($row_data['df_po_row_count'] > 1) {
					$row_data['dispatch_tracker_note'] .= ' This DF appears in ' . $row_data['df_po_row_count'] . ' visible PO rows, so the dispatch ledger is shared across them.';
				}
			}

			if ($payment_term_id === 0 || $row_data['payment_term_name'] === '') {
				$row_data['issues'][] = 'Payment term is not linked with this PO.';
			}

		if (empty($milestone_definitions) && $payment_term_id > 0) {
			$row_data['issues'][] = 'No milestone configuration found for the selected payment term.';
		}

		foreach ($milestone_definitions as $milestone_definition) {
			$milestone_task_id = (int) $milestone_definition['milestone_task_id'];
			$milestone_percentage = (float) $milestone_definition['payment_percentage'];
			$scheduled_amount = round(($order_value * $milestone_percentage) / 100, 2);
			$row_data['configured_percentage_total'] += $milestone_percentage;
			$row_data['expected_collection_amount'] += $scheduled_amount;
			$row_data['milestone_count']++;

			$task_candidates = isset($task_candidates_by_df[(int) $po_row['df_id']][$milestone_task_id])
				? $task_candidates_by_df[(int) $po_row['df_id']][$milestone_task_id]
				: array();
			$matched_task = $this->pick_allrunningdf_task_candidate($task_candidates, (int) $po_row['po_id']);

			$amount_received = 0;
			$outstanding_amount = $scheduled_amount;
			$work_status_key = 'unmapped';
			$work_status_label = 'Not Mapped';
			$payment_status_key = 'gap';
			$payment_status_label = 'Milestone Task Missing';
			$receiver_name = '';
			$record_id = 0;
			$target_date = 'N/A';
			$target_date_raw = '';
			$target_is_overdue = false;
			$target_is_upcoming = false;
			$latest_followup_text = '';
			$latest_followup_date = '';
			$latest_followup_date_display = 'No follow-up logged';
			$next_followup_display = 'Not Planned';
			$next_followup_raw = '';

			if (!empty($matched_task)) {
				$record_id = (int) $matched_task['id'];
				$target_date_raw = trim((string) $matched_task['end_date']);
				$target_date = $this->format_allrunningdf_date($target_date_raw);
				$amount_received = (float) $matched_task['amount_received'];
				$receiver_name = $this->format_allrunningdf_person_name(
					isset($matched_task['receiver_title']) ? $matched_task['receiver_title'] : '',
					isset($matched_task['receiver_first_name']) ? $matched_task['receiver_first_name'] : '',
					isset($matched_task['receiver_last_name']) ? $matched_task['receiver_last_name'] : ''
				);
				$outstanding_amount = round(max(0, $scheduled_amount - $amount_received), 2);
				$task_status = (int) $matched_task['task_status'];

				if ($task_status === 1) {
					$work_status_key = 'completed';
					$work_status_label = 'Completed';
				} elseif ($task_status === 2) {
					$work_status_key = 'approval';
					$work_status_label = 'Waiting Approval';
				} elseif ($target_date_raw !== '' && $target_date_raw !== '0000-00-00' && $target_date_raw < $today) {
					$work_status_key = 'delayed';
					$work_status_label = 'Delayed';
				} else {
					$work_status_key = 'pending';
					$work_status_label = 'Ongoing';
				}

				$target_is_overdue = ($target_date_raw !== '' && $target_date_raw !== '0000-00-00' && $target_date_raw < $today);
				$target_is_upcoming = ($target_date_raw !== '' && $target_date_raw !== '0000-00-00' && $target_date_raw >= $today && $target_date_raw <= $next_seven_days);

				if ($amount_received > 0 && $outstanding_amount > 0) {
					$payment_status_key = $target_is_overdue ? 'partial_overdue' : 'partial';
					$payment_status_label = 'Part Received';
				} elseif ($amount_received > 0) {
					$payment_status_key = 'received';
					$payment_status_label = 'Received';
				} elseif ($task_status === 1) {
					$payment_status_key = 'collection_pending';
					$payment_status_label = 'Collection Pending';
				} elseif ($target_is_overdue) {
					$payment_status_key = 'overdue';
					$payment_status_label = 'Overdue';
				} else {
					$payment_status_key = 'planned';
					$payment_status_label = 'Planned';
				}

				if (isset($followup_map[$record_id])) {
					$latest_followup_text = trim((string) $followup_map[$record_id]['remarks']);
					$latest_followup_date = trim((string) $followup_map[$record_id]['added_on']);
					$latest_followup_date_display = $this->format_allrunningdf_datetime($latest_followup_date);
					$next_followup_raw = trim((string) $followup_map[$record_id]['next_followup']);
					if ($next_followup_raw !== '' && $next_followup_raw !== '0000-00-00') {
						$next_followup_display = $this->format_allrunningdf_date($next_followup_raw);
					}
				}
			} else {
				$row_data['missing_mapping_count']++;
			}

			$row_data['received_amount'] += $amount_received;
			if ($outstanding_amount > 0) {
				if ($target_is_overdue) {
					$row_data['overdue_amount'] += $outstanding_amount;
					$row_data['overdue_milestone_count']++;
				} elseif ($target_is_upcoming) {
					$row_data['upcoming_amount'] += $outstanding_amount;
				}
			}

			if ($amount_received > 0 && $outstanding_amount > 0) {
				$row_data['partial_amount'] += $outstanding_amount;
			}

			if ($work_status_key === 'completed' && $outstanding_amount > 0) {
				$row_data['collection_pending_amount'] += $outstanding_amount;
			}

			if ($outstanding_amount > 0 && $next_followup_raw !== '' && $next_followup_raw !== '0000-00-00' && $next_followup_raw < $today) {
				$row_data['followup_due_count']++;
			}

			if ($latest_followup_date !== '' && ($row_data['latest_followup_on'] === '' || strtotime($latest_followup_date) > strtotime($row_data['latest_followup_on']))) {
				$row_data['latest_followup_on'] = $latest_followup_date;
				$row_data['latest_followup_text'] = $latest_followup_text;
				$row_data['latest_followup_on_display'] = $latest_followup_date_display;
			}

			$row_data['milestones'][] = array(
				'task_name' => trim((string) $milestone_definition['task_name']),
				'payment_percentage' => $milestone_percentage,
				'scheduled_amount' => $scheduled_amount,
				'scheduled_amount_display' => $this->format_allrunningdf_money($scheduled_amount),
				'amount_received' => $amount_received,
				'amount_received_display' => $this->format_allrunningdf_money($amount_received),
				'outstanding_amount' => $outstanding_amount,
				'outstanding_amount_display' => $this->format_allrunningdf_money($outstanding_amount),
				'work_status_key' => $work_status_key,
				'work_status_label' => $work_status_label,
				'payment_status_key' => $payment_status_key,
				'payment_status_label' => $payment_status_label,
				'target_date' => $target_date,
				'target_date_raw' => $target_date_raw,
				'record_id' => $record_id,
				'receiver_name' => $receiver_name !== '' ? $receiver_name : 'Not Updated',
				'payment_received_on' => !empty($matched_task['amount_received_date']) ? $this->format_allrunningdf_date($matched_task['amount_received_date']) : 'Not Updated',
				'followup_text' => $latest_followup_text,
				'followup_logged_on' => $latest_followup_date_display,
				'next_followup' => $next_followup_display,
				'can_update_receipt' => ($record_id > 0 && $outstanding_amount > 0),
				'prefill_receipt_amount' => $amount_received > 0 ? $amount_received : $scheduled_amount,
				'target_receipt_amount' => $scheduled_amount,
				'current_received_amount' => $amount_received
			);
		}

		$row_data['received_amount'] = round($row_data['received_amount'], 2);
		$row_data['pending_amount'] = round(max(0, $order_value - $row_data['received_amount']), 2);
		$row_data['overdue_amount'] = round($row_data['overdue_amount'], 2);
		$row_data['upcoming_amount'] = round($row_data['upcoming_amount'], 2);
		$row_data['collection_pending_amount'] = round($row_data['collection_pending_amount'], 2);
		$row_data['partial_amount'] = round($row_data['partial_amount'], 2);
		$row_data['received_amount_display'] = $this->format_allrunningdf_money($row_data['received_amount']);
		$row_data['pending_amount_display'] = $this->format_allrunningdf_money($row_data['pending_amount']);
		$row_data['overdue_amount_display'] = $this->format_allrunningdf_money($row_data['overdue_amount']);
		$row_data['upcoming_amount_display'] = $this->format_allrunningdf_money($row_data['upcoming_amount']);
			$row_data['collection_pending_amount_display'] = $this->format_allrunningdf_money($row_data['collection_pending_amount']);
			$row_data['collection_percentage'] = $order_value > 0 ? round(($row_data['received_amount'] / $order_value) * 100) : 0;

			if ($row_data['dispatch_tracker_available']) {
				if ($row_data['df_po_row_count'] > 1) {
					$row_data['dispatch_sync_gap_note'] = 'Dispatch ledger is captured DF-wise and this DF is visible across ' . $row_data['df_po_row_count'] . ' PO rows, so reconciliation should be read at the DF total level.';
				} else {
					$dispatch_gap_amount = round(abs($row_data['dispatch_payment_received'] - $row_data['received_amount']), 2);
					$row_data['dispatch_sync_gap'] = ($dispatch_gap_amount > 0.01);
					$row_data['dispatch_sync_gap_note'] = $row_data['dispatch_sync_gap']
						? ('DF-wise dispatch ledger shows ' . $row_data['dispatch_payment_received_display'] . ' received while finance milestone capture shows ' . $row_data['received_amount_display'] . '.')
						: 'DF-wise dispatch ledger and finance milestone capture are aligned.';

					if ($row_data['dispatch_sync_gap']) {
						$row_data['issues'][] = $row_data['dispatch_sync_gap_note'];
					}
				}

				if ($row_data['dispatch_payment_due_status_raw'] !== '' && strtolower($row_data['dispatch_payment_due_status_raw']) === 'due' && $row_data['dispatch_due_date'] === 'N/A') {
					$row_data['issues'][] = 'Dispatch ledger marks this row as Due but no due date is captured there.';
				}

				if ($row_data['dispatch_invoice_no'] !== '' && $row_data['dispatch_invoice_amount'] <= 0) {
					$row_data['issues'][] = 'Dispatch ledger has an invoice number but invoice amount is not updated.';
				}
			}

			if ($row_data['payment_term_name'] !== '' && abs($row_data['configured_percentage_total'] - 100) > 0.01) {
				$row_data['issues'][] = 'Milestone percentage total is ' . rtrim(rtrim(number_format($row_data['configured_percentage_total'], 2, '.', ''), '0'), '.') . '%, not 100%.';
			}

		if ($row_data['missing_mapping_count'] > 0) {
			$row_data['issues'][] = $row_data['missing_mapping_count'] . ' milestone task(s) are not mapped against the scheduled DF work.';
		}

		if ($row_data['overdue_amount'] > 0) {
			$row_data['issues'][] = 'Overdue collection exposure is ' . $this->format_allrunningdf_money($row_data['overdue_amount']) . '.';
		}

		if ($row_data['followup_due_count'] > 0) {
			$row_data['issues'][] = $row_data['followup_due_count'] . ' milestone(s) have a next follow-up date already crossed.';
		}

		if ($row_data['latest_followup_text'] === '' && $row_data['overdue_amount'] > 0) {
			$row_data['issues'][] = 'No finance follow-up note is logged for the overdue exposure.';
		}

		if ($row_data['received_amount'] > ($order_value + 0.01)) {
			$row_data['issues'][] = 'Received amount is greater than order value. Please verify the receipt entries.';
		}

		if ($row_data['payment_term_name'] === '' || empty($milestone_definitions) || $row_data['missing_mapping_count'] > 0) {
			$row_data['health_key'] = 'gap';
			$row_data['health_label'] = 'Configuration Gap';
			$row_data['health_priority'] = 4;
		} elseif ($row_data['overdue_amount'] > 0) {
			$row_data['health_key'] = 'critical';
			$row_data['health_label'] = 'Overdue';
			$row_data['health_priority'] = 3;
		} elseif ($row_data['pending_amount'] <= 0.01) {
			$row_data['health_key'] = 'collected';
			$row_data['health_label'] = 'Collected';
			$row_data['health_priority'] = 0;
		} elseif ($row_data['upcoming_amount'] > 0 || $row_data['partial_amount'] > 0 || $row_data['collection_pending_amount'] > 0) {
			$row_data['health_key'] = 'watch';
			$row_data['health_label'] = 'Watch';
			$row_data['health_priority'] = 2;
		} else {
			$row_data['health_key'] = 'active';
			$row_data['health_label'] = 'Active';
			$row_data['health_priority'] = 1;
		}

		if ($row_data['latest_followup_text'] === '') {
			$row_data['latest_followup_on_display'] = 'No follow-up logged';
		}

		$rows[] = $row_data;
		$unique_df_tracker[(int) $po_row['df_id']] = true;

		$report['summary']['po_count']++;
		$report['summary']['total_order_value'] += $order_value;
		$report['summary']['total_received_amount'] += $row_data['received_amount'];
			$report['summary']['total_pending_amount'] += $row_data['pending_amount'];
			$report['summary']['total_overdue_amount'] += $row_data['overdue_amount'];
			$report['summary']['total_upcoming_amount'] += $row_data['upcoming_amount'];
			$report['summary']['milestone_count'] += $row_data['milestone_count'];
			$report['summary']['overdue_milestone_count'] += $row_data['overdue_milestone_count'];
			if (!isset($dispatch_summary_df_seen[(int) $po_row['df_id']])) {
				$dispatch_summary_df_seen[(int) $po_row['df_id']] = true;
				if ($row_data['dispatch_tracker_available']) {
					$report['summary']['dispatch_tracker_row_count']++;
					$report['summary']['dispatch_invoice_amount_total'] += $row_data['dispatch_invoice_amount'];
					$report['summary']['dispatch_tracker_received_total'] += $row_data['dispatch_payment_received'];
					$report['summary']['dispatch_tracker_balance_total'] += $row_data['dispatch_balance_amount'];

					if ($row_data['dispatch_due_state_key'] === 'due') {
						$report['summary']['dispatch_tracker_due_count']++;
					}

					if ($row_data['dispatch_due_state_key'] === 'hold') {
						$report['summary']['dispatch_tracker_hold_count']++;
					}

					if ($row_data['dispatch_sync_gap']) {
						$report['summary']['dispatch_sync_gap_count']++;
					}
				} else {
					$report['summary']['dispatch_tracker_missing_count']++;
				}
			}

		if ($row_data['health_priority'] >= 3) {
			$report['summary']['critical_row_count']++;
		}
		if (!empty($row_data['issues'])) {
			$report['summary']['gap_row_count']++;
		}
		if ($row_data['overdue_amount'] > 0) {
			$report['summary']['overdue_row_count']++;
		}
		if ($row_data['partial_amount'] > 0) {
			$report['summary']['partial_row_count']++;
		}

		$marketing_key = $row_data['marketing_person'];
		if (!isset($marketing_summary[$marketing_key])) {
			$marketing_summary[$marketing_key] = array(
				'label' => $marketing_key,
				'df_count' => 0,
				'order_value' => 0,
				'received_amount' => 0,
				'pending_amount' => 0,
				'overdue_amount' => 0
			);
		}
		$marketing_summary[$marketing_key]['df_count']++;
		$marketing_summary[$marketing_key]['order_value'] += $order_value;
		$marketing_summary[$marketing_key]['received_amount'] += $row_data['received_amount'];
		$marketing_summary[$marketing_key]['pending_amount'] += $row_data['pending_amount'];
		$marketing_summary[$marketing_key]['overdue_amount'] += $row_data['overdue_amount'];
	}

	$report['summary']['df_count'] = count($unique_df_tracker);
	$report['summary']['collection_percentage'] = $report['summary']['total_order_value'] > 0
		? round(($report['summary']['total_received_amount'] / $report['summary']['total_order_value']) * 100)
		: 0;
	$report['summary']['total_order_value'] = round($report['summary']['total_order_value'], 2);
	$report['summary']['total_received_amount'] = round($report['summary']['total_received_amount'], 2);
		$report['summary']['total_pending_amount'] = round($report['summary']['total_pending_amount'], 2);
		$report['summary']['total_overdue_amount'] = round($report['summary']['total_overdue_amount'], 2);
		$report['summary']['total_upcoming_amount'] = round($report['summary']['total_upcoming_amount'], 2);
		$report['summary']['dispatch_invoice_amount_total'] = round($report['summary']['dispatch_invoice_amount_total'], 2);
		$report['summary']['dispatch_tracker_received_total'] = round($report['summary']['dispatch_tracker_received_total'], 2);
		$report['summary']['dispatch_tracker_balance_total'] = round($report['summary']['dispatch_tracker_balance_total'], 2);
		$report['summary']['total_order_value_display'] = $this->format_allrunningdf_money($report['summary']['total_order_value']);
		$report['summary']['total_received_amount_display'] = $this->format_allrunningdf_money($report['summary']['total_received_amount']);
		$report['summary']['total_pending_amount_display'] = $this->format_allrunningdf_money($report['summary']['total_pending_amount']);
		$report['summary']['total_overdue_amount_display'] = $this->format_allrunningdf_money($report['summary']['total_overdue_amount']);
		$report['summary']['total_upcoming_amount_display'] = $this->format_allrunningdf_money($report['summary']['total_upcoming_amount']);
		$report['summary']['dispatch_invoice_amount_total_display'] = $this->format_allrunningdf_money($report['summary']['dispatch_invoice_amount_total']);
		$report['summary']['dispatch_tracker_received_total_display'] = $this->format_allrunningdf_money($report['summary']['dispatch_tracker_received_total']);
		$report['summary']['dispatch_tracker_balance_total_display'] = $this->format_allrunningdf_money($report['summary']['dispatch_tracker_balance_total']);

	usort($rows, function($left, $right) {
		if ((int) $left['health_priority'] === (int) $right['health_priority']) {
			if ((float) $left['overdue_amount'] === (float) $right['overdue_amount']) {
				if ((float) $left['pending_amount'] === (float) $right['pending_amount']) {
					return strcmp((string) $left['df_no'], (string) $right['df_no']);
				}
				return ((float) $left['pending_amount'] > (float) $right['pending_amount']) ? -1 : 1;
			}
			return ((float) $left['overdue_amount'] > (float) $right['overdue_amount']) ? -1 : 1;
		}
		return ((int) $left['health_priority'] > (int) $right['health_priority']) ? -1 : 1;
	});

	$report['rows'] = $rows;
	$report['top_risk_rows'] = array_slice($rows, 0, 5);

	$gap_rows = array();
	foreach ($rows as $row_data) {
		if (!empty($row_data['issues'])) {
			$gap_rows[] = $row_data;
		}
	}
	$report['gap_rows'] = array_slice($gap_rows, 0, 5);

	foreach ($marketing_summary as $marketing_key => $marketing_row) {
		$marketing_summary[$marketing_key]['order_value_display'] = $this->format_allrunningdf_money($marketing_row['order_value']);
		$marketing_summary[$marketing_key]['received_amount_display'] = $this->format_allrunningdf_money($marketing_row['received_amount']);
		$marketing_summary[$marketing_key]['pending_amount_display'] = $this->format_allrunningdf_money($marketing_row['pending_amount']);
		$marketing_summary[$marketing_key]['overdue_amount_display'] = $this->format_allrunningdf_money($marketing_row['overdue_amount']);
	}

	$marketing_summary = array_values($marketing_summary);
	usort($marketing_summary, function($left, $right) {
		if ((float) $left['overdue_amount'] === (float) $right['overdue_amount']) {
			if ((float) $left['pending_amount'] === (float) $right['pending_amount']) {
				return strcmp((string) $left['label'], (string) $right['label']);
			}
			return ((float) $left['pending_amount'] > (float) $right['pending_amount']) ? -1 : 1;
		}
		return ((float) $left['overdue_amount'] > (float) $right['overdue_amount']) ? -1 : 1;
	});
	$report['marketing_summary'] = array_slice($marketing_summary, 0, 6);

	if (!empty($report['top_risk_rows'][0]) && $report['top_risk_rows'][0]['overdue_amount'] > 0) {
		$report['insights'][] = $report['top_risk_rows'][0]['df_no'] . ' carries the highest overdue exposure at ' . $report['top_risk_rows'][0]['overdue_amount_display'] . '.';
	}

	if ($report['summary']['gap_row_count'] > 0) {
		$report['insights'][] = $report['summary']['gap_row_count'] . ' row(s) have payment-term or milestone-mapping configuration gaps that need correction.';
	}

	if ($report['summary']['partial_row_count'] > 0) {
		$report['insights'][] = $report['summary']['partial_row_count'] . ' row(s) already have partial payment captured but still carry a balance to be followed up.';
	}

		if ($report['summary']['total_upcoming_amount'] > 0) {
			$report['insights'][] = 'Upcoming milestone pressure in the next 7 days is ' . $report['summary']['total_upcoming_amount_display'] . '.';
		}

		if ($report['summary']['dispatch_tracker_row_count'] > 0) {
			$report['insights'][] = $report['summary']['dispatch_tracker_row_count'] . ' DF(s) already carry DF-wise dispatch ledger data, with billed balance exposure of ' . $report['summary']['dispatch_tracker_balance_total_display'] . '.';
		}

		if ($report['summary']['dispatch_sync_gap_count'] > 0) {
			$report['insights'][] = $report['summary']['dispatch_sync_gap_count'] . ' DF(s) show mismatch between the DF-wise dispatch ledger and finance milestone receipts. These need reconciliation.';
		}

		if (empty($report['insights'])) {
			$report['insights'][] = 'No critical finance signal detected in the current filtered view. This report is stable for routine management review.';
		}

	return $report;
}

private function pick_allrunningdf_task_candidate($candidates, $po_id)
{
	if (empty($candidates)) {
		return array();
	}

	$best_candidate = array();
	$best_score = array(-1, -1, -1);

	foreach ($candidates as $candidate) {
		$candidate_po_id = isset($candidate['po_id']) ? (int) $candidate['po_id'] : 0;
		$po_score = 1;
		if ($po_id > 0 && $candidate_po_id === (int) $po_id) {
			$po_score = 3;
		} elseif ($candidate_po_id === 0) {
			$po_score = 2;
		}

		$paymentstage_score = ((int) $candidate['paymentstage'] === 1) ? 1 : 0;
		$id_score = (int) $candidate['id'];
		$current_score = array($po_score, $paymentstage_score, $id_score);

		if (
			$current_score[0] > $best_score[0] ||
			($current_score[0] === $best_score[0] && $current_score[1] > $best_score[1]) ||
			($current_score[0] === $best_score[0] && $current_score[1] === $best_score[1] && $current_score[2] > $best_score[2])
		) {
			$best_score = $current_score;
			$best_candidate = $candidate;
		}
	}

	return $best_candidate;
}

private function get_allrunningdf_followup_map($record_ids)
{
	$followup_map = array();
	if (empty($record_ids)) {
		return $followup_map;
	}

	$clean_ids = array();
	foreach ($record_ids as $record_id) {
		$clean_ids[] = (int) $record_id;
	}
	$clean_ids = array_unique($clean_ids);

	$query = "
		SELECT
			pf.record_id,
			pf.remarks,
			pf.next_followup,
			pf.added_on,
			u.title,
			u.first_name,
			u.last_name
		FROM payment_followup pf
		INNER JOIN (
			SELECT MAX(id) AS latest_id
			FROM payment_followup
			WHERE record_id IN (" . implode(',', $clean_ids) . ")
			GROUP BY record_id
		) latest_followup ON latest_followup.latest_id = pf.id
		LEFT JOIN system_users u ON u.user_id = pf.added_by
	";

	$followup_rows = $this->db->query($query)->result_array();
	foreach ($followup_rows as $followup_row) {
		$followup_map[(int) $followup_row['record_id']] = $followup_row;
	}

	return $followup_map;
}

private function format_allrunningdf_money($amount)
{
	return '₹' . number_format((float) $amount, 2, '.', ',');
}

private function format_allrunningdf_date($value)
{
	$value = trim((string) $value);
	if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
		return 'N/A';
	}

	$timestamp = strtotime($value);
	if ($timestamp === false) {
		return $value;
	}

	return date('d-m-Y', $timestamp);
}

private function format_allrunningdf_datetime($value)
{
	$value = trim((string) $value);
	if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
		return 'N/A';
	}

	$timestamp = strtotime($value);
	if ($timestamp === false) {
		return $value;
	}

	return date('d-m-Y h:i A', $timestamp);
}

private function format_allrunningdf_title_case($value)
{
	$value = trim((string) $value);
	if ($value === '') {
		return '';
	}

	$value = preg_replace('/\s+/', ' ', $value);
	return ucwords(strtolower($value));
}

private function format_allrunningdf_person_name($title, $first_name, $last_name)
{
	$parts = array();
	foreach (array($title, $first_name, $last_name) as $value) {
		$value = trim((string) $value);
		if ($value !== '') {
			$parts[] = $this->format_allrunningdf_title_case($value);
		}
	}

	return trim(implode(' ', $parts));
}

private function get_allrunningdf_dispatch_due_state($status)
{
	$status = strtolower(trim((string) $status));

	switch ($status) {
		case 'due':
			return array('key' => 'due', 'label' => 'Due');
		case 'not due':
			return array('key' => 'not_due', 'label' => 'Not Due');
		case 'received':
			return array('key' => 'received', 'label' => 'Received');
		case 'hold':
			return array('key' => 'hold', 'label' => 'Hold');
		default:
			return array('key' => 'not_updated', 'label' => 'Not Updated');
	}
}

public function allrunningdflist()
	{
		$i=1;
		$taskdata= array();
		$startdate = $this->uri->segment(3);
		$st = $startdate." 00:00:00";
		$enddate = $this->uri->segment(4);
		$et = $enddate." 23:59:59";
		$dfno = $this->uri->segment(5);
		$this->db->select('a.*, b.payment_terms, c.df_no, c.df_description, c.id as dfprimaryid, d.first_name, d.last_name')->from('poreceived a')->join('payment_terms b','a.payment_term=b.id')->join('df_release c','a.df_id=c.id')->join('system_users d','a.added_by=d.user_id');
		if($startdate<>'' && $startdate=='ALL' && $enddate<>'' && $enddate=='ALL'){

		}else{
		$this->db->where('c.added_on BETWEEN "'.$st. '" and "'.$et.'"');
		}
		if($dfno<>'' && $dfno=='ALL'){

		}else{
			$this->db->where('c.id',$dfno);
		}
		$query = $this->db->get();
		$res = $query->result();
		foreach($res as $row){

				
			$edit = "<a href='".page_url."Task/edit_task/".$row->id."'><i class='fa fa-pencil'></i></a>";	

			$message = "<center><table border='1' style='width:500px; text-align:center'><tr style='background-color:#223010; text-align:center;'><th style='padding:2px 2px 2px 2px; text-align:center; font-weight:bold; color:#fff;'><b>(%)</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>MILESTONE</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>AMOUNT</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>TASK DUE DATE</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>TASK STATUS</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>PAYMENT STATUS</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>FOLLOW UP REMARKS</b></th></tr>";
			   $q = $this->db->select('a.payment_percentage, a.milestone, b.task_name,c.id as scheduledtaskid, c.task_status, c.end_date, c.amount_received, c.amount_received_date, d.first_name, d.last_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->join('task_department_wise_scheduling c','b.task_id=c.taskid','left')->join('system_users d','c.amount_received_by=d.user_id','left')->where('a.payment_term_id',$row->payment_term)->where('c.df_id',$row->dfprimaryid)->get();

			  //echo "<pre>"; print_r($q->result()); exit;
				foreach($q->result() as $row1){
				$receivedmsg = "";	
				$ordervalue = $row->order_value;
				$calculatedvalue = ($ordervalue*$row1->payment_percentage)/100;
				$moneyformat = number_format($calculatedvalue, 2, '.', ','); 
				$partpayment = '₹' . $moneyformat;
				$enddate = date('d-m-Y',strtotime($row1->end_date));
				if($row1->task_status==1){
					$taskstatus = "Done";
					$backgroundcolor = "#DAF7A6;";
					$textcolor = "#000";
					if($row1->amount_received<>'0.00'){
						$totalamotreceived = number_format($row1->amount_received, 2, '.', ','); 
						$tamtreceived = '₹' . $totalamotreceived;
						$reveiveddate = date('d-m-Y',strtotime($row1->amount_received_date));
						$receivedby = $row1->first_name." ".$row1->last_name;
						$receivedmsg = $tamtreceived."<br>".$reveiveddate."<br>".$receivedby;

					}else{
						$receivedmsg = '<span class="btn btn-primary btn-xs" onclick="updateyourprogressremarks('.$row1->scheduledtaskid.','.$calculatedvalue.');">Update Payment Info</span>';
					}
				}else if($row1->task_status==0 && $row1->end_date<date('Y-m-d')){
					$taskstatus = "Delayed";
					$backgroundcolor = "#FF5733;";
					$textcolor = "#fff";
				}else{
					$taskstatus = "Pending to Start";
					$backgroundcolor = "white;";
					$textcolor = "#000";
				}

				$q = $this->db->select('remarks')->from('payment_followup')->where('record_id',$row1->scheduledtaskid)->order_by('id','desc')->limit(1)->get();
				if($q->num_rows()>0){
					foreach($q->result() as $followup);
					$followupremarks = $followup->remarks;
				}else{
					$followupremarks = "";
				}
				$message.='<tr style="background-color:'.$backgroundcolor.' color:'.$textcolor.'">
					<td>'.strtoupper($row1->payment_percentage).'</td>
					<td>'.strtoupper($row1->task_name).'</td>
					<td>'.strtoupper($partpayment).'</td>
					<td>'.$enddate.'</td>
					<td>'.strtoupper($taskstatus).'</td>
					<td>'.strtoupper($receivedmsg).'</td>
					<td>'.$followupremarks.'</td>
				</tr>';
			}
			$message.='</table></center>';
			
			$po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">DOWNLOAD PO</span></a>';

			$formattedInrAmount = number_format($row->order_value, 2, '.', ','); 

			$projectcost = $formattedInrAmount;

			$taskdata[] = array('sr_no'=>$i,
			'company_name'=>strtoupper($row->company_name)."<br><br>".strtoupper($row->customer_currency),
			'dfdetail'=>strtoupper($row->df_no."<br>".$row->df_description),
			'pono'=>strtoupper($row->pono)."<br><br>".date('d-m-Y',strtotime($row->podate))."<br><br>".$po_attachment,
			'order_value'=>$projectcost,
			'podate'=>date('d-m-Y',strtotime($row->podate)),
			'po_attachment'=>$po_attachment,
			'amount_in_customer_currency'=>$row->amount_in_customer_currency,
			'milestone'=>strtoupper($row->payment_terms)."<br>".$message,
			'marketingperson'=>strtoupper($row->first_name." ".$row->last_name),
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

	public function paymentdashboard(){
		//$this->updatepaymentstage();
		$thisweekpayment = $this->upcomingpaymentsthisweek();
		$overduepayments = $this->overduepayments();
		$data['thisweekpayment'] = $thisweekpayment;
		$data['overduepayments'] = $overduepayments;
		$this->load->view('accounts/upcomingpayments',$data);
	}

	public function upcomingpaymentlist()
	{
		$i=1;
		$taskdata= array();
		$startdate = $this->uri->segment(3);
		$st = $startdate." 00:00:00";
		$enddate = $this->uri->segment(4);
		$et = $enddate." 23:59:59";
		$dfno = $this->uri->segment(5);
		
		$this->db->select('a.id as recordid, a.end_date, a.df_id, a.task_status, c.company_name, c.pono, c.podate, c.payment_term, f.payment_terms, c.order_value, d.df_no, c.po_attachment, e.task_name, g.first_name, g.last_name')->from('task_department_wise_scheduling a')->join('poreceived c','a.df_id=c.df_id','left')->join('df_release d','a.df_id=d.id','left')->join('task_management e','a.taskid=e.task_id','left')->join('payment_terms f','c.payment_term=f.id','left')->join('system_users g','a.added_by=g.user_id','left')->where('a.paymentstage',1)->order_by('a.end_date','asc');
			if($startdate<>'' && $startdate=='ALL' && $enddate<>'' && $enddate=='ALL'){

			}else{
			$this->db->where('a.end_date BETWEEN "'.$startdate. '" and "'.$enddate.'"');
			}
			if($dfno<>'' && $dfno=='ALL'){

			}else{
				$this->db->where('a.df_id',$dfno);
			}
		$q = $this->db->get();
		if($q->num_rows()>0){
		
		foreach($q->result() as $row){
			
			
			$po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
				$formattedInrAmount = number_format($row->order_value, 2, '.', ','); 
			$projectcost = '₹' . $formattedInrAmount;
			$q1 = $this->db->select('a.payment_percentage, a.milestone, b.task_name,c.id as scheduledtaskid, c.task_status, c.end_date, c.amount_received, c.amount_received_date, d.first_name, d.last_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->join('task_department_wise_scheduling c','b.task_id=c.taskid')->join('system_users d','c.amount_received_by=d.user_id','left')->where('a.payment_term_id',$row->payment_term)->where('c.id',$row->recordid)->get();
			if($q1->num_rows()>0){
				foreach($q1->result() as $row1);
				$receivedmsg = "";	
				$ordervalue = $row->order_value;
				$calculatedvalue = ($ordervalue*$row1->payment_percentage)/100;
				$moneyformat = number_format($calculatedvalue, 2, '.', ','); 
				$partpayment = '₹' . $moneyformat;
				$enddate = date('d-m-Y',strtotime($row1->end_date));
				if($row1->task_status==1){
					$taskstatus = "Done";
					$backgroundcolor = "green;";
					$textcolor = "#fff";
					if($row1->amount_received<>'0.00'){
						$taskstatus = "Done";
						$totalamotreceived = number_format($row1->amount_received, 2, '.', ','); 
						$tamtreceived = '₹' . $totalamotreceived;
						$reveiveddate = date('d-m-Y',strtotime($row1->amount_received_date));
						$receivedby = $row1->first_name." ".$row1->last_name;

						$receivedmsg = $tamtreceived."<br>".$reveiveddate."<br>".$receivedby;

					}else{
						$taskstatus ="Payment Collection Pending";
						$receivedmsg = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row1->scheduledtaskid.','.$calculatedvalue.');">Update Payment Info</span>';
					}
					}else if($row1->task_status==0 && $row1->end_date<date('Y-m-d')){
					$taskstatus = "Delayed";
					$backgroundcolor = "red;";
					$textcolor = "#fff";
					}else{
					$taskstatus = "Pending to Start";
					$backgroundcolor = "white;";
					$textcolor = "#000";
					}
					$payment_percentages = $row1->payment_percentage;
				}else{
					$partpayment = '';
					$receivedmsg = '';
					$payment_percentages = '';
					$taskstatus = '';
				}
			
			
				$taskdata[] = array('sr_no'=>$i,
				'company_name'=>strtoupper($row->company_name),
				'pono'=>strtoupper($row->pono),
				'podate'=>date('d-m-Y',strtotime($row->podate)),
				'df_no'=>strtoupper($row->df_no),
				'poattachment'=>$po_attachment,
				'task_name'=>strtoupper($row->task_name),
				'projectcost'=>$projectcost,
				'payment_percentage'=>$payment_percentages,
				'amount'=>$partpayment,
				'message'=>$receivedmsg,
				'marketingperson'=>strtoupper($row->first_name." ".$row->last_name),
				'workcompletiondate'=>date('d-m-Y',strtotime($row->end_date)),
				'workstatus'=>strtoupper($taskstatus));
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

	function updatepaymentstage(){


		$q = $this->db->select('a.id, a.df_id, a.id, a.payment_term')->from('poreceived a')->join('task_department_wise_scheduling b','a.id=b.po_id')->join('df_release c','a.df_id=c.id')->where('c.df_status',0)->get();
		foreach($q->result() as $row){

			$q1 = $this->db->select('milestone')->from('payment_terms_milestone')->where('payment_term_id',$row->payment_term)->get();
			foreach($q1->result() as $row1){

				$taskid = $row1->milestone;
				$q2 = $this->db->select('id')->from('task_department_wise_scheduling')->where('taskid',$taskid)->where('df_id',$row->df_id)->where('po_id',$row->id)->get();
				if($q2->num_rows()>0){
					foreach($q2->result() as $row2){
					$data = array('paymentstage'=>1);
					$this->db->where('id',$row2->id);
					$this->db->update('task_department_wise_scheduling',$data);
					}
				}

			}

		}

	}

	public function updatepaymentdetail(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('amountreceived', 'Amount Received', 'required|trim|numeric');
		$this->form_validation->set_rules('paymentreceivedate', 'Payment Received Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$redirect_url = page_url.'Accounts/allrunningdf/'.$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."/";

		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Please enter a valid received amount and payment date.</div>');
			redirect($redirect_url);
		}
		else
		{
		if ((float) $this->input->post('amountreceived') < 0) {
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Received amount cannot be negative.</div>');
			redirect($redirect_url);
		}

		date_default_timezone_set("Asia/Kolkata");
		$data = array('amount_received'=>(float) $this->input->post('amountreceived'),
			'amount_received_date'=>date('Y-m-d',strtotime($this->input->post('paymentreceivedate'))),
			'amount_added_in_record_time'=>date('Y-m-d H:i:s'),
			'amount_received_by'=>$user_id);

		$this->db->where('id',$this->input->post('taskkiid'));
		$this->db->update('task_department_wise_scheduling',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect($redirect_url);

		}
	}

		public function updatepaymentdetailfrompaymentdashboard(){
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('amountreceived', 'Amount Received', 'required|trim|numeric');
		$this->form_validation->set_rules('paymentreceivedate', 'Payment Received Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];
		$redirect_url = page_url.'Accounts/paymentdashboard/'.$this->uri->segment(3)."/".$this->uri->segment(4)."/".$this->uri->segment(5)."/";

		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Please enter a valid received amount and payment date.</div>');
			redirect($redirect_url);
		}
		else
		{
		if ((float) $this->input->post('amountreceived') < 0) {
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Received amount cannot be negative.</div>');
			redirect($redirect_url);
		}

		date_default_timezone_set("Asia/Kolkata");
		$data = array('amount_received'=>(float) $this->input->post('amountreceived'),
			'amount_received_date'=>date('Y-m-d',strtotime($this->input->post('paymentreceivedate'))),
			'amount_added_in_record_time'=>date('Y-m-d H:i:s'),
			'amount_received_by'=>$user_id);

		$this->db->where('id',$this->input->post('taskkiid'));
		$this->db->update('task_department_wise_scheduling',$data);

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect($redirect_url);

		}
	}

	public function filterbydate(){
		$startdate = trim((string) $this->input->post('start_date'));
		$enddate = trim((string) $this->input->post('end_date'));
		$dfno = trim((string) $this->input->post('df_no'));

		if ($dfno === '') {
			$dfno = 'ALL';
		}

		if ($startdate === '' && $enddate === '') {
			redirect(page_url.'Accounts/allrunningdf/ALL/ALL/'.$dfno);
		}

		if ($startdate === '') {
			$startdate = $enddate;
		}

		if ($enddate === '') {
			$enddate = $startdate;
		}

		$startdate = date('Y-m-d',strtotime($startdate));
		$enddate = date('Y-m-d',strtotime($enddate));

		if (strtotime($startdate) > strtotime($enddate)) {
			$tempdate = $startdate;
			$startdate = $enddate;
			$enddate = $tempdate;
		}

		redirect(page_url.'Accounts/allrunningdf/'.$startdate."/".$enddate."/".$dfno);

	}

	public function filterbydateanddf(){
		$startdate = date('Y-m-d',strtotime($this->input->post('start_date')));
		$enddate = date('Y-m-d',strtotime($this->input->post('end_date')));
		$dfno = $this->input->post('df_no');
		redirect(page_url.'Accounts/paymentdashboard/'.$startdate."/".$enddate."/".$dfno);

	}

	public function upcomingpaymentsthisweek(){
		$calculatedvalue[] = array();
		$calculatedvalue[] = 0;
		$startdate = date('Y-m-d');
		$currentDate = new DateTime();
		$currentDate->add(new DateInterval('P7D'));
		$endate = $currentDate->format('Y-m-d');
		$this->db->select('a.id as recordid, a.end_date, a.df_id, a.task_status, c.company_name, c.pono, c.podate, c.payment_term, f.payment_terms, c.order_value, d.df_no, c.po_attachment, e.task_name')->from('task_department_wise_scheduling a')->join('poreceived c','a.df_id=c.df_id')->join('df_release d','a.df_id=d.id')->join('task_management e','a.taskid=e.task_id')->join('payment_terms f','c.payment_term=f.id')->where('a.paymentstage',1)->order_by('a.end_date','asc');
			$this->db->where('a.end_date BETWEEN "'.$startdate. '" and "'.$endate.'"');
			$q = $this->db->get();
			if($q->num_rows()>0){
			foreach($q->result() as $row){
				 $q = $this->db->select('a.payment_percentage, a.milestone, b.task_name,c.id as scheduledtaskid, c.task_status, c.end_date, c.amount_received, c.amount_received_date, d.first_name, d.last_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->join('task_department_wise_scheduling c','b.task_id=c.taskid')->join('system_users d','c.amount_received_by=d.user_id','left')->where('a.payment_term_id',$row->payment_term)->where('c.id',$row->recordid)->get();

				 	foreach($q->result() as $row1);
				$receivedmsg = "";	
				$ordervalue = $row->order_value;
				$calculatedvalue[] = ($ordervalue*$row1->payment_percentage)/100;

			}
		}	
				$sumtotal = array_sum($calculatedvalue);
				$moneyformat = number_format($sumtotal, 2, '.', ','); 
				$partpayment = '₹' . $moneyformat;
				return $partpayment;
	}

		public function overduepayments(){
		$calculatedvalue[] = array();
		$calculatedvalue[] = 0;
		$this->db->select('a.id as recordid, a.end_date, a.df_id, a.task_status, c.company_name, c.pono, c.podate, c.payment_term, f.payment_terms, c.order_value, d.df_no, c.po_attachment, e.task_name')->from('task_department_wise_scheduling a')->join('poreceived c','a.df_id=c.df_id')->join('df_release d','a.df_id=d.id')->join('task_management e','a.taskid=e.task_id')->join('payment_terms f','c.payment_term=f.id')->where('a.paymentstage',1)->order_by('a.end_date','asc')->where('a.task_status',1)->where('a.end_date<=',date('Y-m-d'));
			
			$q = $this->db->get();
				if($q->num_rows()>0){
			foreach($q->result() as $row){
				 $q1 = $this->db->select('a.payment_percentage, a.milestone, b.task_name,c.id as scheduledtaskid, c.task_status, c.end_date, c.amount_received, c.amount_received_date, d.first_name, d.last_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->join('task_department_wise_scheduling c','b.task_id=c.taskid')->join('system_users d','c.amount_received_by=d.user_id','left')->where('a.payment_term_id',$row->payment_term)->where('c.id',$row->recordid)->get();
				 if($q1->num_rows()>0){
				 	foreach($q1->result() as $row1);
				$receivedmsg = "";	
				$ordervalue = $row->order_value;
				$calculatedvalue[] = ($ordervalue*$row1->payment_percentage)/100;
				}
			}
		}	
				$sumtotal = array_sum($calculatedvalue);
				$moneyformat = number_format($sumtotal, 2, '.', ','); 
				$partpayment = '₹' . $moneyformat;
				return $partpayment;
	}

	public function overduepaymentdashboard(){
		$this->load->view('accounts/overduepayments');
	}
	public function overduepaymentslist()
	{
		$i=1;
		$taskdata= array();
		$startdate = $this->uri->segment(3);
		$st = $startdate." 00:00:00";
		$enddate = $this->uri->segment(4);
		$et = $enddate." 23:59:59";
		$dfno = $this->uri->segment(5);
		
		$this->db->select('a.id as recordid, a.end_date, a.df_id, a.task_status, c.company_name, c.pono, c.podate, c.payment_term, f.payment_terms, c.order_value, d.df_no, c.po_attachment, e.task_name, g.first_name, g.last_name')->from('task_department_wise_scheduling a')->join('poreceived c','a.df_id=c.df_id')->join('df_release d','a.df_id=d.id')->join('task_management e','a.taskid=e.task_id')->join('payment_terms f','c.payment_term=f.id')->join('system_users g','a.added_by=g.user_id')->where('a.paymentstage',1)->where('task_status',1)->where('a.amount_received','0.00')->order_by('a.end_date','asc');
			
		$q = $this->db->get();
		if($q->num_rows()>0){
		
		foreach($q->result() as $row){
			
			
			$po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">Click to download PO</span></a>';
				$formattedInrAmount = number_format($row->order_value, 2, '.', ','); 
			$projectcost = '₹' . $formattedInrAmount;
			$q = $this->db->select('a.payment_percentage, a.milestone, b.task_name,c.id as scheduledtaskid, c.task_status, c.end_date, c.amount_received, c.amount_received_date, d.first_name, d.last_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->join('task_department_wise_scheduling c','b.task_id=c.taskid')->join('system_users d','c.amount_received_by=d.user_id','left')->where('a.payment_term_id',$row->payment_term)->where('c.id',$row->recordid)->get();
				foreach($q->result() as $row1);
				$receivedmsg = "";	
				$ordervalue = $row->order_value;
				$calculatedvalue = ($ordervalue*$row1->payment_percentage)/100;
				$moneyformat = number_format($calculatedvalue, 2, '.', ','); 
				$partpayment = '₹' . $moneyformat;
				$enddate = date('d-m-Y',strtotime($row1->end_date));
				if($row1->task_status==1){
					$taskstatus = "Done";
					$backgroundcolor = "green;";
					$textcolor = "#fff";
					if($row1->amount_received<>'0.00'){
						$taskstatus = "Done";
						$totalamotreceived = number_format($row1->amount_received, 2, '.', ','); 
						$tamtreceived = '₹' . $totalamotreceived;
						$reveiveddate = date('d-m-Y',strtotime($row1->amount_received_date));
						$receivedby = $row1->first_name." ".$row1->last_name;

						$receivedmsg = $tamtreceived."<br>".$reveiveddate."<br>".$receivedby;

					}else{
						$taskstatus ="Payment Collection Pending";
						$receivedmsg = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row1->scheduledtaskid.','.$calculatedvalue.');">Update Payment Info</span>';
					}
					}else if($row1->task_status==0 && $row1->end_date<date('Y-m-d')){
					$taskstatus = "Delayed";
					$backgroundcolor = "red;";
					$textcolor = "#fff";
					}else{
					$taskstatus = "Pending to Start";
					$backgroundcolor = "white;";
					$textcolor = "#000";
					}
				
			
			
				$taskdata[] = array('sr_no'=>$i,
				'company_name'=>strtoupper($row->company_name),
				'pono'=>strtoupper($row->pono),
				'podate'=>date('d-m-Y',strtotime($row->podate)),
				'df_no'=>strtoupper($row->df_no),
				'poattachment'=>$po_attachment,
				'task_name'=>strtoupper($row->task_name),
				'projectcost'=>$projectcost,
				'payment_percentage'=>$row1->payment_percentage,
				'amount'=>$partpayment,
				'message'=>$receivedmsg,
				'marketingperson'=>strtoupper($row->first_name." ".$row->last_name),
				'workcompletiondate'=>date('d-m-Y',strtotime($row->end_date)),
				'workstatus'=>strtoupper($taskstatus));
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

	public function marketingpaymentdashboard(){
		
		$thisweekpayment = $this->upcomingpaymentsthisweek();
		$overduepayments = $this->overduepayments();
		$data['thisweekpayment'] = $thisweekpayment;
		$data['overduepayments'] = $overduepayments;
		$this->load->view('accounts/marketingpaymentdashboard',$data);
	}

	public function filterbymarketingperson(){
		$marketingperson = $this->input->post('marketingperson');
		$id = base64_encode($marketingperson);
		redirect(page_url."Accounts/marketingpaymentdashboard/".$id);
	}

	public function marketingpaymentfollowuplist()
	{
		$i=1;
		$departmentid =$this->session->userdata['logged_in']['department_id'];	
		$userid =$this->session->userdata['logged_in']['user_id'];
		$userrole =$this->session->userdata['logged_in']['role'];
		$selecteduser = $this->uri->segment(3);
		$taskdata= array();
		$this->db->select('a.*, b.payment_terms, c.df_no, c.df_description, c.id as dfprimaryid, d.first_name, d.last_name')->from('poreceived a')->join('payment_terms b','a.payment_term=b.id')->join('df_release c','a.df_id=c.id')->join('system_users d','a.added_by=d.user_id');
		if($departmentid==9){
			$this->db->where('a.added_by',$userid);
		}
		if($selecteduser<>'' && $userrole<>12){
			$this->db->where('a.added_by',$selecteduser);
		}
		$query = $this->db->get();

		$res = $query->result();
		foreach($res as $row){

				
			$edit = "<a href='".page_url."Task/edit_task/".$row->id."'><i class='fa fa-pencil'></i></a>";	

			$message = "<center><table border='1' style='width:600px !important; text-align:center'><tr style='background-color:#223010; text-align:center;'><th style='padding:2px 2px 2px 2px; text-align:center; font-weight:bold; color:#fff;'><b>(%)</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>MILESTONE</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>AMOUNT</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>TASK DUE DATE</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>TASK STATUS</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>PAYMENT STATUS</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>FOLLOW UP REMARKS</b></th><th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>NEXT FOLLOW UP</b></th></tr>";
			   $q = $this->db->select('a.payment_percentage, a.milestone, b.task_name,c.id as scheduledtaskid, c.task_status, c.end_date, c.amount_received, c.amount_received_date, d.first_name, d.last_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->join('task_department_wise_scheduling c','b.task_id=c.taskid')->join('system_users d','c.amount_received_by=d.user_id','left')->where('a.payment_term_id',$row->payment_term)->where('c.df_id',$row->dfprimaryid)->get();
				foreach($q->result() as $row1){
				$receivedmsg = "";	
				$ordervalue = $row->order_value;
				$calculatedvalue = ($ordervalue*$row1->payment_percentage)/100;
				$moneyformat = number_format($calculatedvalue, 2, '.', ','); 
				$partpayment = '₹' . $moneyformat;
				$enddate = date('d-m-Y',strtotime($row1->end_date));
				if($row1->task_status==1){
					$taskstatus = "Done";
					$backgroundcolor = "#DAF7A6;";
					$textcolor = "#000";
					if($row1->amount_received<>'0.00'){
						$totalamotreceived = number_format($row1->amount_received, 2, '.', ','); 
						$tamtreceived = '₹' . $totalamotreceived;
						$reveiveddate = date('d-m-Y',strtotime($row1->amount_received_date));
						$receivedby = $row1->first_name." ".$row1->last_name;
						$receivedmsg = $tamtreceived."<br>".$reveiveddate."<br>".$receivedby;

					}else{
						$receivedmsg = '';
					}
				}else if($row1->task_status==0 && $row1->end_date<date('Y-m-d')){
					$taskstatus = "Delayed";
					$backgroundcolor = "#FF5733;";
					$textcolor = "#fff";
				}else{
					$taskstatus = "Pending to Start";
					$backgroundcolor = "white;";
					$textcolor = "#000";
				}

				$q = $this->db->select('remarks, next_followup')->from('payment_followup')->where('record_id',$row1->scheduledtaskid)->order_by('id','desc')->limit(1)->get();
				if($q->num_rows()>0){
					foreach($q->result() as $followup);
					if($row->added_by==$userid){
						$followupremarks = $followup->remarks."<br><br>";
						$followupremarks.= '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row1->scheduledtaskid.','.$calculatedvalue.');">Update Payment Info</span>';
						$next_followup = date('d-m-Y',strtotime($followup->next_followup));
				}else{
					$followupremarks = $followup->remarks."<br><br>";
					$next_followup = date('d-m-Y',strtotime($followup->next_followup));
				}
					
				}else{
					$followupremarks = "";
					$next_followup = "";
				}
				$message.='<tr style="background-color:'.$backgroundcolor.' color:'.$textcolor.'">
					<td>'.strtoupper($row1->payment_percentage).'</td>
					<td>'.strtoupper($row1->task_name).'</td>
					<td>'.strtoupper($partpayment).'</td>
					<td>'.$enddate.'</td>
					<td>'.strtoupper($taskstatus).'</td>
					<td>'.strtoupper($receivedmsg).'</td>
					<td>'.$followupremarks.'</td>
					<td>'.$next_followup.'</td>
				</tr>';
			}
			$message.='</table></center>';
			//$message.='<br><a href="'.page_url.'Task/paymentterms/'.$row->id.'"><span class="btn btn-primary btn-xs">Set Milestone</span></a>';

			$po_attachment = '<a href="'.sfdocument.'Taskdocument/'.$row->po_attachment.'" download><span class="btn btn-primary btn-xs">DOWNLOAD PO</span></a>';

			$formattedInrAmount = number_format($row->order_value, 2, '.', ','); 

			$projectcost = '₹' . $formattedInrAmount;

			$taskdata[] = array('sr_no'=>$i,
			'company_name'=>strtoupper($row->company_name)."<br><br>".strtoupper($row->customer_currency),
			'dfdetail'=>strtoupper($row->df_no."<br>".$row->df_description),
			'pono'=>strtoupper($row->pono)."<br><br>".date('d-m-Y',strtotime($row->podate))."<br><br>".$po_attachment,
			'order_value'=>$projectcost,
			'podate'=>date('d-m-Y',strtotime($row->podate)),
			'po_attachment'=>$po_attachment,
			'amount_in_customer_currency'=>$row->amount_in_customer_currency,
			'milestone'=>strtoupper($row->payment_terms)."<br>".$message,
			'marketingperson'=>strtoupper($row->first_name." ".$row->last_name),
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

	public function paymentfollowupremarks(){
		$userid =$this->session->userdata['logged_in']['user_id'];
		$taskid = $this->input->post('taskkiid');
		$nextfollowupdate = date('Y-m-d',strtotime($this->input->post('nextfollowupdate')));
		$remarks = $this->input->post('taskremarks');

		$data = array('record_id'=>$taskid,
			'next_followup'=>$nextfollowupdate,
			'remarks'=>strtoupper($remarks),
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$userid);
		$this->db->insert('payment_followup',$data);
		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
		redirect(page_url."Accounts/marketingpaymentdashboard/".base64_encode($userid));
	}

	public function paymentfollowupreminders(){
		$this->task->paymentfollowupnotification();
	}


public function nextsevendayincome()
{

$html='<style>li span{ font-weight:bold;}</style>';

$this->load->library('Pdf');
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SHUBHAM PACK');
$pdf->SetTitle("NEXT SEVEN DAYS PAYMENT LIST");
$pdf->SetSubject('NEXT SEVEN DAYS PAYMENT LIST');
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
<th colspan="10" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">UPCOMING PAYMENTS IN NEXT 7 DAYS TILL '.$dateformat.'</th>
</tr>
<tr>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:4%"><b>#</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:13%"><b>COMPANY NAME</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>DF DETAIL</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>PO NO/DATE</b></th>

<th style="background-color:lightgrey;text-align:center; font-size:10px; width:22%"><b>TASK NAME</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:8%"><b>TARGET DATE</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:5%"><b>PAYMENT (%)</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>AMOUNT</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:8%"><b>MARKETING PERSON</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>WORK STATUS</b></th>

</tr><tbody>';
$m=1;
$pending = array();
$pending[] = 0;
$this->db->select('a.id as recordid, a.end_date, a.df_id, a.task_status, c.company_name, c.pono, c.podate, c.payment_term, f.payment_terms, c.order_value, d.df_no, c.po_attachment, e.task_name, g.first_name, g.last_name, d.df_description')->from('task_department_wise_scheduling a')->join('poreceived c','a.df_id=c.df_id')->join('df_release d','a.df_id=d.id')->join('task_management e','a.taskid=e.task_id')->join('payment_terms f','c.payment_term=f.id')->join('system_users g','a.added_by=g.user_id')->where('a.paymentstage',1)->order_by('a.end_date','asc');
	$this->db->where('a.end_date BETWEEN "'.$startdate. '" and "'.$enddate.'"');
	$q = $this->db->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){


	$q = $this->db->select('a.payment_percentage, a.milestone, b.task_name,c.id as scheduledtaskid, c.task_status, c.end_date, c.amount_received, c.amount_received_date, d.first_name, d.last_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->join('task_department_wise_scheduling c','b.task_id=c.taskid')->join('system_users d','c.amount_received_by=d.user_id','left')->where('a.payment_term_id',$row->payment_term)->where('c.id',$row->recordid)->get();
	foreach($q->result() as $row1);
	$receivedmsg = "";	
	$ordervalue = $row->order_value;
	$calculatedvalue = ($ordervalue*$row1->payment_percentage)/100;
	$moneyformat = number_format($calculatedvalue, 2, '.', ','); 
	$partpayment =$moneyformat;
	$marketingperson = $row->first_name." ".$row->last_name;

	$enddate = date('d-m-Y',strtotime($row1->end_date));
				if($row1->task_status==1){
					$taskstatus = "Done";
					$backgroundcolor = "green;";
					$textcolor = "#fff";
					if($row1->amount_received<>'0.00'){
						$taskstatus = "Done";
						$totalamotreceived = number_format($row1->amount_received, 2, '.', ','); 
						$tamtreceived = $totalamotreceived;
						$reveiveddate = date('d-m-Y',strtotime($row1->amount_received_date));
						$receivedby = $row1->first_name." ".$row1->last_name;

						$receivedmsg = $tamtreceived."<br>".$reveiveddate."<br>".$receivedby;

					}else{
						$taskstatus ="Payment Collection Pending";
						$receivedmsg = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row1->scheduledtaskid.','.$calculatedvalue.');">Update Payment Info</span>';
					}
					}else if($row1->task_status==0 && $row1->end_date<date('Y-m-d')){
					$taskstatus = "Delayed";
					$backgroundcolor = "red;";
					$textcolor = "#fff";
					}else{
					$taskstatus = "Pending to Start";
					$backgroundcolor = "white;";
					$textcolor = "#000";
					}

$dfdetail = strtoupper($row->df_no)."<br>".strtoupper($row->df_description);
	$pending[] = $calculatedvalue;
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

$total = array_sum($pending);
$gtotal = number_format($total, 2, '.', ','); 
$html.='<tr>
<td colspan="7" style="text-align:center;">TOTAL AMOUNT </td>
	<td colspan="3" style="text-align:center;">INR '.$gtotal.'</td>
</tr></tbody></table><br><br><br>';


//echo $html; exit;


$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
$pdf->Output('next_seven_days_payment_list.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = SITE_ROOT.'image_bank/daily_reports';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL=$filelocation."/Store_purchase_report_".date('Y-m-d').".pdf"; //Linux

 //$pdf->Output($fileNL, 'F');
 $pdf->Output($fileNL, 'I');
 
    
}

public function overduepaymentspdf()
{

$html='<style>li span{ font-weight:bold;}</style>';

$this->load->library('Pdf');
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SHUBHAM PACK');
$pdf->SetTitle("OVERDUE PAYMENT LIST");
$pdf->SetSubject('OVERDUE PAYMENT LIST');
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
<th colspan="10" style="background-color:lightgrey;text-align:center; font-size:15px; font-weight:bold;">OVERDUE PAYMENTS LIST </th>
</tr>
<tr>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:4%"><b>#</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:13%"><b>COMPANY NAME</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>DF DETAIL</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>PO NO/DATE</b></th>

<th style="background-color:lightgrey;text-align:center; font-size:10px; width:22%"><b>TASK NAME</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:8%"><b>TARGET DATE</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:5%"><b>PAYMENT (%)</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>AMOUNT</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:8%"><b>MARKETING PERSON</b></th>
<th style="background-color:lightgrey;text-align:center; font-size:10px; width:10%"><b>WORK STATUS</b></th>

</tr><tbody>';
$m=1;
$pending = array();
$pending[] = 0;
$this->db->select('a.id as recordid, a.end_date, a.df_id, a.task_status, c.company_name, c.pono, c.podate, c.payment_term, f.payment_terms, c.order_value, d.df_no, c.po_attachment, e.task_name, g.first_name, g.last_name, d.df_description')->from('task_department_wise_scheduling a')->join('poreceived c','a.df_id=c.df_id')->join('df_release d','a.df_id=d.id')->join('task_management e','a.taskid=e.task_id')->join('payment_terms f','c.payment_term=f.id')->join('system_users g','a.added_by=g.user_id')->where('a.paymentstage',1)->where('a.amount_received','0.00');
$this->db->where('a.end_date<=',date('Y-m-d'))->order_by('a.end_date','asc');
	$q = $this->db->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row){


	$q = $this->db->select('a.payment_percentage, a.milestone, b.task_name,c.id as scheduledtaskid, c.task_status, c.end_date, c.amount_received, c.amount_received_date, d.first_name, d.last_name')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->join('task_department_wise_scheduling c','b.task_id=c.taskid')->join('system_users d','c.amount_received_by=d.user_id','left')->where('a.payment_term_id',$row->payment_term)->where('c.id',$row->recordid)->get();
	foreach($q->result() as $row1);
	$receivedmsg = "";	
	$ordervalue = $row->order_value;
	$calculatedvalue = ($ordervalue*$row1->payment_percentage)/100;
	$moneyformat = number_format($calculatedvalue, 2, '.', ','); 
	$partpayment =$moneyformat;
	$marketingperson = $row->first_name." ".$row->last_name;

	$enddate = date('d-m-Y',strtotime($row1->end_date));
				if($row1->task_status==1){
					$taskstatus = "Done";
					$backgroundcolor = "green;";
					$textcolor = "#fff";
					if($row1->amount_received<>'0.00'){
						$taskstatus = "Done";
						$totalamotreceived = number_format($row1->amount_received, 2, '.', ','); 
						$tamtreceived = $totalamotreceived;
						$reveiveddate = date('d-m-Y',strtotime($row1->amount_received_date));
						$receivedby = $row1->first_name." ".$row1->last_name;

						$receivedmsg = $tamtreceived."<br>".$reveiveddate."<br>".$receivedby;

					}else{
						$taskstatus ="Payment Collection Pending";
						$receivedmsg = '<span class="btn btn-warning btn-xs" onclick="updateyourprogressremarks('.$row1->scheduledtaskid.','.$calculatedvalue.');">Update Payment Info</span>';
					}
					}else if($row1->task_status==0 && $row1->end_date<date('Y-m-d')){
					$taskstatus = "Delayed";
					$backgroundcolor = "red;";
					$textcolor = "#fff";
					}else{
					$taskstatus = "Pending to Start";
					$backgroundcolor = "white;";
					$textcolor = "#000";
					}

$dfdetail = strtoupper($row->df_no)."<br>".strtoupper($row->df_description);
	$pending[] = $calculatedvalue;
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

$total = array_sum($pending);
$gtotal = number_format($total, 2, '.', ','); 
$html.='<tr>
<td colspan="7" style="text-align:center;">TOTAL AMOUNT </td>
	<td colspan="3" style="text-align:center;">INR '.$gtotal.'</td>
</tr></tbody></table><br><br><br>';


//echo $html; exit;


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


}
