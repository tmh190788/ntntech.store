/* global xts_settings */
(function($) {
	XTSThemeModule.scrollTopButton = function() {
		var $btn = $('.xts-scroll-to-top');

		if ($btn.length <= 0) {
			return;
		}

		XTSThemeModule.$window.on('scroll', function() {
			if ($(this).scrollTop() > 100) {
				$btn.addClass('xts-shown');
			} else {
				$btn.removeClass('xts-shown');
			}
		});

		$btn.on('click', function() {
			$('html, body').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	};

	$(document).ready(function() {
		XTSThemeModule.scrollTopButton();
	});
})(jQuery);