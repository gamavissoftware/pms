<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Finished goods code for the first (fixed) line of Annexure-IV (Price Schedule).
 *
 * Format: SP (Shubham Pack) + FG (Finished Good) + MC (Machine) + 5-digit sequence, e.g. SPFGMC00001.
 * The number goes up by one for each new code. A code stands for one machine specification, so quotes with the same
 * specification share it and a new number is used only when a new specification appears. The specification is read
 * from the quotation (spec key, e.g. SP_600_L_4_5_60):
 *   SP | "Specify Machine Model (For Internal Purpose)"; when that is not selected, the Model in
 *      "Quotation Cum Technical Specification Of The Machine" | L (liquid/paste) or P (powder/granules)
 *      | No. of Tracks | number of axis rows | actual speed (the highest value when a range such as 50-60 is entered)
 * A part that cannot be read from the quotation is reported in 'missing'; such a quotation gets no code and no number is used.
 * The registry is the table fg_code_master (Database/fg_code_master_001.sql). The counter continues from the highest
 * number in it, or from fg_code_last_number (last number already created in SAP) when that is higher.
 */
class Fg_code
{
    const PREFIX = 'SP';
    const SEPARATOR = '_';
    const CODE_PREFIX = 'SPFGMC';

    private $model_types = array(1250, 1200, 1000, 800, 600, 300);

    /**
     * @param string     $model        Specify Machine Model (For Internal Purpose), e.g. 600
     * @param string     $fallback_model Model from the Quotation Cum Technical Specification, used when $model has no supported type
     * @param string|int $product_type 1 = Liquid / Paste, 2 = Powder / Granules (or the text itself)
     * @param string     $tracks       No. of Tracks text
     * @param int|string $axis_count   number of axis rows on the quotation
     * @param string     $speed        actual speed text (may contain HTML from the editor)
     * @param bool       $allocate     false = only look up the code of a known specification, never take a new number
     * @return array code (string, '' when incomplete), spec (string), parts (array), missing (array of part names)
     */
    public function build($model, $product_type, $tracks, $axis_count, $speed, $fallback_model = '', $allocate = true)
    {
        $parts = array(
            'model' => $this->model_type($model) !== '' ? $this->model_type($model) : $this->model_type($fallback_model),
            'type' => $this->liquid_or_powder($product_type),
            'track' => $this->first_number($tracks, false),
            'axis' => $this->first_number($axis_count, false),
            'speed' => $this->first_number($speed, true)
        );

        $missing = array();
        $spec = self::PREFIX;
        foreach ($parts as $name => $value) {
            if ($value === '') {
                $missing[] = $name;
                continue;
            }
            $spec .= self::SEPARATOR . $value;
        }

        $code = '';
        if (empty($missing)) {
            $code = $allocate ? $this->code_for_spec($spec) : $this->peek($spec);
        }

        return array('code' => $code, 'spec' => $spec, 'parts' => $parts, 'missing' => $missing);
    }

    /**
     * Existing code of a specification, or the next number that a new specification would receive.
     * @return array code ('' when the specification has no code yet), next (the code a new specification would get)
     */
    public function lookup($spec)
    {
        $existing = $this->peek($spec);
        return array('code' => $existing, 'next' => $existing !== '' ? '' : $this->format($this->next_number()));
    }

    // Code already registered for the specification, '' when there is none.
    private function peek($spec)
    {
        $ci =& get_instance();
        if (!$ci->db->table_exists('fg_code_master')) {
            return '';
        }

        $row = $ci->db->select('fg_code')->from('fg_code_master')->where('spec_key', $spec)->limit(1)->get()->row_array();
        return isset($row['fg_code']) ? $row['fg_code'] : '';
    }

    // Code of the specification; registers it with the next number when it is new.
    private function code_for_spec($spec)
    {
        $ci =& get_instance();
        if (!$ci->db->table_exists('fg_code_master')) {
            log_message('error', 'fg_code_master is missing: run Database/fg_code_master_001.sql.');
            return '';
        }

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $existing = $this->peek($spec);
            if ($existing !== '') {
                return $existing;
            }

            // INSERT IGNORE: a concurrent save taking the same number or specification is simply retried
            $seq = $this->next_number();
            $ci->db->query(
                'INSERT IGNORE INTO fg_code_master (seq, fg_code, spec_key, created_at) VALUES (?, ?, ?, ?)',
                array($seq, $this->format($seq), $spec, date('Y-m-d H:i:s'))
            );
        }

        log_message('error', 'Could not register finished goods code for ' . $spec);
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

    /**
     * Builds the code for a stored quotation record (quotation_* tables keyed by record_id).
     */
    public function for_record($record_id, $allocate = true)
    {
        $ci =& get_instance();
        $record_id = (int) $record_id;

        $customer = $ci->db->select('mach_model_no')->from('quotation_customer_data')
            ->where('id', $record_id)->limit(1)->get()->row_array();
        $one = $ci->db->select('product_to_be_packed')->from('quotation_annexture_1')
            ->where('record_id', $record_id)->limit(1)->get()->row_array();
        $tech = $ci->db->select('model')->from('quotation_cum_tech_spec')
            ->where('record_id', $record_id)->where("model <> ''", null, false)->order_by('id', 'asc')->limit(1)->get()->row_array();
        $two = $ci->db->select('actual_speed, no_of_track')->from('quotation_annexure_2')
            ->where('record_id', $record_id)->limit(1)->get()->row_array();
        $axis_rows = $ci->db->from('quotation_no_of_axis_in_machine')->where('record_id', $record_id)->count_all_results();

        return $this->build(
            isset($customer['mach_model_no']) ? $customer['mach_model_no'] : '',
            isset($one['product_to_be_packed']) ? $one['product_to_be_packed'] : '',
            isset($two['no_of_track']) ? $two['no_of_track'] : '',
            $axis_rows,
            isset($two['actual_speed']) ? $two['actual_speed'] : '',
            isset($tech['model']) ? $tech['model'] : '',
            $allocate
        );
    }

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

    // One of the supported model types (300, 600 ...) typed or selected on the quotation.
    private function model_type($text)
    {
        $text = trim(strip_tags((string) $text));
        foreach ($this->model_types as $number) {
            if (preg_match('/(?<![0-9.])' . $number . '(?![0-9])/', $text)) {
                return (string) $number;
            }
        }

        return '';
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
