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
 * Class m3_acp_module
 *
 * @package mrgoldy\ultimateblog\migrations\v10x
 */
class m3_acp_module extends \phpbb\db\migration\container_aware_migration
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
		$data = [
			# Add ACP module
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_ULTIMATEBLOG'
			]],
			['module.add', [
				'acp',
				'ACP_ULTIMATEBLOG',
				[
					'module_basename'	=> '\mrgoldy\ultimateblog\acp\main_module',
					'modes'				=> ['overview', 'settings', 'categories'],
				],
			]],
		];

		return $data;
	}
}
