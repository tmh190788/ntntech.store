/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_mega_menu.default', function() {
		XTSThemeModule.menuClickEvent();
	});

	XTSThemeModule.menuClickEvent = function() {
		var menu = $('.xts-header .xts-nav-main, .xts-header .xts-nav-secondary, .elementor-widget-xts_mega_menu .xts-nav-mega, .xts-sticky-cats .xts-nav-sticky-cat');

		menu.on('click', ' > .xts-event-click > a', function(e) {
			e.preventDefault();
			var $this = $(this);

			if (!$this.parent().hasClass('xts-opened')) {
				menu.find('.xts-opened').removeClass('xts-opened');
			}

			$this.parent().toggleClass('xts-opened');
		});

		XTSThemeModule.$document.on('click', function(e) {
			var target = e.target;

			if (menu.find('.xts-opened').length > 0 && !$(target).is('.xts-event-hover') && !$(target).parents().is('.xts-event-hover') && !$(target).parents().is('.xts-opened')) {
				menu.find('.xts-opened').removeClass('xts-opened');
				$('.xts-close-side').removeClass('xts-opened');

				return false;
			}
		});

		XTSThemeModule.$window.on('resize', XTSThemeModule.debounce(function() {
			if (XTSThemeModule.isTablet()) {
				menu.find(' > .menu-item-has-children.xts-event-hover').each(function() {
					$(this).data('original-event', 'hover').removeClass('xts-event-hover').addClass('xts-event-click');
				});
			} else {
				menu.find(' > .xts-event-click').each(function() {
					var $this = $(this);
					if ('hover' === $this.data('original-event')) {
						$this.removeClass('xts-event-click').addClass('xts-event-hover');
					}
				});
			}
		}, 300));
	};

	$(document).ready(function() {
		XTSThemeModule.menuClickEvent();
	});
})(jQuery);
