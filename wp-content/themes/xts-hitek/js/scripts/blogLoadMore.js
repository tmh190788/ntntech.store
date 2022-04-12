/* global xts_settings */
(function($) {
	XTSThemeModule.blogLoadMore = function() {
		var infiniteBtnClass = '.xts-load-more.xts-type-blog.xts-action-infinite';
		var process = false;

		XTSThemeModule.clickOnScrollButton(infiniteBtnClass, false);

		$('.xts-load-more.xts-type-blog').on('click', function(e) {
			e.preventDefault();

			if (process) {
				return;
			}

			process = true;

			var $this = $(this);
			var $holder = $this.parent().parent().find('.xts-blog');
			var source = $holder.data('source');
			var ajaxurl = xts_settings.ajaxurl;
			var paged = $holder.data('paged');
			var atts = $holder.data('atts');
			var method = 'POST';

			$this.addClass('xts-loading');

			var data = {
				paged : paged,
				atts  : atts,
				action: 'xts_get_blog_' + source
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

			$.ajax({
				url     : ajaxurl,
				data    : data,
				dataType: 'json',
				method  : method,
				success : function(data) {
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

						XTSThemeModule.$document.trigger('xtsBlogLoadMoreSuccess');

						$holder.imagesLoaded().progress(function () {
							XTSThemeModule.clickOnScrollButton(infiniteBtnClass, true);
						});

						$holder.data('paged', paged + 1);
						window.history.pushState('', '', data.currentPage);

						if ('main_loop' === source) {
							$this.attr('href', data.nextPage);
						}
					}

					if ('no-more-posts' === data.status) {
						$this.remove();
					}
				},
				error   : function() {
					console.log('ajax error');
				},
				complete: function() {
					$this.removeClass('xts-loading');
					process = false;
				}
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.blogLoadMore();
	});
})(jQuery);
