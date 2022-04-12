/* global xts_settings */
(function($) {
	XTSThemeModule.headerBanner = function() {
		if ('undefined' === typeof Cookies) {
			return;
		}

		var banner_version = xts_settings.header_banner_version;
		var $banner = $('.xts-header-banner');

		if ('closed' === Cookies.get('xts_header_banner_' + banner_version) || 'no' === xts_settings.header_banner_close_button || 'no' === xts_settings.header_banner) {
			return;
		}

		if (!XTSThemeModule.$body.hasClass('page-template-maintenance')) {
			$banner.addClass('xts-display');
		}

		$banner.on('click', '.xts-header-banner-close', function(e) {
			e.preventDefault();
			closeBanner();
		});

		var closeBanner = function() {
			$banner.removeClass('xts-display').addClass('xts-hide');
			Cookies.set('xts_header_banner_' + banner_version, 'closed', {
				expires: parseInt(xts_settings.cookies_expires),
				path   : '/'
			});
		};
	};

	$(document).ready(function() {
		XTSThemeModule.headerBanner();
	});
})(jQuery);
