<?php
// Run: php tests/customised_df/tracking.php (no application database needed).
define('BASEPATH', __DIR__);

class CI_Model
{
    public $db;
}

class CustomisedDfTestResult
{
    private $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    public function result_array()
    {
        return $this->rows;
    }
}

class CustomisedDfTestDb
{
    public $table_created = false;
    public $rows = array();

    public function table_exists($table)
    {
        return $this->table_created;
    }

    public function query($sql)
    {
        $this->table_created = strpos($sql, 'customised_df_schedule_activity') !== false;
        return $this->table_created;
    }

    public function insert($table, $row)
    {
        $this->rows[] = $row;
        return true;
    }

    public function select($fields, $escape = true)
    {
        return $this;
    }

    public function from($table)
    {
        return $this;
    }

    public function group_by($field)
    {
        return $this;
    }

    public function get()
    {
        $grouped = array();
        foreach ($this->rows as $row) {
            $df_id = (int) $row['df_id'];
            if (!isset($grouped[$df_id])) {
                $grouped[$df_id] = array(
                    'df_id' => $df_id,
                    'last_scheduled_on' => $row['scheduled_on'],
                    'activity_count' => 0
                );
            }

            $grouped[$df_id]['activity_count']++;
            if ($row['scheduled_on'] > $grouped[$df_id]['last_scheduled_on']) {
                $grouped[$df_id]['last_scheduled_on'] = $row['scheduled_on'];
            }
        }

        return new CustomisedDfTestResult(array_values($grouped));
    }
}

function check_customised_df($condition, $message)
{
    if (!$condition) {
        throw new Exception($message);
    }
}

require dirname(__DIR__, 2) . '/application/models/Customised_df_model.php';

$model = new Customised_df_model();
$model->db = new CustomisedDfTestDb();

check_customised_df($model->ensure_activity_table(), 'Activity table should be created when missing.');

$base_activity = array(
    'df_id' => 338,
    'activity_type' => 'department_schedule',
    'task_count' => 8,
    'department_count' => 1,
    'schedule_date' => '2026-09-25',
    'scheduled_by' => 161,
    'scheduled_on' => '2026-09-22 10:00:00'
);

check_customised_df($model->record_activity($base_activity), 'Department scheduling activity should be recorded.');

$later_activity = $base_activity;
$later_activity['activity_type'] = 'workspace_update';
$later_activity['scheduled_on'] = '2026-09-22 11:00:00';
check_customised_df($model->record_activity($later_activity), 'Workspace activity should be recorded.');

$other_df_activity = $base_activity;
$other_df_activity['df_id'] = 339;
$other_df_activity['scheduled_on'] = '2026-09-22 09:00:00';
check_customised_df($model->record_activity($other_df_activity), 'A second DF should be tracked independently.');

$activity_map = $model->get_latest_activity_map();
check_customised_df(count($activity_map) === 2, 'Activity map should contain one entry per DF.');
check_customised_df((int) $activity_map[338]['activity_count'] === 2, 'DF activity count should include each workspace save.');
check_customised_df($activity_map[338]['last_scheduled_on'] === '2026-09-22 11:00:00', 'Latest Customised DF activity should be returned.');

echo "PASS: Customised DF activity creation, recording, and aggregation.\n";
