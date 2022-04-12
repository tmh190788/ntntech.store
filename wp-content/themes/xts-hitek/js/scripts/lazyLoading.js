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