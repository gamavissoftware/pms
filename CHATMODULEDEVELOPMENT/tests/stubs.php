<?php
/**
 * stubs.php — the minimum CodeIgniter needed to exercise the chat helper.
 *
 * Standalone: no system/ directory, no database, no config bootstrap. The
 * repo cannot run CI on a developer machine (see the note in ROLLOUT.md), so
 * the tests load the REAL application/helpers/chat_access_helper.php and give
 * it a fake $CI whose db returns rows this file was told to return.
 *
 * The point is to test OUR logic — the capability rules and the fact that the
 * permission tables are keyed by user id — not MySQL's.
 *
 * PHP 7.4 compatible.
 */

/**
 * A query builder that records what was asked for and replays a canned answer.
 *
 * It deliberately captures `where` values: the whole reason these tests exist
 * is to prove chat_granted_submodules() looks its grants up by USER id, and
 * that can only be checked by inspecting the WHERE that was built.
 */
class Chat_stub_db
{
    /** @var array table => list of row arrays */
    public $data = array();

    /** @var array tables that "exist"; NULL = all of them */
    public $tables = NULL;

    /** @var array every query built, for assertions */
    public $log = array();

    private $q = array();

    public function __construct($data = array(), $tables = NULL)
    {
        $this->data   = $data;
        $this->tables = $tables;
        $this->reset();
    }

    private function reset()
    {
        $this->q = array('table' => '', 'where' => array(), 'select' => '',
                         'join' => array(), 'like' => array());
    }

    public function table_exists($t)
    {
        return $this->tables === NULL ? TRUE : in_array($t, $this->tables, TRUE);
    }

    public function select($s, $escape = TRUE) { $this->q['select'] = $s; return $this; }

    public function from($t)
    {
        // "submodule s" -> "submodule"
        $parts = preg_split('/\s+/', trim($t));
        $this->q['table'] = $parts[0];
        return $this;
    }

    public function join($t, $cond, $type = '') { $this->q['join'][] = array($t, $cond); return $this; }

    public function where($k, $v = NULL)
    {
        $this->q['where'][$k] = $v;
        return $this;
    }

    public function where_in($k, $v) { $this->q['where'][$k] = $v; return $this; }
    public function order_by($a, $b = '') { return $this; }
    public function limit($n) { return $this; }
    public function group_start() { return $this; }
    public function group_end() { return $this; }
    /**
     * Recorded and actually APPLIED, not ignored.
     *
     * chat_marketing_department_ids() finds its departments with
     * ->like('department', 'MARKETING'). A stub that treated that as a no-op
     * would return every department, the marketing gate would open for
     * everybody, and the test would pass while proving nothing.
     */
    public function like($col, $val) { $this->q['like'][$col] = $val; return $this; }

    /** OR-likes are only used for search paths these tests do not exercise. */
    public function or_like($col, $val) { return $this; }

    public function get($t = NULL)
    {
        if ($t !== NULL) $this->from($t);
        $q = $this->q;
        $this->log[] = $q;
        $this->reset();

        // chat_granted_submodules() asks ONE joined question:
        //   submodule s LEFT JOIN module_capablity c
        //     ON c.submoduleid = s.id AND c.role_id = N AND c.moduleid = M
        // Its answer decides every capability, so the stub answers it for
        // real rather than ignoring the join and returning bare submodule
        // rows — which would make a denied capability look granted.
        if ($q['table'] === 'submodule' && !empty($q['join'])) {
            return new Chat_stub_result($this->grant_rows($q));
        }

        return new Chat_stub_result($this->rows_for($q));
    }

    /** Resolve the submodule/module_capablity LEFT JOIN. */
    private function grant_rows($q)
    {
        $cond = $q['join'][0][1];
        preg_match('/c\.role_id\s*=\s*(\d+)/', $cond, $mr);
        preg_match('/c\.moduleid\s*=\s*(\d+)/', $cond, $mm);
        $role_id   = isset($mr[1]) ? (int) $mr[1] : 0;
        $module_id = isset($mm[1]) ? (int) $mm[1] : 0;

        $subs  = isset($this->data['submodule']) ? $this->data['submodule'] : array();
        $caps  = isset($this->data['module_capablity']) ? $this->data['module_capablity'] : array();
        $out   = array();

        foreach ($subs as $s) {
            foreach ($q['where'] as $k => $v) {
                if (is_array($v)) continue;
                $col = strpos($k, '.') !== FALSE ? substr($k, strpos($k, '.') + 1) : $k;
                if (!array_key_exists($col, $s) || (string) $s[$col] !== (string) $v) continue 2;
            }
            $access = NULL;
            foreach ($caps as $c) {
                if ((int) $c['submoduleid'] === (int) $s['id']
                    && (int) $c['role_id'] === $role_id
                    && (int) $c['moduleid'] === $module_id) {
                    $access = $c['submodule_access'];
                    break;
                }
            }
            $out[] = (object) array('submodule' => $s['submodule'], 'submodule_access' => $access);
        }
        return $out;
    }

    /**
     * Rows for one built query: every row of the table whose columns match
     * each scalar WHERE. Good enough for the lookups the helper performs, and
     * anything more would be reimplementing MySQL.
     */
    private function rows_for($q)
    {
        $rows = isset($this->data[$q['table']]) ? $this->data[$q['table']] : array();
        $out  = array();
        foreach ($rows as $r) {
            $ok = TRUE;
            foreach ($q['where'] as $k => $v) {
                if (is_array($v)) continue;
                $col = trim(preg_replace('/\s*(>|<|>=|<=|!=)\s*$/', '', $k));
                if (strpos($col, '.') !== FALSE) $col = substr($col, strpos($col, '.') + 1);
                if (!array_key_exists($col, $r) || (string) $r[$col] !== (string) $v) { $ok = FALSE; break; }
            }
            if ($ok) {
                foreach ($q['like'] as $k => $v) {
                    $col = strpos($k, '.') !== FALSE ? substr($k, strpos($k, '.') + 1) : $k;
                    if (!array_key_exists($col, $r) || stripos((string) $r[$col], (string) $v) === FALSE) {
                        $ok = FALSE; break;
                    }
                }
            }
            if ($ok) $out[] = (object) $r;
        }
        return $out;
    }
}

class Chat_stub_result
{
    private $rows;
    public function __construct($rows) { $this->rows = $rows; }
    public function result() { return $this->rows; }
    public function row() { return count($this->rows) ? $this->rows[0] : NULL; }
    public function num_rows() { return count($this->rows); }
}

class Chat_stub_session
{
    private $data = array();
    public function __construct($logged_in = NULL)
    {
        if ($logged_in !== NULL) $this->data['logged_in'] = $logged_in;
    }
    public function userdata($k) { return isset($this->data[$k]) ? $this->data[$k] : FALSE; }
    public function set_userdata($k, $v) { $this->data[$k] = $v; }
    public function set_flashdata($k, $v) { }
    public function flashdata($k) { return NULL; }
}

class Chat_stub_ci
{
    public $db;
    public $session;
    public function __construct($db, $session)
    {
        $this->db      = $db;
        $this->session = $session;
    }
}

/**
 * The helper caches identity, admin-ness and grants in `static` variables for
 * the life of a request. Tests run many "requests" in one process, so each
 * case must start clean — otherwise case 2 silently asserts case 1's answer.
 *
 * PHP has no way to reset a function-local static, so the helper is loaded
 * into a FRESH process per case by run_tests.php. This function exists to make
 * that requirement explicit and to fail loudly if it is ever ignored.
 */
function chat_test_require_fresh_process()
{
    static $loaded = FALSE;
    if ($loaded) {
        fwrite(STDERR, "FATAL: chat_access_helper was exercised twice in one process.\n" .
                       "Its static caches make the second result meaningless.\n");
        exit(2);
    }
    $loaded = TRUE;
}
