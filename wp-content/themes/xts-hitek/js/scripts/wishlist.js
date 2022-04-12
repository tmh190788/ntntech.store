/* global xts_settings */
(function($) {
	XTSThemeModule.wishlist = function() {
		if ('undefined' === typeof Cookies) {
			return;
		}

		var cookiesName = 'xts_wishlist_count';

		if (XTSThemeModule.$body.hasClass('logged-in')) {
			cookiesName += '_logged';
		}

		if (xts_settings.is_multisite) {
			cookiesName += '_' + xts_settings.current_blog_id;
		}

		var $widget = $('.xts-header-el.xts-header-wishlist, .xts-navbar-wishlist');
		var cookie = Cookies.get(cookiesName);

		if ($widget.length > 0 && 'undefined' !== typeof cookie) {
			try {
				var count = JSON.parse(cookie);
				$widget.find('.xts-wishlist-count, .xts-navbar-count').text(count);
			}
			catch (e) {
				console.log('cant parse cookies json');
			}
		}

		// Add to wishlist action
		XTSThemeModule.$body.on('click', '.xts-wishlist-btn a', function(e) {
			var $this = $(this);
			var productId = $this.data('product-id');
			var addedText = $this.data('added-text');
			var key = $this.data('key');

			if ($this.hasClass('xts-added')) {
				return true;
			}

			e.preventDefault();

			$this.addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action    : 'xts_add_to_wishlist',
					product_id: productId,
					key       : key
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response) {
						$this.addClass('xts-added');
						XTSThemeModule.$document.trigger('xtsAddedToWishlist');

						if (response.wishlist_content) {
							updateWishlist(response);
						}

						if ($this.find('span').length > 0) {
							$this.find('span').text(addedText);
						} else {
							$this.text(addedText);
						}
					} else {
						console.log('something wrong loading wishlist data ',
							response);
					}
				},
				error   : function(data) {
					console.log(
						'We cant add to wishlist. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.removeClass('xts-loading');
				}
			});

		});

		XTSThemeModule.$body.on('click', '.xts-remove-wishlist-btn', function(e) {
			var $this = $(this);
			var productId = $this.data('product-id');
			var key = $this.data('key');

			if ($this.find('a').hasClass('xts-loading')) {
				return true;
			}

			e.preventDefault();

			$this.find('a').addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					action    : 'xts_remove_from_wishlist',
					product_id: productId,
					key       : key
				},
				dataType: 'json',
				method  : 'GET',
				success : function(response) {
					if (response.wishlist_content) {
						updateWishlist(response);

						XTSThemeModule.$document.trigger('xtsWishlistRemoveSuccess');
					} else {
						console.log('something wrong loading wishlist data ',
							response);
					}
				},
				error   : function(data) {
					console.log(
						'We cant remove from wishlist. Something wrong with AJAX response. Probably some PHP conflict.');
				},
				complete: function() {
					$this.find('a').removeClass('xts-loading');
				}
			});

		});

		// Elements update after ajax
		function updateWishlist(data) {
			if ($widget.length > 0) {
				$widget.find('.xts-wishlist-count, .xts-navbar-count').text(data.count);
			}

			if ($('.xts-wishlist-content').length > 0 && !$('.xts-wishlist-content').hasClass('xts-wishlist-preview')) {
				$('.xts-wishlist-content').replaceWith(data.wishlist_content);
			}
		}
	};

	$(document).ready(function() {
		XTSThemeModule.wishlist();
	});
})(jQuery);
