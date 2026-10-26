-- Registry of finished goods codes: one row per quotation, numbered in order from the first quotation
-- (SPFGMC00001, SPFGMC00002 ...). A quotation keeps its code for life; a new quotation takes the next number.
-- Run once per database. (An earlier, empty version of this table keyed by machine specification is replaced.)
DROP TABLE IF EXISTS fg_code_master;
CREATE TABLE fg_code_master (
  id INT NOT NULL AUTO_INCREMENT,
  seq INT NOT NULL,
  fg_code VARCHAR(20) NOT NULL,
  record_id INT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_fg_seq (seq),
  UNIQUE KEY uq_fg_code (fg_code),
  UNIQUE KEY uq_fg_record (record_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
