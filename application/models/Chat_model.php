<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Chat_model
 * -----------------------------------------------------------------------------
 * Every read/write for the Chat module. Brand-new tables only (`chat_*`);
 * existing PMS tables (system_users, user_role, departments, leads,
 * df_release, poreceived, task_department_wise_scheduling, task_management,
 * prestogroup_teams) are READ but never written.
 *
 * Identity is always the pair (user_id, user_type) - see chat_access_helper,
 * because CRM users, engineers and vendors have colliding ids.
 */
class Chat_model extends CI_Model {

	/** conversations a user may see at once in the sidebar */
	const LIST_LIMIT = 200;
	/** messages per page in a conversation */
	const PAGE_SIZE  = 40;
	/** edit window in seconds - keep in step with Chat::EDIT_WINDOW */
	const EDIT_WINDOW = 900;

	public function __construct()
	{
		parent::__construct();

		// Emoji (reactions, message bodies) are 4-byte characters. The app-wide
		// connection charset is utf8 (3-byte) and changing it globally would
		// touch every other module, so we widen it for chat requests only.
		$this->db->simple_query('SET NAMES utf8mb4');
	}

	/* =========================================================================
	 *  IDENTITY / DIRECTORY
	 * =====================================================================*/

	/**
	 * People the current user can start a conversation with.
	 *
	 * PMS has one account table, so this is the staff directory and nothing
	 * else. `$include_external` is vestigial — it gated engineers and vendors
	 * in the CoreTech original and is kept only so the caller in Chat.php
	 * (which passes chat_can('tech_chat')) needs no edit.
	 *
	 * Users who have left (`user_status` = 0) and users deliberately kept out
	 * of pickers (`hide_profile` = 1) are excluded, matching how every other
	 * people-picker in PMS builds its list — see Master_model::__get_users().
	 */
	public function directory($me, $search = '', $include_external = TRUE)
	{
		$out = array();
		$search = trim($search);

		$this->db->select('user_id AS id, first_name, last_name, email, profile_image, user_role_id, department_id'
				. ($this->has_plant_unit() ? ', plant_unit' : ''))
			->from('system_users')
			->where('user_status', 1)
			->where('hide_profile', 0);
		if ($search !== '') {
			$this->db->group_start()
				->like('first_name', $search)
				->or_like('last_name', $search)
				->or_like('email', $search)
				->group_end();
		}
		$rows = $this->db->order_by('first_name', 'asc')->limit(300)->get()->result();

		$roles = $this->role_lookup();
		$depts = $this->dept_lookup();
		foreach ($rows as $r) {
			if ($me && $me['type'] === 'user' && (int) $r->id === (int) $me['id']) continue;
			$out[] = array(
				'id'     => (int) $r->id,
				'type'   => 'user',
				'name'   => chat_name_case($r->first_name . ' ' . $r->last_name),
				'email'  => $r->email,
				'role'   => chat_role_case(isset($roles[$r->user_role_id]) ? $roles[$r->user_role_id] : ''),
				'plant'  => $this->plant_of($r),
				'dept'   => isset($depts[$r->department_id]) ? $depts[$r->department_id] : '',
				'avatar' => chat_avatar_url($r->profile_image, 'user'),
			);
		}

		// The CoreTech original appended engineers (tech_information) and
		// vendors (vendor_registration) here. PMS has neither table and neither
		// kind of login, so the staff list above IS the whole directory.
		unset($include_external);

		return $out;
	}

	/**
	 * Is system_users.plant_unit there?
	 *
	 * Same guard as has_department_column() and the rest: naming a column that
	 * is not there turns every people query in the module into a 500, and the
	 * people queries are the module. Without it nobody shows a plant and
	 * everything else is untouched.
	 *
	 * Checked once per request — field_exists() is a metadata read, not free.
	 */
	private function has_plant_unit()
	{
		static $ok = NULL;
		if ($ok !== NULL) return $ok;
		return ($ok = $this->db->field_exists('plant_unit', 'system_users'));
	}

	/** Read plant_unit off a row that may or may not have been able to select it. */
	private function plant_of($row)
	{
		return isset($row->plant_unit) ? chat_plant_unit($row->plant_unit) : '';
	}

	private function role_lookup()
	{
		static $c = NULL;
		if ($c !== NULL) return $c;
		$c = array();
		foreach ($this->db->select('user_role_id, user_role')->from('user_role')->get()->result() as $r) {
			$c[$r->user_role_id] = $r->user_role;
		}
		return $c;
	}

	private function dept_lookup()
	{
		static $c = NULL;
		if ($c !== NULL) return $c;
		$c = array();
		foreach ($this->db->select('department_id, department')->from('departments')->get()->result() as $r) {
			$c[$r->department_id] = $r->department;
		}
		return $c;
	}

	/* =========================================================================
	 *  DEPARTMENT GROUPS
	 *
	 *  A group can be tagged with the department it belongs to
	 *  (chat_conversation.department_id — see chat_003_department_groups.sql).
	 *  That tag is what the sidebar sorts on: DF groups, department groups,
	 *  and everything else.
	 * =====================================================================*/

	/**
	 * Which section of the sidebar a conversation belongs in.
	 *
	 * Decided HERE rather than in the browser so the full page, the dock and
	 * anything added later cannot drift into disagreeing about what a group
	 * is. The JS only reads the answer.
	 *
	 *   'df'         bound to a DF — the rooms Task::dfrelease() creates
	 *   'department' tagged with a department
	 *   'other'      a plain group, and the resting state of everything that
	 *                existed before this feature
	 *
	 * DF WINS when a group is somehow both. A DF group's whole identity is the
	 * DF; a department on it is extra context, not a reclassification — and
	 * splitting the DF rooms across two sections by an attribute most of them
	 * do not carry would be worse than useless.
	 *
	 * @param object|array $row  a chat_conversation row, or any array carrying
	 *                           type / ref_type / ref_id / department_id
	 */
	public function group_kind($row)
	{
		$get = function ($k) use ($row) {
			if (is_array($row))  return isset($row[$k])  ? $row[$k]  : NULL;
			if (is_object($row)) return isset($row->$k) ? $row->$k : NULL;
			return NULL;
		};

		// Archived wins over everything. A dispatched DF group is no longer a
		// DF group you are working in — it is a closed room you occasionally
		// go back to — and leaving it under "DF Groups" would mean the heading
		// people scan every day slowly fills with rooms that are finished.
		$arch = $get('archived_at');
		if ($arch !== NULL && $arch !== '' && $arch !== '0000-00-00 00:00:00') return 'archived';

		if ($get('type') === 'direct') return '';

		// chat_clean_ref_type(), not a bare === 'df', so a row stored with
		// different casing is still recognised — same as my_conversations()
		if (chat_clean_ref_type($get('ref_type')) === 'df' && (int) $get('ref_id') > 0) {
			return 'df';
		}
		return ((int) $get('department_id') > 0) ? 'department' : 'other';
	}

	/**
	 * Has chat_003_department_groups.sql been run?
	 *
	 * PMS is deployed by uploading files, so the PHP for a feature can land
	 * before its migration does (and on this codebase it has). Without this
	 * check the department column would be written on every group creation
	 * and take ALL group creation down until somebody noticed the SQL was
	 * outstanding. Guarded, the worst case is that department groups simply
	 * do not work yet, which is the failure you can leave running.
	 *
	 * Asked once per request: field_exists() is a metadata read, not free.
	 */
	private function has_department_column()
	{
		static $ok = NULL;
		if ($ok !== NULL) return $ok;
		return ($ok = $this->db->field_exists('department_id', 'chat_conversation'));
	}

	/** Display name for one department id; '' when it is 0 or no longer there. */
	public function department_name($department_id)
	{
		$department_id = (int) $department_id;
		if ($department_id <= 0) return '';
		$depts = $this->dept_lookup();
		return isset($depts[$department_id]) ? $depts[$department_id] : '';
	}

	/**
	 * The departments offered when tagging a group.
	 *
	 * Scoped to the user's OWN business location, which is how every other
	 * department picker in PMS is built (Master_model::select_department()).
	 * That is not cosmetic here: production carries the same department name
	 * under two ids in two locations — MARKETING is 6 and 9, ACCOUNTS 3 and
	 * 20, DISPATCH 4 and 19 — so an unscoped list would offer several
	 * identical-looking options that tag groups into different buckets.
	 *
	 * Each entry also carries the group already tagged with that department,
	 * if there is one, so the picker can say so instead of letting a second
	 * "DESIGN" room be created beside the first.
	 *
	 * @return array of array(id, name, conv_id, conv_name)
	 */
	public function pickable_departments($user_id)
	{
		if (!$this->db->table_exists('departments')) return array();

		$loc = $this->user_business_location($user_id);

		$this->db->select('department_id, department')->from('departments')
			->where('status', 1);
		// A user with no location on their record would otherwise get an empty
		// picker and no way to tag anything; show them everything instead.
		if ($loc > 0) $this->db->where('business_loc_id', $loc);
		$rows = $this->db->order_by('department', 'asc')->get()->result();

		if (empty($rows)) return array();

		$ids = array();
		foreach ($rows as $r) $ids[] = (int) $r->department_id;
		$taken = $this->department_group_map($ids);

		$out = array();
		foreach ($rows as $r) {
			$id  = (int) $r->department_id;
			$has = isset($taken[$id]) ? $taken[$id] : NULL;
			$out[] = array(
				'id'        => $id,
				'name'      => (string) $r->department,
				'conv_id'   => $has ? (int) $has['id'] : 0,
				'conv_name' => $has ? $has['name'] : '',
			);
		}
		return $out;
	}

	/**
	 * The live group for each of these departments, where one exists.
	 *
	 * One query for the whole picker rather than one per department — the
	 * same shape as df_penalty_map(). Where a department somehow has more than
	 * one group the oldest wins, so the answer is stable between calls.
	 *
	 * @return array department_id => array(id, name)
	 */
	private function department_group_map($dept_ids)
	{
		$out = array();
		$ids = array();
		foreach ((array) $dept_ids as $d) { $d = (int) $d; if ($d > 0) $ids[] = $d; }
		if (empty($ids) || !$this->has_department_column()) return $out;

		$rows = $this->db->select('id, name, department_id')->from('chat_conversation')
			->where_in('department_id', $ids)
			->where('is_archived', 0)
			->order_by('id', 'asc')
			->get()->result();

		foreach ($rows as $r) {
			$d = (int) $r->department_id;
			if (!isset($out[$d])) $out[$d] = array('id' => (int) $r->id, 'name' => $r->name);
		}
		return $out;
	}

	/** The business location on someone's staff record; 0 if they have none. */
	private function user_business_location($user_id)
	{
		static $cache = array();
		$user_id = (int) $user_id;
		if ($user_id <= 0) return 0;
		if (isset($cache[$user_id])) return $cache[$user_id];

		$row = $this->db->select('business_location')->from('system_users')
			->where('user_id', $user_id)->get()->row();
		return ($cache[$user_id] = $row ? (int) $row->business_location : 0);
	}

	/**
	 * Tag a group with a department, or untag it by passing 0.
	 *
	 * Recorded in the thread like a rename is: which department a room belongs
	 * to decides where everyone finds it in their sidebar, so it should not
	 * change silently under them.
	 */
	public function set_department($conv_id, $me, $department_id)
	{
		$conv_id       = (int) $conv_id;
		$department_id = (int) $department_id;
		if (!$this->has_department_column()) return FALSE;

		$this->db->where('id', $conv_id)
			->update('chat_conversation', array('department_id' => $department_id));

		$depts = $this->dept_lookup();
		$name  = isset($depts[$department_id]) ? $depts[$department_id] : '';

		$this->system_message($conv_id, $me, $department_id > 0
			? ($me['name'] . ' moved this group to the ' . $name . ' department')
			: ($me['name'] . ' removed this group from its department'));

		return TRUE;
	}

	/**
	 * Batch-resolve (id,type) pairs to display info. One query per type, never
	 * per row - message lists would otherwise hammer the DB.
	 *
	 * @param array $pairs  array of array('id'=>, 'type'=>)
	 * @return array        keyed "type:id" => array(name, role, avatar, initials)
	 */
	public function resolve_people($pairs)
	{
		// Only 'user' resolves in PMS. Any other type falls through to the
		// placeholder loop at the bottom, which is the correct outcome for a
		// row left behind by data from elsewhere.
		$want = array('user' => array());
		foreach ($pairs as $p) {
			$t = isset($want[$p['type']]) ? $p['type'] : 'user';
			$want[$t][(int) $p['id']] = TRUE;
		}

		$out = array();

		if (!empty($want['user'])) {
			$roles = $this->role_lookup();
			$cols  = 'user_id, first_name, last_name, profile_image, user_role_id, email'
			       . ($this->has_plant_unit() ? ', plant_unit' : '');
			$rows = $this->db->select($cols)
				->from('system_users')->where_in('user_id', array_keys($want['user']))->get()->result();
			foreach ($rows as $r) {
				// resolve_people() is the single hydration point for message
				// headers, member lists, DM titles, read receipts and the
				// mention picker, so casing the name here covers all of them.
				$nm = chat_name_case($r->first_name . ' ' . $r->last_name);
				$out['user:' . $r->user_id] = array(
					'id' => (int) $r->user_id, 'type' => 'user',
					'name' => $nm !== '' ? $nm : ('User #' . $r->user_id),
					// sentence case here rather than at every place a role is
					// drawn — this is the one hydration point, so doing it
					// once covers message headers, member lists, DM titles,
					// read receipts and the mention picker together
					'role' => chat_role_case(isset($roles[$r->user_role_id]) ? $roles[$r->user_role_id] : ''),
					'plant' => $this->plant_of($r),
					'email' => $r->email,
					'avatar' => chat_avatar_url($r->profile_image, 'user'),
					'initials' => chat_initials($nm),
				);
			}
		}

		// anything that could not be resolved still needs a usable placeholder
		foreach ($pairs as $p) {
			$k = $p['type'] . ':' . (int) $p['id'];
			if (!isset($out[$k])) {
				$out[$k] = array(
					'id' => (int) $p['id'], 'type' => $p['type'], 'name' => 'Unknown user',
					'role' => '', 'plant' => '', 'email' => '', 'avatar' => '', 'initials' => '?',
				);
			}
		}
		return $out;
	}

	/* =========================================================================
	 *  CONVERSATIONS
	 * =====================================================================*/

	/** Canonical key so a 1:1 pair can only ever have ONE conversation row. */
	public function dm_key($a_type, $a_id, $b_type, $b_id)
	{
		$a = $a_type . ':' . (int) $a_id;
		$b = $b_type . ':' . (int) $b_id;
		$pair = array($a, $b);
		sort($pair);
		return implode('|', $pair);
	}

	/**
	 * Add (or re-activate) a member without ever tripping the uq_conv_member
	 * unique key.
	 *
	 * Every participant write goes through here because duplicates are normal,
	 * not exceptional: a DF group seeds the same person from both the DF's
	 * author and their tasks on it, two people can click "open DM" at the same
	 * moment, and a removed member can be re-added.
	 * CI 3.1.4 does not disable mysqli exceptions, so on PHP 8 a duplicate
	 * INSERT would surface as a fatal - the upsert removes the possibility.
	 */
	private function upsert_participant($conv_id, $user_id, $user_type, $role, $added_by)
	{
		$now = date('Y-m-d H:i:s');
		$this->db->query(
			'INSERT INTO chat_participant
				(conversation_id, user_id, user_type, member_role, joined_at, added_by, is_active)
			 VALUES (?, ?, ?, ?, ?, ?, 1)
			 ON DUPLICATE KEY UPDATE is_active = 1, left_at = NULL',
			array((int) $conv_id, (int) $user_id, $user_type, $role, $now, (int) $added_by)
		);
	}

	/** Find-or-create the DM between the current user and one other person. */
	public function ensure_direct($me, $other_id, $other_type)
	{
		$key = $this->dm_key($me['type'], $me['id'], $other_type, $other_id);
		$now = date('Y-m-d H:i:s');

		// INSERT IGNORE then read back: if two requests race, one inserts and
		// both end up with the same conversation id.
		$this->db->query(
			'INSERT IGNORE INTO chat_conversation
				(type, name, dm_key, created_by, created_by_type, last_activity_at, created_at)
			 VALUES ("direct", "", ?, ?, ?, ?, ?)',
			array($key, (int) $me['id'], $me['type'], $now, $now)
		);

		$row = $this->db->select('id')->from('chat_conversation')
			->where('dm_key', $key)->get()->row();
		if (!$row) return 0;
		$conv_id = (int) $row->id;

		$this->upsert_participant($conv_id, $me['id'], $me['type'], 'member', $me['id']);
		$this->upsert_participant($conv_id, $other_id, $other_type, 'member', $me['id']);

		return $conv_id;
	}

	/**
	 * Create a named channel.
	 *
	 * @param array  $members  array of array('id'=>,'type'=>)  (creator added automatically)
	 * @param string $ref_type ''|df|task|lead - binds the group to a record
	 * @param int    $department_id  departments.department_id, or 0 for a
	 *                               group that belongs to no department
	 */
	public function create_channel($me, $name, $members, $description = '', $ref_type = '', $ref_id = 0, $department_id = 0)
	{
		$now  = date('Y-m-d H:i:s');
		$type = ($ref_type !== '' && $ref_id > 0) ? 'job' : 'group';

		// dm_key stays NULL for groups - see the note on the column. Anything
		// else collides on uq_dm_key the moment a second group is created.
		$row = array(
			'type'             => $type,
			'name'             => $name,
			'dm_key'           => NULL,
			'description'      => $description,
			'ref_type'         => $ref_type,
			'ref_id'           => (int) $ref_id,
			'avatar_color'     => $this->pick_color($name),
			'created_by'       => (int) $me['id'],
			'created_by_type'  => $me['type'],
			'last_activity_at' => $now,
			'created_at'       => $now,
		);
		// see has_department_column() — the column may not exist yet
		if ($this->has_department_column()) $row['department_id'] = (int) $department_id;

		$this->db->insert('chat_conversation', $row);
		$conv_id = (int) $this->db->insert_id();

		// creator is the owner
		$this->upsert_participant($conv_id, $me['id'], $me['type'], 'owner', $me['id']);

		// $members may legitimately repeat the same person (a DF group pulls
		// the same user from the DF's author and from each of their tasks)
		foreach ($members as $m) {
			if ((int) $m['id'] === (int) $me['id'] && $m['type'] === $me['type']) continue;
			$this->upsert_participant($conv_id, $m['id'],
				isset($m['type']) ? $m['type'] : 'user', 'member', $me['id']);
		}

		$label = $type === 'job' ? 'created this job group' : 'created the group';
		$this->system_message($conv_id, $me, $me['name'] . ' ' . $label . ' “' . $name . '”');
		$this->push_event($conv_id, 'channel', $me, 0);

		return $conv_id;
	}

	private function pick_color($seed)
	{
		$palette = array('#2563eb', '#0ea5e9', '#7c3aed', '#db2777', '#059669', '#d97706', '#dc2626', '#0891b2');
		return $palette[abs(crc32((string) $seed)) % count($palette)];
	}

	public function get_conversation($conv_id)
	{
		return $this->db->select('*')->from('chat_conversation')
			->where('id', (int) $conv_id)->get()->row();
	}

	/** Membership check - the gate in front of every conversation-scoped call. */
	public function participant_row($conv_id, $me)
	{
		return $this->db->select('*')->from('chat_participant')
			->where('conversation_id', (int) $conv_id)
			->where('user_id', (int) $me['id'])
			->where('user_type', $me['type'])
			->where('is_active', 1)
			->get()->row();
	}

	public function is_participant($conv_id, $me)
	{
		return $this->participant_row($conv_id, $me) !== NULL;
	}

	/** Active member ids for a conversation, as (id,type) pairs. */
	public function participant_pairs($conv_id, $only_active = TRUE)
	{
		$this->db->select('user_id, user_type, member_role, last_read_message_id, joined_at')
			->from('chat_participant')->where('conversation_id', (int) $conv_id);
		if ($only_active) $this->db->where('is_active', 1);
		// oldest first, so "hand ownership to the longest-standing member" in
		// leave_channel() is deterministic rather than whatever order MySQL felt like
		$rows = $this->db->order_by('joined_at', 'asc')->order_by('id', 'asc')->get()->result();

		$out = array();
		foreach ($rows as $r) {
			$out[] = array(
				'id' => (int) $r->user_id, 'type' => $r->user_type,
				'member_role' => $r->member_role,
				'last_read_message_id' => (int) $r->last_read_message_id,
				'joined_at' => $r->joined_at,
			);
		}
		return $out;
	}

	/** Members with names/avatars resolved - for the members panel. */
	public function members($conv_id)
	{
		$pairs = $this->participant_pairs($conv_id);
		if (empty($pairs)) return array();
		$people = $this->resolve_people($pairs);
		$online = $this->online_map();

		$out = array();
		foreach ($pairs as $p) {
			$k = $p['type'] . ':' . $p['id'];
			$info = $people[$k];
			$info['member_role'] = $p['member_role'];
			$info['is_online']   = isset($online[$k]);
			$out[] = $info;
		}
		usort($out, function ($a, $b) {
			$rank = array('owner' => 0, 'admin' => 1, 'member' => 2);
			$ra = isset($rank[$a['member_role']]) ? $rank[$a['member_role']] : 3;
			$rb = isset($rank[$b['member_role']]) ? $rank[$b['member_role']] : 3;
			if ($ra !== $rb) return $ra - $rb;
			return strcasecmp($a['name'], $b['name']);
		});
		return $out;
	}

	/**
	 * Sidebar list: every conversation the user belongs to, newest activity
	 * first, with unread count, last-message preview and (for DMs) the other
	 * person's name / avatar / role.
	 */
	public function my_conversations($me)
	{
		// c.* already carries the archive columns when chat_005 has been run,
		// and simply does not when it has not — which is exactly the degrade
		// we want, so nothing here needs a guard.
		$rows = $this->db->select('c.*, p.last_read_message_id, p.is_muted, p.member_role', FALSE)
			->from('chat_participant p')
			->join('chat_conversation c', 'c.id = p.conversation_id')
			->where('p.user_id', (int) $me['id'])
			->where('p.user_type', $me['type'])
			->where('p.is_active', 1)
			->where('c.is_archived', 0)
			->order_by('c.last_activity_at', 'desc')
			->limit(self::LIST_LIMIT)
			->get()->result();

		if (empty($rows)) return array();

		$conv_ids = array();
		foreach ($rows as $r) $conv_ids[] = (int) $r->id;

		$unread   = $this->unread_map($me, $conv_ids);
		$lasts    = $this->last_messages($conv_ids);
		$partners = $this->dm_partners($me, $rows);
		$online   = $this->online_map();
		$audience = $this->audience_map($conv_ids);

		// One query for every DF group in the list, not one per row — see
		// df_penalty_map(). Most people's list has a handful of DF groups in
		// it, and someone with none pays nothing.
		$df_ids = array();
		foreach ($rows as $r) {
			// through chat_clean_ref_type(), not a bare === 'df', so a row
			// stored with different casing is still recognised as a DF group
			if (chat_clean_ref_type($r->ref_type) === 'df' && (int) $r->ref_id > 0) {
				$df_ids[] = (int) $r->ref_id;
			}
		}
		$penalties = $this->df_penalty_map($df_ids);

		// Department names for the tagged groups in this list. dept_lookup()
		// is one query for the whole table and is cached for the request, so
		// the sidebar pays nothing per row.
		$depts = $this->dept_lookup();

		$out = array();
		foreach ($rows as $r) {
			$id   = (int) $r->id;
			$dept = isset($r->department_id) ? (int) $r->department_id : 0;
			$item = array(
				'id'          => $id,
				'type'        => $r->type,
				'ref_type'    => $r->ref_type,
				'ref_id'      => (int) $r->ref_id,
				// Which sidebar section this row belongs in — DF groups,
				// department groups or the rest. Settled on the server so the
				// full page and the dock cannot disagree; see group_kind().
				'group_kind'  => $this->group_kind($r),
				'department_id' => $dept,
				'department'    => isset($depts[$dept]) ? $depts[$dept] : '',
				'member_role' => $r->member_role,
				'is_muted'    => (int) $r->is_muted,
				'unread'      => isset($unread[$id]) ? (int) $unread[$id] : 0,
				'last_at'     => $r->last_activity_at,
				'last_text'   => isset($lasts[$id]) ? $lasts[$id]['preview'] : '',
				'last_sender' => isset($lasts[$id]) ? $lasts[$id]['sender'] : '',
				'color'       => $r->avatar_color !== '' ? $r->avatar_color : $this->pick_color($r->name . $id),
				'photo'       => isset($r->avatar) && $r->avatar !== '' ? chat_upload_url . $r->avatar : '',
				// who this conversation involves — always 'team' in PMS; see
				// audience_map()
				'audiences'   => isset($audience[$id]) ? $audience[$id] : array('team'),

				// Archived: closed, filed under its own heading, still
				// readable, one click from coming back. NOT deleted — that is
				// is_archived, which this query already filters out entirely.
				'archived'       => (isset($r->archived_at) && $r->archived_at) ? 1 : 0,
				'archived_at'    => isset($r->archived_at) ? (string) $r->archived_at : '',
				'archive_reason' => isset($r->archive_reason) ? $r->archive_reason : '',
				'archive_files'  => isset($r->archive_files) ? (int) $r->archive_files : 0,
				'archive_size'   => (isset($r->archive_bytes) && $r->archive_bytes)
										? $this->human_size((int) $r->archive_bytes) : '',
			);

			// A DF group whose DF is marked as a penalty DF flies a flag next
			// to its name in the list. Every other conversation simply is not
			// flagged, so the sidebar can test one key without caring what a
			// conversation is bound to.
			$df_ref = (chat_clean_ref_type($r->ref_type) === 'df') ? (int) $r->ref_id : 0;
			$item['penalty'] = ($df_ref > 0 && isset($penalties[$df_ref]));

			if ($r->type === 'direct') {
				$other = isset($partners[$id]) ? $partners[$id] : NULL;
				$item['name']      = $other ? $other['name'] : 'Direct message';
				$item['role']      = $other ? $other['role'] : '';
				$item['plant']     = ($other && isset($other['plant'])) ? $other['plant'] : '';
				$item['avatar']    = $other ? $other['avatar'] : '';
				$item['initials']  = $other ? $other['initials'] : '?';
				$item['peer_id']   = $other ? $other['id'] : 0;
				$item['peer_type'] = $other ? $other['type'] : '';
				$item['is_online'] = $other ? isset($online[$other['type'] . ':' . $other['id']]) : FALSE;
			} else {
				$item['name']      = $r->name;
				$item['role']      = $r->description;
				$item['avatar']    = $item['photo'];   // group photo, if one was set
				$item['initials']  = chat_initials($r->name);
				$item['is_online'] = FALSE;
			}
			$out[] = $item;
		}

		return $this->unread_first($out);
	}

	/**
	 * Anything unread goes to the top of the list, WhatsApp-style.
	 *
	 * The SQL orders by last_activity_at, which answers "what happened most
	 * recently" — but the question somebody opens chat with is "what is
	 * waiting for me", and a direct message from this morning should not sit
	 * below three groups that have merely been busy since.
	 *
	 * Sorted HERE, not in the browser, because my_conversations() is the one
	 * list every surface draws: the full page, the dock and the Flutter app's
	 * chat_conversations endpoint. Ordering has a single owner, so the app and
	 * the web cannot show the same account two different lists.
	 *
	 * MUTED ROOMS FLOAT TOO. Unread is unread: a muted group is still a group
	 * somebody is waiting on, and burying it under every read conversation is
	 * how it gets missed for a week. Muting keeps doing what it is actually
	 * for — no sound, no toast, no desktop notification, none of which this
	 * function touches — it simply stops deciding where the row sits.
	 *
	 * Recency still decides everything else: the sort is STABLE (the original
	 * position is the tiebreak, rather than relying on the engine — PMS runs
	 * on PHP 7, where usort is not), so both halves come out in exactly the
	 * last_activity_at order the query produced. The web sidebar's sections
	 * bucket this list without re-sorting it, so they are unaffected.
	 */
	private function unread_first($items)
	{
		$keyed = array();
		foreach ($items as $i => $item) {
			$hot = !empty($item['unread']) ? 0 : 1;
			$keyed[] = array($hot, $i, $item);
		}

		usort($keyed, function ($a, $b) {
			if ($a[0] !== $b[0]) return $a[0] - $b[0];   // unread before read
			return $a[1] - $b[1];                        // else keep server order
		});

		$out = array();
		foreach ($keyed as $k) $out[] = $k[2];
		return $out;
	}

	/**
	 * Which audiences each conversation involves.
	 *
	 * In the CoreTech CRM this grouped the participants by user_type so the
	 * sidebar could keep internal chats, technician chats and vendor chats
	 * apart. PMS has one kind of account, so EVERY conversation is 'team' and
	 * the query that worked it out would be a guaranteed-constant answer paid
	 * for on every sidebar load. It is therefore answered directly.
	 *
	 * Kept as a function, and still returning a list per conversation, because
	 * my_conversations() and the sidebar filter both consume that shape — if an
	 * outside-party portal is ever added this is the one place that changes.
	 *
	 * @return array conversation_id => array of audience keys
	 */
	private function audience_map($conv_ids)
	{
		$final = array();
		foreach ((array) $conv_ids as $id) $final[(int) $id] = array('team');
		return $final;
	}

	/** unread count per conversation for one user, in a single grouped query */
	private function unread_map($me, $conv_ids)
	{
		if (empty($conv_ids)) return array();

		$sql = 'SELECT m.conversation_id, COUNT(*) AS cnt
				FROM chat_message m
				JOIN chat_participant p
				  ON p.conversation_id = m.conversation_id
				 AND p.user_id = ? AND p.user_type = ? AND p.is_active = 1
				WHERE m.conversation_id IN (' . implode(',', array_map('intval', $conv_ids)) . ')
				  AND m.id > p.last_read_message_id
				  AND m.is_deleted = 0
				  AND m.message_type <> "system"
				  AND NOT (m.sender_id = ? AND m.sender_type = ?)
				GROUP BY m.conversation_id';

		$rows = $this->db->query($sql, array((int) $me['id'], $me['type'], (int) $me['id'], $me['type']))->result();
		$out = array();
		foreach ($rows as $r) $out[(int) $r->conversation_id] = (int) $r->cnt;
		return $out;
	}

	/** last message preview per conversation */
	private function last_messages($conv_ids)
	{
		if (empty($conv_ids)) return array();
		$ids = implode(',', array_map('intval', $conv_ids));

		$rows = $this->db->query(
			'SELECT m.* FROM chat_message m
			 JOIN (SELECT conversation_id, MAX(id) AS mx FROM chat_message
			       WHERE conversation_id IN (' . $ids . ') GROUP BY conversation_id) t
			   ON t.mx = m.id'
		)->result();

		$pairs = array();
		foreach ($rows as $r) $pairs[] = array('id' => (int) $r->sender_id, 'type' => $r->sender_type);
		$people = empty($pairs) ? array() : $this->resolve_people($pairs);

		$out = array();
		foreach ($rows as $r) {
			$k = $r->sender_type . ':' . $r->sender_id;
			if ($r->is_deleted) {
				$preview = 'This message was deleted';
			} elseif ($r->message_type === 'file') {
				$preview = '📎 ' . ($r->body !== '' ? $r->body : 'Attachment');
			} elseif ($r->message_type === 'system') {
				$preview = $r->body;
			} else {
				$preview = $r->body;
			}
			$out[(int) $r->conversation_id] = array(
				'preview' => $this->shorten($this->plain(strip_tags($preview)), 90),
				'sender'  => $r->message_type === 'system' ? '' : (isset($people[$k]) ? $people[$k]['name'] : ''),
			);
		}
		return $out;
	}

	/**
	 * Strip formatting markers for places that show plain text: sidebar
	 * previews, notification bodies, search snippets. Without this a bold
	 * message reads "**Please check**" in the preview.
	 */
	public function plain($s)
	{
		$s = (string) $s;
		$s = preg_replace('/^\s*(?:[-*]|\d+[.)])\s+/mu', '', $s);   // list markers
		$s = str_replace(array('**', '~~', '`'), '', $s);            // bold / strike / code
		$s = preg_replace('/(^|\s)[*_]([^*_\n]+)[*_](?=\s|$)/u', '$1$2', $s);  // italic
		return $s;
	}

	private function shorten($s, $n)
	{
		$s = trim(preg_replace('/\s+/u', ' ', $s));
		if (function_exists('mb_strlen') && mb_strlen($s, 'UTF-8') > $n) {
			return mb_substr($s, 0, $n, 'UTF-8') . '…';
		}
		return $s;
	}

	/** for each DM, who is the OTHER person */
	private function dm_partners($me, $conv_rows)
	{
		$dm_ids = array();
		foreach ($conv_rows as $r) if ($r->type === 'direct') $dm_ids[] = (int) $r->id;
		if (empty($dm_ids)) return array();

		$rows = $this->db->select('conversation_id, user_id, user_type')
			->from('chat_participant')
			->where_in('conversation_id', $dm_ids)
			->get()->result();

		$pairs = array(); $by_conv = array();
		foreach ($rows as $r) {
			if ((int) $r->user_id === (int) $me['id'] && $r->user_type === $me['type']) continue;
			$by_conv[(int) $r->conversation_id] = array('id' => (int) $r->user_id, 'type' => $r->user_type);
			$pairs[] = array('id' => (int) $r->user_id, 'type' => $r->user_type);
		}
		if (empty($pairs)) return array();

		$people = $this->resolve_people($pairs);
		$out = array();
		foreach ($by_conv as $cid => $p) $out[$cid] = $people[$p['type'] . ':' . $p['id']];
		return $out;
	}

	/* =========================================================================
	 *  MESSAGES
	 * =====================================================================*/

	/**
	 * A page of messages.
	 *  - $before_id > 0  -> older page (infinite scroll upwards)
	 *  - $after_id  > 0  -> only newer than X (real-time top-up)
	 */
	public function messages($conv_id, $before_id = 0, $after_id = 0, $limit = self::PAGE_SIZE)
	{
		$this->db->select('*')->from('chat_message')
			->where('conversation_id', (int) $conv_id);

		if ($before_id > 0) $this->db->where('id <', (int) $before_id);
		if ($after_id  > 0) $this->db->where('id >', (int) $after_id);

		$rows = $this->db->order_by('id', $after_id > 0 ? 'asc' : 'desc')
			->limit((int) $limit)->get()->result();

		if ($after_id === 0) $rows = array_reverse($rows);
		return $this->hydrate($rows);
	}

	public function message($id)
	{
		$row = $this->db->select('*')->from('chat_message')->where('id', (int) $id)->get()->row();
		if (!$row) return NULL;
		$h = $this->hydrate(array($row));
		return isset($h[0]) ? $h[0] : NULL;
	}

	public function pinned_messages($conv_id)
	{
		$this->expire_pins($conv_id);

		$rows = $this->db->select('*')->from('chat_message')
			->where('conversation_id', (int) $conv_id)
			->where('is_pinned', 1)->where('is_deleted', 0)
			->order_by('pinned_at', 'desc')->limit(30)->get()->result();
		return $this->hydrate($rows);
	}

	/**
	 * Has Database/chat_004_pin_expiry.sql been run?
	 *
	 * Same guard, and the same reason, as has_department_column(): PMS is
	 * deployed by uploading files, so this PHP can land before its migration
	 * does. Without the check every pin would try to write a column that is
	 * not there and take pinning down completely. Guarded, the worst case is
	 * that pins simply never expire — which is exactly how pinning behaved
	 * before this feature, and a failure you can leave running.
	 */
	private function has_pin_expiry()
	{
		static $ok = NULL;
		if ($ok !== NULL) return $ok;
		return ($ok = $this->db->field_exists('pinned_until', 'chat_message'));
	}

	/**
	 * How long a pin can be made to last.
	 *
	 * The WhatsApp set, because it is the one people already know, plus
	 * "Always" for the genuinely permanent pin (a group's standing rules, a
	 * link everybody needs) that an expiry would be wrong for.
	 *
	 * Lives here rather than in the browser so the menu and the validation
	 * cannot disagree about what is on offer — pin() rejects anything that is
	 * not a key of this array and falls back to 0.
	 *
	 * @return array days => label
	 */
	public function pin_durations()
	{
		return array(
			1  => '24 hours',
			7  => '7 days',
			30 => '30 days',
			0  => 'Always',
		);
	}

	/**
	 * Take down the pins in one conversation whose time has run out.
	 *
	 * Called from pinned_messages(), which every path that displays the pin
	 * bar already goes through — opening a conversation, the stream's pin
	 * event, and the response to pinning something. So an expired pin is gone
	 * by the time anybody could have seen it, WITHOUT a scheduled job, which
	 * PMS has no runner for. The cost is one indexed UPDATE that matches
	 * nothing in the overwhelming majority of cases.
	 *
	 * is_pinned is cleared rather than the row being marked some third way, so
	 * everything that already asks "is this pinned" keeps working and an
	 * expired pin is indistinguishable from one somebody took down by hand.
	 * pinned_by / pinned_at are cleared for the same reason — to match exactly
	 * what toggle_pin() leaves behind when it unpins.
	 */
	private function expire_pins($conv_id)
	{
		if (!$this->has_pin_expiry()) return;

		$this->db->where('conversation_id', (int) $conv_id)
			->where('is_pinned', 1)
			// NULL means "no expiry", and NULL <= anything is never true, so
			// this one condition already leaves the permanent pins alone
			->where('pinned_until <=', date('Y-m-d H:i:s'))
			->update('chat_message', array(
				'is_pinned'    => 0,
				'pinned_by'    => 0,
				'pinned_at'    => NULL,
				'pinned_until' => NULL,
			));
	}

	/**
	 * Turn raw message rows into render-ready records: sender info, attachments,
	 * reactions, lead tags and the quoted parent of a reply - all batched.
	 */
	public function hydrate($rows)
	{
		if (empty($rows)) return array();

		$ids = array(); $pairs = array(); $reply_ids = array();
		foreach ($rows as $r) {
			$ids[] = (int) $r->id;
			$pairs[] = array('id' => (int) $r->sender_id, 'type' => $r->sender_type);
			if ($r->reply_to_id > 0) $reply_ids[] = (int) $r->reply_to_id;
		}

		// --- attachments -----------------------------------------------------
		$att = array();
		$arows = $this->db->select('*')->from('chat_attachment')->where_in('message_id', $ids)->get()->result();
		foreach ($arows as $a) {
			$att[(int) $a->message_id][] = array(
				'id'        => (int) $a->id,
				'name'      => $a->file_name,
				'ext'       => strtolower($a->file_ext),
				'size'      => (int) $a->file_size,
				'size_h'    => $this->human_size((int) $a->file_size),
				'is_image'  => (int) $a->is_image,
				'url'       => chat_upload_url . $a->rel_path . $a->stored_name,
				'download'  => site_url('Chat/download/' . (int) $a->id),
			);
		}

		// --- reactions -------------------------------------------------------
		$reac = array();
		$rrows = $this->db->select('*')->from('chat_reaction')->where_in('message_id', $ids)->get()->result();
		foreach ($rrows as $x) $pairs[] = array('id' => (int) $x->user_id, 'type' => $x->user_type);

		// --- record links (df | task | lead) ---------------------------------
		// Grouped by type so each type costs ONE query however many messages
		// on the page carry a link.
		$tags = array();
		$trows = $this->db->select('*')->from('chat_message_lead')->where_in('message_id', $ids)->get()->result();
		$by_type = array();
		foreach ($trows as $t) {
			$rt = chat_clean_ref_type($t->ref_type);
			if ($rt === '') continue;
			$by_type[$rt][] = (int) $t->ref_id;
		}
		$info_by_type = array();
		foreach ($by_type as $rt => $rids) {
			$info_by_type[$rt] = $this->record_info($rt, $rids);
		}
		foreach ($trows as $t) {
			$rt  = chat_clean_ref_type($t->ref_type);
			$key = (int) $t->ref_id;
			if ($rt === '' || !isset($info_by_type[$rt][$key])) continue;
			$tags[(int) $t->message_id][] = $info_by_type[$rt][$key];
		}

		// --- origin: forwarded from / privately replying to --------------------
		$origins = array();
		$origin_ids = array();
		foreach ($rows as $r) {
			if (isset($r->origin_message_id) && $r->origin_message_id > 0) {
				$origin_ids[] = (int) $r->origin_message_id;
			}
		}
		if (!empty($origin_ids)) {
			$orows = $this->db->select('m.id, m.sender_id, m.sender_type, m.body, m.message_type, m.is_deleted, c.type AS conv_type, c.name AS conv_name', FALSE)
				->from('chat_message m')
				->join('chat_conversation c', 'c.id = m.conversation_id')
				->where_in('m.id', array_unique($origin_ids))
				->get()->result();

			foreach ($orows as $om) $pairs[] = array('id' => (int) $om->sender_id, 'type' => $om->sender_type);
			$origins = $orows;   // resolved to names once $people exists, below
		}

		// --- read receipts ----------------------------------------------------
		// Everything needed is already in chat_participant.last_read_message_id:
		// a member has seen message N when their pointer has reached N. One
		// query per hydrate call covers every conversation in the batch, so
		// this costs nothing per message.
		$conv_ids = array();
		foreach ($rows as $r) $conv_ids[(int) $r->conversation_id] = TRUE;

		$readers = array();   // conversation_id => array(last_read_message_id, ...)
		if (!empty($conv_ids)) {
			$prows = $this->db->select('conversation_id, user_id, user_type, last_read_message_id')
				->from('chat_participant')
				->where_in('conversation_id', array_keys($conv_ids))
				->where('is_active', 1)
				->get()->result();
			foreach ($prows as $p) {
				$readers[(int) $p->conversation_id][] = array(
					'id'   => (int) $p->user_id,
					'type' => $p->user_type,
					'read' => (int) $p->last_read_message_id,
				);
			}
		}

		// --- calls (a join card rendered in place of the message body) -------
		$calls = array();
		if ($this->db->table_exists('chat_call')) {
			$crows = $this->db->select('*')->from('chat_call')->where_in('message_id', $ids)->get()->result();
			foreach ($crows as $c) {
				$calls[(int) $c->message_id] = array(
					'id'       => (int) $c->id,
					'provider' => $c->provider,
					'label'    => chat_provider_label($c->provider),
					'join_url' => $c->join_url,
					'topic'    => $c->topic,
					'status'   => $c->status,
					'started'  => date('h:i A', strtotime($c->started_at)),
					'mine'     => FALSE,   // filled in per-viewer below
					'starter'  => array('id' => (int) $c->started_by, 'type' => $c->started_by_type),
				);
			}
		}

		// --- @mentions (names, so the client can highlight them inline) ------
		$ment  = array();
		$mrows = $this->db->select('message_id, user_id, user_type')->from('chat_mention')
			->where_in('message_id', $ids)->get()->result();
		foreach ($mrows as $x) $pairs[] = array('id' => (int) $x->user_id, 'type' => $x->user_type);

		// --- quoted parents --------------------------------------------------
		$replies = array();
		if (!empty($reply_ids)) {
			$prows = $this->db->select('id, sender_id, sender_type, body, message_type, is_deleted')
				->from('chat_message')->where_in('id', array_unique($reply_ids))->get()->result();
			foreach ($prows as $p) $pairs[] = array('id' => (int) $p->sender_id, 'type' => $p->sender_type);
			$replies = $prows;
		}

		$people = $this->resolve_people($pairs);

		// reactions grouped by emoji, with who reacted
		foreach ($rrows as $x) {
			$mid = (int) $x->message_id;
			if (!isset($reac[$mid][$x->emoji])) {
				$reac[$mid][$x->emoji] = array('emoji' => $x->emoji, 'count' => 0, 'users' => array(), 'keys' => array());
			}
			$k = $x->user_type . ':' . $x->user_id;
			$reac[$mid][$x->emoji]['count']++;
			$reac[$mid][$x->emoji]['users'][] = isset($people[$k]) ? $people[$k]['name'] : 'Someone';
			$reac[$mid][$x->emoji]['keys'][]  = $k;
		}

		// mention names per message, plus a flag for "this one is aimed at me"
		foreach ($mrows as $x) {
			$k = $x->user_type . ':' . $x->user_id;
			$ment[(int) $x->message_id][] = array(
				'id'   => (int) $x->user_id,
				'type' => $x->user_type,
				'name' => isset($people[$k]) ? $people[$k]['name'] : '',
			);
		}

		// origin cards, now that names are resolved
		$origin_map = array();
		foreach ($origins as $om) {
			$k = $om->sender_type . ':' . $om->sender_id;
			$origin_map[(int) $om->id] = array(
				'id'        => (int) $om->id,
				'from'      => isset($people[$k]) ? $people[$k]['name'] : 'Unknown',
				'where'     => ($om->conv_type === 'direct') ? '' : $om->conv_name,
				'body'      => $om->is_deleted ? 'This message was deleted'
							   : $this->shorten($this->plain(strip_tags(
									$om->body !== '' ? $om->body : 'Attachment')), 160),
			);
		}

		$reply_map = array();
		foreach ($replies as $p) {
			$k = $p->sender_type . ':' . $p->sender_id;
			$reply_map[(int) $p->id] = array(
				'id'     => (int) $p->id,
				'sender' => isset($people[$k]) ? $people[$k]['name'] : 'Unknown',
				'body'   => $p->is_deleted ? 'This message was deleted'
							: $this->shorten(strip_tags($p->body !== '' ? $p->body : 'Attachment'), 120),
			);
		}

		$out = array();
		foreach ($rows as $r) {
			$k   = $r->sender_type . ':' . $r->sender_id;
			$who = isset($people[$k]) ? $people[$k] : array('name' => 'Unknown', 'role' => '', 'avatar' => '', 'initials' => '?');
			$mid = (int) $r->id;

			$out[] = array(
				'id'           => $mid,
				'conversation' => (int) $r->conversation_id,
				'sender_id'    => (int) $r->sender_id,
				'sender_type'  => $r->sender_type,
				'sender_name'  => $who['name'],
				'sender_role'  => $who['role'],
				'sender_plant' => isset($who['plant']) ? $who['plant'] : '',
				'avatar'       => $who['avatar'],
				'initials'     => $who['initials'],
				'type'         => $r->message_type,
				'body'         => $r->is_deleted ? '' : $r->body,
				'is_deleted'   => (int) $r->is_deleted,
				'is_edited'    => (int) $r->is_edited,
				'is_pinned'    => (int) $r->is_pinned,
				// when this pin takes itself down; '' = never. The pin bar
				// shows it so a pin says how long it is going to be there.
				'pinned_until' => (isset($r->pinned_until) && $r->pinned_until) ? $r->pinned_until : '',
				'reply_to'     => ($r->reply_to_id > 0 && isset($reply_map[(int) $r->reply_to_id]))
									? $reply_map[(int) $r->reply_to_id] : NULL,
				'attachments'  => isset($att[$mid])  ? $att[$mid] : array(),
				'reactions'    => isset($reac[$mid]) ? array_values($reac[$mid]) : array(),
				'tags'         => isset($tags[$mid]) ? $tags[$mid] : array(),
				'mentions'     => isset($ment[$mid]) ? $ment[$mid] : array(),
				// forwarded from / privately replying to a message elsewhere
				'origin_kind'  => isset($r->origin_kind) ? $r->origin_kind : '',
				'origin'       => (isset($r->origin_message_id) && isset($origin_map[(int) $r->origin_message_id]))
									? $origin_map[(int) $r->origin_message_id] : NULL,
				'call'         => isset($calls[$mid]) ? $calls[$mid] : NULL,
				// how many OTHER members have read this far (the sender is
				// excluded - you have obviously seen your own message)
				'seen_count'   => $this->seen_count($readers, (int) $r->conversation_id, $mid,
									(int) $r->sender_id, $r->sender_type),
				'seen_total'   => isset($readers[(int) $r->conversation_id])
									? max(0, count($readers[(int) $r->conversation_id]) - 1) : 0,
				'created_at'   => $r->created_at,
				'time'         => date('h:i A', strtotime($r->created_at)),
				'day'          => $this->day_label($r->created_at),
				// seconds of the edit window still left, computed server-side so
				// it cannot drift with the browser's clock or timezone
				'edit_left'    => max(0, self::EDIT_WINDOW - (time() - strtotime($r->created_at))),
			);
		}
		return $out;
	}

	/** how many members other than the sender have read up to this message */
	private function seen_count($readers, $conv_id, $msg_id, $sender_id, $sender_type)
	{
		if (empty($readers[$conv_id])) return 0;
		$n = 0;
		foreach ($readers[$conv_id] as $p) {
			if ($p['id'] === (int) $sender_id && $p['type'] === $sender_type) continue;
			if ($p['read'] >= $msg_id) $n++;
		}
		return $n;
	}

	/**
	 * Exactly who has and has not seen a given message.
	 * Used by the "Seen by" panel; the counts on each bubble come from hydrate.
	 */
	public function read_receipts($conv_id, $msg_id, $sender)
	{
		$rows = $this->db->select('user_id, user_type, last_read_message_id, last_read_at')
			->from('chat_participant')
			->where('conversation_id', (int) $conv_id)
			->where('is_active', 1)
			->get()->result();

		$pairs = array();
		foreach ($rows as $r) $pairs[] = array('id' => (int) $r->user_id, 'type' => $r->user_type);
		if (empty($pairs)) return array('seen' => array(), 'pending' => array());

		$people = $this->resolve_people($pairs);

		$seen = array(); $pending = array();
		foreach ($rows as $r) {
			// the sender is not waiting to read their own message
			if ((int) $r->user_id === (int) $sender['id'] && $r->user_type === $sender['type']) continue;

			$k = $r->user_type . ':' . $r->user_id;
			$who = $people[$k];
			if ((int) $r->last_read_message_id >= (int) $msg_id) {
				$who['at'] = $r->last_read_at ? date('d M, h:i A', strtotime($r->last_read_at)) : '';
				$seen[] = $who;
			} else {
				$pending[] = $who;
			}
		}
		return array('seen' => $seen, 'pending' => $pending);
	}

	private function day_label($ts)
	{
		$d = date('Y-m-d', strtotime($ts));
		if ($d === date('Y-m-d')) return 'Today';
		if ($d === date('Y-m-d', strtotime('-1 day'))) return 'Yesterday';
		return date('d M Y', strtotime($ts));
	}

	/** Public: the header and the archived rows both need to say how big a
	 *  bundle is before somebody commits to downloading it. */
	public function human_size($b)
	{
		if ($b >= 1048576) return round($b / 1048576, 1) . ' MB';
		if ($b >= 1024)    return round($b / 1024) . ' KB';
		return $b . ' B';
	}

	/* =========================================================================
	 *  CRM RECORD LOOKUPS  (read-only)
	 * =====================================================================*/

	/** Lead cards: id, company, status - as required by the lead-tagging spec. */
	public function lead_info($ids)
	{
		$ids = array_filter(array_unique(array_map('intval', (array) $ids)));
		if (empty($ids)) return array();

		$rows = $this->db->select('id, unique_id, company_name, customer_name, status, contact_person, added_by')
			->from('leads')->where_in('id', $ids)->get()->result();

		$stages  = $this->lead_stage_lookup();
		$current = $this->lead_current_stage($ids);

		$out = array();
		foreach ($rows as $r) {
			$id = (int) $r->id;
			// the live stage is the LATEST progress_remarks row, exactly as
			// Salescrm_model resolves it; leads.status is the fallback.
			$stage_id = isset($current[$id]) ? $current[$id] : (int) $r->status;

			$out[$id] = array(
				'ref_type' => 'lead',
				'ref_id'   => $id,
				'code'     => $r->unique_id !== '' ? $r->unique_id : ('LEAD-' . $id),
				'title'    => $r->company_name !== '' ? $r->company_name : $r->customer_name,
				'sub'      => $r->contact_person,
				'status'   => isset($stages[$stage_id]) ? $stages[$stage_id] : 'Open',
				// Leads::edit_leads() renders the per-lead page and reads the
				// id from URI segment 3. NOT update_lead_information — despite
				// its name that is the form's POST target, and a GET on it
				// would run the update path with an empty $_POST.
				'url'      => site_url('Leads/edit_leads/' . $id),
				'owner_id' => (int) $r->added_by,
			);
		}
		return $out;
	}

	/** lead_stage.lead_id => lead_stage.lead_name (the CRM's status master) */
	private function lead_stage_lookup()
	{
		static $c = NULL;
		if ($c !== NULL) return $c;
		$c = array();
		if ($this->db->table_exists('lead_stage')) {
			foreach ($this->db->select('lead_id, lead_name')->from('lead_stage')->get()->result() as $r) {
				$c[(int) $r->lead_id] = $r->lead_name;
			}
		}
		return $c;
	}

	/** latest progress_remarks.lead_status per lead */
	private function lead_current_stage($ids)
	{
		if (empty($ids) || !$this->db->table_exists('progress_remarks')) return array();
		$in = implode(',', array_map('intval', $ids));

		$rows = $this->db->query(
			'SELECT p.lead_id, p.lead_status FROM progress_remarks p
			 JOIN (SELECT lead_id, MAX(id) AS mx FROM progress_remarks
			       WHERE lead_id IN (' . $in . ') GROUP BY lead_id) t
			   ON t.mx = p.id'
		)->result();

		$out = array();
		foreach ($rows as $r) $out[(int) $r->lead_id] = (int) $r->lead_status;
		return $out;
	}

	/**
	 * Cards for a linked record, whatever kind it is.
	 *
	 * The CoreTech original had one function per kind (lead_info / ticket_info)
	 * and a two-way `if` at every call site. PMS links THREE kinds, so the
	 * dispatch lives here instead and callers just name the type — adding a
	 * fourth is one entry in chat_ref_types() plus one method here.
	 *
	 * @param string $ref_type  df | task | lead
	 * @return array            ref_id => card
	 */
	public function record_info($ref_type, $ids)
	{
		switch (chat_clean_ref_type($ref_type)) {
			case 'df':   return $this->df_info($ids);
			case 'task': return $this->task_info($ids);
			case 'lead': return $this->lead_info($ids);
		}
		return array();
	}

	/**
	 * Which DFs are marked as penalty DFs, and for how much.
	 *
	 * "Penalty DF" is not a column anywhere. A DF is marked from the DF
	 * release dashboard, which posts to Task::save_penality_df(); that method
	 * zeroes `penalityamount` across the DF's own `poreceived` rows and writes
	 * the figure onto one of them. So the penalty ON A DF is the SUM over its
	 * POs, and the DF is flagged when that sum is non-zero.
	 *
	 * Non-zero rather than positive is deliberate, and matches PMS's own
	 * penalty report (the derived table at the top of
	 * views/master/penalitydf.php, which this mirrors): a correction keyed in
	 * as a negative stays visible as a flagged DF instead of silently dropping
	 * off the screen.
	 *
	 * Only flagged DFs come back, so isset() is the whole test. Defined once
	 * here because four surfaces now ask this same question — the conversation
	 * list, the DF card in a group header, a DF tagged onto a message, and the
	 * backfill screen — and they must not be able to disagree.
	 *
	 * @param  array $df_ids  df_release ids
	 * @return array          df id => penalty amount, flagged DFs only
	 */
	public function df_penalty_map($df_ids)
	{
		$df_ids = array_filter(array_unique(array_map('intval', (array) $df_ids)));
		if (empty($df_ids)) return array();

		// Summed per DF in SQL, filtered in PHP: the row count here is bounded
		// by the caller (a screenful of conversations, or the 500-row backfill
		// list), and a plain GROUP BY reads the same in every MySQL mode.
		$rows = $this->db->select('df_id, SUM(IFNULL(penalityamount, 0)) AS penalty_amount', FALSE)
			->from('poreceived')
			->where_in('df_id', $df_ids)
			->group_by('df_id')
			->get()->result();

		$out = array();
		foreach ($rows as $r) {
			$amount = (float) $r->penalty_amount;
			// float compare, not != 0 — penalityamount is an int column today
			// but the sum arrives as a string and order_value alongside it is
			// decimal(19,2), so this stays correct if the column ever follows.
			if (abs($amount) > 0.0001) $out[(int) $r->df_id] = $amount;
		}
		return $out;
	}

	/**
	 * DF cards, for DF groups and DF links.
	 *
	 * A "DF" in PMS is a row of `df_release`, keyed by `df_release.id`. That is
	 * what `task_department_wise_scheduling.df_id` points at, what
	 * Task::dfrelease() creates, and what /gantt/<df_id> renders.
	 *
	 * NOT `df_design_form_table` — despite the name that is the design form,
	 * a different record with its own ids, and joining tasks to it lines DFs up
	 * against unrelated rows. Verified against Task::dfrelease() and
	 * Gantt_chart_model::get_header().
	 */
	public function df_info($ids)
	{
		$ids = array_filter(array_unique(array_map('intval', (array) $ids)));
		if (empty($ids)) return array();

		$rows = $this->db->select('d.id, d.df_no, d.df_description, d.added_on, d.added_by, d.df_status, d.on_hold, p.company_name', FALSE)
			->from('df_release d')
			->join('poreceived p', 'p.df_id = d.id', 'left')
			->where_in('d.id', $ids)
			->group_by('d.id')
			->get()->result();

		// Asked separately rather than summed in the join above: a DF can have
		// several POs, and summing penalityamount across a join that is already
		// collapsed by GROUP BY d.id would depend on which row MySQL happened
		// to keep. See df_penalty_map().
		$penalties = $this->df_penalty_map($ids);

		$out = array();
		foreach ($rows as $r) {
			$id   = (int) $r->id;
			$code = strtoupper(trim((string) $r->df_no));

			if ((int) $r->on_hold === 1)          $status = 'On hold';
			elseif ((int) $r->df_status === 1)    $status = 'Completed';
			else                                  $status = 'Running';

			$out[$id] = array(
				'ref_type' => 'df',
				'ref_id'   => $id,
				'code'     => $code !== '' ? $code : ('DF-' . $id),
				'title'    => $this->shorten(trim(strip_tags((string) $r->df_description)), 70),
				// the customer, which is how people actually recognise a DF
				'sub'      => (string) $r->company_name,
				'status'   => $status,
				// the rebuilt DF progress board — see routes.php /gantt/<df_id>
				'url'      => site_url('gantt/' . $id),
				'owner_id' => (int) $r->added_by,
				// a penalty is an exception, not a status, so it rides
				// ALONGSIDE `status` rather than replacing it — a DF can be
				// running and penalised at the same time.
				//
				// The FLAG, not the figure. What the penalty comes to is the
				// penalty report's business (views/master/penalitydf.php);
				// chat says only that the DF is marked, so no money crosses
				// into a chat payload at all.
				'penalty'        => isset($penalties[$id]),
			);
		}
		return $out;
	}

	/**
	 * Task cards.
	 *
	 * A task is an INSTANCE in task_department_wise_scheduling; its name lives
	 * on the task_management master it points at, so this joins the two. The
	 * department is carried too, because task names repeat across departments
	 * and are ambiguous on their own.
	 */
	public function task_info($ids)
	{
		$ids = array_filter(array_unique(array_map('intval', (array) $ids)));
		if (empty($ids)) return array();

		$rows = $this->db->select('s.id, s.df_id, s.department_id, s.task_status, s.assigned_user, s.end_date, m.task_name, d.df_no', FALSE)
			->from('task_department_wise_scheduling s')
			->join('task_management m', 'm.task_id = s.taskid', 'left')
			->join('df_release d', 'd.id = s.df_id', 'left')
			->where_in('s.id', $ids)->get()->result();

		$depts = $this->dept_lookup();

		$out = array();
		foreach ($rows as $r) {
			$id   = (int) $r->id;
			$name = trim(strip_tags((string) $r->task_name));
			$ref  = strtoupper(trim((string) $r->df_no));
			$out[$id] = array(
				'ref_type' => 'task',
				'ref_id'   => $id,
				'code'     => $ref !== '' ? $ref : ('TASK-' . $id),
				'title'    => $this->shorten($name !== '' ? $name : ('Task #' . $id), 70),
				'sub'      => isset($depts[$r->department_id]) ? $depts[$r->department_id] : '',
				'status'   => ((int) $r->task_status === 1) ? 'Completed' : 'Pending',
				// a task is only meaningful inside its DF's board
				'url'      => site_url('gantt/' . (int) $r->df_id),
				'owner_id' => (int) $r->assigned_user,
			);
		}
		return $out;
	}

	/**
	 * Full-text-ish search across the messages a user is allowed to see.
	 *
	 * Scoped by the participant join, so it can only ever return messages from
	 * conversations the searcher actually belongs to - there is no way to probe
	 * other people's chats through it.
	 *
	 * @param int $conv_id  limit to one conversation, or 0 for everything
	 */
	public function search_messages($me, $q, $conv_id = 0, $limit = 60)
	{
		$q = trim($q);
		if ($q === '') return array();

		$this->db->select('m.*, c.type AS conv_type, c.name AS conv_name', FALSE)
			->from('chat_message m')
			->join('chat_participant p',
				'p.conversation_id = m.conversation_id AND p.user_id = ' . (int) $me['id'] .
				' AND p.user_type = ' . $this->db->escape($me['type']) . ' AND p.is_active = 1')
			->join('chat_conversation c', 'c.id = m.conversation_id')
			->where('m.is_deleted', 0)
			->where('m.message_type <>', 'system')
			->like('m.body', $q);

		if ($conv_id > 0) $this->db->where('m.conversation_id', (int) $conv_id);

		$rows = $this->db->order_by('m.id', 'desc')->limit((int) $limit)->get()->result();
		if (empty($rows)) return array();

		// resolve senders, and give DMs a readable conversation name
		$pairs = array();
		foreach ($rows as $r) $pairs[] = array('id' => (int) $r->sender_id, 'type' => $r->sender_type);
		$people = $this->resolve_people($pairs);

		$dm_names = $this->dm_names_for($me, $rows);

		$out = array();
		foreach ($rows as $r) {
			$k    = $r->sender_type . ':' . $r->sender_id;
			$cid  = (int) $r->conversation_id;
			$name = ($r->conv_type === 'direct')
				? (isset($dm_names[$cid]) ? $dm_names[$cid] : 'Direct message')
				: $r->conv_name;

			$out[] = array(
				'id'           => (int) $r->id,
				'conversation' => $cid,
				'conv_name'    => $name,
				'conv_type'    => $r->conv_type,
				'sender_name'  => isset($people[$k]) ? $people[$k]['name'] : 'Unknown',
				'avatar'       => isset($people[$k]) ? $people[$k]['avatar'] : '',
				'initials'     => isset($people[$k]) ? $people[$k]['initials'] : '?',
				'body'         => $this->shorten($this->plain(strip_tags($r->body !== '' ? $r->body : 'Attachment')), 160),
				'created_at'   => $r->created_at,
				'when'         => date('d M Y, h:i A', strtotime($r->created_at)),
			);
		}
		return $out;
	}

	/** conversation_id => other person's name, for the DMs among these rows */
	private function dm_names_for($me, $rows)
	{
		$ids = array();
		foreach ($rows as $r) if ($r->conv_type === 'direct') $ids[] = (int) $r->conversation_id;
		if (empty($ids)) return array();

		$parts = $this->db->select('conversation_id, user_id, user_type')
			->from('chat_participant')->where_in('conversation_id', array_unique($ids))->get()->result();

		$pairs = array(); $by_conv = array();
		foreach ($parts as $p) {
			if ((int) $p->user_id === (int) $me['id'] && $p->user_type === $me['type']) continue;
			$by_conv[(int) $p->conversation_id] = array('id' => (int) $p->user_id, 'type' => $p->user_type);
			$pairs[] = array('id' => (int) $p->user_id, 'type' => $p->user_type);
		}
		if (empty($pairs)) return array();

		$people = $this->resolve_people($pairs);
		$out = array();
		foreach ($by_conv as $cid => $p) $out[$cid] = $people[$p['type'] . ':' . $p['id']]['name'];
		return $out;
	}

	/** Type-ahead for the lead/ticket tag picker. */
	/**
	 * The picker behind "link a record": find DFs, tasks or leads by text.
	 *
	 * Read-only and unscoped by design — linking a record to a conversation is
	 * gated by the 'lead_tag' capability, and that grant is what decides who
	 * may see this list at all (Chat::search_records checks it before calling).
	 *
	 * @param string $kind  df | task | lead
	 */
	public function search_records($q, $kind = 'lead', $limit = 15)
	{
		$q    = trim($q);
		$kind = chat_clean_ref_type($kind);
		if ($kind === '') $kind = 'lead';
		$out  = array();

		// --- DF ---------------------------------------------------------------
		if ($kind === 'df') {
			$this->db->select('d.id, d.df_no, d.df_description, p.company_name', FALSE)
				->from('df_release d')
				->join('poreceived p', 'p.df_id = d.id', 'left');
			if ($q !== '') {
				$this->db->group_start()
					->like('d.df_no', $q)
					->or_like('d.df_description', $q)
					->or_like('p.company_name', $q)
					->group_end();
			}
			$rows = $this->db->group_by('d.id')->order_by('d.id', 'desc')->limit($limit)->get()->result();
			foreach ($rows as $r) {
				$code = strtoupper(trim((string) $r->df_no));
				$out[] = array(
					'ref_type' => 'df', 'ref_id' => (int) $r->id,
					'code'     => $code !== '' ? $code : ('DF-' . (int) $r->id),
					'title'    => $this->shorten(trim(strip_tags((string) $r->df_description)), 60),
					'status'   => (string) $r->company_name,
				);
			}
			return $out;
		}

		// --- Task -------------------------------------------------------------
		if ($kind === 'task') {
			$this->db->select('s.id, s.task_status, s.department_id, m.task_name, d.df_no', FALSE)
				->from('task_department_wise_scheduling s')
				->join('task_management m', 'm.task_id = s.taskid', 'left')
				->join('df_release d', 'd.id = s.df_id', 'left');
			if ($q !== '') {
				$this->db->group_start()
					->like('m.task_name', $q)
					->or_like('d.df_no', $q)
					->group_end();
			}
			$rows  = $this->db->order_by('s.id', 'desc')->limit($limit)->get()->result();
			$depts = $this->dept_lookup();
			foreach ($rows as $r) {
				$name = trim(strip_tags((string) $r->task_name));
				$ref  = strtoupper(trim((string) $r->df_no));
				$out[] = array(
					'ref_type' => 'task', 'ref_id' => (int) $r->id,
					'code'     => $ref !== '' ? $ref : ('TASK-' . (int) $r->id),
					'title'    => $this->shorten($name !== '' ? $name : ('Task #' . (int) $r->id), 60),
					'status'   => isset($depts[$r->department_id]) ? $depts[$r->department_id] : '',
				);
			}
			return $out;
		}

		// --- Lead -------------------------------------------------------------
		$this->db->select('id, unique_id, company_name, customer_name, status')->from('leads');
		if ($q !== '') {
			$this->db->group_start()
				->like('company_name', $q)->or_like('customer_name', $q)
				->or_like('unique_id', $q)->group_end();
		}
		$rows = $this->db->order_by('id', 'desc')->limit($limit)->get()->result();

		$ids = array();
		foreach ($rows as $r) $ids[] = (int) $r->id;
		$stages  = $this->lead_stage_lookup();
		$current = $this->lead_current_stage($ids);

		foreach ($rows as $r) {
			$id = (int) $r->id;
			$stage_id = isset($current[$id]) ? $current[$id] : (int) $r->status;
			$out[] = array(
				'ref_type' => 'lead', 'ref_id' => $id,
				'code' => $r->unique_id !== '' ? $r->unique_id : ('LEAD-' . $id),
				'title' => $r->company_name !== '' ? $r->company_name : $r->customer_name,
				'status' => isset($stages[$stage_id]) ? $stages[$stage_id] : 'Open',
			);
		}
		return $out;
	}

	/* =========================================================================
	 *  WRITES
	 * =====================================================================*/

	/**
	 * Store a message and fan out events + notifications to every other member.
	 *
	 * @return int  new message id
	 */
	public function send_message($conv_id, $me, $body, $opts = array())
	{
		$now = date('Y-m-d H:i:s');
		$this->db->insert('chat_message', array(
			'conversation_id'   => (int) $conv_id,
			'sender_id'         => (int) $me['id'],
			'sender_type'       => $me['type'],
			'message_type'      => isset($opts['type']) ? $opts['type'] : 'text',
			'body'              => $body,
			'reply_to_id'       => isset($opts['reply_to']) ? (int) $opts['reply_to'] : 0,
			// a pointer at a message in ANOTHER conversation (forward / private reply)
			'origin_message_id' => isset($opts['origin_id']) ? (int) $opts['origin_id'] : 0,
			'origin_kind'       => isset($opts['origin_kind']) ? $opts['origin_kind'] : '',
			'created_at'        => $now,
		));
		$msg_id = (int) $this->db->insert_id();

		$this->db->where('id', (int) $conv_id)->update('chat_conversation', array(
			'last_message_id'  => $msg_id,
			'last_activity_at' => $now,
		));

		// sender has implicitly read their own message
		$this->db->where('conversation_id', (int) $conv_id)
			->where('user_id', (int) $me['id'])->where('user_type', $me['type'])
			->update('chat_participant', array('last_read_message_id' => $msg_id, 'last_read_at' => $now));

		return $msg_id;
	}

	/**
	 * Copy a message into another conversation.
	 *
	 * The body and any attachments come across; attachment rows point at the
	 * SAME file on disk rather than duplicating it, which is safe because the
	 * module never deletes uploaded files. The copy keeps a pointer back to the
	 * original so the card can say who wrote it and where.
	 *
	 * @return int  the new message id
	 */
	/**
	 * Have I just forwarded this exact message into this exact conversation?
	 *
	 * A double-click on Forward, or a retry after a slow response, sends the
	 * request twice and the second one is indistinguishable from a deliberate
	 * re-forward - except that nobody deliberately re-forwards the same message
	 * to the same room within seconds. The columns needed to spot it are
	 * already there, so this costs one indexed look-up and no new state.
	 */
	public function forwarded_recently($src_id, $target_conv_id, $me, $seconds = 20)
	{
		return $this->db->from('chat_message')
			->where('conversation_id', (int) $target_conv_id)
			->where('origin_message_id', (int) $src_id)
			->where('origin_kind', 'forward')
			->where('sender_id', (int) $me['id'])
			->where('sender_type', $me['type'])
			->where('created_at >=', date('Y-m-d H:i:s', time() - (int) $seconds))
			->count_all_results() > 0;
	}

	public function forward_message($src, $target_conv_id, $me)
	{
		$msg_id = $this->send_message($target_conv_id, $me, $src->body, array(
			'type'        => ($src->message_type === 'system' || $src->message_type === 'call')
								? 'text' : $src->message_type,
			'origin_id'   => (int) $src->id,
			'origin_kind' => 'forward',
		));

		$atts = $this->db->select('*')->from('chat_attachment')
			->where('message_id', (int) $src->id)->get()->result();

		foreach ($atts as $a) {
			$this->db->insert('chat_attachment', array(
				'message_id'      => $msg_id,
				'conversation_id' => (int) $target_conv_id,
				'file_name'       => $a->file_name,
				'stored_name'     => $a->stored_name,   // same file, no re-upload
				'rel_path'        => $a->rel_path,
				'file_ext'        => $a->file_ext,
				'mime_type'       => $a->mime_type,
				'file_size'       => (int) $a->file_size,
				'is_image'        => (int) $a->is_image,
				'uploaded_by'     => (int) $me['id'],
				'created_at'      => date('Y-m-d H:i:s'),
			));
		}
		return $msg_id;
	}

	/** Bare "X did Y" line rendered centred in the thread. */
	public function system_message($conv_id, $me, $text)
	{
		$now = date('Y-m-d H:i:s');
		$this->db->insert('chat_message', array(
			'conversation_id' => (int) $conv_id,
			'sender_id'       => (int) $me['id'],
			'sender_type'     => $me['type'],
			'message_type'    => 'system',
			'body'            => $text,
			'created_at'      => $now,
		));
		$id = (int) $this->db->insert_id();
		$this->db->where('id', (int) $conv_id)->update('chat_conversation', array(
			'last_message_id' => $id, 'last_activity_at' => $now,
		));
		return $id;
	}

	public function attach_file($conv_id, $msg_id, $me, $file)
	{
		$this->db->insert('chat_attachment', array(
			'message_id'      => (int) $msg_id,
			'conversation_id' => (int) $conv_id,
			'file_name'       => $file['file_name'],
			'stored_name'     => $file['stored_name'],
			'rel_path'        => $file['rel_path'],
			'file_ext'        => $file['file_ext'],
			'mime_type'       => $file['mime_type'],
			'file_size'       => (int) $file['file_size'],
			'is_image'        => (int) $file['is_image'],
			'uploaded_by'     => (int) $me['id'],
			'created_at'      => date('Y-m-d H:i:s'),
		));
		return (int) $this->db->insert_id();
	}

	public function attachment($id)
	{
		return $this->db->select('*')->from('chat_attachment')->where('id', (int) $id)->get()->row();
	}

	/** Record @mentions and raise a notification for each mentioned member. */
	public function save_mentions($conv_id, $msg_id, $me, $mentions, $conv_name)
	{
		if (empty($mentions)) return;
		$now = date('Y-m-d H:i:s');
		$seen = array();

		foreach ($mentions as $m) {
			// the client can send the same person twice ("@Raj ... @Raj")
			$key = $m['type'] . ':' . (int) $m['id'];
			if (isset($seen[$key])) continue;
			$seen[$key] = TRUE;

			// only real members can be mentioned
			$ok = $this->db->where('conversation_id', (int) $conv_id)
				->where('user_id', (int) $m['id'])->where('user_type', $m['type'])
				->where('is_active', 1)->count_all_results('chat_participant');
			if (!$ok) continue;
			if ((int) $m['id'] === (int) $me['id'] && $m['type'] === $me['type']) continue;

			$this->db->insert('chat_mention', array(
				'message_id'      => (int) $msg_id,
				'conversation_id' => (int) $conv_id,
				'user_id'         => (int) $m['id'],
				'user_type'       => $m['type'],
				'created_at'      => $now,
			));

			$this->notify($m, $conv_id, $msg_id, 'mention',
				$me['name'] . ' mentioned you',
				$conv_name !== '' ? ('in ' . $conv_name) : '');
		}
	}

	public function edit_message($msg_id, $body)
	{
		$this->db->where('id', (int) $msg_id)->update('chat_message', array(
			'body' => $body, 'is_edited' => 1, 'edited_at' => date('Y-m-d H:i:s'),
		));
	}

	public function delete_message($msg_id, $me)
	{
		$this->db->where('id', (int) $msg_id)->update('chat_message', array(
			'is_deleted' => 1,
			'deleted_at' => date('Y-m-d H:i:s'),
			'deleted_by' => (int) $me['id'],
			'is_pinned'  => 0,
		));
	}

	/** Add the reaction, or remove it when the same user clicks the same emoji. */
	public function toggle_reaction($msg_id, $me, $emoji)
	{
		$existing = $this->db->select('id')->from('chat_reaction')
			->where('message_id', (int) $msg_id)
			->where('user_id', (int) $me['id'])
			->where('user_type', $me['type'])
			->where('emoji', $emoji)->get()->row();

		if ($existing) {
			$this->db->where('id', $existing->id)->delete('chat_reaction');
			return 'removed';
		}
		// IGNORE, not INSERT: a double-click would otherwise hit uq_reaction
		$this->db->query(
			'INSERT IGNORE INTO chat_reaction (message_id, user_id, user_type, emoji, created_at)
			 VALUES (?, ?, ?, ?, ?)',
			array((int) $msg_id, (int) $me['id'], $me['type'], $emoji, date('Y-m-d H:i:s'))
		);
		return 'added';
	}

	/**
	 * Pin a message, or take an existing pin down. Still a toggle: the same
	 * control does both, so there is one thing to find rather than two.
	 *
	 * @param int $days how long the pin should last — a key of
	 *                  pin_durations(). 0 (the default) means no expiry, which
	 *                  is how every pin made before chat_004 behaves. Ignored
	 *                  when this call is an UNpin.
	 */
	public function toggle_pin($msg_id, $me, $days = 0)
	{
		$row = $this->db->select('is_pinned')->from('chat_message')->where('id', (int) $msg_id)->get()->row();
		if (!$row) return NULL;

		$new  = $row->is_pinned ? 0 : 1;
		$days = (int) $days;
		if (!array_key_exists($days, $this->pin_durations())) $days = 0;

		$set = array(
			'is_pinned' => $new,
			'pinned_by' => $new ? (int) $me['id'] : 0,
			'pinned_at' => $new ? date('Y-m-d H:i:s') : NULL,
		);

		// Only touched when the column is actually there — see
		// has_pin_expiry(). On a server that has the PHP but not chat_004,
		// pinning keeps working and simply never expires.
		if ($this->has_pin_expiry()) {
			$set['pinned_until'] = ($new && $days > 0)
				? date('Y-m-d H:i:s', strtotime('+' . $days . ' day'))
				: NULL;
		}

		$this->db->where('id', (int) $msg_id)->update('chat_message', $set);
		return $new;
	}

	public function tag_record($conv_id, $msg_id, $me, $ref_type, $ref_id)
	{
		$this->db->replace('chat_message_lead', array(
			'message_id'      => (int) $msg_id,
			'conversation_id' => (int) $conv_id,
			'ref_type'        => $ref_type,
			'ref_id'          => (int) $ref_id,
			'tagged_by'       => (int) $me['id'],
			'created_at'      => date('Y-m-d H:i:s'),
		));
	}

	/* =========================================================================
	 *  CALLS  (Zoom / Google Meet)
	 * =====================================================================*/

	/**
	 * Start a call in a conversation: records it, drops a join card into the
	 * thread, and returns both. The caller notifies + fans out the event.
	 */
	public function start_call($conv_id, $me, $provider, $join_url, $topic = '')
	{
		$now = date('Y-m-d H:i:s');

		// only one live call per conversation - re-starting simply supersedes
		$this->db->where('conversation_id', (int) $conv_id)->where('status', 'active')
			->update('chat_call', array('status' => 'ended', 'ended_at' => $now));

		$label = chat_provider_label($provider);
		$msg_id = $this->send_message($conv_id, $me, $me['name'] . ' started a ' . $label . ' call', array(
			'type' => 'call',
		));

		$this->db->insert('chat_call', array(
			'conversation_id' => (int) $conv_id,
			'message_id'      => $msg_id,
			'provider'        => $provider,
			'join_url'        => $join_url,
			'topic'           => $topic,
			'started_by'      => (int) $me['id'],
			'started_by_type' => $me['type'],
			'started_at'      => $now,
			'status'          => 'active',
		));

		return array('call_id' => (int) $this->db->insert_id(), 'message_id' => $msg_id);
	}

	/** Attach the real join link once the starter pastes it back. */
	public function set_call_url($call_id, $join_url)
	{
		$this->db->where('id', (int) $call_id)->update('chat_call', array('join_url' => $join_url));
	}

	public function end_call($call_id)
	{
		$this->db->where('id', (int) $call_id)->where('status', 'active')
			->update('chat_call', array('status' => 'ended', 'ended_at' => date('Y-m-d H:i:s')));
	}

	public function get_call($call_id)
	{
		return $this->db->select('*')->from('chat_call')->where('id', (int) $call_id)->get()->row();
	}

	/** The live call in a conversation, if any. */
	public function active_call($conv_id)
	{
		return $this->db->select('*')->from('chat_call')
			->where('conversation_id', (int) $conv_id)->where('status', 'active')
			->order_by('id', 'desc')->limit(1)->get()->row();
	}

	/** A user's saved personal meeting rooms, keyed by provider. */
	public function meeting_links($me)
	{
		// one empty slot per known provider, so the settings form always shows
		// every option even before anything has been saved
		$out = array();
		foreach (array_keys(chat_providers()) as $p) $out[$p] = '';

		if (!$this->db->table_exists('chat_user_meeting')) return $out;

		$rows = $this->db->select('provider, join_url')->from('chat_user_meeting')
			->where('user_id', (int) $me['id'])->where('user_type', $me['type'])->get()->result();
		foreach ($rows as $r) $out[$r->provider] = $r->join_url;
		return $out;
	}

	public function save_meeting_link($me, $provider, $join_url)
	{
		$this->db->query(
			'INSERT INTO chat_user_meeting (user_id, user_type, provider, join_url, updated_at)
			 VALUES (?, ?, ?, ?, ?)
			 ON DUPLICATE KEY UPDATE join_url = VALUES(join_url), updated_at = VALUES(updated_at)',
			array((int) $me['id'], $me['type'], $provider, $join_url, date('Y-m-d H:i:s'))
		);
	}

	/* =========================================================================
	 *  MEMBERSHIP
	 * =====================================================================*/

	public function add_members($conv_id, $me, $members)
	{
		$added = array(); $seen = array();

		foreach ($members as $m) {
			$type = isset($m['type']) ? $m['type'] : 'user';
			$key  = $type . ':' . (int) $m['id'];
			if (isset($seen[$key])) continue;            // same person twice in one request
			$seen[$key] = TRUE;

			$existing = $this->db->select('id, is_active')->from('chat_participant')
				->where('conversation_id', (int) $conv_id)
				->where('user_id', (int) $m['id'])->where('user_type', $type)->get()->row();
			if ($existing && $existing->is_active) continue;   // already a member

			$this->upsert_participant($conv_id, $m['id'], $type, 'member', $me['id']);
			$added[] = array('id' => (int) $m['id'], 'type' => $type);
		}

		if (!empty($added)) {
			$people = $this->resolve_people($added);
			$names  = array();
			foreach ($added as $a) $names[] = $people[$a['type'] . ':' . $a['id']]['name'];
			$this->system_message($conv_id, $me, $me['name'] . ' added ' . implode(', ', $names));
		}
		return $added;
	}

	public function remove_member($conv_id, $me, $user_id, $user_type)
	{
		$this->db->where('conversation_id', (int) $conv_id)
			->where('user_id', (int) $user_id)->where('user_type', $user_type)
			->update('chat_participant', array('is_active' => 0, 'left_at' => date('Y-m-d H:i:s')));

		$p = $this->resolve_people(array(array('id' => (int) $user_id, 'type' => $user_type)));
		$name = $p[$user_type . ':' . (int) $user_id]['name'];

		$self = ((int) $user_id === (int) $me['id'] && $user_type === $me['type']);
		$this->system_message($conv_id, $me, $self
			? ($name . ' left the group')
			: ($me['name'] . ' removed ' . $name));
	}

	public function set_member_role($conv_id, $user_id, $user_type, $role)
	{
		$this->db->where('conversation_id', (int) $conv_id)
			->where('user_id', (int) $user_id)->where('user_type', $user_type)
			->update('chat_participant', array('member_role' => $role));
	}

	/** Store the uploaded group photo (relative path under chat_upload_path). */
	public function set_group_photo($conv_id, $me, $rel)
	{
		$this->db->where('id', (int) $conv_id)->update('chat_conversation', array('avatar' => $rel));
		$this->system_message($conv_id, $me, $me['name'] . ' updated the group photo');
	}

	public function rename_channel($conv_id, $me, $name, $description)
	{
		$this->db->where('id', (int) $conv_id)->update('chat_conversation', array(
			'name' => $name, 'description' => $description,
		));
		$this->system_message($conv_id, $me, $me['name'] . ' renamed the group to “' . $name . '”');
	}

	public function mark_read($conv_id, $me, $up_to_id = 0)
	{
		if ($up_to_id <= 0) {
			$row = $this->db->select_max('id', 'mx')->from('chat_message')
				->where('conversation_id', (int) $conv_id)->get()->row();
			$up_to_id = $row ? (int) $row->mx : 0;
		}
		$this->db->where('conversation_id', (int) $conv_id)
			->where('user_id', (int) $me['id'])->where('user_type', $me['type'])
			->update('chat_participant', array(
				'last_read_message_id' => (int) $up_to_id,
				'last_read_at'         => date('Y-m-d H:i:s'),
			));

		// mentions inside this conversation are now seen
		$this->db->where('conversation_id', (int) $conv_id)
			->where('user_id', (int) $me['id'])->where('user_type', $me['type'])
			->where('message_id <=', (int) $up_to_id)
			->update('chat_mention', array('is_read' => 1));

		$this->db->where('conversation_id', (int) $conv_id)
			->where('user_id', (int) $me['id'])->where('user_type', $me['type'])
			->where('is_read', 0)
			->update('chat_notification', array('is_read' => 1, 'read_at' => date('Y-m-d H:i:s')));
	}

	/* =========================================================================
	 *  REAL-TIME  (event fan-out + long-poll reads)
	 * =====================================================================*/

	/**
	 * Fan an event out to every ACTIVE member except the actor. This is the one
	 * write that makes delivery real-time; the long-poll then does a single
	 * indexed read per client.
	 */
	public function push_event($conv_id, $event_type, $actor, $msg_id = 0, $only = NULL)
	{
		$targets = ($only !== NULL) ? $only : $this->participant_pairs($conv_id);
		if (empty($targets)) return;

		$now  = date('Y-m-d H:i:s');
		$rows = array();
		foreach ($targets as $t) {
			if ((int) $t['id'] === (int) $actor['id'] && $t['type'] === $actor['type']) continue;
			$rows[] = array(
				'user_id'         => (int) $t['id'],
				'user_type'       => $t['type'],
				'conversation_id' => (int) $conv_id,
				'message_id'      => (int) $msg_id,
				'event_type'      => $event_type,
				'actor_id'        => (int) $actor['id'],
				'actor_type'      => $actor['type'],
				'created_at'      => $now,
			);
		}
		if (!empty($rows)) $this->db->insert_batch('chat_event', $rows);
	}

	/** Everything that happened for me since event id X. */
	public function events_since($me, $since_id, $limit = 100)
	{
		return $this->db->select('*')->from('chat_event')
			->where('user_id', (int) $me['id'])
			->where('user_type', $me['type'])
			->where('id >', (int) $since_id)
			->order_by('id', 'asc')->limit($limit)->get()->result();
	}

	/** Current head of my event feed - the cursor a client starts from. */
	public function latest_event_id($me)
	{
		$row = $this->db->select_max('id', 'mx')->from('chat_event')
			->where('user_id', (int) $me['id'])->where('user_type', $me['type'])->get()->row();
		return $row && $row->mx ? (int) $row->mx : 0;
	}

	/**
	 * The single cheap query the long-poll loop runs once per second.
	 * Returns TRUE the instant something is waiting.
	 */
	public function has_events($me, $since_id)
	{
		$row = $this->db->query(
			'SELECT 1 FROM chat_event
			  WHERE user_id = ? AND user_type = ? AND id > ? LIMIT 1',
			array((int) $me['id'], $me['type'], (int) $since_id)
		)->row();
		return $row !== NULL;
	}

	public function notify($to, $conv_id, $msg_id, $type, $title, $body)
	{
		$this->db->insert('chat_notification', array(
			'user_id'         => (int) $to['id'],
			'user_type'       => $to['type'],
			'conversation_id' => (int) $conv_id,
			'message_id'      => (int) $msg_id,
			'notif_type'      => $type,
			'title'           => $this->shorten($title, 190),
			'body'            => $this->shorten($body, 390),
			'created_at'      => date('Y-m-d H:i:s'),
		));
	}

	/** Raise a notification for every member of a conversation but the actor. */
	public function notify_conversation($conv_id, $actor, $msg_id, $type, $title, $body, $skip_muted = TRUE)
	{
		$rows = $this->db->select('user_id, user_type, is_muted')->from('chat_participant')
			->where('conversation_id', (int) $conv_id)->where('is_active', 1)->get()->result();

		$now  = date('Y-m-d H:i:s');
		$bulk = array();
		foreach ($rows as $r) {
			if ((int) $r->user_id === (int) $actor['id'] && $r->user_type === $actor['type']) continue;
			if ($skip_muted && $r->is_muted) continue;
			$bulk[] = array(
				'user_id'         => (int) $r->user_id,
				'user_type'       => $r->user_type,
				'conversation_id' => (int) $conv_id,
				'message_id'      => (int) $msg_id,
				'notif_type'      => $type,
				'title'           => $this->shorten($title, 190),
				'body'            => $this->shorten($body, 390),
				'created_at'      => $now,
			);
		}
		if (!empty($bulk)) $this->db->insert_batch('chat_notification', $bulk);
	}

	/**
	 * Mentions of ME among the given messages, ready to alert on.
	 *
	 * Being mentioned deserves its own signal: the plain message event only
	 * tells the client "something arrived", which produces no alert at all when
	 * the conversation happens to be open. This is what makes "@Meenu" reach
	 * Meenu whatever she is looking at.
	 */
	public function mention_alerts($me, $message_ids)
	{
		$message_ids = array_filter(array_unique(array_map('intval', (array) $message_ids)));
		if (empty($message_ids)) return array();

		$rows = $this->db->select('mn.message_id, mn.conversation_id, m.sender_id, m.sender_type, m.body, c.type AS conv_type, c.name AS conv_name', FALSE)
			->from('chat_mention mn')
			->join('chat_message m', 'm.id = mn.message_id')
			->join('chat_conversation c', 'c.id = mn.conversation_id')
			->where('mn.user_id', (int) $me['id'])
			->where('mn.user_type', $me['type'])
			->where_in('mn.message_id', $message_ids)
			->where('m.is_deleted', 0)
			->get()->result();

		if (empty($rows)) return array();

		$pairs = array();
		foreach ($rows as $r) $pairs[] = array('id' => (int) $r->sender_id, 'type' => $r->sender_type);
		$people = $this->resolve_people($pairs);

		$out = array();
		foreach ($rows as $r) {
			$k = $r->sender_type . ':' . $r->sender_id;
			$out[] = array(
				'message_id'   => (int) $r->message_id,
				'conversation' => (int) $r->conversation_id,
				'from'         => isset($people[$k]) ? $people[$k]['name'] : 'Someone',
				'conv_name'    => ($r->conv_type === 'direct') ? '' : $r->conv_name,
				'preview'      => $this->shorten($this->plain(strip_tags($r->body)), 110),
			);
		}
		return $out;
	}

	public function notifications($me, $limit = 25)
	{
		return $this->db->select('*')->from('chat_notification')
			->where('user_id', (int) $me['id'])->where('user_type', $me['type'])
			->order_by('id', 'desc')->limit($limit)->get()->result();
	}

	public function unread_totals($me)
	{
		$sql = 'SELECT
					SUM(CASE WHEN c.type = "direct" THEN t.cnt ELSE 0 END) AS direct_unread,
					SUM(CASE WHEN c.type <> "direct" THEN t.cnt ELSE 0 END) AS channel_unread,
					SUM(t.cnt) AS total
				FROM (
					SELECT m.conversation_id, COUNT(*) AS cnt
					FROM chat_message m
					JOIN chat_participant p
					  ON p.conversation_id = m.conversation_id
					 AND p.user_id = ? AND p.user_type = ? AND p.is_active = 1
					WHERE m.id > p.last_read_message_id
					  AND m.is_deleted = 0
					  AND m.message_type <> "system"
					  AND NOT (m.sender_id = ? AND m.sender_type = ?)
					GROUP BY m.conversation_id
				) t
				JOIN chat_conversation c ON c.id = t.conversation_id AND c.is_archived = 0';

		$row = $this->db->query($sql, array((int) $me['id'], $me['type'], (int) $me['id'], $me['type']))->row();

		$mentions = $this->db->where('user_id', (int) $me['id'])->where('user_type', $me['type'])
			->where('is_read', 0)->count_all_results('chat_mention');

		return array(
			'direct'   => $row && $row->direct_unread  ? (int) $row->direct_unread  : 0,
			'channels' => $row && $row->channel_unread ? (int) $row->channel_unread : 0,
			'total'    => $row && $row->total          ? (int) $row->total          : 0,
			'mentions' => (int) $mentions,
		);
	}

	/* =========================================================================
	 *  PRESENCE / TYPING
	 * =====================================================================*/

	/**
	 * Heartbeat. Single-statement upsert - the messenger fires several requests
	 * at once on load, and a check-then-insert would race on the very first one.
	 * typing_* is only touched when the user is actually typing, so a plain
	 * heartbeat never wipes someone's typing state.
	 */
	public function touch_presence($me, $typing_conv = 0)
	{
		$now  = date('Y-m-d H:i:s');
		$tail = ($typing_conv > 0)
			? 'last_seen_at = VALUES(last_seen_at), typing_conv_id = VALUES(typing_conv_id), typing_at = VALUES(typing_at)'
			: 'last_seen_at = VALUES(last_seen_at)';

		$this->db->query(
			'INSERT INTO chat_presence (user_id, user_type, last_seen_at, typing_conv_id, typing_at)
			 VALUES (?, ?, ?, ?, ?)
			 ON DUPLICATE KEY UPDATE ' . $tail,
			array((int) $me['id'], $me['type'], $now, (int) $typing_conv, $typing_conv > 0 ? $now : NULL)
		);
	}

	/** Everyone seen in the last 2 minutes, keyed "type:id". */
	public function online_map()
	{
		static $c = NULL;
		if ($c !== NULL) return $c;
		$c = array();
		$rows = $this->db->select('user_id, user_type')->from('chat_presence')
			->where('last_seen_at >=', date('Y-m-d H:i:s', time() - 120))->get()->result();
		foreach ($rows as $r) $c[$r->user_type . ':' . $r->user_id] = TRUE;
		return $c;
	}

	/** Who is typing in this conversation right now (last 6 seconds). */
	public function typing_in($conv_id, $me)
	{
		$rows = $this->db->select('user_id, user_type')->from('chat_presence')
			->where('typing_conv_id', (int) $conv_id)
			->where('typing_at >=', date('Y-m-d H:i:s', time() - 6))
			->get()->result();

		$pairs = array();
		foreach ($rows as $r) {
			if ((int) $r->user_id === (int) $me['id'] && $r->user_type === $me['type']) continue;
			$pairs[] = array('id' => (int) $r->user_id, 'type' => $r->user_type);
		}
		if (empty($pairs)) return array();

		$people = $this->resolve_people($pairs);
		$names = array();
		foreach ($pairs as $p) $names[] = $people[$p['type'] . ':' . $p['id']]['name'];
		return $names;
	}

	/* =========================================================================
	 *  JOB / TECHNICAL-TEAM CHANNELS
	 * =====================================================================*/

	/**
	 * Find-or-create the channel bound to a ticket, seeded with the people the
	 * ticket already knows about: creator, account manager, help desk and the
	 * assigned engineer(s).
	 */
	/**
	 * Every active department leader.
	 *
	 * PMS records a department's leader on `prestogroup_teams.team_leader`
	 * (not `departments.departmenthead`, which is a separate and less
	 * consistently maintained field). This is the SAME set of people
	 * Task_model::triggernotificationondfrelease() already WhatsApps when a DF
	 * is released, so the chat group matches who is already told.
	 *
	 * Users who have left (`user_status` = 0) are dropped — a departed leader
	 * would otherwise be added to every new DF group forever.
	 *
	 * @return array  array of array('id'=>, 'type'=>'user'), de-duplicated
	 */
	public function department_leaders()
	{
		if (!$this->db->table_exists('prestogroup_teams')) return array();

		$rows = $this->db->select('t.team_leader, t.department_id', FALSE)
			->from('prestogroup_teams t')
			->join('system_users u', 'u.user_id = t.team_leader', 'inner')
			->where('t.status', 1)
			->where('t.team_leader >', 0)
			->where('u.user_status', 1)
			->get()->result();

		$out = array();
		$seen = array();
		foreach ($rows as $r) {
			$uid = (int) $r->team_leader;
			// one person can lead more than one department
			if ($uid <= 0 || isset($seen[$uid])) continue;
			$seen[$uid] = TRUE;
			$out[] = array('id' => $uid, 'type' => 'user');
		}
		return $out;
	}

	/** Did this user release this DF? Used to scope the one-click button. */
	public function df_released_by($df_id, $user_id)
	{
		$row = $this->db->select('added_by')->from('df_release')
			->where('id', (int) $df_id)->get()->row();
		return $row && (int) $row->added_by === (int) $user_id;
	}

	/** The conversation bound to a DF, or NULL. */
	public function df_channel_id($df_id)
	{
		$row = $this->db->select('id')->from('chat_conversation')
			->where('ref_type', 'df')->where('ref_id', (int) $df_id)->get()->row();
		return $row ? (int) $row->id : NULL;
	}

	/**
	 * Find-or-create the chat group for one DF.
	 *
	 * Named "DF - <df no> - Group" and seeded with every active department
	 * leader plus whoever released the DF. Called from two places:
	 *
	 *   - Task::dfrelease(), right after a DF is released, so the group exists
	 *     before anybody looks for it
	 *   - Chat::df(), the one-click button for DFs released before this
	 *     feature existed
	 *
	 * IDEMPOTENT. Called again on a DF that already has a group it ADDS any
	 * leader who is new and removes nobody — so a change of department head
	 * brings the new person in without disturbing the room or its history.
	 * That is what makes the one-click button safe to press twice.
	 *
	 * Everyone who is newly added gets a chat notification (the bell, the
	 * toast and the desktop alert); people already in the room get nothing,
	 * which is what stops a second press spamming twenty people.
	 *
	 * @param  int   $df_id  df_release.id
	 * @param  array $me     the acting identity
	 * @return array|NULL    array(id, created, added) or NULL if no such DF
	 */
	public function ensure_df_channel($df_id, $me)
	{
		$df_id = (int) $df_id;

		$df = $this->db->select('id, df_no, df_description, added_by')
			->from('df_release')->where('id', $df_id)->get()->row();
		if (!$df) return NULL;

		$df_no = strtoupper(trim((string) $df->df_no));
		if ($df_no === '') $df_no = 'DF-' . $df_id;

		// The name the customer asked for. Built in one place so the release
		// hook and the one-click button can never disagree about it.
		$name = 'DF - ' . $df_no . ' - Group';
		$desc = $this->shorten(trim(strip_tags((string) $df->df_description)), 180);

		$members = $this->department_leaders();

		// whoever released the DF, even if they lead no department
		$owner = (int) $df->added_by;
		if ($owner > 0) {
			$have = FALSE;
			foreach ($members as $m) if ((int) $m['id'] === $owner) { $have = TRUE; break; }
			if (!$have) $members[] = array('id' => $owner, 'type' => 'user');
		}

		$existing = $this->df_channel_id($df_id);

		if ($existing) {
			$added = $this->add_members($existing, $me, $members);

			// Nobody is notified about their OWN arrival.
			$others = array();
			foreach ($added as $a) {
				if ((int) $a['id'] === (int) $me['id'] && $a['type'] === $me['type']) continue;
				$others[] = $a;
			}
			if (!empty($others)) {
				$this->notify_people($others, $existing, $name,
					'You have been added to the group for ' . $df_no . '.');
			}

			// Whoever pressed the button is reaching for this conversation, so
			// put them in it. Without this a non-leader opening an existing DF
			// group is redirected into a room they are not a member of, and
			// Chat::openable() quietly refuses — the messenger loads with
			// nothing selected and no explanation, which reads as broken.
			$joined = $this->join_self($existing, $me);
			$this->sync_df_assignees($existing, $df_id, $me);

			if (!empty($added) || $joined) $this->push_event($existing, 'member', $me, 0);

			return array('id' => $existing, 'created' => FALSE, 'added' => count($others));
		}

		$conv_id = $this->create_channel($me, $name, $members, $desc, 'df', $df_id);

		// anyone already holding a task on this DF belongs in the room too
		$this->sync_df_assignees($conv_id, $df_id, $me);

		// create_channel posts the system message and pushes the realtime
		// event, but writes no chat_notification rows — that is what puts the
		// group in the bell dropdown and raises a toast, so it is done here.
		$this->notify_conversation(
			$conv_id, $me, 0, 'group', $name,
			'A group has been created for ' . $df_no . '. You are a member.'
		);

		return array('id' => $conv_id, 'created' => TRUE, 'added' => count($members));
	}

	/**
	 * Make sure the acting user is an active member of a conversation.
	 *
	 * @return bool TRUE if they were actually added (i.e. were not already in)
	 */
	public function join_self($conv_id, $me)
	{
		$row = $this->db->select('id, is_active')->from('chat_participant')
			->where('conversation_id', (int) $conv_id)
			->where('user_id', (int) $me['id'])
			->where('user_type', $me['type'])->get()->row();

		if ($row && $row->is_active) return FALSE;

		$this->upsert_participant($conv_id, $me['id'], $me['type'], 'member', $me['id']);
		$this->system_message($conv_id, $me, $me['name'] . ' joined the group');
		return TRUE;
	}

	/**
	 * Notify a specific list of people about a conversation.
	 *
	 * notify_conversation() targets every active member, which is wrong when
	 * only a few people were just added — the rest have been in the room for
	 * weeks and do not need telling again.
	 *
	 * @param array $people  array of array('id'=>, 'type'=>)
	 */
	public function notify_people($people, $conv_id, $title, $body)
	{
		if (empty($people)) return;

		$now  = date('Y-m-d H:i:s');
		$bulk = array();
		foreach ($people as $p) {
			$bulk[] = array(
				'user_id'         => (int) $p['id'],
				'user_type'       => isset($p['type']) ? $p['type'] : 'user',
				'conversation_id' => (int) $conv_id,
				'message_id'      => 0,
				'notif_type'      => 'group',
				'title'           => $this->shorten($title, 190),
				'body'            => $this->shorten($body, 390),
				'created_at'      => $now,
			);
		}
		$this->db->insert_batch('chat_notification', $bulk);
	}

	/**
	 * DFs and whether each already has its chat group — the backfill list.
	 *
	 * DF groups became automatic at release only from the date this feature
	 * shipped, so every DF already in flight needs one creating once. This is
	 * what the /chat/df-groups screen renders.
	 *
	 * @param  bool $only_running  exclude completed DFs (df_status = 1)
	 * @param  int  $owner_id      when > 0, only DFs this user released.
	 *                             Marketing sees their own; administrators
	 *                             pass 0 and see everything. See
	 *                             chat_df_group_scope().
	 * @return array
	 */
	public function df_group_overview($only_running = TRUE, $limit = 500, $owner_id = 0)
	{
		$only_running = (bool) $only_running;
		$owner_id     = (int) $owner_id;
		$limit        = (int) $limit;

		// `poreceived` is AGGREGATED IN A SUBQUERY, not joined flat: a DF can
		// have several POs, and a flat join would multiply those rows against
		// the chat_conversation join. Collapsing poreceived to one row per
		// df_id first makes that impossible.
		//
		// The penalty is NOT taken from here — it comes from df_penalty_map()
		// below, so this screen and the messenger read one definition.
		$sql = 'SELECT d.id, d.df_no, d.df_description, d.added_on, d.df_status, d.on_hold,
		               MAX(p.company_name)   AS company_name,
		               MAX(c.id)             AS conv_id
		        FROM df_release d
		        LEFT JOIN (
		            SELECT df_id, MAX(company_name) AS company_name
		            FROM poreceived
		            WHERE df_id > 0
		            GROUP BY df_id
		        ) p ON p.df_id = d.id
		        LEFT JOIN chat_conversation c ON c.ref_type = ? AND c.ref_id = d.id
		        WHERE 1 = 1';

		// bound rather than inlined: a double-quoted literal would be read as
		// an identifier, not a string, under MySQL ANSI_QUOTES.
		$params = array('df');
		if ($only_running) $sql .= ' AND d.df_status = 0';
		if ($owner_id > 0) { $sql .= ' AND d.added_by = ?'; $params[] = $owner_id; }

		$sql .= ' GROUP BY d.id ORDER BY d.id DESC LIMIT ' . ($limit > 0 ? $limit : 500);

		$rows = $this->db->query($sql, $params)->result();

		$df_ids = array();
		foreach ($rows as $r) $df_ids[] = (int) $r->id;
		$penalties = $this->df_penalty_map($df_ids);

		$out = array();
		foreach ($rows as $r) {
			$code  = strtoupper(trim((string) $r->df_no));
			$df_id = (int) $r->id;

			$out[] = array(
				'df_id'    => (int) $r->id,
				'df_no'    => $code !== '' ? $code : ('DF-' . (int) $r->id),
				'title'    => $this->shorten(trim(strip_tags((string) $r->df_description)), 80),
				'company'  => (string) $r->company_name,
				'released' => ($r->added_on && $r->added_on !== '0000-00-00 00:00:00')
					? date('d M Y', strtotime($r->added_on)) : '',
				'status'   => ((int) $r->on_hold === 1) ? 'On hold'
					: (((int) $r->df_status === 1) ? 'Completed' : 'Running'),
				'conv_id'  => $r->conv_id ? (int) $r->conv_id : 0,
				// the flag only — see df_info() for why no figure leaves here
				'penalty'  => isset($penalties[$df_id]),
			);
		}
		return $out;
	}

	/* =========================================================================
	 *  GROUP LIFECYCLE
	 * =====================================================================*/

	/**
	 * Delete a group.
	 *
	 * SOFT delete — sets `is_archived`, which my_conversations() and
	 * unread_totals() already filter on, so the room disappears from every
	 * member's list at once. Nothing is dropped from chat_message.
	 *
	 * That is deliberate. A hard delete would take the thread, its
	 * attachments, and every reply pointing into it with no way back, on one
	 * click, for everyone. Archiving is indistinguishable from deletion to the
	 * people using it and is one UPDATE to undo if it was a mistake.
	 */
	public function delete_channel($conv_id, $me)
	{
		$this->db->where('id', (int) $conv_id)
			->update('chat_conversation', array('is_archived' => 1));

		// recorded in the thread so the history explains itself if the row is
		// ever un-archived
		$this->system_message($conv_id, $me, $me['name'] . ' deleted the group');
		$this->push_event($conv_id, 'channel', $me, 0);
		return TRUE;
	}

	/* =========================================================================
	 *  TEAMS
	 * =====================================================================*/

	/**
	 * The people this user leads.
	 *
	 * PMS models a team as `prestogroup_teams` (one row per department, with a
	 * `team_leader`) and its roster as `presto_team_members.employee_id`. A
	 * leader may hold more than one team, so this unions them.
	 *
	 * Used to let a team leader pull their own people into a DF group without
	 * holding the app-wide manage_members capability — they are accountable
	 * for their department's part of the DF, so they are the right person to
	 * decide who from it belongs in the room.
	 *
	 * @return array of int user ids
	 */
	public function my_team_member_ids($user_id)
	{
		$user_id = (int) $user_id;
		if ($user_id <= 0) return array();
		if (!$this->db->table_exists('presto_team_members') || !$this->db->table_exists('prestogroup_teams')) {
			return array();
		}

		$rows = $this->db->select('m.employee_id', FALSE)
			->from('presto_team_members m')
			->join('prestogroup_teams t', 't.team_id = m.team_id', 'inner')
			->join('system_users u', 'u.user_id = m.employee_id', 'inner')
			->where('t.team_leader', $user_id)
			->where('t.status', 1)
			->where('u.user_status', 1)
			->where('m.employee_id >', 0)
			->get()->result();

		$out = array();
		foreach ($rows as $r) {
			$id = (int) $r->employee_id;
			if ($id > 0 && !in_array($id, $out, TRUE)) $out[] = $id;
		}
		return $out;
	}

	/**
	 * Bring everyone assigned a task on this DF into its group.
	 *
	 * WHY THIS IS A RECONCILE AND NOT A HOOK
	 * --------------------------------------
	 * `task_department_wise_scheduling.assigned_user` is written in 52 places
	 * across 15 controllers and models (Dashboard, Task, DF_revision, Form,
	 * Opportunity, the mobile API...). Hooking each one would mean editing 52
	 * working call sites, missing some, and missing every one added later.
	 *
	 * So the membership is DERIVED instead: this compares the group against
	 * the DF's assignments and adds whoever is missing. It cannot be bypassed
	 * by an assignment path it has never heard of, and a path added next year
	 * is covered for free.
	 *
	 * The cost is one indexed lookup that usually inserts nothing. It runs
	 * when the group is opened and when ensure_df_channel() is called, so an
	 * assignee lands in the room the first time anybody looks at it.
	 *
	 * @return array the people actually added
	 */
	public function sync_df_assignees($conv_id, $df_id, $me)
	{
		$conv_id = (int) $conv_id;
		$df_id   = (int) $df_id;
		if ($conv_id <= 0 || $df_id <= 0) return array();

		$rows = $this->db->select('assigned_user, userid')
			->from('task_department_wise_scheduling')
			->where('df_id', $df_id)
			->get()->result();
		if (empty($rows)) return array();

		$want = array();
		foreach ($rows as $r) {
			foreach (array($r->assigned_user, $r->userid) as $uid) {
				$uid = (int) $uid;
				if ($uid > 0 && !isset($want[$uid])) $want[$uid] = TRUE;
			}
		}
		if (empty($want)) return array();

		// people who have left the company are not dragged into new rooms
		$active = $this->db->select('user_id')->from('system_users')
			->where_in('user_id', array_keys($want))
			->where('user_status', 1)->get()->result();

		$members = array();
		foreach ($active as $u) $members[] = array('id' => (int) $u->user_id, 'type' => 'user');
		if (empty($members)) return array();

		$added = $this->add_members($conv_id, $me, $members);

		if (!empty($added)) {
			$conv = $this->get_conversation($conv_id);
			$name = $conv ? $conv->name : 'a DF group';
			$this->notify_people($added, $conv_id, $name,
				'You have been added because you are assigned a task on this DF.');
			$this->push_event($conv_id, 'member', $me, 0);
		}
		return $added;
	}

	/** Housekeeping for the transport table. */
	public function prune_events($days = 7)
	{
		$this->db->where('created_at <', date('Y-m-d H:i:s', time() - ($days * 86400)))
			->delete('chat_event');
		$this->db->where('created_at <', date('Y-m-d H:i:s', time() - (30 * 86400)))
			->where('is_read', 1)->delete('chat_notification');
	}

	/* =========================================================================
	 *  ARCHIVING  -  the other end of a DF group's life
	 *
	 *  A DF group is created the day the DF is released and goes quiet the day
	 *  the machine ships, but it keeps every photo, drawing and spreadsheet
	 *  anybody posted sitting on disk forever. Archiving closes the room, packs
	 *  its attachments into ONE .zip, and later releases the loose originals.
	 *
	 *  ARCHIVED IS NOT DELETED. `is_archived` is this module's soft-delete flag
	 *  (see delete_channel()); archiving has its own `archived_at`. A deleted
	 *  group is gone for everyone; an archived one sits under its own heading,
	 *  is still readable, and comes back in one click.
	 * =====================================================================*/

	/**
	 * Has Database/chat_005_archive.sql been run?
	 *
	 * Same guard, same reason, as has_department_column() and
	 * has_pin_expiry(): PMS deploys by uploading files, so this PHP can land
	 * before its migration. Without the columns nothing archives and the
	 * module behaves exactly as it did before — which is the failure you can
	 * leave running over a weekend.
	 */
	private function has_archive_columns()
	{
		static $ok = NULL;
		if ($ok !== NULL) return $ok;
		return ($ok = ($this->db->field_exists('archived_at', 'chat_conversation')
					&& $this->db->field_exists('archived_at', 'chat_attachment')));
	}

	/** Where the bundles live: chat_uploads/_archives/, which inherits that
	 *  directory's .htaccess rather than needing protection of its own. */
	private function archive_dir()
	{
		$dir = chat_upload_path . chat_archive_dir;
		if (!is_dir($dir)) @mkdir($dir, 0755, TRUE);
		return $dir;
	}

	/**
	 * The DF groups that are ready to be archived.
	 *
	 * TWO conditions, both required, exactly as the dispatch desk describes it:
	 *
	 *   1. task 103 on the DF is complete. 103 is the dispatch task — the
	 *      codebase already treats it that way (see the cascade rule in
	 *      Dashboard_model::auto_update_df_status(), which closes a DF's other
	 *      tasks once 103 lands).
	 *   2. df_release.df_status = 1, i.e. the DF itself is closed.
	 *
	 * Either one alone is not enough. 103 can be ticked while the DF is still
	 * open and being corrected, and a DF can be closed for reasons that are
	 * not a dispatch at all. Requiring both means the group is archived when
	 * the machine has actually gone.
	 *
	 * A SWEEP rather than a hook on the moment of dispatch, because there are
	 * four separate places in PMS that close a DF (three in Task.php and the
	 * auto-close in Dashboard_model) and a fifth will be added the week after
	 * this ships. A sweep cannot be forgotten by a new code path: it simply
	 * finds the group on the next pass.
	 *
	 * @return array conversation rows, each with its df_no, ready to archive
	 */
	public function archivable_df_groups($limit = 25)
	{
		if (!$this->has_archive_columns()) return array();

		// EXISTS, not a JOIN with GROUP BY.
		//
		// The first version joined task_department_wise_scheduling and leaned
		// on GROUP BY c.id to collapse the duplicate rows a DF can have for
		// task 103. That is invalid under ONLY_FULL_GROUP_BY, which is the
		// DEFAULT on MySQL 5.7 and later: every selected column must be
		// grouped or aggregated, and df_no / task_completed_on were neither.
		// The query errored, and with db_debug off in production CI returns
		// FALSE rather than raising — so ->result() was called on a boolean
		// and took the whole request down with a 500.
		//
		// EXISTS says what is actually meant ("a DF with a completed task
		// 103"), cannot multiply rows, and needs no GROUP BY at all. The
		// dispatch date comes from its own scalar subquery.
		$sql = 'SELECT c.id, c.name, c.ref_id AS df_id, d.df_no,
				       (SELECT MAX(t2.task_completed_on)
				          FROM task_department_wise_scheduling t2
				         WHERE t2.df_id = c.ref_id AND t2.taskid = 103
				           AND t2.task_status = 1) AS dispatched_on
				  FROM chat_conversation c
				  JOIN df_release d ON d.id = c.ref_id AND d.df_status = 1
				 WHERE c.ref_type = "df" AND c.ref_id > 0
				   AND c.is_archived = 0
				   AND c.archived_at IS NULL
				   AND EXISTS (SELECT 1
				                 FROM task_department_wise_scheduling t
				                WHERE t.df_id = c.ref_id AND t.taskid = 103
				                  AND t.task_status = 1)
				 ORDER BY c.id ASC
				 LIMIT ' . (int) $limit;

		$q = $this->db->query($sql);

		// Belt and braces: with db_debug off a failed query is FALSE, not an
		// exception. Nothing in housekeeping is worth a 500 on the messenger.
		return is_object($q) ? $q->result() : array();
	}

	/**
	 * Archive one conversation: pack its files, close the room, say so.
	 *
	 * ORDER MATTERS and is the whole safety story. The zip is built and
	 * REOPENED FOR VERIFICATION before a single flag is written, so a group is
	 * never marked archived against a bundle that turned out to be unreadable.
	 * Originals are not touched here at all — that is purge_archived_originals(),
	 * a grace period later.
	 *
	 * @param  string $reason short text shown on the archived row
	 * @return array  ok / error / files / bytes
	 */
	public function archive_conversation($conv_id, $me, $reason = '', $note = '')
	{
		$conv_id = (int) $conv_id;
		if (!$this->has_archive_columns()) {
			return array('ok' => FALSE, 'error' => 'Run Database/chat_005_archive.sql first.');
		}

		$conv = $this->get_conversation($conv_id);
		if (!$conv)                     return array('ok' => FALSE, 'error' => 'No such conversation.');
		if ($conv->archived_at)         return array('ok' => FALSE, 'error' => 'Already archived.');

		$zip = $this->build_archive_zip($conv_id, $conv);
		if (!$zip['ok']) return $zip;

		// The message people actually see. Posted BEFORE the flags so it is
		// the last thing in the thread rather than the first thing after it.
		if ($note === '') {
			$note = 'Thank you all for your efforts — this DF has been dispatched. '
			      . 'This group is now archived. Every file shared here has been '
			      . 'bundled into one download, and the group can be reopened at '
			      . 'any time.';
		}
		$this->system_message($conv_id, $me, $note);

		$this->db->where('id', $conv_id)->update('chat_conversation', array(
			'archived_at'    => date('Y-m-d H:i:s'),
			'archived_by'    => (int) $me['id'],
			'archive_reason' => $this->shorten($reason, 180),
			'archive_zip'    => $zip['rel'],
			'archive_bytes'  => (int) $zip['bytes'],
			'archive_files'  => (int) $zip['files'],
		));

		$this->push_event($conv_id, 'channel', $me, 0);
		return array('ok' => TRUE, 'files' => $zip['files'], 'bytes' => $zip['bytes']);
	}

	/**
	 * Collect every attachment in a conversation into one .zip.
	 *
	 * Names inside the bundle are the ORIGINAL filenames, not the randomised
	 * stored_name — the bundle is for a person opening it in Explorer a year
	 * from now, and "a3f9c2....bin" tells them nothing. Duplicates get a
	 * numeric suffix rather than silently overwriting each other, which a zip
	 * will otherwise happily do.
	 *
	 * A missing original is SKIPPED, not fatal: a file somebody deleted off
	 * the server by hand should not stop the other two hundred being saved.
	 * The count that comes back is what actually went in.
	 */
	private function build_archive_zip($conv_id, $conv = NULL)
	{
		if (!class_exists('ZipArchive')) {
			return array('ok' => FALSE, 'error' => 'PHP ZipArchive is not available on this server.');
		}

		$rows = $this->db->select('id, file_name, stored_name, rel_path, created_at')
			->from('chat_attachment')->where('conversation_id', (int) $conv_id)
			->order_by('id', 'asc')->get()->result();

		// A group with no files still archives — the room closing is the point,
		// the bundle is a consequence. It simply has no zip.
		if (empty($rows)) return array('ok' => TRUE, 'rel' => '', 'bytes' => 0, 'files' => 0);

		$label = 'conversation-' . (int) $conv_id;
		if ($conv && isset($conv->ref_type) && chat_clean_ref_type($conv->ref_type) === 'df') {
			$df = $this->db->select('df_no')->from('df_release')
				->where('id', (int) $conv->ref_id)->get()->row();
			if ($df && trim((string) $df->df_no) !== '') {
				$label = 'DF-' . preg_replace('/[^A-Za-z0-9._-]+/', '-', trim($df->df_no));
			}
		}

		$rel  = chat_archive_dir . $label . '-' . (int) $conv_id . '.zip';
		$path = chat_upload_path . $rel;
		$this->archive_dir();
		if (is_file($path)) @unlink($path);      // rebuilding replaces cleanly

		$zip = new ZipArchive();
		if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
			return array('ok' => FALSE, 'error' => 'Could not create the archive file. Check that '
			                                     . chat_upload_path . chat_archive_dir . ' is writable.');
		}

		$used = array();
		$n    = 0;
		foreach ($rows as $r) {
			// The name is taken for EVERY row, including ones whose file is
			// gone, BEFORE the skip. unique_zip_name() is a running counter,
			// so skipping first would mean a missing file does not consume its
			// "(2)" — and restore/purge, which replay this over all rows to
			// work out what a file is called inside the bundle, would then
			// compute different names than the ones actually written.
			$name = $this->unique_zip_name($r->file_name, $used);

			$src = chat_upload_path . $r->rel_path . $r->stored_name;
			if (!is_file($src)) continue;

			if ($zip->addFile($src, $name)) $n++;
		}

		// A manifest, because a zip of two hundred files with no context is a
		// puzzle. Who sent what, when, and how big — readable in Notepad.
		$zip->addFromString('FILE-LIST.txt', $this->archive_manifest($conv_id, $rows, $label));
		$zip->close();

		// VERIFY before anything is marked archived. A zip that will not
		// reopen is worse than no zip at all, because it looks like a backup.
		$check = new ZipArchive();
		if ($check->open($path) !== TRUE) {
			@unlink($path);
			return array('ok' => FALSE, 'error' => 'The archive was written but could not be reopened.');
		}
		$check->close();

		clearstatcache(TRUE, $path);
		return array('ok' => TRUE, 'rel' => $rel, 'bytes' => (int) filesize($path), 'files' => $n);
	}

	/** Keep two files called "photo.jpg" from becoming one file in the zip. */
	private function unique_zip_name($name, &$used)
	{
		$name = preg_replace('/[\\\/:*?"<>|]+/', '_', (string) $name);
		if ($name === '') $name = 'file';

		$key = strtolower($name);
		if (!isset($used[$key])) { $used[$key] = 1; return $name; }

		$dot  = strrpos($name, '.');
		$stem = ($dot === FALSE) ? $name : substr($name, 0, $dot);
		$ext  = ($dot === FALSE) ? ''    : substr($name, $dot);

		$i = $used[$key];
		do { $i++; $try = $stem . ' (' . $i . ')' . $ext; } while (isset($used[strtolower($try)]));

		$used[$key] = $i;
		$used[strtolower($try)] = 1;
		return $try;
	}

	/** The human index that ships inside every bundle. */
	private function archive_manifest($conv_id, $rows, $label)
	{
		$who = $this->db->query(
			'SELECT a.id, u.first_name, u.last_name
			   FROM chat_attachment a
			   LEFT JOIN system_users u ON u.user_id = a.uploaded_by
			  WHERE a.conversation_id = ?', array((int) $conv_id))->result();
		$names = array();
		foreach ($who as $w) {
			$names[(int) $w->id] = trim($w->first_name . ' ' . $w->last_name);
		}

		$out  = $label . " — files shared in this group\r\n";
		$out .= "Bundled on " . date('d M Y, h:i A') . "\r\n";
		$out .= str_repeat('=', 62) . "\r\n\r\n";
		foreach ($rows as $r) {
			$by = isset($names[(int) $r->id]) && $names[(int) $r->id] !== ''
				? $names[(int) $r->id] : 'Unknown';
			$out .= $r->file_name . "\r\n";
			$out .= '    shared by ' . $by . ' on ' . date('d M Y, h:i A', strtotime($r->created_at)) . "\r\n\r\n";
		}
		return $out;
	}

	/**
	 * Put an archived group back.
	 *
	 * Restores the ORIGINALS from the bundle first, and only clears the flags
	 * if that worked — a group whose files cannot come back should stay
	 * archived and say so, rather than reopening with two hundred dead
	 * download links. Files still on disk (unarchived inside the grace period)
	 * are left exactly where they are.
	 */
	public function unarchive_conversation($conv_id, $me)
	{
		$conv_id = (int) $conv_id;
		if (!$this->has_archive_columns()) {
			return array('ok' => FALSE, 'error' => 'Run Database/chat_005_archive.sql first.');
		}

		$conv = $this->get_conversation($conv_id);
		if (!$conv)             return array('ok' => FALSE, 'error' => 'No such conversation.');
		if (!$conv->archived_at) return array('ok' => FALSE, 'error' => 'That group is not archived.');

		$restored = $this->restore_archive_zip($conv_id, $conv);
		if (!$restored['ok']) return $restored;

		$this->db->where('id', $conv_id)->update('chat_conversation', array(
			'archived_at'    => NULL,
			'archived_by'    => 0,
			'archive_reason' => '',
			// The zip is KEPT. It costs one file, it is the only copy of
			// anything that could not be restored, and re-archiving later
			// simply rebuilds it.
		));

		$this->system_message($conv_id, $me, $me['name'] . ' reopened this group.');
		$this->push_event($conv_id, 'channel', $me, 0);
		return array('ok' => TRUE, 'restored' => $restored['restored']);
	}

	/**
	 * Put the loose originals back on disk from the bundle.
	 *
	 * Matches by the name that went IN — unique_zip_name() is deterministic
	 * over the same rows in the same id order, so replaying it reproduces
	 * exactly the names build_archive_zip() used.
	 */
	private function restore_archive_zip($conv_id, $conv)
	{
		$rows = $this->db->select('id, file_name, stored_name, rel_path, archived_at')
			->from('chat_attachment')->where('conversation_id', (int) $conv_id)
			->order_by('id', 'asc')->get()->result();

		if (empty($rows)) return array('ok' => TRUE, 'restored' => 0);

		// Nothing was ever purged — every original is still on disk, so there
		// is nothing to restore and the zip need not even be opened.
		$missing = FALSE;
		foreach ($rows as $r) {
			if ($r->archived_at) { $missing = TRUE; break; }
		}
		if (!$missing) return array('ok' => TRUE, 'restored' => 0);

		$path = chat_upload_path . $conv->archive_zip;
		if ($conv->archive_zip === '' || !is_file($path)) {
			return array('ok' => FALSE,
				'error' => 'The archive bundle for this group is missing, so its files cannot be '
				         . 'restored. The group has been left archived.');
		}

		$zip = new ZipArchive();
		if ($zip->open($path) !== TRUE) {
			return array('ok' => FALSE, 'error' => 'The archive bundle could not be opened.');
		}

		$used = array();
		$n = 0;
		foreach ($rows as $r) {
			$name = $this->unique_zip_name($r->file_name, $used);
			if (!$r->archived_at) continue;                 // still on disk

			$dest_dir = chat_upload_path . $r->rel_path;
			if (!is_dir($dest_dir)) @mkdir($dest_dir, 0755, TRUE);

			$data = $zip->getFromName($name);
			if ($data === FALSE) continue;                  // not in the bundle; leave it marked
			if (@file_put_contents($dest_dir . $r->stored_name, $data) === FALSE) continue;

			$this->db->where('id', (int) $r->id)
				->update('chat_attachment', array('archived_at' => NULL));
			$n++;
		}
		$zip->close();

		return array('ok' => TRUE, 'restored' => $n);
	}

	/**
	 * Delete the loose originals of groups archived longer ago than the grace
	 * period. THIS is the step that actually returns the storage.
	 *
	 * Split from archive_conversation() deliberately. Archiving is automatic
	 * and triggered by a status somewhere else in PMS; deleting files is not
	 * something that should happen in the same instant as an automatic
	 * decision nobody reviewed. CHAT_ARCHIVE_GRACE_DAYS is how long you have
	 * to notice — set it to 0 for "as soon as the zip is verified", or -1 to
	 * never delete anything at all.
	 *
	 * A file is only deleted when its bundle EXISTS and OPENS and CONTAINS it.
	 * Anything that fails any of those three is left on disk — the whole point
	 * is to save storage, not to lose a drawing.
	 *
	 * @return array how many files went, and how many bytes came back
	 */
	public function purge_archived_originals($limit = 5)
	{
		$grace = defined('CHAT_ARCHIVE_GRACE_DAYS') ? (int) CHAT_ARCHIVE_GRACE_DAYS : 30;
		if ($grace < 0 || !$this->has_archive_columns()) {
			return array('groups' => 0, 'files' => 0, 'bytes' => 0);
		}

		$cutoff = date('Y-m-d H:i:s', time() - ($grace * 86400));

		$convs = $this->db->select('id, archive_zip')->from('chat_conversation')
			->where('archived_at IS NOT NULL', NULL, FALSE)
			->where('archived_at <=', $cutoff)
			->where('archive_zip !=', '')
			->limit((int) $limit)->get()->result();

		$files = 0; $bytes = 0; $groups = 0;
		foreach ($convs as $c) {
			$path = chat_upload_path . $c->archive_zip;
			if (!is_file($path)) continue;

			$zip = new ZipArchive();
			if ($zip->open($path) !== TRUE) continue;

			$rows = $this->db->select('id, file_name, stored_name, rel_path')
				->from('chat_attachment')
				->where('conversation_id', (int) $c->id)
				->where('archived_at IS NULL', NULL, FALSE)
				->order_by('id', 'asc')->get()->result();

			// Replay the naming over ALL rows, not just the un-purged ones, or
			// the "(2)" suffixes drift out of step with what is in the bundle.
			$all = $this->db->select('id, file_name')->from('chat_attachment')
				->where('conversation_id', (int) $c->id)->order_by('id', 'asc')->get()->result();
			$used = array(); $name_of = array();
			foreach ($all as $a) $name_of[(int) $a->id] = $this->unique_zip_name($a->file_name, $used);

			$did = 0;
			foreach ($rows as $r) {
				$name = isset($name_of[(int) $r->id]) ? $name_of[(int) $r->id] : '';
				// present in the bundle, or it does not go
				if ($name === '' || $zip->locateName($name) === FALSE) continue;

				$src = chat_upload_path . $r->rel_path . $r->stored_name;
				if (is_file($src)) {
					$size = (int) @filesize($src);
					if (!@unlink($src)) continue;
					$bytes += $size;
				}
				$this->db->where('id', (int) $r->id)->update('chat_attachment',
					array('archived_at' => date('Y-m-d H:i:s')));
				$files++; $did++;
			}
			$zip->close();
			if ($did) $groups++;
		}

		return array('groups' => $groups, 'files' => $files, 'bytes' => $bytes);
	}

	/**
	 * Find dispatched DF groups and archive them. Safe to call often.
	 *
	 * Bounded on purpose: a handful per pass, so the first run after this
	 * ships — which could face a backlog of every DF ever dispatched — spreads
	 * the zipping over many page loads instead of timing out on one.
	 */
	public function archive_dispatched_df_groups($me, $limit = 3)
	{
		$done = array();
		foreach ($this->archivable_df_groups($limit) as $g) {
			$when = ($g->dispatched_on && $g->dispatched_on !== '0000-00-00 00:00:00')
				? date('d M Y', strtotime($g->dispatched_on)) : '';

			$note = 'Thank you all for your efforts — DF ' . $g->df_no . ' has been dispatched'
			      . ($when ? ' on ' . $when : '') . '. This group is now archived. '
			      . 'Every file shared here has been bundled into one download, and the group '
			      . 'can be reopened at any time from the Archived section.';

			$r = $this->archive_conversation((int) $g->id, $me,
				'DF ' . $g->df_no . ' dispatched', $note);
			if ($r['ok']) $done[] = (int) $g->id;
		}
		return $done;
	}
}
