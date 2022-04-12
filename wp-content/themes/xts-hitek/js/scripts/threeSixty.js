/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_360_view.default', function() {
		XTSThemeModule.threeSixty();
	});

	XTSThemeModule.threeSixty = function() {
		$('.xts-360-view').each(function() {
			var $this = $(this);
			var data = $this.data('args');

			if (!data) {
				return false;
			}

			$this.ThreeSixty({
				totalFrames : data.frames_count,
				endFrame    : data.frames_count,
				currentFrame: 1,
				imgList     : '.xts-360-images',
				progress    : '.xts-360-progress',
				imgArray    : data.images,
				height      : data.height,
				width       : data.width,
				responsive  : true,
				navigation  : 'yes' === data.navigation
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.threeSixty();
	});
})(jQuery);