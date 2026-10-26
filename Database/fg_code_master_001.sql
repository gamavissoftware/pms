-- Registry of finished goods codes (SPFGMC00001, SPFGMC00002 ...). One row per distinct machine specification
-- (model, liquid/powder, tracks, axis, speed); quotes with the same specification share the code.
-- Run once per database.
CREATE TABLE IF NOT EXISTS fg_code_master (
  id INT NOT NULL AUTO_INCREMENT,
  seq INT NOT NULL,
  fg_code VARCHAR(20) NOT NULL,
  spec_key VARCHAR(60) NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_fg_seq (seq),
  UNIQUE KEY uq_fg_code (fg_code),
  UNIQUE KEY uq_fg_spec (spec_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
