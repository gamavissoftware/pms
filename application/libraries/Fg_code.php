<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Finished goods code and titles for the first (fixed) line of Annexure-IV (Price Schedule).
 *
 * Code: SP (Shubham Pack) + FG (Finished Good) + MC (Machine) + 5-digit number, e.g. SPFGMC00001.
 *   Numbered from the first quotation in the system; every quotation takes the next number once and keeps it.
 *   Registry: table fg_code_master (Database/fg_code_master_001.sql). The counter continues from the highest number in
 *   it, or from fg_code_last_number (last number already created in SAP) when that is higher.
 * Title: SP-{model}{L|P}-{tracks}T-{axis}A-{speed} | {machine type}, e.g.
 *   SP-1200L-12T-5A-100 | VFFS SINGLE TRACK MACHINE
 *   Title 1 and Title 2 start out equal (existing quotations were filled this way); Title 2 can be edited by the sales person.
 */
class Fg_code
{
    const CODE_PREFIX = 'SPFGMC';

    /** Code of the quotation, registering the next number when it has none yet. '' only if the registry is missing. */
    public function assign($record_id)
    {
        $ci =& get_instance();
        $record_id = (int) $record_id;
        if ($record_id <= 0 || !$ci->db->table_exists('fg_code_master')) {
            if ($record_id > 0) {
                log_message('error', 'fg_code_master is missing: run Database/fg_code_master_001.sql.');
            }
            return '';
        }

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $existing = $this->code_of($record_id);
            if ($existing !== '') {
                return $existing;
            }

            // INSERT IGNORE: a concurrent save taking the same number is simply retried with the next one
            $seq = $this->next_number();
            $ci->db->query(
                'INSERT IGNORE INTO fg_code_master (seq, fg_code, record_id, created_at) VALUES (?, ?, ?, ?)',
                array($seq, $this->format($seq), $record_id, date('Y-m-d H:i:s'))
            );
        }

        log_message('error', 'Could not register a finished goods code for quotation ' . $record_id);
        return '';
    }

    /** Code already registered for the quotation, '' when it has none. */
    public function code_of($record_id)
    {
        $ci =& get_instance();
        if (!$ci->db->table_exists('fg_code_master')) {
            return '';
        }

        $row = $ci->db->select('fg_code')->from('fg_code_master')->where('record_id', (int) $record_id)->limit(1)->get()->row_array();
        return isset($row['fg_code']) ? $row['fg_code'] : '';
    }

    /** The code the next new quotation will receive (shown on the form before it is saved). */
    public function next_code()
    {
        $ci =& get_instance();
        return $ci->db->table_exists('fg_code_master') ? $this->format($this->next_number()) : '';
    }

    /** Title of a stored quotation (Title 1 and Title 2 start out equal). */
    public function title1_for_record($record_id)
    {
        $ci =& get_instance();
        $record_id = (int) $record_id;

        $header = $ci->db->select('mach_model_no, machine_name')->from('quotation_customer_data')->where('id', $record_id)->limit(1)->get()->row_array();
        $tech = $ci->db->select('model')->from('quotation_cum_tech_spec')
            ->where('record_id', $record_id)->where("model <> ''", null, false)->order_by('id', 'asc')->limit(1)->get()->row_array();
        $one = $ci->db->select('product_to_be_packed')->from('quotation_annexture_1')->where('record_id', $record_id)->limit(1)->get()->row_array();
        $two = $ci->db->select('no_of_track, actual_speed')->from('quotation_annexure_2')->where('record_id', $record_id)->limit(1)->get()->row_array();
        $axis_rows = $ci->db->from('quotation_no_of_axis_in_machine')->where('record_id', $record_id)->count_all_results();

        return $this->title1(
            isset($header['mach_model_no']) ? $header['mach_model_no'] : '',
            isset($tech['model']) ? $tech['model'] : '',
            isset($header['machine_name']) ? $header['machine_name'] : '',
            isset($one['product_to_be_packed']) ? $one['product_to_be_packed'] : '',
            isset($two['no_of_track']) ? $two['no_of_track'] : '',
            isset($two['actual_speed']) ? $two['actual_speed'] : '',
            $axis_rows
        );
    }

    /**
     * Title = SP-{model type}{L|P}-{tracks}T-{axis}A-{speed} followed by the machine type, e.g.
     *   SP-1200L-12T-5A-100 | VFFS SINGLE TRACK MACHINE
     *   model type : "Specify Machine Model (For Internal Purpose)" (300, 600, 800, 1000, 1200, 1250); when that is not
     *                selected, the Model in "Quotation Cum Technical Specification Of The Machine"
     *   L / P      : Product to be packed - Liquid / Paste = L, Powder / Granules = P
     *   tracks, axis, speed : No. of Tracks, number of axis rows, actual speed (the highest value of a range)
     * A part that is empty on the quotation is left out.
     */
    public function title1($model, $fallback_model, $machine_type, $product, $tracks, $speed, $axis_count)
    {
        $model_type = $this->model_type($model) !== '' ? $this->model_type($model) : $this->model_type($fallback_model);
        $head = 'SP';
        $lp = $this->liquid_or_powder($product);
        if ($model_type !== '' || $lp !== '') {
            $head .= '-' . $model_type . $lp;
        }

        $pieces = array($head);
        $track = $this->first_number($tracks, false);
        if ($track !== '') {
            $pieces[] = $track . 'T';
        }
        $axis = $this->first_number($axis_count, false);
        if ($axis !== '') {
            $pieces[] = $axis . 'A';
        }
        $speed_value = $this->first_number($speed, true);
        if ($speed_value !== '') {
            $pieces[] = $speed_value;
        }

        $title = implode('-', $pieces);
        $machine_type = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $machine_type))));
        if ($machine_type !== '') {
            $title .= ' | ' . $machine_type;
        }

        return substr($title, 0, 255);
    }

    private $model_types = array(1250, 1200, 1000, 800, 600, 300);

    // One of the supported model types found in the text (as a whole number), '' when none.
    private function model_type($text)
    {
        $text = trim(html_entity_decode(strip_tags((string) $text)));
        foreach ($this->model_types as $number) {
            if (preg_match('/(?<![0-9.])' . $number . '(?![0-9])/', $text)) {
                return (string) $number;
            }
        }

        return '';
    }

    // 1 / Liquid / Paste = L, 2 / Powder / Granules = P, '' otherwise.
    private function liquid_or_powder($value)
    {
        $value = strtolower(trim(strip_tags((string) $value)));
        if ($value === '1' || preg_match('/liquid|paste/', $value)) {
            return 'L';
        }
        if ($value === '2' || preg_match('/powder|granul/', $value)) {
            return 'P';
        }

        return '';
    }

    private function next_number()
    {
        $ci =& get_instance();
        $row = $ci->db->select_max('seq')->from('fg_code_master')->get()->row_array();
        $last = isset($row['seq']) ? (int) $row['seq'] : 0;
        $sap_last = defined('fg_code_last_number') ? (int) fg_code_last_number : 0;

        return max($last, $sap_last) + 1;
    }

    private function format($number)
    {
        return self::CODE_PREFIX . str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }

    // First whole number in the text. With $range_upper a leading range such as "50-60" gives its highest end (60).
    private function first_number($text, $range_upper)
    {
        $text = trim(html_entity_decode(strip_tags((string) $text)));
        if (!preg_match('/(\d+)(?:\s*(?:-|\x{2013}|&ndash;|to)\s*(\d+))?/u', $text, $m)) {
            return '';
        }

        $value = ($range_upper && isset($m[2]) && $m[2] !== '') ? max((int) $m[1], (int) $m[2]) : (int) $m[1];
        return $value > 0 ? (string) $value : '';
    }
}
