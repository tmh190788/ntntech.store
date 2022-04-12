/* global xts_settings */
(function($) {
	XTSThemeModule.filtersArea = function() {
		var time = 200;

		XTSThemeModule.$body.on('click', '.xts-filters-area-btn', function(e) {
			e.preventDefault();

			if (isOpened()) {
				closeFilters();
			} else {
				XTSThemeModule.openFilters(time);
			}
		});

		if ('no' === xts_settings.shop_filters_area_stop_close) {
			XTSThemeModule.$document.on('pjax:start', function() {
				if (isOpened()) {
					closeFilters();
				}
			});
		}

		var isOpened = function() {
			return $('.xts-filters-area').hasClass('xts-opened');
		};

		var closeFilters = function() {
			$('.xts-filters-area').removeClass('xts-opened').stop().slideUp(time);
		};
	};

	XTSThemeModule.openFilters = function(time) {
		$('.xts-filters-area').stop().slideDown(time);
		XTSThemeModule.$body.removeClass('xts-filters-opened');

		setTimeout(function() {
			$('.xts-filters-area').addClass('xts-opened');
			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		}, time);
	};

	$(document).ready(function() {
		XTSThemeModule.filtersArea();
	});
})(jQuery);
