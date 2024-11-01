	jQuery(function($) {

		"use strict";

		/* open image upload via URL */
		$(".img_add_url").click(function(event) {
			event.preventDefault();

			$("p.upload-image").hide();
			$("p.upload-url").slideDown();
			$("p.upload-url input").focus();
		});

		/* open image upload via local */
		$(".img_add_file").click(function(event) {
			event.preventDefault();

			$("p.upload-url").hide();
			$("p.upload-image").slideDown();
		});

		/* process image upload from URL */
		$(".img_add").on('click', function(event) {
			event.preventDefault();

			var images_limit = $('.upload-images').attr('data-limit');

			if ($('#img_url').val() === '') {
				$('p.upload-result').fadeIn(500).html('<span class="alert alert-danger">' + ptajax.noImageURLError + '</span>');
				return;
			}

			if( $('.upload-images-lib li img').length > ( images_limit -1 ) ) {
				$('p.upload-result').fadeIn(500).html('<span class="alert alert-warning">' + ptajax.ImageLimiteURLError + '</span>');
				return;
			}

			$('.upload-images').append('<div class="loading"></div>');
			$('.wfs_post_submit, p.upload-result').hide(); // hide submit button + results while working

			var id = $("#img_url").val();
			var nonce = $("#wfs_post_upload_url_nonce").val();
			$.ajax({
				url: ptajax.ajaxurl,
				type: 'POST',
				data: {
					id: id,
					action: 'wfs_upload_image_from_url',
					nonce: nonce
				},
				dataType: 'json',
				success: function(data) {
					$('.loading').remove();
					$('.wfs_post_submit').show();
					$('#img_url').val("");
					if (data.status) {
						$('.upload-images-lib').prepend(data.message);
						$('p.upload-result').fadeIn(500).html('<span class="alert alert-success">' + ptajax.ImageSuccess + '</span>');
						// $.scrollTo('body,html', 800);
					} else {
						$('p.upload-result').fadeIn(500).html('<span class="alert alert-warning">' + data.message + '</span>');
						// $.scrollTo('body,html', 800);
					}
				}
			});
		});



		/* process image upload from local */
		$(".img_add_upload").on('click', function(event) {
			event.preventDefault();

			var images_limit = $('.upload-images').attr('data-limit');

			if ($('#wfs_post_files').val() === '') {
				$('p.upload-result').fadeIn(500).html('<span class="alert alert-danger">' + ptajax.noImageUploadError + '</span>');
				return;
			}

			if( $('.upload-images-lib li img').length > ( images_limit -1 ) ) {
				$('p.upload-result').fadeIn(500).html('<span class="alert alert-warning">' + ptajax.ImageLimiteURLError + '</span>');
				return;
			}

			$('.upload-images').append('<div class="loading"></div>');
			$('.wfs_post_submit, p.upload-result').hide(); // hide submit button + results while working

			var nonce = $("#wfs_post_upload_local_nonce").val();

			var formdata = false;
			if (window.FormData) {
				formdata = new FormData();
			}

			var files_data = $('#wfs_post_files');

			$.each($(files_data), function(i, obj) {
				$.each(obj.files, function(j, file) {
					formdata.append('files[' + j + ']', file);
				})
			});
			// our AJAX identifier
			formdata.append('action', 'wfs_upload_image_from_local');

			formdata.append('nonce', nonce);

			$.ajax({
				url: ptajax.ajaxurl,
				type: 'POST',
				data: formdata,
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function(data) {
					$('.loading').remove();
					$('.wfs_post_submit').show();
					$('#img_url').val("");
					if (data.status) {
						$('#wfs_post_files').val("");
						$('.upload-images-lib').prepend(data.message);
						$('p.upload-result').fadeIn(500).html('<span class="alert alert-success">' + ptajax.ImageSuccess + '</span>');
						// $.scrollTo('body,html', 800);
					} else {
						$('p.upload-result').fadeIn(500).html('<span class="alert alert-warning">' + data.message + '</span>');
						// $.scrollTo('body,html', 800);
					}
				}
			});

		});

		/* mark featured image */
		$(document).on('click', '.upload-images-lib li img', function(e){
			e.preventDefault();
			$('.upload-images-lib li').removeClass('featured');
			$(this).parent().addClass('featured');
			$('.boxmark').hide();
			$(this).parent().find('.boxmark').show();
		});

		/* process image removal */
		$(document).on('click', 'a.remove_image', function(event) {
			event.preventDefault();

			$('.wfs_post_submit').append('<div class="loading"></div>');
			$('p.upload-result').hide(); // hide results while working
			$(this).parent().addClass('deleting').fadeOut();

			var id = $(this).attr('data-id').toString();

			$.ajax({
				url: ptajax.ajaxurl,
				type: 'POST',
				data: {
					action: 'wfs_remove_uploaded_image',
					id: id
				},
				dataType: 'json',
				success: function(data) {
					$('.loading').remove();
					if (data.status) {
						$('.deleting').remove();
						$('p.upload-result').fadeIn(500).html('<span class="alert alert-success">' + data.message + '</span>');
						// $.scrollTo('body,html', 800);
					} else {
						$('.upload-images-lib li').removeClass('deleting').show();
						$('p.upload-result').fadeIn(500).html('<span class="alert alert-warning">' + data.message + '</span>');
						// $.scrollTo('body,html', 800);
					}
				}
			});

		});

		/****************
		 **  SUBMIT POST
		 *****************/

		if ($('.wfsposts_form').length) {

			// $('input[name="blt_make_featured_image"]').prop('checked', true);

			$('.wfs_post_submit').on('click', function(e) {

				event.preventDefault();

				var images_limit = $('.upload-images').attr('data-limit');

				if ( $('#wfs_userName').length && ( $('#wfs_userName').val() == '' ) ) { // // Validate name
					$('p.upload-result').fadeIn(500).html('<span class="alert alert-danger">' + ptajax.UserNameError + '</span>');
					return;
				}

				if ( ( $('#wfs_userEmail').length ) && ! validateEmail( $('#wfs_userEmail').val() ) ) { // Validate email
					$('p.upload-result').fadeIn(500).html('<span class="alert alert-danger">' + ptajax.UserEmailError + '</span>');
					return;
				}

				if ($('#wfs_post_title').val() == '') { // Validate title
					$('p.upload-result').fadeIn(500).html('<span class="alert alert-danger">' + ptajax.noTitleError + '</span>');
					return;
				}

				if ( ! $('.upload-images-lib li img').length ) { // Validate images
					$('p.upload-result').fadeIn(500).html('<span class="alert alert-danger">' + ptajax.noImageError + '</span>');
					return;
				} else if( $('.upload-images-lib li img').length > images_limit ) {
					$('p.upload-result').fadeIn(500).html('<span class="alert alert-danger">' + ptajax.ImageLimiteURLError + '</span>');
					return;
				} 

				$('.wfs_post_submit:submit, p.upload-result').hide(); // hide submit button + results while working
				$(".upload-input input").css({
					'border': '1px solid #ccc'
				});
				$('#wfsposts_form form').append('<div class="loading"></div>');
				// prepare data prior to ajax
				var wfs_userID = $('#wfs_userID').val();
				var wfs_userName = $('#wfs_userName').val();
				var wfs_userEmail = $('#wfs_userEmail').val();
				var wfs_post_title = $('#wfs_post_title').val();
				var wfs_post_category = $('#wfs_post_category').val();
				var wfs_post_tags = $('#wfs_post_tags').val();
				var wfs_source = $('#wfs_post_source').val();

				var wfs_post_content = $("#wfs_post_content").val(); // In Text mode
				if ( $("#wp-wfs_post_content-wrap").hasClass("tmce-active") ) {
			        wfs_post_content = tinyMCE.activeEditor.getContent(); // In Visual mode
			    }

				var imgs = $(".upload-images-lib li").map(function() {
					return this.id.split("-")[1];
				}).get().join(",");
				var img_featured = $(".upload-images-lib li.featured").map(function () {
					return this.id.split("-")[1];
				}).get().join(",");
				var nonce = $("#wfs_post_upload_form_nonce").val();

				// end here, do ajax request
				$.ajax({
					url: ptajax.ajaxurl,
					type: 'POST',
					data: {
						action: 'wfs_upload_post',
						userID: wfs_userID,
						userName: wfs_userName,
						userEmail: wfs_userEmail,
						wfs_post_title: wfs_post_title,
						wfs_post_category: wfs_post_category,
						wfs_post_tags: wfs_post_tags,
						wfs_source: wfs_source,
						wfs_post_content: wfs_post_content,
						imgs: imgs,
						img_featured: img_featured,
						nonce: nonce
					},
					dataType: 'json',
					success: function(data) {
						$('.loading').remove();
						$('.wfs_post_submit:submit').show();
						if (data.error) {
							$('p.upload-result').fadeIn(500).html('<span class="alert alert-warning">' + data.error + '</span>');
							// $.scrollTo('body,html', 800);
						} else {

							$("form#wfsposts_form").remove();

							$('p.upload-result').fadeIn(500).html('<span class="alert alert-success">' + data.success + '</span>');
							// $.scrollTo('body,html', 800);
						}
					}
				});
				return false;
			});
		}

		function validateEmail(email) { 
		 // http://stackoverflow.com/a/46181/11236		  
		    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		    return re.test(email);
		}

	});