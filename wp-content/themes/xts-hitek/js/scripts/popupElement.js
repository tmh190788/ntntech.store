/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_popup.default', function() {
		XTSThemeModule.popupElement();
	});

	XTSThemeModule.popupElement = function() {
		if ('undefined' === typeof $.fn.magnificPopup) {
			return;
		}

		$.magnificPopup.close();

		$('.xts-popup-opener').magnificPopup({
			type        : 'inline',
			removalDelay: 400,
			tClose      : xts_settings.magnific_close,
			tLoading    : xts_settings.magnific_loading,
			preloader   : false,
			callbacks   : {
				beforeOpen: function() {
					this.st.mainClass = 'xts-popup-effect';
				},
				open      : function() {
					XTSThemeModule.$document.trigger('xtsImagesLoaded');
					XTSThemeModule.$window.resize();
				}
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.popupElement();
	});
})(jQuery);