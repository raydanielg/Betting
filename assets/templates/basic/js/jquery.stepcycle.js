
/* jQuery StepCycle v1.5 */ ;
(function($, window, document, undefined) {
	'use strict';

	// Create the defaults once
	var pluginName = 'stepCycle';
	var defaults = {
		autoAdvance: true,
		transitionTime: 1.5,
		displayTime: 5,
		transition: 'zoom',
		easing: 'linear',
		navDotClass: 'navDot',
		navContainerSelector: '.navDots',
		navSelectedClass: 'selected',
		navItemTemplate: '<a class="navDot" href="#">&nbsp;</a>',
		prevButton: '.cycle_prev',
		nextButton: '.cycle_next',
		childSelector: '.banner_slide',
		ie8CheckSelector: '.ltie9',
		randomize: false,
		showNav: true
	};
	var transitions = {
		fade: crossFade,
		zoom: zoomFade,
		slidePush: slidePush,
		slideOver: slideOver
	};

	// The actual plugin constructor
	function Plugin(element, options) {
		this.element = element;

		this.options = $.extend({}, defaults, options);

		this._defaults = defaults;
		this._name = pluginName;

		this.init();
	}

	Plugin.prototype = {
		init: init,
		startTimer: startTimer,
		setupNav: setupNav,
		transitionSlide: transitionSlide,
		crossFade: crossFade,
		zoomFade: zoomFade,
		slidePush: slidePush,
		slideOver: slideOver
	};

	function init() {

		// Properties
		this.timeout = 0;
		this.navDotClass = this.options.navDotClass;
		this.activeSlide = 0;
		this.transition = transitions[this.options.transition] || crossFade;

		if (isIE8(this.options.ie8CheckSelector)) {
			this.transition = transitions.fade;
		}

		// Events
		$(this.options.prevButton).click($.proxy(clickPrev, this));
		$(this.options.nextButton).click($.proxy(clickNext, this));

		// Captures slides
		if (!this.options.childSelector) {
			this.slides = $(this.element).children();
		} else {
			this.slides = $(this.element).find(this.options.childSelector);
		}

		// Initialize the slider
		if (this.slides.length > 1) {

			if (this.options.randomize) {shuffleArray(this.slides);}

			// hide inner element
			if (isIE8(this.options.ie8CheckSelector)) {
				this.slides.find('.banner_image, .banner_overlay').hide();
			}

			// hide elements
			this.slides.hide().first().show();

			if (isIE8(this.options.ie8CheckSelector)) {
				this.slides.first().find('.banner_image, .banner_overlay').show();
			}

			this.startTimer();

			if (this.options.showNav) {
				setupNav(this, this.options);
			} else {
				$(this.options.navContainerSelector).hide();
			}
		}
	}

	function startTimer() {

		var plugin = this;

		if (!plugin.options.autoAdvance) {
			return;
		}

		plugin.timeout = setTimeout(function() {
			plugin.transitionSlide();
		}, plugin.options.displayTime * 1000);
	}

	// setup nav elements
	function setupNav(rotator, options) {

		var $navContainer = $(options.navContainerSelector);

		$navContainer.empty();
		rotator.slides.each(function(index, element) {
			$navContainer.append(options.navItemTemplate);
		});

		rotator.navDots = $navContainer.find('.' + rotator.navDotClass);
		rotator.navDots.removeClass(options.navSelectedClass);
		$(rotator.navDots[rotator.activeSlide]).addClass(options.navSelectedClass);

		rotator.navDots.find('a').click(function(e) {

		var nextSlide = rotator.navDots.find('a').index(this);
		rotator.transitionSlide(nextSlide);

			e.preventDefault();
		});
	}

	// transition to the next slide
	function transitionSlide(nextSlide) {

		if (nextSlide === this.activeSlide) {
			return;
		}

		clearTimeout(this.timeout);
		this.slides.stop(true);

		if (nextSlide === undefined) {
			nextSlide = this.activeSlide + 1;
		}

		if (nextSlide >= this.slides.length || nextSlide < 0) {
			nextSlide = 0;
		}

		this.transition(nextSlide);
		this.activeSlide = nextSlide;

		if (this.options.showNav) {
			this.navDots.removeClass(this.options.navSelectedClass);
			$(this.navDots[this.activeSlide]).addClass(this.options.navSelectedClass);
		}
	}

	// crossfade animation
	function crossFade(nextSlide) {

		var plugin = this;
		var $currentSlide = $(this.slides[this.activeSlide]);
		var $nextSlide = $(this.slides[nextSlide]);

		if (typeof plugin.options.transitionBegin === 'function') {
			plugin.options.transitionBegin($currentSlide, $nextSlide);
		}

		if (isIE8(plugin.options.ie8CheckSelector)) {
			$currentSlide
				.fadeOut(plugin.options.transitionTime * 1000, function() {
					$currentSlide.hide();
				})
				.find('.banner_image, .banner_overlay')
				.fadeOut(plugin.options.transitionTime * 1000);
			$nextSlide
				.show()
				.fadeIn(plugin.options.transitionTime * 1000, function() {
					if (typeof plugin.options.transitionComplete === 'function') {
						plugin.options.transitionComplete($currentSlide, $nextSlide);
					}
				})
				.find('.banner_image, .banner_overlay')
				.fadeIn(plugin.options.transitionTime * 1000, function() {
					plugin.startTimer();
				});

			this.slides.not($currentSlide).not($nextSlide).hide();

		} else {
			$currentSlide.animate({
				opacity: 0
			}, plugin.options.transitionTime * 1000, plugin.options.easing, function() {
				$currentSlide.hide();
			});
			$nextSlide.css('opacity', 0).show().animate({
				opacity: 1
			}, plugin.options.transitionTime * 1000, plugin.options.easing, function() {
				plugin.startTimer();
				if (typeof plugin.options.transitionComplete === 'function') {
					plugin.options.transitionComplete($currentSlide, $nextSlide);
				}
			});

			this.slides.not($currentSlide).not($nextSlide).css('opacity', 0);
		}
	}

	// zoomfade animation
	function zoomFade(nextSlide) {

		var plugin = this;
		var $currentSlide = $(this.slides[this.activeSlide]);
		var $nextSlide = $(this.slides[nextSlide]);

		var aniLeft = $currentSlide.width() * -0.125;
		var aniTop = $nextSlide.height() * -0.125;

		if (typeof plugin.options.transitionBegin === 'function') {
			plugin.options.transitionBegin($currentSlide, $nextSlide);
		}

		$currentSlide.animate({
			width: '125%',
			height: '125%',
			opacity: 0,
			left: aniLeft,
			top: aniTop
		}, plugin.options.transitionTime * 1000 / 2, function() {
			$currentSlide.hide();
		});
		$nextSlide.hide().css({
			left: 0,
			top: 0,
			width: '100%',
			height: '100%',
			opacity: 0
		}).show().animate({
			opacity: 1
		}, plugin.options.transitionTime * 1000, function() {
			plugin.startTimer();
			if (typeof plugin.options.transitionComplete === 'function') {
				plugin.options.transitionComplete($currentSlide, $nextSlide);
			}
		});

		this.slides.not($currentSlide).not($nextSlide).hide();

	}

	// slidepush animation
	function slidePush(nextSlide) {

		var plugin = this;
		var $currentSlide = $(this.slides[this.activeSlide]);
		var $nextSlide = $(this.slides[nextSlide]);

		var imageWidth = $currentSlide.width();
		plugin.slides.css('z-index', 100);

		if (typeof plugin.options.transitionBegin === 'function') {
			plugin.options.transitionBegin($currentSlide, $nextSlide);
		}

		if ((nextSlide > plugin.activeSlide) || (plugin.activeSlide === plugin.slides.length - 1 && nextSlide === 0)) {
			$currentSlide.css({
				'z-index': 999
			}).animate({
				left: -1 * imageWidth
			}, plugin.options.transitionTime * 1000, plugin.options.easing, function() {
				$currentSlide.hide();
			});
			$nextSlide.css({
				left: imageWidth,
				'z-index': 1000
			}).show().animate({
				left: 0
			}, plugin.options.transitionTime * 1000, plugin.options.easing, function() {
				plugin.startTimer();
				if (typeof plugin.options.transitionComplete === 'function') {
					plugin.options.transitionComplete($currentSlide, $nextSlide);
				}
			});

			this.slides.not($currentSlide).not($nextSlide).css('left', imageWidth);

		} else {
			$currentSlide.css({
				'z-index': 999
			}).animate({
				left: imageWidth
			}, plugin.options.transitionTime * 1000, plugin.options.easing, function() {
				$currentSlide.hide();
			});
			$nextSlide.css({
				left: -1 * imageWidth,
				'z-index': 1000
			}).show().animate({
				left: 0
			}, plugin.options.transitionTime * 1000, plugin.options.easing, function() {
				plugin.startTimer();
				if (typeof plugin.options.transitionComplete === 'function') {
					plugin.options.transitionComplete($currentSlide, $nextSlide);
				}
			});

			this.slides.not($currentSlide).not($nextSlide).css('left', -1 * imageWidth);

		}


	}

	// slideover animation
	function slideOver(nextSlide) {

		var plugin = this;
		var $currentSlide = $(this.slides[this.activeSlide]);
		var $nextSlide = $(this.slides[nextSlide]);

		var imageWidth = $currentSlide.width();
		plugin.slides.css('z-index', 100);
		$currentSlide.css({
			'z-index': 999
		});

		if (typeof plugin.options.transitionBegin === 'function') {
			plugin.options.transitionBegin($currentSlide, $nextSlide);
		}

		if ((nextSlide > plugin.activeSlide) || (plugin.activeSlide === plugin.slides.length - 1 && nextSlide === 0)) {
			$nextSlide.css({
				left: imageWidth,
				'z-index': 1000
			}).show().animate({
				left: 0
			}, plugin.options.transitionTime * 1000, plugin.options.easing, function() {
				plugin.startTimer();
				if (typeof plugin.options.transitionComplete === 'function') {
					plugin.options.transitionComplete($currentSlide, $nextSlide);
				}
			});

			this.slides.not($currentSlide).not($nextSlide).css('left', imageWidth);

		} else {
			$nextSlide.css({
				left: -imageWidth,
				'z-index': 1000
			}).show().animate({
				left: 0
			}, plugin.options.transitionTime * 1000, plugin.options.easing, function() {
				plugin.startTimer();
				if (typeof plugin.options.transitionComplete === 'function') {
					plugin.options.transitionComplete($currentSlide, $nextSlide);
				}
			});

			this.slides.not($currentSlide).not($nextSlide).css('left', -1 * imageWidth);

		}
	}

	function isIE8(ie8CheckSelector) {
		return ($(ie8CheckSelector).length > 0);
	}

	//=====================================================
	// EVENTS
	//=====================================================
	function clickPrev(e) {

		var nextSlide = this.activeSlide - 1;

		e.preventDefault();

		if (nextSlide < 0) {
			nextSlide = this.slides.length - 1;
		}

		this.transitionSlide(nextSlide);
	}

	function clickNext(e) {

		var nextSlide = this.activeSlide + 1;

		e.preventDefault();

		if (nextSlide >= this.slides.length) {
			nextSlide = 0;
		}

		this.transitionSlide(nextSlide);
	}


	//=====================================================
	// Helper functions
	//=====================================================

	function shuffleArray(array) {
		/**
		 * Randomize array element order in-place.
		 * Using Durstenfeld shuffle algorithm.
		 */
	    for (var i = array.length - 1; i > 0; i--) {
	        var j = Math.floor(Math.random() * (i + 1));
	        var temp = array[i];
	        array[i] = array[j];
	        array[j] = temp;
	    }
	    return array;
	}


	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function(options) {
		return this.each(function() {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
					new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);
