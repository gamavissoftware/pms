<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * One-off, CLI-only: fills the finished goods code on the first Annexure-IV line of existing quotations.
 *   php index.php Fg_code_backfill run        dry run: prints what would be written, changes nothing
 *   php index.php Fg_code_backfill run 1      writes the codes
 *   php index.php Fg_code_backfill run 1 1    also replaces codes that are not in the SPFGMC format
 *   php index.php Fg_code_backfill run 1 2    recomputes every saved code from the quotation (after a rule change)
 * Only quotations whose model, L/P, track, axis and speed can all be read get a code (SPFGMC + number; quotations with
 * the same specification share one). The others are listed so the model can be selected on their edit page.
 * Mode 1 1 also replaces codes that are not in the SPFGMC format; the dry run never takes a number.
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
        $lines = $this->db->select('id, record_id, fg_code')->from('quotation_annexture_4')->where('flag', 0)->where('category_type', 0);
        if ((int) $rewrite !== 2) {
            $this->db->group_start()->where('fg_code IS NULL', null, false)->or_where('fg_code', '');
            if ((int) $rewrite === 1) {
                $this->db->or_where("fg_code NOT LIKE 'SPFGMC%'", null, false);
            }
            $this->db->group_end();
        }
        $lines = $this->db->order_by('record_id', 'asc')->order_by('id', 'asc')->get()->result_array();

        $done = 0;
        $cleared = 0;
        $incomplete = array();
        $seen = array();
        foreach ($lines as $line) {
            // one model line per quotation record
            if (isset($seen[$line['record_id']])) {
                continue;
            }
            $seen[$line['record_id']] = true;

            // numbers are taken only when writing; a dry run just reports
            $built = $this->fg_code->for_record($line['record_id'], (int) $write === 1);
            $had_code = trim((string) $line['fg_code']) !== '';
            if (!empty($built['missing'])) {
                $incomplete[] = 'record ' . $line['record_id'] . ' ' . $built['spec'] . ' (missing: ' . implode(',', $built['missing']) . ')';
                // a code that can no longer be built (old partial code) is cleared in rewrite mode
                if ((int) $write === 1 && (int) $rewrite > 0 && $had_code) {
                    $this->db->where('id', (int) $line['id'])->update('quotation_annexture_4', array('fg_code' => ''));
                    $cleared++;
                }
                continue;
            }

            if ((int) $write === 1) {
                if ($built['code'] !== '') {
                    $this->db->where('id', (int) $line['id'])->update('quotation_annexture_4', array('fg_code' => $built['code']));
                    $done++;
                }
            } else {
                echo 'record ' . $line['record_id'] . ' -> ' . ($built['code'] !== '' ? $built['code'] : $built['spec'] . ' (new)') . "\n";
                $done++;
            }
        }

        echo ((int) $write === 1 ? 'Written: ' : 'Would write: ') . $done . ' | cleared: ' . $cleared . ' | left without a code: ' . count($incomplete) . "\n";
        foreach ($incomplete as $row) {
            echo '  ' . $row . "\n";
        }
    }
}
