-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — PLC RULES BY MACHINE MODEL (008)
--  Target : MySQL 5.7+ / MariaDB 10.3+
--
--  Run AFTER Database/abom_006_seed.sql. Then Database/abom_009_seed_1250p.sql.
--
--  PURELY ADDITIVE: one nullable column. No row is inserted, updated or
--  deleted, and NULL means "any model" — which is what all four existing
--  rules get, so family detection is bit-for-bit unchanged until a rule
--  that names a model is added.
--
--  WHY
--  ---
--  abom_variant_rule has had machine_model since 005; abom_plc_rule
--  never did, because the PLC family looked model-independent: axes and
--  speed decided it and that was that.
--
--  DF-1883 (SPM1250P, 11 axis, 6 track, 70 PPM, intermittent) falsifies
--  that. It runs ELEVEN axes on FX5, by fitting two simple-motion cards:
--
--      FX5-80SSC-S   8 axis
--      FX5-40SSC-S   4 axis      = 12 axes of motion capacity
--
--  The existing priority-10 rule says "axis count 10 or more exceeds the
--  FX5-80SSC-S 8-axis simple motion limit", which was true of every
--  machine described by the first nine reference BOMs and is not true of
--  this one. The limit is a property of ONE CARD, not of the family.
--
--  The rule is not being weakened. It stays exactly as it is, and
--  abom_009 adds a narrower rule AHEAD of it, scoped to the model, the
--  axis ceiling and the motion type that actually describe this machine.
--  Everything else still trips the 10-axis rule and still lands on iQ-R.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE `abom_plc_rule`
  -- NULL = any model, which is what every pre-existing rule means and
  -- what the evaluator already does with a NULL bound. Same column, same
  -- semantics and same position as abom_variant_rule.machine_model, so
  -- the two rule tables stay readable side by side.
  ADD COLUMN `machine_model` VARCHAR(32) NULL
      COMMENT 'NULL = applies to any model' AFTER `priority`;

-- ---------------------------------------------------------------------
-- POST-INSTALL CHECK
--
--   SHOW COLUMNS FROM `abom_plc_rule` LIKE 'machine_model';   -- 1 row
--   SELECT COUNT(*) FROM `abom_plc_rule`
--    WHERE `machine_model` IS NOT NULL;                       -- 0
--
-- Zero is correct here: this script only opens the door. abom_009 is
-- what walks through it.
-- ---------------------------------------------------------------------
