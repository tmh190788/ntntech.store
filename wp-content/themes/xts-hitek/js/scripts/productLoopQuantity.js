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