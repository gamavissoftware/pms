<?php
// Exercise the real save action with in-memory persistence and notification doubles.
error_reporting(E_ALL);
define('page_url', '/');
class SaveRedirect extends RuntimeException {}
function redirect($url) { throw new SaveRedirect($url); }
class SaveInput {
    public $ids;
    public function post($key) {
        $data = array('assigned_to_user_id' => $this->ids, 'requested_due_date' => '2026-10-01', 'title' => 'Test task', 'task_details' => 'Test details', 'priority' => 'MEDIUM');
        return isset($data[$key]) ? $data[$key] : '';
    }
}
class SaveValidation {
    public function set_error_delimiters(...$args) {}
    public function set_rules(...$args) {}
    public function run() { return true; }
}
class SaveDatabase {
    public function trans_start() {}
    public function trans_complete() {}
    public function trans_status() { return true; }
}
class SaveModel {
    public $tasks = array(), $updates = array(), $notifications = array();
    public function module_ready() { return true; }
    public function get_user($id) { return array('user_id' => $id, 'business_location' => 2, 'department_id' => 1, 'name' => 'User'); }
    public function get_users($ids) { $users = array(); foreach ($ids as $id) { $users[$id] = $this->get_user($id); } return $users; }
    public function create_task($data) { $id = count($this->tasks) + 1; $this->tasks[$id] = $data; return $id; }
    public function generate_task_code($id) { return 'TASK-' . $id; }
    public function update_task($id, $data) { $this->tasks[$id] = array_merge($this->tasks[$id], $data); }
    public function add_update($data) { $this->updates[$data['task_id']] = $data; }
    public function add_notifications($id, $users, $message, $url) { $this->notifications[$id] = $message; }
    public function get_task($id) { return $this->tasks[$id]; }
}
$source = file_get_contents(__DIR__ . '/../../application/controllers/Task_management.php');
$start = strpos($source, '    public function save()');
$end = strpos($source, '    public function view(', $start);
$action = substr($source, $start, $end - $start);
eval('class SaveHarness {
    public $input, $form_validation, $db, $task_module;
    public $messages = array();
    private $task_business_location_id = 2;
    private function get_current_user_id() { return 7; }
    private function upload_task_attachment($field) { return null; }
    private function set_flash_message($type, $message) {}
    private function send_task_email($task, $subject, $heading, $note) { $this->messages[] = array($task, $heading); }
    private function send_task_whatsapp($task, $recipient, $heading, $note, $event) { $this->messages[] = array($task, $heading); }
' . $action . '}');
function check($condition, $message) { if (!$condition) { throw new RuntimeException($message); } }
foreach (array(array('7'), array('8'), array('7', '8'), array('8', '7')) as $ids) {
    $controller = new SaveHarness();
    $controller->input = new SaveInput();
    $controller->input->ids = $ids;
    $controller->form_validation = new SaveValidation();
    $controller->db = new SaveDatabase();
    $controller->task_module = new SaveModel();
    try { $controller->save(); } catch (SaveRedirect $e) {}
    check(count($controller->task_module->tasks) === count($ids), 'Task count mismatch');
    foreach ($controller->task_module->tasks as $id => $task) {
        $self = $task['assigned_to_user_id'] === 7;
        check($task['status'] === ($self ? 'OPEN' : 'AWAITING_DUE_DATE'), 'Incorrect task status');
        check($task['committed_due_date'] === ($self ? '2026-10-01' : null), 'Incorrect committed date');
        check($controller->task_module->updates[$id]['status'] === $task['status'], 'History status mismatch');
        check((strpos($controller->task_module->notifications[$id], 'Please confirm') === false) === $self, 'Incorrect notification');
    }
    check(count($controller->messages) === count($ids) * 2, 'Missing outbound messages');
    foreach ($controller->messages as $message) {
        check((strpos($message[1], 'confirm the due date') === false) === ($message[0]['assigned_to_user_id'] === 7), 'Incorrect outbound message');
    }
}
echo "PASS: self, other, and mixed assignees in both orders; dates, statuses, history, and notifications.\n";
