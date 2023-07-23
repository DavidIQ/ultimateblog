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
 * Class blog
 *
 * @package mrgoldy\ultimateblog\controller
 */
class blog
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

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

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

	/** @var string .php Extension */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth					 $auth
	 * @param \phpbb\config\config				 $config
	 * @param \mrgoldy\ultimateblog\core\functions $func
	 * @param \phpbb\controller\helper			 $helper
	 * @param \phpbb\language\language			 $lang
	 * @param \phpbb\notification\manager			$notification_manager
	 * @param \phpbb\path_helper					$path_helper
	 * @param \phpbb\textformatter\s9e\renderer	$renderer
	 * @param \phpbb\request\request				$request
	 * @param \phpbb\template\template			 $template
	 * @param \phpbb\user							$user
	 * @param										$php_ext
	 * @param										$phpbb_root_path
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, $func, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\notification\manager $notification_manager, \phpbb\path_helper $path_helper, \phpbb\textformatter\s9e\renderer $renderer, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $php_ext, $phpbb_root_path)
	{
		$this->auth					= $auth;
		$this->config				= $config;
		$this->func					= $func;
		$this->helper				= $helper;
		$this->lang					= $lang;
		$this->notification_manager	= $notification_manager;
		$this->path_helper			= $path_helper;
		$this->renderer				= $renderer;
		$this->request				= $request;
		$this->template				= $template;
		$this->user					= $user;
		$this->php_ext				= $php_ext;
		$this->phpbb_root_path		= $phpbb_root_path;
	}

	/**
	 * @param $blog_id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function display($blog_id)
	{
		# Check if Ultimate Blog is enabled and if the user has the 'view' permission
		$this->func->ub_status();

		# Increment blog views, only when not viewed this session
		$view_cookie = $this->request->variable($this->config['cookie_name'] . '_blog_viewed_' . (int) $blog_id, '', true, \phpbb\request\request_interface::COOKIE);

		# Get blog data
		$blog = $this->func->blog_data((int) $blog_id, (int) $this->user->data['user_id']);

		if (empty($view_cookie))
		{
			# Set cookie. Auto prefixed with cookie-prefix: COOKIE NAME | COOKIE DATA | 0 = COOKIE FOR THIS SESSIOn
			$this->user->set_cookie('blog_viewed_' . (int) $blog_id, 'viewed', 0);

			# Increment the blog views counter
			$view_multiplier = $this->func->increment_blog_views((int) $blog_id);

			# If view_multiplier is NOT 0, it means a (new) threshold is reached, so we send out a notification
			if (!empty($view_multiplier))
			{
				# Increment our notifications sent counter
				$this->config->increment('ub_notification_views_id', 1);

				# Send out notification
				$this->notification_manager->add_notifications('mrgoldy.ultimateblog.notification.type.views', array(
					'blog_id'			=> (int) $blog_id,
					'multiplier'		=> (int) $view_multiplier,
					'notification_id'	=> $this->config['ub_notification_views_id'],
					'author_id' 		=> (int) $blog['author_id'],
				));
			}
		}

		# Check if we have a blog for this ID.
		if (!$blog)
		{
			throw new http_exception(404, $this->lang->lang('BLOG_ERROR_NO_BLOG'));
		}

		# Check for friends only
		if ($blog['friends_only'] && (!in_array($this->user->data['user_id'], explode(',', $blog['friends'])) && !$this->auth->acl_get('m_ub_view_friends_only') && $this->user->data['user_id'] != $blog['author_id']))
		{
			throw new http_exception(403, $this->lang->lang('BLOG_ERROR_FRIENDS_ONLY'));
		}

		# Set up initial comment list
		$comments = $this->func->comment_list((int) $blog_id);
		$parents = $children = array();

		foreach ($comments as $comment)
		{
			if ($comment['parent_id'] == 0)
			{
				$parents[] = $comment;
			}
			else
			{
				$children[$comment['parent_id']][] = $comment;
			}
		}

		foreach ($parents as $parent)
		{
			$reply_count = !empty($children[$parent['comment_id']]) ? count($children[$parent['comment_id']]) : 0;

			$this->template->assign_block_vars('comments', array(
				'AUTHOR'		=> get_username_string('full', $parent['user_id'], $parent['username'], $parent['user_colour']),
				'AVATAR'		=> phpbb_get_user_avatar($parent),
				'ID'			=> $parent['comment_id'],
				'SHOW_REPLIES'	=> $this->lang->lang('BLOG_COMMENTS_SHOW_REPLIES', $reply_count),
				'TEXT'			=> $this->renderer->render($parent['comment_text']),
				'TIME'			=> $this->user->format_date($parent['comment_time']),

				'S_CAN_DELETE'	=> $this->auth->acl_get('m_ub_edit') || ($this->auth->acl_get('u_ub_comment_delete') && $this->user->data['user_id'] == $parent['user_id']) ? true : false,
				'S_CAN_EDIT'	=> $this->auth->acl_get('m_ub_delete') || ($this->auth->acl_get('u_ub_comment_edit') && $this->user->data['user_id'] == $parent['user_id']) ? true : false,
				'S_IS_AUTHOR'	=> $parent['user_id'] == $blog['author_id'] ? true : false,
				'S_REPORTED'	=> $parent['comment_reported'],
				'S_UNAPPROVED'	=> !$parent['comment_approved'],

				'U_DELETE'		=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'action' => 'delete', 'cid' => (int) $parent['comment_id'], 'aid' => (int) $parent['user_id'])),
				'U_EDIT'		=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'action' => 'edit', 'cid' => (int) $parent['comment_id'])),
				'U_REPLY'		=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'action' => 'add', 'pid' => (int) $parent['comment_id'], 'paid' => (int) $parent['user_id'])),
				'U_REPORT'		=> $this->helper->route('mrgoldy_ultimateblog_report', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'id' => (int) $parent['comment_id'])),
				'U_REPORT_VIEW'	=> append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}?i=-mrgoldy-ultimateblog-mcp-report_module&amp;mode=ub_comment_reports_details&amp;comment_id={$parent['comment_id']}"),
			));

			if (!empty($children[$parent['comment_id']]))
			{
				foreach ($children[$parent['comment_id']] as $child)
				{
					$this->template->assign_block_vars('comments.replies', array(
						'AVATAR'	=> phpbb_get_user_avatar($child),
						'ID'		=> $child['comment_id'],
						'AUTHOR'	=> get_username_string('full', $child['user_id'], $child['username'], $child['user_colour']),
						'TEXT'		=> $this->renderer->render($child['comment_text']),
						'TIME'		=> $this->user->format_date($child['comment_time']),

						'S_CAN_DELETE'	=> $this->auth->acl_get('m_ub_delete') || ($this->auth->acl_get('u_ub_comment_delete') && $this->user->data['user_id'] == $child['user_id']) ? true : false,
						'S_CAN_EDIT'	=> $this->auth->acl_get('m_ub_edit') || ($this->auth->acl_get('u_ub_comment_delete') && $this->user->data['user_id'] == $child['user_id']) ? true : false,
						'S_IS_AUTHOR'	=> $child['user_id'] == $blog['author_id'] ? true : false,
						'S_REPORTED'	=> $child['comment_reported'],
						'S_UNAPPROVED'	=> !$child['comment_approved'],

						'U_DELETE'		=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'action' => 'delete', 'aid' => (int) $child['user_id'], 'cid' => (int) $child['comment_id'])),
						'U_EDIT'		=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'action' => 'edit', 'cid' => (int) $child['comment_id'])),
						'U_REPORT'		=> $this->helper->route('mrgoldy_ultimateblog_report', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'id' => (int) $child['comment_id'])),
						'U_REPORT_VIEW'	=> append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}?i=-mrgoldy-ultimateblog-mcp-report_module&amp;mode=ub_comment_reports_details&amp;comment_id={$child['comment_id']}"),
					));
				}
			}
		}

		# Set up category and edit lists
		$category_ids = explode(',', $blog['categories']);
		$blog_categories = $this->func->category_list($category_ids);
		$edit_reasons = $this->func->edit_list((int) $blog_id);
		$last_modified = '';
		$edit_count = 0;

		# Set up categories template block variables
		foreach ($blog_categories as $blog_category)
		{
			$this->template->assign_block_vars('blog_categories', array(
				'CATEGORY_ID'	=> $blog_category['category_id'],
				'CATEGORY_NAME'	=> $blog_category['category_name'],
				'U_CATEGORY'	=> $this->helper->route('mrgoldy_ultimateblog_category', array('category_id' => (int) $blog_category['category_id'], 'title' => urlencode($blog_category['category_name']))),
			));
		}

		# Set up edit reasons template block variables
		if (!empty($edit_reasons))
		{
			foreach ($edit_reasons as $edit_reason)
			{
				# Set up last modified date, so only when count is still at 0, as edits are ordered by time DESC.
				if ($edit_count == 0)
				{
					$last_modified = $this->user->format_date($edit_reason['edit_time'], 'Y-m-d') . 'T' . $this->user->format_date($edit_reason['edit_time'], 'H:i:s') . 'Z';
				}

				# Increase count
				$edit_count++;

				$this->template->assign_block_vars('edit_reasons', array(
					'TEXT'			=> $edit_reason['edit_text'],
					'TIME'			=> $this->user->format_date($edit_reason['edit_time']),
					'USER'			=> get_username_string('full', $edit_reason['user_id'], $edit_reason['username'], $edit_reason['user_colour']),

					'U_EDIT_DELETE'	=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'editdelete', 'eid' => (int) $edit_reason['edit_id'])),
				));
			}
		}

		# Set up template variables
		$this->template->assign_vars(array(
			'AUTHOR_FULL'			=> get_username_string('full', $blog['user_id'], $blog['username'], $blog['user_colour']),
			'AUTHOR_NAME'			=> get_username_string('no_profile', $blog['user_id'], $blog['username'], $blog['user_colour']),
			'AUTHOR_URL'			=> get_username_string('profile', $blog['user_id'], $blog['username'], $blog['user_colour']),

			'BLOG_ID'					=> $blog['blog_id'],
			'BLOG_DATE'					=> $this->user->format_date($blog['blog_date']),
			'BLOG_DATE_META'			=> $this->user->format_date($blog['blog_date'], 'Y-m-d') . 'T' . $this->user->format_date($blog['blog_date'], 'H:i:s') . 'Z',
			'BLOG_DATE_SHORT'			=> $this->user->format_date($blog['blog_date'], 'j M Y'),
			'BLOG_DESCRIPTION'			=> $blog['blog_description'],
			'BLOG_EDITS'				=> $edit_count,
			'BLOG_EDIT_META'			=> $edit_count > 0 ? $last_modified : $this->user->format_date($blog['blog_date'], 'Y-m-d') . 'T' . $this->user->format_date($blog['blog_date'], 'H:i:s') . 'Z',
			'BLOG_IMAGE'				=> !empty($blog['blog_image']) ? $this->path_helper->get_web_root_path() . $this->config['ub_image_dir'] . '/' . $blog['blog_image'] : '',
			'BLOG_LOGO_META'			=> generate_board_url() . 'images/site_logo.gif',
			'BLOG_RATING'				=> isset($blog['blog_rating']) ? round($blog['blog_rating'], 2) : false,
			'BLOG_RATING_COUNT'			=> $blog['rating_count'],
			'BLOG_RATING_COUNT_TEXT'	=> $this->lang->lang('BLOG_RATING_COUNT', (int) $blog['rating_count']),
			'BLOG_TEXT'					=> $this->renderer->render($blog['blog_text']),
			'BLOG_TITLE'				=> $blog['blog_title'],
			'BLOG_USER_AVATAR'			=> phpbb_get_user_avatar($this->user->data),
			'BLOG_USER_RATING'			=> isset($blog['user_rating']) ? $blog['blog_rating'] : false,
			'BLOG_VIEWS'				=> $blog['blog_views'],
			'BLOG_VIEWS_TEXT'			=> $this->lang->lang('VIEWED_COUNTS', $blog['blog_views']),

			'COMMENTS_COUNT'			=> $blog['comment_count'] ? $blog['comment_count'] : 0,

			'S_BLOG_CAN_DELETE'			=> ($this->auth->acl_get('u_ub_delete') && $blog['user_id'] == $this->user->data['user_id']) || $this->auth->acl_get('m_ub_delete') ? true : false,
			'S_BLOG_CANDELETE_PERM'		=> $this->auth->acl_get('u_ub_delete'),
			'S_BLOG_CAN_EDIT_PERM'		=> $this->auth->acl_get('u_ub_edit'),
			'S_BLOG_CAN_EDIT_MOD'		=> $this->auth->acl_get('m_ub_edit'),
			'S_BLOG_CAN_EDIT_USER'		=> $this->auth->acl_get('u_ub_edit') && $blog['user_id'] == $this->user->data['user_id'] ? true : false,
			'S_BLOG_CAN_EDIT_VIEW'		=> $this->auth->acl_get('u_ub_edit_view'),
			'S_BLOG_CAN_EDIT_DELETE'	=> $this->auth->acl_get('m_ub_edit_delete'),
			'S_BLOG_CAN_REPORT'			=> $this->auth->acl_get('u_ub_report'),
			'S_BLOG_EDIT_LOCKED'		=> $blog['locked_edit'],
			'S_BLOG_MOD_APPROVE'		=> $this->auth->acl_get('m_ub_approve'),
			'S_BLOG_MOD_REPORT'			=> $this->auth->acl_get('m_ub_report'),
			'S_BLOG_RATING_CAN'			=> $this->auth->acl_get('u_ub_rate'),
			'S_BLOG_RATING_ENABLED'		=> $this->config['ub_enable_rating'],
			'S_BLOG_RATING_LOCKED'		=> $blog['locked_rating'],
			'S_BLOG_REPORTED'			=> $blog['blog_reported'],
			'S_BLOG_UNAPPROVED'			=> !$blog['blog_approved'],

			'S_COMMENTS_CAN_LOCK'	=> $this->auth->acl_get('m_ub_lock_comments'),
			'S_COMMENTS_CAN_POST'	=> $this->auth->acl_get('u_ub_comment_post'),
			'S_COMMENTS_CAN_VIEW'	=> $this->auth->acl_get('u_ub_comment_view'),
			'S_COMMENTS_ENABLED'	=> $this->config['ub_enable_comments'],
			'S_COMMENTS_LOCKED'		=> $blog['locked_comments'],
			'S_COMMENTS_PER_PAGE'	=> $this->config['ub_comments_per_page'],

			'S_ULTIMATEBLOG_ENABLED'	=> $this->config['ub_enable'],

			'U_BLOG_ARCHIVE'		=> $this->helper->route('mrgoldy_ultimateblog_archives'),
			'U_BLOG_CATEGORIES'		=> $this->helper->route('mrgoldy_ultimateblog_categories'),
			'U_BLOG_DELETE'			=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'delete', 'blog_id' => (int) $blog_id)),
			'U_BLOG_EDIT'			=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'edit', 'blog_id' => (int) $blog_id)),
			'U_BLOG_RATING'			=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'rate')),
			'U_BLOG_REPORT'			=> $this->helper->route('mrgoldy_ultimateblog_report', array('blog_id' => (int) $blog_id, 'mode' => 'blog', 'id' => (int) $blog_id)),
			'U_BLOG_VIEW_REPORT'	=> append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}?i=-mrgoldy-ultimateblog-mcp-report_module&amp;mode=ub_blog_reports_details&amp;blog_id={$blog_id}"),
			'U_BLOG_URL'			=> $this->helper->get_current_url(),

			'U_COMMENT_ADD'		=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'action' => 'add', 'pid' => (int) 0)),
		));

		# Breadcrumbs
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->config['ub_title'],
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_index'),
		));
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $blog['blog_title'],
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog['blog_id'], 'title' => urlencode($blog['blog_title']))),
		));

		return $this->helper->render('ub_blog_body.html', $this->config['ub_title'] . ' - ' . $blog['blog_title']);
	}
}
