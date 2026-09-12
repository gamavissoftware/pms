-- =====================================================================
--  AUTOMATION BOM GENERATOR MODULE — PER-BOM PROVENANCE (install 010)
--
--  Run AFTER Database/abom_009_seed_1250p.sql.
--
--  Touches ONLY abom_item.source_df on the 31 items of the FX5-JE build,
--  plus one incorrect note. No row is inserted or deleted, no other
--  column is written, and nothing that affects GENERATION changes: what
--  each BOM contains and what quantity it gets are untouched.
--
--  WHY
--  ---
--  FX5-JE is one build assembled from FIVE uploaded BOMs — DF-1723,
--  DF-1778, DF-1808, DF-1858 and DF-1864. They are the same machine
--  specification at different sizes (5 to 7 axes, 6 to 12 tracks, 70 to
--  100 PPM) and share one parts catalogue; the quantities that differ
--  between them are CALCULATED, which is the reason the module exists.
--
--  What that cost was provenance. The register said "5 source BOMs" and
--  every item claimed a single representative DF, so there was no way to
--  answer "what did DF-1808 actually contain" or "which sheets use this
--  part". Both are now on the master sheet, as a column and a filter.
--
--  HOW THESE VALUES WERE DERIVED
--  -----------------------------
--  Not by hand. Every part number in the five source workbooks was read
--  and matched against the 31 catalogue items, comparing on letters and
--  digits only and allowing a prefix match — because the same part is
--  printed with different suffixes across sheets:
--
--      FX5-65EC          vs  FX5-65ECA            (DF-1808, DF-1864)
--      FR-D740-022-E16   vs  FR-D740-022-E16/EC   (DF-1858)
--
--  An exact compare called both of those "not on that sheet", which is
--  worse than no answer: it is confidently wrong. All 31 items matched.
--
--  The DBR is matched on description: it carries no part number on any
--  sheet, only the RECKON maker name.
--
--  ONE CORRECTION
--  --------------
--  Item 2 (PLC BATTERY, FX3U-32BL) carried the note "Not listed on
--  DF-1858". That is wrong — DF-1858 lists it at line 2. The note came
--  from a hand reading of the sheets; the mechanical match above found
--  it on all five. The note is cleared and the severity dropped to
--  'none' to match.
-- =====================================================================
SET NAMES utf8mb4;

START TRANSACTION;

-- --- per-item provenance, FX5-JE (variant 1) -------------------------
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 1;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 2;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1864' WHERE `id` = 3;
UPDATE `abom_item` SET `source_df` = 'DF-1858' WHERE `id` = 4;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1864' WHERE `id` = 5;
UPDATE `abom_item` SET `source_df` = 'DF-1778, DF-1858' WHERE `id` = 6;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 7;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1864' WHERE `id` = 8;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1864' WHERE `id` = 9;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1864' WHERE `id` = 10;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1864' WHERE `id` = 11;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 12;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 13;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 14;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1864' WHERE `id` = 15;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 16;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 17;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 18;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858' WHERE `id` = 19;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 20;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 21;
UPDATE `abom_item` SET `source_df` = 'DF-1778, DF-1808, DF-1864' WHERE `id` = 22;
UPDATE `abom_item` SET `source_df` = 'DF-1778, DF-1808, DF-1864' WHERE `id` = 23;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 24;
UPDATE `abom_item` SET `source_df` = 'DF-1778, DF-1808, DF-1864' WHERE `id` = 25;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 26;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 27;
UPDATE `abom_item` SET `source_df` = 'DF-1808, DF-1864' WHERE `id` = 28;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 29;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 30;
UPDATE `abom_item` SET `source_df` = 'DF-1723, DF-1778, DF-1808, DF-1858, DF-1864' WHERE `id` = 31;
-- --- correction: the PLC battery IS on DF-1858 -----------------------
UPDATE `abom_item`
   SET `data_issue`     = NULL,
       `issue_severity` = 'none'
 WHERE `id` = 2
   AND `data_issue` LIKE 'Not listed on DF-1858%';

COMMIT;

-- =====================================================================
--  POST-INSTALL CHECK
--
--   SELECT source_df, COUNT(*) FROM abom_item
--    WHERE variant_id = 1 GROUP BY source_df ORDER BY COUNT(*) DESC;
--
--  Expect 31 items spread across several combinations, the largest
--  being all five DFs. Every one of the other builds still carries its
--  single source DF, untouched.
--
--   SELECT COUNT(*) FROM abom_item WHERE source_df IS NULL;   -- 0
-- =====================================================================
