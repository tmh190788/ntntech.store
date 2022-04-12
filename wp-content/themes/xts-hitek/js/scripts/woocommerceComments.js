/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_single_product_reviews.default', function($wrapper) {
		$wrapper.find('.wc-tabs-wrapper, .woocommerce-tabs').trigger('init');
		$wrapper.find('#rating').parent().find('> .stars').remove();
		$wrapper.find('#rating').trigger('init');
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_single_product_tabs.default', function($wrapper) {
		$wrapper.find('.wc-tabs-wrapper, .woocommerce-tabs').trigger('init');
		$wrapper.find('#rating').parent().find('> .stars').remove();
		$wrapper.find('#rating').trigger('init');
	});

	XTSThemeModule.woocommerceComments = function() {
		var hash = window.location.hash;
		var url = window.location.href;

		if (hash.toLowerCase().indexOf('comment-') >= 0 || hash === '#reviews' || hash === '#tab-reviews' || url.indexOf('comment-page-') > 0 || url.indexOf('cpage=') > 0) {
			setTimeout(function() {
				window.scrollTo(0, 0);
			}, 1);

			setTimeout(function() {
				if ($(hash).length > 0) {
					$('html, body').stop().animate({
						scrollTop: $(hash).offset().top - 100
					}, 400);
				}
			}, 10);
		}
	};

	$(document).ready(function() {
		XTSThemeModule.woocommerceComments();
	});
})(jQuery);
