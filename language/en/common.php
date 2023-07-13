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
	$lang = [];
}

$lang = array_merge($lang, [
	'BLOG'		=> 'Story',
	'BLOGS'		=> 'Stories',
	'BLOG_COUNT'	=> [
		1 => '%d story',
		2 => '%d stories',
	],

	'BLOG_ADD'						=> 'Add story',
	'BLOG_ARCHIVE'					=> 'Archive',
	'BLOG_ARCHIVE_DESCRIPTION'		=> 'A list of months, grouped by year, in which stories were posted. Click on a month to view all stories for that specific period.',
	'BLOG_ARCHIVE_DESCRIPTION_DATE'	=> 'All stories posted in the month of %1$s %2$s.', // %1$s is month (October), %2$s is year (2049)
	'BLOG_AUTHOR'					=> 'author',

	'BLOG_BLOG_CATEGORY'			=> 'Story category',

	'BLOG_CATEGORIES'				=> 'Categories',
	'BLOG_CATEGORIES_DESCRIPTION'	=> 'A list of categories in which stories are posted. Click on a category to view all stories for that specific library.',
	'BLOG_CATEGORIES_GO'			=> 'Go to category',
	'BLOG_CATEGORIES_PRIVATE'		=> 'Private category',

	'BLOG_COMMENTS'					=> 'Comments',
	'BLOG_COMMENTS_DELETE'			=> 'Delete this comment',
	'BLOG_COMMENTS_EDIT'			=> 'Edit this comment',
	'BLOG_COMMENTS_LINK'			=> 'Link to this comment',
	'BLOG_COMMENTS_POST'			=> 'Post a comment',
	'BLOG_COMMENTS_REPLY'			=> 'Reply to this comment',
	'BLOG_COMMENTS_REPORT'			=> 'Report this comment',
	'BLOG_COMMENTS_SHOW_MORE'		=> 'Show more comments',
	'BLOG_COMMENTS_SHOW_REPLIES'	=> 'Show replies (%d)',
	'BLOG_COMMENTS_COUNT'			=> [
		1	=> '%d comment',
		2	=> '%d comments',
	],

	'BLOG_DELETE'					=> 'Delete story',

	'BLOG_EDIT'						=> 'Edit story',
	'BLOG_EDIT_DELETE'				=> 'Delete edit reason',
	'BLOG_EDIT_REASONS'				=> 'Edit reasons',
	'BLOG_EDIT_TOTAL'				=> 'Total edits',
	'BLOG_EDIT_VIEW'				=> 'View all edits',
	'BLOG_EDITED_ON'				=> 'Edited on',

	'BLOG_ERROR_ARCHIVE_EMPTY'		=> 'There are <strong>no stories</strong> for this specific month and year combination.',
	'BLOG_ERROR_CANT_ADD'			=> 'You do <strong>not</strong> have the permission to post a story.',
	'BLOG_ERROR_CANT_COMMENT'		=> 'You do <strong>not</strong> have the permission to post a comment.',
	'BLOG_ERROR_CANT_COMMENT_DELETE' => 'You do <strong>not</strong> have the permission to delete a comment.',
	'BLOG_ERROR_CANT_COMMENT_EDIT'	=> 'You do <strong>not</strong> have the permission to edit a comment.',
	'BLOG_ERROR_CANT_DELETE'		=> 'You do <strong>not</strong> have the permission to delete a story.',
	'BLOG_ERROR_CANT_EDIT'			=> 'You do <strong>not</strong> have the permission to edit this story.',
	'BLOG_ERROR_CANT_EDIT_DELETE'	=> 'You do <strong>not</strong> have the permission to delete this edit reason.',
	'BLOG_ERROR_CANT_VIEW'			=> 'You do <strong>not</strong> have the permission to view this page.',
	'BLOG_ERROR_CATEGORY_NONE'		=> 'You have <strong>not</strong> selected any category. You need to select <strong>one <em>(1)</em> or more</strong> categories.',
	'BLOG_ERROR_DESC_BOUNDS'		=> 'Your story description is out of bounds. It has to be <strong>between ' . constants::BLOG_DESC_MINIMUM . ' and ' . constants::BLOG_DESC_MAXIMUM . '</strong> characters. It currently has %d characters.',
	'BLOG_ERROR_DISABLED'			=> 'The Library extension for this board has been disabled.',
	'BLOG_ERROR_IMAGE'				=> 'You have not <strong>added</strong> a image.',
	'BLOG_ERROR_INVALID_MODE'		=> 'You have entered an <strong>invalid</strong> mode.',
	'BLOG_ERROR_FRIENDS_ONLY'		=> 'This story is only visible to users in the <strong>friendslist of the author</strong>.',
	'BLOG_ERROR_MIN_CHARS'			=> 'Your story text has too few characters. It needs to have atleast <strong>%1$s</strong> characters and it currently has only <strong>%2$s</strong> characters.',
	'BLOG_ERROR_NO_BLOG'			=> 'There is <strong>no</strong> story post for this id.',
	'BLOG_ERROR_NO_CATEGORY'		=> 'There is <strong>no</strong> story category for this id.',
	'BLOG_ERROR_NO_USER_BLOG'		=> 'There are <strong>no</strong> story posts for this user.',
	'BLOG_ERROR_NO_TITLE'			=> 'You have <strong>not</strong> entered a story title.',

	'BLOG_GO'						=> 'Go to story',

	'BLOG_INDEX'					=> 'Index',
	'BLOG_INDEX_COMMENTED'			=> 'Last commented',
	'BLOG_INDEX_LATEST'				=> 'Latest',
	'BLOG_INDEX_RATED'				=> 'Highest rated',
	'BLOG_INDEX_VIEWED'				=> 'Most viewed',

	'BLOG_NEW'						=> 'New stories',

	'BLOG_PERM_ADD_CAN'				=> 'You <strong>can</strong> post new stories',
	'BLOG_PERM_ADD_NOT'				=> 'You <strong>cannot</strong> post new stories',
	'BLOG_PERM_COMMENT_CAN'			=> 'You <strong>can</strong> comment on stories',
	'BLOG_PERM_COMMENT_NOT'			=> 'You <strong>cannot</strong> comment on stories',
	'BLOG_PERM_COMMENT_VIEW_CAN'	=> 'You <strong>can</strong> view story comments',
	'BLOG_PERM_COMMENT_VIEW_NOT'	=> 'You <strong>cannot</strong> view story comments',
	'BLOG_PERM_DELETE_CAN'			=> 'You <strong>can</strong> delete your own stories',
	'BLOG_PERM_DELETE_NOT'			=> 'You <strong>cannot</strong> delete your own stories',
	'BLOG_PERM_EDIT_CAN'			=> 'You <strong>can</strong> edit your own stories',
	'BLOG_PERM_EDIT_NOT'			=> 'You <strong>cannot</strong> edit your own stories',
	'BLOG_PERM_EDIT_VIEW_CAN'		=> 'You <strong>can</strong> view story edit reasons',
	'BLOG_PERM_EDIT_VIEW_NOT'		=> 'You <strong>cannot</strong> view story edit reasons',
	'BLOG_PERM_NOAPPROVE_CAN'		=> 'You <strong>can</strong> post new stories without approval',
	'BLOG_PERM_NOAPPROVE_NOT'		=> 'You <strong>cannot</strong> post new stories without approval',
	'BLOG_PERM_RATE_CAN'			=> 'You <strong>can</strong> rate stories',
	'BLOG_PERM_RATE_NOT'			=> 'You <strong>cannot</strong> rate stories',
	'BLOG_PERM_REPORT_CAN'			=> 'You <strong>can</strong> report stories and comments',
	'BLOG_PERM_REPORT_NOT'			=> 'You <strong>cannot</strong> report stories and comments',

	'BLOG_POSTED_ON'				=> 'Posted on',

	'BLOG_RATING_AVG'				=> 'Average rating',
	'BLOG_RATING_NONE'				=> '<em>Not rated yet</em>',
	'BLOG_RATING_YOUR'				=> 'Your rating',
	'BLOG_RATING_COUNT'				=> [
		1	=> '%d rating',
		2	=> '%d ratings',
	],
	'BLOG_READ_FULL'				=> 'Read the full story',
	'BLOG_REPORT_BLOG'				=> 'Report story',
	'BLOG_REPORT_BLOG_EXPLAIN'		=> 'Use this form to report the selected story entry. Reporting should generally be used only if the story breaks forum and/or library rules.',
	'BLOG_REPORT_COMMENT'			=> 'Report comment',
	'BLOG_REPORT_COMMENT_EXPLAIN'	=> 'Use this form to report the selected comment entry. Reporting should generally be used only if the comment breaks forum and/or library rules.',
	'BLOG_REPORTED'					=> 'This story has been reported.',

	'BLOG_UNAPPROVED'				=> 'Unapproved',

	'BLOG_VIEW_PERMISSIONS'			=> 'View your permissions',
	'BLOG_VIEWS'					=> 'Views',

	'UB_NOTIFICATION_BLOG_DELETED'		=> '<strong>Story deleted</strong> by %1$s:<br>"%2$s"', // 1: Deleter name | 2: Blog title
	'UB_NOTIFICATION_BLOG_EDITED'		=> '<strong>Story edited</strong> by %1$s:<br>"%2$s"', // 1: Editor name | 2: Blog title
	'UB_NOTIFICATION_BLOG_REPORT'		=> '<strong>Story report closed</strong> by %1$s for:<br>"%2$s"', // 1: Moderator name | 2: Blog title
	'UB_NOTIFICATION_COMMENT_ADDED'		=> '<strong>Comment reply added</strong> by %1$s for:<br>"%2$s"', // 1: Author name | 2: Blog title
	'UB_NOTIFICATION_COMMENT_DELETED'	=> '<strong>Comment deleted</strong> by %1$s for:<br>"%2$s"', // 1: Deleter name | 2: Blog title
	'UB_NOTIFICATION_COMMENT_EDITED'	=> '<strong>Comment edited</strong> by %1$s for:<br>"%2$s"', // 1: Editor name | 2: Blog title
	'UB_NOTIFICATION_COMMENT_REPORT'	=> '<strong>Comment report closed</strong> by %1$s for:<br>"%2$s"', // 1: Moderator name | 2: Blog title
	'UB_NOTIFICATION_DEFAULT'			=> 'Notification for Library', // Should never get this.
	'UB_NOTIFICATION_TYPE_COMMENTS_MSG'	=> '<strong>Comment</strong> from %1$s in your story:<br>"%2$s"', // 1: Username | 2: Blog title
	'UB_NOTIFICATION_TYPE_RATINGS_MSG'	=> 'Your story has received <strong>%1$s ratings</strong>:<br>"%2$s"', // 1: Rating count | 2: Blog title
	'UB_NOTIFICATION_TYPE_VIEWS_MSG'	=> 'Your story has received <strong>%1$s views</strong>:<br>"%2$s"', // 1: View count | 2: Blog title
]);
