/* global xts_settings */
(function($) {
	XTSThemeModule.offCanvasCartWidget = function () {
		var $closeSide = $('.xts-close-side');
		var $widget = $('.xts-cart-widget-side');
		var $body = XTSThemeModule.$body;

		$body.on('click', '.xts-header-cart.xts-opener, .xts-navbar-cart.xts-opener', function (e) {
			if (!isCart() && !isCheckout()) {
				e.preventDefault();
			}

			if ($widget.hasClass('xts-opened')) {
				hideWidget();
			} else {
				showWidget();
			}
		});

		$body.on('click touchstart', '.xts-close-side, .xts-close-button', function () {
			hideWidget();
		});

		XTSThemeModule.$document.keyup(function (e) {
			if (27 === e.keyCode) {
				hideWidget();
			}
		});

		var showWidget = function () {
			XTSThemeModule.$document.trigger('xtsOffCanvasCartWidgetShown');

			if (isCart() || isCheckout()) {
				return false;
			}

			$widget.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
		};

		var hideWidget = function () {
			$widget.removeClass('xts-opened');
			$closeSide.removeClass('xts-opened');
		};

		var isCart = function () {
			return XTSThemeModule.$body.hasClass('woocommerce-cart');
		};

		var isCheckout = function () {
			return XTSThemeModule.$body.hasClass('woocommerce-checkout');
		};
	};

	$(document).ready(function() {
		XTSThemeModule.offCanvasCartWidget();
	});
})(jQuery);