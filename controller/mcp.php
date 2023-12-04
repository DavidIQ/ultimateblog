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
 * Class mcp
 *
 * @package mrgoldy\ultimateblog\controller
 */
class mcp
{
	/** @var string */
	protected $u_action;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \mrgoldy\ultimateblog\core\functions */
	protected $func;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

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

	/** @var string Ultimate Blog comments table */
	protected $ub_comments_table;

	/** @var string Ultimate Blog reports table */
	protected $ub_reports_table;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth						$auth					Authentication object
	* @param \phpbb\config\config					$config					Config object
	* @param \phpbb\db\driver\driver_interface		$db						Database object
	* @param \mrgoldy\ultimateblog\core\functions	$func					Ultimate Blog functions
	* @param \phpbb\controller\helper				$helper					Controller helper object
	* @param \phpbb\language\language				$lang					Language object
	* @param \phpbb\log\log							$log					Log object
	* @param \phpbb\notification\manager			$notification_manager	Notification manager
	* @param \phpbb\pagination						$pagination				Pagination object
	* @param \phpbb\textformatter\s9e\renderer		$renderer				Renderer object
	* @param \phpbb\request\request					$request				Request object
	* @param \phpbb\template\template				$template				Template object
	* @param \phpbb\user							$user					User object
	* @param string									$php_ext				phpEx
	* @param string									$phpbb_root_path		phpBB root path
	* @param string									$ub_blogs_table			Ultimate Blog blogs table
	* @param string									$ub_comments_table		Ultimate Blog comments table
	* @param string									$ub_reports_table		Ultimate Blog reports table
	* @access public
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $func, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\log\log $log, \phpbb\notification\manager $notification_manager, \phpbb\pagination $pagination, \phpbb\textformatter\s9e\renderer $renderer, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $php_ext, $phpbb_root_path, $ub_blogs_table, $ub_comments_table, $ub_reports_table)
	{
		$this->auth					= $auth;
		$this->config				= $config;
		$this->db					= $db;
		$this->func					= $func;
		$this->helper				= $helper;
		$this->lang					= $lang;
		$this->log					= $log;
		$this->notification_manager = $notification_manager;
		$this->pagination			= $pagination;
		$this->renderer				= $renderer;
		$this->request				= $request;
		$this->template				= $template;
		$this->user					= $user;
		$this->php_ext				= $php_ext;
		$this->phpbb_root_path		= $phpbb_root_path;
		$this->ub_blogs_table		= $ub_blogs_table;
		$this->ub_comments_table	= $ub_comments_table;
		$this->ub_reports_table		= $ub_reports_table;
	}

	/**
	 * @param $id
	 * @param $mode
	 * @param $action
	 * @param $start
	 * @param $sort_time
	 * @param $sort_by
	 * @param $sort_dir
	 */
	public function blog_reports_open($id, $mode, $action, $start, $sort_time, $sort_by, $sort_dir)
	{
		$error = '';

		# Get report list (type: blog, status: open [0])
		$this->get_report_list('blog', 0, $start, $sort_time, $sort_by, $sort_dir);

		# Delete or close was pressed.
		if ($action)
		{
			switch ($action)
			{
				case 'delete':
				case 'close';
					# Send it off to be dealt with
					$this->update_reports($id, $mode, $this->request->variable('report_id_list', array(0)), $action, 'blog');
				break;
			}
		}

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($error),
			'ERROR_MSG'	=> $error,

			'MCP_UB_REPORTS_EXPLAIN'	=> $this->lang->lang('MCP_UB_BLOG_REPORTS_OPEN_EXPLAIN'),
			'MCP_UB_REPORTS_TITLE'		=> $this->lang->lang('MCP_UB_BLOG_REPORTS_OPEN'),

			'S_COMMENT_REPORT'		=> false,
			'S_MCP_ACTION'			=> $this->u_action,
			'S_REPORTS_CLOSED'		=> false,
		));
	}

	/**
	 * @param $id
	 * @param $mode
	 * @param $action
	 * @param $start
	 * @param $sort_time
	 * @param $sort_by
	 * @param $sort_dir
	 */
	public function blog_reports_closed($id, $mode, $action, $start, $sort_time, $sort_by, $sort_dir)
	{
		$error = '';

		# Get report list (type: blog, status: closed [1], $start)
		$this->get_report_list('blog', 1, $start, $sort_time, $sort_by, $sort_dir);

		# Delete was pressed.
		if ($action)
		{
			switch ($action)
			{
				case 'delete':
					# Send it off to be dealt with
					$this->update_reports($id, $mode, $this->request->variable('report_id_list', array(0)), $action, 'blog');
				break;
			}
		}

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($error),
			'ERROR_MSG'	=> $error,

			'MCP_UB_REPORTS_EXPLAIN'	=> $this->lang->lang('MCP_UB_BLOG_REPORTS_CLOSED_EXPLAIN'),
			'MCP_UB_REPORTS_TITLE'		=> $this->lang->lang('MCP_UB_BLOG_REPORTS_CLOSED'),

			'S_COMMENT_REPORT'		=> false,
			'S_REPORTS_CLOSED'		=> true,
		));
	}

	/**
	 * @param $id
	 * @param $mode
	 * @param $action
	 * @param $start
	 * @param $sort_time
	 * @param $sort_by
	 * @param $sort_dir
	 */
	public function comment_reports_open($id, $mode, $action, $start, $sort_time, $sort_by, $sort_dir)
	{
		$error = '';

		# Get report list (type: comment, status: open [0])
		$this->get_report_list('comment', 0, $start, $sort_time, $sort_by, $sort_dir);

		# Delete or close was pressed.
		if ($action)
		{
			switch ($action)
			{
				case 'delete':
				case 'close';
					# Send it off to be dealt with
					$this->update_reports($id, $mode, $this->request->variable('report_id_list', array(0)), $action, 'comment');
				break;
			}
		}

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($error),
			'ERROR_MSG'	=> $error,

			'MCP_UB_REPORTS_EXPLAIN'	=> $this->lang->lang('MCP_UB_COMMENT_REPORTS_OPEN_EXPLAIN'),
			'MCP_UB_REPORTS_TITLE'		=> $this->lang->lang('MCP_UB_COMMENT_REPORTS_OPEN'),

			'S_COMMENT_REPORT'		=> true,
			'S_REPORTS_CLOSED'		=> false,
		));
	}

	/**
	 * @param $id
	 * @param $mode
	 * @param $action
	 * @param $start
	 * @param $sort_time
	 * @param $sort_by
	 * @param $sort_dir
	 */
	public function comment_reports_closed($id, $mode, $action, $start, $sort_time, $sort_by, $sort_dir)
	{
		$error = '';

		# Get report list (type: comment, status: closed [1])
		$this->get_report_list('comment', 1, $start, $sort_time, $sort_by, $sort_dir);

		# Delete or close was pressed.
		if ($action)
		{
			switch ($action)
			{
				case 'delete':
					# Send it off to be dealt with
					$this->update_reports($id, $mode, $this->request->variable('report_id_list', array(0)), $action, 'comment');
				break;
			}
		}

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($error),
			'ERROR_MSG'	=> $error,

			'MCP_UB_REPORTS_EXPLAIN'	=> $this->lang->lang('MCP_UB_COMMENT_REPORTS_CLOSED_EXPLAIN'),
			'MCP_UB_REPORTS_TITLE'		=> $this->lang->lang('MCP_UB_COMMENT_REPORTS_CLOSED'),

			'S_COMMENT_REPORT'		=> true,
			'S_REPORTS_CLOSED'		=> true,
		));
	}

	/**
	 * @param $id
	 * @param $mode
	 * @param $action
	 */
	public function reports_details($id, $mode, $action)
	{
		$type = $mode === 'ub_blog_reports_details' ? 'blog' : 'comment';
		$blog_id = $this->request->variable('blog_id', 0);
		$comment_id = $this->request->variable('comment_id', 0);
		$report_id = $this->request->variable('report_id', 0);

		$sql_array = array(
			'SELECT'	=> 'r.report_id, r.report_time, r.report_text, r.report_closed, r.blog_id, b.blog_title, r.comment_id,rr.reason_title, r.user_id as reporter_user_id, u1.username as reporter_username, u2.user_colour as reporter_user_colour, u2.user_id as author_user_id, u2.username as author_username, u2.user_colour as author_user_colour, u2.user_ip as author_user_ip',
			'FROM'		=> array(
				$this->ub_reports_table => 'r',
				$this->ub_blogs_table => 'b',
				REPORTS_REASONS_TABLE => 'rr',
				USERS_TABLE => 'u1',
			),

			'WHERE'		=> 'r.user_id = u1.user_id
							AND r.blog_id = b.blog_id
							AND r.reason_id = rr.reason_id
							AND r.report_closed = 0',
		);

		$sql_array['WHERE'] .= !empty($report_id) ? ' AND r.report_id = ' . (int) $report_id : (!empty($blog_id) ? ' AND r.comment_id = 0 AND r.blog_id = ' . (int) $blog_id : ' AND r.comment_id = ' . (int) $comment_id);

		switch ($type)
		{
			case 'blog':
				$sql_array['SELECT'] .= ', b.blog_date as post_time';
				$sql_array['LEFT_JOIN']	=	array(array(
												'FROM' => array(USERS_TABLE => 'u2'),
												'ON'	=> 'b.author_id = u2.user_id',
											));
			break;

			case 'comment':
				$sql_array['SELECT'] .= ', c.comment_time as post_time, c.parent_id';
				$sql_array['LEFT_JOIN']	= array(
											array(
												'FROM'	=> array($this->ub_comments_table => 'c'),
												'ON'	=> 'r.comment_id = c.comment_id'
											),
											array(
												'FROM' => array(USERS_TABLE => 'u2'),
												'ON'	=> 'c.user_id = u2.user_id',
											),
										);
			break;
		}

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$report = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		# Check if report ID excists
		if (!$report)
		{
			trigger_error($this->lang->lang('NO_REPORT_SELECTED'));
		}

		# Set report_id if coming from a blog id or comment id
		$report_id = $report['report_id'];

		# Delete or close was pressed.
		if ($action)
		{
			switch ($action)
			{
				case 'close':
				case 'delete':
					# Send it off to be dealt with
					$this->update_reports($id, $mode, $this->request->variable('report_id_list', array(0)), $action, $type);
				break;
			}
		}

		# Get blog information
		$sql = 'SELECT b.blog_title, b.blog_date, b.blog_text, b.enable_bbcode, b.enable_smilies, b.enable_magic_url, u.user_id, u.username, u.user_colour
				FROM ' . $this->ub_blogs_table . ' b
				JOIN ' . USERS_TABLE . ' u
				WHERE b.author_id = u.user_id
					AND b.blog_id = ' . (int) $report['blog_id'];
		$result = $this->db->sql_query($sql);
		$blog = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		# Get comment information
		if ($type === 'comment')
		{
			# Set up array for parent comment
			$sql_array = array(
				'SELECT'	=> 'c.comment_id, c.comment_time, c.comment_text, c.comment_approved, c.comment_reported, u.user_id, u.username, u.user_colour',
				'FROM'		=> array($this->ub_comments_table => 'c',
									USERS_TABLE => 'u'),
				'WHERE'		=> 'c.user_id = u.user_id AND c.comment_id = ',
			);
			$sql_array['WHERE'] .= $report['parent_id'] == 0 ? (int) $report['comment_id'] : (int) $report['parent_id'];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$parent = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$this->template->assign_vars(array(
				'PARENT_AUTHOR'	=> get_username_string('full', $parent['user_id'], $parent['username'], $parent['user_colour']),
				'PARENT_TEXT'	=> $this->renderer->render($parent['comment_text']),
				'PARENT_TIME'	=> $this->user->format_date($parent['comment_time']),

				'S_PARENT_APPROVED'	=> $parent['comment_approved'],
				'S_PARENT_REPORTED'	=> $parent['comment_reported'],
			));

			# And get all replies
			$sql_array['WHERE'] = 'c.user_id = u.user_id AND c.parent_id = ';
			$sql_array['WHERE'] .= $report['parent_id'] == 0 ? $report['comment_id'] : $report['parent_id'];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			while ($child = $this->db->sql_fetchrow($result))
			{
				# Set up all children
				$this->template->assign_block_vars('replies', array(
					'AUTHOR'	=> get_username_string('full', $child['user_id'], $child['username'], $child['user_colour']),
					'TEXT'		=> $this->renderer->render($child['comment_text']),
					'TIME'		=> $this->user->format_date($child['comment_time']),

					'S_APPROVED'	=> $child['comment_approved'],
					'S_REPORTED'	=> $child['comment_reported'],
				));
			}
			$this->db->sql_freeresult($result);
		}

		$this->template->assign_vars(array(
			'BLOG_AUTHOR'		=> get_username_string('full', $blog['user_id'], $blog['username'], $blog['user_colour']),
			'BLOG_TEXT'			=> $blog['blog_text'],
			'BLOG_TIME'			=> $this->user->format_date($blog['blog_date']),
			'BLOG_TITLE'		=> $report['blog_title'],

			'REPORT_ID'			=> $report['report_id'],
			'REPORT_REASON'		=> $report['reason_title'],
			'REPORT_TEXT'		=> $report['report_text'],
			'REPORT_TIME'		=> $this->user->format_date($report['report_time']),
			'REPORTED_POST_AUTHOR'	=> get_username_string('full', $report['author_user_id'], $report['author_username'], $report['author_user_colour']),
			'REPORTED_POST_IP'		=> $report['author_user_ip'],
			'REPORTED_POST_IPADDR'	=> ($this->auth->acl_getf_global('m_info') && $this->request->variable('lookup', '')) ? @gethostbyaddr($report['author_user_ip']) : '',
			'REPORTED_POST_TEXT'	=> $type == 'blog' ? $this->renderer->render($blog['blog_text']) : $this->renderer->render($parent['comment_text']),
			'REPORTED_POST_TIME'	=> $this->user->format_date($report['post_time']),
			'REPORTER'				=> get_username_string('full', $report['reporter_user_id'], $report['reporter_username'], $report['reporter_user_colour']),

			'MCP_UB_REPORTS_DETAILS_TITLE'	=> $this->lang->lang('MCP_UB_BLOG_REPORTS_DETAILS'),
			'VIEW_ULTIMATEBLOG'				=> $this->lang->lang('MCP_UB_VIEW_ULTIMATEBLOG', $this->config['ub_title']),

			'S_IS_COMMENT'		=> $type === 'comment' ? true : false,
			'S_CAN_APPROVE'		=> $this->auth->acl_get('m_ub_approve'),
			'S_CAN_BLOG_DELETE'	=> $this->auth->acl_get('m_ub_delete'),
			'S_CAN_BLOG_EDIT'	=> $this->auth->acl_get('m_ub_edit'),
			'S_CAN_VIEWIP'		=> $this->auth->acl_getf_global('m_info'),
			'S_REPORT_CLOSED'	=> $report['report_closed'],

			'U_BLOG_DELETE'		=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'delete', 'blog_id' => (int) $report['blog_id'])),
			'U_BLOG_EDIT'		=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'edit', 'blog_id' => (int) $report['blog_id'])),
			'U_LOOKUP_IP'		=> $this->auth->acl_getf_global('m_info') ? $this->u_action . '&amp;report_id=' . $report_id . '&amp;lookup=' . $report['author_user_ip'] . '#ip' : '',
			'U_VIEW_BLOG'		=> $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $report['blog_id'], 'title' => urlencode($report['blog_title']))),
			'U_VIEW_COMMENT'	=> $type === 'comment' ? $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $report['blog_id'])) . '#' . (int) $report['comment_id'] : '',
			'U_VIEW_UB'			=> $this->helper->route('mrgoldy_ultimateblog_index'),
		));
	}

	public function unapproved_blogs()
	{
		$error = [];

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($error),
			'ERROR_MSG'	=> $error,

			'MCP_UB_REPORTS_TITLE'		=> $this->lang->lang('MCP_UB_BLOG_QUEUE'),

			'S_MCP_ACTION'			=> $this->u_action,
		));
	}

	public function unapproved_comments()
	{
		$error = [];

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($error),
			'ERROR_MSG'	=> $error,

			'MCP_UB_REPORTS_TITLE'		=> $this->lang->lang('MCP_UB_COMMENT_QUEUE'),

			'S_MCP_ACTION'			=> $this->u_action,
		));
	}

	/**
	 * @param		$type
	 * @param		$status
	 * @param		$start
	 * @param int	$sort_time
	 * @param string $sort_by
	 * @param string $sort_dir
	 */
	private function get_report_list($type, $status, $start, $sort_time = 0, $sort_by = '', $sort_dir = '')
	{
		# Set up a report list
		$sql_array = array(
			'SELECT'	=> 'r.report_id, r.blog_id, r.comment_id, r.report_time as report_time, b.blog_title, r.user_id as reporter_user_id, u1.username as reporter_username, u1.user_colour as reporter_user_colour, u2.user_id as author_user_id, u2.username as author_username, u2.user_colour as author_user_colour',

			'FROM'		=> array(
				$this->ub_reports_table => 'r',
				$this->ub_blogs_table => 'b',
				USERS_TABLE => 'u1',
			),

			'WHERE'		=> 'r.user_id = u1.user_id
							AND r.blog_id = b.blog_id
							AND r.report_closed = ' . (int) $status,
		);
		switch ($type)
		{
			case 'blog':
				$sql_array['SELECT'] .= ', b.blog_date as post_time';
				$sql_array['LEFT_JOIN']	=	array(array(
												'FROM' => array(USERS_TABLE => 'u2'),
												'ON'	=> 'b.author_id = u2.user_id',
											));
				$sql_array['WHERE'] .= ' AND r.comment_id = 0';
			break;

			case 'comment':
				$sql_array['SELECT'] .= ', c.comment_time as post_time';
				$sql_array['LEFT_JOIN']	= array(
											array(
												'FROM'	=> array($this->ub_comments_table => 'c'),
												'ON'	=> 'r.comment_id = c.comment_id'
											),
											array(
												'FROM' => array(USERS_TABLE => 'u2'),
												'ON'	=> 'c.user_id = u2.user_id',
											),
										);
				$sql_array['WHERE'] .= ' AND r.comment_id != 0';
			break;
		}
		$sql_array['WHERE'] .= $this->auth->acl_get('m_ub_view_friends_only') ? '' : ' AND b.friends_only = 0';
		$sql_array['WHERE'] .= !empty($sort_time) ? ' AND r.report_time > ' . (time() - $sort_time) : '';
		$sql_array['ORDER_BY'] = !empty($sort_by) ? $sort_by : 'report_time';
		$sql_array['ORDER_BY'] .= !empty($sort_dir) ? ' ' . $sort_dir : ' DESC';

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $this->config['topics_per_page'], $start);
		$reports['rowset'] = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		# Now we run the query again to get the total rows...
		# the query is identical except we count the rows instead
		$sql_array['SELECT'] = 'COUNT(r.report_id) as report_count';
		$sql_array['ORDER_BY'] = '';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$reports['count'] = (int) $this->db->sql_fetchfield('report_count');
		$this->db->sql_freeresult($result);

		# Setup the pagination template variable
		$base_url = $this->u_action . "&amp;ubst=$sort_time&amp;ubsb=$sort_by&amp;ubsd=$sort_dir";
		$this->pagination->generate_template_pagination($base_url, 'pagination', 'start', $reports['count'], $this->config['topics_per_page'], $start);

		$this->template->assign_vars(array(
			'TOTAL_REPORTS'			=> $this->lang->lang('LIST_REPORTS', (int) $reports['count']),

			'S_MCP_ACTION'			=> $this->u_action,
		));

		# Send the reports rowset off for display
		$this->setup_report_template_block($reports['rowset'], $type);
	}

	/**
	 * @param $reports_rowset
	 * @param $type
	 */
	private function setup_report_template_block($reports_rowset, $type)
	{
		if (!empty($reports_rowset))
		{
			foreach ($reports_rowset as $report)
			{
				$this->template->assign_block_vars('reports', array(
					'AUTHOR'		=> get_username_string('full', $report['author_user_id'], $report['author_username'], $report['author_user_colour']),
					'AUTHOR_TIME'	=> $this->user->format_date($report['post_time']),
					'BLOG_TITLE'	=> $report['blog_title'],
					'REPORTER'		=> get_username_string('full', $report['reporter_user_id'], $report['reporter_username'], $report['reporter_user_colour']),
					'REPORT_ID'		=> $report['report_id'],
					'REPORT_TIME'	=> $this->user->format_date($report['report_time']),

					'U_VIEW_BLOG'			=> $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $report['blog_id'], 'title' => urlencode($report['blog_title']))),
					'U_VIEW_DETAILS'		=> append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}?i=-mrgoldy-ultimateblog-mcp-report_module&amp;mode=ub_{$type}_reports_details&amp;report_id={$report['report_id']}"),
				));
			}
		}
	}

	/**
	 * @param $id
	 * @param $mode
	 * @param $report_id_list
	 * @param $action
	 * @param $type
	 */
	private function update_reports($id, $mode, $report_id_list, $action, $type)
	{
		# Set up redirect url
		if ($mode === 'ub_blog_reports_details' || $mode === 'ub_comment_reports_details')
		{
			switch ($action)
			{
				case 'close':
					$redirect_url = $this->u_action . '&amp;report_id=' . (int) $report_id_list[0];
				break;

				case 'delete':
					$redirect_url = append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}?i=-mrgoldy-ultimateblog-mcp-report_module&amp;mode=ub_{$type}_reports_open");
				break;
			}
		}
		else
		{
			$redirect_url = $this->u_action;
		}

		if (empty($report_id_list))
		{
			trigger_error($this->lang->lang('MCP_UB_REPORTS_IDS_EMPTY') . '<br><br>' . $this->lang->lang('RETURN_REPORTS', '<a href="'. $redirect_url . '">', '</a>'));
		}

		$success_msg = '';
		$users_to_notify = array();

		$s_hidden_fields = build_hidden_fields(array(
			'i'					=> $id,
			'mode'				=> $mode,
			'report_id_list'	=> $report_id_list,
			'action'			=> $action)
		);

		if (confirm_box(true))
		{
			# Retrieve ID's to adjust
			switch ($type)
			{
				case 'blog':
					$sql = 'SELECT report_closed, blog_id, user_notify, user_id FROM ' . $this->ub_reports_table . ' WHERE ' . $this->db->sql_in_set('report_id', $report_id_list);
				break;

				case 'comment':
					$sql = 'SELECT report_closed, blog_id, comment_id, user_notify, user_id FROM ' . $this->ub_reports_table . ' WHERE ' . $this->db->sql_in_set('report_id', $report_id_list);
				break;
			}

			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$adjust_id_list[] = $type === 'blog' ? (int) $row['blog_id'] : (int) $row['comment_id'];

				# Only notify when user has requested it, and the report is not already closed.
				if (!empty($row['user_notify']) && empty($row['report_closed']))
				{
					$users_to_notify[] = array(
						'user_id'		=> (int) $row['user_id'],
						'blog_id'		=> (int) $row['blog_id'],
						'comment_id'	=> $type === 'blog' ? 0 : (int) $row['comment_id'],
					);
				}
			}
			$this->db->sql_freeresult($result);

			switch ($action)
			{
				case 'close':
					$sql = 'UPDATE ' . $this->ub_reports_table . ' SET report_closed = 1 WHERE ' . $this->db->sql_in_set('report_id', $report_id_list);
				break;

				case 'delete':
					$sql = 'DELETE FROM ' . $this->ub_reports_table . ' WHERE ' . $this->db->sql_in_set('report_id', $report_id_list);
				break;
			}
			$this->db->sql_query($sql);

			# Unmark the blog/comment as reported
			$sql = 'UPDATE';
			$sql .= $type === 'blog' ? ' ' . $this->ub_blogs_table . ' SET blog_reported = 0 ' : ' ' . $this->ub_comments_table . ' SET comment_reported = 0 ';
			$sql .= $type === 'blog' ? ' WHERE ' . $this->db->sql_in_set('blog_id', $adjust_id_list) : ' WHERE ' . $this->db->sql_in_set('comment_id', $adjust_id_list);
			$this->db->sql_query($sql);

			# Add it to the log
			$this->log->add('mod', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_REPORT_'. strtoupper($action) . 'D', time(), array());

			# Send notifaction ($users_to_notify)
			# If parent ID is NOT 0, it's a reply and we send a notification to the original comment author, if it's not the same author
			if (!empty($users_to_notify))
			{
				foreach ($users_to_notify as $user_to_notify)
				{
					# Increment our notifications sent counter
					$this->config->increment('ub_notification_id', 1);

					# Send out notification
					$this->notification_manager->add_notifications('mrgoldy.ultimateblog.notification.type.ultimateblog', array(
						'actionee_id'		=> (int) $this->user->data['user_id'],
						'author_id'			=> (int) $user_to_notify['user_id'],
						'blog_id'			=> (int) $user_to_notify['blog_id'],
						'blog_title'		=> '',
						'comment_id'		=> (int) $user_to_notify['comment_id'],
						'notification_id'	=> $this->config['ub_notification_id'],
						'notification_type'	=> 'report_' . $type,
					));
				}
			}

			# Show success message
			$success_msg = $this->lang->lang('MCP_UB_REPORTS_' . strtoupper($action) . 'D_SUCCESS') . '<br><br>' . $this->lang->lang('RETURN_REPORTS', '<a href="'. $redirect_url . '">', '</a>');
		}
		else
		{
			confirm_box(false, $this->lang->lang('MCP_UB_REPORTS_' . strtoupper($action) . '_CONFIRM', count($report_id_list)), $s_hidden_fields);
		}

		if (!$success_msg)
		{

			redirect($redirect_url);
		}
		else
		{
			meta_refresh(3, $redirect_url);
			trigger_error($success_msg);
		}

	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return void
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
