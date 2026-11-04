-- Attention person printed on the cover page of a sales quotation ("Specially prepared for ... MR. NAME").
-- Per quotation; blank means the customer's contact name is used. Run once per database;
-- error 1060 (duplicate column) on a re-run is safe to ignore.
ALTER TABLE quotation_customer_data ADD COLUMN attention_person VARCHAR(150) NULL DEFAULT NULL;
