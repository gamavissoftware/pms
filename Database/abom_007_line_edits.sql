-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — EDITABLE LINES (install 007)
--  Target : MySQL 5.7+ / MariaDB 10.3+
--
--  Run AFTER Database/abom_006_seed.sql.
--
--  PURELY ADDITIVE: two nullable columns on abom_bom_line. No table is
--  created, dropped or re-typed, no row is deleted, and nothing outside
--  the abom_ prefix is touched. Safe on a live install with saved BOMs.
--
--  WHY
--  ---
--  Engineering needs three things the generated sheet could not do:
--
--    1. A REMARKS column they write themselves. It was rendering the
--       master item's usage note plus the panel location on every row —
--       "Panel With Machine" repeated 58 times, which is already a
--       header chip and told the reader nothing. It is now an empty,
--       typeable field.
--
--    2. Extra rows inserted by hand, for the add-ons that are not in
--       any reference BOM.
--
--    3. Rows removed, for the parts a particular machine does not take.
--
--  NONE OF THIS TOUCHES MASTER DATA. abom_item is never written by these
--  paths — the edits live on abom_bom_line, which is already a frozen
--  per-BOM snapshot (abom_001.sql section 8). Adding a row to one BOM
--  cannot change what the next BOM generates, and cannot change the nine
--  reference builds.
--
--  WHY user_remark AND NOT THE EXISTING `remarks` COLUMN
--  ----------------------------------------------------
--  abom_bom_line.remarks already holds the master item's DATA ISSUE text
--  ("⚠ ERP 4060431 CONFLICT — ...") copied at generation time. That is
--  provenance: it is what the "Points to verify during review" banner is
--  built from, and it must not be typeable over — an engineer clearing a
--  conflict warning by typing in the box would silently remove the one
--  thing standing between a known-bad ERP code and a purchase order.
--
--  So the engineer's free text gets its own column. The two are
--  displayed in different places and neither can overwrite the other.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE `abom_bom_line`
  -- The engineer's own remark. NULL/'' renders as an empty input, and
  -- prints as an empty cell — deliberately, so an unfilled remark is
  -- visibly unfilled rather than carrying a default nobody chose.
  ADD COLUMN `user_remark` VARCHAR(255) NULL AFTER `remarks`,

  -- Where a hand-added row was inserted, so it survives a reload in the
  -- position the engineer put it. Manual rows are renumbered into the
  -- S.NO. sequence on save; this records the line_no they were placed
  -- after, for the audit trail and for re-sorting after a regeneration.
  ADD COLUMN `inserted_after` SMALLINT UNSIGNED NULL AFTER `is_manual_add`;

-- ---------------------------------------------------------------------
-- POST-INSTALL CHECK
--
--   SHOW COLUMNS FROM `abom_bom_line` LIKE 'user_remark';     -- 1 row
--   SHOW COLUMNS FROM `abom_bom_line` LIKE 'inserted_after';  -- 1 row
--
-- Existing saved BOMs get NULL in both, which is exactly right: they
-- were generated before either field existed, and they still render and
-- print unchanged.
-- ---------------------------------------------------------------------
