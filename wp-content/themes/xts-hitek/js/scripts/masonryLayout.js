/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete xtsPortfolioPjaxComplete xtsElementorProductTabsReady xtsPortfolioPjaxComplete', function() {
		XTSThemeModule.masonryLayout();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default',
		'frontend/element_ready/xts_image_gallery.default',
		'frontend/element_ready/xts_blog.default',
		'frontend/element_ready/xts_portfolio.default',
		'frontend/element_ready/xts_instagram.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.masonryLayout();
		});
	});

	XTSThemeModule.masonryLayout = function() {
		$('.xts-masonry-layout:not(.xts-carousel)').each(function() {
			var $this = $(this);
			var columnWidth = $this.hasClass('xts-different-images') || $this.hasClass('xts-different-sizes') ? '.xts-col:not(.xts-wide):not(.swiper-slide)' : '.xts-col:not(.swiper-slide)';
			$this.imagesLoaded(function() {
				var config = {
					resizable   : false,
					isOriginLeft: !XTSThemeModule.$body.hasClass('rtl'),
					layoutMode  : 'packery',
					packery     : {
						gutter     : 0,
						columnWidth: columnWidth
					},
					itemSelector: '.xts-col:not(.xts-post-gallery-col)'
				};

				if ($this.hasClass('xts-in-view-animation')) {
					config.transitionDuration = 0;
				}

				$this.isotope(config);
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.masonryLayout();
	});
})(jQuery);