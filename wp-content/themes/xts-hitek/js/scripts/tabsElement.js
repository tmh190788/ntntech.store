/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_tabs.default', function() {
		XTSThemeModule.tabsElement();
	});

	XTSThemeModule.tabsElement = function() {
		$('.xts-tabs').each(function() {
			var $wrapper = $(this);
			var $tabTitles = $wrapper.find('.xts-nav-tabs li');
			var $tabContents = $wrapper.find('.xts-tab-content');
			var activeClass = 'xts-active';
			var animationClass = 'xts-in';
			var animationTime = 100;

			$tabTitles.on('click', 'a', function(e) {
				e.preventDefault();
				var $control = $(this).parent();
				var tabIndex = $control.data('tab-index');

				if (!$control.hasClass(activeClass)) {
					deactivateActiveTab();
					activateTab(tabIndex);
				}
			});

			var activateTab = function(tabIndex) {
				var $requestedTitle = $tabTitles.filter('[data-tab-index="' + tabIndex + '"]');
				var $requestedContent = $tabContents.filter('[data-tab-index="' + tabIndex + '"]');

				setTimeout(function() {
					$requestedTitle.addClass(activeClass);
					$requestedContent.addClass(activeClass);
				}, animationTime);

				setTimeout(function() {
					$requestedContent.addClass(animationClass);
				}, animationTime * 2);
			};

			var deactivateActiveTab = function() {
				var $activeTitle = $tabTitles.filter('.' + activeClass);
				var $activeContent = $tabContents.filter('.' + activeClass);

				$activeContent.removeClass(animationClass);
				setTimeout(function() {
					$activeTitle.removeClass(activeClass);
					$activeContent.removeClass(activeClass);
				}, animationTime);
			};
		});
	};

	$(document).ready(function() {
		XTSThemeModule.tabsElement();
	});
})(jQuery);