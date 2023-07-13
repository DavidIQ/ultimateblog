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
	'BLOGS_TOTAL'						=> 'Total blogs',
	'BLOGS_TOTAL_SEARCH'				=> 'Search user’s blogs',

	'UB_NOTIFICATION_GROUP'				=> 'Blog notifications',
	'UB_NOTIFICATION_TYPE_COMMENTS'		=> 'Your blog has received <em>(a multiple of)</em> ' . constants::NOTIFY_COMMENTS_THRESHOLD . ' comments',
	'UB_NOTIFICATION_TYPE_RATING'		=> 'Your blog has received <em>(a multiple of)</em> ' . constants::NOTIFY_RATINGS_THRESHOLD . ' ratings',
	'UB_NOTIFICATION_TYPE_VIEWS'		=> 'Your blog has received <em>(a multiple of)</em> ' . constants::NOTIFY_VIEWS_THRESHOLD . ' views',
));
