jQuery( document ).ready(function( $ ) {
	var opts = {
		lines: 13, // The number of lines to draw
		length: 3, // The length of each line
		width: 2, // The line thickness
		radius: 4, // The radius of the inner circle
		corners: 1, // Corner roundness (0..1)
		rotate: 0, // The rotation offset
		direction: 1, // 1: clockwise, -1: counterclockwise
		color: '#000', // #rgb or #rrggbb or array of colors
		speed: 0.7, // Rounds per second
		trail: 50, // Afterglow percentage
		shadow: false, // Whether to render a shadow
		hwaccel: false, // Whether to use hardware acceleration
		className: 'spinner', // The CSS class to assign to the spinner
		zIndex: 2e9, // The z-index (defaults to 2000000000)
		top: '10px', // Top position relative to parent
		left: '10px' // Left position relative to parent
	};
	var spinnerWrapper = $('<div />');
	spinnerWrapper.css({
		width: '30px',
		height: '30px',
		position: 'relative'
	});
	var spinner = new Spinner(opts).spin();
	spinnerWrapper.append(spinner.el);
	
	$('.globalcontent-ajax-autoload').each(function() {
		var elm = $(this),
			url = elm.attr('href');
		
		spinnerWrapper.clone().insertBefore(elm);
		$.ajax({
			url: url,
			dataType: 'html',
			cache: false
		})
		.done(function(data, textStatus, jqXHR) {
			elm.prev().remove();
			if (textStatus == 'success') {
				if (data.search('<!-- BE_USER Edit Panel: -->') > 0) {
					data = data.substring(0, data.search('<!-- BE_USER Edit Panel: -->'));
				}
				
				data = $(data);
				data.find('.frontEndEditIconLinks').remove();
				data.hide();
				
				elm.replaceWith(data);
				data.animate({
					opacity: 'toggle',
					height: 'toggle'
				}, 'slow');
			} else {
				elm.remove();
			}
		});
	} );
});
