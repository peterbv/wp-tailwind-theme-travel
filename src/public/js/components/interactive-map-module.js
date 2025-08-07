// src/public/js/components/interactive-map-module.js
import solidCore, { registerComponent, __ } from "../../solid-core";
import SolidInteractiveMap from "./solid/SolidInteractiveMap";

// Registrar el componente de mapa interactivo
registerComponent("interactive-map", SolidInteractiveMap);

/**
 * Inicializa los componentes de mapa interactivo en la página
 */
function initInteractiveMap() {
  // Buscar contenedores del mapa
  const containers = document.querySelectorAll(
    ".solid-interactive-map-container"
  );

  if (containers.length === 0) {
    return; // No hay contenedores, salir
  }

  // Para cada contenedor, inicializar el componente
  containers.forEach((container) => {
    try {
      // Verificar si ya está inicializado para evitar duplicación
      if (container.dataset.solidInitialized === "true") return;

      // Marcar como en proceso de inicialización para evitar carreras
      container.dataset.solidInitializing = "true";
      
      // Extraer datos del contenedor con validación extra
      let pointsOfInterest = [];
      let mainLocation = {
        lat: parseFloat(container.dataset.latitude) || -13.53168,
        lng: parseFloat(container.dataset.longitude) || -71.96741,
        title: container.dataset.markerTitle || "Mystical Terra Spa",
        address: container.dataset.address || "Calle Principal 123, Cusco, Perú",
        description: container.dataset.markerDescription || "Our main spa location",
        contactInfo: container.dataset.phone 
          ? `${container.dataset.phone}${container.dataset.email ? ' | ' + container.dataset.email : ''}`
          : ""
      };

      try {
        // Intentar parsear JSON de puntos de interés si está disponible
        if (container.dataset.pointsOfInterest && container.dataset.pointsOfInterest !== "[]") {
          const parsed = JSON.parse(container.dataset.pointsOfInterest);
          if (Array.isArray(parsed)) {
            // Validar la estructura de cada POI
            pointsOfInterest = parsed.filter(poi => {
              return poi && typeof poi === 'object' && 
                     (typeof poi.lat === 'number' || typeof poi.latitude === 'number') && 
                     (typeof poi.lng === 'number' || typeof poi.longitude === 'number');
            }).map(poi => {
              // Normalizar formato - permitir tanto lat/lng como latitude/longitude
              return {
                lat: poi.lat || poi.latitude,
                lng: poi.lng || poi.longitude,
                title: poi.title || "",
                description: poi.description || "",
                image: poi.image || "",
                category: poi.category || "",
                website: poi.website || ""
              };
            });
          }
          console.log("POIs procesados correctamente:", pointsOfInterest);
        } else {
          console.log("No se encontraron puntos de interés o formato incorrecto");
        }
        
        // Intentar parsear JSON de ubicación principal si está disponible
        if (container.dataset.mainLocation) {
          try {
            const parsed = JSON.parse(container.dataset.mainLocation);
            if (parsed && typeof parsed === 'object') {
              // Normalizar el formato
              mainLocation = {
                lat: parsed.lat || parsed.latitude || mainLocation.lat,
                lng: parsed.lng || parsed.longitude || mainLocation.lng,
                title: parsed.title || mainLocation.title,
                address: parsed.address || mainLocation.address,
                description: parsed.description || mainLocation.description,
                contactInfo: parsed.contactInfo || mainLocation.contactInfo
              };
            }
          } catch (e) {
            console.warn("Error al parsear mainLocation, usando valores fallback", e);
          }
        }
        
        
      } catch (error) {
        console.warn("Error al parsear datos JSON:", error);
        // Mostrar mensaje de depuración con los datos que se intentaron analizar
      }

      // Configurar props para el componente con validación de tipos
      const props = {
        title: container.dataset.title || "Find Us",
        subtitle: container.dataset.subtitle || "Our Location",
        description: container.dataset.description || "",
        mainLocation: mainLocation,
        pointsOfInterest: pointsOfInterest,
        zoom: parseInt(container.dataset.zoom) || 14,
        mapHeight: parseInt(container.dataset.mapHeight) || 500,
        accentColor: container.dataset.accentColor || "#D4B254",
        secondaryColor: container.dataset.secondaryColor || "#8BAB8D",
        backgroundColor: container.dataset.backgroundColor || "#F9F5F2",
        textColor: container.dataset.textColor || "#5D534F",
        showDirectionsLink: container.dataset.showDirections !== "false",
        showPointsOfInterest: container.dataset.showPointsOfInterest !== "false",
        customMapStyle: container.dataset.customMapStyle || container.dataset.mapStyle || "default",
        enableFullscreen: container.dataset.enableFullscreen !== "false",
        enableZoomControls: container.dataset.enableZoomControls !== "false",
        enableClustering: container.dataset.enableClustering !== "false",
        mapProvider: container.dataset.mapProvider || "osm",
        apiKey: container.dataset.apiKey || "",
        showStreetview: container.dataset.showStreetview !== "false",
        address: container.dataset.address || "",
        phone: container.dataset.phone || "",
        email: container.dataset.email || "",
        bookingUrl: container.dataset.bookingUrl || "#booking",
        mapContext: container.dataset.mapContext || "default",
      };

      console.log("Inicializando mapa con propiedades:", props);

      // Renderizar el componente de Solid.js con manejo de errores mejorado
      try {
        const dispose = solidCore.renderComponent(
          "interactive-map",
          container,
          props
        );

        // Guardar referencia al método dispose para posible limpieza
        container._solidDispose = dispose;

        // Marcar como inicializado
        container.dataset.solidInitialized = "true";
        container.dataset.solidInitializing = "false";

        console.log(
          "Componente de mapa interactivo con Solid.js cargado correctamente"
        );
      } catch (renderError) {
        console.error(
          "Error al renderizar componente de mapa interactivo:",
          renderError
        );
        container.dataset.solidInitializing = "false";
        
        // Mostrar mensaje de error detallado en el contenedor
        container.innerHTML = `
          <div class="p-4 bg-red-100 text-red-800 rounded-md">
            <p>Error al renderizar el componente de mapa: ${renderError.message}</p>
            <pre class="mt-2 text-xs overflow-auto max-h-40">${renderError.stack}</pre>
          </div>
        `;
      }
    } catch (error) {
      console.error(
        "Error al inicializar componente de mapa interactivo con Solid.js:",
        error
      );

      // Mostrar mensaje de error en el contenedor
      container.innerHTML = `
        <div class="p-4 bg-red-100 text-red-800 rounded-md">
          <p>Error al cargar el componente de mapa interactivo: ${error.message}</p>
          <button class="mt-2 px-3 py-1 bg-white text-red-800 rounded border border-red-300 text-sm" 
                  onclick="initInteractiveMap()">
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
  document.addEventListener("DOMContentLoaded", initInteractiveMap);
} else {
  initInteractiveMap();
}

// Observer para detectar elementos que se vuelven visibles con retraso para mejor rendimiento
if ("IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const container = entry.target;
          if (
            container.classList.contains("solid-interactive-map-container") &&
            container.dataset.intersectOnce === "true" &&
            container.dataset.solidInitialized !== "true" &&
            container.dataset.solidInitializing !== "true"
          ) {
            // Inicializar el componente cuando se hace visible
            console.log("Mapa visible en viewport, inicializando...");
            setTimeout(initInteractiveMap, 100); // Ligero retraso para mejorar rendimiento
            container.dataset.intersectOnce = "false"; // Prevenir múltiples inicializaciones
          }
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1, rootMargin: "100px" } // Detectar antes de que esté completamente visible
  );

  // Observar todos los contenedores con data-intersect-once
  document
    .querySelectorAll(".solid-interactive-map-container[data-intersect-once='true']")
    .forEach((container) => {
      observer.observe(container);
    });
}

// Exponer función para posible uso externo y fácil depuración
window.initInteractiveMap = initInteractiveMap;

// Exportar funciones para posible uso externo
export { initInteractiveMap };