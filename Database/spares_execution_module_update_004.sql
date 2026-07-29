-- Spares Execution Module incremental patch
-- Use this file only if previous Spares execution SQL files were already imported.
-- Safe to run in phpMyAdmin on an existing setup.

SET @spares_execution_has_alert_log_table := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'spares_execution_alert_log'
);

SET @spares_execution_has_alert_type_stale := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'spares_execution_alert_log'
      AND COLUMN_NAME = 'alert_type'
      AND LOCATE("'STALE_TASK'", COLUMN_TYPE) > 0
);

SET @spares_execution_alert_type_alter_sql := IF(
    @spares_execution_has_alert_log_table = 1 AND @spares_execution_has_alert_type_stale = 0,
    'ALTER TABLE `spares_execution_alert_log` MODIFY COLUMN `alert_type` enum(''UNSCHEDULED_ORDER'',''OVERDUE_TASK'',''STALE_TASK'',''PENDING_EXTENSION'') NOT NULL',
    'SELECT 1'
);

PREPARE spares_execution_stmt_alert_type FROM @spares_execution_alert_type_alter_sql;
EXECUTE spares_execution_stmt_alert_type;
DEALLOCATE PREPARE spares_execution_stmt_alert_type;

SET @spares_execution_has_task_updates_table := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'spares_execution_task_updates'
);

SET @spares_execution_has_bulk_status_update_type := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'spares_execution_task_updates'
      AND COLUMN_NAME = 'update_type'
      AND LOCATE("'Bulk Status'", COLUMN_TYPE) > 0
);

SET @spares_execution_update_type_alter_sql := IF(
    @spares_execution_has_task_updates_table = 1 AND @spares_execution_has_bulk_status_update_type = 0,
    'ALTER TABLE `spares_execution_task_updates` MODIFY COLUMN `update_type` enum(''Remark'',''Status'',''Assignment'',''Attachment'',''Extension Request'',''Extension Decision'',''Schedule'',''Bulk Status'') NOT NULL DEFAULT ''Remark''',
    'SELECT 1'
);

PREPARE spares_execution_stmt_update_type FROM @spares_execution_update_type_alter_sql;
EXECUTE spares_execution_stmt_update_type;
DEALLOCATE PREPARE spares_execution_stmt_update_type;
