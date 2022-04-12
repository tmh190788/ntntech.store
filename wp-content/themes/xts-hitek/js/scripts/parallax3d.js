/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPortfolioLoadMoreSuccess', function() {
		XTSThemeModule.parallax3d();
	});

	$.each([
		'frontend/element_ready/xts_banner_carousel.default',
		'frontend/element_ready/xts_banner.default',
		'frontend/element_ready/xts_portfolio.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.parallax3d();
		});
	});

	XTSThemeModule.parallax3d = function() {
		var $elements    = $('.xts-hover-parallax, .xts-portfolio-design-parallax .xts-project'),
		    lastMoveTime = 0,
		    frameTime    = 30;

		$elements.each(function() {
			var $el = $(this);

			if ($el.hasClass('xts-parallax3d-init')) {
				return;
			}

			$el.addClass('xts-parallax3d-init');

			$el.on('mousemove', function(e) {
				var now = Date.now();

				if (now < lastMoveTime + frameTime) {
					return;
				}

				lastMoveTime = now;

				var $el         = $(this),
				    width       = $el.outerWidth(),
				    elMouseXRel = (e.pageX - $el.offset().left) / width,
				    elMouseYRel = (e.pageY - $el.offset().top) / $el.outerHeight(),
				    zIndex      = XTSThemeModule.$body.data('parallax-index') ? XTSThemeModule.$body.data('parallax-index') : 1,
				    timeout     = XTSThemeModule.$body.data('parallax-timeout') ? XTSThemeModule.$body.data('parallax-timeout') : 0;

				clearTimeout(timeout);

				if (elMouseXRel > 1) {
					elMouseXRel = 1;
				}
				if (elMouseYRel > 1) {
					elMouseYRel = 1;
				}
				if (elMouseXRel < 0) {
					elMouseXRel = 0;
				}
				if (elMouseYRel < 0) {
					elMouseYRel = 0;
				}

				var rotateX = -12 * (0.5 - elMouseYRel),
				    rotateY = +12 * (0.5 - elMouseXRel);

				var translateX = elMouseXRel * 2 * 2 - 2,
				    translateY = elMouseYRel * 2 * 2 - 2; // -2 to 2

				var perspective = width * 3;

				window.requestAnimationFrame(function() {
					$el.css({
						transform: 'perspective(' + perspective + 'px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(' + translateY + 'px) translateX(' + translateX + 'px) scale(1.05, 1.05)',
						zIndex   : zIndex
					});
				});
			});

			$el.on('mouseleave', function() {
				var $el    = $(this),
				    width  = $el.outerWidth(),
				    zIndex = XTSThemeModule.$body.data('parallax-index') ? XTSThemeModule.$body.data('parallax-index') : 1;

				var perspective = width * 3;

				window.requestAnimationFrame(function() {
					$el.css({
						transform: 'perspective(' + perspective + 'px) rotateX(0deg) rotateY(0deg) translateZ(0px)'
					});
				});

				var timeout = setTimeout(function() {
					$el.css({
						zIndex: 1
					});
				}, 250);

				XTSThemeModule.$body.data('parallax-index', zIndex + 1);
				XTSThemeModule.$body.data('parallax-timeout', timeout);
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.parallax3d();
	});
})(jQuery);
