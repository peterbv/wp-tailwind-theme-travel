// src/public/js/components/solid/SolidGallery.jsx
import {
  createSignal,
  createEffect,
  onMount,
  For,
  Show,
  onCleanup,
} from "solid-js";
import { Portal } from "solid-js/web";
import { __ } from "../../solid-core";

/**
 * Componente de Galería con Solid.js - Optimizado para SEO
 * Diseñado especialmente para sitios de spa y bienestar
 */
const SolidGallery = (props) => {
  // Función para obtener traducciones específicas de este componente
  const getTranslation = (text, domain = "wptbt-gallery-block") => {
    const componentTranslations = window.wptbtI18n_gallery || {};
    if (componentTranslations[text]) {
      return componentTranslations[text];
    }
    return __(text, domain);
  };

  // Propiedades con valores por defecto optimizadas para agencia de viajes
  const {
    title = "",
    subtitle = "",
    description = "",
    images = [],
    columns = 3,
    maxVisibleImages = 12,
    displayMode = "grid", // 'grid', 'masonry', 'slider'
    hoverEffect = "zoom", // 'zoom', 'fade', 'slide', 'none'
    textColor = "#1F2937",
    accentColor = "#DC2626",
    secondaryColor = "#059669",
    fullWidth = false,
    enableLightbox = true,
    spacing = 16,
    imageSize = "medium_large",
    // Nuevas props para SEO
    galleryId = "gallery",
    baseUrl = window.location.origin + window.location.pathname,
    organizationName = "",
    websiteName = "",
  } = props;

  // Estados
  const [currentSlide, setCurrentSlide] = createSignal(0);
  const [totalSlides, setTotalSlides] = createSignal(0);
  const [autoplayInterval, setAutoplayInterval] = createSignal(null);
  const [isHovering, setIsHovering] = createSignal(false);
  const [lightboxOpen, setLightboxOpen] = createSignal(false);
  const [lightboxImage, setLightboxImage] = createSignal(null);
  const [isLoaded, setIsLoaded] = createSignal(false);
  const [imagesLoaded, setImagesLoaded] = createSignal(0);
  const [hoveredItem, setHoveredItem] = createSignal(null);
  const [lightboxVisible, setLightboxVisible] = createSignal(false);
  const [lightboxLoading, setLightboxLoading] = createSignal(false);
  const [lightboxTransitioning, setLightboxTransitioning] = createSignal(false);
  const [lightboxAnimation, setLightboxAnimation] = createSignal("fade-in");
  let lightboxTimerRef;

  // Referencias
  let sliderRef;

  // Calcular imágenes a mostrar y las restantes
  const visibleImages = () => images.slice(0, maxVisibleImages);
  const remainingCount = () => Math.max(0, images.length - maxVisibleImages);
  const hasMoreImages = () => remainingCount() > 0;

  // Generar URLs semánticas para las imágenes
  const getImageUrl = (image, index) => {
    if (image.permalink) return image.permalink;
    const slug = image.alt 
      ? image.alt.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
      : `image-${index + 1}`;
    return `${baseUrl}#${galleryId}-${slug}`;
  };

  // Generar datos estructurados JSON-LD para SEO
  const generateStructuredData = () => {
    const structuredData = {
      "@context": "https://schema.org",
      "@type": "ImageGallery",
      "name": title,
      "description": description,
      "numberOfItems": images.length,
      "image": images.map((image, index) => ({
        "@type": "ImageObject",
        "url": image.fullUrl || image.url,
        "thumbnail": image.url,
        "caption": image.caption || image.alt || `${title} - Image ${index + 1}`,
        "description": image.description || image.alt || `Gallery image ${index + 1}`,
        "width": image.width,
        "height": image.height,
        "encodingFormat": image.mime || "image/jpeg",
        "contentUrl": image.fullUrl || image.url,
        "name": image.title || image.alt || `Image ${index + 1}`,
      })),
    };

    if (organizationName) {
      structuredData.author = {
        "@type": "Organization",
        "name": organizationName,
      };
    }

    return JSON.stringify(structuredData);
  };

  // Gestionar autoplay para el slider
  const startAutoplay = () => {
    if (displayMode === "slider" && !autoplayInterval() && !isHovering()) {
      const interval = setInterval(() => {
        nextSlide();
      }, 5000);
      setAutoplayInterval(interval);
    }
  };

  const stopAutoplay = () => {
    if (autoplayInterval()) {
      clearInterval(autoplayInterval());
      setAutoplayInterval(null);
    }
  };

  // Navegación del slider
  const nextSlide = () => {
    setCurrentSlide((prev) => (prev + 1) % totalSlides());
  };

  const prevSlide = () => {
    setCurrentSlide((prev) => (prev - 1 + totalSlides()) % totalSlides());
  };

  const goToSlide = (index) => {
    setCurrentSlide(index);
  };

  // Manejar clic en imagen con historial del navegador
  const handleImageClick = (e, image, index) => {
    e.preventDefault();
    
    if (!enableLightbox) {
      // Si lightbox está deshabilitado, navegar a la URL de la imagen
      window.open(image.fullUrl || image.url, '_blank');
      return;
    }

    // Actualizar URL sin recargar la página (para SEO y navegación)
    const imageUrl = getImageUrl(image, index);
    history.pushState(
      { galleryId, imageIndex: index }, 
      `${title} - ${image.alt || `Image ${index + 1}`}`,
      imageUrl
    );

    openLightbox(image, index);
  };

  // Abrir lightbox
  const openLightbox = (image, index) => {
    if (!enableLightbox) return;

    setLightboxAnimation("fade-out");
    setLightboxVisible(true);
    setLightboxOpen(true);
    setLightboxLoading(true);

    document.body.style.overflow = "hidden";
    document.addEventListener("keydown", handleLightboxKeyboard);

    setTimeout(() => {
      setLightboxImage({
        ...image,
        index: index,
      });

      setTimeout(() => {
        setLightboxAnimation("fade-in");
        setLightboxLoading(false);
      }, 50);
    }, 50);
  };

  // Abrir lightbox desde el botón "Ver más"
  const openLightboxFromMore = (e) => {
    e.preventDefault();
    if (!enableLightbox || !hasMoreImages()) return;
    handleImageClick(e, images[maxVisibleImages], maxVisibleImages);
  };

  // Cerrar lightbox
  const closeLightbox = () => {
    setLightboxAnimation("fade-out");
    setLightboxTransitioning(true);

    // Restaurar URL original
    history.pushState(null, document.title, baseUrl);

    clearTimeout(lightboxTimerRef);
    lightboxTimerRef = setTimeout(() => {
      setLightboxVisible(false);
      setLightboxOpen(false);
      setLightboxImage(null);
      setLightboxTransitioning(false);

      document.body.style.overflow = "";
      document.removeEventListener("keydown", handleLightboxKeyboard);
    }, 300);
  };

  // Manejar eventos de teclado para el lightbox
  const handleLightboxKeyboard = (e) => {
    if (!lightboxOpen()) return;

    switch (e.key) {
      case "ArrowLeft":
        lightboxPrev();
        break;
      case "ArrowRight":
        lightboxNext();
        break;
      case "Escape":
        closeLightbox();
        break;
    }
  };

  // Manejar carga de imagen en lightbox
  const handleLightboxImageLoad = () => {
    setLightboxLoading(false);
  };

  // Navegar en el lightbox con indicador de carga
  const lightboxPrev = () => {
    if (lightboxTransitioning()) return;

    setLightboxLoading(true);
    setLightboxTransitioning(true);
    setLightboxAnimation("slide-right");

    clearTimeout(lightboxTimerRef);
    lightboxTimerRef = setTimeout(() => {
      const currentIndex = lightboxImage()?.index || 0;
      const newIndex = (currentIndex - 1 + images.length) % images.length;
      const newImage = images[newIndex];

      // Actualizar URL
      const imageUrl = getImageUrl(newImage, newIndex);
      history.replaceState(
        { galleryId, imageIndex: newIndex },
        `${title} - ${newImage.alt || `Image ${newIndex + 1}`}`,
        imageUrl
      );

      setLightboxImage({
        ...newImage,
        index: newIndex,
      });

      setTimeout(() => {
        setLightboxAnimation("fade-in");
        setLightboxTransitioning(false);
      }, 100);
    }, 200);
  };

  const lightboxNext = () => {
    if (lightboxTransitioning()) return;

    setLightboxLoading(true);
    setLightboxTransitioning(true);
    setLightboxAnimation("slide-left");

    clearTimeout(lightboxTimerRef);
    lightboxTimerRef = setTimeout(() => {
      const currentIndex = lightboxImage()?.index || 0;
      const newIndex = (currentIndex + 1) % images.length;
      const newImage = images[newIndex];

      // Actualizar URL
      const imageUrl = getImageUrl(newImage, newIndex);
      history.replaceState(
        { galleryId, imageIndex: newIndex },
        `${title} - ${newImage.alt || `Image ${newIndex + 1}`}`,
        imageUrl
      );

      setLightboxImage({
        ...newImage,
        index: newIndex,
      });

      setTimeout(() => {
        setLightboxAnimation("fade-in");
        setLightboxTransitioning(false);
      }, 100);
    }, 200);
  };

  // Cambiar imagen por miniatura con indicador de carga
  const goToLightboxImage = (targetIndex) => {
    if (lightboxTransitioning()) return;

    const currentIndex = lightboxImage()?.index || 0;
    if (targetIndex === currentIndex) return;

    setLightboxLoading(true);
    setLightboxTransitioning(true);

    if (targetIndex < currentIndex) {
      setLightboxAnimation("slide-right");
    } else {
      setLightboxAnimation("slide-left");
    }

    clearTimeout(lightboxTimerRef);
    lightboxTimerRef = setTimeout(() => {
      const newImage = images[targetIndex];
      
      // Actualizar URL
      const imageUrl = getImageUrl(newImage, targetIndex);
      history.replaceState(
        { galleryId, imageIndex: targetIndex },
        `${title} - ${newImage.alt || `Image ${targetIndex + 1}`}`,
        imageUrl
      );

      setLightboxImage({ ...newImage, index: targetIndex });

      setTimeout(() => {
        setLightboxAnimation("fade-in");
        setLightboxTransitioning(false);
      }, 100);
    }, 200);
  };

  // Generar srcset para imágenes responsivas
  const generateSrcSet = (image) => {
    if (!image.srcset) return "";
    return typeof image.srcset === 'string' ? image.srcset : 
      Object.entries(image.srcset).map(([size, url]) => `${url} ${size}`).join(', ');
  };

  // Funciones de ciclo de vida
  onMount(() => {
    if (displayMode === "slider") {
      const imagesPerSlide = 4;
      setTotalSlides(Math.ceil(visibleImages().length / imagesPerSlide));
      startAutoplay();
    }

    // Manejar navegación del historial
    const handlePopState = (e) => {
      if (e.state?.galleryId === galleryId) {
        if (typeof e.state.imageIndex === 'number') {
          openLightbox(images[e.state.imageIndex], e.state.imageIndex);
        }
      } else if (lightboxOpen()) {
        closeLightbox();
      }
    };

    window.addEventListener('popstate', handlePopState);

    return () => {
      document.removeEventListener("keydown", handleLightboxKeyboard);
      window.removeEventListener('popstate', handlePopState);
      clearTimeout(lightboxTimerRef);
    };
  });

  onCleanup(() => {
    document.removeEventListener("keydown", handleLightboxKeyboard);
    clearTimeout(lightboxTimerRef);
  });

  createEffect(() => {
    if (!isHovering() && displayMode === "slider") {
      startAutoplay();
    }
  });

  createEffect(() => {
    if (imagesLoaded() === visibleImages().length && displayMode === "masonry") {
      setIsLoaded(true);
    }
  });

  const handleImageLoad = () => {
    setImagesLoaded((prev) => prev + 1);
  };

  const getSlideStyle = () => {
    return {
      transform: `translateX(-${currentSlide() * 100}%)`,
      transition: `transform 0.8s cubic-bezier(0.4, 0, 0.2, 1)`,
    };
  };

  // Columnas responsivas optimizadas para agencia de viajes
  const getResponsiveColumns = () => {
    // Para galerías de tours, queremos mostrar bien las imágenes sin saturar
    const baseColumns = Math.min(columns, 4); // Máximo 4 columnas para mejor visualización
    return {
      small: Math.min(baseColumns, 1), // 1 columna en móvil para mejor visibilidad
      medium: Math.min(baseColumns, 2), // 2 columnas en tablet
      large: baseColumns, // Columnas completas en desktop
    };
  };

  const responsive = getResponsiveColumns();

  return (
    <>
      {/* Datos estructurados para SEO */}
      <script type="application/ld+json">
        {generateStructuredData()}
      </script>

      <section
        id={galleryId}
        class={`solid-gallery-component w-full py-8 md:py-12 relative ${
          fullWidth ? "vw-100" : ""
        }`}
        role="img"
        aria-label={`${title} - ${getTranslation("Image gallery with")} ${images.length} ${getTranslation("images")}`}
        style={{
          color: textColor,
          "background-image": `
            radial-gradient(circle at 10% 20%, rgba(138, 171, 141, 0.05) 0%, rgba(138, 171, 141, 0) 20%),
            radial-gradient(circle at 90% 80%, rgba(212, 178, 84, 0.07) 0%, rgba(212, 178, 84, 0) 20%)
          `,
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
        {/* Elementos decorativos compactos */}
        <div
          class="absolute -left-16 top-1/4 w-32 h-32 opacity-10 pointer-events-none rounded-full"
          style={{ "background-color": secondaryColor }}
          aria-hidden="true"
        ></div>
        <div
          class="absolute -right-16 bottom-1/4 w-24 h-24 opacity-10 pointer-events-none rounded-full"
          style={{ "background-color": accentColor }}
          aria-hidden="true"
        ></div>

        <div class="container mx-auto px-4 relative">
          {/* Encabezado semántico - solo mostrar si hay contenido */}
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
                  <h1 class="text-2xl md:text-3xl fancy-text font-medium mb-2">
                    {title}
                  </h1>
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

              {/* Mostrar total de imágenes si hay más de las visibles */}
              <Show when={hasMoreImages()}>
                <p class="text-sm text-gray-600 mt-2 font-light">
                  {getTranslation("Showing")} {visibleImages().length} {getTranslation("of")} {images.length} {getTranslation("images")}
                </p>
              </Show>
            </header>
          </Show>

          {/* Contenido de la galería con límite de imágenes */}
          <div class="gallery-container relative">
            {/* Modo Slider */}
            <Show when={displayMode === "slider"}>
              <div
                class="gallery-slider-container relative overflow-hidden"
                ref={sliderRef}
                role="region"
                aria-label={getTranslation("Image slider")}
              >
                <div class="slider-wrapper" style={getSlideStyle()}>
                  <For
                    each={Array(totalSlides())
                      .fill()
                      .map((_, i) => i)}
                  >
                    {(slideIndex) => {
                      const imagesPerSlide = 4;
                      const slideImages = visibleImages().slice(
                        slideIndex * imagesPerSlide,
                        slideIndex * imagesPerSlide + imagesPerSlide
                      );
                      
                      return (
                        <div class="slider-slide min-w-full">
                          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            <For each={slideImages}>
                              {(image, index) => {
                                const globalIndex = slideIndex * imagesPerSlide + index();
                                const isLastImage = globalIndex === visibleImages().length - 1 && hasMoreImages();
                                
                                return (
                                  <article
                                    class={`gallery-item effect-${hoverEffect} overflow-hidden rounded-lg shadow-lg transition-all duration-500 relative`}
                                    style={{
                                      transform:
                                        hoveredItem() === globalIndex
                                          ? "translateY(-3px)"
                                          : "translateY(0)",
                                      "box-shadow":
                                        hoveredItem() === globalIndex
                                          ? "0 10px 20px rgba(0,0,0,0.15)"
                                          : "0 3px 10px rgba(0,0,0,0.1)",
                                    }}
                                    onMouseEnter={() => setHoveredItem(globalIndex)}
                                    onMouseLeave={() => setHoveredItem(null)}
                                  >
                                    <div
                                      class="aspect-ratio-container relative"
                                      style={{ "aspect-ratio": "1/1" }}
                                    >
                                      {isLastImage ? (
                                        <button
                                          type="button"
                                          onClick={openLightboxFromMore}
                                          class="block w-full h-full focus:outline-none focus:ring-2 focus:ring-offset-2"
                                          style={{ "focus:ring-color": accentColor }}
                                          aria-label={`${getTranslation("View")} ${remainingCount()} ${getTranslation("more images")}`}
                                        >
                                          <img
                                            src={image.url}
                                            alt={image.alt || getTranslation("Gallery image")}
                                            class="w-full h-full object-cover transition-transform duration-700"
                                            style={{
                                              transform:
                                                hoveredItem() === globalIndex &&
                                                hoverEffect === "zoom"
                                                  ? "scale(1.05)"
                                                  : "scale(1)",
                                            }}
                                            loading="lazy"
                                            srcset={generateSrcSet(image)}
                                            sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                                            onLoad={handleImageLoad}
                                          />
                                          
                                          {/* Overlay para "Ver más" con efecto blur */}
                                          <div class="absolute inset-0 flex items-center justify-center transition-all duration-300"
                                               style={{
                                                 "backdrop-filter": "blur(8px)",
                                                 "background-color": "rgba(0, 0, 0, 0.4)",
                                               }}>
                                            <div class="text-center text-white relative z-10">
                                              <div class="text-2xl font-bold mb-1 drop-shadow-lg">
                                                +{remainingCount()}
                                              </div>
                                              <div class="text-sm font-light opacity-95 drop-shadow-md">
                                                {getTranslation("more images")}
                                              </div>
                                            </div>
                                          </div>
                                        </button>
                                      ) : (
                                        <a
                                          href={getImageUrl(image, globalIndex)}
                                          onClick={(e) => handleImageClick(e, image, globalIndex)}
                                          class="block w-full h-full focus:outline-none focus:ring-2 focus:ring-offset-2"
                                          style={{ "focus:ring-color": accentColor }}
                                          aria-label={`${getTranslation("View image")}: ${image.alt || getTranslation("Gallery image")} ${globalIndex + 1}`}
                                        >
                                          <img
                                            src={image.url}
                                            alt={image.alt || getTranslation("Gallery image")}
                                            class="w-full h-full object-cover transition-transform duration-700"
                                            style={{
                                              transform:
                                                hoveredItem() === globalIndex &&
                                                hoverEffect === "zoom"
                                                  ? "scale(1.05)"
                                                  : "scale(1)",
                                            }}
                                            loading="lazy"
                                            srcset={generateSrcSet(image)}
                                            sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                                            onLoad={handleImageLoad}
                                          />
                                        </a>
                                      )}
                                    </div>
                                  </article>
                                );
                              }}
                            </For>
                          </div>
                        </div>
                      );
                    }}
                  </For>
                </div>

                {/* Controles del slider */}
                <nav class="slider-controls mt-4 flex items-center justify-center gap-4" aria-label={getTranslation("Slider navigation")}>
                  <button
                    type="button"
                    class="slider-prev-btn w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center transition-all duration-300 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style={{ border: `1px solid ${accentColor}40`, "focus:ring-color": accentColor }}
                    onClick={() => {
                      stopAutoplay();
                      prevSlide();
                    }}
                    aria-label={getTranslation("Previous slide")}
                  >
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke={textColor}
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
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

                  <div class="slider-dots flex space-x-1.5" role="tablist" aria-label={getTranslation("Slide indicators")}>
                    <For
                      each={Array(totalSlides())
                        .fill()
                        .map((_, i) => i)}
                    >
                      {(dotIndex) => (
                        <button
                          type="button"
                          role="tab"
                          class="slider-dot w-2 h-2 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2"
                          style={{
                            "background-color":
                              currentSlide() === dotIndex
                                ? accentColor
                                : "#E9E2D8",
                            transform:
                              currentSlide() === dotIndex
                                ? "scale(1.2)"
                                : "scale(1)",
                            opacity: currentSlide() === dotIndex ? "1" : "0.6",
                            "focus:ring-color": accentColor,
                          }}
                          onClick={() => {
                            stopAutoplay();
                            goToSlide(dotIndex);
                          }}
                          aria-selected={currentSlide() === dotIndex}
                          aria-label={`${getTranslation("Go to slide")} ${dotIndex + 1}`}
                        />
                      )}
                    </For>
                  </div>

                  <button
                    type="button"
                    class="slider-next-btn w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center transition-all duration-300 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style={{ border: `1px solid ${accentColor}40`, "focus:ring-color": accentColor }}
                    onClick={() => {
                      stopAutoplay();
                      nextSlide();
                    }}
                    aria-label={getTranslation("Next slide")}
                  >
                    <svg
                      class="w-4 h-4"
                      fill="none"
                      stroke={textColor}
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
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
                </nav>
              </div>
            </Show>

            {/* Modo Masonry */}
            <Show when={displayMode === "masonry"}>
              <div
                class="gallery-masonry-container columns-1 sm:columns-2 lg:columns-3"
                style={{
                  "column-gap": `${spacing}px`,
                  opacity: isLoaded() ? "1" : "0.3",
                  transition: "opacity 0.5s ease",
                }}
              >

                <For each={visibleImages()}>
                  {(image, index) => {
                    const isLastImage = index() === visibleImages().length - 1 && hasMoreImages();
                    
                    return (
                      <article
                        class={`gallery-item effect-${hoverEffect} overflow-hidden break-inside-avoid rounded-lg shadow-lg transition-all duration-500 relative`}
                        style={{
                          "margin-bottom": `${spacing}px`,
                          transform:
                            hoveredItem() === index()
                              ? "translateY(-3px) scale(1.01)"
                              : "translateY(0) scale(1)",
                          "box-shadow":
                            hoveredItem() === index()
                              ? "0 10px 20px rgba(0,0,0,0.15)"
                              : "0 3px 10px rgba(0,0,0,0.1)",
                        }}
                        onMouseEnter={() => setHoveredItem(index())}
                        onMouseLeave={() => setHoveredItem(null)}
                      >
                        {isLastImage ? (
                          <button
                            type="button"
                            onClick={openLightboxFromMore}
                            class="block relative w-full focus:outline-none focus:ring-2 focus:ring-offset-2"
                            style={{ "focus:ring-color": accentColor }}
                            aria-label={`${getTranslation("View")} ${remainingCount()} ${getTranslation("more images")}`}
                          >
                            <img
                              src={image.url}
                              alt={image.alt || getTranslation("Gallery image")}
                              class="w-full h-auto transition-transform duration-700"
                              style={{
                                transform:
                                  hoverEffect === "zoom" && hoveredItem() === index()
                                    ? "scale(1.02)"
                                    : "scale(1)",
                              }}
                              loading="lazy"
                              srcset={generateSrcSet(image)}
                              sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                              onLoad={handleImageLoad}
                            />
                            
                            {/* Overlay para "Ver más" con efecto blur */}
                            <div class="absolute inset-0 flex items-center justify-center transition-all duration-300"
                                 style={{
                                   "backdrop-filter": "blur(8px)",
                                   "background-color": "rgba(0, 0, 0, 0.4)",
                                 }}>
                              <div class="text-center text-white relative z-10">
                                <div class="text-2xl font-bold mb-1 drop-shadow-lg">
                                  +{remainingCount()}
                                </div>
                                <div class="text-sm font-light opacity-95 drop-shadow-md">
                                  {getTranslation("more images")}
                                </div>
                              </div>
                            </div>
                          </button>
                        ) : (
                          <a
                            href={getImageUrl(image, index())}
                            onClick={(e) => handleImageClick(e, image, index())}
                            class="block relative focus:outline-none focus:ring-2 focus:ring-offset-2"
                            style={{ "focus:ring-color": accentColor }}
                            aria-label={`${getTranslation("View image")}: ${image.alt || getTranslation("Gallery image")} ${index() + 1}`}
                          >
                            <img
                              src={image.url}
                              alt={image.alt || getTranslation("Gallery image")}
                              class="w-full h-auto transition-transform duration-700"
                              style={{
                                transform:
                                  hoverEffect === "zoom" && hoveredItem() === index()
                                    ? "scale(1.02)"
                                    : "scale(1)",
                              }}
                              loading="lazy"
                              srcset={generateSrcSet(image)}
                              sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                              onLoad={handleImageLoad}
                            />
                          </a>
                        )}
                      </article>
                    );
                  }}
                </For>
              </div>
            </Show>

            {/* Modo Grid */}
            <Show when={displayMode === "grid"}>
              <div class="gallery-grid-container grid">
                <style jsx>{`
                  .gallery-grid-container {
                    grid-template-columns: repeat(${responsive.small}, minmax(0, 1fr));
                    gap: ${spacing}px;
                  }
                  @media (min-width: 640px) {
                    .gallery-grid-container {
                      grid-template-columns: repeat(
                        ${responsive.medium},
                        minmax(0, 1fr)
                      );
                    }
                  }
                  @media (min-width: 1024px) {
                    .gallery-grid-container {
                      grid-template-columns: repeat(
                        ${responsive.large},
                        minmax(0, 1fr)
                      );
                    }
                  }
                `}</style>

                <For each={visibleImages()}>
                  {(image, index) => {
                    const isLastImage = index() === visibleImages().length - 1 && hasMoreImages();
                    
                    return (
                      <article
                        class={`gallery-item effect-${hoverEffect} overflow-hidden relative rounded-lg shadow-sm transition-all duration-300 bg-white`}
                        style={{
                          "aspect-ratio": "4/3",
                          transform: hoveredItem() === index() ? "translateY(-2px)" : "translateY(0)",
                          "box-shadow": hoveredItem() === index() 
                            ? "0 8px 25px rgba(0,0,0,0.15)" 
                            : "0 2px 8px rgba(0,0,0,0.06)",
                        }}
                        onMouseEnter={() => setHoveredItem(index())}
                        onMouseLeave={() => setHoveredItem(null)}
                      >
                        {isLastImage ? (
                          <button
                            type="button"
                            onClick={openLightboxFromMore}
                            class="block w-full h-full relative focus:outline-none focus:ring-2 focus:ring-offset-2"
                            style={{ "focus:ring-color": accentColor }}
                            aria-label={`${getTranslation("View")} ${remainingCount()} ${getTranslation("more images")}`}
                          >
                            <img
                              src={image.url}
                              alt={image.alt || getTranslation("Gallery image")}
                              class="w-full h-full object-cover transition-transform duration-700"
                              style={{
                                transform:
                                  hoverEffect === "zoom" && hoveredItem() === index()
                                    ? "scale(1.02)"
                                    : "scale(1)",
                              }}
                              loading="lazy"
                              srcset={generateSrcSet(image)}
                              sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                              onLoad={handleImageLoad}
                            />
                            
                            {/* Overlay para "Ver más" con efecto blur */}
                            <div class="absolute inset-0 flex items-center justify-center transition-all duration-300"
                                 style={{
                                   "backdrop-filter": "blur(8px)",
                                   "background-color": "rgba(0, 0, 0, 0.4)",
                                 }}>
                              <div class="text-center text-white relative z-10">
                                <div class="text-2xl font-bold mb-1 drop-shadow-lg">
                                  +{remainingCount()}
                                </div>
                                <div class="text-sm font-light opacity-95 drop-shadow-md">
                                  {getTranslation("more images")}
                                </div>
                              </div>
                            </div>
                          </button>
                        ) : (
                          <a
                            href={getImageUrl(image, index())}
                            onClick={(e) => handleImageClick(e, image, index())}
                            class="block w-full h-full relative focus:outline-none focus:ring-2 focus:ring-offset-2"
                            style={{ "focus:ring-color": accentColor }}
                            aria-label={`${getTranslation("View image")}: ${image.alt || getTranslation("Gallery image")} ${index() + 1}`}
                          >
                            <img
                              src={image.url}
                              alt={image.alt || getTranslation("Gallery image")}
                              class="w-full h-full object-cover transition-transform duration-700"
                              style={{
                                transform:
                                  hoverEffect === "zoom" && hoveredItem() === index()
                                    ? "scale(1.02)"
                                    : "scale(1)",
                              }}
                              loading="lazy"
                              srcset={generateSrcSet(image)}
                              sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                              onLoad={handleImageLoad}
                            />
                          </a>
                        )}
                      </article>
                    );
                  }}
                </For>
              </div>
            </Show>
          </div>
        </div>

        {/* Lightbox mejorado - navega por TODAS las imágenes */}
        <Show when={lightboxVisible()}>
          <Portal>
            <div
              class={`lightbox fixed inset-0 z-[9999] flex items-center justify-center transition-opacity duration-300 ${
                lightboxOpen() ? "opacity-100" : "opacity-0"
              }`}
              style={{
                "background-color": "rgba(0,0,0,0.9)",
                "backdrop-filter": "blur(8px)",
              }}
              onClick={closeLightbox}
              role="dialog"
              aria-modal="true"
              aria-label={getTranslation("Image lightbox")}
            >
              <div
                class="lightbox-container max-w-7xl mx-auto px-4 py-4 md:py-8 w-full h-full flex flex-col justify-between"
                onClick={(e) => e.stopPropagation()}
              >
                {/* Área superior con controles */}
                <div class="flex items-center justify-between mb-2 md:mb-4">
                  <div class="flex-1 text-white">
                    <Show when={lightboxImage()?.caption}>
                      <h2 class="text-lg fancy-text italic text-white/80 hidden md:block">
                        {lightboxImage()?.caption}
                      </h2>
                    </Show>
                  </div>

                  <div class="flex items-center space-x-3">
                    <button
                      type="button"
                      class="lightbox-fullscreen-btn w-10 h-10 flex items-center justify-center text-white/70 hover:text-white transition-colors rounded-full hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/50"
                      onClick={() => {
                        window.open(
                          lightboxImage().fullUrl || lightboxImage().url,
                          "_blank"
                        );
                      }}
                      aria-label={getTranslation("View full image")}
                    >
                      <svg
                        class="w-5 h-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                      >
                        <path
                          d="M15 3h6v6M9 3H3v6M3 15v6h6M21 15v6h-6"
                          stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                    </button>

                    <button
                      type="button"
                      class="lightbox-close-btn w-10 h-10 flex items-center justify-center text-white/70 hover:text-white transition-colors rounded-full hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/50"
                      onClick={closeLightbox}
                      aria-label={getTranslation("Close lightbox")}
                    >
                      <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"
                        ></path>
                      </svg>
                    </button>
                  </div>
                </div>

                {/* Área central con imagen y navegación */}
                <div class="flex-1 flex items-center justify-center relative overflow-hidden">
                  <div
                    class={`relative max-h-full transition-all duration-300 ${
                      lightboxAnimation() === "fade-in"
                        ? "opacity-100 scale-[1]"
                        : lightboxAnimation() === "fade-out"
                        ? "opacity-0 scale-[0.95]"
                        : lightboxAnimation() === "slide-left"
                        ? "opacity-0 translate-x-full"
                        : lightboxAnimation() === "slide-right"
                        ? "opacity-0 -translate-x-full"
                        : ""
                    }`}
                  >
                    {/* Indicador de carga mejorado */}
                    <Show when={lightboxLoading()}>
                      <div class="absolute inset-0 flex items-center justify-center bg-black/50 backdrop-blur-sm rounded-lg z-20" aria-live="polite">
                        <div class="flex flex-col items-center space-y-4">
                          <div class="relative w-16 h-16">
                            <div class="absolute inset-0 border-4 border-white/20 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-transparent border-t-white rounded-full animate-spin"></div>
                            <div class="absolute inset-2 border-2 border-transparent border-r-white/60 rounded-full animate-spin animate-reverse"></div>
                          </div>
                          
                          <div class="text-white/80 text-sm font-light">
                            {getTranslation("Loading image")}...
                          </div>
                          
                          <div class="text-white/60 text-xs">
                            {((lightboxImage()?.index || 0) + 1)} / {images.length}
                          </div>
                        </div>
                      </div>
                    </Show>

                    <img
                      src={lightboxImage()?.fullUrl || lightboxImage()?.url}
                      alt={
                        lightboxImage()?.alt || getTranslation("Gallery image")
                      }
                      class="max-w-full max-h-[70vh] mx-auto object-contain rounded shadow-2xl"
                      style={{
                        filter: "drop-shadow(0 12px 24px rgba(0,0,0,0.5))",
                        opacity: lightboxLoading() ? "0.3" : "1",
                        transition: "opacity 0.3s ease",
                      }}
                      onLoad={handleLightboxImageLoad}
                    />
                  </div>

                  {/* Botones de navegación */}
                  <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-1 md:px-4 opacity-0 md:opacity-100 group-hover:opacity-100 transition-opacity">
                    <button
                      type="button"
                      class="lightbox-nav-btn w-10 h-10 md:w-12 md:h-12 bg-black/30 hover:bg-black/60 text-white/70 hover:text-white rounded-full flex items-center justify-center backdrop-blur-sm transition-all transform hover:scale-110 disabled:opacity-30 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-white/50"
                      onClick={(e) => {
                        e.stopPropagation();
                        lightboxPrev();
                      }}
                      disabled={lightboxTransitioning()}
                      aria-label={getTranslation("Previous image")}
                    >
                      <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
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
                      class="lightbox-nav-btn w-10 h-10 md:w-12 md:h-12 bg-black/30 hover:bg-black/60 text-white/70 hover:text-white rounded-full flex items-center justify-center backdrop-blur-sm transition-all transform hover:scale-110 disabled:opacity-30 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-white/50"
                      onClick={(e) => {
                        e.stopPropagation();
                        lightboxNext();
                      }}
                      disabled={lightboxTransitioning()}
                      aria-label={getTranslation("Next image")}
                    >
                      <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
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
                  </div>
                </div>

                {/* Área inferior con leyenda y miniaturas */}
                <div class="mt-2 md:mt-4">
                  <Show when={lightboxImage()?.caption}>
                    <div class="text-center mb-3 md:hidden">
                      <p class="fancy-text italic text-white/80 text-sm">
                        {lightboxImage()?.caption}
                      </p>
                    </div>
                  </Show>

                  <div class="flex items-center justify-center space-x-6">
                    <div class="text-white/70 flex items-center space-x-3">
                      <div class="text-lg fancy-text">
                        {(lightboxImage()?.index || 0) + 1} / {images.length}
                      </div>

                      <div class="hidden md:block w-24 h-1 bg-white/20 rounded-full overflow-hidden">
                        <div
                          class="h-full bg-white/80 rounded-full transition-all duration-300"
                          style={{
                            width: `${
                              (((lightboxImage()?.index || 0) + 1) /
                                images.length) *
                              100
                            }%`,
                          }}
                          role="progressbar"
                          aria-valuenow={(lightboxImage()?.index || 0) + 1}
                          aria-valuemin="1"
                          aria-valuemax={images.length}
                          aria-label={`${getTranslation("Image")} ${(lightboxImage()?.index || 0) + 1} ${getTranslation("of")} ${images.length}`}
                        ></div>
                      </div>
                    </div>
                  </div>

                  {/* Miniaturas compactas - de TODAS las imágenes */}
                  <div class="mt-4 hidden md:block">
                    <div class="flex space-x-1.5 justify-center pb-2 overflow-x-auto max-w-full" role="group" aria-label={getTranslation("Image thumbnails")}>
                      <For each={images}>
                        {(thumb, i) => (
                          <button
                            type="button"
                            class={`h-12 w-12 rounded overflow-hidden transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-white/50 ${
                              i() === lightboxImage()?.index
                                ? "ring-2 ring-offset-2 ring-white opacity-100 scale-110"
                                : "opacity-60 hover:opacity-100"
                            }`}
                            onClick={() => goToLightboxImage(i())}
                            disabled={lightboxTransitioning()}
                            aria-label={`${getTranslation("Go to image")} ${i() + 1}: ${thumb.alt || getTranslation("Gallery image")}`}
                          >
                            <img
                              src={thumb.url}
                              alt={thumb.alt || `${getTranslation("Thumbnail")} ${i() + 1}`}
                              class="h-full w-full object-cover"
                            />
                          </button>
                        )}
                      </For>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </Portal>
        </Show>

        {/* Decoración compacta */}
        <div class="absolute bottom-0 right-0 w-32 h-32 opacity-5 pointer-events-none transform translate-x-12 translate-y-12" aria-hidden="true">
          <svg
            viewBox="0 0 200 200"
            xmlns="http://www.w3.org/2000/svg"
            fill={accentColor}
          >
            <path
              d="M31.9,-31.9C40.4,-21.4,45.8,-8.7,45.1,3.8C44.5,16.4,38.1,28.8,27.6,38.3C17.2,47.8,2.9,54.2,-13.4,53.6C-29.7,53,-48.1,45.3,-55.8,31.1C-63.5,16.9,-60.6,-3.8,-52.1,-19.9C-43.5,-36,-29.3,-47.6,-14.8,-48.8C-0.3,-50,14.1,-40.9,28.4,-32.1Z"
              transform="translate(100 100)"
            />
          </svg>
        </div>
      </section>
    </>
  );
};

export default SolidGallery;