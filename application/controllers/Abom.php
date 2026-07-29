<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom — Automation BOM Generator
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
 * URLs are /abom/... — see application/config/routes.php.
 *
 * The class is deliberately NOT named Bom. Leads.php links to a `BOM`
 * controller (BOM/pis, BOM/bom, BOM/edit_bom_testing, ...) and loads
 * BOM/* views. Neither exists in this working copy, but this copy is
 * partial — system/ is absent too — so they may well exist in
 * production. On a case-insensitive filesystem Bom.php and BOM.php are
 * the SAME FILE. Naming this class Abom removes both the collision risk
 * and the second live /bom/... URL namespace that CI's default routing
 * would otherwise expose.
 *
 * PHP 7.4 compatible (production runs ea-php74).
 */
class Abom extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in') == false) {
            redirect(page_url);
        }

        $this->config->load('abom', true);

        $this->load->model('Abom_master_model');
        $this->load->model('Abom_item_model');
        $this->load->helper('abom_helper');
    }

    // -----------------------------------------------------------------
    // SCREENS
    // -----------------------------------------------------------------

    /**
     * Line-quantity edits are only offered where they can be PERSISTED.
     *
     * On the generator screen a quantity is form state — nothing is
     * saved yet, and the override travels with the save request.
     *
     * On the view screen a quantity edit must reach abom_bom_line with
     * is_overridden set, plus a row in abom_audit_log. That write path
     * lands with build order step 5. Until it does, this flag keeps the
     * released document read-only: an edit that vanishes on refresh is
     * worse than no edit, because someone would mark up the sheet, print
     * it, and not notice the print came from unedited server state.
     *
     * Flipped to TRUE in the same commit that added save_line_qty(),
     * the abom_bom_line.is_overridden write and the abom_audit_log row.
     */
    const QTY_OVERRIDE_PERSISTENCE_AVAILABLE = true;

    public function index()
    {
        redirect(page_url . 'abom/generate');
    }

    /**
     * The generator screen. Editable configuration panel; the table
     * below is re-rendered by generate_ajax() without a page reload.
     */
    public function generate()
    {
        if (!$this->require_tables()) {
            return;
        }

        $cfg = $this->config_from_input($this->default_config());
        $this->load->library('Abom_engine', null, 'abom_engine');

        $errors = $this->validate_config($cfg);
        if (!empty($errors)) {
            $cfg = $this->default_config();
            $errors = array();
        }

        $result = $this->abom_engine->generate($cfg);

        $bom = $this->bom_header_from_config($cfg, $result);
        $this->load->model('Abom_model');
        $bom->bom_no = $this->Abom_model->next_bom_no();

        $this->render_document($bom, $result['lines'], true, $result, $errors);
    }

    /**
     * POST, AJAX. Returns JSON. Called on every sidebar change, so it
     * MUST be fast and MUST NOT write to the database (spec 6.2).
     */
    public function generate_ajax()
    {
        $this->json_only();

        if (!$this->require_tables()) {
            return;
        }

        $cfg    = $this->config_from_input($this->default_config());
        $errors = $this->validate_config($cfg);

        // Out-of-range values are rejected with a field-level message.
        // They are never silently clamped.
        if (!empty($errors)) {
            $this->output->set_status_header(422);
            $this->respond(false, 'Please correct the highlighted fields.', array(
                'errors' => $errors,
            ));
        }

        $this->load->library('Abom_engine', null, 'abom_engine');
        $result = $this->abom_engine->generate($cfg);

        $bom = $this->bom_header_from_config($cfg, $result);
        $this->load->model('Abom_model');
        $bom->bom_no = $this->Abom_model->next_bom_no();

        $family_code = $this->Abom_master_model->family_code((int) $result['family_id']);
        $defaults    = $this->Abom_master_model->family_defaults((int) $result['family_id']);

        // The rendered partial is returned rather than raw rows, so the
        // row-class precedence stays in one place instead of being
        // duplicated in JavaScript (spec 6.2).
        $html = $this->load->view('abom/_table', array(
            'lines'        => $result['lines'],
            'qty_editable' => true,
        ), true);

        $this->respond(true, '', array(
            'family' => array(
                'code'        => $family_code,
                'explanation' => $result['family']['explanation'],
                'detected'    => $result['family']['code'],
                'overridden'  => (bool) $result['overridden'],
                'badge_class' => abom_plc_badge_class($family_code),
                'chip_class'  => abom_chip_class($family_code),
            ),
            'defaults' => $defaults,
            'stats'    => $result['stats'],
            'sections' => $result['sections'],
            'chips'    => $this->header_chips($bom, $family_code, $defaults['panel_location']),
            'bom_no'   => $bom->bom_no,
            'html'     => $html,
        ));
    }

    /**
     * Persists the current configuration as a new BOM.
     *
     * Quantities are RE-GENERATED from the engine rather than trusted
     * from the request — the client may only override, and an override
     * is recorded as such against the engine's computed value.
     */
    public function save()
    {
        $this->json_only();

        if (!$this->require_tables()) {
            return;
        }

        $cfg    = $this->config_from_input($this->default_config());
        $errors = $this->validate_config($cfg);

        if (!empty($errors)) {
            $this->output->set_status_header(422);
            $this->respond(false, 'Please correct the highlighted fields.', array('errors' => $errors));
        }

        $this->load->library('Abom_engine', null, 'abom_engine');
        $this->load->model('Abom_model');

        $result = $this->abom_engine->generate($cfg);
        $lines  = $result['lines'];

        // Client-supplied overrides, keyed by line_no. Anything equal to
        // the computed value is not an override.
        $overrides = $this->input->post('qty');
        if (is_array($overrides)) {
            foreach ($lines as $line) {
                $key = (string) $line->line_no;
                if (!isset($overrides[$key]) || $overrides[$key] === '') {
                    continue;
                }
                $qty = max(0, (int) $overrides[$key]);
                if ($qty !== (int) $line->computed_qty) {
                    $line->qty             = $qty;
                    $line->is_overridden   = 1;
                    $line->override_reason = null;
                }
            }
        }

        $user_id  = $this->current_user_id();
        $now      = date('Y-m-d H:i:s');
        $revision = trim((string) $this->input->post('revision'));

        $header = array(
            'bom_no'            => $this->Abom_model->next_bom_no(),
            'revision'          => $revision !== '' ? $revision : '00',
            'df_ref'            => isset($cfg['df_ref']) && $cfg['df_ref'] !== '' ? $cfg['df_ref'] : null,
            'machine_model'     => $cfg['machine_model'],
            'machine_side'      => $cfg['machine_side'],
            'axes'              => (int) $cfg['axes'],
            'tracks'            => (int) $cfg['tracks'],
            'speed_ppm'         => (int) $cfg['speed_ppm'],
            'motion_type'       => $cfg['motion_type'],
            'plc_family_id'     => (int) $result['family_id'],
            'plc_family_locked' => !empty($result['overridden']) ? 1 : 0,
            'j4_units'          => (int) $cfg['j4_units'],
            'battery_qty'       => (int) $cfg['battery_qty'],
            'features_json'     => json_encode($cfg['features']),
            'status'            => 'draft',
            'prepared_by'       => $user_id > 0 ? $user_id : null,
            'prepared_at'       => $now,
            'notes'             => trim((string) $this->input->post('notes')),
        );

        $bom_id = $this->Abom_model->save_bom($header, $lines, $user_id);

        if ($bom_id <= 0) {
            $this->output->set_status_header(500);
            $this->respond(false, 'The BOM could not be saved. Nothing was written.');
        }

        $this->respond(true, 'BOM ' . $header['bom_no'] . ' saved.', array(
            'bom_id'   => $bom_id,
            'bom_no'   => $header['bom_no'],
            'redirect' => page_url . 'abom/view/' . $bom_id,
        ));
    }

    /**
     * Persists ONE line-quantity override, with an audit row.
     *
     * This is the write path that makes the quantity column editable on
     * a saved BOM. Without it a markup would vanish on refresh and the
     * print would come from unedited server state.
     */
    public function save_line_qty()
    {
        $this->json_only();

        if (!$this->require_tables()) {
            return;
        }

        $this->load->model('Abom_model');

        $bom_id  = (int) $this->input->post('bom_id');
        $line_id = (int) $this->input->post('line_id');
        $qty     = (int) $this->input->post('qty');
        $reason  = trim((string) $this->input->post('reason'));

        $bom = $this->Abom_model->get_bom($bom_id);

        if (!$bom) {
            $this->output->set_status_header(404);
            $this->respond(false, 'BOM not found.');
        }

        // Server-side guard. The UI hides the input, but the rule is
        // enforced here — an approved BOM's quantities are fixed.
        if (!abom_qty_editable($bom)) {
            $this->output->set_status_header(403);
            $this->respond(false, 'Quantities are locked: this BOM is ' . abom_status_label($bom->status) . '.');
        }

        if ($qty < 0 || $qty > 65535) {
            $this->output->set_status_header(422);
            $this->respond(false, 'Quantity must be between 0 and 65535.');
        }

        $line = $this->Abom_model->update_line_qty(
            $bom_id, $line_id, $qty, $reason, $this->current_user_id()
        );

        if (!$line) {
            $this->output->set_status_header(404);
            $this->respond(false, 'Line not found on this BOM.');
        }

        $this->respond(true, 'Quantity saved.', array(
            'line_id'       => (int) $line->id,
            'qty'           => (int) $line->qty,
            'computed_qty'  => (int) $line->computed_qty,
            'is_overridden' => (int) $line->is_overridden,
            'status_html'   => abom_status_badges($line),
            'totals'        => $this->Abom_model->get_bom($bom_id),
        ));
    }

    /**
     * Saved BOMs.
     */
    public function bom_list()
    {
        if (!$this->require_tables()) {
            return;
        }

        $this->load->model('Abom_model');

        $filters = array(
            'status'        => trim((string) $this->input->get('status')),
            'machine_model' => trim((string) $this->input->get('model')),
            'search'        => trim((string) $this->input->get('search')),
        );

        $this->load->view('abom/list', array(
            'boms'          => $this->Abom_model->get_all($filters),
            'filters'       => $filters,
            'models'        => $this->abom('abom_models'),
            'statuses'      => array('draft', 'submitted', 'checked', 'eng_approved',
                                     'approved', 'rejected', 'superseded'),
        ));
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

        $this->load->model('Abom_model');
        $bom = $this->Abom_model->get_bom((int) $bom_id);

        if (!$bom) {
            show_404();
            return;
        }

        $lines = $this->Abom_model->get_lines((int) $bom_id);

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

        $this->load->library('Abom_engine', null, 'abom_engine');
        $result = $this->abom_engine->generate($preset['cfg']);

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
     * @param bool   $editable  configuration panel editable?
     * @param array  $result    optional engine result (reference/generate)
     * @param array  $errors    field => message
     */
    private function render_document($bom, $lines, $editable, $result = null, $errors = array())
    {
        $this->load->library('Abom_engine', null, 'abom_engine');

        $family_code = $this->Abom_master_model->family_code((int) $bom->plc_family_id);
        $defaults    = $this->Abom_master_model->family_defaults((int) $bom->plc_family_id);

        $stats = ($result !== null)
            ? $result['stats']
            : $this->abom_engine->stats($lines);

        $sections = ($result !== null)
            ? $result['sections']
            : $this->abom_engine->sections_present($lines);

        $features = $this->Abom_master_model->get_features();

        $active_features = array();
        if (!empty($bom->features_json)) {
            $decoded = json_decode($bom->features_json, true);
            if (is_array($decoded)) {
                $active_features = $decoded;
            }
        }
        if (empty($active_features)) {
            $active_features = $this->Abom_master_model->default_features();
        }

        // Quantity editability is decided by workflow state, and only
        // offered where the edit can actually be persisted.
        //
        //   generator      -> editable; the value is form state and is
        //                     posted with the save request
        //   saved BOM      -> editable only while the workflow allows it
        //                     AND the write path exists
        //   unsaved preview-> never; there is no row to write to, so an
        //                     input here would be a scribble that
        //                     vanishes on refresh
        $qty_editable = $editable
            || (!empty($bom->id)
                && abom_qty_editable($bom)
                && self::QTY_OVERRIDE_PERSISTENCE_AVAILABLE);

        $data = array(
            'bom'                => $bom,
            'lines'              => $lines,
            'sections'           => $sections,
            'stats'              => $stats,
            'editable'           => (bool) $editable,
            'qty_editable'       => (bool) $qty_editable,
            'qty_locked_reason'  => $this->qty_locked_reason($bom, $editable),
            'presets'            => $this->reference_presets(),
            'chips'              => $this->header_chips($bom, $family_code, $defaults['panel_location']),
            'family_code'        => $family_code,
            'family_explanation' => ($result !== null && isset($result['family']['explanation']))
                                        ? $result['family']['explanation'] : '',
            'overridden'         => ($result !== null) ? !empty($result['overridden'])
                                        : !empty($bom->plc_family_locked),
            'panel_location'     => $defaults['panel_location'],
            'families'           => $this->Abom_master_model->get_families(),
            'features'           => $features,
            'active_features'    => $active_features,
            'errors'             => is_array($errors) ? $errors : array(),
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

        $this->load->view($editable ? 'abom/generate' : 'abom/view', $data);
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
     * Starting configuration for a fresh generator screen.
     *
     * @return array
     */
    private function default_config()
    {
        $presets = $this->reference_presets();

        return $presets['iqr']['cfg'];
    }

    /**
     * Reads a configuration off the request, falling back to $base for
     * anything absent. Nothing here computes a quantity or picks a
     * family — that is Abom_engine's job alone.
     *
     * @param  array $base
     * @return array
     */
    private function config_from_input(array $base)
    {
        $cfg = $base;

        $ints = array('axes', 'tracks', 'speed_ppm', 'j4_units', 'battery_qty');
        foreach ($ints as $key) {
            $raw = $this->input->post($key);
            if ($raw === null) {
                $raw = $this->input->get($key);
            }
            if ($raw !== null && $raw !== '') {
                $cfg[$key] = (int) $raw;
            }
        }

        $strings = array('motion_type', 'machine_model', 'machine_side', 'df_ref');
        foreach ($strings as $key) {
            $raw = $this->input->post($key);
            if ($raw === null) {
                $raw = $this->input->get($key);
            }
            if ($raw !== null && $raw !== '') {
                $cfg[$key] = trim((string) $raw);
            }
        }

        $family = $this->input->post('plc_family_id');
        $cfg['plc_family_id'] = ($family !== null && $family !== '') ? (int) $family : null;

        // Absent features array means "none ticked", not "use defaults" —
        // an unchecked box posts nothing.
        $posted = $this->input->post('features');
        if (is_array($posted)) {
            $features = array();
            foreach ($this->Abom_master_model->get_features() as $code => $feature) {
                $features[$code] = !empty($posted[$code]) ? 1 : 0;
            }
            $cfg['features'] = $features;
        }

        return $cfg;
    }

    /**
     * Guard rails from application/config/abom.php. Out-of-range values
     * are REJECTED with a field-level message, never silently clamped
     * (spec 6.2).
     *
     * @param  array $cfg
     * @return array  field => message
     */
    private function validate_config(array $cfg)
    {
        $errors = array();

        $ranges = array(
            'axes'      => array($this->abom('abom_axes_min'),   $this->abom('abom_axes_max'),   'Axes'),
            'tracks'    => array($this->abom('abom_tracks_min'), $this->abom('abom_tracks_max'), 'Tracks'),
            'speed_ppm' => array($this->abom('abom_speed_min'),  $this->abom('abom_speed_max'),  'Speed (PPM)'),
        );

        foreach ($ranges as $field => $spec) {
            list($min, $max, $label) = $spec;
            $value = isset($cfg[$field]) ? (int) $cfg[$field] : 0;

            if ($value < (int) $min || $value > (int) $max) {
                $errors[$field] = $label . ' must be between ' . (int) $min . ' and ' . (int) $max . '.';
            }
        }

        foreach (array('j4_units' => 'MR-J4 units', 'battery_qty' => 'Battery quantity') as $field => $label) {
            $value = isset($cfg[$field]) ? (int) $cfg[$field] : 0;
            if ($value < 0 || $value > 99) {
                $errors[$field] = $label . ' must be between 0 and 99.';
            }
        }

        if (!in_array($cfg['motion_type'], $this->abom('abom_motion_types'), true)) {
            $errors['motion_type'] = 'Unknown motion type.';
        }

        if (!in_array($cfg['machine_model'], $this->abom('abom_models'), true)) {
            $errors['machine_model'] = 'Unknown machine model.';
        }

        if (!in_array($cfg['machine_side'], $this->abom('abom_sides'), true)) {
            $errors['machine_side'] = 'Unknown machine side.';
        }

        return $errors;
    }

    /**
     * The topbar chip row. Carries the configuration, the PLC family,
     * the panel location, every ENABLED feature gate, and the workflow
     * status — so the header states what the BOM was generated with.
     *
     * @param  object $bom
     * @param  string $family_code
     * @param  string $panel_location
     * @return array
     */
    private function header_chips($bom, $family_code, $panel_location)
    {
        $chips = array();

        $chips[] = array('class' => '', 'label' => $bom->bom_no
            . ($bom->revision !== '' ? ' · REV.' . $bom->revision : ''));
        $chips[] = array('class' => 'lite', 'label' => $bom->machine_model);
        $chips[] = array('class' => 'lite', 'label' => (int) $bom->axes . ' Axis');
        $chips[] = array('class' => 'lite', 'label' => (int) $bom->tracks . ' Track');
        $chips[] = array('class' => 'lite', 'label' => (int) $bom->speed_ppm . ' PPM');

        if (!empty($bom->machine_side) && $bom->machine_side !== 'N/A') {
            $chips[] = array('class' => 'lite', 'label' => $bom->machine_side);
        }

        $chips[] = array('class' => 'cont', 'label' => $bom->motion_type);

        // One chip per enabled feature gate — this is the information
        // the design document's "High Speed" chip was carrying.
        $active = array();
        if (!empty($bom->features_json)) {
            $decoded = json_decode($bom->features_json, true);
            if (is_array($decoded)) {
                $active = $decoded;
            }
        }
        foreach ($this->Abom_master_model->get_features() as $code => $feature) {
            if (!empty($active[$code])) {
                $chips[] = array('class' => 'cont', 'label' => $feature->label);
            }
        }

        $chips[] = array(
            'class' => abom_chip_class($family_code),
            'label' => ($family_code === 'FX5') ? 'FX5 Series' : 'iQ-R Series',
        );

        if (!empty($bom->plc_family_locked)) {
            $chips[] = array('class' => 'alert', 'label' => 'Family overridden');
        }

        if ($panel_location !== '') {
            $chips[] = array('class' => 'lite', 'label' => $panel_location);
        }

        if (!empty($bom->status)) {
            $chips[] = array('class' => 'lite', 'label' => abom_status_label($bom->status));
        }

        return $chips;
    }

    /**
     * Why the quantity column is read-only, for the operator.
     *
     * @param  object $bom
     * @param  bool   $editable
     * @return string  '' when quantities are editable
     */
    private function qty_locked_reason($bom, $editable)
    {
        if ($editable) {
            return '';
        }

        if (empty($bom->id)) {
            return 'This is an unsaved preview generated from live master data. '
                 . 'Save it before marking up quantities.';
        }

        if (!abom_qty_editable($bom)) {
            return 'Quantities are locked because this BOM is ' . abom_status_label($bom->status) . '.';
        }

        if (!self::QTY_OVERRIDE_PERSISTENCE_AVAILABLE) {
            return 'Quantity editing is disabled until the save path is in place, '
                 . 'so that no markup can be lost on refresh.';
        }

        return '';
    }

    /**
     * The two spec Appendix B configurations. On the generator screen
     * these are offered as CONFIGURATION presets — they fill the panel
     * and the engine then generates from those inputs like any other
     * configuration. They are not data loaders.
     *
     * @return array
     */
    private function reference_presets()
    {
        $features = $this->Abom_master_model->default_features();

        return array(
            'fx5' => array(
                'bom_no'   => 'ABOM-REF-FX5',
                'df_ref'   => 'DF-1827',
                'revision' => '',
                'title'    => 'DF-1827',
                'summary'  => 'SPM1200L · 8 Axis · 12 Track · 140 PPM · LHS',
                'panel'    => 'Panel With Machine · expect 29 line items',
                'tags'     => array(array('fx5', 'FX5'), array('int', 'Intermittent')),
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
                'title'    => 'DF-1826 · REV.02',
                'summary'  => 'SPM1200L · 15 Axis · 12 Track · 180 PPM',
                'panel'    => 'Standalone Panel · expect 42 line items',
                'tags'     => array(array('iqr', 'iQ-R'), array('cont', 'Continuous')),
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
        // Abom_master_model reads $this->config->item('abom_tables'), so
        // the list has to be visible outside the 'abom' section too.
        $this->config->set_item('abom_tables', $this->abom('abom_tables'));

        if ($this->Abom_master_model->tables_ready()) {
            return true;
        }

        $missing = $this->Abom_master_model->first_missing_table();

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

        $this->load->view('abom/not_installed', array('missing' => $missing));

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
     * @return int  0 when not resolvable
     */
    private function current_user_id()
    {
        $session = $this->session->userdata('logged_in');

        return isset($session['user_id']) ? (int) $session['user_id'] : 0;
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
