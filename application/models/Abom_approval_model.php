<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abom_approval_model
 *
 * The four-stage workflow and its guards.
 *
 *   draft -> submitted -> checked -> eng_approved -> approved
 *
 * Every guard in here is enforced SERVER-SIDE. The UI hides buttons it
 * should not offer, but that is presentation — this class is where a
 * transition is actually allowed or refused (spec 6.3).
 *
 * Every transition writes a row to abom_bom_approval. Nothing changes
 * status without leaving a trail.
 *
 * PHP 7.4 compatible.
 */
class Abom_approval_model extends CI_Model
{
    private $bom_table      = 'abom_bom';
    private $line_table     = 'abom_bom_line';
    private $approval_table = 'abom_bom_approval';
    private $revision_table = 'abom_bom_revision';

    /**
     * status => array(next_status, stage, permission, label)
     */
    private $transitions = array(
        'draft'        => array('submitted',    'prepare',      'save',         'Submit for checking'),
        'submitted'    => array('checked',      'check',        'check',        'Mark as checked'),
        'checked'      => array('eng_approved', 'eng_approve',  'eng_approve',  'Approve — Engineering'),
        'eng_approved' => array('approved',     'proc_approve', 'proc_approve', 'Approve — Procurement'),
    );

    // -----------------------------------------------------------------
    // INSPECTION
    // -----------------------------------------------------------------

    /**
     * The workflow ladder, for the user guide.
     *
     * Derived from $transitions -- the same array this model enforces --
     * so the guide cannot describe a workflow the code does not run.
     *
     * @return array
     */
    public function stage_ladder()
    {
        $out = array();

        foreach ($this->transitions as $from => $t) {
            $out[] = array(
                'from'       => $from,
                'to'         => $t[0],
                'stage'      => $t[1],
                'permission' => $t[2],
                'label'      => $t[3],
            );
        }

        return $out;
    }

    /**
     * @param  string $status
     * @return array|null
     */
    public function next_transition($status)
    {
        if (!isset($this->transitions[$status])) {
            return null;
        }

        list($next, $stage, $permission, $label) = $this->transitions[$status];

        return array(
            'next'       => $next,
            'stage'      => $stage,
            'permission' => $permission,
            'label'      => $label,
        );
    }

    /**
     * Everything blocking the next transition, as operator-facing text.
     * Empty array means the BOM may advance.
     *
     * @param  object $bom
     * @param  int    $user_id
     * @return array
     */
    public function blockers($bom, $user_id = 0)
    {
        $out    = array();
        $status = (string) $bom->status;

        if ($status === 'approved') {
            $out[] = 'This BOM is fully approved. Editing an approved BOM is not allowed — '
                   . 'create a revision instead.';
            return $out;
        }

        if ($status === 'superseded') {
            $out[] = 'This BOM has been superseded by a later revision.';
            return $out;
        }

        if (!isset($this->transitions[$status])) {
            $out[] = 'There is no next stage from status "' . $status . '".';
            return $out;
        }

        // Draft -> Submitted carries the data-quality gates.
        if ($status === 'draft') {
            $unconfirmed = $this->count_unconfirmed_manual((int) $bom->id);
            if ($unconfirmed > 0) {
                $out[] = $unconfirmed . ' MANUAL quantity line'
                       . ($unconfirmed === 1 ? '' : 's')
                       . ' still need confirming or an override reason.';
            }

            $unacked = $this->count_unacknowledged_conflicts((int) $bom->id);
            if ($unacked > 0) {
                $out[] = $unacked . ' ERP conflict line'
                       . ($unacked === 1 ? '' : 's')
                       . ' must be acknowledged with a comment before submission.';
            }
        }

        // Checking must be done by someone other than the preparer.
        if ($status === 'submitted'
            && !empty($bom->prepared_by)
            && (int) $bom->prepared_by === (int) $user_id) {
            $out[] = 'A BOM must be checked by someone other than the person who prepared it.';
        }

        foreach ($this->separation_blockers($bom, $user_id) as $blocker) {
            $out[] = $blocker;
        }

        return $out;
    }

    /**
     * SEPARATION OF DUTY.
     *
     * Decided against the recorded history, not against grants. A grant
     * says what a person may do in general; it cannot say "not on this
     * document", and any grant scheme collapses the moment an
     * administrator gives one person everything.
     *
     *   1. No user may perform two CONSECUTIVE forward transitions on the
     *      same BOM.
     *   2. The user who created the BOM may not perform the final
     *      approval.
     *
     * Only FORWARD transitions count towards rule 1 — a reject or a
     * reopen is a return, not a certification, so it neither confers nor
     * consumes a turn.
     *
     * Governed by $config['abom_require_distinct_approvers'] (default
     * TRUE). With it FALSE the single-user path is restored.
     *
     * @param  object $bom
     * @param  int    $user_id
     * @return array  operator-facing messages; empty means allowed
     */
    public function separation_blockers($bom, $user_id)
    {
        $out = array();

        // Load the section if a caller reached this model without the
        // controller having done so. Fail CLOSED if the value is absent
        // or unreadable: a separation rule that switches itself off
        // because it could not read its own config is worse than none.
        $required = $this->config->item('abom_require_distinct_approvers', 'abom');

        if ($required === null) {
            $this->config->load('abom', true, true);
            $required = $this->config->item('abom_require_distinct_approvers', 'abom');
        }

        if ($required === null) {
            $required = true;              // fail closed
        }
        if (!$required) {
            return $out;
        }

        $user_id = (int) $user_id;
        if ($user_id <= 0) {
            $out[] = 'Your user could not be identified, so this transition cannot be recorded.';
            return $out;
        }

        $t = $this->next_transition($bom->status);
        if (!$t) {
            return $out;
        }

        // Rule 1 — not two forward transitions in a row.
        $last = $this->last_forward_actor((int) $bom->id);

        if ($last !== null && (int) $last['user_id'] === $user_id) {
            $out[] = sprintf(
                '%s by you already; another user must perform the next stage.',
                $this->action_past_tense($last['stage'])
            );
        }

        // Rule 2 — the creator may not give final approval.
        if ($t['next'] === 'approved') {
            $creator = $this->creator_id($bom);

            if ($creator > 0 && $creator === $user_id) {
                $out[] = 'You created this BOM, so you cannot give it the final '
                       . 'procurement approval. Another user must.';
            }
        }

        return $out;
    }

    /**
     * The user behind the most recent FORWARD transition on this BOM.
     *
     * Read from abom_bom_approval, which records every transition with
     * its actor — not from the abom_bom.*_by columns, which record only
     * the latest occupant of each named slot and would miss a repeat.
     *
     * @param  int $bom_id
     * @return array|null  ['user_id'=>int, 'stage'=>string]
     */
    public function last_forward_actor($bom_id)
    {
        $row = $this->db->select('user_id, stage')
            ->from($this->approval_table)
            ->where('bom_id', (int) $bom_id)
            ->where_in('action', array('submit', 'approve'))
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if (!$row || $row->user_id === null) {
            return null;
        }

        return array('user_id' => (int) $row->user_id, 'stage' => (string) $row->stage);
    }

    /**
     * Who created this BOM.
     *
     * abom_bom.created_by is authoritative. abom_audit_log is the
     * fallback for a row created before that column was populated —
     * the create event is recorded there too.
     *
     * @param  object $bom
     * @return int  0 when unknown
     */
    public function creator_id($bom)
    {
        if (!empty($bom->created_by)) {
            return (int) $bom->created_by;
        }

        $row = $this->db->select('user_id')
            ->from('abom_audit_log')
            ->where('entity', 'abom_bom')
            ->where('entity_id', (int) $bom->id)
            ->where('action', 'create')
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get()
            ->row();

        return ($row && $row->user_id !== null) ? (int) $row->user_id : 0;
    }

    /**
     * @param  string $stage
     * @return string
     */
    private function action_past_tense($stage)
    {
        $map = array(
            'prepare'      => 'Submitted',
            'check'        => 'Checked',
            'eng_approve'  => 'Engineering-approved',
            'proc_approve' => 'Procurement-approved',
        );

        return isset($map[$stage]) ? $map[$stage] : 'Advanced';
    }

    /**
     * @param  int $bom_id
     * @return int
     */
    public function count_unconfirmed_manual($bom_id)
    {
        return (int) $this->db->from($this->line_table)
            ->where('bom_id', (int) $bom_id)
            ->where('formula_code', 'MANUAL')
            ->where('is_confirmed', 0)
            ->where('is_overridden', 0)
            ->count_all_results();
    }

    /**
     * @param  int $bom_id
     * @return int
     */
    public function count_unacknowledged_conflicts($bom_id)
    {
        return (int) $this->db->from($this->line_table)
            ->where('bom_id', (int) $bom_id)
            ->where('issue_severity', 'conflict')
            ->where('is_acknowledged', 0)
            ->count_all_results();
    }

    // -----------------------------------------------------------------
    // TRANSITIONS
    // -----------------------------------------------------------------

    /**
     * Advance one stage. Refuses if blockers() is non-empty.
     *
     * @param  object $bom
     * @param  int    $user_id
     * @param  string $user_name
     * @param  string $comment
     * @return array  ['ok'=>bool, 'errors'=>[], 'status'=>string]
     */
    public function advance($bom, $user_id, $user_name = '', $comment = '')
    {
        $blockers = $this->blockers($bom, $user_id);
        if (!empty($blockers)) {
            return array('ok' => false, 'errors' => $blockers, 'status' => $bom->status);
        }

        $t   = $this->next_transition($bom->status);
        $now = date('Y-m-d H:i:s');

        $data = array('status' => $t['next'], 'updated_at' => $now);

        switch ($t['next']) {
            case 'submitted':
                $data['prepared_by'] = $user_id;
                $data['prepared_at'] = $now;
                break;
            case 'checked':
                $data['checked_by'] = $user_id;
                $data['checked_at'] = $now;
                break;
            case 'eng_approved':
                $data['eng_approved_by'] = $user_id;
                $data['eng_approved_at'] = $now;
                break;
            case 'approved':
                $data['proc_approved_by'] = $user_id;
                $data['proc_approved_at'] = $now;
                break;
        }

        $this->db->trans_begin();

        $this->db->where('id', (int) $bom->id)->update($this->bom_table, $data);

        $this->log($bom->id, $t['stage'],
            $t['next'] === 'submitted' ? 'submit' : 'approve',
            $user_id, $user_name, $comment);

        // The terminal state gets an immutable snapshot — this is what
        // audit and quality will ask for.
        if ($t['next'] === 'approved') {
            $this->snapshot((int) $bom->id, $bom->revision, 'Procurement approval', $user_id);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('ok' => false, 'errors' => array('The transition could not be saved.'),
                         'status' => $bom->status);
        }

        $this->db->trans_commit();

        return array('ok' => true, 'errors' => array(), 'status' => $t['next']);
    }

    /**
     * Any stage may reject, which returns the BOM to draft. A comment is
     * mandatory — a rejection without a reason is not actionable.
     *
     * @return array
     */
    public function reject($bom, $user_id, $user_name = '', $comment = '')
    {
        $comment = trim((string) $comment);

        if ($comment === '') {
            return array('ok' => false, 'errors' => array('A rejection must carry a comment.'),
                         'status' => $bom->status);
        }

        if (in_array($bom->status, array('draft', 'approved', 'superseded'), true)) {
            return array('ok' => false,
                         'errors' => array('A BOM with status "' . $bom->status . '" cannot be rejected.'),
                         'status' => $bom->status);
        }

        $stage = $this->stage_for_status($bom->status);

        $this->db->trans_begin();
        $this->db->where('id', (int) $bom->id)->update($this->bom_table, array(
            'status'     => 'rejected',
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        $this->log($bom->id, $stage, 'reject', $user_id, $user_name, $comment);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('ok' => false, 'errors' => array('The rejection could not be saved.'),
                         'status' => $bom->status);
        }
        $this->db->trans_commit();

        return array('ok' => true, 'errors' => array(), 'status' => 'rejected');
    }

    /**
     * Reopen a rejected BOM for editing.
     *
     * @return array
     */
    public function reopen($bom, $user_id, $user_name = '', $comment = '')
    {
        if ($bom->status !== 'rejected') {
            return array('ok' => false,
                         'errors' => array('Only a rejected BOM can be reopened.'),
                         'status' => $bom->status);
        }

        $this->db->where('id', (int) $bom->id)->update($this->bom_table, array(
            'status'     => 'draft',
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        $this->log($bom->id, 'prepare', 'reopen', $user_id, $user_name, $comment);

        return array('ok' => true, 'errors' => array(), 'status' => 'draft');
    }

    /**
     * Editing an approved BOM is forbidden. This clones it to a new row
     * with the revision incremented, marks the original superseded, and
     * writes a snapshot of the original (spec 6.3).
     *
     * @return array ['ok'=>bool, 'errors'=>[], 'bom_id'=>int]
     */
    public function create_revision($bom, $user_id, $user_name = '', $note = '')
    {
        if ($bom->status !== 'approved') {
            return array('ok' => false,
                         'errors' => array('Only an approved BOM can be revised.'),
                         'bom_id' => 0);
        }

        $this->db->trans_begin();

        $this->snapshot((int) $bom->id, $bom->revision, 'Superseded by a new revision', $user_id);

        $header = (array) $bom;
        unset($header['id']);

        $header['revision']         = str_pad((string) ((int) $bom->revision + 1), 2, '0', STR_PAD_LEFT);
        $header['status']           = 'draft';
        $header['prepared_by']      = $user_id;
        $header['prepared_at']      = date('Y-m-d H:i:s');
        $header['checked_by']       = null;
        $header['checked_at']       = null;
        $header['eng_approved_by']  = null;
        $header['eng_approved_at']  = null;
        $header['proc_approved_by'] = null;
        $header['proc_approved_at'] = null;
        $header['created_by']       = $user_id;
        $header['created_at']       = date('Y-m-d H:i:s');
        $header['updated_at']       = date('Y-m-d H:i:s');
        $header['notes']            = $note;

        // family_code / family_name may be present from a joined read.
        unset($header['family_code'], $header['family_name']);

        $this->db->insert($this->bom_table, $header);
        $new_id = (int) $this->db->insert_id();

        if ($new_id > 0) {
            $lines = $this->db->from($this->line_table)->where('bom_id', (int) $bom->id)->get()->result_array();
            $rows  = array();
            foreach ($lines as $line) {
                unset($line['id']);
                $line['bom_id'] = $new_id;
                $rows[] = $line;
            }
            if (!empty($rows)) {
                $this->db->insert_batch($this->line_table, $rows);
            }

            $this->db->where('id', (int) $bom->id)->update($this->bom_table, array(
                'status'     => 'superseded',
                'updated_at' => date('Y-m-d H:i:s'),
            ));

            $this->log($new_id, 'prepare', 'reopen', $user_id, $user_name,
                'Revision ' . $header['revision'] . ' of ' . $bom->bom_no . '. ' . $note);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return array('ok' => false, 'errors' => array('The revision could not be created.'),
                         'bom_id' => 0);
        }
        $this->db->trans_commit();

        return array('ok' => true, 'errors' => array(), 'bom_id' => $new_id);
    }

    // -----------------------------------------------------------------
    // LINE ACKNOWLEDGEMENTS
    // -----------------------------------------------------------------

    /**
     * Confirms a MANUAL line's quantity, or acknowledges a conflict.
     *
     * @param  int    $bom_id
     * @param  int    $line_id
     * @param  string $what     'confirm' | 'acknowledge'
     * @param  string $comment
     * @param  int    $user_id
     * @return bool
     */
    public function acknowledge_line($bom_id, $line_id, $what, $comment, $user_id = 0)
    {
        $line = $this->db->from($this->line_table)
            ->where('id', (int) $line_id)->where('bom_id', (int) $bom_id)
            ->limit(1)->get()->row();

        if (!$line) {
            return false;
        }

        $comment = trim((string) $comment);

        if ($what === 'acknowledge') {
            if ($comment === '') {
                return false;      // a conflict acknowledgement needs a reason
            }
            $data = array('is_acknowledged' => 1, 'ack_comment' => $comment);
        } else {
            $data = array('is_confirmed' => 1);
            if ($comment !== '') {
                $data['ack_comment'] = $comment;
            }
        }

        $this->db->where('id', (int) $line_id)->update($this->line_table, $data);

        $this->load->model('Abom_model');
        $this->Abom_model->log_audit('abom_bom_line', (int) $line_id,
            $what === 'acknowledge' ? 'acknowledge_conflict' : 'confirm_manual',
            array('is_confirmed' => (int) $line->is_confirmed,
                  'is_acknowledged' => (int) $line->is_acknowledged),
            $data, $user_id);

        return true;
    }

    // -----------------------------------------------------------------
    // TRAIL
    // -----------------------------------------------------------------

    /**
     * @param  int $bom_id
     * @return array
     */
    public function trail($bom_id)
    {
        return $this->db->from($this->approval_table)
            ->where('bom_id', (int) $bom_id)
            ->order_by('id', 'ASC')
            ->get()
            ->result();
    }

    // -----------------------------------------------------------------

    private function stage_for_status($status)
    {
        $map = array(
            'draft'        => 'prepare',
            'submitted'    => 'check',
            'checked'      => 'eng_approve',
            'eng_approved' => 'proc_approve',
        );

        return isset($map[$status]) ? $map[$status] : 'prepare';
    }

    private function log($bom_id, $stage, $action, $user_id, $user_name, $comment)
    {
        $this->db->insert($this->approval_table, array(
            'bom_id'     => (int) $bom_id,
            'stage'      => $stage,
            'action'     => $action,
            'user_id'    => $user_id > 0 ? $user_id : null,
            'user_name'  => $user_name !== '' ? $user_name : null,
            'comment'    => trim((string) $comment) !== '' ? $comment : null,
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }

    private function snapshot($bom_id, $revision, $note, $user_id)
    {
        $header = $this->db->from($this->bom_table)->where('id', (int) $bom_id)->get()->row_array();
        $lines  = $this->db->from($this->line_table)->where('bom_id', (int) $bom_id)
            ->order_by('line_no', 'ASC')->get()->result_array();

        $this->db->insert($this->revision_table, array(
            'bom_id'      => (int) $bom_id,
            'revision'    => (string) $revision,
            'snapshot'    => json_encode(array('header' => $header, 'lines' => $lines)),
            'change_note' => $note,
            'created_by'  => $user_id > 0 ? $user_id : null,
            'created_at'  => date('Y-m-d H:i:s'),
        ));
    }
}
