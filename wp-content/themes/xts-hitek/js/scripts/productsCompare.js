/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.productsCompare();
	});

	XTSThemeModule.productsCompare = function() {
		if ('undefined' === typeof Cookies) {
			return;
		}

		var cookiesName = 'xts_compare_list';

		if (xts_settings.is_multisite) {
			cookiesName += '_' + xts_settings.current_blog_id;
		}

		var $body = XTSThemeModule.$body;
		var $widget = $('.xts-header-el.xts-header-compare, .xts-navbar-compare');
		var cookie = Cookies.get(cookiesName);

		if ($widget.length > 0 && 'undefined' !== typeof cookie) {
			try {
				var ids = JSON.parse(cookie);
				$widget.find('.xts-compare-count, .xts-navbar-count').text(ids.length);
			}
			catch (e) {
				console.log('cant parse cookies json');
			}
		}

		// Add to compare action
		$body.on('click', '.xts-compare-btn a', function(e) {
			var $this = $(this);
			var id = $this.data('id');
			var addedText = $this.data('added-text');

			if ($this.hasClass('xts-added')) {
				return true;
			}

			e.preventDefault();

			$this.addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action: 'xts_add_to_compare',
					id    : id
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					XTSThemeModule.$document.trigger('xtsAddedToCompare');
					if (response.table) {
						updateCompare(response);
					} else {
						console.log('something wrong loading compare data ',
							response);
					}
				},
				error   : function() {
					console.log(
						'We cant add to compare. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.removeClass('xts-loading').addClass('xts-added');

					if ($this.find('span').length > 0) {
						$this.find('span').text(addedText);
					} else {
						$this.text(addedText);
					}
				}
			});

		});

		// Remove from compare action
		$body.on('click', '.xts-compare-remove a', function(e) {
			e.preventDefault();

			var $this = $(this);
			var id = $this.data('id');

			$this.addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action: 'xts_remove_from_compare',
					id    : id
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response.table) {
						updateCompare(response);
					} else {
						console.log('something wrong loading compare data ',
							response);
					}
				},
				error   : function() {
					console.log(
						'We cant remove product compare. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.addClass('xts-loading');
				}
			});

		});

		// Elements update after ajax
		function updateCompare(data) {
			if ($widget.length > 0) {
				$widget.find('.xts-compare-count, .xts-navbar-count').text(data.count);
			}

			var $table = $('.xts-compare-table');
			if ($table.length > 0) {
				$table.replaceWith(data.table);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.productsCompare();
	});
})(jQuery);