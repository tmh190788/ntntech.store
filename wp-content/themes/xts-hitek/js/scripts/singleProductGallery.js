/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSingleProductGalleryReady xtsImagesLoaded xtsProductQuickViewOpen', function() {
		XTSThemeModule.singleProductGallery();
	});

	XTSThemeModule.$document.on('xtsProductQuickViewOpen', function() {
		XTSThemeModule.singleProductWithoutClickAction();
	});

	XTSThemeModule.singleProductWithoutClickAction = function() {
		$('.xts-single-product-images.xts-action-without').on('click', 'a', function(e) {
			e.preventDefault();
		});
	};

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_product_gallery.default', function() {
		XTSThemeModule.$document.trigger('xtsElementorSingleProductGalleryReady');
	});

	XTSThemeModule.singleProductGallery = function() {
		$('.woocommerce-product-gallery').each(function() {
			var $wrapper = $(this);
			var $mainGallery = $wrapper.find('.xts-single-product-images');
			var $thumbsGallery = $wrapper.find('.xts-single-product-thumb');
			var thumbsGallery;
			var direction = 'horizontal';

			if ($wrapper.hasClass('xts-inited')) {
				return;
			}

			if ($wrapper.hasClass('xts-style-side') && XTSThemeModule.isDesktop) {
				direction = 'vertical';

				// Set thumbs gallery height.
				$thumbsGallery.height($mainGallery.find('.wp-post-image').outerHeight());
			}

			if ('horizontal' === direction && XTSThemeModule.isTabletSize) {
				$wrapper.removeClass('xts-style-side').addClass('xts-style-bottom');
				$thumbsGallery.removeClass('xts-row-spacing-0').addClass('xts-row-spacing-10');
			}

			var initMainGallery = function(){
				var mainGalleryControlsId = $mainGallery.data('controls-id');
				var mainGalleryConfig = {
					slidesPerView: 1,
					watchOverflow: true,
					autoHeight   : 'yes' === xts_settings.single_product_gallery_auto_height,
					navigation   : {
						nextEl       : '.xts-next.xts-id-' + mainGalleryControlsId,
						prevEl       : '.xts-prev.xts-id-' + mainGalleryControlsId,
						disabledClass: 'xts-disabled',
						hiddenClass  : 'xts-hidden',
						lockClass    : 'xts-lock'
					},
					a11y         : {
						enabled: false
					},
					on           : {
						init: function() {
							var $carousel = $(this.$el).parent();

							$carousel.addClass('xts-loaded');

							XTSThemeModule.$document.trigger('xtsElementorSingleProductGallerySwiperInited');

							setTimeout(function() {
								if ($('.xts-id-' + mainGalleryControlsId + '.xts-disabled').length >= 2) {
									$carousel.addClass('xts-controls-disabled');
								}
							});
						}
					}
				};

				if (thumbsGallery) {
					mainGalleryConfig.thumbs = {
						swiper: thumbsGallery
					};
				}

				if ($mainGallery.hasClass('xts-carousel') || XTSThemeModule.isMobileSize) {
					var mySwiper;

					XTSThemeModule.addSwiperStructure($mainGallery, true, false, mainGalleryControlsId);
					$mainGallery.addClass('xts-carousel xts-lib-swiper xts-arrows-hpos-inside xts-arrows-vpos-sides xts-arrows-design-default');

					if ('undefined' === typeof Swiper && 'undefined' !== typeof elementorFrontend) {
						var mainAsyncSwiper = elementorFrontend.utils.swiper;
						new mainAsyncSwiper($mainGallery.find('.swiper-container'), mainGalleryConfig).then(function(newSwiperInstance) {
							mySwiper = newSwiperInstance;
						});
					} else if ( 'undefined' !== typeof Swiper ) {
						mySwiper = new Swiper($mainGallery.find('.swiper-container'), mainGalleryConfig);
					}
				}
			}

			if (($wrapper.hasClass('xts-style-side') || $wrapper.hasClass('xts-style-bottom')) && $thumbsGallery.length > 0) {
				var thumbsControlsId = $thumbsGallery.data('controls-id');

				XTSThemeModule.addSwiperStructure($thumbsGallery, true, false, thumbsControlsId);

				var thumbsGalleryConfig = {
					slidesPerView: $thumbsGallery.data('thumb-count'),
					direction    : direction,
					watchOverflow: true,
					spaceBetween : 'vertical' === direction ? 10 : 0,
					navigation   : {
						nextEl       : '.xts-next.xts-id-' + thumbsControlsId,
						prevEl       : '.xts-prev.xts-id-' + thumbsControlsId,
						disabledClass: 'xts-disabled',
						hiddenClass  : 'xts-hidden',
						lockClass    : 'xts-lock'
					},
					a11y         : {
						enabled: false
					},
					on           : {
						init: function() {
							var $carousel = $(this.$el).parent();

							$carousel.addClass('xts-loaded');

							XTSThemeModule.$document.trigger('xtsElementorSingleProductGallerySwiperInited');

							setTimeout(function() {
								if ($('.xts-id-' + thumbsControlsId + '.xts-disabled').length >= 2) {
									$carousel.addClass('xts-controls-disabled');
								}
							});
						}
					}
				};

				if ('undefined' === typeof Swiper && 'undefined' !== typeof elementorFrontend) {
					var thumbsAsyncSwiper = elementorFrontend.utils.swiper;
					new thumbsAsyncSwiper($thumbsGallery.find('.swiper-container'), thumbsGalleryConfig).then(function(newSwiperInstance) {
						thumbsGallery = newSwiperInstance;
						initMainGallery();
					});
				} else if ( 'undefined' !== typeof Swiper ) {
					thumbsGallery = new Swiper($thumbsGallery.find('.swiper-container'), thumbsGalleryConfig);
					initMainGallery();
				}
			} else {
				initMainGallery();
			}

			$wrapper.addClass('xts-inited');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductGallery();
	});
})(jQuery);