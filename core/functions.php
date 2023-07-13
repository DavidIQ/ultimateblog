<?php
/**
*
* Ultimate Blog. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2017, Mr. Goldy
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace mrgoldy\ultimateblog\core;

use phpbb\exception\http_exception;
use mrgoldy\ultimateblog\constants;

/**
 * Class functions
 *
 * @package mrgoldy\ultimateblog\core
 */
class functions
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

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

	/** @var string Ultimate Blog blog <> category corralation table */
	protected $ub_blog_category_table;

	/** @var string Ultimate Blog comments table */
	protected $ub_comments_table;

	/** @var string Ultimate Blog edit reasons table */
	protected $ub_edits_table;

	/** @var string Ultimate Blog index table */
	protected $ub_index_table;

	/** @var string Ultimate Blog ratings table */
	protected $ub_ratings_table;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth					$auth
	* @param \phpbb\config\config				$config
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\template\template			$template
	* @param \phpbb\user						$user
	* @param string								$php_ext
	* @param string								$phpbb_root_path
	* @param string 							$ub_blogs_table
	* @param string								$ub_categories_table
	* @param string								$ub_blog_category_table
	* @param string								$ub_comments_table
	* @param string								$ub_edits_table
	* @param string								$ub_index_table
	* @param string								$ub_ratings_table
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, $php_ext, $phpbb_root_path, $ub_blogs_table, $ub_categories_table, $ub_blog_category_table, $ub_comments_table, $ub_edits_table, $ub_index_table, $ub_ratings_table)
	{
		$this->auth		= $auth;
		$this->config	= $config;
		$this->db		= $db;
		$this->template	= $template;
		$this->user		= $user;
		$this->php_ext	= $php_ext;
		$this->phpbb_root_path			= $phpbb_root_path;
		$this->ub_blogs_table			= $ub_blogs_table;
		$this->ub_categories_table		= $ub_categories_table;
		$this->ub_blog_category_table	= $ub_blog_category_table;
		$this->ub_comments_table		= $ub_comments_table;
		$this->ub_edits_table			= $ub_edits_table;
		$this->ub_index_table			= $ub_index_table;
		$this->ub_ratings_table			= $ub_ratings_table;
	}

	/**
	 *
	 */
	public function ub_status()
	{
		# Check if the Ultimate Blog extension is enabled
		if (!$this->config['ub_enable'])
		{
			throw new http_exception(404, $this->user->lang('BLOG_ERROR_DISABLED'));
		}

		# Check for permission to read the Ultimate Blogs
		if (!$this->auth->acl_get('u_ub_view'))
		{
			throw new http_exception(403, $this->user->lang('BLOG_ERROR_CANT_VIEW'));
		}

		$this->template->assign_var('U_MCP', ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? append_sid("{$this->phpbb_root_path}mcp.$this->php_ext", 'i=main&amp;mode=front', true, $this->user->session_id) : '');
	}

	/**
	 * @return array
	 */
	public function archive_list()
	{
		$sql = 'SELECT blog_date
				FROM ' . $this->ub_blogs_table . '
				GROUP BY MONTH(FROM_UNIXTIME(blog_date)), YEAR(FROM_UNIXTIME(blog_date)), blog_date
				ORDER BY blog_date DESC';
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rowset;
	}

	/**
	 * @param $blog
	 * @return int
	 */
	public function blog_update($blog)
	{
		if (empty($blog['blog_id']))
		{
			$sql = 'INSERT INTO ' . $this->ub_blogs_table . ' ' . $this->db->sql_build_array('INSERT', $blog);
		}
		else
		{
			$sql = 'UPDATE ' . $this->ub_blogs_table . ' SET ' . $this->db->sql_build_array('UPDATE', $blog) . ' WHERE blog_id = ' . (int) $blog['blog_id'];
		}
		$this->db->sql_query($sql);
		$blog_id = empty($blog['blog_id']) ? $this->db->sql_nextid() : $blog['blog_id'];

		return (int) $blog_id;
	}

	/**
	 * @param $blog_id
	 */
	public function blog_delete($blog_id)
	{
		$sql = 'DELETE FROM ' . $this->ub_blogs_table . '
				WHERE blog_id = ' . (int) $blog_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->ub_blog_category_table . '
				WHERE blog_id = ' . (int) $blog_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->ub_comments_table . '
				WHERE blog_id = ' . (int) $blog_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->ub_edits_table . '
				WHERE blog_id = ' . (int) $blog_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->ub_ratings_table . '
				WHERE blog_id = ' . (int) $blog_id;
		$this->db->sql_query($sql);
	}

	/**
	 * @param $blog_id
	 * @param $user_id
	 * @return array
	 */
	public function blog_data($blog_id, $user_id)
	{
		$sql_array = array(
			'SELECT'	=> 'b.*, u.user_id, u.username, u.user_colour, COUNT(distinct c.comment_id) as comment_count, COUNT(distinct br.user_id) as rating_count, AVG(br.rating) as blog_rating, ur.rating as user_rating, GROUP_CONCAT(bc.category_id) as categories, GROUP_CONCAT(distinct z.zebra_id) as friends',
			'FROM'		=> array($this->ub_blogs_table => 'b',
								USERS_TABLE => 'u',
							),
			'LEFT_JOIN'	=> array(
								array(
									'FROM'	=> array($this->ub_blog_category_table => 'bc'),
									'ON'	=> 'b.blog_id = bc.blog_id',
								),
								array(
									'FROM'	=> array($this->ub_ratings_table => 'br'),
									'ON'	=> 'b.blog_id = br.blog_id',
								),
								array(
									'FROM'	=> array($this->ub_ratings_table => 'ur'),
									'ON'	=> 'b.blog_id = ur.blog_id
												AND ur.user_id = ' . (int) $user_id,
								),
								array(
									'FROM'	=> array($this->ub_comments_table => 'c'),
									'ON'	=> 'b.blog_id = c.blog_id',
								),
								array(
									'FROM'	=> array(ZEBRA_TABLE => 'z'),
									'ON'	=> 'b.author_id = z.user_id
												AND z.friend = 1',
								),
							),

			'GROUP_BY'	=> 'ur.rating',
			'WHERE'		=> 'b.author_id = u.user_id
							AND b.blog_id = ' . (int) $blog_id,
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$blog_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $blog_data;
	}

	# mode: index	| category |	 archive	| user
	# data contains information depending on the mode: nothing - category id - month and year - user id (resp.)
	/**
	 * @param	 $mode
	 * @param int $limit
	 * @param int $start
	 * @param int $data
	 * @return array
	 */
	public function blog_list($mode, $limit, $start, $data = 0)
	{
		$sql_array = array(
			'SELECT'	=> 'b.blog_id, b.blog_title, b.blog_approved, b.blog_reported, b.blog_date, b.friends_only, b.blog_image, u.user_id, u.username, u.user_colour, GROUP_CONCAT(bc.category_id) as categories, GROUP_CONCAT(distinct z.user_id) as friends',

			'FROM'		=> array(
				$this->ub_blogs_table => 'b',
				USERS_TABLE => 'u'
			),

			'LEFT_JOIN' => array(
				array(
					'FROM'	=> array($this->ub_blog_category_table	=> 'bc'),
					'ON'	=> 'b.blog_id = bc.blog_id',
				),
				array(
					'FROM'	=> array(ZEBRA_TABLE => 'z'),
					'ON'	=> 'b.author_id = z.user_id
								AND z.friend = 1',
				),
			),

			'GROUP_BY'	=> 'b.blog_id',
			'ORDER_BY'	=> 'b.blog_date DESC',
			'WHERE'		=> 'b.author_id = u.user_id',
		);

		if ($mode == 'category')
		{
			$sql_array['FROM'][$this->ub_blog_category_table] = 'bc2';
			$sql_array['WHERE'] .= ' AND b.blog_id = bc2.blog_id
									AND bc2.category_id = ' . (int) $data;
		}
		else if ($mode == 'archive')
		{
			$sql_array['WHERE'] .= ' AND MONTH(FROM_UNIXTIME(b.blog_date)) = ' . (int) $data['month'] . '
									AND YEAR(FROM_UNIXTIME(b.blog_date)) = ' . (int) $data['year'];
		}
		else if ($mode == 'user')
		{
			$sql_array['WHERE'] .= ' AND b.author_id = ' . (int) $data;
		}

		$sql_array['WHERE'] .= !$this->auth->acl_get('m_ub_approve') ? ' AND (b.blog_approved = 1 OR b.author_id = ' . (int) $this->user->data['user_id'] . ')' : '';

		if (!$this->auth->acl_get('m_ub_view_friends_only'))
		{
			$sql_array['WHERE'] .= ' AND ((b.friends_only = 1 AND (b.author_id = ' . (int) $this->user->data['user_id'] . ' OR z.zebra_id = ' . (int) $this->user->data['user_id'] . ')) OR b.friends_only = 0)';
		}

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $limit, $start);
		$blog['list'] = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		# Now we run the query again to get the total rows...
		# the query is identical except we count the rows instead
		$sql_array['SELECT'] = 'COUNT(b.blog_id) as blog_count';
		$sql_array['GROUP_BY'] = '';
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$blog['count'] = (int) $this->db->sql_fetchfield('blog_count');
		$this->db->sql_freeresult($result);

		return $blog;
	}

	/**
	 * @return array
	 */
	public function blog_index()
	{
		$sql_array = array(
			'SELECT'	=> 'i.block_name, i.block_limit, i.block_data, c.category_name',
			'FROM'	=> array($this->ub_index_table => 'i'),
			'LEFT_JOIN' => array(
				array(
					'FROM'	=> array($this->ub_categories_table	=> 'c'),
					'ON'	=> 'i.block_data = c.category_id
								AND (i.block_id = 1
									OR i.block_id = 2
									OR i.block_id = 3)',
				),
			),
			'ORDER_BY'	=> 'i.block_order DESC',
			'WHERE'		=> 'i.block_order != 0',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rowset;
	}

	/**
	 * @param	 $mode (category1, category2, category3, latest, comments, rating, views)
	 * @param int $limit
	 * @param int $category_id
	 * @param int $rating_threshold
	 * @return array
	 */
	public function blog_index_list($mode, $limit, $category_id = 0, $rating_threshold = 0)
	{
		$sql_array = array(
			'SELECT'	=> 'b.blog_id, b.blog_title, b.blog_approved, b.blog_reported, b.blog_image, u.user_id, u.username, u.user_colour, GROUP_CONCAT(distinct bc.category_id) as categories',

			'FROM'	=> array(
				$this->ub_blogs_table => 'b',
				USERS_TABLE => 'u'
			),

			'LEFT_JOIN' => array(
				array(
					'FROM'	=> array($this->ub_blog_category_table	=> 'bc'),
					'ON'	=> 'b.blog_id = bc.blog_id',
				),
				array(
					'FROM'	=> array(ZEBRA_TABLE => 'z'),
					'ON'	=> 'b.author_id = z.user_id AND z.friend = 1',
				),
			),

			'GROUP_BY'	=> 'b.blog_id',
			'WHERE'		=> 'b.author_id = u.user_id',
		);

		switch ($mode)
		{
			case 'category1':
			case 'category2':
			case 'category3':
				$sql_array['ORDER_BY'] = 'b.blog_date DESC';
				$sql_array['WHERE'] .= ' AND bc.category_id = ' . (int) $category_id;
			break;

			case 'latest':
				$sql_array['ORDER_BY'] = 'b.blog_date DESC';
			break;

			case 'comments':
				$sql_array['FROM'][$this->ub_comments_table] = 'c';
				$sql_array['ORDER_BY'] = 'c.comment_time DESC, b.blog_date DESC';
				$sql_array['WHERE']	.= ' AND c.blog_id = b.blog_id';
			break;

			case 'rating':
				$sql_array['SELECT'] .= ', AVG(r.rating) as blog_rating';
				$sql_array['LEFT_JOIN'][] = array('FROM' => array($this->ub_ratings_table => 'r'), 'ON' => 'r.blog_id = b.blog_id');
				$sql_array['GROUP_BY'] .= ', r.blog_id';
			break;

			case 'views':
				$sql_array['ORDER_BY'] = 'b.blog_views DESC, b.blog_date DESC';
			break;
		}
		$sql_array['WHERE'] .= !$this->auth->acl_get('m_ub_approve') ? ' AND (b.blog_approved = 1 OR b.author_id = ' . (int) $this->user->data['user_id'] . ')' : '';

		if (!$this->auth->acl_get('m_ub_view_friends_only'))
		{
			$sql_array['WHERE'] .= ' AND ((b.friends_only = 1 AND (b.author_id = ' . (int) $this->user->data['user_id'] . ' OR z.zebra_id = ' . (int) $this->user->data['user_id'] . ')) OR b.friends_only = 0)';
		}

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$mode === 'rating' ? $sql .= ' HAVING COUNT(r.user_id) >= ' . (int) $rating_threshold . ' ORDER BY blog_rating DESC, b.blog_date DESC' : '';
		$result = $this->db->sql_query_limit($sql, $limit);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rowset;
	}

	/**
	 * @param array $category_ids
	 * @return mixed
	 */
	public function category_list($category_ids = [])
	{
		$sql_array = array(
			'SELECT'	=> 'c.*',
			'FROM'		=> array($this->ub_categories_table => 'c'),
			'ORDER_BY'	=> 'c.left_id',
		);

		if (!empty($category_ids))
		{
			$sql_array['WHERE'] = $this->db->sql_in_set('category_id', $category_ids);
		}
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);

		# Making indexing easier, putting category id as array key
		while ($row = $this->db->sql_fetchrow($result))
		{
			$data[$row['category_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $data;
	}

	/**
	 * @param $comment_array
	 * @return int
	 */
	public function comment_add($comment_array)
	{
		$sql = 'INSERT INTO ' . $this->ub_comments_table . ' ' . $this->db->sql_build_array('INSERT', $comment_array);
		$this->db->sql_query($sql);
		$comment_id = $this->db->sql_nextid();

		return (int) $comment_id;
	}

	/**
	 * @param $comment_id
	 */
	public function comment_delete($comment_id)
	{
		# Delete comment and any children
		$sql = 'DELETE FROM ' . $this->ub_comments_table . ' WHERE comment_id = ' . (int) $comment_id . ' OR parent_id = ' . (int) $comment_id;
		$this->db->sql_query($sql);
	}

	/**
	 * @param $comment_id
	 * @return mixed
	 */
	public function comment_info($comment_id)
	{
		$sql = 'SELECT user_id, comment_text, bbcode_uid, bbcode_options FROM ' . $this->ub_comments_table . ' WHERE comment_id = ' . (int) $comment_id;
		$result = $this->db->sql_query($sql);
		$comment_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $comment_row;
	}

	/**
	 * @param $blog_id
	 * @return mixed
	 */
	public function comment_list($blog_id)
	{
		$sql = 'SELECT c.*, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_height, u.user_avatar_width
				FROM ' . $this->ub_comments_table . ' c
				JOIN ' . USERS_TABLE . ' u
				WHERE c.user_id = u.user_id
					AND c.blog_id = ' . (int) $blog_id;
		$sql .= !$this->auth->acl_get('m_ub_approve') ? ' AND (c.comment_approved = 1 OR c.user_id = ' . (int) $this->user->data['user_id'] . ')' : '';
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rowset;
	}

	/**
	 * @param $comment_id
	 * @return mixed
	 */
	public function comment_reply_count($comment_id)
	{
		$sql = 'SELECT COUNT(comment_id) as reply_count FROM ' . $this->ub_comments_table . ' WHERE parent_id = ' . (int) $comment_id;
		$result = $this->db->sql_query($sql);
		$reply_count = $this->db->sql_fetchfield('reply_count');
		$this->db->sql_freeresult($result);

		return $reply_count;
	}

	/**
	 * @param $comment_array
	 */
	public function comment_update($comment_array)
	{
		$sql = 'UPDATE ' . $this->ub_comments_table . ' SET ' . $this->db->sql_build_array('UPDATE', $comment_array) . ' WHERE comment_id = ' . (int) $comment_array['comment_id'];
		$this->db->sql_query($sql);
	}

	/**
	 * @param $blog_id
	 * @param $category_ids
	 */
	public function corralation_add($blog_id, $category_ids)
	{
		if (!empty($category_ids))
		{
			$sql_array = array();

			foreach ($category_ids as $category_id)
			{
				$sql_array[] = array(
					'blog_id'		=> (int) $blog_id,
					'category_id'	=> (int) $category_id,
				);
			}

			$this->db->sql_multi_insert($this->ub_blog_category_table, $sql_array);
		}
	}

	/**
	 * @param $blog_id
	 * @param $category_ids
	 */
	public function corralation_delete($blog_id, $category_ids)
	{
		if (!empty($category_ids))
		{
			$sql = 'DELETE FROM ' . $this->ub_blog_category_table . '
					WHERE ' . $this->db->sql_in_set('category_id', $category_ids) . '
						AND blog_id = ' . (int) $blog_id;
			$this->db->sql_query($sql);
		}
	}

	/**
	 * @param $edit_data
	 */
	public function edit_add($edit_data)
	{
		$sql = 'INSERT INTO ' . $this->ub_edits_table . ' ' . $this->db->sql_build_array('INSERT', $edit_data);
		$this->db->sql_query($sql);
	}

	/**
	 * @param $edit_id
	 * @return array
	 */
	public function edit_delete($edit_id)
	{
		# Grab some data for the log
		$sql = 'SELECT b.blog_title, u.user_id, u.username, u.user_colour, e.edit_text
				FROM ' . $this->ub_blogs_table . ' b
				JOIN ' . USERS_TABLE . ' u
				JOIN ' . $this->ub_edits_table . ' e
				WHERE e.blog_id = b.blog_id
					AND e.editor_id = u.user_id
					AND e.edit_id = ' . (int) $edit_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		# Delete the reason
		$sql = 'DELETE FROM ' . $this->ub_edits_table . ' WHERE edit_id = ' . (int) $edit_id;
		$this->db->sql_query($sql);

		return $data = array(
			'blog_title'	=> $row['blog_title'],
			'edit_text'	=> $row['edit_text'],
			'edit_user'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		);
	}

	/**
	 * @param $blog_id
	 * @return mixed
	 */
	public function edit_list($blog_id)
	{
		$sql = 'SELECT e.*, u.user_id, u.username, u.user_colour
				FROM ' . $this->ub_edits_table . ' e
				LEFT JOIN ' . USERS_TABLE . ' u ON u.user_id = e.editor_id
				WHERE e.blog_id = ' . (int) $blog_id . '
				ORDER BY e.edit_time DESC';
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rowset;
	}

	/**
	 * @param $username
	 * @return mixed
	 */
	public function get_user_info($username)
	{
		$sql = 'SELECT user_id, username, user_colour
				FROM ' . USERS_TABLE . "
				WHERE username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'";
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $rowset;
	}

	/**
	 * @param $blog_id
	 */
	public function increment_blog_views($blog_id)
	{
		$sql = 'UPDATE ' . $this->ub_blogs_table . ' SET blog_views = blog_views + 1 WHERE blog_id = ' . (int) $blog_id;
		$this->db->sql_query($sql);

		$view_multiplier = 0;

		$sql = 'SELECT blog_views FROM ' . $this->ub_blogs_table . ' WHERE blog_id = ' . (int) $blog_id;
		$result = $this->db->sql_query($sql);
		$views = $this->db->sql_fetchfield('blog_views');
		$this->db->sql_query($sql);

		# No need to check if blog views is more than one, as we just added one, but hey, why not!
		if (!empty($views))
		{
			$views_division = $views / constants::NOTIFY_VIEWS_THRESHOLD;
			$view_multiplier = fmod($views_division, 1) == 0 ? $views : 0;
		}

		return $view_multiplier;
	}

	/**
	 * @param $blog_id
	 * @param $user_id
	 * @param $score
	 * @return array
	 */
	public function rating_add($blog_id, $user_id, $score)
	{
		$rating_added = false;

		# Set up rating_array
		$rating_array = array(
			'blog_id'	=> (int) $blog_id,
			'user_id'	=> (int) $user_id,
			'rating'	=> (int) $score,
		);

		# Check if user has a current ranking for this blog
		$sql = 'SELECT r.rating, b.blog_title
				FROM ' . $this->ub_ratings_table . ' r
				JOIN ' . $this->ub_blogs_table . ' b
				WHERE r.blog_id = b.blog_id
					AND r.blog_id = ' . (int) $blog_id . '
					AND r.user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$rating = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (empty($rating['rating']))
		{
			# User has not yet rated this blog, insert the new rating
			$sql = 'INSERT INTO ' . $this->ub_ratings_table . ' ' . $this->db->sql_build_array('INSERT', $rating_array);
			$this->db->sql_query($sql);

			$rating_added = true;

			# A new rating is added, count the total amount of ratings and see if we have to send out a notification.
			$sql = 'SELECT COUNT(distinct user_id) as rating_count FROM ' . $this->ub_ratings_table . ' WHERE blog_id = ' . (int) $blog_id;
			$result = $this->db->sql_query($sql);
			$rating_count = $this->db->sql_fetchfield('rating_count');
			$this->db->sql_freeresult($result);

			if (!empty($rating_count))
			{
				$rating_division = $rating_count / constants::NOTIFY_RATINGS_THRESHOLD;
				$rating_multiplier = fmod($rating_division, 1) == 0 ? $rating_division : 0;
			}
		}
		else if ($rating['rating'] != $score)
		{
			# User has already rated this blog
			# Check if current rating is different than stored rating
			$sql = 'UPDATE ' . $this->ub_ratings_table . ' SET ' . $this->db->sql_build_array('UPDATE', $rating_array) . ' WHERE blog_id = ' . (int) $blog_id . ' AND user_id = ' . (int) $user_id;
			$this->db->sql_query($sql);

			$rating_added = true;

			# A new rating is added, count the total amount of ratings and see if we have to send out a notification.
			$sql = 'SELECT COUNT(distinct user_id) as rating_count FROM ' . $this->ub_ratings_table . ' WHERE blog_id = ' . (int) $blog_id;
			$result = $this->db->sql_query($sql);
			$rating_count = $this->db->sql_fetchfield('rating_count');
			$this->db->sql_freeresult($result);

			if (!empty($rating_count))
			{
				$rating_division = $rating_count / constants::NOTIFY_RATINGS_THRESHOLD;
				$rating_multiplier = fmod($rating_division, 1) == 0 ? $rating_division : 0;
			}
		}

		return $rating_data = array('rating_added' => $rating_added, 'rating_multiplier' => $rating_multiplier, 'blog_title' => $rating['blog_title']);
	}

	/**
	 * @param	 $blog_id
	 * @param int $user_id
	 * @return mixed
	 */
	public function rating_get($blog_id, $user_id = 0)
	{
		if ($user_id == 0)
		{
			# We're getting the average rating for this blog
			$sql = 'SELECT AVG(rating) as rating FROM ' . $this->ub_ratings_table . ' WHERE blog_id = ' . (int) $blog_id;
		}
		else
		{
			# We're getting the rating for this blog by a specific user
			$sql = 'SELECT rating FROM ' . $this->ub_ratings_table . ' WHERE blog_id = ' . (int) $blog_id . ' AND user_id = ' . (int) $user_id;
		}

		$result = $this->db->sql_query($sql);
		$rating = $this->db->sql_fetchfield('rating');
		$this->db->sql_freeresult($result);

		return $rating;
	}
}
