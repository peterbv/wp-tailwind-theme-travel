// src/public/js/components/faq-module.js
import solidCore, { registerComponent } from "../../solid-core";
import SolidFAQ from "./solid/SolidFAQ";

// Registrar el componente de FAQ
registerComponent("faq", SolidFAQ);

/**
 * Inicializa los componentes de FAQ en la página
 */
function initFAQs() {
  // Buscar contenedores de FAQ
  const containers = document.querySelectorAll(".solid-faq-container");

  if (containers.length === 0) {
    return; // No hay contenedores, salir
  }

  // Para cada contenedor, inicializar el componente
  containers.forEach((container) => {
    try {
      // Verificar si ya está inicializado
      if (container.dataset.solidInitialized === "true") return;

      // Extraer datos del contenedor
      let faqs = [];

      try {
        // Intentar parsear JSON de FAQs si está disponible
        if (container.dataset.faqs) {
          faqs = JSON.parse(container.dataset.faqs);
        }
      } catch (error) {
        console.warn("Error al parsear datos de FAQs:", error);
      }
      console.log(container.dataset);
      // Configurar props para el componente
      const props = {
        title: container.dataset.title || "Preguntas Frecuentes",
        subtitle: container.dataset.subtitle || "Resolvemos tus dudas",
        faqs: faqs,
        backgroundColor: container.dataset.backgroundColor || "#F7EDE2",
        textColor: container.dataset.textColor || "#424242",
        accentColor: container.dataset.accentColor || "#D4B254",
        secondaryColor: container.dataset.secondaryColor || "#8BAB8D",
        layout: container.dataset.layout || "full",
        contactText: container.dataset.contactText || "¿Tienes más preguntas?",
        contactUrl: container.dataset.contactUrl || "#contact",
        showContactButton: container.dataset.showContactButton !== "false",
        openFirst: container.dataset.openFirst === "true",
        singleOpen: container.dataset.singleOpen === "true",
        animateEntrance: container.dataset.animateEntrance !== "false",
        showTopWave: container.dataset.showTopWave === "true",
        showBottomWave: container.dataset.showBottomWave === "true",
      };

      // Renderizar el componente de Solid.js
      const dispose = solidCore.renderComponent("faq", container, props);

      // Guardar referencia al método dispose para posible limpieza
      container._solidDispose = dispose;

      console.log("Componente de FAQ con Solid.js cargado correctamente");
    } catch (error) {
      console.error(
        "Error al inicializar componente de FAQ con Solid.js:",
        error
      );

      // Mostrar mensaje de error en el contenedor
      container.innerHTML = `
        <div class="p-4 bg-red-100 text-red-800 rounded-md">
          <p>Error al cargar el componente de FAQ: ${error.message}</p>
        </div>
      `;
    }
  });
}

// Inicializar cuando el DOM esté listo
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initFAQs);
} else {
  initFAQs();
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
            container.classList.contains("solid-faq-container") &&
            container.dataset.intersectOnce === "true" &&
            container.dataset.solidInitialized !== "true"
          ) {
            // Inicializar el componente cuando se hace visible
            initFAQs();
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
    .querySelectorAll(".solid-faq-container[data-intersect-once]")
    .forEach((container) => {
      observer.observe(container);
    });
}

// Exportar funciones para posible uso externo
export { initFAQs };
