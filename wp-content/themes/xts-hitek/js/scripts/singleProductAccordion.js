/* global xts_settings */
(function($) {
	$.each([
		'frontend/element_ready/xts_accordion.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.singleProductAccordion();
		});
	});

	XTSThemeModule.singleProductAccordion = function() {
		var $accordion = $('.wc-tabs-wrapper.xts-accordion');
		var hash = window.location.hash;
		var url = window.location.href;

		if (hash.toLowerCase().indexOf('comment-') >= 0 || hash === '#reviews' || hash === '#tab-reviews') {
			$accordion.find('[data-accordion-index="reviews"]').click();
		} else if (url.indexOf('comment-page-') > 0 || url.indexOf('cpage=') > 0) {
			$accordion.find('[data-accordion-index="reviews"]').click();
		}

		XTSThemeModule.$body.on('click', '.wc-tabs li a, ul.tabs li a', function(e) {
			e.preventDefault();
			var index = $(this).data('tab-index');
			$accordion.find('[data-accordion-index="' + index + '"]').click();
			XTSThemeModule.$document.trigger('xtsSingleProductAccordionClick');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductAccordion();
	});
})(jQuery);