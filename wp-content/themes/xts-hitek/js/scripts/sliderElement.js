/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_slider.default', function() {
		XTSThemeModule.sliderAnimations();
		XTSThemeModule.sliderLazyLoad();
		XTSThemeModule.$document.trigger('xtsElementorSliderReady');
	});

	XTSThemeModule.sliderAnimations = function() {
		$('.xts-slider').each(function() {
			var $carousel = $(this);

			$carousel.find('[class*="xts-animation"]').each(function() {
				$(this).addClass('xts-animation-ready');
			});

			runAnimations(0, true);

			$carousel.on('change.flickity', function(event, index) {
				runAnimations(index, false);
			});

			function runAnimations(slideIndex, firstLoad) {
				var nextSlide = $carousel.find('.xts-slide').eq(slideIndex);

				nextSlide.siblings().find('[class*="xts-animation"]').removeClass('xts-animated');

				nextSlide.find('[class*="xts-animation"]').each(function() {
					var $this = $(this);
					var classes = $this.attr('class').split(' ');
					var delay = 0;

					for (var index = 0; index < classes.length; index++) {
						if (classes[index].indexOf('xts_delay_') >= 0) {
							delay = parseInt(classes[index].split('_')[2]);
						}
					}

					if (firstLoad) {
						delay += 500;
					}

					setTimeout(function() {
						$this.addClass('xts-animated');
					}, delay);
				});
			}
		});
	};

	XTSThemeModule.sliderLazyLoad = function() {
		$('.xts-slider').on('select.flickity', function(event, index) {
			var $this = $(this);
			var active = $this.find('.xts-slide').eq(index);
			var $els = $this.find('[id="' + active.attr('id') + '"]');

			$this.find('.xts-slide').eq(index + 1).addClass('xts-loaded');
			active.addClass('xts-loaded');

			$els.each(function() {
				$(this).addClass('xts-loaded');
			});

			// Video pause
			if (active.find('.xts-slide-video-html5').length > 0) {
				active.addClass('xts-playing');
				active.find('.xts-slide-video-html5')[0].play();
			}
			if (active.siblings().find('.xts-slide-video-html5').length > 0) {
				active.removeClass('xts-playing');
				active.siblings().find('.xts-slide-video-html5')[0].pause();
			}

			// Vimeo
			var vimeo;
			if (active.find('.xts-slide-video-vimeo').length > 0) {
				active.addClass('xts-playing');
				vimeo = new Vimeo.Player(active.find('.xts-slide-video-vimeo')[0]);
				vimeo.play();
			}
			if (active.siblings().find('.xts-slide-video-vimeo').length > 0) {
				active.siblings().removeClass('xts-playing');
				vimeo = new Vimeo.Player(active.siblings().find('.xts-slide-video-vimeo')[0]);
				vimeo.pause();
			}
		});
	};

	XTSThemeModule.youtubeVideoAPI = function() {
		window.onYouTubeIframeAPIReady = function() {
			$('.xts-slide-video-youtube').each(function() {
				var $video = $(this);
				var player;

				player = new YT.Player($video[0], {
					events: {
						'onReady': onPlayerReady
					}
				});

				function onPlayerReady() {
					$('.xts-slider').on('select.flickity', function(event, index) {
						var $this = $(this);
						var active = $this.find('.xts-slide').eq(index);

						if (active.find('.xts-slide-video-youtube').length > 0) {
							active.addClass('xts-playing');
							player.playVideo();
						}

						if (active.siblings().find('.xts-slide-video-youtube').length > 0) {
							active.siblings().removeClass('xts-playing');
							player.pauseVideo();
						}
					});
				}
			});
		};
	};

	$(document).ready(function() {
		XTSThemeModule.sliderAnimations();
		XTSThemeModule.sliderLazyLoad();
		XTSThemeModule.youtubeVideoAPI();
	});
})(jQuery);