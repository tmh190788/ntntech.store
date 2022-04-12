/* global xts_settings */
(function($) {
	XTSThemeModule.widgetsCollapse = function() {
		if (XTSThemeModule.isSuperMobile) {
			$('.xts-footer .xts-widget-collapse').addClass('xts-inited');
		}

		XTSThemeModule.$document.on('click', '.xts-widget-collapse.xts-inited .widget-title', function() {
			var $title = $(this);
			var $widget = $title.parent();
			var $content = $widget.find('> .widget-title ~ *');

			if ($widget.hasClass('xts-opened') || ($widget.hasClass('xts-initially-opened') && !$widget.hasClass('xts-initially-clicked'))) {
				if ($widget.hasClass('xts-initially-opened')) {
					$widget.addClass('xts-initially-clicked');
				}

				$widget.removeClass('xts-opened');
				$content.stop().slideUp(200);
			} else {
				$widget.addClass('xts-opened');
				$content.stop().slideDown(200);
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.widgetsCollapse();
	});
})(jQuery);

