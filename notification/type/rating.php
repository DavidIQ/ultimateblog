<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog\notification\type;

use mrgoldy\ultimateblog\constants;

/**
 * Ultimate Blog Notification class.
 *
 * @package notifications
 */
class rating extends \phpbb\notification\type\base
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var string Ultimate Blog blogs table */
	protected $ub_blogs_table;

	/**
	 * Set the controller helper
	 *
	 * @param \phpbb\controller\helper $helper
	 * @return void
	 */
	public function set_controller_helper(\phpbb\controller\helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * Set the config object
	 *
	 * @param \phpbb\controller\helper $helper
	 * @return void
	 */
	public function set_config(\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	/**
	 * Set Ultimate Blog blogs table
	 * @param string Ultimate Blog blogs table
	 * @return void
	 */
	public function set_blogs_table($ub_blogs_table)
	{
		$this->ub_blogs_table = $ub_blogs_table;
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	static public $notification_option = array(
		'lang'	=> 'UB_NOTIFICATION_TYPE_RATING',
		'group'	=> 'UB_NOTIFICATION_GROUP',
	);

	/**
	 * Get notification type name
	 *
	 * @return string
	 */
	public function get_type()
	{
		return 'mrgoldy.ultimateblog.notification.type.rating';
	}

	/**
	 * Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	 *
	 * @return bool True/False whether or not this is available to the user
	 */
	public function is_available()
	{
		return $this->config['ub_enable_rating'];
	}

	/**
	 * Get the id of the notification
	 *
	 * @param array $data The type specific data
	 *
	 * @return int Id of the notification
	 */
	public static function get_item_id($data)
	{
		return $data['notification_id'];
	}

	/**
	 * Get the id of the parent
	 *
	 * @param array $data The type specific data
	 *
	 * @return int Id of the parent
	 */
	public static function get_item_parent_id($data)
	{
		// No parent
		return 0;
	}

	/**
	 * Find the users who want to receive notifications
	 *
	 * @param array $data The type specific data
	 * @param array $options Options for finding users for notification
	 * 		ignore_users => array of users and user types that should not receive notifications from this type because they've already been notified
	 * 						e.g.: array(2 => array(''), 3 => array('', 'email'), ...)
	 *
	 * @return array
	 */
	public function find_users_for_notification($data, $options = array())
	{
		$users[$data['author_id']] = $this->notification_manager->get_default_methods();

		return $users;
	}

	/**
	 * Users needed to query before this notification can be displayed
	 *
	 * @return array Array of user_ids
	 */
	public function users_to_query()
	{
		return array();
	}

	/**
	 * Get the HTML formatted title of this notification
	 *
	 * @return string
	 */
	public function get_title()
	{
		$blog_title = $this->get_data('blog_title');
		$ratings = constants::NOTIFY_RATINGS_THRESHOLD * $this->get_data('multiplier');

		$notification_language = $this->language->lang('UB_NOTIFICATION_TYPE_RATINGS_MSG', $ratings, $blog_title);

		return $notification_language;
	}

	/**
	 * Get the url to this item
	 *
	 * @return string URL
	 */
	public function get_url()
	{
		$blog_id = $this->get_data('blog_id');
		$blog_title = $this->get_data('blog_title');
		$url = $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id, 'title' => urlencode($blog_title)));

		return $url;
	}

	/**
	 * Get email template
	 *
	 * @return string|bool
	 */
	public function get_email_template()
	{
		return false;
	}

	/**
	 * Get email template variables
	 *
	 * @return array
	 */
	public function get_email_template_variables()
	{
		return array();
	}

	/**
	 * Function for preparing the data for insertion in an SQL query
	 * (The service handles insertion)
	 *
	 * @param array $data The type specific data
	 * @param array $pre_create_data Data from pre_create_insert_array()
	 *
	 * @return array Array of data ready to be inserted into the database
	 */
	public function create_insert_array($data, $pre_create_data = array())
	{
		# Grab blog title and author ID
		$sql = 'SELECT author_id, blog_title FROM ' . $this->ub_blogs_table . ' WHERE blog_id = ' . (int) $data['blog_id'];
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$this->set_data('author_id', $row['author_id']);
		$this->set_data('blog_id', $data['blog_id']);
		$this->set_data('blog_title', $row['blog_title']);
		$this->set_data('multiplier', $data['multiplier']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
