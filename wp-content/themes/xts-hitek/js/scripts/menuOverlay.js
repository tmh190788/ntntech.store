/* global xts_settings */
(function($) {
	XTSThemeModule.menuOverlay = function() {
		if ('no' === xts_settings.menu_overlay) {
			return;
		}

		var hoverSelector = '.xts-header .xts-nav-main .menu-item.item-level-0.menu-item-has-children.xts-event-hover, .xts-header .xts-nav-mega .menu-item.item-level-0.menu-item-has-children.xts-event-hover, .xts-sticky-cats';
		var sideClasses;

		$(hoverSelector).on('mouseleave', function() {
			$('.xts-close-side').attr('class', sideClasses);
		});

		$(hoverSelector).on('mouseenter mousemove', function() {
			var $this = $(this);
			var $overlay = $('.xts-close-side');

			if ($overlay.hasClass('xts-opened')) {
				return;
			}

			var isInHeader = $this.parents('.xts-header').length;
			var isInCategories = $this.hasClass('xts-sticky-cats');
			var isInHeaderCategories = $this.parents('.xts-header-cats').length;
			sideClasses = $overlay.attr('class');

			if (isInHeader) {
				if ($this.parents('.xts-sticked').length) {
					$overlay.addClass('xts-location-sticky-header');
				} else {
					$overlay.addClass('xts-location-header');
				}
				if (isInHeaderCategories) {
					$overlay.addClass('xts-location-header-cats');
				}
			} else if (isInCategories) {
				$overlay.addClass('xts-location-categories');
			}

			$overlay.addClass('xts-opened');
		});

		$('.xts-header .menu-item.item-level-0.menu-item-has-children.xts-event-click').on('click', function() {
			$('.xts-close-side').toggleClass('xts-opened').toggleClass('xts-location-header');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.menuOverlay();
	});
})(jQuery);