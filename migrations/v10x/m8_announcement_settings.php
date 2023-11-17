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
class m8_announcement_settings extends \phpbb\db\migration\container_aware_migration
{
	/**
	 * @return void
	 * @access public
	 */
	static public function depends_on()
	{
		return ['\mrgoldy\ultimateblog\migrations\v10x\m2_initial_data'];
	}

	/**
	 * @return void
	 * @access public
	 */
	public function update_data()
	{
		return [
			['config.add', ['ub_announcement_bbcode', 1]],
			['config.add', ['ub_announcement_smilies', 1]],
			['config.add', ['ub_announcement_urls', 1]],
			['config.add', ['ub_announcement_html', 1]],
		];
	}
}
