/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsMenuDropdownsAJAXRenderResults xtsProductTabLoaded xtsProductLoadMoreReInit xtsBlogLoadMoreSuccess', function() {
		XTSThemeModule.carouselInitSwiper();
	});

	$.each([
		'frontend/element_ready/xts_product_brands.default',
		'frontend/element_ready/xts_product_categories.default',
		'frontend/element_ready/xts_product_tabs.default',
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default',
		'frontend/element_ready/xts_image_gallery.default',
		'frontend/element_ready/xts_banner_carousel.default',
		'frontend/element_ready/xts_infobox_carousel.default',
		'frontend/element_ready/xts_blog.default',
		'frontend/element_ready/xts_portfolio.default',
		'frontend/element_ready/xts_instagram.default',
		'frontend/element_ready/xts_testimonials.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.carouselInitSwiper();
		});
	});

	XTSThemeModule.carouselInitSwiper = function() {
		if (XTSThemeModule.isTabletSize && 'yes' === xts_settings.disable_carousel_mobile_devices) {
			return;
		}

		function getConfig(data) {
			var config = {
				slidesPerView      : data.carousel_items_mobile.size,
				loop               : 'yes' === data.infinite_loop,
				centeredSlides     : 'yes' === data.center_mode,
				autoHeight         : 'yes' === data.auto_height,
				watchOverflow      : true,
				watchSlidesProgress: true,
				speed              : 400,
				a11y               : {
					enabled: false
				},
				breakpoints        : {
					768 : {
						slidesPerView: data.carousel_items_tablet.size
					},
					1025: {
						slidesPerView: data.carousel_items.size
					}
				},
				on                 : {
					init: function() {
						var $carousel = $(this.$el).parent();

						$carousel.addClass('xts-loaded');

						setTimeout(function() {
							if ($('.xts-id-' + data.controls_id + '.xts-disabled').length >= 2) {
								$carousel.addClass('xts-controls-disabled');
							}
						});
					}
				}
			};

			if ('yes' === data.dots) {
				config.pagination = {
					el                : '.xts-nav-pagination.xts-id-' + data.controls_id,
					type              : 'bullets',
					clickable         : true,
					bulletClass       : 'xts-nav-pagination-item',
					bulletActiveClass : 'xts-active',
					modifierClass     : 'xts-type-',
					lockClass         : 'xts-lock',
					dynamicBullets    : true,
					dynamicMainBullets: 1,
					renderBullet      : function(index, className) {
						return '<li class="' + className + '"></li>';
					}
				};
			}

			if ('yes' === data.arrows) {
				config.navigation = {
					nextEl       : '.xts-next.xts-id-' + data.controls_id,
					prevEl       : '.xts-prev.xts-id-' + data.controls_id,
					disabledClass: 'xts-disabled',
					lockClass    : 'xts-lock',
					hiddenClass  : 'xts-hidden'
				};
			}

			if ('yes' === data.autoplay) {
				config.autoplay = {
					delay: data.autoplay_speed.size
				};
			}

			return config;
		}

		/**
		 * Thumbnails gallery.
		 */
		function carouselInitGallery($this) {
			var thumbsGallery;
			var $parent = $this.parent();
			var $thumbsGallery = $parent.find('.xts-carousel.xts-lib-swiper.xts-gallery-thumbs');

			if ($thumbsGallery.length > 0) {
				var data = $thumbsGallery.data('carousel-args');

				if ('undefined' == typeof data || $thumbsGallery.hasClass('xts-inited')) {
					return;
				}

				$thumbsGallery.addClass('xts-inited');

				var config = getConfig(data);

				XTSThemeModule.addSwiperStructure($thumbsGallery, 'yes' === data.arrows, 'yes' === data.dots, data.controls_id);

				if ('undefined' === typeof Swiper) {
					var thumbsAsyncSwiper = elementorFrontend.utils.swiper;
					new thumbsAsyncSwiper($thumbsGallery.find('.swiper-container'), config).then(function(newSwiperInstance) {
						thumbsGallery = newSwiperInstance;

						carouselInitMain($this, thumbsGallery);
					});
				} else {
					thumbsGallery = new Swiper($thumbsGallery.find('.swiper-container'), config);

					carouselInitMain($this, thumbsGallery);
				}
			}

			return thumbsGallery;
		}

		/**
		 * Main gallery.
		 */
		if (typeof ($.fn.xtsWaypoint) !== 'undefined') {
			$('.xts-carousel.xts-lib-swiper.xts-init-on-scroll:not(.xts-gallery-thumbs):not([data-sync])').xtsWaypoint(function() {
				var $this = $($(this)[0].element);
				carouselInitGallery($this);

				if (!$this.hasClass('xts-with-thumbs')) {
					carouselInitMain($this);
				}
			}, {
				offset: '100%'
			});
		}

		$('.xts-carousel.xts-lib-swiper:not(.xts-gallery-thumbs):not([data-sync]):not(.xts-init-on-scroll)').each(function() {
			var $this = $(this);
			carouselInitGallery($this);

			if (!$this.hasClass('xts-with-thumbs')) {
				carouselInitMain($this);
			}
		});

		function carouselInitMain($this, thumbsGallery) {
			var data = $this.data('carousel-args');
			var mainSwiper;

			if ('undefined' == typeof data || $this.hasClass('xts-inited')) {
				return;
			}

			// Fix for unnecessary carousel init.
			if ($this.find('.xts-col').length <= data.carousel_items.size && XTSThemeModule.isDesktop) {
				return;
			}

			$this.addClass('xts-inited');

			var config = getConfig(data);

			if ($this.hasClass('xts-with-thumbs') && thumbsGallery) {
				config.thumbs = {
					swiper: thumbsGallery
				};
			}

			XTSThemeModule.addSwiperStructure($this, 'yes' === data.arrows, 'yes' === data.dots, data.controls_id);

			if ('undefined' === typeof Swiper) {
				var mainAsyncSwiper = elementorFrontend.utils.swiper;

				new mainAsyncSwiper($this.find(' > .swiper-container'), config).then(function(newSwiperInstance) {
					mainSwiper = newSwiperInstance;
				});
			} else {
				mainSwiper = new Swiper($this.find(' > .swiper-container'), config);
			}

			// Custom post gallery navigations
			if ($this.hasClass('xts-post-gallery')) {
				var $post = $this.parents('.format-gallery');

				$post.find('.xts-post-control.xts-prev').on('click', function() {
					mainSwiper.slidePrev();
				});

				$post.find('.xts-post-control.xts-next').on('click', function() {
					mainSwiper.slideNext();
				});
			}

			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		}

		/**
		 * Synchronized gallery.
		 */
		if (typeof ($.fn.xtsWaypoint) !== 'undefined') {
			$('.xts-carousel.xts-lib-swiper.xts-init-on-scroll[data-sync="child"]').xtsWaypoint(function() {
				carouselInitChild($($(this)[0].element));
			}, {
				offset: '100%'
			});
		}

		$('.xts-carousel.xts-lib-swiper[data-sync="child"]:not(.xts-init-on-scroll)').each(function() {
			carouselInitChild($(this));
		});

		function carouselInitChild($this) {
			var data = $this.data('carousel-args');

			if ('undefined' == typeof data || $this.hasClass('xts-inited')) {
				return;
			}

			$this.addClass('xts-inited');

			var config = getConfig(data);
			var childSwiper;

			XTSThemeModule.addSwiperStructure($this, 'yes' === data.arrows, 'yes' === data.dots, data.controls_id);

			if ('undefined' === typeof Swiper) {
				var childAsyncSwiper = elementorFrontend.utils.swiper;
				new childAsyncSwiper($this.find(' > .swiper-container'), config).then(function(newSwiperInstance) {
					childSwiper = newSwiperInstance;

					carouselInitParent($('.xts-carousel.xts-lib-swiper[data-sync="parent"][data-sync-id="' + $this.data('sync-id') + '"]'));
				});
			} else {
				childSwiper = new Swiper($this.find(' > .swiper-container'), config);

				carouselInitParent($('.xts-carousel.xts-lib-swiper[data-sync="parent"][data-sync-id="' + $this.data('sync-id') + '"]'));
			}

			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		}

		function carouselInitParent($this) {
			var data = $this.data('carousel-args');
			var syncId = $this.data('sync-id');
			var parent;

			if ('undefined' == typeof data || $this.hasClass('xts-inited')) {
				return;
			}

			$this.addClass('xts-inited');

			var config = getConfig(data);

			if (syncId) {
				var $thumbs = $('.xts-carousel[data-sync-id="' + syncId + '"][data-sync="child"] > .swiper-container');

				if ($thumbs.parent().hasClass('xts-loaded')) {
					config.thumbs = {
						swiper: $thumbs[0].swiper
					};
				}
			}

			XTSThemeModule.addSwiperStructure($this, 'yes' === data.arrows, 'yes' === data.dots, data.controls_id);

			if ('undefined' === typeof Swiper) {
				var parentAsyncSwiper = elementorFrontend.utils.swiper;

				new parentAsyncSwiper($this.find(' > .swiper-container'), config).then(function(newSwiperInstance) {
					parent = newSwiperInstance;
				});
			} else {
				parent = new Swiper($this.find(' > .swiper-container'), config);
			}

			XTSThemeModule.$document.trigger('xtsImagesLoaded');

			if (syncId && $thumbs.parent().hasClass('xts-loaded')) {
				var thumbsData = $thumbs.parent().data('carousel-args');

				if (thumbsData.carousel_items.size <= 1) {
					$thumbs[0].swiper.on('slideChange', function() {
						parent.slideTo($thumbs[0].swiper.realIndex);
					});
				}

				$thumbs[0].swiper.update();
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.carouselInitSwiper();
	});
})(jQuery);