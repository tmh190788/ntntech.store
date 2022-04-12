/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete xtsPortfolioPjaxComplete', function () {
		XTSThemeModule.offCanvasSidebar();
	});

	XTSThemeModule.$document.on('xtsPortfolioPjaxStart xtsPjaxStart', function () {
		XTSThemeModule.hideOffCanvasSidebar();
	});

	XTSThemeModule.offCanvasSidebar = function () {
		var $closeSide = $('.xts-close-side');
		var $sidebar = $('.xts-sidebar');
		var $body = XTSThemeModule.$body;

		if ($sidebar.hasClass('xts-sidebar-hidden-lg') && XTSThemeModule.isDesktop || $sidebar.hasClass('xts-sidebar-hidden-md') && XTSThemeModule.isTabletSize) {
			$sidebar.addClass('xts-inited');
		}

		$body.on('click', '.xts-sidebar-opener, .xts-navbar-sidebar', function (e) {
			e.preventDefault();

			if ($sidebar.hasClass('xts-opened')) {
				XTSThemeModule.hideOffCanvasSidebar();
			} else {
				showSidebar();
			}
		});

		$body.on('click touchstart', '.xts-close-side, .xts-close-button', function () {
			XTSThemeModule.hideOffCanvasSidebar();
		});

		XTSThemeModule.$document.keyup(function (e) {
			if (27 === e.keyCode) {
				XTSThemeModule.hideOffCanvasSidebar();
			}
		});

		var showSidebar = function () {
			$sidebar.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
		};
	};

	XTSThemeModule.hideOffCanvasSidebar = function () {
		$('.xts-sidebar').removeClass('xts-opened');
		$('.xts-close-side').removeClass('xts-opened');
	};

	$(document).ready(function() {
		XTSThemeModule.offCanvasSidebar();
	});
})(jQuery);
