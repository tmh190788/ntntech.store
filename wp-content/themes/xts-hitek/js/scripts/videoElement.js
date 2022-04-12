/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsMenuDropdownsAJAXRenderResults', function() {
		XTSThemeModule.videoElementClick();
		XTSThemeModule.videoElementLazyLoad();
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_video.default', function() {
		XTSThemeModule.videoElementClick();
		XTSThemeModule.videoElementLazyLoad();
	});

	XTSThemeModule.videoElementClick = function() {
		$('.xts-el-video-btn-overlay:not(.xts-el-video-lightbox):not(.xts-el-video-hosted)').on('click', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $video = $this.parents('.xts-el-video').find('iframe');
			var videoScr = $video.data('lazy-load');
			var videoNewSrc = videoScr + '&autoplay=1&rel=0&mute=1';

			if (videoScr.indexOf('vimeo.com') + 1) {
				videoNewSrc = videoScr.replace('#t=', '') + '&autoplay=1';
			}

			$video.attr('src', videoNewSrc);
			$this.parents('.xts-el-video').addClass('xts-playing');
		});

		$('.xts-el-video-btn-overlay.xts-el-video-hosted:not(.xts-el-video-lightbox)').on('click', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $video = $this.parents('.xts-el-video').find('video');
			var videoScr = $video.data('lazy-load');

			$video.attr('src', videoScr);
			$video[0].play();
			$this.parents('.xts-el-video').addClass('xts-playing');
		});
	};

	XTSThemeModule.videoElementLazyLoad = function() {
		$('.xts-el-video, .xts-single-post .xts-post-video').each(function() {
			var $videoWrapper = $(this);
			var $video = $videoWrapper.find('iframe');
			if ($video.length === 0) {
				$video = $videoWrapper.find('video');
			}
			var videoScr = $video.data('lazy-load');

			if (!$videoWrapper.hasClass('xts-action-without') && $videoWrapper.hasClass('xts-el-video')) {
				return;
			}

			XTSThemeModule.$window.on('scroll', function() {
				if ((window.innerHeight + XTSThemeModule.$window.scrollTop() + 100) > $videoWrapper.offset().top) {
					if ($videoWrapper.hasClass('xts-loaded')) {
						return;
					}

					$videoWrapper.addClass('xts-loaded');
					$video.attr('src', videoScr);
					if ($video.attr('autoplay')) {
						$video[0].play();
					}
				}
			});

			XTSThemeModule.$window.scroll();
		});
	};

	$(document).ready(function() {
		XTSThemeModule.videoElementClick();
		XTSThemeModule.videoElementLazyLoad();
	});
})(jQuery);