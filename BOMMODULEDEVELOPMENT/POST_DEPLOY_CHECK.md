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

### 5. It defaults to the iQ-R configuration — **42 items**

On that same page, look at the boxes above the table.

**Expect:** **LINE ITEMS = 42**, TOTAL QTY = 129, SECTIONS = 5, and the
PLC Family panel showing **iQ-R Series**.

**This is the number that matters.** If it is not 42, stop and report it.

### 6. The FX5 configuration gives **29 items**

In the left panel, click the **DF-1827** card under "Load Reference
Configuration".

**Expect:** the table redraws within a second or two. **LINE ITEMS = 29**,
TOTAL QTY = 74, SECTIONS = 3, and the PLC Family badge changes to
**FX5 Series**.

**If nothing happens:** the page's JavaScript is not loading. Check that
`assets/abom/` was copied up.

### 7. Quantities follow the configuration

Still on DF-1827, find the row **SSCNET CABLE, 0.5 METRES**
(`MR-J3-BUS05M`). Note its quantity — it should be **7**.

Now change **Axes** in the left panel from 8 to **9** and click away from
the box.

**Expect:** that row's quantity becomes **8** (one less than the axis
count). Everything else stays as it was — still 29 items, still FX5.

**Why this matters:** it proves quantities are calculated, not copied from
a stored list. That is the whole reason this module exists.

> **Do not use 10 or more axes for this test.** At 10 axes the module
> correctly switches to the iQ-R family, the item list changes to 42, and
> this row disappears altogether — it is an FX5-only part. That is correct
> behaviour, not a fault, but it makes the test confusing.

Set Axes back to 8 before continuing.

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

Now ask a colleague who has **not** been granted anything to look at their
menu.

**Expect:** they do **not** see it, and their menu is otherwise unchanged.

**If they do see it:** the grant list is wider than intended. Check
`ROLLOUT.md` → Ongoing administration.

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

## Done

If steps 1–3 passed and 4–15 behaved as described, the module is working
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
| 5 | 42 line items, iQ-R |
| 6 | 29 line items, FX5 |
| 7 | At 9 axes, SSCNET 0.5 M quantity becomes 8 |
| 8 | Edited quantity survives a reload |
| 10 | PDF, landscape, 3 signature boxes |
| 11 | `6.7Ω, 500W` |
| 13 | `/index.php/abomdevlogin` → **404** |
| 14 | Menu item only for granted users |
| 15 | Approve is blocked with a stated reason |
