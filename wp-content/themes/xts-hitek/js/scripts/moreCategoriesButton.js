/* global xts_settings */
(function($) {
	XTSThemeModule.moreCategoriesButton = function () {
		$('.xts-more-cats').each(function () {
			var $wrapper = $(this);

			$wrapper.find('.xts-more-cats-btn a').on('click', function (e) {
				e.preventDefault();
				$wrapper.addClass('xts-more-cats-visible');
				$(this).parent().remove();
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.moreCategoriesButton();
	});
})(jQuery);
