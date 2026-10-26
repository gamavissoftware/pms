<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Finished goods code and titles for the first (fixed) line of Annexure-IV (Price Schedule).
 *
 * Code: SP (Shubham Pack) + FG (Finished Good) + MC (Machine) + 5-digit number, e.g. SPFGMC00001.
 *   Numbered from the first quotation in the system; every quotation takes the next number once and keeps it.
 *   Registry: table fg_code_master (Database/fg_code_master_001.sql). The counter continues from the highest number in
 *   it, or from fg_code_last_number (last number already created in SAP) when that is higher.
 * Title: Machine Model No | Machine Type | Product to be packed | No. of tracks | Actual speed | No. of axis, e.g.
 *   SPM-300 | VFFS MULTI TRACK MACHINE | Liquid / Paste | 4 Track | Speed 50 | 5 Axis
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

        $header = $ci->db->select('machine_model_no, machine_name')->from('quotation_customer_data')->where('id', $record_id)->limit(1)->get()->row_array();
        $one = $ci->db->select('product_to_be_packed')->from('quotation_annexture_1')->where('record_id', $record_id)->limit(1)->get()->row_array();
        $two = $ci->db->select('no_of_track, actual_speed')->from('quotation_annexure_2')->where('record_id', $record_id)->limit(1)->get()->row_array();
        $axis_rows = $ci->db->from('quotation_no_of_axis_in_machine')->where('record_id', $record_id)->count_all_results();

        return $this->title1(
            isset($header['machine_model_no']) ? $header['machine_model_no'] : '',
            isset($header['machine_name']) ? $header['machine_name'] : '',
            isset($one['product_to_be_packed']) ? $one['product_to_be_packed'] : '',
            isset($two['no_of_track']) ? $two['no_of_track'] : '',
            isset($two['actual_speed']) ? $two['actual_speed'] : '',
            $axis_rows
        );
    }

    /**
     * Title = Machine Model No | Machine Type | Product to be packed | No. of tracks | Actual speed | No. of axis, e.g.
     *   SPM-300 | VFFS MULTI TRACK MACHINE | Liquid / Paste | 4 Track | Speed 50 | 5 Axis
     * A part that is empty on the quotation is left out.
     *
     * @param string     $model_no     "Machine Model No." (General Information)
     * @param string     $machine_type machine name / type of the quotation
     * @param string|int $product      Product to be packed: 1 = Liquid / Paste, 2 = Powder / Granules (or the text itself)
     * @param string     $tracks       No. of Tracks text
     * @param string     $speed        actual speed text (may contain HTML); the highest value when a range is entered
     * @param int|string $axis_count   number of axis rows on the quotation
     */
    public function title1($model_no, $machine_type, $product, $tracks, $speed, $axis_count)
    {
        $clean = function ($text) {
            return trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $text))));
        };

        $parts = array();
        foreach (array($clean($model_no), $clean($machine_type)) as $text) {
            if ($text !== '') {
                $parts[] = $text;
            }
        }

        $product_names = array('1' => 'Liquid / Paste', '2' => 'Powder / Granules');
        $product = $clean($product);
        $product = isset($product_names[$product]) ? $product_names[$product] : $product;
        if ($product !== '') {
            $parts[] = $product;
        }

        $track = $this->first_number($tracks, false);
        if ($track !== '') {
            $parts[] = $track . ' Track';
        }

        $speed_value = $this->first_number($speed, true);
        if ($speed_value !== '') {
            $parts[] = 'Speed ' . $speed_value;
        }

        $axis = $this->first_number($axis_count, false);
        if ($axis !== '') {
            $parts[] = $axis . ' Axis';
        }

        return substr(implode(' | ', $parts), 0, 255);
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
