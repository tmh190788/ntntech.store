/* global xts_settings */
(function($) {
	XTSThemeModule.xtsElementorAddAction('frontend/element_ready/xts_animated_text.default', function() {
		XTSThemeModule.animatedTextElement();
	});

	XTSThemeModule.animatedTextElement = function() {
		$('.xts-anim-text').each(function() {
			var $element = $(this);
			var $animatedTextList = $element.find('.xts-anim-text-list');
			var $animatedTextWords = $animatedTextList.find('.xts-anim-text-item');
			var effect = $animatedTextList.data('effect');

			var animationDelay = $element.data('interval-time');
			// Typing effect
			var typeLettersDelay = $element.data('character-time');
			var selectionDuration = 500;
			var typeAnimationDelay = selectionDuration + 800;
			// Word effect
			var revealDuration = $element.data('animation-time');

			if ($animatedTextList.hasClass('xts-inited')) {
				return;
			}

			trimWords();
			runAnimation();

			function trimWords() {
				if ('typing' !== effect) {
					return;
				}

				$animatedTextWords.each(function() {
					var $word = $(this);
					var letters = $word.text().trim().split('');

					for (var index = 0; index < letters.length; index++) {
						var letterClasses = '';

						if (0 === $word.index()) {
							letterClasses = 'xts-in';
						}

						letters[index] = '<span class="' + letterClasses + '">' + letters[index] + '</span>';
					}

					$word.html(letters.join(''));
				});
			}

			function runAnimation() {
				if ('word' === effect) {
					$animatedTextList.width($animatedTextList.width() + 3);
				} else if ('typing' !== effect) {
					var width = 0;

					$animatedTextWords.each(function() {
						var wordWidth = $(this).width();

						if (wordWidth > width) {
							width = wordWidth;
						}
					});

					$animatedTextList.css('width', width);
				}

				setTimeout(function() {
					hideWord($animatedTextWords.eq(0));
				}, animationDelay);

				$animatedTextList.addClass('xts-inited');
			}

			function hideWord($word) {
				var nextWord = getNextWord($word);

				if ('typing' === effect) {
					$animatedTextList.addClass('xts-selected');

					setTimeout(function() {
						$animatedTextList.removeClass('xts-selected');
						$word.addClass('xts-hidden').removeClass('xts-active').children('span').removeClass('xts-in');
					}, selectionDuration);

					setTimeout(function() {
						showWord(nextWord, typeLettersDelay);
					}, typeAnimationDelay);
				} else if ('word' === effect) {
					$animatedTextList.animate({width: '2px'}, revealDuration, function() {
						switchWord($word, nextWord);
						showWord(nextWord);
					});
				}
			}

			function showLetter($letter, $word, bool, duration) {
				$letter.addClass('xts-in');

				if (!$letter.is(':last-child')) {
					setTimeout(function() {
						showLetter($letter.next(), $word, bool, duration);
					}, duration);
				} else if (!bool) {
					setTimeout(function() {
						hideWord($word);
					}, animationDelay);
				}
			}

			function showWord($word, $duration) {
				if ('typing' === effect) {
					showLetter($word.find('span').eq(0), $word, false, $duration);

					$word.addClass('xts-active').removeClass('xts-hidden');
				} else if ('word' === effect) {
					$animatedTextList.animate({width: $word.width() + 3}, revealDuration, function() {
						setTimeout(function() {
							hideWord($word);
						}, animationDelay);
					});
				}
			}

			function getNextWord($word) {
				return $word.is(':last-child') ? $word.parent().children().eq(0) : $word.next();
			}

			function switchWord($oldWord, $newWord) {
				$oldWord.removeClass('xts-active').addClass('xts-hidden');
				$newWord.removeClass('xts-hidden').addClass('xts-active');
			}
		});
	};

	$(document).ready(function() {
		XTSThemeModule.animatedTextElement();
	});
})(jQuery);