/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsMenuDropdownsAJAXRenderResults', function() {
		XTSThemeModule.videoElementPopup();
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_video.default', function() {
		XTSThemeModule.videoElementPopup();
	});

	XTSThemeModule.videoElementPopup = function() {
		if ('undefined' === typeof ($.fn.magnificPopup)) {
			return;
		}

		$.magnificPopup.close();

		$('.xts-el-video-btn:not(.xts-el-video-hosted), .xts-el-video-btn-overlay.xts-el-video-lightbox:not(.xts-el-video-hosted), .xts-action-btn.xts-video-btn a').magnificPopup({
			tClose         : xts_settings.magnific_close,
			tLoading       : xts_settings.magnific_loading,
			removalDelay   : 400,
			type           : 'iframe',
			preloader      : false,
			fixedContentPos: false,
			iframe         : {
				patterns: {
					youtube: {
						index: 'youtube.com/',
						id   : 'v=',
						src  : '//www.youtube.com/embed/%id%?rel=0&autoplay=1&mute=1'
					},
					vimeo  : {
						index: 'vimeo.com/',
						id   : '/',
						src  : '//player.vimeo.com/video/%id%?autoplay=1'
					}
				}
			},
			callbacks      : {
				beforeOpen: function() {
					this.st.mainClass = 'xts-popup-effect';
				}
			}
		});

		$('.xts-el-video-btn-overlay.xts-el-video-lightbox.xts-el-video-hosted,.xts-el-video-btn.xts-el-video-hosted').magnificPopup({
			type        : 'inline',
			removalDelay: 400,
			tClose      : xts_settings.magnific_close,
			tLoading    : xts_settings.magnific_loading,
			preloader   : false,
			callbacks   : {
				beforeOpen  : function() {
					this.st.mainClass = 'xts-popup-effect xts-popup-video-holder';
				},
				elementParse: function(item) {
					var $video = $(item.src).find('video');
					var videoScr = $video.data('lazy-load');
					$video.attr('src', videoScr);
					$video.attr('autoplay', '1');
				},
				open        : function() {
					XTSThemeModule.$document.trigger('xtsImagesLoaded');
					XTSThemeModule.$window.resize();
				},
				close       : function(e) {
					var magnificPopup = $.magnificPopup.instance;

					var $video = $(magnificPopup.items[0].src).find('video');
					$video.attr('src', '');
				}
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.videoElementPopup();
	});
})(jQuery);