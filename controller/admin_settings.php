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
use phpbb\config\config;

/**
 * Class admin_settings
 *
 * @package mrgoldy\ultimateblog\controller
 */
class admin_settings
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\filesystem\filesystem */
	protected $filesystem;

	/** @var \phpbb\language\language */
	protected $lang;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string Custom form action */
	protected $u_action;

	/** @var string php file extension */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string Ultimate Blog categories table */
	protected $ub_categories_table;

	/** @var string Ultimate Blog index table */
	protected $ub_index_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\config\db_text			 $config_text
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param \phpbb\filesystem\filesystem		$filesystem
	 * @param \phpbb\language\language			$lang
	 * @param \phpbb\log\log					$log
	 * @param \phpbb\request\request			$request
	 * @param \phpbb\template\template			$template
	 * @param \phpbb\user						$user
	 * @param									$phpbb_root_path
	 * @param									$ub_categories_table
	 * @param									$ub_index_table
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\db\driver\driver_interface $db, \phpbb\filesystem\filesystem $filesystem, \phpbb\language\language $lang, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, $php_ext, $phpbb_root_path, $ub_categories_table, $ub_index_table)
	{
		$this->config		= $config;
		$this->config_text	= $config_text;
		$this->db			= $db;
		$this->filesystem	= $filesystem;
		$this->lang			= $lang;
		$this->log			= $log;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->helper		= $helper;
		$this->php_ext		= $php_ext;
		$this->phpbb_root_path			= $phpbb_root_path;
		$this->ub_categories_table		= $ub_categories_table;
		$this->ub_index_table			= $ub_index_table;
	}

	# Ultimate Blog Settings
	public function ub_settings()
	{
		# Add a form key for security
		add_form_key('ub_settings');

		// Load posting language file for the BBCode editor
		$this->lang->add_lang('posting');

		# Set up array for all the setting bools
		$settings = ['ub_enable', 'ub_allow_bbcodes', 'ub_allow_smilies', 'ub_allow_magic_url', 'ub_enable_announcement', 'ub_enable_comments', 'ub_enable_friends_only', 'ub_enable_feed', 'ub_enable_feed_cats', 'ub_enable_feed_stats', 'ub_enable_rating', 'ub_enable_subscriptions'];

		# Set up array for all the index blocks
		$blocks = [1 => 'ub_category1_', 2 => 'ub_category2_', 3 => 'ub_category3_', 4 => 'ub_latest_', 5 => 'ub_comments_', 6 => 'ub_rating_', 7 => 'ub_views_'];

		$errors = [];
		$submit = $this->request->is_set_post('submit');

		$parse_bbcode = $submit ? $this->request->variable('parse_bbcode', false) : $this->config['ub_announcement_bbcode'];
		$parse_smilies = $submit ? $this->request->variable('parse_smilies', false) : $this->config['ub_announcement_smilies'];
		$parse_magic_url = $submit ? $this->request->variable('parse_magic_url', false) : $this->config['ub_announcement_urls'];
		$parse_html = $submit ? $this->request->variable('parse_html', false) : $this->config['ub_announcement_html'];
		$announcement_text = $submit ? $this->request->variable('ub_announcement_text', '', true) : self::get_announcement_for_edit();

		if ($submit)
		{
			# Check form key for security
			if (!check_form_key('ub_settings'))
			{
				$errors[] = $this->lang->lang('FORM_INVALID');
			}

			# Request some variables to check
			$ub_title = $this->request->variable('ub_title', '', true);
			$ub_image_size = $this->request->variable('ub_image_size', 0);
			$ub_image_dir = $this->request->variable('ub_image_dir', 'images/blog', true);
			$ub_image_cat_dir = $this->request->variable('ub_image_cat_dir', 'images/blog/categories', true);
			$ub_blog_min_chars = $this->request->variable('ub_blog_min_chars', 100);
			$ub_blogs_per_page = $this->request->variable('ub_blogs_per_page', 3);
			$ub_comments_per_page = $this->request->variable('ub_comments_per_page', 5);
			$ub_enable_feed_limit = $this->request->variable('ub_enable_feed_limit', 10);
			$ub_custom_index = $this->request->variable('ub_custom_index', false);
			$ub_custom_order = $this->request->variable('ub_index_order', '');
			$order_array = empty($ub_custom_order) ? [] : explode(',', $ub_custom_order);

			if ($ub_title == '')
			{
				$errors[] = $this->lang->lang('ACP_UB_ERROR_EMPTY_TITLE');
			}

			if ($ub_blogs_per_page < 3)
			{
				$errors[] = $this->lang->lang('ACP_UB_ERROR_BLOGS_PER_PAGE');
			}

			if ($ub_comments_per_page < 5)
			{
				$errors[] = $this->lang->lang('ACP_UB_ERROR_COMMENTS_PER_PAGE');
			}

			if ($ub_enable_feed_limit < 5 || $ub_enable_feed_limit > 100)
			{
				$errors[] = $this->lang->lang('ACP_UB_ERROR_FEED_LIMIT', $ub_enable_feed_limit);
			}

			if (!empty($ub_custom_index) && empty($order_array))
			{
				$errors[] = $this->lang->lang('ACP_UB_ERROR_CUSTOM_INDEX_EMPTY');
			}

			if (!$this->filesystem->exists($this->phpbb_root_path . $ub_image_dir))
			{
				$errors[] = $this->lang->lang('DIRECTORY_DOES_NOT_EXIST', $ub_image_dir);
			}

			if (!$this->filesystem->exists($this->phpbb_root_path . $ub_image_cat_dir))
			{
				$errors[] = $this->lang->lang('DIRECTORY_DOES_NOT_EXIST', $ub_image_cat_dir);
			}

			if (empty($errors))
			{
				# Request and set new block variables
				$order_id = [0 => 7, 1 => 6, 2 => 5, 3 => 4, 4 => 3, 5 => 2, 6 => 1];
				foreach ($blocks as $block_id => $block_prefix)
				{
					$block = [];
					$limit = (int) $this->request->variable($block_prefix . 'limit', 3);
					$block = array (
						'block_order'	=> in_array($block_id, $order_array) ? $order_id[array_search($block_id, $order_array)] : 0,
						'block_limit'	=> $limit > constants::BLOCK_MAX_LIMIT ? constants::BLOCK_MAX_LIMIT : ($limit < 1 ? 1 : $limit),
						'block_data'	=> in_array($block_id, [1, 2, 3, 6]) ? (int) $this->request->variable($block_prefix . 'data', 0) : 0,
					);

					$sql = 'UPDATE ' . $this->ub_index_table . ' SET ' . $this->db->sql_build_array('UPDATE', $block) . ' WHERE block_id = ' . (int) $block_id;
					$this->db->sql_query($sql);
				}

				# Set the new settings
				foreach ($settings as $setting)
				{
					$this->config->set($setting, $this->request->variable($setting, 0));
				}

				$uid = $bitfield = $flags = '';
				if ($parse_html)
				{
					$announcement_text = htmlspecialchars_decode($announcement_text, ENT_COMPAT);
				}
				else
				{
					// Prepare the text for storage
					generate_text_for_storage($announcement_text, $uid, $bitfield, $flags, $parse_bbcode, $parse_magic_url, $parse_smilies);
				}
				$this->config->set('ub_title', $ub_title);
				$this->config->set('ub_fa_icon', $this->request->variable('ub_fa_icon', ''));
				$this->config->set('ub_blog_min_chars', $ub_blog_min_chars);
				$this->config->set('ub_blogs_per_page', $ub_blogs_per_page);
				$this->config->set('ub_comments_per_page', $ub_comments_per_page);
				$this->config->set('ub_enable_feed_limit', $ub_enable_feed_limit);
				$this->config->set('ub_custom_index', $ub_custom_index);
				$this->config->set('ub_image_size', $ub_image_size);
				$this->config->set('ub_image_dir', $ub_image_dir);
				$this->config->set('ub_image_cat_dir', $ub_image_cat_dir);
				$this->config_text->set('ub_announcement_text', $announcement_text);
				$this->config->set('ub_announcement_bbcode', $parse_bbcode);
				$this->config->set('ub_announcement_smilies', $parse_smilies);
				$this->config->set('ub_announcement_urls', $parse_magic_url);
				$this->config->set('ub_announcement_html', $parse_html);
				$this->config->set('ub_announcement_uid', $uid);
				$this->config->set('ub_announcement_bitfield', $bitfield);

				# Add it to the admin log
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_UB_LOG_SETTINGS');

				# Send confirmation message
				trigger_error($this->lang->lang('ACP_UB_SETTINGS_CHANGED') . adm_back_link($this->u_action));
			}
		}

		# Get categories
		$sql = 'SELECT category_id, category_name
				FROM ' . $this->ub_categories_table;
		$result = $this->db->sql_query($sql);
		$categories_array = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		# Display block order
		$block_current_order_array = [];
		$sql = 'SELECT * FROM ' . $this->ub_index_table . ' ORDER BY block_order DESC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['block_order'] == 0)
			{
				$block_part = 'blocks_not_used';
			}
			else
			{
				$block_part = 'blocks_used';
				$block_current_order_array[] = $row['block_id'];
			}

			$this->template->assign_block_vars($block_part, [
				'ID'		=> $row['block_id'],
				'LIMIT'		=> $row['block_limit'],
				'NAME'		=> $row['block_name'],
				'DATA'		=> $row['block_data'],
				'EXPLAIN'	=> $row['block_id'] > 3 ? $this->lang->lang('ACP_UB_BLOCK_' . strtoupper($row['block_name']) . '_EXPLAIN') : $this->lang->lang('ACP_UB_BLOCK_CATEGORY_EXPLAIN'),
				'TITLE'		=> $row['block_id'] > 3 ? $this->lang->lang('ACP_UB_BLOCK_' . strtoupper($row['block_name'])) : $this->lang->lang('ACP_UB_BLOCK_CATEGORY'),
				'IS_CAT'	=> $row['block_id'] <= 3 ? true : false,
				'IS_RATING'	=> $row['block_id'] == 6 ? true : false,
			]);

			foreach ($categories_array as $cat)
			{
				$this->template->assign_block_vars($block_part . '.cats', [
					'ID'	=> $cat['category_id'],
					'NAME'	=> $cat['category_name'],
				]);
			}
		}
		$this->db->sql_freeresult($result);

		$font_awesome_icons = $this->get_font_awesome_icons();

		foreach ($font_awesome_icons as $font_awesome_icon)
		{
			$this->template->assign_block_vars('ub_fa_icons', [
				'TITLE'	=> $font_awesome_icon,
			]);
		}

		$s_errors = (bool) count($errors);

		$this->template->assign_vars([
			'S_ERROR'			=> $s_errors,
			'ERROR_MSG'			=> $s_errors ? implode('<br />', $errors) : '',

			'UB_ANNOUNCEMENT_TEXT'	=> $announcement_text,
			'UB_BLOG_MIN_CHARS'		=> $submit ? $ub_blog_min_chars : $this->config['ub_blog_min_chars'],
			'UB_BLOGS_PER_PAGE'		=> $submit ? $ub_blogs_per_page : $this->config['ub_blogs_per_page'],
			'UB_COMMENTS_PER_PAGE'	=> $submit ? $ub_comments_per_page : $this->config['ub_comments_per_page'],
			'UB_ENABLE_FEED_LIMIT'	=> $submit ? $ub_enable_feed_limit : $this->config['ub_enable_feed_limit'],
			'UB_CUSTOM_INDEX_ORDER'	=> implode($this->lang->lang('COMMA_SEPARATOR'), $block_current_order_array),
			'UB_FA_ICON'			=> $submit ? $this->request->variable('ub_fa_icon', '', true) : $this->config['ub_fa_icon'],
			'UB_IMAGE_DIR'			=> $submit ? $ub_image_dir : $this->config['ub_image_dir'],
			'UB_IMAGE_CAT_DIR'		=> $submit ? $ub_image_cat_dir : $this->config['ub_image_cat_dir'],
			'UB_IMAGE_SIZE'			=> $submit ? $ub_image_size : $this->config['ub_image_size'],
			'UB_TITLE'				=> $submit ? $ub_title : $this->config['ub_title'],

			'S_PARSE_BBCODE_CHECKED'	=> $this->config['ub_announcement_bbcode'],
			'S_PARSE_SMILIES_CHECKED'	=> $this->config['ub_announcement_smilies'],
			'S_PARSE_MAGIC_URL_CHECKED'	=> $this->config['ub_announcement_urls'],
			'S_PARSE_HTML_CHECKED'		=> $this->config['ub_announcement_html'],
			
			'BBCODE_STATUS'		=> $this->lang->lang('BBCODE_IS_ON', '<a href="' . $this->helper->route('phpbb_help_bbcode_controller') . '">', '</a>'),
			'SMILIES_STATUS'	=> $this->lang->lang('SMILIES_ARE_ON'),
			'IMG_STATUS'		=> $this->lang->lang('IMAGES_ARE_ON'),
			'FLASH_STATUS'		=> $this->lang->lang('FLASH_IS_ON'),
			'URL_STATUS'		=> $this->lang->lang('URL_IS_ON'),

			'S_BBCODE_ALLOWED'	=> true,
			'S_SMILIES_ALLOWED'	=> true,
			'S_BBCODE_IMG'		=> true,
			'S_BBCODE_FLASH'	=> true,
			'S_LINKS_ALLOWED'	=> true,

			'S_UB_ALLOW_BBCODES'		=> $submit ? $this->request->variable('ub_allow_bbcodes', true) : $this->config['ub_allow_bbcodes'],
			'S_UB_ALLOW_MAGIC_URL'		=> $submit ? $this->request->variable('ub_allow_magic_url', true) : $this->config['ub_allow_magic_url'],
			'S_UB_ALLOW_SMILIES'		=> $submit ? $this->request->variable('ub_allow_smilies', true) : $this->config['ub_allow_smilies'],
			'S_UB_CUSTOM_INDEX'			=> $submit ? $ub_custom_index : $this->config['ub_custom_index'],
			'S_UB_ENABLE'				=> $submit ? $this->request->variable('ub_enable', true) : $this->config['ub_enable'],
			'S_UB_ENABLE_ANNOUNCEMENT'	=> $submit ? $this->request->variable('ub_enable_announcement', true) : $this->config['ub_enable_announcement'],
			'S_UB_ENABLE_COMMENTS'		=> $submit ? $this->request->variable('ub_enable_comments', true) : $this->config['ub_enable_comments'],
			'S_UB_ENABLE_FRIENDS_ONLY'	=> $submit ? $this->request->variable('ub_enable_friends_only', true) : $this->config['ub_enable_friends_only'],
			'S_UB_ENABLE_RATING'		=> $submit ? $this->request->variable('ub_enable_rating', true) : $this->config['ub_enable_rating'],
			'S_UB_ENABLE_FEED'			=> $submit ? $this->request->variable('ub_enable_feed', true) : $this->config['ub_enable_feed'],
			'S_UB_ENABLE_FEED_CATS'		=> $submit ? $this->request->variable('ub_enable_feed_cats', true) : $this->config['ub_enable_feed_cats'],
			'S_UB_ENABLE_FEED_STATS'		=> $submit ? $this->request->variable('ub_enable_feed_stats', true) : $this->config['ub_enable_feed_stats'],
			'S_UB_ENABLE_SUBSCRIPTIONS'	=> $submit ? $this->request->variable('ub_enable_subscriptions', true) : $this->config['ub_enable_subscriptions'],

			'U_ACTION'			=> $this->u_action,
		]);

		// Build custom bbcodes array
		include_once $this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext;

		display_custom_bbcodes();
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

	/**
	 * @return array
	 */
	private function get_font_awesome_icons()
	{
		$font_awesome_icons = ['address-book', 'address-book-o', 'address-card', 'address-card-o', 'bandcamp', 'bath', 'eercast', 'envelope-open', 'envelope-open-o', 'etsy', 'free-code-camp', 'grav', 'handshake-o', 'id-badge', 'id-card', 'id-card-o', 'imdb', 'linode', 'meetup', 'microchip', 'podcast', 'quora', 'ravelry', 'shower', 'snowflake-o', 'superpowers', 'telegram', 'thermometer-empty', 'thermometer-full', 'thermometer-half', 'thermometer-quarter', 'thermometer-three-quarters', 'user-circle', 'user-circle-o', 'user-o', 'window-close', 'window-close-o', 'window-maximize', 'window-minimize', 'window-restore', 'wpexplorer', 'address-book', 'address-book-o', 'address-card', 'address-card-o', 'adjust', 'american-sign-language-interpreting', 'anchor', 'archive', 'area-chart', 'arrows', 'arrows-h', 'arrows-v', 'assistive-listening-systems', 'asterisk', 'at', 'audio-description', 'balance-scale', 'ban', 'bar-chart', 'barcode', 'bars', 'bath', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter', 'battery-three-quarters', 'bed', 'beer', 'bell', 'bell-o', 'bell-slash', 'bell-slash-o', 'bicycle', 'binoculars', 'birthday-cake', 'blind', 'bluetooth', 'bluetooth-b', 'bolt', 'bomb', 'book', 'bookmark', 'bookmark-o', 'braille', 'briefcase', 'bug', 'building', 'building-o', 'bullhorn', 'bullseye', 'bus', 'calculator', 'calendar', 'calendar-check-o', 'calendar-minus-o', 'calendar-o', 'calendar-plus-o', 'calendar-times-o', 'camera', 'camera-retro', 'car', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'cart-arrow-down', 'cart-plus', 'cc', 'certificate', 'check', 'check-circle', 'check-circle-o', 'check-square', 'check-square-o', 'child', 'circle', 'circle-o', 'circle-o-notch', 'circle-thin', 'clock-o', 'clone', 'cloud', 'cloud-download', 'cloud-upload', 'code', 'code-fork', 'coffee', 'cog', 'cogs', 'comment', 'comment-o', 'commenting', 'commenting-o', 'comments', 'comments-o', 'compass', 'copyright', 'creative-commons', 'credit-card', 'credit-card-alt', 'crop', 'crosshairs', 'cube', 'cubes', 'cutlery', 'database', 'deaf', 'desktop', 'diamond', 'dot-circle-o', 'download', 'ellipsis-h', 'ellipsis-v', 'envelope', 'envelope-o', 'envelope-open', 'envelope-open-o', 'envelope-square', 'eraser', 'exchange', 'exclamation', 'exclamation-circle', 'exclamation-triangle', 'external-link', 'external-link-square', 'eye', 'eye-slash', 'eyedropper', 'fax', 'female', 'fighter-jet', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-pdf-o', 'file-powerpoint-o', 'file-video-o', 'file-word-o', 'film', 'filter', 'fire', 'fire-extinguisher', 'flag', 'flag-checkered', 'flag-o', 'flask', 'folder', 'folder-o', 'folder-open', 'folder-open-o', 'frown-o', 'futbol-o', 'gamepad', 'gavel', 'gift', 'glass', 'globe', 'graduation-cap', 'hand-lizard-o', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'handshake-o', 'hashtag', 'hdd-o', 'headphones', 'heart', 'heart-o', 'heartbeat', 'history', 'home', 'hourglass', 'hourglass-end', 'hourglass-half', 'hourglass-o', 'hourglass-start', 'i-cursor', 'id-badge', 'id-card', 'id-card-o', 'inbox', 'industry', 'info', 'info-circle', 'key', 'keyboard-o', 'language', 'laptop', 'leaf', 'lemon-o', 'level-down', 'level-up', 'life-ring', 'lightbulb-o', 'line-chart', 'location-arrow', 'lock', 'low-vision', 'magic', 'magnet', 'male', 'map', 'map-marker', 'map-o', 'map-pin', 'map-signs', 'meh-o', 'microchip', 'microphone', 'microphone-slash', 'minus', 'minus-circle', 'minus-square', 'minus-square-o', 'mobile', 'money', 'moon-o', 'motorcycle', 'mouse-pointer', 'music', 'newspaper-o', 'object-group', 'object-ungroup', 'paint-brush', 'paper-plane', 'paper-plane-o', 'paw', 'pencil', 'pencil-square', 'pencil-square-o', 'percent', 'phone', 'phone-square', 'picture-o', 'pie-chart', 'plane', 'plug', 'plus', 'plus-circle', 'plus-square', 'plus-square-o', 'podcast', 'power-off', 'print', 'puzzle-piece', 'qrcode', 'question', 'question-circle', 'question-circle-o', 'quote-left', 'quote-right', 'random', 'recycle', 'refresh', 'registered', 'reply', 'reply-all', 'retweet', 'road', 'rocket', 'feed', 'feed-square', 'search', 'search-minus', 'search-plus', 'server', 'share', 'share-alt', 'share-alt-square', 'share-square', 'share-square-o', 'shield', 'ship', 'shopping-bag', 'shopping-basket', 'shopping-cart', 'shower', 'sign-in', 'sign-language', 'sign-out', 'signal', 'sitemap', 'sliders', 'smile-o', 'snowflake-o', 'sort', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-asc', 'sort-desc', 'sort-numeric-asc', 'sort-numeric-desc', 'space-shuttle', 'spinner', 'spoon', 'square', 'square-o', 'star', 'star-half', 'star-half-o', 'star-o', 'sticky-note', 'sticky-note-o', 'street-view', 'suitcase', 'sun-o', 'tablet', 'tachometer', 'tag', 'tags', 'tasks', 'taxi', 'television', 'terminal', 'thermometer-empty', 'thermometer-full', 'thermometer-half', 'thermometer-quarter', 'thermometer-three-quarters', 'thumb-tack', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up', 'ticket', 'times', 'times-circle', 'times-circle-o', 'tint', 'toggle-off', 'toggle-on', 'trademark', 'trash', 'trash-o', 'tree', 'trophy', 'truck', 'tty', 'umbrella', 'universal-access', 'university', 'unlock', 'unlock-alt', 'upload', 'user', 'user-circle', 'user-circle-o', 'user-o', 'user-plus', 'user-secret', 'user-times', 'users', 'video-camera', 'volume-control-phone', 'volume-down', 'volume-off', 'volume-up', 'wheelchair', 'wheelchair-alt', 'wifi', 'window-close', 'window-close-o', 'window-maximize', 'window-minimize', 'window-restore', 'wrench', 'american-sign-language-interpreting', 'assistive-listening-systems', 'audio-description', 'blind', 'braille', 'cc', 'deaf', 'low-vision', 'question-circle-o', 'sign-language', 'tty', 'universal-access', 'volume-control-phone', 'wheelchair', 'wheelchair-alt', 'hand-lizard-o', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up', 'ambulance', 'bicycle', 'bus', 'car', 'fighter-jet', 'motorcycle', 'plane', 'rocket', 'ship', 'space-shuttle', 'subway', 'taxi', 'train', 'truck', 'wheelchair', 'wheelchair-alt', 'genderless', 'mars', 'mars-double', 'mars-stroke', 'mars-stroke-h', 'mars-stroke-v', 'mercury', 'neuter', 'transgender', 'transgender-alt', 'venus', 'venus-double', 'venus-mars', 'file', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-o', 'file-pdf-o', 'file-powerpoint-o', 'file-text', 'file-text-o', 'file-video-o', 'file-word-o', 'check-square', 'check-square-o', 'circle', 'circle-o', 'dot-circle-o', 'minus-square', 'minus-square-o', 'plus-square', 'plus-square-o', 'square', 'square-o', 'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'credit-card', 'credit-card-alt', 'google-wallet', 'paypal', 'area-chart', 'bar-chart', 'line-chart', 'pie-chart', 'btc', 'eur', 'gbp', 'gg', 'gg-circle', 'ils', 'inr', 'jpy', 'krw', 'money', 'rub', 'try', 'usd', 'align-center', 'align-justify', 'align-left', 'align-right', 'bold', 'chain-broken', 'clipboard', 'columns', 'eraser', 'file', 'file-o', 'file-text', 'file-text-o', 'files-o', 'floppy-o', 'font', 'header', 'indent', 'italic', 'link', 'list', 'list-alt', 'list-ol', 'list-ul', 'outdent', 'paperclip', 'paragraph', 'repeat', 'scissors', 'strikethrough', 'subscript', 'superscript', 'table', 'text-height', 'text-width', 'th', 'th-large', 'th-list', 'underline', 'undo', 'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-down', 'angle-left', 'angle-right', 'angle-up', 'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-o-down', 'arrow-circle-o-left', 'arrow-circle-o-right', 'arrow-circle-o-up', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows', 'arrows-alt', 'arrows-h', 'arrows-v', 'caret-down', 'caret-left', 'caret-right', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'caret-up', 'chevron-circle-down', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'exchange', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'long-arrow-down', 'long-arrow-left', 'long-arrow-right', 'long-arrow-up', 'arrows-alt', 'backward', 'compress', 'eject', 'expand', 'fast-backward', 'fast-forward', 'forward', 'pause', 'pause-circle', 'pause-circle-o', 'play', 'play-circle', 'play-circle-o', 'random', 'step-backward', 'step-forward', 'stop', 'stop-circle', 'stop-circle-o', 'youtube-play', '500px', 'adn', 'amazon', 'android', 'angellist', 'apple', 'bandcamp', 'behance', 'behance-square', 'bitbucket', 'bitbucket-square', 'black-tie', 'bluetooth', 'bluetooth-b', 'btc', 'buysellads', 'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'chrome', 'codepen', 'codiepie', 'connectdevelop', 'contao', 'css3', 'dashcube', 'delicious', 'deviantart', 'digg', 'dribbble', 'dropbox', 'drupal', 'edge', 'eercast', 'empire', 'envira', 'etsy', 'expeditedssl', 'facebook', 'facebook-official', 'facebook-square', 'firefox', 'first-order', 'flickr', 'font-awesome', 'fonticons', 'fort-awesome', 'forumbee', 'foursquare', 'free-code-camp', 'get-pocket', 'gg', 'gg-circle', 'git', 'git-square', 'github', 'github-alt', 'github-square', 'gitlab', 'glide', 'glide-g', 'google', 'google-plus', 'google-plus-official', 'google-plus-square', 'google-wallet', 'gratipay', 'grav', 'hacker-news', 'houzz', 'html5', 'imdb', 'instagram', 'internet-explorer', 'ioxhost', 'joomla', 'jsfiddle', 'lastfm', 'lastfm-square', 'leanpub', 'linkedin', 'linkedin-square', 'linode', 'linux', 'maxcdn', 'meanpath', 'medium', 'meetup', 'mixcloud', 'modx', 'odnoklassniki', 'odnoklassniki-square', 'opencart', 'openid', 'opera', 'optin-monster', 'pagelines', 'paypal', 'pied-piper', 'pied-piper-alt', 'pied-piper-pp', 'pinterest', 'pinterest-p', 'pinterest-square', 'product-hunt', 'qq', 'quora', 'ravelry', 'rebel', 'reddit', 'reddit-alien', 'reddit-square', 'renren', 'safari', 'scribd', 'sellsy', 'share-alt', 'share-alt-square', 'shirtsinbulk', 'simplybuilt', 'skyatlas', 'skype', 'slack', 'slideshare', 'snapchat', 'snapchat-ghost', 'snapchat-square', 'soundcloud', 'spotify', 'stack-exchange', 'stack-overflow', 'steam', 'steam-square', 'stumbleupon', 'stumbleupon-circle', 'superpowers', 'telegram', 'tencent-weibo', 'themeisle', 'trello', 'tripadvisor', 'tumblr', 'tumblr-square', 'twitch', 'twitter', 'twitter-square', 'usb', 'viacoin', 'viadeo', 'viadeo-square', 'vimeo', 'vimeo-square', 'vine', 'vk', 'weibo', 'weixin', 'whatsapp', 'wikipedia-w', 'windows', 'wordpress', 'wpbeginner', 'wpexplorer', 'wpforms', 'xing', 'xing-square', 'y-combinator', 'yahoo', 'yelp', 'yoast', 'youtube', 'youtube-play', 'youtube-square'];

		return $font_awesome_icons;
	}

	/**
	 * @return string
	 */
	private function get_announcement_for_edit()
	{
		$announcement_text = $this->config_text->get('ub_announcement_text') ?? '';
		if ($this->config['ub_announcement_html'])
		{
			return $announcement_text;
		}
		else
		{
			$options = 0;
			if ($this->config['ub_announcement_bbcode'])
			{
				$options |= OPTION_FLAG_BBCODE;
			}
			if ($this->config['ub_announcement_smilies'])
			{
				$options |= OPTION_FLAG_SMILIES;
			}
			if ($this->config['ub_announcement_urls'])
			{
				$options |= OPTION_FLAG_LINKS;
			}
			$uid = $this->config['ub_announcement_uid'] ?? '';
			return generate_text_for_edit($announcement_text, $uid, $options)['text'];
		}
	}
}
