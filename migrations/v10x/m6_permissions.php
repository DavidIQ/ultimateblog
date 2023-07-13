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
		return array('\mrgoldy\ultimateblog\migrations\v10x\m5_sample_data');
	}

	/**
	* @return void
	* @access public
	*/
	public function update_data()
	{
		$data = array(
			# Add permissions
			array('permission.add', array('u_ub_view')),
			array('permission.add', array('u_ub_post')),
			array('permission.add', array('u_ub_post_private')),
			array('permission.add', array('u_ub_edit')),
			array('permission.add', array('u_ub_edit_view')),
			array('permission.add', array('u_ub_delete')),
			array('permission.add', array('u_ub_noapprove')),
			array('permission.add', array('u_ub_comment_delete')),
			array('permission.add', array('u_ub_comment_edit')),
			array('permission.add', array('u_ub_comment_noapprove')),
			array('permission.add', array('u_ub_comment_post')),
			array('permission.add', array('u_ub_comment_view')),
			array('permission.add', array('u_ub_rate')),
			array('permission.add', array('u_ub_report')),
			array('permission.add', array('u_ub_feed_view')),

			array('permission.add', array('m_ub_edit')),
			array('permission.add', array('m_ub_delete')),
			array('permission.add', array('m_ub_approve')),
			array('permission.add', array('m_ub_changeauthor')),
			array('permission.add', array('m_ub_edit_lock')),
			array('permission.add', array('m_ub_edit_delete')),
			array('permission.add', array('m_ub_view_friends_only')),
			array('permission.add', array('m_ub_lock_rating')),
			array('permission.add', array('m_ub_lock_comments')),
			array('permission.add', array('m_ub_report')),

			array('permission.add', array('a_ub_overview')),
			array('permission.add', array('a_ub_settings')),
			array('permission.add', array('a_ub_categories')),

			# Add view permission for the Guests group
			array('permission.permission_set', array('GUESTS', 'u_ub_view', 'group')),
		);

		# Assign permissions to roles
		if ($this->role_exists('ROLE_USER_STANDARD'))
		{
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_view'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_post'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_edit'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_edit_view'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_delete'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_noapprove'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_comment_delete'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_comment_edit'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_comment_noapprove'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_comment_post'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_comment_view'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_rate'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_report'));
			$data[] = array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_ub_feed_view'));
		}

		if ($this->role_exists('ROLE_USER_FULL'))
		{
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_view'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_post'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_edit'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_edit_view'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_delete'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_noapprove'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_comment_delete'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_comment_edit'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_comment_noapprove'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_comment_post'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_comment_view'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_rate'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_report'));
			$data[] = array('permission.permission_set', array('ROLE_USER_FULL', 'u_ub_feed_view'));
		}

		if ($this->role_exists('ROLE_MOD_STANDARD'))
		{
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_edit'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_delete'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_approve'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_changeauthor'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_edit_lock'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_edit_delete'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_view_friends_only'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_lock_rating'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_lock_comments'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_ub_report'));
		}

		if ($this->role_exists('ROLE_MOD_FULL'))
		{
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_edit'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_delete'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_approve'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_changeauthor'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_edit_lock'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_edit_delete'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_view_friends_only'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_lock_rating'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_lock_comments'));
			$data[] = array('permission.permission_set', array('ROLE_MOD_FULL', 'm_ub_report'));
		}

		if ($this->role_exists('ROLE_ADMIN_STANDARD'))
		{
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_ub_overview'));
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_ub_settings'));
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_ub_categories'));
		}

		if ($this->role_exists('ROLE_ADMIN_FULL'))
		{
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_ub_overview'));
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_ub_settings'));
			$data[] = array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_ub_categories'));
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
