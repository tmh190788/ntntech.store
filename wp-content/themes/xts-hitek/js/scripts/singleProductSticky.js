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