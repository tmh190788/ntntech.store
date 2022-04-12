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