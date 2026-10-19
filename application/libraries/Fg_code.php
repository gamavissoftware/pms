<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Finished goods code for the first (fixed) line of Annexure-IV (Price Schedule).
 *
 * SP + Model Type + L/P + Track + Axis + Speed, concatenated without separators, all read from the quotation:
 *   SP | "Specify Machine Model (For Internal Purpose)" | L (liquid/paste) or P (powder/granules)
 *      | No. of Tracks | number of axis rows | design speed (upper end when a range such as 50-60 is entered)
 * A part that cannot be read from the quotation is left out of the code and reported in 'missing'.
 */
class Fg_code
{
    const PREFIX = 'SP';

    private $model_types = array(1250, 1200, 1000, 800, 600, 300);

    /**
     * @param string     $model        Specify Machine Model (For Internal Purpose), e.g. 600
     * @param string|int $product_type 1 = Liquid / Paste, 2 = Powder / Granules (or the text itself)
     * @param string     $tracks       No. of Tracks text
     * @param int|string $axis_count   number of axis rows on the quotation
     * @param string     $speed        design speed text (may contain HTML from the editor)
     * @return array code (string), parts (array), missing (array of part names)
     */
    public function build($model, $product_type, $tracks, $axis_count, $speed)
    {
        $parts = array(
            'model' => $this->model_type($model),
            'type' => $this->liquid_or_powder($product_type),
            'track' => $this->first_number($tracks, false),
            'axis' => $this->first_number($axis_count, false),
            'speed' => $this->first_number($speed, true)
        );

        $missing = array();
        $code = self::PREFIX;
        foreach ($parts as $name => $value) {
            if ($value === '') {
                $missing[] = $name;
            }
            $code .= $value;
        }

        return array('code' => $code, 'parts' => $parts, 'missing' => $missing);
    }

    /**
     * Builds the code for a stored quotation record (quotation_* tables keyed by record_id).
     */
    public function for_record($record_id)
    {
        $ci =& get_instance();
        $record_id = (int) $record_id;

        $customer = $ci->db->select('mach_model_no')->from('quotation_customer_data')
            ->where('id', $record_id)->limit(1)->get()->row_array();
        $one = $ci->db->select('product_to_be_packed')->from('quotation_annexture_1')
            ->where('record_id', $record_id)->limit(1)->get()->row_array();
        $two = $ci->db->select('speed, no_of_track')->from('quotation_annexure_2')
            ->where('record_id', $record_id)->limit(1)->get()->row_array();
        $axis_rows = $ci->db->from('quotation_no_of_axis_in_machine')->where('record_id', $record_id)->count_all_results();

        return $this->build(
            isset($customer['mach_model_no']) ? $customer['mach_model_no'] : '',
            isset($one['product_to_be_packed']) ? $one['product_to_be_packed'] : '',
            isset($two['no_of_track']) ? $two['no_of_track'] : '',
            $axis_rows,
            isset($two['speed']) ? $two['speed'] : ''
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

    // First whole number in the text. With $range_upper a leading range such as "50-60" gives its upper end (60).
    private function first_number($text, $range_upper)
    {
        $text = trim(html_entity_decode(strip_tags((string) $text)));
        if (!preg_match('/(\d+)(?:\s*(?:-|\x{2013}|&ndash;|to)\s*(\d+))?/u', $text, $m)) {
            return '';
        }

        $value = ($range_upper && isset($m[2]) && $m[2] !== '') ? (int) $m[2] : (int) $m[1];
        return $value > 0 ? (string) $value : '';
    }
}
