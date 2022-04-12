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