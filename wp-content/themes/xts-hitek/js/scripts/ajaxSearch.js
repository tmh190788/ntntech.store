/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.ajaxSearch();
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_ajax_search.default', function() {
		XTSThemeModule.ajaxSearch();
	});

	XTSThemeModule.ajaxSearch = function() {
		if (typeof ($.fn.devbridgeAutocomplete) == 'undefined') {
			return;
		}

		var escapeRegExChars = function(value) {
			return value.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
		};

		$('form.xts-ajax-search').each(function() {
			var $this                 = $(this),
			    number                = parseInt($this.data('count')),
			    thumbnail             = parseInt($this.data('thumbnail')),
			    $results              = $this.parents('.xts-search-wrapper').find('.xts-search-results'),
			    postType              = $this.data('post_type'),
			    url                   = xts_settings.ajaxurl + '?action=xts_ajax_search',
			    symbols_count         = parseInt($this.data('symbols_count')),
			    productCat            = $this.find('[name="product_cat"]'),
			    sku                   = $this.data('sku'),
			    categories_on_results = $this.data('categories_on_results'),
			    price                 = parseInt($this.data('price')),
			    $resultsClasses       = $results;

			// Juno.
			if ($this.parents('.xts-search-wrapper').hasClass('xts-design-widgets xts-search-full-screen')) {
				$resultsClasses = $this.parents('.xts-search-wrapper').find('.xts-search-footer');
			}

			// Neptune.
			if ($this.parents('.xts-search-wrapper').find('.xts-shape-overlays').length > 0) {
				$resultsClasses = $this.parents('.xts-search-wrapper').find('.xts-search-results-wrapper');
			}

			if (number > 0) {
				url += '&number=' + number;
			}

			url += '&post_type=' + postType;

			if (productCat.length && productCat.val() !== '') {
				url += '&product_cat=' + productCat.val();
			}

			$results.on('click', '.xts-search-results-btn', function() {
				$this.submit();
			});

			$this.find('[type="text"]').on('focus', function() {
				var $input = $(this);

				if ($input.hasClass('xts-search-inited')) {
					return;
				}

				$input.devbridgeAutocomplete({
					serviceUrl      : url,
					appendTo        : $results.hasClass('xts-dropdown') ? $results.find('.xts-dropdown-inner') : $results,
					minChars        : symbols_count,
					onSelect        : function(suggestion) {
						if (suggestion.permalink.length > 0) {
							window.location.href = suggestion.permalink;
						}
					},
					onHide          : function() {
						$resultsClasses.removeClass('xts-opened');
						$resultsClasses.removeClass('xts-no-results');
					},
					onSearchStart   : function() {
						$this.addClass('search-loading');
					},
					beforeRender    : function(container) {
						$(container).find('.suggestion-divider-title').parent().addClass('suggestion-divider');
						$(container).find('.xts-search-no-found').parent().addClass('suggestion-no-found');
						if (container[0].childElementCount > 2) {
							$(container).append('<div class="xts-search-results-btn">' + xts_settings.all_results + '</div>');
						}
						$(container).removeAttr('style');
					},
					onSearchComplete: function() {
						$this.removeClass('search-loading');
						XTSThemeModule.$document.trigger('xtsImagesLoaded');
					},
					formatResult    : function(suggestion, currentValue) {
						if ('&' === currentValue) {
							currentValue = '&#038;';
						}

						var pattern = '(' + escapeRegExChars(currentValue) + ')';
						var returnValue = '';

						if (suggestion.divider) {
							returnValue += ' <h5 class="suggestion-divider-title">' + suggestion.divider + '</h5>';
						}

						if (thumbnail && suggestion.thumbnail) {
							returnValue += ' <div class="suggestion-thumb">' + suggestion.thumbnail + '</div>';
						}

						if (suggestion.value) {
							returnValue += ' <div class="suggestion-content">';
							returnValue += '<h4 class="suggestion-title xts-entities-title">' + suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>').replace(/&lt;(\/?strong)&gt;/g, '<$1>') + '</h4>';
						}

						if ('yes' === categories_on_results && suggestion.categories) {
							returnValue += ' <div class="suggestion-cat suggestion-meta">' + suggestion.categories + '</div>';
						}

						if ('yes' === sku && suggestion.sku) {
							returnValue += ' <div class="suggestion-sku suggestion-meta">' + suggestion.sku + '</div>';
						}

						if (price && suggestion.price) {
							returnValue += ' <div class="price">' + suggestion.price + '</div>';
						}

						if (suggestion.value) {
							returnValue += ' </div>';
						}

						if (suggestion.no_found) {
							$resultsClasses.addClass('xts-no-results');
							returnValue = '<div class="xts-search-no-found">' + suggestion.value + '</div>';
						} else {
							$resultsClasses.removeClass('xts-no-results');
						}

						$resultsClasses.addClass('xts-opened');
						$resultsClasses.addClass('xts-searched');

						return returnValue;
					}
				});

				if (productCat.length) {
					var searchForm = $this.find('[type="text"]').devbridgeAutocomplete(),
					    serviceUrl = xts_settings.ajaxurl + '?action=xts_ajax_search';

					if (number > 0) {
						serviceUrl += '&number=' + number;
					}

					serviceUrl += '&post_type=' + postType;

					productCat.on('cat_selected', function() {
						if ('' !== productCat.val()) {
							searchForm.setOptions({
								serviceUrl: serviceUrl + '&product_cat=' + productCat.val()
							});
						} else {
							searchForm.setOptions({
								serviceUrl: serviceUrl
							});
						}

						searchForm.hide();
						searchForm.onValueChange();
					});
				}

				$input.addClass('xts-search-inited');
			});

			XTSThemeModule.$document.on('click', function(e) {
				var target = e.target;

				if (!$(target).is('.xts-search-form') && !$(target).parents().is('.xts-search-form')) {
					$this.find('[type="text"]').devbridgeAutocomplete('hide');
				}
			});

			$('.xts-search-results').on('click', function(e) {
				e.stopPropagation();
			});
		});
	};

	$(document).ready(function() {
		XTSThemeModule.ajaxSearch();
	});
})(jQuery);