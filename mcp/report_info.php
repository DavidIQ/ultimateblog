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
class report_info
{
	/**
	 * @return array
	 */
	function module()
	{
		return array(
			'filename'	=> '\mrgoldy\ultimateblog\mcp\report_module',
			'title'		=> 'MCP_REPORTS',
			'modes'		=> array(
				'ub_blog_reports_open'			=> array('title' => 'MCP_UB_BLOG_REPORTS_OPEN', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_report', 'cat' => array('MCP_REPORTS')),
				'ub_blog_reports_closed'		=> array('title' => 'MCP_UB_BLOG_REPORTS_CLOSED', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_report', 'cat' => array('MCP_REPORTS')),
				'ub_blog_reports_details'		=> array('title' => 'MCP_UB_BLOG_REPORTS_DETAILS', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_report', 'cat' => array('MCP_REPORTS')),
				'ub_comment_reports_open'		=> array('title' => 'MCP_UB_COMMENT_REPORTS_OPEN', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_report', 'cat' => array('MCP_REPORTS')),
				'ub_comment_reports_closed'		=> array('title' => 'MCP_UB_COMMENT_REPORTS_CLOSED', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_report', 'cat' => array('MCP_REPORTS')),
				'ub_comment_reports_details'	=> array('title' => 'MCP_UB_COMMENT_REPORTS_DETAILS', 'auth' => 'ext_mrgoldy/ultimateblog && aclf_m_ub_report', 'cat' => array('MCP_REPORTS')),
			),
		);
	}
}
