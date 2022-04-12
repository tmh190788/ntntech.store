/* global xts_settings */
(function($) {
	XTSThemeModule.$document.on('xtsPjaxComplete', function() {
		XTSThemeModule.layeredNavDropdown();
	});

	XTSThemeModule.layeredNavDropdown = function() {
		$('.xts-widget-layered-nav-dropdown-form').each(function() {
			var $form = $(this);
			var $select = $form.find('select');
			var slug = $select.data('slug');

			$select.change(function() {
				var val = $(this).val();
				$('input[name=filter_' + slug + ']').val(val);
			});

			if ($().selectWoo) {
				$select.selectWoo({
					placeholder            : $select.data('placeholder'),
					minimumResultsForSearch: 5,
					width                  : '100%',
					allowClear             : !$select.attr('multiple'),
					language               : {
						noResults: function() {
							return $select.data('noResults');
						}
					}
				}).on('select2:unselecting', function() {
					$(this).data('unselecting', true);
				}).on('select2:opening', function(e) {
					if ($(this).data('unselecting')) {
						$(this).removeData('unselecting');
						e.preventDefault();
					}
				});
			}
		});

		function ajaxAction($element) {
			var $form = $element.parent('.xts-widget-layered-nav-dropdown-form');
			if ('no' === xts_settings.ajax_shop || typeof ($.fn.pjax) == 'undefined') {
				return;
			}

			$.pjax({
				container: '.xts-site-content',
				timeout  : xts_settings.pjax_timeout,
				url      : $form.attr('action'),
				data     : $form.serialize(),
				scrollTo : false
			});
		}

		$('.xts-widget-layered-nav-dropdown__submit').on('click', function(e) {
			var $this = $(this);
			if (!$this.siblings('select').attr('multiple') || 'no' === xts_settings.ajax_shop) {
				return;
			}

			ajaxAction($this);

			$this.prop('disabled', true);
		});

		$('.xts-widget-layered-nav-dropdown-form select').on('change', function(e) {
			var $this = $(this);
			if ('no' === xts_settings.ajax_shop) {
				$this.parent().submit();
				return;
			}

			if ($this.attr('multiple')) {
				return;
			}

			ajaxAction($(this));
		});
	};

	$(document).ready(function() {
		XTSThemeModule.layeredNavDropdown();
	});
})(jQuery);