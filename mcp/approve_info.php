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
		return array(
			'filename'	=> '\mrgoldy\ultimateblog\mcp\report_module',
			'title'		=> 'MCP_QUEUE',
			'modes'		=> array(
				'ub_blog_approve'		=> array('title' => 'MCP_UB_BLOG_QUEUE', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_approve', 'cat' => array('MCP_QUEUE')),
				'ub_comment_approve'	=> array('title' => 'MCP_UB_COMMENT_QUEUE', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_approve', 'cat' => array('MCP_QUEUE')),
			),
		);
	}
}
