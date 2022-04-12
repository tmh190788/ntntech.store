/* global xts_settings */
(function($) {
	XTSThemeModule.commentImage = function() {
		// This is a dirty method, but there is no hook in WordPress to add attributes to the commenting form.
		$('form.comment-form').attr('enctype', 'multipart/form-data');
	};

	XTSThemeModule.commentImagesUploadValidation = function() {
		var $form = $('.comment-form');
		var $input = $form.find('#xts-add-img-btn');
		var allowedMimes = [];

		if ($input.length === 0) {
			return;
		}

		$.each(xts_settings.comment_images_upload_mimes, function(index, value) {
			allowedMimes.push(String(value));
		});

		$input.on('change', function(e) {
			$form.find('.xts-add-img-count').text(xts_settings.comment_images_added_count_text.replace('%s', this.files.length));
		});

		$form.on('submit', function(e) {
			$form.find('.woocommerce-error').remove();

			var hasLarge = false;
			var hasNotAllowedMime = false;

			if ($input[0].files.length > xts_settings.comment_images_count) {
				showError(xts_settings.comment_images_count_text);
				e.preventDefault();
			}

			Array.prototype.forEach.call($input[0].files, function(file) {
				var size = file.size;
				var type = String(file.type);

				if (size > xts_settings.comment_images_upload_size) {
					hasLarge = true;
				}

				if ($.inArray(type, allowedMimes) < 0) {
					hasNotAllowedMime = true;
				}
			});

			if (hasLarge) {
				showError(xts_settings.comment_images_upload_size_text);
				e.preventDefault();
			}

			if (hasNotAllowedMime) {
				showError(xts_settings.comment_images_upload_mimes_text);
				e.preventDefault();
			}
		});

		function showError(text) {
			$form.append('<div class="comment-form-images-msg"><p class="woocommerce-error" role="alert">' + text + '</p><div>');
		}
	};

	$(document).ready(function() {
		XTSThemeModule.commentImage();
		XTSThemeModule.commentImagesUploadValidation();
	});
})(jQuery);