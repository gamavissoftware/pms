# Chat module — changelog

## 2026-09-23 — which plant somebody is at, and roles that stop shouting

**Every place chat names a person now says their plant beside their role** —
"Management · Sector - 59". `system_users.plant_unit` maps 1 to *Sector - 59*
and 2 to *Sector - 06*; anything else, including 0, NULL and a column that has
not been added yet, shows nothing rather than inventing a plant for somebody.

It belongs next to the role because in a two-plant company "Manager" is only
half an answer. The question behind reading a role in a chat window is usually
*is this the person I should be asking*, and which site they are at is most of
that.

**Roles are in sentence case.** `user_role.user_role` is typed by hand and comes
out however it was entered — "MANAGEMENT" shouting beside "Asstt. Manager".
Next to a name, that reads as emphasis nobody meant.

A role that is **already mixed case is left exactly as typed**: "Asstt. Manager"
stays as it is rather than being flattened to "Asstt. manager". Somebody
capitalised that deliberately and it is correct — the only thing worth fixing is
the shouting. Same rule `chat_name_case()` already applies to people's names,
for the same reason. The first *letter* is capitalised, not the first character,
so "(hod) manager" still comes back with a capital H.

**Two definitions, nine call sites, no drift.**

- `chat_plant_unit()` and `chat_role_case()` live in the helper, so the
  messenger, the dock, the nav widget and the mobile API cannot disagree about
  what a `1` means or how a role should read.
- The casing is applied in `resolve_people()` and `directory()` — the module's
  two hydration points — rather than at each place a role is drawn. That single
  change covers message bylines, member lists, DM titles, read receipts and the
  mention picker together.
- On the browser side one `roleLine()` composes "role · plant" for all nine
  places a person is named: the sidebar's DM rows, the thread header, each
  message byline, the member panel, the mention picker, both people pickers,
  the forward list and the read receipts. Either half can be empty and the dot
  goes with it.

**The column is guarded**, like every other one added to this module:
`has_plant_unit()` checks once per request and the select list is built around
the answer. Naming a column that is not there turns every people query into a
500, and the people queries *are* the module — so on an environment without
`plant_unit` nobody shows a plant and nothing else changes.

**Mobile gets both for free.** `Api::chat_directory()`, `chat_thread()` and the
message hydration all go through the same two model methods, so the app shows
the plant and the cased role without a line of its own. Messages carry
`sender_plant` alongside `sender_role`.

## 2026-09-23 — replies stopped attaching (regression, same day)

Making the composer release instantly broke replying. The clear was put at the
moment of sending — `S.replyTo = null`, tags emptied, private-reply origin
dropped — but **the request is built after that**, and it was still reading
those from `S.*`. So `reply_to` went out as `0` on every message and the reply
arrived as a plain message instead of under the one it answered.

Two other things went with it, unreported and easy to miss: **record links**
(`tags`) and the **private-reply origin** were being cleared before they were
read too, so a message tagged with a DF arrived untagged and a private reply
lost the card pointing back at the group message it answered.

Fixed by capturing everything the message carries into locals **before**
anything is released, and building the payload only from those. The composer
still empties the instant you press Enter — nothing about the fast-sending
change is given back.

**A failed send now hands back the whole message, not just the words.** Since
the composer is cleared before the request goes out, a reply that fails used to
lose what it was replying to, and the person retyped it as a loose message. The
reply target, the tags and the origin all come back with the text.

**The pending bubble draws the quote.** Replying is the one case where the
optimistic bubble alone is ambiguous — a reply showing no quote until the server
answers looks exactly like a reply that failed to attach, which is what this
whole entry is about. Same markup as the real bubble, so nothing shifts when
they swap.

Verified by running `sendMessage()` against stubs: `reply_to: 777`, tags and
`origin_id` all present in the POST, with the composer state released in the
same pass.

## 2026-09-23 — the 500 on the sidebar, and sending that waited on Firebase

### "Could not refresh conversations (500)"

**The archive sweep's query was invalid on MySQL 5.7+.** It joined
`task_department_wise_scheduling` and leaned on `GROUP BY c.id` to collapse the
duplicate rows a DF can have for task 103 — but `d.df_no` and
`t.task_completed_on` come from *other* tables, so neither is functionally
dependent on `c.id` and neither is aggregated. Under `ONLY_FULL_GROUP_BY`,
which is the **default** since 5.7, that is an error. With `db_debug` off in
production CI returns FALSE rather than raising, so `->result()` was called on a
boolean and took the whole request down.

Rewritten as `EXISTS` plus a scalar subquery for the dispatch date: it says what
was actually meant, cannot multiply rows, and needs no `GROUP BY`. The query
result is also checked with `is_object()` before use — nothing in housekeeping
is worth a 500 on the messenger.

**And it was firing everywhere, because the sweep rode along on the dock.** The
dock is an iframe on *every* page in PMS, so "one visit in twelve" meant a sweep
every few clicks anywhere in the app. That is what turned one bad query into an
error people saw constantly, and it was also making chat feel heavy everywhere,
since a sweep zips files. `maybe_archive()` now runs on the full `/Chat` page
only, one visit in fifty, and zips **one** group per pass rather than three.

**`catch (Exception)` was not catching the failures that matter.** PHP 7 turns
most fatals into `Error`, which is not an `Exception` — so a half-finished
deploy (new `Chat.php`, old `Chat_model.php` → "call to undefined method") sailed
straight past the guard that existed precisely for it. Both the sweep and the
list endpoint now catch `Throwable` as well.

**And the list endpoint now fails soft.** `Chat/conversations` is polled,
refreshed after every send and re-fetched whenever the tab returns, so it is the
first thing anyone notices when something is wrong — and a blank 500 tells them
nothing. It now returns a readable message pointing at `/Chat/health`, logs the
file and line, and leaves the rows already on screen alone.

### Sending felt slow, because it was waiting on Firebase

`send()` did not answer the browser until it had pushed a notification to every
member's handset. In the HOD group that is 27 devices — issued in parallel, but
the request still sat through all of them, each with a ten-second timeout, plus
an OAuth token fetch first. The bubble said *Sending…* for as long as Google
felt like taking.

None of that is anything the sender is waiting to find out: the message is
already in the database and already on its way to every open browser through the
long-poll. The phone push is a courtesy to people who are **not** looking. So
the JSON goes out, the connection closes, and the push happens with nobody
watching (`json_and_continue()`; `fastcgi_finish_request()` on PHP-FPM,
Content-Length and a flush elsewhere — and where neither works, behaviour is
exactly what it is today).

**The send button no longer locks for the round trip.** It was disabled until
the response came back, so you could not fire off four quick lines the way you
would in WhatsApp. Only an upload freezes it now — that genuinely occupies the
composer. The reply bar, tags and mention list are released at the moment of
sending too; left until the response, a fast second message inherited the first
one's reply-to and its tags.

### A sound for sending, not just receiving

Chat had one sound, for messages arriving. Sending now has its own: **lower,
quieter, half the length, and a single note** rather than two — the same
distinction WhatsApp draws. It is feedback that a keypress landed, not an
announcement. Rate-limited to 120ms rather than the incoming ping's 1.5s,
because four quick messages should make four sounds.

Both voices now exist in the messenger *and* in the nav widget, because the host
page owns the speaker even when the dock asks for the sound — the top document
is the one holding the user gesture the autoplay policy wants. `d.kind` carries
which one through the dock's postMessage bridge.

## 2026-09-23 — a DF group's other end: archived on dispatch

A DF group is created the day the DF is released and nobody ever closes it. It
goes quiet the day the machine ships and then sits in the sidebar forever,
holding every photo, drawing and spreadsheet anybody posted to it. This adds
the other end of that life.

**When the DF is dispatched, its group archives itself.** Two conditions, both
required, exactly as the dispatch desk describes it:

1. **task 103** on the DF is complete. 103 *is* the dispatch task — the
   codebase already treats it that way, in the cascade rule in
   `Dashboard_model` that closes a DF's remaining tasks once 103 lands.
2. **`df_release.df_status = 1`** — the DF itself is closed.

Either alone is not enough. 103 can be ticked while the DF is still open and
being corrected, and a DF can close for reasons that are not a dispatch. Both
together mean the machine has actually gone.

The group posts *"Thank you all for your efforts — DF 1830 has been dispatched
on 21 Sep 2026. This group is now archived…"*, moves to a new **Archived**
heading at the bottom of the list, and becomes **read-only**.

**A SWEEP, not a hook.** Four separate places in PMS close a DF — three in
`Task.php` and the auto-close in `Dashboard_model` — and a fifth will appear the
week after this ships. A hook on the moment of dispatch is a hook somebody
forgets; a sweep simply finds the group on the next pass. It rides along on
opening chat, roughly one visit in twelve, bounded to three groups per pass, so
the first run after this ships spreads a backlog of every dispatched DF over an
afternoon instead of timing out on one request. Same reasoning as
`maybe_prune()`, and the same absence of a cron — PMS has no job runner, and a
feature that needs one is a feature that quietly stops working.

**ARCHIVED IS NOT DELETED, and could not reuse the column that says so.**
`is_archived` is already this module's soft-delete flag (`delete_channel()`
sets it; three queries filter on it). Reusing it would make a deliberately
archived DF group indistinguishable from a deleted one and fill the Archived
section with groups somebody deleted. Archiving got its own `archived_at`, and
`archived_at IS NULL` is every conversation that exists today — nothing is
backfilled and no existing row changes meaning.

**Read-only is enforced on the server.** Hiding the composer is a courtesy; the
dock, a stale tab, the Flutter app and anyone with curl all reach the same
endpoints. `archived_guard()` refuses `send` and `upload` with a 409. Reading is
untouched — the entire point of archiving rather than deleting is that the
history stays open to everyone who was in it.

**The files become one `.zip`.** Built at the moment of archiving and
**reopened for verification before a single flag is written**, so a group is
never marked archived against a bundle that turned out to be unreadable. Names
inside are the original filenames, not the randomised `stored_name` — the
bundle is for a person opening it in Explorer a year from now, and
`a3f9c2….bin` tells them nothing. Duplicates get a `(2)` suffix instead of
silently overwriting each other, and a `FILE-LIST.txt` says who shared what and
when. A file somebody deleted off the server by hand is skipped, not fatal.

**The originals go on a grace period, not immediately.**
`CHAT_ARCHIVE_GRACE_DAYS` (default **30**) is how long you have to notice a
group was archived wrongly; until it runs out, both copies exist and reopening
costs nothing. Set it to `0` to reclaim the space the moment the zip verifies,
or `-1` to never delete. A file is only deleted when its bundle **exists**,
**opens**, and **contains it** — the point is to save storage, not to lose a
drawing.

**Reopening restores the files.** Unarchiving pulls the originals back out of
the bundle and only then clears the flags — a group whose files cannot come
back stays archived and says so, rather than reopening with two hundred dead
download links. The zip is kept either way.

**The naming had to be deterministic, and one version of it was not.** Restore
and purge work out what a file is called inside the bundle by replaying
`unique_zip_name()` over the rows. The first cut of `build_archive_zip()`
skipped missing files *before* taking their name, so a file deleted by hand
meant every later duplicate's `(2)` drifted out of step and restore would have
pulled back the wrong bytes. The name is now taken for every row and the skip
happens after.

Verified on real files: 6 attachments bundled, verified, originals purged,
group reopened, all 6 back byte-for-byte identical.

**Also:** a manual **Archive group** button in Details, above Leave and Delete
— it is almost always what somebody reaching for "Delete group" actually
wanted. Archived shows collapsed by default for anyone who has never touched
the section controls, and an archived row carries an `archived` tag so a search
that pulls it up beside live rooms still reads correctly.

### On the storage saving — measured, not assumed

Zipping recovers space only from files that are not already compressed:

| what is in the group | loose | zipped |
|---|---|---|
| CSV, logs, text, most `.dwg` | 4.69 MB | 0.02 MB (**−99.6%**) |
| photos and PDFs | 4.69 MB | 4.69 MB (**0.0%**) |

A DF group is mostly phone photos, so **the byte saving on a typical group is
close to nothing**. What it does deliver is real but different: hundreds of
loose files become one, which returns per-file block overhead and inodes, makes
the group's history portable in a single download, and stops the upload
directory growing without bound in file *count*. If the goal is genuinely to
reclaim gigabytes, the lever is re-encoding the photos on upload — a phone JPEG
is 3–5 MB and the same image at 1600px is ~300 KB. That is a separate,
deliberately lossy change; say the word.

## 2026-09-23 — Shared files says who, when, and where

The **Shared files** panel listed a name, a size and a link labelled *Get*. It
answered none of the questions people actually bring to it — *who sent this,
when, and what was being discussed around it* — and *Get* left you to guess
whether it opened, previewed or saved.

The cause was one line: the attachment was pushed onto the list on its own and
the message it arrived in, which knows all three answers, was thrown away.
Carrying the message alongside it, each row now:

- **says who shared it and when** — "Anil Panchal · Today, 12:08 PM", using the
  same `day_label()` wording as the date separators in the thread, so *Today /
  Yesterday / 04 Sep 2026* reads the same in both places;
- **jumps to that message** when the row is clicked, landing you in the part of
  the conversation the file belongs to rather than nowhere;
- **has a Download button** instead of *Get*. It is honest: `Chat/download/<id>`
  already sends `Content-Disposition: attachment`, so the file saves.

Jump and download are separate targets — the row jumps, the button downloads,
and the button stops the click so one can never fire the other.

**The byline wraps where the filename truncates.** In a 320px panel something
has to give, and a filename is long, repetitive and recoverable from a hover
title, while half a date is no answer at all.

**A jump that finds nothing now says so.** `[data-jump]` silently did nothing
when the target was off the loaded page — which is exactly the "it doesn't do
anything" people report. It now says the message is not loaded and points at
*Load older messages*. Quoted and pinned messages get this too; they shared the
handler and the same silence.

**Still the loaded page only.** This list has always been *recent files*, not an
archive of the whole conversation, and a jump can only land on a message that is
on screen — so the two limits are one limit rather than a new trap.

Web only: `sender_name`, `day` and `time` were already on every message the API
returns, so nothing server-side had to change.

## 2026-09-23 — pins can be taken down, and can take themselves down

**Unpinning had no obvious answer.** The tack in a message's hover toolbar was
a toggle, but it said *Pin* whether the message was pinned or not, so the only
way to discover it also unpinned was to press it and see — and you had to find
the original message back up the thread first. The pin bar at the top, the one
thing you could actually see, offered nothing.

Three changes, and the first is the one that matters:

- **Every row in the pin bar has its own × .** That is where somebody looks
  when they want a pin gone, so that is where it is. One press, no
  confirmation — asking "are you sure" about something this reversible is just
  a second press.
- **The tack now says `Unpin` on a pinned message**, and carries a small ×
  beside it, so the toggle stops being a guess.
- **The bar lists each pin on its own line** with how long it has left, and
  says *"and N more pinned"* rather than silently showing only the first three.

**Pins can now expire, the way WhatsApp's do.** Pinning asks *how long for* —
**24 hours / 7 days / 30 days / Always** — and the pin takes itself down when
it runs out. The point is the default case: a pin nobody put a limit on is a
pin nobody ever removes, so the bar fills with a dispatch date that passed
three weeks ago and people stop reading it, which costs more than never having
pinned anything. *Always* is still there for the standing notice that should
outlive everything.

`Database/chat_004_pin_expiry.sql` adds `chat_message.pinned_until`. NULL means
no expiry, which is the honest state of every pin that exists today — nothing
is backfilled, and an old pin stays up until somebody removes it.

**No cron, deliberately.** PMS has no scheduled-job runner, and a pin running
out is not an event anyone needs to be told about — it just stops being shown.
`expire_pins()` clears them as `pinned_messages()` reads them, so the tidy-up
rides along on a query that was already happening and the module stays a pure
file-upload deploy. The cost is one indexed UPDATE that matches nothing almost
every time.

**The sweep has to run before the thread is built.** Both conversation
endpoints now read the pinned list into a variable *above* `messages()` instead
of inline below it. PHP fills an array literal top to bottom, so in the old
order a pin expiring on that very load would vanish from the bar while the
message it sat on still said *pinned* — one render out of step, self-correcting,
and indistinguishable from a bug.

**The column is guarded**, like `department_id` before it: PMS deploys by
uploading files, so this PHP can land before its SQL does. `has_pin_expiry()`
checks once per request, and without the column pinning behaves exactly as it
always has — it simply never expires. `Chat/health` reports it and names the
file, and is still `ok` without it.

**Mobile gets it too.** `Api::chat_pin()` takes the same `days`, validated by
the same `pin_durations()`, and returns that list so the app can build the menu
without hardcoding it. An app build that predates this sends no `days`, which
means *Always* — exactly how it behaved before.

## 2026-09-23 — unread comes first

Anything with unread messages is now lifted out of its section into **Unread**,
pinned at the very top of the list — a direct message and a DF group with new
messages sit side by side there. When you open chat the question is *what is
waiting for me*, not *what kind of room is it*; reading a conversation drops it
straight back under its own heading, so the kinds stay separated for everything
you are actually finished with.

The heading carries the **message** total, not the room count — it is the one
number anybody wants from it, and "Unread 3" beside a 9-message total would be
two numbers meaning different things on one line. Every other heading still
shows its row count, and its unread total only while folded.

**Muted rooms float too.** Unread is unread: a muted group is still a group
somebody is waiting on, and burying it under every read conversation is how it
gets missed for a week. Muting keeps doing what it is actually for — no sound,
no toast, no desktop notification; the alert loop in `handleStream()` still
skips a muted room — it simply stops deciding where the row sits. The row
carries a struck-through bell so a group that arrived at the top in silence
says why, rather than reading as a bug.

`bucketOf()` still exists separately from `sectionOf()` because the two answer
different questions: the home section is a property of the room, the bucket is
where it is drawn *right now*.

**Also fixed while in here:** a search matching nothing used to print *"No match
in …"* once per section — five lines saying the same word. Holding the structure
open is worth it while some of it still has rows, so that stayed; a search that
matches nothing anywhere now gets a single answer instead. Every exit from
`renderList()` goes through `finishList()`, so the new early return cannot leave
the tab counters stale.

Order within a section is unchanged: newest activity first, as the server sends
it. Nothing sorts client-side — every path that mutates the list locally
(`refreshConversationsSoon()` after a send, the stream's `r.conversations`)
replaces it from the server, so the ordering has one owner.

**The mobile app gets this too.** `Chat_model::my_conversations()` now puts the
unread rows first itself (`unread_first()`), instead of the sidebar being the
only thing that knew the rule. That list is what *every* surface draws — the
full page, the dock, and the Flutter app's `Api::chat_conversations()` — and
the app renders it in the order it arrives, so without this the phone kept
showing plain recency while the web showed unread at the top: the same account,
two different lists.

The sort is **stable** (the row's original position is the tiebreak, not the
engine's — PMS runs PHP 7, where `usort` is not stable), so both halves come out
in exactly the `last_activity_at` order the query produced. Muted conversations
float on unread here as well, for the same reason. The sidebar buckets this
list without re-sorting it, so the sections look identical to before.

## 2026-09-22 — groups are filed under DF, department, or neither

The sidebar listed every non-DM conversation in one flat run, so an auto-created
DF room and a department's own room sat interleaved in a column of
near-identical rows. Groups now fall under **three headings** — **DF Groups**,
**Department Groups**, **Other Groups** — with **Direct Messages** above them in
the "All" tab.

**How a group becomes a department group.** Chat had no idea what a department
was, so one was added: `chat_conversation.department_id`
(`Database/chat_003_department_groups.sql`). It is set **when the group is
created** — "Create a group" now asks *General* or *Department* and offers a
department picker — and can be changed afterwards from **Details → Where this
group lives**. Nothing is backfilled; every group that exists today shows under
*Other Groups* until someone files it.

**Not a fourth `chat_ref_types()` entry**, which is what it looks like it should
be. Every ref type there is also a *taggable record*: it gets a card in the
header, a chip on a message, a row in the "Link a record" picker and a deep link
to its own page. A department has none of those and is not something you attach
to a message — it is an attribute of the room. Keeping it separate also lets a
group be both, and **DF wins**: a DF group's identity is its DF, so a department
set on one is extra context, not a reclassification. `set_department()` refuses a
DF group outright rather than storing a value that visibly does nothing.

**The classification is the server's.** `Chat_model::group_kind()` returns
`df` / `department` / `other` and rides on every conversation row and header, so
the full page and the dock cannot file the same room under different headings.
The browser's own copy of the rule is a fallback for exactly one case: a tab left
open across this deploy, still holding rows the previous version fetched.

**The picker is scoped to the user's business location**
(`Master_model::select_department()`'s rule), which is not cosmetic: production
carries the same department name under two ids in two locations — MARKETING is 6
and 9, ACCOUNTS 3 and 20, DISPATCH 4 and 19 — so an unscoped list would offer
identical-looking options that file groups into different buckets. Every active
user sits in location 2, so in practice this is the ~26 departments people are
actually in. A department that already has a group still appears, marked *already
has a group*, with a link to open it — a second one is occasionally what somebody
wants, but not by accident.

**Details worth keeping:**

- Headings appear only when there is more than one kind to separate. Somebody
  whose whole list is four direct messages gets the plain list they had before.
- A heading **folds away** and the choice sticks per browser (`ctChatSections`,
  alongside the sound and Enter preferences). A collapsed section shows its
  **unread count**, so folding one can never hide a new message.
- Filtering leaves the headings standing and says *"No match in df groups."*
  under the ones it emptied, so the list does not appear to lose a whole
  category mid-keystroke.
- Within a section rows stay in newest-activity order, as before.

**It degrades rather than breaking if the SQL has not been run.** PMS deploys by
uploading files, so the PHP for a feature can land before its migration does.
`Chat_model::has_department_column()` guards every write and the column is left
out of the insert when it is absent — the worst case is that department groups
do not work yet, instead of *all* group creation failing. `Chat/health` reports
it separately from `ok` and names `chat_003_department_groups.sql` as the fix,
rather than misdirecting to `chat_001`.

## 2026-09-22 — penalty DFs flagged in the messenger itself

The **PENALTY** flag was only on the backfill screen (`/chat/df-groups`), which
most people never open. It now travels with the DF group wherever the group is
named in `/Chat`:

- **Conversation list** — a red warning mark beside the group name, plus a red
  left edge and a faint tint on the row, so flagged DFs are scannable down a
  long list. `penalty` is searchable text in the filter box, exactly as on the
  DF groups screen, so typing "penalty" pulls them together.
- **Thread header** — the same mark, slightly larger, beside the group name.
- **Details pane and message tags** — the same mark on the linked-DF card,
  alongside the status rather than replacing it: a DF can be running *and*
  penalised.

**The mark only — no wording, and no figure anywhere in the module.** What a
DF's penalty comes to is the penalty report's business
(`views/master/penalitydf.php`); chat says only THAT the DF is flagged. No
money crosses into a chat payload at all — `my_conversations()`, `df_info()`
and `df_group_overview()` carry a boolean and nothing else — and the **₹ amount
was dropped from the DF groups screen too**, where it had shown since 14 Sep;
that screen keeps its worded **⚠ PENALTY** pill, which fits a table column,
while the messenger uses the bare mark. Spelling "PENALTY" out cost
~70px in a list row that already carries a name, a time, a preview and an
unread count, and a DF group is named `DF - <df no> - Group`: the preview
showed a worded chip truncating the **DF number**, the one thing people scan
that list for. The `title`/`aria-label` is what makes a bare glyph mean
something — to a screen reader it is the only text there is. In the header the
mark sits in its own slot rather than inside the `<h4>`, which ellipsises and
would swallow it on a long name.

**One definition of "penalty DF", finally.** `Chat_model::df_penalty_map()` now
owns the rule — `poreceived.penalityamount` summed per `df_id`, flagged when
non-zero — and the messenger list, the DF card, message tags and the backfill
screen all ask through it. `df_group_overview()` no longer computes its own
figure in SQL; the amount is compared against zero inside that one method and
dropped there. With no screen showing money, the module has **no currency
formatter left at all**: `df_groups.php`'s local `dfg_inr()` is gone and so is
the `chat_inr()` it briefly became, which also removes the last thing that
could drift out of step with the penalty report. All of it is guarded by the
new test below.

**Cost.** One extra grouped query per conversation-list load, and only when the
list actually contains DF groups; someone with none pays nothing. The mobile
API shares `my_conversations()`, so the flag is in its payload too — the app can
pick it up whenever it wants it.

### New test: `penalty_rule.php`

Drives the real `df_penalty_map()` against a stubbed CI (no DB, no `system/`)
and asserts the two quietly-breakable halves of the rule: that the amount is
**summed** across a DF's POs (a flat read returns one of several, usually 0, so
the flag vanishes from a DF that carries a penalty) and that the test is
**non-zero, not `> 0`** (so a correction keyed in as a negative stays visible).
It also fails if anything in `Chat_model` reads `penalityamount` outside that
one method, if an amount is stamped onto any payload, or if a penalty figure or
a currency formatter reappears in any chat view.

## 2026-09-14 — penalty DFs flagged on the DF groups screen

A DF that carries a penalty now shows a **PENALTY ₹x** tag with the amount, on
`/chat/df-groups`.

**Where the figure comes from.** `poreceived.penalityamount` (that is the
column's real spelling), summed per `df_id`, flagged when non-zero — the same
`!= 0` test `views/master/penalitydf.php` uses, so a correction entered as a
negative still shows as flagged rather than silently vanishing.

`poreceived` is aggregated in a SUBQUERY rather than joined flat. A DF can have
several `poreceived` rows and `Task::save_penality_df()` writes the penalty
onto one of them; joining flat and summing in the outer query would multiply
those rows against the `chat_conversation` join and over-count. Collapsing to
one row per `df_id` first makes that impossible. Same shape as the penalty
report's own derived table, so the two screens cannot disagree.

**Styling.** The status pills (Running / On hold / Completed) are soft, rounded
and pastel because a status is just a fact. A penalty is an exception, so the
tag is a different SHAPE as well as a different colour — squarer, ringed,
upper-case, with a warning glyph — which also keeps it legible to anyone who
cannot separate red from amber. The row carries a red left accent and a faint
tint so penalties are scannable down a long list, and the hero gains a
**Penalty** count plus a **Penalty only** filter (shown only when there are
any). "penalty" is also searchable text, so the existing search box finds them.

### Found: `format_number_indian()` is broken, and unused

`application/helpers/number_format_helper.php` mis-groups anything above one
lakh — its loop prepends the truncated remainder without removing what it
already emitted:

| Input | Helper | Correct |
|---|---|---|
| 45,000 | `45,000` | `45,000` |
| 2,50,000 | `2,250,000` | `2,50,000` |
| 18,75,000 | `18,1875,000` | `18,75,000` |
| 1,00,00,000 | `1,100,10000,000` | `1,00,00,000` |

Nothing in PMS calls it — this screen was the first, and the preview caught it
before it shipped — so **no existing screen is showing wrong numbers**. The
helper was left alone (fixing a shared helper is a separate decision) and this
view uses the same expression `Task_model::formatIndianCurrency()` uses, which
is what the penalty report formats with. It is inlined rather than loading the
very large Task_model for one string operation, exactly as `penalitydf.php`
does with its own local wrapper.

## 2026-09-11 — group lifecycle, department picker, team membership, dictation

### Delete a group / leave a group

- **Delete** is the creator's (owner's) call, or an app admin's — deliberately
  NOT `manage_members`, which most people hold by default. Running a room's
  membership and removing the room out from under everyone are different acts.
- It is a **soft** delete: `chat_conversation.is_archived = 1`, which
  `my_conversations()` and `unread_totals()` already filter on, so the room
  disappears for every member at once and nothing is dropped from
  `chat_message`. A hard delete would take the thread, its attachments and
  every reply pointing into it, on one click, with no way back. One UPDATE
  undoes this.
- DMs cannot be deleted — there is no creator of a two-person conversation,
  and deleting one would take the other person's history with it.
- **Leave** already existed and is unchanged.

### Department search in the people pickers

Both pickers (New direct message, Add members) now lead with a department
select, then search within it. Ticks survive both, so one group can take three
people from DESIGN and two from PURCHASE. "Select everyone shown" appears only
once the list is narrowed — offering it against the whole company invites a
200-person group by accident.

The department list is built from the people actually in the list rather than
from `departments`, so it can never offer a department with nobody in it.

### Team leaders can add their own team

A team leader may now add members to a group **without** the app-wide
`manage_members` grant — but only their own people
(`presto_team_members` via `prestogroup_teams.team_leader`). The check is on
the actual ids in the request, not on a flag, so a crafted POST cannot smuggle
a non-team-member through alongside a legitimate one. The picker narrows to
their team to match.

### Task assignees join the DF group automatically

Anyone assigned a task on a DF is pulled into that DF's group and notified.

**This is a reconcile, not a hook, and that is the whole design.**
`task_department_wise_scheduling.assigned_user` is written in **52 places
across 15 files** (Dashboard, Task, DF_revision, Form, Opportunity, the mobile
API…). Hooking each would mean editing 52 working call sites, missing some, and
missing every path added later. So membership is derived instead:
`sync_df_assignees()` compares the group against the DF's assignments and adds
whoever is missing. It cannot be bypassed by an assignment path it has never
heard of, and a path added next year is covered for free.

It runs when the group is opened and from `ensure_df_channel()` — usually one
indexed lookup that inserts nothing.

### Speak to type, and AI tidy-up

- **🎤 dictation** uses the browser's own `SpeechRecognition` (Chrome, Edge,
  Safari), set to `en-IN`. No API key, no server round-trip, and the audio
  never leaves the machine. Interim results stream into the box and are
  replaced when the engine settles, so it reads like dictation. The button
  hides itself where unsupported.
- **✨ AI tidy-up** fixes punctuation, capitalisation and dictation errors via
  Anthropic, server-side (`Chat::ai_polish()`). Most useful straight after
  dictating. Pressing it again restores the original — an AI that "improves"
  your message with no way back is worse than none.

**Anthropic has no speech-to-text endpoint**, so the API key cannot power
dictation; Claude takes text, images and PDFs, not audio. The key powers the
tidy-up only, and the mic works whether or not a key is configured. Key lives
in `application/config/chat_ai.php`, is read server-side only, and never
reaches the browser. The ✨ button is hidden until a key is present.

Called with raw cURL rather than the Anthropic PHP SDK: PMS has no `vendor/`
and deploys by uploading files, so there is no composer step on the server.
PMS already calls external APIs this way.

Guards: per-user throttle, a character cap (over-length messages are declined,
never truncated), `stop_reason: "refusal"` checked before the content is read,
and the message body fenced in `<message>` tags with the system prompt stating
it is data — otherwise "ignore your instructions and…" typed into a chat box
would be running our prompt.

### Fixed: route_surface was only checking a fraction of the AJAX surface

The messenger reaches the server two ways — `$.getJSON(BASE + 'directory')`
and `post('send', {...})`, where the helper is
`function post(url, data){ return $.post(BASE + url, data); }`. The test
matched only the first, so the endpoint name in every `post()` call never
appeared next to `BASE` and went unchecked — while the summary line still
reported everything resolved. That is most of the messenger.

Coverage went from 17 view endpoints to 39 once fixed; all resolve.

## 2026-08-20 — DF groups on release, and a DF-entity correction

### Fixed: a DF is `df_release`, not `df_design_form_table`

The initial port joined DF cards and DF groups to `df_design_form_table`. That
is the **design form** — a different record with its own ids. The DF everything
else in PMS means is `df_release`: it is what `Task::dfrelease()` creates, what
`task_department_wise_scheduling.df_id` points at, and what `/gantt/<df_id>`
renders (`Gantt_chart_model::get_header()`).

Joining tasks to the wrong table lines DFs up against unrelated rows and
produces confident, wrong cards. Corrected in `df_info()`, `task_info()`,
`search_records()` and `ensure_df_channel()`, and `schema_check.php` now asserts
the `df_release` / `poreceived` / `prestogroup_teams` columns so the mistake
cannot come back quietly.

Note this is a class of error the existing tests could not catch: every column
named existed, so `schema_check` passed. Only reading `Task::dfrelease()` showed
which table `df_id` actually refers to.

### New: a chat group per DF

- `Chat_model::ensure_df_channel()` creates **`DF - <df no> - Group`**, seeded
  with every active department leader plus whoever released the DF, and
  notifies them (bell, toast, desktop alert).
- "Department leader" is `prestogroup_teams.team_leader` where `status = 1`,
  filtered to users still active. That is deliberately the **same set of people**
  `Task_model::triggernotificationondfrelease()` already WhatsApps on release,
  so chat matches who is already told. It is not
  `departments.departmenthead`, which is a separate and less consistently
  maintained field.
- Hooked into `Task::dfrelease()` after the tasks are built. The hook is wrapped
  so it **cannot break a DF release**: the DF is already committed by that point
  and the group is a convenience on top of it, so a missing chat table or any
  model error is logged and swallowed.
- **Idempotent.** Run again on a DF that already has a group it adds only
  leaders who are new and notifies only them — which is what makes the button
  safe to press twice, and what brings a new department head in without
  disturbing the room.
- Whoever presses the button is joined to the group. Without that, a non-leader
  opening an existing DF group is redirected into a room `Chat::openable()`
  quietly refuses, and the messenger loads with nothing selected — which reads
  as broken.

### Restricted: DF groups are marketing's and administrators' to create

`chat_can_manage_df_groups()` gates the sidebar entry, the backfill page, the
Gantt board button and — on the server — `Chat::df()` itself, since that URL is
guessable and creating a group notifies twenty people.

`chat_df_group_scope()` then decides the reach: administrators `'all'`,
marketing `'own'` (only DFs where `df_release.added_by` is them). Everyone else
is refused. This closes CREATION, not access: members of an existing DF group
still reach it from their conversation list.

Two things found while wiring this, both worth knowing:

- **`$_SESSION['logged_in']['adminuser']` is never set.** It is read in eight
  places — `Task::dfreleasedashboard()` among them, for exactly this
  own-vs-all decision — but nothing anywhere writes it. Every one of those
  admin branches therefore fails closed and always filters to own records.
  This gate uses `user_role.isadmin` instead (SUPER ADMIN and MANAGEMENT in
  production). The existing screens are left alone; fixing them is a separate
  decision.
- **MARKETING is two departments**, ids 6 and 9, both active.
  `chat_marketing_department_ids()` resolves by NAME for that reason —
  hardcoding either id would hide the feature from half the marketing team,
  and which half would depend on data nobody thinks to check.
  `guard_matrix` asserts both ids behave identically.

### New: backfill screen

`/chat/df-groups` (sidebar: **DF Chat Groups**) lists DFs and whether each has a
group, with a one-click button for those that do not. Running DFs by default,
`?all=1` to include completed. Every button routes through the same
find-or-create call the release hook uses, so there is no second code path that
could produce a differently-named or differently-populated group.

The sidebar entry can be removed once backfill is done; the page stays reachable
by URL.

## 2026-08-20 — initial port from the CoreTech CRM

Team messaging inside PMS. See `ROLLOUT.md` for deployment and for the full
account of what differs from the original.

### New

- `application/controllers/Chat.php` — pages, JSON API, long-poll stream
- `application/models/Chat_model.php` — all data access
- `application/helpers/chat_access_helper.php` — identity, permissions, go-live switch
- `application/views/chatmodule/{index,_dock,_navwidget}.php`
- `Database/chat_001_schema.sql` — twelve `chat_` tables
- `Database/chat_002_permissions.sql` — registers CHAT in the permission screen
- `image_bank/chat_uploads/` — attachments, with script execution disabled
- `CHATMODULEDEVELOPMENT/tests/` — the suite, no DB or CI bootstrap needed

### Changed (additive, marked blocks only)

- `application/config/constants.php` — `chat_upload_path`, `chat_upload_url`, `CHAT_MODULE_VISIBLE`
- `application/config/routes.php` — `chat`, `chat/open/(:num)`, `chat/df/(:num)`
- `application/views/common/nav-menu.php` — sidebar link, topbar icon + badge, floating dock
- `application/views/gantt/df_gantt_board.php` — "Discuss this DF" button

### Adapted from the original

- **Permission key.** `module_access.role_id` and `module_capablity.role_id`
  hold a **user** id in PMS; the CoreTech original passed the session role.
  Isolated in `chat_perm_key()` and pinned by `guard_matrix.php`. This was the
  one difference that would have failed silently rather than loudly — it reads
  another user's grants and returns a confident wrong answer.
- **Outside parties removed.** No `tech_information` / `vendor_registration` in
  PMS, so the reply-only regime, the three-way nav switch and the
  Team/Technicians/Vendors tabs are gone. Sidebar tabs are now
  **All / Direct / Groups**. The `(user_id, user_type)` pair, `chat_is_external()`
  and its seven call sites were deliberately KEPT as the seam for adding a
  portal later.
- **Records instead of tickets.** Lead-and-ticket tagging became one generic
  mechanism over DF, task and lead, defined once in `chat_ref_types()`.
  `Chat::job()` → `Chat::df()`, seeding a DF's group from its author and
  everyone holding a task on it.
- **Lead URL corrected.** CoreTech linked leads to
  `Leads/update_lead_information`. In PMS that is the form's POST target, not a
  page — a GET would have run the update path with an empty `$_POST`. Now
  `Leads/edit_leads/<id>`.
- **Uploads** moved to `image_bank/chat_uploads/`, PMS's convention, from
  CoreTech's `assets/`.
- **Admin** is now `user_role.isadmin = 1` rather than the original's hardcoded
  "user_id 2" backdoor, so there is nothing to remember to close at go-live.
- **`audience_map()`** answers directly instead of querying: with one account
  table its result is a constant, and it was being paid for on every sidebar
  load.

### Not done

- Not verified against a live database. The repo cannot boot CodeIgniter on a
  developer machine and the PMS database is remote-only, so the suite tests the
  module's logic, its schema assumptions and its URL surface — not a real
  request. Section 1(e) of `ROLLOUT.md` (`/Chat/health`) is the first check to
  run after deploying.
- Editing a message does not re-scan it for new @mentions (inherited).
- A deleted message's uploaded file stays on disk and remains reachable by
  members of that conversation via its direct link (inherited).
