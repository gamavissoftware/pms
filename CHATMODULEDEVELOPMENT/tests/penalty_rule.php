<?php
/**
 * penalty_rule.php — is a DF flagged as a penalty DF by the right rule, in
 * one place?
 *
 * THE BUG CLASS THIS EXISTS TO CATCH
 * ----------------------------------
 * "Penalty DF" is not a column. A DF is marked from the DF release dashboard,
 * which posts to Task::save_penality_df(); that method zeroes `penalityamount`
 * across the DF's own `poreceived` rows and writes the figure onto one of
 * them. So the penalty on a DF is the SUM over its POs, and the DF is flagged
 * when that sum is NON-ZERO — not when it is positive.
 *
 * Both halves of that are quietly breakable:
 *
 *   - Drop the SUM (join poreceived flat) and a DF with several POs reports
 *     whichever row the database happened to hand back — usually 0, i.e. the
 *     flag silently disappears from a DF that carries a penalty.
 *   - Write `> 0` instead of non-zero and a correction keyed in as a negative
 *     drops off every screen instead of staying visible.
 *
 * Neither throws. Both produce a confident wrong answer, on a screen whose
 * whole job is to warn somebody.
 *
 * So this drives the REAL Chat_model::df_penalty_map() against a stubbed CI
 * (no database, no system/ directory) and asserts the rule, then checks
 * statically that nothing else in the model reads `penalityamount` behind its
 * back — the messenger, the DF card, message tags and the backfill screen all
 * have to agree with views/master/penalitydf.php about what a penalty is.
 *
 *   php CHATMODULEDEVELOPMENT/tests/penalty_rule.php
 *
 * Exit code 0 = passed, 1 = at least one failure.
 *
 * PHP 7.4 compatible.
 */

$root = dirname(dirname(__DIR__));

// ---------------------------------------------------------------------
// The minimum CodeIgniter Chat_model needs to be constructed.
//
// CI_Model is mirrored the way CI 3 actually writes it — a __get that defers
// to the super-object and NOTHING else. A lenient stand-in with real
// properties has hidden a genuine bug in this repo before.
// ---------------------------------------------------------------------
define('BASEPATH', 1);

class Pen_super { public $db; }
function get_instance() { return $GLOBALS['CI']; }
class CI_Model
{
    public function __construct() {}
    public function __get($key) { return get_instance()->$key; }
}

class Pen_result
{
    private $rows;
    public function __construct($rows) { $this->rows = $rows; }
    public function result() { return $this->rows; }
}

/**
 * Records the query that was built and replays canned rows.
 *
 * It does NOT implement GROUP BY: the rows it is handed are what MySQL would
 * have returned already aggregated. What is asserted here is our handling of
 * that answer, plus the shape of the question — testing MySQL's SUM is not
 * this suite's job.
 */
class Pen_db
{
    public $rows = array();
    public $log  = array();
    private $q   = array();

    public function simple_query($sql) { return TRUE; }
    public function select($s, $escape = TRUE) { $this->q['select'] = $s; return $this; }
    public function from($t)              { $this->q['from'] = $t; return $this; }
    public function where_in($k, $v)      { $this->q['where_in'] = $v; $this->q['where_col'] = $k; return $this; }
    public function group_by($k)          { $this->q['group_by'] = $k; return $this; }
    public function get()
    {
        $this->log[] = $this->q;
        $this->q = array();
        return new Pen_result($this->rows);
    }
}

$GLOBALS['CI'] = new Pen_super();
$GLOBALS['CI']->db = new Pen_db();

require $root . '/application/models/Chat_model.php';
$model = new Chat_model();
$db    = $GLOBALS['CI']->db;

$fails = 0;
$ran   = 0;
function check($label, $ok, $detail = '')
{
    global $fails, $ran;
    $ran++;
    if ($ok) { printf("  ok    %s\n", $label); return; }
    $fails++;
    printf("  FAIL  %s%s\n", $label, $detail !== '' ? ' — ' . $detail : '');
}

// ---------------------------------------------------------------------
// 1. THE RULE
// ---------------------------------------------------------------------
print "the penalty rule:\n";

$db->rows = array(
    (object) array('df_id' => '11', 'penalty_amount' => '175000'),   // marked
    (object) array('df_id' => '12', 'penalty_amount' => '0'),        // not marked
    (object) array('df_id' => '13', 'penalty_amount' => '-5000'),    // a correction
);
$map = $model->df_penalty_map(array(11, 12, 13));

check('a DF with a penalty is flagged',        isset($map[11]) && abs($map[11] - 175000) < 0.001);
check('a DF with no penalty is NOT flagged',   !isset($map[12]),
      'penalityamount 0 must not raise the flag');
check('a negative correction stays flagged',   isset($map[13]) && abs($map[13] + 5000) < 0.001,
      'the rule is non-zero, not > 0 — see views/master/penalitydf.php');
check('keys are ids, values are amounts',      array_keys($map) === array(11, 13)
                                               && is_float($map[11]) && is_float($map[13]));

// ---------------------------------------------------------------------
// 2. THE QUESTION IT ASKS
// ---------------------------------------------------------------------
print "\nthe query:\n";

$q = end($db->log);
check('reads poreceived',                      isset($q['from']) && $q['from'] === 'poreceived');
check('SUMs penalityamount',                   isset($q['select'])
                                               && stripos($q['select'], 'SUM(') !== FALSE
                                               && stripos($q['select'], 'penalityamount') !== FALSE,
      'a flat read returns one PO of several, which is usually 0');
check('groups by df_id',                       isset($q['group_by']) && $q['group_by'] === 'df_id');

$before = count($db->log);
$empty  = $model->df_penalty_map(array());
check('no ids asks nothing at all',            $empty === array() && count($db->log) === $before,
      'someone with no DF groups must not pay for a query');

$db->rows = array();
$model->df_penalty_map(array(0, '7', 7, -3));
$asked = end($db->log);
check('ids are cleaned before they are bound', isset($asked['where_in'])
                                               && array_values($asked['where_in']) === array(7, -3),
      'ints, de-duplicated, no 0');

// ---------------------------------------------------------------------
// 3. ONE DEFINITION
// ---------------------------------------------------------------------
print "\none definition:\n";

// Comments are stripped first: this counts CODE that reads the column, and
// the method's own docblock explains the rule at length.
$src  = file_get_contents($root . '/application/models/Chat_model.php');
$code = '';
foreach (token_get_all($src) as $t) {
    if (is_array($t) && ($t[0] === T_COMMENT || $t[0] === T_DOC_COMMENT)) continue;
    $code .= is_array($t) ? $t[1] : $t;
}
$hits = substr_count($code, 'penalityamount');
check('only df_penalty_map() reads the column', $hits === 1,
      sprintf('found %d in code — the messenger, the DF card, message tags and the'
            . ' backfill list must all ask through df_penalty_map()', $hits));

// The figure never leaves the model. Chat FLAGS a penalty DF; what the
// penalty comes to belongs to views/master/penalitydf.php. So the amount is
// read inside df_penalty_map(), compared against zero, and dropped — no
// screen, payload or currency formatter in the module carries money, and
// there is therefore nothing here that can drift out of step with the report.
print "\nno figures, only the flag:\n";

$chunks = preg_split('/\n\t(?:public|private|protected) function /', $code);
$rest   = '';
foreach ($chunks as $chunk) {
    if (strpos($chunk, 'df_penalty_map(') === 0) continue;   // where the sum is read
    $rest .= $chunk;
}
check('no amount is stamped onto a payload', strpos($rest, 'penalty_amount') === FALSE,
      'Chat_model hands out `penalty` as a flag; a figure here would reach a screen');

$views = array('index.php', 'df_groups.php', '_dock.php', '_navwidget.php');
foreach ($views as $v) {
    $src_v = file_get_contents($root . '/application/views/chatmodule/' . $v);
    $money = (strpos($src_v, 'penalty_amount') !== FALSE)
          || (strpos($src_v, 'penalty_text') !== FALSE)
          || (strpos($src_v, '_inr(') !== FALSE);
    check(sprintf('%s renders no penalty figure', $v), !$money);
}

printf("\n%s  penalty_rule — %d checks, %d failed\n",
    $fails ? 'FAIL' : 'PASS', $ran, $fails);

exit($fails ? 1 : 0);
