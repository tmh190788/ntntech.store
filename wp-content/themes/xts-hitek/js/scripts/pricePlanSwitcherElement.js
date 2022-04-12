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