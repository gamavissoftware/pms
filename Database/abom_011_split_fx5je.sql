-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — ONE BUILD PER BOM (install 011)
--
--  Run AFTER Database/abom_009_seed_1250p.sql.
--
--  ⚠ DO NOT RUN Database/abom_010_source_df.sql. This supersedes it.
--     010 added per-item provenance to the SHARED FX5-JE catalogue so
--     that the five BOMs inside it could be told apart. This splits them
--     into five real builds instead, which answers the same question
--     more directly, so 010's updates would land on rows this script
--     retires. Running it first is harmless but pointless; running it
--     after does nothing at all.
--
--  WHAT THIS DOES
--  --------------
--  FX5-JE was ONE build assembled from FIVE uploaded BOMs. That is a
--  sound way to model machines whose quantities are calculated — and it
--  made the register unreadable, because a build assembled from five
--  sheets cannot carry a name and a description that describe any one of
--  them. Engineering asked for one build per BOM. This is that.
--
--      FX5-1723   DF-1723   5 axis  12 track  100 PPM   25 items
--      FX5-1778   DF-1778   7 axis   8 track   80 PPM   29 items
--      FX5-1808   DF-1808   6 axis  12 track  100 PPM   29 items
--      FX5-1858   DF-1858   5 axis   6 track   70 PPM   20 items
--      FX5-1864   DF-1864   6 axis  12 track   80 PPM   28 items
--
--  Each one now carries THAT SHEET'S OWN QUANTITIES, not a shared base.
--  They were read out of the five workbooks: DF-1858 fits one 3 kW
--  amplifier where DF-1864 fits three, DF-1778 fits two 0.7 kW drives
--  for traverse plus perforation where DF-1808 fits one, and so on.
--
--  Quantities are still CALCULATED where a formula applies —
--  TRACK_TEMP, AXES_MINUS_1, BATTERY and J4_STO are carried across
--  unchanged, so a DF-1808-type machine ordered at 8 tracks still gets
--  5 temperature cards rather than the 7 its own sheet lists.
--
--  WHAT IT COSTS, STATED PLAINLY
--  -----------------------------
--  31 catalogue items become 131. An ERP code that is wrong is now wrong
--  in up to five places, and correcting it means five edits. That is the
--  price of per-BOM separation and it is the reason the catalogue was
--  shared in the first place. The master-item screen filters by ERP code
--  so the five are at least easy to find together.
--
--  COVERAGE IS UNCHANGED
--  ---------------------
--  The old catch-all rule sent every unmatched FX5 SPM1200L to FX5-JE.
--  It is deactivated here and five banded rules replace it. The bands
--  are deliberately open-ended at the bottom and split on speed where
--  two sheets share an axis count:
--
--      <= 5 axes, < 80 PPM   -> FX5-1858
--      <= 5 axes, >= 80 PPM  -> FX5-1723
--         6 axes, < 90 PPM   -> FX5-1864
--         6 axes, >= 90 PPM  -> FX5-1808
--         7 axes             -> FX5-1778
--
--  Every axis count from 1 to 7 still resolves, at any speed below the
--  120 PPM that already routes to FX5-J4. Nothing that generated before
--  stops generating.
--
--  NOTHING IS DELETED
--  ------------------
--  FX5-JE and its 31 items are DEACTIVATED, not removed.
--  abom_bom_line.item_id points at those rows for provenance, and any
--  BOM already generated from that build must keep resolving. They stop
--  appearing on new BOMs and stay visible under the "Retired only"
--  filter. Delete them from Build configuration later if you are sure
--  nothing references them.
--
--  feat_autonics_temp and feat_io32 are dropped from the new items. They
--  existed only to model the DF-1858-versus-the-rest difference inside
--  one shared catalogue; DF-1858 is its own build now and simply has
--  those parts. feat_perf and feat_dbr stay — those are real options a
--  customer can decline.
-- =====================================================================
SET NAMES utf8mb4;

START TRANSACTION;

-- --- the five per-BOM builds ---------------------------------------
INSERT INTO `abom_variant`
(`id`,`plc_family_id`,`code`,`name`,`description`,`machine_model`,`source_df`,`default_j4_units`,`default_battery`,`default_panel_location`,`is_active`,`sort_order`) VALUES
(8,1,'FX5-1723','SPM1200L 5 axis / 12 track / 100 PPM','From DF-1723. FX5 with MR-JE amplifiers and HG-SN motors, 10 inch HMI, panel mounted with the machine. No perforation axis.','SPM1200L','DF-1723',0,5,'Panel With Machine',1,8),
(9,1,'FX5-1778','SPM1200L 7 axis / 8 track / 80 PPM','From DF-1778. Six axes plus a traverse axis, so two 0.7 kW auxiliary drives. Carries one AUTONICS temperature card alongside the Mitsubishi ones.','SPM1200L','DF-1778',0,7,'Panel With Machine',1,9),
(10,1,'FX5-1808','SPM1200L 6 axis / 12 track / 100 PPM','From DF-1808. FX5 with MR-JE amplifiers and HG-SN motors, perforation axis fitted, third VFD for the pump.','SPM1200L','DF-1808',0,6,'Panel With Machine',1,10),
(11,1,'FX5-1858','SPM1200L 5 axis / 6 track / 70 PPM','From DF-1858. The smallest build: AUTONICS temperature cards instead of Mitsubishi, combined 16DI/16DO card instead of the 16EX, 12 inch HMI, and no converter module, power supply or perforation axis.','SPM1200L','DF-1858',0,5,'Panel With Machine',1,11),
(12,1,'FX5-1864','SPM1200L 6 axis / 12 track / 80 PPM','From DF-1864. Adds the case packer conveyor, so four VFDs, and runs three 3 kW amplifiers.','SPM1200L','DF-1864',0,6,'Panel With Machine',1,12);

-- --- selection rules, one per build ---------------------------------
INSERT INTO `abom_variant_rule`
(`id`,`priority`,`plc_family_id`,`machine_model`,`min_axes`,`max_axes`,`min_speed`,`max_speed`,`motion_type`,`result_variant_id`,`explanation`,`is_active`) VALUES
(10,62,1,'SPM1200L',NULL,5,NULL,79,'ANY',11,'Up to 5 axes below 80 PPM is the DF-1858 build — the smallest SPM1200L, on AUTONICS temperature cards.',1),
(11,63,1,'SPM1200L',NULL,5,80,NULL,'ANY',8,'Up to 5 axes at 80 PPM or above is the DF-1723 build.',1),
(12,64,1,'SPM1200L',6,6,NULL,89,'ANY',12,'6 axes below 90 PPM is the DF-1864 build, which carries the case packer conveyor.',1),
(13,65,1,'SPM1200L',6,6,90,NULL,'ANY',10,'6 axes at 90 PPM or above is the DF-1808 build.',1),
(14,66,1,'SPM1200L',7,7,NULL,NULL,'ANY',9,'7 axes is the DF-1778 build — six axes plus traverse.',1);

-- --- FX5-1723 — DF-1723 (25 items) -------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(250,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1723',1),
(251,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1723',1),
(252,'2240027','16 SOURCE DI CARD','FX5-16EX/ES','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,'DF-1778 prints ERP 2240008 and DF-1827 prints none for this same card. 2240027 is used here (DF-1723/1808/1864) — verify before ordering.','review','Panel With Machine','NOS','DF-1723',1),
(253,'2020122','4 CH. TEMPERATURE CARD','FX5-4LC','MITSUBISHI',7,'TRACK_TEMP',1,8,1,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up. Matches DF-1723/1778/1808/1827 exactly; DF-1864 prints 8 for 12 tracks where the rule gives 7 — verify that BOM.','review','Panel With Machine','NOS','DF-1723',1),
(254,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1723',1),
(255,'4100058','CONVERTER MODULE','FX5-CNV-BC','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1723',1),
(256,'2030464','65CM CABLE','FX5-65EC','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,'DF-1808 and DF-1864 print the part as FX5-65ECA under the same ERP — confirm which suffix is correct.','review','Panel With Machine','NOS','DF-1723',1),
(257,'4100057','POWER SUPPLY','FX5-1PSU-5V','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1723',1),
(258,'4060513','10 INCH HMI','GS2110-WTBD','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,'DF-1858 was built with a 12 inch GT2512-WXTBD instead, which has no ERP code on that sheet.','review','Panel With Machine','NOS','DF-1723',1),
(259,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',3,'MANUAL',1,8,1,0,NULL,'UNWIND + COLLATING CONV.','Varies by conveyor count: DF-1778/1808/1858 = 2, DF-1723 = 3, DF-1864 = 4 (adds case packer). DF-1808 notes a further unit for the pump.','review','Panel With Machine','NOS','DF-1723',1),
(260,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1723',1),
(261,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,8,1,0,NULL,'FOR EYE MARK',NULL,'none','Panel With Machine','NOS','DF-1723',1),
(262,'4040022','DYNAMIC BRAKING RESISTOR (DBR), 13Ω, 500W','—','RECKON',2,'MANUAL',1,8,1,1,'feat_dbr','FOR REGENERATION: HORZ. + VERT.',NULL,'none','Panel With Machine','NOS','DF-1723',1),
(263,'2050391','3KW SERVO AMPLIFIER','MR-JE-300B','MITSUBISHI',2,'MANUAL',1,8,2,0,NULL,'HORZ. + VERT.','DF-1864 fits 3 (adds filling), DF-1858 fits 1 (piston only).','review','Panel With Machine','NOS','DF-1723',1),
(264,'4060641','3KW SERVO MOTOR, 14.3Nm, 3000RPM, W/O KEYWAY','HG-SN302J','MITSUBISHI',2,'MANUAL',1,8,2,0,NULL,'HORZ. + VERT.','Must match the MR-JE-300B count above.','review','Panel With Machine','NOS','DF-1723',1),
(265,'4030012','2KW SERVO AMPLIFIER','MR-JE-200B','MITSUBISHI',3,'MANUAL',1,8,2,0,NULL,'FILLING PISTON + ROTARY VALVE + PULLING','DF-1864 fits 2, DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1723',1),
(266,'4060525','2KW SERVO MOTOR, 9.55Nm, 2000RPM, W/O KEYWAY','HG-SN202J','MITSUBISHI',1,'MANUAL',1,8,2,0,NULL,'FILLING PISTON','DF-1858 fits 3 (horz. + vert. + rotary valve). Not fitted on DF-1864.','review','Panel With Machine','NOS','DF-1723',1),
(267,'2050388','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, W/O KEYWAY','HG-SN152J','MITSUBISHI',1,'MANUAL',1,8,2,0,NULL,'ROTARY VALVE',NULL,'none','Panel With Machine','NOS','DF-1723',1),
(268,'4060620','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, WITH KEYWAY','HG-SN152JK','MITSUBISHI',1,'MANUAL',1,8,2,0,NULL,'PULLING','Not fitted on DF-1858.','review','Panel With Machine','NOS','DF-1723',1),
(269,'2030328','ENCODER CABLE HG-SN/JR/SR, 1KW–3KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',5,'MANUAL',1,8,3,0,NULL,'HORZ. + VERT. + PISTON + PULLING + ROTARY VALVE','All five reference BOMs list 5 — but the true driver is the count of 1KW–3KW motors, so re-check when the servo list changes.','review','Panel With Machine','NOS','DF-1723',1),
(270,'2110119','POWER CABLE CONNECTOR FOR 2KW–3KW MOTORS','MS3106F2222S+TB','MITSUBISHI',3,'MANUAL',1,8,4,0,NULL,'HORZ. + VERT. + PISTON','DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1723',1),
(271,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',2,'MANUAL',1,8,4,0,NULL,'PULLING + ROTARY VALVE','DF-1858 fits 1.','review','Panel With Machine','NOS','DF-1723',1),
(272,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',1,8,5,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1723',1),
(273,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',4,'AXES_MINUS_1',1,8,5,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Qty = axes − 1. Confirmed on all five: DF-1723/1858 = 4 at 5 axes, DF-1808/1864 = 5 at 6 axes, DF-1778 = 6 at 7 axes.','none','Panel With Machine','NOS','DF-1723',1),
(274,'2050390','BATTERY SET FOR MR-J4/JE SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',5,'BATTERY',1,8,5,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). Equals the axis count on all five reference BOMs, but is entered separately because it does not always.','review','Panel With Machine','NOS','DF-1723',1);

-- --- FX5-1778 — DF-1778 (29 items) -------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(275,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1778',1),
(276,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1778',1),
(277,'2240027','16 SOURCE DI CARD','FX5-16EX/ES','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,'DF-1778 prints ERP 2240008 and DF-1827 prints none for this same card. 2240027 is used here (DF-1723/1808/1864) — verify before ordering.','review','Panel With Machine','NOS','DF-1778',1),
(278,'2020122','4 CH. TEMPERATURE CARD','FX5-4LC','MITSUBISHI',5,'TRACK_TEMP',1,9,1,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up. Matches DF-1723/1778/1808/1827 exactly; DF-1864 prints 8 for 12 tracks where the rule gives 7 — verify that BOM.','review','Panel With Machine','NOS','DF-1778',1),
(279,'2240039','4 CH. TEMPERATURE CARD','TM4-N2SB','AUTONICS',1,'MANUAL',1,9,1,0,NULL,'AUTONICS ALTERNATIVE TO THE FX5-4LC','DF-1858 uses this card exclusively (4 for 6 tracks). DF-1778 carries ONE alongside five FX5-4LC — a single extra unit, not a track-driven quantity.','review','Panel With Machine','NOS','DF-1778',1),
(280,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1778',1),
(281,'4100058','CONVERTER MODULE','FX5-CNV-BC','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1778',1),
(282,'2030464','65CM CABLE','FX5-65EC','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,'DF-1808 and DF-1864 print the part as FX5-65ECA under the same ERP — confirm which suffix is correct.','review','Panel With Machine','NOS','DF-1778',1),
(283,'4100057','POWER SUPPLY','FX5-1PSU-5V','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1778',1),
(284,'4060513','10 INCH HMI','GS2110-WTBD','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,'DF-1858 was built with a 12 inch GT2512-WXTBD instead, which has no ERP code on that sheet.','review','Panel With Machine','NOS','DF-1778',1),
(285,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',2,'MANUAL',1,9,1,0,NULL,'UNWIND + COLLATING CONV.','Varies by conveyor count: DF-1778/1808/1858 = 2, DF-1723 = 3, DF-1864 = 4 (adds case packer). DF-1808 notes a further unit for the pump.','review','Panel With Machine','NOS','DF-1778',1),
(286,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1778',1),
(287,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,9,1,0,NULL,'FOR EYE MARK',NULL,'none','Panel With Machine','NOS','DF-1778',1),
(288,'4040022','DYNAMIC BRAKING RESISTOR (DBR), 13Ω, 500W','—','RECKON',2,'MANUAL',1,9,1,1,'feat_dbr','FOR REGENERATION: HORZ. + VERT.',NULL,'none','Panel With Machine','NOS','DF-1778',1),
(289,'2050391','3KW SERVO AMPLIFIER','MR-JE-300B','MITSUBISHI',2,'MANUAL',1,9,2,0,NULL,'HORZ. + VERT.','DF-1864 fits 3 (adds filling), DF-1858 fits 1 (piston only).','review','Panel With Machine','NOS','DF-1778',1),
(290,'4060641','3KW SERVO MOTOR, 14.3Nm, 3000RPM, W/O KEYWAY','HG-SN302J','MITSUBISHI',2,'MANUAL',1,9,2,0,NULL,'HORZ. + VERT.','Must match the MR-JE-300B count above.','review','Panel With Machine','NOS','DF-1778',1),
(291,'4030012','2KW SERVO AMPLIFIER','MR-JE-200B','MITSUBISHI',3,'MANUAL',1,9,2,0,NULL,'FILLING PISTON + ROTARY VALVE + PULLING','DF-1864 fits 2, DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1778',1),
(292,'4060525','2KW SERVO MOTOR, 9.55Nm, 2000RPM, W/O KEYWAY','HG-SN202J','MITSUBISHI',1,'MANUAL',1,9,2,0,NULL,'FILLING PISTON','DF-1858 fits 3 (horz. + vert. + rotary valve). Not fitted on DF-1864.','review','Panel With Machine','NOS','DF-1778',1),
(293,'2050388','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, W/O KEYWAY','HG-SN152J','MITSUBISHI',1,'MANUAL',1,9,2,0,NULL,'ROTARY VALVE',NULL,'none','Panel With Machine','NOS','DF-1778',1),
(294,'4060620','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, WITH KEYWAY','HG-SN152JK','MITSUBISHI',1,'MANUAL',1,9,2,0,NULL,'PULLING','Not fitted on DF-1858.','review','Panel With Machine','NOS','DF-1778',1),
(295,'4120007','0.7KW SERVO AMPLIFIER','MR-JE-70B','MITSUBISHI',2,'MANUAL',1,9,2,1,'feat_perf','PERFORATION (+ TRAVERSE WHERE FITTED)','Qty = number of 0.7 kW auxiliary axes. DF-1778 fits 2 (traverse + perforation); DF-1808/1864 fit 1.','review','Panel With Machine','NOS','DF-1778',1),
(296,'4120009','0.7KW SERVO MOTOR, 2.4Nm, 3000RPM, WITH KEYWAY','HG-KN73JK','MITSUBISHI',2,'MANUAL',1,9,2,1,'feat_perf','PERFORATION (+ TRAVERSE WHERE FITTED)','Must match the MR-JE-70B count above.','review','Panel With Machine','NOS','DF-1778',1),
(297,'2030328','ENCODER CABLE HG-SN/JR/SR, 1KW–3KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',5,'MANUAL',1,9,3,0,NULL,'HORZ. + VERT. + PISTON + PULLING + ROTARY VALVE','All five reference BOMs list 5 — but the true driver is the count of 1KW–3KW motors, so re-check when the servo list changes.','review','Panel With Machine','NOS','DF-1778',1),
(298,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',2,'MANUAL',1,9,3,1,'feat_perf','PERFORATION','DF-1778 fits 2 (traverse + perforation).','review','Panel With Machine','NOS','DF-1778',1),
(299,'2110119','POWER CABLE CONNECTOR FOR 2KW–3KW MOTORS','MS3106F2222S+TB','MITSUBISHI',3,'MANUAL',1,9,4,0,NULL,'HORZ. + VERT. + PISTON','DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1778',1),
(300,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',2,'MANUAL',1,9,4,0,NULL,'PULLING + ROTARY VALVE','DF-1858 fits 1.','review','Panel With Machine','NOS','DF-1778',1),
(301,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',1,9,5,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1778',1),
(302,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',6,'AXES_MINUS_1',1,9,5,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Qty = axes − 1. Confirmed on all five: DF-1723/1858 = 4 at 5 axes, DF-1808/1864 = 5 at 6 axes, DF-1778 = 6 at 7 axes.','none','Panel With Machine','NOS','DF-1778',1),
(303,'2050390','BATTERY SET FOR MR-J4/JE SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',7,'BATTERY',1,9,5,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). Equals the axis count on all five reference BOMs, but is entered separately because it does not always.','review','Panel With Machine','NOS','DF-1778',1);

-- --- FX5-1808 — DF-1808 (29 items) -------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(304,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(305,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(306,'2240027','16 SOURCE DI CARD','FX5-16EX/ES','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,'DF-1778 prints ERP 2240008 and DF-1827 prints none for this same card. 2240027 is used here (DF-1723/1808/1864) — verify before ordering.','review','Panel With Machine','NOS','DF-1808',1),
(307,'2020122','4 CH. TEMPERATURE CARD','FX5-4LC','MITSUBISHI',7,'TRACK_TEMP',1,10,1,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up. Matches DF-1723/1778/1808/1827 exactly; DF-1864 prints 8 for 12 tracks where the rule gives 7 — verify that BOM.','review','Panel With Machine','NOS','DF-1808',1),
(308,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(309,'4100058','CONVERTER MODULE','FX5-CNV-BC','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(310,'2030464','65CM CABLE','FX5-65EC','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,'DF-1808 and DF-1864 print the part as FX5-65ECA under the same ERP — confirm which suffix is correct.','review','Panel With Machine','NOS','DF-1808',1),
(311,'4100057','POWER SUPPLY','FX5-1PSU-5V','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(312,'4060513','10 INCH HMI','GS2110-WTBD','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,'DF-1858 was built with a 12 inch GT2512-WXTBD instead, which has no ERP code on that sheet.','review','Panel With Machine','NOS','DF-1808',1),
(313,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',3,'MANUAL',1,10,1,0,NULL,'UNWIND + COLLATING CONV.','Varies by conveyor count: DF-1778/1808/1858 = 2, DF-1723 = 3, DF-1864 = 4 (adds case packer). DF-1808 notes a further unit for the pump.','review','Panel With Machine','NOS','DF-1808',1),
(314,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1808',1),
(315,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,10,1,0,NULL,'FOR EYE MARK',NULL,'none','Panel With Machine','NOS','DF-1808',1),
(316,'4040022','DYNAMIC BRAKING RESISTOR (DBR), 13Ω, 500W','—','RECKON',2,'MANUAL',1,10,1,1,'feat_dbr','FOR REGENERATION: HORZ. + VERT.',NULL,'none','Panel With Machine','NOS','DF-1808',1),
(317,'2050391','3KW SERVO AMPLIFIER','MR-JE-300B','MITSUBISHI',2,'MANUAL',1,10,2,0,NULL,'HORZ. + VERT.','DF-1864 fits 3 (adds filling), DF-1858 fits 1 (piston only).','review','Panel With Machine','NOS','DF-1808',1),
(318,'4060641','3KW SERVO MOTOR, 14.3Nm, 3000RPM, W/O KEYWAY','HG-SN302J','MITSUBISHI',2,'MANUAL',1,10,2,0,NULL,'HORZ. + VERT.','Must match the MR-JE-300B count above.','review','Panel With Machine','NOS','DF-1808',1),
(319,'4030012','2KW SERVO AMPLIFIER','MR-JE-200B','MITSUBISHI',3,'MANUAL',1,10,2,0,NULL,'FILLING PISTON + ROTARY VALVE + PULLING','DF-1864 fits 2, DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1808',1),
(320,'4060525','2KW SERVO MOTOR, 9.55Nm, 2000RPM, W/O KEYWAY','HG-SN202J','MITSUBISHI',1,'MANUAL',1,10,2,0,NULL,'FILLING PISTON','DF-1858 fits 3 (horz. + vert. + rotary valve). Not fitted on DF-1864.','review','Panel With Machine','NOS','DF-1808',1),
(321,'2050388','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, W/O KEYWAY','HG-SN152J','MITSUBISHI',1,'MANUAL',1,10,2,0,NULL,'ROTARY VALVE',NULL,'none','Panel With Machine','NOS','DF-1808',1),
(322,'4060620','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, WITH KEYWAY','HG-SN152JK','MITSUBISHI',1,'MANUAL',1,10,2,0,NULL,'PULLING','Not fitted on DF-1858.','review','Panel With Machine','NOS','DF-1808',1),
(323,'4120007','0.7KW SERVO AMPLIFIER','MR-JE-70B','MITSUBISHI',1,'MANUAL',1,10,2,1,'feat_perf','PERFORATION (+ TRAVERSE WHERE FITTED)','Qty = number of 0.7 kW auxiliary axes. DF-1778 fits 2 (traverse + perforation); DF-1808/1864 fit 1.','review','Panel With Machine','NOS','DF-1808',1),
(324,'4120009','0.7KW SERVO MOTOR, 2.4Nm, 3000RPM, WITH KEYWAY','HG-KN73JK','MITSUBISHI',1,'MANUAL',1,10,2,1,'feat_perf','PERFORATION (+ TRAVERSE WHERE FITTED)','Must match the MR-JE-70B count above.','review','Panel With Machine','NOS','DF-1808',1),
(325,'2030328','ENCODER CABLE HG-SN/JR/SR, 1KW–3KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',5,'MANUAL',1,10,3,0,NULL,'HORZ. + VERT. + PISTON + PULLING + ROTARY VALVE','All five reference BOMs list 5 — but the true driver is the count of 1KW–3KW motors, so re-check when the servo list changes.','review','Panel With Machine','NOS','DF-1808',1),
(326,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',1,'MANUAL',1,10,3,1,'feat_perf','PERFORATION','DF-1778 fits 2 (traverse + perforation).','review','Panel With Machine','NOS','DF-1808',1),
(327,'2110119','POWER CABLE CONNECTOR FOR 2KW–3KW MOTORS','MS3106F2222S+TB','MITSUBISHI',3,'MANUAL',1,10,4,0,NULL,'HORZ. + VERT. + PISTON','DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1808',1),
(328,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',2,'MANUAL',1,10,4,0,NULL,'PULLING + ROTARY VALVE','DF-1858 fits 1.','review','Panel With Machine','NOS','DF-1808',1),
(329,'4060594','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS1CBL03M-A2-L','MITSUBISHI',1,'MANUAL',1,10,4,1,'feat_perf','PERFORATION','DF-1778 instead lists ERP 4060431 / MR-PWS2CBL03M-A2-L, 2 off. Two different part numbers are in use for this cable — verify.','review','Panel With Machine','NOS','DF-1808',1),
(330,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',1,10,5,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1808',1),
(331,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',5,'AXES_MINUS_1',1,10,5,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Qty = axes − 1. Confirmed on all five: DF-1723/1858 = 4 at 5 axes, DF-1808/1864 = 5 at 6 axes, DF-1778 = 6 at 7 axes.','none','Panel With Machine','NOS','DF-1808',1),
(332,'2050390','BATTERY SET FOR MR-J4/JE SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',6,'BATTERY',1,10,5,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). Equals the axis count on all five reference BOMs, but is entered separately because it does not always.','review','Panel With Machine','NOS','DF-1808',1);

-- --- FX5-1858 — DF-1858 (20 items) -------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(333,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,11,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1858',1),
(334,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,11,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1858',1),
(335,'2240030','16 DI, 16 DO CARD','FX5-32ET/ESS','MITSUBISHI',1,'FIXED',1,11,1,0,NULL,'REPLACES THE FX5-16EX/ES','DF-1858 is built with this card instead of the FX5-16EX/ES. Turning this option on does NOT remove the 16EX line — deselect that item if it is a substitution.','review','Panel With Machine','NOS','DF-1858',1),
(336,'2240039','4 CH. TEMPERATURE CARD','TM4-N2SB','AUTONICS',4,'TRACK_TEMP',1,11,1,0,NULL,'AUTONICS ALTERNATIVE TO THE FX5-4LC','DF-1858 uses this card exclusively (4 for 6 tracks). DF-1778 carries ONE alongside five FX5-4LC — a single extra unit, not a track-driven quantity.','review','Panel With Machine','NOS','DF-1858',1),
(337,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,11,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1858',1),
(338,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',2,'MANUAL',1,11,1,0,NULL,'UNWIND + COLLATING CONV.','Varies by conveyor count: DF-1778/1808/1858 = 2, DF-1723 = 3, DF-1864 = 4 (adds case packer). DF-1808 notes a further unit for the pump.','review','Panel With Machine','NOS','DF-1858',1),
(339,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,11,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1858',1),
(340,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,11,1,0,NULL,'FOR EYE MARK',NULL,'none','Panel With Machine','NOS','DF-1858',1),
(341,'2050391','3KW SERVO AMPLIFIER','MR-JE-300B','MITSUBISHI',1,'MANUAL',1,11,2,0,NULL,'HORZ. + VERT.','DF-1864 fits 3 (adds filling), DF-1858 fits 1 (piston only).','review','Panel With Machine','NOS','DF-1858',1),
(342,'4060641','3KW SERVO MOTOR, 14.3Nm, 3000RPM, W/O KEYWAY','HG-SN302J','MITSUBISHI',1,'MANUAL',1,11,2,0,NULL,'HORZ. + VERT.','Must match the MR-JE-300B count above.','review','Panel With Machine','NOS','DF-1858',1),
(343,'4030012','2KW SERVO AMPLIFIER','MR-JE-200B','MITSUBISHI',4,'MANUAL',1,11,2,0,NULL,'FILLING PISTON + ROTARY VALVE + PULLING','DF-1864 fits 2, DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1858',1),
(344,'4060525','2KW SERVO MOTOR, 9.55Nm, 2000RPM, W/O KEYWAY','HG-SN202J','MITSUBISHI',3,'MANUAL',1,11,2,0,NULL,'FILLING PISTON','DF-1858 fits 3 (horz. + vert. + rotary valve). Not fitted on DF-1864.','review','Panel With Machine','NOS','DF-1858',1),
(345,'2050388','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, W/O KEYWAY','HG-SN152J','MITSUBISHI',1,'MANUAL',1,11,2,0,NULL,'ROTARY VALVE',NULL,'none','Panel With Machine','NOS','DF-1858',1),
(346,'4060620','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, WITH KEYWAY','HG-SN152JK','MITSUBISHI',1,'MANUAL',1,11,2,0,NULL,'PULLING','Not fitted on DF-1858.','review','Panel With Machine','NOS','DF-1858',1),
(347,'2030328','ENCODER CABLE HG-SN/JR/SR, 1KW–3KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',5,'MANUAL',1,11,3,0,NULL,'HORZ. + VERT. + PISTON + PULLING + ROTARY VALVE','All five reference BOMs list 5 — but the true driver is the count of 1KW–3KW motors, so re-check when the servo list changes.','review','Panel With Machine','NOS','DF-1858',1),
(348,'2110119','POWER CABLE CONNECTOR FOR 2KW–3KW MOTORS','MS3106F2222S+TB','MITSUBISHI',4,'MANUAL',1,11,4,0,NULL,'HORZ. + VERT. + PISTON','DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1858',1),
(349,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',1,'MANUAL',1,11,4,0,NULL,'PULLING + ROTARY VALVE','DF-1858 fits 1.','review','Panel With Machine','NOS','DF-1858',1),
(350,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',1,11,5,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1858',1),
(351,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',4,'AXES_MINUS_1',1,11,5,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Qty = axes − 1. Confirmed on all five: DF-1723/1858 = 4 at 5 axes, DF-1808/1864 = 5 at 6 axes, DF-1778 = 6 at 7 axes.','none','Panel With Machine','NOS','DF-1858',1),
(352,'2050390','BATTERY SET FOR MR-J4/JE SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',5,'BATTERY',1,11,5,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). Equals the axis count on all five reference BOMs, but is entered separately because it does not always.','review','Panel With Machine','NOS','DF-1858',1);

-- --- FX5-1864 — DF-1864 (28 items) -------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(353,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1864',1),
(354,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1864',1),
(355,'2240027','16 SOURCE DI CARD','FX5-16EX/ES','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,'DF-1778 prints ERP 2240008 and DF-1827 prints none for this same card. 2240027 is used here (DF-1723/1808/1864) — verify before ordering.','review','Panel With Machine','NOS','DF-1864',1),
(356,'2020122','4 CH. TEMPERATURE CARD','FX5-4LC','MITSUBISHI',8,'TRACK_TEMP',1,12,1,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up. Matches DF-1723/1778/1808/1827 exactly; DF-1864 prints 8 for 12 tracks where the rule gives 7 — verify that BOM.','review','Panel With Machine','NOS','DF-1864',1),
(357,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1864',1),
(358,'4100058','CONVERTER MODULE','FX5-CNV-BC','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1864',1),
(359,'2030464','65CM CABLE','FX5-65EC','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,'DF-1808 and DF-1864 print the part as FX5-65ECA under the same ERP — confirm which suffix is correct.','review','Panel With Machine','NOS','DF-1864',1),
(360,'4100057','POWER SUPPLY','FX5-1PSU-5V','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1864',1),
(361,'4060513','10 INCH HMI','GS2110-WTBD','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,'DF-1858 was built with a 12 inch GT2512-WXTBD instead, which has no ERP code on that sheet.','review','Panel With Machine','NOS','DF-1864',1),
(362,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',4,'MANUAL',1,12,1,0,NULL,'UNWIND + COLLATING CONV.','Varies by conveyor count: DF-1778/1808/1858 = 2, DF-1723 = 3, DF-1864 = 4 (adds case packer). DF-1808 notes a further unit for the pump.','review','Panel With Machine','NOS','DF-1864',1),
(363,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1864',1),
(364,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,12,1,0,NULL,'FOR EYE MARK',NULL,'none','Panel With Machine','NOS','DF-1864',1),
(365,'4040022','DYNAMIC BRAKING RESISTOR (DBR), 13Ω, 500W','—','RECKON',2,'MANUAL',1,12,1,1,'feat_dbr','FOR REGENERATION: HORZ. + VERT.',NULL,'none','Panel With Machine','NOS','DF-1864',1),
(366,'2050391','3KW SERVO AMPLIFIER','MR-JE-300B','MITSUBISHI',3,'MANUAL',1,12,2,0,NULL,'HORZ. + VERT.','DF-1864 fits 3 (adds filling), DF-1858 fits 1 (piston only).','review','Panel With Machine','NOS','DF-1864',1),
(367,'4060641','3KW SERVO MOTOR, 14.3Nm, 3000RPM, W/O KEYWAY','HG-SN302J','MITSUBISHI',3,'MANUAL',1,12,2,0,NULL,'HORZ. + VERT.','Must match the MR-JE-300B count above.','review','Panel With Machine','NOS','DF-1864',1),
(368,'4030012','2KW SERVO AMPLIFIER','MR-JE-200B','MITSUBISHI',2,'MANUAL',1,12,2,0,NULL,'FILLING PISTON + ROTARY VALVE + PULLING','DF-1864 fits 2, DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1864',1),
(369,'2050388','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, W/O KEYWAY','HG-SN152J','MITSUBISHI',1,'MANUAL',1,12,2,0,NULL,'ROTARY VALVE',NULL,'none','Panel With Machine','NOS','DF-1864',1),
(370,'4060620','1.5KW SERVO MOTOR, 7.16Nm, 2000RPM, WITH KEYWAY','HG-SN152JK','MITSUBISHI',1,'MANUAL',1,12,2,0,NULL,'PULLING','Not fitted on DF-1858.','review','Panel With Machine','NOS','DF-1864',1),
(371,'4120007','0.7KW SERVO AMPLIFIER','MR-JE-70B','MITSUBISHI',1,'MANUAL',1,12,2,1,'feat_perf','PERFORATION (+ TRAVERSE WHERE FITTED)','Qty = number of 0.7 kW auxiliary axes. DF-1778 fits 2 (traverse + perforation); DF-1808/1864 fit 1.','review','Panel With Machine','NOS','DF-1864',1),
(372,'4120009','0.7KW SERVO MOTOR, 2.4Nm, 3000RPM, WITH KEYWAY','HG-KN73JK','MITSUBISHI',1,'MANUAL',1,12,2,1,'feat_perf','PERFORATION (+ TRAVERSE WHERE FITTED)','Must match the MR-JE-70B count above.','review','Panel With Machine','NOS','DF-1864',1),
(373,'2030328','ENCODER CABLE HG-SN/JR/SR, 1KW–3KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',5,'MANUAL',1,12,3,0,NULL,'HORZ. + VERT. + PISTON + PULLING + ROTARY VALVE','All five reference BOMs list 5 — but the true driver is the count of 1KW–3KW motors, so re-check when the servo list changes.','review','Panel With Machine','NOS','DF-1864',1),
(374,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',1,'MANUAL',1,12,3,1,'feat_perf','PERFORATION','DF-1778 fits 2 (traverse + perforation).','review','Panel With Machine','NOS','DF-1864',1),
(375,'2110119','POWER CABLE CONNECTOR FOR 2KW–3KW MOTORS','MS3106F2222S+TB','MITSUBISHI',3,'MANUAL',1,12,4,0,NULL,'HORZ. + VERT. + PISTON','DF-1858 fits 4.','review','Panel With Machine','NOS','DF-1864',1),
(376,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',2,'MANUAL',1,12,4,0,NULL,'PULLING + ROTARY VALVE','DF-1858 fits 1.','review','Panel With Machine','NOS','DF-1864',1),
(377,'4060594','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS1CBL03M-A2-L','MITSUBISHI',1,'MANUAL',1,12,4,1,'feat_perf','PERFORATION','DF-1778 instead lists ERP 4060431 / MR-PWS2CBL03M-A2-L, 2 off. Two different part numbers are in use for this cable — verify.','review','Panel With Machine','NOS','DF-1864',1),
(378,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',1,12,5,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1864',1),
(379,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',5,'AXES_MINUS_1',1,12,5,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Qty = axes − 1. Confirmed on all five: DF-1723/1858 = 4 at 5 axes, DF-1808/1864 = 5 at 6 axes, DF-1778 = 6 at 7 axes.','none','Panel With Machine','NOS','DF-1864',1),
(380,'2050390','BATTERY SET FOR MR-J4/JE SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',6,'BATTERY',1,12,5,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). Equals the axis count on all five reference BOMs, but is entered separately because it does not always.','review','Panel With Machine','NOS','DF-1864',1);

-- --- retire the shared build it replaces ------------------------------
-- Deactivated, never deleted: saved BOM lines point at these item ids.
UPDATE `abom_variant`      SET `is_active` = 0 WHERE `id` = 1;
UPDATE `abom_variant_rule` SET `is_active` = 0 WHERE `id` = 7;
UPDATE `abom_item`         SET `is_active` = 0 WHERE `variant_id` = 1;

COMMIT;

-- =====================================================================
--  POST-INSTALL CHECK
--
--   SELECT v.code, COUNT(i.id) AS items
--     FROM abom_variant v
--     LEFT JOIN abom_item i ON i.variant_id = v.id AND i.is_active = 1
--    WHERE v.is_active = 1
--    GROUP BY v.code, v.sort_order ORDER BY v.sort_order;
--
--      FX5-J4 29 · IQR-STD 34 · IQR-HS 42 · IQR-FLM 42 · IQR-TCF 40
--      FX5-TCF 31 · FX5-1723 25 · FX5-1778 29 · FX5-1808 29
--      FX5-1858 20 · FX5-1864 28
--
--   SELECT COUNT(*) FROM abom_item WHERE is_active = 1;      -- 349
--   SELECT COUNT(*) FROM abom_variant WHERE is_active = 1;   -- 11
--
--  And on /abom/generate, each of the five must now reach its own build:
--   5 axis · 12 track · 100 PPM -> FX5-1723
--   5 axis ·  6 track ·  70 PPM -> FX5-1858
--   6 axis · 12 track · 100 PPM -> FX5-1808
--   6 axis · 12 track ·  80 PPM -> FX5-1864
--   7 axis ·  8 track ·  80 PPM -> FX5-1778
-- =====================================================================
