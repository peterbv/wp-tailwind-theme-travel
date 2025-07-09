// src/public/js/components/google-reviews-module.js
import solidCore, { registerComponent } from "../../solid-core";
import SolidGoogleReviews from "./solid/SolidGoogleReviews";

// Registrar el componente de reseñas de Google
registerComponent("google-reviews", SolidGoogleReviews);

/**
 * Inicializa los componentes de reseñas de Google en la página
 */
function initGoogleReviews() {
  // Buscar contenedores de reseñas
  const containers = document.querySelectorAll(
    ".solid-google-reviews-container"
  );

  if (containers.length === 0) {
    return; // No hay contenedores, salir
  }

  // Para cada contenedor, inicializar el componente
  containers.forEach((container) => {
    try {
      // Verificar si ya está inicializado
      if (container.dataset.solidInitialized === "true") return;

      // Extraer datos del contenedor
      let reviews = [];
      let placeInfo = {};
      let isDynamic = container.dataset.dynamic === "true";

      try {
        // Intentar parsear JSON de reseñas si está disponible
        if (container.dataset.reviews) {
          reviews = JSON.parse(container.dataset.reviews);
        }

        // Intentar parsear JSON de información del lugar
        if (container.dataset.placeInfo) {
          placeInfo = JSON.parse(container.dataset.placeInfo);
        }
      } catch (error) {
        console.warn("Error al parsear datos de reseñas:", error);
      }

      // Configurar props para el componente
      const props = {
        title: container.dataset.title || "What Our Clients Say",
        subtitle: container.dataset.subtitle || "Google Reviews",
        description:
          container.dataset.description ||
          "See what our customers are saying about us",
        reviews: reviews,
        placeInfo: placeInfo,
        displayName: container.dataset.displayName !== "false",
        displayAvatar: container.dataset.displayAvatar !== "false",
        displayRating: container.dataset.displayRating !== "false",
        displayDate: container.dataset.displayDate === "true",
        displayRole: container.dataset.displayRole !== "false",
        clientRole: container.dataset.clientRole || "Customer",
        autoplay: container.dataset.autoplay !== "false",
        autoplaySpeed: parseInt(container.dataset.autoplaySpeed) || 5000,
        backgroundColor: container.dataset.backgroundColor || "#FFFFFF",
        textColor: container.dataset.textColor || "#424242",
        accentColor: container.dataset.accentColor || "#D4B254",
        carouselType: container.dataset.effect || "slide",
        ajaxUrl: container.dataset.ajaxUrl || "",
        nonce: container.dataset.nonce || "",
        placeId: container.dataset.placeId || "",
        apiKey: container.dataset.apiKey || "",
        reviewCount: parseInt(container.dataset.reviewCount) || 5,
        minRating: parseInt(container.dataset.minRating) || 4,
        isDynamic: isDynamic,
      };

      // Renderizar el componente de Solid.js
      const dispose = solidCore.renderComponent(
        "google-reviews",
        container,
        props
      );

      // Guardar referencia al método dispose para posible limpieza
      container._solidDispose = dispose;

      console.log(
        "Componente de reseñas de Google con Solid.js cargado correctamente"
      );
    } catch (error) {
      console.error(
        "Error al inicializar componente de reseñas con Solid.js:",
        error
      );

      // Mostrar mensaje de error en el contenedor
      container.innerHTML = `
        <div class="p-4 bg-red-100 text-red-800 rounded-md">
          <p>Error al cargar el componente de reseñas: ${error.message}</p>
        </div>
      `;
    }
  });
}

// Inicializar cuando el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initGoogleReviews);
} else {
  initGoogleReviews();
}

// Observer para detectar elementos que se vuelven visibles
// (Importante para las animaciones y cargas diferidas)
if ("IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const container = entry.target;
          if (
            container.classList.contains("solid-google-reviews-container") &&
            container.dataset.intersectOnce === "true" &&
            container.dataset.solidInitialized !== "true"
          ) {
            // Inicializar el componente cuando se hace visible
            initGoogleReviews();
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
    .querySelectorAll(".solid-google-reviews-container[data-intersect-once]")
    .forEach((container) => {
      observer.observe(container);
    });
}

// Exportar funciones para posible uso externo
export { initGoogleReviews };
