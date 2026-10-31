<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog\migrations\v10x;

/**
 * Class m6_permissions
 *
 * @package mrgoldy\ultimateblog\migrations\v10x
 */
class m6_permissions extends \phpbb\db\migration\container_aware_migration
{
	/**
	* @return void
	* @access public
	*/
	static public function depends_on()
	{
		return ['\mrgoldy\ultimateblog\migrations\v10x\m5_sample_data'];
	}

	/**
	* @return void
	* @access public
	*/
	public function update_data()
	{
		$data = [
			# Add permissions
			['permission.add', ['u_ub_view']],
			['permission.add', ['u_ub_post']],
			['permission.add', ['u_ub_post_private']],
			['permission.add', ['u_ub_edit']],
			['permission.add', ['u_ub_edit_view']],
			['permission.add', ['u_ub_delete']],
			['permission.add', ['u_ub_noapprove']],
			['permission.add', ['u_ub_comment_delete']],
			['permission.add', ['u_ub_comment_edit']],
			['permission.add', ['u_ub_comment_noapprove']],
			['permission.add', ['u_ub_comment_post']],
			['permission.add', ['u_ub_comment_view']],
			['permission.add', ['u_ub_rate']],
			['permission.add', ['u_ub_report']],
			['permission.add', ['u_ub_feed_view']],

			['permission.add', ['m_ub_edit']],
			['permission.add', ['m_ub_delete']],
			['permission.add', ['m_ub_approve']],
			['permission.add', ['m_ub_changeauthor']],
			['permission.add', ['m_ub_edit_lock']],
			['permission.add', ['m_ub_edit_delete']],
			['permission.add', ['m_ub_view_friends_only']],
			['permission.add', ['m_ub_lock_rating']],
			['permission.add', ['m_ub_lock_comments']],
			['permission.add', ['m_ub_report']],

			['permission.add', ['a_ub_overview']],
			['permission.add', ['a_ub_settings']],
			['permission.add', ['a_ub_categories']],

			# Add view permission for the Guests group
			['permission.permission_set', ['GUESTS', 'u_ub_view', 'group']],
		];

		# Assign permissions to roles
		if ($this->role_exists('ROLE_USER_STANDARD'))
		{
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_view']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_post']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_edit']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_edit_view']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_delete']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_noapprove']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_comment_delete']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_comment_edit']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_comment_noapprove']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_comment_post']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_comment_view']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_rate']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_report']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ub_feed_view']];
		}

		if ($this->role_exists('ROLE_USER_FULL'))
		{
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_view']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_post']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_edit']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_edit_view']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_delete']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_noapprove']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_comment_delete']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_comment_edit']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_comment_noapprove']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_comment_post']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_comment_view']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_rate']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_report']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ub_feed_view']];
		}

		if ($this->role_exists('ROLE_MOD_STANDARD'))
		{
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_edit']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_delete']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_approve']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_changeauthor']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_edit_lock']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_edit_delete']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_view_friends_only']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_lock_rating']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_lock_comments']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_STANDARD', 'm_ub_report']];
		}

		if ($this->role_exists('ROLE_MOD_FULL'))
		{
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_edit']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_delete']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_approve']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_changeauthor']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_edit_lock']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_edit_delete']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_view_friends_only']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_lock_rating']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_lock_comments']];
			$data[] = ['permission.permission_set', ['ROLE_MOD_FULL', 'm_ub_report']];
		}

		if ($this->role_exists('ROLE_ADMIN_STANDARD'))
		{
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ub_overview']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ub_settings']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ub_categories']];
		}

		if ($this->role_exists('ROLE_ADMIN_FULL'))
		{
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ub_overview']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ub_settings']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ub_categories']];
		}

		return $data;
	}

	/**
	 * # Check if permission role exists
	 *
	 * @param $role
	 * @return $role_id
	 * @access private
	 */
	private function role_exists($role)
	{
		$sql = 'SELECT role_id
				FROM ' . ACL_ROLES_TABLE . "
				WHERE role_name = '" . $this->db->sql_escape($role) . "'";
		$result = $this->db->sql_query_limit($sql, 1);
		$role_id = $this->db->sql_fetchfield('role_id');
		$this->db->sql_freeresult($result);

		return $role_id;
	}
}
