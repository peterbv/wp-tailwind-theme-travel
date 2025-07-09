// src/public/js/components/solid/SolidGoogleReviews.jsx
import { createSignal, createEffect, onMount, For, Show } from "solid-js";
import { __ } from "../../solid-core";

/**
 * Componente elegante de reseñas de Google para sitios de spa y bienestar
 * Diseño mejorado con enfoque en elegancia y relajación
 * Con soporte completo para internacionalización
 *
 * @param {Object} props Propiedades del componente
 * @returns {JSX.Element} Componente Solid
 */
const SolidGoogleReviews = (props) => {
  // Propiedades con valores por defecto
  const {
    title = __("What Our Clients Say", "wp-tailwind-blocks"),
    subtitle = __("Testimonials", "wp-tailwind-blocks"),
    description = __(
      "Discover the experiences of those who have already enjoyed our services",
      "wp-tailwind-blocks"
    ),
    reviews = [],
    placeInfo = {},
    displayName = true,
    displayAvatar = true,
    displayRating = true,
    displayDate = true,
    displayRole = true,
    clientRole = __("Client", "wp-tailwind-blocks"),
    autoplay = true,
    autoplaySpeed = 6000,
    backgroundColor = "#F9F5F2", // Color de fondo suave y cálido
    textColor = "#5D534F", // Color de texto más cálido que el negro puro
    accentColor = "#D4B254", // Dorado elegante
    secondaryColor = "#8BAB8D", // Verde salvia para elementos secundarios
    carouselType = "slide",
    ajaxUrl = "",
    nonce = "",
    placeId = "",
    apiKey = "",
    reviewCount = 5,
    minRating = 4,
    isDynamic = false,
  } = props;

  // Función para obtener traducciones específicas de este componente
  const getTranslation = (text, domain = "wptbt-google-reviews-block") => {
    // Buscar primero en traducciones específicas del componente
    const componentTranslations = window.wptbtI18n_google_reviews || {};
    if (componentTranslations[text]) {
      return componentTranslations[text];
    }

    // Si no se encuentra, usar la función __ general
    return __(text, domain);
  };

  // Estados
  const [currentSlide, setCurrentSlide] = createSignal(0);
  const [reviewData, setReviewData] = createSignal(reviews || []);
  const [placeData, setPlaceData] = createSignal(placeInfo || {});
  const [isLoading, setIsLoading] = createSignal(isDynamic);
  const [error, setError] = createSignal(null);
  const [reviewPairs, setReviewPairs] = createSignal([]);
  const [totalSlides, setTotalSlides] = createSignal(0);
  const [autoplayInterval, setAutoplayInterval] = createSignal(null);
  const [isHovering, setIsHovering] = createSignal(false);
  const [activeQuote, setActiveQuote] = createSignal(null);

  // Gestión del autoplay
  const stopAutoplay = () => {
    if (autoplayInterval()) {
      clearInterval(autoplayInterval());
      setAutoplayInterval(null);
    }
  };

  const startAutoplay = () => {
    if (autoplay && !autoplayInterval() && !isHovering()) {
      const interval = setInterval(() => {
        nextSlide();
      }, autoplaySpeed);
      setAutoplayInterval(interval);
    }
  };

  // Navegación de slides con efecto mejorado
  const nextSlide = () => {
    setCurrentSlide((prev) => (prev + 1) % totalSlides());
  };

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev - 1 + totalSlides()) % totalSlides());
  };

  const goToSlide = (index) => {
    setCurrentSlide(index);
  };

  // Cargar reseñas dinámicamente desde AJAX
  const loadReviewsFromAjax = async () => {
    if (!placeId || !apiKey) {
      console.warn(
        getTranslation(
          "Google Reviews: Place ID and API Key are required to load dynamic reviews",
          "wp-tailwind-blocks"
        )
      );
      setIsLoading(false);
      return;
    }

    try {
      // Crear FormData para la petición
      const formData = new FormData();
      formData.append("action", "get_google_reviews");
      formData.append("nonce", nonce);
      formData.append("place_id", placeId);
      formData.append("api_key", apiKey);
      formData.append("review_count", reviewCount);
      formData.append("min_rating", minRating);

      // Hacer la petición AJAX
      const response = await fetch(ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        body: formData,
      });

      if (!response.ok) {
        throw new Error(
          `${getTranslation("Error in response", "wp-tailwind-blocks")}: ${
            response.status
          } ${response.statusText}`
        );
      }

      const data = await response.json();

      if (data.success && data.data.reviews) {
        setReviewData(data.data.reviews);
        setPlaceData(data.data.place_info);
      } else {
        throw new Error(
          getTranslation("Invalid response format", "wp-tailwind-blocks")
        );
      }
    } catch (err) {
      console.error(
        getTranslation("Error loading reviews:", "wp-tailwind-blocks"),
        err
      );
      setError(
        getTranslation(
          "Unable to load reviews. Please try again later.",
          "wp-tailwind-blocks"
        )
      );
    } finally {
      setIsLoading(false);
    }
  };

  // Agrupar reseñas en pares para mostrar dos por slide
  const groupReviewsInPairs = (reviews) => {
    const pairs = [];
    for (let i = 0; i < reviews.length; i += 2) {
      if (i + 1 < reviews.length) {
        pairs.push([reviews[i], reviews[i + 1]]);
      } else {
        pairs.push([reviews[i]]);
      }
    }
    return pairs;
  };

  // Efectos y eventos del ciclo de vida
  onMount(() => {
    // Cargar reseñas dinámicamente si es necesario
    if (isDynamic) {
      loadReviewsFromAjax();
    }

    // Iniciar autoplay si está habilitado
    if (autoplay) {
      startAutoplay();
    }

    // Limpiar el intervalo cuando el componente se desmonta
    return () => {
      if (autoplayInterval()) {
        clearInterval(autoplayInterval());
      }
    };
  });

  // Efecto para actualizar los pares de reseñas cuando cambian las reseñas
  createEffect(() => {
    const pairs = groupReviewsInPairs(reviewData());
    setReviewPairs(pairs);
    setTotalSlides(pairs.length);
  });

  // Reiniciar autoplay cuando isHovering cambia
  createEffect(() => {
    if (!isHovering() && autoplay) {
      startAutoplay();
    }
  });

  // Renderizar estrellas de calificación con diseño personalizado
  const renderStars = (rating) => {
    const stars = [];
    for (let i = 1; i <= 5; i++) {
      stars.push(
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 transition-transform duration-300 inline-block"
          style={{
            transform: i <= activeQuote() ? "scale(1.2)" : "scale(1)",
            "transform-origin": "center",
            fill: i <= rating ? accentColor : "#E9E2D8",
          }}
          viewBox="0 0 24 24"
        >
          <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
        </svg>
      );
    }
    return stars;
  };

  // Renderizar un avatar de usuario con estilo mejorado
  const renderAvatar = (review, index) => {
    const isActive = index === activeQuote();

    if (review.profile_photo_url) {
      return (
        <div
          class="relative"
          style={{
            transition: "all 0.3s ease",
            transform: isActive ? "scale(1.05)" : "scale(1)",
          }}
        >
          <div
            class="rounded-full overflow-hidden border-2 p-1"
            style={{ "border-color": accentColor }}
          >
            <img
              src={review.profile_photo_url}
              alt={review.author_name}
              class="w-16 h-16 object-cover rounded-full"
              onError={(e) => {
                e.target.style.display = "none";
                e.target.nextElementSibling.style.display = "flex";
              }}
            />
          </div>
          <div
            class="hidden absolute inset-0 items-center justify-center rounded-full border-2 p-1"
            style={{
              "border-color": accentColor,
              "background-color": secondaryColor,
            }}
          >
            <span class="text-3xl fancy-text font-bold text-white">
              {review.author_name ? review.author_name.charAt(0) : "?"}
            </span>
          </div>
        </div>
      );
    } else {
      return (
        <div
          class="w-16 h-16 flex items-center justify-center rounded-full border-2 p-1"
          style={{
            "border-color": accentColor,
            "background-color": secondaryColor,
            transition: "all 0.3s ease",
            transform: isActive ? "scale(1.05)" : "scale(1)",
          }}
        >
          <span class="text-3xl fancy-text font-bold text-white">
            {review.author_name ? review.author_name.charAt(0) : "?"}
          </span>
        </div>
      );
    }
  };

  // Estilo para la transición de los slides
  const getSlideStyle = () => {
    return {
      transform: `translateX(-${currentSlide() * 100}%)`,
      transition: `transform 0.8s cubic-bezier(0.4, 0, 0.2, 1)`,
    };
  };

  // Diseño actualizado para testimonios de spa y masajes
  return (
    <div
      class="solid-google-reviews-container w-full py-16 md:py-24 overflow-hidden"
      style={{
        "background-color": backgroundColor,
        color: textColor,
        "background-image": `
          radial-gradient(circle at 10% 20%, rgba(138, 171, 141, 0.05) 0%, rgba(138, 171, 141, 0) 20%),
          radial-gradient(circle at 90% 80%, rgba(212, 178, 84, 0.07) 0%, rgba(212, 178, 84, 0) 20%)
        `,
      }}
      onMouseEnter={() => setIsHovering(true)}
      onMouseLeave={() => setIsHovering(false)}
    >
      {/* Elementos decorativos flotantes */}
      <div
        class="absolute -left-16 top-1/4 w-64 h-64 opacity-10 pointer-events-none rounded-full"
        style={{ "background-color": secondaryColor }}
      ></div>
      <div
        class="absolute -right-16 bottom-1/4 w-48 h-48 opacity-10 pointer-events-none rounded-full"
        style={{ "background-color": accentColor }}
      ></div>

      <div class="container mx-auto px-4 relative">
        {/* Encabezado de la sección */}
        <div class="text-center mb-16 relative">
          {/* Icono decorativo */}
          <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2 opacity-10 pointer-events-none">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="120"
              height="120"
              viewBox="0 0 24 24"
              fill={secondaryColor}
              class="transform rotate-12"
            >
              <path d="M19.5 4c-.276 0-.5.224-.5.5v15c0 .276.224.5.5.5h2c.276 0 .5-.224.5-.5v-15c0-.276-.224-.5-.5-.5h-2zm-17 0c-.276 0-.5.224-.5.5v15c0 .276.224.5.5.5h2c.276 0 .5-.224.5-.5v-15c0-.276-.224-.5-.5-.5h-2zm13.5 2c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm-10 0c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm10 7c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm-10 0c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm10 7c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm-10 0c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2z" />
            </svg>
          </div>

          <span
            class="block text-lg italic font-medium mb-2"
            style={{ color: accentColor }}
          >
            {subtitle}
          </span>

          <div class="relative inline-block">
            <h2 class="text-3xl md:text-4xl lg:text-5xl fancy-text font-medium mb-4">
              {title}
            </h2>
            <div
              class="absolute -bottom-2 left-1/2 w-24 h-0.5 transform -translate-x-1/2"
              style={{ "background-color": accentColor }}
            >
              <div
                class="absolute left-1/2 top-1/2 w-2 h-2 rounded-full -translate-x-1/2 -translate-y-1/2"
                style={{ "background-color": accentColor }}
              ></div>
            </div>
          </div>

          <Show when={description}>
            <p class="text-xl md:text-2xl fancy-text font-light mt-8 max-w-2xl mx-auto italic opacity-80">
              {description}
            </p>
          </Show>

          {/* Elemento decorativo */}
          <div class="absolute left-1/2 bottom-0 transform -translate-x-1/2 translate-y-full">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke={accentColor}
              stroke-width="1"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M12 22V2M2 12h20M17 7l-5 5-5-5M17 17l-5-5-5 5"
                class="opacity-40"
              />
            </svg>
          </div>
        </div>

        {/* Estados de carga y error */}
        <Show when={isLoading()}>
          <div class="flex flex-col justify-center items-center p-8 h-64">
            <div class="relative w-16 h-16">
              <div class="absolute top-0 left-0 w-full h-full border-4 border-gray-200 rounded-full"></div>
              <div
                class="absolute top-0 left-0 w-full h-full border-4 rounded-full animate-spin"
                style={{
                  "border-color": `${accentColor} transparent transparent transparent`,
                  "animation-duration": "1.5s",
                }}
              ></div>
            </div>
            <span
              class="mt-4 text-lg fancy-text italic"
              style={{ color: textColor }}
            >
              {getTranslation("Loading testimonials...", "wp-tailwind-blocks")}
            </span>
          </div>
        </Show>

        <Show when={error()}>
          <div class="bg-red-50 border border-red-100 text-red-700 p-6 rounded-lg shadow-sm mb-8 fancy-text italic text-center">
            {error()}
          </div>
        </Show>

        {/* Carrusel de testimonios rediseñado */}
        <Show when={!isLoading() && !error() && reviewPairs().length > 0}>
          <div class="solid-reviews-carousel mx-auto max-w-6xl relative mt-12">
            {/* Comillas decorativas grandes */}
            <div class="absolute -left-8 top-0 opacity-10 pointer-events-none transform -translate-y-1/2">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="120"
                height="120"
                viewBox="0 0 24 24"
                fill={accentColor}
              >
                <path d="M13 14.725c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275zm-13 0c0-5.141 3.892-10.519 10-11.725l.984 2.126c-2.215.835-4.163 3.742-4.38 5.746 2.491.392 4.396 2.547 4.396 5.149 0 3.182-2.584 4.979-5.199 4.979-3.015 0-5.801-2.305-5.801-6.275z" />
              </svg>
            </div>

            {/* Contenedor del carrusel con efecto de tarjeta */}
            <div
              class="overflow-hidden rounded-xl shadow-xl relative"
              style={{
                "box-shadow":
                  "0 15px 35px rgba(0,0,0,0.05), 0 5px 15px rgba(0,0,0,0.03)",
                "background-color": "rgba(255,255,255,0.9)",
                "backdrop-filter": "blur(10px)",
              }}
            >
              <div class="flex transition-transform" style={getSlideStyle()}>
                <For each={reviewPairs()}>
                  {(pair, pairIndex) => (
                    <div class="min-w-full p-8">
                      {/* Grid de dos columnas para los testimonios */}
                      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <For each={pair}>
                          {(review, index) => {
                            const globalIndex = pairIndex() * 2 + index();
                            return (
                              <div
                                class="review-card bg-white rounded-xl overflow-hidden h-full relative group"
                                style={{
                                  border: "1px solid rgba(0,0,0,0.05)",
                                  "box-shadow": "0 4px 16px rgba(0,0,0,0.04)",
                                  transform: `translateY(0)`,
                                  transition: "all 0.4s ease-out",
                                }}
                                onMouseEnter={() => setActiveQuote(globalIndex)}
                                onMouseLeave={() => setActiveQuote(null)}
                              >
                                {/* Fondo de la tarjeta con gradiente */}
                                <div
                                  class="absolute inset-0 opacity-5 pointer-events-none"
                                  style={{
                                    background: `radial-gradient(circle at top right, ${accentColor}, transparent 70%)`,
                                    "z-index": "0",
                                  }}
                                ></div>

                                {/* Comillas decorativas */}
                                <div
                                  class="absolute top-4 right-4 text-4xl fancy-text opacity-20 leading-none"
                                  style={{ color: accentColor }}
                                >
                                  "
                                </div>

                                {/* Tarjeta interior */}
                                <div class="relative p-6 flex flex-col h-full z-10">
                                  {/* Cabecera del testimonio */}
                                  <div class="flex items-start mb-4">
                                    <Show when={displayAvatar}>
                                      <div class="mr-4">
                                        {renderAvatar(review, globalIndex)}
                                      </div>
                                    </Show>

                                    <div class="flex-1 pt-2">
                                      <Show when={displayName}>
                                        <h3
                                          class="text-xl fancy-text font-medium mb-1 transition-all"
                                          style={{
                                            color: textColor,
                                            "text-shadow":
                                              globalIndex === activeQuote()
                                                ? `0 0 1px ${accentColor}33`
                                                : "none",
                                          }}
                                        >
                                          {review.author_name}
                                        </h3>
                                      </Show>

                                      <Show when={displayRole}>
                                        <p class="text-sm opacity-60 italic mb-1">
                                          {clientRole}
                                        </p>
                                      </Show>

                                      <Show
                                        when={displayRating && review.rating}
                                      >
                                        <div class="flex mt-1">
                                          {renderStars(review.rating)}
                                        </div>
                                      </Show>
                                    </div>
                                  </div>

                                  {/* Línea separadora elegante */}
                                  <div class="w-full h-px my-3 relative overflow-hidden">
                                    <div
                                      class="absolute inset-0 opacity-20"
                                      style={{
                                        "background-color": accentColor,
                                      }}
                                    ></div>
                                    <div
                                      class="absolute left-0 top-0 h-full w-1/3 transition-all duration-500"
                                      style={{
                                        "background-color": accentColor,
                                        transform:
                                          globalIndex === activeQuote()
                                            ? "translateX(200%)"
                                            : "translateX(0)",
                                      }}
                                    ></div>
                                  </div>

                                  {/* Contenido del testimonio */}
                                  <div class="review-content flex-grow mb-4 relative overflow-hidden">
                                    <p
                                      class="text-base font-light leading-relaxed relative italic"
                                      style={{ color: textColor }}
                                    >
                                      {review.text}
                                    </p>
                                  </div>

                                  {/* Pie del testimonio */}
                                  <div class="mt-auto text-right">
                                    <Show
                                      when={
                                        displayDate &&
                                        review.relative_time_description
                                      }
                                    >
                                      <p class="text-xs opacity-50 mt-2 italic">
                                        {review.relative_time_description}
                                      </p>
                                    </Show>
                                  </div>
                                </div>
                              </div>
                            );
                          }}
                        </For>

                        {/* Si solo hay un testimonio en este par, añadir decoración */}
                        <Show when={pair.length === 1}>
                          <div class="hidden lg:flex items-center justify-center opacity-10">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              width="160"
                              height="160"
                              viewBox="0 0 24 24"
                              fill={accentColor}
                              class="transform rotate-12"
                            >
                              <path d="M19.5 4c-.276 0-.5.224-.5.5v15c0 .276.224.5.5.5h2c.276 0 .5-.224.5-.5v-15c0-.276-.224-.5-.5-.5h-2zm-17 0c-.276 0-.5.224-.5.5v15c0 .276.224.5.5.5h2c.276 0 .5-.224.5-.5v-15c0-.276-.224-.5-.5-.5h-2zm13.5 2c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm-10 0c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm10 7c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm-10 0c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm10 7c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2zm-10 0c0 1.103-.897 2-2 2s-2-.897-2-2 .897-2 2-2 2 .897 2 2z" />
                            </svg>
                          </div>
                        </Show>
                      </div>
                    </div>
                  )}
                </For>
              </div>
            </div>

            {/* Paginación estilizada */}
            <div class="flex justify-center mt-10 space-x-3">
              <For each={Array(totalSlides()).fill(0)}>
                {(_, index) => (
                  <button
                    class="w-3 h-3 rounded-full transition-all duration-300 relative"
                    style={{
                      "background-color":
                        currentSlide() === index() ? accentColor : "#E9E2D8",
                      transform:
                        currentSlide() === index() ? "scale(1.2)" : "scale(1)",
                      opacity: currentSlide() === index() ? "1" : "0.7",
                    }}
                    onClick={() => goToSlide(index())}
                    aria-label={
                      getTranslation(
                        "Go to testimonial",
                        "wp-tailwind-blocks"
                      ) +
                      " " +
                      (index() + 1)
                    }
                  >
                    <span
                      class="absolute inset-0 rounded-full animate-ping"
                      style={{
                        "background-color": accentColor,
                        "animation-duration": "1.5s",
                        "animation-iteration-count": "infinite",
                        opacity: currentSlide() === index() ? "0.4" : "0",
                        "animation-delay": "0.3s",
                      }}
                    ></span>
                  </button>
                )}
              </For>
            </div>

            {/* Botones de navegación elegantes */}
            <div class="hidden md:flex justify-between items-center absolute -bottom-2 left-0 right-0 translate-y-full pt-8">
              <button
                class="w-12 h-12 rounded-full bg-white shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center group border border-gray-100"
                onClick={() => {
                  stopAutoplay();
                  prevSlide();
                }}
                aria-label={getTranslation(
                  "Previous testimonial",
                  "wp-tailwind-blocks"
                )}
                style={{
                  transform: "translateY(0px)",
                  transition: "all 0.3s ease",
                }}
                onMouseEnter={(e) => {
                  e.currentTarget.style.transform = "translateY(-3px)";
                  e.currentTarget.style.boxShadow =
                    "0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)";
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.transform = "translateY(0px)";
                  e.currentTarget.style.boxShadow = "";
                }}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 transition-transform duration-300 group-hover:-translate-x-1"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke={textColor}
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 19l-7-7 7-7"
                  />
                </svg>
              </button>

              <div class="text-center">
                {/* https://g.page/r/${placeId}/review */}
                <a
                  href={`https://www.google.com/maps/place/MASSAGES+CUSCO+%7C+MYSTICAL+TERRA+SPA/@-13.516813,-71.9782094,19.17z/data=!4m15!1m8!3m7!1s0x916dd6736815a6bf:0x6bba173f1e0a44a8!2sTriunfo+338,+Cusco+08000,+Per%C3%BA!3b1!8m2!3d-13.5165525!4d-71.9772607!16s%2Fg%2F11q2x8b8xl!3m5!1s0x916dd7d179c38439:0x7e61b655c44a7559!8m2!3d-13.5167063!4d-71.9771131!16s%2Fg%2F11vf1y25c9?hl=es-ES&entry=ttu&g_ep=EgoyMDI1MDMyNC4wIKXMDSoASAFQAw%3D%3D`}
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 text-sm font-medium py-2 px-4 rounded-full transition-all duration-300 group"
                  style={{
                    "background-color": "rgba(255,255,255,0.8)",
                    border: `1px solid ${accentColor}40`,
                    color: textColor,
                    "box-shadow": "0 2px 10px rgba(0,0,0,0.05)",
                  }}
                  onMouseEnter={(e) => {
                    e.currentTarget.style.backgroundColor = accentColor;
                    e.currentTarget.style.color = "white";
                    e.currentTarget.style.boxShadow =
                      "0 4px 20px rgba(0,0,0,0.1)";
                  }}
                  onMouseLeave={(e) => {
                    e.currentTarget.style.backgroundColor =
                      "rgba(255,255,255,0.8)";
                    e.currentTarget.style.color = textColor;
                    e.currentTarget.style.boxShadow =
                      "0 2px 10px rgba(0,0,0,0.05)";
                  }}
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 transition-all duration-300 group-hover:scale-110"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                  >
                    <path d="M12 .587l3.668 7.568 8.332 1.151-6.064 5.828 1.48 8.279-7.416-3.967-7.417 3.967 1.481-8.279-6.064-5.828 8.332-1.151z" />
                  </svg>
                  {getTranslation("Leave your review", "wp-tailwind-blocks")}
                </a>
              </div>

              <button
                class="w-12 h-12 rounded-full bg-white shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center group border border-gray-100"
                onClick={() => {
                  stopAutoplay();
                  nextSlide();
                }}
                aria-label={getTranslation(
                  "Next testimonial",
                  "wp-tailwind-blocks"
                )}
                style={{
                  transform: "translateY(0px)",
                  transition: "all 0.3s ease",
                }}
                onMouseEnter={(e) => {
                  e.currentTarget.style.transform = "translateY(-3px)";
                  e.currentTarget.style.boxShadow =
                    "0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)";
                }}
                onMouseLeave={(e) => {
                  e.currentTarget.style.transform = "translateY(0px)";
                  e.currentTarget.style.boxShadow = "";
                }}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke={textColor}
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                  />
                </svg>
              </button>
            </div>
          </div>

          {/* Nota de Google Reviews */}
          <div class="flex justify-center mt-4 opacity-80">
            <a
              href="https://www.google.com/business/"
              target="_blank"
              rel="noopener noreferrer"
              class="flex items-center gap-2 text-xs text-gray-500 transition hover:text-gray-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="14"
                height="14"
                fill="#4285F4"
              >
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  fill="#34A853"
                />
                <path
                  d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                  fill="#FBBC05"
                />
                <path
                  d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  fill="#EA4335"
                />
              </svg>
              {getTranslation("Google Reviews", "wp-tailwind-blocks")}
            </a>
          </div>
        </Show>
      </div>
    </div>
  );
};

export default SolidGoogleReviews;
