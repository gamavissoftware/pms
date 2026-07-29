-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — SCHEMA (install 001)
--  Target : MySQL 5.7+ / MariaDB 10.3+  (CodeIgniter 3 project)
--  Prefix : abom_          (project dbprefix is '', tables are bare)
--  Charset: utf8mb4        (source data contains ° Ω ⚠ — utf8mb3 is
--                           adequate for these BMP characters, but the
--                           tables are utf8mb4 to match the 167 existing
--                           utf8mb4 tables and to stay future-proof)
--
--  PURELY ADDITIVE. This script creates 11 new abom_* tables and does
--  not ALTER, DROP, RENAME or TRUNCATE anything outside that prefix,
--  and adds no foreign key to any pre-existing table.
--
--  NOTE ON COLLISION SAFETY
--  ------------------------
--  These are plain CREATE TABLE statements — deliberately NOT
--  "CREATE TABLE IF NOT EXISTS" and NOT preceded by "DROP TABLE IF
--  EXISTS" (which is what the handover file abom_schema.sql used).
--  If any abom_* table already exists this script will FAIL LOUDLY on
--  that statement rather than silently overwriting live data or
--  silently skipping. Verified against the 2026-07-09 dump: none of
--  the 293 existing tables begins with "abom".
--
--  Run once, then Database/abom_002_seed.sql.
-- =====================================================================
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. PLC FAMILY  (FX5 / iQ-R)
-- ---------------------------------------------------------------------
CREATE TABLE `abom_plc_family` (
  `id`            TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`          VARCHAR(16)  NOT NULL,
  `name`          VARCHAR(64)  NOT NULL,
  `cpu_summary`   VARCHAR(191) NULL,
  `default_j4_units`  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `default_battery`   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `default_panel_location` VARCHAR(64) NULL,
  `sort_order`    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_family_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. SECTION  (BOM grouping headers, scoped to a family)
-- ---------------------------------------------------------------------
CREATE TABLE `abom_section` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `plc_family_id` TINYINT UNSIGNED NOT NULL,
  `code`          VARCHAR(8)   NOT NULL,
  `name`          VARCHAR(128) NOT NULL,
  `sort_order`    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_section` (`plc_family_id`,`code`),
  CONSTRAINT `fk_abom_section_family` FOREIGN KEY (`plc_family_id`)
     REFERENCES `abom_plc_family`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. QUANTITY FORMULA  (lookup — Bom_engine dispatches on `code`)
-- ---------------------------------------------------------------------
CREATE TABLE `abom_formula` (
  `code`         VARCHAR(16)  NOT NULL,
  `name`         VARCHAR(64)  NOT NULL,
  `description`  VARCHAR(255) NOT NULL,
  `needs_review` TINYINT(1)   NOT NULL DEFAULT 0,
  `sort_order`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. FEATURE  (optional machine options that gate items in/out)
-- ---------------------------------------------------------------------
CREATE TABLE `abom_feature` (
  `code`        VARCHAR(32)  NOT NULL,
  `label`       VARCHAR(128) NOT NULL,
  `help_text`   VARCHAR(255) NULL,
  `default_on`  TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. MASTER ITEM  (the 71 reference line items)
-- ---------------------------------------------------------------------
CREATE TABLE `abom_item` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `erp_code`       VARCHAR(32)  NULL,          -- NULL = ERP code not yet created
  `description`    VARCHAR(255) NOT NULL,
  `part_no`        VARCHAR(96)  NOT NULL,
  `manufacturer`   VARCHAR(64)  NOT NULL DEFAULT 'MITSUBISHI',
  `base_qty`       SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `formula_code`   VARCHAR(16)  NOT NULL DEFAULT 'FIXED',
  `plc_family_id`  TINYINT UNSIGNED NOT NULL,
  `section_id`     SMALLINT UNSIGNED NOT NULL,
  `is_optional`    TINYINT(1)   NOT NULL DEFAULT 0,
  `feature_code`   VARCHAR(32)  NULL,          -- gate: item appears only if feature is ON
  `usage_remark`   VARCHAR(255) NULL,          -- verbatim from source BOM — never normalise
  `data_issue`     VARCHAR(255) NULL,          -- free-text engineering note / data problem
  `issue_severity` ENUM('none','review','no_erp','conflict') NOT NULL DEFAULT 'none',
  `panel_location` VARCHAR(64)  NULL,
  `uom`            VARCHAR(16)  NOT NULL DEFAULT 'NOS',
  `source_df`      VARCHAR(32)  NULL,          -- DF-1826 / DF-1827 provenance
  `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`     DATETIME NULL,
  `updated_at`     DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `ix_abom_item_family`  (`plc_family_id`,`section_id`),
  KEY `ix_abom_item_erp`     (`erp_code`),
  KEY `ix_abom_item_part`    (`part_no`),
  KEY `ix_abom_item_formula` (`formula_code`),
  CONSTRAINT `fk_abom_item_family`  FOREIGN KEY (`plc_family_id`) REFERENCES `abom_plc_family`(`id`),
  CONSTRAINT `fk_abom_item_section` FOREIGN KEY (`section_id`)    REFERENCES `abom_section`(`id`),
  CONSTRAINT `fk_abom_item_formula` FOREIGN KEY (`formula_code`)  REFERENCES `abom_formula`(`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. PLC SELECTION RULE  (data-driven, so engineering can tune
--    thresholds without a code deploy). Priority ASC, first match wins.
-- ---------------------------------------------------------------------
CREATE TABLE `abom_plc_rule` (
  `id`            SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `priority`      SMALLINT UNSIGNED NOT NULL,
  `min_axes`      SMALLINT NULL,
  `max_axes`      SMALLINT NULL,
  `min_speed`     SMALLINT NULL,
  `max_speed`     SMALLINT NULL,
  `motion_type`   ENUM('Intermittent','Continuous','ANY') NOT NULL DEFAULT 'ANY',
  `result_family_id` TINYINT UNSIGNED NOT NULL,
  `explanation`   VARCHAR(255) NOT NULL,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `ix_abom_rule_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. BOM HEADER  (one generated BOM document)
--
--    bom_no uses its own ABOM-nnn series (config abom_bom_no_format).
--    df_ref is free text holding the SOURCE DF number (DF-1826 /
--    DF-1827) so a BOM can reference the existing DF register without
--    minting into, or owning, that namespace.
-- ---------------------------------------------------------------------
CREATE TABLE `abom_bom` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bom_no`         VARCHAR(32)  NOT NULL,      -- e.g. ABOM-1
  `revision`       VARCHAR(8)   NOT NULL DEFAULT '00',
  `df_ref`         VARCHAR(32)  NULL COMMENT 'Optional link to existing DF register',
  `machine_model`  VARCHAR(32)  NOT NULL,      -- SPM1200L / SPM1250P
  `machine_side`   ENUM('LHS','RHS','N/A') NOT NULL DEFAULT 'N/A',
  `axes`           SMALLINT UNSIGNED NOT NULL,
  `tracks`         SMALLINT UNSIGNED NOT NULL,
  `speed_ppm`      SMALLINT UNSIGNED NOT NULL,
  `motion_type`    ENUM('Intermittent','Continuous') NOT NULL,
  `plc_family_id`  TINYINT UNSIGNED NOT NULL,
  `plc_family_locked` TINYINT(1) NOT NULL DEFAULT 0, -- 1 = engineer overrode auto-detect
  `j4_units`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `battery_qty`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `features_json`  TEXT NULL,                  -- {"feat_perf":1,"feat_brake":0,...}
  `status`         ENUM('draft','submitted','checked','eng_approved','approved','rejected','superseded')
                   NOT NULL DEFAULT 'draft',
  `prepared_by`    INT UNSIGNED NULL,
  `prepared_at`    DATETIME NULL,
  `checked_by`     INT UNSIGNED NULL,
  `checked_at`     DATETIME NULL,
  `eng_approved_by` INT UNSIGNED NULL,
  `eng_approved_at` DATETIME NULL,
  `proc_approved_by` INT UNSIGNED NULL,
  `proc_approved_at` DATETIME NULL,
  `total_lines`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `total_qty`      INT UNSIGNED NOT NULL DEFAULT 0,
  `open_issues`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `notes`          TEXT NULL,
  `created_by`     INT UNSIGNED NULL,
  `created_at`     DATETIME NULL,
  `updated_at`     DATETIME NULL,
  `deleted_at`     DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_abom_bom_rev` (`bom_no`,`revision`),
  KEY `ix_abom_bom_status` (`status`),
  KEY `ix_abom_bom_df_ref` (`df_ref`),
  CONSTRAINT `fk_abom_bom_family` FOREIGN KEY (`plc_family_id`) REFERENCES `abom_plc_family`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. BOM LINE  (frozen snapshot — master item edits must NEVER mutate
--    an approved BOM, so every descriptive field is copied, not joined)
-- ---------------------------------------------------------------------
CREATE TABLE `abom_bom_line` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bom_id`        INT UNSIGNED NOT NULL,
  `line_no`       SMALLINT UNSIGNED NOT NULL,
  `item_id`       INT UNSIGNED NULL,           -- provenance only, never joined for display
  `section_name`  VARCHAR(128) NOT NULL,
  `section_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `erp_code`      VARCHAR(32)  NULL,
  `description`   VARCHAR(255) NOT NULL,
  `part_no`       VARCHAR(96)  NOT NULL,
  `manufacturer`  VARCHAR(64)  NOT NULL,
  `qty`           SMALLINT UNSIGNED NOT NULL,
  `computed_qty`  SMALLINT UNSIGNED NOT NULL,  -- what the engine produced
  `is_overridden` TINYINT(1)   NOT NULL DEFAULT 0,
  `override_reason` VARCHAR(255) NULL,
  `uom`           VARCHAR(16)  NOT NULL DEFAULT 'NOS',
  `formula_code`  VARCHAR(16)  NOT NULL,
  `usage_remark`  VARCHAR(255) NULL,
  `remarks`       VARCHAR(255) NULL,
  `issue_severity` ENUM('none','review','no_erp','conflict') NOT NULL DEFAULT 'none',
  `is_optional`   TINYINT(1)   NOT NULL DEFAULT 0,
  `feature_code`  VARCHAR(32)  NULL,
  `panel_location` VARCHAR(64) NULL,
  `is_manual_add` TINYINT(1)   NOT NULL DEFAULT 0, -- engineer added, not from master
  `is_confirmed`  TINYINT(1)   NOT NULL DEFAULT 0, -- MANUAL line confirmed by engineer
  `is_acknowledged` TINYINT(1) NOT NULL DEFAULT 0, -- conflict line acknowledged
  `ack_comment`   VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  KEY `ix_abom_line_bom` (`bom_id`,`line_no`),
  CONSTRAINT `fk_abom_line_bom` FOREIGN KEY (`bom_id`) REFERENCES `abom_bom`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. BOM REVISION SNAPSHOT  (immutable JSON of header+lines at each
--    approval transition — what audit/quality will ask for)
-- ---------------------------------------------------------------------
CREATE TABLE `abom_bom_revision` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bom_id`       INT UNSIGNED NOT NULL,
  `revision`     VARCHAR(8)   NOT NULL,
  `snapshot`     LONGTEXT     NOT NULL,
  `change_note`  VARCHAR(255) NULL,
  `created_by`   INT UNSIGNED NULL,
  `created_at`   DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `ix_abom_rev_bom` (`bom_id`),
  CONSTRAINT `fk_abom_rev_bom` FOREIGN KEY (`bom_id`) REFERENCES `abom_bom`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. APPROVAL TRAIL
-- ---------------------------------------------------------------------
CREATE TABLE `abom_bom_approval` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bom_id`    INT UNSIGNED NOT NULL,
  `stage`     ENUM('prepare','check','eng_approve','proc_approve') NOT NULL,
  `action`    ENUM('submit','approve','reject','reopen') NOT NULL,
  `user_id`   INT UNSIGNED NULL,
  `user_name` VARCHAR(128) NULL,
  `comment`   VARCHAR(500) NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `ix_abom_appr_bom` (`bom_id`),
  CONSTRAINT `fk_abom_appr_bom` FOREIGN KEY (`bom_id`) REFERENCES `abom_bom`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 11. AUDIT LOG  (master-data changes — "who changed this ERP code")
-- ---------------------------------------------------------------------
CREATE TABLE `abom_audit_log` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity`      VARCHAR(64) NOT NULL,
  `entity_id`   INT UNSIGNED NOT NULL,
  `action`      VARCHAR(32) NOT NULL,
  `old_values`  TEXT NULL,
  `new_values`  TEXT NULL,
  `user_id`     INT UNSIGNED NULL,
  `ip_address`  VARCHAR(45) NULL,
  `created_at`  DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `ix_abom_audit_entity` (`entity`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
