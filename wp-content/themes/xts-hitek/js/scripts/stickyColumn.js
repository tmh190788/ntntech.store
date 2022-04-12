/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorColumnReady', function () {
		setTimeout(function() {
			XTSThemeModule.stickyColumn();
		}, 100);
	});

	XTSThemeModule.stickyColumn = function () {
		if (XTSThemeModule.isTabletSize || 'undefined' === typeof $.fn.stick_in_parent) {
			return;
		}

		$('.xts-sticky-column').each(function () {
			var $column = $(this);
			var offset = 150;
			var classes = $column.attr('class').split(' ');

			for (var index = 0; index < classes.length; index++) {
				if (classes[index].indexOf('xts_sticky_offset_') >= 0) {
					var data = classes[index].split('_');
					offset = parseInt(data[3]);
				}
			}

			$column.find('> .elementor-widget-wrap').stick_in_parent({
				offset_top: offset,
				sticky_class: 'xts-is-stuck'
			});

			$('.wc-tabs-wrapper li').on('click', function() {
				setTimeout(function() {
					$column.find('> .elementor-widget-wrap').trigger('sticky_kit:recalc');
				}, 300);
			});
		})
	};

	$(document).ready(function() {
		XTSThemeModule.stickyColumn();
	});
})(jQuery);