/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function () {
		XTSThemeModule.stickySidebar();
	});

	XTSThemeModule.stickySidebar = function() {
		if (XTSThemeModule.isTabletSize || 'undefined' === typeof $.fn.stick_in_parent) {
			return;
		}

		var $sidebar = $('.xts-sidebar');

		if ($sidebar.hasClass('xts-sidebar-hidden-lg') && $sidebar.hasClass('xts-sidebar-hidden-md')) {
			return;
		}

		if ($sidebar.hasClass('xts-sidebar-hidden-lg') && !$sidebar.hasClass('xts-sidebar-hidden-md') && XTSThemeModule.isDesktop) {
			return;
		}

		if ($sidebar.hasClass('xts-sidebar-hidden-md') && !$sidebar.hasClass('xts-sidebar-hidden-lg') && XTSThemeModule.isTabletSize) {
			return;
		}

		$('.xts-sidebar-sticky .xts-sidebar-inner').stick_in_parent({
			offset_top: parseInt(xts_settings.sticky_sidebar_offset),
			sticky_class: 'xts-is-stuck',
		});
	};

	$(document).ready(function() {
		XTSThemeModule.stickySidebar();
	});
})(jQuery);