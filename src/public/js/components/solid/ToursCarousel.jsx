// src/public/js/components/solid/ToursCarousel.jsx
import {
  createSignal,
  createEffect,
  onMount,
  For,
  Show,
  onCleanup,
} from "solid-js";
import { __ } from "../../solid-core";

/**
 * Componente de Carousel de Tours con Solid.js
 * Carousel infinito con animación automática de derecha a izquierda
 */
const ToursCarousel = (props) => {
  // Función para obtener traducciones específicas de este componente
  const getTranslation = (text, domain = "wptbt-tours-carousel") => {
    const componentTranslations = window.wptbtI18n_tours_carousel || {};
    if (componentTranslations[text]) {
      return componentTranslations[text];
    }
    return __(text, domain);
  };

  // Propiedades con valores por defecto
  const {
    title = "",
    subtitle = "",
    description = "",
    tours = [],
    autoplaySpeed = 3000, // Velocidad del autoplay en ms
    showDots = true,
    showArrows = true,
    pauseOnHover = true,
    infinite = true,
    slidesToShow = 3, // Número de tours visibles a la vez
    backgroundColor = "#F8FAFC",
    textColor = "#1F2937",
    accentColor = "#DC2626",
    secondaryColor = "#059669",
    fullWidth = false,
    animationDirection = "left", // "left" | "right"
    // Nuevas props para SEO
    carouselId = "tours-carousel",
    baseUrl = window.location.origin + window.location.pathname,
  } = props;

  // Estados
  const [currentSlide, setCurrentSlide] = createSignal(0);
  const [isAutoPlaying, setIsAutoPlaying] = createSignal(true);
  const [autoplayInterval, setAutoplayInterval] = createSignal(null);
  const [isHovering, setIsHovering] = createSignal(false);
  const [isLoaded, setIsLoaded] = createSignal(false);
  const [toursLoaded, setToursLoaded] = createSignal(0);
  const [hoveredTour, setHoveredTour] = createSignal(null);

  // Referencias
  let carouselRef;

  // Calcular slides necesarios para efecto infinito
  const totalSlides = () => Math.ceil(tours.length / slidesToShow);
  
  // Duplicar tours para efecto infinito suave
  const infiniteTours = () => {
    if (!infinite || tours.length <= slidesToShow) return tours;
    return [...tours, ...tours, ...tours]; // Triple para mejor efecto infinito
  };

  // Autoplay
  const startAutoplay = () => {
    if (!isAutoPlaying() || autoplaySpeed <= 0) return;
    
    const interval = setInterval(() => {
      if (!isHovering() || !pauseOnHover) {
        nextSlide();
      }
    }, autoplaySpeed);
    
    setAutoplayInterval(interval);
  };

  const stopAutoplay = () => {
    if (autoplayInterval()) {
      clearInterval(autoplayInterval());
      setAutoplayInterval(null);
    }
  };

  // Navegación
  const nextSlide = () => {
    if (infinite) {
      setCurrentSlide((prev) => (prev + 1) % totalSlides());
    } else {
      setCurrentSlide((prev) => Math.min(prev + 1, totalSlides() - 1));
    }
  };

  const prevSlide = () => {
    if (infinite) {
      setCurrentSlide((prev) => (prev - 1 + totalSlides()) % totalSlides());
    } else {
      setCurrentSlide((prev) => Math.max(prev - 1, 0));
    }
  };

  const goToSlide = (index) => {
    setCurrentSlide(index);
  };

  // Efectos
  onMount(() => {
    if (tours.length > slidesToShow) {
      startAutoplay();
    }
    setIsLoaded(true);
  });

  onCleanup(() => {
    stopAutoplay();
  });

  createEffect(() => {
    if (isHovering() && pauseOnHover) {
      stopAutoplay();
    } else if (!isHovering() && isAutoPlaying()) {
      startAutoplay();
    }
  });

  // Manejar carga de imágenes
  const handleImageLoad = () => {
    setToursLoaded(prev => prev + 1);
  };

  // Obtener precio formateado
  const getFormattedPrice = (tour) => {
    if (!tour.price) return getTranslation("Price on request");
    return `$${tour.price}`;
  };

  // Obtener URL del tour
  const getTourUrl = (tour) => {
    return tour.permalink || `${baseUrl}tours/${tour.slug || tour.id}`;
  };

  // Estilo de transformación para el carousel
  const getCarouselTransform = () => {
    const translateValue = animationDirection === "left" 
      ? -currentSlide() * (100 / slidesToShow)
      : currentSlide() * (100 / slidesToShow);
    
    return {
      transform: `translateX(${translateValue}%)`,
      transition: "transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)",
    };
  };

  // Calcular slides visibles responsivos
  const getResponsiveSlidesToShow = () => {
    const maxSlides = Math.min(slidesToShow, 4); // Máximo 4 para mejor visualización
    return {
      small: Math.min(maxSlides, 1), // 1 en móvil
      medium: Math.min(maxSlides, 2), // 2 en tablet
      large: maxSlides, // Completo en desktop
    };
  };

  const responsive = getResponsiveSlidesToShow();

  return (
    <>
      <section
        id={carouselId}
        class={`tours-carousel-component w-full py-8 md:py-12 relative ${
          fullWidth ? "vw-100" : ""
        }`}
        style={{
          backgroundColor,
          color: textColor,
          ...(fullWidth
            ? {
                "margin-left": "calc(50% - 50vw)",
                "margin-right": "calc(50% - 50vw)",
                width: "100vw",
                "max-width": "100vw",
              }
            : {}),
        }}
        onMouseEnter={() => setIsHovering(true)}
        onMouseLeave={() => setIsHovering(false)}
      >
        <div class="container mx-auto px-4 relative">
          {/* Encabezado */}
          <Show when={title || subtitle || description}>
            <header class="text-center mb-8 relative">
              <Show when={subtitle}>
                <p
                  class="block text-base italic font-medium mb-1"
                  style={{ color: accentColor }}
                >
                  {subtitle}
                </p>
              </Show>

              <Show when={title}>
                <div class="relative inline-block">
                  <h2 class="text-2xl md:text-3xl font-bold mb-2">
                    {title}
                  </h2>
                  <div
                    class="absolute -bottom-1 left-1/2 w-16 h-0.5 transform -translate-x-1/2"
                    style={{ "background-color": accentColor }}
                    aria-hidden="true"
                  >
                    <div
                      class="absolute left-1/2 top-1/2 w-2 h-2 rounded-full -translate-x-1/2 -translate-y-1/2"
                      style={{ "background-color": accentColor }}
                    ></div>
                  </div>
                </div>
              </Show>

              <Show when={description}>
                <p class="text-gray-600 mt-3 max-w-2xl mx-auto">
                  {description}
                </p>
              </Show>
            </header>
          </Show>

          {/* Carousel container */}
          <div class="tours-carousel-wrapper relative">
            {/* Botones de navegación */}
            <Show when={showArrows && tours.length > slidesToShow}>
              <button
                type="button"
                class="carousel-prev absolute left-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                style={{ "focus:ring-color": accentColor, border: `2px solid ${accentColor}20` }}
                onClick={prevSlide}
                aria-label={getTranslation("Previous tours")}
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke={textColor}
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 19l-7-7 7-7"
                  ></path>
                </svg>
              </button>

              <button
                type="button"
                class="carousel-next absolute right-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                style={{ "focus:ring-color": accentColor, border: `2px solid ${accentColor}20` }}
                onClick={nextSlide}
                aria-label={getTranslation("Next tours")}
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke={textColor}
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                  ></path>
                </svg>
              </button>
            </Show>

            {/* Carousel track */}
            <div class="carousel-track overflow-hidden mx-12">
              <div
                class="carousel-slides flex transition-transform duration-600 ease-in-out"
                style={getCarouselTransform()}
                ref={carouselRef}
              >
                <For each={infiniteTours()}>
                  {(tour, index) => (
                    <article
                      class={`tour-card flex-shrink-0 px-3 transition-all duration-500`}
                      style={{
                        width: `${100 / slidesToShow}%`,
                        transform: hoveredTour() === index() ? "translateY(-8px)" : "translateY(0)",
                      }}
                      onMouseEnter={() => setHoveredTour(index())}
                      onMouseLeave={() => setHoveredTour(null)}
                    >
                      <div class="tour-card-inner bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl">
                        {/* Imagen del tour */}
                        <div class="tour-image relative h-48 overflow-hidden">
                          <img
                            src={tour.featured_image || tour.image}
                            alt={tour.title}
                            class="w-full h-full object-cover transition-transform duration-700"
                            style={{
                              transform: hoveredTour() === index() ? "scale(1.1)" : "scale(1)",
                            }}
                            loading="lazy"
                            onLoad={handleImageLoad}
                          />
                          
                          {/* Badge de precio */}
                          <Show when={tour.price}>
                            <div
                              class="absolute top-4 right-4 px-3 py-1 rounded-full text-white font-semibold text-sm shadow-md"
                              style={{ "background-color": accentColor }}
                            >
                              {getFormattedPrice(tour)}
                            </div>
                          </Show>

                          {/* Badge de duración */}
                          <Show when={tour.duration}>
                            <div class="absolute bottom-4 left-4 px-3 py-1 bg-black/70 text-white rounded-full text-sm backdrop-blur-sm">
                              {tour.duration}
                            </div>
                          </Show>
                        </div>

                        {/* Contenido del card */}
                        <div class="tour-content p-6">
                          <h3 class="tour-title text-lg font-bold mb-2 line-clamp-2 hover:text-opacity-80 transition-colors">
                            <a 
                              href={getTourUrl(tour)}
                              class="text-gray-900 hover:text-gray-700 transition-colors"
                            >
                              {tour.title}
                            </a>
                          </h3>

                          <Show when={tour.excerpt}>
                            <p class="tour-excerpt text-gray-600 text-sm mb-4 line-clamp-3">
                              {tour.excerpt}
                            </p>
                          </Show>

                          {/* Meta información */}
                          <div class="tour-meta flex items-center justify-between text-sm text-gray-500 mb-4">
                            <Show when={tour.location}>
                              <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {tour.location}
                              </span>
                            </Show>

                            <Show when={tour.difficulty}>
                              <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                {tour.difficulty}
                              </span>
                            </Show>
                          </div>

                          {/* Botón CTA */}
                          <a
                            href={getTourUrl(tour)}
                            class="tour-cta-btn inline-flex items-center justify-center w-full px-4 py-2 rounded-lg text-white font-medium transition-all duration-300 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2"
                            style={{ 
                              "background-color": accentColor,
                              "focus:ring-color": accentColor 
                            }}
                          >
                            {getTranslation("View Tour")}
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                          </a>
                        </div>
                      </div>
                    </article>
                  )}
                </For>
              </div>
            </div>

            {/* Indicadores (dots) */}
            <Show when={showDots && tours.length > slidesToShow}>
              <div class="carousel-dots flex justify-center space-x-2 mt-6">
                <For each={Array(totalSlides()).fill().map((_, i) => i)}>
                  {(dotIndex) => (
                    <button
                      type="button"
                      class="carousel-dot w-3 h-3 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2"
                      style={{
                        "background-color": currentSlide() === dotIndex ? accentColor : "#D1D5DB",
                        transform: currentSlide() === dotIndex ? "scale(1.2)" : "scale(1)",
                        "focus:ring-color": accentColor,
                      }}
                      onClick={() => goToSlide(dotIndex)}
                      aria-label={`${getTranslation("Go to slide")} ${dotIndex + 1}`}
                    />
                  )}
                </For>
              </div>
            </Show>
          </div>
        </div>
      </section>
    </>
  );
};

export default ToursCarousel;