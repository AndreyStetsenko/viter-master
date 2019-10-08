/* ===================================================================
 * KMWebDev - Main JS
 *
 * ------------------------------------------------------------------- */

(function($) {

    "use strict";

    var cfg = {
        scrollDuration : 800, // smoothscroll duration
        mailChimpURL   : 'https://facebook.us8.list-manage.com/subscribe/post?u=cdb7b577e41181934ed6a6a44&amp;id=e6957d85dc'   // mailchimp url
    },

    $WIN = $(window);

    // Add the User Agent to the <html>
    // will be used for IE10 detection (Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0))
    var doc = document.documentElement;
    doc.setAttribute('data-useragent', navigator.userAgent);


   /* Preloader
    * -------------------------------------------------- */
    var clPreloader = function() {

        $("html").addClass('cl-preload');

        $WIN.on('load', function() {

            //force page scroll position to top at page refresh
            // $('html, body').animate({ scrollTop: 0 }, 'normal');

            // will first fade out the loading animation
            $("#loader").fadeOut("slow", function() {
                // will fade out the whole DIV that covers the website.
                $("#preloader").delay(300).fadeOut("slow");
            });

            // for hero content animations
            $("html").removeClass('cl-preload');
            $("html").addClass('cl-loaded');

        });
    };


   /* Menu on Scrolldown
    * ------------------------------------------------------ */
    var clMenuOnScrolldown = function() {

        var menuTrigger = $('.header-menu-toggle');

        $WIN.on('scroll', function() {

            if ($WIN.scrollTop() > 150) {
                menuTrigger.addClass('opaque');
            }
            else {
                menuTrigger.removeClass('opaque');
            }

        });
    };


   /* OffCanvas Menu
    * ------------------------------------------------------ */
    var clOffCanvas = function() {

        var menuTrigger     = $('.header-menu-toggle'),
            nav             = $('.header-nav'),
            closeButton     = nav.find('.header-nav__close'),
            siteBody        = $('body'),
            mainContents    = $('section, footer');

        // open-close menu by clicking on the menu icon
        menuTrigger.on('click', function(e){
            e.preventDefault();
            // menuTrigger.toggleClass('is-clicked');
            siteBody.toggleClass('menu-is-open');
        });

        // close menu by clicking the close button
        closeButton.on('click', function(e){
            e.preventDefault();
            menuTrigger.trigger('click');
        });

        // close menu clicking outside the menu itself
        siteBody.on('click', function(e){
            if( !$(e.target).is('.header-nav, .header-nav__content, .header-menu-toggle, .header-menu-toggle span') ) {
                // menuTrigger.removeClass('is-clicked');
                siteBody.removeClass('menu-is-open');
            }
        });

    };


   /* photoswipe
    * ----------------------------------------------------- */
    var clPhotoswipe = function() {
        var items = [],
            $pswp = $('.pswp')[0],
            $folioItems = $('.item-folio');

            // get items
            $folioItems.each( function(i) {

                var $folio = $(this),
                    $thumbLink =  $folio.find('.thumb-link'),
                    $title = $folio.find('.item-folio__title'),
                    $caption = $folio.find('.item-folio__caption'),
                    $titleText = '<h4>' + $.trim($title.html()) + '</h4>',
                    $captionText = $.trim($caption.html()),
                    $href = $thumbLink.attr('href'),
                    $size = $thumbLink.data('size').split('x'),
                    $width  = $size[0],
                    $height = $size[1];

                var item = {
                    src  : $href,
                    w    : $width,
                    h    : $height
                }

                if ($caption.length > 0) {
                    item.title = $.trim($titleText + $captionText);
                }

                items.push(item);
            });

            // bind click event
            $folioItems.each(function(i) {

                $(this).on('click', function(e) {
                    e.preventDefault();
                    var options = {
                        index: i,
                        showHideOpacity: true
                    }

                    // initialize PhotoSwipe
                    var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
                    lightBox.init();
                });

            });

    };


   /* Stat Counter
    * ------------------------------------------------------ */
    var clStatCount = function() {

        var statSection = $(".about-stats"),
            stats = $(".stats__count");

        statSection.waypoint({

            handler: function(direction) {

                if (direction === "down") {

                    stats.each(function () {
                        var $this = $(this);

                        $({ Counter: 0 }).animate({ Counter: $this.text() }, {
                            duration: 4000,
                            easing: 'swing',
                            step: function (curValue) {
                                $this.text(Math.ceil(curValue));
                            }
                        });
                    });

                }

                // trigger once only
                this.destroy();

            },

            offset: "90%"

        });
    };


   /* Masonry
    * ---------------------------------------------------- */
    var clMasonryFolio = function () {

        var containerBricks = $('.masonry');

        containerBricks.imagesLoaded(function () {
            containerBricks.masonry({
                itemSelector: '.masonry__brick',
                resize: true
            });
        });
    };


   /* slick slider
    * ------------------------------------------------------ */
    var clSlickSlider = function() {

        $('.clients').slick({
            arrows: false,
            dots: true,
            infinite: true,
            slidesToShow: 6,
            slidesToScroll: 1,
            //autoplay: true,
            pauseOnFocus: false,
            autoplaySpeed: 1000,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 5
                    }
                },
                {
                    breakpoint: 1000,
                    settings: {
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 800,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 500,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }

            ]
        });

        $('.testimonials').slick({
            arrows: true,
            dots: false,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            adaptiveHeight: true,
            pauseOnFocus: false,
            autoplaySpeed: 1500,
            responsive: [
                {
                    breakpoint: 900,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 800,
                    settings: {
                        arrows: false,
                        dots: true
                    }
                }
            ]
        });

    };

   /* Smooth Scrolling
    * ------------------------------------------------------ */
    var clSmoothScroll = function() {

        $('.smoothscroll').on('click', function (e) {
            var target = this.hash,
            $target    = $(target);

                e.preventDefault();
                e.stopPropagation();

            $('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, cfg.scrollDuration, 'swing').promise().done(function () {

                // check if menu is open
                if ($('body').hasClass('menu-is-open')) {
                    $('.header-menu-toggle').trigger('click');
                }

                window.location.hash = target;
            });
        });

    };


   /* Placeholder Plugin Settings
    * ------------------------------------------------------ */
    var clPlaceholder = function() {
        $('input, textarea, select').placeholder();
    };


   /* Alert Boxes
    * ------------------------------------------------------ */
    var clAlertBoxes = function() {

        $('.alert-box').on('click', '.alert-box__close', function() {
            $(this).parent().fadeOut(500);
        });

    };


   /* Contact Form
    * ------------------------------------------------------ */
    var clContactForm = function() {

        /* local validation */
        $('#contactForm').validate({

            /* submit via ajax */
            submitHandler: function(form) {

                var sLoader = $('.submit-loader');

                $.ajax({

                    type: "POST",
                    url: globalObject.url + "sendEmail.php",
                    data: $(form).serialize(),
                    beforeSend: function() {

                        sLoader.slideDown("slow");

                    },
                    success: function(msg) {

                        // Message was sent
                        if (msg == 'OK') {
                            sLoader.slideUp("slow");
                            $('.message-warning').fadeOut();
                            $('#contactForm').fadeOut();
                            $('.message-success').fadeIn();
                        }
                        // There was an error
                        else {
                            sLoader.slideUp("slow");
                            $('.message-warning').html(msg);
                            $('.message-warning').slideDown("slow");
                        }

                    },
                    error: function() {

                        sLoader.slideUp("slow");
                        $('.message-warning').html("Something went wrong. Please try again.");
                        $('.message-warning').slideDown("slow");

                    }

                });
            }

        });
    };


   /* Animate On Scroll
    * ------------------------------------------------------ */
    var clAOS = function() {

        AOS.init( {
            offset: 200,
            duration: 600,
            easing: 'ease-in-sine',
            delay: 300,
            once: true,
            disable: 'mobile'
        });

    };


   /* AjaxChimp
    * ------------------------------------------------------ */
    var clAjaxChimp = function() {

        $('#mc-form').ajaxChimp({
            language: 'es',
            url: cfg.mailChimpURL
        });

        // Mailchimp translation
        //
        //  Defaults:
        //	 'submit': 'Submitting...',
        //  0: 'We have sent you a confirmation email',
        //  1: 'Please enter a value',
        //  2: 'An email address must contain a single @',
        //  3: 'The domain portion of the email address is invalid (the portion after the @: )',
        //  4: 'The username portion of the email address is invalid (the portion before the @: )',
        //  5: 'This email address looks fake or invalid. Please enter a real email address'

        $.ajaxChimp.translations.es = {
            'submit': 'Submitting...',
            0: '<i class="fa fa-check"></i> We have sent you a confirmation email',
            1: '<i class="fa fa-warning"></i> You must enter a valid e-mail address.',
            2: '<i class="fa fa-warning"></i> E-mail address is not valid.',
            3: '<i class="fa fa-warning"></i> E-mail address is not valid.',
            4: '<i class="fa fa-warning"></i> E-mail address is not valid.',
            5: '<i class="fa fa-warning"></i> E-mail address is not valid.'
        }

    };


   /* Back to Top
    * ------------------------------------------------------ */
    var clBackToTop = function() {

        var pxShow  = 500,         // height on which the button will show
        fadeInTime  = 400,         // how slow/fast you want the button to show
        fadeOutTime = 400,         // how slow/fast you want the button to hide
        scrollSpeed = 300,         // how slow/fast you want the button to scroll to top. can be a value, 'slow', 'normal' or 'fast'
        goTopButton = $(".go-top")

        // Show or hide the sticky footer button
        $(window).on('scroll', function() {
            if ($(window).scrollTop() >= pxShow) {
                goTopButton.fadeIn(fadeInTime);
            } else {
                goTopButton.fadeOut(fadeOutTime);
            }
        });
    };


   /* Initialize
    * ------------------------------------------------------ */
    (function ssInit() {

        clPreloader();
        clMenuOnScrolldown();
        clOffCanvas();
        clPhotoswipe();
        clStatCount();
        clMasonryFolio();
        clSlickSlider();
        clSmoothScroll();
        clPlaceholder();
        clAlertBoxes();
        clContactForm();
        clAOS();
        clAjaxChimp();
        clBackToTop();

    })();




})(jQuery);

// player
// ===== Open Nav =====
$( ".burger-wrapper" ).click(function() {

 // ===== If Nav is not open
 if($('.nav').css("display") == "none"){
   TweenMax.to(".dim", 0.5, {opacity: 1, display: 'block', ease: Power2.easeInOut});
   TweenMax.fromTo(".nav", 0.5, {xPercent: -100},
                 {xPercent: 0, display: 'block', ease: Expo.easeOut});
   TweenMax.staggerFrom('.nav li', 0.5, {opacity:0, y: 20, ease: Power2.easeInOut}, 0.1);

   $('.logo-text').css({'opacity': '0', 'display': 'none'});
 }
 // ===== If Nav is open	and in Curation page
 else if($('.nav').css("display") == "block" && $('#curator').css("display") == "block"){
   TweenMax.to(".dim", 0.5, {opacity: 0, display: 'none', ease: Power2.easeInOut});
   TweenMax.to(".nav", 0.5, {xPercent: -100, display:'none', ease: Expo.easeOut});
   // $('.logo-text').css({'opacity': '1', 'display': 'block'});
 }

 else {
   TweenMax.to(".dim", 0.5, {opacity: 0, display: 'none', ease: Power2.easeInOut});
   TweenMax.to(".nav", 0.5, {xPercent: -100, display:'none', ease: Expo.easeOut});
   $('.logo-text').css({'opacity': '1', 'display': 'block'});
 }

});


// ===== Open Player + dim on =====

$( ".btn-open-player, .track_info" ).click(function() {
 TweenMax.to(".dim", 0.5, {opacity: 1, display: 'block', ease: Power2.easeInOut});
 TweenMax.fromTo("#player", 0.5, {xPercent: -100},
                 {xPercent: 0, display: 'block', ease: Expo.easeOut});
 TweenMax.to(".mini-player", 0.5, {x: -190, ease: Expo.easeOut});
});

$('.dim').click(function() {
 TweenMax.to(".dim", 0.5, {opacity: 0, display: 'none', ease: Power2.easeInOut});
 TweenMax.to("#player", 0.5, {xPercent: -100, display: 'none', ease: Expo.easeOut});
 TweenMax.to(".nav", 0.5, {xPercent: -100, display: 'none', ease: Power2.easeInOut})
 TweenMax.to(".mini-player", 0.5, {x: 0, ease: Expo.easeOut});
});

// ===== Mini Player - Play/Pause Switch =====

$('.btn-play').click(function(){
 TweenMax.to($('.btn-play'), 0.2, {x: 20, opacity: 0, scale: 0.3,  display: 'none', ease: Power2.easeInOut});
 TweenMax.fromTo($('.btn-pause'), 0.2, {x: -20, opacity: 0, scale: 0.3, display: 'none'},
                {x: 0, opacity: 1, scale: 1, display: 'block', ease: Power2.easeInOut});
});

$('.btn-pause').click(function(){
 TweenMax.to($('.btn-pause'), 0.2, {x: 20, opacity: 0, display: 'none', scale: 0.3, ease: Power2.easeInOut});
 TweenMax.fromTo($('.btn-play'), 0.2, {x: -20, opacity: 0, scale: 0.3, display: 'none'},
                {x: 0, opacity: 1, display: 'block', scale: 1, ease: Power2.easeInOut});
});

// ===== HoverIn/HoverOut Flash Effect =====

$('.track_info').hover(function(){

 TweenMax.fromTo($(this), 0.5, {opacity: 0.5, ease: Power2.easeInOut},
                {opacity: 1})},
 function(){
   $(this).css("opacity", "1");
});

$('.burger-wrapper, .logo-text, .back_btn').hover(function(){

 TweenMax.fromTo($(this), 0.5, {opacity: 0.5, ease: Power2.easeInOut},
                {opacity: 1})},
 function(){
   $(this).css("opacity", "1")
});

$('.btn-open-player').hover(function(){

 TweenMax.fromTo($(this), 0.5, {opacity: 0.5, ease: Power2.easeInOut},
                {opacity: 1})},
 function(){
   $(this).css("opacity", "1")
});

$('.nav a').hover(function(){

 TweenMax.fromTo($(this), 0.5, {opacity: 0.5, ease: Power2.easeInOut},
                {opacity: 1})},
 function(){
   $(this).css("opacity", "1")
});

// ===== Player - List Items =====
$('.list_item').click(function() {
 $('.list_item').removeClass('selected');
 $(this).addClass('selected');
});


// ===== Main Play Button - Hover =====

$('.text-wrap .text').hover(function(){
 TweenMax.to($('.main-btn_wrapper'), 0.5, {opacity: 1, display: 'block', position: 'absolute', scale: 1, ease: Elastic.easeOut.config(1, 0.75)}),
 TweenMax.to($('.line'), 0.5, {css: { scaleY: 0.6, transformOrigin: "center center" }, ease: Expo.easeOut})},

 function(){
   TweenMax.to($('.main-btn_wrapper'), 0.5, {opacity: 0, display: 'none', scale: 0, ease: Elastic.easeOut.config(1, 0.75)}),
   TweenMax.to($('.line'), 0.5, {css: { scaleY: 1, transformOrigin: "center center" }, ease: Expo.easeOut})
});


// ===== Curation Page  =====
// ===== List  =====
$('.item').hover(function(){
 TweenMax.to($(this), 0.5, {y: -30, ease: Power2.easeInOut}),
 $(this).children('.thumb').addClass('shadow'),
 $(this).children('.connect_btn').addClass('shadow'),

 TweenMax.to($(this).children('.info'), 0.5, {opacity: 1, ease: Power2.easeInOut})
 },

 function(){
   TweenMax.to($(this), 0.5, {y: 0, ease: Power2.easeInOut}),
   $(this).children('.thumb').removeClass('shadow'),
   $(this).children('.connect_btn').removeClass('shadow'),

   TweenMax.to($(this).children('.info'), 0.5, {opacity: 0, ease: Power2.easeInOut})
});


// ===== Home Page to Curation Page Transition  =====
// ===== Main Play Button Activate =====

$('.text-wrap .text').click(function(){

 var homeToMain = new TimelineMax({});

 // Hide
 $('.logo-text').css('display', 'none'),
 homeToMain.to($('.line, .text-wrap'), 0.5, {display: 'none', opacity: 0, y: -20, ease: Power2.easeInOut}, 0),

 // Background down
 homeToMain.to($('.wave-container'), 1, {yPercent: 30, ease: Power2.easeInOut}, 0),

 // Show
 $('#curator').css('display', 'block'),
 homeToMain.fromTo($('.back_btn'), 0.8, {x: 15},
                   {display: 'flex', opacity: 1, x: 0, ease: Power2.easeInOut}, 1),

 homeToMain.fromTo($('.curator_title_wrapper'), 0.8, {opacity: 0, x: 30},
                   {opacity: 1, x: 0, ease: Power2.easeInOut}, 1),

 homeToMain.fromTo($('.curator_list'), 0.8, {opacity: 0, display: 'none', x: 30},
                 {opacity: 1, x: 0, display: 'block', ease: Power2.easeInOut}, 1.2)

});


// ===== Curation Page to Playlist Page Transition  =====
// ===== Item Activate =====
$('.item').click(function(){
 var mainToPlaylist = new TimelineMax({});

 // Hide
 mainToPlaylist.to($('#curator'), 0.8, {display: 'none', opacity: 0, scale: 1.1, ease: Power2.easeInOut}, 0)

 // mainToPlaylist.fromTo($('.curator_list'), 0.5, {opacity: 1, display: 'block', x: 0},
 // 									{opacity: 0, x: 30, display: 'none', ease: Power2.easeInOut}, 0.5),




});

// ===== Back Button Activate =====

$('.back_btn').click(function(){
// ===== From Playlist(3) to Main(2)
 if($('#curator').css("display") == "none"){
   var playlistToMain = new TimelineMax({});

   // Hide
   playlistToMain.fromTo($('#curator'), 0.8, {display: 'none', opacity: 0, scale: 1.1},
                     {display: 'block', opacity: 1, scale: 1, ease: Power2.easeInOut}, 0)
 }

 // From Main(2) to Home(1)
 else {
   var mainToHome = new TimelineMax({});
   // Hide
   mainToHome.fromTo($('.curator_title_wrapper'), 0.5, {opacity: 1, x: 0},
                     {opacity: 0, x: 30, ease: Power2.easeInOut}, 0.2),

   mainToHome.fromTo($('.curator_list'), 0.5, {opacity: 1, display: 'block', x: 0},
                     {opacity: 0, x: 30, display: 'none', ease: Power2.easeInOut}, 0.5),


   mainToHome.to($('.back_btn'), 0.5, {display: 'none', opacity: 0, x: 15, ease: Power2.easeInOut}, 0.5),

   mainToHome.to($('#curator'), 0, {display: 'none', ease: Power2.easeInOut}, 1),

   // Background Up
   mainToHome.to($('.wave-container'), 1, {yPercent: 0, ease: Power2.easeInOut}, 1),

   // 	Show
   mainToHome.to($('.text-wrap'), 0.5, {display: 'flex', opacity: 1, y: 0, ease: Power2.easeInOut}, 1.2),

   mainToHome.to($('.logo-text, .line'), 0.5, {display: 'block', opacity: 1, y: 0, ease: Power2.easeInOut}, 1.2),

   // 	Force to redraw by using y translate
   mainToHome.fromTo($('.text-wrap .text'), 0.1, {y: 0.1, position: 'absolute'},
                     {y: 0, position: 'relative', ease: Power2.easeInOut}, 1.3)
   // $('.text-wrap .text').css('position', 'relative');
 }
});

$(document).ready(function() {
  $('.popup-youtube').magnificPopup({
    disableOn: 320,
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,
    fixedContentPos: true
  });
});
$('.item').magnificPopup({
  delegate: 'a',
});
