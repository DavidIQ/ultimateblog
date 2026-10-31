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
		return ['\phpbb\db\migration\data\v320\v320'];
	}

	/**
	* @return void
	* @access public
	*/
	public function update_schema()
	{
		return [
			'add_tables'		=> [
				$this->table_prefix . 'ub_blogs'	=> [
					'COLUMNS'		=> [
						'blog_id'				=> ['UINT', null, 'auto_increment'],
						'blog_date'				=> ['TIMESTAMP', 0],
						'blog_description'		=> ['VCHAR:125', ''],
						'blog_image'			=> ['VCHAR:255', ''],
						'blog_text'				=> ['MTEXT_UNI', ''],
						'blog_title'			=> ['VCHAR:255', ''],
						'blog_views'			=> ['UINT', 0],
						'blog_approved'			=> ['BOOL', 1],
						'blog_reported'			=> ['BOOL', 0],
						'author_id'				=> ['UINT', 0],
						'bbcode_bitfield'		=> ['VCHAR:255', ''],
						'bbcode_uid'			=> ['VCHAR:8', ''],
						'enable_bbcode'			=> ['BOOL', 1],
						'enable_smilies'		=> ['BOOL', 1],
						'enable_magic_url'		=> ['BOOL', 1],
						'locked_comments'		=> ['BOOL', 0],
						'locked_edit'			=> ['BOOL', 0],
						'locked_rating'			=> ['BOOL', 0],
						'friends_only'			=> ['BOOL', 0],
					],
					'PRIMARY_KEY'	=> 'blog_id',
				],

				$this->table_prefix . 'ub_blog_category'	=> [
					'COLUMNS'		=> [
						'blog_id'			=> ['UINT', 0],
						'category_id'		=> ['UINT', 0],
					],
				],

				$this->table_prefix . 'ub_categories'	=>	[
					'COLUMNS'		=> [
						'category_id'			=> ['UINT', null, 'auto_increment'],
						'left_id'				=> ['UINT', 0],
						'right_id'				=> ['UINT', 0],
						'category_name'			=> ['VCHAR:255', ''],
						'category_description'	=> ['TEXT_UNI', ''],
						'category_image'		=> ['VCHAR:255', ''],
						'bbcode_bitfield'		=> ['VCHAR:255', ''],
						'bbcode_uid'			=> ['VCHAR:8', ''],
						'enable_bbcode'			=> ['BOOL', 1],
						'enable_smilies'		=> ['BOOL', 1],
						'enable_magic_url'		=> ['BOOL', 1],
						'is_private'			=> ['BOOL', 0],
					],
					'PRIMARY_KEY'	=> 'category_id',
				],

				$this->table_prefix . 'ub_comments'	=> [
					'COLUMNS'		=> [
						'comment_id'			=> ['UINT', null, 'auto_increment'],
						'comment_text'			=> ['TEXT_UNI', ''],
						'comment_time'			=> ['TIMESTAMP', 0],
						'comment_approved'		=> ['BOOL', 1],
						'comment_reported'		=> ['BOOL', 0],
						'user_id'				=> ['UINT', 0],
						'blog_id'				=> ['UINT', 0],
						'parent_id'				=> ['UINT', 0],
						'bbcode_bitfield'		=> ['VCHAR:255', ''],
						'bbcode_uid'			=> ['VCHAR:8', ''],
						'bbcode_options'		=> ['TINT:7', 7],
					],
					'PRIMARY_KEY'	=> 'comment_id',
				],

				$this->table_prefix . 'ub_edits'	=> [
					'COLUMNS'		=> [
						'edit_id'				=> ['UINT', null, 'auto_increment'],
						'edit_text'				=> ['VCHAR:100', ''],
						'edit_time'				=> ['TIMESTAMP', 0],
						'editor_id'				=> ['UINT', 0],
						'blog_id'				=> ['UINT', 0],
					],
					'PRIMARY_KEY'	=> 'edit_id',
				],

				$this->table_prefix . 'ub_index'	=> [
					'COLUMNS'		=> [
						'block_id'				=> ['USINT', 0],
						'block_name'			=> ['XSTEXT', ''],
						'block_limit'			=> ['USINT', 0],
						'block_order'			=> ['USINT', 0],
						'block_data'			=> ['UINT', 0],
					],
				],

				$this->table_prefix . 'ub_ratings'	=> [
					'COLUMNS'		=> [
						'blog_id'				=> ['UINT', 0],
						'user_id'				=> ['UINT', 0],
						'rating'				=> ['TINT:5', 0],
					],
				],

				$this->table_prefix . 'ub_reports' => [
					'COLUMNS'		=> [
						'report_id'				=> ['UINT', null, 'auto_increment'],
						'reason_id'				=> ['USINT', 0],
						'blog_id'				=> ['UINT', 0],
						'comment_id'			=> ['UINT', 0],
						'user_id'				=> ['UINT', 0],
						'user_notify'			=> ['BOOL', 1],
						'report_closed'			=> ['BOOL', 0],
						'report_time'			=> ['TIMESTAMP', 0],
						'report_text'			=> ['TEXT_UNI', ''],
					],
					'PRIMARY_KEY'	=> 'report_id',
				],
			],
		];
	}

	/**
	* @return void
	* @access public
	*/
	public function revert_schema()
	{
		return [
				'drop_tables'		=> [
					$this->table_prefix . 'ub_blogs',
					$this->table_prefix . 'ub_categories',
					$this->table_prefix . 'ub_blog_category',
					$this->table_prefix . 'ub_comments',
					$this->table_prefix . 'ub_index',
					$this->table_prefix . 'ub_edits',
					$this->table_prefix . 'ub_ratings',
					$this->table_prefix . 'ub_reports',
				],
		];
	}
}
