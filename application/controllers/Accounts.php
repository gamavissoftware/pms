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

/*
|--------------------------------------------------------------------------
| Running DF Finance Control
|--------------------------------------------------------------------------
| One row per running DF. Three registers are pulled together:
|
|   poreceived                    -> the order book (what the customer committed)
|   payment_terms_milestone       -> the claim schedule (when each slice falls due)
|   mcs_dispatch_report_tracking  -> the money (what was invoiced and collected)
|
| The ledger is the system of record for money; milestones only decide when an
| amount becomes claimable. The gap between the two - work finished but not yet
| invoiced - is the number finance actually needs, so it is computed explicitly
| rather than inferred.
*/

function allrunningdf()
{
	$filters = $this->get_allrunningdf_filters();

	$data = array(
		'filters' => $filters,
		'report' => $this->build_allrunningdf_report($filters),
		'df_options' => $this->get_allrunningdf_df_options()
	);

	$this->load->view('accounts/allrunningdf', $data);
}

private function get_allrunningdf_filters()
{
	$start_raw = trim((string) $this->uri->segment(3));
	$end_raw = trim((string) $this->uri->segment(4));
	$df_raw = trim((string) $this->uri->segment(5));

	$start_is_all = ($start_raw === '' || strtoupper($start_raw) === 'ALL');
	$end_is_all = ($end_raw === '' || strtoupper($end_raw) === 'ALL');

	// A running DF carries live money whatever its release date, so the default
	// scope is every running DF. The date range only applies when asked for.
	$has_date_filter = !($start_is_all && $end_is_all);

	$start_date = $this->normalize_allrunningdf_date($start_raw, date('Y-m-d', strtotime('-12 months')));
	$end_date = $this->normalize_allrunningdf_date($end_raw, date('Y-m-d'));

	if ($has_date_filter && strtotime($start_date) > strtotime($end_date)) {
		$swap = $start_date;
		$start_date = $end_date;
		$end_date = $swap;
	}

	return array(
		'start_date' => $has_date_filter ? $start_date : 'ALL',
		'end_date' => $has_date_filter ? $end_date : 'ALL',
		'df_id' => ($df_raw === '' ? 'ALL' : $df_raw),
		'has_date_filter' => $has_date_filter
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
	$this->db->where('df_status', 0);
	$this->db->where('IFNULL(on_hold, 0) = 0', null, false);
	$this->db->order_by('df_no', 'ASC');

	return $this->db->get()->result_array();
}

/*
| Some payment terms carry more than one generation of milestone mapping - an
| original set and a later re-map - and both are still live. Terms 14 and 20 are
| the current example: each totals 100% on its own, so together they bill the
| order twice. Walk the rows oldest first and cut a generation every time the
| running percentage completes 100%, then keep only the newest one. If the rows
| do not split cleanly the whole set is returned untouched and the percentage
| check downstream reports it instead of this function guessing.
*/
private function resolve_allrunningdf_milestone_set($milestone_rows)
{
	$generations = array();
	$current = array();
	$running_percentage = 0;

	foreach ($milestone_rows as $milestone_row) {
		$current[] = $milestone_row;
		$running_percentage += (float) $milestone_row['payment_percentage'];

		if (abs($running_percentage - 100) < 0.01) {
			$generations[] = $current;
			$current = array();
			$running_percentage = 0;
		}
	}

	if (!empty($current)) {
		$generations[] = $current;
	}

	if (count($generations) < 2) {
		return array('milestones' => $milestone_rows, 'superseded_count' => 0);
	}

	$live_generation = array_pop($generations);
	$superseded_count = 0;
	foreach ($generations as $generation) {
		$superseded_count += count($generation);
	}

	return array('milestones' => $live_generation, 'superseded_count' => $superseded_count);
}

private function build_allrunningdf_report($filters)
{
	$today = date('Y-m-d');

	$report = array(
		'rows' => array(),
		'summary' => $this->blank_allrunningdf_summary(),
		'insights' => array(),
		'action_rows' => array(),
		'config_issues' => array(),
		'marketing_summary' => array(),
		'filter_summary' => array(
			'date_range_label' => !empty($filters['has_date_filter'])
				? (date('d M Y', strtotime($filters['start_date'])) . ' to ' . date('d M Y', strtotime($filters['end_date'])))
				: 'All running DFs',
			'df_label' => 'All running DFs',
			'generated_on' => date('d M Y h:i A'),
			'date_scope_note' => !empty($filters['has_date_filter'])
				? 'Filtered on DF release date. Running DFs released outside this range are hidden even though their money is still open.'
				: 'Every running DF is in scope, irrespective of release date.'
		)
	);

	/* The DF drives the report, not the PO. A running DF with no PO recorded is
	   exactly the kind of row finance needs to see, so the PO join is a LEFT one. */
	$this->db->select('
		df.id AS df_id,
		df.df_no,
		df.df_description,
		df.added_on AS df_release_on
	');
	$this->db->from('df_release df');
	$this->db->where('df.df_status', 0);
	$this->db->where('IFNULL(df.on_hold, 0) = 0', null, false);

	if (!empty($filters['has_date_filter'])) {
		$this->db->where('DATE(df.added_on) >=', $filters['start_date']);
		$this->db->where('DATE(df.added_on) <=', $filters['end_date']);
	}

	if ((string) $filters['df_id'] !== 'ALL') {
		$this->db->where('df.id', (int) $filters['df_id']);
	}

	$this->db->order_by('df.added_on', 'DESC');
	$df_rows = $this->db->get()->result_array();

	if (empty($df_rows)) {
		return $report;
	}

	$df_ids = array();
	foreach ($df_rows as $df_row) {
		$df_ids[] = (int) $df_row['df_id'];
	}

	if ((string) $filters['df_id'] !== 'ALL') {
		foreach ($this->get_allrunningdf_df_options() as $df_option) {
			if ((string) $df_option['id'] === (string) $filters['df_id']) {
				$report['filter_summary']['df_label'] = trim($df_option['df_no'] . ' ' . $df_option['df_description']);
				break;
			}
		}
	}

	$payment_term_ids = array();
	$milestone_task_ids = array();
	$term_health = array();
	$invoice_usage = array();

	$po_map = $this->get_allrunningdf_po_map($df_ids, $payment_term_ids);
	$milestone_map = $this->get_allrunningdf_milestone_map($payment_term_ids, $milestone_task_ids, $term_health);
	$task_map = $this->get_allrunningdf_task_map($df_ids, $milestone_task_ids);
	$ledger_map = $this->get_allrunningdf_ledger_map($df_ids, $invoice_usage);

	$rows = array();
	$marketing_summary = array();
	$config_issues = array();

	foreach ($df_rows as $df_row) {
		$df_id = (int) $df_row['df_id'];
		$po_list = isset($po_map[$df_id]) ? $po_map[$df_id] : array();

		$row = array(
			'df_id' => $df_id,
			'df_no' => strtoupper(trim((string) $df_row['df_no'])),
			'df_description' => trim((string) $df_row['df_description']),
			'df_release_date' => $this->format_allrunningdf_date($df_row['df_release_on']),
			'df_detail_url' => page_url . 'Dashboard/df_full_detail?df_id=' . $df_id,
			'gantt_url' => page_url . 'gantt/' . $df_id,
			'company_name' => '',
			'marketing_person' => 'Not mapped',
			'currencies' => array(),
			'pos' => array(),
			'po_count' => count($po_list),
			'order_value' => 0,
			'claimable_amount' => 0,
			'claimable_count' => 0,
			'slipped_amount' => 0,
			'slipped_count' => 0,
			'milestone_count' => 0,
			'has_config_issue' => false,
			'issues' => array()
		);

		foreach ($po_list as $po_row) {
			$po = $this->build_allrunningdf_po_block($po_row, $df_id, $milestone_map, $term_health, $task_map, $today, $config_issues);

			$row['pos'][] = $po;
			$row['order_value'] += $po['order_value'];
			$row['claimable_amount'] += $po['claimable_amount'];
			$row['claimable_count'] += $po['claimable_count'];
			$row['slipped_amount'] += $po['slipped_amount'];
			$row['slipped_count'] += $po['slipped_count'];
			$row['milestone_count'] += count($po['milestones']);

			if ($row['company_name'] === '') {
				$row['company_name'] = $po['company_name'];
			}
			if ($row['marketing_person'] === 'Not mapped' && $po['marketing_person'] !== '') {
				$row['marketing_person'] = $po['marketing_person'];
			}
			if ($po['currency'] !== '' && !in_array($po['currency'], $row['currencies'], true)) {
				$row['currencies'][] = $po['currency'];
			}
			if (!empty($po['has_config_issue'])) {
				$row['has_config_issue'] = true;
			}
			foreach ($po['issues'] as $po_issue) {
				$row['issues'][] = $po_issue;
			}
		}

		if ($row['company_name'] === '') {
			$row['company_name'] = 'Customer not mapped';
		}

		$row['order_value'] = round($row['order_value'], 2);
		$row['claimable_amount'] = round($row['claimable_amount'], 2);
		$row['slipped_amount'] = round($row['slipped_amount'], 2);
		$row['order_value_display'] = $this->format_allrunningdf_money($row['order_value']);
		$row['claimable_amount_display'] = $this->format_allrunningdf_money($row['claimable_amount']);
		$row['slipped_amount_display'] = $this->format_allrunningdf_money($row['slipped_amount']);

		$row = array_merge($row, $this->apply_allrunningdf_ledger($row, isset($ledger_map[$df_id]) ? $ledger_map[$df_id] : array(), $invoice_usage, $today));
		$row = array_merge($row, $this->grade_allrunningdf_row($row));

		$rows[] = $row;
		$this->accumulate_allrunningdf_summary($report['summary'], $row);

		$marketing_key = $row['marketing_person'];
		if (!isset($marketing_summary[$marketing_key])) {
			$marketing_summary[$marketing_key] = array(
				'label' => $marketing_key, 'df_count' => 0, 'order_value' => 0,
				'received_amount' => 0, 'balance_amount' => 0, 'unbilled_amount' => 0
			);
		}
		$marketing_summary[$marketing_key]['df_count']++;
		$marketing_summary[$marketing_key]['order_value'] += $row['order_value'];
		$marketing_summary[$marketing_key]['received_amount'] += $row['received_amount'];
		$marketing_summary[$marketing_key]['balance_amount'] += $row['balance_amount'];
		$marketing_summary[$marketing_key]['unbilled_amount'] += $row['unbilled_amount'];
	}

	$report['rows'] = $this->sort_allrunningdf_rows($rows);
	$report['summary'] = $this->finalise_allrunningdf_summary($report['summary']);
	$report['config_issues'] = $this->summarise_allrunningdf_config_issues($config_issues);
	$report['marketing_summary'] = $this->finalise_allrunningdf_marketing($marketing_summary);
	$report['action_rows'] = $this->build_allrunningdf_action_rows($report['rows']);
	$report['insights'] = $this->build_allrunningdf_insights($report['summary'], $report['rows'], $report['config_issues']);

	return $report;
}

private function get_allrunningdf_po_map($df_ids, &$payment_term_ids)
{
	$payment_term_ids = array();
	$po_map = array();

	if (empty($df_ids)) {
		return $po_map;
	}

	$this->db->select('
		p.id AS po_id, p.df_id, p.pono, p.podate, p.order_value, p.company_name,
		p.customer_currency, p.amount_in_customer_currency, p.po_attachment,
		p.payment_term, p.orderhold,
		pt.payment_terms,
		marketing.title AS marketing_title,
		marketing.first_name AS marketing_first_name,
		marketing.last_name AS marketing_last_name
	');
	$this->db->from('poreceived p');
	$this->db->join('payment_terms pt', 'p.payment_term = pt.id', 'left');
	$this->db->join('system_users marketing', 'marketing.user_id = p.added_by', 'left');
	$this->db->where_in('p.df_id', $df_ids);
	$this->db->order_by('p.podate', 'DESC');
	$this->db->order_by('p.id', 'DESC');

	foreach ($this->db->get()->result_array() as $po_row) {
		$po_map[(int) $po_row['df_id']][] = $po_row;
		if ((int) $po_row['payment_term'] > 0) {
			$payment_term_ids[(int) $po_row['payment_term']] = (int) $po_row['payment_term'];
		}
	}

	return $po_map;
}

private function get_allrunningdf_milestone_map($payment_term_ids, &$milestone_task_ids, &$term_health)
{
	$milestone_task_ids = array();
	$term_health = array();
	$milestone_map = array();

	if (empty($payment_term_ids)) {
		return $milestone_map;
	}

	$this->db->select('ptm.id, ptm.payment_term_id, ptm.payment_percentage, ptm.milestone AS milestone_task_id, tm.task_name');
	$this->db->from('payment_terms_milestone ptm');
	$this->db->join('task_management tm', 'tm.task_id = ptm.milestone', 'left');
	$this->db->where_in('ptm.payment_term_id', array_values($payment_term_ids));
	$this->db->order_by('ptm.payment_term_id', 'ASC');
	$this->db->order_by('ptm.id', 'ASC');

	$raw_by_term = array();
	foreach ($this->db->get()->result_array() as $milestone_row) {
		$raw_by_term[(int) $milestone_row['payment_term_id']][] = $milestone_row;
	}

	foreach ($payment_term_ids as $term_id) {
		$raw_rows = isset($raw_by_term[$term_id]) ? $raw_by_term[$term_id] : array();
		$resolved = $this->resolve_allrunningdf_milestone_set($raw_rows);

		$percentage_total = 0;
		$missing_task_count = 0;
		foreach ($resolved['milestones'] as $milestone_row) {
			$percentage_total += (float) $milestone_row['payment_percentage'];
			$milestone_task_ids[(int) $milestone_row['milestone_task_id']] = (int) $milestone_row['milestone_task_id'];
			if (trim((string) $milestone_row['task_name']) === '') {
				$missing_task_count++;
			}
		}

		$milestone_map[$term_id] = $resolved['milestones'];
		$term_health[$term_id] = array(
			'percentage_total' => round($percentage_total, 2),
			'superseded_count' => $resolved['superseded_count'],
			'missing_task_count' => $missing_task_count,
			'milestone_count' => count($resolved['milestones'])
		);
	}

	return $milestone_map;
}

private function get_allrunningdf_task_map($df_ids, $milestone_task_ids)
{
	$task_map = array();

	if (empty($df_ids) || empty($milestone_task_ids)) {
		return $task_map;
	}

	$this->db->select('
		td.id, td.po_id, td.df_id, td.taskid, td.task_status, td.end_date,
		td.task_completed_on, IFNULL(td.paymentstage, 0) AS paymentstage
	');
	$this->db->from('task_department_wise_scheduling td');
	$this->db->where_in('td.df_id', $df_ids);
	$this->db->where_in('td.taskid', array_values($milestone_task_ids));
	$this->db->where('IFNULL(td.on_hold, 0) = 0', null, false);
	$this->db->order_by('td.id', 'DESC');

	foreach ($this->db->get()->result_array() as $task_row) {
		$task_map[(int) $task_row['df_id']][(int) $task_row['taskid']][] = $task_row;
	}

	return $task_map;
}

/* One ledger row per DF - mcs_dispatch_report_tracking.df_id is unique. An
   invoice number shared across DFs is recorded so its amount is not counted
   twice in the totals. */
private function get_allrunningdf_ledger_map($df_ids, &$invoice_usage)
{
	$invoice_usage = array();
	$ledger_map = array();

	if (empty($df_ids)) {
		return $ledger_map;
	}

	$this->db->select('
		tracker.*,
		updater.title AS tracker_updater_title,
		updater.first_name AS tracker_updater_first_name,
		updater.last_name AS tracker_updater_last_name
	');
	$this->db->from('mcs_dispatch_report_tracking tracker');
	$this->db->join('system_users updater', 'updater.user_id = tracker.updated_by', 'left');
	$this->db->where_in('tracker.df_id', $df_ids);

	foreach ($this->db->get()->result_array() as $ledger_row) {
		$ledger_map[(int) $ledger_row['df_id']] = $ledger_row;

		$invoice_no = strtoupper(trim((string) $ledger_row['invoice_no']));
		if ($invoice_no !== '') {
			$invoice_usage[$invoice_no][] = (int) $ledger_row['df_id'];
		}
	}

	return $ledger_map;
}

private function build_allrunningdf_po_block($po_row, $df_id, $milestone_map, $term_health, $task_map, $today, &$config_issues)
{
	$order_value = (float) $po_row['order_value'];
	$term_id = (int) $po_row['payment_term'];
	$term_name = trim((string) $po_row['payment_terms']);
	$milestones = isset($milestone_map[$term_id]) ? $milestone_map[$term_id] : array();
	$health = isset($term_health[$term_id]) ? $term_health[$term_id] : array();

	$po = array(
		'po_id' => (int) $po_row['po_id'],
		'po_no' => trim((string) $po_row['pono']),
		'po_date' => $this->format_allrunningdf_date($po_row['podate']),
		'po_date_raw' => trim((string) $po_row['podate']),
		'order_value' => $order_value,
		'order_value_display' => $this->format_allrunningdf_money($order_value),
		'company_name' => $this->format_allrunningdf_title_case($po_row['company_name']),
		'currency' => strtoupper(trim((string) $po_row['customer_currency'])),
		'currency_amount' => (float) $po_row['amount_in_customer_currency'],
		'marketing_person' => $this->format_allrunningdf_person_name(
			$po_row['marketing_title'], $po_row['marketing_first_name'], $po_row['marketing_last_name']
		),
		'payment_term_id' => $term_id,
		'payment_term_name' => $term_name,
		'download_url' => !empty($po_row['po_attachment']) ? (sfdocument . 'Taskdocument/' . $po_row['po_attachment']) : '',
		'order_on_hold' => ((int) $po_row['orderhold'] === 1),
		'milestones' => array(),
		'claimable_amount' => 0,
		'claimable_count' => 0,
		'slipped_amount' => 0,
		'slipped_count' => 0,
		'percentage_total' => isset($health['percentage_total']) ? $health['percentage_total'] : 0,
		'has_config_issue' => false,
		'issues' => array()
	);

	$po['currency_amount_display'] = ($po['currency'] !== '' && $po['currency'] !== 'INR' && $po['currency_amount'] > 0)
		? ($po['currency'] . ' ' . number_format($po['currency_amount'], 2, '.', ','))
		: '';

	$po_label = ($po['po_no'] !== '' ? $po['po_no'] : 'PO not numbered');

	if ($po['order_on_hold']) {
		$po['issues'][] = $po_label . ' is flagged as an order on hold.';
	}

	if ($term_id === 0 || $term_name === '') {
		$po['issues'][] = $po_label . ' has no payment term linked, so no claim schedule can be built.';
		$po['has_config_issue'] = true;
		$this->record_allrunningdf_config_issue($config_issues, 0, 'Payment term not linked', $df_id, $order_value);
		return $po;
	}

	if (empty($milestones)) {
		$po['issues'][] = 'Payment term "' . $term_name . '" has no milestones configured, so nothing can be claimed against it.';
		$po['has_config_issue'] = true;
		$this->record_allrunningdf_config_issue($config_issues, $term_id, 'No milestones configured', $df_id, $order_value, $term_name);
		return $po;
	}

	if (!empty($health['superseded_count'])) {
		$po['issues'][] = 'Payment term "' . $term_name . '" carries an older milestone mapping as well. '
			. $health['superseded_count'] . ' superseded milestone(s) were ignored so the order is not billed twice.';
		$po['has_config_issue'] = true;
		$this->record_allrunningdf_config_issue($config_issues, $term_id, 'Duplicate milestone generations', $df_id, $order_value, $term_name);
	}

	if (abs($po['percentage_total'] - 100) > 0.01) {
		$po['issues'][] = 'Milestone percentages on "' . $term_name . '" total '
			. rtrim(rtrim(number_format($po['percentage_total'], 2, '.', ''), '0'), '.') . '%, not 100%.';
		$po['has_config_issue'] = true;
		$this->record_allrunningdf_config_issue($config_issues, $term_id, 'Percentages do not total 100', $df_id, $order_value, $term_name);
	}

	if (!empty($health['missing_task_count'])) {
		$po['issues'][] = 'Payment term "' . $term_name . '" points at ' . $health['missing_task_count']
			. ' milestone task(s) that no longer exist in the task master.';
		$po['has_config_issue'] = true;
		$this->record_allrunningdf_config_issue($config_issues, $term_id, 'Milestone task no longer exists', $df_id, $order_value, $term_name);
	}

	$unmapped_count = 0;

	foreach ($milestones as $milestone_row) {
		$task_id = (int) $milestone_row['milestone_task_id'];
		$percentage = (float) $milestone_row['payment_percentage'];
		$amount = round(($order_value * $percentage) / 100, 2);

		$candidates = isset($task_map[$df_id][$task_id]) ? $task_map[$df_id][$task_id] : array();
		$matched = $this->pick_allrunningdf_task_candidate($candidates, $po['po_id']);

		$milestone = array(
			'task_name' => trim((string) $milestone_row['task_name']) !== ''
				? trim((string) $milestone_row['task_name'])
				: 'Task #' . $task_id . ' (missing from task master)',
			'percentage' => $percentage,
			'percentage_display' => rtrim(rtrim(number_format($percentage, 2, '.', ''), '0'), '.') . '%',
			'amount' => $amount,
			'amount_display' => $this->format_allrunningdf_money($amount),
			'trigger_date' => 'Not scheduled',
			'trigger_date_raw' => '',
			'claim_status_key' => 'unmapped',
			'claim_status_label' => 'Not scheduled',
			'is_claimable' => false
		);

		if (empty($matched)) {
			if (trim((string) $milestone_row['task_name']) !== '') {
				$unmapped_count++;
			}
		} else {
			$task_status = (int) $matched['task_status'];
			$end_date = trim((string) $matched['end_date']);
			$completed_on = trim((string) $matched['task_completed_on']);

			if ($task_status === 1) {
				$milestone['is_claimable'] = true;
				$milestone['claim_status_key'] = 'claimable';
				$milestone['claim_status_label'] = 'Claimable';
				$milestone['trigger_date_raw'] = ($completed_on !== '' ? substr($completed_on, 0, 10) : $end_date);
				$milestone['trigger_date'] = $this->format_allrunningdf_date($milestone['trigger_date_raw']);
			} elseif ($task_status === 2) {
				$milestone['claim_status_key'] = 'approval';
				$milestone['claim_status_label'] = 'Awaiting approval';
				$milestone['trigger_date_raw'] = $end_date;
				$milestone['trigger_date'] = $this->format_allrunningdf_date($end_date);
			} elseif ($end_date !== '' && $end_date !== '0000-00-00' && $end_date < $today) {
				$milestone['claim_status_key'] = 'slipped';
				$milestone['claim_status_label'] = 'Trigger overdue';
				$milestone['trigger_date_raw'] = $end_date;
				$milestone['trigger_date'] = $this->format_allrunningdf_date($end_date);
				$po['slipped_amount'] += $amount;
				$po['slipped_count']++;
			} else {
				$milestone['claim_status_key'] = 'planned';
				$milestone['claim_status_label'] = 'Planned';
				$milestone['trigger_date_raw'] = $end_date;
				$milestone['trigger_date'] = $this->format_allrunningdf_date($end_date);
			}

			if ($milestone['is_claimable']) {
				$po['claimable_amount'] += $amount;
				$po['claimable_count']++;
			}
		}

		$po['milestones'][] = $milestone;
	}

	if ($unmapped_count > 0) {
		$po['issues'][] = $unmapped_count . ' milestone(s) on this PO have no matching task scheduled on the DF, so they can never turn claimable.';
		$po['has_config_issue'] = true;
		$this->record_allrunningdf_config_issue($config_issues, $term_id, 'Milestone task not scheduled on the DF', $df_id, $order_value, $term_name);
	}

	$po['claimable_amount'] = round($po['claimable_amount'], 2);
	$po['slipped_amount'] = round($po['slipped_amount'], 2);

	return $po;
}

private function record_allrunningdf_config_issue(&$config_issues, $term_id, $problem, $df_id, $order_value, $term_name = '')
{
	$key = $term_id . '|' . $problem;

	if (!isset($config_issues[$key])) {
		$config_issues[$key] = array(
			'payment_term_id' => (int) $term_id,
			'payment_term_name' => $term_name !== '' ? $term_name : 'Payment term not linked',
			'problem' => $problem,
			'df_ids' => array(),
			'order_value' => 0
		);
	}

	if (!in_array((int) $df_id, $config_issues[$key]['df_ids'], true)) {
		$config_issues[$key]['df_ids'][] = (int) $df_id;
		$config_issues[$key]['order_value'] += (float) $order_value;
	}
}

private function apply_allrunningdf_ledger($row, $ledger_row, $invoice_usage, $today)
{
	$issues = $row['issues'];

	$out = array(
		'ledger_available' => false,
		'invoice_no' => '',
		'invoice_date' => 'Not invoiced',
		'invoice_date_raw' => '',
		'invoiced_amount' => 0,
		'taxable_sale' => 0,
		'received_amount' => 0,
		'balance_amount' => 0,
		'due_date' => 'Not set',
		'due_date_raw' => '',
		'due_status_key' => 'not_updated',
		'due_status_label' => 'Not updated',
		'overdue_balance' => 0,
		'days_overdue' => 0,
		'nos_of_machines' => '',
		'remarks' => '',
		'commissioning_status' => '',
		'last_updated_by' => 'Not updated',
		'last_updated_on' => 'Not updated',
		'invoice_shared_with' => array()
	);

	if (!empty($ledger_row)) {
		$invoiced = (float) $ledger_row['invoice_amount'];
		$received = (float) $ledger_row['payment_received'];

		/* Balance is always recomputed. The stored column is maintained by the
		   dispatch screen and can fall behind when one of the two amounts is
		   edited outside it. */
		$balance = round($invoiced - $received, 2);
		$stored_balance = (float) $ledger_row['balance_amount'];

		$due_state = $this->get_allrunningdf_dispatch_due_state($ledger_row['payment_due_status']);
		$due_date_raw = trim((string) $ledger_row['due_date']);
		$invoice_no = strtoupper(trim((string) $ledger_row['invoice_no']));

		$out['ledger_available'] = true;
		$out['invoice_no'] = $invoice_no;
		$out['invoice_date'] = $this->format_allrunningdf_date($ledger_row['invoice_date']);
		$out['invoice_date_raw'] = trim((string) $ledger_row['invoice_date']);
		$out['invoiced_amount'] = round($invoiced, 2);
		$out['taxable_sale'] = round((float) $ledger_row['taxable_sale'], 2);
		$out['received_amount'] = round($received, 2);
		$out['balance_amount'] = $balance;
		$out['due_date'] = $due_date_raw !== '' && $due_date_raw !== '0000-00-00'
			? $this->format_allrunningdf_date($due_date_raw) : 'Not set';
		$out['due_date_raw'] = $due_date_raw;
		$out['due_status_key'] = $due_state['key'];
		$out['due_status_label'] = $due_state['label'];
		$out['nos_of_machines'] = trim((string) $ledger_row['nos_of_machines']);
		$out['remarks'] = trim((string) $ledger_row['remarks']);
		$out['commissioning_status'] = trim((string) $ledger_row['commissioning_status']);
		$out['last_updated_on'] = $this->format_allrunningdf_datetime($ledger_row['updated_on']);

		$updated_by = $this->format_allrunningdf_person_name(
			$ledger_row['tracker_updater_title'],
			$ledger_row['tracker_updater_first_name'],
			$ledger_row['tracker_updater_last_name']
		);
		$out['last_updated_by'] = $updated_by !== '' ? $updated_by : 'Not updated';

		if ($balance > 0.01 && $due_date_raw !== '' && $due_date_raw !== '0000-00-00' && $due_date_raw < $today) {
			$out['overdue_balance'] = $balance;
			$out['days_overdue'] = (int) floor((strtotime($today) - strtotime($due_date_raw)) / 86400);
		}

		if (abs($stored_balance - $balance) > 0.01) {
			$issues[] = 'Stored balance on the dispatch ledger is ' . $this->format_allrunningdf_money($stored_balance)
				. ' but invoice minus receipt works out to ' . $this->format_allrunningdf_money($balance) . '.';
		}

		if ($received > $invoiced + 0.01) {
			$issues[] = 'Receipts exceed the invoiced amount on the dispatch ledger. Please verify the entries.';
		}

		if ($invoice_no !== '' && $invoiced <= 0) {
			$issues[] = 'Dispatch ledger carries invoice ' . $invoice_no . ' but no invoice amount.';
		}

		if ($invoice_no !== '' && isset($invoice_usage[$invoice_no]) && count($invoice_usage[$invoice_no]) > 1) {
			$out['invoice_shared_with'] = $invoice_usage[$invoice_no];
			$issues[] = 'Invoice ' . $invoice_no . ' is recorded against ' . count($invoice_usage[$invoice_no])
				. ' DFs. Its amount is counted once in the totals, but the split per DF needs confirming.';
		}

		if ($balance > 0.01 && ($due_date_raw === '' || $due_date_raw === '0000-00-00')) {
			$issues[] = 'A balance of ' . $this->format_allrunningdf_money($balance) . ' is outstanding but no due date is set on the ledger.';
		}
	}

	$out['unbilled_amount'] = round(max(0, $row['claimable_amount'] - $out['invoiced_amount']), 2);
	$out['pipeline_amount'] = round(max(0, $row['order_value'] - $row['claimable_amount']), 2);
	$out['outstanding_amount'] = round(max(0, $row['order_value'] - $out['received_amount']), 2);
	$out['collection_percentage'] = $row['order_value'] > 0
		? round(($out['received_amount'] / $row['order_value']) * 100)
		: 0;

	foreach (array(
		'invoiced_amount', 'taxable_sale', 'received_amount', 'balance_amount', 'overdue_balance',
		'unbilled_amount', 'pipeline_amount', 'outstanding_amount'
	) as $money_field) {
		$out[$money_field . '_display'] = $this->format_allrunningdf_money($out[$money_field]);
	}

	$out['issues'] = $issues;

	return $out;
}

private function grade_allrunningdf_row($row)
{
	if ($row['po_count'] === 0) {
		return array('health_key' => 'nopo', 'health_label' => 'No PO recorded', 'health_priority' => 6,
			'focus' => 'This DF is running but no purchase order is recorded against it.');
	}

	if (!empty($row['has_config_issue']) && $row['claimable_amount'] <= 0) {
		return array('health_key' => 'config', 'health_label' => 'Configuration gap', 'health_priority' => 5,
			'focus' => 'Payment setup is incomplete, so this order cannot be tracked to collection.');
	}

	if ($row['overdue_balance'] > 0.01) {
		return array('health_key' => 'overdue', 'health_label' => 'Payment overdue', 'health_priority' => 4,
			'focus' => $row['overdue_balance_display'] . ' is past its due date by ' . $row['days_overdue'] . ' day(s).');
	}

	if ($row['unbilled_amount'] > 0.01) {
		return array('health_key' => 'unbilled', 'health_label' => 'Ready to invoice', 'health_priority' => 3,
			'focus' => $row['unbilled_amount_display'] . ' has become claimable but is not invoiced yet.');
	}

	if (!empty($row['has_config_issue'])) {
		return array('health_key' => 'config', 'health_label' => 'Configuration gap', 'health_priority' => 5,
			'focus' => 'Payment setup needs correction before the numbers can be relied on.');
	}

	if ($row['balance_amount'] > 0.01) {
		return array('health_key' => 'watch', 'health_label' => 'Awaiting payment', 'health_priority' => 2,
			'focus' => $row['balance_amount_display'] . ' is invoiced and within its due date.');
	}

	if ($row['received_amount'] > 0 && $row['outstanding_amount'] <= 0.01) {
		return array('health_key' => 'collected', 'health_label' => 'Fully collected', 'health_priority' => 0,
			'focus' => 'The full order value has been collected.');
	}

	return array('health_key' => 'ontrack', 'health_label' => 'On track', 'health_priority' => 1,
		'focus' => 'Nothing is claimable or overdue right now.');
}

private function blank_allrunningdf_summary()
{
	return array(
		'df_count' => 0, 'po_count' => 0,
		'order_value' => 0, 'claimable_amount' => 0, 'pipeline_amount' => 0,
		'invoiced_amount' => 0, 'received_amount' => 0, 'balance_amount' => 0,
		'unbilled_amount' => 0, 'overdue_amount' => 0, 'outstanding_amount' => 0, 'slipped_amount' => 0,
		'slipped_df_count' => 0,
		'collection_percentage' => 0,
		'overdue_df_count' => 0, 'unbilled_df_count' => 0, 'config_df_count' => 0,
		'no_po_df_count' => 0, 'no_ledger_df_count' => 0, 'shared_invoice_df_count' => 0,
		'_counted_invoices' => array()
	);
}

private function accumulate_allrunningdf_summary(&$summary, $row)
{
	$summary['df_count']++;
	$summary['po_count'] += $row['po_count'];
	$summary['order_value'] += $row['order_value'];
	$summary['claimable_amount'] += $row['claimable_amount'];
	$summary['pipeline_amount'] += $row['pipeline_amount'];
	$summary['unbilled_amount'] += $row['unbilled_amount'];
	$summary['slipped_amount'] += $row['slipped_amount'];
	if ($row['slipped_amount'] > 0.01) {
		$summary['slipped_df_count']++;
	}

	/* One invoice can be recorded against several DFs. Counting its amount once
	   keeps the invoiced and collected totals honest. */
	$invoice_key = $row['invoice_no'] !== '' ? $row['invoice_no'] : ('DF#' . $row['df_id']);
	if (!isset($summary['_counted_invoices'][$invoice_key])) {
		$summary['_counted_invoices'][$invoice_key] = true;
		$summary['invoiced_amount'] += $row['invoiced_amount'];
		$summary['received_amount'] += $row['received_amount'];
		$summary['balance_amount'] += $row['balance_amount'];
		$summary['overdue_amount'] += $row['overdue_balance'];
	}

	if (!empty($row['invoice_shared_with'])) {
		$summary['shared_invoice_df_count']++;
	}
	if ($row['overdue_balance'] > 0.01) {
		$summary['overdue_df_count']++;
	}
	if ($row['unbilled_amount'] > 0.01) {
		$summary['unbilled_df_count']++;
	}
	if (!empty($row['has_config_issue'])) {
		$summary['config_df_count']++;
	}
	if ($row['po_count'] === 0) {
		$summary['no_po_df_count']++;
	}
	if (!$row['ledger_available']) {
		$summary['no_ledger_df_count']++;
	}
}

private function finalise_allrunningdf_summary($summary)
{
	unset($summary['_counted_invoices']);

	$summary['outstanding_amount'] = round(max(0, $summary['order_value'] - $summary['received_amount']), 2);
	$summary['collection_percentage'] = $summary['order_value'] > 0
		? round(($summary['received_amount'] / $summary['order_value']) * 100)
		: 0;

	foreach (array(
		'order_value', 'claimable_amount', 'pipeline_amount', 'invoiced_amount', 'received_amount',
		'balance_amount', 'unbilled_amount', 'overdue_amount', 'outstanding_amount', 'slipped_amount'
	) as $money_field) {
		$summary[$money_field] = round($summary[$money_field], 2);
		$summary[$money_field . '_display'] = $this->format_allrunningdf_money($summary[$money_field]);
	}

	return $summary;
}

private function sort_allrunningdf_rows($rows)
{
	usort($rows, function ($left, $right) {
		if ((int) $left['health_priority'] !== (int) $right['health_priority']) {
			return ((int) $left['health_priority'] > (int) $right['health_priority']) ? -1 : 1;
		}
		foreach (array('overdue_balance', 'unbilled_amount', 'balance_amount', 'order_value') as $money_field) {
			if (abs((float) $left[$money_field] - (float) $right[$money_field]) > 0.01) {
				return ((float) $left[$money_field] > (float) $right[$money_field]) ? -1 : 1;
			}
		}
		return strcmp((string) $left['df_no'], (string) $right['df_no']);
	});

	return $rows;
}

private function summarise_allrunningdf_config_issues($config_issues)
{
	$summarised = array();

	foreach ($config_issues as $issue) {
		$issue['df_count'] = count($issue['df_ids']);
		$issue['order_value'] = round($issue['order_value'], 2);
		$issue['order_value_display'] = $this->format_allrunningdf_money($issue['order_value']);
		$summarised[] = $issue;
	}

	usort($summarised, function ($left, $right) {
		if (abs((float) $left['order_value'] - (float) $right['order_value']) > 0.01) {
			return ((float) $left['order_value'] > (float) $right['order_value']) ? -1 : 1;
		}
		return strcmp((string) $left['problem'], (string) $right['problem']);
	});

	return $summarised;
}

private function finalise_allrunningdf_marketing($marketing_summary)
{
	foreach ($marketing_summary as $key => $marketing_row) {
		foreach (array('order_value', 'received_amount', 'balance_amount', 'unbilled_amount') as $money_field) {
			$marketing_summary[$key][$money_field] = round($marketing_row[$money_field], 2);
			$marketing_summary[$key][$money_field . '_display'] = $this->format_allrunningdf_money($marketing_row[$money_field]);
		}
	}

	$marketing_summary = array_values($marketing_summary);
	usort($marketing_summary, function ($left, $right) {
		foreach (array('unbilled_amount', 'balance_amount', 'order_value') as $money_field) {
			if (abs((float) $left[$money_field] - (float) $right[$money_field]) > 0.01) {
				return ((float) $left[$money_field] > (float) $right[$money_field]) ? -1 : 1;
			}
		}
		return strcmp((string) $left['label'], (string) $right['label']);
	});

	return array_slice($marketing_summary, 0, 8);
}

/* The rows a finance user should act on today, each with the one thing to do. */
private function build_allrunningdf_action_rows($rows)
{
	$actions = array();

	foreach ($rows as $row) {
		if ($row['overdue_balance'] > 0.01) {
			$actions[] = array(
				'df_no' => $row['df_no'], 'company_name' => $row['company_name'],
				'action' => 'Chase payment', 'amount_display' => $row['overdue_balance_display'],
				'detail' => 'Overdue by ' . $row['days_overdue'] . ' day(s) against invoice '
					. ($row['invoice_no'] !== '' ? $row['invoice_no'] : 'not numbered') . '.',
				'tone' => 'red', 'amount' => $row['overdue_balance']
			);
		} elseif ($row['unbilled_amount'] > 0.01) {
			$actions[] = array(
				'df_no' => $row['df_no'], 'company_name' => $row['company_name'],
				'action' => 'Raise invoice', 'amount_display' => $row['unbilled_amount_display'],
				'detail' => $row['claimable_count'] . ' milestone(s) have become claimable but are not invoiced.',
				'tone' => 'amber', 'amount' => $row['unbilled_amount']
			);
		} elseif ($row['po_count'] === 0) {
			$actions[] = array(
				'df_no' => $row['df_no'], 'company_name' => $row['company_name'],
				'action' => 'Record the PO', 'amount_display' => 'No value',
				'detail' => 'DF is released and running with no purchase order against it.',
				'tone' => 'red', 'amount' => 0
			);
		}
	}

	usort($actions, function ($left, $right) {
		return ((float) $left['amount'] >= (float) $right['amount']) ? -1 : 1;
	});

	return array_slice($actions, 0, 8);
}

private function build_allrunningdf_insights($summary, $rows, $config_issues)
{
	$insights = array();

	if ($summary['unbilled_amount'] > 0.01) {
		$insights[] = $summary['unbilled_amount_display'] . ' across ' . $summary['unbilled_df_count']
			. ' DF(s) has become claimable under the payment terms but is not invoiced yet. This is the fastest cash available.';
	}

	if ($summary['overdue_amount'] > 0.01) {
		$insights[] = $summary['overdue_amount_display'] . ' is invoiced and past its due date across '
			. $summary['overdue_df_count'] . ' DF(s).';
	}

	if ($summary['slipped_amount'] > 0.01) {
		$insights[] = $summary['slipped_amount_display'] . ' of billing is blocked behind milestone work that is past its target date, across '
			. $summary['slipped_df_count'] . ' DF(s). Clearing that work is what turns it into an invoice.';
	}

	if ($summary['no_ledger_df_count'] > 0) {
		$insights[] = $summary['no_ledger_df_count'] . ' of ' . $summary['df_count']
			. ' running DF(s) have no dispatch ledger entry, so nothing invoiced or collected is recorded against them.';
	}

	if (!empty($config_issues)) {
		$config_value = 0;
		foreach ($config_issues as $issue) {
			$config_value += $issue['order_value'];
		}
		$insights[] = $this->format_allrunningdf_money($config_value)
			. ' of order value sits on payment terms that need correction. Those rows cannot be tracked to collection until the terms are fixed.';
	}

	if ($summary['shared_invoice_df_count'] > 0) {
		$insights[] = $summary['shared_invoice_df_count'] . ' DF(s) share an invoice number with another DF. '
			. 'Each invoice is counted once in the totals above.';
	}

	if ($summary['no_po_df_count'] > 0) {
		$insights[] = $summary['no_po_df_count'] . ' running DF(s) have no purchase order recorded at all.';
	}

	if (empty($insights)) {
		$insights[] = 'Nothing is claimable, overdue or misconfigured in the current view.';
	}

	return $insights;
}

/*
| Invoice and receipt capture for a running DF, writing to the same ledger the
| dispatch report screen uses (mcs_dispatch_report_tracking, one row per DF).
| Finance can record the money where they are already reading it, instead of
| switching to Machine/mcsdispatchreport.
*/
public function update_df_ledger()
{
	$user_id = $this->session->userdata['logged_in']['user_id'];
	$df_id = (int) $this->input->post('df_id');
	$redirect_url = page_url . 'Accounts/allrunningdf/' . $this->uri->segment(3) . '/' . $this->uri->segment(4) . '/' . $this->uri->segment(5);

	if ($df_id <= 0 || $this->db->where('id', $df_id)->count_all_results('df_release') === 0) {
		$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">That DF could not be found.</div>');
		redirect($redirect_url);
	}

	$invoice_amount = (float) $this->input->post('invoice_amount');
	$payment_received = (float) $this->input->post('payment_received');
	$taxable_sale = (float) $this->input->post('taxable_sale');

	if ($invoice_amount < 0 || $payment_received < 0 || $taxable_sale < 0) {
		$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissable">Amounts cannot be negative.</div>');
		redirect($redirect_url);
	}

	date_default_timezone_set('Asia/Kolkata');

	$save_data = array(
		'invoice_no' => trim((string) $this->input->post('invoice_no')),
		'invoice_date' => $this->post_date_or_null('invoice_date'),
		'invoice_amount' => $invoice_amount,
		'taxable_sale' => $taxable_sale,
		'payment_received' => $payment_received,
		'balance_amount' => round($invoice_amount - $payment_received, 2),
		'due_date' => $this->post_date_or_null('due_date'),
		'payment_due_status' => trim((string) $this->input->post('payment_due_status')),
		'remarks' => trim((string) $this->input->post('remarks')),
		'updated_by' => $user_id,
		'updated_on' => date('Y-m-d H:i:s')
	);

	if ($this->db->where('df_id', $df_id)->count_all_results('mcs_dispatch_report_tracking') > 0) {
		$this->db->where('df_id', $df_id);
		$this->db->update('mcs_dispatch_report_tracking', $save_data);
	} else {
		$save_data['df_id'] = $df_id;
		$save_data['created_on'] = date('Y-m-d H:i:s');
		$this->db->insert('mcs_dispatch_report_tracking', $save_data);
	}

	$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissable">Dispatch ledger updated for this DF.</div>');
	redirect($redirect_url);
}

private function post_date_or_null($field)
{
	$value = trim((string) $this->input->post($field));
	if ($value === '' || $value === '0000-00-00') {
		return null;
	}

	$timestamp = strtotime($value);

	return $timestamp === false ? null : date('Y-m-d', $timestamp);
}

/* A milestone task can be scheduled more than once on a DF. Prefer the row
   booked against this PO, then one already marked as a payment stage, then the
   most recent. */
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

		$current_score = array($po_score, ((int) $candidate['paymentstage'] === 1) ? 1 : 0, (int) $candidate['id']);

		if ($current_score > $best_score) {
			$best_score = $current_score;
			$best_candidate = $candidate;
		}
	}

	return $best_candidate;
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

	return $timestamp === false ? $value : date('d-m-Y', $timestamp);
}

private function format_allrunningdf_datetime($value)
{
	$value = trim((string) $value);
	if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
		return 'N/A';
	}

	$timestamp = strtotime($value);

	return $timestamp === false ? $value : date('d-m-Y h:i A', $timestamp);
}

private function format_allrunningdf_title_case($value)
{
	$value = trim((string) $value);
	if ($value === '') {
		return '';
	}

	return ucwords(strtolower(preg_replace('/\s+/', ' ', $value)));
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
	switch (strtolower(trim((string) $status))) {
		case 'due':
			return array('key' => 'due', 'label' => 'Due');
		case 'not due':
			return array('key' => 'not_due', 'label' => 'Not due');
		case 'received':
			return array('key' => 'received', 'label' => 'Received');
		case 'hold':
			return array('key' => 'hold', 'label' => 'Hold');
		default:
			return array('key' => 'not_updated', 'label' => 'Not updated');
	}
}


	public function paymentdashboard()
	{
		$filters = $this->get_finance_calendar_filters();

		$data = array(
			'filters' => $filters,
			'calendar' => $this->build_finance_calendar($filters['start_date'], $filters['end_date'], $filters['df_id']),
			'df_options' => $this->get_allrunningdf_df_options()
		);

		$this->load->view('accounts/upcomingpayments', $data);
	}

	private function get_finance_calendar_filters()
	{
		$start_raw = trim((string) $this->uri->segment(3));
		$end_raw = trim((string) $this->uri->segment(4));
		$df_raw = trim((string) $this->uri->segment(5));

		$start_date = $this->normalize_allrunningdf_date($start_raw, date('Y-m-d'));
		$end_date = $this->normalize_allrunningdf_date($end_raw, date('Y-m-d', strtotime('+7 days')));

		if (strtotime($start_date) > strtotime($end_date)) {
			$swap = $start_date;
			$start_date = $end_date;
			$end_date = $swap;
		}

		return array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'df_id' => ($df_raw === '' ? 'ALL' : $df_raw)
		);
	}

	public function overduepaymentdashboard()
	{
		$this->load->view('accounts/overduepayments', array(
			'receivables' => $this->build_finance_receivables()
		));
	}

	/* Kept for the marketing payment dashboard, which shows these two as tiles.
	   Both now read the dispatch ledger rather than the unused milestone
	   receipt columns, so they agree with the pages they link to. */
	public function upcomingpaymentsthisweek()
	{
		return $this->format_allrunningdf_money(
			$this->get_ledger_balance_due_between(date('Y-m-d'), date('Y-m-d', strtotime('+7 days')))
		);
	}

	public function overduepayments()
	{
		$summary = $this->build_finance_receivables();

		return $summary['summary']['overdue_total_display'];
	}

	private function get_ledger_balance_due_between($start_date, $end_date)
	{
		$this->db->select('invoice_no, invoice_amount, payment_received, due_date');
		$this->db->from('mcs_dispatch_report_tracking');
		// escaping off: this is an expression, not a column name
		$this->db->where('(invoice_amount - payment_received) > 0.01', null, false);
		$this->db->where('due_date >=', $start_date);
		$this->db->where('due_date <=', $end_date);

		$total = 0;
		$counted = array();

		foreach ($this->db->get()->result_array() as $row) {
			$invoice_no = strtoupper(trim((string) $row['invoice_no']));
			$key = $invoice_no !== '' ? $invoice_no : uniqid('inv', true);
			if (isset($counted[$key])) {
				continue;
			}
			$counted[$key] = true;
			$total += (float) $row['invoice_amount'] - (float) $row['payment_received'];
		}

		return round($total, 2);
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
		$startdate = trim((string) $this->input->post('start_date'));
		$enddate = trim((string) $this->input->post('end_date'));
		$dfno = trim((string) $this->input->post('df_no'));

		if ($dfno === '') {
			$dfno = 'ALL';
		}

		// An empty date would become 1970-01-01 through strtotime, so fall back
		// to the default week instead of sending the user to a dead window.
		$startdate = $this->normalize_allrunningdf_date($startdate, date('Y-m-d'));
		$enddate = $this->normalize_allrunningdf_date($enddate, date('Y-m-d', strtotime('+7 days')));

		if (strtotime($startdate) > strtotime($enddate)) {
			$tempdate = $startdate;
			$startdate = $enddate;
			$enddate = $tempdate;
		}

		redirect(page_url.'Accounts/paymentdashboard/'.$startdate."/".$enddate."/".$dfno);
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



/*
|--------------------------------------------------------------------------
| Receivables
|--------------------------------------------------------------------------
| What is actually owed, from the dispatch ledger. Deliberately NOT limited to
| running DFs: a machine that has shipped closes its DF while its money is
| still outstanding, so the whole receivable sits on closed DFs. Grouped by
| invoice number, because one invoice can be recorded against several DFs.
*/
private function build_finance_receivables()
{
	$today = date('Y-m-d');

	$this->db->select('
		t.df_id, t.invoice_no, t.invoice_date, t.invoice_amount, t.payment_received,
		t.balance_amount, t.due_date, t.payment_due_status, t.remarks, t.updated_on,
		df.df_no, df.df_description, df.df_status, IFNULL(df.on_hold, 0) AS on_hold,
		updater.title AS updater_title, updater.first_name AS updater_first_name,
		updater.last_name AS updater_last_name
	');
	$this->db->from('mcs_dispatch_report_tracking t');
	$this->db->join('df_release df', 'df.id = t.df_id', 'inner');
	$this->db->join('system_users updater', 'updater.user_id = t.updated_by', 'left');
	$this->db->order_by('t.due_date', 'ASC');
	$ledger_rows = $this->db->get()->result_array();

	$report = array(
		'invoices' => array(),
		'buckets' => $this->blank_receivable_buckets(),
		'summary' => array(
			'invoice_count' => 0, 'df_count' => 0,
			'invoiced_total' => 0, 'received_total' => 0, 'balance_total' => 0,
			'overdue_total' => 0, 'overdue_count' => 0,
			'not_due_total' => 0, 'no_due_date_total' => 0, 'no_due_date_count' => 0,
			'oldest_days' => 0, 'closed_df_balance' => 0
		),
		'insights' => array()
	);

	if (empty($ledger_rows)) {
		$report['summary'] = $this->finalise_receivable_summary($report['summary']);
		$report['insights'][] = 'The dispatch ledger has no entries yet, so no receivable can be reported.';
		return $report;
	}

	$company_map = $this->get_receivable_company_map($ledger_rows);

	/* One invoice can span several DFs. Fold them together so the balance is
	   counted once and the DFs it covers stay visible. */
	$grouped = array();
	foreach ($ledger_rows as $ledger_row) {
		$invoice_no = strtoupper(trim((string) $ledger_row['invoice_no']));
		$key = $invoice_no !== '' ? 'INV:' . $invoice_no : 'DF:' . (int) $ledger_row['df_id'];

		if (!isset($grouped[$key])) {
			$grouped[$key] = array('invoice_no' => $invoice_no, 'rows' => array());
		}
		$grouped[$key]['rows'][] = $ledger_row;
	}

	foreach ($grouped as $group) {
		$invoice = $this->build_receivable_invoice($group, $company_map, $today);

		if ($invoice['balance'] <= 0.01) {
			continue;
		}

		$report['invoices'][] = $invoice;
		$report['buckets'][$invoice['bucket_key']]['amount'] += $invoice['balance'];
		$report['buckets'][$invoice['bucket_key']]['count']++;

		$report['summary']['invoice_count']++;
		$report['summary']['df_count'] += count($invoice['df_nos']);
		$report['summary']['invoiced_total'] += $invoice['invoiced'];
		$report['summary']['received_total'] += $invoice['received'];
		$report['summary']['balance_total'] += $invoice['balance'];

		if ($invoice['is_overdue']) {
			$report['summary']['overdue_total'] += $invoice['balance'];
			$report['summary']['overdue_count']++;
			$report['summary']['oldest_days'] = max($report['summary']['oldest_days'], $invoice['days_overdue']);
		} elseif ($invoice['bucket_key'] === 'no_due_date') {
			$report['summary']['no_due_date_total'] += $invoice['balance'];
			$report['summary']['no_due_date_count']++;
		} else {
			$report['summary']['not_due_total'] += $invoice['balance'];
		}

		if ($invoice['df_closed']) {
			$report['summary']['closed_df_balance'] += $invoice['balance'];
		}
	}

	usort($report['invoices'], function ($left, $right) {
		if ((int) $left['sort_rank'] !== (int) $right['sort_rank']) {
			return ((int) $left['sort_rank'] > (int) $right['sort_rank']) ? -1 : 1;
		}
		return ((float) $left['balance'] > (float) $right['balance']) ? -1 : 1;
	});

	$report['summary'] = $this->finalise_receivable_summary($report['summary']);
	$report['buckets'] = $this->finalise_receivable_buckets($report['buckets']);
	$report['insights'] = $this->build_receivable_insights($report['summary'], $report['invoices']);

	return $report;
}

private function blank_receivable_buckets()
{
	$buckets = array();
	foreach (array(
		'not_due' => 'Not yet due',
		'no_due_date' => 'No due date set',
		'1_30' => 'Overdue 1-30 days',
		'31_60' => 'Overdue 31-60 days',
		'61_90' => 'Overdue 61-90 days',
		'90_plus' => 'Overdue 90+ days'
	) as $key => $label) {
		$buckets[$key] = array('key' => $key, 'label' => $label, 'amount' => 0, 'count' => 0);
	}

	return $buckets;
}

/* The ledger has no customer name of its own, so it is read off the PO. */
private function get_receivable_company_map($ledger_rows)
{
	$df_ids = array();
	foreach ($ledger_rows as $ledger_row) {
		$df_ids[] = (int) $ledger_row['df_id'];
	}

	$company_map = array();
	if (empty($df_ids)) {
		return $company_map;
	}

	$this->db->select('
		p.df_id, p.company_name, p.pono, p.order_value, p.customer_currency,
		marketing.title AS marketing_title, marketing.first_name AS marketing_first_name,
		marketing.last_name AS marketing_last_name
	');
	$this->db->from('poreceived p');
	$this->db->join('system_users marketing', 'marketing.user_id = p.added_by', 'left');
	$this->db->where_in('p.df_id', array_unique($df_ids));
	$this->db->order_by('p.id', 'DESC');

	foreach ($this->db->get()->result_array() as $po_row) {
		$df_key = (int) $po_row['df_id'];
		if (!isset($company_map[$df_key])) {
			$company_map[$df_key] = $po_row;
		}
	}

	return $company_map;
}

private function build_receivable_invoice($group, $company_map, $today)
{
	$rows = $group['rows'];
	$first = $rows[0];

	/* Rows sharing an invoice number carry the same invoice figures repeated per
	   DF, so take the maximum rather than the sum. */
	$invoiced = 0;
	$received = 0;
	$stored_balance = 0;
	$df_nos = array();
	$df_ids = array();
	$df_closed = true;

	foreach ($rows as $row) {
		$invoiced = max($invoiced, (float) $row['invoice_amount']);
		$received = max($received, (float) $row['payment_received']);
		$stored_balance = max($stored_balance, (float) $row['balance_amount']);
		$df_nos[] = strtoupper(trim((string) $row['df_no']));
		$df_ids[] = (int) $row['df_id'];
		if ((int) $row['df_status'] === 0) {
			$df_closed = false;
		}
	}

	$balance = round($invoiced - $received, 2);
	$due_date_raw = trim((string) $first['due_date']);
	$has_due_date = ($due_date_raw !== '' && $due_date_raw !== '0000-00-00');
	$days_overdue = 0;
	$is_overdue = false;

	if ($has_due_date && $balance > 0.01 && $due_date_raw < $today) {
		$is_overdue = true;
		$days_overdue = (int) floor((strtotime($today) - strtotime($due_date_raw)) / 86400);
	}

	if ($is_overdue) {
		if ($days_overdue <= 30) {
			$bucket_key = '1_30';
			$sort_rank = 3;
		} elseif ($days_overdue <= 60) {
			$bucket_key = '31_60';
			$sort_rank = 4;
		} elseif ($days_overdue <= 90) {
			$bucket_key = '61_90';
			$sort_rank = 5;
		} else {
			$bucket_key = '90_plus';
			$sort_rank = 6;
		}
	} elseif (!$has_due_date) {
		$bucket_key = 'no_due_date';
		$sort_rank = 2;
	} else {
		$bucket_key = 'not_due';
		$sort_rank = 1;
	}

	$po_row = isset($company_map[$df_ids[0]]) ? $company_map[$df_ids[0]] : array();
	$company_name = isset($po_row['company_name']) ? $this->format_allrunningdf_title_case($po_row['company_name']) : '';
	$marketing_person = isset($po_row['marketing_title'])
		? $this->format_allrunningdf_person_name($po_row['marketing_title'], $po_row['marketing_first_name'], $po_row['marketing_last_name'])
		: '';

	$updated_by = $this->format_allrunningdf_person_name(
		$first['updater_title'], $first['updater_first_name'], $first['updater_last_name']
	);

	$buckets = $this->blank_receivable_buckets();
	$issues = array();

	if (!$has_due_date && $balance > 0.01) {
		$issues[] = 'No payment due date is set on the ledger, so this balance cannot be aged or chased on a date.';
	}
	if (abs($stored_balance - $balance) > 0.01) {
		$issues[] = 'Ledger stores a balance of ' . $this->format_allrunningdf_money($stored_balance)
			. ' but invoice minus receipt is ' . $this->format_allrunningdf_money($balance) . '.';
	}
	if ($received > $invoiced + 0.01) {
		$issues[] = 'Receipts exceed the invoiced amount. Please verify the entries.';
	}
	if (count($df_ids) > 1) {
		$issues[] = 'This invoice is recorded against ' . count($df_ids) . ' DFs. It is counted once here.';
	}
	if (trim((string) $first['invoice_no']) === '') {
		$issues[] = 'No invoice number is recorded against this balance.';
	}

	return array(
		'invoice_no' => $group['invoice_no'] !== '' ? $group['invoice_no'] : 'Not numbered',
		'invoice_date' => $this->format_allrunningdf_date($first['invoice_date']),
		'df_nos' => array_values(array_unique($df_nos)),
		'df_ids' => array_values(array_unique($df_ids)),
		'df_label' => implode(', ', array_values(array_unique($df_nos))),
		'df_closed' => $df_closed,
		'df_status_label' => $df_closed ? 'DF closed' : 'DF running',
		'df_detail_url' => page_url . 'Dashboard/df_full_detail?df_id=' . (int) $df_ids[0],
		'company_name' => $company_name !== '' ? $company_name : 'Customer not mapped',
		'marketing_person' => $marketing_person !== '' ? $marketing_person : 'Not mapped',
		'invoiced' => round($invoiced, 2),
		'invoiced_display' => $this->format_allrunningdf_money($invoiced),
		'received' => round($received, 2),
		'received_display' => $this->format_allrunningdf_money($received),
		'balance' => $balance,
		'balance_display' => $this->format_allrunningdf_money($balance),
		'collection_percentage' => $invoiced > 0 ? round(($received / $invoiced) * 100) : 0,
		'due_date' => $has_due_date ? $this->format_allrunningdf_date($due_date_raw) : 'Not set',
		'due_date_raw' => $due_date_raw,
		'has_due_date' => $has_due_date,
		'is_overdue' => $is_overdue,
		'days_overdue' => $days_overdue,
		'bucket_key' => $bucket_key,
		'bucket_label' => $buckets[$bucket_key]['label'],
		'sort_rank' => $sort_rank,
		'due_status_label' => $this->get_allrunningdf_dispatch_due_state($first['payment_due_status']),
		'remarks' => trim((string) $first['remarks']),
		'last_updated_by' => $updated_by !== '' ? $updated_by : 'Not updated',
		'last_updated_on' => $this->format_allrunningdf_datetime($first['updated_on']),
		'issues' => $issues
	);
}

private function finalise_receivable_summary($summary)
{
	foreach (array(
		'invoiced_total', 'received_total', 'balance_total', 'overdue_total',
		'not_due_total', 'no_due_date_total', 'closed_df_balance'
	) as $money_field) {
		$summary[$money_field] = round($summary[$money_field], 2);
		$summary[$money_field . '_display'] = $this->format_allrunningdf_money($summary[$money_field]);
	}

	$summary['collection_percentage'] = $summary['invoiced_total'] > 0
		? round(($summary['received_total'] / $summary['invoiced_total']) * 100)
		: 0;

	return $summary;
}

private function finalise_receivable_buckets($buckets)
{
	foreach ($buckets as $key => $bucket) {
		$buckets[$key]['amount'] = round($bucket['amount'], 2);
		$buckets[$key]['amount_display'] = $this->format_allrunningdf_money($bucket['amount']);
	}

	return array_values($buckets);
}

private function build_receivable_insights($summary, $invoices)
{
	$insights = array();

	if ($summary['overdue_total'] > 0.01) {
		$insights[] = $summary['overdue_total_display'] . ' is overdue across ' . $summary['overdue_count']
			. ' invoice(s), the oldest by ' . $summary['oldest_days'] . ' days.';
	}

	if ($summary['no_due_date_total'] > 0.01) {
		$insights[] = $summary['no_due_date_total_display'] . ' is outstanding on ' . $summary['no_due_date_count']
			. ' invoice(s) with no due date recorded, so it cannot be aged. Setting the due dates is the first fix.';
	}

	if ($summary['closed_df_balance'] > 0.01) {
		$insights[] = $summary['closed_df_balance_display']
			. ' of the outstanding balance sits on DFs that are already closed. Closing a DF does not close its money.';
	}

	if (!empty($invoices) && $invoices[0]['balance'] > 0.01) {
		$insights[] = 'Largest single exposure is ' . $invoices[0]['balance_display'] . ' on invoice '
			. $invoices[0]['invoice_no'] . ' (' . $invoices[0]['company_name'] . ').';
	}

	if ($summary['balance_total'] <= 0.01) {
		$insights[] = 'Nothing is outstanding on the dispatch ledger.';
	}

	return $insights;
}

/*
|--------------------------------------------------------------------------
| Payment calendar
|--------------------------------------------------------------------------
| Forward view over a date window. Three things matter to a collections week:
| invoices falling due inside it, milestones that will become billable inside
| it, and whatever is already billable or overdue and has been carried in.
| Milestone data is taken from the running-DF report so all three finance
| screens agree with each other.
*/
private function build_finance_calendar($start_date, $end_date, $df_filter)
{
	$today = date('Y-m-d');

	$calendar = array(
		'due_invoices' => array(),
		'becoming_claimable' => array(),
		'ready_now' => array(),
		'overdue_carry_in' => array(),
		'summary' => array(
			'window_label' => date('d M Y', strtotime($start_date)) . ' to ' . date('d M Y', strtotime($end_date)),
			'window_days' => (int) floor((strtotime($end_date) - strtotime($start_date)) / 86400) + 1,
			'due_in_window' => 0, 'due_in_window_count' => 0,
			'becoming_claimable' => 0, 'becoming_claimable_count' => 0,
			'ready_now' => 0, 'ready_now_count' => 0,
			'overdue_carry_in' => 0, 'overdue_carry_in_count' => 0,
			'expected_collection' => 0
		),
		'insights' => array()
	);

	$report = $this->build_allrunningdf_report(array(
		'start_date' => 'ALL', 'end_date' => 'ALL',
		'df_id' => $df_filter, 'has_date_filter' => false
	));

	/* Collection is about invoices, and an invoice outlives its DF - so the two
	   money sections come from the receivables register (every DF), not from the
	   running-DF report. Billing sections below stay on running DFs, because
	   only a running DF can still earn a new milestone. */
	$receivables = $this->build_finance_receivables();

	foreach ($receivables['invoices'] as $invoice) {
		$context = array(
			'df_id' => $invoice['df_ids'][0],
			'df_no' => $invoice['df_label'],
			'company_name' => $invoice['company_name'],
			'marketing_person' => $invoice['marketing_person'],
			'df_detail_url' => $invoice['df_detail_url'],
			'invoice_no' => $invoice['invoice_no'],
			'amount' => $invoice['balance'],
			'amount_display' => $invoice['balance_display'],
			'due_date' => $invoice['due_date']
		);

		if ($invoice['is_overdue']) {
			$calendar['overdue_carry_in'][] = array_merge($context, array('days_overdue' => $invoice['days_overdue']));
			$calendar['summary']['overdue_carry_in'] += $invoice['balance'];
			$calendar['summary']['overdue_carry_in_count']++;
		} elseif ($invoice['has_due_date'] && $invoice['due_date_raw'] >= $start_date && $invoice['due_date_raw'] <= $end_date) {
			$calendar['due_invoices'][] = array_merge($context, array('due_date_raw' => $invoice['due_date_raw']));
			$calendar['summary']['due_in_window'] += $invoice['balance'];
			$calendar['summary']['due_in_window_count']++;
		}
	}

	foreach ($report['rows'] as $row) {
		$context = array(
			'df_id' => $row['df_id'],
			'df_no' => $row['df_no'],
			'company_name' => $row['company_name'],
			'marketing_person' => $row['marketing_person'],
			'df_detail_url' => $row['df_detail_url']
		);

		if ($row['unbilled_amount'] > 0.01) {
			$calendar['ready_now'][] = array_merge($context, array(
				'amount' => $row['unbilled_amount'],
				'amount_display' => $row['unbilled_amount_display'],
				'detail' => $row['claimable_count'] . ' milestone(s) complete and not invoiced.'
			));
			$calendar['summary']['ready_now'] += $row['unbilled_amount'];
			$calendar['summary']['ready_now_count']++;
		}

		foreach ($row['pos'] as $po) {
			foreach ($po['milestones'] as $milestone) {
				if ($milestone['is_claimable'] || $milestone['trigger_date_raw'] === '' || $milestone['trigger_date_raw'] === '0000-00-00') {
					continue;
				}
				if ($milestone['trigger_date_raw'] < $start_date || $milestone['trigger_date_raw'] > $end_date) {
					continue;
				}

				$calendar['becoming_claimable'][] = array_merge($context, array(
					'task_name' => $milestone['task_name'],
					'percentage_display' => $milestone['percentage_display'],
					'amount' => $milestone['amount'],
					'amount_display' => $milestone['amount_display'],
					'trigger_date' => $milestone['trigger_date'],
					'trigger_date_raw' => $milestone['trigger_date_raw'],
					'claim_status_key' => $milestone['claim_status_key'],
					'claim_status_label' => $milestone['claim_status_label'],
					'po_no' => $po['po_no'] !== '' ? $po['po_no'] : 'PO not numbered'
				));
				$calendar['summary']['becoming_claimable'] += $milestone['amount'];
				$calendar['summary']['becoming_claimable_count']++;
			}
		}
	}

	foreach (array('due_invoices', 'overdue_carry_in', 'ready_now') as $section) {
		usort($calendar[$section], function ($left, $right) {
			return ((float) $left['amount'] >= (float) $right['amount']) ? -1 : 1;
		});
	}
	usort($calendar['becoming_claimable'], function ($left, $right) {
		if ($left['trigger_date_raw'] !== $right['trigger_date_raw']) {
			return strcmp($left['trigger_date_raw'], $right['trigger_date_raw']);
		}
		return ((float) $left['amount'] >= (float) $right['amount']) ? -1 : 1;
	});

	$calendar['summary']['expected_collection'] = $calendar['summary']['due_in_window'] + $calendar['summary']['overdue_carry_in'];

	foreach (array('due_in_window', 'becoming_claimable', 'ready_now', 'overdue_carry_in', 'expected_collection') as $money_field) {
		$calendar['summary'][$money_field] = round($calendar['summary'][$money_field], 2);
		$calendar['summary'][$money_field . '_display'] = $this->format_allrunningdf_money($calendar['summary'][$money_field]);
	}

	$calendar['insights'] = $this->build_calendar_insights($calendar['summary'], $report['summary']);
	$calendar['running_df_summary'] = $report['summary'];

	return $calendar;
}

private function build_calendar_insights($summary, $running_summary)
{
	$insights = array();

	if ($summary['expected_collection'] > 0.01) {
		$insights[] = $summary['expected_collection_display'] . ' is collectable in this window: '
			. $summary['due_in_window_display'] . ' falling due plus ' . $summary['overdue_carry_in_display'] . ' already overdue.';
	} else {
		$insights[] = 'No invoice falls due in this window. Collection pressure here comes from billing, not from chasing.';
	}

	if ($summary['ready_now'] > 0.01) {
		$insights[] = $summary['ready_now_display'] . ' across ' . $summary['ready_now_count']
			. ' DF(s) is already claimable and not invoiced. Raising those invoices is what creates next month\'s collection.';
	}

	if ($summary['becoming_claimable'] > 0.01) {
		$insights[] = $summary['becoming_claimable_display'] . ' becomes claimable in this window across '
			. $summary['becoming_claimable_count'] . ' milestone(s), provided the triggering work lands on time.';
	}

	if (!empty($running_summary['slipped_amount']) && $running_summary['slipped_amount'] > 0.01) {
		$insights[] = $running_summary['slipped_amount_display']
			. ' of billing is already blocked behind milestone work that has passed its target date.';
	}

	return $insights;
}

}
