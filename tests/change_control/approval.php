<?php
// Run: php tests/change_control/approval.php. Uses only an in-memory SQLite database.
define('BASEPATH', __DIR__);
class CI_Model { public $db; public function __construct() {} }
class CI_Controller { public $db, $input, $session, $change_model; }
require __DIR__ . '/../../application/models/Df_change_control_model.php';
require __DIR__ . '/../../application/controllers/Df_change_control.php';
class ApprovalResult {
    private $rows;
    function __construct($rows) { $this->rows = $rows; }
    function row_array() { return $this->rows ? $this->rows[0] : array(); }
    function result_array() { return $this->rows; }
}
class ApprovalDb {
    public $pdo;
    private $where = array(), $select = '*', $from, $affected = 0;
    function __construct() { $this->pdo = new PDO('sqlite::memory:'); $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); }
    function table_exists($table) { return true; }
    function select($fields) { $this->select = $fields; return $this; }
    function from($table) { $this->from = $table; return $this; }
    function where($field, $value) { $this->where[$field] = $value; return $this; }
    function predicate() { return implode(' AND ', array_map(function($key) { return "$key = ?"; }, array_keys($this->where))); }
    function get() {
        $q = $this->pdo->prepare('SELECT ' . $this->select . ' FROM ' . $this->from . ($this->where ? ' WHERE ' . $this->predicate() : ''));
        $q->execute(array_values($this->where)); $this->where = array(); $this->select = '*';
        return new ApprovalResult($q->fetchAll(PDO::FETCH_ASSOC));
    }
    function update($table, $data) {
        $q = $this->pdo->prepare('UPDATE ' . $table . ' SET ' . implode(',', array_map(function($key) { return "$key = ?"; }, array_keys($data))) . ' WHERE ' . $this->predicate());
        $q->execute(array_merge(array_values($data), array_values($this->where))); $this->where = array(); $this->affected = $q->rowCount(); return true;
    }
    function affected_rows() { return $this->affected; }
    function insert($table, $data) {
        $q = $this->pdo->prepare('INSERT INTO ' . $table . '(' . implode(',', array_keys($data)) . ') VALUES(' . implode(',', array_fill(0, count($data), '?')) . ')');
        return $q->execute(array_values($data));
    }
}
function check($ok, $message) { if (!$ok) throw new Exception($message); }
$db = new ApprovalDb();
$db->pdo->exec("CREATE TABLE df_change_control(id INTEGER PRIMARY KEY, status TEXT, closed_on TEXT);
CREATE TABLE df_change_control_departments(id INTEGER PRIMARY KEY, change_id INTEGER, status TEXT, notified_on TEXT);
CREATE TABLE df_change_control_history(id INTEGER PRIMARY KEY, change_id INTEGER, department_action_id INTEGER, action_by INTEGER, action_role, action_type, action_note, created_on);
INSERT INTO df_change_control(id,status) VALUES(1,'PENDING_APPROVAL'),(2,'PENDING_APPROVAL'),(3,'OPEN'),(4,'PENDING_APPROVAL');
INSERT INTO df_change_control_departments VALUES(1,1,'PENDING_APPROVAL',NULL),(2,1,'PENDING_APPROVAL',NULL),(3,2,'PENDING_APPROVAL',NULL),(4,3,'PENDING_HEAD_ACTION','2026-01-01'),(5,4,'PENDING_APPROVAL',NULL);");
$model = new Df_change_control_model(); $model->db = $db;
foreach (array(61,161,162,167,0) as $user) check(!$model->record_approval_decision(1,$user,'APPROVE',''), 'Other users including admins must not approve');
check(!$model->record_approval_decision(1,139,'INVALID',''), 'Invalid decision rejected');
check(!$model->record_approval_decision(1,139,'REJECT',' '), 'Rejection requires reason');
check($model->recompute_change_status(1)['current_status'] === 'PENDING_APPROVAL', 'Recomputation must not release pending requests');
$db->pdo->beginTransaction();
check($model->record_approval_decision(1,139,'APPROVE','Proceed'), 'Approval succeeds');
$db->pdo->commit();
check($db->pdo->query("SELECT status FROM df_change_control WHERE id=1")->fetchColumn() === 'OPEN', 'Approved request opens');
check((int)$db->pdo->query("SELECT COUNT(*) FROM df_change_control_departments WHERE change_id=1 AND status='PENDING_HEAD_ACTION' AND notified_on IS NOT NULL")->fetchColumn() === 2, 'All selected departments released together');
check(!$model->record_approval_decision(1,139,'REJECT','Second click'), 'Repeat/conflicting decisions cannot overwrite approval');
check((int)$db->pdo->query('SELECT COUNT(*) FROM df_change_control_history WHERE change_id=1')->fetchColumn() === 1, 'Exactly one approval audit entry');
check($model->record_approval_decision(2,139,'REJECT','Insufficient details'), 'Rejection succeeds');
check($db->pdo->query('SELECT status FROM df_change_control_departments WHERE change_id=2')->fetchColumn() === 'REJECTED', 'Rejected departments stay out of HOD queues');
check(strpos($db->pdo->query('SELECT action_note FROM df_change_control_history WHERE change_id=2')->fetchColumn(),'Insufficient details') !== false, 'Rejection reason recorded');
check($model->recompute_change_status(2)['current_status'] === 'REJECTED', 'Recomputation cannot reopen rejection');
check(!$model->record_approval_decision(2,139,'APPROVE',''), 'Rejected requests cannot later be released');
check(!$model->record_approval_decision(3,139,'REJECT',''), 'Legacy workflow is preserved');
$db->pdo->beginTransaction(); $model->record_approval_decision(4,139,'APPROVE',''); $db->pdo->rollBack();
check($db->pdo->query('SELECT status FROM df_change_control WHERE id=4')->fetchColumn() === 'PENDING_APPROVAL', 'Rollback restores pending master');
check($db->pdo->query('SELECT status FROM df_change_control_departments WHERE change_id=4')->fetchColumn() === 'PENDING_APPROVAL', 'Rollback restores departments');
check((int)$db->pdo->query('SELECT COUNT(*) FROM df_change_control_history WHERE change_id=4')->fetchColumn() === 0, 'Rollback removes decision history');
$controller = (new ReflectionClass('Df_change_control'))->newInstanceWithoutConstructor(); $controller->change_model = $model;
$execution = new ReflectionMethod($controller, 'process_department_execution_update');
foreach (array('PENDING_APPROVAL','REJECTED') as $status) {
    check(!$model->allows_department_work($status), 'Unapproved work blocked');
    $result = $execution->invoke($controller,array('change_id'=>1,'change_status'=>$status,'status'=>'ASSIGNED'),161,'COMPLETED','Force done');
    check(!$result['success'], 'Direct completion blocked even for admin');
}
foreach (array('OPEN','IN_PROGRESS','COMPLETED') as $status) check($model->allows_department_work($status), 'Existing approved workflow permitted');
class ApprovalInput { public $verb = 'POST', $values = array(); function method($upper) { return $this->verb; } function post($key) { return isset($this->values[$key]) ? $this->values[$key] : null; } }
class ApprovalSession { public $userdata = array('logged_in'=>array('user_id'=>139)); function userdata($key) { return $key === 'change_approval_token' ? 'valid-token' : null; } }
function show_error($message, $status) { throw new RuntimeException($message, $status); }
$controller->input = new ApprovalInput(); $controller->session = new ApprovalSession();
foreach (array(array(161,'POST','valid-token'),array(139,'GET','valid-token'),array(139,'POST','wrong-token')) as $case) {
    $controller->session->userdata['logged_in']['user_id'] = $case[0]; $controller->input->verb = $case[1]; $controller->input->values['approval_token'] = $case[2];
    try { $controller->decide_approval(1); throw new Exception('Invalid approval endpoint access allowed'); }
    catch (RuntimeException $error) { check($error->getCode() === 403, 'Forbidden response expected'); }
}
echo "PASS: approval/rejection, exact approver, audit reason, duplicate decisions, rollback, legacy compatibility, execution guard and POST/CSRF protection.\n";
