<?php
// Run: php tests/delay_table/regression.php (no application database needed).
define('BASEPATH', __DIR__);
class CI_Controller { public $db; public $input; }
require __DIR__ . '/../../application/controllers/Task.php';
class TestInput {
    public $values;
    public function post($key) { return isset($this->values[$key]) ? $this->values[$key] : null; }
}
class TestResult {
    private $rows;
    public function __construct($rows) { $this->rows = $rows; }
    public function num_rows() { return count($this->rows); }
    public function result_array() { return $this->rows; }
    public function result() { return array_map(function($row) { return (object)$row; }, $this->rows); }
    public function row() { return (object)($this->rows ? $this->rows[0] : []); }
}
class TestDb {
    public $pdo, $detailMode = false, $matchedIds = [];
    private $select, $from, $joins = [], $where = [], $group = '', $order = [], $limit = '';
    public function __construct() { $this->pdo = new PDO('sqlite::memory:'); $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); }
    public function escape($value) { return $this->pdo->quote($value); }
    public function table_exists($table) {
        $stmt = $this->pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
        $stmt->execute([$table]);
        return (bool)$stmt->fetchColumn();
    }
    public function select($sql, $escape = true) { $this->select = $sql; return $this; }
    public function from($sql) { $this->from = $sql; return $this; }
    public function join($table, $on, $type) { $this->joins[] = "$type JOIN $table ON $on"; return $this; }
    public function where($field, $value, $escape = true) {
        if ($escape === false) { $this->where[] = $field; return $this; }
        $this->where[] = $value === null ? "$field IS NULL" : $field . (preg_match('/[<>=]$/', $field) ? ' ' : ' = ') . $this->escape($value);
        return $this;
    }
    public function where_in($field, $values) { $this->where[] = "$field IN (" . implode(',', array_map([$this, 'escape'], $values)) . ')'; return $this; }
    public function group_by($field) { $this->group = $field; return $this; }
    public function order_by($field, $direction) { $this->order[] = "$field $direction"; return $this; }
    public function limit($limit) { $this->limit = ' LIMIT ' . (int)$limit; return $this; }
    public function get() {
        $sql = 'SELECT ' . ($this->detailMode ? 'a.id' : $this->select) . ' FROM ' . $this->from . ' ' . implode(' ', $this->joins);
        if ($this->where) $sql .= ' WHERE ' . implode(' AND ', $this->where);
        if ($this->group) $sql .= ' GROUP BY ' . $this->group;
        if ($this->order) $sql .= ' ORDER BY ' . implode(',', $this->order);
        if ($this->limit) $sql .= $this->limit;
        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $this->select = $this->from = $this->group = $this->limit = ''; $this->where = $this->joins = $this->order = [];
        if ($this->detailMode) { $this->matchedIds = array_column($rows, 'id'); return new TestResult([]); }
        return new TestResult($rows);
    }
}
function check($condition, $message) { if (!$condition) throw new Exception($message); }
$db = new TestDb();
$today = date('Y-m-d'); $past = date('Y-m-d', strtotime('-5 days')); $future = date('Y-m-d', strtotime('+5 days'));
$db->pdo->exec("CREATE TABLE company_information(company_name, logo, colorcode);
CREATE TABLE df_release(id INTEGER, df_no, df_description, added_on, df_upload, added_by INTEGER, df_status INTEGER, on_hold INTEGER);
CREATE TABLE system_users(user_id INTEGER, title, first_name, last_name);
CREATE TABLE poreceived(df_id INTEGER, company_name, pono, po_attachment);
CREATE TABLE departments(department_id INTEGER, department);
CREATE TABLE task_management(task_id INTEGER, task_name, task_type INTEGER);
CREATE TABLE df_dispatch_plans(id INTEGER, df_id INTEGER, df_no, planned_dispatch_date TEXT);
CREATE TABLE task_department_wise_scheduling(id INTEGER, df_id INTEGER, department_id INTEGER, task_status INTEGER, end_date TEXT, taskid INTEGER, assigned_user INTEGER);
INSERT INTO departments VALUES(1,'Accounts'),(2,'Design');
INSERT INTO task_management VALUES(1,'Main Task',1),(2,'Sub Task',2);
INSERT INTO df_release VALUES(1,'1903','Test','2026-01-01','',1,0,0),(2,'1904','Test','2026-01-01','',1,0,0),(3,'1905','Test','2026-01-01','',1,0,0),(4,'1906','Test','2026-01-01','',1,0,0);
INSERT INTO df_dispatch_plans VALUES(1,1,'1903','$past'),(2,2,'1904','$future'),(3,3,'1905','$past'),(4,4,'1906','$past');");
$insert = $db->pdo->prepare('INSERT INTO task_department_wise_scheduling VALUES(?,?,?,?,?,1,1)');
foreach ([[1,1,1,0,$past],[2,1,1,2,$past],[3,1,1,0,$future],[4,1,1,1,$past],[5,1,1,0,$today],[6,1,1,0,null],[7,1,1,0,'0000-00-00'],[8,1,2,0,$future],[9,2,1,0,$past],[10,3,1,0,$past],[11,4,1,0,$future]] as $row) $insert->execute($row);
$db->pdo->exec("UPDATE task_department_wise_scheduling SET taskid=2 WHERE id IN (8,9,10);");
$db->pdo->exec("INSERT INTO system_users VALUES(1,'MR.','TEST','OWNER'),(2,'Ms.','Future','Owner'); UPDATE task_department_wise_scheduling SET assigned_user=2 WHERE id=3;");
$view = new class {
    public $db, $session, $load;
    public function summarize() {
        $source = file_get_contents(__DIR__ . '/../../application/views/master/delay_table.php');
        eval(substr(explode('?>', $source, 2)[0], 5));
        return [$totalDelayedDf, $rowsData];
    }
};
$view->db = $db; $view->session = (object)['userdata' => ['logged_in' => ['user_id' => 1]]];
$view->load = new class { public function helper($name) { require_once __DIR__ . '/../../application/helpers/' . $name . '_helper.php'; } };
list($delayed, $rows) = $view->summarize();
check($delayed === 1, 'Only DFs past department max date and dispatch boundary count as delayed');
$df = array_values(array_filter($rows, function($row) { return $row['id'] === 1; }))[0];
$dept = $df['department_rows'][0];
check($dept['delayed_tasks'] === 2 && $dept['delay_days'] === 5, 'Main-task delay remains visible as departmental delay');
check($df['delay_status'] === 'On Time', 'Main-task delay inside department window must not mark DF delayed');
check($dept['delayed_users'] === ['Mr. Test Owner'], 'Only department-delayed task assignees, deduplicated');
check($dept['done_tasks'] === 1 && $dept['pending_tasks'] === 6, 'Done and pending counts');
$task = (new ReflectionClass('Task'))->newInstanceWithoutConstructor();
$task->db = $db; $task->input = new TestInput(); $db->detailMode = true;
foreach (['done' => [4], 'pending' => [6,7,1,2,5,3], 'delayed' => [1,2], 'all' => [6,7,1,2,4,5,3]] as $filter => $expected) {
    $task->input->values = ['df_id'=>1,'department_id'=>1,'task_status'=>$filter,'detail_scope'=>$filter === 'all' ? 'all' : 'filtered','response_type'=>'json'];
    ob_start(); $task->get_task_details(); ob_end_clean();
    $actual = $db->matchedIds; sort($actual); sort($expected);
    check($actual === $expected, 'Incorrect modal records for ' . $filter);
}
echo "PASS: delayed DF count, overdue count/days, done/pending totals, and modal record filters.\n";
