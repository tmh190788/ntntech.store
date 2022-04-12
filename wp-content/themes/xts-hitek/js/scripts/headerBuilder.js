/* global xts_settings */
(function($) {
	XTSThemeModule.headerBuilder = function() {
		var $header = $('.xts-header');

		if ($header.find('.xts-header-main').length <= 0) {
			return;
		}

		var $stickyElements = $('.xts-sticky-on');
		var $firstSticky = '';
		var headerHeight = $header.find('.xts-header-main')[0].offsetHeight; // .outerHeight(true); performance slow.
		var isSticked = false;
		var adminBar = $('#wpadminbar');
		var adminBarHeight = adminBar.length > 0 ? adminBar[0].offsetHeight : 0;
		var stickAfter = 300;
		var cloneHTML = '';
		var isHideOnScroll = $header.hasClass('xts-scroll-hide');
		var $overlay = $('.xts-close-side');

		$stickyElements.each(function() {
			var $this = $(this);
			if ($this[0].offsetHeight > 10) {
				$firstSticky = $this;
				return false;
			}
		});

		// Real header sticky option
		if ($header.hasClass('xts-sticky-real')) {
			// if no sticky rows
			if ($firstSticky.length === 0 || $firstSticky[0].offsetHeight < 10) {
				return;
			}

			stickAfter = $firstSticky.offset().top - adminBarHeight;

			$header.addClass('xts-prepared').css({
				paddingTop: headerHeight
			});
		}

		// Sticky header clone
		if ($header.hasClass('xts-sticky-clone')) {
			var data = [];
			data['cloneClass'] = $header.find('.xts-general-header').attr('class');

			cloneHTML = xts_settings.header_clone;

			cloneHTML = cloneHTML.replace(/<%([^%>]+)?%>/g, function(replacement) {
				var selector = replacement.slice(2, -2);

				return $header.find(selector).length
					? $('<div>').append($header.find(selector).first().clone()).html()
					: (data[selector] !== undefined) ? data[selector] : '';
			});

			$header.prepend(cloneHTML);

			$header.find('.xts-header-clone .xts-header-row').removeClass('xts-layout-equal-sides');
		}

		if ($header.hasClass('xts-scroll-slide')) {
			stickAfter = headerHeight + adminBarHeight;
		}

		var previousScroll;

		XTSThemeModule.$window.on('scroll', function() {
			var after = stickAfter;
			var currentScroll = XTSThemeModule.$window.scrollTop();
			var windowHeight = XTSThemeModule.$window.height();
			var documentHeight = XTSThemeModule.$document.height();
			var $headerBanner = $('.xts-header-banner');

			if ($headerBanner.length > 0 && $headerBanner.hasClass('xts-display')) {
				after += $headerBanner[0].offsetHeight;
			}

			if (!$('.xts-header-banner-close').length && $header.hasClass('xts-scroll-stick')) {
				after = stickAfter;
			}

			if (currentScroll > after) {
				stickHeader();
			} else {
				unstickHeader();
			}

			var startAfter = 100;

			if ($header.hasClass('xts-scroll-stick')) {
				startAfter = 500;
			}

			if (isHideOnScroll) {
				if (previousScroll - currentScroll > 0 && currentScroll > after) {
					$header.addClass('xts-up');
					$header.removeClass('xts-down');
				} else if (currentScroll - previousScroll > 0 && currentScroll + windowHeight != documentHeight && currentScroll > (after + startAfter)) {
					$header.addClass('xts-down');
					$header.removeClass('xts-up');
				} else if (currentScroll <= after) {
					$header.removeClass('xts-down');
					$header.removeClass('xts-up');
				} else if (currentScroll + windowHeight >= documentHeight - 5) {
					$header.addClass('xts-up');
					$header.removeClass('xts-down');
				}
			}

			previousScroll = currentScroll;
		});

		function stickHeader() {
			if (isSticked) {
				return;
			}

			isSticked = true;
			$header.addClass('xts-sticked');
			if ($header.hasClass('xts-sticky-clone')) {
				XTSThemeModule.$document.trigger('xtsHeaderBuilderStickCloneHeader');
			}
			if ($overlay.hasClass('xts-location-header')) {
				$overlay.removeClass('xts-location-header');
				$overlay.addClass('xts-location-sticky-header');
			}
		}

		function unstickHeader() {
			if (!isSticked) {
				return;
			}

			isSticked = false;
			$header.removeClass('xts-sticked');
			if ($header.hasClass('xts-sticky-clone')) {
				XTSThemeModule.$document.trigger('xtsHeaderBuilderUnStickCloneHeader');
			}
			if ($overlay.hasClass('xts-location-sticky-header')) {
				$overlay.addClass('xts-location-header');
				$overlay.removeClass('xts-location-sticky-header');
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.headerBuilder();
	});
})(jQuery);
