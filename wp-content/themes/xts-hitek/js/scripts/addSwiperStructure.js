/* global xts_settings */
(function($) {
	XTSThemeModule.addSwiperStructure = function($slider, navigation, pagination, controlsId) {
		if (0 === $slider.find('> .swiper-wrapper').length) {
			$slider.wrapInner('<div class="swiper-wrapper"/>');
		}

		if (0 === $slider.find('> .swiper-container').length) {
			$slider.wrapInner('<div class="swiper-container"/>');
		}

		$slider.find('.xts-col').addClass('swiper-slide');

		if (navigation && 0 === $slider.find('> .xts-nav-arrows').length) {
			$slider.find('> .swiper-container').after('<div class="xts-nav-arrows"><div class="xts-nav-arrow xts-prev xts-id-' + controlsId + '"><div class="xts-arrow-inner"></div></div><div class="xts-nav-arrow xts-next xts-id-' + controlsId + '"><div class="xts-arrow-inner"></div></div></div>');
		}

		if (pagination && 0 === $slider.find('> .xts-nav-pagination').length) {
			$slider.find('> .swiper-container').after('<ol class="xts-nav-pagination xts-id-' + controlsId + '"></ol>');
		}
	};
})(jQuery);