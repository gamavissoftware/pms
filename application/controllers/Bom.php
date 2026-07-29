<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bom — Automation BOM Generator
 *
 * Conventions match application/controllers/Df_dispatch_plan.php: the
 * session logged_in check in the constructor, the json_only()/respond()
 * AJAX pattern, full-document views that inline common/nav-menu,
 * common/info-section.php and common/footer, and assets referenced
 * through the assets_url / page_url constants.
 *
 * CSRF is left exactly as the project has it ($config['csrf_protection']
 * is FALSE application-wide); no tokens are introduced here.
 *
 * URLs are /abom/... — see application/config/routes.php. The class
 * keeps the spec's file name (section 4) while the routes give the
 * module its own URL namespace, away from the existing DF register and
 * the Store/FMS BOM screens.
 *
 * PHP 7.4 compatible (production runs ea-php74).
 */
class Bom extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in') == false) {
            redirect(page_url);
        }

        $this->config->load('abom', true);

        $this->load->model('Bom_master_model');
        $this->load->model('Bom_item_model');
        $this->load->helper('bom_helper');
    }

    // -----------------------------------------------------------------
    // SCREENS
    // -----------------------------------------------------------------

    public function index()
    {
        redirect(page_url . 'abom/reference/iqr');
    }

    /**
     * Read-only released-document view of a saved BOM.
     *
     * @param int $bom_id
     */
    public function view($bom_id = 0)
    {
        if (!$this->require_tables()) {
            return;
        }

        $this->load->model('Bom_model');
        $bom = $this->Bom_model->get_bom((int) $bom_id);

        if (!$bom) {
            show_404();
            return;
        }

        $lines = $this->Bom_model->get_lines((int) $bom_id);

        $this->render_document($bom, $lines, false);
    }

    /**
     * Renders one of the two Appendix B reference configurations
     * straight from the engine, without persisting anything.
     *
     * This is the read-only document view driven by live master data —
     * it is what the department compares against the released DF, and
     * what proves the engine and the design agree.
     *
     * @param string $key  'fx5' | 'iqr'
     */
    public function reference($key = 'iqr')
    {
        if (!$this->require_tables()) {
            return;
        }

        $presets = $this->reference_presets();
        $key     = isset($presets[$key]) ? $key : 'iqr';
        $preset  = $presets[$key];

        $this->load->library('Bom_engine');
        $result = $this->Bom_engine->generate($preset['cfg']);

        $bom = $this->bom_header_from_config($preset['cfg'], $result);
        $bom->bom_no = $preset['bom_no'];
        $bom->df_ref = $preset['df_ref'];
        $bom->revision = $preset['revision'];

        $this->render_document($bom, $result['lines'], false, $result);
    }

    /**
     * Print stylesheet applied, application chrome suppressed. Same
     * document, same partials — the print CSS does the work.
     *
     * @param int $bom_id
     */
    public function printable($bom_id = 0)
    {
        $this->view($bom_id);
    }

    // -----------------------------------------------------------------
    // SHARED RENDER
    // -----------------------------------------------------------------

    /**
     * @param object $bom
     * @param array  $lines
     * @param bool   $editable
     * @param array  $result   optional engine result (reference/generate)
     */
    private function render_document($bom, $lines, $editable, $result = null)
    {
        $this->load->library('Bom_engine');

        $family_code = $this->Bom_master_model->family_code((int) $bom->plc_family_id);
        $defaults    = $this->Bom_master_model->family_defaults((int) $bom->plc_family_id);

        $stats = ($result !== null)
            ? $result['stats']
            : $this->Bom_engine->stats($lines);

        $sections = ($result !== null)
            ? $result['sections']
            : $this->Bom_engine->sections_present($lines);

        $features = $this->Bom_master_model->get_features();

        $active_features = array();
        if (!empty($bom->features_json)) {
            $decoded = json_decode($bom->features_json, true);
            if (is_array($decoded)) {
                $active_features = $decoded;
            }
        }
        if (empty($active_features)) {
            $active_features = $this->Bom_master_model->default_features();
        }

        $data = array(
            'bom'                => $bom,
            'lines'              => $lines,
            'sections'           => $sections,
            'stats'              => $stats,
            'editable'           => (bool) $editable,
            'family_code'        => $family_code,
            'family_explanation' => ($result !== null && isset($result['family']['explanation']))
                                        ? $result['family']['explanation'] : '',
            'overridden'         => ($result !== null) ? !empty($result['overridden'])
                                        : !empty($bom->plc_family_locked),
            'panel_location'     => $defaults['panel_location'],
            'families'           => $this->Bom_master_model->get_families(),
            'features'           => $features,
            'active_features'    => $active_features,
            'errors'             => array(),
            'notes'              => $this->document_notes($lines),
            'signoff_mode'       => $this->abom('abom_signoff_mode'),
            'signoff_names'      => $this->signoff_names($bom),
            'models'             => $this->abom('abom_models'),
            'sides'              => $this->abom('abom_sides'),
            'motion_types'       => $this->abom('abom_motion_types'),
            'axes_min'           => $this->abom('abom_axes_min'),
            'axes_max'           => $this->abom('abom_axes_max'),
            'tracks_min'         => $this->abom('abom_tracks_min'),
            'tracks_max'         => $this->abom('abom_tracks_max'),
            'speed_min'          => $this->abom('abom_speed_min'),
            'speed_max'          => $this->abom('abom_speed_max'),
        );

        $this->load->view('bom/view', $data);
    }

    /**
     * The "Points to verify during review" banner, derived from the
     * generated lines rather than hard-coded, so it stays true as
     * engineering resolves the flags through the UI.
     *
     * @param  array $lines
     * @return array
     */
    private function document_notes($lines)
    {
        $notes    = array();
        $pending  = array();
        $conflict = array();

        foreach ($lines as $line) {
            if (empty($line->erp_code)) {
                $pending[] = 'S.No ' . (int) $line->line_no . ' — ' . $line->description
                    . ' (' . $line->part_no . ')';
            }
            if ($line->issue_severity === 'conflict') {
                $conflict[$line->erp_code][] = 'S.No ' . (int) $line->line_no
                    . ' (' . $line->part_no . ')';
            }
        }

        if (!empty($pending)) {
            $notes[] = count($pending) . ' item' . (count($pending) === 1 ? '' : 's')
                . ' pending ERP code: ' . implode('; ', $pending) . '.';
        }

        foreach ($conflict as $erp => $where) {
            if (count($where) > 1) {
                $notes[] = 'ERP ' . $erp . ' appears on ' . count($where)
                    . ' different parts: ' . implode(' and ', $where) . ' — please verify.';
            }
        }

        return $notes;
    }

    /**
     * @param  object $bom
     * @return array
     */
    private function signoff_names($bom)
    {
        $ids = array();
        foreach (array('prepared_by', 'checked_by', 'eng_approved_by', 'proc_approved_by') as $key) {
            if (!empty($bom->$key)) {
                $ids[$key] = (int) $bom->$key;
            }
        }

        if (empty($ids)) {
            return array();
        }

        $rows = $this->db->select('user_id, first_name, last_name')
            ->from('system_users')
            ->where_in('user_id', array_values($ids))
            ->get()
            ->result();

        $by_id = array();
        foreach ($rows as $row) {
            $by_id[(int) $row->user_id] = trim($row->first_name . ' ' . $row->last_name);
        }

        $out = array();
        foreach ($ids as $key => $id) {
            if (isset($by_id[$id])) {
                $out[$key] = $by_id[$id];
            }
        }

        return $out;
    }

    /**
     * Builds a transient header object from a configuration array, so
     * the shared partials can render an unsaved BOM.
     *
     * @param  array $cfg
     * @param  array $result
     * @return object
     */
    private function bom_header_from_config(array $cfg, array $result)
    {
        $bom = new stdClass();

        $bom->id                = 0;
        $bom->bom_no            = '—';
        $bom->revision          = '';
        $bom->df_ref            = '';
        $bom->machine_model     = $cfg['machine_model'];
        $bom->machine_side      = $cfg['machine_side'];
        $bom->axes              = (int) $cfg['axes'];
        $bom->tracks            = (int) $cfg['tracks'];
        $bom->speed_ppm         = (int) $cfg['speed_ppm'];
        $bom->motion_type       = $cfg['motion_type'];
        $bom->plc_family_id     = (int) $result['family_id'];
        $bom->plc_family_locked = !empty($result['overridden']) ? 1 : 0;
        $bom->j4_units          = (int) $cfg['j4_units'];
        $bom->battery_qty       = (int) $cfg['battery_qty'];
        $bom->features_json     = json_encode($cfg['features']);
        $bom->status            = '';
        $bom->total_lines       = count($result['lines']);
        $bom->total_qty         = (int) $result['stats']['total_qty'];
        $bom->open_issues       = (int) $result['stats']['open_issues'];
        $bom->created_at        = date('Y-m-d H:i:s');

        foreach (array('prepared_by', 'prepared_at', 'checked_by', 'checked_at',
                       'eng_approved_by', 'eng_approved_at',
                       'proc_approved_by', 'proc_approved_at') as $key) {
            $bom->$key = null;
        }

        return $bom;
    }

    /**
     * The two spec Appendix B configurations.
     *
     * @return array
     */
    private function reference_presets()
    {
        $features = $this->Bom_master_model->default_features();

        return array(
            'fx5' => array(
                'bom_no'   => 'ABOM-REF-FX5',
                'df_ref'   => 'DF-1827',
                'revision' => '',
                'cfg'      => array(
                    'axes' => 8, 'tracks' => 12, 'speed_ppm' => 140,
                    'motion_type' => 'Intermittent', 'machine_model' => 'SPM1200L',
                    'machine_side' => 'LHS', 'j4_units' => 7, 'battery_qty' => 7,
                    'plc_family_id' => null, 'features' => $features,
                ),
            ),
            'iqr' => array(
                'bom_no'   => 'ABOM-REF-IQR',
                'df_ref'   => 'DF-1826',
                'revision' => '02',
                'cfg'      => array(
                    'axes' => 15, 'tracks' => 12, 'speed_ppm' => 180,
                    'motion_type' => 'Continuous', 'machine_model' => 'SPM1200L',
                    'machine_side' => 'N/A', 'j4_units' => 11, 'battery_qty' => 12,
                    'plc_family_id' => null, 'features' => $features,
                ),
            ),
        );
    }

    // -----------------------------------------------------------------
    // GUARDS AND HELPERS
    // -----------------------------------------------------------------

    /**
     * Reads a value from the abom config, which is loaded into its own
     * section so it cannot collide with an application config key.
     *
     * @param  string $key
     * @return mixed
     */
    private function abom($key)
    {
        return $this->config->item($key, 'abom');
    }

    /**
     * tables_ready() guard, matching Df_dispatch_plan_model. Fails
     * gracefully with a clear operator-facing message rather than a
     * database error, so a deploy that has not yet run the DDL does not
     * produce a white screen.
     *
     * @return bool
     */
    private function require_tables()
    {
        // Bom_master_model reads $this->config->item('abom_tables'), so
        // the list has to be visible outside the 'abom' section too.
        $this->config->set_item('abom_tables', $this->abom('abom_tables'));

        if ($this->Bom_master_model->tables_ready()) {
            return true;
        }

        $missing = $this->Bom_master_model->first_missing_table();

        if ($this->input->is_ajax_request()) {
            $this->output->set_status_header(503);
            $this->output->set_content_type('application/json');
            echo json_encode(array(
                'status'  => 0,
                'message' => 'Automation BOM tables are not installed. Run Database/abom_001.sql '
                           . 'and Database/abom_002_seed.sql first. Missing: ' . $missing,
            ));
            return false;
        }

        $this->load->view('bom/not_installed', array('missing' => $missing));

        return false;
    }

    /**
     * Permission check against the project's module_capablity ACL.
     * moduleid 4 (BOM CORRECTION TOOL); submodules 74 / 75 / 76.
     *
     * Mirrors Master_profile_guard's own behaviour: if the module has
     * not been provisioned yet — that is, the submodule row does not
     * exist because Database/abom_003_permissions.sql has not been run —
     * access is allowed, so installing the module does not lock every
     * role out before the permission rows land.
     *
     * @param  string $perm_key  generate|save|check|eng_approve|proc_approve|master_edit
     * @return bool
     */
    private function has_perm($perm_key)
    {
        $perms = $this->abom('abom_perms');

        if (!isset($perms[$perm_key])) {
            return false;
        }

        $moduleid    = (int) $perms[$perm_key]['moduleid'];
        $submoduleid = (int) $perms[$perm_key]['submoduleid'];

        if (!$this->db->table_exists('submodule') || !$this->db->table_exists('module_capablity')) {
            return true;
        }

        $provisioned = $this->db->from('submodule')
            ->where('id', $submoduleid)
            ->count_all_results();

        if ($provisioned === 0) {
            return true;                      // not provisioned yet
        }

        $session = $this->session->userdata('logged_in');
        $role_id = !empty($session['role']) ? (int) $session['role'] : 0;

        if ($role_id <= 0) {
            return false;
        }

        return $this->db->from('module_capablity')
            ->where('role_id', $role_id)
            ->where('moduleid', $moduleid)
            ->where('submoduleid', $submoduleid)
            ->where('submodule_access', 1)
            ->count_all_results() > 0;
    }

    /**
     * AJAX-only guard, as Df_dispatch_plan.php does it.
     */
    private function json_only()
    {
        if (!$this->input->is_ajax_request()) {
            show_error('No direct script access allowed');
        }
        $this->output->set_content_type('application/json');
    }

    /**
     * @param bool   $ok
     * @param string $message
     * @param array  $payload
     */
    private function respond($ok, $message = '', $payload = array())
    {
        $out = array_merge(
            array('status' => $ok ? 1 : 0, 'message' => $message),
            $payload
        );

        echo json_encode($out);
        exit;
    }
}
