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
 * Class admin_overview
 *
 * @package mrgoldy\ultimateblog\controller
 */
class admin_overview
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\filesystem\filesystem */
	protected $filesystem;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Custom form action */
	protected $u_action;

	/** @var string phpEx */
	protected $phpEx;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string Ultimate Blog blogs table */
	protected $ub_blogs_table;

	/** @var string Ultimate Blog categories table */
	protected $ub_categories_table;

	/** @var string Ultimate Blog comments table */
	protected $ub_comments_table;

	/** @var string Ultimate Blog ratings table */
	protected $ub_ratings_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\filesystem\filesystem		$filesystem
	 * @param \phpbb\log\log					$log
	 * @param \phpbb\request\request			$request
	 * @param \phpbb\template\template			$template
	 * @param \phpbb\user						$user
	 * @param									$phpEx
	 * @param									$phpbb_root_path
	 * @param									$ub_blogs_table
	 * @param									$ub_categories_table
	 * @param									$ub_comments_table
	 * @param									$ub_ratings_table
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\filesystem\filesystem $filesystem, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $phpEx, $phpbb_root_path, $ub_blogs_table, $ub_categories_table, $ub_comments_table, $ub_ratings_table)
	{

		$this->config		= $config;
		$this->db			= $db;
		$this->filesystem	= $filesystem;
		$this->log			= $log;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->phpEx		= $phpEx;
		$this->phpbb_root_path			= $phpbb_root_path;
		$this->ub_blogs_table			= $ub_blogs_table;
		$this->ub_categories_table		= $ub_categories_table;
		$this->ub_comments_table		= $ub_comments_table;
		$this->ub_ratings_table			= $ub_ratings_table;
	}

	/**
	 * Delete all un-used Ultimate Blog images
	 * @param 	$mode	'blog' or 'category'
	 */
	private function purge_ultimateblog_images($mode)
	{
		$all_images = $inuse_images = array();

		# Set up a new finder
		$finder = new \phpbb\finder($this->filesystem, $this->phpbb_root_path);

		# Set up the finder depending on the mode
		switch ($mode)
		{
			case 'category':
				$finder->core_path($this->config['ub_image_cat_dir'] . '/')
						->core_prefix('ub_cat_');
			break;

			case 'blog':
				$finder->core_path($this->config['ub_image_dir'] . '/')
						->core_prefix('ub_');
			break;
		}

		# Grab all the images
		$all_imgs = $finder->get_files();

		# Get only the image name of all the images we found, as we need that for comparison against the database entries
		foreach ($all_imgs as $image_path)
		{
			$image_path_array = explode('/', $image_path);
			$all_images[] = end($image_path_array);
		}

		# Grab all the current images in use for this mode
		switch ($mode)
		{
			case 'category':
				$sql = 'SELECT category_image FROM ' . $this->ub_categories_table;
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$inuse_images[] = $row['category_image'];
				}
				$this->db->sql_freeresult($result);
			break;

			case 'blog':
				$sql = 'SELECT blog_image FROM ' . $this->ub_blogs_table;
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$inuse_images[] = $row['blog_image'];
				}
				$this->db->sql_freeresult($result);
			break;
		}

		# Find the difference in the arrays
		$delete_images_array = array_diff($all_images, $inuse_images);

		# Foreach difference, we delete the file. Be gone you fools!
		foreach ($delete_images_array as $delete_image)
		{
			switch ($mode)
			{
				case 'category':
					$this->filesystem->remove($this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $delete_image);
				break;

				case 'blog':
					$this->filesystem->remove($this->phpbb_root_path . $this->config['ub_image_dir'] . '/' . $delete_image);
				break;
			}
		}
	}

	/**
	 * Ultimate Blog overview
	 * @param int	$id
	 * @param string $mode
	 * @param string $action
	 */
	public function ub_overview($id, $mode, $action)
	{
		# Tried through services (- '%core.adm_relative_path%'), but didn't work.
		global $phpbb_admin_path;

		if ($action)
		{
			if ($this->request->is_ajax())
			{
				if (confirm_box(true))
				{
					switch ($action)
					{
						case 'purgeblog':
							# Delete the images
							$this->purge_ultimateblog_images('blog');

							# Add it to the log
							$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_PURGE_IMAGES_BLOG', time(), array());

							# Show success message
							trigger_error('ACP_UB_PURGE_IMAGES_BLOG_SUCCESS');
						break;

						case 'purgecategory':
							# Delete the images
							$this->purge_ultimateblog_images('category');

							# Add it to the log
							$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_PURGE_IMAGES_CATEGORY', time(), array());

							# Show success message
							trigger_error('ACP_UB_PURGE_IMAGES_CATEGORY_SUCCESS');
						break;
					}
				}
				else
				{
					switch ($action)
					{
						case 'purgeblog':
							$confirm_lang = $this->user->lang('ACP_UB_PURGE_IMAGES_BLOG_CONFIRM');
						break;

						case 'purgecategory':
							$confirm_lang = $this->user->lang('ACP_UB_PURGE_IMAGES_CATEGORY_CONFIRM');
						break;
					}

					# Display mode
					confirm_box(false, $confirm_lang, build_hidden_fields(array(
						'i'			=> $id,
						'mode'		=> $mode,
						'action'	=> $action,
						)
					));
				}
			}
		}

		$log_user_data = array();
		$log_mod_data = array();
		$log_admin_data = array();
		$log_count = 0;
		view_log('user', $log_user_data, $log_count, 5, 0, 0, 0, 0, 0, $sort_by = 'l.log_time DESC', $keywords = 'Ultimate Blog');
		view_log('mod', $log_mod_data, $log_count, 5, 0, 0, 0, 0, 0, $sort_by = 'l.log_time DESC', $keywords = 'Ultimate Blog');
		view_log('admin', $log_admin_data, $log_count, 5, 0, 0, 0, 0, 0, $sort_by = 'l.log_time DESC', $keywords = 'Ultimate Blog');

		foreach ($log_user_data as $row)
		{
			$this->template->assign_block_vars('user_log', array(
				'USERNAME'			=> $row['username_full'],
				'REPORTEE_USERNAME'	=> ($row['reportee_username'] && $row['user_id'] != $row['reportee_id']) ? $row['reportee_username_full'] : '',
				'IP'				=> $row['ip'],
				'DATE'				=> $this->user->format_date($row['time']),
				'ACTION'			=> $row['action'],
				'ID'				=> $row['id'],
			));
		}

		foreach ($log_mod_data as $row)
		{
			$this->template->assign_block_vars('mod_log', array(
				'USERNAME'			=> $row['username_full'],
				'REPORTEE_USERNAME'	=> ($row['reportee_username'] && $row['user_id'] != $row['reportee_id']) ? $row['reportee_username_full'] : '',
				'IP'				=> $row['ip'],
				'DATE'				=> $this->user->format_date($row['time']),
				'ACTION'			=> $row['action'],
				'ID'				=> $row['id'],
				)
			);
		}

		foreach ($log_admin_data as $row)
		{
			$this->template->assign_block_vars('admin_log', array(
				'USERNAME'			=> $row['username_full'],
				'REPORTEE_USERNAME'	=> ($row['reportee_username'] && $row['user_id'] != $row['reportee_id']) ? $row['reportee_username_full'] : '',
				'IP'				=> $row['ip'],
				'DATE'				=> $this->user->format_date($row['time']),
				'ACTION'			=> $row['action'],
				'ID'				=> $row['id'],
				)
			);
		}

		# Blog count
		$sql = 'SELECT COUNT(blog_id) as count FROM ' . $this->ub_blogs_table;
		$result = $this->db->sql_query($sql);
		$blog_count = $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		# Category count
		$sql = 'SELECT COUNT(category_id) as count FROM ' . $this->ub_categories_table;
		$result = $this->db->sql_query($sql);
		$category_count = $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		if ($this->config['ub_enable_comments'])
		{
			# Comment count
			$sql = 'SELECT COUNT(comment_id) as count FROM ' . $this->ub_comments_table;
			$result = $this->db->sql_query($sql);
			$comment_count = $this->db->sql_fetchfield('count');
			$this->db->sql_freeresult($result);
		}

		# Blog views count
		$sql = 'SELECT SUM(blog_views) as views FROM ' . $this->ub_blogs_table;
		$result = $this->db->sql_query($sql);
		$blog_views = $this->db->sql_fetchfield('views');
		$this->db->sql_freeresult($result);

		# Blogs per day
		$days_count_buffer = round(abs($this->config['ub_start_date'] - time())/60/60/24);
		$days_count = $days_count_buffer == 0 ? 1 : $days_count_buffer;
		$blogs_per_day = round(($blog_count / $days_count), 2);

		if ($this->config['ub_enable_comments'])
		{
			# Comments per day
			$comments_per_day = round(($comment_count / $days_count), 2);
		}

		# User with most blogs
		$sql = 'SELECT b.author_id, u.username, u.user_colour, COUNT(b.blog_id) as count
				FROM ' . $this->ub_blogs_table . ' b
				JOIN '. USERS_TABLE . ' u
				WHERE b.author_id = u.user_id
				GROUP BY b.author_id
				ORDER BY count DESC';
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$user_with_most_blogs = array(
			'user'	=> get_username_string('full', $row['author_id'], $row['username'], $row['user_colour']),
			'count'	=> $row['count'],
		);

		if ($this->config['ub_enable_comments'])
		{
			# User with most comments
			$sql = 'SELECT c.user_id, u.username, u.user_colour, COUNT(c.comment_id) as count
					FROM ' . $this->ub_comments_table . ' c
					JOIN ' . USERS_TABLE . ' u
					WHERE c.user_id = u.user_id
					GROUP BY c.user_id
					ORDER BY count DESC';
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$user_with_most_comments = array(
				'user'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'count'	=> $row['count'],
			);

			# Blog with most comments
			$sql_array = array(
				'SELECT'	=> 'b.author_id, u.username, u.user_colour, b.blog_title, COUNT(c.comment_id) as count',
				'FROM'		=> array($this->ub_blogs_table => 'b',
									USERS_TABLE => 'u'),
				'LEFT_JOIN' => array(
						array(
							'FROM'	=> array($this->ub_comments_table => 'c'),
							'ON'	=> 'b.blog_id = c.blog_id'
						)
					),
				'WHERE'		=> 'b.author_id = u.user_id',
				'GROUP_BY'	=> 'c.blog_id, b.author_id, b.blog_title',
				'ORDER_BY'	=> 'count DESC'
				);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$blog_with_most_comments = array(
				'author'	=> get_username_string('full', $row['author_id'], $row['username'], $row['user_colour']),
				'title'		=> $row['blog_title'],
				'count'		=> $row['count'],
			);
		}

		# Blog with most views
		$sql = 'SELECT b.author_id, u.username, u.user_colour, b.blog_title, b.blog_views
				FROM ' . $this->ub_blogs_table . ' b
				JOIN ' . USERS_TABLE . ' u
				WHERE b.author_id = u.user_id
				ORDER BY b.blog_views DESC';
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$blog_with_most_views = array(
			'author'	=> get_username_string('full', $row['author_id'], $row['username'], $row['user_colour']),
			'title'		=> $row['blog_title'],
			'views'		=> $row['blog_views'],
		);

		if ($this->config['ub_enable_rating'])
		{
			# Blog with best rating
			$sql_array = array(
				'SELECT'	=> 'u.user_id, u.username, u.user_colour, b.blog_title, AVG(r.rating) as rating',
				'FROM'		=> array($this->ub_blogs_table	=> 'b',
									USERS_TABLE => 'u'
								),
				'LEFT_JOIN' => array(
					array(
						'FROM'	=> array($this->ub_ratings_table => 'r'),
						'ON'	=> 'b.blog_id = r.blog_id'
					)
				),
				'WHERE'			=> 'b.author_id = u.user_id',
				'GROUP_BY'		=> 'r.blog_id, u.user_id, b.blog_title',
				'ORDER_BY'		=> 'rating DESC'
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$blog_with_best_rating = array(
				'author'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'title'		=> $row['blog_title'],
				'rating'	=> round($row['rating'], 2),
			);

			# Blog with worst rating (IMPORTANT: HAS TO BE IMMEDIATLY AFTER BEST RATING)
			$sql_array['ORDER_BY'] = 'rating ASC';
			$sql_array['WHERE'] .= ' AND rating > 0';
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$blog_with_worst_rating = array(
				'author'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'title'		=> $row['blog_title'],
				'rating'	=> round($row['rating'], 2),
			);
		}

		$this->template->assign_vars(array(
			'BLOG_COUNT'			=> $blog_count,
			'CATEGORY_COUNT'		=> $category_count,
			'USER_MOST_BLOGS'		=> $user_with_most_blogs['user'],
			'USER_MOST_BLOGS_COUNT'	=> $user_with_most_blogs['count'],
			'USER_MOST_COMMENTS'	=> $this->config['ub_enable_comments'] ? $user_with_most_comments['user'] : '',
			'USER_MOST_COMMENTS_COUNT'	=> $this->config['ub_enable_comments'] ? $user_with_most_comments['count'] : '',
			'COMMENT_COUNT'				=> $this->config['ub_enable_comments'] ? $comment_count : '',
			'BLOG_MOST_COMMENTS_AUTHOR'	=> $this->config['ub_enable_comments'] ? $blog_with_most_comments['author'] : '',
			'BLOG_MOST_COMMENTS_TITLE'	=> $this->config['ub_enable_comments'] ? $blog_with_most_comments['title'] : '',
			'BLOG_MOST_COMMENTS_COUNT'	=> $this->config['ub_enable_comments'] ? $blog_with_most_comments['count'] : '',
			'BLOG_VIEWS'				=> $blog_views,
			'BLOG_MOST_VIEWS_AUTHOR'	=> $blog_with_most_views['author'],
			'BLOG_MOST_VIEWS_TITLE'		=> $blog_with_most_views['title'],
			'BLOG_MOST_VIEWS_COUNT'		=> $blog_with_most_views['views'],
			'COMMENTS_PER_DAY'			=> $this->config['ub_enable_comments'] ? $comments_per_day : '',
			'BLOGS_PER_DAY'				=> $blogs_per_day,
			'BLOG_BEST_RATING_AUTHOR'	=> $this->config['ub_enable_rating'] ? $blog_with_best_rating['author'] : '',
			'BLOG_BEST_RATING_TITLE'	=> $this->config['ub_enable_rating'] ? $blog_with_best_rating['title'] : '',
			'BLOG_BEST_RATING_RATING'	=> $this->config['ub_enable_rating'] ? $blog_with_best_rating['rating'] : '',
			'BLOG_WORST_RATING_AUTHOR'	=> $this->config['ub_enable_rating'] ? $blog_with_worst_rating['author'] : '',
			'BLOG_WORST_RATING_TITLE'	=> $this->config['ub_enable_rating'] ? $blog_with_worst_rating['title'] : '',
			'BLOG_WORST_RATING_RATING'	=> $this->config['ub_enable_rating'] ? $blog_with_worst_rating['rating'] : '',
			'UB_START_DATE'				=> $this->user->format_date($this->config['ub_start_date']),
			'UB_UPTIME'					=> $this->time_elapsed_string($this->config['ub_start_date'], true),

			'S_COMMENTS_ENABLED'		=> $this->config['ub_enable_comments'],
			'S_RATING_ENABLED'			=> $this->config['ub_enable_rating'],

			'U_ACTION'					=> $this->u_action,

			'U_UB_LOG_ADMIN'			=> append_sid("{$phpbb_admin_path}index.$this->phpEx", 'i=acp_logs&amp;mode=admin&amp;keywords=Ultimate%20Blog'),
			'U_UB_LOG_MOD'				=> append_sid("{$phpbb_admin_path}index.$this->phpEx", 'i=acp_logs&amp;mode=mod&amp;keywords=Ultimate%20Blog'),
			'U_UB_LOG_USER'				=> append_sid("{$phpbb_admin_path}index.$this->phpEx", 'i=acp_logs&amp;mode=users&amp;keywords=Ultimate%20Blog'),
		));
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

	/**
	 * @param		$datetime
	 * @param bool $full
	 * @return string
	 */
	public function time_elapsed_string($datetime, $full = false)
	{
		$now = $this->user->create_datetime();
		$ago = $this->user->create_datetime('@' . $datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);

		foreach ($string as $k => &$v)
		{
			if ($diff->$k)
			{
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			}
			else
			{
				unset($string[$k]);
			}
		}

		if (!$full)
		{
			$string = array_slice($string, 0, 1);
		}

		return implode($this->user->lang('COMMA_SEPARATOR'), $string);
	}
}
