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
 * Ultimate Blog MCP module.
 */
class report_module
{
	var $u_action;

	/**
	 * @param $id
	 * @param $mode
	 */
	function main($id, $mode)
	{
		global $phpbb_container, $action;

		$lang = $phpbb_container->get('language');
		$request = $phpbb_container->get('request');
		$mcp_controller = $phpbb_container->get('mrgoldy.ultimateblog.controller.mcp');
		$mcp_controller->set_page_url($this->u_action);

		$start		= $request->variable('start', 0);
		$sort_time	= $request->variable('ubst', 0);
		$sort_by	= $request->variable('ubsb', 'report_time');
		$sort_dir	= $request->variable('ubsd', 'DESC');

		switch ($mode)
		{
			case 'ub_blog_reports_open':
				$this->tpl_name = 'ub_mcp_report_body';
				$this->page_title = $lang->lang('MCP_UB_BLOG_REPORTS_OPEN');
				$mcp_controller->blog_reports_open($id, $mode, $action, $start, $sort_time, $sort_by, $sort_dir);
			break;

			case 'ub_blog_reports_closed':
				$this->tpl_name = 'ub_mcp_report_body';
				$this->page_title = $lang->lang('MCP_UB_BLOG_REPORTS_CLOSED');
				$mcp_controller->blog_reports_closed($id, $mode, $action, $start, $sort_time, $sort_by, $sort_dir);
			break;

			case 'ub_blog_reports_details':
				$this->tpl_name = 'ub_mcp_report_details';
				$this->page_title = $lang->lang('MCP_UB_BLOG_REPORTS_DETAILS');
				$mcp_controller->reports_details($id, $mode, $action);
			break;

			case 'ub_comment_reports_open':
				$this->tpl_name = 'ub_mcp_report_body';
				$this->page_title = $lang->lang('MCP_UB_COMMENT_REPORTS_OPEN');
				$mcp_controller->comment_reports_open($id, $mode, $action, $start, $sort_time, $sort_by, $sort_dir);
			break;

			case 'ub_comment_reports_closed':
				$this->tpl_name = 'ub_mcp_report_body';
				$this->page_title = $lang->lang('MCP_UB_COMMENT_REPORTS_CLOSED');
				$mcp_controller->comment_reports_closed($id, $mode, $action, $start, $sort_time, $sort_by, $sort_dir);
			break;

			case 'ub_comment_reports_details':
				$this->tpl_name = 'ub_mcp_report_details';
				$this->page_title = $lang->lang('MCP_UB_COMMENT_REPORTS_DETAILS');
				$mcp_controller->reports_details($id, $mode, $action);
			break;
		}
	}
}
