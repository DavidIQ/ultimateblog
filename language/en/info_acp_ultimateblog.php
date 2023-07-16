<?php
/**
 *
 * Ultimate Library. An extension for the phpBB Forum Software package.
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

	'ACP_ULTIMATEBLOG'		=> 'Ultimate Library',

	'ACP_UB_ALLOW_BBCODES'				=> 'Allow BBCodes',
	'ACP_UB_ALLOW_BBCODES_EXPLAIN'		=> 'Allow the usage of BBCodes within the Ultimate Library extension.',
	'ACP_UB_ALLOW_MAGIC_URL'			=> 'Allow links',
	'ACP_UB_ALLOW_MAGIC_URL_EXPLAIN'	=> 'If disallowed, the <code>[URL]</code> BBCode and automatic/magic URLs are disabled within the Ultimate Library extension.',
	'ACP_UB_ALLOW_SMILIES'				=> 'Allow smilies',
	'ACP_UB_ALLOW_SMILIES_EXPLAIN'		=> 'Allow the usage of smilies within the Ultimate Library extension.',
	'ACP_UB_ANNOUNCEMENT_TEXT'			=> 'Announcement text',
	'ACP_UB_ANNOUNCEMENT_TEXT_EXPLAIN'	=> 'The text to be displayed above all the Ultimate Library pages. Both <strong>HTML</strong> and <strong>BBCodes</strong> are supported based on option selected for parsing HTML.',

	'ACP_UB_BLOCK_AMOUNT'				=> 'Show amount',
	'ACP_UB_BLOCK_CATEGORY'				=> 'Specific category',
	'ACP_UB_BLOCK_CATEGORY_EXPLAIN'		=> 'Show stories from a specific category',
	'ACP_UB_BLOCK_COMMENTS'				=> 'Latest commented',
	'ACP_UB_BLOCK_COMMENTS_EXPLAIN'		=> 'Show stories with latest comments',
	'ACP_UB_BLOCK_LATEST'				=> 'Latest',
	'ACP_UB_BLOCK_LATEST_EXPLAIN'		=> 'Show latest stories',
	'ACP_UB_BLOCK_RATING'				=> 'Highest rating',
	'ACP_UB_BLOCK_RATING_EXPLAIN'		=> 'Show stories with highest rating',
	'ACP_UB_BLOCK_RATING_REQUIRED'		=> 'Required ratings',
	'ACP_UB_BLOCK_VIEWS'				=> 'Most viewed',
	'ACP_UB_BLOCK_VIEWS_EXPLAIN'		=> 'Show stories with most views',

	'ACP_UB_BLOGS'						=> 'Stories',
	'ACP_UB_BLOGS_PER_PAGE'				=> 'Stories per page',
	'ACP_UB_BLOGS_PER_PAGE_EXPLAIN'		=> 'The amount of stories that are displayed per page.',
	'ACP_UB_COMMENTS_PER_PAGE'			=> 'Comments per page',
	'ACP_UB_COMMENTS_PER_PAGE_EXPLAIN'	=> 'The amount of comments that are displayed per page.',

	'ACP_UB_BLOG_MIN_CHARS'				=> 'Story text minimum characters',
	'ACP_UB_BLOG_MIN_CHARS_EXPLAIN'		=> 'The minimum amount of characters to be in a story text. Set to <strong>0</strong> to disable this feature.',

	'ACP_UB_CATEGORIES'					=> 'Ultimate Library Categories',
	'ACP_UB_CATEGORIES_EXPLAIN'			=> 'Here you can add, edit and delete the categories that are available.',

	'ACP_UB_CATEGORY'						=> 'Category',
	'ACP_UB_CATEGORY_ADD'					=> 'Add category',
	'ACP_UB_CATEGORY_ADD_EXPLAIN'			=> 'Fill in all the information for this Ultimate Library category. A category name is mandatory, while a description is optionable.',
	'ACP_UB_CATEGORY_ADDED'					=> 'A new Ultimate Library category has successfully been added.',
	'ACP_UB_CATEGORY_DELETED'				=> 'The Ultimate Library category has successfully been deleted.',
	'ACP_UB_CATEGORY_DELETE_CONFIRM'		=> 'Are you sure you want to delete this Ultimate Library category?',
	'ACP_UB_CATEGORY_DELETED_DELETE'		=> array(
		0	=> 'The Ultimate Library category has successfully been deleted.<br /><br />There were %d stories that only belonged to this category. No stories have been deleted.',
		1	=> 'The Ultimate Library category has successfully been deleted.<br /><br />There was %d story that only belonged to this category. This story has been deleted:<br />%s',
		2	=> 'The Ultimate Library category has successfully been deleted.<br /><br />There were %d stories that only belonged to this category. These stories have been deleted:<br />%s',
	),
	'ACP_UB_CATEGORY_DELETE_MOVE'			=> array(
		0	=> 'The Ultimate Library category has successfully been deleted.<br /><br />There were %d stories that did not already have the new category. No stories have been moved.',
		1	=> 'The Ultimate Library category has successfully been deleted.<br /><br />There was %d story that did not already have the new category. The story has been added to the new category:<br />%s',
		2	=> 'The Ultimate Library category has successfully been deleted.<br /><br />There were %d stories that did not already have the new category. The stories have been added to the new category:<br />%s',
	),
	'ACP_UB_CATEGORY_DESCRIPTION'			=> 'Category description',
	'ACP_UB_CATEGORY_DESCRIPTION_EXPLAIN'	=> 'The description for this category.<br />Availability for BBCode, smilies and automatic URL parsing is shown in the options, this is inheriting from the <em>Ultimate Library Settings</em>. It is also possible to disable them for this specific description by clicking the check boxes.',
	'ACP_UB_CATEGORY_EDIT'					=> 'Edit category',
	'ACP_UB_CATEGORY_EDIT_EXPLAIN'			=> 'Edit all the information for this Ultimate Library category. A category name is mandatory, while a description is optionable.',
	'ACP_UB_CATEGORY_EDITED'				=> 'The Ultimate Library category has successfully been edited.',
	'ACP_UB_CATEGORY_IMAGE'					=> 'Category image',
	'ACP_UB_CATEGORY_IMAGE_CURRENT'			=> 'Current category image',
	'ACP_UB_CATEGORY_IMAGE_EXPLAIN'			=> '(Optional) Upload an image for this category. Maximum filesize and the file directory is set within the Ultimate Library settings.',
	'ACP_UB_CATEGORY_NAME'					=> 'Category name',
	'ACP_UB_CATEGORY_NAME_EXPLAIN'			=> 'The name for this category.',
	'ACP_UB_CATEGORY_PRIVATE'				=> 'Category is private',
	'ACP_UB_CATEGORY_PRIVATE_EXPLAIN'		=> 'This determines whether everyone can post in this category or only users who have the <em>“Can post stories in private categories”</em> permissions. Do note that <strong>everyone can read</strong> blogs in this category.',
	'ACP_UB_CATEGORY_SETTINGS'				=> 'Ultimate Library category settings',

	'ACP_UB_CUSTOM_INDEX'					=> 'Use custom index',
	'ACP_UB_CUSTOM_INDEX_EXPLAIN'			=> 'Either use the standard <em>(Latest stories and Libraries per page)</em> story index, or the custom one which you can lay out below.',
	'ACP_UB_CUSTOM_INDEX_SETTINGS'			=> 'Custom index settings',

	'ACP_UB_ENABLE'							=> 'Enable Ultimate Library extension',
	'ACP_UB_ENABLE_ANNOUNCEMENT'			=> 'Enable announcements',
	'ACP_UB_ENABLE_ANNOUNCEMENT_EXPLAIN'	=> 'Shows an announcement <em>(defined by you)</em> at the top of all Ultimate Library pages.',
	'ACP_UB_ENABLE_COMMENTS'				=> 'Enable comments',
	'ACP_UB_ENABLE_COMMENTS_EXPLAIN'		=> 'Allow users to comment on stories.',
	'ACP_UB_ENABLE_FRIENDS_ONLY'			=> 'Enable friends only',
	'ACP_UB_ENABLE_FRIENDS_ONLY_EXPLAIN'	=> 'Users can select to show their story posts to users in their friendslist.<br>You got to have the Friends UCP Module <em>(both Zebra and Zebra friends)</em> enabled. ',
	'ACP_UB_ENABLE_RATING'					=> 'Enable rating',
	'ACP_UB_ENABLE_RATING_EXPLAIN'			=> 'Users can rate a story on a 5 star scale.',
	'ACP_UB_ENABLE_FEED'					=> 'Enable feed',
	'ACP_UB_ENABLE_FEED_EXPLAIN'			=> 'Turns on or off ATOM feeds for the entire extension.',
	'ACP_UB_ENABLE_FEED_CATS'				=> 'Enable per-category feed',
	'ACP_UB_ENABLE_FEED_CATS_EXPLAIN'		=> 'Enables the “per-category” feed, which displays a list of stories per single category.',
	'ACP_UB_ENABLE_FEED_LIMIT'				=> 'Number of items',
	'ACP_UB_ENABLE_FEED_LIMIT_EXPLAIN'		=> 'The maximum number of feed items to display.<br><em>(Has to be between 5 and 100)</em>',
	'ACP_UB_ENABLE_FEED_SETTINGS'			=> 'General feed settings',
	'ACP_UB_ENABLE_FEED_STATS'				=> 'Item statistics',
	'ACP_UB_ENABLE_FEED_STATS_EXPLAIN'		=> 'Display individual statistics underneath feed items.<br><em>(e.g. category count, edit count, views, comments, rating)</em>',
	'ACP_UB_ENABLE_SUBSCRIPTIONS'			=> 'Enable subscriptions',
	'ACP_UB_ENABLE_SUBSCRIPTIONS_EXPLAIN'	=> 'Allows users to subscribe to categories and stories and receive a notification when a new story or comment is posted.',

	'ACP_UB_ERROR_BLOGS_PER_PAGE'		=> 'You have to set the “Story per page” setting. The minimum amount is <strong>3</strong>.',
	'ACP_UB_ERROR_CATEGORY_DESCRIPTION_OUT_OF_BOUNDS'	=> 'The category description cannot be more than ' . constants::BLOG_DESC_MAXIMUM . ' characters, wihtout BBCodes. You currently have %s characters.',
	'ACP_UB_ERROR_CATEGORY_IMAGE'		=> 'There was an error with your category image:',
	'ACP_UB_ERROR_COMMENTS_PER_PAGE'	=> 'You have to set the “Comments per page” setting. The minimum amount is <strong>5</strong>.',
	'ACP_UB_ERROR_CUSTOM_INDEX_EMPTY'	=> 'You have to set atleast one block in your “Custom index” or disable to setting.',
	'ACP_UB_ERROR_EMPTY_CATEGORY_NAME'	=> 'You have to set a category name.',
	'ACP_UB_ERROR_EMPTY_TITLE'			=> 'You have to set the “Ultimate Library title” setting.',
	'ACP_UB_ERROR_FEED_LIMIT'			=> 'The feed limit has to be between 5 and 100. You have it set to %s.',

	'ACP_UB_FA_ICON'			=> 'Font Awesome icon',
	'ACP_UB_FA_ICON_CHOOSE'		=> 'Choose icon',
	'ACP_UB_FA_ICON_EXPLAIN'	=> 'The icon used in the <em>Navigation Bar</em> link. You can choose any <a href="http://fontawesome.io/icons/" title="Font Awesome Icons" target="_blank"><strong>Font Awesome icon</strong></a>.<br>It also shows a preview of your current navigation bar link.',

	'ACP_UB_IMAGE_SIZE'				=> 'Maximum image filesize',
	'ACP_UB_IMAGE_SIZE_EXPLAIN'		=> 'Maximum size of each file. If this value is 0, the uploadable filesize is only limited by your PHP configuration.<br>Average image filesizes: PNG ~ 2–4 kB, GIF ~ 6–8 kB, JPG ~ 9–12 kB',
	'ACP_UB_IMAGE_DIR'				=> 'Story image upload directory',
	'ACP_UB_IMAGE_DIR_EXPLAIN'		=> 'Storage path for story images. Please note that if you change this directory while already having uploaded story images you need to manually copy the files to their new location.',
	'ACP_UB_IMAGE_CAT_DIR'			=> 'Category image upload directory',
	'ACP_UB_IMAGE_CAT_DIR_EXPLAIN'	=> 'Storage path for category images. Please note that if you change this directory while already having uploaded category images you need to manually copy the files to their new location.',

	'ACP_UB_INVALID_HASH'		=> 'Error: invalid hash',

	'ACP_UB_LOG_BLOG_ADDED'				=> '<strong>Added an Ultimate Library blog</strong><br>» %s', // Please leave the 'Ultimate Library' part
	'ACP_UB_LOG_BLOG_DELETED'			=> '<strong>Deleted an Ultimate Library blog</strong><br>» %s', // Of each log sentence in place
	'ACP_UB_LOG_BLOG_EDITED'			=> '<strong>Edited an Ultimate Library blog</strong><br>» %s', // It used to filter all log entries in the overview page
	'ACP_UB_LOG_BLOG_EDIT_DELETED'		=> '<strong>Deleted an Ultimate Library edit reason</strong><br>» Library: “%3$s” - Edit: %1$s: %2$s',
	'ACP_UB_LOG_BLOG_RATED'				=> '<strong>Rated an Ultimate Library (%2$s stars)</strong><br>» %1$s',
	'ACP_UB_LOG_BLOG_REPORTED'			=> '<strong>Reported an Ultimate Library blog</strong>',
	'ACP_UB_LOG_CATEGORY_ADDED'			=> '<strong>Created a new Ultimate Library category</strong><br>» %s',
	'ACP_UB_LOG_CATEGORY_EDITED'		=> '<strong>Edited an Ultimate Library category</strong><br>» %s',
	'ACP_UB_LOG_CATEGORY_DELETED'		=> '<strong>Deleted an Ultimate Library category</strong><br>» %s',
	'ACP_UB_LOG_COMMENT_ADDED'			=> '<strong>Added an Ultimate Library comment</strong><br>» <a href="%1$s">View comment</a>',
	'ACP_UB_LOG_COMMENT_DELETED'		=> '<strong>Deleted an Ultimate Library comment</strong>',
	'ACP_UB_LOG_COMMENT_EDITED'			=> '<strong>Edited an Ultimate Library comment</strong>',
	'ACP_UB_LOG_COMMENT_REPORTED'		=> '<strong>Reported an Ultimate Library comment</strong>',
	'ACP_UB_LOG_REPORT_CLOSED'			=> '<strong>Closed an Ultimate Library report</strong>',
	'ACP_UB_LOG_PURGE_IMAGES_BLOG'		=> '<strong>Purged Ultimate Library blog images</strong>',
	'ACP_UB_LOG_PURGE_IMAGES_CATEGORY'	=> '<strong>Purged Ultimate Library category images</strong>',
	'ACP_UB_LOG_REPORT_DELETED'			=> '<strong>Deleted an Ultimate Library report</strong>',
	'ACP_UB_LOG_SETTINGS'				=> '<strong>Edited Ultimate Library settings</strong>',

	'ACP_UB_OVERVIEW'						=> 'Ultimate Library Overview',
	'ACP_UB_OVERVIEW_EXPLAIN'				=> 'This screen will give you a quick overview of all the various statistics of your Ultimate Library extension, aswell as some useful functions.',
	'ACP_UB_OVERVIEW_BLOG_COMMENTS'			=> 'Story with most comments',
	'ACP_UB_OVERVIEW_BLOG_RATING_HIGH'		=> 'Story with highest rating',
	'ACP_UB_OVERVIEW_BLOG_RATING_LOW'		=> 'Story with lowest rating',
	'ACP_UB_OVERVIEW_BLOG_VIEWS'			=> 'Story with most views',
	'ACP_UB_OVERVIEW_BLOGS'					=> 'Number of stories',
	'ACP_UB_OVERVIEW_BLOGS_DAY'				=> 'Stories per day',
	'ACP_UB_OVERVIEW_CATEGORIES'			=> 'Number of categories',
	'ACP_UB_OVERVIEW_COMMENTS'				=> 'Number of comments',
	'ACP_UB_OVERVIEW_COMMENTS_DAY'			=> 'Comments per day',
	'ACP_UB_OVERVIEW_EXT_STARTED'			=> 'Extension started',
	'ACP_UB_OVERVIEW_EXT_UPTIME'			=> 'Extension uptime',
	'ACP_UB_OVERVIEW_USER_BLOGS'			=> 'User with most stories',
	'ACP_UB_OVERVIEW_USER_COMMENTS'			=> 'User with most comments',
	'ACP_UB_OVERVIEW_VIEWS'					=> 'Number of story views',

	'ACP_UB_PURGE_IMAGES'					=> 'Purge Ultimate Library images',
	'ACP_UB_PURGE_IMAGES_BLOG'				=> 'Purge story images',
	'ACP_UB_PURGE_IMAGES_BLOG_CONFIRM'		=> 'Are you sure you wish to purge the story images?',
	'ACP_UB_PURGE_IMAGES_BLOG_EXPLAIN'		=> 'Purge all story images that are not in use. When adding new story images, old images are automatically deleted. This option is here to get rid of those which felt through the cracks.',
	'ACP_UB_PURGE_IMAGES_BLOG_SUCCESS'		=> 'Story images successfully purged.',
	'ACP_UB_PURGE_IMAGES_CATEGORY'			=> 'Purge category images',
	'ACP_UB_PURGE_IMAGES_CATEGORY_CONFIRM'	=> 'Are you sure you wish to purge the category images?',
	'ACP_UB_PURGE_IMAGES_CATEGORY_EXPLAIN'	=> 'Purge all category images that are not in use. When adding new category images, old images are automatically deleted. This option is here to get rid of those which felt through the cracks.',
	'ACP_UB_PURGE_IMAGES_CATEGORY_SUCCESS'	=> 'Category images successfully purged.',

	'ACP_UB_SETTINGS'					=> 'Ultimate Library Settings',
	'ACP_UB_SETTINGS_CHANGED'			=> 'The Ultimate Library settings have successfully been saved.',
	'ACP_UB_SETTINGS_EXPLAIN'			=> 'Configurate all the settings for the Ultimate Library extension.',
	'ACP_UB_STATISTICS'					=> 'Ultimate Library statistics',

	'ACP_UB_TITLE'			=> 'Ultimate Library title',
	'ACP_UB_TITLE_EXPLAIN'	=> 'This is the title that is used in the <em>Navigation Bar</em> link and on a few pages.<br>Basically it is the name of this extension users get to see.'	,
	'ACP_UB_PARSE_HTML'						=> 'Parse HTML',
));
