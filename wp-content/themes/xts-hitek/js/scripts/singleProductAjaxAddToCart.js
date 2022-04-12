/* global xts_settings */
(function($) {
	XTSThemeModule.singleProductAjaxAddToCart = function() {
		if ('no' === xts_settings.single_product_ajax_add_to_cart) {
			return;
		}

		XTSThemeModule.$body.on('submit', 'form.cart', function(e) {
			e.preventDefault();
			var $form = $(this);

			var $productWrapper = $form.parents('.product');

			if ($productWrapper.hasClass('product-type-external') || $productWrapper.hasClass('product-type-zakeke')) {
				return;
			}

			var $button = $form.find('.single_add_to_cart_button');
			var data = $form.serialize();

			data += '&action=xts_single_product_ajax_add_to_cart';

			if ($button.val()) {
				data += '&add-to-cart=' + $button.val();
			}

			$button.removeClass('added xts-not-added').addClass('loading');

			// Trigger event
			$(document.body).trigger('adding_to_cart', [
				$button,
				data
			]);

			$.ajax({
				url    : xts_settings.ajaxurl,
				data   : data,
				method : 'POST',
				success: function(response) {
					if (!response) {
						return;
					}

					if (response.error && response.product_url) {
						window.location = response.product_url;
						return;
					}

					// Redirect to cart option
					if ('yes' === xts_settings.cart_redirect_after_add) {
						window.location = xts_settings.cart_url;
					} else {
						$button.removeClass('loading');

						var fragments = response.fragments;
						var cart_hash = response.cart_hash;

						// Block fragments class
						if (fragments) {
							$.each(fragments, function(key) {
								$(key).addClass('xts-updating');
							});
						}

						// Replace fragments
						if (fragments) {
							$.each(fragments, function(key, value) {
								$(key).replaceWith(value);
							});
						}

						// Show notices
						if (response.notices.indexOf('error') > 0) {
							$('.woocommerce-notices-wrapper').append(response.notices);
							$button.addClass('xts-not-added');
						} else {
							if ('widget' === xts_settings.action_after_add_to_cart) {
								$.magnificPopup.close();
							}

							// Trigger event so themes can refresh other areas
							$(document.body).trigger('added_to_cart', [
								fragments,
								cart_hash,
								$button
							]);
						}
					}
				},
				error  : function() {
					console.log('ajax adding to cart error');
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductAjaxAddToCart();
	});
})(jQuery);