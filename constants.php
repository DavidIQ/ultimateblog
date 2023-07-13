<?php
/**
 *
 * Ultimate Blog. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Mr. Goldy
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace mrgoldy\ultimateblog;

class constants
{
	# Threshold for when to notify a user, every 100th view/comment/rating
	const NOTIFY_COMMENTS_THRESHOLD = 100;
	const NOTIFY_RATINGS_THRESHOLD = 100;
	const NOTIFY_VIEWS_THRESHOLD = 100;

	# Maximum amount of blogs per Custom Index block.
	const BLOCK_MAX_LIMIT = 16;

	# Limit for blog and category descriptions, ideal for search engines.
	const BLOG_DESC_MINIMUM = 50;
	const BLOG_DESC_MAXIMUM = 125;

	# Maximum characters for edit reasons
	const BLOG_EDIT_REASON = 100;
}
