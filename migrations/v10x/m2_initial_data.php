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
 * Class m2_initial_data
 *
 * @package mrgoldy\ultimateblog\migrations\v10x
 */
class m2_initial_data extends \phpbb\db\migration\container_aware_migration
{
	/**
	* @return void
	* @access public
	*/
	static public function depends_on()
	{
		return array('\mrgoldy\ultimateblog\migrations\v10x\m1_initial_schema');
	}

	/**
	* @return void
	* @access public
	*/
	public function update_data()
	{
		$data = array(
			# Add config
			array('config.add', array('ub_allow_bbcodes', 1)),
			array('config.add', array('ub_allow_smilies', 1)),
			array('config.add', array('ub_allow_magic_url', 1)),
			array('config.add', array('ub_blog_min_chars', 200)),
			array('config.add', array('ub_blogs_per_page', 9)),
			array('config.add', array('ub_comments_per_page', 10)),
			array('config.add', array('ub_custom_index', 1)),
			array('config.add', array('ub_enable', 1)),
			array('config.add', array('ub_enable_announcement', 0)),
			array('config.add', array('ub_enable_comments', 1)),
			array('config.add', array('ub_enable_friends_only', 1)),
			array('config.add', array('ub_enable_rating', 1)),
			array('config.add', array('ub_enable_feed', 1)),
			array('config.add', array('ub_enable_feed_cats', 1)),
			array('config.add', array('ub_enable_feed_limit', 10)),
			array('config.add', array('ub_enable_feed_stats', 1)),
			array('config.add', array('ub_enable_subscriptions', 1)),
			array('config.add', array('ub_fa_icon', 'fa-book')),
			array('config.add', array('ub_image_dir', 'images/blog')),
			array('config.add', array('ub_image_cat_dir', 'images/blog/categories')),
			array('config.add', array('ub_image_size', '15')),
			array('config.add', array('ub_notification_id', 0)),
			array('config.add', array('ub_notification_comments_id', 0)),
			array('config.add', array('ub_notification_ratings_id', 0)),
			array('config.add', array('ub_notification_views_id', 0)),
			array('config.add', array('ub_start_date', time())),
			array('config.add', array('ub_title', 'Ultimate Blog')),
			array('config_text.add', array('ub_announcement_text', '<h3>Announcement</h3><p>This is an announcement message that will be displayed troughout the entire <strong>Ultimate Blog</strong> extension.<br>It will be shown on all pages related to this extension.<br><strong><span style="text-decoration: underline;">NOTE</span>:</strong> You have to use <em>HTML</em> and <strong>not</strong> <em>BBCodes</em>!</p>')),
		);

		return $data;
	}
}
