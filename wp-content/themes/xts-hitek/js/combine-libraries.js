/**
*  Ajax Autocomplete for jQuery, version 1.2.24
*  (c) 2015 Tomas Kirda
*
*  Ajax Autocomplete for jQuery is freely distributable under the terms of an MIT-style license.
*  For details, see the web site: https://github.com/devbridge/jQuery-Autocomplete
*/

/*jslint  browser: true, white: true, plusplus: true, vars: true */
/*global define, window, document, jQuery, exports, require */

// Expose plugin as an AMD module if AMD loader is present:
(function (factory) {
	'use strict';
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else if (typeof exports === 'object' && typeof require === 'function') {
		// Browserify
		factory(require('jquery'));
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {
	'use strict';

	var
		utils = (function () {
			return {
				escapeRegExChars: function (value) {
					return value.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
				},
				createNode: function (containerClass) {
					var div = document.createElement('div');
					div.className = containerClass;
					div.style.position = 'absolute';
					div.style.display = 'none';
					return div;
				}
			};
		}()),

		keys = {
			ESC: 27,
			TAB: 9,
			RETURN: 13,
			LEFT: 37,
			UP: 38,
			RIGHT: 39,
			DOWN: 40
		};

	function Autocomplete(el, options) {
		var noop = function () { },
			that = this,
			defaults = {
				ajaxSettings: {},
				autoSelectFirst: false,
				appendTo: document.body,
				serviceUrl: null,
				lookup: null,
				onSelect: null,
				width: 'auto',
				minChars: 1,
				maxHeight: 300,
				deferRequestBy: 0,
				params: {},
				formatResult: Autocomplete.formatResult,
				delimiter: null,
				zIndex: 9999,
				type: 'GET',
				noCache: false,
				onSearchStart: noop,
				onSearchComplete: noop,
				onSearchError: noop,
				preserveInput: false,
				containerClass: 'autocomplete-suggestions',
				tabDisabled: false,
				dataType: 'text',
				currentRequest: null,
				triggerSelectOnValidInput: true,
				preventBadQueries: true,
				lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
					return suggestion.value.toLowerCase().indexOf(queryLowerCase) !== -1;
				},
				paramName: 'query',
				transformResult: function (response) {
					return typeof response === 'string' ? $.parseJSON(response) : response;
				},
				showNoSuggestionNotice: false,
				noSuggestionNotice: 'No results',
				orientation: 'bottom',
				forceFixPosition: false
			};

		// Shared variables:
		that.element = el;
		that.el = $(el);
		that.suggestions = [];
		that.badQueries = [];
		that.selectedIndex = -1;
		that.currentValue = that.element.value;
		that.intervalId = 0;
		that.cachedResponse = {};
		that.onChangeInterval = null;
		that.onChange = null;
		that.isLocal = false;
		that.suggestionsContainer = null;
		that.noSuggestionsContainer = null;
		that.options = $.extend({}, defaults, options);
		that.classes = {
			selected: 'autocomplete-selected',
			suggestion: 'autocomplete-suggestion'
		};
		that.hint = null;
		that.hintValue = '';
		that.selection = null;

		// Initialize and set options:
		that.initialize();
		that.setOptions(options);
	}

	Autocomplete.utils = utils;

	$.Autocomplete = Autocomplete;

	Autocomplete.formatResult = function (suggestion, currentValue) {
		var pattern = '(' + utils.escapeRegExChars(currentValue) + ')';

		return suggestion.value
			.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/&lt;(\/?strong)&gt;/g, '<$1>');
	};

	Autocomplete.prototype = {

		killerFn: null,

		initialize: function () {
			var that = this,
				suggestionSelector = '.' + that.classes.suggestion,
				selected = that.classes.selected,
				options = that.options,
				container;

			// Remove autocomplete attribute to prevent native suggestions:
			that.element.setAttribute('autocomplete', 'off');

			that.killerFn = function (e) {
				if ($(e.target).closest('.' + that.options.containerClass).length === 0) {
					that.killSuggestions();
					that.disableKillerFn();
				}
			};

			// html() deals with many types: htmlString or Element or Array or jQuery
			that.noSuggestionsContainer = $('<div class="autocomplete-no-suggestion"></div>')
				.html(this.options.noSuggestionNotice).get(0);

			that.suggestionsContainer = Autocomplete.utils.createNode(options.containerClass);

			container = $(that.suggestionsContainer);

			container.appendTo(options.appendTo);

			// Only set width if it was provided:
			if (options.width !== 'auto') {
				container.width(options.width);
			}

			// Listen for mouse over event on suggestions list:
			container.on('mouseover.autocomplete', suggestionSelector, function () {
				that.activate($(this).data('index'));
			});

			// Deselect active element when mouse leaves suggestions container:
			container.on('mouseout.autocomplete', function () {
				that.selectedIndex = -1;
				container.children('.' + selected).removeClass(selected);
			});

			// Listen for click event on suggestions list:
			container.on('click.autocomplete', suggestionSelector, function () {
				that.select($(this).data('index'));
			});

			that.fixPositionCapture = function () {
				if (that.visible) {
					that.fixPosition();
				}
			};

			$(window).on('resize.autocomplete', that.fixPositionCapture);

			that.el.on('keydown.autocomplete', function (e) { that.onKeyPress(e); });
			that.el.on('keyup.autocomplete', function (e) { that.onKeyUp(e); });
			that.el.on('blur.autocomplete', function () { that.onBlur(); });
			that.el.on('focus.autocomplete', function () { that.onFocus(); });
			that.el.on('change.autocomplete', function (e) { that.onKeyUp(e); });
			that.el.on('input.autocomplete', function (e) { that.onKeyUp(e); });
		},

		onFocus: function () {
			var that = this;
			that.fixPosition();
			if (that.options.minChars === 0 && that.el.val().length === 0) {
				that.onValueChange();
			}
		},

		onBlur: function () {
			this.enableKillerFn();
		},

		abortAjax: function () {
			var that = this;
			if (that.currentRequest) {
				that.currentRequest.abort();
				that.currentRequest = null;
			}
		},

		setOptions: function (suppliedOptions) {
			var that = this,
				options = that.options;

			$.extend(options, suppliedOptions);

			that.isLocal = $.isArray(options.lookup);

			if (that.isLocal) {
				options.lookup = that.verifySuggestionsFormat(options.lookup);
			}

			options.orientation = that.validateOrientation(options.orientation, 'bottom');

			// Adjust height, width and z-index:
			$(that.suggestionsContainer).css({
				'max-height': options.maxHeight + 'px',
				'width': options.width + 'px',
				'z-index': options.zIndex
			});
		},


		clearCache: function () {
			this.cachedResponse = {};
			this.badQueries = [];
		},

		clear: function () {
			this.clearCache();
			this.currentValue = '';
			this.suggestions = [];
		},

		disable: function () {
			var that = this;
			that.disabled = true;
			clearInterval(that.onChangeInterval);
			that.abortAjax();
		},

		enable: function () {
			this.disabled = false;
		},

		fixPosition: function () {
			// Use only when container has already its content

			var that = this,
				$container = $(that.suggestionsContainer),
				containerParent = $container.parent().get(0);
			// Fix position automatically when appended to body.
			// In other cases force parameter must be given.
			if (containerParent !== document.body && !that.options.forceFixPosition) {
				return;
			}

			// Choose orientation
			var orientation = that.options.orientation,
				containerHeight = $container.outerHeight(),
				height = that.el.outerHeight(),
				offset = that.el.offset(),
				styles = { 'top': offset.top, 'left': offset.left };

			if (orientation === 'auto') {
				var viewPortHeight = $(window).height(),
					scrollTop = $(window).scrollTop(),
					topOverflow = -scrollTop + offset.top - containerHeight,
					bottomOverflow = scrollTop + viewPortHeight - (offset.top + height + containerHeight);

				orientation = (Math.max(topOverflow, bottomOverflow) === topOverflow) ? 'top' : 'bottom';
			}

			if (orientation === 'top') {
				styles.top += -containerHeight;
			} else {
				styles.top += height;
			}

			// If container is not positioned to body,
			// correct its position using offset parent offset
			if (containerParent !== document.body) {
				var opacity = $container.css('opacity'),
					parentOffsetDiff;

				if (!that.visible) {
					$container.css('opacity', 0).show();
				}

				parentOffsetDiff = $container.offsetParent().offset();
				styles.top -= parentOffsetDiff.top;
				styles.left -= parentOffsetDiff.left;

				if (!that.visible) {
					$container.css('opacity', opacity).hide();
				}
			}

			// -2px to account for suggestions border.
			if (that.options.width === 'auto') {
				styles.width = (that.el.outerWidth() - 2) + 'px';
			}

			$container.css(styles);
		},

		enableKillerFn: function () {
			var that = this;
			$(document).on('click.autocomplete', that.killerFn);
		},

		disableKillerFn: function () {
			var that = this;
			$(document).off('click.autocomplete', that.killerFn);
		},

		killSuggestions: function () {
			var that = this;
			that.stopKillSuggestions();
			that.intervalId = window.setInterval(function () {
				if (that.visible) {
					that.el.val(that.currentValue);
					that.hide();
				}

				that.stopKillSuggestions();
			}, 50);
		},

		stopKillSuggestions: function () {
			window.clearInterval(this.intervalId);
		},

		isCursorAtEnd: function () {
			var that = this,
				valLength = that.el.val().length,
				selectionStart = that.element.selectionStart,
				range;

			if (typeof selectionStart === 'number') {
				return selectionStart === valLength;
			}
			if (document.selection) {
				range = document.selection.createRange();
				range.moveStart('character', -valLength);
				return valLength === range.text.length;
			}
			return true;
		},

		onKeyPress: function (e) {
			var that = this;

			// If suggestions are hidden and user presses arrow down, display suggestions:
			if (!that.disabled && !that.visible && e.which === keys.DOWN && that.currentValue) {
				that.suggest();
				return;
			}

			if (that.disabled || !that.visible) {
				return;
			}

			switch (e.which) {
				case keys.ESC:
					that.el.val(that.currentValue);
					that.hide();
					break;
				case keys.RIGHT:
					if (that.hint && that.options.onHint && that.isCursorAtEnd()) {
						that.selectHint();
						break;
					}
					return;
				case keys.TAB:
					if (that.hint && that.options.onHint) {
						that.selectHint();
						return;
					}
					if (that.selectedIndex === -1) {
						that.hide();
						return;
					}
					that.select(that.selectedIndex);
					if (that.options.tabDisabled === false) {
						return;
					}
					break;
				case keys.RETURN:
					if (that.selectedIndex === -1) {
						that.hide();
						return;
					}
					that.select(that.selectedIndex);
					break;
				case keys.UP:
					that.moveUp();
					break;
				case keys.DOWN:
					that.moveDown();
					break;
				default:
					return;
			}

			// Cancel event if function did not return:
			e.stopImmediatePropagation();
			e.preventDefault();
		},

		onKeyUp: function (e) {
			var that = this;

			if (that.disabled) {
				return;
			}

			switch (e.which) {
				case keys.UP:
				case keys.DOWN:
					return;
			}

			clearInterval(that.onChangeInterval);

			if (that.currentValue !== that.el.val()) {
				that.findBestHint();
				if (that.options.deferRequestBy > 0) {
					// Defer lookup in case when value changes very quickly:
					that.onChangeInterval = setInterval(function () {
						that.onValueChange();
					}, that.options.deferRequestBy);
				} else {
					that.onValueChange();
				}
			}
		},

		onValueChange: function () {
			var that = this,
				options = that.options,
				value = that.el.val(),
				query = that.getQuery(value);

			if (that.selection && that.currentValue !== query) {
				that.selection = null;
				(options.onInvalidateSelection || $.noop).call(that.element);
			}

			clearInterval(that.onChangeInterval);
			that.currentValue = value;
			that.selectedIndex = -1;

			// Check existing suggestion for the match before proceeding:
			if (options.triggerSelectOnValidInput && that.isExactMatch(query)) {
				that.select(0);
				return;
			}

			if (query.length < options.minChars) {
				that.hide();
			} else {
				that.getSuggestions(query);
			}
		},

		isExactMatch: function (query) {
			var suggestions = this.suggestions;

			return (suggestions.length === 1 && suggestions[0].value.toLowerCase() === query.toLowerCase());
		},

		getQuery: function (value) {
			var delimiter = this.options.delimiter,
				parts;

			if (!delimiter) {
				return value;
			}
			parts = value.split(delimiter);
			return $.trim(parts[parts.length - 1]);
		},

		getSuggestionsLocal: function (query) {
			var that = this,
				options = that.options,
				queryLowerCase = query.toLowerCase(),
				filter = options.lookupFilter,
				limit = parseInt(options.lookupLimit, 10),
				data;

			data = {
				suggestions: $.grep(options.lookup, function (suggestion) {
					return filter(suggestion, query, queryLowerCase);
				})
			};

			if (limit && data.suggestions.length > limit) {
				data.suggestions = data.suggestions.slice(0, limit);
			}

			return data;
		},

		getSuggestions: function (q) {
			var response,
				that = this,
				options = that.options,
				serviceUrl = options.serviceUrl,
				params,
				cacheKey,
				ajaxSettings;

			options.params[options.paramName] = q;
			params = options.ignoreParams ? null : options.params;

			if (options.onSearchStart.call(that.element, options.params) === false) {
				return;
			}

			if ($.isFunction(options.lookup)) {
				options.lookup(q, function (data) {
					that.suggestions = data.suggestions;
					that.suggest();
					options.onSearchComplete.call(that.element, q, data.suggestions);
				});
				return;
			}

			if (that.isLocal) {
				response = that.getSuggestionsLocal(q);
			} else {
				if ($.isFunction(serviceUrl)) {
					serviceUrl = serviceUrl.call(that.element, q);
				}
				cacheKey = serviceUrl + '?' + $.param(params || {});
				response = that.cachedResponse[cacheKey];
			}

			if (response && $.isArray(response.suggestions)) {
				that.suggestions = response.suggestions;
				that.suggest();
				options.onSearchComplete.call(that.element, q, response.suggestions);
			} else if (!that.isBadQuery(q)) {
				that.abortAjax();

				ajaxSettings = {
					url: serviceUrl,
					data: params,
					type: options.type,
					dataType: options.dataType
				};

				$.extend(ajaxSettings, options.ajaxSettings);

				that.currentRequest = $.ajax(ajaxSettings).done(function (data) {
					var result;
					that.currentRequest = null;
					result = options.transformResult(data, q);
					that.processResponse(result, q, cacheKey);
					options.onSearchComplete.call(that.element, q, result.suggestions);
				}).fail(function (jqXHR, textStatus, errorThrown) {
					options.onSearchError.call(that.element, q, jqXHR, textStatus, errorThrown);
				});
			} else {
				options.onSearchComplete.call(that.element, q, []);
			}
		},

		isBadQuery: function (q) {
			if (!this.options.preventBadQueries) {
				return false;
			}

			var badQueries = this.badQueries,
				i = badQueries.length;

			while (i--) {
				if (q.indexOf(badQueries[i]) === 0) {
					return true;
				}
			}

			return false;
		},

		hide: function () {
			var that = this,
				container = $(that.suggestionsContainer);

			if ($.isFunction(that.options.onHide) && that.visible) {
				that.options.onHide.call(that.element, container);
			}

			that.visible = false;
			that.selectedIndex = -1;
			clearInterval(that.onChangeInterval);
			$(that.suggestionsContainer).hide();
			that.signalHint(null);
		},

		suggest: function () {
			if (this.suggestions.length === 0) {
				if (this.options.showNoSuggestionNotice) {
					this.noSuggestions();
				} else {
					this.hide();
				}
				return;
			}

			var that = this,
				options = that.options,
				groupBy = options.groupBy,
				formatResult = options.formatResult,
				value = that.getQuery(that.currentValue),
				className = that.classes.suggestion,
				classSelected = that.classes.selected,
				container = $(that.suggestionsContainer),
				noSuggestionsContainer = $(that.noSuggestionsContainer),
				beforeRender = options.beforeRender,
				html = '',
				category,
				formatGroup = function (suggestion, index) {
					var currentCategory = suggestion.data[groupBy];

					if (category === currentCategory) {
						return '';
					}

					category = currentCategory;

					return '<div class="autocomplete-group"><strong>' + category + '</strong></div>';
				};

			if (options.triggerSelectOnValidInput && that.isExactMatch(value)) {
				that.select(0);
				return;
			}

			// Build suggestions inner HTML:
			$.each(that.suggestions, function (i, suggestion) {
				if (groupBy) {
					html += formatGroup(suggestion, value, i);
				}

				html += '<div class="' + className + '" data-index="' + i + '">' + formatResult(suggestion, value) + '</div>';
			});

			this.adjustContainerWidth();

			noSuggestionsContainer.detach();
			container.html(html);

			if ($.isFunction(beforeRender)) {
				beforeRender.call(that.element, container);
			}

			that.fixPosition();
			container.show();

			// Select first value by default:
			if (options.autoSelectFirst) {
				that.selectedIndex = 0;
				container.scrollTop(0);
				container.children('.' + className).first().addClass(classSelected);
			}

			that.visible = true;
			that.findBestHint();
		},

		noSuggestions: function () {
			var that = this,
				container = $(that.suggestionsContainer),
				noSuggestionsContainer = $(that.noSuggestionsContainer);

			this.adjustContainerWidth();

			// Some explicit steps. Be careful here as it easy to get
			// noSuggestionsContainer removed from DOM if not detached properly.
			noSuggestionsContainer.detach();
			container.empty(); // clean suggestions if any
			container.append(noSuggestionsContainer);

			that.fixPosition();

			container.show();
			that.visible = true;
		},

		adjustContainerWidth: function () {
			var that = this,
				options = that.options,
				width,
				container = $(that.suggestionsContainer);

			// If width is auto, adjust width before displaying suggestions,
			// because if instance was created before input had width, it will be zero.
			// Also it adjusts if input width has changed.
			// -2px to account for suggestions border.
			if (options.width === 'auto') {
				width = that.el.outerWidth() - 2;
				container.width(width > 0 ? width : 300);
			}
		},

		findBestHint: function () {
			var that = this,
				value = that.el.val().toLowerCase(),
				bestMatch = null;

			if (!value) {
				return;
			}

			$.each(that.suggestions, function (i, suggestion) {
				var foundMatch = suggestion.value.toLowerCase().indexOf(value) === 0;
				if (foundMatch) {
					bestMatch = suggestion;
				}
				return !foundMatch;
			});

			that.signalHint(bestMatch);
		},

		signalHint: function (suggestion) {
			var hintValue = '',
				that = this;
			if (suggestion) {
				hintValue = that.currentValue + suggestion.value.substr(that.currentValue.length);
			}
			if (that.hintValue !== hintValue) {
				that.hintValue = hintValue;
				that.hint = suggestion;
				(this.options.onHint || $.noop)(hintValue);
			}
		},

		verifySuggestionsFormat: function (suggestions) {
			// If suggestions is string array, convert them to supported format:
			if (suggestions.length && typeof suggestions[0] === 'string') {
				return $.map(suggestions, function (value) {
					return { value: value, data: null };
				});
			}

			return suggestions;
		},

		validateOrientation: function (orientation, fallback) {
			orientation = $.trim(orientation || '').toLowerCase();

			if ($.inArray(orientation, ['auto', 'bottom', 'top']) === -1) {
				orientation = fallback;
			}

			return orientation;
		},

		processResponse: function (result, originalQuery, cacheKey) {
			var that = this,
				options = that.options;

			result.suggestions = that.verifySuggestionsFormat(result.suggestions);

			// Cache results if cache is not disabled:
			if (!options.noCache) {
				that.cachedResponse[cacheKey] = result;
				if (options.preventBadQueries && result.suggestions.length === 0) {
					that.badQueries.push(originalQuery);
				}
			}

			// Return if originalQuery is not matching current query:
			if (originalQuery !== that.getQuery(that.currentValue)) {
				return;
			}

			that.suggestions = result.suggestions;
			that.suggest();
		},

		activate: function (index) {
			var that = this,
				activeItem,
				selected = that.classes.selected,
				container = $(that.suggestionsContainer),
				children = container.find('.' + that.classes.suggestion);

			container.find('.' + selected).removeClass(selected);

			that.selectedIndex = index;

			if (that.selectedIndex !== -1 && children.length > that.selectedIndex) {
				activeItem = children.get(that.selectedIndex);
				$(activeItem).addClass(selected);
				return activeItem;
			}

			return null;
		},

		selectHint: function () {
			var that = this,
				i = $.inArray(that.hint, that.suggestions);

			that.select(i);
		},

		select: function (i) {
			var that = this;
			that.hide();
			that.onSelect(i);
		},

		moveUp: function () {
			var that = this;

			if (that.selectedIndex === -1) {
				return;
			}

			if (that.selectedIndex === 0) {
				$(that.suggestionsContainer).children().first().removeClass(that.classes.selected);
				that.selectedIndex = -1;
				that.el.val(that.currentValue);
				that.findBestHint();
				return;
			}

			that.adjustScroll(that.selectedIndex - 1);
		},

		moveDown: function () {
			var that = this;

			if (that.selectedIndex === (that.suggestions.length - 1)) {
				return;
			}

			that.adjustScroll(that.selectedIndex + 1);
		},

		adjustScroll: function (index) {
			var that = this,
				activeItem = that.activate(index);

			if (!activeItem) {
				return;
			}

			var offsetTop,
				upperBound,
				lowerBound,
				heightDelta = $(activeItem).outerHeight();

			offsetTop = activeItem.offsetTop;
			upperBound = $(that.suggestionsContainer).scrollTop();
			lowerBound = upperBound + that.options.maxHeight - heightDelta;

			if (offsetTop < upperBound) {
				$(that.suggestionsContainer).scrollTop(offsetTop);
			} else if (offsetTop > lowerBound) {
				$(that.suggestionsContainer).scrollTop(offsetTop - that.options.maxHeight + heightDelta);
			}

			if (!that.options.preserveInput) {
				that.el.val(that.getValue(that.suggestions[index].value));
			}
			that.signalHint(null);
		},

		onSelect: function (index) {
			var that = this,
				onSelectCallback = that.options.onSelect,
				suggestion = that.suggestions[index];

			that.currentValue = that.getValue(suggestion.value);

			if (that.currentValue !== that.el.val() && !that.options.preserveInput) {
				that.el.val(that.currentValue);
			}

			that.signalHint(null);
			that.suggestions = [];
			that.selection = suggestion;

			if ($.isFunction(onSelectCallback)) {
				onSelectCallback.call(that.element, suggestion);
			}
		},

		getValue: function (value) {
			var that = this,
				delimiter = that.options.delimiter,
				currentValue,
				parts;

			if (!delimiter) {
				return value;
			}

			currentValue = that.currentValue;
			parts = currentValue.split(delimiter);

			if (parts.length === 1) {
				return value;
			}

			return currentValue.substr(0, currentValue.length - parts[parts.length - 1].length) + value;
		},

		dispose: function () {
			var that = this;
			that.el.off('.autocomplete').removeData('autocomplete');
			that.disableKillerFn();
			$(window).off('resize.autocomplete', that.fixPositionCapture);
			$(that.suggestionsContainer).remove();
		}
	};

	// Create chainable jQuery plugin:
	$.fn.devbridgeAutocomplete = function (options, args) {
		var dataKey = 'autocomplete';
		// If function invoked without argument return
		// instance of the first matched element:
		if (arguments.length === 0) {
			return this.first().data(dataKey);
		}

		return this.each(function () {
			var inputElement = $(this),
				instance = inputElement.data(dataKey);

			if (typeof options === 'string') {
				if (instance && typeof instance[options] === 'function') {
					instance[options](args);
				}
			} else {
				// If instance already exists, destroy it:
				if (instance && instance.dispose) {
					instance.dispose();
				}
				instance = new Autocomplete(this, options);
				inputElement.data(dataKey, instance);
			}
		});
	};
}));

/**
 * Swiper 5.3.6
 * Most modern mobile touch slider and framework with hardware accelerated transitions
 * http://swiperjs.com
 *
 * Copyright 2014-2020 Vladimir Kharlampidi
 *
 * Released under the MIT License
 *
 * Released on: February 29, 2020
 */

!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e=e||self).Swiper=t()}(this,(function(){"use strict";var e="undefined"==typeof document?{body:{},addEventListener:function(){},removeEventListener:function(){},activeElement:{blur:function(){},nodeName:""},querySelector:function(){return null},querySelectorAll:function(){return[]},getElementById:function(){return null},createEvent:function(){return{initEvent:function(){}}},createElement:function(){return{children:[],childNodes:[],style:{},setAttribute:function(){},getElementsByTagName:function(){return[]}}},location:{hash:""}}:document,t="undefined"==typeof window?{document:e,navigator:{userAgent:""},location:{},history:{},CustomEvent:function(){return this},addEventListener:function(){},removeEventListener:function(){},getComputedStyle:function(){return{getPropertyValue:function(){return""}}},Image:function(){},Date:function(){},screen:{},setTimeout:function(){},clearTimeout:function(){}}:window,i=function(e){for(var t=0;t<e.length;t+=1)this[t]=e[t];return this.length=e.length,this};function s(s,a){var r=[],n=0;if(s&&!a&&s instanceof i)return s;if(s)if("string"==typeof s){var o,l,d=s.trim();if(d.indexOf("<")>=0&&d.indexOf(">")>=0){var h="div";for(0===d.indexOf("<li")&&(h="ul"),0===d.indexOf("<tr")&&(h="tbody"),0!==d.indexOf("<td")&&0!==d.indexOf("<th")||(h="tr"),0===d.indexOf("<tbody")&&(h="table"),0===d.indexOf("<option")&&(h="select"),(l=e.createElement(h)).innerHTML=d,n=0;n<l.childNodes.length;n+=1)r.push(l.childNodes[n])}else for(o=a||"#"!==s[0]||s.match(/[ .<>:~]/)?(a||e).querySelectorAll(s.trim()):[e.getElementById(s.trim().split("#")[1])],n=0;n<o.length;n+=1)o[n]&&r.push(o[n])}else if(s.nodeType||s===t||s===e)r.push(s);else if(s.length>0&&s[0].nodeType)for(n=0;n<s.length;n+=1)r.push(s[n]);return new i(r)}function a(e){for(var t=[],i=0;i<e.length;i+=1)-1===t.indexOf(e[i])&&t.push(e[i]);return t}s.fn=i.prototype,s.Class=i,s.Dom7=i;var r={addClass:function(e){if(void 0===e)return this;for(var t=e.split(" "),i=0;i<t.length;i+=1)for(var s=0;s<this.length;s+=1)void 0!==this[s]&&void 0!==this[s].classList&&this[s].classList.add(t[i]);return this},removeClass:function(e){for(var t=e.split(" "),i=0;i<t.length;i+=1)for(var s=0;s<this.length;s+=1)void 0!==this[s]&&void 0!==this[s].classList&&this[s].classList.remove(t[i]);return this},hasClass:function(e){return!!this[0]&&this[0].classList.contains(e)},toggleClass:function(e){for(var t=e.split(" "),i=0;i<t.length;i+=1)for(var s=0;s<this.length;s+=1)void 0!==this[s]&&void 0!==this[s].classList&&this[s].classList.toggle(t[i]);return this},attr:function(e,t){var i=arguments;if(1===arguments.length&&"string"==typeof e)return this[0]?this[0].getAttribute(e):void 0;for(var s=0;s<this.length;s+=1)if(2===i.length)this[s].setAttribute(e,t);else for(var a in e)this[s][a]=e[a],this[s].setAttribute(a,e[a]);return this},removeAttr:function(e){for(var t=0;t<this.length;t+=1)this[t].removeAttribute(e);return this},data:function(e,t){var i;if(void 0!==t){for(var s=0;s<this.length;s+=1)(i=this[s]).dom7ElementDataStorage||(i.dom7ElementDataStorage={}),i.dom7ElementDataStorage[e]=t;return this}if(i=this[0]){if(i.dom7ElementDataStorage&&e in i.dom7ElementDataStorage)return i.dom7ElementDataStorage[e];var a=i.getAttribute("data-"+e);return a||void 0}},transform:function(e){for(var t=0;t<this.length;t+=1){var i=this[t].style;i.webkitTransform=e,i.transform=e}return this},transition:function(e){"string"!=typeof e&&(e+="ms");for(var t=0;t<this.length;t+=1){var i=this[t].style;i.webkitTransitionDuration=e,i.transitionDuration=e}return this},on:function(){for(var e,t=[],i=arguments.length;i--;)t[i]=arguments[i];var a=t[0],r=t[1],n=t[2],o=t[3];function l(e){var t=e.target;if(t){var i=e.target.dom7EventData||[];if(i.indexOf(e)<0&&i.unshift(e),s(t).is(r))n.apply(t,i);else for(var a=s(t).parents(),o=0;o<a.length;o+=1)s(a[o]).is(r)&&n.apply(a[o],i)}}function d(e){var t=e&&e.target&&e.target.dom7EventData||[];t.indexOf(e)<0&&t.unshift(e),n.apply(this,t)}"function"==typeof t[1]&&(a=(e=t)[0],n=e[1],o=e[2],r=void 0),o||(o=!1);for(var h,p=a.split(" "),c=0;c<this.length;c+=1){var u=this[c];if(r)for(h=0;h<p.length;h+=1){var v=p[h];u.dom7LiveListeners||(u.dom7LiveListeners={}),u.dom7LiveListeners[v]||(u.dom7LiveListeners[v]=[]),u.dom7LiveListeners[v].push({listener:n,proxyListener:l}),u.addEventListener(v,l,o)}else for(h=0;h<p.length;h+=1){var f=p[h];u.dom7Listeners||(u.dom7Listeners={}),u.dom7Listeners[f]||(u.dom7Listeners[f]=[]),u.dom7Listeners[f].push({listener:n,proxyListener:d}),u.addEventListener(f,d,o)}}return this},off:function(){for(var e,t=[],i=arguments.length;i--;)t[i]=arguments[i];var s=t[0],a=t[1],r=t[2],n=t[3];"function"==typeof t[1]&&(s=(e=t)[0],r=e[1],n=e[2],a=void 0),n||(n=!1);for(var o=s.split(" "),l=0;l<o.length;l+=1)for(var d=o[l],h=0;h<this.length;h+=1){var p=this[h],c=void 0;if(!a&&p.dom7Listeners?c=p.dom7Listeners[d]:a&&p.dom7LiveListeners&&(c=p.dom7LiveListeners[d]),c&&c.length)for(var u=c.length-1;u>=0;u-=1){var v=c[u];r&&v.listener===r?(p.removeEventListener(d,v.proxyListener,n),c.splice(u,1)):r&&v.listener&&v.listener.dom7proxy&&v.listener.dom7proxy===r?(p.removeEventListener(d,v.proxyListener,n),c.splice(u,1)):r||(p.removeEventListener(d,v.proxyListener,n),c.splice(u,1))}}return this},trigger:function(){for(var i=[],s=arguments.length;s--;)i[s]=arguments[s];for(var a=i[0].split(" "),r=i[1],n=0;n<a.length;n+=1)for(var o=a[n],l=0;l<this.length;l+=1){var d=this[l],h=void 0;try{h=new t.CustomEvent(o,{detail:r,bubbles:!0,cancelable:!0})}catch(t){(h=e.createEvent("Event")).initEvent(o,!0,!0),h.detail=r}d.dom7EventData=i.filter((function(e,t){return t>0})),d.dispatchEvent(h),d.dom7EventData=[],delete d.dom7EventData}return this},transitionEnd:function(e){var t,i=["webkitTransitionEnd","transitionend"],s=this;function a(r){if(r.target===this)for(e.call(this,r),t=0;t<i.length;t+=1)s.off(i[t],a)}if(e)for(t=0;t<i.length;t+=1)s.on(i[t],a);return this},outerWidth:function(e){if(this.length>0){if(e){var t=this.styles();return this[0].offsetWidth+parseFloat(t.getPropertyValue("margin-right"))+parseFloat(t.getPropertyValue("margin-left"))}return this[0].offsetWidth}return null},outerHeight:function(e){if(this.length>0){if(e){var t=this.styles();return this[0].offsetHeight+parseFloat(t.getPropertyValue("margin-top"))+parseFloat(t.getPropertyValue("margin-bottom"))}return this[0].offsetHeight}return null},offset:function(){if(this.length>0){var i=this[0],s=i.getBoundingClientRect(),a=e.body,r=i.clientTop||a.clientTop||0,n=i.clientLeft||a.clientLeft||0,o=i===t?t.scrollY:i.scrollTop,l=i===t?t.scrollX:i.scrollLeft;return{top:s.top+o-r,left:s.left+l-n}}return null},css:function(e,i){var s;if(1===arguments.length){if("string"!=typeof e){for(s=0;s<this.length;s+=1)for(var a in e)this[s].style[a]=e[a];return this}if(this[0])return t.getComputedStyle(this[0],null).getPropertyValue(e)}if(2===arguments.length&&"string"==typeof e){for(s=0;s<this.length;s+=1)this[s].style[e]=i;return this}return this},each:function(e){if(!e)return this;for(var t=0;t<this.length;t+=1)if(!1===e.call(this[t],t,this[t]))return this;return this},html:function(e){if(void 0===e)return this[0]?this[0].innerHTML:void 0;for(var t=0;t<this.length;t+=1)this[t].innerHTML=e;return this},text:function(e){if(void 0===e)return this[0]?this[0].textContent.trim():null;for(var t=0;t<this.length;t+=1)this[t].textContent=e;return this},is:function(a){var r,n,o=this[0];if(!o||void 0===a)return!1;if("string"==typeof a){if(o.matches)return o.matches(a);if(o.webkitMatchesSelector)return o.webkitMatchesSelector(a);if(o.msMatchesSelector)return o.msMatchesSelector(a);for(r=s(a),n=0;n<r.length;n+=1)if(r[n]===o)return!0;return!1}if(a===e)return o===e;if(a===t)return o===t;if(a.nodeType||a instanceof i){for(r=a.nodeType?[a]:a,n=0;n<r.length;n+=1)if(r[n]===o)return!0;return!1}return!1},index:function(){var e,t=this[0];if(t){for(e=0;null!==(t=t.previousSibling);)1===t.nodeType&&(e+=1);return e}},eq:function(e){if(void 0===e)return this;var t,s=this.length;return new i(e>s-1?[]:e<0?(t=s+e)<0?[]:[this[t]]:[this[e]])},append:function(){for(var t,s=[],a=arguments.length;a--;)s[a]=arguments[a];for(var r=0;r<s.length;r+=1){t=s[r];for(var n=0;n<this.length;n+=1)if("string"==typeof t){var o=e.createElement("div");for(o.innerHTML=t;o.firstChild;)this[n].appendChild(o.firstChild)}else if(t instanceof i)for(var l=0;l<t.length;l+=1)this[n].appendChild(t[l]);else this[n].appendChild(t)}return this},prepend:function(t){var s,a;for(s=0;s<this.length;s+=1)if("string"==typeof t){var r=e.createElement("div");for(r.innerHTML=t,a=r.childNodes.length-1;a>=0;a-=1)this[s].insertBefore(r.childNodes[a],this[s].childNodes[0])}else if(t instanceof i)for(a=0;a<t.length;a+=1)this[s].insertBefore(t[a],this[s].childNodes[0]);else this[s].insertBefore(t,this[s].childNodes[0]);return this},next:function(e){return this.length>0?e?this[0].nextElementSibling&&s(this[0].nextElementSibling).is(e)?new i([this[0].nextElementSibling]):new i([]):this[0].nextElementSibling?new i([this[0].nextElementSibling]):new i([]):new i([])},nextAll:function(e){var t=[],a=this[0];if(!a)return new i([]);for(;a.nextElementSibling;){var r=a.nextElementSibling;e?s(r).is(e)&&t.push(r):t.push(r),a=r}return new i(t)},prev:function(e){if(this.length>0){var t=this[0];return e?t.previousElementSibling&&s(t.previousElementSibling).is(e)?new i([t.previousElementSibling]):new i([]):t.previousElementSibling?new i([t.previousElementSibling]):new i([])}return new i([])},prevAll:function(e){var t=[],a=this[0];if(!a)return new i([]);for(;a.previousElementSibling;){var r=a.previousElementSibling;e?s(r).is(e)&&t.push(r):t.push(r),a=r}return new i(t)},parent:function(e){for(var t=[],i=0;i<this.length;i+=1)null!==this[i].parentNode&&(e?s(this[i].parentNode).is(e)&&t.push(this[i].parentNode):t.push(this[i].parentNode));return s(a(t))},parents:function(e){for(var t=[],i=0;i<this.length;i+=1)for(var r=this[i].parentNode;r;)e?s(r).is(e)&&t.push(r):t.push(r),r=r.parentNode;return s(a(t))},closest:function(e){var t=this;return void 0===e?new i([]):(t.is(e)||(t=t.parents(e).eq(0)),t)},find:function(e){for(var t=[],s=0;s<this.length;s+=1)for(var a=this[s].querySelectorAll(e),r=0;r<a.length;r+=1)t.push(a[r]);return new i(t)},children:function(e){for(var t=[],r=0;r<this.length;r+=1)for(var n=this[r].childNodes,o=0;o<n.length;o+=1)e?1===n[o].nodeType&&s(n[o]).is(e)&&t.push(n[o]):1===n[o].nodeType&&t.push(n[o]);return new i(a(t))},filter:function(e){for(var t=[],s=0;s<this.length;s+=1)e.call(this[s],s,this[s])&&t.push(this[s]);return new i(t)},remove:function(){for(var e=0;e<this.length;e+=1)this[e].parentNode&&this[e].parentNode.removeChild(this[e]);return this},add:function(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var i,a;for(i=0;i<e.length;i+=1){var r=s(e[i]);for(a=0;a<r.length;a+=1)this[this.length]=r[a],this.length+=1}return this},styles:function(){return this[0]?t.getComputedStyle(this[0],null):{}}};Object.keys(r).forEach((function(e){s.fn[e]=s.fn[e]||r[e]}));var n={deleteProps:function(e){var t=e;Object.keys(t).forEach((function(e){try{t[e]=null}catch(e){}try{delete t[e]}catch(e){}}))},nextTick:function(e,t){return void 0===t&&(t=0),setTimeout(e,t)},now:function(){return Date.now()},getTranslate:function(e,i){var s,a,r;void 0===i&&(i="x");var n=t.getComputedStyle(e,null);return t.WebKitCSSMatrix?((a=n.transform||n.webkitTransform).split(",").length>6&&(a=a.split(", ").map((function(e){return e.replace(",",".")})).join(", ")),r=new t.WebKitCSSMatrix("none"===a?"":a)):s=(r=n.MozTransform||n.OTransform||n.MsTransform||n.msTransform||n.transform||n.getPropertyValue("transform").replace("translate(","matrix(1, 0, 0, 1,")).toString().split(","),"x"===i&&(a=t.WebKitCSSMatrix?r.m41:16===s.length?parseFloat(s[12]):parseFloat(s[4])),"y"===i&&(a=t.WebKitCSSMatrix?r.m42:16===s.length?parseFloat(s[13]):parseFloat(s[5])),a||0},parseUrlQuery:function(e){var i,s,a,r,n={},o=e||t.location.href;if("string"==typeof o&&o.length)for(r=(s=(o=o.indexOf("?")>-1?o.replace(/\S*\?/,""):"").split("&").filter((function(e){return""!==e}))).length,i=0;i<r;i+=1)a=s[i].replace(/#\S+/g,"").split("="),n[decodeURIComponent(a[0])]=void 0===a[1]?void 0:decodeURIComponent(a[1])||"";return n},isObject:function(e){return"object"==typeof e&&null!==e&&e.constructor&&e.constructor===Object},extend:function(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];for(var i=Object(e[0]),s=1;s<e.length;s+=1){var a=e[s];if(null!=a)for(var r=Object.keys(Object(a)),o=0,l=r.length;o<l;o+=1){var d=r[o],h=Object.getOwnPropertyDescriptor(a,d);void 0!==h&&h.enumerable&&(n.isObject(i[d])&&n.isObject(a[d])?n.extend(i[d],a[d]):!n.isObject(i[d])&&n.isObject(a[d])?(i[d]={},n.extend(i[d],a[d])):i[d]=a[d])}}return i}},o={touch:t.Modernizr&&!0===t.Modernizr.touch||!!(t.navigator.maxTouchPoints>0||"ontouchstart"in t||t.DocumentTouch&&e instanceof t.DocumentTouch),pointerEvents:!!t.PointerEvent&&"maxTouchPoints"in t.navigator&&t.navigator.maxTouchPoints>0,observer:"MutationObserver"in t||"WebkitMutationObserver"in t,passiveListener:function(){var e=!1;try{var i=Object.defineProperty({},"passive",{get:function(){e=!0}});t.addEventListener("testPassiveListener",null,i)}catch(e){}return e}(),gestures:"ongesturestart"in t},l=function(e){void 0===e&&(e={});var t=this;t.params=e,t.eventsListeners={},t.params&&t.params.on&&Object.keys(t.params.on).forEach((function(e){t.on(e,t.params.on[e])}))},d={components:{configurable:!0}};l.prototype.on=function(e,t,i){var s=this;if("function"!=typeof t)return s;var a=i?"unshift":"push";return e.split(" ").forEach((function(e){s.eventsListeners[e]||(s.eventsListeners[e]=[]),s.eventsListeners[e][a](t)})),s},l.prototype.once=function(e,t,i){var s=this;if("function"!=typeof t)return s;function a(){for(var i=[],r=arguments.length;r--;)i[r]=arguments[r];s.off(e,a),a.f7proxy&&delete a.f7proxy,t.apply(s,i)}return a.f7proxy=t,s.on(e,a,i)},l.prototype.off=function(e,t){var i=this;return i.eventsListeners?(e.split(" ").forEach((function(e){void 0===t?i.eventsListeners[e]=[]:i.eventsListeners[e]&&i.eventsListeners[e].length&&i.eventsListeners[e].forEach((function(s,a){(s===t||s.f7proxy&&s.f7proxy===t)&&i.eventsListeners[e].splice(a,1)}))})),i):i},l.prototype.emit=function(){for(var e=[],t=arguments.length;t--;)e[t]=arguments[t];var i,s,a,r=this;if(!r.eventsListeners)return r;"string"==typeof e[0]||Array.isArray(e[0])?(i=e[0],s=e.slice(1,e.length),a=r):(i=e[0].events,s=e[0].data,a=e[0].context||r);var n=Array.isArray(i)?i:i.split(" ");return n.forEach((function(e){if(r.eventsListeners&&r.eventsListeners[e]){var t=[];r.eventsListeners[e].forEach((function(e){t.push(e)})),t.forEach((function(e){e.apply(a,s)}))}})),r},l.prototype.useModulesParams=function(e){var t=this;t.modules&&Object.keys(t.modules).forEach((function(i){var s=t.modules[i];s.params&&n.extend(e,s.params)}))},l.prototype.useModules=function(e){void 0===e&&(e={});var t=this;t.modules&&Object.keys(t.modules).forEach((function(i){var s=t.modules[i],a=e[i]||{};s.instance&&Object.keys(s.instance).forEach((function(e){var i=s.instance[e];t[e]="function"==typeof i?i.bind(t):i})),s.on&&t.on&&Object.keys(s.on).forEach((function(e){t.on(e,s.on[e])})),s.create&&s.create.bind(t)(a)}))},d.components.set=function(e){this.use&&this.use(e)},l.installModule=function(e){for(var t=[],i=arguments.length-1;i-- >0;)t[i]=arguments[i+1];var s=this;s.prototype.modules||(s.prototype.modules={});var a=e.name||Object.keys(s.prototype.modules).length+"_"+n.now();return s.prototype.modules[a]=e,e.proto&&Object.keys(e.proto).forEach((function(t){s.prototype[t]=e.proto[t]})),e.static&&Object.keys(e.static).forEach((function(t){s[t]=e.static[t]})),e.install&&e.install.apply(s,t),s},l.use=function(e){for(var t=[],i=arguments.length-1;i-- >0;)t[i]=arguments[i+1];var s=this;return Array.isArray(e)?(e.forEach((function(e){return s.installModule(e)})),s):s.installModule.apply(s,[e].concat(t))},Object.defineProperties(l,d);var h={updateSize:function(){var e,t,i=this.$el;e=void 0!==this.params.width?this.params.width:i[0].clientWidth,t=void 0!==this.params.height?this.params.height:i[0].clientHeight,0===e&&this.isHorizontal()||0===t&&this.isVertical()||(e=e-parseInt(i.css("padding-left"),10)-parseInt(i.css("padding-right"),10),t=t-parseInt(i.css("padding-top"),10)-parseInt(i.css("padding-bottom"),10),n.extend(this,{width:e,height:t,size:this.isHorizontal()?e:t}))},updateSlides:function(){var e=this.params,i=this.$wrapperEl,s=this.size,a=this.rtlTranslate,r=this.wrongRTL,o=this.virtual&&e.virtual.enabled,l=o?this.virtual.slides.length:this.slides.length,d=i.children("."+this.params.slideClass),h=o?this.virtual.slides.length:d.length,p=[],c=[],u=[];function v(t){return!e.cssMode||t!==d.length-1}var f=e.slidesOffsetBefore;"function"==typeof f&&(f=e.slidesOffsetBefore.call(this));var m=e.slidesOffsetAfter;"function"==typeof m&&(m=e.slidesOffsetAfter.call(this));var g=this.snapGrid.length,b=this.snapGrid.length,w=e.spaceBetween,y=-f,x=0,T=0;if(void 0!==s){var E,S;"string"==typeof w&&w.indexOf("%")>=0&&(w=parseFloat(w.replace("%",""))/100*s),this.virtualSize=-w,a?d.css({marginLeft:"",marginTop:""}):d.css({marginRight:"",marginBottom:""}),e.slidesPerColumn>1&&(E=Math.floor(h/e.slidesPerColumn)===h/this.params.slidesPerColumn?h:Math.ceil(h/e.slidesPerColumn)*e.slidesPerColumn,"auto"!==e.slidesPerView&&"row"===e.slidesPerColumnFill&&(E=Math.max(E,e.slidesPerView*e.slidesPerColumn)));for(var C,M=e.slidesPerColumn,P=E/M,z=Math.floor(h/e.slidesPerColumn),k=0;k<h;k+=1){S=0;var $=d.eq(k);if(e.slidesPerColumn>1){var L=void 0,I=void 0,D=void 0;if("row"===e.slidesPerColumnFill&&e.slidesPerGroup>1){var O=Math.floor(k/(e.slidesPerGroup*e.slidesPerColumn)),A=k-e.slidesPerColumn*e.slidesPerGroup*O,G=0===O?e.slidesPerGroup:Math.min(Math.ceil((h-O*M*e.slidesPerGroup)/M),e.slidesPerGroup);L=(I=A-(D=Math.floor(A/G))*G+O*e.slidesPerGroup)+D*E/M,$.css({"-webkit-box-ordinal-group":L,"-moz-box-ordinal-group":L,"-ms-flex-order":L,"-webkit-order":L,order:L})}else"column"===e.slidesPerColumnFill?(D=k-(I=Math.floor(k/M))*M,(I>z||I===z&&D===M-1)&&(D+=1)>=M&&(D=0,I+=1)):I=k-(D=Math.floor(k/P))*P;$.css("margin-"+(this.isHorizontal()?"top":"left"),0!==D&&e.spaceBetween&&e.spaceBetween+"px")}if("none"!==$.css("display")){if("auto"===e.slidesPerView){var H=t.getComputedStyle($[0],null),B=$[0].style.transform,N=$[0].style.webkitTransform;if(B&&($[0].style.transform="none"),N&&($[0].style.webkitTransform="none"),e.roundLengths)S=this.isHorizontal()?$.outerWidth(!0):$.outerHeight(!0);else if(this.isHorizontal()){var X=parseFloat(H.getPropertyValue("width")),V=parseFloat(H.getPropertyValue("padding-left")),Y=parseFloat(H.getPropertyValue("padding-right")),F=parseFloat(H.getPropertyValue("margin-left")),W=parseFloat(H.getPropertyValue("margin-right")),R=H.getPropertyValue("box-sizing");S=R&&"border-box"===R?X+F+W:X+V+Y+F+W}else{var q=parseFloat(H.getPropertyValue("height")),j=parseFloat(H.getPropertyValue("padding-top")),K=parseFloat(H.getPropertyValue("padding-bottom")),U=parseFloat(H.getPropertyValue("margin-top")),_=parseFloat(H.getPropertyValue("margin-bottom")),Z=H.getPropertyValue("box-sizing");S=Z&&"border-box"===Z?q+U+_:q+j+K+U+_}B&&($[0].style.transform=B),N&&($[0].style.webkitTransform=N),e.roundLengths&&(S=Math.floor(S))}else S=(s-(e.slidesPerView-1)*w)/e.slidesPerView,e.roundLengths&&(S=Math.floor(S)),d[k]&&(this.isHorizontal()?d[k].style.width=S+"px":d[k].style.height=S+"px");d[k]&&(d[k].swiperSlideSize=S),u.push(S),e.centeredSlides?(y=y+S/2+x/2+w,0===x&&0!==k&&(y=y-s/2-w),0===k&&(y=y-s/2-w),Math.abs(y)<.001&&(y=0),e.roundLengths&&(y=Math.floor(y)),T%e.slidesPerGroup==0&&p.push(y),c.push(y)):(e.roundLengths&&(y=Math.floor(y)),(T-Math.min(this.params.slidesPerGroupSkip,T))%this.params.slidesPerGroup==0&&p.push(y),c.push(y),y=y+S+w),this.virtualSize+=S+w,x=S,T+=1}}if(this.virtualSize=Math.max(this.virtualSize,s)+m,a&&r&&("slide"===e.effect||"coverflow"===e.effect)&&i.css({width:this.virtualSize+e.spaceBetween+"px"}),e.setWrapperSize&&(this.isHorizontal()?i.css({width:this.virtualSize+e.spaceBetween+"px"}):i.css({height:this.virtualSize+e.spaceBetween+"px"})),e.slidesPerColumn>1&&(this.virtualSize=(S+e.spaceBetween)*E,this.virtualSize=Math.ceil(this.virtualSize/e.slidesPerColumn)-e.spaceBetween,this.isHorizontal()?i.css({width:this.virtualSize+e.spaceBetween+"px"}):i.css({height:this.virtualSize+e.spaceBetween+"px"}),e.centeredSlides)){C=[];for(var Q=0;Q<p.length;Q+=1){var J=p[Q];e.roundLengths&&(J=Math.floor(J)),p[Q]<this.virtualSize+p[0]&&C.push(J)}p=C}if(!e.centeredSlides){C=[];for(var ee=0;ee<p.length;ee+=1){var te=p[ee];e.roundLengths&&(te=Math.floor(te)),p[ee]<=this.virtualSize-s&&C.push(te)}p=C,Math.floor(this.virtualSize-s)-Math.floor(p[p.length-1])>1&&p.push(this.virtualSize-s)}if(0===p.length&&(p=[0]),0!==e.spaceBetween&&(this.isHorizontal()?a?d.filter(v).css({marginLeft:w+"px"}):d.filter(v).css({marginRight:w+"px"}):d.filter(v).css({marginBottom:w+"px"})),e.centeredSlides&&e.centeredSlidesBounds){var ie=0;u.forEach((function(t){ie+=t+(e.spaceBetween?e.spaceBetween:0)}));var se=(ie-=e.spaceBetween)-s;p=p.map((function(e){return e<0?-f:e>se?se+m:e}))}if(e.centerInsufficientSlides){var ae=0;if(u.forEach((function(t){ae+=t+(e.spaceBetween?e.spaceBetween:0)})),(ae-=e.spaceBetween)<s){var re=(s-ae)/2;p.forEach((function(e,t){p[t]=e-re})),c.forEach((function(e,t){c[t]=e+re}))}}n.extend(this,{slides:d,snapGrid:p,slidesGrid:c,slidesSizesGrid:u}),h!==l&&this.emit("slidesLengthChange"),p.length!==g&&(this.params.watchOverflow&&this.checkOverflow(),this.emit("snapGridLengthChange")),c.length!==b&&this.emit("slidesGridLengthChange"),(e.watchSlidesProgress||e.watchSlidesVisibility)&&this.updateSlidesOffset()}},updateAutoHeight:function(e){var t,i=[],s=0;if("number"==typeof e?this.setTransition(e):!0===e&&this.setTransition(this.params.speed),"auto"!==this.params.slidesPerView&&this.params.slidesPerView>1)if(this.params.centeredSlides)i.push.apply(i,this.visibleSlides);else for(t=0;t<Math.ceil(this.params.slidesPerView);t+=1){var a=this.activeIndex+t;if(a>this.slides.length)break;i.push(this.slides.eq(a)[0])}else i.push(this.slides.eq(this.activeIndex)[0]);for(t=0;t<i.length;t+=1)if(void 0!==i[t]){var r=i[t].offsetHeight;s=r>s?r:s}s&&this.$wrapperEl.css("height",s+"px")},updateSlidesOffset:function(){for(var e=this.slides,t=0;t<e.length;t+=1)e[t].swiperSlideOffset=this.isHorizontal()?e[t].offsetLeft:e[t].offsetTop},updateSlidesProgress:function(e){void 0===e&&(e=this&&this.translate||0);var t=this.params,i=this.slides,a=this.rtlTranslate;if(0!==i.length){void 0===i[0].swiperSlideOffset&&this.updateSlidesOffset();var r=-e;a&&(r=e),i.removeClass(t.slideVisibleClass),this.visibleSlidesIndexes=[],this.visibleSlides=[];for(var n=0;n<i.length;n+=1){var o=i[n],l=(r+(t.centeredSlides?this.minTranslate():0)-o.swiperSlideOffset)/(o.swiperSlideSize+t.spaceBetween);if(t.watchSlidesVisibility||t.centeredSlides&&t.autoHeight){var d=-(r-o.swiperSlideOffset),h=d+this.slidesSizesGrid[n];(d>=0&&d<this.size-1||h>1&&h<=this.size||d<=0&&h>=this.size)&&(this.visibleSlides.push(o),this.visibleSlidesIndexes.push(n),i.eq(n).addClass(t.slideVisibleClass))}o.progress=a?-l:l}this.visibleSlides=s(this.visibleSlides)}},updateProgress:function(e){if(void 0===e){var t=this.rtlTranslate?-1:1;e=this&&this.translate&&this.translate*t||0}var i=this.params,s=this.maxTranslate()-this.minTranslate(),a=this.progress,r=this.isBeginning,o=this.isEnd,l=r,d=o;0===s?(a=0,r=!0,o=!0):(r=(a=(e-this.minTranslate())/s)<=0,o=a>=1),n.extend(this,{progress:a,isBeginning:r,isEnd:o}),(i.watchSlidesProgress||i.watchSlidesVisibility||i.centeredSlides&&i.autoHeight)&&this.updateSlidesProgress(e),r&&!l&&this.emit("reachBeginning toEdge"),o&&!d&&this.emit("reachEnd toEdge"),(l&&!r||d&&!o)&&this.emit("fromEdge"),this.emit("progress",a)},updateSlidesClasses:function(){var e,t=this.slides,i=this.params,s=this.$wrapperEl,a=this.activeIndex,r=this.realIndex,n=this.virtual&&i.virtual.enabled;t.removeClass(i.slideActiveClass+" "+i.slideNextClass+" "+i.slidePrevClass+" "+i.slideDuplicateActiveClass+" "+i.slideDuplicateNextClass+" "+i.slideDuplicatePrevClass),(e=n?this.$wrapperEl.find("."+i.slideClass+'[data-swiper-slide-index="'+a+'"]'):t.eq(a)).addClass(i.slideActiveClass),i.loop&&(e.hasClass(i.slideDuplicateClass)?s.children("."+i.slideClass+":not(."+i.slideDuplicateClass+')[data-swiper-slide-index="'+r+'"]').addClass(i.slideDuplicateActiveClass):s.children("."+i.slideClass+"."+i.slideDuplicateClass+'[data-swiper-slide-index="'+r+'"]').addClass(i.slideDuplicateActiveClass));var o=e.nextAll("."+i.slideClass).eq(0).addClass(i.slideNextClass);i.loop&&0===o.length&&(o=t.eq(0)).addClass(i.slideNextClass);var l=e.prevAll("."+i.slideClass).eq(0).addClass(i.slidePrevClass);i.loop&&0===l.length&&(l=t.eq(-1)).addClass(i.slidePrevClass),i.loop&&(o.hasClass(i.slideDuplicateClass)?s.children("."+i.slideClass+":not(."+i.slideDuplicateClass+')[data-swiper-slide-index="'+o.attr("data-swiper-slide-index")+'"]').addClass(i.slideDuplicateNextClass):s.children("."+i.slideClass+"."+i.slideDuplicateClass+'[data-swiper-slide-index="'+o.attr("data-swiper-slide-index")+'"]').addClass(i.slideDuplicateNextClass),l.hasClass(i.slideDuplicateClass)?s.children("."+i.slideClass+":not(."+i.slideDuplicateClass+')[data-swiper-slide-index="'+l.attr("data-swiper-slide-index")+'"]').addClass(i.slideDuplicatePrevClass):s.children("."+i.slideClass+"."+i.slideDuplicateClass+'[data-swiper-slide-index="'+l.attr("data-swiper-slide-index")+'"]').addClass(i.slideDuplicatePrevClass))},updateActiveIndex:function(e){var t,i=this.rtlTranslate?this.translate:-this.translate,s=this.slidesGrid,a=this.snapGrid,r=this.params,o=this.activeIndex,l=this.realIndex,d=this.snapIndex,h=e;if(void 0===h){for(var p=0;p<s.length;p+=1)void 0!==s[p+1]?i>=s[p]&&i<s[p+1]-(s[p+1]-s[p])/2?h=p:i>=s[p]&&i<s[p+1]&&(h=p+1):i>=s[p]&&(h=p);r.normalizeSlideIndex&&(h<0||void 0===h)&&(h=0)}if(a.indexOf(i)>=0)t=a.indexOf(i);else{var c=Math.min(r.slidesPerGroupSkip,h);t=c+Math.floor((h-c)/r.slidesPerGroup)}if(t>=a.length&&(t=a.length-1),h!==o){var u=parseInt(this.slides.eq(h).attr("data-swiper-slide-index")||h,10);n.extend(this,{snapIndex:t,realIndex:u,previousIndex:o,activeIndex:h}),this.emit("activeIndexChange"),this.emit("snapIndexChange"),l!==u&&this.emit("realIndexChange"),(this.initialized||this.runCallbacksOnInit)&&this.emit("slideChange")}else t!==d&&(this.snapIndex=t,this.emit("snapIndexChange"))},updateClickedSlide:function(e){var t=this.params,i=s(e.target).closest("."+t.slideClass)[0],a=!1;if(i)for(var r=0;r<this.slides.length;r+=1)this.slides[r]===i&&(a=!0);if(!i||!a)return this.clickedSlide=void 0,void(this.clickedIndex=void 0);this.clickedSlide=i,this.virtual&&this.params.virtual.enabled?this.clickedIndex=parseInt(s(i).attr("data-swiper-slide-index"),10):this.clickedIndex=s(i).index(),t.slideToClickedSlide&&void 0!==this.clickedIndex&&this.clickedIndex!==this.activeIndex&&this.slideToClickedSlide()}};var p={getTranslate:function(e){void 0===e&&(e=this.isHorizontal()?"x":"y");var t=this.params,i=this.rtlTranslate,s=this.translate,a=this.$wrapperEl;if(t.virtualTranslate)return i?-s:s;if(t.cssMode)return s;var r=n.getTranslate(a[0],e);return i&&(r=-r),r||0},setTranslate:function(e,t){var i=this.rtlTranslate,s=this.params,a=this.$wrapperEl,r=this.wrapperEl,n=this.progress,o=0,l=0;this.isHorizontal()?o=i?-e:e:l=e,s.roundLengths&&(o=Math.floor(o),l=Math.floor(l)),s.cssMode?r[this.isHorizontal()?"scrollLeft":"scrollTop"]=this.isHorizontal()?-o:-l:s.virtualTranslate||a.transform("translate3d("+o+"px, "+l+"px, 0px)"),this.previousTranslate=this.translate,this.translate=this.isHorizontal()?o:l;var d=this.maxTranslate()-this.minTranslate();(0===d?0:(e-this.minTranslate())/d)!==n&&this.updateProgress(e),this.emit("setTranslate",this.translate,t)},minTranslate:function(){return-this.snapGrid[0]},maxTranslate:function(){return-this.snapGrid[this.snapGrid.length-1]},translateTo:function(e,t,i,s,a){var r;void 0===e&&(e=0),void 0===t&&(t=this.params.speed),void 0===i&&(i=!0),void 0===s&&(s=!0);var n=this,o=n.params,l=n.wrapperEl;if(n.animating&&o.preventInteractionOnTransition)return!1;var d,h=n.minTranslate(),p=n.maxTranslate();if(d=s&&e>h?h:s&&e<p?p:e,n.updateProgress(d),o.cssMode){var c=n.isHorizontal();return 0===t?l[c?"scrollLeft":"scrollTop"]=-d:l.scrollTo?l.scrollTo(((r={})[c?"left":"top"]=-d,r.behavior="smooth",r)):l[c?"scrollLeft":"scrollTop"]=-d,!0}return 0===t?(n.setTransition(0),n.setTranslate(d),i&&(n.emit("beforeTransitionStart",t,a),n.emit("transitionEnd"))):(n.setTransition(t),n.setTranslate(d),i&&(n.emit("beforeTransitionStart",t,a),n.emit("transitionStart")),n.animating||(n.animating=!0,n.onTranslateToWrapperTransitionEnd||(n.onTranslateToWrapperTransitionEnd=function(e){n&&!n.destroyed&&e.target===this&&(n.$wrapperEl[0].removeEventListener("transitionend",n.onTranslateToWrapperTransitionEnd),n.$wrapperEl[0].removeEventListener("webkitTransitionEnd",n.onTranslateToWrapperTransitionEnd),n.onTranslateToWrapperTransitionEnd=null,delete n.onTranslateToWrapperTransitionEnd,i&&n.emit("transitionEnd"))}),n.$wrapperEl[0].addEventListener("transitionend",n.onTranslateToWrapperTransitionEnd),n.$wrapperEl[0].addEventListener("webkitTransitionEnd",n.onTranslateToWrapperTransitionEnd))),!0}};var c={setTransition:function(e,t){this.params.cssMode||this.$wrapperEl.transition(e),this.emit("setTransition",e,t)},transitionStart:function(e,t){void 0===e&&(e=!0);var i=this.activeIndex,s=this.params,a=this.previousIndex;if(!s.cssMode){s.autoHeight&&this.updateAutoHeight();var r=t;if(r||(r=i>a?"next":i<a?"prev":"reset"),this.emit("transitionStart"),e&&i!==a){if("reset"===r)return void this.emit("slideResetTransitionStart");this.emit("slideChangeTransitionStart"),"next"===r?this.emit("slideNextTransitionStart"):this.emit("slidePrevTransitionStart")}}},transitionEnd:function(e,t){void 0===e&&(e=!0);var i=this.activeIndex,s=this.previousIndex,a=this.params;if(this.animating=!1,!a.cssMode){this.setTransition(0);var r=t;if(r||(r=i>s?"next":i<s?"prev":"reset"),this.emit("transitionEnd"),e&&i!==s){if("reset"===r)return void this.emit("slideResetTransitionEnd");this.emit("slideChangeTransitionEnd"),"next"===r?this.emit("slideNextTransitionEnd"):this.emit("slidePrevTransitionEnd")}}}};var u={slideTo:function(e,t,i,s){var a;void 0===e&&(e=0),void 0===t&&(t=this.params.speed),void 0===i&&(i=!0);var r=this,n=e;n<0&&(n=0);var o=r.params,l=r.snapGrid,d=r.slidesGrid,h=r.previousIndex,p=r.activeIndex,c=r.rtlTranslate,u=r.wrapperEl;if(r.animating&&o.preventInteractionOnTransition)return!1;var v=Math.min(r.params.slidesPerGroupSkip,n),f=v+Math.floor((n-v)/r.params.slidesPerGroup);f>=l.length&&(f=l.length-1),(p||o.initialSlide||0)===(h||0)&&i&&r.emit("beforeSlideChangeStart");var m,g=-l[f];if(r.updateProgress(g),o.normalizeSlideIndex)for(var b=0;b<d.length;b+=1)-Math.floor(100*g)>=Math.floor(100*d[b])&&(n=b);if(r.initialized&&n!==p){if(!r.allowSlideNext&&g<r.translate&&g<r.minTranslate())return!1;if(!r.allowSlidePrev&&g>r.translate&&g>r.maxTranslate()&&(p||0)!==n)return!1}if(m=n>p?"next":n<p?"prev":"reset",c&&-g===r.translate||!c&&g===r.translate)return r.updateActiveIndex(n),o.autoHeight&&r.updateAutoHeight(),r.updateSlidesClasses(),"slide"!==o.effect&&r.setTranslate(g),"reset"!==m&&(r.transitionStart(i,m),r.transitionEnd(i,m)),!1;if(o.cssMode){var w=r.isHorizontal();return 0===t?u[w?"scrollLeft":"scrollTop"]=-g:u.scrollTo?u.scrollTo(((a={})[w?"left":"top"]=-g,a.behavior="smooth",a)):u[w?"scrollLeft":"scrollTop"]=-g,!0}return 0===t?(r.setTransition(0),r.setTranslate(g),r.updateActiveIndex(n),r.updateSlidesClasses(),r.emit("beforeTransitionStart",t,s),r.transitionStart(i,m),r.transitionEnd(i,m)):(r.setTransition(t),r.setTranslate(g),r.updateActiveIndex(n),r.updateSlidesClasses(),r.emit("beforeTransitionStart",t,s),r.transitionStart(i,m),r.animating||(r.animating=!0,r.onSlideToWrapperTransitionEnd||(r.onSlideToWrapperTransitionEnd=function(e){r&&!r.destroyed&&e.target===this&&(r.$wrapperEl[0].removeEventListener("transitionend",r.onSlideToWrapperTransitionEnd),r.$wrapperEl[0].removeEventListener("webkitTransitionEnd",r.onSlideToWrapperTransitionEnd),r.onSlideToWrapperTransitionEnd=null,delete r.onSlideToWrapperTransitionEnd,r.transitionEnd(i,m))}),r.$wrapperEl[0].addEventListener("transitionend",r.onSlideToWrapperTransitionEnd),r.$wrapperEl[0].addEventListener("webkitTransitionEnd",r.onSlideToWrapperTransitionEnd))),!0},slideToLoop:function(e,t,i,s){void 0===e&&(e=0),void 0===t&&(t=this.params.speed),void 0===i&&(i=!0);var a=e;return this.params.loop&&(a+=this.loopedSlides),this.slideTo(a,t,i,s)},slideNext:function(e,t,i){void 0===e&&(e=this.params.speed),void 0===t&&(t=!0);var s=this.params,a=this.animating,r=this.activeIndex<s.slidesPerGroupSkip?1:s.slidesPerGroup;if(s.loop){if(a)return!1;this.loopFix(),this._clientLeft=this.$wrapperEl[0].clientLeft}return this.slideTo(this.activeIndex+r,e,t,i)},slidePrev:function(e,t,i){void 0===e&&(e=this.params.speed),void 0===t&&(t=!0);var s=this.params,a=this.animating,r=this.snapGrid,n=this.slidesGrid,o=this.rtlTranslate;if(s.loop){if(a)return!1;this.loopFix(),this._clientLeft=this.$wrapperEl[0].clientLeft}function l(e){return e<0?-Math.floor(Math.abs(e)):Math.floor(e)}var d,h=l(o?this.translate:-this.translate),p=r.map((function(e){return l(e)})),c=(n.map((function(e){return l(e)})),r[p.indexOf(h)],r[p.indexOf(h)-1]);return void 0===c&&s.cssMode&&r.forEach((function(e){!c&&h>=e&&(c=e)})),void 0!==c&&(d=n.indexOf(c))<0&&(d=this.activeIndex-1),this.slideTo(d,e,t,i)},slideReset:function(e,t,i){return void 0===e&&(e=this.params.speed),void 0===t&&(t=!0),this.slideTo(this.activeIndex,e,t,i)},slideToClosest:function(e,t,i,s){void 0===e&&(e=this.params.speed),void 0===t&&(t=!0),void 0===s&&(s=.5);var a=this.activeIndex,r=Math.min(this.params.slidesPerGroupSkip,a),n=r+Math.floor((a-r)/this.params.slidesPerGroup),o=this.rtlTranslate?this.translate:-this.translate;if(o>=this.snapGrid[n]){var l=this.snapGrid[n];o-l>(this.snapGrid[n+1]-l)*s&&(a+=this.params.slidesPerGroup)}else{var d=this.snapGrid[n-1];o-d<=(this.snapGrid[n]-d)*s&&(a-=this.params.slidesPerGroup)}return a=Math.max(a,0),a=Math.min(a,this.slidesGrid.length-1),this.slideTo(a,e,t,i)},slideToClickedSlide:function(){var e,t=this,i=t.params,a=t.$wrapperEl,r="auto"===i.slidesPerView?t.slidesPerViewDynamic():i.slidesPerView,o=t.clickedIndex;if(i.loop){if(t.animating)return;e=parseInt(s(t.clickedSlide).attr("data-swiper-slide-index"),10),i.centeredSlides?o<t.loopedSlides-r/2||o>t.slides.length-t.loopedSlides+r/2?(t.loopFix(),o=a.children("."+i.slideClass+'[data-swiper-slide-index="'+e+'"]:not(.'+i.slideDuplicateClass+")").eq(0).index(),n.nextTick((function(){t.slideTo(o)}))):t.slideTo(o):o>t.slides.length-r?(t.loopFix(),o=a.children("."+i.slideClass+'[data-swiper-slide-index="'+e+'"]:not(.'+i.slideDuplicateClass+")").eq(0).index(),n.nextTick((function(){t.slideTo(o)}))):t.slideTo(o)}else t.slideTo(o)}};var v={loopCreate:function(){var t=this,i=t.params,a=t.$wrapperEl;a.children("."+i.slideClass+"."+i.slideDuplicateClass).remove();var r=a.children("."+i.slideClass);if(i.loopFillGroupWithBlank){var n=i.slidesPerGroup-r.length%i.slidesPerGroup;if(n!==i.slidesPerGroup){for(var o=0;o<n;o+=1){var l=s(e.createElement("div")).addClass(i.slideClass+" "+i.slideBlankClass);a.append(l)}r=a.children("."+i.slideClass)}}"auto"!==i.slidesPerView||i.loopedSlides||(i.loopedSlides=r.length),t.loopedSlides=Math.ceil(parseFloat(i.loopedSlides||i.slidesPerView,10)),t.loopedSlides+=i.loopAdditionalSlides,t.loopedSlides>r.length&&(t.loopedSlides=r.length);var d=[],h=[];r.each((function(e,i){var a=s(i);e<t.loopedSlides&&h.push(i),e<r.length&&e>=r.length-t.loopedSlides&&d.push(i),a.attr("data-swiper-slide-index",e)}));for(var p=0;p<h.length;p+=1)a.append(s(h[p].cloneNode(!0)).addClass(i.slideDuplicateClass));for(var c=d.length-1;c>=0;c-=1)a.prepend(s(d[c].cloneNode(!0)).addClass(i.slideDuplicateClass))},loopFix:function(){this.emit("beforeLoopFix");var e,t=this.activeIndex,i=this.slides,s=this.loopedSlides,a=this.allowSlidePrev,r=this.allowSlideNext,n=this.snapGrid,o=this.rtlTranslate;this.allowSlidePrev=!0,this.allowSlideNext=!0;var l=-n[t]-this.getTranslate();if(t<s)e=i.length-3*s+t,e+=s,this.slideTo(e,0,!1,!0)&&0!==l&&this.setTranslate((o?-this.translate:this.translate)-l);else if(t>=i.length-s){e=-i.length+t+s,e+=s,this.slideTo(e,0,!1,!0)&&0!==l&&this.setTranslate((o?-this.translate:this.translate)-l)}this.allowSlidePrev=a,this.allowSlideNext=r,this.emit("loopFix")},loopDestroy:function(){var e=this.$wrapperEl,t=this.params,i=this.slides;e.children("."+t.slideClass+"."+t.slideDuplicateClass+",."+t.slideClass+"."+t.slideBlankClass).remove(),i.removeAttr("data-swiper-slide-index")}};var f={setGrabCursor:function(e){if(!(o.touch||!this.params.simulateTouch||this.params.watchOverflow&&this.isLocked||this.params.cssMode)){var t=this.el;t.style.cursor="move",t.style.cursor=e?"-webkit-grabbing":"-webkit-grab",t.style.cursor=e?"-moz-grabbin":"-moz-grab",t.style.cursor=e?"grabbing":"grab"}},unsetGrabCursor:function(){o.touch||this.params.watchOverflow&&this.isLocked||this.params.cssMode||(this.el.style.cursor="")}};var m,g,b,w,y,x,T,E,S,C,M,P,z,k,$,L={appendSlide:function(e){var t=this.$wrapperEl,i=this.params;if(i.loop&&this.loopDestroy(),"object"==typeof e&&"length"in e)for(var s=0;s<e.length;s+=1)e[s]&&t.append(e[s]);else t.append(e);i.loop&&this.loopCreate(),i.observer&&o.observer||this.update()},prependSlide:function(e){var t=this.params,i=this.$wrapperEl,s=this.activeIndex;t.loop&&this.loopDestroy();var a=s+1;if("object"==typeof e&&"length"in e){for(var r=0;r<e.length;r+=1)e[r]&&i.prepend(e[r]);a=s+e.length}else i.prepend(e);t.loop&&this.loopCreate(),t.observer&&o.observer||this.update(),this.slideTo(a,0,!1)},addSlide:function(e,t){var i=this.$wrapperEl,s=this.params,a=this.activeIndex;s.loop&&(a-=this.loopedSlides,this.loopDestroy(),this.slides=i.children("."+s.slideClass));var r=this.slides.length;if(e<=0)this.prependSlide(t);else if(e>=r)this.appendSlide(t);else{for(var n=a>e?a+1:a,l=[],d=r-1;d>=e;d-=1){var h=this.slides.eq(d);h.remove(),l.unshift(h)}if("object"==typeof t&&"length"in t){for(var p=0;p<t.length;p+=1)t[p]&&i.append(t[p]);n=a>e?a+t.length:a}else i.append(t);for(var c=0;c<l.length;c+=1)i.append(l[c]);s.loop&&this.loopCreate(),s.observer&&o.observer||this.update(),s.loop?this.slideTo(n+this.loopedSlides,0,!1):this.slideTo(n,0,!1)}},removeSlide:function(e){var t=this.params,i=this.$wrapperEl,s=this.activeIndex;t.loop&&(s-=this.loopedSlides,this.loopDestroy(),this.slides=i.children("."+t.slideClass));var a,r=s;if("object"==typeof e&&"length"in e){for(var n=0;n<e.length;n+=1)a=e[n],this.slides[a]&&this.slides.eq(a).remove(),a<r&&(r-=1);r=Math.max(r,0)}else a=e,this.slides[a]&&this.slides.eq(a).remove(),a<r&&(r-=1),r=Math.max(r,0);t.loop&&this.loopCreate(),t.observer&&o.observer||this.update(),t.loop?this.slideTo(r+this.loopedSlides,0,!1):this.slideTo(r,0,!1)},removeAllSlides:function(){for(var e=[],t=0;t<this.slides.length;t+=1)e.push(t);this.removeSlide(e)}},I=(m=t.navigator.platform,g=t.navigator.userAgent,b={ios:!1,android:!1,androidChrome:!1,desktop:!1,iphone:!1,ipod:!1,ipad:!1,edge:!1,ie:!1,firefox:!1,macos:!1,windows:!1,cordova:!(!t.cordova&&!t.phonegap),phonegap:!(!t.cordova&&!t.phonegap),electron:!1},w=t.screen.width,y=t.screen.height,x=g.match(/(Android);?[\s\/]+([\d.]+)?/),T=g.match(/(iPad).*OS\s([\d_]+)/),E=g.match(/(iPod)(.*OS\s([\d_]+))?/),S=!T&&g.match(/(iPhone\sOS|iOS)\s([\d_]+)/),C=g.indexOf("MSIE ")>=0||g.indexOf("Trident/")>=0,M=g.indexOf("Edge/")>=0,P=g.indexOf("Gecko/")>=0&&g.indexOf("Firefox/")>=0,z="Win32"===m,k=g.toLowerCase().indexOf("electron")>=0,$="MacIntel"===m,!T&&$&&o.touch&&(1024===w&&1366===y||834===w&&1194===y||834===w&&1112===y||768===w&&1024===y)&&(T=g.match(/(Version)\/([\d.]+)/),$=!1),b.ie=C,b.edge=M,b.firefox=P,x&&!z&&(b.os="android",b.osVersion=x[2],b.android=!0,b.androidChrome=g.toLowerCase().indexOf("chrome")>=0),(T||S||E)&&(b.os="ios",b.ios=!0),S&&!E&&(b.osVersion=S[2].replace(/_/g,"."),b.iphone=!0),T&&(b.osVersion=T[2].replace(/_/g,"."),b.ipad=!0),E&&(b.osVersion=E[3]?E[3].replace(/_/g,"."):null,b.ipod=!0),b.ios&&b.osVersion&&g.indexOf("Version/")>=0&&"10"===b.osVersion.split(".")[0]&&(b.osVersion=g.toLowerCase().split("version/")[1].split(" ")[0]),b.webView=!(!(S||T||E)||!g.match(/.*AppleWebKit(?!.*Safari)/i)&&!t.navigator.standalone)||t.matchMedia&&t.matchMedia("(display-mode: standalone)").matches,b.webview=b.webView,b.standalone=b.webView,b.desktop=!(b.ios||b.android)||k,b.desktop&&(b.electron=k,b.macos=$,b.windows=z,b.macos&&(b.os="macos"),b.windows&&(b.os="windows")),b.pixelRatio=t.devicePixelRatio||1,b);function D(i){var a=this.touchEventsData,r=this.params,o=this.touches;if(!this.animating||!r.preventInteractionOnTransition){var l=i;l.originalEvent&&(l=l.originalEvent);var d=s(l.target);if(("wrapper"!==r.touchEventsTarget||d.closest(this.wrapperEl).length)&&(a.isTouchEvent="touchstart"===l.type,(a.isTouchEvent||!("which"in l)||3!==l.which)&&!(!a.isTouchEvent&&"button"in l&&l.button>0||a.isTouched&&a.isMoved)))if(r.noSwiping&&d.closest(r.noSwipingSelector?r.noSwipingSelector:"."+r.noSwipingClass)[0])this.allowClick=!0;else if(!r.swipeHandler||d.closest(r.swipeHandler)[0]){o.currentX="touchstart"===l.type?l.targetTouches[0].pageX:l.pageX,o.currentY="touchstart"===l.type?l.targetTouches[0].pageY:l.pageY;var h=o.currentX,p=o.currentY,c=r.edgeSwipeDetection||r.iOSEdgeSwipeDetection,u=r.edgeSwipeThreshold||r.iOSEdgeSwipeThreshold;if(!c||!(h<=u||h>=t.screen.width-u)){if(n.extend(a,{isTouched:!0,isMoved:!1,allowTouchCallbacks:!0,isScrolling:void 0,startMoving:void 0}),o.startX=h,o.startY=p,a.touchStartTime=n.now(),this.allowClick=!0,this.updateSize(),this.swipeDirection=void 0,r.threshold>0&&(a.allowThresholdMove=!1),"touchstart"!==l.type){var v=!0;d.is(a.formElements)&&(v=!1),e.activeElement&&s(e.activeElement).is(a.formElements)&&e.activeElement!==d[0]&&e.activeElement.blur();var f=v&&this.allowTouchMove&&r.touchStartPreventDefault;(r.touchStartForcePreventDefault||f)&&l.preventDefault()}this.emit("touchStart",l)}}}}function O(t){var i=this.touchEventsData,a=this.params,r=this.touches,o=this.rtlTranslate,l=t;if(l.originalEvent&&(l=l.originalEvent),i.isTouched){if(!i.isTouchEvent||"mousemove"!==l.type){var d="touchmove"===l.type&&l.targetTouches&&(l.targetTouches[0]||l.changedTouches[0]),h="touchmove"===l.type?d.pageX:l.pageX,p="touchmove"===l.type?d.pageY:l.pageY;if(l.preventedByNestedSwiper)return r.startX=h,void(r.startY=p);if(!this.allowTouchMove)return this.allowClick=!1,void(i.isTouched&&(n.extend(r,{startX:h,startY:p,currentX:h,currentY:p}),i.touchStartTime=n.now()));if(i.isTouchEvent&&a.touchReleaseOnEdges&&!a.loop)if(this.isVertical()){if(p<r.startY&&this.translate<=this.maxTranslate()||p>r.startY&&this.translate>=this.minTranslate())return i.isTouched=!1,void(i.isMoved=!1)}else if(h<r.startX&&this.translate<=this.maxTranslate()||h>r.startX&&this.translate>=this.minTranslate())return;if(i.isTouchEvent&&e.activeElement&&l.target===e.activeElement&&s(l.target).is(i.formElements))return i.isMoved=!0,void(this.allowClick=!1);if(i.allowTouchCallbacks&&this.emit("touchMove",l),!(l.targetTouches&&l.targetTouches.length>1)){r.currentX=h,r.currentY=p;var c=r.currentX-r.startX,u=r.currentY-r.startY;if(!(this.params.threshold&&Math.sqrt(Math.pow(c,2)+Math.pow(u,2))<this.params.threshold)){var v;if(void 0===i.isScrolling)this.isHorizontal()&&r.currentY===r.startY||this.isVertical()&&r.currentX===r.startX?i.isScrolling=!1:c*c+u*u>=25&&(v=180*Math.atan2(Math.abs(u),Math.abs(c))/Math.PI,i.isScrolling=this.isHorizontal()?v>a.touchAngle:90-v>a.touchAngle);if(i.isScrolling&&this.emit("touchMoveOpposite",l),void 0===i.startMoving&&(r.currentX===r.startX&&r.currentY===r.startY||(i.startMoving=!0)),i.isScrolling)i.isTouched=!1;else if(i.startMoving){this.allowClick=!1,a.cssMode||l.preventDefault(),a.touchMoveStopPropagation&&!a.nested&&l.stopPropagation(),i.isMoved||(a.loop&&this.loopFix(),i.startTranslate=this.getTranslate(),this.setTransition(0),this.animating&&this.$wrapperEl.trigger("webkitTransitionEnd transitionend"),i.allowMomentumBounce=!1,!a.grabCursor||!0!==this.allowSlideNext&&!0!==this.allowSlidePrev||this.setGrabCursor(!0),this.emit("sliderFirstMove",l)),this.emit("sliderMove",l),i.isMoved=!0;var f=this.isHorizontal()?c:u;r.diff=f,f*=a.touchRatio,o&&(f=-f),this.swipeDirection=f>0?"prev":"next",i.currentTranslate=f+i.startTranslate;var m=!0,g=a.resistanceRatio;if(a.touchReleaseOnEdges&&(g=0),f>0&&i.currentTranslate>this.minTranslate()?(m=!1,a.resistance&&(i.currentTranslate=this.minTranslate()-1+Math.pow(-this.minTranslate()+i.startTranslate+f,g))):f<0&&i.currentTranslate<this.maxTranslate()&&(m=!1,a.resistance&&(i.currentTranslate=this.maxTranslate()+1-Math.pow(this.maxTranslate()-i.startTranslate-f,g))),m&&(l.preventedByNestedSwiper=!0),!this.allowSlideNext&&"next"===this.swipeDirection&&i.currentTranslate<i.startTranslate&&(i.currentTranslate=i.startTranslate),!this.allowSlidePrev&&"prev"===this.swipeDirection&&i.currentTranslate>i.startTranslate&&(i.currentTranslate=i.startTranslate),a.threshold>0){if(!(Math.abs(f)>a.threshold||i.allowThresholdMove))return void(i.currentTranslate=i.startTranslate);if(!i.allowThresholdMove)return i.allowThresholdMove=!0,r.startX=r.currentX,r.startY=r.currentY,i.currentTranslate=i.startTranslate,void(r.diff=this.isHorizontal()?r.currentX-r.startX:r.currentY-r.startY)}a.followFinger&&!a.cssMode&&((a.freeMode||a.watchSlidesProgress||a.watchSlidesVisibility)&&(this.updateActiveIndex(),this.updateSlidesClasses()),a.freeMode&&(0===i.velocities.length&&i.velocities.push({position:r[this.isHorizontal()?"startX":"startY"],time:i.touchStartTime}),i.velocities.push({position:r[this.isHorizontal()?"currentX":"currentY"],time:n.now()})),this.updateProgress(i.currentTranslate),this.setTranslate(i.currentTranslate))}}}}}else i.startMoving&&i.isScrolling&&this.emit("touchMoveOpposite",l)}function A(e){var t=this,i=t.touchEventsData,s=t.params,a=t.touches,r=t.rtlTranslate,o=t.$wrapperEl,l=t.slidesGrid,d=t.snapGrid,h=e;if(h.originalEvent&&(h=h.originalEvent),i.allowTouchCallbacks&&t.emit("touchEnd",h),i.allowTouchCallbacks=!1,!i.isTouched)return i.isMoved&&s.grabCursor&&t.setGrabCursor(!1),i.isMoved=!1,void(i.startMoving=!1);s.grabCursor&&i.isMoved&&i.isTouched&&(!0===t.allowSlideNext||!0===t.allowSlidePrev)&&t.setGrabCursor(!1);var p,c=n.now(),u=c-i.touchStartTime;if(t.allowClick&&(t.updateClickedSlide(h),t.emit("tap click",h),u<300&&c-i.lastClickTime<300&&t.emit("doubleTap doubleClick",h)),i.lastClickTime=n.now(),n.nextTick((function(){t.destroyed||(t.allowClick=!0)})),!i.isTouched||!i.isMoved||!t.swipeDirection||0===a.diff||i.currentTranslate===i.startTranslate)return i.isTouched=!1,i.isMoved=!1,void(i.startMoving=!1);if(i.isTouched=!1,i.isMoved=!1,i.startMoving=!1,p=s.followFinger?r?t.translate:-t.translate:-i.currentTranslate,!s.cssMode)if(s.freeMode){if(p<-t.minTranslate())return void t.slideTo(t.activeIndex);if(p>-t.maxTranslate())return void(t.slides.length<d.length?t.slideTo(d.length-1):t.slideTo(t.slides.length-1));if(s.freeModeMomentum){if(i.velocities.length>1){var v=i.velocities.pop(),f=i.velocities.pop(),m=v.position-f.position,g=v.time-f.time;t.velocity=m/g,t.velocity/=2,Math.abs(t.velocity)<s.freeModeMinimumVelocity&&(t.velocity=0),(g>150||n.now()-v.time>300)&&(t.velocity=0)}else t.velocity=0;t.velocity*=s.freeModeMomentumVelocityRatio,i.velocities.length=0;var b=1e3*s.freeModeMomentumRatio,w=t.velocity*b,y=t.translate+w;r&&(y=-y);var x,T,E=!1,S=20*Math.abs(t.velocity)*s.freeModeMomentumBounceRatio;if(y<t.maxTranslate())s.freeModeMomentumBounce?(y+t.maxTranslate()<-S&&(y=t.maxTranslate()-S),x=t.maxTranslate(),E=!0,i.allowMomentumBounce=!0):y=t.maxTranslate(),s.loop&&s.centeredSlides&&(T=!0);else if(y>t.minTranslate())s.freeModeMomentumBounce?(y-t.minTranslate()>S&&(y=t.minTranslate()+S),x=t.minTranslate(),E=!0,i.allowMomentumBounce=!0):y=t.minTranslate(),s.loop&&s.centeredSlides&&(T=!0);else if(s.freeModeSticky){for(var C,M=0;M<d.length;M+=1)if(d[M]>-y){C=M;break}y=-(y=Math.abs(d[C]-y)<Math.abs(d[C-1]-y)||"next"===t.swipeDirection?d[C]:d[C-1])}if(T&&t.once("transitionEnd",(function(){t.loopFix()})),0!==t.velocity){if(b=r?Math.abs((-y-t.translate)/t.velocity):Math.abs((y-t.translate)/t.velocity),s.freeModeSticky){var P=Math.abs((r?-y:y)-t.translate),z=t.slidesSizesGrid[t.activeIndex];b=P<z?s.speed:P<2*z?1.5*s.speed:2.5*s.speed}}else if(s.freeModeSticky)return void t.slideToClosest();s.freeModeMomentumBounce&&E?(t.updateProgress(x),t.setTransition(b),t.setTranslate(y),t.transitionStart(!0,t.swipeDirection),t.animating=!0,o.transitionEnd((function(){t&&!t.destroyed&&i.allowMomentumBounce&&(t.emit("momentumBounce"),t.setTransition(s.speed),t.setTranslate(x),o.transitionEnd((function(){t&&!t.destroyed&&t.transitionEnd()})))}))):t.velocity?(t.updateProgress(y),t.setTransition(b),t.setTranslate(y),t.transitionStart(!0,t.swipeDirection),t.animating||(t.animating=!0,o.transitionEnd((function(){t&&!t.destroyed&&t.transitionEnd()})))):t.updateProgress(y),t.updateActiveIndex(),t.updateSlidesClasses()}else if(s.freeModeSticky)return void t.slideToClosest();(!s.freeModeMomentum||u>=s.longSwipesMs)&&(t.updateProgress(),t.updateActiveIndex(),t.updateSlidesClasses())}else{for(var k=0,$=t.slidesSizesGrid[0],L=0;L<l.length;L+=L<s.slidesPerGroupSkip?1:s.slidesPerGroup){var I=L<s.slidesPerGroupSkip-1?1:s.slidesPerGroup;void 0!==l[L+I]?p>=l[L]&&p<l[L+I]&&(k=L,$=l[L+I]-l[L]):p>=l[L]&&(k=L,$=l[l.length-1]-l[l.length-2])}var D=(p-l[k])/$,O=k<s.slidesPerGroupSkip-1?1:s.slidesPerGroup;if(u>s.longSwipesMs){if(!s.longSwipes)return void t.slideTo(t.activeIndex);"next"===t.swipeDirection&&(D>=s.longSwipesRatio?t.slideTo(k+O):t.slideTo(k)),"prev"===t.swipeDirection&&(D>1-s.longSwipesRatio?t.slideTo(k+O):t.slideTo(k))}else{if(!s.shortSwipes)return void t.slideTo(t.activeIndex);t.navigation&&(h.target===t.navigation.nextEl||h.target===t.navigation.prevEl)?h.target===t.navigation.nextEl?t.slideTo(k+O):t.slideTo(k):("next"===t.swipeDirection&&t.slideTo(k+O),"prev"===t.swipeDirection&&t.slideTo(k))}}}function G(){var e=this.params,t=this.el;if(!t||0!==t.offsetWidth){e.breakpoints&&this.setBreakpoint();var i=this.allowSlideNext,s=this.allowSlidePrev,a=this.snapGrid;this.allowSlideNext=!0,this.allowSlidePrev=!0,this.updateSize(),this.updateSlides(),this.updateSlidesClasses(),("auto"===e.slidesPerView||e.slidesPerView>1)&&this.isEnd&&!this.params.centeredSlides?this.slideTo(this.slides.length-1,0,!1,!0):this.slideTo(this.activeIndex,0,!1,!0),this.autoplay&&this.autoplay.running&&this.autoplay.paused&&this.autoplay.run(),this.allowSlidePrev=s,this.allowSlideNext=i,this.params.watchOverflow&&a!==this.snapGrid&&this.checkOverflow()}}function H(e){this.allowClick||(this.params.preventClicks&&e.preventDefault(),this.params.preventClicksPropagation&&this.animating&&(e.stopPropagation(),e.stopImmediatePropagation()))}function B(){var e=this.wrapperEl;this.previousTranslate=this.translate,this.translate=this.isHorizontal()?-e.scrollLeft:-e.scrollTop,-0===this.translate&&(this.translate=0),this.updateActiveIndex(),this.updateSlidesClasses();var t=this.maxTranslate()-this.minTranslate();(0===t?0:(this.translate-this.minTranslate())/t)!==this.progress&&this.updateProgress(this.translate),this.emit("setTranslate",this.translate,!1)}var N=!1;function X(){}var V={init:!0,direction:"horizontal",touchEventsTarget:"container",initialSlide:0,speed:300,cssMode:!1,updateOnWindowResize:!0,preventInteractionOnTransition:!1,edgeSwipeDetection:!1,edgeSwipeThreshold:20,freeMode:!1,freeModeMomentum:!0,freeModeMomentumRatio:1,freeModeMomentumBounce:!0,freeModeMomentumBounceRatio:1,freeModeMomentumVelocityRatio:1,freeModeSticky:!1,freeModeMinimumVelocity:.02,autoHeight:!1,setWrapperSize:!1,virtualTranslate:!1,effect:"slide",breakpoints:void 0,spaceBetween:0,slidesPerView:1,slidesPerColumn:1,slidesPerColumnFill:"column",slidesPerGroup:1,slidesPerGroupSkip:0,centeredSlides:!1,centeredSlidesBounds:!1,slidesOffsetBefore:0,slidesOffsetAfter:0,normalizeSlideIndex:!0,centerInsufficientSlides:!1,watchOverflow:!1,roundLengths:!1,touchRatio:1,touchAngle:45,simulateTouch:!0,shortSwipes:!0,longSwipes:!0,longSwipesRatio:.5,longSwipesMs:300,followFinger:!0,allowTouchMove:!0,threshold:0,touchMoveStopPropagation:!1,touchStartPreventDefault:!0,touchStartForcePreventDefault:!1,touchReleaseOnEdges:!1,uniqueNavElements:!0,resistance:!0,resistanceRatio:.85,watchSlidesProgress:!1,watchSlidesVisibility:!1,grabCursor:!1,preventClicks:!0,preventClicksPropagation:!0,slideToClickedSlide:!1,preloadImages:!0,updateOnImagesReady:!0,loop:!1,loopAdditionalSlides:0,loopedSlides:null,loopFillGroupWithBlank:!1,allowSlidePrev:!0,allowSlideNext:!0,swipeHandler:null,noSwiping:!0,noSwipingClass:"swiper-no-swiping",noSwipingSelector:null,passiveListeners:!0,containerModifierClass:"swiper-container-",slideClass:"swiper-slide",slideBlankClass:"swiper-slide-invisible-blank",slideActiveClass:"swiper-slide-active",slideDuplicateActiveClass:"swiper-slide-duplicate-active",slideVisibleClass:"swiper-slide-visible",slideDuplicateClass:"swiper-slide-duplicate",slideNextClass:"swiper-slide-next",slideDuplicateNextClass:"swiper-slide-duplicate-next",slidePrevClass:"swiper-slide-prev",slideDuplicatePrevClass:"swiper-slide-duplicate-prev",wrapperClass:"swiper-wrapper",runCallbacksOnInit:!0},Y={update:h,translate:p,transition:c,slide:u,loop:v,grabCursor:f,manipulation:L,events:{attachEvents:function(){var t=this.params,i=this.touchEvents,s=this.el,a=this.wrapperEl;this.onTouchStart=D.bind(this),this.onTouchMove=O.bind(this),this.onTouchEnd=A.bind(this),t.cssMode&&(this.onScroll=B.bind(this)),this.onClick=H.bind(this);var r=!!t.nested;if(!o.touch&&o.pointerEvents)s.addEventListener(i.start,this.onTouchStart,!1),e.addEventListener(i.move,this.onTouchMove,r),e.addEventListener(i.end,this.onTouchEnd,!1);else{if(o.touch){var n=!("touchstart"!==i.start||!o.passiveListener||!t.passiveListeners)&&{passive:!0,capture:!1};s.addEventListener(i.start,this.onTouchStart,n),s.addEventListener(i.move,this.onTouchMove,o.passiveListener?{passive:!1,capture:r}:r),s.addEventListener(i.end,this.onTouchEnd,n),i.cancel&&s.addEventListener(i.cancel,this.onTouchEnd,n),N||(e.addEventListener("touchstart",X),N=!0)}(t.simulateTouch&&!I.ios&&!I.android||t.simulateTouch&&!o.touch&&I.ios)&&(s.addEventListener("mousedown",this.onTouchStart,!1),e.addEventListener("mousemove",this.onTouchMove,r),e.addEventListener("mouseup",this.onTouchEnd,!1))}(t.preventClicks||t.preventClicksPropagation)&&s.addEventListener("click",this.onClick,!0),t.cssMode&&a.addEventListener("scroll",this.onScroll),t.updateOnWindowResize?this.on(I.ios||I.android?"resize orientationchange observerUpdate":"resize observerUpdate",G,!0):this.on("observerUpdate",G,!0)},detachEvents:function(){var t=this.params,i=this.touchEvents,s=this.el,a=this.wrapperEl,r=!!t.nested;if(!o.touch&&o.pointerEvents)s.removeEventListener(i.start,this.onTouchStart,!1),e.removeEventListener(i.move,this.onTouchMove,r),e.removeEventListener(i.end,this.onTouchEnd,!1);else{if(o.touch){var n=!("onTouchStart"!==i.start||!o.passiveListener||!t.passiveListeners)&&{passive:!0,capture:!1};s.removeEventListener(i.start,this.onTouchStart,n),s.removeEventListener(i.move,this.onTouchMove,r),s.removeEventListener(i.end,this.onTouchEnd,n),i.cancel&&s.removeEventListener(i.cancel,this.onTouchEnd,n)}(t.simulateTouch&&!I.ios&&!I.android||t.simulateTouch&&!o.touch&&I.ios)&&(s.removeEventListener("mousedown",this.onTouchStart,!1),e.removeEventListener("mousemove",this.onTouchMove,r),e.removeEventListener("mouseup",this.onTouchEnd,!1))}(t.preventClicks||t.preventClicksPropagation)&&s.removeEventListener("click",this.onClick,!0),t.cssMode&&a.removeEventListener("scroll",this.onScroll),this.off(I.ios||I.android?"resize orientationchange observerUpdate":"resize observerUpdate",G)}},breakpoints:{setBreakpoint:function(){var e=this.activeIndex,t=this.initialized,i=this.loopedSlides;void 0===i&&(i=0);var s=this.params,a=this.$el,r=s.breakpoints;if(r&&(!r||0!==Object.keys(r).length)){var o=this.getBreakpoint(r);if(o&&this.currentBreakpoint!==o){var l=o in r?r[o]:void 0;l&&["slidesPerView","spaceBetween","slidesPerGroup","slidesPerGroupSkip","slidesPerColumn"].forEach((function(e){var t=l[e];void 0!==t&&(l[e]="slidesPerView"!==e||"AUTO"!==t&&"auto"!==t?"slidesPerView"===e?parseFloat(t):parseInt(t,10):"auto")}));var d=l||this.originalParams,h=s.slidesPerColumn>1,p=d.slidesPerColumn>1;h&&!p?a.removeClass(s.containerModifierClass+"multirow "+s.containerModifierClass+"multirow-column"):!h&&p&&(a.addClass(s.containerModifierClass+"multirow"),"column"===d.slidesPerColumnFill&&a.addClass(s.containerModifierClass+"multirow-column"));var c=d.direction&&d.direction!==s.direction,u=s.loop&&(d.slidesPerView!==s.slidesPerView||c);c&&t&&this.changeDirection(),n.extend(this.params,d),n.extend(this,{allowTouchMove:this.params.allowTouchMove,allowSlideNext:this.params.allowSlideNext,allowSlidePrev:this.params.allowSlidePrev}),this.currentBreakpoint=o,u&&t&&(this.loopDestroy(),this.loopCreate(),this.updateSlides(),this.slideTo(e-i+this.loopedSlides,0,!1)),this.emit("breakpoint",d)}}},getBreakpoint:function(e){if(e){var i=!1,s=Object.keys(e).map((function(e){if("string"==typeof e&&0===e.indexOf("@")){var i=parseFloat(e.substr(1));return{value:t.innerHeight*i,point:e}}return{value:e,point:e}}));s.sort((function(e,t){return parseInt(e.value,10)-parseInt(t.value,10)}));for(var a=0;a<s.length;a+=1){var r=s[a],n=r.point;r.value<=t.innerWidth&&(i=n)}return i||"max"}}},checkOverflow:{checkOverflow:function(){var e=this.params,t=this.isLocked,i=this.slides.length>0&&e.slidesOffsetBefore+e.spaceBetween*(this.slides.length-1)+this.slides[0].offsetWidth*this.slides.length;e.slidesOffsetBefore&&e.slidesOffsetAfter&&i?this.isLocked=i<=this.size:this.isLocked=1===this.snapGrid.length,this.allowSlideNext=!this.isLocked,this.allowSlidePrev=!this.isLocked,t!==this.isLocked&&this.emit(this.isLocked?"lock":"unlock"),t&&t!==this.isLocked&&(this.isEnd=!1,this.navigation.update())}},classes:{addClasses:function(){var e=this.classNames,t=this.params,i=this.rtl,s=this.$el,a=[];a.push("initialized"),a.push(t.direction),t.freeMode&&a.push("free-mode"),t.autoHeight&&a.push("autoheight"),i&&a.push("rtl"),t.slidesPerColumn>1&&(a.push("multirow"),"column"===t.slidesPerColumnFill&&a.push("multirow-column")),I.android&&a.push("android"),I.ios&&a.push("ios"),t.cssMode&&a.push("css-mode"),a.forEach((function(i){e.push(t.containerModifierClass+i)})),s.addClass(e.join(" "))},removeClasses:function(){var e=this.$el,t=this.classNames;e.removeClass(t.join(" "))}},images:{loadImage:function(e,i,s,a,r,n){var o;function l(){n&&n()}e.complete&&r?l():i?((o=new t.Image).onload=l,o.onerror=l,a&&(o.sizes=a),s&&(o.srcset=s),i&&(o.src=i)):l()},preloadImages:function(){var e=this;function t(){null!=e&&e&&!e.destroyed&&(void 0!==e.imagesLoaded&&(e.imagesLoaded+=1),e.imagesLoaded===e.imagesToLoad.length&&(e.params.updateOnImagesReady&&e.update(),e.emit("imagesReady")))}e.imagesToLoad=e.$el.find("img");for(var i=0;i<e.imagesToLoad.length;i+=1){var s=e.imagesToLoad[i];e.loadImage(s,s.currentSrc||s.getAttribute("src"),s.srcset||s.getAttribute("srcset"),s.sizes||s.getAttribute("sizes"),!0,t)}}}},F={},W=function(e){function t(){for(var i,a,r,l=[],d=arguments.length;d--;)l[d]=arguments[d];1===l.length&&l[0].constructor&&l[0].constructor===Object?r=l[0]:(a=(i=l)[0],r=i[1]),r||(r={}),r=n.extend({},r),a&&!r.el&&(r.el=a),e.call(this,r),Object.keys(Y).forEach((function(e){Object.keys(Y[e]).forEach((function(i){t.prototype[i]||(t.prototype[i]=Y[e][i])}))}));var h=this;void 0===h.modules&&(h.modules={}),Object.keys(h.modules).forEach((function(e){var t=h.modules[e];if(t.params){var i=Object.keys(t.params)[0],s=t.params[i];if("object"!=typeof s||null===s)return;if(!(i in r&&"enabled"in s))return;!0===r[i]&&(r[i]={enabled:!0}),"object"!=typeof r[i]||"enabled"in r[i]||(r[i].enabled=!0),r[i]||(r[i]={enabled:!1})}}));var p=n.extend({},V);h.useModulesParams(p),h.params=n.extend({},p,F,r),h.originalParams=n.extend({},h.params),h.passedParams=n.extend({},r),h.$=s;var c=s(h.params.el);if(a=c[0]){if(c.length>1){var u=[];return c.each((function(e,i){var s=n.extend({},r,{el:i});u.push(new t(s))})),u}var v,f,m;return a.swiper=h,c.data("swiper",h),a&&a.shadowRoot&&a.shadowRoot.querySelector?(v=s(a.shadowRoot.querySelector("."+h.params.wrapperClass))).children=function(e){return c.children(e)}:v=c.children("."+h.params.wrapperClass),n.extend(h,{$el:c,el:a,$wrapperEl:v,wrapperEl:v[0],classNames:[],slides:s(),slidesGrid:[],snapGrid:[],slidesSizesGrid:[],isHorizontal:function(){return"horizontal"===h.params.direction},isVertical:function(){return"vertical"===h.params.direction},rtl:"rtl"===a.dir.toLowerCase()||"rtl"===c.css("direction"),rtlTranslate:"horizontal"===h.params.direction&&("rtl"===a.dir.toLowerCase()||"rtl"===c.css("direction")),wrongRTL:"-webkit-box"===v.css("display"),activeIndex:0,realIndex:0,isBeginning:!0,isEnd:!1,translate:0,previousTranslate:0,progress:0,velocity:0,animating:!1,allowSlideNext:h.params.allowSlideNext,allowSlidePrev:h.params.allowSlidePrev,touchEvents:(f=["touchstart","touchmove","touchend","touchcancel"],m=["mousedown","mousemove","mouseup"],o.pointerEvents&&(m=["pointerdown","pointermove","pointerup"]),h.touchEventsTouch={start:f[0],move:f[1],end:f[2],cancel:f[3]},h.touchEventsDesktop={start:m[0],move:m[1],end:m[2]},o.touch||!h.params.simulateTouch?h.touchEventsTouch:h.touchEventsDesktop),touchEventsData:{isTouched:void 0,isMoved:void 0,allowTouchCallbacks:void 0,touchStartTime:void 0,isScrolling:void 0,currentTranslate:void 0,startTranslate:void 0,allowThresholdMove:void 0,formElements:"input, select, option, textarea, button, video, label",lastClickTime:n.now(),clickTimeout:void 0,velocities:[],allowMomentumBounce:void 0,isTouchEvent:void 0,startMoving:void 0},allowClick:!0,allowTouchMove:h.params.allowTouchMove,touches:{startX:0,startY:0,currentX:0,currentY:0,diff:0},imagesToLoad:[],imagesLoaded:0}),h.useModules(),h.params.init&&h.init(),h}}e&&(t.__proto__=e),t.prototype=Object.create(e&&e.prototype),t.prototype.constructor=t;var i={extendedDefaults:{configurable:!0},defaults:{configurable:!0},Class:{configurable:!0},$:{configurable:!0}};return t.prototype.slidesPerViewDynamic=function(){var e=this.params,t=this.slides,i=this.slidesGrid,s=this.size,a=this.activeIndex,r=1;if(e.centeredSlides){for(var n,o=t[a].swiperSlideSize,l=a+1;l<t.length;l+=1)t[l]&&!n&&(r+=1,(o+=t[l].swiperSlideSize)>s&&(n=!0));for(var d=a-1;d>=0;d-=1)t[d]&&!n&&(r+=1,(o+=t[d].swiperSlideSize)>s&&(n=!0))}else for(var h=a+1;h<t.length;h+=1)i[h]-i[a]<s&&(r+=1);return r},t.prototype.update=function(){var e=this;if(e&&!e.destroyed){var t=e.snapGrid,i=e.params;i.breakpoints&&e.setBreakpoint(),e.updateSize(),e.updateSlides(),e.updateProgress(),e.updateSlidesClasses(),e.params.freeMode?(s(),e.params.autoHeight&&e.updateAutoHeight()):(("auto"===e.params.slidesPerView||e.params.slidesPerView>1)&&e.isEnd&&!e.params.centeredSlides?e.slideTo(e.slides.length-1,0,!1,!0):e.slideTo(e.activeIndex,0,!1,!0))||s(),i.watchOverflow&&t!==e.snapGrid&&e.checkOverflow(),e.emit("update")}function s(){var t=e.rtlTranslate?-1*e.translate:e.translate,i=Math.min(Math.max(t,e.maxTranslate()),e.minTranslate());e.setTranslate(i),e.updateActiveIndex(),e.updateSlidesClasses()}},t.prototype.changeDirection=function(e,t){void 0===t&&(t=!0);var i=this.params.direction;return e||(e="horizontal"===i?"vertical":"horizontal"),e===i||"horizontal"!==e&&"vertical"!==e?this:(this.$el.removeClass(""+this.params.containerModifierClass+i).addClass(""+this.params.containerModifierClass+e),this.params.direction=e,this.slides.each((function(t,i){"vertical"===e?i.style.width="":i.style.height=""})),this.emit("changeDirection"),t&&this.update(),this)},t.prototype.init=function(){this.initialized||(this.emit("beforeInit"),this.params.breakpoints&&this.setBreakpoint(),this.addClasses(),this.params.loop&&this.loopCreate(),this.updateSize(),this.updateSlides(),this.params.watchOverflow&&this.checkOverflow(),this.params.grabCursor&&this.setGrabCursor(),this.params.preloadImages&&this.preloadImages(),this.params.loop?this.slideTo(this.params.initialSlide+this.loopedSlides,0,this.params.runCallbacksOnInit):this.slideTo(this.params.initialSlide,0,this.params.runCallbacksOnInit),this.attachEvents(),this.initialized=!0,this.emit("init"))},t.prototype.destroy=function(e,t){void 0===e&&(e=!0),void 0===t&&(t=!0);var i=this,s=i.params,a=i.$el,r=i.$wrapperEl,o=i.slides;return void 0===i.params||i.destroyed?null:(i.emit("beforeDestroy"),i.initialized=!1,i.detachEvents(),s.loop&&i.loopDestroy(),t&&(i.removeClasses(),a.removeAttr("style"),r.removeAttr("style"),o&&o.length&&o.removeClass([s.slideVisibleClass,s.slideActiveClass,s.slideNextClass,s.slidePrevClass].join(" ")).removeAttr("style").removeAttr("data-swiper-slide-index")),i.emit("destroy"),Object.keys(i.eventsListeners).forEach((function(e){i.off(e)})),!1!==e&&(i.$el[0].swiper=null,i.$el.data("swiper",null),n.deleteProps(i)),i.destroyed=!0,null)},t.extendDefaults=function(e){n.extend(F,e)},i.extendedDefaults.get=function(){return F},i.defaults.get=function(){return V},i.Class.get=function(){return e},i.$.get=function(){return s},Object.defineProperties(t,i),t}(l),R={name:"device",proto:{device:I},static:{device:I}},q={name:"support",proto:{support:o},static:{support:o}},j={isEdge:!!t.navigator.userAgent.match(/Edge/g),isSafari:function(){var e=t.navigator.userAgent.toLowerCase();return e.indexOf("safari")>=0&&e.indexOf("chrome")<0&&e.indexOf("android")<0}(),isUiWebView:/(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(t.navigator.userAgent)},K={name:"browser",proto:{browser:j},static:{browser:j}},U={name:"resize",create:function(){var e=this;n.extend(e,{resize:{resizeHandler:function(){e&&!e.destroyed&&e.initialized&&(e.emit("beforeResize"),e.emit("resize"))},orientationChangeHandler:function(){e&&!e.destroyed&&e.initialized&&e.emit("orientationchange")}}})},on:{init:function(){t.addEventListener("resize",this.resize.resizeHandler),t.addEventListener("orientationchange",this.resize.orientationChangeHandler)},destroy:function(){t.removeEventListener("resize",this.resize.resizeHandler),t.removeEventListener("orientationchange",this.resize.orientationChangeHandler)}}},_={func:t.MutationObserver||t.WebkitMutationObserver,attach:function(e,i){void 0===i&&(i={});var s=this,a=new(0,_.func)((function(e){if(1!==e.length){var i=function(){s.emit("observerUpdate",e[0])};t.requestAnimationFrame?t.requestAnimationFrame(i):t.setTimeout(i,0)}else s.emit("observerUpdate",e[0])}));a.observe(e,{attributes:void 0===i.attributes||i.attributes,childList:void 0===i.childList||i.childList,characterData:void 0===i.characterData||i.characterData}),s.observer.observers.push(a)},init:function(){if(o.observer&&this.params.observer){if(this.params.observeParents)for(var e=this.$el.parents(),t=0;t<e.length;t+=1)this.observer.attach(e[t]);this.observer.attach(this.$el[0],{childList:this.params.observeSlideChildren}),this.observer.attach(this.$wrapperEl[0],{attributes:!1})}},destroy:function(){this.observer.observers.forEach((function(e){e.disconnect()})),this.observer.observers=[]}},Z={name:"observer",params:{observer:!1,observeParents:!1,observeSlideChildren:!1},create:function(){n.extend(this,{observer:{init:_.init.bind(this),attach:_.attach.bind(this),destroy:_.destroy.bind(this),observers:[]}})},on:{init:function(){this.observer.init()},destroy:function(){this.observer.destroy()}}},Q={update:function(e){var t=this,i=t.params,s=i.slidesPerView,a=i.slidesPerGroup,r=i.centeredSlides,o=t.params.virtual,l=o.addSlidesBefore,d=o.addSlidesAfter,h=t.virtual,p=h.from,c=h.to,u=h.slides,v=h.slidesGrid,f=h.renderSlide,m=h.offset;t.updateActiveIndex();var g,b,w,y=t.activeIndex||0;g=t.rtlTranslate?"right":t.isHorizontal()?"left":"top",r?(b=Math.floor(s/2)+a+l,w=Math.floor(s/2)+a+d):(b=s+(a-1)+l,w=a+d);var x=Math.max((y||0)-w,0),T=Math.min((y||0)+b,u.length-1),E=(t.slidesGrid[x]||0)-(t.slidesGrid[0]||0);function S(){t.updateSlides(),t.updateProgress(),t.updateSlidesClasses(),t.lazy&&t.params.lazy.enabled&&t.lazy.load()}if(n.extend(t.virtual,{from:x,to:T,offset:E,slidesGrid:t.slidesGrid}),p===x&&c===T&&!e)return t.slidesGrid!==v&&E!==m&&t.slides.css(g,E+"px"),void t.updateProgress();if(t.params.virtual.renderExternal)return t.params.virtual.renderExternal.call(t,{offset:E,from:x,to:T,slides:function(){for(var e=[],t=x;t<=T;t+=1)e.push(u[t]);return e}()}),void S();var C=[],M=[];if(e)t.$wrapperEl.find("."+t.params.slideClass).remove();else for(var P=p;P<=c;P+=1)(P<x||P>T)&&t.$wrapperEl.find("."+t.params.slideClass+'[data-swiper-slide-index="'+P+'"]').remove();for(var z=0;z<u.length;z+=1)z>=x&&z<=T&&(void 0===c||e?M.push(z):(z>c&&M.push(z),z<p&&C.push(z)));M.forEach((function(e){t.$wrapperEl.append(f(u[e],e))})),C.sort((function(e,t){return t-e})).forEach((function(e){t.$wrapperEl.prepend(f(u[e],e))})),t.$wrapperEl.children(".swiper-slide").css(g,E+"px"),S()},renderSlide:function(e,t){var i=this.params.virtual;if(i.cache&&this.virtual.cache[t])return this.virtual.cache[t];var a=i.renderSlide?s(i.renderSlide.call(this,e,t)):s('<div class="'+this.params.slideClass+'" data-swiper-slide-index="'+t+'">'+e+"</div>");return a.attr("data-swiper-slide-index")||a.attr("data-swiper-slide-index",t),i.cache&&(this.virtual.cache[t]=a),a},appendSlide:function(e){if("object"==typeof e&&"length"in e)for(var t=0;t<e.length;t+=1)e[t]&&this.virtual.slides.push(e[t]);else this.virtual.slides.push(e);this.virtual.update(!0)},prependSlide:function(e){var t=this.activeIndex,i=t+1,s=1;if(Array.isArray(e)){for(var a=0;a<e.length;a+=1)e[a]&&this.virtual.slides.unshift(e[a]);i=t+e.length,s=e.length}else this.virtual.slides.unshift(e);if(this.params.virtual.cache){var r=this.virtual.cache,n={};Object.keys(r).forEach((function(e){var t=r[e],i=t.attr("data-swiper-slide-index");i&&t.attr("data-swiper-slide-index",parseInt(i,10)+1),n[parseInt(e,10)+s]=t})),this.virtual.cache=n}this.virtual.update(!0),this.slideTo(i,0)},removeSlide:function(e){if(null!=e){var t=this.activeIndex;if(Array.isArray(e))for(var i=e.length-1;i>=0;i-=1)this.virtual.slides.splice(e[i],1),this.params.virtual.cache&&delete this.virtual.cache[e[i]],e[i]<t&&(t-=1),t=Math.max(t,0);else this.virtual.slides.splice(e,1),this.params.virtual.cache&&delete this.virtual.cache[e],e<t&&(t-=1),t=Math.max(t,0);this.virtual.update(!0),this.slideTo(t,0)}},removeAllSlides:function(){this.virtual.slides=[],this.params.virtual.cache&&(this.virtual.cache={}),this.virtual.update(!0),this.slideTo(0,0)}},J={name:"virtual",params:{virtual:{enabled:!1,slides:[],cache:!0,renderSlide:null,renderExternal:null,addSlidesBefore:0,addSlidesAfter:0}},create:function(){n.extend(this,{virtual:{update:Q.update.bind(this),appendSlide:Q.appendSlide.bind(this),prependSlide:Q.prependSlide.bind(this),removeSlide:Q.removeSlide.bind(this),removeAllSlides:Q.removeAllSlides.bind(this),renderSlide:Q.renderSlide.bind(this),slides:this.params.virtual.slides,cache:{}}})},on:{beforeInit:function(){if(this.params.virtual.enabled){this.classNames.push(this.params.containerModifierClass+"virtual");var e={watchSlidesProgress:!0};n.extend(this.params,e),n.extend(this.originalParams,e),this.params.initialSlide||this.virtual.update()}},setTranslate:function(){this.params.virtual.enabled&&this.virtual.update()}}},ee={handle:function(i){var s=this.rtlTranslate,a=i;a.originalEvent&&(a=a.originalEvent);var r=a.keyCode||a.charCode;if(!this.allowSlideNext&&(this.isHorizontal()&&39===r||this.isVertical()&&40===r||34===r))return!1;if(!this.allowSlidePrev&&(this.isHorizontal()&&37===r||this.isVertical()&&38===r||33===r))return!1;if(!(a.shiftKey||a.altKey||a.ctrlKey||a.metaKey||e.activeElement&&e.activeElement.nodeName&&("input"===e.activeElement.nodeName.toLowerCase()||"textarea"===e.activeElement.nodeName.toLowerCase()))){if(this.params.keyboard.onlyInViewport&&(33===r||34===r||37===r||39===r||38===r||40===r)){var n=!1;if(this.$el.parents("."+this.params.slideClass).length>0&&0===this.$el.parents("."+this.params.slideActiveClass).length)return;var o=t.innerWidth,l=t.innerHeight,d=this.$el.offset();s&&(d.left-=this.$el[0].scrollLeft);for(var h=[[d.left,d.top],[d.left+this.width,d.top],[d.left,d.top+this.height],[d.left+this.width,d.top+this.height]],p=0;p<h.length;p+=1){var c=h[p];c[0]>=0&&c[0]<=o&&c[1]>=0&&c[1]<=l&&(n=!0)}if(!n)return}this.isHorizontal()?(33!==r&&34!==r&&37!==r&&39!==r||(a.preventDefault?a.preventDefault():a.returnValue=!1),(34!==r&&39!==r||s)&&(33!==r&&37!==r||!s)||this.slideNext(),(33!==r&&37!==r||s)&&(34!==r&&39!==r||!s)||this.slidePrev()):(33!==r&&34!==r&&38!==r&&40!==r||(a.preventDefault?a.preventDefault():a.returnValue=!1),34!==r&&40!==r||this.slideNext(),33!==r&&38!==r||this.slidePrev()),this.emit("keyPress",r)}},enable:function(){this.keyboard.enabled||(s(e).on("keydown",this.keyboard.handle),this.keyboard.enabled=!0)},disable:function(){this.keyboard.enabled&&(s(e).off("keydown",this.keyboard.handle),this.keyboard.enabled=!1)}},te={name:"keyboard",params:{keyboard:{enabled:!1,onlyInViewport:!0}},create:function(){n.extend(this,{keyboard:{enabled:!1,enable:ee.enable.bind(this),disable:ee.disable.bind(this),handle:ee.handle.bind(this)}})},on:{init:function(){this.params.keyboard.enabled&&this.keyboard.enable()},destroy:function(){this.keyboard.enabled&&this.keyboard.disable()}}};var ie={lastScrollTime:n.now(),lastEventBeforeSnap:void 0,recentWheelEvents:[],event:function(){return t.navigator.userAgent.indexOf("firefox")>-1?"DOMMouseScroll":function(){var t="onwheel"in e;if(!t){var i=e.createElement("div");i.setAttribute("onwheel","return;"),t="function"==typeof i.onwheel}return!t&&e.implementation&&e.implementation.hasFeature&&!0!==e.implementation.hasFeature("","")&&(t=e.implementation.hasFeature("Events.wheel","3.0")),t}()?"wheel":"mousewheel"},normalize:function(e){var t=0,i=0,s=0,a=0;return"detail"in e&&(i=e.detail),"wheelDelta"in e&&(i=-e.wheelDelta/120),"wheelDeltaY"in e&&(i=-e.wheelDeltaY/120),"wheelDeltaX"in e&&(t=-e.wheelDeltaX/120),"axis"in e&&e.axis===e.HORIZONTAL_AXIS&&(t=i,i=0),s=10*t,a=10*i,"deltaY"in e&&(a=e.deltaY),"deltaX"in e&&(s=e.deltaX),e.shiftKey&&!s&&(s=a,a=0),(s||a)&&e.deltaMode&&(1===e.deltaMode?(s*=40,a*=40):(s*=800,a*=800)),s&&!t&&(t=s<1?-1:1),a&&!i&&(i=a<1?-1:1),{spinX:t,spinY:i,pixelX:s,pixelY:a}},handleMouseEnter:function(){this.mouseEntered=!0},handleMouseLeave:function(){this.mouseEntered=!1},handle:function(e){var t=e,i=this,a=i.params.mousewheel;i.params.cssMode&&t.preventDefault();var r=i.$el;if("container"!==i.params.mousewheel.eventsTarged&&(r=s(i.params.mousewheel.eventsTarged)),!i.mouseEntered&&!r[0].contains(t.target)&&!a.releaseOnEdges)return!0;t.originalEvent&&(t=t.originalEvent);var o=0,l=i.rtlTranslate?-1:1,d=ie.normalize(t);if(a.forceToAxis)if(i.isHorizontal()){if(!(Math.abs(d.pixelX)>Math.abs(d.pixelY)))return!0;o=d.pixelX*l}else{if(!(Math.abs(d.pixelY)>Math.abs(d.pixelX)))return!0;o=d.pixelY}else o=Math.abs(d.pixelX)>Math.abs(d.pixelY)?-d.pixelX*l:-d.pixelY;if(0===o)return!0;if(a.invert&&(o=-o),i.params.freeMode){var h={time:n.now(),delta:Math.abs(o),direction:Math.sign(o)},p=i.mousewheel.lastEventBeforeSnap,c=p&&h.time<p.time+500&&h.delta<=p.delta&&h.direction===p.direction;if(!c){i.mousewheel.lastEventBeforeSnap=void 0,i.params.loop&&i.loopFix();var u=i.getTranslate()+o*a.sensitivity,v=i.isBeginning,f=i.isEnd;if(u>=i.minTranslate()&&(u=i.minTranslate()),u<=i.maxTranslate()&&(u=i.maxTranslate()),i.setTransition(0),i.setTranslate(u),i.updateProgress(),i.updateActiveIndex(),i.updateSlidesClasses(),(!v&&i.isBeginning||!f&&i.isEnd)&&i.updateSlidesClasses(),i.params.freeModeSticky){clearTimeout(i.mousewheel.timeout),i.mousewheel.timeout=void 0;var m=i.mousewheel.recentWheelEvents;m.length>=15&&m.shift();var g=m.length?m[m.length-1]:void 0,b=m[0];if(m.push(h),g&&(h.delta>g.delta||h.direction!==g.direction))m.splice(0);else if(m.length>=15&&h.time-b.time<500&&b.delta-h.delta>=1&&h.delta<=6){var w=o>0?.8:.2;i.mousewheel.lastEventBeforeSnap=h,m.splice(0),i.mousewheel.timeout=n.nextTick((function(){i.slideToClosest(i.params.speed,!0,void 0,w)}),0)}i.mousewheel.timeout||(i.mousewheel.timeout=n.nextTick((function(){i.mousewheel.lastEventBeforeSnap=h,m.splice(0),i.slideToClosest(i.params.speed,!0,void 0,.5)}),500))}if(c||i.emit("scroll",t),i.params.autoplay&&i.params.autoplayDisableOnInteraction&&i.autoplay.stop(),u===i.minTranslate()||u===i.maxTranslate())return!0}}else{var y={time:n.now(),delta:Math.abs(o),direction:Math.sign(o),raw:e},x=i.mousewheel.recentWheelEvents;x.length>=2&&x.shift();var T=x.length?x[x.length-1]:void 0;if(x.push(y),T?(y.direction!==T.direction||y.delta>T.delta)&&i.mousewheel.animateSlider(y):i.mousewheel.animateSlider(y),i.mousewheel.releaseScroll(y))return!0}return t.preventDefault?t.preventDefault():t.returnValue=!1,!1},animateSlider:function(e){return e.delta>=6&&n.now()-this.mousewheel.lastScrollTime<60||(e.direction<0?this.isEnd&&!this.params.loop||this.animating||(this.slideNext(),this.emit("scroll",e.raw)):this.isBeginning&&!this.params.loop||this.animating||(this.slidePrev(),this.emit("scroll",e.raw)),this.mousewheel.lastScrollTime=(new t.Date).getTime(),!1)},releaseScroll:function(e){var t=this.params.mousewheel;if(e.direction<0){if(this.isEnd&&!this.params.loop&&t.releaseOnEdges)return!0}else if(this.isBeginning&&!this.params.loop&&t.releaseOnEdges)return!0;return!1},enable:function(){var e=ie.event();if(this.params.cssMode)return this.wrapperEl.removeEventListener(e,this.mousewheel.handle),!0;if(!e)return!1;if(this.mousewheel.enabled)return!1;var t=this.$el;return"container"!==this.params.mousewheel.eventsTarged&&(t=s(this.params.mousewheel.eventsTarged)),t.on("mouseenter",this.mousewheel.handleMouseEnter),t.on("mouseleave",this.mousewheel.handleMouseLeave),t.on(e,this.mousewheel.handle),this.mousewheel.enabled=!0,!0},disable:function(){var e=ie.event();if(this.params.cssMode)return this.wrapperEl.addEventListener(e,this.mousewheel.handle),!0;if(!e)return!1;if(!this.mousewheel.enabled)return!1;var t=this.$el;return"container"!==this.params.mousewheel.eventsTarged&&(t=s(this.params.mousewheel.eventsTarged)),t.off(e,this.mousewheel.handle),this.mousewheel.enabled=!1,!0}},se={update:function(){var e=this.params.navigation;if(!this.params.loop){var t=this.navigation,i=t.$nextEl,s=t.$prevEl;s&&s.length>0&&(this.isBeginning?s.addClass(e.disabledClass):s.removeClass(e.disabledClass),s[this.params.watchOverflow&&this.isLocked?"addClass":"removeClass"](e.lockClass)),i&&i.length>0&&(this.isEnd?i.addClass(e.disabledClass):i.removeClass(e.disabledClass),i[this.params.watchOverflow&&this.isLocked?"addClass":"removeClass"](e.lockClass))}},onPrevClick:function(e){e.preventDefault(),this.isBeginning&&!this.params.loop||this.slidePrev()},onNextClick:function(e){e.preventDefault(),this.isEnd&&!this.params.loop||this.slideNext()},init:function(){var e,t,i=this.params.navigation;(i.nextEl||i.prevEl)&&(i.nextEl&&(e=s(i.nextEl),this.params.uniqueNavElements&&"string"==typeof i.nextEl&&e.length>1&&1===this.$el.find(i.nextEl).length&&(e=this.$el.find(i.nextEl))),i.prevEl&&(t=s(i.prevEl),this.params.uniqueNavElements&&"string"==typeof i.prevEl&&t.length>1&&1===this.$el.find(i.prevEl).length&&(t=this.$el.find(i.prevEl))),e&&e.length>0&&e.on("click",this.navigation.onNextClick),t&&t.length>0&&t.on("click",this.navigation.onPrevClick),n.extend(this.navigation,{$nextEl:e,nextEl:e&&e[0],$prevEl:t,prevEl:t&&t[0]}))},destroy:function(){var e=this.navigation,t=e.$nextEl,i=e.$prevEl;t&&t.length&&(t.off("click",this.navigation.onNextClick),t.removeClass(this.params.navigation.disabledClass)),i&&i.length&&(i.off("click",this.navigation.onPrevClick),i.removeClass(this.params.navigation.disabledClass))}},ae={update:function(){var e=this.rtl,t=this.params.pagination;if(t.el&&this.pagination.el&&this.pagination.$el&&0!==this.pagination.$el.length){var i,a=this.virtual&&this.params.virtual.enabled?this.virtual.slides.length:this.slides.length,r=this.pagination.$el,n=this.params.loop?Math.ceil((a-2*this.loopedSlides)/this.params.slidesPerGroup):this.snapGrid.length;if(this.params.loop?((i=Math.ceil((this.activeIndex-this.loopedSlides)/this.params.slidesPerGroup))>a-1-2*this.loopedSlides&&(i-=a-2*this.loopedSlides),i>n-1&&(i-=n),i<0&&"bullets"!==this.params.paginationType&&(i=n+i)):i=void 0!==this.snapIndex?this.snapIndex:this.activeIndex||0,"bullets"===t.type&&this.pagination.bullets&&this.pagination.bullets.length>0){var o,l,d,h=this.pagination.bullets;if(t.dynamicBullets&&(this.pagination.bulletSize=h.eq(0)[this.isHorizontal()?"outerWidth":"outerHeight"](!0),r.css(this.isHorizontal()?"width":"height",this.pagination.bulletSize*(t.dynamicMainBullets+4)+"px"),t.dynamicMainBullets>1&&void 0!==this.previousIndex&&(this.pagination.dynamicBulletIndex+=i-this.previousIndex,this.pagination.dynamicBulletIndex>t.dynamicMainBullets-1?this.pagination.dynamicBulletIndex=t.dynamicMainBullets-1:this.pagination.dynamicBulletIndex<0&&(this.pagination.dynamicBulletIndex=0)),o=i-this.pagination.dynamicBulletIndex,d=((l=o+(Math.min(h.length,t.dynamicMainBullets)-1))+o)/2),h.removeClass(t.bulletActiveClass+" "+t.bulletActiveClass+"-next "+t.bulletActiveClass+"-next-next "+t.bulletActiveClass+"-prev "+t.bulletActiveClass+"-prev-prev "+t.bulletActiveClass+"-main"),r.length>1)h.each((function(e,a){var r=s(a),n=r.index();n===i&&r.addClass(t.bulletActiveClass),t.dynamicBullets&&(n>=o&&n<=l&&r.addClass(t.bulletActiveClass+"-main"),n===o&&r.prev().addClass(t.bulletActiveClass+"-prev").prev().addClass(t.bulletActiveClass+"-prev-prev"),n===l&&r.next().addClass(t.bulletActiveClass+"-next").next().addClass(t.bulletActiveClass+"-next-next"))}));else{var p=h.eq(i),c=p.index();if(p.addClass(t.bulletActiveClass),t.dynamicBullets){for(var u=h.eq(o),v=h.eq(l),f=o;f<=l;f+=1)h.eq(f).addClass(t.bulletActiveClass+"-main");if(this.params.loop)if(c>=h.length-t.dynamicMainBullets){for(var m=t.dynamicMainBullets;m>=0;m-=1)h.eq(h.length-m).addClass(t.bulletActiveClass+"-main");h.eq(h.length-t.dynamicMainBullets-1).addClass(t.bulletActiveClass+"-prev")}else u.prev().addClass(t.bulletActiveClass+"-prev").prev().addClass(t.bulletActiveClass+"-prev-prev"),v.next().addClass(t.bulletActiveClass+"-next").next().addClass(t.bulletActiveClass+"-next-next");else u.prev().addClass(t.bulletActiveClass+"-prev").prev().addClass(t.bulletActiveClass+"-prev-prev"),v.next().addClass(t.bulletActiveClass+"-next").next().addClass(t.bulletActiveClass+"-next-next")}}if(t.dynamicBullets){var g=Math.min(h.length,t.dynamicMainBullets+4),b=(this.pagination.bulletSize*g-this.pagination.bulletSize)/2-d*this.pagination.bulletSize,w=e?"right":"left";h.css(this.isHorizontal()?w:"top",b+"px")}}if("fraction"===t.type&&(r.find("."+t.currentClass).text(t.formatFractionCurrent(i+1)),r.find("."+t.totalClass).text(t.formatFractionTotal(n))),"progressbar"===t.type){var y;y=t.progressbarOpposite?this.isHorizontal()?"vertical":"horizontal":this.isHorizontal()?"horizontal":"vertical";var x=(i+1)/n,T=1,E=1;"horizontal"===y?T=x:E=x,r.find("."+t.progressbarFillClass).transform("translate3d(0,0,0) scaleX("+T+") scaleY("+E+")").transition(this.params.speed)}"custom"===t.type&&t.renderCustom?(r.html(t.renderCustom(this,i+1,n)),this.emit("paginationRender",this,r[0])):this.emit("paginationUpdate",this,r[0]),r[this.params.watchOverflow&&this.isLocked?"addClass":"removeClass"](t.lockClass)}},render:function(){var e=this.params.pagination;if(e.el&&this.pagination.el&&this.pagination.$el&&0!==this.pagination.$el.length){var t=this.virtual&&this.params.virtual.enabled?this.virtual.slides.length:this.slides.length,i=this.pagination.$el,s="";if("bullets"===e.type){for(var a=this.params.loop?Math.ceil((t-2*this.loopedSlides)/this.params.slidesPerGroup):this.snapGrid.length,r=0;r<a;r+=1)e.renderBullet?s+=e.renderBullet.call(this,r,e.bulletClass):s+="<"+e.bulletElement+' class="'+e.bulletClass+'"></'+e.bulletElement+">";i.html(s),this.pagination.bullets=i.find("."+e.bulletClass)}"fraction"===e.type&&(s=e.renderFraction?e.renderFraction.call(this,e.currentClass,e.totalClass):'<span class="'+e.currentClass+'"></span> / <span class="'+e.totalClass+'"></span>',i.html(s)),"progressbar"===e.type&&(s=e.renderProgressbar?e.renderProgressbar.call(this,e.progressbarFillClass):'<span class="'+e.progressbarFillClass+'"></span>',i.html(s)),"custom"!==e.type&&this.emit("paginationRender",this.pagination.$el[0])}},init:function(){var e=this,t=e.params.pagination;if(t.el){var i=s(t.el);0!==i.length&&(e.params.uniqueNavElements&&"string"==typeof t.el&&i.length>1&&1===e.$el.find(t.el).length&&(i=e.$el.find(t.el)),"bullets"===t.type&&t.clickable&&i.addClass(t.clickableClass),i.addClass(t.modifierClass+t.type),"bullets"===t.type&&t.dynamicBullets&&(i.addClass(""+t.modifierClass+t.type+"-dynamic"),e.pagination.dynamicBulletIndex=0,t.dynamicMainBullets<1&&(t.dynamicMainBullets=1)),"progressbar"===t.type&&t.progressbarOpposite&&i.addClass(t.progressbarOppositeClass),t.clickable&&i.on("click","."+t.bulletClass,(function(t){t.preventDefault();var i=s(this).index()*e.params.slidesPerGroup;e.params.loop&&(i+=e.loopedSlides),e.slideTo(i)})),n.extend(e.pagination,{$el:i,el:i[0]}))}},destroy:function(){var e=this.params.pagination;if(e.el&&this.pagination.el&&this.pagination.$el&&0!==this.pagination.$el.length){var t=this.pagination.$el;t.removeClass(e.hiddenClass),t.removeClass(e.modifierClass+e.type),this.pagination.bullets&&this.pagination.bullets.removeClass(e.bulletActiveClass),e.clickable&&t.off("click","."+e.bulletClass)}}},re={setTranslate:function(){if(this.params.scrollbar.el&&this.scrollbar.el){var e=this.scrollbar,t=this.rtlTranslate,i=this.progress,s=e.dragSize,a=e.trackSize,r=e.$dragEl,n=e.$el,o=this.params.scrollbar,l=s,d=(a-s)*i;t?(d=-d)>0?(l=s-d,d=0):-d+s>a&&(l=a+d):d<0?(l=s+d,d=0):d+s>a&&(l=a-d),this.isHorizontal()?(r.transform("translate3d("+d+"px, 0, 0)"),r[0].style.width=l+"px"):(r.transform("translate3d(0px, "+d+"px, 0)"),r[0].style.height=l+"px"),o.hide&&(clearTimeout(this.scrollbar.timeout),n[0].style.opacity=1,this.scrollbar.timeout=setTimeout((function(){n[0].style.opacity=0,n.transition(400)}),1e3))}},setTransition:function(e){this.params.scrollbar.el&&this.scrollbar.el&&this.scrollbar.$dragEl.transition(e)},updateSize:function(){if(this.params.scrollbar.el&&this.scrollbar.el){var e=this.scrollbar,t=e.$dragEl,i=e.$el;t[0].style.width="",t[0].style.height="";var s,a=this.isHorizontal()?i[0].offsetWidth:i[0].offsetHeight,r=this.size/this.virtualSize,o=r*(a/this.size);s="auto"===this.params.scrollbar.dragSize?a*r:parseInt(this.params.scrollbar.dragSize,10),this.isHorizontal()?t[0].style.width=s+"px":t[0].style.height=s+"px",i[0].style.display=r>=1?"none":"",this.params.scrollbar.hide&&(i[0].style.opacity=0),n.extend(e,{trackSize:a,divider:r,moveDivider:o,dragSize:s}),e.$el[this.params.watchOverflow&&this.isLocked?"addClass":"removeClass"](this.params.scrollbar.lockClass)}},getPointerPosition:function(e){return this.isHorizontal()?"touchstart"===e.type||"touchmove"===e.type?e.targetTouches[0].clientX:e.clientX:"touchstart"===e.type||"touchmove"===e.type?e.targetTouches[0].clientY:e.clientY},setDragPosition:function(e){var t,i=this.scrollbar,s=this.rtlTranslate,a=i.$el,r=i.dragSize,n=i.trackSize,o=i.dragStartPos;t=(i.getPointerPosition(e)-a.offset()[this.isHorizontal()?"left":"top"]-(null!==o?o:r/2))/(n-r),t=Math.max(Math.min(t,1),0),s&&(t=1-t);var l=this.minTranslate()+(this.maxTranslate()-this.minTranslate())*t;this.updateProgress(l),this.setTranslate(l),this.updateActiveIndex(),this.updateSlidesClasses()},onDragStart:function(e){var t=this.params.scrollbar,i=this.scrollbar,s=this.$wrapperEl,a=i.$el,r=i.$dragEl;this.scrollbar.isTouched=!0,this.scrollbar.dragStartPos=e.target===r[0]||e.target===r?i.getPointerPosition(e)-e.target.getBoundingClientRect()[this.isHorizontal()?"left":"top"]:null,e.preventDefault(),e.stopPropagation(),s.transition(100),r.transition(100),i.setDragPosition(e),clearTimeout(this.scrollbar.dragTimeout),a.transition(0),t.hide&&a.css("opacity",1),this.params.cssMode&&this.$wrapperEl.css("scroll-snap-type","none"),this.emit("scrollbarDragStart",e)},onDragMove:function(e){var t=this.scrollbar,i=this.$wrapperEl,s=t.$el,a=t.$dragEl;this.scrollbar.isTouched&&(e.preventDefault?e.preventDefault():e.returnValue=!1,t.setDragPosition(e),i.transition(0),s.transition(0),a.transition(0),this.emit("scrollbarDragMove",e))},onDragEnd:function(e){var t=this.params.scrollbar,i=this.scrollbar,s=this.$wrapperEl,a=i.$el;this.scrollbar.isTouched&&(this.scrollbar.isTouched=!1,this.params.cssMode&&(this.$wrapperEl.css("scroll-snap-type",""),s.transition("")),t.hide&&(clearTimeout(this.scrollbar.dragTimeout),this.scrollbar.dragTimeout=n.nextTick((function(){a.css("opacity",0),a.transition(400)}),1e3)),this.emit("scrollbarDragEnd",e),t.snapOnRelease&&this.slideToClosest())},enableDraggable:function(){if(this.params.scrollbar.el){var t=this.scrollbar,i=this.touchEventsTouch,s=this.touchEventsDesktop,a=this.params,r=t.$el[0],n=!(!o.passiveListener||!a.passiveListeners)&&{passive:!1,capture:!1},l=!(!o.passiveListener||!a.passiveListeners)&&{passive:!0,capture:!1};o.touch?(r.addEventListener(i.start,this.scrollbar.onDragStart,n),r.addEventListener(i.move,this.scrollbar.onDragMove,n),r.addEventListener(i.end,this.scrollbar.onDragEnd,l)):(r.addEventListener(s.start,this.scrollbar.onDragStart,n),e.addEventListener(s.move,this.scrollbar.onDragMove,n),e.addEventListener(s.end,this.scrollbar.onDragEnd,l))}},disableDraggable:function(){if(this.params.scrollbar.el){var t=this.scrollbar,i=this.touchEventsTouch,s=this.touchEventsDesktop,a=this.params,r=t.$el[0],n=!(!o.passiveListener||!a.passiveListeners)&&{passive:!1,capture:!1},l=!(!o.passiveListener||!a.passiveListeners)&&{passive:!0,capture:!1};o.touch?(r.removeEventListener(i.start,this.scrollbar.onDragStart,n),r.removeEventListener(i.move,this.scrollbar.onDragMove,n),r.removeEventListener(i.end,this.scrollbar.onDragEnd,l)):(r.removeEventListener(s.start,this.scrollbar.onDragStart,n),e.removeEventListener(s.move,this.scrollbar.onDragMove,n),e.removeEventListener(s.end,this.scrollbar.onDragEnd,l))}},init:function(){if(this.params.scrollbar.el){var e=this.scrollbar,t=this.$el,i=this.params.scrollbar,a=s(i.el);this.params.uniqueNavElements&&"string"==typeof i.el&&a.length>1&&1===t.find(i.el).length&&(a=t.find(i.el));var r=a.find("."+this.params.scrollbar.dragClass);0===r.length&&(r=s('<div class="'+this.params.scrollbar.dragClass+'"></div>'),a.append(r)),n.extend(e,{$el:a,el:a[0],$dragEl:r,dragEl:r[0]}),i.draggable&&e.enableDraggable()}},destroy:function(){this.scrollbar.disableDraggable()}},ne={setTransform:function(e,t){var i=this.rtl,a=s(e),r=i?-1:1,n=a.attr("data-swiper-parallax")||"0",o=a.attr("data-swiper-parallax-x"),l=a.attr("data-swiper-parallax-y"),d=a.attr("data-swiper-parallax-scale"),h=a.attr("data-swiper-parallax-opacity");if(o||l?(o=o||"0",l=l||"0"):this.isHorizontal()?(o=n,l="0"):(l=n,o="0"),o=o.indexOf("%")>=0?parseInt(o,10)*t*r+"%":o*t*r+"px",l=l.indexOf("%")>=0?parseInt(l,10)*t+"%":l*t+"px",null!=h){var p=h-(h-1)*(1-Math.abs(t));a[0].style.opacity=p}if(null==d)a.transform("translate3d("+o+", "+l+", 0px)");else{var c=d-(d-1)*(1-Math.abs(t));a.transform("translate3d("+o+", "+l+", 0px) scale("+c+")")}},setTranslate:function(){var e=this,t=e.$el,i=e.slides,a=e.progress,r=e.snapGrid;t.children("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y], [data-swiper-parallax-opacity], [data-swiper-parallax-scale]").each((function(t,i){e.parallax.setTransform(i,a)})),i.each((function(t,i){var n=i.progress;e.params.slidesPerGroup>1&&"auto"!==e.params.slidesPerView&&(n+=Math.ceil(t/2)-a*(r.length-1)),n=Math.min(Math.max(n,-1),1),s(i).find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y], [data-swiper-parallax-opacity], [data-swiper-parallax-scale]").each((function(t,i){e.parallax.setTransform(i,n)}))}))},setTransition:function(e){void 0===e&&(e=this.params.speed);this.$el.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y], [data-swiper-parallax-opacity], [data-swiper-parallax-scale]").each((function(t,i){var a=s(i),r=parseInt(a.attr("data-swiper-parallax-duration"),10)||e;0===e&&(r=0),a.transition(r)}))}},oe={getDistanceBetweenTouches:function(e){if(e.targetTouches.length<2)return 1;var t=e.targetTouches[0].pageX,i=e.targetTouches[0].pageY,s=e.targetTouches[1].pageX,a=e.targetTouches[1].pageY;return Math.sqrt(Math.pow(s-t,2)+Math.pow(a-i,2))},onGestureStart:function(e){var t=this.params.zoom,i=this.zoom,a=i.gesture;if(i.fakeGestureTouched=!1,i.fakeGestureMoved=!1,!o.gestures){if("touchstart"!==e.type||"touchstart"===e.type&&e.targetTouches.length<2)return;i.fakeGestureTouched=!0,a.scaleStart=oe.getDistanceBetweenTouches(e)}a.$slideEl&&a.$slideEl.length||(a.$slideEl=s(e.target).closest("."+this.params.slideClass),0===a.$slideEl.length&&(a.$slideEl=this.slides.eq(this.activeIndex)),a.$imageEl=a.$slideEl.find("img, svg, canvas, picture, .swiper-zoom-target"),a.$imageWrapEl=a.$imageEl.parent("."+t.containerClass),a.maxRatio=a.$imageWrapEl.attr("data-swiper-zoom")||t.maxRatio,0!==a.$imageWrapEl.length)?(a.$imageEl.transition(0),this.zoom.isScaling=!0):a.$imageEl=void 0},onGestureChange:function(e){var t=this.params.zoom,i=this.zoom,s=i.gesture;if(!o.gestures){if("touchmove"!==e.type||"touchmove"===e.type&&e.targetTouches.length<2)return;i.fakeGestureMoved=!0,s.scaleMove=oe.getDistanceBetweenTouches(e)}s.$imageEl&&0!==s.$imageEl.length&&(o.gestures?i.scale=e.scale*i.currentScale:i.scale=s.scaleMove/s.scaleStart*i.currentScale,i.scale>s.maxRatio&&(i.scale=s.maxRatio-1+Math.pow(i.scale-s.maxRatio+1,.5)),i.scale<t.minRatio&&(i.scale=t.minRatio+1-Math.pow(t.minRatio-i.scale+1,.5)),s.$imageEl.transform("translate3d(0,0,0) scale("+i.scale+")"))},onGestureEnd:function(e){var t=this.params.zoom,i=this.zoom,s=i.gesture;if(!o.gestures){if(!i.fakeGestureTouched||!i.fakeGestureMoved)return;if("touchend"!==e.type||"touchend"===e.type&&e.changedTouches.length<2&&!I.android)return;i.fakeGestureTouched=!1,i.fakeGestureMoved=!1}s.$imageEl&&0!==s.$imageEl.length&&(i.scale=Math.max(Math.min(i.scale,s.maxRatio),t.minRatio),s.$imageEl.transition(this.params.speed).transform("translate3d(0,0,0) scale("+i.scale+")"),i.currentScale=i.scale,i.isScaling=!1,1===i.scale&&(s.$slideEl=void 0))},onTouchStart:function(e){var t=this.zoom,i=t.gesture,s=t.image;i.$imageEl&&0!==i.$imageEl.length&&(s.isTouched||(I.android&&e.preventDefault(),s.isTouched=!0,s.touchesStart.x="touchstart"===e.type?e.targetTouches[0].pageX:e.pageX,s.touchesStart.y="touchstart"===e.type?e.targetTouches[0].pageY:e.pageY))},onTouchMove:function(e){var t=this.zoom,i=t.gesture,s=t.image,a=t.velocity;if(i.$imageEl&&0!==i.$imageEl.length&&(this.allowClick=!1,s.isTouched&&i.$slideEl)){s.isMoved||(s.width=i.$imageEl[0].offsetWidth,s.height=i.$imageEl[0].offsetHeight,s.startX=n.getTranslate(i.$imageWrapEl[0],"x")||0,s.startY=n.getTranslate(i.$imageWrapEl[0],"y")||0,i.slideWidth=i.$slideEl[0].offsetWidth,i.slideHeight=i.$slideEl[0].offsetHeight,i.$imageWrapEl.transition(0),this.rtl&&(s.startX=-s.startX,s.startY=-s.startY));var r=s.width*t.scale,o=s.height*t.scale;if(!(r<i.slideWidth&&o<i.slideHeight)){if(s.minX=Math.min(i.slideWidth/2-r/2,0),s.maxX=-s.minX,s.minY=Math.min(i.slideHeight/2-o/2,0),s.maxY=-s.minY,s.touchesCurrent.x="touchmove"===e.type?e.targetTouches[0].pageX:e.pageX,s.touchesCurrent.y="touchmove"===e.type?e.targetTouches[0].pageY:e.pageY,!s.isMoved&&!t.isScaling){if(this.isHorizontal()&&(Math.floor(s.minX)===Math.floor(s.startX)&&s.touchesCurrent.x<s.touchesStart.x||Math.floor(s.maxX)===Math.floor(s.startX)&&s.touchesCurrent.x>s.touchesStart.x))return void(s.isTouched=!1);if(!this.isHorizontal()&&(Math.floor(s.minY)===Math.floor(s.startY)&&s.touchesCurrent.y<s.touchesStart.y||Math.floor(s.maxY)===Math.floor(s.startY)&&s.touchesCurrent.y>s.touchesStart.y))return void(s.isTouched=!1)}e.preventDefault(),e.stopPropagation(),s.isMoved=!0,s.currentX=s.touchesCurrent.x-s.touchesStart.x+s.startX,s.currentY=s.touchesCurrent.y-s.touchesStart.y+s.startY,s.currentX<s.minX&&(s.currentX=s.minX+1-Math.pow(s.minX-s.currentX+1,.8)),s.currentX>s.maxX&&(s.currentX=s.maxX-1+Math.pow(s.currentX-s.maxX+1,.8)),s.currentY<s.minY&&(s.currentY=s.minY+1-Math.pow(s.minY-s.currentY+1,.8)),s.currentY>s.maxY&&(s.currentY=s.maxY-1+Math.pow(s.currentY-s.maxY+1,.8)),a.prevPositionX||(a.prevPositionX=s.touchesCurrent.x),a.prevPositionY||(a.prevPositionY=s.touchesCurrent.y),a.prevTime||(a.prevTime=Date.now()),a.x=(s.touchesCurrent.x-a.prevPositionX)/(Date.now()-a.prevTime)/2,a.y=(s.touchesCurrent.y-a.prevPositionY)/(Date.now()-a.prevTime)/2,Math.abs(s.touchesCurrent.x-a.prevPositionX)<2&&(a.x=0),Math.abs(s.touchesCurrent.y-a.prevPositionY)<2&&(a.y=0),a.prevPositionX=s.touchesCurrent.x,a.prevPositionY=s.touchesCurrent.y,a.prevTime=Date.now(),i.$imageWrapEl.transform("translate3d("+s.currentX+"px, "+s.currentY+"px,0)")}}},onTouchEnd:function(){var e=this.zoom,t=e.gesture,i=e.image,s=e.velocity;if(t.$imageEl&&0!==t.$imageEl.length){if(!i.isTouched||!i.isMoved)return i.isTouched=!1,void(i.isMoved=!1);i.isTouched=!1,i.isMoved=!1;var a=300,r=300,n=s.x*a,o=i.currentX+n,l=s.y*r,d=i.currentY+l;0!==s.x&&(a=Math.abs((o-i.currentX)/s.x)),0!==s.y&&(r=Math.abs((d-i.currentY)/s.y));var h=Math.max(a,r);i.currentX=o,i.currentY=d;var p=i.width*e.scale,c=i.height*e.scale;i.minX=Math.min(t.slideWidth/2-p/2,0),i.maxX=-i.minX,i.minY=Math.min(t.slideHeight/2-c/2,0),i.maxY=-i.minY,i.currentX=Math.max(Math.min(i.currentX,i.maxX),i.minX),i.currentY=Math.max(Math.min(i.currentY,i.maxY),i.minY),t.$imageWrapEl.transition(h).transform("translate3d("+i.currentX+"px, "+i.currentY+"px,0)")}},onTransitionEnd:function(){var e=this.zoom,t=e.gesture;t.$slideEl&&this.previousIndex!==this.activeIndex&&(t.$imageEl.transform("translate3d(0,0,0) scale(1)"),t.$imageWrapEl.transform("translate3d(0,0,0)"),e.scale=1,e.currentScale=1,t.$slideEl=void 0,t.$imageEl=void 0,t.$imageWrapEl=void 0)},toggle:function(e){var t=this.zoom;t.scale&&1!==t.scale?t.out():t.in(e)},in:function(e){var t,i,s,a,r,n,o,l,d,h,p,c,u,v,f,m,g=this.zoom,b=this.params.zoom,w=g.gesture,y=g.image;(w.$slideEl||(w.$slideEl=this.slides.eq(this.activeIndex),w.$imageEl=w.$slideEl.find("img, svg, canvas, picture, .swiper-zoom-target"),w.$imageWrapEl=w.$imageEl.parent("."+b.containerClass)),w.$imageEl&&0!==w.$imageEl.length)&&(w.$slideEl.addClass(""+b.zoomedSlideClass),void 0===y.touchesStart.x&&e?(t="touchend"===e.type?e.changedTouches[0].pageX:e.pageX,i="touchend"===e.type?e.changedTouches[0].pageY:e.pageY):(t=y.touchesStart.x,i=y.touchesStart.y),g.scale=w.$imageWrapEl.attr("data-swiper-zoom")||b.maxRatio,g.currentScale=w.$imageWrapEl.attr("data-swiper-zoom")||b.maxRatio,e?(f=w.$slideEl[0].offsetWidth,m=w.$slideEl[0].offsetHeight,s=w.$slideEl.offset().left+f/2-t,a=w.$slideEl.offset().top+m/2-i,o=w.$imageEl[0].offsetWidth,l=w.$imageEl[0].offsetHeight,d=o*g.scale,h=l*g.scale,u=-(p=Math.min(f/2-d/2,0)),v=-(c=Math.min(m/2-h/2,0)),(r=s*g.scale)<p&&(r=p),r>u&&(r=u),(n=a*g.scale)<c&&(n=c),n>v&&(n=v)):(r=0,n=0),w.$imageWrapEl.transition(300).transform("translate3d("+r+"px, "+n+"px,0)"),w.$imageEl.transition(300).transform("translate3d(0,0,0) scale("+g.scale+")"))},out:function(){var e=this.zoom,t=this.params.zoom,i=e.gesture;i.$slideEl||(i.$slideEl=this.slides.eq(this.activeIndex),i.$imageEl=i.$slideEl.find("img, svg, canvas, picture, .swiper-zoom-target"),i.$imageWrapEl=i.$imageEl.parent("."+t.containerClass)),i.$imageEl&&0!==i.$imageEl.length&&(e.scale=1,e.currentScale=1,i.$imageWrapEl.transition(300).transform("translate3d(0,0,0)"),i.$imageEl.transition(300).transform("translate3d(0,0,0) scale(1)"),i.$slideEl.removeClass(""+t.zoomedSlideClass),i.$slideEl=void 0)},enable:function(){var e=this.zoom;if(!e.enabled){e.enabled=!0;var t=!("touchstart"!==this.touchEvents.start||!o.passiveListener||!this.params.passiveListeners)&&{passive:!0,capture:!1},i=!o.passiveListener||{passive:!1,capture:!0},s="."+this.params.slideClass;o.gestures?(this.$wrapperEl.on("gesturestart",s,e.onGestureStart,t),this.$wrapperEl.on("gesturechange",s,e.onGestureChange,t),this.$wrapperEl.on("gestureend",s,e.onGestureEnd,t)):"touchstart"===this.touchEvents.start&&(this.$wrapperEl.on(this.touchEvents.start,s,e.onGestureStart,t),this.$wrapperEl.on(this.touchEvents.move,s,e.onGestureChange,i),this.$wrapperEl.on(this.touchEvents.end,s,e.onGestureEnd,t),this.touchEvents.cancel&&this.$wrapperEl.on(this.touchEvents.cancel,s,e.onGestureEnd,t)),this.$wrapperEl.on(this.touchEvents.move,"."+this.params.zoom.containerClass,e.onTouchMove,i)}},disable:function(){var e=this.zoom;if(e.enabled){this.zoom.enabled=!1;var t=!("touchstart"!==this.touchEvents.start||!o.passiveListener||!this.params.passiveListeners)&&{passive:!0,capture:!1},i=!o.passiveListener||{passive:!1,capture:!0},s="."+this.params.slideClass;o.gestures?(this.$wrapperEl.off("gesturestart",s,e.onGestureStart,t),this.$wrapperEl.off("gesturechange",s,e.onGestureChange,t),this.$wrapperEl.off("gestureend",s,e.onGestureEnd,t)):"touchstart"===this.touchEvents.start&&(this.$wrapperEl.off(this.touchEvents.start,s,e.onGestureStart,t),this.$wrapperEl.off(this.touchEvents.move,s,e.onGestureChange,i),this.$wrapperEl.off(this.touchEvents.end,s,e.onGestureEnd,t),this.touchEvents.cancel&&this.$wrapperEl.off(this.touchEvents.cancel,s,e.onGestureEnd,t)),this.$wrapperEl.off(this.touchEvents.move,"."+this.params.zoom.containerClass,e.onTouchMove,i)}}},le={loadInSlide:function(e,t){void 0===t&&(t=!0);var i=this,a=i.params.lazy;if(void 0!==e&&0!==i.slides.length){var r=i.virtual&&i.params.virtual.enabled?i.$wrapperEl.children("."+i.params.slideClass+'[data-swiper-slide-index="'+e+'"]'):i.slides.eq(e),n=r.find("."+a.elementClass+":not(."+a.loadedClass+"):not(."+a.loadingClass+")");!r.hasClass(a.elementClass)||r.hasClass(a.loadedClass)||r.hasClass(a.loadingClass)||(n=n.add(r[0])),0!==n.length&&n.each((function(e,n){var o=s(n);o.addClass(a.loadingClass);var l=o.attr("data-background"),d=o.attr("data-src"),h=o.attr("data-srcset"),p=o.attr("data-sizes");i.loadImage(o[0],d||l,h,p,!1,(function(){if(null!=i&&i&&(!i||i.params)&&!i.destroyed){if(l?(o.css("background-image",'url("'+l+'")'),o.removeAttr("data-background")):(h&&(o.attr("srcset",h),o.removeAttr("data-srcset")),p&&(o.attr("sizes",p),o.removeAttr("data-sizes")),d&&(o.attr("src",d),o.removeAttr("data-src"))),o.addClass(a.loadedClass).removeClass(a.loadingClass),r.find("."+a.preloaderClass).remove(),i.params.loop&&t){var e=r.attr("data-swiper-slide-index");if(r.hasClass(i.params.slideDuplicateClass)){var s=i.$wrapperEl.children('[data-swiper-slide-index="'+e+'"]:not(.'+i.params.slideDuplicateClass+")");i.lazy.loadInSlide(s.index(),!1)}else{var n=i.$wrapperEl.children("."+i.params.slideDuplicateClass+'[data-swiper-slide-index="'+e+'"]');i.lazy.loadInSlide(n.index(),!1)}}i.emit("lazyImageReady",r[0],o[0]),i.params.autoHeight&&i.updateAutoHeight()}})),i.emit("lazyImageLoad",r[0],o[0])}))}},load:function(){var e=this,t=e.$wrapperEl,i=e.params,a=e.slides,r=e.activeIndex,n=e.virtual&&i.virtual.enabled,o=i.lazy,l=i.slidesPerView;function d(e){if(n){if(t.children("."+i.slideClass+'[data-swiper-slide-index="'+e+'"]').length)return!0}else if(a[e])return!0;return!1}function h(e){return n?s(e).attr("data-swiper-slide-index"):s(e).index()}if("auto"===l&&(l=0),e.lazy.initialImageLoaded||(e.lazy.initialImageLoaded=!0),e.params.watchSlidesVisibility)t.children("."+i.slideVisibleClass).each((function(t,i){var a=n?s(i).attr("data-swiper-slide-index"):s(i).index();e.lazy.loadInSlide(a)}));else if(l>1)for(var p=r;p<r+l;p+=1)d(p)&&e.lazy.loadInSlide(p);else e.lazy.loadInSlide(r);if(o.loadPrevNext)if(l>1||o.loadPrevNextAmount&&o.loadPrevNextAmount>1){for(var c=o.loadPrevNextAmount,u=l,v=Math.min(r+u+Math.max(c,u),a.length),f=Math.max(r-Math.max(u,c),0),m=r+l;m<v;m+=1)d(m)&&e.lazy.loadInSlide(m);for(var g=f;g<r;g+=1)d(g)&&e.lazy.loadInSlide(g)}else{var b=t.children("."+i.slideNextClass);b.length>0&&e.lazy.loadInSlide(h(b));var w=t.children("."+i.slidePrevClass);w.length>0&&e.lazy.loadInSlide(h(w))}}},de={LinearSpline:function(e,t){var i,s,a,r,n,o=function(e,t){for(s=-1,i=e.length;i-s>1;)e[a=i+s>>1]<=t?s=a:i=a;return i};return this.x=e,this.y=t,this.lastIndex=e.length-1,this.interpolate=function(e){return e?(n=o(this.x,e),r=n-1,(e-this.x[r])*(this.y[n]-this.y[r])/(this.x[n]-this.x[r])+this.y[r]):0},this},getInterpolateFunction:function(e){this.controller.spline||(this.controller.spline=this.params.loop?new de.LinearSpline(this.slidesGrid,e.slidesGrid):new de.LinearSpline(this.snapGrid,e.snapGrid))},setTranslate:function(e,t){var i,s,a=this,r=a.controller.control;function n(e){var t=a.rtlTranslate?-a.translate:a.translate;"slide"===a.params.controller.by&&(a.controller.getInterpolateFunction(e),s=-a.controller.spline.interpolate(-t)),s&&"container"!==a.params.controller.by||(i=(e.maxTranslate()-e.minTranslate())/(a.maxTranslate()-a.minTranslate()),s=(t-a.minTranslate())*i+e.minTranslate()),a.params.controller.inverse&&(s=e.maxTranslate()-s),e.updateProgress(s),e.setTranslate(s,a),e.updateActiveIndex(),e.updateSlidesClasses()}if(Array.isArray(r))for(var o=0;o<r.length;o+=1)r[o]!==t&&r[o]instanceof W&&n(r[o]);else r instanceof W&&t!==r&&n(r)},setTransition:function(e,t){var i,s=this,a=s.controller.control;function r(t){t.setTransition(e,s),0!==e&&(t.transitionStart(),t.params.autoHeight&&n.nextTick((function(){t.updateAutoHeight()})),t.$wrapperEl.transitionEnd((function(){a&&(t.params.loop&&"slide"===s.params.controller.by&&t.loopFix(),t.transitionEnd())})))}if(Array.isArray(a))for(i=0;i<a.length;i+=1)a[i]!==t&&a[i]instanceof W&&r(a[i]);else a instanceof W&&t!==a&&r(a)}},he={makeElFocusable:function(e){return e.attr("tabIndex","0"),e},addElRole:function(e,t){return e.attr("role",t),e},addElLabel:function(e,t){return e.attr("aria-label",t),e},disableEl:function(e){return e.attr("aria-disabled",!0),e},enableEl:function(e){return e.attr("aria-disabled",!1),e},onEnterKey:function(e){var t=this.params.a11y;if(13===e.keyCode){var i=s(e.target);this.navigation&&this.navigation.$nextEl&&i.is(this.navigation.$nextEl)&&(this.isEnd&&!this.params.loop||this.slideNext(),this.isEnd?this.a11y.notify(t.lastSlideMessage):this.a11y.notify(t.nextSlideMessage)),this.navigation&&this.navigation.$prevEl&&i.is(this.navigation.$prevEl)&&(this.isBeginning&&!this.params.loop||this.slidePrev(),this.isBeginning?this.a11y.notify(t.firstSlideMessage):this.a11y.notify(t.prevSlideMessage)),this.pagination&&i.is("."+this.params.pagination.bulletClass)&&i[0].click()}},notify:function(e){var t=this.a11y.liveRegion;0!==t.length&&(t.html(""),t.html(e))},updateNavigation:function(){if(!this.params.loop&&this.navigation){var e=this.navigation,t=e.$nextEl,i=e.$prevEl;i&&i.length>0&&(this.isBeginning?this.a11y.disableEl(i):this.a11y.enableEl(i)),t&&t.length>0&&(this.isEnd?this.a11y.disableEl(t):this.a11y.enableEl(t))}},updatePagination:function(){var e=this,t=e.params.a11y;e.pagination&&e.params.pagination.clickable&&e.pagination.bullets&&e.pagination.bullets.length&&e.pagination.bullets.each((function(i,a){var r=s(a);e.a11y.makeElFocusable(r),e.a11y.addElRole(r,"button"),e.a11y.addElLabel(r,t.paginationBulletMessage.replace(/{{index}}/,r.index()+1))}))},init:function(){this.$el.append(this.a11y.liveRegion);var e,t,i=this.params.a11y;this.navigation&&this.navigation.$nextEl&&(e=this.navigation.$nextEl),this.navigation&&this.navigation.$prevEl&&(t=this.navigation.$prevEl),e&&(this.a11y.makeElFocusable(e),this.a11y.addElRole(e,"button"),this.a11y.addElLabel(e,i.nextSlideMessage),e.on("keydown",this.a11y.onEnterKey)),t&&(this.a11y.makeElFocusable(t),this.a11y.addElRole(t,"button"),this.a11y.addElLabel(t,i.prevSlideMessage),t.on("keydown",this.a11y.onEnterKey)),this.pagination&&this.params.pagination.clickable&&this.pagination.bullets&&this.pagination.bullets.length&&this.pagination.$el.on("keydown","."+this.params.pagination.bulletClass,this.a11y.onEnterKey)},destroy:function(){var e,t;this.a11y.liveRegion&&this.a11y.liveRegion.length>0&&this.a11y.liveRegion.remove(),this.navigation&&this.navigation.$nextEl&&(e=this.navigation.$nextEl),this.navigation&&this.navigation.$prevEl&&(t=this.navigation.$prevEl),e&&e.off("keydown",this.a11y.onEnterKey),t&&t.off("keydown",this.a11y.onEnterKey),this.pagination&&this.params.pagination.clickable&&this.pagination.bullets&&this.pagination.bullets.length&&this.pagination.$el.off("keydown","."+this.params.pagination.bulletClass,this.a11y.onEnterKey)}},pe={init:function(){if(this.params.history){if(!t.history||!t.history.pushState)return this.params.history.enabled=!1,void(this.params.hashNavigation.enabled=!0);var e=this.history;e.initialized=!0,e.paths=pe.getPathValues(),(e.paths.key||e.paths.value)&&(e.scrollToSlide(0,e.paths.value,this.params.runCallbacksOnInit),this.params.history.replaceState||t.addEventListener("popstate",this.history.setHistoryPopState))}},destroy:function(){this.params.history.replaceState||t.removeEventListener("popstate",this.history.setHistoryPopState)},setHistoryPopState:function(){this.history.paths=pe.getPathValues(),this.history.scrollToSlide(this.params.speed,this.history.paths.value,!1)},getPathValues:function(){var e=t.location.pathname.slice(1).split("/").filter((function(e){return""!==e})),i=e.length;return{key:e[i-2],value:e[i-1]}},setHistory:function(e,i){if(this.history.initialized&&this.params.history.enabled){var s=this.slides.eq(i),a=pe.slugify(s.attr("data-history"));t.location.pathname.includes(e)||(a=e+"/"+a);var r=t.history.state;r&&r.value===a||(this.params.history.replaceState?t.history.replaceState({value:a},null,a):t.history.pushState({value:a},null,a))}},slugify:function(e){return e.toString().replace(/\s+/g,"-").replace(/[^\w-]+/g,"").replace(/--+/g,"-").replace(/^-+/,"").replace(/-+$/,"")},scrollToSlide:function(e,t,i){if(t)for(var s=0,a=this.slides.length;s<a;s+=1){var r=this.slides.eq(s);if(pe.slugify(r.attr("data-history"))===t&&!r.hasClass(this.params.slideDuplicateClass)){var n=r.index();this.slideTo(n,e,i)}}else this.slideTo(0,e,i)}},ce={onHashCange:function(){var t=e.location.hash.replace("#","");if(t!==this.slides.eq(this.activeIndex).attr("data-hash")){var i=this.$wrapperEl.children("."+this.params.slideClass+'[data-hash="'+t+'"]').index();if(void 0===i)return;this.slideTo(i)}},setHash:function(){if(this.hashNavigation.initialized&&this.params.hashNavigation.enabled)if(this.params.hashNavigation.replaceState&&t.history&&t.history.replaceState)t.history.replaceState(null,null,"#"+this.slides.eq(this.activeIndex).attr("data-hash")||"");else{var i=this.slides.eq(this.activeIndex),s=i.attr("data-hash")||i.attr("data-history");e.location.hash=s||""}},init:function(){if(!(!this.params.hashNavigation.enabled||this.params.history&&this.params.history.enabled)){this.hashNavigation.initialized=!0;var i=e.location.hash.replace("#","");if(i)for(var a=0,r=this.slides.length;a<r;a+=1){var n=this.slides.eq(a);if((n.attr("data-hash")||n.attr("data-history"))===i&&!n.hasClass(this.params.slideDuplicateClass)){var o=n.index();this.slideTo(o,0,this.params.runCallbacksOnInit,!0)}}this.params.hashNavigation.watchState&&s(t).on("hashchange",this.hashNavigation.onHashCange)}},destroy:function(){this.params.hashNavigation.watchState&&s(t).off("hashchange",this.hashNavigation.onHashCange)}},ue={run:function(){var e=this,t=e.slides.eq(e.activeIndex),i=e.params.autoplay.delay;t.attr("data-swiper-autoplay")&&(i=t.attr("data-swiper-autoplay")||e.params.autoplay.delay),clearTimeout(e.autoplay.timeout),e.autoplay.timeout=n.nextTick((function(){e.params.autoplay.reverseDirection?e.params.loop?(e.loopFix(),e.slidePrev(e.params.speed,!0,!0),e.emit("autoplay")):e.isBeginning?e.params.autoplay.stopOnLastSlide?e.autoplay.stop():(e.slideTo(e.slides.length-1,e.params.speed,!0,!0),e.emit("autoplay")):(e.slidePrev(e.params.speed,!0,!0),e.emit("autoplay")):e.params.loop?(e.loopFix(),e.slideNext(e.params.speed,!0,!0),e.emit("autoplay")):e.isEnd?e.params.autoplay.stopOnLastSlide?e.autoplay.stop():(e.slideTo(0,e.params.speed,!0,!0),e.emit("autoplay")):(e.slideNext(e.params.speed,!0,!0),e.emit("autoplay")),e.params.cssMode&&e.autoplay.running&&e.autoplay.run()}),i)},start:function(){return void 0===this.autoplay.timeout&&(!this.autoplay.running&&(this.autoplay.running=!0,this.emit("autoplayStart"),this.autoplay.run(),!0))},stop:function(){return!!this.autoplay.running&&(void 0!==this.autoplay.timeout&&(this.autoplay.timeout&&(clearTimeout(this.autoplay.timeout),this.autoplay.timeout=void 0),this.autoplay.running=!1,this.emit("autoplayStop"),!0))},pause:function(e){this.autoplay.running&&(this.autoplay.paused||(this.autoplay.timeout&&clearTimeout(this.autoplay.timeout),this.autoplay.paused=!0,0!==e&&this.params.autoplay.waitForTransition?(this.$wrapperEl[0].addEventListener("transitionend",this.autoplay.onTransitionEnd),this.$wrapperEl[0].addEventListener("webkitTransitionEnd",this.autoplay.onTransitionEnd)):(this.autoplay.paused=!1,this.autoplay.run())))}},ve={setTranslate:function(){for(var e=this.slides,t=0;t<e.length;t+=1){var i=this.slides.eq(t),s=-i[0].swiperSlideOffset;this.params.virtualTranslate||(s-=this.translate);var a=0;this.isHorizontal()||(a=s,s=0);var r=this.params.fadeEffect.crossFade?Math.max(1-Math.abs(i[0].progress),0):1+Math.min(Math.max(i[0].progress,-1),0);i.css({opacity:r}).transform("translate3d("+s+"px, "+a+"px, 0px)")}},setTransition:function(e){var t=this,i=t.slides,s=t.$wrapperEl;if(i.transition(e),t.params.virtualTranslate&&0!==e){var a=!1;i.transitionEnd((function(){if(!a&&t&&!t.destroyed){a=!0,t.animating=!1;for(var e=["webkitTransitionEnd","transitionend"],i=0;i<e.length;i+=1)s.trigger(e[i])}}))}}},fe={setTranslate:function(){var e,t=this.$el,i=this.$wrapperEl,a=this.slides,r=this.width,n=this.height,o=this.rtlTranslate,l=this.size,d=this.params.cubeEffect,h=this.isHorizontal(),p=this.virtual&&this.params.virtual.enabled,c=0;d.shadow&&(h?(0===(e=i.find(".swiper-cube-shadow")).length&&(e=s('<div class="swiper-cube-shadow"></div>'),i.append(e)),e.css({height:r+"px"})):0===(e=t.find(".swiper-cube-shadow")).length&&(e=s('<div class="swiper-cube-shadow"></div>'),t.append(e)));for(var u=0;u<a.length;u+=1){var v=a.eq(u),f=u;p&&(f=parseInt(v.attr("data-swiper-slide-index"),10));var m=90*f,g=Math.floor(m/360);o&&(m=-m,g=Math.floor(-m/360));var b=Math.max(Math.min(v[0].progress,1),-1),w=0,y=0,x=0;f%4==0?(w=4*-g*l,x=0):(f-1)%4==0?(w=0,x=4*-g*l):(f-2)%4==0?(w=l+4*g*l,x=l):(f-3)%4==0&&(w=-l,x=3*l+4*l*g),o&&(w=-w),h||(y=w,w=0);var T="rotateX("+(h?0:-m)+"deg) rotateY("+(h?m:0)+"deg) translate3d("+w+"px, "+y+"px, "+x+"px)";if(b<=1&&b>-1&&(c=90*f+90*b,o&&(c=90*-f-90*b)),v.transform(T),d.slideShadows){var E=h?v.find(".swiper-slide-shadow-left"):v.find(".swiper-slide-shadow-top"),S=h?v.find(".swiper-slide-shadow-right"):v.find(".swiper-slide-shadow-bottom");0===E.length&&(E=s('<div class="swiper-slide-shadow-'+(h?"left":"top")+'"></div>'),v.append(E)),0===S.length&&(S=s('<div class="swiper-slide-shadow-'+(h?"right":"bottom")+'"></div>'),v.append(S)),E.length&&(E[0].style.opacity=Math.max(-b,0)),S.length&&(S[0].style.opacity=Math.max(b,0))}}if(i.css({"-webkit-transform-origin":"50% 50% -"+l/2+"px","-moz-transform-origin":"50% 50% -"+l/2+"px","-ms-transform-origin":"50% 50% -"+l/2+"px","transform-origin":"50% 50% -"+l/2+"px"}),d.shadow)if(h)e.transform("translate3d(0px, "+(r/2+d.shadowOffset)+"px, "+-r/2+"px) rotateX(90deg) rotateZ(0deg) scale("+d.shadowScale+")");else{var C=Math.abs(c)-90*Math.floor(Math.abs(c)/90),M=1.5-(Math.sin(2*C*Math.PI/360)/2+Math.cos(2*C*Math.PI/360)/2),P=d.shadowScale,z=d.shadowScale/M,k=d.shadowOffset;e.transform("scale3d("+P+", 1, "+z+") translate3d(0px, "+(n/2+k)+"px, "+-n/2/z+"px) rotateX(-90deg)")}var $=j.isSafari||j.isUiWebView?-l/2:0;i.transform("translate3d(0px,0,"+$+"px) rotateX("+(this.isHorizontal()?0:c)+"deg) rotateY("+(this.isHorizontal()?-c:0)+"deg)")},setTransition:function(e){var t=this.$el;this.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e),this.params.cubeEffect.shadow&&!this.isHorizontal()&&t.find(".swiper-cube-shadow").transition(e)}},me={setTranslate:function(){for(var e=this.slides,t=this.rtlTranslate,i=0;i<e.length;i+=1){var a=e.eq(i),r=a[0].progress;this.params.flipEffect.limitRotation&&(r=Math.max(Math.min(a[0].progress,1),-1));var n=-180*r,o=0,l=-a[0].swiperSlideOffset,d=0;if(this.isHorizontal()?t&&(n=-n):(d=l,l=0,o=-n,n=0),a[0].style.zIndex=-Math.abs(Math.round(r))+e.length,this.params.flipEffect.slideShadows){var h=this.isHorizontal()?a.find(".swiper-slide-shadow-left"):a.find(".swiper-slide-shadow-top"),p=this.isHorizontal()?a.find(".swiper-slide-shadow-right"):a.find(".swiper-slide-shadow-bottom");0===h.length&&(h=s('<div class="swiper-slide-shadow-'+(this.isHorizontal()?"left":"top")+'"></div>'),a.append(h)),0===p.length&&(p=s('<div class="swiper-slide-shadow-'+(this.isHorizontal()?"right":"bottom")+'"></div>'),a.append(p)),h.length&&(h[0].style.opacity=Math.max(-r,0)),p.length&&(p[0].style.opacity=Math.max(r,0))}a.transform("translate3d("+l+"px, "+d+"px, 0px) rotateX("+o+"deg) rotateY("+n+"deg)")}},setTransition:function(e){var t=this,i=t.slides,s=t.activeIndex,a=t.$wrapperEl;if(i.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e),t.params.virtualTranslate&&0!==e){var r=!1;i.eq(s).transitionEnd((function(){if(!r&&t&&!t.destroyed){r=!0,t.animating=!1;for(var e=["webkitTransitionEnd","transitionend"],i=0;i<e.length;i+=1)a.trigger(e[i])}}))}}},ge={setTranslate:function(){for(var e=this.width,t=this.height,i=this.slides,a=this.$wrapperEl,r=this.slidesSizesGrid,n=this.params.coverflowEffect,l=this.isHorizontal(),d=this.translate,h=l?e/2-d:t/2-d,p=l?n.rotate:-n.rotate,c=n.depth,u=0,v=i.length;u<v;u+=1){var f=i.eq(u),m=r[u],g=(h-f[0].swiperSlideOffset-m/2)/m*n.modifier,b=l?p*g:0,w=l?0:p*g,y=-c*Math.abs(g),x=n.stretch;"string"==typeof x&&-1!==x.indexOf("%")&&(x=parseFloat(n.stretch)/100*m);var T=l?0:x*g,E=l?x*g:0;Math.abs(E)<.001&&(E=0),Math.abs(T)<.001&&(T=0),Math.abs(y)<.001&&(y=0),Math.abs(b)<.001&&(b=0),Math.abs(w)<.001&&(w=0);var S="translate3d("+E+"px,"+T+"px,"+y+"px)  rotateX("+w+"deg) rotateY("+b+"deg)";if(f.transform(S),f[0].style.zIndex=1-Math.abs(Math.round(g)),n.slideShadows){var C=l?f.find(".swiper-slide-shadow-left"):f.find(".swiper-slide-shadow-top"),M=l?f.find(".swiper-slide-shadow-right"):f.find(".swiper-slide-shadow-bottom");0===C.length&&(C=s('<div class="swiper-slide-shadow-'+(l?"left":"top")+'"></div>'),f.append(C)),0===M.length&&(M=s('<div class="swiper-slide-shadow-'+(l?"right":"bottom")+'"></div>'),f.append(M)),C.length&&(C[0].style.opacity=g>0?g:0),M.length&&(M[0].style.opacity=-g>0?-g:0)}}(o.pointerEvents||o.prefixedPointerEvents)&&(a[0].style.perspectiveOrigin=h+"px 50%")},setTransition:function(e){this.slides.transition(e).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(e)}},be={init:function(){var e=this.params.thumbs,t=this.constructor;e.swiper instanceof t?(this.thumbs.swiper=e.swiper,n.extend(this.thumbs.swiper.originalParams,{watchSlidesProgress:!0,slideToClickedSlide:!1}),n.extend(this.thumbs.swiper.params,{watchSlidesProgress:!0,slideToClickedSlide:!1})):n.isObject(e.swiper)&&(this.thumbs.swiper=new t(n.extend({},e.swiper,{watchSlidesVisibility:!0,watchSlidesProgress:!0,slideToClickedSlide:!1})),this.thumbs.swiperCreated=!0),this.thumbs.swiper.$el.addClass(this.params.thumbs.thumbsContainerClass),this.thumbs.swiper.on("tap",this.thumbs.onThumbClick)},onThumbClick:function(){var e=this.thumbs.swiper;if(e){var t=e.clickedIndex,i=e.clickedSlide;if(!(i&&s(i).hasClass(this.params.thumbs.slideThumbActiveClass)||null==t)){var a;if(a=e.params.loop?parseInt(s(e.clickedSlide).attr("data-swiper-slide-index"),10):t,this.params.loop){var r=this.activeIndex;this.slides.eq(r).hasClass(this.params.slideDuplicateClass)&&(this.loopFix(),this._clientLeft=this.$wrapperEl[0].clientLeft,r=this.activeIndex);var n=this.slides.eq(r).prevAll('[data-swiper-slide-index="'+a+'"]').eq(0).index(),o=this.slides.eq(r).nextAll('[data-swiper-slide-index="'+a+'"]').eq(0).index();a=void 0===n?o:void 0===o?n:o-r<r-n?o:n}this.slideTo(a)}}},update:function(e){var t=this.thumbs.swiper;if(t){var i="auto"===t.params.slidesPerView?t.slidesPerViewDynamic():t.params.slidesPerView;if(this.realIndex!==t.realIndex){var s,a=t.activeIndex;if(t.params.loop){t.slides.eq(a).hasClass(t.params.slideDuplicateClass)&&(t.loopFix(),t._clientLeft=t.$wrapperEl[0].clientLeft,a=t.activeIndex);var r=t.slides.eq(a).prevAll('[data-swiper-slide-index="'+this.realIndex+'"]').eq(0).index(),n=t.slides.eq(a).nextAll('[data-swiper-slide-index="'+this.realIndex+'"]').eq(0).index();s=void 0===r?n:void 0===n?r:n-a==a-r?a:n-a<a-r?n:r}else s=this.realIndex;t.visibleSlidesIndexes&&t.visibleSlidesIndexes.indexOf(s)<0&&(t.params.centeredSlides?s=s>a?s-Math.floor(i/2)+1:s+Math.floor(i/2)-1:s>a&&(s=s-i+1),t.slideTo(s,e?0:void 0))}var o=1,l=this.params.thumbs.slideThumbActiveClass;if(this.params.slidesPerView>1&&!this.params.centeredSlides&&(o=this.params.slidesPerView),this.params.thumbs.multipleActiveThumbs||(o=1),o=Math.floor(o),t.slides.removeClass(l),t.params.loop||t.params.virtual&&t.params.virtual.enabled)for(var d=0;d<o;d+=1)t.$wrapperEl.children('[data-swiper-slide-index="'+(this.realIndex+d)+'"]').addClass(l);else for(var h=0;h<o;h+=1)t.slides.eq(this.realIndex+h).addClass(l)}}},we=[R,q,K,U,Z,J,te,{name:"mousewheel",params:{mousewheel:{enabled:!1,releaseOnEdges:!1,invert:!1,forceToAxis:!1,sensitivity:1,eventsTarged:"container"}},create:function(){n.extend(this,{mousewheel:{enabled:!1,enable:ie.enable.bind(this),disable:ie.disable.bind(this),handle:ie.handle.bind(this),handleMouseEnter:ie.handleMouseEnter.bind(this),handleMouseLeave:ie.handleMouseLeave.bind(this),animateSlider:ie.animateSlider.bind(this),releaseScroll:ie.releaseScroll.bind(this),lastScrollTime:n.now(),lastEventBeforeSnap:void 0,recentWheelEvents:[]}})},on:{init:function(){!this.params.mousewheel.enabled&&this.params.cssMode&&this.mousewheel.disable(),this.params.mousewheel.enabled&&this.mousewheel.enable()},destroy:function(){this.params.cssMode&&this.mousewheel.enable(),this.mousewheel.enabled&&this.mousewheel.disable()}}},{name:"navigation",params:{navigation:{nextEl:null,prevEl:null,hideOnClick:!1,disabledClass:"swiper-button-disabled",hiddenClass:"swiper-button-hidden",lockClass:"swiper-button-lock"}},create:function(){n.extend(this,{navigation:{init:se.init.bind(this),update:se.update.bind(this),destroy:se.destroy.bind(this),onNextClick:se.onNextClick.bind(this),onPrevClick:se.onPrevClick.bind(this)}})},on:{init:function(){this.navigation.init(),this.navigation.update()},toEdge:function(){this.navigation.update()},fromEdge:function(){this.navigation.update()},destroy:function(){this.navigation.destroy()},click:function(e){var t,i=this.navigation,a=i.$nextEl,r=i.$prevEl;!this.params.navigation.hideOnClick||s(e.target).is(r)||s(e.target).is(a)||(a?t=a.hasClass(this.params.navigation.hiddenClass):r&&(t=r.hasClass(this.params.navigation.hiddenClass)),!0===t?this.emit("navigationShow",this):this.emit("navigationHide",this),a&&a.toggleClass(this.params.navigation.hiddenClass),r&&r.toggleClass(this.params.navigation.hiddenClass))}}},{name:"pagination",params:{pagination:{el:null,bulletElement:"span",clickable:!1,hideOnClick:!1,renderBullet:null,renderProgressbar:null,renderFraction:null,renderCustom:null,progressbarOpposite:!1,type:"bullets",dynamicBullets:!1,dynamicMainBullets:1,formatFractionCurrent:function(e){return e},formatFractionTotal:function(e){return e},bulletClass:"swiper-pagination-bullet",bulletActiveClass:"swiper-pagination-bullet-active",modifierClass:"swiper-pagination-",currentClass:"swiper-pagination-current",totalClass:"swiper-pagination-total",hiddenClass:"swiper-pagination-hidden",progressbarFillClass:"swiper-pagination-progressbar-fill",progressbarOppositeClass:"swiper-pagination-progressbar-opposite",clickableClass:"swiper-pagination-clickable",lockClass:"swiper-pagination-lock"}},create:function(){n.extend(this,{pagination:{init:ae.init.bind(this),render:ae.render.bind(this),update:ae.update.bind(this),destroy:ae.destroy.bind(this),dynamicBulletIndex:0}})},on:{init:function(){this.pagination.init(),this.pagination.render(),this.pagination.update()},activeIndexChange:function(){this.params.loop?this.pagination.update():void 0===this.snapIndex&&this.pagination.update()},snapIndexChange:function(){this.params.loop||this.pagination.update()},slidesLengthChange:function(){this.params.loop&&(this.pagination.render(),this.pagination.update())},snapGridLengthChange:function(){this.params.loop||(this.pagination.render(),this.pagination.update())},destroy:function(){this.pagination.destroy()},click:function(e){this.params.pagination.el&&this.params.pagination.hideOnClick&&this.pagination.$el.length>0&&!s(e.target).hasClass(this.params.pagination.bulletClass)&&(!0===this.pagination.$el.hasClass(this.params.pagination.hiddenClass)?this.emit("paginationShow",this):this.emit("paginationHide",this),this.pagination.$el.toggleClass(this.params.pagination.hiddenClass))}}},{name:"scrollbar",params:{scrollbar:{el:null,dragSize:"auto",hide:!1,draggable:!1,snapOnRelease:!0,lockClass:"swiper-scrollbar-lock",dragClass:"swiper-scrollbar-drag"}},create:function(){n.extend(this,{scrollbar:{init:re.init.bind(this),destroy:re.destroy.bind(this),updateSize:re.updateSize.bind(this),setTranslate:re.setTranslate.bind(this),setTransition:re.setTransition.bind(this),enableDraggable:re.enableDraggable.bind(this),disableDraggable:re.disableDraggable.bind(this),setDragPosition:re.setDragPosition.bind(this),getPointerPosition:re.getPointerPosition.bind(this),onDragStart:re.onDragStart.bind(this),onDragMove:re.onDragMove.bind(this),onDragEnd:re.onDragEnd.bind(this),isTouched:!1,timeout:null,dragTimeout:null}})},on:{init:function(){this.scrollbar.init(),this.scrollbar.updateSize(),this.scrollbar.setTranslate()},update:function(){this.scrollbar.updateSize()},resize:function(){this.scrollbar.updateSize()},observerUpdate:function(){this.scrollbar.updateSize()},setTranslate:function(){this.scrollbar.setTranslate()},setTransition:function(e){this.scrollbar.setTransition(e)},destroy:function(){this.scrollbar.destroy()}}},{name:"parallax",params:{parallax:{enabled:!1}},create:function(){n.extend(this,{parallax:{setTransform:ne.setTransform.bind(this),setTranslate:ne.setTranslate.bind(this),setTransition:ne.setTransition.bind(this)}})},on:{beforeInit:function(){this.params.parallax.enabled&&(this.params.watchSlidesProgress=!0,this.originalParams.watchSlidesProgress=!0)},init:function(){this.params.parallax.enabled&&this.parallax.setTranslate()},setTranslate:function(){this.params.parallax.enabled&&this.parallax.setTranslate()},setTransition:function(e){this.params.parallax.enabled&&this.parallax.setTransition(e)}}},{name:"zoom",params:{zoom:{enabled:!1,maxRatio:3,minRatio:1,toggle:!0,containerClass:"swiper-zoom-container",zoomedSlideClass:"swiper-slide-zoomed"}},create:function(){var e=this,t={enabled:!1,scale:1,currentScale:1,isScaling:!1,gesture:{$slideEl:void 0,slideWidth:void 0,slideHeight:void 0,$imageEl:void 0,$imageWrapEl:void 0,maxRatio:3},image:{isTouched:void 0,isMoved:void 0,currentX:void 0,currentY:void 0,minX:void 0,minY:void 0,maxX:void 0,maxY:void 0,width:void 0,height:void 0,startX:void 0,startY:void 0,touchesStart:{},touchesCurrent:{}},velocity:{x:void 0,y:void 0,prevPositionX:void 0,prevPositionY:void 0,prevTime:void 0}};"onGestureStart onGestureChange onGestureEnd onTouchStart onTouchMove onTouchEnd onTransitionEnd toggle enable disable in out".split(" ").forEach((function(i){t[i]=oe[i].bind(e)})),n.extend(e,{zoom:t});var i=1;Object.defineProperty(e.zoom,"scale",{get:function(){return i},set:function(t){if(i!==t){var s=e.zoom.gesture.$imageEl?e.zoom.gesture.$imageEl[0]:void 0,a=e.zoom.gesture.$slideEl?e.zoom.gesture.$slideEl[0]:void 0;e.emit("zoomChange",t,s,a)}i=t}})},on:{init:function(){this.params.zoom.enabled&&this.zoom.enable()},destroy:function(){this.zoom.disable()},touchStart:function(e){this.zoom.enabled&&this.zoom.onTouchStart(e)},touchEnd:function(e){this.zoom.enabled&&this.zoom.onTouchEnd(e)},doubleTap:function(e){this.params.zoom.enabled&&this.zoom.enabled&&this.params.zoom.toggle&&this.zoom.toggle(e)},transitionEnd:function(){this.zoom.enabled&&this.params.zoom.enabled&&this.zoom.onTransitionEnd()},slideChange:function(){this.zoom.enabled&&this.params.zoom.enabled&&this.params.cssMode&&this.zoom.onTransitionEnd()}}},{name:"lazy",params:{lazy:{enabled:!1,loadPrevNext:!1,loadPrevNextAmount:1,loadOnTransitionStart:!1,elementClass:"swiper-lazy",loadingClass:"swiper-lazy-loading",loadedClass:"swiper-lazy-loaded",preloaderClass:"swiper-lazy-preloader"}},create:function(){n.extend(this,{lazy:{initialImageLoaded:!1,load:le.load.bind(this),loadInSlide:le.loadInSlide.bind(this)}})},on:{beforeInit:function(){this.params.lazy.enabled&&this.params.preloadImages&&(this.params.preloadImages=!1)},init:function(){this.params.lazy.enabled&&!this.params.loop&&0===this.params.initialSlide&&this.lazy.load()},scroll:function(){this.params.freeMode&&!this.params.freeModeSticky&&this.lazy.load()},resize:function(){this.params.lazy.enabled&&this.lazy.load()},scrollbarDragMove:function(){this.params.lazy.enabled&&this.lazy.load()},transitionStart:function(){this.params.lazy.enabled&&(this.params.lazy.loadOnTransitionStart||!this.params.lazy.loadOnTransitionStart&&!this.lazy.initialImageLoaded)&&this.lazy.load()},transitionEnd:function(){this.params.lazy.enabled&&!this.params.lazy.loadOnTransitionStart&&this.lazy.load()},slideChange:function(){this.params.lazy.enabled&&this.params.cssMode&&this.lazy.load()}}},{name:"controller",params:{controller:{control:void 0,inverse:!1,by:"slide"}},create:function(){n.extend(this,{controller:{control:this.params.controller.control,getInterpolateFunction:de.getInterpolateFunction.bind(this),setTranslate:de.setTranslate.bind(this),setTransition:de.setTransition.bind(this)}})},on:{update:function(){this.controller.control&&this.controller.spline&&(this.controller.spline=void 0,delete this.controller.spline)},resize:function(){this.controller.control&&this.controller.spline&&(this.controller.spline=void 0,delete this.controller.spline)},observerUpdate:function(){this.controller.control&&this.controller.spline&&(this.controller.spline=void 0,delete this.controller.spline)},setTranslate:function(e,t){this.controller.control&&this.controller.setTranslate(e,t)},setTransition:function(e,t){this.controller.control&&this.controller.setTransition(e,t)}}},{name:"a11y",params:{a11y:{enabled:!0,notificationClass:"swiper-notification",prevSlideMessage:"Previous slide",nextSlideMessage:"Next slide",firstSlideMessage:"This is the first slide",lastSlideMessage:"This is the last slide",paginationBulletMessage:"Go to slide {{index}}"}},create:function(){var e=this;n.extend(e,{a11y:{liveRegion:s('<span class="'+e.params.a11y.notificationClass+'" aria-live="assertive" aria-atomic="true"></span>')}}),Object.keys(he).forEach((function(t){e.a11y[t]=he[t].bind(e)}))},on:{init:function(){this.params.a11y.enabled&&(this.a11y.init(),this.a11y.updateNavigation())},toEdge:function(){this.params.a11y.enabled&&this.a11y.updateNavigation()},fromEdge:function(){this.params.a11y.enabled&&this.a11y.updateNavigation()},paginationUpdate:function(){this.params.a11y.enabled&&this.a11y.updatePagination()},destroy:function(){this.params.a11y.enabled&&this.a11y.destroy()}}},{name:"history",params:{history:{enabled:!1,replaceState:!1,key:"slides"}},create:function(){n.extend(this,{history:{init:pe.init.bind(this),setHistory:pe.setHistory.bind(this),setHistoryPopState:pe.setHistoryPopState.bind(this),scrollToSlide:pe.scrollToSlide.bind(this),destroy:pe.destroy.bind(this)}})},on:{init:function(){this.params.history.enabled&&this.history.init()},destroy:function(){this.params.history.enabled&&this.history.destroy()},transitionEnd:function(){this.history.initialized&&this.history.setHistory(this.params.history.key,this.activeIndex)},slideChange:function(){this.history.initialized&&this.params.cssMode&&this.history.setHistory(this.params.history.key,this.activeIndex)}}},{name:"hash-navigation",params:{hashNavigation:{enabled:!1,replaceState:!1,watchState:!1}},create:function(){n.extend(this,{hashNavigation:{initialized:!1,init:ce.init.bind(this),destroy:ce.destroy.bind(this),setHash:ce.setHash.bind(this),onHashCange:ce.onHashCange.bind(this)}})},on:{init:function(){this.params.hashNavigation.enabled&&this.hashNavigation.init()},destroy:function(){this.params.hashNavigation.enabled&&this.hashNavigation.destroy()},transitionEnd:function(){this.hashNavigation.initialized&&this.hashNavigation.setHash()},slideChange:function(){this.hashNavigation.initialized&&this.params.cssMode&&this.hashNavigation.setHash()}}},{name:"autoplay",params:{autoplay:{enabled:!1,delay:3e3,waitForTransition:!0,disableOnInteraction:!0,stopOnLastSlide:!1,reverseDirection:!1}},create:function(){var e=this;n.extend(e,{autoplay:{running:!1,paused:!1,run:ue.run.bind(e),start:ue.start.bind(e),stop:ue.stop.bind(e),pause:ue.pause.bind(e),onVisibilityChange:function(){"hidden"===document.visibilityState&&e.autoplay.running&&e.autoplay.pause(),"visible"===document.visibilityState&&e.autoplay.paused&&(e.autoplay.run(),e.autoplay.paused=!1)},onTransitionEnd:function(t){e&&!e.destroyed&&e.$wrapperEl&&t.target===this&&(e.$wrapperEl[0].removeEventListener("transitionend",e.autoplay.onTransitionEnd),e.$wrapperEl[0].removeEventListener("webkitTransitionEnd",e.autoplay.onTransitionEnd),e.autoplay.paused=!1,e.autoplay.running?e.autoplay.run():e.autoplay.stop())}}})},on:{init:function(){this.params.autoplay.enabled&&(this.autoplay.start(),document.addEventListener("visibilitychange",this.autoplay.onVisibilityChange))},beforeTransitionStart:function(e,t){this.autoplay.running&&(t||!this.params.autoplay.disableOnInteraction?this.autoplay.pause(e):this.autoplay.stop())},sliderFirstMove:function(){this.autoplay.running&&(this.params.autoplay.disableOnInteraction?this.autoplay.stop():this.autoplay.pause())},touchEnd:function(){this.params.cssMode&&this.autoplay.paused&&!this.params.autoplay.disableOnInteraction&&this.autoplay.run()},destroy:function(){this.autoplay.running&&this.autoplay.stop(),document.removeEventListener("visibilitychange",this.autoplay.onVisibilityChange)}}},{name:"effect-fade",params:{fadeEffect:{crossFade:!1}},create:function(){n.extend(this,{fadeEffect:{setTranslate:ve.setTranslate.bind(this),setTransition:ve.setTransition.bind(this)}})},on:{beforeInit:function(){if("fade"===this.params.effect){this.classNames.push(this.params.containerModifierClass+"fade");var e={slidesPerView:1,slidesPerColumn:1,slidesPerGroup:1,watchSlidesProgress:!0,spaceBetween:0,virtualTranslate:!0};n.extend(this.params,e),n.extend(this.originalParams,e)}},setTranslate:function(){"fade"===this.params.effect&&this.fadeEffect.setTranslate()},setTransition:function(e){"fade"===this.params.effect&&this.fadeEffect.setTransition(e)}}},{name:"effect-cube",params:{cubeEffect:{slideShadows:!0,shadow:!0,shadowOffset:20,shadowScale:.94}},create:function(){n.extend(this,{cubeEffect:{setTranslate:fe.setTranslate.bind(this),setTransition:fe.setTransition.bind(this)}})},on:{beforeInit:function(){if("cube"===this.params.effect){this.classNames.push(this.params.containerModifierClass+"cube"),this.classNames.push(this.params.containerModifierClass+"3d");var e={slidesPerView:1,slidesPerColumn:1,slidesPerGroup:1,watchSlidesProgress:!0,resistanceRatio:0,spaceBetween:0,centeredSlides:!1,virtualTranslate:!0};n.extend(this.params,e),n.extend(this.originalParams,e)}},setTranslate:function(){"cube"===this.params.effect&&this.cubeEffect.setTranslate()},setTransition:function(e){"cube"===this.params.effect&&this.cubeEffect.setTransition(e)}}},{name:"effect-flip",params:{flipEffect:{slideShadows:!0,limitRotation:!0}},create:function(){n.extend(this,{flipEffect:{setTranslate:me.setTranslate.bind(this),setTransition:me.setTransition.bind(this)}})},on:{beforeInit:function(){if("flip"===this.params.effect){this.classNames.push(this.params.containerModifierClass+"flip"),this.classNames.push(this.params.containerModifierClass+"3d");var e={slidesPerView:1,slidesPerColumn:1,slidesPerGroup:1,watchSlidesProgress:!0,spaceBetween:0,virtualTranslate:!0};n.extend(this.params,e),n.extend(this.originalParams,e)}},setTranslate:function(){"flip"===this.params.effect&&this.flipEffect.setTranslate()},setTransition:function(e){"flip"===this.params.effect&&this.flipEffect.setTransition(e)}}},{name:"effect-coverflow",params:{coverflowEffect:{rotate:50,stretch:0,depth:100,modifier:1,slideShadows:!0}},create:function(){n.extend(this,{coverflowEffect:{setTranslate:ge.setTranslate.bind(this),setTransition:ge.setTransition.bind(this)}})},on:{beforeInit:function(){"coverflow"===this.params.effect&&(this.classNames.push(this.params.containerModifierClass+"coverflow"),this.classNames.push(this.params.containerModifierClass+"3d"),this.params.watchSlidesProgress=!0,this.originalParams.watchSlidesProgress=!0)},setTranslate:function(){"coverflow"===this.params.effect&&this.coverflowEffect.setTranslate()},setTransition:function(e){"coverflow"===this.params.effect&&this.coverflowEffect.setTransition(e)}}},{name:"thumbs",params:{thumbs:{multipleActiveThumbs:!0,swiper:null,slideThumbActiveClass:"swiper-slide-thumb-active",thumbsContainerClass:"swiper-container-thumbs"}},create:function(){n.extend(this,{thumbs:{swiper:null,init:be.init.bind(this),update:be.update.bind(this),onThumbClick:be.onThumbClick.bind(this)}})},on:{beforeInit:function(){var e=this.params.thumbs;e&&e.swiper&&(this.thumbs.init(),this.thumbs.update(!0))},slideChange:function(){this.thumbs.swiper&&this.thumbs.update()},update:function(){this.thumbs.swiper&&this.thumbs.update()},resize:function(){this.thumbs.swiper&&this.thumbs.update()},observerUpdate:function(){this.thumbs.swiper&&this.thumbs.update()},setTransition:function(e){var t=this.thumbs.swiper;t&&t.setTransition(e)},beforeDestroy:function(){var e=this.thumbs.swiper;e&&this.thumbs.swiperCreated&&e&&e.destroy()}}}];return void 0===W.use&&(W.use=W.Class.use,W.installModule=W.Class.installModule),W.use(we),W}));

/*!
 * The Final Countdown for jQuery v2.1.0 (http://hilios.github.io/jQuery.countdown/)
 * Copyright (c) 2015 Edson Hilios
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
(function (factory) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define(["jquery"], factory);
    } else {
        factory(jQuery);
    }
})(function ($) {
    "use strict";
    var instances = [], matchers = [], defaultOptions = {
        precision: 100,
        elapse: false
    };
    matchers.push(/^[0-9]*$/.source);
    matchers.push(/([0-9]{1,2}\/){2}[0-9]{4}( [0-9]{1,2}(:[0-9]{2}){2})?/.source);
    matchers.push(/[0-9]{4}([\/\-][0-9]{1,2}){2}( [0-9]{1,2}(:[0-9]{2}){2})?/.source);
    matchers = new RegExp(matchers.join("|"));
    function parseDateString(dateString) {
        if (dateString instanceof Date) {
            return dateString;
        }
        if (String(dateString).match(matchers)) {
            if (String(dateString).match(/^[0-9]*$/)) {
                dateString = Number(dateString);
            }
            if (String(dateString).match(/\-/)) {
                dateString = String(dateString).replace(/\-/g, "/");
            }
            return new Date(dateString);
        } else {
            throw new Error("Couldn't cast `" + dateString + "` to a date object.");
        }
    }
    var DIRECTIVE_KEY_MAP = {
        Y: "years",
        m: "months",
        n: "daysToMonth",
        w: "weeks",
        d: "daysToWeek",
        D: "totalDays",
        H: "hours",
        M: "minutes",
        S: "seconds"
    };
    function escapedRegExp(str) {
        var sanitize = str.toString().replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
        return new RegExp(sanitize);
    }
    function strftime(offsetObject) {
        return function (format) {
            var directives = format.match(/%(-|!)?[A-Z]{1}(:[^;]+;)?/gi);
            if (directives) {
                for (var i = 0, len = directives.length; i < len; ++i) {
                    var directive = directives[i].match(/%(-|!)?([a-zA-Z]{1})(:[^;]+;)?/), regexp = escapedRegExp(directive[0]), modifier = directive[1] || "", plural = directive[3] || "", value = null;
                    directive = directive[2];
                    if (DIRECTIVE_KEY_MAP.hasOwnProperty(directive)) {
                        value = DIRECTIVE_KEY_MAP[directive];
                        value = Number(offsetObject[value]);
                    }
                    if (value !== null) {
                        if (modifier === "!") {
                            value = pluralize(plural, value);
                        }
                        if (modifier === "") {
                            if (value < 10) {
                                value = "0" + value.toString();
                            }
                        }
                        format = format.replace(regexp, value.toString());
                    }
                }
            }
            format = format.replace(/%%/, "%");
            return format;
        };
    }
    function pluralize(format, count) {
        var plural = "s", singular = "";
        if (format) {
            format = format.replace(/(:|;|\s)/gi, "").split(/\,/);
            if (format.length === 1) {
                plural = format[0];
            } else {
                singular = format[0];
                plural = format[1];
            }
        }
        if (Math.abs(count) === 1) {
            return singular;
        } else {
            return plural;
        }
    }
    var Countdown = function (el, finalDate, options) {
        this.el = el;
        this.$el = $(el);
        this.interval = null;
        this.offset = {};
        this.options = $.extend({}, defaultOptions);
        this.instanceNumber = instances.length;
        instances.push(this);
        this.$el.data("countdown-instance", this.instanceNumber);
        if (options) {
            if (typeof options === "function") {
                this.$el.on("update.countdown", options);
                this.$el.on("stoped.countdown", options);
                this.$el.on("finish.countdown", options);
            } else {
                this.options = $.extend({}, defaultOptions, options);
            }
        }
        this.setFinalDate(finalDate);
        this.start();
    };
    $.extend(Countdown.prototype, {
        start: function () {
            if (this.interval !== null) {
                clearInterval(this.interval);
            }
            var self = this;
            this.update();
            this.interval = setInterval(function () {
                self.update.call(self);
            }, this.options.precision);
        },
        stop: function () {
            clearInterval(this.interval);
            this.interval = null;
            this.dispatchEvent("stoped");
        },
        toggle: function () {
            if (this.interval) {
                this.stop();
            } else {
                this.start();
            }
        },
        pause: function () {
            this.stop();
        },
        resume: function () {
            this.start();
        },
        remove: function () {
            this.stop.call(this);
            instances[this.instanceNumber] = null;
            delete this.$el.data().countdownInstance;
        },
        setFinalDate: function (value) {
            this.finalDate = parseDateString(value);
        },
        update: function () {
            if (this.$el.closest("html").length === 0) {
                this.remove();
                return;
            }
            var hasEventsAttached = $._data(this.el, "events") !== undefined, now = new Date(), newTotalSecsLeft;
            newTotalSecsLeft = this.finalDate.getTime() - now.getTime();
            newTotalSecsLeft = Math.ceil(newTotalSecsLeft / 1e3);
            newTotalSecsLeft = !this.options.elapse && newTotalSecsLeft < 0 ? 0 : Math.abs(newTotalSecsLeft);
            if (this.totalSecsLeft === newTotalSecsLeft || !hasEventsAttached) {
                return;
            } else {
                this.totalSecsLeft = newTotalSecsLeft;
            }
            this.elapsed = now >= this.finalDate;
            this.offset = {
                seconds: this.totalSecsLeft % 60,
                minutes: Math.floor(this.totalSecsLeft / 60) % 60,
                hours: Math.floor(this.totalSecsLeft / 60 / 60) % 24,
                days: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7,
                daysToWeek: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7,
                daysToMonth: Math.floor(this.totalSecsLeft / 60 / 60 / 24 % 30.4368),
                totalDays: Math.floor(this.totalSecsLeft / 60 / 60 / 24),
                weeks: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7),
                months: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 30.4368),
                years: Math.abs(this.finalDate.getFullYear() - now.getFullYear())
            };
            if (!this.options.elapse && this.totalSecsLeft === 0) {
                this.stop();
                this.dispatchEvent("finish");
            } else {
                this.dispatchEvent("update");
            }
        },
        dispatchEvent: function (eventName) {
            var event = $.Event(eventName + ".countdown");
            event.finalDate = this.finalDate;
            event.elapsed = this.elapsed;
            event.offset = $.extend({}, this.offset);
            event.strftime = strftime(this.offset);
            this.$el.trigger(event);
        }
    });
    $.fn.countdown = function () {
        var argumentsArray = Array.prototype.slice.call(arguments, 0);
        return this.each(function () {
            var instanceNumber = $(this).data("countdown-instance");
            if (instanceNumber !== undefined) {
                var instance = instances[instanceNumber], method = argumentsArray[0];
                if (Countdown.prototype.hasOwnProperty(method)) {
                    instance[method].apply(instance, argumentsArray.slice(1));
                } else if (String(method).match(/^[$A-Z_][0-9A-Z_$]*$/i) === null) {
                    instance.setFinalDate.call(instance, method);
                    instance.start();
                } else {
                    $.error("Method %s does not exist on jQuery.countdown".replace(/\%s/gi, method));
                }
            } else {
                new Countdown(this, argumentsArray[0], argumentsArray[1]);
            }
        });
    };
});

!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.dayjs=e()}(this,function(){"use strict";var t="millisecond",e="second",n="minute",r="hour",i="day",s="week",u="month",a="quarter",o="year",f="date",h=/^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[^0-9]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?.?(\d+)?$/,c=/\[([^\]]+)]|Y{2,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g,d={name:"en",weekdays:"Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),months:"January_February_March_April_May_June_July_August_September_October_November_December".split("_")},$=function(t,e,n){var r=String(t);return!r||r.length>=e?t:""+Array(e+1-r.length).join(n)+t},l={s:$,z:function(t){var e=-t.utcOffset(),n=Math.abs(e),r=Math.floor(n/60),i=n%60;return(e<=0?"+":"-")+$(r,2,"0")+":"+$(i,2,"0")},m:function t(e,n){if(e.date()<n.date())return-t(n,e);var r=12*(n.year()-e.year())+(n.month()-e.month()),i=e.clone().add(r,u),s=n-i<0,a=e.clone().add(r+(s?-1:1),u);return+(-(r+(n-i)/(s?i-a:a-i))||0)},a:function(t){return t<0?Math.ceil(t)||0:Math.floor(t)},p:function(h){return{M:u,y:o,w:s,d:i,D:f,h:r,m:n,s:e,ms:t,Q:a}[h]||String(h||"").toLowerCase().replace(/s$/,"")},u:function(t){return void 0===t}},y="en",M={};M[y]=d;var m=function(t){return t instanceof S},D=function(t,e,n){var r;if(!t)return y;if("string"==typeof t)M[t]&&(r=t),e&&(M[t]=e,r=t);else{var i=t.name;M[i]=t,r=i}return!n&&r&&(y=r),r||!n&&y},v=function(t,e){if(m(t))return t.clone();var n="object"==typeof e?e:{};return n.date=t,n.args=arguments,new S(n)},g=l;g.l=D,g.i=m,g.w=function(t,e){return v(t,{locale:e.$L,utc:e.$u,x:e.$x,$offset:e.$offset})};var S=function(){function d(t){this.$L=D(t.locale,null,!0),this.parse(t)}var $=d.prototype;return $.parse=function(t){this.$d=function(t){var e=t.date,n=t.utc;if(null===e)return new Date(NaN);if(g.u(e))return new Date;if(e instanceof Date)return new Date(e);if("string"==typeof e&&!/Z$/i.test(e)){var r=e.match(h);if(r){var i=r[2]-1||0,s=(r[7]||"0").substring(0,3);return n?new Date(Date.UTC(r[1],i,r[3]||1,r[4]||0,r[5]||0,r[6]||0,s)):new Date(r[1],i,r[3]||1,r[4]||0,r[5]||0,r[6]||0,s)}}return new Date(e)}(t),this.$x=t.x||{},this.init()},$.init=function(){var t=this.$d;this.$y=t.getFullYear(),this.$M=t.getMonth(),this.$D=t.getDate(),this.$W=t.getDay(),this.$H=t.getHours(),this.$m=t.getMinutes(),this.$s=t.getSeconds(),this.$ms=t.getMilliseconds()},$.$utils=function(){return g},$.isValid=function(){return!("Invalid Date"===this.$d.toString())},$.isSame=function(t,e){var n=v(t);return this.startOf(e)<=n&&n<=this.endOf(e)},$.isAfter=function(t,e){return v(t)<this.startOf(e)},$.isBefore=function(t,e){return this.endOf(e)<v(t)},$.$g=function(t,e,n){return g.u(t)?this[e]:this.set(n,t)},$.unix=function(){return Math.floor(this.valueOf()/1e3)},$.valueOf=function(){return this.$d.getTime()},$.startOf=function(t,a){var h=this,c=!!g.u(a)||a,d=g.p(t),$=function(t,e){var n=g.w(h.$u?Date.UTC(h.$y,e,t):new Date(h.$y,e,t),h);return c?n:n.endOf(i)},l=function(t,e){return g.w(h.toDate()[t].apply(h.toDate("s"),(c?[0,0,0,0]:[23,59,59,999]).slice(e)),h)},y=this.$W,M=this.$M,m=this.$D,D="set"+(this.$u?"UTC":"");switch(d){case o:return c?$(1,0):$(31,11);case u:return c?$(1,M):$(0,M+1);case s:var v=this.$locale().weekStart||0,S=(y<v?y+7:y)-v;return $(c?m-S:m+(6-S),M);case i:case f:return l(D+"Hours",0);case r:return l(D+"Minutes",1);case n:return l(D+"Seconds",2);case e:return l(D+"Milliseconds",3);default:return this.clone()}},$.endOf=function(t){return this.startOf(t,!1)},$.$set=function(s,a){var h,c=g.p(s),d="set"+(this.$u?"UTC":""),$=(h={},h[i]=d+"Date",h[f]=d+"Date",h[u]=d+"Month",h[o]=d+"FullYear",h[r]=d+"Hours",h[n]=d+"Minutes",h[e]=d+"Seconds",h[t]=d+"Milliseconds",h)[c],l=c===i?this.$D+(a-this.$W):a;if(c===u||c===o){var y=this.clone().set(f,1);y.$d[$](l),y.init(),this.$d=y.set(f,Math.min(this.$D,y.daysInMonth())).$d}else $&&this.$d[$](l);return this.init(),this},$.set=function(t,e){return this.clone().$set(t,e)},$.get=function(t){return this[g.p(t)]()},$.add=function(t,a){var f,h=this;t=Number(t);var c=g.p(a),d=function(e){var n=v(h);return g.w(n.date(n.date()+Math.round(e*t)),h)};if(c===u)return this.set(u,this.$M+t);if(c===o)return this.set(o,this.$y+t);if(c===i)return d(1);if(c===s)return d(7);var $=(f={},f[n]=6e4,f[r]=36e5,f[e]=1e3,f)[c]||1,l=this.$d.getTime()+t*$;return g.w(l,this)},$.subtract=function(t,e){return this.add(-1*t,e)},$.format=function(t){var e=this;if(!this.isValid())return"Invalid Date";var n=t||"YYYY-MM-DDTHH:mm:ssZ",r=g.z(this),i=this.$locale(),s=this.$H,u=this.$m,a=this.$M,o=i.weekdays,f=i.months,h=function(t,r,i,s){return t&&(t[r]||t(e,n))||i[r].substr(0,s)},d=function(t){return g.s(s%12||12,t,"0")},$=i.meridiem||function(t,e,n){var r=t<12?"AM":"PM";return n?r.toLowerCase():r},l={YY:String(this.$y).slice(-2),YYYY:this.$y,M:a+1,MM:g.s(a+1,2,"0"),MMM:h(i.monthsShort,a,f,3),MMMM:h(f,a),D:this.$D,DD:g.s(this.$D,2,"0"),d:String(this.$W),dd:h(i.weekdaysMin,this.$W,o,2),ddd:h(i.weekdaysShort,this.$W,o,3),dddd:o[this.$W],H:String(s),HH:g.s(s,2,"0"),h:d(1),hh:d(2),a:$(s,u,!0),A:$(s,u,!1),m:String(u),mm:g.s(u,2,"0"),s:String(this.$s),ss:g.s(this.$s,2,"0"),SSS:g.s(this.$ms,3,"0"),Z:r};return n.replace(c,function(t,e){return e||l[t]||r.replace(":","")})},$.utcOffset=function(){return 15*-Math.round(this.$d.getTimezoneOffset()/15)},$.diff=function(t,f,h){var c,d=g.p(f),$=v(t),l=6e4*($.utcOffset()-this.utcOffset()),y=this-$,M=g.m(this,$);return M=(c={},c[o]=M/12,c[u]=M,c[a]=M/3,c[s]=(y-l)/6048e5,c[i]=(y-l)/864e5,c[r]=y/36e5,c[n]=y/6e4,c[e]=y/1e3,c)[d]||y,h?M:g.a(M)},$.daysInMonth=function(){return this.endOf(u).$D},$.$locale=function(){return M[this.$L]},$.locale=function(t,e){if(!t)return this.$L;var n=this.clone(),r=D(t,e,!0);return r&&(n.$L=r),n},$.clone=function(){return g.w(this.$d,this)},$.toDate=function(){return new Date(this.valueOf())},$.toJSON=function(){return this.isValid()?this.toISOString():null},$.toISOString=function(){return this.$d.toISOString()},$.toString=function(){return this.$d.toUTCString()},d}(),p=S.prototype;return v.prototype=p,[["$ms",t],["$s",e],["$m",n],["$H",r],["$W",i],["$M",u],["$y",o],["$D",f]].forEach(function(t){p[t[1]]=function(e){return this.$g(e,t[0],t[1])}}),v.extend=function(t,e){return t(e,S,v),v},v.locale=D,v.isDayjs=m,v.unix=function(t){return v(1e3*t)},v.en=M[y],v.Ls=M,v.p={},v});

!function(a,i){"object"==typeof exports&&"undefined"!=typeof module?i(exports):"function"==typeof define&&define.amd?define(["exports"],i):i((a=a||self).timezoneSupport={})}(this,function(a){"use strict";function l(a){return 96<a?a-87:64<a?a-29:a-48}function r(a){var i=a.split("."),e=i[0],r=i[1]||"",A=1,c=0,o=0,n=1;45===a.charCodeAt(0)&&(n=-(c=1));for(var t=c,s=e.length;t<s;++t){o=60*o+l(e.charCodeAt(t))}for(var u=0,m=r.length;u<m;++u){o+=l(r.charCodeAt(u))*(A/=60)}return o*n}function t(a){for(var i=0,e=a.length;i<e;++i)a[i]=r(a[i])}function s(a,i){for(var e=[],r=0,A=i.length;r<A;++r)e[r]=a[i[r]];return e}function A(a){var i=a.split("|"),e=i[2].split(" "),r=i[3].split(""),A=i[4].split(" ");t(e),t(r),t(A),function(a,i){for(var e=0;e<i;++e)a[e]=Math.round((a[e-1]||0)+6e4*a[e]);a[i-1]=1/0}(A,r.length);var c=i[0],o=s(i[1].split(" "),r),n=0|i[5];return{name:c,abbreviations:o,offsets:e=s(e,r),untils:A,population:n}}var c,i,o,n;function d(a){var i=a.year,e=a.month,r=a.day,A=a.hours,c=void 0===A?0:A,o=a.minutes,n=void 0===o?0:o,t=a.seconds,s=void 0===t?0:t,u=a.milliseconds,m=void 0===u?0:u;return Date.UTC(i,e-1,r,c,n,s,m)}function E(a){return{year:a.getUTCFullYear(),month:a.getUTCMonth()+1,day:a.getUTCDate(),dayOfWeek:a.getUTCDay(),hours:a.getUTCHours(),minutes:a.getUTCMinutes(),seconds:a.getUTCSeconds()||0,milliseconds:a.getUTCMilliseconds()||0}}function h(a){return{year:a.getFullYear(),month:a.getMonth()+1,day:a.getDate(),dayOfWeek:a.getDay(),hours:a.getHours(),minutes:a.getMinutes(),seconds:a.getSeconds()||0,milliseconds:a.getMilliseconds()||0}}function T(a,i){var e=function(a,i){for(var e=i.untils,r=0,A=e.length;r<A;++r)if(a<e[r])return r}(a,i);return{abbreviation:i.abbreviations[e],offset:i.offsets[e]}}function z(a,i){Object.defineProperty(a,"epoch",{value:i})}var e,u,m;u=(e={version:"2019a",zones:["Africa/Abidjan|GMT|0|0||48e5","Africa/Nairobi|EAT|-30|0||47e5","Africa/Algiers|CET|-10|0||26e5","Africa/Lagos|WAT|-10|0||17e6","Africa/Maputo|CAT|-20|0||26e5","Africa/Cairo|EET EEST|-20 -30|01010|1M2m0 gL0 e10 mn0|15e6","Africa/Casablanca|+00 +01|0 -10|010101010101010101010101010101010101|1H3C0 wM0 co0 go0 1o00 s00 dA0 vc0 11A0 A00 e00 y00 11A0 uM0 e00 Dc0 11A0 s00 e00 IM0 WM0 mo0 gM0 LA0 WM0 jA0 e00 28M0 e00 2600 e00 28M0 e00 2600 gM0|32e5","Europe/Paris|CET CEST|-10 -20|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|11e6","Africa/Johannesburg|SAST|-20|0||84e5","Africa/Khartoum|EAT CAT|-30 -20|01|1Usl0|51e5","Africa/Sao_Tome|GMT WAT|0 -10|010|1UQN0 2q00","Africa/Tripoli|EET CET CEST|-20 -10 -20|0120|1IlA0 TA0 1o00|11e5","Africa/Windhoek|CAT WAT|-20 -10|0101010101010|1GQo0 11B0 1qL0 WN0 1qL0 11B0 1nX0 11B0 1nX0 11B0 1nX0 11B0|32e4","America/Adak|HST HDT|a0 90|01010101010101010101010|1GIc0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|326","America/Anchorage|AKST AKDT|90 80|01010101010101010101010|1GIb0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|30e4","America/Santo_Domingo|AST|40|0||29e5","America/Araguaina|-03 -02|30 20|010|1IdD0 Lz0|14e4","America/Fortaleza|-03|30|0||34e5","America/Asuncion|-03 -04|30 40|01010101010101010101010|1GTf0 1cN0 17b0 1ip0 17b0 1ip0 17b0 1ip0 19X0 1fB0 19X0 1fB0 19X0 1ip0 17b0 1ip0 17b0 1ip0 19X0 1fB0 19X0 1fB0|28e5","America/Panama|EST|50|0||15e5","America/Mexico_City|CST CDT|60 50|01010101010101010101010|1GQw0 1nX0 14p0 1lb0 14p0 1lb0 14p0 1lb0 14p0 1nX0 11B0 1nX0 11B0 1nX0 14p0 1lb0 14p0 1lb0 14p0 1nX0 11B0 1nX0|20e6","America/Bahia|-02 -03|20 30|01|1GCq0|27e5","America/Managua|CST|60|0||22e5","America/La_Paz|-04|40|0||19e5","America/Lima|-05|50|0||11e6","America/Denver|MST MDT|70 60|01010101010101010101010|1GI90 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|26e5","America/Campo_Grande|-03 -04|30 40|01010101010101010101010|1GCr0 1zd0 Lz0 1C10 Lz0 1C10 On0 1zd0 On0 1zd0 On0 1zd0 On0 1HB0 FX0 1HB0 FX0 1HB0 IL0 1HB0 FX0 1HB0|77e4","America/Cancun|CST CDT EST|60 50 50|01010102|1GQw0 1nX0 14p0 1lb0 14p0 1lb0 Dd0|63e4","America/Caracas|-0430 -04|4u 40|01|1QMT0|29e5","America/Chicago|CST CDT|60 50|01010101010101010101010|1GI80 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|92e5","America/Chihuahua|MST MDT|70 60|01010101010101010101010|1GQx0 1nX0 14p0 1lb0 14p0 1lb0 14p0 1lb0 14p0 1nX0 11B0 1nX0 11B0 1nX0 14p0 1lb0 14p0 1lb0 14p0 1nX0 11B0 1nX0|81e4","America/Phoenix|MST|70|0||42e5","America/Los_Angeles|PST PDT|80 70|01010101010101010101010|1GIa0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|15e6","America/New_York|EST EDT|50 40|01010101010101010101010|1GI70 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|21e6","America/Rio_Branco|-04 -05|40 50|01|1KLE0|31e4","America/Fort_Nelson|PST PDT MST|80 70 70|01010102|1GIa0 1zb0 Op0 1zb0 Op0 1zb0 Op0|39e2","America/Halifax|AST ADT|40 30|01010101010101010101010|1GI60 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|39e4","America/Godthab|-03 -02|30 20|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|17e3","America/Grand_Turk|EST EDT AST|50 40 40|0101010121010101010|1GI70 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 5Ip0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|37e2","America/Havana|CST CDT|50 40|01010101010101010101010|1GQt0 1qM0 Oo0 1zc0 Oo0 1zc0 Oo0 1zc0 Rc0 1zc0 Oo0 1zc0 Oo0 1zc0 Oo0 1zc0 Oo0 1zc0 Rc0 1zc0 Oo0 1zc0|21e5","America/Metlakatla|PST AKST AKDT|80 90 80|01212120121212121|1PAa0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 uM0 jB0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|14e2","America/Miquelon|-03 -02|30 20|01010101010101010101010|1GI50 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|61e2","America/Montevideo|-02 -03|20 30|01010101|1GI40 1o10 11z0 1o10 11z0 1o10 11z0|17e5","America/Noronha|-02|20|0||30e2","America/Port-au-Prince|EST EDT|50 40|010101010101010101010|1GI70 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 3iN0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|23e5","Antarctica/Palmer|-03 -04|30 40|010101010|1H3D0 Op0 1zb0 Rd0 1wn0 Rd0 46n0 Ap0|40","America/Santiago|-03 -04|30 40|010101010101010101010|1H3D0 Op0 1zb0 Rd0 1wn0 Rd0 46n0 Ap0 1Nb0 Ap0 1Nb0 Ap0 1zb0 11B0 1nX0 11B0 1nX0 11B0 1nX0 11B0|62e5","America/Sao_Paulo|-02 -03|20 30|01010101010101010101010|1GCq0 1zd0 Lz0 1C10 Lz0 1C10 On0 1zd0 On0 1zd0 On0 1zd0 On0 1HB0 FX0 1HB0 FX0 1HB0 IL0 1HB0 FX0 1HB0|20e6","Atlantic/Azores|-01 +00|10 0|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|25e4","America/St_Johns|NST NDT|3u 2u|01010101010101010101010|1GI5u 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Op0 1zb0 Rd0 1zb0 Op0 1zb0|11e4","Antarctica/Casey|+11 +08|-b0 -80|0101|1GAF0 blz0 3m10|10","Antarctica/Davis|+05 +07|-50 -70|01|1GAI0|70","Pacific/Port_Moresby|+10|-a0|0||25e4","Pacific/Guadalcanal|+11|-b0|0||11e4","Asia/Tashkent|+05|-50|0||23e5","Pacific/Auckland|NZDT NZST|-d0 -c0|01010101010101010101010|1GQe0 1cM0 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1cM0 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00|14e5","Asia/Baghdad|+03|-30|0||66e5","Antarctica/Troll|+00 +02|0 -20|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|40","Asia/Dhaka|+06|-60|0||16e6","Asia/Amman|EET EEST|-20 -30|010101010101010101010|1GPy0 4bX0 Dd0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 11A0 1o00|25e5","Asia/Kamchatka|+12|-c0|0||18e4","Asia/Baku|+04 +05|-40 -50|010101010|1GNA0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00|27e5","Asia/Bangkok|+07|-70|0||15e6","Asia/Barnaul|+07 +06|-70 -60|010|1N7v0 3rd0","Asia/Beirut|EET EEST|-20 -30|01010101010101010101010|1GNy0 1qL0 11B0 1nX0 11B0 1nX0 11B0 1nX0 11B0 1qL0 WN0 1qL0 WN0 1qL0 11B0 1nX0 11B0 1nX0 11B0 1qL0 WN0 1qL0|22e5","Asia/Kuala_Lumpur|+08|-80|0||71e5","Asia/Kolkata|IST|-5u|0||15e6","Asia/Chita|+10 +08 +09|-a0 -80 -90|012|1N7s0 3re0|33e4","Asia/Ulaanbaatar|+08 +09|-80 -90|01010|1O8G0 1cJ0 1cP0 1cJ0|12e5","Asia/Shanghai|CST|-80|0||23e6","Asia/Colombo|+0530|-5u|0||22e5","Asia/Damascus|EET EEST|-20 -30|01010101010101010101010|1GPy0 1nX0 11B0 1nX0 11B0 1qL0 WN0 1qL0 WN0 1qL0 11B0 1nX0 11B0 1nX0 11B0 1nX0 11B0 1qL0 WN0 1qL0 WN0 1qL0|26e5","Asia/Dili|+09|-90|0||19e4","Asia/Dubai|+04|-40|0||39e5","Asia/Famagusta|EET EEST +03|-20 -30 -30|0101010101201010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 15U0 2Ks0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0","Asia/Gaza|EET EEST|-20 -30|01010101010101010101010|1GPy0 1a00 1fA0 1cL0 1cN0 1nX0 1210 1nz0 1220 1qL0 WN0 1qL0 WN0 1qL0 11B0 1nX0 11B0 1qL0 WN0 1qL0 WN0 1qL0|18e5","Asia/Hong_Kong|HKT|-80|0||73e5","Asia/Hovd|+07 +08|-70 -80|01010|1O8H0 1cJ0 1cP0 1cJ0|81e3","Asia/Irkutsk|+09 +08|-90 -80|01|1N7t0|60e4","Europe/Istanbul|EET EEST +03|-20 -30 -30|01010101012|1GNB0 1qM0 11A0 1o00 1200 1nA0 11A0 1tA0 U00 15w0|13e6","Asia/Jakarta|WIB|-70|0||31e6","Asia/Jayapura|WIT|-90|0||26e4","Asia/Jerusalem|IST IDT|-20 -30|01010101010101010101010|1GPA0 1aL0 1eN0 1oL0 10N0 1oL0 10N0 1oL0 10N0 1rz0 W10 1rz0 W10 1rz0 10N0 1oL0 10N0 1oL0 10N0 1rz0 W10 1rz0|81e4","Asia/Kabul|+0430|-4u|0||46e5","Asia/Karachi|PKT|-50|0||24e6","Asia/Kathmandu|+0545|-5J|0||12e5","Asia/Yakutsk|+10 +09|-a0 -90|01|1N7s0|28e4","Asia/Krasnoyarsk|+08 +07|-80 -70|01|1N7u0|10e5","Asia/Magadan|+12 +10 +11|-c0 -a0 -b0|012|1N7q0 3Cq0|95e3","Asia/Makassar|WITA|-80|0||15e5","Asia/Manila|PST|-80|0||24e6","Europe/Athens|EET EEST|-20 -30|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|35e5","Asia/Novosibirsk|+07 +06|-70 -60|010|1N7v0 4eN0|15e5","Asia/Omsk|+07 +06|-70 -60|01|1N7v0|12e5","Asia/Pyongyang|KST KST|-90 -8u|010|1P4D0 6BA0|29e5","Asia/Qyzylorda|+06 +05|-60 -50|01|1Xei0|73e4","Asia/Rangoon|+0630|-6u|0||48e5","Asia/Sakhalin|+11 +10|-b0 -a0|010|1N7r0 3rd0|58e4","Asia/Seoul|KST|-90|0||23e6","Asia/Srednekolymsk|+12 +11|-c0 -b0|01|1N7q0|35e2","Asia/Tehran|+0330 +0430|-3u -4u|01010101010101010101010|1GLUu 1dz0 1cN0 1dz0 1cp0 1dz0 1cp0 1dz0 1cp0 1dz0 1cN0 1dz0 1cp0 1dz0 1cp0 1dz0 1cp0 1dz0 1cN0 1dz0 1cp0 1dz0|14e6","Asia/Tokyo|JST|-90|0||38e6","Asia/Tomsk|+07 +06|-70 -60|010|1N7v0 3Qp0|10e5","Asia/Vladivostok|+11 +10|-b0 -a0|01|1N7r0|60e4","Asia/Yekaterinburg|+06 +05|-60 -50|01|1N7w0|14e5","Europe/Lisbon|WET WEST|0 -10|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|27e5","Atlantic/Cape_Verde|-01|10|0||50e4","Australia/Sydney|AEDT AEST|-b0 -a0|01010101010101010101010|1GQg0 1fA0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1fA0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0|40e5","Australia/Adelaide|ACDT ACST|-au -9u|01010101010101010101010|1GQgu 1fA0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1fA0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0 1cM0|11e5","Australia/Brisbane|AEST|-a0|0||20e5","Australia/Darwin|ACST|-9u|0||12e4","Australia/Eucla|+0845|-8J|0||368","Australia/Lord_Howe|+11 +1030|-b0 -au|01010101010101010101010|1GQf0 1fAu 1cLu 1cMu 1cLu 1cMu 1cLu 1cMu 1cLu 1cMu 1cLu 1cMu 1cLu 1fAu 1cLu 1cMu 1cLu 1cMu 1cLu 1cMu 1cLu 1cMu|347","Australia/Perth|AWST|-80|0||18e5","Pacific/Easter|-05 -06|50 60|010101010101010101010|1H3D0 Op0 1zb0 Rd0 1wn0 Rd0 46n0 Ap0 1Nb0 Ap0 1Nb0 Ap0 1zb0 11B0 1nX0 11B0 1nX0 11B0 1nX0 11B0|30e2","Europe/Dublin|GMT IST|0 -10|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|12e5","Etc/GMT-1|+01|-10|0|","Pacific/Fakaofo|+13|-d0|0||483","Pacific/Kiritimati|+14|-e0|0||51e2","Etc/GMT-2|+02|-20|0|","Pacific/Tahiti|-10|a0|0||18e4","Pacific/Niue|-11|b0|0||12e2","Etc/GMT+12|-12|c0|0|","Pacific/Galapagos|-06|60|0||25e3","Etc/GMT+7|-07|70|0|","Pacific/Pitcairn|-08|80|0||56","Pacific/Gambier|-09|90|0||125","Etc/UTC|UTC|0|0|","Europe/Ulyanovsk|+04 +03|-40 -30|010|1N7y0 3rd0|13e5","Europe/London|GMT BST|0 -10|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|10e6","Europe/Chisinau|EET EEST|-20 -30|01010101010101010101010|1GNA0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0|67e4","Europe/Kaliningrad|+03 EET|-30 -20|01|1N7z0|44e4","Europe/Kirov|+04 +03|-40 -30|01|1N7y0|48e4","Europe/Moscow|MSK MSK|-40 -30|01|1N7y0|16e6","Europe/Saratov|+04 +03|-40 -30|010|1N7y0 5810","Europe/Simferopol|EET EEST MSK MSK|-20 -30 -40 -30|0101023|1GNB0 1qM0 11A0 1o00 11z0 1nW0|33e4","Europe/Volgograd|+04 +03|-40 -30|010|1N7y0 9Jd0|10e5","Pacific/Honolulu|HST|a0|0||37e4","MET|MET MEST|-10 -20|01010101010101010101010|1GNB0 1qM0 11A0 1o00 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0 WM0 1qM0 11A0 1o00 11A0 1o00 11A0 1qM0 WM0 1qM0","Pacific/Chatham|+1345 +1245|-dJ -cJ|01010101010101010101010|1GQe0 1cM0 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1cM0 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00|600","Pacific/Apia|+14 +13|-e0 -d0|01010101010101010101010|1GQe0 1cM0 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1cM0 1fA0 1a00 1fA0 1a00 1fA0 1a00 1fA0 1a00|37e3","Pacific/Bougainville|+10 +11|-a0 -b0|01|1NwE0|18e4","Pacific/Fiji|+13 +12|-d0 -c0|01010101010101010101010|1Goe0 1Nc0 Ao0 1Q00 xz0 1SN0 uM0 1SM0 uM0 1VA0 s00 1VA0 s00 1VA0 s00 1VA0 uM0 1SM0 uM0 1VA0 s00 1VA0|88e4","Pacific/Guam|ChST|-a0|0||17e4","Pacific/Marquesas|-0930|9u|0||86e2","Pacific/Pago_Pago|SST|b0|0||37e2","Pacific/Norfolk|+1130 +11|-bu -b0|01|1PoCu|25e4","Pacific/Tongatapu|+13 +14|-d0 -e0|010|1S4d0 s00|75e3"],links:["Africa/Abidjan|Africa/Accra","Africa/Abidjan|Africa/Bamako","Africa/Abidjan|Africa/Banjul","Africa/Abidjan|Africa/Bissau","Africa/Abidjan|Africa/Conakry","Africa/Abidjan|Africa/Dakar","Africa/Abidjan|Africa/Freetown","Africa/Abidjan|Africa/Lome","Africa/Abidjan|Africa/Monrovia","Africa/Abidjan|Africa/Nouakchott","Africa/Abidjan|Africa/Ouagadougou","Africa/Abidjan|Africa/Timbuktu","Africa/Abidjan|America/Danmarkshavn","Africa/Abidjan|Atlantic/Reykjavik","Africa/Abidjan|Atlantic/St_Helena","Africa/Abidjan|Etc/GMT","Africa/Abidjan|Etc/GMT+0","Africa/Abidjan|Etc/GMT-0","Africa/Abidjan|Etc/GMT0","Africa/Abidjan|Etc/Greenwich","Africa/Abidjan|GMT","Africa/Abidjan|GMT+0","Africa/Abidjan|GMT-0","Africa/Abidjan|GMT0","Africa/Abidjan|Greenwich","Africa/Abidjan|Iceland","Africa/Algiers|Africa/Tunis","Africa/Cairo|Egypt","Africa/Casablanca|Africa/El_Aaiun","Africa/Johannesburg|Africa/Maseru","Africa/Johannesburg|Africa/Mbabane","Africa/Lagos|Africa/Bangui","Africa/Lagos|Africa/Brazzaville","Africa/Lagos|Africa/Douala","Africa/Lagos|Africa/Kinshasa","Africa/Lagos|Africa/Libreville","Africa/Lagos|Africa/Luanda","Africa/Lagos|Africa/Malabo","Africa/Lagos|Africa/Ndjamena","Africa/Lagos|Africa/Niamey","Africa/Lagos|Africa/Porto-Novo","Africa/Maputo|Africa/Blantyre","Africa/Maputo|Africa/Bujumbura","Africa/Maputo|Africa/Gaborone","Africa/Maputo|Africa/Harare","Africa/Maputo|Africa/Kigali","Africa/Maputo|Africa/Lubumbashi","Africa/Maputo|Africa/Lusaka","Africa/Nairobi|Africa/Addis_Ababa","Africa/Nairobi|Africa/Asmara","Africa/Nairobi|Africa/Asmera","Africa/Nairobi|Africa/Dar_es_Salaam","Africa/Nairobi|Africa/Djibouti","Africa/Nairobi|Africa/Juba","Africa/Nairobi|Africa/Kampala","Africa/Nairobi|Africa/Mogadishu","Africa/Nairobi|Indian/Antananarivo","Africa/Nairobi|Indian/Comoro","Africa/Nairobi|Indian/Mayotte","Africa/Tripoli|Libya","America/Adak|America/Atka","America/Adak|US/Aleutian","America/Anchorage|America/Juneau","America/Anchorage|America/Nome","America/Anchorage|America/Sitka","America/Anchorage|America/Yakutat","America/Anchorage|US/Alaska","America/Campo_Grande|America/Cuiaba","America/Chicago|America/Indiana/Knox","America/Chicago|America/Indiana/Tell_City","America/Chicago|America/Knox_IN","America/Chicago|America/Matamoros","America/Chicago|America/Menominee","America/Chicago|America/North_Dakota/Beulah","America/Chicago|America/North_Dakota/Center","America/Chicago|America/North_Dakota/New_Salem","America/Chicago|America/Rainy_River","America/Chicago|America/Rankin_Inlet","America/Chicago|America/Resolute","America/Chicago|America/Winnipeg","America/Chicago|CST6CDT","America/Chicago|Canada/Central","America/Chicago|US/Central","America/Chicago|US/Indiana-Starke","America/Chihuahua|America/Mazatlan","America/Chihuahua|Mexico/BajaSur","America/Denver|America/Boise","America/Denver|America/Cambridge_Bay","America/Denver|America/Edmonton","America/Denver|America/Inuvik","America/Denver|America/Ojinaga","America/Denver|America/Shiprock","America/Denver|America/Yellowknife","America/Denver|Canada/Mountain","America/Denver|MST7MDT","America/Denver|Navajo","America/Denver|US/Mountain","America/Fortaleza|America/Argentina/Buenos_Aires","America/Fortaleza|America/Argentina/Catamarca","America/Fortaleza|America/Argentina/ComodRivadavia","America/Fortaleza|America/Argentina/Cordoba","America/Fortaleza|America/Argentina/Jujuy","America/Fortaleza|America/Argentina/La_Rioja","America/Fortaleza|America/Argentina/Mendoza","America/Fortaleza|America/Argentina/Rio_Gallegos","America/Fortaleza|America/Argentina/Salta","America/Fortaleza|America/Argentina/San_Juan","America/Fortaleza|America/Argentina/San_Luis","America/Fortaleza|America/Argentina/Tucuman","America/Fortaleza|America/Argentina/Ushuaia","America/Fortaleza|America/Belem","America/Fortaleza|America/Buenos_Aires","America/Fortaleza|America/Catamarca","America/Fortaleza|America/Cayenne","America/Fortaleza|America/Cordoba","America/Fortaleza|America/Jujuy","America/Fortaleza|America/Maceio","America/Fortaleza|America/Mendoza","America/Fortaleza|America/Paramaribo","America/Fortaleza|America/Recife","America/Fortaleza|America/Rosario","America/Fortaleza|America/Santarem","America/Fortaleza|Antarctica/Rothera","America/Fortaleza|Atlantic/Stanley","America/Fortaleza|Etc/GMT+3","America/Halifax|America/Glace_Bay","America/Halifax|America/Goose_Bay","America/Halifax|America/Moncton","America/Halifax|America/Thule","America/Halifax|Atlantic/Bermuda","America/Halifax|Canada/Atlantic","America/Havana|Cuba","America/La_Paz|America/Boa_Vista","America/La_Paz|America/Guyana","America/La_Paz|America/Manaus","America/La_Paz|America/Porto_Velho","America/La_Paz|Brazil/West","America/La_Paz|Etc/GMT+4","America/Lima|America/Bogota","America/Lima|America/Guayaquil","America/Lima|Etc/GMT+5","America/Los_Angeles|America/Dawson","America/Los_Angeles|America/Ensenada","America/Los_Angeles|America/Santa_Isabel","America/Los_Angeles|America/Tijuana","America/Los_Angeles|America/Vancouver","America/Los_Angeles|America/Whitehorse","America/Los_Angeles|Canada/Pacific","America/Los_Angeles|Canada/Yukon","America/Los_Angeles|Mexico/BajaNorte","America/Los_Angeles|PST8PDT","America/Los_Angeles|US/Pacific","America/Los_Angeles|US/Pacific-New","America/Managua|America/Belize","America/Managua|America/Costa_Rica","America/Managua|America/El_Salvador","America/Managua|America/Guatemala","America/Managua|America/Regina","America/Managua|America/Swift_Current","America/Managua|America/Tegucigalpa","America/Managua|Canada/Saskatchewan","America/Mexico_City|America/Bahia_Banderas","America/Mexico_City|America/Merida","America/Mexico_City|America/Monterrey","America/Mexico_City|Mexico/General","America/New_York|America/Detroit","America/New_York|America/Fort_Wayne","America/New_York|America/Indiana/Indianapolis","America/New_York|America/Indiana/Marengo","America/New_York|America/Indiana/Petersburg","America/New_York|America/Indiana/Vevay","America/New_York|America/Indiana/Vincennes","America/New_York|America/Indiana/Winamac","America/New_York|America/Indianapolis","America/New_York|America/Iqaluit","America/New_York|America/Kentucky/Louisville","America/New_York|America/Kentucky/Monticello","America/New_York|America/Louisville","America/New_York|America/Montreal","America/New_York|America/Nassau","America/New_York|America/Nipigon","America/New_York|America/Pangnirtung","America/New_York|America/Thunder_Bay","America/New_York|America/Toronto","America/New_York|Canada/Eastern","America/New_York|EST5EDT","America/New_York|US/East-Indiana","America/New_York|US/Eastern","America/New_York|US/Michigan","America/Noronha|Atlantic/South_Georgia","America/Noronha|Brazil/DeNoronha","America/Noronha|Etc/GMT+2","America/Panama|America/Atikokan","America/Panama|America/Cayman","America/Panama|America/Coral_Harbour","America/Panama|America/Jamaica","America/Panama|EST","America/Panama|Jamaica","America/Phoenix|America/Creston","America/Phoenix|America/Dawson_Creek","America/Phoenix|America/Hermosillo","America/Phoenix|MST","America/Phoenix|US/Arizona","America/Rio_Branco|America/Eirunepe","America/Rio_Branco|America/Porto_Acre","America/Rio_Branco|Brazil/Acre","America/Santiago|Chile/Continental","America/Santo_Domingo|America/Anguilla","America/Santo_Domingo|America/Antigua","America/Santo_Domingo|America/Aruba","America/Santo_Domingo|America/Barbados","America/Santo_Domingo|America/Blanc-Sablon","America/Santo_Domingo|America/Curacao","America/Santo_Domingo|America/Dominica","America/Santo_Domingo|America/Grenada","America/Santo_Domingo|America/Guadeloupe","America/Santo_Domingo|America/Kralendijk","America/Santo_Domingo|America/Lower_Princes","America/Santo_Domingo|America/Marigot","America/Santo_Domingo|America/Martinique","America/Santo_Domingo|America/Montserrat","America/Santo_Domingo|America/Port_of_Spain","America/Santo_Domingo|America/Puerto_Rico","America/Santo_Domingo|America/St_Barthelemy","America/Santo_Domingo|America/St_Kitts","America/Santo_Domingo|America/St_Lucia","America/Santo_Domingo|America/St_Thomas","America/Santo_Domingo|America/St_Vincent","America/Santo_Domingo|America/Tortola","America/Santo_Domingo|America/Virgin","America/Sao_Paulo|Brazil/East","America/St_Johns|Canada/Newfoundland","Antarctica/Palmer|America/Punta_Arenas","Asia/Baghdad|Antarctica/Syowa","Asia/Baghdad|Asia/Aden","Asia/Baghdad|Asia/Bahrain","Asia/Baghdad|Asia/Kuwait","Asia/Baghdad|Asia/Qatar","Asia/Baghdad|Asia/Riyadh","Asia/Baghdad|Etc/GMT-3","Asia/Baghdad|Europe/Minsk","Asia/Bangkok|Asia/Ho_Chi_Minh","Asia/Bangkok|Asia/Novokuznetsk","Asia/Bangkok|Asia/Phnom_Penh","Asia/Bangkok|Asia/Saigon","Asia/Bangkok|Asia/Vientiane","Asia/Bangkok|Etc/GMT-7","Asia/Bangkok|Indian/Christmas","Asia/Dhaka|Antarctica/Vostok","Asia/Dhaka|Asia/Almaty","Asia/Dhaka|Asia/Bishkek","Asia/Dhaka|Asia/Dacca","Asia/Dhaka|Asia/Kashgar","Asia/Dhaka|Asia/Qostanay","Asia/Dhaka|Asia/Thimbu","Asia/Dhaka|Asia/Thimphu","Asia/Dhaka|Asia/Urumqi","Asia/Dhaka|Etc/GMT-6","Asia/Dhaka|Indian/Chagos","Asia/Dili|Etc/GMT-9","Asia/Dili|Pacific/Palau","Asia/Dubai|Asia/Muscat","Asia/Dubai|Asia/Tbilisi","Asia/Dubai|Asia/Yerevan","Asia/Dubai|Etc/GMT-4","Asia/Dubai|Europe/Samara","Asia/Dubai|Indian/Mahe","Asia/Dubai|Indian/Mauritius","Asia/Dubai|Indian/Reunion","Asia/Gaza|Asia/Hebron","Asia/Hong_Kong|Hongkong","Asia/Jakarta|Asia/Pontianak","Asia/Jerusalem|Asia/Tel_Aviv","Asia/Jerusalem|Israel","Asia/Kamchatka|Asia/Anadyr","Asia/Kamchatka|Etc/GMT-12","Asia/Kamchatka|Kwajalein","Asia/Kamchatka|Pacific/Funafuti","Asia/Kamchatka|Pacific/Kwajalein","Asia/Kamchatka|Pacific/Majuro","Asia/Kamchatka|Pacific/Nauru","Asia/Kamchatka|Pacific/Tarawa","Asia/Kamchatka|Pacific/Wake","Asia/Kamchatka|Pacific/Wallis","Asia/Kathmandu|Asia/Katmandu","Asia/Kolkata|Asia/Calcutta","Asia/Kuala_Lumpur|Asia/Brunei","Asia/Kuala_Lumpur|Asia/Kuching","Asia/Kuala_Lumpur|Asia/Singapore","Asia/Kuala_Lumpur|Etc/GMT-8","Asia/Kuala_Lumpur|Singapore","Asia/Makassar|Asia/Ujung_Pandang","Asia/Rangoon|Asia/Yangon","Asia/Rangoon|Indian/Cocos","Asia/Seoul|ROK","Asia/Shanghai|Asia/Chongqing","Asia/Shanghai|Asia/Chungking","Asia/Shanghai|Asia/Harbin","Asia/Shanghai|Asia/Macao","Asia/Shanghai|Asia/Macau","Asia/Shanghai|Asia/Taipei","Asia/Shanghai|PRC","Asia/Shanghai|ROC","Asia/Tashkent|Antarctica/Mawson","Asia/Tashkent|Asia/Aqtau","Asia/Tashkent|Asia/Aqtobe","Asia/Tashkent|Asia/Ashgabat","Asia/Tashkent|Asia/Ashkhabad","Asia/Tashkent|Asia/Atyrau","Asia/Tashkent|Asia/Dushanbe","Asia/Tashkent|Asia/Oral","Asia/Tashkent|Asia/Samarkand","Asia/Tashkent|Etc/GMT-5","Asia/Tashkent|Indian/Kerguelen","Asia/Tashkent|Indian/Maldives","Asia/Tehran|Iran","Asia/Tokyo|Japan","Asia/Ulaanbaatar|Asia/Choibalsan","Asia/Ulaanbaatar|Asia/Ulan_Bator","Asia/Vladivostok|Asia/Ust-Nera","Asia/Yakutsk|Asia/Khandyga","Atlantic/Azores|America/Scoresbysund","Atlantic/Cape_Verde|Etc/GMT+1","Australia/Adelaide|Australia/Broken_Hill","Australia/Adelaide|Australia/South","Australia/Adelaide|Australia/Yancowinna","Australia/Brisbane|Australia/Lindeman","Australia/Brisbane|Australia/Queensland","Australia/Darwin|Australia/North","Australia/Lord_Howe|Australia/LHI","Australia/Perth|Australia/West","Australia/Sydney|Australia/ACT","Australia/Sydney|Australia/Canberra","Australia/Sydney|Australia/Currie","Australia/Sydney|Australia/Hobart","Australia/Sydney|Australia/Melbourne","Australia/Sydney|Australia/NSW","Australia/Sydney|Australia/Tasmania","Australia/Sydney|Australia/Victoria","Etc/UTC|Etc/UCT","Etc/UTC|Etc/Universal","Etc/UTC|Etc/Zulu","Etc/UTC|UCT","Etc/UTC|UTC","Etc/UTC|Universal","Etc/UTC|Zulu","Europe/Athens|Asia/Nicosia","Europe/Athens|EET","Europe/Athens|Europe/Bucharest","Europe/Athens|Europe/Helsinki","Europe/Athens|Europe/Kiev","Europe/Athens|Europe/Mariehamn","Europe/Athens|Europe/Nicosia","Europe/Athens|Europe/Riga","Europe/Athens|Europe/Sofia","Europe/Athens|Europe/Tallinn","Europe/Athens|Europe/Uzhgorod","Europe/Athens|Europe/Vilnius","Europe/Athens|Europe/Zaporozhye","Europe/Chisinau|Europe/Tiraspol","Europe/Dublin|Eire","Europe/Istanbul|Asia/Istanbul","Europe/Istanbul|Turkey","Europe/Lisbon|Atlantic/Canary","Europe/Lisbon|Atlantic/Faeroe","Europe/Lisbon|Atlantic/Faroe","Europe/Lisbon|Atlantic/Madeira","Europe/Lisbon|Portugal","Europe/Lisbon|WET","Europe/London|Europe/Belfast","Europe/London|Europe/Guernsey","Europe/London|Europe/Isle_of_Man","Europe/London|Europe/Jersey","Europe/London|GB","Europe/London|GB-Eire","Europe/Moscow|W-SU","Europe/Paris|Africa/Ceuta","Europe/Paris|Arctic/Longyearbyen","Europe/Paris|Atlantic/Jan_Mayen","Europe/Paris|CET","Europe/Paris|Europe/Amsterdam","Europe/Paris|Europe/Andorra","Europe/Paris|Europe/Belgrade","Europe/Paris|Europe/Berlin","Europe/Paris|Europe/Bratislava","Europe/Paris|Europe/Brussels","Europe/Paris|Europe/Budapest","Europe/Paris|Europe/Busingen","Europe/Paris|Europe/Copenhagen","Europe/Paris|Europe/Gibraltar","Europe/Paris|Europe/Ljubljana","Europe/Paris|Europe/Luxembourg","Europe/Paris|Europe/Madrid","Europe/Paris|Europe/Malta","Europe/Paris|Europe/Monaco","Europe/Paris|Europe/Oslo","Europe/Paris|Europe/Podgorica","Europe/Paris|Europe/Prague","Europe/Paris|Europe/Rome","Europe/Paris|Europe/San_Marino","Europe/Paris|Europe/Sarajevo","Europe/Paris|Europe/Skopje","Europe/Paris|Europe/Stockholm","Europe/Paris|Europe/Tirane","Europe/Paris|Europe/Vaduz","Europe/Paris|Europe/Vatican","Europe/Paris|Europe/Vienna","Europe/Paris|Europe/Warsaw","Europe/Paris|Europe/Zagreb","Europe/Paris|Europe/Zurich","Europe/Paris|Poland","Europe/Ulyanovsk|Europe/Astrakhan","Pacific/Auckland|Antarctica/McMurdo","Pacific/Auckland|Antarctica/South_Pole","Pacific/Auckland|NZ","Pacific/Chatham|NZ-CHAT","Pacific/Easter|Chile/EasterIsland","Pacific/Fakaofo|Etc/GMT-13","Pacific/Fakaofo|Pacific/Enderbury","Pacific/Galapagos|Etc/GMT+6","Pacific/Gambier|Etc/GMT+9","Pacific/Guadalcanal|Antarctica/Macquarie","Pacific/Guadalcanal|Etc/GMT-11","Pacific/Guadalcanal|Pacific/Efate","Pacific/Guadalcanal|Pacific/Kosrae","Pacific/Guadalcanal|Pacific/Noumea","Pacific/Guadalcanal|Pacific/Pohnpei","Pacific/Guadalcanal|Pacific/Ponape","Pacific/Guam|Pacific/Saipan","Pacific/Honolulu|HST","Pacific/Honolulu|Pacific/Johnston","Pacific/Honolulu|US/Hawaii","Pacific/Kiritimati|Etc/GMT-14","Pacific/Niue|Etc/GMT+11","Pacific/Pago_Pago|Pacific/Midway","Pacific/Pago_Pago|Pacific/Samoa","Pacific/Pago_Pago|US/Samoa","Pacific/Pitcairn|Etc/GMT+8","Pacific/Port_Moresby|Antarctica/DumontDUrville","Pacific/Port_Moresby|Etc/GMT-10","Pacific/Port_Moresby|Pacific/Chuuk","Pacific/Port_Moresby|Pacific/Truk","Pacific/Port_Moresby|Pacific/Yap","Pacific/Tahiti|Etc/GMT+10","Pacific/Tahiti|Pacific/Rarotonga"]}).zones,m=e.links,c={},i=u.map(function(a){var i=a.substr(0,a.indexOf("|"));return c[i]=a,i}),o=m.reduce(function(a,i){var e=i.split("|"),r=e[0];return a[e[1]]=r,a},{}),n={},a.convertDateToTime=function(a){var i=h(a),e=/\(([^)]+)\)$/.exec(a.toTimeString());return i.zone={abbreviation:e?e[1]:"???",offset:a.getTimezoneOffset()},z(i,a.getTime()),i},a.convertTimeToDate=function(a){var i=a.epoch;if(void 0!==i)return new Date(i);var e=(a.zone||{}).offset;if(void 0===e)return function(a){var i=a.year,e=a.month,r=a.day,A=a.hours,c=void 0===A?0:A,o=a.minutes,n=void 0===o?0:o,t=a.seconds,s=void 0===t?0:t,u=a.milliseconds;return new Date(i,e-1,r,c,n,s,void 0===u?0:u)}(a);var r=d(a);return new Date(r+6e4*e)},a.findTimeZone=function(a){var i=o[a]||a,e=n[i];if(!e){var r=c[i];if(!r)throw new Error('Unknown time zone "'+i+'".');e=n[i]=A(r)}return e},a.getUTCOffset=function(a,i){var e=T("number"==typeof a?a:a.getTime(),i);return{abbreviation:e.abbreviation,offset:e.offset}},a.getUnixTime=function(a,i){var e=a.zone,r=a.epoch;if(r){if(i)throw new Error("Both epoch and other time zone specified. Omit the other one.");return r}var A=d(a);if(e){if(i)throw new Error("Both own and other time zones specified. Omit the other one.")}else{if(!i)throw new Error("Missing other time zone.");e=T(A,i)}return A+6e4*e.offset},a.getZonedTime=function(a,i){var e="number"==typeof a,r=e?a:a.getTime(),A=T(r,i),c=A.abbreviation,o=A.offset;(e||o)&&(a=new Date(r-6e4*o));var n=E(a);return n.zone={abbreviation:c,offset:o},z(n,r),n},a.listTimeZones=function(){return i.slice()},a.setTimeZone=function(a,i,e){if(a instanceof Date)a=function(a,i){var e,r=(i||{}).useUTC;if(!0===r)e=E;else{if(!1!==r)throw new Error("Extract local or UTC date? Set useUTC option.");e=h}return e(a)}(a,e);else{var r=a,A=r.year,c=r.month,o=r.day,n=r.hours,t=r.minutes,s=r.seconds,u=void 0===s?0:s,m=r.milliseconds;a={year:A,month:c,day:o,hours:n,minutes:t,seconds:u,milliseconds:void 0===m?0:m}}var l=d(a),f=new Date(l);a.dayOfWeek=f.getUTCDay();var p=T(l,i),M=p.abbreviation,b=p.offset;return a.zone={abbreviation:M,offset:b},z(a,l+6e4*b),a},Object.defineProperty(a,"__esModule",{value:!0})});
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.dayjs_plugin_timezone=e()}(this,function(){"use strict";var t={year:0,month:1,day:2,hour:3,minute:4,second:5},e={};return function(n,i,o){var r,u=o().utcOffset(),a=function(t,n,i){void 0===i&&(i={});var o=new Date(t);return function(t,n){void 0===n&&(n={});var i=n.timeZoneName||"short",o=t+"|"+i,r=e[o];return r||(r=new Intl.DateTimeFormat("en-US",{hour12:!1,timeZone:t,year:"numeric",month:"2-digit",day:"2-digit",hour:"2-digit",minute:"2-digit",second:"2-digit",timeZoneName:i}),e[o]=r),r}(n,i).formatToParts(o)},f=function(e,n){for(var i=a(e,n),r=[],u=0;u<i.length;u+=1){var f=i[u],s=f.type,m=f.value,c=t[s];c>=0&&(r[c]=parseInt(m,10))}var d=r[3],v=24===d?0:d,h=r[0]+"-"+r[1]+"-"+r[2]+" "+v+":"+r[4]+":"+r[5]+":000",l=+e;return(o.utc(h).valueOf()-(l-=l%1e3))/6e4},s=i.prototype;s.tz=function(t,e){void 0===t&&(t=r);var n=this.utcOffset(),i=this.toDate().toLocaleString("en-US",{timeZone:t}),a=Math.round((this.toDate()-new Date(i))/1e3/60),f=o(i).$set("millisecond",this.$ms).utcOffset(u-a,!0);if(e){var s=f.utcOffset();f=f.add(n-s,"minute")}return f.$x.$timezone=t,f},s.offsetName=function(t){var e=this.$x.$timezone||o.tz.guess(),n=a(this.valueOf(),e,{timeZoneName:t}).find(function(t){return"timezonename"===t.type.toLowerCase()});return n&&n.value},o.tz=function(t,e,n){var i=n&&e,u=n||e||r,a=f(+o(),u);if("string"!=typeof t)return o(t).tz(u);var s=function(t,e,n){var i=t-60*e*1e3,o=f(i,n);if(e===o)return[i,e];var r=f(i-=60*(o-e)*1e3,n);return o===r?[i,o]:[t-60*Math.min(o,r)*1e3,Math.max(o,r)]}(o.utc(t,i).valueOf(),a,u),m=s[0],c=s[1],d=o(m).utcOffset(c);return d.$x.$timezone=u,d},o.tz.guess=function(){return Intl.DateTimeFormat().resolvedOptions().timeZone},o.tz.setDefault=function(t){r=t}}});

!function(t,i){"object"==typeof exports&&"undefined"!=typeof module?module.exports=i():"function"==typeof define&&define.amd?define(i):t.dayjs_plugin_utc=i()}(this,function(){"use strict";return function(t,i,e){var s=i.prototype;e.utc=function(t){return new i({date:t,utc:!0,args:arguments})},s.utc=function(t){var i=e(this.toDate(),{locale:this.$L,utc:!0});return t?i.add(this.utcOffset(),"minute"):i},s.local=function(){return e(this.toDate(),{locale:this.$L,utc:!1})};var f=s.parse;s.parse=function(t){t.utc&&(this.$u=!0),this.$utils().u(t.$offset)||(this.$offset=t.$offset),f.call(this,t)};var n=s.init;s.init=function(){if(this.$u){var t=this.$d;this.$y=t.getUTCFullYear(),this.$M=t.getUTCMonth(),this.$D=t.getUTCDate(),this.$W=t.getUTCDay(),this.$H=t.getUTCHours(),this.$m=t.getUTCMinutes(),this.$s=t.getUTCSeconds(),this.$ms=t.getUTCMilliseconds()}else n.call(this)};var u=s.utcOffset;s.utcOffset=function(t,i){var e=this.$utils().u;if(e(t))return this.$u?0:e(this.$offset)?u.call(this):this.$offset;var s=Math.abs(t)<=16?60*t:t,f=this;if(i)return f.$offset=s,f.$u=0===t,f;if(0!==t){var n=this.$u?this.toDate().getTimezoneOffset():-1*this.utcOffset();(f=this.local().add(s+n,"minute")).$offset=s,f.$x.$localOffset=n}else f=this.utc();return f};var o=s.format;s.format=function(t){var i=t||(this.$u?"YYYY-MM-DDTHH:mm:ss[Z]":"");return o.call(this,i)},s.valueOf=function(){var t=this.$utils().u(this.$offset)?0:this.$offset+(this.$x.$localOffset||(new Date).getTimezoneOffset());return this.$d.valueOf()-6e4*t},s.isUTC=function(){return!!this.$u},s.toISOString=function(){return this.toDate().toISOString()},s.toString=function(){return this.toDate().toUTCString()};var r=s.toDate;s.toDate=function(t){return"s"===t&&this.$offset?e(this.format("YYYY-MM-DD HH:mm:ss:SSS")).toDate():r.call(this)};var a=s.diff;s.diff=function(t,i,s){if(this.$u===t.$u)return a.call(this,t,i,s);var f=this.local(),n=e(t).local();return a.call(f,n,i,s)}}});

/**
 * JavaScript Client Detection
 * (C) viazenetti GmbH (Christian Ludwig)
 */
(function (window) {
	{
		var unknown = '-';

		// screen
		var screenSize = '';
		if (screen.width) {
			width = (screen.width) ? screen.width : '';
			height = (screen.height) ? screen.height : '';
			screenSize += '' + width + " x " + height;
		}

		// browser
		var nVer = navigator.appVersion;
		var nAgt = navigator.userAgent;
		var browser = navigator.appName;
		var version = '' + parseFloat(navigator.appVersion);
		var majorVersion = parseInt(navigator.appVersion, 10);
		var nameOffset, verOffset, ix;

		// Opera
		if ((verOffset = nAgt.indexOf('Opera')) != -1) {
			browser = 'Opera';
			version = nAgt.substring(verOffset + 6);
			if ((verOffset = nAgt.indexOf('Version')) != -1) {
				version = nAgt.substring(verOffset + 8);
			}
		}
		// Opera Next
		if ((verOffset = nAgt.indexOf('OPR')) != -1) {
			browser = 'Opera';
			version = nAgt.substring(verOffset + 4);
		}
		// Legacy Edge
		else if ((verOffset = nAgt.indexOf('Edge')) != -1) {
			browser = 'Edge';
			version = nAgt.substring(verOffset + 5);
		}
		// Edge (Chromium)
		else if ((verOffset = nAgt.indexOf('Edg')) != -1) {
			browser = 'Microsoft Edge';
			version = nAgt.substring(verOffset + 4);
		}
		// MSIE
		else if ((verOffset = nAgt.indexOf('MSIE')) != -1) {
			browser = 'Internet';
			version = nAgt.substring(verOffset + 5);
		}
		// Chrome
		else if ((verOffset = nAgt.indexOf('Chrome')) != -1) {
			browser = 'Chrome';
			version = nAgt.substring(verOffset + 7);
		}
		// Safari
		else if ((verOffset = nAgt.indexOf('Safari')) != -1) {
			browser = 'Safari';
			version = nAgt.substring(verOffset + 7);
			if ((verOffset = nAgt.indexOf('Version')) != -1) {
				version = nAgt.substring(verOffset + 8);
			}
		}
		// Firefox
		else if ((verOffset = nAgt.indexOf('Firefox')) != -1) {
			browser = 'Firefox';
			version = nAgt.substring(verOffset + 8);
		}
		// MSIE 11+
		else if (nAgt.indexOf('Trident/') != -1) {
			browser = 'Internet';
			version = nAgt.substring(nAgt.indexOf('rv:') + 3);
		}
		// Other browsers
		else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
			browser = nAgt.substring(nameOffset, verOffset);
			version = nAgt.substring(verOffset + 1);
			if (browser.toLowerCase() == browser.toUpperCase()) {
				browser = navigator.appName;
			}
		}
		// trim the version string
		if ((ix = version.indexOf(';')) != -1) version = version.substring(0, ix);
		if ((ix = version.indexOf(' ')) != -1) version = version.substring(0, ix);
		if ((ix = version.indexOf(')')) != -1) version = version.substring(0, ix);

		majorVersion = parseInt('' + version, 10);
		if (isNaN(majorVersion)) {
			version = '' + parseFloat(navigator.appVersion);
			majorVersion = parseInt(navigator.appVersion, 10);
		}

		// mobile version
		var mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer);

		// system
		var os = unknown;
		var clientStrings = [
			{s:'Windows 10', r:/(Windows 10.0|Windows NT 10.0)/},
			{s:'Windows 8.1', r:/(Windows 8.1|Windows NT 6.3)/},
			{s:'Windows 8', r:/(Windows 8|Windows NT 6.2)/},
			{s:'Windows 7', r:/(Windows 7|Windows NT 6.1)/},
			{s:'Windows Vista', r:/Windows NT 6.0/},
			{s:'Windows Server 2003', r:/Windows NT 5.2/},
			{s:'Windows XP', r:/(Windows NT 5.1|Windows XP)/},
			{s:'Windows 2000', r:/(Windows NT 5.0|Windows 2000)/},
			{s:'Windows ME', r:/(Win 9x 4.90|Windows ME)/},
			{s:'Windows 98', r:/(Windows 98|Win98)/},
			{s:'Windows 95', r:/(Windows 95|Win95|Windows_95)/},
			{s:'Windows NT 4.0', r:/(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
			{s:'Windows CE', r:/Windows CE/},
			{s:'Windows 3.11', r:/Win16/},
			{s:'Android', r:/Android/},
			{s:'Open BSD', r:/OpenBSD/},
			{s:'Sun OS', r:/SunOS/},
			{s:'Chrome OS', r:/CrOS/},
			{s:'Linux', r:/(Linux|X11(?!.*CrOS))/},
			{s:'iOS', r:/(iPhone|iPad|iPod)/},
			{s:'Mac OS X', r:/Mac OS X/},
			{s:'Mac OS', r:/(Mac OS|MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
			{s:'QNX', r:/QNX/},
			{s:'UNIX', r:/UNIX/},
			{s:'BeOS', r:/BeOS/},
			{s:'OS/2', r:/OS\/2/},
			{s:'Search Bot', r:/(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
		];
		for (var id in clientStrings) {
			var cs = clientStrings[id];
			if (cs.r.test(nAgt)) {
				os = cs.s;
				break;
			}
		}

		var osVersion = unknown;

		if (/Windows/.test(os)) {
			osVersion = /Windows (.*)/.exec(os)[1];
			os = 'Windows';
		}

		switch (os) {
			case 'Mac OS':
			case 'Mac OS X':
			case 'Android':
				osVersion = /(?:Android|Mac OS|Mac OS X|MacPPC|MacIntel|Mac_PowerPC|Macintosh) ([\.\_\d]+)/.exec(nAgt)[1];
				break;

			case 'iOS':
				osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
				osVersion = osVersion[1] + '.' + osVersion[2] + '.' + (osVersion[3] | 0);
				break;
		}

		// flash (you'll need to include swfobject)
		/* script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" */
		var flashVersion = 'no check';
		if (typeof swfobject != 'undefined') {
			var fv = swfobject.getFlashPlayerVersion();
			if (fv.major > 0) {
				flashVersion = fv.major + '.' + fv.minor + ' r' + fv.release;
			}
			else  {
				flashVersion = unknown;
			}
		}
	}

	window.jscd = {
		screen: screenSize,
		browser: browser,
		browserVersion: version,
		browserMajorVersion: majorVersion,
		mobile: mobile,
		os: os,
		osVersion: osVersion,
		flashVersion: flashVersion
	};
}(this));

(function($) {

	var $html = $('html');

	$html.addClass('browser-' + jscd.browser);
	$html.addClass('platform-' + jscd.os);

})(jQuery);
/*!
 * Flickity PACKAGED v2.2.0
 * Touch, responsive, flickable carousels
 *
 * Licensed GPLv3 for open source use
 * or Flickity Commercial License for commercial use
 *
 * https://flickity.metafizzy.co
 * Copyright 2015-2018 Metafizzy
 */

/**
 * Bridget makes jQuery widgets
 * v2.0.1
 * MIT license
 */

/* jshint browser: true, strict: true, undef: true, unused: true */

(function (window, factory) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('jquery-bridget/jquery-bridget', ['jquery'], function (jQuery) {
			return factory(window, jQuery);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('jquery')
		);
	} else {
		// browser global
		window.jQueryBridget = factory(
			window,
			window.jQuery
		);
	}

}(window, function factory(window, jQuery) {
	'use strict';

	// ----- utils ----- //

	var arraySlice = Array.prototype.slice;

	// helper function for logging errors
	// $.error breaks jQuery chaining
	var console = window.console;
	var logError = typeof console == 'undefined' ? function () { } :
		function (message) {
			console.error(message);
		};

	// ----- jQueryBridget ----- //

	function jQueryBridget(namespace, PluginClass, $) {
		$ = $ || jQuery || window.jQuery;
		if (!$) {
			return;
		}

		// add option method -> $().plugin('option', {...})
		if (!PluginClass.prototype.option) {
			// option setter
			PluginClass.prototype.option = function (opts) {
				// bail out if not an object
				if (!$.isPlainObject(opts)) {
					return;
				}
				this.options = $.extend(true, this.options, opts);
			};
		}

		// make jQuery plugin
		$.fn[namespace] = function (arg0 /*, arg1 */) {
			if (typeof arg0 == 'string') {
				// method call $().plugin( 'methodName', { options } )
				// shift arguments by 1
				var args = arraySlice.call(arguments, 1);
				return methodCall(this, arg0, args);
			}
			// just $().plugin({ options })
			plainCall(this, arg0);
			return this;
		};

		// $().plugin('methodName')
		function methodCall($elems, methodName, args) {
			var returnValue;
			var pluginMethodStr = '$().' + namespace + '("' + methodName + '")';

			$elems.each(function (i, elem) {
				// get instance
				var instance = $.data(elem, namespace);
				if (!instance) {
					logError(namespace + ' not initialized. Cannot call methods, i.e. ' +
						pluginMethodStr);
					return;
				}

				var method = instance[methodName];
				if (!method || methodName.charAt(0) == '_') {
					logError(pluginMethodStr + ' is not a valid method');
					return;
				}

				// apply method, get return value
				var value = method.apply(instance, args);
				// set return value if value is returned, use only first value
				returnValue = returnValue === undefined ? value : returnValue;
			});

			return returnValue !== undefined ? returnValue : $elems;
		}

		function plainCall($elems, options) {
			$elems.each(function (i, elem) {
				var instance = $.data(elem, namespace);
				if (instance) {
					// set options & init
					instance.option(options);
					instance._init();
				} else {
					// initialize new instance
					instance = new PluginClass(elem, options);
					$.data(elem, namespace, instance);
				}
			});
		}

		updateJQuery($);

	}

	// ----- updateJQuery ----- //

	// set $.bridget for v1 backwards compatibility
	function updateJQuery($) {
		if (!$ || ($ && $.bridget)) {
			return;
		}
		$.bridget = jQueryBridget;
	}

	updateJQuery(jQuery || window.jQuery);

	// -----  ----- //

	return jQueryBridget;

}));

/**
 * EvEmitter v1.1.0
 * Lil' event emitter
 * MIT License
 */

/* jshint unused: true, undef: true, strict: true */

(function (global, factory) {
	// universal module definition
	/* jshint strict: false */ /* globals define, module, window */
	if (typeof define == 'function' && define.amd) {
		// AMD - RequireJS
		define('ev-emitter/ev-emitter', factory);
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS - Browserify, Webpack
		module.exports = factory();
	} else {
		// Browser globals
		global.EvEmitter = factory();
	}

}(typeof window != 'undefined' ? window : this, function () {



	function EvEmitter() { }

	var proto = EvEmitter.prototype;

	proto.on = function (eventName, listener) {
		if (!eventName || !listener) {
			return;
		}
		// set events hash
		var events = this._events = this._events || {};
		// set listeners array
		var listeners = events[eventName] = events[eventName] || [];
		// only add once
		if (listeners.indexOf(listener) == -1) {
			listeners.push(listener);
		}

		return this;
	};

	proto.once = function (eventName, listener) {
		if (!eventName || !listener) {
			return;
		}
		// add event
		this.on(eventName, listener);
		// set once flag
		// set onceEvents hash
		var onceEvents = this._onceEvents = this._onceEvents || {};
		// set onceListeners object
		var onceListeners = onceEvents[eventName] = onceEvents[eventName] || {};
		// set flag
		onceListeners[listener] = true;

		return this;
	};

	proto.off = function (eventName, listener) {
		var listeners = this._events && this._events[eventName];
		if (!listeners || !listeners.length) {
			return;
		}
		var index = listeners.indexOf(listener);
		if (index != -1) {
			listeners.splice(index, 1);
		}

		return this;
	};

	proto.emitEvent = function (eventName, args) {
		var listeners = this._events && this._events[eventName];
		if (!listeners || !listeners.length) {
			return;
		}
		// copy over to avoid interference if .off() in listener
		listeners = listeners.slice(0);
		args = args || [];
		// once stuff
		var onceListeners = this._onceEvents && this._onceEvents[eventName];

		for (var i = 0; i < listeners.length; i++) {
			var listener = listeners[i]
			var isOnce = onceListeners && onceListeners[listener];
			if (isOnce) {
				// remove listener
				// remove before trigger to prevent recursion
				this.off(eventName, listener);
				// unset once flag
				delete onceListeners[listener];
			}
			// trigger listener
			listener.apply(this, args);
		}

		return this;
	};

	proto.allOff = function () {
		delete this._events;
		delete this._onceEvents;
	};

	return EvEmitter;

}));

/*!
 * getSize v2.0.3
 * measure size of elements
 * MIT license
 */

/* jshint browser: true, strict: true, undef: true, unused: true */
/* globals console: false */

(function (window, factory) {
	/* jshint strict: false */ /* globals define, module */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('get-size/get-size', factory);
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory();
	} else {
		// browser global
		window.getSize = factory();
	}

})(window, function factory() {
	'use strict';

	// -------------------------- helpers -------------------------- //

	// get a number from a string, not a percentage
	function getStyleSize(value) {
		var num = parseFloat(value);
		// not a percent like '100%', and a number
		var isValid = value.indexOf('%') == -1 && !isNaN(num);
		return isValid && num;
	}

	function noop() { }

	var logError = typeof console == 'undefined' ? noop :
		function (message) {
			console.error(message);
		};

	// -------------------------- measurements -------------------------- //

	var measurements = [
		'paddingLeft',
		'paddingRight',
		'paddingTop',
		'paddingBottom',
		'marginLeft',
		'marginRight',
		'marginTop',
		'marginBottom',
		'borderLeftWidth',
		'borderRightWidth',
		'borderTopWidth',
		'borderBottomWidth'
	];

	var measurementsLength = measurements.length;

	function getZeroSize() {
		var size = {
			width: 0,
			height: 0,
			innerWidth: 0,
			innerHeight: 0,
			outerWidth: 0,
			outerHeight: 0
		};
		for (var i = 0; i < measurementsLength; i++) {
			var measurement = measurements[i];
			size[measurement] = 0;
		}
		return size;
	}

	// -------------------------- getStyle -------------------------- //

	/**
	 * getStyle, get style of element, check for Firefox bug
	 * https://bugzilla.mozilla.org/show_bug.cgi?id=548397
	 */
	function getStyle(elem) {
		var style = getComputedStyle(elem);
		if (!style) {
			logError('Style returned ' + style +
				'. Are you running this code in a hidden iframe on Firefox? ' +
				'See https://bit.ly/getsizebug1');
		}
		return style;
	}

	// -------------------------- setup -------------------------- //

	var isSetup = false;

	var isBoxSizeOuter;

	/**
	 * setup
	 * check isBoxSizerOuter
	 * do on first getSize() rather than on page load for Firefox bug
	 */
	function setup() {
		// setup once
		if (isSetup) {
			return;
		}
		isSetup = true;

		// -------------------------- box sizing -------------------------- //

		/**
		 * Chrome & Safari measure the outer-width on style.width on border-box elems
		 * IE11 & Firefox<29 measures the inner-width
		 */
		var div = document.createElement('div');
		div.style.width = '200px';
		div.style.padding = '1px 2px 3px 4px';
		div.style.borderStyle = 'solid';
		div.style.borderWidth = '1px 2px 3px 4px';
		div.style.boxSizing = 'border-box';

		var body = document.body || document.documentElement;
		body.appendChild(div);
		var style = getStyle(div);
		// round value for browser zoom. desandro/masonry#928
		isBoxSizeOuter = Math.round(getStyleSize(style.width)) == 200;
		getSize.isBoxSizeOuter = isBoxSizeOuter;

		body.removeChild(div);
	}

	// -------------------------- getSize -------------------------- //

	function getSize(elem) {
		setup();

		// use querySeletor if elem is string
		if (typeof elem == 'string') {
			elem = document.querySelector(elem);
		}

		// do not proceed on non-objects
		if (!elem || typeof elem != 'object' || !elem.nodeType) {
			return;
		}

		var style = getStyle(elem);

		// if hidden, everything is 0
		if (style.display == 'none') {
			return getZeroSize();
		}

		var size = {};
		size.width = elem.offsetWidth;
		size.height = elem.offsetHeight;

		var isBorderBox = size.isBorderBox = style.boxSizing == 'border-box';

		// get all measurements
		for (var i = 0; i < measurementsLength; i++) {
			var measurement = measurements[i];
			var value = style[measurement];
			var num = parseFloat(value);
			// any 'auto', 'medium' value will be 0
			size[measurement] = !isNaN(num) ? num : 0;
		}

		var paddingWidth = size.paddingLeft + size.paddingRight;
		var paddingHeight = size.paddingTop + size.paddingBottom;
		var marginWidth = size.marginLeft + size.marginRight;
		var marginHeight = size.marginTop + size.marginBottom;
		var borderWidth = size.borderLeftWidth + size.borderRightWidth;
		var borderHeight = size.borderTopWidth + size.borderBottomWidth;

		var isBorderBoxSizeOuter = isBorderBox && isBoxSizeOuter;

		// overwrite width and height if we can get it from style
		var styleWidth = getStyleSize(style.width);
		if (styleWidth !== false) {
			size.width = styleWidth +
				// add padding and border unless it's already including it
				(isBorderBoxSizeOuter ? 0 : paddingWidth + borderWidth);
		}

		var styleHeight = getStyleSize(style.height);
		if (styleHeight !== false) {
			size.height = styleHeight +
				// add padding and border unless it's already including it
				(isBorderBoxSizeOuter ? 0 : paddingHeight + borderHeight);
		}

		size.innerWidth = size.width - (paddingWidth + borderWidth);
		size.innerHeight = size.height - (paddingHeight + borderHeight);

		size.outerWidth = size.width + marginWidth;
		size.outerHeight = size.height + marginHeight;

		return size;
	}

	return getSize;

});

/**
 * matchesSelector v2.0.2
 * matchesSelector( element, '.selector' )
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true, unused: true */

(function (window, factory) {
	/*global define: false, module: false */
	'use strict';
	// universal module definition
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('desandro-matches-selector/matches-selector', factory);
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory();
	} else {
		// browser global
		window.matchesSelector = factory();
	}

}(window, function factory() {
	'use strict';

	var matchesMethod = (function () {
		var ElemProto = window.Element.prototype;
		// check for the standard method name first
		if (ElemProto.matches) {
			return 'matches';
		}
		// check un-prefixed
		if (ElemProto.matchesSelector) {
			return 'matchesSelector';
		}
		// check vendor prefixes
		var prefixes = ['webkit', 'moz', 'ms', 'o'];

		for (var i = 0; i < prefixes.length; i++) {
			var prefix = prefixes[i];
			var method = prefix + 'MatchesSelector';
			if (ElemProto[method]) {
				return method;
			}
		}
	})();

	return function matchesSelector(elem, selector) {
		return elem[matchesMethod](selector);
	};

}));

/**
 * Fizzy UI utils v2.0.7
 * MIT license
 */

/*jshint browser: true, undef: true, unused: true, strict: true */

(function (window, factory) {
	// universal module definition
	/*jshint strict: false */ /*globals define, module, require */

	if (typeof define == 'function' && define.amd) {
		// AMD
		define('fizzy-ui-utils/utils', [
			'desandro-matches-selector/matches-selector'
		], function (matchesSelector) {
			return factory(window, matchesSelector);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('desandro-matches-selector')
		);
	} else {
		// browser global
		window.fizzyUIUtils = factory(
			window,
			window.matchesSelector
		);
	}

}(window, function factory(window, matchesSelector) {



	var utils = {};

	// ----- extend ----- //

	// extends objects
	utils.extend = function (a, b) {
		for (var prop in b) {
			a[prop] = b[prop];
		}
		return a;
	};

	// ----- modulo ----- //

	utils.modulo = function (num, div) {
		return ((num % div) + div) % div;
	};

	// ----- makeArray ----- //

	var arraySlice = Array.prototype.slice;

	// turn element or nodeList into an array
	utils.makeArray = function (obj) {
		if (Array.isArray(obj)) {
			// use object if already an array
			return obj;
		}
		// return empty array if undefined or null. #6
		if (obj === null || obj === undefined) {
			return [];
		}

		var isArrayLike = typeof obj == 'object' && typeof obj.length == 'number';
		if (isArrayLike) {
			// convert nodeList to array
			return arraySlice.call(obj);
		}

		// array of single index
		return [obj];
	};

	// ----- removeFrom ----- //

	utils.removeFrom = function (ary, obj) {
		var index = ary.indexOf(obj);
		if (index != -1) {
			ary.splice(index, 1);
		}
	};

	// ----- getParent ----- //

	utils.getParent = function (elem, selector) {
		while (elem.parentNode && elem != document.body) {
			elem = elem.parentNode;
			if (matchesSelector(elem, selector)) {
				return elem;
			}
		}
	};

	// ----- getQueryElement ----- //

	// use element as selector string
	utils.getQueryElement = function (elem) {
		if (typeof elem == 'string') {
			return document.querySelector(elem);
		}
		return elem;
	};

	// ----- handleEvent ----- //

	// enable .ontype to trigger from .addEventListener( elem, 'type' )
	utils.handleEvent = function (event) {
		var method = 'on' + event.type;
		if (this[method]) {
			this[method](event);
		}
	};

	// ----- filterFindElements ----- //

	utils.filterFindElements = function (elems, selector) {
		// make array of elems
		elems = utils.makeArray(elems);
		var ffElems = [];

		var isElement = function (elem) {
			return (
				typeof HTMLElement === "object" ? elem instanceof HTMLElement : elem && typeof elem === "object" && elem !== null && elem.nodeType === 1 && typeof elem.nodeName === "string"
			);
		};

		elems.forEach(function (elem) {
			// check that elem is an actual element
			// if (!(elem instanceof HTMLElement)) {
			if (!isElement(elem)) {
				return;
			}
			// add elem if no selector
			if (!selector) {
				ffElems.push(elem);
				return;
			}
			// filter & find items if we have a selector
			// filter
			if (matchesSelector(elem, selector)) {
				ffElems.push(elem);
			}
			// find children
			var childElems = elem.querySelectorAll(selector);
			// concat childElems to filterFound array
			for (var i = 0; i < childElems.length; i++) {
				ffElems.push(childElems[i]);
			}
		});

		return ffElems;
	};

	// ----- debounceMethod ----- //

	utils.debounceMethod = function (_class, methodName, threshold) {
		threshold = threshold || 100;
		// original method
		var method = _class.prototype[methodName];
		var timeoutName = methodName + 'Timeout';

		_class.prototype[methodName] = function () {
			var timeout = this[timeoutName];
			clearTimeout(timeout);

			var args = arguments;
			var _this = this;
			this[timeoutName] = setTimeout(function () {
				method.apply(_this, args);
				delete _this[timeoutName];
			}, threshold);
		};
	};

	// ----- docReady ----- //

	utils.docReady = function (callback) {
		var readyState = document.readyState;
		if (readyState == 'complete' || readyState == 'interactive') {
			// do async to allow for other scripts to run. metafizzy/flickity#441
			setTimeout(callback);
		} else {
			document.addEventListener('DOMContentLoaded', callback);
		}
	};

	// ----- htmlInit ----- //

	// http://jamesroberts.name/blog/2010/02/22/string-functions-for-javascript-trim-to-camel-case-to-dashed-and-to-underscore/
	utils.toDashed = function (str) {
		return str.replace(/(.)([A-Z])/g, function (match, $1, $2) {
			return $1 + '-' + $2;
		}).toLowerCase();
	};

	var console = window.console;
	/**
	 * allow user to initialize classes via [data-namespace] or .js-namespace class
	 * htmlInit( Widget, 'widgetName' )
	 * options are parsed from data-namespace-options
	 */
	utils.htmlInit = function (WidgetClass, namespace) {
		utils.docReady(function () {
			var dashedNamespace = utils.toDashed(namespace);
			var dataAttr = 'data-' + dashedNamespace;
			var dataAttrElems = document.querySelectorAll('[' + dataAttr + ']');
			var jsDashElems = document.querySelectorAll('.js-' + dashedNamespace);
			var elems = utils.makeArray(dataAttrElems)
				.concat(utils.makeArray(jsDashElems));
			var dataOptionsAttr = dataAttr + '-options';
			var jQuery = window.jQuery;

			elems.forEach(function (elem) {
				var attr = elem.getAttribute(dataAttr) ||
					elem.getAttribute(dataOptionsAttr);
				var options;
				try {
					options = attr && JSON.parse(attr);
				} catch (error) {
					// log error, do not initialize
					if (console) {
						console.error('Error parsing ' + dataAttr + ' on ' + elem.className +
							': ' + error);
					}
					return;
				}
				// initialize
				var instance = new WidgetClass(elem, options);
				// make available via $().data('namespace')
				if (jQuery) {
					jQuery.data(elem, namespace, instance);
				}
			});

		});
	};

	// -----  ----- //

	return utils;

}));

// Flickity.Cell
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/cell', [
			'get-size/get-size'
		], function (getSize) {
			return factory(window, getSize);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('get-size')
		);
	} else {
		// browser global
		window.Flickity = window.Flickity || {};
		window.Flickity.Cell = factory(
			window,
			window.getSize
		);
	}

}(window, function factory(window, getSize) {



	function Cell(elem, parent) {
		this.element = elem;
		this.parent = parent;

		this.create();
	}

	var proto = Cell.prototype;

	proto.create = function () {
		this.element.style.position = 'absolute';
		this.element.setAttribute('aria-hidden', 'true');
		this.x = 0;
		this.shift = 0;
	};

	proto.destroy = function () {
		// reset style
		this.unselect();
		this.element.style.position = '';
		var side = this.parent.originSide;
		this.element.style[side] = '';
	};

	proto.getSize = function () {
		this.size = getSize(this.element);
	};

	proto.setPosition = function (x) {
		this.x = x;
		this.updateTarget();
		this.renderPosition(x);
	};

	// setDefaultTarget v1 method, backwards compatibility, remove in v3
	proto.updateTarget = proto.setDefaultTarget = function () {
		var marginProperty = this.parent.originSide == 'left' ? 'marginLeft' : 'marginRight';
		this.target = this.x + this.size[marginProperty] +
			this.size.width * this.parent.cellAlign;
	};

	proto.renderPosition = function (x) {
		// render position of cell with in slider
		var side = this.parent.originSide;
		this.element.style[side] = this.parent.getPositionValue(x);
	};

	proto.select = function () {
		this.element.classList.add('is-selected');
		this.element.removeAttribute('aria-hidden');
	};

	proto.unselect = function () {
		this.element.classList.remove('is-selected');
		this.element.setAttribute('aria-hidden', 'true');
	};

	/**
	 * @param {Integer} factor - 0, 1, or -1
	**/
	proto.wrapShift = function (shift) {
		this.shift = shift;
		this.renderPosition(this.x + this.parent.slideableWidth * shift);
	};

	proto.remove = function () {
		this.element.parentNode.removeChild(this.element);
	};

	return Cell;

}));

// slide
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/slide', factory);
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory();
	} else {
		// browser global
		window.Flickity = window.Flickity || {};
		window.Flickity.Slide = factory();
	}

}(window, function factory() {
	'use strict';

	function Slide(parent) {
		this.parent = parent;
		this.isOriginLeft = parent.originSide == 'left';
		this.cells = [];
		this.outerWidth = 0;
		this.height = 0;
	}

	var proto = Slide.prototype;

	proto.addCell = function (cell) {
		this.cells.push(cell);
		this.outerWidth += cell.size.outerWidth;
		this.height = Math.max(cell.size.outerHeight, this.height);
		// first cell stuff
		if (this.cells.length == 1) {
			this.x = cell.x; // x comes from first cell
			var beginMargin = this.isOriginLeft ? 'marginLeft' : 'marginRight';
			this.firstMargin = cell.size[beginMargin];
		}
	};

	proto.updateTarget = function () {
		var endMargin = this.isOriginLeft ? 'marginRight' : 'marginLeft';
		var lastCell = this.getLastCell();
		var lastMargin = lastCell ? lastCell.size[endMargin] : 0;
		var slideWidth = this.outerWidth - (this.firstMargin + lastMargin);
		this.target = this.x + this.firstMargin + slideWidth * this.parent.cellAlign;
	};

	proto.getLastCell = function () {
		return this.cells[this.cells.length - 1];
	};

	proto.select = function () {
		this.cells.forEach(function (cell) {
			cell.select();
		});
	};

	proto.unselect = function () {
		this.cells.forEach(function (cell) {
			cell.unselect();
		});
	};

	proto.getCellElements = function () {
		return this.cells.map(function (cell) {
			return cell.element;
		});
	};

	return Slide;

}));

// animate
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/animate', [
			'fizzy-ui-utils/utils'
		], function (utils) {
			return factory(window, utils);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		window.Flickity = window.Flickity || {};
		window.Flickity.animatePrototype = factory(
			window,
			window.fizzyUIUtils
		);
	}

}(window, function factory(window, utils) {



	// -------------------------- animate -------------------------- //

	var proto = {};

	proto.startAnimation = function () {
		if (this.isAnimating) {
			return;
		}

		this.isAnimating = true;
		this.restingFrames = 0;
		this.animate();
	};

	proto.animate = function () {
		this.applyDragForce();
		this.applySelectedAttraction();

		var previousX = this.x;

		this.integratePhysics();
		this.positionSlider();
		this.settle(previousX);
		// animate next frame
		if (this.isAnimating) {
			var _this = this;
			requestAnimationFrame(function animateFrame() {
				_this.animate();
			});
		}
	};

	proto.positionSlider = function () {
		var x = this.x;
		// wrap position around
		if (this.options.wrapAround && this.cells.length > 1) {
			x = utils.modulo(x, this.slideableWidth);
			x = x - this.slideableWidth;
			this.shiftWrapCells(x);
		}

		this.setTranslateX(x, this.isAnimating);
		this.dispatchScrollEvent();
	};

	proto.setTranslateX = function (x, is3d) {
		x += this.cursorPosition;
		// reverse if right-to-left and using transform
		x = this.options.rightToLeft ? -x : x;
		var translateX = this.getPositionValue(x);
		// use 3D tranforms for hardware acceleration on iOS
		// but use 2D when settled, for better font-rendering
		this.slider.style.transform = is3d ?
			'translate3d(' + translateX + ',0,0)' : 'translateX(' + translateX + ')';
	};

	proto.dispatchScrollEvent = function () {
		var firstSlide = this.slides[0];
		if (!firstSlide) {
			return;
		}
		var positionX = -this.x - firstSlide.target;
		var progress = positionX / this.slidesWidth;
		this.dispatchEvent('scroll', null, [progress, positionX]);
	};

	proto.positionSliderAtSelected = function () {
		if (!this.cells.length) {
			return;
		}
		this.x = -this.selectedSlide.target;
		this.velocity = 0; // stop wobble
		this.positionSlider();
	};

	proto.getPositionValue = function (position) {
		if (this.options.percentPosition) {
			// percent position, round to 2 digits, like 12.34%
			return (Math.round((position / this.size.innerWidth) * 10000) * 0.01) + '%';
		} else {
			// pixel positioning
			return Math.round(position) + 'px';
		}
	};

	proto.settle = function (previousX) {
		// keep track of frames where x hasn't moved
		if (!this.isPointerDown && Math.round(this.x * 100) == Math.round(previousX * 100)) {
			this.restingFrames++;
		}
		// stop animating if resting for 3 or more frames
		if (this.restingFrames > 2) {
			this.isAnimating = false;
			delete this.isFreeScrolling;
			// render position with translateX when settled
			this.positionSlider();
			this.dispatchEvent('settle', null, [this.selectedIndex]);
		}
	};

	proto.shiftWrapCells = function (x) {
		// shift before cells
		var beforeGap = this.cursorPosition + x;
		this._shiftCells(this.beforeShiftCells, beforeGap, -1);
		// shift after cells
		var afterGap = this.size.innerWidth - (x + this.slideableWidth + this.cursorPosition);
		this._shiftCells(this.afterShiftCells, afterGap, 1);
	};

	proto._shiftCells = function (cells, gap, shift) {
		for (var i = 0; i < cells.length; i++) {
			var cell = cells[i];
			var cellShift = gap > 0 ? shift : 0;
			cell.wrapShift(cellShift);
			gap -= cell.size.outerWidth;
		}
	};

	proto._unshiftCells = function (cells) {
		if (!cells || !cells.length) {
			return;
		}
		for (var i = 0; i < cells.length; i++) {
			cells[i].wrapShift(0);
		}
	};

	// -------------------------- physics -------------------------- //

	proto.integratePhysics = function () {
		this.x += this.velocity;
		this.velocity *= this.getFrictionFactor();
	};

	proto.applyForce = function (force) {
		this.velocity += force;
	};

	proto.getFrictionFactor = function () {
		return 1 - this.options[this.isFreeScrolling ? 'freeScrollFriction' : 'friction'];
	};

	proto.getRestingPosition = function () {
		// my thanks to Steven Wittens, who simplified this math greatly
		return this.x + this.velocity / (1 - this.getFrictionFactor());
	};

	proto.applyDragForce = function () {
		if (!this.isDraggable || !this.isPointerDown) {
			return;
		}
		// change the position to drag position by applying force
		var dragVelocity = this.dragX - this.x;
		var dragForce = dragVelocity - this.velocity;
		this.applyForce(dragForce);
	};

	proto.applySelectedAttraction = function () {
		// do not attract if pointer down or no slides
		var dragDown = this.isDraggable && this.isPointerDown;
		if (dragDown || this.isFreeScrolling || !this.slides.length) {
			return;
		}
		var distance = this.selectedSlide.target * -1 - this.x;
		var force = distance * this.options.selectedAttraction;
		this.applyForce(force);
	};

	return proto;

}));

// Flickity main
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/flickity', [
			'ev-emitter/ev-emitter',
			'get-size/get-size',
			'fizzy-ui-utils/utils',
			'./cell',
			'./slide',
			'./animate'
		], function (EvEmitter, getSize, utils, Cell, Slide, animatePrototype) {
			return factory(window, EvEmitter, getSize, utils, Cell, Slide, animatePrototype);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('ev-emitter'),
			require('get-size'),
			require('fizzy-ui-utils'),
			require('./cell'),
			require('./slide'),
			require('./animate')
		);
	} else {
		// browser global
		var _Flickity = window.Flickity;

		window.Flickity = factory(
			window,
			window.EvEmitter,
			window.getSize,
			window.fizzyUIUtils,
			_Flickity.Cell,
			_Flickity.Slide,
			_Flickity.animatePrototype
		);
	}

}(window, function factory(window, EvEmitter, getSize,
	utils, Cell, Slide, animatePrototype) {



	// vars
	var jQuery = window.jQuery;
	var getComputedStyle = window.getComputedStyle;
	var console = window.console;

	function moveElements(elems, toElem) {
		elems = utils.makeArray(elems);
		while (elems.length) {
			toElem.appendChild(elems.shift());
		}
	}

	// -------------------------- Flickity -------------------------- //

	// globally unique identifiers
	var GUID = 0;
	// internal store of all Flickity intances
	var instances = {};

	function Flickity(element, options) {
		var queryElement = utils.getQueryElement(element);
		if (!queryElement) {
			if (console) {
				console.error('Bad element for Flickity: ' + (queryElement || element));
			}
			return;
		}
		this.element = queryElement;
		// do not initialize twice on same element
		if (this.element.flickityGUID) {
			var instance = instances[this.element.flickityGUID];
			instance.option(options);
			return instance;
		}

		// add jQuery
		if (jQuery) {
			this.$element = jQuery(this.element);
		}
		// options
		this.options = utils.extend({}, this.constructor.defaults);
		this.option(options);

		// kick things off
		this._create();
	}

	Flickity.defaults = {
		accessibility: true,
		// adaptiveHeight: false,
		cellAlign: 'center',
		// cellSelector: undefined,
		// contain: false,
		freeScrollFriction: 0.075, // friction when free-scrolling
		friction: 0.28, // friction when selecting
		namespaceJQueryEvents: true,
		// initialIndex: 0,
		percentPosition: true,
		resize: true,
		selectedAttraction: 0.025,
		setGallerySize: true
		// watchCSS: false,
		// wrapAround: false
	};

	// hash of methods triggered on _create()
	Flickity.createMethods = [];

	var proto = Flickity.prototype;
	// inherit EventEmitter
	utils.extend(proto, EvEmitter.prototype);

	proto._create = function () {
		// add id for Flickity.data
		var id = this.guid = ++GUID;
		this.element.flickityGUID = id; // expando
		instances[id] = this; // associate via id
		// initial properties
		this.selectedIndex = 0;
		// how many frames slider has been in same position
		this.restingFrames = 0;
		// initial physics properties
		this.x = 0;
		this.velocity = 0;
		this.originSide = this.options.rightToLeft ? 'right' : 'left';
		// create viewport & slider
		this.viewport = document.createElement('div');
		this.viewport.className = 'flickity-viewport';
		this._createSlider();

		if (this.options.resize || this.options.watchCSS) {
			window.addEventListener('resize', this);
		}

		// add listeners from on option
		for (var eventName in this.options.on) {
			var listener = this.options.on[eventName];
			this.on(eventName, listener);
		}

		Flickity.createMethods.forEach(function (method) {
			this[method]();
		}, this);

		if (this.options.watchCSS) {
			this.watchCSS();
		} else {
			this.activate();
		}

	};

	/**
	 * set options
	 * @param {Object} opts
	 */
	proto.option = function (opts) {
		utils.extend(this.options, opts);
	};

	proto.activate = function () {
		if (this.isActive) {
			return;
		}
		this.isActive = true;
		this.element.classList.add('flickity-enabled');
		if (this.options.rightToLeft) {
			this.element.classList.add('flickity-rtl');
		}

		this.getSize();
		// move initial cell elements so they can be loaded as cells
		var cellElems = this._filterFindCellElements(this.element.children);
		moveElements(cellElems, this.slider);
		this.viewport.appendChild(this.slider);
		this.element.appendChild(this.viewport);
		// get cells from children
		this.reloadCells();

		if (this.options.accessibility) {
			// allow element to focusable
			this.element.tabIndex = 0;
			// listen for key presses
			this.element.addEventListener('keydown', this);
		}

		this.emitEvent('activate');
		this.selectInitialIndex();
		// flag for initial activation, for using initialIndex
		this.isInitActivated = true;
		// ready event. #493
		this.dispatchEvent('ready');
	};

	// slider positions the cells
	proto._createSlider = function () {
		// slider element does all the positioning
		var slider = document.createElement('div');
		slider.className = 'flickity-slider';
		slider.style[this.originSide] = 0;
		this.slider = slider;
	};

	proto._filterFindCellElements = function (elems) {
		return utils.filterFindElements(elems, this.options.cellSelector);
	};

	// goes through all children
	proto.reloadCells = function () {
		// collection of item elements
		this.cells = this._makeCells(this.slider.children);
		this.positionCells();
		this._getWrapShiftCells();
		this.setGallerySize();
	};

	/**
	 * turn elements into Flickity.Cells
	 * @param {Array or NodeList or HTMLElement} elems
	 * @returns {Array} items - collection of new Flickity Cells
	 */
	proto._makeCells = function (elems) {
		var cellElems = this._filterFindCellElements(elems);

		// create new Flickity for collection
		var cells = cellElems.map(function (cellElem) {
			return new Cell(cellElem, this);
		}, this);

		return cells;
	};

	proto.getLastCell = function () {
		return this.cells[this.cells.length - 1];
	};

	proto.getLastSlide = function () {
		return this.slides[this.slides.length - 1];
	};

	// positions all cells
	proto.positionCells = function () {
		// size all cells
		this._sizeCells(this.cells);
		// position all cells
		this._positionCells(0);
	};

	/**
	 * position certain cells
	 * @param {Integer} index - which cell to start with
	 */
	proto._positionCells = function (index) {
		index = index || 0;
		// also measure maxCellHeight
		// start 0 if positioning all cells
		this.maxCellHeight = index ? this.maxCellHeight || 0 : 0;
		var cellX = 0;
		// get cellX
		if (index > 0) {
			var startCell = this.cells[index - 1];
			cellX = startCell.x + startCell.size.outerWidth;
		}
		var len = this.cells.length;
		for (var i = index; i < len; i++) {
			var cell = this.cells[i];
			cell.setPosition(cellX);
			cellX += cell.size.outerWidth;
			this.maxCellHeight = Math.max(cell.size.outerHeight, this.maxCellHeight);
		}
		// keep track of cellX for wrap-around
		this.slideableWidth = cellX;
		// slides
		this.updateSlides();
		// contain slides target
		this._containSlides();
		// update slidesWidth
		this.slidesWidth = len ? this.getLastSlide().target - this.slides[0].target : 0;
	};

	/**
	 * cell.getSize() on multiple cells
	 * @param {Array} cells
	 */
	proto._sizeCells = function (cells) {
		cells.forEach(function (cell) {
			cell.getSize();
		});
	};

	// --------------------------  -------------------------- //

	proto.updateSlides = function () {
		this.slides = [];
		if (!this.cells.length) {
			return;
		}

		var slide = new Slide(this);
		this.slides.push(slide);
		var isOriginLeft = this.originSide == 'left';
		var nextMargin = isOriginLeft ? 'marginRight' : 'marginLeft';

		var canCellFit = this._getCanCellFit();

		this.cells.forEach(function (cell, i) {
			// just add cell if first cell in slide
			if (!slide.cells.length) {
				slide.addCell(cell);
				return;
			}

			var slideWidth = (slide.outerWidth - slide.firstMargin) +
				(cell.size.outerWidth - cell.size[nextMargin]);

			if (canCellFit.call(this, i, slideWidth)) {
				slide.addCell(cell);
			} else {
				// doesn't fit, new slide
				slide.updateTarget();

				slide = new Slide(this);
				this.slides.push(slide);
				slide.addCell(cell);
			}
		}, this);
		// last slide
		slide.updateTarget();
		// update .selectedSlide
		this.updateSelectedSlide();
	};

	proto._getCanCellFit = function () {
		var groupCells = this.options.groupCells;
		if (!groupCells) {
			return function () {
				return false;
			};
		} else if (typeof groupCells == 'number') {
			// group by number. 3 -> [0,1,2], [3,4,5], ...
			var number = parseInt(groupCells, 10);
			return function (i) {
				return (i % number) !== 0;
			};
		}
		// default, group by width of slide
		// parse '75%
		var percentMatch = typeof groupCells == 'string' &&
			groupCells.match(/^(\d+)%$/);
		var percent = percentMatch ? parseInt(percentMatch[1], 10) / 100 : 1;
		return function (i, slideWidth) {
			return slideWidth <= (this.size.innerWidth + 1) * percent;
		};
	};

	// alias _init for jQuery plugin .flickity()
	proto._init =
		proto.reposition = function () {
			this.positionCells();
			this.positionSliderAtSelected();
		};

	proto.getSize = function () {
		this.size = getSize(this.element);
		this.setCellAlign();
		this.cursorPosition = this.size.innerWidth * this.cellAlign;
	};

	var cellAlignShorthands = {
		// cell align, then based on origin side
		center: {
			left: 0.5,
			right: 0.5
		},
		left: {
			left: 0,
			right: 1
		},
		right: {
			right: 0,
			left: 1
		}
	};

	proto.setCellAlign = function () {
		var shorthand = cellAlignShorthands[this.options.cellAlign];
		this.cellAlign = shorthand ? shorthand[this.originSide] : this.options.cellAlign;
	};

	proto.setGallerySize = function () {
		if (this.options.setGallerySize) {
			var height = this.options.adaptiveHeight && this.selectedSlide ?
				this.selectedSlide.height : this.maxCellHeight;
			this.viewport.style.height = height + 'px';
		}
	};

	proto._getWrapShiftCells = function () {
		// only for wrap-around
		if (!this.options.wrapAround) {
			return;
		}
		// unshift previous cells
		this._unshiftCells(this.beforeShiftCells);
		this._unshiftCells(this.afterShiftCells);
		// get before cells
		// initial gap
		var gapX = this.cursorPosition;
		var cellIndex = this.cells.length - 1;
		this.beforeShiftCells = this._getGapCells(gapX, cellIndex, -1);
		// get after cells
		// ending gap between last cell and end of gallery viewport
		gapX = this.size.innerWidth - this.cursorPosition;
		// start cloning at first cell, working forwards
		this.afterShiftCells = this._getGapCells(gapX, 0, 1);
	};

	proto._getGapCells = function (gapX, cellIndex, increment) {
		// keep adding cells until the cover the initial gap
		var cells = [];
		while (gapX > 0) {
			var cell = this.cells[cellIndex];
			if (!cell) {
				break;
			}
			cells.push(cell);
			cellIndex += increment;
			gapX -= cell.size.outerWidth;
		}
		return cells;
	};

	// ----- contain ----- //

	// contain cell targets so no excess sliding
	proto._containSlides = function () {
		if (!this.options.contain || this.options.wrapAround || !this.cells.length) {
			return;
		}
		var isRightToLeft = this.options.rightToLeft;
		var beginMargin = isRightToLeft ? 'marginRight' : 'marginLeft';
		var endMargin = isRightToLeft ? 'marginLeft' : 'marginRight';
		var contentWidth = this.slideableWidth - this.getLastCell().size[endMargin];
		// content is less than gallery size
		var isContentSmaller = contentWidth < this.size.innerWidth;
		// bounds
		var beginBound = this.cursorPosition + this.cells[0].size[beginMargin];
		var endBound = contentWidth - this.size.innerWidth * (1 - this.cellAlign);
		// contain each cell target
		this.slides.forEach(function (slide) {
			if (isContentSmaller) {
				// all cells fit inside gallery
				slide.target = contentWidth * this.cellAlign;
			} else {
				// contain to bounds
				slide.target = Math.max(slide.target, beginBound);
				slide.target = Math.min(slide.target, endBound);
			}
		}, this);
	};

	// -----  ----- //

	/**
	 * emits events via eventEmitter and jQuery events
	 * @param {String} type - name of event
	 * @param {Event} event - original event
	 * @param {Array} args - extra arguments
	 */
	proto.dispatchEvent = function (type, event, args) {
		var emitArgs = event ? [event].concat(args) : args;
		this.emitEvent(type, emitArgs);

		if (jQuery && this.$element) {
			// default trigger with type if no event
			type += this.options.namespaceJQueryEvents ? '.flickity' : '';
			var $event = type;
			if (event) {
				// create jQuery event
				var jQEvent = jQuery.Event(event);
				jQEvent.type = type;
				$event = jQEvent;
			}
			this.$element.trigger($event, args);
		}
	};

	// -------------------------- select -------------------------- //

	/**
	 * @param {Integer} index - index of the slide
	 * @param {Boolean} isWrap - will wrap-around to last/first if at the end
	 * @param {Boolean} isInstant - will immediately set position at selected cell
	 */
	proto.select = function (index, isWrap, isInstant) {
		if (!this.isActive) {
			return;
		}
		index = parseInt(index, 10);
		this._wrapSelect(index);

		if (this.options.wrapAround || isWrap) {
			index = utils.modulo(index, this.slides.length);
		}
		// bail if invalid index
		if (!this.slides[index]) {
			return;
		}
		var prevIndex = this.selectedIndex;
		this.selectedIndex = index;
		this.updateSelectedSlide();
		if (isInstant) {
			this.positionSliderAtSelected();
		} else {
			this.startAnimation();
		}
		if (this.options.adaptiveHeight) {
			this.setGallerySize();
		}
		// events
		this.dispatchEvent('select', null, [index]);
		// change event if new index
		if (index != prevIndex) {
			this.dispatchEvent('change', null, [index]);
		}
		// old v1 event name, remove in v3
		this.dispatchEvent('cellSelect');
	};

	// wraps position for wrapAround, to move to closest slide. #113
	proto._wrapSelect = function (index) {
		var len = this.slides.length;
		var isWrapping = this.options.wrapAround && len > 1;
		if (!isWrapping) {
			return index;
		}
		var wrapIndex = utils.modulo(index, len);
		// go to shortest
		var delta = Math.abs(wrapIndex - this.selectedIndex);
		var backWrapDelta = Math.abs((wrapIndex + len) - this.selectedIndex);
		var forewardWrapDelta = Math.abs((wrapIndex - len) - this.selectedIndex);
		if (!this.isDragSelect && backWrapDelta < delta) {
			index += len;
		} else if (!this.isDragSelect && forewardWrapDelta < delta) {
			index -= len;
		}
		// wrap position so slider is within normal area
		if (index < 0) {
			this.x -= this.slideableWidth;
		} else if (index >= len) {
			this.x += this.slideableWidth;
		}
	};

	proto.previous = function (isWrap, isInstant) {
		this.select(this.selectedIndex - 1, isWrap, isInstant);
	};

	proto.next = function (isWrap, isInstant) {
		this.select(this.selectedIndex + 1, isWrap, isInstant);
	};

	proto.updateSelectedSlide = function () {
		var slide = this.slides[this.selectedIndex];
		// selectedIndex could be outside of slides, if triggered before resize()
		if (!slide) {
			return;
		}
		// unselect previous selected slide
		this.unselectSelectedSlide();
		// update new selected slide
		this.selectedSlide = slide;
		slide.select();
		this.selectedCells = slide.cells;
		this.selectedElements = slide.getCellElements();
		// HACK: selectedCell & selectedElement is first cell in slide, backwards compatibility
		// Remove in v3?
		this.selectedCell = slide.cells[0];
		this.selectedElement = this.selectedElements[0];
	};

	proto.unselectSelectedSlide = function () {
		if (this.selectedSlide) {
			this.selectedSlide.unselect();
		}
	};

	proto.selectInitialIndex = function () {
		var initialIndex = this.options.initialIndex;
		// already activated, select previous selectedIndex
		if (this.isInitActivated) {
			this.select(this.selectedIndex, false, true);
			return;
		}
		// select with selector string
		if (initialIndex && typeof initialIndex == 'string') {
			var cell = this.queryCell(initialIndex);
			if (cell) {
				this.selectCell(initialIndex, false, true);
				return;
			}
		}

		var index = 0;
		// select with number
		if (initialIndex && this.slides[initialIndex]) {
			index = initialIndex;
		}
		// select instantly
		this.select(index, false, true);
	};

	/**
	 * select slide from number or cell element
	 * @param {Element or Number} elem
	 */
	proto.selectCell = function (value, isWrap, isInstant) {
		// get cell
		var cell = this.queryCell(value);
		if (!cell) {
			return;
		}

		var index = this.getCellSlideIndex(cell);
		this.select(index, isWrap, isInstant);
	};

	proto.getCellSlideIndex = function (cell) {
		// get index of slides that has cell
		for (var i = 0; i < this.slides.length; i++) {
			var slide = this.slides[i];
			var index = slide.cells.indexOf(cell);
			if (index != -1) {
				return i;
			}
		}
	};

	// -------------------------- get cells -------------------------- //

	/**
	 * get Flickity.Cell, given an Element
	 * @param {Element} elem
	 * @returns {Flickity.Cell} item
	 */
	proto.getCell = function (elem) {
		// loop through cells to get the one that matches
		for (var i = 0; i < this.cells.length; i++) {
			var cell = this.cells[i];
			if (cell.element == elem) {
				return cell;
			}
		}
	};

	/**
	 * get collection of Flickity.Cells, given Elements
	 * @param {Element, Array, NodeList} elems
	 * @returns {Array} cells - Flickity.Cells
	 */
	proto.getCells = function (elems) {
		elems = utils.makeArray(elems);
		var cells = [];
		elems.forEach(function (elem) {
			var cell = this.getCell(elem);
			if (cell) {
				cells.push(cell);
			}
		}, this);
		return cells;
	};

	/**
	 * get cell elements
	 * @returns {Array} cellElems
	 */
	proto.getCellElements = function () {
		return this.cells.map(function (cell) {
			return cell.element;
		});
	};

	/**
	 * get parent cell from an element
	 * @param {Element} elem
	 * @returns {Flickit.Cell} cell
	 */
	proto.getParentCell = function (elem) {
		// first check if elem is cell
		var cell = this.getCell(elem);
		if (cell) {
			return cell;
		}
		// try to get parent cell elem
		elem = utils.getParent(elem, '.flickity-slider > *');
		return this.getCell(elem);
	};

	/**
	 * get cells adjacent to a slide
	 * @param {Integer} adjCount - number of adjacent slides
	 * @param {Integer} index - index of slide to start
	 * @returns {Array} cells - array of Flickity.Cells
	 */
	proto.getAdjacentCellElements = function (adjCount, index) {
		if (!adjCount) {
			return this.selectedSlide.getCellElements();
		}
		index = index === undefined ? this.selectedIndex : index;

		var len = this.slides.length;
		if (1 + (adjCount * 2) >= len) {
			return this.getCellElements();
		}

		var cellElems = [];
		for (var i = index - adjCount; i <= index + adjCount; i++) {
			var slideIndex = this.options.wrapAround ? utils.modulo(i, len) : i;
			var slide = this.slides[slideIndex];
			if (slide) {
				cellElems = cellElems.concat(slide.getCellElements());
			}
		}
		return cellElems;
	};

	/**
	 * select slide from number or cell element
	 * @param {Element, Selector String, or Number} selector
	 */
	proto.queryCell = function (selector) {
		if (typeof selector == 'number') {
			// use number as index
			return this.cells[selector];
		}
		if (typeof selector == 'string') {
			// do not select invalid selectors from hash: #123, #/. #791
			if (selector.match(/^[#\.]?[\d\/]/)) {
				return;
			}
			// use string as selector, get element
			selector = this.element.querySelector(selector);
		}
		// get cell from element
		return this.getCell(selector);
	};

	// -------------------------- events -------------------------- //

	proto.uiChange = function () {
		this.emitEvent('uiChange');
	};

	// keep focus on element when child UI elements are clicked
	proto.childUIPointerDown = function (event) {
		// HACK iOS does not allow touch events to bubble up?!
		if (event.type != 'touchstart') {
			event.preventDefault();
		}
		this.focus();
	};

	// ----- resize ----- //

	proto.onresize = function () {
		this.watchCSS();
		this.resize();
	};

	utils.debounceMethod(Flickity, 'onresize', 150);

	proto.resize = function () {
		if (!this.isActive) {
			return;
		}
		this.getSize();
		// wrap values
		if (this.options.wrapAround) {
			this.x = utils.modulo(this.x, this.slideableWidth);
		}
		this.positionCells();
		this._getWrapShiftCells();
		this.setGallerySize();
		this.emitEvent('resize');
		// update selected index for group slides, instant
		// TODO: position can be lost between groups of various numbers
		var selectedElement = this.selectedElements && this.selectedElements[0];
		this.selectCell(selectedElement, false, true);
	};

	// watches the :after property, activates/deactivates
	proto.watchCSS = function () {
		var watchOption = this.options.watchCSS;
		if (!watchOption) {
			return;
		}

		var afterContent = getComputedStyle(this.element, ':after').content;
		// activate if :after { content: 'flickity' }
		if (afterContent.indexOf('flickity') != -1) {
			this.activate();
		} else {
			this.deactivate();
		}
	};

	// ----- keydown ----- //

	// go previous/next if left/right keys pressed
	proto.onkeydown = function (event) {
		// only work if element is in focus
		var isNotFocused = document.activeElement && document.activeElement != this.element;
		if (!this.options.accessibility || isNotFocused) {
			return;
		}

		var handler = Flickity.keyboardHandlers[event.keyCode];
		if (handler) {
			handler.call(this);
		}
	};

	Flickity.keyboardHandlers = {
		// left arrow
		37: function () {
			var leftMethod = this.options.rightToLeft ? 'next' : 'previous';
			this.uiChange();
			this[leftMethod]();
		},
		// right arrow
		39: function () {
			var rightMethod = this.options.rightToLeft ? 'previous' : 'next';
			this.uiChange();
			this[rightMethod]();
		},
	};

	// ----- focus ----- //

	proto.focus = function () {
		// TODO remove scrollTo once focus options gets more support
		// https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/focus#Browser_compatibility
		var prevScrollY = window.pageYOffset;
		this.element.focus({ preventScroll: true });
		// hack to fix scroll jump after focus, #76
		if (window.pageYOffset != prevScrollY) {
			window.scrollTo(window.pageXOffset, prevScrollY);
		}
	};

	// -------------------------- destroy -------------------------- //

	// deactivate all Flickity functionality, but keep stuff available
	proto.deactivate = function () {
		if (!this.isActive) {
			return;
		}
		this.element.classList.remove('flickity-enabled');
		this.element.classList.remove('flickity-rtl');
		this.unselectSelectedSlide();
		// destroy cells
		this.cells.forEach(function (cell) {
			cell.destroy();
		});
		this.element.removeChild(this.viewport);
		// move child elements back into element
		moveElements(this.slider.children, this.element);
		if (this.options.accessibility) {
			this.element.removeAttribute('tabIndex');
			this.element.removeEventListener('keydown', this);
		}
		// set flags
		this.isActive = false;
		this.emitEvent('deactivate');
	};

	proto.destroy = function () {
		this.deactivate();
		window.removeEventListener('resize', this);
		this.allOff();
		this.emitEvent('destroy');
		if (jQuery && this.$element) {
			jQuery.removeData(this.element, 'flickity');
		}
		delete this.element.flickityGUID;
		delete instances[this.guid];
	};

	// -------------------------- prototype -------------------------- //

	utils.extend(proto, animatePrototype);

	// -------------------------- extras -------------------------- //

	/**
	 * get Flickity instance from element
	 * @param {Element} elem
	 * @returns {Flickity}
	 */
	Flickity.data = function (elem) {
		elem = utils.getQueryElement(elem);
		var id = elem && elem.flickityGUID;
		return id && instances[id];
	};

	utils.htmlInit(Flickity, 'flickity');

	if (jQuery && jQuery.bridget) {
		jQuery.bridget('flickity', Flickity);
	}

	// set internal jQuery, for Webpack + jQuery v3, #478
	Flickity.setJQuery = function (jq) {
		jQuery = jq;
	};

	Flickity.Cell = Cell;
	Flickity.Slide = Slide;

	return Flickity;

}));

/*!
 * Unipointer v2.3.0
 * base class for doing one thing with pointer event
 * MIT license
 */

/*jshint browser: true, undef: true, unused: true, strict: true */

(function (window, factory) {
	// universal module definition
	/* jshint strict: false */ /*global define, module, require */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('unipointer/unipointer', [
			'ev-emitter/ev-emitter'
		], function (EvEmitter) {
			return factory(window, EvEmitter);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('ev-emitter')
		);
	} else {
		// browser global
		window.Unipointer = factory(
			window,
			window.EvEmitter
		);
	}

}(window, function factory(window, EvEmitter) {



	function noop() { }

	function Unipointer() { }

	// inherit EvEmitter
	var proto = Unipointer.prototype = Object.create(EvEmitter.prototype);

	proto.bindStartEvent = function (elem) {
		this._bindStartEvent(elem, true);
	};

	proto.unbindStartEvent = function (elem) {
		this._bindStartEvent(elem, false);
	};

	/**
	 * Add or remove start event
	 * @param {Boolean} isAdd - remove if falsey
	 */
	proto._bindStartEvent = function (elem, isAdd) {
		// munge isAdd, default to true
		isAdd = isAdd === undefined ? true : isAdd;
		var bindMethod = isAdd ? 'addEventListener' : 'removeEventListener';

		// default to mouse events
		var startEvent = 'mousedown';
		if (window.PointerEvent) {
			// Pointer Events
			startEvent = 'pointerdown';
		} else if ('ontouchstart' in window) {
			// Touch Events. iOS Safari
			startEvent = 'touchstart';
		}
		elem[bindMethod](startEvent, this);
	};

	// trigger handler methods for events
	proto.handleEvent = function (event) {
		var method = 'on' + event.type;
		if (this[method]) {
			this[method](event);
		}
	};

	// returns the touch that we're keeping track of
	proto.getTouch = function (touches) {
		for (var i = 0; i < touches.length; i++) {
			var touch = touches[i];
			if (touch.identifier == this.pointerIdentifier) {
				return touch;
			}
		}
	};

	// ----- start event ----- //

	proto.onmousedown = function (event) {
		// dismiss clicks from right or middle buttons
		var button = event.button;
		if (button && (button !== 0 && button !== 1)) {
			return;
		}
		this._pointerDown(event, event);
	};

	proto.ontouchstart = function (event) {
		this._pointerDown(event, event.changedTouches[0]);
	};

	proto.onpointerdown = function (event) {
		this._pointerDown(event, event);
	};

	/**
	 * pointer start
	 * @param {Event} event
	 * @param {Event or Touch} pointer
	 */
	proto._pointerDown = function (event, pointer) {
		// dismiss right click and other pointers
		// button = 0 is okay, 1-4 not
		if (event.button || this.isPointerDown) {
			return;
		}

		this.isPointerDown = true;
		// save pointer identifier to match up touch events
		this.pointerIdentifier = pointer.pointerId !== undefined ?
			// pointerId for pointer events, touch.indentifier for touch events
			pointer.pointerId : pointer.identifier;

		this.pointerDown(event, pointer);
	};

	proto.pointerDown = function (event, pointer) {
		this._bindPostStartEvents(event);
		this.emitEvent('pointerDown', [event, pointer]);
	};

	// hash of events to be bound after start event
	var postStartEvents = {
		mousedown: ['mousemove', 'mouseup'],
		touchstart: ['touchmove', 'touchend', 'touchcancel'],
		pointerdown: ['pointermove', 'pointerup', 'pointercancel'],
	};

	proto._bindPostStartEvents = function (event) {
		if (!event) {
			return;
		}
		// get proper events to match start event
		var events = postStartEvents[event.type];
		// bind events to node
		events.forEach(function (eventName) {
			window.addEventListener(eventName, this);
		}, this);
		// save these arguments
		this._boundPointerEvents = events;
	};

	proto._unbindPostStartEvents = function () {
		// check for _boundEvents, in case dragEnd triggered twice (old IE8 bug)
		if (!this._boundPointerEvents) {
			return;
		}
		this._boundPointerEvents.forEach(function (eventName) {
			window.removeEventListener(eventName, this);
		}, this);

		delete this._boundPointerEvents;
	};

	// ----- move event ----- //

	proto.onmousemove = function (event) {
		this._pointerMove(event, event);
	};

	proto.onpointermove = function (event) {
		if (event.pointerId == this.pointerIdentifier) {
			this._pointerMove(event, event);
		}
	};

	proto.ontouchmove = function (event) {
		var touch = this.getTouch(event.changedTouches);
		if (touch) {
			this._pointerMove(event, touch);
		}
	};

	/**
	 * pointer move
	 * @param {Event} event
	 * @param {Event or Touch} pointer
	 * @private
	 */
	proto._pointerMove = function (event, pointer) {
		this.pointerMove(event, pointer);
	};

	// public
	proto.pointerMove = function (event, pointer) {
		this.emitEvent('pointerMove', [event, pointer]);
	};

	// ----- end event ----- //


	proto.onmouseup = function (event) {
		this._pointerUp(event, event);
	};

	proto.onpointerup = function (event) {
		if (event.pointerId == this.pointerIdentifier) {
			this._pointerUp(event, event);
		}
	};

	proto.ontouchend = function (event) {
		var touch = this.getTouch(event.changedTouches);
		if (touch) {
			this._pointerUp(event, touch);
		}
	};

	/**
	 * pointer up
	 * @param {Event} event
	 * @param {Event or Touch} pointer
	 * @private
	 */
	proto._pointerUp = function (event, pointer) {
		this._pointerDone();
		this.pointerUp(event, pointer);
	};

	// public
	proto.pointerUp = function (event, pointer) {
		this.emitEvent('pointerUp', [event, pointer]);
	};

	// ----- pointer done ----- //

	// triggered on pointer up & pointer cancel
	proto._pointerDone = function () {
		this._pointerReset();
		this._unbindPostStartEvents();
		this.pointerDone();
	};

	proto._pointerReset = function () {
		// reset properties
		this.isPointerDown = false;
		delete this.pointerIdentifier;
	};

	proto.pointerDone = noop;

	// ----- pointer cancel ----- //

	proto.onpointercancel = function (event) {
		if (event.pointerId == this.pointerIdentifier) {
			this._pointerCancel(event, event);
		}
	};

	proto.ontouchcancel = function (event) {
		var touch = this.getTouch(event.changedTouches);
		if (touch) {
			this._pointerCancel(event, touch);
		}
	};

	/**
	 * pointer cancel
	 * @param {Event} event
	 * @param {Event or Touch} pointer
	 * @private
	 */
	proto._pointerCancel = function (event, pointer) {
		this._pointerDone();
		this.pointerCancel(event, pointer);
	};

	// public
	proto.pointerCancel = function (event, pointer) {
		this.emitEvent('pointerCancel', [event, pointer]);
	};

	// -----  ----- //

	// utility function for getting x/y coords from event
	Unipointer.getPointerPoint = function (pointer) {
		return {
			x: pointer.pageX,
			y: pointer.pageY
		};
	};

	// -----  ----- //

	return Unipointer;

}));

/*!
 * Unidragger v2.3.0
 * Draggable base class
 * MIT license
 */

/*jshint browser: true, unused: true, undef: true, strict: true */

(function (window, factory) {
	// universal module definition
	/*jshint strict: false */ /*globals define, module, require */

	if (typeof define == 'function' && define.amd) {
		// AMD
		define('unidragger/unidragger', [
			'unipointer/unipointer'
		], function (Unipointer) {
			return factory(window, Unipointer);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('unipointer')
		);
	} else {
		// browser global
		window.Unidragger = factory(
			window,
			window.Unipointer
		);
	}

}(window, function factory(window, Unipointer) {



	// -------------------------- Unidragger -------------------------- //

	function Unidragger() { }

	// inherit Unipointer & EvEmitter
	var proto = Unidragger.prototype = Object.create(Unipointer.prototype);

	// ----- bind start ----- //

	proto.bindHandles = function () {
		this._bindHandles(true);
	};

	proto.unbindHandles = function () {
		this._bindHandles(false);
	};

	/**
	 * Add or remove start event
	 * @param {Boolean} isAdd
	 */
	proto._bindHandles = function (isAdd) {
		// munge isAdd, default to true
		isAdd = isAdd === undefined ? true : isAdd;
		// bind each handle
		var bindMethod = isAdd ? 'addEventListener' : 'removeEventListener';
		var touchAction = isAdd ? this._touchActionValue : '';
		for (var i = 0; i < this.handles.length; i++) {
			var handle = this.handles[i];
			this._bindStartEvent(handle, isAdd);
			handle[bindMethod]('click', this);
			// touch-action: none to override browser touch gestures. metafizzy/flickity#540
			if (window.PointerEvent) {
				handle.style.touchAction = touchAction;
			}
		}
	};

	// prototype so it can be overwriteable by Flickity
	proto._touchActionValue = 'none';

	// ----- start event ----- //

	/**
	 * pointer start
	 * @param {Event} event
	 * @param {Event or Touch} pointer
	 */
	proto.pointerDown = function (event, pointer) {
		var isOkay = this.okayPointerDown(event);
		if (!isOkay) {
			return;
		}
		// track start event position
		this.pointerDownPointer = pointer;

		event.preventDefault();
		this.pointerDownBlur();
		// bind move and end events
		this._bindPostStartEvents(event);
		this.emitEvent('pointerDown', [event, pointer]);
	};

	// nodes that have text fields
	var cursorNodes = {
		TEXTAREA: true,
		INPUT: true,
		SELECT: true,
		OPTION: true,
	};

	// input types that do not have text fields
	var clickTypes = {
		radio: true,
		checkbox: true,
		button: true,
		submit: true,
		image: true,
		file: true,
	};

	// dismiss inputs with text fields. flickity#403, flickity#404
	proto.okayPointerDown = function (event) {
		var isCursorNode = cursorNodes[event.target.nodeName];
		var isClickType = clickTypes[event.target.type];
		var isOkay = !isCursorNode || isClickType;
		if (!isOkay) {
			this._pointerReset();
		}
		return isOkay;
	};

	// kludge to blur previously focused input
	proto.pointerDownBlur = function () {
		var focused = document.activeElement;
		// do not blur body for IE10, metafizzy/flickity#117
		var canBlur = focused && focused.blur && focused != document.body;
		if (canBlur) {
			focused.blur();
		}
	};

	// ----- move event ----- //

	/**
	 * drag move
	 * @param {Event} event
	 * @param {Event or Touch} pointer
	 */
	proto.pointerMove = function (event, pointer) {
		var moveVector = this._dragPointerMove(event, pointer);
		this.emitEvent('pointerMove', [event, pointer, moveVector]);
		this._dragMove(event, pointer, moveVector);
	};

	// base pointer move logic
	proto._dragPointerMove = function (event, pointer) {
		var moveVector = {
			x: pointer.pageX - this.pointerDownPointer.pageX,
			y: pointer.pageY - this.pointerDownPointer.pageY
		};
		// start drag if pointer has moved far enough to start drag
		if (!this.isDragging && this.hasDragStarted(moveVector)) {
			this._dragStart(event, pointer);
		}
		return moveVector;
	};

	// condition if pointer has moved far enough to start drag
	proto.hasDragStarted = function (moveVector) {
		return Math.abs(moveVector.x) > 3 || Math.abs(moveVector.y) > 3;
	};

	// ----- end event ----- //

	/**
	 * pointer up
	 * @param {Event} event
	 * @param {Event or Touch} pointer
	 */
	proto.pointerUp = function (event, pointer) {
		this.emitEvent('pointerUp', [event, pointer]);
		this._dragPointerUp(event, pointer);
	};

	proto._dragPointerUp = function (event, pointer) {
		if (this.isDragging) {
			this._dragEnd(event, pointer);
		} else {
			// pointer didn't move enough for drag to start
			this._staticClick(event, pointer);
		}
	};

	// -------------------------- drag -------------------------- //

	// dragStart
	proto._dragStart = function (event, pointer) {
		this.isDragging = true;
		// prevent clicks
		this.isPreventingClicks = true;
		this.dragStart(event, pointer);
	};

	proto.dragStart = function (event, pointer) {
		this.emitEvent('dragStart', [event, pointer]);
	};

	// dragMove
	proto._dragMove = function (event, pointer, moveVector) {
		// do not drag if not dragging yet
		if (!this.isDragging) {
			return;
		}

		this.dragMove(event, pointer, moveVector);
	};

	proto.dragMove = function (event, pointer, moveVector) {
		event.preventDefault();
		this.emitEvent('dragMove', [event, pointer, moveVector]);
	};

	// dragEnd
	proto._dragEnd = function (event, pointer) {
		// set flags
		this.isDragging = false;
		// re-enable clicking async
		setTimeout(function () {
			delete this.isPreventingClicks;
		}.bind(this));

		this.dragEnd(event, pointer);
	};

	proto.dragEnd = function (event, pointer) {
		this.emitEvent('dragEnd', [event, pointer]);
	};

	// ----- onclick ----- //

	// handle all clicks and prevent clicks when dragging
	proto.onclick = function (event) {
		if (this.isPreventingClicks) {
			event.preventDefault();
		}
	};

	// ----- staticClick ----- //

	// triggered after pointer down & up with no/tiny movement
	proto._staticClick = function (event, pointer) {
		// ignore emulated mouse up clicks
		if (this.isIgnoringMouseUp && event.type == 'mouseup') {
			return;
		}

		this.staticClick(event, pointer);

		// set flag for emulated clicks 300ms after touchend
		if (event.type != 'mouseup') {
			this.isIgnoringMouseUp = true;
			// reset flag after 300ms
			setTimeout(function () {
				delete this.isIgnoringMouseUp;
			}.bind(this), 400);
		}
	};

	proto.staticClick = function (event, pointer) {
		this.emitEvent('staticClick', [event, pointer]);
	};

	// ----- utils ----- //

	Unidragger.getPointerPoint = Unipointer.getPointerPoint;

	// -----  ----- //

	return Unidragger;

}));

// drag
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/drag', [
			'./flickity',
			'unidragger/unidragger',
			'fizzy-ui-utils/utils'
		], function (Flickity, Unidragger, utils) {
			return factory(window, Flickity, Unidragger, utils);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('./flickity'),
			require('unidragger'),
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		window.Flickity = factory(
			window,
			window.Flickity,
			window.Unidragger,
			window.fizzyUIUtils
		);
	}

}(window, function factory(window, Flickity, Unidragger, utils) {



	// ----- defaults ----- //

	utils.extend(Flickity.defaults, {
		draggable: '>1',
		dragThreshold: 3,
	});

	// ----- create ----- //

	Flickity.createMethods.push('_createDrag');

	// -------------------------- drag prototype -------------------------- //

	var proto = Flickity.prototype;
	utils.extend(proto, Unidragger.prototype);
	proto._touchActionValue = 'pan-y';

	// --------------------------  -------------------------- //

	var isTouch = 'createTouch' in document;
	var isTouchmoveScrollCanceled = false;

	proto._createDrag = function () {
		this.on('activate', this.onActivateDrag);
		this.on('uiChange', this._uiChangeDrag);
		this.on('deactivate', this.onDeactivateDrag);
		this.on('cellChange', this.updateDraggable);
		// TODO updateDraggable on resize? if groupCells & slides change
		// HACK - add seemingly innocuous handler to fix iOS 10 scroll behavior
		// #457, RubaXa/Sortable#973
		if (isTouch && !isTouchmoveScrollCanceled) {
			window.addEventListener('touchmove', function () { });
			isTouchmoveScrollCanceled = true;
		}
	};

	proto.onActivateDrag = function () {
		this.handles = [this.viewport];
		this.bindHandles();
		this.updateDraggable();
	};

	proto.onDeactivateDrag = function () {
		this.unbindHandles();
		this.element.classList.remove('is-draggable');
	};

	proto.updateDraggable = function () {
		// disable dragging if less than 2 slides. #278
		if (this.options.draggable == '>1') {
			this.isDraggable = this.slides.length > 1;
		} else {
			this.isDraggable = this.options.draggable;
		}
		if (this.isDraggable) {
			this.element.classList.add('is-draggable');
		} else {
			this.element.classList.remove('is-draggable');
		}
	};

	// backwards compatibility
	proto.bindDrag = function () {
		this.options.draggable = true;
		this.updateDraggable();
	};

	proto.unbindDrag = function () {
		this.options.draggable = false;
		this.updateDraggable();
	};

	proto._uiChangeDrag = function () {
		delete this.isFreeScrolling;
	};

	// -------------------------- pointer events -------------------------- //

	proto.pointerDown = function (event, pointer) {
		if (!this.isDraggable) {
			this._pointerDownDefault(event, pointer);
			return;
		}
		var isOkay = this.okayPointerDown(event);
		if (!isOkay) {
			return;
		}

		this._pointerDownPreventDefault(event);
		this.pointerDownFocus(event);
		// blur
		if (document.activeElement != this.element) {
			// do not blur if already focused
			this.pointerDownBlur();
		}

		// stop if it was moving
		this.dragX = this.x;
		this.viewport.classList.add('is-pointer-down');
		// track scrolling
		this.pointerDownScroll = getScrollPosition();
		window.addEventListener('scroll', this);

		this._pointerDownDefault(event, pointer);
	};

	// default pointerDown logic, used for staticClick
	proto._pointerDownDefault = function (event, pointer) {
		// track start event position
		// Safari 9 overrides pageX and pageY. These values needs to be copied. #779
		this.pointerDownPointer = {
			pageX: pointer.pageX,
			pageY: pointer.pageY,
		};
		// bind move and end events
		this._bindPostStartEvents(event);
		this.dispatchEvent('pointerDown', event, [pointer]);
	};

	var focusNodes = {
		INPUT: true,
		TEXTAREA: true,
		SELECT: true,
	};

	proto.pointerDownFocus = function (event) {
		var isFocusNode = focusNodes[event.target.nodeName];
		if (!isFocusNode) {
			this.focus();
		}
	};

	proto._pointerDownPreventDefault = function (event) {
		var isTouchStart = event.type == 'touchstart';
		var isTouchPointer = event.pointerType == 'touch';
		var isFocusNode = focusNodes[event.target.nodeName];
		if (!isTouchStart && !isTouchPointer && !isFocusNode) {
			event.preventDefault();
		}
	};

	// ----- move ----- //

	proto.hasDragStarted = function (moveVector) {
		return Math.abs(moveVector.x) > this.options.dragThreshold;
	};

	// ----- up ----- //

	proto.pointerUp = function (event, pointer) {
		delete this.isTouchScrolling;
		this.viewport.classList.remove('is-pointer-down');
		this.dispatchEvent('pointerUp', event, [pointer]);
		this._dragPointerUp(event, pointer);
	};

	proto.pointerDone = function () {
		window.removeEventListener('scroll', this);
		delete this.pointerDownScroll;
	};

	// -------------------------- dragging -------------------------- //

	proto.dragStart = function (event, pointer) {
		if (!this.isDraggable) {
			return;
		}
		this.dragStartPosition = this.x;
		this.startAnimation();
		window.removeEventListener('scroll', this);
		this.dispatchEvent('dragStart', event, [pointer]);
	};

	proto.pointerMove = function (event, pointer) {
		var moveVector = this._dragPointerMove(event, pointer);
		this.dispatchEvent('pointerMove', event, [pointer, moveVector]);
		this._dragMove(event, pointer, moveVector);
	};

	proto.dragMove = function (event, pointer, moveVector) {
		if (!this.isDraggable) {
			return;
		}
		event.preventDefault();

		this.previousDragX = this.dragX;
		// reverse if right-to-left
		var direction = this.options.rightToLeft ? -1 : 1;
		if (this.options.wrapAround) {
			// wrap around move. #589
			moveVector.x = moveVector.x % this.slideableWidth;
		}
		var dragX = this.dragStartPosition + moveVector.x * direction;

		if (!this.options.wrapAround && this.slides.length) {
			// slow drag
			var originBound = Math.max(-this.slides[0].target, this.dragStartPosition);
			dragX = dragX > originBound ? (dragX + originBound) * 0.5 : dragX;
			var endBound = Math.min(-this.getLastSlide().target, this.dragStartPosition);
			dragX = dragX < endBound ? (dragX + endBound) * 0.5 : dragX;
		}

		this.dragX = dragX;

		this.dragMoveTime = new Date();
		this.dispatchEvent('dragMove', event, [pointer, moveVector]);
	};

	proto.dragEnd = function (event, pointer) {
		if (!this.isDraggable) {
			return;
		}
		if (this.options.freeScroll) {
			this.isFreeScrolling = true;
		}
		// set selectedIndex based on where flick will end up
		var index = this.dragEndRestingSelect();

		if (this.options.freeScroll && !this.options.wrapAround) {
			// if free-scroll & not wrap around
			// do not free-scroll if going outside of bounding slides
			// so bounding slides can attract slider, and keep it in bounds
			var restingX = this.getRestingPosition();
			this.isFreeScrolling = -restingX > this.slides[0].target &&
				-restingX < this.getLastSlide().target;
		} else if (!this.options.freeScroll && index == this.selectedIndex) {
			// boost selection if selected index has not changed
			index += this.dragEndBoostSelect();
		}
		delete this.previousDragX;
		// apply selection
		// TODO refactor this, selecting here feels weird
		// HACK, set flag so dragging stays in correct direction
		this.isDragSelect = this.options.wrapAround;
		this.select(index);
		delete this.isDragSelect;
		this.dispatchEvent('dragEnd', event, [pointer]);
	};

	proto.dragEndRestingSelect = function () {
		var restingX = this.getRestingPosition();
		// how far away from selected slide
		var distance = Math.abs(this.getSlideDistance(-restingX, this.selectedIndex));
		// get closet resting going up and going down
		var positiveResting = this._getClosestResting(restingX, distance, 1);
		var negativeResting = this._getClosestResting(restingX, distance, -1);
		// use closer resting for wrap-around
		var index = positiveResting.distance < negativeResting.distance ?
			positiveResting.index : negativeResting.index;
		return index;
	};

	/**
	 * given resting X and distance to selected cell
	 * get the distance and index of the closest cell
	 * @param {Number} restingX - estimated post-flick resting position
	 * @param {Number} distance - distance to selected cell
	 * @param {Integer} increment - +1 or -1, going up or down
	 * @returns {Object} - { distance: {Number}, index: {Integer} }
	 */
	proto._getClosestResting = function (restingX, distance, increment) {
		var index = this.selectedIndex;
		var minDistance = Infinity;
		var condition = this.options.contain && !this.options.wrapAround ?
			// if contain, keep going if distance is equal to minDistance
			function (d, md) { return d <= md; } : function (d, md) { return d < md; };
		while (condition(distance, minDistance)) {
			// measure distance to next cell
			index += increment;
			minDistance = distance;
			distance = this.getSlideDistance(-restingX, index);
			if (distance === null) {
				break;
			}
			distance = Math.abs(distance);
		}
		return {
			distance: minDistance,
			// selected was previous index
			index: index - increment
		};
	};

	/**
	 * measure distance between x and a slide target
	 * @param {Number} x
	 * @param {Integer} index - slide index
	 */
	proto.getSlideDistance = function (x, index) {
		var len = this.slides.length;
		// wrap around if at least 2 slides
		var isWrapAround = this.options.wrapAround && len > 1;
		var slideIndex = isWrapAround ? utils.modulo(index, len) : index;
		var slide = this.slides[slideIndex];
		if (!slide) {
			return null;
		}
		// add distance for wrap-around slides
		var wrap = isWrapAround ? this.slideableWidth * Math.floor(index / len) : 0;
		return x - (slide.target + wrap);
	};

	proto.dragEndBoostSelect = function () {
		// do not boost if no previousDragX or dragMoveTime
		if (this.previousDragX === undefined || !this.dragMoveTime ||
			// or if drag was held for 100 ms
			new Date() - this.dragMoveTime > 100) {
			return 0;
		}

		var distance = this.getSlideDistance(-this.dragX, this.selectedIndex);
		var delta = this.previousDragX - this.dragX;
		if (distance > 0 && delta > 0) {
			// boost to next if moving towards the right, and positive velocity
			return 1;
		} else if (distance < 0 && delta < 0) {
			// boost to previous if moving towards the left, and negative velocity
			return -1;
		}
		return 0;
	};

	// ----- staticClick ----- //

	proto.staticClick = function (event, pointer) {
		// get clickedCell, if cell was clicked
		var clickedCell = this.getParentCell(event.target);
		var cellElem = clickedCell && clickedCell.element;
		var cellIndex = clickedCell && this.cells.indexOf(clickedCell);
		this.dispatchEvent('staticClick', event, [pointer, cellElem, cellIndex]);
	};

	// ----- scroll ----- //

	proto.onscroll = function () {
		var scroll = getScrollPosition();
		var scrollMoveX = this.pointerDownScroll.x - scroll.x;
		var scrollMoveY = this.pointerDownScroll.y - scroll.y;
		// cancel click/tap if scroll is too much
		if (Math.abs(scrollMoveX) > 3 || Math.abs(scrollMoveY) > 3) {
			this._pointerDone();
		}
	};

	// ----- utils ----- //

	function getScrollPosition() {
		return {
			x: window.pageXOffset,
			y: window.pageYOffset
		};
	}

	// -----  ----- //

	return Flickity;

}));

// prev/next buttons
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/prev-next-button', [
			'./flickity',
			'unipointer/unipointer',
			'fizzy-ui-utils/utils'
		], function (Flickity, Unipointer, utils) {
			return factory(window, Flickity, Unipointer, utils);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('./flickity'),
			require('unipointer'),
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		factory(
			window,
			window.Flickity,
			window.Unipointer,
			window.fizzyUIUtils
		);
	}

}(window, function factory(window, Flickity, Unipointer, utils) {
	'use strict';

	var svgURI = 'http://www.w3.org/2000/svg';

	// -------------------------- PrevNextButton -------------------------- //

	function PrevNextButton(direction, parent) {
		this.direction = direction;
		this.parent = parent;
		this._create();
	}

	PrevNextButton.prototype = Object.create(Unipointer.prototype);

	PrevNextButton.prototype._create = function () {
		// properties
		this.isEnabled = true;
		this.isPrevious = this.direction == -1;
		var leftDirection = this.parent.options.rightToLeft ? 1 : -1;
		this.isLeft = this.direction == leftDirection;

		var element = this.element = document.createElement('button');
		element.className = 'flickity-button flickity-prev-next-button';
		element.className += this.isPrevious ? ' previous' : ' next';
		// prevent button from submitting form http://stackoverflow.com/a/10836076/182183
		element.setAttribute('type', 'button');
		// init as disabled
		this.disable();

		element.setAttribute('aria-label', this.isPrevious ? 'Previous' : 'Next');

		// create arrow
		var svg = this.createSVG();
		element.appendChild(svg);
		// events
		this.parent.on('select', this.update.bind(this));
		this.on('pointerDown', this.parent.childUIPointerDown.bind(this.parent));
	};

	PrevNextButton.prototype.activate = function () {
		this.bindStartEvent(this.element);
		this.element.addEventListener('click', this);
		// add to DOM
		this.parent.element.appendChild(this.element);
	};

	PrevNextButton.prototype.deactivate = function () {
		// remove from DOM
		this.parent.element.removeChild(this.element);
		// click events
		this.unbindStartEvent(this.element);
		this.element.removeEventListener('click', this);
	};

	PrevNextButton.prototype.createSVG = function () {
		var svg = document.createElementNS(svgURI, 'svg');
		svg.setAttribute('class', 'flickity-button-icon');
		svg.setAttribute('viewBox', '0 0 100 100');
		var path = document.createElementNS(svgURI, 'path');
		var pathMovements = getArrowMovements(this.parent.options.arrowShape);
		path.setAttribute('d', pathMovements);
		path.setAttribute('class', 'arrow');
		// rotate arrow
		if (!this.isLeft) {
			path.setAttribute('transform', 'translate(100, 100) rotate(180) ');
		}
		svg.appendChild(path);
		return svg;
	};

	// get SVG path movmement
	function getArrowMovements(shape) {
		// use shape as movement if string
		if (typeof shape == 'string') {
			return shape;
		}
		// create movement string
		return 'M ' + shape.x0 + ',50' +
			' L ' + shape.x1 + ',' + (shape.y1 + 50) +
			' L ' + shape.x2 + ',' + (shape.y2 + 50) +
			' L ' + shape.x3 + ',50 ' +
			' L ' + shape.x2 + ',' + (50 - shape.y2) +
			' L ' + shape.x1 + ',' + (50 - shape.y1) +
			' Z';
	}

	PrevNextButton.prototype.handleEvent = utils.handleEvent;

	PrevNextButton.prototype.onclick = function () {
		if (!this.isEnabled) {
			return;
		}
		this.parent.uiChange();
		var method = this.isPrevious ? 'previous' : 'next';
		this.parent[method]();
	};

	// -----  ----- //

	PrevNextButton.prototype.enable = function () {
		if (this.isEnabled) {
			return;
		}
		this.element.disabled = false;
		this.isEnabled = true;
	};

	PrevNextButton.prototype.disable = function () {
		if (!this.isEnabled) {
			return;
		}
		this.element.disabled = true;
		this.isEnabled = false;
	};

	PrevNextButton.prototype.update = function () {
		// index of first or last slide, if previous or next
		var slides = this.parent.slides;
		// enable is wrapAround and at least 2 slides
		if (this.parent.options.wrapAround && slides.length > 1) {
			this.enable();
			return;
		}
		var lastIndex = slides.length ? slides.length - 1 : 0;
		var boundIndex = this.isPrevious ? 0 : lastIndex;
		var method = this.parent.selectedIndex == boundIndex ? 'disable' : 'enable';
		this[method]();
	};

	PrevNextButton.prototype.destroy = function () {
		this.deactivate();
		this.allOff();
	};

	// -------------------------- Flickity prototype -------------------------- //

	utils.extend(Flickity.defaults, {
		prevNextButtons: true,
		arrowShape: {
			x0: 10,
			x1: 60, y1: 50,
			x2: 70, y2: 40,
			x3: 30
		}
	});

	Flickity.createMethods.push('_createPrevNextButtons');
	var proto = Flickity.prototype;

	proto._createPrevNextButtons = function () {
		if (!this.options.prevNextButtons) {
			return;
		}

		this.prevButton = new PrevNextButton(-1, this);
		this.nextButton = new PrevNextButton(1, this);

		this.on('activate', this.activatePrevNextButtons);
	};

	proto.activatePrevNextButtons = function () {
		this.prevButton.activate();
		this.nextButton.activate();
		this.on('deactivate', this.deactivatePrevNextButtons);
	};

	proto.deactivatePrevNextButtons = function () {
		this.prevButton.deactivate();
		this.nextButton.deactivate();
		this.off('deactivate', this.deactivatePrevNextButtons);
	};

	// --------------------------  -------------------------- //

	Flickity.PrevNextButton = PrevNextButton;

	return Flickity;

}));

// page dots
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/page-dots', [
			'./flickity',
			'unipointer/unipointer',
			'fizzy-ui-utils/utils'
		], function (Flickity, Unipointer, utils) {
			return factory(window, Flickity, Unipointer, utils);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('./flickity'),
			require('unipointer'),
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		factory(
			window,
			window.Flickity,
			window.Unipointer,
			window.fizzyUIUtils
		);
	}

}(window, function factory(window, Flickity, Unipointer, utils) {

	// -------------------------- PageDots -------------------------- //



	function PageDots(parent) {
		this.parent = parent;
		this._create();
	}

	PageDots.prototype = Object.create(Unipointer.prototype);

	PageDots.prototype._create = function () {
		// create holder element
		this.holder = document.createElement('ol');
		this.holder.className = 'flickity-page-dots';
		// create dots, array of elements
		this.dots = [];
		// events
		this.handleClick = this.onClick.bind(this);
		this.on('pointerDown', this.parent.childUIPointerDown.bind(this.parent));
	};

	PageDots.prototype.activate = function () {
		this.setDots();
		this.holder.addEventListener('click', this.handleClick);
		this.bindStartEvent(this.holder);
		// add to DOM
		this.parent.element.appendChild(this.holder);
	};

	PageDots.prototype.deactivate = function () {
		this.holder.removeEventListener('click', this.handleClick);
		this.unbindStartEvent(this.holder);
		// remove from DOM
		this.parent.element.removeChild(this.holder);
	};

	PageDots.prototype.setDots = function () {
		// get difference between number of slides and number of dots
		var delta = this.parent.slides.length - this.dots.length;
		if (delta > 0) {
			this.addDots(delta);
		} else if (delta < 0) {
			this.removeDots(-delta);
		}
	};

	PageDots.prototype.addDots = function (count) {
		var fragment = document.createDocumentFragment();
		var newDots = [];
		var length = this.dots.length;
		var max = length + count;

		for (var i = length; i < max; i++) {
			var dot = document.createElement('li');
			dot.className = 'dot';
			dot.setAttribute('aria-label', 'Page dot ' + (i + 1));
			fragment.appendChild(dot);
			newDots.push(dot);
		}

		this.holder.appendChild(fragment);
		this.dots = this.dots.concat(newDots);
	};

	PageDots.prototype.removeDots = function (count) {
		// remove from this.dots collection
		var removeDots = this.dots.splice(this.dots.length - count, count);
		// remove from DOM
		removeDots.forEach(function (dot) {
			this.holder.removeChild(dot);
		}, this);
	};

	PageDots.prototype.updateSelected = function () {
		// remove selected class on previous
		if (this.selectedDot) {
			this.selectedDot.className = 'dot';
			this.selectedDot.removeAttribute('aria-current');
		}
		// don't proceed if no dots
		if (!this.dots.length) {
			return;
		}
		this.selectedDot = this.dots[this.parent.selectedIndex];
		this.selectedDot.className = 'dot is-selected';
		this.selectedDot.setAttribute('aria-current', 'step');
	};

	PageDots.prototype.onTap = // old method name, backwards-compatible
		PageDots.prototype.onClick = function (event) {
			var target = event.target;
			// only care about dot clicks
			if (target.nodeName != 'LI') {
				return;
			}

			this.parent.uiChange();
			var index = this.dots.indexOf(target);
			this.parent.select(index);
		};

	PageDots.prototype.destroy = function () {
		this.deactivate();
		this.allOff();
	};

	Flickity.PageDots = PageDots;

	// -------------------------- Flickity -------------------------- //

	utils.extend(Flickity.defaults, {
		pageDots: true
	});

	Flickity.createMethods.push('_createPageDots');

	var proto = Flickity.prototype;

	proto._createPageDots = function () {
		if (!this.options.pageDots) {
			return;
		}
		this.pageDots = new PageDots(this);
		// events
		this.on('activate', this.activatePageDots);
		this.on('select', this.updateSelectedPageDots);
		this.on('cellChange', this.updatePageDots);
		this.on('resize', this.updatePageDots);
		this.on('deactivate', this.deactivatePageDots);
	};

	proto.activatePageDots = function () {
		this.pageDots.activate();
	};

	proto.updateSelectedPageDots = function () {
		this.pageDots.updateSelected();
	};

	proto.updatePageDots = function () {
		this.pageDots.setDots();
	};

	proto.deactivatePageDots = function () {
		this.pageDots.deactivate();
	};

	// -----  ----- //

	Flickity.PageDots = PageDots;

	return Flickity;

}));

// player & autoPlay
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/player', [
			'ev-emitter/ev-emitter',
			'fizzy-ui-utils/utils',
			'./flickity'
		], function (EvEmitter, utils, Flickity) {
			return factory(EvEmitter, utils, Flickity);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			require('ev-emitter'),
			require('fizzy-ui-utils'),
			require('./flickity')
		);
	} else {
		// browser global
		factory(
			window.EvEmitter,
			window.fizzyUIUtils,
			window.Flickity
		);
	}

}(window, function factory(EvEmitter, utils, Flickity) {



	// -------------------------- Player -------------------------- //

	function Player(parent) {
		this.parent = parent;
		this.state = 'stopped';
		// visibility change event handler
		this.onVisibilityChange = this.visibilityChange.bind(this);
		this.onVisibilityPlay = this.visibilityPlay.bind(this);
	}

	Player.prototype = Object.create(EvEmitter.prototype);

	// start play
	Player.prototype.play = function () {
		if (this.state == 'playing') {
			return;
		}
		// do not play if page is hidden, start playing when page is visible
		var isPageHidden = document.hidden;
		if (isPageHidden) {
			document.addEventListener('visibilitychange', this.onVisibilityPlay);
			return;
		}

		this.state = 'playing';
		// listen to visibility change
		document.addEventListener('visibilitychange', this.onVisibilityChange);
		// start ticking
		this.tick();
	};

	Player.prototype.tick = function () {
		// do not tick if not playing
		if (this.state != 'playing') {
			return;
		}

		var time = this.parent.options.autoPlay;
		// default to 3 seconds
		time = typeof time == 'number' ? time : 3000;
		var _this = this;
		// HACK: reset ticks if stopped and started within interval
		this.clear();
		this.timeout = setTimeout(function () {
			_this.parent.next(true);
			_this.tick();
		}, time);
	};

	Player.prototype.stop = function () {
		this.state = 'stopped';
		this.clear();
		// remove visibility change event
		document.removeEventListener('visibilitychange', this.onVisibilityChange);
	};

	Player.prototype.clear = function () {
		clearTimeout(this.timeout);
	};

	Player.prototype.pause = function () {
		if (this.state == 'playing') {
			this.state = 'paused';
			this.clear();
		}
	};

	Player.prototype.unpause = function () {
		// re-start play if paused
		if (this.state == 'paused') {
			this.play();
		}
	};

	// pause if page visibility is hidden, unpause if visible
	Player.prototype.visibilityChange = function () {
		var isPageHidden = document.hidden;
		this[isPageHidden ? 'pause' : 'unpause']();
	};

	Player.prototype.visibilityPlay = function () {
		this.play();
		document.removeEventListener('visibilitychange', this.onVisibilityPlay);
	};

	// -------------------------- Flickity -------------------------- //

	utils.extend(Flickity.defaults, {
		pauseAutoPlayOnHover: true
	});

	Flickity.createMethods.push('_createPlayer');
	var proto = Flickity.prototype;

	proto._createPlayer = function () {
		this.player = new Player(this);

		this.on('activate', this.activatePlayer);
		this.on('uiChange', this.stopPlayer);
		this.on('pointerDown', this.stopPlayer);
		this.on('deactivate', this.deactivatePlayer);
	};

	proto.activatePlayer = function () {
		if (!this.options.autoPlay) {
			return;
		}
		this.player.play();
		this.element.addEventListener('mouseenter', this);
	};

	// Player API, don't hate the ... thanks I know where the door is

	proto.playPlayer = function () {
		this.player.play();
	};

	proto.stopPlayer = function () {
		this.player.stop();
	};

	proto.pausePlayer = function () {
		this.player.pause();
	};

	proto.unpausePlayer = function () {
		this.player.unpause();
	};

	proto.deactivatePlayer = function () {
		this.player.stop();
		this.element.removeEventListener('mouseenter', this);
	};

	// ----- mouseenter/leave ----- //

	// pause auto-play on hover
	proto.onmouseenter = function () {
		if (!this.options.pauseAutoPlayOnHover) {
			return;
		}
		this.player.pause();
		this.element.addEventListener('mouseleave', this);
	};

	// resume auto-play on hover off
	proto.onmouseleave = function () {
		this.player.unpause();
		this.element.removeEventListener('mouseleave', this);
	};

	// -----  ----- //

	Flickity.Player = Player;

	return Flickity;

}));

// add, remove cell
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/add-remove-cell', [
			'./flickity',
			'fizzy-ui-utils/utils'
		], function (Flickity, utils) {
			return factory(window, Flickity, utils);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('./flickity'),
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		factory(
			window,
			window.Flickity,
			window.fizzyUIUtils
		);
	}

}(window, function factory(window, Flickity, utils) {



	// append cells to a document fragment
	function getCellsFragment(cells) {
		var fragment = document.createDocumentFragment();
		cells.forEach(function (cell) {
			fragment.appendChild(cell.element);
		});
		return fragment;
	}

	// -------------------------- add/remove cell prototype -------------------------- //

	var proto = Flickity.prototype;

	/**
	 * Insert, prepend, or append cells
	 * @param {Element, Array, NodeList} elems
	 * @param {Integer} index
	 */
	proto.insert = function (elems, index) {
		var cells = this._makeCells(elems);
		if (!cells || !cells.length) {
			return;
		}
		var len = this.cells.length;
		// default to append
		index = index === undefined ? len : index;
		// add cells with document fragment
		var fragment = getCellsFragment(cells);
		// append to slider
		var isAppend = index == len;
		if (isAppend) {
			this.slider.appendChild(fragment);
		} else {
			var insertCellElement = this.cells[index].element;
			this.slider.insertBefore(fragment, insertCellElement);
		}
		// add to this.cells
		if (index === 0) {
			// prepend, add to start
			this.cells = cells.concat(this.cells);
		} else if (isAppend) {
			// append, add to end
			this.cells = this.cells.concat(cells);
		} else {
			// insert in this.cells
			var endCells = this.cells.splice(index, len - index);
			this.cells = this.cells.concat(cells).concat(endCells);
		}

		this._sizeCells(cells);
		this.cellChange(index, true);
	};

	proto.append = function (elems) {
		this.insert(elems, this.cells.length);
	};

	proto.prepend = function (elems) {
		this.insert(elems, 0);
	};

	/**
	 * Remove cells
	 * @param {Element, Array, NodeList} elems
	 */
	proto.remove = function (elems) {
		var cells = this.getCells(elems);
		if (!cells || !cells.length) {
			return;
		}

		var minCellIndex = this.cells.length - 1;
		// remove cells from collection & DOM
		cells.forEach(function (cell) {
			cell.remove();
			var index = this.cells.indexOf(cell);
			minCellIndex = Math.min(index, minCellIndex);
			utils.removeFrom(this.cells, cell);
		}, this);

		this.cellChange(minCellIndex, true);
	};

	/**
	 * logic to be run after a cell's size changes
	 * @param {Element} elem - cell's element
	 */
	proto.cellSizeChange = function (elem) {
		var cell = this.getCell(elem);
		if (!cell) {
			return;
		}
		cell.getSize();

		var index = this.cells.indexOf(cell);
		this.cellChange(index);
	};

	/**
	 * logic any time a cell is changed: added, removed, or size changed
	 * @param {Integer} changedCellIndex - index of the changed cell, optional
	 */
	proto.cellChange = function (changedCellIndex, isPositioningSlider) {
		var prevSelectedElem = this.selectedElement;
		this._positionCells(changedCellIndex);
		this._getWrapShiftCells();
		this.setGallerySize();
		// update selectedIndex
		// try to maintain position & select previous selected element
		var cell = this.getCell(prevSelectedElem);
		if (cell) {
			this.selectedIndex = this.getCellSlideIndex(cell);
		}
		this.selectedIndex = Math.min(this.slides.length - 1, this.selectedIndex);

		this.emitEvent('cellChange', [changedCellIndex]);
		// position slider
		this.select(this.selectedIndex);
		// do not position slider after lazy load
		if (isPositioningSlider) {
			this.positionSliderAtSelected();
		}
	};

	// -----  ----- //

	return Flickity;

}));

// lazyload
(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/lazyload', [
			'./flickity',
			'fizzy-ui-utils/utils'
		], function (Flickity, utils) {
			return factory(window, Flickity, utils);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('./flickity'),
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		factory(
			window,
			window.Flickity,
			window.fizzyUIUtils
		);
	}

}(window, function factory(window, Flickity, utils) {
	'use strict';

	Flickity.createMethods.push('_createLazyload');
	var proto = Flickity.prototype;

	proto._createLazyload = function () {
		this.on('select', this.lazyLoad);
	};

	proto.lazyLoad = function () {
		var lazyLoad = this.options.lazyLoad;
		if (!lazyLoad) {
			return;
		}
		// get adjacent cells, use lazyLoad option for adjacent count
		var adjCount = typeof lazyLoad == 'number' ? lazyLoad : 0;
		var cellElems = this.getAdjacentCellElements(adjCount);
		// get lazy images in those cells
		var lazyImages = [];
		cellElems.forEach(function (cellElem) {
			var lazyCellImages = getCellLazyImages(cellElem);
			lazyImages = lazyImages.concat(lazyCellImages);
		});
		// load lazy images
		lazyImages.forEach(function (img) {
			new LazyLoader(img, this);
		}, this);
	};

	function getCellLazyImages(cellElem) {
		// check if cell element is lazy image
		if (cellElem.nodeName == 'IMG') {
			var lazyloadAttr = cellElem.getAttribute('data-flickity-lazyload');
			var srcAttr = cellElem.getAttribute('data-flickity-lazyload-src');
			var srcsetAttr = cellElem.getAttribute('data-flickity-lazyload-srcset');
			if (lazyloadAttr || srcAttr || srcsetAttr) {
				return [cellElem];
			}
		}
		// select lazy images in cell
		var lazySelector = 'img[data-flickity-lazyload], ' +
			'img[data-flickity-lazyload-src], img[data-flickity-lazyload-srcset]';
		var imgs = cellElem.querySelectorAll(lazySelector);
		return utils.makeArray(imgs);
	}

	// -------------------------- LazyLoader -------------------------- //

	/**
	 * class to handle loading images
	 */
	function LazyLoader(img, flickity) {
		this.img = img;
		this.flickity = flickity;
		this.load();
	}

	LazyLoader.prototype.handleEvent = utils.handleEvent;

	LazyLoader.prototype.load = function () {
		this.img.addEventListener('load', this);
		this.img.addEventListener('error', this);
		// get src & srcset
		var src = this.img.getAttribute('data-flickity-lazyload') ||
			this.img.getAttribute('data-flickity-lazyload-src');
		var srcset = this.img.getAttribute('data-flickity-lazyload-srcset');
		// set src & serset
		this.img.src = src;
		if (srcset) {
			this.img.setAttribute('srcset', srcset);
		}
		// remove attr
		this.img.removeAttribute('data-flickity-lazyload');
		this.img.removeAttribute('data-flickity-lazyload-src');
		this.img.removeAttribute('data-flickity-lazyload-srcset');
	};

	LazyLoader.prototype.onload = function (event) {
		this.complete(event, 'flickity-lazyloaded');
	};

	LazyLoader.prototype.onerror = function (event) {
		this.complete(event, 'flickity-lazyerror');
	};

	LazyLoader.prototype.complete = function (event, className) {
		// unbind events
		this.img.removeEventListener('load', this);
		this.img.removeEventListener('error', this);

		var cell = this.flickity.getParentCell(this.img);
		var cellElem = cell && cell.element;
		this.flickity.cellSizeChange(cellElem);

		this.img.classList.add(className);
		this.flickity.dispatchEvent('lazyLoad', event, cellElem);
	};

	// -----  ----- //

	Flickity.LazyLoader = LazyLoader;

	return Flickity;

}));

/*!
 * Flickity v2.2.0
 * Touch, responsive, flickable carousels
 *
 * Licensed GPLv3 for open source use
 * or Flickity Commercial License for commercial use
 *
 * https://flickity.metafizzy.co
 * Copyright 2015-2018 Metafizzy
 */

(function (window, factory) {
	// universal module definition
	/* jshint strict: false */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity/js/index', [
			'./flickity',
			'./drag',
			'./prev-next-button',
			'./page-dots',
			'./player',
			'./add-remove-cell',
			'./lazyload'
		], factory);
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			require('./flickity'),
			require('./drag'),
			require('./prev-next-button'),
			require('./page-dots'),
			require('./player'),
			require('./add-remove-cell'),
			require('./lazyload')
		);
	}

})(window, function factory(Flickity) {
	/*jshint strict: false*/
	return Flickity;
});

/*!
 * Flickity asNavFor v2.0.1
 * enable asNavFor for Flickity
 */

/*jshint browser: true, undef: true, unused: true, strict: true*/

(function (window, factory) {
	// universal module definition
	/*jshint strict: false */ /*globals define, module, require */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define('flickity-as-nav-for/as-nav-for', [
			'flickity/js/index',
			'fizzy-ui-utils/utils'
		], factory);
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			require('flickity'),
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		window.Flickity = factory(
			window.Flickity,
			window.fizzyUIUtils
		);
	}

}(window, function factory(Flickity, utils) {



	// -------------------------- asNavFor prototype -------------------------- //

	// Flickity.defaults.asNavFor = null;

	Flickity.createMethods.push('_createAsNavFor');

	var proto = Flickity.prototype;

	proto._createAsNavFor = function () {
		this.on('activate', this.activateAsNavFor);
		this.on('deactivate', this.deactivateAsNavFor);
		this.on('destroy', this.destroyAsNavFor);

		var asNavForOption = this.options.asNavFor;
		if (!asNavForOption) {
			return;
		}
		// HACK do async, give time for other flickity to be initalized
		var _this = this;
		setTimeout(function initNavCompanion() {
			_this.setNavCompanion(asNavForOption);
		});
	};

	proto.setNavCompanion = function (elem) {
		elem = utils.getQueryElement(elem);
		var companion = Flickity.data(elem);
		// stop if no companion or companion is self
		if (!companion || companion == this) {
			return;
		}

		this.navCompanion = companion;
		// companion select
		var _this = this;
		this.onNavCompanionSelect = function () {
			_this.navCompanionSelect();
		};
		companion.on('select', this.onNavCompanionSelect);
		// click
		this.on('staticClick', this.onNavStaticClick);

		this.navCompanionSelect(true);
	};

	proto.navCompanionSelect = function (isInstant) {
		if (!this.navCompanion) {
			return;
		}
		// select slide that matches first cell of slide
		var selectedCell = this.navCompanion.selectedCells[0];
		var firstIndex = this.navCompanion.cells.indexOf(selectedCell);
		var lastIndex = firstIndex + this.navCompanion.selectedCells.length - 1;
		var selectIndex = Math.floor(lerp(firstIndex, lastIndex,
			this.navCompanion.cellAlign));
		this.selectCell(selectIndex, false, isInstant);
		// set nav selected class
		this.removeNavSelectedElements();
		// stop if companion has more cells than this one
		if (selectIndex >= this.cells.length) {
			return;
		}

		var selectedCells = this.cells.slice(firstIndex, lastIndex + 1);
		this.navSelectedElements = selectedCells.map(function (cell) {
			return cell.element;
		});
		this.changeNavSelectedClass('add');
	};

	function lerp(a, b, t) {
		return (b - a) * t + a;
	}

	proto.changeNavSelectedClass = function (method) {
		this.navSelectedElements.forEach(function (navElem) {
			navElem.classList[method]('is-nav-selected');
		});
	};

	proto.activateAsNavFor = function () {
		this.navCompanionSelect(true);
	};

	proto.removeNavSelectedElements = function () {
		if (!this.navSelectedElements) {
			return;
		}
		this.changeNavSelectedClass('remove');
		delete this.navSelectedElements;
	};

	proto.onNavStaticClick = function (event, pointer, cellElement, cellIndex) {
		if (typeof cellIndex == 'number') {
			this.navCompanion.selectCell(cellIndex);
		}
	};

	proto.deactivateAsNavFor = function () {
		this.removeNavSelectedElements();
	};

	proto.destroyAsNavFor = function () {
		if (!this.navCompanion) {
			return;
		}
		this.navCompanion.off('select', this.onNavCompanionSelect);
		this.off('staticClick', this.onNavStaticClick);
		delete this.navCompanion;
	};

	// -----  ----- //

	return Flickity;

}));

/*!
 * imagesLoaded v4.1.4
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

(function (window, factory) {
	'use strict';
	// universal module definition

	/*global define: false, module: false, require: false */

	if (typeof define == 'function' && define.amd) {
		// AMD
		define('imagesloaded/imagesloaded', [
			'ev-emitter/ev-emitter'
		], function (EvEmitter) {
			return factory(window, EvEmitter);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('ev-emitter')
		);
	} else {
		// browser global
		window.imagesLoaded = factory(
			window,
			window.EvEmitter
		);
	}

})(typeof window !== 'undefined' ? window : this,

	// --------------------------  factory -------------------------- //

	function factory(window, EvEmitter) {



		var $ = window.jQuery;
		var console = window.console;

		// -------------------------- helpers -------------------------- //

		// extend objects
		function extend(a, b) {
			for (var prop in b) {
				a[prop] = b[prop];
			}
			return a;
		}

		var arraySlice = Array.prototype.slice;

		// turn element or nodeList into an array
		function makeArray(obj) {
			if (Array.isArray(obj)) {
				// use object if already an array
				return obj;
			}

			var isArrayLike = typeof obj == 'object' && typeof obj.length == 'number';
			if (isArrayLike) {
				// convert nodeList to array
				return arraySlice.call(obj);
			}

			// array of single index
			return [obj];
		}

		// -------------------------- imagesLoaded -------------------------- //

		/**
		 * @param {Array, Element, NodeList, String} elem
		 * @param {Object or Function} options - if function, use as callback
		 * @param {Function} onAlways - callback function
		 */
		function ImagesLoaded(elem, options, onAlways) {
			// coerce ImagesLoaded() without new, to be new ImagesLoaded()
			if (!(this instanceof ImagesLoaded)) {
				return new ImagesLoaded(elem, options, onAlways);
			}
			// use elem as selector string
			var queryElem = elem;
			if (typeof elem == 'string') {
				queryElem = document.querySelectorAll(elem);
			}
			// bail if bad element
			if (!queryElem) {
				console.error('Bad element for imagesLoaded ' + (queryElem || elem));
				return;
			}

			this.elements = makeArray(queryElem);
			this.options = extend({}, this.options);
			// shift arguments if no options set
			if (typeof options == 'function') {
				onAlways = options;
			} else {
				extend(this.options, options);
			}

			if (onAlways) {
				this.on('always', onAlways);
			}

			this.getImages();

			if ($) {
				// add jQuery Deferred object
				this.jqDeferred = new $.Deferred();
			}

			// HACK check async to allow time to bind listeners
			setTimeout(this.check.bind(this));
		}

		ImagesLoaded.prototype = Object.create(EvEmitter.prototype);

		ImagesLoaded.prototype.options = {};

		ImagesLoaded.prototype.getImages = function () {
			this.images = [];

			// filter & find items if we have an item selector
			this.elements.forEach(this.addElementImages, this);
		};

		/**
		 * @param {Node} element
		 */
		ImagesLoaded.prototype.addElementImages = function (elem) {
			// filter siblings
			if (elem.nodeName == 'IMG') {
				this.addImage(elem);
			}
			// get background image on element
			if (this.options.background === true) {
				this.addElementBackgroundImages(elem);
			}

			// find children
			// no non-element nodes, #143
			var nodeType = elem.nodeType;
			if (!nodeType || !elementNodeTypes[nodeType]) {
				return;
			}
			var childImgs = elem.querySelectorAll('img');
			// concat childElems to filterFound array
			for (var i = 0; i < childImgs.length; i++) {
				var img = childImgs[i];
				this.addImage(img);
			}

			// get child background images
			if (typeof this.options.background == 'string') {
				var children = elem.querySelectorAll(this.options.background);
				for (i = 0; i < children.length; i++) {
					var child = children[i];
					this.addElementBackgroundImages(child);
				}
			}
		};

		var elementNodeTypes = {
			1: true,
			9: true,
			11: true
		};

		ImagesLoaded.prototype.addElementBackgroundImages = function (elem) {
			var style = getComputedStyle(elem);
			if (!style) {
				// Firefox returns null if in a hidden iframe https://bugzil.la/548397
				return;
			}
			// get url inside url("...")
			var reURL = /url\((['"])?(.*?)\1\)/gi;
			var matches = reURL.exec(style.backgroundImage);
			while (matches !== null) {
				var url = matches && matches[2];
				if (url) {
					this.addBackground(url, elem);
				}
				matches = reURL.exec(style.backgroundImage);
			}
		};

		/**
		 * @param {Image} img
		 */
		ImagesLoaded.prototype.addImage = function (img) {
			var loadingImage = new LoadingImage(img);
			this.images.push(loadingImage);
		};

		ImagesLoaded.prototype.addBackground = function (url, elem) {
			var background = new Background(url, elem);
			this.images.push(background);
		};

		ImagesLoaded.prototype.check = function () {
			var _this = this;
			this.progressedCount = 0;
			this.hasAnyBroken = false;
			// complete if no images
			if (!this.images.length) {
				this.complete();
				return;
			}

			function onProgress(image, elem, message) {
				// HACK - Chrome triggers event before object properties have changed. #83
				setTimeout(function () {
					_this.progress(image, elem, message);
				});
			}

			this.images.forEach(function (loadingImage) {
				loadingImage.once('progress', onProgress);
				loadingImage.check();
			});
		};

		ImagesLoaded.prototype.progress = function (image, elem, message) {
			this.progressedCount++;
			this.hasAnyBroken = this.hasAnyBroken || !image.isLoaded;
			// progress event
			this.emitEvent('progress', [this, image, elem]);
			if (this.jqDeferred && this.jqDeferred.notify) {
				this.jqDeferred.notify(this, image);
			}
			// check if completed
			if (this.progressedCount == this.images.length) {
				this.complete();
			}

			if (this.options.debug && console) {
				console.log('progress: ' + message, image, elem);
			}
		};

		ImagesLoaded.prototype.complete = function () {
			var eventName = this.hasAnyBroken ? 'fail' : 'done';
			this.isComplete = true;
			this.emitEvent(eventName, [this]);
			this.emitEvent('always', [this]);
			if (this.jqDeferred) {
				var jqMethod = this.hasAnyBroken ? 'reject' : 'resolve';
				this.jqDeferred[jqMethod](this);
			}
		};

		// --------------------------  -------------------------- //

		function LoadingImage(img) {
			this.img = img;
		}

		LoadingImage.prototype = Object.create(EvEmitter.prototype);

		LoadingImage.prototype.check = function () {
			// If complete is true and browser supports natural sizes,
			// try to check for image status manually.
			var isComplete = this.getIsImageComplete();
			if (isComplete) {
				// report based on naturalWidth
				this.confirm(this.img.naturalWidth !== 0, 'naturalWidth');
				return;
			}

			// If none of the checks above matched, simulate loading on detached element.
			this.proxyImage = new Image();
			this.proxyImage.addEventListener('load', this);
			this.proxyImage.addEventListener('error', this);
			// bind to image as well for Firefox. #191
			this.img.addEventListener('load', this);
			this.img.addEventListener('error', this);
			this.proxyImage.src = this.img.src;
		};

		LoadingImage.prototype.getIsImageComplete = function () {
			// check for non-zero, non-undefined naturalWidth
			// fixes Safari+InfiniteScroll+Masonry bug infinite-scroll#671
			return this.img.complete && this.img.naturalWidth;
		};

		LoadingImage.prototype.confirm = function (isLoaded, message) {
			this.isLoaded = isLoaded;
			this.emitEvent('progress', [this, this.img, message]);
		};

		// ----- events ----- //

		// trigger specified handler for event type
		LoadingImage.prototype.handleEvent = function (event) {
			var method = 'on' + event.type;
			if (this[method]) {
				this[method](event);
			}
		};

		LoadingImage.prototype.onload = function () {
			this.confirm(true, 'onload');
			this.unbindEvents();
		};

		LoadingImage.prototype.onerror = function () {
			this.confirm(false, 'onerror');
			this.unbindEvents();
		};

		LoadingImage.prototype.unbindEvents = function () {
			this.proxyImage.removeEventListener('load', this);
			this.proxyImage.removeEventListener('error', this);
			this.img.removeEventListener('load', this);
			this.img.removeEventListener('error', this);
		};

		// -------------------------- Background -------------------------- //

		function Background(url, element) {
			this.url = url;
			this.element = element;
			this.img = new Image();
		}

		// inherit LoadingImage prototype
		Background.prototype = Object.create(LoadingImage.prototype);

		Background.prototype.check = function () {
			this.img.addEventListener('load', this);
			this.img.addEventListener('error', this);
			this.img.src = this.url;
			// check if image is already complete
			var isComplete = this.getIsImageComplete();
			if (isComplete) {
				this.confirm(this.img.naturalWidth !== 0, 'naturalWidth');
				this.unbindEvents();
			}
		};

		Background.prototype.unbindEvents = function () {
			this.img.removeEventListener('load', this);
			this.img.removeEventListener('error', this);
		};

		Background.prototype.confirm = function (isLoaded, message) {
			this.isLoaded = isLoaded;
			this.emitEvent('progress', [this, this.element, message]);
		};

		// -------------------------- jQuery -------------------------- //

		ImagesLoaded.makeJQueryPlugin = function (jQuery) {
			jQuery = jQuery || window.jQuery;
			if (!jQuery) {
				return;
			}
			// set local variable
			$ = jQuery;
			// $().imagesLoaded()
			$.fn.imagesLoaded = function (options, callback) {
				var instance = new ImagesLoaded(this, options, callback);
				return instance.jqDeferred.promise($(this));
			};
		};
		// try making plugin
		ImagesLoaded.makeJQueryPlugin();

		// --------------------------  -------------------------- //

		return ImagesLoaded;

	});

/*!
 * Flickity imagesLoaded v2.0.0
 * enables imagesLoaded option for Flickity
 */

/*jshint browser: true, strict: true, undef: true, unused: true */

(function (window, factory) {
	// universal module definition
	/*jshint strict: false */ /*globals define, module, require */
	if (typeof define == 'function' && define.amd) {
		// AMD
		define([
			'flickity/js/index',
			'imagesloaded/imagesloaded'
		], function (Flickity, imagesLoaded) {
			return factory(window, Flickity, imagesLoaded);
		});
	} else if (typeof module == 'object' && module.exports) {
		// CommonJS
		module.exports = factory(
			window,
			require('flickity'),
			require('imagesloaded')
		);
	} else {
		// browser global
		window.Flickity = factory(
			window,
			window.Flickity,
			window.imagesLoaded
		);
	}

}(window, function factory(window, Flickity, imagesLoaded) {
	'use strict';

	Flickity.createMethods.push('_createImagesLoaded');

	var proto = Flickity.prototype;

	proto._createImagesLoaded = function () {
		this.on('activate', this.imagesLoaded);
	};

	proto.imagesLoaded = function () {
		if (!this.options.imagesLoaded) {
			return;
		}
		var _this = this;
		function onImagesLoadedProgress(instance, image) {
			var cell = _this.getParentCell(image.img);
			_this.cellSizeChange(cell && cell.element);
			if (!_this.options.freeScroll) {
				_this.positionSliderAtSelected();
			}
		}
		imagesLoaded(this.slider).on('progress', onImagesLoadedProgress);
	};

	return Flickity;

}));


/**
 * Flickity fade v1.0.0
 * Fade between Flickity slides
 */

/* jshint browser: true, undef: true, unused: true */

( function( window, factory ) {
  // universal module definition
  /*globals define, module, require */
  if ( typeof define == 'function' && define.amd ) {
    // AMD
    define( [
      'flickity/js/index',
      'fizzy-ui-utils/utils',
    ], factory );
  } else if ( typeof module == 'object' && module.exports ) {
    // CommonJS
    module.exports = factory(
      require('flickity'),
      require('fizzy-ui-utils')
    );
  } else {
    // browser global
    factory(
      window.Flickity,
      window.fizzyUIUtils
    );
  }

}( this, function factory( Flickity, utils ) {

// ---- Slide ---- //

var Slide = Flickity.Slide;

var slideUpdateTarget = Slide.prototype.updateTarget;
Slide.prototype.updateTarget = function() {
  slideUpdateTarget.apply( this, arguments );
  if ( !this.parent.options.fade ) {
    return;
  }
  // position cells at selected target
  var slideTargetX = this.target - this.x;
  var firstCellX = this.cells[0].x;
  this.cells.forEach( function( cell ) {
    var targetX = cell.x - firstCellX - slideTargetX;
    cell.renderPosition( targetX );
  });
};

Slide.prototype.setOpacity = function( alpha ) {
  this.cells.forEach( function( cell ) {
    cell.element.style.opacity = alpha;
  });
};

// ---- Flickity ---- //

var proto = Flickity.prototype;

Flickity.createMethods.push('_createFade');

proto._createFade = function() {
  this.fadeIndex = this.selectedIndex;
  this.prevSelectedIndex = this.selectedIndex;
  this.on( 'select', this.onSelectFade );
  this.on( 'dragEnd', this.onDragEndFade );
  this.on( 'settle', this.onSettleFade );
  this.on( 'activate', this.onActivateFade );
  this.on( 'deactivate', this.onDeactivateFade );
};

var updateSlides = proto.updateSlides;
proto.updateSlides = function() {
  updateSlides.apply( this, arguments );
  if ( !this.options.fade ) {
    return;
  }
  // set initial opacity
  this.slides.forEach( function( slide, i ) {
    var alpha = i == this.selectedIndex ? 1 : 0;
    slide.setOpacity( alpha );
  }, this );
};

/* ---- events ---- */

proto.onSelectFade = function() {
  // in case of resize, keep fadeIndex within current count
  this.fadeIndex = Math.min( this.prevSelectedIndex, this.slides.length - 1 );
  this.prevSelectedIndex = this.selectedIndex;
};

proto.onSettleFade = function() {
  delete this.didDragEnd;
  if ( !this.options.fade ) {
    return;
  }
  // set full and 0 opacity on selected & faded slides
  this.selectedSlide.setOpacity( 1 );
  var fadedSlide = this.slides[ this.fadeIndex ];
  if ( fadedSlide && this.fadeIndex != this.selectedIndex ) {
    this.slides[ this.fadeIndex ].setOpacity( 0 );
  }
};

proto.onDragEndFade = function() {
  // set flag
  this.didDragEnd = true;
};

proto.onActivateFade = function() {
  if ( this.options.fade ) {
    this.element.classList.add('is-fade');
  }
};

proto.onDeactivateFade = function() {
  if ( !this.options.fade ) {
    return;
  }
  this.element.classList.remove('is-fade');
  // reset opacity
  this.slides.forEach( function( slide ) {
    slide.setOpacity('');
  });
};

/* ---- position & fading ---- */

var positionSlider = proto.positionSlider;
proto.positionSlider = function() {
  if ( !this.options.fade ) {
    positionSlider.apply( this, arguments );
    return;
  }

  this.fadeSlides();
  this.dispatchScrollEvent();
};

var positionSliderAtSelected = proto.positionSliderAtSelected;
proto.positionSliderAtSelected = function() {
  if ( this.options.fade ) {
    // position fade slider at origin
    this.setTranslateX( 0 );
  }
  positionSliderAtSelected.apply( this, arguments );
};

proto.fadeSlides = function() {
  if ( this.slides.length < 2 ) {
    return;
  }
  // get slides to fade-in & fade-out
  var indexes = this.getFadeIndexes();
  var fadeSlideA = this.slides[ indexes.a ];
  var fadeSlideB = this.slides[ indexes.b ];
  var distance = this.wrapDifference( fadeSlideA.target, fadeSlideB.target );
  var progress = this.wrapDifference( fadeSlideA.target, -this.x );
  progress = progress / distance;

  fadeSlideA.setOpacity( 1 - progress );
  fadeSlideB.setOpacity( progress );

  // hide previous slide
  var fadeHideIndex = indexes.a;
  if ( this.isDragging ) {
    fadeHideIndex = progress > 0.5 ? indexes.a : indexes.b;
  }
  var isNewHideIndex = this.fadeHideIndex != undefined &&
    this.fadeHideIndex != fadeHideIndex &&
    this.fadeHideIndex != indexes.a &&
    this.fadeHideIndex != indexes.b;
  if ( isNewHideIndex ) {
    // new fadeHideSlide set, hide previous
    this.slides[ this.fadeHideIndex ].setOpacity( 0 );
  }
  this.fadeHideIndex = fadeHideIndex;
};

proto.getFadeIndexes = function() {
  if ( !this.isDragging && !this.didDragEnd ) {
    return {
      a: this.fadeIndex,
      b: this.selectedIndex,
    };
  }
  if ( this.options.wrapAround ) {
    return this.getFadeDragWrapIndexes();
  } else {
    return this.getFadeDragLimitIndexes();
  }
};

proto.getFadeDragWrapIndexes = function() {
  var distances = this.slides.map( function( slide, i ) {
    return this.getSlideDistance( -this.x, i );
  }, this );
  var absDistances = distances.map( function( distance ) {
    return Math.abs( distance );
  });
  var minDistance = Math.min.apply( Math, absDistances );
  var closestIndex = absDistances.indexOf( minDistance );
  var distance = distances[ closestIndex ];
  var len = this.slides.length;

  var delta = distance >= 0 ? 1 : -1;
  return {
    a: closestIndex,
    b: utils.modulo( closestIndex + delta, len ),
  };
};

proto.getFadeDragLimitIndexes = function() {
  // calculate closest previous slide
  var dragIndex = 0;
  for ( var i=0; i < this.slides.length - 1; i++ ) {
    var slide = this.slides[i];
    if ( -this.x < slide.target ) {
      break;
    }
    dragIndex = i;
  }
  return {
    a: dragIndex,
    b: dragIndex + 1,
  };
};

proto.wrapDifference = function( a, b ) {
  var diff = b - a;

  if ( !this.options.wrapAround ) {
    return diff;
  }

  var diffPlus = diff + this.slideableWidth;
  var diffMinus = diff - this.slideableWidth;
  if ( Math.abs( diffPlus ) < Math.abs( diff ) ) {
    diff = diffPlus;
  }
  if ( Math.abs( diffMinus ) < Math.abs( diff ) ) {
    diff = diffMinus;
  }
  return diff;
};

// ---- wrapAround ---- //

var _getWrapShiftCells = proto._getWrapShiftCells;
proto._getWrapShiftCells = function() {
  if ( !this.options.fade ) {
    _getWrapShiftCells.apply( this, arguments );
  }
};

var shiftWrapCells = proto.shiftWrapCells;
proto.shiftWrapCells = function() {
  if ( !this.options.fade ) {
    shiftWrapCells.apply( this, arguments );
  }
};

return Flickity;

}));

/*!
 * Isotope PACKAGED v2.2.2
 *
 * Licensed GPLv3 for open source use
 * or Isotope Commercial License for commercial use
 *
 * http://isotope.metafizzy.co
 * Copyright 2015 Metafizzy
 */

/**
 * Bridget makes jQuery widgets
 * v1.1.0
 * MIT license
 */

( function( window ) {



	// -------------------------- utils -------------------------- //

	var slice = Array.prototype.slice;

	function noop() {}

	// -------------------------- definition -------------------------- //

	function defineBridget( $ ) {

		// bail if no jQuery
		if ( !$ ) {
			return;
		}

		// -------------------------- addOptionMethod -------------------------- //

		/**
		 * adds option method -> $().plugin('option', {...})
		 * @param {Function} PluginClass - constructor class
		 */
		function addOptionMethod( PluginClass ) {
			// don't overwrite original option method
			if ( PluginClass.prototype.option ) {
				return;
			}

			// option setter
			PluginClass.prototype.option = function( opts ) {
				// bail out if not an object
				if ( !$.isPlainObject( opts ) ){
					return;
				}
				this.options = $.extend( true, this.options, opts );
			};
		}

		// -------------------------- plugin bridge -------------------------- //

		// helper function for logging errors
		// $.error breaks jQuery chaining
		var logError = typeof console === 'undefined' ? noop :
			function( message ) {
				console.error( message );
			};

		/**
		 * jQuery plugin bridge, access methods like $elem.plugin('method')
		 * @param {String} namespace - plugin name
		 * @param {Function} PluginClass - constructor class
		 */
		function bridge( namespace, PluginClass ) {
			// add to jQuery fn namespace
			$.fn[ namespace ] = function( options ) {
				if ( typeof options === 'string' ) {
					// call plugin method when first argument is a string
					// get arguments for method
					var args = slice.call( arguments, 1 );

					for ( var i=0, len = this.length; i < len; i++ ) {
						var elem = this[i];
						var instance = $.data( elem, namespace );
						if ( !instance ) {
							logError( "cannot call methods on " + namespace + " prior to initialization; " +
								"attempted to call '" + options + "'" );
							continue;
						}
						if ( !$.isFunction( instance[options] ) || options.charAt(0) === '_' ) {
							logError( "no such method '" + options + "' for " + namespace + " instance" );
							continue;
						}

						// trigger method with arguments
						var returnValue = instance[ options ].apply( instance, args );

						// break look and return first value if provided
						if ( returnValue !== undefined ) {
							return returnValue;
						}
					}
					// return this if no return value
					return this;
				} else {
					return this.each( function() {
						var instance = $.data( this, namespace );
						if ( instance ) {
							// apply options & init
							instance.option( options );
							instance._init();
						} else {
							// initialize new instance
							instance = new PluginClass( this, options );
							$.data( this, namespace, instance );
						}
					});
				}
			};

		}

		// -------------------------- bridget -------------------------- //

		/**
		 * converts a Prototypical class into a proper jQuery plugin
		 *   the class must have a ._init method
		 * @param {String} namespace - plugin name, used in $().pluginName
		 * @param {Function} PluginClass - constructor class
		 */
		$.bridget = function( namespace, PluginClass ) {
			addOptionMethod( PluginClass );
			bridge( namespace, PluginClass );
		};

		return $.bridget;

	}

	// transport
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( 'jquery-bridget/jquery.bridget',[ 'jquery' ], defineBridget );
	} else if ( typeof exports === 'object' ) {
		defineBridget( require('jquery') );
	} else {
		// get jquery from browser global
		defineBridget( window.jQuery );
	}

})( window );

/*!
 * eventie v1.0.6
 * event binding helper
 *   eventie.bind( elem, 'click', myFn )
 *   eventie.unbind( elem, 'click', myFn )
 * MIT license
 */

/*jshint browser: true, undef: true, unused: true */
/*global define: false, module: false */

( function( window ) {



	var docElem = document.documentElement;

	var bind = function() {};

	function getIEEvent( obj ) {
		var event = window.event;
		// add event.target
		event.target = event.target || event.srcElement || obj;
		return event;
	}

	if ( docElem.addEventListener ) {
		bind = function( obj, type, fn ) {
			obj.addEventListener( type, fn, false );
		};
	} else if ( docElem.attachEvent ) {
		bind = function( obj, type, fn ) {
			obj[ type + fn ] = fn.handleEvent ?
				function() {
					var event = getIEEvent( obj );
					fn.handleEvent.call( fn, event );
				} :
				function() {
					var event = getIEEvent( obj );
					fn.call( obj, event );
				};
			obj.attachEvent( "on" + type, obj[ type + fn ] );
		};
	}

	var unbind = function() {};

	if ( docElem.removeEventListener ) {
		unbind = function( obj, type, fn ) {
			obj.removeEventListener( type, fn, false );
		};
	} else if ( docElem.detachEvent ) {
		unbind = function( obj, type, fn ) {
			obj.detachEvent( "on" + type, obj[ type + fn ] );
			try {
				delete obj[ type + fn ];
			} catch ( err ) {
				// can't delete window object properties
				obj[ type + fn ] = undefined;
			}
		};
	}

	var eventie = {
		bind: bind,
		unbind: unbind
	};

	// ----- module definition ----- //

	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( 'eventie/eventie',eventie );
	} else if ( typeof exports === 'object' ) {
		// CommonJS
		module.exports = eventie;
	} else {
		// browser global
		window.eventie = eventie;
	}

})( window );

/*!
 * EventEmitter v4.2.11 - git.io/ee
 * Unlicense - http://unlicense.org/
 * Oliver Caldwell - http://oli.me.uk/
 * @preserve
 */

;(function () {
	'use strict';

	/**
	 * Class for managing events.
	 * Can be extended to provide event functionality in other classes.
	 *
	 * @class EventEmitter Manages event registering and emitting.
	 */
	function EventEmitter() {}

	// Shortcuts to improve speed and size
	var proto = EventEmitter.prototype;
	var exports = this;
	var originalGlobalValue = exports.EventEmitter;

	/**
	 * Finds the index of the listener for the event in its storage array.
	 *
	 * @param {Function[]} listeners Array of listeners to search through.
	 * @param {Function} listener Method to look for.
	 * @return {Number} Index of the specified listener, -1 if not found
	 * @api private
	 */
	function indexOfListener(listeners, listener) {
		var i = listeners.length;
		while (i--) {
			if (listeners[i].listener === listener) {
				return i;
			}
		}

		return -1;
	}

	/**
	 * Alias a method while keeping the context correct, to allow for overwriting of target method.
	 *
	 * @param {String} name The name of the target method.
	 * @return {Function} The aliased method
	 * @api private
	 */
	function alias(name) {
		return function aliasClosure() {
			return this[name].apply(this, arguments);
		};
	}

	/**
	 * Returns the listener array for the specified event.
	 * Will initialise the event object and listener arrays if required.
	 * Will return an object if you use a regex search. The object contains keys for each matched event. So /ba[rz]/ might return an object containing bar and baz. But only if you have either defined them with defineEvent or added some listeners to them.
	 * Each property in the object response is an array of listener functions.
	 *
	 * @param {String|RegExp} evt Name of the event to return the listeners from.
	 * @return {Function[]|Object} All listener functions for the event.
	 */
	proto.getListeners = function getListeners(evt) {
		var events = this._getEvents();
		var response;
		var key;

		// Return a concatenated array of all matching events if
		// the selector is a regular expression.
		if (evt instanceof RegExp) {
			response = {};
			for (key in events) {
				if (events.hasOwnProperty(key) && evt.test(key)) {
					response[key] = events[key];
				}
			}
		}
		else {
			response = events[evt] || (events[evt] = []);
		}

		return response;
	};

	/**
	 * Takes a list of listener objects and flattens it into a list of listener functions.
	 *
	 * @param {Object[]} listeners Raw listener objects.
	 * @return {Function[]} Just the listener functions.
	 */
	proto.flattenListeners = function flattenListeners(listeners) {
		var flatListeners = [];
		var i;

		for (i = 0; i < listeners.length; i += 1) {
			flatListeners.push(listeners[i].listener);
		}

		return flatListeners;
	};

	/**
	 * Fetches the requested listeners via getListeners but will always return the results inside an object. This is mainly for internal use but others may find it useful.
	 *
	 * @param {String|RegExp} evt Name of the event to return the listeners from.
	 * @return {Object} All listener functions for an event in an object.
	 */
	proto.getListenersAsObject = function getListenersAsObject(evt) {
		var listeners = this.getListeners(evt);
		var response;

		if (listeners instanceof Array) {
			response = {};
			response[evt] = listeners;
		}

		return response || listeners;
	};

	/**
	 * Adds a listener function to the specified event.
	 * The listener will not be added if it is a duplicate.
	 * If the listener returns true then it will be removed after it is called.
	 * If you pass a regular expression as the event name then the listener will be added to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to attach the listener to.
	 * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addListener = function addListener(evt, listener) {
		var listeners = this.getListenersAsObject(evt);
		var listenerIsWrapped = typeof listener === 'object';
		var key;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key) && indexOfListener(listeners[key], listener) === -1) {
				listeners[key].push(listenerIsWrapped ? listener : {
					listener: listener,
					once: false
				});
			}
		}

		return this;
	};

	/**
	 * Alias of addListener
	 */
	proto.on = alias('addListener');

	/**
	 * Semi-alias of addListener. It will add a listener that will be
	 * automatically removed after its first execution.
	 *
	 * @param {String|RegExp} evt Name of the event to attach the listener to.
	 * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addOnceListener = function addOnceListener(evt, listener) {
		return this.addListener(evt, {
			listener: listener,
			once: true
		});
	};

	/**
	 * Alias of addOnceListener.
	 */
	proto.once = alias('addOnceListener');

	/**
	 * Defines an event name. This is required if you want to use a regex to add a listener to multiple events at once. If you don't do this then how do you expect it to know what event to add to? Should it just add to every possible match for a regex? No. That is scary and bad.
	 * You need to tell it what event names should be matched by a regex.
	 *
	 * @param {String} evt Name of the event to create.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.defineEvent = function defineEvent(evt) {
		this.getListeners(evt);
		return this;
	};

	/**
	 * Uses defineEvent to define multiple events.
	 *
	 * @param {String[]} evts An array of event names to define.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.defineEvents = function defineEvents(evts) {
		for (var i = 0; i < evts.length; i += 1) {
			this.defineEvent(evts[i]);
		}
		return this;
	};

	/**
	 * Removes a listener function from the specified event.
	 * When passed a regular expression as the event name, it will remove the listener from all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to remove the listener from.
	 * @param {Function} listener Method to remove from the event.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeListener = function removeListener(evt, listener) {
		var listeners = this.getListenersAsObject(evt);
		var index;
		var key;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key)) {
				index = indexOfListener(listeners[key], listener);

				if (index !== -1) {
					listeners[key].splice(index, 1);
				}
			}
		}

		return this;
	};

	/**
	 * Alias of removeListener
	 */
	proto.off = alias('removeListener');

	/**
	 * Adds listeners in bulk using the manipulateListeners method.
	 * If you pass an object as the second argument you can add to multiple events at once. The object should contain key value pairs of events and listeners or listener arrays. You can also pass it an event name and an array of listeners to be added.
	 * You can also pass it a regular expression to add the array of listeners to all events that match it.
	 * Yeah, this function does quite a bit. That's probably a bad thing.
	 *
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add to multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to add.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addListeners = function addListeners(evt, listeners) {
		// Pass through to manipulateListeners
		return this.manipulateListeners(false, evt, listeners);
	};

	/**
	 * Removes listeners in bulk using the manipulateListeners method.
	 * If you pass an object as the second argument you can remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
	 * You can also pass it an event name and an array of listeners to be removed.
	 * You can also pass it a regular expression to remove the listeners from all events that match it.
	 *
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to remove from multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to remove.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeListeners = function removeListeners(evt, listeners) {
		// Pass through to manipulateListeners
		return this.manipulateListeners(true, evt, listeners);
	};

	/**
	 * Edits listeners in bulk. The addListeners and removeListeners methods both use this to do their job. You should really use those instead, this is a little lower level.
	 * The first argument will determine if the listeners are removed (true) or added (false).
	 * If you pass an object as the second argument you can add/remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
	 * You can also pass it an event name and an array of listeners to be added/removed.
	 * You can also pass it a regular expression to manipulate the listeners of all events that match it.
	 *
	 * @param {Boolean} remove True if you want to remove listeners, false if you want to add.
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add/remove from multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to add/remove.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.manipulateListeners = function manipulateListeners(remove, evt, listeners) {
		var i;
		var value;
		var single = remove ? this.removeListener : this.addListener;
		var multiple = remove ? this.removeListeners : this.addListeners;

		// If evt is an object then pass each of its properties to this method
		if (typeof evt === 'object' && !(evt instanceof RegExp)) {
			for (i in evt) {
				if (evt.hasOwnProperty(i) && (value = evt[i])) {
					// Pass the single listener straight through to the singular method
					if (typeof value === 'function') {
						single.call(this, i, value);
					}
					else {
						// Otherwise pass back to the multiple function
						multiple.call(this, i, value);
					}
				}
			}
		}
		else {
			// So evt must be a string
			// And listeners must be an array of listeners
			// Loop over it and pass each one to the multiple method
			i = listeners.length;
			while (i--) {
				single.call(this, evt, listeners[i]);
			}
		}

		return this;
	};

	/**
	 * Removes all listeners from a specified event.
	 * If you do not specify an event then all listeners will be removed.
	 * That means every event will be emptied.
	 * You can also pass a regex to remove all events that match it.
	 *
	 * @param {String|RegExp} [evt] Optional name of the event to remove all listeners for. Will remove from every event if not passed.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeEvent = function removeEvent(evt) {
		var type = typeof evt;
		var events = this._getEvents();
		var key;

		// Remove different things depending on the state of evt
		if (type === 'string') {
			// Remove all listeners for the specified event
			delete events[evt];
		}
		else if (evt instanceof RegExp) {
			// Remove all events matching the regex.
			for (key in events) {
				if (events.hasOwnProperty(key) && evt.test(key)) {
					delete events[key];
				}
			}
		}
		else {
			// Remove all listeners in all events
			delete this._events;
		}

		return this;
	};

	/**
	 * Alias of removeEvent.
	 *
	 * Added to mirror the node API.
	 */
	proto.removeAllListeners = alias('removeEvent');

	/**
	 * Emits an event of your choice.
	 * When emitted, every listener attached to that event will be executed.
	 * If you pass the optional argument array then those arguments will be passed to every listener upon execution.
	 * Because it uses `apply`, your array of arguments will be passed as if you wrote them out separately.
	 * So they will not arrive within the array on the other side, they will be separate.
	 * You can also pass a regular expression to emit to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
	 * @param {Array} [args] Optional array of arguments to be passed to each listener.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.emitEvent = function emitEvent(evt, args) {
		var listeners = this.getListenersAsObject(evt);
		var listener;
		var i;
		var key;
		var response;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key)) {
				i = listeners[key].length;

				while (i--) {
					// If the listener returns true then it shall be removed from the event
					// The function is executed either with a basic call or an apply if there is an args array
					listener = listeners[key][i];

					if (listener.once === true) {
						this.removeListener(evt, listener.listener);
					}

					response = listener.listener.apply(this, args || []);

					if (response === this._getOnceReturnValue()) {
						this.removeListener(evt, listener.listener);
					}
				}
			}
		}

		return this;
	};

	/**
	 * Alias of emitEvent
	 */
	proto.trigger = alias('emitEvent');

	/**
	 * Subtly different from emitEvent in that it will pass its arguments on to the listeners, as opposed to taking a single array of arguments to pass on.
	 * As with emitEvent, you can pass a regex in place of the event name to emit to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
	 * @param {...*} Optional additional arguments to be passed to each listener.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.emit = function emit(evt) {
		var args = Array.prototype.slice.call(arguments, 1);
		return this.emitEvent(evt, args);
	};

	/**
	 * Sets the current value to check against when executing listeners. If a
	 * listeners return value matches the one set here then it will be removed
	 * after execution. This value defaults to true.
	 *
	 * @param {*} value The new value to check for when executing listeners.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.setOnceReturnValue = function setOnceReturnValue(value) {
		this._onceReturnValue = value;
		return this;
	};

	/**
	 * Fetches the current value to check against when executing listeners. If
	 * the listeners return value matches this one then it should be removed
	 * automatically. It will return true by default.
	 *
	 * @return {*|Boolean} The current value to check for or the default, true.
	 * @api private
	 */
	proto._getOnceReturnValue = function _getOnceReturnValue() {
		if (this.hasOwnProperty('_onceReturnValue')) {
			return this._onceReturnValue;
		}
		else {
			return true;
		}
	};

	/**
	 * Fetches the events object and creates one if required.
	 *
	 * @return {Object} The events storage object.
	 * @api private
	 */
	proto._getEvents = function _getEvents() {
		return this._events || (this._events = {});
	};

	/**
	 * Reverts the global {@link EventEmitter} to its previous value and returns a reference to this version.
	 *
	 * @return {Function} Non conflicting EventEmitter class.
	 */
	EventEmitter.noConflict = function noConflict() {
		exports.EventEmitter = originalGlobalValue;
		return EventEmitter;
	};

	// Expose the class either via AMD, CommonJS or the global object
	if (typeof define === 'function' && define.amd) {
		define('eventEmitter/EventEmitter',[],function () {
			return EventEmitter;
		});
	}
	else if (typeof module === 'object' && module.exports){
		module.exports = EventEmitter;
	}
	else {
		exports.EventEmitter = EventEmitter;
	}
}.call(this));

/*!
 * getStyleProperty v1.0.4
 * original by kangax
 * http://perfectionkills.com/feature-testing-css-properties/
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true */
/*global define: false, exports: false, module: false */

( function( window ) {



	var prefixes = 'Webkit Moz ms Ms O'.split(' ');
	var docElemStyle = document.documentElement.style;

	function getStyleProperty( propName ) {
		if ( !propName ) {
			return;
		}

		// test standard property first
		if ( typeof docElemStyle[ propName ] === 'string' ) {
			return propName;
		}

		// capitalize
		propName = propName.charAt(0).toUpperCase() + propName.slice(1);

		// test vendor specific properties
		var prefixed;
		for ( var i=0, len = prefixes.length; i < len; i++ ) {
			prefixed = prefixes[i] + propName;
			if ( typeof docElemStyle[ prefixed ] === 'string' ) {
				return prefixed;
			}
		}
	}

	// transport
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( 'get-style-property/get-style-property',[],function() {
			return getStyleProperty;
		});
	} else if ( typeof exports === 'object' ) {
		// CommonJS for Component
		module.exports = getStyleProperty;
	} else {
		// browser global
		window.getStyleProperty = getStyleProperty;
	}

})( window );

/*!
 * getSize v1.2.2
 * measure size of elements
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true, unused: true */
/*global define: false, exports: false, require: false, module: false, console: false */

( function( window, undefined ) {



	// -------------------------- helpers -------------------------- //

	// get a number from a string, not a percentage
	function getStyleSize( value ) {
		var num = parseFloat( value );
		// not a percent like '100%', and a number
		var isValid = value.indexOf('%') === -1 && !isNaN( num );
		return isValid && num;
	}

	function noop() {}

	var logError = typeof console === 'undefined' ? noop :
		function( message ) {
			console.error( message );
		};

	// -------------------------- measurements -------------------------- //

	var measurements = [
		'paddingLeft',
		'paddingRight',
		'paddingTop',
		'paddingBottom',
		'marginLeft',
		'marginRight',
		'marginTop',
		'marginBottom',
		'borderLeftWidth',
		'borderRightWidth',
		'borderTopWidth',
		'borderBottomWidth'
	];

	function getZeroSize() {
		var size = {
			width: 0,
			height: 0,
			innerWidth: 0,
			innerHeight: 0,
			outerWidth: 0,
			outerHeight: 0
		};
		for ( var i=0, len = measurements.length; i < len; i++ ) {
			var measurement = measurements[i];
			size[ measurement ] = 0;
		}
		return size;
	}



	function defineGetSize( getStyleProperty ) {

		// -------------------------- setup -------------------------- //

		var isSetup = false;

		var getStyle, boxSizingProp, isBoxSizeOuter;

		/**
		 * setup vars and functions
		 * do it on initial getSize(), rather than on script load
		 * For Firefox bug https://bugzilla.mozilla.org/show_bug.cgi?id=548397
		 */
		function setup() {
			// setup once
			if ( isSetup ) {
				return;
			}
			isSetup = true;

			var getComputedStyle = window.getComputedStyle;
			getStyle = ( function() {
				var getStyleFn = getComputedStyle ?
					function( elem ) {
						return getComputedStyle( elem, null );
					} :
					function( elem ) {
						return elem.currentStyle;
					};

				return function getStyle( elem ) {
					var style = getStyleFn( elem );
					if ( !style ) {
						logError( 'Style returned ' + style +
							'. Are you running this code in a hidden iframe on Firefox? ' +
							'See http://bit.ly/getsizebug1' );
					}
					return style;
				};
			})();

			// -------------------------- box sizing -------------------------- //

			boxSizingProp = getStyleProperty('boxSizing');

			/**
			 * WebKit measures the outer-width on style.width on border-box elems
			 * IE & Firefox measures the inner-width
			 */
			if ( boxSizingProp ) {
				var div = document.createElement('div');
				div.style.width = '200px';
				div.style.padding = '1px 2px 3px 4px';
				div.style.borderStyle = 'solid';
				div.style.borderWidth = '1px 2px 3px 4px';
				div.style[ boxSizingProp ] = 'border-box';

				var body = document.body || document.documentElement;
				body.appendChild( div );
				var style = getStyle( div );

				isBoxSizeOuter = getStyleSize( style.width ) === 200;
				body.removeChild( div );
			}

		}

		// -------------------------- getSize -------------------------- //

		function getSize( elem ) {
			setup();

			// use querySeletor if elem is string
			if ( typeof elem === 'string' ) {
				elem = document.querySelector( elem );
			}

			// do not proceed on non-objects
			if ( !elem || typeof elem !== 'object' || !elem.nodeType ) {
				return;
			}

			var style = getStyle( elem );

			// if hidden, everything is 0
			if ( style.display === 'none' ) {
				return getZeroSize();
			}

			var size = {};
			size.width = elem.offsetWidth;
			size.height = elem.offsetHeight;

			var isBorderBox = size.isBorderBox = !!( boxSizingProp &&
				style[ boxSizingProp ] && style[ boxSizingProp ] === 'border-box' );

			// get all measurements
			for ( var i=0, len = measurements.length; i < len; i++ ) {
				var measurement = measurements[i];
				var value = style[ measurement ];
				value = mungeNonPixel( elem, value );
				var num = parseFloat( value );
				// any 'auto', 'medium' value will be 0
				size[ measurement ] = !isNaN( num ) ? num : 0;
			}

			var paddingWidth = size.paddingLeft + size.paddingRight;
			var paddingHeight = size.paddingTop + size.paddingBottom;
			var marginWidth = size.marginLeft + size.marginRight;
			var marginHeight = size.marginTop + size.marginBottom;
			var borderWidth = size.borderLeftWidth + size.borderRightWidth;
			var borderHeight = size.borderTopWidth + size.borderBottomWidth;

			var isBorderBoxSizeOuter = isBorderBox && isBoxSizeOuter;

			// overwrite width and height if we can get it from style
			var styleWidth = getStyleSize( style.width );
			if ( styleWidth !== false ) {
				size.width = styleWidth +
					// add padding and border unless it's already including it
					( isBorderBoxSizeOuter ? 0 : paddingWidth + borderWidth );
			}

			var styleHeight = getStyleSize( style.height );
			if ( styleHeight !== false ) {
				size.height = styleHeight +
					// add padding and border unless it's already including it
					( isBorderBoxSizeOuter ? 0 : paddingHeight + borderHeight );
			}

			size.innerWidth = size.width - ( paddingWidth + borderWidth );
			size.innerHeight = size.height - ( paddingHeight + borderHeight );

			size.outerWidth = size.width + marginWidth;
			size.outerHeight = size.height + marginHeight;

			return size;
		}

		// IE8 returns percent values, not pixels
		// taken from jQuery's curCSS
		function mungeNonPixel( elem, value ) {
			// IE8 and has percent value
			if ( window.getComputedStyle || value.indexOf('%') === -1 ) {
				return value;
			}
			var style = elem.style;
			// Remember the original values
			var left = style.left;
			var rs = elem.runtimeStyle;
			var rsLeft = rs && rs.left;

			// Put in the new values to get a computed value out
			if ( rsLeft ) {
				rs.left = elem.currentStyle.left;
			}
			style.left = value;
			value = style.pixelLeft;

			// Revert the changed values
			style.left = left;
			if ( rsLeft ) {
				rs.left = rsLeft;
			}

			return value;
		}

		return getSize;

	}

	// transport
	if ( typeof define === 'function' && define.amd ) {
		// AMD for RequireJS
		define( 'get-size/get-size',[ 'get-style-property/get-style-property' ], defineGetSize );
	} else if ( typeof exports === 'object' ) {
		// CommonJS for Component
		module.exports = defineGetSize( require('desandro-get-style-property') );
	} else {
		// browser global
		window.getSize = defineGetSize( window.getStyleProperty );
	}

})( window );

/*!
 * docReady v1.0.4
 * Cross browser DOMContentLoaded event emitter
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true, unused: true*/
/*global define: false, require: false, module: false */

( function( window ) {



	var document = window.document;
	// collection of functions to be triggered on ready
	var queue = [];

	function docReady( fn ) {
		// throw out non-functions
		if ( typeof fn !== 'function' ) {
			return;
		}

		if ( docReady.isReady ) {
			// ready now, hit it
			fn();
		} else {
			// queue function when ready
			queue.push( fn );
		}
	}

	docReady.isReady = false;

	// triggered on various doc ready events
	function onReady( event ) {
		// bail if already triggered or IE8 document is not ready just yet
		var isIE8NotReady = event.type === 'readystatechange' && document.readyState !== 'complete';
		if ( docReady.isReady || isIE8NotReady ) {
			return;
		}

		trigger();
	}

	function trigger() {
		docReady.isReady = true;
		// process queue
		for ( var i=0, len = queue.length; i < len; i++ ) {
			var fn = queue[i];
			fn();
		}
	}

	function defineDocReady( eventie ) {
		// trigger ready if page is ready
		if ( document.readyState === 'complete' ) {
			trigger();
		} else {
			// listen for events
			eventie.bind( document, 'DOMContentLoaded', onReady );
			eventie.bind( document, 'readystatechange', onReady );
			eventie.bind( window, 'load', onReady );
		}

		return docReady;
	}

	// transport
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( 'doc-ready/doc-ready',[ 'eventie/eventie' ], defineDocReady );
	} else if ( typeof exports === 'object' ) {
		module.exports = defineDocReady( require('eventie') );
	} else {
		// browser global
		window.docReady = defineDocReady( window.eventie );
	}

})( window );

/**
 * matchesSelector v1.0.3
 * matchesSelector( element, '.selector' )
 * MIT license
 */

/*jshint browser: true, strict: true, undef: true, unused: true */
/*global define: false, module: false */

( function( ElemProto ) {

	'use strict';

	var matchesMethod = ( function() {
		// check for the standard method name first
		if ( ElemProto.matches ) {
			return 'matches';
		}
		// check un-prefixed
		if ( ElemProto.matchesSelector ) {
			return 'matchesSelector';
		}
		// check vendor prefixes
		var prefixes = [ 'webkit', 'moz', 'ms', 'o' ];

		for ( var i=0, len = prefixes.length; i < len; i++ ) {
			var prefix = prefixes[i];
			var method = prefix + 'MatchesSelector';
			if ( ElemProto[ method ] ) {
				return method;
			}
		}
	})();

	// ----- match ----- //

	function match( elem, selector ) {
		return elem[ matchesMethod ]( selector );
	}

	// ----- appendToFragment ----- //

	function checkParent( elem ) {
		// not needed if already has parent
		if ( elem.parentNode ) {
			return;
		}
		var fragment = document.createDocumentFragment();
		fragment.appendChild( elem );
	}

	// ----- query ----- //

	// fall back to using QSA
	// thx @jonathantneal https://gist.github.com/3062955
	function query( elem, selector ) {
		// append to fragment if no parent
		checkParent( elem );

		// match elem with all selected elems of parent
		var elems = elem.parentNode.querySelectorAll( selector );
		for ( var i=0, len = elems.length; i < len; i++ ) {
			// return true if match
			if ( elems[i] === elem ) {
				return true;
			}
		}
		// otherwise return false
		return false;
	}

	// ----- matchChild ----- //

	function matchChild( elem, selector ) {
		checkParent( elem );
		return match( elem, selector );
	}

	// ----- matchesSelector ----- //

	var matchesSelector;

	if ( matchesMethod ) {
		// IE9 supports matchesSelector, but doesn't work on orphaned elems
		// check for that
		var div = document.createElement('div');
		var supportsOrphans = match( div, 'div' );
		matchesSelector = supportsOrphans ? match : matchChild;
	} else {
		matchesSelector = query;
	}

	// transport
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( 'matches-selector/matches-selector',[],function() {
			return matchesSelector;
		});
	} else if ( typeof exports === 'object' ) {
		module.exports = matchesSelector;
	}
	else {
		// browser global
		window.matchesSelector = matchesSelector;
	}

})( Element.prototype );

/**
 * Fizzy UI utils v1.0.1
 * MIT license
 */

/*jshint browser: true, undef: true, unused: true, strict: true */

( function( window, factory ) {
	/*global define: false, module: false, require: false */
	'use strict';
	// universal module definition

	if ( typeof define == 'function' && define.amd ) {
		// AMD
		define( 'fizzy-ui-utils/utils',[
			'doc-ready/doc-ready',
			'matches-selector/matches-selector'
		], function( docReady, matchesSelector ) {
			return factory( window, docReady, matchesSelector );
		});
	} else if ( typeof exports == 'object' ) {
		// CommonJS
		module.exports = factory(
			window,
			require('doc-ready'),
			require('desandro-matches-selector')
		);
	} else {
		// browser global
		window.fizzyUIUtils = factory(
			window,
			window.docReady,
			window.matchesSelector
		);
	}

}( window, function factory( window, docReady, matchesSelector ) {



	var utils = {};

	// ----- extend ----- //

	// extends objects
	utils.extend = function( a, b ) {
		for ( var prop in b ) {
			a[ prop ] = b[ prop ];
		}
		return a;
	};

	// ----- modulo ----- //

	utils.modulo = function( num, div ) {
		return ( ( num % div ) + div ) % div;
	};

	// ----- isArray ----- //

	var objToString = Object.prototype.toString;
	utils.isArray = function( obj ) {
		return objToString.call( obj ) == '[object Array]';
	};

	// ----- makeArray ----- //

	// turn element or nodeList into an array
	utils.makeArray = function( obj ) {
		var ary = [];
		if ( utils.isArray( obj ) ) {
			// use object if already an array
			ary = obj;
		} else if ( obj && typeof obj.length == 'number' ) {
			// convert nodeList to array
			for ( var i=0, len = obj.length; i < len; i++ ) {
				ary.push( obj[i] );
			}
		} else {
			// array of single index
			ary.push( obj );
		}
		return ary;
	};

	// ----- indexOf ----- //

	// index of helper cause IE8
	utils.indexOf = Array.prototype.indexOf ? function( ary, obj ) {
		return ary.indexOf( obj );
	} : function( ary, obj ) {
		for ( var i=0, len = ary.length; i < len; i++ ) {
			if ( ary[i] === obj ) {
				return i;
			}
		}
		return -1;
	};

	// ----- removeFrom ----- //

	utils.removeFrom = function( ary, obj ) {
		var index = utils.indexOf( ary, obj );
		if ( index != -1 ) {
			ary.splice( index, 1 );
		}
	};

	// ----- isElement ----- //

	// http://stackoverflow.com/a/384380/182183
	utils.isElement = typeof HTMLElement == 'object' ?
		function isElementDOM2( obj ) {
			return obj instanceof HTMLElement;
		} :
		function isElementQuirky( obj ) {
			return obj && typeof obj == 'object' && obj !== null &&
				obj.nodeType == 1 && typeof obj.nodeName == 'string';
		};

	// ----- setText ----- //

	utils.setText = ( function() {
		var setTextProperty;
		function setText( elem, text ) {
			// only check setTextProperty once
			setTextProperty = setTextProperty || ( document.documentElement.textContent !== undefined ? 'textContent' : 'innerText' );
			elem[ setTextProperty ] = text;
		}
		return setText;
	})();

	// ----- getParent ----- //

	utils.getParent = function( elem, selector ) {
		while ( elem != document.body ) {
			elem = elem.parentNode;
			if ( matchesSelector( elem, selector ) ) {
				return elem;
			}
		}
	};

	// ----- getQueryElement ----- //

	// use element as selector string
	utils.getQueryElement = function( elem ) {
		if ( typeof elem == 'string' ) {
			return document.querySelector( elem );
		}
		return elem;
	};

	// ----- handleEvent ----- //

	// enable .ontype to trigger from .addEventListener( elem, 'type' )
	utils.handleEvent = function( event ) {
		var method = 'on' + event.type;
		if ( this[ method ] ) {
			this[ method ]( event );
		}
	};

	// ----- filterFindElements ----- //

	utils.filterFindElements = function( elems, selector ) {
		// make array of elems
		elems = utils.makeArray( elems );
		var ffElems = [];

		for ( var i=0, len = elems.length; i < len; i++ ) {
			var elem = elems[i];
			// check that elem is an actual element
			if ( !utils.isElement( elem ) ) {
				continue;
			}
			// filter & find items if we have a selector
			if ( selector ) {
				// filter siblings
				if ( matchesSelector( elem, selector ) ) {
					ffElems.push( elem );
				}
				// find children
				var childElems = elem.querySelectorAll( selector );
				// concat childElems to filterFound array
				for ( var j=0, jLen = childElems.length; j < jLen; j++ ) {
					ffElems.push( childElems[j] );
				}
			} else {
				ffElems.push( elem );
			}
		}

		return ffElems;
	};

	// ----- debounceMethod ----- //

	utils.debounceMethod = function( _class, methodName, threshold ) {
		// original method
		var method = _class.prototype[ methodName ];
		var timeoutName = methodName + 'Timeout';

		_class.prototype[ methodName ] = function() {
			var timeout = this[ timeoutName ];
			if ( timeout ) {
				clearTimeout( timeout );
			}
			var args = arguments;

			var _this = this;
			this[ timeoutName ] = setTimeout( function() {
				method.apply( _this, args );
				delete _this[ timeoutName ];
			}, threshold || 100 );
		};
	};

	// ----- htmlInit ----- //

	// http://jamesroberts.name/blog/2010/02/22/string-functions-for-javascript-trim-to-camel-case-to-dashed-and-to-underscore/
	utils.toDashed = function( str ) {
		return str.replace( /(.)([A-Z])/g, function( match, $1, $2 ) {
			return $1 + '-' + $2;
		}).toLowerCase();
	};

	var console = window.console;
	/**
	 * allow user to initialize classes via .js-namespace class
	 * htmlInit( Widget, 'widgetName' )
	 * options are parsed from data-namespace-option attribute
	 */
	utils.htmlInit = function( WidgetClass, namespace ) {
		docReady( function() {
			var dashedNamespace = utils.toDashed( namespace );
			var elems = document.querySelectorAll( '.js-' + dashedNamespace );
			var dataAttr = 'data-' + dashedNamespace + '-options';

			for ( var i=0, len = elems.length; i < len; i++ ) {
				var elem = elems[i];
				var attr = elem.getAttribute( dataAttr );
				var options;
				try {
					options = attr && JSON.parse( attr );
				} catch ( error ) {
					// log error, do not initialize
					if ( console ) {
						console.error( 'Error parsing ' + dataAttr + ' on ' +
							elem.nodeName.toLowerCase() + ( elem.id ? '#' + elem.id : '' ) + ': ' +
							error );
					}
					continue;
				}
				// initialize
				var instance = new WidgetClass( elem, options );
				// make available via $().data('layoutname')
				var jQuery = window.jQuery;
				if ( jQuery ) {
					jQuery.data( elem, namespace, instance );
				}
			}
		});
	};

	// -----  ----- //

	return utils;

}));

/**
 * Outlayer Item
 */

( function( window, factory ) {
	'use strict';
	// universal module definition
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( 'outlayer/item',[
				'eventEmitter/EventEmitter',
				'get-size/get-size',
				'get-style-property/get-style-property',
				'fizzy-ui-utils/utils'
			],
			function( EventEmitter, getSize, getStyleProperty, utils ) {
				return factory( window, EventEmitter, getSize, getStyleProperty, utils );
			}
		);
	} else if (typeof exports === 'object') {
		// CommonJS
		module.exports = factory(
			window,
			require('wolfy87-eventemitter'),
			require('get-size'),
			require('desandro-get-style-property'),
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		window.Outlayer = {};
		window.Outlayer.Item = factory(
			window,
			window.EventEmitter,
			window.getSize,
			window.getStyleProperty,
			window.fizzyUIUtils
		);
	}

}( window, function factory( window, EventEmitter, getSize, getStyleProperty, utils ) {
	'use strict';

	// ----- helpers ----- //

	var getComputedStyle = window.getComputedStyle;
	var getStyle = getComputedStyle ?
		function( elem ) {
			return getComputedStyle( elem, null );
		} :
		function( elem ) {
			return elem.currentStyle;
		};


	function isEmptyObj( obj ) {
		for ( var prop in obj ) {
			return false;
		}
		prop = null;
		return true;
	}

	// -------------------------- CSS3 support -------------------------- //

	var transitionProperty = getStyleProperty('transition');
	var transformProperty = getStyleProperty('transform');
	var supportsCSS3 = transitionProperty && transformProperty;
	var is3d = !!getStyleProperty('perspective');

	var transitionEndEvent = {
		WebkitTransition: 'webkitTransitionEnd',
		MozTransition: 'transitionend',
		OTransition: 'otransitionend',
		transition: 'transitionend'
	}[ transitionProperty ];

	// properties that could have vendor prefix
	var prefixableProperties = [
		'transform',
		'transition',
		'transitionDuration',
		'transitionProperty'
	];

	// cache all vendor properties
	var vendorProperties = ( function() {
		var cache = {};
		for ( var i=0, len = prefixableProperties.length; i < len; i++ ) {
			var prop = prefixableProperties[i];
			var supportedProp = getStyleProperty( prop );
			if ( supportedProp && supportedProp !== prop ) {
				cache[ prop ] = supportedProp;
			}
		}
		return cache;
	})();

	// -------------------------- Item -------------------------- //

	function Item( element, layout ) {
		if ( !element ) {
			return;
		}

		this.element = element;
		// parent layout class, i.e. Masonry, Isotope, or Packery
		this.layout = layout;
		this.position = {
			x: 0,
			y: 0
		};

		this._create();
	}

	// inherit EventEmitter
	utils.extend( Item.prototype, EventEmitter.prototype );

	Item.prototype._create = function() {
		// transition objects
		this._transn = {
			ingProperties: {},
			clean: {},
			onEnd: {}
		};

		this.css({
			position: 'absolute'
		});
	};

	// trigger specified handler for event type
	Item.prototype.handleEvent = function( event ) {
		var method = 'on' + event.type;
		if ( this[ method ] ) {
			this[ method ]( event );
		}
	};

	Item.prototype.getSize = function() {
		this.size = getSize( this.element );
	};

	/**
	 * apply CSS styles to element
	 * @param {Object} style
	 */
	Item.prototype.css = function( style ) {
		var elemStyle = this.element.style;

		for ( var prop in style ) {
			// use vendor property if available
			var supportedProp = vendorProperties[ prop ] || prop;
			elemStyle[ supportedProp ] = style[ prop ];
		}
	};

	// measure position, and sets it
	Item.prototype.getPosition = function() {
		var style = getStyle( this.element );
		var layoutOptions = this.layout.options;
		var isOriginLeft = layoutOptions.isOriginLeft;
		var isOriginTop = layoutOptions.isOriginTop;
		var xValue = style[ isOriginLeft ? 'left' : 'right' ];
		var yValue = style[ isOriginTop ? 'top' : 'bottom' ];
		// convert percent to pixels
		var layoutSize = this.layout.size;
		var x = xValue.indexOf('%') != -1 ?
			( parseFloat( xValue ) / 100 ) * layoutSize.width : parseInt( xValue, 10 );
		var y = yValue.indexOf('%') != -1 ?
			( parseFloat( yValue ) / 100 ) * layoutSize.height : parseInt( yValue, 10 );

		// clean up 'auto' or other non-integer values
		x = isNaN( x ) ? 0 : x;
		y = isNaN( y ) ? 0 : y;
		// remove padding from measurement
		x -= isOriginLeft ? layoutSize.paddingLeft : layoutSize.paddingRight;
		y -= isOriginTop ? layoutSize.paddingTop : layoutSize.paddingBottom;

		this.position.x = x;
		this.position.y = y;
	};

	// set settled position, apply padding
	Item.prototype.layoutPosition = function() {
		var layoutSize = this.layout.size;
		var layoutOptions = this.layout.options;
		var style = {};

		// x
		var xPadding = layoutOptions.isOriginLeft ? 'paddingLeft' : 'paddingRight';
		var xProperty = layoutOptions.isOriginLeft ? 'left' : 'right';
		var xResetProperty = layoutOptions.isOriginLeft ? 'right' : 'left';

		var x = this.position.x + layoutSize[ xPadding ];
		// set in percentage or pixels
		style[ xProperty ] = this.getXValue( x );
		// reset other property
		style[ xResetProperty ] = '';

		// y
		var yPadding = layoutOptions.isOriginTop ? 'paddingTop' : 'paddingBottom';
		var yProperty = layoutOptions.isOriginTop ? 'top' : 'bottom';
		var yResetProperty = layoutOptions.isOriginTop ? 'bottom' : 'top';

		var y = this.position.y + layoutSize[ yPadding ];
		// set in percentage or pixels
		style[ yProperty ] = this.getYValue( y );
		// reset other property
		style[ yResetProperty ] = '';

		this.css( style );
		this.emitEvent( 'layout', [ this ] );
	};

	Item.prototype.getXValue = function( x ) {
		var layoutOptions = this.layout.options;
		return layoutOptions.percentPosition && !layoutOptions.isHorizontal ?
			( ( x / this.layout.size.width ) * 100 ) + '%' : x + 'px';
	};

	Item.prototype.getYValue = function( y ) {
		var layoutOptions = this.layout.options;
		return layoutOptions.percentPosition && layoutOptions.isHorizontal ?
			( ( y / this.layout.size.height ) * 100 ) + '%' : y + 'px';
	};


	Item.prototype._transitionTo = function( x, y ) {
		this.getPosition();
		// get current x & y from top/left
		var curX = this.position.x;
		var curY = this.position.y;

		var compareX = parseInt( x, 10 );
		var compareY = parseInt( y, 10 );
		var didNotMove = compareX === this.position.x && compareY === this.position.y;

		// save end position
		this.setPosition( x, y );

		// if did not move and not transitioning, just go to layout
		if ( didNotMove && !this.isTransitioning ) {
			this.layoutPosition();
			return;
		}

		var transX = x - curX;
		var transY = y - curY;
		var transitionStyle = {};
		transitionStyle.transform = this.getTranslate( transX, transY );

		this.transition({
			to: transitionStyle,
			onTransitionEnd: {
				transform: this.layoutPosition
			},
			isCleaning: true
		});
	};

	Item.prototype.getTranslate = function( x, y ) {
		// flip cooridinates if origin on right or bottom
		var layoutOptions = this.layout.options;
		x = layoutOptions.isOriginLeft ? x : -x;
		y = layoutOptions.isOriginTop ? y : -y;

		if ( is3d ) {
			return 'translate3d(' + x + 'px, ' + y + 'px, 0)';
		}

		return 'translate(' + x + 'px, ' + y + 'px)';
	};

	// non transition + transform support
	Item.prototype.goTo = function( x, y ) {
		this.setPosition( x, y );
		this.layoutPosition();
	};

	// use transition and transforms if supported
	Item.prototype.moveTo = supportsCSS3 ?
		Item.prototype._transitionTo : Item.prototype.goTo;

	Item.prototype.setPosition = function( x, y ) {
		this.position.x = parseInt( x, 10 );
		this.position.y = parseInt( y, 10 );
	};

	// ----- transition ----- //

	/**
	 * @param {Object} style - CSS
	 * @param {Function} onTransitionEnd
	 */

	// non transition, just trigger callback
	Item.prototype._nonTransition = function( args ) {
		this.css( args.to );
		if ( args.isCleaning ) {
			this._removeStyles( args.to );
		}
		for ( var prop in args.onTransitionEnd ) {
			args.onTransitionEnd[ prop ].call( this );
		}
	};

	/**
	 * proper transition
	 * @param {Object} args - arguments
	 *   @param {Object} to - style to transition to
	 *   @param {Object} from - style to start transition from
	 *   @param {Boolean} isCleaning - removes transition styles after transition
	 *   @param {Function} onTransitionEnd - callback
	 */
	Item.prototype._transition = function( args ) {
		// redirect to nonTransition if no transition duration
		if ( !parseFloat( this.layout.options.transitionDuration ) ) {
			this._nonTransition( args );
			return;
		}

		var _transition = this._transn;
		// keep track of onTransitionEnd callback by css property
		for ( var prop in args.onTransitionEnd ) {
			_transition.onEnd[ prop ] = args.onTransitionEnd[ prop ];
		}
		// keep track of properties that are transitioning
		for ( prop in args.to ) {
			_transition.ingProperties[ prop ] = true;
			// keep track of properties to clean up when transition is done
			if ( args.isCleaning ) {
				_transition.clean[ prop ] = true;
			}
		}

		// set from styles
		if ( args.from ) {
			this.css( args.from );
			// force redraw. http://blog.alexmaccaw.com/css-transitions
			var h = this.element.offsetHeight;
			// hack for JSHint to hush about unused var
			h = null;
		}
		// enable transition
		this.enableTransition( args.to );
		// set styles that are transitioning
		this.css( args.to );

		this.isTransitioning = true;

	};

	// dash before all cap letters, including first for
	// WebkitTransform => -webkit-transform
	function toDashedAll( str ) {
		return str.replace( /([A-Z])/g, function( $1 ) {
			return '-' + $1.toLowerCase();
		});
	}

	var transitionProps = 'opacity,' +
		toDashedAll( vendorProperties.transform || 'transform' );

	Item.prototype.enableTransition = function(/* style */) {
		// HACK changing transitionProperty during a transition
		// will cause transition to jump
		if ( this.isTransitioning ) {
			return;
		}

		// make `transition: foo, bar, baz` from style object
		// HACK un-comment this when enableTransition can work
		// while a transition is happening
		// var transitionValues = [];
		// for ( var prop in style ) {
		//   // dash-ify camelCased properties like WebkitTransition
		//   prop = vendorProperties[ prop ] || prop;
		//   transitionValues.push( toDashedAll( prop ) );
		// }
		// enable transition styles
		this.css({
			transitionProperty: transitionProps,
			transitionDuration: this.layout.options.transitionDuration
		});
		// listen for transition end event
		this.element.addEventListener( transitionEndEvent, this, false );
	};

	Item.prototype.transition = Item.prototype[ transitionProperty ? '_transition' : '_nonTransition' ];

	// ----- events ----- //

	Item.prototype.onwebkitTransitionEnd = function( event ) {
		this.ontransitionend( event );
	};

	Item.prototype.onotransitionend = function( event ) {
		this.ontransitionend( event );
	};

	// properties that I munge to make my life easier
	var dashedVendorProperties = {
		'-webkit-transform': 'transform',
		'-moz-transform': 'transform',
		'-o-transform': 'transform'
	};

	Item.prototype.ontransitionend = function( event ) {
		// disregard bubbled events from children
		if ( event.target !== this.element ) {
			return;
		}
		var _transition = this._transn;
		// get property name of transitioned property, convert to prefix-free
		var propertyName = dashedVendorProperties[ event.propertyName ] || event.propertyName;

		// remove property that has completed transitioning
		delete _transition.ingProperties[ propertyName ];
		// check if any properties are still transitioning
		if ( isEmptyObj( _transition.ingProperties ) ) {
			// all properties have completed transitioning
			this.disableTransition();
		}
		// clean style
		if ( propertyName in _transition.clean ) {
			// clean up style
			this.element.style[ event.propertyName ] = '';
			delete _transition.clean[ propertyName ];
		}
		// trigger onTransitionEnd callback
		if ( propertyName in _transition.onEnd ) {
			var onTransitionEnd = _transition.onEnd[ propertyName ];
			onTransitionEnd.call( this );
			delete _transition.onEnd[ propertyName ];
		}

		this.emitEvent( 'transitionEnd', [ this ] );
	};

	Item.prototype.disableTransition = function() {
		this.removeTransitionStyles();
		this.element.removeEventListener( transitionEndEvent, this, false );
		this.isTransitioning = false;
	};

	/**
	 * removes style property from element
	 * @param {Object} style
	 **/
	Item.prototype._removeStyles = function( style ) {
		// clean up transition styles
		var cleanStyle = {};
		for ( var prop in style ) {
			cleanStyle[ prop ] = '';
		}
		this.css( cleanStyle );
	};

	var cleanTransitionStyle = {
		transitionProperty: '',
		transitionDuration: ''
	};

	Item.prototype.removeTransitionStyles = function() {
		// remove transition
		this.css( cleanTransitionStyle );
	};

	// ----- show/hide/remove ----- //

	// remove element from DOM
	Item.prototype.removeElem = function() {
		this.element.parentNode.removeChild( this.element );
		// remove display: none
		this.css({ display: '' });
		this.emitEvent( 'remove', [ this ] );
	};

	Item.prototype.remove = function() {
		// just remove element if no transition support or no transition
		if ( !transitionProperty || !parseFloat( this.layout.options.transitionDuration ) ) {
			this.removeElem();
			return;
		}

		// start transition
		var _this = this;
		this.once( 'transitionEnd', function() {
			_this.removeElem();
		});
		this.hide();
	};

	Item.prototype.reveal = function() {
		delete this.isHidden;
		// remove display: none
		this.css({ display: '' });

		var options = this.layout.options;

		var onTransitionEnd = {};
		var transitionEndProperty = this.getHideRevealTransitionEndProperty('visibleStyle');
		onTransitionEnd[ transitionEndProperty ] = this.onRevealTransitionEnd;

		this.transition({
			from: options.hiddenStyle,
			to: options.visibleStyle,
			isCleaning: true,
			onTransitionEnd: onTransitionEnd
		});
	};

	Item.prototype.onRevealTransitionEnd = function() {
		// check if still visible
		// during transition, item may have been hidden
		if ( !this.isHidden ) {
			this.emitEvent('reveal');
		}
	};

	/**
	 * get style property use for hide/reveal transition end
	 * @param {String} styleProperty - hiddenStyle/visibleStyle
	 * @returns {String}
	 */
	Item.prototype.getHideRevealTransitionEndProperty = function( styleProperty ) {
		var optionStyle = this.layout.options[ styleProperty ];
		// use opacity
		if ( optionStyle.opacity ) {
			return 'opacity';
		}
		// get first property
		for ( var prop in optionStyle ) {
			return prop;
		}
	};

	Item.prototype.hide = function() {
		// set flag
		this.isHidden = true;
		// remove display: none
		this.css({ display: '' });

		var options = this.layout.options;

		var onTransitionEnd = {};
		var transitionEndProperty = this.getHideRevealTransitionEndProperty('hiddenStyle');
		onTransitionEnd[ transitionEndProperty ] = this.onHideTransitionEnd;

		this.transition({
			from: options.visibleStyle,
			to: options.hiddenStyle,
			// keep hidden stuff hidden
			isCleaning: true,
			onTransitionEnd: onTransitionEnd
		});
	};

	Item.prototype.onHideTransitionEnd = function() {
		// check if still hidden
		// during transition, item may have been un-hidden
		if ( this.isHidden ) {
			this.css({ display: 'none' });
			this.emitEvent('hide');
		}
	};

	Item.prototype.destroy = function() {
		this.css({
			position: '',
			left: '',
			right: '',
			top: '',
			bottom: '',
			transition: '',
			transform: ''
		});
	};

	return Item;

}));

/*!
 * Outlayer v1.4.2
 * the brains and guts of a layout library
 * MIT license
 */

( function( window, factory ) {
	'use strict';
	// universal module definition

	if ( typeof define == 'function' && define.amd ) {
		// AMD
		define( 'outlayer/outlayer',[
				'eventie/eventie',
				'eventEmitter/EventEmitter',
				'get-size/get-size',
				'fizzy-ui-utils/utils',
				'./item'
			],
			function( eventie, EventEmitter, getSize, utils, Item ) {
				return factory( window, eventie, EventEmitter, getSize, utils, Item);
			}
		);
	} else if ( typeof exports == 'object' ) {
		// CommonJS
		module.exports = factory(
			window,
			require('eventie'),
			require('wolfy87-eventemitter'),
			require('get-size'),
			require('fizzy-ui-utils'),
			require('./item')
		);
	} else {
		// browser global
		window.Outlayer = factory(
			window,
			window.eventie,
			window.EventEmitter,
			window.getSize,
			window.fizzyUIUtils,
			window.Outlayer.Item
		);
	}

}( window, function factory( window, eventie, EventEmitter, getSize, utils, Item ) {
	'use strict';

	// ----- vars ----- //

	var console = window.console;
	var jQuery = window.jQuery;
	var noop = function() {};

	// -------------------------- Outlayer -------------------------- //

	// globally unique identifiers
	var GUID = 0;
	// internal store of all Outlayer intances
	var instances = {};


	/**
	 * @param {Element, String} element
	 * @param {Object} options
	 * @constructor
	 */
	function Outlayer( element, options ) {
		var queryElement = utils.getQueryElement( element );
		if ( !queryElement ) {
			if ( console ) {
				console.error( 'Bad element for ' + this.constructor.namespace +
					': ' + ( queryElement || element ) );
			}
			return;
		}
		this.element = queryElement;
		// add jQuery
		if ( jQuery ) {
			this.$element = jQuery( this.element );
		}

		// options
		this.options = utils.extend( {}, this.constructor.defaults );
		this.option( options );

		// add id for Outlayer.getFromElement
		var id = ++GUID;
		this.element.outlayerGUID = id; // expando
		instances[ id ] = this; // associate via id

		// kick it off
		this._create();

		if ( this.options.isInitLayout ) {
			this.layout();
		}
	}

	// settings are for internal use only
	Outlayer.namespace = 'outlayer';
	Outlayer.Item = Item;

	// default options
	Outlayer.defaults = {
		containerStyle: {
			position: 'relative'
		},
		isInitLayout: true,
		isOriginLeft: true,
		isOriginTop: true,
		isResizeBound: true,
		isResizingContainer: true,
		// item options
		transitionDuration: '0.4s',
		hiddenStyle: {
			opacity: 0,
			transform: 'scale(0.001)'
		},
		visibleStyle: {
			opacity: 1,
			transform: 'scale(1)'
		}
	};

	// inherit EventEmitter
	utils.extend( Outlayer.prototype, EventEmitter.prototype );

	/**
	 * set options
	 * @param {Object} opts
	 */
	Outlayer.prototype.option = function( opts ) {
		utils.extend( this.options, opts );
	};

	Outlayer.prototype._create = function() {
		// get items from children
		this.reloadItems();
		// elements that affect layout, but are not laid out
		this.stamps = [];
		this.stamp( this.options.stamp );
		// set container style
		utils.extend( this.element.style, this.options.containerStyle );

		// bind resize method
		if ( this.options.isResizeBound ) {
			this.bindResize();
		}
	};

	// goes through all children again and gets bricks in proper order
	Outlayer.prototype.reloadItems = function() {
		// collection of item elements
		this.items = this._itemize( this.element.children );
	};


	/**
	 * turn elements into Outlayer.Items to be used in layout
	 * @param {Array or NodeList or HTMLElement} elems
	 * @returns {Array} items - collection of new Outlayer Items
	 */
	Outlayer.prototype._itemize = function( elems ) {

		var itemElems = this._filterFindItemElements( elems );
		var Item = this.constructor.Item;

		// create new Outlayer Items for collection
		var items = [];
		for ( var i=0, len = itemElems.length; i < len; i++ ) {
			var elem = itemElems[i];
			var item = new Item( elem, this );
			items.push( item );
		}

		return items;
	};

	/**
	 * get item elements to be used in layout
	 * @param {Array or NodeList or HTMLElement} elems
	 * @returns {Array} items - item elements
	 */
	Outlayer.prototype._filterFindItemElements = function( elems ) {
		return utils.filterFindElements( elems, this.options.itemSelector );
	};

	/**
	 * getter method for getting item elements
	 * @returns {Array} elems - collection of item elements
	 */
	Outlayer.prototype.getItemElements = function() {
		var elems = [];
		for ( var i=0, len = this.items.length; i < len; i++ ) {
			elems.push( this.items[i].element );
		}
		return elems;
	};

	// ----- init & layout ----- //

	/**
	 * lays out all items
	 */
	Outlayer.prototype.layout = function() {
		this._resetLayout();
		this._manageStamps();

		// don't animate first layout
		var isInstant = this.options.isLayoutInstant !== undefined ?
			this.options.isLayoutInstant : !this._isLayoutInited;
		this.layoutItems( this.items, isInstant );

		// flag for initalized
		this._isLayoutInited = true;
	};

	// _init is alias for layout
	Outlayer.prototype._init = Outlayer.prototype.layout;

	/**
	 * logic before any new layout
	 */
	Outlayer.prototype._resetLayout = function() {
		this.getSize();
	};


	Outlayer.prototype.getSize = function() {
		this.size = getSize( this.element );
	};

	/**
	 * get measurement from option, for columnWidth, rowHeight, gutter
	 * if option is String -> get element from selector string, & get size of element
	 * if option is Element -> get size of element
	 * else use option as a number
	 *
	 * @param {String} measurement
	 * @param {String} size - width or height
	 * @private
	 */
	Outlayer.prototype._getMeasurement = function( measurement, size ) {
		var option = this.options[ measurement ];
		var elem;
		if ( !option ) {
			// default to 0
			this[ measurement ] = 0;
		} else {
			// use option as an element
			if ( typeof option === 'string' ) {
				elem = this.element.querySelector( option );
			} else if ( utils.isElement( option ) ) {
				elem = option;
			}
			// use size of element, if element
			this[ measurement ] = elem ? getSize( elem )[ size ] : option;
		}
	};

	/**
	 * layout a collection of item elements
	 * @api public
	 */
	Outlayer.prototype.layoutItems = function( items, isInstant ) {
		items = this._getItemsForLayout( items );

		this._layoutItems( items, isInstant );

		this._postLayout();
	};

	/**
	 * get the items to be laid out
	 * you may want to skip over some items
	 * @param {Array} items
	 * @returns {Array} items
	 */
	Outlayer.prototype._getItemsForLayout = function( items ) {
		var layoutItems = [];
		for ( var i=0, len = items.length; i < len; i++ ) {
			var item = items[i];
			if ( !item.isIgnored ) {
				layoutItems.push( item );
			}
		}
		return layoutItems;
	};

	/**
	 * layout items
	 * @param {Array} items
	 * @param {Boolean} isInstant
	 */
	Outlayer.prototype._layoutItems = function( items, isInstant ) {
		this._emitCompleteOnItems( 'layout', items );

		if ( !items || !items.length ) {
			// no items, emit event with empty array
			return;
		}

		var queue = [];

		for ( var i=0, len = items.length; i < len; i++ ) {
			var item = items[i];
			// get x/y object from method
			var position = this._getItemLayoutPosition( item );
			// enqueue
			position.item = item;
			position.isInstant = isInstant || item.isLayoutInstant;
			queue.push( position );
		}

		this._processLayoutQueue( queue );
	};

	/**
	 * get item layout position
	 * @param {Outlayer.Item} item
	 * @returns {Object} x and y position
	 */
	Outlayer.prototype._getItemLayoutPosition = function( /* item */ ) {
		return {
			x: 0,
			y: 0
		};
	};

	/**
	 * iterate over array and position each item
	 * Reason being - separating this logic prevents 'layout invalidation'
	 * thx @paul_irish
	 * @param {Array} queue
	 */
	Outlayer.prototype._processLayoutQueue = function( queue ) {
		for ( var i=0, len = queue.length; i < len; i++ ) {
			var obj = queue[i];
			this._positionItem( obj.item, obj.x, obj.y, obj.isInstant );
		}
	};

	/**
	 * Sets position of item in DOM
	 * @param {Outlayer.Item} item
	 * @param {Number} x - horizontal position
	 * @param {Number} y - vertical position
	 * @param {Boolean} isInstant - disables transitions
	 */
	Outlayer.prototype._positionItem = function( item, x, y, isInstant ) {
		if ( isInstant ) {
			// if not transition, just set CSS
			item.goTo( x, y );
		} else {
			item.moveTo( x, y );
		}
	};

	/**
	 * Any logic you want to do after each layout,
	 * i.e. size the container
	 */
	Outlayer.prototype._postLayout = function() {
		this.resizeContainer();
	};

	Outlayer.prototype.resizeContainer = function() {
		if ( !this.options.isResizingContainer ) {
			return;
		}
		var size = this._getContainerSize();
		if ( size ) {
			this._setContainerMeasure( size.width, true );
			this._setContainerMeasure( size.height, false );
		}
	};

	/**
	 * Sets width or height of container if returned
	 * @returns {Object} size
	 *   @param {Number} width
	 *   @param {Number} height
	 */
	Outlayer.prototype._getContainerSize = noop;

	/**
	 * @param {Number} measure - size of width or height
	 * @param {Boolean} isWidth
	 */
	Outlayer.prototype._setContainerMeasure = function( measure, isWidth ) {
		if ( measure === undefined ) {
			return;
		}

		var elemSize = this.size;
		// add padding and border width if border box
		if ( elemSize.isBorderBox ) {
			measure += isWidth ? elemSize.paddingLeft + elemSize.paddingRight +
				elemSize.borderLeftWidth + elemSize.borderRightWidth :
				elemSize.paddingBottom + elemSize.paddingTop +
				elemSize.borderTopWidth + elemSize.borderBottomWidth;
		}

		measure = Math.max( measure, 0 );
		this.element.style[ isWidth ? 'width' : 'height' ] = measure + 'px';
	};

	/**
	 * emit eventComplete on a collection of items events
	 * @param {String} eventName
	 * @param {Array} items - Outlayer.Items
	 */
	Outlayer.prototype._emitCompleteOnItems = function( eventName, items ) {
		var _this = this;
		function onComplete() {
			_this.dispatchEvent( eventName + 'Complete', null, [ items ] );
		}

		var count = items.length;
		if ( !items || !count ) {
			onComplete();
			return;
		}

		var doneCount = 0;
		function tick() {
			doneCount++;
			if ( doneCount === count ) {
				onComplete();
			}
		}

		// bind callback
		for ( var i=0, len = items.length; i < len; i++ ) {
			var item = items[i];
			item.once( eventName, tick );
		}
	};

	/**
	 * emits events via eventEmitter and jQuery events
	 * @param {String} type - name of event
	 * @param {Event} event - original event
	 * @param {Array} args - extra arguments
	 */
	Outlayer.prototype.dispatchEvent = function( type, event, args ) {
		// add original event to arguments
		var emitArgs = event ? [ event ].concat( args ) : args;
		this.emitEvent( type, emitArgs );

		if ( jQuery ) {
			// set this.$element
			this.$element = this.$element || jQuery( this.element );
			if ( event ) {
				// create jQuery event
				var $event = jQuery.Event( event );
				$event.type = type;
				this.$element.trigger( $event, args );
			} else {
				// just trigger with type if no event available
				this.$element.trigger( type, args );
			}
		}
	};

	// -------------------------- ignore & stamps -------------------------- //


	/**
	 * keep item in collection, but do not lay it out
	 * ignored items do not get skipped in layout
	 * @param {Element} elem
	 */
	Outlayer.prototype.ignore = function( elem ) {
		var item = this.getItem( elem );
		if ( item ) {
			item.isIgnored = true;
		}
	};

	/**
	 * return item to layout collection
	 * @param {Element} elem
	 */
	Outlayer.prototype.unignore = function( elem ) {
		var item = this.getItem( elem );
		if ( item ) {
			delete item.isIgnored;
		}
	};

	/**
	 * adds elements to stamps
	 * @param {NodeList, Array, Element, or String} elems
	 */
	Outlayer.prototype.stamp = function( elems ) {
		elems = this._find( elems );
		if ( !elems ) {
			return;
		}

		this.stamps = this.stamps.concat( elems );
		// ignore
		for ( var i=0, len = elems.length; i < len; i++ ) {
			var elem = elems[i];
			this.ignore( elem );
		}
	};

	/**
	 * removes elements to stamps
	 * @param {NodeList, Array, or Element} elems
	 */
	Outlayer.prototype.unstamp = function( elems ) {
		elems = this._find( elems );
		if ( !elems ){
			return;
		}

		for ( var i=0, len = elems.length; i < len; i++ ) {
			var elem = elems[i];
			// filter out removed stamp elements
			utils.removeFrom( this.stamps, elem );
			this.unignore( elem );
		}

	};

	/**
	 * finds child elements
	 * @param {NodeList, Array, Element, or String} elems
	 * @returns {Array} elems
	 */
	Outlayer.prototype._find = function( elems ) {
		if ( !elems ) {
			return;
		}
		// if string, use argument as selector string
		if ( typeof elems === 'string' ) {
			elems = this.element.querySelectorAll( elems );
		}
		elems = utils.makeArray( elems );
		return elems;
	};

	Outlayer.prototype._manageStamps = function() {
		if ( !this.stamps || !this.stamps.length ) {
			return;
		}

		this._getBoundingRect();

		for ( var i=0, len = this.stamps.length; i < len; i++ ) {
			var stamp = this.stamps[i];
			this._manageStamp( stamp );
		}
	};

	// update boundingLeft / Top
	Outlayer.prototype._getBoundingRect = function() {
		// get bounding rect for container element
		var boundingRect = this.element.getBoundingClientRect();
		var size = this.size;
		this._boundingRect = {
			left: boundingRect.left + size.paddingLeft + size.borderLeftWidth,
			top: boundingRect.top + size.paddingTop + size.borderTopWidth,
			right: boundingRect.right - ( size.paddingRight + size.borderRightWidth ),
			bottom: boundingRect.bottom - ( size.paddingBottom + size.borderBottomWidth )
		};
	};

	/**
	 * @param {Element} stamp
	 **/
	Outlayer.prototype._manageStamp = noop;

	/**
	 * get x/y position of element relative to container element
	 * @param {Element} elem
	 * @returns {Object} offset - has left, top, right, bottom
	 */
	Outlayer.prototype._getElementOffset = function( elem ) {
		var boundingRect = elem.getBoundingClientRect();
		var thisRect = this._boundingRect;
		var size = getSize( elem );
		var offset = {
			left: boundingRect.left - thisRect.left - size.marginLeft,
			top: boundingRect.top - thisRect.top - size.marginTop,
			right: thisRect.right - boundingRect.right - size.marginRight,
			bottom: thisRect.bottom - boundingRect.bottom - size.marginBottom
		};
		return offset;
	};

	// -------------------------- resize -------------------------- //

	// enable event handlers for listeners
	// i.e. resize -> onresize
	Outlayer.prototype.handleEvent = function( event ) {
		var method = 'on' + event.type;
		if ( this[ method ] ) {
			this[ method ]( event );
		}
	};

	/**
	 * Bind layout to window resizing
	 */
	Outlayer.prototype.bindResize = function() {
		// bind just one listener
		if ( this.isResizeBound ) {
			return;
		}
		eventie.bind( window, 'resize', this );
		this.isResizeBound = true;
	};

	/**
	 * Unbind layout to window resizing
	 */
	Outlayer.prototype.unbindResize = function() {
		if ( this.isResizeBound ) {
			eventie.unbind( window, 'resize', this );
		}
		this.isResizeBound = false;
	};

	// original debounce by John Hann
	// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/

	// this fires every resize
	Outlayer.prototype.onresize = function() {
		if ( this.resizeTimeout ) {
			clearTimeout( this.resizeTimeout );
		}

		var _this = this;
		function delayed() {
			_this.resize();
			delete _this.resizeTimeout;
		}

		this.resizeTimeout = setTimeout( delayed, 100 );
	};

	// debounced, layout on resize
	Outlayer.prototype.resize = function() {
		// don't trigger if size did not change
		// or if resize was unbound. See #9
		if ( !this.isResizeBound || !this.needsResizeLayout() ) {
			return;
		}

		this.layout();
	};

	/**
	 * check if layout is needed post layout
	 * @returns Boolean
	 */
	Outlayer.prototype.needsResizeLayout = function() {
		var size = getSize( this.element );
		// check that this.size and size are there
		// IE8 triggers resize on body size change, so they might not be
		var hasSizes = this.size && size;
		return hasSizes && size.innerWidth !== this.size.innerWidth;
	};

	// -------------------------- methods -------------------------- //

	/**
	 * add items to Outlayer instance
	 * @param {Array or NodeList or Element} elems
	 * @returns {Array} items - Outlayer.Items
	 **/
	Outlayer.prototype.addItems = function( elems ) {
		var items = this._itemize( elems );
		// add items to collection
		if ( items.length ) {
			this.items = this.items.concat( items );
		}
		return items;
	};

	/**
	 * Layout newly-appended item elements
	 * @param {Array or NodeList or Element} elems
	 */
	Outlayer.prototype.appended = function( elems ) {
		var items = this.addItems( elems );
		if ( !items.length ) {
			return;
		}
		// layout and reveal just the new items
		this.layoutItems( items, true );
		this.reveal( items );
	};

	/**
	 * Layout prepended elements
	 * @param {Array or NodeList or Element} elems
	 */
	Outlayer.prototype.prepended = function( elems ) {
		var items = this._itemize( elems );
		if ( !items.length ) {
			return;
		}
		// add items to beginning of collection
		var previousItems = this.items.slice(0);
		this.items = items.concat( previousItems );
		// start new layout
		this._resetLayout();
		this._manageStamps();
		// layout new stuff without transition
		this.layoutItems( items, true );
		this.reveal( items );
		// layout previous items
		this.layoutItems( previousItems );
	};

	/**
	 * reveal a collection of items
	 * @param {Array of Outlayer.Items} items
	 */
	Outlayer.prototype.reveal = function( items ) {
		this._emitCompleteOnItems( 'reveal', items );

		var len = items && items.length;
		for ( var i=0; len && i < len; i++ ) {
			var item = items[i];
			item.reveal();
		}
	};

	/**
	 * hide a collection of items
	 * @param {Array of Outlayer.Items} items
	 */
	Outlayer.prototype.hide = function( items ) {
		this._emitCompleteOnItems( 'hide', items );

		var len = items && items.length;
		for ( var i=0; len && i < len; i++ ) {
			var item = items[i];
			item.hide();
		}
	};

	/**
	 * reveal item elements
	 * @param {Array}, {Element}, {NodeList} items
	 */
	Outlayer.prototype.revealItemElements = function( elems ) {
		var items = this.getItems( elems );
		this.reveal( items );
	};

	/**
	 * hide item elements
	 * @param {Array}, {Element}, {NodeList} items
	 */
	Outlayer.prototype.hideItemElements = function( elems ) {
		var items = this.getItems( elems );
		this.hide( items );
	};

	/**
	 * get Outlayer.Item, given an Element
	 * @param {Element} elem
	 * @param {Function} callback
	 * @returns {Outlayer.Item} item
	 */
	Outlayer.prototype.getItem = function( elem ) {
		// loop through items to get the one that matches
		for ( var i=0, len = this.items.length; i < len; i++ ) {
			var item = this.items[i];
			if ( item.element === elem ) {
				// return item
				return item;
			}
		}
	};

	/**
	 * get collection of Outlayer.Items, given Elements
	 * @param {Array} elems
	 * @returns {Array} items - Outlayer.Items
	 */
	Outlayer.prototype.getItems = function( elems ) {
		elems = utils.makeArray( elems );
		var items = [];
		for ( var i=0, len = elems.length; i < len; i++ ) {
			var elem = elems[i];
			var item = this.getItem( elem );
			if ( item ) {
				items.push( item );
			}
		}

		return items;
	};

	/**
	 * remove element(s) from instance and DOM
	 * @param {Array or NodeList or Element} elems
	 */
	Outlayer.prototype.remove = function( elems ) {
		var removeItems = this.getItems( elems );

		this._emitCompleteOnItems( 'remove', removeItems );

		// bail if no items to remove
		if ( !removeItems || !removeItems.length ) {
			return;
		}

		for ( var i=0, len = removeItems.length; i < len; i++ ) {
			var item = removeItems[i];
			item.remove();
			// remove item from collection
			utils.removeFrom( this.items, item );
		}
	};

	// ----- destroy ----- //

	// remove and disable Outlayer instance
	Outlayer.prototype.destroy = function() {
		// clean up dynamic styles
		var style = this.element.style;
		style.height = '';
		style.position = '';
		style.width = '';
		// destroy items
		for ( var i=0, len = this.items.length; i < len; i++ ) {
			var item = this.items[i];
			item.destroy();
		}

		this.unbindResize();

		var id = this.element.outlayerGUID;
		delete instances[ id ]; // remove reference to instance by id
		delete this.element.outlayerGUID;
		// remove data for jQuery
		if ( jQuery ) {
			jQuery.removeData( this.element, this.constructor.namespace );
		}

	};

	// -------------------------- data -------------------------- //

	/**
	 * get Outlayer instance from element
	 * @param {Element} elem
	 * @returns {Outlayer}
	 */
	Outlayer.data = function( elem ) {
		elem = utils.getQueryElement( elem );
		var id = elem && elem.outlayerGUID;
		return id && instances[ id ];
	};


	// -------------------------- create Outlayer class -------------------------- //

	/**
	 * create a layout class
	 * @param {String} namespace
	 */
	Outlayer.create = function( namespace, options ) {
		// sub-class Outlayer
		function Layout() {
			Outlayer.apply( this, arguments );
		}
		// inherit Outlayer prototype, use Object.create if there
		if ( Object.create ) {
			Layout.prototype = Object.create( Outlayer.prototype );
		} else {
			utils.extend( Layout.prototype, Outlayer.prototype );
		}
		// set contructor, used for namespace and Item
		Layout.prototype.constructor = Layout;

		Layout.defaults = utils.extend( {}, Outlayer.defaults );
		// apply new options
		utils.extend( Layout.defaults, options );
		// keep prototype.settings for backwards compatibility (Packery v1.2.0)
		Layout.prototype.settings = {};

		Layout.namespace = namespace;

		Layout.data = Outlayer.data;

		// sub-class Item
		Layout.Item = function LayoutItem() {
			Item.apply( this, arguments );
		};

		Layout.Item.prototype = new Item();

		// -------------------------- declarative -------------------------- //

		utils.htmlInit( Layout, namespace );

		// -------------------------- jQuery bridge -------------------------- //

		// make into jQuery plugin
		if ( jQuery && jQuery.bridget ) {
			jQuery.bridget( namespace, Layout );
		}

		return Layout;
	};

	// ----- fin ----- //

	// back in global
	Outlayer.Item = Item;

	return Outlayer;

}));


/**
 * Isotope Item
 **/

( function( window, factory ) {
	'use strict';
	// universal module definition
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		define( 'isotope/js/item',[
				'outlayer/outlayer'
			],
			factory );
	} else if ( typeof exports == 'object' ) {
		// CommonJS
		module.exports = factory(
			require('outlayer')
		);
	} else {
		// browser global
		window.Isotope = window.Isotope || {};
		window.Isotope.Item = factory(
			window.Outlayer
		);
	}

}( window, function factory( Outlayer ) {
	'use strict';

	// -------------------------- Item -------------------------- //

	// sub-class Outlayer Item
	function Item() {
		Outlayer.Item.apply( this, arguments );
	}

	Item.prototype = new Outlayer.Item();

	Item.prototype._create = function() {
		// assign id, used for original-order sorting
		this.id = this.layout.itemGUID++;
		Outlayer.Item.prototype._create.call( this );
		this.sortData = {};
	};

	Item.prototype.updateSortData = function() {
		if ( this.isIgnored ) {
			return;
		}
		// default sorters
		this.sortData.id = this.id;
		// for backward compatibility
		this.sortData['original-order'] = this.id;
		this.sortData.random = Math.random();
		// go thru getSortData obj and apply the sorters
		var getSortData = this.layout.options.getSortData;
		var sorters = this.layout._sorters;
		for ( var key in getSortData ) {
			var sorter = sorters[ key ];
			this.sortData[ key ] = sorter( this.element, this );
		}
	};

	var _destroy = Item.prototype.destroy;
	Item.prototype.destroy = function() {
		// call super
		_destroy.apply( this, arguments );
		// reset display, #741
		this.css({
			display: ''
		});
	};

	return Item;

}));

/**
 * Isotope LayoutMode
 */

( function( window, factory ) {
	'use strict';
	// universal module definition

	if ( typeof define == 'function' && define.amd ) {
		// AMD
		define( 'isotope/js/layout-mode',[
				'get-size/get-size',
				'outlayer/outlayer'
			],
			factory );
	} else if ( typeof exports == 'object' ) {
		// CommonJS
		module.exports = factory(
			require('get-size'),
			require('outlayer')
		);
	} else {
		// browser global
		window.Isotope = window.Isotope || {};
		window.Isotope.LayoutMode = factory(
			window.getSize,
			window.Outlayer
		);
	}

}( window, function factory( getSize, Outlayer ) {
	'use strict';

	// layout mode class
	function LayoutMode( isotope ) {
		this.isotope = isotope;
		// link properties
		if ( isotope ) {
			this.options = isotope.options[ this.namespace ];
			this.element = isotope.element;
			this.items = isotope.filteredItems;
			this.size = isotope.size;
		}
	}

	/**
	 * some methods should just defer to default Outlayer method
	 * and reference the Isotope instance as `this`
	 **/
	( function() {
		var facadeMethods = [
			'_resetLayout',
			'_getItemLayoutPosition',
			'_manageStamp',
			'_getContainerSize',
			'_getElementOffset',
			'needsResizeLayout'
		];

		for ( var i=0, len = facadeMethods.length; i < len; i++ ) {
			var methodName = facadeMethods[i];
			LayoutMode.prototype[ methodName ] = getOutlayerMethod( methodName );
		}

		function getOutlayerMethod( methodName ) {
			return function() {
				return Outlayer.prototype[ methodName ].apply( this.isotope, arguments );
			};
		}
	})();

	// -----  ----- //

	// for horizontal layout modes, check vertical size
	LayoutMode.prototype.needsVerticalResizeLayout = function() {
		// don't trigger if size did not change
		var size = getSize( this.isotope.element );
		// check that this.size and size are there
		// IE8 triggers resize on body size change, so they might not be
		var hasSizes = this.isotope.size && size;
		return hasSizes && size.innerHeight != this.isotope.size.innerHeight;
	};

	// ----- measurements ----- //

	LayoutMode.prototype._getMeasurement = function() {
		this.isotope._getMeasurement.apply( this, arguments );
	};

	LayoutMode.prototype.getColumnWidth = function() {
		this.getSegmentSize( 'column', 'Width' );
	};

	LayoutMode.prototype.getRowHeight = function() {
		this.getSegmentSize( 'row', 'Height' );
	};

	/**
	 * get columnWidth or rowHeight
	 * segment: 'column' or 'row'
	 * size 'Width' or 'Height'
	 **/
	LayoutMode.prototype.getSegmentSize = function( segment, size ) {
		var segmentName = segment + size;
		var outerSize = 'outer' + size;
		// columnWidth / outerWidth // rowHeight / outerHeight
		this._getMeasurement( segmentName, outerSize );
		// got rowHeight or columnWidth, we can chill
		if ( this[ segmentName ] ) {
			return;
		}
		// fall back to item of first element
		var firstItemSize = this.getFirstItemSize();
		this[ segmentName ] = firstItemSize && firstItemSize[ outerSize ] ||
			// or size of container
			this.isotope.size[ 'inner' + size ];
	};

	LayoutMode.prototype.getFirstItemSize = function() {
		var firstItem = this.isotope.filteredItems[0];
		return firstItem && firstItem.element && getSize( firstItem.element );
	};

	// ----- methods that should reference isotope ----- //

	LayoutMode.prototype.layout = function() {
		this.isotope.layout.apply( this.isotope, arguments );
	};

	LayoutMode.prototype.getSize = function() {
		this.isotope.getSize();
		this.size = this.isotope.size;
	};

	// -------------------------- create -------------------------- //

	LayoutMode.modes = {};

	LayoutMode.create = function( namespace, options ) {

		function Mode() {
			LayoutMode.apply( this, arguments );
		}

		Mode.prototype = new LayoutMode();

		// default options
		if ( options ) {
			Mode.options = options;
		}

		Mode.prototype.namespace = namespace;
		// register in Isotope
		LayoutMode.modes[ namespace ] = Mode;

		return Mode;
	};

	return LayoutMode;

}));

/*!
 * Masonry v3.3.1
 * Cascading grid layout library
 * http://masonry.desandro.com
 * MIT License
 * by David DeSandro
 */

( function( window, factory ) {
	'use strict';
	// universal module definition
	if ( typeof define === 'function' && define.amd ) {
		// AMD
		define( 'masonry/masonry',[
				'outlayer/outlayer',
				'get-size/get-size',
				'fizzy-ui-utils/utils'
			],
			factory );
	} else if ( typeof exports === 'object' ) {
		// CommonJS
		module.exports = factory(
			require('outlayer'),
			require('get-size'),
			require('fizzy-ui-utils')
		);
	} else {
		// browser global
		window.Masonry = factory(
			window.Outlayer,
			window.getSize,
			window.fizzyUIUtils
		);
	}

}( window, function factory( Outlayer, getSize, utils ) {



	// -------------------------- masonryDefinition -------------------------- //

	// create an Outlayer layout class
	var Masonry = Outlayer.create('masonry');

	Masonry.prototype._resetLayout = function() {
		this.getSize();
		this._getMeasurement( 'columnWidth', 'outerWidth' );
		this._getMeasurement( 'gutter', 'outerWidth' );
		this.measureColumns();

		// reset column Y
		var i = this.cols;
		this.colYs = [];
		while (i--) {
			this.colYs.push( 0 );
		}

		this.maxY = 0;
	};

	Masonry.prototype.measureColumns = function() {
		this.getContainerWidth();
		// if columnWidth is 0, default to outerWidth of first item
		if ( !this.columnWidth ) {
			var firstItem = this.items[0];
			var firstItemElem = firstItem && firstItem.element;
			// columnWidth fall back to item of first element
			this.columnWidth = firstItemElem && getSize( firstItemElem ).outerWidth ||
				// if first elem has no width, default to size of container
				this.containerWidth;
		}

		var columnWidth = this.columnWidth += this.gutter;

		// calculate columns
		var containerWidth = this.containerWidth + this.gutter;
		var cols = containerWidth / columnWidth;
		// fix rounding errors, typically with gutters
		var excess = columnWidth - containerWidth % columnWidth;
		// if overshoot is less than a pixel, round up, otherwise floor it
		var mathMethod = excess && excess < 1 ? 'round' : 'floor';
		cols = Math[ mathMethod ]( cols );
		this.cols = Math.max( cols, 1 );
	};

	Masonry.prototype.getContainerWidth = function() {
		// container is parent if fit width
		var container = this.options.isFitWidth ? this.element.parentNode : this.element;
		// check that this.size and size are there
		// IE8 triggers resize on body size change, so they might not be
		var size = getSize( container );
		this.containerWidth = size && size.innerWidth;
	};

	Masonry.prototype._getItemLayoutPosition = function( item ) {
		item.getSize();
		// how many columns does this brick span
		var remainder = item.size.outerWidth % this.columnWidth;
		var mathMethod = remainder && remainder < 1 ? 'round' : 'ceil';
		// round if off by 1 pixel, otherwise use ceil
		var colSpan = Math[ mathMethod ]( item.size.outerWidth / this.columnWidth );
		colSpan = Math.min( colSpan, this.cols );

		var colGroup = this._getColGroup( colSpan );
		// get the minimum Y value from the columns
		var minimumY = Math.min.apply( Math, colGroup );
		var shortColIndex = utils.indexOf( colGroup, minimumY );

		// position the brick
		var position = {
			x: this.columnWidth * shortColIndex,
			y: minimumY
		};

		// apply setHeight to necessary columns
		var setHeight = minimumY + item.size.outerHeight;
		var setSpan = this.cols + 1 - colGroup.length;
		for ( var i = 0; i < setSpan; i++ ) {
			this.colYs[ shortColIndex + i ] = setHeight;
		}

		return position;
	};

	/**
	 * @param {Number} colSpan - number of columns the element spans
	 * @returns {Array} colGroup
	 */
	Masonry.prototype._getColGroup = function( colSpan ) {
		if ( colSpan < 2 ) {
			// if brick spans only one column, use all the column Ys
			return this.colYs;
		}

		var colGroup = [];
		// how many different places could this brick fit horizontally
		var groupCount = this.cols + 1 - colSpan;
		// for each group potential horizontal position
		for ( var i = 0; i < groupCount; i++ ) {
			// make an array of colY values for that one group
			var groupColYs = this.colYs.slice( i, i + colSpan );
			// and get the max value of the array
			colGroup[i] = Math.max.apply( Math, groupColYs );
		}
		return colGroup;
	};

	Masonry.prototype._manageStamp = function( stamp ) {
		var stampSize = getSize( stamp );
		var offset = this._getElementOffset( stamp );
		// get the columns that this stamp affects
		var firstX = this.options.isOriginLeft ? offset.left : offset.right;
		var lastX = firstX + stampSize.outerWidth;
		var firstCol = Math.floor( firstX / this.columnWidth );
		firstCol = Math.max( 0, firstCol );
		var lastCol = Math.floor( lastX / this.columnWidth );
		// lastCol should not go over if multiple of columnWidth #425
		lastCol -= lastX % this.columnWidth ? 0 : 1;
		lastCol = Math.min( this.cols - 1, lastCol );
		// set colYs to bottom of the stamp
		var stampMaxY = ( this.options.isOriginTop ? offset.top : offset.bottom ) +
			stampSize.outerHeight;
		for ( var i = firstCol; i <= lastCol; i++ ) {
			this.colYs[i] = Math.max( stampMaxY, this.colYs[i] );
		}
	};

	Masonry.prototype._getContainerSize = function() {
		this.maxY = Math.max.apply( Math, this.colYs );
		var size = {
			height: this.maxY
		};

		if ( this.options.isFitWidth ) {
			size.width = this._getContainerFitWidth();
		}

		return size;
	};

	Masonry.prototype._getContainerFitWidth = function() {
		var unusedCols = 0;
		// count unused columns
		var i = this.cols;
		while ( --i ) {
			if ( this.colYs[i] !== 0 ) {
				break;
			}
			unusedCols++;
		}
		// fit container to columns that have been used
		return ( this.cols - unusedCols ) * this.columnWidth - this.gutter;
	};

	Masonry.prototype.needsResizeLayout = function() {
		var previousWidth = this.containerWidth;
		this.getContainerWidth();
		return previousWidth !== this.containerWidth;
	};

	return Masonry;

}));

/*!
 * Masonry layout mode
 * sub-classes Masonry
 * http://masonry.desandro.com
 */

( function( window, factory ) {
	'use strict';
	// universal module definition
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		define( 'isotope/js/layout-modes/masonry',[
				'../layout-mode',
				'masonry/masonry'
			],
			factory );
	} else if ( typeof exports == 'object' ) {
		// CommonJS
		module.exports = factory(
			require('../layout-mode'),
			require('masonry-layout')
		);
	} else {
		// browser global
		factory(
			window.Isotope.LayoutMode,
			window.Masonry
		);
	}

}( window, function factory( LayoutMode, Masonry ) {
	'use strict';

	// -------------------------- helpers -------------------------- //

	// extend objects
	function extend( a, b ) {
		for ( var prop in b ) {
			a[ prop ] = b[ prop ];
		}
		return a;
	}

	// -------------------------- masonryDefinition -------------------------- //

	// create an Outlayer layout class
	var MasonryMode = LayoutMode.create('masonry');

	// save on to these methods
	var _getElementOffset = MasonryMode.prototype._getElementOffset;
	var layout = MasonryMode.prototype.layout;
	var _getMeasurement = MasonryMode.prototype._getMeasurement;

	// sub-class Masonry
	extend( MasonryMode.prototype, Masonry.prototype );

	// set back, as it was overwritten by Masonry
	MasonryMode.prototype._getElementOffset = _getElementOffset;
	MasonryMode.prototype.layout = layout;
	MasonryMode.prototype._getMeasurement = _getMeasurement;

	var measureColumns = MasonryMode.prototype.measureColumns;
	MasonryMode.prototype.measureColumns = function() {
		// set items, used if measuring first item
		this.items = this.isotope.filteredItems;
		measureColumns.call( this );
	};

	// HACK copy over isOriginLeft/Top options
	var _manageStamp = MasonryMode.prototype._manageStamp;
	MasonryMode.prototype._manageStamp = function() {
		this.options.isOriginLeft = this.isotope.options.isOriginLeft;
		this.options.isOriginTop = this.isotope.options.isOriginTop;
		_manageStamp.apply( this, arguments );
	};

	return MasonryMode;

}));

/**
 * fitRows layout mode
 */

( function( window, factory ) {
	'use strict';
	// universal module definition
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		define( 'isotope/js/layout-modes/fit-rows',[
				'../layout-mode'
			],
			factory );
	} else if ( typeof exports == 'object' ) {
		// CommonJS
		module.exports = factory(
			require('../layout-mode')
		);
	} else {
		// browser global
		factory(
			window.Isotope.LayoutMode
		);
	}

}( window, function factory( LayoutMode ) {
	'use strict';

	var FitRows = LayoutMode.create('fitRows');

	FitRows.prototype._resetLayout = function() {
		this.x = 0;
		this.y = 0;
		this.maxY = 0;
		this._getMeasurement( 'gutter', 'outerWidth' );
	};

	FitRows.prototype._getItemLayoutPosition = function( item ) {
		item.getSize();

		var itemWidth = item.size.outerWidth + this.gutter;
		// if this element cannot fit in the current row
		var containerWidth = this.isotope.size.innerWidth + this.gutter;
		if ( this.x !== 0 && itemWidth + this.x > containerWidth ) {
			this.x = 0;
			this.y = this.maxY;
		}

		var position = {
			x: this.x,
			y: this.y
		};

		this.maxY = Math.max( this.maxY, this.y + item.size.outerHeight );
		this.x += itemWidth;

		return position;
	};

	FitRows.prototype._getContainerSize = function() {
		return { height: this.maxY };
	};

	return FitRows;

}));

/**
 * vertical layout mode
 */

( function( window, factory ) {
	'use strict';
	// universal module definition
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		define( 'isotope/js/layout-modes/vertical',[
				'../layout-mode'
			],
			factory );
	} else if ( typeof exports == 'object' ) {
		// CommonJS
		module.exports = factory(
			require('../layout-mode')
		);
	} else {
		// browser global
		factory(
			window.Isotope.LayoutMode
		);
	}

}( window, function factory( LayoutMode ) {
	'use strict';

	var Vertical = LayoutMode.create( 'vertical', {
		horizontalAlignment: 0
	});

	Vertical.prototype._resetLayout = function() {
		this.y = 0;
	};

	Vertical.prototype._getItemLayoutPosition = function( item ) {
		item.getSize();
		var x = ( this.isotope.size.innerWidth - item.size.outerWidth ) *
			this.options.horizontalAlignment;
		var y = this.y;
		this.y += item.size.outerHeight;
		return { x: x, y: y };
	};

	Vertical.prototype._getContainerSize = function() {
		return { height: this.y };
	};

	return Vertical;

}));

/*!
 * Isotope v2.2.2
 *
 * Licensed GPLv3 for open source use
 * or Isotope Commercial License for commercial use
 *
 * http://isotope.metafizzy.co
 * Copyright 2015 Metafizzy
 */

( function( window, factory ) {
	'use strict';
	// universal module definition

	if ( typeof define == 'function' && define.amd ) {
		// AMD
		define( [
				'outlayer/outlayer',
				'get-size/get-size',
				'matches-selector/matches-selector',
				'fizzy-ui-utils/utils',
				'isotope/js/item',
				'isotope/js/layout-mode',
				// include default layout modes
				'isotope/js/layout-modes/masonry',
				'isotope/js/layout-modes/fit-rows',
				'isotope/js/layout-modes/vertical'
			],
			function( Outlayer, getSize, matchesSelector, utils, Item, LayoutMode ) {
				return factory( window, Outlayer, getSize, matchesSelector, utils, Item, LayoutMode );
			});
	} else if ( typeof exports == 'object' ) {
		// CommonJS
		module.exports = factory(
			window,
			require('outlayer'),
			require('get-size'),
			require('desandro-matches-selector'),
			require('fizzy-ui-utils'),
			require('./item'),
			require('./layout-mode'),
			// include default layout modes
			require('./layout-modes/masonry'),
			require('./layout-modes/fit-rows'),
			require('./layout-modes/vertical')
		);
	} else {
		// browser global
		window.Isotope = factory(
			window,
			window.Outlayer,
			window.getSize,
			window.matchesSelector,
			window.fizzyUIUtils,
			window.Isotope.Item,
			window.Isotope.LayoutMode
		);
	}

}( window, function factory( window, Outlayer, getSize, matchesSelector, utils,
	Item, LayoutMode ) {



	// -------------------------- vars -------------------------- //

	var jQuery = window.jQuery;

	// -------------------------- helpers -------------------------- //

	var trim = String.prototype.trim ?
		function( str ) {
			return str.trim();
		} :
		function( str ) {
			return str.replace( /^\s+|\s+$/g, '' );
		};

	var docElem = document.documentElement;

	var getText = docElem.textContent ?
		function( elem ) {
			return elem.textContent;
		} :
		function( elem ) {
			return elem.innerText;
		};

	// -------------------------- isotopeDefinition -------------------------- //

	// create an Outlayer layout class
	var Isotope = Outlayer.create( 'isotope', {
		layoutMode: "masonry",
		isJQueryFiltering: true,
		sortAscending: true
	});

	Isotope.Item = Item;
	Isotope.LayoutMode = LayoutMode;

	Isotope.prototype._create = function() {
		this.itemGUID = 0;
		// functions that sort items
		this._sorters = {};
		this._getSorters();
		// call super
		Outlayer.prototype._create.call( this );

		// create layout modes
		this.modes = {};
		// start filteredItems with all items
		this.filteredItems = this.items;
		// keep of track of sortBys
		this.sortHistory = [ 'original-order' ];
		// create from registered layout modes
		for ( var name in LayoutMode.modes ) {
			this._initLayoutMode( name );
		}
	};

	Isotope.prototype.reloadItems = function() {
		// reset item ID counter
		this.itemGUID = 0;
		// call super
		Outlayer.prototype.reloadItems.call( this );
	};

	Isotope.prototype._itemize = function() {
		var items = Outlayer.prototype._itemize.apply( this, arguments );
		// assign ID for original-order
		for ( var i=0, len = items.length; i < len; i++ ) {
			var item = items[i];
			item.id = this.itemGUID++;
		}
		this._updateItemsSortData( items );
		return items;
	};


	// -------------------------- layout -------------------------- //

	Isotope.prototype._initLayoutMode = function( name ) {
		var Mode = LayoutMode.modes[ name ];
		// set mode options
		// HACK extend initial options, back-fill in default options
		var initialOpts = this.options[ name ] || {};
		this.options[ name ] = Mode.options ?
			utils.extend( Mode.options, initialOpts ) : initialOpts;
		// init layout mode instance
		this.modes[ name ] = new Mode( this );
	};


	Isotope.prototype.layout = function() {
		// if first time doing layout, do all magic
		if ( !this._isLayoutInited && this.options.isInitLayout ) {
			this.arrange();
			return;
		}
		this._layout();
	};

	// private method to be used in layout() & magic()
	Isotope.prototype._layout = function() {
		// don't animate first layout
		var isInstant = this._getIsInstant();
		// layout flow
		this._resetLayout();
		this._manageStamps();
		this.layoutItems( this.filteredItems, isInstant );

		// flag for initalized
		this._isLayoutInited = true;
	};

	// filter + sort + layout
	Isotope.prototype.arrange = function( opts ) {
		// set any options pass
		this.option( opts );
		this._getIsInstant();
		// filter, sort, and layout

		// filter
		var filtered = this._filter( this.items );
		this.filteredItems = filtered.matches;

		var _this = this;
		function hideReveal() {
			_this.reveal( filtered.needReveal );
			_this.hide( filtered.needHide );
		}

		this._bindArrangeComplete();

		if ( this._isInstant ) {
			this._noTransition( hideReveal );
		} else {
			hideReveal();
		}

		this._sort();
		this._layout();
	};
	// alias to _init for main plugin method
	Isotope.prototype._init = Isotope.prototype.arrange;

	// HACK
	// Don't animate/transition first layout
	// Or don't animate/transition other layouts
	Isotope.prototype._getIsInstant = function() {
		var isInstant = this.options.isLayoutInstant !== undefined ?
			this.options.isLayoutInstant : !this._isLayoutInited;
		this._isInstant = isInstant;
		return isInstant;
	};

	// listen for layoutComplete, hideComplete and revealComplete
	// to trigger arrangeComplete
	Isotope.prototype._bindArrangeComplete = function() {
		// listen for 3 events to trigger arrangeComplete
		var isLayoutComplete, isHideComplete, isRevealComplete;
		var _this = this;
		function arrangeParallelCallback() {
			if ( isLayoutComplete && isHideComplete && isRevealComplete ) {
				_this.dispatchEvent( 'arrangeComplete', null, [ _this.filteredItems ] );
			}
		}
		this.once( 'layoutComplete', function() {
			isLayoutComplete = true;
			arrangeParallelCallback();
		});
		this.once( 'hideComplete', function() {
			isHideComplete = true;
			arrangeParallelCallback();
		});
		this.once( 'revealComplete', function() {
			isRevealComplete = true;
			arrangeParallelCallback();
		});
	};

	// -------------------------- filter -------------------------- //

	Isotope.prototype._filter = function( items ) {
		var filter = this.options.filter;
		filter = filter || '*';
		var matches = [];
		var hiddenMatched = [];
		var visibleUnmatched = [];

		var test = this._getFilterTest( filter );

		// test each item
		for ( var i=0, len = items.length; i < len; i++ ) {
			var item = items[i];
			if ( item.isIgnored ) {
				continue;
			}
			// add item to either matched or unmatched group
			var isMatched = test( item );
			// item.isFilterMatched = isMatched;
			// add to matches if its a match
			if ( isMatched ) {
				matches.push( item );
			}
			// add to additional group if item needs to be hidden or revealed
			if ( isMatched && item.isHidden ) {
				hiddenMatched.push( item );
			} else if ( !isMatched && !item.isHidden ) {
				visibleUnmatched.push( item );
			}
		}

		// return collections of items to be manipulated
		return {
			matches: matches,
			needReveal: hiddenMatched,
			needHide: visibleUnmatched
		};
	};

	// get a jQuery, function, or a matchesSelector test given the filter
	Isotope.prototype._getFilterTest = function( filter ) {
		if ( jQuery && this.options.isJQueryFiltering ) {
			// use jQuery
			return function( item ) {
				return jQuery( item.element ).is( filter );
			};
		}
		if ( typeof filter == 'function' ) {
			// use filter as function
			return function( item ) {
				return filter( item.element );
			};
		}
		// default, use filter as selector string
		return function( item ) {
			return matchesSelector( item.element, filter );
		};
	};

	// -------------------------- sorting -------------------------- //

	/**
	 * @params {Array} elems
	 * @public
	 */
	Isotope.prototype.updateSortData = function( elems ) {
		// get items
		var items;
		if ( elems ) {
			elems = utils.makeArray( elems );
			items = this.getItems( elems );
		} else {
			// update all items if no elems provided
			items = this.items;
		}

		this._getSorters();
		this._updateItemsSortData( items );
	};

	Isotope.prototype._getSorters = function() {
		var getSortData = this.options.getSortData;
		for ( var key in getSortData ) {
			var sorter = getSortData[ key ];
			this._sorters[ key ] = mungeSorter( sorter );
		}
	};

	/**
	 * @params {Array} items - of Isotope.Items
	 * @private
	 */
	Isotope.prototype._updateItemsSortData = function( items ) {
		// do not update if no items
		var len = items && items.length;

		for ( var i=0; len && i < len; i++ ) {
			var item = items[i];
			item.updateSortData();
		}
	};

	// ----- munge sorter ----- //

	// encapsulate this, as we just need mungeSorter
	// other functions in here are just for munging
	var mungeSorter = ( function() {
		// add a magic layer to sorters for convienent shorthands
		// `.foo-bar` will use the text of .foo-bar querySelector
		// `[foo-bar]` will use attribute
		// you can also add parser
		// `.foo-bar parseInt` will parse that as a number
		function mungeSorter( sorter ) {
			// if not a string, return function or whatever it is
			if ( typeof sorter != 'string' ) {
				return sorter;
			}
			// parse the sorter string
			var args = trim( sorter ).split(' ');
			var query = args[0];
			// check if query looks like [an-attribute]
			var attrMatch = query.match( /^\[(.+)\]$/ );
			var attr = attrMatch && attrMatch[1];
			var getValue = getValueGetter( attr, query );
			// use second argument as a parser
			var parser = Isotope.sortDataParsers[ args[1] ];
			// parse the value, if there was a parser
			sorter = parser ? function( elem ) {
					return elem && parser( getValue( elem ) );
				} :
				// otherwise just return value
				function( elem ) {
					return elem && getValue( elem );
				};

			return sorter;
		}

		// get an attribute getter, or get text of the querySelector
		function getValueGetter( attr, query ) {
			var getValue;
			// if query looks like [foo-bar], get attribute
			if ( attr ) {
				getValue = function( elem ) {
					return elem.getAttribute( attr );
				};
			} else {
				// otherwise, assume its a querySelector, and get its text
				getValue = function( elem ) {
					var child = elem.querySelector( query );
					return child && getText( child );
				};
			}
			return getValue;
		}

		return mungeSorter;
	})();

	// parsers used in getSortData shortcut strings
	Isotope.sortDataParsers = {
		'parseInt': function( val ) {
			return parseInt( val, 10 );
		},
		'parseFloat': function( val ) {
			return parseFloat( val );
		}
	};

	// ----- sort method ----- //

	// sort filteredItem order
	Isotope.prototype._sort = function() {
		var sortByOpt = this.options.sortBy;
		if ( !sortByOpt ) {
			return;
		}
		// concat all sortBy and sortHistory
		var sortBys = [].concat.apply( sortByOpt, this.sortHistory );
		// sort magic
		var itemSorter = getItemSorter( sortBys, this.options.sortAscending );
		this.filteredItems.sort( itemSorter );
		// keep track of sortBy History
		if ( sortByOpt != this.sortHistory[0] ) {
			// add to front, oldest goes in last
			this.sortHistory.unshift( sortByOpt );
		}
	};

	// returns a function used for sorting
	function getItemSorter( sortBys, sortAsc ) {
		return function sorter( itemA, itemB ) {
			// cycle through all sortKeys
			for ( var i = 0, len = sortBys.length; i < len; i++ ) {
				var sortBy = sortBys[i];
				var a = itemA.sortData[ sortBy ];
				var b = itemB.sortData[ sortBy ];
				if ( a > b || a < b ) {
					// if sortAsc is an object, use the value given the sortBy key
					var isAscending = sortAsc[ sortBy ] !== undefined ? sortAsc[ sortBy ] : sortAsc;
					var direction = isAscending ? 1 : -1;
					return ( a > b ? 1 : -1 ) * direction;
				}
			}
			return 0;
		};
	}

	// -------------------------- methods -------------------------- //

	// get layout mode
	Isotope.prototype._mode = function() {
		var layoutMode = this.options.layoutMode;
		var mode = this.modes[ layoutMode ];
		if ( !mode ) {
			// TODO console.error
			throw new Error( 'No layout mode: ' + layoutMode );
		}
		// HACK sync mode's options
		// any options set after init for layout mode need to be synced
		mode.options = this.options[ layoutMode ];
		return mode;
	};

	Isotope.prototype._resetLayout = function() {
		// trigger original reset layout
		Outlayer.prototype._resetLayout.call( this );
		this._mode()._resetLayout();
	};

	Isotope.prototype._getItemLayoutPosition = function( item  ) {
		return this._mode()._getItemLayoutPosition( item );
	};

	Isotope.prototype._manageStamp = function( stamp ) {
		this._mode()._manageStamp( stamp );
	};

	Isotope.prototype._getContainerSize = function() {
		return this._mode()._getContainerSize();
	};

	Isotope.prototype.needsResizeLayout = function() {
		return this._mode().needsResizeLayout();
	};

	// -------------------------- adding & removing -------------------------- //

	// HEADS UP overwrites default Outlayer appended
	Isotope.prototype.appended = function( elems ) {
		var items = this.addItems( elems );
		if ( !items.length ) {
			return;
		}
		// filter, layout, reveal new items
		var filteredItems = this._filterRevealAdded( items );
		// add to filteredItems
		this.filteredItems = this.filteredItems.concat( filteredItems );
	};

	// HEADS UP overwrites default Outlayer prepended
	Isotope.prototype.prepended = function( elems ) {
		var items = this._itemize( elems );
		if ( !items.length ) {
			return;
		}
		// start new layout
		this._resetLayout();
		this._manageStamps();
		// filter, layout, reveal new items
		var filteredItems = this._filterRevealAdded( items );
		// layout previous items
		this.layoutItems( this.filteredItems );
		// add to items and filteredItems
		this.filteredItems = filteredItems.concat( this.filteredItems );
		this.items = items.concat( this.items );
	};

	Isotope.prototype._filterRevealAdded = function( items ) {
		var filtered = this._filter( items );
		this.hide( filtered.needHide );
		// reveal all new items
		this.reveal( filtered.matches );
		// layout new items, no transition
		this.layoutItems( filtered.matches, true );
		return filtered.matches;
	};

	/**
	 * Filter, sort, and layout newly-appended item elements
	 * @param {Array or NodeList or Element} elems
	 */
	Isotope.prototype.insert = function( elems ) {
		var items = this.addItems( elems );
		if ( !items.length ) {
			return;
		}
		// append item elements
		var i, item;
		var len = items.length;
		for ( i=0; i < len; i++ ) {
			item = items[i];
			this.element.appendChild( item.element );
		}
		// filter new stuff
		var filteredInsertItems = this._filter( items ).matches;
		// set flag
		for ( i=0; i < len; i++ ) {
			items[i].isLayoutInstant = true;
		}
		this.arrange();
		// reset flag
		for ( i=0; i < len; i++ ) {
			delete items[i].isLayoutInstant;
		}
		this.reveal( filteredInsertItems );
	};

	var _remove = Isotope.prototype.remove;
	Isotope.prototype.remove = function( elems ) {
		elems = utils.makeArray( elems );
		var removeItems = this.getItems( elems );
		// do regular thing
		_remove.call( this, elems );
		// bail if no items to remove
		var len = removeItems && removeItems.length;
		if ( !len ) {
			return;
		}
		// remove elems from filteredItems
		for ( var i=0; i < len; i++ ) {
			var item = removeItems[i];
			// remove item from collection
			utils.removeFrom( this.filteredItems, item );
		}
	};

	Isotope.prototype.shuffle = function() {
		// update random sortData
		for ( var i=0, len = this.items.length; i < len; i++ ) {
			var item = this.items[i];
			item.sortData.random = Math.random();
		}
		this.options.sortBy = 'random';
		this._sort();
		this._layout();
	};

	/**
	 * trigger fn without transition
	 * kind of hacky to have this in the first place
	 * @param {Function} fn
	 * @returns ret
	 * @private
	 */
	Isotope.prototype._noTransition = function( fn ) {
		// save transitionDuration before disabling
		var transitionDuration = this.options.transitionDuration;
		// disable transition
		this.options.transitionDuration = 0;
		// do it
		var returnValue = fn.call( this );
		// re-enable transition for reveal
		this.options.transitionDuration = transitionDuration;
		return returnValue;
	};

	// ----- helper methods ----- //

	/**
	 * getter method for getting filtered item elements
	 * @returns {Array} elems - collection of item elements
	 */
	Isotope.prototype.getFilteredItemElements = function() {
		var elems = [];
		for ( var i=0, len = this.filteredItems.length; i < len; i++ ) {
			elems.push( this.filteredItems[i].element );
		}
		return elems;
	};

	// -----  ----- //

	return Isotope;

}));


/*!
 * Packery layout mode PACKAGED v1.1.1
 * sub-classes Packery
 * http://packery.metafizzy.co
 */

!function (a) { function b(a) { return new RegExp("(^|\\s+)" + a + "(\\s+|$)") } function c(a, b) { var c = d(a, b) ? f : e; c(a, b) } var d, e, f; "classList" in document.documentElement ? (d = function (a, b) { return a.classList.contains(b) }, e = function (a, b) { a.classList.add(b) }, f = function (a, b) { a.classList.remove(b) }) : (d = function (a, c) { return b(c).test(a.className) }, e = function (a, b) { d(a, b) || (a.className = a.className + " " + b) }, f = function (a, c) { a.className = a.className.replace(b(c), " ") }); var g = { hasClass: d, addClass: e, removeClass: f, toggleClass: c, has: d, add: e, remove: f, toggle: c }; "function" == typeof define && define.amd ? define("classie/classie", g) : "object" == typeof exports ? module.exports = g : a.classie = g }(window), function (a) { function b() { function a(b) { for (var c in a.defaults) this[c] = a.defaults[c]; for (c in b) this[c] = b[c] } return c.Rect = a, a.defaults = { x: 0, y: 0, width: 0, height: 0 }, a.prototype.contains = function (a) { var b = a.width || 0, c = a.height || 0; return this.x <= a.x && this.y <= a.y && this.x + this.width >= a.x + b && this.y + this.height >= a.y + c }, a.prototype.overlaps = function (a) { var b = this.x + this.width, c = this.y + this.height, d = a.x + a.width, e = a.y + a.height; return this.x < d && b > a.x && this.y < e && c > a.y }, a.prototype.getMaximalFreeRects = function (b) { if (!this.overlaps(b)) return !1; var c, d = [], e = this.x + this.width, f = this.y + this.height, g = b.x + b.width, h = b.y + b.height; return this.y < b.y && (c = new a({ x: this.x, y: this.y, width: this.width, height: b.y - this.y }), d.push(c)), e > g && (c = new a({ x: g, y: this.y, width: e - g, height: this.height }), d.push(c)), f > h && (c = new a({ x: this.x, y: h, width: this.width, height: f - h }), d.push(c)), this.x < b.x && (c = new a({ x: this.x, y: this.y, width: b.x - this.x, height: this.height }), d.push(c)), d }, a.prototype.canFit = function (a) { return this.width >= a.width && this.height >= a.height }, a } var c = a.Packery = function () { }; "function" == typeof define && define.amd ? define("packery/js/rect", b) : "object" == typeof exports ? module.exports = b() : (a.Packery = a.Packery || {}, a.Packery.Rect = b()) }(window), function (a) { function b(a) { function b(a, b, c) { this.width = a || 0, this.height = b || 0, this.sortDirection = c || "downwardLeftToRight", this.reset() } b.prototype.reset = function () { this.spaces = [], this.newSpaces = []; var b = new a({ x: 0, y: 0, width: this.width, height: this.height }); this.spaces.push(b), this.sorter = c[this.sortDirection] || c.downwardLeftToRight }, b.prototype.pack = function (a) { for (var b = 0, c = this.spaces.length; c > b; b++) { var d = this.spaces[b]; if (d.canFit(a)) { this.placeInSpace(a, d); break } } }, b.prototype.placeInSpace = function (a, b) { a.x = b.x, a.y = b.y, this.placed(a) }, b.prototype.placed = function (a) { for (var b = [], c = 0, d = this.spaces.length; d > c; c++) { var e = this.spaces[c], f = e.getMaximalFreeRects(a); f ? b.push.apply(b, f) : b.push(e) } this.spaces = b, this.mergeSortSpaces() }, b.prototype.mergeSortSpaces = function () { b.mergeRects(this.spaces), this.spaces.sort(this.sorter) }, b.prototype.addSpace = function (a) { this.spaces.push(a), this.mergeSortSpaces() }, b.mergeRects = function (a) { for (var b = 0, c = a.length; c > b; b++) { var d = a[b]; if (d) { var e = a.slice(0); e.splice(b, 1); for (var f = 0, g = 0, h = e.length; h > g; g++) { var i = e[g], j = b > g ? 0 : 1; d.contains(i) && (a.splice(g + j - f, 1), f++) } } } return a }; var c = { downwardLeftToRight: function (a, b) { return a.y - b.y || a.x - b.x }, rightwardTopToBottom: function (a, b) { return a.x - b.x || a.y - b.y } }; return b } if ("function" == typeof define && define.amd) define("packery/js/packer", ["./rect"], b); else if ("object" == typeof exports) module.exports = b(require("./rect")); else { var c = a.Packery = a.Packery || {}; c.Packer = b(c.Rect) } }(window), function (a) { function b(a, b, c) { var d = a("transform"), e = function () { b.Item.apply(this, arguments) }; e.prototype = new b.Item; var f = e.prototype._create; return e.prototype._create = function () { f.call(this), this.rect = new c, this.placeRect = new c }, e.prototype.dragStart = function () { this.getPosition(), this.removeTransitionStyles(), this.isTransitioning && d && (this.element.style[d] = "none"), this.getSize(), this.isPlacing = !0, this.needsPositioning = !1, this.positionPlaceRect(this.position.x, this.position.y), this.isTransitioning = !1, this.didDrag = !1 }, e.prototype.dragMove = function (a, b) { this.didDrag = !0; var c = this.layout.size; a -= c.paddingLeft, b -= c.paddingTop, this.positionPlaceRect(a, b) }, e.prototype.dragStop = function () { this.getPosition(); var a = this.position.x !== this.placeRect.x, b = this.position.y !== this.placeRect.y; this.needsPositioning = a || b, this.didDrag = !1 }, e.prototype.positionPlaceRect = function (a, b, c) { this.placeRect.x = this.getPlaceRectCoord(a, !0), this.placeRect.y = this.getPlaceRectCoord(b, !1, c) }, e.prototype.getPlaceRectCoord = function (a, b, c) { var d = b ? "Width" : "Height", e = this.size["outer" + d], f = this.layout[b ? "columnWidth" : "rowHeight"], g = this.layout.size["inner" + d]; b || (g = Math.max(g, this.layout.maxY), this.layout.rowHeight || (g -= this.layout.gutter)); var h; if (f) { f += this.layout.gutter, g += b ? this.layout.gutter : 0, a = Math.round(a / f); var i; i = this.layout.options.isHorizontal ? b ? "ceil" : "floor" : b ? "floor" : "ceil"; var j = Math[i](g / f); j -= Math.ceil(e / f), h = j } else h = g - e; return a = c ? a : Math.min(a, h), a *= f || 1, Math.max(0, a) }, e.prototype.copyPlaceRectPosition = function () { this.rect.x = this.placeRect.x, this.rect.y = this.placeRect.y }, e.prototype.removeElem = function () { this.element.parentNode.removeChild(this.element), this.layout.packer.addSpace(this.rect), this.emitEvent("remove", [this]) }, e } "function" == typeof define && define.amd ? define("packery/js/item", ["get-style-property/get-style-property", "outlayer/outlayer", "./rect"], b) : "object" == typeof exports ? module.exports = b(require("desandro-get-style-property"), require("outlayer"), require("./rect")) : a.Packery.Item = b(a.getStyleProperty, a.Outlayer, a.Packery.Rect) }(window), function (a) { function b(a, b, c, d, e, f) { function g(a, b) { return a.position.y - b.position.y || a.position.x - b.position.x } function h(a, b) { return a.position.x - b.position.x || a.position.y - b.position.y } d.prototype.canFit = function (a) { return this.width >= a.width - 1 && this.height >= a.height - 1 }; var i = c.create("packery"); return i.Item = f, i.prototype._create = function () { c.prototype._create.call(this), this.packer = new e, this.stamp(this.options.stamped); var a = this; this.handleDraggabilly = { dragStart: function (b) { a.itemDragStart(b.element) }, dragMove: function (b) { a.itemDragMove(b.element, b.position.x, b.position.y) }, dragEnd: function (b) { a.itemDragEnd(b.element) } }, this.handleUIDraggable = { start: function (b) { a.itemDragStart(b.currentTarget) }, drag: function (b, c) { a.itemDragMove(b.currentTarget, c.position.left, c.position.top) }, stop: function (b) { a.itemDragEnd(b.currentTarget) } } }, i.prototype._resetLayout = function () { this.getSize(), this._getMeasurements(); var a = this.packer; this.options.isHorizontal ? (a.width = Number.POSITIVE_INFINITY, a.height = this.size.innerHeight + this.gutter, a.sortDirection = "rightwardTopToBottom") : (a.width = this.size.innerWidth + this.gutter, a.height = Number.POSITIVE_INFINITY, a.sortDirection = "downwardLeftToRight"), a.reset(), this.maxY = 0, this.maxX = 0 }, i.prototype._getMeasurements = function () { this._getMeasurement("columnWidth", "width"), this._getMeasurement("rowHeight", "height"), this._getMeasurement("gutter", "width") }, i.prototype._getItemLayoutPosition = function (a) { return this._packItem(a), a.rect }, i.prototype._packItem = function (a) { this._setRectSize(a.element, a.rect), this.packer.pack(a.rect), this._setMaxXY(a.rect) }, i.prototype._setMaxXY = function (a) { this.maxX = Math.max(a.x + a.width, this.maxX), this.maxY = Math.max(a.y + a.height, this.maxY) }, i.prototype._setRectSize = function (a, c) { var d = b(a), e = d.outerWidth, f = d.outerHeight; (e || f) && (e = this._applyGridGutter(e, this.columnWidth), f = this._applyGridGutter(f, this.rowHeight)), c.width = Math.min(e, this.packer.width), c.height = Math.min(f, this.packer.height) }, i.prototype._applyGridGutter = function (a, b) { if (!b) return a + this.gutter; b += this.gutter; var c = a % b, d = c && 1 > c ? "round" : "ceil"; return a = Math[d](a / b) * b }, i.prototype._getContainerSize = function () { return this.options.isHorizontal ? { width: this.maxX - this.gutter } : { height: this.maxY - this.gutter } }, i.prototype._manageStamp = function (a) { var b, c = this.getItem(a); if (c && c.isPlacing) b = c.placeRect; else { var e = this._getElementOffset(a); b = new d({ x: this.options.isOriginLeft ? e.left : e.right, y: this.options.isOriginTop ? e.top : e.bottom }) } this._setRectSize(a, b), this.packer.placed(b), this._setMaxXY(b) }, i.prototype.sortItemsByPosition = function () { var a = this.options.isHorizontal ? h : g; this.items.sort(a) }, i.prototype.fit = function (a, b, c) { var d = this.getItem(a); d && (this._getMeasurements(), this.stamp(d.element), d.getSize(), d.isPlacing = !0, b = void 0 === b ? d.rect.x : b, c = void 0 === c ? d.rect.y : c, d.positionPlaceRect(b, c, !0), this._bindFitEvents(d), d.moveTo(d.placeRect.x, d.placeRect.y), this.layout(), this.unstamp(d.element), this.sortItemsByPosition(), d.isPlacing = !1, d.copyPlaceRectPosition()) }, i.prototype._bindFitEvents = function (a) { function b() { d++ , 2 === d && c.emitEvent("fitComplete", [c, a]) } var c = this, d = 0; a.on("layout", function () { return b(), !0 }), this.on("layoutComplete", function () { return b(), !0 }) }, i.prototype.resize = function () { var a = b(this.element), c = this.size && a, d = this.options.isHorizontal ? "innerHeight" : "innerWidth"; c && a[d] === this.size[d] || this.layout() }, i.prototype.itemDragStart = function (a) { this.stamp(a); var b = this.getItem(a); b && b.dragStart() }, i.prototype.itemDragMove = function (a, b, c) { function d() { f.layout(), delete f.dragTimeout } var e = this.getItem(a); e && e.dragMove(b, c); var f = this; this.clearDragTimeout(), this.dragTimeout = setTimeout(d, 40) }, i.prototype.clearDragTimeout = function () { this.dragTimeout && clearTimeout(this.dragTimeout) }, i.prototype.itemDragEnd = function (b) { var c, d = this.getItem(b); if (d && (c = d.didDrag, d.dragStop()), !d || !c && !d.needsPositioning) return void this.unstamp(b); a.add(d.element, "is-positioning-post-drag"); var e = this._getDragEndLayoutComplete(b, d); d.needsPositioning ? (d.on("layout", e), d.moveTo(d.placeRect.x, d.placeRect.y)) : d && d.copyPlaceRectPosition(), this.clearDragTimeout(), this.on("layoutComplete", e), this.layout() }, i.prototype._getDragEndLayoutComplete = function (b, c) { var d = c && c.needsPositioning, e = 0, f = d ? 2 : 1, g = this; return function () { return e++ , e !== f ? !0 : (c && (a.remove(c.element, "is-positioning-post-drag"), c.isPlacing = !1, c.copyPlaceRectPosition()), g.unstamp(b), g.sortItemsByPosition(), d && g.emitEvent("dragItemPositioned", [g, c]), !0) } }, i.prototype.bindDraggabillyEvents = function (a) { a.on("dragStart", this.handleDraggabilly.dragStart), a.on("dragMove", this.handleDraggabilly.dragMove), a.on("dragEnd", this.handleDraggabilly.dragEnd) }, i.prototype.bindUIDraggableEvents = function (a) { a.on("dragstart", this.handleUIDraggable.start).on("drag", this.handleUIDraggable.drag).on("dragstop", this.handleUIDraggable.stop) }, i.Rect = d, i.Packer = e, i } "function" == typeof define && define.amd ? define("packery/js/packery", ["classie/classie", "get-size/get-size", "outlayer/outlayer", "./rect", "./packer", "./item"], b) : "object" == typeof exports ? module.exports = b(require("desandro-classie"), require("get-size"), require("outlayer"), require("./rect"), require("./packer"), require("./item")) : a.Packery = b(a.classie, a.getSize, a.Outlayer, a.Packery.Rect, a.Packery.Packer, a.Packery.Item) }(window), function (a) { function b(a, b) { for (var c in b) a[c] = b[c]; return a } function c(a, c, d) { var e = a.create("packery"), f = e.prototype._getElementOffset, g = e.prototype._getMeasurement; b(e.prototype, c.prototype), e.prototype._getElementOffset = f, e.prototype._getMeasurement = g; var h = e.prototype._resetLayout; e.prototype._resetLayout = function () { this.packer = this.packer || new c.Packer, h.apply(this, arguments) }; var i = e.prototype._getItemLayoutPosition; e.prototype._getItemLayoutPosition = function (a) { return a.rect = a.rect || new c.Rect, i.call(this, a) }; var j = e.prototype._manageStamp; return e.prototype._manageStamp = function () { this.options.isOriginLeft = this.isotope.options.isOriginLeft, this.options.isOriginTop = this.isotope.options.isOriginTop, j.apply(this, arguments) }, e.prototype.needsResizeLayout = function () { var a = d(this.element), b = this.size && a, c = this.options.isHorizontal ? "innerHeight" : "innerWidth"; return b && a[c] !== this.size[c] }, e } "function" == typeof define && define.amd ? define(["isotope/js/layout-mode", "packery/js/packery", "get-size/get-size"], c) : "object" == typeof exports ? module.exports = c(require("isotope-layout/js/layout-mode"), require("packery"), require("get-size")) : c(a.Isotope.LayoutMode, a.Packery, a.getSize) }(window);

/*!
 * Copyright 2012, Chris Wanstrath
 * Released under the MIT License
 * https://github.com/defunkt/jquery-pjax
 */

(function($){

  // When called on a container with a selector, fetches the href with
  // ajax into the container or with the data-pjax attribute on the link
  // itself.
  //
  // Tries to make sure the back button and ctrl+click work the way
  // you'd expect.
  //
  // Exported as $.fn.pjax
  //
  // Accepts a jQuery ajax options object that may include these
  // pjax specific options:
  //
  //
  // container - Where to stick the response body. Usually a String selector.
  //             $(container).html(xhr.responseBody)
  //             (default: current jquery context)
  //      push - Whether to pushState the URL. Defaults to true (of course).
  //   replace - Want to use replaceState instead? That's cool.
  //
  // For convenience the second parameter can be either the container or
  // the options object.
  //
  // Returns the jQuery object
  function fnPjax(selector, container, options) {
    var context = this
    return this.on('click.pjax', selector, function(event) {
      var opts = $.extend({}, optionsFor(container, options))
      if (!opts.container)
        opts.container = $(this).attr('data-pjax') || context
      handleClick(event, opts)
    })
  }

  // Public: pjax on click handler
  //
  // Exported as $.pjax.click.
  //
  // event   - "click" jQuery.Event
  // options - pjax options
  //
  // Examples
  //
  //   $(document).on('click', 'a', $.pjax.click)
  //   // is the same as
  //   $(document).pjax('a')
  //
  //  $(document).on('click', 'a', function(event) {
  //    var container = $(this).closest('[data-pjax-container]')
  //    $.pjax.click(event, container)
  //  })
  //
  // Returns nothing.
  function handleClick(event, container, options) {
    options = optionsFor(container, options)

    var link = event.currentTarget

    if (link.tagName.toUpperCase() !== 'A')
      throw "$.fn.pjax or $.pjax.click requires an anchor element"

    // Middle click, cmd click, and ctrl click should open
    // links in a new tab as normal.
    if ( event.which > 1 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey )
      return

    // Ignore cross origin links
    if ( location.protocol !== link.protocol || location.hostname !== link.hostname )
      return

    // Ignore case when a hash is being tacked on the current URL
    if ( link.href.indexOf('#') > -1 && stripHash(link) == stripHash(location) )
      return

    // Ignore event with default prevented
    if (event.isDefaultPrevented())
      return

    var defaults = {
      url: link.href,
      container: $(link).attr('data-pjax'),
      target: link
    }

    var opts = $.extend({}, defaults, options)
    var clickEvent = $.Event('pjax:click')
    $(link).trigger(clickEvent, [opts])

    if (!clickEvent.isDefaultPrevented()) {
      pjax(opts)
      event.preventDefault()
      $(link).trigger('pjax:clicked', [opts])
    }
  }

  // Public: pjax on form submit handler
  //
  // Exported as $.pjax.submit
  //
  // event   - "click" jQuery.Event
  // options - pjax options
  //
  // Examples
  //
  //  $(document).on('submit', 'form', function(event) {
  //    var container = $(this).closest('[data-pjax-container]')
  //    $.pjax.submit(event, container)
  //  })
  //
  // Returns nothing.
  function handleSubmit(event, container, options) {
    options = optionsFor(container, options)

    var form = event.currentTarget
    var $form = $(form)

    if (form.tagName.toUpperCase() !== 'FORM')
      throw "$.pjax.submit requires a form element"

    var defaults = {
      type: ($form.attr('method') || 'GET').toUpperCase(),
      url: $form.attr('action'),
      container: $form.attr('data-pjax'),
      target: form
    }

    if (defaults.type !== 'GET' && window.FormData !== undefined) {
      defaults.data = new FormData(form);
      defaults.processData = false;
      defaults.contentType = false;
    } else {
      // Can't handle file uploads, exit
      if ($(form).find(':file').length) {
        return;
      }

      // Fallback to manually serializing the fields
      defaults.data = $(form).serializeArray();
    }

    pjax($.extend({}, defaults, options))

    event.preventDefault()
  }

  // Loads a URL with ajax, puts the response body inside a container,
  // then pushState()'s the loaded URL.
  //
  // Works just like $.ajax in that it accepts a jQuery ajax
  // settings object (with keys like url, type, data, etc).
  //
  // Accepts these extra keys:
  //
  // container - Where to stick the response body.
  //             $(container).html(xhr.responseBody)
  //      push - Whether to pushState the URL. Defaults to true (of course).
  //   replace - Want to use replaceState instead? That's cool.
  //
  // Use it just like $.ajax:
  //
  //   var xhr = $.pjax({ url: this.href, container: '#main' })
  //   console.log( xhr.readyState )
  //
  // Returns whatever $.ajax returns.
  function pjax(options) {
    options = $.extend(true, {}, $.ajaxSettings, pjax.defaults, options)

    if ($.isFunction(options.url)) {
      options.url = options.url()
    }

    var target = options.target

    var hash = parseURL(options.url).hash

    var _container = findContainerFor(options.container)
    var context = options.context = _container[0]
    var selector = _container[1]

    // We want the browser to maintain two separate internal caches: one
    // for pjax'd partial page loads and one for normal page loads.
    // Without adding this secret parameter, some browsers will often
    // confuse the two.
    if (!options.data) options.data = {}
    if ($.isArray(options.data)) {
      options.data.push({name: '_pjax', value: selector})
    } else {
      options.data._pjax = selector
    }

    function fire(type, args, props) {
      if (!props) props = {}
      props.relatedTarget = target
      var event = $.Event(type, props)
      context.trigger(event, args)
      return !event.isDefaultPrevented()
    }

    var timeoutTimer

    options.beforeSend = function(xhr, settings) {
      // No timeout for non-GET requests
      // Its not safe to request the resource again with a fallback method.
      if (settings.type !== 'GET') {
        settings.timeout = 0
      }

      xhr.setRequestHeader('X-PJAX', 'true')
      xhr.setRequestHeader('X-PJAX-Container', selector)

      if (!fire('pjax:beforeSend', [xhr, settings]))
        return false

      if (settings.timeout > 0) {
        timeoutTimer = setTimeout(function() {
          if (fire('pjax:timeout', [xhr, options]))
            xhr.abort('timeout')
        }, settings.timeout)

        // Clear timeout setting so jquerys internal timeout isn't invoked
        settings.timeout = 0
      }

      var url = parseURL(settings.url)
      if (hash) url.hash = hash
      options.requestUrl = stripInternalParams(url)
    }

    options.complete = function(xhr, textStatus) {
      if (timeoutTimer)
        clearTimeout(timeoutTimer)

      fire('pjax:complete', [xhr, textStatus, options])

      fire('pjax:end', [xhr, options])
    }

    options.error = function(xhr, textStatus, errorThrown) {
      var container = extractContainer("", xhr, options)

      var allowed = fire('pjax:error', [xhr, textStatus, errorThrown, options])
      if (options.type == 'GET' && textStatus !== 'abort' && allowed) {
        locationReplace(container.url)
      }
    }

    options.success = function(data, status, xhr) {
      var previousState = pjax.state;

      // If $.pjax.defaults.version is a function, invoke it first.
      // Otherwise it can be a static string.
      var currentVersion = (typeof $.pjax.defaults.version === 'function') ?
          $.pjax.defaults.version() :
          $.pjax.defaults.version

      var latestVersion = xhr.getResponseHeader('X-PJAX-Version')

      var container = extractContainer(data, xhr, options)

      var url = parseURL(container.url)
      if (hash) {
        url.hash = hash
        container.url = url.href
      }

      // If there is a layout version mismatch, hard load the new url
      if (currentVersion && latestVersion && currentVersion !== latestVersion) {
        locationReplace(container.url)
        return
      }

      // If the new response is missing a body, hard load the page
      if (!container.contents) {
        locationReplace(container.url)
        return
      }

      pjax.state = {
        id: options.id || uniqueId(),
        url: container.url,
        title: container.title,
        container: selector,
        fragment: options.fragment,
        timeout: options.timeout
      }

      if (options.push || options.replace) {
        window.history.replaceState(pjax.state, container.title, container.url)
      }

      // Only blur the focus if the focused element is within the container.
      var blurFocus = $.contains(options.container, document.activeElement)

      // Clear out any focused controls before inserting new page contents.
      if (blurFocus) {
        try {
          document.activeElement.blur()
        } catch (e) { }
      }

      if (container.title) document.title = container.title

      fire('pjax:beforeReplace', [container.contents, options], {
        state: pjax.state,
        previousState: previousState
      })
      context.html(container.contents)

      // FF bug: Won't autofocus fields that are inserted via JS.
      // This behavior is incorrect. So if theres no current focus, autofocus
      // the last field.
      //
      // http://www.w3.org/html/wg/drafts/html/master/forms.html
      var autofocusEl = context.find('input[autofocus], textarea[autofocus]').last()[0]
      if (autofocusEl && document.activeElement !== autofocusEl) {
        autofocusEl.focus();
      }

      executeScriptTags(container.scripts)

      var scrollTo = options.scrollTo

      // Ensure browser scrolls to the element referenced by the URL anchor
      if (hash) {
        var name = decodeURIComponent(hash.slice(1))
        var target = document.getElementById(name) || document.getElementsByName(name)[0]
        if (target) scrollTo = $(target).offset().top
      }

      if (typeof scrollTo == 'number') $(window).scrollTop(scrollTo)

      fire('pjax:success', [data, status, xhr, options])
    }


    // Initialize pjax.state for the initial page load. Assume we're
    // using the container and options of the link we're loading for the
    // back button to the initial page. This ensures good back button
    // behavior.
    if (!pjax.state) {
      pjax.state = {
        id: uniqueId(),
        url: window.location.href,
        title: document.title,
        container: selector,
        fragment: options.fragment,
        timeout: options.timeout
      }
      window.history.replaceState(pjax.state, document.title)
    }

    // Cancel the current request if we're already pjaxing
    abortXHR(pjax.xhr)

    pjax.options = options
    var xhr = pjax.xhr = $.ajax(options)

    if (xhr.readyState > 0) {
      if (options.push && !options.replace) {
        // Cache current container element before replacing it
        cachePush(pjax.state.id, cloneContents(context, selector))

        window.history.pushState(null, "", options.requestUrl)
      }

      fire('pjax:start', [xhr, options])
      fire('pjax:send', [xhr, options])
    }

    return pjax.xhr
  }

  // Public: Reload current page with pjax.
  //
  // Returns whatever $.pjax returns.
  function pjaxReload(container, options) {
    var defaults = {
      url: window.location.href,
      push: false,
      replace: true,
      scrollTo: false
    }

    return pjax($.extend(defaults, optionsFor(container, options)))
  }

  // Internal: Hard replace current state with url.
  //
  // Work for around WebKit
  //   https://bugs.webkit.org/show_bug.cgi?id=93506
  //
  // Returns nothing.
  function locationReplace(url) {
    window.history.replaceState(null, "", pjax.state.url)
    window.location.replace(url)
  }


  var initialPop = true
  var initialURL = window.location.href
  var initialState = window.history.state

  // Initialize $.pjax.state if possible
  // Happens when reloading a page and coming forward from a different
  // session history.
  if (initialState && initialState.container) {
    pjax.state = initialState
  }

  // Non-webkit browsers don't fire an initial popstate event
  if ('state' in window.history) {
    initialPop = false
  }

  // popstate handler takes care of the back and forward buttons
  //
  // You probably shouldn't use pjax on pages with other pushState
  // stuff yet.
  function onPjaxPopstate(event) {

    // Hitting back or forward should override any pending PJAX request.
    if (!initialPop) {
      abortXHR(pjax.xhr)
    }

    var previousState = pjax.state
    var state = event.state
    var direction

    if (state && state.container) {
      // When coming forward from a separate history session, will get an
      // initial pop with a state we are already at. Skip reloading the current
      // page.
      if (initialPop && initialURL == state.url) return

      if (previousState) {
        // If popping back to the same state, just skip.
        // Could be clicking back from hashchange rather than a pushState.
        if (previousState.id === state.id) return

        // Since state IDs always increase, we can deduce the navigation direction
        direction = previousState.id < state.id ? 'forward' : 'back'
      }

      var cache = cacheMapping[state.id] || []
      var selector = cache[0] || state.container
      var container = $(selector), contents = cache[1]

      if (container.length) {
        if (previousState) {
          // Cache current container before replacement and inform the
          // cache which direction the history shifted.
          cachePop(direction, previousState.id, cloneContents(container, selector))
        }

        var popstateEvent = $.Event('pjax:popstate', {
          state: state,
          direction: direction
        })
        container.trigger(popstateEvent)

        var options = {
          id: state.id,
          url: state.url,
          container: container,
          push: false,
          fragment: state.fragment,
          timeout: state.timeout,
          scrollTo: false
        }

        if (contents) {
          container.trigger('pjax:start', [null, options])

          pjax.state = state
          if (state.title) document.title = state.title
          var beforeReplaceEvent = $.Event('pjax:beforeReplace', {
            state: state,
            previousState: previousState
          })
          container.trigger(beforeReplaceEvent, [contents, options])
          container.html(contents)

          container.trigger('pjax:end', [null, options])
        } else {
          pjax(options)
        }

        // Force reflow/relayout before the browser tries to restore the
        // scroll position.
        container[0].offsetHeight
      } else {
        locationReplace(location.href)
      }
    }
    initialPop = false
  }

  // Fallback version of main pjax function for browsers that don't
  // support pushState.
  //
  // Returns nothing since it retriggers a hard form submission.
  function fallbackPjax(options) {
    var url = $.isFunction(options.url) ? options.url() : options.url,
        method = options.type ? options.type.toUpperCase() : 'GET'

    var form = $('<form>', {
      method: method === 'GET' ? 'GET' : 'POST',
      action: url,
      style: 'display:none'
    })

    if (method !== 'GET' && method !== 'POST') {
      form.append($('<input>', {
        type: 'hidden',
        name: '_method',
        value: method.toLowerCase()
      }))
    }

    var data = options.data
    if (typeof data === 'string') {
      $.each(data.split('&'), function(index, value) {
        var pair = value.split('=')
        form.append($('<input>', {type: 'hidden', name: pair[0], value: pair[1]}))
      })
    } else if ($.isArray(data)) {
      $.each(data, function(index, value) {
        form.append($('<input>', {type: 'hidden', name: value.name, value: value.value}))
      })
    } else if (typeof data === 'object') {
      var key
      for (key in data)
        form.append($('<input>', {type: 'hidden', name: key, value: data[key]}))
    }

    $(document.body).append(form)
    form.submit()
  }

  // Internal: Abort an XmlHttpRequest if it hasn't been completed,
  // also removing its event handlers.
  function abortXHR(xhr) {
    if ( xhr && xhr.readyState < 4) {
      xhr.onreadystatechange = $.noop
      xhr.abort()
    }
  }

  // Internal: Generate unique id for state object.
  //
  // Use a timestamp instead of a counter since ids should still be
  // unique across page loads.
  //
  // Returns Number.
  function uniqueId() {
    return (new Date).getTime()
  }

  function cloneContents(container, selector) {
    var cloned = container.clone()
    // Unmark script tags as already being eval'd so they can get executed again
    // when restored from cache. HAXX: Uses jQuery internal method.
    cloned.find('script').each(function(){
      if (!this.src) jQuery._data(this, 'globalEval', false)
    })
    return [selector, cloned.contents()]
  }

  // Internal: Strip internal query params from parsed URL.
  //
  // Returns sanitized url.href String.
  function stripInternalParams(url) {
    url.search = url.search.replace(/([?&])(_pjax|_)=[^&]*/g, '')
    return url.href.replace(/\?($|#)/, '$1')
  }

  // Internal: Parse URL components and returns a Locationish object.
  //
  // url - String URL
  //
  // Returns HTMLAnchorElement that acts like Location.
  function parseURL(url) {
    var a = document.createElement('a')
    a.href = url
    return a
  }

  // Internal: Return the `href` component of given URL object with the hash
  // portion removed.
  //
  // location - Location or HTMLAnchorElement
  //
  // Returns String
  function stripHash(location) {
    return location.href.replace(/#.*/, '')
  }

  // Internal: Build options Object for arguments.
  //
  // For convenience the first parameter can be either the container or
  // the options object.
  //
  // Examples
  //
  //   optionsFor('#container')
  //   // => {container: '#container'}
  //
  //   optionsFor('#container', {push: true})
  //   // => {container: '#container', push: true}
  //
  //   optionsFor({container: '#container', push: true})
  //   // => {container: '#container', push: true}
  //
  // Returns options Object.
  function optionsFor(container, options) {
    // Both container and options
    if ( container && options )
      options.container = container

    // First argument is options Object
    else if ( $.isPlainObject(container) )
      options = container

    // Only container
    else
      options = {container: container}

    // Find and validate container
    if (options.container)
      options.container = findContainerFor(options.container)

    return options
  }

  // Internal: Find container element for a variety of inputs.
  //
  // Because we can't persist elements using the history API, we must be
  // able to find a String selector that will consistently find the Element.
  //
  // container - A selector String, jQuery object, or DOM Element.
  //
  // Returns a jQuery object whose context is `document` and has a selector.
  function findContainerFor(container) {
    var selector, $container;
    if ( $.isArray(container) ) {
      $container = container[0]
      selector = container[1]
    } else {
      selector = container
      $container = $(selector)
    }

    if ( !$container.length ) {
      throw "no pjax container for " + selector
    } else if ( true ) {
      return [$container, selector];
    } else if ( container.selector !== '' && container.context === document ) {
      return container
    } else if ( container.attr('id') ) {
      return $('#' + container.attr('id'))
    } else {
      throw "cant get selector for pjax container!"
    }
  }

  // Internal: Filter and find all elements matching the selector.
  //
  // Where $.fn.find only matches descendants, findAll will test all the
  // top level elements in the jQuery object as well.
  //
  // elems    - jQuery object of Elements
  // selector - String selector to match
  //
  // Returns a jQuery object.
  function findAll(elems, selector) {
    return elems.filter(selector).add(elems.find(selector));
  }

  function parseHTML(html) {
    return $.parseHTML(html, document, true)
  }

  // Internal: Extracts container and metadata from response.
  //
  // 1. Extracts X-PJAX-URL header if set
  // 2. Extracts inline <title> tags
  // 3. Builds response Element and extracts fragment if set
  //
  // data    - String response data
  // xhr     - XHR response
  // options - pjax options Object
  //
  // Returns an Object with url, title, and contents keys.
  function extractContainer(data, xhr, options) {
    var obj = {}, fullDocument = /<html/i.test(data)

    // Prefer X-PJAX-URL header if it was set, otherwise fallback to
    // using the original requested url.
    var serverUrl = xhr.getResponseHeader('X-PJAX-URL')
    obj.url = serverUrl ? stripInternalParams(parseURL(serverUrl)) : options.requestUrl

    // Attempt to parse response html into elements
    if (fullDocument) {
      var $head = $(parseHTML(data.match(/<head[^>]*>([\s\S.]*)<\/head>/i)[0]))
      var $body = $(parseHTML(data.match(/<body[^>]*>([\s\S.]*)<\/body>/i)[0]))
    } else {
      var $head = $body = $(parseHTML(data))
    }

    // If response data is empty, return fast
    if ($body.length === 0)
      return obj

    // If there's a <title> tag in the header, use it as
    // the page's title.
    obj.title = findAll($head, 'title').last().text()

    if (options.fragment) {
      // If they specified a fragment, look for it in the response
      // and pull it out.
      if (options.fragment === 'body') {
        var $fragment = $body
      } else {
        var $fragment = findAll($body, options.fragment).first()
      }

      if ($fragment.length) {
        obj.contents = options.fragment === 'body' ? $fragment : $fragment.contents()

        // If there's no title, look for data-title and title attributes
        // on the fragment
        if (!obj.title)
          obj.title = $fragment.attr('title') || $fragment.data('title')
      }

    } else if (!fullDocument) {
      obj.contents = $body
    }

    // Clean up any <title> tags
    if (obj.contents) {
      // Remove any parent title elements
      obj.contents = obj.contents.not(function() { return $(this).is('title') })

      // Then scrub any titles from their descendants
      obj.contents.find('title').remove()

      // Gather all script[src] elements
      obj.scripts = findAll(obj.contents, 'script[src]').remove()
      obj.contents = obj.contents.not(obj.scripts)
    }

    // Trim any whitespace off the title
    if (obj.title) obj.title = $.trim(obj.title)

    return obj
  }

  // Load an execute scripts using standard script request.
  //
  // Avoids jQuery's traditional $.getScript which does a XHR request and
  // globalEval.
  //
  // scripts - jQuery object of script Elements
  //
  // Returns nothing.
  function executeScriptTags(scripts) {
    if (!scripts) return

    var existingScripts = $('script[src]')

    scripts.each(function() {
      var src = this.src
      var matchedScripts = existingScripts.filter(function() {
        return this.src === src
      })
      if (matchedScripts.length) return

      var script = document.createElement('script')
      var type = $(this).attr('type')
      if (type) script.type = type
      script.src = $(this).attr('src')
      document.head.appendChild(script)
    })
  }

  // Internal: History DOM caching class.
  var cacheMapping      = {}
  var cacheForwardStack = []
  var cacheBackStack    = []

  // Push previous state id and container contents into the history
  // cache. Should be called in conjunction with `pushState` to save the
  // previous container contents.
  //
  // id    - State ID Number
  // value - DOM Element to cache
  //
  // Returns nothing.
  function cachePush(id, value) {
    cacheMapping[id] = value
    cacheBackStack.push(id)

    // Remove all entries in forward history stack after pushing a new page.
    trimCacheStack(cacheForwardStack, 0)

    // Trim back history stack to max cache length.
    trimCacheStack(cacheBackStack, pjax.defaults.maxCacheLength)
  }

  // Shifts cache from directional history cache. Should be
  // called on `popstate` with the previous state id and container
  // contents.
  //
  // direction - "forward" or "back" String
  // id        - State ID Number
  // value     - DOM Element to cache
  //
  // Returns nothing.
  function cachePop(direction, id, value) {
    var pushStack, popStack
    cacheMapping[id] = value

    if (direction === 'forward') {
      pushStack = cacheBackStack
      popStack  = cacheForwardStack
    } else {
      pushStack = cacheForwardStack
      popStack  = cacheBackStack
    }

    pushStack.push(id)
    if (id = popStack.pop())
      delete cacheMapping[id]

    // Trim whichever stack we just pushed to to max cache length.
    trimCacheStack(pushStack, pjax.defaults.maxCacheLength)
  }

  // Trim a cache stack (either cacheBackStack or cacheForwardStack) to be no
  // longer than the specified length, deleting cached DOM elements as necessary.
  //
  // stack  - Array of state IDs
  // length - Maximum length to trim to
  //
  // Returns nothing.
  function trimCacheStack(stack, length) {
    while (stack.length > length)
      delete cacheMapping[stack.shift()]
  }

  // Public: Find version identifier for the initial page load.
  //
  // Returns String version or undefined.
  function findVersion() {
    return $('meta').filter(function() {
      var name = $(this).attr('http-equiv')
      return name && name.toUpperCase() === 'X-PJAX-VERSION'
    }).attr('content')
  }

  // Install pjax functions on $.pjax to enable pushState behavior.
  //
  // Does nothing if already enabled.
  //
  // Examples
  //
  //     $.pjax.enable()
  //
  // Returns nothing.
  function enable() {
    $.fn.pjax = fnPjax
    $.pjax = pjax
    $.pjax.enable = $.noop
    $.pjax.disable = disable
    $.pjax.click = handleClick
    $.pjax.submit = handleSubmit
    $.pjax.reload = pjaxReload
    $.pjax.defaults = {
      timeout: 650,
      push: true,
      replace: false,
      type: 'GET',
      dataType: 'html',
      scrollTo: 0,
      maxCacheLength: 20,
      version: findVersion
    }
    $(window).on('popstate.pjax', onPjaxPopstate)
  }

  // Disable pushState behavior.
  //
  // This is the case when a browser doesn't support pushState. It is
  // sometimes useful to disable pushState for debugging on a modern
  // browser.
  //
  // Examples
  //
  //     $.pjax.disable()
  //
  // Returns nothing.
  function disable() {
    $.fn.pjax = function() { return this }
    $.pjax = fallbackPjax
    $.pjax.enable = enable
    $.pjax.disable = $.noop
    $.pjax.click = $.noop
    $.pjax.submit = $.noop
    $.pjax.reload = function() { window.location.reload() }

    $(window).off('popstate.pjax', onPjaxPopstate)
  }


  // Add the state property to jQuery's event object so we can use it in
  // $(window).bind('popstate')
  if ( $.event.props && $.inArray('state', $.event.props) < 0 ) {
    $.event.props.push('state');
  } else if ( ! ('state' in $.Event.prototype) ) {
    $.event.addProp('state');
  }

  // Is pjax supported by this browser?
  $.support.pjax =
      window.history && window.history.pushState && window.history.replaceState &&
      // pushState isn't reliable on iOS until 5.
      !navigator.userAgent.match(/((iPod|iPhone|iPad).+\bOS\s+[1-4]\D|WebApps\/.+CFNetwork)/)

  $.support.pjax ? enable() : disable()

})(jQuery);
/*!
 * JavaScript Cookie v2.1.4
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
; (function (factory) {
	var registeredInModuleLoader = false;
	if (typeof define === 'function' && define.amd) {
		define(factory);
		registeredInModuleLoader = true;
	}
	if (typeof exports === 'object') {
		module.exports = factory();
		registeredInModuleLoader = true;
	}
	if (!registeredInModuleLoader) {
		var OldCookies = window.Cookies;
		var api = window.Cookies = factory();
		api.noConflict = function () {
			window.Cookies = OldCookies;
			return api;
		};
	}
}(function () {
	function extend() {
		var i = 0;
		var result = {};
		for (; i < arguments.length; i++) {
			var attributes = arguments[i];
			for (var key in attributes) {
				result[key] = attributes[key];
			}
		}
		return result;
	}

	function init(converter) {
		function api(key, value, attributes) {
			var result;
			if (typeof document === 'undefined') {
				return;
			}

			// Write

			if (arguments.length > 1) {
				attributes = extend({
					path: '/'
				}, api.defaults, attributes);

				if (typeof attributes.expires === 'number') {
					var expires = new Date();
					expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
					attributes.expires = expires;
				}

				// We're using "expires" because "max-age" is not supported by IE
				attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

				try {
					result = JSON.stringify(value);
					if (/^[\{\[]/.test(result)) {
						value = result;
					}
				} catch (e) { }

				if (!converter.write) {
					value = encodeURIComponent(String(value))
						.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
				} else {
					value = converter.write(value, key);
				}

				key = encodeURIComponent(String(key));
				key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
				key = key.replace(/[\(\)]/g, escape);

				var stringifiedAttributes = '';

				for (var attributeName in attributes) {
					if (!attributes[attributeName]) {
						continue;
					}
					stringifiedAttributes += '; ' + attributeName;
					if (attributes[attributeName] === true) {
						continue;
					}
					stringifiedAttributes += '=' + attributes[attributeName];
				}
				return (document.cookie = key + '=' + value + stringifiedAttributes);
			}

			// Read

			if (!key) {
				result = {};
			}

			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all. Also prevents odd result when
			// calling "get()"
			var cookies = document.cookie ? document.cookie.split('; ') : [];
			var rdecode = /(%[0-9A-Z]{2})+/g;
			var i = 0;

			for (; i < cookies.length; i++) {
				var parts = cookies[i].split('=');
				var cookie = parts.slice(1).join('=');

				if (cookie.charAt(0) === '"') {
					cookie = cookie.slice(1, -1);
				}

				try {
					var name = parts[0].replace(rdecode, decodeURIComponent);
					cookie = converter.read ?
						converter.read(cookie, name) : converter(cookie, name) ||
						cookie.replace(rdecode, decodeURIComponent);

					if (this.json) {
						try {
							cookie = JSON.parse(cookie);
						} catch (e) { }
					}

					if (key === name) {
						result = cookie;
						break;
					}

					if (!key) {
						result[name] = cookie;
					}
				} catch (e) { }
			}

			return result;
		}

		api.set = api;
		api.get = function (key) {
			return api.call(api, key);
		};
		api.getJSON = function () {
			return api.apply({
				json: true
			}, [].slice.call(arguments));
		};
		api.defaults = {};

		api.remove = function (key, attributes) {
			api(key, '', extend(attributes, {
				expires: -1
			}));
		};

		api.withConverter = init;

		return api;
	}

	return init(function () { });
}));
/*! Magnific Popup - v1.0.0 - 2015-01-03
* http://dimsemenov.com/plugins/magnific-popup/
* Copyright (c) 2015 Dmitry Semenov; */
;(function (factory) { 
if (typeof define === 'function' && define.amd) { 
 // AMD. Register as an anonymous module. 
 define(['jquery'], factory); 
 } else if (typeof exports === 'object') { 
 // Node/CommonJS 
 factory(require('jquery')); 
 } else { 
 // Browser globals 
 factory(window.jQuery || window.Zepto); 
 } 
 }(function($) { 

/*>>core*/
/**
 * 
 * Magnific Popup Core JS file
 * 
 */


/**
 * Private static constants
 */
var CLOSE_EVENT = 'Close',
	BEFORE_CLOSE_EVENT = 'BeforeClose',
	AFTER_CLOSE_EVENT = 'AfterClose',
	BEFORE_APPEND_EVENT = 'BeforeAppend',
	MARKUP_PARSE_EVENT = 'MarkupParse',
	OPEN_EVENT = 'Open',
	CHANGE_EVENT = 'Change',
	NS = 'mfp',
	EVENT_NS = '.' + NS,
	READY_CLASS = 'mfp-ready',
	REMOVING_CLASS = 'mfp-removing',
	PREVENT_CLOSE_CLASS = 'mfp-prevent-close';


/**
 * Private vars 
 */
/*jshint -W079 */
var mfp, // As we have only one instance of MagnificPopup object, we define it locally to not to use 'this'
	MagnificPopup = function(){},
	_isJQ = !!(window.jQuery),
	_prevStatus,
	_window = $(window),
	_document,
	_prevContentType,
	_wrapClasses,
	_currPopupType;


/**
 * Private functions
 */
var _mfpOn = function(name, f) {
		mfp.ev.on(NS + name + EVENT_NS, f);
	},
	_getEl = function(className, appendTo, html, raw) {
		var el = document.createElement('div');
		el.className = 'mfp-'+className;
		if(html) {
			el.innerHTML = html;
		}
		if(!raw) {
			el = $(el);
			if(appendTo) {
				el.appendTo(appendTo);
			}
		} else if(appendTo) {
			appendTo.appendChild(el);
		}
		return el;
	},
	_mfpTrigger = function(e, data) {
		mfp.ev.triggerHandler(NS + e, data);

		if(mfp.st.callbacks) {
			// converts "mfpEventName" to "eventName" callback and triggers it if it's present
			e = e.charAt(0).toLowerCase() + e.slice(1);
			if(mfp.st.callbacks[e]) {
				mfp.st.callbacks[e].apply(mfp, $.isArray(data) ? data : [data]);
			}
		}
	},
	_getCloseBtn = function(type) {
		if(type !== _currPopupType || !mfp.currTemplate.closeBtn) {
			mfp.currTemplate.closeBtn = $( mfp.st.closeMarkup.replace('%title%', mfp.st.tClose ) );
			_currPopupType = type;
		}
		return mfp.currTemplate.closeBtn;
	},
	// Initialize Magnific Popup only when called at least once
	_checkInstance = function() {
		if(!$.magnificPopup.instance) {
			/*jshint -W020 */
			mfp = new MagnificPopup();
			mfp.init();
			$.magnificPopup.instance = mfp;
		}
	},
	// CSS transition detection, http://stackoverflow.com/questions/7264899/detect-css-transitions-using-javascript-and-without-modernizr
	supportsTransitions = function() {
		var s = document.createElement('p').style, // 's' for style. better to create an element if body yet to exist
			v = ['ms','O','Moz','Webkit']; // 'v' for vendor

		if( s['transition'] !== undefined ) {
			return true; 
		}
			
		while( v.length ) {
			if( v.pop() + 'Transition' in s ) {
				return true;
			}
		}
				
		return false;
	};



/**
 * Public functions
 */
MagnificPopup.prototype = {

	constructor: MagnificPopup,

	/**
	 * Initializes Magnific Popup plugin. 
	 * This function is triggered only once when $.fn.magnificPopup or $.magnificPopup is executed
	 */
	init: function() {
		var appVersion = navigator.appVersion;
		mfp.isIE7 = appVersion.indexOf("MSIE 7.") !== -1; 
		mfp.isIE8 = appVersion.indexOf("MSIE 8.") !== -1;
		mfp.isLowIE = mfp.isIE7 || mfp.isIE8;
		mfp.isAndroid = (/android/gi).test(appVersion);
		mfp.isIOS = (/iphone|ipad|ipod/gi).test(appVersion);
		mfp.supportsTransition = supportsTransitions();

		// We disable fixed positioned lightbox on devices that don't handle it nicely.
		// If you know a better way of detecting this - let me know.
		mfp.probablyMobile = (mfp.isAndroid || mfp.isIOS || /(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent) );
		_document = $(document);

		mfp.popupsCache = {};
	},

	/**
	 * Opens popup
	 * @param  data [description]
	 */
	open: function(data) {

		var i;

		if(data.isObj === false) { 
			// convert jQuery collection to array to avoid conflicts later
			mfp.items = data.items.toArray();

			mfp.index = 0;
			var items = data.items,
				item;
			for(i = 0; i < items.length; i++) {
				item = items[i];
				if(item.parsed) {
					item = item.el[0];
				}
				if(item === data.el[0]) {
					mfp.index = i;
					break;
				}
			}
		} else {
			mfp.items = $.isArray(data.items) ? data.items : [data.items];
			mfp.index = data.index || 0;
		}

		// if popup is already opened - we just update the content
		if(mfp.isOpen) {
			mfp.updateItemHTML();
			return;
		}
		
		mfp.types = []; 
		_wrapClasses = '';
		if(data.mainEl && data.mainEl.length) {
			mfp.ev = data.mainEl.eq(0);
		} else {
			mfp.ev = _document;
		}

		if(data.key) {
			if(!mfp.popupsCache[data.key]) {
				mfp.popupsCache[data.key] = {};
			}
			mfp.currTemplate = mfp.popupsCache[data.key];
		} else {
			mfp.currTemplate = {};
		}



		mfp.st = $.extend(true, {}, $.magnificPopup.defaults, data ); 
		mfp.fixedContentPos = mfp.st.fixedContentPos === 'auto' ? !mfp.probablyMobile : mfp.st.fixedContentPos;

		if(mfp.st.modal) {
			mfp.st.closeOnContentClick = false;
			mfp.st.closeOnBgClick = false;
			mfp.st.showCloseBtn = false;
			mfp.st.enableEscapeKey = false;
		}
		

		// Building markup
		// main containers are created only once
		if(!mfp.bgOverlay) {

			// Dark overlay
			mfp.bgOverlay = _getEl('bg').on('click'+EVENT_NS, function() {
				mfp.close();
			});

			mfp.wrap = _getEl('wrap').attr('tabindex', -1).on('click'+EVENT_NS, function(e) {
				if(mfp._checkIfClose(e.target)) {
					mfp.close();
				}
			});

			mfp.container = _getEl('container', mfp.wrap);
		}

		mfp.contentContainer = _getEl('content');
		if(mfp.st.preloader) {
			mfp.preloader = _getEl('preloader', mfp.container, mfp.st.tLoading);
		}


		// Initializing modules
		var modules = $.magnificPopup.modules;
		for(i = 0; i < modules.length; i++) {
			var n = modules[i];
			n = n.charAt(0).toUpperCase() + n.slice(1);
			mfp['init'+n].call(mfp);
		}
		_mfpTrigger('BeforeOpen');


		if(mfp.st.showCloseBtn) {
			// Close button
			if(!mfp.st.closeBtnInside) {
				mfp.wrap.append( _getCloseBtn() );
			} else {
				_mfpOn(MARKUP_PARSE_EVENT, function(e, template, values, item) {
					values.close_replaceWith = _getCloseBtn(item.type);
				});
				_wrapClasses += ' mfp-close-btn-in';
			}
		}

		if(mfp.st.alignTop) {
			_wrapClasses += ' mfp-align-top';
		}

	

		if(mfp.fixedContentPos) {
			mfp.wrap.css({
				overflow: mfp.st.overflowY,
				overflowX: 'hidden',
				overflowY: mfp.st.overflowY
			});
		} else {
			mfp.wrap.css({ 
				top: _window.scrollTop(),
				position: 'absolute'
			});
		}
		if( mfp.st.fixedBgPos === false || (mfp.st.fixedBgPos === 'auto' && !mfp.fixedContentPos) ) {
			mfp.bgOverlay.css({
				height: _document.height(),
				position: 'absolute'
			});
		}

		

		if(mfp.st.enableEscapeKey) {
			// Close on ESC key
			_document.on('keyup' + EVENT_NS, function(e) {
				if(e.keyCode === 27) {
					mfp.close();
				}
			});
		}

		_window.on('resize' + EVENT_NS, function() {
			mfp.updateSize();
		});


		if(!mfp.st.closeOnContentClick) {
			_wrapClasses += ' mfp-auto-cursor';
		}
		
		if(_wrapClasses)
			mfp.wrap.addClass(_wrapClasses);


		// this triggers recalculation of layout, so we get it once to not to trigger twice
		var windowHeight = mfp.wH = _window.height();

		
		var windowStyles = {};

		if( mfp.fixedContentPos ) {
            if(mfp._hasScrollBar(windowHeight)){
                var s = mfp._getScrollbarSize();
                if(s) {
                    windowStyles.marginRight = s;
                }
            }
        }

		if(mfp.fixedContentPos) {
			if(!mfp.isIE7) {
				windowStyles.overflow = 'hidden';
			} else {
				// ie7 double-scroll bug
				$('body, html').css('overflow', 'hidden');
			}
		}

		
		
		var classesToadd = mfp.st.mainClass;
		if(mfp.isIE7) {
			classesToadd += ' mfp-ie7';
		}
		if(classesToadd) {
			mfp._addClassToMFP( classesToadd );
		}

		// add content
		mfp.updateItemHTML();

		_mfpTrigger('BuildControls');

		// remove scrollbar, add margin e.t.c
		$('html').css(windowStyles);
		
		// add everything to DOM
		mfp.bgOverlay.add(mfp.wrap).prependTo( mfp.st.prependTo || $(document.body) );

		// Save last focused element
		mfp._lastFocusedEl = document.activeElement;
		
		// Wait for next cycle to allow CSS transition
		setTimeout(function() {
			
			if(mfp.content) {
				mfp._addClassToMFP(READY_CLASS);
				mfp._setFocus();
			} else {
				// if content is not defined (not loaded e.t.c) we add class only for BG
				mfp.bgOverlay.addClass(READY_CLASS);
			}
			
			// Trap the focus in popup
			_document.on('focusin' + EVENT_NS, mfp._onFocusIn);

		}, 16);

		mfp.isOpen = true;
		mfp.updateSize(windowHeight);
		_mfpTrigger(OPEN_EVENT);

		return data;
	},

	/**
	 * Closes the popup
	 */
	close: function() {
		if(!mfp.isOpen) return;
		_mfpTrigger(BEFORE_CLOSE_EVENT);

		mfp.isOpen = false;
		// for CSS3 animation
		if(mfp.st.removalDelay && !mfp.isLowIE && mfp.supportsTransition )  {
			mfp._addClassToMFP(REMOVING_CLASS);
			setTimeout(function() {
				mfp._close();
			}, mfp.st.removalDelay);
		} else {
			mfp._close();
		}
	},

	/**
	 * Helper for close() function
	 */
	_close: function() {
		_mfpTrigger(CLOSE_EVENT);

		var classesToRemove = REMOVING_CLASS + ' ' + READY_CLASS + ' ';

		mfp.bgOverlay.detach();
		mfp.wrap.detach();
		mfp.container.empty();

		if(mfp.st.mainClass) {
			classesToRemove += mfp.st.mainClass + ' ';
		}

		mfp._removeClassFromMFP(classesToRemove);

		if(mfp.fixedContentPos) {
			var windowStyles = {marginRight: ''};
			if(mfp.isIE7) {
				$('body, html').css('overflow', '');
			} else {
				windowStyles.overflow = '';
			}
			$('html').css(windowStyles);
		}
		
		_document.off('keyup' + EVENT_NS + ' focusin' + EVENT_NS);
		mfp.ev.off(EVENT_NS);

		// clean up DOM elements that aren't removed
		mfp.wrap.attr('class', 'mfp-wrap').removeAttr('style');
		mfp.bgOverlay.attr('class', 'mfp-bg');
		mfp.container.attr('class', 'mfp-container');

		// remove close button from target element
		if(mfp.st.showCloseBtn &&
		(!mfp.st.closeBtnInside || mfp.currTemplate[mfp.currItem.type] === true)) {
			if(mfp.currTemplate.closeBtn)
				mfp.currTemplate.closeBtn.detach();
		}


		// if(mfp._lastFocusedEl) {
		// 	$(mfp._lastFocusedEl).focus(); // put tab focus back
		// }
		mfp.currItem = null;	
		mfp.content = null;
		mfp.currTemplate = null;
		mfp.prevHeight = 0;

		_mfpTrigger(AFTER_CLOSE_EVENT);
	},
	
	updateSize: function(winHeight) {

		if(mfp.isIOS) {
			// fixes iOS nav bars https://github.com/dimsemenov/Magnific-Popup/issues/2
			var zoomLevel = document.documentElement.clientWidth / window.innerWidth;
			var height = window.innerHeight * zoomLevel;
			mfp.wrap.css('height', height);
			mfp.wH = height;
		} else {
			mfp.wH = winHeight || _window.height();
		}
		// Fixes #84: popup incorrectly positioned with position:relative on body
		if(!mfp.fixedContentPos) {
			mfp.wrap.css('height', mfp.wH);
		}

		_mfpTrigger('Resize');

	},

	/**
	 * Set content of popup based on current index
	 */
	updateItemHTML: function() {
		var item = mfp.items[mfp.index];

		// Detach and perform modifications
		mfp.contentContainer.detach();

		if(mfp.content)
			mfp.content.detach();

		if(!item.parsed) {
			item = mfp.parseEl( mfp.index );
		}

		var type = item.type;	

		_mfpTrigger('BeforeChange', [mfp.currItem ? mfp.currItem.type : '', type]);
		// BeforeChange event works like so:
		// _mfpOn('BeforeChange', function(e, prevType, newType) { });
		
		mfp.currItem = item;

		

		

		if(!mfp.currTemplate[type]) {
			var markup = mfp.st[type] ? mfp.st[type].markup : false;

			// allows to modify markup
			_mfpTrigger('FirstMarkupParse', markup);

			if(markup) {
				mfp.currTemplate[type] = $(markup);
			} else {
				// if there is no markup found we just define that template is parsed
				mfp.currTemplate[type] = true;
			}
		}

		if(_prevContentType && _prevContentType !== item.type) {
			mfp.container.removeClass('mfp-'+_prevContentType+'-holder');
		}
		
		var newContent = mfp['get' + type.charAt(0).toUpperCase() + type.slice(1)](item, mfp.currTemplate[type]);
		mfp.appendContent(newContent, type);

		item.preloaded = true;

		_mfpTrigger(CHANGE_EVENT, item);
		_prevContentType = item.type;
		
		// Append container back after its content changed
		mfp.container.prepend(mfp.contentContainer);

		_mfpTrigger('AfterChange');
	},


	/**
	 * Set HTML content of popup
	 */
	appendContent: function(newContent, type) {
		mfp.content = newContent;
		
		if(newContent) {
			if(mfp.st.showCloseBtn && mfp.st.closeBtnInside &&
				mfp.currTemplate[type] === true) {
				// if there is no markup, we just append close button element inside
				if(!mfp.content.find('.mfp-close').length) {
					mfp.content.append(_getCloseBtn());
				}
			} else {
				mfp.content = newContent;
			}
		} else {
			mfp.content = '';
		}

		_mfpTrigger(BEFORE_APPEND_EVENT);
		mfp.container.addClass('mfp-'+type+'-holder');

		mfp.contentContainer.append(mfp.content);
	},



	
	/**
	 * Creates Magnific Popup data object based on given data
	 * @param  {int} index Index of item to parse
	 */
	parseEl: function(index) {
		var item = mfp.items[index],
			type;

		if(item.tagName) {
			item = { el: $(item) };
		} else {
			type = item.type;
			item = { data: item, src: item.src };
		}

		if(item.el) {
			var types = mfp.types;

			// check for 'mfp-TYPE' class
			for(var i = 0; i < types.length; i++) {
				if( item.el.hasClass('mfp-'+types[i]) ) {
					type = types[i];
					break;
				}
			}

			item.src = item.el.attr('data-mfp-src');
			if(!item.src) {
				item.src = item.el.attr('href');
			}
		}

		item.type = type || mfp.st.type || 'inline';
		item.index = index;
		item.parsed = true;
		mfp.items[index] = item;
		_mfpTrigger('ElementParse', item);

		return mfp.items[index];
	},


	/**
	 * Initializes single popup or a group of popups
	 */
	addGroup: function(el, options) {
		var eHandler = function(e) {
			e.mfpEl = this;
			mfp._openClick(e, el, options);
		};

		if(!options) {
			options = {};
		} 

		var eName = 'click.magnificPopup';
		options.mainEl = el;
		
		if(options.items) {
			options.isObj = true;
			el.off(eName).on(eName, eHandler);
		} else {
			options.isObj = false;
			if(options.delegate) {
				el.off(eName).on(eName, options.delegate , eHandler);
			} else {
				options.items = el;
				el.off(eName).on(eName, eHandler);
			}
		}
	},
	_openClick: function(e, el, options) {
		var midClick = options.midClick !== undefined ? options.midClick : $.magnificPopup.defaults.midClick;


		if(!midClick && ( e.which === 2 || e.ctrlKey || e.metaKey ) ) {
			return;
		}

		var disableOn = options.disableOn !== undefined ? options.disableOn : $.magnificPopup.defaults.disableOn;

		if(disableOn) {
			if($.isFunction(disableOn)) {
				if( !disableOn.call(mfp) ) {
					return true;
				}
			} else { // else it's number
				if( _window.width() < disableOn ) {
					return true;
				}
			}
		}
		
		if(e.type) {
			e.preventDefault();

			// This will prevent popup from closing if element is inside and popup is already opened
			if(mfp.isOpen) {
				e.stopPropagation();
			}
		}
			

		options.el = $(e.mfpEl);
		if(options.delegate) {
			options.items = el.find(options.delegate);
		}
		mfp.open(options);
	},


	/**
	 * Updates text on preloader
	 */
	updateStatus: function(status, text) {

		if(mfp.preloader) {
			if(_prevStatus !== status) {
				mfp.container.removeClass('mfp-s-'+_prevStatus);
			}

			if(!text && status === 'loading') {
				text = mfp.st.tLoading;
			}

			var data = {
				status: status,
				text: text
			};
			// allows to modify status
			_mfpTrigger('UpdateStatus', data);

			status = data.status;
			text = data.text;

			mfp.preloader.html(text);

			mfp.preloader.find('a').on('click', function(e) {
				e.stopImmediatePropagation();
			});

			mfp.container.addClass('mfp-s-'+status);
			_prevStatus = status;
		}
	},


	/*
		"Private" helpers that aren't private at all
	 */
	// Check to close popup or not
	// "target" is an element that was clicked
	_checkIfClose: function(target) {

		if($(target).hasClass(PREVENT_CLOSE_CLASS)) {
			return;
		}

		var closeOnContent = mfp.st.closeOnContentClick;
		var closeOnBg = mfp.st.closeOnBgClick;

		if(closeOnContent && closeOnBg) {
			return true;
		} else {

			// We close the popup if click is on close button or on preloader. Or if there is no content.
			if(!mfp.content || $(target).hasClass('mfp-close') || (mfp.preloader && target === mfp.preloader[0]) ) {
				return true;
			}

			// if click is outside the content
			if(  (target !== mfp.content[0] && !$.contains(mfp.content[0], target))  ) {
				if(closeOnBg) {
					// last check, if the clicked element is in DOM, (in case it's removed onclick)
					if( $.contains(document, target) ) {
						return true;
					}
				}
			} else if(closeOnContent) {
				return true;
			}

		}
		return false;
	},
	_addClassToMFP: function(cName) {
		mfp.bgOverlay.addClass(cName);
		mfp.wrap.addClass(cName);
	},
	_removeClassFromMFP: function(cName) {
		this.bgOverlay.removeClass(cName);
		mfp.wrap.removeClass(cName);
	},
	_hasScrollBar: function(winHeight) {
		return (  (mfp.isIE7 ? _document.height() : document.body.scrollHeight) > (winHeight || _window.height()) );
	},
	_setFocus: function() {
		(mfp.st.focus ? mfp.content.find(mfp.st.focus).eq(0) : mfp.wrap).focus();
	},
	_onFocusIn: function(e) {
		if( e.target !== mfp.wrap[0] && !$.contains(mfp.wrap[0], e.target) ) {
			mfp._setFocus();
			return false;
		}
	},
	_parseMarkup: function(template, values, item) {
		var arr;
		if(item.data) {
			values = $.extend(item.data, values);
		}
		_mfpTrigger(MARKUP_PARSE_EVENT, [template, values, item] );

		$.each(values, function(key, value) {
			if(value === undefined || value === false) {
				return true;
			}
			arr = key.split('_');
			if(arr.length > 1) {
				var el = template.find(EVENT_NS + '-'+arr[0]);

				if(el.length > 0) {
					var attr = arr[1];
					if(attr === 'replaceWith') {
						if(el[0] !== value[0]) {
							el.replaceWith(value);
						}
					} else if(attr === 'img') {
						if(el.is('img')) {
							el.attr('src', value);
						} else {
							el.replaceWith( '<img src="'+value+'" class="' + el.attr('class') + '" />' );
						}
					} else {
						el.attr(arr[1], value);
					}
				}

			} else {
				template.find(EVENT_NS + '-'+key).html(value);
			}
		});
	},

	_getScrollbarSize: function() {
		// thx David
		if(mfp.scrollbarSize === undefined) {
			var scrollDiv = document.createElement("div");
			scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
			document.body.appendChild(scrollDiv);
			mfp.scrollbarSize = scrollDiv.offsetWidth - scrollDiv.clientWidth;
			document.body.removeChild(scrollDiv);
		}
		return mfp.scrollbarSize;
	}

}; /* MagnificPopup core prototype end */




/**
 * Public static functions
 */
$.magnificPopup = {
	instance: null,
	proto: MagnificPopup.prototype,
	modules: [],

	open: function(options, index) {
		_checkInstance();	

		if(!options) {
			options = {};
		} else {
			options = $.extend(true, {}, options);
		}
			

		options.isObj = true;
		options.index = index || 0;
		return this.instance.open(options);
	},

	close: function() {
		return $.magnificPopup.instance && $.magnificPopup.instance.close();
	},

	registerModule: function(name, module) {
		if(module.options) {
			$.magnificPopup.defaults[name] = module.options;
		}
		$.extend(this.proto, module.proto);			
		this.modules.push(name);
	},

	defaults: {   

		// Info about options is in docs:
		// http://dimsemenov.com/plugins/magnific-popup/documentation.html#options
		
		disableOn: 0,	

		key: null,

		midClick: false,

		mainClass: '',

		preloader: true,

		focus: '', // CSS selector of input to focus after popup is opened
		
		closeOnContentClick: false,

		closeOnBgClick: true,

		closeBtnInside: true, 

		showCloseBtn: true,

		enableEscapeKey: true,

		modal: false,

		alignTop: false,
	
		removalDelay: 0,

		prependTo: null,
		
		fixedContentPos: 'auto', 
	
		fixedBgPos: 'auto',

		overflowY: 'auto',

		closeMarkup: '<button title="%title%" type="button" class="mfp-close">&times;</button>',

		tClose: 'Close (Esc)',

		tLoading: 'Loading...'

	}
};



$.fn.magnificPopup = function(options) {
	_checkInstance();

	var jqEl = $(this);

	// We call some API method of first param is a string
	if (typeof options === "string" ) {

		if(options === 'open') {
			var items,
				itemOpts = _isJQ ? jqEl.data('magnificPopup') : jqEl[0].magnificPopup,
				index = parseInt(arguments[1], 10) || 0;

			if(itemOpts.items) {
				items = itemOpts.items[index];
			} else {
				items = jqEl;
				if(itemOpts.delegate) {
					items = items.find(itemOpts.delegate);
				}
				items = items.eq( index );
			}
			mfp._openClick({mfpEl:items}, jqEl, itemOpts);
		} else {
			if(mfp.isOpen)
				mfp[options].apply(mfp, Array.prototype.slice.call(arguments, 1));
		}

	} else {
		// clone options obj
		options = $.extend(true, {}, options);
		
		/*
		 * As Zepto doesn't support .data() method for objects 
		 * and it works only in normal browsers
		 * we assign "options" object directly to the DOM element. FTW!
		 */
		if(_isJQ) {
			jqEl.data('magnificPopup', options);
		} else {
			jqEl[0].magnificPopup = options;
		}

		mfp.addGroup(jqEl, options);

	}
	return jqEl;
};


//Quick benchmark
/*
var start = performance.now(),
	i,
	rounds = 1000;

for(i = 0; i < rounds; i++) {

}
console.log('Test #1:', performance.now() - start);

start = performance.now();
for(i = 0; i < rounds; i++) {

}
console.log('Test #2:', performance.now() - start);
*/


/*>>core*/

/*>>inline*/

var INLINE_NS = 'inline',
	_hiddenClass,
	_inlinePlaceholder, 
	_lastInlineElement,
	_putInlineElementsBack = function() {
		if(_lastInlineElement) {
			_inlinePlaceholder.after( _lastInlineElement.addClass(_hiddenClass) ).detach();
			_lastInlineElement = null;
		}
	};

$.magnificPopup.registerModule(INLINE_NS, {
	options: {
		hiddenClass: 'hide', // will be appended with `mfp-` prefix
		markup: '',
		tNotFound: 'Content not found'
	},
	proto: {

		initInline: function() {
			mfp.types.push(INLINE_NS);

			_mfpOn(CLOSE_EVENT+'.'+INLINE_NS, function() {
				_putInlineElementsBack();
			});
		},

		getInline: function(item, template) {

			_putInlineElementsBack();

			if(item.src) {
				var inlineSt = mfp.st.inline,
					el = $(item.src);

				if(el.length) {

					// If target element has parent - we replace it with placeholder and put it back after popup is closed
					var parent = el[0].parentNode;
					if(parent && parent.tagName) {
						if(!_inlinePlaceholder) {
							_hiddenClass = inlineSt.hiddenClass;
							_inlinePlaceholder = _getEl(_hiddenClass);
							_hiddenClass = 'mfp-'+_hiddenClass;
						}
						// replace target inline element with placeholder
						_lastInlineElement = el.after(_inlinePlaceholder).detach().removeClass(_hiddenClass);
					}

					mfp.updateStatus('ready');
				} else {
					mfp.updateStatus('error', inlineSt.tNotFound);
					el = $('<div>');
				}

				item.inlineElement = el;
				return el;
			}

			mfp.updateStatus('ready');
			mfp._parseMarkup(template, {}, item);
			return template;
		}
	}
});

/*>>inline*/

/*>>ajax*/
var AJAX_NS = 'ajax',
	_ajaxCur,
	_removeAjaxCursor = function() {
		if(_ajaxCur) {
			$(document.body).removeClass(_ajaxCur);
		}
	},
	_destroyAjaxRequest = function() {
		_removeAjaxCursor();
		if(mfp.req) {
			mfp.req.abort();
		}
	};

$.magnificPopup.registerModule(AJAX_NS, {

	options: {
		settings: null,
		cursor: 'mfp-ajax-cur',
		tError: '<a href="%url%">The content</a> could not be loaded.'
	},

	proto: {
		initAjax: function() {
			mfp.types.push(AJAX_NS);
			_ajaxCur = mfp.st.ajax.cursor;

			_mfpOn(CLOSE_EVENT+'.'+AJAX_NS, _destroyAjaxRequest);
			_mfpOn('BeforeChange.' + AJAX_NS, _destroyAjaxRequest);
		},
		getAjax: function(item) {

			if(_ajaxCur) {
				$(document.body).addClass(_ajaxCur);
			}

			mfp.updateStatus('loading');

			var opts = $.extend({
				url: item.src,
				success: function(data, textStatus, jqXHR) {
					var temp = {
						data:data,
						xhr:jqXHR
					};

					_mfpTrigger('ParseAjax', temp);

					mfp.appendContent( $(temp.data), AJAX_NS );

					item.finished = true;

					_removeAjaxCursor();

					mfp._setFocus();

					setTimeout(function() {
						mfp.wrap.addClass(READY_CLASS);
					}, 16);

					mfp.updateStatus('ready');

					_mfpTrigger('AjaxContentAdded');
				},
				error: function() {
					_removeAjaxCursor();
					item.finished = item.loadError = true;
					mfp.updateStatus('error', mfp.st.ajax.tError.replace('%url%', item.src));
				}
			}, mfp.st.ajax.settings);

			mfp.req = $.ajax(opts);

			return '';
		}
	}
});





	

/*>>ajax*/

/*>>image*/
var _imgInterval,
	_getTitle = function(item) {
		if(item.data && item.data.title !== undefined) 
			return item.data.title;

		var src = mfp.st.image.titleSrc;

		if(src) {
			if($.isFunction(src)) {
				return src.call(mfp, item);
			} else if(item.el) {
				return item.el.attr(src) || '';
			}
		}
		return '';
	};

$.magnificPopup.registerModule('image', {

	options: {
		markup: '<div class="mfp-figure">'+
					'<div class="mfp-close"></div>'+
					'<figure>'+
						'<div class="mfp-img"></div>'+
						'<figcaption>'+
							'<div class="mfp-bottom-bar">'+
								'<div class="mfp-title"></div>'+
								'<div class="mfp-counter"></div>'+
							'</div>'+
						'</figcaption>'+
					'</figure>'+
				'</div>',
		cursor: 'mfp-zoom-out-cur',
		titleSrc: 'title', 
		verticalFit: true,
		tError: '<a href="%url%">The image</a> could not be loaded.'
	},

	proto: {
		initImage: function() {
			var imgSt = mfp.st.image,
				ns = '.image';

			mfp.types.push('image');

			_mfpOn(OPEN_EVENT+ns, function() {
				if(mfp.currItem.type === 'image' && imgSt.cursor) {
					$(document.body).addClass(imgSt.cursor);
				}
			});

			_mfpOn(CLOSE_EVENT+ns, function() {
				if(imgSt.cursor) {
					$(document.body).removeClass(imgSt.cursor);
				}
				_window.off('resize' + EVENT_NS);
			});

			_mfpOn('Resize'+ns, mfp.resizeImage);
			if(mfp.isLowIE) {
				_mfpOn('AfterChange', mfp.resizeImage);
			}
		},
		resizeImage: function() {
			var item = mfp.currItem;
			if(!item || !item.img) return;

			if(mfp.st.image.verticalFit) {
				var decr = 0;
				// fix box-sizing in ie7/8
				if(mfp.isLowIE) {
					decr = parseInt(item.img.css('padding-top'), 10) + parseInt(item.img.css('padding-bottom'),10);
				}
				item.img.css('max-height', mfp.wH-decr);
			}
		},
		_onImageHasSize: function(item) {
			if(item.img) {
				
				item.hasSize = true;

				if(_imgInterval) {
					clearInterval(_imgInterval);
				}
				
				item.isCheckingImgSize = false;

				_mfpTrigger('ImageHasSize', item);

				if(item.imgHidden) {
					if(mfp.content)
						mfp.content.removeClass('mfp-loading');
					
					item.imgHidden = false;
				}

			}
		},

		/**
		 * Function that loops until the image has size to display elements that rely on it asap
		 */
		findImageSize: function(item) {

			var counter = 0,
				img = item.img[0],
				mfpSetInterval = function(delay) {

					if(_imgInterval) {
						clearInterval(_imgInterval);
					}
					// decelerating interval that checks for size of an image
					_imgInterval = setInterval(function() {
						if(img.naturalWidth > 0) {
							mfp._onImageHasSize(item);
							return;
						}

						if(counter > 200) {
							clearInterval(_imgInterval);
						}

						counter++;
						if(counter === 3) {
							mfpSetInterval(10);
						} else if(counter === 40) {
							mfpSetInterval(50);
						} else if(counter === 100) {
							mfpSetInterval(500);
						}
					}, delay);
				};

			mfpSetInterval(1);
		},

		getImage: function(item, template) {

			var guard = 0,

				// image load complete handler
				onLoadComplete = function() {
					if(item) {
						if (item.img[0].complete) {
							item.img.off('.mfploader');
							
							if(item === mfp.currItem){
								mfp._onImageHasSize(item);

								mfp.updateStatus('ready');
							}

							item.hasSize = true;
							item.loaded = true;

							_mfpTrigger('ImageLoadComplete');
							
						}
						else {
							// if image complete check fails 200 times (20 sec), we assume that there was an error.
							guard++;
							if(guard < 200) {
								setTimeout(onLoadComplete,100);
							} else {
								onLoadError();
							}
						}
					}
				},

				// image error handler
				onLoadError = function() {
					if(item) {
						item.img.off('.mfploader');
						if(item === mfp.currItem){
							mfp._onImageHasSize(item);
							mfp.updateStatus('error', imgSt.tError.replace('%url%', item.src) );
						}

						item.hasSize = true;
						item.loaded = true;
						item.loadError = true;
					}
				},
				imgSt = mfp.st.image;


			var el = template.find('.mfp-img');
			if(el.length) {
				var img = document.createElement('img');
				img.className = 'mfp-img';
				if(item.el && item.el.find('img').length) {
					img.alt = item.el.find('img').attr('alt');
				}
				item.img = $(img).on('load.mfploader', onLoadComplete).on('error.mfploader', onLoadError);
				img.src = item.src;

				// without clone() "error" event is not firing when IMG is replaced by new IMG
				// TODO: find a way to avoid such cloning
				if(el.is('img')) {
					item.img = item.img.clone();
				}

				img = item.img[0];
				if(img.naturalWidth > 0) {
					item.hasSize = true;
				} else if(!img.width) {										
					item.hasSize = false;
				}
			}

			mfp._parseMarkup(template, {
				title: _getTitle(item),
				img_replaceWith: item.img
			}, item);

			mfp.resizeImage();

			if(item.hasSize) {
				if(_imgInterval) clearInterval(_imgInterval);

				if(item.loadError) {
					template.addClass('mfp-loading');
					mfp.updateStatus('error', imgSt.tError.replace('%url%', item.src) );
				} else {
					template.removeClass('mfp-loading');
					mfp.updateStatus('ready');
				}
				return template;
			}

			mfp.updateStatus('loading');
			item.loading = true;

			if(!item.hasSize) {
				item.imgHidden = true;
				template.addClass('mfp-loading');
				mfp.findImageSize(item);
			} 

			return template;
		}
	}
});



/*>>image*/

/*>>zoom*/
var hasMozTransform,
	getHasMozTransform = function() {
		if(hasMozTransform === undefined) {
			hasMozTransform = document.createElement('p').style.MozTransform !== undefined;
		}
		return hasMozTransform;		
	};

$.magnificPopup.registerModule('zoom', {

	options: {
		enabled: false,
		easing: 'ease-in-out',
		duration: 300,
		opener: function(element) {
			return element.is('img') ? element : element.find('img');
		}
	},

	proto: {

		initZoom: function() {
			var zoomSt = mfp.st.zoom,
				ns = '.zoom',
				image;
				
			if(!zoomSt.enabled || !mfp.supportsTransition) {
				return;
			}

			var duration = zoomSt.duration,
				getElToAnimate = function(image) {
					var newImg = image.clone().removeAttr('style').removeAttr('class').addClass('mfp-animated-image'),
						transition = 'all '+(zoomSt.duration/1000)+'s ' + zoomSt.easing,
						cssObj = {
							position: 'fixed',
							zIndex: 9999,
							left: 0,
							top: 0,
							'-webkit-backface-visibility': 'hidden'
						},
						t = 'transition';

					cssObj['-webkit-'+t] = cssObj['-moz-'+t] = cssObj['-o-'+t] = cssObj[t] = transition;

					newImg.css(cssObj);
					return newImg;
				},
				showMainContent = function() {
					mfp.content.css('visibility', 'visible');
				},
				openTimeout,
				animatedImg;

			_mfpOn('BuildControls'+ns, function() {
				if(mfp._allowZoom()) {

					clearTimeout(openTimeout);
					mfp.content.css('visibility', 'hidden');

					// Basically, all code below does is clones existing image, puts in on top of the current one and animated it
					
					image = mfp._getItemToZoom();

					if(!image) {
						showMainContent();
						return;
					}

					animatedImg = getElToAnimate(image); 
					
					animatedImg.css( mfp._getOffset() );

					mfp.wrap.append(animatedImg);

					openTimeout = setTimeout(function() {
						animatedImg.css( mfp._getOffset( true ) );
						openTimeout = setTimeout(function() {

							showMainContent();

							setTimeout(function() {
								animatedImg.remove();
								image = animatedImg = null;
								_mfpTrigger('ZoomAnimationEnded');
							}, 16); // avoid blink when switching images 

						}, duration); // this timeout equals animation duration

					}, 16); // by adding this timeout we avoid short glitch at the beginning of animation


					// Lots of timeouts...
				}
			});
			_mfpOn(BEFORE_CLOSE_EVENT+ns, function() {
				if(mfp._allowZoom()) {

					clearTimeout(openTimeout);

					mfp.st.removalDelay = duration;

					if(!image) {
						image = mfp._getItemToZoom();
						if(!image) {
							return;
						}
						animatedImg = getElToAnimate(image);
					}
					
					
					animatedImg.css( mfp._getOffset(true) );
					mfp.wrap.append(animatedImg);
					mfp.content.css('visibility', 'hidden');
					
					setTimeout(function() {
						animatedImg.css( mfp._getOffset() );
					}, 16);
				}

			});

			_mfpOn(CLOSE_EVENT+ns, function() {
				if(mfp._allowZoom()) {
					showMainContent();
					if(animatedImg) {
						animatedImg.remove();
					}
					image = null;
				}	
			});
		},

		_allowZoom: function() {
			return mfp.currItem.type === 'image';
		},

		_getItemToZoom: function() {
			if(mfp.currItem.hasSize) {
				return mfp.currItem.img;
			} else {
				return false;
			}
		},

		// Get element postion relative to viewport
		_getOffset: function(isLarge) {
			var el;
			if(isLarge) {
				el = mfp.currItem.img;
			} else {
				el = mfp.st.zoom.opener(mfp.currItem.el || mfp.currItem);
			}

			var offset = el.offset();
			var paddingTop = parseInt(el.css('padding-top'),10);
			var paddingBottom = parseInt(el.css('padding-bottom'),10);
			offset.top -= ( $(window).scrollTop() - paddingTop );


			/*
			
			Animating left + top + width/height looks glitchy in Firefox, but perfect in Chrome. And vice-versa.

			 */
			var obj = {
				width: el.width(),
				// fix Zepto height+padding issue
				height: (_isJQ ? el.innerHeight() : el[0].offsetHeight) - paddingBottom - paddingTop
			};

			// I hate to do this, but there is no another option
			if( getHasMozTransform() ) {
				obj['-moz-transform'] = obj['transform'] = 'translate(' + offset.left + 'px,' + offset.top + 'px)';
			} else {
				obj.left = offset.left;
				obj.top = offset.top;
			}
			return obj;
		}

	}
});



/*>>zoom*/

/*>>iframe*/

var IFRAME_NS = 'iframe',
	_emptyPage = '//about:blank',
	
	_fixIframeBugs = function(isShowing) {
		if(mfp.currTemplate[IFRAME_NS]) {
			var el = mfp.currTemplate[IFRAME_NS].find('iframe');
			if(el.length) { 
				// reset src after the popup is closed to avoid "video keeps playing after popup is closed" bug
				if(!isShowing) {
					el[0].src = _emptyPage;
				}

				// IE8 black screen bug fix
				if(mfp.isIE8) {
					el.css('display', isShowing ? 'block' : 'none');
				}
			}
		}
	};

$.magnificPopup.registerModule(IFRAME_NS, {

	options: {
		markup: '<div class="mfp-iframe-scaler">'+
					'<div class="mfp-close"></div>'+
					'<iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe>'+
				'</div>',

		srcAction: 'iframe_src',

		// we don't care and support only one default type of URL by default
		patterns: {
			youtube: {
				index: 'youtube.com', 
				id: 'v=', 
				src: '//www.youtube.com/embed/%id%?autoplay=1'
			},
			vimeo: {
				index: 'vimeo.com/',
				id: '/',
				src: '//player.vimeo.com/video/%id%?autoplay=1'
			},
			gmaps: {
				index: '//maps.google.',
				src: '%id%&output=embed'
			}
		}
	},

	proto: {
		initIframe: function() {
			mfp.types.push(IFRAME_NS);

			_mfpOn('BeforeChange', function(e, prevType, newType) {
				if(prevType !== newType) {
					if(prevType === IFRAME_NS) {
						_fixIframeBugs(); // iframe if removed
					} else if(newType === IFRAME_NS) {
						_fixIframeBugs(true); // iframe is showing
					} 
				}// else {
					// iframe source is switched, don't do anything
				//}
			});

			_mfpOn(CLOSE_EVENT + '.' + IFRAME_NS, function() {
				_fixIframeBugs();
			});
		},

		getIframe: function(item, template) {
			var embedSrc = item.src;
			var iframeSt = mfp.st.iframe;
				
			$.each(iframeSt.patterns, function() {
				if(embedSrc.indexOf( this.index ) > -1) {
					if(this.id) {
						if(typeof this.id === 'string') {
							embedSrc = embedSrc.substr(embedSrc.lastIndexOf(this.id)+this.id.length, embedSrc.length);
						} else {
							embedSrc = this.id.call( this, embedSrc );
						}
					}
					embedSrc = this.src.replace('%id%', embedSrc );
					return false; // break;
				}
			});
			
			var dataObj = {};
			if(iframeSt.srcAction) {
				dataObj[iframeSt.srcAction] = embedSrc;
			}
			mfp._parseMarkup(template, dataObj, item);

			mfp.updateStatus('ready');

			return template;
		}
	}
});



/*>>iframe*/

/*>>gallery*/
/**
 * Get looped index depending on number of slides
 */
var _getLoopedId = function(index) {
		var numSlides = mfp.items.length;
		if(index > numSlides - 1) {
			return index - numSlides;
		} else  if(index < 0) {
			return numSlides + index;
		}
		return index;
	},
	_replaceCurrTotal = function(text, curr, total) {
		return text.replace(/%curr%/gi, curr + 1).replace(/%total%/gi, total);
	};

$.magnificPopup.registerModule('gallery', {

	options: {
		enabled: false,
		arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',
		preload: [0,2],
		navigateByImgClick: true,
		arrows: true,

		tPrev: 'Previous (Left arrow key)',
		tNext: 'Next (Right arrow key)',
		tCounter: '%curr% of %total%'
	},

	proto: {
		initGallery: function() {

			var gSt = mfp.st.gallery,
				ns = '.mfp-gallery',
				supportsFastClick = Boolean($.fn.mfpFastClick);

			mfp.direction = true; // true - next, false - prev
			
			if(!gSt || !gSt.enabled ) return false;

			_wrapClasses += ' mfp-gallery';

			_mfpOn(OPEN_EVENT+ns, function() {

				if(gSt.navigateByImgClick) {
					mfp.wrap.on('click'+ns, '.mfp-img', function() {
						if(mfp.items.length > 1) {
							mfp.next();
							return false;
						}
					});
				}

				_document.on('keydown'+ns, function(e) {
					if (e.keyCode === 37) {
						mfp.prev();
					} else if (e.keyCode === 39) {
						mfp.next();
					}
				});
			});

			_mfpOn('UpdateStatus'+ns, function(e, data) {
				if(data.text) {
					data.text = _replaceCurrTotal(data.text, mfp.currItem.index, mfp.items.length);
				}
			});

			_mfpOn(MARKUP_PARSE_EVENT+ns, function(e, element, values, item) {
				var l = mfp.items.length;
				values.counter = l > 1 ? _replaceCurrTotal(gSt.tCounter, item.index, l) : '';
			});

			_mfpOn('BuildControls' + ns, function() {
				if(mfp.items.length > 1 && gSt.arrows && !mfp.arrowLeft) {
					var markup = gSt.arrowMarkup,
						arrowLeft = mfp.arrowLeft = $( markup.replace(/%title%/gi, gSt.tPrev).replace(/%dir%/gi, 'left') ).addClass(PREVENT_CLOSE_CLASS),			
						arrowRight = mfp.arrowRight = $( markup.replace(/%title%/gi, gSt.tNext).replace(/%dir%/gi, 'right') ).addClass(PREVENT_CLOSE_CLASS);

					var eName = supportsFastClick ? 'mfpFastClick' : 'click';
					arrowLeft[eName](function() {
						mfp.prev();
					});			
					arrowRight[eName](function() {
						mfp.next();
					});	

					// Polyfill for :before and :after (adds elements with classes mfp-a and mfp-b)
					if(mfp.isIE7) {
						_getEl('b', arrowLeft[0], false, true);
						_getEl('a', arrowLeft[0], false, true);
						_getEl('b', arrowRight[0], false, true);
						_getEl('a', arrowRight[0], false, true);
					}

					mfp.container.append(arrowLeft.add(arrowRight));
				}
			});

			_mfpOn(CHANGE_EVENT+ns, function() {
				if(mfp._preloadTimeout) clearTimeout(mfp._preloadTimeout);

				mfp._preloadTimeout = setTimeout(function() {
					mfp.preloadNearbyImages();
					mfp._preloadTimeout = null;
				}, 16);		
			});


			_mfpOn(CLOSE_EVENT+ns, function() {
				_document.off(ns);
				mfp.wrap.off('click'+ns);
			
				if(mfp.arrowLeft && supportsFastClick) {
					mfp.arrowLeft.add(mfp.arrowRight).destroyMfpFastClick();
				}
				mfp.arrowRight = mfp.arrowLeft = null;
			});

		}, 
		next: function() {
			mfp.direction = true;
			mfp.index = _getLoopedId(mfp.index + 1);
			mfp.updateItemHTML();
		},
		prev: function() {
			mfp.direction = false;
			mfp.index = _getLoopedId(mfp.index - 1);
			mfp.updateItemHTML();
		},
		goTo: function(newIndex) {
			mfp.direction = (newIndex >= mfp.index);
			mfp.index = newIndex;
			mfp.updateItemHTML();
		},
		preloadNearbyImages: function() {
			var p = mfp.st.gallery.preload,
				preloadBefore = Math.min(p[0], mfp.items.length),
				preloadAfter = Math.min(p[1], mfp.items.length),
				i;

			for(i = 1; i <= (mfp.direction ? preloadAfter : preloadBefore); i++) {
				mfp._preloadItem(mfp.index+i);
			}
			for(i = 1; i <= (mfp.direction ? preloadBefore : preloadAfter); i++) {
				mfp._preloadItem(mfp.index-i);
			}
		},
		_preloadItem: function(index) {
			index = _getLoopedId(index);

			if(mfp.items[index].preloaded) {
				return;
			}

			var item = mfp.items[index];
			if(!item.parsed) {
				item = mfp.parseEl( index );
			}

			_mfpTrigger('LazyLoad', item);

			if(item.type === 'image') {
				item.img = $('<img class="mfp-img" />').on('load.mfploader', function() {
					item.hasSize = true;
				}).on('error.mfploader', function() {
					item.hasSize = true;
					item.loadError = true;
					_mfpTrigger('LazyLoadError', item);
				}).attr('src', item.src);
			}


			item.preloaded = true;
		}
	}
});

/*
Touch Support that might be implemented some day

addSwipeGesture: function() {
	var startX,
		moved,
		multipleTouches;

		return;

	var namespace = '.mfp',
		addEventNames = function(pref, down, move, up, cancel) {
			mfp._tStart = pref + down + namespace;
			mfp._tMove = pref + move + namespace;
			mfp._tEnd = pref + up + namespace;
			mfp._tCancel = pref + cancel + namespace;
		};

	if(window.navigator.msPointerEnabled) {
		addEventNames('MSPointer', 'Down', 'Move', 'Up', 'Cancel');
	} else if('ontouchstart' in window) {
		addEventNames('touch', 'start', 'move', 'end', 'cancel');
	} else {
		return;
	}
	_window.on(mfp._tStart, function(e) {
		var oE = e.originalEvent;
		multipleTouches = moved = false;
		startX = oE.pageX || oE.changedTouches[0].pageX;
	}).on(mfp._tMove, function(e) {
		if(e.originalEvent.touches.length > 1) {
			multipleTouches = e.originalEvent.touches.length;
		} else {
			//e.preventDefault();
			moved = true;
		}
	}).on(mfp._tEnd + ' ' + mfp._tCancel, function(e) {
		if(moved && !multipleTouches) {
			var oE = e.originalEvent,
				diff = startX - (oE.pageX || oE.changedTouches[0].pageX);

			if(diff > 20) {
				mfp.next();
			} else if(diff < -20) {
				mfp.prev();
			}
		}
	});
},
*/


/*>>gallery*/

/*>>retina*/

var RETINA_NS = 'retina';

$.magnificPopup.registerModule(RETINA_NS, {
	options: {
		replaceSrc: function(item) {
			return item.src.replace(/\.\w+$/, function(m) { return '@2x' + m; });
		},
		ratio: 1 // Function or number.  Set to 1 to disable.
	},
	proto: {
		initRetina: function() {
			if(window.devicePixelRatio > 1) {

				var st = mfp.st.retina,
					ratio = st.ratio;

				ratio = !isNaN(ratio) ? ratio : ratio();

				if(ratio > 1) {
					_mfpOn('ImageHasSize' + '.' + RETINA_NS, function(e, item) {
						item.img.css({
							'max-width': item.img[0].naturalWidth / ratio,
							'width': '100%'
						});
					});
					_mfpOn('ElementParse' + '.' + RETINA_NS, function(e, item) {
						item.src = st.replaceSrc(item, ratio);
					});
				}
			}

		}
	}
});

/*>>retina*/

/*>>fastclick*/
/**
 * FastClick event implementation. (removes 300ms delay on touch devices)
 * Based on https://developers.google.com/mobile/articles/fast_buttons
 *
 * You may use it outside the Magnific Popup by calling just:
 *
 * $('.your-el').mfpFastClick(function() {
 *     console.log('Clicked!');
 * });
 *
 * To unbind:
 * $('.your-el').destroyMfpFastClick();
 * 
 * 
 * Note that it's a very basic and simple implementation, it blocks ghost click on the same element where it was bound.
 * If you need something more advanced, use plugin by FT Labs https://github.com/ftlabs/fastclick
 * 
 */

(function() {
	var ghostClickDelay = 1000,
		supportsTouch = 'ontouchstart' in window,
		unbindTouchMove = function() {
			_window.off('touchmove'+ns+' touchend'+ns);
		},
		eName = 'mfpFastClick',
		ns = '.'+eName;


	// As Zepto.js doesn't have an easy way to add custom events (like jQuery), so we implement it in this way
	$.fn.mfpFastClick = function(callback) {

		return $(this).each(function() {

			var elem = $(this),
				lock;

			if( supportsTouch ) {

				var timeout,
					startX,
					startY,
					pointerMoved,
					point,
					numPointers;

				elem.on('touchstart' + ns, function(e) {
					pointerMoved = false;
					numPointers = 1;

					point = e.originalEvent ? e.originalEvent.touches[0] : e.touches[0];
					startX = point.clientX;
					startY = point.clientY;

					_window.on('touchmove'+ns, function(e) {
						point = e.originalEvent ? e.originalEvent.touches : e.touches;
						numPointers = point.length;
						point = point[0];
						if (Math.abs(point.clientX - startX) > 10 ||
							Math.abs(point.clientY - startY) > 10) {
							pointerMoved = true;
							unbindTouchMove();
						}
					}).on('touchend'+ns, function(e) {
						unbindTouchMove();
						if(pointerMoved || numPointers > 1) {
							return;
						}
						lock = true;
						e.preventDefault();
						clearTimeout(timeout);
						timeout = setTimeout(function() {
							lock = false;
						}, ghostClickDelay);
						callback();
					});
				});

			}

			elem.on('click' + ns, function() {
				if(!lock) {
					callback();
				}
			});
		});
	};

	$.fn.destroyMfpFastClick = function() {
		$(this).off('touchstart' + ns + ' click' + ns);
		if(supportsTouch) _window.off('touchmove'+ns+' touchend'+ns);
	};
})();

/*>>fastclick*/
 _checkInstance(); }));
/*
 *
 * TERMS OF USE - EASING EQUATIONS
 * 
 * Open source under the BSD License. 
 * 
 * Copyright © 2001 Robert Penner
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
 */

 (function( $ ){
    $(function() {
        if ( $(window).width() <= 1024 || ( ! $('body').hasClass('elementor-editor-active') && 0 === $('.xts-parallax-on-scroll').length ) ) return;
        ParallaxScroll.init();
    });

    var ParallaxScroll = {
        /* PUBLIC VARIABLES */
        showLogs: false,
        round: 1000,

        /* PUBLIC FUNCTIONS */
        init: function() {
            this._log("init");
            if (this._inited) {
                this._log("Already Inited");
                this._inited = true;
                return;
            }
            this._requestAnimationFrame = (function(){
              return  window.requestAnimationFrame       || 
                      window.webkitRequestAnimationFrame || 
                      window.mozRequestAnimationFrame    || 
                      window.oRequestAnimationFrame      || 
                      window.msRequestAnimationFrame     || 
                      function(/* function */ callback, /* DOMElement */ element){
                          window.setTimeout(callback, 1000 / 60);
                      };
            })();
            this._onScroll(true);
        },

        /* PRIVATE VARIABLES */
        _inited: false,
        _properties: ['x', 'y', 'z', 'rotateX', 'rotateY', 'rotateZ', 'scaleX', 'scaleY', 'scaleZ', 'scale'],
        _requestAnimationFrame:null,

        /* PRIVATE FUNCTIONS */
        _log: function(message) {
            if (this.showLogs) console.log("Parallax Scroll / " + message);
        },
        _onScroll: function(noSmooth) {
            var scroll = $(document).scrollTop();
            var windowHeight = $(window).height();
            this._log("onScroll " + scroll);
			$('.xts-parallax-on-scroll').each($.proxy(function(index, el) {
                var $el = $(el);
                var properties = [];
                var applyProperties = false;
                var style = $el.data("style");
                if (style == undefined) {
                    style = $el.attr("style") || "";
                    $el.data("style", style);
				}
				var classes = $el.attr('class').split(' ');
				var datas = [[]];
				for (var index = 0; index < classes.length; index++) {
					if (classes[index].indexOf('xts_scroll') >= 0) {
						var data = classes[index].split('_');
						datas[0][data[2]] = data[3]
					}
				}
                var iData;
                for(iData = 2; ; iData++) {
                    if($el.data("parallax"+iData)) {
                        datas.push($el.data("parallax-"+iData));
                    }
                    else {
                        break;
                    }
                }
                var datasLength = datas.length;
                for(iData = 0; iData < datasLength; iData ++) {
                    var data = datas[iData];
                    var scrollFrom = data["from-scroll"];
                    if (scrollFrom == undefined) scrollFrom = Math.max(0, $(el).offset().top - windowHeight);
                    scrollFrom = scrollFrom | 0;
                    var scrollDistance = data["distance"];
                    var scrollTo = data["to-scroll"];
                    if (scrollDistance == undefined && scrollTo == undefined) scrollDistance = windowHeight;
                    scrollDistance = Math.max(scrollDistance | 0, 1);
                    var easing = data["easing"];
                    var easingReturn = data["easing-return"];
                    if (easing == undefined || !$.easing|| !$.easing[easing]) easing = null;
                    if (easingReturn == undefined || !$.easing|| !$.easing[easingReturn]) easingReturn = easing;
                    if (easing) {
                        var totalTime = data["duration"];
                        if (totalTime == undefined) totalTime = scrollDistance;
                        totalTime = Math.max(totalTime | 0, 1);
                        var totalTimeReturn = data["duration-return"];
                        if (totalTimeReturn == undefined) totalTimeReturn = totalTime;
                        scrollDistance = 1;
                        var currentTime = $el.data("current-time");
                        if(currentTime == undefined) currentTime = 0;
                    }
                    if (scrollTo == undefined) scrollTo = scrollFrom + scrollDistance;
                    scrollTo = scrollTo | 0;
                    var smoothness = data["smoothness"];
                    if (smoothness == undefined) smoothness = 30;
                    smoothness = smoothness | 0;
                    if (noSmooth || smoothness == 0) smoothness = 1;
                    smoothness = smoothness | 0;
                    var scrollCurrent = scroll;
                    scrollCurrent = Math.max(scrollCurrent, scrollFrom);
                    scrollCurrent = Math.min(scrollCurrent, scrollTo);
                    if(easing) {
                        if($el.data("sens") == undefined) $el.data("sens", "back");
                        if(scrollCurrent>scrollFrom) {
                            if($el.data("sens") == "back") {
                                currentTime = 1;
                                $el.data("sens", "go");
                            }
                            else {
                                currentTime++;
                            }
                        }
                        if(scrollCurrent<scrollTo) {
                            if($el.data("sens") == "go") {
                                currentTime = 1;
                                $el.data("sens", "back");
                            }
                            else {
                                currentTime++;
                            }
                        }
                        if(noSmooth) currentTime = totalTime;
                        $el.data("current-time", currentTime);
                    }
                    this._properties.map($.proxy(function(prop) {
                        var defaultProp = 0;
                        var to = data[prop];
                        if (to == undefined) return;
                        if(prop=="scale" || prop=="scaleX" || prop=="scaleY" || prop=="scaleZ" ) {
                            defaultProp = 1;
                        }
                        else {
                            to = to | 0;
						}
						
                        var prev = $el.data("_" + prop);
                        if (prev == undefined) prev = defaultProp;
                        var next = ((to-defaultProp) * ((scrollCurrent - scrollFrom) / (scrollTo - scrollFrom))) + defaultProp;
                        var val = prev + (next - prev) / smoothness;
                        if(easing && currentTime>0 && currentTime<=totalTime) {
                            var from = defaultProp;
                            if($el.data("sens") == "back") {
                                from = to;
                                to = -to;
                                easing = easingReturn;
                                totalTime = totalTimeReturn;
                            }
                            val = $.easing[easing](null, currentTime, from, to, totalTime);
                        }
                        val = Math.ceil(val * this.round) / this.round;
                        if(val==prev&&next==to) val = to;
                        if(!properties[prop]) properties[prop] = 0;
                        properties[prop] += val;
                        if (prev != properties[prop]) {
                            $el.data("_" + prop, properties[prop]);
                            applyProperties = true;
                        }
                    }, this));
				}
                if (applyProperties) {
					
                    if (properties["z"] != undefined) {
                        var perspective = data["perspective"];
                        if (perspective == undefined) perspective = 800;
                        var $parent = $el.parent();
                        if(!$parent.data("style")) $parent.data("style", $parent.attr("style") || "");
                        $parent.attr("style", "perspective:" + perspective + "px; -webkit-perspective:" + perspective + "px; "+ $parent.data("style"));
                    }
                    if(properties["scaleX"] == undefined) properties["scaleX"] = 1;
                    if(properties["scaleY"] == undefined) properties["scaleY"] = 1;
                    if(properties["scaleZ"] == undefined) properties["scaleZ"] = 1;
                    if (properties["scale"] != undefined) {
                        properties["scaleX"] *= properties["scale"];
                        properties["scaleY"] *= properties["scale"];
                        properties["scaleZ"] *= properties["scale"];
                    }
                    var translate3d = "translate3d(" + (properties["x"] ? properties["x"] : 0) + "px, " + (properties["y"] ? properties["y"] : 0) + "px, " + (properties["z"] ? properties["z"] : 0) + "px)";
                    var rotate3d = "rotateX(" + (properties["rotateX"] ? properties["rotateX"] : 0) + "deg) rotateY(" + (properties["rotateY"] ? properties["rotateY"] : 0) + "deg) rotateZ(" + (properties["rotateZ"] ? properties["rotateZ"] : 0) + "deg)";
                    var scale3d = "scaleX(" + properties["scaleX"] + ") scaleY(" + properties["scaleY"] + ") scaleZ(" + properties["scaleZ"] + ")";
                    var cssTransform = translate3d + " " + rotate3d + " " + scale3d + ";";
                    this._log(cssTransform);
                    $el.attr("style", "transform:" + cssTransform + " -webkit-transform:" + cssTransform + " " + style);
                }
            }, this));
            if(window.requestAnimationFrame) {
                window.requestAnimationFrame($.proxy(this._onScroll, this, false));
            }
            else {
                this._requestAnimationFrame($.proxy(this._onScroll, this, false));
            }
        }
    };
})(jQuery);
/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 * 
 * Open source under the BSD License. 
 * 
 * Copyright © 2008 George McGinley Smith
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
*/

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend(jQuery.easing,
    {
        def: 'easeOutQuad',
        swing: function (x, t, b, c, d) {
            //alert(jQuery.easing.default);
            return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
        },
        easeInQuad: function (x, t, b, c, d) {
            return c * (t /= d) * t + b;
        },
        easeOutQuad: function (x, t, b, c, d) {
            return -c * (t /= d) * (t - 2) + b;
        },
        easeInOutQuad: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) return c / 2 * t * t + b;
            return -c / 2 * ((--t) * (t - 2) - 1) + b;
        },
        easeInCubic: function (x, t, b, c, d) {
            return c * (t /= d) * t * t + b;
        },
        easeOutCubic: function (x, t, b, c, d) {
            return c * ((t = t / d - 1) * t * t + 1) + b;
        },
        easeInOutCubic: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) return c / 2 * t * t * t + b;
            return c / 2 * ((t -= 2) * t * t + 2) + b;
        },
        easeInQuart: function (x, t, b, c, d) {
            return c * (t /= d) * t * t * t + b;
        },
        easeOutQuart: function (x, t, b, c, d) {
            return -c * ((t = t / d - 1) * t * t * t - 1) + b;
        },
        easeInOutQuart: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) return c / 2 * t * t * t * t + b;
            return -c / 2 * ((t -= 2) * t * t * t - 2) + b;
        },
        easeInQuint: function (x, t, b, c, d) {
            return c * (t /= d) * t * t * t * t + b;
        },
        easeOutQuint: function (x, t, b, c, d) {
            return c * ((t = t / d - 1) * t * t * t * t + 1) + b;
        },
        easeInOutQuint: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) return c / 2 * t * t * t * t * t + b;
            return c / 2 * ((t -= 2) * t * t * t * t + 2) + b;
        },
        easeInSine: function (x, t, b, c, d) {
            return -c * Math.cos(t / d * (Math.PI / 2)) + c + b;
        },
        easeOutSine: function (x, t, b, c, d) {
            return c * Math.sin(t / d * (Math.PI / 2)) + b;
        },
        easeInOutSine: function (x, t, b, c, d) {
            return -c / 2 * (Math.cos(Math.PI * t / d) - 1) + b;
        },
        easeInExpo: function (x, t, b, c, d) {
            return (t == 0) ? b : c * Math.pow(2, 10 * (t / d - 1)) + b;
        },
        easeOutExpo: function (x, t, b, c, d) {
            return (t == d) ? b + c : c * (-Math.pow(2, -10 * t / d) + 1) + b;
        },
        easeInOutExpo: function (x, t, b, c, d) {
            if (t == 0) return b;
            if (t == d) return b + c;
            if ((t /= d / 2) < 1) return c / 2 * Math.pow(2, 10 * (t - 1)) + b;
            return c / 2 * (-Math.pow(2, -10 * --t) + 2) + b;
        },
        easeInCirc: function (x, t, b, c, d) {
            return -c * (Math.sqrt(1 - (t /= d) * t) - 1) + b;
        },
        easeOutCirc: function (x, t, b, c, d) {
            return c * Math.sqrt(1 - (t = t / d - 1) * t) + b;
        },
        easeInOutCirc: function (x, t, b, c, d) {
            if ((t /= d / 2) < 1) return -c / 2 * (Math.sqrt(1 - t * t) - 1) + b;
            return c / 2 * (Math.sqrt(1 - (t -= 2) * t) + 1) + b;
        },
        easeInElastic: function (x, t, b, c, d) {
            var s = 1.70158; var p = 0; var a = c;
            if (t == 0) return b; if ((t /= d) == 1) return b + c; if (!p) p = d * .3;
            if (a < Math.abs(c)) { a = c; var s = p / 4; }
            else var s = p / (2 * Math.PI) * Math.asin(c / a);
            return -(a * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p)) + b;
        },
        easeOutElastic: function (x, t, b, c, d) {
            var s = 1.70158; var p = 0; var a = c;
            if (t == 0) return b; if ((t /= d) == 1) return b + c; if (!p) p = d * .3;
            if (a < Math.abs(c)) { a = c; var s = p / 4; }
            else var s = p / (2 * Math.PI) * Math.asin(c / a);
            return a * Math.pow(2, -10 * t) * Math.sin((t * d - s) * (2 * Math.PI) / p) + c + b;
        },
        easeInOutElastic: function (x, t, b, c, d) {
            var s = 1.70158; var p = 0; var a = c;
            if (t == 0) return b; if ((t /= d / 2) == 2) return b + c; if (!p) p = d * (.3 * 1.5);
            if (a < Math.abs(c)) { a = c; var s = p / 4; }
            else var s = p / (2 * Math.PI) * Math.asin(c / a);
            if (t < 1) return -.5 * (a * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p)) + b;
            return a * Math.pow(2, -10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p) * .5 + c + b;
        },
        easeInBack: function (x, t, b, c, d, s) {
            if (s == undefined) s = 1.70158;
            return c * (t /= d) * t * ((s + 1) * t - s) + b;
        },
        easeOutBack: function (x, t, b, c, d, s) {
            if (s == undefined) s = 1.70158;
            return c * ((t = t / d - 1) * t * ((s + 1) * t + s) + 1) + b;
        },
        easeInOutBack: function (x, t, b, c, d, s) {
            if (s == undefined) s = 1.70158;
            if ((t /= d / 2) < 1) return c / 2 * (t * t * (((s *= (1.525)) + 1) * t - s)) + b;
            return c / 2 * ((t -= 2) * t * (((s *= (1.525)) + 1) * t + s) + 2) + b;
        },
        easeInBounce: function (x, t, b, c, d) {
            return c - jQuery.easing.easeOutBounce(x, d - t, 0, c, d) + b;
        },
        easeOutBounce: function (x, t, b, c, d) {
            if ((t /= d) < (1 / 2.75)) {
                return c * (7.5625 * t * t) + b;
            } else if (t < (2 / 2.75)) {
                return c * (7.5625 * (t -= (1.5 / 2.75)) * t + .75) + b;
            } else if (t < (2.5 / 2.75)) {
                return c * (7.5625 * (t -= (2.25 / 2.75)) * t + .9375) + b;
            } else {
                return c * (7.5625 * (t -= (2.625 / 2.75)) * t + .984375) + b;
            }
        },
        easeInOutBounce: function (x, t, b, c, d) {
            if (t < d / 2) return jQuery.easing.easeInBounce(x, t * 2, 0, c, d) * .5 + b;
            return jQuery.easing.easeOutBounce(x, t * 2 - d, 0, c, d) * .5 + c * .5 + b;
        }
    });

/*! PhotoSwipe - v4.1.2 - 2017-04-05
* http://photoswipe.com
* Copyright (c) 2017 Dmitry Semenov; */
(function (root, factory) { 
	if (typeof define === 'function' && define.amd) {
		define(factory);
	} else if (typeof exports === 'object') {
		module.exports = factory();
	} else {
		root.PhotoSwipe = factory();
	}
})(this, function () {

	'use strict';
	var PhotoSwipe = function(template, UiClass, items, options){

/*>>framework-bridge*/
/**
 *
 * Set of generic functions used by gallery.
 * 
 * You're free to modify anything here as long as functionality is kept.
 * 
 */
var framework = {
	features: null,
	bind: function(target, type, listener, unbind) {
		var methodName = (unbind ? 'remove' : 'add') + 'EventListener';
		type = type.split(' ');
		for(var i = 0; i < type.length; i++) {
			if(type[i]) {
				target[methodName]( type[i], listener, false);
			}
		}
	},
	isArray: function(obj) {
		return (obj instanceof Array);
	},
	createEl: function(classes, tag) {
		var el = document.createElement(tag || 'div');
		if(classes) {
			el.className = classes;
		}
		return el;
	},
	getScrollY: function() {
		var yOffset = window.pageYOffset;
		return yOffset !== undefined ? yOffset : document.documentElement.scrollTop;
	},
	unbind: function(target, type, listener) {
		framework.bind(target,type,listener,true);
	},
	removeClass: function(el, className) {
		var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
		el.className = el.className.replace(reg, ' ').replace(/^\s\s*/, '').replace(/\s\s*$/, ''); 
	},
	addClass: function(el, className) {
		if( !framework.hasClass(el,className) ) {
			el.className += (el.className ? ' ' : '') + className;
		}
	},
	hasClass: function(el, className) {
		return el.className && new RegExp('(^|\\s)' + className + '(\\s|$)').test(el.className);
	},
	getChildByClass: function(parentEl, childClassName) {
		var node = parentEl.firstChild;
		while(node) {
			if( framework.hasClass(node, childClassName) ) {
				return node;
			}
			node = node.nextSibling;
		}
	},
	arraySearch: function(array, value, key) {
		var i = array.length;
		while(i--) {
			if(array[i][key] === value) {
				return i;
			} 
		}
		return -1;
	},
	extend: function(o1, o2, preventOverwrite) {
		for (var prop in o2) {
			if (o2.hasOwnProperty(prop)) {
				if(preventOverwrite && o1.hasOwnProperty(prop)) {
					continue;
				}
				o1[prop] = o2[prop];
			}
		}
	},
	easing: {
		sine: {
			out: function(k) {
				return Math.sin(k * (Math.PI / 2));
			},
			inOut: function(k) {
				return - (Math.cos(Math.PI * k) - 1) / 2;
			}
		},
		cubic: {
			out: function(k) {
				return --k * k * k + 1;
			}
		}
		/*
			elastic: {
				out: function ( k ) {

					var s, a = 0.1, p = 0.4;
					if ( k === 0 ) return 0;
					if ( k === 1 ) return 1;
					if ( !a || a < 1 ) { a = 1; s = p / 4; }
					else s = p * Math.asin( 1 / a ) / ( 2 * Math.PI );
					return ( a * Math.pow( 2, - 10 * k) * Math.sin( ( k - s ) * ( 2 * Math.PI ) / p ) + 1 );

				},
			},
			back: {
				out: function ( k ) {
					var s = 1.70158;
					return --k * k * ( ( s + 1 ) * k + s ) + 1;
				}
			}
		*/
	},

	/**
	 * 
	 * @return {object}
	 * 
	 * {
	 *  raf : request animation frame function
	 *  caf : cancel animation frame function
	 *  transfrom : transform property key (with vendor), or null if not supported
	 *  oldIE : IE8 or below
	 * }
	 * 
	 */
	detectFeatures: function() {
		if(framework.features) {
			return framework.features;
		}
		var helperEl = framework.createEl(),
			helperStyle = helperEl.style,
			vendor = '',
			features = {};

		// IE8 and below
		features.oldIE = document.all && !document.addEventListener;

		features.touch = 'ontouchstart' in window;

		if(window.requestAnimationFrame) {
			features.raf = window.requestAnimationFrame;
			features.caf = window.cancelAnimationFrame;
		}

		features.pointerEvent = navigator.pointerEnabled || navigator.msPointerEnabled;

		// fix false-positive detection of old Android in new IE
		// (IE11 ua string contains "Android 4.0")
		
		if(!features.pointerEvent) { 

			var ua = navigator.userAgent;

			// Detect if device is iPhone or iPod and if it's older than iOS 8
			// http://stackoverflow.com/a/14223920
			// 
			// This detection is made because of buggy top/bottom toolbars
			// that don't trigger window.resize event.
			// For more info refer to _isFixedPosition variable in core.js

			if (/iP(hone|od)/.test(navigator.platform)) {
				var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
				if(v && v.length > 0) {
					v = parseInt(v[1], 10);
					if(v >= 1 && v < 8 ) {
						features.isOldIOSPhone = true;
					}
				}
			}

			// Detect old Android (before KitKat)
			// due to bugs related to position:fixed
			// http://stackoverflow.com/questions/7184573/pick-up-the-android-version-in-the-browser-by-javascript
			
			var match = ua.match(/Android\s([0-9\.]*)/);
			var androidversion =  match ? match[1] : 0;
			androidversion = parseFloat(androidversion);
			if(androidversion >= 1 ) {
				if(androidversion < 4.4) {
					features.isOldAndroid = true; // for fixed position bug & performance
				}
				features.androidVersion = androidversion; // for touchend bug
			}	
			features.isMobileOpera = /opera mini|opera mobi/i.test(ua);

			// p.s. yes, yes, UA sniffing is bad, propose your solution for above bugs.
		}
		
		var styleChecks = ['transform', 'perspective', 'animationName'],
			vendors = ['', 'webkit','Moz','ms','O'],
			styleCheckItem,
			styleName;

		for(var i = 0; i < 4; i++) {
			vendor = vendors[i];

			for(var a = 0; a < 3; a++) {
				styleCheckItem = styleChecks[a];

				// uppercase first letter of property name, if vendor is present
				styleName = vendor + (vendor ? 
										styleCheckItem.charAt(0).toUpperCase() + styleCheckItem.slice(1) : 
										styleCheckItem);
			
				if(!features[styleCheckItem] && styleName in helperStyle ) {
					features[styleCheckItem] = styleName;
				}
			}

			if(vendor && !features.raf) {
				vendor = vendor.toLowerCase();
				features.raf = window[vendor+'RequestAnimationFrame'];
				if(features.raf) {
					features.caf = window[vendor+'CancelAnimationFrame'] || 
									window[vendor+'CancelRequestAnimationFrame'];
				}
			}
		}
			
		if(!features.raf) {
			var lastTime = 0;
			features.raf = function(fn) {
				var currTime = new Date().getTime();
				var timeToCall = Math.max(0, 16 - (currTime - lastTime));
				var id = window.setTimeout(function() { fn(currTime + timeToCall); }, timeToCall);
				lastTime = currTime + timeToCall;
				return id;
			};
			features.caf = function(id) { clearTimeout(id); };
		}

		// Detect SVG support
		features.svg = !!document.createElementNS && 
						!!document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect;

		framework.features = features;

		return features;
	}
};

framework.detectFeatures();

// Override addEventListener for old versions of IE
if(framework.features.oldIE) {

	framework.bind = function(target, type, listener, unbind) {
		
		type = type.split(' ');

		var methodName = (unbind ? 'detach' : 'attach') + 'Event',
			evName,
			_handleEv = function() {
				listener.handleEvent.call(listener);
			};

		for(var i = 0; i < type.length; i++) {
			evName = type[i];
			if(evName) {

				if(typeof listener === 'object' && listener.handleEvent) {
					if(!unbind) {
						listener['oldIE' + evName] = _handleEv;
					} else {
						if(!listener['oldIE' + evName]) {
							return false;
						}
					}

					target[methodName]( 'on' + evName, listener['oldIE' + evName]);
				} else {
					target[methodName]( 'on' + evName, listener);
				}

			}
		}
	};
	
}

/*>>framework-bridge*/

/*>>core*/
//function(template, UiClass, items, options)

var self = this;

/**
 * Static vars, don't change unless you know what you're doing.
 */
var DOUBLE_TAP_RADIUS = 25, 
	NUM_HOLDERS = 3;

/**
 * Options
 */
var _options = {
	allowPanToNext:true,
	spacing: 0.12,
	bgOpacity: 1,
	mouseUsed: false,
	loop: true,
	pinchToClose: true,
	closeOnScroll: true,
	closeOnVerticalDrag: true,
	verticalDragRange: 0.75,
	hideAnimationDuration: 333,
	showAnimationDuration: 333,
	showHideOpacity: false,
	focus: true,
	escKey: true,
	arrowKeys: true,
	mainScrollEndFriction: 0.35,
	panEndFriction: 0.35,
	isClickableElement: function(el) {
        return el.tagName === 'A';
    },
    getDoubleTapZoom: function(isMouseClick, item) {
    	if(isMouseClick) {
    		return 1;
    	} else {
    		return item.initialZoomLevel < 0.7 ? 1 : 1.33;
    	}
    },
    maxSpreadZoom: 1.33,
	modal: true,

	// not fully implemented yet
	scaleMode: 'fit' // TODO
};
framework.extend(_options, options);


/**
 * Private helper variables & functions
 */

var _getEmptyPoint = function() { 
		return {x:0,y:0}; 
	};

var _isOpen,
	_isDestroying,
	_closedByScroll,
	_currentItemIndex,
	_containerStyle,
	_containerShiftIndex,
	_currPanDist = _getEmptyPoint(),
	_startPanOffset = _getEmptyPoint(),
	_panOffset = _getEmptyPoint(),
	_upMoveEvents, // drag move, drag end & drag cancel events array
	_downEvents, // drag start events array
	_globalEventHandlers,
	_viewportSize = {},
	_currZoomLevel,
	_startZoomLevel,
	_translatePrefix,
	_translateSufix,
	_updateSizeInterval,
	_itemsNeedUpdate,
	_currPositionIndex = 0,
	_offset = {},
	_slideSize = _getEmptyPoint(), // size of slide area, including spacing
	_itemHolders,
	_prevItemIndex,
	_indexDiff = 0, // difference of indexes since last content update
	_dragStartEvent,
	_dragMoveEvent,
	_dragEndEvent,
	_dragCancelEvent,
	_transformKey,
	_pointerEventEnabled,
	_isFixedPosition = true,
	_likelyTouchDevice,
	_modules = [],
	_requestAF,
	_cancelAF,
	_initalClassName,
	_initalWindowScrollY,
	_oldIE,
	_currentWindowScrollY,
	_features,
	_windowVisibleSize = {},
	_renderMaxResolution = false,
	_orientationChangeTimeout,


	// Registers PhotoSWipe module (History, Controller ...)
	_registerModule = function(name, module) {
		framework.extend(self, module.publicMethods);
		_modules.push(name);
	},

	_getLoopedId = function(index) {
		var numSlides = _getNumItems();
		if(index > numSlides - 1) {
			return index - numSlides;
		} else  if(index < 0) {
			return numSlides + index;
		}
		return index;
	},
	
	// Micro bind/trigger
	_listeners = {},
	_listen = function(name, fn) {
		if(!_listeners[name]) {
			_listeners[name] = [];
		}
		return _listeners[name].push(fn);
	},
	_shout = function(name) {
		var listeners = _listeners[name];

		if(listeners) {
			var args = Array.prototype.slice.call(arguments);
			args.shift();

			for(var i = 0; i < listeners.length; i++) {
				listeners[i].apply(self, args);
			}
		}
	},

	_getCurrentTime = function() {
		return new Date().getTime();
	},
	_applyBgOpacity = function(opacity) {
		_bgOpacity = opacity;
		self.bg.style.opacity = opacity * _options.bgOpacity;
	},

	_applyZoomTransform = function(styleObj,x,y,zoom,item) {
		if(!_renderMaxResolution || (item && item !== self.currItem) ) {
			zoom = zoom / (item ? item.fitRatio : self.currItem.fitRatio);	
		}
			
		styleObj[_transformKey] = _translatePrefix + x + 'px, ' + y + 'px' + _translateSufix + ' scale(' + zoom + ')';
	},
	_applyCurrentZoomPan = function( allowRenderResolution ) {
		if(_currZoomElementStyle) {

			if(allowRenderResolution) {
				if(_currZoomLevel > self.currItem.fitRatio) {
					if(!_renderMaxResolution) {
						_setImageSize(self.currItem, false, true);
						_renderMaxResolution = true;
					}
				} else {
					if(_renderMaxResolution) {
						_setImageSize(self.currItem);
						_renderMaxResolution = false;
					}
				}
			}
			

			_applyZoomTransform(_currZoomElementStyle, _panOffset.x, _panOffset.y, _currZoomLevel);
		}
	},
	_applyZoomPanToItem = function(item) {
		if(item.container) {

			_applyZoomTransform(item.container.style, 
								item.initialPosition.x, 
								item.initialPosition.y, 
								item.initialZoomLevel,
								item);
		}
	},
	_setTranslateX = function(x, elStyle) {
		elStyle[_transformKey] = _translatePrefix + x + 'px, 0px' + _translateSufix;
	},
	_moveMainScroll = function(x, dragging) {

		if(!_options.loop && dragging) {
			var newSlideIndexOffset = _currentItemIndex + (_slideSize.x * _currPositionIndex - x) / _slideSize.x,
				delta = Math.round(x - _mainScrollPos.x);

			if( (newSlideIndexOffset < 0 && delta > 0) || 
				(newSlideIndexOffset >= _getNumItems() - 1 && delta < 0) ) {
				x = _mainScrollPos.x + delta * _options.mainScrollEndFriction;
			} 
		}
		
		_mainScrollPos.x = x;
		_setTranslateX(x, _containerStyle);
	},
	_calculatePanOffset = function(axis, zoomLevel) {
		var m = _midZoomPoint[axis] - _offset[axis];
		return _startPanOffset[axis] + _currPanDist[axis] + m - m * ( zoomLevel / _startZoomLevel );
	},
	
	_equalizePoints = function(p1, p2) {
		p1.x = p2.x;
		p1.y = p2.y;
		if(p2.id) {
			p1.id = p2.id;
		}
	},
	_roundPoint = function(p) {
		p.x = Math.round(p.x);
		p.y = Math.round(p.y);
	},

	_mouseMoveTimeout = null,
	_onFirstMouseMove = function() {
		// Wait until mouse move event is fired at least twice during 100ms
		// We do this, because some mobile browsers trigger it on touchstart
		if(_mouseMoveTimeout ) { 
			framework.unbind(document, 'mousemove', _onFirstMouseMove);
			framework.addClass(template, 'pswp--has_mouse');
			_options.mouseUsed = true;
			_shout('mouseUsed');
		}
		_mouseMoveTimeout = setTimeout(function() {
			_mouseMoveTimeout = null;
		}, 100);
	},

	_bindEvents = function() {
		framework.bind(document, 'keydown', self);

		if(_features.transform) {
			// don't bind click event in browsers that don't support transform (mostly IE8)
			framework.bind(self.scrollWrap, 'click', self);
		}
		

		if(!_options.mouseUsed) {
			framework.bind(document, 'mousemove', _onFirstMouseMove);
		}

		framework.bind(window, 'resize scroll orientationchange', self);

		_shout('bindEvents');
	},

	_unbindEvents = function() {
		framework.unbind(window, 'resize scroll orientationchange', self);
		framework.unbind(window, 'scroll', _globalEventHandlers.scroll);
		framework.unbind(document, 'keydown', self);
		framework.unbind(document, 'mousemove', _onFirstMouseMove);

		if(_features.transform) {
			framework.unbind(self.scrollWrap, 'click', self);
		}

		if(_isDragging) {
			framework.unbind(window, _upMoveEvents, self);
		}

		clearTimeout(_orientationChangeTimeout);

		_shout('unbindEvents');
	},
	
	_calculatePanBounds = function(zoomLevel, update) {
		var bounds = _calculateItemSize( self.currItem, _viewportSize, zoomLevel );
		if(update) {
			_currPanBounds = bounds;
		}
		return bounds;
	},
	
	_getMinZoomLevel = function(item) {
		if(!item) {
			item = self.currItem;
		}
		return item.initialZoomLevel;
	},
	_getMaxZoomLevel = function(item) {
		if(!item) {
			item = self.currItem;
		}
		return item.w > 0 ? _options.maxSpreadZoom : 1;
	},

	// Return true if offset is out of the bounds
	_modifyDestPanOffset = function(axis, destPanBounds, destPanOffset, destZoomLevel) {
		if(destZoomLevel === self.currItem.initialZoomLevel) {
			destPanOffset[axis] = self.currItem.initialPosition[axis];
			return true;
		} else {
			destPanOffset[axis] = _calculatePanOffset(axis, destZoomLevel); 

			if(destPanOffset[axis] > destPanBounds.min[axis]) {
				destPanOffset[axis] = destPanBounds.min[axis];
				return true;
			} else if(destPanOffset[axis] < destPanBounds.max[axis] ) {
				destPanOffset[axis] = destPanBounds.max[axis];
				return true;
			}
		}
		return false;
	},

	_setupTransforms = function() {

		if(_transformKey) {
			// setup 3d transforms
			var allow3dTransform = _features.perspective && !_likelyTouchDevice;
			_translatePrefix = 'translate' + (allow3dTransform ? '3d(' : '(');
			_translateSufix = _features.perspective ? ', 0px)' : ')';	
			return;
		}

		// Override zoom/pan/move functions in case old browser is used (most likely IE)
		// (so they use left/top/width/height, instead of CSS transform)
	
		_transformKey = 'left';
		framework.addClass(template, 'pswp--ie');

		_setTranslateX = function(x, elStyle) {
			elStyle.left = x + 'px';
		};
		_applyZoomPanToItem = function(item) {

			var zoomRatio = item.fitRatio > 1 ? 1 : item.fitRatio,
				s = item.container.style,
				w = zoomRatio * item.w,
				h = zoomRatio * item.h;

			s.width = w + 'px';
			s.height = h + 'px';
			s.left = item.initialPosition.x + 'px';
			s.top = item.initialPosition.y + 'px';

		};
		_applyCurrentZoomPan = function() {
			if(_currZoomElementStyle) {

				var s = _currZoomElementStyle,
					item = self.currItem,
					zoomRatio = item.fitRatio > 1 ? 1 : item.fitRatio,
					w = zoomRatio * item.w,
					h = zoomRatio * item.h;

				s.width = w + 'px';
				s.height = h + 'px';


				s.left = _panOffset.x + 'px';
				s.top = _panOffset.y + 'px';
			}
			
		};
	},

	_onKeyDown = function(e) {
		var keydownAction = '';
		if(_options.escKey && e.keyCode === 27) { 
			keydownAction = 'close';
		} else if(_options.arrowKeys) {
			if(e.keyCode === 37) {
				keydownAction = 'prev';
			} else if(e.keyCode === 39) { 
				keydownAction = 'next';
			}
		}

		if(keydownAction) {
			// don't do anything if special key pressed to prevent from overriding default browser actions
			// e.g. in Chrome on Mac cmd+arrow-left returns to previous page
			if( !e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey ) {
				if(e.preventDefault) {
					e.preventDefault();
				} else {
					e.returnValue = false;
				} 
				self[keydownAction]();
			}
		}
	},

	_onGlobalClick = function(e) {
		if(!e) {
			return;
		}

		// don't allow click event to pass through when triggering after drag or some other gesture
		if(_moved || _zoomStarted || _mainScrollAnimating || _verticalDragInitiated) {
			e.preventDefault();
			e.stopPropagation();
		}
	},

	_updatePageScrollOffset = function() {
		self.setScrollOffset(0, framework.getScrollY());		
	};
	


	



// Micro animation engine
var _animations = {},
	_numAnimations = 0,
	_stopAnimation = function(name) {
		if(_animations[name]) {
			if(_animations[name].raf) {
				_cancelAF( _animations[name].raf );
			}
			_numAnimations--;
			delete _animations[name];
		}
	},
	_registerStartAnimation = function(name) {
		if(_animations[name]) {
			_stopAnimation(name);
		}
		if(!_animations[name]) {
			_numAnimations++;
			_animations[name] = {};
		}
	},
	_stopAllAnimations = function() {
		for (var prop in _animations) {

			if( _animations.hasOwnProperty( prop ) ) {
				_stopAnimation(prop);
			} 
			
		}
	},
	_animateProp = function(name, b, endProp, d, easingFn, onUpdate, onComplete) {
		var startAnimTime = _getCurrentTime(), t;
		_registerStartAnimation(name);

		var animloop = function(){
			if ( _animations[name] ) {
				
				t = _getCurrentTime() - startAnimTime; // time diff
				//b - beginning (start prop)
				//d - anim duration

				if ( t >= d ) {
					_stopAnimation(name);
					onUpdate(endProp);
					if(onComplete) {
						onComplete();
					}
					return;
				}
				onUpdate( (endProp - b) * easingFn(t/d) + b );

				_animations[name].raf = _requestAF(animloop);
			}
		};
		animloop();
	};
	


var publicMethods = {

	// make a few local variables and functions public
	shout: _shout,
	listen: _listen,
	viewportSize: _viewportSize,
	options: _options,

	isMainScrollAnimating: function() {
		return _mainScrollAnimating;
	},
	getZoomLevel: function() {
		return _currZoomLevel;
	},
	getCurrentIndex: function() {
		return _currentItemIndex;
	},
	isDragging: function() {
		return _isDragging;
	},	
	isZooming: function() {
		return _isZooming;
	},
	setScrollOffset: function(x,y) {
		_offset.x = x;
		_currentWindowScrollY = _offset.y = y;
		_shout('updateScrollOffset', _offset);
	},
	applyZoomPan: function(zoomLevel,panX,panY,allowRenderResolution) {
		_panOffset.x = panX;
		_panOffset.y = panY;
		_currZoomLevel = zoomLevel;
		_applyCurrentZoomPan( allowRenderResolution );
	},

	init: function() {

		if(_isOpen || _isDestroying) {
			return;
		}

		var i;

		self.framework = framework; // basic functionality
		self.template = template; // root DOM element of PhotoSwipe
		self.bg = framework.getChildByClass(template, 'pswp__bg');

		_initalClassName = template.className;
		_isOpen = true;
				
		_features = framework.detectFeatures();
		_requestAF = _features.raf;
		_cancelAF = _features.caf;
		_transformKey = _features.transform;
		_oldIE = _features.oldIE;
		
		self.scrollWrap = framework.getChildByClass(template, 'pswp__scroll-wrap');
		self.container = framework.getChildByClass(self.scrollWrap, 'pswp__container');

		_containerStyle = self.container.style; // for fast access

		// Objects that hold slides (there are only 3 in DOM)
		self.itemHolders = _itemHolders = [
			{el:self.container.children[0] , wrap:0, index: -1},
			{el:self.container.children[1] , wrap:0, index: -1},
			{el:self.container.children[2] , wrap:0, index: -1}
		];

		// hide nearby item holders until initial zoom animation finishes (to avoid extra Paints)
		_itemHolders[0].el.style.display = _itemHolders[2].el.style.display = 'none';

		_setupTransforms();

		// Setup global events
		_globalEventHandlers = {
			resize: self.updateSize,

			// Fixes: iOS 10.3 resize event
			// does not update scrollWrap.clientWidth instantly after resize
			// https://github.com/dimsemenov/PhotoSwipe/issues/1315
			orientationchange: function() {
				clearTimeout(_orientationChangeTimeout);
				_orientationChangeTimeout = setTimeout(function() {
					if(_viewportSize.x !== self.scrollWrap.clientWidth) {
						self.updateSize();
					}
				}, 500);
			},
			scroll: _updatePageScrollOffset,
			keydown: _onKeyDown,
			click: _onGlobalClick
		};

		// disable show/hide effects on old browsers that don't support CSS animations or transforms, 
		// old IOS, Android and Opera mobile. Blackberry seems to work fine, even older models.
		var oldPhone = _features.isOldIOSPhone || _features.isOldAndroid || _features.isMobileOpera;
		if(!_features.animationName || !_features.transform || oldPhone) {
			_options.showAnimationDuration = _options.hideAnimationDuration = 0;
		}

		// init modules
		for(i = 0; i < _modules.length; i++) {
			self['init' + _modules[i]]();
		}
		
		// init
		if(UiClass) {
			var ui = self.ui = new UiClass(self, framework);
			ui.init();
		}

		_shout('firstUpdate');
		_currentItemIndex = _currentItemIndex || _options.index || 0;
		// validate index
		if( isNaN(_currentItemIndex) || _currentItemIndex < 0 || _currentItemIndex >= _getNumItems() ) {
			_currentItemIndex = 0;
		}
		self.currItem = _getItemAt( _currentItemIndex );

		
		if(_features.isOldIOSPhone || _features.isOldAndroid) {
			_isFixedPosition = false;
		}
		
		template.setAttribute('aria-hidden', 'false');
		if(_options.modal) {
			if(!_isFixedPosition) {
				template.style.position = 'absolute';
				template.style.top = framework.getScrollY() + 'px';
			} else {
				template.style.position = 'fixed';
			}
		}

		if(_currentWindowScrollY === undefined) {
			_shout('initialLayout');
			_currentWindowScrollY = _initalWindowScrollY = framework.getScrollY();
		}
		
		// add classes to root element of PhotoSwipe
		var rootClasses = 'pswp--open ';
		if(_options.mainClass) {
			rootClasses += _options.mainClass + ' ';
		}
		if(_options.showHideOpacity) {
			rootClasses += 'pswp--animate_opacity ';
		}
		rootClasses += _likelyTouchDevice ? 'pswp--touch' : 'pswp--notouch';
		rootClasses += _features.animationName ? ' pswp--css_animation' : '';
		rootClasses += _features.svg ? ' pswp--svg' : '';
		framework.addClass(template, rootClasses);

		self.updateSize();

		// initial update
		_containerShiftIndex = -1;
		_indexDiff = null;
		for(i = 0; i < NUM_HOLDERS; i++) {
			_setTranslateX( (i+_containerShiftIndex) * _slideSize.x, _itemHolders[i].el.style);
		}

		if(!_oldIE) {
			framework.bind(self.scrollWrap, _downEvents, self); // no dragging for old IE
		}	

		_listen('initialZoomInEnd', function() {
			self.setContent(_itemHolders[0], _currentItemIndex-1);
			self.setContent(_itemHolders[2], _currentItemIndex+1);

			_itemHolders[0].el.style.display = _itemHolders[2].el.style.display = 'block';

			if(_options.focus) {
				// focus causes layout, 
				// which causes lag during the animation, 
				// that's why we delay it untill the initial zoom transition ends
				template.focus();
			}
			 

			_bindEvents();
		});

		// set content for center slide (first time)
		self.setContent(_itemHolders[1], _currentItemIndex);
		
		self.updateCurrItem();

		_shout('afterInit');

		if(!_isFixedPosition) {

			// On all versions of iOS lower than 8.0, we check size of viewport every second.
			// 
			// This is done to detect when Safari top & bottom bars appear, 
			// as this action doesn't trigger any events (like resize). 
			// 
			// On iOS8 they fixed this.
			// 
			// 10 Nov 2014: iOS 7 usage ~40%. iOS 8 usage 56%.
			
			_updateSizeInterval = setInterval(function() {
				if(!_numAnimations && !_isDragging && !_isZooming && (_currZoomLevel === self.currItem.initialZoomLevel)  ) {
					self.updateSize();
				}
			}, 1000);
		}

		framework.addClass(template, 'pswp--visible');
	},

	// Close the gallery, then destroy it
	close: function() {
		if(!_isOpen) {
			return;
		}

		_isOpen = false;
		_isDestroying = true;
		_shout('close');
		_unbindEvents();

		_showOrHide(self.currItem, null, true, self.destroy);
	},

	// destroys the gallery (unbinds events, cleans up intervals and timeouts to avoid memory leaks)
	destroy: function() {
		_shout('destroy');

		if(_showOrHideTimeout) {
			clearTimeout(_showOrHideTimeout);
		}
		
		template.setAttribute('aria-hidden', 'true');
		template.className = _initalClassName;

		if(_updateSizeInterval) {
			clearInterval(_updateSizeInterval);
		}

		framework.unbind(self.scrollWrap, _downEvents, self);

		// we unbind scroll event at the end, as closing animation may depend on it
		framework.unbind(window, 'scroll', self);

		_stopDragUpdateLoop();

		_stopAllAnimations();

		_listeners = null;
	},

	/**
	 * Pan image to position
	 * @param {Number} x     
	 * @param {Number} y     
	 * @param {Boolean} force Will ignore bounds if set to true.
	 */
	panTo: function(x,y,force) {
		if(!force) {
			if(x > _currPanBounds.min.x) {
				x = _currPanBounds.min.x;
			} else if(x < _currPanBounds.max.x) {
				x = _currPanBounds.max.x;
			}

			if(y > _currPanBounds.min.y) {
				y = _currPanBounds.min.y;
			} else if(y < _currPanBounds.max.y) {
				y = _currPanBounds.max.y;
			}
		}
		
		_panOffset.x = x;
		_panOffset.y = y;
		_applyCurrentZoomPan();
	},
	
	handleEvent: function (e) {
		e = e || window.event;
		if(_globalEventHandlers[e.type]) {
			_globalEventHandlers[e.type](e);
		}
	},


	goTo: function(index) {

		index = _getLoopedId(index);

		var diff = index - _currentItemIndex;
		_indexDiff = diff;

		_currentItemIndex = index;
		self.currItem = _getItemAt( _currentItemIndex );
		_currPositionIndex -= diff;
		
		_moveMainScroll(_slideSize.x * _currPositionIndex);
		

		_stopAllAnimations();
		_mainScrollAnimating = false;

		self.updateCurrItem();
	},
	next: function() {
		self.goTo( _currentItemIndex + 1);
	},
	prev: function() {
		self.goTo( _currentItemIndex - 1);
	},

	// update current zoom/pan objects
	updateCurrZoomItem: function(emulateSetContent) {
		if(emulateSetContent) {
			_shout('beforeChange', 0);
		}

		// itemHolder[1] is middle (current) item
		if(_itemHolders[1].el.children.length) {
			var zoomElement = _itemHolders[1].el.children[0];
			if( framework.hasClass(zoomElement, 'pswp__zoom-wrap') ) {
				_currZoomElementStyle = zoomElement.style;
			} else {
				_currZoomElementStyle = null;
			}
		} else {
			_currZoomElementStyle = null;
		}
		
		_currPanBounds = self.currItem.bounds;	
		_startZoomLevel = _currZoomLevel = self.currItem.initialZoomLevel;

		_panOffset.x = _currPanBounds.center.x;
		_panOffset.y = _currPanBounds.center.y;

		if(emulateSetContent) {
			_shout('afterChange');
		}
	},


	invalidateCurrItems: function() {
		_itemsNeedUpdate = true;
		for(var i = 0; i < NUM_HOLDERS; i++) {
			if( _itemHolders[i].item ) {
				_itemHolders[i].item.needsUpdate = true;
			}
		}
	},

	updateCurrItem: function(beforeAnimation) {

		if(_indexDiff === 0) {
			return;
		}

		var diffAbs = Math.abs(_indexDiff),
			tempHolder;

		if(beforeAnimation && diffAbs < 2) {
			return;
		}


		self.currItem = _getItemAt( _currentItemIndex );
		_renderMaxResolution = false;
		
		_shout('beforeChange', _indexDiff);

		if(diffAbs >= NUM_HOLDERS) {
			_containerShiftIndex += _indexDiff + (_indexDiff > 0 ? -NUM_HOLDERS : NUM_HOLDERS);
			diffAbs = NUM_HOLDERS;
		}
		for(var i = 0; i < diffAbs; i++) {
			if(_indexDiff > 0) {
				tempHolder = _itemHolders.shift();
				_itemHolders[NUM_HOLDERS-1] = tempHolder; // move first to last

				_containerShiftIndex++;
				_setTranslateX( (_containerShiftIndex+2) * _slideSize.x, tempHolder.el.style);
				self.setContent(tempHolder, _currentItemIndex - diffAbs + i + 1 + 1);
			} else {
				tempHolder = _itemHolders.pop();
				_itemHolders.unshift( tempHolder ); // move last to first

				_containerShiftIndex--;
				_setTranslateX( _containerShiftIndex * _slideSize.x, tempHolder.el.style);
				self.setContent(tempHolder, _currentItemIndex + diffAbs - i - 1 - 1);
			}
			
		}

		// reset zoom/pan on previous item
		if(_currZoomElementStyle && Math.abs(_indexDiff) === 1) {

			var prevItem = _getItemAt(_prevItemIndex);
			if(prevItem.initialZoomLevel !== _currZoomLevel) {
				_calculateItemSize(prevItem , _viewportSize );
				_setImageSize(prevItem);
				_applyZoomPanToItem( prevItem ); 				
			}

		}

		// reset diff after update
		_indexDiff = 0;

		self.updateCurrZoomItem();

		_prevItemIndex = _currentItemIndex;

		_shout('afterChange');
		
	},



	updateSize: function(force) {
		
		if(!_isFixedPosition && _options.modal) {
			var windowScrollY = framework.getScrollY();
			if(_currentWindowScrollY !== windowScrollY) {
				template.style.top = windowScrollY + 'px';
				_currentWindowScrollY = windowScrollY;
			}
			if(!force && _windowVisibleSize.x === window.innerWidth && _windowVisibleSize.y === window.innerHeight) {
				return;
			}
			_windowVisibleSize.x = window.innerWidth;
			_windowVisibleSize.y = window.innerHeight;

			//template.style.width = _windowVisibleSize.x + 'px';
			template.style.height = _windowVisibleSize.y + 'px';
		}



		_viewportSize.x = self.scrollWrap.clientWidth;
		_viewportSize.y = self.scrollWrap.clientHeight;

		_updatePageScrollOffset();

		_slideSize.x = _viewportSize.x + Math.round(_viewportSize.x * _options.spacing);
		_slideSize.y = _viewportSize.y;

		_moveMainScroll(_slideSize.x * _currPositionIndex);

		_shout('beforeResize'); // even may be used for example to switch image sources


		// don't re-calculate size on inital size update
		if(_containerShiftIndex !== undefined) {

			var holder,
				item,
				hIndex;

			for(var i = 0; i < NUM_HOLDERS; i++) {
				holder = _itemHolders[i];
				_setTranslateX( (i+_containerShiftIndex) * _slideSize.x, holder.el.style);

				hIndex = _currentItemIndex+i-1;

				if(_options.loop && _getNumItems() > 2) {
					hIndex = _getLoopedId(hIndex);
				}

				// update zoom level on items and refresh source (if needsUpdate)
				item = _getItemAt( hIndex );

				// re-render gallery item if `needsUpdate`,
				// or doesn't have `bounds` (entirely new slide object)
				if( item && (_itemsNeedUpdate || item.needsUpdate || !item.bounds) ) {

					self.cleanSlide( item );
					
					self.setContent( holder, hIndex );

					// if "center" slide
					if(i === 1) {
						self.currItem = item;
						self.updateCurrZoomItem(true);
					}

					item.needsUpdate = false;

				} else if(holder.index === -1 && hIndex >= 0) {
					// add content first time
					self.setContent( holder, hIndex );
				}
				if(item && item.container) {
					_calculateItemSize(item, _viewportSize);
					_setImageSize(item);
					_applyZoomPanToItem( item );
				}
				
			}
			_itemsNeedUpdate = false;
		}	

		_startZoomLevel = _currZoomLevel = self.currItem.initialZoomLevel;
		_currPanBounds = self.currItem.bounds;

		if(_currPanBounds) {
			_panOffset.x = _currPanBounds.center.x;
			_panOffset.y = _currPanBounds.center.y;
			_applyCurrentZoomPan( true );
		}
		
		_shout('resize');
	},
	
	// Zoom current item to
	zoomTo: function(destZoomLevel, centerPoint, speed, easingFn, updateFn) {
		/*
			if(destZoomLevel === 'fit') {
				destZoomLevel = self.currItem.fitRatio;
			} else if(destZoomLevel === 'fill') {
				destZoomLevel = self.currItem.fillRatio;
			}
		*/

		if(centerPoint) {
			_startZoomLevel = _currZoomLevel;
			_midZoomPoint.x = Math.abs(centerPoint.x) - _panOffset.x ;
			_midZoomPoint.y = Math.abs(centerPoint.y) - _panOffset.y ;
			_equalizePoints(_startPanOffset, _panOffset);
		}

		var destPanBounds = _calculatePanBounds(destZoomLevel, false),
			destPanOffset = {};

		_modifyDestPanOffset('x', destPanBounds, destPanOffset, destZoomLevel);
		_modifyDestPanOffset('y', destPanBounds, destPanOffset, destZoomLevel);

		var initialZoomLevel = _currZoomLevel;
		var initialPanOffset = {
			x: _panOffset.x,
			y: _panOffset.y
		};

		_roundPoint(destPanOffset);

		var onUpdate = function(now) {
			if(now === 1) {
				_currZoomLevel = destZoomLevel;
				_panOffset.x = destPanOffset.x;
				_panOffset.y = destPanOffset.y;
			} else {
				_currZoomLevel = (destZoomLevel - initialZoomLevel) * now + initialZoomLevel;
				_panOffset.x = (destPanOffset.x - initialPanOffset.x) * now + initialPanOffset.x;
				_panOffset.y = (destPanOffset.y - initialPanOffset.y) * now + initialPanOffset.y;
			}

			if(updateFn) {
				updateFn(now);
			}

			_applyCurrentZoomPan( now === 1 );
		};

		if(speed) {
			_animateProp('customZoomTo', 0, 1, speed, easingFn || framework.easing.sine.inOut, onUpdate);
		} else {
			onUpdate(1);
		}
	}


};


/*>>core*/

/*>>gestures*/
/**
 * Mouse/touch/pointer event handlers.
 * 
 * separated from @core.js for readability
 */

var MIN_SWIPE_DISTANCE = 30,
	DIRECTION_CHECK_OFFSET = 10; // amount of pixels to drag to determine direction of swipe

var _gestureStartTime,
	_gestureCheckSpeedTime,

	// pool of objects that are used during dragging of zooming
	p = {}, // first point
	p2 = {}, // second point (for zoom gesture)
	delta = {},
	_currPoint = {},
	_startPoint = {},
	_currPointers = [],
	_startMainScrollPos = {},
	_releaseAnimData,
	_posPoints = [], // array of points during dragging, used to determine type of gesture
	_tempPoint = {},

	_isZoomingIn,
	_verticalDragInitiated,
	_oldAndroidTouchEndTimeout,
	_currZoomedItemIndex = 0,
	_centerPoint = _getEmptyPoint(),
	_lastReleaseTime = 0,
	_isDragging, // at least one pointer is down
	_isMultitouch, // at least two _pointers are down
	_zoomStarted, // zoom level changed during zoom gesture
	_moved,
	_dragAnimFrame,
	_mainScrollShifted,
	_currentPoints, // array of current touch points
	_isZooming,
	_currPointsDistance,
	_startPointsDistance,
	_currPanBounds,
	_mainScrollPos = _getEmptyPoint(),
	_currZoomElementStyle,
	_mainScrollAnimating, // true, if animation after swipe gesture is running
	_midZoomPoint = _getEmptyPoint(),
	_currCenterPoint = _getEmptyPoint(),
	_direction,
	_isFirstMove,
	_opacityChanged,
	_bgOpacity,
	_wasOverInitialZoom,

	_isEqualPoints = function(p1, p2) {
		return p1.x === p2.x && p1.y === p2.y;
	},
	_isNearbyPoints = function(touch0, touch1) {
		return Math.abs(touch0.x - touch1.x) < DOUBLE_TAP_RADIUS && Math.abs(touch0.y - touch1.y) < DOUBLE_TAP_RADIUS;
	},
	_calculatePointsDistance = function(p1, p2) {
		_tempPoint.x = Math.abs( p1.x - p2.x );
		_tempPoint.y = Math.abs( p1.y - p2.y );
		return Math.sqrt(_tempPoint.x * _tempPoint.x + _tempPoint.y * _tempPoint.y);
	},
	_stopDragUpdateLoop = function() {
		if(_dragAnimFrame) {
			_cancelAF(_dragAnimFrame);
			_dragAnimFrame = null;
		}
	},
	_dragUpdateLoop = function() {
		if(_isDragging) {
			_dragAnimFrame = _requestAF(_dragUpdateLoop);
			_renderMovement();
		}
	},
	_canPan = function() {
		return !(_options.scaleMode === 'fit' && _currZoomLevel ===  self.currItem.initialZoomLevel);
	},
	
	// find the closest parent DOM element
	_closestElement = function(el, fn) {
	  	if(!el || el === document) {
	  		return false;
	  	}

	  	// don't search elements above pswp__scroll-wrap
	  	if(el.getAttribute('class') && el.getAttribute('class').indexOf('pswp__scroll-wrap') > -1 ) {
	  		return false;
	  	}

	  	if( fn(el) ) {
	  		return el;
	  	}

	  	return _closestElement(el.parentNode, fn);
	},

	_preventObj = {},
	_preventDefaultEventBehaviour = function(e, isDown) {
	    _preventObj.prevent = !_closestElement(e.target, _options.isClickableElement);

		_shout('preventDragEvent', e, isDown, _preventObj);
		return _preventObj.prevent;

	},
	_convertTouchToPoint = function(touch, p) {
		p.x = touch.pageX;
		p.y = touch.pageY;
		p.id = touch.identifier;
		return p;
	},
	_findCenterOfPoints = function(p1, p2, pCenter) {
		pCenter.x = (p1.x + p2.x) * 0.5;
		pCenter.y = (p1.y + p2.y) * 0.5;
	},
	_pushPosPoint = function(time, x, y) {
		if(time - _gestureCheckSpeedTime > 50) {
			var o = _posPoints.length > 2 ? _posPoints.shift() : {};
			o.x = x;
			o.y = y; 
			_posPoints.push(o);
			_gestureCheckSpeedTime = time;
		}
	},

	_calculateVerticalDragOpacityRatio = function() {
		var yOffset = _panOffset.y - self.currItem.initialPosition.y; // difference between initial and current position
		return 1 -  Math.abs( yOffset / (_viewportSize.y / 2)  );
	},

	
	// points pool, reused during touch events
	_ePoint1 = {},
	_ePoint2 = {},
	_tempPointsArr = [],
	_tempCounter,
	_getTouchPoints = function(e) {
		// clean up previous points, without recreating array
		while(_tempPointsArr.length > 0) {
			_tempPointsArr.pop();
		}

		if(!_pointerEventEnabled) {
			if(e.type.indexOf('touch') > -1) {

				if(e.touches && e.touches.length > 0) {
					_tempPointsArr[0] = _convertTouchToPoint(e.touches[0], _ePoint1);
					if(e.touches.length > 1) {
						_tempPointsArr[1] = _convertTouchToPoint(e.touches[1], _ePoint2);
					}
				}
				
			} else {
				_ePoint1.x = e.pageX;
				_ePoint1.y = e.pageY;
				_ePoint1.id = '';
				_tempPointsArr[0] = _ePoint1;//_ePoint1;
			}
		} else {
			_tempCounter = 0;
			// we can use forEach, as pointer events are supported only in modern browsers
			_currPointers.forEach(function(p) {
				if(_tempCounter === 0) {
					_tempPointsArr[0] = p;
				} else if(_tempCounter === 1) {
					_tempPointsArr[1] = p;
				}
				_tempCounter++;

			});
		}
		return _tempPointsArr;
	},

	_panOrMoveMainScroll = function(axis, delta) {

		var panFriction,
			overDiff = 0,
			newOffset = _panOffset[axis] + delta[axis],
			startOverDiff,
			dir = delta[axis] > 0,
			newMainScrollPosition = _mainScrollPos.x + delta.x,
			mainScrollDiff = _mainScrollPos.x - _startMainScrollPos.x,
			newPanPos,
			newMainScrollPos;

		// calculate fdistance over the bounds and friction
		if(newOffset > _currPanBounds.min[axis] || newOffset < _currPanBounds.max[axis]) {
			panFriction = _options.panEndFriction;
			// Linear increasing of friction, so at 1/4 of viewport it's at max value. 
			// Looks not as nice as was expected. Left for history.
			// panFriction = (1 - (_panOffset[axis] + delta[axis] + panBounds.min[axis]) / (_viewportSize[axis] / 4) );
		} else {
			panFriction = 1;
		}
		
		newOffset = _panOffset[axis] + delta[axis] * panFriction;

		// move main scroll or start panning
		if(_options.allowPanToNext || _currZoomLevel === self.currItem.initialZoomLevel) {


			if(!_currZoomElementStyle) {
				
				newMainScrollPos = newMainScrollPosition;

			} else if(_direction === 'h' && axis === 'x' && !_zoomStarted ) {
				
				if(dir) {
					if(newOffset > _currPanBounds.min[axis]) {
						panFriction = _options.panEndFriction;
						overDiff = _currPanBounds.min[axis] - newOffset;
						startOverDiff = _currPanBounds.min[axis] - _startPanOffset[axis];
					}
					
					// drag right
					if( (startOverDiff <= 0 || mainScrollDiff < 0) && _getNumItems() > 1 ) {
						newMainScrollPos = newMainScrollPosition;
						if(mainScrollDiff < 0 && newMainScrollPosition > _startMainScrollPos.x) {
							newMainScrollPos = _startMainScrollPos.x;
						}
					} else {
						if(_currPanBounds.min.x !== _currPanBounds.max.x) {
							newPanPos = newOffset;
						}
						
					}

				} else {

					if(newOffset < _currPanBounds.max[axis] ) {
						panFriction =_options.panEndFriction;
						overDiff = newOffset - _currPanBounds.max[axis];
						startOverDiff = _startPanOffset[axis] - _currPanBounds.max[axis];
					}

					if( (startOverDiff <= 0 || mainScrollDiff > 0) && _getNumItems() > 1 ) {
						newMainScrollPos = newMainScrollPosition;

						if(mainScrollDiff > 0 && newMainScrollPosition < _startMainScrollPos.x) {
							newMainScrollPos = _startMainScrollPos.x;
						}

					} else {
						if(_currPanBounds.min.x !== _currPanBounds.max.x) {
							newPanPos = newOffset;
						}
					}

				}


				//
			}

			if(axis === 'x') {

				if(newMainScrollPos !== undefined) {
					_moveMainScroll(newMainScrollPos, true);
					if(newMainScrollPos === _startMainScrollPos.x) {
						_mainScrollShifted = false;
					} else {
						_mainScrollShifted = true;
					}
				}

				if(_currPanBounds.min.x !== _currPanBounds.max.x) {
					if(newPanPos !== undefined) {
						_panOffset.x = newPanPos;
					} else if(!_mainScrollShifted) {
						_panOffset.x += delta.x * panFriction;
					}
				}

				return newMainScrollPos !== undefined;
			}

		}

		if(!_mainScrollAnimating) {
			
			if(!_mainScrollShifted) {
				if(_currZoomLevel > self.currItem.fitRatio) {
					_panOffset[axis] += delta[axis] * panFriction;
				
				}
			}

			
		}
		
	},

	// Pointerdown/touchstart/mousedown handler
	_onDragStart = function(e) {

		// Allow dragging only via left mouse button.
		// As this handler is not added in IE8 - we ignore e.which
		// 
		// http://www.quirksmode.org/js/events_properties.html
		// https://developer.mozilla.org/en-US/docs/Web/API/event.button
		if(e.type === 'mousedown' && e.button > 0  ) {
			return;
		}

		if(_initialZoomRunning) {
			e.preventDefault();
			return;
		}

		if(_oldAndroidTouchEndTimeout && e.type === 'mousedown') {
			return;
		}

		if(_preventDefaultEventBehaviour(e, true)) {
			e.preventDefault();
		}



		_shout('pointerDown');

		if(_pointerEventEnabled) {
			var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');
			if(pointerIndex < 0) {
				pointerIndex = _currPointers.length;
			}
			_currPointers[pointerIndex] = {x:e.pageX, y:e.pageY, id: e.pointerId};
		}
		


		var startPointsList = _getTouchPoints(e),
			numPoints = startPointsList.length;

		_currentPoints = null;

		_stopAllAnimations();

		// init drag
		if(!_isDragging || numPoints === 1) {

			

			_isDragging = _isFirstMove = true;
			framework.bind(window, _upMoveEvents, self);

			_isZoomingIn = 
				_wasOverInitialZoom = 
				_opacityChanged = 
				_verticalDragInitiated = 
				_mainScrollShifted = 
				_moved = 
				_isMultitouch = 
				_zoomStarted = false;

			_direction = null;

			_shout('firstTouchStart', startPointsList);

			_equalizePoints(_startPanOffset, _panOffset);

			_currPanDist.x = _currPanDist.y = 0;
			_equalizePoints(_currPoint, startPointsList[0]);
			_equalizePoints(_startPoint, _currPoint);

			//_equalizePoints(_startMainScrollPos, _mainScrollPos);
			_startMainScrollPos.x = _slideSize.x * _currPositionIndex;

			_posPoints = [{
				x: _currPoint.x,
				y: _currPoint.y
			}];

			_gestureCheckSpeedTime = _gestureStartTime = _getCurrentTime();

			//_mainScrollAnimationEnd(true);
			_calculatePanBounds( _currZoomLevel, true );
			
			// Start rendering
			_stopDragUpdateLoop();
			_dragUpdateLoop();
			
		}

		// init zoom
		if(!_isZooming && numPoints > 1 && !_mainScrollAnimating && !_mainScrollShifted) {
			_startZoomLevel = _currZoomLevel;
			_zoomStarted = false; // true if zoom changed at least once

			_isZooming = _isMultitouch = true;
			_currPanDist.y = _currPanDist.x = 0;

			_equalizePoints(_startPanOffset, _panOffset);

			_equalizePoints(p, startPointsList[0]);
			_equalizePoints(p2, startPointsList[1]);

			_findCenterOfPoints(p, p2, _currCenterPoint);

			_midZoomPoint.x = Math.abs(_currCenterPoint.x) - _panOffset.x;
			_midZoomPoint.y = Math.abs(_currCenterPoint.y) - _panOffset.y;
			_currPointsDistance = _startPointsDistance = _calculatePointsDistance(p, p2);
		}


	},

	// Pointermove/touchmove/mousemove handler
	_onDragMove = function(e) {

		e.preventDefault();

		if(_pointerEventEnabled) {
			var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');
			if(pointerIndex > -1) {
				var p = _currPointers[pointerIndex];
				p.x = e.pageX;
				p.y = e.pageY; 
			}
		}

		if(_isDragging) {
			var touchesList = _getTouchPoints(e);
			if(!_direction && !_moved && !_isZooming) {

				if(_mainScrollPos.x !== _slideSize.x * _currPositionIndex) {
					// if main scroll position is shifted – direction is always horizontal
					_direction = 'h';
				} else {
					var diff = Math.abs(touchesList[0].x - _currPoint.x) - Math.abs(touchesList[0].y - _currPoint.y);
					// check the direction of movement
					if(Math.abs(diff) >= DIRECTION_CHECK_OFFSET) {
						_direction = diff > 0 ? 'h' : 'v';
						_currentPoints = touchesList;
					}
				}
				
			} else {
				_currentPoints = touchesList;
			}
		}	
	},
	// 
	_renderMovement =  function() {

		if(!_currentPoints) {
			return;
		}

		var numPoints = _currentPoints.length;

		if(numPoints === 0) {
			return;
		}

		_equalizePoints(p, _currentPoints[0]);

		delta.x = p.x - _currPoint.x;
		delta.y = p.y - _currPoint.y;

		if(_isZooming && numPoints > 1) {
			// Handle behaviour for more than 1 point

			_currPoint.x = p.x;
			_currPoint.y = p.y;
		
			// check if one of two points changed
			if( !delta.x && !delta.y && _isEqualPoints(_currentPoints[1], p2) ) {
				return;
			}

			_equalizePoints(p2, _currentPoints[1]);


			if(!_zoomStarted) {
				_zoomStarted = true;
				_shout('zoomGestureStarted');
			}
			
			// Distance between two points
			var pointsDistance = _calculatePointsDistance(p,p2);

			var zoomLevel = _calculateZoomLevel(pointsDistance);

			// slightly over the of initial zoom level
			if(zoomLevel > self.currItem.initialZoomLevel + self.currItem.initialZoomLevel / 15) {
				_wasOverInitialZoom = true;
			}

			// Apply the friction if zoom level is out of the bounds
			var zoomFriction = 1,
				minZoomLevel = _getMinZoomLevel(),
				maxZoomLevel = _getMaxZoomLevel();

			if ( zoomLevel < minZoomLevel ) {
				
				if(_options.pinchToClose && !_wasOverInitialZoom && _startZoomLevel <= self.currItem.initialZoomLevel) {
					// fade out background if zooming out
					var minusDiff = minZoomLevel - zoomLevel;
					var percent = 1 - minusDiff / (minZoomLevel / 1.2);

					_applyBgOpacity(percent);
					_shout('onPinchClose', percent);
					_opacityChanged = true;
				} else {
					zoomFriction = (minZoomLevel - zoomLevel) / minZoomLevel;
					if(zoomFriction > 1) {
						zoomFriction = 1;
					}
					zoomLevel = minZoomLevel - zoomFriction * (minZoomLevel / 3);
				}
				
			} else if ( zoomLevel > maxZoomLevel ) {
				// 1.5 - extra zoom level above the max. E.g. if max is x6, real max 6 + 1.5 = 7.5
				zoomFriction = (zoomLevel - maxZoomLevel) / ( minZoomLevel * 6 );
				if(zoomFriction > 1) {
					zoomFriction = 1;
				}
				zoomLevel = maxZoomLevel + zoomFriction * minZoomLevel;
			}

			if(zoomFriction < 0) {
				zoomFriction = 0;
			}

			// distance between touch points after friction is applied
			_currPointsDistance = pointsDistance;

			// _centerPoint - The point in the middle of two pointers
			_findCenterOfPoints(p, p2, _centerPoint);
		
			// paning with two pointers pressed
			_currPanDist.x += _centerPoint.x - _currCenterPoint.x;
			_currPanDist.y += _centerPoint.y - _currCenterPoint.y;
			_equalizePoints(_currCenterPoint, _centerPoint);

			_panOffset.x = _calculatePanOffset('x', zoomLevel);
			_panOffset.y = _calculatePanOffset('y', zoomLevel);

			_isZoomingIn = zoomLevel > _currZoomLevel;
			_currZoomLevel = zoomLevel;
			_applyCurrentZoomPan();

		} else {

			// handle behaviour for one point (dragging or panning)

			if(!_direction) {
				return;
			}

			if(_isFirstMove) {
				_isFirstMove = false;

				// subtract drag distance that was used during the detection direction  

				if( Math.abs(delta.x) >= DIRECTION_CHECK_OFFSET) {
					delta.x -= _currentPoints[0].x - _startPoint.x;
				}
				
				if( Math.abs(delta.y) >= DIRECTION_CHECK_OFFSET) {
					delta.y -= _currentPoints[0].y - _startPoint.y;
				}
			}

			_currPoint.x = p.x;
			_currPoint.y = p.y;

			// do nothing if pointers position hasn't changed
			if(delta.x === 0 && delta.y === 0) {
				return;
			}

			if(_direction === 'v' && _options.closeOnVerticalDrag) {
				if(!_canPan()) {
					_currPanDist.y += delta.y;
					_panOffset.y += delta.y;

					var opacityRatio = _calculateVerticalDragOpacityRatio();

					_verticalDragInitiated = true;
					_shout('onVerticalDrag', opacityRatio);

					_applyBgOpacity(opacityRatio);
					_applyCurrentZoomPan();
					return ;
				}
			}

			_pushPosPoint(_getCurrentTime(), p.x, p.y);

			_moved = true;
			_currPanBounds = self.currItem.bounds;
			
			var mainScrollChanged = _panOrMoveMainScroll('x', delta);
			if(!mainScrollChanged) {
				_panOrMoveMainScroll('y', delta);

				_roundPoint(_panOffset);
				_applyCurrentZoomPan();
			}

		}

	},
	
	// Pointerup/pointercancel/touchend/touchcancel/mouseup event handler
	_onDragRelease = function(e) {

		if(_features.isOldAndroid ) {

			if(_oldAndroidTouchEndTimeout && e.type === 'mouseup') {
				return;
			}

			// on Android (v4.1, 4.2, 4.3 & possibly older) 
			// ghost mousedown/up event isn't preventable via e.preventDefault,
			// which causes fake mousedown event
			// so we block mousedown/up for 600ms
			if( e.type.indexOf('touch') > -1 ) {
				clearTimeout(_oldAndroidTouchEndTimeout);
				_oldAndroidTouchEndTimeout = setTimeout(function() {
					_oldAndroidTouchEndTimeout = 0;
				}, 600);
			}
			
		}

		_shout('pointerUp');

		if(_preventDefaultEventBehaviour(e, false)) {
			e.preventDefault();
		}

		var releasePoint;

		if(_pointerEventEnabled) {
			var pointerIndex = framework.arraySearch(_currPointers, e.pointerId, 'id');
			
			if(pointerIndex > -1) {
				releasePoint = _currPointers.splice(pointerIndex, 1)[0];

				if(navigator.pointerEnabled) {
					releasePoint.type = e.pointerType || 'mouse';
				} else {
					var MSPOINTER_TYPES = {
						4: 'mouse', // event.MSPOINTER_TYPE_MOUSE
						2: 'touch', // event.MSPOINTER_TYPE_TOUCH 
						3: 'pen' // event.MSPOINTER_TYPE_PEN
					};
					releasePoint.type = MSPOINTER_TYPES[e.pointerType];

					if(!releasePoint.type) {
						releasePoint.type = e.pointerType || 'mouse';
					}
				}

			}
		}

		var touchList = _getTouchPoints(e),
			gestureType,
			numPoints = touchList.length;

		if(e.type === 'mouseup') {
			numPoints = 0;
		}

		// Do nothing if there were 3 touch points or more
		if(numPoints === 2) {
			_currentPoints = null;
			return true;
		}

		// if second pointer released
		if(numPoints === 1) {
			_equalizePoints(_startPoint, touchList[0]);
		}				


		// pointer hasn't moved, send "tap release" point
		if(numPoints === 0 && !_direction && !_mainScrollAnimating) {
			if(!releasePoint) {
				if(e.type === 'mouseup') {
					releasePoint = {x: e.pageX, y: e.pageY, type:'mouse'};
				} else if(e.changedTouches && e.changedTouches[0]) {
					releasePoint = {x: e.changedTouches[0].pageX, y: e.changedTouches[0].pageY, type:'touch'};
				}		
			}

			_shout('touchRelease', e, releasePoint);
		}

		// Difference in time between releasing of two last touch points (zoom gesture)
		var releaseTimeDiff = -1;

		// Gesture completed, no pointers left
		if(numPoints === 0) {
			_isDragging = false;
			framework.unbind(window, _upMoveEvents, self);

			_stopDragUpdateLoop();

			if(_isZooming) {
				// Two points released at the same time
				releaseTimeDiff = 0;
			} else if(_lastReleaseTime !== -1) {
				releaseTimeDiff = _getCurrentTime() - _lastReleaseTime;
			}
		}
		_lastReleaseTime = numPoints === 1 ? _getCurrentTime() : -1;
		
		if(releaseTimeDiff !== -1 && releaseTimeDiff < 150) {
			gestureType = 'zoom';
		} else {
			gestureType = 'swipe';
		}

		if(_isZooming && numPoints < 2) {
			_isZooming = false;

			// Only second point released
			if(numPoints === 1) {
				gestureType = 'zoomPointerUp';
			}
			_shout('zoomGestureEnded');
		}

		_currentPoints = null;
		if(!_moved && !_zoomStarted && !_mainScrollAnimating && !_verticalDragInitiated) {
			// nothing to animate
			return;
		}
	
		_stopAllAnimations();

		
		if(!_releaseAnimData) {
			_releaseAnimData = _initDragReleaseAnimationData();
		}
		
		_releaseAnimData.calculateSwipeSpeed('x');


		if(_verticalDragInitiated) {

			var opacityRatio = _calculateVerticalDragOpacityRatio();

			if(opacityRatio < _options.verticalDragRange) {
				self.close();
			} else {
				var initalPanY = _panOffset.y,
					initialBgOpacity = _bgOpacity;

				_animateProp('verticalDrag', 0, 1, 300, framework.easing.cubic.out, function(now) {
					
					_panOffset.y = (self.currItem.initialPosition.y - initalPanY) * now + initalPanY;

					_applyBgOpacity(  (1 - initialBgOpacity) * now + initialBgOpacity );
					_applyCurrentZoomPan();
				});

				_shout('onVerticalDrag', 1);
			}

			return;
		}


		// main scroll 
		if(  (_mainScrollShifted || _mainScrollAnimating) && numPoints === 0) {
			var itemChanged = _finishSwipeMainScrollGesture(gestureType, _releaseAnimData);
			if(itemChanged) {
				return;
			}
			gestureType = 'zoomPointerUp';
		}

		// prevent zoom/pan animation when main scroll animation runs
		if(_mainScrollAnimating) {
			return;
		}
		
		// Complete simple zoom gesture (reset zoom level if it's out of the bounds)  
		if(gestureType !== 'swipe') {
			_completeZoomGesture();
			return;
		}
	
		// Complete pan gesture if main scroll is not shifted, and it's possible to pan current image
		if(!_mainScrollShifted && _currZoomLevel > self.currItem.fitRatio) {
			_completePanGesture(_releaseAnimData);
		}
	},


	// Returns object with data about gesture
	// It's created only once and then reused
	_initDragReleaseAnimationData  = function() {
		// temp local vars
		var lastFlickDuration,
			tempReleasePos;

		// s = this
		var s = {
			lastFlickOffset: {},
			lastFlickDist: {},
			lastFlickSpeed: {},
			slowDownRatio:  {},
			slowDownRatioReverse:  {},
			speedDecelerationRatio:  {},
			speedDecelerationRatioAbs:  {},
			distanceOffset:  {},
			backAnimDestination: {},
			backAnimStarted: {},
			calculateSwipeSpeed: function(axis) {
				

				if( _posPoints.length > 1) {
					lastFlickDuration = _getCurrentTime() - _gestureCheckSpeedTime + 50;
					tempReleasePos = _posPoints[_posPoints.length-2][axis];
				} else {
					lastFlickDuration = _getCurrentTime() - _gestureStartTime; // total gesture duration
					tempReleasePos = _startPoint[axis];
				}
				s.lastFlickOffset[axis] = _currPoint[axis] - tempReleasePos;
				s.lastFlickDist[axis] = Math.abs(s.lastFlickOffset[axis]);
				if(s.lastFlickDist[axis] > 20) {
					s.lastFlickSpeed[axis] = s.lastFlickOffset[axis] / lastFlickDuration;
				} else {
					s.lastFlickSpeed[axis] = 0;
				}
				if( Math.abs(s.lastFlickSpeed[axis]) < 0.1 ) {
					s.lastFlickSpeed[axis] = 0;
				}
				
				s.slowDownRatio[axis] = 0.95;
				s.slowDownRatioReverse[axis] = 1 - s.slowDownRatio[axis];
				s.speedDecelerationRatio[axis] = 1;
			},

			calculateOverBoundsAnimOffset: function(axis, speed) {
				if(!s.backAnimStarted[axis]) {

					if(_panOffset[axis] > _currPanBounds.min[axis]) {
						s.backAnimDestination[axis] = _currPanBounds.min[axis];
						
					} else if(_panOffset[axis] < _currPanBounds.max[axis]) {
						s.backAnimDestination[axis] = _currPanBounds.max[axis];
					}

					if(s.backAnimDestination[axis] !== undefined) {
						s.slowDownRatio[axis] = 0.7;
						s.slowDownRatioReverse[axis] = 1 - s.slowDownRatio[axis];
						if(s.speedDecelerationRatioAbs[axis] < 0.05) {

							s.lastFlickSpeed[axis] = 0;
							s.backAnimStarted[axis] = true;

							_animateProp('bounceZoomPan'+axis,_panOffset[axis], 
								s.backAnimDestination[axis], 
								speed || 300, 
								framework.easing.sine.out, 
								function(pos) {
									_panOffset[axis] = pos;
									_applyCurrentZoomPan();
								}
							);

						}
					}
				}
			},

			// Reduces the speed by slowDownRatio (per 10ms)
			calculateAnimOffset: function(axis) {
				if(!s.backAnimStarted[axis]) {
					s.speedDecelerationRatio[axis] = s.speedDecelerationRatio[axis] * (s.slowDownRatio[axis] + 
												s.slowDownRatioReverse[axis] - 
												s.slowDownRatioReverse[axis] * s.timeDiff / 10);

					s.speedDecelerationRatioAbs[axis] = Math.abs(s.lastFlickSpeed[axis] * s.speedDecelerationRatio[axis]);
					s.distanceOffset[axis] = s.lastFlickSpeed[axis] * s.speedDecelerationRatio[axis] * s.timeDiff;
					_panOffset[axis] += s.distanceOffset[axis];

				}
			},

			panAnimLoop: function() {
				if ( _animations.zoomPan ) {
					_animations.zoomPan.raf = _requestAF(s.panAnimLoop);

					s.now = _getCurrentTime();
					s.timeDiff = s.now - s.lastNow;
					s.lastNow = s.now;
					
					s.calculateAnimOffset('x');
					s.calculateAnimOffset('y');

					_applyCurrentZoomPan();
					
					s.calculateOverBoundsAnimOffset('x');
					s.calculateOverBoundsAnimOffset('y');


					if (s.speedDecelerationRatioAbs.x < 0.05 && s.speedDecelerationRatioAbs.y < 0.05) {

						// round pan position
						_panOffset.x = Math.round(_panOffset.x);
						_panOffset.y = Math.round(_panOffset.y);
						_applyCurrentZoomPan();
						
						_stopAnimation('zoomPan');
						return;
					}
				}

			}
		};
		return s;
	},

	_completePanGesture = function(animData) {
		// calculate swipe speed for Y axis (paanning)
		animData.calculateSwipeSpeed('y');

		_currPanBounds = self.currItem.bounds;
		
		animData.backAnimDestination = {};
		animData.backAnimStarted = {};

		// Avoid acceleration animation if speed is too low
		if(Math.abs(animData.lastFlickSpeed.x) <= 0.05 && Math.abs(animData.lastFlickSpeed.y) <= 0.05 ) {
			animData.speedDecelerationRatioAbs.x = animData.speedDecelerationRatioAbs.y = 0;

			// Run pan drag release animation. E.g. if you drag image and release finger without momentum.
			animData.calculateOverBoundsAnimOffset('x');
			animData.calculateOverBoundsAnimOffset('y');
			return true;
		}

		// Animation loop that controls the acceleration after pan gesture ends
		_registerStartAnimation('zoomPan');
		animData.lastNow = _getCurrentTime();
		animData.panAnimLoop();
	},


	_finishSwipeMainScrollGesture = function(gestureType, _releaseAnimData) {
		var itemChanged;
		if(!_mainScrollAnimating) {
			_currZoomedItemIndex = _currentItemIndex;
		}


		
		var itemsDiff;

		if(gestureType === 'swipe') {
			var totalShiftDist = _currPoint.x - _startPoint.x,
				isFastLastFlick = _releaseAnimData.lastFlickDist.x < 10;

			// if container is shifted for more than MIN_SWIPE_DISTANCE, 
			// and last flick gesture was in right direction
			if(totalShiftDist > MIN_SWIPE_DISTANCE && 
				(isFastLastFlick || _releaseAnimData.lastFlickOffset.x > 20) ) {
				// go to prev item
				itemsDiff = -1;
			} else if(totalShiftDist < -MIN_SWIPE_DISTANCE && 
				(isFastLastFlick || _releaseAnimData.lastFlickOffset.x < -20) ) {
				// go to next item
				itemsDiff = 1;
			}
		}

		var nextCircle;

		if(itemsDiff) {
			
			_currentItemIndex += itemsDiff;

			if(_currentItemIndex < 0) {
				_currentItemIndex = _options.loop ? _getNumItems()-1 : 0;
				nextCircle = true;
			} else if(_currentItemIndex >= _getNumItems()) {
				_currentItemIndex = _options.loop ? 0 : _getNumItems()-1;
				nextCircle = true;
			}

			if(!nextCircle || _options.loop) {
				_indexDiff += itemsDiff;
				_currPositionIndex -= itemsDiff;
				itemChanged = true;
			}
			

			
		}

		var animateToX = _slideSize.x * _currPositionIndex;
		var animateToDist = Math.abs( animateToX - _mainScrollPos.x );
		var finishAnimDuration;


		if(!itemChanged && animateToX > _mainScrollPos.x !== _releaseAnimData.lastFlickSpeed.x > 0) {
			// "return to current" duration, e.g. when dragging from slide 0 to -1
			finishAnimDuration = 333; 
		} else {
			finishAnimDuration = Math.abs(_releaseAnimData.lastFlickSpeed.x) > 0 ? 
									animateToDist / Math.abs(_releaseAnimData.lastFlickSpeed.x) : 
									333;

			finishAnimDuration = Math.min(finishAnimDuration, 400);
			finishAnimDuration = Math.max(finishAnimDuration, 250);
		}

		if(_currZoomedItemIndex === _currentItemIndex) {
			itemChanged = false;
		}
		
		_mainScrollAnimating = true;
		
		_shout('mainScrollAnimStart');

		_animateProp('mainScroll', _mainScrollPos.x, animateToX, finishAnimDuration, framework.easing.cubic.out, 
			_moveMainScroll,
			function() {
				_stopAllAnimations();
				_mainScrollAnimating = false;
				_currZoomedItemIndex = -1;
				
				if(itemChanged || _currZoomedItemIndex !== _currentItemIndex) {
					self.updateCurrItem();
				}
				
				_shout('mainScrollAnimComplete');
			}
		);

		if(itemChanged) {
			self.updateCurrItem(true);
		}

		return itemChanged;
	},

	_calculateZoomLevel = function(touchesDistance) {
		return  1 / _startPointsDistance * touchesDistance * _startZoomLevel;
	},

	// Resets zoom if it's out of bounds
	_completeZoomGesture = function() {
		var destZoomLevel = _currZoomLevel,
			minZoomLevel = _getMinZoomLevel(),
			maxZoomLevel = _getMaxZoomLevel();

		if ( _currZoomLevel < minZoomLevel ) {
			destZoomLevel = minZoomLevel;
		} else if ( _currZoomLevel > maxZoomLevel ) {
			destZoomLevel = maxZoomLevel;
		}

		var destOpacity = 1,
			onUpdate,
			initialOpacity = _bgOpacity;

		if(_opacityChanged && !_isZoomingIn && !_wasOverInitialZoom && _currZoomLevel < minZoomLevel) {
			//_closedByScroll = true;
			self.close();
			return true;
		}

		if(_opacityChanged) {
			onUpdate = function(now) {
				_applyBgOpacity(  (destOpacity - initialOpacity) * now + initialOpacity );
			};
		}

		self.zoomTo(destZoomLevel, 0, 200,  framework.easing.cubic.out, onUpdate);
		return true;
	};


_registerModule('Gestures', {
	publicMethods: {

		initGestures: function() {

			// helper function that builds touch/pointer/mouse events
			var addEventNames = function(pref, down, move, up, cancel) {
				_dragStartEvent = pref + down;
				_dragMoveEvent = pref + move;
				_dragEndEvent = pref + up;
				if(cancel) {
					_dragCancelEvent = pref + cancel;
				} else {
					_dragCancelEvent = '';
				}
			};

			_pointerEventEnabled = _features.pointerEvent;
			if(_pointerEventEnabled && _features.touch) {
				// we don't need touch events, if browser supports pointer events
				_features.touch = false;
			}

			if(_pointerEventEnabled) {
				if(navigator.pointerEnabled) {
					addEventNames('pointer', 'down', 'move', 'up', 'cancel');
				} else {
					// IE10 pointer events are case-sensitive
					addEventNames('MSPointer', 'Down', 'Move', 'Up', 'Cancel');
				}
			} else if(_features.touch) {
				addEventNames('touch', 'start', 'move', 'end', 'cancel');
				_likelyTouchDevice = true;
			} else {
				addEventNames('mouse', 'down', 'move', 'up');	
			}

			_upMoveEvents = _dragMoveEvent + ' ' + _dragEndEvent  + ' ' +  _dragCancelEvent;
			_downEvents = _dragStartEvent;

			if(_pointerEventEnabled && !_likelyTouchDevice) {
				_likelyTouchDevice = (navigator.maxTouchPoints > 1) || (navigator.msMaxTouchPoints > 1);
			}
			// make variable public
			self.likelyTouchDevice = _likelyTouchDevice; 
			
			_globalEventHandlers[_dragStartEvent] = _onDragStart;
			_globalEventHandlers[_dragMoveEvent] = _onDragMove;
			_globalEventHandlers[_dragEndEvent] = _onDragRelease; // the Kraken

			if(_dragCancelEvent) {
				_globalEventHandlers[_dragCancelEvent] = _globalEventHandlers[_dragEndEvent];
			}

			// Bind mouse events on device with detected hardware touch support, in case it supports multiple types of input.
			if(_features.touch) {
				_downEvents += ' mousedown';
				_upMoveEvents += ' mousemove mouseup';
				_globalEventHandlers.mousedown = _globalEventHandlers[_dragStartEvent];
				_globalEventHandlers.mousemove = _globalEventHandlers[_dragMoveEvent];
				_globalEventHandlers.mouseup = _globalEventHandlers[_dragEndEvent];
			}

			if(!_likelyTouchDevice) {
				// don't allow pan to next slide from zoomed state on Desktop
				_options.allowPanToNext = false;
			}
		}

	}
});


/*>>gestures*/

/*>>show-hide-transition*/
/**
 * show-hide-transition.js:
 *
 * Manages initial opening or closing transition.
 *
 * If you're not planning to use transition for gallery at all,
 * you may set options hideAnimationDuration and showAnimationDuration to 0,
 * and just delete startAnimation function.
 * 
 */


var _showOrHideTimeout,
	_showOrHide = function(item, img, out, completeFn) {

		if(_showOrHideTimeout) {
			clearTimeout(_showOrHideTimeout);
		}

		_initialZoomRunning = true;
		_initialContentSet = true;
		
		// dimensions of small thumbnail {x:,y:,w:}.
		// Height is optional, as calculated based on large image.
		var thumbBounds; 
		if(item.initialLayout) {
			thumbBounds = item.initialLayout;
			item.initialLayout = null;
		} else {
			thumbBounds = _options.getThumbBoundsFn && _options.getThumbBoundsFn(_currentItemIndex);
		}

		var duration = out ? _options.hideAnimationDuration : _options.showAnimationDuration;

		var onComplete = function() {
			_stopAnimation('initialZoom');
			if(!out) {
				_applyBgOpacity(1);
				if(img) {
					img.style.display = 'block';
				}
				framework.addClass(template, 'pswp--animated-in');
				_shout('initialZoom' + (out ? 'OutEnd' : 'InEnd'));
			} else {
				self.template.removeAttribute('style');
				self.bg.removeAttribute('style');
			}

			if(completeFn) {
				completeFn();
			}
			_initialZoomRunning = false;
		};

		// if bounds aren't provided, just open gallery without animation
		if(!duration || !thumbBounds || thumbBounds.x === undefined) {

			_shout('initialZoom' + (out ? 'Out' : 'In') );

			_currZoomLevel = item.initialZoomLevel;
			_equalizePoints(_panOffset,  item.initialPosition );
			_applyCurrentZoomPan();

			template.style.opacity = out ? 0 : 1;
			_applyBgOpacity(1);

			if(duration) {
				setTimeout(function() {
					onComplete();
				}, duration);
			} else {
				onComplete();
			}

			return;
		}

		var startAnimation = function() {
			var closeWithRaf = _closedByScroll,
				fadeEverything = !self.currItem.src || self.currItem.loadError || _options.showHideOpacity;
			
			// apply hw-acceleration to image
			if(item.miniImg) {
				item.miniImg.style.webkitBackfaceVisibility = 'hidden';
			}

			if(!out) {
				_currZoomLevel = thumbBounds.w / item.w;
				_panOffset.x = thumbBounds.x;
				_panOffset.y = thumbBounds.y - _initalWindowScrollY;

				self[fadeEverything ? 'template' : 'bg'].style.opacity = 0.001;
				_applyCurrentZoomPan();
			}

			_registerStartAnimation('initialZoom');
			
			if(out && !closeWithRaf) {
				framework.removeClass(template, 'pswp--animated-in');
			}

			if(fadeEverything) {
				if(out) {
					framework[ (closeWithRaf ? 'remove' : 'add') + 'Class' ](template, 'pswp--animate_opacity');
				} else {
					setTimeout(function() {
						framework.addClass(template, 'pswp--animate_opacity');
					}, 30);
				}
			}

			_showOrHideTimeout = setTimeout(function() {

				_shout('initialZoom' + (out ? 'Out' : 'In') );
				

				if(!out) {

					// "in" animation always uses CSS transitions (instead of rAF).
					// CSS transition work faster here, 
					// as developer may also want to animate other things, 
					// like ui on top of sliding area, which can be animated just via CSS
					
					_currZoomLevel = item.initialZoomLevel;
					_equalizePoints(_panOffset,  item.initialPosition );
					_applyCurrentZoomPan();
					_applyBgOpacity(1);

					if(fadeEverything) {
						template.style.opacity = 1;
					} else {
						_applyBgOpacity(1);
					}

					_showOrHideTimeout = setTimeout(onComplete, duration + 20);
				} else {

					// "out" animation uses rAF only when PhotoSwipe is closed by browser scroll, to recalculate position
					var destZoomLevel = thumbBounds.w / item.w,
						initialPanOffset = {
							x: _panOffset.x,
							y: _panOffset.y
						},
						initialZoomLevel = _currZoomLevel,
						initalBgOpacity = _bgOpacity,
						onUpdate = function(now) {
							
							if(now === 1) {
								_currZoomLevel = destZoomLevel;
								_panOffset.x = thumbBounds.x;
								_panOffset.y = thumbBounds.y  - _currentWindowScrollY;
							} else {
								_currZoomLevel = (destZoomLevel - initialZoomLevel) * now + initialZoomLevel;
								_panOffset.x = (thumbBounds.x - initialPanOffset.x) * now + initialPanOffset.x;
								_panOffset.y = (thumbBounds.y - _currentWindowScrollY - initialPanOffset.y) * now + initialPanOffset.y;
							}
							
							_applyCurrentZoomPan();
							if(fadeEverything) {
								template.style.opacity = 1 - now;
							} else {
								_applyBgOpacity( initalBgOpacity - now * initalBgOpacity );
							}
						};

					if(closeWithRaf) {
						_animateProp('initialZoom', 0, 1, duration, framework.easing.cubic.out, onUpdate, onComplete);
					} else {
						onUpdate(1);
						_showOrHideTimeout = setTimeout(onComplete, duration + 20);
					}
				}
			
			}, out ? 25 : 90); // Main purpose of this delay is to give browser time to paint and
					// create composite layers of PhotoSwipe UI parts (background, controls, caption, arrows).
					// Which avoids lag at the beginning of scale transition.
		};
		startAnimation();

		
	};

/*>>show-hide-transition*/

/*>>items-controller*/
/**
*
* Controller manages gallery items, their dimensions, and their content.
* 
*/

var _items,
	_tempPanAreaSize = {},
	_imagesToAppendPool = [],
	_initialContentSet,
	_initialZoomRunning,
	_controllerDefaultOptions = {
		index: 0,
		errorMsg: '<div class="pswp__error-msg"><a href="%url%" target="_blank">The image</a> could not be loaded.</div>',
		forceProgressiveLoading: false, // TODO
		preload: [1,1],
		getNumItemsFn: function() {
			return _items.length;
		}
	};


var _getItemAt,
	_getNumItems,
	_initialIsLoop,
	_getZeroBounds = function() {
		return {
			center:{x:0,y:0}, 
			max:{x:0,y:0}, 
			min:{x:0,y:0}
		};
	},
	_calculateSingleItemPanBounds = function(item, realPanElementW, realPanElementH ) {
		var bounds = item.bounds;

		// position of element when it's centered
		bounds.center.x = Math.round((_tempPanAreaSize.x - realPanElementW) / 2);
		bounds.center.y = Math.round((_tempPanAreaSize.y - realPanElementH) / 2) + item.vGap.top;

		// maximum pan position
		bounds.max.x = (realPanElementW > _tempPanAreaSize.x) ? 
							Math.round(_tempPanAreaSize.x - realPanElementW) : 
							bounds.center.x;
		
		bounds.max.y = (realPanElementH > _tempPanAreaSize.y) ? 
							Math.round(_tempPanAreaSize.y - realPanElementH) + item.vGap.top : 
							bounds.center.y;
		
		// minimum pan position
		bounds.min.x = (realPanElementW > _tempPanAreaSize.x) ? 0 : bounds.center.x;
		bounds.min.y = (realPanElementH > _tempPanAreaSize.y) ? item.vGap.top : bounds.center.y;
	},
	_calculateItemSize = function(item, viewportSize, zoomLevel) {

		if (item.src && !item.loadError) {
			var isInitial = !zoomLevel;
			
			if(isInitial) {
				if(!item.vGap) {
					item.vGap = {top:0,bottom:0};
				}
				// allows overriding vertical margin for individual items
				_shout('parseVerticalMargin', item);
			}


			_tempPanAreaSize.x = viewportSize.x;
			_tempPanAreaSize.y = viewportSize.y - item.vGap.top - item.vGap.bottom;

			if (isInitial) {
				var hRatio = _tempPanAreaSize.x / item.w;
				var vRatio = _tempPanAreaSize.y / item.h;

				item.fitRatio = hRatio < vRatio ? hRatio : vRatio;
				//item.fillRatio = hRatio > vRatio ? hRatio : vRatio;

				var scaleMode = _options.scaleMode;

				if (scaleMode === 'orig') {
					zoomLevel = 1;
				} else if (scaleMode === 'fit') {
					zoomLevel = item.fitRatio;
				}

				if (zoomLevel > 1) {
					zoomLevel = 1;
				}

				item.initialZoomLevel = zoomLevel;
				
				if(!item.bounds) {
					// reuse bounds object
					item.bounds = _getZeroBounds(); 
				}
			}

			if(!zoomLevel) {
				return;
			}

			_calculateSingleItemPanBounds(item, item.w * zoomLevel, item.h * zoomLevel);

			if (isInitial && zoomLevel === item.initialZoomLevel) {
				item.initialPosition = item.bounds.center;
			}

			return item.bounds;
		} else {
			item.w = item.h = 0;
			item.initialZoomLevel = item.fitRatio = 1;
			item.bounds = _getZeroBounds();
			item.initialPosition = item.bounds.center;

			// if it's not image, we return zero bounds (content is not zoomable)
			return item.bounds;
		}
		
	},

	


	_appendImage = function(index, item, baseDiv, img, preventAnimation, keepPlaceholder) {
		

		if(item.loadError) {
			return;
		}

		if(img) {

			item.imageAppended = true;
			_setImageSize(item, img, (item === self.currItem && _renderMaxResolution) );
			
			baseDiv.appendChild(img);

			if(keepPlaceholder) {
				setTimeout(function() {
					if(item && item.loaded && item.placeholder) {
						item.placeholder.style.display = 'none';
						item.placeholder = null;
					}
				}, 500);
			}
		}
	},
	


	_preloadImage = function(item) {
		item.loading = true;
		item.loaded = false;
		var img = item.img = framework.createEl('pswp__img', 'img');
		var onComplete = function() {
			item.loading = false;
			item.loaded = true;

			if(item.loadComplete) {
				item.loadComplete(item);
			} else {
				item.img = null; // no need to store image object
			}
			img.onload = img.onerror = null;
			img = null;
		};
		img.onload = onComplete;
		img.onerror = function() {
			item.loadError = true;
			onComplete();
		};		

		img.src = item.src;// + '?a=' + Math.random();

		return img;
	},
	_checkForError = function(item, cleanUp) {
		if(item.src && item.loadError && item.container) {

			if(cleanUp) {
				item.container.innerHTML = '';
			}

			item.container.innerHTML = _options.errorMsg.replace('%url%',  item.src );
			return true;
			
		}
	},
	_setImageSize = function(item, img, maxRes) {
		if(!item.src) {
			return;
		}

		if(!img) {
			img = item.container.lastChild;
		}

		var w = maxRes ? item.w : Math.round(item.w * item.fitRatio),
			h = maxRes ? item.h : Math.round(item.h * item.fitRatio);
		
		if(item.placeholder && !item.loaded) {
			item.placeholder.style.width = w + 'px';
			item.placeholder.style.height = h + 'px';
		}

		img.style.width = w + 'px';
		img.style.height = h + 'px';
	},
	_appendImagesPool = function() {

		if(_imagesToAppendPool.length) {
			var poolItem;

			for(var i = 0; i < _imagesToAppendPool.length; i++) {
				poolItem = _imagesToAppendPool[i];
				if( poolItem.holder.index === poolItem.index ) {
					_appendImage(poolItem.index, poolItem.item, poolItem.baseDiv, poolItem.img, false, poolItem.clearPlaceholder);
				}
			}
			_imagesToAppendPool = [];
		}
	};
	


_registerModule('Controller', {

	publicMethods: {

		lazyLoadItem: function(index) {
			index = _getLoopedId(index);
			var item = _getItemAt(index);

			if(!item || ((item.loaded || item.loading) && !_itemsNeedUpdate)) {
				return;
			}

			_shout('gettingData', index, item);

			if (!item.src) {
				return;
			}

			_preloadImage(item);
		},
		initController: function() {
			framework.extend(_options, _controllerDefaultOptions, true);
			self.items = _items = items;
			_getItemAt = self.getItemAt;
			_getNumItems = _options.getNumItemsFn; //self.getNumItems;



			_initialIsLoop = _options.loop;
			if(_getNumItems() < 3) {
				_options.loop = false; // disable loop if less then 3 items
			}

			_listen('beforeChange', function(diff) {

				var p = _options.preload,
					isNext = diff === null ? true : (diff >= 0),
					preloadBefore = Math.min(p[0], _getNumItems() ),
					preloadAfter = Math.min(p[1], _getNumItems() ),
					i;


				for(i = 1; i <= (isNext ? preloadAfter : preloadBefore); i++) {
					self.lazyLoadItem(_currentItemIndex+i);
				}
				for(i = 1; i <= (isNext ? preloadBefore : preloadAfter); i++) {
					self.lazyLoadItem(_currentItemIndex-i);
				}
			});

			_listen('initialLayout', function() {
				self.currItem.initialLayout = _options.getThumbBoundsFn && _options.getThumbBoundsFn(_currentItemIndex);
			});

			_listen('mainScrollAnimComplete', _appendImagesPool);
			_listen('initialZoomInEnd', _appendImagesPool);



			_listen('destroy', function() {
				var item;
				for(var i = 0; i < _items.length; i++) {
					item = _items[i];
					// remove reference to DOM elements, for GC
					if(item.container) {
						item.container = null; 
					}
					if(item.placeholder) {
						item.placeholder = null;
					}
					if(item.img) {
						item.img = null;
					}
					if(item.preloader) {
						item.preloader = null;
					}
					if(item.loadError) {
						item.loaded = item.loadError = false;
					}
				}
				_imagesToAppendPool = null;
			});
		},


		getItemAt: function(index) {
			if (index >= 0) {
				return _items[index] !== undefined ? _items[index] : false;
			}
			return false;
		},

		allowProgressiveImg: function() {
			// 1. Progressive image loading isn't working on webkit/blink 
			//    when hw-acceleration (e.g. translateZ) is applied to IMG element.
			//    That's why in PhotoSwipe parent element gets zoom transform, not image itself.
			//    
			// 2. Progressive image loading sometimes blinks in webkit/blink when applying animation to parent element.
			//    That's why it's disabled on touch devices (mainly because of swipe transition)
			//    
			// 3. Progressive image loading sometimes doesn't work in IE (up to 11).

			// Don't allow progressive loading on non-large touch devices
			return _options.forceProgressiveLoading || !_likelyTouchDevice || _options.mouseUsed || screen.width > 1200; 
			// 1200 - to eliminate touch devices with large screen (like Chromebook Pixel)
		},

		setContent: function(holder, index) {

			if(_options.loop) {
				index = _getLoopedId(index);
			}

			var prevItem = self.getItemAt(holder.index);
			if(prevItem) {
				prevItem.container = null;
			}
	
			var item = self.getItemAt(index),
				img;
			
			if(!item) {
				holder.el.innerHTML = '';
				return;
			}

			// allow to override data
			_shout('gettingData', index, item);

			holder.index = index;
			holder.item = item;

			// base container DIV is created only once for each of 3 holders
			var baseDiv = item.container = framework.createEl('pswp__zoom-wrap'); 

			

			if(!item.src && item.html) {
				if(item.html.tagName) {
					baseDiv.appendChild(item.html);
				} else {
					baseDiv.innerHTML = item.html;
				}
			}

			_checkForError(item);

			_calculateItemSize(item, _viewportSize);
			
			if(item.src && !item.loadError && !item.loaded) {

				item.loadComplete = function(item) {

					// gallery closed before image finished loading
					if(!_isOpen) {
						return;
					}

					// check if holder hasn't changed while image was loading
					if(holder && holder.index === index ) {
						if( _checkForError(item, true) ) {
							item.loadComplete = item.img = null;
							_calculateItemSize(item, _viewportSize);
							_applyZoomPanToItem(item);

							if(holder.index === _currentItemIndex) {
								// recalculate dimensions
								self.updateCurrZoomItem();
							}
							return;
						}
						if( !item.imageAppended ) {
							if(_features.transform && (_mainScrollAnimating || _initialZoomRunning) ) {
								_imagesToAppendPool.push({
									item:item,
									baseDiv:baseDiv,
									img:item.img,
									index:index,
									holder:holder,
									clearPlaceholder:true
								});
							} else {
								_appendImage(index, item, baseDiv, item.img, _mainScrollAnimating || _initialZoomRunning, true);
							}
						} else {
							// remove preloader & mini-img
							if(!_initialZoomRunning && item.placeholder) {
								item.placeholder.style.display = 'none';
								item.placeholder = null;
							}
						}
					}

					item.loadComplete = null;
					item.img = null; // no need to store image element after it's added

					_shout('imageLoadComplete', index, item);
				};

				if(framework.features.transform) {
					
					var placeholderClassName = 'pswp__img pswp__img--placeholder'; 
					placeholderClassName += (item.msrc ? '' : ' pswp__img--placeholder--blank');

					var placeholder = framework.createEl(placeholderClassName, item.msrc ? 'img' : '');
					if(item.msrc) {
						placeholder.src = item.msrc;
					}
					
					_setImageSize(item, placeholder);

					baseDiv.appendChild(placeholder);
					item.placeholder = placeholder;

				}
				

				

				if(!item.loading) {
					_preloadImage(item);
				}


				if( self.allowProgressiveImg() ) {
					// just append image
					if(!_initialContentSet && _features.transform) {
						_imagesToAppendPool.push({
							item:item, 
							baseDiv:baseDiv, 
							img:item.img, 
							index:index, 
							holder:holder
						});
					} else {
						_appendImage(index, item, baseDiv, item.img, true, true);
					}
				}
				
			} else if(item.src && !item.loadError) {
				// image object is created every time, due to bugs of image loading & delay when switching images
				img = framework.createEl('pswp__img', 'img');
				img.style.opacity = 1;
				img.src = item.src;
				_setImageSize(item, img);
				_appendImage(index, item, baseDiv, img, true);
			}
			

			if(!_initialContentSet && index === _currentItemIndex) {
				_currZoomElementStyle = baseDiv.style;
				_showOrHide(item, (img ||item.img) );
			} else {
				_applyZoomPanToItem(item);
			}

			holder.el.innerHTML = '';
			holder.el.appendChild(baseDiv);
		},

		cleanSlide: function( item ) {
			if(item.img ) {
				item.img.onload = item.img.onerror = null;
			}
			item.loaded = item.loading = item.img = item.imageAppended = false;
		}

	}
});

/*>>items-controller*/

/*>>tap*/
/**
 * tap.js:
 *
 * Displatches tap and double-tap events.
 * 
 */

var tapTimer,
	tapReleasePoint = {},
	_dispatchTapEvent = function(origEvent, releasePoint, pointerType) {		
		var e = document.createEvent( 'CustomEvent' ),
			eDetail = {
				origEvent:origEvent, 
				target:origEvent.target, 
				releasePoint: releasePoint, 
				pointerType:pointerType || 'touch'
			};

		e.initCustomEvent( 'pswpTap', true, true, eDetail );
		origEvent.target.dispatchEvent(e);
	};

_registerModule('Tap', {
	publicMethods: {
		initTap: function() {
			_listen('firstTouchStart', self.onTapStart);
			_listen('touchRelease', self.onTapRelease);
			_listen('destroy', function() {
				tapReleasePoint = {};
				tapTimer = null;
			});
		},
		onTapStart: function(touchList) {
			if(touchList.length > 1) {
				clearTimeout(tapTimer);
				tapTimer = null;
			}
		},
		onTapRelease: function(e, releasePoint) {
			if(!releasePoint) {
				return;
			}

			if(!_moved && !_isMultitouch && !_numAnimations) {
				var p0 = releasePoint;
				if(tapTimer) {
					clearTimeout(tapTimer);
					tapTimer = null;

					// Check if taped on the same place
					if ( _isNearbyPoints(p0, tapReleasePoint) ) {
						_shout('doubleTap', p0);
						return;
					}
				}

				if(releasePoint.type === 'mouse') {
					_dispatchTapEvent(e, releasePoint, 'mouse');
					return;
				}

				var clickedTagName = e.target.tagName.toUpperCase();
				// avoid double tap delay on buttons and elements that have class pswp__single-tap
				if(clickedTagName === 'BUTTON' || framework.hasClass(e.target, 'pswp__single-tap') ) {
					_dispatchTapEvent(e, releasePoint);
					return;
				}

				_equalizePoints(tapReleasePoint, p0);

				tapTimer = setTimeout(function() {
					_dispatchTapEvent(e, releasePoint);
					tapTimer = null;
				}, 300);
			}
		}
	}
});

/*>>tap*/

/*>>desktop-zoom*/
/**
 *
 * desktop-zoom.js:
 *
 * - Binds mousewheel event for paning zoomed image.
 * - Manages "dragging", "zoomed-in", "zoom-out" classes.
 *   (which are used for cursors and zoom icon)
 * - Adds toggleDesktopZoom function.
 * 
 */

var _wheelDelta;
	
_registerModule('DesktopZoom', {

	publicMethods: {

		initDesktopZoom: function() {

			if(_oldIE) {
				// no zoom for old IE (<=8)
				return;
			}

			if(_likelyTouchDevice) {
				// if detected hardware touch support, we wait until mouse is used,
				// and only then apply desktop-zoom features
				_listen('mouseUsed', function() {
					self.setupDesktopZoom();
				});
			} else {
				self.setupDesktopZoom(true);
			}

		},

		setupDesktopZoom: function(onInit) {

			_wheelDelta = {};

			var events = 'wheel mousewheel DOMMouseScroll';
			
			_listen('bindEvents', function() {
				framework.bind(template, events,  self.handleMouseWheel);
			});

			_listen('unbindEvents', function() {
				if(_wheelDelta) {
					framework.unbind(template, events, self.handleMouseWheel);
				}
			});

			self.mouseZoomedIn = false;

			var hasDraggingClass,
				updateZoomable = function() {
					if(self.mouseZoomedIn) {
						framework.removeClass(template, 'pswp--zoomed-in');
						self.mouseZoomedIn = false;
					}
					if(_currZoomLevel < 1) {
						framework.addClass(template, 'pswp--zoom-allowed');
					} else {
						framework.removeClass(template, 'pswp--zoom-allowed');
					}
					removeDraggingClass();
				},
				removeDraggingClass = function() {
					if(hasDraggingClass) {
						framework.removeClass(template, 'pswp--dragging');
						hasDraggingClass = false;
					}
				};

			_listen('resize' , updateZoomable);
			_listen('afterChange' , updateZoomable);
			_listen('pointerDown', function() {
				if(self.mouseZoomedIn) {
					hasDraggingClass = true;
					framework.addClass(template, 'pswp--dragging');
				}
			});
			_listen('pointerUp', removeDraggingClass);

			if(!onInit) {
				updateZoomable();
			}
			
		},

		handleMouseWheel: function(e) {

			if(_currZoomLevel <= self.currItem.fitRatio) {
				if( _options.modal ) {

					if (!_options.closeOnScroll || _numAnimations || _isDragging) {
						e.preventDefault();
					} else if(_transformKey && Math.abs(e.deltaY) > 2) {
						// close PhotoSwipe
						// if browser supports transforms & scroll changed enough
						_closedByScroll = true;
						self.close();
					}

				}
				return true;
			}

			// allow just one event to fire
			e.stopPropagation();

			// https://developer.mozilla.org/en-US/docs/Web/Events/wheel
			_wheelDelta.x = 0;

			if('deltaX' in e) {
				if(e.deltaMode === 1 /* DOM_DELTA_LINE */) {
					// 18 - average line height
					_wheelDelta.x = e.deltaX * 18;
					_wheelDelta.y = e.deltaY * 18;
				} else {
					_wheelDelta.x = e.deltaX;
					_wheelDelta.y = e.deltaY;
				}
			} else if('wheelDelta' in e) {
				if(e.wheelDeltaX) {
					_wheelDelta.x = -0.16 * e.wheelDeltaX;
				}
				if(e.wheelDeltaY) {
					_wheelDelta.y = -0.16 * e.wheelDeltaY;
				} else {
					_wheelDelta.y = -0.16 * e.wheelDelta;
				}
			} else if('detail' in e) {
				_wheelDelta.y = e.detail;
			} else {
				return;
			}

			_calculatePanBounds(_currZoomLevel, true);

			var newPanX = _panOffset.x - _wheelDelta.x,
				newPanY = _panOffset.y - _wheelDelta.y;

			// only prevent scrolling in nonmodal mode when not at edges
			if (_options.modal ||
				(
				newPanX <= _currPanBounds.min.x && newPanX >= _currPanBounds.max.x &&
				newPanY <= _currPanBounds.min.y && newPanY >= _currPanBounds.max.y
				) ) {
				e.preventDefault();
			}

			// TODO: use rAF instead of mousewheel?
			self.panTo(newPanX, newPanY);
		},

		toggleDesktopZoom: function(centerPoint) {
			centerPoint = centerPoint || {x:_viewportSize.x/2 + _offset.x, y:_viewportSize.y/2 + _offset.y };

			var doubleTapZoomLevel = _options.getDoubleTapZoom(true, self.currItem);
			var zoomOut = _currZoomLevel === doubleTapZoomLevel;
			
			self.mouseZoomedIn = !zoomOut;

			self.zoomTo(zoomOut ? self.currItem.initialZoomLevel : doubleTapZoomLevel, centerPoint, 333);
			framework[ (!zoomOut ? 'add' : 'remove') + 'Class'](template, 'pswp--zoomed-in');
		}

	}
});


/*>>desktop-zoom*/

/*>>history*/
/**
 *
 * history.js:
 *
 * - Back button to close gallery.
 * 
 * - Unique URL for each slide: example.com/&pid=1&gid=3
 *   (where PID is picture index, and GID and gallery index)
 *   
 * - Switch URL when slides change.
 * 
 */


var _historyDefaultOptions = {
	history: true,
	galleryUID: 1
};

var _historyUpdateTimeout,
	_hashChangeTimeout,
	_hashAnimCheckTimeout,
	_hashChangedByScript,
	_hashChangedByHistory,
	_hashReseted,
	_initialHash,
	_historyChanged,
	_closedFromURL,
	_urlChangedOnce,
	_windowLoc,

	_supportsPushState,

	_getHash = function() {
		return _windowLoc.hash.substring(1);
	},
	_cleanHistoryTimeouts = function() {

		if(_historyUpdateTimeout) {
			clearTimeout(_historyUpdateTimeout);
		}

		if(_hashAnimCheckTimeout) {
			clearTimeout(_hashAnimCheckTimeout);
		}
	},

	// pid - Picture index
	// gid - Gallery index
	_parseItemIndexFromURL = function() {
		var hash = _getHash(),
			params = {};

		if(hash.length < 5) { // pid=1
			return params;
		}

		var i, vars = hash.split('&');
		for (i = 0; i < vars.length; i++) {
			if(!vars[i]) {
				continue;
			}
			var pair = vars[i].split('=');	
			if(pair.length < 2) {
				continue;
			}
			params[pair[0]] = pair[1];
		}
		if(_options.galleryPIDs) {
			// detect custom pid in hash and search for it among the items collection
			var searchfor = params.pid;
			params.pid = 0; // if custom pid cannot be found, fallback to the first item
			for(i = 0; i < _items.length; i++) {
				if(_items[i].pid === searchfor) {
					params.pid = i;
					break;
				}
			}
		} else {
			params.pid = parseInt(params.pid,10)-1;
		}
		if( params.pid < 0 ) {
			params.pid = 0;
		}
		return params;
	},
	_updateHash = function() {

		if(_hashAnimCheckTimeout) {
			clearTimeout(_hashAnimCheckTimeout);
		}


		if(_numAnimations || _isDragging) {
			// changing browser URL forces layout/paint in some browsers, which causes noticable lag during animation
			// that's why we update hash only when no animations running
			_hashAnimCheckTimeout = setTimeout(_updateHash, 500);
			return;
		}
		
		if(_hashChangedByScript) {
			clearTimeout(_hashChangeTimeout);
		} else {
			_hashChangedByScript = true;
		}


		var pid = (_currentItemIndex + 1);
		var item = _getItemAt( _currentItemIndex );
		if(item.hasOwnProperty('pid')) {
			// carry forward any custom pid assigned to the item
			pid = item.pid;
		}
		var newHash = _initialHash + '&'  +  'gid=' + _options.galleryUID + '&' + 'pid=' + pid;

		if(!_historyChanged) {
			if(_windowLoc.hash.indexOf(newHash) === -1) {
				_urlChangedOnce = true;
			}
			// first time - add new hisory record, then just replace
		}

		var newURL = _windowLoc.href.split('#')[0] + '#' +  newHash;

		if( _supportsPushState ) {

			if('#' + newHash !== window.location.hash) {
				history[_historyChanged ? 'replaceState' : 'pushState']('', document.title, newURL);
			}

		} else {
			if(_historyChanged) {
				_windowLoc.replace( newURL );
			} else {
				_windowLoc.hash = newHash;
			}
		}
		
		

		_historyChanged = true;
		_hashChangeTimeout = setTimeout(function() {
			_hashChangedByScript = false;
		}, 60);
	};



	

_registerModule('History', {

	

	publicMethods: {
		initHistory: function() {

			framework.extend(_options, _historyDefaultOptions, true);

			if( !_options.history ) {
				return;
			}


			_windowLoc = window.location;
			_urlChangedOnce = false;
			_closedFromURL = false;
			_historyChanged = false;
			_initialHash = _getHash();
			_supportsPushState = ('pushState' in history);


			if(_initialHash.indexOf('gid=') > -1) {
				_initialHash = _initialHash.split('&gid=')[0];
				_initialHash = _initialHash.split('?gid=')[0];
			}
			

			_listen('afterChange', self.updateURL);
			_listen('unbindEvents', function() {
				framework.unbind(window, 'hashchange', self.onHashChange);
			});


			var returnToOriginal = function() {
				_hashReseted = true;
				if(!_closedFromURL) {

					if(_urlChangedOnce) {
						history.back();
					} else {

						if(_initialHash) {
							_windowLoc.hash = _initialHash;
						} else {
							if (_supportsPushState) {

								// remove hash from url without refreshing it or scrolling to top
								history.pushState('', document.title,  _windowLoc.pathname + _windowLoc.search );
							} else {
								_windowLoc.hash = '';
							}
						}
					}
					
				}

				_cleanHistoryTimeouts();
			};


			_listen('unbindEvents', function() {
				if(_closedByScroll) {
					// if PhotoSwipe is closed by scroll, we go "back" before the closing animation starts
					// this is done to keep the scroll position
					returnToOriginal();
				}
			});
			_listen('destroy', function() {
				if(!_hashReseted) {
					returnToOriginal();
				}
			});
			_listen('firstUpdate', function() {
				_currentItemIndex = _parseItemIndexFromURL().pid;
			});

			

			
			var index = _initialHash.indexOf('pid=');
			if(index > -1) {
				_initialHash = _initialHash.substring(0, index);
				if(_initialHash.slice(-1) === '&') {
					_initialHash = _initialHash.slice(0, -1);
				}
			}
			

			setTimeout(function() {
				if(_isOpen) { // hasn't destroyed yet
					framework.bind(window, 'hashchange', self.onHashChange);
				}
			}, 40);
			
		},
		onHashChange: function() {

			if(_getHash() === _initialHash) {

				_closedFromURL = true;
				self.close();
				return;
			}
			if(!_hashChangedByScript) {

				_hashChangedByHistory = true;
				self.goTo( _parseItemIndexFromURL().pid );
				_hashChangedByHistory = false;
			}
			
		},
		updateURL: function() {

			// Delay the update of URL, to avoid lag during transition, 
			// and to not to trigger actions like "refresh page sound" or "blinking favicon" to often
			
			_cleanHistoryTimeouts();
			

			if(_hashChangedByHistory) {
				return;
			}

			if(!_historyChanged) {
				_updateHash(); // first time
			} else {
				_historyUpdateTimeout = setTimeout(_updateHash, 800);
			}
		}
	
	}
});


/*>>history*/
	framework.extend(self, publicMethods); };
	return PhotoSwipe;
});

/*! PhotoSwipe Default UI - 4.1.2 - 2017-04-05
* http://photoswipe.com
* Copyright (c) 2017 Dmitry Semenov; */
/**
*
* UI on top of main sliding area (caption, arrows, close button, etc.).
* Built just using public methods/properties of PhotoSwipe.
* 
*/
(function (root, factory) { 
	if (typeof define === 'function' && define.amd) {
		define(factory);
	} else if (typeof exports === 'object') {
		module.exports = factory();
	} else {
		root.PhotoSwipeUI_Default = factory();
	}
})(this, function () {

	'use strict';



var PhotoSwipeUI_Default =
 function(pswp, framework) {

	var ui = this;
	var _overlayUIUpdated = false,
		_controlsVisible = true,
		_fullscrenAPI,
		_controls,
		_captionContainer,
		_fakeCaptionContainer,
		_indexIndicator,
		_shareButton,
		_shareModal,
		_shareModalHidden = true,
		_initalCloseOnScrollValue,
		_isIdle,
		_listen,

		_loadingIndicator,
		_loadingIndicatorHidden,
		_loadingIndicatorTimeout,

		_galleryHasOneSlide,

		_options,
		_defaultUIOptions = {
			barsSize: {top:44, bottom:'auto'},
			closeElClasses: ['item', 'caption', 'zoom-wrap', 'ui', 'top-bar'], 
			timeToIdle: 4000, 
			timeToIdleOutside: 1000,
			loadingIndicatorDelay: 1000, // 2s
			
			addCaptionHTMLFn: function(item, captionEl /*, isFake */) {
				if(!item.title) {
					captionEl.children[0].innerHTML = '';
					return false;
				}
				captionEl.children[0].innerHTML = item.title;
				return true;
			},

			closeEl:true,
			captionEl: true,
			fullscreenEl: true,
			zoomEl: true,
			shareEl: true,
			counterEl: true,
			arrowEl: true,
			preloaderEl: true,

			tapToClose: false,
			tapToToggleControls: true,

			clickToCloseNonZoomable: true,

			shareButtons: [
				{id:'facebook', label:'Share on Facebook', url:'https://www.facebook.com/sharer/sharer.php?u={{url}}'},
				{id:'twitter', label:'Tweet', url:'https://twitter.com/intent/tweet?text={{text}}&url={{url}}'},
				{id:'pinterest', label:'Pin it', url:'http://www.pinterest.com/pin/create/button/'+
													'?url={{url}}&media={{image_url}}&description={{text}}'},
				{id:'download', label:'Download image', url:'{{raw_image_url}}', download:true}
			],
			getImageURLForShare: function( /* shareButtonData */ ) {
				return pswp.currItem.src || '';
			},
			getPageURLForShare: function( /* shareButtonData */ ) {
				return window.location.href;
			},
			getTextForShare: function( /* shareButtonData */ ) {
				return pswp.currItem.title || '';
			},
				
			indexIndicatorSep: ' / ',
			fitControlsWidth: 1200

		},
		_blockControlsTap,
		_blockControlsTapTimeout;



	var _onControlsTap = function(e) {
			if(_blockControlsTap) {
				return true;
			}


			e = e || window.event;

			if(_options.timeToIdle && _options.mouseUsed && !_isIdle) {
				// reset idle timer
				_onIdleMouseMove();
			}


			var target = e.target || e.srcElement,
				uiElement,
				clickedClass = target.getAttribute('class') || '',
				found;

			for(var i = 0; i < _uiElements.length; i++) {
				uiElement = _uiElements[i];
				if(uiElement.onTap && clickedClass.indexOf('pswp__' + uiElement.name ) > -1 ) {
					uiElement.onTap();
					found = true;

				}
			}

			if(found) {
				if(e.stopPropagation) {
					e.stopPropagation();
				}
				_blockControlsTap = true;

				// Some versions of Android don't prevent ghost click event 
				// when preventDefault() was called on touchstart and/or touchend.
				// 
				// This happens on v4.3, 4.2, 4.1, 
				// older versions strangely work correctly, 
				// but just in case we add delay on all of them)	
				var tapDelay = framework.features.isOldAndroid ? 600 : 30;
				_blockControlsTapTimeout = setTimeout(function() {
					_blockControlsTap = false;
				}, tapDelay);
			}

		},
		_fitControlsInViewport = function() {
			return !pswp.likelyTouchDevice || _options.mouseUsed || screen.width > _options.fitControlsWidth;
		},
		_togglePswpClass = function(el, cName, add) {
			framework[ (add ? 'add' : 'remove') + 'Class' ](el, 'pswp__' + cName);
		},

		// add class when there is just one item in the gallery
		// (by default it hides left/right arrows and 1ofX counter)
		_countNumItems = function() {
			var hasOneSlide = (_options.getNumItemsFn() === 1);

			if(hasOneSlide !== _galleryHasOneSlide) {
				_togglePswpClass(_controls, 'ui--one-slide', hasOneSlide);
				_galleryHasOneSlide = hasOneSlide;
			}
		},
		_toggleShareModalClass = function() {
			_togglePswpClass(_shareModal, 'share-modal--hidden', _shareModalHidden);
		},
		_toggleShareModal = function() {

			_shareModalHidden = !_shareModalHidden;
			
			
			if(!_shareModalHidden) {
				_toggleShareModalClass();
				setTimeout(function() {
					if(!_shareModalHidden) {
						framework.addClass(_shareModal, 'pswp__share-modal--fade-in');
					}
				}, 30);
			} else {
				framework.removeClass(_shareModal, 'pswp__share-modal--fade-in');
				setTimeout(function() {
					if(_shareModalHidden) {
						_toggleShareModalClass();
					}
				}, 300);
			}
			
			if(!_shareModalHidden) {
				_updateShareURLs();
			}
			return false;
		},

		_openWindowPopup = function(e) {
			e = e || window.event;
			var target = e.target || e.srcElement;

			pswp.shout('shareLinkClick', e, target);

			if(!target.href) {
				return false;
			}

			if( target.hasAttribute('download') ) {
				return true;
			}

			window.open(target.href, 'pswp_share', 'scrollbars=yes,resizable=yes,toolbar=no,'+
										'location=yes,width=550,height=420,top=100,left=' + 
										(window.screen ? Math.round(screen.width / 2 - 275) : 100)  );

			if(!_shareModalHidden) {
				_toggleShareModal();
			}
			
			return false;
		},
		_updateShareURLs = function() {
			var shareButtonOut = '',
				shareButtonData,
				shareURL,
				image_url,
				page_url,
				share_text;

			for(var i = 0; i < _options.shareButtons.length; i++) {
				shareButtonData = _options.shareButtons[i];

				image_url = _options.getImageURLForShare(shareButtonData);
				page_url = _options.getPageURLForShare(shareButtonData);
				share_text = _options.getTextForShare(shareButtonData);

				shareURL = shareButtonData.url.replace('{{url}}', encodeURIComponent(page_url) )
									.replace('{{image_url}}', encodeURIComponent(image_url) )
									.replace('{{raw_image_url}}', image_url )
									.replace('{{text}}', encodeURIComponent(share_text) );

				shareButtonOut += '<a href="' + shareURL + '" target="_blank" '+
									'class="pswp__share--' + shareButtonData.id + '"' +
									(shareButtonData.download ? 'download' : '') + '>' + 
									shareButtonData.label + '</a>';

				if(_options.parseShareButtonOut) {
					shareButtonOut = _options.parseShareButtonOut(shareButtonData, shareButtonOut);
				}
			}
			_shareModal.children[0].innerHTML = shareButtonOut;
			_shareModal.children[0].onclick = _openWindowPopup;

		},
		_hasCloseClass = function(target) {
			for(var  i = 0; i < _options.closeElClasses.length; i++) {
				if( framework.hasClass(target, 'pswp__' + _options.closeElClasses[i]) ) {
					return true;
				}
			}
		},
		_idleInterval,
		_idleTimer,
		_idleIncrement = 0,
		_onIdleMouseMove = function() {
			clearTimeout(_idleTimer);
			_idleIncrement = 0;
			if(_isIdle) {
				ui.setIdle(false);
			}
		},
		_onMouseLeaveWindow = function(e) {
			e = e ? e : window.event;
			var from = e.relatedTarget || e.toElement;
			if (!from || from.nodeName === 'HTML') {
				clearTimeout(_idleTimer);
				_idleTimer = setTimeout(function() {
					ui.setIdle(true);
				}, _options.timeToIdleOutside);
			}
		},
		_setupFullscreenAPI = function() {
			if(_options.fullscreenEl && !framework.features.isOldAndroid) {
				if(!_fullscrenAPI) {
					_fullscrenAPI = ui.getFullscreenAPI();
				}
				if(_fullscrenAPI) {
					framework.bind(document, _fullscrenAPI.eventK, ui.updateFullscreen);
					ui.updateFullscreen();
					framework.addClass(pswp.template, 'pswp--supports-fs');
				} else {
					framework.removeClass(pswp.template, 'pswp--supports-fs');
				}
			}
		},
		_setupLoadingIndicator = function() {
			// Setup loading indicator
			if(_options.preloaderEl) {
			
				_toggleLoadingIndicator(true);

				_listen('beforeChange', function() {

					clearTimeout(_loadingIndicatorTimeout);

					// display loading indicator with delay
					_loadingIndicatorTimeout = setTimeout(function() {

						if(pswp.currItem && pswp.currItem.loading) {

							if( !pswp.allowProgressiveImg() || (pswp.currItem.img && !pswp.currItem.img.naturalWidth)  ) {
								// show preloader if progressive loading is not enabled, 
								// or image width is not defined yet (because of slow connection)
								_toggleLoadingIndicator(false); 
								// items-controller.js function allowProgressiveImg
							}
							
						} else {
							_toggleLoadingIndicator(true); // hide preloader
						}

					}, _options.loadingIndicatorDelay);
					
				});
				_listen('imageLoadComplete', function(index, item) {
					if(pswp.currItem === item) {
						_toggleLoadingIndicator(true);
					}
				});

			}
		},
		_toggleLoadingIndicator = function(hide) {
			if( _loadingIndicatorHidden !== hide ) {
				_togglePswpClass(_loadingIndicator, 'preloader--active', !hide);
				_loadingIndicatorHidden = hide;
			}
		},
		_applyNavBarGaps = function(item) {
			var gap = item.vGap;

			if( _fitControlsInViewport() ) {
				
				var bars = _options.barsSize; 
				if(_options.captionEl && bars.bottom === 'auto') {
					if(!_fakeCaptionContainer) {
						_fakeCaptionContainer = framework.createEl('pswp__caption pswp__caption--fake');
						_fakeCaptionContainer.appendChild( framework.createEl('pswp__caption__center') );
						_controls.insertBefore(_fakeCaptionContainer, _captionContainer);
						framework.addClass(_controls, 'pswp__ui--fit');
					}
					if( _options.addCaptionHTMLFn(item, _fakeCaptionContainer, true) ) {

						var captionSize = _fakeCaptionContainer.clientHeight;
						gap.bottom = parseInt(captionSize,10) || 44;
					} else {
						gap.bottom = bars.top; // if no caption, set size of bottom gap to size of top
					}
				} else {
					gap.bottom = bars.bottom === 'auto' ? 0 : bars.bottom;
				}
				
				// height of top bar is static, no need to calculate it
				gap.top = bars.top;
			} else {
				gap.top = gap.bottom = 0;
			}
		},
		_setupIdle = function() {
			// Hide controls when mouse is used
			if(_options.timeToIdle) {
				_listen('mouseUsed', function() {
					
					framework.bind(document, 'mousemove', _onIdleMouseMove);
					framework.bind(document, 'mouseout', _onMouseLeaveWindow);

					_idleInterval = setInterval(function() {
						_idleIncrement++;
						if(_idleIncrement === 2) {
							ui.setIdle(true);
						}
					}, _options.timeToIdle / 2);
				});
			}
		},
		_setupHidingControlsDuringGestures = function() {

			// Hide controls on vertical drag
			_listen('onVerticalDrag', function(now) {
				if(_controlsVisible && now < 0.95) {
					ui.hideControls();
				} else if(!_controlsVisible && now >= 0.95) {
					ui.showControls();
				}
			});

			// Hide controls when pinching to close
			var pinchControlsHidden;
			_listen('onPinchClose' , function(now) {
				if(_controlsVisible && now < 0.9) {
					ui.hideControls();
					pinchControlsHidden = true;
				} else if(pinchControlsHidden && !_controlsVisible && now > 0.9) {
					ui.showControls();
				}
			});

			_listen('zoomGestureEnded', function() {
				pinchControlsHidden = false;
				if(pinchControlsHidden && !_controlsVisible) {
					ui.showControls();
				}
			});

		};



	var _uiElements = [
		{ 
			name: 'caption', 
			option: 'captionEl',
			onInit: function(el) {  
				_captionContainer = el; 
			} 
		},
		{ 
			name: 'share-modal', 
			option: 'shareEl',
			onInit: function(el) {  
				_shareModal = el;
			},
			onTap: function() {
				_toggleShareModal();
			} 
		},
		{ 
			name: 'button--share', 
			option: 'shareEl',
			onInit: function(el) { 
				_shareButton = el;
			},
			onTap: function() {
				_toggleShareModal();
			} 
		},
		{ 
			name: 'button--zoom', 
			option: 'zoomEl',
			onTap: pswp.toggleDesktopZoom
		},
		{ 
			name: 'counter', 
			option: 'counterEl',
			onInit: function(el) {  
				_indexIndicator = el;
			} 
		},
		{ 
			name: 'button--close', 
			option: 'closeEl',
			onTap: pswp.close
		},
		{ 
			name: 'button--arrow--left', 
			option: 'arrowEl',
			onTap: pswp.prev
		},
		{ 
			name: 'button--arrow--right', 
			option: 'arrowEl',
			onTap: pswp.next
		},
		{ 
			name: 'button--fs', 
			option: 'fullscreenEl',
			onTap: function() {  
				if(_fullscrenAPI.isFullscreen()) {
					_fullscrenAPI.exit();
				} else {
					_fullscrenAPI.enter();
				}
			} 
		},
		{ 
			name: 'preloader', 
			option: 'preloaderEl',
			onInit: function(el) {  
				_loadingIndicator = el;
			} 
		}

	];

	var _setupUIElements = function() {
		var item,
			classAttr,
			uiElement;

		var loopThroughChildElements = function(sChildren) {
			if(!sChildren) {
				return;
			}

			var l = sChildren.length;
			for(var i = 0; i < l; i++) {
				item = sChildren[i];
				classAttr = item.className;

				for(var a = 0; a < _uiElements.length; a++) {
					uiElement = _uiElements[a];

					if(classAttr.indexOf('pswp__' + uiElement.name) > -1  ) {

						if( _options[uiElement.option] ) { // if element is not disabled from options
							
							framework.removeClass(item, 'pswp__element--disabled');
							if(uiElement.onInit) {
								uiElement.onInit(item);
							}
							
							//item.style.display = 'block';
						} else {
							framework.addClass(item, 'pswp__element--disabled');
							//item.style.display = 'none';
						}
					}
				}
			}
		};
		loopThroughChildElements(_controls.children);

		var topBar =  framework.getChildByClass(_controls, 'pswp__top-bar');
		if(topBar) {
			loopThroughChildElements( topBar.children );
		}
	};


	

	ui.init = function() {

		// extend options
		framework.extend(pswp.options, _defaultUIOptions, true);

		// create local link for fast access
		_options = pswp.options;

		// find pswp__ui element
		_controls = framework.getChildByClass(pswp.scrollWrap, 'pswp__ui');

		// create local link
		_listen = pswp.listen;


		_setupHidingControlsDuringGestures();

		// update controls when slides change
		_listen('beforeChange', ui.update);

		// toggle zoom on double-tap
		_listen('doubleTap', function(point) {
			var initialZoomLevel = pswp.currItem.initialZoomLevel;
			if(pswp.getZoomLevel() !== initialZoomLevel) {
				pswp.zoomTo(initialZoomLevel, point, 333);
			} else {
				pswp.zoomTo(_options.getDoubleTapZoom(false, pswp.currItem), point, 333);
			}
		});

		// Allow text selection in caption
		_listen('preventDragEvent', function(e, isDown, preventObj) {
			var t = e.target || e.srcElement;
			if(
				t && 
				t.getAttribute('class') && e.type.indexOf('mouse') > -1 && 
				( t.getAttribute('class').indexOf('__caption') > 0 || (/(SMALL|STRONG|EM)/i).test(t.tagName) ) 
			) {
				preventObj.prevent = false;
			}
		});

		// bind events for UI
		_listen('bindEvents', function() {
			framework.bind(_controls, 'pswpTap click', _onControlsTap);
			framework.bind(pswp.scrollWrap, 'pswpTap', ui.onGlobalTap);

			if(!pswp.likelyTouchDevice) {
				framework.bind(pswp.scrollWrap, 'mouseover', ui.onMouseOver);
			}
		});

		// unbind events for UI
		_listen('unbindEvents', function() {
			if(!_shareModalHidden) {
				_toggleShareModal();
			}

			if(_idleInterval) {
				clearInterval(_idleInterval);
			}
			framework.unbind(document, 'mouseout', _onMouseLeaveWindow);
			framework.unbind(document, 'mousemove', _onIdleMouseMove);
			framework.unbind(_controls, 'pswpTap click', _onControlsTap);
			framework.unbind(pswp.scrollWrap, 'pswpTap', ui.onGlobalTap);
			framework.unbind(pswp.scrollWrap, 'mouseover', ui.onMouseOver);

			if(_fullscrenAPI) {
				framework.unbind(document, _fullscrenAPI.eventK, ui.updateFullscreen);
				if(_fullscrenAPI.isFullscreen()) {
					_options.hideAnimationDuration = 0;
					_fullscrenAPI.exit();
				}
				_fullscrenAPI = null;
			}
		});


		// clean up things when gallery is destroyed
		_listen('destroy', function() {
			if(_options.captionEl) {
				if(_fakeCaptionContainer) {
					_controls.removeChild(_fakeCaptionContainer);
				}
				framework.removeClass(_captionContainer, 'pswp__caption--empty');
			}

			if(_shareModal) {
				_shareModal.children[0].onclick = null;
			}
			framework.removeClass(_controls, 'pswp__ui--over-close');
			framework.addClass( _controls, 'pswp__ui--hidden');
			ui.setIdle(false);
		});
		

		if(!_options.showAnimationDuration) {
			framework.removeClass( _controls, 'pswp__ui--hidden');
		}
		_listen('initialZoomIn', function() {
			if(_options.showAnimationDuration) {
				framework.removeClass( _controls, 'pswp__ui--hidden');
			}
		});
		_listen('initialZoomOut', function() {
			framework.addClass( _controls, 'pswp__ui--hidden');
		});

		_listen('parseVerticalMargin', _applyNavBarGaps);
		
		_setupUIElements();

		if(_options.shareEl && _shareButton && _shareModal) {
			_shareModalHidden = true;
		}

		_countNumItems();

		_setupIdle();

		_setupFullscreenAPI();

		_setupLoadingIndicator();
	};

	ui.setIdle = function(isIdle) {
		_isIdle = isIdle;
		_togglePswpClass(_controls, 'ui--idle', isIdle);
	};

	ui.update = function() {
		// Don't update UI if it's hidden
		if(_controlsVisible && pswp.currItem) {
			
			ui.updateIndexIndicator();

			if(_options.captionEl) {
				_options.addCaptionHTMLFn(pswp.currItem, _captionContainer);

				_togglePswpClass(_captionContainer, 'caption--empty', !pswp.currItem.title);
			}

			_overlayUIUpdated = true;

		} else {
			_overlayUIUpdated = false;
		}

		if(!_shareModalHidden) {
			_toggleShareModal();
		}

		_countNumItems();
	};

	ui.updateFullscreen = function(e) {

		if(e) {
			// some browsers change window scroll position during the fullscreen
			// so PhotoSwipe updates it just in case
			setTimeout(function() {
				pswp.setScrollOffset( 0, framework.getScrollY() );
			}, 50);
		}
		
		// toogle pswp--fs class on root element
		framework[ (_fullscrenAPI.isFullscreen() ? 'add' : 'remove') + 'Class' ](pswp.template, 'pswp--fs');
	};

	ui.updateIndexIndicator = function() {
		if(_options.counterEl) {
			_indexIndicator.innerHTML = (pswp.getCurrentIndex()+1) + 
										_options.indexIndicatorSep + 
										_options.getNumItemsFn();
		}
	};
	
	ui.onGlobalTap = function(e) {
		e = e || window.event;
		var target = e.target || e.srcElement;

		if(_blockControlsTap) {
			return;
		}

		if(e.detail && e.detail.pointerType === 'mouse') {

			// close gallery if clicked outside of the image
			if(_hasCloseClass(target)) {
				pswp.close();
				return;
			}

			if(framework.hasClass(target, 'pswp__img')) {
				if(pswp.getZoomLevel() === 1 && pswp.getZoomLevel() <= pswp.currItem.fitRatio) {
					if(_options.clickToCloseNonZoomable) {
						pswp.close();
					}
				} else {
					pswp.toggleDesktopZoom(e.detail.releasePoint);
				}
			}
			
		} else {

			// tap anywhere (except buttons) to toggle visibility of controls
			if(_options.tapToToggleControls) {
				if(_controlsVisible) {
					ui.hideControls();
				} else {
					ui.showControls();
				}
			}

			// tap to close gallery
			if(_options.tapToClose && (framework.hasClass(target, 'pswp__img') || _hasCloseClass(target)) ) {
				pswp.close();
				return;
			}
			
		}
	};
	ui.onMouseOver = function(e) {
		e = e || window.event;
		var target = e.target || e.srcElement;

		// add class when mouse is over an element that should close the gallery
		_togglePswpClass(_controls, 'ui--over-close', _hasCloseClass(target));
	};

	ui.hideControls = function() {
		framework.addClass(_controls,'pswp__ui--hidden');
		_controlsVisible = false;
	};

	ui.showControls = function() {
		_controlsVisible = true;
		if(!_overlayUIUpdated) {
			ui.update();
		}
		framework.removeClass(_controls,'pswp__ui--hidden');
	};

	ui.supportsFullscreen = function() {
		var d = document;
		return !!(d.exitFullscreen || d.mozCancelFullScreen || d.webkitExitFullscreen || d.msExitFullscreen);
	};

	ui.getFullscreenAPI = function() {
		var dE = document.documentElement,
			api,
			tF = 'fullscreenchange';

		if (dE.requestFullscreen) {
			api = {
				enterK: 'requestFullscreen',
				exitK: 'exitFullscreen',
				elementK: 'fullscreenElement',
				eventK: tF
			};

		} else if(dE.mozRequestFullScreen ) {
			api = {
				enterK: 'mozRequestFullScreen',
				exitK: 'mozCancelFullScreen',
				elementK: 'mozFullScreenElement',
				eventK: 'moz' + tF
			};

			

		} else if(dE.webkitRequestFullscreen) {
			api = {
				enterK: 'webkitRequestFullscreen',
				exitK: 'webkitExitFullscreen',
				elementK: 'webkitFullscreenElement',
				eventK: 'webkit' + tF
			};

		} else if(dE.msRequestFullscreen) {
			api = {
				enterK: 'msRequestFullscreen',
				exitK: 'msExitFullscreen',
				elementK: 'msFullscreenElement',
				eventK: 'MSFullscreenChange'
			};
		}

		if(api) {
			api.enter = function() { 
				// disable close-on-scroll in fullscreen
				_initalCloseOnScrollValue = _options.closeOnScroll; 
				_options.closeOnScroll = false; 

				if(this.enterK === 'webkitRequestFullscreen') {
					pswp.template[this.enterK]( Element.ALLOW_KEYBOARD_INPUT );
				} else {
					return pswp.template[this.enterK](); 
				}
			};
			api.exit = function() { 
				_options.closeOnScroll = _initalCloseOnScrollValue;

				return document[this.exitK](); 

			};
			api.isFullscreen = function() { return document[this.elementK]; };
		}

		return api;
	};



};
return PhotoSwipeUI_Default;


});

// Generated by CoffeeScript 1.10.0

/**
@license Sticky-kit v1.1.3 | MIT | Leaf Corcoran 2015 | http://leafo.net
 */

(function() {
	var $, win;

	$ = window.jQuery;

	win = $(window);

	$.fn.stick_in_parent = function(opts) {
		var doc, elm, enable_bottoming, fn, i, inner_scrolling, len, manual_spacer, offset_top, outer_width, parent_selector, recalc_every, sticky_class;
		if (opts == null) {
			opts = {};
		}
		sticky_class = opts.sticky_class, inner_scrolling = opts.inner_scrolling, recalc_every = opts.recalc_every, parent_selector = opts.parent, offset_top = opts.offset_top, manual_spacer = opts.spacer, enable_bottoming = opts.bottoming;
		if (offset_top == null) {
			offset_top = 0;
		}
		if (parent_selector == null) {
			parent_selector = void 0;
		}
		if (inner_scrolling == null) {
			inner_scrolling = true;
		}
		if (sticky_class == null) {
			sticky_class = 'is_stuck';
		}
		doc = $(document);
		if (enable_bottoming == null) {
			enable_bottoming = true;
		}
		outer_width = function(el) {
			var _el, computed, w;
			if (window.getComputedStyle) {
				_el = el[0];
				computed = window.getComputedStyle(el[0]);
				w = parseFloat(computed.getPropertyValue('width')) + parseFloat(computed.getPropertyValue('margin-left')) + parseFloat(computed.getPropertyValue('margin-right'));
				if (computed.getPropertyValue('box-sizing') !== 'border-box') {
					w += parseFloat(computed.getPropertyValue('border-left-width')) + parseFloat(computed.getPropertyValue('border-right-width')) + parseFloat(computed.getPropertyValue('padding-left')) + parseFloat(computed.getPropertyValue('padding-right'));
				}
				return w;
			} else {
				return el.outerWidth(true);
			}
		};
		fn = function(elm, padding_bottom, parent_top, parent_height, top, height, el_float, detached) {
			var bottomed, detach, fixed, last_pos, last_scroll_height, offset, parent, recalc, recalc_and_tick, recalc_counter, spacer, tick;
			if (elm.data('sticky_kit')) {
				return;
			}
			elm.data('sticky_kit', true);
			last_scroll_height = doc.height();
			parent = elm.parent();
			if (parent_selector != null) {
				parent = parent.closest(parent_selector);
			}
			if (!parent.length) {
				throw 'failed to find stick parent';
			}
			fixed = false;
			bottomed = false;
			spacer = manual_spacer != null
				? manual_spacer && elm.closest(manual_spacer)
				: $('<div />');
			if (spacer) {
				spacer.css('position', elm.css('position'));
			}
			recalc = function() {
				var border_top, padding_top, restore;
				if (detached) {
					return;
				}
				last_scroll_height = doc.height();
				border_top = parseInt(parent.css('border-top-width'), 10);
				padding_top = parseInt(parent.css('padding-top'), 10);
				padding_bottom = parseInt(parent.css('padding-bottom'), 10);
				parent_top = parent.offset().top + border_top + padding_top;
				parent_height = parent.height();
				if (fixed) {
					fixed = false;
					bottomed = false;
					if (manual_spacer == null) {
						elm.insertAfter(spacer);
						spacer.detach();
					}
					elm.css({
						position: '',
						top: '',
						width: '',
						bottom: '',
					}).removeClass(sticky_class);
					restore = true;
				}
				top = elm.offset().top - (parseInt(elm.css('margin-top'), 10) || 0) - offset_top;
				height = elm.outerHeight(true);
				el_float = elm.css('float');
				if (spacer) {
					spacer.css({
						width: outer_width(elm),
						height: height,
						display: elm.css('display'),
						'vertical-align': elm.css('vertical-align'),
						'float': el_float,
					});
				}
				if (restore) {
					return tick();
				}
			};
			recalc();
			if (height === parent_height) {
				return;
			}
			last_pos = void 0;
			offset = offset_top;
			recalc_counter = recalc_every;
			tick = function() {
				var css, delta, recalced, scroll, will_bottom, win_height;
				if (detached) {
					return;
				}
				recalced = false;
				if (recalc_counter != null) {
					recalc_counter -= 1;
					if (recalc_counter <= 0) {
						recalc_counter = recalc_every;
						recalc();
						recalced = true;
					}
				}
				if (!recalced && doc.height() !== last_scroll_height) {
					recalc();
					recalced = true;
				}
				scroll = win.scrollTop();
				if (last_pos != null) {
					delta = scroll - last_pos;
				}
				last_pos = scroll;
				if (fixed) {
					if (enable_bottoming) {
						will_bottom = scroll + height + offset > parent_height + parent_top;
						if (bottomed && !will_bottom) {
							bottomed = false;
							elm.css({
								position: 'fixed',
								bottom: '',
								top: offset,
							}).trigger('sticky_kit:unbottom');
						}
					}
					if (scroll < top) {
						fixed = false;
						offset = offset_top;
						if (manual_spacer == null) {
							if (el_float === 'left' || el_float === 'right') {
								elm.insertAfter(spacer);
							}
							spacer.detach();
						}
						css = {
							position: '',
							width: '',
							top: '',
						};
						elm.css(css).removeClass(sticky_class).trigger('sticky_kit:unstick');
					}
					if (inner_scrolling) {
						win_height = win.height();
						if (height + offset_top > win_height) {
							if (!bottomed) {
								offset -= delta;
								offset = Math.max(win_height - height, offset);
								offset = Math.min(offset_top, offset);
								if (fixed) {
									elm.css({
										top: offset + 'px',
									});
								}
							}
						}
					}
				} else {
					if (scroll > top) {
						fixed = true;
						css = {
							position: 'fixed',
							top: offset,
						};
						css.width = elm.css('box-sizing') === 'border-box'
							? elm.outerWidth() + 'px'
							: elm.width() + 'px';
						elm.css(css).addClass(sticky_class);
						if (manual_spacer == null) {
							elm.after(spacer);
							if (el_float === 'left' || el_float === 'right') {
								spacer.append(elm);
							}
						}
						elm.trigger('sticky_kit:stick');
					}
				}
				if (fixed && enable_bottoming) {
					if (will_bottom == null) {
						will_bottom = scroll + height + offset > parent_height + parent_top;
					}
					if (!bottomed && will_bottom) {
						bottomed = true;
						if (parent.css('position') === 'static') {
							parent.css({
								position: 'relative',
							});
						}
						return elm.css({
							position: 'absolute',
							bottom: padding_bottom,
							top: 'auto',
						}).trigger('sticky_kit:bottom');
					}
				}
			};
			recalc_and_tick = function() {
				recalc();
				return tick();
			};
			detach = function() {
				detached = true;
				win.off('touchmove', tick);
				win.off('scroll', tick);
				win.off('resize', recalc_and_tick);
				$(document.body).off('sticky_kit:recalc', recalc_and_tick);
				elm.off('sticky_kit:detach', detach);
				elm.removeData('sticky_kit');
				elm.css({
					position: '',
					bottom: '',
					top: '',
					width: '',
				});
				parent.position('position', '');
				if (fixed) {
					if (manual_spacer == null) {
						if (el_float === 'left' || el_float === 'right') {
							elm.insertAfter(spacer);
						}
						spacer.remove();
					}
					return elm.removeClass(sticky_class);
				}
			};
			win.on('touchmove', tick);
			win.on('scroll', tick);
			win.on('resize', recalc_and_tick);
			$(document.body).on('sticky_kit:recalc', recalc_and_tick);
			elm.on('sticky_kit:detach', detach);
			return setTimeout(tick, 0);
		};
		for (i = 0, len = this.length; i < len; i++) {
			elm = this[i];
			fn($(elm));
		}
		return this;
	};

}).call(this);

/*!
 * 360 degree Image Slider v2.0.4
 * http://gaurav.jassal.me
 *
 * Copyright 2015, gaurav@jassal.me
 * Dual licensed under the MIT or GPL Version 3 licenses.
 *
 */
(function ($) {
	'use strict';
	/**
	 * @class ThreeSixty
	 * **The ThreeSixty slider class**.
	 *
	 * This as jQuery plugin to create 360 degree product image slider.
	 * The plugin is full customizable with number of options provided. The plugin
	 * have the power to display images in any angle 360 degrees. This feature can be
	 * used successfully in many use cases e.g. on an e-commerce site to help customers
	 * look products in detail, from any angle they desire.
	 *
	 * **Features**
	 *
	 * - Smooth Animation
	 * - Plenty of option parameters for customization
	 * - Api interaction
	 * - Simple mouse interaction
	 * - Custom behavior tweaking
	 * - Support for touch devices
	 * - Easy to integrate
	 * - No flash
	 *
	 * Example code:
	 *      var product1 = $('.product1').ThreeSixty({
	 *        totalFrames: 72,
	 *        endFrame: 72,
	 *        currentFrame: 1,
	 *        imgList: '.threesixty_images',
	 *        progress: '.spinner',
	 *        imagePath:'/assets/product1/',
	 *        filePrefix: 'ipod-',
	 *        ext: '.jpg',
	 *        height: 265,
	 *        width: 400,
	 *        navigation: true
	 *      });
	 * **Note:** There are loads other options that you can override to customize
	 * this plugin.
  
	 * @extends jQuery
	 * @singleton
	 * @param {String} [el] jQuery selector string for the parent container
	 * @param {Object} [options] An optional config object
	 *
	 * @return this
	 */
	$.ThreeSixty = function (el, options) {
		// To avoid scope issues, use 'base' instead of 'this'
		// to reference this class from internal events and functions.
		var base = this,
			AppConfig, frames = [],
			VERSION = '2.0.5';
		// Access to jQuery and DOM versions of element
		/**
		 * @property {$el}
		 * jQuery Dom node attached to the slider inherits all jQuery public functions.
		 */
		base.$el = $(el);
		base.el = el;
		// Add a reverse reference to the DOM object
		base.$el.data('ThreeSixty', base);
		/**
		 * @method init
		 * The function extends the user options with default settings for the
		 * slider and initilize the slider.
		 * **Style Override example**
		 *
		 *      var product1 = $('.product1').ThreeSixty({
		 *        totalFrames: 72,
		 *        endFrame: 72,
		 *        currentFrame: 1,
		 *        imgList: '.threesixty_images',
		 *        progress: '.spinner',
		 *        imagePath:'/assets/product1/',
		 *        filePrefix: 'ipod-',
		 *        ext: '.jpg',
		 *        height: 265,
		 *        width: 400,
		 *        navigation: true,
		 *        styles: {
		 *          border: 2px solide #b4b4b4,
		 *          background: url(http://example.com/images/loader.gif) no-repeat
		 *        }
		 *      });
		 */
		base.init = function () {
			base.$el.addClass('threesixty-ready');
			
			AppConfig = $.extend({}, $.ThreeSixty.defaultOptions, options);
			if (AppConfig.disableSpin) {
				AppConfig.currentFrame = 1;
				AppConfig.endFrame = 1;
			}
			base.initProgress();
			base.loadImages();
		};

		/*
		 * Function to resize the height of responsive slider.
		 */
		base.resize = function () {
			// calculate height
		};
		/**
		 * @method initProgress
		 * The function sets initial styles and start the progress indicator
		 * to show loading of images.
		 *
		 * @private
		 */
		base.initProgress = function () {
			base.$el.css({
				width: AppConfig.width + 'px',
				height: AppConfig.height + 'px',
				'background-image': 'none !important'
			});
			if (AppConfig.styles) {
				base.$el.css(AppConfig.styles);
			}

			base.responsive();

			base.$el.find(AppConfig.progress).css({
				marginTop: ((AppConfig.height / 2) - 15) + 'px'
			});
			base.$el.find(AppConfig.progress).fadeIn('slow');
			base.$el.find(AppConfig.imgList).hide();
		};

		/**
		 * @method loadImages
		 * @private
		 * The function asynchronously loads images and inject into the slider.
		 */
		base.loadImages = function () {
			var li, imageName, image, host, baseIndex;
			li = document.createElement('li');
			baseIndex = AppConfig.zeroBased ? 0 : 1;
			imageName = !AppConfig.imgArray ?
				AppConfig.domain + AppConfig.imagePath + AppConfig.filePrefix + base.zeroPad((AppConfig.loadedImages + baseIndex)) + AppConfig.ext + ((base.browser.isIE()) ? '?' + new Date().getTime() : '') :
				AppConfig.imgArray[AppConfig.loadedImages];
			image = $('<img>').attr('src', imageName).addClass('previous-image').appendTo(li);

			frames.push(image);

			base.$el.find(AppConfig.imgList).append(li);

			$(image).on('load', function () {
				base.imageLoaded();
			});

		};

		/**
		 * @method loadImages
		 * @private
		 * The function gets triggers once the image is loaded. We also update
		 * the progress percentage in this function.
		 */
		base.imageLoaded = function () {
			AppConfig.loadedImages += 1;
			$(AppConfig.progress + ' span').text(Math.floor(AppConfig.loadedImages / AppConfig.totalFrames * 100) + '%');
			if (AppConfig.loadedImages >= AppConfig.totalFrames) {
				if (AppConfig.disableSpin) {
					frames[0].removeClass('previous-image').addClass('current-image');
				}
				$(AppConfig.progress).fadeOut('slow', function () {
					$(this).hide();
					base.showImages();
					base.showNavigation();
				});
			} else {
				base.loadImages();
			}
		};

		/**
		 * @method loadImages
		 * @private
		 * This function is called when all the images are loaded.
		 * **The function does following operations**
		 * - Removes background image placeholder
		 * - Displays the 360 images
		 * - Initilizes mouse intraction events
		 */
		base.showImages = function () {
			base.$el.find('.txtC').fadeIn();
			base.$el.find(AppConfig.imgList).fadeIn();
			base.ready = true;
			AppConfig.ready = true;

			if (AppConfig.drag) {
				base.initEvents();
			}
			base.refresh();
			base.initPlugins();
			AppConfig.onReady();

			setTimeout(function () { base.responsive(); }, 50);
		};

		/**
		 * The function to initilize external plugin
		 */
		base.initPlugins = function () {
			$.each(AppConfig.plugins, function (i, plugin) {
				if (typeof $[plugin] === 'function') {
					$[plugin].call(base, base.$el, AppConfig);
				} else {
					throw new Error(plugin + ' not available.');
				}
			});
		};

		/**
		 * @method showNavigation
		 * Creates a navigation panel if navigation is set to true in the
		 * settings.
		 */
		base.showNavigation = function () {
			if (AppConfig.navigation && !AppConfig.navigation_init) {
				var nav_bar, next, previous, play_stop;

				nav_bar = $('<div/>').attr('class', 'nav_bar');

				next = $('<a/>').attr({
					'href': '#',
					'class': 'nav_bar_next'
				}).html('next');

				previous = $('<a/>').attr({
					'href': '#',
					'class': 'nav_bar_previous'
				}).html('previous');

				play_stop = $('<a/>').attr({
					'href': '#',
					'class': 'nav_bar_play'
				}).html('play');

				nav_bar.append(previous);
				nav_bar.append(play_stop);
				nav_bar.append(next);

				base.$el.prepend(nav_bar);

				next.bind('mousedown touchstart', base.next);
				previous.bind('mousedown touchstart', base.previous);
				play_stop.bind('mousedown touchstart', base.play_stop);
				AppConfig.navigation_init = true;
			}
		};

		/**
		 * @method play_stop
		 * @private
		 * Function toggles the autoplay rotation of 360 slider
		 * @param {Object} [event] jQuery events object.
		 *
		 */

		base.play_stop = function (event) {
			event.preventDefault();

			if (!AppConfig.autoplay) {
				AppConfig.autoplay = true;
				AppConfig.play = setInterval(base.moveToNextFrame, AppConfig.playSpeed);
				$(event.currentTarget).removeClass('nav_bar_play').addClass('nav_bar_stop');
			} else {
				AppConfig.autoplay = false;
				$(event.currentTarget).removeClass('nav_bar_stop').addClass('nav_bar_play');
				clearInterval(AppConfig.play);
				AppConfig.play = null;
			}
		};

		/**
		 * @method next
		 * Using this function you can rotate 360 to next 5 frames.
		 * @param {Object} [event] jQuery events object.
		 *
		 */

		base.next = function (event) {
			if (event) { event.preventDefault(); }
			AppConfig.endFrame -= 5;
			base.refresh();
		};

		/**
		 * @method previous
		 * Using this function you can rotate 360 to previous 5 frames.
		 * @param {Object} [event] jQuery events object.
		 *
		 */
		base.previous = function (event) {
			if (event) { event.preventDefault(); }
			AppConfig.endFrame += 5;
			base.refresh();
		};

		/**
		 * @method play
		 * You are start the auto rotaion for the slider with this function.
		 *
		 */
		base.play = function (speed, direction) {
			var _speed = speed || AppConfig.playSpeed;
			var _direction = direction || AppConfig.autoplayDirection;
			AppConfig.autoplayDirection = _direction

			if (!AppConfig.autoplay) {
				AppConfig.autoplay = true;
				AppConfig.play = setInterval(base.moveToNextFrame, _speed);
			}
		};

		/**
		 * @method stop
		 * You can stop the auto rotation of the 360 slider with this function.
		 *
		 */

		base.stop = function () {
			if (AppConfig.autoplay) {
				AppConfig.autoplay = false;
				clearInterval(AppConfig.play);
				AppConfig.play = null;
			}
		};

		/**
		 * @method endFrame
		 * @private
		 * Function animates to previous frame
		 *
		 */
		base.moveToNextFrame = function () {
			if (AppConfig.autoplayDirection === 1) {
				AppConfig.endFrame -= 1;
			} else {
				AppConfig.endFrame += 1;
			}
			base.refresh();
		};

		/**
		 * @method gotoAndPlay
		 * @public
		 * Function animates to previous frame
		 *
		 */
		base.gotoAndPlay = function (n) {
			if (AppConfig.disableWrap) {
				AppConfig.endFrame = n;
				base.refresh();
			} else {
				// Since we could be looped around grab the multiplier
				var multiplier = Math.ceil(AppConfig.endFrame / AppConfig.totalFrames);
				if (multiplier === 0) {
					multiplier = 1;
				}

				// Figure out the quickest path to the requested frame
				var realEndFrame = (multiplier > 1) ?
					AppConfig.endFrame - ((multiplier - 1) * AppConfig.totalFrames) :
					AppConfig.endFrame;

				var currentFromEnd = AppConfig.totalFrames - realEndFrame;

				// Jump past end if it's faster
				var newEndFrame = 0;
				if (n - realEndFrame > 0) {
					// Faster to move the difference ahead?
					if (n - realEndFrame < realEndFrame + (AppConfig.totalFrames - n)) {
						newEndFrame = AppConfig.endFrame + (n - realEndFrame);
					} else {
						newEndFrame = AppConfig.endFrame - (realEndFrame + (AppConfig.totalFrames - n));
					}
				} else {
					// Faster to move the distance back?
					if (realEndFrame - n < currentFromEnd + n) {
						newEndFrame = AppConfig.endFrame - (realEndFrame - n);
					} else {
						newEndFrame = AppConfig.endFrame + (currentFromEnd + n);
					}
				}

				// Now set the end frame
				if (realEndFrame !== n) {
					AppConfig.endFrame = newEndFrame;
					base.refresh();
				}
			}
		};


		/**
		 * @method initEvents
		 * @private
		 * Function initilizes all the mouse and touch events for 360 slider movement.
		 *
		 */
		base.initEvents = function () {
			base.$el.bind('mousedown touchstart touchmove touchend mousemove click', function (event) {

				event.preventDefault();

				if ((event.type === 'mousedown' && event.which === 1) || event.type === 'touchstart') {
					AppConfig.pointerStartPosX = base.getPointerEvent(event).pageX;
					AppConfig.dragging = true;
					AppConfig.onDragStart(AppConfig.currentFrame);
				} else if (event.type === 'touchmove') {
					base.trackPointer(event);
				} else if (event.type === 'touchend') {
					AppConfig.dragging = false;
					AppConfig.onDragStop(AppConfig.endFrame);
				}
			});

			$(document).bind('mouseup', function (event) {
				AppConfig.dragging = false;
				AppConfig.onDragStop(AppConfig.endFrame);
				$(this).css('cursor', 'none');
			});

			$(window).bind('resize', function (event) {
				base.responsive();
			});

			$(document).bind('mousemove', function (event) {
				if (AppConfig.dragging) {
					event.preventDefault();
					if (!base.browser.isIE && AppConfig.showCursor) {
						base.$el.css('cursor', 'url(assets/images/hand_closed.png), auto');
					}
				} else {
					if (!base.browser.isIE && AppConfig.showCursor) {
						base.$el.css('cursor', 'url(assets/images/hand_open.png), auto');
					}
				}
				base.trackPointer(event);

			});

			$(window).resize(function () {
				base.resize();
			});
		};

		/**
		 * @method getPointerEvent
		 * @private
		 * Function returns touch pointer events
		 *
		 * @params {Object} [event]
		 */
		base.getPointerEvent = function (event) {
			return event.originalEvent.targetTouches ? event.originalEvent.targetTouches[0] : event;
		};

		/**
		 * @method trackPointer
		 * @private
		 * Function calculates the distance between the start pointer and end pointer/
		 *
		 * @params {Object} [event]
		 */
		base.trackPointer = function (event) {
			if (AppConfig.ready && AppConfig.dragging) {
				AppConfig.pointerEndPosX = base.getPointerEvent(event).pageX;
				if (AppConfig.monitorStartTime < new Date().getTime() - AppConfig.monitorInt) {
					AppConfig.pointerDistance = AppConfig.pointerEndPosX - AppConfig.pointerStartPosX;
					if (AppConfig.pointerDistance > 0) {
						AppConfig.endFrame = AppConfig.currentFrame + Math.ceil((AppConfig.totalFrames - 1) * AppConfig.speedMultiplier * (AppConfig.pointerDistance / base.$el.width()));
					} else {
						AppConfig.endFrame = AppConfig.currentFrame + Math.floor((AppConfig.totalFrames - 1) * AppConfig.speedMultiplier * (AppConfig.pointerDistance / base.$el.width()));
					}

					if (AppConfig.disableWrap) {
						AppConfig.endFrame = Math.min(AppConfig.totalFrames - (AppConfig.zeroBased ? 1 : 0), AppConfig.endFrame);
						AppConfig.endFrame = Math.max((AppConfig.zeroBased ? 0 : 1), AppConfig.endFrame);
					}
					base.refresh();
					AppConfig.monitorStartTime = new Date().getTime();
					AppConfig.pointerStartPosX = base.getPointerEvent(event).pageX;
				}
			}
		};

		/**
		 * @method refresh
		 * @public
		 * Function refeshes the timer and set interval for render cycle.
		 *
		 */

		base.refresh = function () {
			if (AppConfig.ticker === 0) {
				AppConfig.ticker = setInterval(base.render, Math.round(1000 / AppConfig.framerate));
			}
		};

		/**
		* @method refresh
		* @private
		* Function render the animation frames on the screen with easing effect.
		*/

		base.render = function () {
			var frameEasing;
			if (AppConfig.currentFrame !== AppConfig.endFrame) {
				frameEasing = AppConfig.endFrame < AppConfig.currentFrame ? Math.floor((AppConfig.endFrame - AppConfig.currentFrame) * 0.1) : Math.ceil((AppConfig.endFrame - AppConfig.currentFrame) * 0.1);
				base.hidePreviousFrame();
				AppConfig.currentFrame += frameEasing;
				base.showCurrentFrame();
				base.$el.trigger('frameIndexChanged', [base.getNormalizedCurrentFrame(), AppConfig.totalFrames]);
			} else {
				window.clearInterval(AppConfig.ticker);
				AppConfig.ticker = 0;
			}
		};

		/**
		 * @method hidePreviousFrame
		 * @private
		 * Function hide the previous frame in the animation loop.
		 */

		base.hidePreviousFrame = function () {
			frames[base.getNormalizedCurrentFrame()].removeClass('current-image').addClass('previous-image');
		};

		/**
		 * @method showCurrentFrame
		 * @private
		 * Function shows the current frame in the animation loop.
		 */
		base.showCurrentFrame = function () {
			frames[base.getNormalizedCurrentFrame()].removeClass('previous-image').addClass('current-image');
		};

		/**
		 * @method getNormalizedCurrentFrame
		 * @private
		 * Function normalize and calculate the current frame once the user release the mouse and release touch event.
		 */

		base.getNormalizedCurrentFrame = function () {
			var c, e;

			if (!AppConfig.disableWrap) {
				c = Math.ceil(AppConfig.currentFrame % AppConfig.totalFrames);
				if (c < 0) {
					c += AppConfig.totalFrames - (AppConfig.zeroBased ? 1 : 0);
				}
			} else {
				c = Math.min(AppConfig.currentFrame, AppConfig.totalFrames - (AppConfig.zeroBased ? 1 : 0));
				e = Math.min(AppConfig.endFrame, AppConfig.totalFrames - (AppConfig.zeroBased ? 1 : 0));
				c = Math.max(c, (AppConfig.zeroBased ? 0 : 1));
				e = Math.max(e, (AppConfig.zeroBased ? 0 : 1));
				AppConfig.currentFrame = c;
				AppConfig.endFrame = e;
			}

			return c;
		};

		/*
		 * @method getCurrentFrame
		 * Function returns the current active frame.
		 *
		 * @return Number
		 */

		base.getCurrentFrame = function () {
			return AppConfig.currentFrame;
		};

		/*
		* @method responsive
		* Function calculates and set responsive height and width
		*
		*/

		base.responsive = function () {
			if (AppConfig.responsive) {
				base.$el.css({
					height: base.$el.find('.current-image').first().css('height'),
					width: '100%'
				});
			}
		};

		/**
		 * Function to return with zero padding.
		 */
		base.zeroPad = function (num) {
			function pad(number, length) {
				var str = number.toString();
				if (AppConfig.zeroPadding) {
					while (str.length < length) {
						str = '0' + str;
					}
				}
				return str;
			}

			var approximateLog = Math.log(AppConfig.totalFrames) / Math.LN10;
			var roundTo = 1e3;
			var roundedLog = Math.round(approximateLog * roundTo) / roundTo;
			var numChars = Math.floor(roundedLog) + 1;
			return pad(num, numChars);
		};

		base.browser = {};

		/**
		 * Function to detect if the brower is IE
		 * @return {boolean}
		 *
		 * http://msdn.microsoft.com/en-gb/library/ms537509(v=vs.85).aspx
		 */
		base.browser.isIE = function () {
			var rv = -1;
			if (navigator.appName === 'Microsoft Internet Explorer') {
				var ua = navigator.userAgent;
				var re = new RegExp('MSIE ([0-9]{1,}[\\.0-9]{0,})');
				if (re.exec(ua) !== null) {
					rv = parseFloat(RegExp.$1);
				}
			}

			return rv !== -1;
		};


		/**
		 * @method getConfig
		 * The function returns the extended version of config object the plugin is going to
		 * user.
		 *
		 * @public
		 *
		 * @return Object
		 */
		base.getConfig = function () {
			return AppConfig;
		};

		$.ThreeSixty.defaultOptions = {
			/**
			 * @cfg {Boolean} dragging [dragging=false]
			 * @private
			 * Private propery contains a flags if users is in dragging mode.
			 */
			dragging: false,
			/**
			 * @cfg {Boolean} ready [ready=false]
			 * @private
			 * Private propery is set to true is all assets are loading and application is
			 * ready to render 360 slider.
			 */
			ready: false,
			/**
			 * @cfg {Number} pointerStartPosX
			 * @private
			 * private property mouse pointer start x position when user starts dragging slider.
			 */
			pointerStartPosX: 0,
			/**
			 * @cfg {Number} pointerEndPosX
			 * @private
			 * private property mouse pointer start x position when user end dragging slider.
			 */
			pointerEndPosX: 0,
			/**
			 * @cfg {Number} pointerDistance
			 * @private
			 * private property contains the distance between the pointerStartPosX and pointerEndPosX
			 */
			pointerDistance: 0,
			/**
			 * @cfg {Number} monitorStartTime
			 * @private
			 * private property contains time user took in dragging mouse from pointerStartPosX and pointerEndPosX
			 */
			monitorStartTime: 0,
			monitorInt: 10,
			/**
			 * @cfg {Number} ticker
			 * @private
			 * Timer event that renders the 360
			 */
			ticker: 0,
			/**
			 * @cfg {Number} speedMultiplier
			 * This property controls the sensitivity for the 360 slider
			 */
			speedMultiplier: 7,
			/**
			 * @cfg {Number} totalFrames
			 * Set total number for frames used in the 360 rotation
			 */
			totalFrames: 180,
			/**
			 * @cfg {Number} currentFrame
			 * Current frame of the slider.
			 */
			currentFrame: 0,
			/**
			 * @cfg {Array} endFrame
			 * Private perperty contains information about the end frame when user slides the slider.
			 */
			endFrame: 0,
			/**
			 * @cfg {Number} loadedImages
			 * Private property contains count of loaded images.
			 */
			loadedImages: 0,
			/**
			 * @cfg {Array} framerate
			 * Set framerate for the slider animation
			 */
			framerate: 60,
			/**
			 * @cfg {String} domains
			 * Set comma seprated list of all parallel domain from where 360 assets needs to be loaded.
			 */
			domains: null,
			/**
			 * @cfg {String} domain
			 * Domain from where assets needs to be loaded. Use this propery is you want to load all assets from
			 * single domain.
			 */
			domain: '',
			/**
			 * @cfg {Boolean} parallel
			 * Set to true if you want to load assets from parallel domain. Default false
			 */
			parallel: false,
			/**
			 * @cfg {Number} queueAmount
			 * Set number of calls to be made on parallel domains.
			 */
			queueAmount: 8,
			/**
			 * @cfg {Number} idle
			 * Mouse Inactivite idle time in seconds. If set more than 0 will auto spine the slider
			 */
			idle: 0,
			/**
			 * @cfg {String} filePrefix
			 * Prefix for the image file name before the numeric value.
			 */
			filePrefix: '',
			/**
			 * @cfg {String} ext [ext=.png]
			 * Slider image extension.
			 */
			ext: 'png',
			/**
			 * @cfg {Object} height [300]
			 * Height of the slider
			 */
			height: 300,
			/**
			 * @cfg {Number} width [300]
			 * Width of the slider
			 */
			width: 300,
			/**
			 * @cfg {Object} styles
			 * CSS Styles for the 360 slider
			 */
			styles: {},
			/**
			 * @cfg {Boolean} navigation[false]
			 * State if navigation controls are visible or not.
			 */
			navigation: false,
			/**
			 * @cfg {Boolean} autoplay[false]
			 * Autoplay the 360 animation
			 */
			autoplay: false,
			/**
			 * @cfg {number} autoplayDirection [1]
			 * Direction for autoplay the 360 animation. 1 for right spin, and -1 for left spin.
			 */
			autoplayDirection: 1,
			/**
			 * Property to disable auto spin
			 * @type {Boolean}
			 */
			disableSpin: false,
			/**
			 * Property to disable infinite wrap
			 * @type {Boolean}
			 */
			disableWrap: false,
			/**
			 * Responsive width
			 * @type {Boolean}
			 */
			responsive: false,
			/**
			 * Zero Padding for filenames
			 * @type {Boolean}
			 */
			zeroPadding: false,
			/**
			 * Zero based for image filenames starting at 0
			 * @type {Boolean}
			 */
			zeroBased: false,
			/**
			 * @type {Array}
			 * List of plugins
			 */
			plugins: [],
			/**
			 * @type {Boolean}
			 * Show hand cursor on drag
			 */
			showCursor: false,
			/**
			 * @cfg {Boolean} drag
			 * Set it to false if you want to disable mousedrag or touch events
			 */
			drag: true,
			/**
			 * @cfg {Function} onReady
			 * Callback triggers once all images are loaded and ready to render on the screen
			 */
			onReady: function () { },
			/**
			 * @cfg {Function} onDragStart
			 * Callback triggers when a user initiates dragging
			 */
			onDragStart: function () { },
			/**
			 * @cfg {Function} onDragStop
			 * Callback triggers when a user releases after dragging
			 */
			onDragStop: function () { },
			/**
			 * @cfg {String} imgList
			 * Set ul element where image will be loaded
			 */
			imgList: '.threesixty_images',
			/**
			 * @cfg {Array} imgArray
			 * Use set of images in array to load images
			 */
			imgArray: null,
			/**
			* @cfg {Number} playSpeed
			* Value to control the speed of play button rotation
			*/
			playSpeed: 100
		};
		base.init();
	};

	$.fn.ThreeSixty = function (options) {
		return Object.create(new $.ThreeSixty(this, options));
	};
}(jQuery));
/**
 *
 * Object.create method for perform as a fallback if method not available.
 * The syntax just takes away the illusion that JavaScript uses Classical Inheritance.
 */
if (typeof Object.create !== 'function') {
	Object.create = function (o) {
		'use strict';

		function F() { }
		F.prototype = o;
		return new F();
	};
}

/*!
 * Bootstrap v4.0.0 (https://getbootstrap.com)
 * Copyright 2011-2018 The Bootstrap Authors (https://github.com/twbs/bootstrap/graphs/contributors)
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */
(function(global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('jquery')) :
		typeof define === 'function' && define.amd ? define([
				'exports',
				'jquery'
			], factory) :
			(factory((global.bootstrap = {}), global.jQuery));
}(this, (function(exports, $) {
	'use strict';

	$ = $ && $.hasOwnProperty('default') ? $['default'] : $;

	function _defineProperties(target, props) {
		for (var i = 0; i < props.length; i++) {
			var descriptor = props[i];
			descriptor.enumerable = descriptor.enumerable || false;
			descriptor.configurable = true;
			if ('value' in descriptor) {
				descriptor.writable = true;
			}
			Object.defineProperty(target, descriptor.key, descriptor);
		}
	}

	function _createClass(Constructor, protoProps, staticProps) {
		if (protoProps) {
			_defineProperties(Constructor.prototype, protoProps);
		}
		if (staticProps) {
			_defineProperties(Constructor, staticProps);
		}
		return Constructor;
	}

	function _extends() {
		_extends = Object.assign || function(target) {
			for (var i = 1; i < arguments.length; i++) {
				var source = arguments[i];

				for (var key in source) {
					if (Object.prototype.hasOwnProperty.call(source, key)) {
						target[key] = source[key];
					}
				}
			}

			return target;
		};

		return _extends.apply(this, arguments);
	}

	function _inheritsLoose(subClass, superClass) {
		subClass.prototype = Object.create(superClass.prototype);
		subClass.prototype.constructor = subClass;
		subClass.__proto__ = superClass;
	}

	/**
	 * --------------------------------------------------------------------------
	 * Bootstrap (v4.0.0): util.js
	 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	 * --------------------------------------------------------------------------
	 */

	var Util = function($$$1) {
		/**
		 * ------------------------------------------------------------------------
		 * Private TransitionEnd Helpers
		 * ------------------------------------------------------------------------
		 */
		var transition = false;
		var MAX_UID = 1000000; // Shoutout AngusCroll (https://goo.gl/pxwQGp)

		function toType(obj) {
			return {}.toString.call(obj).match(/\s([a-zA-Z]+)/)[1].toLowerCase();
		}

		function getSpecialTransitionEndEvent() {
			return {
				bindType    : transition.end,
				delegateType: transition.end,
				handle      : function handle(event) {
					if ($$$1(event.target).is(this)) {
						return event.handleObj.handler.apply(this, arguments); // eslint-disable-line prefer-rest-params
					}

					return undefined; // eslint-disable-line no-undefined
				}
			};
		}

		function transitionEndTest() {
			if (typeof window !== 'undefined' && window.QUnit) {
				return false;
			}

			return {
				end: 'transitionend'
			};
		}

		function transitionEndEmulator(duration) {
			var _this = this;

			var called = false;
			$$$1(this).one(Util.TRANSITION_END, function() {
				called = true;
			});
			setTimeout(function() {
				if (!called) {
					Util.triggerTransitionEnd(_this);
				}
			}, duration);
			return this;
		}

		function setTransitionEndSupport() {
			transition = transitionEndTest();
			$$$1.fn.emulateTransitionEnd = transitionEndEmulator;

			if (Util.supportsTransitionEnd()) {
				$$$1.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
			}
		}

		function escapeId(selector) {
			// We escape IDs in case of special selectors (selector = '#myId:something')
			// $.escapeSelector does not exist in jQuery < 3
			selector = typeof $$$1.escapeSelector === 'function' ? $$$1.escapeSelector(selector).substr(1) : selector.replace(/(:|\.|\[|\]|,|=|@)/g, '\\$1');
			return selector;
		}

		/**
		 * --------------------------------------------------------------------------
		 * Public Util Api
		 * --------------------------------------------------------------------------
		 */


		var Util = {
			TRANSITION_END        : 'bsTransitionEnd',
			getUID                : function getUID(prefix) {
				do {
					// eslint-disable-next-line no-bitwise
					prefix += ~~(Math.random() * MAX_UID); // "~~" acts like a faster Math.floor() here
				} while (document.getElementById(prefix));

				return prefix;
			},
			getSelectorFromElement: function getSelectorFromElement(element) {
				var selector = element.getAttribute('data-target');

				if (!selector || selector === '#') {
					selector = element.getAttribute('href') || '';
				} // If it's an ID

				if (selector.charAt(0) === '#') {
					selector = escapeId(selector);
				}

				try {
					var $selector = $$$1(document).find(selector);
					return $selector.length > 0 ? selector : null;
				}
				catch (err) {
					return null;
				}
			},
			reflow                : function reflow(element) {
				return element.offsetHeight;
			},
			triggerTransitionEnd  : function triggerTransitionEnd(element) {
				$$$1(element).trigger(transition.end);
			},
			supportsTransitionEnd : function supportsTransitionEnd() {
				return Boolean(transition);
			},
			isElement             : function isElement(obj) {
				return (obj[0] || obj).nodeType;
			},
			typeCheckConfig       : function typeCheckConfig(componentName, config, configTypes) {
				for (var property in configTypes) {
					if (Object.prototype.hasOwnProperty.call(configTypes, property)) {
						var expectedTypes = configTypes[property];
						var value = config[property];
						var valueType = value && Util.isElement(value) ? 'element' : toType(value);

						if (!new RegExp(expectedTypes).test(valueType)) {
							throw new Error(componentName.toUpperCase() + ': ' + ('Option "' + property + '" provided type "' + valueType + '" ') + ('but expected type "' + expectedTypes + '".'));
						}
					}
				}
			}
		};
		setTransitionEndSupport();
		return Util;
	}($);

	/**!
	 * @fileOverview Kickass library to create and place poppers near their reference elements.
	 * @version 1.12.9
	 * @license
	 * Copyright (c) 2016 Federico Zivolo and contributors
	 *
	 * Permission is hereby granted, free of charge, to any person obtaining a copy
	 * of this software and associated documentation files (the "Software"), to deal
	 * in the Software without restriction, including without limitation the rights
	 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	 * copies of the Software, and to permit persons to whom the Software is
	 * furnished to do so, subject to the following conditions:
	 *
	 * The above copyright notice and this permission notice shall be included in all
	 * copies or substantial portions of the Software.
	 *
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	 * SOFTWARE.
	 */
	var isBrowser = typeof window !== 'undefined' && typeof document !== 'undefined';
	var longerTimeoutBrowsers = [
		'Edge',
		'Trident',
		'Firefox'
	];
	var timeoutDuration = 0;
	for (var i = 0; i < longerTimeoutBrowsers.length; i += 1) {
		if (isBrowser && navigator.userAgent.indexOf(longerTimeoutBrowsers[i]) >= 0) {
			timeoutDuration = 1;
			break;
		}
	}

	function microtaskDebounce(fn) {
		var called = false;
		return function() {
			if (called) {
				return;
			}
			called = true;
			window.Promise.resolve().then(function() {
				called = false;
				fn();
			});
		};
	}

	function taskDebounce(fn) {
		var scheduled = false;
		return function() {
			if (!scheduled) {
				scheduled = true;
				setTimeout(function() {
					scheduled = false;
					fn();
				}, timeoutDuration);
			}
		};
	}

	var supportsMicroTasks = isBrowser && window.Promise;

	/**
	 * Create a debounced version of a method, that's asynchronously deferred
	 * but called in the minimum time possible.
	 *
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Function} fn
	 * @returns {Function}
	 */
	var debounce = supportsMicroTasks ? microtaskDebounce : taskDebounce;

	/**
	 * Check if the given variable is a function
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Any} functionToCheck - variable to check
	 * @returns {Boolean} answer to: is a function?
	 */
	function isFunction(functionToCheck) {
		var getType = {};
		return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
	}

	/**
	 * Get CSS computed property of the given element
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Eement} element
	 * @argument {String} property
	 */
	function getStyleComputedProperty(element, property) {
		if (element.nodeType !== 1) {
			return [];
		}
		// NOTE: 1 DOM access here
		var css = getComputedStyle(element, null);
		return property ? css[property] : css;
	}

	/**
	 * Returns the parentNode or the host of the element
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element
	 * @returns {Element} parent
	 */
	function getParentNode(element) {
		if (element.nodeName === 'HTML') {
			return element;
		}
		return element.parentNode || element.host;
	}

	/**
	 * Returns the scrolling parent of the given element
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element
	 * @returns {Element} scroll parent
	 */
	function getScrollParent(element) {
		// Return body, `getScroll` will take care to get the correct `scrollTop` from it
		if (!element) {
			return document.body;
		}

		switch (element.nodeName) {
			case 'HTML':
			case 'BODY':
				return element.ownerDocument.body;
			case '#document':
				return element.body;
		}

		// Firefox want us to check `-x` and `-y` variations as well

		var _getStyleComputedProp = getStyleComputedProperty(element),
		    overflow              = _getStyleComputedProp.overflow,
		    overflowX             = _getStyleComputedProp.overflowX,
		    overflowY             = _getStyleComputedProp.overflowY;

		if (/(auto|scroll)/.test(overflow + overflowY + overflowX)) {
			return element;
		}

		return getScrollParent(getParentNode(element));
	}

	/**
	 * Returns the offset parent of the given element
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element
	 * @returns {Element} offset parent
	 */
	function getOffsetParent(element) {
		// NOTE: 1 DOM access here
		var offsetParent = element && element.offsetParent;
		var nodeName = offsetParent && offsetParent.nodeName;

		if (!nodeName || nodeName === 'BODY' || nodeName === 'HTML') {
			if (element) {
				return element.ownerDocument.documentElement;
			}

			return document.documentElement;
		}

		// .offsetParent will return the closest TD or TABLE in case
		// no offsetParent is present, I hate this job...
		if ([
			'TD',
			'TABLE'
		].indexOf(offsetParent.nodeName) !== -1 && getStyleComputedProperty(offsetParent, 'position') === 'static') {
			return getOffsetParent(offsetParent);
		}

		return offsetParent;
	}

	function isOffsetContainer(element) {
		var nodeName = element.nodeName;

		if (nodeName === 'BODY') {
			return false;
		}
		return nodeName === 'HTML' || getOffsetParent(element.firstElementChild) === element;
	}

	/**
	 * Finds the root node (document, shadowDOM root) of the given element
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} node
	 * @returns {Element} root node
	 */
	function getRoot(node) {
		if (node.parentNode !== null) {
			return getRoot(node.parentNode);
		}

		return node;
	}

	/**
	 * Finds the offset parent common to the two provided nodes
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element1
	 * @argument {Element} element2
	 * @returns {Element} common offset parent
	 */
	function findCommonOffsetParent(element1, element2) {
		// This check is needed to avoid errors in case one of the elements isn't defined for any reason
		if (!element1 || !element1.nodeType || !element2 || !element2.nodeType) {
			return document.documentElement;
		}

		// Here we make sure to give as "start" the element that comes first in the DOM
		var order = element1.compareDocumentPosition(element2) & Node.DOCUMENT_POSITION_FOLLOWING;
		var start = order ? element1 : element2;
		var end = order ? element2 : element1;

		// Get common ancestor container
		var range = document.createRange();
		range.setStart(start, 0);
		range.setEnd(end, 0);
		var commonAncestorContainer = range.commonAncestorContainer;

		// Both nodes are inside #document

		if (element1 !== commonAncestorContainer && element2 !== commonAncestorContainer || start.contains(end)) {
			if (isOffsetContainer(commonAncestorContainer)) {
				return commonAncestorContainer;
			}

			return getOffsetParent(commonAncestorContainer);
		}

		// one of the nodes is inside shadowDOM, find which one
		var element1root = getRoot(element1);
		if (element1root.host) {
			return findCommonOffsetParent(element1root.host, element2);
		} else {
			return findCommonOffsetParent(element1, getRoot(element2).host);
		}
	}

	/**
	 * Gets the scroll value of the given element in the given side (top and left)
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element
	 * @argument {String} side `top` or `left`
	 * @returns {number} amount of scrolled pixels
	 */
	function getScroll(element) {
		var side = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'top';

		var upperSide = side === 'top' ? 'scrollTop' : 'scrollLeft';
		var nodeName = element.nodeName;

		if (nodeName === 'BODY' || nodeName === 'HTML') {
			var html = element.ownerDocument.documentElement;
			var scrollingElement = element.ownerDocument.scrollingElement || html;
			return scrollingElement[upperSide];
		}

		return element[upperSide];
	}

	/*
	 * Sum or subtract the element scroll values (left and top) from a given rect object
	 * @method
	 * @memberof Popper.Utils
	 * @param {Object} rect - Rect object you want to change
	 * @param {HTMLElement} element - The element from the function reads the scroll values
	 * @param {Boolean} subtract - set to true if you want to subtract the scroll values
	 * @return {Object} rect - The modifier rect object
	 */
	function includeScroll(rect, element) {
		var subtract = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

		var scrollTop = getScroll(element, 'top');
		var scrollLeft = getScroll(element, 'left');
		var modifier = subtract ? -1 : 1;
		rect.top += scrollTop * modifier;
		rect.bottom += scrollTop * modifier;
		rect.left += scrollLeft * modifier;
		rect.right += scrollLeft * modifier;
		return rect;
	}

	/*
	 * Helper to detect borders of a given element
	 * @method
	 * @memberof Popper.Utils
	 * @param {CSSStyleDeclaration} styles
	 * Result of `getStyleComputedProperty` on the given element
	 * @param {String} axis - `x` or `y`
	 * @return {number} borders - The borders size of the given axis
	 */

	function getBordersSize(styles, axis) {
		var sideA = axis === 'x' ? 'Left' : 'Top';
		var sideB = sideA === 'Left' ? 'Right' : 'Bottom';

		return parseFloat(styles['border' + sideA + 'Width'], 10) + parseFloat(styles['border' + sideB + 'Width'], 10);
	}

	/**
	 * Tells if you are running Internet Explorer 10
	 * @method
	 * @memberof Popper.Utils
	 * @returns {Boolean} isIE10
	 */
	var isIE10 = undefined;

	var isIE10$1 = function() {
		if (isIE10 === undefined) {
			isIE10 = navigator.appVersion.indexOf('MSIE 10') !== -1;
		}
		return isIE10;
	};

	function getSize(axis, body, html, computedStyle) {
		return Math.max(body['offset' + axis], body['scroll' + axis], html['client' + axis], html['offset' + axis], html['scroll' + axis], isIE10$1() ? html['offset' + axis] + computedStyle['margin' + (axis === 'Height' ? 'Top' : 'Left')] + computedStyle['margin' + (axis === 'Height' ? 'Bottom' : 'Right')] : 0);
	}

	function getWindowSizes() {
		var body = document.body;
		var html = document.documentElement;
		var computedStyle = isIE10$1() && getComputedStyle(html);

		return {
			height: getSize('Height', body, html, computedStyle),
			width : getSize('Width', body, html, computedStyle)
		};
	}

	var classCallCheck = function(instance, Constructor) {
		if (!(instance instanceof Constructor)) {
			throw new TypeError('Cannot call a class as a function');
		}
	};

	var createClass = function() {
		function defineProperties(target, props) {
			for (var i = 0; i < props.length; i++) {
				var descriptor = props[i];
				descriptor.enumerable = descriptor.enumerable || false;
				descriptor.configurable = true;
				if ('value' in descriptor) {
					descriptor.writable = true;
				}
				Object.defineProperty(target, descriptor.key, descriptor);
			}
		}

		return function(Constructor, protoProps, staticProps) {
			if (protoProps) {
				defineProperties(Constructor.prototype, protoProps);
			}
			if (staticProps) {
				defineProperties(Constructor, staticProps);
			}
			return Constructor;
		};
	}();

	var defineProperty = function(obj, key, value) {
		if (key in obj) {
			Object.defineProperty(obj, key, {
				value       : value,
				enumerable  : true,
				configurable: true,
				writable    : true
			});
		} else {
			obj[key] = value;
		}

		return obj;
	};

	var _extends$1 = Object.assign || function(target) {
		for (var i = 1; i < arguments.length; i++) {
			var source = arguments[i];

			for (var key in source) {
				if (Object.prototype.hasOwnProperty.call(source, key)) {
					target[key] = source[key];
				}
			}
		}

		return target;
	};

	/**
	 * Given element offsets, generate an output similar to getBoundingClientRect
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Object} offsets
	 * @returns {Object} ClientRect like output
	 */
	function getClientRect(offsets) {
		return _extends$1({}, offsets, {
			right : offsets.left + offsets.width,
			bottom: offsets.top + offsets.height
		});
	}

	/**
	 * Get bounding client rect of given element
	 * @method
	 * @memberof Popper.Utils
	 * @param {HTMLElement} element
	 * @return {Object} client rect
	 */
	function getBoundingClientRect(element) {
		var rect = {};

		// IE10 10 FIX: Please, don't ask, the element isn't
		// considered in DOM in some circumstances...
		// This isn't reproducible in IE10 compatibility mode of IE11
		if (isIE10$1()) {
			try {
				rect = element.getBoundingClientRect();
				var scrollTop = getScroll(element, 'top');
				var scrollLeft = getScroll(element, 'left');
				rect.top += scrollTop;
				rect.left += scrollLeft;
				rect.bottom += scrollTop;
				rect.right += scrollLeft;
			}
			catch (err) {
			}
		} else {
			rect = element.getBoundingClientRect();
		}

		var result = {
			left  : rect.left,
			top   : rect.top,
			width : rect.right - rect.left,
			height: rect.bottom - rect.top
		};

		// subtract scrollbar size from sizes
		var sizes = element.nodeName === 'HTML' ? getWindowSizes() : {};
		var width = sizes.width || element.clientWidth || result.right - result.left;
		var height = sizes.height || element.clientHeight || result.bottom - result.top;

		var horizScrollbar = element.offsetWidth - width;
		var vertScrollbar = element.offsetHeight - height;

		// if an hypothetical scrollbar is detected, we must be sure it's not a `border`
		// we make this check conditional for performance reasons
		if (horizScrollbar || vertScrollbar) {
			var styles = getStyleComputedProperty(element);
			horizScrollbar -= getBordersSize(styles, 'x');
			vertScrollbar -= getBordersSize(styles, 'y');

			result.width -= horizScrollbar;
			result.height -= vertScrollbar;
		}

		return getClientRect(result);
	}

	function getOffsetRectRelativeToArbitraryNode(children, parent) {
		var isIE10 = isIE10$1();
		var isHTML = parent.nodeName === 'HTML';
		var childrenRect = getBoundingClientRect(children);
		var parentRect = getBoundingClientRect(parent);
		var scrollParent = getScrollParent(children);

		var styles = getStyleComputedProperty(parent);
		var borderTopWidth = parseFloat(styles.borderTopWidth, 10);
		var borderLeftWidth = parseFloat(styles.borderLeftWidth, 10);

		var offsets = getClientRect({
			top   : childrenRect.top - parentRect.top - borderTopWidth,
			left  : childrenRect.left - parentRect.left - borderLeftWidth,
			width : childrenRect.width,
			height: childrenRect.height
		});
		offsets.marginTop = 0;
		offsets.marginLeft = 0;

		// Subtract margins of documentElement in case it's being used as parent
		// we do this only on HTML because it's the only element that behaves
		// differently when margins are applied to it. The margins are included in
		// the box of the documentElement, in the other cases not.
		if (!isIE10 && isHTML) {
			var marginTop = parseFloat(styles.marginTop, 10);
			var marginLeft = parseFloat(styles.marginLeft, 10);

			offsets.top -= borderTopWidth - marginTop;
			offsets.bottom -= borderTopWidth - marginTop;
			offsets.left -= borderLeftWidth - marginLeft;
			offsets.right -= borderLeftWidth - marginLeft;

			// Attach marginTop and marginLeft because in some circumstances we may need them
			offsets.marginTop = marginTop;
			offsets.marginLeft = marginLeft;
		}

		if (isIE10 ? parent.contains(scrollParent) : parent === scrollParent && scrollParent.nodeName !== 'BODY') {
			offsets = includeScroll(offsets, parent);
		}

		return offsets;
	}

	function getViewportOffsetRectRelativeToArtbitraryNode(element) {
		var html = element.ownerDocument.documentElement;
		var relativeOffset = getOffsetRectRelativeToArbitraryNode(element, html);
		var width = Math.max(html.clientWidth, window.innerWidth || 0);
		var height = Math.max(html.clientHeight, window.innerHeight || 0);

		var scrollTop = getScroll(html);
		var scrollLeft = getScroll(html, 'left');

		var offset = {
			top   : scrollTop - relativeOffset.top + relativeOffset.marginTop,
			left  : scrollLeft - relativeOffset.left + relativeOffset.marginLeft,
			width : width,
			height: height
		};

		return getClientRect(offset);
	}

	/**
	 * Check if the given element is fixed or is inside a fixed parent
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element
	 * @argument {Element} customContainer
	 * @returns {Boolean} answer to "isFixed?"
	 */
	function isFixed(element) {
		var nodeName = element.nodeName;
		if (nodeName === 'BODY' || nodeName === 'HTML') {
			return false;
		}
		if (getStyleComputedProperty(element, 'position') === 'fixed') {
			return true;
		}
		return isFixed(getParentNode(element));
	}

	/**
	 * Computed the boundaries limits and return them
	 * @method
	 * @memberof Popper.Utils
	 * @param {HTMLElement} popper
	 * @param {HTMLElement} reference
	 * @param {number} padding
	 * @param {HTMLElement} boundariesElement - Element used to define the boundaries
	 * @returns {Object} Coordinates of the boundaries
	 */
	function getBoundaries(popper, reference, padding, boundariesElement) {
		// NOTE: 1 DOM access here
		var boundaries = {
			top : 0,
			left: 0
		};
		var offsetParent = findCommonOffsetParent(popper, reference);

		// Handle viewport case
		if (boundariesElement === 'viewport') {
			boundaries = getViewportOffsetRectRelativeToArtbitraryNode(offsetParent);
		} else {
			// Handle other cases based on DOM element used as boundaries
			var boundariesNode = void 0;
			if (boundariesElement === 'scrollParent') {
				boundariesNode = getScrollParent(getParentNode(reference));
				if (boundariesNode.nodeName === 'BODY') {
					boundariesNode = popper.ownerDocument.documentElement;
				}
			} else if (boundariesElement === 'window') {
				boundariesNode = popper.ownerDocument.documentElement;
			} else {
				boundariesNode = boundariesElement;
			}

			var offsets = getOffsetRectRelativeToArbitraryNode(boundariesNode, offsetParent);

			// In case of HTML, we need a different computation
			if (boundariesNode.nodeName === 'HTML' && !isFixed(offsetParent)) {
				var _getWindowSizes = getWindowSizes(),
				    height          = _getWindowSizes.height,
				    width           = _getWindowSizes.width;

				boundaries.top += offsets.top - offsets.marginTop;
				boundaries.bottom = height + offsets.top;
				boundaries.left += offsets.left - offsets.marginLeft;
				boundaries.right = width + offsets.left;
			} else {
				// for all the other DOM elements, this one is good
				boundaries = offsets;
			}
		}

		// Add paddings
		boundaries.left += padding;
		boundaries.top += padding;
		boundaries.right -= padding;
		boundaries.bottom -= padding;

		return boundaries;
	}

	function getArea(_ref) {
		var width  = _ref.width,
		    height = _ref.height;

		return width * height;
	}

	/**
	 * Utility used to transform the `auto` placement to the placement with more
	 * available space.
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Object} data - The data object generated by update method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function computeAutoPlacement(placement, refRect, popper, reference, boundariesElement) {
		var padding = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 0;

		if (placement.indexOf('auto') === -1) {
			return placement;
		}

		var boundaries = getBoundaries(popper, reference, padding, boundariesElement);

		var rects = {
			top   : {
				width : boundaries.width,
				height: refRect.top - boundaries.top
			},
			right : {
				width : boundaries.right - refRect.right,
				height: boundaries.height
			},
			bottom: {
				width : boundaries.width,
				height: boundaries.bottom - refRect.bottom
			},
			left  : {
				width : refRect.left - boundaries.left,
				height: boundaries.height
			}
		};

		var sortedAreas = Object.keys(rects).map(function(key) {
			return _extends$1({
				key: key
			}, rects[key], {
				area: getArea(rects[key])
			});
		}).sort(function(a, b) {
			return b.area - a.area;
		});

		var filteredAreas = sortedAreas.filter(function(_ref2) {
			var width  = _ref2.width,
			    height = _ref2.height;
			return width >= popper.clientWidth && height >= popper.clientHeight;
		});

		var computedPlacement = filteredAreas.length > 0 ? filteredAreas[0].key : sortedAreas[0].key;

		var variation = placement.split('-')[1];

		return computedPlacement + (variation ? '-' + variation : '');
	}

	/**
	 * Get offsets to the reference element
	 * @method
	 * @memberof Popper.Utils
	 * @param {Object} state
	 * @param {Element} popper - the popper element
	 * @param {Element} reference - the reference element (the popper will be relative to this)
	 * @returns {Object} An object containing the offsets which will be applied to the popper
	 */
	function getReferenceOffsets(state, popper, reference) {
		var commonOffsetParent = findCommonOffsetParent(popper, reference);
		return getOffsetRectRelativeToArbitraryNode(reference, commonOffsetParent);
	}

	/**
	 * Get the outer sizes of the given element (offset size + margins)
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element
	 * @returns {Object} object containing width and height properties
	 */
	function getOuterSizes(element) {
		var styles = getComputedStyle(element);
		var x = parseFloat(styles.marginTop) + parseFloat(styles.marginBottom);
		var y = parseFloat(styles.marginLeft) + parseFloat(styles.marginRight);
		var result = {
			width : element.offsetWidth + y,
			height: element.offsetHeight + x
		};
		return result;
	}

	/**
	 * Get the opposite placement of the given one
	 * @method
	 * @memberof Popper.Utils
	 * @argument {String} placement
	 * @returns {String} flipped placement
	 */
	function getOppositePlacement(placement) {
		var hash = {
			left  : 'right',
			right : 'left',
			bottom: 'top',
			top   : 'bottom'
		};
		return placement.replace(/left|right|bottom|top/g, function(matched) {
			return hash[matched];
		});
	}

	/**
	 * Get offsets to the popper
	 * @method
	 * @memberof Popper.Utils
	 * @param {Object} position - CSS position the Popper will get applied
	 * @param {HTMLElement} popper - the popper element
	 * @param {Object} referenceOffsets - the reference offsets (the popper will be relative to this)
	 * @param {String} placement - one of the valid placement options
	 * @returns {Object} popperOffsets - An object containing the offsets which will be applied to the popper
	 */
	function getPopperOffsets(popper, referenceOffsets, placement) {
		placement = placement.split('-')[0];

		// Get popper node sizes
		var popperRect = getOuterSizes(popper);

		// Add position, width and height to our offsets object
		var popperOffsets = {
			width : popperRect.width,
			height: popperRect.height
		};

		// depending by the popper placement we have to compute its offsets slightly differently
		var isHoriz = [
			'right',
			'left'
		].indexOf(placement) !== -1;
		var mainSide = isHoriz ? 'top' : 'left';
		var secondarySide = isHoriz ? 'left' : 'top';
		var measurement = isHoriz ? 'height' : 'width';
		var secondaryMeasurement = !isHoriz ? 'height' : 'width';

		popperOffsets[mainSide] = referenceOffsets[mainSide] + referenceOffsets[measurement] / 2 - popperRect[measurement] / 2;
		if (placement === secondarySide) {
			popperOffsets[secondarySide] = referenceOffsets[secondarySide] - popperRect[secondaryMeasurement];
		} else {
			popperOffsets[secondarySide] = referenceOffsets[getOppositePlacement(secondarySide)];
		}

		return popperOffsets;
	}

	/**
	 * Mimics the `find` method of Array
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Array} arr
	 * @argument prop
	 * @argument value
	 * @returns index or -1
	 */
	function find(arr, check) {
		// use native find if supported
		if (Array.prototype.find) {
			return arr.find(check);
		}

		// use `filter` to obtain the same behavior of `find`
		return arr.filter(check)[0];
	}

	/**
	 * Return the index of the matching object
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Array} arr
	 * @argument prop
	 * @argument value
	 * @returns index or -1
	 */
	function findIndex(arr, prop, value) {
		// use native findIndex if supported
		if (Array.prototype.findIndex) {
			return arr.findIndex(function(cur) {
				return cur[prop] === value;
			});
		}

		// use `find` + `indexOf` if `findIndex` isn't supported
		var match = find(arr, function(obj) {
			return obj[prop] === value;
		});
		return arr.indexOf(match);
	}

	/**
	 * Loop trough the list of modifiers and run them in order,
	 * each of them will then edit the data object.
	 * @method
	 * @memberof Popper.Utils
	 * @param {dataObject} data
	 * @param {Array} modifiers
	 * @param {String} ends - Optional modifier name used as stopper
	 * @returns {dataObject}
	 */
	function runModifiers(modifiers, data, ends) {
		var modifiersToRun = ends === undefined ? modifiers : modifiers.slice(0, findIndex(modifiers, 'name', ends));

		modifiersToRun.forEach(function(modifier) {
			if (modifier['function']) {
				// eslint-disable-line dot-notation
				console.warn('`modifier.function` is deprecated, use `modifier.fn`!');
			}
			var fn = modifier['function'] || modifier.fn; // eslint-disable-line dot-notation
			if (modifier.enabled && isFunction(fn)) {
				// Add properties to offsets to make them a complete clientRect object
				// we do this before each modifier to make sure the previous one doesn't
				// mess with these values
				data.offsets.popper = getClientRect(data.offsets.popper);
				data.offsets.reference = getClientRect(data.offsets.reference);

				data = fn(data, modifier);
			}
		});

		return data;
	}

	/**
	 * Updates the position of the popper, computing the new offsets and applying
	 * the new style.<br />
	 * Prefer `scheduleUpdate` over `update` because of performance reasons.
	 * @method
	 * @memberof Popper
	 */
	function update() {
		// if popper is destroyed, don't perform any further update
		if (this.state.isDestroyed) {
			return;
		}

		var data = {
			instance   : this,
			styles     : {},
			arrowStyles: {},
			attributes : {},
			flipped    : false,
			offsets    : {}
		};

		// compute reference element offsets
		data.offsets.reference = getReferenceOffsets(this.state, this.popper, this.reference);

		// compute auto placement, store placement inside the data object,
		// modifiers will be able to edit `placement` if needed
		// and refer to originalPlacement to know the original value
		data.placement = computeAutoPlacement(this.options.placement, data.offsets.reference, this.popper, this.reference, this.options.modifiers.flip.boundariesElement, this.options.modifiers.flip.padding);

		// store the computed placement inside `originalPlacement`
		data.originalPlacement = data.placement;

		// compute the popper offsets
		data.offsets.popper = getPopperOffsets(this.popper, data.offsets.reference, data.placement);
		data.offsets.popper.position = 'absolute';

		// run the modifiers
		data = runModifiers(this.modifiers, data);

		// the first `update` will call `onCreate` callback
		// the other ones will call `onUpdate` callback
		if (!this.state.isCreated) {
			this.state.isCreated = true;
			this.options.onCreate(data);
		} else {
			this.options.onUpdate(data);
		}
	}

	/**
	 * Helper used to know if the given modifier is enabled.
	 * @method
	 * @memberof Popper.Utils
	 * @returns {Boolean}
	 */
	function isModifierEnabled(modifiers, modifierName) {
		return modifiers.some(function(_ref) {
			var name    = _ref.name,
			    enabled = _ref.enabled;
			return enabled && name === modifierName;
		});
	}

	/**
	 * Get the prefixed supported property name
	 * @method
	 * @memberof Popper.Utils
	 * @argument {String} property (camelCase)
	 * @returns {String} prefixed property (camelCase or PascalCase, depending on the vendor prefix)
	 */
	function getSupportedPropertyName(property) {
		var prefixes = [
			false,
			'ms',
			'Webkit',
			'Moz',
			'O'
		];
		var upperProp = property.charAt(0).toUpperCase() + property.slice(1);

		for (var i = 0; i < prefixes.length - 1; i++) {
			var prefix = prefixes[i];
			var toCheck = prefix ? '' + prefix + upperProp : property;
			if (typeof document.body.style[toCheck] !== 'undefined') {
				return toCheck;
			}
		}
		return null;
	}

	/**
	 * Destroy the popper
	 * @method
	 * @memberof Popper
	 */
	function destroy() {
		this.state.isDestroyed = true;

		// touch DOM only if `applyStyle` modifier is enabled
		if (isModifierEnabled(this.modifiers, 'applyStyle')) {
			this.popper.removeAttribute('x-placement');
			this.popper.style.left = '';
			this.popper.style.position = '';
			this.popper.style.top = '';
			this.popper.style[getSupportedPropertyName('transform')] = '';
		}

		this.disableEventListeners();

		// remove the popper if user explicity asked for the deletion on destroy
		// do not use `remove` because IE11 doesn't support it
		if (this.options.removeOnDestroy) {
			this.popper.parentNode.removeChild(this.popper);
		}
		return this;
	}

	/**
	 * Get the window associated with the element
	 * @argument {Element} element
	 * @returns {Window}
	 */
	function getWindow(element) {
		var ownerDocument = element.ownerDocument;
		return ownerDocument ? ownerDocument.defaultView : window;
	}

	function attachToScrollParents(scrollParent, event, callback, scrollParents) {
		var isBody = scrollParent.nodeName === 'BODY';
		var target = isBody ? scrollParent.ownerDocument.defaultView : scrollParent;
		target.addEventListener(event, callback, {passive: true});

		if (!isBody) {
			attachToScrollParents(getScrollParent(target.parentNode), event, callback, scrollParents);
		}
		scrollParents.push(target);
	}

	/**
	 * Setup needed event listeners used to update the popper position
	 * @method
	 * @memberof Popper.Utils
	 * @private
	 */
	function setupEventListeners(reference, options, state, updateBound) {
		// Resize event listener on window
		state.updateBound = updateBound;
		getWindow(reference).addEventListener('resize', state.updateBound, {passive: true});

		// Scroll event listener on scroll parents
		var scrollElement = getScrollParent(reference);
		attachToScrollParents(scrollElement, 'scroll', state.updateBound, state.scrollParents);
		state.scrollElement = scrollElement;
		state.eventsEnabled = true;

		return state;
	}

	/**
	 * It will add resize/scroll events and start recalculating
	 * position of the popper element when they are triggered.
	 * @method
	 * @memberof Popper
	 */
	function enableEventListeners() {
		if (!this.state.eventsEnabled) {
			this.state = setupEventListeners(this.reference, this.options, this.state, this.scheduleUpdate);
		}
	}

	/**
	 * Remove event listeners used to update the popper position
	 * @method
	 * @memberof Popper.Utils
	 * @private
	 */
	function removeEventListeners(reference, state) {
		// Remove resize event listener on window
		getWindow(reference).removeEventListener('resize', state.updateBound);

		// Remove scroll event listener on scroll parents
		state.scrollParents.forEach(function(target) {
			target.removeEventListener('scroll', state.updateBound);
		});

		// Reset state
		state.updateBound = null;
		state.scrollParents = [];
		state.scrollElement = null;
		state.eventsEnabled = false;
		return state;
	}

	/**
	 * It will remove resize/scroll events and won't recalculate popper position
	 * when they are triggered. It also won't trigger onUpdate callback anymore,
	 * unless you call `update` method manually.
	 * @method
	 * @memberof Popper
	 */
	function disableEventListeners() {
		if (this.state.eventsEnabled) {
			cancelAnimationFrame(this.scheduleUpdate);
			this.state = removeEventListeners(this.reference, this.state);
		}
	}

	/**
	 * Tells if a given input is a number
	 * @method
	 * @memberof Popper.Utils
	 * @param {*} input to check
	 * @return {Boolean}
	 */
	function isNumeric(n) {
		return n !== '' && !isNaN(parseFloat(n)) && isFinite(n);
	}

	/**
	 * Set the style to the given popper
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element - Element to apply the style to
	 * @argument {Object} styles
	 * Object with a list of properties and values which will be applied to the element
	 */
	function setStyles(element, styles) {
		Object.keys(styles).forEach(function(prop) {
			var unit = '';
			// add unit if the value is numeric and is one of the following
			if ([
				'width',
				'height',
				'top',
				'right',
				'bottom',
				'left'
			].indexOf(prop) !== -1 && isNumeric(styles[prop])) {
				unit = 'px';
			}
			element.style[prop] = styles[prop] + unit;
		});
	}

	/**
	 * Set the attributes to the given popper
	 * @method
	 * @memberof Popper.Utils
	 * @argument {Element} element - Element to apply the attributes to
	 * @argument {Object} styles
	 * Object with a list of properties and values which will be applied to the element
	 */
	function setAttributes(element, attributes) {
		Object.keys(attributes).forEach(function(prop) {
			var value = attributes[prop];
			if (value !== false) {
				element.setAttribute(prop, attributes[prop]);
			} else {
				element.removeAttribute(prop);
			}
		});
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by `update` method
	 * @argument {Object} data.styles - List of style properties - values to apply to popper element
	 * @argument {Object} data.attributes - List of attribute properties - values to apply to popper element
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The same data object
	 */
	function applyStyle(data) {
		// any property present in `data.styles` will be applied to the popper,
		// in this way we can make the 3rd party modifiers add custom styles to it
		// Be aware, modifiers could override the properties defined in the previous
		// lines of this modifier!
		setStyles(data.instance.popper, data.styles);

		// any property present in `data.attributes` will be applied to the popper,
		// they will be set as HTML attributes of the element
		setAttributes(data.instance.popper, data.attributes);

		// if arrowElement is defined and arrowStyles has some properties
		if (data.arrowElement && Object.keys(data.arrowStyles).length) {
			setStyles(data.arrowElement, data.arrowStyles);
		}

		return data;
	}

	/**
	 * Set the x-placement attribute before everything else because it could be used
	 * to add margins to the popper margins needs to be calculated to get the
	 * correct popper offsets.
	 * @method
	 * @memberof Popper.modifiers
	 * @param {HTMLElement} reference - The reference element used to position the popper
	 * @param {HTMLElement} popper - The HTML element used as popper.
	 * @param {Object} options - Popper.js options
	 */
	function applyStyleOnLoad(reference, popper, options, modifierOptions, state) {
		// compute reference element offsets
		var referenceOffsets = getReferenceOffsets(state, popper, reference);

		// compute auto placement, store placement inside the data object,
		// modifiers will be able to edit `placement` if needed
		// and refer to originalPlacement to know the original value
		var placement = computeAutoPlacement(options.placement, referenceOffsets, popper, reference, options.modifiers.flip.boundariesElement, options.modifiers.flip.padding);

		popper.setAttribute('x-placement', placement);

		// Apply `position` to popper before anything else because
		// without the position applied we can't guarantee correct computations
		setStyles(popper, {position: 'absolute'});

		return options;
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by `update` method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function computeStyle(data, options) {
		var x = options.x,
		    y = options.y;
		var popper = data.offsets.popper;

		// Remove this legacy support in Popper.js v2

		var legacyGpuAccelerationOption = find(data.instance.modifiers, function(modifier) {
			return modifier.name === 'applyStyle';
		}).gpuAcceleration;
		if (legacyGpuAccelerationOption !== undefined) {
			console.warn('WARNING: `gpuAcceleration` option moved to `computeStyle` modifier and will not be supported in future versions of Popper.js!');
		}
		var gpuAcceleration = legacyGpuAccelerationOption !== undefined ? legacyGpuAccelerationOption : options.gpuAcceleration;

		var offsetParent = getOffsetParent(data.instance.popper);
		var offsetParentRect = getBoundingClientRect(offsetParent);

		// Styles
		var styles = {
			position: popper.position
		};

		// floor sides to avoid blurry text
		var offsets = {
			left  : Math.floor(popper.left),
			top   : Math.floor(popper.top),
			bottom: Math.floor(popper.bottom),
			right : Math.floor(popper.right)
		};

		var sideA = x === 'bottom' ? 'top' : 'bottom';
		var sideB = y === 'right' ? 'left' : 'right';

		// if gpuAcceleration is set to `true` and transform is supported,
		//  we use `translate3d` to apply the position to the popper we
		// automatically use the supported prefixed version if needed
		var prefixedProperty = getSupportedPropertyName('transform');

		// now, let's make a step back and look at this code closely (wtf?)
		// If the content of the popper grows once it's been positioned, it
		// may happen that the popper gets misplaced because of the new content
		// overflowing its reference element
		// To avoid this problem, we provide two options (x and y), which allow
		// the consumer to define the offset origin.
		// If we position a popper on top of a reference element, we can set
		// `x` to `top` to make the popper grow towards its top instead of
		// its bottom.
		var left = void 0,
		    top  = void 0;
		if (sideA === 'bottom') {
			top = -offsetParentRect.height + offsets.bottom;
		} else {
			top = offsets.top;
		}
		if (sideB === 'right') {
			left = -offsetParentRect.width + offsets.right;
		} else {
			left = offsets.left;
		}
		if (gpuAcceleration && prefixedProperty) {
			styles[prefixedProperty] = 'translate3d(' + left + 'px, ' + top + 'px, 0)';
			styles[sideA] = 0;
			styles[sideB] = 0;
			styles.willChange = 'transform';
		} else {
			// othwerise, we use the standard `top`, `left`, `bottom` and `right` properties
			var invertTop = sideA === 'bottom' ? -1 : 1;
			var invertLeft = sideB === 'right' ? -1 : 1;
			styles[sideA] = top * invertTop;
			styles[sideB] = left * invertLeft;
			styles.willChange = sideA + ', ' + sideB;
		}

		// Attributes
		var attributes = {
			'x-placement': data.placement
		};

		// Update `data` attributes, styles and arrowStyles
		data.attributes = _extends$1({}, attributes, data.attributes);
		data.styles = _extends$1({}, styles, data.styles);
		data.arrowStyles = _extends$1({}, data.offsets.arrow, data.arrowStyles);

		return data;
	}

	/**
	 * Helper used to know if the given modifier depends from another one.<br />
	 * It checks if the needed modifier is listed and enabled.
	 * @method
	 * @memberof Popper.Utils
	 * @param {Array} modifiers - list of modifiers
	 * @param {String} requestingName - name of requesting modifier
	 * @param {String} requestedName - name of requested modifier
	 * @returns {Boolean}
	 */
	function isModifierRequired(modifiers, requestingName, requestedName) {
		var requesting = find(modifiers, function(_ref) {
			var name = _ref.name;
			return name === requestingName;
		});

		var isRequired = !!requesting && modifiers.some(function(modifier) {
			return modifier.name === requestedName && modifier.enabled && modifier.order < requesting.order;
		});

		if (!isRequired) {
			var _requesting = '`' + requestingName + '`';
			var requested = '`' + requestedName + '`';
			console.warn(requested + ' modifier is required by ' + _requesting + ' modifier in order to work, be sure to include it before ' + _requesting + '!');
		}
		return isRequired;
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by update method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function arrow(data, options) {
		var _data$offsets$arrow;

		// arrow depends on keepTogether in order to work
		if (!isModifierRequired(data.instance.modifiers, 'arrow', 'keepTogether')) {
			return data;
		}

		var arrowElement = options.element;

		// if arrowElement is a string, suppose it's a CSS selector
		if (typeof arrowElement === 'string') {
			arrowElement = data.instance.popper.querySelector(arrowElement);

			// if arrowElement is not found, don't run the modifier
			if (!arrowElement) {
				return data;
			}
		} else {
			// if the arrowElement isn't a query selector we must check that the
			// provided DOM node is child of its popper node
			if (!data.instance.popper.contains(arrowElement)) {
				console.warn('WARNING: `arrow.element` must be child of its popper element!');
				return data;
			}
		}

		var placement = data.placement.split('-')[0];
		var _data$offsets = data.offsets,
		    popper        = _data$offsets.popper,
		    reference     = _data$offsets.reference;

		var isVertical = [
			'left',
			'right'
		].indexOf(placement) !== -1;

		var len = isVertical ? 'height' : 'width';
		var sideCapitalized = isVertical ? 'Top' : 'Left';
		var side = sideCapitalized.toLowerCase();
		var altSide = isVertical ? 'left' : 'top';
		var opSide = isVertical ? 'bottom' : 'right';
		var arrowElementSize = getOuterSizes(arrowElement)[len];

		//
		// extends keepTogether behavior making sure the popper and its
		// reference have enough pixels in conjuction
		//

		// top/left side
		if (reference[opSide] - arrowElementSize < popper[side]) {
			data.offsets.popper[side] -= popper[side] - (reference[opSide] - arrowElementSize);
		}
		// bottom/right side
		if (reference[side] + arrowElementSize > popper[opSide]) {
			data.offsets.popper[side] += reference[side] + arrowElementSize - popper[opSide];
		}
		data.offsets.popper = getClientRect(data.offsets.popper);

		// compute center of the popper
		var center = reference[side] + reference[len] / 2 - arrowElementSize / 2;

		// Compute the sideValue using the updated popper offsets
		// take popper margin in account because we don't have this info available
		var css = getStyleComputedProperty(data.instance.popper);
		var popperMarginSide = parseFloat(css['margin' + sideCapitalized], 10);
		var popperBorderSide = parseFloat(css['border' + sideCapitalized + 'Width'], 10);
		var sideValue = center - data.offsets.popper[side] - popperMarginSide - popperBorderSide;

		// prevent arrowElement from being placed not contiguously to its popper
		sideValue = Math.max(Math.min(popper[len] - arrowElementSize, sideValue), 0);

		data.arrowElement = arrowElement;
		data.offsets.arrow = (_data$offsets$arrow = {}, defineProperty(_data$offsets$arrow, side, Math.round(sideValue)), defineProperty(_data$offsets$arrow, altSide, ''), _data$offsets$arrow);

		return data;
	}

	/**
	 * Get the opposite placement variation of the given one
	 * @method
	 * @memberof Popper.Utils
	 * @argument {String} placement variation
	 * @returns {String} flipped placement variation
	 */
	function getOppositeVariation(variation) {
		if (variation === 'end') {
			return 'start';
		} else if (variation === 'start') {
			return 'end';
		}
		return variation;
	}

	/**
	 * List of accepted placements to use as values of the `placement` option.<br />
	 * Valid placements are:
	 * - `auto`
	 * - `top`
	 * - `right`
	 * - `bottom`
	 * - `left`
	 *
	 * Each placement can have a variation from this list:
	 * - `-start`
	 * - `-end`
	 *
	 * Variations are interpreted easily if you think of them as the left to right
	 * written languages. Horizontally (`top` and `bottom`), `start` is left and `end`
	 * is right.<br />
	 * Vertically (`left` and `right`), `start` is top and `end` is bottom.
	 *
	 * Some valid examples are:
	 * - `top-end` (on top of reference, right aligned)
	 * - `right-start` (on right of reference, top aligned)
	 * - `bottom` (on bottom, centered)
	 * - `auto-right` (on the side with more space available, alignment depends by placement)
	 *
	 * @static
	 * @type {Array}
	 * @enum {String}
	 * @readonly
	 * @method placements
	 * @memberof Popper
	 */
	var placements = [
		'auto-start',
		'auto',
		'auto-end',
		'top-start',
		'top',
		'top-end',
		'right-start',
		'right',
		'right-end',
		'bottom-end',
		'bottom',
		'bottom-start',
		'left-end',
		'left',
		'left-start'
	];

	// Get rid of `auto` `auto-start` and `auto-end`
	var validPlacements = placements.slice(3);

	/**
	 * Given an initial placement, returns all the subsequent placements
	 * clockwise (or counter-clockwise).
	 *
	 * @method
	 * @memberof Popper.Utils
	 * @argument {String} placement - A valid placement (it accepts variations)
	 * @argument {Boolean} counter - Set to true to walk the placements counterclockwise
	 * @returns {Array} placements including their variations
	 */
	function clockwise(placement) {
		var counter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

		var index = validPlacements.indexOf(placement);
		var arr = validPlacements.slice(index + 1).concat(validPlacements.slice(0, index));
		return counter ? arr.reverse() : arr;
	}

	var BEHAVIORS = {
		FLIP            : 'flip',
		CLOCKWISE       : 'clockwise',
		COUNTERCLOCKWISE: 'counterclockwise'
	};

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by update method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function flip(data, options) {
		// if `inner` modifier is enabled, we can't use the `flip` modifier
		if (isModifierEnabled(data.instance.modifiers, 'inner')) {
			return data;
		}

		if (data.flipped && data.placement === data.originalPlacement) {
			// seems like flip is trying to loop, probably there's not enough space on any of the flippable sides
			return data;
		}

		var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, options.boundariesElement);

		var placement = data.placement.split('-')[0];
		var placementOpposite = getOppositePlacement(placement);
		var variation = data.placement.split('-')[1] || '';

		var flipOrder = [];

		switch (options.behavior) {
			case BEHAVIORS.FLIP:
				flipOrder = [
					placement,
					placementOpposite
				];
				break;
			case BEHAVIORS.CLOCKWISE:
				flipOrder = clockwise(placement);
				break;
			case BEHAVIORS.COUNTERCLOCKWISE:
				flipOrder = clockwise(placement, true);
				break;
			default:
				flipOrder = options.behavior;
		}

		flipOrder.forEach(function(step, index) {
			if (placement !== step || flipOrder.length === index + 1) {
				return data;
			}

			placement = data.placement.split('-')[0];
			placementOpposite = getOppositePlacement(placement);

			var popperOffsets = data.offsets.popper;
			var refOffsets = data.offsets.reference;

			// using floor because the reference offsets may contain decimals we are not going to consider here
			var floor = Math.floor;
			var overlapsRef = placement === 'left' && floor(popperOffsets.right) > floor(refOffsets.left) || placement === 'right' && floor(popperOffsets.left) < floor(refOffsets.right) || placement === 'top' && floor(popperOffsets.bottom) > floor(refOffsets.top) || placement === 'bottom' && floor(popperOffsets.top) < floor(refOffsets.bottom);

			var overflowsLeft = floor(popperOffsets.left) < floor(boundaries.left);
			var overflowsRight = floor(popperOffsets.right) > floor(boundaries.right);
			var overflowsTop = floor(popperOffsets.top) < floor(boundaries.top);
			var overflowsBottom = floor(popperOffsets.bottom) > floor(boundaries.bottom);

			var overflowsBoundaries = placement === 'left' && overflowsLeft || placement === 'right' && overflowsRight || placement === 'top' && overflowsTop || placement === 'bottom' && overflowsBottom;

			// flip the variation if required
			var isVertical = [
				'top',
				'bottom'
			].indexOf(placement) !== -1;
			var flippedVariation = !!options.flipVariations && (isVertical && variation === 'start' && overflowsLeft || isVertical && variation === 'end' && overflowsRight || !isVertical && variation === 'start' && overflowsTop || !isVertical && variation === 'end' && overflowsBottom);

			if (overlapsRef || overflowsBoundaries || flippedVariation) {
				// this boolean to detect any flip loop
				data.flipped = true;

				if (overlapsRef || overflowsBoundaries) {
					placement = flipOrder[index + 1];
				}

				if (flippedVariation) {
					variation = getOppositeVariation(variation);
				}

				data.placement = placement + (variation ? '-' + variation : '');

				// this object contains `position`, we want to preserve it along with
				// any additional property we may add in the future
				data.offsets.popper = _extends$1({}, data.offsets.popper, getPopperOffsets(data.instance.popper, data.offsets.reference, data.placement));

				data = runModifiers(data.instance.modifiers, data, 'flip');
			}
		});
		return data;
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by update method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function keepTogether(data) {
		var _data$offsets = data.offsets,
		    popper        = _data$offsets.popper,
		    reference     = _data$offsets.reference;

		var placement = data.placement.split('-')[0];
		var floor = Math.floor;
		var isVertical = [
			'top',
			'bottom'
		].indexOf(placement) !== -1;
		var side = isVertical ? 'right' : 'bottom';
		var opSide = isVertical ? 'left' : 'top';
		var measurement = isVertical ? 'width' : 'height';

		if (popper[side] < floor(reference[opSide])) {
			data.offsets.popper[opSide] = floor(reference[opSide]) - popper[measurement];
		}
		if (popper[opSide] > floor(reference[side])) {
			data.offsets.popper[opSide] = floor(reference[side]);
		}

		return data;
	}

	/**
	 * Converts a string containing value + unit into a px value number
	 * @function
	 * @memberof {modifiers~offset}
	 * @private
	 * @argument {String} str - Value + unit string
	 * @argument {String} measurement - `height` or `width`
	 * @argument {Object} popperOffsets
	 * @argument {Object} referenceOffsets
	 * @returns {Number|String}
	 * Value in pixels, or original string if no values were extracted
	 */
	function toValue(str, measurement, popperOffsets, referenceOffsets) {
		// separate value from unit
		var split = str.match(/((?:\-|\+)?\d*\.?\d*)(.*)/);
		var value = +split[1];
		var unit = split[2];

		// If it's not a number it's an operator, I guess
		if (!value) {
			return str;
		}

		if (unit.indexOf('%') === 0) {
			var element = void 0;
			switch (unit) {
				case '%p':
					element = popperOffsets;
					break;
				case '%':
				case '%r':
				default:
					element = referenceOffsets;
			}

			var rect = getClientRect(element);
			return rect[measurement] / 100 * value;
		} else if (unit === 'vh' || unit === 'vw') {
			// if is a vh or vw, we calculate the size based on the viewport
			var size = void 0;
			if (unit === 'vh') {
				size = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
			} else {
				size = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
			}
			return size / 100 * value;
		} else {
			// if is an explicit pixel unit, we get rid of the unit and keep the value
			// if is an implicit unit, it's px, and we return just the value
			return value;
		}
	}

	/**
	 * Parse an `offset` string to extrapolate `x` and `y` numeric offsets.
	 * @function
	 * @memberof {modifiers~offset}
	 * @private
	 * @argument {String} offset
	 * @argument {Object} popperOffsets
	 * @argument {Object} referenceOffsets
	 * @argument {String} basePlacement
	 * @returns {Array} a two cells array with x and y offsets in numbers
	 */
	function parseOffset(offset, popperOffsets, referenceOffsets, basePlacement) {
		var offsets = [
			0,
			0
		];

		// Use height if placement is left or right and index is 0 otherwise use width
		// in this way the first offset will use an axis and the second one
		// will use the other one
		var useHeight = [
			'right',
			'left'
		].indexOf(basePlacement) !== -1;

		// Split the offset string to obtain a list of values and operands
		// The regex addresses values with the plus or minus sign in front (+10, -20, etc)
		var fragments = offset.split(/(\+|\-)/).map(function(frag) {
			return frag.trim();
		});

		// Detect if the offset string contains a pair of values or a single one
		// they could be separated by comma or space
		var divider = fragments.indexOf(find(fragments, function(frag) {
			return frag.search(/,|\s/) !== -1;
		}));

		if (fragments[divider] && fragments[divider].indexOf(',') === -1) {
			console.warn('Offsets separated by white space(s) are deprecated, use a comma (,) instead.');
		}

		// If divider is found, we divide the list of values and operands to divide
		// them by ofset X and Y.
		var splitRegex = /\s*,\s*|\s+/;
		var ops = divider !== -1 ? [
			fragments.slice(0, divider).concat([fragments[divider].split(splitRegex)[0]]),
			[fragments[divider].split(splitRegex)[1]].concat(fragments.slice(divider + 1))
		] : [fragments];

		// Convert the values with units to absolute pixels to allow our computations
		ops = ops.map(function(op, index) {
			// Most of the units rely on the orientation of the popper
			var measurement = (index === 1 ? !useHeight : useHeight) ? 'height' : 'width';
			var mergeWithPrevious = false;
			return op
				// This aggregates any `+` or `-` sign that aren't considered operators
				// e.g.: 10 + +5 => [10, +, +5]
				.reduce(function(a, b) {
					if (a[a.length - 1] === '' && [
						'+',
						'-'
					].indexOf(b) !== -1) {
						a[a.length - 1] = b;
						mergeWithPrevious = true;
						return a;
					} else if (mergeWithPrevious) {
						a[a.length - 1] += b;
						mergeWithPrevious = false;
						return a;
					} else {
						return a.concat(b);
					}
				}, [])
				// Here we convert the string values into number values (in px)
				.map(function(str) {
					return toValue(str, measurement, popperOffsets, referenceOffsets);
				});
		});

		// Loop trough the offsets arrays and execute the operations
		ops.forEach(function(op, index) {
			op.forEach(function(frag, index2) {
				if (isNumeric(frag)) {
					offsets[index] += frag * (op[index2 - 1] === '-' ? -1 : 1);
				}
			});
		});
		return offsets;
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by update method
	 * @argument {Object} options - Modifiers configuration and options
	 * @argument {Number|String} options.offset=0
	 * The offset value as described in the modifier description
	 * @returns {Object} The data object, properly modified
	 */
	function offset(data, _ref) {
		var offset = _ref.offset;
		var placement     = data.placement,
		    _data$offsets = data.offsets,
		    popper        = _data$offsets.popper,
		    reference     = _data$offsets.reference;

		var basePlacement = placement.split('-')[0];

		var offsets = void 0;
		if (isNumeric(+offset)) {
			offsets = [
				+offset,
				0
			];
		} else {
			offsets = parseOffset(offset, popper, reference, basePlacement);
		}

		if (basePlacement === 'left') {
			popper.top += offsets[0];
			popper.left -= offsets[1];
		} else if (basePlacement === 'right') {
			popper.top += offsets[0];
			popper.left += offsets[1];
		} else if (basePlacement === 'top') {
			popper.left += offsets[0];
			popper.top -= offsets[1];
		} else if (basePlacement === 'bottom') {
			popper.left += offsets[0];
			popper.top += offsets[1];
		}

		data.popper = popper;
		return data;
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by `update` method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function preventOverflow(data, options) {
		var boundariesElement = options.boundariesElement || getOffsetParent(data.instance.popper);

		// If offsetParent is the reference element, we really want to
		// go one step up and use the next offsetParent as reference to
		// avoid to make this modifier completely useless and look like broken
		if (data.instance.reference === boundariesElement) {
			boundariesElement = getOffsetParent(boundariesElement);
		}

		var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, boundariesElement);
		options.boundaries = boundaries;

		var order = options.priority;
		var popper = data.offsets.popper;

		var check = {
			primary  : function primary(placement) {
				var value = popper[placement];
				if (popper[placement] < boundaries[placement] && !options.escapeWithReference) {
					value = Math.max(popper[placement], boundaries[placement]);
				}
				return defineProperty({}, placement, value);
			},
			secondary: function secondary(placement) {
				var mainSide = placement === 'right' ? 'left' : 'top';
				var value = popper[mainSide];
				if (popper[placement] > boundaries[placement] && !options.escapeWithReference) {
					value = Math.min(popper[mainSide], boundaries[placement] - (placement === 'right' ? popper.width : popper.height));
				}
				return defineProperty({}, mainSide, value);
			}
		};

		order.forEach(function(placement) {
			var side = [
				'left',
				'top'
			].indexOf(placement) !== -1 ? 'primary' : 'secondary';
			popper = _extends$1({}, popper, check[side](placement));
		});

		data.offsets.popper = popper;

		return data;
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by `update` method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function shift(data) {
		var placement = data.placement;
		var basePlacement = placement.split('-')[0];
		var shiftvariation = placement.split('-')[1];

		// if shift shiftvariation is specified, run the modifier
		if (shiftvariation) {
			var _data$offsets = data.offsets,
			    reference     = _data$offsets.reference,
			    popper        = _data$offsets.popper;

			var isVertical = [
				'bottom',
				'top'
			].indexOf(basePlacement) !== -1;
			var side = isVertical ? 'left' : 'top';
			var measurement = isVertical ? 'width' : 'height';

			var shiftOffsets = {
				start: defineProperty({}, side, reference[side]),
				end  : defineProperty({}, side, reference[side] + reference[measurement] - popper[measurement])
			};

			data.offsets.popper = _extends$1({}, popper, shiftOffsets[shiftvariation]);
		}

		return data;
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by update method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function hide(data) {
		if (!isModifierRequired(data.instance.modifiers, 'hide', 'preventOverflow')) {
			return data;
		}

		var refRect = data.offsets.reference;
		var bound = find(data.instance.modifiers, function(modifier) {
			return modifier.name === 'preventOverflow';
		}).boundaries;

		if (refRect.bottom < bound.top || refRect.left > bound.right || refRect.top > bound.bottom || refRect.right < bound.left) {
			// Avoid unnecessary DOM access if visibility hasn't changed
			if (data.hide === true) {
				return data;
			}

			data.hide = true;
			data.attributes['x-out-of-boundaries'] = '';
		} else {
			// Avoid unnecessary DOM access if visibility hasn't changed
			if (data.hide === false) {
				return data;
			}

			data.hide = false;
			data.attributes['x-out-of-boundaries'] = false;
		}

		return data;
	}

	/**
	 * @function
	 * @memberof Modifiers
	 * @argument {Object} data - The data object generated by `update` method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {Object} The data object, properly modified
	 */
	function inner(data) {
		var placement = data.placement;
		var basePlacement = placement.split('-')[0];
		var _data$offsets = data.offsets,
		    popper        = _data$offsets.popper,
		    reference     = _data$offsets.reference;

		var isHoriz = [
			'left',
			'right'
		].indexOf(basePlacement) !== -1;

		var subtractLength = [
			'top',
			'left'
		].indexOf(basePlacement) === -1;

		popper[isHoriz ? 'left' : 'top'] = reference[basePlacement] - (subtractLength ? popper[isHoriz ? 'width' : 'height'] : 0);

		data.placement = getOppositePlacement(placement);
		data.offsets.popper = getClientRect(popper);

		return data;
	}

	/**
	 * Modifier function, each modifier can have a function of this type assigned
	 * to its `fn` property.<br />
	 * These functions will be called on each update, this means that you must
	 * make sure they are performant enough to avoid performance bottlenecks.
	 *
	 * @function ModifierFn
	 * @argument {dataObject} data - The data object generated by `update` method
	 * @argument {Object} options - Modifiers configuration and options
	 * @returns {dataObject} The data object, properly modified
	 */

	/**
	 * Modifiers are plugins used to alter the behavior of your poppers.<br />
	 * Popper.js uses a set of 9 modifiers to provide all the basic functionalities
	 * needed by the library.
	 *
	 * Usually you don't want to override the `order`, `fn` and `onLoad` props.
	 * All the other properties are configurations that could be tweaked.
	 * @namespace modifiers
	 */
	var modifiers = {
		/**
		 * Modifier used to shift the popper on the start or end of its reference
		 * element.<br />
		 * It will read the variation of the `placement` property.<br />
		 * It can be one either `-end` or `-start`.
		 * @memberof modifiers
		 * @inner
		 */
		shift: {
			/** @prop {number} order=100 - Index used to define the order of execution */
			order  : 100,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled: true,
			/** @prop {ModifierFn} */
			fn     : shift
		},

		/**
		 * The `offset` modifier can shift your popper on both its axis.
		 *
		 * It accepts the following units:
		 * - `px` or unitless, interpreted as pixels
		 * - `%` or `%r`, percentage relative to the length of the reference element
		 * - `%p`, percentage relative to the length of the popper element
		 * - `vw`, CSS viewport width unit
		 * - `vh`, CSS viewport height unit
		 *
		 * For length is intended the main axis relative to the placement of the popper.<br />
		 * This means that if the placement is `top` or `bottom`, the length will be the
		 * `width`. In case of `left` or `right`, it will be the height.
		 *
		 * You can provide a single value (as `Number` or `String`), or a pair of values
		 * as `String` divided by a comma or one (or more) white spaces.<br />
		 * The latter is a deprecated method because it leads to confusion and will be
		 * removed in v2.<br />
		 * Additionally, it accepts additions and subtractions between different units.
		 * Note that multiplications and divisions aren't supported.
		 *
		 * Valid examples are:
		 * ```
		 * 10
		 * '10%'
		 * '10, 10'
		 * '10%, 10'
		 * '10 + 10%'
		 * '10 - 5vh + 3%'
		 * '-10px + 5vh, 5px - 6%'
		 * ```
		 * > **NB**: If you desire to apply offsets to your poppers in a way that may make them overlap
		 * > with their reference element, unfortunately, you will have to disable the `flip` modifier.
		 * > More on this [reading this issue](https://github.com/FezVrasta/popper.js/issues/373)
		 *
		 * @memberof modifiers
		 * @inner
		 */
		offset: {
			/** @prop {number} order=200 - Index used to define the order of execution */
			order  : 200,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled: true,
			/** @prop {ModifierFn} */
			fn     : offset,
			/** @prop {Number|String} offset=0
			 * The offset value as described in the modifier description
			 */
			offset : 0
		},

		/**
		 * Modifier used to prevent the popper from being positioned outside the boundary.
		 *
		 * An scenario exists where the reference itself is not within the boundaries.<br />
		 * We can say it has "escaped the boundaries" — or just "escaped".<br />
		 * In this case we need to decide whether the popper should either:
		 *
		 * - detach from the reference and remain "trapped" in the boundaries, or
		 * - if it should ignore the boundary and "escape with its reference"
		 *
		 * When `escapeWithReference` is set to`true` and reference is completely
		 * outside its boundaries, the popper will overflow (or completely leave)
		 * the boundaries in order to remain attached to the edge of the reference.
		 *
		 * @memberof modifiers
		 * @inner
		 */
		preventOverflow: {
			/** @prop {number} order=300 - Index used to define the order of execution */
			order            : 300,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled          : true,
			/** @prop {ModifierFn} */
			fn               : preventOverflow,
			/**
			 * @prop {Array} [priority=['left','right','top','bottom']]
			 * Popper will try to prevent overflow following these priorities by default,
			 * then, it could overflow on the left and on top of the `boundariesElement`
			 */
			priority         : [
				'left',
				'right',
				'top',
				'bottom'
			],
			/**
			 * @prop {number} padding=5
			 * Amount of pixel used to define a minimum distance between the boundaries
			 * and the popper this makes sure the popper has always a little padding
			 * between the edges of its container
			 */
			padding          : 5,
			/**
			 * @prop {String|HTMLElement} boundariesElement='scrollParent'
			 * Boundaries used by the modifier, can be `scrollParent`, `window`,
			 * `viewport` or any DOM element.
			 */
			boundariesElement: 'scrollParent'
		},

		/**
		 * Modifier used to make sure the reference and its popper stay near eachothers
		 * without leaving any gap between the two. Expecially useful when the arrow is
		 * enabled and you want to assure it to point to its reference element.
		 * It cares only about the first axis, you can still have poppers with margin
		 * between the popper and its reference element.
		 * @memberof modifiers
		 * @inner
		 */
		keepTogether: {
			/** @prop {number} order=400 - Index used to define the order of execution */
			order  : 400,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled: true,
			/** @prop {ModifierFn} */
			fn     : keepTogether
		},

		/**
		 * This modifier is used to move the `arrowElement` of the popper to make
		 * sure it is positioned between the reference element and its popper element.
		 * It will read the outer size of the `arrowElement` node to detect how many
		 * pixels of conjuction are needed.
		 *
		 * It has no effect if no `arrowElement` is provided.
		 * @memberof modifiers
		 * @inner
		 */
		arrow: {
			/** @prop {number} order=500 - Index used to define the order of execution */
			order  : 500,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled: true,
			/** @prop {ModifierFn} */
			fn     : arrow,
			/** @prop {String|HTMLElement} element='[x-arrow]' - Selector or node used as arrow */
			element: '[x-arrow]'
		},

		/**
		 * Modifier used to flip the popper's placement when it starts to overlap its
		 * reference element.
		 *
		 * Requires the `preventOverflow` modifier before it in order to work.
		 *
		 * **NOTE:** this modifier will interrupt the current update cycle and will
		 * restart it if it detects the need to flip the placement.
		 * @memberof modifiers
		 * @inner
		 */
		flip: {
			/** @prop {number} order=600 - Index used to define the order of execution */
			order            : 600,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled          : true,
			/** @prop {ModifierFn} */
			fn               : flip,
			/**
			 * @prop {String|Array} behavior='flip'
			 * The behavior used to change the popper's placement. It can be one of
			 * `flip`, `clockwise`, `counterclockwise` or an array with a list of valid
			 * placements (with optional variations).
			 */
			behavior         : 'flip',
			/**
			 * @prop {number} padding=5
			 * The popper will flip if it hits the edges of the `boundariesElement`
			 */
			padding          : 5,
			/**
			 * @prop {String|HTMLElement} boundariesElement='viewport'
			 * The element which will define the boundaries of the popper position,
			 * the popper will never be placed outside of the defined boundaries
			 * (except if keepTogether is enabled)
			 */
			boundariesElement: 'viewport'
		},

		/**
		 * Modifier used to make the popper flow toward the inner of the reference element.
		 * By default, when this modifier is disabled, the popper will be placed outside
		 * the reference element.
		 * @memberof modifiers
		 * @inner
		 */
		inner: {
			/** @prop {number} order=700 - Index used to define the order of execution */
			order  : 700,
			/** @prop {Boolean} enabled=false - Whether the modifier is enabled or not */
			enabled: false,
			/** @prop {ModifierFn} */
			fn     : inner
		},

		/**
		 * Modifier used to hide the popper when its reference element is outside of the
		 * popper boundaries. It will set a `x-out-of-boundaries` attribute which can
		 * be used to hide with a CSS selector the popper when its reference is
		 * out of boundaries.
		 *
		 * Requires the `preventOverflow` modifier before it in order to work.
		 * @memberof modifiers
		 * @inner
		 */
		hide: {
			/** @prop {number} order=800 - Index used to define the order of execution */
			order  : 800,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled: true,
			/** @prop {ModifierFn} */
			fn     : hide
		},

		/**
		 * Computes the style that will be applied to the popper element to gets
		 * properly positioned.
		 *
		 * Note that this modifier will not touch the DOM, it just prepares the styles
		 * so that `applyStyle` modifier can apply it. This separation is useful
		 * in case you need to replace `applyStyle` with a custom implementation.
		 *
		 * This modifier has `850` as `order` value to maintain backward compatibility
		 * with previous versions of Popper.js. Expect the modifiers ordering method
		 * to change in future major versions of the library.
		 *
		 * @memberof modifiers
		 * @inner
		 */
		computeStyle: {
			/** @prop {number} order=850 - Index used to define the order of execution */
			order          : 850,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled        : true,
			/** @prop {ModifierFn} */
			fn             : computeStyle,
			/**
			 * @prop {Boolean} gpuAcceleration=true
			 * If true, it uses the CSS 3d transformation to position the popper.
			 * Otherwise, it will use the `top` and `left` properties.
			 */
			gpuAcceleration: true,
			/**
			 * @prop {string} [x='bottom']
			 * Where to anchor the X axis (`bottom` or `top`). AKA X offset origin.
			 * Change this if your popper should grow in a direction different from `bottom`
			 */
			x              : 'bottom',
			/**
			 * @prop {string} [x='left']
			 * Where to anchor the Y axis (`left` or `right`). AKA Y offset origin.
			 * Change this if your popper should grow in a direction different from `right`
			 */
			y              : 'right'
		},

		/**
		 * Applies the computed styles to the popper element.
		 *
		 * All the DOM manipulations are limited to this modifier. This is useful in case
		 * you want to integrate Popper.js inside a framework or view library and you
		 * want to delegate all the DOM manipulations to it.
		 *
		 * Note that if you disable this modifier, you must make sure the popper element
		 * has its position set to `absolute` before Popper.js can do its work!
		 *
		 * Just disable this modifier and define you own to achieve the desired effect.
		 *
		 * @memberof modifiers
		 * @inner
		 */
		applyStyle: {
			/** @prop {number} order=900 - Index used to define the order of execution */
			order          : 900,
			/** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
			enabled        : true,
			/** @prop {ModifierFn} */
			fn             : applyStyle,
			/** @prop {Function} */
			onLoad         : applyStyleOnLoad,
			/**
			 * @deprecated since version 1.10.0, the property moved to `computeStyle` modifier
			 * @prop {Boolean} gpuAcceleration=true
			 * If true, it uses the CSS 3d transformation to position the popper.
			 * Otherwise, it will use the `top` and `left` properties.
			 */
			gpuAcceleration: undefined
		}
	};

	/**
	 * The `dataObject` is an object containing all the informations used by Popper.js
	 * this object get passed to modifiers and to the `onCreate` and `onUpdate` callbacks.
	 * @name dataObject
	 * @property {Object} data.instance The Popper.js instance
	 * @property {String} data.placement Placement applied to popper
	 * @property {String} data.originalPlacement Placement originally defined on init
	 * @property {Boolean} data.flipped True if popper has been flipped by flip modifier
	 * @property {Boolean} data.hide True if the reference element is out of boundaries, useful to know when to hide the popper.
	 * @property {HTMLElement} data.arrowElement Node used as arrow by arrow modifier
	 * @property {Object} data.styles Any CSS property defined here will be applied to the popper, it expects the JavaScript nomenclature (eg. `marginBottom`)
	 * @property {Object} data.arrowStyles Any CSS property defined here will be applied to the popper arrow, it expects the JavaScript nomenclature (eg. `marginBottom`)
	 * @property {Object} data.boundaries Offsets of the popper boundaries
	 * @property {Object} data.offsets The measurements of popper, reference and arrow elements.
	 * @property {Object} data.offsets.popper `top`, `left`, `width`, `height` values
	 * @property {Object} data.offsets.reference `top`, `left`, `width`, `height` values
	 * @property {Object} data.offsets.arrow] `top` and `left` offsets, only one of them will be different from 0
	 */

	/**
	 * Default options provided to Popper.js constructor.<br />
	 * These can be overriden using the `options` argument of Popper.js.<br />
	 * To override an option, simply pass as 3rd argument an object with the same
	 * structure of this object, example:
	 * ```
	 * new Popper(ref, pop, {
	 *   modifiers: {
	 *     preventOverflow: { enabled: false }
	 *   }
	 * })
	 * ```
	 * @type {Object}
	 * @static
	 * @memberof Popper
	 */
	var Defaults = {
		/**
		 * Popper's placement
		 * @prop {Popper.placements} placement='bottom'
		 */
		placement: 'bottom',

		/**
		 * Whether events (resize, scroll) are initially enabled
		 * @prop {Boolean} eventsEnabled=true
		 */
		eventsEnabled: true,

		/**
		 * Set to true if you want to automatically remove the popper when
		 * you call the `destroy` method.
		 * @prop {Boolean} removeOnDestroy=false
		 */
		removeOnDestroy: false,

		/**
		 * Callback called when the popper is created.<br />
		 * By default, is set to no-op.<br />
		 * Access Popper.js instance with `data.instance`.
		 * @prop {onCreate}
		 */
		onCreate: function onCreate() {},

		/**
		 * Callback called when the popper is updated, this callback is not called
		 * on the initialization/creation of the popper, but only on subsequent
		 * updates.<br />
		 * By default, is set to no-op.<br />
		 * Access Popper.js instance with `data.instance`.
		 * @prop {onUpdate}
		 */
		onUpdate: function onUpdate() {},

		/**
		 * List of modifiers used to modify the offsets before they are applied to the popper.
		 * They provide most of the functionalities of Popper.js
		 * @prop {modifiers}
		 */
		modifiers: modifiers
	};

	/**
	 * @callback onCreate
	 * @param {dataObject} data
	 */

	/**
	 * @callback onUpdate
	 * @param {dataObject} data
	 */

	    // Utils
	    // Methods
	var Popper = function() {
		    /**
		     * Create a new Popper.js instance
		     * @class Popper
		     * @param {HTMLElement|referenceObject} reference - The reference element used to position the popper
		     * @param {HTMLElement} popper - The HTML element used as popper.
		     * @param {Object} options - Your custom options to override the ones defined in [Defaults](#defaults)
		     * @return {Object} instance - The generated Popper.js instance
		     */
		    function Popper(reference, popper) {
			    var _this = this;

			    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
			    classCallCheck(this, Popper);

			    this.scheduleUpdate = function() {
				    return requestAnimationFrame(_this.update);
			    };

			    // make update() debounced, so that it only runs at most once-per-tick
			    this.update = debounce(this.update.bind(this));

			    // with {} we create a new object with the options inside it
			    this.options = _extends$1({}, Popper.Defaults, options);

			    // init state
			    this.state = {
				    isDestroyed  : false,
				    isCreated    : false,
				    scrollParents: []
			    };

			    // get reference and popper elements (allow jQuery wrappers)
			    this.reference = reference && reference.jquery ? reference[0] : reference;
			    this.popper = popper && popper.jquery ? popper[0] : popper;

			    // Deep merge modifiers options
			    this.options.modifiers = {};
			    Object.keys(_extends$1({}, Popper.Defaults.modifiers, options.modifiers)).forEach(function(name) {
				    _this.options.modifiers[name] = _extends$1({}, Popper.Defaults.modifiers[name] || {}, options.modifiers ? options.modifiers[name] : {});
			    });

			    // Refactoring modifiers' list (Object => Array)
			    this.modifiers = Object.keys(this.options.modifiers).map(function(name) {
					    return _extends$1({
						    name: name
					    }, _this.options.modifiers[name]);
				    })
				    // sort the modifiers by order
				    .sort(function(a, b) {
					    return a.order - b.order;
				    });

			    // modifiers have the ability to execute arbitrary code when Popper.js get inited
			    // such code is executed in the same order of its modifier
			    // they could add new properties to their options configuration
			    // BE AWARE: don't add options to `options.modifiers.name` but to `modifierOptions`!
			    this.modifiers.forEach(function(modifierOptions) {
				    if (modifierOptions.enabled && isFunction(modifierOptions.onLoad)) {
					    modifierOptions.onLoad(_this.reference, _this.popper, _this.options, modifierOptions, _this.state);
				    }
			    });

			    // fire the first update to position the popper in the right place
			    this.update();

			    var eventsEnabled = this.options.eventsEnabled;
			    if (eventsEnabled) {
				    // setup event listeners, they will take care of update the position in specific situations
				    this.enableEventListeners();
			    }

			    this.state.eventsEnabled = eventsEnabled;
		    }

		    // We can't use class properties because they don't get listed in the
		    // class prototype and break stuff like Sinon stubs

		    createClass(Popper, [
			    {
				    key  : 'update',
				    value: function update$$1() {
					    return update.call(this);
				    }
			    },
			    {
				    key  : 'destroy',
				    value: function destroy$$1() {
					    return destroy.call(this);
				    }
			    },
			    {
				    key  : 'enableEventListeners',
				    value: function enableEventListeners$$1() {
					    return enableEventListeners.call(this);
				    }
			    },
			    {
				    key  : 'disableEventListeners',
				    value: function disableEventListeners$$1() {
					    return disableEventListeners.call(this);
				    }

				    /**
				     * Schedule an update, it will run on the next UI update available
				     * @method scheduleUpdate
				     * @memberof Popper
				     */


				    /**
				     * Collection of utilities useful when writing custom modifiers.
				     * Starting from version 1.7, this method is available only if you
				     * include `popper-utils.js` before `popper.js`.
				     *
				     * **DEPRECATION**: This way to access PopperUtils is deprecated
				     * and will be removed in v2! Use the PopperUtils module directly instead.
				     * Due to the high instability of the methods contained in Utils, we can't
				     * guarantee them to follow semver. Use them at your own risk!
				     * @static
				     * @private
				     * @type {Object}
				     * @deprecated since version 1.8
				     * @member Utils
				     * @memberof Popper
				     */

			    }
		    ]);
		    return Popper;
	    }();

	/**
	 * The `referenceObject` is an object that provides an interface compatible with Popper.js
	 * and lets you use it as replacement of a real DOM node.<br />
	 * You can use this method to position a popper relatively to a set of coordinates
	 * in case you don't have a DOM node to use as reference.
	 *
	 * ```
	 * new Popper(referenceObject, popperNode);
	 * ```
	 *
	 * NB: This feature isn't supported in Internet Explorer 10
	 * @name referenceObject
	 * @property {Function} data.getBoundingClientRect
	 * A function that returns a set of coordinates compatible with the native `getBoundingClientRect` method.
	 * @property {number} data.clientWidth
	 * An ES6 getter that will return the width of the virtual reference element.
	 * @property {number} data.clientHeight
	 * An ES6 getter that will return the height of the virtual reference element.
	 */

	Popper.Utils = (typeof window !== 'undefined' ? window : global).PopperUtils;
	Popper.placements = placements;
	Popper.Defaults = Defaults;

	/**
	 * --------------------------------------------------------------------------
	 * Bootstrap (v4.0.0): tooltip.js
	 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	 * --------------------------------------------------------------------------
	 */

	var Tooltip = function($$$1) {
		/**
		 * ------------------------------------------------------------------------
		 * Constants
		 * ------------------------------------------------------------------------
		 */
		var NAME = 'tooltip';
		var VERSION = '4.0.0';
		var DATA_KEY = 'bs.tooltip';
		var EVENT_KEY = '.' + DATA_KEY;
		var JQUERY_NO_CONFLICT = $$$1.fn[NAME];
		var TRANSITION_DURATION = 150;
		var CLASS_PREFIX = 'bs-tooltip';
		var BSCLS_PREFIX_REGEX = new RegExp('(^|\\s)' + CLASS_PREFIX + '\\S+', 'g');
		var DefaultType = {
			animation        : 'boolean',
			template         : 'string',
			title            : '(string|element|function)',
			trigger          : 'string',
			delay            : '(number|object)',
			html             : 'boolean',
			selector         : '(string|boolean)',
			placement        : '(string|function)',
			offset           : '(number|string)',
			container        : '(string|element|boolean)',
			fallbackPlacement: '(string|array)',
			boundary         : '(string|element)'
		};
		var AttachmentMap = {
			AUTO  : 'auto',
			TOP   : 'top',
			RIGHT : 'right',
			BOTTOM: 'bottom',
			LEFT  : 'left'
		};
		var Default = {
			animation        : true,
			template         : '<div class="tooltip" role="tooltip">' + '<div class="arrow"></div>' + '<div class="tooltip-inner"></div></div>',
			trigger          : 'hover focus',
			title            : '',
			delay            : 0,
			html             : false,
			selector         : false,
			placement        : 'top',
			offset           : 0,
			container        : false,
			fallbackPlacement: 'flip',
			boundary         : 'scrollParent'
		};
		var HoverState = {
			SHOW: 'show',
			OUT : 'out'
		};
		var Event = {
			HIDE      : 'hide' + EVENT_KEY,
			HIDDEN    : 'hidden' + EVENT_KEY,
			SHOW      : 'show' + EVENT_KEY,
			SHOWN     : 'shown' + EVENT_KEY,
			INSERTED  : 'inserted' + EVENT_KEY,
			CLICK     : 'click' + EVENT_KEY,
			FOCUSIN   : 'focusin' + EVENT_KEY,
			FOCUSOUT  : 'focusout' + EVENT_KEY,
			MOUSEENTER: 'mouseenter' + EVENT_KEY,
			MOUSELEAVE: 'mouseleave' + EVENT_KEY
		};
		var ClassName = {
			FADE: 'fade',
			SHOW: 'show'
		};
		var Selector = {
			TOOLTIP      : '.tooltip',
			TOOLTIP_INNER: '.tooltip-inner',
			ARROW        : '.arrow'
		};
		var Trigger = {
			HOVER : 'hover',
			FOCUS : 'focus',
			CLICK : 'click',
			MANUAL: 'manual'
			/**
			 * ------------------------------------------------------------------------
			 * Class Definition
			 * ------------------------------------------------------------------------
			 */

		};

		var Tooltip =
			    /*#__PURE__*/
			    function() {
				    function Tooltip(element, config) {
					    /**
					     * Check for Popper dependency
					     * Popper - https://popper.js.org
					     */
					    if (typeof Popper === 'undefined') {
						    throw new TypeError('Bootstrap tooltips require Popper.js (https://popper.js.org)');
					    } // private

					    this._isEnabled = true;
					    this._timeout = 0;
					    this._hoverState = '';
					    this._activeTrigger = {};
					    this._popper = null; // Protected

					    this.element = element;
					    this.config = this._getConfig(config);
					    this.tip = null;

					    this._setListeners();
				    } // Getters

				    var _proto = Tooltip.prototype;

				    // Public
				    _proto.enable = function enable() {
					    this._isEnabled = true;
				    };

				    _proto.disable = function disable() {
					    this._isEnabled = false;
				    };

				    _proto.toggleEnabled = function toggleEnabled() {
					    this._isEnabled = !this._isEnabled;
				    };

				    _proto.toggle = function toggle(event) {
					    if (!this._isEnabled) {
						    return;
					    }

					    if (event) {
						    var dataKey = this.constructor.DATA_KEY;
						    var context = $$$1(event.currentTarget).data(dataKey);

						    if (!context) {
							    context = new this.constructor(event.currentTarget, this._getDelegateConfig());
							    $$$1(event.currentTarget).data(dataKey, context);
						    }

						    context._activeTrigger.click = !context._activeTrigger.click;

						    if (context._isWithActiveTrigger()) {
							    context._enter(null, context);
						    } else {
							    context._leave(null, context);
						    }
					    } else {
						    if ($$$1(this.getTipElement()).hasClass(ClassName.SHOW)) {
							    this._leave(null, this);

							    return;
						    }

						    this._enter(null, this);
					    }
				    };

				    _proto.dispose = function dispose() {
					    clearTimeout(this._timeout);
					    $$$1.removeData(this.element, this.constructor.DATA_KEY);
					    $$$1(this.element).off(this.constructor.EVENT_KEY);
					    $$$1(this.element).closest('.modal').off('hide.bs.modal');

					    if (this.tip) {
						    $$$1(this.tip).remove();
					    }

					    this._isEnabled = null;
					    this._timeout = null;
					    this._hoverState = null;
					    this._activeTrigger = null;

					    if (this._popper !== null) {
						    this._popper.destroy();
					    }

					    this._popper = null;
					    this.element = null;
					    this.config = null;
					    this.tip = null;
				    };

				    _proto.show = function show() {
					    var _this = this;

					    if ($$$1(this.element).css('display') === 'none') {
						    throw new Error('Please use show on visible elements');
					    }

					    var showEvent = $$$1.Event(this.constructor.Event.SHOW);

					    if (this.isWithContent() && this._isEnabled) {
						    $$$1(this.element).trigger(showEvent);
						    var isInTheDom = $$$1.contains(this.element.ownerDocument.documentElement, this.element);

						    if (showEvent.isDefaultPrevented() || !isInTheDom) {
							    return;
						    }

						    var tip = this.getTipElement();
						    var tipId = Util.getUID(this.constructor.NAME);
						    tip.setAttribute('id', tipId);
						    this.element.setAttribute('aria-describedby', tipId);
						    this.setContent();

						    if (this.config.animation) {
							    $$$1(tip).addClass(ClassName.FADE);
						    }

						    var placement = typeof this.config.placement === 'function' ? this.config.placement.call(this, tip, this.element) : this.config.placement;

						    var attachment = this._getAttachment(placement);

						    this.addAttachmentClass(attachment);
						    var container = this.config.container === false ? document.body : $$$1(this.config.container);
						    $$$1(tip).data(this.constructor.DATA_KEY, this);

						    if (!$$$1.contains(this.element.ownerDocument.documentElement, this.tip)) {
							    $$$1(tip).appendTo(container);
						    }

						    $$$1(this.element).trigger(this.constructor.Event.INSERTED);
						    this._popper = new Popper(this.element, tip, {
							    placement: attachment,
							    modifiers: {
								    offset         : {
									    offset: this.config.offset
								    },
								    flip           : {
									    behavior: this.config.fallbackPlacement
								    },
								    arrow          : {
									    element: Selector.ARROW
								    },
								    preventOverflow: {
									    boundariesElement: this.config.boundary
								    }
							    },
							    onCreate : function onCreate(data) {
								    if (data.originalPlacement !== data.placement) {
									    _this._handlePopperPlacementChange(data);
								    }
							    },
							    onUpdate : function onUpdate(data) {
								    _this._handlePopperPlacementChange(data);
							    }
						    });
						    $$$1(tip).addClass(ClassName.SHOW); // If this is a touch-enabled device we add extra
						    // empty mouseover listeners to the body's immediate children;
						    // only needed because of broken event delegation on iOS
						    // https://www.quirksmode.org/blog/archives/2014/02/mouse_event_bub.html

						    if ('ontouchstart' in document.documentElement) {
							    $$$1('body').children().on('mouseover', null, $$$1.noop);
						    }

						    var complete = function complete() {
							    if (_this.config.animation) {
								    _this._fixTransition();
							    }

							    var prevHoverState = _this._hoverState;
							    _this._hoverState = null;
							    $$$1(_this.element).trigger(_this.constructor.Event.SHOWN);

							    if (prevHoverState === HoverState.OUT) {
								    _this._leave(null, _this);
							    }
						    };

						    if (Util.supportsTransitionEnd() && $$$1(this.tip).hasClass(ClassName.FADE)) {
							    $$$1(this.tip).one(Util.TRANSITION_END, complete).emulateTransitionEnd(Tooltip._TRANSITION_DURATION);
						    } else {
							    complete();
						    }
					    }
				    };

				    _proto.hide = function hide(callback) {
					    var _this2 = this;

					    var tip = this.getTipElement();
					    var hideEvent = $$$1.Event(this.constructor.Event.HIDE);

					    var complete = function complete() {
						    if (_this2._hoverState !== HoverState.SHOW && tip.parentNode) {
							    tip.parentNode.removeChild(tip);
						    }

						    _this2._cleanTipClass();

						    _this2.element.removeAttribute('aria-describedby');

						    $$$1(_this2.element).trigger(_this2.constructor.Event.HIDDEN);

						    if (_this2._popper !== null) {
							    _this2._popper.destroy();
						    }

						    if (callback) {
							    callback();
						    }
					    };

					    $$$1(this.element).trigger(hideEvent);

					    if (hideEvent.isDefaultPrevented()) {
						    return;
					    }

					    $$$1(tip).removeClass(ClassName.SHOW); // If this is a touch-enabled device we remove the extra
					    // empty mouseover listeners we added for iOS support

					    if ('ontouchstart' in document.documentElement) {
						    $$$1('body').children().off('mouseover', null, $$$1.noop);
					    }

					    this._activeTrigger[Trigger.CLICK] = false;
					    this._activeTrigger[Trigger.FOCUS] = false;
					    this._activeTrigger[Trigger.HOVER] = false;

					    if (Util.supportsTransitionEnd() && $$$1(this.tip).hasClass(ClassName.FADE)) {
						    $$$1(tip).one(Util.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
					    } else {
						    complete();
					    }

					    this._hoverState = '';
				    };

				    _proto.update = function update() {
					    if (this._popper !== null) {
						    this._popper.scheduleUpdate();
					    }
				    }; // Protected

				    _proto.isWithContent = function isWithContent() {
					    return Boolean(this.getTitle());
				    };

				    _proto.addAttachmentClass = function addAttachmentClass(attachment) {
					    $$$1(this.getTipElement()).addClass(CLASS_PREFIX + '-' + attachment);
				    };

				    _proto.getTipElement = function getTipElement() {
					    this.tip = this.tip || $$$1(this.config.template)[0];
					    return this.tip;
				    };

				    _proto.setContent = function setContent() {
					    var $tip = $$$1(this.getTipElement());
					    this.setElementContent($tip.find(Selector.TOOLTIP_INNER), this.getTitle());
					    $tip.removeClass(ClassName.FADE + ' ' + ClassName.SHOW);
				    };

				    _proto.setElementContent = function setElementContent($element, content) {
					    var html = this.config.html;

					    if (typeof content === 'object' && (content.nodeType || content.jquery)) {
						    // Content is a DOM node or a jQuery
						    if (html) {
							    if (!$$$1(content).parent().is($element)) {
								    $element.empty().append(content);
							    }
						    } else {
							    $element.text($$$1(content).text());
						    }
					    } else {
						    $element[html ? 'html' : 'text'](content);
					    }
				    };

				    _proto.getTitle = function getTitle() {
					    var title = this.element.getAttribute('data-original-title');

					    if (!title) {
						    title = typeof this.config.title === 'function' ? this.config.title.call(this.element) : this.config.title;
					    }

					    return title;
				    }; // Private

				    _proto._getAttachment = function _getAttachment(placement) {
					    return AttachmentMap[placement.toUpperCase()];
				    };

				    _proto._setListeners = function _setListeners() {
					    var _this3 = this;

					    var triggers = this.config.trigger.split(' ');
					    triggers.forEach(function(trigger) {
						    if (trigger === 'click') {
							    $$$1(_this3.element).on(_this3.constructor.Event.CLICK, _this3.config.selector, function(event) {
								    return _this3.toggle(event);
							    });
						    } else if (trigger !== Trigger.MANUAL) {
							    var eventIn = trigger === Trigger.HOVER ? _this3.constructor.Event.MOUSEENTER : _this3.constructor.Event.FOCUSIN;
							    var eventOut = trigger === Trigger.HOVER ? _this3.constructor.Event.MOUSELEAVE : _this3.constructor.Event.FOCUSOUT;
							    $$$1(_this3.element).on(eventIn, _this3.config.selector, function(event) {
								    return _this3._enter(event);
							    }).on(eventOut, _this3.config.selector, function(event) {
								    return _this3._leave(event);
							    });
						    }

						    $$$1(_this3.element).closest('.modal').on('hide.bs.modal', function() {
							    return _this3.hide();
						    });
					    });

					    if (this.config.selector) {
						    this.config = _extends({}, this.config, {
							    trigger : 'manual',
							    selector: ''
						    });
					    } else {
						    this._fixTitle();
					    }
				    };

				    _proto._fixTitle = function _fixTitle() {
					    var titleType = typeof this.element.getAttribute('data-original-title');

					    if (this.element.getAttribute('title') || titleType !== 'string') {
						    this.element.setAttribute('data-original-title', this.element.getAttribute('title') || '');
						    this.element.setAttribute('title', '');
					    }
				    };

				    _proto._enter = function _enter(event, context) {
					    var dataKey = this.constructor.DATA_KEY;
					    context = context || $$$1(event.currentTarget).data(dataKey);

					    if (!context) {
						    context = new this.constructor(event.currentTarget, this._getDelegateConfig());
						    $$$1(event.currentTarget).data(dataKey, context);
					    }

					    if (event) {
						    context._activeTrigger[event.type === 'focusin' ? Trigger.FOCUS : Trigger.HOVER] = true;
					    }

					    if ($$$1(context.getTipElement()).hasClass(ClassName.SHOW) || context._hoverState === HoverState.SHOW) {
						    context._hoverState = HoverState.SHOW;
						    return;
					    }

					    clearTimeout(context._timeout);
					    context._hoverState = HoverState.SHOW;

					    if (!context.config.delay || !context.config.delay.show) {
						    context.show();
						    return;
					    }

					    context._timeout = setTimeout(function() {
						    if (context._hoverState === HoverState.SHOW) {
							    context.show();
						    }
					    }, context.config.delay.show);
				    };

				    _proto._leave = function _leave(event, context) {
					    var dataKey = this.constructor.DATA_KEY;
					    context = context || $$$1(event.currentTarget).data(dataKey);

					    if (!context) {
						    context = new this.constructor(event.currentTarget, this._getDelegateConfig());
						    $$$1(event.currentTarget).data(dataKey, context);
					    }

					    if (event) {
						    context._activeTrigger[event.type === 'focusout' ? Trigger.FOCUS : Trigger.HOVER] = false;
					    }

					    if (context._isWithActiveTrigger()) {
						    return;
					    }

					    clearTimeout(context._timeout);
					    context._hoverState = HoverState.OUT;

					    if (!context.config.delay || !context.config.delay.hide) {
						    context.hide();
						    return;
					    }

					    context._timeout = setTimeout(function() {
						    if (context._hoverState === HoverState.OUT) {
							    context.hide();
						    }
					    }, context.config.delay.hide);
				    };

				    _proto._isWithActiveTrigger = function _isWithActiveTrigger() {
					    for (var trigger in this._activeTrigger) {
						    if (this._activeTrigger[trigger]) {
							    return true;
						    }
					    }

					    return false;
				    };

				    _proto._getConfig = function _getConfig(config) {
					    config = _extends({}, this.constructor.Default, $$$1(this.element).data(), config);

					    if (typeof config.delay === 'number') {
						    config.delay = {
							    show: config.delay,
							    hide: config.delay
						    };
					    }

					    if (typeof config.title === 'number') {
						    config.title = config.title.toString();
					    }

					    if (typeof config.content === 'number') {
						    config.content = config.content.toString();
					    }

					    Util.typeCheckConfig(NAME, config, this.constructor.DefaultType);
					    return config;
				    };

				    _proto._getDelegateConfig = function _getDelegateConfig() {
					    var config = {};

					    if (this.config) {
						    for (var key in this.config) {
							    if (this.constructor.Default[key] !== this.config[key]) {
								    config[key] = this.config[key];
							    }
						    }
					    }

					    return config;
				    };

				    _proto._cleanTipClass = function _cleanTipClass() {
					    var $tip = $$$1(this.getTipElement());
					    var tabClass = $tip.attr('class').match(BSCLS_PREFIX_REGEX);

					    if (tabClass !== null && tabClass.length > 0) {
						    $tip.removeClass(tabClass.join(''));
					    }
				    };

				    _proto._handlePopperPlacementChange = function _handlePopperPlacementChange(data) {
					    this._cleanTipClass();

					    this.addAttachmentClass(this._getAttachment(data.placement));
				    };

				    _proto._fixTransition = function _fixTransition() {
					    var tip = this.getTipElement();
					    var initConfigAnimation = this.config.animation;

					    if (tip.getAttribute('x-placement') !== null) {
						    return;
					    }

					    $$$1(tip).removeClass(ClassName.FADE);
					    this.config.animation = false;
					    this.hide();
					    this.show();
					    this.config.animation = initConfigAnimation;
				    }; // Static

				    Tooltip._jQueryInterface = function _jQueryInterface(config) {
					    return this.each(function() {
						    var data = $$$1(this).data(DATA_KEY);

						    var _config = typeof config === 'object' && config;

						    if (!data && /dispose|hide/.test(config)) {
							    return;
						    }

						    if (!data) {
							    data = new Tooltip(this, _config);
							    $$$1(this).data(DATA_KEY, data);
						    }

						    if (typeof config === 'string') {
							    if (typeof data[config] === 'undefined') {
								    throw new TypeError('No method named "' + config + '"');
							    }

							    data[config]();
						    }
					    });
				    };

				    _createClass(Tooltip, null, [
					    {
						    key: 'VERSION',
						    get: function get() {
							    return VERSION;
						    }
					    },
					    {
						    key: 'Default',
						    get: function get() {
							    return Default;
						    }
					    },
					    {
						    key: 'NAME',
						    get: function get() {
							    return NAME;
						    }
					    },
					    {
						    key: 'DATA_KEY',
						    get: function get() {
							    return DATA_KEY;
						    }
					    },
					    {
						    key: 'Event',
						    get: function get() {
							    return Event;
						    }
					    },
					    {
						    key: 'EVENT_KEY',
						    get: function get() {
							    return EVENT_KEY;
						    }
					    },
					    {
						    key: 'DefaultType',
						    get: function get() {
							    return DefaultType;
						    }
					    }
				    ]);
				    return Tooltip;
			    }();
		/**
		 * ------------------------------------------------------------------------
		 * jQuery
		 * ------------------------------------------------------------------------
		 */

		$$$1.fn[NAME] = Tooltip._jQueryInterface;
		$$$1.fn[NAME].Constructor = Tooltip;

		$$$1.fn[NAME].noConflict = function() {
			$$$1.fn[NAME] = JQUERY_NO_CONFLICT;
			return Tooltip._jQueryInterface;
		};

		return Tooltip;
	}($, Popper);

	/**
	 * --------------------------------------------------------------------------
	 * Bootstrap (v4.0.0-alpha.6): index.js
	 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	 * --------------------------------------------------------------------------
	 */

	(function($$$1) {
		if (typeof $$$1 === 'undefined') {
			throw new TypeError('Bootstrap\'s JavaScript requires jQuery. jQuery must be included before Bootstrap\'s JavaScript.');
		}

		var version = $$$1.fn.jquery.split(' ')[0].split('.');
		var minMajor = 1;
		var ltMajor = 2;
		var minMinor = 9;
		var minPatch = 1;
		var maxMajor = 4;

		if (version[0] < ltMajor && version[1] < minMinor || version[0] === minMajor && version[1] === minMinor && version[2] < minPatch || version[0] >= maxMajor) {
			throw new Error('Bootstrap\'s JavaScript requires at least jQuery v1.9.1 but less than v4.0.0');
		}
	})($);

	exports.Util = Util;
	exports.Tooltip = Tooltip;

	Object.defineProperty(exports, '__esModule', {value: true});

})));

/*! @vimeo/player v2.10.0 | (c) 2019 Vimeo | MIT License | https://github.com/vimeo/player.js */
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global = global || self, (global.Vimeo = global.Vimeo || {}, global.Vimeo.Player = factory()));
}(this, function () { 'use strict';

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
  }

  /**
   * @module lib/functions
   */

  /**
   * Check to see this is a node environment.
   * @type {Boolean}
   */

  /* global global */
  var isNode = typeof global !== 'undefined' && {}.toString.call(global) === '[object global]';
  /**
   * Get the name of the method for a given getter or setter.
   *
   * @param {string} prop The name of the property.
   * @param {string} type Either “get” or “set”.
   * @return {string}
   */

  function getMethodName(prop, type) {
    if (prop.indexOf(type.toLowerCase()) === 0) {
      return prop;
    }

    return "".concat(type.toLowerCase()).concat(prop.substr(0, 1).toUpperCase()).concat(prop.substr(1));
  }
  /**
   * Check to see if the object is a DOM Element.
   *
   * @param {*} element The object to check.
   * @return {boolean}
   */

  function isDomElement(element) {
    return Boolean(element && element.nodeType === 1 && 'nodeName' in element && element.ownerDocument && element.ownerDocument.defaultView);
  }
  /**
   * Check to see whether the value is a number.
   *
   * @see http://dl.dropboxusercontent.com/u/35146/js/tests/isNumber.html
   * @param {*} value The value to check.
   * @param {boolean} integer Check if the value is an integer.
   * @return {boolean}
   */

  function isInteger(value) {
    // eslint-disable-next-line eqeqeq
    return !isNaN(parseFloat(value)) && isFinite(value) && Math.floor(value) == value;
  }
  /**
   * Check to see if the URL is a Vimeo url.
   *
   * @param {string} url The url string.
   * @return {boolean}
   */

  function isVimeoUrl(url) {
    return /^(https?:)?\/\/((player|www)\.)?vimeo\.com(?=$|\/)/.test(url);
  }
  /**
   * Get the Vimeo URL from an element.
   * The element must have either a data-vimeo-id or data-vimeo-url attribute.
   *
   * @param {object} oEmbedParameters The oEmbed parameters.
   * @return {string}
   */

  function getVimeoUrl() {
    var oEmbedParameters = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var id = oEmbedParameters.id;
    var url = oEmbedParameters.url;
    var idOrUrl = id || url;

    if (!idOrUrl) {
      throw new Error('An id or url must be passed, either in an options object or as a data-vimeo-id or data-vimeo-url attribute.');
    }

    if (isInteger(idOrUrl)) {
      return "https://vimeo.com/".concat(idOrUrl);
    }

    if (isVimeoUrl(idOrUrl)) {
      return idOrUrl.replace('http:', 'https:');
    }

    if (id) {
      throw new TypeError("\u201C".concat(id, "\u201D is not a valid video id."));
    }

    throw new TypeError("\u201C".concat(idOrUrl, "\u201D is not a vimeo.com url."));
  }

  var arrayIndexOfSupport = typeof Array.prototype.indexOf !== 'undefined';
  var postMessageSupport = typeof window !== 'undefined' && typeof window.postMessage !== 'undefined';

  if (!isNode && (!arrayIndexOfSupport || !postMessageSupport)) {
    throw new Error('Sorry, the Vimeo Player API is not available in this browser.');
  }

  var commonjsGlobal = typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

  function createCommonjsModule(fn, module) {
  	return module = { exports: {} }, fn(module, module.exports), module.exports;
  }

  /*!
   * weakmap-polyfill v2.0.0 - ECMAScript6 WeakMap polyfill
   * https://github.com/polygonplanet/weakmap-polyfill
   * Copyright (c) 2015-2016 polygon planet <polygon.planet.aqua@gmail.com>
   * @license MIT
   */
  (function (self) {

    if (self.WeakMap) {
      return;
    }

    var hasOwnProperty = Object.prototype.hasOwnProperty;

    var defineProperty = function (object, name, value) {
      if (Object.defineProperty) {
        Object.defineProperty(object, name, {
          configurable: true,
          writable: true,
          value: value
        });
      } else {
        object[name] = value;
      }
    };

    self.WeakMap = function () {
      // ECMA-262 23.3 WeakMap Objects
      function WeakMap() {
        if (this === void 0) {
          throw new TypeError("Constructor WeakMap requires 'new'");
        }

        defineProperty(this, '_id', genId('_WeakMap')); // ECMA-262 23.3.1.1 WeakMap([iterable])

        if (arguments.length > 0) {
          // Currently, WeakMap `iterable` argument is not supported
          throw new TypeError('WeakMap iterable is not supported');
        }
      } // ECMA-262 23.3.3.2 WeakMap.prototype.delete(key)


      defineProperty(WeakMap.prototype, 'delete', function (key) {
        checkInstance(this, 'delete');

        if (!isObject(key)) {
          return false;
        }

        var entry = key[this._id];

        if (entry && entry[0] === key) {
          delete key[this._id];
          return true;
        }

        return false;
      }); // ECMA-262 23.3.3.3 WeakMap.prototype.get(key)

      defineProperty(WeakMap.prototype, 'get', function (key) {
        checkInstance(this, 'get');

        if (!isObject(key)) {
          return void 0;
        }

        var entry = key[this._id];

        if (entry && entry[0] === key) {
          return entry[1];
        }

        return void 0;
      }); // ECMA-262 23.3.3.4 WeakMap.prototype.has(key)

      defineProperty(WeakMap.prototype, 'has', function (key) {
        checkInstance(this, 'has');

        if (!isObject(key)) {
          return false;
        }

        var entry = key[this._id];

        if (entry && entry[0] === key) {
          return true;
        }

        return false;
      }); // ECMA-262 23.3.3.5 WeakMap.prototype.set(key, value)

      defineProperty(WeakMap.prototype, 'set', function (key, value) {
        checkInstance(this, 'set');

        if (!isObject(key)) {
          throw new TypeError('Invalid value used as weak map key');
        }

        var entry = key[this._id];

        if (entry && entry[0] === key) {
          entry[1] = value;
          return this;
        }

        defineProperty(key, this._id, [key, value]);
        return this;
      });

      function checkInstance(x, methodName) {
        if (!isObject(x) || !hasOwnProperty.call(x, '_id')) {
          throw new TypeError(methodName + ' method called on incompatible receiver ' + typeof x);
        }
      }

      function genId(prefix) {
        return prefix + '_' + rand() + '.' + rand();
      }

      function rand() {
        return Math.random().toString().substring(2);
      }

      defineProperty(WeakMap, '_polyfill', true);
      return WeakMap;
    }();

    function isObject(x) {
      return Object(x) === x;
    }
  })(typeof self !== 'undefined' ? self : typeof window !== 'undefined' ? window : typeof commonjsGlobal !== 'undefined' ? commonjsGlobal : commonjsGlobal);

  var npo_src = createCommonjsModule(function (module) {
  /*! Native Promise Only
      v0.8.1 (c) Kyle Simpson
      MIT License: http://getify.mit-license.org
  */
  (function UMD(name, context, definition) {
    // special form of UMD for polyfilling across evironments
    context[name] = context[name] || definition();

    if (module.exports) {
      module.exports = context[name];
    }
  })("Promise", typeof commonjsGlobal != "undefined" ? commonjsGlobal : commonjsGlobal, function DEF() {

    var builtInProp,
        cycle,
        scheduling_queue,
        ToString = Object.prototype.toString,
        timer = typeof setImmediate != "undefined" ? function timer(fn) {
      return setImmediate(fn);
    } : setTimeout; // dammit, IE8.

    try {
      Object.defineProperty({}, "x", {});

      builtInProp = function builtInProp(obj, name, val, config) {
        return Object.defineProperty(obj, name, {
          value: val,
          writable: true,
          configurable: config !== false
        });
      };
    } catch (err) {
      builtInProp = function builtInProp(obj, name, val) {
        obj[name] = val;
        return obj;
      };
    } // Note: using a queue instead of array for efficiency


    scheduling_queue = function Queue() {
      var first, last, item;

      function Item(fn, self) {
        this.fn = fn;
        this.self = self;
        this.next = void 0;
      }

      return {
        add: function add(fn, self) {
          item = new Item(fn, self);

          if (last) {
            last.next = item;
          } else {
            first = item;
          }

          last = item;
          item = void 0;
        },
        drain: function drain() {
          var f = first;
          first = last = cycle = void 0;

          while (f) {
            f.fn.call(f.self);
            f = f.next;
          }
        }
      };
    }();

    function schedule(fn, self) {
      scheduling_queue.add(fn, self);

      if (!cycle) {
        cycle = timer(scheduling_queue.drain);
      }
    } // promise duck typing


    function isThenable(o) {
      var _then,
          o_type = typeof o;

      if (o != null && (o_type == "object" || o_type == "function")) {
        _then = o.then;
      }

      return typeof _then == "function" ? _then : false;
    }

    function notify() {
      for (var i = 0; i < this.chain.length; i++) {
        notifyIsolated(this, this.state === 1 ? this.chain[i].success : this.chain[i].failure, this.chain[i]);
      }

      this.chain.length = 0;
    } // NOTE: This is a separate function to isolate
    // the `try..catch` so that other code can be
    // optimized better


    function notifyIsolated(self, cb, chain) {
      var ret, _then;

      try {
        if (cb === false) {
          chain.reject(self.msg);
        } else {
          if (cb === true) {
            ret = self.msg;
          } else {
            ret = cb.call(void 0, self.msg);
          }

          if (ret === chain.promise) {
            chain.reject(TypeError("Promise-chain cycle"));
          } else if (_then = isThenable(ret)) {
            _then.call(ret, chain.resolve, chain.reject);
          } else {
            chain.resolve(ret);
          }
        }
      } catch (err) {
        chain.reject(err);
      }
    }

    function resolve(msg) {
      var _then,
          self = this; // already triggered?


      if (self.triggered) {
        return;
      }

      self.triggered = true; // unwrap

      if (self.def) {
        self = self.def;
      }

      try {
        if (_then = isThenable(msg)) {
          schedule(function () {
            var def_wrapper = new MakeDefWrapper(self);

            try {
              _then.call(msg, function $resolve$() {
                resolve.apply(def_wrapper, arguments);
              }, function $reject$() {
                reject.apply(def_wrapper, arguments);
              });
            } catch (err) {
              reject.call(def_wrapper, err);
            }
          });
        } else {
          self.msg = msg;
          self.state = 1;

          if (self.chain.length > 0) {
            schedule(notify, self);
          }
        }
      } catch (err) {
        reject.call(new MakeDefWrapper(self), err);
      }
    }

    function reject(msg) {
      var self = this; // already triggered?

      if (self.triggered) {
        return;
      }

      self.triggered = true; // unwrap

      if (self.def) {
        self = self.def;
      }

      self.msg = msg;
      self.state = 2;

      if (self.chain.length > 0) {
        schedule(notify, self);
      }
    }

    function iteratePromises(Constructor, arr, resolver, rejecter) {
      for (var idx = 0; idx < arr.length; idx++) {
        (function IIFE(idx) {
          Constructor.resolve(arr[idx]).then(function $resolver$(msg) {
            resolver(idx, msg);
          }, rejecter);
        })(idx);
      }
    }

    function MakeDefWrapper(self) {
      this.def = self;
      this.triggered = false;
    }

    function MakeDef(self) {
      this.promise = self;
      this.state = 0;
      this.triggered = false;
      this.chain = [];
      this.msg = void 0;
    }

    function Promise(executor) {
      if (typeof executor != "function") {
        throw TypeError("Not a function");
      }

      if (this.__NPO__ !== 0) {
        throw TypeError("Not a promise");
      } // instance shadowing the inherited "brand"
      // to signal an already "initialized" promise


      this.__NPO__ = 1;
      var def = new MakeDef(this);

      this["then"] = function then(success, failure) {
        var o = {
          success: typeof success == "function" ? success : true,
          failure: typeof failure == "function" ? failure : false
        }; // Note: `then(..)` itself can be borrowed to be used against
        // a different promise constructor for making the chained promise,
        // by substituting a different `this` binding.

        o.promise = new this.constructor(function extractChain(resolve, reject) {
          if (typeof resolve != "function" || typeof reject != "function") {
            throw TypeError("Not a function");
          }

          o.resolve = resolve;
          o.reject = reject;
        });
        def.chain.push(o);

        if (def.state !== 0) {
          schedule(notify, def);
        }

        return o.promise;
      };

      this["catch"] = function $catch$(failure) {
        return this.then(void 0, failure);
      };

      try {
        executor.call(void 0, function publicResolve(msg) {
          resolve.call(def, msg);
        }, function publicReject(msg) {
          reject.call(def, msg);
        });
      } catch (err) {
        reject.call(def, err);
      }
    }

    var PromisePrototype = builtInProp({}, "constructor", Promise,
    /*configurable=*/
    false); // Note: Android 4 cannot use `Object.defineProperty(..)` here

    Promise.prototype = PromisePrototype; // built-in "brand" to signal an "uninitialized" promise

    builtInProp(PromisePrototype, "__NPO__", 0,
    /*configurable=*/
    false);
    builtInProp(Promise, "resolve", function Promise$resolve(msg) {
      var Constructor = this; // spec mandated checks
      // note: best "isPromise" check that's practical for now

      if (msg && typeof msg == "object" && msg.__NPO__ === 1) {
        return msg;
      }

      return new Constructor(function executor(resolve, reject) {
        if (typeof resolve != "function" || typeof reject != "function") {
          throw TypeError("Not a function");
        }

        resolve(msg);
      });
    });
    builtInProp(Promise, "reject", function Promise$reject(msg) {
      return new this(function executor(resolve, reject) {
        if (typeof resolve != "function" || typeof reject != "function") {
          throw TypeError("Not a function");
        }

        reject(msg);
      });
    });
    builtInProp(Promise, "all", function Promise$all(arr) {
      var Constructor = this; // spec mandated checks

      if (ToString.call(arr) != "[object Array]") {
        return Constructor.reject(TypeError("Not an array"));
      }

      if (arr.length === 0) {
        return Constructor.resolve([]);
      }

      return new Constructor(function executor(resolve, reject) {
        if (typeof resolve != "function" || typeof reject != "function") {
          throw TypeError("Not a function");
        }

        var len = arr.length,
            msgs = Array(len),
            count = 0;
        iteratePromises(Constructor, arr, function resolver(idx, msg) {
          msgs[idx] = msg;

          if (++count === len) {
            resolve(msgs);
          }
        }, reject);
      });
    });
    builtInProp(Promise, "race", function Promise$race(arr) {
      var Constructor = this; // spec mandated checks

      if (ToString.call(arr) != "[object Array]") {
        return Constructor.reject(TypeError("Not an array"));
      }

      return new Constructor(function executor(resolve, reject) {
        if (typeof resolve != "function" || typeof reject != "function") {
          throw TypeError("Not a function");
        }

        iteratePromises(Constructor, arr, function resolver(idx, msg) {
          resolve(msg);
        }, reject);
      });
    });
    return Promise;
  });
  });

  /**
   * @module lib/callbacks
   */
  var callbackMap = new WeakMap();
  /**
   * Store a callback for a method or event for a player.
   *
   * @param {Player} player The player object.
   * @param {string} name The method or event name.
   * @param {(function(this:Player, *): void|{resolve: function, reject: function})} callback
   *        The callback to call or an object with resolve and reject functions for a promise.
   * @return {void}
   */

  function storeCallback(player, name, callback) {
    var playerCallbacks = callbackMap.get(player.element) || {};

    if (!(name in playerCallbacks)) {
      playerCallbacks[name] = [];
    }

    playerCallbacks[name].push(callback);
    callbackMap.set(player.element, playerCallbacks);
  }
  /**
   * Get the callbacks for a player and event or method.
   *
   * @param {Player} player The player object.
   * @param {string} name The method or event name
   * @return {function[]}
   */

  function getCallbacks(player, name) {
    var playerCallbacks = callbackMap.get(player.element) || {};
    return playerCallbacks[name] || [];
  }
  /**
   * Remove a stored callback for a method or event for a player.
   *
   * @param {Player} player The player object.
   * @param {string} name The method or event name
   * @param {function} [callback] The specific callback to remove.
   * @return {boolean} Was this the last callback?
   */

  function removeCallback(player, name, callback) {
    var playerCallbacks = callbackMap.get(player.element) || {};

    if (!playerCallbacks[name]) {
      return true;
    } // If no callback is passed, remove all callbacks for the event


    if (!callback) {
      playerCallbacks[name] = [];
      callbackMap.set(player.element, playerCallbacks);
      return true;
    }

    var index = playerCallbacks[name].indexOf(callback);

    if (index !== -1) {
      playerCallbacks[name].splice(index, 1);
    }

    callbackMap.set(player.element, playerCallbacks);
    return playerCallbacks[name] && playerCallbacks[name].length === 0;
  }
  /**
   * Return the first stored callback for a player and event or method.
   *
   * @param {Player} player The player object.
   * @param {string} name The method or event name.
   * @return {function} The callback, or false if there were none
   */

  function shiftCallbacks(player, name) {
    var playerCallbacks = getCallbacks(player, name);

    if (playerCallbacks.length < 1) {
      return false;
    }

    var callback = playerCallbacks.shift();
    removeCallback(player, name, callback);
    return callback;
  }
  /**
   * Move callbacks associated with an element to another element.
   *
   * @param {HTMLElement} oldElement The old element.
   * @param {HTMLElement} newElement The new element.
   * @return {void}
   */

  function swapCallbacks(oldElement, newElement) {
    var playerCallbacks = callbackMap.get(oldElement);
    callbackMap.set(newElement, playerCallbacks);
    callbackMap.delete(oldElement);
  }

  /**
   * @module lib/embed
   */
  var oEmbedParameters = ['autopause', 'autoplay', 'background', 'byline', 'color', 'controls', 'dnt', 'height', 'id', 'loop', 'maxheight', 'maxwidth', 'muted', 'playsinline', 'portrait', 'responsive', 'speed', 'texttrack', 'title', 'transparent', 'url', 'width'];
  /**
   * Get the 'data-vimeo'-prefixed attributes from an element as an object.
   *
   * @param {HTMLElement} element The element.
   * @param {Object} [defaults={}] The default values to use.
   * @return {Object<string, string>}
   */

  function getOEmbedParameters(element) {
    var defaults = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    return oEmbedParameters.reduce(function (params, param) {
      var value = element.getAttribute("data-vimeo-".concat(param));

      if (value || value === '') {
        params[param] = value === '' ? 1 : value;
      }

      return params;
    }, defaults);
  }
  /**
   * Create an embed from oEmbed data inside an element.
   *
   * @param {object} data The oEmbed data.
   * @param {HTMLElement} element The element to put the iframe in.
   * @return {HTMLIFrameElement} The iframe embed.
   */

  function createEmbed(_ref, element) {
    var html = _ref.html;

    if (!element) {
      throw new TypeError('An element must be provided');
    }

    if (element.getAttribute('data-vimeo-initialized') !== null) {
      return element.querySelector('iframe');
    }

    var div = document.createElement('div');
    div.innerHTML = html;
    element.appendChild(div.firstChild);
    element.setAttribute('data-vimeo-initialized', 'true');
    return element.querySelector('iframe');
  }
  /**
   * Make an oEmbed call for the specified URL.
   *
   * @param {string} videoUrl The vimeo.com url for the video.
   * @param {Object} [params] Parameters to pass to oEmbed.
   * @param {HTMLElement} element The element.
   * @return {Promise}
   */

  function getOEmbedData(videoUrl) {
    var params = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    var element = arguments.length > 2 ? arguments[2] : undefined;
    return new Promise(function (resolve, reject) {
      if (!isVimeoUrl(videoUrl)) {
        throw new TypeError("\u201C".concat(videoUrl, "\u201D is not a vimeo.com url."));
      }

      var url = "https://vimeo.com/api/oembed.json?url=".concat(encodeURIComponent(videoUrl));

      for (var param in params) {
        if (params.hasOwnProperty(param)) {
          url += "&".concat(param, "=").concat(encodeURIComponent(params[param]));
        }
      }

      var xhr = 'XDomainRequest' in window ? new XDomainRequest() : new XMLHttpRequest();
      xhr.open('GET', url, true);

      xhr.onload = function () {
        if (xhr.status === 404) {
          reject(new Error("\u201C".concat(videoUrl, "\u201D was not found.")));
          return;
        }

        if (xhr.status === 403) {
          reject(new Error("\u201C".concat(videoUrl, "\u201D is not embeddable.")));
          return;
        }

        try {
          var json = JSON.parse(xhr.responseText); // Check api response for 403 on oembed

          if (json.domain_status_code === 403) {
            // We still want to create the embed to give users visual feedback
            createEmbed(json, element);
            reject(new Error("\u201C".concat(videoUrl, "\u201D is not embeddable.")));
            return;
          }

          resolve(json);
        } catch (error) {
          reject(error);
        }
      };

      xhr.onerror = function () {
        var status = xhr.status ? " (".concat(xhr.status, ")") : '';
        reject(new Error("There was an error fetching the embed code from Vimeo".concat(status, ".")));
      };

      xhr.send();
    });
  }
  /**
   * Initialize all embeds within a specific element
   *
   * @param {HTMLElement} [parent=document] The parent element.
   * @return {void}
   */

  function initializeEmbeds() {
    var parent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;
    var elements = [].slice.call(parent.querySelectorAll('[data-vimeo-id], [data-vimeo-url]'));

    var handleError = function handleError(error) {
      if ('console' in window && console.error) {
        console.error("There was an error creating an embed: ".concat(error));
      }
    };

    elements.forEach(function (element) {
      try {
        // Skip any that have data-vimeo-defer
        if (element.getAttribute('data-vimeo-defer') !== null) {
          return;
        }

        var params = getOEmbedParameters(element);
        var url = getVimeoUrl(params);
        getOEmbedData(url, params, element).then(function (data) {
          return createEmbed(data, element);
        }).catch(handleError);
      } catch (error) {
        handleError(error);
      }
    });
  }
  /**
   * Resize embeds when messaged by the player.
   *
   * @param {HTMLElement} [parent=document] The parent element.
   * @return {void}
   */

  function resizeEmbeds() {
    var parent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;

    // Prevent execution if users include the player.js script multiple times.
    if (window.VimeoPlayerResizeEmbeds_) {
      return;
    }

    window.VimeoPlayerResizeEmbeds_ = true;

    var onMessage = function onMessage(event) {
      if (!isVimeoUrl(event.origin)) {
        return;
      } // 'spacechange' is fired only on embeds with cards


      if (!event.data || event.data.event !== 'spacechange') {
        return;
      }

      var iframes = parent.querySelectorAll('iframe');

      for (var i = 0; i < iframes.length; i++) {
        if (iframes[i].contentWindow !== event.source) {
          continue;
        } // Change padding-bottom of the enclosing div to accommodate
        // card carousel without distorting aspect ratio


        var space = iframes[i].parentElement;
        space.style.paddingBottom = "".concat(event.data.data[0].bottom, "px");
        break;
      }
    };

    if (window.addEventListener) {
      window.addEventListener('message', onMessage, false);
    } else if (window.attachEvent) {
      window.attachEvent('onmessage', onMessage);
    }
  }

  /**
   * @module lib/postmessage
   */
  /**
   * Parse a message received from postMessage.
   *
   * @param {*} data The data received from postMessage.
   * @return {object}
   */

  function parseMessageData(data) {
    if (typeof data === 'string') {
      try {
        data = JSON.parse(data);
      } catch (error) {
        // If the message cannot be parsed, throw the error as a warning
        console.warn(error);
        return {};
      }
    }

    return data;
  }
  /**
   * Post a message to the specified target.
   *
   * @param {Player} player The player object to use.
   * @param {string} method The API method to call.
   * @param {object} params The parameters to send to the player.
   * @return {void}
   */

  function postMessage(player, method, params) {
    if (!player.element.contentWindow || !player.element.contentWindow.postMessage) {
      return;
    }

    var message = {
      method: method
    };

    if (params !== undefined) {
      message.value = params;
    } // IE 8 and 9 do not support passing messages, so stringify them


    var ieVersion = parseFloat(navigator.userAgent.toLowerCase().replace(/^.*msie (\d+).*$/, '$1'));

    if (ieVersion >= 8 && ieVersion < 10) {
      message = JSON.stringify(message);
    }

    player.element.contentWindow.postMessage(message, player.origin);
  }
  /**
   * Parse the data received from a message event.
   *
   * @param {Player} player The player that received the message.
   * @param {(Object|string)} data The message data. Strings will be parsed into JSON.
   * @return {void}
   */

  function processData(player, data) {
    data = parseMessageData(data);
    var callbacks = [];
    var param;

    if (data.event) {
      if (data.event === 'error') {
        var promises = getCallbacks(player, data.data.method);
        promises.forEach(function (promise) {
          var error = new Error(data.data.message);
          error.name = data.data.name;
          promise.reject(error);
          removeCallback(player, data.data.method, promise);
        });
      }

      callbacks = getCallbacks(player, "event:".concat(data.event));
      param = data.data;
    } else if (data.method) {
      var callback = shiftCallbacks(player, data.method);

      if (callback) {
        callbacks.push(callback);
        param = data.value;
      }
    }

    callbacks.forEach(function (callback) {
      try {
        if (typeof callback === 'function') {
          callback.call(player, param);
          return;
        }

        callback.resolve(param);
      } catch (e) {// empty
      }
    });
  }

  var playerMap = new WeakMap();
  var readyMap = new WeakMap();

  var Player =
  /*#__PURE__*/
  function () {
    /**
     * Create a Player.
     *
     * @param {(HTMLIFrameElement|HTMLElement|string|jQuery)} element A reference to the Vimeo
     *        player iframe, and id, or a jQuery object.
     * @param {object} [options] oEmbed parameters to use when creating an embed in the element.
     * @return {Player}
     */
    function Player(element) {
      var _this = this;

      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

      _classCallCheck(this, Player);

      /* global jQuery */
      if (window.jQuery && element instanceof jQuery) {
        if (element.length > 1 && window.console && console.warn) {
          console.warn('A jQuery object with multiple elements was passed, using the first element.');
        }

        element = element[0];
      } // Find an element by ID


      if (typeof document !== 'undefined' && typeof element === 'string') {
        element = document.getElementById(element);
      } // Not an element!


      if (!isDomElement(element)) {
        throw new TypeError('You must pass either a valid element or a valid id.');
      }

      var win = element.ownerDocument.defaultView; // Already initialized an embed in this div, so grab the iframe

      if (element.nodeName !== 'IFRAME') {
        var iframe = element.querySelector('iframe');

        if (iframe) {
          element = iframe;
        }
      } // iframe url is not a Vimeo url


      if (element.nodeName === 'IFRAME' && !isVimeoUrl(element.getAttribute('src') || '')) {
        throw new Error('The player element passed isn’t a Vimeo embed.');
      } // If there is already a player object in the map, return that


      if (playerMap.has(element)) {
        return playerMap.get(element);
      }

      this.element = element;
      this.origin = '*';
      var readyPromise = new npo_src(function (resolve, reject) {
        var onMessage = function onMessage(event) {
          if (!isVimeoUrl(event.origin) || _this.element.contentWindow !== event.source) {
            return;
          }

          if (_this.origin === '*') {
            _this.origin = event.origin;
          }

          var data = parseMessageData(event.data);
          var isError = data && data.event === 'error';
          var isReadyError = isError && data.data && data.data.method === 'ready';

          if (isReadyError) {
            var error = new Error(data.data.message);
            error.name = data.data.name;
            reject(error);
            return;
          }

          var isReadyEvent = data && data.event === 'ready';
          var isPingResponse = data && data.method === 'ping';

          if (isReadyEvent || isPingResponse) {
            _this.element.setAttribute('data-ready', 'true');

            resolve();
            return;
          }

          processData(_this, data);
        };

        if (win.addEventListener) {
          win.addEventListener('message', onMessage, false);
        } else if (win.attachEvent) {
          win.attachEvent('onmessage', onMessage);
        }

        if (_this.element.nodeName !== 'IFRAME') {
          var params = getOEmbedParameters(element, options);
          var url = getVimeoUrl(params);
          getOEmbedData(url, params, element).then(function (data) {
            var iframe = createEmbed(data, element); // Overwrite element with the new iframe,
            // but store reference to the original element

            _this.element = iframe;
            _this._originalElement = element;
            swapCallbacks(element, iframe);
            playerMap.set(_this.element, _this);
            return data;
          }).catch(reject);
        }
      }); // Store a copy of this Player in the map

      readyMap.set(this, readyPromise);
      playerMap.set(this.element, this); // Send a ping to the iframe so the ready promise will be resolved if
      // the player is already ready.

      if (this.element.nodeName === 'IFRAME') {
        postMessage(this, 'ping');
      }

      return this;
    }
    /**
     * Get a promise for a method.
     *
     * @param {string} name The API method to call.
     * @param {Object} [args={}] Arguments to send via postMessage.
     * @return {Promise}
     */


    _createClass(Player, [{
      key: "callMethod",
      value: function callMethod(name) {
        var _this2 = this;

        var args = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
        return new npo_src(function (resolve, reject) {
          // We are storing the resolve/reject handlers to call later, so we
          // can’t return here.
          // eslint-disable-next-line promise/always-return
          return _this2.ready().then(function () {
            storeCallback(_this2, name, {
              resolve: resolve,
              reject: reject
            });
            postMessage(_this2, name, args);
          }).catch(reject);
        });
      }
      /**
       * Get a promise for the value of a player property.
       *
       * @param {string} name The property name
       * @return {Promise}
       */

    }, {
      key: "get",
      value: function get(name) {
        var _this3 = this;

        return new npo_src(function (resolve, reject) {
          name = getMethodName(name, 'get'); // We are storing the resolve/reject handlers to call later, so we
          // can’t return here.
          // eslint-disable-next-line promise/always-return

          return _this3.ready().then(function () {
            storeCallback(_this3, name, {
              resolve: resolve,
              reject: reject
            });
            postMessage(_this3, name);
          }).catch(reject);
        });
      }
      /**
       * Get a promise for setting the value of a player property.
       *
       * @param {string} name The API method to call.
       * @param {mixed} value The value to set.
       * @return {Promise}
       */

    }, {
      key: "set",
      value: function set(name, value) {
        var _this4 = this;

        return new npo_src(function (resolve, reject) {
          name = getMethodName(name, 'set');

          if (value === undefined || value === null) {
            throw new TypeError('There must be a value to set.');
          } // We are storing the resolve/reject handlers to call later, so we
          // can’t return here.
          // eslint-disable-next-line promise/always-return


          return _this4.ready().then(function () {
            storeCallback(_this4, name, {
              resolve: resolve,
              reject: reject
            });
            postMessage(_this4, name, value);
          }).catch(reject);
        });
      }
      /**
       * Add an event listener for the specified event. Will call the
       * callback with a single parameter, `data`, that contains the data for
       * that event.
       *
       * @param {string} eventName The name of the event.
       * @param {function(*)} callback The function to call when the event fires.
       * @return {void}
       */

    }, {
      key: "on",
      value: function on(eventName, callback) {
        if (!eventName) {
          throw new TypeError('You must pass an event name.');
        }

        if (!callback) {
          throw new TypeError('You must pass a callback function.');
        }

        if (typeof callback !== 'function') {
          throw new TypeError('The callback must be a function.');
        }

        var callbacks = getCallbacks(this, "event:".concat(eventName));

        if (callbacks.length === 0) {
          this.callMethod('addEventListener', eventName).catch(function () {// Ignore the error. There will be an error event fired that
            // will trigger the error callback if they are listening.
          });
        }

        storeCallback(this, "event:".concat(eventName), callback);
      }
      /**
       * Remove an event listener for the specified event. Will remove all
       * listeners for that event if a `callback` isn’t passed, or only that
       * specific callback if it is passed.
       *
       * @param {string} eventName The name of the event.
       * @param {function} [callback] The specific callback to remove.
       * @return {void}
       */

    }, {
      key: "off",
      value: function off(eventName, callback) {
        if (!eventName) {
          throw new TypeError('You must pass an event name.');
        }

        if (callback && typeof callback !== 'function') {
          throw new TypeError('The callback must be a function.');
        }

        var lastCallback = removeCallback(this, "event:".concat(eventName), callback); // If there are no callbacks left, remove the listener

        if (lastCallback) {
          this.callMethod('removeEventListener', eventName).catch(function (e) {// Ignore the error. There will be an error event fired that
            // will trigger the error callback if they are listening.
          });
        }
      }
      /**
       * A promise to load a new video.
       *
       * @promise LoadVideoPromise
       * @fulfill {number} The video with this id successfully loaded.
       * @reject {TypeError} The id was not a number.
       */

      /**
       * Load a new video into this embed. The promise will be resolved if
       * the video is successfully loaded, or it will be rejected if it could
       * not be loaded.
       *
       * @param {number|object} options The id of the video or an object with embed options.
       * @return {LoadVideoPromise}
       */

    }, {
      key: "loadVideo",
      value: function loadVideo(options) {
        return this.callMethod('loadVideo', options);
      }
      /**
       * A promise to perform an action when the Player is ready.
       *
       * @todo document errors
       * @promise LoadVideoPromise
       * @fulfill {void}
       */

      /**
       * Trigger a function when the player iframe has initialized. You do not
       * need to wait for `ready` to trigger to begin adding event listeners
       * or calling other methods.
       *
       * @return {ReadyPromise}
       */

    }, {
      key: "ready",
      value: function ready() {
        var readyPromise = readyMap.get(this) || new npo_src(function (resolve, reject) {
          reject(new Error('Unknown player. Probably unloaded.'));
        });
        return npo_src.resolve(readyPromise);
      }
      /**
       * A promise to add a cue point to the player.
       *
       * @promise AddCuePointPromise
       * @fulfill {string} The id of the cue point to use for removeCuePoint.
       * @reject {RangeError} the time was less than 0 or greater than the
       *         video’s duration.
       * @reject {UnsupportedError} Cue points are not supported with the current
       *         player or browser.
       */

      /**
       * Add a cue point to the player.
       *
       * @param {number} time The time for the cue point.
       * @param {object} [data] Arbitrary data to be returned with the cue point.
       * @return {AddCuePointPromise}
       */

    }, {
      key: "addCuePoint",
      value: function addCuePoint(time) {
        var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
        return this.callMethod('addCuePoint', {
          time: time,
          data: data
        });
      }
      /**
       * A promise to remove a cue point from the player.
       *
       * @promise AddCuePointPromise
       * @fulfill {string} The id of the cue point that was removed.
       * @reject {InvalidCuePoint} The cue point with the specified id was not
       *         found.
       * @reject {UnsupportedError} Cue points are not supported with the current
       *         player or browser.
       */

      /**
       * Remove a cue point from the video.
       *
       * @param {string} id The id of the cue point to remove.
       * @return {RemoveCuePointPromise}
       */

    }, {
      key: "removeCuePoint",
      value: function removeCuePoint(id) {
        return this.callMethod('removeCuePoint', id);
      }
      /**
       * A representation of a text track on a video.
       *
       * @typedef {Object} VimeoTextTrack
       * @property {string} language The ISO language code.
       * @property {string} kind The kind of track it is (captions or subtitles).
       * @property {string} label The human‐readable label for the track.
       */

      /**
       * A promise to enable a text track.
       *
       * @promise EnableTextTrackPromise
       * @fulfill {VimeoTextTrack} The text track that was enabled.
       * @reject {InvalidTrackLanguageError} No track was available with the
       *         specified language.
       * @reject {InvalidTrackError} No track was available with the specified
       *         language and kind.
       */

      /**
       * Enable the text track with the specified language, and optionally the
       * specified kind (captions or subtitles).
       *
       * When set via the API, the track language will not change the viewer’s
       * stored preference.
       *
       * @param {string} language The two‐letter language code.
       * @param {string} [kind] The kind of track to enable (captions or subtitles).
       * @return {EnableTextTrackPromise}
       */

    }, {
      key: "enableTextTrack",
      value: function enableTextTrack(language, kind) {
        if (!language) {
          throw new TypeError('You must pass a language.');
        }

        return this.callMethod('enableTextTrack', {
          language: language,
          kind: kind
        });
      }
      /**
       * A promise to disable the active text track.
       *
       * @promise DisableTextTrackPromise
       * @fulfill {void} The track was disabled.
       */

      /**
       * Disable the currently-active text track.
       *
       * @return {DisableTextTrackPromise}
       */

    }, {
      key: "disableTextTrack",
      value: function disableTextTrack() {
        return this.callMethod('disableTextTrack');
      }
      /**
       * A promise to pause the video.
       *
       * @promise PausePromise
       * @fulfill {void} The video was paused.
       */

      /**
       * Pause the video if it’s playing.
       *
       * @return {PausePromise}
       */

    }, {
      key: "pause",
      value: function pause() {
        return this.callMethod('pause');
      }
      /**
       * A promise to play the video.
       *
       * @promise PlayPromise
       * @fulfill {void} The video was played.
       */

      /**
       * Play the video if it’s paused. **Note:** on iOS and some other
       * mobile devices, you cannot programmatically trigger play. Once the
       * viewer has tapped on the play button in the player, however, you
       * will be able to use this function.
       *
       * @return {PlayPromise}
       */

    }, {
      key: "play",
      value: function play() {
        return this.callMethod('play');
      }
      /**
       * A promise to unload the video.
       *
       * @promise UnloadPromise
       * @fulfill {void} The video was unloaded.
       */

      /**
       * Return the player to its initial state.
       *
       * @return {UnloadPromise}
       */

    }, {
      key: "unload",
      value: function unload() {
        return this.callMethod('unload');
      }
      /**
       * Cleanup the player and remove it from the DOM
       *
       * It won't be usable and a new one should be constructed
       *  in order to do any operations.
       *
       * @return {Promise}
       */

    }, {
      key: "destroy",
      value: function destroy() {
        var _this5 = this;

        return new npo_src(function (resolve) {
          readyMap.delete(_this5);
          playerMap.delete(_this5.element);

          if (_this5._originalElement) {
            playerMap.delete(_this5._originalElement);

            _this5._originalElement.removeAttribute('data-vimeo-initialized');
          }

          if (_this5.element && _this5.element.nodeName === 'IFRAME' && _this5.element.parentNode) {
            _this5.element.parentNode.removeChild(_this5.element);
          }

          resolve();
        });
      }
      /**
       * A promise to get the autopause behavior of the video.
       *
       * @promise GetAutopausePromise
       * @fulfill {boolean} Whether autopause is turned on or off.
       * @reject {UnsupportedError} Autopause is not supported with the current
       *         player or browser.
       */

      /**
       * Get the autopause behavior for this player.
       *
       * @return {GetAutopausePromise}
       */

    }, {
      key: "getAutopause",
      value: function getAutopause() {
        return this.get('autopause');
      }
      /**
       * A promise to set the autopause behavior of the video.
       *
       * @promise SetAutopausePromise
       * @fulfill {boolean} Whether autopause is turned on or off.
       * @reject {UnsupportedError} Autopause is not supported with the current
       *         player or browser.
       */

      /**
       * Enable or disable the autopause behavior of this player.
       *
       * By default, when another video is played in the same browser, this
       * player will automatically pause. Unless you have a specific reason
       * for doing so, we recommend that you leave autopause set to the
       * default (`true`).
       *
       * @param {boolean} autopause
       * @return {SetAutopausePromise}
       */

    }, {
      key: "setAutopause",
      value: function setAutopause(autopause) {
        return this.set('autopause', autopause);
      }
      /**
       * A promise to get the buffered property of the video.
       *
       * @promise GetBufferedPromise
       * @fulfill {Array} Buffered Timeranges converted to an Array.
       */

      /**
       * Get the buffered property of the video.
       *
       * @return {GetBufferedPromise}
       */

    }, {
      key: "getBuffered",
      value: function getBuffered() {
        return this.get('buffered');
      }
      /**
       * A promise to get the color of the player.
       *
       * @promise GetColorPromise
       * @fulfill {string} The hex color of the player.
       */

      /**
       * Get the color for this player.
       *
       * @return {GetColorPromise}
       */

    }, {
      key: "getColor",
      value: function getColor() {
        return this.get('color');
      }
      /**
       * A promise to set the color of the player.
       *
       * @promise SetColorPromise
       * @fulfill {string} The color was successfully set.
       * @reject {TypeError} The string was not a valid hex or rgb color.
       * @reject {ContrastError} The color was set, but the contrast is
       *         outside of the acceptable range.
       * @reject {EmbedSettingsError} The owner of the player has chosen to
       *         use a specific color.
       */

      /**
       * Set the color of this player to a hex or rgb string. Setting the
       * color may fail if the owner of the video has set their embed
       * preferences to force a specific color.
       *
       * @param {string} color The hex or rgb color string to set.
       * @return {SetColorPromise}
       */

    }, {
      key: "setColor",
      value: function setColor(color) {
        return this.set('color', color);
      }
      /**
       * A representation of a cue point.
       *
       * @typedef {Object} VimeoCuePoint
       * @property {number} time The time of the cue point.
       * @property {object} data The data passed when adding the cue point.
       * @property {string} id The unique id for use with removeCuePoint.
       */

      /**
       * A promise to get the cue points of a video.
       *
       * @promise GetCuePointsPromise
       * @fulfill {VimeoCuePoint[]} The cue points added to the video.
       * @reject {UnsupportedError} Cue points are not supported with the current
       *         player or browser.
       */

      /**
       * Get an array of the cue points added to the video.
       *
       * @return {GetCuePointsPromise}
       */

    }, {
      key: "getCuePoints",
      value: function getCuePoints() {
        return this.get('cuePoints');
      }
      /**
       * A promise to get the current time of the video.
       *
       * @promise GetCurrentTimePromise
       * @fulfill {number} The current time in seconds.
       */

      /**
       * Get the current playback position in seconds.
       *
       * @return {GetCurrentTimePromise}
       */

    }, {
      key: "getCurrentTime",
      value: function getCurrentTime() {
        return this.get('currentTime');
      }
      /**
       * A promise to set the current time of the video.
       *
       * @promise SetCurrentTimePromise
       * @fulfill {number} The actual current time that was set.
       * @reject {RangeError} the time was less than 0 or greater than the
       *         video’s duration.
       */

      /**
       * Set the current playback position in seconds. If the player was
       * paused, it will remain paused. Likewise, if the player was playing,
       * it will resume playing once the video has buffered.
       *
       * You can provide an accurate time and the player will attempt to seek
       * to as close to that time as possible. The exact time will be the
       * fulfilled value of the promise.
       *
       * @param {number} currentTime
       * @return {SetCurrentTimePromise}
       */

    }, {
      key: "setCurrentTime",
      value: function setCurrentTime(currentTime) {
        return this.set('currentTime', currentTime);
      }
      /**
       * A promise to get the duration of the video.
       *
       * @promise GetDurationPromise
       * @fulfill {number} The duration in seconds.
       */

      /**
       * Get the duration of the video in seconds. It will be rounded to the
       * nearest second before playback begins, and to the nearest thousandth
       * of a second after playback begins.
       *
       * @return {GetDurationPromise}
       */

    }, {
      key: "getDuration",
      value: function getDuration() {
        return this.get('duration');
      }
      /**
       * A promise to get the ended state of the video.
       *
       * @promise GetEndedPromise
       * @fulfill {boolean} Whether or not the video has ended.
       */

      /**
       * Get the ended state of the video. The video has ended if
       * `currentTime === duration`.
       *
       * @return {GetEndedPromise}
       */

    }, {
      key: "getEnded",
      value: function getEnded() {
        return this.get('ended');
      }
      /**
       * A promise to get the loop state of the player.
       *
       * @promise GetLoopPromise
       * @fulfill {boolean} Whether or not the player is set to loop.
       */

      /**
       * Get the loop state of the player.
       *
       * @return {GetLoopPromise}
       */

    }, {
      key: "getLoop",
      value: function getLoop() {
        return this.get('loop');
      }
      /**
       * A promise to set the loop state of the player.
       *
       * @promise SetLoopPromise
       * @fulfill {boolean} The loop state that was set.
       */

      /**
       * Set the loop state of the player. When set to `true`, the player
       * will start over immediately once playback ends.
       *
       * @param {boolean} loop
       * @return {SetLoopPromise}
       */

    }, {
      key: "setLoop",
      value: function setLoop(loop) {
        return this.set('loop', loop);
      }
      /**
       * A promise to set the muted state of the player.
       *
       * @promise SetMutedPromise
       * @fulfill {boolean} The muted state that was set.
       */

      /**
       * Set the muted state of the player. When set to `true`, the player
       * volume will be muted.
       *
       * @param {boolean} muted
       * @return {SetMutedPromise}
       */

    }, {
      key: "setMuted",
      value: function setMuted(muted) {
        return this.set('muted', muted);
      }
      /**
       * A promise to get the muted state of the player.
       *
       * @promise GetMutedPromise
       * @fulfill {boolean} Whether or not the player is muted.
       */

      /**
       * Get the muted state of the player.
       *
       * @return {GetMutedPromise}
       */

    }, {
      key: "getMuted",
      value: function getMuted() {
        return this.get('muted');
      }
      /**
       * A promise to get the paused state of the player.
       *
       * @promise GetLoopPromise
       * @fulfill {boolean} Whether or not the video is paused.
       */

      /**
       * Get the paused state of the player.
       *
       * @return {GetLoopPromise}
       */

    }, {
      key: "getPaused",
      value: function getPaused() {
        return this.get('paused');
      }
      /**
       * A promise to get the playback rate of the player.
       *
       * @promise GetPlaybackRatePromise
       * @fulfill {number} The playback rate of the player on a scale from 0.5 to 2.
       */

      /**
       * Get the playback rate of the player on a scale from `0.5` to `2`.
       *
       * @return {GetPlaybackRatePromise}
       */

    }, {
      key: "getPlaybackRate",
      value: function getPlaybackRate() {
        return this.get('playbackRate');
      }
      /**
       * A promise to set the playbackrate of the player.
       *
       * @promise SetPlaybackRatePromise
       * @fulfill {number} The playback rate was set.
       * @reject {RangeError} The playback rate was less than 0.5 or greater than 2.
       */

      /**
       * Set the playback rate of the player on a scale from `0.5` to `2`. When set
       * via the API, the playback rate will not be synchronized to other
       * players or stored as the viewer's preference.
       *
       * @param {number} playbackRate
       * @return {SetPlaybackRatePromise}
       */

    }, {
      key: "setPlaybackRate",
      value: function setPlaybackRate(playbackRate) {
        return this.set('playbackRate', playbackRate);
      }
      /**
       * A promise to get the played property of the video.
       *
       * @promise GetPlayedPromise
       * @fulfill {Array} Played Timeranges converted to an Array.
       */

      /**
       * Get the played property of the video.
       *
       * @return {GetPlayedPromise}
       */

    }, {
      key: "getPlayed",
      value: function getPlayed() {
        return this.get('played');
      }
      /**
       * A promise to get the seekable property of the video.
       *
       * @promise GetSeekablePromise
       * @fulfill {Array} Seekable Timeranges converted to an Array.
       */

      /**
       * Get the seekable property of the video.
       *
       * @return {GetSeekablePromise}
       */

    }, {
      key: "getSeekable",
      value: function getSeekable() {
        return this.get('seekable');
      }
      /**
       * A promise to get the seeking property of the player.
       *
       * @promise GetSeekingPromise
       * @fulfill {boolean} Whether or not the player is currently seeking.
       */

      /**
       * Get if the player is currently seeking.
       *
       * @return {GetSeekingPromise}
       */

    }, {
      key: "getSeeking",
      value: function getSeeking() {
        return this.get('seeking');
      }
      /**
       * A promise to get the text tracks of a video.
       *
       * @promise GetTextTracksPromise
       * @fulfill {VimeoTextTrack[]} The text tracks associated with the video.
       */

      /**
       * Get an array of the text tracks that exist for the video.
       *
       * @return {GetTextTracksPromise}
       */

    }, {
      key: "getTextTracks",
      value: function getTextTracks() {
        return this.get('textTracks');
      }
      /**
       * A promise to get the embed code for the video.
       *
       * @promise GetVideoEmbedCodePromise
       * @fulfill {string} The `<iframe>` embed code for the video.
       */

      /**
       * Get the `<iframe>` embed code for the video.
       *
       * @return {GetVideoEmbedCodePromise}
       */

    }, {
      key: "getVideoEmbedCode",
      value: function getVideoEmbedCode() {
        return this.get('videoEmbedCode');
      }
      /**
       * A promise to get the id of the video.
       *
       * @promise GetVideoIdPromise
       * @fulfill {number} The id of the video.
       */

      /**
       * Get the id of the video.
       *
       * @return {GetVideoIdPromise}
       */

    }, {
      key: "getVideoId",
      value: function getVideoId() {
        return this.get('videoId');
      }
      /**
       * A promise to get the title of the video.
       *
       * @promise GetVideoTitlePromise
       * @fulfill {number} The title of the video.
       */

      /**
       * Get the title of the video.
       *
       * @return {GetVideoTitlePromise}
       */

    }, {
      key: "getVideoTitle",
      value: function getVideoTitle() {
        return this.get('videoTitle');
      }
      /**
       * A promise to get the native width of the video.
       *
       * @promise GetVideoWidthPromise
       * @fulfill {number} The native width of the video.
       */

      /**
       * Get the native width of the currently‐playing video. The width of
       * the highest‐resolution available will be used before playback begins.
       *
       * @return {GetVideoWidthPromise}
       */

    }, {
      key: "getVideoWidth",
      value: function getVideoWidth() {
        return this.get('videoWidth');
      }
      /**
       * A promise to get the native height of the video.
       *
       * @promise GetVideoHeightPromise
       * @fulfill {number} The native height of the video.
       */

      /**
       * Get the native height of the currently‐playing video. The height of
       * the highest‐resolution available will be used before playback begins.
       *
       * @return {GetVideoHeightPromise}
       */

    }, {
      key: "getVideoHeight",
      value: function getVideoHeight() {
        return this.get('videoHeight');
      }
      /**
       * A promise to get the vimeo.com url for the video.
       *
       * @promise GetVideoUrlPromise
       * @fulfill {number} The vimeo.com url for the video.
       * @reject {PrivacyError} The url isn’t available because of the video’s privacy setting.
       */

      /**
       * Get the vimeo.com url for the video.
       *
       * @return {GetVideoUrlPromise}
       */

    }, {
      key: "getVideoUrl",
      value: function getVideoUrl() {
        return this.get('videoUrl');
      }
      /**
       * A promise to get the volume level of the player.
       *
       * @promise GetVolumePromise
       * @fulfill {number} The volume level of the player on a scale from 0 to 1.
       */

      /**
       * Get the current volume level of the player on a scale from `0` to `1`.
       *
       * Most mobile devices do not support an independent volume from the
       * system volume. In those cases, this method will always return `1`.
       *
       * @return {GetVolumePromise}
       */

    }, {
      key: "getVolume",
      value: function getVolume() {
        return this.get('volume');
      }
      /**
       * A promise to set the volume level of the player.
       *
       * @promise SetVolumePromise
       * @fulfill {number} The volume was set.
       * @reject {RangeError} The volume was less than 0 or greater than 1.
       */

      /**
       * Set the volume of the player on a scale from `0` to `1`. When set
       * via the API, the volume level will not be synchronized to other
       * players or stored as the viewer’s preference.
       *
       * Most mobile devices do not support setting the volume. An error will
       * *not* be triggered in that situation.
       *
       * @param {number} volume
       * @return {SetVolumePromise}
       */

    }, {
      key: "setVolume",
      value: function setVolume(volume) {
        return this.set('volume', volume);
      }
    }]);

    return Player;
  }(); // Setup embed only if this is not a node environment


  if (!isNode) {
    initializeEmbeds();
    resizeEmbeds();
  }

  return Player;

}));
