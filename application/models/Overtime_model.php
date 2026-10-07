<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Overtime_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        // Let transaction failures roll back and reach the module's safe error handling.
        $this->db->db_debug = false;
    }

    public function module_ready()
    {
        foreach (array('requests', 'history', 'notifications', 'employee_locks', 'policies', 'leader_overrides', 'settings_history', 'assignments') as $name) {
            if (!$this->db->table_exists('overtime_' . $name)) return false;
        }
        foreach (array('overtime_cost_rates', 'overtime_email_log') as $name) if (!$this->db->table_exists($name)) return false;
        return $this->db->field_exists('df_id', 'overtime_requests') && $this->db->field_exists('cost_amount', 'overtime_assignments');
    }

    private function query($sql, $params = array())
    {
        $result = $this->db->query($sql, $params);
        if ($result === false) throw new RuntimeException('The operation could not be saved. Please try again.');
        return $result;
    }

    public function user($id)
    {
        return $this->query("SELECT u.user_id, u.first_name, u.last_name, u.business_location, u.department_id, u.email,
            COALESCE(r.isadmin,0) AS is_admin, d.department
            FROM system_users u LEFT JOIN user_role r ON r.user_role_id=u.user_role_id AND r.status=1
            LEFT JOIN departments d ON d.department_id=u.department_id
            WHERE u.user_id=? AND u.user_status=1", array((int) $id))->row_array();
    }

    public function users($location)
    {
        return $this->query('SELECT user_id, first_name, last_name FROM system_users WHERE business_location=? AND user_status=1 ORDER BY first_name,last_name', array((int) $location))->result_array();
    }

    public function can_request_for_team($user)
    {
        if (!$user) return false;
        if ((int) $user['is_admin'] === 1) return true;
        $params = array($user['user_id'], $user['business_location']);
        return (bool) $this->query('SELECT team_id FROM prestogroup_teams WHERE team_leader=? AND business_loc_id=? AND status=1 LIMIT 1', $params)->row_array()
            || (bool) $this->query('SELECT department_id FROM departments WHERE departmenthead=? AND business_loc_id=? AND status=1 LIMIT 1', $params)->row_array();
    }

    public function df_options()
    {
        return $this->query('SELECT id, df_no, df_description FROM df_release WHERE df_status=0 ORDER BY id DESC')->result_array();
    }

    public function assignments($id)
    {
        return $this->query('SELECT * FROM overtime_assignments WHERE request_id=? ORDER BY id', array((int)$id))->result_array();
    }

    public static function team_schedule($input)
    {
        $hours = isset($input['hours']) && is_scalar($input['hours']) ? (string)$input['hours'] : '';
        if (!preg_match('/^\d{1,2}(?:\.\d{1,2})?$/D', $hours) || (float)$hours <= 0 || (float)$hours > 24) {
            throw new InvalidArgumentException('Enter overtime hours greater than zero and no more than 24.');
        }
        $start = isset($input['start_at']) && is_string($input['start_at']) ? DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $input['start_at']) : false;
        if (!$start || $start->format('Y-m-d\TH:i') !== $input['start_at']) throw new InvalidArgumentException('Enter a valid overtime start date and time.');
        $minutes = (int)round((float)$hours * 60);
        if ($minutes < 1) throw new InvalidArgumentException('Overtime must be at least one minute.');
        $input['end_at'] = $start->modify('+' . $minutes . ' minutes')->format('Y-m-d\TH:i');
        $input['break_minutes'] = '0';
        return $input;
    }

    public function administrators($location, $exclude = array())
    {
        $rows = $this->query('SELECT u.user_id FROM system_users u JOIN user_role r ON r.user_role_id=u.user_role_id
            WHERE u.business_location=? AND u.user_status=1 AND r.status=1 AND r.isadmin=1', array((int) $location))->result_array();
        return array_values(array_filter(array_map(function ($r) { return (int) $r['user_id']; }, $rows), function ($id) use ($exclude) { return !in_array($id, $exclude, true) && ot_user_permissions($this->db, $id)['decide']; }));
    }

    public function leaders($user)
    {
        $override = $this->query('SELECT leader_id FROM overtime_leader_overrides WHERE employee_id=? AND business_location_id=?', array($user['user_id'], $user['business_location']))->row_array();
        if ($override) {
            $rows = array(array('user_id' => $override['leader_id']));
        } else {
            $rows = $this->query('SELECT DISTINCT t.team_leader AS user_id FROM presto_team_members m
                JOIN prestogroup_teams t ON t.team_id=m.team_id
                WHERE m.employee_id=? AND t.business_loc_id=? AND t.status=1', array($user['user_id'], $user['business_location']))->result_array();
            $heads_team = $this->query('SELECT team_id FROM prestogroup_teams WHERE team_leader=? AND business_loc_id=? AND status=1', array($user['user_id'], $user['business_location']))->row_array();
            if (!empty($user['is_admin']) || $heads_team) {
                $department = $this->query('SELECT departmenthead FROM departments WHERE department_id=? AND business_loc_id=? AND status=1', array($user['department_id'], $user['business_location']))->row_array();
                // A configured HOD takes priority; invalid/self mappings must be corrected,
                // rather than silently routing the request to another approver.
                if ($department && !empty($department['departmenthead'])) {
                    $rows = array(array('user_id' => $department['departmenthead']));
                }
            }
        }
        $leaders = array();
        foreach ($rows as $row) {
            $leader = $this->user($row['user_id']);
            if ($leader && (int) $leader['user_id'] !== (int) $user['user_id'] && (int) $leader['business_location'] === (int) $user['business_location']) $leaders[] = $leader;
        }
        return $leaders;
    }

    public function policy($location)
    {
        $policy = $this->query('SELECT * FROM overtime_policies WHERE business_location_id=?', array($location))->row_array();
        return $policy ?: array('max_request_minutes' => 720, 'max_daily_minutes' => 720, 'past_days' => 0, 'future_days' => 90);
    }

    /**
     * The overtime cost master. One rate row per scope; the most specific match is used:
     * a PMS user falls back to their department and then to the location default, and a
     * manual labour name falls back to the manual rate and then to the location default.
     * Department rates deliberately do not apply to manual workers - those are contract
     * rates, not salaried ones.
     */
    public function rate_map($location)
    {
        $map = array('USER' => array(), 'DEPARTMENT' => array(), 'MANUAL' => null, 'LOCATION' => null);
        foreach ($this->query('SELECT scope,scope_id,hourly_rate FROM overtime_cost_rates WHERE business_location_id=?', array((int) $location))->result_array() as $row) {
            if ($row['scope'] === 'USER' || $row['scope'] === 'DEPARTMENT') $map[$row['scope']][(int) $row['scope_id']] = (float) $row['hourly_rate'];
            elseif (array_key_exists($row['scope'], $map)) $map[$row['scope']] = (float) $row['hourly_rate'];
        }
        return $map;
    }

    public static function rate_for($map, $user_id, $department_id)
    {
        if ($user_id) {
            if (isset($map['USER'][(int) $user_id])) return $map['USER'][(int) $user_id];
            if (isset($map['DEPARTMENT'][(int) $department_id])) return $map['DEPARTMENT'][(int) $department_id];
        } elseif ($map['MANUAL'] !== null) {
            return $map['MANUAL'];
        }
        return $map['LOCATION'] === null ? 0.0 : $map['LOCATION'];
    }

    public static function cost_of($minutes, $rate)
    {
        return round((int) $minutes / 60 * (float) $rate, 2);
    }

    /** Rates as the master screen shows them, with the name each scope points at. */
    public function cost_rates($location)
    {
        return $this->query("SELECT c.*, CASE c.scope
                WHEN 'USER' THEN COALESCE(CONCAT(u.first_name,' ',u.last_name),CONCAT('User #',c.scope_id))
                WHEN 'DEPARTMENT' THEN COALESCE(d.department,CONCAT('Department #',c.scope_id))
                WHEN 'MANUAL' THEN 'Manual / contract labour'
                ELSE 'Everyone else in this location' END AS scope_label,
            a.first_name AS actor_first_name, a.last_name AS actor_last_name
            FROM overtime_cost_rates c
            LEFT JOIN system_users u ON u.user_id=c.scope_id AND c.scope='USER'
            LEFT JOIN departments d ON d.department_id=c.scope_id AND c.scope='DEPARTMENT'
            LEFT JOIN system_users a ON a.user_id=c.updated_by
            WHERE c.business_location_id=?
            ORDER BY CASE c.scope WHEN 'LOCATION' THEN 1 WHEN 'MANUAL' THEN 2 WHEN 'DEPARTMENT' THEN 3 ELSE 4 END, scope_label", array((int) $location))->result_array();
    }

    public function save_cost_rate($actor_id, $input)
    {
        if (!ot_user_permissions($this->db, $actor_id)['costs_edit']) throw new InvalidArgumentException('Overtime Cost Rates permission with Edit access is required.');
        $user = $this->user($actor_id);
        if (!$user || (int) $user['is_admin'] !== 1) throw new InvalidArgumentException('Administrator access is required to change cost rates.');
        $scope = isset($input['scope']) && is_scalar($input['scope']) ? strtoupper(trim((string) $input['scope'])) : '';
        if (!in_array($scope, array('LOCATION', 'MANUAL', 'DEPARTMENT', 'USER'), true)) throw new InvalidArgumentException('Choose a valid rate scope.');
        $scope_id = in_array($scope, array('LOCATION', 'MANUAL'), true) ? 0 : (isset($input['scope_id']) ? (int) $input['scope_id'] : 0);
        if (in_array($scope, array('DEPARTMENT', 'USER'), true) && $scope_id <= 0) throw new InvalidArgumentException('Select the department or employee this rate applies to.');
        if ($scope === 'USER') {
            $person = $this->user($scope_id);
            if (!$person || (int) $person['business_location'] !== (int) $user['business_location']) throw new InvalidArgumentException('Select an active employee in your business location.');
        }
        if ($scope === 'DEPARTMENT' && !$this->query('SELECT department_id FROM departments WHERE department_id=? AND business_loc_id=? AND status=1', array($scope_id, $user['business_location']))->row_array()) {
            throw new InvalidArgumentException('Select an active department in your business location.');
        }
        $rate = isset($input['hourly_rate']) && is_scalar($input['hourly_rate']) ? trim((string) $input['hourly_rate']) : '';
        if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $rate) || (float) $rate < 0) throw new InvalidArgumentException('Enter an hourly cost between 0 and 99,999,999 with at most two decimals.');
        $note = isset($input['note']) && is_scalar($input['note']) ? trim((string) $input['note']) : '';
        if (strlen($note) > 255) throw new InvalidArgumentException('The rate note must be at most 255 characters.');
        $this->transaction(function () use ($user, $scope, $scope_id, $rate, $note) {
            $this->query('INSERT INTO overtime_cost_rates (business_location_id,scope,scope_id,hourly_rate,note,updated_by,updated_at)
                VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE hourly_rate=VALUES(hourly_rate),note=VALUES(note),updated_by=VALUES(updated_by),updated_at=VALUES(updated_at)',
                array($user['business_location'], $scope, $scope_id, $rate, $note, $user['user_id'], date('Y-m-d H:i:s')));
            $this->settings_audit($user, 'COST_RATE_SAVED', $scope . ' #' . $scope_id . ' = ' . $rate . ' per hour. ' . $note);
        });
    }

    public function delete_cost_rate($actor_id, $rate_id)
    {
        if (!ot_user_permissions($this->db, $actor_id)['costs_edit']) throw new InvalidArgumentException('Overtime Cost Rates permission with Edit access is required.');
        $user = $this->user($actor_id);
        if (!$user || (int) $user['is_admin'] !== 1) throw new InvalidArgumentException('Administrator access is required to change cost rates.');
        $rate = $this->query('SELECT * FROM overtime_cost_rates WHERE id=? AND business_location_id=?', array((int) $rate_id, $user['business_location']))->row_array();
        if (!$rate) throw new InvalidArgumentException('That cost rate no longer exists in your business location.');
        $this->transaction(function () use ($user, $rate) {
            $this->query('DELETE FROM overtime_cost_rates WHERE id=? AND business_location_id=?', array((int) $rate['id'], $user['business_location']));
            $this->settings_audit($user, 'COST_RATE_REMOVED', $rate['scope'] . ' #' . $rate['scope_id'] . ' was ' . $rate['hourly_rate'] . ' per hour.');
        });
    }

    /**
     * Costs are stamped on the person row when the request is raised and again when it is
     * approved, so a later rate change cannot rewrite what a finished night cost. That also
     * means rows created before the cost master existed read as zero, which is what this
     * restamps - deliberately, on request, with an audit entry.
     */
    public function recalculate_costs($actor_id)
    {
        if (!ot_user_permissions($this->db, $actor_id)['costs_edit']) throw new InvalidArgumentException('Overtime Cost Rates permission with Edit access is required.');
        $user = $this->user($actor_id);
        if (!$user || (int) $user['is_admin'] !== 1) throw new InvalidArgumentException('Administrator access is required to recalculate costs.');
        return $this->transaction(function () use ($user) {
            $map = $this->rate_map($user['business_location']);
            $rows = $this->query('SELECT w.id, w.user_id, w.department_id, r.requested_minutes FROM overtime_assignments w
                JOIN overtime_requests r ON r.id=w.request_id WHERE r.business_location_id=?', array((int) $user['business_location']))->result_array();
            $changed = 0;
            foreach ($rows as $row) {
                $rate = self::rate_for($map, $row['user_id'], $row['department_id']);
                $this->query('UPDATE overtime_assignments SET hourly_rate=?,cost_amount=? WHERE id=?', array($rate, self::cost_of($row['requested_minutes'], $rate), (int) $row['id']));
                $changed++;
            }
            $this->settings_audit($user, 'COST_RECALCULATED', $changed . ' person rows restamped at the current rates.');
            return $changed;
        });
    }

    private function stamp_costs($request_id, $location, $minutes)
    {
        $map = $this->rate_map($location);
        foreach ($this->query('SELECT id,user_id,department_id FROM overtime_assignments WHERE request_id=?', array((int) $request_id))->result_array() as $row) {
            $rate = self::rate_for($map, $row['user_id'], $row['department_id']);
            $this->query('UPDATE overtime_assignments SET hourly_rate=?,cost_amount=? WHERE id=?', array($rate, self::cost_of($minutes, $rate), (int) $row['id']));
        }
    }

    public function log_email($request_id, $kind, $recipient, $subject, $status, $error = '')
    {
        $this->db->insert('overtime_email_log', array('request_id' => (int) $request_id, 'kind' => $kind, 'recipient' => substr((string) $recipient, 0, 255),
            'subject' => substr((string) $subject, 0, 255), 'status' => $status, 'error' => substr((string) $error, 0, 60000), 'created_at' => date('Y-m-d H:i:s')));
    }

    /** Everything the request and decision emails need, resolved in one place. */
    public function email_context($id)
    {
        $request = $this->get_request($id);
        if (!$request) return null;
        $request['requester'] = $this->user($request['employee_id']);
        $request['approver'] = $this->user(139);
        $request['people'] = $request['assignments'];
        return $request;
    }

    public static function validate_request($input, $policy, $today = null)
    {
        $today = $today ?: date('Y-m-d');
        $dates = array();
        foreach (array('start_at', 'end_at') as $key) {
            $raw = isset($input[$key]) && is_scalar($input[$key]) ? (string) $input[$key] : '';
            $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $raw);
            if (!$date || $date->format('Y-m-d\TH:i') !== $raw) throw new InvalidArgumentException('Enter valid start and end dates and times.');
            $dates[$key] = $date;
        }
        $elapsed = (int) (($dates['end_at']->getTimestamp() - $dates['start_at']->getTimestamp()) / 60);
        $break = isset($input['break_minutes']) ? filter_var($input['break_minutes'], FILTER_VALIDATE_INT) : false;
        if ($elapsed <= 0 || $elapsed > 1440) throw new InvalidArgumentException('End time must be after start time and within 24 hours. Overnight requests are supported.');
        if ($break === false || $break < 0 || $break >= $elapsed) throw new InvalidArgumentException('Break minutes must be zero or more and less than the total duration.');
        $minutes = $elapsed - $break;
        if ($minutes > (int) $policy['max_request_minutes']) throw new InvalidArgumentException('Requested overtime exceeds the per-request limit.');
        $first = (new DateTimeImmutable($today))->modify('-' . (int) $policy['past_days'] . ' days')->format('Y-m-d');
        $last = (new DateTimeImmutable($today))->modify('+' . (int) $policy['future_days'] . ' days')->format('Y-m-d');
        if ($dates['start_at']->format('Y-m-d') < $first || $dates['start_at']->format('Y-m-d') > $last) throw new InvalidArgumentException('Overtime start date is outside the allowed request window.');
        $reason = isset($input['reason']) && is_scalar($input['reason']) ? trim((string) $input['reason']) : '';
        $reference = isset($input['work_reference']) && is_scalar($input['work_reference']) ? trim((string) $input['work_reference']) : '';
        if (strlen($reason) < 10 || strlen($reason) > 4000) throw new InvalidArgumentException('Provide a reason between 10 and 4,000 characters.');
        if (strlen($reference) > 255) throw new InvalidArgumentException('Work reference must be at most 255 characters.');
        return array('start_at' => $dates['start_at']->format('Y-m-d H:i:s'), 'end_at' => $dates['end_at']->format('Y-m-d H:i:s'), 'break_minutes' => $break,
            'requested_minutes' => $minutes, 'reason' => $reason, 'work_reference' => $reference);
    }

    private function transaction($callback)
    {
        if ($this->db->trans_begin() === false) throw new RuntimeException('Unable to start a database transaction. Please try again.');
        try {
            $result = $callback();
            if ($this->db->trans_status() === false) throw new RuntimeException('The operation could not be saved. Please try again.');
            if ($this->db->trans_commit() === false) throw new RuntimeException('The operation could not be saved. Please try again.');
            return $result;
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            throw $e;
        }
    }

    private function lock_employee($id)
    {
        // A no-op upsert takes an exclusive lock directly; INSERT IGNORE can take
        // shared duplicate-key locks that deadlock when concurrent callers upgrade.
        $this->query('INSERT INTO overtime_employee_locks (employee_id) VALUES (?) ON DUPLICATE KEY UPDATE employee_id=VALUES(employee_id)', array($id));
        $this->query('SELECT employee_id FROM overtime_employee_locks WHERE employee_id=? FOR UPDATE', array($id));
    }

    private function insert($table, $data)
    {
        if (!$this->db->insert($table, $data)) throw new RuntimeException('The operation could not be saved. Please try again.');
        return (int) $this->db->insert_id();
    }

    private function record_history($id, $actor, $action, $from, $to, $note)
    {
        $this->insert('overtime_history', array('request_id' => $id, 'actor_id' => $actor, 'action' => $action, 'from_status' => $from, 'to_status' => $to, 'note' => $note, 'created_at' => date('Y-m-d H:i:s')));
    }

    private function notify($id, $users, $message)
    {
        foreach (array_unique($users) as $user) $this->insert('overtime_notifications', array('request_id' => $id, 'user_id' => $user, 'message' => $message, 'created_at' => date('Y-m-d H:i:s')));
    }

    public function submit($actor_id, $input, $key)
    {
        if (!is_string($key) || !preg_match('/^[a-f0-9]{64}$/D', $key)) throw new InvalidArgumentException('Invalid submission reference. Reload the form.');
        return $this->transaction(function () use ($actor_id, $input, $key) {
            $ids = isset($input['user_ids']) && is_array($input['user_ids']) ? $input['user_ids'] : array();
            foreach ($ids as $id) {
                if (!is_scalar($id) || !ctype_digit((string)$id) || (int)$id <= 0) throw new InvalidArgumentException('Select valid employees.');
            }
            $ids = array_values(array_unique(array_map('intval', $ids)));
            if (count($ids) > 100) throw new InvalidArgumentException('Select no more than 100 people per request.');
            // Always acquire all user locks in ascending order, including the requester,
            // so simultaneous multi-person requests cannot deadlock each other.
            $locks = array_unique(array_merge($ids, array((int)$actor_id))); sort($locks, SORT_NUMERIC);
            foreach ($locks as $lock) $this->lock_employee($lock);
            $user = $this->user($actor_id);
            if (!$user || empty($user['business_location']) || empty($user['department_id'])) throw new InvalidArgumentException('Your active user profile must have a business location and department.');
            if (!ot_user_permissions($this->db, $actor_id)['create']) throw new InvalidArgumentException('Overtime Requests permission with Add access is required.');
            if (!$this->can_request_for_team($user)) throw new InvalidArgumentException('Only an active team leader, department head or administrator can raise overtime requests.');
            $existing = $this->query('SELECT id FROM overtime_requests WHERE employee_id=? AND submission_key=?', array($actor_id, $key))->row_array();
            if ($existing) return (int) $existing['id'];
            $policy = $this->policy($user['business_location']);
            $data = self::validate_request(self::team_schedule($input), $policy);
            $df_id = isset($input['df_id']) && is_scalar($input['df_id']) ? filter_var($input['df_id'], FILTER_VALIDATE_INT) : false;
            if (!$df_id || !$this->query('SELECT id FROM df_release WHERE id=? AND df_status=0', array($df_id))->row_array()) throw new InvalidArgumentException('Select a valid active DF number.');
            if (!$this->user(139)) throw new InvalidArgumentException('Shubham Sharma (user 139) must have an active account for approval.');
            $people = array();
            foreach ($ids as $id) {
                $person = $this->user($id);
                if (!$person || (int)$person['business_location'] !== (int)$user['business_location']) throw new InvalidArgumentException('Select active employees in your business location.');
                $people[] = array('user_id'=>$id, 'person_name'=>trim($person['first_name'].' '.$person['last_name']), 'department_id'=>(int)$person['department_id']);
            }
            $manual = isset($input['manual_people']) && is_string($input['manual_people']) ? trim($input['manual_people']) : '';
            if (strlen($manual) > 20000) throw new InvalidArgumentException('Manual worker names are too long.');
            $names = array();
            foreach (preg_split('/\r\n|\r|\n/', $manual) as $name) {
                $name = trim($name);
                if ($name === '') continue;
                if (strlen($name) > 200) throw new InvalidArgumentException('Each manual worker name must be at most 200 characters.');
                $normalized = strtolower($name);
                if (isset($names[$normalized])) throw new InvalidArgumentException('A manual worker is listed more than once. Use distinct names or include a contractor / worker reference.');
                $names[$normalized] = true;
                $people[] = array('user_id'=>null, 'person_name'=>$name, 'department_id'=>(int)$user['department_id']);
            }
            if (!$people || count($people) > 100) throw new InvalidArgumentException('Select or enter between 1 and 100 people.');
            $day = substr($data['start_at'], 0, 10);
            $next = (new DateTimeImmutable($day))->modify('+1 day')->format('Y-m-d');
            foreach ($ids as $id) {
                $person_where = '(r.df_id IS NULL AND r.employee_id=? OR EXISTS (SELECT 1 FROM overtime_assignments a WHERE a.request_id=r.id AND a.user_id=?))';
                $overlap = $this->query("SELECT r.id FROM overtime_requests r WHERE $person_where AND r.status IN ('PENDING_LEADER','PENDING_ADMIN','APPROVED') AND r.start_at < ? AND r.end_at > ? LIMIT 1", array($id, $id, $data['end_at'], $data['start_at']))->row_array();
                if ($overlap) throw new InvalidArgumentException('Employee #' . $id . ' already has overlapping pending or approved overtime.');
                $total = $this->query("SELECT COALESCE(SUM(r.requested_minutes),0) AS minutes FROM overtime_requests r WHERE $person_where AND r.start_at>=? AND r.start_at<? AND r.status IN ('PENDING_LEADER','PENDING_ADMIN','APPROVED')", array($id, $id, $day.' 00:00:00', $next.' 00:00:00'))->row_array();
                if ((int)$total['minutes'] + $data['requested_minutes'] > (int)$policy['max_daily_minutes']) throw new InvalidArgumentException('Employee #' . $id . ' exceeds the daily overtime limit, including pending requests.');
            }
            $now = date('Y-m-d H:i:s');
            $data = array_merge($data, array('request_code' => 'OT-' . strtoupper(bin2hex(random_bytes(8))), 'employee_id' => $actor_id,
                'df_id'=>$df_id, 'business_location_id' => $user['business_location'], 'department_id' => $user['department_id'], 'leader_id' => $actor_id,
                'status' => 'PENDING_ADMIN', 'submission_key' => $key, 'created_at' => $now, 'updated_at' => $now));
            $id = $this->insert('overtime_requests', $data);
            $rates = $this->rate_map($user['business_location']);
            foreach ($people as $person) {
                $rate = self::rate_for($rates, $person['user_id'], $person['department_id']);
                $this->insert('overtime_assignments', array_merge($person, array('request_id'=>$id, 'hourly_rate'=>$rate, 'cost_amount'=>self::cost_of($data['requested_minutes'], $rate))));
            }
            $this->record_history($id, $actor_id, 'SUBMITTED', '', 'PENDING_ADMIN', 'Submitted to Shubham Sharma. ' . count($people) . ' people; ' . $data['requested_minutes'] . ' minutes each.');
            $this->notify($id, array(139), $data['request_code'] . ' needs your approval.');
            return $id;
        });
    }

    public static function can_view($request, $user)
    {
        if (!$user) return false;
        if ($user && (int)$user['user_id'] === 139) return true;
        $assigned = false;
        foreach (isset($request['assignments']) ? $request['assignments'] : array() as $person) {
            if ((int)$person['user_id'] === (int)$user['user_id'] && !empty($person['assigned_at'])) $assigned = true;
        }
        return $user && (int) $request['business_location_id'] === (int) $user['business_location'] &&
            ((int) $request['employee_id'] === (int) $user['user_id'] || (int) $request['leader_id'] === (int) $user['user_id'] || (int) $user['is_admin'] === 1 || $assigned);
    }

    public static function can_decide($request, $user)
    {
        return $user && (int)$user['user_id'] === 139 && in_array($request['status'], array('PENDING_LEADER','PENDING_ADMIN'), true);
    }

    public function get_request($id)
    {
        $request = $this->query('SELECT r.*, u.first_name, u.last_name, d.department, df.df_no, l.first_name AS leader_first_name, l.last_name AS leader_last_name
            FROM overtime_requests r LEFT JOIN system_users u ON u.user_id=r.employee_id
            LEFT JOIN df_release df ON df.id=r.df_id
            LEFT JOIN departments d ON d.department_id=r.department_id LEFT JOIN system_users l ON l.user_id=r.leader_id WHERE r.id=?', array((int) $id))->row_array();
        if ($request) $request['assignments'] = $this->assignments($id);
        return $request;
    }

    public function decide($id, $actor_id, $decision, $note)
    {
        if (!in_array($decision, array('APPROVE', 'REJECT', 'CANCEL'), true)) throw new InvalidArgumentException('Invalid action.');
        $note = trim((string) $note);
        if (strlen($note) > 4000 || ($decision === 'CANCEL' && strlen($note) < 5)) throw new InvalidArgumentException('Cancellation requires a reason of at least 5 characters. Remarks must be at most 4,000 characters.');
        return $this->transaction(function () use ($id, $actor_id, $decision, $note) {
            $initial = $this->get_request($id);
            if (!$initial) throw new InvalidArgumentException('Request not found.');
            $this->lock_employee($initial['employee_id']);
            $request = $this->query('SELECT * FROM overtime_requests WHERE id=? FOR UPDATE', array((int) $id))->row_array();
            $user = $this->user($actor_id);
            if (!self::can_view($request, $user)) throw new InvalidArgumentException('You do not have access to this request.');
            if (!ot_user_permissions($this->db, $actor_id)[$decision === 'CANCEL' ? 'create' : 'decide']) throw new InvalidArgumentException('The required overtime action permission is not enabled.');
            $now = date('Y-m-d H:i:s');
            $data = array('updated_at' => $now);
            if ($decision === 'CANCEL') {
                if ((int) $request['employee_id'] !== (int) $actor_id || !in_array($request['status'], array('PENDING_LEADER', 'PENDING_ADMIN', 'APPROVED'), true)) throw new InvalidArgumentException('Only the requester can cancel an active request.');
                if ($request['status'] === 'APPROVED' && $request['start_at'] <= $now) throw new InvalidArgumentException('Approved overtime cannot be cancelled after its start time.');
                $data['status'] = 'CANCELLED';
            } else {
                if (!self::can_decide($request, $user)) throw new InvalidArgumentException('This request is no longer awaiting your approval, or you are not an eligible approver.');
                $data['admin_decided_by'] = $actor_id;
                $data['admin_decided_at'] = $now;
                $data['status'] = $decision === 'REJECT' ? 'REJECTED' : 'APPROVED';
            }
            if (!$this->db->where('id', (int) $id)->where('status', $request['status'])->update('overtime_requests', $data) || $this->db->affected_rows() !== 1) throw new RuntimeException('The request changed. Reload and try again.');
            $this->record_history($id, $actor_id, $decision, $request['status'], $data['status'], $note);
            $recipients = array($request['employee_id'], $request['leader_id']);
            if ($data['status'] === 'APPROVED') {
                $this->query('UPDATE overtime_assignments SET assigned_at=? WHERE request_id=?', array($now, $id));
                $this->stamp_costs($id, $request['business_location_id'], $request['requested_minutes']);
            }
            foreach ($this->query('SELECT * FROM overtime_assignments WHERE request_id=? FOR UPDATE', array($id))->result_array() as $person) {
                if ($person['user_id'] && $person['assigned_at']) $recipients[] = (int)$person['user_id'];
            }
            if ($decision === 'CANCEL') $recipients[] = 139;
            $this->notify($id, $recipients, $request['request_code'] . ': ' . str_replace('_', ' ', $data['status']) . '.');
            return $data['status'];
        });
    }

    public function history($id) // Public read-only audit trail.
    {
        return $this->query('SELECT h.*, u.first_name, u.last_name FROM overtime_history h LEFT JOIN system_users u ON u.user_id=h.actor_id WHERE h.request_id=? ORDER BY h.id', array($id))->result_array();
    }

    public function reassign_leader($id, $actor_id, $leader_id, $note)
    {
        $note = trim((string) $note);
        if (strlen($note) < 5 || strlen($note) > 1000) throw new InvalidArgumentException('Provide a reassignment reason between 5 and 1,000 characters.');
        $this->transaction(function () use ($id, $actor_id, $leader_id, $note) {
            $initial = $this->get_request($id);
            if (!$initial) throw new InvalidArgumentException('Request not found.');
            $this->lock_employee($initial['employee_id']);
            $request = $this->query('SELECT * FROM overtime_requests WHERE id=? FOR UPDATE', array($id))->row_array();
            $actor = $this->user($actor_id); $leader = $this->user($leader_id);
            if (!ot_user_permissions($this->db, $actor_id)['leaders_edit']) throw new InvalidArgumentException('Reporting Leaders permission with Edit access is required.');
            if (!self::can_view($request, $actor) || (int) $actor['is_admin'] !== 1 || $request['status'] !== 'PENDING_LEADER') throw new InvalidArgumentException('Only an administrator can reassign a request awaiting team leader approval.');
            if (!$leader || (int) $leader['business_location'] !== (int) $request['business_location_id'] || (int) $leader_id === (int) $request['employee_id'] || (int) $leader_id === (int) $request['leader_id']) throw new InvalidArgumentException('Choose a different active leader in this business location, other than the requester.');
            if (!ot_user_permissions($this->db, $leader_id)['decide']) throw new InvalidArgumentException('The replacement leader needs Overtime Approvals permission with Edit access.');
            if (!$this->administrators($request['business_location_id'], array((int) $request['employee_id'], (int) $leader_id))) throw new InvalidArgumentException('An independent administrator must remain available for final approval.');
            $this->query('UPDATE overtime_requests SET leader_id=?,updated_at=? WHERE id=?', array($leader_id, date('Y-m-d H:i:s'), $id));
            $this->record_history($id, $actor_id, 'LEADER_REASSIGNED', $request['status'], $request['status'], 'Leader #' . $request['leader_id'] . ' → #' . $leader_id . '. ' . $note);
            $this->notify($id, array($leader_id, $request['employee_id']), $request['request_code'] . ': reporting team leader reassigned; awaiting review.');
        });
    }

    private function scope($user, $filters, &$params)
    {
        $params = array(); $where = '1=1';
        $assigned = 'EXISTS (SELECT 1 FROM overtime_assignments visible_worker WHERE visible_worker.request_id=r.id AND visible_worker.user_id=? AND visible_worker.assigned_at IS NOT NULL)';
        if ((int)$user['user_id'] !== 139) {
            $where .= ' AND r.business_location_id=?'; $params[] = (int)$user['business_location'];
            if ((int) $user['is_admin'] !== 1) {
                $where .= ' AND (r.employee_id=? OR r.leader_id=? OR ' . $assigned . ')';
                $params[] = $user['user_id']; $params[] = $user['user_id']; $params[] = $user['user_id'];
            }
        }
        if (!empty($filters['mine'])) {
            $where .= ' AND (r.employee_id=? OR ' . $assigned . ')';
            $params[] = $user['user_id']; $params[] = $user['user_id'];
        }
        if (!empty($filters['inbox'])) {
            $where .= (int)$user['user_id'] === 139 ? " AND r.status IN ('PENDING_LEADER','PENDING_ADMIN')" : ' AND 1=0';
        }
        foreach (array('status' => 'r.status', 'df_id' => 'r.df_id') as $key => $column) {
            if (!empty($filters[$key])) { $where .= ' AND ' . $column . '=?'; $params[] = $filters[$key]; }
        }
        if (!empty($filters['from'])) { $where .= ' AND r.start_at>=?'; $params[] = $filters['from'] . ' 00:00:00'; }
        if (!empty($filters['to'])) { $where .= ' AND r.start_at<?'; $params[] = (new DateTimeImmutable($filters['to']))->modify('+1 day')->format('Y-m-d') . ' 00:00:00'; }
        return $where;
    }

    // One row per selected person; legacy self-requests contribute one person.
    private function report_from()
    {
        return ' FROM overtime_requests r LEFT JOIN overtime_assignments w ON w.request_id=r.id
            LEFT JOIN system_users u ON u.user_id=r.employee_id
            LEFT JOIN departments d ON d.department_id=COALESCE(w.department_id,r.department_id)
            LEFT JOIN df_release df ON df.id=r.df_id ';
    }

    private function report_scope($user, $filters, &$params)
    {
        $where = $this->scope($user, $filters, $params);
        if (!empty($filters['employee_id'])) {
            $where .= ' AND (w.user_id=? OR (w.id IS NULL AND r.employee_id=?))';
            $params[] = $filters['employee_id']; $params[] = $filters['employee_id'];
        }
        if (!empty($filters['department_id'])) { $where .= ' AND COALESCE(w.department_id,r.department_id)=CAST(? AS UNSIGNED)'; $params[] = $filters['department_id']; }
        if (!empty($filters['person'])) {
            $where .= " AND COALESCE(w.person_name,CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) LIKE ? ESCAPE '!'";
            $params[] = '%' . str_replace(array('!','%','_'), array('!!','!%','!_'), $filters['person']) . '%';
        }
        return $where;
    }

    public function listing($user, $filters, $limit = 50, $offset = 0)
    {
        $params = array(); $where = $this->report_scope($user, $filters, $params);
        return $this->query("SELECT r.*, u.first_name, u.last_name, d.department, df.df_no,
            COALESCE(w.person_name,CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,''))) AS person_name,
            CASE WHEN w.id IS NULL THEN r.employee_id ELSE w.user_id END AS worker_id,
            w.assigned_at, COALESCE(w.hourly_rate,0) AS hourly_rate, COALESCE(w.cost_amount,0) AS cost_amount" . $this->report_from() . ' WHERE ' . $where .
            ' ORDER BY r.created_at DESC,r.id DESC,w.id LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset, $params)->result_array();
    }

    public function summary($user, $filters)
    {
        $params = array(); $where = $this->report_scope($user, $filters, $params);
        return $this->query("SELECT COUNT(*) AS total, COUNT(DISTINCT r.id) AS request_count, COALESCE(SUM(requested_minutes),0) AS requested_minutes,
            COALESCE(SUM(CASE WHEN r.status='APPROVED' THEN requested_minutes ELSE 0 END),0) AS approved_minutes,
            COALESCE(SUM(CASE WHEN r.status IN ('PENDING_LEADER','PENDING_ADMIN') THEN 1 ELSE 0 END),0) AS pending_count,
            COALESCE(SUM(CASE WHEN r.status IN ('PENDING_LEADER','PENDING_ADMIN') THEN requested_minutes ELSE 0 END),0) AS pending_minutes,
            COALESCE(SUM(CASE WHEN r.status='REJECTED' THEN 1 ELSE 0 END),0) AS rejected_count,
            COALESCE(SUM(w.cost_amount),0) AS requested_cost,
            COALESCE(SUM(CASE WHEN r.status='APPROVED' THEN w.cost_amount ELSE 0 END),0) AS approved_cost,
            COALESCE(SUM(CASE WHEN r.status IN ('PENDING_LEADER','PENDING_ADMIN') THEN w.cost_amount ELSE 0 END),0) AS pending_cost
            " . $this->report_from() . ' WHERE ' . $where, $params)->row_array();
    }

    public function grouped_report($user, $filters, $group)
    {
        $params = array(); $where = $this->report_scope($user, $filters, $params);
        $column = 'CASE WHEN w.id IS NULL THEN r.employee_id ELSE COALESCE(w.user_id,0) END';
        $name = "COALESCE(w.person_name,CONCAT(COALESCE(u.first_name,''),' ',COALESCE(u.last_name,'')))";
        // Day-wise totals read as a diary, so they are ordered by date rather than by size.
        $order = 'approved_minutes DESC, label';
        if ($group === 'department') { $column = 'COALESCE(w.department_id,r.department_id)'; $name = "COALESCE(d.department,'Unassigned')"; }
        if ($group === 'df') { $column = 'COALESCE(r.df_id,0)'; $name = "COALESCE(df.df_no,'Legacy / no DF')"; }
        if ($group === 'day') { $column = 'DATE(r.start_at)'; $name = 'DATE(r.start_at)'; $order = 'label'; }
        return $this->query("SELECT $column AS group_id, $name AS label, COUNT(*) AS total,
            COUNT(DISTINCT r.id) AS request_count,
            SUM(requested_minutes) AS requested_minutes,
            SUM(CASE WHEN r.status='APPROVED' THEN requested_minutes ELSE 0 END) AS approved_minutes,
            SUM(CASE WHEN r.status IN ('PENDING_LEADER','PENDING_ADMIN') THEN requested_minutes ELSE 0 END) AS pending_minutes,
            COALESCE(SUM(w.cost_amount),0) AS requested_cost,
            COALESCE(SUM(CASE WHEN r.status='APPROVED' THEN w.cost_amount ELSE 0 END),0) AS approved_cost,
            COALESCE(SUM(CASE WHEN r.status IN ('PENDING_LEADER','PENDING_ADMIN') THEN w.cost_amount ELSE 0 END),0) AS pending_cost
            " . $this->report_from() . " WHERE $where GROUP BY $column, $name ORDER BY $order", $params)->result_array();
    }

    public function filter_options($user)
    {
        $params = array(); $where = $this->scope($user, array(), $params);
        $employees = $this->query('SELECT DISTINCT worker.user_id, worker.first_name, worker.last_name ' . $this->report_from() . ' JOIN system_users worker ON worker.user_id=CASE WHEN w.id IS NULL THEN r.employee_id ELSE w.user_id END WHERE ' . $where . ' ORDER BY worker.first_name,worker.last_name', $params)->result_array();
        $departments = $this->query('SELECT DISTINCT d.department_id, d.department ' . $this->report_from() . ' WHERE d.department_id IS NOT NULL AND ' . $where . ' ORDER BY d.department', $params)->result_array();
        $dfs = $this->query('SELECT DISTINCT df.id, df.df_no FROM overtime_requests r JOIN df_release df ON df.id=r.df_id WHERE ' . $where . ' ORDER BY df.df_no', $params)->result_array();
        return array('employees' => $employees, 'departments' => $departments, 'dfs'=>$dfs);
    }

    public function notifications($user)
    {
        $params = array(); $where = $this->scope($user, array(), $params);
        $params[] = $user['user_id'];
        return $this->query('SELECT n.* FROM overtime_notifications n JOIN overtime_requests r ON r.id=n.request_id WHERE ' . $where . ' AND n.user_id=? AND n.read_at IS NULL ORDER BY n.id DESC LIMIT 20', $params)->result_array();
    }

    public function mark_read($user_id, $id)
    {
        $this->query('UPDATE overtime_notifications SET read_at=? WHERE id=? AND user_id=?', array(date('Y-m-d H:i:s'), $id, $user_id));
    }

    public function save_policy($actor_id, $input)
    {
        if (!ot_user_permissions($this->db, $actor_id)['policy_edit']) throw new InvalidArgumentException('Request Limits permission with Edit access is required.');
        $user = $this->user($actor_id);
        if (!$user || (int) $user['is_admin'] !== 1) throw new InvalidArgumentException('Administrator access required.');
        $data = array();
        foreach (array('max_request_minutes' => array(1,1440), 'max_daily_minutes' => array(1,1440), 'past_days' => array(0,365), 'future_days' => array(0,365)) as $key => $range) {
            $value = isset($input[$key]) ? filter_var($input[$key], FILTER_VALIDATE_INT) : false;
            if ($value === false || $value < $range[0] || $value > $range[1]) throw new InvalidArgumentException('Enter valid policy limits: minutes 1–1440 and date windows 0–365 days.');
            $data[$key] = $value;
        }
        if ($data['max_request_minutes'] > $data['max_daily_minutes']) throw new InvalidArgumentException('Daily limit must be at least the per-request limit.');
        $this->transaction(function () use ($user, $data, $actor_id) {
            $this->query('INSERT INTO overtime_policies (business_location_id,max_request_minutes,max_daily_minutes,past_days,future_days,updated_by,updated_at)
                VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE max_request_minutes=VALUES(max_request_minutes),max_daily_minutes=VALUES(max_daily_minutes),past_days=VALUES(past_days),future_days=VALUES(future_days),updated_by=VALUES(updated_by),updated_at=VALUES(updated_at)',
                array($user['business_location'], $data['max_request_minutes'], $data['max_daily_minutes'], $data['past_days'], $data['future_days'], $actor_id, date('Y-m-d H:i:s')));
            $this->settings_audit($user, 'POLICY_UPDATED', json_encode($data));
        });
    }

    public function save_override($actor_id, $employee_id, $leader_id, $reason)
    {
        if (!ot_user_permissions($this->db, $actor_id)['leaders_edit']) throw new InvalidArgumentException('Reporting Leaders permission with Edit access is required.');
        $user = $this->user($actor_id); $employee = $this->user($employee_id); $leader = $leader_id ? $this->user($leader_id) : null;
        if (!$user || (int) $user['is_admin'] !== 1 || !$employee || (int) $employee['business_location'] !== (int) $user['business_location']) throw new InvalidArgumentException('Select an active employee in your business location. Administrator access is required.');
        if ($leader_id && (!$leader || (int) $leader['business_location'] !== (int) $user['business_location'] || (int) $employee_id === (int) $leader_id)) throw new InvalidArgumentException('Choose a different active reporting leader in this business location.');
        $reason = trim((string) $reason);
        if (strlen($reason) < 5 || strlen($reason) > 1000) throw new InvalidArgumentException('Provide an override reason between 5 and 1,000 characters.');
        $this->transaction(function () use ($user, $employee_id, $leader_id, $reason) {
            $this->lock_employee($employee_id);
            if ($leader_id) {
                $this->query('INSERT INTO overtime_leader_overrides (employee_id,business_location_id,leader_id,updated_by,updated_at) VALUES (?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE leader_id=VALUES(leader_id),business_location_id=VALUES(business_location_id),updated_by=VALUES(updated_by),updated_at=VALUES(updated_at)', array($employee_id, $user['business_location'], $leader_id, $user['user_id'], date('Y-m-d H:i:s')));
            } else {
                $this->query('DELETE FROM overtime_leader_overrides WHERE employee_id=? AND business_location_id=?', array($employee_id, $user['business_location']));
            }
            $this->settings_audit($user, 'LEADER_OVERRIDE', 'Employee #' . $employee_id . ', leader #' . $leader_id . ' (0 restores team mapping). ' . $reason);
        });
    }

    private function settings_audit($user, $action, $note)
    {
        $this->insert('overtime_settings_history', array('business_location_id' => $user['business_location'], 'actor_id' => $user['user_id'], 'action' => $action, 'note' => $note, 'created_at' => date('Y-m-d H:i:s')));
    }

    public function settings_data($location)
    {
        return array('overrides' => $this->query('SELECT o.*, u.first_name, u.last_name, l.first_name AS leader_first_name, l.last_name AS leader_last_name FROM overtime_leader_overrides o
            LEFT JOIN system_users u ON u.user_id=o.employee_id LEFT JOIN system_users l ON l.user_id=o.leader_id WHERE o.business_location_id=? ORDER BY u.first_name', array($location))->result_array(),
            'rates' => $this->cost_rates($location),
            'departments' => $this->query('SELECT department_id, department FROM departments WHERE business_loc_id=? AND status=1 ORDER BY department', array((int) $location))->result_array(),
            'audit' => $this->query('SELECT h.*,u.first_name,u.last_name FROM overtime_settings_history h LEFT JOIN system_users u ON u.user_id=h.actor_id WHERE h.business_location_id=? ORDER BY h.id DESC LIMIT 50', array($location))->result_array());
    }
}
