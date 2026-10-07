<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Gantt_exporter
 *
 * CSV, XLSX and PDF of a DF progress board, from the array Gantt_chart_model
 * returns. Nothing here queries.
 *
 * ISOLATION
 * ---------
 * Every PHPExcel and TCPDF call for this module lives in this one file, the
 * same arrangement Abom_exporter uses, so swapping either library later is a
 * one-file change. It goes through the project's existing wrappers —
 * application/libraries/Excel.php and application/libraries/Pdf.php — and adds
 * no new dependency.
 *
 * THE CHART TRAVELS
 * -----------------
 * A Gantt exported as a plain table is not a Gantt. The XLSX carries one
 * narrow column per week with the bars painted as cell fills, and the PDF
 * draws them as rectangles at millimetre positions. Both take their windows
 * from $row['ranges'], which the model resolved once, so the spreadsheet, the
 * printout and the screen cannot drift apart.
 *
 * UNICODE
 * -------
 * TCPDF core fonts are not Unicode and the board uses → (U+2192) between
 * dates. Rendered with helvetica it becomes '?'; dejavusans renders it. Same
 * finding as Abom_exporter, same fix.
 *
 * PHP 7.4 compatible — production runs ea-php74.
 */
class Gantt_exporter
{
	/** @var object CodeIgniter instance */
	protected $CI;

	/** Bar fills, shared by both renderers. */
	private $colors = array(
		'plan'      => 'DBEAFE',   // scheduled window, nothing done yet
		'plan_done' => 'DCFCE7',   // scheduled window of a closed row
		'progress'  => '16A34A',   // work completed
		'overrun'   => 'DC2626',   // time past the planned end
		'today'     => 'DC2626',
		'head'      => '1E3A8A',
		'sub'       => '334155',
	);

	private $status_labels = array(
		'done'     => 'Completed',
		'late'     => 'Completed late',
		'overdue'  => 'Overdue',
		'running'  => 'In progress',
		'hold'     => 'On hold',
		'upcoming' => 'Not started',
	);

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	// ------------------------------------------------------------------
	// SHARED
	// ------------------------------------------------------------------

	private function status_label($status)
	{
		return isset($this->status_labels[$status]) ? $this->status_labels[$status] : ucfirst((string) $status);
	}

	private function day($date, $fallback = '')
	{
		return ($date !== '' && $date !== NULL) ? date('d M Y', strtotime($date)) : $fallback;
	}

	private function short_day($date, $fallback = '-')
	{
		return ($date !== '' && $date !== NULL) ? date('d-m-y', strtotime($date)) : $fallback;
	}

	/**
	 * Milestones and their tasks as one flat list, in display order, each
	 * carrying the level so a renderer can indent or bold it.
	 *
	 * @param  array $board
	 * @return array
	 */
	private function flatten(array $board)
	{
		$out = array();

		foreach ($board['rows'] as $row) {

			$out[] = array(
				// milestone | department | task -- a task-grouped board has no
				// children, and its rows must not be typeset as headings.
				'level'      => isset($row['level']) ? $row['level'] : 'milestone',
				'no'         => (string) $row['no'],
				'who'        => (isset($row['level']) && $row['level'] === 'task' && $row['owner'] !== '')
					? $row['department'] . ' / ' . $row['owner']
					: $row['department'],
				'name'       => $row['name'],
				'start'      => $row['planned_start'],
				'end'        => $row['planned_end'],
				'actual'     => $row['completed_on'],
				'status'     => $this->status_label($row['status']),
				'delay'      => (int) $row['delay_days'],
				'progress'   => (float) $row['progress'],
				'done_count' => (int) $row['done_count'],
				'total'      => (int) $row['total_count'],
				'is_done'    => !empty($row['is_done']),
				'ranges'     => $row['ranges'],
				'remarks'    => isset($row['remarks']) ? $row['remarks'] : '',
			);

			$sub = 0;

			foreach ($row['children'] as $child) {
				$sub++;

				$out[] = array(
					'level'      => 'task',
					'no'         => $row['no'] . '.' . $sub,
					'who'        => ($child['owner'] !== '') ? $child['owner'] : 'Unassigned',
					'name'       => $child['name'],
					'start'      => $child['planned_start'],
					'end'        => $child['planned_end'],
					'actual'     => $child['completed_on'],
					'status'     => $this->status_label($child['status']),
					'delay'      => (int) $child['delay_days'],
					'progress'   => (float) $child['progress'],
					'done_count' => (int) $child['done_count'],
					'total'      => 1,
					'is_done'    => !empty($child['is_done']),
					'ranges'     => $child['ranges'],
					'remarks'    => $child['remarks'],
				);
			}
		}

		return $out;
	}

	/**
	 * Which column a date falls in, as a 0-based index into
	 * $timeline['columns'] -- one per week or one per day depending on scale.
	 *
	 * @return int|null  NULL when the date sits outside the chart
	 */
	private function week_index($date, array $timeline)
	{
		if ($date === '' || $date === NULL) {
			return NULL;
		}

		$offset = (int) round(
			(strtotime($date . ' 00:00:00') - strtotime($timeline['start'] . ' 00:00:00')) / 86400
		);

		if ($offset < 0) {
			return NULL;
		}

		$index = (int) floor($offset / max(1, (int) $timeline['col_days']));

		return ($index < count($timeline['columns'])) ? $index : NULL;
	}

	/**
	 * A date range as a [first week, last week] pair of column indices,
	 * clamped to the chart. NULL when it does not overlap at all.
	 */
	private function week_span($range, array $timeline)
	{
		if (empty($range)) {
			return NULL;
		}

		$last = count($timeline['columns']) - 1;

		$from = $this->week_index($range[0], $timeline);
		$to   = $this->week_index($range[1], $timeline);

		// A window may start before the chart or end after it; keep the part
		// that overlaps rather than dropping the bar.
		if ($from === NULL) { $from = ($range[0] < $timeline['start']) ? 0 : NULL; }
		if ($to === NULL)   { $to   = ($range[1] > $timeline['end']) ? $last : NULL; }

		if ($from === NULL || $to === NULL || $to < $from) {
			return NULL;
		}

		return array($from, $to);
	}

	/**
	 * Where a date sits along the timeline, 0..1. Used for millimetre
	 * positions in the PDF, so bars keep day precision there.
	 */
	private function day_fraction($date, array $timeline)
	{
		$total = max(1, (int) $timeline['total_days']);

		$offset = (strtotime($date . ' 00:00:00') - strtotime($timeline['start'] . ' 00:00:00')) / 86400;

		return max(0, min(1, $offset / $total));
	}

	/**
	 * "DF-1810 - Godrej Consumer Products - Gantt - 20-08-2026.xlsx"
	 *
	 * A file called "gantt.pdf" in a mailbox tells the recipient nothing;
	 * the DF number and customer are what they search on.
	 */
	private function filename(array $board, $ext)
	{
		$parts = array($this->df_label($board, '-'));

		if ($board['header']['company'] !== '') {
			$parts[] = $board['header']['company'];
		}

		$parts[] = 'Gantt';
		$parts[] = date('d-m-Y');

		return $this->safe_filename(implode(' - ', $parts)) . '.' . $ext;
	}

	private function safe_filename($name)
	{
		$name = preg_replace('/[^A-Za-z0-9 \-_.]/', '', (string) $name);
		$name = trim(preg_replace('/\s+/', ' ', $name));

		// preg_replace returns NULL on failure, which would leave every
		// download named ".xlsx" — the same trap Abom_exporter documents.
		return ($name === '' || $name === NULL) ? 'DF-Gantt' : $name;
	}

	/**
	 * "DF 1810", but some DF numbers already read "DF - 1669_R1" and prefixing
	 * those again produced "DF DF - 1669_R1" in the title and, worse, files
	 * called "DF-DF - 1669_R1 - ....pdf".
	 */
	private function df_label(array $board, $joiner = ' ')
	{
		$df_no = trim((string) $board['header']['df_no']);

		return (stripos($df_no, 'DF') === 0) ? $df_no : 'DF' . $joiner . $df_no;
	}

	private function title(array $board)
	{
		$title = $this->df_label($board) . ' - Progress Gantt';

		if ($board['header']['company'] !== '') {
			$title .= ' - ' . $board['header']['company'];
		}

		return $title;
	}

	/**
	 * The one line that says where this document came from and how current
	 * it is. A printed chart with no timestamp gets acted on months later.
	 */
	private function provenance(array $board)
	{
		$bits = array();

		if ($board['header']['po_no'] !== '') {
			$bits[] = 'PO ' . $board['header']['po_no'] . ' dated ' . $board['header']['po_date'];
		}

		$bits[] = 'DF released ' . $board['header']['df_date'];

		if ($board['header']['marketing'] !== '') {
			$bits[] = 'Marketing: ' . $board['header']['marketing'];
		}

		if (!empty($board['options'])) {
			$groups = array('milestone' => 'Milestone wise', 'department' => 'Department wise', 'task' => 'Task wise');
			$group  = $board['options']['group'];

			$bits[] = (isset($groups[$group]) ? $groups[$group] : $group)
				. ', ' . $board['options']['scale'] . ' columns';
		}

		$bits[] = 'Generated ' . date('d M Y, H:i');

		return implode('   |   ', $bits);
	}

	/**
	 * The KPI strip as label / headline / detail triples. Split three ways
	 * because the PDF stacks them in a fixed-width box and a single long
	 * string ran off the end of it; CSV and XLSX join them back up.
	 */
	private function summary(array $board)
	{
		$stats = $board['stats'];

		// The counted unit follows the grouping -- milestones, departments or
		// tasks -- so the label comes from the model rather than being fixed.
		$open = (int) $stats['units'] - (int) $stats['units_done'];
		$noun = $stats['unit_noun'];

		return array(
			array('Overall progress', $stats['progress'] . '%',
				$stats['tasks_done'] . ' of ' . $stats['tasks'] . ' tasks closed'),

			array($stats['unit_label'], $stats['units_done'] . ' of ' . $stats['units'],
				$open . ' still open'),

			array('Running late', $stats['units_late'] . ' ' . $noun . ((int) $stats['units_late'] === 1 ? '' : 's'),
				$stats['tasks_overdue'] . ' overdue task' . ((int) $stats['tasks_overdue'] === 1 ? '' : 's')),

			array('Planned finish', $this->day($stats['planned_finish'], '-'),
				'latest scheduled end date'),

			array('Projected finish', $this->day($stats['projected_finish'], '-'),
				((int) $stats['slippage_days'] > 0)
					? $stats['slippage_days'] . ' days behind plan'
					: 'on or ahead of plan'),

			array('Open tickets', (string) $stats['open_tickets'],
				'raised on these departments'),
		);
	}

	/**
	 * The same figures as flat label/value pairs, for the text formats.
	 */
	private function summary_pairs(array $board)
	{
		$out = array();

		foreach ($this->summary($board) as $item) {
			$out[] = array($item[0], $item[1] . ' (' . $item[2] . ')');
		}

		return $out;
	}

	private function table_columns()
	{
		return array('#', 'Department / Owner', 'Milestone & task', 'Start', 'Due', 'Actual', 'Status', 'Delay (days)', 'Progress %');
	}

	private function table_row(array $row)
	{
		return array(
			$row['no'],
			$row['who'],
			$row['name'],
			$this->short_day($row['start']),
			$this->short_day($row['end']),
			$this->short_day($row['actual'], 'Pending'),
			$row['status'],
			$row['delay'],
			$row['progress'],
		);
	}

	// ------------------------------------------------------------------
	// CSV
	// ------------------------------------------------------------------

	/**
	 * @return array ['filename'=>string, 'body'=>string]
	 */
	public function csv(array $board)
	{
		$q = function ($v) {
			return '"' . str_replace('"', '""', (string) $v) . '"';
		};

		$lines = array();

		$lines[] = $q($this->title($board));
		$lines[] = $q($this->provenance($board));
		$lines[] = '';

		foreach ($this->summary_pairs($board) as $pair) {
			$lines[] = $q($pair[0]) . ',' . $q($pair[1]);
		}

		$lines[] = '';

		$header = array();
		foreach ($this->table_columns() as $label) {
			$header[] = $q($label);
		}
		$header[] = $q('Remark');
		$lines[] = implode(',', $header);

		foreach ($this->flatten($board) as $row) {
			$cells = array();
			foreach ($this->table_row($row) as $cell) {
				$cells[] = $q($cell);
			}
			$cells[] = $q($row['remarks']);
			$lines[] = implode(',', $cells);
		}

		// Excel opens a bare UTF-8 CSV as Windows-1252 and mangles any
		// non-ASCII customer name; the BOM is what makes it read correctly.
		$body = "\xEF\xBB\xBF" . implode("\r\n", $lines) . "\r\n";

		return array('filename' => $this->filename($board, 'csv'), 'body' => $body);
	}

	// ------------------------------------------------------------------
	// XLSX
	// ------------------------------------------------------------------

	/**
	 * Detail columns on the left, one narrow column per week on the right
	 * with the bars painted as cell fills, panes frozen between them.
	 *
	 * @return array ['filename'=>string, 'path'=>string]
	 * @throws RuntimeException when ext-zip is missing
	 */
	public function xlsx(array $board)
	{
		// PHPExcel's Excel2007 writer needs ext-zip. Without this check the
		// failure is a 500 from deep inside the writer rather than something
		// an operator can act on.
		if (!class_exists('ZipArchive')) {
			throw new RuntimeException(
				'Excel export needs the PHP zip extension (ZipArchive), which is not enabled '
				. 'on this server. Enable ext-zip in cPanel > Select PHP Version > Extensions, '
				. 'or use the PDF or CSV export instead.'
			);
		}

		// Load the wrapper for its require of PHPExcel, but build a FRESH
		// workbook rather than using $this->CI->excel. CodeIgniter caches a
		// library instance for the whole request, so a second export in one
		// request would keep writing into the first one's sheet -- 37 week
		// columns landing in a workbook that already had 252 day columns.
		$this->CI->load->library('Excel');

		$xl = new Excel();

		$timeline = $board['timeline'];
		$weeks    = $timeline['columns'];
		$columns  = $this->table_columns();

		$first_week_col = count($columns);          // 0-based: weeks start after the detail columns
		$last_week_col  = $first_week_col + count($weeks) - 1;

		$xl->setActiveSheetIndex(0);
		$sheet = $xl->getActiveSheet();
		$sheet->setTitle('Gantt');

		$last_col = PHPExcel_Cell::stringFromColumnIndex($last_week_col);
		$detail_last = PHPExcel_Cell::stringFromColumnIndex($first_week_col - 1);

		$r = 1;

		// ---- document header -----------------------------------------
		$sheet->setCellValue('A' . $r, $this->title($board));
		$sheet->mergeCells('A' . $r . ':' . $detail_last . $r);
		$sheet->getStyle('A' . $r)->getFont()->setBold(true)->setSize(13);
		$r++;

		$sheet->setCellValue('A' . $r, $this->provenance($board));
		$sheet->mergeCells('A' . $r . ':' . $detail_last . $r);
		$sheet->getStyle('A' . $r)->getFont()->setSize(9)->getColor()->setRGB('475569');
		$r++;
		$r++;

		// ---- summary -------------------------------------------------
		foreach ($this->summary_pairs($board) as $pair) {
			// Merged so the label cannot be clipped by the narrow '#' column
			// and the value gets the width of the task column.
			$sheet->setCellValue('A' . $r, $pair[0]);
			$sheet->mergeCells('A' . $r . ':B' . $r);
			$sheet->getStyle('A' . $r)->getFont()->setBold(true);

			$sheet->setCellValue('C' . $r, $pair[1]);
			$sheet->mergeCells('C' . $r . ':' . $detail_last . $r);
			$r++;
		}

		$r++;

		// ---- month band ----------------------------------------------
		$month_row = $r;
		$col = $first_week_col;

		foreach ($timeline['months'] as $month) {
			$from = PHPExcel_Cell::stringFromColumnIndex($col);
			$to   = PHPExcel_Cell::stringFromColumnIndex($col + $month['span'] - 1);

			$sheet->setCellValue($from . $month_row, $month['label']);

			if ($month['span'] > 1) {
				$sheet->mergeCells($from . $month_row . ':' . $to . $month_row);
			}

			$sheet->getStyle($from . $month_row . ':' . $to . $month_row)->applyFromArray(array(
				'font'      => array('bold' => TRUE, 'size' => 9, 'color' => array('rgb' => 'FFFFFF')),
				'fill'      => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
				                     'startcolor' => array('rgb' => $this->colors['sub'])),
				'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
			));

			$col += $month['span'];
		}

		$r++;

		// ---- column header + week numbers ----------------------------
		$head_row = $r;

		foreach ($columns as $i => $label) {
			$sheet->setCellValueByColumnAndRow($i, $head_row, $label);
		}

		foreach ($weeks as $i => $week) {
			$sheet->setCellValueByColumnAndRow($first_week_col + $i, $head_row, $week['label']);
		}

		$sheet->getStyle('A' . $head_row . ':' . $last_col . $head_row)->applyFromArray(array(
			'font'      => array('bold' => TRUE, 'color' => array('rgb' => 'FFFFFF')),
			'fill'      => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
			                     'startcolor' => array('rgb' => $this->colors['head'])),
			'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
		));

		$sheet->getStyle(
			PHPExcel_Cell::stringFromColumnIndex($first_week_col) . $head_row . ':' . $last_col . $head_row
		)->applyFromArray(array(
			'font'      => array('size' => 8),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			                     'textRotation' => 90),
		));

		$sheet->getRowDimension($head_row)->setRowHeight(42);
		$r++;

		// ---- rows ----------------------------------------------------
		$first_data_row = $r;

		foreach ($this->flatten($board) as $row) {

			foreach ($this->table_row($row) as $i => $cell) {
				// A task name beginning with '=' would be stored as a formula
				// and shown as #NAME?. Several task remarks in this data do.
				if (is_string($cell) && substr($cell, 0, 1) === '=') {
					$sheet->setCellValueExplicitByColumnAndRow($i, $r, $cell, PHPExcel_Cell_DataType::TYPE_STRING);
				} else {
					$sheet->setCellValueByColumnAndRow($i, $r, $cell);
				}
			}

			$is_milestone = ($row['level'] !== 'task');

			if ($is_milestone) {
				$sheet->getStyle('A' . $r . ':' . $detail_last . $r)->getFont()->setBold(TRUE);
				$sheet->getStyle('A' . $r . ':' . $last_col . $r)->applyFromArray(array(
					'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
					                'startcolor' => array('rgb' => 'F1F5F9')),
				));
			} else {
				// Tasks are indented under their milestone so the outline
				// survives sorting and filtering being turned off.
				$sheet->getStyle('C' . $r)->getAlignment()->setIndent(2);
			}

			if ($row['delay'] > 0) {
				$sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(7) . $r)
					->getFont()->setBold(TRUE)->getColor()->setRGB('B91C1C');
			}

			$this->paint_bars($sheet, $r, $row['ranges'], $timeline, $first_week_col, $row['is_done']);

			$r++;
		}

		$last_data_row = $r - 1;

		// ---- widths, borders, freeze ---------------------------------
		$widths = array(7, 26, 42, 10, 10, 10, 15, 12, 11);

		foreach ($widths as $i => $w) {
			$sheet->getColumnDimensionByColumn($i)->setWidth($w);
		}

		for ($i = 0; $i < count($weeks); $i++) {
			$sheet->getColumnDimensionByColumn($first_week_col + $i)->setWidth(2.8);
		}

		$sheet->getStyle('A' . $head_row . ':' . $last_col . $last_data_row)->applyFromArray(array(
			'borders' => array(
				'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,
				                      'color' => array('rgb' => 'CBD5E1')),
			),
		));

		$sheet->getStyle('D' . $first_data_row . ':' . 'I' . $last_data_row)
			->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// Panes freeze at the first week column so the task names stay put
		// while the timeline scrolls -- without this a 37-week sheet is
		// unreadable the moment you scroll right.
		$sheet->freezePane(PHPExcel_Cell::stringFromColumnIndex($first_week_col) . $first_data_row);

		// ---- legend --------------------------------------------------
		$r++;
		$legend = array(
			array('Planned window', $this->colors['plan']),
			array('Planned window of a closed milestone', $this->colors['plan_done']),
			array('Work completed', $this->colors['progress']),
			array('Time past the planned end', $this->colors['overrun']),
		);

		foreach ($legend as $item) {
			$sheet->getStyle('A' . $r)->applyFromArray(array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
				                'startcolor' => array('rgb' => $item[1])),
				'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,
				                                         'color' => array('rgb' => 'CBD5E1'))),
			));
			$sheet->setCellValue('B' . $r, $item[0]);
			$r++;
		}

		$sheet->setSelectedCell('A' . $first_data_row);

		// ---- save ----------------------------------------------------
		$path   = rtrim(sys_get_temp_dir(), '/') . '/' . $this->filename($board, 'xlsx');
		$writer = PHPExcel_IOFactory::createWriter($xl, 'Excel2007');

		// SUPPRESS VENDOR NOISE WHILE SAVING.
		//
		// PHPExcel is unmaintained and raises deprecations on modern PHP.
		// CodeIgniter's error handler renders those as an HTML block and
		// ECHOES it -- straight into the download stream, ahead of the ZIP.
		// Excel then refuses the file and the user is told "cannot open"
		// with no clue why. Abom_exporter documents this happening for real
		// on 2026-08-11.
		//
		// Scoped to errors raised INSIDE the vendored tree, so anything from
		// this module's own code still reaches the normal handler.
		set_error_handler(function ($no, $str, $file = '', $line = 0) {
			return (strpos(str_replace('\\', '/', (string) $file), '/third_party/PHPExcel') !== FALSE);
		});

		try {
			$writer->save($path);
		} catch (Exception $e) {
			restore_error_handler();
			throw new RuntimeException('Excel export failed while writing the file: ' . $e->getMessage());
		}

		restore_error_handler();

		return array('filename' => $this->filename($board, 'xlsx'), 'path' => $path);
	}

	/**
	 * Paint one row's bars across the week columns.
	 *
	 * Order matters: the planned track goes down first, the completed work
	 * over it, the overrun after that.
	 */
	private function paint_bars($sheet, $excel_row, $ranges, array $timeline, $first_week_col, $is_done)
	{
		$layers = array(
			array('plan',     $is_done ? $this->colors['plan_done'] : $this->colors['plan']),
			array('progress', $this->colors['progress']),
			array('overrun',  $this->colors['overrun']),
		);

		foreach ($layers as $layer) {

			list($key, $rgb) = $layer;

			$span = $this->week_span(isset($ranges[$key]) ? $ranges[$key] : NULL, $timeline);

			if ($span === NULL) {
				continue;
			}

			$from = PHPExcel_Cell::stringFromColumnIndex($first_week_col + $span[0]);
			$to   = PHPExcel_Cell::stringFromColumnIndex($first_week_col + $span[1]);

			$sheet->getStyle($from . $excel_row . ':' . $to . $excel_row)->applyFromArray(array(
				'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
				                'startcolor' => array('rgb' => $rgb)),
			));
		}
	}

	// ------------------------------------------------------------------
	// PDF
	// ------------------------------------------------------------------

	/**
	 * Landscape A4. Page 1 carries the summary, then the chart itself is
	 * drawn as rectangles -- label block on the left, timeline on the right,
	 * week scale repeated at the top of every page.
	 *
	 * @return array ['filename'=>string, 'body'=>string]
	 */
	public function pdf(array $board)
	{
		$this->CI->load->library('Pdf');

		$timeline = $board['timeline'];
		$rows     = $this->flatten($board);

		$font = 'dejavusans';

		$pdf = new Pdf();
		$pdf->SetCreator('Shubham Packaging PMS');
		$pdf->SetTitle($this->title($board));
		$pdf->SetAuthor('Shubham Packaging PMS');
		$pdf->setPrintHeader(FALSE);
		$pdf->setPrintFooter(FALSE);
		$pdf->SetMargins(8, 8, 8);
		$pdf->SetAutoPageBreak(FALSE);

		// Landscape A4: 297 x 210mm, 8mm margins -> 281 x 194mm printable.
		$page_w   = 281.0;
		$left     = 8.0;
		$bottom_y = 196.0;

		// Label columns, mm. They total $label_w, and the timeline gets what
		// is left; the guard below keeps that relationship true if anyone
		// edits the widths.
		$cols = array(
			array('#',                  9.0,  'C'),
			array('Department / Owner', 30.0, 'L'),
			array('Milestone & task',   46.0, 'L'),
			array('Due',                14.0, 'C'),
			array('Delay',              13.0, 'C'),
			array('%',                  10.0, 'C'),
		);

		$label_w = 0.0;
		foreach ($cols as $col) { $label_w += $col[1]; }

		$chart_x = $left + $label_w;
		$chart_w = $page_w - $label_w;

		if ($chart_w < 60) {
			throw new RuntimeException(
				'Gantt_exporter: label columns total ' . $label_w . 'mm, leaving only '
				. $chart_w . 'mm of timeline on landscape A4.');
		}

		$row_h = 4.8;

		$pdf->AddPage('L', 'A4');
		$y = $this->pdf_cover($pdf, $board, $font, $left, $page_w);
		$y = $this->pdf_scale($pdf, $timeline, $font, $left, $label_w, $chart_x, $chart_w, $y, $cols);

		foreach ($rows as $row) {

			if ($y + $row_h > $bottom_y) {
				$this->pdf_footer($pdf, $board, $font, $left, $page_w);
				$pdf->AddPage('L', 'A4');
				$y = 10.0;
				$y = $this->pdf_scale($pdf, $timeline, $font, $left, $label_w, $chart_x, $chart_w, $y, $cols);
			}

			$this->pdf_row($pdf, $row, $timeline, $font, $left, $cols, $chart_x, $chart_w, $y, $row_h);
			$y += $row_h;
		}

		// The today line is drawn per page inside pdf_scale/pdf_row bands, so
		// the last page just needs its footer.
		$this->pdf_footer($pdf, $board, $font, $left, $page_w);

		return array(
			'filename' => $this->filename($board, 'pdf'),
			'body'     => $pdf->Output($this->filename($board, 'pdf'), 'S'),
		);
	}

	/**
	 * Title, provenance and the KPI strip. Returns the y to carry on from.
	 */
	private function pdf_cover($pdf, array $board, $font, $left, $page_w)
	{
		$pdf->SetFillColor(30, 58, 138);
		$pdf->Rect($left, 8, $page_w, 12, 'F');

		$pdf->SetTextColor(255, 255, 255);
		$pdf->SetFont($font, 'B', 11);
		$pdf->SetXY($left + 3, 9.5);
		$pdf->Cell($page_w - 6, 5, $this->title($board), 0, 0, 'L');

		$pdf->SetFont($font, '', 6.5);
		$pdf->SetXY($left + 3, 15);
		$pdf->Cell($page_w - 6, 4, $this->provenance($board), 0, 0, 'L');

		$pdf->SetTextColor(15, 23, 42);

		// KPI strip: six equal boxes across the page.
		$summary = $this->summary($board);
		$box_w   = $page_w / count($summary);
		$x       = $left;
		$y       = 22.0;

		$inner = $box_w - 4.5;

		foreach ($summary as $item) {
			$pdf->SetFillColor(241, 245, 249);
			$pdf->SetDrawColor(203, 213, 225);
			$pdf->Rect($x, $y, $box_w - 1.5, 14, 'DF');

			$pdf->SetFont($font, '', 5);
			$pdf->SetTextColor(100, 116, 139);
			$pdf->SetXY($x + 1.5, $y + 1);
			$pdf->Cell($inner, 2.6, $this->fit($pdf, strtoupper($item[0]), $inner), 0, 0, 'L');

			$pdf->SetFont($font, 'B', 8);
			$pdf->SetTextColor(15, 23, 42);
			$pdf->SetXY($x + 1.5, $y + 4);
			$pdf->Cell($inner, 4.4, $this->fit($pdf, $item[1], $inner), 0, 0, 'L');

			$pdf->SetFont($font, '', 5);
			$pdf->SetTextColor(100, 116, 139);
			$pdf->SetXY($x + 1.5, $y + 9.2);
			$pdf->Cell($inner, 3, $this->fit($pdf, $item[2], $inner), 0, 0, 'L');

			$x += $box_w;
		}

		// Legend, so a printed page explains its own colours.
		$y += 16.5;
		$x  = $left;

		$legend = array(
			array('Planned', $this->colors['plan']),
			array('Closed', $this->colors['plan_done']),
			array('Work done', $this->colors['progress']),
			array('Overrun', $this->colors['overrun']),
		);

		$pdf->SetFont($font, '', 6);

		foreach ($legend as $item) {
			list($r, $g, $b) = $this->rgb($item[1]);
			$pdf->SetFillColor($r, $g, $b);
			$pdf->SetDrawColor(148, 163, 184);
			$pdf->Rect($x, $y + 0.6, 6, 2.6, 'DF');

			$pdf->SetTextColor(71, 85, 105);
			$pdf->SetXY($x + 7, $y);
			$pdf->Cell(24, 4, $item[0], 0, 0, 'L');

			$x += 32;
		}

		$pdf->SetTextColor(15, 23, 42);

		return $y + 5.5;
	}

	/**
	 * Month band, week numbers and the label-column headings. Returns the y
	 * where data rows begin.
	 */
	private function pdf_scale($pdf, array $timeline, $font, $left, $label_w, $chart_x, $chart_w, $y, array $cols)
	{
		$weeks    = $timeline['columns'];
		$week_w   = $chart_w / max(1, count($weeks));

		// Month band. On a two-year DF a band is only a few millimetres wide,
		// so the label steps down "Mar 26" -> "Mar" -> "M" -> nothing rather
		// than running into its neighbour.
		$pdf->SetFont($font, 'B', 6);
		$x = $chart_x;

		foreach ($timeline['months'] as $month) {
			$w = $week_w * $month['span'];

			$pdf->SetFillColor(51, 65, 85);
			$pdf->Rect($x, $y, $w, 4, 'F');

			$label = $this->widest_that_fits($pdf, array(
				$month['label'],
				substr($month['label'], 0, 3),
				substr($month['label'], 0, 1),
			), $w - 0.6);

			if ($label !== '') {
				$pdf->SetTextColor(255, 255, 255);
				$pdf->SetXY($x, $y + 0.3);
				$pdf->Cell($w, 3.4, $label, 0, 0, 'C');
			}

			// A hairline at each boundary keeps the months separable when the
			// labels have had to drop out.
			$pdf->SetDrawColor(148, 163, 184);
			$pdf->SetLineWidth(0.1);
			$pdf->Line($x, $y, $x, $y + 4);

			$x += $w;
		}

		// Label-block heading, aligned with the month band
		$pdf->SetFillColor(30, 58, 138);
		$pdf->Rect($left, $y, $label_w, 8, 'F');

		$pdf->SetTextColor(255, 255, 255);
		$pdf->SetFont($font, 'B', 6);
		$cx = $left;

		foreach ($cols as $col) {
			$pdf->SetXY($cx, $y + 2);
			$pdf->Cell($col[1], 4, $col[0], 0, 0, $col[2]);
			$cx += $col[1];
		}

		// Week numbers, thinned until a label actually fits its slot. Measured
		// rather than guessed: at 85 weeks on A4 a slot is under 2mm and a
		// fixed rule printed every label on top of the next one.
		$pdf->SetFont($font, '', 4.2);

		$needed = $pdf->GetStringWidth('W00') + 0.8;
		$every  = 1;

		while ($every < 8 && ($week_w * $every) < $needed) {
			$every *= 2;
		}

		$x = $chart_x;

		foreach ($weeks as $i => $week) {
			$pdf->SetFillColor($week['is_current'] ? 254 : 226, $week['is_current'] ? 226 : 232, $week['is_current'] ? 226 : 240);
			$pdf->Rect($x, $y + 4, $week_w, 4, 'F');

			if ($i % $every === 0) {
				$pdf->SetTextColor($week['is_current'] ? 185 : 51, $week['is_current'] ? 28 : 65, $week['is_current'] ? 28 : 85);
				$pdf->SetXY($x, $y + 4.4);
				$pdf->Cell($week_w * $every, 3, $week['label'], 0, 0, 'C');
			}

			$x += $week_w;
		}

		$pdf->SetTextColor(15, 23, 42);

		return $y + 8;
	}

	/**
	 * One label row plus its bars.
	 */
	private function pdf_row($pdf, array $row, array $timeline, $font, $left, array $cols, $chart_x, $chart_w, $y, $row_h)
	{
		$is_milestone = ($row['level'] !== 'task');

		// Row band
		$pdf->SetFillColor($is_milestone ? 241 : 255, $is_milestone ? 245 : 255, $is_milestone ? 249 : 255);
		$pdf->Rect($left, $y, $chart_x - $left + $chart_w, $row_h, 'F');

		$pdf->SetDrawColor(226, 232, 240);
		$pdf->Line($left, $y + $row_h, $chart_x + $chart_w, $y + $row_h);

		// Labels
		$pdf->SetFont($font, $is_milestone ? 'B' : '', 5.6);
		$pdf->SetTextColor($is_milestone ? 15 : 71, $is_milestone ? 23 : 85, $is_milestone ? 42 : 105);

		$values = array(
			$row['no'],
			$row['who'],
			($is_milestone ? '' : '   ') . $row['name'],
			$this->short_day($row['end']),
			($row['delay'] > 0) ? $row['delay'] . 'd' : 'On time',
			round($row['progress']) . '%',
		);

		$cx = $left;

		foreach ($cols as $i => $col) {
			if ($i === 4 && $row['delay'] > 0) {
				$pdf->SetTextColor(185, 28, 28);
			}

			$pdf->SetXY($cx, $y + 0.6);
			// Trim rather than wrap: a wrapped cell would break row alignment
			// with the bars, which is the whole point of the document.
			$pdf->Cell($col[1] - 1, $row_h - 1, $this->fit($pdf, $values[$i], $col[1] - 1.5), 0, 0, $col[2]);
			$cx += $col[1];

			if ($i === 4) {
				$pdf->SetTextColor($is_milestone ? 15 : 71, $is_milestone ? 23 : 85, $is_milestone ? 42 : 105);
			}
		}

		// Today line, drawn per row so it runs the height of the chart
		// without needing a second pass over the page.
		if ($timeline['today_pct'] !== NULL) {
			$tx = $chart_x + ($chart_w * ((float) $timeline['today_pct'] / 100));
			$pdf->SetDrawColor(220, 38, 38);
			$pdf->SetLineStyle(array('width' => 0.2, 'dash' => 1, 'color' => array(220, 38, 38)));
			$pdf->Line($tx, $y, $tx, $y + $row_h);
			$pdf->SetLineStyle(array('width' => 0.2, 'dash' => 0, 'color' => array(226, 232, 240)));
		}

		// Bars
		$bar_h = $is_milestone ? 2.8 : 2.0;
		$bar_y = $y + (($row_h - $bar_h) / 2);

		$layers = array(
			array('plan',     $row['is_done'] ? $this->colors['plan_done'] : $this->colors['plan']),
			array('progress', $this->colors['progress']),
			array('overrun',  $this->colors['overrun']),
		);

		foreach ($layers as $layer) {

			list($key, $rgb) = $layer;

			if (empty($row['ranges'][$key])) {
				continue;
			}

			$range = $row['ranges'][$key];

			$x1 = $chart_x + ($chart_w * $this->day_fraction($range[0], $timeline));
			$x2 = $chart_x + ($chart_w * $this->day_fraction(
				date('Y-m-d', strtotime($range[1] . ' +1 day')), $timeline));

			$w = max(0.7, $x2 - $x1);      // a one-day task must still be visible

			list($r, $g, $b) = $this->rgb($rgb);
			$pdf->SetFillColor($r, $g, $b);
			$pdf->Rect($x1, $bar_y, $w, $bar_h, 'F');
		}

		$pdf->SetTextColor(15, 23, 42);
	}

	private function pdf_footer($pdf, array $board, $font, $left, $page_w)
	{
		$pdf->SetFont($font, '', 5.5);
		$pdf->SetTextColor(100, 116, 139);
		$pdf->SetXY($left, 199);
		$pdf->Cell($page_w / 2, 4, $this->df_label($board) . '  |  Shubham Packaging PMS', 0, 0, 'L');
		$pdf->SetXY($left + ($page_w / 2), 199);
		$pdf->Cell($page_w / 2, 4, 'Page ' . $pdf->getAliasNumPage() . ' of ' . $pdf->getAliasNbPages()
			. '   |   Generated ' . date('d M Y, H:i'), 0, 0, 'R');
		$pdf->SetTextColor(15, 23, 42);
	}

	/**
	 * The first candidate that fits $width mm, or '' when none do.
	 */
	private function widest_that_fits($pdf, array $candidates, $width)
	{
		foreach ($candidates as $candidate) {
			if ($pdf->GetStringWidth($candidate) <= $width) {
				return $candidate;
			}
		}

		return '';
	}

	/**
	 * Shorten a string until it fits $width mm, with an ellipsis.
	 */
	private function fit($pdf, $text, $width)
	{
		$text = (string) $text;

		if ($pdf->GetStringWidth($text) <= $width) {
			return $text;
		}

		while ($text !== '' && $pdf->GetStringWidth($text . '…') > $width) {
			$text = mb_substr($text, 0, mb_strlen($text) - 1);
		}

		return $text . '…';
	}

	/**
	 * 'DC2626' -> array(220, 38, 38)
	 */
	private function rgb($hex)
	{
		$hex = ltrim((string) $hex, '#');

		return array(
			hexdec(substr($hex, 0, 2)),
			hexdec(substr($hex, 2, 2)),
			hexdec(substr($hex, 4, 2)),
		);
	}
}
