-- Log of sales quotations sent to SAP (Quotations API). One row per PMS quotation record (quotation_customer_data.id);
-- sap_doc_entry is the SAP quotation, used to update the same document when the quotation is edited.
-- Run once per database.
CREATE TABLE IF NOT EXISTS SAP_quotation_sync (
  id INT NOT NULL AUTO_INCREMENT,
  record_id INT NOT NULL,
  sap_doc_entry INT NULL DEFAULT NULL,
  sap_doc_num INT NULL DEFAULT NULL,
  sync_status VARCHAR(20) NOT NULL,
  request_payload MEDIUMTEXT NULL,
  response_payload MEDIUMTEXT NULL,
  error_message TEXT NULL,
  attempt_count INT NOT NULL DEFAULT 0,
  last_attempt_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_sap_quote_record (record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
