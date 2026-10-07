<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Gantt_chart
 *
 * DF-wise progress Gantt board. Replaces Task/finalgantchartWithDetails, which
 * ran its queries inside the render loop; everything here is prepared by
 * Gantt_chart_model in a fixed handful of queries and the view is pure markup.
 *
 *   /index.php/Gantt_chart/index/166   the board
 *   /index.php/Gantt_chart/tasks       POST, JSON, task drill-down
 *   /index.php/Gantt_chart/tickets     POST, JSON, department tickets
 *   /index.php/gantt/export/xlsx/166   csv | xlsx | pdf of the whole board
 *
 * The board takes ?group=milestone|department|task and ?scale=week|day, plus
 * ?dept=<id> to narrow it -- between them these cover all four charts the old
 * app kept as separate pages. Exports honour the same switches.
 */
class Gantt_chart extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Gantt_chart_model', 'gantt');

		if (!$this->session->userdata('logged_in')) {
			$this->session->set_flashdata('message', 'Session Logged Out. Login to continue', 'refresh');
			redirect(page_url);
		}
	}

	/**
	 * The board. DF id comes from the route or the third URI segment so the
	 * old /Task/finalgantchartWithDetails/166 muscle memory still maps over.
	 */
	public function index($df_id = 0)
	{
		$df_id = (int) ($df_id > 0 ? $df_id : $this->uri->segment(3));

		$data = $this->gantt->get_board($df_id, $this->requested_options());

		$this->load->view('gantt/df_gantt_board', $data);
	}

	/**
	 * The view spec off the query string: ?group=task&scale=day&dept=33
	 *
	 * These are the four old charts, which were four separate pages, reduced
	 * to two switches. The model validates them; anything unrecognised falls
	 * back to the default board.
	 */
	private function requested_options()
	{
		return array(
			'group'      => (string) $this->input->get('group'),
			'scale'      => (string) $this->input->get('scale'),
			'department' => (int) $this->input->get('dept'),
		);
	}

	/**
	 * Task-level detail for one department on this DF.
	 */
	public function tasks()
	{
		$df_id      = (int) $this->input->post('df_id');
		$department = (int) $this->input->post('department_id');

		if ($df_id <= 0 || $department <= 0) {
			$this->json(array('success' => FALSE, 'rows' => array(), 'message' => 'Task details are not available for this selection.'));

			return;
		}

		$this->json(array('success' => TRUE, 'rows' => $this->gantt->get_department_tasks($df_id, $department)));
	}

	/**
	 * Tickets raised against one department on this DF.
	 */
	public function tickets()
	{
		$df_id      = (int) $this->input->post('df_id');
		$department = (int) $this->input->post('department_id');

		if ($df_id <= 0 || $department <= 0) {
			$this->json(array('success' => FALSE, 'rows' => array(), 'message' => 'No tickets available for this selection.'));

			return;
		}

		$this->json(array('success' => TRUE, 'rows' => $this->gantt->get_department_tickets($df_id, $department)));
	}

	/**
	 * The board as a downloadable document: csv | xlsx | pdf.
	 *
	 * Both long and short forms route here, and the format defaults to csv so
	 * the original /gantt/export/<df_id> links keep working.
	 */
	public function export($format = 'csv', $df_id = 0)
	{
		// /gantt/export/166 lands here as export('166'), not export('csv', 166).
		if ($df_id <= 0 && ctype_digit((string) $format)) {
			$df_id  = (int) $format;
			$format = 'csv';
		}

		$df_id  = (int) $df_id;
		$format = strtolower(trim((string) $format));

		if (!in_array($format, array('csv', 'xlsx', 'pdf'), TRUE)) {
			$format = 'csv';
		}

		// The download is of whatever the operator is looking at, so the same
		// switches apply.
		$board = $this->gantt->get_board($df_id, $this->requested_options());

		if (empty($board['found'])) {
			show_error(!empty($board['message']) ? $board['message'] : 'Nothing to export.', 404, 'Gantt Export');

			return;
		}

		$this->load->library('Gantt_exporter', NULL, 'gantt_exporter');

		// A BINARY DOWNLOAD MUST CARRY NOTHING BUT THE FILE.
		//
		// Any notice, warning or stray echo raised while building the document
		// lands in the response ahead of the bytes, and the result is a file
		// Excel or a PDF reader refuses to open with no indication why. The
		// exporter silences the known vendor case at source; this is the
		// backstop for everything else -- captured, discarded, and logged so it
		// is not lost.
		ob_start();

		$result = $this->build_export($format, $board);

		// The xlsx guard clears every buffer before rendering its own message,
		// so the buffer opened above may already be gone by now.
		$stray = (ob_get_level() > 0) ? ob_get_clean() : '';

		if ($stray !== '' && $stray !== FALSE) {
			log_message('error', 'Gantt export (' . $format . ') produced ' . strlen($stray)
				. ' bytes of stray output, discarded so the download stays valid: '
				. substr(strip_tags($stray), 0, 500));
		}

		if ($result === NULL) {
			return;                       // the handler rendered its own response
		}

		$this->output
			->set_content_type($result['mime'])
			->set_header('Content-Disposition: attachment; filename="' . $result['filename'] . '"')
			->set_header('Content-Length: ' . strlen($result['body']))
			->set_output($result['body']);
	}

	/**
	 * Builds one export. Returns NULL when it has already rendered a
	 * response, so export() knows not to stream on top of it.
	 *
	 * @param  string $format
	 * @param  array  $board
	 * @return array|null ['filename'=>, 'mime'=>, 'body'=>]
	 */
	private function build_export($format, array $board)
	{
		switch ($format) {

			case 'xlsx':
				try {
					$out = $this->gantt_exporter->xlsx($board);
				} catch (RuntimeException $e) {
					// A missing server extension is an operator problem, not a
					// stack trace. Clear the buffer first or the message renders
					// inside it.
					while (ob_get_level() > 0) { ob_end_clean(); }

					$this->output->set_status_header(503);
					echo '<h3>Excel export unavailable</h3><p>'
						. htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
						. '</p><p><a href="' . page_url . 'gantt/' . (int) $board['df_id'] . '">Back to the chart</a></p>';

					return NULL;
				}

				$body = file_get_contents($out['path']);
				@unlink($out['path']);

				return array(
					'filename' => $out['filename'],
					'mime'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'body'     => $body,
				);

			case 'pdf':
				$out = $this->gantt_exporter->pdf($board);

				return array(
					'filename' => $out['filename'],
					'mime'     => 'application/pdf',
					'body'     => $out['body'],
				);

			case 'csv':
			default:
				$out = $this->gantt_exporter->csv($board);

				return array(
					'filename' => $out['filename'],
					'mime'     => 'text/csv; charset=UTF-8',
					'body'     => $out['body'],
				);
		}
	}

	private function json($payload)
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($payload));
	}
}
