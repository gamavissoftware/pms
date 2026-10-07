<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CHAT access / identity helper  (PMS)
 * -----------------------------------------------------------------------------
 * Ported from the CoreTech CRM chat module. Two jobs:
 *
 *  1. IDENTITY. Resolve the `logged_in` session into a normalised identity.
 *
 *     PMS has exactly ONE kind of account — system_users.user_id — so this is
 *     far simpler than the CoreTech original, which had to keep CRM users,
 *     engineers (tech_information) and vendors (vendor_registration) apart
 *     because their ids collide across three tables.
 *
 *     Every chat row still stores (user_id, user_type) as a composite identity
 *     and every identity here reports type 'user'. That costs nothing today and
 *     means an outside-party portal can be added later without a schema change
 *     or a rewrite of the membership logic.
 *
 *  2. PERMISSIONS. Bridges to the EXISTING framework
 *     (system_modules / submodule / module_access / module_capablity), the same
 *     one the nav menu and the Automation BOM module use. Nothing new invented.
 *
 * -----------------------------------------------------------------------------
 * THE ONE THING THAT DIFFERS FROM CORETECH, AND IT MATTERS
 * -----------------------------------------------------------------------------
 * In PMS, `module_access`.`role_id` and `module_capablity`.`role_id` hold a
 * USER id, not a role id, despite the column name.
 *
 * This was verified against production data by the Automation BOM work (see
 * application/libraries/Abom_permission_guard.php): 131 of the 132 distinct
 * values match system_users.user_id, only 40 match user_role.user_role_id, and
 * the values run to 238 while user_role tops out at 104.
 * application/views/common/nav-menu.php does the same thing in roughly forty
 * places — `->where('role_id', $user_id)` — so this is the application's own
 * convention, not merely an accident of the data.
 *
 * The CoreTech original passed the session ROLE here, which is correct there
 * and would be silently wrong here: every check would consult another user's
 * grants and return a confident, wrong answer. chat_perm_key() below is the
 * single place that decision lives.
 * -----------------------------------------------------------------------------
 */

if (!function_exists('chat_submodule_map')) {
	/**
	 * capability key => submodule label(s) in the system permission screen.
	 *
	 * A capability accepts SEVERAL labels so a renamed row keeps working: the
	 * preferred name first, older names after. Chat_002_permissions.sql inserts
	 * the first label of each.
	 */
	function chat_submodule_map()
	{
		return array(
			'messenger'      => array('Chat - Messenger'),
			'create_channel' => array('Chat - Create Group', 'Chat - Create Channel'),
			'manage_members' => array('Chat - Manage Members'),
			'pin'            => array('Chat - Pin Message'),
			'file_share'     => array('Chat - File Sharing'),
			// Linking a conversation to a DF, a task or a lead. Named
			// 'lead_tag' because that is the key the controller, model and view
			// already use in ~20 places; the LABEL is the general one, since in
			// PMS it governs all three record types.
			'lead_tag'       => array('Chat - Link Records', 'Chat - Lead Tagging'),
			// Starting things. Gated on the messenger grant itself — there is
			// no separate row for these, exactly as in the original.
			'start_dm'       => array('Chat - Messenger'),
			'start_call'     => array('Chat - Messenger'),
			// 'tech_chat' is deliberately absent — see chat_can().
		);
	}
}

if (!function_exists('chat_identity')) {
	/**
	 * Who is talking?
	 *
	 * @return array|NULL  array(id, type, name, role, avatar, initials), or
	 *                     NULL when nobody is logged in.
	 */
	function chat_identity($CI)
	{
		static $me = FALSE;
		if ($me !== FALSE) return $me;

		$s = $CI->session->userdata('logged_in');
		if (!is_array($s) || empty($s['user_id'])) return ($me = NULL);

		// PMS stores the FIRST name in `user_name` and the surname separately.
		$last = isset($s['last_name']) ? $s['last_name'] : '';
		$name = chat_name_case(
			trim((isset($s['user_name']) ? $s['user_name'] : '') . ' ' . $last)
		);
		if ($name === '') $name = 'User';

		$me = array(
			'id'     => (int) $s['user_id'],
			'type'   => 'user',
			'name'   => $name,
			// cased like every other role in the module — this one is the
			// signed-in user's own, and it is drawn next to theirs
			'role'   => chat_role_case(chat_role_name($CI, isset($s['role']) ? $s['role'] : 0)),
			'avatar' => isset($s['profile_image']) ? $s['profile_image'] : '',
		);

		$me['initials'] = chat_initials($me['name']);
		return $me;
	}
}

if (!function_exists('chat_name_case')) {
	/**
	 * "AAKASH SHARMA" -> "Aakash Sharma", for DISPLAY only.
	 *
	 * `system_users` has been filled in by many hands over many years, so the
	 * same list shows "AAKASH SHARMA" next to "Abhay Pathak". Chat puts those
	 * names side by side more than anything else in PMS — the people picker,
	 * the member list, every message header — so the inconsistency is very
	 * visible here.
	 *
	 * NOTHING IS WRITTEN BACK. This is applied where a name is read for
	 * display; the stored value is left exactly as it is, so no other module
	 * changes and nobody's record is edited as a side effect of opening chat.
	 *
	 * A word is only re-cased when it is entirely upper or entirely lower
	 * case. A word that is ALREADY mixed case was typed deliberately and is
	 * left alone — that is what keeps "McDonald" and "DeSouza" intact instead
	 * of flattening them to "Mcdonald" and "Desouza".
	 *
	 * Within a word, the letter after a hyphen, apostrophe or dot is
	 * capitalised too, so "D'SOUZA" becomes "D'Souza" and "R.K. SHARMA"
	 * becomes "R.K. Sharma" rather than "R.k. Sharma".
	 *
	 * KNOWN LIMIT: a lowercase nobiliary particle IS capitalised — "van der
	 * Berg" becomes "Van Der Berg" — because an all-lowercase word is exactly
	 * what needs fixing in the common case ("abhishek kumar"). The two cannot
	 * be told apart without a particle list, and on this staff roster the
	 * lowercase-surname case does not arise while the shouting one is
	 * everywhere. Add a particle check here if that ever changes.
	 */
	function chat_name_case($name)
	{
		$name = trim(preg_replace('/\s+/u', ' ', (string) $name));
		if ($name === '') return '';

		$words = explode(' ', $name);
		foreach ($words as $i => $w) {
			if ($w === '') continue;

			$has_lower = (bool) preg_match('/\p{Ll}/u', $w);
			$has_upper = (bool) preg_match('/\p{Lu}/u', $w);

			// mixed case -> somebody meant it; leave it exactly as typed
			if ($has_lower && $has_upper) continue;

			$w = function_exists('mb_strtolower') ? mb_strtolower($w, 'UTF-8') : strtolower($w);
			$w = preg_replace_callback(
				'/(^|[\s\-\'’.])(\p{L})/u',
				function ($m) {
					return $m[1] . (function_exists('mb_strtoupper')
						? mb_strtoupper($m[2], 'UTF-8') : strtoupper($m[2]));
				},
				$w
			);
			$words[$i] = $w;
		}
		return implode(' ', $words);
	}
}

if (!function_exists('chat_initials')) {
	/** "Aditya Raj" -> "AR"; used for the fallback avatar. */
	function chat_initials($name)
	{
		$parts = preg_split('/\s+/', trim((string) $name));
		$out = '';
		foreach ($parts as $p) {
			if ($p === '') continue;
			$out .= strtoupper(substr($p, 0, 1));
			if (strlen($out) >= 2) break;
		}
		return $out !== '' ? $out : '?';
	}
}

if (!function_exists('chat_role_name')) {
	/**
	 * user_role_id -> readable role, cached per request.
	 *
	 * This one genuinely IS keyed by the role id (session `role`), because it
	 * reads user_role — a different table from the permission ones above.
	 */
	function chat_role_name($CI, $role_id)
	{
		static $cache = array();
		$role_id = (int) $role_id;
		if ($role_id <= 0) return '';
		if (isset($cache[$role_id])) return $cache[$role_id];

		$row = $CI->db->select('user_role')->from('user_role')
			->where('user_role_id', $role_id)->get()->row();
		return ($cache[$role_id] = $row ? $row->user_role : '');
	}
}

if (!function_exists('chat_perm_key')) {
	/**
	 * The value `module_access.role_id` / `module_capablity.role_id` is keyed
	 * by. In PMS that is the USER id — see the block comment at the top of this
	 * file for the evidence.
	 *
	 * Kept as its own function so there is exactly ONE place to change if the
	 * schema is ever normalised to a real role id.
	 */
	function chat_perm_key($CI)
	{
		$s = $CI->session->userdata('logged_in');
		if (is_array($s) && !empty($s['user_id'])) return (int) $s['user_id'];
		return 0;
	}
}

if (!function_exists('chat_role_id')) {
	/**
	 * BACK-COMPAT SHIM. The CoreTech module called this to get the permission
	 * key. Keeping the name means the ported controller and model need no edit,
	 * but the VALUE is now the user id — see chat_perm_key().
	 */
	function chat_role_id($CI)
	{
		return chat_perm_key($CI);
	}
}

if (!function_exists('chat_module_row')) {
	/** cached lookup of the CHAT module row */
	function chat_module_row($CI)
	{
		static $row = FALSE;
		if ($row === FALSE) {
			if (!$CI->db->table_exists('system_modules')) return ($row = NULL);
			$row = $CI->db->select('id')->from('system_modules')
				->where('modulename', 'CHAT')->get()->row();
		}
		return $row;
	}
}

if (!function_exists('chat_granted_submodules')) {
	/**
	 * map of granted submodule-label => bool, for one permission key.
	 *
	 * Returns an EMPTY array both when the module is not granted at all and
	 * when there is no row — chat_can() treats that as "not configured" and
	 * falls back to chat_default_caps(), so chat works on day one.
	 */
	function chat_granted_submodules($CI, $perm_key)
	{
		static $cache = array();
		$perm_key = (int) $perm_key;
		if (isset($cache[$perm_key])) return $cache[$perm_key];

		$mod = chat_module_row($CI);
		if (!$mod) return ($cache[$perm_key] = array());

		if (!$CI->db->table_exists('module_access') || !$CI->db->table_exists('module_capablity')) {
			return ($cache[$perm_key] = array());
		}

		$acc = $CI->db->select('access')->from('module_access')
			->where('role_id', $perm_key)->where('moduleid', $mod->id)->get()->row();
		if (!$acc || $acc->access != 1) return ($cache[$perm_key] = array());

		$rows = $CI->db->select('s.submodule, c.submodule_access', FALSE)
			->from('submodule s')
			->join('module_capablity c',
				'c.submoduleid = s.id AND c.role_id = ' . $perm_key .
				' AND c.moduleid = ' . (int) $mod->id, 'left')
			->where('s.moduleid', $mod->id)->where('s.status', 1)->get()->result();

		$out = array();
		foreach ($rows as $r) $out[$r->submodule] = ($r->submodule_access == 1);
		return ($cache[$perm_key] = $out);
	}
}

if (!function_exists('chat_is_admin')) {
	/**
	 * Full access, no permission rows needed.
	 *
	 * PMS marks admin roles with `user_role`.`isadmin` = 1 (the same test
	 * Dashboard_model uses), so this is a real role flag rather than the magic
	 * "user_id 2" backdoor the CoreTech original carried. There is nothing to
	 * remember to close at go-live as a result.
	 */
	function chat_is_admin($CI)
	{
		static $is = NULL;
		if ($is !== NULL) return $is;

		$me = chat_identity($CI);
		if (!$me) return ($is = FALSE);

		$s       = $CI->session->userdata('logged_in');
		$role_id = (is_array($s) && isset($s['role'])) ? (int) $s['role'] : 0;
		if ($role_id <= 0) return ($is = FALSE);

		$row = $CI->db->select('isadmin')->from('user_role')
			->where('user_role_id', $role_id)->get()->row();
		return ($is = ($row && (int) $row->isadmin === 1));
	}
}

if (!function_exists('chat_marketing_department_ids')) {
	/**
	 * The department ids that ARE marketing.
	 *
	 * Resolved by NAME, not hardcoded, because PMS has **two** active
	 * departments called MARKETING (ids 6 and 9 in production). Pinning either
	 * number would hide the feature from half the marketing team, and which
	 * half would depend on data nobody thinks to look at. Task::dfrelease()
	 * hardcodes 9 for its auto-assign; that is its business, not ours.
	 *
	 * @return array of int
	 */
	function chat_marketing_department_ids($CI)
	{
		static $ids = NULL;
		if ($ids !== NULL) return $ids;

		$ids = array();
		if (!$CI->db->table_exists('departments')) return $ids;

		$rows = $CI->db->select('department_id')->from('departments')
			->like('department', 'MARKETING')->where('status', 1)->get()->result();
		foreach ($rows as $r) $ids[] = (int) $r->department_id;
		return $ids;
	}
}

if (!function_exists('chat_can_manage_df_groups')) {
	/**
	 * Who may see and use the DF group tools?
	 *
	 * Marketing (they own the DF release) and administrators. Everybody else
	 * still takes part in a DF group once they are added to it — this governs
	 * CREATING and BACKFILLING them, not membership.
	 *
	 * "Administrator" is `user_role.isadmin = 1`, which in production is
	 * SUPER ADMIN and MANAGEMENT. That is the same test chat_is_admin() uses.
	 *
	 * NOT `$_SESSION['logged_in']['adminuser']`, which several PMS screens read
	 * — including Task::dfreleasedashboard() — but which nothing ever writes.
	 * It is always undefined, so every one of those admin branches silently
	 * fails closed. Copying that pattern would make this gate look right and
	 * behave wrong.
	 */
	function chat_can_manage_df_groups($CI)
	{
		$me = chat_identity($CI);
		if (!$me) return FALSE;

		// creating a DF group is still creating a group
		if (!chat_can($CI, 'create_channel')) return FALSE;

		if (chat_is_admin($CI)) return TRUE;

		$s    = $CI->session->userdata('logged_in');
		$dept = (is_array($s) && isset($s['department_id'])) ? (int) $s['department_id'] : 0;
		if ($dept <= 0) return FALSE;

		return in_array($dept, chat_marketing_department_ids($CI), TRUE);
	}
}

if (!function_exists('chat_df_group_scope')) {
	/**
	 * Which DFs may this person act on?
	 *
	 *   'all'  administrators — every DF
	 *   'own'  marketing      — only DFs they released (df_release.added_by)
	 *   ''     nobody else gets here
	 */
	function chat_df_group_scope($CI)
	{
		if (!chat_can_manage_df_groups($CI)) return '';
		return chat_is_admin($CI) ? 'all' : 'own';
	}
}

if (!function_exists('chat_can')) {
	/**
	 * Capability check against the role framework.
	 *
	 * @param string $capability  key from chat_submodule_map()
	 */
	function chat_can($CI, $capability)
	{
		$me = chat_identity($CI);
		if (!$me) return FALSE;

		// 'tech_chat' gated conversations with engineers and vendors in the
		// CoreTech CRM. PMS has no such accounts, so every one of its ~7 call
		// sites guards a peer type that cannot occur. Answering TRUE keeps them
		// all no-ops rather than editing working code to delete a branch that
		// never runs — and leaves the gate in place if a portal is ever added.
		if ($capability === 'tech_chat') return TRUE;

		if (chat_is_admin($CI)) return TRUE;

		$mod = chat_module_row($CI);
		if (!$mod) return TRUE;                       // module not registered yet -> fail-open

		$map = chat_submodule_map();
		if (!isset($map[$capability])) return TRUE;   // unknown capability -> allow

		$granted = chat_granted_submodules($CI, chat_perm_key($CI));

		// A user who has NOT been given the CHAT module at all still gets the
		// everyday essentials, so chat is usable company-wide from day one
		// without anyone configuring permissions first. Only the capability
		// that exposes other modules' records (lead_tag) stays closed until
		// explicitly granted.
		if (empty($granted)) return in_array($capability, chat_default_caps(), TRUE);

		foreach ($map[$capability] as $label) {
			if (!empty($granted[$label])) return TRUE;
		}
		return FALSE;
	}
}

if (!function_exists('chat_default_caps')) {
	/**
	 * What every logged-in user can do before any CHAT permission is assigned.
	 *
	 * Deliberately includes group creation and member management: without them
	 * only an admin could make a group, and chat would look broken until
	 * someone configured permissions. Grant the CHAT module to a user to take
	 * fine-grained control back.
	 *
	 * 'lead_tag' is deliberately absent — it reaches into DF, task and lead
	 * records, so it is opt-in.
	 */
	function chat_default_caps()
	{
		return array(
			'messenger', 'file_share', 'create_channel', 'manage_members', 'pin',
			'start_dm', 'start_call', 'tech_chat',
		);
	}
}

if (!function_exists('chat_is_external')) {
	/**
	 * TRUE for an account that sits outside the company.
	 *
	 * ALWAYS FALSE in PMS: there is one account table and everybody in it is
	 * staff. The function is kept because the ported controller tests it in
	 * seven places to enforce the reply-only rules for engineers and vendors;
	 * with no such accounts those branches are simply never taken.
	 *
	 * Keeping the seam rather than deleting the call sites means adding a
	 * customer or vendor portal later is a change to THIS function, not an
	 * archaeology exercise across the whole module.
	 */
	function chat_is_external($CI)
	{
		$me = chat_identity($CI);
		return $me && $me['type'] !== 'user';
	}
}

if (!function_exists('chat_external_caps')) {
	/** Unreachable in PMS while chat_is_external() is always FALSE. */
	function chat_external_caps()
	{
		return array('messenger', 'file_share', 'pin');
	}
}

if (!function_exists('chat_ref_types')) {
	/**
	 * The PMS records a conversation can be linked to.
	 *
	 * ONE definition shared by the controller (validation), the model (the
	 * lookup and the label on the chip) and the view (the icon), so adding a
	 * fourth record type is one entry here.
	 *
	 *   label  - shown to users
	 *   table  - where it lives
	 *   pk     - its primary key column
	 *   url    - page_url-relative detail page, '%d' replaced by the id
	 */
	function chat_ref_types()
	{
		return array(
			'df' => array(
				'label' => 'DF',
				'table' => 'df_design_form_table',
				'pk'    => 'id',
				'url'   => 'Gantt_chart/index/%d',
			),
			'task' => array(
				'label' => 'Task',
				'table' => 'task_department_wise_scheduling',
				'pk'    => 'id',
				'url'   => 'Task_management/index/%d',
			),
			'lead' => array(
				'label' => 'Lead',
				'table' => 'leads',
				'pk'    => 'id',
				'url'   => 'Leads/view/%d',
			),
		);
	}
}

if (!function_exists('chat_clean_ref_type')) {
	/** anything unrecognised collapses to '' (= not a link) */
	function chat_clean_ref_type($ref_type)
	{
		$ref_type = strtolower(trim((string) $ref_type));
		return isset(chat_ref_types()[$ref_type]) ? $ref_type : '';
	}
}

if (!function_exists('chat_providers')) {
	/**
	 * The meeting providers a call can use.
	 *
	 * One definition shared by the controller (validation + start URL) and the
	 * model (the label on the join card), so adding a provider is one entry
	 * here rather than a hunt through both.
	 *
	 *   label  - shown to users
	 *   start  - where to send someone who has no personal room saved
	 *   hosts  - the ONLY hosts a join link may live on (plus their subdomains)
	 */
	function chat_providers()
	{
		return array(
			'zoom' => array(
				'label' => 'Zoom',
				'start' => 'https://zoom.us/start/videomeeting',
				'hosts' => array('zoom.us', 'zoomgov.com'),
			),
			'meet' => array(
				'label' => 'Google Meet',
				'start' => 'https://meet.google.com/new',
				'hosts' => array('meet.google.com'),
			),
			'teams' => array(
				'label' => 'Microsoft Teams',
				// opens the Teams "New meeting" form, where Meet now / Copy link live
				'start' => 'https://teams.microsoft.com/l/meeting/new',
				// teams.microsoft.com = work/school, teams.live.com = personal,
				// teams.microsoft.us = US government tenants
				'hosts' => array('teams.microsoft.com', 'teams.live.com', 'teams.microsoft.us'),
			),
		);
	}
}

if (!function_exists('chat_provider_label')) {
	function chat_provider_label($provider)
	{
		$p = chat_providers();
		return isset($p[$provider]) ? $p[$provider]['label'] : 'Zoom';
	}
}

if (!function_exists('chat_clean_provider')) {
	/** anything unrecognised collapses to zoom */
	function chat_clean_provider($provider)
	{
		$provider = strtolower(trim((string) $provider));
		return isset(chat_providers()[$provider]) ? $provider : 'zoom';
	}
}

if (!function_exists('chat_nav_visible')) {
	/**
	 * Should Chat appear in the UI at all?
	 *
	 * Governs every entry point a user can see — the sidebar link, the topbar
	 * icon and badge, the notification poll and the floating dock. Purely
	 * cosmetic: the controller itself stays live, so /Chat is reachable by URL
	 * for piloting before launch.
	 *
	 * Flip CHAT_MODULE_VISIBLE to TRUE in config/constants.php to launch.
	 * Defaults to hidden when the constant is missing, so an out-of-date
	 * constants.php can never reveal it by accident.
	 */
	function chat_nav_visible($CI)
	{
		if (!defined('CHAT_MODULE_VISIBLE') || !CHAT_MODULE_VISIBLE) return FALSE;
		return chat_can($CI, 'messenger');
	}
}

if (!function_exists('chat_avatar_url')) {
	/**
	 * Public URL for a participant photo, or '' when there is none (the UI then
	 * paints an initials bubble).
	 *
	 * PMS keeps profile images under image_bank/users/ — the `user_profile`
	 * constant. The $type argument is vestigial (everyone is a 'user' here) but
	 * is kept so the call sites in the model need no edit.
	 */
	function chat_avatar_url($file, $type = 'user')
	{
		$file = trim((string) $file);
		if ($file === '') return '';
		if (preg_match('#^https?://#i', $file)) return $file;
		return user_profile . $file;
	}
}

/* ---------------------------------------------------------------------------
 |  WHICH PLANT SOMEBODY WORKS AT
 |
 |  system_users.plant_unit: 1 = Sector - 59, 2 = Sector - 06.
 |
 |  It sits beside the role everywhere a person is named in chat, because in a
 |  two-plant company "Manager" is only half an answer — the question behind
 |  reading a role in a chat window is usually "is this the person I should be
 |  asking", and which site they are at is most of that.
 |
 |  Defined HERE, in one place, so the messenger, the dock, the nav widget and
 |  the mobile API cannot disagree about what a 1 means. Anything that is not
 |  1 or 2 — including 0, NULL and a column that does not exist yet — comes
 |  back as an empty string and simply shows nothing, rather than inventing a
 |  plant for somebody.
 |------------------------------------------------------------------------- */
if (!function_exists('chat_plant_units')) {
	function chat_plant_units()
	{
		return array(
			1 => 'Sector - 59',
			2 => 'Sector - 06',
		);
	}
}

if (!function_exists('chat_plant_unit')) {
	function chat_plant_unit($value)
	{
		$map = chat_plant_units();
		$v   = (int) $value;
		return isset($map[$v]) ? $map[$v] : '';
	}
}

/* ---------------------------------------------------------------------------
 |  ROLES, IN SENTENCE CASE
 |
 |  user_role.user_role is typed by hand and comes out of the table however
 |  somebody happened to enter it — "MANAGEMENT" shouting beside "Asstt.
 |  Manager". Next to a name in a chat window the shouting reads as emphasis
 |  nobody meant.
 |
 |  Sentence case: first letter up, the rest down. "MANAGEMENT" -> "Management",
 |  "ENGINEER" -> "Engineer".
 |
 |  A role that is ALREADY mixed case is left exactly as typed — "Asstt.
 |  Manager" stays "Asstt. Manager" rather than being flattened to "Asstt.
 |  manager". Somebody capitalised that deliberately and it is correct; the
 |  only thing worth fixing is the shouting. Same rule chat_name_case() uses on
 |  people's names, for the same reason.
 |------------------------------------------------------------------------- */
if (!function_exists('chat_role_case')) {
	function chat_role_case($role)
	{
		$role = trim(preg_replace('/\s+/u', ' ', (string) $role));
		if ($role === '') return '';

		$has_lower = (bool) preg_match('/\p{Ll}/u', $role);
		$has_upper = (bool) preg_match('/\p{Lu}/u', $role);
		if ($has_lower && $has_upper) return $role;   // deliberate; leave it

		$lower = function_exists('mb_strtolower') ? mb_strtolower($role, 'UTF-8') : strtolower($role);

		// first LETTER, not first character — "(hod) manager" should still
		// come back with a capital H
		return preg_replace_callback('/\p{L}/u', function ($m) {
			return function_exists('mb_strtoupper')
				? mb_strtoupper($m[0], 'UTF-8') : strtoupper($m[0]);
		}, $lower, 1);
	}
}
