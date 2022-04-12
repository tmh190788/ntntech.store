/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_product_tabs.default', function() {
		XTSThemeModule.productsTabs();
		XTSThemeModule.$document.trigger('xtsElementorProductTabsReady');
		XTSThemeModule.$document.trigger('xts_countDownTimer');
	});

	XTSThemeModule.productsTabs = function() {
		var process = false;

		$('.xts-products-tabs').each(function() {
			var $wrapper = $(this);
			var $content = $wrapper.find('.xts-products-tab-content');
			var cache = [];

			cache[0] = {
				html: $content.html()
			};

			$wrapper.find('.xts-products-tab-title').on('click', function(e) {
				e.preventDefault();

				var $this = $(this);
				var atts = $this.data('atts');
				var index = $this.index();

				if (process || $this.hasClass('xts-active')) {
					return;
				}

				process = true;

				loadTab(atts, index, $content, $this, cache, function(data) {
					var itemQueue = [];
					var queueTimer;

					// Animations
					function processItemQueue(delay) {
						if (queueTimer) {
							return;
						}

						queueTimer = window.setInterval(function() {
							if (itemQueue.length) {
								$(itemQueue.shift()).addClass('xts-animated');
								processItemQueue(delay);
							} else {
								window.clearInterval(queueTimer);
								queueTimer = null;
							}
						}, delay);
					}

					if (data.html) {
						$content.html(data.html);

						if ($content.find('.xts-products.xts-in-view-animation').length > 0 ) {
							$content.find('.xts-products').removeClass('xts-inited xts-loaded');

							// Animations
							if (typeof ($.fn.xtsWaypoint) !== 'undefined') {
								$content.find('.xts-col').each(function() {
									var $element = $(this);

									$element.data('xts-waypoint', 'inited');

									$element.xtsWaypoint(function() {
										var $this = $($(this)[0].element);
										var delay = $this.parents('.xts-in-view-animation').data('animation-delay');

										$this.addClass('xts-animation-ready');

										itemQueue.push($this);
										processItemQueue(delay);

									}, {
										offset: '90%'
									});
								});
							}
						}

						XTSThemeModule.$document.trigger('xtsProductTabLoaded');
						XTSThemeModule.$document.trigger('xtsImagesLoaded');
					}
				});
			});
		});

		var loadTab = function(atts, index, $holder, $btn, cache, callback) {
			$btn.parent().find('.xts-active').removeClass('xts-active');
			$btn.addClass('xts-active');

			if (cache[index]) {
				$holder.addClass('xts-loading');
				setTimeout(function() {
					callback(cache[index]);
					$holder.removeClass('xts-loading');
					process = false;
				}, 300);
				return;
			}

			$holder.addClass('xts-loading').parent().addClass('xts-loading');

			$btn.addClass('xts-loading');

			$.ajax({
				url     : xts_settings.ajaxurl,
				data    : {
					atts  : atts,
					action: 'xts_get_products_tab_element'
				},
				dataType: 'json',
				method  : 'POST',
				success : function(data) {
					cache[index] = data;
					callback(data);
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() {
					$holder.removeClass('xts-loading').parent().removeClass('xts-loading');
					$btn.removeClass('xts-loading');
					process = false;
				}
			});
		};
	};

	$(document).ready(function() {
		XTSThemeModule.productsTabs();
	});
})(jQuery);