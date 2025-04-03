/**
 * Child theme scripts.
 */

var urls = {};
var domain = window.location.protocol + "//" + window.location.host;
var domainPath = window.location.protocol + "//" + window.location.host + "//" + window.location.pathname;

jQuery(function($) {

	hawp = [];
	hawp.lastScrollPos = $(window).scrollTop();

	/**
	 * Browser js check.
	 */
	hawp.browserJsCheck = function() {
		$('html').addClass('js').removeClass('nojs');
	};

	/**
	 * JS On/Off.
	 */
	function jsOff(target) {
		$('.js-turnoff[data-target="'+target+'"],.js-turnon[data-target="'+target+'"]').removeClass('js-on');
		$(target).trigger('turnoff', [target]).removeClass('js-on');
	};
	function jsOn(target) {
		$(target).trigger('turnon', [target]).addClass('js-on');
		$('.js-turnon[data-target="'+target+'"],.js-turnoff[data-target="'+target+'"]').addClass('js-on');
	};

	/**
	 * Menu.
	 */
	hawp.addMenu = function() {
		// Dropdown menu.
		$(".menu").on("click", ".menu-item-has-children > a", function(e) {
			e.preventDefault(); // remove this to make the parent pages go to their url on second click.
			var li = $(this).parent("li");

			if (li.hasClass('js-on')) {
				li.find('.js-on').removeClass('js-active');
				li.removeClass('js-on');
				$(this).attr("aria-expanded", "false");
			} else {
				e.preventDefault(); // prevents parent items from being clickable
				li.siblings('.js-on').find('.js-on').removeClass('js-on');
				li.siblings('.js-on').removeClass('js-on');
				li.addClass('js-on');
				$(this).attr("aria-expanded", "true");
			}
		})

		$(document).on("click", ".js-turnon,.js-turnoff", function(e) {
			e.preventDefault();
			var target = $(this).attr("data-target");

			$(target).hasClass("js-on") ? jsOff(target) : jsOn(target);
		})

		// Deactivate menu.
		$(document).on("click", function(e) {
			$(".menu").each(function() {
				if (!$(this).is(e.target) && $(this).has(e.target).length === 0) {
					$(this).find('.js-on').removeClass('js-on');
				}
			});
			$(".js-on").each(function() {
				var target = $(this).attr("data-target");

				if (!target || $(target).attr('data-autoclose')=='0') {
					return;
				}
				if (!$(target).is(e.target) && $(target).has(e.target).length === 0 && !$('.js-turnon[data-target="'+target+'"]').is(e.target) && $('.js-turnon[data-target="'+target+'"]').has(e.target).length === 0) {
					jsOff(target);
				}
			});
		})

		// If escape is clicked
		$("body").keyup(function(e) {
			if (e.keyCode == 27) {
				$(document.activeElement).closest('.js-on').find("> a").attr("aria-expanded", "false").focus();
				$('.js-on').removeClass('js-on').trigger('turnoff');
			}
		})

		// Add parent menu item attributes
		$('.menu-item-has-children a').attr({"role":"menuitem", "aria-haspopup":"true","aria-expanded":"false"});

		// Move menu search to correct location
		if (window.matchMedia('(min-width: 68.5em)').matches) {
			$('.menu-search-form .search-field').attr('autocomplete', 'off');
			$('.menu-search-form').appendTo('.menu-search');
			$('.menu-search').addClass('menu-item-has-children').on('click', function() {
				if (!$(this).hasClass('js-on')) {
					$('.menu-search-form .search-field').focus();
				}
			});
		}
	};

	/**
	 * Add image title from alt tag content dynamically.
	 */
	hawp.addImageTitle = function() {
		$("img").attr("title", function() {
			return $(this).attr("alt");
		});
	};

	/**
	 * Add lity lightbox to wordpress button.
	 */
	hawp.addLityToButton = function() {
		$(".button-lity .wp-block-button__link").each(function() {
			$($(this).attr("data-lity","")).addClass("lity-button");
		});
	};

	/**
	 * Add lity lightbox to wordpress gallery.
	 */
	hawp.addLityToGallery = function() {
		$(".wp-block-gallery .blocks-gallery-item img").each(function() {
			$($(this).attr("data-lity", "")).addClass("lity-img");
		});
	};

	/**
	 * Tab scripts.
	 */
	hawp.addTabs = function() {
		var mobile = window.matchMedia("(min-width: 48.5em)");
		mobile.matches && $(".tabs .tab-link:first-child").addClass("current").next(".tab-content").addClass("current");
		$(".tabs .tab-link").click(function() {
			var data_attr = $(this).attr("data-tab"),
				check = $(this).hasClass("current");
			mobile.matches ? ($(".tabs .tab-link").removeClass("current"), $(".tab-content").removeClass("current"), $(this).addClass("current"), $("#" + data_attr).addClass("current")) : check ? ($(this).removeClass("current"), $("#" + data_attr).removeClass("current")) : ($(this).addClass("current"), $("#" + data_attr).addClass("current"));
		});
	};

	/**
	 * Add sticky header class.
	 */
	hawp.stickyHeader = function() {
		var mobile = window.matchMedia("(min-width: 48.5em)");

		if (mobile.matches) {

			const menu = document.querySelector('#header');
			const sentry = document.querySelector('.sentry');

			var observer2 = new IntersectionObserver(function(entries) {
				if (entries[0].intersectionRatio === 0) // no intersection with screen
					menu.classList.add("sticky-active");
				else if (entries[0].intersectionRatio === 1) // fully intersects with screen
					menu.classList.remove("sticky-active");
			}, {
				threshold: [0, 1]
			});

			observer2.observe(sentry);
		}
	};

	/**
	 * Gravity form material design.
	 */
	hawp.materialDesignGform = function() {
		$('.gfield label').addClass('gfield_float_label');
		$(".gfield input, .gfield textarea, .gfield select").focusin(function() {
			var wrapper = $(this).parents('.gfield');
			$(this).parent().addClass('gfield_in_focus');
			$(wrapper).addClass('gfield_is_set');
		});
		$(".gfield input, .gfield textarea, .gfield select").focusout(function() {
			if ($(this).val() == "") {
				var wrapper = $(this).parents('.gfield');
				$(this).parent().removeClass('gfield_in_focus');
				$(wrapper).removeClass('gfield_is_set');
			} else {
				$(this).parent().removeClass('gfield_in_focus');
			}
		});
	};

	/**
	 * Add warning for 3rd party links.
	 */
	hawp.targetBlankWarning = function() {
		$('a[target="_blank"]').click(function(event) {
			event.preventDefault();
			var yesno = confirm("This link opens a new window. Are you sure you want to continue?");
			if (yesno) window.open($(this).attr('href'));
		});
	};

	/**
	 * Animate wp block columns.
	 */
	hawp.animateContent = function() {
		$('.wp-block-group.animate-content').each(function() {
			if ($(window).scrollTop() + $(window).innerHeight() > $(this).offset().top + ($(window).innerHeight()/4)) {
				$(this).addClass('animated');
			}
		});
	};

	hawp.owlAnimations = function() {
		$(".work-items").owlCarousel({
			loop:true,
			margin:0,
			nav:true,
			navText: ['<span class="owl-prev-inside">Prev</span>', '<span class="owl-next-inside">Next</span>'],
			mouseDrag: false,
			dots: false,
			autoplay:false,
			animateIn: 'fadeIn',
			animateOut: 'fadeOut',
			autoplaySpeed: 500,
			autoplayTimeout:8000,
			autoplayHoverPause:false,
			responsive:{
				0:{
					items:1
				},
				600:{
					items:1
				},
				1000:{
					items:1
				},
			}
		})
	};

	hawp.headerAnimationDown = function() {
		var scrollPos = $(window).scrollTop();
		if($(window).width() > 981){
			if(scrollPos > 142){
				if (scrollPos >= hawp.lastScrollPos){
					$('html').removeClass('scrolled-up').addClass('scrolled-down');
				} else {
					$('html').removeClass('scrolled-down').addClass('scrolled-up');
				}
			} else {
				$('html').removeClass('scrolled-up scrolled-down');
			}
		} else {
			$('html').removeClass('scrolled-up scrolled-down');
		}
		hawp.lastScrollPos = scrollPos;
	}

	// Place items in here to have them run when the document is loaded
	$(document).ready(function() {
		hawp.browserJsCheck();
		hawp.addMenu();
		hawp.addImageTitle();
		hawp.addLityToButton();
		hawp.addLityToGallery();
		hawp.addTabs();
		hawp.materialDesignGform();
		//hawp.targetBlankWarning();
		hawp.animateContent();
		//hawp.owlAnimations();
		//hawp.stickyHeader();
		//hawp.headerAnimationDown();
	});

	// Place items in here to have them run when the window is scrolled
	$(window).scroll(function() {
		hawp.animateContent();
		//hawp.headerAnimationDown();
	});

});
