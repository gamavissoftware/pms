-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — SEED DATA (install 002)
--
--  Copied VERBATIM from BOMMODULEDEVELOPMENT/abom_seed.sql. The 71
--  master items were extracted programmatically from the two source
--  BOMs and verified against them — they are not re-keyed here.
--
--  Run AFTER Database/abom_001.sql.
--
--  The flagged data-quality issues in this data are DELIBERATE and must
--  not be "fixed": ERP 4060431 on two different parts (ids 59 and 66),
--  five items with no ERP code (3, 18, 41, 55, 58), and the DF-1826
--  battery-vs-J4 count mismatch (id 70). Engineering resolves these
--  through the module UI, with an audit trail.
--
--  TRUNCATE below touches abom_* tables ONLY. Nothing outside the
--  abom_ prefix is read or written by this script.
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `abom_item`;
TRUNCATE TABLE `abom_section`;
TRUNCATE TABLE `abom_plc_rule`;
TRUNCATE TABLE `abom_feature`;
TRUNCATE TABLE `abom_formula`;
TRUNCATE TABLE `abom_plc_family`;

-- --- PLC families ----------------------------------------------------
INSERT INTO `abom_plc_family`
(`id`,`code`,`name`,`cpu_summary`,`default_j4_units`,`default_battery`,`default_panel_location`,`sort_order`) VALUES
(1,'FX5','FX5 Series','FX5U-80MT/ESS + FX5-80SSC-S (8-axis simple motion)',7,7,'Panel With Machine',1),
(2,'iQ-R','iQ-R Series','R16MTCPU + R04ENCPU + R312B (16-axis motion controller)',11,12,'Standalone',2);

-- --- Quantity formulas ----------------------------------------------
INSERT INTO `abom_formula` (`code`,`name`,`description`,`needs_review`,`sort_order`) VALUES
('FIXED','Fixed quantity','Always the stored base quantity, independent of configuration.',0,1),
('AXES','Per axis','Quantity = total servo axis count.',0,2),
('AXES_MINUS_1','Axes minus one','Quantity = axes - 1. Inter-amplifier SSCNET daisy-chain links (FX5).',0,3),
('J4_STO','Per MR-J4 unit','Quantity = number of MR-J4 amplifier units. MR-JE amplifiers are EXCLUDED; one MR-J4W2 dual-axis amplifier counts as ONE unit.',0,4),
('BATTERY','Per servo battery','Quantity = servo battery count. Deliberately independent of J4_STO: DF-1826 shows STO=11 but Battery=12.',0,5),
('MANUAL','Manual review','Base quantity is the reference-BOM value. Engineer MUST confirm before approval.',1,6);

-- --- Optional feature gates -----------------------------------------
INSERT INTO `abom_feature` (`code`,`label`,`help_text`,`default_on`,`sort_order`) VALUES
('feat_perf','Perforation Axis','Adds MR-JE-70B amplifier, HG-KN73JK motor and their encoder/power cables (FX5).',1,1),
('feat_brake','Electromagnetic Brake','Adds MR-BKCNS1 brake connector set (iQ-R, 7KW up/down assembly).',1,2),
('feat_dbr','Dynamic Braking Resistor','Adds RECKON 6.7 Ohm 500W DBR for regeneration (iQ-R).',1,3),
('feat_imark','I-Mark Sensor','Adds MR-CCN1 amplifier I/O connector (iQ-R).',1,4);
INSERT INTO `abom_section` (`id`,`plc_family_id`,`code`,`name`,`sort_order`) VALUES
(1,1,'F1','PLC, I/O\'s, HMI, RTD, VFD',1),
(2,1,'F2','Servo Amplifiers & Motors',2),
(3,1,'F3','Servo Cables',3),
(4,2,'R1','PLC, I/O\'s, HMI, RTD, VFD',1),
(5,2,'R2','Servo Amplifiers & Motors',2),
(6,2,'R3','Servo Encoder Cables',3),
(7,2,'R4','Servo Power Connectors & Cables',4),
(8,2,'R5','SSCNET Cables & Servo Battery',5);
-- --- PLC auto-selection rules (first match by priority wins) ---------
-- Reference logic:
--   iQ-R if  axes >= 10  OR  speed >= 160 PPM  OR (motion = Continuous AND axes >= 12)
--   otherwise FX5
INSERT INTO `abom_plc_rule`
(`id`,`priority`,`min_axes`,`max_axes`,`min_speed`,`max_speed`,`motion_type`,`result_family_id`,`explanation`,`is_active`) VALUES
(1,10,10,NULL,NULL,NULL,'ANY',2,'Axis count 10 or more exceeds the FX5-80SSC-S 8-axis simple motion limit.',1),
(2,20,NULL,NULL,160,NULL,'ANY',2,'Line speed 160 PPM or above requires the iQ-R motion controller scan rate.',1),
(3,30,12,NULL,NULL,NULL,'Continuous',2,'Continuous motion with 12 or more axes requires iQ-R synchronised control.',1),
(4,99,NULL,NULL,NULL,NULL,'ANY',1,'Default: FX5 Series covers up to 9 axes, up to 140 PPM, intermittent motion.',1);


-- --- 71 master items -------------------------------------------------
INSERT INTO `abom_item`
(`id`,`erp_code`,`description`,`part_no`,`manufacturer`,`base_qty`,`formula_code`,`plc_family_id`,`section_id`,`is_optional`,`feature_code`,`usage_remark`,`data_issue`,`issue_severity`,`panel_location`,`uom`,`source_df`,`is_active`) VALUES
(1,'4060524','PLC (40 SINK/SOURCE DI, 40 SOURCE DO)','FX5U-80MT/ESS','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(2,'4130021','PLC BATTERY','FX3U-32BL','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(3,NULL,'16 SOURCE DI CARD','FX5-16EX/ES','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,'NO ERP CODE — raise procurement request','no_erp','Panel With Machine','NOS','DF-1827',1),
(4,'2240029','8 SOURCE DI, 8 SOURCE DO CARD','FX5-16ET/ESS','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(5,'4100057','POWER SUPPLY','FX5-1PSU-5V','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(6,'2050435','4 CH. ANALOG OUTPUT CARD','FX5-4DA-ADP','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(7,'2020122','4 CH. TEMPERATURE CARD','FX5-4LC','MITSUBISHI',7,'MANUAL',1,1,0,NULL,NULL,'Base qty 7 for 12 tracks — review per machine track count','review','Panel With Machine','NOS','DF-1827',1),
(8,'2240009','8 AXIS SIMPLE MOTION CARD','FX5-80SSC-S','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(9,'4100058','CONVERTER MODULE','FX5-CNV-BC','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(10,'2030464','65CM CABLE','FX5-65EC','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(11,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',2,'MANUAL',1,1,0,NULL,'UNWIND + COLLATING CONV.','Base qty 2 — review per machine configuration','review','Panel With Machine','NOS','DF-1827',1),
(12,'4060513','10 INCH HMI','GS2110-WTBD','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(13,'2110146','CONNECTOR (FOR MOTION CARD)','LD77MHIOCON','MITSUBISHI',1,'FIXED',1,1,0,NULL,NULL,NULL,'none','Panel With Machine','NOS','DF-1827',1),
(14,'2200006','SAFE TORQUE OFF CONNECTOR','MR-D05ULD3M-B','MITSUBISHI',7,'J4_STO',1,1,0,NULL,NULL,'Qty = MR-J4 amp units (excl. MR-JE). Use \'J4 Units\' control.','review','Panel With Machine','NOS','DF-1827',1),
(15,'2160574','SERVO AMPLIFIER 3.5KW WITH SSCNET','MR-J4-350B','MITSUBISHI',4,'MANUAL',1,2,0,NULL,NULL,'Base qty 4 — review per machine config','review','Panel With Machine','NOS','DF-1827',1),
(16,'4060510','SERVO AMPLIFIER 2KW WITH SSCNET','MR-J4-200B','MITSUBISHI',3,'MANUAL',1,2,0,NULL,NULL,'Base qty 3 — review per machine config','review','Panel With Machine','NOS','DF-1827',1),
(17,'4060400','SERVO MOTOR 10.5Nm, 3.5KW, 3000RPM, W/O KEYWAY','HG-JR353','MITSUBISHI',4,'MANUAL',1,2,0,NULL,'HORZ. + VERT. + FILLING PISTON (L) + FILLING PISTON (R)',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(18,NULL,'SERVO MOTOR 7.2Nm, 1.5KW, 2000RPM','HG-SR152K','MITSUBISHI',1,'MANUAL',1,2,0,NULL,'PULLING','NEW PART — NO ERP CODE. Raise new item creation request.','no_erp','Panel With Machine','NOS','DF-1827',1),
(19,'4060491','SERVO MOTOR 6.4Nm, 2KW, 3000RPM, W/O KEYWAY','HG-JR203','MITSUBISHI',2,'MANUAL',1,2,0,NULL,'ROTARY VALVE',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(20,'4120007','0.7KW SERVO AMPLIFIER — PERFORATION AXIS','MR-JE-70B','MITSUBISHI',1,'FIXED',1,2,1,'feat_perf','PERFORATION',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(21,'4120009','0.7KW SERVO MOTOR, 3000RPM, 2.4Nm, W/ KEYWAY','HG-KN73JK','MITSUBISHI',1,'FIXED',1,2,1,'feat_perf','PERFORATION',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(22,'2030328','ENCODER CABLE HG-JR/SR, 1KW–3.5KW, 5 MTR','MR-J3ENSCBL5M-L','MITSUBISHI',7,'MANUAL',1,3,0,NULL,NULL,'Base qty 7 for 8-axis machine — adjust per axis count and routing','review','Panel With Machine','NOS','DF-1827',1),
(23,'2030449','ENCODER CABLE HG-SN/JR, 0.75KW, 5 MTR','MR-J3ENCBL5M-A2-L','MITSUBISHI',1,'FIXED',1,3,1,'feat_perf','PERFORATION',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(24,'4060594','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS1CBL03M-A2-L','MITSUBISHI',1,'FIXED',1,3,1,'feat_perf','PERFORATION',NULL,'none','Panel With Machine','NOS','DF-1827',1),
(25,'2110119','POWER CABLE CONNECTOR FOR 2KW–5KW MOTORS','MS3106F2222S+TB','MITSUBISHI',4,'MANUAL',1,3,0,NULL,NULL,'Base qty 4 — adjust per count of 2KW–5KW motors','review','Panel With Machine','NOS','DF-1827',1),
(26,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',3,'MANUAL',1,3,0,NULL,NULL,'Base qty 3 — adjust per count of 1KW–1.5KW motors','review','Panel With Machine','NOS','DF-1827',1),
(27,'2030329','SSCNET CABLE, 0.5 METRES','MR-J3-BUS05M','MITSUBISHI',7,'AXES_MINUS_1',1,3,0,NULL,NULL,'Qty = Total Axes − 1 (inter-amp connections for FX5)','review','Panel With Machine','NOS','DF-1827',1),
(28,'4060430','SSCNET CABLE, 5 METRES','MR-J3-BUS5M-A','MITSUBISHI',1,'FIXED',1,3,0,NULL,NULL,'Controller → first amplifier','review','Panel With Machine','NOS','DF-1827',1),
(29,'2050390','BATTERY SET FOR MR-J4 SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',7,'BATTERY',1,3,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). DF-1827 ref = 7.','review','Panel With Machine','NOS','DF-1827',1),
(30,'4010222','16 AXIS MOTION CONTROLLER','R16MTCPU','MITSUBISHI',1,'FIXED',2,4,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(31,'4010223','iQ-R PLC, 40K STEP','R04ENCPU','MITSUBISHI',1,'FIXED',2,4,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(32,'4100055','CPU POWER SUPPLY','R61P','MITSUBISHI',1,'FIXED',2,4,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(33,'4010224','12 SLOT CHASSIS','R312B','MITSUBISHI',1,'FIXED',2,4,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(34,'4010225','4 CH. RTD INPUT CARD','R60TCRT4','MITSUBISHI',8,'MANUAL',2,4,0,NULL,NULL,'Base qty 8 for 12 tracks — review per machine track count','review','Standalone','NOS','DF-1826',1),
(35,'4010226','MODULAR IO HEADER MODULE','M-CCB-H','MITSUBISHI',1,'FIXED',2,4,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(36,'4010227','16 CH. DI CARD','M-16D','MITSUBISHI',5,'MANUAL',2,4,0,NULL,NULL,'Base qty 5 for 15A/12T — review per machine I/O count','review','Standalone','NOS','DF-1826',1),
(37,'4010228','16 CH. DO CARD','M-16TE','MITSUBISHI',3,'MANUAL',2,4,0,NULL,NULL,'Base qty 3 for 15A/12T — review per machine I/O count','review','Standalone','NOS','DF-1826',1),
(38,'4010229','2 CH. ANALOG OUTPUT CARD','M-DA2','MITSUBISHI',2,'MANUAL',2,4,0,NULL,NULL,'Base qty 2 — review per analog output requirements','review','Standalone','NOS','DF-1826',1),
(39,'4060518','4 CH. ANALOG INPUT CARD','M-AD4','MITSUBISHI',1,'FIXED',2,4,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(40,'4010230','BUS END','M-BE','MITSUBISHI',1,'FIXED',2,4,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(41,NULL,'HMI 15 INCH','GT3715-FHCBD','MITSUBISHI',1,'FIXED',2,4,0,NULL,NULL,'NEW PART — NO ERP CODE. Raise new item creation request.','no_erp','Standalone','NOS','DF-1826',1),
(42,'2040140','VFD — 1HP','FR-D740-022-E16','MITSUBISHI',4,'MANUAL',2,4,0,NULL,'COLLATING (1st STAGE) + CASE PACKER CONV. + TRANSFER CONV. + UNWIND','Base qty 4 — review per machine configuration','review','Standalone','NOS','DF-1826',1),
(43,'2110113','AMPLIFIER I/O CONNECTOR','MR-CCN1','MITSUBISHI',1,'FIXED',2,4,1,'feat_imark','FOR I-MARK SENSOR',NULL,'none','Standalone','NOS','DF-1826',1),
(44,'2200006','SAFE TORQUE OFF CONNECTOR','MR-D05ULD3M-B','MITSUBISHI',11,'J4_STO',2,4,0,NULL,NULL,'Qty = MR-J4 amp units. Use \'J4 Units\' control.','review','Standalone','NOS','DF-1826',1),
(45,'4060660','SERVO AMPLIFIER 7KW WITH SSCNET & ELECTROMAGNETIC BRAKE','MR-J4-700B','MITSUBISHI',1,'FIXED',2,5,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1826',1),
(46,'4060583','SERVO AMPLIFIER 5KW WITH SSCNET','MR-J4-500B','MITSUBISHI',2,'FIXED',2,5,0,NULL,'HORZ. + VERT.',NULL,'none','Standalone','NOS','DF-1826',1),
(47,'2160574','SERVO AMPLIFIER 3.5KW WITH SSCNET','MR-J4-350B','MITSUBISHI',2,'MANUAL',2,5,0,NULL,NULL,'Base qty 2 for Filling Pistons — review per config','review','Standalone','NOS','DF-1826',1),
(48,'4060510','SERVO AMPLIFIER 2KW WITH SSCNET','MR-J4-200B','MITSUBISHI',3,'MANUAL',2,5,0,NULL,NULL,'Base qty 3 — review per config','review','Standalone','NOS','DF-1826',1),
(49,'4060432','SERVO AMPLIFIER 0.75KW DUAL AXIS WITH SSCNET','MR-J4W2-77B','MITSUBISHI',3,'MANUAL',2,5,0,NULL,NULL,'Base qty 3 units = 6 axes (dual-axis amp). Review per config','review','Standalone','NOS','DF-1826',1),
(50,'4060661','SERVO MOTOR 33.4Nm, 7KW, 2000RPM','HG-SR702B','MITSUBISHI',1,'FIXED',2,5,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1826',1),
(51,'4060662','SERVO MOTOR 15.9Nm, 5KW, 3000RPM','HG-JR503','MITSUBISHI',2,'FIXED',2,5,0,NULL,'HORZ. + VERT.',NULL,'none','Standalone','NOS','DF-1826',1),
(52,'4060400','SERVO MOTOR 10.5Nm, 3.5KW, 3000RPM, W/O KEYWAY','HG-JR353','MITSUBISHI',2,'MANUAL',2,5,0,NULL,'FILLING PISTON (2 NOS.)','Review per config','review','Standalone','NOS','DF-1826',1),
(53,'4060491','SERVO MOTOR 6.4Nm, 2KW, 3000RPM, W/O KEYWAY','HG-JR203','MITSUBISHI',1,'FIXED',2,5,0,NULL,'ROTARY VALVE',NULL,'none','Standalone','NOS','DF-1826',1),
(54,'4060663','SERVO MOTOR 9.55Nm, 2KW, 2000RPM','HG-SR202','MITSUBISHI',1,'FIXED',2,5,0,NULL,'PULLING',NULL,'none','Standalone','NOS','DF-1826',1),
(55,NULL,'SERVO MOTOR 4.8Nm, 1.5KW, 3000RPM','HG-JR153','MITSUBISHI',1,'FIXED',2,5,0,NULL,'COLLATING (2nd STAGE)','NO ERP CODE — raise procurement request','no_erp','Standalone','NOS','DF-1826',1),
(56,'2050266','SERVO MOTOR 2.4Nm, 0.7KW, 3000RPM, W/O KEYWAY','HG-KR73','MITSUBISHI',6,'MANUAL',2,5,0,NULL,'CODING + PERFORATION + CASE PACKER FLAPPER (3 NOS.) + SHUT OFF NOZZLE','Review per config','review','Standalone','NOS','DF-1826',1),
(57,'2030373','ENCODER CABLE HG-JR/SR, 1KW–7KW, 10 MTR','MR-J3ENSCBL10M-L','MITSUBISHI',4,'MANUAL',2,6,0,NULL,'UP/DWN ASSEMBLY + FILLING PISTON (2 NOS.) + ROTARY VALVE','Review per cable routing','review','Standalone','NOS','DF-1826',1),
(58,NULL,'ENCODER CABLE HG-JR/SR, 1KW–7KW, 20 MTR','MR-J3ENSCBL20M-L','MITSUBISHI',4,'MANUAL',2,6,0,NULL,'COLLATING (2nd STAGE) + HORZ. + VERT. + PULLING','NO ERP CODE — raise procurement request','no_erp','Standalone','NOS','DF-1826',1),
(59,'4060431','ENCODER CABLE HG-SN/JR, 0.75KW, 0.3 MTR','MR-J3JCBL03M-A2-L','MITSUBISHI',5,'MANUAL',2,6,0,NULL,'CASE PACKER FLAPPER (3 NOS.) + PERFORATION + SHUT OFF NOZZLE','⚠ ERP 4060431 CONFLICT — also used for Power Cable MR-PWS2CBL03M-A2-L. Data error — verify both ERPs before ordering.','conflict','Standalone','NOS','DF-1826',1),
(60,'2030772','ENCODER CABLE HG-SN/JR, 0.75KW, 20 MTR','MR-EKCBL20M-L','MITSUBISHI',5,'MANUAL',2,6,0,NULL,NULL,'Review per cable routing','review','Standalone','NOS','DF-1826',1),
(61,'2030449','ENCODER CABLE HG-SN/JR, 0.75KW, 10 MTR','MR-J3ENCBL10M-A2-L','MITSUBISHI',1,'FIXED',2,6,0,NULL,'CODING',NULL,'none','Standalone','NOS','DF-1826',1),
(62,'2110162','POWER CONNECTOR FOR 7KW SERVO MOTOR','MS3106F3217S+TB','MITSUBISHI',1,'FIXED',2,7,0,NULL,'UP/DWN ASSEMBLY',NULL,'none','Standalone','NOS','DF-1826',1),
(63,'2110163','ELECTROMAGNETIC BRAKE CONNECTOR SET','MR-BKCNS1','MITSUBISHI',1,'FIXED',2,7,1,'feat_brake',NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(64,'2110119','POWER CABLE CONNECTOR FOR 2KW–5KW MOTORS','MS3106F2222S+TB','MITSUBISHI',6,'MANUAL',2,7,0,NULL,'PULLING + ROTARY VALVE + FILLING PISTON (2 NOS.) + HORZ. + VERT.','Review per motor count','review','Standalone','NOS','DF-1826',1),
(65,'2050389','POWER CABLE CONNECTOR FOR 1KW–1.5KW MOTORS','MS3106F1810S+TB','MITSUBISHI',1,'MANUAL',2,7,0,NULL,'COLLATING (2nd STAGE)','Review per motor count','review','Standalone','NOS','DF-1826',1),
(66,'4060431','POWER CABLE 0.3 MTR FOR 0.7KW MOTOR','MR-PWS2CBL03M-A2-L','MITSUBISHI',6,'MANUAL',2,7,0,NULL,'CODING + PERFORATION + CASE PACKER FLAPPER (3 NOS.) + SHUT OFF NOZZLE','⚠ ERP 4060431 CONFLICT — also used for Encoder Cable MR-J3JCBL03M-A2-L. Data error — verify both ERPs before ordering.','conflict','Standalone','NOS','DF-1826',1),
(67,'4060430','SSCNET CABLE, 5 METRES','MR-J3BUS5M-A','MITSUBISHI',1,'FIXED',2,8,0,NULL,NULL,'Controller → first amplifier','review','Standalone','NOS','DF-1826',1),
(68,'2030505','SSCNET CABLE, 3 METRES','MR-J3BUS3M','MITSUBISHI',2,'FIXED',2,8,0,NULL,NULL,NULL,'none','Standalone','NOS','DF-1826',1),
(69,'2030364','SSCNET CABLE, 1 METRE','MR-J3BUS1M','MITSUBISHI',10,'MANUAL',2,8,0,NULL,NULL,'Base qty 10 for 15-axis machine — review per amplifier layout topology','review','Standalone','NOS','DF-1826',1),
(70,'2050390','BATTERY SET FOR MR-J4 SERVO AMPLIFIER','MR-BAT6V1SET','MITSUBISHI',12,'BATTERY',2,8,0,NULL,NULL,'Qty = Servo Battery Count (sidebar). ⚠ DF-1826 shows 12, J4 unit count = 11 — verify with engineering.','review','Standalone','NOS','DF-1826',1),
(71,'2050599','DYNAMIC BRAKING RESISTOR (DBR), 6.7Ω, 500W','—','RECKON',3,'MANUAL',2,8,1,'feat_dbr','FOR REGENERATION: UP/DWN ASSEMBLY + HORZ. + VERT.',NULL,'none','Standalone','NOS','DF-1826',1);

SET FOREIGN_KEY_CHECKS = 1;
