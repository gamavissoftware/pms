-- =====================================================================
--  abom_013_omron.sql
--
--  Adds the OMRON NJ build family, so a released DF drawn around an
--  OMRON controller can be imported at /abom/import.
--
--  WHY THIS IS NEEDED
--  ------------------
--  DF-1822 (SPM 1200L, 12 track, 23 axis, flow meter) is an OMRON sheet:
--  NJ501-1500 controller, NX I/O, R88D servo drives. The module shipped
--  with two families, FX5 and iQ-R, and both are Mitsubishi. The import
--  form makes you choose a PLC family and validates it against
--  abom_plc_family, so that sheet could not be imported at all.
--
--  WHAT IT DOES
--    1. one row in abom_plc_family        (the OMRON NJ family)
--    2. three rows in abom_section        (the headings that sheet uses)
--    3. one row in abom_plc_rule          (so the family can be REACHED)
--
--  Creates NO items and NO build variant. Those come from importing the
--  DF itself, which is the point: the import writes the variant, its
--  selection rule and one item per line, in one transaction.
--
--  SAFE TO RE-RUN. It aborts with a clear message if the family already
--  exists, and writes nothing. It uses no TRUNCATE and never disables
--  FOREIGN_KEY_CHECKS -- both of which broke abom_002 under phpMyAdmin.
--
--  ROLLBACK is at the bottom, commented out.
-- =====================================================================

SET NAMES utf8mb4;

START TRANSACTION;

-- Pre-flight. Re-running this must not create a second OMRON family:
-- items and variants would split across the two and nobody would see it.
SELECT COUNT(*) INTO @exists FROM `abom_plc_family` WHERE `code` = 'OMRON';

-- Fails loudly rather than quietly doing half the job.
SELECT IF(@exists = 0, 'ok',
    'ABORT: an OMRON family already exists. This script has been run. Nothing changed.')
  INTO @check;

-- --- 1. the family -------------------------------------------------
-- j4_units and battery default to 0: MR-J4 amplifiers and their battery
-- sets are Mitsubishi parts, so the J4_STO and BATTERY formulas have
-- nothing to read on an OMRON build. Leaving them non-zero would put
-- phantom quantities on the sheet.
INSERT INTO `abom_plc_family`
  (`code`, `name`, `cpu_summary`,
   `default_j4_units`, `default_battery`, `default_panel_location`, `sort_order`)
SELECT 'OMRON', 'OMRON NJ Series',
       'NJ501-1500 (64-axis EtherCAT motion controller) + NX I/O + R88D-1SN servo',
       0, 0, 'Standalone', 3
 WHERE @exists = 0;

SET @family_id = LAST_INSERT_ID();

-- --- 2. its sections -----------------------------------------------
-- Sections are per family. Without them every imported line is filed
-- under a fallback heading and the import reports a warning on each row.
-- These three are the headings DF-1822 actually uses. Codes follow the
-- established per-family prefix (F1-F3 for FX5, R1-R5 for iQ-R).
INSERT INTO `abom_section` (`plc_family_id`, `code`, `name`, `sort_order`)
SELECT @family_id, 'O1', 'PLC, I/O\'s, HMI, RTD, VFD', 1 WHERE @exists = 0
UNION ALL
SELECT @family_id, 'O2', 'Servo Amplifiers & Motors',  2 WHERE @exists = 0
UNION ALL
SELECT @family_id, 'O3', 'Servo Cables',               3 WHERE @exists = 0;

-- --- 3. a selection rule so the family is reachable ----------------
-- A family with no rule is invisible to the generator: detect_plc_family
-- walks abom_plc_rule and would never return OMRON, so an imported OMRON
-- build could never be selected and every BOM would quietly pick a
-- Mitsubishi build instead.
--
-- The rule is a TECHNICAL one, not a commercial guess: the iQ-R
-- R16MTCPU tops out at 16 axes, so above 16 there is no Mitsubishi build
-- in this system that can drive the machine, and the OMRON NJ501-1500
-- (64 axes) is the only one that can. Priority 6 puts it ahead of the
-- existing "10 or more axes -> iQ-R" rule at priority 10, and clear of
-- the SPM1250P rule already sitting at priority 5 -- the two do not
-- overlap today (9-12 axes vs 17+), but sharing a priority means the
-- order is decided by insertion id, which is not something to rely on.
--
-- NOT model-scoped, deliberately: it is true of any machine in this
-- system, not just the SPM1200L.
--
-- LIMITATION, WORTH KNOWING: this only auto-detects OMRON ABOVE 16 axes.
-- An OMRON build at a LOWER axis count is a commercial choice that no
-- axis/speed rule can infer -- for those, override the PLC family on the
-- generator (which is recorded and flagged on the sheet), or add a rule
-- scoped to that machine model.
INSERT INTO `abom_plc_rule`
  (`priority`, `machine_model`, `min_axes`, `max_axes`,
   `min_speed`, `max_speed`, `motion_type`, `result_family_id`,
   `explanation`, `is_active`)
SELECT 6, NULL, 17, NULL, NULL, NULL, 'ANY', @family_id,
       'Above 16 axes no Mitsubishi build in this system applies -- the iQ-R R16MTCPU ceiling is 16 -- so the OMRON NJ501-1500 is the controller.',
       1
 WHERE @exists = 0;

COMMIT;

-- --- what it did ----------------------------------------------------
SELECT @check AS `RESULT`;

SELECT f.`id`, f.`code`, f.`name`, f.`cpu_summary`
  FROM `abom_plc_family` f WHERE f.`code` = 'OMRON';

SELECT s.`id`, s.`code`, s.`name`
  FROM `abom_section` s
  JOIN `abom_plc_family` f ON f.`id` = s.`plc_family_id`
 WHERE f.`code` = 'OMRON' ORDER BY s.`sort_order`;

SELECT r.`priority`, r.`min_axes`, r.`result_family_id`, r.`explanation`
  FROM `abom_plc_rule` r
  JOIN `abom_plc_family` f ON f.`id` = r.`result_family_id`
 WHERE f.`code` = 'OMRON';

-- =====================================================================
--  ROLLBACK -- only safe while NOTHING has been imported against it.
--  Once a DF has been imported the family owns variants and items;
--  delete those first or the foreign keys will (correctly) refuse.
--
--  SET @fid = (SELECT id FROM abom_plc_family WHERE code = 'OMRON');
--  DELETE FROM `abom_plc_rule` WHERE `result_family_id` = @fid;
--  DELETE FROM `abom_section`  WHERE `plc_family_id`    = @fid;
--  DELETE FROM `abom_plc_family` WHERE `id` = @fid;
-- =====================================================================
