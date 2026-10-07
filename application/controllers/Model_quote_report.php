<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model-wise quote report - pick a machine model, see every company quoted
 * for it, and for each quote the Annexure-IV price schedule and the optional
 * items, with totals. index() renders it, export() writes the same data as
 * .xlsx; both go through _data(), so the file always equals the page.
 *
 * Added 2026-09-30. Self-contained, like Exchange_rate and Attendance_report:
 * a new controller and one new view, read-only over the quotation tables.
 * Linked from the opportunity dashboard twice: the "Sales Performance &
 * Brand Momentum" section (admins) and Quick Links (everyone). Open to every
 * logged-in user since 2026-09-30, but a sales user sees only the customers
 * they added - see _sees_all().
 *
 * The model is quotation_customer_data.mach_model_no - the "Specify Machine
 * Model (For Internal Purpose)" drop-down, 300/600/800/1000/1200. The long
 * machine_model_no is free text (617 spellings in 815 quotes on 2026-09-30),
 * so it is shown on each quote and searchable, never used as the selector.
 * 299 older quotes carry mach_model_no = 0; they are the "Not specified"
 * choice rather than being silently left out.
 *
 * The total is the price schedule's BASIC COST only - machine + line items
 * + consumable spares, the "Basic Cost" row of Salescrm_model::getQuoteData
 * (the quote PDF). Discount, packing, forwarding, insurance, installation,
 * freight and GST are deliberately left out (asked 2026-09-30).
 *
 * Quote owner is the lead's added_by - the person the quote PDF names as the
 * sales contact (Salescrm_model::getUserDetails) - falling back to whoever
 * saved the quote when the lead has none.
 */
class Model_quote_report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $session = $this->session->userdata('logged_in');

        if ($session == FALSE) {
            redirect(page_url);
        }

        $user_id = (int) $this->session->userdata['logged_in']['user_id'];

        if (empty($user_id)) {
            redirect(site_url(), 'refresh');
        }
    }

    /**
     * Who sees every customer: roles 12 and 41 (as in "View Your Customers"),
     * plus the admins by id who had this report before it was opened up
     * (pms_is_super_admin, 139, 161). Asked 2026-09-30: "make sure for sales
     * user they will see their own customer only".
     *
     * Anyone else sees quotes on LEADS THEY OWN (leads.added_by - the same
     * person shown as Quote Owner), or on customers they added. Ownership
     * cannot rest on customer_detail.added_by alone: it is 0 for 740 of the
     * 815 quoted customers, so Ajay Kumar (118), owner of 112 quotes, saw no
     * company at all under that rule.
     */
    private function _sees_all()
    {
        $s    = $this->session->userdata('logged_in');
        $role = isset($s['role']) ? (int) $s['role'] : 0;
        $uid  = (int) $s['user_id'];

        return in_array($role, array(12, 41), true)
            || pms_is_super_admin()
            || in_array($uid, array(139, 161), true);
    }

    private function _models()
    {
        return array('300' => 'SPM 300', '600' => 'SPM 600', '800' => 'SPM 800',
                     '1000' => 'SPM 1000', '1200' => 'SPM 1200', '0' => 'Not specified (older quotes)');
    }

    private function _currency($c)
    {
        $c = (int) $c;
        if ($c === 1) return 'USD';
        if ($c === 2) return 'INR';
        return 'EUR';   /* as the quote PDF decides it */
    }

    /* Company as the report groups it - several customer_detail rows (one
       per contact) share one company name. */
    private function _company_expr()
    {
        return "TRIM(COALESCE(NULLIF(TRIM(c.company_name),''), c.customer_name, ''))";
    }

    /** Quotes for the model (and optional text), by company then newest. */
    private function _quotes($model, $text, $company)
    {
        $this->db->select("q.id, q.lead_id, q.ref_no, q.quotation_date, q.currency, q.machine_name,
                           q.machine_model_no, q.mach_model_no, q.version, q.customer_id,
                           " . $this->_company_expr() . " AS company,
                           TRIM(COALESCE(c.customer_name,'')) AS contact,
                           TRIM(CONCAT(COALESCE(lo.first_name,''),' ',COALESCE(lo.last_name,''))) AS lead_owner,
                           TRIM(CONCAT(COALESCE(qo.first_name,''),' ',COALESCE(qo.last_name,''))) AS quote_by", FALSE)
                 ->from('quotation_customer_data q')
                 ->join('customer_detail c', 'c.id = q.customer_id', 'left')
                 ->join('leads l', 'l.id = q.lead_id', 'left')
                 ->join('system_users lo', 'lo.user_id = l.added_by', 'left')
                 ->join('system_users qo', 'qo.user_id = q.added_by', 'left');

        /* Sales users: their own leads/customers only - see _sees_all(). Applied
           here, the one query behind the company list, the page and the
           Excel file, so none of the three can show more than the others. */
        if (!$this->_sees_all()) {
            $me = (int) $this->session->userdata['logged_in']['user_id'];
            $this->db->group_start()->where('l.added_by', $me)->or_where('c.added_by', $me)->group_end();
        }

        if ($model === '0') {
            $this->db->group_start()->where('q.mach_model_no', 0)->or_where('q.mach_model_no IS NULL', null, FALSE)->or_where('q.mach_model_no', '')->group_end();
        } elseif ($model !== '') {
            $this->db->where('q.mach_model_no', (int) $model);
        }

        if ($text !== '') {
            $this->db->group_start()
                     ->like('q.machine_model_no', $text)
                     ->or_like('q.machine_name', $text)
                     ->or_like('q.ref_no', $text)
                     ->group_end();
        }

        /* $company is a LIST of names (multi-select since 2026-09-30); an
           empty list or '__all' means no company filter. */
        $company = array_values(array_filter((array) $company, 'strlen'));
        if ($company && !in_array('__all', $company, true)) {
            $or = array();
            foreach ($company as $name) {
                $or[] = $name === '(No company)'
                      ? $this->_company_expr() . " = ''"
                      : $this->_company_expr() . ' = ' . $this->db->escape($name);
            }
            $this->db->where('(' . implode(' OR ', $or) . ')', NULL, FALSE);
        }

        $rows = $this->db->order_by('company', 'ASC')
                         ->order_by('q.quotation_date', 'DESC')
                         ->order_by('q.id', 'DESC')
                         ->get()
                         ->result_array();

        foreach ($rows as &$r) {
            $owner = $r['lead_owner'] !== '' ? $r['lead_owner'] : $r['quote_by'];
            $r['owner']    = $owner !== '' ? ucwords(strtolower($owner)) : '-';
            $r['cur']      = $this->_currency($r['currency']);
            $r['company']  = $r['company'] !== '' ? $r['company'] : '(No company)';
        }
        unset($r);

        return $rows;
    }

    private function _group($rows, $key)
    {
        $out = array();
        foreach ($rows as $r) $out[(int) $r[$key]][] = $r;
        return $out;
    }

    /**
     * Annexure-IV + optionals for a set of quotes, loaded in one pass per
     * table rather than per quote - "All companies" on SPM 1200 is 200+.
     */
    private function _schedule($quotes)
    {
        $ids = array();
        foreach ($quotes as $q) $ids[] = (int) $q['id'];
        if (empty($ids)) return array();

        $a4 = $this->_group($this->db->where_in('record_id', $ids)
                                     ->where_in('category_type', array(0, 1))
                                     ->order_by('category_type', 'ASC')->order_by('id', 'ASC')
                                     ->get('quotation_annexture_4')->result_array(), 'record_id');

        $opt = $this->_group($this->db->where_in('record_id', $ids)->order_by('id', 'ASC')
                                      ->get('quotation_optional')->result_array(), 'record_id');

        $cons = $this->db->table_exists('quotation_consumable_spares')
              ? $this->_group($this->db->where_in('record_id', $ids)->get('quotation_consumable_spares')->result_array(), 'record_id')
              : array();

        /* Item names: line items and optionals store a presto_instruments id. */
        $pids = array();
        foreach (array($a4, $opt) as $set) foreach ($set as $rows) foreach ($rows as $r) {
            if (ctype_digit((string) $r['description'])) $pids[(int) $r['description']] = 1;
        }
        $names = array();
        if ($pids) {
            foreach ($this->db->select('id, instruments_name')->where_in('id', array_keys($pids))->where('status', 1)
                              ->get('presto_instruments')->result_array() as $r) {
                $names[(int) $r['id']] = $r['instruments_name'];
            }
        }
        $name = function ($d) use ($names) {
            if (ctype_digit((string) $d)) return isset($names[(int) $d]) ? $names[(int) $d] : ('Item #' . $d);
            return (string) $d;
        };

        $out = array();

        foreach ($quotes as $q) {
            $id    = (int) $q['id'];
            $lines = array();
            $price_data = array();

            foreach (isset($a4[$id]) ? $a4[$id] : array() as $r) {
                $qty   = (float) $r['qty'];
                $price = (float) $r['price'];
                $total = $qty * $price;
                if ((string) $r['price'] !== '') $price_data[] = $total;

                $lines[] = array(
                    'kind'  => (int) $r['category_type'] === 0 ? 'machine' : 'item',
                    'name'  => (int) $r['category_type'] === 0
                               ? trim(strtoupper($q['machine_name']) . ' MODEL ' . strtoupper($q['machine_model_no']))
                                 . (trim($r['description']) !== '' && strcasecmp(trim($r['description']), trim($q['machine_model_no'])) !== 0 ? ' - ' . trim($r['description']) : '')
                               : $name($r['description']),
                    'qty'   => $qty,
                    'unit'  => trim((string) $r['unit']),
                    'price' => $price,
                    'total' => $total,
                );
            }

            if (!empty($cons[$id])) {
                $ct = 0;
                foreach ($cons[$id] as $c) $ct += (float) $c['price'] * (float) $c['qty'];
                $price_data[] = $ct;
                $lines[] = array('kind' => 'item', 'name' => '1 Year Consumable Spares', 'qty' => 1,
                                 'unit' => 'Set', 'price' => $ct, 'total' => $ct);
            }

            /* Basic cost only - see the class comment. */
            $basic = array_sum($price_data);

            $options = array();
            $opt_total = 0;
            foreach (isset($opt[$id]) ? $opt[$id] : array() as $o) {
                $qty = (float) $o['value'];
                $price = (float) $o['price'];
                $options[] = array('name' => ucwords(strtolower($name($o['description']))), 'qty' => $qty,
                                   'unit' => '', 'price' => $price, 'total' => $qty * $price);
                $opt_total += $qty * $price;
            }

            $out[$id] = array(
                'lines'     => $lines,
                'total'     => $basic,
                'options'   => $options,
                'opt_total' => $opt_total,
            );
        }

        return $out;
    }

    /** Everything the page and the export share. */
    private function _data()
    {
        $models = $this->_models();

        $model = (string) $this->input->get('model');
        if (!isset($models[$model])) $model = '';
        /* The "Model text contains" box was removed 2026-09-30 ("not
           required"); the model drop-down is the only selector now. */
        $text = '';

        /* Several companies may be picked (company[]); a plain company=
           link from before still works. '__all' wins over any names. */
        $sel = $this->input->get('company');
        $sel = array_values(array_unique(array_filter(array_map('trim', (array) $sel), 'strlen')));
        if (in_array('__all', $sel, true)) $sel = array('__all');

        if ($sel === array('__all'))  $label = 'All companies';
        elseif (count($sel) <= 3)     $label = implode(', ', $sel);
        else                          $label = count($sel) . ' companies';

        $data = array('models' => $models, 'model' => $model, 'text' => $text, 'selected' => $sel,
                      'companies' => array(), 'quotes' => array(), 'schedule' => array(),
                      'groups' => array(), 'grand' => array(),
                      'own_only' => !$this->_sees_all(),
                      'model_label' => $model !== '' ? $models[$model] : 'Any model',
                      'company_label' => $label);

        if ($model === '') return $data;

        /* Every company quoted for this model, with its quote count. */
        $counts = array();
        foreach ($this->_quotes($model, $text, array()) as $q) {
            if (!isset($counts[$q['company']])) $counts[$q['company']] = 0;
            $counts[$q['company']]++;
        }
        uksort($counts, 'strnatcasecmp');
        $data['companies'] = $counts;

        if (!$sel) return $data;

        $data['quotes']   = $this->_quotes($model, $text, $sel);
        $data['schedule'] = $this->_schedule($data['quotes']);

        /* Segregated by customer, each with its own per-currency totals,
           plus the overall per-currency totals. Per currency because quotes
           are in USD, INR and EUR, and one sum across them means nothing. */
        $add = function (&$bucket, $cur, $s) {
            if (!isset($bucket[$cur])) $bucket[$cur] = array('quotes' => 0, 'total' => 0, 'opt' => 0);
            $bucket[$cur]['quotes']++;
            $bucket[$cur]['total'] += $s['total'];
            $bucket[$cur]['opt']   += $s['opt_total'];
        };

        $groups = array();
        $grand  = array();
        foreach ($data['quotes'] as $q) {
            $s = $data['schedule'][(int) $q['id']];
            if (!isset($groups[$q['company']])) $groups[$q['company']] = array('quotes' => array(), 'grand' => array());
            $groups[$q['company']]['quotes'][] = $q;
            $add($groups[$q['company']]['grand'], $q['cur'], $s);
            $add($grand, $q['cur'], $s);
        }
        uksort($groups, 'strnatcasecmp');
        foreach ($groups as &$g) ksort($g['grand']);
        unset($g);
        ksort($grand);

        $data['groups'] = $groups;
        $data['grand']  = $grand;

        return $data;
    }

    public function index()
    {
        $this->load->view('model_quote_report/index', $this->_data());
    }

    /**
     * The report as .xlsx, two sheets: Summary (one row per quote, then the
     * per-currency totals) and Line Items (every row, one bordered block per
     * quote with an empty row between). Rows come in customer order, since
     * _quotes() sorts by company. Asked back 2026-09-30 after a one-sheet
     * layout was tried: "earlier excel was okay with 2 tabs use same setup".
     */
    public function export()
    {
        date_default_timezone_set('Asia/Kolkata');
        $d = $this->_data();

        if (empty($d['quotes'])) {
            redirect(page_url . 'Model_quote_report?' . http_build_query(array(
                'model' => $d['model'], 'company' => $d['selected'])));
            return;
        }

        $this->load->library('excel');

        $book = new PHPExcel();
        $book->getProperties()->setTitle('Model-wise Quote Report');

        $blue   = '0F62FE';
        $navy   = '16324F';
        $pale   = 'EEF4FF';
        $border = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,
                                                                  'color' => array('rgb' => 'D6DEEA'))));
        $head = function ($sheet, $range) use ($navy) {
            $sheet->getStyle($range)->applyFromArray(array(
                'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $navy)),
                'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
            ));
        };
        $title = function ($sheet, $text, $sub) use ($blue) {
            $sheet->setCellValue('A1', $text);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB($blue);
            $sheet->setCellValue('A2', $sub);
            $sheet->getStyle('A2')->getFont()->setItalic(true)->getColor()->setRGB('6C757D');
        };
        $numfmt = '#,##0.00';

        $sub = 'Model: ' . $d['model_label'] . ($d['text'] !== '' ? ' ("' . $d['text'] . '")' : '')
             . '   |   Customers: ' . $d['company_label']
             . '   |   ' . count($d['quotes']) . ' quotes   |   Basic cost only (no discount, packing, freight, taxes)'
             . '   |   Exported ' . date('d M Y, g:i a');

        /* ---------------- Summary ---------------- */
        $sh = $book->getActiveSheet();
        $sh->setTitle('Summary');
        $title($sh, 'Model-wise Quote Report', $sub);

        $cols = array('Ref No', 'Quote Date', 'Company', 'Contact', 'Quote Owner', 'Machine', 'Model',
                      'Currency', 'Basic Cost', 'Optional Items', 'Basic + Optional');
        $sh->fromArray($cols, null, 'A4');
        $head($sh, 'A4:K4');

        $r = 5;
        foreach ($d['quotes'] as $q) {
            $s = $d['schedule'][(int) $q['id']];
            $sh->fromArray(array(
                $q['ref_no'] !== '' ? $q['ref_no'] : 'Quote #' . $q['id'],
                $q['quotation_date'] ? date('d-m-Y', strtotime($q['quotation_date'])) : '',
                $q['company'], $q['contact'], $q['owner'], $q['machine_name'], $q['machine_model_no'],
                $q['cur'], $s['total'], $s['opt_total'], $s['total'] + $s['opt_total'],
            ), null, 'A' . $r);
            $r++;
        }
        $last = $r - 1;
        $sh->getStyle('I5:K' . $last)->getNumberFormat()->setFormatCode($numfmt);
        $sh->getStyle('A4:K' . $last)->applyFromArray($border);
        /* No setAutoFilter(): PHPExcel's writer does count($columns > 0), fatal on PHP 8. */

        $r += 1;
        $sh->setCellValue('A' . $r, 'TOTAL AMOUNT (per currency)');
        $sh->getStyle('A' . $r)->getFont()->setBold(true)->getColor()->setRGB($blue);
        $r++;
        $sh->fromArray(array('Currency', 'Quotes', 'Basic Cost', 'Optional Items', 'Grand Total'), null, 'A' . $r);
        $head($sh, 'A' . $r . ':E' . $r);
        $g0 = $r + 1;
        foreach ($d['grand'] as $cur => $g) {
            $r++;
            $sh->fromArray(array($cur, $g['quotes'], $g['total'], $g['opt'], $g['total'] + $g['opt']), null, 'A' . $r);
        }
        $sh->getStyle('C' . $g0 . ':E' . $r)->getNumberFormat()->setFormatCode($numfmt);
        $sh->getStyle('A' . $g0 . ':E' . $r)->applyFromArray(array(
            'font' => array('bold' => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $pale)),
        ) + $border);

        foreach (array('A' => 24, 'B' => 12, 'C' => 32, 'D' => 22, 'E' => 20, 'F' => 28, 'G' => 40,
                       'H' => 10, 'I' => 16, 'J' => 16, 'K' => 18) as $c => $w) {
            $sh->getColumnDimension($c)->setWidth($w);
        }
        $sh->freezePane('A5');

        /* ---------------- Line items ---------------- */
        $li = $book->createSheet();
        $li->setTitle('Line Items');
        $title($li, 'Quote Line Items', $sub);

        $cols = array('Ref No', 'Quote Date', 'Company', 'Quote Owner', 'Currency', 'Section', '#',
                      'Item', 'Qty', 'Unit', 'Price', 'Total');
        $li->fromArray($cols, null, 'A4');
        $head($li, 'A4:L4');

        $r = 5;
        foreach ($d['quotes'] as $q) {
            $s   = $d['schedule'][(int) $q['id']];
            $ref = $q['ref_no'] !== '' ? $q['ref_no'] : 'Quote #' . $q['id'];
            $dt  = $q['quotation_date'] ? date('d-m-Y', strtotime($q['quotation_date'])) : '';
            $start = $r;

            foreach (array('Price Schedule' => $s['lines'], 'Optional Items' => $s['options']) as $sec => $rows) {
                $i = 0;
                foreach ($rows as $l) {
                    $i++;
                    $li->fromArray(array($ref, $dt, $q['company'], $q['owner'], $q['cur'], $sec, $i,
                                         $l['name'], $l['qty'], $l['unit'], $l['price'], $l['total']), null, 'A' . $r);
                    if (isset($l['kind']) && $l['kind'] === 'machine') {
                        $li->getStyle('H' . $r)->getFont()->setBold(true);
                    }
                    $r++;
                }
                if ($rows) {
                    $label = $sec === 'Price Schedule' ? 'Basic Cost' : 'Optional Items Total';
                    $amt   = $sec === 'Price Schedule' ? $s['total'] : $s['opt_total'];
                    $li->fromArray(array($ref, $dt, $q['company'], $q['owner'], $q['cur'], $sec, '',
                                         $label, '', '', '', $amt), null, 'A' . $r);
                    $li->getStyle('H' . $r . ':L' . $r)->getFont()->setBold(true);
                    $li->getStyle('A' . $r . ':L' . $r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                       ->getStartColor()->setRGB($pale);
                    $r++;
                }
            }

            /* Each quote is its own bordered block with one empty row after
               it, so where one quote ends and the next begins is visible
               without reading the Ref No column (asked 2026-09-30). */
            if ($r > $start) {
                $li->getStyle('A' . $start . ':L' . ($r - 1))->applyFromArray($border);
                $r++;
            }
        }
        $li->getStyle('A4:L4')->applyFromArray($border);
        $last = $r - 1;
        if ($last >= 5) {
            $li->getStyle('K5:L' . $last)->getNumberFormat()->setFormatCode($numfmt);
        }
        foreach (array('A' => 24, 'B' => 12, 'C' => 30, 'D' => 20, 'E' => 10, 'F' => 15, 'G' => 5,
                       'H' => 60, 'I' => 8, 'J' => 8, 'K' => 15, 'L' => 16) as $c => $w) {
            $li->getColumnDimension($c)->setWidth($w);
        }
        $li->getStyle('H5:H' . max(5, $last))->getAlignment()->setWrapText(true);
        $li->freezePane('A5');

        $book->setActiveSheetIndex(0);

        $slug = preg_replace('/[^A-Za-z0-9]+/', '_', $d['model_label'] . '_' . (count($d['selected']) > 3 ? count($d['selected']) . '_companies' : $d['company_label']));
        $name = 'quote_report_' . substr(trim($slug, '_'), 0, 80) . '_' . date('Ymd') . '.xlsx';

        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');

        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
