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

/**
 * Ultimate Blog Notification class.
 *
 * @package notifications
 */
class ultimateblog extends \phpbb\notification\type\base
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\user_loader */
	protected $user_loader;

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
	 * Set the user loader
	 * @param \phpbb\user_loader $user_loader
	 * @return void
	 */
	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
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
	 * Get notification type name
	 *
	 * @return string
	 */
	public function get_type()
	{
		return 'mrgoldy.ultimateblog.notification.type.ultimateblog';
	}

	/**
	 * Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	 *
	 * @return bool True/False whether or not this is available to the user
	 */
	public function is_available()
	{
		return false;
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
		return array($this->get_data('actionee_id'));
	}

	/**
	 * Get the HTML formatted title of this notification
	 *
	 * @return string
	 */
	public function get_title()
	{
		$username = $this->user_loader->get_username($this->get_data('actionee_id'), 'no_profile');
		$blog_title = $this->get_data('blog_title');

		switch ($this->get_data('notification_type'))
		{
			case 'blog_deleted':
				$notification_language = $this->language->lang('UB_NOTIFICATION_BLOG_DELETED', $username, $blog_title);
			break;

			case 'blog_edited':
				$notification_language = $this->language->lang('UB_NOTIFICATION_BLOG_EDITED', $username, $blog_title);
			break;

			case 'comment_added':
				$notification_language = $this->language->lang('UB_NOTIFICATION_COMMENT_ADDED', $username, $blog_title);
			break;

			case 'comment_edited':
				$notification_language = $this->language->lang('UB_NOTIFICATION_COMMENT_EDITED', $username, $blog_title);
			break;

			case 'comment_deleted':
				$notification_language = $this->language->lang('UB_NOTIFICATION_COMMENT_DELETED', $username, $blog_title);
			break;

			case 'report_blog':
				$notification_language = $this->language->lang('UB_NOTIFICATION_BLOG_REPORT', $username, $blog_title);
			break;

			case 'report_comment':
				$notification_language = $this->language->lang('UB_NOTIFICATION_COMMENT_REPORT', $username, $blog_title);
			break;

			default:
				$notification_language = $this->language->lang('UB_NOTIFICATION_DEFAULT'); // Should never get this.
			break;
		}

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
		$comment_id = $this->get_data('comment_id');

		switch ($this->get_data('notification_type'))
		{
			case 'blog_edited':
			case 'comment_deleted':
			case 'report_blog':
				$url = $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id, 'title' => urlencode($blog_title)));
			break;

			case 'comment_added':
			case 'comment_edited':
			case 'report_comment':
				$url = $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id)) . '#' . (int) $comment_id;
			break;

			case 'blog_deleted':
			default:
				$url = $this->helper->route('mrgoldy_ultimateblog_index');
			break;
		}
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
		# Check if blog title is set
		if (empty($data['blog_title']))
		{
			# Grab blog title
			$sql = 'SELECT blog_title FROM ' . $this->ub_blogs_table . ' WHERE blog_id = ' . (int) $data['blog_id'];
			$result = $this->db->sql_query($sql);
			$blog_title = $this->db->sql_fetchfield('blog_title');
			$this->db->sql_freeresult($result);

			$this->set_data('blog_title', $blog_title);
		}
		else
		{
			$this->set_data('blog_title', $data['blog_title']);
		}

		$this->set_data('actionee_id', $data['actionee_id']);
		$this->set_data('author_id', $data['author_id']);
		$this->set_data('blog_id', $data['blog_id']);
		$this->set_data('comment_id', $data['comment_id']);
		$this->set_data('notification_type', $data['notification_type']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
