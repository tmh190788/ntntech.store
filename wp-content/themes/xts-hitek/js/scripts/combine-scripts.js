var XTSThemeModule = {};
/* global xts_settings */

(function($) {
	XTSThemeModule.supports_html5_storage = false;

	try {
		XTSThemeModule.supports_html5_storage = ('sessionStorage' in window && window.sessionStorage !== null);
		window.sessionStorage.setItem('xts', 'test');
		window.sessionStorage.removeItem('xts');
	}
	catch (err) {
		XTSThemeModule.supports_html5_storage = false;
	}

	XTSThemeModule.isTablet = function() {
		return XTSThemeModule.$window.width() <= 1024;
	};

	XTSThemeModule.isMobile = function() {
		return XTSThemeModule.$window.width() <= 767;
	};

	XTSThemeModule.removeURLParameter = function(url, parameter) {
		var urlParts = url.split('?');

		if (urlParts.length >= 2) {

			var prefix = encodeURIComponent(parameter) + '=';
			var pars = urlParts[1].split(/[&;]/g);

			for (var i = pars.length; i-- > 0;) {
				if (pars[i].lastIndexOf(prefix, 0) !== -1) {
					pars.splice(i, 1);
				}
			}

			return urlParts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
		}

		return url;
	};

	XTSThemeModule.debounce = function(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this;
			var args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) {
					func.apply(context, args);
				}
			};
			var callNow = immediate && !timeout;

			clearTimeout(timeout);
			timeout = setTimeout(later, wait);

			if (callNow) {
				func.apply(context, args);
			}
		};
	};

	XTSThemeModule.$window = $(window);

	XTSThemeModule.$document = $(document);

	XTSThemeModule.$body = $('body');

	XTSThemeModule.windowWidth = XTSThemeModule.$window.width();

	XTSThemeModule.isDesktop = XTSThemeModule.windowWidth > 1024;

	XTSThemeModule.isTabletSize = XTSThemeModule.windowWidth <= 1024;

	XTSThemeModule.isMobileSize = XTSThemeModule.windowWidth <= 767;

	XTSThemeModule.isSuperMobile = XTSThemeModule.windowWidth <= 575;

	XTSThemeModule.xtsElementorAddAction = function(name, callback) {
		XTSThemeModule.$window.on('elementor/frontend/init', function() {
			if (!elementorFrontend.isEditMode()) {
				return;
			}

			elementorFrontend.hooks.addAction(name, callback);
		});
	};

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/section', function($wrapper) {
		$wrapper.removeClass('xts-animated');
		$wrapper.data('xts-waypoint', '');
		$wrapper.removeClass('xts-anim-ready');
		XTSThemeModule.$document.trigger('xtsElementorSectionReady');
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/global', function($wrapper) {
		if ($wrapper.attr('style') && $wrapper.attr('style').indexOf('transform:translate3d') === 0 && !$wrapper.hasClass('xts-parallax-on-scroll')) {
			$wrapper.attr('style', '');
		}

		$wrapper.removeClass('xts-animated');
		$wrapper.data('xts-waypoint', '');
		$wrapper.removeClass('xts-anim-ready');
		XTSThemeModule.$document.trigger('xtsElementorGlobalReady');
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/column', function($wrapper) {
		if ($wrapper.attr('style') && $wrapper.attr('style').indexOf('transform:translate3d') === 0 && !$wrapper.hasClass('xts-parallax-on-scroll')) {
			$wrapper.attr('style', '');
		}

		$wrapper.removeClass('xts-animated');
		$wrapper.data('xts-waypoint', '');
		$wrapper.removeClass('xts-anim-ready');
		XTSThemeModule.$document.trigger('xtsElementorColumnReady');
	});

	XTSThemeModule.$document.ready(function() {
		if (typeof ($.fn.elementorWaypoint) !== 'undefined') {
			$.fn.xtsWaypoint = $.fn.elementorWaypoint;
		} else if (typeof ($.fn.waypoint) !== 'undefined') {
			$.fn.xtsWaypoint = $.fn.waypoint;
		}
	});

	XTSThemeModule.$window.on('elementor/frontend/init', function() {
		if (!elementorFrontend.isEditMode()) {
			return;
		}

		if ('enabled' === xts_settings.elementor_no_gap) {
			elementorFrontend.hooks.addAction('frontend/element_ready/section', function($wrapper, $) {
				var cid = $wrapper.data('model-cid');

				if (typeof elementorFrontend.config.elements.data[cid] !== 'undefined') {
					var size = elementorFrontend.config.elements.data[cid].attributes.content_width.size;

					if (!size) {
						$wrapper.addClass('xts-negative-gap');
					}
				}
			});

			elementor.channels.editor.on('change:section', function(view) {
				var changed = view.elementSettingsModel.changed;

				if (typeof changed.content_width !== 'undefined') {
					var sectionId = view._parent.model.id;
					var $section = $('.elementor-element-' + sectionId);
					var size = changed.content_width.size;

					if (size) {
						$section.removeClass('xts-negative-gap');
					} else {
						$section.addClass('xts-negative-gap');
					}
				}
			});
		}
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.clickOnScrollButton = function(btnClass) {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		var $btn = $(btnClass);

		if ($btn.length <= 0) {
			return;
		}

		$btn.trigger('xtsWaypointDestroy');

		var waypoint = $btn.xtsWaypoint({
			handler: function() {
				$btn.trigger('click');
			},
			offset : function() {
				return XTSThemeModule.$window.outerHeight();
			}
		});

		$btn.data('waypoint-inited', true).off('xtsWaypointDestroy').on('xtsWaypointDestroy', function() {
			if ($btn.data('waypoint-inited')) {
				waypoint[0].destroy();
				$btn.data('waypoint-inited', false)
			}
		});
	};
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSliderReady xtsPjaxComplete', function() {
		XTSThemeModule.carouselInitFlickity();
	});

	XTSThemeModule.carouselInitFlickity = function() {
		$('.xts-slider').each(function() {
			var $carousel = $(this);
			var data = $carousel.data('carousel-args');

			if (!data) {
				return;
			}

			var config = {
				contain             : 'yes' !== data.center_mode,
				percentPosition     : true,
				cellAlign           : 'yes' === data.center_mode ? 'center' : 'left',
				rightToLeft         : XTSThemeModule.$body.hasClass('rtl'),
				prevNextButtons     : 'yes' === data.arrows,
				pageDots            : 'yes' === data.dots,
				wrapAround          : 'yes' === data.infinite_loop,
				autoPlay            : 'yes' !== data.autoplay ? false : parseInt(data.autoplay_speed.size),
				pauseAutoPlayOnHover: 'yes' === data.autoplay,
				adaptiveHeight      : 'yes' === data.auto_height,
				groupCells          : 'yes' !== data.center_mode,
				draggable           : 'yes' === data.draggable ? '>1' : false,
				imagesLoaded        : true,
				fade                : $carousel.hasClass('xts-anim-fade'),
				on                  : {
					ready: function() {
						if ($carousel.hasClass('xts-arrows-style-text')) {
							$carousel.find('> .flickity-button.next').append('<span>' + xts_settings.flickity_slider_element_next_text + '</span>');
							$carousel.find('> .flickity-button.previous').append('<span>' + xts_settings.flickity_slider_element_previous_text + '</span>');
						}

						$carousel.find('> .flickity-button').wrapAll('<div class="flickity-buttons"></div>');
					}
				}
			};

			$carousel.flickity(config);

			if ($carousel.hasClass('xts-anim-parallax')) {
				var flkty = $carousel.data('flickity');
				var $imgs = $('.xts-slide .xts-slide-bg');

				$carousel.on('scroll.flickity', function() {
					flkty.slides.forEach(function(e, i) {
						var img = $imgs[i];

						var x = 0 === i
							? Math.abs(flkty.x) > flkty.slidesWidth
								? flkty.slidesWidth + flkty.x + flkty.slides[flkty.slides.length - 1].outerWidth + e.target
								: e.target + flkty.x
							: i === flkty.slides.length - 1 && Math.abs(flkty.x) + flkty.slides[i].outerWidth < flkty.slidesWidth
								? e.target - flkty.slidesWidth + flkty.x - flkty.slides[i].outerWidth
								: e.target + flkty.x;

						img.style.transform = 'translateX( ' + -.5 * x + 'px)';
					});
				});
			}

			setTimeout(function() {
				$carousel.addClass('xts-enabled');
			}, 100);

			$carousel.on('dragStart.flickity', function() {
				$carousel.addClass('xts-dragging');
			});

			$carousel.on('dragEnd.flickity', function() {
				$carousel.removeClass('xts-dragging');
			});

			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.carouselInitFlickity();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete xtsPostVideoLoaded xtsBlogLoadMoreSuccess', function() {
		XTSThemeModule.calcVideoSize();
	});

	$.each([
		'frontend/element_ready/xts_blog.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.calcVideoSize();
		});
	});

	XTSThemeModule.calcVideoSize = function() {
		$('.xts-video-resize').each(function() {
			var $this = $(this);
			var $video = $this.find('iframe');

			if ($video.length <= 0) {
				return;
			}

			var containerWidth = $this.outerWidth() + 5;
			var containerHeight = $this.outerHeight() + 5;
			var aspectRatioSetting = '16:9';

			var aspectRatioArray = aspectRatioSetting.split(':');
			var aspectRatio = aspectRatioArray[0] / aspectRatioArray[1];
			var ratioWidth = containerWidth / aspectRatio;
			var ratioHeight = containerHeight * aspectRatio;
			var isWidthFixed = containerWidth / containerHeight > aspectRatio;

			var size = {
				width : isWidthFixed ? containerWidth : ratioHeight,
				height: isWidthFixed ? ratioWidth : containerHeight
			};

			$video.width(size.width).height(size.height + 140);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.calcVideoSize();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete xtsPortfolioPjaxComplete xtsElementorProductTabsReady xtsPortfolioPjaxComplete', function() {
		XTSThemeModule.masonryLayout();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default',
		'frontend/element_ready/xts_image_gallery.default',
		'frontend/element_ready/xts_blog.default',
		'frontend/element_ready/xts_portfolio.default',
		'frontend/element_ready/xts_instagram.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.masonryLayout();
		});
	});

	XTSThemeModule.masonryLayout = function() {
		$('.xts-masonry-layout:not(.xts-carousel)').each(function() {
			var $this = $(this);
			var columnWidth = $this.hasClass('xts-different-images') || $this.hasClass('xts-different-sizes') ? '.xts-col:not(.xts-wide):not(.swiper-slide)' : '.xts-col:not(.swiper-slide)';
			$this.imagesLoaded(function() {
				var config = {
					resizable   : false,
					isOriginLeft: !XTSThemeModule.$body.hasClass('rtl'),
					layoutMode  : 'packery',
					packery     : {
						gutter     : 0,
						columnWidth: columnWidth
					},
					itemSelector: '.xts-col:not(.xts-post-gallery-col)'
				};

				if ($this.hasClass('xts-in-view-animation')) {
					config.transitionDuration = 0;
				}

				$this.isotope(config);
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.masonryLayout();
	});
})(jQuery);
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

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.ajaxSearch();
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_ajax_search.default', function() {
		XTSThemeModule.ajaxSearch();
	});

	XTSThemeModule.ajaxSearch = function() {
		if (typeof ($.fn.devbridgeAutocomplete) == 'undefined') {
			return;
		}

		var escapeRegExChars = function(value) {
			return value.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
		};

		$('form.xts-ajax-search').each(function() {
			var $this                 = $(this),
			    number                = parseInt($this.data('count')),
			    thumbnail             = parseInt($this.data('thumbnail')),
			    $results              = $this.parents('.xts-search-wrapper').find('.xts-search-results'),
			    postType              = $this.data('post_type'),
			    url                   = xts_settings.ajaxurl + '?action=xts_ajax_search',
			    symbols_count         = parseInt($this.data('symbols_count')),
			    productCat            = $this.find('[name="product_cat"]'),
			    sku                   = $this.data('sku'),
			    categories_on_results = $this.data('categories_on_results'),
			    price                 = parseInt($this.data('price')),
			    $resultsClasses       = $results;

			// Juno.
			if ($this.parents('.xts-search-wrapper').hasClass('xts-design-widgets xts-search-full-screen')) {
				$resultsClasses = $this.parents('.xts-search-wrapper').find('.xts-search-footer');
			}

			// Neptune.
			if ($this.parents('.xts-search-wrapper').find('.xts-shape-overlays').length > 0) {
				$resultsClasses = $this.parents('.xts-search-wrapper').find('.xts-search-results-wrapper');
			}

			if (number > 0) {
				url += '&number=' + number;
			}

			url += '&post_type=' + postType;

			if (productCat.length && productCat.val() !== '') {
				url += '&product_cat=' + productCat.val();
			}

			$results.on('click', '.xts-search-results-btn', function() {
				$this.submit();
			});

			$this.find('[type="text"]').on('focus', function() {
				var $input = $(this);

				if ($input.hasClass('xts-search-inited')) {
					return;
				}

				$input.devbridgeAutocomplete({
					serviceUrl      : url,
					appendTo        : $results.hasClass('xts-dropdown') ? $results.find('.xts-dropdown-inner') : $results,
					minChars        : symbols_count,
					onSelect        : function(suggestion) {
						if (suggestion.permalink.length > 0) {
							window.location.href = suggestion.permalink;
						}
					},
					onHide          : function() {
						$resultsClasses.removeClass('xts-opened');
						$resultsClasses.removeClass('xts-no-results');
					},
					onSearchStart   : function() {
						$this.addClass('search-loading');
					},
					beforeRender    : function(container) {
						$(container).find('.suggestion-divider-title').parent().addClass('suggestion-divider');
						$(container).find('.xts-search-no-found').parent().addClass('suggestion-no-found');
						if (container[0].childElementCount > 2) {
							$(container).append('<div class="xts-search-results-btn">' + xts_settings.all_results + '</div>');
						}
						$(container).removeAttr('style');
					},
					onSearchComplete: function() {
						$this.removeClass('search-loading');
						XTSThemeModule.$document.trigger('xtsImagesLoaded');
					},
					formatResult    : function(suggestion, currentValue) {
						if ('&' === currentValue) {
							currentValue = '&#038;';
						}

						var pattern = '(' + escapeRegExChars(currentValue) + ')';
						var returnValue = '';

						if (suggestion.divider) {
							returnValue += ' <h5 class="suggestion-divider-title">' + suggestion.divider + '</h5>';
						}

						if (thumbnail && suggestion.thumbnail) {
							returnValue += ' <div class="suggestion-thumb">' + suggestion.thumbnail + '</div>';
						}

						if (suggestion.value) {
							returnValue += ' <div class="suggestion-content">';
							returnValue += '<h4 class="suggestion-title xts-entities-title">' + suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>').replace(/&lt;(\/?strong)&gt;/g, '<$1>') + '</h4>';
						}

						if ('yes' === categories_on_results && suggestion.categories) {
							returnValue += ' <div class="suggestion-cat suggestion-meta">' + suggestion.categories + '</div>';
						}

						if ('yes' === sku && suggestion.sku) {
							returnValue += ' <div class="suggestion-sku suggestion-meta">' + suggestion.sku + '</div>';
						}

						if (price && suggestion.price) {
							returnValue += ' <div class="price">' + suggestion.price + '</div>';
						}

						if (suggestion.value) {
							returnValue += ' </div>';
						}

						if (suggestion.no_found) {
							$resultsClasses.addClass('xts-no-results');
							returnValue = '<div class="xts-search-no-found">' + suggestion.value + '</div>';
						} else {
							$resultsClasses.removeClass('xts-no-results');
						}

						$resultsClasses.addClass('xts-opened');
						$resultsClasses.addClass('xts-searched');

						return returnValue;
					}
				});

				if (productCat.length) {
					var searchForm = $this.find('[type="text"]').devbridgeAutocomplete(),
					    serviceUrl = xts_settings.ajaxurl + '?action=xts_ajax_search';

					if (number > 0) {
						serviceUrl += '&number=' + number;
					}

					serviceUrl += '&post_type=' + postType;

					productCat.on('cat_selected', function() {
						if ('' !== productCat.val()) {
							searchForm.setOptions({
								serviceUrl: serviceUrl + '&product_cat=' + productCat.val()
							});
						} else {
							searchForm.setOptions({
								serviceUrl: serviceUrl
							});
						}

						searchForm.hide();
						searchForm.onValueChange();
					});
				}

				$input.addClass('xts-search-inited');
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if (!$(target).is('.xts-search-form') && !$(target).parents().is('.xts-search-form')) {
					$this.find('[type="text"]').devbridgeAutocomplete('hide');
				}
			});

			$('.xts-search-results').on('click', function(e) {
				e.stopPropagation();
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.ajaxSearch();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSectionReady xtsElementorColumnReady xtsElementorGlobalReady', function() {
		XTSThemeModule.animations();
	});

	XTSThemeModule.animations = function() {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		$('[class*="xts-animation"]').each(function() {
			var $element = $(this);

			if ('inited' === $element.data('xts-waypoint') || $element.parents('.xts-autoplay-animations-off').length > 0) {
				return;
			}

			$element.data('xts-waypoint', 'inited');

			$element.xtsWaypoint(function() {
				var $this = $($(this)[0].element);

				var classes = $this.attr('class').split(' ');
				var delay = 0;

				for (var index = 0; index < classes.length; index++) {
					if (classes[index].indexOf('xts_delay_') >= 0) {
						delay = classes[index].split('_')[2];
					}
				}

				$this.addClass('xts-animation-ready');

				setTimeout(function() {
					$this.addClass('xts-animated');
				}, delay);
			}, {
				offset: '90%'
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.animations();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsProductTabLoaded xtsProductLoadMoreReInit xtsPjaxComplete xtsPortfolioLoadMoreSuccess xtsBlogLoadMoreSuccess xtsWishlistRemoveSuccess', function() {
		XTSThemeModule.itemsAnimationInView();
	});

	XTSThemeModule.$document.on('xtsPortfolioPjaxComplete', function() {
		setTimeout(function() {
			XTSThemeModule.itemsAnimationInView();
		}, 100);
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
		'frontend/element_ready/xts_testimonials.default',
		'frontend/element_ready/xts_title.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.itemsAnimationInView();
		});
	});

	XTSThemeModule.itemsAnimationInView = function() {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		$('.xts-in-view-animation').each(function() {
			var itemQueue = [];
			var queueTimer;
			var $wrapper = $(this);

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

			$wrapper.find('.xts-col, .xts-animation-item').each(function() {
				var $element = $(this);

				if ('inited' === $element.data('xts-waypoint')) {
					return;
				}

				$element.data('xts-waypoint', 'inited');

				$element.xtsWaypoint(function() {
					var $this = $($(this)[0].element);
					var delay = $this.parents('.xts-in-view-animation').data('animation-delay');

					$this.addClass('xts-animation-ready');

					itemQueue.push($this);
					processItemQueue(delay);
				}, {
					offset: '90%'
				});
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.itemsAnimationInView();
	});
})(jQuery);
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
/* global xts_settings */
(function($) {
	XTSThemeModule.addSwiperStructure = function($slider, navigation, pagination, controlsId) {
		if (0 === $slider.find('> .swiper-wrapper').length) {
			$slider.wrapInner('<div class="swiper-wrapper"/>');
		}

		if (0 === $slider.find('> .swiper-container').length) {
			$slider.wrapInner('<div class="swiper-container"/>');
		}

		$slider.find('.xts-col').addClass('swiper-slide');

		if (navigation && 0 === $slider.find('> .xts-nav-arrows').length) {
			$slider.find('> .swiper-container').after('<div class="xts-nav-arrows"><div class="xts-nav-arrow xts-prev xts-id-' + controlsId + '"><div class="xts-arrow-inner"></div></div><div class="xts-nav-arrow xts-next xts-id-' + controlsId + '"><div class="xts-arrow-inner"></div></div></div>');
		}

		if (pagination && 0 === $slider.find('> .xts-nav-pagination').length) {
			$slider.find('> .swiper-container').after('<ol class="xts-nav-pagination xts-id-' + controlsId + '"></ol>');
		}
	};
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.cookiesPopup = function() {
		if ('undefined' === typeof Cookies) {
			return;
		}

		var cookies_version = xts_settings.cookies_version;
		if ('accepted' === Cookies.get('xts_cookies_' + cookies_version)) {
			return;
		}

		var $cookies = $('.xts-cookies');

		setTimeout(function() {
			$cookies.addClass('xts-show');
			$cookies.on('click', '.xts-cookies-accept-btn', function(e) {
				e.preventDefault();
				acceptCookies();
			});
		}, 2500);

		var acceptCookies = function() {
			$cookies.removeClass('xts-show');
			Cookies.set('xts_cookies_' + cookies_version, 'accepted', {
				expires: parseInt(xts_settings.cookies_expires),
				path   : '/'
			});
		};
	};

	$(document).ready(function() {
		XTSThemeModule.cookiesPopup();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.headerBanner = function() {
		if ('undefined' === typeof Cookies) {
			return;
		}

		var banner_version = xts_settings.header_banner_version;
		var $banner = $('.xts-header-banner');

		if ('closed' === Cookies.get('xts_header_banner_' + banner_version) || 'no' === xts_settings.header_banner_close_button || 'no' === xts_settings.header_banner) {
			return;
		}

		if (!XTSThemeModule.$body.hasClass('page-template-maintenance')) {
			$banner.addClass('xts-display');
		}

		$banner.on('click', '.xts-header-banner-close', function(e) {
			e.preventDefault();
			closeBanner();
		});

		var closeBanner = function() {
			$banner.removeClass('xts-display').addClass('xts-hide');
			Cookies.set('xts_header_banner_' + banner_version, 'closed', {
				expires: parseInt(xts_settings.cookies_expires),
				path   : '/'
			});
		};
	};

	$(document).ready(function() {
		XTSThemeModule.headerBanner();
	});
})(jQuery);

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

/* global xts_settings */
(function($) {
	XTSThemeModule.hideNotices = function() {
		var notices = '.woocommerce-error, .woocommerce-info, .woocommerce-message, .wpcf7-response-output, .mc4wp-alert';

		XTSThemeModule.$body.on('click', notices, function(e) {
			var noticeItem   = $(this),
			    noticeHeight = noticeItem.outerHeight();

			if ('a' !== $(e.target).prop('tagName').toLowerCase()) {
				noticeItem.css('height', noticeHeight);

				setTimeout(function() {
					noticeItem.addClass('xts-hide');
				}, 100);
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.hideNotices();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	$.each([
		'frontend/element_ready/xts_product_brands.default',
		'frontend/element_ready/xts_product_categories.default',
		'frontend/element_ready/xts_product_tabs.default',
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default',
		'frontend/element_ready/xts_image.default',
		'frontend/element_ready/xts_image_gallery.default',
		'frontend/element_ready/xts_banner_carousel.default',
		'frontend/element_ready/xts_banner.default',
		'frontend/element_ready/xts_infobox.default',
		'frontend/element_ready/xts_infobox_carousel.default',
		'frontend/element_ready/xts_blog.default',
		'frontend/element_ready/xts_portfolio.default',
		'frontend/element_ready/xts_instagram.default',
		'frontend/element_ready/xts_testimonials.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.lazyLoading();
		});
	});

	XTSThemeModule.lazyLoading = function() {
		if (!window.addEventListener || !window.requestAnimationFrame || !document.getElementsByClassName) {
			return;
		}

		// start
		var pItem = document.getElementsByClassName('xts-lazy-load'), pCount, timer;

		XTSThemeModule.$document.on('xtsImagesLoaded added_to_cart', function() {
			inView();
		});

		$('.xts-scroll-content, .xts-sidebar-content').on('scroll', function() {
			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		});

		// WooCommerce tabs fix
		$('.wc-tabs > li').on('click', function() {
			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		});

		// scroll and resize events
		window.addEventListener('scroll', scroller, false);
		window.addEventListener('resize', scroller, false);

		// DOM mutation observer
		if (MutationObserver) {
			var observer = new MutationObserver(function() {
				// console.log('mutated', pItem.length, pCount)
				if (pItem.length !== pCount) {
					inView();
				}
			});

			observer.observe(document.body, {
				subtree      : true,
				childList    : true,
				attributes   : true,
				characterData: true
			});
		}

		// initial check
		inView();

		// throttled scroll/resize
		function scroller() {
			timer = timer || setTimeout(function() {
				timer = null;
				inView();
			}, 100);
		}

		// image in view?
		function inView() {
			if (pItem.length) {
				requestAnimationFrame(function() {
					var offset = parseInt(xts_settings.lazy_loading_offset);
					var wT = window.pageYOffset, wB = wT + window.innerHeight + offset, cRect, pT, pB, p = 0;

					while (p < pItem.length) {
						cRect = pItem[p].getBoundingClientRect();
						pT = wT + cRect.top;
						pB = pT + cRect.height;

						if (wT < pB && wB > pT && !pItem[p].loaded) {
							loadFullImage(pItem[p]);
						} else {
							p++;
						}
					}
					pCount = pItem.length;
				});
			}
		}

		// replace with full image
		function loadFullImage(item) {
			item.onload = addedImg;

			item.src = item.dataset.xtsSrc;
			if (typeof (item.dataset.srcset) != 'undefined') {
				item.srcset = item.dataset.srcset;
			}

			item.loaded = true;

			// replace image
			function addedImg() {
				requestAnimationFrame(function() {
					item.classList.add('xts-loaded');

					// Reload flickity
					// $('div[data-xts-carousel].flickity-enabled').flickity('reloadCells');

					var $masonry = jQuery(item).parents('.xts-masonry-layout');
					if ($masonry.length > 0) {
						$masonry.isotope('layout');
					}
				});
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.lazyLoading();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.moreCategoriesButton = function () {
		$('.xts-more-cats').each(function () {
			var $wrapper = $(this);

			$wrapper.find('.xts-more-cats-btn a').on('click', function (e) {
				e.preventDefault();
				$wrapper.addClass('xts-more-cats-visible');
				$(this).parent().remove();
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.moreCategoriesButton();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete xtsPortfolioPjaxComplete', function () {
		XTSThemeModule.offCanvasSidebar();
	});

	XTSThemeModule.$document.on('xtsPortfolioPjaxStart xtsPjaxStart', function () {
		XTSThemeModule.hideOffCanvasSidebar();
	});

	XTSThemeModule.offCanvasSidebar = function () {
		var $closeSide = $('.xts-close-side');
		var $sidebar = $('.xts-sidebar');
		var $body = XTSThemeModule.$body;

		if ($sidebar.hasClass('xts-sidebar-hidden-lg') && XTSThemeModule.isDesktop || $sidebar.hasClass('xts-sidebar-hidden-md') && XTSThemeModule.isTabletSize) {
			$sidebar.addClass('xts-inited');
		}

		$body.on('click', '.xts-sidebar-opener, .xts-navbar-sidebar', function (e) {
			e.preventDefault();

			if ($sidebar.hasClass('xts-opened')) {
				XTSThemeModule.hideOffCanvasSidebar();
			} else {
				showSidebar();
			}
		});

		$body.on('click touchstart', '.xts-close-side, .xts-close-button', function () {
			XTSThemeModule.hideOffCanvasSidebar();
		});

		XTSThemeModule.$document.keyup(function (e) {
			if (27 === e.keyCode) {
				XTSThemeModule.hideOffCanvasSidebar();
			}
		});

		var showSidebar = function () {
			$sidebar.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
		};
	};

	XTSThemeModule.hideOffCanvasSidebar = function () {
		$('.xts-sidebar').removeClass('xts-opened');
		$('.xts-close-side').removeClass('xts-opened');
	};

	$(document).ready(function() {
		XTSThemeModule.offCanvasSidebar();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.pageTitleEffect = function() {
		var $pageTitle   = $('.xts-parallax-scroll'),
		    lastMoveTime = 0,
		    frameTime    = 10;

		if ($pageTitle.length < 1) {
			return;
		}

		var $inner  = $pageTitle.find('.container'),
		    $bg     = $pageTitle.find('.xts-page-title-overlay'),
		    $window = XTSThemeModule.$window;

		XTSThemeModule.$document.on('scroll', function() {
			var now    = Date.now(),
			    height = $pageTitle.outerHeight(),
			    top    = $pageTitle.offset().top,
			    bottom = height + top,
			    scroll = $window.scrollTop();

			if (now < lastMoveTime + frameTime || scroll > bottom) {
				return;
			}

			lastMoveTime = now;

			var translateY = scroll / 5,
			    opacity    = 1 - 0.9 * scroll / bottom,
			    scale      = 1 + 0.1 * scroll / bottom;

			window.requestAnimationFrame(function() {
				$inner.css({
					transform: 'translateY(' + translateY + 'px)',
					opacity  : opacity
				});

				$bg.css({
					transform      : 'translateY(' + translateY / 2 + 'px) scale(' + scale + ', ' + scale + ')',
					transformOrigin: 'top'
				});
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.pageTitleEffect();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPortfolioLoadMoreSuccess', function() {
		XTSThemeModule.parallax3d();
	});

	$.each([
		'frontend/element_ready/xts_banner_carousel.default',
		'frontend/element_ready/xts_banner.default',
		'frontend/element_ready/xts_portfolio.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.parallax3d();
		});
	});

	XTSThemeModule.parallax3d = function() {
		var $elements    = $('.xts-hover-parallax, .xts-portfolio-design-parallax .xts-project'),
		    lastMoveTime = 0,
		    frameTime    = 30;

		$elements.each(function() {
			var $el = $(this);

			if ($el.hasClass('xts-parallax3d-init')) {
				return;
			}

			$el.addClass('xts-parallax3d-init');

			$el.on('mousemove', function(e) {
				var now = Date.now();

				if (now < lastMoveTime + frameTime) {
					return;
				}

				lastMoveTime = now;

				var $el         = $(this),
				    width       = $el.outerWidth(),
				    elMouseXRel = (e.pageX - $el.offset().left) / width,
				    elMouseYRel = (e.pageY - $el.offset().top) / $el.outerHeight(),
				    zIndex      = XTSThemeModule.$body.data('parallax-index') ? XTSThemeModule.$body.data('parallax-index') : 1,
				    timeout     = XTSThemeModule.$body.data('parallax-timeout') ? XTSThemeModule.$body.data('parallax-timeout') : 0;

				clearTimeout(timeout);

				if (elMouseXRel > 1) {
					elMouseXRel = 1;
				}
				if (elMouseYRel > 1) {
					elMouseYRel = 1;
				}
				if (elMouseXRel < 0) {
					elMouseXRel = 0;
				}
				if (elMouseYRel < 0) {
					elMouseYRel = 0;
				}

				var rotateX = -12 * (0.5 - elMouseYRel),
				    rotateY = +12 * (0.5 - elMouseXRel);

				var translateX = elMouseXRel * 2 * 2 - 2,
				    translateY = elMouseYRel * 2 * 2 - 2; // -2 to 2

				var perspective = width * 3;

				window.requestAnimationFrame(function() {
					$el.css({
						transform: 'perspective(' + perspective + 'px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(' + translateY + 'px) translateX(' + translateX + 'px) scale(1.05, 1.05)',
						zIndex   : zIndex
					});
				});
			});

			$el.on('mouseleave', function() {
				var $el    = $(this),
				    width  = $el.outerWidth(),
				    zIndex = XTSThemeModule.$body.data('parallax-index') ? XTSThemeModule.$body.data('parallax-index') : 1;

				var perspective = width * 3;

				window.requestAnimationFrame(function() {
					$el.css({
						transform: 'perspective(' + perspective + 'px) rotateX(0deg) rotateY(0deg) translateZ(0px)'
					});
				});

				var timeout = setTimeout(function() {
					$el.css({
						zIndex: 1
					});
				}, 250);

				XTSThemeModule.$body.data('parallax-index', zIndex + 1);
				XTSThemeModule.$body.data('parallax-timeout', timeout);
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.parallax3d();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.promoPopup = function() {
		var promo_popup_version = xts_settings.promo_popup_version;

		if (xts_settings.promo_popup !== 'yes' || (xts_settings.promo_popup_hide_mobile === 'yes' && XTSThemeModule.isMobileSize) || 0 === $('.xts-promo-popup').length) {
			return;
		}

		var shown = false;
		var pages = Cookies.get('xts_shown_pages');

		var showPopup = function() {
			$.magnificPopup.open({
				items       : {
					src: '.xts-promo-popup'
				},
				type        : 'inline',
				removalDelay: 400,
				tClose      : xts_settings.magnific_close,
				tLoading    : xts_settings.magnific_loading,
				preloader   : false,
				callbacks   : {
					beforeOpen: function() {
						this.st.mainClass = 'xts-popup-effect';
					},
					close     : function() {
						Cookies.set('xts_popup_' + promo_popup_version, 'shown', {
							expires: parseInt(xts_settings.cookies_expires),
							path   : '/'
						});
					}
				}
			});
			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		};

		$('.xts-open-promo-popup').on('click', function(e) {
			e.preventDefault();
			showPopup();
		});

		if (!pages) {
			pages = 0;
		}

		if (pages < xts_settings.promo_popup_page_visited) {
			pages++;
			Cookies.set('xts_shown_pages', pages, {
				expires: parseInt(xts_settings.cookies_expires),
				path   : '/'
			});
			return false;
		}

		if (Cookies.get('xts_popup_' + promo_popup_version) !== 'shown') {
			if (xts_settings.promo_popup_show_after === 'user-scroll') {
				XTSThemeModule.$window.on('scroll', function() {
					if (shown) {
						return false;
					}

					if (XTSThemeModule.$document.scrollTop() >= xts_settings.promo_popup_user_scroll) {
						showPopup();
						shown = true;
					}
				});
			} else {
				setTimeout(function() {
					showPopup();
				}, xts_settings.promo_popup_delay);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.promoPopup();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.scrollTopButton = function() {
		var $btn = $('.xts-scroll-to-top');

		if ($btn.length <= 0) {
			return;
		}

		XTSThemeModule.$window.on('scroll', function() {
			if ($(this).scrollTop() > 100) {
				$btn.addClass('xts-shown');
			} else {
				$btn.removeClass('xts-shown');
			}
		});

		$btn.on('click', function() {
			$('html, body').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	};

	$(document).ready(function() {
		XTSThemeModule.scrollTopButton();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.searchDropdown = function() {
		$('.xts-header-search.xts-display-dropdown').each(function() {
			var $element = $(this);

			$element.find('> a').on('click', function(e) {
				e.preventDefault();
				if (!$element.hasClass('xts-opened')) {
					$element.addClass('xts-opened');
					setTimeout(function() {
						$element.find('input[type=text]').focus();
					}, 200);
				} else {
					$element.removeClass('xts-opened');
					$element.find('input[type=text]').blur();
				}
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($element.hasClass('xts-opened') && !$(target).is('.xts-header-search.xts-display-dropdown') && !$(target).parents().is('.xts-header-search.xts-display-dropdown')) {
					$element.removeClass('xts-opened');
					$element.find('input[type=text]').blur();
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.searchDropdown();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsHeaderBuilderStickCloneHeader xtsHeaderBuilderUnStickCloneHeader xtsOffCanvasMyAccountShown xtsOffCanvasCartWidgetShown', function () {
		XTSThemeModule.searchElementCloseSearch();
	});

	XTSThemeModule.searchElement = function() {
		var $closeSide = $('.xts-close-side');
		var $searchWrapper = $('.xts-search-full-screen');
		var $search = $searchWrapper.find('input[type=text]');

		$('.xts-display-full-screen > a').on('click', function(e) {
			e.preventDefault();

			$searchWrapper.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
			setTimeout(function () {
				$search.focus();
			}, 600);
			XTSThemeModule.$document.trigger('xtsSearchOpened');
		});

		XTSThemeModule.$document.keyup(function(e) {
			if (27 === e.keyCode && $searchWrapper.hasClass('xts-opened')) {
				XTSThemeModule.searchElementCloseSearch();
			}
		});

		$('.xts-search-close > a, .xts-close-side').on('click', function(e) {
			if ($searchWrapper.hasClass('xts-opened')) {
				XTSThemeModule.searchElementCloseSearch();
			}
		});

		// Prevent search button click.
		$('.xts-header-search > a').on('click', function(e) {
			e.preventDefault();
		});
	};

	XTSThemeModule.searchElementCloseSearch = function() {
		var $searchWrapper = $('.xts-search-full-screen');
		if (!$searchWrapper.hasClass('xts-opened')) {
			return;
		}
		$searchWrapper.removeClass('xts-opened');
		$searchWrapper.find('input[type=text]').blur().val('');
		$('.xts-close-side').removeClass('xts-opened');
		XTSThemeModule.$document.trigger('xtsSearchClosed');
	};

	$(document).ready(function() {
		XTSThemeModule.searchElement();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorColumnReady', function () {
		setTimeout(function() {
			XTSThemeModule.stickyColumn();
		}, 100);
	});

	XTSThemeModule.stickyColumn = function () {
		if (XTSThemeModule.isTabletSize || 'undefined' === typeof $.fn.stick_in_parent) {
			return;
		}

		$('.xts-sticky-column').each(function () {
			var $column = $(this);
			var offset = 150;
			var classes = $column.attr('class').split(' ');

			for (var index = 0; index < classes.length; index++) {
				if (classes[index].indexOf('xts_sticky_offset_') >= 0) {
					var data = classes[index].split('_');
					offset = parseInt(data[3]);
				}
			}

			$column.find('> .elementor-widget-wrap').stick_in_parent({
				offset_top: offset,
				sticky_class: 'xts-is-stuck'
			});

			$('.wc-tabs-wrapper li').on('click', function() {
				setTimeout(function() {
					$column.find('> .elementor-widget-wrap').trigger('sticky_kit:recalc');
				}, 300);
			});
		})
	};

	$(document).ready(function() {
		XTSThemeModule.stickyColumn();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxStart xtsPortfolioPjaxStart', function() {
		XTSThemeModule.stickyLoaderPosition();
	});

	XTSThemeModule.stickyLoaderPosition = function() {
		var loaderVerticalPosition = function() {
			var $products = $('.xts-products[data-source="main_loop"], .xts-portfolio-loop[data-source="main_loop"]');
			var $loader = $products.parent().find('.xts-sticky-loader');

			if ($products.length < 1) {
				return;
			}

			var offset = XTSThemeModule.$window.height() / 2;
			var scrollTop = XTSThemeModule.$window.scrollTop();
			var holderTop = $products.offset().top - offset + 45;
			var holderHeight = $products.height();
			var holderBottom = holderTop + holderHeight - 100;

			if (scrollTop < holderTop) {
				$loader.addClass('xts-position-top');
				$loader.removeClass('xts-position-stick');
			} else if (scrollTop > holderBottom) {
				$loader.addClass('xts-position-bottom');
				$loader.removeClass('xts-position-stick');
			} else {
				$loader.addClass('xts-position-stick');
				$loader.removeClass('xts-position-top xts-position-bottom');
			}
		};

		XTSThemeModule.$window.off('scroll.loaderVerticalPosition');

		XTSThemeModule.$window.on('scroll.loaderVerticalPosition', loaderVerticalPosition);
	};

	$(document).ready(function() {
		XTSThemeModule.stickyLoaderPosition();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function () {
		XTSThemeModule.stickySidebar();
	});

	XTSThemeModule.stickySidebar = function() {
		if (XTSThemeModule.isTabletSize || 'undefined' === typeof $.fn.stick_in_parent) {
			return;
		}

		var $sidebar = $('.xts-sidebar');

		if ($sidebar.hasClass('xts-sidebar-hidden-lg') && $sidebar.hasClass('xts-sidebar-hidden-md')) {
			return;
		}

		if ($sidebar.hasClass('xts-sidebar-hidden-lg') && !$sidebar.hasClass('xts-sidebar-hidden-md') && XTSThemeModule.isDesktop) {
			return;
		}

		if ($sidebar.hasClass('xts-sidebar-hidden-md') && !$sidebar.hasClass('xts-sidebar-hidden-lg') && XTSThemeModule.isTabletSize) {
			return;
		}

		$('.xts-sidebar-sticky .xts-sidebar-inner').stick_in_parent({
			offset_top: parseInt(xts_settings.sticky_sidebar_offset),
			sticky_class: 'xts-is-stuck',
		});
	};

	$(document).ready(function() {
		XTSThemeModule.stickySidebar();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsWishlistRemoveSuccess xtsProductTabLoaded xtsElementorProductTabsReady xtsProductLoadMoreReInit xtsPortfolioPjaxComplete xtsProductQuickViewOpen xtsPjaxComplete', function () {
		XTSThemeModule.tooltip();
	});

	XTSThemeModule.$document.on('xtsPjaxStart xtsPortfolioPjaxStart', function () {
		XTSThemeModule.hideTooltip();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.tooltip();
		});
	});

	var tooltipConfig = {
		left : {
			selectors: xts_settings.tooltip_left_selector
		},
		top  : {
			selectors: xts_settings.tooltip_top_selector
		},
		right: {
			selectors: ''
		}
	};

	XTSThemeModule.tooltip = function() {
		if (XTSThemeModule.isTabletSize) {
			return;
		}

		var findTitle = function($el) {
			var text = $el.text();

			if ($el.data('xts-tooltip')) {
				text = $el.data('xts-tooltip');
			}

			if ($el.find('.added_to_cart').length > 0) {
				text = $el.find('.add_to_cart_button').text();
			}

			return text;
		};

		var rtlPlacement = function(placement) {
			if ('left' === placement && XTSThemeModule.$body.hasClass('rtl')) {
				return 'right';
			}

			if ('right' === placement && XTSThemeModule.$body.hasClass('rtl')) {
				return 'left';
			}

			return placement;
		};

		$.each(tooltipConfig, function(key, value) {
			$(value.selectors).on('mouseenter touchstart', function() {
				var $this = $(this);

				if ( $this.hasClass('xts-tooltip-inited') ) {
					return;
				}

				$this.tooltip({
					animation: false,
					container: 'body',
					trigger: 'hover',
					boundary: 'window',
					placement: rtlPlacement(key),
					title: function() {
						return findTitle($this);
					},
				});

				$this.tooltip('show');

				$this.addClass('xts-tooltip-inited');
			});
		});
	};

	XTSThemeModule.hideTooltip = function() {
		if (XTSThemeModule.isTabletSize) {
			return;
		}

		$.each(tooltipConfig, function(key, value) {
			$(value.selectors).tooltip('hide');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.tooltip();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.widgetsCollapse = function() {
		if (XTSThemeModule.isSuperMobile) {
			$('.xts-footer .xts-widget-collapse').addClass('xts-inited');
		}

		XTSThemeModule.$document.on('click', '.xts-widget-collapse.xts-inited .widget-title', function() {
			var $title = $(this);
			var $widget = $title.parent();
			var $content = $widget.find('> .widget-title ~ *');

			if ($widget.hasClass('xts-opened') || ($widget.hasClass('xts-initially-opened') && !$widget.hasClass('xts-initially-clicked'))) {
				if ($widget.hasClass('xts-initially-opened')) {
					$widget.addClass('xts-initially-clicked');
				}

				$widget.removeClass('xts-opened');
				$content.stop().slideUp(200);
			} else {
				$widget.addClass('xts-opened');
				$content.stop().slideDown(200);
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.widgetsCollapse();
	});
})(jQuery);


/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_mega_menu.default', function() {
		XTSThemeModule.menuClickEvent();
	});

	XTSThemeModule.menuClickEvent = function() {
		var menu = $('.xts-header .xts-nav-main, .xts-header .xts-nav-secondary, .elementor-widget-xts_mega_menu .xts-nav-mega, .xts-sticky-cats .xts-nav-sticky-cat');

		menu.on('click', ' > .xts-event-click > a', function(e) {
			e.preventDefault();
			var $this = $(this);

			if (!$this.parent().hasClass('xts-opened')) {
				menu.find('.xts-opened').removeClass('xts-opened');
			}

			$this.parent().toggleClass('xts-opened');
		});

		XTSThemeModule.$document.on('click', function(e) {
			var target = e.target;

			if (menu.find('.xts-opened').length > 0 && !$(target).is('.xts-event-hover') && !$(target).parents().is('.xts-event-hover') && !$(target).parents().is('.xts-opened')) {
				menu.find('.xts-opened').removeClass('xts-opened');
				$('.xts-close-side').removeClass('xts-opened');

				return false;
			}
		});

		XTSThemeModule.$window.on('resize', XTSThemeModule.debounce(function() {
			if (XTSThemeModule.isTablet()) {
				menu.find(' > .menu-item-has-children.xts-event-hover').each(function() {
					$(this).data('original-event', 'hover').removeClass('xts-event-hover').addClass('xts-event-click');
				});
			} else {
				menu.find(' > .xts-event-click').each(function() {
					var $this = $(this);
					if ('hover' === $this.data('original-event')) {
						$this.removeClass('xts-event-click').addClass('xts-event-hover');
					}
				});
			}
		}, 300));
	};

	$(document).ready(function() {
		XTSThemeModule.menuClickEvent();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_mega_menu.default', function() {
		XTSThemeModule.menuDropdownsAJAX();
	});

	XTSThemeModule.menuDropdownsAJAX = function() {
		var $menus = $('.menu').has('.xts-dropdown-ajax');

		$('body').on('mousemove', checkMenuProximity);

		function checkMenuProximity(event) {
			$menus.each(function() {
				var $menu = $(this);

				if ($menu.hasClass('xts-dropdowns-loading') || $menu.hasClass('xts-dropdowns-loaded')) {
					return;
				}

				if (!isNear($menu, 50, event)) {
					return;
				}

				loadDropdowns($menu);
			});
		}

		function loadDropdowns($menu) {
			$menu.addClass('xts-dropdowns-loading');

			var storageKey = xts_settings.menu_storage_key + '_' + $menu.attr('id');
			var storedData = false;

			var $items = $menu.find('.xts-dropdown-ajax'),
			    ids    = [];

			$items.each(function() {
				ids.push(jQuery(this).find('.xts-dropdown-placeholder').data('id'));
			});

			if (xts_settings.ajax_dropdowns_save && XTSThemeModule.supports_html5_storage) {
				var unparsedData = localStorage.getItem(storageKey);

				try {
					storedData = JSON.parse(unparsedData);
				}
				catch (e) {
					console.log('cant parse Json', e);
				}
			}

			if (storedData) {
				renderResults(storedData);
				$menu.removeClass('xts-dropdowns-loading').addClass('xts-dropdowns-loaded');
			} else {
				jQuery.ajax({
					url     : xts_settings.ajaxurl,
					data    : {
						action: 'xts_load_html_dropdowns',
						ids   : ids
					},
					dataType: 'json',
					method  : 'POST',
					success : function(response) {
						if ('success' === response.status) {
							renderResults(response.data);
							if (xts_settings.ajax_dropdowns_save && XTSThemeModule.supports_html5_storage) {
								localStorage.setItem(storageKey, JSON.stringify(response.data));
							}
						} else {
							console.log('loading html dropdowns returns wrong data - ', response.message);
						}
					},
					error   : function() {
						console.log('loading html dropdowns ajax error');
					},
					complete: function() {
						$menu.removeClass('xts-dropdowns-loading').addClass('xts-dropdowns-loaded');
					}
				});
			}

			function renderResults(data) {
				Object.keys(data).forEach(function(id) {
					var html = data[id];
					$menu.find('[data-id="' + id + '"]').siblings('.xts-dropdown-inner').html(html);
					$menu.find('[data-id="' + id + '"]').remove();
				});

				// Initialize OWL Carousels
				XTSThemeModule.$document.trigger('xtsMenuDropdownsAJAXRenderResults');
			}
		}

		function isNear($element, distance, event) {
			var left   = $element.offset().left - distance,
			    top    = $element.offset().top - distance,
			    right  = left + $element.width() + (2 * distance),
			    bottom = top + $element.height() + (2 * distance),
			    x      = event.pageX,
			    y      = event.pageY;

			return (x > left && x < right && y > top && y < bottom);
		}
	};

	$(document).ready(function() {
		XTSThemeModule.menuDropdownsAJAX();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_mega_menu.default', function() {
		XTSThemeModule.menuOffsets();
	});

	XTSThemeModule.menuOffsets = function() {
		var setOffset = function(li) {
			var $dropdown = li.find(' > .xts-dropdown-menu');
			var dropdownWidth = $dropdown.outerWidth();
			var dropdownOffset = $dropdown.offset();
			var toRight;
			var viewportWidth;

			$dropdown.attr('style', '');

			if (!dropdownWidth || !dropdownOffset) {
				return;
			}

			if ($dropdown.hasClass('xts-style-full')) {
				viewportWidth = XTSThemeModule.$window.width();

				if (dropdownOffset.left + dropdownWidth + parseInt(xts_settings.menu_animation_offset) >= viewportWidth) {
					toRight = dropdownOffset.left + dropdownWidth - viewportWidth + parseInt(xts_settings.menu_animation_offset);

					$dropdown.css({
						left: -toRight
					});
				}
			} else if ($dropdown.hasClass('xts-style-sized') || $dropdown.hasClass('xts-style-default') || $dropdown.hasClass('xts-style-container')) {
				viewportWidth = xts_settings.site_width;

				if (XTSThemeModule.$window.width() < viewportWidth || !viewportWidth) {
					viewportWidth = XTSThemeModule.$window.width();
				}

				var extraSpace = 15;
				var containerOffset = (XTSThemeModule.$window.width() - viewportWidth) / 2;
				var dropdownOffsetLeft = dropdownOffset.left - containerOffset;

				if (XTSThemeModule.$body.hasClass('xts-layout-boxed')) {
					extraSpace = 0;
				}

				if (($dropdown.hasClass('xts-style-container')) || (dropdownOffsetLeft + dropdownWidth >= viewportWidth)) {
					toRight = dropdownOffsetLeft + dropdownWidth - viewportWidth + parseInt(xts_settings.menu_animation_offset);

					$dropdown.css({
						left: -toRight - extraSpace
					});
				}
			}
		};

		$('.xts-nav-main, .xts-nav-main > li, .xts-nav-secondary, .xts-nav-secondary > li, .xts-nav-mega.xts-direction-h, .xts-nav-mega.xts-direction-h > li').each(function() {
			var $menu = $(this);

			if ($menu.hasClass('menu-item')) {
				$menu = $(this).parent();
			}

			$menu.on('mouseenter mousemove', function() {
				if ($menu.hasClass('xts-offsets-calculated')) {
					return;
				}

				$menu.find(' > .menu-item-has-children').each(function() {
					setOffset($(this));
				});

				$menu.addClass('xts-offsets-calculated');
			});

			setTimeout(function() {
				XTSThemeModule.$window.on('resize', XTSThemeModule.debounce(function() {
					$menu.removeClass('xts-offsets-calculated');
					$menu.find(' > .menu-item-has-children > .xts-dropdown-menu').attr('style', '');
				}, 300));
			}, 2000);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.menuOffsets();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.mobileNavigation = function() {
		var $body = XTSThemeModule.$body;
		var $mobileNav = $('.xts-side-mobile');
		var $dropDownCat = $('.xts-nav-mobile .menu-item-has-children');
		var $closeSide = $('.xts-close-side');
		var $search = $mobileNav.find('.searchform input[type=text]');
		var time = 200;

		$dropDownCat.append('<span class="xts-submenu-opener"></span>');

		$mobileNav.on('click', '.xts-submenu-opener', function(e) {
			e.preventDefault();
			var $this = $(this);

			if ($this.hasClass('xts-opened')) {
				$this.removeClass('xts-opened').siblings('ul').slideUp(time);
			} else {
				$this.addClass('xts-opened').siblings('ul').slideDown(time);
			}
		});

		$body.on('click', '.xts-header-mobile-burger > a, .xts-navbar-burger', function(e) {
			e.preventDefault();

			if ($mobileNav.hasClass('xts-opened')) {
				closeMenu();
			} else {
				openMenu();
			}
		});

		$body.on('click touchstart', '.xts-close-side', function() {
			closeMenu();
		});

		$body.on('click', '.xts-menu-item-account.xts-opener', function() {
			closeMenu();
		});

		XTSThemeModule.$document.keyup(function(e) {
			if (27 === e.keyCode) {
				closeMenu();
			}
		});

		function openMenu() {
			$mobileNav.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
		}

		function closeMenu() {
			$mobileNav.removeClass('xts-opened');
			$closeSide.removeClass('xts-opened');
			$search.blur();
		}

		$('.xts-header-mobile-search').on('click', function(e) {
			e.preventDefault();

			if (XTSThemeModule.isDesktop) {
				return;
			}

			if (!$mobileNav.hasClass('xts-opened')) {
				openMenu();
				setTimeout(function() {
					$search.focus();
				}, 600);
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.mobileNavigation();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.onePageMenu = function() {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		var scrollToAnchor = function(hash) {
			var $htmlBody = $('html, body');

			$htmlBody.stop(true);
			var $anchor = $('.xts-menu-anchor[data-id="' + hash + '"]');

			if ($anchor.length < 1) {
				return;
			}

			var position = $anchor.offset().top;

			$htmlBody.animate({
				scrollTop: position - $anchor.data('offset')
			}, 800);

			setTimeout(function() {
				activeMenuItem(hash);
			}, 800);
		};

		var activeMenuItem = function(hash) {
			$('.xts-onepage-link').each(function() {
				var $this = $(this);
				var itemHash = $this.find('> a').attr('href').split('#')[1];

				if (itemHash === hash) {
					$this.siblings().removeClass('current-menu-item');
					$this.addClass('current-menu-item');
				}
			});
		};

		XTSThemeModule.$body.on('click', '.xts-onepage-link > a', function(e) {
			var $this = $(this);
			var hash = $this.attr('href').split('#')[1];

			if ($('.xts-menu-anchor[data-id="' + hash + '"]').length < 1) {
				return;
			}

			e.stopPropagation();
			e.preventDefault();

			scrollToAnchor(hash);

			$('.xts-close-side').trigger('click');
			$('.xts-fs-close').trigger('click');
		});

		if ($('.xts-onepage-link').length > 0) {
			XTSThemeModule.$document.on('scroll', function() {
				var scrollTop = $(this).scrollTop();

				if (scrollTop === 0) {
					var $item = $('.xts-onepage-link').first();

					$item.siblings().removeClass('current-menu-item');
					$item.addClass('current-menu-item');
				}
			});

			$('.xts-menu-anchor').xtsWaypoint(function() {
				activeMenuItem($($(this)[0].element).data('id'));
			}, {
				offset: function() {
					return $($(this)[0].element).data('offset');
				}
			});

			var locationHash = window.location.hash.split('#')[1];

			if (window.location.hash.length > 1) {
				setTimeout(function() {
					scrollToAnchor(locationHash);
				}, 500);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.onePageMenu();
	});
})(jQuery);
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
/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_animated_text.default', function() {
		XTSThemeModule.animatedTextElement();
	});

	XTSThemeModule.animatedTextElement = function() {
		$('.xts-anim-text').each(function() {
			var $element = $(this);
			var $animatedTextList = $element.find('.xts-anim-text-list');
			var $animatedTextWords = $animatedTextList.find('.xts-anim-text-item');
			var effect = $animatedTextList.data('effect');

			var animationDelay = $element.data('interval-time');
			// Typing effect
			var typeLettersDelay = $element.data('character-time');
			var selectionDuration = 500;
			var typeAnimationDelay = selectionDuration + 800;
			// Word effect
			var revealDuration = $element.data('animation-time');

			if ($animatedTextList.hasClass('xts-inited')) {
				return;
			}

			trimWords();
			runAnimation();

			function trimWords() {
				if ('typing' !== effect) {
					return;
				}

				$animatedTextWords.each(function() {
					var $word = $(this);
					var letters = $word.text().trim().split('');

					for (var index = 0; index < letters.length; index++) {
						var letterClasses = '';

						if (0 === $word.index()) {
							letterClasses = 'xts-in';
						}

						letters[index] = '<span class="' + letterClasses + '">' + letters[index] + '</span>';
					}

					$word.html(letters.join(''));
				});
			}

			function runAnimation() {
				if ('word' === effect) {
					$animatedTextList.width($animatedTextList.width() + 3);
				} else if ('typing' !== effect) {
					var width = 0;

					$animatedTextWords.each(function() {
						var wordWidth = $(this).width();

						if (wordWidth > width) {
							width = wordWidth;
						}
					});

					$animatedTextList.css('width', width);
				}

				setTimeout(function() {
					hideWord($animatedTextWords.eq(0));
				}, animationDelay);

				$animatedTextList.addClass('xts-inited');
			}

			function hideWord($word) {
				var nextWord = getNextWord($word);

				if ('typing' === effect) {
					$animatedTextList.addClass('xts-selected');

					setTimeout(function() {
						$animatedTextList.removeClass('xts-selected');
						$word.addClass('xts-hidden').removeClass('xts-active').children('span').removeClass('xts-in');
					}, selectionDuration);

					setTimeout(function() {
						showWord(nextWord, typeLettersDelay);
					}, typeAnimationDelay);
				} else if ('word' === effect) {
					$animatedTextList.animate({width: '2px'}, revealDuration, function() {
						switchWord($word, nextWord);
						showWord(nextWord);
					});
				}
			}

			function showLetter($letter, $word, bool, duration) {
				$letter.addClass('xts-in');

				if (!$letter.is(':last-child')) {
					setTimeout(function() {
						showLetter($letter.next(), $word, bool, duration);
					}, duration);
				} else if (!bool) {
					setTimeout(function() {
						hideWord($word);
					}, animationDelay);
				}
			}

			function showWord($word, $duration) {
				if ('typing' === effect) {
					showLetter($word.find('span').eq(0), $word, false, $duration);

					$word.addClass('xts-active').removeClass('xts-hidden');
				} else if ('word' === effect) {
					$animatedTextList.animate({width: $word.width() + 3}, revealDuration, function() {
						setTimeout(function() {
							hideWord($word);
						}, animationDelay);
					});
				}
			}

			function getNextWord($word) {
				return $word.is(':last-child') ? $word.parent().children().eq(0) : $word.next();
			}

			function switchWord($oldWord, $newWord) {
				$oldWord.removeClass('xts-active').addClass('xts-hidden');
				$newWord.removeClass('xts-hidden').addClass('xts-active');
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.animatedTextElement();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.buttonSmoothScroll = function() {
		$('.xts-button-wrapper.xts-smooth-scroll a').on('click', function(e) {
			e.stopPropagation();

			var $button = $(this);
			var time = $button.parent().data('smooth-time');
			var offset = $button.parent().data('smooth-offset');
			var hash = $button.attr('href').split('#')[1];

			var $anchor = $('#' + hash);

			if ($anchor.length < 1) {
				return;
			}

			var position = $anchor.offset().top;

			$('html, body').animate({
				scrollTop: position - offset
			}, time);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.buttonSmoothScroll();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_circle_progress.default', function() {
		XTSThemeModule.circleProgressBarElement();
	});

	XTSThemeModule.circleProgressBarElement = function() {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		$('.xts-circle-progress').each(function() {
			var $element = $(this);
			var $circleValue = $element.find('.xts-circle-meter-value');
			var $counter = $element.find('.xts-circle-number');
			var counterFinal = $counter.data('final');
			var duration = $element.data('duration');

			$element.xtsWaypoint(function() {
				if ('done' !== $counter.attr('data-state') && $counter.text() !== counterFinal) {
					$counter.prop('Counter', 0).animate({
						Counter: counterFinal
					}, {
						duration: duration,
						easing  : 'swing',
						step    : function(now) {
							if (now >= counterFinal) {
								$counter.attr('data-state', 'done');
							}

							$counter.text(Math.ceil(now));
						}
					});
				}

				// animate progress
				var circumference = parseInt($element.data('circumference'));
				var dashoffset = circumference * (1 - ($circleValue.data('value') / 100));

				$circleValue.css({
					'transitionDuration': duration + 'ms',
					'strokeDashoffset'  : dashoffset
				});

			}, {
				offset: '90%'
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.circleProgressBarElement();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_popup.default', function() {
		XTSThemeModule.popupElement();
	});

	XTSThemeModule.popupElement = function() {
		if ('undefined' === typeof $.fn.magnificPopup) {
			return;
		}

		$.magnificPopup.close();

		$('.xts-popup-opener').magnificPopup({
			type        : 'inline',
			removalDelay: 400,
			tClose      : xts_settings.magnific_close,
			tLoading    : xts_settings.magnific_loading,
			preloader   : false,
			callbacks   : {
				beforeOpen: function() {
					this.st.mainClass = 'xts-popup-effect';
				},
				open      : function() {
					XTSThemeModule.$document.trigger('xtsImagesLoaded');
					XTSThemeModule.$window.resize();
				}
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.popupElement();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete xtsProductQuickViewOpen xtsProductLoadMoreReInit xtsWishlistRemoveSuccess xtsProductTabLoaded xtsElementorProductTabsReady', function() {
		XTSThemeModule.countDownTimerElement();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default',
		'frontend/element_ready/xts_single_product_countdown.default',
		'frontend/element_ready/xts_product_countdown.default',
		'frontend/element_ready/xts_countdown_timer.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.countDownTimerElement();
		});
	});

	XTSThemeModule.countDownTimerElement = function() {
		$('.xts-countdown-timer').each(function() {
			var $this = $(this);
			dayjs.extend(window.dayjs_plugin_utc);
			dayjs.extend(window.dayjs_plugin_timezone);
			var time = dayjs.tz($this.data('end-date'), $this.data('timezone'));

			$this.countdown(time.toDate(), function(event) {
				$this.html(event.strftime(''
					+ '<div class="xts-countdown-item xts-countdown-days"><div class="xts-countdown-digit">%-D</div><div class="xts-countdown-label">' + xts_settings.countdown_days + '</div></div> '
					+ '<div class="xts-countdown-item xts-countdown-hours"><div class="xts-countdown-digit">%H</div><div class="xts-countdown-label">' + xts_settings.countdown_hours + '</div></div> '
					+ '<div class="xts-countdown-item xts-countdown-min"><div class="xts-countdown-digit">%M</div><div class="xts-countdown-label">' + xts_settings.countdown_mins + '</div></div> '
					+ '<div class="xts-countdown-item xts-countdown-sec"><div class="xts-countdown-digit">%S</div><div class="xts-countdown-label">' + xts_settings.countdown_sec + '</div></div>'
				));
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.countDownTimerElement();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_google_map.default', function() {
		XTSThemeModule.googleMapInit();
		XTSThemeModule.googleMapCloseContent();
	});

	XTSThemeModule.googleMapInit = function() {
		if ( typeof google === 'undefined' ) {
			return;
		}

		$('.xts-map').each(function() {
			var $map = $(this);
			var data = $map.data('map-args');

			var config = {
				locations: [
					{
						lat: data.latitude,
						lon: data.longitude,
						icon: data.marker_icon,
						animation: google.maps.Animation.DROP,
					},
				],
				controls_on_map: false,
				map_div: '#' + data.selector,
				start: 1,
				map_options: {
					zoom: parseInt(data.zoom),
					scrollwheel: 'yes' === data.mouse_zoom,
					disableDefaultUI: data.default_ui,
				},
			};

			if (data.json_style) {
				config.styles = {};
				config.styles[xts_settings.google_map_style_text] = $.parseJSON(atob(data.json_style));
			}

			if ('yes' === data.marker_text_needed) {
				config.locations[0].html = data.marker_text;
			}

			if ('button' === data.lazy_type) {
				$map.find('.xts-map-button').on('click', function(e) {
					e.preventDefault();

					if ($map.hasClass('xts-loaded')) {
						return;
					}

					$map.addClass('xts-loaded');
					new Maplace(config).Load();
				});
			} else if ('scroll' === data.lazy_type) {
				XTSThemeModule.$window.on('scroll', function() {
					if ((window.innerHeight + XTSThemeModule.$window.scrollTop() + 100) > $map.offset().top) {
						if ($map.hasClass('xts-loaded')) {
							return;
						}

						$map.addClass('xts-loaded');
						new Maplace(config).Load();
					}
				});

				XTSThemeModule.$window.scroll();
			} else {
				new Maplace(config).Load();
			}
		});
	};

	XTSThemeModule.googleMapCloseContent = function() {
		var $map = $('.xts-map-close');

		if ( $map.hasClass('xts-inited') ) {
			return;
		}

		$map.addClass('xts-inited');

		$map.on('click', function(e) {
			e.preventDefault();
			$(this).parent().toggleClass('xts-opened');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.googleMapInit();
		XTSThemeModule.googleMapCloseContent();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_hotspots.default', function() {
		XTSThemeModule.hotSpotsElement();
	});

	XTSThemeModule.hotSpotsElement = function() {
		$('.xts-spot').each(function() {
			var $this = $(this);
			var $btn = $this.find('.xts-spot-icon');

			if ((!$this.hasClass('xts-event-click') && XTSThemeModule.isDesktop ) || $this.hasClass('xts-inited')) {
				return;
			}

			$this.addClass('xts-inited');

			$btn.on('click', function() {
				var $content = $(this).parent().find('.xts-spot-content');

				if ($content.hasClass('xts-opened')) {
					$content.removeClass('xts-opened');
				} else {
					$content.addClass('xts-opened');
					$content.parent().siblings().find('.xts-spot-content').removeClass('xts-opened');
				}

				return false;
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($this.find('.xts-spot-content').hasClass('xts-opened') && !$(target).is('.xts-spot') && !$(target).parents().is('.xts-spot')) {
					$this.find('.xts-spot-content').removeClass('xts-opened');
					return false;
				}
			});
		});

		$('.xts-spot-content').each(function() {
			var $this = $(this);
			var offsetLeft = $this.offset().left;
			var offsetRight = XTSThemeModule.windowWidth - (offsetLeft + $this.outerWidth());

			if (XTSThemeModule.isTabletSize) {
				if (offsetLeft <= 0) {
					$this.css('marginLeft', Math.abs(offsetLeft - 15) + 'px');
				}

				if (offsetRight <= 0) {
					$this.css('marginLeft', offsetRight - 15 + 'px');
				}
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.hotSpotsElement();
	});
})(jQuery);
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

/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_price_plan_switcher.default', function() {
		XTSThemeModule.pricePlanSwitcherElement();
	});

	XTSThemeModule.pricePlanSwitcherElement = function() {
		$('.xts-nav-pp-switcher li').on('click', 'a', function(e) {
			e.preventDefault();
			var $control = $(this).parent();
			var switcherAction = $control.data('action');

			$control.siblings().removeClass('xts-active');
			$control.addClass('xts-active');

			$('.xts-price-plan').each(function() {
				var $pricePlan = $(this);
				var $pricePlanPricing = $pricePlan.find('.xts-plan-pricing');
				var pricingData = $pricePlanPricing.data('pricing');

				if (pricingData[switcherAction].price || pricingData[switcherAction].fraction || pricingData[switcherAction].title) {
					$pricePlanPricing.find('.xts-plan-price').text(pricingData[switcherAction].price);
					$pricePlanPricing.find('.xts-plan-fraction').text(pricingData[switcherAction].fraction);
					$pricePlanPricing.parent().find('.xts-plan-pricing-subtitle').text(pricingData[switcherAction].title);
				}

				if (pricingData[switcherAction].button_data) {
					$pricePlan.find('.xts-button').attr('href', pricingData[switcherAction].button_data.href);
					$pricePlan.find('.xts-button').data('product_id', pricingData[switcherAction].button_data.product_id);
					$pricePlan.find('.xts-button').data('product_sku', pricingData[switcherAction].button_data.product_sku);
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.pricePlanSwitcherElement();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_slider.default', function() {
		XTSThemeModule.sliderAnimations();
		XTSThemeModule.sliderLazyLoad();
		XTSThemeModule.$document.trigger('xtsElementorSliderReady');
	});

	XTSThemeModule.sliderAnimations = function() {
		$('.xts-slider').each(function() {
			var $carousel = $(this);

			$carousel.find('[class*="xts-animation"]').each(function() {
				$(this).addClass('xts-animation-ready');
			});

			runAnimations(0, true);

			$carousel.on('change.flickity', function(event, index) {
				runAnimations(index, false);
			});

			function runAnimations(slideIndex, firstLoad) {
				var nextSlide = $carousel.find('.xts-slide').eq(slideIndex);

				nextSlide.siblings().find('[class*="xts-animation"]').removeClass('xts-animated');

				nextSlide.find('[class*="xts-animation"]').each(function() {
					var $this = $(this);
					var classes = $this.attr('class').split(' ');
					var delay = 0;

					for (var index = 0; index < classes.length; index++) {
						if (classes[index].indexOf('xts_delay_') >= 0) {
							delay = parseInt(classes[index].split('_')[2]);
						}
					}

					if (firstLoad) {
						delay += 500;
					}

					setTimeout(function() {
						$this.addClass('xts-animated');
					}, delay);
				});
			}
		});
	};

	XTSThemeModule.sliderLazyLoad = function() {
		$('.xts-slider').on('select.flickity', function(event, index) {
			var $this = $(this);
			var active = $this.find('.xts-slide').eq(index);
			var $els = $this.find('[id="' + active.attr('id') + '"]');

			$this.find('.xts-slide').eq(index + 1).addClass('xts-loaded');
			active.addClass('xts-loaded');

			$els.each(function() {
				$(this).addClass('xts-loaded');
			});

			// Video pause
			if (active.find('.xts-slide-video-html5').length > 0) {
				active.addClass('xts-playing');
				active.find('.xts-slide-video-html5')[0].play();
			}
			if (active.siblings().find('.xts-slide-video-html5').length > 0) {
				active.removeClass('xts-playing');
				active.siblings().find('.xts-slide-video-html5')[0].pause();
			}

			// Vimeo
			var vimeo;
			if (active.find('.xts-slide-video-vimeo').length > 0) {
				active.addClass('xts-playing');
				vimeo = new Vimeo.Player(active.find('.xts-slide-video-vimeo')[0]);
				vimeo.play();
			}
			if (active.siblings().find('.xts-slide-video-vimeo').length > 0) {
				active.siblings().removeClass('xts-playing');
				vimeo = new Vimeo.Player(active.siblings().find('.xts-slide-video-vimeo')[0]);
				vimeo.pause();
			}
		});
	};

	XTSThemeModule.youtubeVideoAPI = function() {
		window.onYouTubeIframeAPIReady = function() {
			$('.xts-slide-video-youtube').each(function() {
				var $video = $(this);
				var player;

				player = new YT.Player($video[0], {
					events: {
						'onReady': onPlayerReady
					}
				});

				function onPlayerReady() {
					$('.xts-slider').on('select.flickity', function(event, index) {
						var $this = $(this);
						var active = $this.find('.xts-slide').eq(index);

						if (active.find('.xts-slide-video-youtube').length > 0) {
							active.addClass('xts-playing');
							player.playVideo();
						}

						if (active.siblings().find('.xts-slide-video-youtube').length > 0) {
							active.siblings().removeClass('xts-playing');
							player.pauseVideo();
						}
					});
				}
			});
		};
	};

	$(document).ready(function() {
		XTSThemeModule.sliderAnimations();
		XTSThemeModule.sliderLazyLoad();
		XTSThemeModule.youtubeVideoAPI();
	});
})(jQuery);
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
/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_360_view.default', function() {
		XTSThemeModule.threeSixty();
	});

	XTSThemeModule.threeSixty = function() {
		$('.xts-360-view').each(function() {
			var $this = $(this);
			var data = $this.data('args');

			if (!data) {
				return false;
			}

			$this.ThreeSixty({
				totalFrames : data.frames_count,
				endFrame    : data.frames_count,
				currentFrame: 1,
				imgList     : '.xts-360-images',
				progress    : '.xts-360-progress',
				imgArray    : data.images,
				height      : data.height,
				width       : data.width,
				responsive  : true,
				navigation  : 'yes' === data.navigation
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.threeSixty();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsMenuDropdownsAJAXRenderResults', function() {
		XTSThemeModule.videoElementClick();
		XTSThemeModule.videoElementLazyLoad();
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_video.default', function() {
		XTSThemeModule.videoElementClick();
		XTSThemeModule.videoElementLazyLoad();
	});

	XTSThemeModule.videoElementClick = function() {
		$('.xts-el-video-btn-overlay:not(.xts-el-video-lightbox):not(.xts-el-video-hosted)').on('click', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $video = $this.parents('.xts-el-video').find('iframe');
			var videoScr = $video.data('lazy-load');
			var videoNewSrc = videoScr + '&autoplay=1&rel=0&mute=1';

			if (videoScr.indexOf('vimeo.com') + 1) {
				videoNewSrc = videoScr.replace('#t=', '') + '&autoplay=1';
			}

			$video.attr('src', videoNewSrc);
			$this.parents('.xts-el-video').addClass('xts-playing');
		});

		$('.xts-el-video-btn-overlay.xts-el-video-hosted:not(.xts-el-video-lightbox)').on('click', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $video = $this.parents('.xts-el-video').find('video');
			var videoScr = $video.data('lazy-load');

			$video.attr('src', videoScr);
			$video[0].play();
			$this.parents('.xts-el-video').addClass('xts-playing');
		});
	};

	XTSThemeModule.videoElementLazyLoad = function() {
		$('.xts-el-video, .xts-single-post .xts-post-video').each(function() {
			var $videoWrapper = $(this);
			var $video = $videoWrapper.find('iframe');
			if ($video.length === 0) {
				$video = $videoWrapper.find('video');
			}
			var videoScr = $video.data('lazy-load');

			if (!$videoWrapper.hasClass('xts-action-without') && $videoWrapper.hasClass('xts-el-video')) {
				return;
			}

			XTSThemeModule.$window.on('scroll', function() {
				if ((window.innerHeight + XTSThemeModule.$window.scrollTop() + 100) > $videoWrapper.offset().top) {
					if ($videoWrapper.hasClass('xts-loaded')) {
						return;
					}

					$videoWrapper.addClass('xts-loaded');
					$video.attr('src', videoScr);
					if ($video.attr('autoplay')) {
						$video[0].play();
					}
				}
			});

			XTSThemeModule.$window.scroll();
		});
	};

	$(document).ready(function() {
		XTSThemeModule.videoElementClick();
		XTSThemeModule.videoElementLazyLoad();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsMenuDropdownsAJAXRenderResults', function() {
		XTSThemeModule.videoElementPopup();
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_video.default', function() {
		XTSThemeModule.videoElementPopup();
	});

	XTSThemeModule.videoElementPopup = function() {
		if ('undefined' === typeof ($.fn.magnificPopup)) {
			return;
		}

		$.magnificPopup.close();

		$('.xts-el-video-btn:not(.xts-el-video-hosted), .xts-el-video-btn-overlay.xts-el-video-lightbox:not(.xts-el-video-hosted), .xts-action-btn.xts-video-btn a').magnificPopup({
			tClose         : xts_settings.magnific_close,
			tLoading       : xts_settings.magnific_loading,
			removalDelay   : 400,
			type           : 'iframe',
			preloader      : false,
			fixedContentPos: false,
			iframe         : {
				patterns: {
					youtube: {
						index: 'youtube.com/',
						id   : 'v=',
						src  : '//www.youtube.com/embed/%id%?rel=0&autoplay=1&mute=1'
					},
					vimeo  : {
						index: 'vimeo.com/',
						id   : '/',
						src  : '//player.vimeo.com/video/%id%?autoplay=1'
					}
				}
			},
			callbacks      : {
				beforeOpen: function() {
					this.st.mainClass = 'xts-popup-effect';
				}
			}
		});

		$('.xts-el-video-btn-overlay.xts-el-video-lightbox.xts-el-video-hosted,.xts-el-video-btn.xts-el-video-hosted').magnificPopup({
			type        : 'inline',
			removalDelay: 400,
			tClose      : xts_settings.magnific_close,
			tLoading    : xts_settings.magnific_loading,
			preloader   : false,
			callbacks   : {
				beforeOpen  : function() {
					this.st.mainClass = 'xts-popup-effect xts-popup-video-holder';
				},
				elementParse: function(item) {
					var $video = $(item.src).find('video');
					var videoScr = $video.data('lazy-load');
					$video.attr('src', videoScr);
					$video.attr('autoplay', '1');
				},
				open        : function() {
					XTSThemeModule.$document.trigger('xtsImagesLoaded');
					XTSThemeModule.$window.resize();
				},
				close       : function(e) {
					var magnificPopup = $.magnificPopup.instance;

					var $video = $(magnificPopup.items[0].src).find('video');
					$video.attr('src', '');
				}
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.videoElementPopup();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.blogLoadMore = function() {
		var infiniteBtnClass = '.xts-load-more.xts-type-blog.xts-action-infinite';
		var process = false;

		XTSThemeModule.clickOnScrollButton(infiniteBtnClass, false);

		$('.xts-load-more.xts-type-blog').on('click', function(e) {
			e.preventDefault();

			if (process) {
				return;
			}

			process = true;

			var $this = $(this);
			var $holder = $this.parent().parent().find('.xts-blog');
			var source = $holder.data('source');
			var ajaxurl = xts_settings.ajaxurl;
			var paged = $holder.data('paged');
			var atts = $holder.data('atts');
			var method = 'POST';

			$this.addClass('xts-loading');

			var data = {
				paged : paged,
				atts  : atts,
				action: 'xts_get_blog_' + source
			};

			if ('main_loop' === source) {
				ajaxurl = $this.attr('href');
				method = 'GET';
				data = {
					loop: $holder.find('.xts-col').last().data('loop')
				};
			} else {
				data.atts.loop = $holder.find('.xts-col').last().data('loop');
			}

			$.ajax({
				url     : ajaxurl,
				data    : data,
				dataType: 'json',
				method  : method,
				success : function(data) {
					if (data.items) {
						if ($holder.hasClass('xts-masonry-layout')) {
							var items = $(data.items);
							$holder.append(items).isotope('appended', items);
							$holder.imagesLoaded().progress(function() {
								$holder.isotope('layout');
							});
						} else {
							$holder.append(data.items);
						}

						XTSThemeModule.$document.trigger('xtsBlogLoadMoreSuccess');

						$holder.imagesLoaded().progress(function () {
							XTSThemeModule.clickOnScrollButton(infiniteBtnClass, true);
						});

						$holder.data('paged', paged + 1);
						window.history.pushState('', '', data.currentPage);

						if ('main_loop' === source) {
							$this.attr('href', data.nextPage);
						}
					}

					if ('no-more-posts' === data.status) {
						$this.remove();
					}
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() {
					$this.removeClass('xts-loading');
					process = false;
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.blogLoadMore();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsBlogLoadMoreSuccess', function() {
		XTSThemeModule.postVideoControls();
	});

	$.each([
		'frontend/element_ready/xts_blog.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.postVideoControls();
		});
	});

	XTSThemeModule.postVideoControls = function() {
		$('.xts-post-controls .xts-play').on('click', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $parent = $this.parents('.xts-post-thumb').find('.xts-post-video');
			var $video = $parent.find('iframe');
			if ($video.length === 0) {
				$video = $parent.find('video');
			}

			if ($parent.hasClass('xts-loaded')) {
				return;
			}

			var videoScr = $video.data('lazy-load');

			if (videoScr.indexOf('vimeo.com') + 1) {
				videoScr = videoScr.replace('#t=', '') + '&autoplay=1';
			} else if (videoScr.indexOf('youtube.com') + 1) {
				videoScr = videoScr + '&autoplay=1&rel=0';
			}

			$video.attr('src', videoScr);
			$parent.addClass('xts-loaded');
			$this.addClass('xts-loading');

			if ($parent.hasClass('xts-post-video-youtube')) {
				if ('undefined' === typeof YT || 'undefined' === typeof YT.Player) {
					var interval;

					$.getScript('https://www.youtube.com/player_api', function() {
						interval = setInterval(function() {
							if ('undefined' !== typeof YT.Player) {
								clearInterval(interval);
								youtubePostVideoControls($parent);
								$this.removeClass('xts-loading');
								XTSThemeModule.$document.trigger('xtsPostVideoLoaded');
							}
						}, 100);
					});
				} else {
					youtubePostVideoControls($parent);
					$this.removeClass('xts-loading');
				}
			} else if ($parent.hasClass('xts-post-video-html5')) {
				hostedPostVideoControls($parent);
				$this.removeClass('xts-loading');
				XTSThemeModule.$document.trigger('xtsPostVideoLoaded');
				$this.trigger('click');
				$this.parents('.xts-post').addClass('xts-video-playing');
			} else if ($parent.hasClass('xts-post-video-vimeo')) {
				if ('undefined' === typeof Vimeo || 'undefined' === typeof Vimeo.Player) {
					$.getScript(xts_settings.vimeo_library_url, function() {
						vimeoPostVideoControls($parent);
						$this.removeClass('xts-loading');
						XTSThemeModule.$document.trigger('xtsPostVideoLoaded');
					});
				} else {
					vimeoPostVideoControls($parent);
					$this.removeClass('xts-loading');
					XTSThemeModule.$document.trigger('xtsPostVideoLoaded');
				}
				$this.trigger('click');
				$this.parents('.xts-post').addClass('xts-video-playing');
			}
		});

		function youtubePostVideoControls($parent) {
			var $video = $parent.find('iframe');
			var $wrapper = $video.parents('.xts-post');
			var $playBtn = $wrapper.find('.xts-post-control.xts-play');
			var $muteBtn = $wrapper.find('.xts-post-control.xts-mute');
			var player;

			player = new YT.Player($video[0], {
				events: {
					'onReady': onPlayerReady
				}
			});

			function onPlayerReady() {
				$playBtn.on('click', function() {
					if ($wrapper.hasClass('xts-video-playing')) {
						$wrapper.removeClass('xts-video-playing');
						player.pauseVideo();
					} else {
						$wrapper.addClass('xts-video-playing');
						player.playVideo();
					}
				});

				$muteBtn.on('click', function() {
					if ($wrapper.hasClass('xts-video-muted')) {
						$wrapper.removeClass('xts-video-muted');
						player.unMute();
					} else {
						$wrapper.addClass('xts-video-muted');
						player.mute();
					}
				});

				$playBtn.trigger('click');
			}
		}

		function hostedPostVideoControls($parent) {
			var $video = $parent.find('video');
			var $wrapper = $video.parents('.xts-post');
			var $playBtn = $wrapper.find('.xts-post-control.xts-play');
			var $muteBtn = $wrapper.find('.xts-post-control.xts-mute');

			$playBtn.on('click', function() {
				if ($wrapper.hasClass('xts-video-playing')) {
					$wrapper.removeClass('xts-video-playing');
					$video[0].pause();
				} else {
					$wrapper.addClass('xts-video-playing');
					$video[0].play();
				}
			});

			$muteBtn.on('click', function() {
				if ($wrapper.hasClass('xts-video-muted')) {
					$wrapper.removeClass('xts-video-muted');
					$video.prop('muted', false);
				} else {
					$wrapper.addClass('xts-video-muted');
					$video.prop('muted', true);
				}
			});
		}

		function vimeoPostVideoControls($parent) {
			var $video = $parent.find('iframe');
			var $wrapper = $video.parents('.xts-post');
			var $playBtn = $wrapper.find('.xts-post-control.xts-play');
			var $muteBtn = $wrapper.find('.xts-post-control.xts-mute');
			var player = new Vimeo.Player($video[0]);

			$playBtn.on('click', function() {
				if ($wrapper.hasClass('xts-video-playing')) {
					$wrapper.removeClass('xts-video-playing');
					player.pause();
				} else {
					$wrapper.addClass('xts-video-playing');
					player.play();
				}
			});

			$muteBtn.on('click', function() {
				if ($wrapper.hasClass('xts-video-muted')) {
					$wrapper.removeClass('xts-video-muted');
					player.setVolume(1);
				} else {
					$wrapper.addClass('xts-video-muted');
					player.setVolume(0);
				}
			});
		}
	};

	$(document).ready(function() {
		XTSThemeModule.postVideoControls();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsProductTabLoaded xtsPjaxComplete', function() {
		XTSThemeModule.productsLoadMore();
	});

	XTSThemeModule.productsLoadMore = function() {
		var infiniteBtnClass = '.xts-load-more.xts-type-shop.xts-action-infinite';
		var process = false;
		var intervalID;

		$('.xts-products').each(function() {
			var $this = $(this);
			var cache = [];
			var id = $this.attr('id');
			var $loadWrap = $('.xts-arrows-loader[data-id="' + id + '"]');
			var $btnWrap = $('.xts-ajax-arrows[data-id="' + id + '"]');

			if ($btnWrap.length <= 0) {
				return;
			}

			cache[1] = {
				items : $this.html(),
				status: 'have-posts'
			};

			XTSThemeModule.$window.on('scroll', function() {
				arrowsPosition();
			});

			setTimeout(function() {
				arrowsPosition();
			}, 500);

			function arrowsPosition() {
				if ($btnWrap.length <= 0) {
					return;
				}

				var offset = XTSThemeModule.$window.height() / 2;
				var scrollTop = XTSThemeModule.$window.scrollTop();
				var holderTop = $this.offset().top - offset;
				var $btnPrev = $btnWrap.find('.xts-prev');
				var btnsHeight = $btnPrev.outerHeight();
				var holderHeight = $this.height() - btnsHeight;
				var holderBottom = holderTop + holderHeight;

				if (scrollTop < holderTop || scrollTop > holderBottom) {
					$btnWrap.removeClass('xts-shown');
					$loadWrap.removeClass('xts-shown');
				} else {
					$btnWrap.addClass('xts-shown');
					$loadWrap.addClass('xts-shown');
				}
			}

			$('.xts-ajax-arrows .xts-prev, .xts-ajax-arrows .xts-next').on('click', function(e) {
				e.preventDefault();
				var $this = $(this);

				if (process || $this.hasClass('xts-disabled')) {
					return;
				}

				process = true;

				clearInterval(intervalID);

				var id = $this.parent().data('id');
				var $holder = $('#' + id);
				var source = $holder.data('source');
				var ajaxurl = xts_settings.ajaxurl;
				var atts = $holder.data('atts');
				var paged = $holder.data('paged');
				var method = 'POST';
				var $next = $this.parent().find('.xts-next');
				var $prev = $this.parent().find('.xts-prev');

				paged++;

				if ($this.hasClass('xts-prev')) {
					if (paged < 2) {
						return;
					}

					paged = paged - 2;
				}

				var data = {
					paged : paged,
					atts  : atts,
					action: 'xts_get_product_' + source
				};

				data.atts.loop = $holder.find('.xts-col').last().data('loop');

				loadProducts(ajaxurl, data, method, $this, cache, paged, $holder, function(data) {
					if (data.items) {
						if ($holder.hasClass('xts-masonry-layout')) {
							var items = $(data.items);
							$holder.html(items).isotope('appended', items);
							$holder.imagesLoaded().progress(function() {
								$holder.isotope('layout');
							});
						} else {
							$holder.html(data.items);
						}

						reInit();

						$holder.data('paged', paged);
					}

					if (XTSThemeModule.isMobileSize) {
						$('html, body').stop().animate({
							scrollTop: $holder.offset().top - 150
						}, 400);
					}

					if (paged > 1) {
						$prev.removeClass('xts-disabled');
					} else {
						$prev.addClass('xts-disabled');
					}

					if ('no-more-posts' === data.status) {
						$next.addClass('xts-disabled');
					} else {
						$next.removeClass('xts-disabled');
					}
				});

			});
		});

		// Load more button
		XTSThemeModule.clickOnScrollButton(infiniteBtnClass, false);

		$('.xts-load-more.xts-type-shop').on('click', function(e) {
			e.preventDefault();

			if (process) {
				return;
			}

			process = true;

			var $this = $(this);
			var id = $this.data('id');
			var $holder = $('#' + id);
			var source = $holder.data('source');
			var ajaxurl = xts_settings.ajaxurl;
			var atts = $holder.data('atts');
			var paged = $holder.data('paged');
			var method = 'POST';

			paged++;

			$this.addClass('xts-loading');

			var data = {
				paged : paged,
				atts  : atts,
				action: 'xts_get_product_' + source
			};

			if ('main_loop' === source) {
				ajaxurl = $this.attr('href');
				method = 'GET';
				data = {
					loop: $holder.find('.xts-col').last().data('loop')
				};
			} else {
				data.atts.loop = $holder.find('.xts-col').last().data('loop');
			}

			loadProducts(ajaxurl, data, method, $this, [], paged, $holder, function(data) {
				if (data.items) {
					if ($holder.hasClass('xts-masonry-layout')) {
						var items = $(data.items);
						$holder.append(items).isotope('appended', items);
						$holder.imagesLoaded().progress(function() {
							$holder.isotope('layout');
						});
					} else {
						$holder.append(data.items);
					}

					reInit();

					$holder.imagesLoaded().progress(function () {
						XTSThemeModule.clickOnScrollButton(infiniteBtnClass, true);
					});

					$holder.data('paged', paged);

					if ('main_loop' === source) {
						$this.attr('href', data.nextPage);
					}
				}

				if ('no-more-posts' === data.status) {
					$this.remove();
				}
			});
		});

		var loadProducts = function(ajaxurl, data, method, $btn, cache, paged, $holder, callback) {
			if (cache[paged]) {
				$holder.addClass('xts-loading');
				setTimeout(function() {
					callback(cache[paged]);
					$holder.removeClass('xts-loading');
					process = false;
				}, 300);
				return;
			}

			var id = $holder.attr('id');
			var $loader = $('.xts-arrows-loader[data-id="' + id + '"]');

			$loader.addClass('xts-loading');
			$holder.addClass('xts-loading');

			if ('GET' === method) {
				ajaxurl = XTSThemeModule.removeURLParameter(ajaxurl, 'loop');
			}

			$.ajax({
				url     : ajaxurl,
				data    : data,
				dataType: 'json',
				method  : method,
				success : function(data) {
					cache[paged] = data;
					callback(data);
					window.history.pushState('', '', data.currentPage);
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() {
					$btn.removeClass('xts-loading');
					$loader.removeClass('xts-loading');
					$holder.removeClass('xts-loading');
					process = false;
				}
			});
		};

		var reInit = function() {
			XTSThemeModule.$document.trigger('xtsProductLoadMoreReInit');
		};
	};

	$(document).ready(function() {
		XTSThemeModule.productsLoadMore();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.actionAfterAddToCart = function() {
		var closeAfterTimeoutNumber;
		var hoverTimeoutNumber = 0;

		XTSThemeModule.$body.on('added_to_cart', function() {
			if ('popup' === xts_settings.action_after_add_to_cart) {
				var html = [
					'<h4>' + xts_settings.action_after_add_to_cart_title + '</h4>',
					'<a href="#" class="xts-button xts-style-link xts-color-primary xts-close-popup">' + xts_settings.action_after_add_to_cart_continue_shopping + '</a>',
					'<a href="' + xts_settings.action_after_add_to_cart_cart_url + '" class="xts-button xts-color-primary xts-view-cart">' + xts_settings.action_after_add_to_cart_view_cart + '</a>'
				].join('');

				$.magnificPopup.open({
					items       : {
						src : '<div class="mfp-with-anim xts-popup-content xts-cart-popup">' + html + '</div>',
						type: 'inline'
					},
					tClose      : xts_settings.magnific_close,
					tLoading    : xts_settings.magnific_loading,
					removalDelay: 400,
					preloader   : false,
					callbacks   : {
						beforeOpen: function() {
							this.st.mainClass = 'xts-popup-effect';
						}
					}
				});

				$('.xts-popup-content').on('click', '.xts-close-popup', function(e) {
					e.preventDefault();
					$.magnificPopup.close();
				});

				closeAfterTimeout();
			} else if ('widget' === xts_settings.action_after_add_to_cart) {
				clearTimeout(hoverTimeoutNumber);

				if ($('.xts-sticked .xts-header-cart').length > 0) {
					$('.xts-sticked .xts-header-cart .xts-dropdown').addClass('xts-opened');
				} else {
					$('.xts-header-cart .xts-dropdown').addClass('xts-opened');
				}

				hoverTimeoutNumber = setTimeout(function() {
					$('.xts-header-cart .xts-dropdown').removeClass('xts-opened');
				}, 3500);

				var $opener = $('.xts-header-cart.xts-opener');
				if ($opener.length > 0) {
					$opener.first().trigger('click');
				}

				closeAfterTimeout();
			}
		});

		var closeAfterTimeout = function() {
			if ('no' === xts_settings.action_after_add_to_cart_timeout) {
				return false;
			}

			clearTimeout(closeAfterTimeoutNumber);

			closeAfterTimeoutNumber = setTimeout(function() {
				$('.xts-close-side').trigger('click');
				$.magnificPopup.close();
			}, parseInt(xts_settings.action_after_add_to_cart_timeout_number) * 1000);
		};
	};

	$(document).ready(function() {
		XTSThemeModule.actionAfterAddToCart();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function () {
		XTSThemeModule.ajaxSortByWidget();
	});

	XTSThemeModule.ajaxShop = function() {
		var ajaxLinks = '.xts-widget-filter a, .widget_product_categories:not(.xts-search-area-widget) a, .widget_layered_nav_filters a, .woocommerce-widget-layered-nav a,body.post-type-archive-product:not(.woocommerce-account) .woocommerce-pagination a, body.tax-product_cat:not(.woocommerce-account) .woocommerce-pagination a, .xts-clear-filters a, .xts-nav-shop-cat a, .xts-products-per-page a, .xts-wc-price-filter a, .xts-wc-sort-by a, .xts-filters-area:not(.xts-with-content) a, .xts-products-per-row a, .woocommerce-widget-layered-nav-list a, .xts-widget-stock-status a, .xts-shop-content .xts-cats a';

		if ('no' === xts_settings.ajaxShop || 'undefined' === typeof ($.fn.pjax)) {
			return;
		}

		var filtersState = false;

		XTSThemeModule.$body.on('click', '.post-type-archive-product .xts-shop-footer .woocommerce-pagination a', function() {
			scrollToTop(true);
		});

		XTSThemeModule.$body.on('click', '.xts-shop-content .xts-cats a', function() {
			scrollToTop(true);
		});

		XTSThemeModule.$document.pjax(ajaxLinks, '.xts-site-content', {
			timeout : xts_settings.pjax_timeout,
			scrollTo: false
		});

		XTSThemeModule.$document.on('submit', '.widget_price_filter form', function(event) {
			$.pjax.submit(event, {
				container: '.xts-site-content',
				timeout  : xts_settings.pjax_timeout,
				scrollTo : false
			});

			return false;
		});

		XTSThemeModule.$document.on('submit', '.xts-shop-tools .xts-search-form form.xts-opened, .xts-filters-area .xts-ajax-search form, .xts-filters-area .widget_product_search form, .xts-shop-widget-sidebar .xts-ajax-search form, .xts-shop-widget-sidebar .widget_product_search form', function(event) {
			var $form = $(this);

			if ($form.find('input[name="post_type"]').val() !== 'product') {
				return;
			}

			$.pjax.submit(event, {
				container: '.xts-site-content',
				timeout  : xts_settings.pjax_timeout,
				scrollTo : false
			});

			return false;
		});

		XTSThemeModule.$document.on('pjax:error', function(xhr, textStatus, error) {
			console.log('pjax error ' + error);
		});

		XTSThemeModule.$document.on('pjax:start', function() {
			$('.xts-ajax-content').removeClass('xts-loaded').addClass('xts-loading');
			XTSThemeModule.$document.trigger('xtsPjaxStart');
			XTSThemeModule.$window.trigger('scroll.loaderVerticalPosition');
		});

		XTSThemeModule.$document.on('pjax:complete', function() {
			XTSThemeModule.$window.off('scroll.loaderVerticalPosition');
			var $body = XTSThemeModule.$body;
			if ($body.hasClass('tax-xts-portfolio-cat') || $body.hasClass('post-type-archive-xts-portfolio')) {
				return;
			}

			XTSThemeModule.$document.trigger('xtsPjaxComplete');
			XTSThemeModule.$document.trigger('xtsImagesLoaded');

			// Init variations forms for quick shop after ajax (copied from woocommerce/assets/js/frontend/add-to-cart-variation.js?ver=3.7.0)
			$(function() {
				if (typeof wc_add_to_cart_variation_params !== 'undefined') {
					$('.variations_form').each(function() {
						$(this).wc_variation_form();
					});
				}
			});

			scrollToTop(false);

			$(document.body).trigger('wc_fragment_refresh');

			$('.xts-ajax-content').removeClass('xts-loading');
		});

		XTSThemeModule.$document.on('pjax:beforeReplace', function(contents, options) {
			var $data = $('<div class="temp-wrapper"></div>').append(options);
			$('meta[name="description"]').attr('content', $data.find('meta').attr('content'));

			if ($('.xts-filters-area').hasClass('xts-opened') && 'yes' === xts_settings.shop_filters_area_stop_close) {
				filtersState = true;
				XTSThemeModule.$body.addClass('xts-filters-opened');
			}
		});

		XTSThemeModule.$document.on('pjax:end', function() {
			$('.xts-site-content').find('meta').remove();
			if (filtersState) {
				$('.xts-filters-area').css('display', 'block');
				XTSThemeModule.openFilters(200);
				filtersState = false;
			}

			$('.xts-ajax-content').addClass('xts-loaded');
		});

		var scrollToTop = function(type) {
			if ('no' === xts_settings.ajax_shop_scroll && type === false) {
				return;
			}

			var $scrollTo = $(xts_settings.ajax_shop_scroll_class);
			var scrollTo = $scrollTo.offset().top - xts_settings.ajax_shop_scroll_offset;

			$('html, body').stop().animate({
				scrollTop: scrollTo
			}, 400);
		};
	};

	XTSThemeModule.ajaxSortByWidget = function () {
		if ('undefined' === typeof ($.fn.pjax)) {
			return;
		}

		var $widget = $('.woocommerce-ordering');

		$widget.on('change', 'select.orderby', function () {
			var $form = $(this).closest('form');

			$form.find('[name="_pjax"]').remove();

			$.pjax({
				container: '.xts-site-content',
				timeout: xts_settings.pjax_timeout,
				url: '?' + $form.serialize(),
				scrollTo: false
			});
		});

		$widget.submit(function (e) {
			e.preventDefault(e);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.ajaxShop();
		XTSThemeModule.ajaxSortByWidget();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.commentImage = function() {
		// This is a dirty method, but there is no hook in WordPress to add attributes to the commenting form.
		$('form.comment-form').attr('enctype', 'multipart/form-data');
	};

	XTSThemeModule.commentImagesUploadValidation = function() {
		var $form = $('.comment-form');
		var $input = $form.find('#xts-add-img-btn');
		var allowedMimes = [];

		if ($input.length === 0) {
			return;
		}

		$.each(xts_settings.comment_images_upload_mimes, function(index, value) {
			allowedMimes.push(String(value));
		});

		$input.on('change', function(e) {
			$form.find('.xts-add-img-count').text(xts_settings.comment_images_added_count_text.replace('%s', this.files.length));
		});

		$form.on('submit', function(e) {
			$form.find('.woocommerce-error').remove();

			var hasLarge = false;
			var hasNotAllowedMime = false;

			if ($input[0].files.length > xts_settings.comment_images_count) {
				showError(xts_settings.comment_images_count_text);
				e.preventDefault();
			}

			Array.prototype.forEach.call($input[0].files, function(file) {
				var size = file.size;
				var type = String(file.type);

				if (size > xts_settings.comment_images_upload_size) {
					hasLarge = true;
				}

				if ($.inArray(type, allowedMimes) < 0) {
					hasNotAllowedMime = true;
				}
			});

			if (hasLarge) {
				showError(xts_settings.comment_images_upload_size_text);
				e.preventDefault();
			}

			if (hasNotAllowedMime) {
				showError(xts_settings.comment_images_upload_mimes_text);
				e.preventDefault();
			}
		});

		function showError(text) {
			$form.append('<div class="comment-form-images-msg"><p class="woocommerce-error" role="alert">' + text + '</p><div>');
		}
	};

	$(document).ready(function() {
		XTSThemeModule.commentImage();
		XTSThemeModule.commentImagesUploadValidation();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.filtersArea = function() {
		var time = 200;

		XTSThemeModule.$body.on('click', '.xts-filters-area-btn', function(e) {
			e.preventDefault();

			if (isOpened()) {
				closeFilters();
			} else {
				XTSThemeModule.openFilters(time);
			}
		});

		if ('no' === xts_settings.shop_filters_area_stop_close) {
			XTSThemeModule.$document.on('pjax:start', function() {
				if (isOpened()) {
					closeFilters();
				}
			});
		}

		var isOpened = function() {
			return $('.xts-filters-area').hasClass('xts-opened');
		};

		var closeFilters = function() {
			$('.xts-filters-area').removeClass('xts-opened').stop().slideUp(time);
		};
	};

	XTSThemeModule.openFilters = function(time) {
		$('.xts-filters-area').stop().slideDown(time);
		XTSThemeModule.$body.removeClass('xts-filters-opened');

		setTimeout(function() {
			$('.xts-filters-area').addClass('xts-opened');
			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		}, time);
	};

	$(document).ready(function() {
		XTSThemeModule.filtersArea();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorProductTabsReady', function() {
		XTSThemeModule.gridSwatches();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.gridSwatches();
		});
	});

	XTSThemeModule.gridSwatches = function() {
		XTSThemeModule.$body.on('click', '.xts-loop-swatch', function() {
			var src, srcset, image_sizes;

			var $this = $(this);
			var imageSrc = $this.data('image-src');
			var imageSrcset = $this.data('image-srcset');
			var imageSizes = $this.data('image-sizes');

			if (typeof imageSrc == 'undefined' || '' === imageSrc) {
				return;
			}

			var $product = $this.parents('.xts-product');
			var $image = $product.find('.xts-product-image img');
			var srcOrig = $image.attr('original-src');
			var srcsetOrig = $image.attr('original-srcset');
			var sizesOrig = $image.attr('original-sizes');

			if (typeof srcOrig == 'undefined') {
				$image.attr('original-src', $image.attr('src'));
			}

			if (typeof srcsetOrig == 'undefined') {
				$image.attr('original-srcset', $image.attr('srcset'));
			}

			if (typeof sizesOrig == 'undefined') {
				$image.attr('original-sizes', $image.attr('sizes'));
			}

			if ($this.hasClass('xts-active')) {
				src = srcOrig;
				srcset = srcsetOrig;
				image_sizes = sizesOrig;

				$this.removeClass('xts-active');
				$product.removeClass('xts-product-swatched');
			} else {
				src = imageSrc;
				srcset = imageSrcset;
				image_sizes = imageSizes;

				$this.parent().find('.xts-active').removeClass('xts-active');
				$this.addClass('xts-active');
				$product.addClass('xts-product-swatched');
			}

			$product.addClass('xts-loading');

			$image.attr('src', src).attr('srcset', srcset).attr('image_sizes', image_sizes).one('load', function() {
				$product.removeClass('xts-loading');
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.gridSwatches();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.layeredNavDropdown();
	});

	XTSThemeModule.layeredNavDropdown = function() {
		$('.xts-widget-layered-nav-dropdown-form').each(function() {
			var $form = $(this);
			var $select = $form.find('select');
			var slug = $select.data('slug');

			$select.change(function() {
				var val = $(this).val();
				$('input[name=filter_' + slug + ']').val(val);
			});

			if ($().selectWoo) {
				$select.selectWoo({
					placeholder            : $select.data('placeholder'),
					minimumResultsForSearch: 5,
					width                  : '100%',
					allowClear             : !$select.attr('multiple'),
					language               : {
						noResults: function() {
							return $select.data('noResults');
						}
					}
				}).on('select2:unselecting', function() {
					$(this).data('unselecting', true);
				}).on('select2:opening', function(e) {
					if ($(this).data('unselecting')) {
						$(this).removeData('unselecting');
						e.preventDefault();
					}
				});
			}
		});

		function ajaxAction($element) {
			var $form = $element.parent('.xts-widget-layered-nav-dropdown-form');
			if ('no' === xts_settings.ajax_shop || typeof ($.fn.pjax) == 'undefined') {
				return;
			}

			$.pjax({
				container: '.xts-site-content',
				timeout  : xts_settings.pjax_timeout,
				url      : $form.attr('action'),
				data     : $form.serialize(),
				scrollTo : false
			});
		}

		$('.xts-widget-layered-nav-dropdown__submit').on('click', function(e) {
			var $this = $(this);
			if (!$this.siblings('select').attr('multiple') || 'no' === xts_settings.ajax_shop) {
				return;
			}

			ajaxAction($this);

			$this.prop('disabled', true);
		});

		$('.xts-widget-layered-nav-dropdown-form select').on('change', function(e) {
			var $this = $(this);
			if ('no' === xts_settings.ajax_shop) {
				$this.parent().submit();
				return;
			}

			if ($this.attr('multiple')) {
				return;
			}

			ajaxAction($(this));
		});
	};

	$(document).ready(function() {
		XTSThemeModule.layeredNavDropdown();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.miniCartQuantity = function() {
		var timeout;

		XTSThemeModule.$document.on('change input', '.woocommerce-mini-cart .quantity .qty', function() {
			var input = $(this);
			var qtyVal = input.val();
			var itemID = input.parents('.woocommerce-mini-cart-item').data('key');
			var cart_hash_key = xts_settings.cart_hash_key;
			var fragment_name = xts_settings.fragment_name;

			clearTimeout(timeout);

			timeout = setTimeout(function() {
				input.parents('.mini_cart_item').addClass('xts-loading');

				$.ajax({
					url     : xts_settings.ajaxurl,
					data    : {
						action : 'xts_update_mini_cart_item',
						item_id: itemID,
						qty    : qtyVal
					},
					dataType: 'json',
					method  : 'GET',
					success : function(data) {
						if (data && data.fragments) {

							$.each(data.fragments, function(key, value) {
								$(key).replaceWith(value);
							});

							if (XTSThemeModule.supports_html5_storage) {
								sessionStorage.setItem(fragment_name, JSON.stringify(data.fragments));
								localStorage.setItem(cart_hash_key, data.cart_hash);
								sessionStorage.setItem(cart_hash_key, data.cart_hash);

								if (data.cart_hash) {
									sessionStorage.setItem('wc_cart_created', (new Date()).getTime());
								}
							}
						}
					}
				});
			}, 500);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.miniCartQuantity();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.offCanvasCartWidget = function () {
		var $closeSide = $('.xts-close-side');
		var $widget = $('.xts-cart-widget-side');
		var $body = XTSThemeModule.$body;

		$body.on('click', '.xts-header-cart.xts-opener, .xts-navbar-cart.xts-opener', function (e) {
			if (!isCart() && !isCheckout()) {
				e.preventDefault();
			}

			if ($widget.hasClass('xts-opened')) {
				hideWidget();
			} else {
				showWidget();
			}
		});

		$body.on('click touchstart', '.xts-close-side, .xts-close-button', function () {
			hideWidget();
		});

		XTSThemeModule.$document.keyup(function (e) {
			if (27 === e.keyCode) {
				hideWidget();
			}
		});

		var showWidget = function () {
			XTSThemeModule.$document.trigger('xtsOffCanvasCartWidgetShown');

			if (isCart() || isCheckout()) {
				return false;
			}

			$widget.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
		};

		var hideWidget = function () {
			$widget.removeClass('xts-opened');
			$closeSide.removeClass('xts-opened');
		};

		var isCart = function () {
			return XTSThemeModule.$body.hasClass('woocommerce-cart');
		};

		var isCheckout = function () {
			return XTSThemeModule.$body.hasClass('woocommerce-checkout');
		};
	};

	$(document).ready(function() {
		XTSThemeModule.offCanvasCartWidget();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.offCanvasMyAccount = function () {
		var $closeSide = $('.xts-close-side');
		var $element = $('.xts-login-side');
		var $body = XTSThemeModule.$body;

		$body.on('click', '.xts-header-my-account.xts-opener, .xts-login-to-price-msg.xts-opener, .xts-menu-item-account.xts-opener, .xts-navbar-my-account.xts-opener', function (e) {
			e.preventDefault();

			if ($element.hasClass('xts-opened')) {
				hideWidget();
			} else {
				setTimeout(function() {
					showWidget();
				}, 100);
			}
		});

		$body.on('click touchstart', '.xts-close-side, .xts-close-button', function () {
			hideWidget();
		});

		XTSThemeModule.$document.keyup(function (e) {
			if (27 === e.keyCode) {
				hideWidget();
			}
		});

		var showWidget = function () {
			XTSThemeModule.$document.trigger('xtsOffCanvasMyAccountShown');
			$element.addClass('xts-opened');
			$closeSide.addClass('xts-opened');
		};

		var hideWidget = function () {
			$element.removeClass('xts-opened');
			$closeSide.removeClass('xts-opened');
		};

		if ( $element.find('.woocommerce-notices-wrapper > ul').length > 0 ) {
			showWidget();
		}
	};

	$(document).ready(function() {
		XTSThemeModule.offCanvasMyAccount();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.pageTitleProductCategoriesMenuBtns();
	});

	XTSThemeModule.pageTitleProductCategories = function() {
		if (XTSThemeModule.isDesktop) {
			return;
		}

		var time = 200;
		var $body = XTSThemeModule.$body;

		$body.on('click', '.xts-nav-shop-cat .xts-show-cat-btn, .xts-nav-shop-cat .xts-submenu-opener', function(e) {
			e.preventDefault();
			var $this = $(this);

			if ($this.hasClass('xts-opened')) {
				$this.removeClass('xts-opened').siblings('.xts-dropdown').slideUp(time);
			} else {
				$this.addClass('xts-opened').siblings('.xts-dropdown').slideDown(time);
			}
		});

		$body.on('click', '.xts-show-cat-btn', function(e) {
			e.preventDefault();

			if (isOpened()) {
				closeCats();
			} else {
				openCats();
			}
		});

		$body.on('click', '.xts-nav-shop-cat a', function(e) {
			if (!$(e.target).hasClass('xts-show-cat-btn')) {
				closeCats();
				$('.xts-nav-shop-cat').stop().attr('style', '');
			}
		});

		var isOpened = function() {
			return $('.xts-nav-shop-cat').hasClass('xts-opened');
		};

		var openCats = function() {
			$('.xts-nav-shop-cat').addClass('xts-opened').stop().slideDown(time);
			$('.xts-show-cat-btn').addClass('xts-opened');
		};

		var closeCats = function() {
			$('.xts-nav-shop-cat').removeClass('xts-opened').stop().slideUp(time);
			$('.xts-show-cat-btn').removeClass('xts-opened');
		};
	};

	XTSThemeModule.pageTitleProductCategoriesMenuBtns = function() {
		if (XTSThemeModule.isDesktop) {
			return;
		}

		$('.xts-nav-shop-cat .xts-has-children').prepend('<span class="xts-submenu-opener"></span>');
	};

	$(document).ready(function() {
		XTSThemeModule.pageTitleProductCategories();
		XTSThemeModule.pageTitleProductCategoriesMenuBtns();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.productCategoriesWidgetAccordion();
	});

	XTSThemeModule.productCategoriesWidgetAccordion = function() {
		var $widget = $('.widget_product_categories');
		var $list = $widget.find('.product-categories');
		var time = 300;

		$('.dropdown_product_cat').on('change', function() {
			if ($(this).val() !== '') {
				var this_page;
				var home_url = xts_settings.home_url;

				if (home_url.indexOf('?') > 0) {
					this_page = home_url + '&product_cat=' + jQuery(this).val();
				} else {
					this_page = home_url + '?product_cat=' + jQuery(this).val();
				}

				location.href = this_page;
			} else {
				location.href = xts_settings.shop_url;
			}
		});

		$widget.each(function() {
			var $select = $(this).find('select');

			if ($().selectWoo) {
				$select.selectWoo({
					minimumResultsForSearch: 5,
					width                  : '100%',
					allowClear             : true,
					placeholder            : xts_settings.product_categories_placeholder,
					language               : {
						noResults: function() {
							return xts_settings.product_categories_no_results;
						}
					}
				});
			}
		});

		if ('no' === xts_settings.product_categories_widget_accordion) {
			return;
		}

		$list.find('.cat-parent').each(function() {
			var $this = $(this);
			if ($this.find(' > .xts-cats-toggle').length > 0 || $this.find(' > .children').length === 0) {
				return;
			}

			$this.find('> ul').before('<div class="xts-cats-toggle"></div>');
		});

		$list.on('click', '.xts-cats-toggle', function() {
			var $btn = $(this);
			var $subList = $btn.next();

			if ($subList.hasClass('xts-shown')) {
				$btn.removeClass('xts-active');
				$subList.stop().slideUp(time).removeClass('xts-shown');
			} else {
				$subList.parent().parent().find('> li > .xts-shown').slideUp().removeClass('xts-shown');
				$subList.parent().parent().find('> li > .xts-active').removeClass('xts-active');
				$btn.addClass('xts-active');
				$subList.stop().slideDown(time).addClass('xts-shown');
			}
		});

		if ($list.find('li.current-cat.cat-parent, li.current-cat-parent').length > 0) {
			$list.find('li.current-cat.cat-parent, li.current-cat-parent').find('> .xts-cats-toggle').click();
		}

		$widget.addClass('xts-loaded');
	};

	$(document).ready(function() {
		XTSThemeModule.productCategoriesWidgetAccordion();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsSingleProductAccordionClick xtsWishlistRemoveSuccess xtsProductTabLoaded xtsElementorProductTabsReady xtsPjaxComplete xtsProductLoadMoreReInit', function() {
		XTSThemeModule.productHoverSummary();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.productHoverSummary();
		});
	});

	XTSThemeModule.productHoverSummary = function() {
		var $summaryHover = $('.xts-prod-design-summary .xts-col, .xts-prod-design-summary-alt .xts-col');
		$summaryHover.on('mouseenter mousemove touchstart', function() {
			var $product = $(this).find('.xts-product');
			var $content = $product.find('.xts-more-desc');

			if ($content.hasClass('xts-height-calculated')) {
				return;
			}

			$product.imagesLoaded(function() {
				productHoverSummaryRecalc($product);
			});

			productHoverSummaryRecalc($product);

			$content.addClass('xts-height-calculated');
		});

		$summaryHover.on('click', '.xts-more-desc-btn', function(e) {
			e.preventDefault();
			productHoverSummaryRecalc($(this).parents('.xts-product'));
		});

		function productHoverSummaryMoreBtn() {
			$('.xts-prod-design-summary .xts-col, .xts-prod-design-summary-alt .xts-col, .xts-prod-design-summary-alt-2 .xts-col').on('mouseenter touchstart', function() {
				var $product = $(this).find('.xts-product');
				var $content = $product.find('.xts-more-desc');
				var $moreBtn = $content.find('.xts-more-desc-btn');
				var $inner = $content.find('.xts-more-desc-inner');

				if ($content.hasClass('xts-more-desc-calculated')) {
					return;
				}

				var contentHeight = $content.outerHeight();
				var innerHeight = $inner.outerHeight();
				var delta = innerHeight - contentHeight;

				if (delta > 10) {
					$moreBtn.addClass('xts-shown');
				} else if (delta > 0) {
					$content.css('height', contentHeight + delta);
				}

				$content.addClass('xts-more-desc-calculated');
			});

			$('.xts-more-desc-btn').on('click', function(e) {
				e.preventDefault();
				$(this).parent().addClass('xts-opened');
			});
		}

		function productHoverSummaryRecalc($product) {
			if ($product.parents('.xts-carousel').length > 0) {
				return;
			}

			var heightHideInfo = $product.find('.xts-product-hide-info').outerHeight();

			$product.find('.xts-product-bg').css({
				marginBottom: -heightHideInfo
			});

			$product.addClass('xts-ready');
		}

		productHoverSummaryMoreBtn();
	};

	$(document).ready(function() {
		XTSThemeModule.productHoverSummary();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsWishlistRemoveSuccess xtsProductTabLoaded xtsElementorProductTabsReady xtsProductLoadMoreReInit xtsMenuDropdownsAJAXRenderResults xtsPjaxComplete', function () {
		XTSThemeModule.productLoopQuantity();
	});

	XTSThemeModule.productLoopQuantity = function() {
		$('.xts-products .xts-product').on('change input', '.quantity .qty', function() {
			var add_to_cart_button = $(this).parents('.xts-product').find('.add_to_cart_button');
			add_to_cart_button.attr('data-quantity', $(this).val());
			add_to_cart_button.attr('href', '?add-to-cart=' + add_to_cart_button.attr('data-product_id') + '&quantity=' + $(this).val());
		});
	};

	$(document).ready(function() {
		XTSThemeModule.productLoopQuantity();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.productQuickView();
		});
	});

	XTSThemeModule.productQuickView = function() {
		XTSThemeModule.$document.on('click', '.xts-quick-view-btn a', function(e) {
			e.preventDefault();

			if ($('.xts-quick-view-btn a').hasClass('xts-loading')) {
				return true;
			}

			var $btn = $(this);
			var productId = $btn.data('id');
			var data = {
				id    : productId,
				action: 'xts_quick_view'
			};

			$btn.addClass('xts-loading');

			var initPopup = function(data) {
				$.magnificPopup.open({
					items       : {
						src : '<div class="mfp-with-anim xts-popup-content xts-quick-view-popup">' + data + '</div>',
						type: 'inline'
					},
					tClose      : xts_settings.magnific_close,
					tLoading    : xts_settings.magnific_loading,
					removalDelay: 400, //delay removal by X to allow out-animation
					preloader   : false,
					callbacks   : {
						beforeOpen: function() {
							this.st.mainClass = 'xts-popup-effect';
						},
						open      : function() {
							var $variationsForm = $('.xts-quick-view-popup .variations_form');
							$variationsForm.wc_variation_form().find('.variations select:eq(0)').change();
							$variationsForm.trigger('wc_variation_form');

							XTSThemeModule.$document.trigger('xtsProductQuickViewOpen');
						}
					}
				});
			};

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : data,
				method  : 'get',
				success : function(data) {
					if (xts_settings.quick_view_in_popup_fix) {
						$.magnificPopup.close();
						setTimeout(function() {
							initPopup(data);
						}, 500);
					} else {
						initPopup(data);
					}
				},
				complete: function() {
					$btn.removeClass('xts-loading');
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.productQuickView();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.productsCompare();
	});

	XTSThemeModule.productsCompare = function() {
		if ('undefined' === typeof Cookies) {
			return;
		}

		var cookiesName = 'xts_compare_list';

		if (xts_settings.is_multisite) {
			cookiesName += '_' + xts_settings.current_blog_id;
		}

		var $body = XTSThemeModule.$body;
		var $widget = $('.xts-header-el.xts-header-compare, .xts-navbar-compare');
		var cookie = Cookies.get(cookiesName);

		if ($widget.length > 0 && 'undefined' !== typeof cookie) {
			try {
				var ids = JSON.parse(cookie);
				$widget.find('.xts-compare-count, .xts-navbar-count').text(ids.length);
			}
			catch (e) {
				console.log('cant parse cookies json');
			}
		}

		// Add to compare action
		$body.on('click', '.xts-compare-btn a', function(e) {
			var $this = $(this);
			var id = $this.data('id');
			var addedText = $this.data('added-text');

			if ($this.hasClass('xts-added')) {
				return true;
			}

			e.preventDefault();

			$this.addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action: 'xts_add_to_compare',
					id    : id
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					XTSThemeModule.$document.trigger('xtsAddedToCompare');
					if (response.table) {
						updateCompare(response);
					} else {
						console.log('something wrong loading compare data ',
							response);
					}
				},
				error   : function() {
					console.log(
						'We cant add to compare. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.removeClass('xts-loading').addClass('xts-added');

					if ($this.find('span').length > 0) {
						$this.find('span').text(addedText);
					} else {
						$this.text(addedText);
					}
				}
			});

		});

		// Remove from compare action
		$body.on('click', '.xts-compare-remove a', function(e) {
			e.preventDefault();

			var $this = $(this);
			var id = $this.data('id');

			$this.addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action: 'xts_remove_from_compare',
					id    : id
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response.table) {
						updateCompare(response);
					} else {
						console.log('something wrong loading compare data ',
							response);
					}
				},
				error   : function() {
					console.log(
						'We cant remove product compare. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.addClass('xts-loading');
				}
			});

		});

		// Elements update after ajax
		function updateCompare(data) {
			if ($widget.length > 0) {
				$widget.find('.xts-compare-count, .xts-navbar-count').text(data.count);
			}

			var $table = $('.xts-compare-table');
			if ($table.length > 0) {
				$table.replaceWith(data.table);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.productsCompare();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_product_tabs.default', function() {
		XTSThemeModule.productsTabs();
		XTSThemeModule.$document.trigger('xtsElementorProductTabsReady');
		XTSThemeModule.$document.trigger('xts_countDownTimer');
	});

	XTSThemeModule.productsTabs = function() {
		var process = false;

		$('.xts-products-tabs').each(function() {
			var $wrapper = $(this);
			var $content = $wrapper.find('.xts-products-tab-content');
			var cache = [];

			cache[0] = {
				html: $content.html()
			};

			$wrapper.find('.xts-products-tab-title').on('click', function(e) {
				e.preventDefault();

				var $this = $(this);
				var atts = $this.data('atts');
				var index = $this.index();

				if (process || $this.hasClass('xts-active')) {
					return;
				}

				process = true;

				loadTab(atts, index, $content, $this, cache, function(data) {
					var itemQueue = [];
					var queueTimer;

					// Animations
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

					if (data.html) {
						$content.html(data.html);

						if ($content.find('.xts-products.xts-in-view-animation').length > 0 ) {
							$content.find('.xts-products').removeClass('xts-inited xts-loaded');

							// Animations
							if (typeof ($.fn.xtsWaypoint) !== 'undefined') {
								$content.find('.xts-col').each(function() {
									var $element = $(this);

									$element.data('xts-waypoint', 'inited');

									$element.xtsWaypoint(function() {
										var $this = $($(this)[0].element);
										var delay = $this.parents('.xts-in-view-animation').data('animation-delay');

										$this.addClass('xts-animation-ready');

										itemQueue.push($this);
										processItemQueue(delay);

									}, {
										offset: '90%'
									});
								});
							}
						}

						XTSThemeModule.$document.trigger('xtsProductTabLoaded');
						XTSThemeModule.$document.trigger('xtsImagesLoaded');
					}
				});
			});
		});

		var loadTab = function(atts, index, $holder, $btn, cache, callback) {
			$btn.parent().find('.xts-active').removeClass('xts-active');
			$btn.addClass('xts-active');

			if (cache[index]) {
				$holder.addClass('xts-loading');
				setTimeout(function() {
					callback(cache[index]);
					$holder.removeClass('xts-loading');
					process = false;
				}, 300);
				return;
			}

			$holder.addClass('xts-loading').parent().addClass('xts-loading');

			$btn.addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					atts  : atts,
					action: 'xts_get_products_tab_element'
				},
				dataType: 'json',
				method  : 'POST',
				success : function(data) {
					cache[index] = data;
					callback(data);
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() {
					$holder.removeClass('xts-loading').parent().removeClass('xts-loading');
					$btn.removeClass('xts-loading');
					process = false;
				}
			});
		};
	};

	$(document).ready(function() {
		XTSThemeModule.productsTabs();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.quantity = function() {
		if (!String.prototype.getDecimals) {
			String.prototype.getDecimals = function() {
				var num   = this,
				    match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
				if (!match) {
					return 0;
				}
				return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
			};
		}

		XTSThemeModule.$document.on('click', '.xts-plus, .xts-minus', function() {
			// Get values
			var $this = $(this);
			var $qty = $this.closest('.quantity').find('.qty');
			var currentVal = parseFloat($qty.val());
			var max = parseFloat($qty.attr('max'));
			var min = parseFloat($qty.attr('min'));
			var step = $qty.attr('step');

			// Format values
			if (!currentVal || '' === currentVal || 'NaN' === currentVal) {
				currentVal = 0;
			}
			if ('' === max || 'NaN' === max) {
				max = '';
			}
			if ('' === min || 'NaN' === min) {
				min = 0;
			}
			if ('any' === step || '' === step || undefined === step || 'NaN' === parseFloat(step)) {
				step = '1';
			}

			// Change the value
			if ($this.is('.xts-plus')) {
				if (max && (currentVal >= max)) {
					$qty.val(max);
				} else {
					$qty.val((currentVal + parseFloat(step)).toFixed(step.getDecimals()));
				}
			} else {
				if (min && (currentVal <= min)) {
					$qty.val(min);
				} else if (currentVal > 0) {
					$qty.val((currentVal - parseFloat(step)).toFixed(step.getDecimals()));
				}
			}

			// Trigger change event
			$qty.trigger('change');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.quantity();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsProductTabLoaded xtsElementorProductTabsReady xtsWishlistRemoveSuccess xtsProductLoadMoreReInit xtsPjaxComplete xtsMenuDropdownsAJAXRenderResults', function() {
		XTSThemeModule.quickShop();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.quickShop();
		});
	});

	XTSThemeModule.quickShop = function() {
		var $variationsForms = $('.xts-product-variations .xts-variations_form');

		$variationsForms.each(function() {
			var $form             = $(this),
			    $product          = $form.parents('.xts-product'),
			    $img              = $product.find('.xts-product-image img'),
			    originalSrc       = $img.hasClass('xts-lazy-load') ? $img.attr('data-xts-src') : $img.attr('src'),
			    originalSrcSet    = $img.attr('srcset'),
			    originalSizes     = $img.attr('sizes'),
			    $btn              = $product.find('.product_type_variable'),
			    originalBtnText   = $btn.text(),
			    $price            = $product.find('.price').first(),
			    priceOriginalHtml = $price.html(),
			    addToCartText     = xts_settings.quick_shop_add_to_cart_text;

			if ($form.hasClass('xts-quick-inited')) {
				return;
			}

			$product.on('mouseenter touchstart', function() {
				if ($form.hasClass('xts-wc-variations-inited')) {
					return;
				}

				$form.wc_variation_form();

				$form.addClass('xts-wc-variations-inited');
			});

			// first click
			$form.on('click', '.xts-variation-swatch', function() {
					firstInteraction($form);
				})
				.on('change', 'select', function() {
					firstInteraction($form);
				})
				.on('show_variation', function(event, variation, purchasable) {
					$product.addClass('xts-variation-active');

					if (variation.price_html.length > 1) {
						$price.html(variation.price_html);
					}

					if (variation.image.thumb_src.length > 1) {
						$img.attr('src', variation.image.thumb_src);
					}

					if (variation.image.srcset.length > 1) {
						$img.attr('srcset', variation.image.srcset);
					}

					if (variation.image.sizes.length > 1) {
						$img.attr('sizes', variation.image.sizes);
					}

					$btn.data('purchasable', purchasable);

					if (purchasable) {
						$btn.find('span').text(addToCartText);
					} else {
						$btn.find('span').text(originalBtnText);
					}
				})
				.on('hide_variation', function() {
					$product.removeClass('xts-variation-active');
					$price.html(priceOriginalHtml);
					$btn.data('purchasable', false);
					$btn.find('span').text(originalBtnText);
					$img.attr('src', originalSrc);
					$img.attr('srcset', originalSrcSet);
					$img.attr('sizes', originalSizes);
				});

			$product.on('click', '.product_type_variable', function(e) {
				if (!$(this).data('purchasable')) {
					return true;
				}

				e.preventDefault();
				$form.submit();
				$btn.addClass('loading');

				$(document.body).one('added_to_cart', function() {
					$btn.removeClass('loading');
				});
			});

			$form.addClass('xts-quick-inited');
		});

		function firstInteraction($form) {
			var $product = $form.parents('.xts-product');

			if ($product.hasClass('xts-form-first-inited')) {
				return false;
			}

			$product.addClass('xts-form-first-inited');

			loadVariations($form);
		}

		function loadVariations($form) {
			var variationsCount = parseInt($form.parent().data('variations_count'));

			if (false !== $form.data('product_variations') || variationsCount > 60) {
				return;
			}

			$form.block({message: null,
				overlayCSS      : {
					background: '#fff',
					opacity   : 0.6
				}
			});
			$form.addClass('loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action: 'xts_load_variations',
					id    : $form.data('product_id')
				},
				method  : 'get',
				dataType: 'json',
				success : function(data) {
					if (data.length > 0) {
						$form.data('product_variations', data).trigger('reload_product_variations');
					}
				},
				complete: function() {
					$form.unblock();
					$form.removeClass('loading');
				},
				error   : function() {
				}
			});
		}
	};

	$(document).ready(function() {
		XTSThemeModule.quickShop();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_ajax_search.default', function() {
		XTSThemeModule.searchCatDropdown();
	});

	XTSThemeModule.searchCatDropdown = function() {
		$('.xts-search-cats').each(function() {
			var $dropdown = $(this);
			var $btn = $dropdown.find('> a');
			var $input = $dropdown.find('> input');
			var $list = $dropdown.find('> .xts-dropdown-search-cats');
			var $searchInput = $dropdown.parent().parent().find('.s');

			$searchInput.on('focus', function() {
				inputPadding();
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($list.hasClass('xts-opened') && !$(target).is('.xts-search-cats') && !$(target).parents().is('.xts-search-cats')) {
					hideList();
					return false;
				}
			});

			$btn.on('click', function(e) {
				e.preventDefault();

				if ($list.hasClass('xts-opened')) {
					hideList();
				} else {
					showList();
				}

				return false;
			});

			$list.on('click', 'a', function(e) {
				e.preventDefault();
				var $this = $(this);
				var value = $this.data('val');
				var label = $this.text();

				$list.find('.xts-current').removeClass('xts-current');
				$this.parent().addClass('xts-current');
				if (value !== 0) {
					$list.find('ul:not(.children) > li:first-child').show();
				} else if (value === 0) {
					$list.find('ul:not(.children) > li:first-child').hide();
				}

				$btn.find('span').text(label);
				$input.val(value).trigger('cat_selected');

				hideList();
				inputPadding();
			});

			function showList() {
				$list.addClass('xts-opened');

				if (typeof ($.fn.devbridgeAutocomplete) != 'undefined') {
					$dropdown.siblings('[type="text"]').devbridgeAutocomplete('hide');
				}
			}

			function hideList() {
				$list.removeClass('xts-opened');
			}

			function inputPadding() {
				if (XTSThemeModule.isMobile() || $searchInput.hasClass('xts-padding-inited') || 'yes' !== xts_settings.search_input_padding) {
					return;
				}

				var paddingValue = $dropdown.innerWidth() + 17;

				if (!$dropdown.parents('.searchform').hasClass('xts-style-default') && !$dropdown.parents('.searchform').hasClass('xts-style-icon-alt-2')) {
					paddingValue += $dropdown.parent().siblings('.searchsubmit').innerWidth();
				}

				var padding = 'padding-right';

				if (XTSThemeModule.$body.hasClass('rtl')) {
					padding = 'padding-left';
				}

				$searchInput.css(padding, paddingValue);
				$searchInput.addClass('xts-padding-inited');
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.searchCatDropdown();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.shopToolsFilters();
	});

	XTSThemeModule.shopToolsFilters = function() {
		if (XTSThemeModule.isDesktop) {
			return false;
		}

		$('.xts-shop-tools-widget').each(function() {
			var $widget = $(this);

			$widget.addClass('xts-event-click');

			$widget.find('.xts-tools-widget-title').on('click', function() {
				if ($widget.hasClass('xts-opened')) {
					$widget.removeClass('xts-opened');
				} else {
					$widget.siblings().removeClass('xts-opened');
					$widget.addClass('xts-opened');
				}
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($widget.hasClass('xts-opened') && !$(target).is('.xts-tools-widget-widget') && !$(target).parents().is('.xts-shop-tools-widget')) {
					$widget.removeClass('xts-opened');
					return false;
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.shopToolsFilters();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.shopToolsSearch();
	});

	XTSThemeModule.shopToolsSearch = function() {
		$('.xts-shop-tools .xts-search-form').each(function() {
			var $formWrapper = $(this);
			var $form = $formWrapper.find('form');

			$form.find('.searchsubmit').on('click', function(e) {
				if (!$form.hasClass('xts-opened')) {
					e.preventDefault();
					$form.addClass('xts-opened');
					setTimeout(function() {
						$form.find('input[type=text]').focus();
					}, 200);
				}
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($form.hasClass('xts-opened') && !$(target).is('.xts-shop-tools .xts-search-form') && !$(target).parents().is('.xts-shop-tools .xts-search-form')) {
					$form.removeClass('xts-opened');
					$form.find('input[type=text]').blur();
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.shopToolsSearch();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	$.each([
		'frontend/element_ready/xts_accordion.default',
		'frontend/element_ready/xts_single_product_tabs.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.singleProductAccordion();
		});
	});

	XTSThemeModule.singleProductAccordion = function() {
		var $accordion = $('.wc-tabs-wrapper.xts-accordion');
		var hash = window.location.hash;
		var url = window.location.href;

		if (hash.toLowerCase().indexOf('comment-') >= 0 || hash === '#reviews' || hash === '#tab-reviews') {
			$accordion.find('[data-accordion-index="reviews"]').click();
		} else if (url.indexOf('comment-page-') > 0 || url.indexOf('cpage=') > 0) {
			$accordion.find('[data-accordion-index="reviews"]').click();
		}

		XTSThemeModule.$body.on('click', '.wc-tabs li a, ul.tabs li a', function(e) {
			e.preventDefault();
			var index = $(this).data('tab-index');
			$accordion.find('[data-accordion-index="' + index + '"]').click();
			XTSThemeModule.$document.trigger('xtsSingleProductAccordionClick');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductAccordion();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.singleProductAjaxAddToCart = function() {
		if ('no' === xts_settings.single_product_ajax_add_to_cart) {
			return;
		}

		XTSThemeModule.$body.on('submit', 'form.cart', function(e) {
			e.preventDefault();
			var $form = $(this);

			var $productWrapper = $form.parents('.product');

			if ($productWrapper.hasClass('product-type-external') || $productWrapper.hasClass('product-type-zakeke')) {
				return;
			}

			var $button = $form.find('.single_add_to_cart_button');
			var data = $form.serialize();

			data += '&action=xts_single_product_ajax_add_to_cart';

			if ($button.val()) {
				data += '&add-to-cart=' + $button.val();
			}

			$button.removeClass('added xts-not-added').addClass('loading');

			// Trigger event
			$(document.body).trigger('adding_to_cart', [
				$button,
				data
			]);

			$.ajax({
				url    : xts_settings.ajaxurl,
				data   : data,
				method : 'POST',
				success: function(response) {
					if (!response) {
						return;
					}

					if (response.error && response.product_url) {
						window.location = response.product_url;
						return;
					}

					// Redirect to cart option
					if ('yes' === xts_settings.cart_redirect_after_add) {
						window.location = xts_settings.cart_url;
					} else {
						$button.removeClass('loading');

						var fragments = response.fragments;
						var cart_hash = response.cart_hash;

						// Block fragments class
						if (fragments) {
							$.each(fragments, function(key) {
								$(key).addClass('xts-updating');
							});
						}

						// Replace fragments
						if (fragments) {
							$.each(fragments, function(key, value) {
								$(key).replaceWith(value);
							});
						}

						// Show notices
						if (response.notices.indexOf('error') > 0) {
							$('.woocommerce-notices-wrapper').append(response.notices);
							$button.addClass('xts-not-added');
						} else {
							if ('widget' === xts_settings.action_after_add_to_cart) {
								$.magnificPopup.close();
							}

							// Trigger event so themes can refresh other areas
							$(document.body).trigger('added_to_cart', [
								fragments,
								cart_hash,
								$button
							]);
						}
					}
				},
				error  : function() {
					console.log('ajax adding to cart error');
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductAjaxAddToCart();
	});
})(jQuery);
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
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSingleProductGalleryReady', function() {
		XTSThemeModule.singleProductGalleryPhotoSwipe();
	});

	XTSThemeModule.singleProductGalleryPhotoSwipe = function() {
		var trigger = '.xts-photoswipe-btn';
		var $mainGallery = $('.xts-single-product-images');

		if ($mainGallery.hasClass('xts-action-photoswipe')) {
			trigger += ', a:not(.xts-video-btn-link)';
		}

		$mainGallery.on('click', 'a', function(e) {
			e.preventDefault();
		});

		$mainGallery.parent().on('click', trigger, function(e) {
			e.preventDefault();

			var index = getCurrentGalleryIndex(e);
			var items = getProductImages($mainGallery.find('.xts-col'));

			XTSThemeModule.callPhotoSwipe({
				index: index,
				items: items,
				galleryItems: $mainGallery,
				parents: '.xts-col',
				global: false,
			});
		});

		var getCurrentGalleryIndex = function(e) {
			if ($mainGallery.hasClass('xts-carousel')) {
				return $mainGallery.find('.xts-col.swiper-slide-active').index();
			} else {
				return $(e.currentTarget).parent().parent().index();
			}
		};

		var getProductImages = function($gallery) {
			var items = [];

			$gallery.each(function() {
				var $image = $(this).find('a > img');

				items.push({
					src: $image.parent().attr('href'),
					w: $image.data('large_image_width'),
					h: $image.data('large_image_height'),
					title: 'yes' === xts_settings.single_product_main_gallery_images_captions
						? $image.data('caption')
						: false,
				});
			});

			return items;
		};
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductGalleryPhotoSwipe();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSingleProductGalleryReady xtsImagesLoaded xtsProductQuickViewOpen xtsElementorSingleProductGallerySwiperInited', function() {
		XTSThemeModule.singleProductGalleryZoom();
	});

	XTSThemeModule.singleProductGalleryZoom = function() {
		var $galleryWrapper = $('.woocommerce-product-gallery');
		var $mainGallery = $('.xts-single-product-images');
		var zoomOptions = {
			touch: false
		};

		if ('ontouchstart' in window) {
			zoomOptions.on = 'click';
		}

		if (!$mainGallery.hasClass('xts-action-zoom')) {
			return;
		}

		if (($galleryWrapper.hasClass('xts-style-bottom') || $galleryWrapper.hasClass('xts-style-side')) && $mainGallery.hasClass('xts-loaded')) {
			var swiper = $mainGallery.find('.swiper-container')[0].swiper;

			init($mainGallery.find('.xts-col').eq(0).find('.xts-col-inner'));

			swiper.on('slideChange', function() {
				var $wrapper = $mainGallery.find('.xts-col').eq(swiper.activeIndex).find('.xts-col-inner');

				init($wrapper);
			});
		} else {
			$mainGallery.find('.xts-col').each(function() {
				var $wrapper = $(this).find('.xts-col-inner');

				init($wrapper);
			});
		}

		function init($wrapper) {
			var image = $wrapper.find('img');

			if (image.data('large_image_width') > $wrapper.width()) {
				$wrapper.trigger('zoom.destroy');
				$wrapper.zoom(zoomOptions);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductGalleryZoom();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.singleProductSticky = function() {
		if (XTSThemeModule.isTabletSize || 'undefined' === typeof $.fn.stick_in_parent) {
			return;
		}

		var $wrapper = $('.xts-product-sticky');
		var $summary = $wrapper.find('.xts-single-product-summary');
		var $gallery = $wrapper.find('.woocommerce-product-gallery');
		var offset = 40;

		if ($('.xts-sticky-on').length > 0 || $('.xts-header-clone').length > 0) {
			offset = parseInt(xts_settings.single_product_sticky_offset);
		}

		if (0 === $wrapper.length) {
			return;
		}

		$gallery.imagesLoaded(function() {
			var diff = $summary.outerHeight() - $gallery.outerHeight();

			if (diff < -100) {
				$summary.stick_in_parent({
					offset_top  : offset,
					sticky_class: 'xts-is-stuck'
				});
			} else if (diff > 100) {
				$gallery.stick_in_parent({
					offset_top  : offset,
					sticky_class: 'xts-is-stuck'
				});
			}

			XTSThemeModule.$window.on('resize', XTSThemeModule.debounce(function() {
				if (XTSThemeModule.isTablet()) {
					$summary.trigger('sticky_kit:detach');
					$gallery.trigger('sticky_kit:detach');
				} else if ($summary.outerHeight() < $gallery.outerHeight()) {
					$summary.stick_in_parent({
						offset_top  : offset,
						sticky_class: 'xts-is-stuck'
					});
				} else {
					$gallery.stick_in_parent({
						offset_top  : offset,
						sticky_class: 'xts-is-stuck'
					});
				}
			}, 300));
		});
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductSticky();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.singleProductStickyAddToCart = function() {
		var $trigger = $('form.cart');
		var $stickyBtn = $('.xts-sticky-atc');

		if (0 === $stickyBtn.length || 0 === $trigger.length || (XTSThemeModule.isMobileSize && !$stickyBtn.hasClass('xts-mb-show'))) {
			return;
		}

		var summaryOffset = $trigger.offset().top + $trigger.outerHeight();
		var $scrollToTop = $('.xts-scroll-to-top');

		var stickyAddToCartToggle = function() {
			var windowScroll = XTSThemeModule.$window.scrollTop();
			var windowHeight = XTSThemeModule.$window.height();
			var documentHeight = XTSThemeModule.$document.height();
			var totalScroll = parseInt(windowScroll + windowHeight) + 60;

			if (summaryOffset < windowScroll && totalScroll !== documentHeight && totalScroll < documentHeight) {
				$stickyBtn.addClass('xts-shown');

				if ($stickyBtn.hasClass('xts-mb-show')) {
					$scrollToTop.addClass('xts-sticky-atc-shown');
				}
			} else if (totalScroll === documentHeight || totalScroll > documentHeight || summaryOffset > windowScroll) {
				$stickyBtn.removeClass('xts-shown');

				if ($stickyBtn.hasClass('xts-mb-show')) {
					$scrollToTop.removeClass('xts-sticky-atc-shown');
				}
			}
		};

		stickyAddToCartToggle();

		XTSThemeModule.$window.on('scroll', stickyAddToCartToggle);

		$('.xts-sticky-atc-btn').on('click', function(e) {
			e.preventDefault();
			$('html, body').animate({
				scrollTop: $('.xts-single-product .product_title, .elementor-widget-xts_single_product_title').offset().top - 60
			}, 800);
		});

		// Wishlist.
		$('.xts-sticky-atc .xts-wishlist-btn a').on('click', function(e) {
			if (!$(this).hasClass('xts-added')) {
				e.preventDefault();
			}

			$('.xts-single-product-actions > .xts-wishlist-btn a').trigger('click');
		});

		XTSThemeModule.$document.on('xtsAddedToWishlist', function() {
			$('.xts-sticky-atc .xts-wishlist-btn a').addClass('xts-added');
		});

		// Compare.
		$('.xts-sticky-atc .xts-compare-btn a').on('click', function(e) {
			if (!$(this).hasClass('xts-added')) {
				e.preventDefault();
			}

			$('.xts-single-product-actions > .xts-compare-btn a').trigger('click');
		});

		XTSThemeModule.$document.on('xtsAddedToCompare', function() {
			$('.xts-sticky-atc .xts-compare-btn a').addClass('xts-added');
		});

		// Quantity.
		$('.xts-sticky-atc .qty').on('change', function() {
			$('.xts-single-product form.cart .qty').val($(this).val());
		});

		$('.xts-single-product form.cart .qty').on('change', function() {
			$('.xts-sticky-atc .qty').val($(this).val());
		});
	};

	$(document).ready(function() {
		XTSThemeModule.singleProductStickyAddToCart();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xts_variationsSwatches xtsProductTabLoaded xtsProductLoadMoreReInit xtsProductQuickViewOpen xtsPjaxComplete xtsMenuDropdownsAJAXRenderResults xtsWishlistRemoveSuccess xtsElementorSingleProductGallerySwiperInited', function() {
		XTSThemeModule.variationsSwatches();
	});

	$.each([
		'frontend/element_ready/xts_single_product_add_to_cart.default',
		'frontend/element_ready/xts_product_tabs.default',
		'frontend/element_ready/xts_products.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.variationsSwatches();
		});
	});

	XTSThemeModule.variationsSwatches = function() {
		var variationGalleryReplace = false;

		// Firefox mobile fix.
		$('.variations_form .label').on('click', function(e) {
			if ($(this).siblings('.value').hasClass('with-swatches')) {
				e.preventDefault();
			}
		});

		$('.variations_form').each(function() {
			initForm($(this));
		});

		$('.xts-products .xts-col').on('mouseenter touchstart', function() {
			var $form = $(this).find('.xts-variations_form');
			if ($form.length > 0) {
				if ($form.hasClass('xts-swatches-inited')) {
					return;
				}

				initForm($form);

				$form.addClass('xts-swatches-inited');
			}
		});

		function initForm($form) {
			var $carousel = $('.xts-single-product-images');

			// If AJAX.
			if (!$form.data('product_variations')) {
				$form.find('.xts-variation-swatch').addClass('xts-enabled');
			}

			if ($('.xts-variation-swatch').hasClass('xts-active')) {
				$form.addClass('xts-selected');
			}

			$form.on('click', '.xts-variation-swatch', function() {
				var $this = $(this);
				var term = $this.data('term');
				var taxonomy = $this.data('taxonomy');

				resetSwatches($form);

				if ($this.hasClass('xts-active') ||
					$this.hasClass('xts-disabled')) {
					return;
				}

				$form.find('select[id*=' + taxonomy + ']').val(term).trigger('change');

				$this.siblings().removeClass('xts-active');
				$this.addClass('xts-active');

				resetSwatches($form);
			});

			$form.on('click', '.reset_variations', function() {
				$form.find('.xts-active').removeClass('xts-active');
			});

			$form.on('reset_data', function() {
				var all_attributes_chosen = true;
				var $mainGallery = $form.parents('.product').find('.xts-single-product-images');
				if ($mainGallery.hasClass('xts-loaded')) {
					var swiper = $mainGallery.find('.swiper-container')[0].swiper;
				}

				$form.find('.variations select').each(function() {
					var value = $(this).val() || '';

					if (value.length === 0) {
						all_attributes_chosen = false;
					}
				});

				if (all_attributes_chosen) {
					$(this).parent().find('.xts-active').removeClass('xts-active');
				}

				$form.removeClass('xts-selected');

				resetSwatches($form);

				if ($carousel.hasClass('xts-loaded') && swiper) {
					swiper.slideTo(0, 100);
				}

				replaceGallery('default', $form);
			});

			$form.on('reset_image', function() {
				var $firstImageWrapper = $form.parents('.product').find('.xts-single-product-images .xts-col').first();
				var $firstThumbWrapper = $form.parents('.product').find('.xts-single-product-thumb .xts-col').first();

				variationsImageReset($firstImageWrapper);
				variationsImageReset($firstThumbWrapper);
			});

			$form.on('show_variation', function(e, variation) {
				var $firstImageWrapper = $form.parents('.product').find('.xts-single-product-images .xts-col').first();
				var $firstThumbWrapper = $form.parents('.product').find('.xts-single-product-thumb .xts-col').first();

				var $mainGallery = $form.parents('.product').find('.xts-single-product-images');
				if ($mainGallery.hasClass('xts-loaded')) {
					var swiper = $mainGallery.find('.swiper-container')[0].swiper;
				}

				if ($carousel.hasClass('xts-loaded') && swiper) {
					swiper.slideTo(0, 100);
				}

				if (!$form.parent().hasClass('xts-product-variations') && !replaceGallery(variation.variation_id, $form)) {
					variationsImageUpdate(variation, $firstImageWrapper, 'main');
					variationsImageUpdate(variation, $firstThumbWrapper, 'thumb');
				}

				$form.addClass('xts-selected');
			});
		}

		function variationsImageUpdate(variation, $firstImageWrapper, type) {
			var $firstImage = $firstImageWrapper.find('img');
			var $productLink = $firstImageWrapper.find('a').eq(0);

			var imageSrc = 'main' === type ? variation.image.src : variation.image.gallery_thumbnail_src;

			if (variation && variation.image && imageSrc && imageSrc.length > 1) {
				// See if the gallery has an image with the same original src as
				// the image we want to switch to.

				var $galleryHasImage = $firstImageWrapper.find('img[data-o_src="' + variation.image.thumb_src + '"]').length > 0;

				// If the gallery has the image, reset the images. We'll scroll to
				// the correct one.
				if ($galleryHasImage) {
					variationsImageReset($firstImageWrapper);
				}

				if ($firstImage.attr('src') === variation.image.thumb_src || $firstImage.attr('src') === variation.image.gallery_thumbnail_src) {
					return;
				}

				$firstImage.wc_set_variation_attr('src', imageSrc);
				if ('main' === type) {
					$firstImage.wc_set_variation_attr('height', variation.image.src_h);
					$firstImage.wc_set_variation_attr('width', variation.image.src_w);
					$firstImage.wc_set_variation_attr('srcset', variation.image.srcset);
					$firstImage.wc_set_variation_attr('sizes', variation.image.sizes);
					$firstImage.wc_set_variation_attr('title', variation.image.title);
					$firstImage.wc_set_variation_attr('data-caption', variation.image.caption);
					$firstImage.wc_set_variation_attr('alt', variation.image.alt);
					$firstImage.wc_set_variation_attr('data-src', variation.image.full_src);
					$firstImage.wc_set_variation_attr('data-large_image', variation.image.full_src);
					$firstImage.wc_set_variation_attr('data-large_image_width', variation.image.full_src_w);
					$firstImage.wc_set_variation_attr('data-large_image_height', variation.image.full_src_h);
				}

				$firstImageWrapper.wc_set_variation_attr('data-thumb', imageSrc);

				if ($productLink.length > 0) {
					$productLink.wc_set_variation_attr('href', variation.image.full_src);
				}
			} else {
				variationsImageReset($firstImageWrapper);
			}

			window.setTimeout(function() {
				XTSThemeModule.$window.trigger('resize');
				XTSThemeModule.$document.trigger('xtsImagesLoaded');
			}, 20);
		}

		function variationsImageReset($firstImageWrapper) {
			var $firstImage = $firstImageWrapper.find('img');
			var $productLink = $firstImageWrapper.find('a').eq(0);

			$firstImage.wc_reset_variation_attr('src');
			$firstImage.wc_reset_variation_attr('width');
			$firstImage.wc_reset_variation_attr('height');
			$firstImage.wc_reset_variation_attr('srcset');
			$firstImage.wc_reset_variation_attr('sizes');
			$firstImage.wc_reset_variation_attr('title');
			$firstImage.wc_reset_variation_attr('data-caption');
			$firstImage.wc_reset_variation_attr('alt');
			$firstImage.wc_reset_variation_attr('data-src');
			$firstImage.wc_reset_variation_attr('data-large_image');
			$firstImage.wc_reset_variation_attr('data-large_image_width');
			$firstImage.wc_reset_variation_attr('data-large_image_height');
			$firstImageWrapper.wc_reset_variation_attr('data-thumb');

			if ($productLink.length > 0) {
				$productLink.wc_reset_variation_attr('href');
			}

			window.setTimeout(function() {
				XTSThemeModule.$window.trigger('resize');
				XTSThemeModule.$document.trigger('xtsImagesLoaded');
			}, 20);
		}

		function resetSwatches($form) {
			// If using AJAX
			if (!$form.data('product_variations')) {
				return;
			}

			$form.find('.variations select').each(function() {
				var select = $(this);
				var options = select.html();
				options = $(options);

				select.parent().find('.xts-variation-swatch').removeClass('xts-enabled').addClass('xts-disabled');

				options.each(function() {
					var $this = $(this);
					var value = $this.val();

					if ($this.hasClass('enabled')) {
						select.parent().find('.xts-variation-swatch[data-term="' + value + '"]').removeClass('xts-disabled').addClass('xts-enabled');
					} else {
						select.parent().find('.xts-variation-swatch[data-term="' + value + '"]').addClass('xts-disabled').removeClass('xts-enabled');
					}

				});
			});
		}

		function isQuickView() {
			return $('.product').hasClass('xts-quick-view-product');
		}

		function isQuickShop($variationForm) {
			return $variationForm.parent().hasClass('quick-shop-form');
		}

		function getAdditionalVariationsImagesData($variationForm) {
			var rawData = $variationForm.data('product_variations');
			var data = [];

			if (!rawData) {
				return data;
			}

			rawData.forEach(function(value) {
				data[value.variation_id] = value.additional_variation_images;
				data['default'] = value.additional_variation_images_default;
			});

			return data;
		}

		function isVariationGallery(key, $variationForm) {
			var data = getAdditionalVariationsImagesData($variationForm);

			return typeof data !== 'undefined' && data && data[key] && data[key].length > 1;
		}

		function replaceMainGallery(imagesData, $variationForm) {
			var $mainGallery = $variationForm.parents('.product').find('.xts-single-product-images');

			$mainGallery.removeClass('xts-loaded').removeClass('xts-controls-disabled');
			if ($mainGallery.hasClass('xts-loaded')) {
				$mainGallery.find('.swiper-container')[0].swiper.destroy();
			}
			$mainGallery.empty();

			for (var key in imagesData) {
				var $html = '<div class="xts-col" data-thumb="' + imagesData[key].thumbnail_src + '"><div class="xts-col-inner">';

				if (!isQuickView()) {
					$html += '<a href="' + imagesData[key].full_src + '" data-elementor-open-lightbox="no">';
				}

				var srcset = 'undefined' !== typeof imagesData[key].srcset ? imagesData[key].srcset : '';

				$html += '<img width="' + imagesData[key].width + '" height="' + imagesData[key].height + '" src="' + imagesData[key].src + '" class="' + imagesData[key].class + '" alt="' + imagesData[key].alt + '" title="' + imagesData[key].title + '" data-caption="' + imagesData[key].data_caption + '" data-src="' + imagesData[key].data_src + '"  data-large_image="' + imagesData[key].data_large_image + '" data-large_image_width="' + imagesData[key].data_large_image_width + '" data-large_image_height="' + imagesData[key].data_large_image_height + '" srcset="' + srcset + '" sizes="' + imagesData[key].sizes + '" />';

				if (!isQuickView()) {
					$html += '</a>';
				}

				$html += '</div></div>';

				$mainGallery.append($html);
			}

			XTSThemeModule.$window.resize();
		}

		function replaceThumbnailsGallery(imagesData, $variationForm) {
			var $thumbnailsGallery = $variationForm.parents('.product').find('.xts-single-product-thumb-wrapper .xts-single-product-thumb');

			if (0 === $thumbnailsGallery.length) {
				return;
			}

			$thumbnailsGallery.removeClass('xts-loaded').removeClass('xts-controls-disabled');
			if ($thumbnailsGallery.hasClass('xts-loaded')) {
				$thumbnailsGallery.find('.swiper-container')[0].swiper.destroy();
			}
			$thumbnailsGallery.empty();

			for (var key in imagesData) {
				var $html = '<div class="xts-col">';

				$html += '<img src="' + imagesData[key].thumbnail_src + '" alt="image">';

				$html += '</div>';

				$thumbnailsGallery.append($html);
			}
		}

		function replaceGallery(key, $variationForm) {
			if (!isVariationGallery(key, $variationForm) || isQuickShop($variationForm) || ('default' === key && !variationGalleryReplace)) {
				return false;
			}

			var data = getAdditionalVariationsImagesData($variationForm);

			replaceMainGallery(data[key], $variationForm);
			replaceThumbnailsGallery(data[key], $variationForm);

			$('.woocommerce-product-gallery').removeClass('xts-inited');

			XTSThemeModule.$document.trigger('xtsImagesLoaded');

			variationGalleryReplace = 'default' !== key;

			return true;
		}
	};

	$(document).ready(function() {
		XTSThemeModule.variationsSwatches();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.wishlist = function() {
		if ('undefined' === typeof Cookies) {
			return;
		}

		var cookiesName = 'xts_wishlist_count';

		if (XTSThemeModule.$body.hasClass('logged-in')) {
			cookiesName += '_logged';
		}

		if (xts_settings.is_multisite) {
			cookiesName += '_' + xts_settings.current_blog_id;
		}

		var $widget = $('.xts-header-el.xts-header-wishlist, .xts-navbar-wishlist');
		var cookie = Cookies.get(cookiesName);

		if ($widget.length > 0 && 'undefined' !== typeof cookie) {
			try {
				var count = JSON.parse(cookie);
				$widget.find('.xts-wishlist-count, .xts-navbar-count').text(count);
			}
			catch (e) {
				console.log('cant parse cookies json');
			}
		}

		// Add to wishlist action
		XTSThemeModule.$body.on('click', '.xts-wishlist-btn a', function(e) {
			var $this = $(this);
			var productId = $this.data('product-id');
			var addedText = $this.data('added-text');
			var key = $this.data('key');

			if ($this.hasClass('xts-added')) {
				return true;
			}

			e.preventDefault();

			$this.addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action    : 'xts_add_to_wishlist',
					product_id: productId,
					key       : key
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response) {
						$this.addClass('xts-added');
						XTSThemeModule.$document.trigger('xtsAddedToWishlist');

						if (response.wishlist_content) {
							updateWishlist(response);
						}

						if ($this.find('span').length > 0) {
							$this.find('span').text(addedText);
						} else {
							$this.text(addedText);
						}
					} else {
						console.log('something wrong loading wishlist data ',
							response);
					}
				},
				error   : function(data) {
					console.log(
						'We cant add to wishlist. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.removeClass('xts-loading');
				}
			});

		});

		XTSThemeModule.$body.on('click', '.xts-remove-wishlist-btn', function(e) {
			var $this = $(this);
			var productId = $this.data('product-id');
			var key = $this.data('key');

			if ($this.find('a').hasClass('xts-loading')) {
				return true;
			}

			e.preventDefault();

			$this.find('a').addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action    : 'xts_remove_from_wishlist',
					product_id: productId,
					key       : key
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response.wishlist_content) {
						updateWishlist(response);

						XTSThemeModule.$document.trigger('xtsWishlistRemoveSuccess');
					} else {
						console.log('something wrong loading wishlist data ',
							response);
					}
				},
				error   : function(data) {
					console.log(
						'We cant remove from wishlist. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.find('a').removeClass('xts-loading');
				}
			});

		});

		// Elements update after ajax
		function updateWishlist(data) {
			if ($widget.length > 0) {
				$widget.find('.xts-wishlist-count, .xts-navbar-count').text(data.count);
			}

			if ($('.xts-wishlist-content').length > 0 && !$('.xts-wishlist-content').hasClass('xts-wishlist-preview')) {
				$('.xts-wishlist-content').replaceWith(data.wishlist_content);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.wishlist();
	});
})(jQuery);

/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_single_product_reviews.default', function($wrapper) {
		$wrapper.find('.wc-tabs-wrapper, .woocommerce-tabs').trigger('init');
		$wrapper.find('#rating').parent().find('> .stars').remove();
		$wrapper.find('#rating').trigger('init');
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_single_product_tabs.default', function($wrapper) {
		$wrapper.find('.wc-tabs-wrapper, .woocommerce-tabs').trigger('init');
		$wrapper.find('#rating').parent().find('> .stars').remove();
		$wrapper.find('#rating').trigger('init');
	});

	XTSThemeModule.woocommerceComments = function() {
		var hash = window.location.hash;
		var url = window.location.href;

		if (hash.toLowerCase().indexOf('comment-') >= 0 || hash === '#reviews' || hash === '#tab-reviews' || url.indexOf('comment-page-') > 0 || url.indexOf('cpage=') > 0) {
			setTimeout(function() {
				window.scrollTo(0, 0);
			}, 1);

			setTimeout(function() {
				if ($(hash).length > 0) {
					$('html, body').stop().animate({
						scrollTop: $(hash).offset().top - 100
					}, 400);
				}
			}, 10);
		}
	};

	$(document).ready(function() {
		XTSThemeModule.woocommerceComments();
	});
})(jQuery);

/* global xts_settings */
/* global woocommerce_price_slider_params */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.woocommercePriceSlider();
	});

	XTSThemeModule.woocommercePriceSlider = function() {
		// woocommerce_price_slider_params is required to continue, ensure the object exists
		if (typeof woocommerce_price_slider_params === 'undefined' || $('.price_slider_amount #min_price').length < 1 || !$.fn.slider) {
			return false;
		}

		var $slider = $('.price_slider:not(.ui-slider)');

		if ($slider.slider('instance') !== undefined) {
			return;
		}

		$('input#min_price, input#max_price').hide();
		$('.price_slider, .price_label').show();

		var min_price         = $('.price_slider_amount #min_price').data('min'),
		    max_price         = $('.price_slider_amount #max_price').data('max'),
		    step              = $('.price_slider_amount').data('step') || 1,
		    current_min_price = $('.price_slider_amount #min_price').val(),
		    current_max_price = $('.price_slider_amount #max_price').val();

		if ($('.products').attr('data-min_price') && $('.products').attr('data-min_price').length > 0) {
			current_min_price = parseInt($('.products').attr('data-min_price'), 10);
		}

		if ($('.products').attr('data-max_price') && $('.products').attr('data-max_price').length > 0) {
			current_max_price = parseInt($('.products').attr('data-max_price'), 10);
		}

		$slider.slider({
			range  : true,
			animate: true,
			min    : min_price,
			max    : max_price,
			step   : step,
			values : [
				current_min_price,
				current_max_price
			],
			create : function() {
				$('.price_slider_amount #min_price').val(current_min_price);
				$('.price_slider_amount #max_price').val(current_max_price);

				$(document.body).trigger('price_slider_create', [
					current_min_price,
					current_max_price
				]);
			},
			slide  : function(event, ui) {
				$('input#min_price').val(ui.values[0]);
				$('input#max_price').val(ui.values[1]);

				$(document.body).trigger('price_slider_slide', [
					ui.values[0],
					ui.values[1]
				]);
			},
			change : function(event, ui) {
				$(document.body).trigger('price_slider_change', [
					ui.values[0],
					ui.values[1]
				]);
			}
		});

		setTimeout(function() {
			$(document.body).trigger('price_slider_create', [
				current_min_price,
				current_max_price
			]);

			if ($slider.find('.ui-slider-range').length > 1) {
				$slider.find('.ui-slider-range').first().remove();
			}
		}, 10);
	};

	$(document).ready(function() {
		XTSThemeModule.woocommercePriceSlider();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsProductQuickViewOpen', function() {
		XTSThemeModule.variationsPrice();
	});

	XTSThemeModule.variationsPrice = function () {
		if ('no' === xts_settings.single_product_variations_price) {
			return;
		}

		$('.xts-single-product .variations_form').each(function () {
			var $form = $(this);
			var $price = $form.parent().find('.price').first();

			if ( 0 === $price.length ) {
				$price = $('.elementor-widget-xts_product_price .price');
			}

			var priceOriginalHtml = $price.html();

			$form.on('show_variation', function (e, variation) {
				if (variation.price_html.length > 1) {
					$price.html(variation.price_html);
				}

				$form.addClass('xts-price-outside');
			});

			$form.on('hide_variation', function () {
				$price.html(priceOriginalHtml);
				$form.removeClass('xts-price-outside');
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.variationsPrice();
	});
})(jQuery);
/* global xts_settings */
(function($) {
	XTSThemeModule.ajaxPortfolio = function() {
		if ('no' === xts_settings.ajax_portfolio || 'undefined' === typeof ($.fn.pjax)) {
			return;
		}

		var ajaxLinks = '.xts-type-links .xts-nav-portfolio a, .tax-xts-portfolio-cat .xts-breadcrumbs a, .post-type-archive-xts-portfolio .xts-breadcrumbs a,.tax-xts-portfolio-cat .xts-pagination a, .post-type-archive-xts-portfolio .xts-pagination a';

		XTSThemeModule.$body.on('click', '.tax-xts-portfolio-cat .xts-pagination a, .post-type-archive-xts-portfolio .xts-pagination a', function() {
			scrollToTop(true);
		});

		XTSThemeModule.$document.pjax(ajaxLinks, '.xts-site-content', {
			timeout : xts_settings.pjax_timeout,
			scrollTo: false
		});

		XTSThemeModule.$document.on('pjax:start', function() {
			$('.xts-ajax-content').removeClass('xts-loaded').addClass('xts-loading');
			XTSThemeModule.$document.trigger('xtsPortfolioPjaxStart');
			XTSThemeModule.$window.trigger('scroll.loaderVerticalPosition');
		});

		XTSThemeModule.$document.on('pjax:end', function() {
			$('.xts-ajax-content').addClass('xts-loaded');
		});

		XTSThemeModule.$document.on('pjax:complete', function() {
			if (!XTSThemeModule.$body.hasClass('tax-xts-portfolio-cat') && !XTSThemeModule.$body.hasClass('post-type-archive-xts-portfolio')) {
				return;
			}

			XTSThemeModule.$document.trigger('xtsPortfolioPjaxComplete');
			XTSThemeModule.$document.trigger('xtsImagesLoaded');

			scrollToTop(false);

			$('.xts-ajax-content').removeClass('xts-loading');
		});

		var scrollToTop = function(type) {
			if ('no' === xts_settings.ajax_shop_scroll && type === false) {
				return;
			}

			var $scrollTo = $(xts_settings.ajax_shop_scroll_class);
			var scrollTo = $scrollTo.offset().top - xts_settings.ajax_shop_scroll_offset;

			$('html, body').stop().animate({
				scrollTop: scrollTo
			}, 400);
		};
	};

	$(document).ready(function() {
		XTSThemeModule.ajaxPortfolio();
	});
})(jQuery);

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
/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPortfolioPjaxComplete', function() {
		XTSThemeModule.portfolioLoadMore();
	});

	XTSThemeModule.portfolioLoadMore = function() {
		var infiniteBtnClass = '.xts-load-more.xts-type-portfolio.xts-action-infinite';
		var process = false;

		XTSThemeModule.clickOnScrollButton(infiniteBtnClass, false);

		$('.xts-load-more.xts-type-portfolio').on('click', function(e) {
			e.preventDefault();

			if (process) {
				return;
			}

			process = true;

			var $this = $(this);
			var $holder = $this.parent().parent().find('.xts-portfolio-loop');
			var source = $holder.data('source');
			var ajaxurl = xts_settings.ajaxurl;
			var paged = $holder.data('paged');
			var atts = $holder.data('atts');
			var method = 'POST';

			$this.addClass('xts-loading');

			var data = {
				paged : paged,
				atts  : atts,
				action: 'xts_get_portfolio_' + source
			};

			if ('main_loop' === source) {
				ajaxurl = $(this).attr('href');
				method = 'GET';
				data = {
					loop: $holder.find('.xts-col').last().data('loop')
				};
			} else {
				data.atts.loop = $holder.find('.xts-col').last().data('loop');
			}

			$.ajax({
				url     : ajaxurl,
				data    : data,
				dataType: 'json',
				method  : method,
				success : function(data) {
					if (data.items) {
						if ($holder.hasClass('xts-masonry-layout')) {
							var items = $(data.items);
							$holder.append(items).isotope('appended', items);
							$holder.imagesLoaded().progress(function() {
								$holder.isotope('layout');
							});
						} else {
							$holder.append(data.items);
						}

						XTSThemeModule.$document.trigger('xtsPortfolioLoadMoreSuccess');

						$holder.imagesLoaded().progress(function () {
							XTSThemeModule.clickOnScrollButton(infiniteBtnClass, true);
						});

						$holder.data('paged', paged + 1);
						window.history.pushState('', '', data.currentPage);

						if ('main_loop' === source) {
							$this.attr('href', data.nextPage);
						}
					}

					if ('no-more-posts' === data.status) {
						$this.remove();
					}
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() {
					$this.removeClass('xts-loading');
					process = false;
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.portfolioLoadMore();
	});
})(jQuery);


/* global xts_settings */
(function($) {
	XTSThemeModule.menuOverlay = function() {
		if ('no' === xts_settings.menu_overlay) {
			return;
		}

		var hoverSelector = '.xts-header .xts-nav-main .menu-item.item-level-0.menu-item-has-children.xts-event-hover, .xts-header .xts-nav-mega .menu-item.item-level-0.menu-item-has-children.xts-event-hover, .xts-sticky-cats';
		var sideClasses;

		$(hoverSelector).on('mouseleave', function() {
			$('.xts-close-side').attr('class', sideClasses);
		});

		$(hoverSelector).on('mouseenter mousemove', function() {
			var $this = $(this);
			var $overlay = $('.xts-close-side');

			if ($overlay.hasClass('xts-opened')) {
				return;
			}

			var isInHeader = $this.parents('.xts-header').length;
			var isInCategories = $this.hasClass('xts-sticky-cats');
			var isInHeaderCategories = $this.parents('.xts-header-cats').length;
			sideClasses = $overlay.attr('class');

			if (isInHeader) {
				if ($this.parents('.xts-sticked').length) {
					$overlay.addClass('xts-location-sticky-header');
				} else {
					$overlay.addClass('xts-location-header');
				}
				if (isInHeaderCategories) {
					$overlay.addClass('xts-location-header-cats');
				}
			} else if (isInCategories) {
				$overlay.addClass('xts-location-categories');
			}

			$overlay.addClass('xts-opened');
		});

		$('.xts-header .menu-item.item-level-0.menu-item-has-children.xts-event-click').on('click', function() {
			$('.xts-close-side').toggleClass('xts-opened').toggleClass('xts-location-header');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.menuOverlay();
	});
})(jQuery);