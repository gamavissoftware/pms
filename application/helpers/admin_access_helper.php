<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Administrator (SUPER ADMIN) access checks.
 *
 * Role 12 is the SUPER ADMIN role. Alongside it we keep a small allow-list of
 * user ids that are granted the same privileges WITHOUT carrying the role, so
 * that staff such as IT keep their real role and department everywhere else
 * (user lists, reports, department filters) while still being able to reach the
 * administrator screens.
 */

if ( ! defined('PMS_SUPER_ADMIN_ROLE_ID'))
{
	define('PMS_SUPER_ADMIN_ROLE_ID', 12);
}

if ( ! function_exists('pms_super_admin_user_ids'))
{
	/**
	 * Users granted SUPER ADMIN privileges without holding role 12.
	 *
	 * 82 = Deepesh Yadav, IT (department 30, role 50) - granted 16-09-2026.
	 *
	 * @return array
	 */
	function pms_super_admin_user_ids()
	{
		return array(82);
	}
}

if ( ! function_exists('pms_role_is_super_admin'))
{
	/**
	 * Does this role / user hold SUPER ADMIN privileges?
	 *
	 * Use this where the ids are already to hand and there may be no session,
	 * such as the mobile API. Prefer pms_is_super_admin() inside the web app.
	 *
	 * @param  mixed $role_id user_role.user_role_id
	 * @param  mixed $user_id system_users.user_id
	 * @return bool
	 */
	function pms_role_is_super_admin($role_id, $user_id = NULL)
	{
		if ((int) $role_id === PMS_SUPER_ADMIN_ROLE_ID)
		{
			return TRUE;
		}

		return in_array((int) $user_id, pms_super_admin_user_ids(), TRUE);
	}
}

if ( ! function_exists('pms_is_super_admin'))
{
	/**
	 * Does the logged-in user hold SUPER ADMIN privileges?
	 *
	 * @return bool
	 */
	function pms_is_super_admin()
	{
		$session = NULL;

		// This codebase reads the login session both as $this->session->userdata(...)
		// and as $_SESSION['logged_in']; resolve whichever is populated so the
		// helper behaves the same wherever it is called from.
		$CI = &get_instance();
		if (isset($CI->session))
		{
			$session = $CI->session->userdata('logged_in');
		}

		if ( ! is_array($session) && isset($_SESSION['logged_in']))
		{
			$session = $_SESSION['logged_in'];
		}

		// Fail closed: no readable session means no administrator privileges.
		if ( ! is_array($session))
		{
			return FALSE;
		}

		return pms_role_is_super_admin(
			isset($session['role']) ? $session['role'] : 0,
			isset($session['user_id']) ? $session['user_id'] : 0
		);
	}
}
