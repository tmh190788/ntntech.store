/* global xts_settings */
(function($) {
	XTSThemeModule.imageElement = function() {
		$('.xts-photoswipe-image').each(function() {
			var $this = $(this);

			if ($this.hasClass('xts-image-global-lightbox')) {
				return;
			}

			$this.on('click', 'a', function(e) {
				var $link = $(this);
				e.preventDefault();
				var item = [
					{
						src  : $link.attr('href'),
						w    : $link.data('width'),
						h    : $link.data('height'),
						title: $link.find('img').attr('title')
					}
				];

				XTSThemeModule.callPhotoSwipe({
					index       : $link.data('index'),
					items       : item,
					galleryItems: $this,
					parents     : '.xts-image',
					global      : false
				});
			});
		});

		var isItemInArray = function(items, src) {
			for (var i = 0; i < items.length; i++) {
				if (items[i].src === src) {
					return true;
				}
			}

			return false;
		};

		// Global lightbox.
		var globalItems = [];

		$('.xts-image-global-lightbox').each(function() {
			var $this = $(this);
			var $link = $this.find('a');

			if (!isItemInArray(globalItems, $link.attr('href'))) {
				globalItems.push({
					src  : $link.attr('href'),
					w    : $link.data('width'),
					h    : $link.data('height'),
					title: $link.find('img').attr('title')
				});
			}

			$this.on('click', 'a', function(e) {
				e.preventDefault();
				var index = $(this).data('index');

				XTSThemeModule.callPhotoSwipe({
					index       : index,
					items       : globalItems,
					galleryItems: $('.xts-image-global-lightbox'),
					parents     : '.xts-image-single',
					global      : true
				});
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.imageElement();
	});
})(jQuery);
