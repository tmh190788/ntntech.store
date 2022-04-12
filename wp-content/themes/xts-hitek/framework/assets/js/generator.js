(function($) {
	'use strict';

	function generator() {
		var $form = $('.xts-generator-form');

		$form.on('change', '.xts-file-value', prepare);
		prepare();

		function prepare() {
			var fields = {};
			var $this = $(this);
			var id = $this.attr('id');
			var checked = $this.prop('checked');
			var $children = $form.find('[data-parent="' + id + '"] [type=\"checkbox\"]');

			$children.prop('checked', checked);

			var parentChecked = function($this) {
				$form.find('[name="' + $this.parent().data('parent') + '"]').each(function() {
					$(this).prop('checked', 'checked');
					if ('none' !== $(this).parent().data('parent')) {
						parentChecked($(this));
					}
				});
			};

			if ('none' !== $this.parent().data('parent')) {
				parentChecked($(this));
			}

			var uncheckedEmpty = function($this) {
				var id = $this.parent().data('parent');
				var $children = $form.find('[data-parent="' + id + '"]');

				if ($children.length > 0) {
					var checked = false;

					$children.each(function() {
						if ($(this).find('[type="checkbox"]').prop('checked')) {
							checked = true;
						}
					});

					if (!checked) {
						$form.find('[name="' + id + '"]').prop('checked', '');
						uncheckedEmpty($form.find('[name="' + id + '"]'));
					}
				}
			};

			uncheckedEmpty($(this));

			$form.find('.xts-generator-checkbox:not(.xts-checkbox-disabled) > .xts-file-value').each(function() {
				fields[this.name] = $(this).prop('checked');
			});

			var base64 = btoa(JSON.stringify(fields));

			$form.find('[name="xts_generator_options_data"]').val(base64);
		}

		$('.xts-generator-update-button').on('click', function(e) {
			e.preventDefault();
			$form.find('[name="xts_generate"]').click();
		});

		$form.on('click', '[name="xts_generate"]', function() {
			$form.addClass('xts-loading');
		});
	}

	function generatorFiles() {
		var $filesList = $('.xts-files-list');
		var baseUrl = $filesList.data('base-url');

		function isInAction() {
			return $filesList.hasClass('xts-loading');
		}

		function startLoading() {
			$filesList.addClass('xts-loading');
		}

		$filesList.on('click', '.xts-add-new-file', function(e) {
			e.preventDefault();

			if (isInAction()) {
				return;
			}

			var name = prompt('Enter new file name', 'New file');

			if (!name || 0 === name.length) {
				return;
			}

			startLoading();

			$.ajax({
				url     : xtsAdminConfig.ajaxUrl,
				data    : {
					action        : 'xts_new_generator_file_action',
					generator_type: $(this).data('generator-type'),
					name          : name
				},
				dataType: 'json',
				success : function(response) {
					if (response.id) {
						window.location = baseUrl + '&xts_file=' + response.id;
					}
				}
			});
		});

		$filesList.on('click', '.xts-remove-file-btn', function(e) {
			e.preventDefault();

			if (isInAction() || !confirm(
				'Are you sure you want to remove this file?')) {
				return;
			}

			var id = $(this).data('id');

			startLoading();

			$.ajax({
				url     : xtsAdminConfig.ajaxUrl,
				data    : {
					action        : 'xts_remove_generator_file_action',
					generator_type: $(this).data('generator-type'),
					id            : id
				},
				dataType: 'json',
				success : function(response) {
					if (response) {
						window.location = baseUrl;
					}
				}
			});
		});
	}

	jQuery(document).ready(function() {
		generator();
		generatorFiles();
	});
})(jQuery);

