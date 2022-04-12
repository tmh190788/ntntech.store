/* global xts_settings */
(function($) {
	XTSThemeModule.quantity = function() {
		if (!String.prototype.getDecimals) {
			String.prototype.getDecimals = function() {
				var num   = this,
				    match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
				if (!match) {
					return 0;
				}
				return Math.max(0, (match[1] ? match[1].length : 0) - (match[2] ? +match[2] : 0));
			};
		}

		XTSThemeModule.$document.on('click', '.xts-plus, .xts-minus', function() {
			// Get values
			var $this = $(this);
			var $qty = $this.closest('.quantity').find('.qty');
			var currentVal = parseFloat($qty.val());
			var max = parseFloat($qty.attr('max'));
			var min = parseFloat($qty.attr('min'));
			var step = $qty.attr('step');

			// Format values
			if (!currentVal || '' === currentVal || 'NaN' === currentVal) {
				currentVal = 0;
			}
			if ('' === max || 'NaN' === max) {
				max = '';
			}
			if ('' === min || 'NaN' === min) {
				min = 0;
			}
			if ('any' === step || '' === step || undefined === step || 'NaN' === parseFloat(step)) {
				step = '1';
			}

			// Change the value
			if ($this.is('.xts-plus')) {
				if (max && (currentVal >= max)) {
					$qty.val(max);
				} else {
					$qty.val((currentVal + parseFloat(step)).toFixed(step.getDecimals()));
				}
			} else {
				if (min && (currentVal <= min)) {
					$qty.val(min);
				} else if (currentVal > 0) {
					$qty.val((currentVal - parseFloat(step)).toFixed(step.getDecimals()));
				}
			}

			// Trigger change event
			$qty.trigger('change');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.quantity();
	});
})(jQuery);
