<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Attendance report - the desktop half of the app's Attendance screen.
 *
 * Added 2026-09-30. Deliberately SELF-CONTAINED, like Exchange_rate: a new
 * controller and one new view, and no existing PMS file changed. It reads
 * the same two tables mobile/Api.php writes - app_attendance_day (the daily
 * Present / Absent / On Duty / Gate Pass mark) and app_attendance_log (the
 * IN punch a Present also writes) - and writes nothing.
 *
 * Who sees whom is repeated from mobile/Api.php rather than shared, because
 * those rules live in the mobile controller: _att_is_admin, _att_gate_exempt,
 * _att_team_member_ids and _att_scope. Change one, change both, or the web
 * sheet and the phone sheet will list different people.
 *
 *   admin (role 12, or 139/161/61)  -> everyone active, minus the exempt
 *   leads a team (prestogroup_teams) -> the team plus themselves
 *   anyone else                      -> themselves only
 *
 * The scope is derived from the session on every request. The department,
 * user and status filters only ever NARROW it; a user id typed into the URL
 * that is outside the scope is simply dropped.
 */
class Attendance_report extends CI_Controller
{
    /* A range longer than this is refused: it is people x days rows, and
       187 people over a quarter is a page nobody can read. */
    const MAX_DAYS = 62;

    /* Where a mark was made, by plant (asked 2026-10-01): a mark within
       PLANT_RADIUS_M of a plant's pin is tagged with that plant, anything
       further from both is "Outside". Taken from the mark's own GPS point
       (app_attendance_day.latitude/longitude), not from the user's profile,
       so the same person shows Sector 6 one day and Sector 59 the next.
       The two pins are ~4.3 km apart, so the circles never overlap.

       Both pins were given by the user from Google Maps on 2026-10-01:
       Sector 59 = Shubham Flexible Packaging Machines Pvt. Ltd. (Plot B-8A,
       10 m from the app geofence's office pin), Sector 6 = Shubham Flexible
       Packaging M/CS (P) Ltd. (Plot 61). */
    const PLANT_RADIUS_M = 1000;
    private static $PLANTS = array(
        'Sector 59' => array(28.31842220205592, 77.31269207540004),
        'Sector 6'  => array(28.35625357202389, 77.32118499748876),
    );

    /** [plant, metres to the nearest plant, nearest plant's name] for a GPS
        point. plant is 'Outside' when near neither, '' with no point. */
    private function _plant_at($lat, $lng)
    {
        $lat = (float) $lat;
        $lng = (float) $lng;
        if ($lat == 0.0 && $lng == 0.0) return array('', 0, '');

        $best = '';
        $bestD = PHP_INT_MAX;
        foreach (self::$PLANTS as $name => $p) {
            $dLat = deg2rad($p[0] - $lat);
            $dLon = deg2rad($p[1] - $lng);
            $a = sin($dLat / 2) * sin($dLat / 2)
               + cos(deg2rad($lat)) * cos(deg2rad($p[0])) * sin($dLon / 2) * sin($dLon / 2);
            $d = 6371000.0 * 2 * atan2(sqrt($a), sqrt(1 - $a));
            if ($d < $bestD) { $bestD = $d; $best = $name; }
        }
        return array($bestD <= self::PLANT_RADIUS_M ? $best : 'Outside', (int) round($bestD), $best);
    }

    private function _plant_row($d)
    {
        list($plant, $dist, $near) = $d ? $this->_plant_at($d['latitude'], $d['longitude']) : array('', 0, '');
        return array('plant' => $plant, 'plant_distance_m' => $dist, 'nearest_plant' => $near);
    }

    /** "1.2 km" / "350 m". */
    private function _dist($m)
    {
        return $m >= 1000 ? number_format($m / 1000, 1) . ' km' : $m . ' m';
    }

    /** "Sector 6", or "Outside - 2.3 km from Sector 59", or '' with no point. */
    private function _marked_from($r)
    {
        if ($r['plant'] === '') return '';
        if ($r['plant'] !== 'Outside') return $r['plant'];
        return 'Outside - ' . $this->_dist($r['plant_distance_m']) . ' from ' . $r['nearest_plant'];
    }

    /* The row on the userwise permission screen. Resolved by NAME, like
       User_management's own ensure_permission_submodule(), because ids
       differ between installations. */
    const PERM_NAME = 'ATTENDANCE REPORT';

    public function __construct()
    {
        parent::__construct();

        /* setup() is run once from the shell to register the permission
           row; it has no session and is unreachable from a browser. */
        if (is_cli() && $this->router->fetch_method() === 'setup') {
            return;
        }

        $session = $this->session->userdata('logged_in');

        if ($session == FALSE) {
            redirect(page_url);
        }

        $user_id = $this->session->userdata['logged_in']['user_id'];

        if (empty($user_id)) {
            redirect(site_url(), 'refresh');
        }
    }

    /* ------------------------------------------------------------------ */
    /* Rules repeated from mobile/Api.php                                  */
    /* ------------------------------------------------------------------ */

    private function _is_admin($user_id, $role_id)
    {
        return ((int) $role_id === 12 || in_array((int) $user_id, array(139, 161, 61), true));
    }

    /* Do not have to mark, so they are never on a sheet. Mirrors
       _att_gate_exempt() - 61, 62, 139, 167. */
    private function _exempt($user_id)
    {
        return in_array((int) $user_id, array(61, 62, 139, 167), true);
    }

    private function _team_member_ids($user_id)
    {
        if (!$this->db->table_exists('prestogroup_teams')
            || !$this->db->table_exists('presto_team_members')) {
            return array();
        }

        $rows = $this->db->select('m.employee_id')
                         ->from('prestogroup_teams t')
                         ->join('presto_team_members m', 'm.team_id = t.team_id', 'inner')
                         ->where('t.team_leader', (int) $user_id)
                         ->get()
                         ->result_array();

        $ids = array();
        foreach ($rows as $r) {
            $id = (int) $r['employee_id'];
            if ($id > 0 && $id !== (int) $user_id) $ids[$id] = $id;
        }

        return array_values($ids);
    }

    private function _strip_exempt($ids)
    {
        $out = array();
        foreach ($ids as $id) {
            if (!$this->_exempt((int) $id)) $out[] = (int) $id;
        }
        return $out;
    }

    private function _me()
    {
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];

        /* Role read from the table, not the session - same as Exchange_rate. */
        $u = $this->db->select('user_id, user_role_id')
                      ->from('system_users')
                      ->where('user_id', $user_id)
                      ->get()
                      ->row_array();

        return array('id' => $user_id, 'role' => $u ? (int) $u['user_role_id'] : 0);
    }

    private function _scope($me)
    {
        $user_id = (int) $me['id'];

        if ($this->_is_admin($user_id, $me['role'])) {
            $rows = $this->db->select('user_id')
                             ->from('system_users')
                             ->where('user_status', 1)
                             ->get()
                             ->result_array();

            $ids = array();
            foreach ($rows as $r) $ids[] = (int) $r['user_id'];

            return array('scope' => 'ALL', 'label' => 'Everyone',
                         'ids' => $this->_strip_exempt($ids));
        }

        $team = $this->_team_member_ids($user_id);

        if (!empty($team)) {
            $ids = $team;
            if (!in_array($user_id, $ids)) $ids[] = $user_id;

            return array('scope' => 'TEAM', 'label' => 'My team',
                         'ids' => $this->_strip_exempt($ids));
        }

        return array('scope' => 'SELF', 'label' => 'My attendance', 'ids' => array($user_id));
    }

    private function _statuses()
    {
        return array(
            'PRESENT'    => 'Present',
            'ABSENT'     => 'Absent',
            'ON_DUTY'    => 'On Duty',
            'GATE_PASS'  => 'Gate Pass',
            'NOT_MARKED' => 'Not marked',
        );
    }

    private function _worked($punches)
    {
        $total = 0;
        $open  = null;

        foreach ($punches as $p) {
            if (strtoupper($p['punch_type']) === 'IN') {
                $open = strtotime($p['punched_at']);
            } elseif ($open !== null) {
                $total += (strtotime($p['punched_at']) - $open);
                $open = null;
            }
        }

        return (int) round($total / 60);
    }

    /* ------------------------------------------------------------------ */
    /* The report                                                          */
    /* ------------------------------------------------------------------ */

    private function _date_or($v, $fallback)
    {
        $v = trim((string) $v);
        if ($v !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v)) return $v;
        return $fallback;
    }

    /**
     * Everything index() and export() share: the filters as read from the
     * query string, the people in scope, and one row per person per day.
     */
    private function _build()
    {
        date_default_timezone_set('Asia/Kolkata');

        $today = date('Y-m-d');
        $me    = $this->_me();
        $scope = $this->_scope($me);

        $from = $this->_date_or($this->input->get('from'), $today);
        $to   = $this->_date_or($this->input->get('to'), $from);

        if ($to < $from) { $t = $from; $from = $to; $to = $t; }

        /* No future days: a day that has not happened is not "not marked". */
        if ($to > $today)   $to = $today;
        if ($from > $today) $from = $today;

        $notice = '';
        $span = (int) round((strtotime($to) - strtotime($from)) / 86400) + 1;
        if ($span > self::MAX_DAYS) {
            $from   = date('Y-m-d', strtotime($to . ' -' . (self::MAX_DAYS - 1) . ' days'));
            $notice = 'The range was cut to the last ' . self::MAX_DAYS . ' days, ending ' . date('d M Y', strtotime($to)) . '.';
        }

        $dept   = (int) $this->input->get('department');
        $target = (int) $this->input->get('user');
        $status = strtoupper(trim((string) $this->input->get('status')));
        if (!isset($this->_statuses()[$status])) $status = '';

        /* Everyone in scope, with name + department + HR code. */
        $people = array();
        if (!empty($scope['ids'])) {
            $rows = $this->db->select("u.user_id, u.department_id, u.employee_code,
                                       TRIM(CONCAT(u.first_name,' ',COALESCE(u.last_name,''))) AS user_name,
                                       COALESCE(d.department,'') AS department", FALSE)
                             ->from('system_users u')
                             ->join('departments d', 'd.department_id = u.department_id', 'left')
                             ->where_in('u.user_id', $scope['ids'])
                             ->get()
                             ->result_array();

            foreach ($rows as $r) {
                $n = trim($r['user_name']);
                $people[(int) $r['user_id']] = array(
                    'user_id'       => (int) $r['user_id'],
                    'name'          => $n !== '' ? ucwords(strtolower($n)) : ('User ' . $r['user_id']),
                    'department_id' => (int) $r['department_id'],
                    'department'    => ucwords(strtolower(trim($r['department']))),
                    'emp_code'      => trim((string) $r['employee_code']),
                );
            }
        }

        uasort($people, function ($a, $b) { return strcasecmp($a['name'], $b['name']); });

        /* The filter drop-downs are built from the whole scope, before the
           filters narrow it, so a chosen department can be changed back. */
        $departments = array();
        foreach ($people as $p) {
            if ($p['department_id'] > 0) $departments[$p['department_id']] = $p['department'] !== '' ? $p['department'] : ('Department ' . $p['department_id']);
        }
        asort($departments);

        $ids = array();
        foreach ($people as $uid => $p) {
            if ($dept && $p['department_id'] !== $dept) continue;
            if ($target && $uid !== $target) continue;
            $ids[] = $uid;
        }

        $marks   = array();
        $punches = array();

        if (!empty($ids) && $this->db->table_exists('app_attendance_day')) {
            $rows = $this->db->where_in('user_id', $ids)
                             ->where('work_date >=', $from)
                             ->where('work_date <=', $to)
                             ->get('app_attendance_day')
                             ->result_array();
            foreach ($rows as $r) $marks[$r['work_date']][(int) $r['user_id']] = $r;
        }

        if (!empty($ids) && $this->db->table_exists('app_attendance_log')) {
            $rows = $this->db->where_in('user_id', $ids)
                             ->where('punch_date >=', $from)
                             ->where('punch_date <=', $to)
                             ->order_by('punched_at', 'ASC')
                             ->get('app_attendance_log')
                             ->result_array();
            foreach ($rows as $r) $punches[$r['punch_date']][(int) $r['user_id']][] = $r;
        }

        $labels  = $this->_statuses();
        $summary = array_fill_keys(array_keys($labels), 0);
        $list    = array();

        /* Newest day first, like the app's month view. */
        for ($day = $to; $day >= $from; $day = date('Y-m-d', strtotime($day . ' -1 day'))) {
            $day_rows = array();

            foreach ($ids as $uid) {
                $d  = isset($marks[$day][$uid]) ? $marks[$day][$uid] : null;
                $pl = isset($punches[$day][$uid]) ? $punches[$day][$uid] : array();

                $first_in = null;
                $last_out = null;
                foreach ($pl as $p) {
                    if (strtoupper($p['punch_type']) === 'IN' && $first_in === null) $first_in = $p['punched_at'];
                    if (strtoupper($p['punch_type']) === 'OUT') $last_out = $p['punched_at'];
                }

                $key = $d ? strtoupper($d['status']) : 'NOT_MARKED';
                if (!isset($summary[$key])) { $summary[$key] = 0; $labels[$key] = ucwords(strtolower(str_replace('_', ' ', $key))); }

                /* The chips count the whole filtered set; the status chip
                   then narrows the table, so the other counts stay visible. */
                $summary[$key]++;
                if ($status !== '' && $key !== $status) continue;

                $day_rows[] = $people[$uid] + array(
                    'date'          => $day,
                    'status'        => $key,
                    'status_label'  => $labels[$key],
                    'marked_at'     => $d ? $d['marked_at'] : null,
                    'remark'        => $d ? (string) $d['remark'] : '',
                    'site_name'     => $d ? (string) $d['site_name'] : '',
                ) + $this->_plant_row($d) + array(
                    'distance_m'    => $d ? (int) $d['distance_m'] : 0,
                    'in_premises'   => $d ? (int) $d['in_premises'] : 0,
                    'is_mocked'     => $d ? (int) $d['is_mocked'] : 0,
                    'changed_count' => $d ? (int) $d['changed_count'] : 0,
                    'first_in'      => $first_in,
                    'last_out'      => $last_out,
                    'worked'        => $this->_worked($pl),
                );
            }

            /* Marked first, the silent ones last - same order as the app. */
            usort($day_rows, function ($a, $b) {
                if ($a['status'] === 'NOT_MARKED' && $b['status'] !== 'NOT_MARKED') return 1;
                if ($b['status'] === 'NOT_MARKED' && $a['status'] !== 'NOT_MARKED') return -1;
                return strcasecmp($a['name'], $b['name']);
            });

            foreach ($day_rows as $r) $list[] = $r;
        }

        return array(
            'me'          => $me,
            'scope'       => $scope,
            'today'       => $today,
            'from'        => $from,
            'to'          => $to,
            'department'  => $dept,
            'user'        => $target,
            'status'      => $status,
            'notice'      => $notice,
            'labels'      => $labels,
            'summary'     => $summary,
            'rows'        => $list,
            'departments' => $departments,
            'people'      => $people,
        );
    }

    /* ------------------------------------------------------------------ */
    /* Permission                                                          */
    /* ------------------------------------------------------------------ */

    private function _perm_id()
    {
        $row = $this->db->select('id')
                        ->from('submodule')
                        ->where('submodule', self::PERM_NAME)
                        ->limit(1)
                        ->get()
                        ->row_array();

        return $row ? (int) $row['id'] : 0;
    }

    /**
     * Granted on the userwise permission screen (Master > User Management).
     * At launch only the admins hold it (2026-09-30, "admin only for now");
     * anyone given it later sees the rows their scope allows - so a granted
     * HOD still sees only their team.
     */
    private function _guard()
    {
        $id = $this->_perm_id();
        $user_id = (int) $this->session->userdata['logged_in']['user_id'];

        $ok = $id > 0 && $this->db->from('module_capablity')
                                  ->where('role_id', $user_id)
                                  ->where('submoduleid', $id)
                                  ->where('submodule_access', '1')
                                  ->count_all_results() > 0;

        if (!$ok) {
            show_error('You do not have permission to open the Attendance Report. Ask an administrator to allow it for you under User Management &gt; Permissions.', 403, 'Not allowed');
        }
    }

    /**
     * CLI only:  php index.php Attendance_report setup            (list modules)
     *            php index.php Attendance_report setup <module_id>
     *
     * Registers the permission row under that module and, only when it is
     * first created, grants it to the admins. Re-running never re-grants,
     * so a permission someone later removes stays removed.
     */
    public function setup($module_id = 0)
    {
        if (!is_cli()) show_404();

        $module_id = (int) $module_id;

        if (!$module_id) {
            foreach ($this->db->select('id, modulename, status')->order_by('id')->get('system_modules')->result_array() as $m) {
                echo $m['id'] . "\t" . $m['status'] . "\t" . $m['modulename'] . "\n";
            }
            echo 'existing ' . self::PERM_NAME . ' id: ' . $this->_perm_id() . "\n";
            return;
        }

        $id = $this->_perm_id();
        if ($id) { echo 'already registered as submodule ' . $id . "\n"; return; }

        $row = array('moduleid' => $module_id, 'submodule' => self::PERM_NAME,
                     'status' => 1, 'addedOn' => date('Y-m-d H:i:s'));
        if ($this->db->field_exists('dynachem', 'submodule'))    $row['dynachem'] = 0;
        if ($this->db->field_exists('shubhampack', 'submodule')) $row['shubhampack'] = 2;
        $this->db->insert('submodule', $row);
        $id = (int) $this->db->insert_id();
        echo 'registered submodule ' . $id . "\n";

        $admins = $this->db->select('user_id')
                           ->from('system_users')
                           ->where('user_status', 1)
                           ->group_start()
                               ->where('user_role_id', 12)
                               ->or_where_in('user_id', array(139, 161, 61))
                           ->group_end()
                           ->get()
                           ->result_array();

        foreach ($admins as $a) {
            $uid = (int) $a['user_id'];
            $acc = $this->db->select('id')->from('module_access')
                            ->where('role_id', $uid)->where('moduleid', $module_id)
                            ->limit(1)->get()->row_array();

            $this->db->insert('module_capablity', array(
                'acessid'          => $acc ? (int) $acc['id'] : 0,
                'role_id'          => $uid,
                'moduleid'         => $module_id,
                'submoduleid'      => $id,
                'submodule_access' => '1',
                'madd'             => '1',
                'medit'            => '0',
                'mremove'          => '0',
                'addedOn'          => date('Y-m-d H:i:s'),
            ));
            echo 'granted ' . $uid . "\n";
        }
    }

    public function index()
    {
        $this->_guard();
        $data = $this->_build();
        $this->load->view('attendance_report/index', $data);
    }

    /** The same rows the page shows, same filters, as .xlsx. */
    public function export()
    {
        $this->_guard();
        $data = $this->_build();

        $this->load->library('excel');

        $book  = new PHPExcel();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Attendance');

        $range = $data['from'] === $data['to']
               ? date('d M Y', strtotime($data['from']))
               : date('d M Y', strtotime($data['from'])) . ' to ' . date('d M Y', strtotime($data['to']));

        $filters = array();
        if ($data['department'] && isset($data['departments'][$data['department']])) $filters[] = 'Department: ' . $data['departments'][$data['department']];
        if ($data['user'] && isset($data['people'][$data['user']]))                   $filters[] = 'User: ' . $data['people'][$data['user']]['name'];
        if ($data['status'] !== '')                                                    $filters[] = 'Status: ' . $data['labels'][$data['status']];

        $sheet->setCellValue('A1', 'Attendance Report - ' . $range);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $chips = array();
        foreach ($data['summary'] as $k => $n) $chips[] = $data['labels'][$k] . ': ' . $n;
        $sheet->setCellValue('A2', implode('   |   ', $chips));
        $sheet->setCellValue('A3', ($filters ? implode('   |   ', $filters) . '   |   ' : '')
                                  . 'Scope: ' . $data['scope']['label']
                                  . '   |   Exported ' . date('d M Y, g:i a'));
        $sheet->getStyle('A3')->getFont()->setItalic(true)->getColor()->setRGB('777777');

        $head = array('Date', 'Emp Code', 'Name', 'Plant', 'Department', 'Status', 'Marked At',
                      'First In', 'Last Out', 'Worked (h:mm)', 'Marked From', 'Distance to Plant (m)',
                      'At a Plant', 'Mock GPS', 'Changed', 'Remark');
        $sheet->fromArray($head, null, 'A5');
        $last_col = PHPExcel_Cell::stringFromColumnIndex(count($head) - 1);
        $sheet->getStyle('A5:' . $last_col . '5')->applyFromArray(array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '3BAFDA')),
        ));

        $colour = array('PRESENT' => 'E6F6EF', 'ABSENT' => 'FDE8E8', 'ON_DUTY' => 'E8F0FD',
                        'GATE_PASS' => 'FDF1E3', 'NOT_MARKED' => 'F1F1F1');

        $t = function ($dt) { return $dt ? date('g:i a', strtotime($dt)) : ''; };

        $row = 6;
        foreach ($data['rows'] as $r) {
            $marked = $r['status'] !== 'NOT_MARKED';
            $sheet->fromArray(array(
                date('d-m-Y', strtotime($r['date'])),
                $r['emp_code'],
                $r['name'],
                $r['plant'],
                $r['department'],
                $r['status_label'],
                $t($r['marked_at']),
                $t($r['first_in']),
                $t($r['last_out']),
                $r['worked'] > 0 ? sprintf('%d:%02d', intdiv($r['worked'], 60), $r['worked'] % 60) : '',
                $this->_marked_from($r),
                $r['plant'] !== '' ? $r['plant_distance_m'] : '',
                $r['plant'] !== '' ? ($r['plant'] !== 'Outside' ? 'Yes' : 'No') : '',
                $marked ? ($r['is_mocked'] ? 'Yes' : 'No') : '',
                $r['changed_count'] > 0 ? $r['changed_count'] : '',
                $r['remark'],
            ), null, 'A' . $row);

            /* Emp code as text - "0757" must not lose its zero. */
            $sheet->setCellValueExplicit('B' . $row, $r['emp_code'], PHPExcel_Cell_DataType::TYPE_STRING);

            if (isset($colour[$r['status']])) {
                $sheet->getStyle('F' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                      ->getStartColor()->setRGB($colour[$r['status']]);
            }
            $row++;
        }

        if (empty($data['rows'])) $sheet->setCellValue('A6', 'No rows for these filters.');

        foreach (array('A' => 12, 'B' => 10, 'C' => 26, 'D' => 12, 'E' => 20, 'F' => 12, 'G' => 11, 'H' => 10,
                       'I' => 10, 'J' => 12, 'K' => 30, 'L' => 11, 'M' => 11, 'N' => 10, 'O' => 9, 'P' => 40) as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }
        $sheet->freezePane('A6');
        /* No setAutoFilter(): PHPExcel's writer does count($columns > 0), fatal on PHP 8. */

        $name = 'attendance_' . str_replace('-', '', $data['from'])
              . ($data['from'] !== $data['to'] ? '_' . str_replace('-', '', $data['to']) : '') . '.xlsx';

        $writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');

        if (ob_get_length()) ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
