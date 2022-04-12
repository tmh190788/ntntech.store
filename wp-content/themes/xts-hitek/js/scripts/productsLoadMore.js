/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsProductTabLoaded xtsPjaxComplete', function() {
		XTSThemeModule.productsLoadMore();
	});

	XTSThemeModule.productsLoadMore = function() {
		var infiniteBtnClass = '.xts-load-more.xts-type-shop.xts-action-infinite';
		var process = false;
		var intervalID;

		$('.xts-products').each(function() {
			var $this = $(this);
			var cache = [];
			var id = $this.attr('id');
			var $loadWrap = $('.xts-arrows-loader[data-id="' + id + '"]');
			var $btnWrap = $('.xts-ajax-arrows[data-id="' + id + '"]');

			if ($btnWrap.length <= 0) {
				return;
			}

			cache[1] = {
				items : $this.html(),
				status: 'have-posts'
			};

			XTSThemeModule.$window.on('scroll', function() {
				arrowsPosition();
			});

			setTimeout(function() {
				arrowsPosition();
			}, 500);

			function arrowsPosition() {
				if ($btnWrap.length <= 0) {
					return;
				}

				var offset = XTSThemeModule.$window.height() / 2;
				var scrollTop = XTSThemeModule.$window.scrollTop();
				var holderTop = $this.offset().top - offset;
				var $btnPrev = $btnWrap.find('.xts-prev');
				var btnsHeight = $btnPrev.outerHeight();
				var holderHeight = $this.height() - btnsHeight;
				var holderBottom = holderTop + holderHeight;

				if (scrollTop < holderTop || scrollTop > holderBottom) {
					$btnWrap.removeClass('xts-shown');
					$loadWrap.removeClass('xts-shown');
				} else {
					$btnWrap.addClass('xts-shown');
					$loadWrap.addClass('xts-shown');
				}
			}

			$('.xts-ajax-arrows .xts-prev, .xts-ajax-arrows .xts-next').on('click', function(e) {
				e.preventDefault();
				var $this = $(this);

				if (process || $this.hasClass('xts-disabled')) {
					return;
				}

				process = true;

				clearInterval(intervalID);

				var id = $this.parent().data('id');
				var $holder = $('#' + id);
				var source = $holder.data('source');
				var ajaxurl = xts_settings.ajaxurl;
				var atts = $holder.data('atts');
				var paged = $holder.data('paged');
				var method = 'POST';
				var $next = $this.parent().find('.xts-next');
				var $prev = $this.parent().find('.xts-prev');

				paged++;

				if ($this.hasClass('xts-prev')) {
					if (paged < 2) {
						return;
					}

					paged = paged - 2;
				}

				var data = {
					paged : paged,
					atts  : atts,
					action: 'xts_get_product_' + source
				};

				data.atts.loop = $holder.find('.xts-col').last().data('loop');

				loadProducts(ajaxurl, data, method, $this, cache, paged, $holder, function(data) {
					if (data.items) {
						if ($holder.hasClass('xts-masonry-layout')) {
							var items = $(data.items);
							$holder.html(items).isotope('appended', items);
							$holder.imagesLoaded().progress(function() {
								$holder.isotope('layout');
							});
						} else {
							$holder.html(data.items);
						}

						reInit();

						$holder.data('paged', paged);
					}

					if (XTSThemeModule.isMobileSize) {
						$('html, body').stop().animate({
							scrollTop: $holder.offset().top - 150
						}, 400);
					}

					if (paged > 1) {
						$prev.removeClass('xts-disabled');
					} else {
						$prev.addClass('xts-disabled');
					}

					if ('no-more-posts' === data.status) {
						$next.addClass('xts-disabled');
					} else {
						$next.removeClass('xts-disabled');
					}
				});

			});
		});

		// Load more button
		XTSThemeModule.clickOnScrollButton(infiniteBtnClass, false);

		$('.xts-load-more.xts-type-shop').on('click', function(e) {
			e.preventDefault();

			if (process) {
				return;
			}

			process = true;

			var $this = $(this);
			var id = $this.data('id');
			var $holder = $('#' + id);
			var source = $holder.data('source');
			var ajaxurl = xts_settings.ajaxurl;
			var atts = $holder.data('atts');
			var paged = $holder.data('paged');
			var method = 'POST';

			paged++;

			$this.addClass('xts-loading');

			var data = {
				paged : paged,
				atts  : atts,
				action: 'xts_get_product_' + source
			};

			if ('main_loop' === source) {
				ajaxurl = $this.attr('href');
				method = 'GET';
				data = {
					loop: $holder.find('.xts-col').last().data('loop')
				};
			} else {
				data.atts.loop = $holder.find('.xts-col').last().data('loop');
			}

			loadProducts(ajaxurl, data, method, $this, [], paged, $holder, function(data) {
				if (data.items) {
					if ($holder.hasClass('xts-masonry-layout')) {
						var items = $(data.items);
						$holder.append(items).isotope('appended', items);
						$holder.imagesLoaded().progress(function() {
							$holder.isotope('layout');
						});
					} else {
						$holder.append(data.items);
					}

					reInit();

					$holder.imagesLoaded().progress(function () {
						XTSThemeModule.clickOnScrollButton(infiniteBtnClass, true);
					});

					$holder.data('paged', paged);

					if ('main_loop' === source) {
						$this.attr('href', data.nextPage);
					}
				}

				if ('no-more-posts' === data.status) {
					$this.remove();
				}
			});
		});

		var loadProducts = function(ajaxurl, data, method, $btn, cache, paged, $holder, callback) {
			if (cache[paged]) {
				$holder.addClass('xts-loading');
				setTimeout(function() {
					callback(cache[paged]);
					$holder.removeClass('xts-loading');
					process = false;
				}, 300);
				return;
			}

			var id = $holder.attr('id');
			var $loader = $('.xts-arrows-loader[data-id="' + id + '"]');

			$loader.addClass('xts-loading');
			$holder.addClass('xts-loading');

			if ('GET' === method) {
				ajaxurl = XTSThemeModule.removeURLParameter(ajaxurl, 'loop');
			}

			$.ajax({
				url     : ajaxurl,
				data    : data,
				dataType: 'json',
				method  : method,
				success : function(data) {
					cache[paged] = data;
					callback(data);
					window.history.pushState('', '', data.currentPage);
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() {
					$btn.removeClass('xts-loading');
					$loader.removeClass('xts-loading');
					$holder.removeClass('xts-loading');
					process = false;
				}
			});
		};

		var reInit = function() {
			XTSThemeModule.$document.trigger('xtsProductLoadMoreReInit');
		};
	};

	$(document).ready(function() {
		XTSThemeModule.productsLoadMore();
	});
})(jQuery);