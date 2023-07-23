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

use mrgoldy\ultimateblog\constants;

/**
 * Class admin_categories
 *
 * @package mrgoldy\ultimateblog\controller
 */
class admin_categories
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\filesystem\filesystem */
	protected $filesystem;

	/** @var \phpbb\files\factory */
	protected $files_factory;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

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

	/** @var string Custom form action */
	protected $u_action;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string Ultimate Blog blogs table */
	protected $ub_blogs_table;

	/** @var string Ultimate Blog categories table */
	protected $ub_categories_table;

	/** @var string Ultimate Blog blog <> category corralation table */
	protected $ub_blog_category_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\filesystem\filesystem		$filesystem
	 * @param \phpbb\files\factory				$files_factory
	 * @param \phpbb\controller\helper			$helper
	 * @param \phpbb\language\language			$lang
	 * @param \phpbb\log\log					$log
	 * @param \phpbb\textformatter\s9e\parser	$parser
	 * @param \phpbb\textformatter\s9e\renderer $renderer
	 * @param \phpbb\request\request			$request
	 * @param \phpbb\template\template			$template
	 * @param \phpbb\user						$user
	 * @param \phpbb\textformatter\s9e\utils	$utils
	 * @param									$phpbb_root_path
	 * @param									$ub_blogs_table
	 * @param									$ub_categories_table
	 * @param									$ub_blog_category_table
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\filesystem\filesystem $filesystem, \phpbb\files\factory $files_factory, \phpbb\controller\helper $helper, \phpbb\language\language $lang, \phpbb\log\log $log, \phpbb\textformatter\s9e\parser $parser, \phpbb\textformatter\s9e\renderer $renderer, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\textformatter\s9e\utils $utils, $phpbb_root_path, $ub_blogs_table, $ub_categories_table, $ub_blog_category_table)
	{
		$this->config					= $config;
		$this->db						= $db;
		$this->filesystem				= $filesystem;
		$this->files_factory			= $files_factory;
		$this->helper					= $helper;
		$this->lang						= $lang;
		$this->log						= $log;
		$this->parser					= $parser;
		$this->renderer					= $renderer;
		$this->request					= $request;
		$this->template					= $template;
		$this->user						= $user;
		$this->utils					= $utils;
		$this->phpbb_root_path			= $phpbb_root_path;
		$this->ub_blogs_table			= $ub_blogs_table;
		$this->ub_categories_table		= $ub_categories_table;
		$this->ub_blog_category_table	= $ub_blog_category_table;
	}

	# Display Ultimate Blog categories
	public function ub_categories_index()
	{
		# Select all categories
		$sql_array = array(
			'SELECT'	=> 'c.*, COUNT(distinct bc.blog_id) as count',

			'FROM'		=> array(
				$this->ub_categories_table => 'c',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->ub_blog_category_table => 'bc'),
					'ON'	=> 'c.category_id = bc.category_id',
				),
			),

			'GROUP_BY'	=> 'bc.category_id, c.category_id',
			'ORDER_BY'	=> 'c.left_id ASC',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			# Set category block variables.
			$this->template->assign_block_vars('categories', array(
				'NAME'			=> $row['category_name'],
				'DESC'			=> $this->renderer->render($row['category_description']),
				'COUNT'			=> $row['count'],
				'IMAGE'			=> !empty($row['category_image']) ? $this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $row['category_image'] : '',
				'U_DELETE'		=> "{$this->u_action}&amp;action=delete&amp;category_id=" . (int) $row['category_id'],
				'U_EDIT'		=> "{$this->u_action}&amp;action=edit&amp;category_id=" . (int) $row['category_id'],
				'U_MOVE_DOWN'	=> "{$this->u_action}&amp;action=move_down&amp;category_id=" . (int) $row['category_id'] . '&amp;hash=' . generate_link_hash('ub_move'),
				'U_MOVE_UP'		=> "{$this->u_action}&amp;action=move_up&amp;category_id=" . (int) $row['category_id'] . '&amp;hash=' . generate_link_hash('ub_move'),
				'S_IS_PRIVATE'	=> $row['is_private'],
			));
		}
		$this->db->sql_freeresult($result);

		// Set output vars for display in the template
		$this->template->assign_vars(array(
			'U_ACTION'		=> "{$this->u_action}",
		));
	}

	public function ub_category_add()
	{
		# Add a form key for security
		add_form_key('ub_category_update');

		# Collect all the data
		$data = array(
			'category_name'				=> $this->request->variable('category_name', '', true),
			'category_description'		=> $this->request->variable('category_description', '', true),
			'enable_bbcode'				=> !$this->request->variable('disable_bbcode', false),
			'enable_smilies'			=> !$this->request->variable('disable_smilies', false),
			'enable_magic_url'			=> !$this->request->variable('disable_magic_url', false),
			'is_private'				=> $this->request->variable('category_is_private', false),
		);

		# Update the new category
		$this->ub_category_update($data, 'add');

		# Set the template variables
		$this->template->assign_vars(array(
			'CATEGORY_NAME'				=> $this->request->variable('category_name', '', true),
			'S_UB_CATEGORY_ADD'			=> true,

			'U_UB_CATEGORY_ADD_ACTION'	=> "{$this->u_action}&amp;action=add",
		));
	}

	/**
	 * @param $category_id
	 */
	public function ub_category_edit($category_id)
	{
		# Add a form key for security
		add_form_key('ub_category_update');

		# Collect all the data
		$data = array(
			'category_name'				=> $this->request->variable('category_name', '', true),
			'category_description'		=> $this->request->variable('category_description', '', true),
			'enable_bbcode'				=> !$this->request->variable('disable_bbcode', false),
			'enable_smilies'			=> !$this->request->variable('disable_smilies', false),
			'enable_magic_url'			=> !$this->request->variable('disable_magic_url', false),
			'is_private'				=> $this->request->variable('category_is_private', false),
		);

		# Update the category
		$this->ub_category_update($data, 'edit', $category_id);

		# Set the template variables
		$this->template->assign_vars(array(
			'S_UB_CATEGORY_EDIT'		=> true,

			'U_UB_CATEGORY_EDIT_ACTION'	=> "{$this->u_action}&amp;action=edit&amp;category_id={$category_id}",
		));
	}

	/**
	 * @param	 $data
	 * @param	 $status
	 * @param int $category_id
	 */
	private function ub_category_update($data, $status, $category_id = 0)
	{
		$submit = $this->request->is_set_post('submit');
		$this->lang->add_lang('posting');
		$errors = array();

		if ($status === 'edit')
		{
			# Grab the data if we're editing
			$sql = 'SELECT *
					FROM ' . $this->ub_categories_table . '
					WHERE category_id = ' . (int) $category_id;
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
		}

		if ($submit)
		{
			# Get an instance of the files upload class
			$upload = $this->files_factory->get('upload')
					-> set_max_filesize($this->config['ub_image_size'] * 1000)
					-> set_allowed_extensions(array('png', 'jpg', 'jpeg', 'gif'));

			$upload_file = $upload->handle_upload('files.types.form', 'category_image');

			# Depending on the status (edit / add) check if we have to upload a new file.
			if (($status === 'add' || $status === 'edit') && $upload_file->get('uploadname'))
			{
				# Perform some common checks
				if (!empty($upload_file->error) || !$upload_file->is_image() || !$upload_file->is_uploaded() || $upload_file->init_error())
				{
					$errors[] = $this->lang->lang('ACP_UB_ERROR_CATEGORY_IMAGE');
					$upload_file->remove();
					foreach ($upload_file->error as $file_error)
					{
						$errors[] = $file_error;
					}
				}
			}

			# Check form key for security
			if (!check_form_key('ub_category_update'))
			{
				$errors[] = $this->lang->lang('FORM_INVALID');
			}
			if ($data['category_name'] == '')
			{
				# Invalid category name
				$errors[] = $this->lang->lang('ACP_UB_ERROR_EMPTY_CATEGORY_NAME');
			}

			# Prepare the text for storage
			!$data['enable_bbcode'] || !$this->config['ub_allow_bbcodes'] ? $this->parser->disable_bbcodes() : $this->parser->enable_bbcodes();
			!$data['enable_smilies'] || !$this->config['ub_allow_smilies'] ? $this->parser->disable_smilies() : $this->parser->enable_smilies();
			!$data['enable_magic_url'] || !$this->config['ub_allow_magic_url'] ? $this->parser->disable_magic_url() : $this->parser->enable_magic_url();
			$category_description = $data['category_description'];
			$category_description = htmlspecialchars_decode($category_description, ENT_COMPAT);
			$data['category_description'] = $this->parser->parse($category_description);

			$clean_description_length = strlen($this->utils->clean_formatting($data['category_description']));
			if ($clean_description_length > constants::BLOG_DESC_MAXIMUM)
			{
				# String out of bounds
				$errors[] = $this->lang->lang('ACP_UB_ERROR_CATEGORY_DESCRIPTION_OUT_OF_BOUNDS', $clean_description_length);
			}
		}

		# Update the category
		if ($submit && empty($errors))
		{
			$delete_image = $this->request->variable('category_delete_image', false);

			if ($status === 'add' || ($status === 'edit' && ($upload_file->get('uploadname') || $delete_image)))
			{
				# If editing and uploading a new file, delete the old file
				if ($status === 'edit' && !empty($row['category_image']) && $this->filesystem->exists($this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $row['category_image']))
				{
					$this->filesystem->remove($this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $row['category_image']);
				}

				if ($delete_image)
				{
					$data['category_image'] = '';
				}
				else
				{
					# We're adding the new file
					$upload_file->clean_filename('unique_ext', 'ub_cat_');
					$upload_file->move_file($this->config['ub_image_cat_dir'], true, true, 0644);
					@chmod($this->phpbb_root_path . $this->config['ub_image_cat_dir'] . $upload_file->get('realname'), 0644);

					$data['category_image'] = $upload_file->get('realname');
				}
			}

			if ($status == 'add')
			{
				$sql = 'SELECT MAX(right_id) AS right_id
						FROM ' . $this->ub_categories_table;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);
				$data['left_id'] = $row['right_id'] + 1;
				$data['right_id'] = $row['right_id'] + 2;

				$sql = 'INSERT INTO ' . $this->ub_categories_table . ' ' . $this->db->sql_build_array('INSERT', $data);
				$this->db->sql_query($sql);

				# Add it to the admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_CATEGORY_ADDED', time(), array($data['category_name']));

				# Send confirmation message.
				trigger_error($this->lang->lang('ACP_UB_CATEGORY_ADDED') . adm_back_link("{$this->u_action}"));
			}
			else if ($status == 'edit')
			{
				$sql = 'UPDATE ' . $this->ub_categories_table . '
						SET ' . $this->db->sql_build_array('UPDATE', $data) . '
						WHERE category_id = ' . (int) $category_id;
				$this->db->sql_query($sql);

				# Add it to the admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_CATEGORY_EDITED', time(), array($data['category_name']));

				# Send confirmation message.
				trigger_error($this->lang->lang('ACP_UB_CATEGORY_EDITED') . adm_back_link("{$this->u_action}"));
			}
		}

		# Set the template variables
		$this->template->assign_vars(array(
			'S_ERROR'				=> !empty($errors),
			'ERROR_MSG'				=> !empty($errors) ? implode('<br />', $errors) : '',

			'CATEGORY_NAME'			=> $submit ? $data['category_name'] : (!empty($row['category_name']) ? $row['category_name'] : ''),
			'CATEGORY_DESCRIPTION'	=> $submit ? $this->utils->unparse($data['category_description']) : (!empty($row['category_description']) ? $this->utils->unparse($row['category_description']) : ''),
			'CATEGORY_IMAGE'		=> !empty($row['category_image']) ? $this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $row['category_image'] : '',
			'S_CATEGORY_IS_PRIVATE'	=> $submit ? $data['is_private'] : (!empty($row['is_private']) ? $row['is_private'] : false),

			'S_BBCODE_ENABLED'		=> !empty($row['enable_bbcode']) ? $row['enable_bbcode'] : 0,
			'S_SMILIES_ENABLED'		=> !empty($row['enable_smilies']) ? $row['enable_smilies'] : 0,
			'S_MAGIC_URL_ENABLED'	=> !empty($row['enable_magic_url']) ? $row['enable_magic_url'] : 0,

			'BBCODE_STATUS'			=> !empty($this->config['ub_allow_bbcodes']) ? $this->lang->lang('BBCODE_IS_ON', '<a href="' . $this->helper->route('phpbb_help_bbcode_controller') . '">', '</a>') : $this->lang->lang('BBCODE_IS_OFF', '<a href="' . $this->helper->route('phpbb_help_bbcode_controller') . '">', '</a>'),
			'SMILIES_STATUS'		=> !empty($this->config['ub_allow_smilies']) ? $this->lang->lang('SMILIES_ARE_ON') : $this->lang->lang('SMILIES_ARE_OFF'),
			'URL_STATUS'			=> !empty($this->config['ub_allow_magic_url']) ? $this->lang->lang('URL_IS_ON') : $this->lang->lang('URL_IS_OFF'),
		));
	}

	/**
	 * @param $category_id
	 */
	public function ub_category_delete($category_id)
	{
		# Add a form key for security
		add_form_key('ub_category_delete');

		# Get all categories
		$sql = 'SELECT *
				FROM ' . $this->ub_categories_table . '
				ORDER BY category_name ASC';
		$result = $this->db->sql_query($sql);

		# Set up the categories list
		$categories_list = '';

		while ($row = $this->db->sql_fetchrow($result))
		{
			# Grab the category name and image we're deleting
			if ($row['category_id'] == $category_id)
			{
				$category_name = $row['category_name'];
				$category_image = $row['category_image'];
			}
			# All other categories go into the list.
			else
			{
				$categories_list .= '<option value="' . $row['category_id'] . '">' . $row['category_name'] . '</option>';
			}
		}
		$this->db->sql_freeresult($result);

		# Does the category contain any blogs?
		$sql = 'SELECT COUNT(blog_id) as blogs
				FROM ' . $this->ub_blog_category_table . '
				WHERE category_id = ' . (int) $category_id;
		$this->db->sql_query($sql);
		$count = $this->db->sql_fetchfield('blogs');

		# If there are no blogs in this categories we can straight up delete it.
		if ($count == 0)
		{
			if (confirm_box(true))
			{
				# Delete the category
				$sql = 'DELETE FROM ' . $this->ub_categories_table . '
						WHERE category_id = ' . (int) $category_id;
				$this->db->sql_query($sql);

				# Delete the category image
				if ($this->filesystem->exists($this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $category_image))
				{
					$this->filesystem->remove($this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $category_image);
				}

				# Add it to the Admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_CATEGORY_DELETED', time(), array($category_name));

				# Send a confirmation message
				trigger_error($this->lang->lang('ACP_UB_CATEGORY_DELETED') . adm_back_link("{$this->u_action}"));
			}
			else
			{
				# Are you sure?
				confirm_box(false, $this->lang->lang('ACP_UB_CATEGORY_DELETE_CONFIRM'));
			}
		}

		# Else we might have to move or delete the blogs.
		else
		{
			$delete_action = $this->request->variable('delete_action', '');
			$new_category_id = $this->request->variable('id', 0);
			$blog_titles_list = '';
			$i = 0;

			# Was submit pressed?
			if ($this->request->is_set_post('submit'))
			{
				# Check the form key for security
				if (!check_form_key('ub_category_delete'))
				{
					trigger_error($this->lang->lang('FORM_INVALID'));
				}

				# Select everything from blog-category corralation table.
				$sql = 'SELECT * FROM ' . $this->ub_blog_category_table;
				$result = $this->db->sql_query($sql);

				$with_cat = $without_cat = []; # For comparison when mode is 'delete'
				$old_cat = $new_cat = []; # For comparison when mode is 'move'

				while ($row = $this->db->sql_fetchrow($result))
				{
					if ($row['category_id'] == $category_id)
					{
						$with_cat[] = $row['blog_id']; # For comparison when mode is 'delete'
						$old_cat[] = $row['blog_id']; # For comparison when mode is 'move'
					}
					else
					{
						if ($row['category_id'] == $new_category_id)
						{
							$new_cat[] = $row['category_id']; # For comparison when mode is 'move'
						}
						$without_cat[] = $row['blog_id']; # For comparison when mode is 'delete'
					}
				}
				$this->db->sql_freeresult($result);

				if ($delete_action == 'delete')
				{
					$blogs_to_delete = array_diff($with_cat, $without_cat);
					$i = count($blogs_to_delete);

					# Make sure there are blogs to delete
					if (!empty($i))
					{
						# Select the blog titles that we're going to delete.
						$sql = 'SELECT blog_title
								FROM ' . $this->ub_blogs_table . '
								WHERE ' . $this->db->sql_in_set('blog_id', $blogs_to_delete);
						$result = $this->db->sql_query($sql);
						while ($row = $this->db->sql_fetchrow($result))
						{
							$blog_titles_list .= '<br />&#8226; ' . $row['blog_title'];
						}
						$this->db->sql_freeresult($result);

						# Delete the blog entries that are only in this category.
						$sql = 'DELETE FROM ' . $this->ub_blogs_table . '
								WHERE ' . $this->db->sql_in_set('blog_id', $blogs_to_delete);
						$this->db->sql_query($sql);
					}

					# Set up confirmation message
					$confirmation_message = $this->lang->lang('ACP_UB_CATEGORY_DELETED_DELETE', $i, $blog_titles_list);
				}
				else if ($delete_action == 'move')
				{
					$blogs_to_move = array_diff($old_cat, $new_cat);
					$i = count($blogs_to_move);

					if (!empty($i))
					{
						# Select the blog titles that we're going to move.
						$sql = 'SELECT blog_title
								FROM ' . $this->ub_blogs_table . '
								WHERE ' . $this->db->sql_in_set('blog_id', $blogs_to_move);
						$result = $this->db->sql_query($sql);
						while ($row = $this->db->sql_fetchrow($result))
						{
							$blog_titles_list .= '<br />&#8226; ' . $row['blog_title'];
						}
						$this->db->sql_freeresult($result);

						# Add the new corralation entries
						$sql_arr = [];
						foreach ($blogs_to_move as $blog_to_move_id)
						{
							$sql_array[] = array(
								'blog_id'		=> $blog_to_move_id,
								'category_id'	=> $new_category_id,
							);
						}
						$this->db->sql_multi_insert($this->ub_blog_category_table, $sql_ary);

						# Set up a confirmation message
						$confirmation_message = $this->lang->lang('ACP_UB_CATEGORY_DELETED_MOVE', $i, $blog_titles_list);
					}
				}

				# Delete all blogs from the Blogs-Categories corralation table with the old category id.
				$sql = 'DELETE FROM ' . $this->ub_blog_category_table . '
						WHERE category_id = ' . (int) $category_id;
				$this->db->sql_query($sql);

				# Delete the category from the categories table with the old category id.
				$sql = 'DELETE FROM ' . $this->ub_categories_table . '
						WHERE category_id = ' . (int) $category_id;
				$this->db->sql_query($sql);

				# Delete the category image
				if ($this->filesystem->exists($this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $category_image))
				{
					$this->filesystem->remove($this->phpbb_root_path . $this->config['ub_image_cat_dir'] . '/' . $category_image);
				}

				# Add it to the admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'ACP_UB_LOG_CATEGORY_DELETED', time(), array($category_name));

				# Send a confirmation message
				trigger_error($confirmation_message . adm_back_link($this->u_action));
			}

			# Set output vars for display in the template
			$this->template->assign_vars(array(
				'CATEGORY_NAME'				=> $category_name,
				'CATEGORIES_LIST'			=> $categories_list,

				'S_UB_CATEGORY_DELETE'		=> true,
				'S_CATEGORIES_LIST'			=> $categories_list ? true : false,

				'U_DELETE_ACTION'			=> "{$this->u_action}&amp;category_id={$category_id}&amp;action=delete",
			));
		}
	}

	/**
	 * @param $category_id
	 * @param $direction
	 */
	public function ub_category_move($category_id, $direction)
	{
		# Check hash for security
		if (!check_link_hash($this->request->variable('hash', ''), 'ub_move'))
		{
			trigger_error($this->lang->lang('ACP_UB_INVALID_HASH') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'SELECT *
				FROM ' . $this->ub_categories_table . '
				WHERE category_id = ' . (int) $category_id;
		$result = $this->db->sql_query($sql);
		$item = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		$move_cat_name = $this->ub_category_move_by($item, $direction);

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array('success' => ($move_cat_name !== false)));
		}
	}

	/**
	 * @param		$cat_row
	 * @param string $direction
	 * @param int	$steps
	 * @return bool|mixed
	 */
	private function ub_category_move_by($cat_row, $direction = 'move_up', $steps = 1)
	{
		$sql = 'SELECT category_id, category_name, left_id, right_id
			FROM ' . $this->ub_categories_table . "
			WHERE " . (($direction == 'move_up') ? "right_id < {$cat_row['right_id']} ORDER BY right_id DESC" : "left_id > {$cat_row['left_id']} ORDER BY left_id ASC");
		$result = $this->db->sql_query_limit($sql, $steps);
		$target = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$target = $row;
		}
		$this->db->sql_freeresult($result);
		if (!sizeof($target))
		{
			// The category is already on top or bottom
			return false;
		}

		if ($direction == 'move_up')
		{
			$left_id = $target['left_id'];
			$right_id = $cat_row['right_id'];
			$diff_up = $cat_row['left_id'] - $target['left_id'];
			$diff_down = $cat_row['right_id'] + 1 - $cat_row['left_id'];
			$move_up_left = $cat_row['left_id'];
			$move_up_right = $cat_row['right_id'];
		}
		else
		{
			$left_id = $cat_row['left_id'];
			$right_id = $target['right_id'];
			$diff_up = $cat_row['right_id'] + 1 - $cat_row['left_id'];
			$diff_down = $target['right_id'] - $cat_row['right_id'];
			$move_up_left = $cat_row['right_id'] + 1;
			$move_up_right = $target['right_id'];
		}

		$sql = 'UPDATE ' . $this->ub_categories_table . "
			SET left_id = left_id + CASE
				WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END,
			right_id = right_id + CASE
				WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END
			WHERE
				left_id BETWEEN {$left_id} AND {$right_id}
				AND right_id BETWEEN {$left_id} AND {$right_id}";
		$this->db->sql_query($sql);

		return $target['category_name'];
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return void
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
