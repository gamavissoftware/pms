# Automation BOM Module — change log

Schema deltas, naming decisions and design deviations.

Deltas between the handover documents and what was actually built, so
`Database/abom_001.sql` and `Automation_BOM_Module_CI3_Spec.md` do not
drift apart.

The handover documents are treated as read-only inputs and have not been
edited. This file is the record of every deliberate divergence.

Source documents, unchanged:

| File | md5 |
|---|---|
| `SPM1200L_Automation_BOM_DF1826_DF1827_Review.html` | `bf9c0500f1939e4cbb11110addb766d2` |
| `Automation_BOM_Module_CI3_Spec.md` | — read-only |
| `abom_schema.sql` | — superseded by `Database/abom_001.sql` |
| `abom_seed.sql` | — copied verbatim into `Database/abom_002_seed.sql` |

---

## 0. Naming — every artefact is `abom`-prefixed

### 0.1 Why

`application/controllers/Leads.php` — live and current — carries 17
references to a `BOM` controller (`BOM/pis`, `BOM/bom`,
`BOM/edit_bom_testing`, `BOM/edit_pis`, `BOM/caddataupload`,
`BOM/caddatauploaded`) and loads five `BOM/*` views. None of it exists in
this working tree, in the git baseline, or in the 2022 `crmbackup.zip`.

A live controller does not carry 17 dead paths. The conclusion is that
`application/` in this working tree is an **incomplete copy of
production**, not merely a copy missing `system/`.

Two consequences drive the naming:

- On a case-insensitive filesystem (macOS, where this is developed)
  `Bom.php` and `BOM.php` are the SAME FILE. Any module artefact whose
  name differs from a production one only by case would silently
  overwrite it.
- The collision surface cannot be enumerated from here, because the
  thing that would enumerate it is the part of the tree we do not have.
  So the prefix is applied unconditionally rather than on the strength
  of a check against a tree already known to be incomplete.

### 0.2 What was renamed

| Spec §4 / §5 name | Built as |
|---|---|
| `controllers/Bom.php` | `controllers/Abom.php` (`class Abom`) |
| `models/Bom_model.php` | `models/Abom_model.php` |
| `models/Bom_item_model.php` | `models/Abom_item_model.php` |
| `models/Bom_master_model.php` | `models/Abom_master_model.php` |
| `libraries/Bom_engine.php` | `libraries/Abom_engine.php` (`class Abom_engine`) |
| `libraries/Bom_exporter.php` | `libraries/Abom_exporter.php` (when built) |
| `helpers/bom_helper.php` | `helpers/abom_helper.php` |
| `views/bom/` | `views/abom/` |
| `assets/bom/` | `assets/abom/` |
| `assets/bom/bom.css` | `assets/abom/abom.css` |
| `assets/bom/bom-print.css` | `assets/abom/abom-print.css` |
| `assets/bom/bom.js` | `assets/abom/abom.js` |
| `assets/bom/bom-generate.js` | `assets/abom/abom-generate.js` |

**Spec §4 and §5 file names are superseded by this table.**

URLs are `/abom/...`. Spec §6.1's `bom/...` routes are superseded too:
CI's default routing resolves a class name directly, so a class named
`Bom` would expose a second live `/bom/...` namespace whether or not it
was routed.

### 0.3 CSS class and DOM id renames

Specificity was considered as a defence and rejected: `.abom-wrap
.bom-topbar` (0,2,0) loses to `#content .bom-topbar` (1,1,0) and to
`!important`, and production is known to carry a BOM module with six
controller actions and five views — almost certainly with its own
stylesheet. Duplicate DOM ids are worse than a style clash: they are
silently destructive, because `getElementById` returns whichever element
appears first in the document.

| Design name | Module name |
|---|---|
| `.bom-topbar` | `.abom-topbar` |
| `.bom-area` | `.abom-area` |
| `.bom-actions` | `.abom-actions` |
| `.bom-table-wrap` | `.abom-table-wrap` |
| `#bomTable` | `#abomTable` |
| `#bomBody` | `#abomBody` |
| `#bomContent` | `#abomContent` |
| `#bomTableHost` | `#abomTableHost` |

Design fidelity is unaffected, because class-name identity was only ever
a proxy for it. `BOMMODULEDEVELOPMENT/tests/design_diff.php` holds the
same map and applies it to the reference before comparing, so it still
reports "47 of 47" and tests same-structure / same-palette /
same-typography rather than same-spelling.

Database columns (`bom_no`, `bom_id`) and internal method names
(`get_bom`, `next_bom_no`) keep the bare word; they live inside
`abom_*` tables and `Abom_*` classes respectively and cannot collide.

### 0.3.1 CodeIgniter loader casing

`CI_Loader::library()` assigns the instance to `strtolower($class)`
unless an object name is given, so `$this->Abom_engine` is always null.
Models keep their case; libraries do not. Every library load in the
module passes an explicit object name:

```php
$this->load->library('Abom_engine', null, 'abom_engine');
```

This was only caught by running against a real CodeIgniter bootstrap —
the standalone harness injects its stubs directly and cannot see it.

### 0.4 Case sensitivity

Development is on case-insensitive macOS; production is case-sensitive
Linux, where `views/abom/`, `views/Abom/` and `views/ABOM/` are three
different directories. Every `load->view()`, `load->model()`,
`load->library()`, `load->helper()`, `load->config()` and `assets_url`
path in the module has been verified character-for-character against
its on-disk name. Re-run with:

```bash
php BOMMODULEDEVELOPMENT/tests/case_audit.php
```

### 0.5 Working-tree integrity

`BOMMODULEDEVELOPMENT/tests/integrity.php` records a checksum manifest
of every file the module owns, the one file it appends to
(`application/config/routes.php`) and the four read-only input
documents. Run `--write` after an intended change, and plain to verify.
It reports CHANGED, MISSING and NEW, so a stray file cannot hide either.

This exists because an unrelated change to
`application/controllers/Spares.php` appeared in the working tree
mid-build and was only noticed incidentally, in a `git status`. The tree
is shared. `manifest.json` doubles as the deployment file list.

### 0.6 Deployment (rule 12)

This working tree must **never** be synced, rsynced, pushed or uploaded
to the server as a whole — doing so would delete `BOM.php`,
`views/BOM/` and anything else missing here. Deployment is copy-up of
the module's own new files only, by explicit name list, reviewed first.

The git baseline on `main` is a baseline of a **partial copy**, not of
production. It is not a safe restore point, and "zero modified files"
does not mean "safe to deploy".

---

## 1. Schema deltas — `abom_schema.sql` → `Database/abom_001.sql`

### 1.1 Three new columns on `abom_bom_line` — approved

```sql
`is_confirmed`    TINYINT(1)   NOT NULL DEFAULT 0,  -- MANUAL line confirmed by engineer
`is_acknowledged` TINYINT(1)   NOT NULL DEFAULT 0,  -- conflict line acknowledged
`ack_comment`     VARCHAR(255) NULL,
```

**Why.** Spec §6.3 requires that Draft → Submitted be blocked until
every `MANUAL` line is confirmed or overridden with a reason, and until
every `conflict` line is explicitly acknowledged with a comment. The
handover schema has `is_overridden` and `override_reason`, which cover
the override path, but nowhere to record a *confirmation* or an
*acknowledgement*. Without these three columns the §6.3 guards cannot be
enforced server-side, which the spec requires ("never only in the UI").

### 1.2 New column on `abom_bom` — `df_ref`

```sql
`df_ref` VARCHAR(32) NULL COMMENT 'Optional link to existing DF register',
KEY `ix_abom_bom_df_ref` (`df_ref`),
```

**Why.** The application already owns the `DF-` namespace through
`DF_revision`, `Df_change_control` and `Df_dispatch_plan`, and DF-1826 /
DF-1827 are real records in that register. This module therefore mints
its own `ABOM-%d` series (`$config['abom_bom_no_format']`) and uses
`df_ref` as free text to reference a source DF without owning that
number.

### 1.3 `DROP TABLE IF EXISTS` removed

The handover file prefixed every table with `DROP TABLE IF EXISTS`,
which would silently destroy live BOM data on a second run.
`Database/abom_001.sql` uses plain `CREATE TABLE` — deliberately not
`IF NOT EXISTS` either — so a name collision fails loudly rather than
overwriting or silently skipping.

### 1.4 `SET FOREIGN_KEY_CHECKS = 0` removed from the schema file

Unnecessary: the creation order already satisfies every foreign key.
It is retained in `abom_002_seed.sql`, where the `TRUNCATE` statements
do need it. Those `TRUNCATE`s touch `abom_*` tables only.

### 1.5 Foreign key constraints renamed

`fk_section_family` → `fk_abom_section_family`, and likewise for
`fk_item_*`, `fk_bom_*`, `fk_line_*`, `fk_rev_*`, `fk_appr_*`. Index
names gained the same prefix. Constraint names are schema-global in
MySQL and the database already holds 293 tables; the generic names
risked a collision.

---

## 2. UI deltas — spec §7 is overridden by the approved design document

Spec §7.1, §7.2, §7.3 and §7.6 are void. The HTML governs appearance,
the spec governs behaviour. Recorded here because the spec document
still contains the superseded text.

| Spec §7 says | Built instead |
|---|---|
| 12 columns | The design's **9** columns |
| `--fx5 --iqr --warn --noerp --manual --opt --hdr` | The design's `--navy --blue --lblue --lgreen --lyellow --orange --red` |
| Row classes `fx5-r iqr-r warn-r noerp-r man-r opt-r` | The design's `data-row` / `sec-row` plus modifiers (§2.2) |
| 4-box internal sign-off block | 3 boxes by default; see §2.3 |
| PLC family colour coding of rows | None — a generated BOM is always one family, shown in the config panel |

### 2.1 Nine-column mapping

No engine field is dropped.

| Column | Source |
|---|---|
| S.NO. | continuous line number |
| ERP CODE | `erp_code`; NULL renders the NEW / PENDING `.formula-tag` badge |
| DESCRIPTION | `description` |
| MODEL NO. / PART NO. | `part_no` |
| MANUFACTURER | `manufacturer` |
| QTY. | computed quantity; `✎` marker appended when overridden |
| UOM | `uom` |
| REMARKS | `usage_remark`, then `panel_location`, then `OPTIONAL` |
| STATUS | `.formula-tag` badges: CONFLICT, ERP PENDING, REVIEW, MANUAL QTY, QTY EDITED |

### 2.2 Row-class precedence — final

Modifier classes on `data-row`, coloured only from the design's existing
palette variables. First match wins; severity outranks category.

| Class | Variable | Rows (of 71) |
|---|---|---|
| `is-conflict` | `--red` | 2 |
| `is-noerp` | `--orange` | 5 |
| `is-optional` | `--lgreen` | 7 |
| `is-manual` | `--lblue` | 24 |
| *(no modifier)* | — | 33 |

`review` severity produces **no row tint**. It applies to 29 of 71
master items; tinting them would put a large fraction of a
customer-signed document into a warning colour and would collapse
`is-manual` to the 3 rows carrying no severity. Review surfaces as a
STATUS badge instead — nothing is lost, because STATUS carries every
applicable badge regardless of which class wins the row.

`--lyellow` is reserved for the job the design's own legend gives it:
"Qty edited during review (marked ✎)". It is applied as a `qty-edited`
class on the **QTY cell only**, never the row.

### 2.3 Sign-off block

`$config['abom_signoff_mode']`:

- `'customer'` (default) — 3 boxes, exactly as the design document:
  Prepared By — Engineering / Checked By / Approved By — Customer.
- `'internal'` — the same box markup with a fourth added, splitting
  Approved By into Engineering and Procurement.

The spec's four-stage workflow (`draft → submitted → checked →
eng_approved → approved`) is unchanged and still drives
`abom_bom_approval` and the on-screen status indicator. That is internal
state, not print layout.

---

## 3. Test suite

`BOMMODULEDEVELOPMENT/tests/` — standalone, no CodeIgniter bootstrap and
no database. Parses `abom_seed.sql` directly and drives the real
`application/libraries/Bom_engine.php`.

```bash
php BOMMODULEDEVELOPMENT/tests/run_tests.php      # spec §5.1 suite
php BOMMODULEDEVELOPMENT/tests/dump_reference.php # Appendix B line lists
```

Covers all 13 spec §5.1 cases plus seed integrity (Appendix A) and the
row-class distribution above. The two regression counts — FX5 29 lines,
iQ-R 42 lines — are asserted, and cross-checked line by line against the
dataset embedded in the approved design document: exact match on part
number and quantity, in order, for both reference configurations, with
total quantities 74 and 129.
