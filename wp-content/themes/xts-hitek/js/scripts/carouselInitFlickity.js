/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsElementorSliderReady xtsPjaxComplete', function() {
		XTSThemeModule.carouselInitFlickity();
	});

	XTSThemeModule.carouselInitFlickity = function() {
		$('.xts-slider').each(function() {
			var $carousel = $(this);
			var data = $carousel.data('carousel-args');

			if (!data) {
				return;
			}

			var config = {
				contain             : 'yes' !== data.center_mode,
				percentPosition     : true,
				cellAlign           : 'yes' === data.center_mode ? 'center' : 'left',
				rightToLeft         : XTSThemeModule.$body.hasClass('rtl'),
				prevNextButtons     : 'yes' === data.arrows,
				pageDots            : 'yes' === data.dots,
				wrapAround          : 'yes' === data.infinite_loop,
				autoPlay            : 'yes' !== data.autoplay ? false : parseInt(data.autoplay_speed.size),
				pauseAutoPlayOnHover: 'yes' === data.autoplay,
				adaptiveHeight      : 'yes' === data.auto_height,
				groupCells          : 'yes' !== data.center_mode,
				draggable           : 'yes' === data.draggable ? '>1' : false,
				imagesLoaded        : true,
				fade                : $carousel.hasClass('xts-anim-fade'),
				on                  : {
					ready: function() {
						if ($carousel.hasClass('xts-arrows-style-text')) {
							$carousel.find('> .flickity-button.next').append('<span>' + xts_settings.flickity_slider_element_next_text + '</span>');
							$carousel.find('> .flickity-button.previous').append('<span>' + xts_settings.flickity_slider_element_previous_text + '</span>');
						}

						$carousel.find('> .flickity-button').wrapAll('<div class="flickity-buttons"></div>');
					}
				}
			};

			$carousel.flickity(config);

			if ($carousel.hasClass('xts-anim-parallax')) {
				var flkty = $carousel.data('flickity');
				var $imgs = $('.xts-slide .xts-slide-bg');

				$carousel.on('scroll.flickity', function() {
					flkty.slides.forEach(function(e, i) {
						var img = $imgs[i];

						var x = 0 === i
							? Math.abs(flkty.x) > flkty.slidesWidth
								? flkty.slidesWidth + flkty.x + flkty.slides[flkty.slides.length - 1].outerWidth + e.target
								: e.target + flkty.x
							: i === flkty.slides.length - 1 && Math.abs(flkty.x) + flkty.slides[i].outerWidth < flkty.slidesWidth
								? e.target - flkty.slidesWidth + flkty.x - flkty.slides[i].outerWidth
								: e.target + flkty.x;

						img.style.transform = 'translateX( ' + -.5 * x + 'px)';
					});
				});
			}

			setTimeout(function() {
				$carousel.addClass('xts-enabled');
			}, 100);

			$carousel.on('dragStart.flickity', function() {
				$carousel.addClass('xts-dragging');
			});

			$carousel.on('dragEnd.flickity', function() {
				$carousel.removeClass('xts-dragging');
			});

			XTSThemeModule.$document.trigger('xtsImagesLoaded');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.carouselInitFlickity();
	});
})(jQuery);
