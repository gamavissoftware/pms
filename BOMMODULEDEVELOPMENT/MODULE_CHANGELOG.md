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
| `abom_seed.sql` | — copied verbatim into `Database/abom_002_seed.sql`, since superseded by `Database/abom_006_seed.sql` (§0.18) |

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

## 0.17 User guide (`/abom/guide`) — generated, not written

Built for operator training. The requirement was that it explain
"how the formula works and everything", which is exactly the kind of
document that is accurate on the day it is written and wrong six months
later.

**So it is not written — it is generated.** Everything factual on the page
is read from the same tables the engine uses, or computed by the engine as
the page renders:

| Section | Source |
|---|---|
| PLC selection rules, in priority order | `abom_plc_rule`, via `plc_rules_readable()` |
| The formula list and what each reads | `abom_formula`, via `formulas_with_usage()` |
| How many master items use each formula | live `COUNT(*)` on `abom_item` |
| Feature switches, defaults, item counts | `abom_feature`, via `features_with_usage()` |
| The four workflow stages | `Abom_approval_model::stage_ladder()` — the same `$transitions` array the model enforces |
| Both worked examples | `Abom_engine::generate()`, run as the page loads |

Edit `abom_plc_rule` and the training material changes with it. The guide
cannot describe a workflow the code does not run, or a rule the engine does
not apply, because it has no independent copy of either.

The prose that *is* hand-written is the part that cannot be derived —
what a formula means in practice, what to do about a red row, why MR-J4
units and battery quantity are separate fields. Formula explanations are
keyed by formula CODE with a fallback to `abom_formula.description`, so a
new code added to the table still appears in the table rather than
silently going missing.

**Worked examples are the teaching tool.** Section 12 prints the two
reference configurations and what they must produce — measured live at
29 items / qty 74 (FX5, DF-1827) and 42 items / qty 129 (iQ-R, DF-1826),
each with the rule that selected the family. A trainee types the
configuration in and compares. If the numbers ever stop matching, the page
says so by itself.

**Gating** is `require_tables()` + `require_any_perm()`, the same read gate
as `view`, `list`, `reference` and `export` — measured: full grants 200,
`APPROVALS`-only 200, no grants redirected.

Reachable from the Guide button on the generator, the saved-BOM list and
every BOM document, and printable as a handout (the contents list is
dropped in print, and headings are kept with the table that follows).

**Two defects found by rendering it rather than trusting it:**

- `generate()` returns `family` as the ARRAY from `detect_plc_family()`
  (`family_id` / `code` / `rule_id` / `priority` / `explanation`), not a
  family row. `$result['family']->name` rendered an empty string and the
  reason line vanished entirely. Both silently — the page looked finished.
- `Abom_master_model` had no `$item_table`, so the usage counts needed one
  added.

`route_surface.php` failed on the first run, as designed: `guide` was a
public method not in the matrix. Entry surface is now 17 routes, 18 public
methods, 18 matrix rows, agreeing in both directions.

---

## 0.18 Nine reference BOMs — build variants, and the track-driven
## temperature card rule

### The request

Engineering supplied eight further SPM1200L / SPM1250P workbooks and
asked for the previous master data to be replaced with them, plus one
explicit rule: ERP **2020122** (FX5-4LC, 4 CH. temperature card) is to be
quantified as

```
(((Number of Track + 1) * 2) + 2) / 4
```

### Why the module could not just be re-seeded

The module scoped every master item to a **PLC family** and generated
"every active item of the detected family". That is correct only while
one family means one build. It is not true of the new data:

| Axes | Amplifiers | Motors |
|---|---|---|
| 5–7 (DF-1723/1778/1808/1858/1864) | MR-JE-300B / MR-JE-200B | HG-SN |
| 8 (DF-1827) | MR-J4-350B / MR-J4-200B | HG-JR |

Both are FX5. Flattened into one family catalogue, a 6-axis machine
generates **both** amplifier ranges and procurement orders twice the
drives. The iQ-R family splits three ways for the same reason
(panel-mounted 10-axis, standalone 15-axis high speed, and the SPM1250P
flow-meter build with its remote head and per-track cut-off drives).

### What was added

`Database/abom_005_variants.sql` — purely additive:

| Object | Purpose |
|---|---|
| `abom_variant` | one buildable configuration of a family |
| `abom_variant_rule` | selection rules, priority ASC, first match wins |
| `abom_item.variant_id` | nullable; a NULL is **never generated** |
| `abom_bom.variant_id` / `.variant_locked` | which build a saved BOM came from, and whether it was overridden |
| `abom_bom_line.variant_code` | frozen onto the line like every other descriptive field |

`Abom_engine::detect_variant()` runs after `detect_plc_family()` and uses
the same contract. `Abom_item_model::get_by_variant()` — not
`get_by_family()` — is what `generate()` now reads from.

**No variant means no lines**, and the generator says so in the sidebar.
Falling back to the whole family is the one wrong answer available here.
Every variant rule therefore names a machine model, including the two
that would otherwise be catch-alls: a model the reference data does not
describe must not silently inherit an SPM1200L build.

### The five variants

| Code | Family | Items | From |
|---|---|---|---|
| `FX5-JE` | FX5 | 31 | DF-1723, DF-1778, DF-1808, DF-1858, DF-1864 |
| `FX5-J4` | FX5 | 29 | DF-1827 |
| `IQR-STD` | iQ-R | 34 | DF-1770 |
| `IQR-HS` | iQ-R | 42 | DF-1826 |
| `IQR-FLM` | iQ-R | 42 | DF-1855 |

DF-1827 still generates **29** lines and DF-1826 still generates **42** —
the two Appendix B regression numbers are unchanged by the re-seed.

### `TRACK_TEMP`

Rounded **up**; half a card cannot be bought. Checked against every
source BOM carrying a 4-channel temperature or RTD card:

| Tracks | Rule | Sheets |
|---|---|---|
| 6 | 4 | DF-1858 = 4 |
| 8 | 5 | DF-1778 = 5 |
| 9 | 6 | DF-1855 = 6 (5.5 rounded up) |
| 12 | 7 | DF-1723, DF-1808, DF-1827, DF-1770 = 7; DF-1864, DF-1826 = **8** |

Eight of ten agree exactly. The two that do not are flagged for review on
the row rather than averaged away.

The rule is applied to all three 4-channel temperature cards — 2020122
(FX5-4LC), 2240039 (AUTONICS TM4-N2SB) and 4010225 (R60TCRT4) — because
it is the same physical rule, one card per four channels, and the table
above confirms it on the iQ-R cards too. To confine it to 2020122, set
the other two rows back to `MANUAL`; nothing else changes.

`TRACKS` was added alongside it for the SPM1250P cut-off drives, which
are genuinely one per track.

### `find_erp_conflicts()` was rewritten

It counted **rows** per ERP code. With items scoped to variants the same
part legitimately appears once per variant that fits it — VFD 2040140 is
on all five — so counting rows reported every one of those as a conflict
and buried the real defects. It now counts `DISTINCT part_no`, which is
the actual defect, and reports exactly four:

| ERP | Part A | Part B |
|---|---|---|
| `4060431` | encoder cable MR-J3JCBL03M-A2-L | power cable MR-PWS2CBL03M-A2-L |
| `2110163` | MR-BKCNS1 (DF-1826) | SC-BKC1CBL1M-L (DF-1855) |
| `2030449` | 10 MTR MR-J3ENCBL10M-A2-L (5 sheets) | 5 MTR MR-J3ENCBL5M-A2-L (DF-1827 only) |
| `2030505` | MR-J3BUS3M (DF-1826) | MR-J3BUS3M-A (DF-1855) |

### Deliberately out of scope

Sheet 2 of the DF-1864 workbook also carries a full **electrical panel**
BOM — energy meter, SMPS, MCBs, MPCBs, contactors, SSRs, sensors,
heaters, tower light. That is panel hardware, not automation, and none of
the module's five sections describes it. It is not seeded. The DF-1723
automation portion of that same sheet **is**, inside `FX5-JE`.

---

## 0.19 A clone you cannot re-specify is a photocopy — `save_config`

`duplicate()` shipped in §0.14's build and produced a fresh draft of an
existing document. It was not enough. Every field on the Machine
Configuration panel was rendered `readonly` on `abom/view`, so the copy
was permanently stuck describing the original machine: copying an
11-axis BOM to quote a 15-axis one meant retyping it from the generator.

### The two flags

`_config_panel.php` keyed everything off one variable, `$editable`,
which meant "this is the generator". That conflated two questions that
are not the same:

| flag | means | Save button |
|---|---|---|
| `$editable` | the GENERATOR screen | `Save BOM` — creates a NEW document |
| `$config_editable` | a SAVED BOM still open to change | `Apply configuration` — rewrites THIS document |

They are kept apart deliberately. Showing the generator's `Save BOM` on
a saved BOM would silently fork it into a second document, which is the
opposite of what someone editing a clone wants.

### Regenerating is not optional

Axes, tracks, speed, motion type, model, family and variant are the
inputs to variant selection and to every computed formula. A header
reading 15 axes above lines computed for 11 is a document that will be
built wrong, so changing the specification re-derives the line set.

`Abom_model::replace_lines()` does it in one transaction, and is
deliberately NOT `save_lines()`: that method edits the lines of a fixed
configuration, this one changes the machine.

### What survives, and what is reported

`Abom::carry_line_edits()` re-attaches hand work by **master item**, not
by line number — line 7 before and line 7 after a build change are
unrelated parts.

- hand-added rows (`item_id IS NULL`) — kept in full, re-appended
- typed remarks — re-attached to the same item
- quantity overrides — re-applied to the same item, with `computed_qty`
  left as the NEW engine answer so the sheet still shows both numbers
- anything whose item is not in the new build — **reported**, not
  dropped in silence

### The seeded-remark trap, caught by the harness

§0.18's REMARKS work made `Abom_engine` seed every line's `user_remark`
from the master item's `usage_remark`. The first cut of
`carry_line_edits()` therefore treated all 20 seeded defaults as hand
typed, and told the user "20 remarks carried" when they had written two.

The rule is now: **a remark is hand-set only when it DIFFERS from the
seeded default.** Clearing a seeded remark to blank counts as a hand
edit and is carried as one, so a regeneration cannot quietly restore
text somebody deliberately deleted.

This was found by `tests/reconfigure_probe.php`, not in a browser.

### One consequence, recorded rather than hidden

`replace_lines()` deletes the line set and writes a new one. Nothing in
the schema holds a foreign key onto `abom_bom_line.id`, so that is
structurally safe — but `Abom_model::get_audit()` finds per-line audit
rows by *current* line id, so quantity-override history recorded against
the old ids drops out of that BOM's displayed trail. The audit rows
themselves are not deleted, and the reconfiguration is logged against
`abom_bom` with the full before/after configuration, which is the entry
that explains where the earlier line history stops.

Keeping the per-line trail across a build change would mean carrying ids
for parts that are no longer on the document. Not worth it.

### Gate

`abom_config_editable()` — **draft and rejected only**, the same set
`delete` uses. Narrower than `abom_qty_editable()` on purpose: editing a
quantity marks up a document, editing the axis count changes what
machine the document is for. A sheet under review or already approved is
duplicated or revised, never re-specified underneath its reviewer. The
server re-checks the rule on POST; the hidden button is not the gate.

### Verification

`tests/reconfigure_probe.php` — 37 assertions against the real
controller (via reflection, no CI bootstrap) and the real engine:
tracks 12→6 re-derives the temperature cards while all hand work
survives; a 6-axis FX5 → 15-axis iQ-R jump changes the build and reports
what could not be carried; a clean BOM invents nothing; the confirmation
message names the changed fields and the carried counts.

`tests/render_view.php je config` renders the unlocked panel and asserts
`Save BOM` and the preset picker are absent from it.

---

## 0.20 Export filenames

Downloads were named `ABOM-14.pdf`. They are now named for the machine:

```
DF-1808 - SPM1200L, 6A, 12T, 100PPM - INTERMITTENT - MITSUBISHI.pdf
DF-1826 REV.02 - SPM1200L, 15A, 12T, 180PPM - CONTINUOUS - MITSUBISHI.pdf
DF-1808 - SPM1200L, 6A, 12T, 100PPM, LHS - INTERMITTENT - MITSUBISHI.pdf
```

- leads with `df_ref`, falling back to `bom_no` when there is no DF
- revision appended only when it is not `00`
- side appended only when it is not `N/A`
- brand derived from the lines, not hard-coded — `title()` no longer
  says MITSUBISHI regardless of what is on the sheet
- `safe_filename()` strips `\ / : * ? " < > | ;` and newlines

One defect worth recording: the sanitiser first used a `/`-delimited
regex whose character class began `[\\/`. PCRE reads that escaped
backslash followed by a literal slash as the **closing delimiter**, so
`preg_replace` returned `NULL` and every download arrived as `.pdf` with
no name at all. Fixed by delimiting with `~`.

---

## 0.21 Manufacturer on the saved-BOM register

The register showed model, configuration, family and build but not who
makes the parts, and could not be filtered by it.

### Derived, not stored

There is no manufacturer column on `abom_bom` and there should not be
one. Manufacturer is a property of the LINES, and the master data
already carries three:

| brand | items |
|---|---|
| MITSUBISHI | 368 |
| RECKON | 9 |
| AUTONICS | 3 |

Nearly every BOM is one brand of automation **plus a bought-in part or
two** — the RECKON braking resistor, the AUTONICS temperature card. A
single-value header column would have had to pick one and be wrong about
the rest.

So both the column and the filter options are read from
`abom_bom_line`. A new manufacturer becomes visible and filterable the
moment a BOM containing it is saved, with nothing to maintain — which
matters, because more are expected.

### Ordering matches the export filename

`Abom_model::attach_manufacturers()` orders brands by how many lines each
supplies, so the first is the machine's actual brand. That is the same
rule `Abom_exporter::brand()` uses to name the download, so the register
and the file cannot disagree about who made the machine.

The column shows the primary brand and a `+2` count with the full list on
hover. Three brand names per row would bury the one that matters on a
screen whose job is scanning down one column.

### Two query choices worth recording

- **`EXISTS`, not a join, for the filter.** A join would multiply the
  header row once per matching line and the `N SAVED` count in the banner
  would start lying.
- **A second query, not `GROUP_CONCAT`.** The join would have to be a raw
  derived table to survive CI's identifier escaping. Getting that subtly
  wrong on the screen everyone starts from is a poor trade for saving one
  round trip.

### The filter hides itself

While only one brand is on record the dropdown does not render at all — a
select with a single option is furniture, not a control. It appears on
its own once a second brand reaches a saved BOM.

### Verification

`tests/render_list.php` — 16 assertions. The register had **no test
coverage at all** before this: it renders straight from a model call, so
nothing in `run_tests.php` ever touched it, and a column added to
`<thead>` but not `<tbody>` would shear the table silently. The harness
now counts header cells against every row's cells, pins the column
between BUILD and LINES, and covers three brands, two, one, and none.

Run `php BOMMODULEDEVELOPMENT/tests/render_list.php single` for the
one-brand case, which asserts the filter is absent.

**Not verified locally:** the three SQL statements themselves. This tree
has no `system/` directory and no reachable database, so CI's query
builder cannot be executed here. The SQL is hand-checked against
`ONLY_FULL_GROUP_BY` and CI3's escaping rules, and `POST_DEPLOY_CHECK.md`
step 18 confirms it on the server.

---

## 0.22 DF reference uniqueness — checked as it is typed

Two BOMs could carry the same DF reference. A DF reference is an
engineering drawing number, so that means two live documents both
claiming to be the parts list for one drawing, and the person who finds
out is whoever orders from the wrong one.

### The rule is (df_ref, revision), not df_ref

Written as "df_ref must be unique" this would have broken two things the
module already does on purpose:

| case | must be | why |
|---|---|---|
| `create_revision()` raises REV.01 of DF-1808 | **allowed** | same drawing, next revision — the whole revision workflow |
| a soft-deleted BOM holds DF-1899 | **allowed** | a deleted document is not competing for the number; refusing would make delete a trap |
| editing a BOM without changing its reference | **allowed** | it must not clash with itself |
| a new BOM typed as DF-1805A when ABOM-10 has it | **refused** | the reported case |

Both of the first two would have been broken by the obvious
implementation, and neither would have surfaced until a user hit it.
`tests/dfref_probe.php` pins all four.

### duplicate() no longer copies the DF reference

This is a behaviour change worth stating plainly. A copy is a new
document for the next machine and will have its own drawing number;
inheriting the source's is exactly what puts two BOMs on one drawing.
Carrying it forward would also have landed every clone in breach of the
new rule the moment it was created, blocking the first edit for a reason
the operator did not cause.

Nothing is lost — the copy's note still records what it came from, and
`Abom_exporter::filename()` already falls back to the BOM number until a
reference is typed.

### Checked while the field has focus

`Abom::check_df_ref()` answers on blur and on a 500 ms pause in typing —
not per keystroke, since "DF-1" and "DF-18" are not questions anybody
meant to ask. The answer names **which** BOM holds the reference, its
status, and links to it, because "this already exists" is never the
question anyone's next thought is.

The endpoint decides nothing. `save()` and `save_config()` re-run the
same model check before writing, so a stale answer on screen, JavaScript
off, or a hand-made POST cannot get a duplicate through. It is gated on
any of the three permissions rather than on `save`: it is a read, and a
checker typing in a field should not be told the module is unavailable.

### Verification

`tests/dfref_probe.php` — 19 assertions against the real
`Abom_model::df_ref_conflict()` over a fake register, covering the
revision case, the soft-delete case, self-exclusion, case-insensitivity
(the column collation is), whitespace, and a blank revision reading as
`00`.

---

## 0.23 The topbar chip row is gone

The bar carried one chip per configuration value — BOM number, model,
axes, tracks, speed, motion, every enabled feature gate, PLC family,
build variant, panel location, status. On a wide machine with four
feature gates on, that is twelve badges: it wrapped onto a second line
and crowded the action buttons. Twelve badges in a row read as
decoration rather than as information.

### Nothing was lost

Every value it showed is stated somewhere it belongs:

| was a chip | now read from |
|---|---|
| BOM no, revision, DF ref, model, motion, axes, tracks, speed, side | the document title strip |
| PLC family, build variant, panel location | the configuration sidebar |
| enabled feature gates | the configuration sidebar (**added**) |
| workflow status | the sign-off block |

The feature gates were the one real gap. The sidebar renders its feature
CHECKBOXES only while the panel is editable, so on a released document
the chip row was the only place they appeared — and they decide whether
whole groups of parts are on the sheet (the braking resistor, the I-mark
sensor, the perforation axis). A **read-only feature list** was added to
the sidebar's locked branch so that removing the chips could not quietly
drop them from an approved BOM.

It stays silent when no gates are enabled rather than printing "None":
an empty heading on a document is a question, not an answer.

### Removed, not orphaned

`Abom::header_chips()` (75 lines), the `chips` key in both
`generate_ajax()` and `render_document()`, and `applyChips()` in
`abom-generate.js` all went with it. `.config-chip` STAYS in the
stylesheet — the reference-BOM register, the config screens and the
guide still use it.

### Verification

`tests/render_view.php` in all three modes asserts zero `config-chip`
inside `#configBar`, and that the locked mode carries the four enabled
features as `.fr-item` badges while the generator carries its six
checkboxes and no readout.

---

## 0.24 Generate BOM straight from a build — `abom_012`

The Reference BOMs register listed the builds but the only way to
generate one was the generator's reference picker, which asks *"which DF
was this like?"*. Someone standing at the register has already answered
that. It gets worse as the list grows: searching a picker of forty
reference configurations to reach a build you are looking at is work the
screen should not create.

Each active row now carries **+ Generate BOM**, landing on
`/abom/generate?build=<id>` already configured for that build.

### Why this needed a schema change

The reference machine for a build genuinely was not stored anywhere:

- `abom_variant_rule` carries min/max **axes** and **speed**, because
  those select the build — but nothing about **tracks**, which select
  nothing and only drive quantities. A rule saying "6 axes at 90 PPM or
  above" tells you nothing about whether that machine runs 6 or 12
  tracks.
- `abom_variant.name` spells the machine out for **five** of the eleven
  builds (`SPM1200L 5 axis / 12 track / 100 PPM`) and not for the other
  six (`iQ-R high speed continuous`). Parsing a label that is only
  sometimes formatted that way is not a foundation.

So `abom_012_variant_reference.sql` adds `ref_axes`, `ref_tracks`,
`ref_speed_ppm`, `ref_motion_type`, `ref_machine_side` and backfills all
eleven from the reference configuration already verified against each
released DF. A build added next year gets these filled in with it and
the button is correct for it with no code change — which is the point.

### NULL is allowed, and says so

A build with no recorded machine still generates. It falls back to its
own selection rule (`min_axes`, else `max_axes`; `min_speed`, else
`max_speed`), and **tracks fall back to the module default because no
rule carries them**. The screen then states which values are defaults
rather than the build's own, so a starting point is never mistaken for a
fact.

### Only the build id travels

Not the whole configuration. A URL carrying eleven parameters is one
typo away from generating a different machine, and the machine is
derived server-side so a link cannot be edited into something that
reaches a different build.

Auto-detection is deliberately left **on** rather than pinning a variant
override. An override would stamp "Manual override" on an ordinary new
BOM, and — worse — would hide the case where the recorded machine and
the selection rules disagree. Instead the controller checks that the
generated sheet reached the build that was asked for, and says plainly
when it did not:

> ⚠️ This configuration does not select the FX5-1808 build. The machine
> recorded against it reaches FX5-1864 instead …

Retired builds get no button. Engineering took them out of service; the
sheet stays readable.

### Verification

`tests/build_prefill_probe.php` — 18 assertions driving the REAL
`Abom_master_model` (over a seed-backed fake `$db`, not the stub, since
the stub would only be testing a reimplementation) and the REAL engine.
Every one of the eleven builds is round-tripped: recorded machine →
engine → back to its own build, with a non-empty sheet. The five builds
that state their machine in their own name are cross-checked against the
migration, since name and `ref_*` were written at different times from
the same DF. The no-recorded-machine fallback is covered too.

```
FX5-1808   SPM1200L  6A / 12T / 100 PPM Inte    FX5-1808    29 lines  full
IQR-HS     SPM1200L  15A / 12T / 180 PPM Cont   IQR-HS      42 lines  full
…all 11 round-trip
```

`tests/seed_parser.php` also gained support for the
`ALTER TABLE … ADD COLUMN` + `UPDATE` pattern this migration uses — it
previously dropped assignments to columns no `INSERT` mentioned, which
would have made the migration look like it had done nothing.

---

## 0.25 Search and filters on the build register

`/abom/master_bom` listed every build with no way to narrow it. Six
controls now do, in the same server-side GET form `/abom/list` and
`/abom/master` already use — a third idiom on a third register would be
one more thing to learn for no gain, and a GET form means a narrowed
view is a URL somebody can bookmark or send on.

| control | source |
|---|---|
| free text | code, name, description, model, source DFs, panel location, recorded machine |
| family | `abom_plc_family` |
| machine model | **derived** from the builds present |
| panel location | **derived** from the builds present |
| flags | has conflicts / no ERP / to review / nothing to resolve |
| active | active / retired / both |

Only family comes from a table; model and panel location are read off
the builds that exist, so a value introduced by a future build is
filterable the day it lands with nothing to maintain. Both hide
themselves while only one value exists.

### The phrase trap

The first cut matched "every word appears somewhere". That is wrong here
in a way that is easy to miss, and the harness caught it:

> Build names read `SPM1200L 6 axis / 12 track / 100 PPM`. A search for
> **6 track** found the word "6" in *6 axis* and the word "track" in
> *12 track*, and returned a **12-track machine to somebody who asked
> for a 6-track one**.

The right rows were in the result, buried among wrong ones — the worst
kind of search failure, because it looks like it worked.

`search_variants()` is therefore **phrase-first**: if the typed string
appears verbatim in any build, only those are returned. Nothing else can
be what was meant. Only when no build contains the phrase does it fall
back to all-words-in-any-order, which is what keeps half-remembered
queries working — `1808 fx5` and `fx5 1808` both find FX5-1808 and
neither is a phrase in anything.

### Two results that look wrong and are not

Searching `1858` returns **FX5-1858 and FX5-JE**. FX5-JE is the retired
combined build and its `source_df` names every DF it absorbed, DF-1858
among them. Somebody chasing an old drawing number wants to know which
build took it over, retired or not. Setting **Active only** narrows it
to the live build.

Both of these were assertions I had written wrong, not defects — worth
recording, because the next person to read the test will have the same
first reaction.

### Filtered in PHP, not SQL

`get_variants()` is cached and shared with the generator's override
picker and with the engine. Pushing a `WHERE` into it would quietly
narrow those too: a filter on one screen must never change what another
screen can select. The register is also bounded by how many machine
types the business builds, not by how much work it has done — a very
successful decade is tens of rows.

### Verification

`tests/build_filter_probe.php` — 50 assertions over the real
`Abom::filter_variants()` and the real seeded register, plus the real
view. Covers each filter alone, filters combined (they must AND, never
OR), the phrase trap in both directions, that active + retired partitions
the register exactly, that the form round-trips its own state, and that
no matches renders an empty state rather than stranded table headers.

`abom_sel()` moved from a bare declaration inside `master_list.php` into
`abom_helper.php` on the way — two unguarded declarations of one name is
a fatal error waiting for the day somebody loads both views in one
request.

---

## 0.26 Version history — the snapshots were being kept and never read

`abom_bom_revision` has stored a full JSON snapshot of header and lines
since the module shipped: once when a BOM reaches procurement approval,
and again when it is superseded by a new revision. **Nothing had ever
read those rows.** The history was being kept and could not be looked
at.

Two screens now read it:

| route | what it is |
|---|---|
| `/abom/history/<bom_id>` | every revision of the document, newest first, with what changed between each |
| `/abom/version/<rev_id>` | one archived snapshot, rendered as the document it was |

Reached from a **History** button on any saved BOM. Both are READS,
gated on any of the three permissions like view and export — a checker
who can see a BOM can see how it got that way, and hiding the history
from the people asked to approve it would be the wrong way round.

### How a version chain is identified

`create_revision()` does not edit in place: it writes a NEW `abom_bom`
row carrying the same `bom_no` with the revision incremented, and marks
the old row superseded. So one document is several rows, and `bom_no`
ties them together. No `parent_id` column was invented — `bom_no` is
already unique per document by construction.

Opening the history from ANY revision shows the whole chain, because
"what changed" is a question about the document, not about the row
somebody happens to have open.

### The newest revision has no snapshot of itself

It is still being prepared, so nothing has photographed it. Without
handling that it would be the one version nobody could compare — the
version people ask about most. `live_snapshot()` builds the same shape
from the current rows.

### Lines are compared by MASTER ITEM, never by position

This is the whole risk of the feature. Inserting one row near the top of
a sheet would, compared by position, report every row below it as both
removed and added — burying a real removal in forty false ones.

A removal is the change that must never be missed: a part that has left
the sheet is the one most likely to stop a machine being built and the
least likely to be spotted by reading the new document on its own. So
removals are reported first and in red.

Hand-added rows have no `item_id`, so they are keyed on part number and
description together — either alone collides too readily on a parts
list.

### What the changelog reports

Grouped by kind, ordered by consequence: **removed → added →
configuration → quantities → part details → remarks.** Somebody who
reads only the top of the block has still read the part that could stop
a build.

"Part details" is the non-obvious one. A frozen line duplicates
description, part number and ERP code on purpose. If one moved between
revisions, the **master item was corrected and this revision picked the
correction up** — a change to what will be ordered, which must not be
left for a reviewer to spot by eye.

Family ids, variant ids and the features JSON are rendered as words. How
a row is stored is not what changed as far as anyone reviewing the
document is concerned.

### An archived version is a record, not a document

`/abom/version/<id>` withholds every control that would act on a live
BOM — no quantity inputs, no row buttons, no remarks, no workflow, no
Save. **Export and Print are withheld too, and that is the important
one:** a PDF printed from here would be indistinguishable from a current
BOM once it left the screen, and somebody would order from it. The
header reads `ARCHIVED · NOT FOR ISSUE`.

A snapshot that will not decode is reported as unreadable rather than
skipped — a version silently missing from a history is worse than one
marked broken — and never rendered as an empty BOM, since an empty parts
list reads as "nothing was required".

### Within-revision edits

The snapshots answer "what did REV.01 contain". `abom_audit_log`
answers "who changed what while REV.01 was being prepared", and each
version card carries that trail too. Either alone leaves an obvious
question open.

`abom_audit_log` stores old/new as JSON; `abom_audit_detail()` states
the fields that actually moved and stops at three, so one noisy row
cannot push the rest of the history off the screen.

### Verification

`tests/version_probe.php` — 45 assertions against the real
`Abom_revision_model::diff()`. The position-independence trap is pinned
in both directions (a row inserted at the top is **one** addition, not
five; reordering alone reports nothing), along with removals keeping
their old quantity, master-data corrections not being mistaken for new
parts, hand-added rows staying distinct when they share a part number,
and ids/JSON rendering as words.

`tests/render_view.php je archived` proves the archived screen carries
**zero** export links, print buttons, quantity inputs, row buttons,
remark fields, workflow blocks and save bars, against 3/1/29/58/29/1/1
on the live screen.

Two fixture bugs of my own were caught on the way — both cases where the
test helper derived `part_no` from the ERP code, so changing one changed
two fields and the assertion was measuring its own fixture.

---

## 0.27 Reference-BOM import

Every reference build so far reached the database as a hand-written SQL
seed. `/abom/import` lets the team add one from the released DF
spreadsheet itself.

### Why this is the most dangerous write path in the module

A saved BOM is one document, and a mistake in it affects one machine.
Master data is the catalogue **every future BOM is generated from**, so
a mistake here is silently wrong on every machine of that type from now
on — and nobody finds out until something is built short.

That shapes the whole design: **three steps, and the middle one writes
nothing.**

| step | what it does |
|---|---|
| `import()` | the form: the file, and the machine the DF was drawn for |
| `import_preview()` | parse, validate, infer, **show**. Writes nothing. |
| `import_commit()` | re-reads the staged file and writes, once, in a transaction |

The commit **re-parses from the same bytes** rather than trusting what
the browser posts back. The preview is a picture of the file; the import
must come from the file, or a stale or tampered form could write rows
nobody reviewed. Only three things can be changed at preview — the
quantity rule, the optional flag, the feature gate. Descriptions, part
numbers, ERP codes and quantities come from the sheet and nowhere else,
or the imported catalogue would no longer match the document it claims
to be from.

### The hard part is not parsing, it is the quantity formulas

A spreadsheet cell holds **7**. The module needs to know whether that is
a fixed seven, one per axis on a seven-axis machine, or the twelve-track
temperature-card rule that happens to come to seven. Import it as FIXED
and the build is right for the machine it came from and **silently
wrong for every other size** — a nine-track machine gets seven cards
instead of six, and nobody knows until the panel is wired.

Two signals, in this order:

1. **What the part is.** A temperature card is track-driven whatever
   number sits beside it. This is the stronger signal and it decides.
2. **What the number matches.** Which formulas would have produced
   exactly this quantity for the stated reference machine.

Where the number fits several rules and the description says nothing,
the row comes back **not confident**, with every candidate listed and
`FIXED` selected — the only choice that cannot be wrong on a different
machine size. The screen asks; the model never guesses.

A part that is unmistakably track-driven but whose number disagrees with
the declared machine is also refused confidence, naming both numbers:
either the machine above is wrong or the quantity is, and neither can be
assumed.

### The machine matters as much as the file

Every rule is inferred against the machine typed on the form, so a
correct spreadsheet with the wrong track count above it produces a wrong
catalogue. The form says so, in a bordered block, next to those fields.

### A build with no selection rule is never used

That failure is silent — the register would list the build, the items
would be there, and every generated BOM would quietly pick a different
one. So the rule is written **in the same transaction**, never as a
follow-up step someone might skip. It is added **last in priority
order**, so it cannot take machines away from a build that already
claims them.

### Verification: the round trip

`tests/import_probe.php` — 60 assertions. The strongest is a round trip
against the module's own data: take builds the seed already has, work
out what their released sheet would have shown for each item, and check
the importer independently arrives at the **same formula the seed was
hand-authored with**.

```
BUILD       ITEMS   AGREED     ASKED DISAGREED  MACHINE
FX5-1808       14       12         2         0  6A/12T
FX5-1858       10        8         2         0  5A/6T
IQR-HS         24       13        11         0  15A/12T
IQR-TCF        20       12         8         0  11A/6T
FX5-1778       14       12         2         0  7A/8T
IQR-FLM        23       13        10         0  15A/9T

105 rows, 70 agreed, 35 asked, 0 silently disagreed
```

**Zero silent disagreements** is the property that matters. A third of
rows are put in front of a person, which is the correct trade: a
question costs a moment, a wrong guess costs a machine.

Also covered: a title block above the headings, section heading rows,
blank spacers, alternative column spellings, and every file that must be
refused — no heading row, missing QTY, headings with no data. Fractional
and zero quantities are **refused, never rounded**.

### One thing found while testing

PHPExcel's `createReaderForFile()` probes every reader, which loads
`Reader/Excel5.php` — and that file uses curly-brace string offsets,
removed in PHP 8. On a PHP 8 host that is a fatal error while merely
*looking at* an `.xlsx`. The reader is now chosen by extension, so
`.xlsx` and `.csv` work regardless of PHP version, and the one genuinely
unsupported case (`.xls` on PHP 8) says so and tells the operator to
re-save. Production is 7.4, where all three work.

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

| Class | Variable | Rows (of 178) |
|---|---|---|
| `is-conflict` | `--red` | 8 |
| `is-noerp` | `--orange` | 9 |
| `is-optional` | `--lgreen` | 15 |
| `is-manual` | `--lblue` | 68 |
| *(no modifier)* | — | 78 |

`review` severity produces **no row tint**. It applies to 82 of 178
master items; tinting them would put a large fraction of a
customer-signed document into a warning colour and would collapse
`is-manual` to the handful of rows carrying no severity. Review surfaces as a
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
php BOMMODULEDEVELOPMENT/tests/run_tests.php         # spec §5.1 suite
php BOMMODULEDEVELOPMENT/tests/dump_reference.php    # Appendix B line lists
php BOMMODULEDEVELOPMENT/tests/reconfigure_probe.php # save_config carry-over
php BOMMODULEDEVELOPMENT/tests/render_list.php       # saved-BOM register
php BOMMODULEDEVELOPMENT/tests/dfref_probe.php       # DF reference uniqueness
php BOMMODULEDEVELOPMENT/tests/build_prefill_probe.php  # Generate-from-build
php BOMMODULEDEVELOPMENT/tests/build_filter_probe.php   # build register filters
php BOMMODULEDEVELOPMENT/tests/version_probe.php     # version diff / changelog
php BOMMODULEDEVELOPMENT/tests/import_probe.php      # reference-BOM import
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

1. ~~**Wire `AUTOMATION BOM MASTER ITEMS` before any master-item screen
   ships.**~~ **DONE, 2026-08-11.** The master-item screens shipped at
   `/abom/master`, and all four entry points — `master`, `master_form`,
   `master_save`, `master_toggle` — call `require_perm('master_edit')`.
   Verified by `tests/guard_matrix.php`, which reports the permission
   against each of them. The grant now confers what its name says.

   The six CONFIGURATION tables — build variants, variant selection
   rules, PLC family rules, sections, feature gates and quantity formulas
   — followed at `/abom/config`, driven from one descriptor in
   `Abom_config_model` so all six share one validated write path. Adding
   a new build is no longer a migration.

   `abom_formula` is metadata-only by design: a formula CODE is
   dispatched on in `Abom_engine::calc_qty()`, and a code the engine has
   no case for falls through to `default` and silently behaves as FIXED.
   Offering an Add button there would offer a quantity rule that quietly
   does nothing, so rows cannot be added or removed — only reworded.

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
