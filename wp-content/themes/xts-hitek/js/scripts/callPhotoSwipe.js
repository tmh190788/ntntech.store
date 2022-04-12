/* global xts_settings */
(function($) {
	XTSThemeModule.callPhotoSwipe = function(args) {
		var options = {
			index              : args.index,
			tapToToggleControls: false,
			isClickableElement : function(el) {
				return $(el).hasClass('xts-pswp-gallery') || $(el).parent().hasClass('xts-pswp-gallery') || el.tagName === 'A';
			},
			shareButtons       : [
				{
					id   : 'facebook',
					label: xts_settings.photoswipe_facebook,
					url  : 'https://www.facebook.com/sharer/sharer.php?u={{url}}'
				},
				{
					id   : 'twitter',
					label: xts_settings.photoswipe_twitter,
					url  : 'https://twitter.com/intent/tweet?text={{text}}&url={{url}}'
				},
				{
					id   : 'pinterest',
					label: xts_settings.photoswipe_pinterest,
					url  : 'https://www.pinterest.com/pin/create/button/?url={{url}}&media={{image_url}}&description={{text}}'
				},
				{
					id      : 'download',
					label   : xts_settings.photoswipe_download_image,
					url     : '{{raw_image_url}}',
					download: true
				}
			],
			getThumbBoundsFn   : function(index) {
				if (args.galleryItems.hasClass('xts-carousel')) {
					return;
				}

				var $element = args.galleryItems.find(args.parents).eq(index);

				if (args.global) {
					$element = args.galleryItems.find('a[data-index=' + index + ']').parents(args.parents);
				}

				var pageYScroll = window.pageYOffset || document.documentElement.scrollTop;
				var rect = $element[0].getElementsByTagName('img')[0].getBoundingClientRect();

				return {
					x: rect.left,
					y: rect.top + pageYScroll,
					w: rect.width
				};
			}
		};

		XTSThemeModule.$body.find('.pswp').remove();
		XTSThemeModule.$body.append(xts_settings.photoswipe_template);
		var $pswpElement = document.querySelectorAll('.pswp')[0];
		var $customGallery = $('.xts-pswp-gallery');
		var gallery = new PhotoSwipe($pswpElement, PhotoSwipeUI_Default, args.items, options);

		gallery.init();
		$customGallery.empty();

		if (args.galleryItems.hasClass('xts-lightbox-gallery')) {
			if (args.items.length <= 1) {
				return;
			}

			for (var index = 0; index < args.items.length; index++) {
				$customGallery.append('<img src="' + args.items[index].src + '" data-index="' + (index + 1) + '" alt="image">');
			}

			$customGallery.find('img[data-index="' + (gallery.getCurrentIndex() + 1) + '"]').addClass('xts-active');

			gallery.listen('beforeChange', function() {
				var index = gallery.getCurrentIndex() + 1;
				var $current = $customGallery.find('img[data-index="' + index + '"]');

				$current.siblings().removeClass('xts-active');
				$current.addClass('xts-active');
			});

			$customGallery.find('img').on('click', function() {
				var index = $(this).data('index');
				gallery.goTo(index - 1);
			});
		}
	};
})(jQuery);
