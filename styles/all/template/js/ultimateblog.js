
(function($) {

'use strict';

function deselect(e) {
	$('.pop').slideFadeToggle(function() {
			e.removeClass('selected');
	});
}

phpbb.addAjaxCallback('mrgoldy_ultimateblog.reload', function() {
	location.reload();
});

phpbb.addAjaxCallback('mrgoldy_ultimateblog.edit_comment', function(data) {
	$('#comments_edit').attr('action', data.form_action);
	$('input[name="author_id"]').attr('value', data.author_id);
	$('input[name="comment_id"]').attr('value', data.comment_id);
	$('#comments_edit > textarea').text(data.comment_text);
	$('.comment-edit-modal').show();
});

$(function() {
	// Delete a reply, submit the form
	$('.comment-delete').on('click', function() {
		this.parent().submit();
	});

	// Hide any excess comments
	$('#comments-list').each(function() {
		if (window.location.hash && $.isNumeric(window.location.hash.substring(1))) {
			var commentId = window.location.hash.substring(1);
			$('#' + commentId).prevAll().show();
			$('#' + commentId).show();
			$('#' + commentId).nextAll().slice(0, -1).hide();
			$(document).scrollTop( $('#' + commentId).offset().top );
		} else {
			$(this).children().slice(0, commentsPerPage).show();
			$(this).children().slice(commentsPerPage, -1).hide();
		}

		if ($(this).children().filter(':hidden').length == 0) {
			$(this).children().slice(-1).hide();
		}
	});

	// Show more comments
	$('.comment-view-more').click(function(e){
		e.preventDefault();
		$('#comments-list').children().filter(':hidden').slice(0, commentsPerPage).show();

		if ($('#comments-list').children().filter(':hidden').length == 0){
			$('#comments-list').children().slice(-1).hide();
		}
	});

	// Show replies to a comment
	$('.show-replies').on('click', function() {
		$('#' + this.id).hide();
		$('#replies_to_' + this.id.split('_')[3]).show(); // #ID: "show_replies_for_" +ID. So 4th string of the split. Index starts at 0
	});

	// Show the add reply field
	$('.show-add-reply').on('click', function() {
		$("[id^='add_reply_to']").hide();
		$('#add_reply_to_' + this.id.split('_')[3]).show();
	})

	// Enable the submit button
	$('.comment-textarea > textarea').on('keyup', function() {
		if ($(this).val() == ''){
			$('.comment-add').attr('disabled', 'disabled');
			$('.comment-add > i').removeClass('comment-add-enabled');
		} else {
			$('.comment-add').removeAttr('disabled');
			$('.comment-add > i').addClass('comment-add-enabled');
		}
	});
	$('.reply-textarea > textarea').on('keyup', function() {
		if ($(this).val() == ''){
			$('.reply-add').attr('disabled', 'disabled');
			$('.reply-add > i').removeClass('comment-add-enabled');
		} else {
			$('.reply-add').removeAttr('disabled');
			$('.reply-add > i').addClass('comment-add-enabled');

		}
	});

	// Open the 'view icons' screen
	$('#view_edits').on('click', function() {
		if($(this).hasClass('selected')) {
			deselect($(this));
		} else {
			$(this).addClass('selected');
			$('.pop').slideFadeToggle();
		}
		return false;
	});

	// Closing the 'view edits' screen
	$('.view-edits-close').on('click', function() {
		deselect($('#ub_fa_icon_choose'));
		return false;
	});

	// Set up blog rating
	$('.blog-rating').each(function () {
		$(this).raty({
			number: 5,
			hints: [null, null, null, null, null],
			starType: 'i',
			starOff: 'fa fa-star-o',
			starOn: 'fa fa-star',
			score: function() {
				return $(this).attr('data-score');
			},
			click: function (score, evt) {
				$.ajax({
					type: 'post',
					url: blogRatingURL,
					data: {
						score: score,
						bid: $('#blog_id').val(),
					},
					dataType: 'json'
				});
				$('.blog-rating').raty('score', score);
				return false;
			}
		});
	});
});

$.fn.slideFadeToggle = function(easing, callback) {
	return this.animate({ opacity: 'toggle', height: 'toggle' }, 'fast', easing, callback);
};

})(jQuery);
