// src/public/js/components/solid/DestinationsCarousel.jsx
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
 * Componente de Carousel de Destinos con Solid.js
 * Carousel infinito con animación automática de derecha a izquierda
 */
const DestinationsCarousel = (props) => {
  // Función para obtener traducciones específicas de este componente
  const getTranslation = (text, domain = "wptbt-destinations-carousel") => {
    const componentTranslations = window.wptbtI18n_destinations_carousel || {};
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
    destinations = [],
    autoplaySpeed = 3000, // Velocidad del autoplay en ms
    showDots = true,
    showArrows = true,
    pauseOnHover = true,
    infinite = true,
    slidesToShow = 3, // Número de destinos visibles a la vez
    backgroundColor = "#F8FAFC",
    textColor = "#1F2937",
    accentColor = "#DC2626",
    secondaryColor = "#059669",
    fullWidth = false,
    animationDirection = "left", // "left" | "right"
    // Nuevas props para SEO
    carouselId = "destinations-carousel",
    baseUrl = window.location.origin + window.location.pathname,
  } = props;

  // Estados
  const [currentSlide, setCurrentSlide] = createSignal(0);
  const [isAutoPlaying, setIsAutoPlaying] = createSignal(true);
  const [autoplayInterval, setAutoplayInterval] = createSignal(null);
  const [isHovering, setIsHovering] = createSignal(false);
  const [isLoaded, setIsLoaded] = createSignal(false);
  const [destinationsLoaded, setDestinationsLoaded] = createSignal(0);
  const [hoveredDestination, setHoveredDestination] = createSignal(null);
  const [isTransitioning, setIsTransitioning] = createSignal(false);

  // Referencias
  let carouselRef;

  // Lista circular de destinos para efecto infinito
  const [circularDestinations, setCircularDestinations] = createSignal([]);
  
  // Inicializar lista circular
  const initializeCircularDestinations = () => {
    if (!infinite || destinations.length === 0) return destinations;
    // Duplicar la lista para tener suficientes elementos para el efecto circular
    const multiplier = Math.max(3, Math.ceil((slidesToShow + 2) / destinations.length));
    const result = [];
    for (let i = 0; i < multiplier; i++) {
      result.push(...destinations.map((destination, index) => ({
        ...destination,
        uniqueId: `${destination.id || index}-${i}` // ID único para evitar conflictos
      })));
    }
    return result;
  };
  
  // Calcular slides totales
  const totalSlides = () => {
    if (!infinite) {
      // En modo no infinito, cada card es un slide individual
      return Math.max(0, destinations.length - slidesToShow + 1);
    }
    return destinations.length;
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
        
        const current = circularDestinations();
        const newList = [...current];
        // Mover el primer elemento al final
        const firstElement = newList.shift();
        if (firstElement) {
          newList.push(firstElement);
          setCircularDestinations(newList);
          // Reset la posición para mantener la continuidad visual sin doble movimiento
          setCurrentSlide(0);
        }
        setIsTransitioning(false);
      }, 500);
    } else {
      setCurrentSlide((prev) => Math.min(prev + 1, destinations.length - slidesToShow));
    }
  };

  const prevSlide = () => {
    // Prevenir múltiples transiciones simultáneas
    if (isTransitioning()) return;
    
    if (infinite) {
      setIsTransitioning(true);
      
      // Para ir hacia atrás, mover el último elemento al principio
      const current = circularDestinations();
      const newList = [...current];
      const lastElement = newList.pop();
      if (lastElement) {
        newList.unshift(lastElement);
        setCircularDestinations(newList);
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
    // Inicializar destinos circulares
    if (infinite && destinations.length > 0) {
      setCircularDestinations(initializeCircularDestinations());
      setCurrentSlide(0); // Empezar en posición 0 para el efecto infinito
    } else {
      setCircularDestinations(destinations);
    }
    
    if (destinations.length > slidesToShow) {
      startAutoplay();
    }
    setIsLoaded(true);
    setIsTransitioning(false);
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
    setDestinationsLoaded(prev => prev + 1);
  };

  // Obtener URL del destino
  const getDestinationUrl = (destination) => {
    return destination.link || destination.permalink || `${baseUrl}destinations/${destination.slug || destination.id}`;
  };

  // Estilo de transformación para el carousel
  const getCarouselTransform = () => {
    // Ancho de un solo card (no del grupo completo)
    const singleCardWidth = 100 / slidesToShow;
    
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

  // Calcular slides visibles responsivos
  const getResponsiveSlidesToShow = () => {
    const maxSlides = slidesToShow; // Respeta la configuración del usuario
    return {
      small: Math.min(maxSlides, 1), // 1 en móvil
      medium: Math.min(maxSlides, 3), // 3 en tablet
      large: maxSlides, // Completo en desktop
    };
  };

  const responsive = getResponsiveSlidesToShow();

  // Generar structured data para SEO
  const generateStructuredData = () => {
    if (destinations.length === 0) return null;

    const structuredData = {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "name": title || "Destinations Carousel",
      "description": description || "Explore amazing travel destinations",
      "numberOfItems": destinations.length,
      "itemListElement": destinations.map((destination, index) => ({
        "@type": "ListItem",
        "position": index + 1,
        "item": {
          "@type": "TouristDestination",
          "name": destination.name || "",
          "description": destination.description || "",
          "image": destination.image || "",
          "url": destination.link || `${baseUrl}#destination-${index}`,
          "tourCount": destination.tourCount || 0
        }
      }))
    };

    return JSON.stringify(structuredData);
  };

  return (
    <>
      <section
        id={carouselId}
        class={`destinations-carousel-component w-full py-8 md:py-12 relative ${
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
        {/* Structured Data para SEO */}
        <Show when={generateStructuredData()}>
          <script type="application/ld+json">
            {generateStructuredData()}
          </script>
        </Show>

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
          <div class="destinations-carousel-wrapper relative">
            {/* Botones de navegación */}
            <Show when={showArrows && destinations.length > slidesToShow}>
              <button
                type="button"
                class="carousel-prev absolute left-0 top-1/2 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white shadow-lg flex items-center justify-center transition-all duration-300 hover:shadow-xl hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2"
                style={{ "focus:ring-color": accentColor, border: `2px solid ${accentColor}20` }}
                onClick={prevSlide}
                aria-label={getTranslation("Previous destinations")}
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
                aria-label={getTranslation("Next destinations")}
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
                <For each={circularDestinations()}>
                  {(destination, index) => (
                    <article
                      class={`destination-card flex-shrink-0 px-3 transition-all duration-300`}
                      style={{
                        width: `${100 / slidesToShow}%`,
                        "min-width": `${100 / slidesToShow}%`,
                        "flex-shrink": "0",
                        transform: hoveredDestination() === index() ? "translateY(-4px)" : "translateY(0)",
                      }}
                      onMouseEnter={() => setHoveredDestination(index())}
                      onMouseLeave={() => setHoveredDestination(null)}
                    >
                      {/* Diseño minimalista inspirado en la imagen de referencia */}
                      <div class="destination-card-inner relative overflow-hidden rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl aspect-[4/3] cursor-pointer group">
                        
                        {/* Imagen de fondo completa */}
                        <div class="absolute inset-0">
                          <Show 
                            when={destination.image}
                            fallback={
                              <div class="w-full h-full bg-gradient-to-br from-gray-200 via-gray-300 to-gray-400 flex items-center justify-center">
                                <div class="text-center">
                                  <svg class="w-12 h-12 text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                  </svg>
                                  <p class="text-gray-600 text-sm font-medium">{destination.name || destination.title}</p>
                                </div>
                              </div>
                            }
                          >
                            <img
                              src={destination.image}
                              alt={destination.name || destination.title}
                              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                              loading="lazy"
                              onLoad={handleImageLoad}
                            />
                          </Show>
                          
                          {/* Overlay con gradiente sutil desde abajo */}
                          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                        </div>

                        {/* Contenido minimalista centrado - solo nombre del destino */}
                        <a href={getDestinationUrl(destination)} class="absolute inset-0 flex items-center justify-center text-center text-white z-10 group-hover:bg-black/10 transition-colors duration-300">
                          <h3 class="text-2xl md:text-3xl font-bold tracking-tight drop-shadow-lg">
                            {destination.name || destination.title}
                          </h3>
                        </a>
                      </div>
                    </article>
                  )}
                </For>
              </div>
            </div>

            {/* Indicadores (dots) - Solo para modo no infinito */}
            <Show when={showDots && !infinite && destinations.length > slidesToShow}>
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

export default DestinationsCarousel;