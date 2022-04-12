/* global xts_settings */
(function($) {
	XTSThemeModule.pageTitleEffect = function() {
		var $pageTitle   = $('.xts-parallax-scroll'),
		    lastMoveTime = 0,
		    frameTime    = 10;

		if ($pageTitle.length < 1) {
			return;
		}

		var $inner  = $pageTitle.find('.container'),
		    $bg     = $pageTitle.find('.xts-page-title-overlay'),
		    $window = XTSThemeModule.$window;

		XTSThemeModule.$document.on('scroll', function() {
			var now    = Date.now(),
			    height = $pageTitle.outerHeight(),
			    top    = $pageTitle.offset().top,
			    bottom = height + top,
			    scroll = $window.scrollTop();

			if (now < lastMoveTime + frameTime || scroll > bottom) {
				return;
			}

			lastMoveTime = now;

			var translateY = scroll / 5,
			    opacity    = 1 - 0.9 * scroll / bottom,
			    scale      = 1 + 0.1 * scroll / bottom;

			window.requestAnimationFrame(function() {
				$inner.css({
					transform: 'translateY(' + translateY + 'px)',
					opacity  : opacity
				});

				$bg.css({
					transform      : 'translateY(' + translateY / 2 + 'px) scale(' + scale + ', ' + scale + ')',
					transformOrigin: 'top'
				});
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.pageTitleEffect();
	});
})(jQuery);
