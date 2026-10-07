<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customised_df_model extends CI_Model
{
    private $activity_table = 'customised_df_schedule_activity';

    public function ensure_activity_table()
    {
        if ($this->db->table_exists($this->activity_table)) {
            return true;
        }

        $sql = "CREATE TABLE IF NOT EXISTS `{$this->activity_table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `df_id` INT NOT NULL,
            `activity_type` VARCHAR(30) NOT NULL DEFAULT 'workspace_update',
            `task_count` INT NOT NULL DEFAULT 0,
            `department_count` INT NOT NULL DEFAULT 0,
            `schedule_date` DATE DEFAULT NULL,
            `scheduled_by` INT NOT NULL DEFAULT 0,
            `scheduled_on` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_customised_df_activity_df` (`df_id`, `scheduled_on`),
            KEY `idx_customised_df_activity_user` (`scheduled_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        return (bool) $this->db->query($sql);
    }

    public function record_activity($activity)
    {
        if (!$this->ensure_activity_table()) {
            return false;
        }

        return $this->db->insert($this->activity_table, array(
            'df_id' => (int) $activity['df_id'],
            'activity_type' => (string) $activity['activity_type'],
            'task_count' => (int) $activity['task_count'],
            'department_count' => (int) $activity['department_count'],
            'schedule_date' => !empty($activity['schedule_date']) ? (string) $activity['schedule_date'] : null,
            'scheduled_by' => (int) $activity['scheduled_by'],
            'scheduled_on' => (string) $activity['scheduled_on']
        ));
    }

    public function get_latest_activity_map()
    {
        if (!$this->ensure_activity_table()) {
            return array();
        }

        $query = $this->db
            ->select('df_id, MAX(scheduled_on) AS last_scheduled_on, COUNT(*) AS activity_count', false)
            ->from($this->activity_table)
            ->group_by('df_id')
            ->get();

        $activity_map = array();
        foreach ($query->result_array() as $row) {
            $activity_map[(int) $row['df_id']] = $row;
        }

        return $activity_map;
    }
}
