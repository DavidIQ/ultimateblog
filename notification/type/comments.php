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
class comments extends \phpbb\notification\type\base
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var string Ultimate Blog blogs table */
	protected $ub_blogs_table;

	/** @var \phpbb\user_loader */
	protected $user_loader;

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
	 * Set the user loader object
	 * 
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
	* {@inheritdoc}
	*/
	static public $notification_option = [
		'lang'	=> 'UB_NOTIFICATION_TYPE_COMMENTS',
		'group'	=> 'UB_NOTIFICATION_GROUP',
	];

	/**
	* {@inheritdoc}
	*/
	public function get_type()
	{
		return 'mrgoldy.ultimateblog.notification.type.comments';
	}

	/**
	* {@inheritdoc}
	*/
	public function is_available()
	{
		return $this->config['ub_enable_comments'];
	}

	/**
	* {@inheritdoc}
	*/
	public static function get_item_id($data)
	{
		return $data['notification_id'];
	}

	/**
	* {@inheritdoc}
	*/
	public static function get_item_parent_id($data)
	{
		// No parent
		return 0;
	}

	/**
	* {@inheritdoc}
	*/
	public function find_users_for_notification($data, $options = array())
	{
		return $this->check_user_notification_options([$data['author_id']]);
	}

	/**
	* {@inheritdoc}
	*/
	public function users_to_query()
	{
		return [];
	}

	/**
	* {@inheritdoc}
	*/
	public function get_title()
	{
		$blog_title = $this->get_data('blog_title');
		$username = $this->user_loader->get_username($this->get_data('actionee_id'), 'no_profile', false, false, true);
		$notification_title = $this->language->lang('UB_NOTIFICATION_TYPE_COMMENTS_MSG', $username, $blog_title);
		return $notification_title;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_url()
	{
		$blog_id = $this->get_data('blog_id');
		$blog_title = $this->get_data('blog_title');
		$comment_id = $this->get_data('comment_id');
		$url = $this->helper->route('mrgoldy_ultimateblog_view', ['blog_id' => (int) $blog_id, 'title' => urlencode($blog_title)]) . '#' . (int) $comment_id;
		return $url;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_email_template()
	{
		return false;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_email_template_variables()
	{
		return [];
	}

	/**
	* {@inheritdoc}
	*/
	public function create_insert_array($data, $pre_create_data = [])
	{
		$this->set_data('notification_id', $data['notification_id']);
		$this->set_data('actionee_id', $data['actionee_id']);
		$this->set_data('actionee_username', $data['actionee_username']);
		$this->set_data('author_id', $data['author_id']);
		$this->set_data('blog_id', $data['blog_id']);
		$this->set_data('blog_title', $data['blog_title']);
		$this->set_data('comment_id', $data['comment_id']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
