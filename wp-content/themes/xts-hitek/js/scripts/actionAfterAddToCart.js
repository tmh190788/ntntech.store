/* global xts_settings */
(function($) {
	XTSThemeModule.actionAfterAddToCart = function() {
		var closeAfterTimeoutNumber;
		var hoverTimeoutNumber = 0;

		XTSThemeModule.$body.on('added_to_cart', function() {
			if ('popup' === xts_settings.action_after_add_to_cart) {
				var html = [
					'<h4>' + xts_settings.action_after_add_to_cart_title + '</h4>',
					'<a href="#" class="xts-button xts-style-link xts-color-primary xts-close-popup">' + xts_settings.action_after_add_to_cart_continue_shopping + '</a>',
					'<a href="' + xts_settings.action_after_add_to_cart_cart_url + '" class="xts-button xts-color-primary xts-view-cart">' + xts_settings.action_after_add_to_cart_view_cart + '</a>'
				].join('');

				$.magnificPopup.open({
					items       : {
						src : '<div class="mfp-with-anim xts-popup-content xts-cart-popup">' + html + '</div>',
						type: 'inline'
					},
					tClose      : xts_settings.magnific_close,
					tLoading    : xts_settings.magnific_loading,
					removalDelay: 400,
					preloader   : false,
					callbacks   : {
						beforeOpen: function() {
							this.st.mainClass = 'xts-popup-effect';
						}
					}
				});

				$('.xts-popup-content').on('click', '.xts-close-popup', function(e) {
					e.preventDefault();
					$.magnificPopup.close();
				});

				closeAfterTimeout();
			} else if ('widget' === xts_settings.action_after_add_to_cart) {
				clearTimeout(hoverTimeoutNumber);

				if ($('.xts-sticked .xts-header-cart').length > 0) {
					$('.xts-sticked .xts-header-cart .xts-dropdown').addClass('xts-opened');
				} else {
					$('.xts-header-cart .xts-dropdown').addClass('xts-opened');
				}

				hoverTimeoutNumber = setTimeout(function() {
					$('.xts-header-cart .xts-dropdown').removeClass('xts-opened');
				}, 3500);

				var $opener = $('.xts-header-cart.xts-opener');
				if ($opener.length > 0) {
					$opener.first().trigger('click');
				}

				closeAfterTimeout();
			}
		});

		var closeAfterTimeout = function() {
			if ('no' === xts_settings.action_after_add_to_cart_timeout) {
				return false;
			}

			clearTimeout(closeAfterTimeoutNumber);

			closeAfterTimeoutNumber = setTimeout(function() {
				$('.xts-close-side').trigger('click');
				$.magnificPopup.close();
			}, parseInt(xts_settings.action_after_add_to_cart_timeout_number) * 1000);
		};
	};

	$(document).ready(function() {
		XTSThemeModule.actionAfterAddToCart();
	});
})(jQuery);