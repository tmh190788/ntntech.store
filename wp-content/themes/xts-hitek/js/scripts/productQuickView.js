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