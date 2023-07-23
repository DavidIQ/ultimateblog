<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog\controller;

/**
 * Class categories
 *
 * @package mrgoldy\ultimateblog\controller
 */
class categories
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \mrgoldy\ultimateblog\core\functions */
	protected $func;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbb\template\template */
	protected $template;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config				 $config		Config object
	 * @param \mrgoldy\ultimateblog\core\functions $func		Ultimate Blog functions
	 * @param \phpbb\controller\helper			 $helper		Controller helper object
	 * @param \phpbb\language\language			 $lang		Language object
	 * @param \phpbb\pagination					$pagination	Pagination object
	 * @param \phpbb\path_helper					$path_helper Path helper
	 * @param \phpbb\textformatter\s9e\renderer	$renderer
	 * @param \phpbb\template\template			 $template	Template object
	 * @internal param $ \phpbb\textformatter/s9e/renderer		$renderer		Renderer object
	 * @access	public
	 */
	public function __construct(\phpbb\config\config $config, $func, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\pagination $pagination, \phpbb\path_helper $path_helper, \phpbb\textformatter\s9e\renderer $renderer, \phpbb\template\template $template)
	{
		$this->config		= $config;
		$this->func			= $func;
		$this->helper		= $helper;
		$this->lang			= $lang;
		$this->pagination	= $pagination;
		$this->path_helper	= $path_helper;
		$this->renderer		= $renderer;
		$this->template		= $template;
	}

	/**
	 * @param $page
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function overview($page)
	{
		# Check if Ultimate Blog is enabled and if the user has the 'view' permission
		$this->func->ub_status();

		# Set start variable
		$start = (($page - 1) * $this->config['ub_blogs_per_page']);

		# Grab all categories
		$categories = $this->func->category_list();

		# Set up count for pagination.
		$count = count($categories);

		# Assign template block variables.
		foreach ($categories as $category)
		{
			$this->template->assign_block_vars('categories', array(
				'NAME'	=> $category['category_name'],
				'DESC'	=> $this->renderer->render($category['category_description']),
				'IMG'	=> !empty($category['category_image']) ? $this->path_helper->get_web_root_path() . $this->config['ub_image_cat_dir'] . '/' . $category['category_image'] : '',

				'S_IS_PRIVATE'	=> $category['is_private'],
				'U_CATEGORY'	=> $this->helper->route('mrgoldy_ultimateblog_category', array('category_id' => (int) $category['category_id'], 'title' => urlencode($category['category_name']))),
			));
		}

		# Create pagination
		$this->pagination->generate_template_pagination(
			array(
				'routes' => array(
					'mrgoldy_ultimateblog_categories',
					'mrgoldy_ultimateblog_categoriespage',
				),
				'params' => array(),
			), 'pagination', 'page', $count, $this->config['ub_blogs_per_page'], $start);

		# And the template variables.
		$this->template->assign_vars(array(
			'PAGE_NUMBER'			=> $this->pagination->on_page($count, $this->config['ub_blogs_per_page'], $start),
			'TOTAL_CATEGORIES'		=> $this->lang->lang('BLOG_CATEGORIES_COUNT', (int) $count),
			'ULTIMATEBLOG_TITLE'	=> $this->config['ub_title'],

			'U_BLOG_ARCHIVE'	=> $this->helper->route('mrgoldy_ultimateblog_archives'),
			'U_BLOG_CATEGORIES'	=> $this->helper->route('mrgoldy_ultimateblog_categories'),
			'U_BLOG_INDEX'		=> $this->helper->route('mrgoldy_ultimateblog_index'),
		));

		# Breadcrumbs: add the Ultimate Blog part
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->config['ub_title'],
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_index'),
		));

		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->lang->lang('BLOG_CATEGORIES'),
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_categories'),
		));

		return $this->helper->render('ub_categories.html', $this->config['ub_title'] . ' - ' . $this->lang->lang('BLOG_CATEGORIES'));
	}
}
