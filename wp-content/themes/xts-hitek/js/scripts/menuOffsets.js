/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_mega_menu.default', function() {
		XTSThemeModule.menuOffsets();
	});

	XTSThemeModule.menuOffsets = function() {
		var setOffset = function(li) {
			var $dropdown = li.find(' > .xts-dropdown-menu');
			var dropdownWidth = $dropdown.outerWidth();
			var dropdownOffset = $dropdown.offset();
			var toRight;
			var viewportWidth;

			$dropdown.attr('style', '');

			if (!dropdownWidth || !dropdownOffset) {
				return;
			}

			if ($dropdown.hasClass('xts-style-full')) {
				viewportWidth = XTSThemeModule.$window.width();

				if (dropdownOffset.left + dropdownWidth + parseInt(xts_settings.menu_animation_offset) >= viewportWidth) {
					toRight = dropdownOffset.left + dropdownWidth - viewportWidth + parseInt(xts_settings.menu_animation_offset);

					$dropdown.css({
						left: -toRight
					});
				}
			} else if ($dropdown.hasClass('xts-style-sized') || $dropdown.hasClass('xts-style-default') || $dropdown.hasClass('xts-style-container')) {
				viewportWidth = xts_settings.site_width;

				if (XTSThemeModule.$window.width() < viewportWidth || !viewportWidth) {
					viewportWidth = XTSThemeModule.$window.width();
				}

				var extraSpace = 15;
				var containerOffset = (XTSThemeModule.$window.width() - viewportWidth) / 2;
				var dropdownOffsetLeft = dropdownOffset.left - containerOffset;

				if (XTSThemeModule.$body.hasClass('xts-layout-boxed')) {
					extraSpace = 0;
				}

				if (($dropdown.hasClass('xts-style-container')) || (dropdownOffsetLeft + dropdownWidth >= viewportWidth)) {
					toRight = dropdownOffsetLeft + dropdownWidth - viewportWidth + parseInt(xts_settings.menu_animation_offset);

					$dropdown.css({
						left: -toRight - extraSpace
					});
				}
			}
		};

		$('.xts-nav-main, .xts-nav-main > li, .xts-nav-secondary, .xts-nav-secondary > li, .xts-nav-mega.xts-direction-h, .xts-nav-mega.xts-direction-h > li').each(function() {
			var $menu = $(this);

			if ($menu.hasClass('menu-item')) {
				$menu = $(this).parent();
			}

			$menu.on('mouseenter mousemove', function() {
				if ($menu.hasClass('xts-offsets-calculated')) {
					return;
				}

				$menu.find(' > .menu-item-has-children').each(function() {
					setOffset($(this));
				});

				$menu.addClass('xts-offsets-calculated');
			});

			setTimeout(function() {
				XTSThemeModule.$window.on('resize', XTSThemeModule.debounce(function() {
					$menu.removeClass('xts-offsets-calculated');
					$menu.find(' > .menu-item-has-children > .xts-dropdown-menu').attr('style', '');
				}, 300));
			}, 2000);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.menuOffsets();
	});
})(jQuery);