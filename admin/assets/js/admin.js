(function ( $ ) {
	"use strict";

	$(function () {
		$('#wpc2-download-media a').click( function( event ) {
			event.preventDefault();
			var href = $(this).attr('href');
			if ( 'string' == typeof( href ) ) {
				window.location = href;
			}
		});
	});

}(jQuery));
