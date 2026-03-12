
(function ($) {

  "use strict";

  // Navbar collapse on click
  $('.navbar-collapse a').on('click', function () {
    $(".navbar-collapse").collapse('hide');
  });

  // Smooth scroll
  $('.smoothscroll').click(function () {
    var el = $(this).attr('href');
    var elWrapped = $(el);
    var header_height = $('.navbar').height();

    scrollToDiv(elWrapped, header_height);
    return false;

    function scrollToDiv(element, navheight) {
      var offset = element.offset();
      var offsetTop = offset.top;
      var totalScroll = offsetTop - navheight;

      $('body,html').animate({
        scrollTop: totalScroll
      }, 300);
    }
  });

  // Wait for deferred scripts/DOM to be ready
  $(document).ready(function () {

    // 1. Swiper Initialization
    if ($('.swiper-container').length > 0 && typeof Swiper !== 'undefined') {
      new Swiper('.swiper-container', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 1.2,
        spaceBetween: 10,
        coverflowEffect: {
          rotate: 0,
          stretch: 0,
          depth: 150,
          modifier: 1,
          slideShadows: false,
        },
        speed: 2500,
        loop: true,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
          reverseDirection: true,
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        observer: true,
        observeParents: true,
        breakpoints: {
          768: {
            slidesPerView: 1.8,
            spaceBetween: 10,
          }
        }
      });
    }

    // 2. Marquee Text Animation
    const container = document.querySelector('.marquee-container');
    const textElement = document.querySelector('.marquee-text');

    if (container && textElement) {
      const containerWidth = container.offsetWidth;
      const textWidth = textElement.scrollWidth;

      if (textWidth > containerWidth) {
        textElement.style.position = 'relative';
        let currentX = containerWidth;

        function animateText() {
          currentX -= 0.5;
          if (currentX < -textWidth) {
            currentX = containerWidth;
          }
          textElement.style.left = currentX + 'px';
          requestAnimationFrame(animateText);
        }
        requestAnimationFrame(animateText);
      }
    }

    // 3. Scroll Overlay and Parallax Effects
    window.addEventListener('scroll', function () {
      // Menu Darken Overlay
      const overlay = document.getElementById('menu-darken-overlay');
      if (overlay) {
        const maxScroll = 1000;
        let opacity = Math.min(window.scrollY / maxScroll, 1);
        overlay.style.opacity = opacity;
      }

      // Parallax Effect
      const section = document.querySelector('.agradecimiento-section');
      const texto = document.querySelector('.agradecimiento-texto-parallax');
      if (section && texto) {
        let scrollY = window.scrollY - section.offsetTop;
        texto.style.transform = `translateY(${scrollY * 0.8}px)`;
      }
    });

  });

})(window.jQuery);


