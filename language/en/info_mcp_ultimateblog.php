<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'MCP_UB_BLOG_REPORTS_OPEN'				=> 'Open blog reports',
	'MCP_UB_BLOG_REPORTS_OPEN_EXPLAIN'		=> 'This is a list of all reported blogs which are still to be handled.',
	'MCP_UB_BLOG_REPORTS_CLOSED'			=> 'Closed blog reports',
	'MCP_UB_BLOG_REPORTS_CLOSED_EXPLAIN'	=> 'This is a list of all reports about blogs which have previously been resolved.',
	'MCP_UB_BLOG_REPORTS_DETAILS'			=> 'Blog report details',
	'MCP_UB_BLOG_REPORTS_LATEST'			=> 'Latest 5 blog reports',
	'MCP_UB_BLOG_REPORTS_TOTAL'				=> array(
		0 => 'There are no blog reports to review.',
		1 => 'There is %s blog report to review.',
		2 => 'There are %s blog reports to review.',
	),

	'MCP_UB_COMMENT_REPORTED'				=> 'This comment has been reported.',
	'MCP_UB_COMMENT_REPORTS_OPEN'			=> 'Open comment reports',
	'MCP_UB_COMMENT_REPORTS_OPEN_EXPLAIN'	=> 'This is a list of all reported comments which are still to be handled.',
	'MCP_UB_COMMENT_REPORTS_CLOSED'			=> 'Closed comment reports',
	'MCP_UB_COMMENT_REPORTS_CLOSED_EXPLAIN'	=> 'This is a list of all reports about comments which have previously been resolved.',
	'MCP_UB_COMMENT_REPORTS_DETAILS'		=> 'Comment report details',
	'MCP_UB_COMMENT_REPORTS_LATEST'			=> 'Latest 5 comment reports',
	'MCP_UB_COMMENT_TO'						=> 'Comment to',
	'MCP_UB_COMMENT_REPORTS_TOTAL'			=> array(
		0	=> 'There are no comment reports to review.',
		1	=> 'There is %s comment report to review.',
		2	=> 'There are %s comment reports to review.',
	),

	'MCP_UB_REPORTS_CLOSE_CONFIRM'		=> array(
		1 => 'Are you sure you wish to close the selected report?',
		2 => 'Are you sure you wish to close the selected reports?',
	),
	'MCP_UB_REPORTS_DELETE_CONFIRM'		=> array(
		1 => 'Are you sure you wish to delete the selected report?<br>This will permantenly remove the report form the database.<br><br><strong>This action cannot be undone!</strong>',
		2 => 'Are you sure you wish to delete the selected reports?<br>This will permanently remove the reports from the database.<br><br><strong>This action cannot be undone!</strong>',
	),
	'MCP_UB_REPORTS_CLOSED_SUCCESS'		=> array(
		1 => 'The selected report has been closed successfully.',
		2 => 'The selected reports have been closed successfully.',
	),
	'MCP_UB_REPORTS_DELETED_SUCCESS'		=> array(
		1 => 'The select report has been deleted successfully.',
		2 => 'The selected reports have been deleted successfully.',
	),
	'MCP_UB_REPORTS_IDS_EMPTY'			=> 'You have not selected any reports to delete or close.',

	'MCP_UB_REVIEW_BLOG'			=> 'Blog review',
	'MCP_UB_REVIEW_COMMENT'			=> 'Comment review',

	'MCP_UB_VIEW_BLOG'				=> 'View blog',
	'MCP_UB_VIEW_COMMENT'			=> 'View comment',
	'MCP_UB_VIEW_ULTIMATEBLOG'		=> 'View %s',
));
