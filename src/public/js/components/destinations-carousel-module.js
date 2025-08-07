// src/public/js/components/destinations-carousel-module.js
import solidCore, { registerComponent } from "../solid-core";
import DestinationsCarousel from "./solid/DestinationsCarousel";

// Registrar el componente de destinations carousel
registerComponent("destinations-carousel", DestinationsCarousel);

/**
 * Inicializa los componentes de destinations carousel en la página
 */
function initDestinationsCarousel() {
  // Buscar contenedores del carousel de destinos
  const containers = document.querySelectorAll(
    ".solid-destinations-carousel-container"
  );

  if (containers.length === 0) {
    return; // No hay contenedores, salir
  }

  // Para cada contenedor, inicializar el componente
  containers.forEach((container) => {
    try {
      // Verificar si ya está inicializado para evitar duplicación
      if (container.dataset.solidInitialized === "true") return;

      // Marcar como en proceso de inicialización
      container.dataset.solidInitializing = "true";

      // Extraer datos del contenedor
      let destinations = [];
      
      try {
        if (container.dataset.destinations && container.dataset.destinations !== "[]") {
          const parsed = JSON.parse(container.dataset.destinations);
          if (Array.isArray(parsed)) {
            destinations = parsed;
          }
        }
      } catch (error) {
        console.warn("Error al parsear datos de destinos:", error);
      }

      // Configurar props para el componente
      const props = {
        title: container.dataset.title || "",
        subtitle: container.dataset.subtitle || "",
        description: container.dataset.description || "",
        destinations: destinations,
        autoplaySpeed: parseInt(container.dataset.autoplaySpeed) || 3000,
        showDots: container.dataset.showDots !== "false",
        showArrows: container.dataset.showArrows !== "false", 
        pauseOnHover: container.dataset.pauseOnHover !== "false",
        slidesToShow: parseInt(container.dataset.slidesToShow) || 3,
        backgroundColor: container.dataset.backgroundColor || "#F8FAFC",
        textColor: container.dataset.textColor || "#1F2937",
        accentColor: container.dataset.accentColor || "#DC2626",
        secondaryColor: container.dataset.secondaryColor || "#059669",
        fullWidth: container.dataset.fullWidth === "true",
        animationDirection: container.dataset.animationDirection || "left",
        carouselId: container.dataset.carouselId || "destinations-carousel",
      };

      console.log("Inicializando carousel de destinos con propiedades:", props);

      // Renderizar el componente de Solid.js
      const dispose = solidCore.renderComponent(
        "destinations-carousel",
        container,
        props
      );

      // Guardar referencia al método dispose para posible limpieza
      container._solidDispose = dispose;

      // Marcar como inicializado
      container.dataset.solidInitialized = "true";
      container.dataset.solidInitializing = "false";

      console.log(
        "Componente de destinations carousel con Solid.js cargado correctamente"
      );
    } catch (error) {
      console.error(
        "Error al inicializar componente de destinations carousel con Solid.js:",
        error
      );

      // Mostrar mensaje de error en el contenedor
      container.innerHTML = `
        <div class="p-4 bg-red-100 text-red-800 rounded-md">
          <p>Error al cargar el componente de carousel de destinos: ${error.message}</p>
          <button class="mt-2 px-3 py-1 bg-white text-red-800 rounded border border-red-300 text-sm" 
                  onclick="initDestinationsCarousel()">
            Reintentar
          </button>
        </div>
      `;
      
      // Limpiar marca de inicialización para permitir reintentos
      container.dataset.solidInitializing = "false";
    }
  });
}

// Inicializar cuando el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initDestinationsCarousel);
} else {
  initDestinationsCarousel();
}

// Observer para detectar elementos que se vuelven visibles
if ("IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const container = entry.target;
          if (
            container.classList.contains("solid-destinations-carousel-container") &&
            container.dataset.intersectOnce === "true" &&
            container.dataset.solidInitialized !== "true" &&
            container.dataset.solidInitializing !== "true"
          ) {
            // Inicializar el componente cuando se hace visible
            console.log("Destinations carousel visible en viewport, inicializando...");
            setTimeout(initDestinationsCarousel, 100);
            container.dataset.intersectOnce = "false";
          }
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1, rootMargin: "100px" }
  );

  // Observar todos los contenedores con data-intersect-once
  document
    .querySelectorAll(".solid-destinations-carousel-container[data-intersect-once='true']")
    .forEach((container) => {
      observer.observe(container);
    });
}

// Exponer función para posible uso externo y depuración
window.initDestinationsCarousel = initDestinationsCarousel;