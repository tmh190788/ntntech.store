/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsProductTabLoaded xtsProductLoadMoreReInit xtsPjaxComplete xtsPortfolioLoadMoreSuccess xtsBlogLoadMoreSuccess xtsWishlistRemoveSuccess', function() {
		XTSThemeModule.itemsAnimationInView();
	});

	XTSThemeModule.$document.on('xtsPortfolioPjaxComplete', function() {
		setTimeout(function() {
			XTSThemeModule.itemsAnimationInView();
		}, 100);
	});

	$.each([
		'frontend/element_ready/xts_product_brands.default',
		'frontend/element_ready/xts_product_categories.default',
		'frontend/element_ready/xts_product_tabs.default',
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default',
		'frontend/element_ready/xts_image_gallery.default',
		'frontend/element_ready/xts_banner_carousel.default',
		'frontend/element_ready/xts_infobox_carousel.default',
		'frontend/element_ready/xts_blog.default',
		'frontend/element_ready/xts_portfolio.default',
		'frontend/element_ready/xts_instagram.default',
		'frontend/element_ready/xts_testimonials.default',
		'frontend/element_ready/xts_title.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.itemsAnimationInView();
		});
	});

	XTSThemeModule.itemsAnimationInView = function() {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		$('.xts-in-view-animation').each(function() {
			var itemQueue = [];
			var queueTimer;
			var $wrapper = $(this);

			function processItemQueue(delay) {
				if (queueTimer) {
					return;
				}

				queueTimer = window.setInterval(function() {
					if (itemQueue.length) {
						$(itemQueue.shift()).addClass('xts-animated');
						processItemQueue(delay);
					} else {
						window.clearInterval(queueTimer);
						queueTimer = null;
					}
				}, delay);
			}

			$wrapper.find('.xts-col, .xts-animation-item').each(function() {
				var $element = $(this);

				if ('inited' === $element.data('xts-waypoint')) {
					return;
				}

				$element.data('xts-waypoint', 'inited');

				$element.xtsWaypoint(function() {
					var $this = $($(this)[0].element);
					var delay = $this.parents('.xts-in-view-animation').data('animation-delay');

					$this.addClass('xts-animation-ready');

					itemQueue.push($this);
					processItemQueue(delay);
				}, {
					offset: '90%'
				});
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.itemsAnimationInView();
	});
})(jQuery);