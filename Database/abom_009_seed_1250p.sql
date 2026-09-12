-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — TWO SPM1250P BUILDS (install 009)
--
--  Run AFTER Database/abom_008_plc_rule_model.sql.
--
--  ADDS DF-1805 and DF-1883 as two new build variants, with their items
--  and their selection rules.
--
--  PURELY ADDITIVE. There is NO DELETE, NO TRUNCATE and NO UPDATE of any
--  existing row anywhere in this file. Every id it writes is new:
--
--      abom_variant       6, 7          (1-5 untouched)
--      abom_variant_rule  8, 9          (1-7 untouched)
--      abom_plc_rule      5             (1-4 untouched)
--      abom_item          179-249       (1-178 untouched)
--
--  Existing BOMs are not read or written. The nine earlier reference
--  builds generate exactly what they generated before — proven by the
--  regression numbers in tests/run_tests.php, which are unchanged.
--
--  THE TWO MACHINES
--  ----------------
--      DF-1805   SPM1250P  11 axis  6 track  120 PPM  CONTINUOUS
--                Tilting cup filling. iQ-R, standalone panel.   40 items
--
--      DF-1883   SPM1250P  11 axis  6 track   70 PPM  INTERMITTENT
--                Tilting cup filling with filling plate.
--                FX5, panel with machine.                       31 items
--
--  Same model, same axis count, same track count — and DIFFERENT PLC
--  FAMILIES. That is why both a PLC rule and two variant rules are
--  needed, and why all three are scoped tightly enough that no existing
--  machine can reach them.
--
--  HOW THE NEW RULES AVOID THE EXISTING ONES
--  -----------------------------------------
--  Every new rule is bounded by machine_model SPM1250P AND an axis band
--  of 9 to 12. DF-1855, the only SPM1250P already in the system, is 15
--  axes — so it clears none of them and still resolves through the
--  priority-10 rule to IQR-FLM exactly as before. Every SPM1200L fails
--  the model test.
--
--  The LOWER bound matters as much as the upper one. Without it a
--  six-axis SPM1250P would fall into the eleven-axis tilting-cup build
--  and quote its item list. No reference BOM describes such a machine,
--  so the honest answer is no match — the generator then says so and
--  generates nothing, rather than inventing a plausible sheet.
--
--      DF-1883  SPM1250P 11ax  70 PPM Intermittent -> PLC rule 5 -> FX5
--                                                  -> var rule 8 -> FX5-TCF
--      DF-1805  SPM1250P 11ax 120 PPM Continuous   -> PLC rule 1 -> iQ-R
--                                                  -> var rule 9 -> IQR-TCF
--      DF-1855  SPM1250P 15ax  80 PPM Continuous   -> PLC rule 1 -> iQ-R
--                                                  -> var rule 1 -> IQR-FLM   (unchanged)
--
--  ⚠ DF-1883 CARRIES FIVE ERP CODES ON THE WRONG PARTS
--  ---------------------------------------------------
--  Its worksheet tab is literally named "Old BOM", and it reuses five
--  FX5-era ERP codes for MR-J4 parts that every other sheet gives to
--  different components:
--
--      4030012  MR-JE-200B elsewhere  ->  MR-J4-200B here
--      4060525  HG-SN202J  elsewhere  ->  HG-SR202   here
--      4060620  HG-SN152JK elsewhere  ->  HG-SR152   here
--      4120007  MR-JE-70B  elsewhere  ->  MR-J4-70B  here
--      4120009  HG-KN73JK  elsewhere  ->  HG-KR73    here
--
--  Seeded VERBATIM as supplied and every one flagged 'conflict', so they
--  surface on the sheet, in the PDF's "Points to verify" box and in
--  find_erp_conflicts(). They are NOT silently corrected — that is an
--  engineering call, not a data-entry one. This pattern is what a BOM
--  copied from an older machine and re-parted without re-coding looks
--  like, and it should be confirmed before anything is ordered from it.
--
--  TEMPERATURE CARDS AT 6 TRACKS
--  -----------------------------
--  The rule gives 4.  DF-1883 lists 4 — agrees.
--                     DF-1805 lists 5 — flagged for review on that row.
-- =====================================================================
SET NAMES utf8mb4;

START TRANSACTION;

-- --- Build variants ---------------------------------------------------
INSERT INTO `abom_variant`
(`id`,`plc_family_id`,`code`,`name`,`description`,`machine_model`,`source_df`,`default_j4_units`,`default_battery`,`default_panel_location`,`is_active`,`sort_order`) VALUES
(6,2,'IQR-TCF','iQ-R tilting cup filling (11 axis)','SPM1250P tilting cup filling at 120 PPM continuous. Standalone iQ-R panel, 12 inch HMI, 7 kW up/down assembly with electromagnetic brake, front and back flapper axes.','SPM1250P','DF-1805',13,13,'Standalone',1,6),
(7,1,'FX5-TCF','FX5 tilting cup filling (11 axis)','SPM1250P tilting cup filling with filling plate at 70 PPM intermittent. FX5 running ELEVEN axes on two simple-motion cards (FX5-80SSC-S + FX5-40SSC-S), panel mounted with the machine, six individual weight-adjust axes.','SPM1250P','DF-1883',11,11,'Panel With Machine',1,7);

-- --- PLC family rule --------------------------------------------------
-- Ahead of the priority-10 axis rule, and narrow enough that only this
-- machine reaches it: the model, an axis ceiling below DF-1855's 15, and
-- the motion type that distinguishes it from DF-1805.
INSERT INTO `abom_plc_rule`
(`id`,`priority`,`machine_model`,`min_axes`,`max_axes`,`min_speed`,`max_speed`,`motion_type`,`result_family_id`,`explanation`,`is_active`) VALUES
(5,5,'SPM1250P',9,12,NULL,NULL,'Intermittent',1,'SPM1250P intermittent between 9 and 12 axes runs on FX5 with a second simple-motion card (FX5-80SSC-S + FX5-40SSC-S) — the 8-axis limit is a property of one card, not of the family.',1);

-- --- Build selection rules -------------------------------------------
INSERT INTO `abom_variant_rule`
(`id`,`priority`,`plc_family_id`,`machine_model`,`min_axes`,`max_axes`,`min_speed`,`max_speed`,`motion_type`,`result_variant_id`,`explanation`,`is_active`) VALUES
(8,5,1,'SPM1250P',9,12,NULL,NULL,'ANY',7,'SPM1250P on FX5 between 9 and 12 axes is the tilting cup filling machine with the filling plate and six individual weight-adjust axes.',1),
(9,6,2,'SPM1250P',9,12,NULL,NULL,'ANY',6,'SPM1250P on iQ-R between 9 and 12 axes is the 120 PPM continuous tilting cup filling machine. Below 9 no reference build exists; above 12 it is the 15-axis flow-meter build.',1);

-- =====================================================================
--  VARIANT 6 — IQR-TCF   (DF-1805, 40 items, iQ-R sections 6-10)
-- =====================================================================
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(179,'4140023','PROGRAMMABLE CONTROLLER CPU MODULE, CC-LINK IE FIELD BASIC, 2 PORTS','R04ENCPU','MITSUBISHI',1,'FIXED',2,6,6,0,NULL,NULL,'DF-1826 prints ERP 4010223 for R04ENCPU. Two codes are in circulation for this CPU — verify which applies to the CC-Link IE Field Basic variant.','review','Standalone','NOS','DF-1805',1),
(180,'4010224','MAIN BASE UNIT, 12 SLOT','R312B','MITSUBISHI',1,'FIXED',2,6,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1805',1),
(181,'4100055','POWER SUPPLY MODULE','R61P','MITSUBISHI',1,'FIXED',2,6,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1805',1),
(182,'4010230','BUS END','M-BE','MITSUBISHI',1,'FIXED',2,6,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1805',1),
(183,'4010222','16 AXIS MOTION MODULE, CC-LINK IE FIELD BASIC','R16MTCPU','MITSUBISHI',1,'FIXED',2,6,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1805',1),
(184,'4010226','CC-LINK IO HEADER MODULE','M-CCB-H','MITSUBISHI',1,'FIXED',2,6,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1805',1),
(185,'4010227','16 CH. DI CARD','M-16D','MITSUBISHI',4,'MANUAL',2,6,6,0,NULL,NULL,'Base qty 4 — review per machine I/O count.','review','Standalone','NOS','DF-1805',1),
(186,'4010228','16 CH. DO CARD','M-16TE','MITSUBISHI',4,'MANUAL',2,6,6,0,NULL,NULL,'Base qty 4 — review per machine I/O count.','review','Standalone','NOS','DF-1805',1),
(187,'4060518','4 CH. ANALOG INPUT MODULE','M-AD4','MITSUBISHI',1,'FIXED',2,6,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1805',1),
(188,'4010225','TEMPERATURE CONTROL MODULE, 4 CH. RTD','R60TCRT4','MITSUBISHI',5,'TRACK_TEMP',2,6,6,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up, which gives 4 at 6 tracks. DF-1805 lists 5 — verify that sheet.','review','Standalone','NOS','DF-1805',1),
(189,'4020090','GT25 HMI — 12 INCH','GT2512-WXTBD','MITSUBISHI',1,'FIXED',2,6,6,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1805',1),
(190,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',5,'MANUAL',2,6,6,0,NULL,'DRUM ROLLER + COLLATING CONV. + ROPE CONV. + TAPER CONV. + WEIGHING CONV.','DF-1805 writes the quantity as "1 + 4". Seeded as 5 — confirm the split is not two different ratings.','review','Standalone','NOS','DF-1805',1),
(191,'4010229','2 CH. ANALOG OUTPUT MODULE','M-DA2','MITSUBISHI',3,'MANUAL',2,6,6,0,NULL,'FOR VFD SET POINTS','DF-1805 writes the quantity as "1+2". Seeded as 3 — confirm.','review','Standalone','NOS','DF-1805',1),
(192,'2200006','SAFE TORQUE OFF CONNECTOR','MR-D05ULD3M-B','MITSUBISHI',13,'J4_STO',2,6,6,0,NULL,NULL,'Qty = MR-J4 amp units. DF-1805 prints the part as MR-D05UDL3M-B; every other sheet prints MR-D05ULD3M-B — treated as a typo on that sheet.','review','Standalone','NOS','DF-1805',1),
(193,'2110113','AMPLIFIER I/O CONNECTOR','MR-CCN1','MITSUBISHI',1,'FIXED',2,6,6,1,'feat_imark','FOR I-MARK SENSOR',NULL,'none','Standalone','NOS','DF-1805',1),
(194,'4060660','SERVO AMPLIFIER 7KW WITH SSCNET & ELECTROMAGNETIC BRAKE','MR-J4-700B','MITSUBISHI',1,'FIXED',2,6,7,0,NULL,'UP/DOWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1805',1),
(195,'4060583','SERVO AMPLIFIER 5KW WITH SSCNET','MR-J4-500B','MITSUBISHI',2,'FIXED',2,6,7,0,NULL,'HORZ. + VERT.',NULL,'none','Standalone','NOS','DF-1805',1),
(196,'4060510','SERVO AMPLIFIER 2KW WITH SSCNET','MR-J4-200B','MITSUBISHI',3,'MANUAL',2,6,7,0,NULL,NULL,'DF-1770 prints ERP 4060196 for this same amplifier — verify.','review','Standalone','NOS','DF-1805',1),
(197,'4060432','SERVO AMPLIFIER 0.75KW DUAL AXIS WITH SSCNET','MR-J4W2-77B','MITSUBISHI',3,'MANUAL',2,6,7,0,NULL,NULL,'Base qty 3 units = 6 axes (dual-axis amp). Review per config.','review','Standalone','NOS','DF-1805',1),
(198,'4060661','SERVO MOTOR 33.4Nm, 7KW, 2000RPM','HG-SR702B','MITSUBISHI',1,'FIXED',2,6,7,0,NULL,'UP/DOWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1805',1),
(199,'4060662','SERVO MOTOR 15.9Nm, 5KW, 3000RPM','HG-JR503','MITSUBISHI',2,'FIXED',2,6,7,0,NULL,'HORZ. + VERT.',NULL,'none','Standalone','NOS','DF-1805',1),
(200,'4060663','SERVO MOTOR 9.55Nm, 2KW, 2000RPM','HG-SR202','MITSUBISHI',1,'FIXED',2,6,7,0,NULL,'PULLING (KEYWAY FROM MARKET)',NULL,'none','Standalone','NOS','DF-1805',1),
(201,'4060438','SERVO MOTOR 7.16Nm, 1.5KW, 2000RPM','HG-SR152','MITSUBISHI',2,'MANUAL',2,6,7,0,NULL,'FRONT FLAPPER + BACK FLAPPER',NULL,'none','Standalone','NOS','DF-1805',1),
(202,'2050266','SERVO MOTOR 2.4Nm, 0.7KW, 3000RPM','HG-KR73','MITSUBISHI',5,'MANUAL',2,6,7,0,NULL,'OVERALL WEIGHT + TILTING + PERFORATION + PIN HOLE + UNWIND','Review per config.','review','Standalone','NOS','DF-1805',1),
(203,'4060431','ENCODER CABLE HG-KN/KR, 0.75KW, 0.3 MTR','MR-J3JCBL03M-A2-L','MITSUBISHI',2,'MANUAL',2,6,8,0,NULL,'UNWIND + PIN HOLE ASSEMBLY','⚠ ERP 4060431 CONFLICT — also used for power cable MR-PWS2CBL03M-A2-L. Data error — verify both before ordering.','conflict','Standalone','NOS','DF-1805',1),
(204,NULL,'ENCODER CABLE HG-KN/KR, 0.75KW, 20 MTR','MR-EKCBL20M-L','MITSUBISHI',2,'MANUAL',2,6,8,0,NULL,NULL,'NO ERP CODE on DF-1805. DF-1826 prints 2030772 for the same cable — confirm.','no_erp','Standalone','NOS','DF-1805',1),
(205,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',1,'MANUAL',2,6,8,0,NULL,'PERFORATION','⚠ ERP 2030449 CONFLICT — DF-1827 assigns it to the 5 MTR cable instead. Verify.','conflict','Standalone','NOS','DF-1805',1),
(206,'4070181','ENCODER CABLE HG-KN/KR, 0.75KW, 5 MTR','MR-J3ENCBL5M-A2-L','MITSUBISHI',2,'MANUAL',2,6,8,0,NULL,'CENTRE TILTING + OVERALL WEIGHT ADJ.','DF-1827 prints ERP 2030449 for this same cable — two codes are in circulation.','review','Standalone','NOS','DF-1805',1),
(207,'2030373','ENCODER CABLE HG-JR/SR, 1KW–7KW, 10 MTR','MR-J3ENSCBL10M-L','MITSUBISHI',4,'MANUAL',2,6,8,0,NULL,'UP/DOWN ASSEMBLY + HORZ. + VERT. + PULLING','Review per cable routing.','review','Standalone','NOS','DF-1805',1),
(208,'2030328','ENCODER CABLE HG-JR/SR, 1KW–7KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',2,'MANUAL',2,6,8,0,NULL,'FRONT FLAPPER + BACK FLAPPER','Review per cable routing.','review','Standalone','NOS','DF-1805',1),
(209,'4060431','POWER CABLE 0.3 MTR FOR 0.1KW–0.75KW MOTORS','MR-PWS2CBL03M-A2-L','MITSUBISHI',5,'MANUAL',2,6,9,0,NULL,'UNWIND + PIN HOLE + PERFORATION + CENTRE TILTING FUNNEL + OVERALL WEIGHT ADJ.','⚠ ERP 4060431 CONFLICT — also used for encoder cable MR-J3JCBL03M-A2-L. Data error — verify both before ordering.','conflict','Standalone','NOS','DF-1805',1),
(210,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',2,'MANUAL',2,6,9,0,NULL,'FRONT FLAPPER + BACK FLAPPER',NULL,'none','Standalone','NOS','DF-1805',1),
(211,'2110119','POWER CABLE CONNECTOR FOR 2KW–5KW MOTORS','MS3106F2222S+TB','MITSUBISHI',3,'MANUAL',2,6,9,0,NULL,'HORZ. + VERT. + PULLING',NULL,'none','Standalone','NOS','DF-1805',1),
(212,'2110162','POWER CONNECTOR FOR 7KW SERVO MOTOR','MS3106F3217S+TB','MITSUBISHI',1,'FIXED',2,6,9,0,NULL,'UP/DOWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1805',1),
(213,'2110163','ELECTROMAGNETIC BRAKE CONNECTOR SET','MR-BKCNS1','MITSUBISHI',1,'FIXED',2,6,9,1,'feat_brake',NULL,'⚠ ERP 2110163 CONFLICT — DF-1855 prints the same code against brake CABLE set SC-BKC1CBL1M-L. Verify.','conflict','Standalone','NOS','DF-1805',1),
(214,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',2,6,10,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Standalone','NOS','DF-1805',1),
(215,'2030505','SSCNET CABLE, 3 METRES','MR-J3BUS3M-A','MITSUBISHI',3,'MANUAL',2,6,10,0,NULL,NULL,'⚠ ERP 2030505 CONFLICT — DF-1826 prints MR-J3BUS3M under this code. The -A suffix is a different Mitsubishi part.','conflict','Standalone','NOS','DF-1805',1),
(216,'2030364','SSCNET CABLE, 1 METRE','MR-J3BUS1M','MITSUBISHI',10,'MANUAL',2,6,10,0,NULL,NULL,'Base qty 10 — review per amplifier layout topology.','review','Standalone','NOS','DF-1805',1),
(217,'2050390','BATTERY SET FOR MR-J4 SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',13,'BATTERY',2,6,10,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). DF-1805 ref = 13.','review','Standalone','NOS','DF-1805',1),
(218,'2050599','DYNAMIC BRAKING RESISTOR (DBR), 6.7Ω, 500W','—','RECKON',3,'MANUAL',2,6,10,1,'feat_dbr','FOR REGENERATION',NULL,'none','Standalone','NOS','DF-1805',1);

-- =====================================================================
--  VARIANT 7 — FX5-TCF   (DF-1883, 31 items, FX5 sections 1-5)
-- =====================================================================
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`variant_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(219,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1883',1),
(220,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1883',1),
(221,'2240027','16 SINK/SOURCE DI CARD','FX5-16EX/ES','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1883',1),
(222,'2020122','4 CH. TEMPERATURE CARD','FX5-4LC','MITSUBISHI',4,'TRACK_TEMP',1,7,1,0,NULL,NULL,'Qty = (((tracks+1)*2)+2)/4 rounded up. DF-1883 lists 4 at 6 tracks, which the rule reproduces exactly.','none','Panel With Machine','NOS','DF-1883',1),
(223,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,'AXES 1-8',NULL,'none','Panel With Machine','NOS','DF-1883',1),
(224,NULL,'4 AXIS SIMPLE MOTION CARD','FX5-40SSC-S','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,'AXES 9-11','NEW PART — NO ERP CODE. This is the card that lets an FX5 run 11 axes; without it the 8-axis limit applies and this machine needs iQ-R.','no_erp','Panel With Machine','NOS','DF-1883',1),
(225,'4100058','CONVERTER MODULE','FX5-CNV-BC','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1883',1),
(226,'2030464','65CM CABLE','FX5-65EC','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1883',1),
(227,'4060513','10 INCH HMI','GS2110-WTBD','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1883',1),
(228,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',5,'MANUAL',1,7,1,0,NULL,'ROPE CONV. + TAKE OFF CONV. + COLLATING CONV. + WEIGHING CONV. + UNWIND','Base qty 5 — review per conveyor count.','review','Panel With Machine','NOS','DF-1883',1),
(229,'4060585','4 CH. ANALOG INPUT CARD','FX5-4AD-ADP','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,'FOR LOAD CELL',NULL,'none','Panel With Machine','NOS','DF-1883',1),
(230,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,'FOR VFDs',NULL,'none','Panel With Machine','NOS','DF-1883',1),
(231,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,7,1,0,NULL,'FOR I-MARK',NULL,'none','Panel With Machine','NOS','DF-1883',1),
(232,'4040022','DYNAMIC BRAKING RESISTOR (DBR), 13Ω, 500W','—','RECKON',2,'MANUAL',1,7,1,1,'feat_dbr','FOR REGENERATION: HORZ. + VERT.',NULL,'none','Panel With Machine','NOS','DF-1883',1),
(233,'4030012','SERVO AMPLIFIER 2KW WITH SSCNET','MR-J4-200B','MITSUBISHI',3,'MANUAL',1,7,2,0,NULL,'HORZ. + VERT.','⚠ ERP 4030012 CONFLICT — every other sheet gives this code to the MR-JE-200B. DF-1883 puts an MR-J4-200B on it. Its worksheet tab is named "Old BOM"; this looks like a BOM re-parted without re-coding. Verify before ordering.','conflict','Panel With Machine','NOS','DF-1883',1),
(234,'4060525','SERVO MOTOR 9.55Nm, 2KW, 2000RPM','HG-SR202','MITSUBISHI',2,'MANUAL',1,7,2,0,NULL,NULL,'⚠ ERP 4060525 CONFLICT — every other sheet gives this code to the HG-SN202J. 4060663 is HG-SR202 elsewhere. Verify.','conflict','Panel With Machine','NOS','DF-1883',1),
(235,NULL,'SERVO AMPLIFIER 3KW','MR-JE-300B','MITSUBISHI',1,'MANUAL',1,7,2,0,NULL,'FILLING MOTOR','NO ERP CODE on DF-1883. 2050391 is MR-JE-300B on five other sheets — confirm.','no_erp','Panel With Machine','NOS','DF-1883',1),
(236,NULL,'SERVO MOTOR 14.3Nm, 3KW, 3000RPM, W/O KEYWAY','HG-SN302J','MITSUBISHI',1,'MANUAL',1,7,2,0,NULL,'FILLING MOTOR','NO ERP CODE on DF-1883, and it describes this part as 1.5KW 7.16Nm — HG-SN302J is 3KW 14.3Nm on five other sheets, where it carries 4060641. Both the rating and the code need confirming.','no_erp','Panel With Machine','NOS','DF-1883',1),
(237,'4060620','SERVO MOTOR 7.16Nm, 1.5KW, 2000RPM','HG-SR152','MITSUBISHI',1,'MANUAL',1,7,2,0,NULL,NULL,'⚠ ERP 4060620 CONFLICT — every other sheet gives this code to the HG-SN152JK. 4060438 is HG-SR152 elsewhere. Verify.','conflict','Panel With Machine','NOS','DF-1883',1),
(238,'4120007','SERVO AMPLIFIER 0.7KW','MR-J4-70B','MITSUBISHI',7,'MANUAL',1,7,2,0,NULL,'PERFORATION + INDIVIDUAL WEIGHT ADJUST (6 NOS.)','⚠ ERP 4120007 CONFLICT — every other sheet gives this code to the MR-JE-70B. DF-1883 puts an MR-J4-70B on it. Verify.','conflict','Panel With Machine','NOS','DF-1883',1),
(239,'4120009','SERVO MOTOR 2.4Nm, 0.7KW, 3000RPM','HG-KR73','MITSUBISHI',7,'MANUAL',1,7,2,0,NULL,'PERFORATION + INDIVIDUAL WEIGHT ADJUST (6 NOS.)','⚠ ERP 4120009 CONFLICT — every other sheet gives this code to the HG-KN73JK. 2050266 is HG-KR73 elsewhere. Verify.','conflict','Panel With Machine','NOS','DF-1883',1),
(240,'2030328','ENCODER CABLE HG-JR/SR, 1KW–7KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',4,'MANUAL',1,7,3,0,NULL,'HORZ. + VERT. + PULLING + FILLING PLATE','Review per cable routing.','review','Panel With Machine','NOS','DF-1883',1),
(241,'2030449','ENCODER CABLE HG-KN/KR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',1,'MANUAL',1,7,3,0,NULL,'PERFORATION','⚠ ERP 2030449 CONFLICT — DF-1827 assigns it to the 5 MTR cable instead. Verify.','conflict','Panel With Machine','NOS','DF-1883',1),
(242,NULL,'ENCODER CABLE HG-KN/KR, 0.75KW, 5 MTR','MR-J3ENCBL5M-A2-L','MITSUBISHI',6,'MANUAL',1,7,3,0,NULL,'INDIVIDUAL WEIGHT ADJUSTMENT','NO ERP CODE on DF-1883. DF-1770 prints 4070181 and DF-1827 prints 2030449 for this same cable — settle it.','no_erp','Panel With Machine','NOS','DF-1883',1),
(243,'2110119','POWER CABLE CONNECTOR FOR 2KW–3KW MOTORS','MS3106F2222S+TB','MITSUBISHI',2,'MANUAL',1,7,4,0,NULL,NULL,'Review per motor count.','review','Panel With Machine','NOS','DF-1883',1),
(244,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',2,'MANUAL',1,7,4,0,NULL,NULL,'Review per motor count.','review','Panel With Machine','NOS','DF-1883',1),
(245,'4060431','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS2CBL03M-A2-L','MITSUBISHI',7,'MANUAL',1,7,4,0,NULL,'PERFORATION + INDIVIDUAL WEIGHT ADJUST (6 NOS.)','⚠ ERP 4060431 CONFLICT — also used for encoder cable MR-J3JCBL03M-A2-L. Verify both before ordering.','conflict','Panel With Machine','NOS','DF-1883',1),
(246,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',1,7,5,0,NULL,'CONTROLLER → FIRST AMPLIFIER',NULL,'none','Panel With Machine','NOS','DF-1883',1),
(247,NULL,'SSCNET CABLE, 3 METRES','MR-J3BUS3M-A','MITSUBISHI',1,'MANUAL',1,7,5,0,NULL,'ALL DRIVES TO INDIVIDUAL WEIGHT SERVO','NO ERP CODE on DF-1883. 2030505 is used for this part elsewhere — confirm.','no_erp','Panel With Machine','NOS','DF-1883',1),
(248,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3BUS05M','MITSUBISHI',10,'MANUAL',1,7,5,0,NULL,'INTER-AMPLIFIER DAISY CHAIN','Base qty 10 for 11 axes. The FX5 builds otherwise follow axes − 1, which would give 10 — consistent, but left manual because this machine daisy-chains across two motion cards.','review','Panel With Machine','NOS','DF-1883',1),
(249,'2050390','BATTERY SET FOR MR-J4/JE SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',11,'BATTERY',1,7,5,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). DF-1883 ref = 11.','review','Panel With Machine','NOS','DF-1883',1);

COMMIT;

-- =====================================================================
--  POST-INSTALL VERIFICATION
--
--   SELECT COUNT(*) FROM abom_item;                          -- 249
--   SELECT variant_id, COUNT(*) FROM abom_item
--     GROUP BY variant_id ORDER BY variant_id;
--                          -- 31/29/34/42/42/40/31
--   SELECT COUNT(*) FROM abom_variant WHERE is_active=1;     -- 7
--   SELECT COUNT(*) FROM abom_variant_rule WHERE is_active=1;-- 9
--   SELECT COUNT(*) FROM abom_plc_rule WHERE is_active=1;    -- 5
--   SELECT COUNT(*) FROM abom_item WHERE variant_id IS NULL; -- 0
--
--  And on /abom/generate, the two new machines:
--   SPM1250P · 11 axis ·  6 track ·  70 PPM · Intermittent -> FX5-TCF, 31 lines
--   SPM1250P · 11 axis ·  6 track · 120 PPM · Continuous   -> IQR-TCF, 40 lines
--  with DF-1855 unchanged:
--   SPM1250P · 15 axis ·  9 track ·  80 PPM · Continuous   -> IQR-FLM, 42 lines
-- =====================================================================
