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
  const [isTransitioning, setIsTransitioning] = createSignal(false);
  const [currentSlidesToShow, setCurrentSlidesToShow] = createSignal(slidesToShow);

  // Referencias
  let carouselRef;

  // Lista circular de tours para efecto infinito
  const [circularTours, setCircularTours] = createSignal([]);
  
  // Inicializar lista circular
  const initializeCircularTours = () => {
    if (!infinite || tours.length === 0) return tours;
    // Duplicar la lista para tener suficientes elementos para el efecto circular
    const multiplier = Math.max(3, Math.ceil((slidesToShow + 2) / tours.length));
    const result = [];
    for (let i = 0; i < multiplier; i++) {
      result.push(...tours.map((tour, index) => ({
        ...tour,
        uniqueId: `${tour.id || index}-${i}` // ID único para evitar conflictos
      })));
    }
    return result;
  };
  
  // Calcular slides totales
  const totalSlides = () => {
    if (!infinite) {
      // En modo no infinito, cada card es un slide individual
      return Math.max(0, tours.length - currentSlidesToShow() + 1);
    }
    return tours.length;
  };

  // Autoplay
  const startAutoplay = () => {
    if (!isAutoPlaying() || autoplaySpeed <= 0) return;
    
    // Limpiar cualquier intervalo previo antes de crear uno nuevo
    stopAutoplay();
    
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

  // Navegación simplificada - movimiento de 1 card
  const nextSlide = () => {
    // Prevenir múltiples transiciones simultáneas
    if (isTransitioning()) return;
    
    if (infinite) {
      setIsTransitioning(true);
      setCurrentSlide((prev) => prev + 1);
      
      // Después de la transición, reciclar sin cambiar currentSlide
      setTimeout(() => {
        // Verificar nuevamente el estado para evitar condiciones de carrera
        if (!isTransitioning()) return;
        
        const current = circularTours();
        const newList = [...current];
        // Mover el primer elemento al final
        const firstElement = newList.shift();
        if (firstElement) {
          newList.push(firstElement);
          setCircularTours(newList);
          // Reset la posición para mantener la continuidad visual sin doble movimiento
          setCurrentSlide(0);
        }
        setIsTransitioning(false);
      }, 500);
    } else {
      setCurrentSlide((prev) => Math.min(prev + 1, tours.length - currentSlidesToShow()));
    }
  };

  const prevSlide = () => {
    // Prevenir múltiples transiciones simultáneas
    if (isTransitioning()) return;
    
    if (infinite) {
      setIsTransitioning(true);
      
      // Para ir hacia atrás, mover el último elemento al principio
      const current = circularTours();
      const newList = [...current];
      const lastElement = newList.pop();
      if (lastElement) {
        newList.unshift(lastElement);
        setCircularTours(newList);
        setCurrentSlide(0); // Reset a posición inicial
      }
      
      setTimeout(() => {
        setIsTransitioning(false);
      }, 50);
    } else {
      setCurrentSlide((prev) => Math.max(prev - 1, 0));
    }
  };

  const goToSlide = (index) => {
    setCurrentSlide(index);
  };

  // Efectos
  onMount(() => {
    // Inicializar responsive
    updateSlidesToShow();
    
    // Agregar listener para resize
    const handleResize = () => updateSlidesToShow();
    window.addEventListener('resize', handleResize);
    
    // Inicializar tours circulares
    if (infinite && tours.length > 0) {
      setCircularTours(initializeCircularTours());
      setCurrentSlide(0); // Empezar en posición 0 para el efecto infinito
    } else {
      setCircularTours(tours);
    }
    
    if (tours.length > currentSlidesToShow()) {
      startAutoplay();
    }
    setIsLoaded(true);
    setIsTransitioning(false);
    
    // Cleanup en onCleanup
    onCleanup(() => {
      window.removeEventListener('resize', handleResize);
    });
  });

  onCleanup(() => {
    stopAutoplay();
  });

  createEffect(() => {
    if (isHovering() && pauseOnHover) {
      stopAutoplay();
    } else if (!isHovering() && isAutoPlaying() && !isTransitioning()) {
      // Solo reanudar autoplay si no hay una transición en curso
      setTimeout(() => {
        if (!isHovering() && !isTransitioning()) {
          startAutoplay();
        }
      }, 100); // Pequeño delay para evitar conflictos
    }
  });

  // Manejar carga de imágenes
  const handleImageLoad = () => {
    setToursLoaded(prev => prev + 1);
  };

  // Obtener precio formateado con lógica de promociones
  const getFormattedPrice = (tour) => {
    const symbol = tour.currency === 'USD' ? '$' : (tour.currency === 'PEN' ? 'S/' : (tour.currency === 'EUR' ? '€' : 'Bs'));
    
    // Si no hay ningún precio
    if (!tour.price && !tour.price_promotion && !tour.price_international && !tour.price_national) {
      return getTranslation("Price on request");
    }
    
    // Si hay precio promocional y original, mostrar ambos
    if (tour.price_promotion && tour.price_original) {
      return {
        hasPromotion: true,
        promotional: `${symbol}${tour.price_promotion}`,
        original: `${symbol}${tour.price_original}`
      };
    }
    
    // Prioridad: promocional > nacional > internacional > precio por defecto
    let bestPrice = tour.price_promotion || tour.price_national || tour.price_international || tour.price;
    
    if (bestPrice) {
      return {
        hasPromotion: false,
        promotional: `${symbol}${bestPrice}`
      };
    }
    
    return getTranslation("Price on request");
  };

  // Obtener componente de precio formateado
  const getPriceComponent = (tour) => {
    const priceData = getFormattedPrice(tour);
    
    if (typeof priceData === 'string') {
      return <span>{priceData}</span>;
    }
    
    if (priceData.hasPromotion) {
      return (
        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
          <span class="text-base sm:text-lg font-bold">{priceData.promotional}</span>
          <span class="text-xs sm:text-sm line-through opacity-75">{priceData.original}</span>
        </div>
      );
    }
    
    return <span class="text-base sm:text-lg font-bold">{priceData.promotional}</span>;
  };

  // Obtener URL del tour
  const getTourUrl = (tour) => {
    return tour.permalink || `${baseUrl}tours/${tour.slug || tour.id}`;
  };

  // Estilo de transformación para el carousel
  const getCarouselTransform = () => {
    // Ancho de un solo card (no del grupo completo)
    const singleCardWidth = 100 / currentSlidesToShow();
    
    if (!infinite) {
      const translateValue = animationDirection === "left" 
        ? -currentSlide() * singleCardWidth
        : currentSlide() * singleCardWidth;
      
      return {
        transform: `translateX(${translateValue}%)`,
        transition: "transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)",
      };
    }
    
    // Para efecto infinito - mover un card a la vez
    const translateValue = animationDirection === "left" 
      ? -currentSlide() * singleCardWidth
      : currentSlide() * singleCardWidth;
    
    return {
      transform: `translateX(${translateValue}%)`,
      transition: isTransitioning() ? "transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)" : "none",
    };
  };

  // Función para detectar el tamaño de pantalla y actualizar slides
  const updateSlidesToShow = () => {
    const width = window.innerWidth;
    const maxSlides = slidesToShow; // Respeta la configuración del usuario
    
    let newSlidesToShow;
    if (width < 768) { // móvil
      newSlidesToShow = Math.min(maxSlides, 1);
    } else if (width < 1024) { // tablet
      newSlidesToShow = Math.min(maxSlides, 2);
    } else { // desktop
      newSlidesToShow = maxSlides;
    }
    
    if (newSlidesToShow !== currentSlidesToShow()) {
      setCurrentSlidesToShow(newSlidesToShow);
      // Reset currentSlide si es necesario
      if (currentSlide() > Math.max(0, tours.length - newSlidesToShow)) {
        setCurrentSlide(Math.max(0, tours.length - newSlidesToShow));
      }
    }
  };

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
        <div class="container mx-auto px-4 relative overflow-y-visible">
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
          <div class="tours-carousel-wrapper relative pt-3 pb-6">
            {/* Botones de navegación */}
            <Show when={showArrows && tours.length > currentSlidesToShow()}>
              <button
                type="button"
                class="carousel-prev absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white shadow-lg flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                style={{ "focus:ring-color": accentColor, border: `2px solid ${accentColor}20` }}
                onClick={prevSlide}
                aria-label={getTranslation("Previous tours")}
              >
                <svg
                  class="w-4 h-4 sm:w-5 sm:h-5"
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
                class="carousel-next absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white shadow-lg flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                style={{ "focus:ring-color": accentColor, border: `2px solid ${accentColor}20` }}
                onClick={nextSlide}
                aria-label={getTranslation("Next tours")}
              >
                <svg
                  class="w-4 h-4 sm:w-5 sm:h-5"
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
            <div class="carousel-track overflow-x-hidden overflow-y-visible mx-6 sm:mx-8 md:mx-12 py-3">
              <div
                class="carousel-slides flex transition-transform duration-600 ease-in-out"
                style={getCarouselTransform()}
                ref={carouselRef}
              >
                <For each={circularTours()}>
                  {(tour, index) => (
                    <article
                      class={`tour-card flex-shrink-0 px-2 sm:px-3 transition-all duration-500`}
                      style={{
                        width: `${100 / currentSlidesToShow()}%`,
                        "min-width": `${100 / currentSlidesToShow()}%`,
                        "flex-shrink": "0",
                        transform: hoveredTour() === index() ? "translateY(-4px)" : "translateY(0)",
                      }}
                      onMouseEnter={() => setHoveredTour(index())}
                      onMouseLeave={() => setHoveredTour(null)}
                      
                      // ALTERNATIVA SIN HOVER: Si el efecto sigue cortándose, reemplaza las líneas arriba con:
                      // style={{
                      //   width: `${100 / currentSlidesToShow()}%`,
                      //   "min-width": `${100 / currentSlidesToShow()}%`,
                      //   "flex-shrink": "0",
                      // }}
                    >
                      <div class="tour-card-inner bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl relative h-80 sm:h-96 md:h-[440px] lg:h-[500px]">
                        {/* Imagen del tour que ocupa todo el card */}
                        <div class="tour-image absolute inset-0 overflow-hidden">
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
                        </div>
                        
                        {/* Degradado de oscuro a transparente */}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                        
                        {/* Badge de precio especial */}
                        <Show when={tour.price_promotion && tour.price_original}>
                          <div
                            class="absolute top-4 right-4 px-3 py-1 rounded-full text-white font-semibold text-sm shadow-md z-10"
                            style={{ "background-color": accentColor }}
                          >
                            Special Price
                          </div>
                        </Show>

                        

                        {/* Contenido del card superpuesto */}
                        <div class="tour-content absolute bottom-0 left-0 right-0 p-6 z-10">
                          <h3 class="tour-title fancy-text text-lg font-bold mb-2 line-clamp-2 hover:text-opacity-80 transition-colors">
                            <a 
                              href={getTourUrl(tour)}
                              class="text-white hover:text-gray-200 transition-colors"
                            >
                              {tour.title}
                            </a>
                          </h3>

                          <Show when={tour.subtitle}>
                            <p class="tour-subtitle text-white/90 text-sm mb-4 line-clamp-3">
                              {tour.subtitle}
                            </p>
                          </Show>

                          {/* Meta información */}
                          <div class="tour-meta flex items-center justify-between text-sm text-white/80 mb-4">
                            <Show when={tour.location}>
                              <span class="flex items-center text-white/80">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {tour.location}
                              </span>
                            </Show>

                            <Show when={tour.difficulty}>
                              <span class="flex items-center text-white/80">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                {tour.difficulty}
                              </span>
                            </Show>
                          </div>

                          {/* Botón CTA y Precio */}
                          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                            <a
                              href={getTourUrl(tour)}
                              class="tour-cta-btn inline-flex items-center justify-center px-4 sm:px-6 py-2 rounded-lg text-white font-medium transition-all duration-300 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 text-sm sm:text-base"
                              style={{ 
                                "background-color": accentColor,
                                "focus:ring-color": accentColor 
                              }}
                            >
                              {getTranslation("View Tour")}
                              <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                              </svg>
                            </a>
                            
                            <Show when={tour.price || tour.price_promotion || tour.price_international || tour.price_national}>
                              <div class="text-white font-bold text-center sm:text-right">
                                {getPriceComponent(tour)}
                              </div>
                            </Show>
                          </div>
                        </div>
                      </div>
                    </article>
                  )}
                </For>
              </div>
            </div>

            {/* Indicadores (dots) - Solo para modo no infinito */}
            <Show when={showDots && !infinite && tours.length > currentSlidesToShow()}>
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