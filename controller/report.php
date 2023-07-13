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

use phpbb\exception\http_exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
* Ultimate Blog Report controller
*/
class report
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\report\report_reason_list_provider */
	protected $report_reason_provider;

	/** @var string Ultimate Blog blogs table */
	protected $ub_blogs_table;

	/** @var string Ultimate Blog comments table */

	/** @var string Ultimate Blog reports table */
	protected $ub_reports_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth										$auth				Authentication object
	 * @param \phpbb\config\config									$config			Config object
	 * @param \phpbb\db\driver\driver_interface						$db				Database object
	 * @param \phpbb\controller\helper								$helper			Controller helper object
	 * @param \phpbb\language\language								$lang				Language object
	 * @param \phpbb\log\log											$log				Log object
	 * @param \phpbb\request\request|\phpbb\request\request_interface $request			Request object
	 * @param \phpbb\template\template								$template			Template object
	 * @param \phpbb\user											 $user				User object
	 * @param \phpbb\report\report_reason_list_provider				$ui_provider
	 * @param string													$ub_blogs_table	Ultimate Blog blogs table
	 * @param string													$ub_comments_table Ultimate Blog comments table
	 * @param string													$ub_reports_table	Ultimate Blog reports table
	 * @internal param \report\reason\report_reason_list_provider $uiprovider Report reason list provider
	 * @access	public
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\log\log $log, \phpbb\request\request_interface $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\report\report_reason_list_provider $ui_provider, $ub_blogs_table, $ub_comments_table, $ub_reports_table)
	{
		$this->auth			= $auth;
		$this->config		= $config;
		$this->db			= $db;
		$this->helper		= $helper;
		$this->lang			= $lang;
		$this->log			= $log;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->report_reason_provider	= $ui_provider;
		$this->ub_blogs_table			= $ub_blogs_table;
		$this->ub_comments_table		= $ub_comments_table;
		$this->ub_reports_table			= $ub_reports_table;
	}

	/**
	* @param int		$blog_id	ID of the blog
	* @param int		$id			ID of the entity to report
	* @param string		$mode		Mode (either 'blog' or 'comment')
	* @return \Symfony\Component\HttpFoundation\Response a Symfony response object
	*/
	public function handle($blog_id, $id, $mode)
	{
		# Add the MCP & Report language
		$this->lang->add_lang('report', 'mrgoldy/ultimateblog');
		$this->lang->add_lang('mcp');

		# Request the variables
		$user_notify	= ($this->user->data['is_registered']) ? $this->request->variable('notify', 0) : false;
		$reason_id		= $this->request->variable('reason_id', 0);
		$report_text	= $this->request->variable('report_text', '', true);
		$submit			= $this->request->variable('submit', '');
		$cancel			= $this->request->variable('cancel', '');

		# Add a security key
		add_form_key('blog_report');

		# Set up redirect URL
		$redirect_url = $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id));

		# Has the report been cancelled?
		if ($cancel)
		{
			return new RedirectResponse($redirect_url, 302);
		}

		# Check if there is a valid mode
		if (!in_array($mode, array('blog', 'comment')))
		{
			throw new http_exception(404, $this->lang->lang('BLOG_REPORT_INVALID_MODE'));
		}

		# Check if the user has permission to report
		if (!$this->auth->acl_get('u_ub_report'))
		{
			throw new http_exception(403, $this->lang->lang('BLOG_REPORT_NO_PERMISSION'));
		}

		# Check if the blog or comment has already been reported
		$already_reported = $this->check_already_reported($id, $mode);
		if ($already_reported)
		{
			$message = $this->lang->lang('BLOG_REPORT_ALREADY_' . strtoupper($mode));
			$message .= '<br><br><a href="' . $redirect_url . '">' . $this->lang->lang('BLOG_REPORT_RETURN') . '</a>';
			return $this->helper->message($message);
		}

		# Check if the blog or comment even exists.
		$exists = $this->check_existance($id, $mode);
		if (!$exists)
		{
			$message = $this->lang->lang('BLOG_REPORT_NO_' . strtoupper($mode));
			$message .= '<br><br><a href="' . $redirect_url . '">' . $this->lang->lang('BLOG_REPORT_RETURN') . '</a>';
			throw new http_exception(403, $message);
		}

		# Check form key for security
		if ($submit && !check_form_key('blog_report'))
		{
			throw new http_exception(400, $this->lang->lang('BLOG_REPORT_FORM_INVALID'));
		}

		if ($submit)
		{
			# Add to report table
			$report_array = array(
				'reason_id'		=> (int) $reason_id,
				'blog_id'		=> (int) $blog_id,
				'comment_id'	=> $mode === 'comment' ? (int) $id : (int) 0,
				'user_id'		=> (int) $this->user->data['user_id'],
				'user_notify'	=> (int) $user_notify,
				'report_closed'	=> (int) 0,
				'report_time'	=> time(),
				'report_text'	=> (string) $report_text,
			);

			$sql = 'INSERT INTO ' . $this->ub_reports_table . ' ' . $this->db->sql_build_array('INSERT', $report_array);
			$this->db->sql_query($sql);

			# Mark as reported in the respecitve $mode table
			switch ($mode)
			{
				case 'blog':
					$sql = 'UPDATE ' . $this->ub_blogs_table . ' SET blog_reported = 1 WHERE blog_id = ' . (int) $id;
				break;

				case 'comment':
					$sql = 'UPDATE ' . $this->ub_comments_table . ' SET comment_reported = 1 WHERE comment_id = ' . (int) $id;
				break;
			}
			$this->db->sql_query($sql);

			# Add it to the log
			$this->log->add('user', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_' . strtoupper($mode) . '_REPORTED', time(), array('reportee_id' => (int) $this->user->data['user_id']));

			# Send notification

			# Show success message (notify or not?)
			$this->helper->assign_meta_refresh_var(3, $redirect_url);
			$message = $this->lang->lang('BLOG_REPORT_SUCCESS_' . strtoupper($mode));
			$message .= !empty($user_notify) ? '<br>' . $this->lang->lang('BLOG_REPORT_NOTIFY') : '';
			$message .= '<br><br><a href="' . $redirect_url . '">' . $this->lang->lang('BLOG_REPORT_RETURN') . '</a>';
			return $this->helper->message($message);
		}

		# Setting up an rendering template
		$this->report_reason_provider->display_reasons($reason_id);

		$action_params = $mode === 'blog' ? array('blog_id' => (int) $blog_id, 'id' => (int) $id, 'mode' => 'blog') : array('blog_id' => (int) $blog_id, 'id' => (int) $id, 'mode' => 'comment');

		$this->template->assign_vars(array(
			'REPORT_TEXT'		=> $report_text,

			'S_CAN_NOTIFY'		=> ($this->user->data['is_registered']) ? true : false,
			'S_IN_REPORT'		=> true,
			'S_NOTIFY'			=> $user_notify,
			'S_REPORT_ACTION'	=> $this->helper->route('mrgoldy_ultimateblog_report', $action_params),
			'S_REPORT_COMMENT'	=> ($mode === 'comment') ? true : false,
		));

		$page_title = ($mode === 'blog') ? $this->lang->lang('BLOG_REPORT_BLOG') : $this->lang->lang('BLOG_REPORT_COMMENT');
		return $this->helper->render('ub_report_body.html', $page_title);
	}

	/**
	 * @param $id
	 * @param $mode
	 * @return bool
	 */
	private function check_already_reported($id, $mode)
	{
		switch ($mode)
		{
			case 'blog':
				$sql = 'SELECT blog_id FROM ' . $this->ub_reports_table . ' WHERE report_closed = 0 AND comment_id = 0 AND blog_id = ' . (int) $id;
			break;

			case 'comment':
				$sql = 'SELECT comment_id FROM ' . $this->ub_reports_table . ' WHERE report_closed = 0 AND comment_id = ' . (int) $id;
			break;
		}

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return !empty($row) ? true : false;
	}

	/**
	 * @param $id
	 * @param $mode
	 * @return bool
	 */
	private function check_existance($id, $mode)
	{
		switch ($mode)
		{
			case 'blog':
				$sql = 'SELECT blog_id FROM ' . $this->ub_blogs_table . ' WHERE blog_id = ' . (int) $id;
			break;

			case 'comment':
				$sql = 'SELECT comment_id FROM ' . $this->ub_comments_table . ' WHERE comment_id = ' . (int) $id;
			break;
		}

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return !empty($row) ? true : false;
	}
}
