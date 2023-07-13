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
 * Class m1_initial_schema
 *
 * @package mrgoldy\ultimateblog\migrations\v10x
 */
class m1_initial_schema extends \phpbb\db\migration\container_aware_migration
{
	/**
	* @return void
	* @access public
	*/
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	/**
	* @return void
	* @access public
	*/
	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'ub_blogs'	=> array(
					'COLUMNS'		=> array(
						'blog_id'				=> array('UINT', null, 'auto_increment'),
						'blog_date'				=> array('TIMESTAMP', 0),
						'blog_description'		=> array('VCHAR:125', ''),
						'blog_image'			=> array('VCHAR:255', ''),
						'blog_text'				=> array('MTEXT_UNI', ''),
						'blog_title'			=> array('VCHAR:255', ''),
						'blog_views'			=> array('UINT', 0),
						'blog_approved'			=> array('BOOL', 1),
						'blog_reported'			=> array('BOOL', 0),
						'author_id'				=> array('UINT', 0),
						'bbcode_bitfield'		=> array('VCHAR:255', ''),
						'bbcode_uid'			=> array('VCHAR:8', ''),
						'enable_bbcode'			=> array('BOOL', 1),
						'enable_smilies'		=> array('BOOL', 1),
						'enable_magic_url'		=> array('BOOL', 1),
						'locked_comments'		=> array('BOOL', 0),
						'locked_edit'			=> array('BOOL', 0),
						'locked_rating'			=> array('BOOL', 0),
						'friends_only'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'blog_id',
				),

				$this->table_prefix . 'ub_blog_category'	=> array(
					'COLUMNS'		=> array(
						'blog_id'			=> array('UINT', 0),
						'category_id'		=> array('UINT', 0),
					),
				),

				$this->table_prefix . 'ub_categories'	=>	array(
					'COLUMNS'		=> array(
						'category_id'			=> array('UINT', null, 'auto_increment'),
						'left_id'				=> array('UINT', 0),
						'right_id'				=> array('UINT', 0),
						'category_name'			=> array('VCHAR:255', ''),
						'category_description'	=> array('TEXT_UNI', ''),
						'category_image'		=> array('VCHAR:255', ''),
						'bbcode_bitfield'		=> array('VCHAR:255', ''),
						'bbcode_uid'			=> array('VCHAR:8', ''),
						'enable_bbcode'			=> array('BOOL', 1),
						'enable_smilies'		=> array('BOOL', 1),
						'enable_magic_url'		=> array('BOOL', 1),
						'is_private'			=> array('BOOL', 0),
					),
					'PRIMARY_KEY'	=> 'category_id',
				),

				$this->table_prefix . 'ub_comments'	=> array(
					'COLUMNS'		=> array(
						'comment_id'			=> array('UINT', null, 'auto_increment'),
						'comment_text'			=> array('TEXT_UNI', ''),
						'comment_time'			=> array('TIMESTAMP', 0),
						'comment_approved'		=> array('BOOL', 1),
						'comment_reported'		=> array('BOOL', 0),
						'user_id'				=> array('UINT', 0),
						'blog_id'				=> array('UINT', 0),
						'parent_id'				=> array('UINT', 0),
						'bbcode_bitfield'		=> array('VCHAR:255', ''),
						'bbcode_uid'			=> array('VCHAR:8', ''),
						'bbcode_options'		=> array('TINT:7', 7),
					),
					'PRIMARY_KEY'	=> 'comment_id',
				),

				$this->table_prefix . 'ub_edits'	=> array(
					'COLUMNS'		=> array(
						'edit_id'				=> array('UINT', null, 'auto_increment'),
						'edit_text'				=> array('VCHAR:100', ''),
						'edit_time'				=> array('TIMESTAMP', 0),
						'editor_id'				=> array('UINT', 0),
						'blog_id'				=> array('UINT', 0),
					),
					'PRIMARY_KEY'	=> 'edit_id',
				),

				$this->table_prefix . 'ub_index'	=> array(
					'COLUMNS'		=> array(
						'block_id'				=> array('USINT', 0),
						'block_name'			=> array('XSTEXT', ''),
						'block_limit'			=> array('USINT', 0),
						'block_order'			=> array('USINT', 0),
						'block_data'			=> array('UINT', 0),
					),
				),

				$this->table_prefix . 'ub_ratings'	=> array(
					'COLUMNS'		=> array(
						'blog_id'				=> array('UINT', 0),
						'user_id'				=> array('UINT', 0),
						'rating'				=> array('TINT:5', 0),
					),
				),

				$this->table_prefix . 'ub_reports' => array(
					'COLUMNS'		=> array(
						'report_id'				=> array('UINT', null, 'auto_increment'),
						'reason_id'				=> array('USINT', 0),
						'blog_id'				=> array('UINT', 0),
						'comment_id'			=> array('UINT', 0),
						'user_id'				=> array('UINT', 0),
						'user_notify'			=> array('BOOL', 1),
						'report_closed'			=> array('BOOL', 0),
						'report_time'			=> array('TIMESTAMP', 0),
						'report_text'			=> array('TEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'report_id',
				),
			),
		);
	}

	/**
	* @return void
	* @access public
	*/
	public function revert_schema()
	{
		return array(
				'drop_tables'		=> array(
					$this->table_prefix . 'ub_blogs',
					$this->table_prefix . 'ub_categories',
					$this->table_prefix . 'ub_blog_category',
					$this->table_prefix . 'ub_comments',
					$this->table_prefix . 'ub_index',
					$this->table_prefix . 'ub_edits',
					$this->table_prefix . 'ub_ratings',
					$this->table_prefix . 'ub_reports',
				),
		);
	}
}
