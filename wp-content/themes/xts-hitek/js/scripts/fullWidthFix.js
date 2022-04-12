/* global xts_settings */
(function($) {
	XTSThemeModule.fullWidthFix = function() {
		var $alignfull = $('.alignfull');

		recalc();

		XTSThemeModule.$window.on('resize', XTSThemeModule.debounce(function() {
			recalc();
		}, 300));

		function recalc() {
			if (XTSThemeModule.$window.width() <= 1400) {
				var $sidebarWidth = $('.xts-sidebar').outerWidth();

				$alignfull.css('--xts-sidebar-width', $sidebarWidth + 'px');
			} else {
				$alignfull.css('--xts-sidebar-width', '352px');
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.fullWidthFix();
	});
})(jQuery);