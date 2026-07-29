# Automation BOM Generator — CodeIgniter 3 Implementation Specification

**Project:** SPM1200L / SPM1250P Mitsubishi Automation BOM Module
**Target framework:** CodeIgniter 3.1.x (PHP 7.2+), MySQL 5.7+ / MariaDB 10.3+
**Prepared for:** Development handover to Claude Code
**Document revision:** 1.0
**Source of truth:** DF-1826 REV.02 and DF-1827 reference BOMs (71 master line items)

---

## 0. How to use this document

This is a build instruction, not a discussion document. Everything needed to implement the module is specified here or in the two companion files:

| File | Purpose |
|---|---|
| `abom_schema.sql` | Complete DDL — 11 tables, `abom_` prefix |
| `abom_seed.sql` | All seed data including the 71 master items, verbatim from the source BOMs |
| `SPM1200L_Automation_BOM_DF1826_DF1827_Review.html` | The approved UI prototype. The finished module must look and behave like this. |

Import the two SQL files first. Do **not** re-key the master item data by hand — it was extracted programmatically from the source Excel files and verified against them.

Where this document says MUST, it is a business rule that came from the source BOMs or from engineering review. Where it says SHOULD, it is an implementation preference that can be adapted to fit existing project conventions.

---

## 1. What the module does

An engineer enters a machine configuration — axis count, track count, line speed, motion type, model, hand — and the module produces a complete, print-ready, approval-ready electrical/automation Bill of Materials. It does this by selecting the correct Mitsubishi PLC family for that configuration, pulling every master item belonging to that family, computing each line quantity from a formula rather than a stored constant, and gating optional items behind machine-feature checkboxes.

The output goes through a four-stage approval chain (Prepared → Checked → Engineering Approval → Procurement Approval) and can be exported to CSV, Excel and PDF.

The critical design constraint: **quantities are computed, not stored.** Two machines of the same model with different axis counts must produce different SSCNET cable quantities without anybody editing master data. This is the whole reason the module exists.

---

## 2. Domain rules

### 2.1 PLC family auto-selection

Two Mitsubishi families are in scope.

**FX5 Series** — `FX5U-80MT/ESS` CPU with `FX5-80SSC-S` 8-axis simple motion module. Panel location "Panel With Machine". Reference BOM DF-1827 (8 axes, 12 tracks, 140 PPM, Intermittent). 29 items.

**iQ-R Series** — `R16MTCPU` motion controller with `R04ENCPU` PLC on an `R312B` 12-slot chassis. Panel location "Standalone". Reference BOM DF-1826 REV.02 (15 axes, 12 tracks, 180 PPM, Continuous). 42 items.

Selection logic:

```
iQ-R  IF  axes >= 10
      OR  speed_ppm >= 160
      OR (motion_type = 'Continuous' AND axes >= 12)
FX5   otherwise
```

This MUST be implemented as a data-driven evaluation over `abom_plc_rule` (priority ascending, first match wins), not as a hard-coded `if` chain. Engineering will change these thresholds when new machine variants are introduced, and they must not need a code deploy to do it. The seeded rules reproduce the logic above exactly.

The engineer MUST be able to override the detected family. When they do, set `abom_bom.plc_family_locked = 1` and display a visible "manual override" marker on the BOM header and in the export. An override that is never surfaced is how wrong BOMs get approved.

### 2.2 Quantity formulas

Every master item carries a `formula_code`. The engine dispatches on it:

| Code | Rule | Notes |
|---|---|---|
| `FIXED` | `qty = base_qty` | Independent of configuration. 37 items. |
| `AXES` | `qty = axes` | Reserved — no seeded item uses it today, but the engine MUST support it because future per-axis items are expected. |
| `AXES_MINUS_1` | `qty = max(1, axes - 1)` | FX5 SSCNET 0.5 M inter-amplifier links. An 8-axis machine daisy-chains 7 links. 1 item. |
| `J4_STO` | `qty = j4_units` | Safe Torque Off connectors. 2 items. |
| `BATTERY` | `qty = battery_qty` | Servo battery sets. 2 items. |
| `MANUAL` | `qty = base_qty`, flagged for review | 29 items. Base quantity is the reference-BOM value and is almost certainly wrong for a different configuration. |

Two rules that are easy to get wrong and MUST be honoured:

**`J4_STO` and `BATTERY` are separate inputs.** They cannot be driven from one number. DF-1826 shows 11 MR-J4 amplifier units but 12 battery sets. The most likely explanation is that the dual-axis `MR-J4W2-77B` needs two batteries, but this has not been confirmed by engineering, so the module keeps them as two independent sidebar controls rather than guessing.

**MR-J4 unit counting excludes MR-JE.** `MR-JE-70B` (perforation axis) is not an MR-J4 and does not take an STO connector. One `MR-J4W2-77B` dual-axis amplifier is ONE unit for STO purposes even though it drives two axes. If you later automate the `j4_units` count instead of taking it as an input, this is the rule.

**`MANUAL` is a first-class state, not an error.** These lines render with a yellow row background and a `MANUAL ⚠` badge on the formula chip. A BOM MUST NOT be advanced past the "Checked" stage while any `MANUAL` line is unconfirmed — see §6.3.

### 2.3 Family defaults on switch

When the computed PLC family changes as the engineer edits the configuration, the J4-units and battery inputs reset to that family's defaults, taken from `abom_plc_family`:

| Family | `default_j4_units` | `default_battery` |
|---|---|---|
| FX5 | 7 | 7 |
| iQ-R | 11 | 12 |

Only reset on an actual family transition. If the engineer has typed a value and the family has not changed, leave it alone.

### 2.4 Feature gating

Four optional machine features gate items in and out. An item with a non-null `feature_code` is included only when that feature is switched on.

| Feature code | Label | Items gated |
|---|---|---|
| `feat_perf` | Perforation Axis | `MR-JE-70B`, `HG-KN73JK`, `MR-J3ENCBL5M-A2-L`, `MR-PWS1CBL03M-A2-L` (FX5) |
| `feat_brake` | Electromagnetic Brake | `MR-BKCNS1` (iQ-R) |
| `feat_dbr` | Dynamic Braking Resistor | RECKON 6.7 Ω 500 W DBR (iQ-R) |
| `feat_imark` | I-Mark Sensor | `MR-CCN1` (iQ-R) |

All four default to ON. Gated-out items MUST be removed from the generated BOM entirely — not greyed out, not shown with quantity zero. Procurement reads this document; anything visible on it is something they may order.

---

## 3. Data quality — the eight known issues

These came out of the source-file audit. They MUST be carried into the module as persistent flags, not fixed silently in the seed data. The whole point of surfacing them is that engineering resolves them and the master data gets corrected through the UI with an audit trail.

**1. ERP code 4060431 is assigned to two different parts (severity: `conflict`).** In DF-1826 the same ERP code appears on `MR-J3JCBL03M-A2-L` (encoder cable 0.3 M, qty 5) and on `MR-PWS2CBL03M-A2-L` (power cable 0.3 M, qty 6). One of them is wrong. Both rows render red and both carry the cross-reference text. A BOM containing a `conflict` line MUST NOT be submittable for approval until an engineer explicitly acknowledges it with a comment.

**2–6. Five parts have no ERP code (severity: `no_erp`).** `HG-JR153` servo motor 4.8 Nm (DF-1826, collating 2nd stage); `GT3715-FHCBD` 15-inch HMI (DF-1826, marked NEW); `HG-SR152K` servo motor 7.2 Nm (DF-1827, pulling, marked NEW); `FX5-16EX/ES` 16-source DI card (DF-1827); `MR-J3ENSCBL20M-L` encoder cable 20 M (DF-1826). These render orange with `⚠ MISSING` in the ERP column. The module SHOULD offer a "raise ERP creation request" action on these rows that records the request against the item.

**7. Battery quantity discrepancy (severity: `review`).** Covered in §2.2. The note stays on the `MR-BAT6V1SET` iQ-R row until engineering confirms.

**8. Speed discrepancy in DF-1827.** The title block text reads "SPEED 120 PPM" but the filename, the data column and the component selection all confirm 140 PPM. The seed data uses 140 PPM. No flag is carried — this is recorded here so the reviewing department knows the discrepancy was noticed and resolved, not overlooked.

Severity counts in the seeded data: 2 `conflict`, 5 `no_erp`, 29 `review`, 35 `none`.

---

## 4. File layout

```
application/
├── config/
│   └── abom.php                          ← module config (see §4.1)
├── controllers/
│   └── Bom.php                           ← main controller
├── models/
│   ├── Bom_item_model.php                ← master item CRUD + filtered fetch
│   ├── Bom_model.php                     ← BOM header/lines persistence
│   ├── Bom_approval_model.php            ← workflow transitions + trail
│   └── Bom_master_model.php              ← families, sections, formulas, features, rules
├── libraries/
│   ├── Bom_engine.php                    ← ★ the core. PLC detect + qty calc + gating
│   └── Bom_exporter.php                  ← CSV / XLSX / PDF
├── helpers/
│   └── bom_helper.php                    ← row-class, badge and formatting helpers
├── views/
│   └── bom/
│       ├── generator.php                 ← main screen (sidebar + table)
│       ├── _sidebar.php
│       ├── _table.php                    ← AJAX-replaceable table partial
│       ├── _doc_header.php               ← print-visible document header
│       ├── _approval_block.php           ← print-only sign-off boxes
│       ├── list.php                      ← saved BOMs
│       ├── view.php                      ← read-only approved BOM
│       └── master/                       ← master item admin screens
│           ├── index.php
│           └── form.php
└── third_party/                          ← PhpSpreadsheet / TCPDF if not already present
assets/
└── bom/
    ├── bom.css                           ← ★ must reproduce the approved palette (§7.1)
    ├── bom.js                            ← filters, live recalc, export triggers
    └── bom-print.css
```

If the project already has an admin layout, module loader or base controller, the BOM controller MUST extend it rather than standing alone. Match the project's existing conventions for authentication, CSRF and asset loading — do not introduce a parallel pattern.

### 4.1 `application/config/abom.php`

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['abom_table_prefix']   = 'abom_';
$config['abom_default_uom']    = 'NOS';
$config['abom_bom_no_format']  = 'DF-%d';        // sprintf with sequence
$config['abom_models']         = ['SPM1200L', 'SPM1250P'];
$config['abom_sides']          = ['LHS', 'RHS', 'N/A'];
$config['abom_motion_types']   = ['Intermittent', 'Continuous'];

// Guard rails for configuration input
$config['abom_axes_min']       = 1;
$config['abom_axes_max']       = 16;             // R16MTCPU ceiling
$config['abom_tracks_min']     = 1;
$config['abom_tracks_max']     = 24;
$config['abom_speed_min']      = 40;
$config['abom_speed_max']      = 400;

// Approval stages, in order
$config['abom_stages'] = ['prepare', 'check', 'eng_approve', 'proc_approve'];

// Permission keys — map these onto the project's existing ACL
$config['abom_perms'] = [
    'generate'     => 'bom.generate',
    'save'         => 'bom.save',
    'check'        => 'bom.check',
    'eng_approve'  => 'bom.approve.engineering',
    'proc_approve' => 'bom.approve.procurement',
    'master_edit'  => 'bom.master.edit',
];
```

---

## 5. The engine — `application/libraries/Bom_engine.php`

This library is the only place quantity or family logic may live. Controllers, models and views MUST NOT compute quantities. Keeping it in one class is what makes the rules testable.

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bom_engine
 * Pure business logic — no DB writes, no output. Takes a configuration and a
 * set of master items, returns computed BOM lines.
 */
class Bom_engine
{
    protected $CI;
    protected $rules = null;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Bom_master_model');
        $this->CI->load->model('Bom_item_model');
    }

    // -----------------------------------------------------------------
    // PLC FAMILY DETECTION
    // -----------------------------------------------------------------

    /**
     * @param  array $cfg ['axes'=>int, 'speed_ppm'=>int, 'motion_type'=>string]
     * @return array ['family_id'=>int, 'code'=>string, 'rule_id'=>int, 'explanation'=>string]
     */
    public function detect_plc_family(array $cfg)
    {
        if ($this->rules === null) {
            $this->rules = $this->CI->Bom_master_model->get_active_rules(); // ORDER BY priority ASC
        }

        $axes   = (int) $cfg['axes'];
        $speed  = (int) $cfg['speed_ppm'];
        $motion = $cfg['motion_type'];

        foreach ($this->rules as $r) {
            if ($r->min_axes  !== null && $axes  <  (int) $r->min_axes)  continue;
            if ($r->max_axes  !== null && $axes  >  (int) $r->max_axes)  continue;
            if ($r->min_speed !== null && $speed <  (int) $r->min_speed) continue;
            if ($r->max_speed !== null && $speed >  (int) $r->max_speed) continue;
            if ($r->motion_type !== 'ANY' && $r->motion_type !== $motion) continue;

            return [
                'family_id'   => (int) $r->result_family_id,
                'code'        => $this->CI->Bom_master_model->family_code((int) $r->result_family_id),
                'rule_id'     => (int) $r->id,
                'explanation' => $r->explanation,
            ];
        }

        // Unreachable while the catch-all rule (priority 99) is active.
        throw new RuntimeException('No PLC selection rule matched. Check abom_plc_rule seed data.');
    }

    // -----------------------------------------------------------------
    // QUANTITY CALCULATION
    // -----------------------------------------------------------------

    /**
     * @param  object $item   row from abom_item
     * @param  array  $cfg    ['axes'=>int, 'j4_units'=>int, 'battery_qty'=>int]
     * @return int
     */
    public function calc_qty($item, array $cfg)
    {
        $base = (int) $item->base_qty;

        switch ($item->formula_code) {
            case 'AXES':
                return max(0, (int) $cfg['axes']);

            case 'AXES_MINUS_1':
                return max(1, (int) $cfg['axes'] - 1);

            case 'J4_STO':
                return max(0, (int) $cfg['j4_units']);

            case 'BATTERY':
                return max(0, (int) $cfg['battery_qty']);

            case 'FIXED':
            case 'MANUAL':
            default:
                return $base;
        }
    }

    // -----------------------------------------------------------------
    // FEATURE GATING
    // -----------------------------------------------------------------

    public function feature_enabled($feature_code, array $features)
    {
        if (empty($feature_code)) {
            return TRUE;                      // ungated item — always included
        }
        return !empty($features[$feature_code]);
    }

    // -----------------------------------------------------------------
    // GENERATION
    // -----------------------------------------------------------------

    /**
     * @param  array $cfg  axes, tracks, speed_ppm, motion_type, machine_model,
     *                     machine_side, j4_units, battery_qty, features[],
     *                     plc_family_id (optional manual override)
     * @return array ['family'=>..., 'sections'=>[...], 'lines'=>[...], 'stats'=>[...]]
     */
    public function generate(array $cfg)
    {
        $detected = $this->detect_plc_family($cfg);
        $family_id = !empty($cfg['plc_family_id'])
            ? (int) $cfg['plc_family_id']
            : $detected['family_id'];

        $overridden = ($family_id !== $detected['family_id']);

        $items = $this->CI->Bom_item_model->get_by_family($family_id); // ordered by section sort_order, then id
        $features = isset($cfg['features']) ? $cfg['features'] : [];

        $lines = [];
        $line_no = 0;

        foreach ($items as $item) {
            if ($item->is_optional && !$this->feature_enabled($item->feature_code, $features)) {
                continue;                     // gated out — excluded entirely
            }

            $qty = $this->calc_qty($item, $cfg);
            $line_no++;

            $lines[] = (object) [
                'line_no'        => $line_no,
                'item_id'        => (int) $item->id,
                'section_name'   => $item->section_name,
                'section_order'  => (int) $item->section_sort,
                'erp_code'       => $item->erp_code,
                'description'    => $item->description,
                'part_no'        => $item->part_no,
                'manufacturer'   => $item->manufacturer,
                'qty'            => $qty,
                'computed_qty'   => $qty,
                'is_overridden'  => 0,
                'uom'            => $item->uom,
                'formula_code'   => $item->formula_code,
                'usage_remark'   => $item->usage_remark,
                'remarks'        => $item->data_issue,
                'issue_severity' => $item->issue_severity,
                'is_optional'    => (int) $item->is_optional,
                'feature_code'   => $item->feature_code,
                'panel_location' => $item->panel_location,
                'is_manual_add'  => 0,
                'row_class'      => $this->row_class($item, $family_id),
            ];
        }

        return [
            'family'      => $detected,
            'family_id'   => $family_id,
            'overridden'  => $overridden,
            'lines'       => $lines,
            'stats'       => $this->stats($lines),
        ];
    }

    // -----------------------------------------------------------------
    // ROW CLASS — precedence MUST match the approved prototype (§7.2)
    // -----------------------------------------------------------------

    public function row_class($item, $family_id)
    {
        if ($item->issue_severity === 'conflict')                       return 'warn-r';
        if (empty($item->erp_code) || $item->issue_severity === 'no_erp') return 'noerp-r';
        if (!empty($item->is_optional))                                 return 'opt-r';
        if ($item->formula_code === 'MANUAL')                           return 'man-r';
        return ($family_id == 1) ? 'fx5-r' : 'iqr-r';
    }

    // -----------------------------------------------------------------

    public function stats(array $lines)
    {
        $s = ['lines'=>0,'total_qty'=>0,'manual'=>0,'no_erp'=>0,'conflict'=>0,'optional'=>0];
        foreach ($lines as $l) {
            $s['lines']++;
            $s['total_qty'] += (int) $l->qty;
            if ($l->formula_code === 'MANUAL')     $s['manual']++;
            if (empty($l->erp_code))               $s['no_erp']++;
            if ($l->issue_severity === 'conflict') $s['conflict']++;
            if ($l->is_optional)                   $s['optional']++;
        }
        $s['open_issues'] = $s['no_erp'] + $s['conflict'];
        return $s;
    }
}
```

### 5.1 Required unit tests

Write these before wiring the UI. They encode the rules that matter, and they are what proves to the reviewing department that the engine is correct.

| Test | Input | Expected |
|---|---|---|
| FX5 baseline | 8 axes, 140 PPM, Intermittent | family `FX5`, matched rule priority 99 |
| iQ-R by axis count | 10 axes, 100 PPM, Intermittent | family `iQ-R`, rule 1 |
| iQ-R by speed | 6 axes, 160 PPM, Intermittent | family `iQ-R`, rule 2 |
| iQ-R by continuous motion | 12 axes, 120 PPM, Continuous | family `iQ-R`, rule 3 (but rule 1 matches first — assert the family, not the rule) |
| Boundary — 9 axes | 9 axes, 140 PPM, Intermittent | family `FX5` |
| Boundary — 159 PPM | 8 axes, 159 PPM, Intermittent | family `FX5` |
| `AXES_MINUS_1` | 8 axes | qty 7 |
| `AXES_MINUS_1` floor | 1 axis | qty 1, never 0 |
| `J4_STO` / `BATTERY` independence | j4_units 11, battery 12 | STO line 11, battery line 12 |
| Feature gate off | `feat_perf` = 0, FX5 | exactly 4 fewer lines; none of the four perforation parts present |
| Full FX5 generation | all features on | 29 lines |
| Full iQ-R generation | all features on | 42 lines |
| Row-class precedence | conflict item that is also MANUAL | `warn-r` |

The two full-generation counts (29 and 42) are the regression guard. If a seed edit changes them, someone changed master data and must say why.

---

## 6. Controller and endpoints

### 6.1 Routes

```php
// application/config/routes.php
$route['bom']                      = 'bom/index';
$route['bom/generate']             = 'bom/generate';          // POST, AJAX
$route['bom/save']                 = 'bom/save';              // POST
$route['bom/list']                 = 'bom/bom_list';
$route['bom/view/(:num)']          = 'bom/view/$1';
$route['bom/export/(:any)/(:num)'] = 'bom/export/$1/$2';      // csv|xlsx|pdf
$route['bom/submit/(:num)']        = 'bom/submit/$1';         // POST
$route['bom/approve/(:num)']       = 'bom/approve/$1';        // POST
$route['bom/reject/(:num)']        = 'bom/reject/$1';         // POST
$route['bom/master']               = 'bom/master_index';
$route['bom/master/edit/(:num)']   = 'bom/master_edit/$1';
```

### 6.2 `Bom::generate()` contract

Accepts the configuration as POST, returns JSON. This is the endpoint the sidebar calls on every input change, so it MUST be fast and MUST NOT write to the database.

Request:

```json
{
  "axes": 15, "tracks": 12, "speed_ppm": 180,
  "motion_type": "Continuous", "machine_model": "SPM1200L", "machine_side": "N/A",
  "j4_units": 11, "battery_qty": 12,
  "plc_family_id": null,
  "features": { "feat_perf": 1, "feat_brake": 1, "feat_dbr": 1, "feat_imark": 1 }
}
```

Response:

```json
{
  "status": "ok",
  "family": { "code": "iQ-R", "explanation": "Axis count 10 or more exceeds...", "overridden": false },
  "stats":  { "lines": 42, "total_qty": 118, "manual": 17, "no_erp": 3, "conflict": 2, "open_issues": 5 },
  "html":   "<rendered _table.php partial>"
}
```

Returning rendered HTML rather than raw rows keeps the row-class precedence logic in one place (the helper) instead of duplicating it in JavaScript. Validate every numeric input against the `abom_axes_min`/`max` style config bounds and reject out-of-range values with a 422 and a field-level message — do not clamp silently.

### 6.3 Save and workflow guards

On `save`, persist the header, then insert every line as a **frozen snapshot**. `abom_bom_line` deliberately duplicates description, part number and manufacturer instead of joining to `abom_item`. A master item corrected next month MUST NOT retroactively change a BOM that procurement already approved.

Guards, enforced server-side in `Bom_approval_model` (never only in the UI):

- **Draft → Submitted** requires: every `MANUAL` line either confirmed or overridden with a reason; zero unacknowledged `conflict` lines.
- **Submitted → Checked** requires the `bom.check` permission and a different user from `prepared_by`.
- **Checked → Engineering Approved** requires `bom.approve.engineering`.
- **Engineering Approved → Approved** requires `bom.approve.procurement`. This is the terminal state.
- Any stage may **Reject**, which returns the BOM to `draft` and requires a comment.
- Editing an `approved` BOM is forbidden. Offer "Create Revision" instead: clone to a new row with `revision` incremented, mark the original `superseded`, and write a `abom_bom_revision` snapshot of the original.

Every transition writes a row to `abom_bom_approval` with stage, action, user, timestamp and comment. Every master-data change writes to `abom_audit_log` with old and new values.

---

## 7. User interface

The approved prototype `SPM1200L_Automation_BOM_DF1826_DF1827_Review.html` is the visual specification. Reproduce it — do not redesign it. Departments have already reviewed this layout.

### 7.1 Palette

Copy these CSS custom properties verbatim into `assets/bom/bom.css`:

```css
:root{
  --fx5:#DEEAF1; --iqr:#E2EFDA; --warn:#FFD7D7; --noerp:#FFE0B2;
  --manual:#FFF9C4; --opt:#F3EEFF; --hdr:#2E4057;
  --sec-fx5:#4A90D9; --sec-iqr:#27AE60; --accent:#E74C3C;
}
tr.item.fx5-r{background:var(--fx5);}
tr.item.iqr-r{background:var(--iqr);}
tr.item.warn-r{background:var(--warn);}
tr.item.noerp-r{background:var(--noerp);}
tr.item.man-r{background:var(--manual);}
tr.item.opt-r{background:var(--opt);}
```

Body font `'Segoe UI', Arial, sans-serif` at 13px, page background `#f0f2f5`. Section header rows use `--sec-fx5` or `--sec-iqr` depending on family, full-width across all 12 columns, with the reference-BOM caption appended: `▶ {section} [{family} Series — DF-1826 REV.02 Reference (15A / 180 PPM / Continuous)]`.

### 7.2 Row-class precedence

Exactly as in `Bom_engine::row_class()`, and in this order — the first match wins:

1. ERP conflict → `warn-r` (red)
2. Missing ERP code → `noerp-r` (orange)
3. Optional item → `opt-r` (purple tint)
4. `MANUAL` formula → `man-r` (yellow)
5. Otherwise family colour → `fx5-r` (blue) or `iqr-r` (green)

A conflict on a manual optional line shows red, not yellow. Severity outranks category.

### 7.3 Table columns

Twelve columns, in this order:

`# | ERP Code | Description | Part No / Model | Mfr | Qty | Formula | Used For / Installation | Remarks / Issues | Opt | Location | UOM`

The **Used For / Installation** column carries the source BOM's remark verbatim — strings like `COLLATING (1st STAGE) + CASE PACKER CONV. + TRANSFER CONV. + UNWIND` and `FILLING PISTON (2 NOS.)`. Do not normalise, abbreviate or title-case these. The reviewing engineers match them against the mechanical layout drawings.

A missing ERP code renders as `⚠ MISSING` in `--accent` red, bold. The formula column renders as a coloured chip; `MANUAL` shows as `MANUAL ⚠`.

### 7.4 Sidebar

Inputs: axes, tracks, speed (PPM), motion type, machine model, machine side, BOM/DF number, revision, prepared by, the four feature checkboxes, J4 units, battery quantity. Every change triggers a debounced call to `/bom/generate` — roughly 250 ms is right; the engineer is typing.

The detected PLC family displays prominently with the rule explanation text underneath, so the engineer can see *why* iQ-R was chosen. The override control sits next to it.

### 7.5 Filters and statistics

Section pills filter the table client-side (show/hide rows by `data-section`). Additional toggles for "manual only", "issues only" and "optional only". The stats bar shows line count, total quantity, manual count, missing-ERP count and conflict count. Filtering is presentation only — it MUST NOT change what gets saved or exported unless the engineer explicitly chooses "export current view".

### 7.6 Document header and sign-off block

The document header is visible on screen and in print: machine model, side, axes, tracks, speed, motion type, PLC family, BOM/DF number, revision, date, prepared by.

The approval block is print-only (`@media print`) with four boxes — Prepared By, Checked By, Approved By (Engineering), Approved By (Procurement) — each with a "Sign & Date" rule. Once the workflow is live, a stage that has been completed in the system SHOULD print the recorded name and timestamp instead of a blank line, with the blank rule kept for the wet signature.

Print CSS: hide the sidebar and all controls, repeat table headers across pages (`thead { display: table-header-group; }`), avoid breaking a section header away from its first row, and force background colours to print (`-webkit-print-color-adjust: exact; print-color-adjust: exact;`) — the colour coding is meaningful, not decorative.

---

## 8. Export

`Bom_exporter` produces three formats from a saved BOM.

**CSV** — plain, one row per line item, section name repeated in a column rather than as a spanning header row. UTF-8 with BOM so Excel opens `Ω` and `⚠` correctly.

**XLSX** (PhpSpreadsheet) — reproduce the on-screen formatting: section header rows merged across all columns in the family colour, item rows in their precedence colour, header row `#2E4057` with white bold text, freeze panes below the header, autofilter across the data range, sensible column widths. Include the document header block above the table and the approval block below it.

One trap worth stating explicitly: any cell whose value begins with `=` will be written as a formula and evaluate to `#VALUE!`. Several remark strings in this dataset start with `=`. Prefix such values with a single quote or set the cell type to string explicitly.

**PDF** (TCPDF or Dompdf, whichever the project already carries) — landscape A4, repeating header, the sign-off block on the final page, and a footer with BOM number, revision, page x of y and generation timestamp.

All three exports MUST embed the BOM number, revision, status and generation timestamp. An exported file that circulates without its revision is how the wrong BOM gets built.

---

## 9. Build order

1. Import `abom_schema.sql` and `abom_seed.sql`. Verify: 71 rows in `abom_item`, 29 with `plc_family_id = 1`, 42 with `plc_family_id = 2`, 8 sections, 4 rules, 4 features, 6 formulas.
2. Build `Bom_master_model` and `Bom_item_model` (read paths only).
3. Build `Bom_engine` and write the §5.1 tests. Do not proceed until they pass — in particular the 29 / 42 line counts.
4. Build the generator view and the `/bom/generate` AJAX endpoint. Compare side by side against `SPM1200L_Automation_BOM_DF1826_DF1827_Review.html` at 15 axes / 180 PPM / Continuous and at 8 axes / 140 PPM / Intermittent; the line lists must match exactly.
5. Add save, the saved-BOM list and the read-only view.
6. Add the approval workflow, guards and audit trail.
7. Add exports.
8. Add the master-item admin screens with audit logging.
9. Add print CSS and validate a physical print of both reference configurations.

### 9.1 Acceptance criteria

The module is done when an engineer can enter 15 axes / 12 tracks / 180 PPM / Continuous and get a 42-line iQ-R BOM that matches DF-1826 REV.02 item for item; enter 8 axes / 12 tracks / 140 PPM / Intermittent and get a 29-line FX5 BOM that matches DF-1827 item for item; change the axis count and watch the SSCNET 0.5 M quantity follow; switch off Perforation and watch four FX5 lines disappear; and carry a BOM through all four approval stages with a complete audit trail and a print-out that a department head can sign.

---

## 10. Things the developer must not do

Silently fix the data quality issues in the seed. They are flags, and engineering resolves them through the UI.

Compute quantities anywhere other than `Bom_engine`. Once quantity logic exists in two places it will diverge, and nobody will know which BOM was right.

Join `abom_bom_line` back to `abom_item` for display. The line is a frozen snapshot on purpose.

Normalise, re-case or "clean up" the `usage_remark` strings. They are matched against drawings by hand.

Hide `MANUAL` lines or auto-confirm them to make the BOM look cleaner. The yellow rows are the ones that need a human, and hiding them defeats the module.

Drive `J4_STO` and `BATTERY` from a single input.

Use `utf8` instead of `utf8mb4`. The data contains `Ω`, `⚠` and `°`.

---

## Appendix A — Seeded item distribution

| | FX5 | iQ-R | Total |
|---|---|---|---|
| PLC, I/O's, HMI, RTD, VFD | 14 | 15 | 29 |
| Servo Amplifiers & Motors | 7 | 12 | 19 |
| Servo Cables | 8 | — | 8 |
| Servo Encoder Cables | — | 5 | 5 |
| Servo Power Connectors & Cables | — | 5 | 5 |
| SSCNET Cables & Servo Battery | — | 5 | 5 |
| **Total** | **29** | **42** | **71** |

Formula distribution: `FIXED` 37, `MANUAL` 29, `J4_STO` 2, `BATTERY` 2, `AXES_MINUS_1` 1, `AXES` 0.

Severity distribution: `none` 35, `review` 29, `no_erp` 5, `conflict` 2.

Feature-gated items: `feat_perf` 4, `feat_brake` 1, `feat_dbr` 1, `feat_imark` 1.

## Appendix B — Reference configurations

| | DF-1827 | DF-1826 REV.02 |
|---|---|---|
| Model | SPM1200L | SPM1200L |
| Axes | 8 | 15 |
| Tracks | 12 | 12 |
| Speed | 140 PPM | 180 PPM |
| Motion | Intermittent | Continuous |
| Side | LHS | N/A |
| PLC family | FX5 | iQ-R |
| Panel location | Panel With Machine | Standalone |
| J4 units (STO) | 7 | 11 |
| Battery sets | 7 | 12 |
| Line items | 29 | 42 |
