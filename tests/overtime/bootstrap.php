<?php
define('BASEPATH', __DIR__);
define('page_url', '/index.php/');
date_default_timezone_set('Asia/Calcutta');
class CI_Model { public $db; public function __construct() { $this->db = (object) array('db_debug' => false); } }
class CI_Controller { public $input, $session, $overtime, $load, $config, $security, $output; }
require __DIR__ . '/../../application/models/Overtime_model.php';
require __DIR__ . '/../../application/controllers/Overtime.php';
require __DIR__ . '/../../application/helpers/overtime_helper.php';
class OtResult {
    private $rows;
    public function __construct($rows) { $this->rows = $rows; }
    public function row_array() { return $this->rows ? $this->rows[0] : array(); }
    public function result_array() { return $this->rows; }
}
class OtDb {
    public $pdo, $fail_table = '', $db_debug = false, $mysql = false, $database;
    private $owns_database = false;
    private $where = array(), $affected = 0;
    public function __construct() {
        $socket = getenv('OVERTIME_TEST_MYSQL_SOCKET');
        if ($socket) {
            if (strpos($socket, '/tmp/pms-overtime-mysql.') !== 0) throw new RuntimeException('Only an isolated /tmp overtime MySQL test socket is permitted.');
            $this->mysql = true;
            $this->pdo = new PDO('mysql:unix_socket=' . $socket, 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->database = getenv('OVERTIME_TEST_MYSQL_DATABASE') ?: 'overtime_test_' . bin2hex(random_bytes(6));
            if (!preg_match('/^overtime_test_[a-f0-9]+$/D', $this->database)) throw new RuntimeException('Invalid test database name.');
            $this->owns_database = !getenv('OVERTIME_TEST_MYSQL_DATABASE');
            if ($this->owns_database) $this->pdo->exec('CREATE DATABASE ' . $this->database);
            $this->pdo->exec('USE ' . $this->database);
        } else {
            $this->pdo = new PDO('sqlite::memory:');
        }
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!$this->mysql) $this->pdo->sqliteCreateFunction('NOW', function(){return date('Y-m-d H:i:s');});
        if (!$this->mysql) $this->pdo->sqliteCreateFunction('CONCAT', function (...$parts) { return implode('', $parts); });
    }
    public function __destruct() { if ($this->owns_database) $this->pdo->exec('DROP DATABASE ' . $this->database); }
    public function table_exists($table) { if ($this->mysql) return (bool) $this->pdo->query('SHOW TABLES LIKE ' . $this->pdo->quote($table))->fetchColumn(); return (bool) $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name=" . $this->pdo->quote($table))->fetchColumn(); }
    public function field_exists($field,$table) {
        $rows=$this->pdo->query($this->mysql ? 'SHOW COLUMNS FROM '.$table : 'PRAGMA table_info('.$table.')')->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as $row) if (($this->mysql ? $row['Field'] : $row['name'])===$field) return true;
        return false;
    }
    public function query($sql, $params = array()) {
        if (!$this->mysql) $sql = str_replace(array(' FOR UPDATE','INSERT IGNORE'), array('', 'INSERT OR IGNORE'), $sql);
        if (!$this->mysql && strpos($sql, 'ON DUPLICATE KEY UPDATE') !== false) {
            $key = strpos($sql, 'overtime_policies') !== false ? 'business_location_id' : 'employee_id';
            $sql = str_replace('ON DUPLICATE KEY UPDATE', 'ON CONFLICT(' . $key . ') DO UPDATE SET', $sql);
            $sql = preg_replace('/VALUES\((\w+)\)/', 'excluded.$1', $sql);
        }
        $q = $this->pdo->prepare($sql); $q->execute($params); $this->affected = $q->rowCount();
        return new OtResult($q->columnCount() ? $q->fetchAll(PDO::FETCH_ASSOC) : array());
    }
    public function insert($table, $data) {
        if ($this->fail_table === $table) return false;
        $this->query('INSERT INTO ' . $table . '(' . implode(',',array_keys($data)) . ') VALUES (' . implode(',',array_fill(0,count($data),'?')) . ')', array_values($data)); return true;
    }
    public function insert_id() { return $this->pdo->lastInsertId(); }
    public function where($field,$value) { $this->where[$field]=$value; return $this; }
    public function update($table,$data) {
        $where=$this->where; $this->where=array();
        $this->query('UPDATE ' . $table . ' SET ' . implode(',',array_map(function($k){return $k.'=?';},array_keys($data))) . ' WHERE ' . implode(' AND ',array_map(function($k){return $k.'=?';},array_keys($where))), array_merge(array_values($data),array_values($where))); return true;
    }
    public function affected_rows() { return $this->affected; }
    public function trans_begin() { return $this->pdo->beginTransaction(); }
    public function trans_commit() { return $this->pdo->commit(); }
    public function trans_rollback() { return $this->pdo->inTransaction() ? $this->pdo->rollBack() : true; }
    public function trans_status() { return true; }
}
function ot_fixture() {
    $db = new OtDb();
    $schema = file_get_contents(__DIR__ . '/../../Database/overtime_001.sql') . "\n" . file_get_contents(__DIR__.'/../../Database/overtime_003_team_requests.sql');
    if (!$db->mysql) {
    $schema = preg_replace('/--[^\n]*/', '', $schema);
    $schema = preg_replace('/id INT NOT NULL AUTO_INCREMENT/', 'id INTEGER PRIMARY KEY AUTOINCREMENT', $schema);
    $schema = preg_replace('/PRIMARY KEY\(id\),?/', '', $schema);
    $schema = preg_replace('/UNIQUE KEY \w+\(([^)]+)\)/', 'UNIQUE($1)', $schema);
    $schema = preg_replace('/KEY \w+\([^)]+\),?/', '', $schema);
    $schema = preg_replace('/,\s*\)/', ')', $schema);
    $schema = preg_replace('/ENGINE=InnoDB DEFAULT CHARSET=utf8mb4/', '', $schema);
    }
    $db->pdo->exec($schema);
    $db->pdo->exec("CREATE TABLE system_users(user_id INTEGER PRIMARY KEY,first_name TEXT,last_name TEXT,business_location INTEGER,department_id INTEGER,user_role_id INTEGER,user_status INTEGER);
        CREATE TABLE user_role(user_role_id INTEGER PRIMARY KEY,isadmin INTEGER,status INTEGER);
        CREATE TABLE departments(department_id INTEGER PRIMARY KEY,department TEXT,departmenthead INTEGER DEFAULT 0,business_loc_id INTEGER DEFAULT 2,status INTEGER DEFAULT 1);
        CREATE TABLE prestogroup_teams(team_id INTEGER PRIMARY KEY,team_leader INTEGER,business_loc_id INTEGER,status INTEGER);
        CREATE TABLE presto_team_members(employee_id INTEGER,team_id INTEGER);
        CREATE TABLE df_release(id INTEGER PRIMARY KEY,df_no VARCHAR(100),df_description TEXT,df_status INTEGER);
        INSERT INTO df_release VALUES(100,'DF-100','Packing line',0),(101,'DF-101','Automation line',0),(102,'DF-102','Closed DF',1);
        INSERT INTO user_role VALUES(1,0,1),(2,1,1),(3,1,0);
        INSERT INTO departments(department_id,department) VALUES(10,'Production'),(20,'Operations');
        INSERT INTO system_users VALUES(1,'Asha','Sharma',2,10,1,1),(2,'Dev','Singh',2,10,1,1),(3,'Riya','Mehta',2,20,2,1),(4,'Arjun','Kapoor',2,20,2,1),(5,'Other','Employee',2,20,1,1),(6,'Remote','Admin',3,20,2,1),(7,'Inactive','Admin',2,20,2,0),(8,'Inactive Role','Admin',2,20,3,1);
        INSERT INTO system_users VALUES(139,'Shubham','Sharma',2,20,1,1);
        INSERT INTO prestogroup_teams VALUES(10,2,2,1),(20,3,2,1),(30,6,3,1),(40,7,2,0);
        INSERT INTO presto_team_members VALUES(1,10),(2,20),(5,20),(6,30);");
    $identity = $db->mysql ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $db->pdo->exec("CREATE TABLE system_modules(id $identity,modulename VARCHAR(200),status INTEGER,dynachem INTEGER,shubhampack INTEGER);
        CREATE TABLE submodule(id $identity,moduleid INTEGER,submodule VARCHAR(200),status INTEGER,addedOn DATETIME,dynachem INTEGER,shubhampack INTEGER);
        CREATE TABLE module_access(id $identity,role_id INTEGER,moduleid INTEGER,access INTEGER);
        CREATE TABLE module_capablity(id $identity,role_id INTEGER,moduleid INTEGER,submoduleid INTEGER,submodule_access INTEGER,madd INTEGER,medit INTEGER);
        INSERT INTO system_modules(modulename,status,dynachem,shubhampack) VALUES('MASTER',1,1,2);");
    $permission_sql=file_get_contents(__DIR__.'/../../Database/overtime_002_permissions.sql');
    if (!$db->mysql) $permission_sql=str_replace(array('START TRANSACTION;','FROM DUAL'),array('BEGIN TRANSACTION;',''),$permission_sql);
    $db->pdo->exec($permission_sql);
    $db->pdo->exec($permission_sql);
    foreach (range(1,8) as $user_id) {
        foreach ($db->query('SELECT id FROM system_modules')->result_array() as $module) {
            $db->insert('module_access',array('role_id'=>$user_id,'moduleid'=>$module['id'],'access'=>1));
            foreach($db->query('SELECT id FROM submodule WHERE moduleid=?',array($module['id']))->result_array() as $sub) $db->insert('module_capablity',array('role_id'=>$user_id,'moduleid'=>$module['id'],'submoduleid'=>$sub['id'],'submodule_access'=>1,'madd'=>1,'medit'=>1));
        }
    }
    $model = new Overtime_model(); $model->db = $db;
    return array($model, $db);
}
function ot_input($day = '+1 day', $start = '18:00', $end = '20:00', $leader = 2) {
    $date=(new DateTimeImmutable('today'))->modify($day)->format('Y-m-d');
    return array('start_at'=>$date.'T'.$start,'end_at'=>$date.'T'.$end,'break_minutes'=>'15','reason'=>'Complete the pending production order.','work_reference'=>'JOB-2048','leader_id'=>$leader);
}
function ot_team_input($day = '+1 day', $people = array(1,5), $manual = "Contract worker A\nContract worker B") {
    $date=(new DateTimeImmutable('today'))->modify($day)->format('Y-m-d');
    return array('df_id'=>'100','start_at'=>$date.'T18:00','hours'=>'2','reason'=>'Finish packing for the planned DF dispatch.','work_reference'=>'Job 2048','user_ids'=>$people,'manual_people'=>$manual);
}
function check($condition,$message) { if (!$condition) throw new RuntimeException($message); }
function rejects($callback,$message) { try {$callback();} catch (InvalidArgumentException $e) {return;} throw new RuntimeException($message); }
function show_error($message,$status=500) { throw new RuntimeException($message,$status); }
function show_404() { throw new RuntimeException('Not found',404); }
