// src/public/js/components/tours-carousel-module.js
import solidCore, { registerComponent } from "../solid-core";
import ToursCarousel from "./solid/ToursCarousel";

// Registrar el componente de tours carousel
registerComponent("tours-carousel", ToursCarousel);

/**
 * Inicializa los carousels de tours en la página
 */
function initToursCarousels() {
  // Buscar contenedores de tours carousel
  const containers = document.querySelectorAll(".solid-tours-carousel-container");

  if (containers.length === 0) {
    return; // No hay contenedores, salir
  }

  // Para cada contenedor, inicializar el componente
  containers.forEach((container) => {
    try {
      // Verificar si ya está inicializado
      if (container.dataset.solidInitialized === "true") return;

      // Extraer datos del contenedor
      let tours = [];

      try {
        // Intentar parsear JSON de tours
        if (container.dataset.tours) {
          tours = JSON.parse(container.dataset.tours);
        }
      } catch (error) {
        console.warn("Error al parsear datos de tours:", error);
      }

      // Configurar props para el componente
      const props = {
        title: container.dataset.title || "",
        subtitle: container.dataset.subtitle || "",
        description: container.dataset.description || "",
        tours: tours,
        autoplaySpeed: parseInt(container.dataset.autoplaySpeed || 3000, 10),
        slidesToShow: parseInt(container.dataset.slidesToShow || 3, 10),
        showDots: container.dataset.showDots === "true",
        showArrows: container.dataset.showArrows === "true",
        pauseOnHover: container.dataset.pauseOnHover === "true",
        infinite: container.dataset.infinite === "true",
        animationDirection: container.dataset.animationDirection || "left",
        backgroundColor: container.dataset.backgroundColor || "#F8FAFC",
        textColor: container.dataset.textColor || "#1F2937",
        accentColor: container.dataset.accentColor || "#DC2626",
        secondaryColor: container.dataset.secondaryColor || "#059669",
        fullWidth: container.dataset.fullWidth === "true",
      };

      // Renderizar el componente de Solid.js
      const dispose = solidCore.renderComponent("tours-carousel", container, props);

      // Guardar referencia al método dispose para posible limpieza
      container._solidDispose = dispose;
      
      // Marcar como inicializado
      container.dataset.solidInitialized = "true";

      console.log("Componente de Tours Carousel con Solid.js cargado correctamente");
    } catch (error) {
      console.error(
        "Error al inicializar componente de Tours Carousel con Solid.js:",
        error
      );

      // Mostrar mensaje de error en el contenedor
      container.innerHTML = `
        <div class="p-4 bg-red-100 text-red-800 rounded-md">
          <p>Error al cargar el componente de Tours Carousel: ${error.message}</p>
        </div>
      `;
    }
  });
}

// Inicializar cuando el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initToursCarousels);
} else {
  initToursCarousels();
}

// Observer para detectar elementos que se vuelven visibles
if ("IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const container = entry.target;
          if (
            container.classList.contains("solid-tours-carousel-container") &&
            container.dataset.intersectOnce === "true" &&
            container.dataset.solidInitialized !== "true"
          ) {
            // Inicializar el componente cuando se hace visible
            initToursCarousels();
            // Marcar como intersectado (para CSS)
            container.dataset.intersectOnce = "true";
          }
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.25 }
  );

  // Observar todos los contenedores con data-intersect-once
  document
    .querySelectorAll(".solid-tours-carousel-container[data-intersect-once]")
    .forEach((container) => {
      observer.observe(container);
    });
}

// Exportar funciones para posible uso externo
export { initToursCarousels };