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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class feed
 *
 * @package mrgoldy\ultimateblog\controller
 */
class feed
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\feed\helper */
	protected $feed_helper;

	/** @var \phpbb\symfony_request */
	protected $request;

	/** @var \phpbb\template\twig\environment */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string .php Extension */
	protected $php_ext;

	/** @var string Ultimate Blog blogs table */
	protected $ub_blogs_table;

	/** @var string Ultimate Blog categories table */
	protected $ub_categories_table;

	/** @var string Ultimate Blog blog <> category corralation table */
	protected $ub_blog_category_table;

	/** @var string Ultimate Blog edits table */
	protected $ub_edits_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth						$auth					Authentication object
	 * @param \phpbb\config\config					$config					Config object
	 * @param \phpbb\controller\helper				$controller_helper		Controller helper object
	 * @param \phpbb\db\driver\driver_interface		$db						Database object
	 * @param \phpbb\feed\helper					$feed_helper			Feed helper object
	 * @param \phpbb\symfony_request				$request				Request object
	 * @param \phpbb\template\twig\environment		$twig					Template object
	 * @param \phpbb\user							$user					User object
	 * @param string								$php_ext				phpEx
	 * @param string								$ub_blogs_table			Ultimate Blog blogs table
	 * @param string								$ub_categories_table	Ultimate Blog categories table
	 * @param string								$ub_blog_category_table	Ultimate Blog blog <> category corralation table
	 * @param string								$ub_comments_table		Ultimate Blog comments table
	 * @param string								$ub_edits_table			Ultimate Blog edits table
	 * @param string								$ub_ratings_table		Ultimate Blog ratings table
	 * @access public
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $controller_helper, \phpbb\db\driver\driver_interface $db, \phpbb\feed\helper $feed_helper, \phpbb\symfony_request $request, \phpbb\template\twig\environment $twig, \phpbb\user $user, $php_ext, $ub_blogs_table, $ub_categories_table, $ub_blog_category_table, $ub_comments_table, $ub_edits_table, $ub_ratings_table)
	{
		$this->auth						= $auth;
		$this->config					= $config;
		$this->controller_helper		= $controller_helper;
		$this->db						= $db;
		$this->feed_helper				= $feed_helper;
		$this->request					= $request;
		$this->template					= $twig;
		$this->user						= $user;
		$this->php_ext					= $php_ext;
		$this->ub_blogs_table			= $ub_blogs_table;
		$this->ub_categories_table		= $ub_categories_table;
		$this->ub_blog_category_table	= $ub_blog_category_table;
		$this->ub_comments_table		= $ub_comments_table;
		$this->ub_edits_table			= $ub_edits_table;
		$this->ub_ratings_table			= $ub_ratings_table;
	}

	/**
	 * Handle the feed to output for various options:
	 * Latest blogs, latest blogs per category id, all categories
	 * @param $mode Feed mode: Latest | Category | Categories
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle($mode, $id)
	{
		# Check if the Ultimate Blog extension is enabled
		if (!$this->config['ub_enable'])
		{
			throw new http_exception(404, $this->user->lang('BLOG_ERROR_DISABLED'));
		}

		# Check for permission to read the Ultimate Blogs (before checking if the feed is enabled, otherwise they don't have to know that ..)
		if (!$this->auth->acl_get('u_ub_view') || !$this->auth->acl_get('u_ub_feed_view'))
		{
			throw new http_exception(403, $this->user->lang('BLOG_ERROR_CANT_VIEW'));
		}

		# Check if the feed is enabled
		if (!$this->config['ub_enable_feed'])
		{
			throw new http_exception(404, $this->user->lang('NO_FEED_ENABLED'));
		}

		# Select all the blogs
		$sql_array = array(
			'SELECT'	=> 'b.*, e.edit_time as last_edited, u.username, COUNT(distinct e.edit_id) as edit_count, COUNT(distinct c.comment_id) as comment_count, COUNT(distinct r.user_id) as rating_count, AVG(r.rating) as rating_average, GROUP_CONCAT(distinct bc.category_id) as categories',
			'FROM'		=> array($this->ub_blogs_table => 'b',
								USERS_TABLE => 'u',
							),
			'LEFT_JOIN'	=> array(
								array(
									'FROM'	=> array($this->ub_blog_category_table => 'bc'),
									'ON'	=> 'b.blog_id = bc.blog_id',
								),
								array(
									'FROM'	=> array($this->ub_comments_table => 'c'),
									'ON'	=> 'b.blog_id = c.blog_id',
								),
								array(
									'FROM'	=> array($this->ub_edits_table => 'e'),
									'ON'	=> 'b.blog_id = e.blog_id',
								),
								array(
									'FROM'	=> array($this->ub_ratings_table => 'r'),
									'ON'	=> 'b.blog_id = r.blog_id',
								),
								array(
									'FROM'	=> array(ZEBRA_TABLE => 'z'),
									'ON'	=> 'b.author_id = z.user_id
												AND z.friend = 1',
								),
							),
			'GROUP_BY'	=> 'b.blog_id, c.blog_id, e.blog_id, r.blog_id',
			'ORDER_BY'	=> 'b.blog_date DESC, e.edit_time DESC',
			'WHERE'		=> 'b.author_id = u.user_id,
							AND b.blog_approved = 1',
		);

		# Check for friends only
		if (!$this->auth->acl_get('m_ub_view_friends_only'))
		{
			$sql_array['WHERE'] .= ' AND ((b.friends_only = 1 AND (b.author_id = ' . (int) $this->user->data['user_id'] . ' OR z.zebra_id = ' . (int) $this->user->data['user_id'] . ')) OR b.friends_only = 0)';
		}

		# Add a specific category ID
		if ($mode === 'category' && $this->config['ub_enable_feed_cats'])
		{
			# Check if category id is valid
			$sql = 'SELECT category_name FROM ' . $this->ub_categories_table . ' WHERE category_id = ' . (int) $id;
			$result = $this->db->sql_query($sql);
			$category_exists = $this->db->sql_fetchfield('category_name');
			$this->db->sql_freeresult($result);

			if (empty($category_exists))
			{
				throw new http_exception(404, $this->user->lang('BLOG_ERROR_NO_CATEGORY'));
			}

			$sql_array['FROM'][$this->ub_blog_category_table] = 'bc2';
			$sql_array['WHERE'] .= ' AND b.blog_id = bc2.blog_id
									AND bc2.category_id = ' . (int) $id;
		}

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $this->config['ub_enable_feed_limit']);
		$feeds_rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		# Set up some variables
		$feed_updated_time = 0;
		$item_vars = $categories = array();
		$board_url = $this->feed_helper->get_board_url();

		# Set up a category list
		$sql = 'SELECT * FROM ' . $this->ub_categories_table . ' ORDER BY left_id';
		$result = $this->db->sql_query($sql);

		# Making indexing easier, putting category id as array key
		while ($row = $this->db->sql_fetchrow($result))
		{
			$categories[$row['category_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		# Iterate through blogs
		foreach ($feeds_rowset as $row)
		{
			# Set BBCode options
			$options = (($this->config['ub_allow_bbcodes']) ? OPTION_FLAG_BBCODE : 0) + (($this->config['ub_allow_smilies']) ? OPTION_FLAG_SMILIES : 0) + (($this->config['ub_allow_magic_url']) ? OPTION_FLAG_LINKS : 0);

			# Set up category lists.
			$category_count = 0;
			$category_name = $category_url = $statistics = '';
			$blog_categories = explode(',', $row['categories']);
			$blog_categories_count = count($blog_categories);
			$category_url = $board_url . '/app.' . $this->php_ext . '/blog' . '/';
			$category_url .= $blog_categories_count > 1 ? 'categories' : 'category/' . (int) $categories[$blog_categories[0]]['category_id'] . '/' . urlencode($categories[$blog_categories[0]]['category_name']);
			foreach ($blog_categories as $blog_category)
			{
				$category_count++;
				$category_name .= $categories[$blog_category]['category_name'];
				$category_name .= ($category_count != $blog_categories_count) ? ', ' : '';
			}

			# Set up statistics for this blog
			$comment_count = $row['comment_count'] !== null ? (int) $row['comment_count'] : 0;
			$statistics .= $this->user->lang('BLOG_CATEGORIES') . ' ' . $blog_categories_count;
			$statistics .= ' - ' . $this->user->lang('BLOG_EDIT_TOTAL') . ' ' . $row['edit_count'];
			$statistics .= ' - ' . $this->user->lang('VIEWED') . ' ' . $row['blog_views'];
			$statistics .= $this->config['ub_enable_comments'] ? ' - ' . $this->user->lang('BLOG_COMMENTS') . ' ' . $comment_count	: '';
			$statistics .= ($this->config['ub_enable_rating'] && ($row['rating_count'] !== null)) ? ' - ' . $this->user->lang('BLOG_RATING_AVG') . ' ' . round($row['rating_average'], 2) . ' - ' . $this->user->lang('BLOG_RATING_COUNT', (int) $row['rating_count']) : '';

			$published = ($row['blog_date'] !== null) ? (int) $row['blog_date'] : 0;
			$updated = ($row['last_edited'] !== null) ? (int) $row['last_edited'] : 0;
			$item_row = array(
				'author'		=> $row['username'],
				'published'		=> ($published > 0) ? $this->feed_helper->format_date($published) : '',
				'updated'		=> ($updated > 0) ? $this->feed_helper->format_date($updated) : '',
				'link'			=> $board_url . '/app.' . $this->php_ext . '/blog/view/' . $row['blog_id'] . '/' . urlencode($row['blog_title']),
				'title'			=> censor_text($row['blog_title']),
				'category'		=> !empty($category_url) ? $category_url : '',
				'category_name'	=> !empty($category_name) ? $category_name : '',
				'description'	=> censor_text($this->feed_helper->generate_content($row['blog_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $options, 0, array())),
				'statistics'	=> $this->config['ub_enable_feed_stats'] ? censor_text($this->feed_helper->generate_content($statistics, '', '', 0, 0, array())) : '',
			);

			$item_vars[] = $item_row;
			$feed_updated_time = max($feed_updated_time, $published, $updated);
		}

		# If we do not have any items at all, sending the current time is better than sending no time.
		if (!$feed_updated_time)
		{
			$feed_updated_time = time();
		}

		$content = $this->template->render('feed.xml.twig', array(
			# FEED_IMAGE is not used (atom)
			'FEED_IMAGE'			=> '',
			'SELF_LINK'				=> $this->controller_helper->route($this->request->attributes->get('_route'), $this->request->attributes->get('_route_params'), true, '', UrlGeneratorInterface::ABSOLUTE_URL),
			'FEED_LINK'				=> $board_url . '/index.' . $this->php_ext,
			'FEED_TITLE'			=> $this->config['sitename'],
			'FEED_SUBTITLE'			=> $this->config['site_desc'],
			'FEED_UPDATED'			=> $this->feed_helper->format_date($feed_updated_time),
			'FEED_LANG'				=> $this->user->lang['USER_LANG'],
			'FEED_AUTHOR'			=> $this->config['sitename'],

			# Feed entries
			'FEED_ROWS'				=> $item_vars,
		));

		$response = new Response($content);
		$response->headers->set('Content-Type', 'application/atom+xml');
		$response->setCharset('UTF-8');
		$response->setLastModified(new \DateTime('@' . $feed_updated_time));
		if (!empty($this->user->data['is_bot']))
		{
			// Let reverse proxies know we detected a bot.
			$response->headers->set('X-PHPBB-IS-BOT', 'yes');
		}

		return $response;
	}
}
