-- Title 1 (generated) and Title 2 (editable, starts equal to Title 1) of the finished goods item on the first
-- Annexure-IV line. Run once per database; error 1060 (duplicate column) on a re-run is safe to ignore.
ALTER TABLE quotation_annexture_4 ADD COLUMN fg_title1 VARCHAR(255) NULL DEFAULT NULL AFTER fg_code;
ALTER TABLE quotation_annexture_4 ADD COLUMN fg_title2 VARCHAR(255) NULL DEFAULT NULL AFTER fg_title1;
