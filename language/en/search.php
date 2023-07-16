<?php
/**
 *
 * Ultimate Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2024, DavidIQ
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang,
[
    'SEARCH_CATEGORIES'         => 'Search in categories',
    'SEARCH_CATEGORIES_EXPLAIN' => 'Select the categories you wish to search in.',
    'SEARCH_BLOG_TITLE_CNT'     => 'Story titles and content text',
    'SEARCH_BLOG_CNT_ONLY'      => 'Story content text only',
    'SEARCH_BLOG_TITLE_ONLY'    => 'Story titles only',
    'SEARCH_BLOG_COMMENTS'      => 'story comments',
    'RETURN_FIRST_BLOG_EXPLAIN' => 'Set to 0 to display the entire story.',
    'BLOG_CHARACTERS'           => 'characters of story',
    'BLOG_TIME'                 => 'Story time',
    'BLOG_TITLE'				=> 'Story title',

    'SEARCH_BUTTON'             => 'Search in %s',
]);