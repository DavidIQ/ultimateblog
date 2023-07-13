(function($) {

'use strict';

$(function() {
	//
	$('.blog-menu-toggle').click(function() {
		$('.blog-menu').slideToggle('slow');
		$('.blog-menu-toggle > i').toggleClass('icon-hidden');
	});
});

})(jQuery);
