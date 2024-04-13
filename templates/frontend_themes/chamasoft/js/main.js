 AOS.init({
 	duration: 800,
 	easing: 'slide'
 });


(function($) {

	"use strict";

	var isMobile = {
		Android: function() {
			return navigator.userAgent.match(/Android/i);
		},
			BlackBerry: function() {
			return navigator.userAgent.match(/BlackBerry/i);
		},
			iOS: function() {
			return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		},
			Opera: function() {
			return navigator.userAgent.match(/Opera Mini/i);
		},
			Windows: function() {
			return navigator.userAgent.match(/IEMobile/i);
		},
			any: function() {
			return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
		}
	};

	$('nav .dropdown').hover(function(){
		var $this = $(this);
		//if($this.find('.show')){console.log('.show exists')}
		var timer;
		clearTimeout(timer);
		//$this.addClass('show');
		//$this.find('> a').attr('aria-expanded', true);
		$this.find('.dropdown-menu').addClass('animated-fast fadeInUp show');
		//$(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
		//$this.find('.dropdown-menu').addClass('show');
	}, function(){
		var $this = $(this);
		var timer;
		timer = setTimeout(function(){
			//$this.removeClass('show');
			//$this.find('> a').attr('aria-expanded', false);
			$this.find('.dropdown-menu').removeClass('animated-fast fadeInUp show');
			//$(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
			//$this.find('.dropdown-menu').removeClass('show');
		}, 100);
	});


	$(window).stellar({
    responsive: true,
    parallaxBackgrounds: true,
    parallaxElements: true,
    horizontalScrolling: false,
    hideDistantElements: false,
    scrollProperty: 'scroll'
  });


	var fullHeight = function() {

		$('.js-fullheight').css('height', $(window).height());
		$(window).resize(function(){
			$('.js-fullheight').css('height', $(window).height());
		});

	};
	fullHeight();

	// loader
	var loader = function() {
		setTimeout(function() { 
			if($('#ftco-loader').length > 0) {
				$('#ftco-loader').removeClass('show');
			}
		}, 1);
	};
	loader();

	// Scrollax
   $.Scrollax();

	var carousel = function() {
		$('.carousel-testimony').owlCarousel({
			center: true,
			loop: true,
			items:1,
			margin: 30,
			stagePadding: 0,
			nav: true,
			navText: ['<span class="ion-ios-arrow-back">', '<span class="ion-ios-arrow-forward">'],
			responsive:{
				0:{
					items: 1
				},
				600:{
					items: 2
				},
				1000:{
					items: 3
				}
			}
		});

		$('.slider-carousel').owlCarousel({
			animateOut: 'fadeOut',
	    	animateIn: 'fadeIn',
			autoplay: true,
			// center: true,
			loop: true,
			items:1,
			margin: 0,
			stagePadding: 0,
			nav: false,
			dots: false,
			touchDrag: true,
        	mouseDrag: false,
			navText: ['<span class="ion-ios-arrow-back">', '<span class="ion-ios-arrow-forward">'],
			responsive:{
				0:{
					items: 1
				},
				600:{
					items: 1
				},
				1000:{
					items: 1
				}
			}
		});

	};
	carousel();

	var scrollWindow = function() {
		$(window).scroll(function(){
			var $w = $(this),
					st = $w.scrollTop(),
					navbar = $('.ftco_navbar'),
					sd = $('.js-scroll-wrap');

			if (st > 150) {
				if ( !navbar.hasClass('scrolled') ) {
					navbar.addClass('scrolled');
					$('.logo1').fadeOut('fast');	
					$('.logo2').fadeIn('fast');	
				}
			} 
			if (st < 150) {
				if ( navbar.hasClass('scrolled') ) {
					navbar.removeClass('scrolled sleep');
					$('.logo2').fadeOut('fast');	
					$('.logo1').fadeIn('fast');
				}
			} 
			if ( st > 350 ) {
				if ( !navbar.hasClass('awake') ) {
					navbar.addClass('awake');	
				}
				
				if(sd.length > 0) {
					sd.addClass('sleep');
				}
			}
			if ( st < 350 ) {
				if ( navbar.hasClass('awake') ) {
					navbar.removeClass('awake');
					navbar.addClass('sleep');
				}
				if(sd.length > 0) {
					sd.removeClass('sleep');
				}
			}
		});
	};
	scrollWindow();

	var isMobile = {
		Android: function() {
			return navigator.userAgent.match(/Android/i);
		},
			BlackBerry: function() {
			return navigator.userAgent.match(/BlackBerry/i);
		},
			iOS: function() {
			return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		},
			Opera: function() {
			return navigator.userAgent.match(/Opera Mini/i);
		},
			Windows: function() {
			return navigator.userAgent.match(/IEMobile/i);
		},
			any: function() {
			return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
		}
	};

	
	var counter = function() {
		
		$('#section-counter').waypoint( function( direction ) {

			if( direction === 'down' && !$(this.element).hasClass('ftco-animated') ) {

				var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',')
				$('.number').each(function(){
					var $this = $(this),
						num = $this.data('number');
						//console.log(num);
					$this.animateNumber(
					  {
					    number: num,
					    numberStep: comma_separator_number_step
					  }, 7000
					);
				});
				
			}

		} , { offset: '95%' } );

	}
	counter();

	var contentWayPoint = function() {
		var i = 0;
		$('.ftco-animate').waypoint( function( direction ) {

			if( direction === 'down' && !$(this.element).hasClass('ftco-animated') ) {
				
				i++;

				$(this.element).addClass('item-animate');
				setTimeout(function(){

					$('body .ftco-animate.item-animate').each(function(k){
						var el = $(this);
						setTimeout( function () {
							var effect = el.data('animate-effect');
							if ( effect === 'fadeIn') {
								el.addClass('fadeIn ftco-animated');
							} else if ( effect === 'fadeInLeft') {
								el.addClass('fadeInLeft ftco-animated');
							} else if ( effect === 'fadeInRight') {
								el.addClass('fadeInRight ftco-animated');
							} else {
								el.addClass('fadeInUp ftco-animated');
							}
							el.removeClass('item-animate');
						},  k * 50, 'easeInOutExpo' );
					});
					
				}, 100);
				
			}

		} , { offset: '95%' } );
	};
	contentWayPoint();


	// navigation
	var OnePageNav = function() {
		$(".smoothscroll[href^='#'], #ftco-nav ul li a[href^='#']").on('click', function(e) {
		 	e.preventDefault();

		 	var hash = this.hash,
		 			navToggler = $('.navbar-toggler');
		 	$('html, body').animate({
		    scrollTop: $(hash).offset().top
		  }, 700, 'easeInOutExpo', function(){
		    window.location.hash = hash;
		  });


		  if ( navToggler.is(':visible') ) {
		  	navToggler.click();
		  }
		});
		$('body').on('activate.bs.scrollspy', function () {
		  //console.log('nice');
		})
	};
	OnePageNav();


	// magnific popup
	$('.image-popup').magnificPopup({
    type: 'image',
    closeOnContentClick: true,
    closeBtnInside: false,
    fixedContentPos: true,
    mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
     gallery: {
      enabled: true,
      navigateByImgClick: true,
      preload: [0,1] // Will preload 0 - before current, and 1 after the current image
    },
    image: {
      verticalFit: true
    },
    zoom: {
      enabled: true,
      duration: 300 // don't foget to change the duration also in CSS
    }
  });

  $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
    disableOn: 700,
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,

    fixedContentPos: false
	});
	
	// $("#calculate_price").on('click',function(){
	// 	var number_of_members = $('#number_of_members').val();
	// 	var payment_plan = $('#payment_plan').val();
	// 	$.post('billing/price_calculator',
	// 		{'number_of_members': number_of_members, 'payment_plan': payment_plan, },
	// 				function(data){
	// 					var pricing = $.parseJSON(data);
	// 					if(payment_plan=='1'&&number_of_members>0){
	// 								number_of_members = total_amount = numeral(number_of_members).format('0,0');
	// 						$('#subscription_plan').slideDown().focus();
	// 						$('#subscription_plan_warning').slideUp();
	// 								var total_amount = parseFloat(pricing.monthly_amount)+parseFloat(pricing.monthly_tax);
	// 								total_amount = numeral(total_amount).format('0,0.00');
	// 						$('#subcription_price_text').html('<p class="small-txt"><span>'+total_amount+'</span> per Month(VAT Inclusive)<span></span> for </span><span>'+number_of_members+'</span> Members</p><i class="icon icon-alerts-02"></i>');
	// 					}else if(payment_plan=='2'&&number_of_members>0){
	// 								number_of_members = total_amount = numeral(number_of_members).format('0,0');
	// 						$('#subscription_plan').slideDown().focus();
	// 						$('#subscription_plan_warning').slideUp();
	// 								var total_amount = parseFloat(pricing.quarterly_amount)+parseFloat(pricing.quarterly_tax);
	// 								total_amount = numeral(total_amount).format('0,0.00');
	// 						$('#subcription_price_text').html('<p class="small-txt"><span>'+total_amount+'</span> per Quarter(VAT Inclusive) <span></span> for </span><span>'+number_of_members+'</span> Members</p><i class="icon icon-alerts-02"></i>');
	// 					}else if(payment_plan=='3'&&number_of_members>0){	
	// 								number_of_members = total_amount = numeral(number_of_members).format('0,0');
	// 						$('#subscription_plan').slideDown().focus();
	// 						$('#subscription_plan_warning').slideUp();
	// 								var total_amount = parseFloat(pricing.annual_amount)+parseFloat(pricing.annual_tax);
	// 								total_amount = numeral(total_amount).format('0,0.00');
	// 						$('#subcription_price_text').html('<p class="small-txt"><span>'+total_amount+'</span> per Annum(VAT Inclusive)<span></span> for </span><span>'+number_of_members+'</span> Members</p><i class="icon icon-alerts-02"></i>');
	// 					}else{
	// 						$('#subscription_plan').slideUp();
	// 						$('#subscription_plan_warning').slideDown();	
	// 					}
	// 		});
	// });

	$('#price_calculators').submit(function(e){
			var number_of_members = $('#number_of_members').val();
			var payment_plan = $('#payment_plan').val();
			$.post('billing/price_calculator',
			{'number_of_members': number_of_members, 'payment_plan': payment_plan, },
					function(data){
							var pricing = $.parseJSON(data);
							if(payment_plan=='1'&&number_of_members>0){
									number_of_members = total_amount = numeral(number_of_members).format('0,0');
									$('#subscription_plan').slideDown().focus();
									$('#subscription_plan_warning').slideUp();
									var total_amount = parseFloat(pricing.monthly_amount)+parseFloat(pricing.monthly_tax);
									total_amount = numeral(total_amount).format('0,0.00');
									$('#subcription_price_text').html('<p class="small-txt"><span>'+total_amount+'</span> per Month(VAT Inclusive)<span></span> for </span><span>'+number_of_members+'</span> Members</p><i class="icon icon-alerts-02"></i>');
							}else if(payment_plan=='2'&&number_of_members>0){
									number_of_members = total_amount = numeral(number_of_members).format('0,0');
									$('#subscription_plan').slideDown().focus();
									$('#subscription_plan_warning').slideUp();
									var total_amount = parseFloat(pricing.quarterly_amount)+parseFloat(pricing.quarterly_tax);
									total_amount = numeral(total_amount).format('0,0.00');
									$('#subcription_price_text').html('<p class="small-txt"><span>'+total_amount+'</span> per Quarter(VAT Inclusive) <span></span> for </span><span>'+number_of_members+'</span> Members</p><i class="icon icon-alerts-02"></i>');
							}else if(payment_plan=='3'&&number_of_members>0){   
									number_of_members = total_amount = numeral(number_of_members).format('0,0');
									$('#subscription_plan').slideDown().focus();
									$('#subscription_plan_warning').slideUp();
									var total_amount = parseFloat(pricing.annual_amount)+parseFloat(pricing.annual_tax);
									total_amount = numeral(total_amount).format('0,0.00');
									$('#subcription_price_text').html('<p class="small-txt"><span>'+total_amount+'</span> per Annum(VAT Inclusive)<span></span> for </span><span>'+number_of_members+'</span> Members</p><i class="icon icon-alerts-02"></i>');
							}else{
									$('#subscription_plan').slideUp();
									$('#subscription_plan_warning').slideDown();    
							}
			});
			e.preventDefault();
	});

})(jQuery);

