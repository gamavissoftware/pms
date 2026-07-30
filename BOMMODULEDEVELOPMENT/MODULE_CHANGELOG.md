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
| rows in `module_capablity` | 2,614 |
| distinct values in `module_capablity.role_id` | 131, ranging 61–238 |
| matching `system_users.user_id` | **131 of 131 — every one** |
| matching `user_role.user_role_id` | 39 of 131 |
| values above `MAX(user_role.user_role_id)` | 92 of 131 |
| `user_role` size | 102 rows, max id 104 — below the observed maximum of 238 |

*Corrected 2026-07-30.* This table previously read "132 distinct,
ranging 12–238, 131 of 132 matching, 40 matching `user_role`". Those
numbers were wrong. Re-measured against the untouched production dump
(`module_capablity` extracted into a throwaway `abom_baseline` schema so
the sandbox's own test grants could not contaminate the count), the
correspondence is unanimous: there is no orphan value, and the single
exception implied by "131 of 132" never existed. The finding is stronger
than it was written, not weaker. The error was caught by mechanically
re-running every factual claim in this document rather than re-reading it.

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

## 0.13 `generate` and `save` were not permission-gated

Found while verifying a claim I had just written into
`POST_DEPLOY_CHECK.md` step 14 — that an ungranted user is refused at
`/abom/generate`. It was not true.

**What was wrong.** Spec §4.1 defines six permission keys. Only the three
approval ones were ever checked. `generate`, `save` and the line-edit
paths ran `require_tables()` and nothing else, so **any logged-in user
could open the generator by URL and save a BOM**, regardless of grants.

Demonstrated: user 63, holding zero `AUTOMATION BOM` grants, could not see
the menu item — correctly — but reached `/abom/generate` with all 42 rows
and **successfully saved a BOM**.

This also falsified the premise the rollout order rests on. "The module is
invisible to every user until a grant exists" was true of the *menu* and
false of the *URL*, which is a meaningful difference when the argument
being made is that step 1 is safe because nobody can see anything.

Same class of defect as §0.9.2: a permission that gates nothing is
decorative, and `AUTOMATION BOM GENERATOR` was gating nothing at all.

**The fix.** `Abom::require_perm($action)` gates
`generate`, `generate_ajax`, `save`, `save_line_qty` and
`acknowledge_line`. It distinguishes the two failure modes, because they
need different remedies:

- **unconfigured** → the configuration diagnostic naming the key and file
- **not granted** → an access denial following the house convention
  (`Master_profile_guard::deny`): flashdata plus a redirect for a page,
  403 JSON for AJAX

**Read paths were left open at this point** — `view`, `list`, `print`,
`export` and `reference` accepted any logged-in user, on the reasoning
that an approver holding only `APPROVALS` must be able to open the BOM
they are approving and the spec defines no view permission. That reasoning
was sound; the conclusion was too broad. **Superseded by §0.14**, which
gates reads on holding *any one* of the three permissions — which
protects the approver without leaving the whole BOM library open to every
account in the company.

Six regression assertions added to the separation-of-duty suite.

---

## 0.14 Full guard-surface audit — `reject` and `reopen` were ungated

§0.9.2 and §0.13 were each found by accident, while checking a sentence
that was about to be written into a document. Two accidents is a pattern,
not luck, so the whole guard surface was audited rather than patched
again: **every public entry point, every guard, in a table, because an
empty cell is visible in a way an absent `if` is not.**

`BOMMODULEDEVELOPMENT/tests/guard_matrix.php` builds it statically
(tracking brace depth, resolving delegation so `approve()` shows the
guards inside `workflow_action()`). A companion probe drives all 17 entry
points over HTTP as three real users. Both are reproducible.

**The audit found a third instance of the same defect.** `reject` and
`reopen` had no permission check whatsoever — not even the per-stage one
the other transitions use, because §6.3 says "any stage may reject" and
that was implemented literally as *anyone*:

```
  ungranted REJECT      HTTP 200  status now 'rejected'
  ungranted REOPEN      HTTP 200  status now 'draft'
  approval trail rows written by user 63: 2
```

A user with zero grants rejected a submitted BOM, reopened it, and left
their name twice in the approval history. This is worse than §0.13: the
generate gap let an ungranted user create their own BOM, this one let them
interfere with someone else's and write to the audit trail.

**The fix.** `reject` requires `check` (rejection is an act of approval
authority, not of merely being logged in). `reopen` requires `save`
(returning a rejected BOM to draft is a generator action). Both now answer
403 and write nothing.

**Read paths, per §0.13's supersession.** A single
`Abom::require_any_perm()` gates `view`, `reference`, `bom_list` and
`export` on holding **any one** of `generate` / `check` / `master_edit`.
The approver case was re-verified after the change and still passes — an
`APPROVALS`-only user reads every BOM and exports all three formats.

**Measured access matrix, after the fixes** (`REDIRECT` = flashdata +
redirect for a page request, `403` = JSON refusal for AJAX, `422` = the
request was authorised and then refused by workflow state or separation of
duty, which is the correct answer for a permitted user at the wrong time):

| Entry point | no grants | `APPROVALS` only | all three |
|---|---|---|---|
| `index` | REDIRECT | REDIRECT | REDIRECT |
| `generate` | REDIRECT | REDIRECT | ALLOWED |
| `generate_ajax` | 403 | 403 | ALLOWED |
| `save` | 403 | 403 | ALLOWED |
| `save_line_qty` | 403 | 403 | ALLOWED |
| `acknowledge_line` | 403 | 403 | ALLOWED |
| `submit` | 403 | 403 | 422 |
| `approve` | 403 | 403 | 422 |
| `reject` | **403** | 422 | 422 |
| `reopen` | **403** | 403 | 422 |
| `create_revision` | 403 | 403 | 422 |
| `view` | **REDIRECT** | ALLOWED | ALLOWED |
| `printable` | **REDIRECT** | ALLOWED | ALLOWED |
| `bom_list` | **REDIRECT** | ALLOWED | ALLOWED |
| `reference` | **REDIRECT** | ALLOWED | ALLOWED |
| `export` csv/xlsx/pdf | **REDIRECT** | ALLOWED | ALLOWED |

Bold cells changed in this pass. `index` redirects for everyone by
design — it is a router to `generate` or `bom_list`, not a screen.

### 0.14.1 Object-level checks — what is enforced and what is a decision

Permission answers *may this user do this kind of thing*. It does not
answer *may they do it to this particular record*. Probed separately:

| Question | Measured | Status |
|---|---|---|
| `generate` holder edits a line on **someone else's** draft | **succeeds** (qty 1 → 999) | **accepted, see below** |
| `APPROVALS` holder advances a BOM they are unconnected to | refused (422) | see caveat below |
| `/abom/view/{non-existent id}` | 404 | enforced |
| `/abom/view/{soft-deleted id}` | 404 | enforced |
| `export` of a soft-deleted BOM | 404 | enforced |
| soft-deleted BOM in `bom_list` | absent | enforced |
| `/abom/view/{superseded id}` | renders, 0 editable qty inputs | enforced |
| editing a line on a superseded BOM | 403 | enforced |
| `line_id` from BOM B posted with `bom_id` of BOM A | 404, nothing written | enforced |
| same smuggling on `acknowledge_line` | 422, `is_confirmed` stayed 0 | enforced |

**ACCEPTED, DELIBERATE: BOMs have no per-user ownership.** Any holder of
`AUTOMATION BOM GENERATOR` may edit any BOM in `draft`. This is a
recorded decision, not an oversight:

- The spec defines no owner column and no ownership rule, and
  `abom_bom` has no `owner_id`. Inventing one is a schema and behaviour
  change beyond this module's brief.
- It matches how the rest of the PMS works — DF dispatch plans and
  master records are editable by anyone holding the capability.
- It is the behaviour the business needs. A production engineer who is
  away should not block their colleague from correcting a BOM before
  submission.
- The exposure is bounded: only `draft` BOMs are editable at all, every
  change is attributed in `abom_audit_log`, and the moment a BOM is
  submitted the qty inputs disappear for everyone.

If per-user ownership is ever wanted, it is an `owner_id` column plus one
check in `save_line_qty` — not a redesign. Listed as future work.

**CORRECTION to the second row — the earlier reading was wrong.** It was
first reported as "refused, 422". That figure was **an artefact of the test
fixture**, not a protection. The BOM used had approval history that
happened to trip the consecutive-transition rule. Re-probed against a BOM
with a clean approval trail:

```
  APPROVALS-only holder, no link to the BOM   HTTP 200
  status submitted -> checked
```

**An unconnected `APPROVALS` holder CAN advance a BOM.** The original
caveat — that the 422 came from workflow rules and not from an ownership
check — was correct in kind but understated: there was no reliable refusal
there at all. The protection I declined to overclaim turned out not to
exist, which is why it was worth not claiming.

This is the same accepted decision as drafts, applied to transitions:
**stage authority is decided by permission plus separation of duty, not by
connection to the BOM.** Anyone holding the stage's permission may act at
that stage on any BOM. That is what a shared engineering and procurement
team needs, and it is consistent with the no-ownership decision above.

### 0.14.1a Incidental protections — read before changing the workflow

What genuinely stops one person walking a BOM to fully approved is
**separation of duty, enforced against recorded approval history** — not
grants, and not ownership. Measured:

| Attempt | Result |
|---|---|
| unconnected approver advances `submitted → checked` | **200 — permitted, by design** |
| the **same** user then advances `checked → eng_approved` | 422 *"Checked by you already; another user must perform the next stage."* |
| the **creator** advances `eng_approved → approved` | 422 *"You created this BOM, so you cannot give it the final procurement approval."* |

Those last two are the load-bearing ones, and they are config-gated on
`abom_require_distinct_approvers` (default `TRUE`). **If that default is
ever changed to `FALSE`, a single approver can take a BOM from submitted
to approved alone.**

All three are pinned by assertions 15a–15c in the separation-of-duty
suite, with a comment at the top of that block saying no ownership check
enforces them and the test is the tripwire. 15a deliberately asserts the
*permitted* case, so that adding an ownership check later also fails the
suite — the behaviour is a decision, and a change in either direction
should be made on purpose rather than discovered afterwards.

### 0.14.1b Guard ORDER leaked status and existence

Found while hunting for anything else protected only as a side effect.
In `workflow_action('advance')` the per-stage permission check runs
**after** `next_transition()`, so an ungranted user was answered before
being refused:

```
  ungranted + already-approved BOM   HTTP 422
    "There is no next stage from \"Approved — Procurement\"."   <- status leaked
  ungranted + non-existent BOM       HTTP 404                  <- existence leaked
```

Not a write — the approval trail stayed empty — but with reads now gated
(§0.14) it was inconsistent to leave this path narrating BOM state to
someone refused everywhere else.

**Fixed** by calling `require_any_perm()` as a floor at the top of
`workflow_action`, before the BOM is loaded. It is weaker than every
per-action check below it, so it relaxes nothing. Both cases now answer a
uniform 403 disclosing nothing. Pinned by assertion block 16.

**That is the complete list.** Every other refusal in the matrix traces to
an explicit check: `save_line_qty` and `acknowledge_line` both scope the
line by `bom_id` in the model and abort when it does not match (verified
in `Abom_model::update_line_qty` and
`Abom_approval_model::acknowledge_line`); soft-deleted records are filtered
by `deleted_at IS NULL`; superseded BOMs are refused by
`abom_qty_editable()` on status. None of those is incidental.

### 0.14.2 `master_edit` grants nothing today

`AUTOMATION BOM MASTER ITEMS` is defined in `abom_perms`, is created by
`abom_003_permissions.sql`, and is accepted by `require_any_perm()` as one
of the three read keys. **No screen consumes it**, because the master-item
maintenance screens are not part of this module — item, section, formula
and PLC-rule editing is done in SQL. Granting it today confers read access
to BOM screens and nothing more.

This is recorded rather than removed, because removing the submodule row
later would orphan any grants made against it. See the future-work entry.

### 0.14.3 CSRF — a known, accepted, application-level risk

`config['csrf_protection']` is **FALSE application-wide** and this module
does not change it. Every module POST endpoint — `save`, `save_line_qty`,
`submit`, `approve`, `reject`, `reopen`, `create_revision`,
`acknowledge_line` — is therefore vulnerable to cross-site request forgery
in exactly the same way as every other POST endpoint in the PMS. The
module inherits this posture; it does not add to it, and it is out of
scope to fix here.

**Stated as accepted, deliberately not fixed.** Enabling it means setting
`csrf_protection = TRUE` in `application/config/config.php` and then
adding the CSRF token to **every** AJAX call and form in the entire
application — several hundred call sites across all existing controllers
and views — because CI3's CSRF check is global and unconditional: the day
it is switched on, every POST in the PMS that does not carry a valid token
starts failing. That is an application-wide project with its own testing
and rollback plan, not a line in a module changelog.

### 0.14.4 Deliberate blanks in the matrix

Every cell the matrix leaves empty, and why:

- **`index` has no permission, workflow or object guard.** It only
  redirects to `generate` or `bom_list`, both of which are gated. It
  reaches no data and renders no view.
- **`__construct` has only the session check.** Table and config guards
  are per-action because the diagnostics differ per action; putting them
  in the constructor would mean every route returned the same unhelpful
  message.
- **`generate_ajax`, `save`, `submit`, `approve`, `reject`, `reopen`,
  `create_revision` have no `object/id` cell.** They resolve the BOM
  through the model, which filters `deleted_at IS NULL`, and answer 404 or
  422 on a miss rather than calling `show_404()` — so the guard exists but
  under a different name. `save_line_qty` and `acknowledge_line` were
  probed directly for cross-BOM id smuggling (both refuse).
- **`generate_ajax` and `save` have no `workflow` cell.** Neither acts on
  an existing BOM: one computes a preview, the other creates a new BOM in
  `draft`. There is no prior state to validate.
- **No entry point has an ownership cell.** Accepted by design, §0.14.1.
- **`export` has no workflow cell.** Exporting is read-only and valid in
  every status, including superseded — a superseded BOM must remain
  printable for the record.

---

## 0.15 `abom_002_seed.sql` failed in phpMyAdmin — TRUNCATE vs FK checks

**Found on production, during the real deployment.** Step 2 of the rollout
reported:

```
#1701 Cannot truncate a table referenced in a foreign key constraint
      (`abom_item`, CONSTRAINT `fk_abom_item_section`
       FOREIGN KEY (`section_id`) REFERENCES `abom_section` (`id`))
```

**Cause.** The seed cleared its six tables with `TRUNCATE`, guarded by
`SET FOREIGN_KEY_CHECKS = 0`. TRUNCATE on a parent table is refused
whenever FK checks are on — regardless of whether the child table holds any
rows — so the whole block depended on that session variable surviving.

It does through the `mysql` command line, which is the only way it was
ever tested. It does **not** through phpMyAdmin, which applies its own
"Enable foreign key checks" setting (on by default) and overrides it.

**This is a testing failure, not just a coding one.** Every verification of
this script — the 11-table import, the `71 · 29 · 42 · 8 · 4 · 4 · 6`
counts, the rollout rehearsal — ran through the CLI. The rollout document
tells the operator to use phpMyAdmin. The script was never once executed
the way the instructions say to execute it, so a whole class of
tool-dependent behaviour was invisible.

**The fix.** `DELETE FROM` in child-before-parent order, no
`FOREIGN_KEY_CHECKS` manipulation at all, wrapped in a transaction. It
needs no session flag, so it behaves identically through phpMyAdmin, the
mysql client, or anything else.

Two things improved as a side effect:

- **It can no longer orphan live BOMs.** With FK checks disabled, TRUNCATE
  would have emptied `abom_plc_family` even while `abom_bom` rows
  referenced it. `DELETE` is refused instead — the correct outcome, since
  re-seeding must not be able to break existing BOMs.
- **A refused re-seed no longer half-applies.** `START TRANSACTION` /
  `COMMIT` means a failure at `abom_plc_family` rolls back the already-run
  deletes, instead of leaving the module with no master data.

`SET FOREIGN_KEY_CHECKS = 1` at the end of the file was also removed: the
script no longer turns them off, and unconditionally turning them *on*
would corrupt a session that had deliberately disabled them.

**Verified, with FK checks explicitly ON:**

| Case | Result |
|---|---|
| fresh install | `71 · 29 · 42 · 8 · 4 · 4 · 6` |
| run a second time | clean, still 71 items |
| re-seed while a BOM exists | refused (1451), **and fully rolled back** — items, sections and the BOM all intact |
| seed data section vs handover | md5 `a5bba49541eca67b5e9482472cbb7564`, byte-identical |

**Checked for the same class of bug everywhere else:** no `TRUNCATE` or
`FOREIGN_KEY_CHECKS` remains in `abom_001.sql`,
`abom_003_permissions.sql`, or either `abom_004` views file. The rollback's
11 `DROP TABLE` statements were re-run with FK checks ON — they are already
in child-before-parent order and complete cleanly. That mattered most of
the three, because the rollback runs when something has already gone wrong.

---

## 0.16 The application sidebar is not loaded on module screens

**Reported from production**: the left sidebar overlapped the BOM table,
hiding the first ~250px of every screen — the config panel and the start of
the page title.

**Cause.** Every other page in the PMS wraps its content in
`<div class="wrapper">`, and the theme uses that class to offset content
past the fixed sidebar. This module's screens are full-bleed document
sheets, not `.wrapper` pages, so nothing offset them and the sidebar sat on
top of the content.

Two fixes were possible: add `.wrapper` and keep the sidebar, or drop the
sidebar. **Dropped, at the operator's request**, and it is the better fit
here anyway: a BOM is a 9-column document with editable quantity inputs,
and the ~250px the sidebar costs is the difference between the table
fitting and scrolling sideways.

`common/nav-menu.php` and `common/info-section.php` are simply **not
loaded** by `generate.php`, `view.php` and `list.php`. Neither shared file
is modified — rule 11 still holds, and nav-menu still carries the module's
own menu entry, so the module is reached from every other page exactly as
before.

`common/info-section.php` was dropped in the same change for a different
reason: **its entire body is wrapped in an HTML comment**, so it renders
nothing at all while still running a `SELECT` against `system_reports` on
every page load.

**Navigating back out** is a home button in `.app-header`, present on all
four screens. It is the only way out now, so it is unconditional — never
permission-gated, never hidden on screen. It links to `page_url` +
`Dashboard`, capital D, matching `Dashboard.php` on disk and the eight
existing links in `nav-menu.php` — this module runs on case-sensitive
Linux and `dashboard` would 404 there while working locally.

Hidden in `@media print` alongside the other controls: a printed BOM must
not carry a navigation button.

New CSS is scoped under `.abom-wrap` like every other rule in this module,
including the one media query, which hides the button's text label below
480px and leaves the icon.

**Verified on all four screens** (`generate`, `view`, `list`, `print`):
`id="topnav"` absent, the meeting bar absent, the home button present and
pointing at `Dashboard`. Design diff still reports 47 of 47 — removing the
app chrome moves the module *closer* to the approved design document, which
is itself a standalone sheet with no application navigation.

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
`application/libraries/Abom_engine.php`.

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

---

## 4. Future work — carried forward, not done here

Each of these is a deliberate decision to defer, with the trigger that
should bring it back.

1. **Wire `AUTOMATION BOM MASTER ITEMS` before any master-item screen
   ships.** The permission exists and is grantable today, but no screen
   consumes it (§0.14.2) — item, section, formula and PLC-rule editing is
   done in SQL. **If** master-item maintenance screens are ever built,
   every one of them must call `require_perm('master_edit')` before the
   first of them is deployed. Shipping such a screen without that call
   would repeat §0.13 exactly: a declared permission that gates nothing,
   on the module's most sensitive data — the seed every BOM is generated
   from. `PERMISSIONS_WORKSHEET.md` says plainly that the grant confers
   nothing today, so nobody grants it expecting protection that is not
   there.

2. **Six views carry a host-pinned definer.** `SQL SECURITY INVOKER` is
   the right fix (§0.11) and `abom_004_views_invoker.sql` is written and
   rehearsed, with the six current definitions captured verbatim in
   `abom_004_views_invoker_ROLLBACK.sql`. **Deliberately not bundled with
   this module** — it touches host application objects, so it lands as its
   own reviewed change after this module is verified in production, and
   not on the same evening.

3. **Per-user BOM ownership**, if wanted: an `owner_id` column on
   `abom_bom` plus one check in `save_line_qty`. Currently any `generate`
   holder may edit any draft, accepted and reasoned in §0.14.1.

4. **CSRF protection** is off application-wide and stays off (§0.14.3).
   Trigger: an application-wide security pass, not a module change.

5. **`Database/abom_003_permissions.sql` prints suggested grants as
   comments and creates none.** Ongoing administration — a new engineer,
   a leaver, a role change — is manual `module_capablity` maintenance,
   the same as every other module in the PMS. Per user, not per role.
