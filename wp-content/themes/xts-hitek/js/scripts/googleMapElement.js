/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_google_map.default', function() {
		XTSThemeModule.googleMapInit();
		XTSThemeModule.googleMapCloseContent();
	});

	XTSThemeModule.googleMapInit = function() {
		if ( typeof google === 'undefined' ) {
			return;
		}

		$('.xts-map').each(function() {
			var $map = $(this);
			var data = $map.data('map-args');

			var config = {
				locations: [
					{
						lat: data.latitude,
						lon: data.longitude,
						icon: data.marker_icon,
						animation: google.maps.Animation.DROP,
					},
				],
				controls_on_map: false,
				map_div: '#' + data.selector,
				start: 1,
				map_options: {
					zoom: parseInt(data.zoom),
					scrollwheel: 'yes' === data.mouse_zoom,
					disableDefaultUI: data.default_ui,
				},
			};

			if (data.json_style) {
				config.styles = {};
				config.styles[xts_settings.google_map_style_text] = $.parseJSON(atob(data.json_style));
			}

			if ('yes' === data.marker_text_needed) {
				config.locations[0].html = data.marker_text;
			}

			if ('button' === data.lazy_type) {
				$map.find('.xts-map-button').on('click', function(e) {
					e.preventDefault();

					if ($map.hasClass('xts-loaded')) {
						return;
					}

					$map.addClass('xts-loaded');
					new Maplace(config).Load();
				});
			} else if ('scroll' === data.lazy_type) {
				XTSThemeModule.$window.on('scroll', function() {
					if ((window.innerHeight + XTSThemeModule.$window.scrollTop() + 100) > $map.offset().top) {
						if ($map.hasClass('xts-loaded')) {
							return;
						}

						$map.addClass('xts-loaded');
						new Maplace(config).Load();
					}
				});

				XTSThemeModule.$window.scroll();
			} else {
				new Maplace(config).Load();
			}
		});
	};

	XTSThemeModule.googleMapCloseContent = function() {
		var $map = $('.xts-map-close');

		if ( $map.hasClass('xts-inited') ) {
			return;
		}

		$map.addClass('xts-inited');

		$map.on('click', function(e) {
			e.preventDefault();
			$(this).parent().toggleClass('xts-opened');
		});
	};

	$(document).ready(function() {
		XTSThemeModule.googleMapInit();
		XTSThemeModule.googleMapCloseContent();
	});
})(jQuery);
