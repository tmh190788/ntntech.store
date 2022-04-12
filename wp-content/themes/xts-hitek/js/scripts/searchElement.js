/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsHeaderBuilderStickCloneHeader xtsHeaderBuilderUnStickCloneHeader xtsOffCanvasMyAccountShown xtsOffCanvasCartWidgetShown', function () {
		XTSThemeModule.searchElementCloseSearch();
	});

	XTSThemeModule.searchElement = function() {
		var $closeSide = $('.xts-close-side');
		var $searchWrapper = $('.xts-search-full-screen');
		var $search = $searchWrapper.find('input[type=text]');

		$('.xts-display-full-screen > a').on('click', function(e) {
			e.preventDefault();

			$searchWrapper.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
			setTimeout(function () {
				$search.focus();
			}, 600);
			XTSThemeModule.$document.trigger('xtsSearchOpened');
		});

		XTSThemeModule.$document.keyup(function(e) {
			if (27 === e.keyCode && $searchWrapper.hasClass('xts-opened')) {
				XTSThemeModule.searchElementCloseSearch();
			}
		});

		$('.xts-search-close > a, .xts-close-side').on('click', function(e) {
			if ($searchWrapper.hasClass('xts-opened')) {
				XTSThemeModule.searchElementCloseSearch();
			}
		});

		// Prevent search button click.
		$('.xts-header-search > a').on('click', function(e) {
			e.preventDefault();
		});
	};

	XTSThemeModule.searchElementCloseSearch = function() {
		var $searchWrapper = $('.xts-search-full-screen');
		if (!$searchWrapper.hasClass('xts-opened')) {
			return;
		}
		$searchWrapper.removeClass('xts-opened');
		$searchWrapper.find('input[type=text]').blur().val('');
		$('.xts-close-side').removeClass('xts-opened');
		XTSThemeModule.$document.trigger('xtsSearchClosed');
	};

	$(document).ready(function() {
		XTSThemeModule.searchElement();
	});
})(jQuery);