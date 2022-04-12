/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsProductQuickViewOpen', function() {
		XTSThemeModule.variationsPrice();
	});

	XTSThemeModule.variationsPrice = function () {
		if ('no' === xts_settings.single_product_variations_price) {
			return;
		}

		$('.xts-single-product .variations_form').each(function () {
			var $form = $(this);
			var $price = $form.parent().find('.price').first();

			if ( 0 === $price.length ) {
				$price = $('.elementor-widget-xts_product_price .price');
			}

			var priceOriginalHtml = $price.html();

			$form.on('show_variation', function (e, variation) {
				if (variation.price_html.length > 1) {
					$price.html(variation.price_html);
				}

				$form.addClass('xts-price-outside');
			});

			$form.on('hide_variation', function () {
				$price.html(priceOriginalHtml);
				$form.removeClass('xts-price-outside');
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.variationsPrice();
	});
})(jQuery);