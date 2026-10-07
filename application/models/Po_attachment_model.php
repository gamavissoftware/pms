<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Received PO: attachment revisions and the change log.
 *
 * The PO file that is attached when an order is punched stays linked to that
 * order for good. Editing the order never needs a fresh upload; when someone
 * does attach a revised PO, the earlier one is kept as an older revision
 * instead of being thrown away.
 *
 * Every method degrades quietly when the tables are not there yet, so the PO
 * screens keep working on an installation where the migration has not been
 * run.
 */
class Po_attachment_model extends CI_Model
{
	/**
	 * Fields of `poreceived` that are worth telling the user about, in the
	 * order they should be read.
	 */
	private $tracked_fields = array(
		'company_name'                => 'Company Name',
		'pono'                        => 'PO Number',
		'podate'                      => 'PO Date',
		'financialyear'               => 'Financial Year',
		'payment_term'                => 'Payment Term',
		'customer_currency'           => 'Customer Currency',
		'amount_in_customer_currency' => 'Amount in Customer Currency',
		'order_value'                 => 'Order Value (INR)',
		'brand_tag'                   => 'Brand',
		'prof_inv_no'                 => 'Proforma Invoice No.',
		'orderhold'                   => 'Order Status',
	);

	/*
	 * po_attachment is deliberately absent: swapping the file is recorded as a
	 * revision event, which reads far better than two stored file names.
	 */

	public function __construct()
	{
		parent::__construct();
	}

	public function attachments_ready()
	{
		return $this->db->table_exists('poreceived_attachments');
	}

	public function logs_ready()
	{
		return $this->db->table_exists('poreceived_change_logs');
	}

	public function field_label($field)
	{
		return isset($this->tracked_fields[$field]) ? $this->tracked_fields[$field] : $field;
	}

	/* ------------------------------------------------------------------
	 * Attachments
	 * ------------------------------------------------------------------ */

	/**
	 * File the newly uploaded PO as the current revision and push the
	 * previous one down into the history.
	 *
	 * @return int revision number that was stored, or 0 when nothing was.
	 */
	public function add_revision($po_id, $stored_file_name, $original_file_name, $user_id, $remarks = '')
	{
		$po_id = (int) $po_id;
		$stored_file_name = trim((string) $stored_file_name);

		if ($po_id <= 0 || $stored_file_name === '' || !$this->attachments_ready()) {
			return 0;
		}

		$this->backfill_original($po_id);

		$revision = $this->next_revision_no($po_id);

		$this->db->where('po_id', $po_id)->update('poreceived_attachments', array('is_current' => 0));

		$this->db->insert('poreceived_attachments', array(
			'po_id'              => $po_id,
			'stored_file_name'   => $this->fit($stored_file_name, 255),
			'original_file_name' => $this->fit(($original_file_name !== '' ? $original_file_name : $stored_file_name), 255),
			'revision_no'        => $revision,
			'is_current'         => 1,
			'remarks'            => ($remarks !== '' ? $this->fit($remarks, 500) : null),
			'uploaded_by'        => ((int) $user_id > 0 ? (int) $user_id : null),
			'uploaded_on'        => date('Y-m-d H:i:s'),
		));

		return $revision;
	}

	/**
	 * An order punched before this module existed has a PO file but no
	 * revision history. Give it revision 1 the first time we touch it, so the
	 * original attachment is never lost behind a newer one.
	 */
	public function backfill_original($po_id)
	{
		$po_id = (int) $po_id;

		if ($po_id <= 0 || !$this->attachments_ready()) {
			return;
		}

		$already = $this->db->from('poreceived_attachments')->where('po_id', $po_id)->count_all_results();

		if ($already > 0) {
			return;
		}

		$order = $this->db->select('po_attachment, added_by, added_on')
			->from('poreceived')
			->where('id', $po_id)
			->get()
			->row();

		if (!$order || trim((string) $order->po_attachment) === '') {
			return;
		}

		$uploaded_on = (!empty($order->added_on) && $order->added_on > '1000-01-01 00:00:00')
			? $order->added_on
			: date('Y-m-d H:i:s');

		$this->db->insert('poreceived_attachments', array(
			'po_id'              => $po_id,
			'stored_file_name'   => $order->po_attachment,
			'original_file_name' => $order->po_attachment,
			'revision_no'        => 1,
			'is_current'         => 1,
			'remarks'            => 'Carried forward from the original order entry.',
			'uploaded_by'        => ((int) $order->added_by > 0 ? (int) $order->added_by : null),
			'uploaded_on'        => $uploaded_on,
		));
	}

	private function next_revision_no($po_id)
	{
		$row = $this->db->select_max('revision_no', 'max_revision')
			->from('poreceived_attachments')
			->where('po_id', (int) $po_id)
			->get()
			->row();

		return ($row && (int) $row->max_revision > 0) ? ((int) $row->max_revision + 1) : 1;
	}

	/**
	 * Every PO file attached to this order, newest first, with the name of
	 * whoever attached it.
	 */
	public function get_revisions($po_id)
	{
		$po_id = (int) $po_id;

		if ($po_id <= 0 || !$this->attachments_ready()) {
			return array();
		}

		return $this->db->select('a.*, u.title, u.first_name, u.last_name')
			->from('poreceived_attachments a')
			->join('system_users u', 'u.user_id = a.uploaded_by', 'left')
			->where('a.po_id', $po_id)
			->order_by('a.revision_no', 'desc')
			->order_by('a.id', 'desc')
			->get()
			->result();
	}

	/**
	 * How many PO files are on record for each of the given orders.
	 *
	 * @return array po_id => count
	 */
	public function revision_counts(array $po_ids)
	{
		$po_ids = array_filter(array_map('intval', $po_ids));

		if (empty($po_ids) || !$this->attachments_ready()) {
			return array();
		}

		$rows = $this->db->select('po_id, COUNT(*) AS revision_count', false)
			->from('poreceived_attachments')
			->where_in('po_id', $po_ids)
			->group_by('po_id')
			->get()
			->result();

		$counts = array();

		foreach ($rows as $row) {
			$counts[(int) $row->po_id] = (int) $row->revision_count;
		}

		return $counts;
	}

	/* ------------------------------------------------------------------
	 * Change log
	 * ------------------------------------------------------------------ */

	/**
	 * Compare the order as it was against what is being saved and return one
	 * entry per field that genuinely changed.
	 */
	public function diff($before, array $after)
	{
		$changes = array();

		if (!$before) {
			return $changes;
		}

		foreach ($after as $field => $new_value) {
			if (!isset($this->tracked_fields[$field]) || !property_exists($before, $field)) {
				continue;
			}

			$old_value = $before->$field;

			if ($this->same_value($field, $old_value, $new_value)) {
				continue;
			}

			$changes[] = array(
				'field_name'  => $field,
				'field_label' => $this->tracked_fields[$field],
				'old_value'   => $this->readable($field, $old_value),
				'new_value'   => $this->readable($field, $new_value),
			);
		}

		return $changes;
	}

	/**
	 * Amounts differ by formatting far more often than by value, so compare
	 * them as numbers rather than as strings.
	 */
	private function same_value($field, $old_value, $new_value)
	{
		$numeric_fields = array('order_value', 'amount_in_customer_currency');

		if (in_array($field, $numeric_fields, true)) {
			return abs((float) $old_value - (float) $new_value) < 0.005;
		}

		$integer_fields = array('financialyear', 'payment_term', 'brand_tag', 'orderhold');

		if (in_array($field, $integer_fields, true)) {
			return (int) $old_value === (int) $new_value;
		}

		return trim((string) $old_value) === trim((string) $new_value);
	}

	/**
	 * Turn a stored value into something a person can read in the log.
	 */
	private function readable($field, $value)
	{
		$value = (string) $value;

		if (trim($value) === '' || $value === '0000-00-00') {
			return '';
		}

		switch ($field) {
			case 'podate':
				return date('d-m-Y', strtotime($value));

			case 'financialyear':
				return $this->lookup('financialyear', 'id', 'year', $value);

			case 'payment_term':
				return $this->lookup('payment_terms', 'id', 'payment_terms', $value);

			case 'brand_tag':
				return $this->lookup('company_brand', 'id', 'name', $value);

			case 'order_value':
			case 'amount_in_customer_currency':
				return number_format((float) $value, 2);

			case 'orderhold':
				return ((int) $value === 1) ? 'Cancelled / On Hold' : 'Active';
		}

		return $value;
	}

	private function lookup($table, $key_column, $value_column, $key)
	{
		if ((int) $key <= 0 || !$this->db->table_exists($table)) {
			return (string) $key;
		}

		$row = $this->db->select($value_column)->from($table)->where($key_column, (int) $key)->get()->row();

		return ($row && trim((string) $row->$value_column) !== '') ? $row->$value_column : (string) $key;
	}

	/**
	 * Write one log row per changed field.
	 */
	public function log_changes($po_id, $action, array $changes, $user_id, $remarks = '')
	{
		$po_id = (int) $po_id;

		if ($po_id <= 0 || empty($changes) || !$this->logs_ready()) {
			return;
		}

		$now = date('Y-m-d H:i:s');
		$rows = array();

		foreach ($changes as $change) {
			$rows[] = array(
				'po_id'       => $po_id,
				'action'      => $this->fit($action, 50),
				'field_name'  => isset($change['field_name']) ? $this->fit($change['field_name'], 100) : null,
				'field_label' => isset($change['field_label']) ? $this->fit($change['field_label'], 255) : null,
				'old_value'   => isset($change['old_value']) ? $change['old_value'] : null,
				'new_value'   => isset($change['new_value']) ? $change['new_value'] : null,
				'remarks'     => ($remarks !== '' ? $this->fit($remarks, 500) : null),
				'changed_by'  => ((int) $user_id > 0 ? (int) $user_id : null),
				'changed_on'  => $now,
			);
		}

		$this->db->insert_batch('poreceived_change_logs', $rows);
	}

	/**
	 * Keep a value inside its column so a long company name or file name can
	 * never fail the insert on a strict-mode server.
	 */
	private function fit($value, $length)
	{
		$value = (string) $value;

		return (mb_strlen($value) > $length) ? mb_substr($value, 0, $length - 3) . '...' : $value;
	}

	/**
	 * A single log entry that is not a field edit - order created, PO
	 * revision attached, order cancelled.
	 */
	public function log_event($po_id, $action, $description, $user_id, $remarks = '')
	{
		$this->log_changes($po_id, $action, array(array(
			'field_name'  => null,
			'field_label' => $description,
			'old_value'   => null,
			'new_value'   => null,
		)), $user_id, $remarks);
	}

	public function get_logs($po_id)
	{
		$po_id = (int) $po_id;

		if ($po_id <= 0 || !$this->logs_ready()) {
			return array();
		}

		return $this->db->select('l.*, u.title, u.first_name, u.last_name')
			->from('poreceived_change_logs l')
			->join('system_users u', 'u.user_id = l.changed_by', 'left')
			->where('l.po_id', $po_id)
			->order_by('l.changed_on', 'desc')
			->order_by('l.id', 'desc')
			->get()
			->result();
	}

	/**
	 * Person name for a log or revision row, already title cased.
	 */
	public function person_name($row)
	{
		$name = trim(
			trim((string) (isset($row->title) ? $row->title : '')) . ' ' .
			trim((string) (isset($row->first_name) ? $row->first_name : '')) . ' ' .
			trim((string) (isset($row->last_name) ? $row->last_name : ''))
		);

		return ($name !== '') ? ucwords(strtolower($name)) : 'System';
	}
}
