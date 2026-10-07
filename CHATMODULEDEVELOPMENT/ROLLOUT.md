# Chat module — deployment

Team messaging inside PMS: direct messages, groups, file sharing, @mentions,
reactions, read receipts, forwarding, screenshot annotation, meeting join
cards, and a floating chat dock on every page.

Ported from the CoreTech CRM chat module. Nothing in PMS was rewritten: every
new table is prefixed `chat_`, every new permission row is additive, and the
four existing files that were touched carry clearly-marked
`===== CHAT MODULE =====` blocks.

---

## 1. Deploy

### a) Import the schema

phpMyAdmin → the PMS database → SQL tab → run, in this order:

1. `Database/chat_001_schema.sql` — the twelve `chat_` tables
2. `Database/chat_002_permissions.sql` — registers CHAT in the permission screen
3. `Database/chat_003_department_groups.sql` — lets a group be filed under a
   department, which is what the sidebar's **Department Groups** heading sorts on
4. `Database/chat_004_pin_expiry.sql` — lets a pin expire on its own (24 hours,
   7 days, 30 days), instead of staying up until somebody removes it
5. `Database/chat_005_archive.sql` — archives a DF group once its DF is
   dispatched, and bundles the group's attachments into one `.zip`

All five are safe to run more than once. `002` pre-flights and aborts with a
clear message rather than creating duplicates.

`003`, `004` and `005` are the three the app survives without: each is guarded by a
column check, and without them the module behaves exactly as it did before —
`003` missing means every group shows under *Other Groups*, `004` missing means
a pin never expires and stays up until somebody takes it down, and `005` missing
means nothing archives. `Chat/health` reports all three separately from `ok` and
names the file. Run them anyway; a
half-installed feature is a support ticket waiting.

### b) Upload the NEW files

| File | Purpose |
|---|---|
| `application/controllers/Chat.php` | pages + JSON API + long-poll stream |
| `application/models/Chat_model.php` | all data access |
| `application/helpers/chat_access_helper.php` | identity + permissions + the go-live switch |
| `application/views/chatmodule/index.php` | the messenger (full page **and** dock) |
| `application/views/chatmodule/_dock.php` | floating bubble + panel, persistence |
| `application/views/chatmodule/_navwidget.php` | nav badge and notifications |
| `application/views/chatmodule/df_groups.php` | backfill screen at `/chat/df-groups` |
| `image_bank/chat_uploads/_archives/index.html` | keeps the bundle directory unlistable |

### c) Upload the CHANGED files

Each contains only additive, marked blocks.

| File | Change |
|---|---|
| `application/config/constants.php` | upload paths, `CHAT_MODULE_VISIBLE`, `chat_archive_dir` + `CHAT_ARCHIVE_GRACE_DAYS` |
| `application/config/routes.php` | `chat`, `chat/open/(:num)`, `chat/df/(:num)`, `chat/df-groups`, `chat/archive/(:num)` |
| `application/views/common/nav-menu.php` | sidebar link, topbar icon + badge, floating dock |
| `application/views/gantt/df_gantt_board.php` | "Discuss this DF" button |

### d) Create the upload directory

`image_bank/chat_uploads/`, writable by PHP (755), containing the `.htaccess`
and `index.html` from this repo — **and `_archives/` inside it**, same
permissions, for the attachment bundles. PHP creates `_archives/` itself if
`chat_uploads/` is writable; upload it if the web user cannot create
directories. It sits inside `chat_uploads/` on purpose, so Apache applies that
directory's `.htaccess` to it and the bundles inherit the no-script rule rather
than needing protection of their own. Script execution is disabled inside it and
downloads are served through `Chat/download/<id>`, which checks conversation
membership first.

### e) Check it

Open **`/index.php/Chat/health`**. It reports which `chat_` tables and columns
are present, whether `dm_key` is nullable, whether the upload directory is
writable, and whether the permission rows exist. Every deployment problem so
far has been a half-finished deploy — PHP uploaded without the SQL, or the
reverse — and this turns "chat is broken" into a specific answer.

Then open **`/index.php/Chat`**.

### f) Go live

Chat is **hidden by default**. While hidden the controller still runs, so you
can pilot it by URL. When you are ready, set this in
`application/config/constants.php`:

```php
defined('CHAT_MODULE_VISIBLE') OR define('CHAT_MODULE_VISIBLE', TRUE);
```

That single switch reveals the sidebar link, the topbar icon and badge, the
"Discuss this DF" button and the floating dock, together.

---

## 2. Permissions

Chat plugs into the existing framework
(`system_modules` → `submodule` → `module_access` → `module_capablity`). After
running `chat_002_permissions.sql` a **CHAT** module appears in
*Master → User management → Module access*:

| Sub-module | Controls | Default |
|---|---|---|
| Chat - Messenger | open chat at all | **on** |
| Chat - Create Group | the "Group" button, and DF groups | **on** |
| Chat - Manage Members | add / remove members, rename a group | **on** |
| Chat - Pin Message | pin / unpin | **on** |
| Chat - File Sharing | the attach button and uploads | **on** |
| Chat - Link Records | linking DFs, tasks and leads to messages | off |

**A user with no CHAT grants gets the "on" set above**, so chat is usable
company-wide from day one without configuring anything. Only **Link Records**
stays closed until granted, since it exposes DF, task and lead data. Grant the
CHAT module to a user to take fine-grained control — from that point the ticks
are the whole truth, and an unticked capability is denied.

A role marked `user_role.isadmin = 1` bypasses all of this.

> **`role_id` holds a USER id.** Both `module_access.role_id` and
> `module_capablity.role_id` are keyed by `system_users.user_id`, not by
> `user_role.user_role_id`, despite the column name. This is PMS's own
> convention — `nav-menu.php` does the same in about forty places — and it is
> verified against production data in `Abom_permission_guard.php`. Any grant
> written by hand must use a **user** id.

---

## 3. What differs from the CoreTech original

The two applications share a framework, a session shape, `system_users` and the
whole permission model, so most of the module ported unchanged. Four things did
not.

### No outside parties

CoreTech chat carried a whole reply-only regime for engineers
(`tech_information`) and vendors (`vendor_registration`) — separate portals,
separate nav menus, an "our team starts it, they answer it" rule, and
Team/Technicians/Vendors tabs to keep the three apart.

PMS has one account table and everybody in it is staff, so that layer is gone.
What was kept deliberately:

- `chat_participant` still stores the **pair** `(user_id, user_type)`, always
  `'user'` here. It costs nothing and it is what would let a second account
  table be added later without ids colliding with staff ids.
- `chat_is_external()` still exists and always returns FALSE, so the seven
  places in the controller that enforce the reply-only rules are intact rather
  than deleted.

The sidebar tabs became **All / Direct / Groups** — one-to-one versus
many-to-many is the split that still carries information.

### Records, not tickets

CoreTech linked messages to CRM leads and auto-created a group per service
ticket. PMS links three record types through one mechanism, defined once in
`chat_ref_types()`:

| Type | Table | Opens |
|---|---|---|
| DF | `df_design_form_table` | `/gantt/<df_id>` |
| Task | `task_department_wise_scheduling` | its DF's board |
| Lead | `leads` | `Leads/edit_leads/<id>` |

Adding a fourth is one entry in `chat_ref_types()` plus one method on the
model — the picker, the chips and the server all read that list.

`/chat/df/<df_id>` finds or creates the group for a DF, seeded with whoever
raised it plus everyone holding a task on it. Called again it **adds** anyone
new and removes nobody, so reassigned work keeps its history.

### The permission key

See the note in section 2. This was the one change that would have failed
silently rather than loudly, and it is what `guard_matrix.php` exists to pin
down.

### Uploads

Attachments live under `image_bank/chat_uploads/`, where every other PMS module
keeps its files, rather than CoreTech's `assets/`.

---

## 4. Tests

```bash
php CHATMODULEDEVELOPMENT/tests/run_tests.php
```

No database, no CodeIgniter bootstrap, no `system/` directory needed.

| Test | What it protects |
|---|---|
| `lint` | all ten shipped and touched files parse — including the 3,400-line view and `nav-menu.php`, where a syntax error takes down every page of the app |
| `schema_check` | every PMS column the module reads exists in the production dump. This is the port's main risk: a ported query naming a CoreTech column fails only when a user clicks that one feature |
| `guard_matrix` | the capability rules, and above all that grants are keyed by **user** id. Each case runs in its own process because the helper caches in statics |
| `route_surface` | every route, every `BASE + 'endpoint'` in the views, and every record-card link resolves to a real controller method |

`schema_check` **skips** rather than passes when the production dump is absent
(it is gitignored, ~53 MB) — a check that silently proves nothing is worse than
no check.

---

## 5. How real-time works

No Node and no WebSocket daemon — this is shared hosting — so delivery is a PHP
**long-poll**. `Chat/stream` parks for up to 25s, waking the moment a row lands
in `chat_event` for that user, so a message arrives in well under a second.

Three things make that safe:

- `session_write_close()` runs **before** the wait loop. CI's session driver is
  `files`, so holding the session open would block every other request from
  that same user for the full 25 seconds.
- The stream runs **only while the tab is visible**. Hiding a tab aborts the
  parked request and releases the PHP worker; a user with eight PMS tabs open
  costs one worker, not eight.
- On other PMS pages the nav badge uses a light 12s poll instead, since a
  parked request per open tab would tie up a worker each.

`chat_event` is transport, not history — it prunes itself (roughly one page
load in 200 runs an indexed DELETE of rows older than 7 days). Message history
lives in `chat_message` and is never pruned. If you need more headroom, shorten
`Chat::STREAM_HOLD`.

---

## 6. Two things worth knowing

**Emoji need utf8mb4.** The app-wide DB connection cannot store 4-byte
characters, so `Chat_model::__construct()` issues `SET NAMES utf8mb4` for chat
requests only. Do **not** "fix" this by changing `config/database.php` — the
rest of PMS is deliberately untouched.

**`chat_reaction.emoji` is `utf8mb4_bin` on purpose.** Under the usual
`utf8mb4_unicode_ci`, MySQL considers virtually all emoji equal — `'👍' = '🎉'`
is TRUE — so reacting with 🎉 would silently delete an existing 👍. The binary
collation compares actual bytes.
