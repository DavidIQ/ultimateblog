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
	'ACL_CAT_ULTIMATEBLOG'	=> 'Ultimate Blog',

	'ACL_U_UB_VIEW'					=> 'Can view blogs',
	'ACL_U_UB_POST'					=> 'Can post blogs',
	'ACL_U_UB_POST_PRIVATE'			=> 'Can post blogs in private categories',
	'ACL_U_UB_EDIT'					=> 'Can edit own blogs',
	'ACL_U_UB_EDIT_VIEW'			=> 'Can view edit reasons',
	'ACL_U_UB_DELETE'				=> 'Can delete own blogs',
	'ACL_U_UB_NOAPPROVE'			=> 'Can post blogs without approval',
	'ACL_U_UB_COMMENT_DELETE'		=> 'Can delete own blog comments',
	'ACL_U_UB_COMMENT_EDIT'			=> 'Can edit own blog comments',
	'ACL_U_UB_COMMENT_NOAPPROVE'	=> 'Can post blog comments without approval',
	'ACL_U_UB_COMMENT_POST'			=> 'Can post blog comments',
	'ACL_U_UB_COMMENT_VIEW'			=> 'Can view blog comments',
	'ACL_U_UB_RATE'					=> 'Can rate blogs',
	'ACL_U_UB_REPORT'				=> 'Can report blogs and comments',
	'ACL_U_UB_FEED_VIEW'				=> 'Can view feed',

	'ACL_M_UB_EDIT'			=> 'Can edit blogs and comments',
	'ACL_M_UB_DELETE'		=> 'Can delete blogs and comments',
	'ACL_M_UB_APPROVE'		=> 'Can approve blogs and comments',
	'ACL_M_UB_CHANGEAUTHOR'	=> 'Can change blog author',
	'ACL_M_UB_EDIT_LOCK'	=> 'Can lock blog editing',
	'ACL_M_UB_EDIT_DELETE'	=> 'Can delete edit reasons',
	'ACL_M_UB_LOCK_COMMENTS'	=> 'Can lock blog comments',
	'ACL_M_UB_LOCK_RATING'		=> 'Can lock blog rating',
	'ACL_M_UB_REPORT'			=> 'Can manage blog and comment reports',
	'ACL_M_UB_VIEW_FRIENDS_ONLY'	=> 'Can view “<em>friends only</em>” blogs',

	'ACL_A_UB_OVERVIEW'		=> 'Can view Ultimate Blog statistics',
	'ACL_A_UB_SETTINGS'		=> 'Can edit Ultimate Blog settings',
	'ACL_A_UB_CATEGORIES'	=> 'Can edit Ultimate Blog categories',
));
