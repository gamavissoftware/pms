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

## 0.7 Database facts proven this build

`Database/abom_001.sql` and `abom_002_seed.sql` have been run against a
disposable container loaded with the full production dump. Findings:

**Production is MariaDB 11.x, not 10.x.** The dump uses the collation
`utf8mb4_uca1400_ai_ci`, which only exists on MariaDB 11+. A 10.6
container aborted the import at 119 of 293 tables with
`Unknown collation`. On **MariaDB 11.4** all 293 tables imported cleanly
and both module scripts ran without error, producing
`71 / 29 / 42 / 8 / 4 / 4 / 6`.

### 0.7.1 Collation — a constraint, not a bug

The database default is `utf8mb4_uca1400_ai_ci`; the `abom_*` tables are
`utf8mb4_unicode_ci`; and the three existing tables the module reads
(`system_users`, `submodule`, `module_capablity`) are
`latin1_swedish_ci`.

That is safe **only** while `abom_*` varchars are never compared to
varchars in other tables. The day someone writes such a comparison,
MariaDB raises *"Illegal mix of collations"* — and it will raise it on
production while passing every test that stays inside the module.

Verified today: the module touches exactly three non-`abom_` tables, and
every one of them by **integer** column —
`system_users.user_id`, `submodule.id`, and
`module_capablity.role_id / moduleid / submoduleid`. No varchar
comparison crosses the boundary.

**Constraint for future work:** do not join or compare an `abom_*`
varchar column to a varchar column in any pre-existing table. If it
becomes necessary, add an explicit `COLLATE` to the comparison rather
than changing either table's collation.

`utf8mb4_unicode_ci` is kept deliberately: it is portable, whereas
`uca1400` exists only on MariaDB 11+ and would make the schema
un-installable on anything older.

---

## 0.8 Export dependencies and Unicode

### 0.8.1 PDF font — must stay Unicode

TCPDF's core fonts are not Unicode. Measured with
`BOMMODULEDEVELOPMENT/tests/pdf_glyph_probe.php`, which renders a probe
PDF and extracts the text back out:

| Glyph | dejavusans | freeserif | helvetica |
|---|---|---|---|
| `Ω` U+03A9 | ok | ok | **LOST** |
| `✎` U+270E | ok | ok | **LOST** |
| `⚠` U+26A0 | ok | ok | **LOST** |
| `−` U+2212 | ok | ok | **LOST** |
| `—` `–` `°` `·` | ok | ok | ok |

With helvetica the DBR line renders as `6.7?, 500W` — a plausible-looking
spec rather than a visible error.

`$config['abom_pdf_font'] = 'dejavusans'`. **No substitution was needed
for `✎`** — it renders correctly. Do not change this to a core font; the
probe exits non-zero if the configured font loses a glyph.

Confirmed on the real export: the produced PDF contains `Ω`, 42 `NO(S)`
cells and **zero** `?` characters, on landscape A4 with the column header
repeating on both pages.

### 0.8.2 XLSX needs ext-zip

PHPExcel's Excel2007 writer requires `ZipArchive`. It was absent from the
first PHP 7.4 test container and produced a 500 from inside the writer.
`Abom_exporter::xlsx()` now checks `class_exists('ZipArchive')` first and
throws a message an operator can act on; the controller renders
`abom/export_unavailable` with a 503 and offers CSV and PDF instead.

**Confirm ext-zip is enabled on the production PHP build** (cPanel →
Select PHP Version → Extensions). CSV and PDF have no such dependency.

### 0.8.3 UOM display map applies to all three exports

`abom_uom()` (`NOS` → `NO(S)`) is applied in the CSV, XLSX and PDF
writers as well as on screen — all three are read by humans. A future
machine-readable or ERP-bound export must use the raw stored value and
must not call it.

---

## 0.9 HOST APPLICATION: `module_capablity.role_id` holds a USER id

**This is a property of the Shubham Packaging PMS, not of this module.**
It is recorded here because there is nowhere else it is written down, and
anyone who touches permissions in this codebase needs it.

The column is named `role_id`, sits in a table called `module_capablity`,
and stores `system_users.user_id`. Measured against production data:

| Check | Result |
|---|---|
| distinct values in `module_capablity.role_id` | 132, ranging 12–238 |
| matching `system_users.user_id` | **131 of 132** |
| matching `user_role.user_role_id` | 40 of 132 |
| `user_role` size | 102 rows, max id 104 — below the observed maximum of 238 |

The application agrees: every item in
`application/views/common/nav-menu.php` gates on
`->where('role_id', $user_id)`.

**`Master_profile_guard`'s use of `$session['role']` is correct** and must
not be "fixed" to match. It queries a different table —
`user_role.user_role_id` — for a different purpose (whether a profile may
write master records). Only `module_capablity` has the misleading column
name.

`Abom_permission_guard::allows()` originally used `$session['role']` and
would have denied every user on production while presenting as a
permissions problem. It now uses `$session['user_id']`.

This was invisible to every offline test. It surfaced only when an
approval button failed to appear for a user who demonstrably held the
grant.

### 0.9.1 Consequence: access does not follow a job

Because grants are per **user** rather than per **role**, they do not
travel with a person's position. A new engineer has no access until
someone inserts rows; someone who leaves or changes department keeps
whatever they had until someone deletes them.

That is true of every existing module in this application today, not just
this one. `ROLLOUT.md` carries the grant and revoke queries under
"Ongoing administration".

---

## 0.9.2 Separation of duty — FIXED

### The defect

A single grant on `AUTOMATION BOM APPROVALS` let one user perform **all
three** approval transitions. Proven: user 61, holding only that grant,
walked a BOM prepared by user 91 from `submitted` to `approved` in three
consecutive requests, producing this trail:

```
stage         action   user_id  user_name
check         approve  61       Virendra Sharma
eng_approve   approve  61       Virendra Sharma
proc_approve  approve  61       Virendra Sharma
```

The printed sheet carries three signature boxes. One person clicking four
times produced a document implying three independent reviews of something
procurement orders parts against. The four stages certified nothing.

### Why the fix is NOT in the permission table

`module_capablity` **does** carry per-capability columns —
`submodule_access`, `madd`, `medit`, `mremove` — and mapping the four
transitions onto them was considered and rejected:

- `Reporting.php:16974` (the existing **Set Access Permission** admin
  screen) writes all four, labelled Add / Edit / Remove.
- Production data: 1,388 rows have `madd = 1`; `medit` and `mremove` are
  effectively always 0.

Overloading them would mean someone ticking **"Edit"** in an existing
admin screen silently granted **engineering approval authority**, with no
indication in that UI. That is a worse defect than the one being fixed.

More fundamentally: a grant says what a person may do **in general**. It
cannot say "not on this particular document", and any grant-based scheme
collapses the moment an administrator gives one person everything —
which is exactly how this happened.

### The rule

Enforced on the transition, against the recorded history in
`abom_bom_approval` and `abom_bom.created_by`:

1. **No user may perform two CONSECUTIVE forward transitions on the same
   BOM.**
2. **The user who created the BOM may not perform the final approval.**

Only *forward* transitions (`submit`, `approve`) count towards rule 1 — a
reject or reopen is a return, not a certification, so it neither confers
nor consumes a turn.

Minimum distinct people to reach `approved`: **two**, three in the normal
case. It holds whatever the grants say.

Implemented in `Abom_approval_model::separation_blockers()`, reached from
`blockers()`, which `advance()` already refuses on — so the server
enforces it whether or not the UI offered the control. The disabled
button additionally carries the reason as readable text next to it, not
just a tooltip.

### `$config['abom_require_distinct_approvers']`

Default `TRUE`. **Fails closed**: if the value cannot be read the rule
applies anyway, because a separation rule that switches itself off
because it could not find its own config is worse than none.

Setting it `FALSE` restores the previous behaviour — one approver may do
all three stages. Note that this flag does **not** govern the spec §6.3
`prepared_by` check, which is unconditional: the preparer can never check
their own work either way. So `FALSE` means two people minimum, not one.

### Verified — 23 assertions, two real users

| | |
|---|---|
| A submits, then is refused at check | 422, cites separation |
| B checks, then is refused at eng_approve (consecutive) | 422 |
| A may eng_approve — not consecutive for A | allowed |
| A refused the final approval as **creator**, with B as last actor | 422, cites creation |
| Direct POST bypassing the disabled control | 422, **no** approval row written, status untouched |
| Completed trail | ≥ 2 distinct users |
| Flag `FALSE` | one approver does all three again; still 2 people minimum |
| Flag back `TRUE` | the same path blocked again |

`e2e67` was rewritten to walk the workflow with two actors, which is what
a real workflow does — it now proves separation of duty as part of the
normal path. **38 of 38.**

---

## 0.10 `abom_003_permissions.sql` is id-agnostic

`submodule`.`id` is `AUTO_INCREMENT` (confirmed in the production dump:
`MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74`). The
permissions script therefore never hardcodes an id — it inserts, reads
`LAST_INSERT_ID()`, and prints the three assigned numbers for pasting
into `application/config/abom.php`.

Consequences: no dependency on a `MAX(submodule.id)` reading, no way for
a stale number to make the script wrong, and identical behaviour on a
scratch copy and on production.

Proven in the container by forcing `AUTO_INCREMENT` to 907 — the script
assigned 907/908/909, printed them, and the module picked them up from
config with no code change.

Also in the script:

- **Pre-flight, inside the script** — aborts via `SIGNAL SQLSTATE
  '45000'` if module 4 is missing, if `abom_item` does not exist
  (001/002 not run), or if `AUTOMATION BOM %` submodules already exist.
- **Transactional** — `START TRANSACTION` plus an `EXIT HANDLER FOR
  SQLEXCEPTION` that rolls back and re-signals, so a partial permission
  set cannot survive a failure.
- **Safe to re-run** — verified: the second run aborted with a clear
  message, exit code 1, and left the 3 submodules and 27 grants
  untouched.
- **Grants mirrored** from whoever already holds the existing module 4
  submodule, rather than guessed. If module 4 has no other submodule,
  none are created and the summary says so.

---

## 0.11 HOST APPLICATION: six views with a host-pinned definer

**A correction first.** In an earlier report I said a plain `mysqldump`
aborts on this database and that production backups may therefore be
broken. **That was wrong, and the alarm was overstated.** The failure was
specific to the test container.

### What is actually true

Six views carry `DEFINER = u537620103_shuser@127.0.0.1` with
`SQL SECURITY DEFINER`:

| View | Shape | Used by live code |
|---|---|---|
| `system_users_view` | has logic | **25 files** |
| `units_view` | passthrough | 2 files |
| `poinstructions_view` | passthrough | 1 file |
| `service_order_report` | has logic | 2 files (view-file name collision) |
| `module_capablity_view` | passthrough | 0 |
| `dfmom_points_view` | passthrough | 0 |

In the container, which has no such user, **all six are completely
unreadable** — `ERROR 1449` on a bare `SELECT COUNT(*)`, not merely on
dump — and `mysqldump` aborted after 870 bytes.

Creating `u537620103_shuser@127.0.0.1` in the container fixed everything
at once: all six views readable, and `mysqldump` succeeding with **no
flags** — 50 MB, 287 `CREATE TABLE`, `-- Dump completed`.

**So the definer resolves on production, and production backups are
fine.** The corroborating evidence is that 25 live files select from
`system_users_view` and the application works daily; if the definer did
not resolve there, those paths would be erroring loudly.

### The real risk, which is not about backups

The definer is pinned to the host `127.0.0.1`. MySQL treats
`user@127.0.0.1` and `user@localhost` as **different accounts**, and
`application/config/database.php` connects to `localhost`. Both grants
evidently exist on production today. But:

1. **Restore portability.** Restoring this dump into any environment that
   lacks that exact `user@host` gives six broken views and 25 broken code
   paths. Anyone standing up a staging copy, a new host or a developer
   machine hits exactly what this build hit. It is silent for the
   application until a page touches one, and loud for `mysqldump`.
2. **Single point of failure.** If the host part of that grant ever
   changes — a migration, a switch from TCP to socket — all six views
   break simultaneously and take 25+ code paths with them.

The definer almost certainly dates from a hosting migration: it is the
application's own DB user, host-qualified in a way the application itself
does not use.

### Two fixes, neither applied

**Option A — `SQL SECURITY INVOKER`.** Recreate each view with
`SQL SECURITY INVOKER`, so permissions are evaluated as the *calling*
user rather than a stored definer.

- *For:* removes the dependency on any particular account existing.
  Restores and migrations stop being fragile. Smallest permanent change.
- *Against:* the calling user must hold `SELECT` on the underlying tables.
  The application's DB user already does, so no practical loss here — but
  it is a genuine semantic change, and any future least-privilege account
  would need the underlying grants.

**Option B — recreate with a definer that exists.** Keep
`SQL SECURITY DEFINER`, change the definer to an account that reliably
exists, e.g. `u537620103_shuser@localhost` or `@%`.

- *For:* preserves current semantics exactly. Lowest behavioural risk.
- *Against:* keeps the fragility, just repoints it. `@%` weakens host
  restriction. A future migration can break it again.

**Recommendation: Option A** for these six, because all six are simple
reads over tables the application user can already select, so the
`DEFINER` semantics are buying nothing while costing portability.

Either way, it is six `CREATE OR REPLACE VIEW` statements and **has
nothing to do with this module.** Nothing has been changed.

**One thing worth doing regardless:** four of the six are 1:1
passthroughs of a single table (`system_users_view` is a column-for-column
mirror of `system_users`). They add a failure mode and no behaviour.
Retiring them would be a larger change and is not proposed here.

### Check on production

```sql
SELECT TABLE_NAME, DEFINER, SECURITY_TYPE
  FROM information_schema.VIEWS WHERE TABLE_SCHEMA = DATABASE();

SELECT User, Host FROM mysql.user WHERE User = 'u537620103_shuser';
-- needs privileges; if unavailable, this is equivalent:
SELECT COUNT(*) FROM system_users_view;   -- errors 1449 if the definer is missing
```

`ROLLOUT.md` keeps `--single-transaction` and the backup verification
regardless: it costs nothing, and it is what turned an 870-byte file that
looked like a backup into a caught error.

---

## 0.12 Stray file provenance — `controllers/Abom_model.php`

Caught by the rollback rehearsal's R1 check. Traced:

- **Not** in the working tree (only `Abom.php`), **never committed** (0
  commits for that path), **not** in the manifest, and **not** in any
  `ROLLOUT.md` copy instruction. Not an eighth document defect.
- Cause was a careless `cp` of mine into the sandbox:
  `cp application/controllers/Abom.php application/models/Abom_model.php <dest>/application/controllers/`
  — two sources, one destination, so both landed in `controllers/`.
- Requesting `/abom_model` returns **HTTP 500**,
  `Class 'CI_Model' not found` — loud, and only because that class extends
  `CI_Model`. A stray extending `CI_Controller` would have executed.

**The manifest would have missed it.** Its controller pattern was the
exact filename `application/controllers/Abom.php`, so a sibling was
invisible to the NEW-file detection. Every pattern is now a glob
(`Abom*.php`, both cases, in every directory the module writes to), and
`manifest.json` is excluded from its own checksum set. Verified: dropping
a stray into `controllers/` now reports
`NEW module application/controllers/Abom_model.php` and exits 1.

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
