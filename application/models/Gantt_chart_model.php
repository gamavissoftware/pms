<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Gantt_chart_model
 *
 * Builds the whole DF-wise Gantt board in a fixed number of queries (7), no
 * matter how many departments, tasks or weeks the DF spans. The old chart
 * (charts/finalgantchartwithDetails.php) queried inside the week loop, so the
 * cost was rows x weeks -- tens of thousands of round trips for one page.
 *
 * Everything this model returns is already positioned for rendering: bar
 * offsets are percentages of the timeline width, so the view is pure markup
 * and the browser can zoom the chart by resizing one container.
 *
 * ONE BOARD, TWO SWITCHES
 * -----------------------
 * The four charts the old app kept as four separate views are really two
 * independent choices, so they are options here rather than files:
 *
 *   group: milestone | task | department      rows
 *   scale: week | day                         columns
 *
 *   old "Day wise"            = task + day
 *   old "Week wise"           = task + week
 *   old "Department wise"     = department + day
 *   old "Department & week"   = milestone + week   (the default)
 *
 * Any other combination works too, which is the point of doing it this way.
 */
class Gantt_chart_model extends CI_Model
{
	/** Task statuses in task_department_wise_scheduling */
	const STATUS_DONE = 1;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Whether a date column holds a real date (the app stores 0000-00-00 for "unset").
	 */
	private function has_date($value)
	{
		if (empty($value)) {
			return FALSE;
		}

		$value = substr((string) $value, 0, 10);

		return ($value !== '0000-00-00' && strtotime($value) !== FALSE);
	}

	private function as_date($value)
	{
		return $this->has_date($value) ? date('Y-m-d', strtotime((string) $value)) : '';
	}

	private function title_case($value)
	{
		$value = trim((string) $value);

		return ($value === '') ? '' : ucwords(strtolower($value));
	}

	/**
	 * Whole days from $from to $to. Negative when $to is earlier.
	 */
	private function day_diff($from, $to)
	{
		return (int) round((strtotime($to . ' 00:00:00') - strtotime($from . ' 00:00:00')) / 86400);
	}

	/** Row groupings and column scales this board understands. */
	public static function groups()
	{
		return array(
			'milestone'  => 'Milestone wise',
			'department' => 'Department wise',
			'task'       => 'Task wise',
		);
	}

	public static function scales()
	{
		return array(
			'week' => 'Week columns',
			'day'  => 'Day columns',
		);
	}

	/**
	 * Normalise whatever came off the query string into a usable view spec.
	 *
	 * @param  array $options
	 * @return array
	 */
	public function view_options($options = array())
	{
		$group = isset($options['group']) ? strtolower(trim((string) $options['group'])) : '';
		$scale = isset($options['scale']) ? strtolower(trim((string) $options['scale'])) : '';

		if (!array_key_exists($group, self::groups())) { $group = 'milestone'; }
		if (!array_key_exists($scale, self::scales())) { $scale = 'week'; }

		return array(
			'group'      => $group,
			'scale'      => $scale,
			'department' => isset($options['department']) ? (int) $options['department'] : 0,
		);
	}

	/**
	 * The complete board for one DF.
	 *
	 * @param  int   $df_id
	 * @param  array $options  group | scale | department, see view_options()
	 * @return array
	 */
	public function get_board($df_id, $options = array())
	{
		$df_id   = (int) $df_id;
		$options = $this->view_options($options);

		$board = array(
			'found'    => FALSE,
			'df_id'    => $df_id,
			'message'  => '',
			'options'  => $options,
			'header'   => array(),
			'timeline' => array(),
			'rows'     => array(),
			'stats'    => array(),
			'changes'  => array(),
			'departments' => array(),
		);

		if ($df_id <= 0) {
			$board['message'] = 'No DF selected.';

			return $board;
		}

		$header = $this->get_header($df_id);

		if ($header === NULL) {
			$board['message'] = 'DF #' . $df_id . ' was not found.';

			return $board;
		}

		$board['header'] = $header;

		// ------------------------------------------------------------------
		// 1. Every scheduled task on this DF, with its task, department and
		//    owner, in one query. All three groupings are built from this.
		// ------------------------------------------------------------------
		$schedule    = array();
		$departments = array();

		// task_frequency 2 is the recurring "DF Meeting", which the old day and
		// week charts excluded and which is not a schedulable step. It is not
		// mapped into any milestone either, so dropping it here changes nothing
		// for the milestone grouping.
		$sql = 'SELECT sc.id, sc.taskid, sc.department_id, sc.start_date, sc.end_date,
		               sc.task_status, sc.task_completed_on, sc.remarks, sc.on_hold, sc.mark_hold,
		               t.task_name, t.sortorder AS task_order,
		               d.department, d.color, d.sort_order AS dept_order,
		               u.title AS owner_title, u.first_name AS owner_first, u.last_name AS owner_last
		        FROM task_department_wise_scheduling sc
		        LEFT JOIN task_management t ON t.task_id = sc.taskid
		        LEFT JOIN departments d ON d.department_id = sc.department_id
		        LEFT JOIN system_users u ON u.user_id = sc.assigned_user
		        WHERE sc.df_id = ?
		          AND (t.task_frequency IS NULL OR t.task_frequency <> 2)';

		$binds = array($df_id);

		foreach ($this->db->query($sql, $binds)->result() as $row) {
			$schedule[(int) $row->taskid] = $row;

			$department_id = (int) $row->department_id;

			// The department picker offers what this DF actually touches, not
			// every department in the company.
			if (!isset($departments[$department_id])) {
				$departments[$department_id] = array(
					'department_id' => $department_id,
					'department'    => $this->title_case($row->department),
					'color'         => $this->safe_color($row->color),
					'order'         => (int) $row->dept_order,
				);
			}
		}

		if (empty($schedule)) {
			$board['message'] = 'No task schedule has been created for this DF yet.';

			return $board;
		}

		uasort($departments, function ($a, $b) {
			return strcmp($a['department'], $b['department']);
		});

		$board['departments'] = array_values($departments);

		// A department filter narrows the whole board -- rows, timeline and
		// the figures above it -- so everything on screen agrees.
		if ($options['department'] > 0) {
			foreach ($schedule as $task_id => $row) {
				if ((int) $row->department_id !== $options['department']) {
					unset($schedule[$task_id]);
				}
			}

			if (empty($schedule)) {
				$board['message'] = 'That department has no scheduled tasks on this DF.';

				return $board;
			}
		}

		// ------------------------------------------------------------------
		// 2. Milestone definitions and their task mapping. Only the milestone
		//    grouping needs these, so the other two views cost two queries less.
		// ------------------------------------------------------------------
		$masters = array();
		$mapping = array();

		if ($options['group'] === 'milestone') {

			$masters = $this->db->query(
				'SELECT m.id, m.department_id, m.taskname, m.main_task_id, m.sortorder,
				        d.department, d.color
				 FROM mdgantchartmaster m
				 LEFT JOIN departments d ON d.department_id = m.department_id
				 WHERE m.status = 1
				 ORDER BY m.sortorder ASC, m.id ASC'
			)->result();

			foreach ($this->db->query(
				'SELECT s.report_id, s.task_id
				 FROM sharmajitaskmapping s
				 ORDER BY s.id ASC'
			)->result() as $row) {
				$mapping[(int) $row->report_id][] = (int) $row->task_id;
			}
		}

		// ------------------------------------------------------------------
		// 4. Ticket counts per department, in one grouped query.
		// ------------------------------------------------------------------
		$tickets = array();

		foreach ($this->db->query(
			'SELECT department_id,
			        COUNT(*) AS total_tickets,
			        SUM(CASE WHEN ticket_status = 0 THEN 1 ELSE 0 END) AS open_tickets
			 FROM communication_ticket_system
			 WHERE df_id = ?
			 GROUP BY department_id',
			array($df_id)
		)->result() as $row) {
			$tickets[(int) $row->department_id] = array(
				'total' => (int) $row->total_tickets,
				'open'  => (int) $row->open_tickets,
			);
		}

		// ------------------------------------------------------------------
		// Assemble rows for the chosen grouping, purely in PHP.
		// ------------------------------------------------------------------
		$today = date('Y-m-d');

		switch ($options['group']) {

			case 'task':
				$rows = $this->rows_by_task($schedule, $tickets, $today);
				break;

			case 'department':
				$rows = $this->rows_by_department($schedule, $tickets, $today);
				break;

			case 'milestone':
			default:
				$rows = $this->rows_by_milestone($masters, $mapping, $schedule, $tickets, $today);
				break;
		}

		if (empty($rows)) {
			$board['message'] = ($options['group'] === 'milestone')
				? 'This DF has a task schedule but no dated milestones to plot. Try the task or department view.'
				: 'This DF has a task schedule but nothing dated to plot.';

			return $board;
		}

		list($span_min, $span_max) = $this->span($rows);

		if ($span_min === '' || $span_max === '') {
			$board['message'] = 'None of the scheduled tasks on this DF carry dates.';

			return $board;
		}

		// ------------------------------------------------------------------
		// Timeline, then bar geometry for every row.
		// ------------------------------------------------------------------
		$timeline = $this->build_timeline($span_min, $span_max, $today, $options['scale']);

		foreach ($rows as &$group) {
			$group['ranges'] = $this->bar_ranges($group);
			$group['bars']   = $this->build_bars($group['ranges'], $timeline);

			foreach ($group['children'] as &$child) {
				$child['ranges'] = $this->bar_ranges($child);
				$child['bars']   = $this->build_bars($child['ranges'], $timeline);
			}
			unset($child);
		}
		unset($group);

		$board['found']    = TRUE;
		$board['timeline'] = $timeline;
		$board['rows']     = $rows;
		$board['stats']    = $this->build_stats($rows, $schedule, $tickets, $options, $today);
		$board['changes']  = $this->get_change_control($df_id);

		return $board;
	}

	/**
	 * The earliest planned start and the latest of every planned or actual
	 * end across a row set -- the span the timeline has to cover.
	 */
	private function span(array $rows)
	{
		$min = '';
		$max = '';

		foreach ($rows as $row) {

			if ($row['planned_start'] !== '' && ($min === '' || $row['planned_start'] < $min)) {
				$min = $row['planned_start'];
			}

			foreach (array($row['planned_end'], $row['actual_end']) as $candidate) {
				if ($candidate !== '' && ($max === '' || $candidate > $max)) {
					$max = $candidate;
				}
			}
		}

		return array($min, $max);
	}

	/**
	 * Curated milestones from mdgantchartmaster, each owning the mapped tasks
	 * scheduled against its own department. This is the default view and the
	 * only one that shows a hand-picked subset rather than everything.
	 */
	private function rows_by_milestone($masters, array $mapping, array $schedule, array $tickets, $today)
	{
		$rows   = array();
		$serial = 0;

		foreach ($masters as $master) {

			$report_id  = (int) $master->id;
			$department = (int) $master->department_id;

			if (empty($mapping[$report_id])) {
				continue;
			}

			$children = array();

			foreach ($mapping[$report_id] as $task_id) {

				if (!isset($schedule[$task_id])) {
					continue;
				}

				$sc = $schedule[$task_id];

				// A milestone owns the tasks scheduled against its own department.
				if ($department > 0 && (int) $sc->department_id !== $department) {
					continue;
				}

				$children[] = $this->build_task_row($sc, $today);
			}

			if (empty($children)) {
				continue;
			}

			$serial++;

			$group = $this->roll_up($children, $today);

			// Prefer the milestone task's own sign-off date when it is recorded.
			$main_task_id = (int) $master->main_task_id;

			if ($group['is_done'] && isset($schedule[$main_task_id])) {
				$main_done_on = $this->as_date($schedule[$main_task_id]->task_completed_on);

				if ($main_done_on !== '' && $main_done_on > $group['completed_on']) {
					$group = $this->roll_up($children, $today, $main_done_on);
				}
			}

			$rows[] = array_merge($group, array(
				'level'         => 'milestone',
				'no'            => $serial,
				'row_id'        => 'm' . $report_id,
				'main_task_id'  => $main_task_id,
				'department_id' => $department,
				'department'    => $this->title_case($master->department),
				'color'         => $this->safe_color($master->color),
				'name'          => $this->title_case($master->taskname),
				'owner'         => '',
				'remarks'       => '',
				'tickets'       => $this->ticket_count($tickets, $department),
				'children'      => $children,
			));
		}

		return $rows;
	}

	/**
	 * One row per department that has work on this DF, owning every task
	 * booked to it. Ordered by when the department's work actually starts,
	 * which makes the chart read as a cascade -- departments.sort_order is 0
	 * for several live departments (Marketing among them), so ordering on it
	 * would drop them to the bottom regardless of when they run.
	 */
	private function rows_by_department(array $schedule, array $tickets, $today)
	{
		$buckets = array();

		foreach ($schedule as $sc) {
			$buckets[(int) $sc->department_id][] = $sc;
		}

		$rows = array();

		foreach ($buckets as $department_id => $tasks) {

			$children = array();

			foreach ($tasks as $sc) {
				$children[] = $this->build_task_row($sc, $today);
			}

			usort($children, array($this, 'compare_by_date'));

			$first = $tasks[0];

			// The department name would otherwise sit in two adjacent columns.
			// Who is actually carrying the work is the more useful thing to
			// put next to it.
			$owners = array();

			foreach ($children as $child) {
				if ($child['owner'] !== '' && !in_array($child['owner'], $owners, TRUE)) {
					$owners[] = $child['owner'];
				}
			}

			$rows[] = array_merge($this->roll_up($children, $today), array(
				'level'         => 'department',
				'row_id'        => 'd' . $department_id,
				'department_id' => $department_id,
				'department'    => $this->title_case($first->department),
				'color'         => $this->safe_color($first->color),
				'name'          => $this->title_case($first->department),
				'owner'         => implode(', ', $owners),
				'remarks'       => '',
				'tickets'       => $this->ticket_count($tickets, $department_id),
				'children'      => $children,
			));
		}

		usort($rows, array($this, 'compare_by_date'));

		return $this->number($rows);
	}

	/**
	 * Every scheduled task as its own row, in date order -- the flat view the
	 * old day and week charts showed.
	 */
	private function rows_by_task(array $schedule, array $tickets, $today)
	{
		$rows = array();

		foreach ($schedule as $sc) {

			$department_id = (int) $sc->department_id;

			$rows[] = array_merge($this->build_task_row($sc, $today), array(
				'level'      => 'task',
				'row_id'     => 't' . (int) $sc->taskid,
				'department' => $this->title_case($sc->department),
				'color'      => $this->safe_color($sc->color),
				'tickets'    => $this->ticket_count($tickets, $department_id),
				'children'   => array(),
			));
		}

		usort($rows, array($this, 'compare_by_date'));

		return $this->number($rows);
	}

	/**
	 * Chart order: when the work is planned to start, then when it is due,
	 * then by name so the sequence is stable between page loads.
	 */
	private function compare_by_date($a, $b)
	{
		if ($a['planned_start'] !== $b['planned_start']) {
			if ($a['planned_start'] === '') { return 1; }
			if ($b['planned_start'] === '') { return -1; }

			return strcmp($a['planned_start'], $b['planned_start']);
		}

		if ($a['planned_end'] !== $b['planned_end']) {
			return strcmp($a['planned_end'], $b['planned_end']);
		}

		return strcmp($a['name'], $b['name']);
	}

	private function number(array $rows)
	{
		$serial = 0;

		foreach ($rows as &$row) {
			$row['no'] = ++$serial;
		}
		unset($row);

		return $rows;
	}

	private function ticket_count(array $tickets, $department_id)
	{
		return isset($tickets[$department_id])
			? $tickets[$department_id]
			: array('total' => 0, 'open' => 0);
	}

	/**
	 * DF / PO / customer strip. One query.
	 */
	private function get_header($df_id)
	{
		$query = $this->db->query(
			'SELECT a.id, a.df_no, a.added_on, a.df_description,
			        b.company_name, b.pono, b.podate,
			        c.title AS m_title, c.first_name AS m_first, c.last_name AS m_last
			 FROM df_release a
			 LEFT JOIN poreceived b ON b.df_id = a.id
			 LEFT JOIN system_users c ON c.user_id = a.added_by
			 WHERE a.id = ?
			 LIMIT 1',
			array((int) $df_id)
		);

		if ($query->num_rows() === 0) {
			return NULL;
		}

		$row = $query->row();

		return array(
			'df_id'       => (int) $row->id,
			'df_no'       => strtoupper(trim((string) $row->df_no)),
			'description' => $this->title_case($row->df_description),
			'company'     => $this->title_case($row->company_name),
			'po_no'       => trim((string) $row->pono),
			'po_date'     => $this->has_date($row->podate) ? date('d M Y', strtotime($row->podate)) : '-',
			'df_date'     => $this->has_date($row->added_on) ? date('d M Y', strtotime($row->added_on)) : '-',
			'marketing'   => $this->title_case($row->m_title . ' ' . $row->m_first . ' ' . $row->m_last),
		);
	}

	/**
	 * One scheduled task -> a plottable row.
	 *
	 * Planned window is what was scheduled. The actual window starts when the
	 * task was due to start and ends on completion (or today, while it runs).
	 * Anything past the planned end is the overrun.
	 */
	private function build_task_row($sc, $today)
	{
		$planned_start = $this->as_date($sc->start_date);
		$planned_end   = $this->as_date($sc->end_date);
		$completed_on  = ((int) $sc->task_status === self::STATUS_DONE) ? $this->as_date($sc->task_completed_on) : '';
		$is_done       = ($completed_on !== '');
		$on_hold       = ((int) $sc->on_hold === 1 || (int) $sc->mark_hold === 1);

		if ($planned_end === '' && $planned_start !== '') {
			$planned_end = $planned_start;
		}

		if ($planned_start === '' && $planned_end !== '') {
			$planned_start = $planned_end;
		}

		$actual_start = '';
		$actual_end   = '';

		if ($planned_start !== '') {
			if ($is_done) {
				$actual_start = $planned_start;
				$actual_end   = max($completed_on, $planned_start);
			} elseif ($today >= $planned_start) {
				$actual_start = $planned_start;
				$actual_end   = $today;
			}
		}

		$delay_days = 0;

		if ($planned_end !== '') {
			$measure_against = $is_done ? $completed_on : $today;
			$delay_days      = max(0, $this->day_diff($planned_end, $measure_against));
		}

		return array(
			'kind'          => 'task',
			'level'         => 'task',
			'row_id'        => 't' . (int) $sc->taskid,
			'task_id'       => (int) $sc->taskid,
			'record_id'     => (int) $sc->id,
			'name'          => $this->title_case($sc->task_name),
			'owner'         => $this->title_case($sc->owner_title . ' ' . $sc->owner_first . ' ' . $sc->owner_last),
			'remarks'       => trim((string) $sc->remarks),
			'department_id' => (int) $sc->department_id,
			'planned_start' => $planned_start,
			'planned_end'   => $planned_end,
			'actual_start'  => $actual_start,
			'actual_end'    => $actual_end,
			'completed_on'  => $completed_on,
			'is_done'       => $is_done,
			'on_hold'       => $on_hold,
			'delay_days'    => $delay_days,
			'done_count'    => $is_done ? 1 : 0,
			'total_count'   => 1,
			'progress'      => $is_done ? 100.0 : 0.0,
			'status'        => $this->derive_status($is_done, $delay_days, $planned_start, $on_hold, $today),
		);
	}

	/**
	 * The envelope of a set of child tasks: earliest start, latest end, how
	 * many are closed, and when the last one actually landed.
	 *
	 * Shared by the milestone and department groupings -- the only difference
	 * between them is which tasks get handed in and what the row is called.
	 *
	 * @param  array  $children
	 * @param  string $today
	 * @param  string $completed_override  a recorded sign-off date that beats
	 *                                     the latest child completion
	 * @return array
	 */
	private function roll_up(array $children, $today, $completed_override = '')
	{
		$planned_start = '';
		$planned_end   = '';
		$last_done_on  = '';
		$done_count    = 0;
		$on_hold       = FALSE;

		foreach ($children as $child) {

			if ($child['planned_start'] !== '' && ($planned_start === '' || $child['planned_start'] < $planned_start)) {
				$planned_start = $child['planned_start'];
			}

			if ($child['planned_end'] !== '' && ($planned_end === '' || $child['planned_end'] > $planned_end)) {
				$planned_end = $child['planned_end'];
			}

			if ($child['is_done']) {
				$done_count++;

				if ($child['completed_on'] > $last_done_on) {
					$last_done_on = $child['completed_on'];
				}
			}

			if ($child['on_hold']) {
				$on_hold = TRUE;
			}
		}

		$total_count = count($children);
		$is_done     = ($total_count > 0 && $done_count === $total_count);

		if ($is_done && $completed_override !== '' && $completed_override > $last_done_on) {
			$last_done_on = $completed_override;
		}

		$completed_on = $is_done ? $last_done_on : '';

		$actual_start = '';
		$actual_end   = '';

		if ($planned_start !== '') {
			if ($is_done && $completed_on !== '') {
				$actual_start = $planned_start;
				$actual_end   = max($completed_on, $planned_start);
			} elseif ($today >= $planned_start) {
				$actual_start = $planned_start;
				$actual_end   = $today;
			}
		}

		$delay_days = 0;

		if ($planned_end !== '') {
			$measure_against = ($is_done && $completed_on !== '') ? $completed_on : $today;
			$delay_days      = max(0, $this->day_diff($planned_end, $measure_against));
		}

		return array(
			'kind'          => 'group',
			'planned_start' => $planned_start,
			'planned_end'   => $planned_end,
			'actual_start'  => $actual_start,
			'actual_end'    => $actual_end,
			'completed_on'  => $completed_on,
			'is_done'       => $is_done,
			'on_hold'       => $on_hold,
			'delay_days'    => $delay_days,
			'done_count'    => $done_count,
			'total_count'   => $total_count,
			'progress'      => ($total_count > 0) ? round(($done_count * 100) / $total_count, 1) : 0.0,
			'status'        => $this->derive_status($is_done, $delay_days, $planned_start, $on_hold, $today),
		);
	}

	private function derive_status($is_done, $delay_days, $planned_start, $on_hold, $today)
	{
		if ($is_done) {
			return ($delay_days > 0) ? 'late' : 'done';
		}

		if ($on_hold) {
			return 'hold';
		}

		if ($delay_days > 0) {
			return 'overdue';
		}

		if ($planned_start !== '' && $today >= $planned_start) {
			return 'running';
		}

		return 'upcoming';
	}

	/**
	 * Only accept a hex colour out of the departments table.
	 */
	private function safe_color($color)
	{
		$color = trim((string) $color);

		return preg_match('/^#[0-9a-fA-F]{3,8}$/', $color) ? $color : '#94a3b8';
	}

	/**
	 * The column axis, plus the month bands that sit above it.
	 *
	 * Week scale anchors on ISO Mondays; day scale is one column per calendar
	 * day. Either way bar positions stay day-based (see segment()), so a
	 * two-day task is never drawn as a whole week -- the scale changes the
	 * gridlines and the headings, not the geometry.
	 *
	 * @param  string $scale  'week' | 'day'
	 */
	private function build_timeline($span_min, $span_max, $today, $scale = 'week')
	{
		$by_week = ($scale !== 'day');

		$first = new DateTime($span_min);
		$last  = new DateTime($span_max);

		if ($by_week) {
			$first->modify('monday this week');
			$last->modify('sunday this week');
		}

		// Always keep today on the canvas so the marker means something.
		$today_dt = new DateTime($today);

		if ($today_dt < $first) {
			$first = clone $today_dt;
			if ($by_week) { $first->modify('monday this week'); }
		}

		if ($today_dt > $last) {
			$last = clone $today_dt;
			if ($by_week) { $last->modify('sunday this week'); }
		}

		$total_days = (int) $first->diff($last)->days + 1;
		$step       = $by_week ? 7 : 1;

		$columns = array();
		$months  = array();

		$cursor = clone $first;

		while ($cursor <= $last) {

			$month_label = $cursor->format('M y');
			$index       = count($months) - 1;

			if ($index < 0 || $months[$index]['label'] !== $month_label) {
				$months[] = array('label' => $month_label, 'span' => 1);
			} else {
				$months[$index]['span']++;
			}

			$col_end = clone $cursor;

			if ($by_week) {
				$col_end->modify('+6 days');
			}

			$columns[] = array(
				'from'       => $cursor->format('Y-m-d'),
				'to'         => $col_end->format('Y-m-d'),
				'label'      => $by_week ? ('W' . $cursor->format('W')) : $cursor->format('d'),
				'sub'        => $by_week ? $cursor->format('d M') : $cursor->format('D'),
				'is_current' => ($today >= $cursor->format('Y-m-d') && $today <= $col_end->format('Y-m-d')),
				'is_weekend' => (!$by_week && (int) $cursor->format('N') >= 6),
			);

			$cursor->modify('+' . $step . ' days');
		}

		$today_offset = $this->day_diff($first->format('Y-m-d'), $today);
		$today_pct    = NULL;

		if ($today_offset >= 0 && $today_offset < $total_days) {
			$today_pct = round((($today_offset + 0.5) / $total_days) * 100, 4);
		}

		return array(
			'scale'      => $by_week ? 'week' : 'day',
			'start'      => $first->format('Y-m-d'),
			'end'        => $last->format('Y-m-d'),
			'total_days' => $total_days,
			'col_days'   => $step,
			'columns'    => $columns,
			'months'     => $months,
			'today_pct'  => $today_pct,
		);
	}

	/**
	 * Resolve a row into the three date windows that get drawn:
	 *
	 *   plan     - the scheduled window, drawn as the track
	 *   progress - work actually completed
	 *   overrun  - time spent past the planned end (to completion, or to today)
	 *   marker   - the day the row actually finished
	 *
	 * This is the single definition of what the chart shows. The screen turns
	 * these into percentages, the Excel export buckets them into week columns
	 * and the PDF draws them as millimetres, so all three must agree.
	 *
	 * Drawing progress from the completion count rather than from elapsed time
	 * matters: a milestone that is 2/6 done but sitting on its planned window
	 * must not look like a full green bar.
	 */
	private function bar_ranges($row)
	{
		$ranges = array('plan' => NULL, 'progress' => NULL, 'overrun' => NULL, 'marker' => NULL);

		if ($row['planned_start'] === '' || $row['planned_end'] === '') {
			return $ranges;
		}

		$ranges['plan'] = array($row['planned_start'], $row['planned_end']);

		if ($row['progress'] > 0) {

			if ($row['is_done'] && $row['completed_on'] !== '') {
				// Finished: the window the work actually occupied, so a milestone
				// closed early reads as early instead of filling its whole plan.
				// The unused tail stays as the light planned track.
				$end = min($row['completed_on'], $row['planned_end']);
				$end = max($end, $row['planned_start']);

				$ranges['progress'] = array($row['planned_start'], $end);
			} else {
				// Still running: we know how much is closed, not exactly when each
				// piece happened, so fill that share of the track. Snapped to whole
				// days so the week-column and millimetre renderings can match.
				$days = $this->day_diff($row['planned_start'], $row['planned_end']) + 1;
				$fill = max(1, (int) round($days * min(100.0, (float) $row['progress']) / 100));
				$end  = date('Y-m-d', strtotime($row['planned_start'] . ' +' . ($fill - 1) . ' days'));

				$ranges['progress'] = array($row['planned_start'], $end);
			}
		}

		if ($row['actual_end'] !== '' && $row['actual_end'] > $row['planned_end']) {
			$ranges['overrun'] = array(
				date('Y-m-d', strtotime($row['planned_end'] . ' +1 day')),
				$row['actual_end'],
			);
		}

		if ($row['completed_on'] !== '') {
			$ranges['marker'] = $row['completed_on'];
		}

		return $ranges;
	}

	/**
	 * The same windows as left/width percentages of the timeline, for the
	 * on-screen chart.
	 */
	private function build_bars($ranges, $timeline)
	{
		$bars = array('plan' => NULL, 'progress' => NULL, 'overrun' => NULL, 'marker' => NULL);

		foreach (array('plan', 'progress', 'overrun') as $key) {
			if ($ranges[$key] !== NULL) {
				$bars[$key] = $this->segment($ranges[$key][0], $ranges[$key][1], $timeline);
			}
		}

		if ($ranges['marker'] !== NULL) {
			$point = $this->segment($ranges['marker'], $ranges['marker'], $timeline);

			if ($point !== NULL) {
				$bars['marker'] = array('left' => round($point['left'] + ($point['width'] / 2), 4));
			}
		}

		return $bars;
	}

	/**
	 * A date range as left/width percentages of the timeline.
	 */
	private function segment($from, $to, $timeline)
	{
		if ($from === '' || $to === '' || $to < $from) {
			return NULL;
		}

		$total = max(1, (int) $timeline['total_days']);

		$start_day = $this->day_diff($timeline['start'], $from);
		$end_day   = $this->day_diff($timeline['start'], $to) + 1;

		$start_day = max(0, min($total, $start_day));
		$end_day   = max(0, min($total, $end_day));

		if ($end_day <= $start_day) {
			return NULL;
		}

		return array(
			'left'  => round(($start_day / $total) * 100, 4),
			'width' => round((($end_day - $start_day) / $total) * 100, 4),
			'days'  => $end_day - $start_day,
		);
	}

	/**
	 * Headline numbers for the KPI strip.
	 *
	 * Task progress, finish dates and tickets come from the DF's whole task
	 * set (narrowed by the department filter), so they do not jump about when
	 * you switch grouping. Only the row counter follows the grouping, and it
	 * says which unit it is counting.
	 */
	private function build_stats(array $rows, array $schedule, array $tickets, array $options, $today)
	{
		$unit_labels = array(
			'milestone'  => array('Milestones done', 'milestone'),
			'department' => array('Departments done', 'department'),
			'task'       => array('Tasks done', 'task'),
		);

		$unit = isset($unit_labels[$options['group']]) ? $unit_labels[$options['group']] : $unit_labels['milestone'];

		$stats = array(
			'unit_label'       => $unit[0],
			'unit_noun'        => $unit[1],
			'units'            => count($rows),
			'units_done'       => 0,
			'units_late'       => 0,
			'tasks'            => 0,
			'tasks_done'       => 0,
			'tasks_overdue'    => 0,
			'open_tickets'     => 0,
			'progress'         => 0.0,
			'planned_finish'   => '',
			'projected_finish' => '',
			'slippage_days'    => 0,
			'critical'         => array(),
		);

		foreach ($rows as $row) {

			if ($row['is_done']) {
				$stats['units_done']++;
			}

			if (!$row['is_done'] && $row['delay_days'] > 0) {
				$stats['units_late']++;

				$stats['critical'][] = array(
					'department' => $row['department'],
					'name'       => $row['name'],
					'delay_days' => $row['delay_days'],
				);
			}
		}

		// Task-level figures come from the schedule, not the rows, because the
		// milestone grouping only plots a curated subset of it.
		$seen_departments = array();

		foreach ($schedule as $sc) {

			$task = $this->build_task_row($sc, $today);

			$stats['tasks']++;

			if ($task['is_done']) {
				$stats['tasks_done']++;
			} elseif ($task['delay_days'] > 0) {
				$stats['tasks_overdue']++;
			}

			if ($task['planned_end'] !== '' && $task['planned_end'] > $stats['planned_finish']) {
				$stats['planned_finish'] = $task['planned_end'];
			}

			$finish = ($task['is_done'] && $task['completed_on'] !== '')
				? $task['completed_on']
				: ($task['planned_end'] !== '' ? max($task['planned_end'], $today) : '');

			if ($finish !== '' && $finish > $stats['projected_finish']) {
				$stats['projected_finish'] = $finish;
			}

			$department_id = (int) $sc->department_id;

			if (!isset($seen_departments[$department_id])) {
				$seen_departments[$department_id] = TRUE;
				$stats['open_tickets'] += (int) $this->ticket_count($tickets, $department_id)['open'];
			}
		}

		if ($stats['tasks'] > 0) {
			$stats['progress'] = round(($stats['tasks_done'] * 100) / $stats['tasks'], 1);
		}

		if ($stats['planned_finish'] !== '' && $stats['projected_finish'] !== '') {
			$stats['slippage_days'] = max(0, $this->day_diff($stats['planned_finish'], $stats['projected_finish']));
		}

		usort($stats['critical'], function ($a, $b) {
			return $b['delay_days'] - $a['delay_days'];
		});

		$stats['critical'] = array_slice($stats['critical'], 0, 5);

		return $stats;
	}

	/**
	 * Rework / ECN / IOM linked to this DF, when that module is installed.
	 * Returns a summary plus a department_id -> requests map for the row chips.
	 */
	private function get_change_control($df_id)
	{
		$empty = array('summary' => array(), 'by_department' => array(), 'active' => 0, 'departments' => 0);

		if (!file_exists(APPPATH . 'models/Df_change_control_model.php')) {
			return $empty;
		}

		$this->load->model('Df_change_control_model');

		// Go through get_instance(), not $this.
		//
		// CI_Model defines __get but NOT __isset, so isset($this->AnyModel) is
		// always FALSE inside a model however successfully the loader ran. The
		// loader attaches models to the super-object, so that is where to look
		// -- exactly what the old chart view did with its own $CI handle.
		$CI = get_instance();

		if (!isset($CI->Df_change_control_model)
			|| !method_exists($CI->Df_change_control_model, 'module_ready')
			|| !$CI->Df_change_control_model->module_ready()
		) {
			return $empty;
		}

		$summary       = $CI->Df_change_control_model->get_gantt_change_summary($df_id);
		$by_department = $CI->Df_change_control_model->get_gantt_department_map($df_id);

		$active = 0;

		foreach ($summary as $change) {
			if (isset($change['status']) && $change['status'] !== 'COMPLETED') {
				$active++;
			}
		}

		$departments = 0;

		foreach ($by_department as $department) {
			if (!empty($department['has_requests'])) {
				$departments++;
			}
		}

		return array(
			'summary'       => is_array($summary) ? $summary : array(),
			'by_department' => is_array($by_department) ? $by_department : array(),
			'active'        => $active,
			'departments'   => $departments,
		);
	}

	/**
	 * Task-level detail for the drill-down modal. One query.
	 */
	public function get_department_tasks($df_id, $department_id)
	{
		$rows = $this->db->query(
			'SELECT t.task_name, sc.id, sc.taskid, sc.department_id, sc.start_date, sc.end_date, sc.task_status,
			        sc.task_completed_on, sc.remarks, sc.on_hold, sc.mark_hold,
			        u.title AS owner_title, u.first_name AS owner_first, u.last_name AS owner_last
			 FROM task_department_wise_scheduling sc
			 LEFT JOIN task_management t ON t.task_id = sc.taskid
			 LEFT JOIN system_users u ON u.user_id = sc.assigned_user
			 WHERE sc.df_id = ? AND sc.department_id = ?
			 ORDER BY sc.end_date ASC, t.task_name ASC',
			array((int) $df_id, (int) $department_id)
		)->result();

		$today = date('Y-m-d');
		$out   = array();

		foreach ($rows as $row) {
			$task = $this->build_task_row($row, $today);

			$out[] = array(
				'task_name'     => $task['name'],
				'owner'         => ($task['owner'] !== '') ? $task['owner'] : 'Not Assigned',
				'planned_start' => ($task['planned_start'] !== '') ? date('d M Y', strtotime($task['planned_start'])) : '-',
				'planned_end'   => ($task['planned_end'] !== '') ? date('d M Y', strtotime($task['planned_end'])) : '-',
				'completed_on'  => ($task['completed_on'] !== '') ? date('d M Y', strtotime($task['completed_on'])) : '',
				'delay_days'    => $task['delay_days'],
				'status'        => $task['status'],
				'remarks'       => $task['remarks'],
			);
		}

		return $out;
	}

	/**
	 * Tickets raised against one department on this DF. One query.
	 */
	public function get_department_tickets($df_id, $department_id)
	{
		$rows = $this->db->query(
			'SELECT ct.help_ticket_no, ct.ticket_status, ct.added_on,
			        COALESCE(NULLIF(ct.updated_remarks, ""), NULLIF(ct.remarks, ""), "") AS ticket_remark,
			        u.title AS raiser_title, u.first_name AS raiser_first, u.last_name AS raiser_last,
			        t.task_name
			 FROM communication_ticket_system ct
			 LEFT JOIN system_users u ON u.user_id = ct.added_by
			 LEFT JOIN task_department_wise_scheduling sc ON sc.id = ct.task_record_id
			 LEFT JOIN task_management t ON t.task_id = sc.taskid
			 WHERE ct.df_id = ? AND ct.department_id = ?
			 ORDER BY ct.added_on DESC, ct.id DESC',
			array((int) $df_id, (int) $department_id)
		)->result();

		$out = array();

		foreach ($rows as $row) {
			$out[] = array(
				'ticket_no'  => trim((string) $row->help_ticket_no),
				'status'     => ((int) $row->ticket_status === 1) ? 'Closed' : 'Open',
				'raised_by'  => $this->title_case($row->raiser_title . ' ' . $row->raiser_first . ' ' . $row->raiser_last),
				'raised_on'  => $this->has_date($row->added_on) ? date('d M Y, h:i A', strtotime($row->added_on)) : '-',
				'task_name'  => $this->title_case($row->task_name),
				'remark'     => trim((string) $row->ticket_remark),
			);
		}

		return $out;
	}
}
