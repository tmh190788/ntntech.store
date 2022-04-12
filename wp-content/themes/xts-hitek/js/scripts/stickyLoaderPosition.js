/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxStart xtsPortfolioPjaxStart', function() {
		XTSThemeModule.stickyLoaderPosition();
	});

	XTSThemeModule.stickyLoaderPosition = function() {
		var loaderVerticalPosition = function() {
			var $products = $('.xts-products[data-source="main_loop"], .xts-portfolio-loop[data-source="main_loop"]');
			var $loader = $products.parent().find('.xts-sticky-loader');

			if ($products.length < 1) {
				return;
			}

			var offset = XTSThemeModule.$window.height() / 2;
			var scrollTop = XTSThemeModule.$window.scrollTop();
			var holderTop = $products.offset().top - offset + 45;
			var holderHeight = $products.height();
			var holderBottom = holderTop + holderHeight - 100;

			if (scrollTop < holderTop) {
				$loader.addClass('xts-position-top');
				$loader.removeClass('xts-position-stick');
			} else if (scrollTop > holderBottom) {
				$loader.addClass('xts-position-bottom');
				$loader.removeClass('xts-position-stick');
			} else {
				$loader.addClass('xts-position-stick');
				$loader.removeClass('xts-position-top xts-position-bottom');
			}
		};

		XTSThemeModule.$window.off('scroll.loaderVerticalPosition');

		XTSThemeModule.$window.on('scroll.loaderVerticalPosition', loaderVerticalPosition);
	};

	$(document).ready(function() {
		XTSThemeModule.stickyLoaderPosition();
	});
})(jQuery);