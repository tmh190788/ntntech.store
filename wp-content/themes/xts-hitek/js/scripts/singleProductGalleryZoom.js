/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSingleProductGalleryReady xtsImagesLoaded xtsProductQuickViewOpen xtsElementorSingleProductGallerySwiperInited', function() {
		XTSThemeModule.singleProductGalleryZoom();
	});

	XTSThemeModule.singleProductGalleryZoom = function() {
		var $galleryWrapper = $('.woocommerce-product-gallery');
		var $mainGallery = $('.xts-single-product-images');
		var zoomOptions = {
			touch: false
		};

		if ('ontouchstart' in window) {
			zoomOptions.on = 'click';
		}

		if (!$mainGallery.hasClass('xts-action-zoom')) {
			return;
		}

		if (($galleryWrapper.hasClass('xts-style-bottom') || $galleryWrapper.hasClass('xts-style-side')) && $mainGallery.hasClass('xts-loaded')) {
			var swiper = $mainGallery.find('.swiper-container')[0].swiper;

			init($mainGallery.find('.xts-col').eq(0).find('.xts-col-inner'));

			swiper.on('slideChange', function() {
				var $wrapper = $mainGallery.find('.xts-col').eq(swiper.activeIndex).find('.xts-col-inner');

				init($wrapper);
			});
		} else {
			$mainGallery.find('.xts-col').each(function() {
				var $wrapper = $(this).find('.xts-col-inner');

				init($wrapper);
			});
		}

		function init($wrapper) {
			var image = $wrapper.find('img');

			if (image.data('large_image_width') > $wrapper.width()) {
				$wrapper.trigger('zoom.destroy');
				$wrapper.zoom(zoomOptions);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductGalleryZoom();
	});
})(jQuery);