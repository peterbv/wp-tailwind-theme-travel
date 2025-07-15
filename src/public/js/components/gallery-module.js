// src/public/js/components/gallery-module.js
import solidCore, { registerComponent } from "../../solid-core";
import SolidGallery from "./solid/SolidGallery";

// Registrar el componente de galería
registerComponent("gallery", SolidGallery);

/**
 * Inicializa las galerías en la página
 */
function initGalleries() {
  // Buscar contenedores de galería
  const containers = document.querySelectorAll(".solid-gallery-container");

  if (containers.length === 0) {
    return; // No hay contenedores, salir
  }

  // Para cada contenedor, inicializar el componente
  containers.forEach((container) => {
    try {
      // Verificar si ya está inicializado
      if (container.dataset.solidInitialized === "true") return;

      // Extraer datos del contenedor
      let images = [];

      try {
        // Intentar parsear JSON de imágenes
        if (container.dataset.images) {
          images = JSON.parse(container.dataset.images);
        }
      } catch (error) {
        console.warn("Error al parsear datos de imágenes:", error);
      }

      // Configurar props para el componente
      const props = {
        title: container.dataset.title || "",
        subtitle: container.dataset.subtitle || "",
        description: container.dataset.description || "",
        images: images,
        columns: parseInt(container.dataset.columns || 3, 10),
        displayMode: container.dataset.displayMode || "grid", // 'grid', 'masonry', 'slider'
        hoverEffect: container.dataset.hoverEffect || "zoom", // 'zoom', 'fade', 'slide', 'none'
        backgroundColor: container.dataset.backgroundColor || "#F9F5F2",
        textColor: container.dataset.textColor || "#5D534F",
        accentColor: container.dataset.accentColor || "#D4B254",
        secondaryColor: container.dataset.secondaryColor || "#8BAB8D",
        fullWidth: container.dataset.fullWidth === "true",
        enableLightbox: container.dataset.enableLightbox !== "false",
        spacing: parseInt(container.dataset.spacing || 16, 10),
      };

      // Renderizar el componente de Solid.js
      const dispose = solidCore.renderComponent("gallery", container, props);

      // Guardar referencia al método dispose para posible limpieza
      container._solidDispose = dispose;

      console.log("Componente de galería con Solid.js cargado correctamente");
    } catch (error) {
      console.error(
        "Error al inicializar componente de galería con Solid.js:",
        error
      );

      // Mostrar mensaje de error en el contenedor
      container.innerHTML = `
        <div class="p-4 bg-red-100 text-red-800 rounded-md">
          <p>Error al cargar el componente de galería: ${error.message}</p>
        </div>
      `;
    }
  });
}

// Inicializar cuando el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initGalleries);
} else {
  initGalleries();
}

// Observer para detectar elementos que se vuelven visibles
if ("IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const container = entry.target;
          if (
            container.classList.contains("solid-gallery-container") &&
            container.dataset.intersectOnce === "true" &&
            container.dataset.solidInitialized !== "true"
          ) {
            // Inicializar el componente cuando se hace visible
            initGalleries();
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
    .querySelectorAll(".solid-gallery-container[data-intersect-once]")
    .forEach((container) => {
      observer.observe(container);
    });
}

// Exportar funciones para posible uso externo
export { initGalleries };
