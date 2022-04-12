var xtsFramework;
/* global jQuery, wp, xtsTypography, WebFont */

(function($) {
	'use strict';

	xtsFramework = (function() {

		var xtsFrameworkAdmin = {

			optionsPage: function() {
				$('.xts-options').each(function() {
					var $options = $(this);
					var $lastTab = $options.find('.xts-last-tab-input');

					$options.on('click', '.xts-sections-nav a', function(e) {
						e.preventDefault();
						var $btn = $(this),
						    id   = $btn.data('id');

						$lastTab.val(id);

						$options.find('.xts-fields-section.xts-fields-section').removeClass('xts-active-section').addClass('xts-hidden');

						$options.find('.xts-fields-section[data-id="' + id + '"]').addClass('xts-active-section').removeClass('xts-hidden');

						$options.find('.xts-active-nav').removeClass('xts-active-nav');

						$options.find('a[data-id="' + id + '"]').parent().addClass('xts-active-nav');

						if ($btn.parent().hasClass('xts-subsection-nav')) {
							$btn.parent().parent().parent().addClass('xts-active-nav');
						}

						if ($btn.parent().hasClass('xts-has-child')) {
							var $first = $btn.parent().find('.xts-subsection-nav').first();
							var firstId = $first.find('> a').data('id');
							$btn.parent().find('.xts-subsection-nav').first().addClass('xts-active-nav');
							$options.find('.xts-fields-section.xts-fields-section').removeClass('xts-active-section').addClass('xts-hidden');
							$options.find('.xts-fields-section[data-id="' + firstId + '"]').addClass('xts-active-section').removeClass('xts-hidden');
							$lastTab.val(firstId);
						}

						$(document).trigger('xts_section_changed');
					});

					$(document).trigger('xts_section_changed');

					$options.on('click', '.xts-reset-options-btn', function(e) {
						return confirm('All your options will be reset to default values. Continue?');
					});
				});

				$('.toplevel_page_xtemos_options').parent().find('li a').on('click', function(e) {
					var $this = $(this),
					    href  = $this.attr('href');

					activateSection(href, $this, e);
				});

				if ($('.xts-options-message').length <= 0) {
					activateSection(window.location.href, false, false);
				}

				function activateSection(href, $this, event) {
					var section = false;

					if (href) {
						var hrefParts = href.split('tab=');
						if (hrefParts[1]) {
							section = hrefParts[1];
						}
					}

					if (!section) {
						return true;
					}

					var $sectionLink = $('.xts-sections-nav [data-id="' + section + '"]');

					if ($sectionLink.length === 0) {
						return true;
					}

					$sectionLink.trigger('click');

					if ($this) {
						event.preventDefault();
						$this.parent().parent().find('.current').removeClass('current');
						$this.parent().addClass('current');
					}
				}
			},

			optionsPresetsCheckbox: function($checkbox) {
				var $options = $('.xts-options');
				var $fieldsToSave = $options.find('.xts-fields-to-save');

				var $field = $checkbox.parents('.xts-field');
				var checked = $checkbox.prop('checked');
				var name = $checkbox.data('name');

				if (!checked) {
					$field.removeClass('xts-field-disabled');
					addField(name);
				} else {
					$field.addClass('xts-field-disabled');
					removeField(name);
				}

				function addField(name) {
					var current     = $fieldsToSave.val(),
					    fieldsArray = current.split(','),
					    index       = fieldsArray.indexOf(name);

					if (index > -1) {
						return;
					}

					if (current.length == 0) {
						fieldsArray = [name];
					} else {
						fieldsArray.push(name);
					}

					$fieldsToSave.val(fieldsArray.join(','));
				}

				function removeField(name) {
					var current     = $fieldsToSave.val(),
					    fieldsArray = current.split(','),
					    index       = fieldsArray.indexOf(name);

					if (index > -1) {
						fieldsArray.splice(index, 1);
						$fieldsToSave.val(fieldsArray.join(','));
					}
				}
			},

			optionsPresets: function() {
				var $options        = $('.xts-options'),
				    $checkboxes     = $options.find(
					    '.xts-inherit-checkbox-wrapper input'),
				    $presetsWrapper = $options.find('.xts-presets-wrapper'),
				    currentID       = $presetsWrapper.data('current-id'),
				    nonceValue      = $presetsWrapper.find('[name="_wpnonce"]').val(),
				    baseUrl         = $presetsWrapper.data('base-url'),
				    presetUrl       = $presetsWrapper.data('preset-url');

				initSelect2();

				$presetsWrapper.on('click', '.xts-add-new-preset', function(e) {
					e.preventDefault();

					if (isInAction()) {
						return;
					}

					var name = prompt('Enter new preset name', 'New preset');

					if (!name || name.length == 0) {
						return;
					}

					startLoading();

					$.ajax({
						url     : xtsAdminConfig.ajaxUrl,
						method  : 'POST',
						data    : {
							action  : 'xts_new_preset_action',
							name    : name,
							preset  : currentID,
							security: nonceValue
						},
						dataType: 'json',
						success : function(r) {
							if (r.ui && r.ui.length > 10) {
								// updateUI(r.ui);
								window.location = presetUrl + r.id;
							}
							xtsFrameworkAdmin.hideNotice();
						},
						error   : function(r) {
							window.location = baseUrl;
							console.log('ajax error', r);
						},
						complete: function() {
							stopLoading();
						}
					});

				}).on('click', '.xts-remove-preset-btn', function(e) {
					e.preventDefault();

					if (isInAction() || !confirm(
						'Are you sure you want to remove this preset?')) {
						return;
					}

					var id = $(this).data('id');

					startLoading();

					$.ajax({
						url     : xtsAdminConfig.ajaxUrl,
						method  : 'POST',
						data    : {
							action  : 'xts_remove_preset_action',
							id      : id,
							preset  : currentID,
							security: nonceValue
						},
						dataType: 'json',
						success : function(r) {
							if (r.ui && r.ui.length > 10) {
								if (id == currentID) {
									window.location = baseUrl;
								} else {
									updateUI(r.ui);
								}
							}
							xtsFrameworkAdmin.hideNotice();
						},
						error   : function(r) {
							window.location = baseUrl;
							console.log('ajax error', r);
						},
						complete: function() {
							stopLoading();
						}
					});
				}).on('submit', 'form', function(e) {
					e.preventDefault();
					var data = [];

					$presetsWrapper.find('form').find('.xts-rule').each(function() {
						data.push({
							type      : $(this).find('.xts-rule-type').val(),
							comparison: $(this).find('.xts-rule-comparison').val(),
							post_type : $(this).find('.xts-rule-post-type').val(),
							taxonomy  : $(this).find('.xts-rule-taxonomy').val(),
							custom    : $(this).find('.xts-rule-custom').val(),
							value_id  : $(this).find('.xts-rule-value-id').val()
						});
					});

					startLoading();

					$.ajax({
						url     : xtsAdminConfig.ajaxUrl,
						method  : 'POST',
						data    : {
							action  : 'xts_save_preset_conditions_action',
							data    : data,
							preset  : currentID,
							security: nonceValue
						},
						dataType: 'json',
						success : function(r) {
							if (r.ui && r.ui.length > 10) {
								updateUI(r.ui);
								$('.xts-presets-wrapper .xts-presets-response').html('<div class="xts-notice xts-success">' + r.success_msg + '</div>');
							}
							xtsFrameworkAdmin.hideNotice();
						},
						error   : function(r) {
							$('.xts-presets-wrapper .xts-presets-response').html('<div class="xts-notice xts-error">' + r.error_msg + '</div>');
						},
						complete: function() {
							stopLoading();
						}
					});
				}).on('click', '.xts-add-preset-rule', function(e) {
					e.preventDefault();
					var $template = $presetsWrapper.find('.xts-rule-template').clone();
					$template.find('.xts-rule').removeClass('xts-hidden');
					$presetsWrapper.find('.xts-condition-rules').append($template.html());
					initSelect2();
				}).on('click', '.xts-remove-preset-rule', function(e) {
					e.preventDefault();
					$(this).parent().remove();
				}).on('change', '.xts-rule-type', function(e) {
					var $type     = $(this),
					    $rule     = $type.parents('.xts-rule'),
					    $postType = $rule.find('.xts-rule-post-type'),
					    $taxonomy = $rule.find('.xts-rule-taxonomy'),
					    $custom   = $rule.find('.xts-rule-custom'),
					    $valueID  = $rule.find('.xts-rule-value-wrapper'),
					    type      = $type.val();

					switch (type) {
						case 'post_type':
							$postType.show();
							$taxonomy.hide();
							$custom.hide();
							$valueID.hide();
							break;
						case 'taxonomy':
							$postType.hide();
							$taxonomy.show();
							$custom.hide();
							$valueID.hide();
							break;
						case 'post_id':
						case 'term_id':
							$postType.hide();
							$taxonomy.hide();
							$custom.hide();
							$valueID.show();
							break;
						case 'custom':
							$postType.hide();
							$taxonomy.hide();
							$custom.show();
							$valueID.hide();
							break;
					}
				});

				$checkboxes.on('change', function() {
					xtsFrameworkAdmin.optionsPresetsCheckbox($(this));
				});

				function updateUI(html) {
					$presetsWrapper.html($(html).html());
					initSelect2();
				}

				function initSelect2() {
					$presetsWrapper.find('.xts-condition-rules .xts-rule').each(function() {
						var $rule  = $(this),
						    $field = $rule.find('.xts-rule-value-id');

						$field.select2({
							ajax             : {
								url     : xtsAdminConfig.ajaxUrl,
								data    : function(params) {
									var query = {
										action  : 'xts_get_entity_ids_action',
										type    : $rule.find('.xts-rule-type').val(),
										security: nonceValue,
										name    : params.term
									};

									return query;
								},
								method  : 'POST',
								dataType: 'json'
								// Additional AJAX parameters go here; see
								// the end of this chapter for the full
								// code of this example
							},
							theme            : 'xts',
							dropdownAutoWidth: false,
							width            : 'resolve'
						});
					});
				}

				function isInAction() {
					return $presetsWrapper.hasClass('xts-presets-loading');
				}

				function startLoading() {
					$presetsWrapper.addClass('xts-presets-loading');
				}

				function stopLoading() {
					$presetsWrapper.removeClass('xts-presets-loading');
				}

			},

			switcherControl: function() {
				var $switchers = $('.xts-active-section .xts-switcher-control');

				if ($switchers.length <= 0) {
					return;
				}

				$switchers.each(function() {
					var $field    = $(this),
					    $switcher = $field.find('.xts-switcher-btn'),
					    $input    = $field.find('input[type="hidden"]');

					if ($field.hasClass('xts-field-inited')) {
						return;
					}

					$switcher.on('click', function() {
						if ($switcher.hasClass('xts-active')) {
							$input.val(0).change();
							$switcher.removeClass('xts-active');
						} else {
							$input.val(1).change();
							$switcher.addClass('xts-active');
						}
					});

					$field.addClass('xts-field-inited');
				});
			},

			buttonsControl: function() {
				var $sets = $('.xts-active-section .xts-buttons-control');

				if ($sets.length <= 0) {
					return;
				}

				$sets.each(function() {
					var $set   = $(this),
					    $input = $set.find('input[type="hidden"]');

					if ($set.hasClass('xts-field-inited')) {
						return;
					}

					$set.on('click', '.xts-set-item', function() {
						var $btn = $(this);
						if ($btn.hasClass('xts-btns-set-active')) {
							return;
						}
						var val = $btn.data('value');

						$set.find('.xts-btns-set-active').removeClass('xts-btns-set-active');

						$btn.addClass('xts-btns-set-active');

						$input.val(val).change();
					});

					$set.addClass('xts-field-inited');
				});
			},

			colorControl: function() {
				var $colors = $('.xts-active-section .xts-color-control');

				if ($colors.length <= 0) {
					return;
				}

				$colors.each(function() {
					var $color = $(this),
					    $input = $color.find('input[type="text"]');

					if ($color.hasClass('xts-field-inited')) {
						return;
					}

					$input.wpColorPicker();

					$color.addClass('xts-field-inited');
				});
			},

			checkboxControl: function() {
				var $checkboxes = $('.xts-active-section .xts-checkbox-control');

				if ($checkboxes.length <= 0) {
					return;
				}

				$checkboxes.each(function() {
					var $checkbox = $(this).find('input');

					if ($checkbox.hasClass('xts-field-inited')) {
						return;
					}

					$checkbox.change(function() {
						if ($checkbox.prop('checked')) {
							$checkbox.val('on');
						} else {
							$checkbox.val('off');
						}
					});

					$checkbox.addClass('xts-field-inited');
				});
			},

			uploadControl: function(force_init) {
				var $uploads = $('.xts-active-section .xts-upload-control');

				if (force_init) {
					$uploads = $('.widget-content .xts-upload-control');
				}

				if ($uploads.length <= 0) {
					return;
				}

				$uploads.each(function() {
					var $upload       = $(this),
					    $removeBtn    = $upload.find('.xts-btn-remove'),
					    $inputURL     = $upload.find('input.xts-upload-input-url'),
					    $inputID      = $upload.find('input.xts-upload-input-id'),
					    $preview      = $upload.find('.xts-upload-preview'),
					    $previewInput = $upload.find('.xts-upload-preview-input');

					if ($upload.hasClass('xts-field-inited') && !force_init) {
						return;
					}

					$upload.off('click').on('click', '.xts-upload-btn, img', function(e) {
						e.preventDefault();

						var custom_uploader = wp.media({
							title   : 'Insert file',
							button  : {
								text: 'Use this file' // button label text
							},
							multiple: false // for multiple image selection set
							// to true
						}).on('select', function() { // it also has "open" and "close" events
							var attachment = custom_uploader.state().get('selection').first().toJSON();
							$inputID.val(attachment.id).trigger('change');
							$inputURL.val(attachment.url.split(xtsAdminConfig.wpUploadDir.baseurl)[1]);
							$previewInput.val(attachment.url.split(xtsAdminConfig.wpUploadDir.baseurl)[1]);
							$preview.find('img').remove();
							$preview.prepend('<img src="' + attachment.url + '" />');
							$removeBtn.addClass('xts-active');
						}).open();
					});

					$removeBtn.on('click', function(e) {
						e.preventDefault();

						if ($preview.find('img').length == 1) {
							$preview.find('img').remove();
						} else {
							$preview.find('input').val('');
						}

						$previewInput.val('');
						$inputID.val('').trigger('change');
						$inputURL.val('');
						$removeBtn.removeClass('xts-active');
					});

					$upload.addClass('xts-field-inited');
				});
			},

			uploadListControl: function(force_init) {
				var $uploads = $('.xts-active-section .xts-upload_list-control');

				if (force_init) {
					$uploads = $('.widget-content .xts-upload_list-control');
				}

				if ($uploads.length <= 0) {
					return;
				}

				$uploads.each(function() {
					var $upload = $(this);
					var $inputID = $upload.find('input.xts-upload-input-id');
					var $preview = $upload.find('.xts-upload-preview');
					var $clearBtn = $upload.find('.xts-btn-remove');

					if ($upload.hasClass('xts-field-inited') && !force_init) {
						return;
					}

					$upload.off('click').on('click', '.xts-upload-btn, img', function(e) {
						e.preventDefault();

						var custom_uploader = wp.media({
							title   : 'Insert file',
							button  : {
								text: 'Use this file' // button label text
							},
							multiple: true // for multiple image selection set
							// to true
						}).on('select', function() { // it also has "open" and "close" events
							var attachments = custom_uploader.state().get('selection');
							var inputIdValue = $inputID.val();

							attachments.map(function(attachment) {
								attachment = attachment.toJSON();

								if (attachment.id) {
									var attachment_image = attachment.sizes &&
									attachment.sizes.thumbnail ?
										attachment.sizes.thumbnail.url :
										attachment.url;
									inputIdValue = inputIdValue ? inputIdValue +
										',' + attachment.id : attachment.id;

									$preview.append(
										'<div data-attachment_id="' +
										attachment.id + '"><img src="' +
										attachment_image +
										'"><a href="#" class="xts-remove"><span class="dashicons dashicons-dismiss"></span></a></div>');
								}
							});

							$inputID.val(inputIdValue).trigger('change');
							$clearBtn.addClass('xts-active');
						}).open();
					});

					$preview.on('click', '.xts-remove', function(e) {
						e.preventDefault();
						$(this).parent().remove();

						var attachmentIds = '';

						$preview.find('div').each(function() {
							var attachmentId = $(this).attr('data-attachment_id');
							attachmentIds = attachmentIds + attachmentId + ',';
						});

						$inputID.val(attachmentIds).trigger('change');

						if (!attachmentIds) {
							$clearBtn.removeClass('xts-active');
						}
					});

					$clearBtn.on('click', function(e) {
						e.preventDefault();
						$preview.empty();
						$inputID.val('').trigger('change');
						$clearBtn.removeClass('xts-active');
					});

					$upload.addClass('xts-field-inited');
				});
			},

			selectControl: function(force_init) {
				var $select = $('.xts-active-section .xts-select.xts-select2:not(.xts-autocomplete)');

				if (force_init) {
					$select = $('.widget-content .xts-select.xts-select2:not(.xts-autocomplete)');
				}

				if ($select.length > 0) {
					var select2Defaults = {
						width      : '100%',
						allowClear : true,
						theme      : 'xts',
						placeholder: {
							id  : '0',
							text: 'Select'
						}
					};

					$select.each(function() {
						var $field = $(this);
						if ($field.hasClass('xts-field-inited')) {
							return;
						}

						if ($field.attr('multiple')) {
							$field.on('select2:select', function(e) {
								var $elm = $(e.params.data.element);
								$(this).append($elm);
								$(this).trigger('change.select2');
							});

							$field.parent().find('.xts-select2-all').on('click', function(e) {
								e.preventDefault();

								$field.select2('destroy').find('option').prop('selected', 'selected').end().select2(select2Defaults);
							});

							$field.parent().find('.xts-deselect2-all').on('click', function(e) {
								e.preventDefault();

								$field.select2('destroy').find('option').prop('selected', false).end().select2(select2Defaults);
							});
						}

						if ($field.parents('#widget-list').length > 0) {
							return;
						}

						$field.select2(select2Defaults);

						$field.addClass('xts-field-inited');
					});
				}

				$('.xts-active-section .xts-select.xts-select2.xts-autocomplete').each(function() {
					var $field = $(this);
					var type = $field.data('type');
					var value = $field.data('value');
					var search = $field.data('search');

					if ($field.hasClass('xts-field-inited')) {
						return;
					}

					$field.select2({
						theme            : 'xts',
						allowClear       : true,
						placeholder      : 'Select',
						dropdownAutoWidth: false,
						width            : 'resolve',
						ajax             : {
							url           : xtsAdminConfig.ajaxUrl,
							data          : function(params) {
								return {
									action: search,
									type  : type,
									value : value,
									params: params
								};
							},
							method        : 'POST',
							dataType      : 'json',
							delay         : 250,
							processResults: function(data) {
								return {
									results: data
								};
							},
							cache         : true
						}
					});

					$field.addClass('xts-field-inited');
				});
			},

			backgroundControl: function() {
				var $bgs = $('.xts-active-section .xts-background-control');

				if ($bgs.length <= 0) {
					return;
				}

				$bgs.each(function() {
					var $bg               = $(this),
					    $removeBtn        = $bg.find('.xts-btn-remove'),
					    $inputURL         = $bg.find('input.xts-upload-input-url'),
					    $inputID          = $bg.find('input.xts-upload-input-id'),
					    $preview          = $bg.find('.xts-upload-preview'),
					    $colorInput       = $bg.find('.xts-bg-color input[type="text"]'),
					    $bgPreview        = $bg.find('.xts-bg-preview'),
					    $repeatSelect     = $bg.find('.xts-bg-repeat'),
					    $sizeSelect       = $bg.find('.xts-bg-size'),
					    $attachmentSelect = $bg.find('.xts-bg-attachment'),
					    $positionSelect   = $bg.find('.xts-bg-position'),
					    $imageOptions     = $bg.find('.xts-bg-image-options'),
					    $customPosition   = $bg.find('.xts-bg-position-custom'),
					    $customPositionX  = $bg.find('.xts-bg-position-custom .xts-position-x input'),
					    $customPositionY  = $bg.find('.xts-bg-position-custom .xts-position-y input'),
					    data              = {};

					if ($bg.hasClass('xts-field-inited')) {
						return;
					}

					$colorInput.wpColorPicker({
						change: function() {
							updatePreview();
						},
						clear : function() {
							updatePreview();
						}
					});

					$bg.find('select').select2({
						allowClear       : true,
						theme            : 'xts',
						dropdownAutoWidth: false,
						width            : 'resolve'
					});

					$bg.on('click', '.xts-upload-btn, img', function(e) {
						e.preventDefault();

						var custom_uploader = wp.media({
							title   : 'Insert image',
							library : {
								// uncomment the next line if you want to
								// attach image to the current post uploadedTo
								// : wp.media.view.settings
								//
								// post.id,
								type: 'image'
							},
							button  : {
								text: 'Use this image' // button label text
							},
							multiple: false // for multiple image selection set
							// to true
						}).on('select', function() { // it also has "open" and "close" events
							var attachment = custom_uploader.state().get('selection').first().toJSON();
							$inputURL.val(attachment.url.split(xtsAdminConfig.wpUploadDir.baseurl)[1]);
							$inputID.val(attachment.id);
							$preview.find('img').remove();
							$preview.prepend(
								'<img src="' + attachment.url + '" />');
							$removeBtn.addClass('xts-active');
							$imageOptions.addClass('xts-active');
							updatePreview();
						}).open();
					});

					$removeBtn.on('click', function(e) {
						e.preventDefault();
						$preview.find('img').remove();
						$inputID.val('');
						$inputURL.val('');
						$imageOptions.removeClass('xts-active');
						$removeBtn.removeClass('xts-active');
						updatePreview();
					});

					$bg.on('change', 'select, .xts-position-x input, .xts-position-y input', function() {
						updatePreview();

						if ('custom' === $positionSelect.val()) {
							$customPosition.addClass('xts-active');
						} else {
							$customPosition.removeClass('xts-active');
						}
					});

					function updatePreview() {
						data.backgroundColor = $colorInput.val();
						data.backgroundImage = 'url(' + xtsAdminConfig.wpUploadDir.baseurl + $inputURL.val() + ')';
						data.backgroundRepeat = $repeatSelect.val();
						data.backgroundSize = $sizeSelect.val();
						data.backgroundAttachment = $attachmentSelect.val();
						if ('custom' === $positionSelect.val()) {
							data.backgroundPosition = $customPositionX.val() + 'px ' + $customPositionY.val() + 'px';
						} else {
							data.backgroundPosition = $positionSelect.val();
						}
						data.height = 100;

						if (data.backgroundColor || $inputURL.val()) {
							$bgPreview.css(data).show();
						} else {
							$bgPreview.hide();
						}
					}

					$bg.addClass('xts-field-inited');
				});
			},

			customFontsControl: function() {
				var $custom_fonts = $('.xts-active-section .xts-custom-fonts');

				if ($custom_fonts.length <= 0) {
					return;
				}

				$custom_fonts.each(function() {
					var $parent = $(this);

					if ($parent.hasClass('xts-field-inited')) {
						return;
					}

					$parent.on('click', '.xts-custom-fonts-btn-add', function(e) {
						e.preventDefault();

						var $template = $parent.find(
							'.xts-custom-fonts-template').clone();
						var key = $parent.data('key') + 1;

						$parent.find('.xts-custom-fonts-sections').append($template);
						var regex = /{{index}}/gi;
						$template.removeClass(
							'xts-custom-fonts-template hide').html($template.html().replace(regex, key)).attr('data-id', $template.attr('data-id').replace(regex, key));

						$parent.data('key', key);

						xtsFrameworkAdmin.uploadControl(false);
					});

					$parent.on('click', '.xts-custom-fonts-btn-remove', function(e) {
						e.preventDefault();

						$(this).parent().parent().remove();
					});

					$parent.addClass('xts-field-inited');
				});
			},

			typographyControl: function() {
				var $typography = $('.xts-active-section .xts-advanced-typography-field');

				if ($typography.length <= 0) {
					return;
				}

				var isSelecting     = false,
				    selVals         = [],
				    select2Defaults = {
					    width     : '100%',
					    allowClear: true,
					    theme     : 'xts'
				    },
				    defaultVariants = {
					    '100'      : 'Thin 100',
					    '200'      : 'Extra Light 200',
					    '300'      : 'Light 300',
					    '400'      : 'Normal 400',
					    '500'      : 'Medium 500',
					    '600'      : 'Semi Bold 600',
					    '700'      : 'Bold 700',
					    '800'      : 'Extra Bold 800',
					    '900'      : 'Black 900',
					    '100italic': 'Thin 100 Italic',
					    '200italic': 'Extra Light 200 Italic',
					    '300italic': 'Light 300 Italic',
					    '400italic': 'Normal 400 Italic',
					    '500italic': 'Medium 500 Italic',
					    '600italic': 'Semi Bold 600 Italic',
					    '700italic': 'Bold 700 Italic',
					    '800italic': 'Extra Bold 800 Italic',
					    '900italic': 'Black 900 Italic'
				    };

				$typography.each(function() {
					var $parent = $(this);

					if ($parent.hasClass('xts-field-inited')) {
						return;
					}

					$parent.find('.xts-typography-section:not(.xts-typography-template)').each(function() {
						var $section = $(this),
						    id       = $section.data('id');

						initTypographySection($parent, id);
					});

					$parent.on('click', '.xts-typography-btn-add', function(e) {
						e.preventDefault();

						var $template = $parent.find('.xts-typography-template').clone(),
						    key       = $parent.data('key') + 1;

						$parent.find('.xts-typography-sections').append($template);
						var regex = /{{index}}/gi;

						$template.removeClass('xts-typography-template hide').html($template.html().replace(regex, key)).attr('data-id', $template.attr('data-id').replace(regex, key));

						$parent.data('key', key);

						initTypographySection($parent, $template.attr('data-id'));
					});

					$parent.on('click', '.xts-typography-btn-remove', function(e) {
						e.preventDefault();

						$(this).parents('.xts-typography-section').remove();
					});

					$parent.addClass('xts-field-inited');
				});

				function initTypographySection($parent, id) {
					var $section            = $parent.find('[data-id="' + id + '"]'),
					    $family             = $section.find('.xts-typography-family'),
					    $familyInput        = $section.find('.xts-typography-family-input'),
					    $googleInput        = $section.find('.xts-typography-google-input'),
					    $customInput        = $section.find('.xts-typography-custom-input'),
					    $customSelector     = $section.find('.xts-typography-custom-selector'),
					    $selector           = $section.find('.xts-typography-selector'),
					    $transform          = $section.find('.xts-typography-transform'),
					    $color              = $section.find('.xts-typography-color'),
					    $colorHover         = $section.find('.xts-typography-color-hover'),
					    $colorActive        = $section.find('.xts-typography-color-active'),
					    $responsiveControls = $section.find('.xts-typography-responsive-controls');

					if ($family.data('value') !== '') {
						$family.val($family.data('value'));
					}

					syncronizeFontVariants($section, true, false);

					//init when value is changed
					$section.find(
						'.xts-typography-family, .xts-typography-style, .xts-typography-subset').on(
						'change', function() {
							syncronizeFontVariants($section, false, false);
						}
					);

					var fontFamilies = [
						    {
							    id  : '',
							    text: ''
						    }
					    ],
					    customFonts  = {
						    text    : 'Custom fonts',
						    children: []
					    },
					    stdFonts     = {
						    text    : 'Standard fonts',
						    children: []
					    },
					    googleFonts  = {
						    text    : 'Google fonts',
						    children: []
					    };

					$.map(xtsTypography.stdfonts, function(val, i) {
						stdFonts.children.push({
							id      : i,
							text    : val,
							selected: (i == $family.data('value'))
						});

					});

					$.map(xtsTypography.googlefonts, function(val, i) {
						googleFonts.children.push({
							id      : i,
							text    : i,
							google  : true,
							selected: (i == $family.data('value'))
						});
					});

					$.map(xtsTypography.customFonts, function(val, i) {
						customFonts.children.push({
							id      : i,
							text    : i,
							selected: (i == $family.data('value'))
						});
					});

					if (customFonts.children.length > 0) {
						fontFamilies.push(customFonts);
					}

					fontFamilies.push(stdFonts);
					fontFamilies.push(googleFonts);

					$family.on('select change click', function() {
						if ($family.hasClass('xts-field-inited')) {
							return;
						}

						$family.addClass('xts-field-inited');

						$family.empty();

						$family.select2({
							data             : fontFamilies,
							allowClear       : true,
							theme            : 'xts',
							dropdownAutoWidth: false,
							width            : 'resolve'
						}).on(
							'select2:selecting',
							function(e) {
								var data = e.params.args.data;
								var fontName = data.text;

								$familyInput.attr('value', fontName);

								// option values
								selVals = data;
								isSelecting = true;

								syncronizeFontVariants($section, false, true);
							}
						).on(
							'select2:unselecting',
							function(e) {
								$(this).one('select2:opening', function(ev) {
									ev.preventDefault();
								});
							}
						).on(
							'select2:unselect',
							function(e) {
								$familyInput.val('');

								$googleInput.val('false');

								$family.val(null).change();

								syncronizeFontVariants($section, false, true);
							}
						);

						$family.select2('open');
					});

					// CSS selector multi select field
					$selector.select2(select2Defaults).on(
						'select2:selecting',
						function(e) {
							var val = e.params.args.data.id;
							if (val != 'custom') {
								return;
							}
							$customInput.val(true);
							$customSelector.removeClass('hide');
						}
					).on(
						'select2:unselect',
						function(e) {
							console.log(e);
							var val = e.params.data.id;
							if (val != 'custom') {
								return;
							}
							$customInput.val('');
							$customSelector.val('').addClass('hide');
						}
					);

					$transform.select2(select2Defaults);

					// Color picker fields
					$color.wpColorPicker({
						change: function(event, ui) {
							// needed for palette click
							setTimeout(function() {
								updatePreview($section);
							}, 5);
						}
					});
					$colorHover.wpColorPicker();
					$colorActive.wpColorPicker();

					// Responsive font size and line height
					$responsiveControls.on('click',
						'.xts-typography-responsive-opener',
						function() {
							var $this = $(this);
							$this.parent().find(
								'.xts-typography-control-tablet, .xts-typography-control-mobile').toggleClass('show hide');
						}).on('change', 'input', function() {
						updatePreview($section);
					});
				}

				function updatePreview($section) {
					var sectionFields = {
						familyInput: $section.find(
							'.xts-typography-family-input'),
						weightInput: $section.find(
							'.xts-typography-weight-input'),
						preview    : $section.find('.xts-typography-preview'),
						sizeInput  : $section.find(
							'.xts-typography-size-container .xts-typography-control-desktop input'),
						heightInput: $section.find(
							'.xts-typography-height-container .xts-typography-control-desktop input'),
						colorInput : $section.find('.xts-typography-color')
					};

					var size   = sectionFields.sizeInput.val(),
					    height = sectionFields.heightInput.val(),
					    weight = sectionFields.weightInput.val(),
					    color  = sectionFields.colorInput.val(),
					    family = sectionFields.familyInput.val();

					if (!height) {
						height = size;
					}

					//show in the preview box the font
					sectionFields.preview.css('font-weight', weight).css('font-family', family + ', sans-serif').css('font-size', size + 'px').css('line-height', height + 'px');

					if (family == 'none' || family == '') {
						//if selected is not a font remove style "font-family"
						// at preview box
						$(sectionFields.preview).parent().hide();
					} else {
						$(sectionFields.preview).parent().show();
					}

					if (color) {
						var bgVal = '#444444';
						if (color !== '') {
							// Replace the hash with a blank.
							color = color.replace('#', '');

							var r = parseInt(color.substr(0, 2), 16);
							var g = parseInt(color.substr(2, 2), 16);
							var b = parseInt(color.substr(4, 2), 16);
							var res = ((r * 299) + (g * 587) + (b * 114)) /
								1000;
							bgVal = (res >= 128) ? '#444444' : '#ffffff';
						}
						console.log(color);
						sectionFields.preview.css('color', '#' + color).css('background-color', bgVal);
					}

					sectionFields.preview.slideDown();
				}

				function loadGoogleFont(family, style, script) {

					if (family == null || family == 'inherit') {
						return;
					}

					//add reference to google font family
					//replace spaces with "+" sign
					var link = family.replace(/\s+/g, '+');

					if (style && style !== '') {
						link += ':' + style.replace(/\-/g, ' ');
					}

					if (script && script !== '') {
						link += '&subset=' + script;
					}

					if (typeof (WebFont) !== 'undefined' && WebFont) {
						WebFont.load({
							google: {
								families: [link]
							}
						});
					}
				}

				function syncronizeFontVariants($section, init, changeFamily) {
					var sectionFields = {
						family     : $section.find('.xts-typography-family'),
						familyInput: $section.find(
							'.xts-typography-family-input'),
						style      : $section.find('select.xts-typography-style'),
						styleInput : $section.find(
							'.xts-typography-style-input'),
						weightInput: $section.find(
							'.xts-typography-weight-input'),
						subsetInput: $section.find(
							'.xts-typography-subset-input'),
						subset     : $section.find('select.xts-typography-subset'),
						googleInput: $section.find(
							'.xts-typography-google-input'),
						preview    : $section.find('.xts-typography-preview'),
						sizeInput  : $section.find(
							'.xts-typography-size-container .xts-typography-control-desktop input'),
						heightInput: $section.find(
							'.xts-typography-height-container .xts-typography-control-desktop input'),
						colorInput : $section.find('.xts-typography-color')
					};

					// Set all the variables to be checked against
					var family = sectionFields.familyInput.val();

					if (!family) {
						family = null; //"inherit";
					}

					var style = sectionFields.style.val();
					var script = sectionFields.subset.val();

					// Is selected font a google font?
					var google;
					if (isSelecting === true) {
						google = selVals.google;
						sectionFields.googleInput.val(google);
					} else {
						google = xtsFrameworkAdmin.makeBool(
							sectionFields.googleInput.val()
						); // Check if font is a google font
					}

					// Page load. Speeds things up memory wise to offload to
					// client
					if (init) {
						style = sectionFields.style.data('value');
						script = sectionFields.subset.data('value');

						if (style !== '') {
							style = String(style);
						}

						if (typeof (script) !== undefined) {
							script = String(script);
						}
					}

					// Something went wrong trying to read google fonts, so
					// turn google off
					if (xtsTypography.googlefonts === undefined) {
						google = false;
					}

					// Get font details
					var details = '';
					if (google === true &&
						(family in xtsTypography.googlefonts)) {
						details = xtsTypography.googlefonts[family];
					} else {
						details = defaultVariants;
					}

					sectionFields.subsetInput.val(script);

					// If we changed the font. Selecting variable is set to
					// true only when family field is opened
					if (isSelecting || init || changeFamily) {
						var html = '<option value=""></option>';

						// Google specific stuff
						if (google === true) {

							// STYLES
							var selected = '';
							$.each(
								details.variants,
								function(index, variant) {
									if (variant.id === style ||
										xtsFrameworkAdmin.size(
											details.variants) === 1) {
										selected = ' selected="selected"';
										style = variant.id;
									} else {
										selected = '';
									}

									html += '<option value="' + variant.id +
										'"' + selected + '>' +
										variant.name.replace(
											/\+/g, ' '
										) + '</option>';
								}
							);

							// destroy select2
							if (sectionFields.subset.data('select2')) {
								sectionFields.style.select2('destroy');
							}

							// Instert new HTML
							sectionFields.style.html(html);

							// Init select2
							sectionFields.style.select2(select2Defaults);

							// SUBSETS
							selected = '';
							html = '<option value=""></option>';

							$.each(
								details.subsets,
								function(index, subset) {
									if (subset.id === script ||
										xtsFrameworkAdmin.size(
											details.subsets) === 1) {
										selected = ' selected="selected"';
										script = subset.id;
										sectionFields.subset.val(script);
									} else {
										selected = '';
									}
									html += '<option value="' + subset.id +
										'"' + selected + '>' +
										subset.name.replace(
											/\+/g, ' '
										) + '</option>';
								}
							);

							// Destroy select2
							if (sectionFields.subset.data('select2')) {
								sectionFields.subset.select2('destroy');
							}

							// Inset new HTML
							sectionFields.subset.html(html);

							// Init select2
							sectionFields.subset.select2(select2Defaults);

							sectionFields.subset.parent().fadeIn('fast');
							// $( '#' + mainID + ' .typography-family-backup'
							// ).fadeIn( 'fast' );
						} else {
							if (details) {
								$.each(
									details,
									function(index, value) {
										if (index === style || index ===
											'normal') {
											selected = ' selected="selected"';
											sectionFields.style.find(
												'.select2-chosen').text(value);
										} else {
											selected = '';
										}

										html += '<option value="' + index +
											'"' + selected + '>' +
											value.replace(
												'+', ' '
											) + '</option>';
									}
								);

								// Destory select2
								if (sectionFields.subset.data('select2')) {
									sectionFields.style.select2('destroy');
								}

								// Insert new HTML
								sectionFields.style.html(html);

								// Init select2
								sectionFields.style.select2(select2Defaults);

								// Prettify things
								sectionFields.subset.parent().fadeOut('fast');
							}
						}

						sectionFields.familyInput.val(family);
					}

					// Check if the selected value exists. If not, empty it.
					// Else, apply it.
					if (sectionFields.style.find(
						'option[value=\'' + style + '\']').length === 0) {
						style = '';
						sectionFields.style.val('');
					} else if (style === '400') {
						sectionFields.style.val(style);
					}

					// Weight and italic
					if (style.indexOf('italic') !== -1) {
						sectionFields.preview.css('font-style', 'italic');
						sectionFields.styleInput.val('italic');
						style = style.replace('italic', '');
					} else {
						sectionFields.preview.css('font-style', 'normal');
						sectionFields.styleInput.val('');
					}

					sectionFields.weightInput.val(style);

					// Handle empty subset select
					if (sectionFields.subset.find(
						'option[value=\'' + script + '\']').length === 0) {
						script = '';
						sectionFields.subset.val('');
						sectionFields.subsetInput.val(script);
					}

					if (google) {
						loadGoogleFont(family, style, script);
					}

					// if (!init) {
					updatePreview($section);
					// }

					isSelecting = false;
				}
			},

			makeBool: function(val) {
				if (val == 'false' || val == '0' || val === false || val ===
					0) {
					return false;
				} else if (val == 'true' || val == '1' || val === true || val ==
					1) {
					return true;
				}
			},

			size: function(obj) {
				var size = 0,
				    key;

				for (key in obj) {
					if (obj.hasOwnProperty(key)) {
						size++;
					}
				}

				return size;
			},

			megaMenu: function() {
				// Design
				$('.xts-design select').on('change', function() {
					var selectValue = $(this).val();
					var $block = $(this).parents('li').find('.xts-block');
					var $width = $(this).parents('li').find('.xts-width');
					var $height = $(this).parents('li').find('.xts-height');
					var $ajax = $(this).parents('li').find('.xts-dropdown-ajax');

					if ('full' === selectValue) {
						$block.show();
						$ajax.show();
						$width.hide();
						$height.hide();
					}

					if ('container' === selectValue) {
						$block.show();
						$ajax.show();
						$width.hide();
						$height.hide();
					}

					if ('sized' === selectValue) {
						$block.show();
						$ajax.show();
						$width.show();
						$height.show();
					}

					if ('default' === selectValue) {
						$block.hide();
						$ajax.hide();
						$width.hide();
						$height.hide();
					}
				}).change();

				// Menu block edit link
				$('.xts-block select').change(function() {
					var data = $(this).find('option:selected').data('edit-link');

					if (data) {
						$('.edit-block-link').attr('href', data).show();
					} else {
						$('.edit-block-link').hide();
					}
				});

				// Transfer
				var $menuItems = $('ul#menu-to-edit li.menu-item');
				$menuItems.each(function() {
					var $item = $(this);
					var $title = $item.find('.field-title-attribute');
					var $customFields = $item.find(
						'.xts-mega-menu-custom-fields');
					$title.after($customFields);
					$customFields.show();
				});

				//Image
				var $menuImageItems = $('.xts-mega-menu-image-wrapper');
				$menuImageItems.each(function() {
					var $megaMenu = $(this);
					var $preview = $megaMenu.find(
						'.xts-mega-menu-image-preview');
					var $uploadBtn = $megaMenu.find('.xts-mega-menu-upload');
					var $removeBtn = $megaMenu.find('.xts-mega-menu-remove');
					var $inputID = $megaMenu.find(
						'input.xts-mega-menu-image-id');

					$uploadBtn.on('click', function(e) {
						e.preventDefault();

						var custom_uploader = wp.media({
							title   : 'Insert image',
							library : {
								type: 'image'
							},
							button  : {
								text: 'Use this image'
							},
							multiple: false
						}).on('select', function() {
							var attachment = custom_uploader.state().get('selection').first().toJSON();
							$inputID.val(attachment.id);
							$preview.find('img').remove();
							$preview.prepend(
								'<img src="' + attachment.url + '" />');
							$removeBtn.addClass('xts-active');
						}).open();

					});

					$removeBtn.on('click', function(e) {
						e.preventDefault();
						$preview.find('img').remove();
						$inputID.val('');
						$removeBtn.removeClass('xts-active');
					});
				});
			},

			rangeControl: function() {
				var $ranges = $('.xts-active-section .xts-range-control');

				if ($ranges.length <= 0) {
					return;
				}

				$ranges.each(function() {
					var $range  = $(this),
					    $input  = $range.find('.xts-range-value'),
					    $slider = $range.find('.xts-range-slider'),
					    $text   = $range.find('.xts-range-field-value-text'),
					    data    = $input.data();

					if ($range.hasClass('xts-field-inited')) {
						return;
					}

					$slider.slider({
						range: 'min',
						value: data.start,
						min  : data.min,
						max  : data.max,
						step : data.step,
						slide: function(event, ui) {
							$input.val(ui.value).trigger('change');
							$text.text(ui.value);
						}
					});

					// Initiate the display
					$input.val($slider.slider('value')).trigger('change');
					$text.text($slider.slider('value'));

					$range.addClass('xts-field-inited');
				});
			},

			editorControl: function() {
				var $editor = $('.xts-active-section .xts-editor-control');
				if ($editor.length <= 0) {
					return;
				}

				$editor.each(function() {
					var $editor  = $(this),
					    $field   = $editor.find('textarea'),
					    language = $field.data('language');

					if ($editor.hasClass('xts-field-inited')) {
						return;
					}

					var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};

					editorSettings.codemirror = _.extend({},
						editorSettings.codemirror, {
							indentUnit: 2,
							tabSize   : 2,
							mode      : language
						}
					);

					wp.codeEditor.initialize($field, editorSettings);

					$editor.addClass('xts-field-inited');
				});
			},

			fieldsDependencies: function() {
				var $fields = $('.xts-field[data-dependency]');

				$fields.each(function() {
					var $field       = $(this),
					    dependencies = $field.data('dependency').split(';');

					dependencies.forEach(function(dependency) {
						if (dependency.length == 0) {
							return;
						}
						var data = dependency.split(':');

						var $parentField = $('.xts-' + data[0] + '-field');

						$parentField.on('change', 'input, select', function(e) {
							testFieldDependency($field, dependencies);
						});

						$parentField.find('input, select').change();
					});

				});

				function testFieldDependency($field, dependencies) {
					var show = true;
					dependencies.forEach(function(dependency) {
						if (dependency.length == 0 || show == false) {
							return;
						}
						var data         = dependency.split(':'),
						    $parentField = $('.xts-' + data[0] + '-field'),
						    value        = $parentField.find('input[type=hidden], select').val();

						switch (data[1]) {
							case 'equals':
								var values = data[2].split(',');
								show = false;
								for (var i = 0; i < values.length; i++) {
									var element = values[i];
									if (value == element) {
										show = true;
									}
								}
								break;
							case 'not_equals':
								var values = data[2].split(',');
								show = true;
								for (var i = 0; i < values.length; i++) {
									var element = values[i];
									if (value == element) {
										show = false;
									}
								}
								break;
						}

					});

					if (show) {
						$field.addClass('xts-shown').removeClass('xts-hidden');
						$field.find('.xts-css-output').val('1');
					} else {
						$field.addClass('xts-hidden').removeClass('xts-shown');
						$field.find('.xts-css-output').val('0');
					}
				}
			},

			settingsSearch: function() {
				var $searchForm  = $('.xts-options-search'),
				    $searchInput = $searchForm.find('input');

				if (0 === $searchForm.length) {
					return;
				}

				$searchForm.find('form').submit(function(e) {
					e.preventDefault();
				});

				var $autocomplete = $searchInput.autocomplete({
					source: function(request, response) {
						var results = xtsAdminConfig.xtsOptions.filter(function(value) {
							return value.text.search(new RegExp(request.term, 'i')) != -1;
						});

						response(results.slice(0, 16));
					},

					select: function(event, ui) {
						var $field = $('.xts-' + ui.item.id + '-field');

						$('.xts-sections-nav a[data-id="' + ui.item.section_id + '"]').click();

						$('.xts-highlight-field').removeClass('xts-highlight-field');
						$field.addClass('xts-highlight-field');

						setTimeout(function() {
							if (!isInViewport($field)) {
								$('html, body').animate({
									scrollTop: $field.offset().top - 200
								}, 400);
							}
						}, 300);
					}

				}).data('ui-autocomplete');

				$autocomplete._renderItem = function(ul, item) {
					var $itemContent = '<span class="xts-section-icon ' + item.icon + '"></span><span class="xts-setting-title">' + item.title + '</span><br><span class="xts-settting-path">' + item.path + '</span>';
					return $('<li>').append($itemContent).appendTo(ul);
				};

				$autocomplete._renderMenu = function(ul, items) {
					var that = this;

					$.each(items, function(index, item) {
						that._renderItemData(ul, item);
					});

					$(ul).addClass('xts-settings-result');
				};

				var isInViewport = function($el) {
					var elementTop = $el.offset().top;
					var elementBottom = elementTop + $el.outerHeight();
					var viewportTop = $(window).scrollTop();
					var viewportBottom = viewportTop + $(window).height();
					return elementBottom > viewportTop && elementTop + 200 < viewportBottom;
				};
			},

			htmlBlockEditLink: function() {
				$('.xts-html-block-links').each(function() {
					var $wrapper = $(this);
					var $select = $wrapper.find('select');
					var $link = $wrapper.find('.xts-edit-block-link');

					if ( $link.length > 0 ) {
						changeLink();

						$select.change(function() {
							changeLink();
						});

						function changeLink() {
							var selectValue = $select.find('option:selected').val();
							var currentHref = $link.attr('href');

							var newHref = currentHref.split('post=')[0] + 'post=' + selectValue + '&action=elementor';

							if (!selectValue || '0' === selectValue || 0 === selectValue) {
								$link.hide();
							} else {
								$link.attr('href', newHref).show();
							}
						}
					}
				});
			},

			imageOptimizer: function() {

				$('body').on('click', '.xts-optimize-image', function(e) {
					e.preventDefault();

					var $btn     = $(this),
					    id       = $btn.data('id'),
					    security = $btn.data('security');

					$.ajax({
						url     : xtsAdminConfig.ajaxUrl,
						method  : 'POST',
						data    : {
							action  : 'xts_optimize_image',
							id      : id,
							security: security
						},
						dataType: 'json',
						success : function(r) {
							if (r.success) {
								var $result = $btn.parent().parent().find('.xts-optimizer-result');
								$result.find('.xts-optimizer-original span').text(r.original_size_text);
								$result.find('.xts-optimizer-saved span').text(r.saved_bytes_text);
								$result.find('.xts-optimizer-optimized span').text(r.optimized_size_text);
								$result.removeClass('hidden');
							} else if (r.errors) {
								$btn.parent().find('.xts-optimization-error').remove();
								$btn.parent().append(
									'<span class="xts-optimization-error">' +
									r.errors + '</span>');
							}
						},
						complete: function(r) {
							console.log('complete');
						},
						error   : function(r) {
							console.log('error');
						}
					});
				});
			},

			sizeGuideTableControl: function() {
				if ($.fn.editTable) {
					var table = $('.xts-size-guide-table-field textarea').editTable();

					setTimeout(function() {
						$('.editor-post-publish-button, .editor-post-publish-panel__toggle').on('click', function() {
							$('.xts-size-guide-table-field textarea').val(table.getJsonData());
						});
					});
				}
			},

			additionalVariationImages: function() {
				$('#woocommerce-product-data').on('woocommerce_variations_loaded', function() {
					$('.xts-avi-wrapper').each(function() {
						var $this = $(this);
						var $galleryImages = $this.find('.xts-avi-list');
						var $imageGalleryIds = $this.find('.xts-variation-gallery-ids');
						var galleryFrame;

						$this.find('.xts-avi-add-image').on('click', function(event) {
							event.preventDefault();

							// If the media frame already exists, reopen it.
							if (galleryFrame) {
								galleryFrame.open();
								return;
							}

							// Create the media frame.
							galleryFrame = wp.media.frames.product_gallery = wp.media({
								states: [
									new wp.media.controller.Library({
										filterable: 'all',
										multiple  : true
									})
								]
							});

							// When an image is selected, run a callback.
							galleryFrame.on('select', function() {
								var selection = galleryFrame.state().get('selection');
								var attachment_ids = $imageGalleryIds.val();

								selection.map(function(attachment) {
									attachment = attachment.toJSON();

									if (attachment.id) {
										var attachment_image = attachment.sizes && attachment.sizes.thumbnail ?
											attachment.sizes.thumbnail.url :
											attachment.url;
										attachment_ids = attachment_ids ?
											attachment_ids + ',' + attachment.id :
											attachment.id;

										$galleryImages.append('<div class="xts-avi-image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '"><a href="#" class="xts-avi-remove-image xts-remove"><span class="dashicons dashicons-dismiss"></span></a></li>');
									}
								});

								$imageGalleryIds.val(attachment_ids);

								triggerChange();
							});

							// Finally, open the modal.
							galleryFrame.open();
						});

						// Image ordering.
						if (typeof $galleryImages.sortable !== 'undefined') {
							$galleryImages.sortable({
								items               : 'div.xts-avi-image',
								cursor              : 'move',
								scrollSensitivity   : 40,
								forcePlaceholderSize: true,
								forceHelperSize     : false,
								helper              : 'clone',
								opacity             : 0.65,
								placeholder         : 'wc-metabox-sortable-placeholder',
								start               : function(event, ui) {
									ui.item.css('background-color', '#f6f6f6');
								},
								stop                : function(event, ui) {
									ui.item.removeAttr('style');
								},
								update              : function() {
									var attachment_ids = '';

									$galleryImages.find('div.xts-avi-image').each(function() {
										var attachment_id = $(this).attr('data-attachment_id');
										attachment_ids = attachment_ids + attachment_id + ',';
									});

									$imageGalleryIds.val(attachment_ids);

									triggerChange();
								}
							});
						}

						// Remove images.
						$(document).on('click', '.xts-avi-remove-image', function(event) {
							event.preventDefault();
							$(this).parent().remove();

							var attachment_ids = '';

							$galleryImages.find('div.xts-avi-image').each(function() {
								var attachment_id = $(this).attr('data-attachment_id');
								attachment_ids = attachment_ids + attachment_id + ',';
							});

							$imageGalleryIds.val(attachment_ids);

							triggerChange();
						});

						function triggerChange() {
							$this.parents('.woocommerce_variation').eq(0).addClass('variation-needs-update');
							$('#variable_product_options').find('input').eq(0).change();
						}

					});

				});
			},

			responsiveFields: function() {
				$('.xts-field-responsive-selector').on('click', '.xts-responsive-switch', function() {
					var $this     = $(this),
					    $field    = $this.parents('.xts-field'),
					    id        = $field.data('id'),
					    generalId = id.split('_tablet')[0].split('_mobile')[0].split('_mobile_small')[0];

					$('.xts-' + generalId + '-field').hide();
					$('.xts-' + generalId + '_tablet-field').hide();
					$('.xts-' + generalId + '_mobile-field').hide();
					$('.xts-' + generalId + '_mobile_small-field').hide();

					if ($this.hasClass('xts-switch-tablet')) {
						$('.xts-' + generalId + '_tablet-field').show();
					} else if ($this.hasClass('xts-switch-mobile')) {
						$('.xts-' + generalId + '_mobile-field').show();
					} else if ($this.hasClass('xts-switch-mobile_small')) {
						$('.xts-' + generalId + '_mobile_small-field').show();
					} else {
						$('.xts-' + generalId + '-field').show();
					}
				});

				$('.xts-inherit-checkbox-wrapper input').on('change', function() {
					var $field = $(this).parents('.xts-field');
					var id = $field.data('id');
					var generalId = id.split('_tablet')[0].split('_mobile')[0];

					if ($field.hasClass('xts-desktop-field') || $field.hasClass('xts-tablet-field') || $field.hasClass('xts-mobile-field') || $field.hasClass('xts-mobile_small-field')) {
						var $tablet = $('.xts-' + generalId + '_tablet-field');
						var $mobile = $('.xts-' + generalId + '_mobile-field');
						var $mobileSmall = $('.xts-' + generalId + '_mobile_small-field');
						var $desktop = $('.xts-' + generalId + '-field');

						var $tabletCheckbox = $tablet.find('.xts-inherit-checkbox-wrapper input');
						var $mobileCheckbox = $mobile.find('.xts-inherit-checkbox-wrapper input');
						var $mobileSmallCheckbox = $mobileSmall.find('.xts-inherit-checkbox-wrapper input');
						var $desktopCheckbox = $desktop.find('.xts-inherit-checkbox-wrapper input');

						if (!$(this).prop('checked')) {
							$tablet.removeClass('xts-field-disabled');
							$mobile.removeClass('xts-field-disabled');
							$mobileSmall.removeClass('xts-field-disabled');
							$desktop.removeClass('xts-field-disabled');

							$tabletCheckbox.prop('checked', false);
							$mobileCheckbox.prop('checked', false);
							$mobileSmallCheckbox.prop('checked', false);
							$desktopCheckbox.prop('checked', false);
						} else {
							$tablet.addClass('xts-field-disabled');
							$mobile.addClass('xts-field-disabled');
							$mobileSmall.addClass('xts-field-disabled');
							$desktop.addClass('xts-field-disabled');

							$tabletCheckbox.prop('checked', true);
							$mobileCheckbox.prop('checked', true);
							$mobileSmallCheckbox.prop('checked', true);
							$desktopCheckbox.prop('checked', true);
						}

						xtsFrameworkAdmin.optionsPresetsCheckbox($tabletCheckbox);
						xtsFrameworkAdmin.optionsPresetsCheckbox($mobileCheckbox);
						xtsFrameworkAdmin.optionsPresetsCheckbox($mobileSmallCheckbox);
						xtsFrameworkAdmin.optionsPresetsCheckbox($desktopCheckbox);
					}
				});
			},

			presetsActive: function() {
				function checkAll() {
					$('.xts-sections-nav li').each(function() {
						var $li = $(this);
						var sectionId = $li.find('a').data('id');

						$('.xts-fields-section[data-id="' + sectionId + '"]').find('.xts-inherit-checkbox-wrapper input').each(function() {
							if (!$(this).prop('checked')) {
								$li.addClass('xts-not-inherit');
							}
						});
					});
				}

				function checkChild() {
					$('.xts-sections-nav .xts-has-child').each(function() {
						var $child = $(this).find('.xts-not-inherit');

						var checkedParent = false;

						if ($child.length > 0) {
							checkedParent = true;
						}

						if (checkedParent) {
							$(this).addClass('xts-not-inherit');
						} else {
							$(this).removeClass('xts-not-inherit');
						}
					});
				}

				checkAll();
				checkChild();

				$('.xts-inherit-checkbox-wrapper input').on('change', function() {
					var sectionId = $(this).parents('.xts-fields-section').data('id');

					var checked = false;
					$(this).parents('.xts-fields-section').find('.xts-inherit-checkbox-wrapper input').each(function() {
						if (!$(this).prop('checked')) {
							checked = true;
						}
					});

					if (checked) {
						$('.xts-sections-nav li a[data-id="' + sectionId + '"]').parent().addClass('xts-not-inherit');
					} else {
						$('.xts-sections-nav li a[data-id="' + sectionId + '"]').parent().removeClass('xts-not-inherit');
					}

					checkChild();
					checkAll();
				});
			},

			hideNotice: function() {
				var $notice = $('.xts-notice:not(.xts-info)');

				$notice.each(function() {
					var $notice = $(this);
					setTimeout(function() {
						$notice.addClass('xts-hidden');
					}, 10000);
				});

				$notice.on('click', function() {
					$(this).addClass('xts-hidden');
				});
			},

			textAreaControl: function() {
				var $editor = $('.xts-active-section .xts-textarea-wysiwyg');

				if ($editor.length <= 0) {
					return;
				}

				$editor.each(function() {
					var $field = $(this);
					var id = $field.attr('id');
					var settings = wp.editor.getDefaultSettings();

					if ($field.hasClass('xts-field-inited')) {
						return;
					}

					settings.tinymce.selector = '#' + id;
					settings.tinymce.branding = false;
					settings.tinymce.toolbar = 'mybutton';
					settings.tinymce.paste_text_sticky = true;
					settings.tinymce.menubar = false;
					settings.tinymce.force_br_newlines = false;
					settings.tinymce.force_p_newlines = false;
					settings.tinymce.forced_root_block = '';
					settings.tinymce.plugins = 'charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview';
					settings.tinymce.toolbar1 = 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv';
					settings.tinymce.toolbar2 = 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help';

					window.tinymce.init(settings.tinymce);

					$('.xts-wysiwyg-buttons button').on('click', function(e) {
						e.preventDefault();

						var $button = $(this);
						var id = $button.data('id');
						var mode = $button.data('mode');

						$button.siblings().removeClass('xts-btns-set-active');
						$button.addClass('xts-btns-set-active');

						switchEditor(id, mode);
					});

					var switchEditor = function(id, mode) {
						var editor = tinymce.get(id);
						var $textarea = $('#' + id);

						if ('visual' === mode) {
							$textarea.attr('aria-hidden', true);
							editor.show();
							window.setUserSetting('editor', 'tinymce');
						} else if ('text' === mode) {
							editor.hide();
							window.setUserSetting('editor', 'html');
							$textarea.css({
								'display'   : '',
								'visibility': ''
							});
							$textarea.attr('aria-hidden', false);
						}
					};

					$editor.addClass($field);
				});
			},

			pluginActivation: function() {
				var checkPlugin = function($link, callback) {
					setTimeout(function() {
						$.ajax({
							url    : xtsAdminConfig.ajaxUrl,
							method : 'POST',
							data   : {
								action    : 'xts_check_plugins',
								xts_plugin: $link.data('plugin')
							},
							success: function(response) {
								if ('success' === response.status) {
									changeNextButtonStatus(response.data.required_plugins);
									changePageStatus(response.data.is_all_activated);
								} else {
									xtsFrameworkAdmin.addNotice($('.xts-plugin-response'), 'warning', response.message);
									removeLinkClasses($link);
									xtsFrameworkAdmin.hideNotice();
								}

								callback(response);
							}
						});
					}, 1000);
				};

				var activatePlugin = function($link, callback) {
					$.ajax({
						url    : xtsPluginsData[$link.data('plugin')]['activate_url'].replaceAll('&amp;', '&'),
						method : 'GET',
						success: function() {
							checkPlugin($link, function(response) {
								if ('success' === response.status) {
									if ('activate' === response.data.status) {
										activatePlugin($link, callback);
									} else {
										removeLinkClasses($link);
										changeLinkAction('activate', 'deactivate', $link, response);
										changeLinkAction('install', 'deactivate', $link, response);
										changeLinkAction('update', 'deactivate', $link, response);
										callback();
									}
								}
							});
						}
					});
				};

				var deactivatePlugin = function($link) {
					$.ajax({
						url    : xtsAdminConfig.ajaxUrl,
						method : 'POST',
						data   : {
							action    : 'xts_deactivate_plugin',
							xts_plugin: $link.data('plugin')
						},
						success: function(response) {
							if ('error' === response.status) {
								xtsFrameworkAdmin.addNotice($('.xts-plugin-response'), 'warning', response.message);
								removeLinkClasses($link);
								xtsFrameworkAdmin.hideNotice();
								return;
							}

							checkPlugin($link, function(response) {
								if ('success' === response.status) {
									if ('activate' === response.data.status) {
										removeLinkClasses($link);
										changeLinkAction('deactivate', 'activate', $link, response);
									} else {
										deactivatePlugin($link);
									}
								}
							});
						}
					});
				};

				function parsePlugins($link, callback) {
					$.ajax({
						url    : $link.attr('href'),
						method : 'POST',
						success: function() {
							setTimeout(function() {
								checkPlugin($link, function(response) {
									if ('success' === response.status) {
										if ('activate' === response.data.status) {
											activatePlugin($link, callback);
										} else {
											removeLinkClasses($link);
											changeLinkAction('activate', 'deactivate', $link, response);
											callback();
										}
									}
								});
							}, 1000);
						}
					});
				}

				function addLinkClasses($link) {
					$link.parents('.xts-plugin-wrapper').addClass('xts-loading');
					$link.parents('.xts-plugin-wrapper').siblings().addClass('xts-disabled');
					$('.xts-ajax-all-plugins').addClass('xts-disabled');
					$('.xts-dashboard-box-footer').addClass('xts-disabled');

					$link.text(xtsAdminConfig[$link.data('action') + '_process_plugin_btn_text']);
				}

				function removeLinkClasses($link) {
					$link.parents('.xts-plugin-wrapper').removeClass('xts-loading');
					$link.parents('.xts-plugin-wrapper').siblings().removeClass('xts-disabled');
					$('.xts-ajax-all-plugins').removeClass('xts-disabled');
					$('.xts-dashboard-box-footer').removeClass('xts-disabled');
				}

				function changeNextButtonStatus(status) {
					var $nextBtn = $('.xts-next-btn');
					if ('has_required' === status) {
						$nextBtn.addClass('xts-disabled');
					} else {
						$nextBtn.removeClass('xts-disabled');
					}
				}

				function changePageStatus(status) {
					var $page = $('.xts-plugins');
					if ('yes' === status) {
						$page.addClass('xts-all-active');
					} else {
						$page.removeClass('xts-all-active');
					}
				}

				function changeLinkAction(actionBefore, actionAfter, $link, response) {
					if (response && response.data.version) {
						$link.parents('.xts-plugin-wrapper').find('.xts-plugin-version').text(response.data.version);
					}

					$link.removeClass('xts-' + actionBefore + '-now').addClass('xts-' + actionAfter + '-now');
					$link.attr('href', xtsPluginsData[$link.data('plugin')][actionAfter + '_url'].replaceAll('&amp;', '&'));
					$link.data('action', actionAfter);
					$link.text(xtsAdminConfig[actionAfter + '_plugin_btn_text']);
				}

				$(document).on('click', '.xts-ajax-plugin:not(.xts-deactivate-now)', function(e) {
					e.preventDefault();

					var $link = $(this);
					addLinkClasses($link);
					parsePlugins($link, function() {});
				});

				$(document).on('click', '.xts-deactivate-now', function(e) {
					e.preventDefault();

					var $link = $(this);
					addLinkClasses($link);
					deactivatePlugin($link);
				});

				$(document).on('click', '.xts-ajax-all-plugins', function(e) {
					e.preventDefault();

					var itemQueue = [];

					function activationAction() {
						if (itemQueue.length) {
							var $link = $(itemQueue.shift());

							addLinkClasses($link);

							parsePlugins($link, function() {
								activationAction();
							});
						}
					}

					$('.xts-plugin-wrapper .xts-ajax-plugin:not(.xts-deactivate-now)').each(function() {
						itemQueue.push($(this));
					});

					activationAction();
				});
			},

			childThemeActivation: function() {
				$('.xts-install-child-theme').on('click', function(e) {
					e.preventDefault();
					var $btn = $(this);
					var $responseSelector = $('.xts-child-theme-response');

					$btn.addClass('xts-loading');

					$.ajax({
						url     : xtsAdminConfig.ajaxUrl,
						method  : 'POST',
						data    : {
							action: 'xts_install_child_theme'
						},
						dataType: 'json',
						success : function(response) {
							$btn.removeClass('xts-loading');

							if (response && 'success' === response.status) {
								$('.xts-child-step').addClass('xts-installed');
							} else if (response && 'dir_not_exists' === response.status) {
								xtsFrameworkAdmin.addNotice($responseSelector, 'error', 'The directory can\'t be created on the server. Please, install the child theme manually or contact our support for help.');
							} else {
								xtsFrameworkAdmin.addNotice($responseSelector, 'error', 'The child theme can\'t be installed. Skip this step and install the child theme manually via Appearance -> Themes.');
							}
						},
						error   : function() {
							$btn.removeClass('xts-loading');

							xtsFrameworkAdmin.addNotice($responseSelector, 'error', 'The child theme can\'t be installed. Skip this step and install the child theme manually via Appearance -> Themes.');
						}
					});
				});
			},

			addNotice: function($selector, $type, $message) {
				$selector.html('<div class="xts-notice xts-' + $type + '">' + $message + '</div>').fadeIn();

				xtsFrameworkAdmin.hideNotice();
			},

			whiteLabel: function() {
				setTimeout(function() {
					$('.theme').on('click',function(){
						themeClass();
					});
					themeClass();
					function themeClass() {
						var $name = $('.theme-overlay .theme-name');
						if ($name.text().includes(xtsAdminConfig.theme_slug)) {
							$('.theme-overlay').addClass('xts-space-theme');
						} else {
							$('.theme-overlay').removeClass('xts-space-theme');
						}
					}
				}, 500);
			}
		};

		return {
			init: function() {
				$(document).ready(function() {
					// Other.
					xtsFrameworkAdmin.hideNotice();
					xtsFrameworkAdmin.megaMenu();
					xtsFrameworkAdmin.pluginActivation();
					xtsFrameworkAdmin.childThemeActivation();
					xtsFrameworkAdmin.whiteLabel();

					// Woocommerce.
					xtsFrameworkAdmin.additionalVariationImages();

					// Theme settings.
					xtsFrameworkAdmin.optionsPage();
					xtsFrameworkAdmin.optionsPresets();
					xtsFrameworkAdmin.presetsActive();
					xtsFrameworkAdmin.settingsSearch();
					xtsFrameworkAdmin.responsiveFields();
					xtsFrameworkAdmin.fieldsDependencies();
					xtsFrameworkAdmin.htmlBlockEditLink();

					// Metabox controls.
					xtsFrameworkAdmin.sizeGuideTableControl();

					// Widget controls.
					xtsFrameworkAdmin.uploadControl(true);
					xtsFrameworkAdmin.uploadListControl(true);
					xtsFrameworkAdmin.selectControl(true);
				});

				$(document).on('widget-updated widget-added', function(e, widget) {
					xtsFrameworkAdmin.uploadControl(true);
					xtsFrameworkAdmin.uploadListControl(true);
					xtsFrameworkAdmin.selectControl(true);
				});

				$(document).on('xts_section_changed', function() {
					setTimeout(function() {
						xtsFrameworkAdmin.typographyControl();
					});
					xtsFrameworkAdmin.customFontsControl();
					xtsFrameworkAdmin.backgroundControl();
					xtsFrameworkAdmin.switcherControl();
					xtsFrameworkAdmin.buttonsControl();
					xtsFrameworkAdmin.checkboxControl();
					xtsFrameworkAdmin.uploadControl(false);
					xtsFrameworkAdmin.uploadListControl(false);
					xtsFrameworkAdmin.selectControl(false);
					xtsFrameworkAdmin.editorControl();
					xtsFrameworkAdmin.textAreaControl();
					xtsFrameworkAdmin.colorControl();
					xtsFrameworkAdmin.rangeControl();
				});
			}
		};

	}());

})(jQuery);

jQuery(document).ready(function() {
	xtsFramework.init();
});