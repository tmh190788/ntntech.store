/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete xtsProductQuickViewOpen xtsProductLoadMoreReInit xtsWishlistRemoveSuccess xtsProductTabLoaded xtsElementorProductTabsReady', function() {
		XTSThemeModule.countDownTimerElement();
	});

	$.each([
		'frontend/element_ready/xts_products.default',
		'frontend/element_ready/xts_single_product_tabs.default',
		'frontend/element_ready/xts_single_product_countdown.default',
		'frontend/element_ready/xts_product_countdown.default',
		'frontend/element_ready/xts_countdown_timer.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.countDownTimerElement();
		});
	});

	XTSThemeModule.countDownTimerElement = function() {
		$('.xts-countdown-timer').each(function() {
			var $this = $(this);
			dayjs.extend(window.dayjs_plugin_utc);
			dayjs.extend(window.dayjs_plugin_timezone);
			var time = dayjs.tz($this.data('end-date'), $this.data('timezone'));

			$this.countdown(time.toDate(), function(event) {
				$this.html(event.strftime(''
					+ '<div class="xts-countdown-item xts-countdown-days"><div class="xts-countdown-digit">%-D</div><div class="xts-countdown-label">' + xts_settings.countdown_days + '</div></div> '
					+ '<div class="xts-countdown-item xts-countdown-hours"><div class="xts-countdown-digit">%H</div><div class="xts-countdown-label">' + xts_settings.countdown_hours + '</div></div> '
					+ '<div class="xts-countdown-item xts-countdown-min"><div class="xts-countdown-digit">%M</div><div class="xts-countdown-label">' + xts_settings.countdown_mins + '</div></div> '
					+ '<div class="xts-countdown-item xts-countdown-sec"><div class="xts-countdown-digit">%S</div><div class="xts-countdown-label">' + xts_settings.countdown_sec + '</div></div>'
				));
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.countDownTimerElement();
	});
})(jQuery);
