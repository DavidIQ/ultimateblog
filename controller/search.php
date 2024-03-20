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
		
		// Get the needed resultset now
		$sql_array['SELECT'] = 'b.blog_id, b.blog_title, b.blog_approved, u.user_id, u.username, u.user_colour, c.comment_id, c.comment_text, c.comment_time, c.comment_approved, c.comment_reported, c.bbcode_bitfield, c.bbcode_uid, c.bbcode_options, parent.comment_approved AS parent_approved';
		$sql = $this->db->sql_build_query('SELECT_DISTINCT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $this->config['ub_blogs_per_page'], $start);
		$comments = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		// Iterate over all the comments
		foreach ($comments as $comment)
		{
			$comment_text = self::get_search_result_text($comment['comment_text'], $comment['bbcode_uid']);

			// Assign the comment template block variables
			$this->template->assign_block_vars('comments', [
				'AUTHOR'	 => get_username_string('full', $comment['user_id'], $comment['username'], $comment['user_colour']),
				'ID'		 => $comment['comment_id'],
				'BLOG_TITLE' => $comment['blog_title'],
				'TEXT'		 => $comment_text,
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

	/**
	 * Get the search result text for displaying
	 * 
	 * @param string $text The text to display
	 * @param string $bbcode_uuid The bbcode UUID
	 * @param array $word_matches The word matches
	 * 
	 * @return string
	 */
	private function get_search_result_text(string $text, string $bbcode_uuid, array $word_matches = [])
	{
		$text = censor_text($text);
		$text = str_replace('[*:' . $bbcode_uuid . ']', '&sdot;&nbsp;', $text);
		strip_bbcode($text, $bbcode_uuid);
		$text = get_context($text, $word_matches, $this->config['default_search_return_chars']);
		$text = bbcode_nl2br($text);

		return $text;
	}
}
