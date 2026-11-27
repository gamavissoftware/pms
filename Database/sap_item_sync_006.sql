-- Log of finished goods items (quotation FG codes) sent to SAP as Items. One row per FG code.
-- Run once per database.
CREATE TABLE IF NOT EXISTS SAP_item_sync (
  fg_code VARCHAR(20) NOT NULL,
  record_id INT NOT NULL,
  item_name VARCHAR(100) NULL,
  sync_status VARCHAR(20) NOT NULL,
  error_message TEXT NULL,
  response_payload MEDIUMTEXT NULL,
  attempt_count INT NOT NULL DEFAULT 0,
  last_attempt_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (fg_code),
  KEY idx_item_record (record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
