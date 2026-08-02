# Automation BOM Generator — rollout

Someone will be doing this at 9pm. Every step is explicit, ordered, and
has a check. Nothing here is reversible-by-accident.

**Rule 12: never sync this working tree to the server.** It is an
incomplete copy of production — `application/controllers/BOM.php`,
`application/views/BOM/` and possibly more exist there and not here. A
wholesale `rsync` would delete them. Copy the 28 files listed in step 4
**by name**, and nothing else.

---

## The safe order — read this first

The module is **unreachable by every user until a grant exists** — both
the menu item and the URL — and `abom_003_permissions.sql` deliberately
creates none. An ungranted user who types `/abom/generate` is redirected
to the Dashboard with a permission message; an ungranted POST to
`/abom/save` gets a 403 and writes nothing. Read paths (viewing, listing
and exporting an existing BOM) stay open to any logged-in user, because an
approver holding only `APPROVALS` must be able to open the document they
are approving.

That gives a rollout where nothing is reachable by the business until
everything has been verified on the real server.

| | Do | Visible to |
|---|---|---|
| **1** | Deploy everything — tables, seed, permissions (zero grants), files, config, routes, **and the nav snippet** | **nobody** |
| **2** | Grant **one super admin only**, then run `POST_DEPLOY_CHECK.md` | that one person |
| **3** | Print check — both PDFs against the released DF sheet | nobody |
| **4** | Fill in `PERMISSIONS_WORKSHEET.md` and grant the real users | **the business** |
| **5** | Later, on a different day: `abom_004_views_invoker.sql` | nobody |

Steps 1–3 are invisible to normal users. **Step 4 is the first moment
anything changes for anyone** — by which point it has all been exercised
on the real server, on real data, by a real person.

Step 5 is a **separate change with nothing to do with this module.** Do
not bundle it: two unrelated changes in one window means an unexplained
symptom has two candidate causes, and someone backs out the wrong one.

**Apply the nav snippet in step 1, not later.** With zero grants nobody
can see the menu item anyway, so it changes nothing for anyone — and it
keeps step 14 of `POST_DEPLOY_CHECK.md` a real test rather than a skipped
one. Verified during the rehearsal: with the snippet applied and no grants
in place, the item did **not** render; after a grant was inserted, it did.

There is **no point of no return in steps 1–3.** The Rollback section
undoes all of it, and it has been rehearsed.

---

## Before you start

| | |
|---|---|
| Take a database backup | **See the box below — a plain `mysqldump` silently truncates on this database.** |
| Confirm PHP | must be **7.4** (`ea-php74`). The module is 7.4-safe; CodeIgniter 3.1.4 is not PHP 8 clean. |
| Confirm ext-zip | **Run the probe below.** Only needed for Excel export; CSV and PDF work without it. |
| Confirm MariaDB | 11.x. Proven against 11.4. |
| **Confirm no other deployment is in flight** | **See the box below.** Ask before you start, not after. |

### Is anyone else deploying tonight?

This module **appends** to two files that every feature shares:

- `application/config/routes.php`
- `application/views/common/nav-menu.php`

Almost any other feature touches the same two. If two people deploy on the
same evening, both appending, **one set of changes silently overwrites the
other** — and the symptom is not an error, it is a menu item or a route
that quietly stops existing.

Parallel work was in progress on `Df_dispatch_plan` while this module was
built: its controller, model, view and a new migration were all being
edited in the same working folder. That is normal, and not a problem in
itself — but it is exactly the kind of change that also edits those two
files.

**Before you start, ask whoever else is deploying: who goes first?** Agree
an order and do not do both the same night. It takes five minutes and it
is the single most likely way this goes wrong.

The expected-diff check at the end of step 4 **will** catch a collision if
one happens. Knowing to look is better than discovering it.

### Database backup — and why the obvious command fails

A plain `mysqldump` **aborts partway through this database** and leaves a
file that looks like a backup:

```
mysqldump: Got error: 1449: "The user specified as a definer
('u537620103_shuser'@'127.0.0.1') does not exist" when using LOCK TABLES
```

The database contains **6 views** whose `DEFINER` may not resolve in the
context you run the dump from. In the rehearsal this produced an
**870-byte file** — and nothing said so. The entire rollback plan rests
on this backup, so it is verified, not assumed.

```bash
mysqldump -u USER -p --single-transaction --quick DBNAME > backup_before_abom.sql
```

`--single-transaction` avoids `LOCK TABLES` and so avoids the definer
error. On MariaDB 11 the binary may be named `mariadb-dump`; either name
works, `mysqldump` is normally a symlink.

**Verify it before going further:**

```bash
ls -lh backup_before_abom.sql
grep -c '^CREATE TABLE' backup_before_abom.sql
tail -1 backup_before_abom.sql
```

- Size must be **tens of megabytes**, not kilobytes.
- `CREATE TABLE` count should be around **287** (293 objects, 6 of which
  are views and appear as `CREATE VIEW`).
- The last line must read `-- Dump completed on ...`. If it does not, the
  dump was truncated. **Do not proceed.**

### Back up the two existing files this module modifies

These are the **only** two files outside the module's own that change.
Two `cp` commands now are the difference between a five-minute rollback
and an outage.

```bash
STAMP=$(date +%Y%m%d-%H%M%S)
cp application/config/routes.php            application/config/routes.php.bak-$STAMP
cp application/views/common/nav-menu.php    application/views/common/nav-menu.php.bak-$STAMP
ls -la application/config/routes.php.bak-* application/views/common/nav-menu.php.bak-*
```

Record `$STAMP`. The rollback section needs it.

### ext-zip probe

The XLSX writer needs `ZipArchive`. The module degrades gracefully
without it — a 503 and a page offering CSV and PDF — but that is a
mitigation, not a fix. Check before you start:

```bash
php -m | grep -i '^zip$' && echo "zip: OK" || echo "zip: MISSING"
```

Or, if you only have browser access, put this in a temporary file at the
web root, load it, then delete it:

```php
<?php echo extension_loaded('zip') ? 'zip: OK' : 'zip: MISSING';
```

If missing: cPanel → **Select PHP Version** → **Extensions** → tick
`zip`. No restart needed on cPanel. Re-run the probe to confirm.

---

## Step 1 — pre-flight queries (read-only, safe on production)

```sql
SELECT COUNT(*) AS abom_tables FROM information_schema.tables
  WHERE table_schema = DATABASE() AND table_name LIKE 'abom\_%';
SELECT MAX(id) AS max_submodule_id FROM submodule;
SELECT id, moduleid, submodule, status FROM submodule WHERE id >= 70 ORDER BY id;
SELECT id, modulename FROM system_modules WHERE id = 4;
```

**`abom_tables` must be 0.** If it is not, STOP — something already owns
that prefix.

You do **not** need `max_submodule_id` for anything — step 3 asks the
database for the ids it assigns. The query is here only so you can see
the table's state before and after.

---

## Step 2 — schema and seed

Run in this order. Both only ever create or populate `abom_*` tables.

```bash
mysql -u USER -p DBNAME < Database/abom_001.sql
mysql -u USER -p DBNAME < Database/abom_002_seed.sql
```

**Check:**

```sql
SELECT (SELECT COUNT(*) FROM abom_item) AS items,
       (SELECT COUNT(*) FROM abom_item WHERE plc_family_id=1) AS fx5,
       (SELECT COUNT(*) FROM abom_item WHERE plc_family_id=2) AS iqr,
       (SELECT COUNT(*) FROM abom_section) AS sections,
       (SELECT COUNT(*) FROM abom_plc_rule WHERE is_active=1) AS rules,
       (SELECT COUNT(*) FROM abom_feature) AS features,
       (SELECT COUNT(*) FROM abom_formula) AS formulas;
```

Must be exactly **`71 · 29 · 42 · 8 · 4 · 4 · 6`**. Anything else, stop.

`abom_001.sql` uses plain `CREATE TABLE`, so a name collision fails
loudly rather than overwriting. It is safe to run once and only once.

---

## Step 3 — permissions

```bash
mysql -u USER -p DBNAME < Database/abom_003_permissions.sql
```

The script **never hardcodes an id.** `submodule.id` is `AUTO_INCREMENT`,
so it inserts, asks the database what it assigned, and prints the result.
It works the same on a scratch copy and on production.

It pre-flights itself and **aborts without changing anything** if module 4
is missing, if step 2 has not run, or if it has already been run — so it
is safe to re-run by mistake.

Output looks like this. **Copy it; you need it in step 5.**

```
PASTE THIS INTO application/config/abom.php
$config['abom_submodule_ids'] = array(
    'generator'    => 907,
    'approvals'    => 908,
    'master_items' => 909,
);

generator  approvals  master_items  grant_rows_created  note
907        908        909           27                  Grants mirrored from the existing module 4 submodule.
```

Your numbers will differ — that is the point.

**Deciding who gets what:** use
`BOMMODULEDEVELOPMENT/PERMISSIONS_WORKSHEET.md`. It lists your users by
name, role and department (not ids), explains in one sentence what each
permission allows and what over-granting risks, and gives the exact
INSERT statements. Five minutes.

### Grants — the script creates NONE, on purpose

`grant_rows_created` will be **0**. Nobody acquires the authority to
approve an automation BOM because a script assumed it.

The script's third output is a set of **commented-out** `INSERT`
statements, one per user who already holds the existing BOM Correction
Tool submodule, annotated with their name. That list is a **starting
point for review, not a recommendation** — those people were granted a
different feature for different reasons.

Decide three separate lists. They should not be the same:

| Submodule | Who belongs on it |
|---|---|
| `AUTOMATION BOM GENERATOR` | Engineering — whoever builds BOMs. Usually the longest list. |
| `AUTOMATION BOM APPROVALS` | The reviewers and approvers. **Read the stage-authority note below first.** |
| `AUTOMATION BOM MASTER ITEMS` | **The smallest list of the three.** |

**Why `MASTER ITEMS` is the tightest.** Approving a BOM affects one
document, and it is signed, dated and recorded in the approval trail.
Editing a master item silently changes what **every future BOM
generates** — a corrected quantity or ERP code propagates into every BOM
generated afterwards, with no signature on it and nobody reviewing the
change. It is the widest blast radius of the three and the least obvious.

> **Separation of duty is enforced on the transition, not the grant.**
> Granting one person `APPROVALS` does **not** let them approve a BOM
> alone. Two rules apply, regardless of what the grants say:
>
> 1. No user may perform two consecutive forward transitions on the same
>    BOM.
> 2. Whoever created the BOM cannot give it the final approval.
>
> So a released BOM has been touched by at least **two** distinct people,
> normally three. Grants decide who is in the pool; these rules decide
> the floor.
>
> Governed by `$config['abom_require_distinct_approvers']`, default
> `TRUE`, failing closed. Setting it `FALSE` restores the older behaviour
> where one approver does all three stages — a deliberate written
> decision, not an accident. See MODULE_CHANGELOG.md §0.9.2.
>
> Practical consequence for your grant lists: `APPROVALS` needs **at
> least two people** or no BOM can ever be released.

Note: **`module_capablity.role_id` holds a user id**, not a role id,
despite the name. The whole application works that way — see
MODULE_CHANGELOG.md §0.9.

Until at least one `APPROVALS` grant exists, the module generates, saves
and views BOMs normally; only the approval controls are unavailable.

**Check:**

```sql
SELECT id, moduleid, submodule, status FROM submodule
 WHERE moduleid = 4 ORDER BY id;
```

---

## Step 4 — copy the module files

**28 files. By name. Nothing else.** Checksums are in
`BOMMODULEDEVELOPMENT/tests/manifest.json`; verify after copying with
`php BOMMODULEDEVELOPMENT/tests/integrity.php` run from a checkout.

```
application/config/abom.php
application/controllers/Abom.php
application/helpers/abom_helper.php
application/libraries/Abom_engine.php
application/libraries/Abom_exporter.php
application/libraries/Abom_permission_guard.php
application/models/Abom_approval_model.php
application/models/Abom_item_model.php
application/models/Abom_master_model.php
application/models/Abom_model.php
application/views/abom/_approval_block.php
application/views/abom/_config_panel.php
application/views/abom/_doc_header.php
application/views/abom/_document.php
application/views/abom/_legend.php
application/views/abom/_table.php
application/views/abom/_workflow.php
application/views/abom/export_unavailable.php
application/views/abom/generate.php
application/views/abom/guide.php
application/views/abom/list.php
application/views/abom/not_configured.php
application/views/abom/not_installed.php
application/views/abom/view.php
assets/abom/abom-generate.js
assets/abom/abom-print.css
assets/abom/abom.css
assets/abom/abom.js
```

Two new directories: `application/views/abom/` and `assets/abom/`.

**Do not hand-copy 28 paths.** That is 28 chances to mistype one. Build a
tarball from a checkout and unpack it on the server — the file list then
cannot drift from what was tested:

```bash
# on the machine holding the module, from the project root
tar czf abom-module.tar.gz   application/config/abom.php   application/controllers/Abom.php   application/helpers/abom_helper.php   application/libraries/Abom_engine.php   application/libraries/Abom_exporter.php   application/libraries/Abom_permission_guard.php   application/models/Abom_approval_model.php   application/models/Abom_item_model.php   application/models/Abom_master_model.php   application/models/Abom_model.php   application/views/abom   assets/abom

tar tzf abom-module.tar.gz | wc -l     # expect 28 files + 2 directory entries
```

Upload `abom-module.tar.gz`, then on the server, **from the project
root**:

```bash
tar xzf abom-module.tar.gz
rm abom-module.tar.gz
```

`tar` preserves the paths and the casing, which is the whole risk this
step carries. If you must use a File Manager instead, copy directory by
directory rather than file by file.

**Case matters.** The server is case-sensitive; this Mac is not.
`Abom.php` not `ABOM.php`; `views/abom/` not `views/Abom/`. Nothing here
may be uploaded as `BOM.php` or into `views/BOM/` — those are the
existing module's and must not be touched.

### Collision check — run this BEFORE copying anything

All 28 are **new** files. If any of those names already exists on the
server, copying would overwrite something. This checks the server as it
is right now, and changes nothing:

```bash
# from the project root on the SERVER, before unpacking
COLLIDE=0
for f in \
  application/config/abom.php \
  application/controllers/Abom.php \
  application/helpers/abom_helper.php \
  application/libraries/Abom_engine.php \
  application/libraries/Abom_exporter.php \
  application/libraries/Abom_permission_guard.php \
  application/models/Abom_approval_model.php \
  application/models/Abom_item_model.php \
  application/models/Abom_master_model.php \
  application/models/Abom_model.php \
  application/views/abom \
  assets/abom ; do
  if [ -e "$f" ]; then echo "COLLISION: $f already exists"; COLLIDE=1; fi
done
[ "$COLLIDE" -eq 0 ] && echo "No collisions - safe to unpack" || echo "STOP - do not unpack"
```

**If anything collides, stop and change nothing.** Report every
collision. Do not rename, do not overwrite, do not merge — a name clash
means something is already using that name and the situation needs
understanding first.

This replaces the "get a production file listing" step that earlier
drafts asked for. It is strictly stronger: a listing tells you what was
there whenever it was taken, this tells you what is there at the moment
you copy. A listing (`find application -type f | sort`) is still useful
hygiene for other reasons, but it is **not a prerequisite** for this
deployment — under rule 12 nothing that exists only on the server is ever
touched, so the gap between server and local is irrelevant here. The only
thing that mattered was collision, and this covers it.

### The two modified files — expected diff

`routes.php` and `nav-menu.php` are the only existing files that change.
After editing, confirm the diff is **only yours**:

```bash
diff application/config/routes.php.bak-$STAMP application/config/routes.php
diff application/views/common/nav-menu.php.bak-$STAMP application/views/common/nav-menu.php
```

Expected for `routes.php`: **16 added lines and nothing else** — the
comment block plus 16 `$route['abom...']` entries, all appended at the
end. No deletions, no changes above them.

Expected for `nav-menu.php`: **one added block** — the `<?php ... ?>`
gate plus one `<li>` for "Automation BOM Generator", roughly 30 lines, in
one place. No deletions.

Anything else in either diff is somebody else's change. Stop and find out
whose before proceeding.

---

## Step 5 — set the submodule ids in config

Edit `application/config/abom.php` on the server, filling in the three
ids from step 3:

```php
$config['abom_submodule_ids'] = array(
    'generator'    => 74,   // <- the real id
    'approvals'    => 75,   // <- the real id
    'master_items' => 76,   // <- the real id
);
```

They ship as `null` on purpose. Left unset, the module still generates,
saves and views BOMs, and the approval panel shows a specific
configuration diagnostic instead of an empty toolbar.

Other values worth knowing, all with working defaults:

| Key | Default | Meaning |
|---|---|---|
| `abom_signoff_mode` | `'customer'` | **3 printed boxes** — Prepared By (Engineering) / Checked By / Approved By (Customer). `'internal'` gives 4, splitting the last into Engineering and Procurement. |
| `abom_bom_no_format` | `'ABOM-%d'` | Own series; does not touch the DF register. |
| `abom_pdf_font` | `'dejavusans'` | **Must stay a Unicode font** — see MODULE_CHANGELOG.md §0.8.1. |
| `abom_uom_display` | `NOS → NO(S)` | Human-readable output only. |

---

## Step 6 — routes

Append to the **end** of `application/config/routes.php`. Append only —
change nothing above it.

```php
/*
| -------------------------------------------------------------------------
| Automation BOM Generator  (application/controllers/Abom.php)
| -------------------------------------------------------------------------
*/
$route['abom']                  = 'abom/index';
$route['abom/guide']            = 'abom/guide';
$route['abom/generate']         = 'abom/generate';
$route['abom/generate_ajax']    = 'abom/generate_ajax';      // POST, AJAX
$route['abom/reference/(:any)'] = 'abom/reference/$1';
$route['abom/view/(:num)']      = 'abom/view/$1';
$route['abom/print/(:num)']     = 'abom/printable/$1';
$route['abom/save']             = 'abom/save';                // POST, AJAX
$route['abom/save_line_qty']    = 'abom/save_line_qty';        // POST, AJAX
$route['abom/list']             = 'abom/bom_list';

// Approval workflow — all POST, AJAX
$route['abom/submit/(:num)']          = 'abom/submit/$1';
$route['abom/approve/(:num)']         = 'abom/approve/$1';
$route['abom/reject/(:num)']          = 'abom/reject/$1';
$route['abom/reopen/(:num)']          = 'abom/reopen/$1';
$route['abom/create_revision/(:num)'] = 'abom/create_revision/$1';
$route['abom/acknowledge_line']       = 'abom/acknowledge_line';

// Export — csv | xlsx | pdf
$route['abom/export/(:any)/(:num)']   = 'abom/export/$1/$2';
```

**Check:** `https://YOURHOST/index.php/abom/generate` loads.

`application/config/autoload.php` does **not** need changing at any
point — the module config is loaded explicitly by the controller.

---

## Step 7 — navigation

The snippet is in **`BOMMODULEDEVELOPMENT/NAV_SNIPPET.md`**. Paste it
into `application/views/common/nav-menu.php` at **line 1352** — between
the `<?php }?>` that closes the M/cs Dispatch Report block (line 1350)
and the `</ul>` at line 1354.

It contains **no literal submodule id** — it reads the id from
`application/config/abom.php`, so it is correct on every environment with
no edit. If step 5 has not been done, no menu item renders rather than a
broken one.

Verified by pasting it at that exact line in the sandbox: the file lints,
the item renders, the rest of the menu is unchanged, zero PHP errors.

**The line numbers are a hint, not the anchor.** 1350/1352/1354 are
correct for `nav-menu.php` as it stands today. If anyone has edited that
file since, they will have moved. Anchor on the **text** instead: find the
last `<?php }?>` before the `</ul>` that closes the main menu list — the
one immediately after the "M/cs Dispatch Report (Accounts)" item — and
paste between them.

**Check:**

```bash
php -l application/views/common/nav-menu.php
```

Then load any page.

> **If no menu item appears, that is expected until a grant exists.**
> Step 3 deliberately grants nobody. The item renders only for a user who
> holds the `AUTOMATION BOM GENERATOR` grant, so if you have not yet
> inserted any `module_capablity` rows, the menu is unchanged and nothing
> is wrong. Insert a grant for yourself and reload.
>
> The module is still reachable directly at `/index.php/abom/generate`
> regardless — the menu item is navigation, not access control.

---

## Step 8 — verify

In order. Stop at the first failure.

| # | Do | Expect |
|---|---|---|
| 1 | `/index.php/abom/generate` | Loads, 42 rows, iQ-R detected |
| 2 | Click the DF-1827 preset | Redraws to **29 rows**, family FX5 |
| 3 | Change Axes 15 → 8 | SSCNET 0.5 M quantity follows to 7 |
| 4 | Untick Perforation Axis (on FX5) | **4 lines disappear**, 29 → 25 |
| 5 | Set Axes to 99 | Field error, no silent clamping |
| 6 | Save BOM | Redirects to a saved `ABOM-n` |
| 7 | `/index.php/abom/list` | The saved BOM is listed |
| 8 | Open it, change a quantity, **reload** | The change survived, ✎ shown |
| 9 | Try to advance to Submitted | Blocked, citing MANUAL and conflict lines |
| 10 | Confirm the MANUAL lines, acknowledge conflicts | Blockers clear |
| 11 | Advance through all four stages | Second stage refuses the preparer; trail records each step |
| 12 | On the approved BOM | Quantity inputs gone; server returns 403 if forced |
| 13 | Export CSV, PDF, Excel | Download; PDF shows `6.7Ω, 500W` and `NO(S)`, never `?` |
| 14 | Print preview the BOM | Sidebar and workflow panel hidden, 3 sign-off boxes, colours print |
| 15 | Load Dashboard, DF Dispatch, Task Management | Unchanged, no PHP errors |
| 16 | **Confirm the dev login shim is absent** — see below | 404 and an empty grep |

### Step 8b — confirm the dev shim is not reachable

A sandbox-only controller, `Abomdevlogin.php`, bypasses authentication
entirely. It is not in the step 4 file list, it does not exist in the
working tree, and teardown deletes it — but "it isn't in the list" is a
promise about the list, and the risk is someone copying a folder instead.
So confirm its absence **positively**, not by assumption.

**1. The URL must 404:**

```bash
curl -s -o /dev/null -w "%{http_code}\n" https://YOURHOST/index.php/abomdevlogin
```

Must print `404`. Anything else — 200, 302, 500 — means the file is
present and reachable. Delete it immediately and check the access log for
prior hits.

**2. No file of that name, any casing:**

```bash
ls application/controllers/ | grep -i abomdevlogin
find application -iname '*abomdevlogin*'
```

Both must print **nothing**. The case-insensitive match matters: the
server is case-sensitive, so `AbomDevLogin.php` would be a different file
that CI would still route to.

Three independent things would have to fail for an auth bypass to be
reachable: the file would have to be copied, `ENVIRONMENT` would have to
not be `production`, and a `.abom-sandbox` marker file would have to exist
beside `index.php`. The guard **fails closed** — any unknown, unreadable
or ambiguous signal denies, including a stale filesystem stat.

---

## If something goes wrong

| Symptom | Cause | Fix |
|---|---|---|
| "tables are not installed" | step 2 not run | run the two SQL files |
| Approval panel shows a configuration diagnostic | step 5 not done | set `abom_submodule_ids` |
| Approval buttons disabled, no diagnostic | role lacks the grant | add the `module_capablity` row |
| Excel export shows "export unavailable" | ext-zip off | enable it, or use CSV/PDF |
| PDF shows `6.7?, 500W` | `abom_pdf_font` changed to a core font | set it back to `dejavusans` |
| 404 on `/abom/...` | step 6 not done | append the routes |
| Blank page, no error | PHP 8 on the server | the app needs `ea-php74` |

---

## Rollback

The whole premise of this module is that the existing software is not
affected. The honest form of that guarantee is a documented undo, not
just a careful install.

Rehearsed in the sandbox: the database and file tree return to their
pre-install state. Run the steps in this order.

### R1 — remove the module's own files

The same explicit list used in step 4. By name, not by folder.

```bash
rm -f  application/config/abom.php \
       application/controllers/Abom.php \
       application/helpers/abom_helper.php \
       application/libraries/Abom_engine.php \
       application/libraries/Abom_exporter.php \
       application/libraries/Abom_permission_guard.php \
       application/models/Abom_approval_model.php \
       application/models/Abom_item_model.php \
       application/models/Abom_master_model.php \
       application/models/Abom_model.php
rm -rf application/views/abom
rm -rf assets/abom
```

**Check:**

```bash
ls application/views/abom assets/abom          # expect: No such file or directory
ls application/controllers/ | grep -i abom     # expect: NOTHING AT ALL
find application assets -iname '*abom*'        # expect: nothing
```

**A match on the second or third command means investigate, not
continue.** In the rehearsal these checks caught two files that should not
have been there:

- `application/controllers/Abom_model.php` — a *model* sitting in
  `controllers/`, from a careless copy. CI would have tried to route
  `/abom_model` to it.
- `application/controllers/Abomdevlogin.php` — the sandbox dev login
  shim, which **bypasses authentication entirely**. If this is on a
  server, delete it immediately and check the access log for hits on
  `/abomdevlogin`.

Anything matching `abom` that is not in the step 4 list is a stray.
Delete it.

### R2 — restore the two modified files from the step 0 backups

```bash
cp application/config/routes.php.bak-$STAMP         application/config/routes.php
cp application/views/common/nav-menu.php.bak-$STAMP application/views/common/nav-menu.php
```

**Check:** `grep -c abom application/config/routes.php` → `0`.
`php -l application/config/routes.php` and
`php -l application/views/common/nav-menu.php` → no syntax errors.

If the backups were not taken, remove the appended block from
`routes.php` by hand (everything from the
`| Automation BOM Generator` comment to the end of file) and remove the
nav snippet from `nav-menu.php` — but take the backups.

### R3 — remove the permissions

Use the ids recorded at install. **Do not use a wildcard on
`module_capablity`** — deleting by `moduleid = 4` alone would remove the
existing BOM Correction Tool grants too.

```sql
-- Confirm what you are about to delete, and note the ids.
SELECT id, moduleid, submodule FROM submodule
 WHERE submodule LIKE 'AUTOMATION BOM %';

-- Grants first (child rows), then the submodules.
DELETE FROM module_capablity
 WHERE moduleid = 4
   AND submoduleid IN (SELECT id FROM submodule WHERE submodule LIKE 'AUTOMATION BOM %');

DELETE FROM submodule WHERE submodule LIKE 'AUTOMATION BOM %';
```

**Check:** both queries return 0 rows afterwards, and the existing
submodule 27 grants are untouched:

```sql
SELECT COUNT(*) AS should_be_unchanged FROM module_capablity
 WHERE moduleid = 4 AND submoduleid = 27;
```

### R4 — drop the module's tables

All eleven named explicitly. **No wildcard** — a `LIKE 'abom%'` loop is
how the wrong table gets dropped.

```sql
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `abom_audit_log`;
DROP TABLE IF EXISTS `abom_bom_approval`;
DROP TABLE IF EXISTS `abom_bom_revision`;
DROP TABLE IF EXISTS `abom_bom_line`;
DROP TABLE IF EXISTS `abom_bom`;
DROP TABLE IF EXISTS `abom_item`;
DROP TABLE IF EXISTS `abom_plc_rule`;
DROP TABLE IF EXISTS `abom_section`;
DROP TABLE IF EXISTS `abom_feature`;
DROP TABLE IF EXISTS `abom_formula`;
DROP TABLE IF EXISTS `abom_plc_family`;
SET FOREIGN_KEY_CHECKS = 1;
```

**Check:**

```sql
SELECT COUNT(*) AS abom_tables FROM information_schema.tables
  WHERE table_schema = DATABASE() AND table_name LIKE 'abom\_%';
```

Must be **0**.

### R5 — confirm the application is unaffected

Load three existing pages and confirm HTTP 200 with no PHP errors:
Dashboard, DF Dispatch Morning Meeting, Task Management.

Nothing outside the `abom_` prefix and the two backed-up files is touched
at any point, so no existing data is at risk. The `mysqldump` from step 0
remains the backstop.

---

## Print check — the one thing no container can verify

Two reference BOMs have been generated from a freshly installed sandbox
and exported to PDF, ready to print:

| File | Configuration | Items | Total qty |
|---|---|---|---|
| `DF-1827_FX5_29items.pdf` | FX5 · 8 axes / 12 tracks / 140 PPM / Intermittent / LHS | **29** | 74 |
| `DF-1826_iQ-R_42items.pdf` | iQ-R · 15 axes / 12 tracks / 180 PPM / Continuous | **42** | 129 |

Both are **landscape A4** (841.89 × 595.276 pt), 2 pages, and were
generated with **`abom_signoff_mode = 'customer'`** — so each carries
**three** sign-off boxes: Prepared By (Engineering), Checked By, Approved
By (Customer). If the paper in your hand has four boxes, it was generated
in `'internal'` mode and is a different document.

Verified mechanically before printing: 29 and 42 `NO(S)` cells
respectively, **zero** `?` characters, and the `Ω` in
`DYNAMIC BRAKING RESISTOR (DBR), 6.7Ω, 500W` present in the iQ-R sheet.
`Ω` is correctly absent from the FX5 sheet — the braking resistor is an
iQ-R item.

**On the department's actual printer, hold each next to the released DF
sheet and check:**

- Page breaks — no section header stranded at the foot of a page.
- Column widths — nothing truncated, especially DESCRIPTION and REMARKS.
- The three signature boxes are present and large enough to sign.
- `6.7Ω` reads as an omega, not a box, a question mark or a gap.
- Row background colours print (they are meaningful, not decorative).
- The footer carries BOM number, revision, page x of y and a timestamp.

---

## Ongoing administration

Grants are per **user**, not per **role** — `module_capablity.role_id`
holds a `system_users.user_id`. **Access therefore does not follow a job
change.** A new engineer has no access until someone inserts rows; a
departing one keeps approval rights until someone deletes them. This is
true of every existing module in this application, not just this one.

Substitute the submodule id from step 3 for `<SUBMODULE_ID>`.

**Grant** a user access:

```sql
INSERT INTO `module_capablity`
  (`acessid`,`role_id`,`moduleid`,`submoduleid`,`submodule_access`,
   `madd`,`medit`,`mremove`,`addedOn`,`upadtedOn`)
VALUES (0, <USER_ID>, 4, <SUBMODULE_ID>, 1, 1, 1, 0, NOW(), NOW());
```

**Revoke** a user's access:

```sql
DELETE FROM `module_capablity`
 WHERE `role_id` = <USER_ID> AND `moduleid` = 4 AND `submoduleid` = <SUBMODULE_ID>;
```

**Audit** who currently holds what — worth running when someone leaves:

```sql
SELECT s.submodule, mc.role_id AS user_id,
       CONCAT(u.first_name, ' ', u.last_name) AS user_name
  FROM module_capablity mc
  JOIN submodule s     ON s.id = mc.submoduleid
  LEFT JOIN system_users u ON u.user_id = mc.role_id
 WHERE mc.moduleid = 4 AND mc.submodule_access = 1
   AND s.submodule LIKE 'AUTOMATION BOM %'
 ORDER BY s.submodule, mc.role_id;
```

---

## Step 9 — tear down the local test environment

**Do this once the rollout is verified on the server, and not before.**

Two Docker containers hold a copy of production data. They are bound to
`127.0.0.1` only and nothing has left the machine, but they should not
outlive the rollout.

```bash
docker rm -f abom-web abom-mysql
docker network rm abom-net
docker rmi abom-php74
rm -rf ~/pms-sandbox
```

**Delete the shim first**, before anything else, so it cannot outlive
the sandbox by accident:

```bash
rm -f ~/pms-sandbox/application/controllers/Abomdevlogin.php
rm -f ~/pms-sandbox/.abom-sandbox
find ~/pms-sandbox -iname '*abomdevlogin*'      # must print nothing
```

`~/pms-sandbox` also contains a copy of the application and a
`database.php` pointing at the container. The shim
(`application/controllers/Abomdevlogin.php`) bypasses authentication
entirely. **It must never reach the server.** It is not in the step 4
file list, does not exist in the working tree, refuses to run outside the
sandbox, and step 8b confirms its absence on the live site — deleting it
here is the fourth of those four defences, not the only one.

Confirm afterwards:

```bash
docker ps -a | grep abom     # expect nothing
ls ~/pms-sandbox             # expect: no such directory
```
