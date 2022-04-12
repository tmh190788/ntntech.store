/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSectionReady xtsElementorColumnReady xtsElementorGlobalReady', function() {
		XTSThemeModule.animations();
	});

	XTSThemeModule.animations = function() {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		$('[class*="xts-animation"]').each(function() {
			var $element = $(this);

			if ('inited' === $element.data('xts-waypoint') || $element.parents('.xts-autoplay-animations-off').length > 0) {
				return;
			}

			$element.data('xts-waypoint', 'inited');

			$element.xtsWaypoint(function() {
				var $this = $($(this)[0].element);

				var classes = $this.attr('class').split(' ');
				var delay = 0;

				for (var index = 0; index < classes.length; index++) {
					if (classes[index].indexOf('xts_delay_') >= 0) {
						delay = classes[index].split('_')[2];
					}
				}

				$this.addClass('xts-animation-ready');

				setTimeout(function() {
					$this.addClass('xts-animated');
				}, delay);
			}, {
				offset: '90%'
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.animations();
	});
})(jQuery);