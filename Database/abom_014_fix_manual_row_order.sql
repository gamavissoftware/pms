-- =====================================================================
--  abom_014_fix_manual_row_order.sql
--
--  Repairs hand-added BOM lines that were stored with section_order 99.
--
--  THE BUG
--  -------
--  Lines are read back ordered by section_order, then line_no. When a row
--  was added by hand the browser sent the section NAME but not its sort
--  position, and the insert fell back to 99 -- after every real section.
--
--  The row was placed correctly, saved, and then reappeared at the very
--  bottom of the sheet under a SECOND copy of its own section heading.
--  Its line_no was right the whole time, which is why the S.No looked
--  correct while the position did not.
--
--  Fixed in application/models/Abom_model.php (the order is now derived
--  from the BOM's own lines) and assets/abom/abom-lines.js (the browser
--  now sends it). This repairs the rows written before that.
--
--  WHAT IT TOUCHES
--  Only abom_bom_line rows that are ALL of:
--     is_manual_add = 1, section_order = 99, and in a section that some
--     other line of the SAME BOM already places.
--  Anything else is left exactly as it is -- including a genuine
--  section that really does belong last.
--
--  SAFE TO RE-RUN. Second run matches nothing.
-- =====================================================================

SET NAMES utf8mb4;

-- --- before -----------------------------------------------------------
SELECT COUNT(*) AS `rows_to_repair`
  FROM `abom_bom_line` l
 WHERE l.`is_manual_add` = 1
   AND l.`section_order` = 99
   AND EXISTS (SELECT 1 FROM (SELECT * FROM `abom_bom_line`) s
                WHERE s.`bom_id`        = l.`bom_id`
                  AND s.`section_name`  = l.`section_name`
                  AND s.`section_order` <> 99);

START TRANSACTION;

-- MIN() so a section whose other lines disagree resolves to the earliest
-- position rather than picking one arbitrarily.
UPDATE `abom_bom_line` l
  JOIN (
        SELECT `bom_id`, `section_name`, MIN(`section_order`) AS `real_order`
          FROM `abom_bom_line`
         WHERE `section_order` <> 99
         GROUP BY `bom_id`, `section_name`
       ) fix
    ON fix.`bom_id`       = l.`bom_id`
   AND fix.`section_name` = l.`section_name`
   SET l.`section_order`  = fix.`real_order`
 WHERE l.`is_manual_add` = 1
   AND l.`section_order` = 99;

COMMIT;

-- --- after ------------------------------------------------------------
SELECT COUNT(*) AS `rows_still_at_99_by_design`
  FROM `abom_bom_line`
 WHERE `is_manual_add` = 1 AND `section_order` = 99;

SELECT b.`bom_no`, l.`line_no`, l.`section_order`, l.`section_name`, l.`description`
  FROM `abom_bom_line` l
  JOIN `abom_bom` b ON b.`id` = l.`bom_id`
 WHERE l.`is_manual_add` = 1
 ORDER BY b.`id`, l.`section_order`, l.`line_no`;
