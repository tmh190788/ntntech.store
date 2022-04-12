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