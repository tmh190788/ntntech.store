/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSingleProductGalleryReady', function() {
		XTSThemeModule.singleProductGalleryPhotoSwipe();
	});

	XTSThemeModule.singleProductGalleryPhotoSwipe = function() {
		var trigger = '.xts-photoswipe-btn';
		var $mainGallery = $('.xts-single-product-images');

		if ($mainGallery.hasClass('xts-action-photoswipe')) {
			trigger += ', a:not(.xts-video-btn-link)';
		}

		$mainGallery.on('click', 'a', function(e) {
			e.preventDefault();
		});

		$mainGallery.parent().on('click', trigger, function(e) {
			e.preventDefault();

			var index = getCurrentGalleryIndex(e);
			var items = getProductImages($mainGallery.find('.xts-col'));

			XTSThemeModule.callPhotoSwipe({
				index: index,
				items: items,
				galleryItems: $mainGallery,
				parents: '.xts-col',
				global: false,
			});
		});

		var getCurrentGalleryIndex = function(e) {
			if ($mainGallery.hasClass('xts-carousel')) {
				return $mainGallery.find('.xts-col.swiper-slide-active').index();
			} else {
				return $(e.currentTarget).parent().parent().index();
			}
		};

		var getProductImages = function($gallery) {
			var items = [];

			$gallery.each(function() {
				var $image = $(this).find('a > img');

				items.push({
					src: $image.parent().attr('href'),
					w: $image.data('large_image_width'),
					h: $image.data('large_image_height'),
					title: 'yes' === xts_settings.single_product_main_gallery_images_captions
						? $image.data('caption')
						: false,
				});
			});

			return items;
		};
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductGalleryPhotoSwipe();
	});
})(jQuery);