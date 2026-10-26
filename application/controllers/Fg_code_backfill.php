<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * One-off, CLI-only: gives every existing quotation its finished goods code (SPFGMC00001, SPFGMC00002 ...) in order of
 * quotation id, starting from the first quotation, on its first Annexure-IV line. Titles are not touched (they are blank by default and filled by the sales person).
 *   php index.php Fg_code_backfill run        dry run: reports what would be done, changes nothing, takes no numbers
 *   php index.php Fg_code_backfill run 1      writes the codes
 * Safe to run again: a quotation keeps the code it already has, and titles are never changed.
 */
class Fg_code_backfill extends CI_Controller
{
    public function run($write = 0)
    {
        if (!is_cli()) {
            show_404();
        }
        foreach (array('fg_code', 'fg_title1', 'fg_title2') as $column) {
            if (!$this->db->field_exists($column, 'quotation_annexture_4')) {
                echo "Column $column is missing: run Database/quotation_fg_code_001.sql and quotation_fg_titles_002.sql first.\n";
                return;
            }
        }
        if (!$this->db->table_exists('fg_code_master')) {
            echo "Table fg_code_master is missing: run Database/fg_code_master_001.sql first.\n";
            return;
        }

        $this->load->library('Fg_code');
        $quotes = $this->db->select('id')->from('quotation_customer_data')->order_by('id', 'asc')->get()->result_array();

        $coded = 0;
        $no_line = array();
        foreach ($quotes as $quote) {
            $record_id = (int) $quote['id'];
            $line = $this->db->select('id, fg_title1, fg_title2')->from('quotation_annexture_4')
                ->where('record_id', $record_id)->where('flag', 0)->where('category_type', 0)->order_by('id', 'asc')->limit(1)->get()->row_array();
            if (empty($line)) {
                $no_line[] = $record_id;
            }

            if ((int) $write !== 1) {
                $coded++;
                continue;
            }

            $code = $this->fg_code->assign($record_id);
            if ($code === '' || empty($line)) {
                continue;
            }

            $this->db->where('id', (int) $line['id'])->update('quotation_annexture_4', array('fg_code' => $code));
            $coded++;
        }

        // lines of quotations that no longer exist must not keep an old-format code
        if ((int) $write === 1) {
            $this->db->query("UPDATE quotation_annexture_4 a LEFT JOIN quotation_customer_data q ON q.id = a.record_id SET a.fg_code = '' WHERE q.id IS NULL AND a.fg_code <> ''");
        }

        echo ((int) $write === 1 ? 'Coded: ' : 'Would code: ') . $coded . ' quotations (first id ' . (isset($quotes[0]) ? $quotes[0]['id'] : '-') . ', last id ' . (count($quotes) ? $quotes[count($quotes) - 1]['id'] : '-') . ')' . "\n";
        echo 'Quotations with no model line to store it on: ' . count($no_line) . (count($no_line) ? ' (ids ' . implode(',', array_slice($no_line, 0, 20)) . ')' : '') . "\n";
    }
}
