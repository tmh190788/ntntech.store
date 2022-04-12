/* global xts_settings */
(function($) {
	XTSThemeModule.offCanvasMyAccount = function () {
		var $closeSide = $('.xts-close-side');
		var $element = $('.xts-login-side');
		var $body = XTSThemeModule.$body;

		$body.on('click', '.xts-header-my-account.xts-opener, .xts-login-to-price-msg.xts-opener, .xts-menu-item-account.xts-opener, .xts-navbar-my-account.xts-opener', function (e) {
			e.preventDefault();

			if ($element.hasClass('xts-opened')) {
				hideWidget();
			} else {
				setTimeout(function() {
					showWidget();
				}, 100);
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
			XTSThemeModule.$document.trigger('xtsOffCanvasMyAccountShown');
			$element.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
		};

		var hideWidget = function () {
			$element.removeClass('xts-opened');
			$closeSide.removeClass('xts-opened');
		};

		if ( $element.find('.woocommerce-notices-wrapper > ul').length > 0 ) {
			showWidget();
		}
	};

	$(document).ready(function() {
		XTSThemeModule.offCanvasMyAccount();
	});
})(jQuery);