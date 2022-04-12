/* global xts_settings */
(function($) {
	XTSThemeModule.searchDropdown = function() {
		$('.xts-header-search.xts-display-dropdown').each(function() {
			var $element = $(this);

			$element.find('> a').on('click', function(e) {
				e.preventDefault();
				if (!$element.hasClass('xts-opened')) {
					$element.addClass('xts-opened');
					setTimeout(function() {
						$element.find('input[type=text]').focus();
					}, 200);
				} else {
					$element.removeClass('xts-opened');
					$element.find('input[type=text]').blur();
				}
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($element.hasClass('xts-opened') && !$(target).is('.xts-header-search.xts-display-dropdown') && !$(target).parents().is('.xts-header-search.xts-display-dropdown')) {
					$element.removeClass('xts-opened');
					$element.find('input[type=text]').blur();
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.searchDropdown();
	});
})(jQuery);