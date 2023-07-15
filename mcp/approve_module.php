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
class approve_module
{
	var $u_action;

	/**
	 * @var string
	 */
	var $tpl_name;

	/**
	 * @var string
	 */
	var $page_title;

	/**
	 * @param $id
	 * @param $mode
	 */
	function main($id, $mode)
	{
		global $phpbb_container;

		$lang = $phpbb_container->get('language');
		$request = $phpbb_container->get('request');
		$mcp_controller = $phpbb_container->get('mrgoldy.ultimateblog.controller.mcp');
		$mcp_controller->set_page_url($this->u_action);
		
		switch ($mode)
		{
			case 'ub_blog_approve':
				$this->tpl_name = 'ub_mcp_approve_body';
				$this->page_title = $lang->lang('MCP_UB_BLOG_QUEUE');
				$mcp_controller->unapproved_blogs();
				break;

			case 'ub_comment_approve':
				$this->tpl_name = 'ub_mcp_approve_body';
				$this->page_title = $lang->lang('MCP_UB_COMMENT_QUEUE');
				$mcp_controller->unapproved_comments();
				break;
		}
	}
}
