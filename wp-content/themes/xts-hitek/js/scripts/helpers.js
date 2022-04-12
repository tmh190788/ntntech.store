var XTSThemeModule = {};
/* global xts_settings */

(function($) {
	XTSThemeModule.supports_html5_storage = false;

	try {
		XTSThemeModule.supports_html5_storage = ('sessionStorage' in window && window.sessionStorage !== null);
		window.sessionStorage.setItem('xts', 'test');
		window.sessionStorage.removeItem('xts');
	}
	catch (err) {
		XTSThemeModule.supports_html5_storage = false;
	}

	XTSThemeModule.isTablet = function() {
		return XTSThemeModule.$window.width() <= 1024;
	};

	XTSThemeModule.isMobile = function() {
		return XTSThemeModule.$window.width() <= 767;
	};

	XTSThemeModule.removeURLParameter = function(url, parameter) {
		var urlParts = url.split('?');

		if (urlParts.length >= 2) {

			var prefix = encodeURIComponent(parameter) + '=';
			var pars = urlParts[1].split(/[&;]/g);

			for (var i = pars.length; i-- > 0;) {
				if (pars[i].lastIndexOf(prefix, 0) !== -1) {
					pars.splice(i, 1);
				}
			}

			return urlParts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
		}

		return url;
	};

	XTSThemeModule.debounce = function(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this;
			var args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) {
					func.apply(context, args);
				}
			};
			var callNow = immediate && !timeout;

			clearTimeout(timeout);
			timeout = setTimeout(later, wait);

			if (callNow) {
				func.apply(context, args);
			}
		};
	};

	XTSThemeModule.$window = $(window);

	XTSThemeModule.$document = $(document);

	XTSThemeModule.$body = $('body');

	XTSThemeModule.windowWidth = XTSThemeModule.$window.width();

	XTSThemeModule.isDesktop = XTSThemeModule.windowWidth > 1024;

	XTSThemeModule.isTabletSize = XTSThemeModule.windowWidth <= 1024;

	XTSThemeModule.isMobileSize = XTSThemeModule.windowWidth <= 767;

	XTSThemeModule.isSuperMobile = XTSThemeModule.windowWidth <= 575;

	XTSThemeModule.xtsElementorAddAction = function(name, callback) {
		XTSThemeModule.$window.on('elementor/frontend/init', function() {
			if (!elementorFrontend.isEditMode()) {
				return;
			}

			elementorFrontend.hooks.addAction(name, callback);
		});
	};

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/section', function($wrapper) {
		$wrapper.removeClass('xts-animated');
		$wrapper.data('xts-waypoint', '');
		$wrapper.removeClass('xts-anim-ready');
		XTSThemeModule.$document.trigger('xtsElementorSectionReady');
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/global', function($wrapper) {
		if ($wrapper.attr('style') && $wrapper.attr('style').indexOf('transform:translate3d') === 0 && !$wrapper.hasClass('xts-parallax-on-scroll')) {
			$wrapper.attr('style', '');
		}

		$wrapper.removeClass('xts-animated');
		$wrapper.data('xts-waypoint', '');
		$wrapper.removeClass('xts-anim-ready');
		XTSThemeModule.$document.trigger('xtsElementorGlobalReady');
	});

	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/column', function($wrapper) {
		if ($wrapper.attr('style') && $wrapper.attr('style').indexOf('transform:translate3d') === 0 && !$wrapper.hasClass('xts-parallax-on-scroll')) {
			$wrapper.attr('style', '');
		}

		$wrapper.removeClass('xts-animated');
		$wrapper.data('xts-waypoint', '');
		$wrapper.removeClass('xts-anim-ready');
		XTSThemeModule.$document.trigger('xtsElementorColumnReady');
	});

	XTSThemeModule.$document.ready(function() {
		if (typeof ($.fn.elementorWaypoint) !== 'undefined') {
			$.fn.xtsWaypoint = $.fn.elementorWaypoint;
		} else if (typeof ($.fn.waypoint) !== 'undefined') {
			$.fn.xtsWaypoint = $.fn.waypoint;
		}
	});

	XTSThemeModule.$window.on('elementor/frontend/init', function() {
		if (!elementorFrontend.isEditMode()) {
			return;
		}

		if ('enabled' === xts_settings.elementor_no_gap) {
			elementorFrontend.hooks.addAction('frontend/element_ready/section', function($wrapper, $) {
				var cid = $wrapper.data('model-cid');

				if (typeof elementorFrontend.config.elements.data[cid] !== 'undefined') {
					var size = elementorFrontend.config.elements.data[cid].attributes.content_width.size;

					if (!size) {
						$wrapper.addClass('xts-negative-gap');
					}
				}
			});

			elementor.channels.editor.on('change:section', function(view) {
				var changed = view.elementSettingsModel.changed;

				if (typeof changed.content_width !== 'undefined') {
					var sectionId = view._parent.model.id;
					var $section = $('.elementor-element-' + sectionId);
					var size = changed.content_width.size;

					if (size) {
						$section.removeClass('xts-negative-gap');
					} else {
						$section.addClass('xts-negative-gap');
					}
				}
			});
		}
	});
})(jQuery);
