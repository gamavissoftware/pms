# Automation BOM — post-deploy check

**Ten minutes, in a browser. No developer needed.**

Do this straight after deploying, before anyone else is given access.

You need: a login that has been granted `AUTOMATION BOM GENERATOR` and
`AUTOMATION BOM APPROVALS` (one super admin is enough for now), and the
released **DF-1827** and **DF-1826** sheets on paper or screen for step 12.

Replace `YOURSITE` below with your normal address, e.g.
`https://crm.sunderindoil.com` or `https://shubhampack.in/pms`.

---

## Before you start — two things to know

**Nothing here changes anything for other users.** The module is
invisible to anyone without a grant, and at this point only you have one.

**Every step is read-only or undoable, except one.** Step 8 saves a BOM
to the database. That is a new record; it affects nothing else, and you
can ignore or delete it afterwards. There is **no point of no return**.

**If any step fails**, stop and write down the step number and what you
saw. `ROLLOUT.md` has a **Rollback** section that removes everything and
has been rehearsed. Nothing is at risk.

---

## Part 1 — the rest of the application still works

The most important checks. Do these first.

### 1. Dashboard

Open `YOURSITE/index.php/Dashboard`

**Expect:** loads normally, exactly as it did yesterday.
**If not:** stop. Roll back. This means the deployment disturbed
something it should not have.

### 2. DF Dispatch Morning Meeting

Open `YOURSITE/index.php/Df_dispatch_plan`

**Expect:** loads normally.

### 3. Task Management

Open `YOURSITE/index.php/Task_management`

**Expect:** loads normally.

> If steps 1–3 all look normal, the risky part is already behind you.
> Everything after this is about the new module only.

---

## Part 2 — the new module works

### 4. The generator screen opens

Open `YOURSITE/index.php/abom/generate`

**Expect:** a page headed **"Automation BOM Generator"** with a dark blue
banner, a "Machine Configuration" panel down the left, and a table of
line items.

**If you get a "not installed" message:** the database scripts have not
been run. See `ROLLOUT.md` step 2.
**If you get a login page:** your session expired. Log in and retry.

### 5. It defaults to the DF-1808 configuration — **29 items**

On that same page, look at the boxes above the table.

**Expect:** **LINE ITEMS = 29**, TOTAL QTY = 57, SECTIONS = 5, the PLC
Family panel showing **FX5 Series**, and the Build Variant panel showing
**FX5-JE**.

**Both of those matter.** The family says which CPU; the variant says
which machine. If either is wrong, stop and report it.

### 6. The 8-axis configuration switches BUILD, not just family — **29 items**

In the left panel, click the **DF-1827** card under "Load Reference
Configuration".

**Expect:** the table redraws within a second or two. **LINE ITEMS = 29**,
TOTAL QTY = 74, SECTIONS = 5. The PLC Family badge still reads **FX5
Series** — but the Build Variant badge changes from **FX5-JE** to
**FX5-J4**.

Scroll to Servo Amplifiers & Motors and confirm the amplifiers changed
with it: `MR-JE-300B` / `MR-JE-200B` before, `MR-J4-350B` / `MR-J4-200B`
now. **Neither BOM may show both.** If you can see an MR-JE and an MR-J4
amplifier on the same sheet, stop — that is the failure this variant
layer exists to prevent, and procurement would order twice the drives.

**If nothing happens:** the page's JavaScript is not loading. Check that
`assets/abom/` was copied up.

### 7. Quantities follow the configuration

Still on DF-1827, find the row **SSCNET CABLE, 0.5 METRES**
(`MR-J3BUS05M`). Note its quantity — it should be **7**.

Now change **Axes** in the left panel from 8 to **9** and click away from
the box.

**Expect:** that row's quantity becomes **8** (one less than the axis
count). Everything else stays as it was — still 29 items, still FX5-J4.

**Why this matters:** it proves quantities are calculated, not copied from
a stored list. That is the whole reason this module exists.

> **Do not use 10 or more axes for this test.** At 10 axes the module
> correctly switches to the iQ-R family and the IQR-STD build, the item
> list changes to 34, and this row disappears altogether — it is an
> FX5-only part. That is correct behaviour, not a fault, but it makes the
> test confusing.

Set Axes back to 8 before continuing.

### 7b. The temperature card follows the TRACK count

This is the rule the module was re-seeded for, so check it explicitly.

Still on DF-1827, find **4 CH. TEMPERATURE CARD** (`FX5-4LC`, ERP
**2020122**). At 12 tracks its quantity is **7**.

Change **Tracks** in the left panel and confirm the quantity each time:

| Set Tracks to | 6 | 8 | 9 | 12 |
|---|---|---|---|----|
| **Expect** | **4** | **5** | **6** | **7** |

The rule is `(((tracks + 1) × 2) + 2) ÷ 4`, rounded up — 9 tracks gives
5.5, and half a card cannot be bought.

**If the number does not move at all**, the item is still on the old
`MANUAL` formula: `Database/abom_006_seed.sql` has not been run, or
`abom_002_seed.sql` was run after it and put the old data back. See
`ROLLOUT.md` step 2.

Set Tracks back to 12 before continuing.

### 8. Saving works, and survives a reload

Click **DF-1826** to load the 42-item configuration again, then click
**Save BOM**.

**Expect:** the page moves to a saved BOM with a number like **ABOM-1** in
the top-left, and the quantity boxes are still editable.

Now change any quantity — say the first row from 1 to **5** — click away,
then **reload the page (F5)**.

**Expect:** the **5 is still there**, with a small pencil mark ✎ beside
it and "QTY EDITED" in the STATUS column.

**If the 5 reverts to 1:** stop and report it. An edit that does not
survive a reload is the one failure that would matter later — the printed
sheet would not match what you saw.

### 9. The saved list shows it

Open `YOURSITE/index.php/abom/list`

**Expect:** your `ABOM-1` listed with 42 lines and status **Draft**.

### 10. A PDF downloads and opens

Back on the BOM (`abom/view/1`, or click **Open** from the list), scroll
to the **Workflow** panel and click **📥 PDF**.

**Expect:** a PDF downloads. Open it.

- It is **landscape**, about 2 pages.
- The table has nine columns ending in REMARKS and STATUS.
- At the bottom: **three signature boxes** — Prepared By (Engineering),
  Checked By, Approved By (Customer).

### 11. The Ω character prints properly

In that PDF, find the last row: **DYNAMIC BRAKING RESISTOR (DBR)**.

**Expect:** it reads **`6.7Ω, 500W`** — a proper omega symbol.

**If you see `6.7?, 500W` or `6.7, 500W`:** stop and report it. A missing
symbol is worse than a wrong one, because `6.7, 500W` reads like a
plausible specification.

### 12. Compare against the released DF sheets — **on paper**

Print the PDF from step 10, and print the DF-1827 one too. Lay each beside
the corresponding released DF sheet.

**Check:**
- The item counts match: **29** for DF-1827, **42** for DF-1826.
- No section heading is stranded alone at the bottom of a page.
- No column is cut off — especially DESCRIPTION and REMARKS.
- The signature boxes are big enough to actually sign.
- The coloured row backgrounds printed (they carry meaning).

This is the one check no amount of testing can replace.

---

## Part 3 — security

### 13. The developer login is not reachable

Open `YOURSITE/index.php/abomdevlogin`

**Expect: a 404 / "page not found".**

**If you get anything else — a blank page, an error, or worse a message
saying "session established" — stop immediately.** A file that should not
have been deployed has been. Delete
`application/controllers/Abomdevlogin.php` from the server, then tell
whoever deployed it.

### 14. The menu item appears only for granted users

Look at your left-hand navigation menu.

**Expect:** an **"Automation BOM Generator"** entry, because you have been
granted access.

Now **sign in as a second account that has not been granted anything** —
your own second login, or a colleague's with their permission. Do not just
ask them to glance at it; log in and look.

**Expect:** the "Automation BOM Generator" entry is **absent**, and the
rest of their menu is unchanged.

**If it appears for an ungranted user, stop and report it.** Do not
continue to step 4 of `ROLLOUT.md` and do not grant anyone else. It would
mean the permission gate is not working, and that is a finding worth
having before anyone else logs in — which is the whole reason this check
runs while only you have access.

**Then confirm the URL is closed too, not just the menu.** While signed in
as that ungranted account, type `YOURSITE/index.php/abom/generate`
directly.

**Expect:** you are sent back to the Dashboard with a red message —
*"You do not have permission to use the Automation BOM generator."* You
should **not** see a working generator screen.

**If you do see the generator**, stop and report it. A hidden menu item is
not access control; the URL has to be closed as well.

### 15. One person cannot approve a BOM alone

On your saved BOM, scroll to the **Workflow** panel.

**Expect:** a message listing what is blocking the next stage — something
like *"19 MANUAL quantity lines still need confirming"* and *"2 ERP
conflict lines must be acknowledged"*. The approve button is greyed out
with the reason written next to it.

**This is correct.** Those items need a human decision before a BOM can be
submitted, and once submitted a **different person** must check it. One
person cannot take a BOM from draft to approved on their own — that is
deliberate, and it is why `AUTOMATION BOM APPROVALS` needs at least two
names.

---

## 16 — clone a BOM, then re-specify the clone

This is the check that matters most for day-to-day use: quoting the next
machine should mean copying the last one and changing three numbers.

1. `/index.php/abom/list` → **Clone** on any BOM. You land on the copy,
   which is a **draft**.
2. The badge in the header reads **DRAFT · EDITABLE**, not
   *FOR CUSTOMER REVIEW*.
3. In the left panel, **DF Reference, Machine Model, Axes, Tracks, Speed,
   Side and Motion Type are all editable** — typeable boxes and
   dropdowns, not greyed-out text. So are MR-J4 Units, Battery Qty, the
   feature checkboxes and the two overrides.
4. There is an **Apply configuration** button in the top bar. There is
   **no** *Save BOM* button — that one belongs to the generator and would
   create a second document.
5. Before changing anything, type a remark on one row and add a manual
   row, then press **Save changes** under the table.
6. Now change **Tracks** to a different number and press **Apply
   configuration**. Confirm the dialog — it lists exactly what you
   changed.
7. Expect a message naming the change, the regenerated line count, and
   what was carried over, e.g.
   *"ABOM-14 updated: tracks 12 -> 6. 30 lines regenerated. Carried over:
   1 added row, 1 remark."*
8. After the reload: the **temperature card quantity has changed** to
   match the new track count, your **typed remark is still there**, and
   your **added row is still at the bottom**.
9. **The original BOM is untouched.** Open it and confirm its
   configuration and line count are exactly as before.

### 16b — the gate holds

Open an **approved** BOM. The configuration panel must be locked, the
badge must read *FOR CUSTOMER REVIEW & APPROVAL*, and there must be no
*Apply configuration* button. A released document is duplicated or
revised, never re-specified in place.

---

## 17 — download file names

Export any BOM as PDF, Excel and CSV. The saved files must be named for
the machine, not for the BOM number:

```
DF-1808 - SPM1200L, 6A, 12T, 100PPM - INTERMITTENT - MITSUBISHI.pdf
```

Revision appears only when it is not `00`; the side (LHS/RHS) appears
only when it is set. A BOM with no DF reference falls back to its BOM
number. **A file arriving with no name at all (just `.pdf`) is a fault** —
report it.

---

## 18 — manufacturer on the register

`/index.php/abom/list`

1. There is a **MANUFACTURER** column between BUILD and LINES.
2. Most rows read **MITSUBISHI** with a small **+1** or **+2** beside it.
   Hover the badge — it lists the other brands on that BOM (RECKON for
   the braking resistor, AUTONICS for the temperature card).
3. A **All manufacturers** dropdown sits in the filter bar next to the
   model filter. Choose **RECKON** and press **Filter** — only BOMs that
   contain a RECKON part are listed, and the `N SAVED` count in the
   header matches the number of rows shown.
4. Choose **All manufacturers** again and confirm the full list returns.

**If the dropdown is missing**, that is correct behaviour only when every
saved BOM uses a single brand — the filter hides itself until there are
at least two. With the shipped reference BOMs there should be three.

**If the row count and the `N SAVED` badge disagree**, report it: that
would mean the filter is duplicating header rows and the query needs
looking at.

---

## 19 — a DF reference cannot be used twice

1. `/index.php/abom/generate`. In **DF Reference**, type a reference that
   already exists — `DF-1805A` if ABOM-10 is still on the register.
2. **Within about half a second of stopping typing**, the field turns red
   and a message names the BOM that holds it, with a link to open it.
   You should not have to press anything.
3. Press **Save BOM**. It refuses, and says the same thing.
4. Change the reference to something unused — the message turns green
   and reads *"… is not used by any other BOM."* Save now works.

### 19b — what must still be allowed

- **Revisions.** Open an approved BOM and use **Create revision**. It
  succeeds: REV.01 of the same drawing number is legitimate and is not
  blocked.
- **Re-saving a BOM unchanged.** Open a draft, change Tracks only, press
  **Apply configuration**. It must not complain about the DF reference —
  a BOM does not clash with itself.
- **Deleted BOMs.** Delete a draft, then create a new BOM using its DF
  reference. Allowed — a deleted document does not hold a drawing number.

### 19c — duplicate no longer copies the reference

Clone any BOM. The copy's **DF Reference is empty**, with the note
recording which BOM it came from. This is deliberate: a copy is a new
document for the next machine and will have its own drawing number.
Type the new reference before submitting it.

---

## 20 — the header bar

Open any BOM and the generator.

1. The bar above the document holds **buttons only** — no row of small
   coloured badges, and nothing wrapping onto a second line under them.
2. On a **generator** screen the sidebar shows **Machine Features** as
   tick boxes, as before.
3. On an **approved** BOM the sidebar shows **Machine Features** as a
   short read-only list of the gates that are switched on. This is the
   check that matters: those gates decide whether the braking resistor,
   the I-mark sensor and the perforation axis are on the sheet, and they
   used to be visible only in the badges that were removed.
4. A BOM with no feature gates enabled shows no Machine Features heading
   at all. That is correct.

---

## 21 — Generate BOM from a build

**Run `Database/abom_012_variant_reference.sql` first.** Its final SELECT
must return **11 rows, every one with ref_axes / ref_tracks /
ref_speed_ppm filled in.** A NULL there is not fatal but means that
build's button will prefill defaults and say so.

1. `/index.php/abom/master_bom`. Each active row now has **+ Generate
   BOM** next to **Open sheet**.
2. Press it on **FX5-1808**. You land on the generator with a blue note
   reading *"Configured for the FX5-1808 build (DF-1808) — SPM1200L,
   6 axis, 12 track, 100 PPM intermittent."*
3. Check the sidebar agrees: Axes **6**, Tracks **12**, Speed **100**,
   Motion **Intermittent**, Model **SPM1200L**.
4. Check the **BUILD VARIANT** badge reads **FX5-1808** and does *not*
   say "Manual override". The build was reached by the normal selection
   rules, not pinned.
5. The sheet should show **29 line items**.

Spot-check two more against this table:

| build | expect | lines |
|---|---|---|
| IQR-HS | 15 axis, 12 track, 180 PPM, Continuous | 42 |
| FX5-1858 | 5 axis, 6 track, 70 PPM, Intermittent | 20 |
| IQR-TCF | 11 axis, 6 track, 120 PPM, Continuous | 40 |

**If you see a red warning** saying the configuration does not select
that build, report it — the recorded machine and the selection rules
disagree, which is a master-data fault, not something you did.

**A retired build has no Generate button.** That is correct.

---

## 22 — searching the build register

`/index.php/abom/master_bom`

1. A filter bar sits above the table: a search box, then **All
   families**, **All models**, **All panel locations**, **Any flags** and
   **Active and retired**.
2. Type `1808` and press **Filter**. You get **FX5-1808** and
   **FX5-JE**. Both are right — FX5-JE is the retired combined build and
   DF-1808 is one of the sheets it was assembled from. Add **Active
   only** and just FX5-1808 remains.
3. Type `6 track`. Only builds whose recorded machine really runs six
   tracks come back — **FX5-1858, FX5-TCF, IQR-TCF**. If a 12-track
   build such as FX5-1808 appears here, report it.
4. Type `tilting cup` — two builds.
5. Set **Any flags** to *Nothing to resolve* — only builds with no
   conflicts, no missing ERP codes and nothing flagged for review.
6. The chip beside the bar reads **"3 of 12 builds"** while filtered and
   plain **"12 builds"** when not, with a **Clear filters** link only in
   the first case.
7. A search matching nothing shows *"No build matches that filter"* with
   a way back — not an empty table.

The URL carries the filters, so a narrowed view can be bookmarked or
sent to a colleague.

---

## 23 — version history

**No SQL for this one.** `abom_bom_revision` has been collecting
snapshots since the module went live; this is the first release that
reads them.

1. Open any saved BOM. There is a **🕐 History** button in the bar.
2. It lists every revision of that BOM number, newest first, each with
   who prepared, checked and approved it, and when.
3. On a BOM that has only ever been REV.00, expect one card reading
   *"First revision. N lines, nothing to compare against."* That is
   correct — there is nothing before it.

### 23b — the changelog, on a BOM that has been revised

If you have no revised BOM yet, make one: approve a draft, then use
**Create revision**, change the tracks on the new revision and apply it.

The history should then show, on the newer revision, coloured chips such
as *3 quantity changes*, *1 config change*, and groups listing:

- **Removed from the BOM** — in red, first
- **Added to the BOM**
- **Machine configuration** — e.g. `Tracks 12 → 6`
- **Quantities** — e.g. the temperature card `7 → 4`

**Check the removals carefully.** If a part left the sheet between
revisions and is *not* listed under "Removed", report it — that is the
one failure this screen cannot be allowed to have.

### 23c — an archived version must not look issuable

Press **🖼 View this version** on an older revision.

- The header reads **ARCHIVED · NOT FOR ISSUE**.
- There is **no Export CSV / Excel / PDF, and no Print button.**
- There are no quantity boxes, no row + / × buttons, no remark fields
  and no approval controls.

If any export or print control appears here, stop and report it: a PDF
from this screen would be indistinguishable from a current BOM.

---

## 24 — importing a reference BOM

**No SQL.** One directory must be writable:
`exported_files/abom_import` (created automatically if the parent is
writable — check afterwards that it exists).

### 24a — the safest possible first test

Do this before importing anything real.

1. `/index.php/abom/master_bom` → **📤 Import a BOM**.
2. **📥 Download the template**. Open it — nine columns, one section
   heading row, four example items.
3. Upload that template straight back. Fill in the build as: code
   `TEST-IMPORT`, DF `TEST`, name anything, family FX5, model SPM1200L,
   **6 axes, 12 tracks, 100 PPM**.
4. Press **🔍 Check the sheet**. Nothing is written yet — the header
   says so.
5. On the preview, check the **QUANTITY RULE** column:
   - `4 CH. TEMPERATURE CARD` qty 7 → **TRACK_TEMP**
   - `SERVO AMPLIFIER` and `SERVO MOTOR` qty 6 → **AXES**
   - `CPU MODULE` qty 1 → **FIXED**
6. Press Cancel. Nothing has been created — confirm `TEST-IMPORT` does
   not appear in the register.

### 24b — a real DF

Now import one of your own sheets.

1. Enter the machine **the DF was drawn for** — not a typical one. Every
   quantity rule is worked out against it.
2. On the preview, read every row marked **DECIDE** in orange. Those are
   rows where the number fits more than one rule and the description
   does not say which. They import as FIXED unless you change them.
3. Rows in red **cannot** be imported and are ticked out automatically.
   Fix the sheet and upload again if you need them.
4. Confirm. You land on the new build's sheet.
5. **Then generate from it**: `/abom/generate?build=<the new build>` —
   or the **+ Generate BOM** button on its register row — and check the
   line count and a few quantities against the DF.

### 24c — what the import must never do

- It must **never** modify an existing build. Re-importing with a code
  that already exists is refused, not merged.
- A part number already used by another build produces a *warning*, not
  an error — each build keeps its own copy, which is what makes them
  independent.

---

## Done

If steps 1–3 passed and 4–24 behaved as described, the module is working
on the real server.

**Next:** fill in `PERMISSIONS_WORKSHEET.md` and grant the real users.
That is the first moment anything changes for the business.

**If anything failed**, note the step number and what you saw. Nothing is
at risk — `ROLLOUT.md` → **Rollback** removes everything, has been
rehearsed, and touches nothing outside the module's own files and the
`abom_` tables.

---

## Quick reference

| Step | Expect |
|---|---|
| 1–3 | Existing pages load normally |
| 5 | 29 line items, FX5, build FX5-JE |
| 6 | 29 line items, FX5, build FX5-J4, MR-J4 amplifiers replace MR-JE |
| 7 | At 9 axes, SSCNET 0.5 M quantity becomes 8 |
| 7b | Temperature card: 6T→4, 8T→5, 9T→6, 12T→7 |
| 8 | Edited quantity survives a reload |
| 10 | PDF, landscape, 3 signature boxes |
| 11 | `6.7Ω, 500W` |
| 13 | `/index.php/abomdevlogin` → **404** |
| 14 | Menu item only for granted users |
| 15 | Approve is blocked with a stated reason |
| 16 | Clone is editable; tracks change re-derives cards; hand work survives |
| 16b | Approved BOM has no *Apply configuration* button |
| 17 | `DF-1808 - SPM1200L, 6A, 12T, 100PPM - INTERMITTENT - MITSUBISHI.pdf` |
| 18 | MANUFACTURER column + working brand filter on `/abom/list` |
| 19 | Duplicate DF ref flagged as you type, and refused on save |
| 19b | Revisions, self-saves and reuse of a deleted BOM's ref still work |
| 19c | A clone's DF Reference is empty |
| 20 | Header bar is buttons only; features listed in the sidebar |
| 21 | + Generate BOM lands preconfigured; FX5-1808 → 6A/12T/100, 29 lines |
| 22 | `6 track` returns only 6-track builds; count reads "N of 12" |
| 23 | History button lists every revision with who and when |
| 23b | Changelog lists removals first, in red |
| 23c | Archived version has no export and no print |
| 24a | Template imports; temperature card infers TRACK_TEMP |
| 24b | DECIDE rows reviewed before confirming |
| 24c | Duplicate build code refused, never merged |
