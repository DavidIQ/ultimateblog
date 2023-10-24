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
use mrgoldy\ultimateblog\constants;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class posting
 *
 * @package mrgoldy\ultimateblog\controller
 */
class posting
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\files\factory */
	protected $files_factory;

	/** @var \phpbb\filesystem */
	protected $filesystem;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var \mrgoldy\ultimateblog\core\functions */
	protected $func;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth					 $auth			Authentication object
	 * @param \phpbb\config\config				 $config			Config object
	 * @param \phpbb\files\factory				 $files_factory
	 * @param \phpbb\filesystem\filesystem		 $filesystem		Filesystem object
	 * @param \phpbb\controller\helper			 $helper			Controller helper object
	 * @param \phpbb\language\language			 $lang			Language object
	 * @param \phpb\log\log|\phpbb\log\log		 $log			 Log boject
	 * @param \phpbb\pagination					$pagination		Pagination object
	 * @param \phpbb\textformatter\s9e\parser		$parser			Parser object
	 * @param \phpbb\path_helper					$path_helper	 Path helper
	 * @param \phpbb\textformatter\s9e\renderer	$renderer		Renderer object
	 * @param \phpbb\request\request				$request		 Request object
	 * @param \phpbb\template\template			 $template		Template object
	 * @param \phpbb\user							$user			User object
	 * @param \phpbb\textformatter\s9e\utils		$utils			Utils object
	 * @param string								$php_ext		 phpEx
	 * @param string								$phpbb_root_path phpBB root path
	 * @param \mrgoldy\ultimateblog\core\functions $func			Ultimate Blog functions
	 * @internal param \phpbb\files\factory $files Files factory
	 * @access	public
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\files\factory $files_factory, \phpbb\filesystem\filesystem $filesystem, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\log\log $log, \phpbb\notification\manager $notification_manager, \phpbb\pagination $pagination, \phpbb\textformatter\s9e\parser $parser, \phpbb\path_helper $path_helper, \phpbb\textformatter\s9e\renderer $renderer, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\textformatter\s9e\utils $utils, $php_ext, $phpbb_root_path, $func)
	{
		$this->auth					= $auth;
		$this->config				= $config;
		$this->files_factory		= $files_factory;
		$this->filesystem			= $filesystem;
		$this->helper				= $helper;
		$this->lang					= $lang;
		$this->log					= $log;
		$this->notification_manager	= $notification_manager;
		$this->pagination			= $pagination;
		$this->parser				= $parser;
		$this->path_helper			= $path_helper;
		$this->renderer				= $renderer;
		$this->request				= $request;
		$this->template				= $template;
		$this->user					= $user;
		$this->utils				= $utils;
		$this->php_ext				= $php_ext;
		$this->phpbb_root_path		= $phpbb_root_path;
		$this->func					= $func;
	}

	/**
	 * @param int $blog_id
	 * @param	 $mode
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle($mode, $blog_id = 0)
	{
		# Add the Ultimate Blog posting language
		$this->lang->add_lang('posting', 'mrgoldy/ultimateblog');

		# Check Ultimate Blog status, add language, smilies and BBCodes
		$this->func->ub_status();
		$this->lang->add_lang('posting');
		include_once($this->phpbb_root_path . 'includes/functions_posting.' . $this->php_ext);
		include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
		generate_smilies('inline', 0);
		display_custom_bbcodes();

		# Set modes and other variables
		$add	= $mode === 'add' ? true : false;
		$delete	= $mode === 'delete' ? true : false;
		$edit	= $mode === 'edit' ? true : false;
		$errors = $private_categories = array();

		# Request some variables
		$submit			= $this->request->is_set_post('submit');
		$preview		= $this->request->is_set_post('preview');
		$cancel			= $this->request->is_set_post('cancel');
		$category_id	= $this->request->variable('cid', 0);

		# Check for valid $mode
		if (!in_array($mode, array('add', 'delete', 'edit')))
		{
			throw new http_exception(404, $this->lang->lang('BLOG_ERROR_INVALID_MODE'));
		}

		# Grab blog2edit data (for edit or delete or add)
		if ($edit || $delete || $add)
		{
			$blog_to_edit = $this->func->blog_data($blog_id, $this->user->data['user_id']);
		}

		# Check if the blog exists
		if (empty($blog_to_edit) && ($edit || $delete))
		{
			throw new http_exception(404, $this->lang->lang('BLOG_ERROR_NO_BLOG'));
		}

		# Set up a list of category id's
		if ($preview || $submit)
		{
			$current_categories = $this->request->variable('blog_categories', array(0));
		}
		else if ($edit)
		{
			$current_categories = explode(',', $blog_to_edit['categories']);
		}
		else
		{
			$current_categories = array($category_id);
		}

		# Get a category list
		$categories = $this->func->category_list();
		$private_categories = array();

		# Set up category template block
		foreach ($categories as $category)
		{
			# Set up a list of private categories
			$category['is_private'] ? $private_categories[] = $category['category_id'] : false;

			$this->template->assign_block_vars('categories', array(
				'ID'	=> $category['category_id'],
				'NAME'	=> $category['category_name'],

				'S_CURRENT_CATEGORY'	=> in_array($category['category_id'], $current_categories),
				'S_IS_PRIVATE'			=> $category['is_private'],
			));
		}

		# Check for cancel
		if ($cancel)
		{
			if ($edit || $delete)
			{
				$redirect_url = $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id, 'title' => urlencode($blog_to_edit['blog_title'])));
			}
			else if (!empty($category_id))
			{
				$redirect_url = $this->helper->route('mrgoldy_ultimateblog_category', array('category_id' => (int) $category_id, 'title' => urlencode($categories[$category_id]['category_name'])));
			}
			else
			{
				$redirect_url = $this->helper->route('mrgoldy_ultimateblog_index');
			}

			return new RedirectResponse ($redirect_url, 302);
		}

		# Check permissions
		if ($add && !$this->auth->acl_get('u_ub_post'))
		{
			throw new http_exception(403, $this->lang->lang('BLOG_ERROR_CANT_ADD'));
		}
		if ($edit && ((!$this->auth->acl_get('u_ub_edit') || $this->user->data['user_id'] != $blog_to_edit['author_id']) && !$this->auth->acl_get('m_ub_edit')))
		{
			throw new http_exception(403, $this->lang->lang('BLOG_ERROR_CANT_EDIT'));
		}
		if ($delete && ((!$this->auth->acl_get('u_ub_delete') || $this->user->data['user_id'] != $blog_to_edit['author_id']) && !$this->auth->acl_get('m_ub_delete')))
		{
			throw new http_exception(403, $this->lang->lang('BLOG_ERROR_CANT_DELETE'));
		}

		# If delete (confirm box)
		if ($delete)
		{
			if (confirm_box(true))
			{
				# Delete the blog
				$this->func->blog_delete($blog_id);

				# Delete the blog image
				if ($this->filesystem->exists($this->phpbb_root_path . $this->config['ub_image_dir'] . '/' . $blog_to_edit['blog_image']))
				{
					$this->filesystem->remove($this->phpbb_root_path . $this->config['ub_image_dir'] . '/' . $blog_to_edit['blog_image']);
				}

				# Add it to the log
				$log_mode = $blog_to_edit['author_id'] == $this->user->data['user_id'] ? 'user' : 'mod';
				$this->log->add($log_mode, $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_BLOG_DELETED', time(), array($blog_to_edit['blog_title']));

				return new RedirectResponse($this->helper->route('mrgoldy_ultimateblog_index'), 302);
			}
			else
			{
				confirm_box(false, $this->lang->lang('BLOG_DELETE_CONFIRM', $blog_to_edit['blog_title']));
			}
		}

		# Add a form key for security
		add_form_key('blog_add');

		# Check for changed author
		$author = $this->request->variable('blog_change_author', '', true);
		if ($this->auth->acl_get('m_ub_changeauthor') && !empty($author))
		{
			$changed_author = $this->func->get_user_info($this->request->variable('blog_change_author', '', true));
		}

		# If submit or preview, request blog variables (blog to update)
		if ($submit || $preview)
		{
			# Blog to update
			$blog_to_update = array(
				'author_id'			=> $author && $changed_author ? $changed_author['user_id'] : ($edit ? $blog_to_edit['author_id'] : $this->user->data['user_id']),
				'blog_approved'		=> $edit ? $blog_to_edit['blog_approved'] : ($this->auth->acl_get('u_ub_noapprove') || $this->auth->acl_get('m_ub_approve')),
				'blog_date'			=> $edit ? $blog_to_edit['blog_date'] : time(),
				'blog_description'	=> $this->request->variable('blog_description', '', true),
				'blog_title'		=> $this->request->variable('blog_title', '', true),
				'enable_bbcode'		=> !$this->request->variable('disable_bbcode', false),
				'enable_smilies'	=> !$this->request->variable('disable_smilies', false),
				'enable_magic_url'	=> !$this->request->variable('disable_magic_url', false),
				'friends_only'		=> $this->request->variable('friends_only', false),
				'locked_comments'	=> $this->auth->acl_get('m_ub_lock_comments') ? $this->request->variable('locked_comments', false) : ($edit ? $blog_to_edit['locked_comments'] : false),
				'locked_edit'		=> $this->auth->acl_get('m_ub_lock_edit') ? $this->request->variable('locked_edit', false) : ($edit ? $blog_to_edit['locked_edit'] : false),
				'locked_rating'		=> $this->auth->acl_get('m_ub_lock_rating') ? $this->request->variable('locked_rating', false) : ($edit ? $blog_to_edit['locked_rating'] : false),
			);

			# Set up blog text for storage
			!$blog_to_update['enable_bbcode'] || !$this->config['ub_allow_bbcodes'] ? $this->parser->disable_bbcodes() : $this->parser->enable_bbcodes();
			!$blog_to_update['enable_smilies'] || !$this->config['ub_allow_smilies'] ? $this->parser->disable_smilies() : $this->parser->enable_smilies();
			!$blog_to_update['enable_magic_url'] || !$this->config['ub_allow_magic_url'] ? $this->parser->disable_magic_url() : $this->parser->enable_magic_url();
			$blog_text = $this->request->variable('blog_text', '', true);
			$blog_text = htmlspecialchars_decode($blog_text, ENT_COMPAT);
			$blog_to_update['blog_text'] = $this->parser->parse($blog_text);

			# Categories to update
			if ($edit && !$this->auth->acl_get('u_ub_post_private'))
			{
				$new_categories = array_merge($this->request->variable('blog_categories', array(0)), array_intersect(explode(',', $blog_to_edit['categories']), $private_categories));
			}
			else
			{
				$new_categories = $this->request->variable('blog_categories', array(0));
			}
		}

		# If submit or preview, same if as above, but easier to have all errors in one, seperate if-statement
		if ($submit || $preview)
		{
			# Check form key for security
			!check_form_key('blog_add') ? $errors[] = $this->lang->lang('FORM_INVALID') : false;

			# Check if changed author exists when editing
			$author ? (!$changed_author ? $errors[] = $this->lang->lang('NO_USER') : false) : false;

			# Check if there is an edit reason supplied
			$edit_reason = $this->request->variable('edit_reason', '', true);

			# Check if a blog title is present
			empty($blog_to_update['blog_title']) ? $errors[] = $this->lang->lang('BLOG_ERROR_NO_TITLE') : false;

			# Check if categories are selected
			empty($new_categories) ? $errors[] = $this->lang->lang('BLOG_ERROR_CATEGORY_NONE') : false;

			# Check if the description is the correct length
			(strlen($blog_to_update['blog_description']) < constants::BLOG_DESC_MINIMUM || strlen($blog_to_update['blog_description'] > constants::BLOG_DESC_MAXIMUM)) ? $errors[] = $this->lang->lang('BLOG_ERROR_DESC_BOUNDS', strlen($blog_to_update['blog_description'])) : false;

			# Check if the text is the correct length
			(strlen($this->utils->clean_formatting($blog_to_update['blog_text'])) < $this->config['ub_blog_min_chars']) ? $errors[] = $this->lang->lang('BLOG_ERROR_MIN_CHARS', $this->config['ub_blog_min_chars'], strlen($this->utils->clean_formatting($blog_to_update['blog_text']))) : false;

			# Get an instance of the files upload class
			$upload = $this->files_factory->get('upload')
					-> set_max_filesize($this->config['ub_image_size'] * 1000)
					-> set_allowed_extensions(array('png', 'jpg', 'jpeg', 'gif'));

			$upload_file = $upload->handle_upload('files.types.form', 'blog_image');

			# Check for errors, only when: Adding OR Editing and a file is found
			if ($add || ($edit && $upload_file->get('uploadname')))
			{
				if (!empty($upload_file->error) || !$upload_file->is_image() || !$upload_file->is_uploaded() || $upload_file->init_error())
				{
					$errors[] = $this->lang->lang('BLOG_ERROR_IMAGE');
					$upload_file->remove();
					foreach ($upload_file->error as $file_error)
					{
						$errors[] = $file_error;
					}
				}
			}
		}

		# If submit, not preview and empty errors
		if ($submit && !$preview && empty($errors))
		{
			if ($add || ($edit && $upload_file->get('uploadname')))
			{
				# If editing and uploading a new file, delete the old file
				if ($edit && !empty($blog_to_edit['blog_image']) && $this->filesystem->exists($this->phpbb_root_path . $this->config['ub_image_dir'] . '/' . $blog_to_edit['blog_image']))
				{
					$this->filesystem->remove($this->phpbb_root_path . $this->config['ub_image_dir'] . '/' . $blog_to_edit['blog_image']);
				}

				# We're adding the new file
				$upload_file->clean_filename('unique_ext', 'ub_');
				$upload_file->move_file($this->config['ub_image_dir'], true, true, 0644);
				@chmod($this->phpbb_root_path . $this->config['ub_image_dir'] . $upload_file->get('realname'), 0644);

				$blog_to_update['blog_image'] = $upload_file->get('realname');
			}

			# Set blog id when editing a blog
			$edit ? $blog_to_update['blog_id'] = (int) $blog_id : false;

			# Add or update the blog
			$blog_id = $this->func->blog_update($blog_to_update);

			# Add / Update blog <> category corralation
			if ($add)
			{
				$this->func->corralation_add((int) $blog_id, $new_categories);
			}
			else
			{
				$old_categories = explode(',', $blog_to_edit['categories']);
				$categories_to_del = array_diff($old_categories, $new_categories);
				$categories_to_add = array_diff($new_categories, $old_categories);

				$this->func->corralation_add((int) $blog_id, $categories_to_add);
				$this->func->corralation_delete((int) $blog_id, $categories_to_del);
			}

			# Add edit reason
			if ($edit)
			{
				$this->func->edit_add(array(
					'blog_id'	=> (int) $blog_id,
					'edit_text'	=> $edit_reason,
					'edit_time'	=> time(),
					'editor_id'	=> $this->user->data['user_id'],
				));
			}

			# Check if user editing is author, if not, send the author a notification
			if (($this->user->data['user_id'] != $blog_to_edit['author_id']) && !$add)
			{
				# Increment our notifications sent counter
				$this->config->increment('ub_notification_id', 1);

				# Send out notification
				$this->notification_manager->add_notifications('mrgoldy.ultimateblog.notification.type.ultimateblog', array(
					'actionee_id'		=> (int) $this->user->data['user_id'],
					'author_id'			=> (int) $blog_to_edit['author_id'],
					'blog_id'			=> (int) $blog_id,
					'blog_title'		=> $blog_to_edit['blog_title'],
					'comment_id'		=> 0,
					'notification_id'	=> $this->config['ub_notification_id'],
					'notification_type'	=> $edit ? 'blog_edited' : 'blog_deleted',
				));
			}

			# Add it to the log
			$log_mode = $mode == 'add' ? 'user' : ($blog_to_edit['author_id'] == $this->user->data['user_id'] ? 'user' : 'mod');
			$this->log->add('user', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_BLOG_' . strtoupper($mode) . 'ED', time(), array($blog_to_update['blog_title'], 'reportee_id' => (int) $this->user->data['user_id']));

			# Confirmation message
			$blog_url = $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id, 'title' => urlencode($blog_to_update['blog_title'])));
			trigger_error($this->lang->lang('BLOG_' . strtoupper($mode) . 'ED', $blog_to_update['blog_title'], $blog_url));
		}

		# Assign template variables
		$this->template->assign_vars(array(
			'S_ERROR'				=> !empty($errors),
			'ERROR_MSG'				=> !empty($errors) ? implode('<br />', $errors) : '',

			'S_BLOG_PREVIEW'		=> $preview,
			'BLOG_PREVIEW'			=> $preview ? $this->renderer->render($blog_to_update['blog_text']) : '',

			'BLOG_CHANGE_AUTHOR'	=> $this->auth->acl_get('m_ub_changeauthor') && ($preview || $submit) ? $author : '',
			'BLOG_DESCRIPTION'		=> $preview || $submit ? $blog_to_update['blog_description'] : ($edit ? $blog_to_edit['blog_description'] : ''),
			'BLOG_DESCRIPTION_MIN'	=> constants::BLOG_DESC_MINIMUM,
			'BLOG_DESCRIPTION_MAX'	=> constants::BLOG_DESC_MAXIMUM,
			'BLOG_EDIT_REASON'		=> $edit && ($submit || $preview) ? $edit_reason : '',
			'BLOG_IMAGE'			=> $edit ? $this->path_helper->get_web_root_path() . $this->config['ub_image_dir'] . '/' . $blog_to_edit['blog_image'] : false,
			'BLOG_TEXT'				=> $preview || $submit ? $this->utils->unparse($blog_to_update['blog_text']) : ($edit ? $this->utils->unparse($blog_to_edit['blog_text']) : ''),
			'BLOG_TITLE'			=> $preview || $submit ? $blog_to_update['blog_title'] : ($edit ? $blog_to_edit['blog_title'] : ''),

			'BBCODE_STATUS'			=> $this->config['ub_allow_bbcodes'] ? $this->lang->lang('BBCODE_IS_ON', '<a href="' . $this->helper->route('phpbb_help_bbcode_controller') . '">', '</a>') : $this->lang->lang('BBCODE_IS_OFF', '<a href="' . $this->helper->route('phpbb_help_bbcode_controller') . '">', '</a>'),
			'SMILIES_STATUS'		=> $this->config['ub_allow_smilies'] ? $this->lang->lang('SMILIES_ARE_ON') : $this->lang->lang('SMILIES_ARE_OFF'),
			'URL_STATUS'			=> $this->config['ub_allow_magic_url'] ? $this->lang->lang('URL_IS_ON') : $this->lang->lang('URL_IS_OFF'),

			'S_BBCODE_ALLOWED'		=> $this->config['ub_allow_bbcodes'],
			'S_BBCODE_DISABLED'		=> $preview || $submit ? !$blog_to_update['enable_bbcode'] : ($edit ? !$blog_to_edit['enable_bbcode'] : false),
			'S_SMILIES_ALLOWED'		=> $this->config['ub_allow_smilies'],
			'S_SMILIES_DISABLED'	=> $preview || $submit ? !$blog_to_update['enable_smilies'] : ($edit ? !$blog_to_edit['enable_smilies'] : false),
			'S_MAGIC_URL_ALLOWED'	=> $this->config['ub_allow_magic_url'],
			'S_MAGIC_URL_DISABLED'	=> $preview || $submit ? !$blog_to_update['enable_magic_url'] : ($edit ? !$blog_to_edit['enable_magic_url'] : false),

			'S_BLOG_ADD'				=> $add,
			'S_BLOG_CAN_LOCK_COMMENTS'	=> $this->auth->acl_get('m_ub_lock_comments'),
			'S_BLOG_CAN_LOCK_EDIT'		=> $this->auth->acl_get('m_ub_edit_lock'),
			'S_BLOG_CAN_LOCK_RATING'	=> $this->auth->acl_get('m_ub_lock_rating'),
			'S_BLOG_CAN_MODERATE'		=> $this->auth->acl_get('m_ub_lock_comments') || $this->auth->acl_get('m_ub_edit_lock') || $this->auth->acl_get('m_ub_lock_rating'),
			'S_BLOG_CHANGE_AUTHOR'		=> $this->auth->acl_get('m_ub_changeauthor'),
			'S_BLOG_EDIT'				=> $edit,
			'S_BLOG_ENABLED_COMMENTS'	=> $this->config['ub_enable_comments'],
			'S_BLOG_ENABLED_RATING'		=> $this->config['ub_enable_rating'],
			'S_BLOG_FRIENDS_ENABLED'	=> $this->config['ub_enable_friends_only'],
			'S_BLOG_FRIENDS_ONLY'		=> $preview || $submit ? $blog_to_update['friends_only'] : ($edit ? $blog_to_edit['friends_only'] : false),
			'S_BLOG_LOCKED_COMMENTS'	=> $preview || $submit ? $blog_to_update['locked_comments'] : ($edit ? $blog_to_edit['locked_comments'] : false),
			'S_BLOG_LOCKED_EDIT'		=> $preview || $submit ? $blog_to_update['locked_edit'] : ($edit ? $blog_to_edit['locked_edit'] : false),
			'S_BLOG_LOCKED_RATING'		=> $preview || $submit ? $blog_to_update['locked_rating'] : ($edit ? $blog_to_edit['locked_rating'] : false),
			'S_BLOG_POST_PRIVATE'		=> $this->auth->acl_get('u_ub_post_private'),

			'U_ACTION_BLOG_ADD'		=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'add')),
			'U_ACTION_BLOG_EDIT'	=> $this->helper->route('mrgoldy_ultimateblog_posting', array('mode' => 'edit', 'blog_id' => (int) $blog_id)),
		));

		# Breadcrumbs
		$this->template->assign_block_vars('navlinks', array(
			'FORUM_NAME'	=> $this->config['ub_title'],
			'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_index'),
		));

		if (!empty($blog_id))
		{
			$this->template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $blog_to_edit['blog_title'],
				'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id, 'title' => urlencode($blog_to_edit['blog_title']))),
			));
		}
		else if (!empty($category_id))
		{
			$this->template->assign_block_vars('navlinks', array(
				'FORUM_NAME'	=> $categories[$category_id]['category_name'],
				'U_VIEW_FORUM'	=> $this->helper->route('mrgoldy_ultimateblog_category', array('category_id' => (int) $category_id, 'title' => urlencode($categories[$category_id]['category_name']))),
			));
		}

		return $this->helper->render('ub_blog_edit.html', $this->config['ub_title'] . ' - ' . $this->lang->lang('BLOG_' . strtoupper($mode)));
	}

	/**
	 * @param $blog_id
	 * @param $mode
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function misc($mode, $blog_id)
	{
		# Add the Ultimate Blog posting language
		$this->lang->add_lang('posting', 'mrgoldy/ultimateblog');

		# Check if Ultimate Blog is enabled and if the user has the 'view' permission
		$this->func->ub_status();

		# Get blog data
		$blog = $this->func->blog_data((int) $blog_id, (int) $this->user->data['user_id']);

		switch ($mode)
		{
			case 'rate':
				if ($this->request->is_ajax())
				{
					# Check if rating is enabled and user can rate
					if ($this->config['ub_enable_rating'] && $this->auth->acl_get('u_ub_rate'))
					{
						$score_obj = $this->request->variable('score', 0);
						$score = json_decode(htmlspecialchars_decode($score_obj));
						$rating_data = $this->func->rating_add($blog['blog_id'], $this->user->data['user_id'], $score);

						# if $rating_added, add it to the LOG
						if ($rating_data['rating_added'])
						{
							$this->log->add('user', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_BLOG_RATED', time(), array($rating_data['blog_title'], $score));

							# If a new rating is added, check if the multiplier is not 0, otherwise the notification threshold is reached
							if (!empty($rating_data['rating_multiplier']))
							{
								# Increment our notifications sent counter
								$this->config->increment('ub_notification_ratings_id', 1);

								# Send out notification
								$this->notification_manager->add_notifications('mrgoldy.ultimateblog.notification.type.rating', array(
									'blog_id'			=> (int) $blog_id,
									'multiplier'		=> (int) $rating_data['rating_multiplier'],
									'notification_id'	=> $this->config['ub_notification_ratings_id'],
									'author_id' 		=> (int) $blog['author_id'],
								));
							}
						}
					}

					# Need to return an empty response, otherwise 500 Internal Server error (???)
					return new JsonResponse();
				}
			break;

			case 'editdelete':
				$edit_id = (int) $this->request->variable('eid', 0);

				# Check permission
				if (!$this->auth->acl_get('m_ub_edit_delete'))
				{
					throw new http_exception(403, $this->lang->lang('BLOG_ERROR_CANT_EDIT_DELETE'));
				}

				# Delete the edit reason
				$edit_array = $this->func->edit_delete($edit_id);

				# Add it to the log
				$this->log->add('mod', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_BLOG_EDIT_DELETED', time(), array($edit_array['edit_user'], $edit_array['edit_text'], $edit_array['blog_title']));

				# Return to the blog view
				redirect($this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id, 'title' => urlencode($edit_array['blog_title']))));
			break;

			case 'comment':
				$action = $this->request->variable('action', '');

				if ($this->request->is_ajax())
				{
					switch ($action)
					{
						case 'add':
							# Check permission
							if (!$this->auth->acl_get('u_ub_comment_post'))
							{
								throw new http_exception(403, $this->lang->lang('BLOG_ERROR_CANT_COMMENT'));
							}

							$parent_id = (int) $this->request->variable('pid', 0);
							$parent_author_id = (int) $this->request->variable('paid', 0);
							$comment_text = $this->request->variable('comment_text', '', true);
							$comment_text = htmlspecialchars_decode($comment_text, ENT_COMPAT);

							# Generate text for storage
							!$this->config['ub_allow_bbcodes'] ? $this->parser->disable_bbcodes() : $this->parser->enable_bbcodes();
							!$this->config['ub_allow_smilies'] ? $this->parser->disable_smilies() : $this->parser->enable_smilies();
							!$this->config['ub_allow_magic_url'] ? $this->parser->disable_magic_url() : $this->parser->enable_magic_url();
							$comment_array = array(
								'comment_approved'	=> $this->auth->acl_get('u_ub_comment_noapprove') || $this->auth->acl_get('m_ub_approve'),
								'comment_text'		=> $this->parser->parse($comment_text),
								'comment_time'		=> time(),
								'blog_id'			=> (int) $blog_id,
								'parent_id'			=> (int) $parent_id,
								'user_id'			=> (int) $this->user->data['user_id'],
							);

							# Add the comment
							$comment_id = $this->func->comment_add($comment_array);

							# If the commentor is not the blog author send to author
							if ($this->user->data['user_id'] != $blog['author_id'])
							{
								# Increment our notifications sent counter
								$this->config->increment('ub_notification_id', 1);

								# Send out notification
								$this->notification_manager->add_notifications('mrgoldy.ultimateblog.notification.type.comments', [
									'actionee_id'		=> (int) $this->user->data['user_id'],
									'actionee_username' => $this->user->data['username'],
									'author_id'			=> (int) $blog['author_id'],
									'blog_id'			=> (int) $blog_id,
									'blog_title'		=> $blog['blog_title'],
									'comment_id'		=> (int) $comment_id,
									'notification_id'	=> $this->config['ub_notification_id'],
								]);
							}
							
							# If parent ID is NOT 0, it's a reply and we send a notification to the original comment author, if it's not the same author
							if (!empty($parent_id) && ($this->user->data['user_id'] != $parent_author_id))
							{
								# Increment our notifications sent counter
								$this->config->increment('ub_notification_id', 1);

								# Send out notification
								$this->notification_manager->add_notifications('mrgoldy.ultimateblog.notification.type.ultimateblog', array(
									'actionee_id'		=> (int) $this->user->data['user_id'],
									'author_id'			=> (int) $parent_author_id,
									'blog_id'			=> (int) $blog_id,
									'blog_title'		=> $blog['blog_title'],
									'comment_id'		=> (int) $parent_id,
									'notification_id'	=> $this->config['ub_notification_id'],
									'notification_type'	=> 'comment_added',
								));
							}

							# Add it to the log
							$comment_param = !empty($parent_id) ? $parent_id : $comment_id;
							$comment_url = $this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => (int) $blog_id)) . '#' . (int) $comment_param;
							$this->log->add('user', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_COMMENT_ADDED', time(), array($comment_url, 'reportee_id' => $this->user->data['user_id']));
							redirect($comment_url);
						break;

						case 'delete':
							$author_id = $this->request->variable('aid', 0);
							$comment_id = $this->request->variable('cid', 0);

							if (!$this->auth->acl_get('m_ub_delete') && (!$this->auth->acl_get('u_ub_comment_delete') || $this->user->data['user_id'] != $author_id))
							{
								throw new http_exception(403, $this->lang->lang('BLOG_ERROR_CANT_COMMENT_DELETE'));
							}

							$reply_count = $this->func->comment_reply_count((int) $comment_id);

							if (confirm_box(true))
							{
								# Delete the comment
								$this->func->comment_delete((int) $comment_id);

								# Check if user deleting is author, if not, send the author a notification
								if ($this->user->data['user_id'] != $author_id)
								{
									# Increment our notifications sent counter
									$this->config->increment('ub_notification_id', 1);

									# Send out notification
									$this->notification_manager->add_notifications('mrgoldy.ultimateblog.notification.type.ultimateblog', array(
										'actionee_id'		=> (int) $this->user->data['user_id'],
										'author_id'			=> (int) $author_id,
										'blog_id'			=> (int) $blog_id,
										'blog_title'		=> $blog['blog_title'],
										'comment_id'		=> (int) $comment_id,
										'notification_id'	=> $this->config['ub_notification_id'],
										'notification_type'	=> 'comment_deleted',
									));
								}

								# Add it to the log
								$log_mode = $author_id == $this->user->data['user_id'] ? 'user' : 'mod';
								$this->log->add($log_mode, $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_COMMENT_DELETED', time(), array('reportee_id' => $this->user->data['user_id']));

								# Redirect the user back to the blog
								redirect($this->helper->route('mrgoldy_ultimateblog_view', array('blog_id' => $blog_id)));
							}
							else
							{
								confirm_box(false, $this->user->lang('BLOG_COMMENTS_DELETE_CONFIRM') . (!empty($reply_count) ? '<br>'. $this->lang->lang('BLOG_COMMENTS_DELETE_REPLIES') : ''), build_hidden_fields(array(
									'blog_id'	=> $blog_id,
									'mode'		=> $mode,
									'aid'		=> $author_id,
									'cid' 		=> $comment_id,
									'action'	=> $action,
								)));
							}
						break;

						case 'edit':
							$comment_id = $this->request->variable('cid', 0);

							if ($this->request->is_ajax())
							{
								$comment_row = $this->func->comment_info((int) $comment_id);

								# Check permission
								if (!$this->auth->acl_get('m_ub_edit') && (!$this->auth->acl_get('u_ub_comment_edit') || $this->user->data['user_id'] != $comment_row['user_id']))
								{
									throw new http_exception(403, $this->lang->lang('BLOG_ERROR_CANT_COMMENT_EDIT'));
								}

								return new JsonResponse(array(
									'author_id'		=> (int) $comment_row['user_id'],
									'comment_id'	=> (int) $comment_id,
									'comment_text'	=> $this->utils->unparse($comment_row['comment_text']),
									'form_action'	=> $this->helper->route('mrgoldy_ultimateblog_misc', array('blog_id' => (int) $blog_id, 'mode' => 'comment', 'action' => 'edit_save', 'cid' => (int) $comment_id)),
								));
							}
						break;

						case 'edit_save':
							$cancel = $this->request->is_set_post('cancel');
							$submit = $this->request->is_set_post('submit');

							if ($this->request->is_ajax())
							{
								if ($cancel)
								{
									return new JsonResponse(array());
								}

								$author_id = $this->request->variable('author_id', 0);
								$comment_id = $this->request->variable('comment_id', 0);
								$comment_text = $this->request->variable('comment_edit_text', '', true);
								$comment_text = htmlspecialchars_decode($comment_text, ENT_COMPAT);

								if ($submit)
								{
									# Generate text for storage
									!$this->config['ub_allow_bbcodes'] ? $this->parser->disable_bbcodes() : $this->parser->enable_bbcodes();
									!$this->config['ub_allow_smilies'] ? $this->parser->disable_smilies() : $this->parser->enable_smilies();
									!$this->config['ub_allow_magic_url'] ? $this->parser->disable_magic_url() : $this->parser->enable_magic_url();
									$comment_array = array(
										'comment_id'		=> (int) $comment_id,
										'comment_text'		=> $this->parser->parse($comment_text),
									);

									# Save the comment
									$this->func->comment_update($comment_array);

									# Check if user editing is author, if not, send the author a notification
									if ($this->user->data['user_id'] != $author_id)
									{
										# Increment our notifications sent counter
										$this->config->increment('ub_notification_id', 1);

										# Send out notification
										$this->notification_manager->add_notifications('mrgoldy.ultimateblog.notification.type.ultimateblog', array(
											'actionee_id'		=> (int) $this->user->data['user_id'],
											'author_id'			=> (int) $author_id,
											'blog_id'			=> (int) $blog_id,
											'blog_title'		=> $blog['blog_title'],
											'comment_id'		=> (int) $comment_id,
											'notification_id'	=> $this->config['ub_notification_id'],
											'notification_type'	=> 'comment_edited',
										));
									}

									# Add it to the log
									$log_mode = $author_id == $this->user->data['user_id'] ? 'user' : 'mod';
									$this->log->add($log_mode, $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_COMMENT_EDITED', time(), array('reportee_id' => $this->user->data['user_id']));

									# Return
									return new JsonResponse();
								}
							}
						break;
					}
				}
			break;
		}
	}
}
