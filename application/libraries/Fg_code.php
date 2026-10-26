<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Finished goods code and titles for the first (fixed) line of Annexure-IV (Price Schedule).
 *
 * Code: SP (Shubham Pack) + FG (Finished Good) + MC (Machine) + 5-digit number, e.g. SPFGMC00001.
 *   Numbered from the first quotation in the system; every quotation takes the next number once and keeps it.
 *   Registry: table fg_code_master (Database/fg_code_master_001.sql). The counter continues from the highest number in
 *   it, or from fg_code_last_number (last number already created in SAP) when that is higher.
 * Title 1: machine type | tracks | speed | axis details, built from the stored quotation, e.g.
 *   VFFS MULTI TRACK MACHINE | 4 Track | Speed 50 | Axis: Laminate Pulling x1, Vertical Sealing x2
 *   Title 2 starts as a copy of Title 1 and can be edited by the sales person.
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

    /** Title 1 of a stored quotation. */
    public function title1_for_record($record_id)
    {
        $ci =& get_instance();
        $record_id = (int) $record_id;

        $header = $ci->db->select('machine_name')->from('quotation_customer_data')->where('id', $record_id)->limit(1)->get()->row_array();
        $two = $ci->db->select('no_of_track, actual_speed')->from('quotation_annexure_2')->where('record_id', $record_id)->limit(1)->get()->row_array();
        $axis = $ci->db->select('description, axiscount')->from('quotation_no_of_axis_in_machine')
            ->where('record_id', $record_id)->order_by('id', 'asc')->get()->result_array();

        $rows = array();
        foreach ($axis as $a) {
            $rows[] = array('name' => $a['description'], 'count' => $a['axiscount']);
        }

        return $this->title1(
            isset($header['machine_name']) ? $header['machine_name'] : '',
            isset($two['no_of_track']) ? $two['no_of_track'] : '',
            isset($two['actual_speed']) ? $two['actual_speed'] : '',
            $rows
        );
    }

    /**
     * @param string $machine_type machine name / type of the quotation
     * @param string $tracks       No. of Tracks text
     * @param string $speed        actual speed text (may contain HTML); the highest value when a range is entered
     * @param array  $axis_rows    list of array(name, count)
     */
    public function title1($machine_type, $tracks, $speed, $axis_rows)
    {
        $parts = array();

        $machine_type = trim(preg_replace('/\s+/', ' ', strip_tags((string) $machine_type)));
        if ($machine_type !== '') {
            $parts[] = $machine_type;
        }

        $track = $this->first_number($tracks, false);
        if ($track !== '') {
            $parts[] = $track . ' Track';
        }

        $speed_value = $this->first_number($speed, true);
        if ($speed_value !== '') {
            $parts[] = 'Speed ' . $speed_value;
        }

        $axis = array();
        foreach ((array) $axis_rows as $row) {
            $name = trim(preg_replace('/\s+/', ' ', strip_tags((string) $row['name'])));
            if ($name === '') {
                continue;
            }
            $count = trim((string) $row['count']);
            $axis[] = $name . (is_numeric($count) && (float) $count > 0 ? ' x' . (int) $count : '');
        }
        if (!empty($axis)) {
            $parts[] = 'Axis: ' . implode(', ', $axis);
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
