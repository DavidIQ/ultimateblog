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
	'UB_VIEWONLINE_ARCHIVE'			=> 'Viewing the %1$s archive for %3$s %2$s', // 1: Ultimate Blog title | 2: Numeric year (eg. 2017) | 3: Textual month (eg. January)
	'UB_VIEWONLINE_ARCHIVES'		=> 'Viewing the %s archives', // 1: Ultimate Blog title
	'UB_VIEWONLINE_CATEGORY'		=> 'Viewing %1$s entries in %2$s', // 1: Ultimate Blog title | 2: Category name
	'UB_VIEWONLINE_CATEGORIES'		=> 'Viewing %s categories', // 1: Ultimate Blog title
	'UB_VIEWONLINE_POSTING'			=> 'Posting a new %s entry', // 1: Ultimate Blog title
	'UB_VIEWONLINE_USER'			=> 'Viewing a user’s %s entries', // 1: Ultimate Blog title
	'UB_VIEWONLINE_BLOG'			=> 'Reading a %s entry', // 1: Ultimate Blog title
	'UB_VIEWONLINE_INDEX'			=> 'Viewing the %s index', // 1: Ultimate Blog title
));
