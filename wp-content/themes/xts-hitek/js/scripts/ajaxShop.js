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