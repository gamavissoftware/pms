<?php
/**
 * Overtime cost master, cost stamping, day-wise reporting, the navigation badge and the
 * two notification emails. The mailer runs against a stub CodeIgniter instance so the
 * whole message is built and logged without touching SMTP.
 */
require __DIR__ . '/bootstrap.php';
if (!function_exists('log_message')) { function log_message($level, $message) {} }

list($m, $db) = ot_fixture();
$key = function () { return bin2hex(random_bytes(32)); };
$costs = function ($model, $id) { $total = 0; foreach ($model->assignments($id) as $row) $total += (float) $row['cost_amount']; return round($total, 2); };
$person_cost = function ($model, $id, $name) {
    foreach ($model->assignments($id) as $row) if ($row['person_name'] === $name) return round((float) $row['cost_amount'], 2);
    throw new RuntimeException('No assignment row for ' . $name);
};

/* ---- money formatting uses Indian digit grouping ---- */
check(ot_money(0) === '0.00', 'Zero formats as 0.00');
check(ot_money(1234.5) === '1,234.50', 'Thousands group as 1,234.50');
check(ot_money(123456.789) === '1,23,456.79', 'Lakhs group as 1,23,456.79');
check(ot_money(10000000) === '1,00,00,000.00', 'Crores group as 1,00,00,000.00');

/* ---- with no rates configured, overtime still records, at zero cost ---- */
$first = $m->submit(2, ot_team_input('+1 day', array(1), 'Contract A'), $key());
check($costs($m, $first) === 0.0, 'No cost rate means no cost, not a failure');

/* ---- the master resolves the most specific rate ---- */
$m->save_cost_rate(4, array('scope' => 'LOCATION', 'scope_id' => 0, 'hourly_rate' => '100', 'note' => 'Location default'));
$m->save_cost_rate(4, array('scope' => 'MANUAL', 'scope_id' => 0, 'hourly_rate' => '60', 'note' => 'Contract labour'));
$m->save_cost_rate(4, array('scope' => 'DEPARTMENT', 'scope_id' => 10, 'hourly_rate' => '150', 'note' => ''));
$m->save_cost_rate(4, array('scope' => 'USER', 'scope_id' => 1, 'hourly_rate' => '250', 'note' => ''));
$map = $m->rate_map(2);
check(Overtime_model::rate_for($map, 1, 10) === 250.0, 'An employee rate beats their department');
check(Overtime_model::rate_for($map, 2, 10) === 150.0, 'A department rate beats the location default');
check(Overtime_model::rate_for($map, 3, 20) === 100.0, 'A department with no rate falls back to the location');
check(Overtime_model::rate_for($map, null, 10) === 60.0, 'Manual labour uses the manual rate, never the department rate');
check(Overtime_model::rate_for($m->rate_map(3), 1, 10) === 0.0, 'Another location has its own, here empty, rate list');
check(Overtime_model::cost_of(150, 100) === 250.0, 'Two and a half hours at 100 is 250');

/* ---- a new request is stamped with the estimate at the moment it is raised ---- */
$second = $m->submit(2, ot_team_input('+2 days', array(1, 5), 'Contract A'), $key());
check($person_cost($m, $second, 'Asha Sharma') === 500.0, 'Employee rate applied at two hours');
check($person_cost($m, $second, 'Other Employee') === 200.0, 'Location default applied at two hours');
check($person_cost($m, $second, 'Contract A') === 120.0, 'Manual rate applied at two hours');
check($costs($m, $second) === 820.0, 'Person costs total for the request');

/* ---- approval restamps at the rates in force on the day of the decision ---- */
$m->save_cost_rate(4, array('scope' => 'USER', 'scope_id' => 1, 'hourly_rate' => '300', 'note' => 'Revised'));
check($person_cost($m, $second, 'Asha Sharma') === 500.0, 'A rate change alone does not restate a pending request');
check($m->decide($second, 139, 'APPROVE', '') === 'APPROVED', 'Shubham approves');
check($person_cost($m, $second, 'Asha Sharma') === 600.0, 'Approval restamps at the current rate');

/* ---- and a later rate change cannot rewrite what an approved night cost ---- */
$m->save_cost_rate(4, array('scope' => 'USER', 'scope_id' => 1, 'hourly_rate' => '999', 'note' => 'Much later'));
check($person_cost($m, $second, 'Asha Sharma') === 600.0, 'Approved cost is history, not a live lookup');

/* ---- recalculation is the deliberate exception, and covers rows raised before the master existed ---- */
$restated = $m->recalculate_costs(4);
check($restated >= 4, 'Recalculation covers every person row in the location');
check($person_cost($m, $second, 'Asha Sharma') === 1998.0, 'Recalculation restates approved rows on request');
check($costs($m, $first) === 2118.0, 'Rows raised before any rate existed stop reading as zero');

/* ---- reports: day-wise totals, occasions, and the DF filter ---- */
$viewer = $m->user(139);
$window = array('from' => date('Y-m-d', strtotime('-1 day')), 'to' => date('Y-m-d', strtotime('+7 days')), 'status' => '', 'df_id' => 0, 'person' => '', 'employee_id' => 0, 'department_id' => 0);
$days = $m->grouped_report($viewer, $window, 'day');
check(count($days) === 2, 'One row per overtime date');
check($days[0]['label'] < $days[1]['label'], 'Day-wise rows read in date order');
check((int) $days[0]['request_count'] === 1 && (int) $days[0]['total'] === 2, 'Occasions count requests, person entries count people');
check(round((float) $days[1]['approved_cost'], 2) === 2318.0 && round((float) $days[1]['pending_cost'], 2) === 0.0, 'Approved cost lands on the approval day-row');
check(round((float) $days[0]['pending_cost'], 2) === 2118.0 && round((float) $days[0]['approved_cost'], 2) === 0.0, 'Pending cost is reported separately from approved');
$summary = $m->summary($viewer, $window);
check(round((float) $summary['approved_cost'], 2) === 2318.0, 'Summary approved cost matches the day rows');
check(round((float) $summary['requested_cost'], 2) === 4436.0, 'Requested cost covers every status');
$other_df = $m->grouped_report($viewer, array_merge($window, array('df_id' => 101)), 'day');
check($other_df === array(), 'Filtering to a DF with no overtime returns nothing');
$same_df = $m->grouped_report($viewer, array_merge($window, array('df_id' => 100)), 'df');
check(count($same_df) === 1 && (int) $same_df[0]['request_count'] === 2, 'DF grouping counts both occasions on DF-100');

/* ---- the cost master is administrator-only, and gated on its own permission ---- */
rejects(function () use ($m) { $m->save_cost_rate(1, array('scope' => 'LOCATION', 'scope_id' => 0, 'hourly_rate' => '10', 'note' => '')); }, 'A non-administrator with the grant cannot set rates');
rejects(function () use ($m) { $m->save_cost_rate(139, array('scope' => 'LOCATION', 'scope_id' => 0, 'hourly_rate' => '10', 'note' => '')); }, 'The approver has no master grant and cannot set rates');
rejects(function () use ($m) { $m->recalculate_costs(1); }, 'Recalculation is administrator-only');
foreach (array('-5', 'abc', '10.005', '') as $bad) {
    rejects(function () use ($m, $bad) { $m->save_cost_rate(4, array('scope' => 'LOCATION', 'scope_id' => 0, 'hourly_rate' => $bad, 'note' => '')); }, 'Rejects the rate "' . $bad . '"');
}
rejects(function () use ($m) { $m->save_cost_rate(4, array('scope' => 'USER', 'scope_id' => 6, 'hourly_rate' => '10', 'note' => '')); }, 'Rejects an employee from another business location');
rejects(function () use ($m) { $m->save_cost_rate(4, array('scope' => 'DEPARTMENT', 'scope_id' => 999, 'hourly_rate' => '10', 'note' => '')); }, 'Rejects an unknown department');
rejects(function () use ($m) { $m->save_cost_rate(4, array('scope' => 'EVERYONE', 'scope_id' => 0, 'hourly_rate' => '10', 'note' => '')); }, 'Rejects an invented scope');
rejects(function () use ($m) { $m->save_cost_rate(4, array('scope' => 'USER', 'scope_id' => 0, 'hourly_rate' => '10', 'note' => '')); }, 'Rejects an employee rate with no employee');

/* ---- removing a rate leaves the costs it already produced alone ---- */
$rates = $m->cost_rates(2);
check(count($rates) === 4 && $rates[0]['scope'] === 'LOCATION', 'The rates list runs from the broadest scope to the narrowest');
$user_rate = null;
foreach ($rates as $rate) if ($rate['scope'] === 'USER') $user_rate = $rate;
check($user_rate['scope_label'] === 'Asha Sharma', 'A user rate is labelled with the employee name');
$m->delete_cost_rate(4, $user_rate['id']);
check(count($m->cost_rates(2)) === 3, 'The rate is gone from the master');
check($person_cost($m, $second, 'Asha Sharma') === 1998.0, 'Stored costs survive the rate being removed');
rejects(function () use ($m, $user_rate) { $m->delete_cost_rate(4, $user_rate['id']); }, 'Removing the same rate twice is refused');

/* ---- the navigation badge ---- */
$badge = ot_nav_badge($db, 139);
check($badge['alert'] === true && $badge['count'] === 1, 'The approver sees one request waiting');
check(strpos($badge['title'], 'waiting for your approval') !== false, 'The badge says what it is counting');
$m->decide($first, 139, 'REJECT', 'Not needed after all.');
$cleared = ot_nav_badge($db, 139);
check($cleared['alert'] === false, 'With nothing pending the badge stops alerting');
$requester = ot_nav_badge($db, 2, ot_user_permissions($db, 2));
check($requester['alert'] === false && $requester['count'] > 0, 'Everybody else sees unread updates, not an alert');
$db->query('UPDATE overtime_notifications SET read_at=? WHERE user_id=2', array(date('Y-m-d H:i:s')));
check(ot_nav_badge($db, 2, ot_user_permissions($db, 2))['count'] === 0, 'A read notification leaves no badge');

/* ---- emails: built, addressed and logged, with SMTP stubbed out ---- */
class OtFakeEmail {
    public $sent = array(), $to, $subject, $message, $alt, $fail = false;
    public function clear($clear_attachments = false) {}
    public function initialize($config) {}
    public function from($address, $name = '') {}
    public function to($address) { $this->to = $address; }
    public function subject($subject) { $this->subject = $subject; }
    public function message($body) { $this->message = $body; }
    public function set_alt_message($body) { $this->alt = $body; }
    public function send($auto_clear = true) { if ($this->fail) return false; $this->sent[] = array($this->to, $this->subject, $this->message); return true; }
    public function print_debugger($include = array()) { return 'stub debugger output'; }
}
class OtFakeLoader { public function helper($name) {} public function library($name, $config = null, $alias = null) {} }
class OtFakeCI { public $load, $email, $overtime; }
$CI = new OtFakeCI();
$CI->load = new OtFakeLoader();
$CI->email = new OtFakeEmail();
$CI->overtime = $m;
// CodeIgniter's own get_instance() returns by reference; the stub must match.
function &get_instance() { global $CI; return $CI; }
require __DIR__ . '/../../application/libraries/Overtime_mailer.php';
$mailer = new Overtime_mailer();

$raised = $m->submit(2, ot_team_input('+3 days', array(1), 'Contract A'), $key());
check($mailer->request_raised($m->email_context($raised)) === true, 'The request email is sent');
check($CI->email->to === 'user139@example.test', 'The request email goes to the approver');
check(strpos($CI->email->subject, 'Overtime approval needed') === 0, 'The request subject says what is needed');
check(strpos($CI->email->message, 'DF-100') !== false, 'The request email names the DF');
check(strpos($CI->email->message, 'Contract A') !== false, 'The request email lists the manual worker');
check(strpos($CI->email->message, '420.00') !== false, 'The request email carries the cost at the rate that now applies');
check(strpos($CI->email->message, '<script>') === false, 'The request email escapes request content');
check(strpos($CI->email->alt, 'Overtime') !== false, 'A plain-text alternative is set');

$m->decide($raised, 139, 'APPROVE', 'Go ahead.');
check($mailer->decision($m->email_context($raised), 'APPROVED', 'Go ahead.') === true, 'The decision email is sent');
check($CI->email->to === 'user2@example.test', 'The decision email goes back to whoever raised it');
check(strpos($CI->email->subject, 'Overtime approved') === 0, 'The decision subject states the outcome');
check(strpos($CI->email->message, 'Go ahead.') !== false, 'Approval remarks are carried through');
check($mailer->decision($m->email_context($raised), 'CANCELLED', '') === false, 'Only approvals and rejections are emailed');

$log = $db->query('SELECT kind,recipient,status FROM overtime_email_log WHERE request_id=? ORDER BY id', array($raised))->result_array();
check(count($log) === 2 && $log[0]['kind'] === 'REQUESTED' && $log[0]['status'] === 'SENT', 'The request email is logged as sent');
check($log[1]['kind'] === 'APPROVED' && $log[1]['recipient'] === 'user2@example.test', 'The decision email is logged against its recipient');

$no_address = $m->email_context($raised);
$no_address['requester'] = $m->user(5);
check($mailer->decision($no_address, 'REJECTED', '') === false, 'A person with no email address is skipped, not fatal');
$skipped = $db->query("SELECT status,error FROM overtime_email_log WHERE request_id=? AND status='SKIPPED'", array($raised))->row_array();
check(strpos($skipped['error'], 'No email address') === 0, 'The skip reason is recorded');

$CI->email->fail = true;
check($mailer->request_raised($m->email_context($raised)) === false, 'A refused send does not throw');
$failed = $db->query("SELECT status FROM overtime_email_log WHERE request_id=? AND status='FAILED'", array($raised))->row_array();
check($failed && $failed['status'] === 'FAILED', 'A refused send is logged as failed');

echo "PASS: cost master resolution and stamping, historical cost stability, recalculation, day/DF cost reporting, master permission gates, navigation badge, and request/decision emails with logging.\n";
