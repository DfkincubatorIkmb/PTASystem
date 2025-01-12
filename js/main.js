//( function showDetails() {
//        alert("Details about John Abraham from New York, USA.");
//    }

(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner(0);


//    // Sticky Navbar
//    $(window).scroll(function () {
//        if ($(this).scrollTop() > 45) {
//            $('.navbar').addClass('sticky-top shadow-sm');
//        } else {
//            $('.navbar').removeClass('sticky-top shadow-sm');
//        }
//    });


    // International Tour carousel
    $(".InternationalTour-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: false,
        dots: true,
        loop: true,
        margin: 25,
        nav : false,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:2
            },
            1200:{
                items:3
            }
        }
    });


    // packages carousel
    $(".packages-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: false,
        dots: false,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:2
            },
            1200:{
                items:3
            }
        }
    });


    // testimonial carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: true,
        dots: true,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:2
            },
            1200:{
                items:3
            }
        }
    });

    
   $(document).ready(function () {
    var navbar = $('.navbar');
    var backToTopBtn = $('.back-to-top');
    var isScrolling = false; // Flag to track if scrolling animation is in progress

    // Function to handle scroll events
    function handleScroll() {
        var scrollTop = $(window).scrollTop();

        // Toggle sticky navbar class
        if (scrollTop > 20) {
            navbar.addClass('sticky-top shadow-sm');
        } else {
            navbar.removeClass('sticky-top shadow-sm');
        }

        // Toggle back-to-top button visibility
        if (scrollTop > 20) {
            if (!isScrolling) {
                backToTopBtn.stop(true, true).fadeIn('fast');
            }
        } else {
            backToTopBtn.stop(true, true).fadeOut('fast');
        }
    }

    // Initial handling of scroll position
    handleScroll();

    // Smooth scroll to top on button click
    backToTopBtn.click(function (e) {
        e.preventDefault();
        isScrolling = true; // Set scrolling flag to true
        $('html, body').stop().animate({ scrollTop: 0 }, {
            duration: 20,
            easing: 'easeInOutExpo',
            complete: function () {
                isScrolling = false; // Reset scrolling flag to false
            }
        });
    });

    // Throttle the scroll event for better performance
    var throttleTimeout;
    $(window).scroll(function () {
        if (!throttleTimeout) {
            throttleTimeout = setTimeout(function () {
                handleScroll();
                throttleTimeout = null;
            }, 20); // Adjust the delay as needed
        }
    });
});

})(jQuery);

