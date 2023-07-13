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
	'BLOG_REPORT_ALREADY_BLOG'		=> 'This blog has already been reported.',
	'BLOG_REPORT_ALREADY_COMMENT'	=> 'This comment has already been reported.',
	'BLOG_REPORT_FORM_INVALID'		=> '<strong>400 Bad Request:</strong> Invalid form.',
	'BLOG_REPORT_INVALID_MODE'		=> 'The requested mode is invalid.',
	'BLOG_REPORT_NO_BLOG'			=> 'There is no blog with the given id.',
	'BLOG_REPORT_NO_COMMENT'		=> 'There is no comment with the given id.',
	'BLOG_REPORT_NO_PERMISSION'		=> 'You do not have the permission to report.',
	'BLOG_REPORT_NOTIFY'			=> 'You will be notified when your report is dealt with.',
	'BLOG_REPORT_RETURN'			=> '« Return to the blog you were viewing',
	'BLOG_REPORT_SUCCESS_BLOG'		=> 'The blog has successfully been reported.',
	'BLOG_REPORT_SUCCESS_COMMENT'	=> 'The comment has successfully been reported.',
));
