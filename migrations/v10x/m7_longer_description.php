<?php

/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2023, DavidIQ
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog\migrations\v10x;

/**
 * Class m6_permissions
 *
 * @package mrgoldy\ultimateblog\migrations\v10x
 */
class m7_longer_description extends \phpbb\db\migration\container_aware_migration
{
	/**
	 * @return void
	 * @access public
	 */
	static public function depends_on()
	{
		return ['\mrgoldy\ultimateblog\migrations\v10x\m1_initial_schema'];
	}

	/**
	 * @return void
	 * @access public
	 */
	public function update_schema()
	{
		return [
			'change_columns'		=> [
				$this->table_prefix . 'ub_blogs'	=> [
					'blog_description'		=> ['VCHAR:325', ''],
				],
			],
		];
	}

	/**
	 * @return void
	 * @access public
	 */
	public function revert_schema()
	{
		return [
			'change_columns'		=> [
				$this->table_prefix . 'ub_blogs'	=> [
					'blog_description'		=> ['VCHAR:125', ''],
				],
			],
		];
	}
}
