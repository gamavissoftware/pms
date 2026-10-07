<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * APP LOG
 *
 * Who has the mobile app, what build they are on, and who is actually
 * opening it. Nothing in the PMS knew any of this: user_devices records a
 * push token and nothing else, and user_login_ip_tracking only ever sees
 * web logins, so a phone could be in daily use and leave no trace.
 *
 * The phone reports itself to ping() on every launch. Everything on the
 * dashboard is derived from that, so a person appears here only once they
 * are on a build new enough to report - people still on an older APK show
 * as "not reporting" rather than being silently dropped.
 *
 * Self-contained by design: its own controller, its own two tables, and it
 * touches nothing that already exists.
 */
class App_log extends CI_Controller
{
    /** Roles allowed to read the log: admin (12) and management (41). */
    private $viewer_roles = array(12, 41);

    /** One activity row per device per this many minutes, so a restart loop cannot flood the table. */
    private $activity_gap_minutes = 60;

    public function __construct()
    {
        parent::__construct();

        /**
         * ping() is called by the phone, which has no PMS session - so the
         * session gate has to be skipped for it, and only for it.
         */
        if ($this->router->fetch_method() === 'ping') {
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

        $role = (int) $this->db->select('user_role_id')
            ->from('system_users')
            ->where('user_id', (int) $user_id)
            ->get()->row()->user_role_id;

        if (!in_array($role, $this->viewer_roles, true)) {
            redirect(page_url . 'Dashboard', 'refresh');
        }
    }

    /* ==============================================================
     * THE PHONE SIDE
     * ============================================================== */

    /**
     * Called by the app on every launch.
     *
     * Keyed on device_id - a random id the app generates once and keeps in
     * SharedPreferences - so reinstalling counts as a new install and
     * merely reopening does not. Deliberately forgiving: a malformed body
     * gets a 200 and is ignored, because a crash here must never be able
     * to stop the app from starting.
     */
    public function ping()
    {
        header('Content-Type: application/json');

        $post = json_decode(file_get_contents("php://input"), true);

        $user_id   = isset($post['user_id']) ? (int) $post['user_id'] : 0;
        $device_id = isset($post['device_id']) ? substr(trim($post['device_id']), 0, 80) : '';

        if (!$user_id || $device_id === '') {
            echo json_encode(array("status" => false, "message" => "user_id or device_id missing"));
            return;
        }

        $version = isset($post['app_version'])  ? substr(trim($post['app_version']), 0, 30) : '';
        $build   = isset($post['build_number']) ? substr(trim($post['build_number']), 0, 20) : '';
        $os      = isset($post['os_version'])   ? substr(trim($post['os_version']), 0, 80) : '';
        $plat    = isset($post['platform'])     ? substr(trim($post['platform']), 0, 20) : 'android';

        $now = date('Y-m-d H:i:s');

        $existing = $this->db->select('id, open_count')
            ->from('app_device_log')
            ->where('device_id', $device_id)
            ->get()->row();

        if ($existing) {
            /**
             * user_id is overwritten rather than kept: a shared handset should
             * be attributed to whoever is logged in on it now.
             */
            $this->db->where('id', $existing->id)->update('app_device_log', array(
                'user_id'      => $user_id,
                'app_version'  => $version,
                'build_number' => $build,
                'os_version'   => $os,
                'platform'     => $plat,
                'last_seen'    => $now,
                'open_count'   => $existing->open_count + 1,
            ));
        } else {
            $this->db->insert('app_device_log', array(
                'user_id'      => $user_id,
                'device_id'    => $device_id,
                'platform'     => $plat,
                'app_version'  => $version,
                'build_number' => $build,
                'os_version'   => $os,
                'first_seen'   => $now,
                'last_seen'    => $now,
                'open_count'   => 1,
            ));
        }

        /** Throttled history, so the timeline stays readable and the table stays small. */
        $recent = $this->db->select('id')
            ->from('app_activity_log')
            ->where('device_id', $device_id)
            ->where('seen_at >', date('Y-m-d H:i:s', strtotime('-' . $this->activity_gap_minutes . ' minutes')))
            ->limit(1)
            ->get()->row();

        if (!$recent) {
            $this->db->insert('app_activity_log', array(
                'user_id'     => $user_id,
                'device_id'   => $device_id,
                'app_version' => $version,
                'seen_at'     => $now,
            ));
        }

        echo json_encode(array("status" => true, "message" => "logged"));
    }

    /* ==============================================================
     * THE WEB SIDE
     * ============================================================== */

    public function index()
    {
        $data = array();

        $data['latest'] = $this->latest_published_version();

        /* ---- headline counts ---- */
        $data['total_installs'] = (int) $this->db->count_all('app_device_log');

        $data['total_users'] = (int) $this->db->select('COUNT(DISTINCT user_id) AS c')
            ->from('app_device_log')->get()->row()->c;

        $data['active_today'] = $this->active_users_since(date('Y-m-d 00:00:00'));
        $data['active_week']  = $this->active_users_since(date('Y-m-d 00:00:00', strtotime('-6 days')));
        $data['active_month'] = $this->active_users_since(date('Y-m-d 00:00:00', strtotime('-29 days')));

        /* ---- android vs ios ---- */
        $data['platforms'] = $this->platform_summary();

        /* ---- which builds are in the field ---- */
        $data['versions'] = $this->db->query("
            SELECT
                CASE WHEN app_version = '' THEN 'unknown' ELSE app_version END AS app_version,
                build_number,
                COUNT(*) AS devices,
                COUNT(DISTINCT user_id) AS people,
                MAX(last_seen) AS newest
            FROM app_device_log
            GROUP BY app_version, build_number
            ORDER BY devices DESC, app_version DESC
        ")->result();

        /* ---- everyone who has ever opened it ---- */
        $data['people'] = $this->db->query("
            SELECT
                d.user_id,
                TRIM(CONCAT(u.first_name, ' ', u.last_name)) AS name,
                dept.department AS department,
                u.user_status,
                d.app_version,
                d.build_number,
                d.os_version,
                d.platform,
                d.device_id,
                d.first_seen,
                d.last_seen,
                d.open_count
            FROM app_device_log d
            LEFT JOIN system_users u ON u.user_id = d.user_id
            LEFT JOIN departments dept ON dept.department_id = u.department_id
            ORDER BY d.last_seen DESC
        ")->result();

        /**
         * Active staff with no install at all. The absence is the point: it
         * is the only way to see who the app has never reached.
         */
        $data['without_app'] = $this->db->query("
            SELECT
                u.user_id,
                TRIM(CONCAT(u.first_name, ' ', u.last_name)) AS name,
                dept.department AS department,
                CASE WHEN t.user_id IS NULL THEN 0 ELSE 1 END AS has_push_token
            FROM system_users u
            LEFT JOIN departments dept ON dept.department_id = u.department_id
            LEFT JOIN (SELECT DISTINCT user_id FROM user_devices) t ON t.user_id = u.user_id
            WHERE u.user_status = 1
            AND u.user_id NOT IN (SELECT user_id FROM app_device_log)
            ORDER BY has_push_token DESC, dept.department ASC, name ASC
        ")->result();

        /* ---- last 30 days of opens, for the recent-activity list ---- */
        $data['recent'] = $this->db->query("
            SELECT
                a.seen_at,
                a.app_version,
                TRIM(CONCAT(u.first_name, ' ', u.last_name)) AS name,
                dept.department AS department
            FROM app_activity_log a
            LEFT JOIN system_users u ON u.user_id = a.user_id
            LEFT JOIN departments dept ON dept.department_id = u.department_id
            WHERE a.seen_at >= ?
            ORDER BY a.seen_at DESC
            LIMIT 300
        ", array(date('Y-m-d 00:00:00', strtotime('-29 days'))))->result();

        /**
         * How far each audience actually reaches, shown on the send form so
         * nobody picks "everyone" without knowing what everyone means today.
         */
        $me = (int) $this->session->userdata['logged_in']['user_id'];

        $all_targets    = $this->broadcast_targets('all', $me, $data['latest']);
        $behind_targets = $this->broadcast_targets('behind', $me, $data['latest']);
        $my_targets     = $this->broadcast_targets('me', $me, $data['latest']);

        $data['reach'] = array(
            'me'     => array('devices' => count($my_targets),     'people' => $this->distinct_people($my_targets)),
            'all'    => array('devices' => count($all_targets),    'people' => $this->distinct_people($all_targets)),
            'behind' => array('devices' => count($behind_targets), 'people' => $this->distinct_people($behind_targets)),
        );

        $data['broadcasts'] = $this->db->select('*')
            ->from('app_broadcast_log')
            ->order_by('created_at', 'DESC')
            ->limit(20)
            ->get()->result();

        /* ---- what has been entered from the app (see entry_log_summary) ---- */
        $data['entries'] = $this->entry_log_summary();

        $this->load->view('app_log/app_log', $data);
    }

    /* ==============================================================
     * ANDROID vs iOS
     *
     * The phone reports `platform` on every ping, so the split is already
     * in app_device_log - it had simply never been read back. Bucketed with
     * a CASE rather than grouped on the raw column because the value is
     * whatever the handset sent: 'ios', 'android', and historically an
     * empty string from rows written before the app sent anything at all.
     *
     * Android and iOS are always both returned, even at zero. A missing iOS
     * row would read as "no data yet"; a zero says "nobody is on it", which
     * is the true and more useful answer while the iOS build is unreleased.
     * ============================================================== */

    /** Installs, people and weekly opens, split by platform. */
    private function platform_summary()
    {
        /* The same three rules the view's applog_platform_key() applies, so a
           row cannot land in one bucket on the chart and another in the list.
           Takes the column name because the second query joins two tables and
           an unqualified `platform` there is a trap waiting for the day
           app_activity_log grows one of its own. */
        $bucket = function ($col) {
            return "CASE
                WHEN LOWER($col) LIKE 'ios%' OR LOWER($col) IN ('iphone','ipad','darwin') THEN 'ios'
                WHEN LOWER($col) LIKE 'android%' THEN 'android'
                ELSE 'unknown'
            END";
        };

        $rows = $this->db->query("
            SELECT {$bucket('platform')} AS bucket,
                   COUNT(*) AS installs,
                   COUNT(DISTINCT user_id) AS people,
                   MAX(last_seen) AS newest
            FROM app_device_log
            GROUP BY bucket
        ")->result();

        /* opens are counted on the activity table, which carries no platform
           of its own - the device it came from is what knows */
        $active = $this->db->query("
            SELECT {$bucket('d.platform')} AS bucket,
                   COUNT(DISTINCT a.user_id) AS people
            FROM app_activity_log a
            INNER JOIN app_device_log d ON d.device_id = a.device_id
            WHERE a.seen_at >= ?
            GROUP BY bucket
        ", array(date('Y-m-d 00:00:00', strtotime('-6 days'))))->result();

        $seen_active = array();
        foreach ($active as $a) $seen_active[$a->bucket] = (int) $a->people;

        $found = array();
        foreach ($rows as $r) $found[$r->bucket] = $r;

        $labels = array('android' => 'Android', 'ios' => 'iOS', 'unknown' => 'Not reported');

        $out   = array();
        $total = 0;

        foreach ($labels as $key => $label) {
            $r = isset($found[$key]) ? $found[$key] : null;

            /* a zero Android or iOS row is meaningful; a zero "not reported"
               row is just clutter, so that one is dropped when empty */
            if (!$r && $key === 'unknown') continue;

            $installs = $r ? (int) $r->installs : 0;
            $total   += $installs;

            $out[] = array(
                'key'         => $key,
                'label'       => $label,
                'installs'    => $installs,
                'people'      => $r ? (int) $r->people : 0,
                'active_week' => isset($seen_active[$key]) ? $seen_active[$key] : 0,
                'newest'      => $r ? $r->newest : null,
                'share'       => 0.0,
            );
        }

        foreach ($out as $i => $row) {
            $out[$i]['share'] = $total > 0 ? round($row['installs'] * 100 / $total, 1) : 0.0;
        }

        return array('rows' => $out, 'total' => $total);
    }

    /* ==============================================================
     * ENTRIES MADE FROM THE APP
     *
     * Recorded by mobile/Api.php, which logs every write it performs into
     * app_entry_log. The web PMS never calls that controller, so a row in
     * there means the record was created or changed on a phone - that is
     * the whole basis of the numbers below.
     *
     * This page only reads that table; the recording is not done here and
     * must not be. The table is created by the first app write rather than
     * by a migration, so it can legitimately not exist yet - hence the
     * existence check instead of assuming it is there.
     * ============================================================== */

    /** Everything the page shows about app entries, in one read. */
    private function entry_log_summary()
    {
        $out = array(
            'ready'        => false,
            'today'        => 0,
            'week'         => 0,
            'month'        => 0,
            'total'        => 0,
            'people_today' => 0,
            'people_month' => 0,
            'chats'        => 0,
            'modules'      => array(),
            'people'       => array(),
            'recent'       => array(),
        );

        $exists = $this->db->query("SHOW TABLES LIKE 'app_entry_log'");
        if (!$exists OR $exists->num_rows() === 0) {
            return $out;
        }
        $out['ready'] = true;

        $today = date('Y-m-d');
        $week  = date('Y-m-d', strtotime('-6 days'));
        $month = date('Y-m-d', strtotime('-29 days'));

        /**
         * Chat messages are recorded too but counted apart: they would
         * dwarf everything else and "entries" is meant to mean records.
         */
        $row = $this->db->query("
            SELECT
                SUM(log_type = 'entry' AND log_date = ?) AS today,
                SUM(log_type = 'entry' AND log_date >= ?) AS week,
                SUM(log_type = 'entry' AND log_date >= ?) AS month,
                SUM(log_type = 'entry') AS total,
                SUM(log_type = 'chat') AS chats,
                COUNT(DISTINCT CASE WHEN log_type = 'entry' AND log_date = ? THEN user_id END) AS people_today,
                COUNT(DISTINCT CASE WHEN log_type = 'entry' AND log_date >= ? THEN user_id END) AS people_month
            FROM app_entry_log
        ", array($today, $week, $month, $today, $month))->row();

        if ($row) {
            $out['today']        = (int) $row->today;
            $out['week']         = (int) $row->week;
            $out['month']        = (int) $row->month;
            $out['total']        = (int) $row->total;
            $out['chats']        = (int) $row->chats;
            $out['people_today'] = (int) $row->people_today;
            $out['people_month'] = (int) $row->people_month;
        }

        $out['modules'] = $this->db->query("
            SELECT module, COUNT(*) AS entries, COUNT(DISTINCT user_id) AS people, MAX(created_at) AS last_at
            FROM app_entry_log
            WHERE log_type = 'entry' AND log_date >= ?
            GROUP BY module
            ORDER BY entries DESC
        ", array($month))->result();

        $out['people'] = $this->db->query("
            SELECT
                l.user_id,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS name,
                dept.department AS department,
                COUNT(*) AS entries,
                MAX(l.created_at) AS last_at
            FROM app_entry_log l
            LEFT JOIN system_users u ON u.user_id = l.user_id
            LEFT JOIN departments dept ON dept.department_id = u.department_id
            WHERE l.log_type = 'entry' AND l.log_date >= ?
            GROUP BY l.user_id, name, department
            ORDER BY entries DESC
        ", array($month))->result();

        $out['recent'] = $this->db->query("
            SELECT
                l.created_at, l.endpoint, l.module, l.action, l.primary_table,
                l.record_id, l.ref_key, l.ref_id, l.payload,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS name,
                dept.department AS department
            FROM app_entry_log l
            LEFT JOIN system_users u ON u.user_id = l.user_id
            LEFT JOIN departments dept ON dept.department_id = u.department_id
            WHERE l.log_type = 'entry'
            ORDER BY l.id DESC
            LIMIT 300
        ")->result();

        return $out;
    }

    private function distinct_people($targets)
    {
        $ids = array();
        foreach ($targets as $t) {
            $ids[(int) $t['user_id']] = true;
        }
        return count($ids);
    }

    /* ==============================================================
     * BROADCAST
     * ============================================================== */

    /**
     * Sends one push to a chosen audience.
     *
     * Uses the fcm helper (autoloaded), not another controller - the helper
     * is where the service-account auth already lives, and duplicating it
     * would mean two places to fix when the key rotates.
     *
     * Every send is written to app_broadcast_log first and updated after, so
     * a push that goes out cannot fail to leave a trace even if the request
     * dies half way through the token list.
     */
    public function broadcast()
    {
        $me      = (int) $this->session->userdata['logged_in']['user_id'];
        $type    = $this->input->post('push_type') === 'app_update' ? 'app_update' : 'custom';
        $audience = $this->input->post('audience');

        if (!in_array($audience, array('me', 'all', 'behind'), true)) {
            $audience = 'me';
        }

        $latest = $this->latest_published_version();

        if ($type === 'app_update') {
            /**
             * Wording is fixed for an update push. The version is read from
             * version.json rather than typed, so the message cannot advertise
             * a build the download page is not actually serving.
             */
            $title   = 'Update available';
            $message = $latest['version'] !== ''
                ? 'Version ' . $latest['version'] . ' is ready. Open the app to update.'
                : 'A new version is ready. Open the app to update.';
        } else {
            $title   = trim((string) $this->input->post('title'));
            $message = trim((string) $this->input->post('message'));
        }

        if ($title === '' || $message === '') {
            $this->session->set_flashdata('applog_msg', 'Nothing sent - a title and a message are both required.');
            $this->session->set_flashdata('applog_ok', 0);
            redirect(page_url . 'App_log', 'refresh');
            return;
        }

        $targets = $this->broadcast_targets($audience, $me, $latest);

        if (empty($targets)) {
            $this->session->set_flashdata('applog_msg', 'Nothing sent - nobody in that audience has a device registered.');
            $this->session->set_flashdata('applog_ok', 0);
            redirect(page_url . 'App_log', 'refresh');
            return;
        }

        $people = array();
        foreach ($targets as $t) {
            $people[(int) $t['user_id']] = true;
        }

        $sender = $this->db->select('first_name, last_name')
            ->from('system_users')->where('user_id', $me)->get()->row();

        $this->db->insert('app_broadcast_log', array(
            'sent_by'      => $me,
            'sent_by_name' => $sender ? trim($sender->first_name . ' ' . $sender->last_name) : '',
            'push_type'    => $type,
            'audience'     => $audience,
            'title'        => $title,
            'message'      => $message,
            'people'       => count($people),
            'devices'      => count($targets),
            'sent'         => 0,
            'failed'       => 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ));
        $log_id = $this->db->insert_id();

        $sent = 0;
        $failed = 0;

        foreach ($targets as $t) {
            try {
                sendFCM($t['fcm_token'], $title, $message, array(
                    'type' => $type === 'app_update' ? 'app_update' : 'app_broadcast',
                ));
                $sent++;
            } catch (Throwable $e) {
                $failed++;
            }
        }

        $this->db->where('id', $log_id)->update('app_broadcast_log', array(
            'sent'   => $sent,
            'failed' => $failed,
        ));

        $this->session->set_flashdata(
            'applog_msg',
            'Sent to ' . $sent . ' device' . ($sent === 1 ? '' : 's')
                . ' across ' . count($people) . ' ' . (count($people) === 1 ? 'person' : 'people')
                . ($failed ? ', ' . $failed . ' failed.' : '.')
        );
        $this->session->set_flashdata('applog_ok', $failed ? 0 : 1);

        redirect(page_url . 'App_log', 'refresh');
    }

    /**
     * The device tokens a broadcast should reach.
     *
     * "behind" deliberately includes people who have never reported at all:
     * silence means an old build, which is exactly who an update push is for.
     */
    private function broadcast_targets($audience, $me, $latest)
    {
        $this->db->select('user_id, fcm_token')
            ->from('user_devices')
            ->where('fcm_token !=', '');

        if ($audience === 'me') {
            $this->db->where('user_id', $me);
        }

        $rows = $this->db->get()->result_array();

        if ($audience !== 'behind') {
            return $this->dedupe_tokens($rows);
        }

        /* Users already on the published build - everyone else is behind. */
        $current = array();

        if ($latest['version'] !== '') {
            $q = $this->db->select('user_id')
                ->from('app_device_log')
                ->where('app_version', $latest['version'])
                ->where('build_number', (string) $latest['build'])
                ->get()->result_array();

            foreach ($q as $r) {
                $current[(int) $r['user_id']] = true;
            }
        }

        $out = array();
        foreach ($rows as $r) {
            if (!isset($current[(int) $r['user_id']])) {
                $out[] = $r;
            }
        }

        return $this->dedupe_tokens($out);
    }

    /** One push per token, however many rows point at it. */
    private function dedupe_tokens($rows)
    {
        $seen = array();
        $out  = array();

        foreach ($rows as $r) {
            $tok = trim((string) $r['fcm_token']);
            if ($tok === '' || isset($seen[$tok])) continue;
            $seen[$tok] = true;
            $out[] = array('user_id' => $r['user_id'], 'fcm_token' => $tok);
        }

        return $out;
    }

    /* ==============================================================
     * HELPERS
     * ============================================================== */

    private function active_users_since($since)
    {
        return (int) $this->db->select('COUNT(DISTINCT user_id) AS c')
            ->from('app_device_log')
            ->where('last_seen >=', $since)
            ->get()->row()->c;
    }

    /**
     * The build the update gate is currently advertising, so the dashboard
     * can mark who is behind without that number being typed in twice.
     */
    private function latest_published_version()
    {
        $out = array('version' => '', 'build' => '');

        $path = FCPATH . 'App/version.json';
        if (!is_readable($path)) {
            return $out;
        }

        $json = json_decode(file_get_contents($path), true);
        if (!is_array($json)) {
            return $out;
        }

        $out['version'] = isset($json['version']) ? $json['version'] : '';
        $out['build']   = isset($json['build']) ? $json['build'] : '';
        return $out;
    }
}
