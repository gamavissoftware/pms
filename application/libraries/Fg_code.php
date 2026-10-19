<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Finished goods code for the first (fixed) line of Annexure-IV (Price Schedule).
 *
 * SP + Model Type + L/P + Track + Axis + Speed, concatenated without separators:
 *   SP | 300/600/800/1000/1200/1250 | L (liquid) or P (powder) | 6/8/10/12 tracks | 3/5/7 axis | 60/80/100 speed
 * A part that cannot be read from the quotation (missing, or ambiguous such as a speed range) is left out of the
 * code and reported in 'missing', so the code is never built from a guess.
 */
class Fg_code
{
    const PREFIX = 'SP';

    private $model_types = array(1250, 1200, 1000, 800, 600, 300);
    private $tracks = array(12, 10, 8, 6);
    private $axes = array(7, 5, 3);
    private $speeds = array(100, 80, 60);

    /**
     * @param string     $model        model text as typed on the quotation
     * @param string|int $product_type 1 = Liquid / Paste, 2 = Powder / Granules (or the text itself)
     * @param string     $tracks       No. of Tracks text
     * @param int|string $axis_total   total of the axis counts entered on the quotation
     * @param string     $speed        design speed text (may contain HTML from the editor)
     * @return array code (string), parts (array), missing (array of part names)
     */
    public function build($model, $product_type, $tracks, $axis_total, $speed)
    {
        $parts = array(
            'model' => $this->pick($model, $this->model_types),
            'type' => $this->liquid_or_powder($product_type),
            'track' => $this->pick($tracks, $this->tracks, true),
            'axis' => $this->pick($axis_total, $this->axes, true),
            'speed' => $this->pick($speed, $this->speeds)
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

        $line = $ci->db->select('description')->from('quotation_annexture_4')
            ->where('record_id', $record_id)->where('flag', 0)->order_by('id', 'asc')->limit(1)->get()->row_array();
        $one = $ci->db->select('product_to_be_packed')->from('quotation_annexture_1')
            ->where('record_id', $record_id)->limit(1)->get()->row_array();
        $two = $ci->db->select('speed, no_of_track')->from('quotation_annexure_2')
            ->where('record_id', $record_id)->limit(1)->get()->row_array();
        $axis = $ci->db->select_sum('axiscount')->from('quotation_no_of_axis_in_machine')
            ->where('record_id', $record_id)->get()->row_array();

        return $this->build(
            isset($line['description']) ? $line['description'] : '',
            isset($one['product_to_be_packed']) ? $one['product_to_be_packed'] : '',
            isset($two['no_of_track']) ? $two['no_of_track'] : '',
            isset($axis['axiscount']) ? $axis['axiscount'] : '',
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

    // The one allowed number found as a whole number in the text; '' when none or more than one is found.
    // $exact_only: the whole text must be that number (tracks / axis are plain counts, not sentences).
    private function pick($text, $allowed, $exact_only = false)
    {
        $text = trim(html_entity_decode(strip_tags((string) $text)));
        if ($text === '') {
            return '';
        }

        if ($exact_only) {
            return ctype_digit($text) && in_array((int) $text, $allowed, true) ? (string) (int) $text : '';
        }

        $found = array();
        foreach ($allowed as $number) {
            if (preg_match('/(?<![0-9.])' . $number . '(?![0-9])/', $text)) {
                $found[] = $number;
            }
        }

        return count($found) === 1 ? (string) $found[0] : '';
    }
}
