/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsSingleProductAccordionClick xtsWishlistRemoveSuccess xtsProductTabLoaded xtsElementorProductTabsReady xtsPjaxComplete xtsProductLoadMoreReInit', function() {
		XTSThemeModule.productHoverSummary();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.productHoverSummary();
		});
	});

	XTSThemeModule.productHoverSummary = function() {
		var $summaryHover = $('.xts-prod-design-summary .xts-col, .xts-prod-design-summary-alt .xts-col');
		$summaryHover.on('mouseenter mousemove touchstart', function() {
			var $product = $(this).find('.xts-product');
			var $content = $product.find('.xts-more-desc');

			if ($content.hasClass('xts-height-calculated')) {
				return;
			}

			$product.imagesLoaded(function() {
				productHoverSummaryRecalc($product);
			});

			productHoverSummaryRecalc($product);

			$content.addClass('xts-height-calculated');
		});

		$summaryHover.on('click', '.xts-more-desc-btn', function(e) {
			e.preventDefault();
			productHoverSummaryRecalc($(this).parents('.xts-product'));
		});

		function productHoverSummaryMoreBtn() {
			$('.xts-prod-design-summary .xts-col, .xts-prod-design-summary-alt .xts-col, .xts-prod-design-summary-alt-2 .xts-col').on('mouseenter touchstart', function() {
				var $product = $(this).find('.xts-product');
				var $content = $product.find('.xts-more-desc');
				var $moreBtn = $content.find('.xts-more-desc-btn');
				var $inner = $content.find('.xts-more-desc-inner');

				if ($content.hasClass('xts-more-desc-calculated')) {
					return;
				}

				var contentHeight = $content.outerHeight();
				var innerHeight = $inner.outerHeight();
				var delta = innerHeight - contentHeight;

				if (delta > 10) {
					$moreBtn.addClass('xts-shown');
				} else if (delta > 0) {
					$content.css('height', contentHeight + delta);
				}

				$content.addClass('xts-more-desc-calculated');
			});

			$('.xts-more-desc-btn').on('click', function(e) {
				e.preventDefault();
				$(this).parent().addClass('xts-opened');
			});
		}

		function productHoverSummaryRecalc($product) {
			if ($product.parents('.xts-carousel').length > 0) {
				return;
			}

			var heightHideInfo = $product.find('.xts-product-hide-info').outerHeight();

			$product.find('.xts-product-bg').css({
				marginBottom: -heightHideInfo
			});

			$product.addClass('xts-ready');
		}

		productHoverSummaryMoreBtn();
	};

	$(document).ready(function() {
		XTSThemeModule.productHoverSummary();
	});
})(jQuery);