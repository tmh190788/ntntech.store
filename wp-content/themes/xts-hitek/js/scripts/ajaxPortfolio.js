/* global xts_settings */
(function($) {
	XTSThemeModule.ajaxPortfolio = function() {
		if ('no' === xts_settings.ajax_portfolio || 'undefined' === typeof ($.fn.pjax)) {
			return;
		}

		var ajaxLinks = '.xts-type-links .xts-nav-portfolio a, .tax-xts-portfolio-cat .xts-breadcrumbs a, .post-type-archive-xts-portfolio .xts-breadcrumbs a,.tax-xts-portfolio-cat .xts-pagination a, .post-type-archive-xts-portfolio .xts-pagination a';

		XTSThemeModule.$body.on('click', '.tax-xts-portfolio-cat .xts-pagination a, .post-type-archive-xts-portfolio .xts-pagination a', function() {
			scrollToTop(true);
		});

		XTSThemeModule.$document.pjax(ajaxLinks, '.xts-site-content', {
			timeout : xts_settings.pjax_timeout,
			scrollTo: false
		});

		XTSThemeModule.$document.on('pjax:start', function() {
			$('.xts-ajax-content').removeClass('xts-loaded').addClass('xts-loading');
			XTSThemeModule.$document.trigger('xtsPortfolioPjaxStart');
			XTSThemeModule.$window.trigger('scroll.loaderVerticalPosition');
		});

		XTSThemeModule.$document.on('pjax:end', function() {
			$('.xts-ajax-content').addClass('xts-loaded');
		});

		XTSThemeModule.$document.on('pjax:complete', function() {
			if (!XTSThemeModule.$body.hasClass('tax-xts-portfolio-cat') && !XTSThemeModule.$body.hasClass('post-type-archive-xts-portfolio')) {
				return;
			}

			XTSThemeModule.$document.trigger('xtsPortfolioPjaxComplete');
			XTSThemeModule.$document.trigger('xtsImagesLoaded');

			scrollToTop(false);

			$('.xts-ajax-content').removeClass('xts-loading');
		});

		var scrollToTop = function(type) {
			if ('no' === xts_settings.ajax_shop_scroll && type === false) {
				return;
			}

			var $scrollTo = $(xts_settings.ajax_shop_scroll_class);
			var scrollTo = $scrollTo.offset().top - xts_settings.ajax_shop_scroll_offset;

			$('html, body').stop().animate({
				scrollTop: scrollTo
			}, 400);
		};
	};

	$(document).ready(function() {
		XTSThemeModule.ajaxPortfolio();
	});
})(jQuery);
