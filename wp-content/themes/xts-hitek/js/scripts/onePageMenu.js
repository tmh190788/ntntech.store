/* global xts_settings */
(function($) {
	XTSThemeModule.onePageMenu = function() {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		var scrollToAnchor = function(hash) {
			var $htmlBody = $('html, body');

			$htmlBody.stop(true);
			var $anchor = $('.xts-menu-anchor[data-id="' + hash + '"]');

			if ($anchor.length < 1) {
				return;
			}

			var position = $anchor.offset().top;

			$htmlBody.animate({
				scrollTop: position - $anchor.data('offset')
			}, 800);

			setTimeout(function() {
				activeMenuItem(hash);
			}, 800);
		};

		var activeMenuItem = function(hash) {
			$('.xts-onepage-link').each(function() {
				var $this = $(this);
				var itemHash = $this.find('> a').attr('href').split('#')[1];

				if (itemHash === hash) {
					$this.siblings().removeClass('current-menu-item');
					$this.addClass('current-menu-item');
				}
			});
		};

		XTSThemeModule.$body.on('click', '.xts-onepage-link > a', function(e) {
			var $this = $(this);
			var hash = $this.attr('href').split('#')[1];

			if ($('.xts-menu-anchor[data-id="' + hash + '"]').length < 1) {
				return;
			}

			e.stopPropagation();
			e.preventDefault();

			scrollToAnchor(hash);

			$('.xts-close-side').trigger('click');
			$('.xts-fs-close').trigger('click');
		});

		if ($('.xts-onepage-link').length > 0) {
			XTSThemeModule.$document.on('scroll', function() {
				var scrollTop = $(this).scrollTop();

				if (scrollTop === 0) {
					var $item = $('.xts-onepage-link').first();

					$item.siblings().removeClass('current-menu-item');
					$item.addClass('current-menu-item');
				}
			});

			$('.xts-menu-anchor').xtsWaypoint(function() {
				activeMenuItem($($(this)[0].element).data('id'));
			}, {
				offset: function() {
					return $($(this)[0].element).data('offset');
				}
			});

			var locationHash = window.location.hash.split('#')[1];

			if (window.location.hash.length > 1) {
				setTimeout(function() {
					scrollToAnchor(locationHash);
				}, 500);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.onePageMenu();
	});
})(jQuery);