<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * One-off, CLI-only: fills the finished goods code on the first Annexure-IV line of existing quotations.
 *   php index.php Fg_code_backfill run        dry run: prints what would be written, changes nothing
 *   php index.php Fg_code_backfill run 1      writes the codes
 *   php index.php Fg_code_backfill run 1 1    also rewrites codes saved in the old format without underscores
 * Only complete codes (model, L/P, track, axis and speed all readable) are written. Quotations whose values
 * cannot be read are listed so the code can be entered by hand on their edit page; nothing is guessed.
 */
class Fg_code_backfill extends CI_Controller
{
    public function run($write = 0, $rewrite = 0)
    {
        if (!is_cli()) {
            show_404();
        }
        if (!$this->db->field_exists('fg_code', 'quotation_annexture_4')) {
            echo "Column fg_code is missing: run Database/quotation_fg_code_001.sql first.\n";
            return;
        }

        $this->load->library('Fg_code');
        $lines = $this->db->select('id, record_id, fg_code')->from('quotation_annexture_4')->where('flag', 0)
            ->group_start()->where('fg_code IS NULL', null, false)->or_where('fg_code', '');
        if ((int) $rewrite === 1) {
            $this->db->or_where("fg_code NOT LIKE '%\\_%'", null, false);
        }
        $lines = $this->db->group_end()
            ->order_by('id', 'asc')->get()->result_array();

        $done = 0;
        $incomplete = array();
        $seen = array();
        foreach ($lines as $line) {
            // one model line per quotation record
            if (isset($seen[$line['record_id']])) {
                continue;
            }
            $seen[$line['record_id']] = true;

            $built = $this->fg_code->for_record($line['record_id']);
            // an existing code in the old format is rewritten even when incomplete; empty ones only when complete
            $had_code = trim((string) $line['fg_code']) !== '';
            if (!empty($built['missing']) && !((int) $rewrite === 1 && $had_code)) {
                $incomplete[] = 'record ' . $line['record_id'] . ' ' . $built['code'] . ' (missing: ' . implode(',', $built['missing']) . ')';
                continue;
            }

            if ((int) $write === 1) {
                $this->db->where('id', (int) $line['id'])->update('quotation_annexture_4', array('fg_code' => $built['code']));
            } else {
                echo 'record ' . $line['record_id'] . ' -> ' . $built['code'] . "\n";
            }
            $done++;
        }

        echo ((int) $write === 1 ? 'Written: ' : 'Would write: ') . $done . ' | left for manual entry: ' . count($incomplete) . "\n";
        foreach ($incomplete as $row) {
            echo '  ' . $row . "\n";
        }
    }
}
