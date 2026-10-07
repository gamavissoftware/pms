<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function df_delay_assert_alias($alias)
{
    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $alias)) {
        throw new InvalidArgumentException('Invalid task alias');
    }
}

function df_delay_has_table($db, $table)
{
    if (method_exists($db, 'table_exists')) {
        return $db->table_exists($table);
    }

    return true;
}

function df_delay_valid_date_sql($dateExpression)
{
    return "$dateExpression IS NOT NULL AND $dateExpression >= '1000-01-01'";
}

function df_delay_task_type_sql($alias)
{
    df_delay_assert_alias($alias);
    return "(SELECT COALESCE(MAX(tm_delay_type.task_type), 2) FROM task_management tm_delay_type WHERE tm_delay_type.task_id = $alias.taskid)";
}

function df_delay_department_max_date_sql($alias)
{
    df_delay_assert_alias($alias);
    return "(SELECT MAX(dept_delay_tasks.end_date)
        FROM task_department_wise_scheduling dept_delay_tasks
        WHERE dept_delay_tasks.df_id = $alias.df_id
        AND dept_delay_tasks.department_id = $alias.department_id
        AND " . df_delay_valid_date_sql('dept_delay_tasks.end_date') . ")";
}

function df_delay_schedule_max_date_sql($alias)
{
    df_delay_assert_alias($alias);
    return "(SELECT MAX(df_delay_tasks.end_date)
        FROM task_department_wise_scheduling df_delay_tasks
        WHERE df_delay_tasks.df_id = $alias.df_id
        AND " . df_delay_valid_date_sql('df_delay_tasks.end_date') . ")";
}

function df_delay_machine_dispatch_date_sql($db, $taskAlias, $dfAlias = 'df')
{
    df_delay_assert_alias($taskAlias);
    df_delay_assert_alias($dfAlias);

    $fallback = df_delay_schedule_max_date_sql($taskAlias);
    if (!df_delay_has_table($db, 'df_dispatch_plans')) {
        return $fallback;
    }

    return "COALESCE(
        (SELECT MAX(df_delay_dispatch.planned_dispatch_date)
            FROM df_dispatch_plans df_delay_dispatch
            WHERE " . df_delay_valid_date_sql('df_delay_dispatch.planned_dispatch_date') . "
            AND (
                df_delay_dispatch.df_id = $dfAlias.id
                OR df_delay_dispatch.df_no = $dfAlias.df_no
            )
        ),
        $fallback
    )";
}

/** Department delay includes main-task slippage and department-window overruns. */
function df_department_overdue_sql($db, $alias = 't', $today = null)
{
    df_delay_assert_alias($alias);
    $date = $db->escape($today === null ? date('Y-m-d') : $today);
    $taskType = df_delay_task_type_sql($alias);
    $departmentMaxDate = df_delay_department_max_date_sql($alias);

    return "$alias.task_status IN (0,2)
        AND " . df_delay_valid_date_sql("$alias.end_date") . "
        AND (
            ($taskType = 1 AND $alias.end_date < $date)
            OR ($departmentMaxDate IS NOT NULL AND $departmentMaxDate < $date)
        )";
}

/**
 * Current DF delay excludes completed tasks, missing dates and tasks due today.
 * Sub tasks only count after the department max planned date and machine
 * dispatch planned date are crossed. Main-task slippage stays departmental
 * until the department window is crossed.
 */
function df_open_overdue_sql($db, $alias = 't', $today = null, $dfAlias = 'df')
{
    df_delay_assert_alias($alias);
    df_delay_assert_alias($dfAlias);
    $date = $db->escape($today === null ? date('Y-m-d') : $today);
    $taskType = df_delay_task_type_sql($alias);
    $departmentMaxDate = df_delay_department_max_date_sql($alias);
    $dispatchDate = df_delay_machine_dispatch_date_sql($db, $alias, $dfAlias);

    return "$alias.task_status IN (0,2)
        AND " . df_delay_valid_date_sql("$alias.end_date") . "
        AND $departmentMaxDate IS NOT NULL
        AND $departmentMaxDate < $date
        AND $dispatchDate IS NOT NULL
        AND $dispatchDate < $date
        AND (
            ($taskType = 1 AND $alias.end_date < $date)
            OR ($taskType <> 1)
        )";
}

/** One entry per running, non-hold DF, regardless of the number of overdue tasks. */
function df_current_delay_counts($db, $today = null)
{
    $rows = $db->select('t.df_id, COUNT(t.id) AS delayed_tasks', false)
        ->from('task_department_wise_scheduling t')
        ->join('df_release df', 'df.id = t.df_id', 'inner')
        ->where('df.df_status', 0)
        ->where('df.on_hold', 0)
        ->where(df_open_overdue_sql($db, 't', $today), null, false)
        ->group_by('t.df_id')->get()->result_array();
    $counts = array();
    foreach ($rows as $row) {
        $counts[(int)$row['df_id']] = (int)$row['delayed_tasks'];
    }
    return $counts;
}
