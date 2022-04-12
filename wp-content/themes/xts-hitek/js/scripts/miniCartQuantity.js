/* global xts_settings */
(function($) {
	XTSThemeModule.miniCartQuantity = function() {
		var timeout;

		XTSThemeModule.$document.on('change input', '.woocommerce-mini-cart .quantity .qty', function() {
			var input = $(this);
			var qtyVal = input.val();
			var itemID = input.parents('.woocommerce-mini-cart-item').data('key');
			var cart_hash_key = xts_settings.cart_hash_key;
			var fragment_name = xts_settings.fragment_name;

			clearTimeout(timeout);

			timeout = setTimeout(function() {
				input.parents('.mini_cart_item').addClass('xts-loading');

				$.ajax({
					url     : xts_settings.ajaxurl,
					data    : {
						action : 'xts_update_mini_cart_item',
						item_id: itemID,
						qty    : qtyVal
					},
					dataType: 'json',
					method  : 'GET',
					success : function(data) {
						if (data && data.fragments) {

							$.each(data.fragments, function(key, value) {
								$(key).replaceWith(value);
							});

							if (XTSThemeModule.supports_html5_storage) {
								sessionStorage.setItem(fragment_name, JSON.stringify(data.fragments));
								localStorage.setItem(cart_hash_key, data.cart_hash);
								sessionStorage.setItem(cart_hash_key, data.cart_hash);

								if (data.cart_hash) {
									sessionStorage.setItem('wc_cart_created', (new Date()).getTime());
								}
							}
						}
					}
				});
			}, 500);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.miniCartQuantity();
	});
})(jQuery);
