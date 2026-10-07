<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Overtime notifications delivered into the PMS chat module.
 *
 * The same two moments the mailer covers: a new request reaches Shubham Sharma, and his
 * decision reaches whoever raised it. Both land in the ordinary one-to-one conversation
 * between those two people, written by the person whose action it was, so the thread
 * reads like a conversation rather than a robot feed and the recipient's normal unread
 * badge does the work.
 *
 * Like the mailer, this is a courtesy on top of the in-module notification and never a
 * precondition: it runs after the transaction has committed and swallows every failure,
 * including chat not being installed at all. Outcomes are written to overtime_email_log
 * with a CHAT_ kind, which is the module's log of everything it sent outside itself.
 */
class Overtime_chat
{
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('overtime');
    }

    /**
     * Every table this flow writes to is checked up front, not just the obvious three:
     * a half-installed chat module would otherwise accept the message and then fail on
     * the notification, leaving a delivered message logged as a failure.
     *
     * Loading Chat_model switches the connection to utf8mb4 for the rest of the request.
     * That is deliberate on its part, and harmless here because this runs after the
     * transaction has committed and immediately before a redirect.
     */
    private function ready()
    {
        foreach (array('chat_conversation', 'chat_participant', 'chat_message', 'chat_notification', 'chat_event') as $table) {
            if (!$this->CI->db->table_exists($table)) return false;
        }
        $this->CI->load->model('Chat_model', 'overtime_chat_model');
        return true;
    }

    private function identity($person, $fallback_id)
    {
        $name = $person ? ot_person_name($person['first_name'], $person['last_name']) : '';
        return array('id' => (int) ($person ? $person['user_id'] : $fallback_id), 'type' => 'user',
            'name' => $name === '' ? 'PMS user' : $name);
    }

    /** A new request is waiting for Shubham Sharma. */
    public function request_raised($request)
    {
        $from = $this->identity($request['requester'], (int) $request['employee_id']);
        $to = $this->identity($request['approver'], 139);
        $body = '**Overtime approval needed — ' . $request['request_code'] . '**' . "\n"
            . $this->facts($request) . "\n"
            . 'Raised by ' . $from['name'] . '. Please approve or reject.' . "\n"
            . 'Reason: ' . $this->trim_to($request['reason'], 400) . "\n"
            . ot_link('view/' . (int) $request['id']);
        return $this->deliver($request, 'CHAT_REQUESTED', $from, $to, $body);
    }

    /** The decision goes back to whoever raised the request. */
    public function decision($request, $status, $note)
    {
        if (!in_array($status, array('APPROVED', 'REJECTED'), true)) return false;
        $from = $this->identity($request['approver'], 139);
        $to = $this->identity($request['requester'], (int) $request['employee_id']);
        $verb = $status === 'APPROVED' ? 'approved' : 'rejected';
        $body = '**Overtime ' . $verb . ' — ' . $request['request_code'] . '**' . "\n"
            . $this->facts($request) . "\n"
            . ($status === 'APPROVED'
                ? 'The overtime is assigned to the people on this request.'
                : 'This overtime was not approved. Raise a new request if the work is still needed.') . "\n"
            . (trim((string) $note) === '' ? '' : 'Remarks: ' . $this->trim_to($note, 400) . "\n")
            . ot_link('view/' . (int) $request['id']);
        return $this->deliver($request, 'CHAT_' . $status, $from, $to, $body);
    }

    /** The one line that carries the DF, the timing, the people and the cost. */
    private function facts($request)
    {
        $people = isset($request['people']) ? $request['people'] : array();
        $count = max(1, count($people));
        $cost = 0;
        $named = array();
        foreach ($people as $person) {
            $cost += (float) $person['cost_amount'];
            $named[] = $person['person_name'];
        }
        $line = 'DF ' . ($request['df_no'] ?: 'not linked') . ' · ' . $request['start_at'] . ' to ' . $request['end_at']
            . ' · ' . ot_hours($request['requested_minutes']) . ' hours each for ' . $count . ' '
            . ($count === 1 ? 'person' : 'people') . ' · ' . ot_hours($count * $request['requested_minutes']) . ' person-hours';
        if ($cost > 0) $line .= ' · ₹ ' . ot_money($cost);
        if ($named) $line .= "\n" . 'People: ' . $this->trim_to(implode(', ', $named), 300);
        return $line;
    }

    private function trim_to($text, $limit)
    {
        $text = trim(preg_replace('/\s+/u', ' ', (string) $text));
        if (function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > $limit) return mb_substr($text, 0, $limit, 'UTF-8') . '…';
        return $text;
    }

    private function deliver($request, $kind, $from, $to, $body)
    {
        $recipient = 'chat user #' . $to['id'];
        if ($to['id'] <= 0 || $from['id'] <= 0 || $to['id'] === $from['id']) {
            // Shubham raising his own request would otherwise message himself.
            $this->CI->overtime->log_email($request['id'], $kind, $recipient, $request['request_code'], 'SKIPPED',
                $to['id'] === $from['id'] ? 'Sender and recipient are the same person.' : 'No chat recipient for this request.');
            return false;
        }
        try {
            if (!$this->ready()) {
                $this->CI->overtime->log_email($request['id'], $kind, $recipient, $request['request_code'], 'SKIPPED', 'The chat module is not installed.');
                return false;
            }
            $chat = $this->CI->overtime_chat_model;
            $conversation = (int) $chat->ensure_direct($from, $to['id'], $to['type']);
            if ($conversation <= 0) throw new RuntimeException('The direct conversation could not be opened.');
            $message = (int) $chat->send_message($conversation, $from, $body, array('type' => 'text'));
            $chat->notify_conversation($conversation, $from, $message, 'message', $from['name'], $chat->plain(strip_tags($body)));
            $chat->push_event($conversation, 'message', $from, $message);
            $this->CI->overtime->log_email($request['id'], $kind, $recipient, $request['request_code'], 'SENT');
            return true;
        } catch (Throwable $e) {
            log_message('error', 'Overtime chat notification failed: ' . $e->getMessage());
            $this->CI->overtime->log_email($request['id'], $kind, $recipient, $request['request_code'], 'FAILED', $e->getMessage());
        }
        return false;
    }
}
