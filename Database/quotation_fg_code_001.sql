-- Finished goods code for the first (fixed) Annexure-IV line item of a sales quotation.
-- Run once per database. Error 1060 (duplicate column) on a re-run is safe to ignore.
ALTER TABLE quotation_annexture_4 ADD COLUMN fg_code VARCHAR(40) NULL DEFAULT NULL AFTER description;
