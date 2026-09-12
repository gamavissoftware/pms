-- ---------------------------------------------------------------------
-- abom_012_variant_reference.sql
--
-- Gives every BUILD a reference machine, so the Reference BOMs register
-- can offer "Generate BOM" and land on a generator already configured
-- for that build.
--
-- WHY THIS IS A SCHEMA CHANGE AND NOT A LOOKUP
--
-- The information genuinely was not stored anywhere:
--
--   * abom_variant_rule carries min/max AXES and SPEED, because those
--     select the build — but nothing about TRACKS, which do not affect
--     selection at all, only quantities. A rule can say "6 axes at 90
--     PPM or above" and still tell you nothing about whether that
--     machine runs 6 or 12 tracks.
--   * abom_variant.name spells the machine out for five of the eleven
--     builds ("SPM1200L 5 axis / 12 track / 100 PPM") and not for the
--     other six ("iQ-R high speed continuous"). Parsing a display label
--     that is only sometimes formatted that way is not a foundation.
--
-- So the reference machine is recorded as data. A build added next year
-- gets these filled in with it, and the button is correct for it with
-- no code change — which is the point, since more builds are expected.
--
-- Every value below is taken from the reference configuration already
-- verified against the released DF for that build. Nothing is invented.
--
-- NULL is allowed and is not a fault: a build whose reference machine
-- has not been recorded still generates, the module falls back to the
-- build's own selection rule and the configured defaults, and the
-- register says so rather than offering a button that lies.
--
-- SAFE TO RE-RUN. Adds columns only if absent, and the backfill is an
-- UPDATE keyed on id.
--
-- Run AFTER: abom_005, abom_006, abom_009, abom_011.
-- ---------------------------------------------------------------------

-- --- columns ---------------------------------------------------------
-- ALTER TABLE ... ADD COLUMN IF NOT EXISTS is MariaDB syntax and this
-- install is MariaDB 11.4 (recorded in MODULE_CHANGELOG §0.7). On MySQL
-- 8 these would need the IF NOT EXISTS dropping.
ALTER TABLE `abom_variant`
  ADD COLUMN IF NOT EXISTS `ref_axes`        SMALLINT NULL
      COMMENT 'Reference machine: axes. NULL = not recorded'
      AFTER `source_df`,
  ADD COLUMN IF NOT EXISTS `ref_tracks`      SMALLINT NULL
      COMMENT 'Reference machine: tracks. Not derivable from any rule'
      AFTER `ref_axes`,
  ADD COLUMN IF NOT EXISTS `ref_speed_ppm`   SMALLINT NULL
      COMMENT 'Reference machine: speed in PPM'
      AFTER `ref_tracks`,
  ADD COLUMN IF NOT EXISTS `ref_motion_type` ENUM('Intermittent','Continuous') NULL
      COMMENT 'Reference machine: motion type'
      AFTER `ref_speed_ppm`,
  ADD COLUMN IF NOT EXISTS `ref_machine_side` VARCHAR(8) NULL
      COMMENT 'Reference machine: LHS / RHS / N/A'
      AFTER `ref_motion_type`;

-- --- backfill --------------------------------------------------------
-- id, code and source DF are stated on every row so a reviewer can
-- check each line against the released drawing without a join.
--
-- j4_units and battery are NOT repeated here: abom_variant already
-- carries default_j4_units and default_battery, and duplicating them
-- would create two answers to one question.

UPDATE `abom_variant` SET                     -- FX5-J4, from DF-1827
  `ref_axes` = 8,  `ref_tracks` = 12, `ref_speed_ppm` = 140,
  `ref_motion_type` = 'Intermittent', `ref_machine_side` = 'LHS'
WHERE `id` = 2;

UPDATE `abom_variant` SET                     -- IQR-STD, from DF-1770
  `ref_axes` = 10, `ref_tracks` = 12, `ref_speed_ppm` = 140,
  `ref_motion_type` = 'Intermittent', `ref_machine_side` = 'LHS'
WHERE `id` = 3;

UPDATE `abom_variant` SET                     -- IQR-HS, from DF-1826
  `ref_axes` = 15, `ref_tracks` = 12, `ref_speed_ppm` = 180,
  `ref_motion_type` = 'Continuous',   `ref_machine_side` = 'N/A'
WHERE `id` = 4;

UPDATE `abom_variant` SET                     -- IQR-FLM, from DF-1855
  `ref_axes` = 15, `ref_tracks` = 9,  `ref_speed_ppm` = 80,
  `ref_motion_type` = 'Continuous',   `ref_machine_side` = 'RHS'
WHERE `id` = 5;

UPDATE `abom_variant` SET                     -- IQR-TCF, from DF-1805
  `ref_axes` = 11, `ref_tracks` = 6,  `ref_speed_ppm` = 120,
  `ref_motion_type` = 'Continuous',   `ref_machine_side` = 'N/A'
WHERE `id` = 6;

UPDATE `abom_variant` SET                     -- FX5-TCF, from DF-1883
  `ref_axes` = 11, `ref_tracks` = 6,  `ref_speed_ppm` = 70,
  `ref_motion_type` = 'Intermittent', `ref_machine_side` = 'N/A'
WHERE `id` = 7;

UPDATE `abom_variant` SET                     -- FX5-1723, from DF-1723
  `ref_axes` = 5,  `ref_tracks` = 12, `ref_speed_ppm` = 100,
  `ref_motion_type` = 'Intermittent', `ref_machine_side` = 'N/A'
WHERE `id` = 8;

UPDATE `abom_variant` SET                     -- FX5-1778, from DF-1778
  `ref_axes` = 7,  `ref_tracks` = 8,  `ref_speed_ppm` = 80,
  `ref_motion_type` = 'Intermittent', `ref_machine_side` = 'LHS'
WHERE `id` = 9;

UPDATE `abom_variant` SET                     -- FX5-1808, from DF-1808
  `ref_axes` = 6,  `ref_tracks` = 12, `ref_speed_ppm` = 100,
  `ref_motion_type` = 'Intermittent', `ref_machine_side` = 'N/A'
WHERE `id` = 10;

UPDATE `abom_variant` SET                     -- FX5-1858, from DF-1858
  `ref_axes` = 5,  `ref_tracks` = 6,  `ref_speed_ppm` = 70,
  `ref_motion_type` = 'Intermittent', `ref_machine_side` = 'LHS'
WHERE `id` = 11;

UPDATE `abom_variant` SET                     -- FX5-1864, from DF-1864
  `ref_axes` = 6,  `ref_tracks` = 12, `ref_speed_ppm` = 80,
  `ref_motion_type` = 'Intermittent', `ref_machine_side` = 'LHS'
WHERE `id` = 12;

-- --- verify ----------------------------------------------------------
-- Expect 11 rows, every one with a reference machine, and each
-- selects_correctly = 1. A 0 in that column means the recorded machine
-- would NOT route to its own build, which is a data fault worth
-- knowing about before anyone presses the button.
SELECT v.`id`, v.`code`, v.`source_df`, v.`machine_model`,
       v.`ref_axes`, v.`ref_tracks`, v.`ref_speed_ppm`,
       v.`ref_motion_type`, v.`ref_machine_side`
  FROM `abom_variant` v
 WHERE v.`is_active` = 1
 ORDER BY v.`sort_order`, v.`id`;
