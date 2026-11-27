<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * One-off, CLI-only: sends the finished goods code of every existing quotation to SAP as an Item.
 *   php index.php Fg_item_backfill run            dry run: counts what would be sent, changes nothing
 *   php index.php Fg_item_backfill run 1 [limit]  sends (optionally only the first <limit> codes); safe to re-run,
 *                                                 codes already logged as SUCCESS are skipped
 */
class Fg_item_backfill extends CI_Controller
{
    public function run($write = 0, $limit = 0)
    {
        if (!is_cli()) {
            show_404();
        }
        if (!$this->db->table_exists('SAP_item_sync')) {
            echo "Table SAP_item_sync is missing: run Database/sap_item_sync_006.sql first.\n";
            return;
        }

        $this->db->select('a.record_id, a.fg_code')->from('quotation_annexture_4 a')
            ->join('SAP_item_sync s', "s.fg_code = a.fg_code AND s.sync_status = 'SUCCESS'", 'left')
            ->where('a.flag', 0)->where('a.category_type', 0)->where("a.fg_code <> ''", null, false)->where('s.fg_code IS NULL', null, false)
            ->group_by('a.fg_code')->order_by('a.record_id', 'asc');
        if ((int) $limit > 0) {
            $this->db->limit((int) $limit);
        }
        $rows = $this->db->get()->result_array();

        echo ((int) $write === 1 ? 'Sending ' : 'Would send ') . count($rows) . " finished goods codes\n";
        if ((int) $write !== 1) {
            return;
        }

        $this->load->library('Sap_service');
        $cookie = $this->sap_service->session_cookie();
        if ($cookie === null) {
            echo "SAP login failed.\n";
            return;
        }

        $ok = 0;
        $failed = array();
        foreach ($rows as $i => $row) {
            if ($i > 0 && $i % 200 === 0) {
                $cookie = $this->sap_service->session_cookie();   // a fresh session for long runs
            }
            $result = $this->sap_service->sync_fg_item((int) $row['record_id'], $cookie);
            if (!empty($result['success'])) {
                $ok++;
            } else {
                $failed[] = $row['fg_code'] . ': ' . (isset($result['message']) ? $result['message'] : 'unknown error');
            }
        }

        echo 'Sent or already in SAP: ' . $ok . ' | failed: ' . count($failed) . "\n";
        foreach (array_slice($failed, 0, 20) as $line) {
            echo '  ' . $line . "\n";
        }
    }
}
