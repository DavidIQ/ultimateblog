function deselect(e) {
	$('.pop').slideFadeToggle(function() {
			e.removeClass('selected');
	});
}

$(function() {
	// Drag and drop for Blog Index
	$('.ub-index-used').sortable({
		connectWith: '.ub-index-not-used',
		receive: function(event, ui){
			$('#ub_index_order').val($(this).sortable('toArray', {attribute: 'data-block-id'}));
		}
	}).disableSelection();

	$('.ub-index-not-used').sortable({
		connectWith: '.ub-index-used',
		receive: function (event, ui){
			$('#ub_index_order').val($(ui.sender).sortable('toArray', {attribute: 'data-block-id'}));
		}
	}).disableSelection();

	// Set up switch (on/off) elements
	var elem = document.querySelector('#ub_enable');
	var init = new Switchery(elem, { color: '#64bd63', secondaryColor: '#ff0000', size: 'large'});
	var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
	elems.forEach(function(html) {
		var switchery = new Switchery(html, { color: '#64bd63', secondaryColor: '#ff0000', size: 'medium' });
	});

	// Open the 'choose icon' screen
	$('#ub_fa_icon_choose').on('click', function() {
		if($(this).hasClass('selected')) {
			deselect($(this));
		} else {
			$(this).addClass('selected');
			$('.pop').slideFadeToggle();
		}
		return false;
	});

	// Add the chosen icon to the input field and the preview
	$('.ub-fa-icon-add').on('click', function() {
		deselect($('#ub_fa_icon_choose'));
		$('#ub_fa_icon').val(this.title);
		$('#ub_fa_icon_preview').removeClass();
		$('#ub_fa_icon_preview').addClass('icon ' + this.title + ' fa-fw');
		return false;
	});

	// Closing the 'choose icon' screen
	$('.ub-fa-icon-close').on('click', function() {
		deselect($('#ub_fa_icon_choose'));
		return false;
	});

	// Update the preview text on clicking outside the title input field
	$('#ub_title').on('blur',function () {
		$('#ub_fa_icon_preview_span').text(this.value);
	});
});

$.fn.slideFadeToggle = function(easing, callback) {
	return this.animate({ opacity: 'toggle', height: 'toggle' }, 'fast', easing, callback);
};
