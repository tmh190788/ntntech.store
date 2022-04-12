/* global xts_settings */
(function($) {
	$.each([
		'frontend/element_ready/xts_accordion.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.accordionElement();
		});
	});

	XTSThemeModule.accordionElement = function() {
		$('.xts-accordion').each(function() {
			var $wrapper = $(this);
			var $tabTitles = $wrapper.find('.xts-accordion-title');
			var $tabContents = $wrapper.find('.xts-accordion-content');
			var toggleSelf = 'yes' === $wrapper.data('toggle-self');
			var singleProduct = $wrapper.hasClass('woocommerce-tabs');
			var activeClass = 'xts-active';
			var state = $wrapper.data('state');
			var time = 300;

			var isTabActive = function(tabIndex) {
				return $tabTitles.filter('[data-accordion-index="' + tabIndex + '"]').hasClass(activeClass);
			};

			var activateTab = function(tabIndex) {
				var $requestedTitle = $tabTitles.filter('[data-accordion-index="' + tabIndex + '"]');
				var $requestedContent = $tabContents.filter('[data-accordion-index="' + tabIndex + '"]');

				$requestedTitle.addClass(activeClass);
				$requestedContent.stop().slideDown(time).addClass(activeClass);

				if ('first' === state && !$wrapper.hasClass('xts-inited')) {
					$requestedContent.stop().show().css('display', 'block');
				}

				$wrapper.addClass('xts-inited');
			};

			var deactivateActiveTab = function() {
				var $activeTitle = $tabTitles.filter('.' + activeClass);
				var $activeContent = $tabContents.filter('.' + activeClass);

				$activeTitle.removeClass(activeClass);
				$activeContent.stop().slideUp(time).removeClass(activeClass);
			};

			var deactivateActiveTabByIndex = function(tabIndex) {
				var $requestedTitle = $tabTitles.filter('[data-accordion-index="' + tabIndex + '"]');
				var $requestedContent = $tabContents.filter('[data-accordion-index="' + tabIndex + '"]');

				$requestedTitle.removeClass(activeClass);
				$requestedContent.stop().slideUp(time).removeClass(activeClass);
			};

			var getFirstTabIndex = function() {
				return $tabTitles.first().data('accordion-index');
			};

			if ('first' === state) {
				activateTab(getFirstTabIndex());
			}

			$tabTitles.on('click', function() {
				var tabIndex = $(this).data('accordion-index');
				var isActiveTab = isTabActive(tabIndex);

				if (singleProduct) {
					if (isActiveTab && toggleSelf) {
						deactivateActiveTabByIndex(tabIndex);
					} else {
						activateTab(tabIndex);
					}
				} else {
					if (isActiveTab && toggleSelf) {
						deactivateActiveTab();
					} else {
						deactivateActiveTab();
						activateTab(tabIndex);
					}
				}

				setTimeout(function() {
					XTSThemeModule.$window.resize();
				}, time);
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.accordionElement();
	});
})(jQuery);