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