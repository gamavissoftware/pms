<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Overtime email notifications.
 *
 * Two messages only, and both are sent after the database transaction has committed:
 * a new request goes to the approver, and the approve/reject decision goes back to the
 * person who raised it. Delivery never blocks the workflow - a refused or timed-out SMTP
 * connection is written to overtime_email_log and the request still stands.
 *
 * SMTP settings come from application/config/constants.php so credentials live where the
 * rest of this application already keeps them.
 */
class Overtime_mailer
{
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('overtime');
    }

    private function settings()
    {
        return array(
            'protocol' => 'smtp',
            'smtp_host' => defined('overtime_mail_host') ? overtime_mail_host : 'ssl://smtp.googlemail.com',
            'smtp_port' => defined('overtime_mail_port') ? overtime_mail_port : 465,
            'smtp_user' => defined('overtime_mail_user') ? overtime_mail_user : 'taskmanagement@shubhampack.com',
            'smtp_pass' => defined('overtime_mail_pass') ? overtime_mail_pass : '',
            'smtp_timeout' => 15,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
        );
    }

    /** A new request is waiting for Shubham Sharma. */
    public function request_raised($request)
    {
        $approver = $request['approver'];
        $subject = 'Overtime approval needed: ' . $request['request_code'] . ($request['df_no'] ? ' (DF ' . $request['df_no'] . ')' : '');
        $intro = ot_e(trim($request['first_name'] . ' ' . $request['last_name'])) . ' has raised an overtime request that is waiting for your approval.';
        return $this->deliver($request, 'REQUESTED', $approver, $subject, $this->body($request, $intro, 'Open the request to approve or reject it.'));
    }

    /** The decision goes back to whoever raised the request. */
    public function decision($request, $status, $note)
    {
        if (!in_array($status, array('APPROVED', 'REJECTED'), true)) return false;
        $approver = trim($request['approver'] ? $request['approver']['first_name'] . ' ' . $request['approver']['last_name'] : 'The approver');
        $subject = 'Overtime ' . strtolower($status === 'APPROVED' ? 'approved' : 'rejected') . ': ' . $request['request_code'] . ($request['df_no'] ? ' (DF ' . $request['df_no'] . ')' : '');
        $intro = ot_e($approver) . ' has <strong>' . strtolower($status === 'APPROVED' ? 'approved' : 'rejected') . '</strong> your overtime request'
            . ($status === 'APPROVED' ? '. The overtime is now assigned to the people listed below.' : '.');
        $tail = trim((string) $note) === '' ? '' : '<p style="margin:16px 0 0"><strong>Remarks:</strong><br>' . nl2br(ot_e($note)) . '</p>';
        return $this->deliver($request, $status, $request['requester'], $subject, $this->body($request, $intro, 'Open the request for the full audit history.') . $tail);
    }

    private function body($request, $intro, $call_to_action)
    {
        $people = isset($request['people']) ? $request['people'] : array();
        $cost = 0;
        $names = array();
        foreach ($people as $person) {
            $cost += (float) $person['cost_amount'];
            $names[] = ot_e($person['person_name']) . ($person['user_id'] ? '' : ' <em>(manual / contract)</em>');
        }
        $rows = array(
            'Request' => ot_e($request['request_code']),
            'DF No.' => ot_e($request['df_no'] ?: 'Not linked to a DF'),
            'Raised by' => ot_e(trim($request['first_name'] . ' ' . $request['last_name']) . ' · ' . $request['department']),
            'Overtime period' => ot_e($request['start_at']) . ' to ' . ot_e($request['end_at']),
            'Hours per person' => ot_hours($request['requested_minutes']),
            'People' => count($names) . ' — ' . implode(', ', $names),
            'Total person-hours' => ot_hours(max(1, count($names)) * $request['requested_minutes']),
            'Overtime cost' => ot_money($cost) . ($cost > 0 ? '' : ' (no cost rate configured yet)'),
            'Status' => ot_e(ot_status($request['status'])),
        );
        $html = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#22313f">'
            . '<p style="margin:0 0 14px">' . $intro . '</p>'
            . '<table cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-size:13px">';
        foreach ($rows as $label => $value) {
            $html .= '<tr><td style="border:1px solid #dde4ea;background:#f5f8fa;white-space:nowrap"><strong>' . ot_e($label) . '</strong></td>'
                . '<td style="border:1px solid #dde4ea">' . $value . '</td></tr>';
        }
        $html .= '</table>'
            . '<p style="margin:16px 0 0"><strong>Reason:</strong><br>' . nl2br(ot_e($request['reason'])) . '</p>'
            . '<p style="margin:16px 0 0"><a href="' . ot_e(ot_link('view/' . (int) $request['id'])) . '" style="background:#137a6d;color:#fff;padding:9px 15px;border-radius:4px;text-decoration:none">Open the request</a></p>'
            . '<p style="margin:16px 0 0;font-size:12px;color:#6b7c8c">' . ot_e($call_to_action)
            . ' This is an automatic message from the PMS overtime module. Approved hours are an authorization, not attendance or payroll.</p></div>';
        return $html;
    }

    private function deliver($request, $kind, $person, $subject, $html)
    {
        $to = $person && !empty($person['email']) ? trim($person['email']) : '';
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->CI->overtime->log_email($request['id'], $kind, $to, $subject, 'SKIPPED', $to === '' ? 'No email address on the PMS account.' : 'The PMS account has an invalid email address.');
            return false;
        }
        try {
            $settings = $this->settings();
            $this->CI->load->library('email');
            $this->CI->email->clear(true);
            $this->CI->email->initialize($settings);
            $this->CI->email->from($settings['smtp_user'], 'Shubham Pack PMS — Overtime');
            $this->CI->email->to($to);
            $this->CI->email->subject($subject);
            $this->CI->email->message($html);
            $this->CI->email->set_alt_message(trim(preg_replace('/\n{3,}/', "\n\n", strip_tags(str_replace(array('</tr>', '</p>'), "\n", $html)))));
            if ($this->CI->email->send(false)) {
                $this->CI->overtime->log_email($request['id'], $kind, $to, $subject, 'SENT');
                return true;
            }
            $this->CI->overtime->log_email($request['id'], $kind, $to, $subject, 'FAILED', $this->CI->email->print_debugger(array('headers')));
        } catch (Throwable $e) {
            log_message('error', 'Overtime email failed: ' . $e->getMessage());
            $this->CI->overtime->log_email($request['id'], $kind, $to, $subject, 'FAILED', $e->getMessage());
        }
        return false;
    }
}
