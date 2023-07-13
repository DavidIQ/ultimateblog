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

use phpbb\exception\http_exception;

/**
 * Class bloglist
 *
 * @package mrgoldy\ultimateblog\controller
 */
class bloglist
{
	/** @var \phpbb\auth\auth */
	protected $auth;

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

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth 						$auth			Authentication object
	* @param \phpbb\config\config					$config			Config object
	* @param \mrgoldy\ultimateblog\core\functions	$func			Ultimate Blog functions
	* @param \phpbb\controller\helper				$helper			Controller helper object
	* @param \phpbb\language\language				$lang			Language object
	* @param \phpbb\pagination						$pagination		Pagination object
	* @param \phpbb\path_helper						$path_helper	Path helper
	* @param \phpbb\textformatter\s9e\renderer		$renderer		Renderer object
	* @param \phpbb\request\request					$request		Request object
	* @param \phpbb\template\template				$template		Template object
	* @param \phpbb\user							$user			User object
	* @param \phpbb\textformatter\s9e\utils			$utils			Utils object
	* @access public
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, $func, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\pagination $pagination, \phpbb\path_helper $path_helper, \phpbb\textformatter\s9e\renderer $renderer, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\textformatter\s9e\utils $utils)
	{
		$this->auth			= $auth;
		$this->config		= $config;
		$this->func			= $func;
		$this->helper		= $helper;
		$this->lang			= $lang;
		$this->pagination	= $pagination;
		$this->path_helper	= $path_helper;
		$this->renderer		= $renderer;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->utils		= $utils;
	}

	/**
	 * @param $page
	 * @return mixed
	 */
	private function set_start($page)
	{
		return (($page - 1) * $this->config['ub_blogs_per_page']);
	}

	/**
	 * @param $page
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function index($page)
	{
		# Check if Ultimate Blog is enabled and if the user has the 'view' permission
		$this->func->ub_status();

		if (!$this->config['ub_custom_index'])
		{
			# Set start variable
			$start = $this->set_start($page);

			# Get a list of blogs and categories
			$blogs = $this->func->blog_list('index', $this->config['ub_blogs_per_page'], $start);
			$cats = $this->func->category_list();

			# Set up blog list
			$this->setup_blog_list($blogs['list'], $cats);

			# Start pagination
			$this->pagination->generate_template_pagination(
				array(
					'routes' => array(
						'mrgoldy_ultimateblog_index',
						'mrgoldy_ultimateblog_indexpage',
					),
					'params' => array(),
				), 'pagination', 'page', $blogs['count'], $this->config['ub_blogs_per_page'], $start);

			# Set up additional template variables
			$this->template->assign_vars(array(
				'PAGE_NUMBER'	=> $this->pagination->on_page($blogs['count'], $this->config['ub_blogs_per_page'], $start),
				'TOTAL_BLOGS'	=> $this->lang->lang('BLOG_COUNT', (int) $blogs['count']),
			));
		}
		else
		{
			$cats = $this->func->category_list();
			$rowset = $this->func->blog_index();

			foreach ($rowset as $row)
			{
				$block_title = '';
				# Per index panel we grab the blog list
				# 1: Specific category | 2: Specific category | 3: Specific category | 4: Latest | 5: Comments | 6: Rating | 7: Views
				switch ($row['block_name'])
				{
					case 'category1':
					case 'category2':
					case 'category3':
						$block_title = $row['category_name'];
						$blogs = $this->func->blog_index_list($row['block_name'], $row['block_limit'], $row['block_data'], 0);
					break;

					case 'latest':
						$block_title = $this->lang->lang('BLOG_INDEX_LATEST');
						$blogs = $this->func->blog_index_list($row['block_name'], $row['block_limit'], 0, 0);
					break;

					case 'comments':
						$block_title = $this->lang->lang('BLOG_INDEX_COMMENTED');
						$blogs = $this->func->blog_index_list($row['block_name'], $row['block_limit'], 0, 0);
					break;

					case 'rating':
						$block_title = $this->lang->lang('BLOG_INDEX_RATED');
						$blogs = $this->func->blog_index_list($row['block_name'], $row['block_limit'], 0, $row['block_data']);
					break;

					case 'views':
						$block_title = $this->lang->lang('BLOG_INDEX_VIEWED');
						$blogs = $this->func->blog_index_list($row['block_name'], $row['block_limit'], 0, 0);
					break;
				}

				$this->template->assign_block_vars('index_blocks', array(
					'TITLE'		=> $block_title,
				));

				foreach ($blogs as $blog)
				{
					# Assign the blog template block variables
					$this->template->assign_block_vars('index_blocks.blogs', array(
						'AUTHOR'	=> get_username_string('full', $blog['user_id'], $blog['username'], $blog['user_colour']),
						'ID'		=> $blog['blog_id'],
						'IMAGE'		=> $this->path_helper->get_web_root_path() . $this->config['ub_image_dir'] . '/' . $blog['blog_image'],
						'TITLE'		=> $blog['blog_title'],

						'S_IS_AUTHOR'		=> ($this->user->data['user_id'] == $blog['user_id']) ? true : false,
						'S_IS_REPORTED'		=> $blog['blog_reported'],
						'S_IS_UNAPPROVED'	=> !$blog['blog_approved'],

						'U_BLOG'		=> $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog['blog_id'], 'title' => urlencode($blog['blog_title']))),
					));

					# Explode the category list for this blog
					$cat_ids = explode(',', $blog['categories']);

					# Iterate over all the categories for this blog
					foreach ($cat_ids as $cat_id)
					{
						# Assign the blog categories template block variables
						$this->template->assign_block_vars('index_blocks.blogs.cats', array(
							'BLOG_CATEGORY_NAME'	=> $cats[$cat_id]['category_name'],

							'U_BLOG_CATEGORY'		=> $this->helper->route('mrgoldy_ultimateblog_category', array('category_id' => (int) $cat_id, 'title' => urlencode($cats[$cat_id]['category_name']))),
						));
					}
				}
			}

			# Setup_blog_list function is not called from the custom index, so call the template function from here specifically.
			$this->setup_bloglist_template();
		}

		# Set up additional template variables
		$this->template->assign_vars(array(
			'S_BLOG_INDEX'			=> true,
			'S_BLOG_CUSTOM_INDEX'	=> $this->config['ub_custom_index'],
			'U_BLOG_ADD'			=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'add')),
		));

		return $this->helper->render('ub_blog_index.html', $this->config['ub_title']);
	}

	/**
	 * @param $category_id
	 * @param $page
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function category($category_id, $page)
	{
		# Request pagination start
		$start = $this->set_start($page);

		# Get a list of blogs and categories
		$blogs = $this->func->blog_list('category', $this->config['ub_blogs_per_page'], $start, (int) $category_id);
		$cats = $this->func->category_list();
		$category_row = $cats[$category_id];

		if (!$category_row['category_name'])
		{
			throw new http_exception(404, $this->lang->lang('BLOG_ERROR_NO_CATEGORY'));
		}

		# Set up blog list
		$this->setup_blog_list($blogs['list'], $cats, $category_id);

		$this->pagination->generate_template_pagination(
			array(
				'routes' => array(
					'mrgoldy_ultimateblog_category',
					'mrgoldy_ultimateblog_categorypage',
				),
				'params' => array('category_id' => (int) $category_id),
			), 'pagination', 'page', $blogs['count'], $this->config['ub_blogs_per_page'], $start);

		# Set up additional template variables
		$this->template->assign_vars(array(
			'CATEGORY_DESCRIPTION'			=> $this->renderer->render($category_row['category_description']),
			'CATEGORY_DESCRIPTION_META'		=> $this->utils->clean_formatting($category_row['category_description']),
			'CATEGORY_TITLE'				=> $category_row['category_name'],
			'PAGE_NUMBER'					=> $this->pagination->on_page($blogs['count'], $this->config['ub_blogs_per_page'], $start),
			'TOTAL_BLOGS'					=> $this->lang->lang('BLOG_COUNT', (int) $blogs['count']),

			'S_BLOG_CATEGORY'				=> true,
			'S_BLOG_CATEGORY_ID'			=> $category_row['category_id'],
			# Needed for atom feed
			'S_BLOG_CATEGORY_MODE'			=> 'category',
			'S_BLOG_CATEGORY_POST_PRIVATE'	=> $this->auth->acl_get('u_ub_post_private'),
			'S_BLOG_CATEGORY_PRIVATE'		=> $category_row['is_private'],

			'U_BLOG_ADD'					=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'add', 'cid' => (int) $category_id)),
		));

		# Breadcrumbs
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $category_row['category_name'],
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_category', array('category_id' => (int) $category_id, 'title' => urlencode($category_row['category_name']))),
		));

		return $this->helper->render('ub_blog_index.html', $this->config['ub_title'] . ' - ' . $category_row['category_name']);
	}

	/**
	 * @param $year
	 * @param $month
	 * @param $page
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function archive($year, $month, $page)
	{
		# Request pagination start
		$start = $this->set_start($page);

		# Get a list of blogs and categories
		$blogs = $this->func->blog_list('archive', $this->config['ub_blogs_per_page'], $start, $archive = array('year' => $year, 'month' => $month));
		$cats = $this->func->category_list();

		# Check if there are blogs for this month and year.
		if (empty($blogs['list'][0]['blog_date']))
		{
			throw new http_exception(404, $this->lang->lang('BLOG_ERROR_ARCHIVE_EMPTY'));
		}

		# Set up blog list
		$this->setup_blog_list($blogs['list'], $cats);

		# Get month and year format
		$date_text = $this->user->format_date($blogs['list'][0]['blog_date'], 'F Y');

		$this->pagination->generate_template_pagination(
			array(
				'routes' => array(
					'mrgoldy_ultimateblog_archive',
					'mrgoldy_ultimateblog_archivepage',
				),
				'params' => array('year' => (int) $year, 'month' => (int) $month),
			), 'pagination', 'page', $blogs['count'], $this->config['ub_blogs_per_page'], $start);

		# Set up additional template variables
		$this->template->assign_vars(array(
			'ARCHIVE_TITLE'			=> $date_text,
			'ARCHIVE_DESCRIPTION'	=> $this->lang->lang('BLOG_ARCHIVE_DESCRIPTION_DATE', $this->user->format_date($blogs['list'][0]['blog_date'], 'F'), $this->user->format_date($blogs['list'][0]['blog_date'], 'Y')),
			'PAGE_NUMBER'			=> $this->pagination->on_page($blogs['count'], $this->config['ub_blogs_per_page'], $start),
			'TOTAL_BLOGS'			=> $this->lang->lang('BLOG_COUNT', (int) $blogs['count']),

			'S_BLOG_ARCHIVE'		=> true,
		));

		# Breadcrumbs
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->lang->lang('BLOG_ARCHIVE'),
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_archives'),
		));

		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $date_text,
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_archive', array('year' => (int) $year, 'month' => (int) $month)),
		));

		return $this->helper->render('ub_blog_index.html', $this->config['ub_title'] . ' - ' . $this->lang->lang('BLOG_ARCHIVE') . ' - ' . $date_text);
	}

	/**
	 * @param $user_id
	 * @param $page
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function user($user_id, $page)
	{
		# Request pagination start
		$start = $this->set_start($page);

		$blogs = $this->func->blog_list('user', $this->config['ub_blogs_per_page'], $start, (int) $user_id);
		$cats = $this->func->category_list();

		# Check if there are blogs for this user
		if (empty($blogs['list'][0]['username']))
		{
			throw new http_exception(404, $this->lang->lang('BLOG_ERROR_NO_USER_BLOG'));
		}

		$this->setup_blog_list($blogs['list'], $cats);

		$this->pagination->generate_template_pagination(
			array(
				'routes' => array(
					'mrgoldy_ultimateblog_user',
					'mrgoldy_ultimateblog_userpage',
				),
				'params' => array('user_id' => (int) $user_id),
			), 'pagination', 'page', $blogs['count'], $this->config['ub_blogs_per_page'], $start);

		$this->template->assign_vars(array(
			'PAGE_NUMBER'	=> $this->pagination->on_page($blogs['count'], $this->config['ub_blogs_per_page'], $start),
			'TOTAL_BLOGS'	=> $this->lang->lang('BLOG_COUNT', (int) $blogs['count']),
		));

		return $this->helper->render('ub_blog_index.html', $this->config['ub_title'] . ' - ' . $blogs['list'][0]['username']);
	}

	/**
	 * @param	 $blogs
	 * @param	 $cats
	 * @param int $category_id
	 */
	private function setup_blog_list($blogs, $cats, $category_id = 0)
	{
		# Iterate over all the blogs
		foreach ($blogs as $blog)
		{
			# Assign the blog template block variables
			$this->template->assign_block_vars('blogs', array(
				'AUTHOR'	=> get_username_string('full', $blog['user_id'], $blog['username'], $blog['user_colour']),
				'ID'		=> $blog['blog_id'],
				'IMAGE'		=> $this->path_helper->get_web_root_path() . $this->config['ub_image_dir'] . '/' . $blog['blog_image'],
				'TITLE'		=> $blog['blog_title'],

				'S_IS_AUTHOR'		=> ($this->user->data['user_id'] == $blog['user_id']) ? true : false,
				'S_IS_REPORTED'		=> $blog['blog_reported'],
				'S_IS_UNAPPROVED'	=> !$blog['blog_approved'],

				'U_BLOG'		=> $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog['blog_id'], 'title' => urlencode($blog['blog_title']))),
			));

			# Explode the category list for this blog
			$cat_ids = explode(',', $blog['categories']);

			# Iterate over all the categories for this blog
			foreach ($cat_ids as $cat_id)
			{
				# Assign the blog categories template block variables
				$this->template->assign_block_vars('blogs.cats', array(
					'BLOG_CATEGORY_NAME'	=> $cats[$cat_id]['category_name'],

					'S_CURRENT_CATEGORY'	=> $category_id == $cat_id ? true : false,

					'U_BLOG_CATEGORY'		=> $this->helper->route('mrgoldy_ultimateblog_category', array('category_id' => (int) $cat_id, 'title' => urlencode($cats[$cat_id]['category_name']))),
				));
			}
		}

		$this->setup_bloglist_template();
	}

	private function setup_bloglist_template()
	{
		# Set up template variables
		$this->template->assign_vars(array(
			'S_BLOG_CAN_ADD'			=> $this->auth->acl_get('u_ub_post'),
			'S_BLOG_CAN_COMMENT'		=> $this->auth->acl_get('u_ub_comment'),
			'S_BLOG_CAN_COMMENT_VIEW' 	=> $this->auth->acl_get('u_ub_comment_view'),
			'S_BLOG_CAN_EDIT'			=> $this->auth->acl_get('u_ub_edit'),
			'S_BLOG_CAN_EDIT_VIEW'		=> $this->auth->acl_get('u_ub_edit_view'),
			'S_BLOG_CAN_DELETE'			=> $this->auth->acl_get('u_ub_delete'),
			'S_BLOG_CAN_NOAPPROVE'		=> $this->auth->acl_get('u_ub_noapprove'),
			'S_BLOG_CAN_RATE'			=> $this->auth->acl_get('u_ub_rate'),
			'S_BLOG_CAN_REPORT'			=> $this->auth->acl_get('u_ub_report'),
			'S_BLOG_COMMENTS_ENABLED' 	=> $this->config['ub_enable_comments'],
			'S_BLOG_MOD_APPROVE'		=> $this->auth->acl_get('m_ub_approve'),
			'S_BLOG_MOD_REPORT'			=> $this->auth->acl_get('m_ub_report'),
			'S_BLOG_RATING_ENABLED'		=> $this->config['ub_enable_rating'],

			'U_BLOG_ARCHIVE'			=> $this->helper->route('mrgoldy_ultimateblog_archives'),
			'U_BLOG_CATEGORIES'			=> $this->helper->route('mrgoldy_ultimateblog_categories'),
			'U_BLOG_INDEX'				=> $this->helper->route('mrgoldy_ultimateblog_index'),
			'U_BLOG_ADD'				=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'add')),
		));

		# Breadcrumbs: add the Ultimate Blog part
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->config['ub_title'],
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_index'),
		));
	}
}
