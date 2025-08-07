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
                      <div class="destination-card-inner relative overflow-hidden rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl h-80 cursor-pointer group">
                        
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
                          <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                          
                          {/* Badge flotante con animación */}
                          <Show when={destination.tourCount && destination.tourCount > 0}>
                            <div class="absolute top-5 right-5 transform transition-all duration-500 group-hover:scale-110">
                              <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-full shadow-lg backdrop-blur-sm flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-xs font-bold tracking-wide">
                                  {destination.tourCount} {destination.tourCount === 1 ? getTranslation("Tour") : getTranslation("Tours")}
                                </span>
                              </div>
                            </div>
                          </Show>

                          {/* Botón play/explore flotante en el centro */}
                          <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <div class="w-16 h-16 bg-white/95 rounded-full flex items-center justify-center shadow-2xl transform scale-75 group-hover:scale-100 transition-all duration-300 backdrop-blur-sm">
                              <svg class="w-6 h-6 text-purple-600 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                              </svg>
                            </div>
                          </div>
                        </div>

                        {/* Contenido con diseño premium */}
                        <div class="destination-content p-6 h-40 flex flex-col justify-between relative">
                          {/* Elemento decorativo */}
                          <div class="absolute -top-6 left-6 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-500"></div>
                          
                          <div class="flex-1 relative z-10">
                            {/* Título con tipografía premium */}
                            <h3 class="destination-title text-xl font-bold mb-3 text-gray-900 leading-tight tracking-tight">
                              <a 
                                href={getDestinationUrl(destination)}
                                class="hover:text-transparent hover:bg-clip-text hover:bg-gradient-to-r hover:from-purple-600 hover:to-pink-600 transition-all duration-300"
                              >
                                {destination.name || destination.title}
                              </a>
                            </h3>

                            {/* Descripción con fade */}
                            <Show when={destination.description}>
                              <p class="destination-description text-gray-600 text-sm leading-relaxed line-clamp-2 mb-4 group-hover:text-gray-700 transition-colors duration-300">
                                {destination.description}
                              </p>
                            </Show>

                            {/* Rating/stars simulado */}
                            <div class="flex items-center space-x-1 mb-3">
                              <div class="flex space-x-1">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                              </div>
                              <span class="text-xs text-gray-500 font-medium">4.8</span>
                            </div>
                          </div>

                          {/* Botón CTA con gradiente y efectos */}
                          <div class="destination-cta">
                            <a
                              href={getDestinationUrl(destination)}
                              class="group/btn relative inline-flex items-center justify-center w-full px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold text-sm transition-all duration-500 hover:shadow-xl hover:shadow-purple-500/25 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 overflow-hidden"
                            >
                              <div class="absolute inset-0 bg-gradient-to-r from-purple-700 to-pink-700 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></div>
                              <span class="relative z-10 mr-2">{getTranslation("Discover Paradise")}</span>
                              <svg class="relative z-10 w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                              </svg>
                              
                              {/* Efecto de brillo animado */}
                              <div class="absolute inset-0 -skew-x-12 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                            </a>
                          </div>
                        </div>
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