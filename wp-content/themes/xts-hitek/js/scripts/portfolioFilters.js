/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPortfolioPjaxComplete', function() {
		XTSThemeModule.portfolioFilters();
	});

	XTSThemeModule.portfolioFilters = function() {
		$('.xts-type-masonry .xts-nav-portfolio').on('click', 'a', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $filter = $this.parents('.xts-nav-portfolio-wrapper');
			var filterValue = $this.parent().attr('data-filter');

			$filter.find('.xts-active').removeClass('xts-active');
			$this.parent().addClass('xts-active');

			var itemQueue = [];
			var queueTimer;

			function processItemQueue(delay) {
				if (queueTimer) {
					return;
				}

				queueTimer = window.setInterval(function() {
					if (itemQueue.length) {
						$(itemQueue.shift()).addClass('xts-animated');
						processItemQueue(delay);
					} else {
						window.clearInterval(queueTimer);
						queueTimer = null;
					}
				}, delay);
			}

			$filter.siblings('.xts-portfolio-loop').isotope({
				filter: function() {
					var $item = $(this);
					var $itemChildren = $item.find('> .xts-project');
					var $parent = $item.parent();
					var delay = $parent.data('animation-delay');

					if (($itemChildren.hasClass(filterValue) || '*' === filterValue) && $parent.hasClass('xts-in-view-animation')) {
						$item.removeClass('xts-animated');

						$item.imagesLoaded(function() {
							itemQueue.push($item);
							processItemQueue(delay);
						});
					}

					return $itemChildren.hasClass(filterValue) || '*' === filterValue;
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.portfolioFilters();
	});
})(jQuery);
