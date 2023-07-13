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
 * Ultimate Blog ACP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	/**
	 * @param $id
	 * @param $mode
	 */
	public function main($id, $mode)
	{
		global $phpbb_container;

		$lang = $phpbb_container->get('language');
		$request = $phpbb_container->get('request');
		$overview_controller = $phpbb_container->get('mrgoldy.ultimateblog.controller.admin.overview');
		$settings_controller = $phpbb_container->get('mrgoldy.ultimateblog.controller.admin.settings');
		$categories_controller = $phpbb_container->get('mrgoldy.ultimateblog.controller.admin.categories');

		$action = $request->variable('action', '');
		$category_id = $request->variable('category_id', 0);
		$overview_controller->set_page_url($this->u_action);
		$settings_controller->set_page_url($this->u_action);
		$categories_controller->set_page_url($this->u_action);

		switch ($mode)
		{
			case 'overview':
				$this->tpl_name = 'ub_overview';
				$this->page_title = $lang->lang('ACP_UB_OVERVIEW');
				$overview_controller->ub_overview($id, $mode, $action);
			break;

			case 'settings':
				$this->tpl_name = 'ub_settings';
				$this->page_title = $lang->lang('ACP_UB_SETTINGS');
				$settings_controller->ub_settings();
			break;

			case 'categories':
				$this->tpl_name = 'ub_categories';
				$this->page_title = $lang->lang('ACP_UB_CATEGORIES');
				switch ($action)
				{
					case 'add':
						$this->page_title = $lang->lang('ACP_UB_CATEGORY_ADD');
						$categories_controller->ub_category_add();
						return;
					break;

					case 'edit':
						$this->page_title = $lang->lang('ACP_UB_CATEGORY_EDIT');
						$categories_controller->ub_category_edit($category_id);
						return;
					break;

					case 'delete':
						$categories_controller->ub_category_delete($category_id);
					break;

					case 'move_down':
					case 'move_up':
						$categories_controller->ub_category_move($category_id, $action);
					break;
				}
				$categories_controller->ub_categories_index();
			break;
		}
	}
}
