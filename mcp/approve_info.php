<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog\mcp;

/**
 * Ultimate Blog MCP module info.
 */
class approve_info
{
	/**
	 * @return array
	 */
	function module()
	{
		return [
			'filename'	=> '\mrgoldy\ultimateblog\mcp\report_module',
			'title'		=> 'MCP_QUEUE',
			'modes'		=> [
				'ub_blog_approve'		=> ['title' => 'MCP_UB_BLOG_QUEUE', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_approve', 'cat' => ['MCP_QUEUE']],
				'ub_comment_approve'	=> ['title' => 'MCP_UB_COMMENT_QUEUE', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_approve', 'cat' => ['MCP_QUEUE']],
			],
		];
	}
}
