/* global xts_settings */
(function($) {
	XTSThemeModule.imageGalleryElement = function() {
		var getGalleryItems = function($gallery, items) {
			$gallery.find('a').each(function() {
				var $link = $(this);
				var index = $link.data('index');

				if (!isItemInArray(items, $link.attr('href'))) {
					items[index] = {
						src  : $link.attr('href'),
						w    : $link.data('width'),
						h    : $link.data('height'),
						title: $link.find('img').attr('title')
					};
				}
			});

			return items;
		};

		var isItemInArray = function(items, src) {
			for (var i = 0; i < items.length; i++) {
				if (items[i] && items[i].src === src) {
					return true;
				}
			}

			return false;
		};

		$('.xts-photoswipe-images').each(function() {
			var $this = $(this);

			if ($this.hasClass('xts-images-global-lightbox') || $this.hasClass('xts-images-comments-lightbox')) {
				return;
			}

			$this.on('click', 'a', function(e) {
				e.preventDefault();
				var index = $(this).data('index');
				var items = getGalleryItems($this, []);

				XTSThemeModule.callPhotoSwipe({
					index       : index,
					items       : items,
					galleryItems: $this,
					parents     : '.xts-col',
					global      : false
				});
			});
		});

		var globalLightBox = function($selector) {
			var globalItems = [];

			$selector.each(function() {
				var $this = $(this);
				var items = getGalleryItems($this, []);

				globalItems = globalItems.concat(items.filter(Boolean));

				$this.on('click', 'a', function(e) {
					e.preventDefault();
					var index = $(this).data('index');

					XTSThemeModule.callPhotoSwipe({
						index       : index,
						items       : globalItems,
						galleryItems: $selector,
						parents     : '.xts-col',
						global      : true
					});
				});
			});
		};

		globalLightBox($('.xts-images-global-lightbox'));
		globalLightBox($('.xts-images-comments-lightbox'));
	};

	$(document).ready(function() {
		XTSThemeModule.imageGalleryElement();
	});
})(jQuery);