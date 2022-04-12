/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_hotspots.default', function() {
		XTSThemeModule.hotSpotsElement();
	});

	XTSThemeModule.hotSpotsElement = function() {
		$('.xts-spot').each(function() {
			var $this = $(this);
			var $btn = $this.find('.xts-spot-icon');

			if ((!$this.hasClass('xts-event-click') && XTSThemeModule.isDesktop ) || $this.hasClass('xts-inited')) {
				return;
			}

			$this.addClass('xts-inited');

			$btn.on('click', function() {
				var $content = $(this).parent().find('.xts-spot-content');

				if ($content.hasClass('xts-opened')) {
					$content.removeClass('xts-opened');
				} else {
					$content.addClass('xts-opened');
					$content.parent().siblings().find('.xts-spot-content').removeClass('xts-opened');
				}

				return false;
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($this.find('.xts-spot-content').hasClass('xts-opened') && !$(target).is('.xts-spot') && !$(target).parents().is('.xts-spot')) {
					$this.find('.xts-spot-content').removeClass('xts-opened');
					return false;
				}
			});
		});

		$('.xts-spot-content').each(function() {
			var $this = $(this);
			var offsetLeft = $this.offset().left;
			var offsetRight = XTSThemeModule.windowWidth - (offsetLeft + $this.outerWidth());

			if (XTSThemeModule.isTabletSize) {
				if (offsetLeft <= 0) {
					$this.css('marginLeft', Math.abs(offsetLeft - 15) + 'px');
				}

				if (offsetRight <= 0) {
					$this.css('marginLeft', offsetRight - 15 + 'px');
				}
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.hotSpotsElement();
	});
})(jQuery);