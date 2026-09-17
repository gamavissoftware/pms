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

        if (!$this->require_perm('generate')) {
            return;
        }

        // ?build=<id> — arrived from the Reference BOMs register. The
        // build is named, and the machine is derived from it, rather
        // than the whole configuration travelling in the URL: an
        // operator who knows they want the FX5-1808 build should not
        // have to work out which axis and speed combination routes to
        // it, and a URL carrying eleven parameters is one typo away
        // from generating a different machine entirely.
        $build  = (int) $this->input->get('build');
        $notice = '';

        if ($build > 0) {
            $from = $this->Abom_master_model->config_for_variant(
                $build, $this->default_config()
            );

            if ($from === null) {
                show_404();
                return;
            }

            $cfg = $from['cfg'];

            // PIN THE BUILD THAT WAS ASKED FOR.
            //
            // "Generate BOM" on a reference build means "give me this
            // build". Without pinning, the recorded machine was fed back
            // through the selection rules and whatever they returned won
            // — so a build whose recorded machine no longer routes to it
            // produced a DIFFERENT sheet, with different and usually more
            // lines than the reference the operator was looking at.
            //
            // The engine already honours $cfg['variant_id'] as an
            // explicit choice; it just was not being told. The picker in
            // the panel shows the pinned build, so Recalculate keeps it
            // and "Use auto-detected" hands control back to the rules.
            //
            // A disagreement is still REPORTED (see below) — it is a
            // master-data fault worth fixing. It just no longer decides
            // which sheet you are shown.
            $cfg['variant_id'] = $build;

            $notice = $this->build_prefill_notice($from);
        } else {
            $cfg = $this->config_from_input($this->default_config());
        }

        $this->load->library('Abom_engine', null, 'abom_engine');

        $errors = $this->validate_config($cfg);
        if (!empty($errors)) {
            $cfg    = $this->default_config();
            $errors = array();
            $notice = '';
        }

        $result = $this->abom_engine->generate($cfg);

        // Did the recorded machine actually reach the build it was
        // recorded against? It must, or the register has offered a
        // button that lands somewhere else — which the operator would
        // otherwise only discover by reading the badge.
        // The sheet is now the build that was asked for, so the check is
        // no longer "which build did we land on" but "would the rules
        // have found this one on their own". They should, and when they
        // do not the register is offering a button whose machine routes
        // elsewhere — still worth saying, just no longer by substituting
        // a different sheet.
        if ($build > 0) {
            // Ask the rules WITHOUT the pin, and without any family
            // override. $result['family_id'] is no use here: the family
            // follows the pinned variant, so probing with it would just
            // confirm the pin and never report a real disagreement.
            $probe = $cfg;
            unset($probe['variant_id']);
            $probe['plc_family_id'] = null;

            $reached = 0;

            try {
                $fam      = $this->abom_engine->detect_plc_family($probe);
                $detected = $this->abom_engine->detect_variant($probe, (int) $fam['family_id']);
                $reached  = $detected ? (int) $detected['variant_id'] : 0;
            } catch (Exception $e) {
                // No family rule matched at all. That is itself the
                // disagreement, and $reached stays 0 — "reaches no build".
                $reached = 0;
            }

            if ($reached !== $build) {
                $notice = $this->build_prefill_mismatch(
                    $from['variant'], $result, $reached
                );
            }
        }

        $bom = $this->bom_header_from_config($cfg, $result);
        $this->load->model('Abom_model');
        $bom->bom_no = $this->Abom_model->next_bom_no();

        $this->render_document($bom, $result['lines'], true, $result, $errors, $notice);
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

        if (!$this->require_perm('generate')) {
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
        $defaults    = $this->Abom_master_model->variant_defaults(
            $result['variant_id'], (int) $result['family_id']
        );

        $variant = isset($result['variant']) ? $result['variant'] : null;

        // The rendered partial is returned rather than raw rows, so the
        // row-class precedence stays in one place instead of being
        // duplicated in JavaScript (spec 6.2).
        $html = $this->load->view('abom/_table', array(
            'lines'         => $result['lines'],
            'qty_editable'  => true,
            'rows_editable' => true,
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
            'variant' => array(
                'id'          => $result['variant_id'],
                'code'        => $variant ? $variant->code : '',
                'name'        => $variant ? $variant->name : '',
                'description' => $variant ? (string) $variant->description : '',
                'explanation' => !empty($result['variant_detected'])
                                    ? $result['variant_detected']['explanation'] : '',
                'detected'    => !empty($result['variant_detected'])
                                    ? $result['variant_detected']['code'] : '',
                'overridden'  => (bool) $result['variant_overridden'],
                'missing'     => (bool) $result['variant_missing'],
                'message'     => !empty($result['variant_missing'])
                                    ? $this->variant_missing_message($cfg) : '',
                'badge_class' => abom_plc_badge_class($family_code),
            ),
            'defaults' => $defaults,
            'stats'    => $result['stats'],
            'sections' => $result['sections'],
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

        if (!$this->require_perm('save')) {
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

        // No variant means no lines. Saving an empty BOM would create a
        // document that reads as "nothing is required for this machine",
        // which is the one wrong answer procurement must never be given.
        if (!empty($result['variant_missing']) || empty($lines)) {
            $this->output->set_status_header(422);
            $this->respond(false, $this->variant_missing_message($cfg));
        }

        $lines = $this->apply_row_edits($lines);

        if (empty($lines)) {
            $this->output->set_status_header(422);
            $this->respond(false, 'Every line has been removed from this BOM. '
                . 'A document with no items cannot be saved.');
        }

        $user_id  = $this->current_user_id();
        $now      = date('Y-m-d H:i:s');
        $revision = trim((string) $this->input->post('revision'));

        // The DF reference must not already be taken. Checked HERE, on
        // the write, not only in the browser: the live check as you type
        // is a courtesy, this is the rule.
        $clash = $this->Abom_model->df_ref_conflict(
            isset($cfg['df_ref']) ? $cfg['df_ref'] : '',
            $revision !== '' ? $revision : '00'
        );

        if ($clash) {
            $this->output->set_status_header(422);
            $this->respond(false, $this->df_ref_taken_message($clash), array(
                'errors' => array('df_ref' => 'Already used by ' . $clash->bom_no . '.'),
                'conflict' => $this->df_ref_conflict_payload($clash),
            ));
        }

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
            'variant_id'        => isset($result['variant_id']) ? $result['variant_id'] : null,
            'variant_locked'    => !empty($result['variant_overridden']) ? 1 : 0,
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

        if (!$this->require_perm('save')) {
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

    // -----------------------------------------------------------------
    // APPROVAL WORKFLOW  (build order step 6)
    // -----------------------------------------------------------------

    /**
     * Advance one stage. Every guard is enforced in
     * Abom_approval_model::blockers(), never only in the UI.
     */
    public function submit($bom_id = 0)
    {
        $this->workflow_action((int) $bom_id, 'advance');
    }

    /** Alias — the UI calls the same endpoint at every stage. */
    public function approve($bom_id = 0)
    {
        $this->workflow_action((int) $bom_id, 'advance');
    }

    public function reject($bom_id = 0)
    {
        $this->workflow_action((int) $bom_id, 'reject');
    }

    public function reopen($bom_id = 0)
    {
        $this->workflow_action((int) $bom_id, 'reopen');
    }

    /**
     * Editing an approved BOM is forbidden; this is the offered
     * alternative (spec 6.3).
     */
    public function create_revision($bom_id = 0)
    {
        $this->workflow_action((int) $bom_id, 'revision');
    }

    // -----------------------------------------------------------------
    // MASTER DATA  (build order step 8)
    //
    // EVERY entry point below calls require_perm('master_edit'), and
    // that is not optional. MODULE_CHANGELOG §4.1 is explicit: the
    // AUTOMATION BOM MASTER ITEMS permission has existed and been
    // grantable since the module shipped, gating nothing, because no
    // screen consumed it. Shipping a master-item screen without the
    // check would repeat §0.13 exactly — a declared permission that
    // protects nothing, on the seed every BOM is generated from.
    //
    // Editing here changes what FUTURE BOMs contain. It cannot change an
    // existing one: abom_bom_line is a frozen snapshot and nothing on
    // these screens writes to it.
    // -----------------------------------------------------------------

    /**
     * Master-item register, with the same filters the engine cares
     * about: build, section, formula, severity, active state, free text.
     */
    public function master()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $filters = array(
            'variant_id'   => (int) $this->input->get('variant'),
            'section_id'   => (int) $this->input->get('section'),
            'formula_code' => trim((string) $this->input->get('formula')),
            'severity'     => trim((string) $this->input->get('severity')),
            'search'       => trim((string) $this->input->get('search')),
            'is_active'    => $this->input->get('active') === null
                                ? '' : trim((string) $this->input->get('active')),
        );

        $this->load->view('abom/master_list', array(
            'items'    => $this->Abom_item_model->get_all($filters),
            'filters'  => $filters,
            'variants' => $this->Abom_master_model->get_variants(),
            'formulas' => $this->Abom_master_model->get_formulas(),
            'counts'   => $this->Abom_item_model->count_by_variant(),
        ));
    }

    /**
     * Add or edit one master item. $item_id 0 = add.
     */
    public function master_form($item_id = 0)
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $item_id = (int) $item_id;
        $item    = $item_id > 0 ? $this->Abom_item_model->get_item($item_id) : null;

        if ($item_id > 0 && !$item) {
            show_404();
            return;
        }

        $this->render_master_form($item, array(), $item_id);
    }

    /**
     * POST target for both add and edit.
     *
     * Re-renders the form with field-level messages on failure rather
     * than redirecting — the operator keeps everything they typed.
     */
    public function master_save()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $item_id = (int) $this->input->post('item_id');
        $data    = $this->Abom_item_model->payload($this->input->post());
        $errors  = $this->Abom_item_model->validate($data);

        if (!empty($errors)) {
            $existing = $item_id > 0 ? $this->Abom_item_model->get_item($item_id) : null;

            // Re-render from the POSTED values, not the stored ones, so
            // corrections are made to what was typed.
            $shown = (object) array_merge(
                (array) ($existing ? $existing : new stdClass()),
                $data,
                array('id' => $item_id)
            );

            $this->render_master_form($shown, $errors, $item_id);
            return;
        }

        $user_id = $this->current_user_id();

        if ($item_id > 0) {
            $this->Abom_item_model->update($item_id, $data, $user_id);
            $message = 'Item ' . $item_id . ' updated.';
        } else {
            $item_id = $this->Abom_item_model->create($data, $user_id);

            if ($item_id <= 0) {
                $this->render_master_form((object) $data,
                    array('description' => 'The item could not be saved. Nothing was written.'), 0);
                return;
            }

            $message = 'Item ' . $item_id . ' created.';
        }

        $this->session->set_flashdata('abom_master_message', $message);
        redirect(page_url . 'abom/master_form/' . $item_id);
    }

    /**
     * Activates or deactivates one item. POST, AJAX.
     *
     * Never a delete — abom_bom_line.item_id points at these rows for
     * provenance. See Abom_item_model::set_active().
     */
    public function master_toggle()
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $item_id = (int) $this->input->post('item_id');
        $active  = (int) $this->input->post('active');

        $item = $this->Abom_item_model->get_item($item_id);

        if (!$item) {
            $this->output->set_status_header(404);
            $this->respond(false, 'Item not found.');
        }

        if (!$this->Abom_item_model->set_active($item_id, $active, $this->current_user_id())) {
            $this->output->set_status_header(500);
            $this->respond(false, 'The item could not be changed. Nothing was written.');
        }

        $used = $this->Abom_item_model->usage_count($item_id);

        $this->respond(true,
            $item->description . ($active ? ' reactivated.' : ' deactivated.')
            . ($used > 0
                ? ' ' . $used . ' existing BOM line' . ($used === 1 ? '' : 's')
                  . ' generated from it are unchanged.'
                : ''),
            array('item_id' => $item_id, 'active' => $active ? 1 : 0));
    }

    /**
     * Shared render for add and edit.
     *
     * @param object|null $item
     * @param array       $errors
     * @param int         $item_id
     */
    private function render_master_form($item, array $errors, $item_id)
    {
        $variants = $this->Abom_master_model->get_variants();

        // Sections are per PLC family, and the family is derived from
        // the build. The form needs every family's sections so the
        // dropdown can filter as the build changes, without a round trip.
        $sections = array();
        foreach ($this->Abom_master_model->get_families() as $family) {
            foreach ($this->Abom_master_model->get_sections((int) $family->id) as $section) {
                $sections[] = $section;
            }
        }

        $this->load->view('abom/master_form', array(
            'item'     => $item,
            'item_id'  => (int) $item_id,
            'errors'   => $errors,
            'variants' => $variants,
            'sections' => $sections,
            'formulas' => $this->Abom_master_model->get_formulas(),
            'features' => $this->Abom_master_model->get_features(),
            'families' => $this->Abom_master_model->get_families(),
            'uses'     => $item_id > 0 ? $this->Abom_item_model->usage_count($item_id) : 0,
            'message'  => $this->session->flashdata('abom_master_message'),
        ));
    }

    // -----------------------------------------------------------------
    // BUILD CONFIGURATION  (variants, rules, sections, features, formulas)
    //
    // The tables that decide WHICH BUILD a machine gets, before
    // abom_item decides what that build contains. Same permission as the
    // master items — this is the same class of data and more
    // consequential, not less: a wrong selection rule does not produce a
    // visibly broken BOM, it produces a plausible one for the wrong
    // machine.
    // -----------------------------------------------------------------

    /**
     * REFERENCE BOM REGISTER — the uploaded BOMs, as BOMs.
     *
     * /abom/master lists all 249 master items flat. That is the right
     * shape for "find every line using ERP 4060431" and the wrong one for
     * "work through DF-1808". This is the same data grouped the way it
     * arrived: one sheet per build, in section order, editable in place.
     */
    public function master_bom($variant_id = 0)
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->load->model('Abom_config_model');

        $variant_id = (int) $variant_id;

        // No id = the register.
        if ($variant_id <= 0) {
            $all      = $this->Abom_master_model->get_variants();
            $families = $this->Abom_master_model->get_families();
            $summary  = $this->Abom_item_model->build_summary();

            $filters = array(
                'search' => trim((string) $this->input->get('search')),
                'family' => (int) $this->input->get('family'),
                'model'  => trim((string) $this->input->get('model')),
                'panel'  => trim((string) $this->input->get('panel')),
                'flag'   => trim((string) $this->input->get('flag')),
                'active' => $this->input->get('active') === null
                                ? '' : trim((string) $this->input->get('active')),
            );

            $this->load->view('abom/master_bom_list', array(
                'variants' => $this->filter_variants($all, $summary, $filters),
                'total'    => count($all),
                // variant_id => generated-BOM count. A build with a count
                // is not deletable; see Abom::master_bom_delete().
                'bom_usage' => $this->Abom_master_model->variant_bom_usage_map(),
                'filters'  => $filters,
                'families' => $families,
                'summary'  => $summary,
                // Offered options come from the builds THAT EXIST, not
                // from a fixed list, so a model or a panel location
                // introduced by a future build is filterable the day it
                // lands with nothing to maintain.
                'models'   => $this->distinct_variant_field($all, 'machine_model'),
                'panels'   => $this->distinct_variant_field($all, 'default_panel_location'),
                'message'  => $this->session->flashdata('abom_master_message'),
            ));
            return;
        }

        $variant = $this->Abom_master_model->get_variant($variant_id);

        if (!$variant) { show_404(); return; }

        $this->render_master_bom($variant, null, array(), $this->session->flashdata('abom_master_message'));
    }

    /**
     * POST target for the build sheet. Bulk save, all-or-nothing.
     *
     * Re-renders with per-row messages on failure rather than
     * redirecting, so a rejected sheet keeps everything that was typed.
     */
    public function master_bom_save()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $variant_id = (int) $this->input->post('variant_id');
        $variant    = $this->Abom_master_model->get_variant($variant_id);

        if (!$variant) { show_404(); return; }

        $raw  = $this->input->post('rows');
        $rows = !empty($raw) ? json_decode($raw, true) : null;

        if (!is_array($rows)) {
            $this->render_master_bom($variant, null,
                array('_form' => 'No rows were received, so nothing was changed.'), '');
            return;
        }

        $result = $this->Abom_item_model->save_build($variant_id, $rows, $this->current_user_id());

        if (empty($result['ok'])) {
            // Hand the posted rows back so corrections are made to what
            // was typed, not to what is stored.
            $this->render_master_bom($variant, $rows,
                array_merge($result['errors'], array('_form' => $result['error'])), '');
            return;
        }

        $parts = array();
        if ($result['updated']) { $parts[] = $result['updated'] . ' updated'; }
        if ($result['created']) { $parts[] = $result['created'] . ' added'; }
        if ($result['retired']) { $parts[] = $result['retired'] . ' retired'; }

        $this->session->set_flashdata('abom_master_message',
            $variant->code . ' saved' . (empty($parts) ? ' — no changes.' : ' — ' . implode(', ', $parts) . '.'));

        redirect(page_url . 'abom/master_bom/' . $variant_id);
    }

    /**
     * Delete a reference build, and only ever an UNUSED one.
     *
     * Exists for the duplicate left behind when the same DF is imported
     * twice -- which is easy to do, because an import that half-works
     * looks like one that failed.
     *
     * THE RULE: if any BOM was generated from this build, it is not
     * deletable, at all, by anyone. A generated BOM records the build it
     * came from; deleting the build would leave a document unable to
     * explain where its lines came from. Deactivate it instead -- that
     * takes it out of selection for new BOMs and leaves the existing ones
     * intact, which is what "retire this build" actually means.
     *
     * Soft-deleted BOMs count as usage. They still exist, are still
     * restorable and still have to render.
     *
     * abom_bom.variant_id has an index but NO foreign key, so the
     * database will not refuse this on its own -- the check below and the
     * one inside delete_variant() are the whole of the protection.
     */
    public function master_bom_delete()
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $variant_id = (int) $this->input->post('variant_id');
        $variant    = $this->Abom_master_model->get_variant($variant_id);

        if (!$variant) {
            $this->output->set_status_header(404);
            $this->respond(false, 'That build was not found. It may already have been deleted.');
        }

        $used = $this->Abom_master_model->variant_bom_usage($variant_id);

        if ($used > 0) {
            // Name the BOMs rather than saying "in use". The operator can
            // then go and look at them and decide for themselves.
            $numbers = $this->Abom_master_model->variant_bom_numbers($variant_id, 5);
            $listed  = implode(', ', $numbers);

            if ($used > count($numbers)) {
                $listed .= ' and ' . ($used - count($numbers)) . ' more';
            }

            $this->output->set_status_header(409);
            $this->respond(false,
                $variant->code . ' cannot be deleted: ' . $used . ' BOM'
                . ($used === 1 ? ' was' : 's were') . ' generated from it (' . $listed . '). '
                . 'A BOM records the build it came from, so deleting it would leave '
                . ($used === 1 ? 'that document' : 'those documents')
                . ' unable to explain where the lines came from. '
                . 'Deactivate the build instead — it stops being selected for new BOMs '
                . 'and the existing ones are untouched.');
        }

        $result = $this->Abom_master_model->delete_variant($variant_id, $this->current_user_id());

        if (empty($result['ok'])) {
            $this->output->set_status_header(409);
            $this->respond(false, $result['error']);
        }

        $this->respond(true,
            $variant->code . ' deleted, with ' . $result['items'] . ' item'
            . ($result['items'] === 1 ? '' : 's') . ' and ' . $result['rules'] . ' selection rule'
            . ($result['rules'] === 1 ? '' : 's') . '.',
            array(
                'variant_id' => $variant_id,
                'items'      => (int) $result['items'],
                'rules'      => (int) $result['rules'],
            ));
    }

    /**
     * @param object      $variant
     * @param array|null  $posted  rows to re-render from, on failure
     * @param array       $errors  item ref => [field => message], plus _form
     * @param string      $message
     */
    private function render_master_bom($variant, $posted, array $errors, $message)
    {
        $family_id = (int) $variant->plc_family_id;

        $this->load->view('abom/master_bom', array(
            'variant'  => $variant,
            'family'   => $this->Abom_master_model->get_family($family_id),
            'items'    => $this->Abom_item_model->get_all(array('variant_id' => (int) $variant->id)),
            'posted'   => $posted,
            'errors'   => $errors,
            'sections' => $this->Abom_master_model->get_sections($family_id),
            'formulas' => $this->Abom_master_model->get_formulas(),
            'features' => $this->Abom_master_model->get_features(),
            'message'  => $message,
        ));
    }

    /** Hub — the six tables with their counts. */
    public function config()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->load->model('Abom_config_model');

        $this->load->view('abom/config_hub', array(
            'entities' => $this->Abom_config_model->descriptors(),
            'counts'   => $this->Abom_config_model->counts(),
            'message'  => $this->session->flashdata('abom_master_message'),
        ));
    }

    /** One table's rows. */
    public function config_list($entity = '')
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->load->model('Abom_config_model');
        $d = $this->Abom_config_model->descriptor($entity);

        if (!$d) { show_404(); return; }

        $this->load->view('abom/config_list', array(
            'entity'  => $entity,
            'd'       => $d,
            'rows'    => $this->Abom_config_model->rows($entity),
            'key'     => $this->Abom_config_model->key_column($entity),
            'refs'    => $this->config_refs($d),
            'message' => $this->session->flashdata('abom_master_message'),
        ));
    }

    /** Add or edit one row. Empty $id = add. */
    public function config_form($entity = '', $id = '')
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->load->model('Abom_config_model');
        $d = $this->Abom_config_model->descriptor($entity);

        if (!$d) { show_404(); return; }

        $id  = ($id === '' || $id === '0') ? null : $id;
        $row = ($id !== null) ? $this->Abom_config_model->row($entity, $id) : null;

        if ($id !== null && !$row) { show_404(); return; }

        if ($id === null && isset($d['addable']) && $d['addable'] === false) {
            show_404();
            return;
        }

        $this->render_config_form($entity, $d, $row, $id, array());
    }

    /** POST target for add and edit. */
    public function config_save()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->load->model('Abom_config_model');

        $entity = (string) $this->input->post('entity');
        $d      = $this->Abom_config_model->descriptor($entity);

        if (!$d) { show_404(); return; }

        $id     = $this->input->post('row_id');
        $id     = ($id === null || $id === '' || $id === '0') ? null : $id;
        $is_new = ($id === null);

        if ($is_new && isset($d['addable']) && $d['addable'] === false) {
            show_404();
            return;
        }

        $data   = $this->Abom_config_model->payload($entity, $this->input->post(), $is_new);
        $errors = $this->Abom_config_model->validate($entity, $data, $id);

        if (!empty($errors)) {
            $existing = $is_new ? null : $this->Abom_config_model->row($entity, $id);

            $shown = (object) array_merge(
                (array) ($existing ? $existing : new stdClass()), $data);

            $this->render_config_form($entity, $d, $shown, $id, $errors);
            return;
        }

        $user_id = $this->current_user_id();

        if ($is_new) {
            $id      = $this->Abom_config_model->create($entity, $data, $user_id);
            $message = $d['label'] . ' created.';
        } else {
            $this->Abom_config_model->update($entity, $id, $data, $user_id);
            $message = $d['label'] . ' updated.';
        }

        $this->session->set_flashdata('abom_master_message', $message);
        redirect(page_url . 'abom/config_list/' . $entity);
    }

    /**
     * Deletes one configuration row. POST, AJAX.
     *
     * Refused while anything still points at it — see
     * Abom_config_model::blockers(). The consequences of an orphan here
     * are silent, which is why this is a hard refusal and not a warning.
     */
    public function config_delete()
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->load->model('Abom_config_model');

        $entity = (string) $this->input->post('entity');
        $id     = $this->input->post('row_id');
        $d      = $this->Abom_config_model->descriptor($entity);

        if (!$d || empty($d['deletable'])) {
            $this->output->set_status_header(403);
            $this->respond(false, 'That cannot be deleted.');
        }

        if (!$this->Abom_config_model->row($entity, $id)) {
            $this->output->set_status_header(404);
            $this->respond(false, 'Not found — it may already have been deleted.');
        }

        $blockers = $this->Abom_config_model->blockers($entity, $id);

        if (!empty($blockers)) {
            $this->output->set_status_header(409);
            $this->respond(false,
                'This ' . strtolower($d['label']) . ' is still in use and was not deleted: '
                . implode('; ', $blockers) . '. '
                . (!empty($d['active'])
                    ? 'Deactivate it instead — that stops it being used from now on and leaves '
                      . 'everything that already refers to it intact.'
                    : 'Move or remove those first.'));
        }

        if (!$this->Abom_config_model->delete($entity, $id, $this->current_user_id())) {
            $this->output->set_status_header(500);
            $this->respond(false, 'It could not be deleted. Nothing was changed.');
        }

        $this->respond(true, $d['label'] . ' deleted.', array(
            'redirect' => page_url . 'abom/config_list/' . $entity,
        ));
    }

    /**
     * Reference options for every 'ref' field in a descriptor.
     *
     * @param  array $d
     * @return array  ref name => [key => label]
     */
    private function config_refs(array $d)
    {
        $refs = array();

        foreach ($d['fields'] as $spec) {
            if ($spec['type'] === 'ref' && !isset($refs[$spec['ref']])) {
                $refs[$spec['ref']] = $this->Abom_config_model->ref_options($spec['ref']);
            }
        }

        return $refs;
    }

    /**
     * @param string      $entity
     * @param array       $d
     * @param object|null $row
     * @param mixed       $id
     * @param array       $errors
     */
    private function render_config_form($entity, array $d, $row, $id, array $errors)
    {
        $this->load->view('abom/config_form', array(
            'entity' => $entity,
            'd'      => $d,
            'row'    => $row,
            'row_id' => $id,
            'errors' => $errors,
            'refs'   => $this->config_refs($d),
        ));
    }

    /**
     * Persists row edits on a SAVED BOM: rows added, rows removed,
     * quantities and remarks changed.
     *
     * The counterpart of save() for a document that already exists.
     * save() regenerates from the engine and treats the posted rows as
     * a selection over that; this one does NOT regenerate at all. The
     * lines of a saved BOM are a frozen snapshot and the master item
     * behind them may since have changed — re-deriving them would
     * silently rewrite an approved document from current master data.
     *
     * Gated on the same status rule as quantity editing: once a BOM is
     * engineering-approved, its lines are fixed.
     */
    public function save_lines()
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('save')) { return; }

        $this->load->model('Abom_model');

        $bom_id = (int) $this->input->post('bom_id');
        $bom    = $this->Abom_model->get_bom($bom_id);

        if (!$bom) {
            $this->output->set_status_header(404);
            $this->respond(false, 'BOM not found.');
        }

        if (!abom_qty_editable($bom)) {
            $this->output->set_status_header(403);
            $this->respond(false, 'This BOM is ' . abom_status_label($bom->status)
                . ' and its lines can no longer be changed.');
        }

        $raw  = $this->input->post('rows');
        $rows = !empty($raw) ? json_decode($raw, true) : null;

        if (!is_array($rows) || empty($rows)) {
            $this->output->set_status_header(422);
            $this->respond(false, 'No rows were received, so nothing was changed.');
        }

        $result = $this->Abom_model->save_lines($bom_id, $rows, $this->current_user_id());

        if (empty($result['ok'])) {
            $this->output->set_status_header(422);
            $this->respond(false, $result['error']);
        }

        $parts = array();
        if ($result['added'])   { $parts[] = $result['added'] . ' added'; }
        if ($result['removed']) { $parts[] = $result['removed'] . ' removed'; }

        $this->respond(true,
            'Saved — ' . $result['kept'] . ' line' . ($result['kept'] === 1 ? '' : 's') . ' kept'
            . (empty($parts) ? '' : ', ' . implode(', ', $parts)) . '.',
            array(
                'bom_id'   => $bom_id,
                'reload'   => true,
            ));
    }

    /**
     * Soft-deletes a BOM from the register.
     *
     * Gated three ways: the module's save permission, the status rule in
     * $config['abom_deletable_status'], and — because the deleted_at
     * column is the only thing standing between this and losing a
     * document — it is POST only, like every other write here.
     */
    public function delete_bom($bom_id = 0)
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('save')) { return; }

        $this->load->model('Abom_model');

        $bom = $this->Abom_model->get_bom((int) $bom_id);

        if (!$bom) {
            $this->output->set_status_header(404);
            $this->respond(false, 'BOM not found.');
        }

        // Falls back rather than requiring a config edit. application/
        // config/abom.php carries the site's submodule ids and must not
        // be shipped over a working install (see the banner in it), so
        // this feature cannot depend on that file being updated.
        $allowed = $this->abom('abom_deletable_status');
        $allowed = is_array($allowed) && !empty($allowed) ? $allowed : array('draft', 'rejected');

        if (!in_array($bom->status, $allowed, true)) {
            // Named statuses, not "not allowed" — the operator needs to
            // know which door out of this state actually exists.
            $this->output->set_status_header(409);
            $this->respond(false,
                'This BOM is ' . abom_status_label($bom->status) . ' and cannot be deleted. '
                . 'A document that has entered the approval chain is a record of who signed '
                . 'what. Reject it, reopen it, or supersede it with a revision instead — all '
                . 'three leave a trail. Only ' . implode(' and ', array_map('abom_status_label', $allowed))
                . ' BOMs may be deleted.');
        }

        if (!$this->Abom_model->soft_delete((int) $bom_id, $this->current_user_id())) {
            $this->output->set_status_header(500);
            $this->respond(false, 'The BOM could not be deleted. Nothing was changed.');
        }

        $this->respond(true, $bom->bom_no . ' deleted.', array(
            'bom_id'   => (int) $bom_id,
            'redirect' => page_url . 'abom/list',
        ));
    }

    /**
     * Duplicates a BOM as a new draft with its own number.
     *
     * Distinct from create_revision(), which supersedes the original.
     * This leaves the source untouched and current — it is for the next
     * machine, not for a correction to this one.
     */
    public function duplicate($bom_id = 0)
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('save')) { return; }

        $this->load->model('Abom_model');

        $bom = $this->Abom_model->get_bom((int) $bom_id);

        if (!$bom) {
            $this->output->set_status_header(404);
            $this->respond(false, 'BOM not found.');
        }

        // Any status may be duplicated, including an approved one — that
        // is the commonest case. The COPY is always a fresh draft, so
        // nothing about the original's standing travels with it.
        $result = $this->Abom_model->duplicate((int) $bom_id, $this->current_user_id());

        if (empty($result['ok'])) {
            $this->output->set_status_header(500);
            $this->respond(false, $result['error']);
        }

        $this->respond(true,
            $result['bom_no'] . ' created as a copy of ' . $bom->bom_no . '.', array(
                'bom_id'   => (int) $result['bom_id'],
                'bom_no'   => $result['bom_no'],
                'redirect' => page_url . 'abom/view/' . (int) $result['bom_id'],
            ));
    }

    /**
     * Rewrites the MACHINE CONFIGURATION of a saved BOM and re-derives
     * its lines.
     *
     * This is what a clone needs to be worth cloning. duplicate() gives
     * a fresh draft of an existing document; without this the copy is
     * stuck describing the original machine, and copying an 11-axis BOM
     * to quote a 15-axis one means retyping it.
     *
     * Regenerating is not optional. Axes, tracks, speed, motion type,
     * model, the family and the variant are the inputs to variant
     * selection and to every computed formula, so a header that says 15
     * axes above lines computed for 11 is a document that will be built
     * wrong. Changing the spec therefore changes the sheet.
     *
     * Three things the engineer did BY HAND survive it, because they
     * are not derivable and losing them silently would be the worst
     * failure this screen could have:
     *
     *   hand-added rows      kept in full and re-appended
     *   typed remarks        re-attached to the same master item
     *   quantity overrides   re-applied to the same master item
     *
     * Anything whose master item is not in the new build cannot be
     * carried, and is REPORTED rather than dropped in silence.
     *
     * Gated to draft/rejected — see abom_config_editable(). A sheet
     * under review or already approved is not re-specified in place;
     * it is duplicated or revised.
     */
    public function save_config()
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('save')) { return; }

        $this->load->model('Abom_model');

        $bom_id = (int) $this->input->post('bom_id');
        $bom    = $this->Abom_model->get_bom($bom_id);

        if (!$bom) {
            $this->output->set_status_header(404);
            $this->respond(false, 'BOM not found. It may have been deleted.');
        }

        if (!abom_config_editable($bom)) {
            $this->output->set_status_header(409);
            $this->respond(false, 'BOM ' . $bom->bom_no . ' is ' . strtoupper($bom->status)
                . '. Its configuration can no longer be changed. '
                . 'Duplicate it, or raise a revision, and edit that instead.');
        }

        // Start from what is STORED, not from the module defaults, so a
        // field the panel did not post keeps this BOM's own value rather
        // than silently reverting to a factory setting.
        $base = $this->default_config();
        foreach (array('axes', 'tracks', 'speed_ppm', 'j4_units', 'battery_qty') as $k) {
            $base[$k] = (int) $bom->$k;
        }
        $base['motion_type']   = $bom->motion_type;
        $base['machine_model'] = $bom->machine_model;
        $base['machine_side']  = $bom->machine_side;
        $base['df_ref']        = (string) $bom->df_ref;

        $cfg    = $this->config_from_input($base);
        $errors = $this->validate_config($cfg);

        if (!empty($errors)) {
            $this->output->set_status_header(422);
            $this->respond(false, 'Please correct the highlighted fields.', array('errors' => $errors));
        }

        // Read before the clash check, which needs it, rather than at
        // the point it is written into the header further down.
        $posted_revision  = $this->input->post('revision');
        $header_revision  = ($posted_revision !== null && trim((string) $posted_revision) !== '')
            ? trim((string) $posted_revision)
            : (string) $bom->revision;

        // Same rule as save(). Excludes THIS BOM, so re-applying a
        // configuration without touching the reference is not a clash
        // with itself.
        $clash = $this->Abom_model->df_ref_conflict(
            isset($cfg['df_ref']) ? $cfg['df_ref'] : '',
            isset($header_revision) ? $header_revision : $bom->revision,
            $bom_id
        );

        if ($clash) {
            $this->output->set_status_header(422);
            $this->respond(false, $this->df_ref_taken_message($clash), array(
                'errors' => array('df_ref' => 'Already used by ' . $clash->bom_no . '.'),
                'conflict' => $this->df_ref_conflict_payload($clash),
            ));
        }

        $this->load->library('Abom_engine', null, 'abom_engine');

        $result = $this->abom_engine->generate($cfg);
        $fresh  = $result['lines'];

        // Same rule as save(): no variant means no lines, and an empty
        // BOM reads as "nothing is required for this machine". Refuse,
        // and leave the existing document exactly as it was.
        if (!empty($result['variant_missing']) || empty($fresh)) {
            $this->output->set_status_header(422);
            $this->respond(false, $this->variant_missing_message($cfg)
                . ' The BOM has not been changed.');
        }

        $carried = $this->carry_line_edits($this->Abom_model->get_lines($bom_id), $fresh);

        $header = array(
            'df_ref'            => ($cfg['df_ref'] !== '') ? $cfg['df_ref'] : null,
            'machine_model'     => $cfg['machine_model'],
            'machine_side'      => $cfg['machine_side'],
            'axes'              => (int) $cfg['axes'],
            'tracks'            => (int) $cfg['tracks'],
            'speed_ppm'         => (int) $cfg['speed_ppm'],
            'motion_type'       => $cfg['motion_type'],
            'plc_family_id'     => (int) $result['family_id'],
            'plc_family_locked' => !empty($result['overridden']) ? 1 : 0,
            'variant_id'        => isset($result['variant_id']) ? $result['variant_id'] : null,
            'variant_locked'    => !empty($result['variant_overridden']) ? 1 : 0,
            'j4_units'          => (int) $cfg['j4_units'],
            'battery_qty'       => (int) $cfg['battery_qty'],
            'features_json'     => json_encode($cfg['features']),
        );

        // A revision is the author's label for the document, so it is
        // writable here, but never blanked by an absent field.
        if ($header_revision !== (string) $bom->revision) {
            $header['revision'] = $header_revision;
        }

        $ok = $this->Abom_model->replace_lines($bom_id, $header, $carried['lines'],
            $this->current_user_id());

        if (!$ok) {
            $this->output->set_status_header(500);
            $this->respond(false, 'The configuration could not be applied. Nothing was changed.');
        }

        $this->respond(true, $this->reconfigure_summary($bom, $header, $carried), array(
            'bom_id'   => $bom_id,
            'dropped'  => $carried['dropped'],
            'redirect' => page_url . 'abom/view/' . $bom_id,
        ));
    }

    /**
     * Narrows the build register.
     *
     * Filtered in PHP over the already-loaded set rather than in SQL.
     * get_variants() is cached and shared with the generator's override
     * picker and with the engine, so pushing a WHERE into it would
     * quietly narrow those too — a filter on one screen must not change
     * what another screen can select.
     *
     * The register is also the one list where the row count is bounded
     * by how many machine types the business builds, not by how much
     * work it has done. Even a very successful decade is tens of rows.
     *
     * @param  array $variants  id => variant
     * @param  array $summary   id => item counts and flag counts
     * @param  array $filters
     * @return array
     */
    private function filter_variants(array $variants, array $summary, array $filters)
    {
        $search = strtolower(trim((string) $filters['search']));
        $out    = array();

        foreach ($variants as $id => $v) {
            if ($filters['family'] > 0 && (int) $v->plc_family_id !== (int) $filters['family']) {
                continue;
            }

            if ($filters['model'] !== '' && (string) $v->machine_model !== $filters['model']) {
                continue;
            }

            if ($filters['panel'] !== ''
                && (string) $v->default_panel_location !== $filters['panel']) {
                continue;
            }

            // '' means "active and retired". '1' and '0' are explicit.
            if ($filters['active'] !== ''
                && (int) !empty($v->is_active) !== (int) $filters['active']) {
                continue;
            }

            if ($filters['flag'] !== '') {
                $c = isset($summary[$id]) ? $summary[$id]
                    : array('conflict' => 0, 'no_erp' => 0, 'review' => 0);

                if ($filters['flag'] === 'clean') {
                    if ($c['conflict'] || $c['no_erp'] || $c['review']) { continue; }
                } elseif (empty($c[$filters['flag']])) {
                    continue;
                }
            }

            $out[$id] = $v;
        }

        return $search === '' ? $out : $this->search_variants($out, $search);
    }

    /**
     * Free-text search over the build register, PHRASE FIRST.
     *
     * Two passes, and the order matters more than it looks.
     *
     * A plain "every word must appear somewhere" match is wrong here in
     * a way that is easy to miss. Build names read
     * "SPM1200L 6 axis / 12 track / 100 PPM", so a search for
     * "6 track" finds the word "6" in "6 axis" and the word "track" in
     * "12 track" and returns a 12-track machine to somebody who asked
     * for a 6-track one. The right rows are in the result, buried among
     * wrong ones, which is the worst kind of search failure: it looks
     * like it worked.
     *
     * So: if the typed string appears verbatim in any build, only those
     * builds are returned. Nothing else can be what was meant.
     *
     * Only when NO build contains the phrase does it fall back to
     * all-words-in-any-order, which is what makes half-remembered
     * queries work — "1808 fx5" and "fx5 1808" both find FX5-1808, and
     * neither is a phrase in anything.
     *
     * @param  array  $variants  already narrowed by the other filters
     * @param  string $search    lower-cased, trimmed
     * @return array
     */
    private function search_variants(array $variants, $search)
    {
        $hay = array();
        foreach ($variants as $id => $v) {
            $hay[$id] = $this->variant_haystack($v);
        }

        $phrase = array();
        foreach ($variants as $id => $v) {
            if (strpos($hay[$id], $search) !== false) {
                $phrase[$id] = $v;
            }
        }

        if (!empty($phrase)) {
            return $phrase;
        }

        $words = array_filter(preg_split('/\s+/', $search), 'strlen');

        if (empty($words)) {
            return $variants;
        }

        $out = array();
        foreach ($variants as $id => $v) {
            foreach ($words as $word) {
                if (strpos($hay[$id], $word) === false) {
                    continue 2;
                }
            }
            $out[$id] = $v;
        }

        return $out;
    }

    /**
     * Everything about a build that somebody might type at the
     * register, flattened and lower-cased.
     *
     * The recorded machine is spelled out as "6 axis", "12 track",
     * "100 ppm" rather than left as bare numbers, so searching the way
     * the table READS finds the row. Bare numbers alone would make "6"
     * match a speed, an axis count and a track count indiscriminately.
     *
     * source_df carries every DF a build was assembled from, so
     * searching an old drawing number still finds the build that
     * absorbed it — including a retired one, which is usually exactly
     * what somebody chasing an old document wants.
     *
     * @param  object $v
     * @return string
     */
    private function variant_haystack($v)
    {
        $bits = array(
            (string) $v->code,
            (string) $v->name,
            (string) $v->description,
            (string) $v->machine_model,
            (string) $v->source_df,
            (string) $v->default_panel_location,
        );

        foreach (array('ref_axes' => 'axis', 'ref_tracks' => 'track',
                       'ref_speed_ppm' => 'ppm') as $col => $unit) {
            if (isset($v->$col) && $v->$col !== null && (int) $v->$col > 0) {
                $bits[] = (int) $v->$col . ' ' . $unit;
            }
        }

        if (!empty($v->ref_motion_type)) {
            $bits[] = (string) $v->ref_motion_type;
        }

        return strtolower(implode(' ', $bits));
    }

    /**
     * The distinct non-empty values of one variant column, for a filter
     * dropdown. Derived so the options track the data.
     *
     * @param  array  $variants
     * @param  string $field
     * @return array
     */
    private function distinct_variant_field(array $variants, $field)
    {
        $out = array();

        foreach ($variants as $v) {
            $value = isset($v->$field) ? trim((string) $v->$field) : '';
            if ($value !== '' && !in_array($value, $out, true)) {
                $out[] = $value;
            }
        }

        sort($out);

        return $out;
    }

    /**
     * Says where the prefilled configuration came from, and — when the
     * build has gaps — which values are the module's defaults rather
     * than the build's own.
     *
     * The distinction matters. Somebody who pressed "Generate BOM" on
     * the FX5-1808 row is entitled to assume every number on the panel
     * describes that build. Where one does not, saying so is the
     * difference between a starting point and a quiet wrong answer.
     *
     * @param  array $from  from Abom_master_model::config_for_variant()
     * @return string
     */
    private function build_prefill_notice(array $from)
    {
        $v   = $from['variant'];
        $cfg = $from['cfg'];

        $machine = (int) $cfg['axes'] . ' axis, ' . (int) $cfg['tracks'] . ' track, '
            . (int) $cfg['speed_ppm'] . ' PPM ' . strtolower($cfg['motion_type']);

        $msg = 'Configured for the <b>' . abom_e($v->code) . '</b> build'
            . (!empty($v->source_df) ? ' (' . abom_e($v->source_df) . ')' : '')
            . ' — ' . abom_e($cfg['machine_model']) . ', ' . abom_e($machine)
            . '. Change anything you need to and press Recalculate.';

        if (!empty($from['missing'])) {
            $msg .= ' <b>Note:</b> this build has no recorded '
                . abom_e(implode(', ', $from['missing']))
                . ', so ' . (count($from['missing']) === 1 ? 'that value is' : 'those values are')
                . ' a default rather than the build&rsquo;s own. Check '
                . (count($from['missing']) === 1 ? 'it' : 'them') . ' before saving.';
        }

        return $msg;
    }

    /**
     * The recorded machine does not route back to its own build.
     *
     * A data fault, not a user error, and still stated plainly. What
     * changed is what it COSTS: the sheet used to be silently swapped
     * for whichever build the rules reached, which is how "generate from
     * this reference" ended up showing extra lines. The requested build
     * is now pinned, so this reports the disagreement without changing
     * what you are looking at.
     *
     * @param  object $wanted
     * @param  array  $result
     * @param  int    $reached  variant the rules would have selected
     * @return string
     */
    private function build_prefill_mismatch($wanted, array $result, $reached = 0)
    {
        $other = '';

        if ($reached > 0) {
            $row = $this->Abom_master_model->get_variant($reached);
            if ($row) {
                $other = $row->code;
            }
        }

        return '<b>&#9888;&#65039; Heads up — this build&rsquo;s selection rules disagree '
            . 'with its recorded machine.</b> The sheet below IS the <b>'
            . abom_e($wanted->code) . '</b> build, exactly as the reference holds it. '
            . 'But the machine recorded against it '
            . ($other !== ''
                ? 'would reach the <b>' . abom_e($other) . '</b> build'
                : 'reaches no build at all')
            . ' if the rules were left to decide, so a BOM configured from scratch '
            . 'with those numbers would not land here. Nothing you have done wrong, '
            . 'and nothing to stop you — but worth reporting so the master data can '
            . 'be corrected.';
    }

    /**
     * Live "is this DF reference free?" check, for the field itself.
     *
     * Answers WHILE THE REFERENCE IS BEING TYPED rather than at the end
     * of a save. Finding out that DF-1805A is taken after configuring a
     * machine, generating forty lines and pressing Save means doing the
     * thinking twice; finding out as the field loses focus costs
     * nothing.
     *
     * This endpoint decides NOTHING. save() and save_config() re-run the
     * same model check on the write, so a stale answer here, a disabled
     * script or a hand-made POST cannot get a duplicate through.
     *
     * Read-only, so it is gated on any of the three permissions rather
     * than on 'save' — a checker with view rights typing in a search box
     * should not be told the module is unavailable.
     */
    public function check_df_ref()
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_any_perm()) { return; }

        $this->load->model('Abom_model');

        $df_ref = trim((string) $this->input->post('df_ref'));

        if ($df_ref === '') {
            $this->respond(true, '', array('taken' => false));
        }

        $revision = trim((string) $this->input->post('revision'));
        $exclude  = (int) $this->input->post('bom_id');

        $clash = $this->Abom_model->df_ref_conflict($df_ref, $revision, $exclude);

        if (!$clash) {
            $this->respond(true, $df_ref . ' is free.', array('taken' => false));
        }

        $this->respond(true, $this->df_ref_taken_message($clash), array(
            'taken'    => true,
            'conflict' => $this->df_ref_conflict_payload($clash),
        ));
    }

    /**
     * One sentence naming WHICH document already holds the reference,
     * and what state it is in.
     *
     * "This DF reference already exists" is not enough to act on. The
     * useful question is always the next one — which BOM, and is it a
     * draft somebody abandoned or an approved document? So the answer
     * names it, and the caller renders a link to it.
     *
     * @param  object $clash
     * @return string
     */
    private function df_ref_taken_message($clash)
    {
        $where = $clash->bom_no
            . ((string) $clash->revision !== '' ? ' rev ' . $clash->revision : '');

        return 'DF reference ' . $clash->df_ref . ' is already used by ' . $where
            . ' (' . abom_status_label($clash->status) . ')'
            . (!empty($clash->machine_model) ? ', a ' . $clash->machine_model : '')
            . '. Two BOMs cannot share one drawing number at the same revision — '
            . 'use a different reference, or raise a revision on ' . $clash->bom_no . '.';
    }

    /**
     * The conflicting BOM, shaped for the browser so the field can link
     * straight to it.
     *
     * @param  object $clash
     * @return array
     */
    private function df_ref_conflict_payload($clash)
    {
        return array(
            'bom_id'   => (int) $clash->id,
            'bom_no'   => $clash->bom_no,
            'revision' => $clash->revision,
            'status'   => abom_status_label($clash->status),
            'url'      => page_url . 'abom/view/' . (int) $clash->id,
        );
    }

    /**
     * Re-attaches the engineer's hand work to a freshly generated line
     * set, matching on MASTER ITEM rather than on line number.
     *
     * Line numbers are worthless across a regeneration: a different
     * build has a different set of parts in a different order, so line 7
     * before and line 7 after are unrelated. item_id is the only stable
     * identity a generated line has.
     *
     * Hand-added rows have no item_id at all, which is exactly why they
     * are appended wholesale instead of matched — there is nothing in
     * the new build for them to correspond to, and they are the rows
     * least recoverable if lost.
     *
     * A REMARK COUNTS AS HAND-SET ONLY WHEN IT DIFFERS FROM THE SEEDED
     * DEFAULT. Abom_engine seeds every line's user_remark from the
     * master item's usage_remark, so "user_remark is not empty" is true
     * of nearly every line and says nothing about whether a person
     * typed it. Carrying those forward would re-pin one build's master
     * text onto another build's lines, and would report twenty remarks
     * preserved when the engineer wrote two.
     *
     * Clearing a seeded remark to blank IS a hand edit and is carried as
     * one — otherwise the next regeneration would quietly put back the
     * text somebody deliberately deleted.
     *
     * @param  array $old    persisted lines
     * @param  array $fresh  engine output
     * @return array  lines, kept counts, dropped descriptions
     */
    private function carry_line_edits(array $old, array $fresh)
    {
        $remarks   = array();
        $overrides = array();
        $manual    = array();

        // SECOND KEY: THE PART ITSELF.
        //
        // Matching only on item_id means matching on the master ROW, and
        // every build has its own rows. Change the model on a clone, a
        // different build is selected, and the same physical part is a
        // different abom_item id — so every typed remark and every
        // quantity override was dropped and the sheet came back as pure
        // master data. That is what "I cloned it and my changes are gone"
        // is.
        //
        // So a part is also remembered by what it IS: its ERP code, or
        // its part number when it has no code yet. Carried across a build
        // change, which is the case a clone exists for.
        //
        // AMBIGUOUS KEYS ARE NOT USED. This data legitimately contains one
        // ERP code on two different parts, so a key appearing more than
        // once in the old sheet is skipped rather than guessed at — item_id
        // still matches those exactly when the build has not changed.
        // Candidates for identifying a part, best first. ERP code is the
        // stronger identifier; the part number is the fallback, and the
        // only one available while a code is still pending.
        $part_keys = function ($line) {
            $keys = array();

            $erp = isset($line->erp_code) ? strtoupper(trim((string) $line->erp_code)) : '';
            if ($erp !== '' && $erp !== 'NEW' && $erp !== 'PENDING') {
                $keys[] = 'E:' . $erp;
            }

            $part = isset($line->part_no) ? strtoupper(trim((string) $line->part_no)) : '';
            if ($part !== '' && $part !== '—' && $part !== '-') {
                $keys[] = 'P:' . $part;
            }

            return $keys;
        };

        // How often each candidate occurs in the OLD sheet. A key that
        // occurs more than once identifies nothing, so it is skipped and
        // the next candidate is tried -- this data really does carry one
        // ERP code on two different parts, and those two have different
        // part numbers, which still resolves them.
        $key_count = array();
        foreach ($old as $line) {
            if (empty($line->item_id)) { continue; }
            foreach ($part_keys($line) as $k) {
                $key_count[$k] = isset($key_count[$k]) ? $key_count[$k] + 1 : 1;
            }
        }

        $part_key = function ($line) use ($part_keys, &$key_count) {
            foreach ($part_keys($line) as $k) {
                if (!isset($key_count[$k]) || $key_count[$k] === 1) {
                    return $k;
                }
            }

            return '';
        };

        $remarks_by_part   = array();
        $overrides_by_part = array();

        foreach ($old as $line) {
            if (empty($line->item_id)) {
                if (!empty($line->is_manual_add)) {
                    $manual[] = $line;
                }
                continue;
            }

            $key = (int) $line->item_id;

            $pk = $part_key($line);

            $typed  = isset($line->user_remark) ? trim((string) $line->user_remark) : '';
            $seeded = isset($line->usage_remark) ? trim((string) $line->usage_remark) : '';

            if ($typed !== $seeded) {
                $remarks[$key] = $typed;
                if ($pk !== '') {
                    $remarks_by_part[$pk] = array('value' => $typed, 'from' => $key);
                }
            }

            if (!empty($line->is_overridden)) {
                $overrides[$key] = array(
                    'qty'    => (int) $line->qty,
                    'reason' => isset($line->override_reason) ? $line->override_reason : null,
                );
                if ($pk !== '') {
                    $overrides_by_part[$pk] = array('value' => $overrides[$key], 'from' => $key);
                }
            }
        }

        $seen   = array();
        $kept_r = 0;
        $kept_o = 0;

        foreach ($fresh as $line) {
            $key = !empty($line->item_id) ? (int) $line->item_id : 0;
            if ($key === 0) {
                continue;
            }
            $seen[$key] = true;

            // item_id first — exact, and right whenever the build is
            // unchanged. The part key is the fallback that survives a
            // build change.
            $pk = $part_key($line);

            if (!isset($remarks[$key]) && $pk !== '' && isset($remarks_by_part[$pk])) {
                $remarks[$key] = $remarks_by_part[$pk]['value'];
                // The edit came from a DIFFERENT master row. Mark that row
                // as accounted for, or it is reported as dropped when it
                // was in fact carried onto the part's new row.
                $seen[$remarks_by_part[$pk]['from']] = true;
            }

            if (!isset($overrides[$key]) && $pk !== '' && isset($overrides_by_part[$pk])) {
                $overrides[$key] = $overrides_by_part[$pk]['value'];
                $seen[$overrides_by_part[$pk]['from']] = true;
            }

            if (isset($remarks[$key])) {
                $line->user_remark = $remarks[$key];
                $kept_r++;
            }

            if (isset($overrides[$key])) {
                // computed_qty stays the engine's answer for the NEW
                // configuration. The override sits on top of it, so the
                // document still shows what the rule would have given
                // and what was put there instead.
                $line->qty             = $overrides[$key]['qty'];
                $line->is_overridden   = 1;
                $line->override_reason = $overrides[$key]['reason'];
                $kept_o++;
            }
        }

        // Hand-added rows go back on the end, keeping their section so
        // they file where they were put.
        $lines = $fresh;
        foreach ($manual as $line) {
            $lines[] = $line;
        }

        $n = 0;
        foreach ($lines as $line) {
            $n++;
            $line->line_no = $n;
        }

        $dropped = array();
        foreach (array_keys($remarks) as $key) {
            if (empty($seen[$key])) {
                $dropped[] = 'remark on item #' . $key;
            }
        }
        foreach (array_keys($overrides) as $key) {
            if (empty($seen[$key])) {
                $dropped[] = 'quantity override on item #' . $key;
            }
        }

        return array(
            'lines'     => $lines,
            'manual'    => count($manual),
            'remarks'   => $kept_r,
            'overrides' => $kept_o,
            'dropped'   => $dropped,
        );
    }

    /**
     * Plain-language account of what applying the configuration did.
     *
     * It names what CHANGED and what was CARRIED, because "Saved." after
     * an operation that rewrote every line of a parts list is not enough
     * for anyone to know whether the result is what they meant.
     *
     * @param  object $before
     * @param  array  $header
     * @param  array  $carried
     * @return string
     */
    private function reconfigure_summary($before, array $header, array $carried)
    {
        $labels = array(
            'df_ref'        => 'DF reference',
            'machine_model' => 'model',
            'machine_side'  => 'side',
            'axes'          => 'axes',
            'tracks'        => 'tracks',
            'speed_ppm'     => 'speed',
            'motion_type'   => 'motion',
        );

        $changed = array();
        foreach ($labels as $field => $label) {
            $was = (string) $before->{$field};
            $now = (string) $header[$field];
            if ($was !== $now) {
                $changed[] = $label . ' ' . ($was !== '' ? $was : '-')
                    . ' -> ' . ($now !== '' ? $now : '-');
            }
        }

        $parts = array($before->bom_no . ' updated');
        if (!empty($changed)) {
            $parts[0] .= ': ' . implode(', ', $changed);
        }
        $parts[0] .= '.';

        $parts[] = count($carried['lines']) . ' lines regenerated.';

        $kept = array();
        if ($carried['manual'] > 0)    { $kept[] = $carried['manual'] . ' added row' . ($carried['manual'] === 1 ? '' : 's'); }
        if ($carried['remarks'] > 0)   { $kept[] = $carried['remarks'] . ' remark' . ($carried['remarks'] === 1 ? '' : 's'); }
        if ($carried['overrides'] > 0) { $kept[] = $carried['overrides'] . ' quantity override' . ($carried['overrides'] === 1 ? '' : 's'); }
        if (!empty($kept)) {
            $parts[] = 'Carried over: ' . implode(', ', $kept) . '.';
        }

        if (!empty($carried['dropped'])) {
            $parts[] = 'Could not be carried (the item is not in the new build): '
                . implode(', ', $carried['dropped']) . '.';
        }

        return implode(' ', $parts);
    }

    /**
     * Confirms a MANUAL line or acknowledges a conflict line — the two
     * things that gate Draft -> Submitted.
     */
    public function acknowledge_line()
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('save')) { return; }

        $this->load->model('Abom_model');
        $this->load->model('Abom_approval_model');

        $bom_id  = (int) $this->input->post('bom_id');
        $line_id = (int) $this->input->post('line_id');
        $what    = $this->input->post('what') === 'acknowledge' ? 'acknowledge' : 'confirm';
        $comment = trim((string) $this->input->post('comment'));

        $bom = $this->Abom_model->get_bom($bom_id);
        if (!$bom) {
            $this->output->set_status_header(404);
            $this->respond(false, 'BOM not found.');
        }

        if (!abom_qty_editable($bom)) {
            $this->output->set_status_header(403);
            $this->respond(false, 'This BOM is ' . abom_status_label($bom->status)
                . ' and can no longer be annotated.');
        }

        if ($what === 'acknowledge' && $comment === '') {
            $this->output->set_status_header(422);
            $this->respond(false, 'An ERP conflict must be acknowledged with a comment.');
        }

        $ok = $this->Abom_approval_model->acknowledge_line(
            $bom_id, $line_id, $what, $comment, $this->current_user_id());

        if (!$ok) {
            $this->output->set_status_header(422);
            $this->respond(false, 'The line could not be annotated.');
        }

        $this->respond(true, $what === 'acknowledge' ? 'Conflict acknowledged.' : 'Quantity confirmed.',
            array('blockers' => $this->Abom_approval_model->blockers(
                $this->Abom_model->get_bom($bom_id), $this->current_user_id())));
    }

    /**
     * Shared workflow entry point.
     *
     * @param int    $bom_id
     * @param string $what  advance|reject|reopen|revision
     */
    private function workflow_action($bom_id, $what)
    {
        $this->json_only();

        if (!$this->require_tables()) { return; }
        if (!$this->require_permissions_configured()) { return; }

        // A floor, checked BEFORE the BOM is loaded. The per-action checks
        // below are the real authority, but they run after the transition
        // is looked up, so without this an ungranted user learned a BOM's
        // status ("There is no next stage from Approved — Procurement")
        // or its existence from the 404. Reads are gated the same way, so
        // leaving this path open would have been inconsistent with them.
        // Weaker than every check below it, so it relaxes nothing.
        if (!$this->require_any_perm()) { return; }

        $this->load->model('Abom_model');
        $this->load->model('Abom_approval_model');

        $bom = $this->Abom_model->get_bom($bom_id);
        if (!$bom) {
            $this->output->set_status_header(404);
            $this->respond(false, 'BOM not found.');
        }

        $user_id   = $this->current_user_id();
        $user_name = $this->current_user_name();
        $comment   = trim((string) $this->input->post('comment'));

        if ($what === 'advance') {
            $t = $this->Abom_approval_model->next_transition($bom->status);
            if (!$t) {
                $this->output->set_status_header(422);
                $this->respond(false, 'There is no next stage from "'
                    . abom_status_label($bom->status) . '".');
            }
            if (!$this->has_perm($t['permission'])) {
                $this->output->set_status_header(403);
                $this->respond(false, 'You do not have permission to ' . strtolower($t['label']) . '.');
            }
            $r = $this->Abom_approval_model->advance($bom, $user_id, $user_name, $comment);

        } elseif ($what === 'reject') {
            // Spec 6.3: any stage may reject. A rejector is therefore
            // someone who holds approval authority, not merely anyone
            // logged in. Without this an ungranted user could reject and
            // reopen BOMs and leave their name in the approval trail.
            if (!$this->has_perm('check')) {
                $this->output->set_status_header(403);
                $this->respond(false, 'You do not have permission to reject a BOM.');
            }
            $r = $this->Abom_approval_model->reject($bom, $user_id, $user_name, $comment);

        } elseif ($what === 'reopen') {
            // Reopening returns a rejected BOM to draft for editing,
            // which is a generator action.
            if (!$this->has_perm('save')) {
                $this->output->set_status_header(403);
                $this->respond(false, 'You do not have permission to reopen a BOM.');
            }
            $r = $this->Abom_approval_model->reopen($bom, $user_id, $user_name, $comment);

        } else {
            if (!$this->has_perm('save')) {
                $this->output->set_status_header(403);
                $this->respond(false, 'You do not have permission to create a revision.');
            }
            $r = $this->Abom_approval_model->create_revision($bom, $user_id, $user_name, $comment);
            if (!$r['ok']) {
                $this->output->set_status_header(422);
                $this->respond(false, implode(' ', $r['errors']));
            }
            $this->respond(true, 'Revision created.', array(
                'bom_id'   => $r['bom_id'],
                'redirect' => page_url . 'abom/view/' . $r['bom_id'],
            ));
        }

        if (!$r['ok']) {
            $this->output->set_status_header(422);
            $this->respond(false, implode(' ', $r['errors']), array('errors' => $r['errors']));
        }

        $this->respond(true, 'BOM is now ' . abom_status_label($r['status']) . '.', array(
            'status'       => $r['status'],
            'status_label' => abom_status_label($r['status']),
            'redirect'     => page_url . 'abom/view/' . (int) $bom->id,
        ));
    }

    // -----------------------------------------------------------------
    // EXPORT  (build order step 7)
    // -----------------------------------------------------------------

    /**
     * @param string $format  csv|xlsx|pdf
     * @param int    $bom_id
     */
    public function export($format = 'csv', $bom_id = 0)
    {
        if (!$this->require_tables()) { return; }

        if (!$this->require_any_perm()) {
            return;
        }

        $this->load->model('Abom_model');
        $bom = $this->Abom_model->get_bom((int) $bom_id);

        if (!$bom) { show_404(); return; }

        $lines = $this->Abom_model->get_lines((int) $bom_id);

        $this->load->library('Abom_exporter', null, 'abom_exporter');

        // A BINARY DOWNLOAD MUST CARRY NOTHING BUT THE FILE.
        //
        // Any notice, warning or stray echo raised while building the
        // document lands in the response ahead of the bytes, and the
        // result is a file the application refuses to open with no
        // indication why. A vendored PHPExcel warning did exactly that
        // to the XLSX export on 2026-08-11 — the download was a
        // CodeIgniter warning page with the spreadsheet appended.
        //
        // The exporter suppresses that specific known defect at source.
        // This is the backstop for everything else: whatever leaks
        // during generation is captured and discarded rather than
        // corrupting the file, and is logged so it is not lost.
        ob_start();

        $result = $this->build_export(strtolower($format), $bom, $lines);

        $stray = ob_get_clean();

        if ($stray !== '' && $stray !== false) {
            log_message('error', 'Abom export (' . $format . ') produced ' . strlen($stray)
                . ' bytes of stray output, discarded so the download stays valid: '
                . substr(strip_tags($stray), 0, 500));
        }

        if ($result === null) {
            return;                       // the handler already rendered
        }

        $this->stream_file($result['filename'], $result['mime'], $result['body']);
    }

    /**
     * Builds one export. Returns NULL when it has rendered its own
     * response (the missing-ext guard), so export() knows not to stream.
     *
     * @param  string $format
     * @param  object $bom
     * @param  array  $lines
     * @return array|null  ['filename'=>, 'mime'=>, 'body'=>]
     */
    private function build_export($format, $bom, array $lines)
    {
        switch ($format) {
            case 'xlsx':
                try {
                    $out = $this->abom_exporter->xlsx($bom, $lines);
                } catch (RuntimeException $e) {
                    // A missing server extension is an operator problem,
                    // not a stack trace. Discard anything buffered first
                    // or the guard page renders inside it.
                    while (ob_get_level() > 0) { ob_end_clean(); }

                    $this->output->set_status_header(503);
                    $this->load->view('abom/export_unavailable', array(
                        'format'  => 'Excel (XLSX)',
                        'message' => $e->getMessage(),
                        'bom'     => $bom,
                    ));
                    return null;
                }

                $body = file_get_contents($out['path']);
                @unlink($out['path']);

                return array(
                    'filename' => $out['filename'],
                    'mime'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'body'     => $body,
                );

            case 'pdf':
                $out = $this->abom_exporter->pdf($bom, $lines);

                return array(
                    'filename' => $out['filename'],
                    'mime'     => 'application/pdf',
                    'body'     => $out['body'],
                );

            case 'csv':
            default:
                $out = $this->abom_exporter->csv($bom, $lines);

                return array(
                    'filename' => $out['filename'],
                    'mime'     => 'text/csv; charset=UTF-8',
                    'body'     => $out['body'],
                );
        }
    }

    /**
     * @param string $filename
     * @param string $mime
     * @param string $body
     */
    private function stream_file($filename, $mime, $body)
    {
        $this->output
            ->set_content_type($mime)
            ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
            ->set_header('Content-Length: ' . strlen($body))
            ->set_output($body);
    }

    /**
     * Everything the approval panel needs: the next transition, whether
     * this user may perform it, what is currently blocking it, the
     * trail, and whether the ACL is even configured.
     *
     * @param  object $bom
     * @return array
     */
    private function workflow_state($bom)
    {
        $state = array(
            'enabled'      => false,
            'configured'   => false,
            'problems'     => array(),
            'next'         => null,
            'may'          => false,
            'blockers'     => array(),
            'trail'        => array(),
            'can_reject'   => false,
            'can_reopen'   => false,
            'can_revise'   => false,
            'status_label' => abom_status_label(isset($bom->status) ? $bom->status : ''),
        );

        if (empty($bom->id)) {
            return $state;              // unsaved preview has no workflow
        }

        $state['enabled'] = true;

        $this->load->library('Abom_permission_guard', null, 'abom_permission_guard');
        $state['configured'] = $this->abom_permission_guard->is_configured();
        $state['problems']   = $this->abom_permission_guard->problems();

        $this->load->model('Abom_approval_model');
        $user_id = $this->current_user_id();

        $state['next']     = $this->Abom_approval_model->next_transition($bom->status);
        $state['blockers'] = $this->Abom_approval_model->blockers($bom, $user_id);
        $state['trail']    = $this->Abom_approval_model->trail((int) $bom->id);

        if ($state['configured'] && $state['next']) {
            $state['may'] = $this->has_perm($state['next']['permission']);
        }

        $state['can_reject'] = $state['configured']
            && in_array($bom->status, array('submitted', 'checked', 'eng_approved'), true);
        $state['can_reopen'] = ($bom->status === 'rejected');
        $state['can_revise'] = $state['configured'] && ($bom->status === 'approved');

        return $state;
    }

    /**
     * @return string
     */
    private function current_user_name()
    {
        $session = $this->session->userdata('logged_in');
        $name = trim(
            (isset($session['first_name']) ? $session['first_name'] : '') . ' ' .
            (isset($session['last_name']) ? $session['last_name'] : '')
        );

        return $name !== '' ? $name : 'User ' . $this->current_user_id();
    }

    /**
     * Saved BOMs.
     */
    public function bom_list()
    {
        if (!$this->require_tables()) {
            return;
        }

        if (!$this->require_any_perm()) {
            return;
        }

        $this->load->model('Abom_model');

        $filters = array(
            'status'        => trim((string) $this->input->get('status')),
            'machine_model' => trim((string) $this->input->get('model')),
            'manufacturer'  => trim((string) $this->input->get('mfr')),
            'search'        => trim((string) $this->input->get('search')),
        );

        $deletable = $this->abom('abom_deletable_status');
        if (!is_array($deletable) || empty($deletable)) {
            $deletable = array('draft', 'rejected');     // same fallback as delete_bom()
        }

        $this->load->view('abom/list', array(
            'boms'          => $this->Abom_model->get_all($filters),
            'filters'       => $filters,
            'models'        => $this->abom('abom_models'),
            // Derived from the saved BOMs, not a config list. A new
            // brand becomes filterable as soon as a BOM carrying it is
            // saved, with nothing to maintain.
            'manufacturers' => $this->Abom_model->manufacturers(),
            'statuses'      => array('draft', 'submitted', 'checked', 'eng_approved',
                                     'approved', 'rejected', 'superseded'),
            // The view hides the Delete button outside these states.
            // Abom::delete_bom() enforces the same rule server-side —
            // this only decides what is offered.
            'deletable_status' => $deletable,
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

        if (!$this->require_any_perm()) {
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

    // -----------------------------------------------------------------
    // REFERENCE-BOM IMPORT
    //
    // Three steps, and the middle one is the whole point:
    //
    //   import()         the form — the file, and the machine it is for
    //   import_preview() parse, validate, infer, SHOW. Writes nothing.
    //   import_commit()  re-reads the staged file and writes, once.
    //
    // Master data is the catalogue every future BOM is generated from.
    // A wrong quantity rule here is silently wrong on every machine of
    // that type from now on, so nothing is written until a person has
    // seen exactly what would be created and pressed confirm.
    //
    // Gated on 'master_edit', like every other master-data screen. This
    // is the most consequential write in the module and must not be
    // reachable by a permission that only allows generating BOMs.
    // -----------------------------------------------------------------

    /**
     * The upload form.
     */
    public function import()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->render_import_form(array(), array());
    }

    /**
     * Parse, validate, infer — and SHOW. Nothing is written here.
     */
    public function import_preview()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->load->model('Abom_import_model');

        $meta   = $this->import_meta_from_input();
        $errors = $this->validate_import_meta($meta);

        $staged = $this->stage_upload($errors);

        if (!empty($errors)) {
            $this->render_import_form($meta, $errors);
            return;
        }

        $parsed = $this->Abom_import_model->parse($staged['path']);

        if (empty($parsed['ok'])) {
            @unlink($staged['path']);
            $this->render_import_form($meta, array('file' => $parsed['error']));
            return;
        }

        $analysis = $this->Abom_import_model->analyse($parsed['rows'], $meta);

        $this->load->view('abom/import_preview', array(
            'meta'     => $meta,
            'parsed'   => $parsed,
            'analysis' => $analysis,
            'token'    => $staged['token'],
            'filename' => $staged['name'],
            'formulas' => $this->Abom_master_model->get_formulas(),
            'families' => $this->Abom_master_model->get_families(),
        ));
    }

    /**
     * Write it. Once.
     */
    public function import_commit()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $this->load->model('Abom_import_model');

        $meta  = $this->import_meta_from_input();
        $token = trim((string) $this->input->post('token'));
        $path  = $this->staged_path($token);

        if ($path === null || !is_readable($path)) {
            $this->render_import_form($meta, array('file' =>
                'The uploaded file is no longer available — it may have expired. '
                . 'Upload it again. Nothing has been written.'));
            return;
        }

        // RE-PARSED from the same bytes rather than trusting what the
        // browser posted back. The preview is a picture of the file; the
        // import must come from the file itself, or a tampered or stale
        // form could write rows nobody reviewed.
        $parsed = $this->Abom_import_model->parse($path);

        if (empty($parsed['ok'])) {
            $this->render_import_form($meta, array('file' => $parsed['error']));
            return;
        }

        $analysis = $this->Abom_import_model->analyse($parsed['rows'], $meta);
        $items    = $this->apply_import_overrides($analysis['items']);

        if (!empty($analysis['errors'])) {
            $this->load->view('abom/import_preview', array(
                'meta' => $meta, 'parsed' => $parsed, 'analysis' => $analysis,
                'token' => $token, 'filename' => basename($path),
                'formulas' => $this->Abom_master_model->get_formulas(),
                'families' => $this->Abom_master_model->get_families(),
                'commit_error' => 'The sheet still has errors that stop the import.',
            ));
            return;
        }

        $result = $this->Abom_import_model->commit($items, $meta, $this->current_user_id());

        if (empty($result['ok'])) {
            $this->load->view('abom/import_preview', array(
                'meta' => $meta, 'parsed' => $parsed, 'analysis' => $analysis,
                'token' => $token, 'filename' => basename($path),
                'formulas' => $this->Abom_master_model->get_formulas(),
                'families' => $this->Abom_master_model->get_families(),
                'commit_error' => $result['error'],
            ));
            return;
        }

        @unlink($path);

        $this->session->set_flashdata('abom_master_message',
            $meta['code'] . ' imported from ' . $meta['source_df'] . ' — '
            . $result['items'] . ' items'
            . ($result['skipped'] > 0
                ? ', ' . $result['skipped'] . ' row(s) skipped because of errors' : '')
            . '. Open the sheet to check it before generating from it.');

        redirect(page_url . 'abom/master_bom/' . (int) $result['variant_id']);
    }

    /**
     * A blank sheet in exactly the layout the importer reads.
     *
     * Offered because "why was my file rejected" is a much worse
     * conversation than "here is the shape it needs to be". CSV rather
     * than xlsx so it opens anywhere and carries no formatting to
     * mislead.
     */
    public function import_template()
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_perm('master_edit')) { return; }

        $rows = array(
            array('S.NO.', 'ERP CODE', 'DESCRIPTION', 'MODEL NO. / PART NO.',
                  'MANUFACTURER', 'QTY.', 'UOM', 'REMARKS', 'STATUS'),
            array('', '', 'PLC, I/O\'s, HMI, RTD, VFD', '', '', '', '', '',
                  '<- a section heading: description only, no part or qty'),
            array('1', '4140023', 'PROGRAMMABLE CONTROLLER CPU MODULE', 'R04ENCPU',
                  'MITSUBISHI', '1', 'NO(S)', '', ''),
            array('2', '2020122', '4 CH. TEMPERATURE CARD', 'FX5-4LC',
                  'MITSUBISHI', '7', 'NO(S)', 'one per 4 thermocouple channels', ''),
            array('', '', 'Servo Amplifiers & Motors', '', '', '', '', '', ''),
            array('3', '4060524', 'SERVO AMPLIFIER 0.75 KW', 'MR-JE-70A',
                  'MITSUBISHI', '6', 'NO(S)', '', ''),
            array('4', '', 'SERVO MOTOR 0.75 KW', 'HG-SN52J',
                  'MITSUBISHI', '6', 'NO(S)', 'ERP code pending', 'REVIEW'),
        );

        $body = '';
        foreach ($rows as $row) {
            $out = array();
            foreach ($row as $cell) {
                $out[] = '"' . str_replace('"', '""', $cell) . '"';
            }
            $body .= implode(',', $out) . "\r\n";
        }

        $this->stream_file('abom-reference-bom-template.csv', 'text/csv',
            "\xEF\xBB\xBF" . $body);
    }

    // -----------------------------------------------------------------
    // IMPORT HELPERS
    // -----------------------------------------------------------------

    /**
     * @param  array $meta
     * @param  array $errors
     */
    private function render_import_form(array $meta, array $errors)
    {
        $this->load->view('abom/import_form', array(
            'meta'     => $meta,
            'errors'   => $errors,
            'families' => $this->Abom_master_model->get_families(),
            'models'   => $this->abom('abom_models'),
            'sides'    => $this->abom('abom_sides'),
            'motions'  => $this->abom('abom_motion_types'),
            'variants' => $this->Abom_master_model->get_variants(),
        ));
    }

    /**
     * The build being described, from the form.
     *
     * @return array
     */
    private function import_meta_from_input()
    {
        $get = function ($key, $default = '') {
            $v = $this->input->post($key);

            return ($v === null) ? $default : trim((string) $v);
        };

        return array(
            'code'             => strtoupper($get('code')),
            'name'             => $get('name'),
            'description'      => $get('description'),
            'source_df'        => strtoupper($get('source_df')),
            'plc_family_id'    => (int) $get('plc_family_id'),
            'machine_model'    => $get('machine_model'),
            'machine_side'     => $get('machine_side', 'N/A'),
            'axes'             => (int) $get('axes'),
            'tracks'           => (int) $get('tracks'),
            'speed_ppm'        => (int) $get('speed_ppm'),
            'motion_type'      => $get('motion_type', 'Intermittent'),
            'j4_units'         => (int) $get('j4_units'),
            'battery_qty'      => (int) $get('battery_qty'),
            'panel_location'   => $get('panel_location'),
            'rule_min_axes'    => $get('rule_min_axes'),
            'rule_max_axes'    => $get('rule_max_axes'),
            'rule_min_speed'   => $get('rule_min_speed'),
            'rule_max_speed'   => $get('rule_max_speed'),
            'rule_motion'      => $get('rule_motion', 'ANY'),
            'rule_explanation' => $get('rule_explanation'),
        );
    }

    /**
     * Everything that must be right before a sheet is even opened.
     *
     * The MACHINE matters as much as the file: every quantity rule is
     * inferred by testing the sheet's numbers against what each rule
     * would give for this machine. A wrong track count here produces a
     * wrong catalogue from a correct spreadsheet.
     *
     * @param  array $meta
     * @return array  field => message
     */
    private function validate_import_meta(array $meta)
    {
        $errors = array();

        if ($meta['code'] === '') {
            $errors['code'] = 'Give the build a short code, e.g. FX5-1808.';
        } elseif (!preg_match('/^[A-Z0-9][A-Z0-9\-_.]{1,23}$/', $meta['code'])) {
            $errors['code'] = 'Letters, digits, hyphen, underscore and dot only, 2 to 24 characters.';
        } else {
            $taken = $this->Abom_master_model->get_variants();
            foreach ($taken as $v) {
                if (strcasecmp($v->code, $meta['code']) === 0) {
                    $errors['code'] = 'Build code ' . $meta['code'] . ' already exists ('
                        . $v->name . '). An import never overwrites an existing build.';
                    break;
                }
            }
        }

        if ($meta['name'] === '') {
            $errors['name'] = 'Give the build a name your team will recognise.';
        }

        if ($meta['source_df'] === '') {
            $errors['source_df'] = 'Which DF is this sheet from? It is the provenance of '
                . 'every item imported.';
        }

        $families = $this->Abom_master_model->get_families();
        if (!isset($families[$meta['plc_family_id']])) {
            $errors['plc_family_id'] = 'Choose the PLC family.';
        }

        if (!in_array($meta['machine_model'], $this->abom('abom_models'), true)) {
            $errors['machine_model'] = 'Choose the machine model.';
        }

        if (!in_array($meta['machine_side'], $this->abom('abom_sides'), true)) {
            $errors['machine_side'] = 'Unknown side.';
        }

        if (!in_array($meta['motion_type'], $this->abom('abom_motion_types'), true)) {
            $errors['motion_type'] = 'Unknown motion type.';
        }

        foreach (array(
            'axes'      => array($this->abom('abom_axes_min'),   $this->abom('abom_axes_max'),   'Axes'),
            'tracks'    => array($this->abom('abom_tracks_min'), $this->abom('abom_tracks_max'), 'Tracks'),
            'speed_ppm' => array($this->abom('abom_speed_min'),  $this->abom('abom_speed_max'),  'Speed'),
        ) as $field => $spec) {
            list($min, $max, $label) = $spec;
            $v = (int) $meta[$field];

            if ($v < (int) $min || $v > (int) $max) {
                $errors[$field] = $label . ' must be between ' . (int) $min
                    . ' and ' . (int) $max . '. Every quantity rule is worked out '
                    . 'against this machine, so it has to be the machine the sheet is for.';
            }
        }

        foreach (array('j4_units' => 'MR-J4 units', 'battery_qty' => 'Battery quantity') as $f => $l) {
            if ((int) $meta[$f] < 0 || (int) $meta[$f] > 99) {
                $errors[$f] = $l . ' must be between 0 and 99.';
            }
        }

        return $errors;
    }

    /**
     * Moves the upload somewhere it will survive between preview and
     * confirm, and hands back a token for it.
     *
     * The staged file is keyed by a random token held in the session,
     * NOT by a path the browser sends: a path from the browser is a
     * path an attacker chooses, and this method opens whatever it is
     * given.
     *
     * @param  array $errors  appended to on failure
     * @return array  ['path'=>string,'token'=>string,'name'=>string]
     */
    private function stage_upload(array &$errors)
    {
        $blank = array('path' => '', 'token' => '', 'name' => '');

        if (empty($_FILES['sheet']['name'])) {
            $errors['file'] = 'Choose the spreadsheet to import.';

            return $blank;
        }

        if (!empty($_FILES['sheet']['error'])) {
            $errors['file'] = 'The file did not upload correctly (error code '
                . (int) $_FILES['sheet']['error'] . '). It may be larger than the '
                . 'server allows.';

            return $blank;
        }

        $name = (string) $_FILES['sheet']['name'];
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (!in_array($ext, array('xlsx', 'xls', 'csv'), true)) {
            $errors['file'] = 'Only .xlsx, .xls and .csv files can be imported. '
                . 'That one is a .' . $ext . '.';

            return $blank;
        }

        $dir = FCPATH . 'exported_files/abom_import';

        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
            $errors['file'] = 'The server has nowhere to stage the upload. '
                . 'Create a writable exported_files/abom_import directory.';

            return $blank;
        }

        // The stored name is generated, never taken from the upload —
        // a filename is attacker-controlled input.
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $path  = $dir . '/' . $token . '.' . $ext;

        if (!@move_uploaded_file($_FILES['sheet']['tmp_name'], $path)) {
            $errors['file'] = 'The upload could not be saved on the server.';

            return $blank;
        }

        $this->session->set_userdata('abom_import_' . $token, $path);

        return array('path' => $path, 'token' => $token, 'name' => $name);
    }

    /**
     * The staged file for a token, or null.
     *
     * Resolved through the SESSION, so a token from the form can only
     * ever reach a file this same user uploaded.
     *
     * @param  string $token
     * @return string|null
     */
    private function staged_path($token)
    {
        if (!preg_match('/^[a-f0-9]{32}$/', (string) $token)) {
            return null;
        }

        $path = $this->session->userdata('abom_import_' . $token);

        if (!$path || !is_string($path)) {
            return null;
        }

        // Belt and braces: it must still be inside the staging
        // directory, whatever the session says.
        $dir = realpath(FCPATH . 'exported_files/abom_import');
        $real = realpath($path);

        if ($dir === false || $real === false || strpos($real, $dir) !== 0) {
            return null;
        }

        return $real;
    }

    /**
     * Applies the operator's per-row decisions from the preview.
     *
     * Only the quantity formula, the optional flag and the feature gate
     * can be changed. Descriptions, part numbers, ERP codes and
     * quantities come from the sheet and from nowhere else — letting the
     * browser rewrite those would mean the imported catalogue no longer
     * matched the document it claims to be from.
     *
     * @param  array $items
     * @return array
     */
    private function apply_import_overrides(array $items)
    {
        $formulas = $this->input->post('formula');
        $optional = $this->input->post('optional');
        $feature  = $this->input->post('feature');
        $skip     = $this->input->post('skip');

        $valid = array();
        foreach ($this->Abom_master_model->get_formulas() as $f) {
            $valid[] = $f->code;
        }

        $out = array();

        foreach ($items as $item) {
            $row = (string) $item['excel_row'];

            if (is_array($skip) && !empty($skip[$row])) {
                // Deliberately left out. Recorded as an error so commit()
                // skips it by the same route a broken row takes.
                $item['errors'][] = 'Excluded by the operator at preview.';
                $out[] = $item;
                continue;
            }

            if (is_array($formulas) && isset($formulas[$row])
                && in_array($formulas[$row], $valid, true)) {
                $item['formula'] = $formulas[$row];
            }

            $item['is_optional'] = (is_array($optional) && !empty($optional[$row])) ? 1 : 0;
            $item['feature_code'] = (is_array($feature) && !empty($feature[$row]))
                ? trim((string) $feature[$row]) : null;

            $out[] = $item;
        }

        return $out;
    }

    /**
     * VERSION HISTORY — every revision of one document, what each
     * contained, and what changed between them.
     *
     * abom_bom_revision has been collecting full snapshots since the
     * module shipped and nothing ever read them. This is the screen
     * that reads them.
     *
     * Opened from ANY revision and shows the whole chain, because
     * "what changed" is a question about the document, not about the
     * row somebody happens to have open.
     *
     * A READ. Gated on any of the three permissions, like view and
     * export — a checker who can see a BOM can see how it got that way,
     * and hiding the history from the people asked to approve it would
     * be the wrong way round.
     *
     * @param int $bom_id  any revision of the document
     */
    public function history($bom_id = 0)
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_any_perm()) { return; }

        $this->load->model('Abom_model');
        $this->load->model('Abom_revision_model');

        $bom = $this->Abom_model->get_bom((int) $bom_id);

        if (!$bom) {
            show_404();
            return;
        }

        $chain = $this->Abom_revision_model->chain($bom->bom_no);

        if (empty($chain)) {
            $chain = array($bom);
        }

        $ids = array();
        foreach ($chain as $row) { $ids[] = (int) $row->id; }

        $archived = $this->Abom_revision_model->snapshots($ids);
        $audit    = $this->Abom_revision_model->audit_for($ids);

        // Snapshots by the BOM row they belong to. A row can carry more
        // than one — approved, then later superseded — and the LAST is
        // the one that describes what that revision finally contained.
        $by_bom = array();
        foreach ($archived as $snap) {
            $by_bom[(int) $snap->bom_id][] = $snap;
        }

        // Walk the chain oldest first, diffing each version against the
        // one before it. The newest revision has no archived snapshot
        // yet — it is still being prepared — so its CURRENT state is
        // used, which is what anyone asking about it means.
        $versions = array();
        $previous = null;

        foreach ($chain as $row) {
            $id    = (int) $row->id;
            $snaps = isset($by_bom[$id]) ? $by_bom[$id] : array();

            if (!empty($snaps)) {
                $last = $snaps[count($snaps) - 1];
                $data = $this->Abom_revision_model->snapshot_data((int) $last->id);
                $from = 'archived';
                $ref  = (int) $last->id;
            } else {
                $data = $this->Abom_revision_model->live_snapshot($id);
                $from = 'live';
                $ref  = 0;
            }

            if ($data === null) {
                // A snapshot that will not decode. Said out loud rather
                // than skipped: a version silently missing from a
                // history is worse than one marked unreadable.
                $versions[] = array(
                    'bom' => $row, 'source' => 'broken', 'ref' => 0,
                    'diff' => null, 'lines' => 0, 'snapshots' => $snaps,
                    'audit' => isset($audit[$id]) ? $audit[$id] : array(),
                );
                continue;
            }

            $versions[] = array(
                'bom'       => $row,
                'source'    => $from,
                'ref'       => $ref,
                'lines'     => count($data['lines']),
                'diff'      => $this->Abom_revision_model->diff($previous, $data),
                'snapshots' => $snaps,
                'audit'     => isset($audit[$id]) ? $audit[$id] : array(),
            );

            $previous = $data;
        }

        // Newest first on screen. Built oldest first above because each
        // diff needs the version before it.
        $versions = array_reverse($versions);

        $this->load->view('abom/history', array(
            'bom'       => $bom,
            'versions'  => $versions,
            'user_names'=> $this->user_names_for($versions),
        ));
    }

    /**
     * One ARCHIVED version, rendered as the document it was.
     *
     * Read-only without qualification — not "locked because of its
     * status" but genuinely a photograph of a document that no longer
     * exists in this form. Nothing on it can be edited, exported or
     * advanced, and it carries a banner saying so, because a printed
     * page from here would otherwise be indistinguishable from a
     * current BOM.
     *
     * @param int $revision_id  abom_bom_revision.id
     */
    public function version($revision_id = 0)
    {
        if (!$this->require_tables()) { return; }
        if (!$this->require_any_perm()) { return; }

        $this->load->model('Abom_revision_model');
        $this->load->library('Abom_engine', null, 'abom_engine');

        $data = $this->Abom_revision_model->snapshot_data((int) $revision_id);

        if ($data === null) {
            show_404();
            return;
        }

        $bom = (object) $data['header'];

        $lines = array();
        $n     = 0;
        foreach ($data['lines'] as $row) {
            $line = (object) $row;
            $n++;
            // line_no is re-derived rather than trusted: a snapshot
            // taken mid-edit can carry gaps, and a printed sheet that
            // jumps from 12 to 14 reads as a missing part.
            $line->line_no       = $n;
            $line->row_class     = $this->abom_engine->row_class($line);
            $line->status_badges = $this->abom_engine->status_badges($line);
            $lines[] = $line;
        }

        $this->render_document($bom, $lines, false, null, array(),
            $this->archived_notice($data['row'], $bom), true);
    }

    /**
     * The banner on an archived version.
     *
     * @param  object $row
     * @param  object $bom
     * @return string
     */
    private function archived_notice($row, $bom)
    {
        $when = !empty($row->created_at)
            ? date('d-m-Y H:i', strtotime($row->created_at)) : 'an unrecorded date';

        return '<b>&#128444;&#65039; Archived version.</b> This is '
            . abom_e($bom->bom_no) . ' as it stood at REV.' . abom_e($row->revision)
            . ', captured on ' . abom_e($when)
            . (!empty($row->change_note) ? ' — ' . abom_e($row->change_note) : '')
            . '. It is a record, not the current document: nothing here can be edited, '
            . 'and it must not be issued. '
            . '<a href="' . page_url . 'abom/history/' . (int) $row->bom_id . '">Back to the version history</a>.';
    }

    /**
     * Display names for everyone who appears in a version history.
     *
     * Resolved in ONE pass rather than per row: a chain of ten
     * revisions with a dozen audit entries each would otherwise run
     * a hundred identical lookups to print the same four names.
     *
     * @param  array $versions
     * @return array  user_id => name
     */
    private function user_names_for(array $versions)
    {
        $ids = array();

        foreach ($versions as $v) {
            foreach (array('created_by', 'prepared_by', 'checked_by',
                           'eng_approved_by', 'proc_approved_by') as $f) {
                if (!empty($v['bom']->$f)) { $ids[(int) $v['bom']->$f] = true; }
            }
            foreach ($v['snapshots'] as $s) {
                if (!empty($s->created_by)) { $ids[(int) $s->created_by] = true; }
            }
            foreach ($v['audit'] as $a) {
                if (!empty($a->user_id)) { $ids[(int) $a->user_id] = true; }
            }
        }

        if (empty($ids)) {
            return array();
        }

        $this->load->model('User_model');

        $out = array();
        foreach (array_keys($ids) as $id) {
            $name = $this->lookup_user_name($id);
            if ($name !== '') { $out[$id] = $name; }
        }

        return $out;
    }

    /**
     * One user's display name, or '' when it cannot be resolved.
     *
     * Wrapped so a host-application schema change cannot take the
     * history screen down with it — a missing name is a cosmetic loss,
     * and the id is shown instead.
     *
     * @param  int $user_id
     * @return string
     */
    private function lookup_user_name($user_id)
    {
        $row = $this->db->select('first_name, last_name')
            ->from('system_users')
            ->where('user_id', (int) $user_id)
            ->limit(1)
            ->get()
            ->row();

        if (!$row) {
            return '';
        }

        return trim($row->first_name . ' ' . $row->last_name);
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
    public function reference($key = 'df1826')
    {
        if (!$this->require_tables()) {
            return;
        }

        if (!$this->require_any_perm()) {
            return;
        }

        $presets = $this->reference_presets();

        // 'fx5' and 'iqr' were the two keys before the reference set grew
        // to nine. Old links and bookmarks still resolve.
        $aliases = array('fx5' => 'df1827', 'iqr' => 'df1826');
        if (isset($aliases[$key])) {
            $key = $aliases[$key];
        }

        $key    = isset($presets[$key]) ? $key : 'df1826';
        $preset = $presets[$key];

        $this->load->library('Abom_engine', null, 'abom_engine');
        $result = $this->abom_engine->generate($preset['cfg']);

        $bom = $this->bom_header_from_config($preset['cfg'], $result);
        $bom->bom_no = $preset['bom_no'];
        $bom->df_ref = $preset['df_ref'];
        $bom->revision = $preset['revision'];

        $this->render_document($bom, $result['lines'], false, $result);
    }

    /**
     * User guide / training screen.
     *
     * Everything factual on this page is READ FROM THE DATABASE, not
     * written into the view: the formula list, the PLC selection rules,
     * the feature switches and the per-formula item counts all come from
     * the same tables the engine uses. A guide that restates the rules in
     * prose drifts the first time someone edits abom_plc_rule, and a
     * training document that is quietly wrong is worse than none.
     *
     * The worked examples are generated live by Abom_engine for the same
     * reason — the two reference numbers a trainee is told to expect are
     * produced by the engine as the page renders, so they cannot be
     * stale.
     */
    public function guide()
    {
        if (!$this->require_tables()) {
            return;
        }

        if (!$this->require_any_perm()) {
            return;
        }

        $this->load->model('Abom_master_model');
        $this->load->model('Abom_approval_model');
        $this->load->library('Abom_engine', null, 'abom_engine');

        $data = array(
            'formulas' => $this->Abom_master_model->formulas_with_usage(),
            'rules'    => $this->Abom_master_model->plc_rules_readable(),
            'variants' => $this->Abom_master_model->variants_readable(),
            'features' => $this->Abom_master_model->features_with_usage(),
            'families' => $this->Abom_master_model->families(),
            'stages'   => $this->Abom_approval_model->stage_ladder(),
            'examples' => array(),
        );

        // Worked examples, computed now — not transcribed.
        //
        // generate() returns 'family' as an ARRAY from detect_plc_family()
        // (family_id / code / rule_id / priority / explanation), not as a
        // family row. The readable name comes from the family record.
        foreach ($this->reference_presets() as $key => $preset) {
            $result = $this->abom_engine->generate($preset['cfg']);

            $family_name = '';
            $fam = $this->Abom_master_model->get_family((int) $result['family_id']);
            if ($fam) {
                $family_name = $fam->name;
            }

            $data['examples'][$key] = array(
                'label'   => $preset['bom_no'] . ' — ' . $preset['df_ref'],
                'cfg'     => $preset['cfg'],
                'lines'   => count($result['lines']),
                'qty'     => array_sum(array_map(function ($l) {
                    return (int) $l->qty;
                }, $result['lines'])),
                'family'  => $family_name,
                'why'     => isset($result['family']['explanation'])
                    ? $result['family']['explanation'] : '',
                'variant' => !empty($result['variant']) ? $result['variant']->code : '—',
                'why_var' => !empty($result['variant_detected'])
                    ? $result['variant_detected']['explanation'] : '',
                // The temperature card count for this configuration,
                // read off the generated lines rather than restated. If
                // TRACK_TEMP ever stops agreeing with the source BOMs,
                // this column is where a trainee sees it first.
                'temp_cards' => $this->temp_card_qty($result['lines']),
            );
        }

        $this->load->view('abom/guide', $data);
    }

    /**
     * Total quantity of TRACK_TEMP lines on a generated set — the
     * 4-channel temperature / RTD card count. Summed rather than taken
     * from the first match because a build can carry more than one such
     * card (the AUTONICS alternative is a second TRACK_TEMP line).
     *
     * @param  array $lines
     * @return int
     */
    private function temp_card_qty($lines)
    {
        $total = 0;

        foreach ($lines as $line) {
            if ($line->formula_code === 'TRACK_TEMP') {
                $total += (int) $line->qty;
            }
        }

        return $total;
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
    private function render_document($bom, $lines, $editable, $result = null,
                                     $errors = array(), $notice = '', $archived = false)
    {
        $this->load->library('Abom_engine', null, 'abom_engine');

        $family_code = $this->Abom_master_model->family_code((int) $bom->plc_family_id);

        // Panel location and the J4/battery defaults come from the
        // VARIANT, falling back to the family. DF-1770 and DF-1826 are
        // both iQ-R but one sits in a panel with the machine on 9/9 and
        // the other is standalone on 11/12 — reading the family would put
        // one BOM on the other's numbers.
        $variant_id = isset($bom->variant_id) ? $bom->variant_id : null;
        $defaults   = $this->Abom_master_model->variant_defaults(
            $variant_id, (int) $bom->plc_family_id
        );

        $variant = ($result !== null && isset($result['variant']))
            ? $result['variant']
            : $this->Abom_master_model->get_variant($variant_id);

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
        // Re-specifying a saved BOM — the point of duplicate(). Draft
        // and rejected only; a sheet under review or released is
        // duplicated or revised rather than changed underneath its
        // reviewer. Never on the generator, whose fields $editable
        // already governs and whose Save creates a new document.
        $config_editable = !$editable
            && !empty($bom->id)
            && abom_config_editable($bom)
            && $this->has_perm('save');

        if ($archived) {
            // A record cannot be edited, and offering the controls
            // would invite somebody to try and then wonder why nothing
            // saved. There is no row to write to: a snapshot is JSON.
            $config_editable = false;
        }

        $qty_editable = !$archived && ($editable
            || (!empty($bom->id)
                && abom_qty_editable($bom)
                && self::QTY_OVERRIDE_PERSISTENCE_AVAILABLE));

        // ROW EDITING — insert, remove, and the typeable REMARKS column.
        //
        // On the GENERATOR it is form state until Save. On a SAVED BOM
        // it now has a real write path (save_lines(), with an audit row),
        // so it is offered wherever the workflow still allows the
        // document to change — the same rule as quantity editing, and
        // for the same reason: an edit that cannot be persisted must not
        // be offered, because a markup that vanishes on refresh is worse
        // than no markup at all.
        $rows_editable = !$archived && ($editable
            || (!empty($bom->id)
                && abom_qty_editable($bom)
                && self::QTY_OVERRIDE_PERSISTENCE_AVAILABLE));

        $data = array(
            'bom'                => $bom,
            'lines'              => $lines,
            'sections'           => $sections,
            'stats'              => $stats,
            'editable'           => (bool) $editable,
            // The MACHINE CONFIGURATION panel, unlocked on a saved BOM
            // that is still the author's to change. Not the same
            // question as $editable: this screen writes back to THIS
            // document, where the generator's Save creates a new one.
            'config_editable'    => (bool) $config_editable,
            'qty_editable'       => (bool) $qty_editable,
            'rows_editable'      => $rows_editable,
            'qty_locked_reason'  => $this->qty_locked_reason($bom, $editable),
            'presets'            => $this->reference_presets(),
            'workflow'           => $archived
                                        ? array('enabled' => false, 'status_label' => '')
                                        : $this->workflow_state($bom),
            'family_code'        => $family_code,
            'family_explanation' => ($result !== null && isset($result['family']['explanation']))
                                        ? $result['family']['explanation'] : '',
            'overridden'         => ($result !== null) ? !empty($result['overridden'])
                                        : !empty($bom->plc_family_locked),
            'variant'            => $variant,
            'variant_explanation'=> ($result !== null && !empty($result['variant_detected']))
                                        ? $result['variant_detected']['explanation'] : '',
            'variant_overridden' => ($result !== null) ? !empty($result['variant_overridden'])
                                        : !empty($bom->variant_locked),
            'variant_missing'    => ($result !== null) ? !empty($result['variant_missing']) : false,
            'variant_message'    => ($result !== null && !empty($result['variant_missing']))
                                        ? $this->variant_missing_message($this->cfg_from_bom($bom)) : '',
            'variants'           => $this->Abom_master_model->get_active_variants(),
            'panel_location'     => $defaults['panel_location'],
            'families'           => $this->Abom_master_model->get_families(),
            'features'           => $features,
            'active_features'    => $active_features,
            'errors'             => is_array($errors) ? $errors : array(),
            // One-off explanation of where this screen's starting
            // configuration came from. Empty on an ordinary visit.
            'notice'             => (string) $notice,
            // An ARCHIVED version: a photograph of a document that no
            // longer exists in this form. Suppresses every control that
            // would act on a live BOM.
            'archived'           => (bool) $archived,
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
        $bom->variant_id        = isset($result['variant_id']) ? $result['variant_id'] : null;
        $bom->variant_locked    = !empty($result['variant_overridden']) ? 1 : 0;
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

        // DF-1808 rather than the old DF-1826 default: 6 axis / 12 track
        // SPM1200L is the commonest build in the reference set, so a
        // fresh generator screen opens on something an engineer is
        // likely to be configuring rather than on the 15-axis outlier.
        return isset($presets['df1808']['cfg'])
            ? $presets['df1808']['cfg']
            : reset($presets)['cfg'];
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

        // A variant override is NOT carried over from $base. The base is
        // a reference configuration whose variant was right for ITS axis
        // and track counts; carrying it into a differently configured
        // machine would silently pin the wrong build. Absent means
        // "auto-detect", which is what the picker's blank option posts.
        $variant = $this->input->post('variant_id');
        $cfg['variant_id'] = ($variant !== null && $variant !== '') ? (int) $variant : null;

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
     * The nine source BOMs, one preset each. On the generator screen
     * these are offered as CONFIGURATION presets — they fill the panel
     * and the engine then generates from those inputs like any other
     * configuration. They are NOT data loaders: nothing here selects a
     * variant or a quantity, so a preset that stops reproducing its
     * source BOM is telling you the rules have drifted.
     *
     * Track counts 6 / 8 / 9 / 12 and axis counts 5 / 6 / 7 / 8 / 10 / 11
     * / 15 are all represented, which is what makes them useful as a live
     * regression check on the track-driven temperature card rule.
     *
     * feat_autonics_temp and feat_io32 default OFF and are left off here;
     * DF-1858 is the build that wants them, and its preset turns them on.
     *
     * @return array
     */
    private function reference_presets()
    {
        $features = $this->Abom_master_model->default_features();

        // DF-1858 needed feature overrides while it shared a catalogue
        // with four other machines. It is its own build now (abom_011)
        // and simply HAS the AUTONICS card and the combined I/O card, so
        // the only switch left that means anything to it is perforation,
        // which it does not fit.
        $features_1858 = array_merge($features, array('feat_perf' => 0));

        $base = function (array $over) use ($features) {
            return array_merge(array(
                'axes' => 6, 'tracks' => 12, 'speed_ppm' => 80,
                'motion_type' => 'Intermittent', 'machine_model' => 'SPM1200L',
                'machine_side' => 'N/A', 'j4_units' => 0, 'battery_qty' => 6,
                'plc_family_id' => null, 'variant_id' => null,
                'features' => $features,
            ), $over);
        };

        return array(
            'df1858' => array(
                'bom_no'   => 'ABOM-REF-1858',
                'df_ref'   => 'DF-1858',
                'revision' => '',
                'title'    => 'DF-1858',
                'summary'  => 'SPM1200L · 5 Axis · 6 Track · 70 PPM · LHS',
                'panel'    => 'FX5-1858 · smallest build · 20 items',
                'tags'     => array(array('fx5', 'FX5'), array('int', 'Intermittent')),
                'cfg'      => $base(array(
                    'axes' => 5, 'tracks' => 6, 'speed_ppm' => 70,
                    'machine_side' => 'LHS', 'battery_qty' => 5,
                    'features' => $features_1858,
                )),
            ),
            'df1723' => array(
                'bom_no'   => 'ABOM-REF-1723',
                'df_ref'   => 'DF-1723',
                'revision' => '',
                'title'    => 'DF-1723',
                'summary'  => 'SPM1200L · 5 Axis · 12 Track · 100 PPM',
                'panel'    => 'FX5-1723 · 25 items',
                'tags'     => array(array('fx5', 'FX5'), array('int', 'Intermittent')),
                'cfg'      => $base(array(
                    'axes' => 5, 'tracks' => 12, 'speed_ppm' => 100, 'battery_qty' => 5,
                )),
            ),
            'df1808' => array(
                'bom_no'   => 'ABOM-REF-1808',
                'df_ref'   => 'DF-1808',
                'revision' => '',
                'title'    => 'DF-1808',
                'summary'  => 'SPM1200L · 6 Axis · 12 Track · 100 PPM',
                'panel'    => 'FX5-1808 · 29 items',
                'tags'     => array(array('fx5', 'FX5'), array('int', 'Intermittent')),
                'cfg'      => $base(array(
                    'axes' => 6, 'tracks' => 12, 'speed_ppm' => 100, 'battery_qty' => 6,
                )),
            ),
            'df1864' => array(
                'bom_no'   => 'ABOM-REF-1864',
                'df_ref'   => 'DF-1864',
                'revision' => '',
                'title'    => 'DF-1864',
                'summary'  => 'SPM1200L · 6 Axis · 12 Track · 80 PPM · LHS',
                'panel'    => 'FX5-1864 · case packer · 28 items',
                'tags'     => array(array('fx5', 'FX5'), array('int', 'Intermittent')),
                'cfg'      => $base(array(
                    'axes' => 6, 'tracks' => 12, 'speed_ppm' => 80,
                    'machine_side' => 'LHS', 'battery_qty' => 6,
                )),
            ),
            'df1778' => array(
                'bom_no'   => 'ABOM-REF-1778',
                'df_ref'   => 'DF-1778',
                'revision' => '',
                'title'    => 'DF-1778',
                'summary'  => 'SPM1200L · 7 Axis · 8 Track · 80 PPM · LHS',
                'panel'    => 'FX5-1778 · traverse axis · 29 items',
                'tags'     => array(array('fx5', 'FX5'), array('int', 'Intermittent')),
                'cfg'      => $base(array(
                    'axes' => 7, 'tracks' => 8, 'speed_ppm' => 80,
                    'machine_side' => 'LHS', 'battery_qty' => 7,
                )),
            ),
            'df1827' => array(
                'bom_no'   => 'ABOM-REF-1827',
                'df_ref'   => 'DF-1827',
                'revision' => '',
                'title'    => 'DF-1827',
                'summary'  => 'SPM1200L · 8 Axis · 12 Track · 140 PPM · LHS',
                'panel'    => 'FX5-J4 · expect 29 line items',
                'tags'     => array(array('fx5', 'FX5'), array('int', 'Intermittent')),
                'cfg'      => $base(array(
                    'axes' => 8, 'tracks' => 12, 'speed_ppm' => 140,
                    'machine_side' => 'LHS', 'j4_units' => 7, 'battery_qty' => 7,
                )),
            ),
            'df1770' => array(
                'bom_no'   => 'ABOM-REF-1770',
                'df_ref'   => 'DF-1770',
                'revision' => '',
                'title'    => 'DF-1770',
                'summary'  => 'SPM1200L · 10 Axis · 12 Track · 140 PPM · LHS',
                'panel'    => 'IQR-STD · panel with machine',
                'tags'     => array(array('iqr', 'iQ-R'), array('int', 'Intermittent')),
                'cfg'      => $base(array(
                    'axes' => 10, 'tracks' => 12, 'speed_ppm' => 140,
                    'machine_side' => 'LHS', 'j4_units' => 9, 'battery_qty' => 9,
                )),
            ),
            'df1826' => array(
                'bom_no'   => 'ABOM-REF-1826',
                'df_ref'   => 'DF-1826',
                'revision' => '02',
                'title'    => 'DF-1826 · REV.02',
                'summary'  => 'SPM1200L · 15 Axis · 12 Track · 180 PPM',
                'panel'    => 'IQR-HS · standalone · expect 42 line items',
                'tags'     => array(array('iqr', 'iQ-R'), array('cont', 'Continuous')),
                'cfg'      => $base(array(
                    'axes' => 15, 'tracks' => 12, 'speed_ppm' => 180,
                    'motion_type' => 'Continuous',
                    'j4_units' => 11, 'battery_qty' => 12,
                )),
            ),
            'df1883' => array(
                'bom_no'   => 'ABOM-REF-1883',
                'df_ref'   => 'DF-1883',
                'revision' => '',
                'title'    => 'DF-1883',
                'summary'  => 'SPM1250P · 11 Axis · 6 Track · 70 PPM',
                'panel'    => 'FX5-TCF · 11 axes on two motion cards',
                'tags'     => array(array('fx5', 'FX5'), array('int', 'Intermittent')),
                'cfg'      => $base(array(
                    'axes' => 11, 'tracks' => 6, 'speed_ppm' => 70,
                    'machine_model' => 'SPM1250P',
                    'j4_units' => 11, 'battery_qty' => 11,
                )),
            ),
            'df1805' => array(
                'bom_no'   => 'ABOM-REF-1805',
                'df_ref'   => 'DF-1805',
                'revision' => '',
                'title'    => 'DF-1805',
                'summary'  => 'SPM1250P · 11 Axis · 6 Track · 120 PPM',
                'panel'    => 'IQR-TCF · tilting cup filling · standalone',
                'tags'     => array(array('iqr', 'iQ-R'), array('cont', 'Continuous')),
                'cfg'      => $base(array(
                    'axes' => 11, 'tracks' => 6, 'speed_ppm' => 120,
                    'motion_type' => 'Continuous', 'machine_model' => 'SPM1250P',
                    'j4_units' => 13, 'battery_qty' => 13,
                )),
            ),
            'df1855' => array(
                'bom_no'   => 'ABOM-REF-1855',
                'df_ref'   => 'DF-1855',
                'revision' => '',
                'title'    => 'DF-1855',
                'summary'  => 'SPM1250P · 15 Axis · 9 Track · 80 PPM · RHS',
                'panel'    => 'IQR-FLM · flow meter · temperature cards = 6',
                'tags'     => array(array('iqr', 'iQ-R'), array('cont', 'Continuous')),
                'cfg'      => $base(array(
                    'axes' => 15, 'tracks' => 9, 'speed_ppm' => 80,
                    'motion_type' => 'Continuous', 'machine_model' => 'SPM1250P',
                    'machine_side' => 'RHS', 'j4_units' => 15, 'battery_qty' => 15,
                )),
            ),
        );
    }

    /**
     * Applies the engineer's row edits to the freshly generated lines.
     *
     * THE ENGINE REMAINS THE AUTHORITY. For a generated row the client
     * may say three things and no more: keep it or drop it, what order
     * it sits in, and what quantity and remark to carry. Description,
     * part number, ERP code, manufacturer, formula, severity and
     * computed_qty are all taken from the engine's output and never from
     * the request — otherwise a crafted POST could put any text it liked
     * onto a document that carries an approval signature.
     *
     * A row the client did not send is a row the engineer removed.
     *
     * NOTHING HERE WRITES TO MASTER DATA. abom_item is not touched by
     * this path or by anything it calls; every edit lands on
     * abom_bom_line, which is a per-BOM frozen snapshot. Adding a row to
     * one BOM cannot change what the next one generates, and cannot
     * alter the nine reference builds.
     *
     * Falls through untouched when no row list is posted, so the
     * pre-existing save path (and any client without JavaScript) still
     * behaves exactly as before.
     *
     * @param  array $lines  engine output
     * @return array
     */
    private function apply_row_edits(array $lines)
    {
        $raw = $this->input->post('rows');

        if (empty($raw)) {
            return $this->apply_legacy_qty($lines);
        }

        $posted = json_decode($raw, true);

        if (!is_array($posted) || empty($posted)) {
            return $this->apply_legacy_qty($lines);
        }

        // Generated lines, by the stable row key the table renders.
        $by_key = array();
        foreach ($lines as $line) {
            $by_key['i' . (int) $line->item_id] = $line;
        }

        $out     = array();
        $line_no = 0;
        $foreign = array();

        foreach ($posted as $row) {
            if (!is_array($row)) {
                continue;
            }

            $key    = isset($row['key']) ? (string) $row['key'] : '';
            $manual = !empty($row['manual']);

            if (!$manual) {
                // A key the engine did not just produce means the table
                // in the browser was generated from a DIFFERENT
                // configuration to the one being saved. That is a stale
                // sheet, not a set of deliberate removals.
                //
                // This is collected and rejected below rather than
                // skipped. Skipping is what the first version did, and
                // on 2026-08-11 it saved ABOM-5 with ONE line — the
                // hand-added row — because none of the 29 posted keys
                // matched the regenerated build. A BOM silently reduced
                // to a fraction of itself is the worst failure this
                // module can have: it is a purchasable document that
                // looks finished.
                //
                // A row the engineer deliberately REMOVED is simply not
                // posted at all, so it never lands here.
                if ($key === '' || !isset($by_key[$key])) {
                    $foreign[] = $key !== '' ? $key : '(no key)';
                    continue;
                }

                $line = $by_key[$key];
                unset($by_key[$key]);          // each row once only
            } else {
                $line = $this->manual_line($row);

                if ($line === null) {
                    continue;                  // wholly blank row
                }
            }

            $line_no++;
            $line->line_no = $line_no;

            // Quantity: an override only when it differs from what the
            // engine computed. A manual row has no computed value, so
            // whatever was typed IS its quantity.
            if (isset($row['qty']) && $row['qty'] !== '') {
                $qty = max(0, (int) $row['qty']);

                if ($manual) {
                    $line->qty          = $qty;
                    $line->computed_qty = $qty;
                } elseif ($qty !== (int) $line->computed_qty) {
                    $line->qty             = $qty;
                    $line->is_overridden   = 1;
                    $line->override_reason = null;
                }
            }

            if (isset($row['user_remark'])) {
                $line->user_remark = mb_substr(trim((string) $row['user_remark']), 0, 255);
            }

            $out[] = $line;
        }

        // Stale sheet — refuse the whole save. Returning what survived
        // would write a BOM the engineer never saw and never approved.
        if (!empty($foreign)) {
            $this->output->set_status_header(409);
            $this->respond(false,
                'The sheet on screen was generated from a different configuration to the one '
                . 'being saved, so ' . count($foreign) . ' of its rows no longer exist. '
                . 'Nothing has been saved. Press Recalculate, check the table, then save again.');
        }

        return $out;
    }

    /**
     * Builds one hand-added line from posted fields.
     *
     * A row where every descriptive field is blank is discarded, not
     * saved: the engineer clicked "+" and changed their mind, and an
     * empty row on a purchasable document is worse than no row.
     *
     * @param  array $row
     * @return object|null
     */
    private function manual_line(array $row)
    {
        $get = function ($key, $limit) use ($row) {
            return isset($row[$key]) ? mb_substr(trim((string) $row[$key]), 0, $limit) : '';
        };

        $description  = $get('description', 255);
        $part_no      = $get('part_no', 96);
        $erp_code     = $get('erp_code', 32);
        $manufacturer = $get('manufacturer', 64);

        if ($description === '' && $part_no === '' && $erp_code === '') {
            return null;
        }

        $qty = isset($row['qty']) ? max(0, (int) $row['qty']) : 0;

        return (object) array(
            'line_no'        => 0,
            'item_id'        => null,          // no master item — that is the point
            'section_name'   => $get('section', 128),
            'section_order'  => 99,
            'erp_code'       => $erp_code !== '' ? $erp_code : null,
            'description'    => $description !== '' ? $description : $part_no,
            'part_no'        => $part_no,
            'manufacturer'   => $manufacturer,
            'qty'            => $qty,
            'computed_qty'   => $qty,
            'is_overridden'  => 0,
            'uom'            => $get('uom', 16) !== '' ? $get('uom', 16) : $this->abom('abom_default_uom'),
            'formula_code'   => 'MANUAL',
            'variant_code'   => null,
            'usage_remark'   => null,
            'remarks'        => null,
            'user_remark'    => $get('user_remark', 255),
            // A hand-added row has no ERP code validated against
            // anything. When it also carries none, it is flagged exactly
            // as a master item with no ERP code would be.
            'issue_severity' => $erp_code === '' ? 'no_erp' : 'none',
            'is_optional'    => 0,
            'feature_code'   => null,
            'panel_location' => null,
            'is_manual_add'  => 1,
            'row_class'      => '',
            'status_badges'  => array(),
        );
    }

    /**
     * The original qty-only save path, keyed by line number.
     *
     * Kept for any caller that posts `qty[]` without `rows` — the row
     * list is a newer contract and this is what a client that predates
     * it still sends.
     *
     * @param  array $lines
     * @return array
     */
    private function apply_legacy_qty(array $lines)
    {
        $overrides = $this->input->post('qty');

        if (!is_array($overrides)) {
            return $lines;
        }

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

        return $lines;
    }

    /**
     * Why the engine produced no lines, phrased so the operator can act
     * on it. Reached when no variant rule matches the configuration —
     * which is a data gap, not a user error, so it names the numbers and
     * points at the table to fix.
     *
     * @param  array $cfg
     * @return string
     */
    private function variant_missing_message(array $cfg)
    {
        return 'No build variant matches '
            . (isset($cfg['machine_model']) ? $cfg['machine_model'] : '?') . ' · '
            . (int) $cfg['axes'] . ' axis · '
            . (int) $cfg['tracks'] . ' track · '
            . (int) $cfg['speed_ppm'] . ' PPM · '
            . (isset($cfg['motion_type']) ? $cfg['motion_type'] : '?')
            . '. No items can be generated for a machine with no build. '
            . 'Pick a build manually below, or add a selection rule to '
            . 'abom_variant_rule for this configuration.';
    }

    /**
     * The configuration a saved (or transient) BOM was generated from.
     * Used only to phrase the message above for a BOM being re-rendered.
     *
     * @param  object $bom
     * @return array
     */
    private function cfg_from_bom($bom)
    {
        return array(
            'axes'          => (int) $bom->axes,
            'tracks'        => (int) $bom->tracks,
            'speed_ppm'     => (int) $bom->speed_ppm,
            'motion_type'   => $bom->motion_type,
            'machine_model' => $bom->machine_model,
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
     * Permission check, delegated to Abom_permission_guard.
     *
     * The guard also validates that the configured submodule ids exist
     * and belong to module 4, so an unconfigured module reports itself
     * as a configuration problem rather than as an access denial.
     *
     * @param  string $action
     * @return bool
     */
    private function has_perm($action)
    {
        $this->load->library('Abom_permission_guard', null, 'abom_permission_guard');

        return $this->abom_permission_guard->allows($action);
    }

    /**
     * Gate an action. Returns FALSE when the caller may not proceed, and
     * has already emitted the response.
     *
     * Distinguishes the two failures, because they need different
     * remedies and conflating them wastes a day:
     *
     *   unconfigured -> a CONFIGURATION diagnostic naming the key and file
     *   not granted  -> an access denial, following the house convention
     *                   (Master_profile_guard::deny): flashdata plus a
     *                   redirect for a page, 403 JSON for AJAX
     *
     * Read paths (view, list, print, export, reference) are deliberately
     * NOT gated on this: an approver who holds only APPROVALS must be
     * able to open the BOM they are approving, and the spec defines no
     * view permission.
     *
     * @param  string $action  generate|save|master_edit
     * @return bool
     */
    private function require_perm($action)
    {
        if (!$this->require_permissions_configured()) {
            return false;
        }

        if ($this->has_perm($action)) {
            return true;
        }

        $message = 'You do not have permission to use the Automation BOM generator. '
                 . 'Ask an administrator to grant you access.';

        if ($this->input->is_ajax_request()) {
            $this->output->set_status_header(403);
            $this->output->set_content_type('application/json');
            echo json_encode(array('status' => 0, 'message' => $message));
            return false;
        }

        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger alert-dismissable">' . $message . '</div>'
        );
        redirect(page_url . 'Dashboard');

        return false;
    }

    /**
     * Gate a READ path on holding ANY of the module's three permissions.
     *
     * Reads cannot be gated on a single permission: an approver holding
     * only APPROVALS must be able to open the BOM they are approving, and
     * the spec defines no view permission. But "open to any logged-in
     * user" is a wider door than that argument needs — it would let every
     * account in the company list and export every automation BOM, part
     * numbers and all, as XLSX or PDF.
     *
     * Holding any one of the three is the narrowest rule that still lets
     * every legitimate reader in.
     *
     * @return bool
     */
    private function require_any_perm()
    {
        if (!$this->require_permissions_configured()) {
            return false;
        }

        foreach (array('generate', 'check', 'master_edit') as $action) {
            if ($this->has_perm($action)) {
                return true;
            }
        }

        $message = 'You do not have access to Automation BOMs. '
                 . 'Ask an administrator to grant you access.';

        if ($this->input->is_ajax_request()) {
            $this->output->set_status_header(403);
            $this->output->set_content_type('application/json');
            echo json_encode(array('status' => 0, 'message' => $message));
            return false;
        }

        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-danger alert-dismissable">' . $message . '</div>'
        );
        redirect(page_url . 'Dashboard');

        return false;
    }

    /**
     * Renders the ACL diagnostic and returns FALSE when the module's
     * permission wiring is not configured. Same spirit as
     * require_tables().
     *
     * @return bool
     */
    private function require_permissions_configured()
    {
        $this->load->library('Abom_permission_guard', null, 'abom_permission_guard');

        if ($this->abom_permission_guard->is_configured()) {
            return true;
        }

        $problems = $this->abom_permission_guard->problems();

        if ($this->input->is_ajax_request()) {
            $this->output->set_status_header(503);
            $this->output->set_content_type('application/json');
            echo json_encode(array('status' => 0,
                'message' => 'Automation BOM permissions are not configured: ' . implode(' ', $problems)));
            return false;
        }

        $this->load->view('abom/not_configured', array('problems' => $problems));

        return false;
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
