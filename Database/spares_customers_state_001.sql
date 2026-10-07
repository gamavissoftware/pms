-- Adds State capture to SPARES / SERVICE customers (SAP rejects Business Partners without a state).
-- Run once. If the column already exists MySQL returns error 1060 (Duplicate column name); that is safe to ignore.
ALTER TABLE spares_customers ADD COLUMN state_id INT NULL DEFAULT NULL AFTER country_id;
