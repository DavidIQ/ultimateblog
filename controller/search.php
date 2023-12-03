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
 * Class functions
 *
 * Ultimate Blog Search controller
 */
class search
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \mrgoldy\ultimateblog\core\functions */
	protected $functions;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth					$auth
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param \phpbb\language\language			$lang
	 * @param \phpbb\pagination					$pagination
	 * @param \phpbb\request\request			$request
	 * @param \phpbb\template\template			$template
	 * @param \phpbb\user						$user
	 * @param \mrgoldy\ultimateblog\core\functions	$functions
	 * @param \phpbb\controller\helper			$helper
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $lang, \phpbb\pagination $pagination, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \mrgoldy\ultimateblog\core\functions $functions, \phpbb\controller\helper $helper, \phpbb\textformatter\s9e\renderer $renderer)
	{
		$this->auth			= $auth;
		$this->config		= $config;
		$this->db			= $db;
		$this->lang			= $lang;
		$this->pagination	= $pagination;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->functions	= $functions;
		$this->helper		= $helper;
		$this->renderer		= $renderer;
	}

	public function handle()
	{
		$this->search_precheck();

		# Request some variables
		$start			= max($this->request->variable('start', 0), 0);
		$submit			= $this->request->variable('submit', false);
		$keywords		= $this->request->variable('k', '', true);
		$author			= $this->request->variable('a', '', true);
		$fields			= $this->request->variable('sf', 'all');
		$return_chars	= $this->request->variable('rc', 300);
		$search_cats	= $this->request->variable('cid', array(0));
		$sort_days		= $this->request->variable('st', 0);
		$sort_by		= $this->request->variable('sb', 't');
		$sort_dir		= $this->request->variable('sd', 'd');

		# Set up keywords

		# Set up author ID's
		if ($author) {
			if ((strpos($author, '*') !== false) && (utf8_strlen(str_replace(array('*', '%'), '', $author)) < $this->config['min_search_author_chars'])) {
				trigger_error($this->lang->lang('TOO_FEW_AUTHOR_CHARS', (int) $this->config['min_search_author_chars']));
			}
			$sql_where = (strpos($author, '*') !== false) ? ' username_clean ' . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), utf8_clean_string($author))) : " username_clean = '" . $this->db->sql_escape(utf8_clean_string($author)) . "'";
			$sql = 'SELECT user_id
				FROM ' . USERS_TABLE . "
				WHERE $sql_where
					AND user_type <> " . USER_IGNORE;
			$result = $this->db->sql_query_limit($sql, 100);
			while ($row = $this->db->sql_fetchrow($result)) {
				$author_id_ary[] = (int) $row['user_id'];
			}
			$this->db->sql_freeresult($result);

			if (!count($author_id_ary)) {
				trigger_error('NO_SEARCH_RESULTS');
			}
		}

		/*
		Search for: keywords / author
		Search in: categories
		Search within: all / blog titles / blogs / comments
		Sort result by: Author, Post time, Topic title, Category | ASC / DESC
		Limit results to: all | 1 day | 7 days | 2 weeks | 1 month | 3 months | 6 months | 1 year
		Return first X characters; 0, 25, 50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000
		 */
	}

	public function user_comments(int $user_id, int $page = null)
	{
		$this->search_precheck();

		// Request pagination start
		$start = $this->functions->set_start($page ?? 1);

		$sql_array = [
			'SELECT'	=> 'COUNT(DISTINCT c.comment_id) AS total',
			'FROM'		=> [$this->functions->ub_comments_table => 'c'],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [USERS_TABLE => 'u'],
					'ON'	=> 'u.user_id = c.user_id',
				],
				[
					'FROM'	=> [$this->functions->ub_blogs_table => 'b'],
					'ON'	=> 'b.blog_id = c.blog_id',
				],
				[
					'FROM'	=> [$this->functions->ub_comments_table => 'parent'],
					'ON'	=> 'parent.comment_id = c.comment_id = 0 OR parent.comment_id = c.parent_id',
				]
			],
			'WHERE'		=> 'c.user_id = ' . (int) $user_id,
			'ORDER_BY'	=> 'c.comment_time DESC',
		];

		if (!$this->auth->acl_get('m_ub_approve'))
		{
			$sql_array['WHERE'] .= ' AND b.blog_approved = 1 AND c.comment_approved = 1 AND parent.comment_approved = 1';
		}

		// Get the total count first
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$total = $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);

		if (!$total)
		{
			$this->lang->add_lang('search');
			trigger_error('NO_SEARCH_RESULTS');
		}

		$comments = [];
		$title_suffix = '';

		// Get the needed resultset now
		$sql_array['SELECT'] = 'b.blog_id, b.blog_title, b.blog_approved, u.user_id, u.username, u.user_colour, c.comment_id, c.comment_text, c.comment_time, c.comment_approved, c.comment_reported, c.bbcode_bitfield, c.bbcode_uid, c.bbcode_options, parent.comment_approved AS parent_approved';
		$sql = $this->db->sql_build_query('SELECT_DISTINCT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $this->config['ub_blogs_per_page'], $start);
		$comments = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		// Iterate over all the comments
		foreach ($comments as $comment)
		{
			// Assign the comment template block variables
			$this->template->assign_block_vars('comments', [
				'AUTHOR'	 => get_username_string('full', $comment['user_id'], $comment['username'], $comment['user_colour']),
				'ID'		 => $comment['comment_id'],
				'BLOG_TITLE' => $comment['blog_title'],
				'TEXT'		 => $this->renderer->render($comment['comment_text']),
				'TIME'		 => $this->user->format_date($comment['comment_time']),

				'S_IS_AUTHOR'		=> $this->user->data['user_id'] == $comment['user_id'],
				'S_IS_REPORTED'		=> $comment['comment_reported'] && $this->auth->acl_get('m_ub_report'),
				'S_IS_UNAPPROVED'	=> !$comment['comment_approved'],

				'U_COMMENT'		=> $this->helper->route('mrgoldy_ultimateblog_view', ['blog_id' => (int) $comment['blog_id'], 'title' => urlencode($comment['blog_title'])]) . '#' . (int) $comment['comment_id']
			]);
		}

		$this->pagination->generate_template_pagination(
			[
				'routes' => [
					'mrgoldy_ultimateblog_usercomments',
					'mrgoldy_ultimateblog_usercommentspage',
				],
				'params' => ['user_id' => (int) $user_id],
			], 'pagination', 'page', $total, $this->config['ub_blogs_per_page'], $start);

		$this->template->assign_vars([
			'PAGE_NUMBER'		=> $this->pagination->on_page($total, $this->config['ub_blogs_per_page'], $start),
			'TOTAL_COMMENTS'	=> $this->lang->lang('BLOG_COMMENTS_COUNT', (int) $total),
		]);

		return $this->helper->render('ub_search_user_comments.html', $this->config['ub_title'] . ' - ' . $this->lang->lang('BLOG_COMMENTS'));
	}

	/**
	 * Do some precheck validations before searching
	 * 
	 * @return void
	 */
	private function search_precheck()
	{
		// Check if user is logged in
		if ($this->user->data['user_id'] == ANONYMOUS) {
			login_box('', $this->lang->lang('LOGIN_EXPLAIN_EGOSEARCH'));
		}

		// Is user able to search? Has search been disabled?
		if (!$this->auth->acl_get('u_search') || !$this->config['load_search']) {
			trigger_error('NO_SEARCH');
		}

		// Check search load limit
		if ($this->user->load && $this->config['limit_search_load'] && ($this->user->load > doubleval($this->config['limit_search_load']))) {
			trigger_error('NO_SEARCH_LOAD');
		}
	}
}
