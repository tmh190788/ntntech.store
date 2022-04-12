/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_ajax_search.default', function() {
		XTSThemeModule.searchCatDropdown();
	});

	XTSThemeModule.searchCatDropdown = function() {
		$('.xts-search-cats').each(function() {
			var $dropdown = $(this);
			var $btn = $dropdown.find('> a');
			var $input = $dropdown.find('> input');
			var $list = $dropdown.find('> .xts-dropdown-search-cats');
			var $searchInput = $dropdown.parent().parent().find('.s');

			$searchInput.on('focus', function() {
				inputPadding();
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if ($list.hasClass('xts-opened') && !$(target).is('.xts-search-cats') && !$(target).parents().is('.xts-search-cats')) {
					hideList();
					return false;
				}
			});

			$btn.on('click', function(e) {
				e.preventDefault();

				if ($list.hasClass('xts-opened')) {
					hideList();
				} else {
					showList();
				}

				return false;
			});

			$list.on('click', 'a', function(e) {
				e.preventDefault();
				var $this = $(this);
				var value = $this.data('val');
				var label = $this.text();

				$list.find('.xts-current').removeClass('xts-current');
				$this.parent().addClass('xts-current');
				if (value !== 0) {
					$list.find('ul:not(.children) > li:first-child').show();
				} else if (value === 0) {
					$list.find('ul:not(.children) > li:first-child').hide();
				}

				$btn.find('span').text(label);
				$input.val(value).trigger('cat_selected');

				hideList();
				inputPadding();
			});

			function showList() {
				$list.addClass('xts-opened');

				if (typeof ($.fn.devbridgeAutocomplete) != 'undefined') {
					$dropdown.siblings('[type="text"]').devbridgeAutocomplete('hide');
				}
			}

			function hideList() {
				$list.removeClass('xts-opened');
			}

			function inputPadding() {
				if (XTSThemeModule.isMobile() || $searchInput.hasClass('xts-padding-inited') || 'yes' !== xts_settings.search_input_padding) {
					return;
				}

				var paddingValue = $dropdown.innerWidth() + 17;

				if (!$dropdown.parents('.searchform').hasClass('xts-style-default') && !$dropdown.parents('.searchform').hasClass('xts-style-icon-alt-2')) {
					paddingValue += $dropdown.parent().siblings('.searchsubmit').innerWidth();
				}

				var padding = 'padding-right';

				if (XTSThemeModule.$body.hasClass('rtl')) {
					padding = 'padding-left';
				}

				$searchInput.css(padding, paddingValue);
				$searchInput.addClass('xts-padding-inited');
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.searchCatDropdown();
	});
})(jQuery);