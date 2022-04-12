/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete xtsPostVideoLoaded xtsBlogLoadMoreSuccess', function() {
		XTSThemeModule.calcVideoSize();
	});

	$.each([
		'frontend/element_ready/xts_blog.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.calcVideoSize();
		});
	});

	XTSThemeModule.calcVideoSize = function() {
		$('.xts-video-resize').each(function() {
			var $this = $(this);
			var $video = $this.find('iframe');

			if ($video.length <= 0) {
				return;
			}

			var containerWidth = $this.outerWidth() + 5;
			var containerHeight = $this.outerHeight() + 5;
			var aspectRatioSetting = '16:9';

			var aspectRatioArray = aspectRatioSetting.split(':');
			var aspectRatio = aspectRatioArray[0] / aspectRatioArray[1];
			var ratioWidth = containerWidth / aspectRatio;
			var ratioHeight = containerHeight * aspectRatio;
			var isWidthFixed = containerWidth / containerHeight > aspectRatio;

			var size = {
				width : isWidthFixed ? containerWidth : ratioHeight,
				height: isWidthFixed ? ratioWidth : containerHeight
			};

			$video.width(size.width).height(size.height + 140);
		});
	};

	$(document).ready(function() {
		XTSThemeModule.calcVideoSize();
	});
})(jQuery);
