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
 * Class m4_mcp_module
 *
 * @package mrgoldy\ultimateblog\migrations
 */
class m4_mcp_module extends \phpbb\db\migration\migration
{
	/**
	 * @return array
	 */
	static public function depends_on()
	{
		return ['\mrgoldy\ultimateblog\migrations\v10x\m3_acp_module'];
	}

	/**
	 * @return array
	 */
	public function update_data()
	{
		return [
			['module.add', [
				'mcp',
				'MCP_REPORTS',
				[
					'module_basename'	=> '\mrgoldy\ultimateblog\mcp\report_module',
					'modes'				=> ['ub_blog_reports_open', 'ub_blog_reports_closed', 'ub_blog_reports_details', 'ub_comment_reports_open', 'ub_comment_reports_closed', 'ub_comment_reports_details'],
				],
			]],
			['module.add', [
				'mcp',
				'MCP_QUEUE',
				[
					'module_basename'	=> '\mrgoldy\ultimateblog\mcp\approve_module',
					'modes'				=> ['ub_blog_approve', 'ub_comment_approve'],
				]
			]]
		];
	}
}
