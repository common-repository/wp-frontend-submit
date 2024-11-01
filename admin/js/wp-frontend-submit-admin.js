(function( $ ) {
	'use strict';

	$( "#customize-control-wfs_images_limit" ).append('<p class="error"></p>');
	$( "#customize-control-wfs_images_limit input" ).on('input', function() {
	  	var limit = $( "#customize-control-wfs_images_limit input" ).val();
	  	if( ! $.isNumeric( limit ) ) {
	  		 $('.error').fadeIn(1000).html(limit+' is not a number');
	  	} else {
      	$('.error').hide();
      }
	});

})( jQuery );
