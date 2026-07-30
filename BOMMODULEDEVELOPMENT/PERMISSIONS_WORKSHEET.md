# Automation BOM — who gets which permission

Fill in the names, run the statements at the bottom, done. Five minutes.

`abom_003_permissions.sql` deliberately grants **nobody**, so this
decision is made on purpose rather than inherited from whoever happened
to have access to a different feature.

---

## Step A — list the candidates by name

Run this. It gives you names, roles and departments, not ids.

```sql
SELECT u.user_id,
       CONCAT(u.first_name, ' ', u.last_name) AS name,
       r.user_role   AS role,
       d.department  AS department
  FROM system_users u
  LEFT JOIN user_role   r ON r.user_role_id  = u.user_role_id
  LEFT JOIN departments d ON d.department_id = u.department_id
 WHERE u.user_status = 1
 ORDER BY d.department, r.user_role, u.first_name;
```

Sorted by department then role, so the engineering and design people
group together and you can scan rather than search.

---

## Step B — the three permissions

### 1. `AUTOMATION BOM GENERATOR`

**Allows:** creating a BOM, editing quantities on a draft, and saving it.
Nothing is released by this — a generated BOM is a draft until somebody
else approves it.

**Risk of over-granting:** low. The worst outcome is clutter in the saved
list. Normally the **longest** of the three lists: engineering and design
staff who build BOMs.

```
Names: ______________________________________________________________
       ______________________________________________________________
       ______________________________________________________________
```

### 2. `AUTOMATION BOM APPROVALS`

**Allows:** advancing a BOM through checked → engineering approved →
procurement approved, and rejecting one back to draft. This is the
authority that turns a draft into a document procurement orders parts
against.

**Risk of over-granting:** this is the one that matters. Every name here
can put their signature on a released BOM.

> **You need at least TWO people here, or nothing can ever be released.**
> The same person cannot perform two consecutive stages, and whoever
> created a BOM cannot give it the final approval. With one name on this
> list, every BOM stalls.
>
> Three or more is more comfortable — with exactly two, both must be
> available for anything to move.

```
Names: ______________________________________________________________
       ______________________________________________________________
       ______________________________________________________________
```

### 3. `AUTOMATION BOM MASTER ITEMS`

**Allows:** editing the 71 master line items — descriptions, ERP codes,
base quantities, which feature gates an item.

**Risk of over-granting: the highest of the three, and the least
obvious.** Approving a BOM affects one document, and it is signed, dated
and recorded in the approval trail. Editing a master item silently
changes **what every future BOM generates** — a changed quantity or ERP
code propagates into every BOM generated afterwards, with no signature on
it and nobody reviewing the change.

**Keep this the shortest list.** Two or three people who own the master
data.

```
Names: ______________________________________________________________
       ______________________________________________________________
```

---

## Step C — get the three submodule ids

From the output of `abom_003_permissions.sql`, or:

```sql
SELECT id, submodule FROM submodule
 WHERE moduleid = 4 AND submodule LIKE 'AUTOMATION BOM %' ORDER BY id;
```

They must match `$config['abom_submodule_ids']` in
`application/config/abom.php`.

---

## Step D — insert the grants

One statement per person per permission. `role_id` takes the **user id**
— the column name says role, the application stores a user id in it (see
MODULE_CHANGELOG.md §0.9).

```sql
INSERT INTO `module_capablity`
  (`acessid`,`role_id`,`moduleid`,`submoduleid`,`submodule_access`,
   `madd`,`medit`,`mremove`,`addedOn`,`upadtedOn`)
VALUES (0, <USER_ID>, 4, <SUBMODULE_ID>, 1, 1, 1, 0, NOW(), NOW());
```

Worked example — user 61 gets all three, where the ids are 80/81/82:

```sql
INSERT INTO `module_capablity`
  (`acessid`,`role_id`,`moduleid`,`submoduleid`,`submodule_access`,
   `madd`,`medit`,`mremove`,`addedOn`,`upadtedOn`)
VALUES (0, 61, 4, 80, 1, 1, 1, 0, NOW(), NOW()),
       (0, 61, 4, 81, 1, 1, 1, 0, NOW(), NOW()),
       (0, 61, 4, 82, 1, 1, 1, 0, NOW(), NOW());
```

---

## Step E — check it

```sql
SELECT s.submodule,
       COUNT(*) AS people,
       GROUP_CONCAT(CONCAT(u.first_name,' ',u.last_name) ORDER BY u.first_name SEPARATOR ', ') AS names
  FROM module_capablity mc
  JOIN submodule s        ON s.id = mc.submoduleid
  LEFT JOIN system_users u ON u.user_id = mc.role_id
 WHERE mc.moduleid = 4 AND mc.submodule_access = 1
   AND s.submodule LIKE 'AUTOMATION BOM %'
 GROUP BY s.submodule
 ORDER BY s.submodule;
```

Confirm before going live:

- `AUTOMATION BOM APPROVALS` has **2 or more** people.
- `AUTOMATION BOM MASTER ITEMS` is the shortest list.
- Every name is someone who should be there.

---

## Ongoing

Grants are per **user**, not per role, so **they do not follow a job
change.** A new engineer gets nothing until someone inserts rows; a
departing one keeps approval rights until someone deletes them. Re-run
step E when anybody joins, moves department or leaves.

Revoke:

```sql
DELETE FROM `module_capablity`
 WHERE `role_id` = <USER_ID> AND `moduleid` = 4 AND `submoduleid` = <SUBMODULE_ID>;
```
