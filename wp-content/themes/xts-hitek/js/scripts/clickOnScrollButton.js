/* global xts_settings */
(function($) {
	XTSThemeModule.clickOnScrollButton = function(btnClass) {
		if (typeof ($.fn.xtsWaypoint) === 'undefined') {
			return;
		}

		var $btn = $(btnClass);

		if ($btn.length <= 0) {
			return;
		}

		$btn.trigger('xtsWaypointDestroy');

		var waypoint = $btn.xtsWaypoint({
			handler: function() {
				$btn.trigger('click');
			},
			offset : function() {
				return XTSThemeModule.$window.outerHeight();
			}
		});

		$btn.data('waypoint-inited', true).off('xtsWaypointDestroy').on('xtsWaypointDestroy', function() {
			if ($btn.data('waypoint-inited')) {
				waypoint[0].destroy();
				$btn.data('waypoint-inited', false)
			}
		});
	};
})(jQuery);
