/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPortfolioPjaxComplete', function() {
		XTSThemeModule.portfolioPhotoSwipe();
	});

	XTSThemeModule.portfolioPhotoSwipe = function() {
		$('.xts-portfolio-loop').each(function() {
			var $this = $(this);

			$this.on('click', '.xts-project-photoswipe > a', function(e) {
				e.preventDefault();
				var $parent = $(this).parents('.xts-col');
				var index = $parent.index();
				var items = getPortfolioImages($this.find('.xts-col'));

				XTSThemeModule.callPhotoSwipe({
					index       : index,
					items       : items,
					galleryItems: $this,
					parents     : '.xts-col',
					global      : false
				});
			});
		});

		var getPortfolioImages = function($gallery) {
			var items = [];

			$gallery.each(function() {
				var $btn = $(this).find('.xts-project-photoswipe > a');

				items.push({
					src: $btn.attr('href'),
					w  : $btn.data('width'),
					h  : $btn.data('height')
				});
			});

			return items;
		};
	};

	$(document).ready(function() {
		XTSThemeModule.portfolioPhotoSwipe();
	});
})(jQuery);