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
	'BLOG_ADDED'					=> 'Your blog (“<strong>%1$s</strong>”) has been successfully added.<br><br><strong><a href="%2$s">« Click here to view your blog</a></strong>',

	'BLOG_CHANGE_AUTHOR'			=> 'Change author',
	'BLOG_COMMENTS_DELETE_CONFIRM'	=> 'Are you sure you wish to delete this comment?',
	'BLOG_COMMENTS_DELETE_REPLIES'	=> 'This will also delete all replies made to this comment.',

	'BLOG_DELETE_CONFIRM'			=> 'You are about to delete “<strong>%s</strong>”.<br>Are you sure you want to delete this blog?<br>This action can <strong>not</strong> be undone!',
	'BLOG_DESCRIPTION'				=> 'Blog description',
	'BLOG_DESCRIPTION_PLACEHOLDER'	=> 'Add a mandatory blog description. It needs to have between ' . constants::BLOG_DESC_MINIMUM . ' and ' . constants::BLOG_DESC_MAXIMUM . ' characters.',

	'BLOG_EDIT_REASON'				=> 'Edit reason',
	'BLOG_EDITED'					=> 'Your blog (“<strong>%1$s</strong>”) has been successfully edited.<br><br><strong><a href="%2$s">« Click here to view your blog</a></strong>',

	'BLOG_FRIENDS_ONLY'				=> 'Only visible for <strong>YOUR</strong> friends',

	'BLOG_IMAGE'					=> 'Blog image',
	'BLOG_IMAGE_EXPLAIN'			=> '<em>(w 350px | h 225px)</em>',

	'BLOG_LOCK_COMMENTS'			=> 'Prevent commenting',
	'BLOG_LOCK_RATING'				=> 'Prevent rating',

	'BLOG_PREVIEW'					=> 'Blog preview',

	'BLOG_TITLE'					=> 'Blog title',
));
