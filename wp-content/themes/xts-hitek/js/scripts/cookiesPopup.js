/* global xts_settings */
(function($) {
	XTSThemeModule.cookiesPopup = function() {
		if ('undefined' === typeof Cookies) {
			return;
		}

		var cookies_version = xts_settings.cookies_version;
		if ('accepted' === Cookies.get('xts_cookies_' + cookies_version)) {
			return;
		}

		var $cookies = $('.xts-cookies');

		setTimeout(function() {
			$cookies.addClass('xts-show');
			$cookies.on('click', '.xts-cookies-accept-btn', function(e) {
				e.preventDefault();
				acceptCookies();
			});
		}, 2500);

		var acceptCookies = function() {
			$cookies.removeClass('xts-show');
			Cookies.set('xts_cookies_' + cookies_version, 'accepted', {
				expires: parseInt(xts_settings.cookies_expires),
				path   : '/'
			});
		};
	};

	$(document).ready(function() {
		XTSThemeModule.cookiesPopup();
	});
})(jQuery);