<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

use mrgoldy\ultimateblog\constants;

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(

	'ACP_ULTIMATEBLOG'		=> 'Ultimate Blog',

	'ACP_UB_ALLOW_BBCODES'				=> 'Allow BBCodes',
	'ACP_UB_ALLOW_BBCODES_EXPLAIN'		=> 'Allow the usage of BBCodes within the Ultimate Blog extension.',
	'ACP_UB_ALLOW_MAGIC_URL'			=> 'Allow links',
	'ACP_UB_ALLOW_MAGIC_URL_EXPLAIN'	=> 'If disallowed, the <code>[URL]</code> BBCode and automatic/magic URLs are disabled within the Ultimate Blog extension.',
	'ACP_UB_ALLOW_SMILIES'				=> 'Allow smilies',
	'ACP_UB_ALLOW_SMILIES_EXPLAIN'		=> 'Allow the usage of smilies within the Ultimate Blog extension.',
	'ACP_UB_ANNOUNCEMENT_TEXT'			=> 'Announcement text',
	'ACP_UB_ANNOUNCEMENT_TEXT_EXPLAIN'	=> 'The text to be displayed above all the Ultimate Blog pages. Both <strong>HTML</strong> and <strong>BBCodes</strong> are supported based on option selected for parsing HTML.',

	'ACP_UB_BLOCK_AMOUNT'				=> 'Show amount',
	'ACP_UB_BLOCK_CATEGORY'				=> 'Specific category',
	'ACP_UB_BLOCK_CATEGORY_EXPLAIN'		=> 'Show blogs from a specific category',
	'ACP_UB_BLOCK_COMMENTS'				=> 'Latest commented',
	'ACP_UB_BLOCK_COMMENTS_EXPLAIN'		=> 'Show blogs with latest comments',
	'ACP_UB_BLOCK_LATEST'				=> 'Latest',
	'ACP_UB_BLOCK_LATEST_EXPLAIN'		=> 'Show latest blogs',
	'ACP_UB_BLOCK_RATING'				=> 'Highest rating',
	'ACP_UB_BLOCK_RATING_EXPLAIN'		=> 'Show blogs with highest rating',
	'ACP_UB_BLOCK_RATING_REQUIRED'		=> 'Required ratings',
	'ACP_UB_BLOCK_VIEWS'				=> 'Most viewed',
	'ACP_UB_BLOCK_VIEWS_EXPLAIN'		=> 'Show blogs with most views',

	'ACP_UB_BLOGS'						=> 'Blogs',
	'ACP_UB_BLOGS_PER_PAGE'				=> 'Blogs per page',
	'ACP_UB_BLOGS_PER_PAGE_EXPLAIN'		=> 'The amount of blogs that are displayed per page.',
	'ACP_UB_COMMENTS_PER_PAGE'			=> 'Comments per page',
	'ACP_UB_COMMENTS_PER_PAGE_EXPLAIN'	=> 'The amount of comments that are displayed per page.',

	'ACP_UB_BLOG_MIN_CHARS'				=> 'Blog text minimum characters',
	'ACP_UB_BLOG_MIN_CHARS_EXPLAIN'		=> 'The minimum amount of characters to be in a blog text. Set to <strong>0</strong> to disable this feature.',

	'ACP_UB_CATEGORIES'					=> 'Ultimate Blog Categories',
	'ACP_UB_CATEGORIES_EXPLAIN'			=> 'Here you can add, edit and delete the categories that are available.',

	'ACP_UB_CATEGORY'						=> 'Category',
	'ACP_UB_CATEGORY_ADD'					=> 'Add category',
	'ACP_UB_CATEGORY_ADD_EXPLAIN'			=> 'Fill in all the information for this Ultimate Blog category. A category name is mandatory, while a description is optionable.',
	'ACP_UB_CATEGORY_ADDED'					=> 'A new Ultimate Blog category has successfully been added.',
	'ACP_UB_CATEGORY_DELETED'				=> 'The Ultimate Blog category has successfully been deleted.',
	'ACP_UB_CATEGORY_DELETE_CONFIRM'		=> 'Are you sure you want to delete this Ultimate Blog category?',
	'ACP_UB_CATEGORY_DELETED_DELETE'		=> array(
		0	=> 'The Ultimate Blog category has successfully been deleted.<br /><br />There were %d blogs that only belonged to this category. No blogs have been deleted.',
		1	=> 'The Ultimate Blog category has successfully been deleted.<br /><br />There was %d blog that only belonged to this category. This blog has been deleted:<br />%s',
		2	=> 'The Ultimate Blog category has successfully been deleted.<br /><br />There were %d blogs that only belonged to this category. These blogs have been deleted:<br />%s',
	),
	'ACP_UB_CATEGORY_DELETE_MOVE'			=> array(
		0	=> 'The Ultimate Blog category has successfully been deleted.<br /><br />There were %d blogs that did not already have the new category. No blogs have been moved.',
		1	=> 'The Ultimate Blog category has successfully been deleted.<br /><br />There was %d blog that did not already have the new category. The blog has been added to the new category:<br />%s',
		2	=> 'The Ultimate Blog category has successfully been deleted.<br /><br />There were %d blogs that did not already have the new category. The blogs have been added to the new category:<br />%s',
	),
	'ACP_UB_CATEGORY_DESCRIPTION'			=> 'Category description',
	'ACP_UB_CATEGORY_DESCRIPTION_EXPLAIN'	=> 'The description for this category.<br />Availability for BBCode, smilies and automatic URL parsing is shown in the options, this is inheriting from the <em>Ultimate Blog Settings</em>. It is also possible to disable them for this specific description by clicking the check boxes.',
	'ACP_UB_CATEGORY_EDIT'					=> 'Edit category',
	'ACP_UB_CATEGORY_EDIT_EXPLAIN'			=> 'Edit all the information for this Ultimate Blog category. A category name is mandatory, while a description is optionable.',
	'ACP_UB_CATEGORY_EDITED'				=> 'The Ultimate Blog category has successfully been edited.',
	'ACP_UB_CATEGORY_IMAGE'					=> 'Category image',
	'ACP_UB_CATEGORY_IMAGE_CURRENT'			=> 'Current category image',
	'ACP_UB_CATEGORY_IMAGE_EXPLAIN'			=> '(Optional) Upload an image for this category. Maximum filesize and the file directory is set within the Ultimate Blog settings.',
	'ACP_UB_CATEGORY_NAME'					=> 'Category name',
	'ACP_UB_CATEGORY_NAME_EXPLAIN'			=> 'The name for this category.',
	'ACP_UB_CATEGORY_PRIVATE'				=> 'Category is private',
	'ACP_UB_CATEGORY_PRIVATE_EXPLAIN'		=> 'This determines whether everyone can post in this category or only users who have the <em>“Can post blogs in private categories”</em> permissions. Do note that <strong>everyone can read</strong> blogs in this category.',
	'ACP_UB_CATEGORY_SETTINGS'				=> 'Ultimate Blog category settings',

	'ACP_UB_CUSTOM_INDEX'					=> 'Use custom index',
	'ACP_UB_CUSTOM_INDEX_EXPLAIN'			=> 'Either use the standard <em>(Latest blogs and Blogs per page)</em> blog index, or the custom one which you can lay out below.',
	'ACP_UB_CUSTOM_INDEX_SETTINGS'			=> 'Custom index settings',

	'ACP_UB_ENABLE'							=> 'Enable Ultimate Blog extension',
	'ACP_UB_ENABLE_ANNOUNCEMENT'			=> 'Enable announcements',
	'ACP_UB_ENABLE_ANNOUNCEMENT_EXPLAIN'	=> 'Shows an announcement <em>(defined by you)</em> at the top of all Ultimate Blog pages.',
	'ACP_UB_ENABLE_COMMENTS'				=> 'Enable comments',
	'ACP_UB_ENABLE_COMMENTS_EXPLAIN'		=> 'Allow users to comment on blogs.',
	'ACP_UB_ENABLE_FRIENDS_ONLY'			=> 'Enable friends only',
	'ACP_UB_ENABLE_FRIENDS_ONLY_EXPLAIN'	=> 'Users can select to show their blog posts to users in their friendslist.<br>You got to have the Friends UCP Module <em>(both Zebra and Zebra friends)</em> enabled. ',
	'ACP_UB_ENABLE_RATING'					=> 'Enable rating',
	'ACP_UB_ENABLE_RATING_EXPLAIN'			=> 'Users can rate a blog on a 5 star scale.',
	'ACP_UB_ENABLE_FEED'					=> 'Enable feed',
	'ACP_UB_ENABLE_FEED_EXPLAIN'			=> 'Turns on or off ATOM feeds for the entire extension.',
	'ACP_UB_ENABLE_FEED_CATS'				=> 'Enable per-category feed',
	'ACP_UB_ENABLE_FEED_CATS_EXPLAIN'		=> 'Enables the “per-category” feed, which displays a list of blogs per single category.',
	'ACP_UB_ENABLE_FEED_LIMIT'				=> 'Number of items',
	'ACP_UB_ENABLE_FEED_LIMIT_EXPLAIN'		=> 'The maximum number of feed items to display.<br><em>(Has to be between 5 and 100)</em>',
	'ACP_UB_ENABLE_FEED_SETTINGS'			=> 'General feed settings',
	'ACP_UB_ENABLE_FEED_STATS'				=> 'Item statistics',
	'ACP_UB_ENABLE_FEED_STATS_EXPLAIN'		=> 'Display individual statistics underneath feed items.<br><em>(e.g. category count, edit count, views, comments, rating)</em>',
	'ACP_UB_ENABLE_SUBSCRIPTIONS'			=> 'Enable subscriptions',
	'ACP_UB_ENABLE_SUBSCRIPTIONS_EXPLAIN'	=> 'Allows users to subscribe to categories and blogs and receive a notification when a new blog or comment is posted.',

	'ACP_UB_ERROR_BLOGS_PER_PAGE'		=> 'You have to set the “Blog per page” setting. The minimum amount is <strong>3</strong>.',
	'ACP_UB_ERROR_CATEGORY_DESCRIPTION_OUT_OF_BOUNDS'	=> 'The category description cannot be more than ' . constants::BLOG_DESC_MAXIMUM . ' characters, wihtout BBCodes. You currently have %s characters.',
	'ACP_UB_ERROR_CATEGORY_IMAGE'		=> 'There was an error with your category image:',
	'ACP_UB_ERROR_COMMENTS_PER_PAGE'	=> 'You have to set the “Comments per page” setting. The minimum amount is <strong>5</strong>.',
	'ACP_UB_ERROR_CUSTOM_INDEX_EMPTY'	=> 'You have to set atleast one block in your “Custom index” or disable to setting.',
	'ACP_UB_ERROR_EMPTY_CATEGORY_NAME'	=> 'You have to set a category name.',
	'ACP_UB_ERROR_EMPTY_TITLE'			=> 'You have to set the “Ultimate Blog title” setting.',
	'ACP_UB_ERROR_FEED_LIMIT'			=> 'The feed limit has to be between 5 and 100. You have it set to %s.',

	'ACP_UB_FA_ICON'			=> 'Font Awesome icon',
	'ACP_UB_FA_ICON_CHOOSE'		=> 'Choose icon',
	'ACP_UB_FA_ICON_EXPLAIN'	=> 'The icon used in the <em>Navigation Bar</em> link. You can choose any <a href="http://fontawesome.io/icons/" title="Font Awesome Icons" target="_blank"><strong>Font Awesome icon</strong></a>.<br>It also shows a preview of your current navigation bar link.',

	'ACP_UB_IMAGE_SIZE'				=> 'Maximum image filesize',
	'ACP_UB_IMAGE_SIZE_EXPLAIN'		=> 'Maximum size of each file. If this value is 0, the uploadable filesize is only limited by your PHP configuration.<br>Average image filesizes: PNG ~ 2–4 kB, GIF ~ 6–8 kB, JPG ~ 9–12 kB',
	'ACP_UB_IMAGE_DIR'				=> 'Blog image upload directory',
	'ACP_UB_IMAGE_DIR_EXPLAIN'		=> 'Storage path for blog images. Please note that if you change this directory while already having uploaded blog images you need to manually copy the files to their new location.',
	'ACP_UB_IMAGE_CAT_DIR'			=> 'Category image upload directory',
	'ACP_UB_IMAGE_CAT_DIR_EXPLAIN'	=> 'Storage path for category images. Please note that if you change this directory while already having uploaded category images you need to manually copy the files to their new location.',

	'ACP_UB_INVALID_HASH'		=> 'Error: invalid hash',

	'ACP_UB_LOG_BLOG_ADDED'				=> '<strong>Added an Ultimate Blog blog</strong><br>» %s', // Please leave the 'Ultimate Blog' part
	'ACP_UB_LOG_BLOG_DELETED'			=> '<strong>Deleted an Ultimate Blog blog</strong><br>» %s', // Of each log sentence in place
	'ACP_UB_LOG_BLOG_EDITED'			=> '<strong>Edited an Ultimate Blog blog</strong><br>» %s', // It used to filter all log entries in the overview page
	'ACP_UB_LOG_BLOG_EDIT_DELETED'		=> '<strong>Deleted an Ultimate Blog edit reason</strong><br>» Blog: “%3$s” - Edit: %1$s: %2$s',
	'ACP_UB_LOG_BLOG_RATED'				=> '<strong>Rated an Ultimate Blog (%2$s stars)</strong><br>» %1$s',
	'ACP_UB_LOG_BLOG_REPORTED'			=> '<strong>Reported an Ultimate Blog blog</strong>',
	'ACP_UB_LOG_CATEGORY_ADDED'			=> '<strong>Created a new Ultimate Blog category</strong><br>» %s',
	'ACP_UB_LOG_CATEGORY_EDITED'		=> '<strong>Edited an Ultimate Blog category</strong><br>» %s',
	'ACP_UB_LOG_CATEGORY_DELETED'		=> '<strong>Deleted an Ultimate Blog category</strong><br>» %s',
	'ACP_UB_LOG_COMMENT_ADDED'			=> '<strong>Added an Ultimate Blog comment</strong><br>» <a href="%1$s">View comment</a>',
	'ACP_UB_LOG_COMMENT_DELETED'		=> '<strong>Deleted an Ultimate Blog comment</strong>',
	'ACP_UB_LOG_COMMENT_EDITED'			=> '<strong>Edited an Ultimate Blog comment</strong>',
	'ACP_UB_LOG_COMMENT_REPORTED'		=> '<strong>Reported an Ultimate Blog comment</strong>',
	'ACP_UB_LOG_REPORT_CLOSED'			=> '<strong>Closed an Ultimate Blog report</strong>',
	'ACP_UB_LOG_PURGE_IMAGES_BLOG'		=> '<strong>Purged Ultimate Blog blog images</strong>',
	'ACP_UB_LOG_PURGE_IMAGES_CATEGORY'	=> '<strong>Purged Ultimate Blog category images</strong>',
	'ACP_UB_LOG_REPORT_DELETED'			=> '<strong>Deleted an Ultimate Blog report</strong>',
	'ACP_UB_LOG_SETTINGS'				=> '<strong>Edited Ultimate Blog settings</strong>',

	'ACP_UB_OVERVIEW'						=> 'Ultimate Blog Overview',
	'ACP_UB_OVERVIEW_EXPLAIN'				=> 'This screen will give you a quick overview of all the various statistics of your Ultimate Blog extension, aswell as some useful functions.',
	'ACP_UB_OVERVIEW_BLOG_COMMENTS'			=> 'Blog with most comments',
	'ACP_UB_OVERVIEW_BLOG_RATING_HIGH'		=> 'Blog with highest rating',
	'ACP_UB_OVERVIEW_BLOG_RATING_LOW'		=> 'Blog with lowest rating',
	'ACP_UB_OVERVIEW_BLOG_VIEWS'			=> 'Blog with most views',
	'ACP_UB_OVERVIEW_BLOGS'					=> 'Number of blogs',
	'ACP_UB_OVERVIEW_BLOGS_DAY'				=> 'Blogs per day',
	'ACP_UB_OVERVIEW_CATEGORIES'			=> 'Number of categories',
	'ACP_UB_OVERVIEW_COMMENTS'				=> 'Number of comments',
	'ACP_UB_OVERVIEW_COMMENTS_DAY'			=> 'Comments per day',
	'ACP_UB_OVERVIEW_EXT_STARTED'			=> 'Extension started',
	'ACP_UB_OVERVIEW_EXT_UPTIME'			=> 'Extension uptime',
	'ACP_UB_OVERVIEW_USER_BLOGS'			=> 'User with most blogs',
	'ACP_UB_OVERVIEW_USER_COMMENTS'			=> 'User with most comments',
	'ACP_UB_OVERVIEW_VIEWS'					=> 'Number of blog views',

	'ACP_UB_PURGE_IMAGES'					=> 'Purge Ultimate Blog images',
	'ACP_UB_PURGE_IMAGES_BLOG'				=> 'Purge blog images',
	'ACP_UB_PURGE_IMAGES_BLOG_CONFIRM'		=> 'Are you sure you wish to purge the blog images?',
	'ACP_UB_PURGE_IMAGES_BLOG_EXPLAIN'		=> 'Purge all blog images that are not in use. When adding new blog images, old images are automatically deleted. This option is here to get rid of those which felt through the cracks.',
	'ACP_UB_PURGE_IMAGES_BLOG_SUCCESS'		=> 'Blog images successfully purged.',
	'ACP_UB_PURGE_IMAGES_CATEGORY'			=> 'Purge category images',
	'ACP_UB_PURGE_IMAGES_CATEGORY_CONFIRM'	=> 'Are you sure you wish to purge the category images?',
	'ACP_UB_PURGE_IMAGES_CATEGORY_EXPLAIN'	=> 'Purge all category images that are not in use. When adding new category images, old images are automatically deleted. This option is here to get rid of those which felt through the cracks.',
	'ACP_UB_PURGE_IMAGES_CATEGORY_SUCCESS'	=> 'Category images successfully purged.',

	'ACP_UB_SETTINGS'					=> 'Ultimate Blog Settings',
	'ACP_UB_SETTINGS_CHANGED'			=> 'The Ultimate Blog settings have successfully been saved.',
	'ACP_UB_SETTINGS_EXPLAIN'			=> 'Configurate all the settings for the Ultimate Blog extension.',
	'ACP_UB_STATISTICS'					=> 'Ultimate Blog statistics',

	'ACP_UB_TITLE'			=> 'Ultimate Blog title',
	'ACP_UB_TITLE_EXPLAIN'	=> 'This is the title that is used in the <em>Navigation Bar</em> link and on a few pages.<br>Basically it is the name of this extension users get to see.'	,
	'ACP_UB_PARSE_HTML'						=> 'Parse HTML',
));
