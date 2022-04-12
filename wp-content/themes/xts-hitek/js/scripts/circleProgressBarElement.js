/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_circle_progress.default', function() {
		XTSThemeModule.circleProgressBarElement();
	});

	XTSThemeModule.circleProgressBarElement = function() {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		$('.xts-circle-progress').each(function() {
			var $element = $(this);
			var $circleValue = $element.find('.xts-circle-meter-value');
			var $counter = $element.find('.xts-circle-number');
			var counterFinal = $counter.data('final');
			var duration = $element.data('duration');

			$element.xtsWaypoint(function() {
				if ('done' !== $counter.attr('data-state') && $counter.text() !== counterFinal) {
					$counter.prop('Counter', 0).animate({
						Counter: counterFinal
					}, {
						duration: duration,
						easing  : 'swing',
						step    : function(now) {
							if (now >= counterFinal) {
								$counter.attr('data-state', 'done');
							}

							$counter.text(Math.ceil(now));
						}
					});
				}

				// animate progress
				var circumference = parseInt($element.data('circumference'));
				var dashoffset = circumference * (1 - ($circleValue.data('value') / 100));

				$circleValue.css({
					'transitionDuration': duration + 'ms',
					'strokeDashoffset'  : dashoffset
				});

			}, {
				offset: '90%'
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.circleProgressBarElement();
	});
})(jQuery);