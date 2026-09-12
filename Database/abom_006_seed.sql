-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — SEED DATA (install 006)
--
--  SUPERSEDES Database/abom_002_seed.sql IN FULL. Do not run 002 after
--  this file; it would delete everything here and put the two-DF data
--  back. 002 is kept in the repository only so an existing install can
--  be traced back to what it was seeded with.
--
--  Run order on a fresh database:
--      abom_001.sql  ->  abom_005_variants.sql  ->  abom_006_seed.sql
--  On an install that already ran 001 + 002:
--      abom_005_variants.sql  ->  abom_006_seed.sql
--
--  WHAT CHANGED AND WHY
--  --------------------
--  The previous seed held 71 items extracted from TWO reference BOMs
--  (DF-1826 iQ-R, DF-1827 FX5), scoped to a PLC family. This seed holds
--  178 items extracted from NINE, scoped to a BUILD VARIANT:
--
--     DF-1723   SPM1200L    5 axis  12 track  100 PPM   FX5 / MR-JE
--     DF-1858   SPM1200L    5 axis   6 track   70 PPM   FX5 / MR-JE
--     DF-1808   SPM1200L    6 axis  12 track  100 PPM   FX5 / MR-JE
--     DF-1864   SPM1200L    6 axis  12 track   80 PPM   FX5 / MR-JE
--     DF-1778   SPM1200L    7 axis   8 track   80 PPM   FX5 / MR-JE
--     DF-1827   SPM1200L    8 axis  12 track  140 PPM   FX5 / MR-J4
--     DF-1770   SPM1200L   10 axis  12 track  140 PPM   iQ-R panel
--     DF-1826   SPM1200L   15 axis  12 track  180 PPM   iQ-R standalone
--     DF-1855   SPM1250P   15 axis   9 track   80 PPM   iQ-R flow meter
--
--  Five track counts (6, 8, 9, 12) and seven axis counts are now
--  represented, which is what makes the track-driven quantity rule below
--  worth having.
--
--  THE TEMPERATURE CARD RULE
--  -------------------------
--  ERP 2020122 (FX5-4LC, 4 CH. TEMPERATURE CARD) now carries formula
--  TRACK_TEMP, as specified by engineering:
--
--      qty = (((tracks + 1) * 2) + 2) / 4      rounded UP
--
--  Checked against every source BOM that lists a 4-channel temperature
--  or RTD card:
--
--      6 track   ->  4     DF-1858 lists 4    OK
--      8 track   ->  5     DF-1778 lists 5    OK
--      9 track   ->  6     DF-1855 lists 6    OK  (5.5 rounded up)
--     12 track   ->  7     DF-1723 lists 7    OK
--                          DF-1808 lists 7    OK
--                          DF-1827 lists 7    OK
--                          DF-1770 lists 7    OK
--                          DF-1864 lists 8    flagged, see item 4 note
--                          DF-1826 lists 8    flagged, see item 99 note
--
--  Eight of ten agree exactly; the two that do not are flagged for
--  review rather than quietly averaged away. Rounding is UP because half
--  a card cannot be purchased.
--
--  The same rule is applied to the other two 4-channel temperature cards
--  in the catalogue, because it is the same physical rule (one card per
--  four RTD channels) and the numbers above confirm it:
--     2240039  TM4-N2SB   AUTONICS   4 CH. temperature card  (FX5 alt.)
--     4010225  R60TCRT4   MITSUBISHI 4 CH. RTD input card    (iQ-R)
--  If engineering wants the rule confined to 2020122, change those two
--  rows back to formula_code 'MANUAL' — nothing else needs to change.
--
--  DELIBERATE DATA-QUALITY FLAGS
--  -----------------------------
--  The flagged issues below are REAL and must not be "tidied":
--  FOUR ERP codes are each on two different parts (severity 'conflict',
--  and what Abom_item_model::find_erp_conflicts() reports):
--    * 4060431  encoder cable MR-J3JCBL03M-A2-L  vs  power cable
--               MR-PWS2CBL03M-A2-L
--    * 2110163  brake connector set MR-BKCNS1 (DF-1826)  vs  brake cable
--               set SC-BKC1CBL1M-L (DF-1855)
--    * 2030449  10 MTR MR-J3ENCBL10M-A2-L on five sheets  vs  5 MTR
--               MR-J3ENCBL5M-A2-L on DF-1827 alone
--    * 2030505  MR-J3BUS3M (DF-1826)  vs  MR-J3BUS3M-A (DF-1855)
--
--  And, flagged for review rather than as conflicts because it is one
--  part with two codes rather than one code on two parts:
--    * MR-J4-200B carries ERP 4060510 on DF-1826/DF-1855 but 4060196 on
--      DF-1770
--    * MR-J3ENCBL5M-A2-L carries 4070181 on DF-1770
--    * R04ENCPU carries 4010223 on DF-1826 but 4140023 on DF-1855
--
--  9 items have no ERP code at all.
--  Engineering resolves these through the module UI, with an audit trail.
--
--  OUT OF SCOPE
--  ------------
--  Sheet2 of the DF-1864 workbook also carries a full electrical panel
--  BOM (energy meter, SMPS, MCBs, MPCBs, contactors, SSRs, sensors,
--  heaters, tower light). That is panel hardware, not automation, and
--  none of the module's five sections describes it. It is deliberately
--  NOT seeded here. The DF-1723 AUTOMATION portion of that same sheet
--  IS folded into the FX5-JE variant.
--
--  Deletes touch abom_* tables ONLY, children before parents, in one
--  transaction — same reasoning as abom_002_seed.sql: DELETE rather than
--  TRUNCATE so the script behaves identically under phpMyAdmin.
--
--  SAVED BOMs ARE NOT TOUCHED. abom_bom, abom_bom_line, abom_bom_revision
--  and abom_bom_approval are never read or written here. A BOM saved
--  before this re-seed still opens and still prints: every descriptive
--  field on abom_bom_line is a frozen snapshot, not a join back to
--  abom_item. Such a BOM shows an empty Build column, which is correct —
--  it was generated before builds existed.
--
--  abom_plc_family is upserted rather than deleted, because abom_bom
--  holds a foreign key onto it. See the note above that INSERT.
-- =====================================================================
SET NAMES utf8mb4;

START TRANSACTION;

DELETE FROM `abom_variant_rule`;  -- child of variant
DELETE FROM `abom_item`;          -- child of section, formula, plc_family
DELETE FROM `abom_variant`;       -- child of plc_family
DELETE FROM `abom_section`;       -- child of plc_family
DELETE FROM `abom_plc_rule`;      -- no foreign keys
DELETE FROM `abom_feature`;       -- no foreign keys
DELETE FROM `abom_formula`;       -- parent of abom_item

-- abom_plc_family is DELIBERATELY NOT DELETED. See the INSERT below.

-- --- PLC families ----------------------------------------------------
-- The default_* columns here are the fallback used when a BOM has no
-- variant. The live defaults come from abom_variant, which differs
-- between builds of the same family (DF-1770 is 9/9 in a panel with the
-- machine; DF-1826 is 11/12 standalone).
--
-- UPSERTED, NOT DELETED AND RE-INSERTED — and this is not a style
-- preference, it is the only thing that works.
--
-- `abom_bom`.`plc_family_id` is a foreign key onto this table
-- (fk_abom_bom_family, abom_001.sql), and the re-seed deliberately does
-- NOT delete saved BOMs. So on any site where a BOM has ever been saved,
-- `DELETE FROM abom_plc_family` fails with
--
--     #1451 Cannot delete or update a parent row: a foreign key
--           constraint fails (`abom_bom`, CONSTRAINT `fk_abom_bom_family`)
--
-- and takes the whole re-seed down with it. A clean database has no
-- abom_bom rows, so the delete succeeds there and the fault is invisible
-- until the script meets a real install.
--
-- Deleting them was pointless anyway: these two rows are byte-identical
-- to what abom_002_seed.sql wrote. The ON DUPLICATE KEY UPDATE is a
-- no-op that makes the INSERT safe to re-run; the two UPDATEs after it
-- then bring the rows to the values below whatever they held before, so
-- the file is still a true re-seed and not merely an insert-if-absent.
--
-- REPLACE INTO would NOT work here — it is a DELETE followed by an
-- INSERT and trips exactly the same constraint.
INSERT INTO `abom_plc_family`
(`id`,`code`,`name`,`cpu_summary`,`default_j4_units`,`default_battery`,`default_panel_location`,`sort_order`) VALUES
(1,'FX5','FX5 Series','FX5U-80MT/ESS + FX5-80SSC-S (8-axis simple motion)',7,7,'Panel With Machine',1),
(2,'iQ-R','iQ-R Series','R16MTCPU + R04ENCPU + R312B (16-axis motion controller)',11,12,'Standalone',2)
ON DUPLICATE KEY UPDATE `id` = `id`;

UPDATE `abom_plc_family` SET
  `code` = 'FX5', `name` = 'FX5 Series',
  `cpu_summary` = 'FX5U-80MT/ESS + FX5-80SSC-S (8-axis simple motion)',
  `default_j4_units` = 7, `default_battery` = 7,
  `default_panel_location` = 'Panel With Machine', `sort_order` = 1
 WHERE `id` = 1;

UPDATE `abom_plc_family` SET
  `code` = 'iQ-R', `name` = 'iQ-R Series',
  `cpu_summary` = 'R16MTCPU + R04ENCPU + R312B (16-axis motion controller)',
  `default_j4_units` = 11, `default_battery` = 12,
  `default_panel_location` = 'Standalone', `sort_order` = 2
 WHERE `id` = 2;

-- --- Quantity formulas ----------------------------------------------
INSERT INTO `abom_formula` (`code`,`name`,`description`,`needs_review`,`sort_order`) VALUES
('FIXED','Fixed quantity','Always the stored base quantity, independent of configuration.',0,1),
('AXES','Per axis','Quantity = total servo axis count.',0,2),
('AXES_MINUS_1','Axes minus one','Quantity = axes - 1. Inter-amplifier SSCNET daisy-chain links.',0,3),
('TRACKS','Per track','Quantity = track count. One unit per track (per-track cut-off drives).',0,4),
('TRACK_TEMP','Per track — temperature card','Quantity = (((tracks + 1) * 2) + 2) / 4, rounded up. One 4-channel temperature/RTD card per four thermocouple channels. 6T=4, 8T=5, 9T=6, 12T=7.',0,5),
('J4_STO','Per MR-J4 unit','Quantity = number of MR-J4 amplifier units. MR-JE amplifiers are EXCLUDED; one MR-J4W2 dual-axis amplifier counts as ONE unit.',0,6),
('BATTERY','Per servo battery','Quantity = servo battery count. Deliberately independent of J4_STO: DF-1826 shows STO=11 but Battery=12.',0,7),
('MANUAL','Manual review','Base quantity is the reference-BOM value. Engineer MUST confirm before approval.',1,8);

-- --- Optional feature gates -----------------------------------------
INSERT INTO `abom_feature` (`code`,`label`,`help_text`,`default_on`,`sort_order`) VALUES
('feat_perf','Perforation / Auxiliary 0.7 kW Axis','Adds the 0.7 kW amplifier, motor and their encoder/power cables. DF-1778 fits two (traverse + perforation).',1,1),
('feat_brake','Electromagnetic Brake','Adds the brake connector or cable set for the 7 kW up/down assembly (iQ-R).',1,2),
('feat_dbr','Dynamic Braking Resistor','Adds the RECKON DBR used for regeneration on the horizontal/vertical axes.',1,3),
('feat_imark','I-Mark Sensor','Adds the MR-CCN1 amplifier I/O connector (iQ-R).',1,4),
('feat_autonics_temp','Autonics Temperature Card','Substitutes the AUTONICS TM4-N2SB for the Mitsubishi FX5-4LC. DF-1858 is built this way; DF-1778 carries one alongside. Leave OFF unless the build sheet says otherwise.',0,5),
('feat_io32','Combined 16DI/16DO I/O Card','Adds the FX5-32ET/ESS in place of the FX5-16EX/ES. DF-1858 is built this way.',0,6);

-- --- Sections (five per family, in printed order) --------------------
INSERT INTO `abom_section` (`id`,`plc_family_id`,`code`,`name`,`sort_order`) VALUES
(1,1,'F1','PLC, I/O\'s, HMI, RTD, VFD',1),
(2,1,'F2','Servo Amplifiers & Motors',2),
(3,1,'F3','Servo Encoder Cables',3),
(4,1,'F4','Servo Power Connectors & Cables',4),
(5,1,'F5','SSCNET Cables & Servo Battery',5),
(6,2,'R1','PLC, I/O\'s, HMI, RTD, VFD',1),
(7,2,'R2','Servo Amplifiers & Motors',2),
(8,2,'R3','Servo Encoder Cables',3),
(9,2,'R4','Servo Power Connectors & Cables',4),
(10,2,'R5','SSCNET Cables & Servo Battery',5);

-- --- PLC auto-selection rules (first match by priority wins) ---------
-- Unchanged from 002 and still correct against all nine reference BOMs:
--   DF-1723/1778/1808/1827/1858/1864 -> FX5 ; DF-1770/1826/1855 -> iQ-R
INSERT INTO `abom_plc_rule`
(`id`,`priority`,`min_axes`,`max_axes`,`min_speed`,`max_speed`,`motion_type`,`result_family_id`,`explanation`,`is_active`) VALUES
(1,10,10,NULL,NULL,NULL,'ANY',2,'Axis count 10 or more exceeds the FX5-80SSC-S 8-axis simple motion limit.',1),
(2,20,NULL,NULL,160,NULL,'ANY',2,'Line speed 160 PPM or above requires the iQ-R motion controller scan rate.',1),
(3,30,12,NULL,NULL,NULL,'Continuous',2,'Continuous motion with 12 or more axes requires iQ-R synchronised control.',1),
(4,99,NULL,NULL,NULL,NULL,'ANY',1,'Default: FX5 Series covers up to 9 axes, up to 140 PPM, intermittent motion.',1);

-- --- Build variants --------------------------------------------------
INSERT INTO `abom_variant`
(`id`,`plc_family_id`,`code`,`name`,`description`,`machine_model`,`source_df`,`default_j4_units`,`default_battery`,`default_panel_location`,`is_active`,`sort_order`) VALUES
(1,1,'FX5-JE','FX5 + MR-JE (5-7 axis)','Standard SPM1200L build up to 7 axes and about 100 PPM. MR-JE-300B / MR-JE-200B amplifiers with HG-SN motors, 10 inch HMI, panel mounted with the machine.','SPM1200L','DF-1723, DF-1778, DF-1808, DF-1858, DF-1864',0,6,'Panel With Machine',1,1),
(2,1,'FX5-J4','FX5 + MR-J4 (8 axis)','Higher-torque SPM1200L build at 8 axes and 120-140 PPM. MR-J4-350B / MR-J4-200B amplifiers with HG-JR motors. Same FX5 CPU as FX5-JE but an entirely different servo range.','SPM1200L','DF-1827',7,7,'Panel With Machine',1,2),
(3,2,'IQR-STD','iQ-R standard (10-12 axis)','iQ-R motion controller in a panel with the machine. GT2510 10 inch HMI, mixed MR-J4 and dual-axis MR-J4W2 amplifiers.','SPM1200L','DF-1770',9,9,'Panel With Machine',1,3),
(4,2,'IQR-HS','iQ-R high speed continuous','Standalone iQ-R panel for 15 axis continuous running at 180 PPM. 15 inch HMI, 7 kW up/down assembly with electromagnetic brake, 20 metre encoder runs.','SPM1200L','DF-1826',11,12,'Standalone',1,4),
(5,2,'IQR-FLM','iQ-R flow meter (SPM1250P)','SPM1250P liquid filling with flow meter. Remote head module plus high-speed counter cards for flow meter pulses, and one 0.4 kW cut-off drive PER TRACK.','SPM1250P','DF-1855',15,15,'Standalone',1,5);

-- --- Variant selection rules (evaluated after the family, priority ASC)
--
-- EVERY rule names a machine model, including the two that would
-- otherwise be catch-alls. The nine reference BOMs describe the SPM1200L
-- and the SPM1250P and nothing else, so a third model must NOT quietly
-- inherit an SPM1200L build: it falls through to no match, the engine
-- generates nothing, and the generator says which configuration was
-- unmatched. Adding a model here is a deliberate act by engineering.
INSERT INTO `abom_variant_rule`
(`id`,`priority`,`plc_family_id`,`machine_model`,`min_axes`,`max_axes`,`min_speed`,`max_speed`,`motion_type`,`result_variant_id`,`explanation`,`is_active`) VALUES
(1,10,2,'SPM1250P',NULL,NULL,NULL,NULL,'ANY',5,'SPM1250P is the flow-meter filling machine — remote head, high-speed counters and per-track cut-off drives.',1),
(2,20,2,'SPM1200L',NULL,NULL,180,NULL,'ANY',4,'180 PPM or above is the high-speed standalone iQ-R build.',1),
(3,30,2,'SPM1200L',13,NULL,NULL,NULL,'ANY',4,'13 axes or more needs the standalone iQ-R panel and the 7 kW up/down assembly.',1),
(4,40,2,'SPM1200L',NULL,NULL,NULL,NULL,'ANY',3,'Default iQ-R build for the SPM1200L: motion controller in a panel with the machine.',1),
(5,50,1,'SPM1200L',8,NULL,NULL,NULL,'ANY',2,'8 axes on FX5 is the MR-J4 / HG-JR build (DF-1827).',1),
(6,60,1,'SPM1200L',NULL,NULL,120,NULL,'ANY',2,'120 PPM or above on FX5 needs the MR-J4 servo range.',1),
(7,70,1,'SPM1200L',NULL,NULL,NULL,NULL,'ANY',1,'Default FX5 build for the SPM1200L: MR-JE amplifiers with HG-SN motors.',1);

-- =====================================================================
--  MASTER ITEMS — 178 rows
--  Ordering within a variant is section sort_order then id, and THAT is
--  the printed BOM line order.
-- =====================================================================

-- ---------------------------------------------------------------------
-- VARIANT 1 — FX5-JE   (DF-1723, DF-1778, DF-1808, DF-1858, DF-1864)
-- ---------------------------------------------------------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(1,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(2,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,'Not listed on DF-1858 (5 axis / 6 track) — confirm for the smallest builds.','review','Panel With Machine','NOS','DF-1808',1),
(3,'2240027','16 SOURCE DI CARD','FX5-16EX/ES','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,'DF-1778 prints ERP 2240008 and DF-1827 prints none for this same card. 2240027 is used here (DF-1723/1808/1864) — verify before ordering.','review','Panel With Machine','NOS','DF-1808',1),
(4,'2240030','16 DI, 16 DO CARD','FX5-32ET/ESS','MITSUBISHI',1,'FIXED',1,1,1,1,'feat_io32','REPLACES THE FX5-16EX/ES','DF-1858 is built with this card instead of the FX5-16EX/ES. Turning this option on does NOT remove the 16EX line — deselect that item if it is a substitution.','review','Panel With Machine','NOS','DF-1858',1),
(5,'2020122','4 CH. TEMPERATURE CARD','FX5-4LC','MITSUBISHI',7,'TRACK_TEMP',1,1,1,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up. Matches DF-1723/1778/1808/1827 exactly; DF-1864 prints 8 for 12 tracks where the rule gives 7 — verify that BOM.','review','Panel With Machine','NOS','DF-1808',1),
(6,'2240039','4 CH. TEMPERATURE CARD','TM4-N2SB','AUTONICS',4,'TRACK_TEMP',1,1,1,1,'feat_autonics_temp','AUTONICS ALTERNATIVE TO THE FX5-4LC','DF-1858 uses this card exclusively (4 for 6 tracks). DF-1778 carries ONE alongside five FX5-4LC — a single extra unit, not a track-driven quantity.','review','Panel With Machine','NOS','DF-1858',1),
(7,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(8,'4100058','CONVERTER MODULE','FX5-CNV-BC','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(9,'2030464','65CM CABLE','FX5-65EC','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,'DF-1808 and DF-1864 print the part as FX5-65ECA under the same ERP — confirm which suffix is correct.','review','Panel With Machine','NOS','DF-1808',1),
(10,'4100057','POWER SUPPLY','FX5-1PSU-5V','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(11,'4060513','10 INCH HMI','GS2110-WTBD','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,'DF-1858 was built with a 12 inch GT2512-WXTBD instead, which has no ERP code on that sheet.','review','Panel With Machine','NOS','DF-1808',1),
(12,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',2,'MANUAL',1,1,1,0,NULL,'UNWIND + COLLATING CONV.','Varies by conveyor count: DF-1778/1808/1858 = 2, DF-1723 = 3, DF-1864 = 4 (adds case packer). DF-1808 notes a further unit for the pump.','review','Panel With Machine','NOS','DF-1808',1),
(13,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(14,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,1,1,0,NULL,'FOR EYE MARK',NULL,'none','Panel With Machine','NOS','DF-1808',1),
(15,'4040022','DYNAMIC BRAKING RESISTOR (DBR), 13Ω, 500W','—','RECKON',2,'MANUAL',1,1,1,1,'feat_dbr','FOR REGENERATION: HORZ. + VERT.',NULL,'none','Panel With Machine','NOS','DF-1808',1),
(16,'2050391','3KW SERVO AMPLIFIER','MR-JE-300B','MITSUBISHI',2,'MANUAL',1,1,2,0,NULL,'HORZ. + VERT.','DF-1864 fits 3 (adds filling), DF-1858 fits 1 (piston only).','review','Panel With Machine','NOS','DF-1808',1),
(17,'4060641','3KW SERVO MOTOR, 14.3Nm, 3000RPM, W/O KEYWAY','HG-SN302J','MITSUBISHI',2,'MANUAL',1,1,2,0,NULL,'HORZ. + VERT.','Must match the MR-JE-300B count above.','review','Panel With Machine','NOS','DF-1808',1),
(18,'4030012','2KW SERVO AMPLIFIER','MR-JE-200B','MITSUBISHI',3,'MANUAL',1,1,2,0,NULL,'FILLING PISTON + ROTARY VALVE + PULLING','DF-1864 fits 2, DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1808',1),
(19,'4060525','2KW SERVO MOTOR, 9.55Nm, 2000RPM, W/O KEYWAY','HG-SN202J','MITSUBISHI',1,'MANUAL',1,1,2,0,NULL,'FILLING PISTON','DF-1858 fits 3 (horz. + vert. + rotary valve). Not fitted on DF-1864.','review','Panel With Machine','NOS','DF-1808',1),
(20,'2050388','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, W/O KEYWAY','HG-SN152J','MITSUBISHI',1,'MANUAL',1,1,2,0,NULL,'ROTARY VALVE',NULL,'none','Panel With Machine','NOS','DF-1808',1),
(21,'4060620','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, WITH KEYWAY','HG-SN152JK','MITSUBISHI',1,'MANUAL',1,1,2,0,NULL,'PULLING','Not fitted on DF-1858.','review','Panel With Machine','NOS','DF-1808',1),
(22,'4120007','0.7KW SERVO AMPLIFIER','MR-JE-70B','MITSUBISHI',1,'MANUAL',1,1,2,1,'feat_perf','PERFORATION (+ TRAVERSE WHERE FITTED)','Qty = number of 0.7 kW auxiliary axes. DF-1778 fits 2 (traverse + perforation); DF-1808/1864 fit 1.','review','Panel With Machine','NOS','DF-1808',1),
(23,'4120009','0.7KW SERVO MOTOR, 2.4Nm, 3000RPM, WITH KEYWAY','HG-KN73JK','MITSUBISHI',1,'MANUAL',1,1,2,1,'feat_perf','PERFORATION (+ TRAVERSE WHERE FITTED)','Must match the MR-JE-70B count above.','review','Panel With Machine','NOS','DF-1808',1),
(24,'2030328','ENCODER CABLE HG-SN/JR/SR, 1KW–3KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',5,'MANUAL',1,1,3,0,NULL,'HORZ. + VERT. + PISTON + PULLING + ROTARY VALVE','All five reference BOMs list 5 — but the true driver is the count of 1KW–3KW motors, so re-check when the servo list changes.','review','Panel With Machine','NOS','DF-1808',1),
(25,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',1,'MANUAL',1,1,3,1,'feat_perf','PERFORATION','DF-1778 fits 2 (traverse + perforation).','review','Panel With Machine','NOS','DF-1808',1),
(26,'2110119','POWER CABLE CONNECTOR FOR 2KW–3KW MOTORS','MS3106F2222S+TB','MITSUBISHI',3,'MANUAL',1,1,4,0,NULL,'HORZ. + VERT. + PISTON','DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1808',1),
(27,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',2,'MANUAL',1,1,4,0,NULL,'PULLING + ROTARY VALVE','DF-1858 fits 1.','review','Panel With Machine','NOS','DF-1808',1),
(28,'4060594','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS1CBL03M-A2-L','MITSUBISHI',1,'MANUAL',1,1,4,1,'feat_perf','PERFORATION','DF-1778 instead lists ERP 4060431 / MR-PWS2CBL03M-A2-L, 2 off. Two different part numbers are in use for this cable — verify.','review','Panel With Machine','NOS','DF-1808',1),
(29,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',1,1,5,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1808',1),
(30,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',5,'AXES_MINUS_1',1,1,5,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Qty = axes − 1. Confirmed on all five: DF-1723/1858 = 4 at 5 axes, DF-1808/1864 = 5 at 6 axes, DF-1778 = 6 at 7 axes.','none','Panel With Machine','NOS','DF-1808',1),
(31,'2050390','BATTERY SET FOR MR-J4/JE SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',6,'BATTERY',1,1,5,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). Equals the axis count on all five reference BOMs, but is entered separately because it does not always.','review','Panel With Machine','NOS','DF-1808',1);

-- ---------------------------------------------------------------------
-- VARIANT 2 — FX5-J4   (DF-1827, 8 axis / 12 track / 140 PPM)
-- ---------------------------------------------------------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(32,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(33,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(34,NULL,'16 SOURCE DI CARD','FX5-16EX/ES','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,'NO ERP CODE on DF-1827. DF-1723/1808/1864 print 2240027 and DF-1778 prints 2240008 for the same card — raise a procurement request and settle the code.','no_erp','Panel With Machine','NOS','DF-1827',1),
(35,'2240029','8 SOURCE DI, 8 SOURCE DO CARD','FX5-16ET/ESS','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(36,'4100057','POWER SUPPLY','FX5-1PSU-5V','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(37,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(38,'2020122','4 CH. TEMPERATURE CARD','FX5-4LC','MITSUBISHI',7,'TRACK_TEMP',1,2,1,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up. DF-1827 lists 7 at 12 tracks, which the rule reproduces exactly.','none','Panel With Machine','NOS','DF-1827',1),
(39,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(40,'4100058','CONVERTER MODULE','FX5-CNV-BC','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(41,'2030464','65CM CABLE','FX5-65EC','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(42,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',2,'MANUAL',1,2,1,0,NULL,'UNWIND + COLLATING CONV.','Base qty 2 — review per machine configuration.','review','Panel With Machine','NOS','DF-1827',1),
(43,'4060513','10 INCH HMI','GS2110-WTBD','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(44,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,2,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(45,'2200006','SAFE TORQUE OFF CONNECTOR','MR-D05ULD3M-B','MITSUBISHI',7,'J4_STO',1,2,1,0,NULL,NULL,'Qty = MR-J4 amp units (excl. MR-JE). Use the \'MR-J4 Units\' control.','review','Panel With Machine','NOS','DF-1827',1),
(46,'2160574','SERVO AMPLIFIER 3.5KW WITH SSCNET','MR-J4-350B','MITSUBISHI',4,'MANUAL',1,2,2,0,NULL,NULL,'Base qty 4 — review per machine config.','review','Panel With Machine','NOS','DF-1827',1),
(47,'4060510','SERVO AMPLIFIER 2KW WITH SSCNET','MR-J4-200B','MITSUBISHI',3,'MANUAL',1,2,2,0,NULL,NULL,'DF-1770 prints ERP 4060196 for this same amplifier. Two ERP codes are in circulation for MR-J4-200B — settle it before raising a PO.','review','Panel With Machine','NOS','DF-1827',1),
(48,'4060400','SERVO MOTOR 10.5Nm, 3.5KW, 3000RPM, W/O KEYWAY','HG-JR353','MITSUBISHI',4,'MANUAL',1,2,2,0,NULL,'HORZ. + VERT. + FILLING PISTON (L) + FILLING PISTON (R)',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(49,'4060511','SERVO MOTOR 7.2Nm, 1.5KW, 2000RPM, WITH KEYWAY','HG-SR152K','MITSUBISHI',1,'MANUAL',1,2,2,0,NULL,'PULLING','DF-1827 shows this part as NEW with no ERP. The code used here (4060511) comes from DF-1770, which lists the same part — confirm it is the same item.','review','Panel With Machine','NOS','DF-1827',1),
(50,'4060491','SERVO MOTOR 6.4Nm, 2KW, 3000RPM, W/O KEYWAY','HG-JR203','MITSUBISHI',2,'MANUAL',1,2,2,0,NULL,'ROTARY VALVE',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(51,'4120007','0.7KW SERVO AMPLIFIER — PERFORATION AXIS','MR-JE-70B','MITSUBISHI',1,'FIXED',1,2,2,1,'feat_perf','PERFORATION',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(52,'4120009','0.7KW SERVO MOTOR, 2.4Nm, 3000RPM, WITH KEYWAY','HG-KN73JK','MITSUBISHI',1,'FIXED',1,2,2,1,'feat_perf','PERFORATION',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(53,'2030328','ENCODER CABLE HG-JR/SR, 1KW–3.5KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',7,'MANUAL',1,2,3,0,NULL,NULL,'Base qty 7 for an 8-axis machine — adjust per axis count and routing.','review','Panel With Machine','NOS','DF-1827',1),
(54,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 5 MTR','MR-J3ENCBL5M-A2-L','MITSUBISHI',1,'FIXED',1,2,3,1,'feat_perf','PERFORATION','⚠ ERP 2030449 CONFLICT — DF-1827 assigns it to this 5 MTR cable, but DF-1723/1778/1808/1826/1864 all assign it to the 10 MTR MR-J3ENCBL10M-A2-L, and DF-1770 gives this 5 MTR cable ERP 4070181. DF-1827 looks wrong — verify before ordering.','conflict','Panel With Machine','NOS','DF-1827',1),
(55,'2110119','POWER CABLE CONNECTOR FOR 2KW–5KW MOTORS','MS3106F2222S+TB','MITSUBISHI',4,'MANUAL',1,2,4,0,NULL,NULL,'Base qty 4 — adjust per count of 2KW–5KW motors.','review','Panel With Machine','NOS','DF-1827',1),
(56,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',3,'MANUAL',1,2,4,0,NULL,NULL,'Base qty 3 — adjust per count of 1KW–1.5KW motors.','review','Panel With Machine','NOS','DF-1827',1),
(57,'4060594','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS1CBL03M-A2-L','MITSUBISHI',1,'FIXED',1,2,4,1,'feat_perf','PERFORATION',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(58,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',7,'AXES_MINUS_1',1,2,5,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Qty = axes − 1. DF-1827 lists 7 at 8 axes.','none','Panel With Machine','NOS','DF-1827',1),
(59,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',1,2,5,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(60,'2050390','BATTERY SET FOR MR-J4 SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',7,'BATTERY',1,2,5,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). DF-1827 ref = 7.','review','Panel With Machine','NOS','DF-1827',1);

-- ---------------------------------------------------------------------
-- VARIANT 3 — IQR-STD   (DF-1770, 10 axis / 12 track / 120-140 PPM)
-- ---------------------------------------------------------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(61,'4010222','16 AXIS MOTION CONTROLLER','R16MTCPU','MITSUBISHI',1,'FIXED',2,3,6,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(62,'4010223','iQ-R PLC, 40K STEP','R04ENCPU','MITSUBISHI',1,'FIXED',2,3,6,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(63,'4100055','CPU POWER SUPPLY','R61P','MITSUBISHI',1,'FIXED',2,3,6,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(64,'4010224','12 SLOT CHASSIS','R312B','MITSUBISHI',1,'FIXED',2,3,6,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(65,'4010225','4 CH. RTD INPUT CARD','R60TCRT4','MITSUBISHI',7,'TRACK_TEMP',2,3,6,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up — the same 4-channel rule as ERP 2020122. DF-1770 lists 7 at 12 tracks, which the rule reproduces exactly.','none','Panel With Machine','NOS','DF-1770',1),
(66,'4010226','MODULAR IO HEADER MODULE','M-CCB-H','MITSUBISHI',1,'FIXED',2,3,6,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(67,'4010227','16 CH. DI CARD','M-16D','MITSUBISHI',5,'MANUAL',2,3,6,0,NULL,NULL,'Base qty 5 for 10 axis / 12 track — review per machine I/O count.','review','Panel With Machine','NOS','DF-1770',1),
(68,'4010228','16 CH. DO CARD','M-16TE','MITSUBISHI',4,'MANUAL',2,3,6,0,NULL,NULL,'Base qty 4 for 10 axis / 12 track — review per machine I/O count.','review','Panel With Machine','NOS','DF-1770',1),
(69,'4010229','2 CH. ANALOG OUTPUT CARD','M-DA2','MITSUBISHI',1,'MANUAL',2,3,6,0,NULL,NULL,'Base qty 1 — review per analog output requirements.','review','Panel With Machine','NOS','DF-1770',1),
(70,'4060518','4 CH. ANALOG INPUT CARD','M-AD4','MITSUBISHI',1,'FIXED',2,3,6,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(71,'4010230','BUS END','M-BE','MITSUBISHI',1,'FIXED',2,3,6,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(72,'4020083','GT25 HMI — 10 INCH','GT2510-WXTBD','MITSUBISHI',1,'FIXED',2,3,6,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(73,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',2,'MANUAL',2,3,6,0,NULL,NULL,'Base qty 2 — review per conveyor count.','review','Panel With Machine','NOS','DF-1770',1),
(74,'2110113','AMPLIFIER I/O CONNECTOR','MR-CCN1','MITSUBISHI',1,'FIXED',2,3,6,1,'feat_imark','FOR I-MARK SENSOR',NULL,'none','Panel With Machine','NOS','DF-1770',1),
(75,'2200006','SAFE TORQUE OFF CONNECTOR','MR-D05ULD3M-B','MITSUBISHI',9,'J4_STO',2,3,6,0,NULL,NULL,'Qty = MR-J4 amp units. Use the \'MR-J4 Units\' control.','review','Panel With Machine','NOS','DF-1770',1),
(76,'2160574','SERVO AMPLIFIER 3.5KW WITH SSCNET','MR-J4-350B','MITSUBISHI',4,'MANUAL',2,3,7,0,NULL,NULL,'Base qty 4 — review per config.','review','Panel With Machine','NOS','DF-1770',1),
(77,'4060196','SERVO AMPLIFIER 2KW WITH SSCNET','MR-J4-200B','MITSUBISHI',3,'MANUAL',2,3,7,0,NULL,NULL,'DF-1826 and DF-1855 print ERP 4060510 for this same amplifier. Two ERP codes are in circulation for MR-J4-200B — settle it before raising a PO.','review','Panel With Machine','NOS','DF-1770',1),
(78,'4060432','SERVO AMPLIFIER 0.75KW DUAL AXIS WITH SSCNET','MR-J4W2-77B','MITSUBISHI',2,'MANUAL',2,3,7,0,NULL,NULL,'Base qty 2 units = 4 axes (dual-axis amp). Review per config.','review','Panel With Machine','NOS','DF-1770',1),
(79,'4060400','SERVO MOTOR 10.5Nm, 3.5KW, 3000RPM, W/O KEYWAY','HG-JR353','MITSUBISHI',4,'MANUAL',2,3,7,0,NULL,'HORZ. + VERT. + FILLING PISTON (2 NOS.)',NULL,'none','Panel With Machine','NOS','DF-1770',1),
(80,'4060491','SERVO MOTOR 6.4Nm, 2KW, 3000RPM, W/O KEYWAY','HG-JR203','MITSUBISHI',2,'MANUAL',2,3,7,0,NULL,'ROTARY VALVE (2 NOS.)',NULL,'none','Panel With Machine','NOS','DF-1770',1),
(81,'4060511','SERVO MOTOR 7.2Nm, 1.5KW, 2000RPM, WITH KEYWAY','HG-SR152K','MITSUBISHI',1,'MANUAL',2,3,7,0,NULL,'PULLING',NULL,'none','Panel With Machine','NOS','DF-1770',1),
(82,'2050266','SERVO MOTOR 2.4Nm, 0.7KW, 3000RPM, W/O KEYWAY','HG-KR73','MITSUBISHI',3,'MANUAL',2,3,7,0,NULL,'CODING + SHUT OFF + PERFORATION','Review per config.','review','Panel With Machine','NOS','DF-1770',1),
(83,'2030328','ENCODER CABLE HG-SN/JR/SR, 1KW–3KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',6,'MANUAL',2,3,8,0,NULL,NULL,'Review per cable routing.','review','Panel With Machine','NOS','DF-1770',1),
(84,'2030373','ENCODER CABLE HG-SN/JR/SR, 1KW–3KW, 10 MTR','MR-J3ENSCBL10M-L','MITSUBISHI',1,'MANUAL',2,3,8,0,NULL,NULL,'Review per cable routing.','review','Panel With Machine','NOS','DF-1770',1),
(85,'4070181','ENCODER CABLE HG-KN/KR, 0.75KW, 5 MTR','MR-J3ENCBL5M-A2-L','MITSUBISHI',2,'MANUAL',2,3,8,0,NULL,NULL,'DF-1827 prints ERP 2030449 for this same cable. Two ERP codes are in circulation — verify.','review','Panel With Machine','NOS','DF-1770',1),
(86,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',1,'MANUAL',2,3,8,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1770',1),
(87,'2110119','POWER CABLE CONNECTOR FOR 2KW–3KW MOTORS','MS3106F2222S+TB','MITSUBISHI',4,'MANUAL',2,3,9,0,NULL,NULL,'Review per motor count.','review','Panel With Machine','NOS','DF-1770',1),
(88,'2050389','POWER CABLE CONNECTOR FOR 1KW–2KW MOTORS','MS3106F1810S+TB','MITSUBISHI',3,'MANUAL',2,3,9,0,NULL,NULL,'Review per motor count.','review','Panel With Machine','NOS','DF-1770',1),
(89,'4060431','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS2CBL03M-A2-L','MITSUBISHI',3,'MANUAL',2,3,9,0,NULL,NULL,'⚠ ERP 4060431 CONFLICT — the same code is on encoder cable MR-J3JCBL03M-A2-L (DF-1826). Data error — verify both before ordering.','conflict','Panel With Machine','NOS','DF-1770',1),
(90,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',6,'MANUAL',2,3,10,0,NULL,NULL,'DF-1770 lists 6 at 10 axes. The FX5 builds follow axes − 1; the iQ-R topology does not, so this stays manual.','review','Panel With Machine','NOS','DF-1770',1),
(91,'2030364','SSCNET CABLE, 1 METRE','MR-J3BUS1M','MITSUBISHI',1,'MANUAL',2,3,10,0,NULL,NULL,'Review per amplifier layout topology.','review','Panel With Machine','NOS','DF-1770',1),
(92,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',2,3,10,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1770',1),
(93,'2030505','SSCNET CABLE, 3 METRES','MR-J3BUS3M','MITSUBISHI',1,'MANUAL',2,3,10,0,NULL,NULL,'DF-1770 prints no ERP for this cable. 2030505 is taken from DF-1826, which lists the same part — confirm.','review','Panel With Machine','NOS','DF-1770',1),
(94,'2050390','BATTERY SET FOR MR-J4 SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',9,'BATTERY',2,3,10,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). DF-1770 ref = 9.','review','Panel With Machine','NOS','DF-1770',1);

-- ---------------------------------------------------------------------
-- VARIANT 4 — IQR-HS   (DF-1826 REV.02, 15 axis / 12 track / 180 PPM)
-- ---------------------------------------------------------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(95,'4010222','16 AXIS MOTION CONTROLLER','R16MTCPU','MITSUBISHI',1,'FIXED',2,4,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(96,'4010223','iQ-R PLC, 40K STEP','R04ENCPU','MITSUBISHI',1,'FIXED',2,4,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(97,'4100055','CPU POWER SUPPLY','R61P','MITSUBISHI',1,'FIXED',2,4,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(98,'4010224','12 SLOT CHASSIS','R312B','MITSUBISHI',1,'FIXED',2,4,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(99,'4010225','4 CH. RTD INPUT CARD','R60TCRT4','MITSUBISHI',8,'TRACK_TEMP',2,4,6,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up — the same 4-channel rule as ERP 2020122. DF-1826 prints 8 at 12 tracks where the rule gives 7; DF-1770 prints 7 at 12 tracks. Verify this sheet.','review','Standalone','NOS','DF-1826',1),
(100,'4010226','MODULAR IO HEADER MODULE','M-CCB-H','MITSUBISHI',1,'FIXED',2,4,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(101,'4010227','16 CH. DI CARD','M-16D','MITSUBISHI',5,'MANUAL',2,4,6,0,NULL,NULL,'Base qty 5 for 15 axis / 12 track — review per machine I/O count.','review','Standalone','NOS','DF-1826',1),
(102,'4010228','16 CH. DO CARD','M-16TE','MITSUBISHI',3,'MANUAL',2,4,6,0,NULL,NULL,'Base qty 3 for 15 axis / 12 track — review per machine I/O count.','review','Standalone','NOS','DF-1826',1),
(103,'4010229','2 CH. ANALOG OUTPUT CARD','M-DA2','MITSUBISHI',2,'MANUAL',2,4,6,0,NULL,NULL,'Base qty 2 — review per analog output requirements.','review','Standalone','NOS','DF-1826',1),
(104,'4060518','4 CH. ANALOG INPUT CARD','M-AD4','MITSUBISHI',1,'FIXED',2,4,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(105,'4010230','BUS END','M-BE','MITSUBISHI',1,'FIXED',2,4,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(106,NULL,'HMI 15 INCH','GT3715-FHCBD','MITSUBISHI',1,'FIXED',2,4,6,0,NULL,NULL,'NEW PART — NO ERP CODE. Raise new item creation request.','no_erp','Standalone','NOS','DF-1826',1),
(107,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',4,'MANUAL',2,4,6,0,NULL,'COLLATING (1st STAGE) + CASE PACKER CONV. + TRANSFER CONV. + UNWIND','Base qty 4 — review per machine configuration.','review','Standalone','NOS','DF-1826',1),
(108,'2110113','AMPLIFIER I/O CONNECTOR','MR-CCN1','MITSUBISHI',1,'FIXED',2,4,6,1,'feat_imark','FOR I-MARK SENSOR',NULL,'none','Standalone','NOS','DF-1826',1),
(109,'2200006','SAFE TORQUE OFF CONNECTOR','MR-D05ULD3M-B','MITSUBISHI',11,'J4_STO',2,4,6,0,NULL,NULL,'Qty = MR-J4 amp units. Use the \'MR-J4 Units\' control.','review','Standalone','NOS','DF-1826',1),
(110,'4060660','SERVO AMPLIFIER 7KW WITH SSCNET & ELECTROMAGNETIC BRAKE','MR-J4-700B','MITSUBISHI',1,'FIXED',2,4,7,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1826',1),
(111,'4060583','SERVO AMPLIFIER 5KW WITH SSCNET','MR-J4-500B','MITSUBISHI',2,'FIXED',2,4,7,0,NULL,'HORZ. + VERT.',NULL,'none','Standalone','NOS','DF-1826',1),
(112,'2160574','SERVO AMPLIFIER 3.5KW WITH SSCNET','MR-J4-350B','MITSUBISHI',2,'MANUAL',2,4,7,0,NULL,NULL,'Base qty 2 for Filling Pistons — review per config.','review','Standalone','NOS','DF-1826',1),
(113,'4060510','SERVO AMPLIFIER 2KW WITH SSCNET','MR-J4-200B','MITSUBISHI',3,'MANUAL',2,4,7,0,NULL,NULL,'DF-1770 prints ERP 4060196 for this same amplifier — verify.','review','Standalone','NOS','DF-1826',1),
(114,'4060432','SERVO AMPLIFIER 0.75KW DUAL AXIS WITH SSCNET','MR-J4W2-77B','MITSUBISHI',3,'MANUAL',2,4,7,0,NULL,NULL,'Base qty 3 units = 6 axes (dual-axis amp). Review per config.','review','Standalone','NOS','DF-1826',1),
(115,'4060661','SERVO MOTOR 33.4Nm, 7KW, 2000RPM','HG-SR702B','MITSUBISHI',1,'FIXED',2,4,7,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1826',1),
(116,'4060662','SERVO MOTOR 15.9Nm, 5KW, 3000RPM','HG-JR503','MITSUBISHI',2,'FIXED',2,4,7,0,NULL,'HORZ. + VERT.',NULL,'none','Standalone','NOS','DF-1826',1),
(117,'4060400','SERVO MOTOR 10.5Nm, 3.5KW, 3000RPM, W/O KEYWAY','HG-JR353','MITSUBISHI',2,'MANUAL',2,4,7,0,NULL,'FILLING PISTON (2 NOS.)','Review per config.','review','Standalone','NOS','DF-1826',1),
(118,'4060491','SERVO MOTOR 6.4Nm, 2KW, 3000RPM, W/O KEYWAY','HG-JR203','MITSUBISHI',1,'FIXED',2,4,7,0,NULL,'ROTARY VALVE',NULL,'none','Standalone','NOS','DF-1826',1),
(119,'4060663','SERVO MOTOR 9.55Nm, 2KW, 2000RPM','HG-SR202','MITSUBISHI',1,'FIXED',2,4,7,0,NULL,'PULLING',NULL,'none','Standalone','NOS','DF-1826',1),
(120,NULL,'SERVO MOTOR 4.8Nm, 1.5KW, 3000RPM','HG-JR153','MITSUBISHI',1,'FIXED',2,4,7,0,NULL,'COLLATING (2nd STAGE)','NO ERP CODE — raise procurement request.','no_erp','Standalone','NOS','DF-1826',1),
(121,'2050266','SERVO MOTOR 2.4Nm, 0.7KW, 3000RPM, W/O KEYWAY','HG-KR73','MITSUBISHI',6,'MANUAL',2,4,7,0,NULL,'CODING + PERFORATION + CASE PACKER FLAPPER (3 NOS.) + SHUT OFF NOZZLE','Review per config.','review','Standalone','NOS','DF-1826',1),
(122,'2030373','ENCODER CABLE HG-JR/SR, 1KW–7KW, 10 MTR','MR-J3ENSCBL10M-L','MITSUBISHI',4,'MANUAL',2,4,8,0,NULL,'UP/DWN ASSEMBLY + FILLING PISTON (2 NOS.) + ROTARY VALVE','Review per cable routing.','review','Standalone','NOS','DF-1826',1),
(123,NULL,'ENCODER CABLE HG-JR/SR, 1KW–7KW, 20 MTR','MR-J3ENSCBL20M-L','MITSUBISHI',4,'MANUAL',2,4,8,0,NULL,'COLLATING (2nd STAGE) + HORZ. + VERT. + PULLING','NO ERP CODE — raise procurement request.','no_erp','Standalone','NOS','DF-1826',1),
(124,'4060431','ENCODER CABLE HG-KN/KR, 0.75KW, 0.3 MTR','MR-J3JCBL03M-A2-L','MITSUBISHI',5,'MANUAL',2,4,8,0,NULL,'CASE PACKER FLAPPER (3 NOS.) + PERFORATION + SHUT OFF NOZZLE','⚠ ERP 4060431 CONFLICT — also used for power cable MR-PWS2CBL03M-A2-L. Data error — verify both ERPs before ordering.','conflict','Standalone','NOS','DF-1826',1),
(125,'2030772','ENCODER CABLE HG-KN/KR, 0.75KW, 20 MTR','MR-EKCBL20M-L','MITSUBISHI',5,'MANUAL',2,4,8,0,NULL,NULL,'Review per cable routing.','review','Standalone','NOS','DF-1826',1),
(126,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',1,'FIXED',2,4,8,0,NULL,'CODING',NULL,'none','Standalone','NOS','DF-1826',1),
(127,'2110162','POWER CONNECTOR FOR 7KW SERVO MOTOR','MS3106F3217S+TB','MITSUBISHI',1,'FIXED',2,4,9,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1826',1),
(128,'2110163','ELECTROMAGNETIC BRAKE CONNECTOR SET','MR-BKCNS1','MITSUBISHI',1,'FIXED',2,4,9,1,'feat_brake',NULL,'⚠ ERP 2110163 CONFLICT — DF-1855 prints the same code against brake CABLE set SC-BKC1CBL1M-L, which is a different part. Verify before ordering.','conflict','Standalone','NOS','DF-1826',1),
(129,'2110119','POWER CABLE CONNECTOR FOR 2KW–5KW MOTORS','MS3106F2222S+TB','MITSUBISHI',6,'MANUAL',2,4,9,0,NULL,'PULLING + ROTARY VALVE + FILLING PISTON (2 NOS.) + HORZ. + VERT.','Review per motor count.','review','Standalone','NOS','DF-1826',1),
(130,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',1,'MANUAL',2,4,9,0,NULL,'COLLATING (2nd STAGE)','Review per motor count.','review','Standalone','NOS','DF-1826',1),
(131,'4060431','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS2CBL03M-A2-L','MITSUBISHI',6,'MANUAL',2,4,9,0,NULL,'CODING + PERFORATION + CASE PACKER FLAPPER (3 NOS.) + SHUT OFF NOZZLE','⚠ ERP 4060431 CONFLICT — also used for encoder cable MR-J3JCBL03M-A2-L. Data error — verify both ERPs before ordering.','conflict','Standalone','NOS','DF-1826',1),
(132,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',2,4,10,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Standalone','NOS','DF-1826',1),
(133,'2030505','SSCNET CABLE, 3 METRES','MR-J3BUS3M','MITSUBISHI',2,'FIXED',2,4,10,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(134,'2030364','SSCNET CABLE, 1 METRE','MR-J3BUS1M','MITSUBISHI',10,'MANUAL',2,4,10,0,NULL,NULL,'Base qty 10 for a 15-axis machine — review per amplifier layout topology.','review','Standalone','NOS','DF-1826',1),
(135,'2050390','BATTERY SET FOR MR-J4 SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',12,'BATTERY',2,4,10,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). ⚠ DF-1826 shows 12, J4 unit count = 11 — verify with engineering.','review','Standalone','NOS','DF-1826',1),
(136,'2050599','DYNAMIC BRAKING RESISTOR (DBR), 6.7Ω, 500W','—','RECKON',3,'MANUAL',2,4,10,1,'feat_dbr','FOR REGENERATION: UP/DWN ASSEMBLY + HORZ. + VERT.',NULL,'none','Standalone','NOS','DF-1826',1);

-- ---------------------------------------------------------------------
-- VARIANT 5 — IQR-FLM   (DF-1855, SPM1250P, 15 axis / 9 track / 80 PPM)
-- ---------------------------------------------------------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(137,'4140023','PROGRAMMABLE CONTROLLER CPU MODULE, CC-LINK IE FIELD BASIC, 2 PORTS','R04ENCPU','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,'DF-1826 prints ERP 4010223 for R04ENCPU. Two ERP codes are in circulation for this CPU — verify which applies to the CC-Link IE Field Basic variant.','review','Standalone','NOS','DF-1855',1),
(138,'4010224','MAIN BASE UNIT, 12 SLOT','R312B','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1855',1),
(139,NULL,'8 SLOT BASE RACK','R38B','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,'NEW PART — NO ERP CODE. Raise new item creation request.','no_erp','Standalone','NOS','DF-1855',1),
(140,NULL,'iQ-R REMOTE HEAD MODULE','RJ72GF15-T2','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,'NEW PART — NO ERP CODE. Raise new item creation request.','no_erp','Standalone','NOS','DF-1855',1),
(141,'4100055','POWER SUPPLY MODULE','R61P','MITSUBISHI',2,'FIXED',2,5,6,0,NULL,'ONE PER BASE (MAIN + REMOTE HEAD)',NULL,'none','Standalone','NOS','DF-1855',1),
(142,NULL,'2 CH. HIGH SPEED COUNTER MODULE','R62P2','MITSUBISHI',5,'MANUAL',2,5,6,0,NULL,'FOR FLOW METER PULSE','NEW PART — NO ERP CODE. Qty 5 covers 9 flow meters at 2 channels each — review against the actual meter count.','no_erp','Standalone','NOS','DF-1855',1),
(143,'4010230','BUS END','M-BE','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1855',1),
(144,'4010222','16 AXIS MOTION MODULE, CC-LINK IE FIELD BASIC','R16MTCPU','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1855',1),
(145,'4010226','CC-LINK IO HEADER MODULE','M-CCB-H','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1855',1),
(146,'4010227','16 CH. DI CARD','M-16D','MITSUBISHI',4,'MANUAL',2,5,6,0,NULL,NULL,'Base qty 4 — review per machine I/O count.','review','Standalone','NOS','DF-1855',1),
(147,'4010228','16 CH. DO CARD','M-16TE','MITSUBISHI',4,'MANUAL',2,5,6,0,NULL,NULL,'Base qty 4 — review per machine I/O count.','review','Standalone','NOS','DF-1855',1),
(148,'4060518','4 CH. ANALOG INPUT MODULE','M-AD4','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1855',1),
(149,'4010225','TEMPERATURE CONTROL MODULE, 4 CH. RTD','R60TCRT4','MITSUBISHI',6,'TRACK_TEMP',2,5,6,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up — the same 4-channel rule as ERP 2020122. DF-1855 lists 6 at 9 tracks; the rule gives 5.5 rounded up to 6.','none','Standalone','NOS','DF-1855',1),
(150,'4020090','GT25 HMI — 12 INCH','GT2512-WXTBD','MITSUBISHI',1,'FIXED',2,5,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1855',1),
(151,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',3,'MANUAL',2,5,6,0,NULL,'UNWIND + COLLATING CONV.','Base qty 3 — review per conveyor count.','review','Standalone','NOS','DF-1855',1),
(152,'4060586','VFD — 3HP','FR-D740-050-E16','MITSUBISHI',3,'MANUAL',2,5,6,0,NULL,'TRANSFER PUMP','Base qty 3 — review per pump count.','review','Standalone','NOS','DF-1855',1),
(153,'4010229','2 CH. ANALOG OUTPUT MODULE','M-DA2','MITSUBISHI',3,'MANUAL',2,5,6,0,NULL,'FOR VFD SET POINTS','Base qty 3 — one channel per VFD set point.','review','Standalone','NOS','DF-1855',1),
(154,'2200006','SAFE TORQUE OFF CONNECTOR','MR-D05ULD3M-B','MITSUBISHI',15,'J4_STO',2,5,6,0,NULL,'FOR SAFETY CIRCUIT','Qty = MR-J4 amp units. DF-1855 prints the part as MR-D05UDL3M-B; every other sheet prints MR-D05ULD3M-B — treated as a typo on that sheet.','review','Standalone','NOS','DF-1855',1),
(155,'2110113','AMPLIFIER I/O CONNECTOR','MR-CCN1','MITSUBISHI',1,'FIXED',2,5,6,1,'feat_imark','FOR I-MARK SENSOR',NULL,'none','Standalone','NOS','DF-1855',1),
(156,'4060660','SERVO AMPLIFIER 7KW WITH SSCNET & ELECTROMAGNETIC BRAKE','MR-J4-700B','MITSUBISHI',1,'FIXED',2,5,7,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1855',1),
(157,'4060661','SERVO MOTOR 33.4Nm, 7KW, 2000RPM','HG-SR702B','MITSUBISHI',1,'FIXED',2,5,7,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1855',1),
(158,'2160574','SERVO AMPLIFIER 3.5KW WITH SSCNET','MR-J4-350B','MITSUBISHI',2,'MANUAL',2,5,7,0,NULL,'HORIZONTAL + VERTICAL','DF-1855 shows this part as NEW with no ERP. 2160574 is taken from DF-1826/DF-1827, which list the same amplifier — confirm.','review','Standalone','NOS','DF-1855',1),
(159,'4060400','SERVO MOTOR 10.5Nm, 3.5KW, 3000RPM, W/O KEYWAY','HG-JR353','MITSUBISHI',2,'MANUAL',2,5,7,0,NULL,'HORIZONTAL + VERTICAL','DF-1855 prints no ERP and describes this motor as 15.9Nm; every other sheet gives HG-JR353 as 10.5Nm. Torque figure and ERP both need confirming.','review','Standalone','NOS','DF-1855',1),
(160,'4060510','SERVO AMPLIFIER 2KW WITH SSCNET','MR-J4-200B','MITSUBISHI',2,'MANUAL',2,5,7,0,NULL,'PULLING (KEYWAY FROM MARKET)','DF-1770 prints ERP 4060196 for this same amplifier — verify.','review','Standalone','NOS','DF-1855',1),
(161,'4060663','SERVO MOTOR 9.55Nm, 2KW, 2000RPM','HG-SR202','MITSUBISHI',1,'FIXED',2,5,7,0,NULL,'PULLING',NULL,'none','Standalone','NOS','DF-1855',1),
(162,'4060438','SERVO MOTOR 7.16Nm, 1.5KW, 2000RPM','HG-SR152','MITSUBISHI',1,'FIXED',2,5,7,0,NULL,'PLAIN CUT (ROTARY CUTTER)',NULL,'none','Standalone','NOS','DF-1855',1),
(163,'4060432','SERVO AMPLIFIER 0.75KW DUAL AXIS WITH SSCNET','MR-J4W2-77B','MITSUBISHI',1,'MANUAL',2,5,7,0,NULL,'CODING','Base qty 1 unit = 2 axes (dual-axis amp).','review','Standalone','NOS','DF-1855',1),
(164,'2050266','SERVO MOTOR 2.4Nm, 0.7KW, 3000RPM, W/O KEYWAY','HG-KR73','MITSUBISHI',1,'MANUAL',2,5,7,0,NULL,'CODING',NULL,'none','Standalone','NOS','DF-1855',1),
(165,NULL,'SERVO AMPLIFIER 0.4KW WITH SSCNET','MR-J4-40B','MITSUBISHI',9,'TRACKS',2,5,7,0,NULL,'CUT OFF — ONE PER TRACK','NEW PART — NO ERP CODE. Qty = track count: DF-1855 lists 9 for 9 tracks.','no_erp','Standalone','NOS','DF-1855',1),
(166,NULL,'SERVO MOTOR 1.3Nm, 0.4KW, 3000RPM','HG-KR43','MITSUBISHI',9,'TRACKS',2,5,7,0,NULL,'CUT OFF — ONE PER TRACK','NEW PART — NO ERP CODE. Qty = track count: DF-1855 lists 9 for 9 tracks.','no_erp','Standalone','NOS','DF-1855',1),
(167,'2030373','ENCODER CABLE HG-JR/SR, 1KW–7KW, 10 MTR','MR-J3ENSCBL10M-L','MITSUBISHI',5,'MANUAL',2,5,8,0,NULL,'UP/DWN ASSEMBLY + HORZ. + VERT. + PULLING + PLAIN CUT','Review per cable routing.','review','Standalone','NOS','DF-1855',1),
(168,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',10,'MANUAL',2,5,8,0,NULL,'CUT OFF (9 NOS.) + CODING','DF-1855 shows this cable as NEW with no ERP. 2030449 is taken from DF-1826, which lists the same cable — confirm. Qty tracks the per-track cut-off drives plus coding.','review','Standalone','NOS','DF-1855',1),
(169,'4060431','POWER CABLE 0.3 MTR FOR 0.1KW–0.75KW MOTORS','MR-PWS2CBL03M-A2-L','MITSUBISHI',10,'MANUAL',2,5,9,0,NULL,'CUT OFF (9 NOS.) + CODING','⚠ ERP 4060431 CONFLICT — the same code is on encoder cable MR-J3JCBL03M-A2-L (DF-1826). Data error — verify both before ordering.','conflict','Standalone','NOS','DF-1855',1),
(170,'2110119','POWER CABLE CONNECTOR FOR 2KW–5KW MOTORS','MS3106F2222S+TB','MITSUBISHI',3,'MANUAL',2,5,9,0,NULL,'HORZ. + VERT. + PULLING','Review per motor count.','review','Standalone','NOS','DF-1855',1),
(171,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',1,'MANUAL',2,5,9,0,NULL,'ROTARY CUTTER','Review per motor count.','review','Standalone','NOS','DF-1855',1),
(172,'2110162','POWER CONNECTOR FOR 7KW SERVO MOTOR','MS3106F3217S+TB','MITSUBISHI',1,'FIXED',2,5,9,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1855',1),
(173,'2110163','ELECTROMAGNETIC BRAKE CABLE SET','SC-BKC1CBL1M-L','MITSUBISHI',1,'FIXED',2,5,9,1,'feat_brake',NULL,'⚠ ERP 2110163 CONFLICT — DF-1826 prints the same code against brake CONNECTOR set MR-BKCNS1, which is a different part. Verify before ordering.','conflict','Standalone','NOS','DF-1855',1),
(174,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',2,5,10,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Standalone','NOS','DF-1855',1),
(175,'2030505','SSCNET CABLE, 3 METRES','MR-J3BUS3M-A','MITSUBISHI',3,'MANUAL',2,5,10,0,NULL,NULL,'⚠ ERP 2030505 CONFLICT — DF-1826 prints MR-J3BUS3M and DF-1855 prints MR-J3BUS3M-A under this one code. The -A suffix is a different Mitsubishi part number, not a typo — settle which is being bought.','conflict','Standalone','NOS','DF-1855',1),
(176,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',10,'MANUAL',2,5,10,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Base qty 10 — review per amplifier layout topology.','review','Standalone','NOS','DF-1855',1),
(177,'2050390','BATTERY SET FOR MR-J4 SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',15,'BATTERY',2,5,10,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). DF-1855 ref = 15, one per axis.','review','Standalone','NOS','DF-1855',1),
(178,'2050599','DYNAMIC BRAKING RESISTOR (DBR), 6.7Ω, 500W','—','RECKON',3,'MANUAL',2,5,10,1,'feat_dbr','FOR REGENERATION',NULL,'none','Standalone','NOS','DF-1855',1);

COMMIT;

-- =====================================================================
--  POST-INSTALL VERIFICATION — run these and compare
--
--   SELECT COUNT(*) FROM abom_item;                       -- 178
--   SELECT variant_id, COUNT(*) FROM abom_item
--     GROUP BY variant_id;                                -- 31/29/34/42/42
--   SELECT COUNT(*) FROM abom_variant;                    -- 5
--   SELECT COUNT(*) FROM abom_variant_rule;               -- 7
--   SELECT COUNT(*) FROM abom_item WHERE erp_code IS NULL;-- 9
--   SELECT erp_code, COUNT(DISTINCT part_no) FROM abom_item
--     WHERE erp_code IS NOT NULL GROUP BY erp_code
--     HAVING COUNT(DISTINCT part_no) > 1;
--                     -- exactly 4: 2030449, 2030505, 2110163, 4060431
--   SELECT COUNT(*) FROM abom_item
--     WHERE issue_severity = 'conflict';                  -- 8
--
--  And the temperature-card rule, straight off the generator screen:
--   6 tracks -> 4    8 tracks -> 5    9 tracks -> 6    12 tracks -> 7
-- =====================================================================
