/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.pageTitleProductCategoriesMenuBtns();
	});

	XTSThemeModule.pageTitleProductCategories = function() {
		if (XTSThemeModule.isDesktop) {
			return;
		}

		var time = 200;
		var $body = XTSThemeModule.$body;

		$body.on('click', '.xts-nav-shop-cat .xts-show-cat-btn, .xts-nav-shop-cat .xts-submenu-opener', function(e) {
			e.preventDefault();
			var $this = $(this);

			if ($this.hasClass('xts-opened')) {
				$this.removeClass('xts-opened').siblings('.xts-dropdown').slideUp(time);
			} else {
				$this.addClass('xts-opened').siblings('.xts-dropdown').slideDown(time);
			}
		});

		$body.on('click', '.xts-show-cat-btn', function(e) {
			e.preventDefault();

			if (isOpened()) {
				closeCats();
			} else {
				openCats();
			}
		});

		$body.on('click', '.xts-nav-shop-cat a', function(e) {
			if (!$(e.target).hasClass('xts-show-cat-btn')) {
				closeCats();
				$('.xts-nav-shop-cat').stop().attr('style', '');
			}
		});

		var isOpened = function() {
			return $('.xts-nav-shop-cat').hasClass('xts-opened');
		};

		var openCats = function() {
			$('.xts-nav-shop-cat').addClass('xts-opened').stop().slideDown(time);
			$('.xts-show-cat-btn').addClass('xts-opened');
		};

		var closeCats = function() {
			$('.xts-nav-shop-cat').removeClass('xts-opened').stop().slideUp(time);
			$('.xts-show-cat-btn').removeClass('xts-opened');
		};
	};

	XTSThemeModule.pageTitleProductCategoriesMenuBtns = function() {
		if (XTSThemeModule.isDesktop) {
			return;
		}

		$('.xts-nav-shop-cat .xts-has-children').prepend('<span class="xts-submenu-opener"></span>');
	};

	$(document).ready(function() {
		XTSThemeModule.pageTitleProductCategories();
		XTSThemeModule.pageTitleProductCategoriesMenuBtns();
	});
})(jQuery);