/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.shopToolsSearch();
	});

	XTSThemeModule.shopToolsSearch = function() {
		$('.xts-shop-tools .xts-search-form').each(function() {
			var $formWrapper = $(this);
			var $form = $formWrapper.find('form');

			$form.find('.searchsubmit').on('click', function(e) {
				if (!$form.hasClass('xts-opened')) {
					e.preventDefault();
					$form.addClass('xts-opened');
					setTimeout(function() {
						$form.find('input[type=text]').focus();
					}, 200);
				}
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($form.hasClass('xts-opened') && !$(target).is('.xts-shop-tools .xts-search-form') && !$(target).parents().is('.xts-shop-tools .xts-search-form')) {
					$form.removeClass('xts-opened');
					$form.find('input[type=text]').blur();
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.shopToolsSearch();
	});
})(jQuery);