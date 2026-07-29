<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Automation BOM Generator — module configuration
 *
 * NEW FILE. Loaded explicitly with $this->config->load('abom') from the
 * module's controller and library, so application/config/autoload.php
 * does NOT need to be modified.
 *
 * PHP 7.4 compatible (production runs ea-php74).
 */

$config['abom_table_prefix']   = 'abom_';
$config['abom_default_uom']    = 'NOS';

// Own numbering series. The existing DF register (DF_revision,
// Df_change_control, Df_dispatch_plan) owns the "DF-" namespace; this
// module must not mint into it. A generated BOM may still REFERENCE a
// source DF through abom_bom.df_ref, which is free text.
$config['abom_bom_no_format']  = 'ABOM-%d';

$config['abom_models']         = array('SPM1200L', 'SPM1250P');
$config['abom_sides']          = array('LHS', 'RHS', 'N/A');
$config['abom_motion_types']   = array('Intermittent', 'Continuous');

// Guard rails for configuration input. Out-of-range values are rejected
// with a field-level message — never silently clamped.
$config['abom_axes_min']       = 1;
$config['abom_axes_max']       = 16;             // R16MTCPU ceiling
$config['abom_tracks_min']     = 1;
$config['abom_tracks_max']     = 24;
$config['abom_speed_min']      = 40;
$config['abom_speed_max']      = 400;

// Approval stages, in order. Internal workflow state — this drives
// abom_bom_approval and the on-screen status indicator. It is NOT the
// printed sign-off layout; see abom_signoff_mode below.
$config['abom_stages'] = array('prepare', 'check', 'eng_approve', 'proc_approve');

// Printed sign-off block.
//   'customer' = 3 boxes — Prepared By (Engineering) / Checked By /
//                Approved By (Customer). Matches the approved design
//                document and is what goes to the customer. DEFAULT.
//   'internal' = 4 boxes — the same markup with Approved By
//                (Engineering) and Approved By (Procurement) split out.
$config['abom_signoff_mode'] = 'customer';

// Permission keys, mapped onto the project's module_capablity ACL.
// moduleid 4 = BOM CORRECTION TOOL.
//   submodule 74 AUTOMATION BOM GENERATOR    -> generate, save
//   submodule 75 AUTOMATION BOM APPROVALS    -> check, eng_approve, proc_approve
//   submodule 76 AUTOMATION BOM MASTER ITEMS -> master_edit
$config['abom_module_id'] = 4;
$config['abom_perms'] = array(
    'generate'     => array('moduleid' => 4, 'submoduleid' => 74),
    'save'         => array('moduleid' => 4, 'submoduleid' => 74),
    'check'        => array('moduleid' => 4, 'submoduleid' => 75),
    'eng_approve'  => array('moduleid' => 4, 'submoduleid' => 75),
    'proc_approve' => array('moduleid' => 4, 'submoduleid' => 75),
    'master_edit'  => array('moduleid' => 4, 'submoduleid' => 76),
);

// Tables the module owns. Used by the tables_ready() guard.
$config['abom_tables'] = array(
    'abom_plc_family',
    'abom_section',
    'abom_formula',
    'abom_feature',
    'abom_item',
    'abom_plc_rule',
    'abom_bom',
    'abom_bom_line',
    'abom_bom_revision',
    'abom_bom_approval',
    'abom_audit_log',
);
