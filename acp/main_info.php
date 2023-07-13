<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog\acp;

/**
 * Ultimate Blog ACP module info.
 */
class main_info
{
	/**
	 * @return array
	 */
	public function module()
	{
		return array(
			'filename'	=> '\mrgoldy\ultimateblog\acp\main_module',
			'title'		=> 'ACP_ULTIMATEBLOG',
			'modes'		=> array(
				'overview'		=> array(
					'title' => 'ACP_UB_OVERVIEW',
					'auth'	=> 'ext_mrgoldy/ultimateblog && acl_a_ub_overview',
					'cat'	=> array('ACP_ULTIMATEBLOG'),
				),
				'settings'		=> array(
					'title'	=> 'ACP_UB_SETTINGS',
					'auth'	=> 'ext_mrgoldy/ultimateblog && acl_a_ub_settings',
					'cat'	=> array('ACP_ULTIMATEBLOG'),
				),
				'categories'	=> array(
					'title'	=> 'ACP_UB_CATEGORIES',
					'auth'	=> 'ext_mrgoldy/ultimateblog && acl_a_ub_categories',
					'cat'	=> array('ACP_ULTIMATEBLOG'),
				),
			),
		);
	}
}
