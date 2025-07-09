/**
 * JavaScript para el bloque de galería de spa
 *
 * Maneja el lightbox y la funcionalidad del slider
 */

(function () {
  "use strict";

  // Esperar a que el DOM esté cargado
  document.addEventListener("DOMContentLoaded", function () {
    // Inicializar todas las galerías en la página
    initGalleries();

    // Cargar scripts necesarios para lightbox y slider
    loadRequiredScripts();
  });

  /**
   * Inicializa todas las galerías en la página
   */
  function initGalleries() {
    // Detectar todas las galerías
    const galleries = document.querySelectorAll(".wptbt-gallery-container");

    if (!galleries.length) return;

    // Inicializar cada galería según su modo
    galleries.forEach(function (gallery) {
      const galleryMode = gallery.querySelector("[data-gallery-mode]");

      if (!galleryMode) return;

      const mode = galleryMode.dataset.galleryMode;

      // Añadir efectos de intersección para animación al hacer scroll
      setupIntersectionObserver(gallery);

      // Si es masonry, inicializar layout después de que las imágenes carguen
      if (mode === "masonry") {
        const images = galleryMode.querySelectorAll("img");
        let imagesLoaded = 0;

        images.forEach(function (img) {
          // Si la imagen ya está cargada
          if (img.complete) {
            imagesLoaded++;
            if (imagesLoaded === images.length) {
              // Todas las imágenes están cargadas
              initMasonry(galleryMode);
            }
          } else {
            // Esperar a que la imagen cargue
            img.addEventListener("load", function () {
              imagesLoaded++;
              if (imagesLoaded === images.length) {
                // Todas las imágenes están cargadas
                initMasonry(galleryMode);
              }
            });

            // Manejar errores de carga
            img.addEventListener("error", function () {
              imagesLoaded++;
              if (imagesLoaded === images.length) {
                // Todas las imágenes están cargadas o han fallado
                initMasonry(galleryMode);
              }
            });
          }
        });
      }

      // Inicializar slider si es necesario
      if (mode === "slider") {
        initSlider(galleryMode);
      }
    });
  }

  /**
   * Configura el observer de intersección para animaciones al hacer scroll
   *
   * @param {Element} element - Elemento a observar
   */
  function setupIntersectionObserver(element) {
    // Comprobar soporte para Intersection Observer
    if (!("IntersectionObserver" in window)) return;

    const observerOptions = {
      root: null,
      rootMargin: "0px",
      threshold: 0.25,
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          // El elemento es visible, añadir clase para animación
          entry.target.setAttribute("data-intersect-once", "true");
          // Dejar de observar después de la animación
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Empezar a observar el elemento
    observer.observe(element);
  }

  /**
   * Carga scripts externos necesarios para lightbox y slider
   */
  function loadRequiredScripts() {
    // Si hay galerías con lightbox, cargar SimpleLightbox
    if (
      document.querySelectorAll(".gallery-image-link[data-lightbox]").length > 0
    ) {
      loadLightboxScripts();
    }

    // Si hay sliders, cargar Swiper
    if (document.querySelectorAll(".wptbt-gallery-slider").length > 0) {
      loadSwiperScripts();
    }
  }

  /**
   * Carga los scripts y estilos para el lightbox
   */
  function loadLightboxScripts() {
    // Comprobar si SimpleLightbox ya está cargado
    if (window.SimpleLightbox) {
      initLightbox();
      return;
    }

    // Cargar CSS
    const lightboxCSS = document.createElement("link");
    lightboxCSS.rel = "stylesheet";
    lightboxCSS.href =
      "https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.10.3/simple-lightbox.min.css";
    document.head.appendChild(lightboxCSS);

    // Cargar JavaScript
    const lightboxJS = document.createElement("script");
    lightboxJS.src =
      "https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.10.3/simple-lightbox.min.js";
    lightboxJS.onload = initLightbox;
    document.body.appendChild(lightboxJS);
  }

  /**
   * Carga los scripts y estilos para Swiper (slider)
   */
  function loadSwiperScripts() {
    // Comprobar si Swiper ya está cargado
    if (window.Swiper) {
      initAllSliders();
      return;
    }

    // Cargar CSS
    const swiperCSS = document.createElement("link");
    swiperCSS.rel = "stylesheet";
    swiperCSS.href =
      "https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.css";
    document.head.appendChild(swiperCSS);

    // Cargar JavaScript
    const swiperJS = document.createElement("script");
    swiperJS.src =
      "https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.js";
    swiperJS.onload = initAllSliders;
    document.body.appendChild(swiperJS);
  }

  /**
   * Inicializa el lightbox para todas las galerías
   */
  function initLightbox() {
    // Agrupar los lightboxes por galerías
    const galleryGroups = {};

    // Recoger todos los enlaces de lightbox
    const lightboxLinks = document.querySelectorAll(
      ".gallery-image-link[data-lightbox]"
    );

    // Agrupar por ID de galería
    lightboxLinks.forEach(function (link) {
      const galleryId = link.getAttribute("data-lightbox");

      if (!galleryGroups[galleryId]) {
        galleryGroups[galleryId] = [];
      }

      galleryGroups[galleryId].push(link);
    });

    // Inicializar SimpleLightbox para cada grupo
    for (const galleryId in galleryGroups) {
      if (galleryGroups.hasOwnProperty(galleryId)) {
        new SimpleLightbox(galleryGroups[galleryId], {
          captionsData: "title",
          captionDelay: 250,
          animationSpeed: 250,
          fadeSpeed: 300,
          scrollZoom: false,
          loop: true,
          docClose: true,
          swipeClose: true,
          closeText: "×",
          navText: ["←", "→"],
        });
      }
    }
  }

  /**
   * Inicializa todos los sliders de la página
   */
  function initAllSliders() {
    const sliders = document.querySelectorAll(".wptbt-gallery-slider");

    sliders.forEach(function (slider) {
      initSlider(slider);
    });
  }

  /**
   * Inicializa un slider específico
   *
   * @param {Element} sliderContainer - Contenedor del slider
   */
  function initSlider(sliderContainer) {
    // Esperar a que Swiper esté disponible
    if (!window.Swiper) return;

    // Buscar el contenedor de Swiper dentro del contenedor de la galería
    const swiperElement = sliderContainer.querySelector(".swiper-container");

    if (!swiperElement) return;

    // Opciones de Swiper
    const swiperOptions = {
      slidesPerView: 1,
      spaceBetween: 20,
      grabCursor: true,
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      pagination: {
        el: sliderContainer.querySelector(".swiper-pagination"),
        clickable: true,
      },
      navigation: {
        nextEl: sliderContainer.querySelector(".swiper-button-next"),
        prevEl: sliderContainer.querySelector(".swiper-button-prev"),
      },
      breakpoints: {
        640: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
      },
    };

    // Inicializar Swiper
    new Swiper(swiperElement, swiperOptions);
  }

  /**
   * Inicializa el layout de masonry
   *
   * @param {Element} masonryContainer - Contenedor del masonry
   */
  function initMasonry(masonryContainer) {
    // Aquí podrías usar una biblioteca como Masonry.js si es necesario
    // Para la versión simple, el CSS ya proporciona el efecto masonry
    // por lo que esta función está preparada para futuras mejoras
    // Si se desea agregar Masonry.js en el futuro, sería así:
    /*
        if (window.Masonry) {
            new Masonry(masonryContainer, {
                itemSelector: '.wptbt-gallery-item',
                columnWidth: '.wptbt-gallery-item',
                percentPosition: true,
                gutter: parseInt(masonryContainer.style.getPropertyValue('--gallery-gap') || '16')
            });
        }
        */
  }
})();
