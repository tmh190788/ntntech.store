/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorProductTabsReady', function() {
		XTSThemeModule.gridSwatches();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.gridSwatches();
		});
	});

	XTSThemeModule.gridSwatches = function() {
		XTSThemeModule.$body.on('click', '.xts-loop-swatch', function() {
			var src, srcset, image_sizes;

			var $this = $(this);
			var imageSrc = $this.data('image-src');
			var imageSrcset = $this.data('image-srcset');
			var imageSizes = $this.data('image-sizes');

			if (typeof imageSrc == 'undefined' || '' === imageSrc) {
				return;
			}

			var $product = $this.parents('.xts-product');
			var $image = $product.find('.xts-product-image img');
			var srcOrig = $image.attr('original-src');
			var srcsetOrig = $image.attr('original-srcset');
			var sizesOrig = $image.attr('original-sizes');

			if (typeof srcOrig == 'undefined') {
				$image.attr('original-src', $image.attr('src'));
			}

			if (typeof srcsetOrig == 'undefined') {
				$image.attr('original-srcset', $image.attr('srcset'));
			}

			if (typeof sizesOrig == 'undefined') {
				$image.attr('original-sizes', $image.attr('sizes'));
			}

			if ($this.hasClass('xts-active')) {
				src = srcOrig;
				srcset = srcsetOrig;
				image_sizes = sizesOrig;

				$this.removeClass('xts-active');
				$product.removeClass('xts-product-swatched');
			} else {
				src = imageSrc;
				srcset = imageSrcset;
				image_sizes = imageSizes;

				$this.parent().find('.xts-active').removeClass('xts-active');
				$this.addClass('xts-active');
				$product.addClass('xts-product-swatched');
			}

			$product.addClass('xts-loading');

			$image.attr('src', src).attr('srcset', srcset).attr('image_sizes', image_sizes).one('load', function() {
				$product.removeClass('xts-loading');
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.gridSwatches();
	});
})(jQuery);