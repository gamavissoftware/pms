# Automation BOM Generator — rollout

Someone will be doing this at 9pm. Every step is explicit, ordered, and
has a check. Nothing here is reversible-by-accident.

**Rule 12: never sync this working tree to the server.** It is an
incomplete copy of production — `application/controllers/BOM.php`,
`application/views/BOM/` and possibly more exist there and not here. A
wholesale `rsync` would delete them. Copy the 27 files listed in step 4
**by name**, and nothing else.

---

## Before you start

| | |
|---|---|
| Take a database backup | `mysqldump -u USER -p DBNAME > backup_before_abom.sql` |
| Confirm PHP | must be **7.4** (`ea-php74`). The module is 7.4-safe; CodeIgniter 3.1.4 is not PHP 8 clean. |
| Confirm ext-zip | **Run the probe below.** Only needed for Excel export; CSV and PDF work without it. |
| Confirm MariaDB | 11.x. Proven against 11.4. |

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

**Grants** are mirrored from whoever already holds the existing BOM
Correction Tool submodule under module 4, so the same people can reach
the new module. Adjust afterwards in `module_capablity` if that is not
what you want. Note that **`module_capablity.role_id` holds a user id**,
not a role id, despite the name — the whole application works that way.

If `grant_rows_created` is 0, nobody has access yet; add rows explicitly.

**Check:**

```sql
SELECT id, moduleid, submodule, status FROM submodule
 WHERE moduleid = 4 ORDER BY id;
```

---

## Step 4 — copy the module files

**27 files. By name. Nothing else.** Checksums are in
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

**Case matters.** The server is case-sensitive; this Mac is not.
`Abom.php` not `ABOM.php`; `views/abom/` not `views/Abom/`. Nothing here
may be uploaded as `BOM.php` or into `views/BOM/` — those are the
existing module's and must not be touched.

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
| `abom_signoff_mode` | `'customer'` | 3 printed boxes. `'internal'` gives 4. |
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

**Check:** `php -l application/views/common/nav-menu.php`, then load any
page and click the item.

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

**Rollback:** remove the 27 files, revert the `routes.php` append, remove
the nav snippet, and `DROP TABLE` the eleven `abom_*` tables. To undo the
permissions, delete the three `AUTOMATION BOM %` rows from `submodule`
and their `module_capablity` grants. Nothing outside the `abom_` prefix
is touched at any point, so no existing data is at risk.

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

`~/pms-sandbox` also contains a copy of the application and a
`database.php` pointing at the container, plus a sandbox-only login shim
(`application/controllers/Abomdevlogin.php`) that bypasses
authentication. **That file must never reach the server.** It is not in
the step 4 file list and does not exist in the working tree.

Confirm afterwards:

```bash
docker ps -a | grep abom     # expect nothing
ls ~/pms-sandbox             # expect: no such directory
```
