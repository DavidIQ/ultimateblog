<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog;

/**
 * Ultimate Blog Extension base
 *
 * It is recommended to remove this file from
 * an extension if it is not going to be used.
 */
class ext extends \phpbb\extension\base
{
	/**
	* Check whether or not the extension can be enabled.
	* The current phpBB version should meet or exceed
	* the minimum version required by this extension:
	*
	* Requires phpBB 3.2.0 due to new faq controller route for bbcodes,
	* the revised notifications system, font awesome and the text reparser.
	*
	* @return bool
	* @access public
	*/
	public function is_enableable()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=');
	}

	protected static $notification_types = array(
		'mrgoldy.ultimateblog.notification.type.ultimateblog',
		'mrgoldy.ultimateblog.notification.type.comments',
		'mrgoldy.ultimateblog.notification.type.rating',
		'mrgoldy.ultimateblog.notification.type.views',
	);

	/**
	 * Enable our notifications.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/* @var $phpbb_notifications \phpbb\notification\manager */
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach (self::$notification_types as $type)
				{
					$phpbb_notifications->enable_notifications($type);
				}
				return 'notifications';
			break;
			default:
				// Run parent enable step method
				return parent::enable_step($old_state);
			break;
		}
	}
	/**
	 * Disable our notifications.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/* @var $phpbb_notifications \phpbb\notification\manager */
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach (self::$notification_types as $type)
				{
					$phpbb_notifications->disable_notifications($type);
				}
				return 'notifications';
			break;
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
			break;
		}
	}
	/**
	 * Purge our notifications
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/* @var $phpbb_notifications \phpbb\notification\manager */
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach (self::$notification_types as $type)
				{
					$phpbb_notifications->purge_notifications($type);
				}
				return 'notifications';
			break;
			default:
				// Run parent purge step method
				return parent::purge_step($old_state);
			break;
		}
	}
}
