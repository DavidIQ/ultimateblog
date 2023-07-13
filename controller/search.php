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

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth					$auth
	* @param \phpbb\config\config				$config
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\language\language			$lang
	* @param \phpbb\pagination					$pagination
	* @param \phpbb\request\request				$request
	* @param \phpbb\template\template			$template
	* @param \phpbb\user						$user
	* @param string 							$ub_blogs_table
	* @param string								$ub_categories_table
	* @param string								$ub_blog_category_table
	* @param string								$ub_comments_table
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $lang, \phpbb\pagination $pagination, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->auth			= $auth;
		$this->config		= $config;
		$this->db			= $db;
		$this->lang			= $lang;
		$this->pagination	= $pagination;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
	}

	public function handle()
	{
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

		# Check if user is logged in
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			login_box('', $this->lang->lang['LOGIN_EXPLAIN_EGOSEARCH']);
		}

		# Is user able to search? Has search been disabled?
		if (!$this->auth->acl_get('u_search') || !$this->config['load_search'])
		{
			trigger_error('NO_SEARCH');
		}

		# Check search load limit
		if ($this->user->load && $this->config['limit_search_load'] && ($this->user->load > doubleval($this->config['limit_search_load'])))
		{
			trigger_error('NO_SEARCH_LOAD');
		}

		# Set up keywords

		# Set up author ID's
		if ($author)
		{
			if ((strpos($author, '*') !== false) && (utf8_strlen(str_replace(array('*', '%'), '', $author)) < $this->config['min_search_author_chars']))
			{
				trigger_error($this->lang->lang('TOO_FEW_AUTHOR_CHARS', (int) $this->config['min_search_author_chars']));
			}
			$sql_where = (strpos($author, '*') !== false) ? ' username_clean ' . $this->db->sql_like_expression(str_replace('*', $this->db->get_any_char(), utf8_clean_string($author))) : " username_clean = '" . $this->db->sql_escape(utf8_clean_string($author)) . "'";
			$sql = 'SELECT user_id
				FROM ' . USERS_TABLE . "
				WHERE $sql_where
					AND user_type <> " . USER_IGNORE;
			$result = $this->db->sql_query_limit($sql, 100);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$author_id_ary[] = (int) $row['user_id'];
			}
			$this->db->sql_freeresult($result);

			if (!count($author_id_ary))
			{
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
}
