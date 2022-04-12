/* global xts_settings */
(function($) {
	XTSThemeModule.promoPopup = function() {
		var promo_popup_version = xts_settings.promo_popup_version;

		if (xts_settings.promo_popup !== 'yes' || (xts_settings.promo_popup_hide_mobile === 'yes' && XTSThemeModule.isMobileSize) || 0 === $('.xts-promo-popup').length) {
			return;
		}

		var shown = false;
		var pages = Cookies.get('xts_shown_pages');

		var showPopup = function() {
			$.magnificPopup.open({
				items       : {
					src: '.xts-promo-popup'
				},
				type        : 'inline',
				removalDelay: 400,
				tClose      : xts_settings.magnific_close,
				tLoading    : xts_settings.magnific_loading,
				preloader   : false,
				callbacks   : {
					beforeOpen: function() {
						this.st.mainClass = 'xts-popup-effect';
					},
					close     : function() {
						Cookies.set('xts_popup_' + promo_popup_version, 'shown', {
							expires: parseInt(xts_settings.cookies_expires),
							path   : '/'
						});
					}
				}
			});
			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		};

		$('.xts-open-promo-popup').on('click', function(e) {
			e.preventDefault();
			showPopup();
		});

		if (!pages) {
			pages = 0;
		}

		if (pages < xts_settings.promo_popup_page_visited) {
			pages++;
			Cookies.set('xts_shown_pages', pages, {
				expires: parseInt(xts_settings.cookies_expires),
				path   : '/'
			});
			return false;
		}

		if (Cookies.get('xts_popup_' + promo_popup_version) !== 'shown') {
			if (xts_settings.promo_popup_show_after === 'user-scroll') {
				XTSThemeModule.$window.on('scroll', function() {
					if (shown) {
						return false;
					}

					if (XTSThemeModule.$document.scrollTop() >= xts_settings.promo_popup_user_scroll) {
						showPopup();
						shown = true;
					}
				});
			} else {
				setTimeout(function() {
					showPopup();
				}, xts_settings.promo_popup_delay);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.promoPopup();
	});
})(jQuery);
