/**
 * Banner Carousel optimizado con soporte para animaciones intersect
 */
(function () {
  "use strict";

  // Opciones configurables
  const SETTINGS = {
    autoplaySpeed: 5000, // tiempo entre slides (ms)
    transitionDuration: 800, // duración de la transición (ms)
  };

  /**
   * Inicializa un carrusel de banner
   * @param {HTMLElement} carousel - El elemento carrusel a inicializar
   */
  function initBannerCarousel(carousel) {
    if (!carousel) return;

    // Obtener elementos
    const slides = carousel.querySelectorAll(".banner-slide");
    const dots = carousel.querySelectorAll(".banner-nav-dot");

    // No inicializar si no hay suficientes slides
    if (slides.length <= 1) return;

    // Verificar si hay videos (no usar autoplay con videos)
    const hasVideo = carousel.querySelector(".banner-video-slide") !== null;
    if (hasVideo && carousel.classList.contains("banner-with-video")) {
      console.log("Carousel contiene videos, no se inicializa el autoplay");
      return;
    }

    // Estado del carrusel
    let currentSlide = 0;
    let slideInterval;
    let isTransitioning = false;

    /**
     * Muestra un slide específico
     * @param {number} index - Índice del slide a mostrar
     */
    function showSlide(index) {
      if (isTransitioning || index === currentSlide) return;
      isTransitioning = true;

      // Actualizar clases activas
      slides.forEach((slide) => slide.classList.remove("active"));
      dots.forEach((dot) => dot.classList.remove("active"));

      // Activar el slide solicitado
      slides[index].classList.add("active");

      // Reiniciar animaciones para el nuevo slide
      resetIntersectionAttributes(slides[index]);

      // Activar el dot correspondiente
      if (dots[index]) {
        dots[index].classList.add("active");
      }

      // Actualizar índice actual
      currentSlide = index;

      // Permitir nuevas transiciones después de completar esta
      setTimeout(() => {
        isTransitioning = false;
      }, SETTINGS.transitionDuration);
    }

    /**
     * Reinicia atributos de intersección para activar animaciones
     * @param {HTMLElement} slide - El slide a reiniciar
     */
    function resetIntersectionAttributes(slide) {
      // Seleccionar todos los elementos con data-intersect-once dentro del slide
      const elements = slide.querySelectorAll("[data-intersect-once]");

      elements.forEach((el) => {
        // Primero desactivar para reiniciar
        el.setAttribute("data-intersect-once", "false");

        // Forzar un reflow para que el navegador reconozca el cambio
        void el.offsetWidth;

        // Activar después de un pequeño retraso
        requestAnimationFrame(() => {
          el.setAttribute("data-intersect-once", "true");
        });
      });
    }

    // Funciones de navegación
    function nextSlide() {
      showSlide((currentSlide + 1) % slides.length);
    }

    function prevSlide() {
      showSlide((currentSlide - 1 + slides.length) % slides.length);
    }

    // Funciones de autoplay
    function startAutoplay() {
      stopAutoplay(); // Limpiar cualquier intervalo existente
      slideInterval = setInterval(nextSlide, SETTINGS.autoplaySpeed);
    }

    function stopAutoplay() {
      if (slideInterval) {
        clearInterval(slideInterval);
      }
    }

    function resetAutoplay() {
      stopAutoplay();
      startAutoplay();
    }

    /**
     * Añade botones de navegación al carrusel
     */
    function addNavButtons() {
      // Eliminar botones existentes si los hay
      const existingButtons = carousel.querySelectorAll(
        ".banner-nav-prev, .banner-nav-next"
      );
      existingButtons.forEach((button) => button.remove());

      // Crear botones
      const prevButton = document.createElement("button");
      prevButton.className =
        "banner-nav-prev absolute left-4 top-1/2 transform -translate-y-1/2 z-20 p-2 bg-black/30 hover:bg-black/50 text-white rounded-full transition-all";
      prevButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>';
      prevButton.setAttribute("aria-label", "Slide anterior");

      const nextButton = document.createElement("button");
      nextButton.className =
        "banner-nav-next absolute right-4 top-1/2 transform -translate-y-1/2 z-20 p-2 bg-black/30 hover:bg-black/50 text-white rounded-full transition-all";
      nextButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>';
      nextButton.setAttribute("aria-label", "Slide siguiente");

      // Añadir eventos
      prevButton.addEventListener("click", () => {
        prevSlide();
        resetAutoplay();
      });

      nextButton.addEventListener("click", () => {
        nextSlide();
        resetAutoplay();
      });

      // Añadir botones al carrusel
      carousel.appendChild(prevButton);
      carousel.appendChild(nextButton);
      const pauseButton = document.createElement("button");
      pauseButton.className =
        "banner-nav-pause absolute right-16 bottom-6 z-20 p-2 bg-black/30 hover:bg-black/50 text-white rounded-full transition-all";
      pauseButton.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>';
      pauseButton.setAttribute("aria-label", "Pausar carrusel");

      let isPaused = false;
      pauseButton.addEventListener("click", () => {
        if (isPaused) {
          startAutoplay();
          pauseButton.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>';
        } else {
          stopAutoplay();
          pauseButton.innerHTML =
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
        }
        isPaused = !isPaused;
      });

      carousel.appendChild(pauseButton);
    }

    // Configurar dots
    dots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        showSlide(index);
        resetAutoplay();
      });
    });

    // Soporte para gestos táctiles
    let touchStartX = 0;

    carousel.addEventListener(
      "touchstart",
      (e) => {
        touchStartX = e.changedTouches[0].screenX;
      },
      { passive: true }
    );

    carousel.addEventListener(
      "touchend",
      (e) => {
        const touchEndX = e.changedTouches[0].screenX;
        const threshold = 50; // mínima distancia para considerar swipe

        if (touchEndX < touchStartX - threshold) {
          nextSlide();
          resetAutoplay();
        } else if (touchEndX > touchStartX + threshold) {
          prevSlide();
          resetAutoplay();
        }
      },
      { passive: true }
    );

    // Soporte para teclado
    carousel.setAttribute("tabindex", "0");
    carousel.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") {
        prevSlide();
        resetAutoplay();
      } else if (e.key === "ArrowRight") {
        nextSlide();
        resetAutoplay();
      }
    });

    // Pausar al hover o focus
    carousel.addEventListener("mouseenter", stopAutoplay);
    carousel.addEventListener("mouseleave", startAutoplay);
    carousel.addEventListener("focus", stopAutoplay);
    carousel.addEventListener("blur", startAutoplay);

    // Inicializar carrusel
    addNavButtons();
    startAutoplay();

    // Activar las animaciones iniciales para el primer slide
    if (slides[0]) {
      resetIntersectionAttributes(slides[0]);
    }
  }

  /**
   * Inicializa reproductor de video para banners de video
   * @param {HTMLElement} videoSlide - El slide de video a inicializar
   */
  function initBannerVideo(videoSlide) {
    const videoElement = videoSlide.querySelector("video");

    if (videoElement) {
      videoElement.play().catch((error) => {
        console.warn("Autoplay para video no pudo iniciarse:", error);
      });
    }

    // Activar animaciones en el slide de video
    const intersectElements = videoSlide.querySelectorAll(
      "[data-intersect-once]"
    );

    setTimeout(() => {
      intersectElements.forEach((el) => {
        el.setAttribute("data-intersect-once", "true");
      });
    }, 100);
  }

  // Inicializar al cargar la página
  document.addEventListener("DOMContentLoaded", function () {
    // Inicializar carruseles
    const carousels = document.querySelectorAll(".banner-carousel");
    carousels.forEach((carousel) => {
      initBannerCarousel(carousel);
    });

    // Inicializar videos
    const videoSlides = document.querySelectorAll(".banner-video-slide.active");
    videoSlides.forEach((videoSlide) => {
      initBannerVideo(videoSlide);
    });

    // Para banners simples (solo una imagen), activar las animaciones
    const singleBanners = document.querySelectorAll(
      ".banner-single .banner-slide.active"
    );
    singleBanners.forEach((slide) => {
      const intersectElements = slide.querySelectorAll("[data-intersect-once]");

      requestAnimationFrame(() => {
        intersectElements.forEach((el) => {
          el.setAttribute("data-intersect-once", "true");
        });
      });
    });
  });

  // Hacer la función disponible globalmente
  window.initBannerCarousel = initBannerCarousel;
  window.initBannerVideo = initBannerVideo;
})();
