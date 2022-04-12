(function($) {
	'use strict';

	$('.xts-dummy-box').each(function() {
		var $import        = $(this),
		    interval       = 0,
		    $responseArea  = $import.find('.xts-dummy-response'),
		    $bar           = $import.find('.xts-dummy-progress-bar'),
		    $pagesSelect   = $import.find('.xts-additional-pages'),
		    initialClearer = 0,
		    fake2timeout   = 0,
		    noticeTimeout  = 0,
		    errorTimeout   = 0;

		$import.on('click', '#xts-submit', function(e) {
			runImport(e, 'base');
		});
		$import.on('click', '#xts-clear', function(e) {
			clearDummyContent(e);
		});
		$import.on('click', '#xts-import-page', function(e) {
			if (!$pagesSelect.val()) {
				$responseArea.html('<div class="xts-notice xts-warning">Please select page.</div>').fadeIn();
				hideNotice();
				return;
			}

			runImport(e, $pagesSelect.val());
		});

		$pagesSelect.on('change', function() {
			var baseUrl = $pagesSelect.data('base-url');
			var value = $pagesSelect.val();
			if (!value) {
				value = 'base';
			}

			$import.find('.xts-dummy-pages-preview img').attr('src', baseUrl + value + '/preview.jpg');
		});

		hideNotice();

		function hideNotice() {
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
		}

		function runImport(e, version) {
			e.preventDefault();

			if ($import.hasClass('xts-form-in-action')) {
				return;
			}

			$import.addClass('xts-form-in-action');

			$('#xts-import-page').addClass('xts-disabled');
			$('#xts-submit').addClass('xts-disabled');
			$('#xts-clear').addClass('xts-disabled');
			$('.xts-view-page-area > a').remove();

			clearInterval(initialClearer);

			fakeLoading(60, 70, 80);

			clearResponseArea();

			callImportAJAX(function(response) {
				clearResponseArea();
				setTimeout(function(){
					handleResponse(response);
				}, 300);
			}, function() {
				clearFakeLoading();

				$import.removeClass('xts-form-in-action');

				updateProgress(100, 0);

				$bar.parent().find('.xts-notice').remove();

				$import.addClass('xts-imported');

				$('#xts-submit').addClass('xts-disabled');
				$('#xts-clear').removeClass('xts-disabled');
				$('#xts-import-page').removeClass('xts-disabled');
				$('#xts-import-page').parents('.xts-dummy-box').find('.xts-dummy-response').html('');

				initialClearer = setTimeout(function() {
					destroyProgressBar(200);
				}, 2000);
			}, {
				action : 'xts_dummy_content',
				version: version
			});
		}

		function clearDummyContent(e) {
			e.preventDefault();

			if ($import.hasClass('xts-form-in-action') || !confirm('Are you sure?')) {
				return;
			}

			$import.addClass('xts-form-in-action');

			$('#xts-import-page').addClass('xts-disabled');
			$('#xts-submit').addClass('xts-disabled');
			$('#xts-clear').addClass('xts-disabled');
			$('.xts-view-page-area > a').remove();

			clearInterval(initialClearer);

			fakeLoading(60, 70, 80);

			clearResponseArea();

			$.ajax({
				url     : xtsAdminConfig.ajaxUrl,
				data    : {
					action: 'xts_clear_dummy_content'
				},
				timeout : 30000,
				success : function(response) {
					clearResponseArea();
					setTimeout(function(){
						handleResponse(response);
					}, 300);
				},
				error   : function() {
					$responseArea.html('<div class="xts-notice xts-warning">AJAX call error while importing the dummy content.</div>').fadeIn();
					hideNotice();
				},
				complete: function() {
					clearFakeLoading();

					$import.removeClass('xts-form-in-action');

					$('#xts-submit').removeClass('xts-disabled');
					$('#xts-clear').addClass('xts-disabled');

					updateProgress(100, 0);

					$import.removeClass('xts-imported');

					initialClearer = setTimeout(function() {
						destroyProgressBar(200);
					}, 2000);
				}
			});
		}

		function callImportAJAX(success, complete, data) {
			$.ajax({
				url     : xtsAdminConfig.ajaxUrl,
				data    : data,
				timeout : 1000000,
				success : function(response) {
					if (success) {
						success(response);
					}
				},
				error   : function() {
					$responseArea.html('<div class="xts-notice xts-warning">AJAX call error while clearing the dummy content.</div>').fadeIn();
					hideNotice();
				},
				complete: function() {
					if (complete) {
						complete();
					}
				}
			});
		}

		function handleResponse(response) {
			var rJSON = {
				status : '',
				message: ''
			};

			try {
				rJSON = JSON.parse(response);
			}
			catch (e) {
			}

			if (!response) {
				$responseArea.html('<div class="xts-notice xts-warning">Empty AJAX response, please try again.</div>').fadeIn();

			} else if (rJSON.status === 'success' && rJSON.action && 'clear' === rJSON.action) {
				$responseArea.html('<div class="xts-notice xts-success">The dummy content was successfully cleared.</div>').fadeIn();
			} else if (rJSON.status === 'success' && rJSON.page_data) {
				$responseArea.html('<div class="xts-notice xts-success">The page was successfully imported.</div>').fadeIn();
				$('.xts-view-page-area').html('<a class="xts-inline-btn xts-blank-btn" target="_blank" href="'+ rJSON.page_data.url +'">View imported page<span class="dashicons dashicons-external"></span></a>').fadeIn();
			} else if (rJSON.status === 'success') {
				$responseArea.html('<div class="xts-notice xts-success">All data imported successfully!</div>').fadeIn();
			} else if (rJSON.status === 'fail') {
				$responseArea.html('<div class="xts-notice xts-warning">' + rJSON.message + '</div>').fadeIn();
			} else {
				$responseArea.html('<div>' + response + '</div>').fadeIn();
			}

			hideNotice();
		}

		function fakeLoading(fake1progress, fake2progress, noticeProgress) {
			destroyProgressBar(0);

			updateProgress(fake1progress, 350);

			fake2timeout = setTimeout(function() {
				updateProgress(fake2progress, 100);
			}, 10000);

			noticeTimeout = setTimeout(function() {
				updateProgress(noticeProgress, 100);
				$responseArea.html('<div class="xts-notice xts-info">Please, wait. Theme needs much time to download all attachments</div>').fadeIn();
			}, 60000);

			errorTimeout = setTimeout(function() {
				$responseArea.html('<div class="xts-notice xts-info">Still no any response from the server. Check if the dummy content already imported.</div>').fadeIn();
			}, 1000000);

			hideNotice();
		}

		function clearFakeLoading() {
			clearTimeout(fake2timeout);
			clearTimeout(noticeTimeout);
			clearTimeout(errorTimeout);
		}

		function destroyProgressBar(hide) {
			$bar.hide(hide).attr('data-progress', 0).find('div').width(0);
		}

		function clearResponseArea() {
			$responseArea.fadeOut(200, function() {
				$(this).html('');
			});
		}

		function updateProgress(to, time) {
			$bar.show();

			clearInterval(interval);

			var i = $bar.attr('data-progress');

			if (time === 0) {
				$bar.attr('data-progress', 100).find('div').width($bar.attr('data-progress') + '%');
				$bar.find('.xts-dummy-progress-bar-count').html($bar.attr('data-progress') + '%');
			} else {
				interval = setInterval(function() {
					i++;
					$bar.attr('data-progress', i).find('div').width($bar.attr('data-progress') + '%');
					$bar.find('.xts-dummy-progress-bar-count').html($bar.attr('data-progress') + '%');
					if (i >= to) {
						clearInterval(interval);
					}
				}, time);
			}
		}
	});
})(jQuery);
