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

// UOM presentation map. The STORED value stays canonical ('NOS') — it is
// what an ERP UOM master expects and what any machine-readable or
// ERP-bound export must emit. The printed document has to read 'NO(S)'
// because procurement and the customer put it side by side with the
// released DF. Applied in the view, print, PDF and XLSX renderers only.
// A UOM not in this map falls through unchanged.
$config['abom_uom_display'] = array(
    'NOS' => 'NO(S)',
);

// ---------------------------------------------------------------------
// SEPARATION OF DUTY
// ---------------------------------------------------------------------
// The four-stage workflow only certifies anything if more than one person
// is involved. Grants cannot express this: a grant says what a person may
// do in general, not "not on this particular document", and any
// grant-based scheme is defeated by an administrator giving one person
// everything — which is exactly how a single user was able to record
// themselves as checker, engineer AND procurement (MODULE_CHANGELOG §0.9.2).
//
// So it is enforced on the TRANSITION, against the recorded history:
//
//   1. No user may perform two CONSECUTIVE forward transitions on the
//      same BOM.
//   2. The user who created the BOM may not perform the final approval.
//
// That requires at minimum two distinct people, three in the normal case,
// and holds whatever the grants say.
//
// Setting this FALSE restores the single-user path. A site that genuinely
// cannot staff separate reviewers can make that choice — but it is then a
// deliberate, written decision rather than an accident.
$config['abom_require_distinct_approvers'] = TRUE;

// Workflow states in which a line quantity may still be edited.
// Once a BOM is engineering-approved the printed quantities must not be
// typeable over — see abom_qty_editable() in helpers/abom_helper.php.
$config['abom_qty_editable_status'] = array('draft', 'submitted', 'checked', 'rejected');

// Workflow states in which a BOM may be DELETED.
//
// Deliberately narrow. Deletion here is a SOFT delete — deleted_at is
// set and the row, its lines, its approval trail and its revision
// snapshots all remain — but a document that has entered the approval
// chain is a record of who signed what, and making it vanish from the
// register is not a thing an engineer should be able to do on their own.
//
// The way out of a submitted or approved BOM is the workflow: reject it,
// reopen it, or supersede it with a revision. All three leave a trail.
// Deletion is for the case the workflow has no answer to — a draft
// created by mistake.
//
// Widening this is a deliberate, written decision, exactly like
// abom_require_distinct_approvers above.
$config['abom_deletable_status'] = array('draft', 'rejected');

/**
 * Statuses in which the MACHINE CONFIGURATION may still be changed.
 *
 * Narrower than abom_qty_editable_status: changing axes or tracks
 * re-derives every computed line, which is fine on your own draft and
 * not fine on a sheet somebody else is checking. Same set as delete.
 */
$config['abom_config_editable_status'] = array('draft', 'rejected');

// Own numbering series. The existing DF register (DF_revision,
// Df_change_control, Df_dispatch_plan) owns the "DF-" namespace; this
// module must not mint into it. A generated BOM may still REFERENCE a
// source DF through abom_bom.df_ref, which is free text.
$config['abom_bom_no_format']  = 'ABOM-%d';

// Selectable machine models. Adding one here is what lets a released DF
// for that machine be IMPORTED (/abom/import) — the import creates the
// abom_variant, its abom_variant_rule and the items, all scoped to the
// model chosen on that screen. So the name here must match the name the
// business actually uses, or a later generate will not match the rule the
// import wrote.
//
// Names follow the compact form already established by SPM1200L/SPM1250P
// (no space, series number, suffix letter). The suffixes are the ones in
// production: machine_master carries 300L x8, 600L x10, 800L x4,
// 1000P x8 and 1000L x5, against 1200L x45 and 1250P x10.
//
// 1000 is listed BOTH ways on purpose — SPM1000L and SPM1000P are both
// in real use, and picking one would silently exclude the other.
$config['abom_models']         = array(
    'SPM300L',
    'SPM600L',
    'SPM800L',
    'SPM1000L',
    'SPM1000P',
    'SPM1200L',
    'SPM1250P',
);
$config['abom_sides']          = array('LHS', 'RHS', 'N/A');
$config['abom_motion_types']   = array('Intermittent', 'Continuous');

// Guard rails for configuration input. Out-of-range values are rejected
// with a field-level message — never silently clamped.
$config['abom_axes_min']       = 1;
// 64 = the OMRON NJ501-1500 ceiling, which is the widest controller in
// the system. It was 16 (the Mitsubishi iQ-R R16MTCPU ceiling), which
// rejected DF-1822 outright at 23 axes.
//
// This is a SANITY range, not a per-family limit, and it never was one:
// at 16 it was already wrong for FX5, whose FX5-80SSC-S does 8. What
// actually confines a machine to a buildable configuration is the
// selection rules -- an iQ-R at 23 axes now matches no build and gets
// the explicit "No build variant matches" message rather than a
// validation error, which says more.
$config['abom_axes_max']       = 64;
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

// =====================================================================
//  ⚠ THIS FILE CARRIES SITE-SPECIFIC VALUES. DO NOT OVERWRITE A
//    CONFIGURED INSTALL WITH A FRESH COPY OF IT.
//
//  abom_submodule_ids below is filled in PER SITE, from whatever ids the
//  live `submodule` table assigned when Database/abom_003_permissions.sql
//  was run there. In the repository they are null, because a guessed id
//  would be worse than none (see the note on that array).
//
//  So copying this file onto a working install REPLACES the real ids
//  with nulls, and every screen in the module drops to
//  "Automation BOM permissions are not configured". That has happened
//  once, on 2026-08-11, during the nine-DF re-seed deployment.
//
//  IF YOU ARE UPDATING AN INSTALL THAT ALREADY WORKS:
//    1. Read the current ids off the server's copy of this file first,
//       or off the database:
//         SELECT id, submodule FROM submodule
//          WHERE submodule LIKE 'AUTOMATION BOM %' ORDER BY id;
//    2. Upload the new file.
//    3. Put the three ids back into abom_submodule_ids below.
//
//  Nothing else in this file is site-specific.
// =====================================================================

// ---------------------------------------------------------------------
// PERMISSIONS — module_capablity ACL wiring
// ---------------------------------------------------------------------
// moduleid 4 = BOM CORRECTION TOOL.
//
// The three submodule ids are DELIBERATELY UNSET. They must be filled in
// with the real numbers from the live `submodule` table after
// Database/abom_003_permissions.sql has been run there.
//
// Shipping them unset is intentional. If this file carried guessed ids
// and the live table used different ones, every permission check would
// return false, the approval buttons would silently vanish, and it would
// look like a permissions problem rather than a configuration one.
// Abom_permission_guard verifies each configured id actually exists and
// is attached to module 4, and refuses to render the approval controls
// with a specific diagnostic if it does not. See abom_submodule_ids
// below — leave a value NULL and the guard tells the operator exactly
// which one to set and where.
$config['abom_module_id'] = 19;

$config['abom_submodule_ids'] = array(
    'generator'    => 77,   // AUTOMATION BOM GENERATOR
    'approvals'    => 78,   // AUTOMATION BOM APPROVALS
    'master_items' => 79,   // AUTOMATION BOM MASTER ITEMS
);

// Action -> submodule group. The ids come from abom_submodule_ids above.
$config['abom_perms'] = array(
    'generate'     => 'generator',
    'save'         => 'generator',
    'check'        => 'approvals',
    'eng_approve'  => 'approvals',
    'proc_approve' => 'approvals',
    'master_edit'  => 'master_items',
);

// ---------------------------------------------------------------------
// EXPORT
// ---------------------------------------------------------------------
// TCPDF core fonts (helvetica and friends) are NOT Unicode. The seed
// contains Ω (U+03A9) and the design depends on ✎ (U+270E); rendered
// with a core font both come out as '?'. Verified by
// BOMMODULEDEVELOPMENT/tests/pdf_glyph_probe.php — dejavusans renders
// Ω ✎ — – ° ⚠ · − correctly, helvetica mangles four of them.
// Do not change this to a core font.
$config['abom_pdf_font']       = 'dejavusans';
$config['abom_pdf_font_size']  = 7;
$config['abom_pdf_orientation'] = 'L';
$config['abom_pdf_format']     = 'A4';

// Tables the module owns. Used by the tables_ready() guard.
$config['abom_tables'] = array(
    'abom_plc_family',
    'abom_section',
    'abom_formula',
    'abom_feature',
    'abom_item',
    'abom_plc_rule',
    // Added by Database/abom_005_variants.sql. Listing them here means an
    // install that has run 001+002 but not 005 fails the tables_ready()
    // guard with a named missing table, rather than reaching the engine
    // and dying on an unknown column.
    'abom_variant',
    'abom_variant_rule',
    'abom_bom',
    'abom_bom_line',
    'abom_bom_revision',
    'abom_bom_approval',
    'abom_audit_log',
);
