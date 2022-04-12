/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsBlogLoadMoreSuccess', function() {
		XTSThemeModule.postVideoControls();
	});

	$.each([
		'frontend/element_ready/xts_blog.default'
	], function(index, value) {
		XTSThemeModule.xtsElementorAddAction(value, function() {
			XTSThemeModule.postVideoControls();
		});
	});

	XTSThemeModule.postVideoControls = function() {
		$('.xts-post-controls .xts-play').on('click', function(e) {
			e.preventDefault();
			var $this = $(this);
			var $parent = $this.parents('.xts-post-thumb').find('.xts-post-video');
			var $video = $parent.find('iframe');
			if ($video.length === 0) {
				$video = $parent.find('video');
			}

			if ($parent.hasClass('xts-loaded')) {
				return;
			}

			var videoScr = $video.data('lazy-load');

			if (videoScr.indexOf('vimeo.com') + 1) {
				videoScr = videoScr.replace('#t=', '') + '&autoplay=1';
			} else if (videoScr.indexOf('youtube.com') + 1) {
				videoScr = videoScr + '&autoplay=1&rel=0';
			}

			$video.attr('src', videoScr);
			$parent.addClass('xts-loaded');
			$this.addClass('xts-loading');

			if ($parent.hasClass('xts-post-video-youtube')) {
				if ('undefined' === typeof YT || 'undefined' === typeof YT.Player) {
					var interval;

					$.getScript('https://www.youtube.com/player_api', function() {
						interval = setInterval(function() {
							if ('undefined' !== typeof YT.Player) {
								clearInterval(interval);
								youtubePostVideoControls($parent);
								$this.removeClass('xts-loading');
								XTSThemeModule.$document.trigger('xtsPostVideoLoaded');
							}
						}, 100);
					});
				} else {
					youtubePostVideoControls($parent);
					$this.removeClass('xts-loading');
				}
			} else if ($parent.hasClass('xts-post-video-html5')) {
				hostedPostVideoControls($parent);
				$this.removeClass('xts-loading');
				XTSThemeModule.$document.trigger('xtsPostVideoLoaded');
				$this.trigger('click');
				$this.parents('.xts-post').addClass('xts-video-playing');
			} else if ($parent.hasClass('xts-post-video-vimeo')) {
				if ('undefined' === typeof Vimeo || 'undefined' === typeof Vimeo.Player) {
					$.getScript(xts_settings.vimeo_library_url, function() {
						vimeoPostVideoControls($parent);
						$this.removeClass('xts-loading');
						XTSThemeModule.$document.trigger('xtsPostVideoLoaded');
					});
				} else {
					vimeoPostVideoControls($parent);
					$this.removeClass('xts-loading');
					XTSThemeModule.$document.trigger('xtsPostVideoLoaded');
				}
				$this.trigger('click');
				$this.parents('.xts-post').addClass('xts-video-playing');
			}
		});

		function youtubePostVideoControls($parent) {
			var $video = $parent.find('iframe');
			var $wrapper = $video.parents('.xts-post');
			var $playBtn = $wrapper.find('.xts-post-control.xts-play');
			var $muteBtn = $wrapper.find('.xts-post-control.xts-mute');
			var player;

			player = new YT.Player($video[0], {
				events: {
					'onReady': onPlayerReady
				}
			});

			function onPlayerReady() {
				$playBtn.on('click', function() {
					if ($wrapper.hasClass('xts-video-playing')) {
						$wrapper.removeClass('xts-video-playing');
						player.pauseVideo();
					} else {
						$wrapper.addClass('xts-video-playing');
						player.playVideo();
					}
				});

				$muteBtn.on('click', function() {
					if ($wrapper.hasClass('xts-video-muted')) {
						$wrapper.removeClass('xts-video-muted');
						player.unMute();
					} else {
						$wrapper.addClass('xts-video-muted');
						player.mute();
					}
				});

				$playBtn.trigger('click');
			}
		}

		function hostedPostVideoControls($parent) {
			var $video = $parent.find('video');
			var $wrapper = $video.parents('.xts-post');
			var $playBtn = $wrapper.find('.xts-post-control.xts-play');
			var $muteBtn = $wrapper.find('.xts-post-control.xts-mute');

			$playBtn.on('click', function() {
				if ($wrapper.hasClass('xts-video-playing')) {
					$wrapper.removeClass('xts-video-playing');
					$video[0].pause();
				} else {
					$wrapper.addClass('xts-video-playing');
					$video[0].play();
				}
			});

			$muteBtn.on('click', function() {
				if ($wrapper.hasClass('xts-video-muted')) {
					$wrapper.removeClass('xts-video-muted');
					$video.prop('muted', false);
				} else {
					$wrapper.addClass('xts-video-muted');
					$video.prop('muted', true);
				}
			});
		}

		function vimeoPostVideoControls($parent) {
			var $video = $parent.find('iframe');
			var $wrapper = $video.parents('.xts-post');
			var $playBtn = $wrapper.find('.xts-post-control.xts-play');
			var $muteBtn = $wrapper.find('.xts-post-control.xts-mute');
			var player = new Vimeo.Player($video[0]);

			$playBtn.on('click', function() {
				if ($wrapper.hasClass('xts-video-playing')) {
					$wrapper.removeClass('xts-video-playing');
					player.pause();
				} else {
					$wrapper.addClass('xts-video-playing');
					player.play();
				}
			});

			$muteBtn.on('click', function() {
				if ($wrapper.hasClass('xts-video-muted')) {
					$wrapper.removeClass('xts-video-muted');
					player.setVolume(1);
				} else {
					$wrapper.addClass('xts-video-muted');
					player.setVolume(0);
				}
			});
		}
	};

	$(document).ready(function() {
		XTSThemeModule.postVideoControls();
	});
})(jQuery);