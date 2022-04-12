/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.shopToolsFilters();
	});

	XTSThemeModule.shopToolsFilters = function() {
		if (XTSThemeModule.isDesktop) {
			return false;
		}

		$('.xts-shop-tools-widget').each(function() {
			var $widget = $(this);

			$widget.addClass('xts-event-click');

			$widget.find('.xts-tools-widget-title').on('click', function() {
				if ($widget.hasClass('xts-opened')) {
					$widget.removeClass('xts-opened');
				} else {
					$widget.siblings().removeClass('xts-opened');
					$widget.addClass('xts-opened');
				}
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($widget.hasClass('xts-opened') && !$(target).is('.xts-tools-widget-widget') && !$(target).parents().is('.xts-shop-tools-widget')) {
					$widget.removeClass('xts-opened');
					return false;
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.shopToolsFilters();
	});
})(jQuery);