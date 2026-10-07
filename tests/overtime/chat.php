<?php
/**
 * Overtime notifications delivered into the chat module. The Chat model is stubbed, so
 * the whole message is composed, addressed and logged without a chat installation.
 */
require __DIR__ . '/bootstrap.php';
if (!function_exists('log_message')) { function log_message($level, $message) {} }

list($m, $db) = ot_fixture();
foreach (array('chat_conversation','chat_participant','chat_message','chat_notification','chat_event') as $chat_table) $db->pdo->exec('CREATE TABLE ' . $chat_table . '(id INTEGER PRIMARY KEY)');

class OtFakeChat {
    public $sent = array(), $notified = array(), $events = array(), $conversations = array(), $fail = false, $no_conversation = false;
    public function ensure_direct($me, $other_id, $other_type) {
        if ($this->no_conversation) return 0;
        $pair = array($me['type'] . ':' . $me['id'], $other_type . ':' . $other_id);
        sort($pair);
        $key = implode('|', $pair);
        if (!isset($this->conversations[$key])) $this->conversations[$key] = count($this->conversations) + 1;
        return $this->conversations[$key];
    }
    public function send_message($conv_id, $me, $body, $opts = array()) {
        if ($this->fail) throw new RuntimeException('chat write refused');
        $this->sent[] = array('conversation' => $conv_id, 'from' => $me['id'], 'from_name' => $me['name'], 'body' => $body);
        return count($this->sent);
    }
    public function notify_conversation($conv_id, $actor, $msg_id, $type, $title, $body) { $this->notified[] = array($conv_id, $actor['id'], $title, $body); }
    public function push_event($conv_id, $type, $actor, $msg_id = 0, $only = null) { $this->events[] = array($conv_id, $type, $msg_id); }
    public function plain($s) { return str_replace(array('**', '`'), '', (string) $s); }
}
class OtChatLoader {
    public $chat;
    public function __construct($chat) { $this->chat = $chat; }
    public function helper($name) {}
    public function model($name, $alias = null) { global $CI; $CI->{$alias ? $alias : $name} = $this->chat; }
    public function library($name, $config = null, $alias = null) {}
}
class OtChatCI { public $load, $db, $overtime, $overtime_chat_model; }

$chat = new OtFakeChat();
$CI = new OtChatCI();
$CI->load = new OtChatLoader($chat);
$CI->db = $db;
$CI->overtime = $m;
// CodeIgniter's own get_instance() returns by reference; the stub must match.
function &get_instance() { global $CI; return $CI; }
require __DIR__ . '/../../application/libraries/Overtime_chat.php';
$notifier = new Overtime_chat();

$m->save_cost_rate(4, array('scope' => 'LOCATION', 'scope_id' => 0, 'hourly_rate' => '100', 'note' => ''));
$id = $m->submit(2, ot_team_input('+1 day', array(1), 'Contract worker A'), bin2hex(random_bytes(32)));
$request = $m->email_context($id);

/* ---- a new request reaches the approver, written by the person who raised it ---- */
check($notifier->request_raised($request) === true, 'The request message is sent');
$sent = end($chat->sent);
check($sent['from'] === 2, 'The request message comes from whoever raised it');
check($sent['from_name'] === 'Dev Singh', 'The sender name is title-cased');
check(strpos($sent['body'], 'Overtime approval needed') !== false, 'The message opens with what is being asked');
check(strpos($sent['body'], $request['request_code']) !== false, 'The request code is in the message');
check(strpos($sent['body'], 'DF DF-100') !== false, 'The DF number is in the message');
check(strpos($sent['body'], '2.00 hours each for 2 people') !== false, 'Hours and headcount are stated');
check(strpos($sent['body'], '4.00 person-hours') !== false, 'The person-hour total is stated');
check(strpos($sent['body'], '₹ 400.00') !== false, 'The cost is stated when a rate exists');
check(strpos($sent['body'], 'Contract worker A') !== false, 'Manual workers are named');
check(strpos($sent['body'], 'Finish packing for the planned DF dispatch.') !== false, 'The reason is carried');
check(strpos($sent['body'], '/index.php/Overtime/view/' . $id) !== false, 'The message links to the request');
$conversation = $sent['conversation'];
check(count($chat->notified) === 1 && $chat->notified[0][0] === $conversation, 'The recipient gets a chat notification');
check($chat->notified[0][2] === 'Dev Singh', 'The notification is titled with the sender');
check(strpos($chat->notified[0][3], '**') === false, 'The notification preview drops the formatting marks');
check(count($chat->events) === 1, 'A live chat event is pushed');

/* ---- the decision goes back down the same conversation ---- */
$m->decide($id, 139, 'APPROVE', 'Go ahead, dispatch is tight.');
$decided = $m->email_context($id);
check($notifier->decision($decided, 'APPROVED', 'Go ahead, dispatch is tight.') === true, 'The decision message is sent');
$sent = end($chat->sent);
check($sent['from'] === 139, 'The decision message comes from the approver');
check($sent['conversation'] === $conversation, 'It lands in the same one-to-one conversation');
check(strpos($sent['body'], 'Overtime approved') !== false, 'The decision states the outcome');
check(strpos($sent['body'], 'assigned to the people on this request') !== false, 'Approval says what happens next');
check(strpos($sent['body'], 'Remarks: Go ahead, dispatch is tight.') !== false, 'Remarks are carried');

$rejected = $m->email_context($m->submit(2, ot_team_input('+2 days', array(5), ''), bin2hex(random_bytes(32))));
check($notifier->decision($rejected, 'REJECTED', '') === true, 'A rejection is sent');
$sent = end($chat->sent);
check(strpos($sent['body'], 'Overtime rejected') !== false, 'The rejection states the outcome');
check(strpos($sent['body'], 'Raise a new request') !== false, 'The rejection says what to do instead');
check(strpos($sent['body'], 'Remarks:') === false, 'No empty remarks line when none were given');
check($notifier->decision($rejected, 'CANCELLED', '') === false, 'Only approvals and rejections are announced');

/* ---- the approver raising his own overtime must not message himself ---- */
$self = $m->email_context($id);
$self['requester'] = $m->user(139);
$before = count($chat->sent);
check($notifier->request_raised($self) === false, 'A self-addressed request sends nothing');
check(count($chat->sent) === $before, 'No chat message was written');
$skipped = $db->query("SELECT error FROM overtime_email_log WHERE status='SKIPPED' AND kind='CHAT_REQUESTED' ORDER BY id DESC LIMIT 1")->row_array();
check(strpos($skipped['error'], 'same person') !== false, 'The skip reason is recorded');

/* ---- failures never reach the request ---- */
$chat->fail = true;
check($notifier->request_raised($request) === false, 'A refused chat write does not throw');
$failed = $db->query("SELECT kind,recipient FROM overtime_email_log WHERE status='FAILED' ORDER BY id DESC LIMIT 1")->row_array();
check($failed['kind'] === 'CHAT_REQUESTED' && $failed['recipient'] === 'chat user #139', 'The failure is logged against the chat recipient');
$chat->fail = false;
$chat->no_conversation = true;
check($notifier->request_raised($request) === false, 'A conversation that cannot be opened is a failure, not a fatal');
$chat->no_conversation = false;

/* ---- chat not installed at all ---- */
$db->pdo->exec('DROP TABLE chat_event');
check($notifier->request_raised($request) === false, 'A half-installed chat module sends nothing rather than half-sending');
$skipped = $db->query("SELECT error FROM overtime_email_log WHERE status='SKIPPED' ORDER BY id DESC LIMIT 1")->row_array();
check(strpos($skipped['error'], 'chat module is not installed') !== false, 'The uninstalled reason is recorded');

$kinds = $db->query("SELECT DISTINCT kind FROM overtime_email_log WHERE kind LIKE 'CHAT%' ORDER BY kind")->result_array();
check(count($kinds) === 3, 'Request, approval and rejection are logged under their own kinds');

echo "PASS: chat notification routing, message content, one shared conversation, self-addressed and uninstalled skips, and failure logging.\n";
