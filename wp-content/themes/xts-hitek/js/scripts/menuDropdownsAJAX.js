/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_mega_menu.default', function() {
		XTSThemeModule.menuDropdownsAJAX();
	});

	XTSThemeModule.menuDropdownsAJAX = function() {
		var $menus = $('.menu').has('.xts-dropdown-ajax');

		$('body').on('mousemove', checkMenuProximity);

		function checkMenuProximity(event) {
			$menus.each(function() {
				var $menu = $(this);

				if ($menu.hasClass('xts-dropdowns-loading') || $menu.hasClass('xts-dropdowns-loaded')) {
					return;
				}

				if (!isNear($menu, 50, event)) {
					return;
				}

				loadDropdowns($menu);
			});
		}

		function loadDropdowns($menu) {
			$menu.addClass('xts-dropdowns-loading');

			var storageKey = xts_settings.menu_storage_key + '_' + $menu.attr('id');
			var storedData = false;

			var $items = $menu.find('.xts-dropdown-ajax'),
			    ids    = [];

			$items.each(function() {
				ids.push(jQuery(this).find('.xts-dropdown-placeholder').data('id'));
			});

			if (xts_settings.ajax_dropdowns_save && XTSThemeModule.supports_html5_storage) {
				var unparsedData = localStorage.getItem(storageKey);

				try {
					storedData = JSON.parse(unparsedData);
				}
				catch (e) {
					console.log('cant parse Json', e);
				}
			}

			if (storedData) {
				renderResults(storedData);
				$menu.removeClass('xts-dropdowns-loading').addClass('xts-dropdowns-loaded');
			} else {
				jQuery.ajax({
					url     : xts_settings.ajaxurl,
					data    : {
						action: 'xts_load_html_dropdowns',
						ids   : ids
					},
					dataType: 'json',
					method  : 'POST',
					success : function(response) {
						if ('success' === response.status) {
							renderResults(response.data);
							if (xts_settings.ajax_dropdowns_save && XTSThemeModule.supports_html5_storage) {
								localStorage.setItem(storageKey, JSON.stringify(response.data));
							}
						} else {
							console.log('loading html dropdowns returns wrong data - ', response.message);
						}
					},
					error   : function() {
						console.log('loading html dropdowns ajax error');
					},
					complete: function() {
						$menu.removeClass('xts-dropdowns-loading').addClass('xts-dropdowns-loaded');
					}
				});
			}

			function renderResults(data) {
				Object.keys(data).forEach(function(id) {
					var html = data[id];
					$menu.find('[data-id="' + id + '"]').siblings('.xts-dropdown-inner').html(html);
					$menu.find('[data-id="' + id + '"]').remove();
				});

				// Initialize OWL Carousels
				XTSThemeModule.$document.trigger('xtsMenuDropdownsAJAXRenderResults');
			}
		}

		function isNear($element, distance, event) {
			var left   = $element.offset().left - distance,
			    top    = $element.offset().top - distance,
			    right  = left + $element.width() + (2 * distance),
			    bottom = top + $element.height() + (2 * distance),
			    x      = event.pageX,
			    y      = event.pageY;

			return (x > left && x < right && y > top && y < bottom);
		}
	};

	$(document).ready(function() {
		XTSThemeModule.menuDropdownsAJAX();
	});
})(jQuery);