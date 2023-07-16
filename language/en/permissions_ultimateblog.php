<?php
/**
 *
 * Ultimate Library. An extension for the phpBB Forum Software package.
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
	'ACL_CAT_ULTIMATEBLOG'	=> 'Ultimate Library',

	'ACL_U_UB_VIEW'					=> 'Can view stories',
	'ACL_U_UB_POST'					=> 'Can post stories',
	'ACL_U_UB_POST_PRIVATE'			=> 'Can post stories in private categories',
	'ACL_U_UB_EDIT'					=> 'Can edit own stories',
	'ACL_U_UB_EDIT_VIEW'			=> 'Can view edit reasons',
	'ACL_U_UB_DELETE'				=> 'Can delete own stories',
	'ACL_U_UB_NOAPPROVE'			=> 'Can post stories without approval',
	'ACL_U_UB_COMMENT_DELETE'		=> 'Can delete own story comments',
	'ACL_U_UB_COMMENT_EDIT'			=> 'Can edit own story comments',
	'ACL_U_UB_COMMENT_NOAPPROVE'	=> 'Can post story comments without approval',
	'ACL_U_UB_COMMENT_POST'			=> 'Can post story comments',
	'ACL_U_UB_COMMENT_VIEW'			=> 'Can view story comments',
	'ACL_U_UB_RATE'					=> 'Can rate story',
	'ACL_U_UB_REPORT'				=> 'Can report stories and comments',
	'ACL_U_UB_FEED_VIEW'				=> 'Can view feed',

	'ACL_M_UB_EDIT'			=> 'Can edit stories and comments',
	'ACL_M_UB_DELETE'		=> 'Can delete stories and comments',
	'ACL_M_UB_APPROVE'		=> 'Can approve stories and comments',
	'ACL_M_UB_CHANGEAUTHOR'	=> 'Can change story author',
	'ACL_M_UB_EDIT_LOCK'	=> 'Can lock story editing',
	'ACL_M_UB_EDIT_DELETE'	=> 'Can delete edit reasons',
	'ACL_M_UB_LOCK_COMMENTS'	=> 'Can lock story comments',
	'ACL_M_UB_LOCK_RATING'		=> 'Can lock story rating',
	'ACL_M_UB_REPORT'			=> 'Can manage story and comment reports',
	'ACL_M_UB_VIEW_FRIENDS_ONLY'	=> 'Can view “<em>friends only</em>” stories',

	'ACL_A_UB_OVERVIEW'		=> 'Can view Ultimate Library statistics',
	'ACL_A_UB_SETTINGS'		=> 'Can edit Ultimate Library settings',
	'ACL_A_UB_CATEGORIES'	=> 'Can edit Ultimate Library categories',
));
