/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.productCategoriesWidgetAccordion();
	});

	XTSThemeModule.productCategoriesWidgetAccordion = function() {
		var $widget = $('.widget_product_categories');
		var $list = $widget.find('.product-categories');
		var time = 300;

		$('.dropdown_product_cat').on('change', function() {
			if ($(this).val() !== '') {
				var this_page;
				var home_url = xts_settings.home_url;

				if (home_url.indexOf('?') > 0) {
					this_page = home_url + '&product_cat=' + jQuery(this).val();
				} else {
					this_page = home_url + '?product_cat=' + jQuery(this).val();
				}

				location.href = this_page;
			} else {
				location.href = xts_settings.shop_url;
			}
		});

		$widget.each(function() {
			var $select = $(this).find('select');

			if ($().selectWoo) {
				$select.selectWoo({
					minimumResultsForSearch: 5,
					width                  : '100%',
					allowClear             : true,
					placeholder            : xts_settings.product_categories_placeholder,
					language               : {
						noResults: function() {
							return xts_settings.product_categories_no_results;
						}
					}
				});
			}
		});

		if ('no' === xts_settings.product_categories_widget_accordion) {
			return;
		}

		$list.find('.cat-parent').each(function() {
			var $this = $(this);
			if ($this.find(' > .xts-cats-toggle').length > 0 || $this.find(' > .children').length === 0) {
				return;
			}

			$this.find('> ul').before('<div class="xts-cats-toggle"></div>');
		});

		$list.on('click', '.xts-cats-toggle', function() {
			var $btn = $(this);
			var $subList = $btn.next();

			if ($subList.hasClass('xts-shown')) {
				$btn.removeClass('xts-active');
				$subList.stop().slideUp(time).removeClass('xts-shown');
			} else {
				$subList.parent().parent().find('> li > .xts-shown').slideUp().removeClass('xts-shown');
				$subList.parent().parent().find('> li > .xts-active').removeClass('xts-active');
				$btn.addClass('xts-active');
				$subList.stop().slideDown(time).addClass('xts-shown');
			}
		});

		if ($list.find('li.current-cat.cat-parent, li.current-cat-parent').length > 0) {
			$list.find('li.current-cat.cat-parent, li.current-cat-parent').find('> .xts-cats-toggle').click();
		}

		$widget.addClass('xts-loaded');
	};

	$(document).ready(function() {
		XTSThemeModule.productCategoriesWidgetAccordion();
	});
})(jQuery);