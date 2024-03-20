<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Ultimate Blog Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string .php Extension */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string Ultimate Blog blogs table */
	protected $ub_blogs_table;

	/** @var string Ultimate Blog categories table */
	protected $ub_categories_table;

	/** @var string Ultimate Blog comments table */
	protected $ub_comments_table;

	/** @var string Ultimate Blog reports table */
	protected $ub_reports_table;

	/** @var \mrgoldy\ultimateblog\core\functions */
	protected $functions;

	/** @var \phpbb\request\request */
	protected $request;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth					$auth				Authentication object
	 * @param \phpbb\config\config				$config				Configuration object
	 * @param \phpbb\config\db_text			 $config_text		 Config text tool
	 * @param \phpbb\db\driver\driver_interface $db					Database driver interface
	 * @param \phpbb\controller\helper			$helper				Controller helper object
	 * @param \phpbb\language\language			$lang
	 * @param \phpbb\template\template			$template			Template object
	 * @param \phpbb\user						$user				User object
	 * @param string							$php_ext			 phpEx
	 * @param									$phpbb_root_path
	 * @param string							$ub_blogs_table		Ultimate Blog blogs table
	 * @param string							$ub_categories_table Ultimate Blog categories table
	 * @param string							$ub_comments_table	Ultimate Blog comments table
	 * @param									$ub_reports_table
	 * @param \mrgoldy\ultimateblog\core\functions $funcs Functions object
	 * @param \phpbb\request\request			$request Request object
	 * @internal param \phpbb\language\language $language Language object
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\template\template $template, \phpbb\user $user, $php_ext, $phpbb_root_path, $ub_blogs_table, $ub_categories_table, $ub_comments_table, $ub_reports_table, \mrgoldy\ultimateblog\core\functions $funcs, \phpbb\request\request $request)
	{
		$this->auth			= $auth;
		$this->config		= $config;
		$this->config_text	= $config_text;
		$this->db			= $db;
		$this->helper		= $helper;
		$this->lang			= $lang;
		$this->template 	= $template;
		$this->user			= $user;
		$this->php_ext		= $php_ext;
		$this->phpbb_root_path		= $phpbb_root_path;
		$this->ub_blogs_table		= $ub_blogs_table;
		$this->ub_categories_table	= $ub_categories_table;
		$this->ub_comments_table	= $ub_comments_table;
		$this->ub_reports_table		= $ub_reports_table;
		$this->functions			= $funcs;
		$this->request				= $request;
	}

	/**
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup'							=> 'load_language_on_setup',
			'core.page_header'							=> 'add_page_header_link',
			'core.mcp_front_reports_count_query_before'	=> 'mcp_front_display_latest',
			'core.modify_mcp_modules_display_option'	=> 'mcp_modules_display',
			'core.viewonline_overwrite_location'		=> 'viewonline_page',
			'core.memberlist_prepare_profile_data'		=> 'add_viewprofile_blog_info',
			'core.permissions'							=> 'add_permission',
			'core.viewtopic_modify_post_row'			=> 'viewtopic_post_row_add_blog_count',
			'core.search_modify_submit_parameters'		=> 'search_setup',
			'core.search_get_topic_data'				=> 'search_get_blog_data',
		];
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'mrgoldy/ultimateblog',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add a link to the controller in the forum navbar
	 */
	public function add_page_header_link()
	{
		$announcement_text = $this->config_text->get('ub_announcement_text');
		// Generate for display
		if (!$this->config['ub_announcement_html'])
		{
			$options = 0;
			if ($this->config['ub_announcement_bbcode'])
			{
				$options |= OPTION_FLAG_BBCODE;
			}
			if ($this->config['ub_announcement_smilies'])
			{
				$options |= OPTION_FLAG_SMILIES;
			}
			if ($this->config['ub_announcement_urls'])
			{
				$options |= OPTION_FLAG_LINKS;
			}
			$announcement_text = generate_text_for_display($announcement_text, $this->config['ub_announcement_uid'], $this->config['ub_announcement_bitfield'], $options, false);
		}

		$this->template->assign_vars([
			'UB_ANNOUNCEMENT_TEXT'		=> $announcement_text,
			'UB_FA_ICON'				=> $this->config['ub_fa_icon'],
			'UB_TITLE'					=> $this->config['ub_title'],

			'S_UB_ANNOUNCEMENT_ENABLED'		=> $this->config['ub_enable_announcement'],
			'S_UB_FEED_ENABLED'				=> $this->config['ub_enable_feed'],
			'S_UB_FEED_CATEGORIES_ENABLED'	=> $this->config['ub_enable_feed_cats'],
			'S_UB_FEED_VIEW'				=> $this->auth->acl_get('u_ub_view') && $this->auth->acl_get('u_ub_feed_view'),
			'S_ULTIMATEBLOG_ENABLED'		=> $this->config['ub_enable'],

			'U_BLOG_INDEX'				=> $this->helper->route('mrgoldy_ultimateblog_index'),
		]);
	}

	public function mcp_front_display_latest()
	{
		# Latest 5 Blogs reported
		$this->template->assign_var('S_SHOW_ULTIMATEBLOG_REPORTS', $this->auth->acl_get('m_ub_report'));

		if ($this->auth->acl_get('m_ub_report'))
		{
			$sql = 'SELECT COUNT(report_id) AS total
					FROM ' . $this->ub_reports_table . '
					WHERE comment_id = 0
						AND report_closed = 0';
			$result = $this->db->sql_query($sql);
			$total = (int) $this->db->sql_fetchfield('total');
			$this->db->sql_freeresult($result);
			if ($total)
			{
				$sql_ary = [
					'SELECT'	=> 'r.report_time, r.blog_id, b.blog_title, b.blog_date as post_time, u.username, u.user_colour, u.user_id, u2.username as author_name, u2.user_colour as author_colour, u2.user_id as author_id',
					'FROM'		=> [
						$this->ub_blogs_table	=> 'b',
						$this->ub_reports_table	=> 'r',
						USERS_TABLE				=> ['u', 'u2'],
					],
					'WHERE'		=> 'r.blog_id = b.blog_id
						AND r.comment_id = 0
						AND r.report_closed = 0
						AND r.user_id = u.user_id
						AND b.author_id = u2.user_id',
					'ORDER_BY'	=> 'b.blog_date DESC, b.blog_id DESC',
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_ary);
				$result = $this->db->sql_query_limit($sql, 5);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$this->template->assign_block_vars('blog_report', [
						'AUTHOR_FULL'		=> get_username_string('full', $row['author_id'], $row['author_name'], $row['author_colour']),
						'REPORTER_FULL'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
						'SUBJECT'			=> $row['blog_title'],
						'POST_TIME'			=> $this->user->format_date($row['post_time']),
						'REPORT_TIME'		=> $this->user->format_date($row['report_time']),

						'U_BLOG_REPORT_DETAILS'	=> append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}", 'blog_id=' . $row['blog_id'] . "&amp;i=-mrgoldy-ultimateblog-mcp-report_module&amp;mode=ub_blog_reports_details"),
					]);
				}
				$this->db->sql_freeresult($result);
			}

			$sql = 'SELECT COUNT(report_id) AS total_comment
					FROM ' . $this->ub_reports_table . '
					WHERE comment_id = 0
						AND report_closed = 0';
			$result = $this->db->sql_query($sql);
			$total_comment = (int) $this->db->sql_fetchfield('total_comment');
			$this->db->sql_freeresult($result);
			if ($total_comment)
			{
				$sql_ary = [
					'SELECT'	=> 'r.report_time, b.blog_title, c.comment_id, c.comment_time as post_time, u.username, u.user_colour, u.user_id, u2.username as author_name, u2.user_colour as author_colour, u2.user_id as author_id',
					'FROM'		=> [
						$this->ub_blogs_table		=> 'b',
						$this->ub_comments_table	=> 'c',
						$this->ub_reports_table		=> 'r',
						USERS_TABLE					=> ['u', 'u2'],
					],

					'WHERE'		=> 'r.blog_id = b.blog_id
						AND r.comment_id = c.comment_id
						AND r.comment_id != 0
						AND r.report_closed = 0
						AND r.user_id = u.user_id
						AND c.user_id = u2.user_id',
					'ORDER_BY'	=> 'c.comment_time DESC, c.comment_id DESC',
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_ary);
				$result = $this->db->sql_query_limit($sql, 5);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$this->template->assign_block_vars('comment_report', [
						'U_BLOG_REPORT_DETAILS'	=> append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}", 'comment_id=' . $row['comment_id'] . "&amp;i=-mrgoldy-ultimateblog-mcp-report_module&amp;mode=ub_comment_reports_details"),
						'REPORTER_FULL'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
						'AUTHOR_FULL'		=> get_username_string('full', $row['author_id'], $row['author_name'], $row['author_colour']),
						'SUBJECT'		=> $row['blog_title'],
						'REPORT_TIME'	=> $this->user->format_date($row['report_time']),
						'POST_TIME'		=> $this->user->format_date($row['post_time']),
					]);
				}
				$this->db->sql_freeresult($result);
			}

			$this->template->assign_vars([
				'MCP_UB_BLOG_REPORTS_TOTAL'		=> $this->lang->lang('MCP_UB_BLOG_REPORTS_TOTAL', (int) $total),
				'MCP_UB_COMMENT_REPORTS_TOTAL'	=> $this->lang->lang('MCP_UB_COMMENT_REPORTS_TOTAL', (int) $total_comment),
			]);
		}
	}

	/*
	* Fix display for Ultimate Blog's MCP Modules
	*
	* Modules added:
	* 'ub_blog_reports_open', 'ub_blog_reports_closed', 'ub_blog_reports_details', 'ub_comment_reports_open', 'ub_comment_reports_closed, 'ub_comment_reports_details'
	*
	* @param object		$event		Event object
	* @return null
	* @access public
	*/
	/**
	 * @param $event
	 */
	public function mcp_modules_display($event)
	{
		$module = $event['module'];
		$mode = $event['mode'];
		if ($mode == 'ub_blog_reports_open' || $mode == 'ub_blog_reports_closed' || $mode == 'ub_blog_reports_details' || $mode == 'ub_comment_reports_open' || $mode == 'ub_comment_reports_closed' || $mode == 'ub_comment_reports_details')
		{
			$module->set_display('pm_reports', 'pm_report_details', false);
			$module->set_display('reports', 'report_details', false);
		}

		if ($mode == '' || $mode == 'reports' || $mode == 'reports_closed' || $mode == 'report_details' || $mode == 'pm_reports' || $mode == 'pm_reports_closed' || $mode == 'pm_report_details'
						|| $mode == 'ub_blog_reports_open' || $mode == 'ub_blog_reports_closed' || $mode == 'ub_comment_reports_open' || $mode == 'ub_comment_reports_closed' || $mode == 'ub_comment_reports_details')
		{
			$module->set_display('\mrgoldy\ultimateblog\mcp\report_module', 'ub_blog_reports_details', false);
		}

		if ($mode == '' || $mode == 'reports' || $mode == 'reports_closed' || $mode == 'report_details' || $mode == 'pm_reports' || $mode == 'pm_reports_closed' || $mode == 'pm_report_details'
						|| $mode == 'ub_blog_reports_open' || $mode == 'ub_blog_reports_closed' || $mode == 'ub_blog_reports_details' || $mode == 'ub_comment_reports_open' || $mode == 'ub_comment_reports_closed')
		{
			$module->set_display('\mrgoldy\ultimateblog\mcp\report_module', 'ub_comment_reports_details', false);
		}
	}

	/**
	 * Show users viewing Ultimate Blog on the Who Is Online page
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function viewonline_page($event)
	{
		# Add viewonline language file
		$this->lang->add_lang('viewonline', 'mrgoldy/ultimateblog');

		# Grab Ultimate Blog category information
		$categories = [];
		$sql = 'SELECT category_id, category_name FROM ' . $this->ub_categories_table;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$categories[$row['category_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		if ($event['on_page'][1] === 'app' && strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/blog') === 0)
		{
			$params = explode('/', $event['row']['session_page']);

			switch ($params[2])
			{
				case 'archive':
					$event['location'] = ctype_digit($params[3]) && ctype_digit($params[4]) ? $this->lang->lang('UB_VIEWONLINE_ARCHIVE', $this->config['UB_TITLE'], $params[3], $this->user->format_date(mktime(0, 0, 0, (int) $params[4]), 'F')): $this->lang->lang('UB_VIEWONLINE_ARCHIVES', $this->config['ub_title']);
					$event['location_url'] = ctype_digit($params[3]) && ctype_digit($params[4]) ? $this->helper->route('mrgoldy_ultimateblog_archive', ['year' => (int) $params[3], 'month' => (int) $params[4]]) : $this->helper->route('mrgoldy_ultimateblog_archives');
				break;

				case 'archives':
					$event['location'] = $this->lang->lang('UB_VIEWONLINE_ARCHIVES', $this->config['ub_title']);
					$event['location_url'] = $this->helper->route('mrgoldy_ultimateblog_archives');
				break;

				case 'category':
					$event['location'] = ctype_digit($params[3]) ? $this->lang->lang('UB_VIEWONLINE_CATEGORY', $this->config['ub_title'], $categories[$params[3]]['category_name']) : $this->lang->lang('UB_VIEWONLINE_CATEGORIES', $this->config['ub_title']);
					$event['location_url'] = ctype_digit($params[3]) ? $this->helper->route('mrgoldy_ultimateblog_category', ['category_id' => (int) $params[3], 'title' => $params[4]]): $this->helper->route('mrgoldy_ultimateblog_categories');
				break;

				case 'categories':
					$event['location'] = $this->lang->lang('UB_VIEWONLINE_CATEGORIES', $this->config['ub_title']);
					$event['location_url'] = $this->helper->route('mrgoldy_ultimateblog_categories');
				break;

				case 'posting':
					$event['location'] = $this->lang->lang('UB_VIEWONLINE_POSTING', $this->config['ub_title']);
					$event['location_url'] = $this->helper->route('mrgoldy_ultimateblog_index');
				break;

				case 'user':
					$event['location'] = $this->lang->lang('UB_VIEWONLINE_USER', $this->config['ub_title']);
					$event['location_url'] = ctype_digit($params[3]) ? $this->helper->route('mrgoldy_ultimateblog_user', ['user_id' => (int) $params[3]]) : $this->lang->lang('UB_VIEWONLINE_INDEX', $this->config['ub_title']);
				break;

				case 'view':
					$event['location'] = $this->lang->lang('UB_VIEWONLINE_BLOG', $this->config['ub_title']);
					$event['location_url'] = ctype_digit($params[3]) ? $this->helper->route('mrgoldy_ultimateblog_view', ['blog_id' => (int) $params[3], 'title' => $params[4]]) : $this->lang->lang('UB_VIEWONLINE_INDEX', $this->config['ub_title']);
				break;

				case 'page':
				default:
					$event['location'] = $this->lang->lang('UB_VIEWONLINE_INDEX', $this->config['ub_title']);
					$event['location_url'] = $this->helper->route('mrgoldy_ultimateblog_index');
				break;
			}
		}
	}

	/**
	 * @param $event
	 */
	public function add_viewprofile_blog_info($event)
	{
		if (!$this->config['ub_enable'])
		{
			return;
		}

		$user_id = (int) $event['data']['user_id'];
		$blog_count = self::get_blog_count($user_id);
		$comment_count = self::get_comment_count($user_id);
		
		$template_data = $event['template_data'];
		$template_data['UB_BLOG_COUNT'] = $blog_count;
		$template_data['U_UB_USER_BLOG_POSTS'] = $this->helper->route('mrgoldy_ultimateblog_user', ['user_id' => $user_id]);
		$template_data['UB_COMMENT_COUNT'] = $comment_count;
		$template_data['U_UB_USER_BLOG_COMMENTS'] = $this->helper->route('mrgoldy_ultimateblog_usercomments', ['user_id' => $user_id]);

		$event['template_data'] = $template_data;
	}

	/**
	* Add permissions for Ultimate Blog
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$categories = $event['categories'];

		$categories['ultimateblog'] = 'ACL_CAT_ULTIMATEBLOG';

		$permissions['u_ub_view']				= ['lang' => 'ACL_U_UB_VIEW', 'cat' => 'ultimateblog'];
		$permissions['u_ub_post']				= ['lang' => 'ACL_U_UB_POST', 'cat' => 'ultimateblog'];
		$permissions['u_ub_post_private']		= ['lang' => 'ACL_U_UB_POST_PRIVATE', 'cat' => 'ultimateblog'];
		$permissions['u_ub_edit']				= ['lang' => 'ACL_U_UB_EDIT', 'cat' => 'ultimateblog'];
		$permissions['u_ub_edit_view']			= ['lang' => 'ACL_U_UB_EDIT_VIEW', 'cat' => 'ultimateblog'];
		$permissions['u_ub_delete']				= ['lang' => 'ACL_U_UB_DELETE', 'cat' => 'ultimateblog'];
		$permissions['u_ub_noapprove']			= ['lang' => 'ACL_U_UB_NOAPPROVE', 'cat' => 'ultimateblog'];
		$permissions['u_ub_comment_delete']		= ['lang' => 'ACL_U_UB_COMMENT_DELETE', 'cat' => 'ultimateblog'];
		$permissions['u_ub_comment_edit']		= ['lang' => 'ACL_U_UB_COMMENT_EDIT', 'cat' => 'ultimateblog'];
		$permissions['u_ub_comment_noapprove']	= ['lang' => 'ACL_U_UB_COMMENT_NOAPPROVE', 'cat' => 'ultimateblog'];
		$permissions['u_ub_comment_post']		= ['lang' => 'ACL_U_UB_COMMENT_POST', 'cat' => 'ultimateblog'];
		$permissions['u_ub_comment_view']		= ['lang' => 'ACL_U_UB_COMMENT_VIEW', 'cat' => 'ultimateblog'];
		$permissions['u_ub_rate']				= ['lang' => 'ACL_U_UB_RATE', 'cat' => 'ultimateblog'];
		$permissions['u_ub_report']				= ['lang' => 'ACL_U_UB_REPORT', 'cat' => 'ultimateblog'];
		$permissions['u_ub_feed_view']			= ['lang' => 'ACL_U_UB_FEED_VIEW', 'cat' => 'ultimateblog'];

		$permissions['m_ub_edit']				= ['lang' => 'ACL_M_UB_EDIT', 'cat' => 'ultimateblog'];
		$permissions['m_ub_delete']				= ['lang' => 'ACL_M_UB_DELETE', 'cat' => 'ultimateblog'];
		$permissions['m_ub_approve']			= ['lang' => 'ACL_M_UB_APPROVE', 'cat' => 'ultimateblog'];
		$permissions['m_ub_changeauthor']		= ['lang' => 'ACL_M_UB_CHANGEAUTHOR', 'cat' => 'ultimateblog'];
		$permissions['m_ub_edit_lock']			= ['lang' => 'ACL_M_UB_EDIT_LOCK', 'cat' => 'ultimateblog'];
		$permissions['m_ub_edit_delete']		= ['lang' => 'ACL_M_UB_EDIT_DELETE', 'cat' => 'ultimateblog'];
		$permissions['m_ub_view_friends_only']	= ['lang' => 'ACL_M_UB_VIEW_FRIENDS_ONLY', 'cat' => 'ultimateblog'];
		$permissions['m_ub_lock_rating']		= ['lang' => 'ACL_M_UB_LOCK_RATING', 'cat' => 'ultimateblog'];
		$permissions['m_ub_lock_comments']		= ['lang' => 'ACL_M_UB_LOCK_COMMENTS', 'cat' => 'ultimateblog'];
		$permissions['m_ub_report']				= ['lang' => 'ACL_M_UB_REPORT', 'cat' => 'ultimateblog'];

		$permissions['a_ub_overview']			= ['lang' => 'ACL_A_UB_OVERVIEW', 'cat' => 'ultimateblog'];
		$permissions['a_ub_settings']			= ['lang' => 'ACL_A_UB_SETTINGS', 'cat' => 'ultimateblog'];
		$permissions['a_ub_categories']			= ['lang' => 'ACL_A_UB_CATEGORIES', 'cat' => 'ultimateblog'];

		$event['categories'] = $categories;
		$event['permissions'] = $permissions;
	}

	/**
	* Add the blog count to the post row in viewtopic
	*
	* @param \phpbb\event\data $event The event object
	*/
	public function viewtopic_post_row_add_blog_count($event)
	{
		if (!$this->config['ub_enable'])
		{
			return;
		}

		$poster_id = $event['row']['user_id'];
		$blog_count = self::get_blog_count($poster_id);

		if ($blog_count > 0)
		{
			$post_row = $event['post_row'];
			$post_row['U_USER_BLOG_POSTS'] = $this->helper->route('mrgoldy_ultimateblog_user', ['user_id' => $poster_id]);
			$post_row['USER_BLOG_COUNT'] = $blog_count;
			$event['post_row'] = $post_row;
		}
	}

	/**
	 * Setup search page
	 */
	public function search_setup()
	{
		if (!$this->config['ub_enable'])
		{
			return;
		}

		$this->lang->add_lang('search', 'mrgoldy/ultimateblog');
		$search_panel = $this->request->variable('sp', 'forums-panel');
		
		$this->template->assign_vars([
			'UB_SHOW_SEARCH_PANEL'		=> $search_panel,
		]);
		
		$categories = $this->functions->category_list();
		foreach ($categories as $category)
		{
			$this->template->assign_block_vars('categories', [
				'CAT_ID'		=> $category['category_id'],
				'CAT_NAME'		=> $category['category_name'],
			]);
		}
	}

	/**
	 * Get blog data for search
	 *
	 * @param \phpbb\event\data $event The event object
	 */
	public function search_get_blog_data($event)
	{
		if (!$this->config['ub_enable'])
		{
			return;
		}

		/**
		* @var	string	sql_select		The SQL SELECT string used by search to get topic data
		* @var	string	sql_from		The SQL FROM string used by search to get topic data
		* @var	string	sql_where		The SQL WHERE string used by search to get topic data
		* @var	int		total_match_count	The total number of search matches
		* @var	array	sort_by_sql		Array of SQL sorting instructions
		* @var	string	sort_dir		The sorting direction
		* @var	string	sort_key		The sorting key
		* @var	string	sql_order_by	The SQL ORDER BY string used by search to get topic data
		*/
	}

	/**
	 * Gets the blog count for a user
	 * 
	 * @param int $author_id - The user ID
	 * @return int - The blog count
	 */
	private function get_blog_count(int $author_id): int
	{
		$sql = 'SELECT COUNT(blog_id) as blog_count
				FROM ' . $this->ub_blogs_table . '
				WHERE author_id = ' . (int) $author_id . '
				AND blog_approved = 1';
		$result = $this->db->sql_query($sql, 90);
		$blog_count = $this->db->sql_fetchfield('blog_count');
		$this->db->sql_freeresult($result);
		return (int) $blog_count;
	} 

	/**
	 * Gets the comment count for a user
	 * 
	 * @param int $commenter_id - The user ID
	 * @return int - The comment count
	 */
	private function get_comment_count(int $commenter_id)
	{
		$sql = 'SELECT COUNT(c.comment_id) as comment_count
				FROM ' . $this->ub_comments_table . ' c
				JOIN ' . $this->ub_blogs_table . ' b ON c.blog_id = b.blog_id AND b.author_id != c.user_id
				WHERE user_id = ' . (int) $commenter_id . '
				AND comment_approved = 1
				AND parent_id = 0';
		$result = $this->db->sql_query($sql, 60);
		$comment_count = $this->db->sql_fetchfield('comment_count');
		$this->db->sql_freeresult($result);
		return (int) $comment_count;
	}
}
