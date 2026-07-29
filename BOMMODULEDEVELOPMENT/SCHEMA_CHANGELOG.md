# Automation BOM Module — schema & design change log

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
