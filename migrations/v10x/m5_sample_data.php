<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog\migrations\v10x;

/**
 * Class m5_sample_data
 *
 * @package mrgoldy\ultimateblog\migrations\v10x
 */
class m5_sample_data extends \phpbb\db\migration\container_aware_migration
{
	/**
	* @return void
	* @access public
	*/
	static public function depends_on()
	{
		return array('\mrgoldy\ultimateblog\migrations\v10x\m4_mcp_module');
	}

	/**
	* @return void
	* @access public
	*/
	public function update_data()
	{
		$data = array(
			# Insert sample data for Ultimate Blog
			array('custom', array(array($this, 'insert_sample_data'))),

			# Create blog images directory
			array('custom', array(array($this, 'create_blog_image_dir'))),
		);

		return $data;
	}

	public function create_blog_image_dir()
	{
		global $phpbb_container;

		$img_dir = $this->phpbb_root_path . 'images';
		$blog_dir = $img_dir . '/blog';
		$cat_dir = $blog_dir . '/categories';
		$filesystem = $phpbb_container->get('filesystem');

		if ($filesystem->exists($img_dir) && $filesystem->is_writable($img_dir))
		{
			if (!$filesystem->exists($blog_dir))
			{
				$filesystem->mkdir($blog_dir, 511);
			}
			if (!$filesystem->exists($cat_dir))
			{
				$filesystem->mkdir($cat_dir, 511);
			}
		}

		$filesystem->mirror($this->phpbb_root_path . 'ext/mrgoldy/ultimateblog/images/', $blog_dir);
	}

	/**
	* # Insert sample data
	* @return void
	* @access public
	*/
	public function insert_sample_data()
	{
		$user = $this->container->get('user');

		$sample_blog = array(
			array(
				'blog_id'				=> 1,
				'blog_date'				=> time(),
				'blog_description'		=> 'This is a descriptive piece of text summarising everything in this blog. Enjoy using Ultimate Blog!',
				'blog_image'			=> 'ub_image.png',
				'blog_text'				=> '<r>This is an example blog in your phpBB3 Extension: <B><s>[b]</s>Ultimate Blog<e>[/b]</e></B>.<br/>
<br/>
Everything seems to be working. You may delete this blog if you like and continue to set up your Ultimate Blog extension. During the installation of this extension a category and blog post have been made. Which you are read right now. These were to made to show you the overall look and options of the extension.<br/>
<br/>
Permissions have also been added, regular permissions for Registered Users role <I><s>[i]</s>Standard<e>[/i]</e></I> and <I><s>[i]</s>Full<e>[/i]</e></I>. Moderating permissions for the Global Moderators roles <I><s>[i]</s>Standard<e>[/i]</e></I> and <I><s>[i]</s>Full<e>[/i]</e></I> and administrative permissions for the Administrators roles <I><s>[i]</s>Standard<e>[/i]</e></I> and <I><s>[i]</s>Full<e>[/i]</e></I>.<br/>
<br/>
Hope you like the Ultimate Blog extension and: Have fun!</r>',
				'blog_title'			=> 'Welcome to Ultimate Blog',
				'blog_views'			=> 0,
				'blog_approved'			=> 1,
				'author_id'				=> (int) $user->data['user_id'],
				'bbcode_bitfield'		=> '',
				'bbcode_uid'			=> '',
				'enable_bbcode'			=> 1,
				'enable_smilies'		=> 1,
				'enable_magic_url'		=> 1,
				'locked_comments'		=> 0,
				'locked_edit'			=> 0,
				'locked_rating'			=> 0,
				'friends_only'			=> 0,
			),
		);

		$sample_category = array(
			array(
				'category_id'			=> 1,
				'category_name'			=> 'Your first Ultimate Blog category',
				'category_description'	=> '<t>A description of your first Ultimate Blog category.</t>',
				'category_image'		=> 'ub_cat_image.png',
				'bbcode_bitfield'		=> '',
				'bbcode_uid'			=> '',
				'enable_bbcode'			=> 1,
				'enable_smilies'		=> 1,
				'enable_magic_url'		=> 1,
				'is_private'			=> 0,
				'left_id'				=> 1,
				'right_id'				=> 2,
			),
		);

		$sample_blog_category = array(
			array(
				'blog_id'				=> 1,
				'category_id'			=> 1,
			),
		);

		$sample_comment = array(
			array(
				'comment_id'			=> 1,
				'comment_text'			=> '<r>This is an example comment on one of your <B><s>[b]</s>Ultimate Blog<e>[/b]</e></B> blogs.</r>',
				'comment_time'			=> time(),
				'comment_approved'		=> 1,
				'user_id'				=> (int) $user->data['user_id'],
				'blog_id'				=> 1,
				'parent_id'				=> 0,
				'bbcode_bitfield'		=> '',
				'bbcode_uid'			=> '',
				'bbcode_options'		=> 7,
			),
			array(
				'comment_id'			=> 2,
				'comment_text'			=> '<r>This is a second example comment on one of your <B><s>[b]</s>Ultimate Blog<e>[/b]</e></B> blogs. <E>:-D</E></r>',
				'comment_time'			=> time()+300,
				'comment_approved'		=> 1,
				'user_id'				=> (int) $user->data['user_id'],
				'blog_id'				=> 1,
				'parent_id'				=> 0,
				'bbcode_bitfield'		=> '',
				'bbcode_uid'			=> '',
				'bbcode_options'		=> 7,
			),
			array(
				'comment_id'			=> 3,
				'comment_text'			=> '<t>This is an example reply on one of your Ultimate Blog comments.</t>',
				'comment_time'			=> time()+600,
				'comment_approved'		=> 1,
				'user_id'				=> (int) $user->data['user_id'],
				'blog_id'				=> 1,
				'parent_id'				=> 1,
				'bbcode_bitfield'		=> '',
				'bbcode_uid'			=> '',
				'bbcode_options'		=> 7,
			),
		);

		$ultimateblog_index = array(
			array(
				'block_id'		=> 1,
				'block_name'	=> 'category1',
				'block_limit'	=> 3,
				'block_order'	=> 0,
				'block_data'	=> 1,
			),
			array(
				'block_id'		=> 2,
				'block_name'	=> 'category2',
				'block_limit'	=> 3,
				'block_order'	=> 0,
				'block_data'	=> 1,
			),
			array(
				'block_id'		=> 3,
				'block_name'	=> 'category3',
				'block_limit'	=> 3,
				'block_order'	=> 0,
				'block_data'	=> 1,
			),
			array(
				'block_id'		=> 4,
				'block_name'	=> 'latest',
				'block_limit'	=> 3,
				'block_order'	=> 1,
				'block_data'	=> 0,
			),
			array(
				'block_id'		=> 5,
				'block_name'	=> 'comments',
				'block_limit'	=> 3,
				'block_order'	=> 1,
				'block_data'	=> 0,
			),
			array(
				'block_id'		=> 6,
				'block_name'	=> 'rating',
				'block_limit'	=> 3,
				'block_order'	=> 0,
				'block_data'	=> 10,
			),
			array(
				'block_id'		=> 7,
				'block_name'	=> 'views',
				'block_limit'	=> 3,
				'block_order'	=> 0,
				'block_data'	=> 0,
			),
		);

		# Insert the data
		$this->db->sql_multi_insert($this->table_prefix . 'ub_blogs', $sample_blog);
		$this->db->sql_multi_insert($this->table_prefix . 'ub_categories', $sample_category);
		$this->db->sql_multi_insert($this->table_prefix . 'ub_blog_category', $sample_blog_category);
		$this->db->sql_multi_insert($this->table_prefix . 'ub_comments', $sample_comment);
		$this->db->sql_multi_insert($this->table_prefix . 'ub_index', $ultimateblog_index);
	}
}
